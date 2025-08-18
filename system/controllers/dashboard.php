<?php

/**
 *  PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 *  by https://t.me/ibnux
 **/

use PEAR2\Net\RouterOS;

require_once 'system/autoload/PEAR2/Autoload.php';
/**
 *  PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 *  by https://t.me/ibnux
 **/

_admin();
$ui->assign('_title', Lang::T('Dashboard'));
$ui->assign('_admin', $admin);

// Function to get total data usage from all MikroTik routers
function getTotalDataUsage() {
    global $CACHE_PATH;
    
    // Cache file for data usage (cache for 5 minutes)
    $cacheFile = $CACHE_PATH . File::pathFixer('/TotalDataUsage.temp');
    
    // Check if cache exists and is valid (5 minutes)
    if (file_exists($cacheFile) && time() - filemtime($cacheFile) < 300) {
        return json_decode(file_get_contents($cacheFile), true);
    }
    
    $totalRxBytes = 0;
    $totalTxBytes = 0;
    $totalBytes = 0;
    $activeRouters = 0;
    $routerDetails = [];
    
    try {
        // Get all enabled routers
        $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
        
        foreach ($routers as $router) {
            try {
                // Quick connection test first
                $fp = @fsockopen($router['ip_address'], $router['port'] ?: 8728, $errno, $errstr, 2);
                if (!$fp) {
                    continue; // Skip if router is not reachable
                }
                fclose($fp);
                
                // Connect to MikroTik router
                $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
                
                // Get all interfaces
                $request = new PEAR2\Net\RouterOS\Request('/interface/print');
                $interfaces = $client->sendSync($request);
                
                $routerRx = 0;
                $routerTx = 0;
                $wanInterfaceFound = false;
                
                if ($interfaces) {
                    // First, try to find WAN interface by checking routes
                    $routeRequest = new PEAR2\Net\RouterOS\Request('/ip/route/print');
                    $routeRequest->setQuery(PEAR2\Net\RouterOS\Query::where('dst-address', '0.0.0.0/0'));
                    $routes = $client->sendSync($routeRequest);
                    
                    $wanInterfaces = [];
                    if ($routes) {
                        foreach ($routes as $route) {
                            $gateway = $route->getProperty('gateway');
                            $interfaceName = $route->getProperty('interface');
                            
                            // If route has interface name, this is likely WAN
                            if ($interfaceName && !empty($interfaceName)) {
                                $wanInterfaces[] = $interfaceName;
                            }
                        }
                    }
                    
                    // If no WAN interfaces found via routes, try common naming patterns
                    if (empty($wanInterfaces)) {
                        foreach ($interfaces as $interface) {
                            $interfaceName = strtolower($interface->getProperty('name'));
                            $interfaceType = $interface->getProperty('type');
                            
                            // Common WAN interface patterns
                            if (preg_match('/^(ether1|wan|internet|pppoe-out|fiber|adsl)/', $interfaceName) ||
                                $interfaceType == 'pppoe-out' ||
                                (strpos($interfaceName, 'ether1') !== false && $interfaceType == 'ether')) {
                                $wanInterfaces[] = $interface->getProperty('name');
                            }
                        }
                    }
                    
                    // Get data from WAN interfaces
                    foreach ($interfaces as $interface) {
                        $interfaceName = $interface->getProperty('name');
                        
                        // Only process if this is a WAN interface
                        if (in_array($interfaceName, $wanInterfaces)) {
                            $rxBytes = (int)$interface->getProperty('rx-byte');
                            $txBytes = (int)$interface->getProperty('tx-byte');
                            
                            if ($rxBytes > 0 || $txBytes > 0) {
                                $routerRx += $rxBytes;
                                $routerTx += $txBytes;
                                $wanInterfaceFound = true;
                                
                                error_log("Router {$router['name']}: WAN interface {$interfaceName} - RX: " . formatBytes($rxBytes) . ", TX: " . formatBytes($txBytes));
                            }
                        }
                    }
                    
                    // If no WAN interface found, fallback to ether1 (most common WAN interface)
                    if (!$wanInterfaceFound) {
                        foreach ($interfaces as $interface) {
                            $interfaceName = $interface->getProperty('name');
                            $interfaceType = $interface->getProperty('type');
                            
                            if ($interfaceName == 'ether1' && $interfaceType == 'ether') {
                                $rxBytes = (int)$interface->getProperty('rx-byte');
                                $txBytes = (int)$interface->getProperty('tx-byte');
                                
                                if ($rxBytes > 0 || $txBytes > 0) {
                                    $routerRx += $rxBytes;
                                    $routerTx += $txBytes;
                                    $wanInterfaceFound = true;
                                    
                                    error_log("Router {$router['name']}: Fallback to ether1 - RX: " . formatBytes($rxBytes) . ", TX: " . formatBytes($txBytes));
                                }
                                break;
                            }
                        }
                    }
                }
                
                $totalRxBytes += $routerRx;
                $totalTxBytes += $routerTx;
                $activeRouters++;
                
                $routerDetails[] = [
                    'name' => $router['name'],
                    'rx' => formatBytes($routerRx),
                    'tx' => formatBytes($routerTx),
                    'total' => formatBytes($routerRx + $routerTx),
                    'wan_found' => $wanInterfaceFound
                ];
                
            } catch (Exception $e) {
                // Log error but continue with other routers
                error_log("Error getting data usage from router {$router['name']}: " . $e->getMessage());
                continue;
            }
        }
        
        $totalBytes = $totalRxBytes + $totalTxBytes;
        
        // Format the data
        $result = [
            'total_rx' => formatBytes($totalRxBytes),
            'total_tx' => formatBytes($totalTxBytes), 
            'total_usage' => formatBytes($totalBytes),
            'total_rx_raw' => $totalRxBytes,
            'total_tx_raw' => $totalTxBytes,
            'total_usage_raw' => $totalBytes,
            'active_routers' => $activeRouters,
            'router_details' => $routerDetails,
            'last_updated' => date('Y-m-d H:i:s')
        ];
        
        // Cache the result
        file_put_contents($cacheFile, json_encode($result));
        
        return $result;
        
    } catch (Exception $e) {
        error_log("Error in getTotalDataUsage: " . $e->getMessage());
        return [
            'total_rx' => '0 B',
            'total_tx' => '0 B',
            'total_usage' => '0 B',
            'total_rx_raw' => 0,
            'total_tx_raw' => 0,
            'total_usage_raw' => 0,
            'active_routers' => 0,
            'router_details' => [],
            'last_updated' => date('Y-m-d H:i:s'),
            'error' => $e->getMessage()
        ];
    }
}

