<?php
include "../init.php";

if (php_sapi_name() !== 'cli') {
    die("This script can only be run from command line");
}

// Get router ID from command line argument
$router_id = isset($argv[1]) ? intval($argv[1]) : 0;
if (!$router_id) {
    die("Router ID required\n");
}

// Create a unique lock file for this router
$lockFile = "$CACHE_PATH/router_monitor_{$router_id}.lock";
$lock = fopen($lockFile, 'c');

if ($lock === false) {
    die("Failed to open lock file for router $router_id\n");
}

if (!flock($lock, LOCK_EX | LOCK_NB)) {
    die("Monitor for router $router_id is already running\n");
}

try {
    // Set timeout for router connection
    set_time_limit(30);
    
    $router = ORM::for_table('tbl_routers')
        ->where('id', $router_id)
        ->where('enabled', '1')
        ->find_one();
        
    if (!$router) {
        die("Router not found or not enabled\n");
    }

    // Attempt to connect and check router
    $success = false;
    $error = '';
    
    try {
        $client = Router::getClient($router['ip_address'], $router['username'], $router['password']);
        if ($client) {
            // Basic check - try to get system resource
            $client->write('/system/resource/print');
            $response = $client->read();
            if ($response) {
                $success = true;
                $router->last_seen = date('Y-m-d H:i:s');
                $router->status = 'online';
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        $router->status = 'offline';
    }

    $router->save();

    if (!$success) {
        // Send notification about offline router
        $message = "Router {$router['name']} ({$router['ip_address']}) is offline\n";
        if ($error) {
            $message .= "Error: $error\n";
        }
        Message::sendTelegram($message);
        echo $message;
    }

} catch (Exception $e) {
    echo "Error monitoring router $router_id: " . $e->getMessage() . "\n";
}

// Release lock
flock($lock, LOCK_UN);
fclose($lock);
unlink($lockFile);
