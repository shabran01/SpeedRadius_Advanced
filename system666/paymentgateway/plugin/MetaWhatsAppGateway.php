<?php

/**
 * Meta WhatsApp Cloud API Gateway Plugin for SpeedRadius
 * Uses the official Meta (Facebook) WhatsApp Business Cloud API
 * Docs: https://developers.facebook.com/docs/whatsapp/cloud-api
 *
 * Registers into the existing send_whatsapp hook so all system
 * notifications (expiry, payment, OTP, etc.) flow through Meta.
 */

// ── Register admin menu items ──────────────────────────────────────────────
register_menu(
    "Meta WhatsApp (Official)",
    true,
    "metaWhatsApp",
    'COMMUNICATION',
    'glyphicon glyphicon-comment',
    '',
    '',
    ['Admin', 'SuperAdmin']
);
register_menu(
    "Meta WA Logs",
    true,
    "metaWhatsApp_logs",
    'COMMUNICATION',
    'glyphicon glyphicon-list-alt',
    '',
    '',
    ['Admin', 'SuperAdmin']
);

// ── Register the send_whatsapp hook (priority 5 — fires after GoWhatsapp=1) ──
register_hook('send_whatsapp', 'metaWhatsApp_hook_send', 5);

// ── Webhook endpoint (called by Meta to verify & receive delivery receipts) ──
register_menu(
    "Meta WA Webhook",
    false,
    "metaWhatsApp_webhook",
    '',
    '',
    '',
    '',
    []
);

// ══════════════════════════════════════════════════════════════════════════════
// CONTROLLER  —  main dashboard page
// ══════════════════════════════════════════════════════════════════════════════
function metaWhatsApp()
{
    global $ui, $config, $admin;
    _admin();

    // Last 50 logs
    $logs = ORM::for_table('tbl_sms_logs')
        ->where('gateway', 'MetaWA')
        ->order_by_desc('id')
        ->limit(50)
        ->find_many();

    $ui->assign('logs', $logs);
    $ui->assign('_title', 'Meta WhatsApp Gateway');
    $ui->assign('_system_menu', 'plugin/metaWhatsApp');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('_c', $config);
    $ui->display('metaWhatsAppGateway.tpl');
}

// ══════════════════════════════════════════════════════════════════════════════
// CONTROLLER  —  configuration save / view
// ══════════════════════════════════════════════════════════════════════════════
function metaWhatsApp_config()
{
    global $ui, $config, $admin;
    _admin();

    $fields = [
        'meta_wa_phone_id'       => 'Phone Number ID',
        'meta_wa_access_token'   => 'Permanent Access Token',
        'meta_wa_api_version'    => 'API Version',
        'meta_wa_webhook_token'  => 'Webhook Verify Token',
        'meta_wa_active'         => 'Gateway Active (meta/off)',
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        foreach (array_keys($fields) as $key) {
            $val = trim(_post($key));
            $d = ORM::for_table('tbl_appconfig')->where('setting', $key)->find_one();
            if ($d) {
                $d->value = $val;
                $d->save();
            } else {
                $d = ORM::for_table('tbl_appconfig')->create();
                $d->setting = $key;
                $d->value   = $val;
                $d->save();
            }
        }
        r2(U . 'plugin/metaWhatsApp', 's', 'Meta WhatsApp configuration saved successfully');
    }

    $ui->assign('_title', 'Meta WhatsApp — Configuration');
    $ui->assign('_system_menu', 'plugin/metaWhatsApp');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('_c', $config);
    $ui->assign('menu', 'config');
    $ui->display('metaWhatsAppGateway.tpl');
}

// ══════════════════════════════════════════════════════════════════════════════
// CONTROLLER  —  send test message (AJAX + form)
// ══════════════════════════════════════════════════════════════════════════════
function metaWhatsApp_test()
{
    global $config;
    _admin();
    header('Content-Type: application/json');

    $phone   = trim(_post('phone'));
    $message = trim(_post('message'));

    if (empty($phone) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Phone and message are required.']);
        exit;
    }

    $result = metaWhatsApp_sendMessage($phone, $message);
    echo json_encode($result);
    exit;
}

