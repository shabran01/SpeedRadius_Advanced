<?php
use PEAR2\Net\RouterOS;

register_menu("Traffic Monitor", true, "traffic_monitor_ui", 'AFTER_SETTINGS', 'ion ion-stats-bars', "Live", "blue");

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
    if(empty($router)){
        $router = $routers[0]['id'];
    }
    
    // Get interfaces for the selected router
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
    if($mikrotik){
        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        $interfaces = $client->sendSync(new RouterOS\Request('/interface/print'));
        $interfaceList = [];
        foreach ($interfaces as $interface) {
            $interfaceList[] = $interface->getProperty('name');
        }
        $ui->assign('interfaces', $interfaceList);
    }
    
    $ui->assign('routers', $routers);
    $ui->assign('router', $router);
    $ui->display('traffic_monitor.tpl');
}

function traffic_monitor_get_data()
{
    $interface = $_GET['interface'] ?? 'ether1';
    global $routes;
    $router = $routes['2'];
    
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
    if(!$mikrotik) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Router not found']);
        exit;
    }

    try {
        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

        // Get traffic data
        $results = $client->sendSync(
            (new RouterOS\Request('/interface/monitor-traffic'))
                ->setArgument('interface', $interface)
                ->setArgument('once', '')
        );

        $rows = array();
        $rows2 = array();
        $labels = array();

        foreach ($results as $result) {
            $ftx = intval($result->getProperty('tx-bits-per-second'));
            $frx = intval($result->getProperty('rx-bits-per-second'));

            $rows[] = $ftx;
            $rows2[] = $frx;
            $labels[] = date('H:i:s');
        }

        $result = array(
            'labels' => $labels,
            'rows' => array(
                'tx' => $rows,
                'rx' => $rows2
            )
        );

        header('Content-Type: application/json');
        echo json_encode($result);

    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
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
