<?php

/**
 * M-WA Electron Gateway Plugin for SpeedRadius
 *
 * Talks to the local Express API that the MWA-Electron desktop app exposes
 * (https://github.com/fitri-hy/MWA-Electron), so system notifications
 * (expiry, payment, OTP, vouchers) can be delivered over WhatsApp.
 *
 * READ THIS FIRST — what MWA-Electron actually offers
 * ---------------------------------------------------
 * MWA-Electron is an Electron DESKTOP application. Its "multi-account WhatsApp
 * Web" feature is a set of Electron webviews pointing at web.whatsapp.com and
 * has NO API at all — nothing can send through those tabs programmatically.
 * Only its Baileys-based BOT layer is reachable, via two unauthenticated
 * endpoints on the Express server it starts on PORT (default 3000):
 *
 *   GET  /webhook/sessions       -> {"success":true,"sessions":[{"sessionId":"session-1","status":"connected"}]}
 *   POST /webhook/send-message   -> {"sessionId":"..","number":"2547..","message":".."}
 *                                   {"success":true,"message":"Message sent successfully"}
 *
 * Consequences you must plan for:
 *   1. The desktop app must be RUNNING for anything to send. Close it and all
 *      notifications fail. This is not a service.
 *   2. Bot sessions are started by hand in M-WA -> Bot -> Connect. After the
 *      app restarts, the session reports "not_connected" until someone presses
 *      Connect again.
 *   3. /webhook/* has no authentication. Never expose M-WA's port to the
 *      internet without putting an authenticating proxy in front of it.
 *
 * For unattended billing notifications a server-hosted gateway (GoWhatsApp /
 * GOWA) is the better primary. This plugin is designed to sit alongside it.
 */

if (!defined('MWA_PLUGIN_VERSION')) {
    define('MWA_PLUGIN_VERSION', '1.0.0');
}

// ==================== MENU + HOOK REGISTRATION ====================
register_menu("M-WA Electron", true, "mwa_electron", 'COMMUNICATION', 'fa fa-whatsapp', '', '', ['Admin', 'SuperAdmin']);
register_menu("M-WA Electron Logs", true, "mwa_electron_logs", 'COMMUNICATION', 'fa fa-list-alt', '', '', ['Admin', 'SuperAdmin']);

// Internal routes: AJAX + form posts, never shown in the menu.
register_menu("M-WA Sessions", false, "mwa_electron_sessions", '', '', '', '', []);
register_menu("M-WA Quick Send", false, "mwa_electron_quick_send", '', '', '', '', []);

register_hook('send_whatsapp_mwa', 'mwa_electron_hook_send');

// ==================== DASHBOARD ====================
function mwa_electron()
{
    global $ui, $config, $admin;
    _admin();

    if (empty($config['mwa_url'])) {
        r2(U . 'plugin/mwa_electron_config', 'e', 'Please configure M-WA Electron first');
    }

    mwa_electron_init();

    $session = mwa_electron_session_id();
    $sessions = mwa_electron_sessions_list(7);
    $reachable = ($sessions !== null);

    // Is the session we actually send through connected right now?
    $status = 'unknown';
    if ($reachable) {
        $found = mwa_electron_find_session($sessions, $session);
        if ($found !== null) {
            $status = $found['status'];
        } else {
            $status = ($session === '') ? 'not_set' : 'missing';
        }
    }

    $todaySent = ORM::for_table('tbl_mwa_logs')
        ->where('status', 'delivered')
        ->where_gte('created_at', date('Y-m-d 00:00:00'))
        ->count();
    $totalSent = ORM::for_table('tbl_mwa_logs')->where('status', 'delivered')->count();
    $totalFailed = ORM::for_table('tbl_mwa_logs')->where('status', 'failed')->count();

    $recent = ORM::for_table('tbl_mwa_logs')->order_by_desc('id')->limit(8)->find_many();

    $ui->assign('gwReachable', $reachable);
    $ui->assign('gwSessions', $reachable ? $sessions : []);
    $ui->assign('gwStatus', $status);
    $ui->assign('gwSession', $session);
    $ui->assign('gwUrl', $config['mwa_url'] ?? '');
    $ui->assign('gwEnabled', ($config['mwa_enabled'] ?? '') === 'yes');
    $ui->assign('gwPluginVersion', MWA_PLUGIN_VERSION);
    $ui->assign('gwError', $reachable ? '' : mwa_electron_last_error_text());
    $ui->assign('todaySent', $todaySent);
    $ui->assign('totalSent', $totalSent);
    $ui->assign('totalFailed', $totalFailed);
    $ui->assign('recent', $recent);
    $ui->assign('_title', 'M-WA Electron');
    $ui->assign('_system_menu', 'mwa_electron');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('mwaElectron.tpl');
}

