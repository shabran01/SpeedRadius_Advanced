<?php

function getServerStatistics() {
    $stats = array();
    $isLinux = strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN';
    $hasShellExec = function_exists('shell_exec') && strpos(ini_get('disable_functions'), 'shell_exec') === false;
    
    // ---- CPU Usage ----
    $cpu_usage = 0;
    
    if ($isLinux && $hasShellExec) {
        // Try multiple top command formats (some distros have "Cpu(s)", others "%Cpu(s)")
        $cmd = "top -bn1 2>/dev/null | grep -oP '(?<=\\s)\\d+\\.?\\d*(?=\\s*id)' | head -1";
        $idle = @shell_exec($cmd);
        if ($idle !== null && is_numeric(trim($idle))) {
            $cpu_usage = round(100 - floatval(trim($idle)), 2);
        }
    }
    
    if ($isLinux && $cpu_usage <= 0) {
        // Fallback 1: sys_getloadavg() — built-in PHP, no shell_exec needed
        if (function_exists('sys_getloadavg')) {
            $cores = 1;
            if ($hasShellExec) {
                $nproc = @shell_exec('nproc 2>/dev/null');
                if ($nproc !== null) $cores = max(1, intval(trim($nproc)));
            } elseif (is_readable('/proc/cpuinfo')) {
                $cpuinfo = @file_get_contents('/proc/cpuinfo');
                $cores = max(1, substr_count($cpuinfo, 'processor'));
            }
            $load = sys_getloadavg();
            $cpu_usage = min(100, round(($load[0] / $cores) * 100, 2));
        }
    }
    
    if (!$isLinux && $hasShellExec) {
        $cmd = 'wmic cpu get loadpercentage';
        $output = [];
        @exec($cmd, $output);
        if (isset($output[1])) {
            $cpu_usage = intval(trim($output[1]));
        }
    }
    
    $stats['cpu'] = array(
        'load_1min' => min(100, round($cpu_usage, 2)),
        'load_5min' => min(100, round($cpu_usage, 2)),
        'load_15min' => min(100, round($cpu_usage, 2))
    );
    
    // ---- Memory Usage ----
    // Default values so template always has something to display
    $stats['memory'] = array('total' => 0, 'used' => 0, 'free' => 0, 'percentage' => 0);
    $memRaw = false;
    
    if ($isLinux) {
        // Method 1: file_get_contents (may be blocked by open_basedir / stream wrappers)
        $memRaw = @file_get_contents('/proc/meminfo');
        
        // Method 2: fopen+fread (bypasses stream wrapper restrictions)
        if ($memRaw === false) {
            $fh = @fopen('/proc/meminfo', 'r');
            if ($fh) { $memRaw = @stream_get_contents($fh); @fclose($fh); }
        }
        
        // Method 3: file() — line-by-line array, sometimes not blocked
        if ($memRaw === false) {
            $lines = @file('/proc/meminfo', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines !== false) { $memRaw = implode("\n", $lines); }
        }
        
        // Method 4: readfile into output buffer
        if ($memRaw === false) {
            @ob_start();
            $ok = @readfile('/proc/meminfo');
            $memRaw = @ob_get_clean();
            if ($ok === false || empty($memRaw)) { $memRaw = false; }
        }
        
        // Method 5: shell commands (cat is a built-in, always available)
        $shellCmds = ['cat /proc/meminfo 2>/dev/null', 'free -m 2>/dev/null', 'free 2>/dev/null | head -3'];
        $hasAnyExec = function_exists('exec') || function_exists('shell_exec') || function_exists('system');
        
        if ($memRaw === false && $hasAnyExec) {
            foreach ($shellCmds as $shCmd) {
                $out = null;
                if (function_exists('exec')) {
                    $lines = []; @exec($shCmd, $lines); $out = $lines ? implode("\n", $lines) : null;
                }
                if (($out === null || $out === '') && function_exists('shell_exec')) {
                    $out = @shell_exec($shCmd);
                }
                if (($out === null || $out === '') && function_exists('system')) {
                    @ob_start(); @system($shCmd); $out = @ob_get_clean();
                }
                if (!empty(trim($out ?? ''))) {
                    $memRaw = $out;
                    break;
                }
            }
        }
        
        // Parse meminfo content (from any method)
        if ($memRaw !== false && !empty(trim($memRaw))) {
            // Detect if it's 'free -m' output (starts with "total" header line)
            if (preg_match('/^\s*total\s+used\s+free/m', $memRaw)) {
                // 'free -m' output format
                $lines = explode("\n", trim($memRaw));
                if (isset($lines[1])) {
                    $cols = preg_split('/\s+/', trim($lines[1]));
                    if (count($cols) >= 4) {
                        $total_mem = (int)$cols[1];
                        $used_mem  = (int)$cols[2];
                        $free_mem  = (int)$cols[3];
                        $stats['memory'] = array(
                            'total'      => round($total_mem, 2),
                            'used'       => round($used_mem, 2),
                            'free'       => round($free_mem, 2),
                            'percentage' => $total_mem > 0 ? round(($used_mem / $total_mem) * 100, 2) : 0
                        );
                    }
                }
            } else {
                // /proc/meminfo format
                $mem_info = array();
                preg_match_all('/^(.+?):[ \t]+(\d+)/m', $memRaw, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $mem_info[$match[1]] = $match[2];
                }
                $total_mem = isset($mem_info['MemTotal']) ? (int)$mem_info['MemTotal'] : 0;
                $free_mem  = isset($mem_info['MemFree'])  ? (int)$mem_info['MemFree']  : 0;
                $buffers   = isset($mem_info['Buffers'])   ? (int)$mem_info['Buffers']   : 0;
                $cached    = isset($mem_info['Cached'])    ? (int)$mem_info['Cached']    : 0;
                
                if ($total_mem > 0) {
                    $used_mem = $total_mem - $free_mem - $buffers - $cached;
                    $stats['memory'] = array(
                        'total'      => round($total_mem / 1024, 2),
                        'used'       => round($used_mem / 1024, 2),
                        'free'       => round(($free_mem + $buffers + $cached) / 1024, 2),
                        'percentage' => $total_mem > 0 ? round(($used_mem / $total_mem) * 100, 2) : 0
                    );
                }
            }
        }
    }
    
    // Windows fallback
    if (!$isLinux && $stats['memory']['total'] <= 0) {
        if ($hasShellExec) {
            $total = @shell_exec('systeminfo | find "Total Physical Memory"');
            $free  = @shell_exec('systeminfo | find "Available Physical Memory"');
            preg_match('/(\d+,?\d*)/', $total, $tm);
            preg_match('/(\d+,?\d*)/', $free, $fm);
            $total_mem = isset($tm[1]) ? (int)str_replace(',', '', $tm[1]) : 0;
            $free_mem  = isset($fm[1]) ? (int)str_replace(',', '', $fm[1]) : 0;
            if ($total_mem > 0) {
                $used_mem = $total_mem - $free_mem;
                $stats['memory'] = array(
                    'total'      => round($total_mem, 2),
                    'used'       => round($used_mem, 2),
                    'free'       => round($free_mem, 2),
                    'percentage' => round(($used_mem / $total_mem) * 100, 2)
                );
            }
        }
    }
    
    // ---- Disk Usage ----
    $disk_total = @disk_total_space('/');
    $disk_free  = @disk_free_space('/');
    if ($disk_total === false || $disk_total <= 0) {
        $disk_total = 0;
        $disk_free  = 0;
        $disk_used  = 0;
    } else {
        if ($disk_free === false) $disk_free = 0;
        $disk_used = $disk_total - $disk_free;
    }
    
    $stats['disk'] = array(
        'total'      => $disk_total > 0 ? round($disk_total / 1073741824, 2) : 0,
        'used'       => $disk_total > 0 ? round($disk_used / 1073741824, 2) : 0,
        'free'       => $disk_total > 0 ? round($disk_free / 1073741824, 2) : 0,
        'percentage' => $disk_total > 0 ? round(($disk_used / $disk_total) * 100, 2) : 0
    );
    
    return $stats;
}

function formatSize($size) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}
?>
