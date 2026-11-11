<?php

/**
 * TalkSasa SMS Gateway Plugin for SpeedRadius (PHPNuxBill)
 *
 * API v3 — https://bulksms.talksasa.com/api/v3
 *   POST /api/v3/sms/send   { recipient, sender_id, type, message[, schedule_time][, dlt_template_id] }
 *   GET  /api/v3/sms        list messages
 *   GET  /api/v3/balance    remaining balance
 *
 * Auth: Authorization: Bearer <api_token>, Accept: application/json,
 *       Content-Type: application/json.
 *
 * Responses: {"status":"success","data":...} or {"status":"error","message":"..."}
 * The list endpoint nests rows as data.data[].
 */

if (!defined('TALKSASA_PLUGIN_VERSION')) {
    define('TALKSASA_PLUGIN_VERSION', '3.0.0');
}

define('TALKSASA_API_BASE', 'https://bulksms.talksasa.com/api/v3');

// Include the SMS Lock helper
require_once(__DIR__ . '/../helpers/SMSLock.php');

register_menu("TalkSasa SMS Gateway", true, "smsGatewayTalkSasa", 'COMMUNICATION', 'glyphicon glyphicon-envelope', '', '', ['Admin', 'SuperAdmin']);

register_hook('send_sms_talksasa', 'smsGatewayTalkSasa_hook_send_sms');

// ==================== CONFIG UI ====================

function smsGatewayTalkSasa()
{
    global $ui, $config, $admin;
    _admin();

    if (empty($config['talksasa_api_token'])) {
        r2(U . 'plugin/smsGatewayTalkSasa_config', 'e', 'Please configure SMS gateway first');
    }

    $logs = ORM::for_table('tbl_sms_logs')
        ->order_by_desc('created_at')
        ->limit(10)
        ->find_many();

    $ui->assign('sms_logs', $logs);
    $ui->assign('talksasa_version', TALKSASA_PLUGIN_VERSION);
    $ui->assign('_title', 'SMS Gateway');
    $ui->assign('_system_menu', 'plugin/smsGatewayTalkSasa');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('smsGatewayTalkSasa.tpl');
}

function smsGatewayTalkSasa_config()
{
    global $ui;
    _admin();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach (['talksasa_api_token', 'talksasa_sender_id', 'talksasa_dlt_template_id'] as $key) {
            $val = trim((string) _post($key, ''));
            $d = ORM::for_table('tbl_appconfig')->where('setting', $key)->find_one();
            if ($d) {
                $d->value = $val;
                $d->save();
            } else {
                $d = ORM::for_table('tbl_appconfig')->create();
                $d->setting = $key;
                $d->value = $val;
                $d->save();
            }
        }
        r2(U . 'plugin/smsGatewayTalkSasa_config', 's', 'Configuration saved successfully');
    }

    $ui->assign('talksasa_version', TALKSASA_PLUGIN_VERSION);
    $ui->assign('_title', 'SMS Gateway Configuration');
    $ui->assign('_system_menu', 'plugin/smsGatewayTalkSasa');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('menu', 'config');
    $ui->display('smsGatewayTalkSasa.tpl');
}

// ==================== SENDING ====================

