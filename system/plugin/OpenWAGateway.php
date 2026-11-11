<?php

/**
 * OpenWA Gateway Plugin for SpeedRadius (PHPNuxBill)
 * Targets: OpenWA — rmyndharis/OpenWA
 *          https://github.com/rmyndharis/OpenWA
 *
 * API notes (OpenWA v1.x)
 *   - Auth is an API key sent as the X-API-Key header on every call.
 *   - API and dashboard share one port (default 2785). Base URL has no /api
 *     prefix in config — this plugin appends it.
 *   - Sessions are first-class objects: POST /api/sessions {name} creates one,
 *     POST /api/sessions/{id}/start opens the WhatsApp connection, and
 *     GET /api/sessions/{id} carries the live status.
 *   - GET /api/sessions/{id}/qr returns {qrCode, status} where qrCode is a
 *     ready-to-embed data:image/png;base64 URL (only while status is qr_ready).
 *   - POST /api/sessions/{id}/messages/send-text takes
 *     {chatId, text} with chatId in the form 2547XXXXXXXX@c.us.
 *   - Errors are {statusCode, message, error, code}.
 *
 * Credentials never leave this server: the QR is rendered from the data URL
 * returned by the API, so no key is ever embedded in an <img src>.
 */

if (!defined('OPENWA_PLUGIN_VERSION')) {
    define('OPENWA_PLUGIN_VERSION', '1.0.0');
}

// A pairing code is only usable briefly; WhatsApp keeps one live request.
if (!defined('OPENWA_PAIR_TTL')) {
    define('OPENWA_PAIR_TTL', 60);
}

register_menu("OpenWA", true, "openwa", 'COMMUNICATION', 'fa fa-whatsapp', '', '', ['Admin', 'SuperAdmin']);
register_menu("OpenWA Logs", true, "openwa_logs", 'COMMUNICATION', 'fa fa-list-alt', '', '', ['Admin', 'SuperAdmin']);
register_menu("OpenWA API", false, "openwa_api", '', '', '', '', []);

register_hook('send_whatsapp', 'openwa_hook_send');

// ==================== MAIN DASHBOARD ====================
function openwa()
{
    global $ui, $config, $admin;
    _admin();
    $admin = Admin::_info();

    if (empty($config['openwa_url'])) {
        r2(U . 'plugin/openwa_config', 'e', 'Please configure OpenWA first');
    }

    // The API calls below can be slow; never hold the PHP session lock while
    // the AJAX QR poller runs in the same browser session.
    if (function_exists('session_write_close')) {
        session_write_close();
    }

    $health = openwa_request('GET', '/api/health/ready', null, 5, true);
    $serverOk = ($health['http_code'] === 200);

    $state = openwa_get_qr(false);
    $connected = !empty($state['connected']);
    $qrCode = $state['qr'] ?? '';
    $error = $state['error'] ?? '';

    if (!$serverOk) {
        $error = 'Cannot reach OpenWA at ' . rtrim($config['openwa_url'], '/')
            . '. Check the base URL and that the container is running.';
    }

    $st = $state['status'] ?? openwa_status(6);
    $status = $st['status'] ?? 'unknown';
    $me = openwa_digits($st['phone'] ?? '');
    if ($me === '') {
        $me = openwa_digits($config['openwa_number'] ?? '');
    }

    openwa_init();
    $todaySent = ORM::for_table('tbl_openwa_logs')
        ->where('status', 'delivered')
        ->where_gte('created_at', date('Y-m-d') . ' 00:00:00')
        ->count();
    $totalSent = ORM::for_table('tbl_openwa_logs')->where('status', 'delivered')->count();
    $totalFailed = ORM::for_table('tbl_openwa_logs')->where('status', 'failed')->count();

    $ui->assign('serverOk', $serverOk);
    $ui->assign('connected', $connected);
    $ui->assign('qrCode', $qrCode);
    $ui->assign('error', $error);
    $ui->assign('me', $me);
    $ui->assign('waStatus', $status);
    $ui->assign('todaySent', $todaySent);
    $ui->assign('totalSent', $totalSent);
    $ui->assign('totalFailed', $totalFailed);
    $ui->assign('enabled', ($config['openwa_enabled'] ?? '') === 'yes');
    $ui->assign('gwSession', $config['openwa_session_name'] ?? '');
    $ui->assign('gwSessionId', $config['openwa_session_id'] ?? '');
    $ui->assign('gwVersion', OPENWA_PLUGIN_VERSION);
    $ui->assign('gwEngine', $config['openwa_engine'] ?? '');

    // Keep a live pairing code on screen for its whole lifetime.
    $pairCode = '';
    $pairPhone = '';
    $pairExpires = 0;
    $pairAt = intval($config['openwa_pair_code_at'] ?? 0);
    if (!$connected
        && !empty($config['openwa_pair_code'])
        && $pairAt > 0
        && (time() - $pairAt) < OPENWA_PAIR_TTL) {
        $pairCode = $config['openwa_pair_code'];
        $pairPhone = $config['openwa_pair_code_phone'] ?? '';
        $pairExpires = $pairAt + OPENWA_PAIR_TTL;
    }
    $ui->assign('pairCode', $pairCode);
    $ui->assign('pairPhone', $pairPhone);
    $ui->assign('pairExpires', $pairExpires);
    $ui->assign('pairTtl', OPENWA_PAIR_TTL);

    $ui->assign('_title', 'OpenWA');
    $ui->assign('_system_menu', 'openwa');
    $ui->assign('menu', '');
    $ui->assign('_admin', $admin);
    $ui->display('openWAGateway.tpl');
}

