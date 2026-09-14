<?php
/*
|--------------------------------------------------------------------------
| SMS API Handler — processes AJAX requests from the web panel
|--------------------------------------------------------------------------
*/

header('Content-Type: application/json');

// ===== CONFIGURATION (must match index.php) =====
$config = [
    'api_url'   => 'https://api.sms-gate.app/3rdparty/v1/messages',
    'username'  => 'DVWYEW',
    'password'  => 'rlxqfsbsezsiqh',
    'device_id' => '',
];

// ===== CSRF CHECK =====
session_start();
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !is_array($input)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

if (empty($input['csrf']) || !hash_equals($_SESSION['csrf_token'] ?? '', $input['csrf'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid security token. Refresh the page.']);
    exit;
}

// ===== VALIDATE INPUT =====
$action = $input['action'] ?? '';

if ($action !== 'send') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Unknown action']);
    exit;
}

$phones  = $input['phones'] ?? [];
$message = trim($input['message'] ?? '');

if (empty($phones) || !is_array($phones)) {
    echo json_encode(['success' => false, 'error' => 'No phone numbers provided']);
    exit;
}

if ($message === '') {
    echo json_encode(['success' => false, 'error' => 'Message cannot be empty']);
    exit;
}

if (mb_strlen($message) > 1530) {
    echo json_encode(['success' => false, 'error' => 'Message too long (max 1530 chars / 10 SMS parts)']);
    exit;
}

// ===== FORMAT PHONE NUMBERS =====
$formatted = [];
foreach ($phones as $phone) {
    $clean = preg_replace('/[^0-9+]/', '', trim($phone));
    if (strlen($clean) < 9) continue;

    // Kenyan number formatting
    if (preg_match('/^0[17]\d{8}$/', $clean)) {
        $clean = '+254' . substr($clean, 1);
    } elseif (preg_match('/^254\d{9}$/', $clean)) {
        $clean = '+' . $clean;
    } elseif (preg_match('/^[17]\d{8}$/', $clean) && strlen($clean) === 9) {
        $clean = '+254' . $clean;
    }
    // Ensure + prefix for international
    if ($clean[0] !== '+') {
        $clean = '+' . $clean;
    }

    $formatted[] = $clean;
}

if (empty($formatted)) {
    echo json_encode(['success' => false, 'error' => 'No valid phone numbers found']);
    exit;
}

// ===== BUILD API REQUEST =====
$body = [
    'phoneNumbers' => $formatted,
    'textMessage'  => ['text' => $message],
];

if (!empty($config['device_id'])) {
    $body['deviceId'] = $config['device_id'];
}

// ===== SEND VIA CURL =====
$ch = curl_init($config['api_url']);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_USERPWD        => $config['username'] . ':' . $config['password'],
    CURLOPT_POSTFIELDS     => json_encode($body),
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

// ===== HANDLE RESPONSE =====
if ($curlErr) {
    echo json_encode(['success' => false, 'error' => 'Connection error: ' . $curlErr]);
    exit;
}

if ($httpCode >= 200 && $httpCode < 300) {
    $data = json_decode($response, true);
    echo json_encode([
        'success'    => true,
        'message_id' => $data['id'] ?? ($data[0]['id'] ?? 'N/A'),
        'phone'      => $formatted[0],
        'http_code'  => $httpCode,
    ]);
} else {
    $errMsg = 'HTTP ' . $httpCode;
    $data = json_decode($response, true);
    if (isset($data['message'])) {
        $errMsg .= ': ' . $data['message'];
    } elseif (isset($data['error'])) {
        $errMsg .= ': ' . $data['error'];
    } elseif ($response) {
        $errMsg .= ': ' . substr($response, 0, 200);
    }

    echo json_encode(['success' => false, 'error' => $errMsg]);
}
