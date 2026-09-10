<?php

/**
 *  Hotspot Customer Sync Utility
 *  Syncs expired Hotspot customers between billing system and Mikrotik routers
 *  Fixes issue where customers expire in database but remain active on Mikrotik Hotspot
 */

include "../init.php";

// Load required Mikrotik classes
use PEAR2\Net\RouterOS;

// Include MikrotikHotspot device class
require_once '../system/devices/MikrotikHotspot.php';

echo "Hotspot Customer Sync Utility\n";
echo "============================\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

// Set higher time limits and memory limits
set_time_limit(300); // 5 minutes
ini_set('memory_limit', '256M');

// Function to reconnect database if connection is lost
function reconnectDatabase() {
    try {
        // Test if ORM connection is alive by running a simple query
        ORM::for_table('tbl_customers')->find_one();
    } catch (Exception $e) {
        // Reconnect if connection is lost
        include "../init.php";
        echo "🔄 Database reconnected\n";
    }
}

// Find all expired Hotspot customers in database
$expired_customers = ORM::for_table('tbl_user_recharges')
    ->select('tbl_user_recharges.*')
    ->select('tbl_customers.username', 'customer_username')
    ->select('tbl_customers.fullname', 'customer_fullname')
    ->select('tbl_customers.phonenumber', 'customer_phonenumber')
    ->select('tbl_plans.name_plan', 'plan_name')
    ->select('tbl_plans.routers', 'router_name')
    ->select('tbl_plans.type', 'plan_type')
    ->join('tbl_customers', array('tbl_user_recharges.customer_id', '=', 'tbl_customers.id'))
    ->join('tbl_plans', array('tbl_user_recharges.plan_id', '=', 'tbl_plans.id'))
    ->where('tbl_user_recharges.status', 'off')
    ->where('tbl_plans.type', 'Hotspot')
    ->where_lte('tbl_user_recharges.expiration', date('Y-m-d'))
    ->find_many();

echo "Found " . count($expired_customers) . " expired Hotspot customers in database\n\n";

// Check each router for orphaned Hotspot users
$routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
$synced_count = 0;
$error_count = 0;

foreach ($routers as $router) {
    echo "Checking router: {$router['name']} ({$router['ip_address']})\n";
    
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
        
        foreach ($hotspot_users as $user) {
            $router_username = $user->getProperty('name');
            $router_profile = $user->getProperty('profile');
            $user_id = $user->getProperty('.id');
            
            // Check if this user should be expired
            $should_expire = false;
            $customer_info = null;
            
            // Find matching customer in our expired list
            foreach ($expired_customers as $customer) {
                if ($customer['customer_username'] == $router_username) {
                    $should_expire = true;
                    $customer_info = $customer;
                    break;
                }
            }
            
            if ($should_expire && $customer_info) {
                echo "  🔍 Found orphaned user: $router_username (Profile: $router_profile)\n";
                echo "    Database status: Expired on {$customer_info['expiration']}\n";
                
                try {
                    // Reconnect database if needed
                    reconnectDatabase();
                    
                    // Get plan details
                    $plan = [
                        'routers' => $router['name'],
                        'name_plan' => $customer_info['plan_name'],
                        'plan_expired' => null
                    ];
                    
                    $customer_data = [
                        'id' => $customer_info['customer_id'],
                        'username' => $customer_info['customer_username'],
                        'fullname' => $customer_info['customer_fullname'],
                        'phonenumber' => $customer_info['customer_phonenumber']
                    ];
                    
                    // Remove from router (will move to expiry plan if configured)
                    $mikrotik->remove_customer($customer_data, $plan);
                    
                    echo "  ✅ Successfully removed from router\n";
                    $synced_count++;
                    
                    // Log the action (with error handling)
                    try {
                        _log("Hotspot Sync: Removed expired customer $router_username from router {$router['name']}");
                    } catch (Exception $logError) {
                        echo "  ⚠️  Warning: Could not log action\n";
                    }
                    
                } catch (\Exception $e) {
                    echo "  ❌ Error removing user: " . $e->getMessage() . "\n";
                    $error_count++;
                    
                    // Try to log error (with error handling)
                    try {
                        _log("Hotspot Sync Error: Failed to remove $router_username from router {$router['name']}: " . $e->getMessage());
                    } catch (Exception $logError) {
                        echo "  ⚠️  Warning: Could not log error\n";
                    }
                }
            }
        }
        
        // Also check for active users that should be disconnected
        $activeRequest = new RouterOS\Request('/ip/hotspot/active/print');
        $active_users = $client->sendSync($activeRequest);
        
        echo "  Found " . count($active_users) . " active Hotspot connections\n";
        
        foreach ($active_users as $active_user) {
            $active_username = $active_user->getProperty('user');
            $active_id = $active_user->getProperty('.id');
            
            // Check if this user should be expired
            $should_disconnect = false;
            $expired_customer_info = null;
            
            foreach ($expired_customers as $customer) {
                if ($customer['customer_username'] == $active_username) {
                    $should_disconnect = true;
                    $expired_customer_info = $customer;
                    break;
                }
            }
            
            if ($should_disconnect && $expired_customer_info) {
                echo "  🔍 Found active expired user: $active_username\n";
                
                try {
                    // Force disconnect the user
                    $removeRequest = new RouterOS\Request('/ip/hotspot/active/remove');
                    $removeRequest->setArgument('numbers', $active_id);
                    $client->sendSync($removeRequest);
                    
                    echo "  ✅ Successfully disconnected from router\n";
                    $synced_count++;
                    
                    // Log the action
                    _log("Hotspot Sync: Disconnected expired user $active_username from router {$router['name']}");
                    
                } catch (\Exception $e) {
                    echo "  ❌ Error disconnecting user: " . $e->getMessage() . "\n";
                    $error_count++;
                    _log("Hotspot Sync Error: Failed to disconnect $active_username from router {$router['name']}: " . $e->getMessage());
                }
            }
        }
        
    } catch (\Exception $e) {
        echo "  ❌ Error connecting to router: " . $e->getMessage() . "\n";
        $error_count++;
        _log("Hotspot Sync Error: Router {$router['name']} connection failed: " . $e->getMessage());
    }
    
    echo "\n";
}

