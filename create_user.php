<?php
$zone_id = "7bf4f0063e2d73a68e0138b6d078373d"; // Your Cloudflare zone ID
$api_token = "esg5oMslL39GWmMb2qkY-feoemcn3vpmULES3tgB"; // Your Cloudflare API token
$server_ip = "84.46.244.95"; // Your server IP

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subdomain = trim($_POST["subdomain"]);
    $phone_number = trim($_POST["phone"]); // Get the phone number

    // Create the subdomain
    $url = "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records";
    
    $data = [
        "type" => "A",
        "name" => "$subdomain.speedcomwifi.xyz",
        "content" => $server_ip,
        "ttl" => 3600,
        "proxied" => true // Enable proxying
    ];

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $api_token",
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($data)
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Decode the response
    $responseData = json_decode($response, true);
    $message = $responseData['success'] ? "Subdomain created: $subdomain.speedcomwifi.xyz" : "Error: " . $responseData['errors'][0]['message'];

    // Send WhatsApp SMS if the subdomain creation was successful
    if ($responseData['success']) {
        $domain_name = "$subdomain.speedcomwifi.xyz"; // Full domain
        $sms_message = "Subdomain $domain_name has been successfully created.";
        $sms_url = "https://pay.speedcomwifi.xyz/?_route=plugin/whatsappGateway_send&to=$phone_number&msg=" . urlencode($sms_message) . "&secret=91bc70bf6d62f1216fc5e4e4903698c8";
        
        // Initialize cURL for sending SMS
        $ch_sms = curl_init();
        curl_setopt($ch_sms, CURLOPT_URL, $sms_url);
        curl_setopt($ch_sms, CURLOPT_RETURNTRANSFER, true);
        $sms_response = curl_exec($ch_sms);
        curl_close($ch_sms);

        // Optionally, decode the SMS response
        $sms_response_data = json_decode($sms_response, true);
        // Check if the SMS was sent successfully (you can customize this logic based on your API response)
        if (isset($sms_response_data['success']) && $sms_response_data['success']) {
            $message .= " A WhatsApp message has been sent to $phone_number.";
        } else {
            $message .= " But there was an issue sending the WhatsApp message.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Subdomain</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"] {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        .message {
            margin-top: 20px;
            text-align: center;
            font-size: 1.2em;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Subdomain</h1>
        <form method="POST" action="">
            <input type="text" name="subdomain" placeholder="Enter subdomain name" required>
            <input type="text" name="phone" placeholder="Enter phone number (+254...)" required>
            <input type="submit" value="Create Subdomain">
        </form>
        <?php if (isset($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
