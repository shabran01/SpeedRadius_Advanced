<!-- save this as index.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Ngumzo WhatsApp API - Send Message</title>
</head>
<body>
    <h2>Send WhatsApp Message via Ngumzo API</h2>

    <form method="POST">
        <label>Sender Number:</label><br>
        <input type="text" name="sender" placeholder="254712345678" required><br><br>

        <label>Recipient Number:</label><br>
        <input type="text" name="recipient" placeholder="254776543210" required><br><br>

        <label>Message:</label><br>
        <textarea name="message" placeholder="Type your message here" required></textarea><br><br>

        <input type="submit" name="send" value="Send Message">
    </form>

<?php
if(isset($_POST['send'])) {
    $sender = $_POST['sender'];
    $recipient = $_POST['recipient'];
    $message = $_POST['message'];

    $data = array(
        "sender" => $sender,
        "recipient" => $recipient,
        "message" => $message
    );

    $jsonData = json_encode($data);

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://ngumzo.com/v1/send-message',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'api-key: qJQPEi8FWaGqzszK1uvjxPLIuWPTaV', // Your API key
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    echo "<h3>API Response:</h3>";
    echo "<pre>" . $response . "</pre>";
}
?>
</body>
</html>