// ==================== CONFIG ====================
function mwa_electron_config()
{
    global $ui, $config, $admin;
    _admin();

    if (_post('save')) {
        $url = rtrim(trim((string) _post('mwa_url', '')), '/');

        if ($url !== '' && !preg_match('#^https?://#i', $url)) {
            r2(U . 'plugin/mwa_electron_config', 'e', 'The base URL must start with http:// or https:// (for example http://127.0.0.1:3000)');
        }

        $picked = trim((string) _post('mwa_session_id', ''));

        mwa_electron_save('mwa_url', $url);
        mwa_electron_save('mwa_session_id', $picked);
        mwa_electron_save('mwa_country_code', preg_replace('/[^0-9]/', '', (string) _post('mwa_country_code', '254')));
        mwa_electron_save('mwa_header_name', trim((string) _post('mwa_header_name', '')));
        mwa_electron_save('mwa_header_value', trim((string) _post('mwa_header_value', '')));
        mwa_electron_save('mwa_signature', trim((string) _post('mwa_signature', '')));
        mwa_electron_save('mwa_timeout', max(5, intval(_post('mwa_timeout', 20))));
        mwa_electron_save('mwa_enabled', _post('mwa_enabled', 'no') === 'yes' ? 'yes' : 'no');

        if ($url === '') {
            r2(U . 'plugin/mwa_electron_config', 'e', 'Base URL is required.');
        }

        if ($picked === '') {
            r2(U . 'plugin/mwa_electron_config', 'e', 'Saved, but no bot session is selected. Pick one before enabling the gateway.');
        }

        $sessions = mwa_electron_sessions_list(8);
        if ($sessions === null) {
            r2(U . 'plugin/mwa_electron_config', 'e', 'Saved, but M-WA could not be reached: ' . mwa_electron_last_error_text());
        }

        $st = mwa_electron_find_session($sessions, $picked);
        if ($st === null) {
            r2(U . 'plugin/mwa_electron_config', 'e', 'Saved, but the session "' . $picked . '" does not exist on M-WA. Open M-WA -> Bot and create it, or pick one from the list.');
        }

        r2(
            U . 'plugin/mwa_electron',
            $st['status'] === 'connected' ? 's' : 'e',
            $st['status'] === 'connected'
                ? 'Configuration saved and session "' . $picked . '" is connected.'
                : 'Configuration saved, but session "' . $picked . '" is not connected. Open M-WA -> Bot and press Connect.'
        );
    }

    if (empty($config['mwa_url'])) {
        $config['mwa_url'] = 'http://127.0.0.1:3000';
    }

    $sessions = mwa_electron_sessions_list(8);

    $ui->assign('gw_url', $config['mwa_url'] ?? '');
    $ui->assign('gw_session_id', $config['mwa_session_id'] ?? '');
    $ui->assign('gw_country_code', $config['mwa_country_code'] ?? '254');
    $ui->assign('gw_header_name', $config['mwa_header_name'] ?? '');
    $ui->assign('gw_header_value', $config['mwa_header_value'] ?? '');
    $ui->assign('gw_signature', $config['mwa_signature'] ?? '');
    $ui->assign('gw_timeout', intval($config['mwa_timeout'] ?? 20));
    $ui->assign('gw_enabled', ($config['mwa_enabled'] ?? '') === 'yes');
    $ui->assign('gwPluginVersion', MWA_PLUGIN_VERSION);
    $ui->assign('gwSessions', $sessions === null ? [] : $sessions);
    $ui->assign('gwReachable', $sessions !== null);
    $ui->assign('gwError', $sessions === null ? mwa_electron_last_error_text() : '');
    $ui->assign('_title', 'M-WA Electron Configuration');
    $ui->assign('_system_menu', 'mwa_electron');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('menu', 'config');
    $ui->display('mwaElectron_config.tpl');
}

