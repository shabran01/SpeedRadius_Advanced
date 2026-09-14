<?php

/**
 * PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 **/

_admin();
$admin = Admin::_info();
$ui->assign('_admin', $admin);

if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
    r2(U . 'dashboard', 'e', Lang::T('You do not have permission to access this page'));
}

switch ($routes[1]) {
    case 'get-stats':
        if (!isset($routes[2])) {
            jsonResponse(['error' => 'Customer ID not provided']);
        }

        $customer_id = $routes[2];
        $customer = ORM::for_table('tbl_customers')->find_one($customer_id);
        
        if (!$customer) {
            jsonResponse(['error' => 'Customer not found']);
        }

        try {
            require_once 'system/autoload/Mikrotik.php';
            $router = ORM::for_table('tbl_routers')->where('id', $customer['router_id'])->find_one();
            
            if (!$router) {
                jsonResponse(['error' => 'Router not found']);
            }

            $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
            
            if (!$client) {
                jsonResponse(['error' => 'Could not connect to router']);
            }

            // Get interface statistics
            $stats = [];
            if ($customer['service_type'] == 'PPPoE') {
                // Get PPPoE connection stats
                $request = new \PEAR2\Net\RouterOS\Request('/interface pppoe-client monitor');
                $request->setArgument('numbers', '0');
                $request->setArgument('once', '');
                $response = $client->sendSync($request);
                
                foreach ($response as $item) {
                    $stats['download'] = formatBitsPerSecond($item->getProperty('rx-bits-per-second'));
                    $stats['upload'] = formatBitsPerSecond($item->getProperty('tx-bits-per-second'));
                    $stats['signal'] = $item->getProperty('signal-strength');
                }

                // Get ping stats
                $request = new \PEAR2\Net\RouterOS\Request('/ping');
                $request->setArgument('count', '3');
                $request->setArgument('address', '8.8.8.8');
                $response = $client->sendSync($request);
                
                $totalPing = 0;
                $received = 0;
                foreach ($response as $item) {
                    if ($item->getProperty('time')) {
                        $totalPing += intval($item->getProperty('time'));
                        $received++;
                    }
                }
                
                $stats['ping'] = $received > 0 ? round($totalPing / $received) : 0;
                $stats['packet_loss'] = $received > 0 ? round((3 - $received) / 3 * 100) : 100;
            } else {
                // Get Hotspot connection stats
                $request = new \PEAR2\Net\RouterOS\Request('/interface wireless monitor');
                $request->setArgument('numbers', '0');
                $request->setArgument('once', '');
                $response = $client->sendSync($request);
                
                foreach ($response as $item) {
                    $stats['download'] = formatBitsPerSecond($item->getProperty('rx-bits-per-second'));
                    $stats['upload'] = formatBitsPerSecond($item->getProperty('tx-bits-per-second'));
                    $stats['signal'] = $item->getProperty('signal-strength');
                }

                // Get ping stats similar to PPPoE
                $request = new \PEAR2\Net\RouterOS\Request('/ping');
                $request->setArgument('count', '3');
                $request->setArgument('address', '8.8.8.8');
                $response = $client->sendSync($request);
                
                $totalPing = 0;
                $received = 0;
                foreach ($response as $item) {
                    if ($item->getProperty('time')) {
                        $totalPing += intval($item->getProperty('time'));
                        $received++;
                    }
                }
                
                $stats['ping'] = $received > 0 ? round($totalPing / $received) : 0;
                $stats['packet_loss'] = $received > 0 ? round((3 - $received) / 3 * 100) : 100;
            }

            jsonResponse($stats);
        } catch (Exception $e) {
            jsonResponse(['error' => $e->getMessage()]);
        }
        break;

    default:
        r2(U . 'dashboard', 'e', Lang::T('Invalid URL'));
}

function formatBitsPerSecond($bps) {
    if (!$bps) return 0;
    $bps = intval($bps);
    return round($bps / 1000000, 2); // Convert to Mbps
}

function jsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}