// ==================== CONFIG ====================
function openwa_config()
{
    global $ui, $config, $admin;
    _admin();

    if (_post('save')) {
        $url = rtrim(_post('openwa_url', ''), '/');
        openwa_save('openwa_url', $url);
        openwa_save('openwa_api_key', trim(_post('openwa_api_key', '')));
        openwa_save('openwa_session_name', trim(_post('openwa_session_name', 'speedradius')));
        openwa_save('openwa_enabled', _post('openwa_enabled', 'no') === 'yes' ? 'yes' : 'no');
        openwa_save('openwa_number', openwa_digits(_post('openwa_number', '')));
        openwa_save('openwa_public_api_key', _post('openwa_public_api_key', ''));

        // Credentials changed -> the cached session id, QR and trust window are stale.
        openwa_save('openwa_session_id', '');
        openwa_save('openwa_connected_at', 0);
        openwa_save('openwa_pair_code', '');
        openwa_save('openwa_pair_code_at', 0);

        if (empty($url)) {
            r2(U . 'plugin/openwa_config', 'e', 'Base URL is required.');
        }
        if (empty($config['openwa_api_key'])) {
            r2(U . 'plugin/openwa_config', 'e', 'API key is required.');
        }

        $st = openwa_status(15);
        if ($st['reachable']) {
            r2(U . 'plugin/openwa', 's', 'Configuration saved. Server reachable'
                . (($st['status'] ?? '') === 'ready' ? ' and WhatsApp is linked.' : ' — device not linked yet.'));
        }
        r2(U . 'plugin/openwa_config', 'e', 'Saved, but the API check failed: ' . ($st['error'] ?: 'unknown error'));
    }

    if (empty($config['openwa_url'])) $config['openwa_url'] = 'http://127.0.0.1:2785';
    if (empty($config['openwa_session_name'])) $config['openwa_session_name'] = 'speedradius';

    $ui->assign('gw_url', $config['openwa_url'] ?? '');
    $ui->assign('gw_api_key', $config['openwa_api_key'] ?? '');
    $ui->assign('gw_session_name', $config['openwa_session_name'] ?? '');
    $ui->assign('gw_enabled', ($config['openwa_enabled'] ?? '') === 'yes');
    $ui->assign('gw_number', $config['openwa_number'] ?? '');
    $ui->assign('gw_public_key', $config['openwa_public_api_key'] ?? '');
    $ui->assign('gwPluginVersion', OPENWA_PLUGIN_VERSION);

    $ui->assign('_title', 'OpenWA Configuration');
    $ui->assign('_system_menu', 'openwa');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('menu', 'config');
    $ui->display('openWAGateway.tpl');
}

// ==================== ACTIONS ====================

