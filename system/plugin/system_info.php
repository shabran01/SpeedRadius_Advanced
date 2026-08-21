<?php
register_menu("System Info", true, "system_info", 'SETTINGS', '');

function system_info()
{
    global $ui;
    _admin();
    $ui->assign('_title', 'System Information');
    $ui->assign('_system_menu', 'settings');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reload']) && $_POST['reload'] === 'true') {
    $output = array();
    $retcode = 0;

    // CSRF protection
    if (!Csrf::check(_post('csrf_token'))) {
        $output = array('Invalid or Expired CSRF Token');
        $retcode = 1;
    } else {
        $os = strtoupper(PHP_OS);

        if (strpos($os, 'WIN') === 0) {
            // Windows OS
            exec('net stop freeradius', $output, $retcode);
            exec('net start freeradius', $output, $retcode);
        } else {
            // Linux OS — try systemctl without sudo first, then sudo (non-interactive), then service
            exec('systemctl restart freeradius.service 2>&1', $output, $retcode);
            if ($retcode !== 0) {
                exec('sudo -n systemctl restart freeradius.service 2>&1', $output, $retcode);
            }
            if ($retcode !== 0) {
                exec('service freeradius restart 2>&1', $output, $retcode);
            }
        }
    }
    $ui->assign('output', is_array($output) ? implode("\n", $output) : $output);
    $ui->assign('returnCode', $retcode);
}

function system_info_cache_dir()
{
    global $CACHE_PATH;
    if (!empty($CACHE_PATH)) {
        return rtrim($CACHE_PATH, "/\\");
    }
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'cache';
}

  function system_info_get_server_memory_usage()
  {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows system
        $output = array();
        exec('wmic OS get TotalVisibleMemorySize, FreePhysicalMemory /Value', $output);

        $total_memory = null;
        $free_memory = null;

        foreach ($output as $line) {
            if (strpos($line, 'TotalVisibleMemorySize') !== false) {
                $total_memory = intval(preg_replace('/[^0-9]/', '', $line));
            } elseif (strpos($line, 'FreePhysicalMemory') !== false) {
                $free_memory = intval(preg_replace('/[^0-9]/', '', $line));
            }

            if ($total_memory !== null && $free_memory !== null) {
                break;
            }
        }

        if ($total_memory !== null && $free_memory !== null) {
            $total_memory = round($total_memory / 1024);
            $free_memory = round($free_memory / 1024);
            $used_memory = $total_memory - $free_memory;
            $memory_usage_percentage = round($used_memory / $total_memory * 100);

            $memory_usage = [
                'total' => $total_memory,
                'free' => $free_memory,
                'used' => $used_memory,
                'used_percentage' => round($memory_usage_percentage),
            ];

            return $memory_usage;
        }
    } else {
        // Linux - use cron-populated temp files first (/proc blocked by AppArmor)
        
        // Primary: read from cron cache files
        $memFile = system_info_cache_dir() . '/sysinfo_mem.txt';
        if (is_readable($memFile) && (time() - filemtime($memFile)) < 120) {
            $lines = @file($memFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines && isset($lines[1])) {
                $cols = preg_split('/\s+/', trim($lines[1]));
                if (count($cols) >= 7) {
                    return [
                        'total' => (int)$cols[1],
                        'free' => (int)$cols[6],
                        'used' => (int)$cols[2],
                        'used_percentage' => ($cols[1] > 0) ? round(((int)$cols[2] / (int)$cols[1]) * 100) : 0,
                    ];
                }
            }
        }
        
        // Method 1: exec('free -m')
        $output = [];
        $ret = -1;
        @exec('free -m 2>/dev/null', $output, $ret);
        if ($ret === 0 && isset($output[1])) {
            $cols = preg_split('/\s+/', trim($output[1]));
            if (count($cols) >= 7) {
                return [
                    'total' => (int)$cols[1],
                    'free' => (int)$cols[6],   // MemAvailable
                    'used' => (int)$cols[2],
                    'used_percentage' => ($cols[1] > 0) ? round(((int)$cols[2] / (int)$cols[1]) * 100) : 0,
                ];
            }
        }
        
        // Method 2: shell_exec('free -m')
        if (function_exists('shell_exec')) {
            $free = @shell_exec('free -m 2>/dev/null');
            if ($free && trim($free) !== '') {
                $free_arr = explode("\n", trim($free));
                if (isset($free_arr[1])) {
                    $cols = preg_split('/\s+/', trim($free_arr[1]));
                    if (count($cols) >= 7) {
                        return [
                            'total' => (int)$cols[1],
                            'free' => (int)$cols[6],
                            'used' => (int)$cols[2],
                            'used_percentage' => ($cols[1] > 0) ? round(((int)$cols[2] / (int)$cols[1]) * 100) : 0,
                        ];
                    }
                }
            }
        }
        
        // Method 3: /proc/meminfo
        $meminfo = @file_get_contents('/proc/meminfo');
        if ($meminfo) {
            $total = $free = $available = 0;
            if (preg_match('/MemTotal:\s+(\d+)\s+kB/', $meminfo, $m)) $total = (int)$m[1];
            if (preg_match('/MemFree:\s+(\d+)\s+kB/', $meminfo, $m)) $free = (int)$m[1];
            if (preg_match('/MemAvailable:\s+(\d+)\s+kB/', $meminfo, $m)) $available = (int)$m[1];
            if ($total > 0) {
                $total_mb = round($total / 1024);
                $free_mb = round(($available > 0 ? $available : $free) / 1024);
                $used_mb = $total_mb - $free_mb;
                return [
                    'total' => $total_mb,
                    'free' => $free_mb,
                    'used' => $used_mb,
                    'used_percentage' => ($total_mb > 0) ? round(($used_mb / $total_mb) * 100) : 0,
                ];
            }
        }
        
        // Method 4: popen('free -m')
        if (function_exists('popen')) {
            $handle = @popen('free -m 2>/dev/null', 'r');
            if ($handle) {
                $output = stream_get_contents($handle);
                pclose($handle);
                if ($output) {
                    $lines = explode("\n", trim($output));
                    if (isset($lines[1])) {
                        $cols = preg_split('/\s+/', trim($lines[1]));
                        if (count($cols) >= 7) {
                            return [
                                'total' => (int)$cols[1],
                                'free' => (int)$cols[6],
                                'used' => (int)$cols[2],
                                'used_percentage' => ($cols[1] > 0) ? round(((int)$cols[2] / (int)$cols[1]) * 100) : 0,
                            ];
                        }
                    }
                }
            }
        }
    }

    return [
        'total' => 0,
        'free' => 0,
        'used' => 0,
        'used_percentage' => 0,
    ];
}

