<?php
/**
 * Live Queue Monitor Plugin for SpeedRadius
 * 
 * Real-time MikroTik queue monitor — see which customer
 * is maxing bandwidth right now. Auto-refreshes via AJAX.
 * 
 * @package SpeedRadius
 * @version 1.0.0
 */

use PEAR2\Net\RouterOS;

register_menu("Live Queue", true, "live_queue_monitor", 'NETWORK', 'ion ion-speedometer', '', 'teal', ['Admin', 'SuperAdmin']);

function live_queue_monitor()
{
    global $ui, $config, $routes;
    $action = $routes['2'] ?? '';

    if ($action === 'api') {
        live_queue_monitor_api();
        return;
    }
    live_queue_monitor_display();
}

function live_queue_monitor_display()
{
    global $ui, $config;
    _admin();
    $admin = Admin::_info();

    $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
    if (count($routers) === 0) {
        r2(U . 'dashboard', 'e', 'No enabled routers found. Please add a router first.');
    }

    $ui->assign('_title', 'Live Queue Monitor');
    $ui->assign('_system_menu', 'plugin/live_queue_monitor');
    $ui->assign('_admin', $admin);
    $ui->assign('routers', $routers);
    $ui->assign('_L', $GLOBALS['_L'] ?? []);
    $ui->display('live_queue_monitor.tpl');
}

function live_queue_monitor_api()
{
    global $config;
    _admin();
    header('Content-Type: application/json');

    $router_id = _get('router_id');
    if (empty($router_id)) {
        echo json_encode(['success' => false, 'error' => 'No router selected']);
        exit;
    }

    $router = ORM::for_table('tbl_routers')->find_one($router_id);
    if (!$router) {
        echo json_encode(['success' => false, 'error' => 'Router not found']);
        exit;
    }

    try {
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);

        // ── Fetch simple queues ──
        $simpleQueues = [];
        try {
            $simpleResp = $client->sendSync(new RouterOS\Request('/queue/simple/print'));
            foreach ($simpleResp as $q) {
                $rateStr  = $q->getProperty('rate') ?? '0/0';
                $parts    = explode('/', $rateStr);
                $txRate   = live_queue_parse_rate($parts[0] ?? '0');
                $rxRate   = live_queue_parse_rate($parts[1] ?? '0');
                $txBytes  = (int)($q->getProperty('bytes') ?? 0);
                $rxBytes  = (int)($q->getProperty('packets') ?? 0); // simple queue reports total bytes, not split

                $simpleQueues[] = [
                    'id'       => $q->getProperty('.id') ?? '',
                    'name'     => $q->getProperty('name') ?? 'N/A',
                    'target'   => $q->getProperty('target') ?? '',
                    'comment'  => $q->getProperty('comment') ?? '',
                    'tx_rate'  => $txRate,
                    'rx_rate'  => $rxRate,
                    'tx_rate_str'  => live_queue_format_rate($txRate),
                    'rx_rate_str'  => live_queue_format_rate($rxRate),
                    'tx_bytes' => $txBytes,
                    'disabled' => $q->getProperty('disabled') ?? 'false',
                    'type'     => 'simple',
                ];
            }
        } catch (Exception $e) {}

        // ── Fetch queue tree ──
        $treeQueues = [];
        try {
            $treeResp = $client->sendSync(new RouterOS\Request('/queue/tree/print'));
            foreach ($treeResp as $q) {
                $rateStr  = $q->getProperty('max-limit') ?? $q->getProperty('limit-at') ?? '0/0';
                $parts    = explode('/', $rateStr);
                $txRate   = live_queue_parse_rate($parts[0] ?? '0');
                $rxRate   = live_queue_parse_rate($parts[1] ?? '0');
                $txBytes  = (int)($q->getProperty('bytes') ?? 0);

                $treeQueues[] = [
                    'id'       => $q->getProperty('.id') ?? '',
                    'name'     => $q->getProperty('name') ?? 'N/A',
                    'parent'   => $q->getProperty('parent') ?? '',
                    'target'   => '',
                    'comment'  => $q->getProperty('comment') ?? '',
                    'tx_rate'  => $txRate,
                    'rx_rate'  => $rxRate,
                    'tx_rate_str'  => live_queue_format_rate($txRate),
                    'rx_rate_str'  => live_queue_format_rate($rxRate),
                    'tx_bytes' => $txBytes,
                    'disabled' => $q->getProperty('disabled') ?? 'false',
                    'type'     => 'tree',
                ];
            }
        } catch (Exception $e) {}

        // Merge and sort by total rate (descending)
        $allQueues = array_merge($simpleQueues, $treeQueues);
        usort($allQueues, function($a, $b) {
            return ($b['tx_rate'] + $b['rx_rate']) - ($a['tx_rate'] + $a['rx_rate']);
        });

        // Stats
        $activeCount = 0;
        $totalTxRate = 0;
        $totalRxRate = 0;
        foreach ($allQueues as $q) {
            if ($q['disabled'] !== 'true') {
                $activeCount++;
                $totalTxRate += $q['tx_rate'];
                $totalRxRate += $q['rx_rate'];
            }
        }

        echo json_encode([
            'success'       => true,
            'router_name'   => $router['name'],
            'router_ip'     => $router['ip_address'],
            'total_count'   => count($allQueues),
            'active_count'  => $activeCount,
            'total_tx_rate' => $totalTxRate,
            'total_rx_rate' => $totalRxRate,
            'total_tx_str'  => live_queue_format_rate($totalTxRate),
            'total_rx_str'  => live_queue_format_rate($totalRxRate),
            'data'          => $allQueues,
            'timestamp'     => date('H:i:s'),
        ]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Router connection failed: ' . $e->getMessage()]);
    }
    exit;
}

/**
 * Parse MikroTik rate string (e.g. "5M", "256k", "1G") to bits per second
 */
function live_queue_parse_rate($str)
{
    $str = trim(strtoupper($str));
    if ($str === '0' || $str === '') return 0;
    $num = (float)$str;
    if (strpos($str, 'G') !== false) $num *= 1000000000;
    elseif (strpos($str, 'M') !== false) $num *= 1000000;
    elseif (strpos($str, 'k') !== false) $num *= 1000;
    return (int)$num;
}

/**
 * Format bits per second to human readable
 */
function live_queue_format_rate($bps)
{
    if ($bps >= 1000000000) return round($bps / 1000000000, 2) . ' Gbps';
    if ($bps >= 1000000)    return round($bps / 1000000, 2) . ' Mbps';
    if ($bps >= 1000)       return round($bps / 1000, 2) . ' Kbps';
    return $bps . ' bps';
}

/**
 * Format bytes to human readable
 */
function live_queue_format_bytes($bytes)
{
    $bytes = (int)$bytes;
    if ($bytes >= 1073741824) return round($bytes / 1073741824, 1) . ' GB';
    if ($bytes >= 1048576)    return round($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024)       return round($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}