// Function to format bytes
function formatBytes($bytes) {
    if ($bytes >= 1099511627776) {
        return round($bytes / 1099511627776, 2) . ' TB';
    } elseif ($bytes >= 1073741824) {
        return round($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' B';
}

if (isset($_GET['refresh'])) {
    $files = scandir($CACHE_PATH);
    foreach ($files as $file) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (is_file($CACHE_PATH . DIRECTORY_SEPARATOR . $file) && $ext == 'temp') {
            unlink($CACHE_PATH . DIRECTORY_SEPARATOR . $file);
        }
    }
    r2(U . 'dashboard', 's', 'Data Refreshed');
}

$reset_day = $config['reset_day'];
if (empty($reset_day)) {
    $reset_day = 1;
}
//first day of month
if (date("d") >= $reset_day) {
    $start_date = date('Y-m-' . $reset_day);
} else {
    $start_date = date('Y-m-' . $reset_day, strtotime("-1 MONTH"));
}

$current_date = date('Y-m-d');
$month_n = date('n');

$iday = ORM::for_table('tbl_transactions')
    ->where('recharged_on', $current_date)
    ->where_not_equal('method', 'Customer - Balance')
    ->where_not_equal('method', 'Recharge Balance - Administrator')
    ->sum('price');

if ($iday == '') {
    $iday = '0.00';
}
$ui->assign('iday', $iday);

$imonth = ORM::for_table('tbl_transactions')
    ->where_not_equal('method', 'Customer - Balance')
    ->where_not_equal('method', 'Recharge Balance - Administrator')
    ->where_gte('recharged_on', $start_date)
    ->where_lte('recharged_on', $current_date)->sum('price');
if ($imonth == '') {
    $imonth = '0.00';
}
$ui->assign('imonth', $imonth);

// Get total online PPPoE users across all routers
$online_pppoe = ORM::for_table('tbl_user_recharges')
    ->where('status', 'on')
    ->where('type', 'PPPOE')
    ->count();
$ui->assign('online_users', $online_pppoe);

// Get total online Hotspot users across all routers
$online_hotspot = ORM::for_table('tbl_user_recharges')
    ->where('status', 'on')
    ->where('type', 'Hotspot')
    ->count();
$ui->assign('hotspot_users', $online_hotspot);

// Calculate total online users
$total_online = $online_pppoe + $online_hotspot;
$ui->assign('total_online', $total_online);

// Get total expired PPPoE users
$expired_pppoe = ORM::for_table('tbl_user_recharges')
    ->where('status', 'off')
    ->where('type', 'PPPOE')
    ->where_lte('expiration', $current_date)
    ->count();
$ui->assign('expired_pppoe', $expired_pppoe);

// Get total expired Hotspot users
$expired_hotspot = ORM::for_table('tbl_user_recharges')
    ->where('status', 'off')
    ->where('type', 'Hotspot')
    ->where_lte('expiration', $current_date)
    ->count();
$ui->assign('expired_hotspot', $expired_hotspot);

// Calculate total expired users
$total_expired = $expired_pppoe + $expired_hotspot;
$ui->assign('total_expired', $total_expired);

if ($config['enable_balance'] == 'yes'){
    $cb = ORM::for_table('tbl_customers')->whereGte('balance', 0)->sum('balance');
    $ui->assign('cb', $cb);
}

$u_act = ORM::for_table('tbl_user_recharges')->where('status', 'on')->count();
if (empty($u_act)) {
    $u_act = '0';
}
$ui->assign('u_act', $u_act);

$u_all = ORM::for_table('tbl_user_recharges')->count();
if (empty($u_all)) {
    $u_all = '0';
}
$ui->assign('u_all', $u_all);


$c_all = ORM::for_table('tbl_customers')->count();
if (empty($c_all)) {
    $c_all = '0';
}
$ui->assign('c_all', $c_all);

if ($config['hide_uet'] != 'yes') {
    //user expire today
    $query = ORM::for_table('tbl_user_recharges')
        ->where_lte('expiration', $current_date)
        ->where_gte('expiration', date('Y-m-d', strtotime('-1 day')))
        ->order_by_desc('expiration');
    $expire = Paginator::findMany($query);

    // Get the total count of expired records for pagination
    $totalCount = ORM::for_table('tbl_user_recharges')
        ->where_lte('expiration', $current_date)
        ->where_gte('expiration', date('Y-m-d', strtotime('-1 day')))
        ->count();

    // Pass the total count and current page to the paginator
    $paginator['total_count'] = $totalCount;

    // Assign the pagination HTML to the template variable
    $ui->assign('expire', $expire);
    
    // All expired users (not just today)
    $query_all_expired = ORM::for_table('tbl_user_recharges')
        ->where_lte('expiration', $current_date)
        ->where('status', 'off')
        ->order_by_desc('expiration');
    $all_expired = Paginator::findMany($query_all_expired);

    // Get the total count of all expired records for pagination
    $totalExpiredCount = ORM::for_table('tbl_user_recharges')
        ->where_lte('expiration', $current_date)
        ->where('status', 'off')
        ->count();

    // Pass the total count and current page to the paginator
    $paginator['total_expired_count'] = $totalExpiredCount;

    // Assign the pagination HTML to the template variable
    $ui->assign('all_expired', $all_expired);
}

//activity log
$dlog = ORM::for_table('tbl_logs')->limit(5)->order_by_desc('id')->find_many();
$ui->assign('dlog', $dlog);
$log = ORM::for_table('tbl_logs')->count();
$ui->assign('log', $log);

// Include server statistics
require_once 'system/server_stats.php';
$server_stats = getServerStatistics();
$ui->assign('server_stats', $server_stats);

// Get total data usage from all MikroTik routers
$total_data_usage = getTotalDataUsage();
$ui->assign('total_data_usage', $total_data_usage);

if ($config['hide_vs'] != 'yes') {
    $cacheStocksfile = $CACHE_PATH . File::pathFixer('/VoucherStocks.temp');
    $cachePlanfile = $CACHE_PATH . File::pathFixer('/VoucherPlans.temp');
    //Cache for 5 minutes
    if (file_exists($cacheStocksfile) && time() - filemtime($cacheStocksfile) < 600) {
        $stocks = json_decode(file_get_contents($cacheStocksfile), true);
        $plans = json_decode(file_get_contents($cachePlanfile), true);
    } else {
        // Count stock
        $tmp = $v = ORM::for_table('tbl_plans')->select('id')->select('name_plan')->find_many();
        $plans = array();
        $stocks = array("used" => 0, "unused" => 0);
        $n = 0;
        foreach ($tmp as $plan) {
            $unused = ORM::for_table('tbl_voucher')
                ->where('id_plan', $plan['id'])
                ->where('status', 0)->count();
            $used = ORM::for_table('tbl_voucher')
                ->where('id_plan', $plan['id'])
                ->where('status', 1)->count();
            if ($unused > 0 || $used > 0) {
                $plans[$n]['name_plan'] = $plan['name_plan'];
                $plans[$n]['unused'] = $unused;
                $plans[$n]['used'] = $used;
                $stocks["unused"] += $unused;
                $stocks["used"] += $used;
                $n++;
            }
        }
        file_put_contents($cacheStocksfile, json_encode($stocks));
        file_put_contents($cachePlanfile, json_encode($plans));
    }
}

$cacheMRfile = File::pathFixer('/monthlyRegistered.temp');
//Cache for 1 hour
if (file_exists($cacheMRfile) && time() - filemtime($cacheMRfile) < 3600) {
    $monthlyRegistered = json_decode(file_get_contents($cacheMRfile), true);
} else {
    //Monthly Registered Customers
    $result = ORM::for_table('tbl_customers')
        ->select_expr('MONTH(created_at)', 'month')
        ->select_expr('COUNT(*)', 'count')
        ->where_raw('YEAR(created_at) = YEAR(NOW())')
        ->group_by_expr('MONTH(created_at)')
        ->find_many();

    $monthlyRegistered = [];
    foreach ($result as $row) {
        $monthlyRegistered[] = [
            'date' => $row->month,
            'count' => $row->count
        ];
    }
    file_put_contents($cacheMRfile, json_encode($monthlyRegistered));
}

$cacheMSfile = $CACHE_PATH . File::pathFixer('/monthlySales.temp');
//Cache for 12 hours
if (file_exists($cacheMSfile) && time() - filemtime($cacheMSfile) < 43200) {
    $monthlySales = json_decode(file_get_contents($cacheMSfile), true);
} else {
    // Query to retrieve monthly data
    $results = ORM::for_table('tbl_transactions')
        ->select_expr('MONTH(recharged_on)', 'month')
        ->select_expr('SUM(price)', 'total')
        ->where_raw("YEAR(recharged_on) = YEAR(CURRENT_DATE())") // Filter by the current year
        ->where_not_equal('method', 'Customer - Balance')
        ->where_not_equal('method', 'Recharge Balance - Administrator')
        ->group_by_expr('MONTH(recharged_on)')
        ->find_many();

    // Create an array to hold the monthly sales data
    $monthlySales = array();

    // Iterate over the results and populate the array
    foreach ($results as $result) {
        $month = $result->month;
        $totalSales = $result->total;

        $monthlySales[$month] = array(
            'month' => $month,
            'totalSales' => $totalSales
        );
    }

    // Fill in missing months with zero sales
    for ($month = 1; $month <= 12; $month++) {
        if (!isset($monthlySales[$month])) {
            $monthlySales[$month] = array(
                'month' => $month,
                'totalSales' => 0
            );
        }
    }

    // Sort the array by month
    ksort($monthlySales);

    // Reindex the array
    $monthlySales = array_values($monthlySales);
    file_put_contents($cacheMSfile, json_encode($monthlySales));
}

if ($config['router_check']) {
    $routeroffs = ORM::for_table('tbl_routers')->selects(['id', 'name', 'last_seen'])->where('status', 'Offline')->where('enabled', '1')->order_by_desc('name')->find_array();
    $ui->assign('routeroffs', $routeroffs);
}

$timestampFile = "$UPLOAD_PATH/cron_last_run.txt";
if (file_exists($timestampFile)) {
    $lastRunTime = file_get_contents($timestampFile);
    $ui->assign('run_date', date('Y-m-d h:i:s A', $lastRunTime));
}

// Assign the monthly sales data to Smarty
$ui->assign('start_date', $start_date);
$ui->assign('current_date', $current_date);
$ui->assign('monthlySales', $monthlySales);
$ui->assign('xfooter', '');
$ui->assign('monthlyRegistered', $monthlyRegistered);
$ui->assign('stocks', $stocks);
$ui->assign('plans', $plans);
$ui->assign('csrf_token', Csrf::generateAndStoreToken());

// Get all routers for the filter dropdown
$routers = ORM::for_table('tbl_routers')->find_many();
$ui->assign('routers', $routers);

if (isset($routes[1]) && $routes[1] == 'filter') {
    header('Content-Type: application/json');
    
    try {
        $router_id = _post('router_id');
        error_log("Router ID received: " . $router_id);
        
        // Get router name if specific router selected
        if($router_id != 'all') {
            $router = ORM::for_table('tbl_routers')->find_one($router_id);
            if(!$router) {
                throw new Exception('Router not found');
            }
            $router_name = $router->name;
            error_log("Router name: " . $router_name);
        }
        
        $data = array();
        $current_date = date('Y-m-d');
        $start_date = date('Y-m-01');
        
        if ($router_id == 'all') {
            $data['income_today'] = ORM::for_table('tbl_transactions')
                ->where('recharged_on', $current_date)
                ->sum('price') ?: "0";
                
            $data['income_month'] = ORM::for_table('tbl_transactions')
                ->where_gte('recharged_on', $start_date)
                ->where_lte('recharged_on', $current_date)
                ->sum('price') ?: "0";
            
            // Get active users (status = 'on')
            $data['active_users'] = ORM::for_table('tbl_user_recharges')
                ->where('status', 'on')
                ->count();
            
            // Get expired users (status = 'off')
            $data['expired_users'] = ORM::for_table('tbl_user_recharges')
                ->where('status', 'off')
                ->count();
            
            // Get all online PPPoE users across all routers
            $data['online_users'] = ORM::for_table('tbl_user_recharges')
                ->where('status', 'on')
                ->where('type', 'PPPOE')
                ->count();
                
            // Get all online Hotspot users across all routers
            $data['hotspot_users'] = ORM::for_table('tbl_user_recharges')
                ->where('status', 'on')
                ->where('type', 'Hotspot')
                ->count();
            
            // Calculate total online users
            $data['total_online'] = $data['online_users'] + $data['hotspot_users'];
            
        } else {
            $data['income_today'] = ORM::for_table('tbl_transactions')
                ->where('recharged_on', $current_date)
                ->where('routers', $router_name)
                ->sum('price') ?: "0";
            
            $data['income_month'] = ORM::for_table('tbl_transactions')
                ->where('routers', $router_name)
                ->where_gte('recharged_on', $start_date)
                ->where_lte('recharged_on', $current_date)
                ->sum('price') ?: "0";
            
            // Get active users for this router
            $data['active_users'] = ORM::for_table('tbl_user_recharges')
                ->where('routers', $router_name)
                ->where('status', 'on')
                ->count();
            
            // Get expired users for this router
            $data['expired_users'] = ORM::for_table('tbl_user_recharges')
                ->where('routers', $router_name)
                ->where('status', 'off')
                ->count();
            
            // Get online PPPoE users for this router
            $data['online_users'] = ORM::for_table('tbl_user_recharges')
                ->where('routers', $router_name)
                ->where('status', 'on')
                ->where('type', 'PPPOE')
                ->count();
                
            // Get online Hotspot users for this router
            $data['hotspot_users'] = ORM::for_table('tbl_user_recharges')
                ->where('routers', $router_name)
                ->where('status', 'on')
                ->where('type', 'Hotspot')
                ->count();
                
            // Calculate total online users for this router
            $data['total_online'] = $data['online_users'] + $data['hotspot_users'];
        }
        
        // Format the numbers
        $data['income_today'] = number_format((float)$data['income_today'], 0, $_c['dec_point'], $_c['thousands_sep']);
        $data['income_month'] = number_format((float)$data['income_month'], 0, $_c['dec_point'], $_c['thousands_sep']);
        
        error_log("Final data being sent: " . print_r($data, true));
        echo json_encode($data);
        
    } catch (Exception $e) {
        error_log("Error in dashboard filter: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    
    exit();
}

if (isset($routes[1]) && $routes[1] == 'refresh-data-usage') {
    header('Content-Type: application/json');
    
    try {
        // Clear cache to force fresh data
        $cacheFile = $CACHE_PATH . File::pathFixer('/TotalDataUsage.temp');
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
        
        // Get fresh data usage
        $total_data_usage = getTotalDataUsage();
        
        echo json_encode($total_data_usage);
        
    } catch (Exception $e) {
        error_log("Error refreshing data usage: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    
    exit();
}

run_hook('view_dashboard'); #HOOK
$ui->display('dashboard.tpl');