function smsGatewayTalkSasa_hook_send_sms($data = [])
{
    global $config;

    if (!is_array($data) || count($data) < 2) {
        _log("SMS Gateway: Invalid data format", 'SMS', 0);
        return false;
    }

    list($phone, $message) = $data;

    if (empty($phone) || empty($message)) {
        _log("SMS Gateway: Phone or message is empty", 'SMS', 0);
        smsGatewayTalkSasa_log($phone, $message, null, 'failed', 'Phone or message is empty');
        return false;
    }

    SMSLock::cleanOldLocks();

    if (!SMSLock::acquireLock($phone, $message)) {
        _log("SMS Gateway TalkSasa: Duplicate message blocked for $phone", 'SMS', 1);
        return true;
    }

    $recent = ORM::for_table('tbl_sms_logs')
        ->where('phone', $phone)
        ->where('message', $message)
        ->where_gte('created_at', date('Y-m-d H:i:s', strtotime('-5 minutes')))
        ->find_one();

    if ($recent) {
        SMSLock::releaseLock($phone, $message);
        _log("SMS Gateway TalkSasa: Recent duplicate message found in logs", 'SMS', 1);
        return true;
    }

    if (empty($config['talksasa_api_token']) || empty($config['talksasa_sender_id'])) {
        _log("SMS Gateway: Configuration missing", 'SMS', 0);
        smsGatewayTalkSasa_log($phone, $message, null, 'failed', 'Configuration missing (API token / Sender ID)');
        SMSLock::releaseLock($phone, $message);
        return false;
    }

    $recipient = smsGatewayTalkSasa_phoneFormat($phone);

    $payload = [
        'recipient' => $recipient,
        'sender_id' => $config['talksasa_sender_id'],
        'type'      => 'plain',
        'message'   => $message,
    ];
    if (!empty($config['talksasa_dlt_template_id'])) {
        $payload['dlt_template_id'] = $config['talksasa_dlt_template_id'];
    }

    $res = smsGatewayTalkSasa_api('POST', '/sms/send', $payload);
    $json = $res['json'];

    if ($res['error'] !== '') {
        _log("SMS Gateway CURL Error: " . $res['error'], 'SMS', 0);
        smsGatewayTalkSasa_log($recipient, $message, null, 'failed', 'CURL Error: ' . $res['error']);
        SMSLock::releaseLock($phone, $message);
        return false;
    }

    $ok = $res['code'] >= 200 && $res['code'] < 300
        && is_array($json) && (($json['status'] ?? '') === 'success');

    if ($ok) {
        $uid = smsGatewayTalkSasa_response_id($json['data'] ?? null);
        _log("SMS sent successfully to $recipient", 'SMS', 1);
        smsGatewayTalkSasa_log($recipient, $message, $uid, 'sent', 'Message sent successfully');
        SMSLock::releaseLock($phone, $message);
        return true;
    }

    $err = smsGatewayTalkSasa_error($res);
    _log("SMS Gateway Error: $err", 'SMS', 0);
    smsGatewayTalkSasa_log($recipient, $message, null, 'failed', $err);
    SMSLock::releaseLock($phone, $message);
    return false;
}

/** Pull a usable error message out of a v3 response. */
function smsGatewayTalkSasa_error($res)
{
    $json = $res['json'];
    if (is_array($json)) {
        if (!empty($json['message'])) {
            $m = $json['message'];
            if (is_array($m)) $m = implode('; ', array_filter(array_map('strval', $m)));
            if (trim((string) $m) !== '') return trim((string) $m);
        }
        if (isset($json['status']) && $json['status'] === 'error') {
            return 'API returned status=error (HTTP ' . $res['code'] . ')';
        }
    }
    $body = trim((string) $res['body']);
    if ($body === '') return 'HTTP ' . $res['code'] . ' — empty response';
    return 'HTTP ' . $res['code'] . ' — ' . (strlen($body) > 300 ? substr($body, 0, 300) . '...' : $body);
}

/** Extract a message identifier from the many shapes `data` can take. */
function smsGatewayTalkSasa_response_id($data)
{
    if (is_array($data)) {
        foreach (['uid', 'message_id', 'id'] as $k) {
            if (isset($data[$k]) && !is_array($data[$k])) return $data[$k];
        }
        if (isset($data['data']) && is_array($data['data'])) {
            $first = reset($data['data']);
            if (is_array($first)) {
                foreach (['uid', 'message_id', 'id'] as $k) {
                    if (isset($first[$k]) && !is_array($first[$k])) return $first[$k];
                }
            }
        }
        return null;
    }
    return is_string($data) && $data !== '' ? $data : null;
}

// ==================== API CALLS ====================

/**
 * Low-level v3 request. Returns ['code','body','json','error'].
 */
