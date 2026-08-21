<?php
/**
 * SpeedRadius — Database Performance Indexes
 * Run once: php system/optimize_db_indexes.php
 * Or visit: https://yourdomain.com/system/optimize_db_indexes.php
 */

require_once __DIR__ . '/../config.php';

$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($mysqli->connect_error) {
    die("❌ Connection failed: " . $mysqli->connect_error);
}

echo "<h2>⚡ SpeedRadius — Database Optimization</h2>\n";
echo "<pre>\n";

$indexes = [
    // Table => [Index Name => SQL]
    'tbl_logs' => [
        'idx_logs_date'       => 'CREATE INDEX idx_logs_date ON tbl_logs(date)',
        'idx_logs_type'       => 'CREATE INDEX idx_logs_type ON tbl_logs(type)',
    ],
    'tbl_customers' => [
        'idx_customers_username' => 'CREATE INDEX idx_customers_username ON tbl_customers(username)',
        'idx_customers_status'   => 'CREATE INDEX idx_customers_status ON tbl_customers(status)',
    ],
    'tbl_user_recharges' => [
        'idx_recharges_user_status' => 'CREATE INDEX idx_recharges_user_status ON tbl_user_recharges(username, status)',
        'idx_recharges_expiry'      => 'CREATE INDEX idx_recharges_expiry ON tbl_user_recharges(expiration)',
    ],
    'tbl_payment_gateway' => [
        'idx_pg_trx_id'   => 'CREATE INDEX idx_pg_trx_id ON tbl_payment_gateway(gateway_trx_id)',
        'idx_pg_username' => 'CREATE INDEX idx_pg_username ON tbl_payment_gateway(username)',
        'idx_pg_status'   => 'CREATE INDEX idx_pg_status ON tbl_payment_gateway(status)',
    ],
    'tbl_transactions' => [
        'idx_tx_date'     => 'CREATE INDEX idx_tx_date ON tbl_transactions(recharged_on)',
        'idx_tx_username' => 'CREATE INDEX idx_tx_username ON tbl_transactions(username)',
    ],
    'tbl_routers' => [
        'idx_routers_enabled' => 'CREATE INDEX idx_routers_enabled ON tbl_routers(enabled)',
    ],
    'tbl_plans' => [
        'idx_plans_type' => 'CREATE INDEX idx_plans_type ON tbl_plans(type)',
    ],
    'tbl_mpesa_transactions' => [
        'idx_mpesa_transid' => 'CREATE INDEX idx_mpesa_transid ON tbl_mpesa_transactions(TransID)',
    ],
];

$added = 0;
$skipped = 0;
$errors = 0;

foreach ($indexes as $table => $idxList) {
    echo "\n📋 Table: <strong>{$table}</strong>\n";

    // Check existing indexes
    $existing = $mysqli->query("SHOW INDEX FROM `{$table}`");
    $existingNames = [];
    while ($row = $existing->fetch_assoc()) {
        $existingNames[] = $row['Key_name'];
    }

    foreach ($idxList as $name => $sql) {
        if (in_array($name, $existingNames)) {
            echo "   ⏭️  {$name} — already exists\n";
            $skipped++;
        } else {
            if ($mysqli->query($sql)) {
                echo "   ✅ {$name} — created\n";
                $added++;
            } else {
                echo "   ❌ {$name} — ERROR: " . $mysqli->error . "\n";
                $errors++;
            }
        }
    }
}

echo "\n\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Added:  <strong>{$added}</strong> new indexes\n";
echo "⏭️  Skipped: {$skipped} (already exists)\n";
if ($errors > 0) echo "❌ Errors:  {$errors}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Enable query cache setting
$cacheCheck = $mysqli->query("SHOW VARIABLES LIKE 'query_cache_size'")->fetch_assoc();
if ($cacheCheck && $cacheCheck['Value'] == 0) {
    echo "\n💡 Tip: MySQL query_cache is OFF. To enable (if MySQL 5.x):\n";
    echo "   SET GLOBAL query_cache_size = 67108864;\n";
    echo "   SET GLOBAL query_cache_type = 1;\n";
} else {
    echo "\n✅ MySQL query cache already enabled.\n";
}

// ──────────────────────────────────────────
// TABLE OPTIMIZATION
// ──────────────────────────────────────────
echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n🗜️ Optimizing tables (rebuild indexes, reclaim space)...\n\n";

