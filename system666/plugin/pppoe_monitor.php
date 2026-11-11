<?php

use PEAR2\Net\RouterOS;

// Register the PPPoE Monitor menu
register_menu(" PPPoE Monitor", true, "pppoe_monitor_router_menu", 'AFTER_SETTINGS', 'ion ion-ios-pulse', "Hot", "red");

// Require an authenticated admin for JSON endpoints (the plugin router does no auth).
function pppoe_monitor_json_auth()
{
    $admin = Admin::_info();
    if (!$admin) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}

function pppoe_monitor_router_menu()
{
    global $ui, $routes;
    _admin();
    $ui->assign('_title', 'PPPoE Monitor');
    $ui->assign('_system_menu', 'PPPoE Monitor');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
    $router = $routes['2'] ?? $routers[0]['id']; 
    $ui->assign('routers', $routers);
    $ui->assign('router', $router);
    $ui->assign('interfaces', pppoe_monitor_router_getInterface());
    $ui->assign('csrf_token', Csrf::generateAndStoreToken());
    
    $ui->display('pppoe_monitor.tpl');
}

function pppoe_monitor_router_getInterface()
{
    global $routes;
    $routerId = $routes['2'] ?? null;

    if (!$routerId) {
        return [];
    }

    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($routerId);

    if (!$mikrotik) {
        return [];
    }

    try {
        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        if (!$client) {
            return [];
        }
        $interfaces = $client->sendSync(new RouterOS\Request('/interface/print'));
    } catch (Exception $e) {
        return [];
    }

    $interfaceList = [];
    foreach ($interfaces as $interface) {
        $name = $interface->getProperty('name');
        $interfaceList[] = $name; // Jangan menghapus karakter < dan > dari nama interface
    }

    return $interfaceList;
}

