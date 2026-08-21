<?php
include "init.php";

echo "=== Customer Expiration Debug ===\n";
echo "PHP Time: " . date('Y-m-d H:i:s') . "\n";

// Check MySQL time
$res = ORM::raw_execute('SELECT NOW() AS WAKTU;');
$statement = ORM::get_last_statement();
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    echo "MySQL Time: " . $row['WAKTU'] . "\n";
}

// Find expired customers
$d = ORM::for_table('tbl_user_recharges')
    ->where('status', 'on')
    ->where_lte('expiration', date("Y-m-d"))
    ->find_many();

echo "\nFound " . count($d) . " potentially expired customers:\n";
echo "==========================================\n";

foreach ($d as $ds) {
    $date_now = strtotime(date("Y-m-d H:i:s"));
    $expiration = strtotime($ds['expiration'] . ' ' . $ds['time']);
    
    echo "Customer: " . $ds['username'] . "\n";
    echo "Expiration Date: " . $ds['expiration'] . "\n";
    echo "Expiration Time: " . $ds['time'] . "\n";
    echo "Full Expiration: " . $ds['expiration'] . ' ' . $ds['time'] . "\n";
    echo "Current Time: " . date("Y-m-d H:i:s") . "\n";
    echo "Timestamp Now: " . $date_now . "\n";
    echo "Timestamp Exp: " . $expiration . "\n";
    echo "Status: " . (($date_now >= $expiration) ? "EXPIRED" : "ACTIVE") . "\n";
    echo "----------------------------------------\n";
}

// Check specific customer if provided
if (isset($_GET['customer'])) {
    $customer = ORM::for_table('tbl_user_recharges')
        ->where('status', 'on')
        ->where('username', $_GET['customer'])
        ->find_one();
    
    if ($customer) {
        echo "\n=== Specific Customer Check ===\n";
        echo "Customer ID: " . $customer['id'] . "\n";
        echo "Username: " . $customer['username'] . "\n";
        echo "Status: " . $customer['status'] . "\n";
        echo "Expiration: " . $customer['expiration'] . "\n";
        echo "Time: " . $customer['time'] . "\n";
        echo "Customer ID: " . $customer['customer_id'] . "\n";
        echo "Plan ID: " . $customer['plan_id'] . "\n";
        
        // Check customer details
        $c = ORM::for_table('tbl_customers')->where('id', $customer['customer_id'])->find_one();
        if ($c) {
            echo "Customer Name: " . $c['fullname'] . "\n";
            echo "Auto Renewal: " . ($c['auto_renewal'] ? 'Yes' : 'No') . "\n";
        }
        
        // Check plan details
        $p = ORM::for_table('tbl_plans')->where('id', $customer['plan_id'])->find_one();
        if ($p) {
            echo "Plan Name: " . $p['name_plan'] . "\n";
            echo "Plan Type: " . $p['type'] . "\n";
            echo "Router: " . $p['routers'] . "\n";
        }
    }
}
?>