function system_info_get_cpu_usage() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $cmd = 'wmic cpu get loadpercentage,NumberOfLogicalProcessors';
        $output = [];
        exec($cmd, $output);
        
        if (isset($output[1])) {
            $values = preg_split('/\s+/', trim($output[1]));
            if (count($values) >= 2) {
                $load = intval($values[0]);
                $cores = intval($values[1]);
                if ($cores > 0) {
                    return min(100, round($load / $cores, 1)) . '%';
                }
            }
            return trim($values[0]) . '%';
        }
    } else {
        // Linux - try multiple methods
        
        // Method 1: shell_exec with top
        if (function_exists('shell_exec')) {
            $cmd = "top -bn1 2>/dev/null | grep -oP '%Cpu.*?id,\\s*\\K[0-9.]+' | awk '{printf \"%.1f\", 100 - $1}'";
            $cpu_usage = @shell_exec($cmd);
            if ($cpu_usage && trim($cpu_usage) !== '' && is_numeric(trim($cpu_usage))) {
                return round(floatval(trim($cpu_usage)), 1) . '%';
            }
        }
        
        // Method 2: sys_getloadavg() - PHP built-in, no /proc needed
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            $cores = system_info_get_cpu_cores();
            if ($load && $cores && is_numeric($cores) && $cores > 0) {
                $pct = round(($load[0] / (int)$cores) * 100, 1);
                return min(100, $pct) . '%';
            }
        }
        
        // Method 3: /proc/stat
        $stat = @file_get_contents('/proc/stat');
        if ($stat && preg_match('/^cpu\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/', $stat, $m)) {
            $total1 = $m[1] + $m[2] + $m[3] + $m[4];
            $idle1 = $m[4];
            usleep(100000);
            $stat2 = @file_get_contents('/proc/stat');
            if ($stat2 && preg_match('/^cpu\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/', $stat2, $m2)) {
                $total2 = $m2[1] + $m2[2] + $m2[3] + $m2[4];
                $idle2 = $m2[4];
                $totalDelta = $total2 - $total1;
                $idleDelta = $idle2 - $idle1;
                if ($totalDelta > 0) {
                    return round(($totalDelta - $idleDelta) / $totalDelta * 100, 1) . '%';
                }
            }
        }
    }
    return 'Unknown';
}

