<?php

/**
 * Server Statistics Controller
 */

_admin();
$admin = _admin();

switch ($action) {
    case 'get':
        // CPU Usage
        $cpu_load = sys_getloadavg();
        $cpu_usage = round($cpu_load[0] * 100);

        // Memory Usage
        $mem_info = array();
        if (is_readable('/proc/meminfo')) {
            $mem_file = fopen('/proc/meminfo', 'r');
            while ($line = fgets($mem_file)) {
                $pieces = array();
                if (preg_match('/^(\w+):\s+(\d+)\s/', $line, $pieces)) {
                    $mem_info[$pieces[1]] = $pieces[2];
                }
            }
            fclose($mem_file);
        }

        $total_mem = isset($mem_info['MemTotal']) ? round($mem_info['MemTotal'] / 1024 / 1024, 2) : 0;
        $free_mem = isset($mem_info['MemAvailable']) ? round($mem_info['MemAvailable'] / 1024 / 1024, 2) : 0;
        $used_mem = $total_mem - $free_mem;
        $mem_usage = $total_mem > 0 ? round(($used_mem / $total_mem) * 100, 2) : 0;

        // Disk Usage
        $disk_total = round(disk_total_space('/') / 1024 / 1024 / 1024, 2); // GB
        $disk_free = round(disk_free_space('/') / 1024 / 1024 / 1024, 2); // GB
        $disk_used = round($disk_total - $disk_free, 2);

        $response = array(
            'cpu_usage' => $cpu_usage,
            'memory_usage' => $mem_usage,
            'storage_used' => $disk_used,
            'storage_total' => $disk_total
        );

        header('Content-Type: application/json');
        echo json_encode($response);
        break;

    default:
        _alert(Lang::T('Invalid action'), 'danger', "dashboard");
        break;
}
