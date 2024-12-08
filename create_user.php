<?php
// Database configuration
define('APP_URL', 'https://isp.speedcomwifi.xyz');
$_app_stage = 'Live';

// Database credentials
$db_host = 'localhost';
$db_user = 'speedradius';
$db_password = 'KWEYU01@@';
$db_name = 'isp';

// Cloudflare credentials
$zone_id = "7bf4f0063e2d73a68e0138b6d078373d";
$api_token = "esg5oMslL39GWmMb2qkY-feoemcn3vpmULES3tgB";
$server_ip = "84.46.244.95";

// Folder paths
$base_folder = "/var/www/html/subdomains";
$source_folder = "/var/www/html/ISP";

// Error reporting
if ($_app_stage != 'Live') {
    error_reporting(E_ERROR);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(E_ERROR);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}

$messages = [];
$success = true;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subdomain = strtolower(trim($_POST["subdomain"]));
    $phone_number = trim($_POST["phone"]);
    $new_db = $subdomain; // Use subdomain name as database name

    // Validate inputs
    if (!preg_match('/^[a-z0-9]{2,62}$/', $subdomain)) {
        $messages[] = "Error: Subdomain can only contain letters and numbers, minimum 2 characters and maximum 62 characters.";
        $success = false;
    }
    elseif (!preg_match('/^\+[1-9]\d{1,14}$/', $phone_number)) {
        $messages[] = "Error: Please enter a valid phone number in international format (e.g., +254XXXXXXXXX)";
        $success = false;
    }
    else {
        // 1. Create Database
        $mysqli = new mysqli($db_host, $db_user, $db_password);
        if ($mysqli->connect_error) {
            $messages[] = "Database Connection Error: " . $mysqli->connect_error;
            $success = false;
        } else {
            if ($mysqli->query("CREATE DATABASE `$new_db`") === TRUE) {
                $dump_command = "mysqldump -u $db_user -p$db_password $db_name | mysql -u $db_user -p$db_password $new_db";
                system($dump_command, $return_var);

                if ($return_var === 0) {
                    $messages[] = "Database created successfully: $new_db";
                } else {
                    $messages[] = "Error duplicating database content";
                    $success = false;
                }
            } else {
                $messages[] = "Error creating database: " . $mysqli->error;
                $success = false;
            }
            $mysqli->close();
        }

        // 2. Create and Configure Subdomain Folder
        if ($success) {
            $new_folder = "${base_folder}/${subdomain}";
            
            // Create subdomain folder
            if (!file_exists($new_folder)) {
                if (mkdir($new_folder, 0755, true)) {
                    $messages[] = "Subdomain folder created successfully";
                    
                    // Copy files from source
                    $copy_command = "cp -r $source_folder/* $new_folder/";
                    system($copy_command, $return_var);
                    
                    if ($return_var === 0) {
                        $messages[] = "Files copied successfully to subdomain folder";
                        
                        // Update config.php
                        $config_file = "$new_folder/config.php";
                        if (file_exists($config_file)) {
                            $config_contents = file_get_contents($config_file);
                            $new_app_url = "https://$subdomain.speedcomwifi.xyz";
                            
                            // Define the patterns to search for
                            $patterns = [
                                '/\$db_name\s*=\s*[\'"]isp[\'"];/',  // Match $db_name = 'isp';
                                '/define\s*\(\s*[\'"]APP_URL[\'"]\s*,\s*[\'"]https:\/\/isp\.speedcomwifi\.xyz[\'"]\s*\);/'  // Match the APP_URL define
                            ];
                            
                            // Define the replacements
                            $replacements = [
                                "\$db_name = '$new_db';",
                                "define('APP_URL', '$new_app_url');"
                            ];
                            
                            // Perform the replacements
                            $updated_contents = preg_replace($patterns, $replacements, $config_contents);
                            
                            // Save the updated config file
                            if (file_put_contents($config_file, $updated_contents)) {
                                $messages[] = "Configuration updated successfully";
                            } else {
                                $messages[] = "Warning: Could not update configuration file";
                            }
                        } else {
                            $messages[] = "Warning: Configuration file not found";
                        }
                    } else {
                        $messages[] = "Error copying files to subdomain folder";
                        $success = false;
                    }
                } else {
                    $messages[] = "Error creating subdomain folder";
                    $success = false;
                }
            } else {
                $messages[] = "Error: Subdomain folder already exists";
                $success = false;
            }
        }

        // 3. Create DNS Record
        if ($success) {
            $url = "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records";
            
            $data = [
                "type" => "A",
                "name" => $subdomain,
                "content" => $server_ip,
                "ttl" => 1,
                "proxied" => true
            ];
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $api_token",
                "Content-Type: application/json"
            ]);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code === 200) {
                $messages[] = "DNS record created successfully";
                
                // Prepare notification message
                $system_url = "https://$subdomain.speedcomwifi.xyz/admin";
                $notification_message = "Welcome to SpeedRadius! Your system has been created successfully.\n\nSystem URL: $system_url\n\nDefault Login Credentials:\nUsername: admin\nPassword: admin1234\n\nPlease allow up to 5 minutes for DNS propagation. If you need any assistance, please don't hesitate to contact our support team.";
                
                // Send WhatsApp notification
                $whatsapp_message = urlencode($notification_message);
                $whatsapp_url = "https://pay.speedcomwifi.xyz/?_route=plugin/whatsappGateway_send&to=$phone_number&msg=$whatsapp_message&secret=91bc70bf6d62f1216fc5e4e4903698c8";
                
                $ch = curl_init($whatsapp_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                $whatsapp_response = curl_exec($ch);
                $whatsapp_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($whatsapp_http_code === 200) {
                    $messages[] = "WhatsApp notification sent successfully";
                } else {
                    $messages[] = "Warning: Could not send WhatsApp notification";
                }

                // Send SMS notification
                $sms_message = urlencode($notification_message);
                $sms_url = "https://isp.speedcomwifi.xyz/blessed.php?message=$sms_message&phone=$phone_number&senderid=BLESSEDTEXT&api=dacd1b2df32e4f21840650d98d69f838";
                
                $ch_sms = curl_init($sms_url);
                curl_setopt($ch_sms, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch_sms, CURLOPT_FOLLOWLOCATION, true);
                $sms_response = curl_exec($ch_sms);
                $sms_http_code = curl_getinfo($ch_sms, CURLINFO_HTTP_CODE);
                curl_close($ch_sms);
                
                if ($sms_http_code === 200) {
                    $messages[] = "SMS notification sent successfully";
                } else {
                    $messages[] = "Warning: Could not send SMS notification";
                }
            } else {
                $messages[] = "Error creating DNS record";
                $success = false;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpeedRadius - ISP Registration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --success-color: #059669;
            --error-color: #dc2626;
            --background-color: #0f172a;
            --text-color: #f1f5f9;
            --input-bg: rgba(255, 255, 255, 0.05);
            --card-bg: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background: var(--background-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            line-height: 1.5;
        }

        .container {
            width: min(90%, 480px);
            padding: 2rem;
            background: var(--card-bg);
            border-radius: 1rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            position: relative;
            z-index: 1;
        }

        h1 {
            font-size: clamp(1.5rem, 5vw, 2rem);
            margin-bottom: 1.5rem;
            text-align: center;
            color: #fff;
            font-weight: 600;
        }

        .field-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
        }

        input[type="text"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            background: var(--input-bg);
            color: var(--text-color);
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        small {
            display: block;
            margin-top: 0.5rem;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
        }

        .url-preview {
            margin-top: 0.5rem;
            padding: 0.5rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 0.25rem;
            font-size: 0.9rem;
            word-break: break-all;
        }

        input[type="submit"] {
            width: 100%;
            padding: 0.875rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        input[type="submit"]:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }

        .loading {
            display: none;
            text-align: center;
            margin: 1rem 0;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 0.5rem;
            font-size: 0.9rem;
        }

        .message-list {
            margin-top: 1.5rem;
        }

        .error-message, .success-message {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .error-message {
            background: rgba(220, 38, 38, 0.1);
            color: #fecaca;
            border: 1px solid rgba(220, 38, 38, 0.2);
        }

        .success-message {
            background: rgba(5, 150, 105, 0.1);
            color: #a7f3d0;
            border: 1px solid rgba(5, 150, 105, 0.2);
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .star {
            position: fixed;
            width: 2px;
            height: 2px;
            background: white;
            border-radius: 50%;
            opacity: 0.5;
            animation: twinkle 1s infinite;
            pointer-events: none;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        @media (max-width: 480px) {
            .container {
                padding: 1.5rem;
                width: 95%;
            }

            input[type="text"], input[type="submit"] {
                padding: 0.75rem;
            }

            h1 {
                font-size: 1.5rem;
            }
        }

        /* Add loading animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .loading {
            animation: pulse 1.5s infinite;
        }
    </style>
</head>
<body>
    <!-- Create a dynamic star background -->
    <script>
        function createStars() {
            const count = 50;
            const container = document.body;
            
            for (let i = 0; i < count; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                star.style.left = `${Math.random() * 100}%`;
                star.style.top = `${Math.random() * 100}%`;
                star.style.animationDelay = `${Math.random() * 2}s`;
                container.appendChild(star);
            }
        }
        createStars();
    </script>

    <div class="container">
        <div class="header-section">
            <h1>SpeedRadius ISP Registration</h1>
            <p>Join ISPs who have trusted us in managing their businesses. Create an account and start managing your clients and devices.</p>
        </div>
        
        <form method="POST" action="" id="createForm">
            <div class="field-group">
                <label for="subdomain">Subdomain Name:</label>
                <input type="text" id="subdomain" name="subdomain" 
                    placeholder="Enter subdomain name" 
                    pattern="[a-zA-Z0-9]{2,62}" 
                    title="Only letters and numbers allowed, minimum 2 characters and maximum 62 characters"
                    required>
                <div class="url-preview" id="urlPreview">Your system will be accessible at: <span id="previewUrl">speedcomwifi.xyz</span></div>
                <small>Letters and numbers only. No special characters or hyphens.</small>
            </div>

            <div class="field-group">
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" 
                    placeholder="+254..." 
                    pattern="\+[1-9]\d{1,14}" 
                    required>
                <small>International format (e.g., +254XXXXXXXXX)</small>
            </div>

            <div class="loading" id="loadingIndicator">
                Creating your account... Please wait...
            </div>

            <input type="submit" value="Create Account">
        </form>

        <?php if (!empty($messages)): ?>
            <div class="message-list">
                <?php foreach ($messages as $msg): ?>
                    <div class="<?php echo strpos($msg, 'Error') === 0 ? 'error-message' : 'success-message'; ?>">
                        <?php echo htmlspecialchars($msg); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('subdomain').addEventListener('input', function(e) {
            // Remove any non-alphanumeric characters
            this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
            
            const subdomain = this.value.trim().toLowerCase();
            const previewUrl = document.getElementById('previewUrl');
            if (subdomain) {
                previewUrl.textContent = `${subdomain}.speedcomwifi.xyz`;
            } else {
                previewUrl.textContent = 'speedcomwifi.xyz';
            }
        });

        document.getElementById('createForm').addEventListener('submit', function() {
            document.getElementById('loadingIndicator').style.display = 'block';
        });
    </script>
</body>
</html>
