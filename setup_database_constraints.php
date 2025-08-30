<?php
/**
 * Database Constraints Setup Script
 * Adds foreign key constraints to prevent orphaned records
 */

// Include the main initialization file
require_once 'init.php';

echo "<h1>🔐 Database Constraints Setup</h1>\n";
echo "<p>This script adds foreign key constraints to prevent orphaned records in the future.</p>\n";

try {
    $db = ORM::get_db();
    
    // Check current constraints
    $existing_constraints = $db->query("
        SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE CONSTRAINT_SCHEMA = '$db_name' 
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>📋 Current Foreign Key Constraints</h2>\n";
    if (count($existing_constraints) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>Constraint</th><th>Table</th><th>Column</th><th>References Table</th><th>References Column</th>";
        echo "</tr>\n";
        
        foreach ($existing_constraints as $constraint) {
            echo "<tr>";
            echo "<td>{$constraint['CONSTRAINT_NAME']}</td>";
            echo "<td>{$constraint['TABLE_NAME']}</td>";
            echo "<td>{$constraint['COLUMN_NAME']}</td>";
            echo "<td>{$constraint['REFERENCED_TABLE_NAME']}</td>";
            echo "<td>{$constraint['REFERENCED_COLUMN_NAME']}</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    } else {
        echo "<p>No foreign key constraints found.</p>\n";
    }
    
    // Check if we need to add constraints
    $has_customer_constraint = false;
    foreach ($existing_constraints as $constraint) {
        if ($constraint['TABLE_NAME'] == 'tbl_user_recharges' && 
            $constraint['COLUMN_NAME'] == 'customer_id' && 
            $constraint['REFERENCED_TABLE_NAME'] == 'tbl_customers') {
            $has_customer_constraint = true;
            break;
        }
    }
    
    if (isset($_POST['add_constraints'])) {
        echo "<h2>🔧 Adding Constraints</h2>\n";
        
        if (!$has_customer_constraint) {
            try {
                // First, clean up any orphaned records
                echo "<p>Cleaning up orphaned records before adding constraints...</p>\n";
                
                $orphaned_count = $db->query("
                    SELECT COUNT(*) as count FROM tbl_user_recharges ur 
                    LEFT JOIN tbl_customers c ON ur.customer_id = c.id 
                    WHERE ur.customer_id != 0 AND c.id IS NULL
                ")->fetch(PDO::FETCH_ASSOC)['count'];
                
                if ($orphaned_count > 0) {
                    echo "<p style='color: orange;'>⚠️ Found $orphaned_count orphaned records. Please clean them up first using the cleanup script.</p>\n";
                } else {
                    // Add the constraint
                    $db->exec("
                        ALTER TABLE tbl_user_recharges 
                        ADD CONSTRAINT fk_recharge_customer 
                        FOREIGN KEY (customer_id) 
                        REFERENCES tbl_customers(id) 
                        ON DELETE CASCADE 
                        ON UPDATE CASCADE
                    ");
                    
                    echo "<p style='color: green;'>✅ Successfully added foreign key constraint for customer_id</p>\n";
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Failed to add constraint: " . $e->getMessage() . "</p>\n";
            }
        } else {
            echo "<p style='color: blue;'>ℹ️ Customer constraint already exists</p>\n";
        }
    } else {
        echo "<h2>🎯 Recommended Actions</h2>\n";
        
        if (!$has_customer_constraint) {
            echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>\n";
            echo "<h3>⚠️ Missing Constraint</h3>\n";
            echo "<p>The system lacks a foreign key constraint between <code>tbl_user_recharges.customer_id</code> and <code>tbl_customers.id</code>.</p>\n";
            echo "<p><strong>Benefits of adding this constraint:</strong></p>\n";
            echo "<ul>\n";
            echo "<li>🛡️ Prevents deletion of customers with active recharges</li>\n";
            echo "<li>🔄 Automatically deletes recharge records when customer is deleted (CASCADE)</li>\n";
            echo "<li>📊 Maintains data integrity at database level</li>\n";
            echo "</ul>\n";
            
            echo "<form method='post'>\n";
            echo "<p><button type='submit' name='add_constraints' value='1' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px;'>Add Foreign Key Constraints</button></p>\n";
            echo "</form>\n";
            echo "</div>\n";
        } else {
            echo "<p style='color: green;'>✅ All recommended constraints are already in place!</p>\n";
        }
    }
    
    echo "<hr>\n";
    echo "<h2>📚 Manual SQL Commands</h2>\n";
    echo "<p>If you prefer to run these manually in your database:</p>\n";
    echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>\n";
    echo "-- Add foreign key constraint (run this in your MySQL database)\n";
    echo "ALTER TABLE tbl_user_recharges \n";
    echo "ADD CONSTRAINT fk_recharge_customer \n";
    echo "FOREIGN KEY (customer_id) \n";
    echo "REFERENCES tbl_customers(id) \n";
    echo "ON DELETE CASCADE \n";
    echo "ON UPDATE CASCADE;\n";
    echo "</pre>\n";

} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>\n";
}

echo "<hr>\n";
echo "<p><em>Analysis completed at " . date('Y-m-d H:i:s') . "</em></p>\n";
?>
