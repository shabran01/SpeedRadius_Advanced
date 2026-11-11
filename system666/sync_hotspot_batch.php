<?php

/**
 *  Hotspot Customer Sync Utility (Batch Version)
 *  Syncs expired Hotspot customers between billing system and Mikrotik routers
 *  Processes in batches to avoid timeouts and connection issues
 */

include "../init.php";

// Load required Mikrotik classes
use PEAR2\Net\RouterOS;

// Include MikrotikHotspot device class
require_once '../system/devices/MikrotikHotspot.php';

echo "Hotspot Customer Sync Utility (Batch Version)\n";
echo "==========================================\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

// Set higher time limits and memory limits
set_time_limit(300); // 5 minutes
ini_set('memory_limit', '256M');

// Function to reconnect database if connection is lost
function reconnectDatabase() {
    global $pdo;
    try {
        // Test if connection is alive
        $pdo->query("SELECT 1");
    } catch (PDOException $e) {
        // Reconnect if connection is lost
        include "../init.php";
        echo "🔄 Database reconnected\n";
    }
}

// Get routers first
$routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
echo "Found " . count($routers) . " enabled routers\n\n";

$synced_count = 0;
$error_count = 0;

foreach ($routers as $router) {
    echo "Processing router: {$router['name']} ({$router['ip_address']})\n";
    
    try {
        // Connect to Mikrotik
        $mikrotik = new MikrotikHotspot();
        $client = $mikrotik->getClient($router['ip_address'], $router['username'], $router['password']);
        
        if (!$client) {
            echo "  ❌ Failed to connect to router\n";
            $error_count++;
            continue;
        }
        
        // Get all Hotspot users from router
        $printRequest = new RouterOS\Request('/ip/hotspot/user/print');
        $hotspot_users = $client->sendSync($printRequest);
        
        echo "  Found " . count($hotspot_users) . " Hotspot users on router\n";
        
        // Get active connections
        $activeRequest = new RouterOS\Request('/ip/hotspot/active/print');
        $active_users = $client->sendSync($activeRequest);
        
        echo "  Found " . count($active_users) . " active connections\n";
        
        // Create list of usernames on router
        $router_users = [];
        foreach ($hotspot_users as $user) {
            $router_users[] = $user->getProperty('name');
        }
        
        // Process in batches to avoid timeouts
        $batch_size = 50;
        $total_processed = 0;
        
        foreach ($router_users as $index => $router_username) {
            $total_processed++;
            
            // Reconnect database every 50 records
            if ($total_processed % $batch_size == 0) {
                reconnectDatabase();
                echo "  Processed $total_processed users...\n";
            }
            
            try {
                // Check if user should be expired
                $customer = ORM::for_table('tbl_user_recharges')
                    ->select('tbl_user_recharges.*')
                    ->select('tbl_customers.fullname', 'customer_fullname')
                    ->select('tbl_customers.phonenumber', 'customer_phonenumber')
                    ->select('tbl_plans.name_plan', 'plan_name')
                    ->join('tbl_customers', array('tbl_user_recharges.customer_id', '=', 'tbl_customers.id'))
                    ->join('tbl_plans', array('tbl_user_recharges.plan_id', '=', 'tbl_plans.id'))
                    ->where('tbl_customers.username', $router_username)
                    ->where('tbl_plans.type', 'Hotspot')
                    ->find_one();
                
                if ($customer && $customer['status'] == 'off' && $customer['expiration'] < date('Y-m-d')) {
                    echo "  🔍 Removing expired user: $router_username (Expired: {$customer['expiration']})\n";
                    
                    // Get user details from router
                    $user_details = null;
                    foreach ($hotspot_users as $user) {
                        if ($user->getProperty('name') == $router_username) {
                            $user_details = $user;
                            break;
                        }
                    }
                    
                    if ($user_details) {
                        // Remove from hotspot users
                        $removeRequest = new RouterOS\Request('/ip/hotspot/user/remove');
                        $removeRequest->setArgument('numbers', $user_details->getProperty('.id'));
                        $client->sendSync($removeRequest);
                        
                        echo "    ✅ Removed from /ip/hotspot/user\n";
                        $synced_count++;
                        
                        // Also disconnect from active connections if present
                        foreach ($active_users as $active_user) {
                            if ($active_user->getProperty('user') == $router_username) {
                                $disconnectRequest = new RouterOS\Request('/ip/hotspot/active/remove');
                                $disconnectRequest->setArgument('numbers', $active_user->getProperty('.id'));
                                $client->sendSync($disconnectRequest);
                                
                                echo "    ✅ Disconnected from /ip/hotspot/active\n";
                                break;
                            }
                        }
                        
                        // Try to log (with error handling)
                        try {
                            _log("Hotspot Sync: Removed expired customer $router_username from router {$router['name']}");
                        } catch (Exception $logError) {
                            // Silent fail on logging
                        }
                    }
                }
                
            } catch (\Exception $e) {
                echo "  ❌ Error processing $router_username: " . $e->getMessage() . "\n";
                $error_count++;
                
                // Continue with next user
                continue;
            }
        }
        
        echo "  Completed processing " . count($router_users) . " users on this router\n";
        
    } catch (\Exception $e) {
        echo "  ❌ Error connecting to router: " . $e->getMessage() . "\n";
        $error_count++;
        
        // Try to log error
        try {
            _log("Hotspot Sync Error: Router {$router['name']} connection failed: " . $e->getMessage());
        } catch (Exception $logError) {
            // Silent fail on logging
        }
    }
    
    echo "\n";
}

echo "==========================================\n";
echo "Sync Summary:\n";
echo "✅ Expired customers removed: $synced_count\n";
echo "❌ Errors encountered: $error_count\n";
echo "==========================================\n";

if ($synced_count > 0) {
    echo "✅ Sync completed successfully!\n";
    echo "📊 Removed $synced_count expired Hotspot customers from routers\n";
} else {
    echo "ℹ️  No expired customers found or all already synced\n";
}

echo "\nDone.\n";
?>
