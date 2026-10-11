<?php

/**
 * GoWhatsApp Gateway Plugin for SpeedRadius (PHPNuxBill)
 * Targets: GOWA — aldinokemal/go-whatsapp-web-multidevice
 *          https://github.com/aldinokemal/go-whatsapp-web-multidevice
 *
 * Migrated from dimaskiddo/go-whatsapp-multidevice-rest to GOWA v9.
 *
 * WHAT CHANGED IN THE API (v9)
 *   - Auth is HTTP Basic Auth on EVERY request (server APP_BASIC_AUTH=user:pass).
 *     The old GET /auth -> JWT -> "Authorization: Bearer ..." flow no longer exists.
 *   - Routes live at the root (http://host:3000). Append APP_BASE_PATH if the
 *     server was started with one, e.g. https://host/gowa.
 *   - Multi-device (v8+): device-scoped calls take an X-Device-Id header.
 *     It is optional when exactly one device is registered.
 *   - Responses are {code, message, results}. Success == code === "SUCCESS".
 *     (The old {status:true, data:{...}} envelope is gone.)
 *
 * Endpoints used:
 *   GET  /health                      public, always at the root, no auth
 *   GET  /app/status                  results.is_connected / is_logged_in / jid / device_id
 *   GET  /app/info                    results.version / base_path / size limits
 *   GET  /app/devices                 registered device ids
 *   GET  /app/login                   results.qr_link (PNG url) + results.qr_duration
 *   GET  /app/login-with-code?phone=   results.pair_code
 *   GET  /app/logout                  keeps the device slot
 *   GET  /app/reconnect
 *   GET  /user/check?phone=           results.is_on_whatsapp
 *   POST /send/message                JSON {phone, message}
 *   POST /send/image                  multipart {phone, caption, image}
 *
 * Not to be confused with the "GoWAHA" plugin (gowaha.php), which speaks the
 * WAHA API (devlikeapro/waha) and uses an X-Api-Key header.
 */

// Shown on the config page so a stale (opcache-cached) build is visible at a
// glance instead of failing silently as "Function not found".
if (!defined('GOWHATSAPP_PLUGIN_VERSION')) {
    define('GOWHATSAPP_PLUGIN_VERSION', '2.2.66');
}

register_menu("GoWhatsApp", true, "gowhatsapp", 'COMMUNICATION', 'fa fa-whatsapp', '', '', ['Admin', 'SuperAdmin']);
register_menu("GoWhatsApp Logs", true, "gowhatsapp_logs", 'COMMUNICATION', 'fa fa-list-alt', '', '', ['Admin', 'SuperAdmin']);
register_menu("GoWhatsApp API", false, "gowhatsapp_api", '', '', '', '', []);
// Streams the QR PNG through this server (see gowhatsapp_qr_image below).
register_menu("GoWhatsApp QR", false, "gowhatsapp_qr_image", '', '', '', '', []);

register_hook('send_whatsapp_gowhatsapp', 'gowhatsapp_hook_send');

