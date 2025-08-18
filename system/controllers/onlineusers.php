<?php

// Include necessary files and functions here
// ...

_admin();
$ui->assign('_title', Lang::T('Online Users'));
$ui->assign('_system_menu', 'onlineusers');

$action = $routes['1'];
$ui->assign('_admin', $admin);

use PEAR2\Net\RouterOS;

require_once 'system/autoload/PEAR2/Autoload.php';

if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
    _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
}

// Handle cases for hotspot users and PPP users
switch ($action) {
    case 'hotspot':
        // Get all enabled routers for selection
        $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
        $ui->assign('routers', $routers);
        
        // Set default to "all" routers
        $ui->assign('defaultRouter', null); // No specific router selected by default
        
        $ui->display('hotspot_users.tpl');
        break;
case 'hotspot_users':
    $hotspotUsers = mikrotik_get_hotspot_online_users();

    // Filter out entries where all values are null
    $filteredHotspotUsers = array_filter($hotspotUsers, function($user) {
        // Check if all specified fields are null
        return !(
            is_null($user['username']) &&
            is_null($user['address']) &&
            is_null($user['uptime']) &&
            is_null($user['server']) &&
            is_null($user['mac']) &&
            is_null($user['session_time']) &&
            $user['rx_bytes'] === '0 B' &&
            $user['tx_bytes'] === '0 B' &&
            $user['total'] === '0 B'
        );
    });

    header('Content-Type: application/json');
    echo json_encode($filteredHotspotUsers);
    exit;
    break;

case 'pppoe':
        $ui->display('ppp_users.tpl');
        break;

case 'ppp_users':
    $pppUsers = mikrotik_get_ppp_online_users();
    header('Content-Type: application/json');
    echo json_encode($pppUsers);
    exit;
    break;

    case 'disconnect':
        $routerId = $routes['2'];
        $username = $routes['3'];
        $userType = $routes['4'];
        mikrotik_disconnect_online_user($routerId, $username, $userType);
        // Redirect or handle the response as needed
        break;

    case 'summary':
        // Fetch summary of online users 
        $summary = mikrotik_get_online_users_summary();
        header('Content-Type: application/json');
        echo json_encode($summary);
        exit;
        break;
        
    case 'hotspot_stats':
        // Fetch detailed hotspot statistics
        $stats = mikrotik_get_hotspot_stats();
        header('Content-Type: application/json');
        echo json_encode($stats);
        exit;
        break;
    case 'sms_balance':
        // Fetch the SMS balance
        $api_url = 'https://portal.bytewavenetworks.com/api/http/balance?api_token=110|qzbO9Ax3w6VXhtsldP69oglWEpPzCOksuvQuf4jU2240100f ';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            $response_data = ['error' => $error_msg];
        } else {
            $response_data = json_decode($response, true);
        }
        curl_close($ch);
        header('Content-Type: application/json');
        echo json_encode($response_data);
        exit;
        break;


    default:
        // Handle default case or invalid action
        break;
}

// Function to round the value and append the appropriate unit
function mikrotik_formatBytes($bytes, $precision = 2)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

function filter_null_users($users) {
    return array_filter($users, function($user) {
        return array_reduce($user, function($carry, $value) {
            return $carry || $value !== null;
        }, false);
    });
}

