<?php

class BlessedTextsAPI {
    private $apiKey;
    private $baseUrl = 'https://blessedtexts.com/api/sms/v1';

    public function __construct($apiKey) {
        $this->apiKey = $apiKey;
    }

    private function sendRequest($endpoint, $data) {
        $url = $this->baseUrl . $endpoint;
        $headers = [
            "Content-Type: application/json",
            "Accept: application/json"
        ];

        $data['api_key'] = $this->apiKey;
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        }
        curl_close($ch);

        if (isset($error_msg)) {
            return [
                'status' => 'error',
                'message' => $error_msg
            ];
        }

        if ($httpCode >= 400) {
            return [
                'status' => 'error',
                'message' => 'API request failed with status code ' . $httpCode,
                'response' => json_decode($response, true)
            ];
        }

        return json_decode($response, true);
    }

    public function sendSMS($recipient, $sender_id, $message) {
        $data = [
            'phone' => $recipient,
            'sender_id' => $sender_id,
            'message' => $message,
        ];

        return $this->sendRequest('/sendsms', $data);
    }

    public function getCreditBalance() {
        return $this->sendRequest('/credit-balance', []);
    }
}

// Get parameters from URL
$message = isset($_GET['message']) ? trim($_GET['message']) : '';
$phone = isset($_GET['phone']) ? trim($_GET['phone']) : '';
$senderid = isset($_GET['senderid']) ? trim($_GET['senderid']) : 'BLESSEDTEXT';
$apiToken = isset($_GET['api']) ? trim($_GET['api']) : 'f4ffd31ec0054bed95b5886cd5e75945';

if (empty($apiToken)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing API token.'
    ]);
    exit;
}

if (empty($message) || empty($phone) || empty($senderid)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required parameters.'
    ]);
    exit;
}

// Basic phone number validation
$phoneNumbers = explode(',', $phone);
foreach ($phoneNumbers as $number) {
    if (!preg_match('/^\+?\d{9,15}$/', $number)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid phone number format: ' . $number
        ]);
        exit;
    }
}

$blessedTexts = new BlessedTextsAPI($apiToken);
$response = $blessedTexts->sendSMS($phone, $senderid, $message);

echo json_encode($response);

?>