// Also check for active customers in database but not on router
echo "Checking for missing customers on routers...\n";
$active_customers = ORM::for_table('tbl_user_recharges')
    ->select('tbl_user_recharges.*')
    ->select('tbl_customers.username', 'customer_username')
    ->select('tbl_customers.fullname', 'customer_fullname')
    ->select('tbl_customers.password', 'customer_password')
    ->select('tbl_plans.name_plan', 'plan_name')
    ->select('tbl_plans.routers', 'router_name')
    ->join('tbl_customers', array('tbl_user_recharges.customer_id', '=', 'tbl_customers.id'))
    ->join('tbl_plans', array('tbl_user_recharges.plan_id', '=', 'tbl_plans.id'))
    ->where('tbl_user_recharges.status', 'on')
    ->where('tbl_plans.type', 'Hotspot')
    ->where_gte('tbl_user_recharges.expiration', date('Y-m-d'))
    ->find_many();

echo "Found " . count($active_customers) . " active Hotspot customers in database\n\n";

$restored_count = 0;

foreach ($routers as $router) {
    echo "Checking for missing customers on router: {$router['name']}\n";
    
    try {
        $mikrotik = new MikrotikHotspot();
        $client = $mikrotik->getClient($router['ip_address'], $router['username'], $router['password']);
        
        if (!$client) {
            echo "  ❌ Failed to connect to router\n";
            continue;
        }
        
        // Get all Hotspot users from router
        $printRequest = new RouterOS\Request('/ip/hotspot/user/print');
        $hotspot_users = $client->sendSync($printRequest);
        
        // Create list of usernames on router
        $router_users = [];
        foreach ($hotspot_users as $user) {
            $router_users[] = $user->getProperty('name');
        }
        
        // Check for active customers not on router
        foreach ($active_customers as $customer) {
            if ($customer['router_name'] != $router['name']) {
                continue; // Skip if customer belongs to different router
            }
            
            if (!in_array($customer['customer_username'], $router_users)) {
                echo "  🔍 Found missing customer: {$customer['customer_username']}\n";
                echo "    Plan: {$customer['plan_name']}\n";
                echo "    Expires: {$customer['expiration']}\n";
                
                try {
                    // Get full customer data
                    $full_customer = ORM::for_table('tbl_customers')->where('id', $customer['customer_id'])->find_one();
                    $plan = ORM::for_table('tbl_plans')->where('id', $customer['plan_id'])->find_one();
                    
                    // Add customer back to router
                    $mikrotik->add_customer($full_customer, $plan);
                    
                    echo "  ✅ Successfully restored to router\n";
                    $restored_count++;
                    
                    // Log the action
                    _log("Hotspot Sync: Restored active customer {$customer['customer_username']} to router {$router['name']}");
                    
                } catch (\Exception $e) {
                    echo "  ❌ Error restoring customer: " . $e->getMessage() . "\n";
                    _log("Hotspot Sync Error: Failed to restore {$customer['customer_username']} to router {$router['name']}: " . $e->getMessage());
                }
            }
        }
        
    } catch (\Exception $e) {
        echo "  ❌ Error connecting to router: " . $e->getMessage() . "\n";
        _log("Hotspot Sync Error: Router {$router['name']} connection failed: " . $e->getMessage());
    }
    
    echo "\n";
}

echo "============================\n";
echo "Sync Summary:\n";
echo "✅ Expired customers removed: $synced_count\n";
echo "✅ Active customers restored: $restored_count\n";
echo "❌ Errors encountered: $error_count\n";
echo "============================\n";

if ($synced_count > 0 || $restored_count > 0) {
    echo "Sync completed successfully!\n";
} else {
    echo "No sync actions needed.\n";
}

// Send notification if significant changes were made
if ($synced_count > 5 || $restored_count > 5) {
    $message = "Hotspot Sync Report\n";
    $message .= "==================\n";
    $message .= "Expired customers removed: $synced_count\n";
    $message .= "Active customers restored: $restored_count\n";
    $message .= "Errors: $error_count\n";
    $message .= "Time: " . date('Y-m-d H:i:s') . "\n";
    
    // Send to admin if telegram is configured
    if (function_exists('sendTelegram')) {
        sendTelegram($message);
    }
}

echo "\nDone.\n";
