<?php
/**
 * Simple Orphaned Records Viewer
 * Shows orphaned recharge records in your system
 */

require_once 'init.php';

// Check if admin is logged in
$admin = Admin::_info();
if (!$admin) {
    die('Please login as admin first');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Orphaned Records Cleanup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .danger { background-color: #ffebee; }
        .warning { background-color: #fff8e1; }
        .success { background-color: #e8f5e8; }
        .btn { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>

<h1>🧹 Orphaned Records Cleanup Tool</h1>

<?php

if (isset($_POST['delete_expired'])) {
    echo "<h2>🔄 Deleting Expired Orphaned Records...</h2>";
    
    // Get expired orphaned records
    $expired_orphaned = ORM::for_table('tbl_user_recharges')
        ->table_alias('ur')
        ->left_outer_join('tbl_customers', array('ur.customer_id', '=', 'c.id'), 'c')
        ->where_not_equal('ur.customer_id', '0')
        ->where_null('c.id')
        ->where_lt('ur.expiration', date('Y-m-d'))
        ->select('ur.*')
        ->find_many();
    
    $deleted_count = 0;
    foreach ($expired_orphaned as $record) {
        try {
            $record->delete();
            $deleted_count++;
            echo "<p class='success'>✅ Deleted expired record: ID {$record->id}, Username: {$record->username}</p>";
        } catch (Exception $e) {
            echo "<p class='danger'>❌ Failed to delete ID {$record->id}: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<p><strong>Total deleted:</strong> $deleted_count records</p>";
    echo "<hr>";
}

// Find orphaned records
$orphaned_records = ORM::for_table('tbl_user_recharges')
    ->table_alias('ur')
    ->left_outer_join('tbl_customers', array('ur.customer_id', '=', 'c.id'), 'c')
    ->where_not_equal('ur.customer_id', '0')
    ->where_null('c.id')
    ->select('ur.*')
    ->limit(100)
    ->find_many();

$total_orphaned = ORM::for_table('tbl_user_recharges')
    ->table_alias('ur')
    ->left_outer_join('tbl_customers', array('ur.customer_id', '=', 'c.id'), 'c')
    ->where_not_equal('ur.customer_id', '0')
    ->where_null('c.id')
    ->count();

echo "<h2>📊 Orphaned Records Analysis</h2>";
echo "<p><strong>Total orphaned records found:</strong> $total_orphaned</p>";
echo "<p><strong>Showing first 100 records:</strong></p>";

if (count($orphaned_records) > 0) {
    $expired_count = 0;
    $active_count = 0;
    
    echo "<table>";
    echo "<tr><th>ID</th><th>Customer ID</th><th>Username</th><th>Plan</th><th>Type</th><th>Status</th><th>Expiration</th><th>Days Ago</th></tr>";
    
    foreach ($orphaned_records as $record) {
        $is_expired = strtotime($record->expiration) < time();
        $days_ago = floor((time() - strtotime($record->expiration)) / 86400);
        
        if ($is_expired) {
            $expired_count++;
            $row_class = 'danger';
        } else {
            $active_count++;
            $row_class = 'warning';
        }
        
        echo "<tr class='$row_class'>";
        echo "<td>{$record->id}</td>";
        echo "<td style='color: red;'>{$record->customer_id}</td>";
        echo "<td>{$record->username}</td>";
        echo "<td>{$record->namebp}</td>";
        echo "<td>{$record->type}</td>";
        echo "<td>{$record->status}</td>";
        echo "<td>{$record->expiration}</td>";
        echo "<td>" . ($is_expired ? "$days_ago days ago" : "Active") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Summary:</h3>";
    echo "<p>🔴 <strong>Expired orphaned records:</strong> $expired_count (safe to delete)</p>";
    echo "<p>🟡 <strong>Active orphaned records:</strong> $active_count (need customer recreation)</p>";
    
    if ($expired_count > 0) {
        echo "<form method='post' style='margin: 20px 0;'>";
        echo "<p>⚠️ <strong>Warning:</strong> This will permanently delete $expired_count expired orphaned records.</p>";
        echo "<button type='submit' name='delete_expired' class='btn' onclick='return confirm(\"Are you sure you want to delete $expired_count expired orphaned records?\")'>🗑️ Delete $expired_count Expired Orphaned Records</button>";
        echo "</form>";
    }
    
} else {
    echo "<p class='success'>✅ <strong>Great!</strong> No orphaned records found. Your database is clean!</p>";
}

?>

<h3>🔗 Navigation</h3>
<p><a href="<?php echo U; ?>plugin/expired_hotspot_customers_simple">← Back to Expired Hotspot Customers</a></p>
<p><a href="<?php echo U; ?>admin">← Back to Admin Dashboard</a></p>

</body>
</html>
