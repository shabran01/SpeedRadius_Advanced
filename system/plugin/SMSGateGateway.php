<?php

/**
 * SMS Gate (sms-gate.app) Gateway Plugin for SpeedRadius
 * Sends SMS via Android phone using the sms-gate.app Cloud API
 */

// Include the SMS Lock helper
require_once(__DIR__ . '/../helpers/SMSLock.php');

// Register menu item in admin panel
register_menu("SMS Gate Gateway", true, "smsGatewaySmSGate", 'COMMUNICATION', 'glyphicon glyphicon-phone', '', '', ['Admin', 'SuperAdmin']);

// Register hook for sending SMS
register_hook('send_sms_smsgate', 'smsGatewaySmSGate_hook_send_sms');

function smsGatewaySmSGate()
{
    global $ui, $config, $admin;
    _admin();

    if (empty($config['smsgate_username']) || empty($config['smsgate_password'])) {
        r2(U . 'plugin/smsGatewaySmSGate_config', 'e', 'Please configure SMS Gate first');
    }

    // Get last 10 SMS Gate logs only (not WhatsApp)
    $logs = ORM::for_table('tbl_sms_logs')
        ->where('gateway', 'SMSGate')
        ->order_by_desc('created_at')
        ->limit(10)
        ->find_many();

    $ui->assign('sms_logs', $logs);
    $ui->assign('_title', 'SMS Gate Gateway');
    $ui->assign('_system_menu', 'plugin/smsGatewaySmSGate');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('smsGatewaySmSGate.tpl');
}

function smsGatewaySmSGate_config()
{
    global $ui, $config;
    _admin();

    if (!empty(_post('smsgate_username')) || !empty(_post('smsgate_password'))) {
        $fields = ['smsgate_username', 'smsgate_password', 'smsgate_device_id', 'smsgate_mode', 'smsgate_local_url', 'smsgate_api_url', 'smsgate_cloud_provider'];
        foreach ($fields as $field) {
            $val = trim(_post($field));
            $d = ORM::for_table('tbl_appconfig')->where('setting', $field)->find_one();
            if ($d) {
                $d->value = $val;
                $d->save();
            } else {
                $d = ORM::for_table('tbl_appconfig')->create();
                $d->setting = $field;
                $d->value = $val;
                $d->save();
            }
            $config[$field] = $val;
        }
        r2(U . 'plugin/smsGatewaySmSGate_config', 's', 'Configuration saved successfully');
    }

    $ui->assign('_title', 'SMS Gate Configuration');
    $ui->assign('_system_menu', 'plugin/smsGatewaySmSGate');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('menu', 'config');
    $ui->display('smsGatewaySmSGate.tpl');
}

function smsGatewaySmSGate_test()
{
    global $config;
    header('Content-Type: application/json');
    _admin();

    $phone = trim(_post('phone'));
    $message = trim(_post('message'));

    if (empty($phone) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Phone and message are required']);
        return;
    }

    $result = smsGatewaySmSGate_hook_send_sms([$phone, $message]);
    echo json_encode(['success' => $result, 'message' => $result ? 'SMS sent successfully' : 'Failed to send SMS']);
}

