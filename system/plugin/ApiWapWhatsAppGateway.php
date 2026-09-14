<?php

/**
 * ApiWap WhatsApp Gateway Plugin for SpeedRadius
 * Cloud-hosted WhatsApp API by KreativeLabsKE (Kenya)
 * https://apiwap.com | https://docs.apiwap.com
 *
 * No Docker, no self-hosting — just API key + scan QR once.
 *
 * Endpoints:
 * - POST /whatsapp/send-message  → Send text
 * - POST /whatsapp/send-media     → Send media
 * - GET  /whatsapp/check-number   → Check if number is on WhatsApp
 */

register_menu("ApiWap WhatsApp", true, "apiwap_whatsapp", 'COMMUNICATION', 'fa fa-whatsapp', "Cloud", "green", ['Admin', 'SuperAdmin']);

// Register into the system's WhatsApp hook
register_hook('send_whatsapp', 'apiwap_hook_send');

// ==================== MAIN DASHBOARD ====================
function apiwap_whatsapp()
{
    global $ui, $routes;
    _admin();

    $action = $routes['2'] ?? 'dashboard';

    // Handle test send
    if ($action === 'test' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        apiwap_handle_test();
        return;
    }

    // Handle settings save
    if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        apiwap_save_settings();
        return;
    }

    // Load settings
    $cfg = apiwap_get_settings();

    // Load recent logs
    $logs = ORM::for_table('tbl_sms_logs')
        ->where('gateway', 'ApiWap')
        ->order_by_desc('id')
        ->limit(10)
        ->find_many();

    $ui->assign('cfg', $cfg);
    $ui->assign('logs', $logs);
    $ui->assign('_title', 'ApiWap WhatsApp Gateway');
    $ui->assign('_system_menu', 'apiwap_whatsapp');

    $ui->display('apiWapWhatsAppGateway.tpl');
}

// ==================== SETTINGS ====================
function apiwap_get_settings()
{
    $rows = ORM::for_table('tbl_appconfig')->find_many();
    $conf = [];
    foreach ($rows as $r) { $conf[$r['setting']] = $r['value']; }

    return [
        'api_key'    => $conf['apiwap_api_key'] ?? '',
        'enabled'    => $conf['apiwap_enabled'] ?? 'no',
        'sender_name'=> $conf['apiwap_sender_name'] ?? '',
    ];
}

function apiwap_save_settings()
{
    $api_key     = trim($_POST['api_key'] ?? '');
    $enabled     = $_POST['enabled'] ?? 'no';
    $sender_name = trim($_POST['sender_name'] ?? '');

    $settings = [
        'apiwap_api_key'     => $api_key,
        'apiwap_enabled'     => $enabled,
        'apiwap_sender_name' => $sender_name,
    ];

    foreach ($settings as $key => $val) {
        $s = ORM::for_table('tbl_appconfig')->where('setting', $key)->find_one();
        if ($s) { $s->value = $val; $s->save(); }
        else {
            $n = ORM::for_table('tbl_appconfig')->create();
            $n->setting = $key;
            $n->value = $val;
            $n->save();
        }
    }

    _notify('ApiWap settings saved.', 's');
    r2(U . 'plugin/apiwap_whatsapp');
}

// ==================== TEST SEND ====================
function apiwap_handle_test()
{
    $phone   = trim($_POST['test_phone'] ?? '');
    $message = trim($_POST['test_message'] ?? 'Hello from SpeedRadius!');

    if (empty($phone)) {
        _notify('Please enter a phone number.', 'e');
        r2(U . 'plugin/apiwap_whatsapp');
        return;
    }

    $result = apiwap_send_message($phone, $message);

    if ($result['success']) {
        _notify('✅ Test message sent successfully! Response: ' . ($result['response'] ?? 'OK'), 's');
    } else {
        _notify('❌ Failed: ' . $result['error'], 'e');
    }
    r2(U . 'plugin/apiwap_whatsapp');
}

// ==================== CORE: SEND MESSAGE ====================
function apiwap_send_message($phone, $message)
{
    $cfg = apiwap_get_settings();

    if (empty($cfg['api_key'])) {
        return ['success' => false, 'error' => 'ApiWap API key not configured. Go to Settings → Communication → ApiWap WhatsApp'];
    }

    // Format phone to international
    $phone = apiwap_format_phone($phone);

    $url = 'https://api.apiwap.com/api/v1/whatsapp/send-message';

    $payload = json_encode([
        'phoneNumber' => $phone,
        'message'     => $message,
        'type'        => 'text',
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $cfg['api_key'],
            'Content-Type: application/json',
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => false, // Some servers have SSL issues
        CURLOPT_SSL_VERIFYHOST => 0,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);
    curl_close($ch);

    // Log the attempt
    apiwap_log($phone, $message, $httpCode, $response, $error);

    if ($error) {
        return ['success' => false, 'error' => 'Connection error: ' . $error];
    }

    // Try to decode response
    $data = json_decode($response, true);
    
    // Success: 200/201 with success message
    if (($httpCode === 200 || $httpCode === 201) && $data) {
        $msg = $data['message'] ?? '';
        if (stripos($msg, 'success') !== false || stripos($msg, 'sent') !== false) {
            return ['success' => true, 'response' => $msg];
        }
        // Some APIs return success without a message field
        if (!isset($data['error']) && $httpCode === 201) {
            return ['success' => true, 'response' => 'Sent'];
        }
    }

    // Error: extract message from response
    $errMsg = $data['error'] ?? $data['message'] ?? $response;
    if (empty($errMsg)) $errMsg = "HTTP $httpCode — no response body";
    if (strlen($errMsg) > 200) $errMsg = substr($errMsg, 0, 200) . '...';
    
    return ['success' => false, 'error' => $errMsg];
}

// ==================== HOOK: System WhatsApp Integration ====================
function apiwap_hook_send($args)
{
    $phone   = is_array($args) ? ($args[0] ?? '') : $args;
    $message = is_array($args) ? ($args[1] ?? '') : '';
    
    $cfg = apiwap_get_settings();
    if ($cfg['enabled'] !== 'yes') return false;

    $result = apiwap_send_message($phone, $message);
    return $result['success'];
}

// ==================== HELPERS ====================
function apiwap_format_phone($phone)
{
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    if (substr($phone, 0, 1) === '0') $phone = '+254' . substr($phone, 1);
    if (substr($phone, 0, 1) === '7') $phone = '+254' . $phone;
    if (substr($phone, 0, 1) === '1') $phone = '+254' . $phone;
    if (substr($phone, 0, 1) !== '+') $phone = '+' . $phone;
    return $phone;
}

function apiwap_log($phone, $message, $httpCode, $response, $error)
{
    $log = ORM::for_table('tbl_sms_logs')->create();
    $log->phone           = $phone;
    $log->message         = substr($message, 0, 255);
    $log->message_id      = '';
    $log->gateway         = 'ApiWap';
    $log->status          = ($httpCode === 200 || $httpCode === 201) ? 'sent' : 'failed';
    $log->status_message  = $error ?: ($response ? substr($response, 0, 255) : "HTTP $httpCode");
    $log->save();
}