// ══════════════════════════════════════════════════════════════════════════════
// CONTROLLER  —  bulk broadcast
// ══════════════════════════════════════════════════════════════════════════════
function metaWhatsApp_broadcast()
{
    global $config, $ui, $admin;
    _admin();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        $message  = trim(_post('message'));
        $target   = trim(_post('target')); // 'active' | 'all'

        if (empty($message)) {
            echo json_encode(['success' => false, 'message' => 'Message cannot be empty.']);
            exit;
        }

        $query = ORM::for_table('tbl_customers')->where_not_equal('phonenumber', '')->where_not_null('phonenumber');
        if ($target === 'active') {
            $ids = ORM::for_table('tbl_user_recharges')
                ->select('customer_id')
                ->where('status', 'on')
                ->find_many();
            $active_ids = array_map(fn($r) => $r['customer_id'], $ids->as_array());
            if (!empty($active_ids)) {
                $query = $query->where_in('id', $active_ids);
            }
        }
        $customers = $query->find_many();

        $sent = 0; $failed = 0;
        foreach ($customers as $c) {
            $res = metaWhatsApp_sendMessage($c['phonenumber'], $message);
            if ($res['success']) $sent++; else $failed++;
        }
        echo json_encode(['success' => true, 'sent' => $sent, 'failed' => $failed]);
        exit;
    }

    // GET — show broadcast page
    $ui->assign('_title', 'Meta WhatsApp — Broadcast');
    $ui->assign('_system_menu', 'plugin/metaWhatsApp');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('_c', $config);
    $ui->assign('menu', 'broadcast');
    $ui->display('metaWhatsAppGateway.tpl');
}

// ══════════════════════════════════════════════════════════════════════════════
// CONTROLLER  —  message logs page
// ══════════════════════════════════════════════════════════════════════════════
function metaWhatsApp_logs()
{
    global $ui, $config, $admin;
    _admin();

    $page  = max(1, (int)(_get('page') ?? 1));
    $limit = 50;
    $offset = ($page - 1) * $limit;

    $total = ORM::for_table('tbl_sms_logs')->where('gateway', 'MetaWA')->count();
    $logs  = ORM::for_table('tbl_sms_logs')
        ->where('gateway', 'MetaWA')
        ->order_by_desc('id')
        ->limit($limit)
        ->offset($offset)
        ->find_many();

    $ui->assign('logs', $logs);
    $ui->assign('total', $total);
    $ui->assign('page', $page);
    $ui->assign('pages', ceil($total / $limit));
    $ui->assign('_title', 'Meta WhatsApp — Logs');
    $ui->assign('_system_menu', 'plugin/metaWhatsApp_logs');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('_c', $config);
    $ui->assign('menu', 'logs');
    $ui->display('metaWhatsAppGateway.tpl');
}

// ══════════════════════════════════════════════════════════════════════════════
// CONTROLLER  —  Meta webhook (GET=verify, POST=event)
// ══════════════════════════════════════════════════════════════════════════════
function metaWhatsApp_webhook()
{
    global $config;

    $verify_token = $config['meta_wa_webhook_token'] ?? '';

    // ── GET: hub verification ──
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $mode      = $_GET['hub_mode']          ?? '';
        $token     = $_GET['hub_verify_token']  ?? '';
        $challenge = $_GET['hub_challenge']     ?? '';

        if ($mode === 'subscribe' && hash_equals($verify_token, $token)) {
            http_response_code(200);
            echo $challenge;
        } else {
            http_response_code(403);
            echo 'Forbidden';
        }
        exit;
    }

    // ── POST: incoming event ──
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $raw  = file_get_contents('php://input');
        $body = json_decode($raw, true);

        // Process delivery status updates
        if (!empty($body['entry'])) {
            foreach ($body['entry'] as $entry) {
                foreach ($entry['changes'] ?? [] as $change) {
                    $value = $change['value'] ?? [];
                    foreach ($value['statuses'] ?? [] as $status) {
                        $wamid      = $status['id']         ?? '';
                        $st         = $status['status']     ?? '';
                        $recipient  = $status['recipient_id'] ?? '';
                        if (!empty($wamid)) {
                            $log = ORM::for_table('tbl_sms_logs')
                                ->where('message_id', $wamid)
                                ->find_one();
                            if ($log) {
                                $log->status         = ($st === 'delivered' || $st === 'read') ? 'sent' : $log->status;
                                $log->status_message = $st;
                                $log->save();
                            }
                        }
                    }
                }
            }
        }
        http_response_code(200);
        echo 'OK';
        exit;
    }

    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

