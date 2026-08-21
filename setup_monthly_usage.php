<?php
/**
 * Monthly Data Usage – One-time Setup Script
 *
 * Creates the two database tables required for per-customer monthly data
 * usage tracking. Safe to run multiple times (uses CREATE TABLE IF NOT EXISTS).
 *
 * Run once via browser or CLI:
 *   php setup_monthly_usage.php
 */

require_once 'init.php';
require_once 'system/helpers/monthly_usage.php';

echo "<pre>\n";
echo "=== Monthly Data Usage Setup ===\n\n";

try {
    monthly_usage_ensure_tables();
    echo "✓ Table tbl_customer_monthly_usage   — OK\n";
    echo "✓ Table tbl_customer_session_snapshot — OK\n\n";
    echo "Setup complete. You can now delete this file.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>\n";
