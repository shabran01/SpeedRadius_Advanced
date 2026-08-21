<?php

/**
 * Texin SMS Gateway Plugin for SpeedRadius
 * Sends SMS via the Texin API (https://texin.co.ke)
 */

// Include the SMS Lock helper
require_once(__DIR__ . '/../helpers/SMSLock.php');

// Register menu item in admin panel
register_menu("Texin SMS Gateway", true, "smsGatewayTexin", 'COMMUNICATION', 'glyphicon glyphicon-phone', '', '', ['Admin', 'SuperAdmin']);

// Register hook for sending SMS (allows direct hook calls if ever needed)
register_hook('send_sms_texin', 'smsGatewayTexin_hook_send_sms');

function smsGatewayTexin()
{
    global $ui, $config, $admin;
    _admin();

    if (empty($config['texin_api_key'])) {
        r2(U . 'plugin/smsGatewayTexin_config', 'e', 'Please configure Texin SMS first');
    }

    // Fetch balance from Texin API
    $balance = null;
    $balance_error = null;
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://sms.texin.co.ke/api/get_balance',
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode(['api_key' => $config['texin_api_key']]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);
    if ($resp) {
        $bdata = json_decode($resp, true);
        if (!empty($bdata['success'])) {
            $balance = $bdata['sms_balance'];
        } else {
            $balance_error = isset($bdata['message']) ? $bdata['message'] : 'Could not fetch balance';
        }
    }

    // Get last 10 SMS logs for this gateway
    $logs = ORM::for_table('tbl_sms_logs')
        ->where('gateway', 'Texin')
        ->order_by_desc('created_at')
        ->limit(10)
        ->find_many();

    $ui->assign('texin_balance', $balance);
    $ui->assign('texin_balance_error', $balance_error);
    $ui->assign('sms_logs', $logs);
    $ui->assign('_title', 'Texin SMS Gateway');
    $ui->assign('_system_menu', 'plugin/smsGatewayTexin');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('smsGatewayTexin.tpl');
}

function smsGatewayTexin_config()
{
    global $ui, $config;
    _admin();

    if (!empty(_post('texin_api_key'))) {
        $fields = ['texin_api_key', 'texin_sender_id'];
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
        r2(U . 'plugin/smsGatewayTexin_config', 's', 'Configuration saved successfully');
    }

    $ui->assign('_title', 'Texin SMS Configuration');
    $ui->assign('_system_menu', 'plugin/smsGatewayTexin');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('menu', 'config');
    $ui->display('smsGatewayTexin.tpl');
}

function smsGatewayTexin_test()
{
    global $config;
    header('Content-Type: application/json');
    _admin();

    $phone   = trim(_post('phone'));
    $message = trim(_post('message'));

    if (empty($phone) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Phone and message are required']);
        return;
    }

    if (empty($config['texin_api_key'])) {
        echo json_encode(['success' => false, 'message' => 'Texin API key not configured. Go to Communication → Texin SMS Gateway → Config']);
        return;
    }

    // Skip SMSLock for test messages — allow repeated tests
    $phone_formatted = _texin_phone_format($phone);
    $result = _texin_send($phone_formatted, $message);
    
    // Log the test
    _texin_log($phone, $message, null, $result ? 'sent' : 'failed', $result ? 'Test OK' : 'Test failed');
    
    echo json_encode([
        'success' => $result,
        'message' => $result ? 'SMS sent successfully to ' . $phone_formatted : 'Failed to send SMS. Check Texin API key and balance.',
    ]);
}

