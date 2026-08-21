<?php
/**
 * Run Performance Indexes
 * Access via browser:  https://isp.speedcomwifi.xyz/run_indexes.php
 * Or CLI:             php run_indexes.php
 */
require_once __DIR__ . '/init.php';

echo "<h2>Running Performance Indexes...</h2><pre>";

$queries = [
    "ALTER TABLE tbl_customers ADD INDEX IF NOT EXISTS idx_status (status)",
    "ALTER TABLE tbl_customers ADD INDEX IF NOT EXISTS idx_username (username)",
    "ALTER TABLE tbl_customers ADD INDEX IF NOT EXISTS idx_fullname (fullname)",
    "ALTER TABLE tbl_customers ADD INDEX IF NOT EXISTS idx_service_type (service_type)",
    "ALTER TABLE tbl_user_recharges ADD INDEX IF NOT EXISTS idx_customer_status (customer_id, status)",
    "ALTER TABLE tbl_routers ADD INDEX IF NOT EXISTS idx_name (name)",
    "ALTER TABLE tbl_payment_gateway ADD INDEX IF NOT EXISTS idx_gateway_trx_status (gateway_trx_id, status)",
    "ALTER TABLE tbl_payment_gateway ADD INDEX IF NOT EXISTS idx_checkout_status (checkout, status)",
];

$db = ORM::get_db();
foreach ($queries as $sql) {
    echo "Running: $sql\n";
    try {
        $db->exec($sql);
        echo "✅ OK\n\n";
    } catch (Exception $e) {
        // "IF NOT EXISTS" may not be supported — try without it
        if (strpos($e->getMessage(), 'IF NOT EXISTS') !== false || strpos($e->getMessage(), 'Duplicate') !== false) {
            echo "⚠️ Already exists, skipped\n\n";
        } else {
            echo "❌ Error: " . $e->getMessage() . "\n\n";
        }
    }
}

echo "✅ Done! You can delete this file now.</pre>";
