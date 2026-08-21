<?php
/**
 * Session Logger Diagnostic — DELETE THIS FILE after debugging
 * Visit: https://isp.speedcomwifi.xyz/debug_session_log.php
 */

include "init.php";
_admin(); // require admin login

echo "<pre style='background:#111;color:#0f0;padding:20px;font-size:13px;'>";
echo "=== Session Logger Diagnostic ===\n\n";

require_once 'system/autoload/Mikrotik.php';
require_once 'system/autoload/PEAR2/Autoload.php';
require_once 'system/helpers/session_logger.php';

// 1. Table check
echo "--- 1. Ensuring table exists ---\n";
try {
    session_logger_ensure_table();
    echo "✓ tbl_customer_session_logs table OK\n";
} catch (\Throwable $e) {
    echo "✗ Table creation error: " . $e->getMessage() . "\n";
}

// 2. Row count in DB
try {
    $total = ORM::for_table('tbl_customer_session_logs')->count();
    $open  = ORM::for_table('tbl_customer_session_logs')->where_null('disconnected_at')->count();
    echo "\n--- 2. DB records ---\n";
    echo "  Total session records : $total\n";
    echo "  Currently open (no disconnect_at) : $open\n";
} catch (\Throwable $e) {
    echo "✗ DB query error: " . $e->getMessage() . "\n";
}

// 3. Active sessions on each router RIGHT NOW
echo "\n--- 3. Live sessions on each router ---\n";
$routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
echo "  Enabled routers: " . count($routers) . "\n";

foreach ($routers as $router) {
    echo "\n  Router: {$router['name']} ({$router['ip_address']})\n";
    try {
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        if (!$client) { echo "  ✗ getClient() returned null\n"; continue; }
        echo "  ✓ Connected\n";

        // Hotspot
        try {
            $req   = new PEAR2\Net\RouterOS\Request('/ip hotspot active print');
            $resps = $client->sendSync($req);
            $hs    = [];
            foreach ($resps as $r) {
                if ($r->getType() !== PEAR2\Net\RouterOS\Response::TYPE_DATA) continue;
                $u = $r->getProperty('user');
                if (!empty($u)) $hs[] = $u . ' (' . $r->getProperty('address') . ')';
            }
            echo "  Hotspot online (" . count($hs) . "): " . (empty($hs) ? 'none' : implode(', ', $hs)) . "\n";
        } catch (\Throwable $e) {
            echo "  Hotspot: " . $e->getMessage() . "\n";
        }

        // PPPoE
        try {
            $req   = new PEAR2\Net\RouterOS\Request('/ppp/active/print');
            $resps = $client->sendSync($req);
            $pp    = [];
            foreach ($resps as $r) {
                if ($r->getType() !== PEAR2\Net\RouterOS\Response::TYPE_DATA) continue;
                $u = $r->getProperty('name');
                if (!empty($u)) $pp[] = $u . ' (' . $r->getProperty('address') . ')';
            }
            echo "  PPPoE online   (" . count($pp) . "): " . (empty($pp) ? 'none' : implode(', ', $pp)) . "\n";
        } catch (\Throwable $e) {
            echo "  PPPoE: " . $e->getMessage() . "\n";
        }

    } catch (\Throwable $e) {
        echo "  ✗ Connection failed: " . $e->getMessage() . "\n";
    }
}

// 4. Run the snapshot
echo "\n--- 4. Running session_logger_snapshot() ---\n";
try {
    session_logger_snapshot();
    echo "\n✓ Snapshot complete\n";
} catch (\Throwable $e) {
    echo "\n✗ Snapshot error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

// 5. After snapshot — recount
try {
    $total2 = ORM::for_table('tbl_customer_session_logs')->count();
    $open2  = ORM::for_table('tbl_customer_session_logs')->where_null('disconnected_at')->count();
    echo "\n--- 5. DB records AFTER snapshot ---\n";
    echo "  Total session records : $total2\n";
    echo "  Currently open        : $open2\n";

    // Show last 5 records
    $last = ORM::for_table('tbl_customer_session_logs')->order_by_desc('id')->limit(5)->find_array();
    echo "\n  Last 5 records:\n";
    foreach ($last as $r) {
        echo "  [id={$r['id']}] {$r['session_type']} | {$r['username']} | customer_id={$r['customer_id']} | connected={$r['connected_at']} | disconnected=" . ($r['disconnected_at'] ?: 'open') . "\n";
    }
} catch (\Throwable $e) {
    echo "DB error: " . $e->getMessage() . "\n";
}

echo "\n=== Done ===\n";
echo "</pre>";