/** "Generate QR": make sure the session exists, then start it. */
function openwa_connect()
{
    _admin();
    if (function_exists('session_write_close')) session_write_close();

    $sid = openwa_ensure_session();
    if ($sid === '') {
        r2(U . 'plugin/openwa', 'e', 'Could not create the OpenWA session: ' . openwa_last_error());
    }

    $st = openwa_status(10);
    if (($st['status'] ?? '') === 'ready') {
        r2(U . 'plugin/openwa', 's', 'Session is already linked.');
    }

    $resp = openwa_request('POST', '/api/sessions/' . rawurlencode($sid) . '/start', null, 45);
    if ($resp['http_code'] >= 200 && $resp['http_code'] < 300) {
        openwa_save('openwa_connected_at', 0);
        r2(U . 'plugin/openwa', 's', 'Session starting. Scan the QR code with WhatsApp → Linked devices.');
    }

    r2(U . 'plugin/openwa', 'e', 'Failed to start session: ' . openwa_err($resp));
}

/** Pairing code — POST /api/sessions/{id}/pairing-code {phoneNumber} */
function openwa_pair_code()
{
    _admin();
    global $config;
    if (function_exists('session_write_close')) session_write_close();

    $phone = openwa_format_phone(_post('phone', _req('phone', '')));
    if (empty($phone)) {
        r2(U . 'plugin/openwa', 'e', 'Phone number required for pairing.');
    }
    $force = (_post('force', _req('force', '')) === '1');

    $st = openwa_status(12);
    if (($st['status'] ?? '') === 'ready') {
        openwa_save('openwa_pair_code', '');
        openwa_save('openwa_pair_code_at', 0);
        r2(U . 'plugin/openwa', 's', 'This session is already linked — no pairing code needed.');
    }

    // Reuse a code that is still inside its lifetime: every new request
    // invalidates the previous code and the phone then rejects it.
    $oldCode  = $config['openwa_pair_code'] ?? '';
    $oldAt    = intval($config['openwa_pair_code_at'] ?? 0);
    $oldPhone = $config['openwa_pair_code_phone'] ?? '';
    if (!$force && $oldCode !== '' && $oldAt > 0
        && (time() - $oldAt) < OPENWA_PAIR_TTL && $oldPhone === $phone) {
        r2(U . 'plugin/openwa', 's', 'That code is still valid — enter it on the phone, or press "Get a new code" to replace it.');
    }

    openwa_save('openwa_pair_code', '');
    openwa_save('openwa_pair_code_at', 0);

    $sid = openwa_ensure_session();
    if ($sid === '') {
        r2(U . 'plugin/openwa', 'e', 'Could not create the OpenWA session.');
    }

    // The session must be started before a pairing code can be issued.
    if (!in_array(($st['status'] ?? ''), ['qr_ready', 'initializing', 'authenticating'], true)) {
        openwa_request('POST', '/api/sessions/' . rawurlencode($sid) . '/start', null, 45);
    }

    $resp = openwa_request('POST', '/api/sessions/' . rawurlencode($sid) . '/pairing-code',
        ['phoneNumber' => $phone], 45);

    if ($resp['http_code'] >= 200 && $resp['http_code'] < 300) {
        $json = json_decode($resp['body'], true);
        $code = $json['pairingCode'] ?? '';
        if (!empty($code)) {
            openwa_save('openwa_pair_code', $code);
            openwa_save('openwa_pair_code_at', time());
            openwa_save('openwa_pair_code_phone', $phone);
            r2(U . 'plugin/openwa', 's', 'Code ready — type it into the phone within ' . OPENWA_PAIR_TTL . ' seconds.');
        }
        r2(U . 'plugin/openwa', 's', 'Pairing requested — the code should appear on the phone shortly.');
    }

    r2(U . 'plugin/openwa', 'e', 'Pairing failed: ' . openwa_err($resp));
}

/** Logout — unlinks the device and stops the session. */
function openwa_logout()
{
    _admin();
    if (function_exists('session_write_close')) session_write_close();

    $sid = openwa_session_id();
    if ($sid === '') {
        r2(U . 'plugin/openwa', 'e', 'No OpenWA session to log out.');
    }

    $resp = openwa_request('POST', '/api/sessions/' . rawurlencode($sid) . '/logout', null, 30);

    openwa_save('openwa_connected_at', 0);
    openwa_save('openwa_pair_code', '');
    openwa_save('openwa_pair_code_at', 0);

    if ($resp['http_code'] >= 200 && $resp['http_code'] < 300) {
        r2(U . 'plugin/openwa', 's', 'Logged out. Generate a new QR code to reconnect.');
    }
    r2(U . 'plugin/openwa', 'e', 'Logout failed: ' . openwa_err($resp));
}

