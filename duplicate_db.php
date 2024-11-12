<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duplicate Database</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            max-width: 400px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>Duplicate Database</h1>

    <?php
    // Database configuration
    define('APP_URL', 'https://isp.speedcomwifi.xyz');
    $_app_stage = 'Live';

    // Database credentials
    $db_host = 'localhost';
    $db_user = 'speedradius';
    $db_password = 'KWEYU01@@';
    $db_name = 'isp';

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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_db = $_POST['new_db'];

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
                echo "<p style='color:green;'>Database '$db_name' has been duplicated to '$new_db'.</p>";
            } else {
                echo "<p style='color:red;'>Error duplicating database: " . $return_var . "</p>";
            }
        } else {
            echo "<p style='color:red;'>Error creating database: " . $mysqli->error . "</p>";
        }

        // Close connection
        $mysqli->close();
    }
    ?>

    <form action="" method="post">
        <label for="new_db">New Database Name:</label>
        <input type="text" id="new_db" name="new_db" required>
        
        <input type="submit" value="Duplicate Database">
    </form>
</body>
</html>
