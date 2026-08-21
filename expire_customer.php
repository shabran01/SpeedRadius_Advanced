<?php
include "init.php";

// Get customer username from URL or use default
$username = $_GET['username'] ?? '';

if (empty($username)) {
    echo "Usage: expire_customer.php?username=customer_username\n";
    echo "Example: expire_customer.php?username=testuser\n";
    exit;
}

echo "=== Manual Customer Expiration ===\n";
echo "Processing customer: $username\n";

// Find the customer's active recharge
$recharge = ORM::for_table('tbl_user_recharges')
    ->where('status', 'on')
    ->where('username', $username)
    ->find_one();

if (!$recharge) {
    echo "Customer not found or already expired\n";
    exit;
}

echo "Found customer recharge record:\n";
echo "- ID: " . $recharge['id'] . "\n";
echo "- Expiration: " . $recharge['expiration'] . ' ' . $recharge['time'] . "\n";
echo "- Status: " . $recharge['status'] . "\n";

// Get customer and plan details
$customer = ORM::for_table('tbl_customers')->where('id', $recharge['customer_id'])->find_one();
$plan = ORM::for_table('tbl_plans')->where('id', $recharge['plan_id'])->find_one();

if (!$customer || !$plan) {
    echo "Customer or plan not found\n";
    exit;
}

echo "- Customer: " . $customer['fullname'] . "\n";
echo "- Plan: " . $plan['name_plan'] . "\n";

// Set status to off
$recharge->status = 'off';
$recharge->save();
echo "✓ Set customer status to 'off'\n";

// Remove from router
$dvc = Package::getDevice($plan);
if ($_app_stage != 'demo' && file_exists($dvc)) {
    require_once $dvc;
    try {
        (new $plan['device'])->remove_customer($customer, $plan);
        echo "✓ Removed customer from router\n";
    } catch (\Exception $e) {
        echo "✗ Error removing from router: " . $e->getMessage() . "\n";
    }
} else {
    echo "✗ Router device file not found or demo mode\n";
}

echo "\n=== Customer Expired Successfully ===\n";
echo "Customer $username has been moved to expired status.\n";
echo "They should no longer have internet access.\n";
?>