/** AJAX: QR refresh / connection status. */
function openwa_refresh_qr()
{
    _admin();
    if (function_exists('session_write_close')) session_write_close();
    header('Content-Type: application/json');

    $force = _req('force', '') === '1';
    $state = openwa_get_qr($force);

    echo json_encode([
        'connected' => !empty($state['connected']),
        'qr'        => $state['qr'] ?? '',
        'error'     => $state['error'] ?? '',
        'status'    => $state['status']['status'] ?? '',
    ]);
    exit;
}

/** AJAX: quick send (text). */
function openwa_send()
{
    _admin();
    if (function_exists('session_write_close')) session_write_close();
    header('Content-Type: application/json');

    $to  = _req('to', '');
    $msg = _req('message', _req('msg', ''));
    if (empty($to) || empty($msg)) {
        echo json_encode(['code' => 'ERROR', 'message' => 'Missing to or message parameter']);
        exit;
    }
    echo json_encode(openwa_send_message($to, $msg));
    exit;
}

// ==================== HOOK SEND ====================
// Fired from Message::sendWhatsapp() when this gateway is enabled.
function openwa_hook_send($data = [])
{
    global $config;
    $phone = is_array($data) ? ($data[0] ?? '') : $data;
    $txt   = is_array($data) ? ($data[1] ?? '') : '';
    if (empty($phone) || empty($txt)) return false;
    if (($config['openwa_enabled'] ?? '') !== 'yes') return false;
    if (empty($config['openwa_url'])) return false;

    $result = openwa_send_message($phone, $txt);
    return ($result['code'] ?? '') === 'SUCCESS';
}

// ==================== CORE: SEND MESSAGE ====================
function openwa_send_message($phone, $txt)
{
    global $config;
    if (empty($config['openwa_url'])) {
        return ['code' => 'ERROR', 'message' => 'OpenWA not configured'];
    }

    $msisdn = openwa_format_phone($phone);
    if (empty($msisdn)) {
        return ['code' => 'ERROR', 'message' => 'Invalid phone number'];
    }

    openwa_init();

    // Same number + same text within 10s is almost always a retried cron job.
    $recent = ORM::for_table('tbl_openwa_logs')
        ->where('phone_number', $msisdn)
        ->where('message', $txt)
        ->where_gte('created_at', date('Y-m-d H:i:s', strtotime('-10s')))
        ->find_one();
    if ($recent) {
        return ['code' => 'DUPLICATE', 'message' => 'Duplicate message within 10s'];
    }

    $log = ORM::for_table('tbl_openwa_logs')->create();
    $log->phone_number = $msisdn;
    $log->message = $txt;
    $log->status = 'sent';
    $log->created_at = date('Y-m-d H:i:s');
    $log->save();

    $sid = openwa_session_id();
    if ($sid === '') {
        $sid = openwa_ensure_session();
    }
    if ($sid === '') {
        $log->status = 'failed';
        $log->response = 'No OpenWA session';
        $log->save();
        return ['code' => 'ERROR', 'message' => 'No OpenWA session — configure and link a device first'];
    }

    $resp = openwa_request('POST', '/api/sessions/' . rawurlencode($sid) . '/messages/send-text', [
        'chatId' => $msisdn . '@c.us',
        'text'   => $txt,
    ], 25);

    if ($resp['http_code'] >= 200 && $resp['http_code'] < 300) {
        $json = json_decode($resp['body'], true);
        $log->status = 'delivered';
        $log->response = $resp['body'];
        $log->save();
        return [
            'code'    => 'SUCCESS',
            'message' => 'Sent',
            'msgid'   => $json['messageId'] ?? '',
        ];
    }

    $log->status = 'failed';
    $log->response = $resp['body'];
    $log->save();

    return ['code' => 'ERROR', 'message' => openwa_err($resp)];
}