function mikrotik_get_hotspot_online_users()
{
    global $routes;
    // Try to get router ID from routes, default to 'all' if not provided
    $routerId = isset($routes['2']) ? $routes['2'] : 'all';
    
    if ($routerId === 'all') {
        // Get all enabled routers
        $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
        $allHotspotUsers = [];
        
        foreach ($routers as $mikrotik) {
            try {
                $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
                $hotspotActive = $client->sendSync(new RouterOS\Request('/ip/hotspot/active/print'));

                foreach ($hotspotActive as $hotspot) {
                    $username = $hotspot->getProperty('user');
                    $address = $hotspot->getProperty('address');
                    $uptime = $hotspot->getProperty('uptime');
                    $server = $hotspot->getProperty('server');
                    $mac = $hotspot->getProperty('mac-address');
                    $sessionTime = $hotspot->getProperty('session-time-left');
                    $rxBytes = $hotspot->getProperty('bytes-in');
                    $txBytes = $hotspot->getProperty('bytes-out');

                    $allHotspotUsers[] = [
                        'username' => $username,
                        'address' => $address,
                        'uptime' => $uptime,
                        'server' => $server,
                        'mac' => $mac,
                        'session_time' => $sessionTime,
                        'rx_bytes' =>  mikrotik_formatBytes($rxBytes),
                        'tx_bytes' => mikrotik_formatBytes($txBytes),
                        'total' => mikrotik_formatBytes($rxBytes + $txBytes),
                        'router_name' => $mikrotik['name']
                    ];
                }
            } catch (Exception $e) {
                // Continue to next router if this one fails
                continue;
            }
        }
        
        // Filter out users with all null properties
        $filteredHotspotList = filter_null_users($allHotspotUsers);
        return empty($filteredHotspotList) ? [] : $filteredHotspotList;
    } else {
        // Handle single router
        $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($routerId);
        
        if (!$mikrotik) {
            // If specific router not found, get the first enabled router
            $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one();
            if (!$mikrotik) {
                return []; // Return empty array if no router found
            }
        }
        
        try {
            $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            $hotspotActive = $client->sendSync(new RouterOS\Request('/ip/hotspot/active/print'));

            $hotspotList = [];
            foreach ($hotspotActive as $hotspot) {
                $username = $hotspot->getProperty('user');
                $address = $hotspot->getProperty('address');
                $uptime = $hotspot->getProperty('uptime');
                $server = $hotspot->getProperty('server');
                $mac = $hotspot->getProperty('mac-address');
                $sessionTime = $hotspot->getProperty('session-time-left');
                $rxBytes = $hotspot->getProperty('bytes-in');
                $txBytes = $hotspot->getProperty('bytes-out');

                $hotspotList[] = [
                    'username' => $username,
                    'address' => $address,
                    'uptime' => $uptime,
                    'server' => $server,
                    'mac' => $mac,
                    'session_time' => $sessionTime,
                    'rx_bytes' =>  mikrotik_formatBytes($rxBytes),
                    'tx_bytes' => mikrotik_formatBytes($txBytes),
                    'total' => mikrotik_formatBytes($rxBytes + $txBytes),
                    'router_name' => $mikrotik['name']
                ];
            }

            // Filter out users with all null properties
            $filteredHotspotList = filter_null_users($hotspotList);

            // Return an empty array if no users are left after filtering
            return empty($filteredHotspotList) ? [] : $filteredHotspotList;
            
        } catch (Exception $e) {
            // Return empty array on error
            return [];
        }
    }
}

function mikrotik_get_ppp_online_users()
{
    global $routes;
    $routerId = $routes['2'];
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($routerId);
    $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
    $pppUsers = $client->sendSync(new RouterOS\Request('/ppp/active/print'));

    $userList = [];
    foreach ($pppUsers as $pppUser) {
        $username = $pppUser->getProperty('name');
        $address = $pppUser->getProperty('address');
        $uptime = $pppUser->getProperty('uptime');
        $service = $pppUser->getProperty('service');
        $callerid = $pppUser->getProperty('caller-id');
        $bytes_in = $pppUser->getProperty('limit-bytes-in');
        $bytes_out = $pppUser->getProperty('limit-bytes-out');

        $userList[] = [
            'username' => $username,
            'address' => $address,
            'uptime' => $uptime,
            'service' => $service,
            'caller_id' => $callerid,
            'bytes_in' => $bytes_in,
            'bytes_out' => $bytes_out,
        ];
    }

    // Filter out users with all null properties
    return filter_null_users($userList);
}