function system_info_get_cpu_cores() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $output = [];
        exec('wmic cpu get NumberOfCores', $output);
        if (isset($output[1])) {
            return trim($output[1]);
        }
    } else {
        // Linux - try shell_exec first, fall back to /proc/cpuinfo
        $cores = null;
        if (function_exists('shell_exec')) {
            $cores = @shell_exec('nproc 2>/dev/null');
        }
        if (!$cores || trim((string)$cores) === '') {
            $cpuinfo = @file_get_contents('/proc/cpuinfo');
            if ($cpuinfo) {
                $cores = substr_count($cpuinfo, 'processor');
            }
        }
        if ($cores) {
            return trim((string)$cores);
        }
    }
    return 'Unknown';
}

function system_info_getSystemInfo()
{
    $memory_usage = system_info_get_server_memory_usage();

    // Get the Idiorm ORM instance
    $db = ORM::getDb();
    $serverInfo = $db->getAttribute(PDO::ATTR_SERVER_VERSION);
    $databaseName = $db->query('SELECT DATABASE()')->fetchColumn();
    $serverName = gethostname();
    $shellExecEnabled = function_exists('shell_exec');

    // Fallback: Let's use $_SERVER['SERVER_NAME'] if gethostname() is not available
    if (!$serverName) {
        $serverName = $_SERVER['SERVER_NAME'];
    }

    // Retrieve the current time from the database
    $currentTime = $db->query('SELECT CURRENT_TIMESTAMP AS current_time_alias')->fetchColumn();

    $systemInfo = [
        'Server Name' => $serverName,
        'Operating System' => php_uname('s'),
        'System Distro' => system_info_getSystemDistro(),
        'CPU Cores' => system_info_get_cpu_cores(),
        'CPU Usage' => system_info_get_cpu_usage(),
        'PHP Version' => phpversion(),
        'Server Software' => $_SERVER['SERVER_SOFTWARE'],
        'Server IP Address' => $_SERVER['SERVER_ADDR'],
        'Server Port' => $_SERVER['SERVER_PORT'],
        'Remote IP Address' => $_SERVER['REMOTE_ADDR'],
        'Remote Port' => $_SERVER['REMOTE_PORT'],
        'Database Server' => $serverInfo,
        'Database Name' => $databaseName,
        'System Time' => date("F j, Y g:i a"),
        'Database Time' => date("F j, Y g:i a", strtotime($currentTime)),
        'Shell Exec Enabled' => $shellExecEnabled ? 'Yes' : 'No',
        'Server Uptime' => system_info_get_uptime(),
        // Add more system information here
    ];

    return $systemInfo;
}
//Lets get the storage usege
function system_info_get_disk_usage()
{
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows system
        $output = [];
        exec('wmic logicaldisk where "DeviceID=\'C:\'" get Size,FreeSpace /format:list', $output);

        if (!empty($output)) {
            $total_disk = 0;
            $free_disk = 0;

            foreach ($output as $line) {
                if (strpos($line, 'Size=') === 0) {
                    $total_disk = intval(substr($line, 5));
                } elseif (strpos($line, 'FreeSpace=') === 0) {
                    $free_disk = intval(substr($line, 10));
                }
            }

            $used_disk = $total_disk - $free_disk;
            $disk_usage_percentage = round(($used_disk / $total_disk) * 100, 2);

            $disk_usage = [
                'total' => system_info_format_bytes($total_disk),
                'used' => system_info_format_bytes($used_disk),
                'free' => system_info_format_bytes($free_disk),
                'used_percentage' => $disk_usage_percentage . '%',
            ];

            return $disk_usage;
        }
    } else {
        // Linux - try shell_exec first, fall back to PHP disk functions
        $disk = null;
        if (function_exists('shell_exec')) {
            $disk = @shell_exec('df / --output=size,used,avail,pcent --block-size=1 2>/dev/null');
        }
        
        if ($disk && trim($disk) !== '') {
            $disk = (string) trim($disk);
            $disk_arr = explode("\n", $disk);
            if (isset($disk_arr[1])) {
                $disk = explode(" ", preg_replace('/\s+/', ' ', $disk_arr[1]));
                $disk = array_filter($disk);
                $disk = array_merge($disk);

                if (isset($disk[0], $disk[1], $disk[2], $disk[3])) {
                    return [
                        'total' => system_info_format_bytes($disk[0]),
                        'used' => system_info_format_bytes($disk[1]),
                        'free' => system_info_format_bytes($disk[2]),
                        'used_percentage' => $disk[3],
                    ];
                }
            }
        }
        
        // Fallback: use PHP's disk_free_space / disk_total_space
        $total_disk = @disk_total_space('/');
        $free_disk = @disk_free_space('/');
        
        if ($total_disk && $free_disk) {
            $used_disk = $total_disk - $free_disk;
            $disk_usage_percentage = round(($used_disk / $total_disk) * 100, 2) . '%';

            return [
                'total' => system_info_format_bytes($total_disk),
                'used' => system_info_format_bytes($used_disk),
                'free' => system_info_format_bytes($free_disk),
                'used_percentage' => $disk_usage_percentage,
            ];
        }
    }

    return null;
}