// ==================== PUBLIC API ====================
// Usage: ?_route=plugin/openwa_api&key=API_KEY&to=254712345678&message=Hello
function openwa_api()
{
    header('Content-Type: application/json');
    global $config;

    $key = _req('key', '');
    $to  = _req('to', '');
    $msg = _req('message', _req('msg', ''));

    $apiKey = $config['openwa_public_api_key'] ?? '';
    if (empty($apiKey)) {
        echo json_encode(['code' => 'ERROR', 'message' => 'API not configured — set an API key in OpenWA Config']);
        exit;
    }
    if (!hash_equals((string) $apiKey, (string) $key)) {
        echo json_encode(['code' => 'ERROR', 'message' => 'Invalid API key']);
        exit;
    }
    if (empty($to) || empty($msg)) {
        echo json_encode(['code' => 'ERROR', 'message' => 'Missing to or message parameter']);
        exit;
    }

    echo json_encode(openwa_send_message($to, $msg));
    exit;
}

// ==================== LOGS ====================
function openwa_logs()
{
    global $ui, $admin;
    _admin();
    openwa_init();

    if (isset($_POST['delete_all'])) {
        ORM::for_table('tbl_openwa_logs')->delete_many();
        r2(U . 'plugin/openwa_logs', 's', 'All logs deleted');
    }

    $page = max(1, intval(_get('page', 1)));
    $search = _get('search', '');

    $filtered = function () use ($search) {
        $q = ORM::for_table('tbl_openwa_logs');
        if (!empty($search)) {
            $q->where_raw('(phone_number LIKE ? OR message LIKE ?)', ["%$search%", "%$search%"]);
        }
        return $q;
    };

    $total = $filtered()->count();
    $logs = $filtered()->order_by_desc('id')->offset(($page - 1) * 30)->limit(30)->find_many();

    $ui->assign('logs', $logs);
    $ui->assign('total', $total);
    $ui->assign('page', $page);
    $ui->assign('totalPages', max(1, ceil($total / 30)));
    $ui->assign('search', $search);
    $ui->assign('_title', 'OpenWA Logs');
    $ui->assign('_system_menu', 'openwa_logs');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('openWAGateway_logs.tpl');
}

// ==================== OPENWA API HELPERS ====================

function openwa_base()
{
    global $config;
    return rtrim($config['openwa_url'] ?? '', '/');
}

function openwa_key()
{
    global $config;
    return trim($config['openwa_api_key'] ?? '');
}

function openwa_last_error()
{
    return $GLOBALS['openwa_last_error'] ?? 'unknown error';
}

/**
 * Low-level cURL call. Always sends the X-API-Key header. $body is encoded as
 * JSON; pass null for requests with no body.
 */
function openwa_request($method, $path, $body = null, $timeout = 20, $noAuth = false)
{
    $base = openwa_base();
    if ($base === '') {
        return ['http_code' => 0, 'body' => '', 'error' => 'OpenWA base URL is not configured'];
    }

    $headers = ['Accept: application/json'];
    if (!$noAuth) {
        $key = openwa_key();
        if ($key === '') {
            return ['http_code' => 0, 'body' => '', 'error' => 'OpenWA API key is not configured'];
        }
        $headers[] = 'X-API-Key: ' . $key;
    }

    $opts = [
        CURLOPT_URL            => $base . $path,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
    ];

    if ($method === 'POST') {
        $opts[CURLOPT_POST] = true;
        if ($body !== null) {
            $headers[] = 'Content-Type: application/json';
            $opts[CURLOPT_HTTPHEADER] = $headers;
            $opts[CURLOPT_POSTFIELDS] = json_encode($body);
        }
    } elseif ($method !== 'GET') {
        $opts[CURLOPT_CUSTOMREQUEST] = $method;
        if ($body !== null) {
            $headers[] = 'Content-Type: application/json';
            $opts[CURLOPT_HTTPHEADER] = $headers;
            $opts[CURLOPT_POSTFIELDS] = json_encode($body);
        }
    }

    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $out = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    return ['http_code' => $code, 'body' => $out === false ? '' : $out, 'error' => $err];
}

