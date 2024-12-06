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

    $os = strtoupper(PHP_OS);

    if (strpos($os, 'WIN') === 0) {
        // Windows OS
        exec('net stop freeradius', $output, $retcode);
        exec('net start freeradius', $output, $retcode);
    } else {
        // Linux OS
        exec('sudo systemctl restart freeradius.service 2>&1', $output, $retcode);
    }
    $ui->assign('output', $output);
    $ui->assign('returnCode', $retcode);
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
        // Linux system
        $free = shell_exec('free -m');
        $free = (string) trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);

        $total_memory = $mem[1];
        $used_memory = $mem[2];
        $free_memory = $total_memory - $used_memory;
        $memory_usage_percentage = round($used_memory / $total_memory * 100);

        $memory_usage = [
            'total' => $total_memory,
            'free' => $free_memory,
            'used' => $used_memory,
            'used_percentage' => round($memory_usage_percentage),
        ];

        return $memory_usage;
    }

    return null;
}

function system_info_get_cpu_usage() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows system
        $cmd = 'wmic cpu get loadpercentage';
        $output = [];
        exec($cmd, $output);
        
        if (isset($output[1])) {
            $cpu_usage = trim($output[1]);
            return $cpu_usage . '%';
        }
    } else {
        // Linux system
        $cmd = "top -bn1 | grep 'Cpu(s)' | sed 's/.*, *\\([0-9.]*\\)%* id.*/\\1/' | awk '{print 100 - $1}'";
        $cpu_usage = shell_exec($cmd);
        
        if ($cpu_usage !== null) {
            return round(floatval(trim($cpu_usage)), 1) . '%';
        }
    }
    return 'Unknown';
}

function system_info_get_cpu_cores() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows system
        $output = [];
        exec('wmic cpu get NumberOfCores', $output);
        if (isset($output[1])) {
            return trim($output[1]);
        }
    } else {
        // Linux system
        $cores = shell_exec('nproc');
        if ($cores) {
            return trim($cores);
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
        'Memory Usage' => $memory_usage,
        'Network Interfaces' => system_info_get_network_info(),
        'Process Information' => system_info_get_process_info(),
        'Service Status' => system_info_get_service_status(),
        'Storage Information' => system_info_get_raid_status(),
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
        // Linux system
        $disk = shell_exec('df / --output=size,used,avail,pcent --block-size=1');
        $disk = (string) trim($disk);
        $disk_arr = explode("\n", $disk);
        $disk = explode(" ", preg_replace('/\s+/', ' ', $disk_arr[1]));
        $disk = array_filter($disk);
        $disk = array_merge($disk);

        $total_disk = $disk[0];
        $used_disk = $disk[1];
        $free_disk = $disk[2];
        $disk_usage_percentage = $disk[3];

        $disk_usage = [
            'total' => system_info_format_bytes($total_disk),
            'used' => system_info_format_bytes($used_disk),
            'free' => system_info_format_bytes($free_disk),
            'used_percentage' => $disk_usage_percentage,
        ];

        return $disk_usage;
    }

    return null;
}

function system_info_get_uptime() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows system
        $output = [];
        exec('wmic os get lastbootuptime', $output);
        
        if (isset($output[1])) {
            $boot_time = substr($output[1], 0, 14); // Format: YYYYMMDDHHMMSS
            $year = substr($boot_time, 0, 4);
            $month = substr($boot_time, 4, 2);
            $day = substr($boot_time, 6, 2);
            $hour = substr($boot_time, 8, 2);
            $minute = substr($boot_time, 10, 2);
            $second = substr($boot_time, 12, 2);
            
            $boot_timestamp = mktime($hour, $minute, $second, $month, $day, $year);
            $current_timestamp = time();
            $uptime_seconds = $current_timestamp - $boot_timestamp;
            
            return system_info_format_uptime($uptime_seconds);
        }
    } else {
        // Linux system
        $uptime = shell_exec('cat /proc/uptime');
        if ($uptime) {
            $uptime = explode(' ', $uptime)[0];
            return system_info_format_uptime((float)$uptime);
        }
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

    $bytes = max((float)$bytes, 0);
    $pow = floor(($bytes ? log((float)$bytes, 1024) : 0));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

function system_info_getSystemDistro()
{
    $distro = '';

    // Lets retrieve the system distribution based on the operating system
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN') {
        $distro = shell_exec('lsb_release -d');
        if ($distro) {
            $distro = trim(substr($distro, strpos($distro, ':') + 1));
        }
    } elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $distro = system_info_getWindowsVersion();
    }

    // We can add more conditions for different operating systems if needed but only Windows and Linux for now

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

