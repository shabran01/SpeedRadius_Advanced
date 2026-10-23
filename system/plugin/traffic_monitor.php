<?php
use PEAR2\Net\RouterOS;

register_menu("Traffic Monitor", true, "traffic_monitor_ui", 'AFTER_SETTINGS', 'ion ion-stats-bars', "Live", "blue");

function traffic_monitor_ui()
{
    global $ui, $routes;
    _admin();
    $ui->assign('_title', 'Live Traffic Monitor');
    // Must be a lowercase menu key from header.tpl: the AFTER_SETTINGS bucket
    // renders inside the Settings treeview, so the highlight key is "settings".
    $ui->assign('_system_menu', 'settings');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);

    // Get list of routers
    $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();

    $interfaceList = [];
    $ifaceError = '';

    if (count($routers) === 0) {
        $ui->assign('interfaces', $interfaceList);
        $ui->assign('ifaceError', 'No routers are enabled. Add one under Settings &rarr; Routers.');
        $ui->assign('routers', $routers);
        $ui->assign('router', '');
        $ui->display('traffic_monitor.tpl');
        return;
    }

    $router = isset($routes[2]) ? $routes[2] : '';
    if ($router === '') {
        $router = $routers[0]['id'];
    }

    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
    if (!$mikrotik) {
        // Bad router id in the URL — fall back to the first enabled router
        // instead of rendering a half-broken page.
        $router = $routers[0]['id'];
        $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
    }

    // Get interfaces for the selected router. A down router must not take the
    // whole page with it — report the failure instead of throwing.
    if ($mikrotik) {
        try {
            $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password'], 5);
            $interfaces = $client->sendSync(new RouterOS\Request('/interface/print'));
            foreach ($interfaces as $interface) {
                $interfaceList[] = $interface->getProperty('name');
            }
        } catch (Exception $e) {
            $ifaceError = 'Could not read interfaces from ' . $mikrotik['name'] . ': ' . $e->getMessage();
        }
    }

    $ui->assign('interfaces', $interfaceList);
    $ui->assign('ifaceError', $ifaceError);
    $ui->assign('routers', $routers);
    $ui->assign('router', $router);
    $ui->display('traffic_monitor.tpl');
}

function traffic_monitor_get_data()
{
    _admin();
    // Release the session lock so the 2s polling loop never serialises against
    // the page that opened it (and against other requests sharing the session).
    if (function_exists('session_write_close')) {
        session_write_close();
    }

    // RouterOS interface names are letters, digits, "-" and "_". Keep dots for
    // the rare rename, drop everything else (path chars included), and never echo
    // the raw input back.
    $interface = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', _req('interface', 'ether1'));
    if ($interface === '') {
        $interface = 'ether1';
    }

    global $routes;
    $router = isset($routes[2]) ? $routes[2] : '';

    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
    if (!$mikrotik) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Router not found']);
        exit;
    }

    try {
        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password'], 5);

        // Get traffic data — `once` makes monitor-traffic return one snapshot.
        $results = $client->sendSync(
            (new RouterOS\Request('/interface/monitor-traffic'))
                ->setArgument('interface', $interface)
                ->setArgument('once', '')
        );

        $rows = array();
        $rows2 = array();
        $labels = array();
        $totalRx = 0;
        $totalTx = 0;

        foreach ($results as $result) {
            $ftx = intval($result->getProperty('tx-bits-per-second'));
            $frx = intval($result->getProperty('rx-bits-per-second'));

            // Cumulative counters since boot — the UI now shows these too.
            $totalTx = intval($result->getProperty('tx-bytes'));
            $totalRx = intval($result->getProperty('rx-bytes'));

            $rows[] = $ftx;
            $rows2[] = $frx;
            $labels[] = date('H:i:s');
        }

        header('Content-Type: application/json');
        echo json_encode([
            'labels' => $labels,
            'rows' => [
                'tx' => $rows,
                'rx' => $rows2,
            ],
            'totals' => [
                'rx' => $totalRx,
                'tx' => $totalTx,
            ],
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}