function pppoe_monitor_router_get_combined_users() {
    pppoe_monitor_json_auth();

    global $routes;
    $router = $routes['2'];
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);

    if (!$mikrotik) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Router not found']);
        return;
    }

    try {
        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

        // Fetch PPP online users
        $pppUsers = $client->sendSync(new RouterOS\Request('/ppp/active/print'));
        $interfaceTraffic = $client->sendSync(new RouterOS\Request('/interface/print'));

        $interfaceData = [];
        foreach ($interfaceTraffic as $interface) {
            $name = $interface->getProperty('name');
            if (empty($name)) {
                continue;
            }

            $interfaceData[$name] = [
                'status' => $interface->getProperty('running') === 'true' ? 'Connected' : 'Disconnected',
                'txBytes' => intval($interface->getProperty('tx-byte')),
                'rxBytes' => intval($interface->getProperty('rx-byte')),
            ];
        }

        $pppUserList = [];
        foreach ($pppUsers as $pppUser) {
            $username = $pppUser->getProperty('name');
            if (empty($username)) {
                continue;
            }
            $address = $pppUser->getProperty('address');
            $uptime = $pppUser->getProperty('uptime');
            $service = $pppUser->getProperty('service');
            $callerid = $pppUser->getProperty('caller-id');
            $bytes_in = $pppUser->getProperty('limit-bytes-in');
            $bytes_out = $pppUser->getProperty('limit-bytes-out');
            $id = $pppUser->getProperty('.id');

            $interfaceName = "<pppoe-$username>";

            if (isset($interfaceData[$interfaceName])) {
                $trafficData = $interfaceData[$interfaceName];
                $txBytes = $trafficData['txBytes'];
                $rxBytes = $trafficData['rxBytes'];
                $status = $trafficData['status'];
            } else {
                $txBytes = 0;
                $rxBytes = 0;
                $status = 'Disconnected';
            }

            $pppUserList[$username] = [
                'id' => $id,
                'username' => $username,
                'address' => $address,
                'uptime' => $uptime,
                'service' => $service,
                'caller_id' => $callerid,
                'bytes_in' => $bytes_in,
                'bytes_out' => $bytes_out,
                'tx' => pppoe_monitor_router_formatBytes($txBytes),
                'rx' => pppoe_monitor_router_formatBytes($rxBytes),
                'total' => pppoe_monitor_router_formatBytes($txBytes + $rxBytes),
                'status' => $status,
                'max_limit' => 'N/A' // Default value for max_limit
            ];
        }

        // Fetch limited users
        $queues = $client->sendSync(new RouterOS\Request('/queue/simple/print'));

        foreach ($queues as $queue) {
            $name = $queue->getProperty('name');
            $max_limit = $queue->getProperty('max-limit');

            if ($max_limit !== null && $max_limit !== '') {
                $formattedMaxLimit = pppoe_monitor_router_formatMaxLimit($max_limit);
                $strippedName = str_replace('<pppoe-', '', str_replace('>', '', $name));
                if (isset($pppUserList[$name])) {
                    $pppUserList[$name]['max_limit'] = $formattedMaxLimit;
                } elseif (isset($pppUserList[$strippedName])) {
                    $pppUserList[$strippedName]['max_limit'] = $formattedMaxLimit;
                } else {
                    $pppUserList[$name] = [
                        'username' => $name,
                        'max_limit' => $formattedMaxLimit,
                        'id' => null,
                        'address' => null,
                        'uptime' => null,
                        'service' => null,
                        'caller_id' => null,
                        'bytes_in' => null,
                        'bytes_out' => null,
                        'tx' => null,
                        'rx' => null,
                        'total' => null,
                        'status' => 'Disconnected',
                    ];
                }
            }
        }

        // Convert the user list to a regular array for JSON encoding
        $userList = array_values($pppUserList);

        // Return the combined user list as JSON
        header('Content-Type: application/json');
        echo json_encode($userList);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function pppoe_monitor_router_formatMaxLimit($max_limit) {
    $limits = explode('/', $max_limit);
    if (count($limits) == 2) {
        $downloadLimit = intval($limits[0]);
        $uploadLimit = intval($limits[1]);
        $formattedDownloadLimit = ceil($downloadLimit / (1024 * 1024)) . ' MB';
        $formattedUploadLimit = ceil($uploadLimit / (1024 * 1024)) . ' MB';
        return $formattedDownloadLimit . '/' . $formattedUploadLimit;
    }
    return 'N/A';
}

// Fungsi untuk menghitung total data yang digunakan per harinya

function pppoe_monitor_router_formatBytes($bytes, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function pppoe_monitor_router_traffic()
{
    pppoe_monitor_json_auth();

    $interface = isset($_GET['interface']) ? preg_replace('/[^a-zA-Z0-9_\-\.<>\s]/', '', (string)$_GET['interface']) : '';

    global $routes;
    $router = $routes['2'];
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);

    if (!$mikrotik) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Router not found']);
        return;
    }

    try {
        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        if (!$client) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Cannot connect to router']);
            return;
        }

        $results = $client->sendSync(
            (new RouterOS\Request('/interface/monitor-traffic'))
                ->setArgument('interface', $interface)
                ->setArgument('once', '')
        );

        $rows = array();
        $rows2 = array();
        $labels = array();

        foreach ($results as $result) {
            $ftx = $result->getProperty('tx-bits-per-second');
            $frx = $result->getProperty('rx-bits-per-second');

            // Timestamp dalam milidetik (millisecond)
            $timestamp = time() * 1000;

            $rows[] = $ftx;
            $rows2[] = $frx;
            $labels[] = $timestamp; // Tambahkan timestamp ke dalam array labels
        }

        $result = array(
            'labels' => $labels,
            'rows' => array(
                'tx' => $rows,
                'rx' => $rows2
            )
        );
    } catch (Exception $e) {
        $result = array('error' => $e->getMessage());
    }

    // Set header untuk respons JSON
    header('Content-Type: application/json');
    echo json_encode($result);
}