function smsGatewaySmSGate_hook_send_sms($data = [])
{
    global $config;

    if (!is_array($data) || count($data) < 2) {
        _log("SMS Gate: Invalid data format", 'SMS', 0);
        return false;
    }

    list($phone, $message) = $data;

    if (empty($phone) || empty($message)) {
        _log("SMS Gate: Phone or message is empty", 'SMS', 0);
        smsGatewaySmSGate_log($phone, $message, null, 'failed', 'Phone or message is empty');
        return false;
    }

    if (empty($config['smsgate_username']) || empty($config['smsgate_password'])) {
        _log("SMS Gate: Configuration missing", 'SMS', 0);
        smsGatewaySmSGate_log($phone, $message, null, 'failed', 'Configuration missing');
        return false;
    }

    // Clean old locks and check for duplicates
    SMSLock::cleanOldLocks();
    if (!SMSLock::acquireLock($phone, $message)) {
        _log("SMS Gate: Duplicate message blocked for $phone", 'SMS', 1);
        return true;
    }

    $recent = ORM::for_table('tbl_sms_logs')
        ->where('phone', $phone)
        ->where('message', $message)
        ->where_gte('created_at', date('Y-m-d H:i:s', strtotime('-5 minutes')))
        ->find_one();

    if ($recent) {
        SMSLock::releaseLock($phone, $message);
        _log("SMS Gate: Recent duplicate found in logs", 'SMS', 1);
        return true;
    }

    // Build POST body — optionally pin to a specific device
    $phone = smsGatewaySmSGate_phoneFormat($phone);
    $body = [
        'textMessage'  => ['text' => $message],
        'phoneNumbers' => [$phone],
    ];
    if (!empty($config['smsgate_device_id'])) {
        $body['deviceId'] = $config['smsgate_device_id'];
    }

    // Choose URL based on mode: local (internal network) or cloud (external)
    $mode = isset($config['smsgate_mode']) ? $config['smsgate_mode'] : 'cloud';
    if ($mode === 'local' && !empty($config['smsgate_local_url'])) {
        // Local server: http://<phone-ip>:8080/message (official docs)
        $url = rtrim($config['smsgate_local_url'], '/') . '/message';
        $sslVerify = false; // Local server usually doesn't have valid SSL
    } else {
        // Cloud server — determine which provider
        $provider = isset($config['smsgate_cloud_provider']) ? $config['smsgate_cloud_provider'] : 'official';
        
        switch ($provider) {
            case 'private':
                $apiUrl = !empty($config['smsgate_api_url']) 
                    ? $config['smsgate_api_url'] 
                    : 'https://textsms.speedcomwifi.xyz/api/3rdparty/v1/messages';
                $sslVerify = false; // Private server may have self-signed cert
                break;
            case 'custom':
                $apiUrl = $config['smsgate_api_url'] ?? '';
                $sslVerify = false; // Custom server may have self-signed cert
                break;
            case 'official':
            default:
                $apiUrl = 'https://api.sms-gate.app/3rdparty/v1/messages';
                $sslVerify = true;
                break;
        }
        $url = $apiUrl;
        $sslVerify = true;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $sslVerify);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $sslVerify ? 2 : 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERPWD, $config['smsgate_username'] . ':' . $config['smsgate_password']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        _log("SMS Gate CURL Error: " . $curlError, 'SMS', 0);
        smsGatewaySmSGate_log($phone, $message, null, 'failed', 'CURL Error: ' . $curlError);
        SMSLock::releaseLock($phone, $message);
        return false;
    }

    $result = json_decode($response, true);

    // sms-gate.app returns 202 Accepted on success
    if ($httpCode == 202 || $httpCode == 200) {
        $messageId = isset($result['id']) ? $result['id'] : null;
        _log("SMS Gate: Message sent to $phone (ID: $messageId)", 'SMS', 1);
        smsGatewaySmSGate_log($phone, $message, $messageId, 'sent', 'Message sent successfully');
        SMSLock::releaseLock($phone, $message);
        return true;
    } else {
        $errMsg = isset($result['message']) ? $result['message'] : ('HTTP ' . $httpCode . ': ' . $response);
        _log("SMS Gate Error: $errMsg", 'SMS', 0);
        smsGatewaySmSGate_log($phone, $message, null, 'failed', $errMsg);
        SMSLock::releaseLock($phone, $message);
        return false;
    }
}

function smsGatewaySmSGate_log($phone, $message, $message_id, $status, $status_message)
{
    $log = ORM::for_table('tbl_sms_logs')->create();
    $log->phone = $phone;
    $log->message = $message;
    $log->message_id = $message_id;
    $log->gateway = 'SMSGate';
    $log->status = $status;
    $log->status_message = $status_message;
    $log->save();
}

function smsGatewaySmSGate_phoneFormat($phone)
{
    // Strip everything except digits and +
    $phone = preg_replace('/[^0-9+]/', '', $phone);

    // Handle formats: 07XX, 011X, 254XX, +254XX
    if (str_starts_with($phone, '+')) {
        return $phone; // Already E.164 format: +254712345678
    }

    // Remove all leading zeros
    $stripped = ltrim($phone, '0');

    if (str_starts_with($stripped, '254')) {
        return '+' . $stripped;   // 254712345678 → +254712345678
    }

    return '+254' . $stripped;    // 0712345678 / 712345678 → +254712345678
}

function smsGatewaySmSGate_clear()
{
    header('Content-Type: application/json');
    _admin();

    if (!in_array(Admin::_info()['user_type'], ['SuperAdmin', 'Admin'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    $deleted = ORM::for_table('tbl_sms_logs')
        ->where('gateway', 'SMSGate')
        ->delete_many();

    _log("SMS Gate: Cleared " . ($deleted ?: 0) . " SMS logs", 'SMS', 1);
    echo json_encode(['success' => true, 'message' => ($deleted ?: 0) . ' SMS logs cleared']);
}
