<?php
include "init.php";

// Get username from URL parameter
$username = $_GET['username'] ?? '';

if (empty($username)) {
    echo "Usage: disconnect_pppoe.php?username=customer_username\n";
    exit;
}

echo "=== Manual PPPOE Disconnection ===\n";
echo "Disconnecting: $username\n\n";

// Find the customer's recharge record (even if status is off)
$recharge = ORM::for_table('tbl_user_recharges')
    ->where('username', $username)
    ->find_one();

if (!$recharge) {
    echo "Customer not found in database\n";
    exit;
}

$plan = ORM::for_table('tbl_plans')->where('id', $recharge['plan_id'])->find_one();
$customer = ORM::for_table('tbl_customers')->where('id', $recharge['customer_id'])->find_one();

if (!$plan || !$customer) {
    echo "Plan or customer details not found\n";
    exit;
}

echo "Plan: " . $plan['name_plan'] . "\n";
echo "Router: " . $plan['routers'] . "\n";
echo "Status in DB: " . $recharge['status'] . "\n\n";

// Get router info
$mikrotik = ORM::for_table('tbl_routers')->where('name', $plan['routers'])->find_one();
if (!$mikrotik) {
    echo "Router not found: " . $plan['routers'] . "\n";
    exit;
}

echo "Connecting to router: " . $mikrotik['ip_address'] . "\n";

// Load Mikrotik PPPOE device
$dvc = Package::getDevice($plan);
if (file_exists($dvc)) {
    require_once $dvc;
    
    try {
        $device = new $plan['device']();
        
        // Get client connection
        $client = $device->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        if (!$client) {
            echo "✗ Could not connect to router\n";
            exit;
        }
        
        // Remove active PPPOE session
        echo "Removing active PPPOE session...\n";
        $device->removePpoeActive($client, $username);
        
        if (!empty($customer['pppoe_username'])) {
            echo "Removing alternate username session: " . $customer['pppoe_username'] . "\n";
            $device->removePpoeActive($client, $customer['pppoe_username']);
        }
        
        echo "\n✓ PPPOE session disconnected successfully!\n";
        echo "Customer should no longer have internet access.\n";
        
    } catch (\Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "✗ Device file not found: $dvc\n";
}
?>
