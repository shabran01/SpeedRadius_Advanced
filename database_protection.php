<?php
/**
 * Add Database Foreign Key Constraints
 * Prevents orphaned records at database level
 */

require_once 'init.php';

$admin = Admin::_info();
if (!$admin || !in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
    die('Admin access required');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Protection Setup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .danger { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .btn { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .btn-danger { background: #dc3545; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>

<h1>🔐 Database Protection Setup</h1>

<?php

if (isset($_POST['add_constraints'])) {
    echo "<h2>🔧 Adding Database Constraints...</h2>";
    
    try {
        $db = ORM::get_db();
        
        // Check if constraint already exists
        $existing = $db->query("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE CONSTRAINT_SCHEMA = DATABASE()
            AND TABLE_NAME = 'tbl_user_recharges' 
            AND COLUMN_NAME = 'customer_id' 
            AND REFERENCED_TABLE_NAME = 'tbl_customers'
        ")->fetch();
        
        if ($existing) {
            echo "<div class='warning'>ℹ️ Foreign key constraint already exists: {$existing['CONSTRAINT_NAME']}</div>";
        } else {
            // Check for orphaned records first
            $orphaned_count = $db->query("
                SELECT COUNT(*) as count 
                FROM tbl_user_recharges ur 
                LEFT JOIN tbl_customers c ON ur.customer_id = c.id 
                WHERE ur.customer_id != 0 AND c.id IS NULL
            ")->fetch()['count'];
            
            if ($orphaned_count > 0) {
                echo "<div class='danger'>❌ Cannot add constraint: $orphaned_count orphaned records exist. Clean them first!</div>";
                echo "<p><a href='simple_cleanup.php'>→ Go to Cleanup Tool</a></p>";
            } else {
                // Add the constraint
                $db->exec("
                    ALTER TABLE tbl_user_recharges 
                    ADD CONSTRAINT fk_user_recharges_customer 
                    FOREIGN KEY (customer_id) 
                    REFERENCES tbl_customers(id) 
                    ON DELETE CASCADE 
                    ON UPDATE CASCADE
                ");
                
                echo "<div class='success'>✅ Successfully added foreign key constraint!</div>";
                echo "<p><strong>Effect:</strong> Now when a customer is deleted, all their recharge records will be automatically deleted too.</p>";
            }
        }
        
    } catch (Exception $e) {
        echo "<div class='danger'>❌ Error: " . $e->getMessage() . "</div>";
    }
}

// Check current constraint status
try {
    $db = ORM::get_db();
    $constraint = $db->query("
        SELECT CONSTRAINT_NAME, DELETE_RULE, UPDATE_RULE 
        FROM information_schema.REFERENTIAL_CONSTRAINTS 
        WHERE CONSTRAINT_SCHEMA = DATABASE()
        AND TABLE_NAME = 'tbl_user_recharges' 
        AND REFERENCED_TABLE_NAME = 'tbl_customers'
    ")->fetch();
    
    if ($constraint) {
        echo "<div class='success'>";
        echo "<h3>✅ Database Protection Status: ACTIVE</h3>";
        echo "<p><strong>Constraint:</strong> {$constraint['CONSTRAINT_NAME']}</p>";
        echo "<p><strong>Delete Rule:</strong> {$constraint['DELETE_RULE']}</p>";
        echo "<p><strong>Update Rule:</strong> {$constraint['UPDATE_RULE']}</p>";
        echo "<p>🛡️ Your database is protected against orphaned records!</p>";
        echo "</div>";
    } else {
        echo "<div class='warning'>";
        echo "<h3>⚠️ Database Protection Status: NOT ACTIVE</h3>";
        echo "<p>No foreign key constraint found between customer and recharge tables.</p>";
        
        // Check for orphaned records
        $orphaned_count = $db->query("
            SELECT COUNT(*) as count 
            FROM tbl_user_recharges ur 
            LEFT JOIN tbl_customers c ON ur.customer_id = c.id 
            WHERE ur.customer_id != 0 AND c.id IS NULL
        ")->fetch()['count'];
        
        if ($orphaned_count > 0) {
            echo "<p style='color: red;'>❌ Found $orphaned_count orphaned records. Clean these first before adding constraints.</p>";
            echo "<p><a href='simple_cleanup.php' class='btn'>🧹 Clean Orphaned Records First</a></p>";
        } else {
            echo "<p style='color: green;'>✅ No orphaned records found. Safe to add constraints.</p>";
            echo "<form method='post'>";
            echo "<button type='submit' name='add_constraints' class='btn' onclick='return confirm(\"Add foreign key constraint to prevent orphaned records?\")'>🔐 Add Database Protection</button>";
            echo "</form>";
        }
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='danger'>Error checking constraint status: " . $e->getMessage() . "</div>";
}

?>

<h2>📋 What Database Protection Does:</h2>
<ul>
    <li>🛡️ <strong>Prevents orphaned records:</strong> Cannot delete customer if recharges exist</li>
    <li>🔄 <strong>Automatic cleanup:</strong> When customer is deleted, their recharges are auto-deleted</li>
    <li>📊 <strong>Data integrity:</strong> Enforced at database level, not just application level</li>
    <li>⚡ <strong>Performance:</strong> Database handles cascading deletes efficiently</li>
</ul>

<h2>🔧 Manual SQL Command:</h2>
<p>If you prefer to run this manually in your database:</p>
<pre>
-- Run this in your MySQL database
ALTER TABLE tbl_user_recharges 
ADD CONSTRAINT fk_user_recharges_customer 
FOREIGN KEY (customer_id) 
REFERENCES tbl_customers(id) 
ON DELETE CASCADE 
ON UPDATE CASCADE;
</pre>

<h2>🔗 Navigation:</h2>
<p><a href="simple_cleanup.php" class="btn">🧹 Cleanup Orphaned Records</a></p>
<p><a href="<?php echo U; ?>plugin/expired_hotspot_customers_simple" class="btn">📋 Expired Customers</a></p>
<p><a href="<?php echo U; ?>admin" class="btn">🏠 Admin Dashboard</a></p>

</body>
</html>
