<?php
/**
 * BlessedTexts SMS relay for the registration portal.
 * Public endpoint, protected by the shared API key (stored only as a SHA-256 hash).
 * Provider documentation: https://sms.blessedtexts.com/api/sms/v1/sendsms
 */
header('Content-Type: application/json; charset=utf-8');
require_once '/etc/register-app/sms.conf';

$api = isset($_REQUEST['api']) ? (string)$_REQUEST['api'] : '';
if ($api === '' || !hash_equals(SMS_RELAY_KEY_HASH, hash('sha256', $api))) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Invalid API key']);
    exit;
}

$message = isset($_REQUEST['message']) ? trim((string)$_REQUEST['message']) : '';
$phone   = isset($_REQUEST['phone']) ? preg_replace('/\D/', '', (string)$_REQUEST['phone']) : '';
if (str_starts_with($phone, '0')) {
    $phone = '254' . substr($phone, 1);
} elseif (strlen($phone) === 9) {
    $phone = '254' . $phone;
}
if (!preg_match('/^254\d{9}$/', $phone)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid phone number. Use 2547XXXXXXXX.']);
    exit;
}
if ($message === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Message is required']);
    exit;
}
if (strlen($message) > 1000) {
    $message = substr($message, 0, 1000);
}

$sender = isset($_REQUEST['senderid']) ? preg_replace('/[^A-Za-z0-9]/', '', (string)$_REQUEST['senderid']) : '';
if ($sender === '') $sender = SMS_RELAY_SENDER;
if (strlen($sender) > 11) $sender = substr($sender, 0, 11);

$payload = json_encode([
    'api_key'   => $api,
    'sender_id' => $sender,
    'message'   => $message,
    'phone'     => $phone,
]);

$ch = curl_init('https://sms.blessedtexts.com/api/sms/v1/sendsms');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch);

if ($err) {
    error_log('blessed relay curl error: ' . $err);
    http_response_code(502);
    echo json_encode(['status' => 'error', 'message' => 'SMS provider unreachable']);
    exit;
}

$j = json_decode($resp, true);
if ($code === 200 && isset($j[0]['status_code']) && (string)$j[0]['status_code'] === '1000') {
    echo json_encode([
        'status'       => 'success',
        'message_id'   => $j[0]['message_id'] ?? null,
        'phone'        => $j[0]['phone'] ?? $phone,
        'message_cost' => $j[0]['message_cost'] ?? null,
    ]);
    exit;
}

$desc = $j[0]['status_desc'] ?? ($j['status_desc'] ?? 'SMS provider rejected the request');
error_log('blessed relay provider error: ' . $desc);
http_response_code(502);
echo json_encode(['status' => 'error', 'message' => $desc]);