// ==================== LOGS ====================
function mwa_electron_logs()
{
    global $ui, $admin;
    _admin();
    mwa_electron_init();

    if (isset($_POST['delete_all'])) {
        ORM::for_table('tbl_mwa_logs')->delete_many();
        r2(U . 'plugin/mwa_electron_logs', 's', 'All M-WA logs deleted');
    }

    $page = max(1, intval(_get('page', 1)));
    $search = (string) _get('search', '');

    // Built twice on purpose: reusing one Idiorm object after count() relies on
    // internal state that is not documented, and a lost WHERE clause here would
    // silently page through every row.
    $filtered = function () use ($search) {
        $q = ORM::for_table('tbl_mwa_logs');
        if ($search !== '') {
            $q->where_raw('(phone_number LIKE ? OR message LIKE ? OR response LIKE ?)', ["%$search%", "%$search%", "%$search%"]);
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
    $ui->assign('_title', 'M-WA Electron Logs');
    $ui->assign('_system_menu', 'mwa_electron_logs');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('mwaElectron_logs.tpl');
}

// ==================== AJAX: SESSION LIST ====================
function mwa_electron_sessions()
{
    _admin();
    if (function_exists('session_write_close')) {
        session_write_close();
    }
    header('Content-Type: application/json');

    $sessions = mwa_electron_sessions_list(8);
    if ($sessions === null) {
        echo json_encode(['success' => false, 'error' => mwa_electron_last_error_text(), 'sessions' => []]);
        exit;
    }
    echo json_encode(['success' => true, 'sessions' => $sessions]);
    exit;
}

// ==================== AJAX: QUICK SEND ====================
function mwa_electron_quick_send()
{
    _admin();
    if (function_exists('session_write_close')) {
        session_write_close();
    }
    header('Content-Type: application/json');

    $phone = _req('to', '');
    $text  = _req('message', '');

    if ($phone === '' || $text === '') {
        echo json_encode(['success' => false, 'error' => 'Phone and message are required']);
        exit;
    }

    $result = mwa_electron_send_message($phone, $text);
    echo json_encode([
        'success' => ($result['code'] ?? '') === 'SUCCESS',
        'error'   => ($result['code'] ?? '') === 'SUCCESS' ? '' : ($result['message'] ?? 'Unknown error'),
    ]);
    exit;
}

// ==================== HOOK SEND ====================
// Fired from Message::sendWhatsapp() when this gateway is enabled, and available
// as its own action for cron jobs that want to target M-WA specifically.
function mwa_electron_hook_send($data = [])
{
    global $config;
    $phone = is_array($data) ? ($data[0] ?? '') : $data;
    $txt   = is_array($data) ? ($data[1] ?? '') : '';
    if (empty($phone) || empty($txt)) return false;
    if (($config['mwa_enabled'] ?? '') !== 'yes') return false;
    if (empty($config['mwa_url'])) return false;

    $result = mwa_electron_send_message($phone, $txt);
    return ($result['code'] ?? '') === 'SUCCESS';
}

// ==================== CORE: SEND MESSAGE ====================
function mwa_electron_send_message($phone, $txt)
{
    global $config;

    if (empty($config['mwa_url'])) {
        return ['code' => 'ERROR', 'message' => 'M-WA is not configured'];
    }

    $session = mwa_electron_session_id();
    if ($session === '') {
        return ['code' => 'ERROR', 'message' => 'No M-WA bot session is selected in the plugin config'];
    }

    $msisdn = mwa_electron_format_phone($phone);
    if ($msisdn === '') {
        return ['code' => 'ERROR', 'message' => 'Invalid phone number'];
    }

    // Optional signature line, so recipients can tell automated notices apart
    // from messages a person typed by hand.
    $sig = trim((string) ($config['mwa_signature'] ?? ''));
    $body = ($sig !== '') ? $txt . "\n\n" . $sig : $txt;

    mwa_electron_init();

    // Dedup: same number + same message within 10s.
    $recent = ORM::for_table('tbl_mwa_logs')
        ->where('phone_number', $msisdn)
        ->where('message', $txt)
        ->where_gte('created_at', date('Y-m-d H:i:s', strtotime('-10s')))
        ->find_one();
    if ($recent) {
        return ['code' => 'DUPLICATE', 'message' => 'Duplicate message within 10s'];
    }

    $log = ORM::for_table('tbl_mwa_logs')->create();
    $log->phone_number = $msisdn;
    $log->message = $txt;
    $log->status = 'sent';
    $log->created_at = date('Y-m-d H:i:s');
    $log->save();

    // M-WA's sendMessageBySessionId() appends @s.whatsapp.net itself, so the
    // number must be plain digits with no plus sign.
    $resp = mwa_electron_request('POST', '/webhook/send-message', [
        'sessionId' => $session,
        'number'    => $msisdn,
        'message'   => $body,
    ], max(5, intval($config['mwa_timeout'] ?? 20)), true);

    if (mwa_electron_ok($resp['body'], $resp['http_code'])) {
        $log->status = 'delivered';
        $log->response = substr((string) $resp['body'], 0, 500);
        $log->save();
        return ['code' => 'SUCCESS', 'message' => 'Message sent'];
    }

    $err = mwa_electron_err($resp['body'], $resp['http_code'], $resp['error']);
    $log->status = 'failed';
    $log->response = substr($err, 0, 500);
    $log->save();
    return ['code' => 'ERROR', 'message' => $err];
}

// ==================== M-WA API HELPERS ====================

function mwa_electron_session_id()
{
    global $config;
    return trim((string) ($config['mwa_session_id'] ?? ''));
}

/**
 * GET /webhook/sessions.
 *
 * @return array|null  list of ['sessionId' => string, 'status' => string], or
 *                     null when M-WA could not be reached (the reason is kept for
 *                     the caller to display — the app is a desktop process, so
 *                     "not reachable" is a normal, expected state).
 */
function mwa_electron_sessions_list($timeout = 8)
{
    // Clear the previous reason so a stale message is never shown for a check
    // that has not happened yet.
    mwa_electron_last_error('');

    $resp = mwa_electron_request('GET', '/webhook/sessions', [], $timeout);

    $json = json_decode((string) $resp['body'], true);
    if (!is_array($json) || empty($json['success']) || !isset($json['sessions']) || !is_array($json['sessions'])) {
        mwa_electron_last_error(mwa_electron_err($resp['body'], $resp['http_code'], $resp['error']));
        return null;
    }

    $out = [];
    foreach ($json['sessions'] as $s) {
        if (!is_array($s)) continue;
        $id = isset($s['sessionId']) ? (string) $s['sessionId'] : '';
        if ($id === '') continue;
        $out[] = [
            'sessionId' => $id,
            'status'    => isset($s['status']) ? (string) $s['status'] : 'unknown',
        ];
    }
    return $out;
}

/** Find one session entry by id, or null. */
function mwa_electron_find_session($sessions, $id)
{
    if (!is_array($sessions)) return null;
    foreach ($sessions as $s) {
        if (($s['sessionId'] ?? '') === $id) return $s;
    }
    return null;
}

/**
 * Remember / read why the last reachability check failed.
 *
 * Uses $GLOBALS rather than a `global $x` declaration: PHP's global keyword does
 * not create the variable in the including scope, and these plugin files are
 * included at top level, so a bare global would be declared and then wiped.
 *
 * @param string|null $msg  null reads, a string writes.
 */
function mwa_electron_last_error($msg = null)
{
    if ($msg !== null) {
        $GLOBALS['mwa_electron_last_error'] = (string) $msg;
    }
    return isset($GLOBALS['mwa_electron_last_error']) ? (string) $GLOBALS['mwa_electron_last_error'] : '';
}

/** Display form of the last error: never empty, so the UI always says something. */
function mwa_electron_last_error_text()
{
    $e = mwa_electron_last_error();
    return $e !== '' ? $e : 'M-WA could not be reached';
}

/** M-WA's Express API signals success with {"success":true}. */
function mwa_electron_ok($body, $httpCode)
{
    if ($httpCode < 200 || $httpCode >= 300) return false;
    $j = json_decode((string) $body, true);
    return is_array($j) && !empty($j['success']);
}

/** Turn an M-WA failure into something an operator can act on. */
function mwa_electron_err($body, $httpCode, $curlError = '')
{
    $j = json_decode((string) $body, true);

    if (is_array($j) && !empty($j['error'])) {
        $e = trim((string) $j['error']);
        if (stripos($e, 'Session not found or not yet connected') !== false) {
            return 'M-WA says the bot session "' . mwa_electron_session_id() . '" is not connected. '
                 . 'Open M-WA -> Bot and press Connect on that session, then try again.';
        }
        return $e;
    }

    if ($httpCode === 0) {
        // The single most common cause: the desktop app is simply not running.
        return $curlError !== ''
            ? 'Cannot reach M-WA (' . $curlError . '). Is the desktop app running on the machine at this address?'
            : 'Cannot reach M-WA. Is the desktop app running?';
    }
    if ($httpCode === 401 || $httpCode === 403) {
        return 'The M-WA endpoint refused the request (HTTP ' . $httpCode . '). Check the auth header settings.';
    }
    if ($httpCode === 404) {
        return 'HTTP 404 from M-WA. Check that the base URL has the right port (default 3000) and no extra path.';
    }

    $t = trim((string) $body);
    if ($t === '') return 'HTTP ' . $httpCode . ' — empty response';
    if (stripos($t, '<html') !== false || stripos($t, '<!doctype') !== false) {
        return 'HTTP ' . $httpCode . ' — the server returned an HTML page, not the M-WA API. Is the base URL pointing at the Express server?';
    }
    return strlen($t) > 300 ? substr($t, 0, 300) . '...' : $t;
}

/**
 * Low-level cURL call against M-WA's Express server.
 *
 * Optional mwa_header_name/value are sent on every request. M-WA itself does not
 * read them, but a reverse proxy placed in front of it can, which is how you add
 * authentication to an otherwise unauthenticated API.
 */
function mwa_electron_request($method, $path, $fields = [], $timeout = 20, $json = false)
{
    global $config;

    $base = rtrim(trim((string) ($config['mwa_url'] ?? '')), '/');
    if ($base === '') {
        return ['http_code' => 0, 'body' => '', 'error' => 'M-WA base URL is not configured'];
    }

    $url = $base . '/' . ltrim($path, '/');

    $headers = ['Accept: application/json'];
    $hName = trim((string) ($config['mwa_header_name'] ?? ''));
    if ($hName !== '') {
        $headers[] = $hName . ': ' . trim((string) ($config['mwa_header_value'] ?? ''));
    }

    if ($method === 'GET' && !empty($fields)) {
        $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($fields);
    }

    $opts = [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_CONNECTTIMEOUT => 6,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
    ];

    if ($method === 'POST') {
        $opts[CURLOPT_POST] = true;
        if ($json) {
            // M-WA parses bodies with express.json(), so this must be JSON with
            // an explicit Content-Type or req.body arrives empty.
            $headers[] = 'Content-Type: application/json';
            $opts[CURLOPT_HTTPHEADER] = $headers;
            $opts[CURLOPT_POSTFIELDS] = json_encode($fields);
        } elseif (!empty($fields)) {
            $opts[CURLOPT_POSTFIELDS] = http_build_query($fields);
        }
    } elseif ($method !== 'GET') {
        $opts[CURLOPT_CUSTOMREQUEST] = $method;
        if (!empty($fields)) {
            $opts[CURLOPT_POSTFIELDS] = $json ? json_encode($fields) : http_build_query($fields);
        }
    }

    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    return [
        'http_code' => $code,
        'body'      => ($body === false) ? '' : (string) $body,
        'error'     => (string) $err,
    ];
}

// ==================== UTILITIES ====================

/**
 * Normalize any local/international spelling to plain international digits.
 *
 * M-WA builds the JID itself as "<number>@s.whatsapp.net", so a leading plus or
 * a leading trunk zero produces a JID that silently never resolves.
 */
function mwa_electron_format_phone($phone)
{
    global $config;

    $digits = preg_replace('/[^0-9]/', '', (string) $phone);
    if ($digits === '') return '';

    $cc = preg_replace('/[^0-9]/', '', (string) ($config['mwa_country_code'] ?? '254'));

    // Local trunk form (0712345678) — drop the 0, prepend the country code.
    if ($digits[0] === '0') {
        return $cc . substr($digits, 1);
    }
    // Already international without the plus (254712345678).
    if ($cc !== '' && strpos($digits, $cc) === 0) {
        return $digits;
    }
    // Bare national number (712345678).
    return $cc . $digits;
}

function mwa_electron_save($k, $v)
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

function mwa_electron_init()
{
    try {
        ORM::raw_execute("CREATE TABLE IF NOT EXISTS `tbl_mwa_logs` (
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
