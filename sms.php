<?php

class BytewaveSMSAPI {
    private $apiToken;
    private $apiUrl = 'https://portal.bytewavenetworks.com/api/v3/sms/send';

    public function __construct($apiToken) {
        $this->apiToken = $apiToken;
    }

    private function sendRequest($data) {
        $headers = [
            "Authorization: Bearer {$this->apiToken}",
            "Content-Type: application/json",
            "Accept: application/json"
        ];

        $options = [
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);

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

        return json_decode($response, true);
    }

    public function sendSMS($recipient, $sender_id, $message) {
        $data = [
            'recipient' => $recipient,
            'sender_id' => $sender_id,
            'type' => 'plain',
            'message' => $message,
        ];

        return $this->sendRequest($data);
    }
}

// Get parameters from URL
$message = isset($_GET['message']) ? trim($_GET['message']) : '';
$phone = isset($_GET['phone']) ? trim($_GET['phone']) : '';
$senderid = 'BytewaveSMS';

// Directly include the API token
$apiToken = '198|ob9thYMbw3Etd8fV6NbSvMANTQoMVQ8RyiIoY215b40bb35c ';

if (empty($message) || empty($phone)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required parameters.'
    ]);
    exit;
}

$bytewave = new BytewaveSMSAPI($apiToken);
$response = $bytewave->sendSMS($phone, $senderid, $message);

echo json_encode($response);

?>