// ==================== MAIN DASHBOARD ====================
function gowhatsapp()
{
    global $ui, $config, $admin;
    _admin();
    $admin = Admin::_info();

    if (empty($config['gowhatsapp_url'])) {
        r2(U . 'plugin/gowhatsapp_config', 'e', 'Please configure GoWhatsApp first');
    }

    // Release the PHP session lock: the API calls below can be slow and would
    // otherwise block the AJAX QR polls running in the same session.
    if (function_exists('session_write_close')) {
        session_write_close();
    }

    // /health is public — it tells us the server is up even when auth is wrong.
    $health = gowhatsapp_request('GET', '/health', [], 5);
    $serverOk = ($health['http_code'] === 200);

    // Connection state (also produces/refreshes the QR when not linked).
    $state = gowhatsapp_get_qr(false);
    $connected = !empty($state['connected']);
    $qrCode = isset($state['qr']) ? $state['qr'] : '';

    $error = isset($state['error']) ? $state['error'] : '';
    if (!$serverOk) {
        $error = 'Cannot reach the GoWhatsApp server at ' . rtrim($config['gowhatsapp_url'], '/')
            . '. Check the base URL and that the container is running.';
    }

    $st = $state['status'] ?? gowhatsapp_status(6);
    $me = $st['jid'] !== '' ? preg_replace('/[^0-9]/', '', explode('@', $st['jid'])[0]) : '';
    if ($me === '') {
        $me = preg_replace('/[^0-9]/', '', $config['gowhatsapp_number'] ?? '');
    }

    $info = gowhatsapp_app_info();

    gowhatsapp_init();
    $todaySent = ORM::for_table('tbl_gowhatsapp_logs')
        ->where('status', 'delivered')
        ->where_gte('created_at', date('Y-m-d') . ' 00:00:00')
        ->count();
    $totalSent = ORM::for_table('tbl_gowhatsapp_logs')->where('status', 'delivered')->count();
    $totalFailed = ORM::for_table('tbl_gowhatsapp_logs')->where('status', 'failed')->count();

    $ui->assign('serverOk', $serverOk);
    $ui->assign('connected', $connected);
    $ui->assign('qrCode', $qrCode);
    $ui->assign('error', $error);
    $ui->assign('me', $me);
    $ui->assign('todaySent', $todaySent);
    $ui->assign('totalSent', $totalSent);
    $ui->assign('totalFailed', $totalFailed);
    $ui->assign('enabled', ($config['gowhatsapp_enabled'] ?? '') === 'yes');
    $ui->assign('gwDeviceId', $config['gowhatsapp_device_id'] ?? '');
    $ui->assign('gwVersion', $info['version'] ?? '');
    $ui->assign('gwPluginVersion', GOWHATSAPP_PLUGIN_VERSION);
    $ui->assign('gwOs', $info['os'] ?? '');
    $ui->assign('_title', 'GoWhatsApp');
    $ui->assign('_system_menu', 'gowhatsapp');
    $ui->assign('_admin', $admin);
    $ui->display('goWhatsAppGateway.tpl');
}

// ==================== CONFIG ====================
function gowhatsapp_config()
{
    global $ui, $config, $admin;
    _admin();

    if (_post('save')) {
        $url = rtrim(_post('gowhatsapp_url', ''), '/');
        gowhatsapp_save('gowhatsapp_url', $url);
        gowhatsapp_save('gowhatsapp_username', _post('gowhatsapp_username', ''));
        gowhatsapp_save('gowhatsapp_password', _post('gowhatsapp_password', ''));
        gowhatsapp_save('gowhatsapp_device_id', trim(_post('gowhatsapp_device_id', '')));
        gowhatsapp_save('gowhatsapp_enabled', _post('gowhatsapp_enabled', 'no') === 'yes' ? 'yes' : 'no');
        gowhatsapp_save('gowhatsapp_number', preg_replace('/[^0-9]/', '', _post('gowhatsapp_number', '')));
        gowhatsapp_save('gowhatsapp_api_key', _post('gowhatsapp_api_key', ''));

        // Credentials or device changed -> drop the cached QR, the connection
        // trust window, and the device verification so the next call re-checks.
        gowhatsapp_save('gowhatsapp_qr_link', '');
        gowhatsapp_save('gowhatsapp_qr_time', 0);
        gowhatsapp_save('gowhatsapp_connected_at', 0);
        gowhatsapp_save('gowhatsapp_device_ok_at', 0);

        if (empty($url)) {
            r2(U . 'plugin/gowhatsapp_config', 'e', 'Base URL is required.');
        }

        // GOWA has no token exchange, so verify with /app/status instead.
        $st = gowhatsapp_status(15);
        if ($st['reachable']) {
            r2(U . 'plugin/gowhatsapp', 's', 'Configuration saved. Server reachable'
                . ($st['logged_in'] ? ' and device is linked.' : ' — device not linked yet.'));
        }
        r2(U . 'plugin/gowhatsapp_config', 'e', 'Saved, but the API check failed: ' . ($st['error'] ?: 'unknown error'));
    }

    if (empty($config['gowhatsapp_url'])) $config['gowhatsapp_url'] = 'http://127.0.0.1:3000';

    $ui->assign('gw_url', $config['gowhatsapp_url'] ?? '');
    $ui->assign('gw_username', $config['gowhatsapp_username'] ?? '');
    $ui->assign('gw_password', $config['gowhatsapp_password'] ?? '');
    $ui->assign('gw_device_id', $config['gowhatsapp_device_id'] ?? '');
    $ui->assign('gw_enabled', ($config['gowhatsapp_enabled'] ?? '') === 'yes');
    $ui->assign('gw_number', $config['gowhatsapp_number'] ?? '');
    $ui->assign('gw_api_key', $config['gowhatsapp_api_key'] ?? '');
    $ui->assign('gwPluginVersion', GOWHATSAPP_PLUGIN_VERSION);

    $ui->assign('_title', 'GoWhatsApp Configuration');
    $ui->assign('_system_menu', 'gowhatsapp');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('menu', 'config');
    $ui->display('goWhatsAppGateway.tpl');
}