function system_info_get_network_info() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows system
        $output = [];
        exec('wmic NIC get NetConnectionID,Speed,NetConnectionStatus', $output);
        $interfaces = [];
        
        // Skip the header line
        for ($i = 1; $i < count($output); $i++) {
            $line = trim($output[$i]);
            if (empty($line)) continue;
            
            $parts = preg_split('/\s+/', $line, 3);
            if (count($parts) === 3) {
                $status = '';
                switch($parts[2]) {
                    case '0': $status = 'Disconnected'; break;
                    case '1': $status = 'Connecting'; break;
                    case '2': $status = 'Connected'; break;
                    default: $status = 'Unknown';
                }
                
                $interfaces[] = [
                    'name' => $parts[0],
                    'speed' => $parts[1] ? system_info_format_bytes($parts[1]/8, 0).'/s' : 'Unknown',
                    'status' => $status
                ];
            }
        }
        
        return $interfaces;
    } else {
        // Linux system
        $interfaces = [];
        $output = shell_exec("ip -s link");
        if ($output) {
            $lines = explode("\n", $output);
            $current_interface = null;
            
            foreach ($lines as $line) {
                if (preg_match('/^\d+:\s+([^:@]+)[:@]/', $line, $matches)) {
                    $current_interface = [
                        'name' => trim($matches[1]),
                        'status' => strpos($line, 'UP') !== false ? 'Up' : 'Down',
                        'speed' => 'Unknown',
                        'rx_bytes' => 0,
                        'tx_bytes' => 0
                    ];
                } elseif ($current_interface && strpos($line, 'RX:') !== false) {
                    $stats = explode(' ', trim($line));
                    $current_interface['rx_bytes'] = isset($stats[2]) ? system_info_format_bytes($stats[2]) : 0;
                } elseif ($current_interface && strpos($line, 'TX:') !== false) {
                    $stats = explode(' ', trim($line));
                    $current_interface['tx_bytes'] = isset($stats[2]) ? system_info_format_bytes($stats[2]) : 0;
                    $interfaces[] = $current_interface;
                    $current_interface = null;
                }
            }
        }
        return $interfaces;
    }
}

function system_info_get_process_info() {
    $info = [];
    
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Get total processes
        $output = [];
        exec('wmic process get ProcessId', $output);
        $info['total_processes'] = count($output) - 1; // Subtract header line
        
        // Get top CPU processes
        $output = [];
        exec('wmic path win32_perfformatteddata_perfproc_process get Name,PercentProcessorTime /format:csv', $output);
        $top_processes = [];
        
        foreach (array_slice($output, 1, 6) as $line) { // Get top 5 processes
            $parts = explode(',', $line);
            if (count($parts) === 3 && $parts[1] !== '_Total') {
                $top_processes[$parts[1]] = $parts[2] . '%';
            }
        }
        $info['top_processes'] = $top_processes;
        
    } else {
        // Linux system
        // Get total processes
        $total = shell_exec('ps aux | wc -l');
        $info['total_processes'] = trim($total) - 1; // Subtract header line
        
        // Get top CPU processes
        $output = shell_exec("ps aux --sort=-%cpu | head -6"); // Get top 5 processes
        $lines = explode("\n", $output);
        $top_processes = [];
        
        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) continue;
            
            $parts = preg_split('/\s+/', $line);
            if (count($parts) >= 11) {
                $top_processes[$parts[10]] = $parts[2] . '%';
            }
        }
        $info['top_processes'] = $top_processes;
    }
    
    return $info;
}

function system_info_get_service_status() {
    $services = ['freeradius', 'mysql', 'apache2'];
    $status = [];
    
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        foreach ($services as $service) {
            $output = [];
            exec("sc query $service", $output);
            $running = false;
            foreach ($output as $line) {
                if (strpos($line, 'RUNNING') !== false) {
                    $running = true;
                    break;
                }
            }
            $status[$service] = $running ? 'Running' : 'Stopped';
        }
    } else {
        foreach ($services as $service) {
            $output = shell_exec("systemctl is-active $service 2>&1");
            $status[$service] = (trim($output) === 'active') ? 'Running' : 'Stopped';
        }
    }
    
    return $status;
}

function system_info_get_raid_status() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $output = [];
        exec('wmic path win32_volume get Label,FileSystem,Capacity,FreeSpace /format:csv', $output);
        $volumes = [];
        
        foreach (array_slice($output, 1) as $line) {
            $parts = explode(',', $line);
            if (count($parts) >= 4) {
                $volumes[] = [
                    'label' => $parts[1],
                    'filesystem' => $parts[2],
                    'total' => system_info_format_bytes($parts[3]),
                    'free' => system_info_format_bytes($parts[4])
                ];
            }
        }
        
        return ['volumes' => $volumes];
    } else {
        // Try to get mdstat for Linux RAID
        $raid_status = @file_get_contents('/proc/mdstat');
        if ($raid_status) {
            return ['mdstat' => $raid_status];
        }
        
        // If no RAID, return basic disk health
        $smart_output = shell_exec('smartctl --scan');
        $disks = [];
        if ($smart_output) {
            $lines = explode("\n", $smart_output);
            foreach ($lines as $line) {
                if (preg_match('/^\/dev\/\w+/', $line, $matches)) {
                    $disk_info = shell_exec("smartctl -H {$matches[0]}");
                    $disks[] = [
                        'device' => $matches[0],
                        'health' => strpos($disk_info, 'PASSED') !== false ? 'Healthy' : 'Check Required'
                    ];
                }
            }
        }
        
        return ['disks' => $disks];
    }
}

function system_info_generateServiceTable()
{
    function system_info_check_service($service_name)
    {
        if (empty($service_name)) {
            return false;
        }

        $os = strtoupper(PHP_OS);

        if (strpos($os, 'WIN') === 0) {
            // Windows OS
            $command = sprintf('sc query "%s" | findstr RUNNING', $service_name);
            exec($command, $output, $result_code);
            return $result_code === 0 || !empty($output);
        } else {
            // Linux OS
            $command = sprintf("pgrep %s", escapeshellarg($service_name));
            exec($command, $output, $result_code);
            return $result_code === 0;
        }
    }

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

    // Display the template
    $ui->display('system_info.tpl');
}
