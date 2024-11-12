<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database and Subdomain Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            color: #fff;
            overflow: hidden; /* Prevent scrolling */
            background: #000; /* Fallback color */
            height: 100vh; /* Full height */
            display: flex; /* Flexbox for centering */
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            position: relative; /* Relative positioning for stars */
        }

        .star {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.8);
            animation: shine 2s infinite;
        }

        @keyframes shine {
            0% { opacity: 0.5; }
            50% { opacity: 1; }
            100% { opacity: 0.5; }
        }

        /* Create multiple stars */
        .star:nth-child(1) { width: 2px; height: 2px; top: 10%; left: 10%; }
        .star:nth-child(2) { width: 3px; height: 3px; top: 20%; left: 30%; }
        .star:nth-child(3) { width: 2.5px; height: 2.5px; top: 40%; left: 50%; }
        .star:nth-child(4) { width: 1.5px; height: 1.5px; top: 60%; left: 70%; }
        .star:nth-child(5) { width: 3px; height: 3px; top: 70%; left: 80%; }
        .star:nth-child(6) { width: 2px; height: 2px; top: 80%; left: 20%; }
        .star:nth-child(7) { width: 1.8px; height: 1.8px; top: 90%; left: 50%; }
        .star:nth-child(8) { width: 3.5px; height: 3.5px; top: 30%; left: 85%; }
        .star:nth-child(9) { width: 1.2px; height: 1.2px; top: 5%; left: 65%; }
        .star:nth-child(10) { width: 2.5px; height: 2.5px; top: 15%; left: 95%; }

        .container {
            position: relative;
            background-color: rgba(0, 0, 0, 0.7); /* Slightly transparent background */
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 90%;
            max-width: 500px;
            z-index: 1; /* Place container above stars */
            text-align: center; /* Center text */
        }

        h1 {
            color: #4CAF50;
            margin-bottom: 20px;
        }

        label {
            margin-bottom: 5px;
            font-weight: 500;
            display: block;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #4CAF50;
            outline: none;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .message {
            margin-top: 20px;
            font-size: 1.1em;
            color: #fff; /* Change message color to white */
            font-weight: bold;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 1.5em;
            }

            input[type="text"], input[type="password"], input[type="submit"] {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }

            h1 {
                font-size: 1.3em;
            }

            input[type="text"], input[type="password"], input[type="submit"] {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <!-- Star background -->
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>
    <div class="star"></div>

    <div class="container">
        <h1>Database & Subdomain Management</h1>

        <?php
        // Database configuration
        define('APP_URL', 'https://isp.speedcomwifi.xyz');
        $_app_stage = 'Live';

        // Database credentials
        $db_host = 'localhost';
        $db_user = 'speedradius';
        $db_password = 'KWEYU01@@';
        $db_name = 'isp';

        // Cloudflare configuration
        $zone_id = "7bf4f0063e2d73a68e0138b6d078373d"; // Your Cloudflare zone ID
        $api_token = "esg5oMslL39GWmMb2qkY-feoemcn3vpmULES3tgB"; // Your Cloudflare API token
        $server_ip = "84.46.244.95"; // Your server IP
        $base_folder = "/var/www/html/subdomains"; // Base folder for subdomains
        $source_folder = "/var/www/html/ISP"; // Source folder to copy from

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

        $message = '';

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_db = trim($_POST['new_db']);
            $subdomain = strtolower(trim($_POST["subdomain"]));
            $phone_number = trim($_POST["phone"]); // Get the phone number

            // Connection to MySQL
            $mysqli = new mysqli($db_host, $db_user, $db_password);

            // Check connection
            if ($mysqli->connect_error) {
                die("<p style='color:red;'>Connection failed: " . $mysqli->connect_error . "</p>");
            }

            // Create the new database
            if ($mysqli->query("CREATE DATABASE `$new_db`") === TRUE) {
                // Dump the original database and import it into the new database
                $dump_command = "mysqldump -u $db_user -p$db_password $db_name | mysql -u $db_user -p$db_password $new_db";
                
                // Execute the dump command
                system($dump_command, $return_var);

                if ($return_var === 0) {
                    $message .= "<p style='color:green;'>Database '$db_name' has been duplicated to '$new_db'.</p>";
                } else {
                    $message .= "<p style='color:red;'>Error duplicating database: " . $return_var . "</p>";
                }
            } else {
                $message .= "<p style='color:red;'>Error creating database: " . $mysqli->error . "</p>";
            }

            // Create the subdomain folder
            $new_folder = "${base_folder}/${subdomain}";

            // Check if the directory already exists
            if (!file_exists($new_folder)) {
                if (!mkdir($new_folder, 0755, true)) {
                    die("<p style='color:red;'>Failed to create subdomain folder: $new_folder</p>");
                }
            }

            // Copy files from the source folder to the new subdomain folder
            $copy_command = "cp -r $source_folder/* $new_folder/";
            system($copy_command, $return_var);

            if ($return_var === 0) {
                $message .= "<p style='color:green;'>Subdomain '$subdomain' has been created.</p>";

         
// Update config.php file in the new folder
$config_file = "$new_folder/config.php";
if (file_exists($config_file)) {
    $config_contents = file_get_contents($config_file);

    // Define the new APP_URL and database name
    $new_app_url = "https://$subdomain.speedcomwifi.xyz";
    
    // Define the search values
    $search_values = [
        "define('APP_URL', 'https://isp.speedcomwifi.xyz');", 
        "\$db_name = '$new_db';"
    ];

    // Define the replacement values
    $replacement_values = [
        "define('APP_URL', '$new_app_url');", 
        "\$db_name = '$new_db';"
    ];

    // Replace APP_URL and database name
    $updated_contents = str_replace(
        $search_values,      // Use the defined search values
        $replacement_values, // Use the defined replacement values
        $config_contents
    );

    // Save the updated config.php
    file_put_contents($config_file, $updated_contents);
    $message .= "<p style='color:green;'>Configuration for '$subdomain' updated successfully.</p>";
} else {
    $message .= "<p style='color:red;'>Configuration file not found.</p>";
}
} else {
    $message .= "<p style='color:red;'>Error copying files: " . $return_var . "</p>";
}



            // Update DNS records on Cloudflare
            $cf_url = "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records";
            $cf_data = [
                "type" => "A",
                "name" => $subdomain,
                "content" => $server_ip,
                "ttl" => 1,
                "proxied" => true,
            ];

            $ch = curl_init($cf_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization: Bearer $api_token"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($cf_data));
            $cf_response = curl_exec($ch);
            curl_close($ch);
            $cf_result = json_decode($cf_response);

            if (isset($cf_result->success) && $cf_result->success) {
                $message .= "<p style='color:green;'>DNS record for '$subdomain' has been added to Cloudflare.</p>";
            } else {
                $message .= "<p style='color:red;'>Error adding DNS record: " . $cf_response . "</p>";
            }

            // Send WhatsApp notification
            $whatsapp_message = urlencode("Hello from Speed Radius! Your subdomain is ready at : $subdomain.speedcomwifi.xyz/admin and database: $new_db");
            $whatsapp_api_url = "https://pay.speedcomwifi.xyz/?_route=plugin/whatsappGateway_send&to=$phone_number&msg=$whatsapp_message&secret=91bc70bf6d62f1216fc5e4e4903698c8";
            file_get_contents($whatsapp_api_url);

            $mysqli->close();
        }
        ?>

        <form method="post">
            <label for="subdomain">Subdomain:</label>
            <input type="text" name="subdomain" required>
            <label for="new_db">Database Name:</label>
            <input type="text" name="new_db" required>
            <label for="phone">Phone Number:</label>
            <input type="text" name="phone" placeholder="+254" required>
            <input type="submit" value="Create Subdomain & Database">
        </form>

        <?php if ($message): ?>
            <div class="message"><?= $message; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
