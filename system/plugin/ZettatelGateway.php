<?php
/**
 * Zettatel SMS Gateway Plugin for SpeedRadius
 */

require_once(__DIR__ . '/../helpers/SMSLock.php');

// Register menu item in admin panel
register_menu("Zettatel SMS Gateway", true, "smsGatewayZettatel", 'AFTER_SETTINGS', 'glyphicon glyphicon-envelope', '', '', ['Admin', 'SuperAdmin']);

// Register hook for sending SMS with specific name
register_hook('send_sms_zettatel', 'smsGatewayZettatel_hook_send_sms');

function smsGatewayZettatel()
{
    global $ui, $config, $admin;
    _admin();

    if (empty($config['zettatel_api_key']) || empty($config['zettatel_user_id']) || empty($config['zettatel_password']) || empty($config['zettatel_sender_id'])) {
        r2(U . 'plugin/smsGatewayZettatel_config', 'e', 'Please configure Zettatel SMS gateway first');
    }

    $logs = ORM::for_table('tbl_sms_logs')
        ->where('gateway', 'Zettatel')
        ->order_by_desc('created_at')
        ->limit(10)
        ->find_many();

    $ui->assign('sms_logs', $logs);
    $ui->assign('_title', 'Zettatel SMS Gateway');
    $ui->assign('_system_menu', 'plugin/smsGatewayZettatel');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('smsGatewayZettatel.tpl');
}

function smsGatewayZettatel_config()
{
    global $ui;
    _admin();

    if (!empty(_post('zettatel_api_key')) || !empty(_post('zettatel_user_id')) || !empty(_post('zettatel_password')) || !empty(_post('zettatel_sender_id'))) {
        $settings = [
            'zettatel_api_key' => _post('zettatel_api_key'),
            'zettatel_user_id' => _post('zettatel_user_id'),
            'zettatel_password' => _post('zettatel_password'),
            'zettatel_sender_id' => _post('zettatel_sender_id'),
        ];
        foreach ($settings as $k => $v) {
            $d = ORM::for_table('tbl_appconfig')->where('setting', $k)->find_one();
            if ($d) { $d->value = $v; $d->save(); }
            else { $d = ORM::for_table('tbl_appconfig')->create(); $d->setting = $k; $d->value = $v; $d->save(); }
        }
        r2(U . 'plugin/smsGatewayZettatel_config', 's', 'Configuration saved successfully');
    }

    $ui->assign('_title', 'Zettatel SMS Gateway Configuration');
    $ui->assign('_system_menu', 'plugin/smsGatewayZettatel');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('menu', 'config');
    $ui->display('smsGatewayZettatel.tpl');
}

function smsGatewayZettatel_hook_send_sms($data = [])
{
    global $config;

    if (!is_array($data) || count($data) < 2) {
        _log('Zettatel: Invalid data format', 'SMS', 0);
        return false;
    }

    list($phone, $message) = $data;

    if (empty($phone) || empty($message)) {
        _log('Zettatel: Phone or message empty', 'SMS', 0);
        smsGatewayZettatel_log($phone, $message, null, 'failed', 'Phone or message empty');
        return false;
    }

    SMSLock::cleanOldLocks();
    if (!SMSLock::acquireLock($phone, $message)) {
        _log("Zettatel: Duplicate message blocked for $phone", 'SMS', 1);
        return true; // treat as success
    }

    $recent = ORM::for_table('tbl_sms_logs')
        ->where('phone', $phone)
        ->where('message', $message)
        ->where('gateway', 'Zettatel')
        ->where_gte('created_at', date('Y-m-d H:i:s', strtotime('-5 minutes')))
        ->find_one();
    if ($recent) {
        SMSLock::releaseLock($phone, $message);
        _log('Zettatel: Recent duplicate found', 'SMS', 1);
        return true;
    }

    if (empty($config['zettatel_api_key']) || empty($config['zettatel_user_id']) || empty($config['zettatel_password']) || empty($config['zettatel_sender_id'])) {
        _log('Zettatel: Configuration missing', 'SMS', 0);
        smsGatewayZettatel_log($phone, $message, null, 'failed', 'Configuration missing');
        SMSLock::releaseLock($phone, $message);
        return false;
    }

    $phone = smsGatewayZettatel_phoneFormat($phone);

    $postFields = http_build_query([
        'userid' => $config['zettatel_user_id'],
        'password' => $config['zettatel_password'],
        'sendMethod' => 'quick',
        'mobile' => $phone,
        'msg' => $message,
        'senderid' => $config['zettatel_sender_id'],
        'msgType' => 'text',
        'duplicatecheck' => 'true',
        'output' => 'json'
    ]);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://portal.zettatel.com/SMSApi/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_HTTPHEADER => [
            'apikey: ' . $config['zettatel_api_key'],
            'cache-control: no-cache',
            'content-type: application/x-www-form-urlencoded'
        ],
    ]);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        _log('Zettatel CURL Error: ' . $err, 'SMS', 0);
        smsGatewayZettatel_log($phone, $message, null, 'failed', $err);
        SMSLock::releaseLock($phone, $message);
        return false;
    }

    $result = json_decode($response, true);

    // Expecting JSON structure; adjust parsing if actual differs
    $status = isset($result['status']) ? $result['status'] : null;
    $messageId = isset($result['message_id']) ? $result['message_id'] : (isset($result['msgid']) ? $result['msgid'] : null);

    if ($status === 'OK' || $status === 'success' || (isset($result['ErrorCode']) && $result['ErrorCode'] == '000')) {
        _log("SMS sent successfully to $phone via Zettatel", 'SMS', 1);
        smsGatewayZettatel_log($phone, $message, $messageId, 'sent', 'Message sent successfully');
        SMSLock::releaseLock($phone, $message);
        return true;
    } else {
        $errorMsg = isset($result['details']) ? $result['details'] : (isset($result['ErrorMessage']) ? $result['ErrorMessage'] : 'Unknown error');
        _log('Zettatel Error: ' . $errorMsg, 'SMS', 0);
        smsGatewayZettatel_log($phone, $message, null, 'failed', $errorMsg);
        SMSLock::releaseLock($phone, $message);
        return false;
    }
}

function smsGatewayZettatel_log($phone, $message, $message_id, $status, $status_message)
{
    $log = ORM::for_table('tbl_sms_logs')->create();
    $log->phone = $phone;
    $log->message = $message;
    $log->message_id = $message_id;
    $log->gateway = 'Zettatel';
    $log->status = $status;
    $log->status_message = $status_message;
    $log->save();
}

function smsGatewayZettatel_check_balance()
{
    global $config; _admin(); header('Content-Type: application/json');
    if (empty($config['zettatel_api_key']) || empty($config['zettatel_user_id']) || empty($config['zettatel_password'])) {
        echo json_encode(['success' => false, 'message' => 'Configuration missing']); exit; }
    // Zettatel may provide balance endpoint; placeholder implementation
    // If available, implement real call here
    echo json_encode(['success' => false, 'message' => 'Balance endpoint not implemented']); exit;
}

function smsGatewayZettatel_phoneFormat($phone)
{
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (substr($phone,0,1) == '0') { $phone = '254'.substr($phone,1); }
    elseif (substr($phone,0,3) != '254') { $phone = '254'.$phone; }
    return $phone;
}
?>