function smsGatewayTexin_hook_send_sms($data = [])
{
    global $config;

    if (!is_array($data) || count($data) < 2) {
        _log("Texin SMS: Invalid data format", 'SMS', 0);
        return false;
    }

    list($phone, $message) = $data;

    if (empty($phone) || empty($message)) {
        _log("Texin SMS: Phone or message is empty", 'SMS', 0);
        _texin_log($phone, $message, null, 'failed', 'Phone or message is empty');
        return false;
    }

    if (empty($config['texin_api_key'])) {
        _log("Texin SMS: API key not configured", 'SMS', 0);
        _texin_log($phone, $message, null, 'failed', 'API key not configured');
        return false;
    }

    // Duplicate-send protection
    SMSLock::cleanOldLocks();
    if (!SMSLock::acquireLock($phone, $message)) {
        _log("Texin SMS: Duplicate message blocked for $phone", 'SMS', 1);
        return true;
    }

    $recent = ORM::for_table('tbl_sms_logs')
        ->where('gateway', 'Texin')
        ->where('phone', $phone)
        ->where('message', $message)
        ->where_gte('created_at', date('Y-m-d H:i:s', strtotime('-5 minutes')))
        ->find_one();

    if ($recent) {
        SMSLock::releaseLock($phone, $message);
        _log("Texin SMS: Recent duplicate found in logs for $phone", 'SMS', 1);
        return true;
    }

    $phone_formatted = _texin_phone_format($phone);

    $result = _texin_send($phone_formatted, $message);

    SMSLock::releaseLock($phone, $message);

    return $result;
}

/**
 * Send a single SMS via the Texin API.
 * Returns true on success, false on failure.
 */
function _texin_send($phone, $message)
{
    global $config;

    $payload = [
        'api_key'   => $config['texin_api_key'],
        'recipient' => $phone,
        'message'   => $message,
    ];

    if (!empty($config['texin_sender_id'])) {
        $payload['sender_id'] = $config['texin_sender_id'];
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://sms.texin.co.ke/api/send_sms',
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
    ]);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        _log("Texin SMS CURL Error: $curlError", 'SMS', 0);
        _texin_log($phone, $message, null, 'failed', 'CURL Error: ' . $curlError);
        return false;
    }

    $result = json_decode($response, true);

    if ($httpCode === 200 && !empty($result['success'])) {
        $messageId = isset($result['message_id']) ? $result['message_id'] : null;
        _log("Texin SMS: Message sent to $phone (ID: $messageId, Balance: " . (isset($result['new_balance']) ? $result['new_balance'] : 'N/A') . ")", 'SMS', 1);
        _texin_log($phone, $message, $messageId, 'sent', 'Sent. Balance: ' . (isset($result['new_balance']) ? $result['new_balance'] : 'N/A'));
        return true;
    } else {
        $errMsg = isset($result['message']) ? $result['message'] : ('HTTP ' . $httpCode . ': ' . $response);
        _log("Texin SMS Error for $phone: $errMsg", 'SMS', 0);
        _texin_log($phone, $message, null, 'failed', $errMsg);
        return false;
    }
}

/**
 * Log an SMS attempt to tbl_sms_logs.
 */
function _texin_log($phone, $message, $message_id, $status, $status_message)
{
    try {
        $log                 = ORM::for_table('tbl_sms_logs')->create();
        $log->phone          = $phone;
        $log->message        = $message;
        $log->message_id     = $message_id;
        $log->gateway        = 'Texin';
        $log->status         = $status;
        $log->status_message = $status_message;
        $log->save();
    } catch (Exception $e) {
        _log("Texin SMS: Failed to write log — " . $e->getMessage(), 'SMS', 0);
    }
}

/**
 * Format a phone number for the Texin API.
 * Returns 254XXXXXXXXX (12-digit Kenya format, no + sign).
 */
function _texin_phone_format($phone)
{
    // Strip everything except digits
    $phone = preg_replace('/[^0-9]/', '', $phone);

    // Remove leading + if present (we stripped it above, but be safe)
    $phone = ltrim($phone, '+');

    // Strip all leading zeros
    $stripped = ltrim($phone, '0');

    if (str_starts_with($stripped, '254')) {
        return $stripped;           // 254712345678 → 254712345678
    }

    return '254' . $stripped;      // 0712345678 / 712345678 → 254712345678
}
