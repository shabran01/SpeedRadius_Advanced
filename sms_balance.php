<?php
// Your API key
$apiKey = "your api key";

// API endpoint
$url = "https://blessedtexts.com/api/sms/v1/credit-balance";

// Initialize cURL session
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['api_key' => $apiKey]));

// Execute cURL request
$response = curl_exec($ch);

// Close cURL session
curl_close($ch);

// Decode the JSON response
$responseData = json_decode($response, true);

// Check if the request was successful
if ($responseData && $responseData['status_code'] === "1000") {
    $balance = $responseData['balance'];
} else {
    $balance = "Error retrieving balance";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Balance</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #4a4a4a;
            margin-bottom: 20px;
        }
        .balance {
            font-size: 36px;
            font-weight: bold;
            color: #4CAF50;
            padding: 15px;
            border: 2px solid #4CAF50;
            border-radius: 8px;
            background-color: #e8f5e9;
            transition: background-color 0.3s, transform 0.3s;
        }
        .balance:hover {
            background-color: #c8e6c9;
            transform: scale(1.05);
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your SMS Balance</h1>
        <div class="balance">
            <?php echo htmlspecialchars($balance); ?>
        </div>
        <div class="footer">
            &copy; <?php echo date("Y"); ?> Blessed Texts. All rights reserved.
        </div>
    </div>
</body>
</html>