function mikrotik_get_online_users_summary()
{
    global $routes;
    $routerId = $routes['2'];
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($routerId);
    $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

    // Get Hotspot users
    $hotspotActive = $client->sendSync(new RouterOS\Request('/ip/hotspot/active/print'));
    $hotspotList = [];
    $totalHotspotUsage = 0;
    foreach ($hotspotActive as $hotspot) {
        $rxBytes = $hotspot->getProperty('bytes-in');
        $txBytes = $hotspot->getProperty('bytes-out');
        $totalHotspotUsage += $rxBytes + $txBytes;

        $hotspotList[] = [
            'username' => $hotspot->getProperty('user'),
            'address' => $hotspot->getProperty('address'),
            'uptime' => $hotspot->getProperty('uptime'),
            'server' => $hotspot->getProperty('server'),
            'mac' => $hotspot->getProperty('mac-address'),
            'session_time' => $hotspot->getProperty('session-time-left'),
            'rx_bytes' => mikrotik_formatBytes($rxBytes),
            'tx_bytes' => mikrotik_formatBytes($txBytes),
            'total' => mikrotik_formatBytes($rxBytes + $txBytes),
        ];
    }

    // Filter out null hotspot users
    $hotspotList = array_filter($hotspotList, function($user) {
        return !(
            is_null($user['username']) &&
            is_null($user['address']) &&
            is_null($user['uptime']) &&
            is_null($user['server']) &&
            is_null($user['mac']) &&
            is_null($user['session_time']) &&
            $user['rx_bytes'] === '0 B' &&
            $user['tx_bytes'] === '0 B' &&
            $user['total'] === '0 B'
        );
    });

    // Get PPPoE users
    $pppUsers = $client->sendSync(new RouterOS\Request('/ppp/active/print'));
    $pppoeList = [];
    $totalPPPoEUsage = 0;
    foreach ($pppUsers as $pppUser) {
        $bytes_in = $pppUser->getProperty('limit-bytes-in');
        $bytes_out = $pppUser->getProperty('limit-bytes-out');
        $totalPPPoEUsage += $bytes_in + $bytes_out;

        $pppoeList[] = [
            'username' => $pppUser->getProperty('name'),
            'address' => $pppUser->getProperty('address'),
            'uptime' => $pppUser->getProperty('uptime'),
            'service' => $pppUser->getProperty('service'),
            'caller_id' => $pppUser->getProperty('caller-id'),
            'bytes_in' => mikrotik_formatBytes($bytes_in),
            'bytes_out' => mikrotik_formatBytes($bytes_out),
            'total' => mikrotik_formatBytes($bytes_in + $bytes_out),
        ];
    }

    // Filter out null PPPoE users
    $pppoeList = array_filter($pppoeList, function($user) {
        return !(
            is_null($user['username']) &&
            is_null($user['address']) &&
            is_null($user['uptime']) &&
            is_null($user['service']) &&
            is_null($user['caller_id']) &&
            $user['bytes_in'] === '0 B' &&
            $user['bytes_out'] === '0 B' &&
            $user['total'] === '0 B'
        );
    });

    // Calculate total data usage
    $totalDataUsage = $totalHotspotUsage + $totalPPPoEUsage;

    // Calculate total users
    $totalHotspotUsers = count($hotspotList);
    $totalPPPoEUsers = count($pppoeList);
    $totalUsers = $totalHotspotUsers + $totalPPPoEUsers;

    return [
        'hotspot_users' => $totalHotspotUsers,
        'ppoe_users' => $totalPPPoEUsers,
        'total_users' => $totalUsers,
        'total_bytes' => mikrotik_formatBytes($totalDataUsage),
    ];
}