/** Pull the most useful error text out of an OpenWA response. */
function openwa_err($resp)
{
    $code = $resp['http_code'] ?? 0;
    $body = $resp['body'] ?? '';
    $curl = $resp['error'] ?? '';

    $j = json_decode($body, true);
    if (is_array($j)) {
        if (isset($j['code']) && $j['code'] === 'SESSION_NAME_TEARDOWN_PENDING') {
            return 'The previous session is still shutting down — wait a few seconds and retry.';
        }
        $m = $j['message'] ?? '';
        if (is_array($m)) $m = implode('; ', $m);
        if (trim((string) $m) !== '') {
            return trim((string) $m) . (isset($j['code']) ? ' [' . $j['code'] . ']' : '');
        }
        if (!empty($j['error'])) return $j['error'] . ' (HTTP ' . $code . ')';
    }

    if ($code === 401) return 'Unauthorized — check the OpenWA API key';
    if ($code === 403) return 'Forbidden — this API key is not allowed to use this session (check its session scope)';
    if ($code === 404) return 'Not found — the session may have been deleted in OpenWA';
    if ($code === 409) return 'Session is not connected — wait until it is ready and retry';
    if ($code === 0 && $curl) return $curl;
    if ($curl) return $curl;

    $t = trim((string) $body);
    if ($t === '') return 'HTTP ' . $code . ' — empty response';
    if (stripos($t, '<html') !== false || stripos($t, '<!doctype') !== false) {
        return 'HTTP ' . $code . ' — the server returned an HTML page, not JSON. Check the base URL.';
    }
    return strlen($t) > 300 ? substr($t, 0, 300) . '...' : $t;
}

/** Session id cached in config. */
function openwa_session_id()
{
    global $config;
    return trim($config['openwa_session_id'] ?? '');
}

/**
 * Make sure a session exists on the OpenWA server and cache its id.
 * Returns the session id, or '' on failure (see openwa_last_error()).
 */
function openwa_ensure_session()
{
    global $config;

    $cached = openwa_session_id();
    if ($cached !== '') {
        $r = openwa_request('GET', '/api/sessions/' . rawurlencode($cached), null, 8);
        if ($r['http_code'] === 200) {
            return $cached;
        }
        if ($r['http_code'] !== 404) {
            // Transient failure — keep the cached id rather than creating a duplicate.
            $GLOBALS['openwa_last_error'] = openwa_err($r);
            return $cached;
        }
        // 404 -> it really is gone; fall through and recreate.
        openwa_save('openwa_session_id', '');
    }

    $name = trim($config['openwa_session_name'] ?? '');
    if ($name === '') $name = 'speedradius';

    // Reuse an existing session with that name, if any.
    $r = openwa_request('GET', '/api/sessions', null, 10);
    if ($r['http_code'] === 200) {
        $list = json_decode($r['body'], true);
        if (is_array($list)) {
            foreach ($list as $s) {
                if (is_array($s) && ($s['name'] ?? '') === $name && !empty($s['id'])) {
                    openwa_save('openwa_session_id', $s['id']);
                    return $s['id'];
                }
            }
        }
    } else {
        $GLOBALS['openwa_last_error'] = openwa_err($r);
    }

    // Create it.
    $r = openwa_request('POST', '/api/sessions', ['name' => $name], 15);
    if ($r['http_code'] >= 200 && $r['http_code'] < 300) {
        $json = json_decode($r['body'], true);
        if (!empty($json['id'])) {
            openwa_save('openwa_session_id', $json['id']);
            return $json['id'];
        }
    }

    $GLOBALS['openwa_last_error'] = openwa_err($r);
    return '';
}

/** GET /api/sessions/{id} — the single source of truth for connection state. */
function openwa_status($timeout = 8)
{
    global $config;
    $empty = ['reachable' => false, 'error' => 'Not configured', 'status' => '',
              'phone' => '', 'connected' => false];

    if (empty($config['openwa_url'])) return $empty;

    $sid = openwa_session_id();
    if ($sid === '') {
        $sid = openwa_ensure_session();
    }
    if ($sid === '') {
        $empty['error'] = openwa_last_error();
        return $empty;
    }

    $r = openwa_request('GET', '/api/sessions/' . rawurlencode($sid), null, $timeout);
    $json = json_decode($r['body'], true);
    $ok = ($r['http_code'] === 200 && is_array($json));

    $status = $ok ? ($json['status'] ?? '') : '';
    return [
        'reachable' => $ok,
        'error'     => $ok ? '' : openwa_err($r),
        'status'    => $status,
        'phone'     => $ok ? ($json['phone'] ?? '') : '',
        'connected' => $status === 'ready',
        'pushName'  => $ok ? ($json['pushName'] ?? '') : '',
    ];
}