// ==================== ACTIONS ====================

/**
 * "Generate QR". GOWA returns an error if /app/login is called on a device that
 * is already paired, so reconnect in that case instead of failing.
 */
function gowhatsapp_connect()
{
    _admin();

    $st = gowhatsapp_status(10);
    if ($st['logged_in']) {
        gowhatsapp_request('GET', '/app/reconnect', [], 20);
        r2(U . 'plugin/gowhatsapp', 's', 'Device is already linked — reconnected.');
    }

    gowhatsapp_save('gowhatsapp_qr_link', '');
    gowhatsapp_save('gowhatsapp_qr_time', 0);
    gowhatsapp_save('gowhatsapp_connected_at', 0);

    $state = gowhatsapp_get_qr(true);
    if (!empty($state['error'])) {
        r2(U . 'plugin/gowhatsapp', 'e', 'Failed to generate QR: ' . $state['error']);
    }
    r2(U . 'plugin/gowhatsapp', 's', 'QR code generated. Scan it in WhatsApp → Linked devices.');
}

/** Pairing code — GET /app/login-with-code?phone= */
function gowhatsapp_pair_code()
{
    _admin();

    $phone = gowhatsapp_format_phone(_post('phone', _req('phone', '')));
    if (empty($phone)) {
        r2(U . 'plugin/gowhatsapp', 'e', 'Phone number required for pairing.');
    }

    $resp = gowhatsapp_request('GET', '/app/login-with-code', ['phone' => $phone], 30);
    if (gowhatsapp_ok($resp['body'], $resp['http_code'])) {
        $json = json_decode($resp['body'], true);
        $code = $json['results']['pair_code'] ?? '';
        if (!empty($code)) {
            r2(U . 'plugin/gowhatsapp', 's', 'Pairing code: ' . $code
                . ' — enter it on the phone under Linked devices → Link with phone number.');
        }
        r2(U . 'plugin/gowhatsapp', 's', 'Pairing requested — the code should appear on the phone shortly.');
    }

    r2(U . 'plugin/gowhatsapp', 'e', 'Pairing failed: ' . gowhatsapp_err($resp['body'], $resp['http_code'], $resp['error']));
}

/** Logout — GET /app/logout (keeps the device slot so it can be re-paired). */
function gowhatsapp_logout()
{
    _admin();

    $resp = gowhatsapp_request('GET', '/app/logout', [], 25);

    gowhatsapp_save('gowhatsapp_qr_link', '');
    gowhatsapp_save('gowhatsapp_qr_time', 0);
    gowhatsapp_save('gowhatsapp_connected_at', 0);

    if (gowhatsapp_ok($resp['body'], $resp['http_code'])) {
        r2(U . 'plugin/gowhatsapp', 's', 'Logged out. Generate a new QR code to reconnect.');
    }
    r2(U . 'plugin/gowhatsapp', 'e', 'Logout failed: ' . gowhatsapp_err($resp['body'], $resp['http_code'], $resp['error']));
}

/** AJAX: QR refresh / connection status. */
function gowhatsapp_refresh_qr()
{
    _admin();
    if (function_exists('session_write_close')) {
        session_write_close();
    }
    header('Content-Type: application/json');

    $force = _req('force', '') === '1';
    $state = gowhatsapp_get_qr($force);

    echo json_encode([
        'connected' => !empty($state['connected']),
        'qr'        => isset($state['qr']) ? $state['qr'] : '',
        'error'     => isset($state['error']) ? $state['error'] : '',
    ]);
    exit;
}