function system_info_get_uptime() {
    // 1) Linux: prefer `uptime -p` (confirmed working on Ubuntu VPS)
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && function_exists('shell_exec')) {
        $raw = @shell_exec('/usr/bin/uptime -p 2>/dev/null');
        if (!$raw || trim($raw) === '') {
            $raw = @shell_exec('uptime -p 2>/dev/null');
        }
        if ($raw && preg_match('/up\s+(.+)/', trim($raw), $m)) {
            return 'up ' . trim($m[1]);
        }
    }

    // 2) Direct /proc read — no shell needed
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' && is_readable('/proc/uptime')) {
        $data = @file_get_contents('/proc/uptime');
        if ($data !== false) {
            $seconds = (int)explode(' ', trim($data))[0];
            if ($seconds > 0) return system_info_format_uptime($seconds);
        }
    }

    // 3) Cron cache — root cron writes this for servers where /proc is blocked (AppArmor)
    $uptimeFile = system_info_cache_dir() . '/sysinfo_uptime.txt';
    if (is_readable($uptimeFile) && filesize($uptimeFile) > 0 && (time() - filemtime($uptimeFile)) < 300) {
        $data = @file_get_contents($uptimeFile);
        if ($data !== false) {
            $seconds = (int)explode(' ', trim($data))[0];
            if ($seconds > 0) return system_info_format_uptime($seconds);
        }
    }

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows
        $output = [];
        exec('wmic os get lastbootuptime', $output);

        if (isset($output[1])) {
            $boot_time = substr($output[1], 0, 14);
            $boot_timestamp = mktime(
                substr($boot_time, 8, 2),
                substr($boot_time, 10, 2),
                substr($boot_time, 12, 2),
                substr($boot_time, 4, 2),
                substr($boot_time, 6, 2),
                substr($boot_time, 0, 4)
            );
            return system_info_format_uptime(time() - $boot_timestamp);
        }
        return 'Unknown';
    }

    // 4) exec fallback
    $output = [];
    @exec('cat /proc/uptime 2>/dev/null', $output);
    if (!empty($output) && isset($output[0])) {
        $seconds = (int)explode(' ', $output[0])[0];
        if ($seconds > 0) return system_info_format_uptime($seconds);
    }

    return 'Unknown';
}

function system_info_format_uptime($seconds) {
    $days = floor($seconds / 86400);
    $hours = floor(($seconds % 86400) / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    
    $uptime = '';
    if ($days > 0) {
        $uptime .= $days . ' days ';
    }
    if ($hours > 0) {
        $uptime .= $hours . ' hours ';
    }
    if ($minutes > 0) {
        $uptime .= $minutes . ' minutes';
    }
    return trim($uptime);
}

function system_info_format_bytes($bytes, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

function system_info_getSystemDistro()
{
    $distro = '';

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN') {
        // Try lsb_release via shell_exec first
        if (function_exists('shell_exec')) {
            $d = @shell_exec('lsb_release -d 2>/dev/null');
            if ($d) {
                $distro = trim(substr($d, strpos($d, ':') + 1));
            }
        }
        // Fallback: read /etc/os-release
        if (empty($distro)) {
            $osRelease = @file_get_contents('/etc/os-release');
            if ($osRelease) {
                if (preg_match('/^PRETTY_NAME="(.+)"$/m', $osRelease, $m)) {
                    $distro = trim($m[1]);
                } elseif (preg_match('/^NAME="(.+)"$/m', $osRelease, $m)) {
                    $distro = trim($m[1]);
                    if (preg_match('/^VERSION="(.+)"$/m', $osRelease, $m2)) {
                        $distro .= ' ' . trim($m2[1]);
                    }
                }
            }
        }
        // Last resort: try /etc/lsb-release
        if (empty($distro)) {
            $lsbRelease = @file_get_contents('/etc/lsb-release');
            if ($lsbRelease && preg_match('/^DISTRIB_DESCRIPTION="(.+)"$/m', $lsbRelease, $m)) {
                $distro = trim($m[1]);
            }
        }
    } elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $distro = system_info_getWindowsVersion();
    }

    return $distro;
}