function smsGatewayTalkSasa_api($method, $path, $payload = null, $timeout = 30)
{
    global $config;

    $token = trim((string) ($config['talksasa_api_token'] ?? ''));
    if ($token === '') {
        return ['code' => 0, 'body' => '', 'json' => null, 'error' => 'API token not configured'];
    }

    $url = TALKSASA_API_BASE . $path;
    $headers = [
        'Authorization: Bearer ' . $token,
        'Accept: application/json',
    ];

    $ch = curl_init();
    $opts = [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_CONNECTTIMEOUT => 10,
    ];

    if ($method === 'POST') {
        $opts[CURLOPT_POST] = true;
        $opts[CURLOPT_POSTFIELDS] = json_encode($payload ?? []);
        $headers[] = 'Content-Type: application/json';
    } elseif ($method !== 'GET') {
        $opts[CURLOPT_CUSTOMREQUEST] = $method;
    }

    $opts[CURLOPT_HTTPHEADER] = $headers;
    curl_setopt_array($ch, $opts);

    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    return [
        'code'  => $code,
        'body'  => $body === false ? '' : $body,
        'json'  => json_decode((string) $body, true),
        'error' => $err,
    ];
}

// ==================== AJAX: BALANCE / MESSAGES ====================

function smsGatewayTalkSasa_check_balance()
{
    header('Content-Type: application/json');
    _admin();

    $res = smsGatewayTalkSasa_api('GET', '/balance', null, 15);

    if ($res['error'] !== '') {
        echo json_encode(['success' => false, 'message' => 'CURL Error: ' . $res['error']]);
        exit;
    }

    $json = $res['json'];
    if ($res['code'] >= 200 && $res['code'] < 300 && is_array($json) && ($json['status'] ?? '') === 'success') {
        echo json_encode(['success' => true, 'data' => $json['data'] ?? null]);
    } else {
        echo json_encode(['success' => false, 'message' => smsGatewayTalkSasa_error($res)]);
    }
    exit;
}

function smsGatewayTalkSasa_messages()
{
    header('Content-Type: application/json');
    _admin();

    $res = smsGatewayTalkSasa_api('GET', '/sms', null, 20);

    if ($res['error'] !== '') {
        echo json_encode(['success' => false, 'message' => 'CURL Error: ' . $res['error']]);
        exit;
    }

    $json = $res['json'];
    if ($res['code'] >= 200 && $res['code'] < 300 && is_array($json) && ($json['status'] ?? '') === 'success') {
        $data = $json['data'] ?? [];
        // The list endpoint nests rows: {"data":{"data":[...]}}
        if (is_array($data) && isset($data['data']) && is_array($data['data'])) {
            $data = $data['data'];
        }
        if (!is_array($data)) $data = [];
        echo json_encode(['success' => true, 'data' => array_values($data)]);
    } else {
        echo json_encode(['success' => false, 'message' => smsGatewayTalkSasa_error($res)]);
    }
    exit;
}

// ==================== HELPERS ====================

function smsGatewayTalkSasa_log($phone, $message, $message_id, $status, $status_message)
{
    try {
        $log = ORM::for_table('tbl_sms_logs')->create();
        $log->phone = $phone;
        $log->message = $message;
        $log->message_id = $message_id;
        $log->status = $status;
        $log->status_message = $status_message;
        $log->save();
    } catch (Throwable $e) {
        _log("TalkSasa log insert failed: " . $e->getMessage(), 'SMS', 0);
    }
}

/**
 * Normalise to international digits. Comma-separated lists are formatted
 * element by element, since v3 accepts multiple recipients in one field.
 */
function smsGatewayTalkSasa_phoneFormat($phone)
{
    $parts = preg_split('/\s*,\s*/', (string) $phone);
    $out = [];
    foreach ($parts as $p) {
        $p = preg_replace('/[^0-9]/', '', $p);
        if ($p === '') continue;
        if (strlen($p) === 9 && $p[0] === '7') {
            $p = '254' . $p;
        } elseif (substr($p, 0, 1) === '0') {
            $p = '254' . substr($p, 1);
        } elseif (substr($p, 0, 3) !== '254') {
            $p = '254' . $p;
        }
        $out[] = $p;
    }
    return implode(',', $out);
}