function pppoe_monitor_router_online()
{
    pppoe_monitor_json_auth();

    global $routes;
    $router = $routes['2'];
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
    if (!$mikrotik) {
        return [];
    }

    try {
        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        if (!$client) {
            return [];
        }
        $pppUsers = $client->sendSync(new RouterOS\Request('/ppp/active/print'));
    } catch (Exception $e) {
        return [];
    }

    $pppoeInterfaces = [];

    foreach ($pppUsers as $pppUser) {
        $username = $pppUser->getProperty('name');
        $interfaceName = "<pppoe-$username>"; // Tambahkan karakter < dan >

        // Ensure interface name is not empty and it's not already in the list
        if (!empty($interfaceName) && !in_array($interfaceName, $pppoeInterfaces)) {
            $pppoeInterfaces[] = $interfaceName;
        }
    }

    // Return the list of PPPoE interfaces
    return $pppoeInterfaces;
}

function pppoe_monitor_router_delete_ppp_user()
{
    pppoe_monitor_json_auth();

    if (!Csrf::check(_post('csrf_token'))) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid or expired CSRF token']);
        exit;
    }

    global $routes;
    $router = $routes['2'];
    $id = isset($_POST['id']) ? trim((string)$_POST['id']) : '';

    // RouterOS .id is a hex pointer like "*1A2B3C"
    if ($id === '' || !preg_match('/^\*[0-9A-Fa-f]+$/', $id)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid session ID.']);
        return;
    }

    // Ambil detail router dari database
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);

    if (!$mikrotik) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Router not found.']);
        return;
    }

    try {
        // Dapatkan klien MikroTik
        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

        if (!$client) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to connect to the router.']);
            return;
        }
        // Buat permintaan untuk menghapus koneksi aktif PPPoE
        $request = new RouterOS\Request('/ppp/active/remove');
        $request->setArgument('.id', $id); // Gunakan .id yang sesuai
        $client->sendSync($request);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'PPPoE user successfully deleted.']);
    } catch (Exception $e) {
        // Log error untuk debugging
        error_log('Failed to delete PPPoE user: ' . $e->getMessage());

        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to delete PPPoE user: ' . $e->getMessage()]);
    }
}

// ======================================================================
// NEW FUNCTIONS:

// Fungsi untuk menghitung total data yang digunakan per harinya
function pppoe_monitor_router_daily_data_usage()
{
    pppoe_monitor_json_auth();

    $username = isset($_GET['username']) ? preg_replace('/[^a-zA-Z0-9_.-]/', '', (string)$_GET['username']) : '';
    if ($username === '') {
        header('Content-Type: application/json');
        echo json_encode([]);
        return;
    }

    $from = !empty($_GET['start_date']) ? (string)$_GET['start_date'] : date('Y-m-01');
    $to   = !empty($_GET['end_date'])   ? (string)$_GET['end_date']   : date('Y-m-t');

    $fromTs = strtotime($from);
    $toTs   = strtotime($to);
    $from = $fromTs ? date('Y-m-d', $fromTs) : date('Y-m-01');
    $to   = $toTs   ? date('Y-m-d', $toTs)   : date('Y-m-t');

    require_once __DIR__ . '/../helpers/monthly_usage.php';

    $rows = daily_usage_get($username, $from, $to);

    $daily_usage = [];
    foreach ($rows as $row) {
        $day = $row['day'];
        if (!isset($daily_usage[$day])) {
            $daily_usage[$day] = ['total' => 0, 'users' => []];
        }
        $dl = (int)$row['download_bytes'];
        $ul = (int)$row['upload_bytes'];
        $daily_usage[$day]['users'][] = [
            'username' => $username,
            'rx' => pppoe_monitor_router_formatBytes($dl),
            'tx' => pppoe_monitor_router_formatBytes($ul),
            'total' => pppoe_monitor_router_formatBytes($dl + $ul)
        ];
        $daily_usage[$day]['total'] += ($dl + $ul) / (1024 * 1024);
    }

    header('Content-Type: application/json');
    echo json_encode($daily_usage);
}