/**
 * Streams the QR PNG generated by GOWA.
 *
 * GOWA's /app/login returns a `qr_link` built from its own host and port, which
 * is usually http://localhost:3000/... and therefore unreachable from the
 * browser. Fetching it here also means the Basic Auth credentials never have to
 * leave the server (embedding them in an <img src> would expose them).
 */
function gowhatsapp_qr_image()
{
    global $config;
    _admin();
    if (function_exists('session_write_close')) {
        session_write_close();
    }

    $link = $config['gowhatsapp_qr_link'] ?? '';
    if ($link === '') {
        http_response_code(404);
        exit;
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => gowhatsapp_absolute_url($link),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_HTTPHEADER     => ['Authorization: Basic ' . gowhatsapp_basic()],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
    ]);
    $img   = curl_exec($ch);
    $code  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $ctype = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);

    if ($code !== 200 || !$img) {
        http_response_code(404);
        exit;
    }

    header('Content-Type: ' . ($ctype ?: 'image/png'));
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    echo $img;
    exit;
}

/** AJAX: quick send (text). */
function gowhatsapp_send()
{
    _admin();
    if (function_exists('session_write_close')) {
        session_write_close();
    }
    header('Content-Type: application/json');

    $to  = _req('to', '');
    $msg = _req('message', _req('msg', ''));
    if (empty($to) || empty($msg)) {
        echo json_encode(['code' => 'ERROR', 'message' => 'Missing to or message parameter']);
        exit;
    }
    echo json_encode(gowhatsapp_send_message($to, $msg));
    exit;
}

/** Send image — POST /send/image, multipart with `phone`. */
function gowhatsapp_send_image()
{
    _admin();

    $to = _post('to', '');
    $caption = _post('caption', '');
    if (empty($to) || empty($_FILES['image']['tmp_name'])) {
        r2(U . 'plugin/gowhatsapp', 'e', 'Phone number and image are required.');
    }

    $msisdn = gowhatsapp_format_phone($to);
    if (empty($msisdn)) {
        r2(U . 'plugin/gowhatsapp', 'e', 'Invalid phone number.');
    }

    gowhatsapp_init();
    $log = ORM::for_table('tbl_gowhatsapp_logs')->create();
    $log->phone_number = $msisdn;
    $log->message = '[IMAGE] ' . $caption;
    $log->status = 'sent';
    $log->created_at = date('Y-m-d H:i:s');
    $log->save();

    $file = new CURLFile(
        $_FILES['image']['tmp_name'],
        $_FILES['image']['type'] ?: 'image/jpeg',
        $_FILES['image']['name']
    );
    $resp = gowhatsapp_request('POST', '/send/image', [
        'phone'   => $msisdn,
        'caption' => $caption,
        'image'   => $file,
    ], 60);

    $log->status = gowhatsapp_ok($resp['body'], $resp['http_code']) ? 'delivered' : 'failed';
    $log->response = $resp['body'];
    $log->save();

    if ($log->status === 'delivered') {
        r2(U . 'plugin/gowhatsapp', 's', 'Image sent.');
    }
    r2(U . 'plugin/gowhatsapp', 'e', 'Image send failed: ' . gowhatsapp_err($resp['body'], $resp['http_code'], $resp['error']));
}

// ==================== HOOK SEND ====================
// Fired from Message::sendWhatsapp() when this gateway is enabled.
function gowhatsapp_hook_send($data = [])
{
    global $config;
    $phone = is_array($data) ? ($data[0] ?? '') : $data;
    $txt   = is_array($data) ? ($data[1] ?? '') : '';
    if (empty($phone) || empty($txt)) return false;
    if (($config['gowhatsapp_enabled'] ?? '') !== 'yes') return false;
    if (empty($config['gowhatsapp_url'])) return false;

    $result = gowhatsapp_send_message($phone, $txt);
    return ($result['code'] ?? '') === 'SUCCESS';
}

