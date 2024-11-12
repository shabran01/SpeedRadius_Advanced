<?php
$zone_id = "7bf4f0063e2d73a68e0138b6d078373d"; // Your Cloudflare zone ID
$api_token = "esg5oMslL39GWmMb2qkY-feoemcn3vpmULES3tgB"; // Your Cloudflare API token
$server_ip = "84.46.244.95"; // Your server IP
$base_folder = "/var/www/html/subdomains"; // Base folder for subdomains
$source_folder = "/var/www/html/ISP"; // Source folder to copy from

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subdomain = strtolower(trim($_POST["subdomain"]));
    $phone_number = trim($_POST["phone"]); // Get the phone number

    // Create the subdomain folder
    $new_folder = "${base_folder}/${subdomain}";
    error_log("Attempting to create directory: $new_folder");
    
    // Check if the directory already exists
    if (!file_exists($new_folder)) {
        if (!mkdir($new_folder, 0755, true)) {
            error_log("Failed to create directory: $new_folder");
            echo "<script>showToast('Failed to create directory. Please contact support.', 'error');</script>";
            exit;
        }
    }

    // Copy contents from the source folder to the new folder
    $command = "cp -r ${source_folder}/* ${new_folder}/";
    exec($command, $output, $return_var);
    if ($return_var !== 0) {
        error_log("Failed to copy files: " . implode("\n", $output));
        echo "<script>showToast('Failed to copy files. Please contact support.', 'error');</script>";
        exit;
    }

    // Define the path to the config.php file in the new folder
    $config_file = "${new_folder}/config.php";
    if (file_exists($config_file)) {
        $app_url = "https://${subdomain}.speedcomwifi.xyz";
        $db_name = $subdomain;

        // Read the contents of the config file
        $config_content = file_get_contents($config_file);
        if ($config_content !== false) {
            // Replace APP_URL and $db_name in the config file
            $config_content = preg_replace("/(define\('APP_URL',\s*')[^']*(';\s*})/", "$1$app_url$2", $config_content);
            $config_content = preg_replace("/(\$db_name\s*=\s*')[^']*(';\s*})/", "$1$db_name$2", $config_content);
            
            // Write the updated content back to the config file
            file_put_contents($config_file, $config_content);
        } else {
            error_log("Failed to read config.php: $config_file");
            echo "<script>showToast('Failed to read config file. Please contact support.', 'error');</script>";
            exit;
        }
    } else {
        error_log("config.php not found in the new folder: $new_folder");
        echo "<script>showToast('config.php not found. Please contact support.', 'error');</script>";
        exit;
    }

    // Create the DNS record in Cloudflare
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
        // Check if the SMS was sent successfully
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
            background-color: #0e1621; /* Dark background for stars */
            margin: 0;
            padding: 0;
            overflow: hidden; /* Prevent scrolling */
        }
        #stars-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0; /* Behind everything */
        }
        .star {
            position: absolute;
            background: white;
            border-radius: 50%;
            opacity: 0.8;
            animation: twinkle 1.5s infinite alternate;
        }
        @keyframes twinkle {
            0% { transform: scale(1); }
            100% { transform: scale(1.5); opacity: 1; }
        }
        .form-container {
            position: relative;
            z-index: 1; /* On top of the stars */
            background: rgba(255, 255, 255, 0.9); /* Slightly transparent white background */
            padding: 30px; /* Increased padding for a more spacious feel */
            border-radius: 15px; /* More rounded corners */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Slightly more pronounced shadow */
            max-width: 400px; /* Max width for form */
            margin: auto; /* Center horizontally */
            margin-top: 100px; /* Top margin */
        }
        h1 {
            font-size: 2.5rem; /* Larger heading */
            color: #4A5568; /* Darker color for better contrast */
            text-align: center;
        }
        p {
            margin: 5px 0;
            text-align: center;
        }
        input[type="text"] {
            padding: 12px;
            margin: 12px 0;
            border: 2px solid #ddd; /* Thicker border */
            border-radius: 10px; /* More rounded corners */
            width: 100%;
            font-size: 1rem; /* Larger font for readability */
            transition: border-color 0.3s;
        }
        input[type="text"]:focus {
            border-color: #48BB78; /* Green border on focus */
            outline: none; /* Remove default outline */
        }
        input[type="submit"] {
            padding: 12px;
            margin-top: 15px;
            background-color: #48BB78; /* Green background */
            color: white;
            border: none;
            border-radius: 10px; /* More rounded corners */
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s; /* Add transition for hover effect */
            font-size: 1rem; /* Larger font for readability */
            width: 100%; /* Full width */
        }
        input[type="submit"]:hover {
            background-color: #38A169; /* Darker green on hover */
            transform: translateY(-2px); /* Slight lift effect */
        }
        .message {
            margin-top: 20px;
            text-align: center;
            font-size: 1.1em;
            color: #333;
            font-weight: bold; /* Make message bold */
        }
    </style>
</head>
<body>
    <!-- Add stars to the background -->
    <div id="stars-container"></div>
    <script>
        const starsContainer = document.getElementById('stars-container');
        for (let i = 0; i < 100; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            star.style.left = `${Math.random() * 100}%`;
            star.style.top = `${Math.random() * 100}%`;
            star.style.width = `${Math.random() * 3 + 1}px`; // Random size for stars
            star.style.height = star.style.width; // Keep the height equal to width
            star.style.animationDelay = `${Math.random() * 2}s`;
            starsContainer.appendChild(star);
        }
    </script>

    <div class="form-container">
        <h1 class="text-4xl font-extrabold text-gray-900">SpeedRadius</h1>
        <p class="mt-2 text-lg text-gray-800">ISP Registration</p>
        <p class="mt-2 text-md text-gray-600">Join ISPs who have trusted us in managing their businesses. Create an account and start managing your clients and devices.</p>
        
        <form method="POST" class="mt-6">
            <input type="text" name="subdomain" placeholder="Enter your desired subdomain" required>
            <input type="text" name="phone" placeholder="Enter phone number (e.g., +254XXXXXXXX)" required>
            <input type="submit" value="Create Subdomain">
        </form>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
