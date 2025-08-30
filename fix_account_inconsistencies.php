<?php
/**
 * Account Data Inconsistency Diagnostic and Fix Script
 * This script identifies and optionally fixes accounts that exist in tbl_user_recharges 
 * but don't have corresponding records in tbl_customers table
 */

// Include the necessary files
require_once 'config.php';
require_once 'system/orm.php';
require_once 'system/autoload/DB.php';

// Initialize database connection
ORM::configure('mysql:host=' . $db_host . ';dbname=' . $db_name, null, 'default');
ORM::configure('username', $db_user, 'default');
ORM::configure('password', $db_pass, 'default');
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'), 'default');

echo "<h1>Account Data Inconsistency Analysis</h1>\n";
echo "<p>Checking for accounts in tbl_user_recharges that don't exist in tbl_customers...</p>\n";

try {
    // Find all usernames in tbl_user_recharges that don't exist in tbl_customers
    $inconsistent_accounts = ORM::for_table('tbl_user_recharges')
        ->left_outer_join('tbl_customers', array('tbl_user_recharges.username', '=', 'tbl_customers.username'))
        ->where_null('tbl_customers.username')
        ->select('tbl_user_recharges.*')
        ->find_many();

    echo "<h2>Found " . count($inconsistent_accounts) . " inconsistent accounts:</h2>\n";
    
    if (count($inconsistent_accounts) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>ID</th><th>Username</th><th>Customer ID</th><th>Plan ID</th><th>Name Bp</th><th>Type</th><th>Status</th><th>Created On</th><th>Expires On</th><th>Router</th>";
        echo "</tr>\n";
        
        foreach ($inconsistent_accounts as $account) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($account['id']) . "</td>";
            echo "<td><strong>" . htmlspecialchars($account['username']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($account['customer_id']) . "</td>";
            echo "<td>" . htmlspecialchars($account['plan_id']) . "</td>";
            echo "<td>" . htmlspecialchars($account['namebp']) . "</td>";
            echo "<td>" . htmlspecialchars($account['type']) . "</td>";
            echo "<td>" . htmlspecialchars($account['status']) . "</td>";
            echo "<td>" . htmlspecialchars($account['recharged_on']) . "</td>";
            echo "<td>" . htmlspecialchars($account['expiration']) . "</td>";
            echo "<td>" . htmlspecialchars($account['routers']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
        
        // Additional analysis
        echo "<h3>Analysis Summary:</h3>\n";
        
        // Group by status
        $status_count = [];
        $type_count = [];
        $router_count = [];
        
        foreach ($inconsistent_accounts as $account) {
            $status = $account['status'];
            $type = $account['type'];
            $router = $account['routers'];
            
            $status_count[$status] = ($status_count[$status] ?? 0) + 1;
            $type_count[$type] = ($type_count[$type] ?? 0) + 1;
            $router_count[$router] = ($router_count[$router] ?? 0) + 1;
        }
        
        echo "<h4>By Status:</h4>\n";
        foreach ($status_count as $status => $count) {
            echo "<p>• {$status}: {$count} accounts</p>\n";
        }
        
        echo "<h4>By Type:</h4>\n";
        foreach ($type_count as $type => $count) {
            echo "<p>• {$type}: {$count} accounts</p>\n";
        }
        
        echo "<h4>By Router:</h4>\n";
        foreach ($router_count as $router => $count) {
            echo "<p>• {$router}: {$count} accounts</p>\n";
        }
        
        // Check for customer_id = 0 cases
        $zero_customer_id = array_filter($inconsistent_accounts, function($account) {
            return $account['customer_id'] == '0';
        });
        
        if (count($zero_customer_id) > 0) {
            echo "<h4>Accounts with customer_id = 0:</h4>\n";
            echo "<p><strong>" . count($zero_customer_id) . " accounts</strong> have customer_id set to 0, which indicates they might be voucher-based accounts.</p>\n";
        }
        
        // Check if any customer records exist with customer_id references
        echo "<h3>Customer ID Analysis:</h3>\n";
        $non_zero_customer_ids = array_filter($inconsistent_accounts, function($account) {
            return $account['customer_id'] != '0';
        });
        
        if (count($non_zero_customer_ids) > 0) {
            echo "<p>Checking if customer records exist for non-zero customer_ids...</p>\n";
            
            $customer_id_issues = [];
            foreach ($non_zero_customer_ids as $account) {
                $customer = ORM::for_table('tbl_customers')->find_one($account['customer_id']);
                if (!$customer) {
                    $customer_id_issues[] = $account;
                } else {
                    // Customer exists but username doesn't match
                    if ($customer['username'] != $account['username']) {
                        echo "<p><strong>Username mismatch:</strong> Account '{$account['username']}' references customer_id {$account['customer_id']} but customer has username '{$customer['username']}'</p>\n";
                    }
                }
            }
            
            if (count($customer_id_issues) > 0) {
                echo "<p><strong>Critical:</strong> " . count($customer_id_issues) . " accounts reference customer_ids that don't exist in tbl_customers!</p>\n";
            }
        }
        
    } else {
        echo "<p style='color: green;'><strong>Good news!</strong> No data inconsistencies found. All accounts in tbl_user_recharges have corresponding customer records.</p>\n";
    }
    
    // Additional checks
    echo "<h2>Additional Diagnostic Checks:</h2>\n";
    
    // Check for duplicate usernames in tbl_customers
    $duplicate_customers = ORM::for_table('tbl_customers')
        ->select('username')
        ->group_by('username')
        ->having_raw('COUNT(*) > 1')
        ->find_many();
    
    if (count($duplicate_customers) > 0) {
        echo "<p style='color: orange;'><strong>Warning:</strong> Found " . count($duplicate_customers) . " duplicate usernames in tbl_customers table!</p>\n";
        foreach ($duplicate_customers as $dup) {
            echo "<p>• Duplicate username: {$dup['username']}</p>\n";
        }
    }
    
    // Check for orphaned user_recharges (customer_id points to non-existent customer)
    $orphaned_recharges = ORM::for_table('tbl_user_recharges')
        ->left_outer_join('tbl_customers', array('tbl_user_recharges.customer_id', '=', 'tbl_customers.id'))
        ->where_not_equal('tbl_user_recharges.customer_id', '0')
        ->where_null('tbl_customers.id')
        ->select('tbl_user_recharges.*')
        ->find_many();
    
    if (count($orphaned_recharges) > 0) {
        echo "<p style='color: red;'><strong>Critical:</strong> Found " . count($orphaned_recharges) . " orphaned recharges with invalid customer_id references!</p>\n";
    }
    
    echo "<hr>\n";
    echo "<h2>Recommended Actions:</h2>\n";
    
    if (count($inconsistent_accounts) > 0) {
        echo "<ol>\n";
        echo "<li><strong>Backup your database</strong> before making any changes</li>\n";
        echo "<li>Review the inconsistent accounts above</li>\n";
        echo "<li>For accounts with customer_id = 0: These might be voucher accounts and may be normal</li>\n";
        echo "<li>For accounts with valid customer_id but mismatched usernames: Update the username in tbl_user_recharges to match tbl_customers</li>\n";
        echo "<li>For accounts with invalid customer_id: Either create missing customer records or fix the customer_id reference</li>\n";
        echo "</ol>\n";
        
        echo "<p><strong>Next step:</strong> You can create a fix script or manually correct these inconsistencies in your database.</p>\n";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>\n";
    echo "<p>Make sure your database configuration is correct in config.php</p>\n";
}

echo "<hr>\n";
echo "<p><em>Analysis completed at " . date('Y-m-d H:i:s') . "</em></p>\n";
?>