// ==================== CORE: SEND MESSAGE ====================
function gowhatsapp_send_message($phone, $txt)
{
    global $config;
    if (empty($config['gowhatsapp_url'])) {
        return ['code' => 'ERROR', 'message' => 'GoWhatsApp not configured'];
    }

    $msisdn = gowhatsapp_format_phone($phone);
    if (empty($msisdn)) {
        return ['code' => 'ERROR', 'message' => 'Invalid phone number'];
    }

    gowhatsapp_init();

    // Dedup: same number + same message within 10s
    $recent = ORM::for_table('tbl_gowhatsapp_logs')
        ->where('phone_number', $msisdn)
        ->where('message', $txt)
        ->where_gte('created_at', date('Y-m-d H:i:s', strtotime('-10s')))
        ->find_one();
    if ($recent) {
        return ['code' => 'DUPLICATE', 'message' => 'Duplicate message within 10s'];
    }

    $log = ORM::for_table('tbl_gowhatsapp_logs')->create();
    $log->phone_number = $msisdn;
    $log->message = $txt;
    $log->status = 'sent';
    $log->created_at = date('Y-m-d H:i:s');
    $log->save();

    // GOWA expects a JSON body here, unlike the multipart /send/media endpoints.
    $resp = gowhatsapp_request('POST', '/send/message', [
        'phone'   => $msisdn,
        'message' => $txt,
    ], 25, true);

    if (gowhatsapp_ok($resp['body'], $resp['http_code'])) {
        $json = json_decode($resp['body'], true);
        $log->status = 'delivered';
        $log->response = $resp['body'];
        $log->save();
        return [
            'code'    => 'SUCCESS',
            'message' => 'Sent',
            'msgid'   => $json['results']['message_id'] ?? '',
        ];
    }

    $log->status = 'failed';
    $log->response = $resp['body'];
    $log->save();

    return ['code' => 'ERROR', 'message' => gowhatsapp_err($resp['body'], $resp['http_code'], $resp['error'])];
}

