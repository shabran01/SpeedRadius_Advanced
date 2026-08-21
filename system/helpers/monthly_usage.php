<?php

/**
 * Monthly Data Usage Tracking Helper
 *
 * Tracks per-customer download/upload bytes cumulated per month.
 * Data resets automatically each new month (stored by YYYY-MM key).
 *
 * Tables used:
 *  - tbl_customer_monthly_usage   : cumulative totals per customer per month
 *  - tbl_customer_session_snapshot: last-seen bytes per active session (for delta tracking)
 */

/**
 * Ensure the two tracking tables exist. Called from cron and setup scripts.
 */
function monthly_usage_ensure_tables()
{
    $db = ORM::get_db();

    $db->exec("
        CREATE TABLE IF NOT EXISTS `tbl_customer_monthly_usage` (
            `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `customer_id`     INT UNSIGNED NOT NULL,
            `username`        VARCHAR(64)  NOT NULL,
            `month`           CHAR(7)      NOT NULL COMMENT 'YYYY-MM',
            `download_bytes`  BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `upload_bytes`    BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `last_updated`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `ux_customer_month` (`customer_id`, `month`),
            INDEX `idx_month` (`month`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    $db->exec("
        CREATE TABLE IF NOT EXISTS `tbl_customer_session_snapshot` (
            `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `username`        VARCHAR(64)  NOT NULL,
            `router_id`       INT UNSIGNED NOT NULL DEFAULT 0,
            `last_bytes_in`   BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `last_bytes_out`  BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `last_checked`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `ux_user_router` (`username`, `router_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

/**
 * Accumulate bytes into this month's total for a customer.
 * Pass the raw delta (bytes added since last snapshot).
 *
 * @param int    $customer_id
 * @param string $username
 * @param int    $delta_in   bytes downloaded this interval
 * @param int    $delta_out  bytes uploaded this interval
 */
function monthly_usage_accumulate($customer_id, $username, $delta_in, $delta_out)
{
    if ($delta_in <= 0 && $delta_out <= 0) {
        return;
    }
    $month = date('Y-m');

    $existing = ORM::for_table('tbl_customer_monthly_usage')
        ->where('customer_id', (int)$customer_id)
        ->where('month', $month)
        ->find_one();

    if ($existing) {
        $existing->download_bytes = (int)$existing->download_bytes + (int)$delta_in;
        $existing->upload_bytes   = (int)$existing->upload_bytes   + (int)$delta_out;
        $existing->save();
    } else {
        $row = ORM::for_table('tbl_customer_monthly_usage')->create();
        $row->customer_id    = (int)$customer_id;
        $row->username       = $username;
        $row->month          = $month;
        $row->download_bytes = (int)$delta_in;
        $row->upload_bytes   = (int)$delta_out;
        $row->save();
    }
}

/**
 * Process one active session: compute the delta since last snapshot, accumulate
 * into monthly totals, then update the snapshot.
 *
 * @param int    $customer_id
 * @param string $username
 * @param int    $router_id
 * @param int    $current_bytes_in
 * @param int    $current_bytes_out
 */
function monthly_usage_process_session($customer_id, $username, $router_id,
                                        $current_bytes_in, $current_bytes_out)
{
    $snap = ORM::for_table('tbl_customer_session_snapshot')
        ->where('username', $username)
        ->where('router_id', (int)$router_id)
        ->find_one();

    if ($snap) {
        // If current bytes are lower than last snapshot, a session restart occurred –
        // treat the full current bytes as new delta.
        $delta_in  = ($current_bytes_in  >= (int)$snap->last_bytes_in)
            ? ($current_bytes_in  - (int)$snap->last_bytes_in)
            : $current_bytes_in;
        $delta_out = ($current_bytes_out >= (int)$snap->last_bytes_out)
            ? ($current_bytes_out - (int)$snap->last_bytes_out)
            : $current_bytes_out;

        $snap->last_bytes_in  = $current_bytes_in;
        $snap->last_bytes_out = $current_bytes_out;
        $snap->save();
    } else {
        // First time we see this session — treat the whole value as a delta
        // (session may have started before the cron was deployed).
        $delta_in  = $current_bytes_in;
        $delta_out = $current_bytes_out;

        $snap = ORM::for_table('tbl_customer_session_snapshot')->create();
        $snap->username      = $username;
        $snap->router_id     = (int)$router_id;
        $snap->last_bytes_in  = $current_bytes_in;
        $snap->last_bytes_out = $current_bytes_out;
        $snap->save();
    }

    monthly_usage_accumulate($customer_id, $username, $delta_in, $delta_out);
}

/**
 * Remove session snapshot when a session is known to have ended.
 *
 * @param string $username
 * @param int    $router_id
 */
function monthly_usage_clear_session($username, $router_id)
{
    ORM::for_table('tbl_customer_session_snapshot')
        ->where('username', $username)
        ->where('router_id', (int)$router_id)
        ->delete_many();
}

/**
 * Get monthly usage rows for a customer.
 * Returns an array ordered newest-first.
 *
 * @param  int  $customer_id
 * @param  int  $limit  how many months to return (default 12)
 * @return array
 */
function monthly_usage_get($customer_id, $limit = 12)
{
    return ORM::for_table('tbl_customer_monthly_usage')
        ->where('customer_id', (int)$customer_id)
        ->order_by_desc('month')
        ->limit($limit)
        ->find_array();
}

/**
 * Get the current month's usage for a customer.
 *
 * @param  int $customer_id
 * @return array|false
 */
function monthly_usage_current($customer_id)
{
    return ORM::for_table('tbl_customer_monthly_usage')
        ->where('customer_id', (int)$customer_id)
        ->where('month', date('Y-m'))
        ->find_one();
}

/**
 * Format bytes into a human-readable string (B / KB / MB / GB).
 *
 * @param  int $bytes
 * @return string
 */
function monthly_usage_format_bytes($bytes)
{
    $bytes = (int)$bytes;
    if ($bytes >= 1073741824) {
        return round($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' B';
}

// ─────────────────────────────────────────────────────────────────────────────
// Router-level WAN usage tracking
// Tracks total WAN bytes per router per month — persists across reboots.
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Ensure the two router-level tracking tables exist.
 */
function monthly_usage_ensure_router_table()
{
    $db = ORM::get_db();

    $db->exec("
        CREATE TABLE IF NOT EXISTS `tbl_router_monthly_usage` (
            `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `router_id`       INT UNSIGNED NOT NULL,
            `router_name`     VARCHAR(128) NOT NULL DEFAULT '',
            `month`           CHAR(7)      NOT NULL COMMENT 'YYYY-MM',
            `download_bytes`  BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `upload_bytes`    BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `last_updated`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `ux_router_month` (`router_id`, `month`),
            INDEX `idx_month` (`month`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    $db->exec("
        CREATE TABLE IF NOT EXISTS `tbl_router_wan_snapshot` (
            `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `router_id`       INT UNSIGNED NOT NULL UNIQUE,
            `last_rx_bytes`   BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `last_tx_bytes`   BIGINT UNSIGNED NOT NULL DEFAULT 0,
            `last_checked`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

/**
 * Process WAN interface bytes for a router.
 * Computes the delta since last snapshot (handles counter resets on reboot),
 * then accumulates the delta into this month's total.
 *
 * @param int    $router_id
 * @param string $router_name
 * @param int    $rx_bytes   current raw RX byte counter from MikroTik WAN interface
 * @param int    $tx_bytes   current raw TX byte counter from MikroTik WAN interface
 */
function monthly_usage_process_router_wan($router_id, $router_name, $rx_bytes, $tx_bytes)
{
    $snap = ORM::for_table('tbl_router_wan_snapshot')
        ->where('router_id', (int)$router_id)
        ->find_one();

    if ($snap) {
        // If current < last, the router rebooted — treat current value as new delta
        $delta_rx = ($rx_bytes >= (int)$snap->last_rx_bytes)
            ? ($rx_bytes - (int)$snap->last_rx_bytes)
            : $rx_bytes;
        $delta_tx = ($tx_bytes >= (int)$snap->last_tx_bytes)
            ? ($tx_bytes - (int)$snap->last_tx_bytes)
            : $tx_bytes;

        $snap->last_rx_bytes = $rx_bytes;
        $snap->last_tx_bytes = $tx_bytes;
        $snap->save();
    } else {
        // First time seeing this router — count full current value as delta
        $delta_rx = $rx_bytes;
        $delta_tx = $tx_bytes;

        $snap = ORM::for_table('tbl_router_wan_snapshot')->create();
        $snap->router_id     = (int)$router_id;
        $snap->last_rx_bytes = $rx_bytes;
        $snap->last_tx_bytes = $tx_bytes;
        $snap->save();
    }

    if ($delta_rx <= 0 && $delta_tx <= 0) {
        return;
    }

    $month    = date('Y-m');
    $existing = ORM::for_table('tbl_router_monthly_usage')
        ->where('router_id', (int)$router_id)
        ->where('month', $month)
        ->find_one();

    if ($existing) {
        $existing->download_bytes = (int)$existing->download_bytes + (int)$delta_rx;
        $existing->upload_bytes   = (int)$existing->upload_bytes   + (int)$delta_tx;
        $existing->router_name    = $router_name;
        $existing->save();
    } else {
        $row = ORM::for_table('tbl_router_monthly_usage')->create();
        $row->router_id      = (int)$router_id;
        $row->router_name    = $router_name;
        $row->month          = $month;
        $row->download_bytes = (int)$delta_rx;
        $row->upload_bytes   = (int)$delta_tx;
        $row->save();
    }
}

/**
 * Get total network-wide download + upload bytes for a given month,
 * summed across all routers.
 *
 * @param  string|null $month  'YYYY-MM' — defaults to current month
 * @return array  ['download_bytes' => int, 'upload_bytes' => int, 'total_bytes' => int]
 */
function monthly_usage_get_network_totals($month = null)
{
    if ($month === null) {
        $month = date('Y-m');
    }
    try {
        $db   = ORM::get_db();
        $stmt = $db->prepare("
            SELECT COALESCE(SUM(download_bytes), 0) AS dl,
                   COALESCE(SUM(upload_bytes),   0) AS ul
            FROM tbl_router_monthly_usage
            WHERE month = ?
        ");
        $stmt->execute([$month]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $dl  = (int)($row['dl'] ?? 0);
        $ul  = (int)($row['ul'] ?? 0);
        return [
            'download_bytes' => $dl,
            'upload_bytes'   => $ul,
            'total_bytes'    => $dl + $ul,
        ];
    } catch (Exception $e) {
        return ['download_bytes' => 0, 'upload_bytes' => 0, 'total_bytes' => 0];
    }
}
