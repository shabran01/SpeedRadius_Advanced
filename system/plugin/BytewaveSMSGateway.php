<?php

/**
 * BytewaveSMS Gateway Plugin for SpeedRadius
 */

// Include the SMS Lock helper
require_once(__DIR__ . '/../helpers/SMSLock.php');

// Register menu item in admin panel
register_menu("BytewaveSMS Gateway", true, "smsGatewayBytewave", 'AFTER_SETTINGS', 'glyphicon glyphicon-envelope', '', '', ['Admin', 'SuperAdmin']);

// Register hook for sending SMS with specific name
register_hook('send_sms_bytewave', 'smsGatewayBytewave_hook_send_sms');

function smsGatewayBytewave()
{
    global $ui, $config, $admin;
    _admin();

    if (empty($config['bytewave_api_token']) || empty($config['bytewave_sender_id'])) {
        r2(U . 'plugin/smsGatewayBytewave_config', 'e', 'Please configure SMS gateway first');
    }

    // Get last 10 SMS logs
    $logs = ORM::for_table('tbl_sms_logs')
        ->order_by_desc('created_at')
        ->limit(10)
        ->find_many();

    $ui->assign('sms_logs', $logs);
    $ui->assign('_title', 'BytewaveSMS Gateway');
    $ui->assign('_system_menu', 'plugin/smsGatewayBytewave');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('smsGatewayBytewave.tpl');
}

function smsGatewayBytewave_config()
{
    global $ui;
    _admin();

    if (!empty(_post('bytewave_api_token')) || !empty(_post('bytewave_sender_id'))) {
        // Save API Token
        $d = ORM::for_table('tbl_appconfig')->where('setting', 'bytewave_api_token')->find_one();
        if ($d) {
            $d->value = _post('bytewave_api_token');
            $d->save();
        } else {
            $d = ORM::for_table('tbl_appconfig')->create();
            $d->setting = 'bytewave_api_token';
            $d->value = _post('bytewave_api_token');
            $d->save();
        }

        // Save Sender ID
        $d = ORM::for_table('tbl_appconfig')->where('setting', 'bytewave_sender_id')->find_one();
        if ($d) {
            $d->value = _post('bytewave_sender_id');
            $d->save();
        } else {
            $d = ORM::for_table('tbl_appconfig')->create();
            $d->setting = 'bytewave_sender_id';
            $d->value = _post('bytewave_sender_id');
            $d->save();
        }

        r2(U . 'plugin/smsGatewayBytewave_config', 's', 'Configuration saved successfully');
    }

    $ui->assign('_title', 'BytewaveSMS Gateway Configuration');
    $ui->assign('_system_menu', 'plugin/smsGatewayBytewave');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('menu', 'config');
    $ui->display('smsGatewayBytewave.tpl');
}

function smsGatewayBytewave_hook_send_sms($data = [])
{
    global $config;

    // Check if we have the required data
    if (!is_array($data) || count($data) < 2) {
        _log("SMS Gateway BytewaveSMS: Invalid data format", 'SMS', 0);
        return false;
    }

    // Extract phone and message from the data array
    list($phone, $message) = $data;

    if (empty($phone) || empty($message)) {
        _log("SMS Gateway BytewaveSMS: Phone or message is empty", 'SMS', 0);
        smsGatewayBytewave_log($phone, $message, null, 'failed', 'Phone or message is empty');
        return false;
    }

    // Clean old locks periodically
    SMSLock::cleanOldLocks();

    // Try to acquire lock for this message
    if (!SMSLock::acquireLock($phone, $message)) {
        _log("SMS Gateway BytewaveSMS: Duplicate message blocked for $phone", 'SMS', 1);
        return true; // Indicate success to prevent retries
    }

    // Check recent messages in database
    $recent = ORM::for_table('tbl_sms_logs')
        ->where('phone', $phone)
        ->where('message', $message)
        ->where_gte('created_at', date('Y-m-d H:i:s', strtotime('-5 minutes')))
        ->find_one();

    if ($recent) {
        SMSLock::releaseLock($phone, $message);
        _log("SMS Gateway BytewaveSMS: Recent duplicate message found in logs", 'SMS', 1);
        return true;
    }

    // Check if configuration exists
    if (empty($config['bytewave_api_token']) || empty($config['bytewave_sender_id'])) {
        _log("SMS Gateway BytewaveSMS: Configuration missing", 'SMS', 0);
        smsGatewayBytewave_log($phone, $message, null, 'failed', 'Configuration missing');
        return false;
    }

    // Format phone number 
    $phone = smsGatewayBytewave_phoneFormat($phone);

    // Prepare API request
    $url = 'https://portal.bytewavenetworks.com/api/v3/sms/send';
    $postData = [
        'recipient' => $phone,
        'sender_id' => $config['bytewave_sender_id'],
        'type' => 'plain',
        'message' => $message
    ];

    // Send request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $config['bytewave_api_token'],
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        _log("SMS Gateway BytewaveSMS CURL Error: " . $error, 'SMS', 0);
        smsGatewayBytewave_log($phone, $message, null, 'failed', 'CURL Error: ' . $error);
        SMSLock::releaseLock($phone, $message);
        return false;
    }

    $result = json_decode($response, true);

    if ($httpCode == 200 && isset($result['status']) && $result['status'] == 'success') {
        _log("SMS sent successfully to $phone via BytewaveSMS", 'SMS', 1);
        smsGatewayBytewave_log($phone, $message, isset($result['data']['message_id']) ? $result['data']['message_id'] : null, 'sent', 'Message sent successfully');
        SMSLock::releaseLock($phone, $message);
        return true;
    } else {
        $error = isset($result['message']) ? $result['message'] : 'Unknown error occurred';
        _log("SMS Gateway BytewaveSMS Error: $error", 'SMS', 0);
        smsGatewayBytewave_log($phone, $message, null, 'failed', $error);
        SMSLock::releaseLock($phone, $message);
        return false;
    }
}

function smsGatewayBytewave_log($phone, $message, $message_id, $status, $status_message)
{
    $log = ORM::for_table('tbl_sms_logs')->create();
    $log->phone = $phone;
    $log->message = $message;
    $log->message_id = $message_id;
    $log->gateway = 'BytewaveSMS';
    $log->status = $status;
    $log->status_message = $status_message;
    $log->save();
}

function smsGatewayBytewave_check_balance()
{
    global $config;

    header('Content-Type: application/json');
    _admin();

    if (empty($config['bytewave_api_token'])) {
        echo json_encode([
            'success' => false,
            'message' => 'API token not configured'
        ]);
        exit;
    }

    // BytewaveSMS balance check endpoint (you may need to adjust this URL)
    $url = 'https://portal.bytewavenetworks.com/api/v3/balance';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $config['bytewave_api_token'],
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo json_encode([
            'success' => false,
            'message' => 'Connection error: ' . $error
        ]);
        exit;
    }

    if ($httpCode == 200) {
        $result = json_decode($response, true);
        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'HTTP Error: ' . $httpCode,
            'response' => $response
        ]);
    }
    exit;
}

function smsGatewayBytewave_phoneFormat($phone)
{
    // Remove any non-numeric characters except +
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    // Remove leading zeros and ensure proper format
    $phone = ltrim($phone, '0');
    
    // If it doesn't start with +, add country code (assuming Kenya +254)
    if (!str_starts_with($phone, '+')) {
        if (str_starts_with($phone, '254')) {
            $phone = '+' . $phone;
        } else {
            $phone = '+254' . $phone;
        }
    }
    
    return $phone;
}

?>
