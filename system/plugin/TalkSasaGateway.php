<?php

/**
 * TalkSasa SMS Gateway Plugin for SpeedRadius
 */

// Include the SMS Lock helper
require_once(__DIR__ . '/../helpers/SMSLock.php');

// Register menu item in admin panel
register_menu("TalkSasa SMS Gateway", true, "smsGatewayTalkSasa", 'AFTER_SETTINGS', 'glyphicon glyphicon-envelope', '', '', ['Admin', 'SuperAdmin']);

// Register hook for sending SMS with specific name
register_hook('send_sms_talksasa', 'smsGatewayTalkSasa_hook_send_sms');

function smsGatewayTalkSasa()
{
    global $ui, $config, $admin;
    _admin();

    if (empty($config['talksasa_api_token'])) {
        r2(U . 'plugin/smsGatewayTalkSasa_config', 'e', 'Please configure SMS gateway first');
    }

    // Get last 10 SMS logs
    $logs = ORM::for_table('tbl_sms_logs')
        ->order_by_desc('created_at')
        ->limit(10)
        ->find_many();

    $ui->assign('sms_logs', $logs);
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

    if (!empty(_post('talksasa_api_token')) || !empty(_post('talksasa_sender_id'))) {
        // Save API Token
        $d = ORM::for_table('tbl_appconfig')->where('setting', 'talksasa_api_token')->find_one();
        if ($d) {
            $d->value = _post('talksasa_api_token');
            $d->save();
        } else {
            $d = ORM::for_table('tbl_appconfig')->create();
            $d->setting = 'talksasa_api_token';
            $d->value = _post('talksasa_api_token');
            $d->save();
        }

        // Save Sender ID
        $d = ORM::for_table('tbl_appconfig')->where('setting', 'talksasa_sender_id')->find_one();
        if ($d) {
            $d->value = _post('talksasa_sender_id');
            $d->save();
        } else {
            $d = ORM::for_table('tbl_appconfig')->create();
            $d->setting = 'talksasa_sender_id';
            $d->value = _post('talksasa_sender_id');
            $d->save();
        }

        r2(U . 'plugin/smsGatewayTalkSasa_config', 's', 'Configuration saved successfully');
    }

    $ui->assign('_title', 'SMS Gateway Configuration');
    $ui->assign('_system_menu', 'plugin/smsGatewayTalkSasa');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('menu', 'config');
    $ui->display('smsGatewayTalkSasa.tpl');
}

function smsGatewayTalkSasa_hook_send_sms($data = [])
{
    global $config;

    // Check if we have the required data
    if (!is_array($data) || count($data) < 2) {
        _log("SMS Gateway: Invalid data format", 'SMS', 0);
        return false;
    }

    // Extract phone and message from the data array
    list($phone, $message) = $data;

    if (empty($phone) || empty($message)) {
        _log("SMS Gateway: Phone or message is empty", 'SMS', 0);
        smsGatewayTalkSasa_log($phone, $message, null, 'failed', 'Phone or message is empty');
        return false;
    }

    // Clean old locks periodically
    SMSLock::cleanOldLocks();

    // Try to acquire lock for this message
    if (!SMSLock::acquireLock($phone, $message)) {
        _log("SMS Gateway TalkSasa: Duplicate message blocked for $phone", 'SMS', 1);
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
        _log("SMS Gateway TalkSasa: Recent duplicate message found in logs", 'SMS', 1);
        return true;
    }

    // Check if configuration exists
    if (empty($config['talksasa_api_token']) || empty($config['talksasa_sender_id'])) {
        _log("SMS Gateway: Configuration missing", 'SMS', 0);
        smsGatewayTalkSasa_log($phone, $message, null, 'failed', 'Configuration missing');
        return false;
    }

    // Format phone number 
    $phone = smsGatewayTalkSasa_phoneFormat($phone);    // Prepare API request
    $url = 'https://bulksms.talksasa.com/api/v3/sms/send';
    $postData = [
        'recipient' => $phone,
        'sender_id' => $config['talksasa_sender_id'],
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
        'Authorization: Bearer ' . $config['talksasa_api_token'],
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        _log("SMS Gateway CURL Error: " . $error, 'SMS', 0);
        smsGatewayTalkSasa_log($phone, $message, null, 'failed', 'CURL Error: ' . $error);
        return false;
    }

    $result = json_decode($response, true);

    if ($httpCode == 200 && isset($result['status']) && $result['status'] == 'success') {
        _log("SMS sent successfully to $phone", 'SMS', 1);
        smsGatewayTalkSasa_log($phone, $message, isset($result['data']['uid']) ? $result['data']['uid'] : null, 'sent', 'Message sent successfully');
        SMSLock::releaseLock($phone, $message);
        return true;
    } else {
        $error = isset($result['message']) ? $result['message'] : 'Unknown error occurred';
        _log("SMS Gateway Error: $error", 'SMS', 0);
        smsGatewayTalkSasa_log($phone, $message, null, 'failed', $error);
        SMSLock::releaseLock($phone, $message);
        return false;
    }
}

function smsGatewayTalkSasa_log($phone, $message, $message_id, $status, $status_message)
{
    $log = ORM::for_table('tbl_sms_logs')->create();
    $log->phone = $phone;
    $log->message = $message;
    $log->message_id = $message_id;
    $log->status = $status;
    $log->status_message = $status_message;
    $log->save();
}

function smsGatewayTalkSasa_check_balance()
{
    global $config;

    header('Content-Type: application/json');
    _admin();

    if (empty($config['talksasa_api_token'])) {
        echo json_encode([
            'success' => false,
            'message' => 'API token not configured'
        ]);
        exit;
    }

    $url = 'https://bulksms.talksasa.com/api/v3/balance';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $config['talksasa_api_token'],
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
            'message' => 'CURL Error: ' . $error
        ]);
        exit;
    }

    $result = json_decode($response, true);

    if ($httpCode == 200 && isset($result['status']) && $result['status'] == 'success') {
        echo json_encode([
            'success' => true,
            'data' => $result['data']
        ]);
    } else {
        $error = isset($result['message']) ? $result['message'] : 'Unknown error occurred';
        echo json_encode([
            'success' => false,
            'message' => $error
        ]);
    }
    exit;
}

function smsGatewayTalkSasa_phoneFormat($phone)
{
    // Remove any non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // If the number starts with '0', replace it with '254'
    if (substr($phone, 0, 1) == '0') {
        $phone = '254' . substr($phone, 1);
    }
    // If the number starts with neither '254' nor '0', add '254'
    else if (substr($phone, 0, 3) != '254') {
        $phone = '254' . $phone;
    }
    
    return $phone;
}

function smsGatewayTalkSasa_messages()
{
    global $config;

    header('Content-Type: application/json');
    _admin();

    if (empty($config['talksasa_api_token'])) {
        echo json_encode([
            'success' => false,
            'message' => 'API token not configured'
        ]);
        exit;
    }

    $url = 'https://bulksms.talksasa.com/api/v3/sms/';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $config['talksasa_api_token'],
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
            'message' => 'CURL Error: ' . $error
        ]);
        exit;
    }

    $result = json_decode($response, true);

    if ($httpCode == 200 && isset($result['status']) && $result['status'] == 'success') {
        echo json_encode([
            'success' => true,
            'data' => $result['data']
        ]);
    } else {
        $error = isset($result['message']) ? $result['message'] : 'Unknown error occurred';
        echo json_encode([
            'success' => false,
            'message' => $error
        ]);
    }
    exit;
}
