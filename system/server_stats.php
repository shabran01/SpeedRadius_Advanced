<?php

function getServerStatistics() {
    $stats = array();
    
    // CPU Usage using top command for more accurate real-time measurement
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows system
        $cmd = 'wmic cpu get loadpercentage';
        $output = [];
        exec($cmd, $output);
        if (isset($output[1])) {
            $cpu_usage = intval(trim($output[1]));
        } else {
            $cpu_usage = 0;
        }
    } else {
        // Linux system
        $cmd = "top -bn1 | grep 'Cpu(s)' | sed 's/.*, *\\([0-9.]*\\)%* id.*/\\1/' | awk '{print 100 - $1}'";
        $cpu_usage = floatval(trim(shell_exec($cmd)));
    }
    
    $stats['cpu'] = array(
        'load_1min' => min(100, round($cpu_usage, 2)),
        'load_5min' => min(100, round($cpu_usage, 2)), // Using same value for consistency
        'load_15min' => min(100, round($cpu_usage, 2))  // Using same value for consistency
    );
    
    // Memory Usage
    $mem_info = array();
    if (is_readable('/proc/meminfo')) {
        $mem_file = file_get_contents('/proc/meminfo');
        preg_match_all('/^(.+?):[ \t]+(\d+)/m', $mem_file, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $mem_info[$match[1]] = $match[2];
        }
        
        $total_mem = isset($mem_info['MemTotal']) ? $mem_info['MemTotal'] : 0;
        $free_mem = isset($mem_info['MemFree']) ? $mem_info['MemFree'] : 0;
        $buffers = isset($mem_info['Buffers']) ? $mem_info['Buffers'] : 0;
        $cached = isset($mem_info['Cached']) ? $mem_info['Cached'] : 0;
        
        $used_mem = $total_mem - $free_mem - $buffers - $cached;
        
        $stats['memory'] = array(
            'total' => round($total_mem / 1024, 2), // Convert to MB
            'used' => round($used_mem / 1024, 2),
            'free' => round(($free_mem + $buffers + $cached) / 1024, 2),
            'percentage' => round(($used_mem / $total_mem) * 100, 2)
        );
    } else {
        // Fallback for Windows or when /proc/meminfo is not available
        $mem_info = array();
        if (function_exists('shell_exec')) {
            $free = shell_exec('systeminfo | find "Available Physical Memory"');
            $total = shell_exec('systeminfo | find "Total Physical Memory"');
            
            // Extract values (assumes output in MB)
            preg_match('/(\d+,?\d*)/', $total, $total_match);
            preg_match('/(\d+,?\d*)/', $free, $free_match);
            
            $total_mem = str_replace(',', '', $total_match[1]);
            $free_mem = str_replace(',', '', $free_match[1]);
            $used_mem = $total_mem - $free_mem;
            
            $stats['memory'] = array(
                'total' => round($total_mem, 2),
                'used' => round($used_mem, 2),
                'free' => round($free_mem, 2),
                'percentage' => round(($used_mem / $total_mem) * 100, 2)
            );
        }
    }
    
    // Disk Usage
    $disk_total = disk_total_space('/');
    $disk_free = disk_free_space('/');
    $disk_used = $disk_total - $disk_free;
    
    $stats['disk'] = array(
        'total' => round($disk_total / 1073741824, 2), // Convert to GB
        'used' => round($disk_used / 1073741824, 2),
        'free' => round($disk_free / 1073741824, 2),
        'percentage' => round(($disk_used / $disk_total) * 100, 2)
    );
    
    return $stats;
}

function formatSize($size) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}
?>