// ==================== PUBLIC API ====================
// Usage: ?_route=plugin/gowhatsapp_api&key=API_KEY&to=254712345678&message=Hello
function gowhatsapp_api()
{
    header('Content-Type: application/json');
    global $config;

    $key = _req('key', '');
    $to  = _req('to', '');
    $msg = _req('message', _req('msg', ''));

    $apiKey = $config['gowhatsapp_api_key'] ?? '';
    if (empty($apiKey)) {
        echo json_encode(['code' => 'ERROR', 'message' => 'API not configured — set an API key in GoWhatsApp Config']);
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

    echo json_encode(gowhatsapp_send_message($to, $msg));
    exit;
}

// ==================== LOGS ====================
function gowhatsapp_logs()
{
    global $ui, $admin;
    _admin();
    gowhatsapp_init();

    if (isset($_POST['delete_all'])) {
        ORM::for_table('tbl_gowhatsapp_logs')->delete_many();
        r2(U . 'plugin/gowhatsapp_logs', 's', 'All logs deleted');
    }

    $page = max(1, intval(_get('page', 1)));
    $search = _get('search', '');

    // Built twice on purpose: reusing one Idiorm object after count() relies on
    // internal state that is not documented, and a lost WHERE clause here would
    // silently page through every log row.
    $filtered = function () use ($search) {
        $q = ORM::for_table('tbl_gowhatsapp_logs');
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
    $ui->assign('_title', 'GoWhatsApp Logs');
    $ui->assign('_system_menu', 'gowhatsapp_logs');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('goWhatsAppGateway_logs.tpl');
}

// ==================== GOWA API HELPERS ====================

function gowhatsapp_basic()
{
    return base64_encode(gowhatsapp_cfg_user() . ':' . gowhatsapp_cfg_pass());
}

function gowhatsapp_cfg_user()
{
    global $config;
    $u = $config['gowhatsapp_username'] ?? '';
    return ($u === '' || $u === null) ? 'ThisIsUsername' : $u;
}

function gowhatsapp_cfg_pass()
{
    global $config;
    $p = $config['gowhatsapp_password'] ?? '';
    return ($p === '' || $p === null) ? 'ThisIsPassword' : $p;
}

/** GOWA signals success with code === "SUCCESS" (the old {status:true} is gone). */
function gowhatsapp_ok($body, $httpCode)
{
    if ($httpCode < 200 || $httpCode >= 300) return false;
    $j = json_decode($body, true);
    return is_array($j) && isset($j['code']) && strtoupper((string) $j['code']) === 'SUCCESS';
}

/** Pull the most useful error text out of a GOWA response. */
function gowhatsapp_err($body, $httpCode, $curlError = '')
{
    $j = json_decode($body, true);
    if (is_array($j)) {
        if (isset($j['code']) && $j['code'] === 'DEVICE_ID_REQUIRED') {
            return 'No device is registered on the GoWhatsApp server and it could not be created automatically. '
                 . 'Set a Device ID in this config (for example speedradius).';
        }
        $m = isset($j['message']) ? trim((string) $j['message']) : '';
        if ($m !== '') return $m;
        if (!empty($j['code'])) return 'HTTP ' . $httpCode . ' (' . $j['code'] . ')';
    }
    if ($httpCode === 401) return 'Unauthorized — check the Basic Auth username and password (APP_BASIC_AUTH)';
    if ($httpCode === 0 && $curlError) return $curlError;
    if ($curlError) return $curlError;

    $t = trim((string) $body);
    if ($t === '') return 'HTTP ' . $httpCode . ' — empty response';

    // Never surface a raw HTML error page in a flash message.
    if (stripos($t, '<html') !== false || stripos($t, '<!doctype') !== false) {
        return 'HTTP ' . $httpCode . ' — the server returned an HTML page, not JSON. Is the base URL pointing at the GoWhatsApp API root?';
    }
    return strlen($t) > 300 ? substr($t, 0, 300) . '...' : $t;
}

/** resolve a possibly-relative qr_link against the configured base URL */
function gowhatsapp_absolute_url($link)
{
    global $config;
    if (preg_match('#^https?://#i', $link)) return $link;

    $p = parse_url(rtrim($config['gowhatsapp_url'] ?? '', '/'));
    if (empty($p['scheme']) || empty($p['host'])) return $link;

    return $p['scheme'] . '://' . $p['host']
        . (isset($p['port']) ? ':' . $p['port'] : '')
        . '/' . ltrim($link, '/');
}

/**
 * Low-level cURL call. Always sends Basic Auth (GOWA has no token step) and
 * X-Device-Id when configured. $json = true sends a JSON body instead of form
 * encoding — required by POST /send/message.
 */
function gowhatsapp_request($method, $path, $fields = [], $timeout = 20, $json = false)
{
    global $config;
    $base = rtrim($config['gowhatsapp_url'] ?? '', '/');
    if ($base === '') {
        return ['http_code' => 0, 'body' => '', 'error' => 'GoWhatsApp base URL is not configured'];
    }

    $hasFile = false;
    foreach ($fields as $v) {
        if ($v instanceof CURLFile) { $hasFile = true; break; }
    }

    $sendJson = ($json && !$hasFile);

    $headers = [
        'Authorization: Basic ' . gowhatsapp_basic(),
        'Accept: application/json',
    ];
    $device = trim($config['gowhatsapp_device_id'] ?? '');
    if ($device !== '') {
        $headers[] = 'X-Device-Id: ' . $device;
    }
    if ($sendJson) {
        $headers[] = 'Content-Type: application/json';
    }

    $url = $base . $path;
    if ($method === 'GET' && !empty($fields)) {
        $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($fields);
    }

    $opts = [
        CURLOPT_URL            => $url,
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
        if ($hasFile) {
            // Let cURL set the multipart boundary itself.
            $opts[CURLOPT_POSTFIELDS] = $fields;
        } elseif ($sendJson) {
            $opts[CURLOPT_POSTFIELDS] = json_encode($fields);
        } elseif (!empty($fields)) {
            $opts[CURLOPT_POSTFIELDS] = http_build_query($fields);
        }
    } elseif ($method !== 'GET') {
        $opts[CURLOPT_CUSTOMREQUEST] = $method;
        if (!empty($fields)) {
            $opts[CURLOPT_POSTFIELDS] = $sendJson ? json_encode($fields) : http_build_query($fields);
        }
    }

    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    return ['http_code' => $code, 'body' => $body === false ? '' : $body, 'error' => $err];
}

/**
 * GOWA v8+ is multi-device and every device-scoped call needs a device slot.
 * A freshly installed server has none, and unlike the older single-device
 * behaviour there is no implicit default — every call answers
 * DEVICE_ID_REQUIRED until one exists. So: if no device ID is configured,
 * adopt whatever the server already has, and if it has nothing, create a slot.
 * The chosen ID is persisted so this runs once.
 */
function gowhatsapp_ensure_device()
{
    global $config;

    $configured = trim($config['gowhatsapp_device_id'] ?? '');

    // A configured ID is NOT proof the device exists. Typing one into the config
    // form does not create it, and a non-existent ID makes every call answer
    // DEVICE_ID_REQUIRED. So verify against the server — but at most every five
    // minutes, because this runs before every status check and every AJAX poll.
    $verifiedAt = intval($config['gowhatsapp_device_ok_at'] ?? 0);
    if ($configured !== '' && (time() - $verifiedAt) < 300) {
        return $configured;
    }

    $wanted = $configured !== '' ? $configured : 'speedradius';

    // 1. What does the server actually have?
    $resp = gowhatsapp_request('GET', '/app/devices', [], 8);
    $json = json_decode($resp['body'], true);
    $list = (is_array($json) && isset($json['results']) && is_array($json['results'])) ? $json['results'] : [];

    // /app/devices returns a list, but tolerate a single device object.
    if (isset($list['device']) || isset($list['id'])) {
        $list = [$list];
    }

    $have = [];
    foreach ($list as $d) {
        $id = is_array($d) ? ($d['device'] ?? ($d['id'] ?? '')) : (string) $d;
        if ($id !== '') $have[] = $id;
    }

    // 2. Already there? Remember that and stop asking.
    if (in_array($wanted, $have, true)) {
        gowhatsapp_save('gowhatsapp_device_id', $wanted);
        gowhatsapp_save('gowhatsapp_device_ok_at', time());
        return $wanted;
    }

    // 3. Nothing configured but the server has something — adopt the first.
    if ($configured === '' && !empty($have)) {
        gowhatsapp_save('gowhatsapp_device_id', $have[0]);
        gowhatsapp_save('gowhatsapp_device_ok_at', time());
        return $have[0];
    }

    // 4. Create the slot we want.
    $resp = gowhatsapp_request('POST', '/devices', ['device_id' => $wanted], 15, true);
    $json = json_decode($resp['body'], true);
    $created = (is_array($json) && isset($json['results']['id'])) ? $json['results']['id'] : '';
    if ($created !== '') {
        gowhatsapp_save('gowhatsapp_device_id', $created);
        gowhatsapp_save('gowhatsapp_device_ok_at', time());
        return $created;
    }

    // 5. Creation failed — fall back to whatever the server does have.
    if (!empty($have)) {
        gowhatsapp_save('gowhatsapp_device_id', $have[0]);
        gowhatsapp_save('gowhatsapp_device_ok_at', time());
        return $have[0];
    }

    return '';
}

/** GET /app/status — the single source of truth for "can we send yet?". */
function gowhatsapp_status($timeout = 8)
{
    global $config;
    if (empty($config['gowhatsapp_url'])) {
        return ['reachable' => false, 'error' => 'Not configured', 'connected' => false,
                'logged_in' => false, 'jid' => '', 'device_id' => ''];
    }

    // Every device-scoped call below needs a device slot; make sure we have one.
    gowhatsapp_ensure_device();

    $resp = gowhatsapp_request('GET', '/app/status', [], $timeout);
    $json = json_decode($resp['body'], true);
    $ok = ($resp['http_code'] === 200 && is_array($json) && isset($json['results']));
    return [
        'reachable' => $ok,
        'error'     => $ok ? '' : gowhatsapp_err($resp['body'], $resp['http_code'], $resp['error']),
        'connected' => $ok && !empty($json['results']['is_connected']),
        'logged_in' => $ok && !empty($json['results']['is_logged_in']),
        'jid'       => $ok ? ($json['results']['jid'] ?? '') : '',
        'device_id' => $ok ? ($json['results']['device_id'] ?? '') : '',
    ];
}

/** GET /app/info — version and limits, for the dashboard header. */
function gowhatsapp_app_info()
{
    $resp = gowhatsapp_request('GET', '/app/info', [], 6);
    $json = json_decode($resp['body'], true);
    return (is_array($json) && isset($json['results'])) ? $json['results'] : [];
}

/** Absolute URL the browser loads the QR from (our own proxy route). */
function gowhatsapp_qr_url($stamp = 0)
{
    // U already ends in "?_route=", so the cache-buster must be a QUERY
    // parameter appended with "&" — a second "?" would glue "t=123" onto the
    // route name, so the router would look for a function literally named
    // "gowhatsapp_qr_image?t=123", answer "Function not found", and the <img>
    // would receive the dashboard instead of a PNG.
    return U . 'plugin/gowhatsapp_qr_image&t=' . ($stamp ?: time());
}

/**
 * Determine connection state + QR.
 *
 * The QR PNG link is cached in the DB (not the PHP session) so the session lock
 * is never held while waiting on GOWA, and so concurrent AJAX polls don't all
 * hit /app/login at once.
 */
function gowhatsapp_get_qr($force = false)
{
    global $config;
    $now = time();

    // Recently confirmed linked? Trust it briefly to avoid a call every poll.
    if (!$force && !empty($config['gowhatsapp_connected_at']) && ($now - intval($config['gowhatsapp_connected_at'])) < 45) {
        return ['connected' => true, 'qr' => ''];
    }

    $cached   = $config['gowhatsapp_qr_link'] ?? '';
    $cachedAt = intval($config['gowhatsapp_qr_time'] ?? 0);
    $qrTtl    = max(15, intval($config['gowhatsapp_qr_ttl'] ?? 18));

    // Is the device linked? GOWA errors if /app/login runs on a paired device,
    // so this check has to come before we ever ask for a QR.
    $st = gowhatsapp_status(6);
    if ($st['logged_in']) {
        gowhatsapp_save('gowhatsapp_connected_at', $now);
        gowhatsapp_save('gowhatsapp_qr_link', '');
        gowhatsapp_save('gowhatsapp_qr_time', 0);
        return ['connected' => true, 'qr' => '', 'status' => $st];
    }
    if (!$st['reachable']) {
        return ['connected' => false, 'qr' => '', 'error' => $st['error'], 'status' => $st];
    }

    gowhatsapp_save('gowhatsapp_connected_at', 0);

    // Still-valid QR? Serve it without touching the API.
    if (!$force && $cached !== '' && ($now - $cachedAt) < $qrTtl) {
        return ['connected' => false, 'qr' => gowhatsapp_qr_url($cachedAt), 'status' => $st];
    }

    // Stampede guard shared with the AJAX poller.
    $genAt = intval($config['gowhatsapp_qr_gen_at'] ?? 0);
    if (!$force && $genAt && ($now - $genAt) < 10) {
        return [
            'connected'  => false,
            'qr'         => $cached !== '' ? gowhatsapp_qr_url($cachedAt) : '',
            'generating' => true,
            'status'     => $st,
        ];
    }

    gowhatsapp_save('gowhatsapp_qr_gen_at', $now);

    $resp = gowhatsapp_request('GET', '/app/login', [], 40);

    gowhatsapp_save('gowhatsapp_qr_gen_at', 0);

    if (gowhatsapp_ok($resp['body'], $resp['http_code'])) {
        $json = json_decode($resp['body'], true);
        $link = $json['results']['qr_link'] ?? '';
        if ($link !== '') {
            // qr_duration is the QR's own lifetime; keep a 2s safety margin.
            $ttl = max(15, intval($json['results']['qr_duration'] ?? 20) - 2);
            gowhatsapp_save('gowhatsapp_qr_link', $link);
            gowhatsapp_save('gowhatsapp_qr_time', $now);
            gowhatsapp_save('gowhatsapp_qr_ttl', $ttl);
            return ['connected' => false, 'qr' => gowhatsapp_qr_url($now), 'timeout' => $ttl, 'status' => $st];
        }
    }

    return [
        'connected' => false,
        'qr'        => '',
        'error'     => gowhatsapp_err($resp['body'], $resp['http_code'], $resp['error']),
        'status'    => $st,
    ];
}

// ==================== DB HELPERS ====================
function gowhatsapp_save($k, $v)
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

function gowhatsapp_init()
{
    try {
        ORM::raw_execute("CREATE TABLE IF NOT EXISTS `tbl_gowhatsapp_logs` (
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

/** Normalize a Kenyan/international number to digits-only international format. */
function gowhatsapp_format_phone($phone)
{
    $phone = preg_replace('/[^0-9]/', '', (string) $phone);
    if (strlen($phone) === 9 && $phone[0] === '7') {
        $phone = '254' . $phone;
    } elseif (strlen($phone) === 10 && $phone[0] === '0') {
        $phone = '254' . substr($phone, 1);
    }
    return $phone;
}