$tables = ['tbl_logs', 'tbl_customers', 'tbl_user_recharges', 'tbl_payment_gateway', 'tbl_transactions', 'tbl_routers', 'tbl_plans', 'tbl_mpesa_transactions'];
foreach ($tables as $table) {
    $result = $mysqli->query("OPTIMIZE TABLE `{$table}`");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "   ✅ {$table} — {$row['Msg_text']}\n";
    }
}

// ──────────────────────────────────────────
// ORPHAN DATA CLEANUP
// ──────────────────────────────────────────
echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n🧹 Checking for orphan data...\n\n";

// Expired sessions still marked 'on'
$staleCount = $mysqli->query("SELECT COUNT(*) AS cnt FROM tbl_user_recharges WHERE status = 'on' AND expiration < NOW()")->fetch_assoc()['cnt'];
if ($staleCount > 0) {
    $mysqli->query("UPDATE tbl_user_recharges SET status = 'off' WHERE status = 'on' AND expiration < NOW()");
    echo "   ✅ Fixed {$staleCount} stale sessions (expired but marked 'on')\n";
} else {
    echo "   ✅ No stale sessions found\n";
}

// Payment gateway entries with no matching customer
$orphanPg = $mysqli->query("SELECT COUNT(*) AS cnt FROM tbl_payment_gateway pg LEFT JOIN tbl_customers c ON pg.username = c.username WHERE c.id IS NULL AND pg.username NOT LIKE 'MPESA_%'")->fetch_assoc()['cnt'];
if ($orphanPg > 0) {
    echo "   ⚠️  {$orphanPg} orphan payment records (no matching customer)\n";
} else {
    echo "   ✅ No orphan payment records\n";
}

// ──────────────────────────────────────────
// TABLE SIZES & STATS
// ──────────────────────────────────────────
echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n📊 Table Sizes:\n\n";

$sizeQuery = $mysqli->query("
    SELECT TABLE_NAME AS tbl,
           ROUND((DATA_LENGTH + INDEX_LENGTH) / 1048576, 2) AS size_mb,
           TABLE_ROWS AS rows
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = '{$db_name}'
      AND TABLE_NAME LIKE 'tbl_%'
    ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
");

echo "Table                          Size      Rows\n";
echo str_repeat("─", 55) . "\n";
while ($row = $sizeQuery->fetch_assoc()) {
    printf("   %-30s %7s MB  %s\n", $row['tbl'], $row['size_mb'], number_format($row['rows']));
}

// ──────────────────────────────────────────
// MySQL CONFIG RECOMMENDATIONS
// ──────────────────────────────────────────
echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n🔧 MySQL Config Recommendations:\n\n";

$vars = $mysqli->query("SHOW VARIABLES WHERE Variable_name IN ('innodb_buffer_pool_size', 'max_connections', 'table_open_cache', 'query_cache_size', 'innodb_flush_log_at_trx_commit')");
$config = [];
while ($v = $vars->fetch_assoc()) { $config[$v['Variable_name']] = (int)$v['Value']; }

$totalRam = $config['innodb_buffer_pool_size'] > 0 ? round($config['innodb_buffer_pool_size'] / 1048576) : 128;
echo "   Current innodb_buffer_pool_size: {$totalRam} MB\n";

$dbSize = $mysqli->query("SELECT SUM(DATA_LENGTH + INDEX_LENGTH) / 1048576 AS total FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$db_name}'")->fetch_assoc()['total'];
echo "   Database size: " . round($dbSize, 1) . " MB\n";
$recommendedPool = max(256, round($dbSize * 1.5));
echo "   💡 Recommended buffer_pool: {$recommendedPool} MB (1.5× database size)\n";

if ($totalRam < $dbSize) {
    echo "   ⚠️  BUFFER POOL TOO SMALL! MySQL is reading from disk, not RAM.\n";
    echo "   Fix: SET GLOBAL innodb_buffer_pool_size = " . ($recommendedPool * 1048576) . ";\n";
}

echo "\n   table_open_cache: " . ($config['table_open_cache'] ?? '?') . "\n";
echo "   💡 Recommended: 2000 (avoids repeated open/close overhead)\n";

echo "</pre>\n";
