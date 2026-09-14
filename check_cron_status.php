<?php
include "init.php";

echo "=== Cron Job Status Check ===\n";

// Check last cron run time
$timestampFile = "$UPLOAD_PATH/cron_last_run.txt";
if (file_exists($timestampFile)) {
    $lastRun = file_get_contents($timestampFile);
    $lastRunDate = date('Y-m-d H:i:s', $lastRun);
    echo "Last cron run: $lastRunDate\n";
    
    $timeDiff = time() - $lastRun;
    echo "Time since last run: " . floor($timeDiff / 3600) . " hours " . floor(($timeDiff % 3600) / 60) . " minutes\n";
    
    if ($timeDiff > 7200) { // 2 hours
        echo "⚠ WARNING: Cron job hasn't run in over 2 hours!\n";
    }
} else {
    echo "❌ Cron has never run (timestamp file not found)\n";
}

// Check for lock file (stuck cron)
$lockFile = "$CACHE_PATH/router_monitor.lock";
if (file_exists($lockFile)) {
    echo "⚠ Lock file exists - cron may be stuck\n";
    $lockTime = filemtime($lockFile);
    $lockAge = time() - $lockTime;
    echo "Lock file age: " . floor($lockAge / 60) . " minutes\n";
    
    if ($lockAge > 300) { // 5 minutes
        echo "❌ Lock file is old - removing it\n";
        unlink($lockFile);
        echo "✓ Lock file removed\n";
    }
} else {
    echo "✓ No lock file found\n";
}

// Get customers that should be expired (date only check)
$candidates = ORM::for_table('tbl_user_recharges')
    ->where('status', 'on')
    ->where_lte('expiration', date("Y-m-d"))
    ->find_many();

$now = time();
$trulyExpired   = [];
$notYetByTime   = [];

foreach ($candidates as $row) {
    $expiresAt = strtotime($row['expiration'] . ' ' . $row['time']);
    if ($now >= $expiresAt) {
        $trulyExpired[] = $row;
    } else {
        $notYetByTime[] = $row;
    }
}

echo "\n=== Expiration Status ===\n";
echo "Candidates (date expired): " . count($candidates) . "\n";
echo "  ↳ Truly overdue (date+time passed): " . count($trulyExpired) . "\n";
echo "  ↳ Expiring later today (time not yet): " . count($notYetByTime) . "\n";

if (count($trulyExpired) > 0) {
    echo "\n⚠ " . count($trulyExpired) . " customer(s) overdue — cron is running but router removal keeps failing:\n";
    echo str_pad("Username", 20) . str_pad("Router", 20) . str_pad("Expired At", 22) . "Plan\n";
    echo str_repeat("-", 80) . "\n";
    foreach ($trulyExpired as $row) {
        $router = ORM::for_table('tbl_routers')->where('name', $row['routers'])->find_one();
        $routerStatus = $router ? ($router['status'] ?? 'Unknown') : '❌ NOT FOUND';
        $expiresAt = $row['expiration'] . ' ' . $row['time'];
        echo str_pad($row['username'], 20)
           . str_pad($row['routers'] . ' [' . $routerStatus . ']', 25)
           . str_pad($expiresAt, 22)
           . $row['namebp'] . "\n";
    }
    echo "\n  → If router shows [Offline] that is WHY removal keeps failing.\n";
    echo "  → Fix: restore router connectivity and cron will auto-expire them.\n";
    echo "  → Or force-expire now: visit expire_customer.php?username=USERNAME\n";
} else {
    echo "\n✓ All overdue customers are up to date\n";
}

if (count($notYetByTime) > 0) {
    echo "\n--- Expiring later today ---\n";
    foreach ($notYetByTime as $row) {
        echo "  " . $row['username'] . " → expires at " . $row['expiration'] . ' ' . $row['time'] . " (" . $row['namebp'] . ")\n";
    }
}

echo "\n=== Recommended Actions ===\n";
echo "1. Cron job (already running — ensure it stays scheduled):\n";
echo "   * * * * * /usr/bin/php " . __DIR__ . "/system/cron.php\n";
echo "2. Run manually to see live output: php " . __DIR__ . "/system/cron.php\n";
echo "3. Force-expire one customer: visit expire_customer.php?username=USERNAME\n";
?>