function mikrotik_get_hotspot_stats()
{
    global $routes;
    // Try to get router ID from routes, default to 'all' if not provided
    $routerId = isset($routes['2']) ? $routes['2'] : 'all';
    
    if ($routerId === 'all') {
        // Get stats from all enabled routers
        $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
        
        $totalDownload = 0;
        $totalUpload = 0;
        $totalUsers = 0;
        $routerNames = [];
        
        foreach ($routers as $mikrotik) {
            try {
                $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
                $hotspotActive = $client->sendSync(new RouterOS\Request('/ip/hotspot/active/print'));
                
                foreach ($hotspotActive as $hotspot) {
                    $rxBytes = (int)$hotspot->getProperty('bytes-in');
                    $txBytes = (int)$hotspot->getProperty('bytes-out');
                    $username = $hotspot->getProperty('user');
                    
                    // Only count if user has valid data
                    if (!empty($username)) {
                        $totalDownload += $txBytes; // Download from router perspective
                        $totalUpload += $rxBytes;   // Upload from router perspective
                        $totalUsers++;
                    }
                }
                
                $routerNames[] = $mikrotik['name'];
            } catch (Exception $e) {
                // Continue to next router if this one fails
                continue;
            }
        }
        
        $totalBandwidth = $totalDownload + $totalUpload;
        
        return [
            'total_users' => $totalUsers,
            'total_download' => mikrotik_formatBytes($totalDownload),
            'total_upload' => mikrotik_formatBytes($totalUpload),
            'total_bandwidth' => mikrotik_formatBytes($totalBandwidth),
            'total_download_raw' => $totalDownload,
            'total_upload_raw' => $totalUpload,
            'total_bandwidth_raw' => $totalBandwidth,
            'router_name' => 'All Routers (' . implode(', ', $routerNames) . ')'
        ];
    } else {
        // Handle single router
        $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($routerId);
        
        if (!$mikrotik) {
            // If specific router not found, get the first enabled router
            $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one();
            if (!$mikrotik) {
                return [
                    'total_users' => 0,
                    'total_download' => '0 B',
                    'total_upload' => '0 B',
                    'total_bandwidth' => '0 B',
                    'total_download_raw' => 0,
                    'total_upload_raw' => 0,
                    'total_bandwidth_raw' => 0,
                    'error' => 'No router found'
                ];
            }
        }
        
        try {
            $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

            // Get Hotspot users
            $hotspotActive = $client->sendSync(new RouterOS\Request('/ip/hotspot/active/print'));
            
            $totalDownload = 0;
            $totalUpload = 0;
            $totalUsers = 0;
            
            foreach ($hotspotActive as $hotspot) {
                $rxBytes = (int)$hotspot->getProperty('bytes-in');
                $txBytes = (int)$hotspot->getProperty('bytes-out');
                $username = $hotspot->getProperty('user');
                
                // Only count if user has valid data
                if (!empty($username)) {
                    $totalDownload += $txBytes; // Download from router perspective
                    $totalUpload += $rxBytes;   // Upload from router perspective
                    $totalUsers++;
                }
            }
            
            $totalBandwidth = $totalDownload + $totalUpload;
            
            return [
                'total_users' => $totalUsers,
                'total_download' => mikrotik_formatBytes($totalDownload),
                'total_upload' => mikrotik_formatBytes($totalUpload),
                'total_bandwidth' => mikrotik_formatBytes($totalBandwidth),
                'total_download_raw' => $totalDownload,
                'total_upload_raw' => $totalUpload,
                'total_bandwidth_raw' => $totalBandwidth,
                'router_name' => $mikrotik['name']
            ];
            
        } catch (Exception $e) {
            return [
                'total_users' => 0,
                'total_download' => '0 B',
                'total_upload' => '0 B',
                'total_bandwidth' => '0 B',
                'total_download_raw' => 0,
                'total_upload_raw' => 0,
                'total_bandwidth_raw' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
}

function mikrotik_disconnect_online_user($router, $username, $userType)
{
    // Check if the form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve the form data
        $router = $_POST['router'];
        $username = $_POST['username'];
        $userType = $_POST['userType'];
        $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
        if (!$mikrotik) {
            // Handle the error response or redirection
            return;
        }
        try {
            $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            if ($userType == 'hotspot') {
                Mikrotik::removeHotspotActiveUser($client, $username);
                // Handle the success response or redirection
            } elseif ($userType == 'pppoe') {
                Mikrotik::removePpoeActive($client, $username);
                // Handle the success response or redirection
            } else {
                // Handle the error response or redirection
                return;
            }
        } catch (Exception $e) {
            // Handle the error response or redirection
        } finally {
            // Disconnect from the MikroTik router
            if (isset($client)) {
                $client->disconnect();
            }
        }
    }
}

?>