function system_info_getWindowsVersion()
{
    $version = '';

    if (function_exists('shell_exec')) {

        $output = shell_exec('ver');
        if ($output) {
            $lines = explode("\n", $output);
            if (isset($lines[0])) {
                $version = trim($lines[0]);
            }
        }
    }

    if (empty($version) && function_exists('php_uname')) {

        $version = php_uname('v');
    }

    if (empty($version)) {

        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            $version = $_SERVER['SERVER_SOFTWARE'];
        } elseif (isset($_SERVER['WINDIR'])) {
            $version = $_SERVER['WINDIR'];
        }
    }

    return $version;
}
function system_info_check_service($service_name)
{
    if (empty($service_name)) {
        return false;
    }

    $os = strtoupper(PHP_OS);

    if (strpos($os, 'WIN') === 0) {
        $command = sprintf('sc query "%s" | findstr RUNNING', $service_name);
        exec($command, $output, $result_code);
        return $result_code === 0 || !empty($output);
    } else {
        $service_map = [
            'freeradius' => 'freeradius',
            'mysql' => ['mysql', 'mysqld', 'mariadb'],
            'mariadb' => ['mariadb', 'mysql', 'mysqld'],
            'cron' => ['cron', 'crond'],
            'sshd' => ['sshd', 'ssh'],
        ];
        
        $names_to_check = isset($service_map[$service_name]) 
            ? (array)$service_map[$service_name] 
            : [$service_name];
        
        foreach ($names_to_check as $svc) {
            if (function_exists('shell_exec')) {
                $result = @shell_exec("systemctl is-active $svc 2>/dev/null");
                if ($result && trim($result) === 'active') {
                    return true;
                }
            }
            
            $pidFile = "/var/run/$svc/$svc.pid";
            if (!file_exists($pidFile)) {
                $pidFile = "/var/run/$svc.pid";
            }
            if (file_exists($pidFile) && is_readable($pidFile)) {
                $pid = (int)@file_get_contents($pidFile);
                if ($pid > 0 && file_exists("/proc/$pid")) {
                    return true;
                }
            }
            
            $procDirs = @glob('/proc/[0-9]*/comm', GLOB_NOSORT);
            if ($procDirs) {
                foreach ($procDirs as $commFile) {
                    $comm = @file_get_contents($commFile);
                    if ($comm && trim($comm) === $svc) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
}

function system_info_generateServiceTable()
{
    $services_to_check = array("FreeRADIUS", "MySQL", "MariaDB", "Cron", "SSHd");

    $table = array(
        'title' => 'Service Status',
        'rows' => array()
    );

    foreach ($services_to_check as $service_name) {
        $running = system_info_check_service(strtolower($service_name));
        $class = ($running) ? "label pull-right bg-green" : "label pull-right bg-red";
        $label = ($running) ? "running" : "not running";

        $value = sprintf('<small class="%s">%s</small>', $class, $label);

        $table['rows'][] = array($service_name, $value);
    }

    return $table;
}

    $systemInfo = system_info_getSystemInfo();

    $ui->assign('systemInfo', $systemInfo);
    $ui->assign('disk_usage', system_info_get_disk_usage());
    $ui->assign('memory_usage', system_info_get_server_memory_usage());
    $ui->assign('serviceTable', system_info_generateServiceTable());
    $ui->assign('csrf_token', Csrf::generateAndStoreToken());

    // Display the template
    $ui->display('system_info.tpl');
}
