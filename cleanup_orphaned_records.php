<?php
/**
 * Orphaned Records Cleanup Script
 * Fixes existing orphaned recharge records where customer data is missing
 */

// Include the main initialization file
require_once 'init.php';

echo "<h1>🧹 Orphaned Records Cleanup Tool</h1>\n";
echo "<p>This script will identify and help clean up orphaned recharge records.</p>\n";

try {
    // Find orphaned records (recharges with customer_id pointing to non-existent customers)
    $orphaned_by_customer_id = ORM::for_table('tbl_user_recharges')
        ->left_outer_join('tbl_customers', array('tbl_user_recharges.customer_id', '=', 'tbl_customers.id'))
        ->where_not_equal('tbl_user_recharges.customer_id', '0')
        ->where_null('tbl_customers.id')
        ->select('tbl_user_recharges.*')
        ->find_many();

    // Find orphaned records (recharges with username not in customers table)
    $orphaned_by_username = ORM::for_table('tbl_user_recharges')
        ->left_outer_join('tbl_customers', array('tbl_user_recharges.username', '=', 'tbl_customers.username'))
        ->where_null('tbl_customers.username')
        ->select('tbl_user_recharges.*')
        ->find_many();

    echo "<h2>📊 Orphaned Records Analysis</h2>\n";
    echo "<p><strong>Records with invalid customer_id:</strong> " . count($orphaned_by_customer_id) . "</p>\n";
    echo "<p><strong>Records with invalid username:</strong> " . count($orphaned_by_username) . "</p>\n";

    if (count($orphaned_by_customer_id) > 0) {
        echo "<h3>🔍 Orphaned by Customer ID</h3>\n";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>\n";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>Recharge ID</th><th>Customer ID</th><th>Username</th><th>Plan</th><th>Type</th><th>Status</th><th>Expiration</th><th>Actions</th>";
        echo "</tr>\n";

        foreach ($orphaned_by_customer_id as $record) {
            $is_expired = strtotime($record->expiration) < time();
            $row_color = $is_expired ? '#ffe6e6' : '#fff6e6';
            
            echo "<tr style='background-color: $row_color;'>";
            echo "<td>{$record->id}</td>";
            echo "<td style='color: red;'>{$record->customer_id}</td>";
            echo "<td>{$record->username}</td>";
            echo "<td>{$record->namebp}</td>";
            echo "<td>{$record->type}</td>";
            echo "<td>{$record->status}</td>";
            echo "<td>{$record->expiration}</td>";
            echo "<td>";
            if ($is_expired) {
                echo "<span style='color: orange;'>🗑️ Safe to delete (expired)</span>";
            } else {
                echo "<span style='color: red;'>⚠️ Active record - needs customer recreation</span>";
            }
            echo "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }

    if (count($orphaned_by_username) > 0) {
        echo "<h3>🔍 Orphaned by Username</h3>\n";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>\n";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>Recharge ID</th><th>Username</th><th>Customer ID</th><th>Plan</th><th>Type</th><th>Status</th><th>Expiration</th>";
        echo "</tr>\n";

        foreach ($orphaned_by_username as $record) {
            $is_expired = strtotime($record->expiration) < time();
            $row_color = $is_expired ? '#ffe6e6' : '#fff6e6';
            
            echo "<tr style='background-color: $row_color;'>";
            echo "<td>{$record->id}</td>";
            echo "<td style='color: red;'>{$record->username}</td>";
            echo "<td>{$record->customer_id}</td>";
            echo "<td>{$record->namebp}</td>";
            echo "<td>{$record->type}</td>";
            echo "<td>{$record->status}</td>";
            echo "<td>{$record->expiration}</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }

    // Provide cleanup options
    if (count($orphaned_by_customer_id) > 0 || count($orphaned_by_username) > 0) {
        echo "<hr>\n";
        echo "<h2>🛠️ Cleanup Options</h2>\n";
        
        if (isset($_POST['cleanup_action'])) {
            $action = $_POST['cleanup_action'];
            $results = array();
            
            switch ($action) {
                case 'delete_expired_orphaned':
                    // Delete only expired orphaned records
                    $expired_orphaned = ORM::for_table('tbl_user_recharges')
                        ->left_outer_join('tbl_customers', array('tbl_user_recharges.customer_id', '=', 'tbl_customers.id'))
                        ->where_not_equal('tbl_user_recharges.customer_id', '0')
                        ->where_null('tbl_customers.id')
                        ->where_lt('tbl_user_recharges.expiration', date('Y-m-d'))
                        ->select('tbl_user_recharges.*')
                        ->find_many();
                    
                    foreach ($expired_orphaned as $record) {
                        try {
                            $record->delete();
                            $results[] = "✅ Deleted expired orphaned record ID: {$record->id} (Username: {$record->username})";
                        } catch (Exception $e) {
                            $results[] = "❌ Failed to delete record ID: {$record->id} - " . $e->getMessage();
                        }
                    }
                    break;
                    
                case 'create_dummy_customers':
                    // Create dummy customer records for active orphaned recharges
                    $active_orphaned = ORM::for_table('tbl_user_recharges')
                        ->left_outer_join('tbl_customers', array('tbl_user_recharges.username', '=', 'tbl_customers.username'))
                        ->where_null('tbl_customers.username')
                        ->where_gte('tbl_user_recharges.expiration', date('Y-m-d'))
                        ->select('tbl_user_recharges.*')
                        ->group_by('tbl_user_recharges.username')
                        ->find_many();
                    
                    foreach ($active_orphaned as $record) {
                        try {
                            // Check if customer already exists
                            $existing = ORM::for_table('tbl_customers')->where('username', $record->username)->find_one();
                            if (!$existing) {
                                $customer = ORM::for_table('tbl_customers')->create();
                                $customer->username = $record->username;
                                $customer->password = password_hash('recovered_' . rand(1000, 9999), PASSWORD_DEFAULT);
                                $customer->fullname = 'Recovered Customer - ' . $record->username;
                                $customer->address = 'Data Recovery';
                                $customer->phonenumber = '0000000000';
                                $customer->email = $record->username . '@recovered.local';
                                $customer->coordinates = '';
                                $customer->service_type = 'Hotspot';
                                $customer->account_type = 'Personal';
                                $customer->created_at = date('Y-m-d H:i:s');
                                $customer->status = 'Active';
                                $customer->save();
                                
                                $results[] = "✅ Created dummy customer record for: {$record->username}";
                            } else {
                                $results[] = "ℹ️ Customer already exists: {$record->username}";
                            }
                        } catch (Exception $e) {
                            $results[] = "❌ Failed to create customer for {$record->username}: " . $e->getMessage();
                        }
                    }
                    break;
            }
            
            echo "<h3>🔄 Cleanup Results</h3>\n";
            foreach ($results as $result) {
                echo "<p>$result</p>\n";
            }
        } else {
            echo "<form method='post'>\n";
            echo "<h3>Choose cleanup action:</h3>\n";
            echo "<p><input type='radio' name='cleanup_action' value='delete_expired_orphaned' id='delete_expired'>\n";
            echo "<label for='delete_expired'>🗑️ <strong>Delete expired orphaned records</strong> (Safe - removes old expired records)</label></p>\n";
            
            echo "<p><input type='radio' name='cleanup_action' value='create_dummy_customers' id='create_dummy'>\n";
            echo "<label for='create_dummy'>👤 <strong>Create dummy customer records</strong> (Preserves active subscriptions)</label></p>\n";
            
            echo "<p><button type='submit' style='background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Execute Cleanup</button></p>\n";
            echo "</form>\n";
        }
    } else {
        echo "<p style='color: green;'><strong>✅ Great!</strong> No orphaned records found. Your database is clean!</p>\n";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>\n";
}

echo "<hr>\n";
echo "<p><em>Cleanup completed at " . date('Y-m-d H:i:s') . "</em></p>\n";
?>