/**
 * Determine connection state + QR.
 *
 * The QR is a data URL, so it is cached in appconfig and embedded directly —
 * the API key never has to appear in an <img src>. A short trust window stops
 * the AJAX poller from hammering the API.
 */
function openwa_get_qr($force = false)
{
    global $config;
    $now = time();

    if (!$force && !empty($config['openwa_connected_at']) && ($now - intval($config['openwa_connected_at'])) < 45) {
        return ['connected' => true, 'qr' => '', 'status' => ['status' => 'ready']];
    }

    $st = openwa_status(6);
    if ($st['connected']) {
        openwa_save('openwa_connected_at', $now);
        openwa_save('openwa_qr', '');
        openwa_save('openwa_qr_at', 0);
        openwa_save('openwa_pair_code', '');
        openwa_save('openwa_pair_code_at', 0);
        return ['connected' => true, 'qr' => '', 'status' => $st];
    }
    if (!$st['reachable']) {
        return ['connected' => false, 'qr' => '', 'error' => $st['error'], 'status' => $st];
    }

    openwa_save('openwa_connected_at', 0);

    $cached  = $config['openwa_qr'] ?? '';
    $cachedAt = intval($config['openwa_qr_at'] ?? 0);
    $qrTtl   = max(15, intval($config['openwa_qr_ttl'] ?? 25));

    if (!$force && $cached !== '' && ($now - $cachedAt) < $qrTtl) {
        return ['connected' => false, 'qr' => $cached, 'status' => $st];
    }

    $sid = openwa_session_id();
    if ($sid === '') {
        return ['connected' => false, 'qr' => '', 'error' => 'No session', 'status' => $st];
    }

    // Do NOT start the session here. On a multi-tenant server every dashboard view
    // would otherwise spawn a Chromium engine; starting is an explicit action
    // (Generate QR / pairing code).
    $r = openwa_request('GET', '/api/sessions/' . rawurlencode($sid) . '/qr', null, 20);
    if ($r['http_code'] === 200) {
        $json = json_decode($r['body'], true);
        $data = $json['qrCode'] ?? '';
        if ($data !== '') {
            openwa_save('openwa_qr', $data);
            openwa_save('openwa_qr_at', $now);
            openwa_save('openwa_qr_ttl', 25);
            return ['connected' => false, 'qr' => $data, 'status' => $st];
        }
    }

    // 400 simply means "no QR right now" (already linked, or still initializing).
    $msg = '';
    if ($r['http_code'] !== 400) {
        $msg = openwa_err($r);
    }
    return ['connected' => false, 'qr' => '', 'error' => $msg, 'status' => $st];
}

// ==================== DB HELPERS ====================
function openwa_save($k, $v)
{
    global $config;
    $d = ORM::for_table('tbl_appconfig')->where('setting', $k)->find_one();
    if ($d) {
        $d->value = $v;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = $k;
        $d->value = $v;
        $d->save();
    }
    $config[$k] = $v;
}

function openwa_init()
{
    try {
        ORM::raw_execute("CREATE TABLE IF NOT EXISTS `tbl_openwa_logs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `phone_number` VARCHAR(20) NOT NULL,
            `message` TEXT,
            `status` VARCHAR(20) DEFAULT 'sent',
            `response` TEXT,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    } catch (Exception $e) {
        // table may already exist
    }
}

/** Digits only. */
function openwa_digits($phone)
{
    return preg_replace('/[^0-9]/', '', (string) $phone);
}

/** Normalize a Kenyan/international number to digits-only international format. */
function openwa_format_phone($phone)
{
    $phone = preg_replace('/[^0-9]/', '', (string) $phone);
    if (strlen($phone) === 9 && $phone[0] === '7') {
        $phone = '254' . $phone;
    } elseif (strlen($phone) === 10 && $phone[0] === '0') {
        $phone = '254' . substr($phone, 1);
    } elseif (strlen($phone) === 10 && substr($phone, 0, 2) === '01') {
        $phone = '254' . substr($phone, 1);
    }
    return $phone;
}
