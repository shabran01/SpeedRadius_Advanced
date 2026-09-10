<?php
/*
|--------------------------------------------------------------------------
| Health Check — checks if the SMS Gateway server is reachable
|--------------------------------------------------------------------------
*/

header('Content-Type: application/json');

// Public cloud has no dedicated /health endpoint, so we check
// connectivity by hitting the base API. Any response (even 401/404)
// means the server is reachable.
$healthUrl = 'https://api.sms-gate.app/3rdparty/v1/messages';

$ch = curl_init($healthUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 8,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_NOBODY         => true,  // HEAD-like: just check connectivity
]);

curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err      = curl_error($ch);
curl_close($ch);

if ($err) {
    echo json_encode(['ok' => false, 'error' => $err]);
} else {
    echo json_encode(['ok' => true, 'server' => 'api.sms-gate.app', 'http' => $httpCode]);
}
