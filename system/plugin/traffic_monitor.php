<?php
use PEAR2\Net\RouterOS;

register_menu("Traffic Monitor", true, "traffic_monitor_ui", 'AFTER_SETTINGS', 'ion ion-stats-bars', "Live", "blue");

// Auto-create traffic cache table (holds latest sample per router+interface).
// Wrapped in try/catch so a permission/schema issue can NEVER block the page.
if (!isTableExist('tbl_traffic_monitor')) {
    try {
        ORM::raw_execute("CREATE TABLE IF NOT EXISTS tbl_traffic_monitor (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            router_id INT(11) NOT NULL,
            interface VARCHAR(100) NOT NULL,
            rx_bps BIGINT NOT NULL DEFAULT 0,
            tx_bps BIGINT NOT NULL DEFAULT 0,
            updated_at DATETIME NOT NULL,
            UNIQUE KEY uq_router_iface (router_id, interface)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    } catch (\Exception $e) {
        // cache table creation failed — monitoring still works live
    }
}

/**
 * Connect to a router using the SAME proven connection path as the rest of
 * the system (Mikrotik::getClient), but cap the socket timeout so a
 * slow/unreachable router never hangs the page for minutes.
 */
function traffic_monitor_connect($routerRow)
{
    $oldTimeout = ini_get('default_socket_timeout');
    ini_set('default_socket_timeout', '8');
    try {
        return Mikrotik::getClient($routerRow['ip_address'], $routerRow['username'], $routerRow['password']);
    } finally {
        ini_set('default_socket_timeout', $oldTimeout);
    }
}

/**
 * Get interface list for a router — cached for 60s so repeated page loads
 * never block on a slow/unreachable router.
 */
function traffic_monitor_get_interfaces($routerId)
{
    $cacheKey = 'traffic_ifaces_' . $routerId;
    $cached = ORM::for_table('tbl_appconfig')->where('setting', $cacheKey)->find_one();
    if ($cached) {
        $data = json_decode($cached['value'], true);
        if (is_array($data) && isset($data['t']) && isset($data['ifaces'])
            && (time() - $data['t']) < 60 && is_array($data['ifaces']) && count($data['ifaces']) > 0) {
            return $data['ifaces'];
        }
    }

    $list = ['ether1'];
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($routerId);
    if ($mikrotik) {
        try {
            $client = traffic_monitor_connect($mikrotik);
            $interfaces = $client->sendSync(new RouterOS\Request('/interface/print'));
            $tmp = [];
            foreach ($interfaces as $interface) {
                $name = $interface->getProperty('name');
                if (!empty($name)) $tmp[] = $name;
            }
            if (count($tmp) > 0) $list = $tmp;
        } catch (\Exception $e) {
            // Router unreachable — fall back to cache (even if stale) or default
            if ($cached) {
                $data = json_decode($cached['value'], true);
                if (is_array($data) && is_array($data['ifaces']) && count($data['ifaces']) > 0) {
                    return $data['ifaces'];
                }
            }
        }
    }

    // Refresh cache
    $d = ORM::for_table('tbl_appconfig')->where('setting', $cacheKey)->find_one();
    if (!$d) { $d = ORM::for_table('tbl_appconfig')->create(); $d->setting = $cacheKey; }
    $d->value = json_encode(['t' => time(), 'ifaces' => $list]);
    $d->save();

    return $list;
}

function traffic_monitor_ui()
{
    global $ui, $routes;
    _admin();
    $ui->assign('_title', 'Live Traffic Monitor');
    $ui->assign('_system_menu', 'Traffic Monitor');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    
    // Get list of routers
    $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
    $router = $routes['2'];
    if (empty($router) && count($routers) > 0) {
        $router = $routers[0]['id'];
    }
    
    // Interfaces — served from 60s cache, never blocks the page
    $interfaces = traffic_monitor_get_interfaces($router);
    $ui->assign('interfaces', $interfaces);
    
    $ui->assign('routers', $routers);
    $ui->assign('router', $router);
    $ui->display('traffic_monitor.tpl');
}

function traffic_monitor_get_data()
{
    $interface = preg_replace('/[^a-zA-Z0-9\-\/\.]/', '', $_GET['interface'] ?? 'ether1');
    global $routes;
    $router = $routes['2'];
    
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
    if (!$mikrotik) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Router not found']);
        exit;
    }

    $rx = 0; $tx = 0; $fresh = false;

    // Cache is OPTIONAL — if the cache table has any problem we simply skip
    // it and always query the router live. A DB warning must never block
    // the traffic monitor from working.
    $cachedRow = null;
    try {
        $cachedRow = ORM::for_table('tbl_traffic_monitor')
            ->where('router_id', $router)
            ->where('interface', $interface)
            ->find_one();
    } catch (\Exception $e) {
        $cachedRow = null; // cache table unavailable — fall through to live query
    }

    // If cache is fresh (< 1.6s), reuse it — shares ONE router poll across tabs
    if ($cachedRow && strtotime($cachedRow['updated_at']) > time() - 1.6) {
        $rx = intval($cachedRow['rx_bps']);
        $tx = intval($cachedRow['tx_bps']);
        $fresh = true;
    } else {
        $errMsg = null;
        try {
            $client = traffic_monitor_connect($mikrotik);
            $results = $client->sendSync(
                (new RouterOS\Request('/interface/monitor-traffic'))
                    ->setArgument('interface', $interface)
                    ->setArgument('once', '')
            );
            foreach ($results as $result) {
                $rx = intval($result->getProperty('rx-bits-per-second'));
                $tx = intval($result->getProperty('tx-bits-per-second'));
            }
            $fresh = true;

            // Try to save to cache — never let a cache write failure break live data
            try {
                $row = $cachedRow;
                if (!$row) {
                    $row = ORM::for_table('tbl_traffic_monitor')->create();
                    $row->router_id = $router;
                    $row->interface = $interface;
                }
                $row->rx_bps = $rx;
                $row->tx_bps = $tx;
                $row->updated_at = date('Y-m-d H:i:s');
                $row->save();
            } catch (\Exception $e2) {
                // cache write failed — ignore, live data still returned
            }
        } catch (\Exception $e) {
            // Router unreachable — return last known cached value + expose the real error
            if ($cachedRow) {
                $rx = intval($cachedRow['rx_bps']);
                $tx = intval($cachedRow['tx_bps']);
            }
            $errMsg = $e->getMessage();
        }
    }

    header('Content-Type: application/json');
    echo json_encode([
        'labels' => [date('H:i:s')],
        'rows' => ['tx' => [$tx], 'rx' => [$rx]],
        'fresh' => $fresh,
        'online' => $fresh,
        'error' => isset($errMsg) ? $errMsg : null,
    ]);
}

// Helper function to format bytes
function traffic_monitor_format_bytes($bytes, $precision = 2)
{
    $units = ['bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1000));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1000, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}
