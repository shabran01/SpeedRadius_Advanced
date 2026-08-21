<?php
// ================= CONFIG =================
$apiUrl = "https://isp.speedcomwifi.xyz/?_route=plugin/goWhatsappGateway_send";
$secret = "62f301c9036b5888f747ca93edb05f1f";
$statusMessage = "";

// =============== HANDLE FORM ==============
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $to = trim($_POST['to'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($to === '' || $message === '') {
        $statusMessage = "Phone number and message are required.";
    } else {

        $payload = [
            "to"      => $to,
            "message" => $message,
            "secret"  => $secret
        ];

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);

        if ($response === false) {
            $statusMessage = "cURL Error: " . curl_error($ch);
            curl_close($ch);
        } else {
            curl_close($ch);
            $result = json_decode($response, true);

            if (isset($result['code']) && $result['code'] === "SUCCESS") {
                $statusMessage = "Message sent successfully.";
            } else {
                $statusMessage = "Failed to send message.";
                $statusMessage .= "<pre>" . htmlspecialchars($response) . "</pre>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>WhatsApp Sender</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
        }
        .box {
            width: 420px;
            margin: 60px auto;
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        button {
            background: #25D366;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #1ebe5d;
        }
        .status {
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="box">
    <h3>Send WhatsApp Message</h3>

    <form method="post">
        <label>Phone Number (with country code)</label>
        <input type="text" name="to" placeholder="+2547XXXXXXXX" required>

        <label>Message</label>
        <textarea name="message" rows="4" required></textarea>

        <button type="submit">Send Message</button>
    </form>

    <?php if ($statusMessage): ?>
        <div class="status"><?php echo $statusMessage; ?></div>
    <?php endif; ?>
</div>

</body>
</html>
