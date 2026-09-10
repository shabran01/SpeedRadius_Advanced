<?php
/**
 * System Info plugin — cache writer.
 *
 * Writes sysinfo_uptime.txt and sysinfo_mem.txt into system/cache for the
 * System Info plugin. Needed on servers where /proc is blocked for the
 * web user (AppArmor) or where shell_exec/exec are disabled.
 *
 * Run as ROOT every minute via crontab, e.g.:
 *   * * * * * php /var/www/isp/system/cron_sysinfo.php
 */

$cacheDir = __DIR__ . '/cache';
if (!is_dir($cacheDir)) {
    @mkdir($cacheDir, 0755, true);
}
$uptimeFile = $cacheDir . '/sysinfo_uptime.txt';
$memFile    = $cacheDir . '/sysinfo_mem.txt';

// Uptime: first token of /proc/uptime (seconds since boot)
$uptime = @file_get_contents('/proc/uptime');
if ($uptime !== false && trim($uptime) !== '') {
    @file_put_contents($uptimeFile, trim($uptime));
}

// Memory: free -m output (plugin parses the "Mem:" line)
$free = @shell_exec('free -m 2>/dev/null');
if (!$free || trim($free) === '') {
    $out = [];
    @exec('free -m 2>/dev/null', $out);
    $free = $out ? implode("\n", $out) : '';
}
if ($free && trim($free) !== '') {
    @file_put_contents($memFile, trim($free));
}

echo "System info cache updated.\n";
