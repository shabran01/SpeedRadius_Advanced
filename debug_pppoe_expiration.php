<?php
include "init.php";

echo "=== PPPOE Customer Expiration Debug ===\n";

// Find ALL expired customers (not just today)
$expiredCustomers = ORM::for_table('tbl_user_recharges')
    ->where('status', 'on')
    ->where_lt('expiration', date("Y-m-d"))  // strictly less than today
    ->find_many();

echo "Found " . count($expiredCustomers) . " customers who expired BEFORE today:\n";
echo "================================================\n";

foreach ($expiredCustomers as $ds) {
    $date_now = strtotime(date("Y-m-d H:i:s"));
    $expiration = strtotime($ds['expiration'] . ' ' . $ds['time']);
    
    echo "Customer: " . $ds['username'] . "\n";
    echo "Expiration Date: " . $ds['expiration'] . "\n";
    echo "Expiration Time: " . $ds['time'] . "\n";
    echo "Days Expired: " . floor((time() - $expiration) / 86400) . " days\n";
    echo "Status: " . (($date_now >= $expiration) ? "SHOULD BE EXPIRED" : "ACTIVE") . "\n";
    
    // Check if this is a PPPOE customer
    $plan = ORM::for_table('tbl_plans')->where('id', $ds['plan_id'])->find_one();
    if ($plan) {
        echo "Plan: " . $plan['name_plan'] . " (Type: " . $plan['type'] . ")\n";
        echo "Router: " . $plan['routers'] . "\n";
        if ($plan['type'] == 'PPPOE') {
            echo "⚠ THIS IS A PPPOE CUSTOMER - SHOULD BE EXPIRED!\n";
        }
    }
    
    echo "----------------------------------------\n";
}

// Also check customers who expired today but should be processed
$todayExpired = ORM::for_table('tbl_user_recharges')
    ->where('status', 'on')
    ->where('expiration', date("Y-m-d"))
    ->find_many();

echo "\nCustomers expiring TODAY:\n";
echo "========================\n";

foreach ($todayExpired as $ds) {
    $date_now = strtotime(date("Y-m-d H:i:s"));
    $expiration = strtotime($ds['expiration'] . ' ' . $ds['time']);
    
    echo "Customer: " . $ds['username'] . "\n";
    echo "Expiration Time: " . $ds['time'] . "\n";
    echo "Status: " . (($date_now >= $expiration) ? "SHOULD BE EXPIRED NOW" : "Still active until " . $ds['time']) . "\n";
    
    $plan = ORM::for_table('tbl_plans')->where('id', $ds['plan_id'])->find_one();
    if ($plan && $plan['type'] == 'PPPOE') {
        echo "⚠ PPPOE CUSTOMER\n";
    }
    echo "----------------------------------------\n";
}

echo "\n=== Manual Fix Options ===\n";
echo "1. Run full cron: php system/cron.php\n";
echo "2. Expire specific PPPOE customer: php expire_customer.php?username=USERNAME\n";
echo "3. Check if cron is actually running: php check_cron_status.php\n";
?>