// ══════════════════════════════════════════════════════════════════════════════
// HOOK HANDLER  —  fires on every Message::sendWhatsapp() call
// ══════════════════════════════════════════════════════════════════════════════
function metaWhatsApp_hook_send($data = [])
{
    global $config;

    // Only process if this gateway is set as active
    $active = $config['meta_wa_active'] ?? '';
    if ($active !== 'meta') {
        return false;
    }

    if (!is_array($data) || count($data) < 2) {
        _log('MetaWA: Invalid hook data', 'WhatsApp', 0);
        return false;
    }

    list($phone, $message) = $data;

    if (empty($phone) || empty($message)) {
        return false;
    }

    $result = metaWhatsApp_sendMessage($phone, $message);
    return $result['success'];
}

// ══════════════════════════════════════════════════════════════════════════════
// CORE  —  send a message via Meta Cloud API
// ══════════════════════════════════════════════════════════════════════════════
function metaWhatsApp_sendMessage(string $phone, string $message): array
{
    global $config;

    $phone_id = trim($config['meta_wa_phone_id']    ?? '');
    $token    = trim($config['meta_wa_access_token'] ?? '');
    $version  = trim($config['meta_wa_api_version']  ?? 'v19.0');

    if (empty($phone_id) || empty($token)) {
        metaWhatsApp_log($phone, $message, null, 'failed', 'Meta WA not configured (missing Phone ID or Token)');
        _log('MetaWA: Missing configuration', 'WhatsApp', 0);
        return ['success' => false, 'message' => 'Meta WhatsApp not configured.'];
    }

    $to = metaWhatsApp_formatPhone($phone);

    $payload = [
        'messaging_product' => 'whatsapp',
        'to'                => $to,
        'type'              => 'text',
        'text'              => [
            'body'        => $message,
            'preview_url' => false,
        ],
    ];

    $url = "https://graph.facebook.com/{$version}/{$phone_id}/messages";

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json',
        ],
    ]);

    $response  = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        metaWhatsApp_log($phone, $message, null, 'failed', 'cURL Error: ' . $curlError);
        _log('MetaWA cURL Error: ' . $curlError, 'WhatsApp', 0);
        return ['success' => false, 'message' => 'Connection error: ' . $curlError];
    }

    $result = json_decode($response, true);

    if ($httpCode === 200 && !empty($result['messages'][0]['id'])) {
        $wamid = $result['messages'][0]['id'];
        metaWhatsApp_log($phone, $message, $wamid, 'sent', 'Sent via Meta Cloud API');
        _log("MetaWA: Sent to $to — WAMID: $wamid", 'WhatsApp', 1);
        return ['success' => true, 'message' => 'Message sent.', 'wamid' => $wamid];
    }

    // Extract error detail from Meta response
    $errMsg = $result['error']['message'] ?? ('HTTP ' . $httpCode . ': ' . $response);
    metaWhatsApp_log($phone, $message, null, 'failed', $errMsg);
    _log('MetaWA Error: ' . $errMsg, 'WhatsApp', 0);
    return ['success' => false, 'message' => $errMsg, 'http_code' => $httpCode, 'response' => $result];
}

// ══════════════════════════════════════════════════════════════════════════════
// HELPER  —  write to tbl_sms_logs
// ══════════════════════════════════════════════════════════════════════════════
function metaWhatsApp_log(string $phone, string $message, ?string $wamid, string $status, string $note): void
{
    $log = ORM::for_table('tbl_sms_logs')->create();
    $log->phone          = $phone;
    $log->message        = $message;
    $log->message_id     = $wamid;
    $log->gateway        = 'MetaWA';
    $log->status         = $status;
    $log->status_message = $note;
    $log->save();
}

// ══════════════════════════════════════════════════════════════════════════════
// HELPER  —  format phone to E.164  (default country: Kenya +254)
// ══════════════════════════════════════════════════════════════════════════════
function metaWhatsApp_formatPhone(string $phone): string
{
    // Strip everything except digits and leading +
    $clean = preg_replace('/[^0-9+]/', '', $phone);

    // Already in +international format
    if (str_starts_with($clean, '+')) {
        return ltrim($clean, '+');   // Meta expects no leading +
    }

    // Remove redundant leading zeros
    $digits = ltrim($clean, '0');

    // 9 digits  → 7xxxxxxxx (Kenya local without 0)
    if (strlen($digits) === 9) {
        return '254' . $digits;
    }
    // 10 digits starting with 0 → 0xxxxxxxxx
    if (strlen($digits) === 10 && $clean[0] === '0') {
        return '254' . substr($digits, 1);
    }
    // Already has country code 254
    if (str_starts_with($digits, '254')) {
        return $digits;
    }

    // Fallback: prepend 254
    return '254' . $digits;
}
