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
function getTotalDataUsage()
{
    global $CACHE_PATH;

    // Cache file for data usage (cache for 10 minutes)
    $cacheFile = $CACHE_PATH . File::pathFixer('/TotalDataUsage.temp');

    // Check if cache exists and is valid (10 minutes)
    if (file_exists($cacheFile) && time() - filemtime($cacheFile) < 600) {
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
                // Skip routers that are marked as offline (checked by cron)
                if (isset($router['status']) && $router['status'] == 'Offline') {
                    error_log("Skipping offline router: {$router['name']}");
                    continue;
                }

                // Quick connection test first (reduced timeout to 0.5 seconds)
                $fp = @fsockopen($router['ip_address'], $router['port'] ?: 8728, $errno, $errstr, 0.5);
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
                            if (
                                preg_match('/^(ether1|wan|internet|pppoe-out|fiber|adsl)/', $interfaceName) ||
                                $interfaceType == 'pppoe-out' ||
                                (strpos($interfaceName, 'ether1') !== false && $interfaceType == 'ether')
                            ) {
                                $wanInterfaces[] = $interface->getProperty('name');
                            }
                        }
                    }

                    // Get data from WAN interfaces
                    foreach ($interfaces as $interface) {
                        $interfaceName = $interface->getProperty('name');

                        // Only process if this is a WAN interface
                        if (in_array($interfaceName, $wanInterfaces)) {
                            $rxBytes = (int) $interface->getProperty('rx-byte');
                            $txBytes = (int) $interface->getProperty('tx-byte');

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
                                $rxBytes = (int) $interface->getProperty('rx-byte');
                                $txBytes = (int) $interface->getProperty('tx-byte');

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
function formatBytes($bytes)
{
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

if ($iday == '' || $iday === null) {
    $iday = '0.00';
} else {
    $iday = number_format((float) $iday, 2, '.', '');
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

// Revenue Comparison: This Month vs Last Month
$first_day_this_month = date('Y-m-01');
$last_day_this_month = date('Y-m-t');
$first_day_last_month = date('Y-m-01', strtotime('first day of last month'));
$last_day_last_month = date('Y-m-t', strtotime('last day of last month'));

$revenue_this_month = ORM::for_table('tbl_transactions')
    ->where_not_equal('method', 'Customer - Balance')
    ->where_not_equal('method', 'Recharge Balance - Administrator')
    ->where_gte('recharged_on', $first_day_this_month)
    ->where_lte('recharged_on', $last_day_this_month)
    ->sum('price');

$revenue_last_month = ORM::for_table('tbl_transactions')
    ->where_not_equal('method', 'Customer - Balance')
    ->where_not_equal('method', 'Recharge Balance - Administrator')
    ->where_gte('recharged_on', $first_day_last_month)
    ->where_lte('recharged_on', $last_day_last_month)
    ->sum('price');

if ($revenue_this_month == '') {
    $revenue_this_month = '0.00';
}
if ($revenue_last_month == '') {
    $revenue_last_month = '0.00';
}

// Calculate percentage change
$revenue_change = 0;
$revenue_change_percent = 0;
if ($revenue_last_month > 0) {
    $revenue_change = $revenue_this_month - $revenue_last_month;
    $revenue_change_percent = (($revenue_change) / $revenue_last_month) * 100;
}

$ui->assign('revenue_this_month', $revenue_this_month);
$ui->assign('revenue_last_month', $revenue_last_month);
$ui->assign('revenue_change', $revenue_change);
$ui->assign('revenue_change_percent', number_format($revenue_change_percent, 1));

// Year-over-Year Revenue Comparison: This Year (Jan 1 to Today) vs Same Period Last Year
$first_day_this_year = date('Y-01-01');
$today = date('Y-m-d');
$first_day_last_year = date('Y-01-01', strtotime('-1 year'));
$same_day_last_year = date('Y-m-d', strtotime('-1 year'));

$revenue_this_ytd = ORM::for_table('tbl_transactions')
    ->where_not_equal('method', 'Customer - Balance')
    ->where_not_equal('method', 'Recharge Balance - Administrator')
    ->where_gte('recharged_on', $first_day_this_year)
    ->where_lte('recharged_on', $today)
    ->sum('price');

$revenue_last_ytd = ORM::for_table('tbl_transactions')
    ->where_not_equal('method', 'Customer - Balance')
    ->where_not_equal('method', 'Recharge Balance - Administrator')
    ->where_gte('recharged_on', $first_day_last_year)
    ->where_lte('recharged_on', $same_day_last_year)
    ->sum('price');

if ($revenue_this_ytd == '') $revenue_this_ytd = '0.00';
if ($revenue_last_ytd == '') $revenue_last_ytd = '0.00';

$yoy_change = 0;
$yoy_change_percent = 0;
if ($revenue_last_ytd > 0) {
    $yoy_change = $revenue_this_ytd - $revenue_last_ytd;
    $yoy_change_percent = ($yoy_change / $revenue_last_ytd) * 100;
}

$ui->assign('revenue_this_ytd', $revenue_this_ytd);
$ui->assign('revenue_last_ytd', $revenue_last_ytd);
$ui->assign('yoy_change', $yoy_change);
$ui->assign('yoy_change_percent', number_format($yoy_change_percent, 1));
$ui->assign('yoy_current_label', date('Y') . ' (Jan-' . date('M') . ')');
$ui->assign('yoy_last_label', date('Y', strtotime('-1 year')) . ' (Jan-' . date('M', strtotime('-1 year')) . ')');

// Most Popular Plans (Top 5 based on active subscriptions and revenue)
$most_popular_plans = ORM::for_table('tbl_transactions')
    ->select('plan_name')
    ->select_expr('COUNT(*)', 'subscription_count')
    ->select_expr('SUM(price)', 'total_revenue')
    ->where_gte('recharged_on', $first_day_this_month)
    ->where_not_equal('method', 'Customer - Balance')
    ->where_not_equal('method', 'Recharge Balance - Administrator')
    ->group_by('plan_name')
    ->order_by_desc('subscription_count')
    ->limit(5)
    ->find_array();

$ui->assign('most_popular_plans', $most_popular_plans);

// Get total online PPPoE users — prefer router-sourced cache written by cron
$pppoe_cache_file = $CACHE_PATH . DIRECTORY_SEPARATOR . 'pppoe_online_count.json';
$online_pppoe = null;
if (file_exists($pppoe_cache_file) && (time() - filemtime($pppoe_cache_file)) < 600) {
    $pppoe_cache = json_decode(file_get_contents($pppoe_cache_file), true);
    if (isset($pppoe_cache['total'])) {
        $online_pppoe = (int)$pppoe_cache['total'];
    }
}
if ($online_pppoe === null) {
    // Fallback: count active subscriptions from database
    $online_pppoe = ORM::for_table('tbl_user_recharges')
        ->where('status', 'on')
        ->where('type', 'PPPOE')
        ->count();
}
$ui->assign('online_users', $online_pppoe);

// Get total online Hotspot users — prefer router-sourced cache written by cron
$hotspot_cache_file = $CACHE_PATH . DIRECTORY_SEPARATOR . 'hotspot_online_count.json';
$online_hotspot = null;
if (file_exists($hotspot_cache_file) && (time() - filemtime($hotspot_cache_file)) < 600) {
    $hotspot_cache = json_decode(file_get_contents($hotspot_cache_file), true);
    if (isset($hotspot_cache['total'])) {
        $online_hotspot = (int)$hotspot_cache['total'];
    }
}
if ($online_hotspot === null) {
    // Fallback: count active subscriptions from database
    $online_hotspot = ORM::for_table('tbl_user_recharges')
        ->where('status', 'on')
        ->where('type', 'Hotspot')
        ->count();
}
$ui->assign('hotspot_users', $online_hotspot);

// Calculate total online users (this is the sum for ALL routers)
$total_online = $online_pppoe + $online_hotspot;
$ui->assign('total_online', $total_online);

// Total PPPoE subscribers in the system (on + off)
$total_pppoe = ORM::for_table('tbl_user_recharges')
    ->where('type', 'PPPOE')
    ->count();
$ui->assign('total_pppoe', $total_pppoe);

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

if ($config['enable_balance'] == 'yes') {
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
    $ui->assign('expiring_today', $totalCount);

    // Assign the pagination HTML to the template variable
    $ui->assign('expire', $expire);

    // All expired PPPoE users (status off, type PPPOE)
    $expire_pppoe_today = ORM::for_table('tbl_user_recharges')
        ->where('status', 'off')
        ->where('type', 'PPPOE')
        ->order_by_desc('expiration')
        ->limit(50)
        ->find_many();
    $ui->assign('expire_pppoe_today', $expire_pppoe_today);
    $ui->assign('expire_pppoe_count', count($expire_pppoe_today));
}

//activity log
$dlog = ORM::for_table('tbl_logs')->limit(5)->order_by_desc('id')->find_many();
$ui->assign('dlog', $dlog);
$log = ORM::for_table('tbl_logs')->count();
$ui->assign('log', $log);

// Top 5 downloaders this month (Hotspot & PPPoE)
try {
    $current_month = date('Y-m');
    $db = ORM::get_db();

    // Helper to format bytes nicely
    $fmt_bytes = function ($bytes) {
        $bytes = (int)$bytes;
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 2)    . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 2)       . ' KB';
        return $bytes . ' B';
    };

    // Hotspot top 5 (customers with no pppoe_username set)
    $stmt = $db->prepare("
        SELECT mu.download_bytes, mu.upload_bytes, c.username, c.fullname
        FROM tbl_customer_monthly_usage mu
        INNER JOIN tbl_customers c ON c.id = mu.customer_id
        WHERE mu.month = ?
          AND (c.pppoe_username IS NULL OR c.pppoe_username = '')
        ORDER BY mu.download_bytes DESC
        LIMIT 5
    ");
    $stmt->execute([$current_month]);
    $raw_hs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $top_hotspot = [];
    foreach ($raw_hs as $r) {
        $r['download_fmt'] = $fmt_bytes($r['download_bytes']);
        $r['upload_fmt']   = $fmt_bytes($r['upload_bytes']);
        $r['total_fmt']    = $fmt_bytes((int)$r['download_bytes'] + (int)$r['upload_bytes']);
        $top_hotspot[] = $r;
    }
    $ui->assign('top_hotspot_downloaders', $top_hotspot);

    // PPPoE top 5 (customers with pppoe_username set)
    $stmt2 = $db->prepare("
        SELECT mu.download_bytes, mu.upload_bytes, c.username, c.fullname, c.pppoe_username
        FROM tbl_customer_monthly_usage mu
        INNER JOIN tbl_customers c ON c.id = mu.customer_id
        WHERE mu.month = ?
          AND c.pppoe_username IS NOT NULL
          AND c.pppoe_username != ''
        ORDER BY mu.download_bytes DESC
        LIMIT 5
    ");
    $stmt2->execute([$current_month]);
    $raw_pp = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    $top_pppoe = [];
    foreach ($raw_pp as $r) {
        $r['download_fmt'] = $fmt_bytes($r['download_bytes']);
        $r['upload_fmt']   = $fmt_bytes($r['upload_bytes']);
        $r['total_fmt']    = $fmt_bytes((int)$r['download_bytes'] + (int)$r['upload_bytes']);
        $top_pppoe[] = $r;
    }
    $ui->assign('top_pppoe_downloaders', $top_pppoe);
    $ui->assign('top_downloaders_month', date('F Y', strtotime($current_month . '-01')));
} catch (Exception $e) {
    $ui->assign('top_hotspot_downloaders', []);
    $ui->assign('top_pppoe_downloaders', []);
    $ui->assign('top_downloaders_month', date('F Y'));
}

// Include server statistics
require_once 'system/server_stats.php';
$server_stats = getServerStatistics();
$ui->assign('server_stats', $server_stats);

// Get total data usage from all MikroTik routers (used for router details / active count)
$total_data_usage = getTotalDataUsage();

// Override the headline stats with DB-accumulated monthly totals.
// tbl_router_monthly_usage is keyed by YYYY-MM, so it auto-resets on the 1st of every month.
require_once 'system/helpers/monthly_usage.php';
monthly_usage_ensure_router_table();
$monthly_network = monthly_usage_get_network_totals(); // sums all routers for current month
$total_data_usage['total_rx']     = formatBytes($monthly_network['download_bytes']);
$total_data_usage['total_tx']     = formatBytes($monthly_network['upload_bytes']);
$total_data_usage['total_usage']  = formatBytes($monthly_network['total_bytes']);
$total_data_usage['current_month'] = date('F Y');
$total_data_usage['resets_on']     = date('Y-m-01', strtotime('first day of next month'));

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

// Weekly Sales Data
$cacheWSfile = $CACHE_PATH . File::pathFixer('/weeklySales.temp');
//Cache for 1 hour
if (file_exists($cacheWSfile) && time() - filemtime($cacheWSfile) < 3600) {
    $weeklySales = json_decode(file_get_contents($cacheWSfile), true);
} else {
    // Get the last 8 weeks of sales data
    $weeklySales = array();

    // Anchor to this week's Monday to avoid day-of-week-dependent strtotime bugs
    $thisMonday = strtotime('monday this week');

    for ($i = 7; $i >= 0; $i--) {
        // Calculate week start and end dates (Monday to Sunday)
        if ($i == 0) {
            // Current week: Monday to today
            $weekStart = date('Y-m-d', $thisMonday);
            $weekEnd = date('Y-m-d');
        } else {
            // Past weeks: go back exactly $i * 7 days from this Monday
            $weekStart = date('Y-m-d', $thisMonday - ($i * 7 * 86400));
            $weekEnd = date('Y-m-d', $thisMonday - (($i - 1) * 7 * 86400) - 86400);
        }

        // Query sales for this week - using same method as monthly sales
        $results = ORM::for_table('tbl_transactions')
            ->where_gte('recharged_on', $weekStart)
            ->where_lte('recharged_on', $weekEnd)
            ->where_not_equal('method', 'Customer - Balance')
            ->where_not_equal('method', 'Recharge Balance - Administrator')
            ->select_expr('SUM(price)', 'total')
            ->find_one();

        $totalSales = $results ? $results->total : 0;

        $weeklySales[] = array(
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
            'week_label' => date('M d', strtotime($weekStart)) . ' - ' . date('M d', strtotime($weekEnd)),
            'totalSales' => $totalSales ? floatval($totalSales) : 0
        );
    }

    file_put_contents($cacheWSfile, json_encode($weeklySales));
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

// Get last 5 transactions
$lastTransactions = ORM::for_table('tbl_transactions')
    ->select_many('id', 'invoice', 'username', 'plan_name', 'price', 'recharged_on', 'recharged_time', 'method', 'routers')
    ->order_by_desc('id')
    ->limit(5)
    ->find_array();


// Get Top 5 Most Active Users (based on transactions in last 30 days)
$thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
$topActiveUsers = ORM::for_table('tbl_transactions')
    ->select('username')
    ->select_expr('COUNT(*)', 'transaction_count')
    ->select_expr('SUM(price)', 'total_spent')
    ->select_expr('MAX(recharged_on)', 'last_recharge')
    ->where_gte('recharged_on', $thirtyDaysAgo)
    ->where_not_equal('method', 'Customer - Balance')
    ->where_not_equal('method', 'Recharge Balance - Administrator')
    ->group_by('username')
    ->order_by_desc('transaction_count')
    ->limit(5)
    ->find_array();

// Get customer details for each active user
foreach ($topActiveUsers as &$user) {
    $customer = ORM::for_table('tbl_customers')
        ->where('username', $user['username'])
        ->find_one();
    if ($customer) {
        $user['fullname'] = $customer->fullname;
        $user['phonenumber'] = $customer->phonenumber;
    } else {
        $user['fullname'] = '';
        $user['phonenumber'] = '';
    }
}

// Assign the monthly sales data to Smarty
$ui->assign('start_date', $start_date);
$ui->assign('current_date', $current_date);
$ui->assign('monthlySales', $monthlySales);
$ui->assign('weeklySales', $weeklySales);
$ui->assign('lastTransactions', $lastTransactions);
$ui->assign('topActiveUsers', $topActiveUsers);
$ui->assign('xfooter', '');
$ui->assign('monthlyRegistered', $monthlyRegistered);
$ui->assign('stocks', $stocks);
$ui->assign('plans', $plans);
$ui->assign('first_day_this_month', date('M Y'));
$ui->assign('first_day_last_month', date('M Y', strtotime('first day of last month')));
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
        if ($router_id != 'all') {
            $router = ORM::for_table('tbl_routers')->find_one($router_id);
            if (!$router) {
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

            // Get all online PPPoE users across all routers — use router-sourced cache
            $_pppoe_cache_file = $CACHE_PATH . DIRECTORY_SEPARATOR . 'pppoe_online_count.json';
            if (file_exists($_pppoe_cache_file) && (time() - filemtime($_pppoe_cache_file)) < 600) {
                $_pppoe_cache = json_decode(file_get_contents($_pppoe_cache_file), true);
                $data['online_users'] = isset($_pppoe_cache['total']) ? (int)$_pppoe_cache['total'] : 0;
            } else {
                $data['online_users'] = ORM::for_table('tbl_user_recharges')
                    ->where('status', 'on')
                    ->where('type', 'PPPOE')
                    ->count();
            }

            // Get all online Hotspot users — use router-sourced cache
            $_hotspot_cache_file = $CACHE_PATH . DIRECTORY_SEPARATOR . 'hotspot_online_count.json';
            if (file_exists($_hotspot_cache_file) && (time() - filemtime($_hotspot_cache_file)) < 600) {
                $_hotspot_cache = json_decode(file_get_contents($_hotspot_cache_file), true);
                $data['hotspot_users'] = isset($_hotspot_cache['total']) ? (int)$_hotspot_cache['total'] : 0;
            } else {
                $data['hotspot_users'] = ORM::for_table('tbl_user_recharges')
                    ->where('status', 'on')
                    ->where('type', 'Hotspot')
                    ->count();
            }

            // Calculate total online users
            $data['total_online'] = $data['online_users'] + $data['hotspot_users'];

            // Total PPPoE subscribers in the system (on + off)
            $data['total_pppoe'] = ORM::for_table('tbl_user_recharges')
                ->where('type', 'PPPOE')
                ->count();

            // Total customers
            $data['total_customers'] = ORM::for_table('tbl_customers')->count();

            // Expired PPPoE
            $data['expired_pppoe'] = ORM::for_table('tbl_user_recharges')
                ->where('status', 'off')
                ->where('type', 'PPPOE')
                ->count();

            // Expired Hotspot
            $data['expired_hotspot'] = ORM::for_table('tbl_user_recharges')
                ->where('status', 'off')
                ->where('type', 'Hotspot')
                ->count();

            // Total expired
            $data['total_expired'] = $data['expired_pppoe'] + $data['expired_hotspot'];

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

            // Get online PPPoE users for this router — use router-sourced cache
            $_pppoe_cache_file = $CACHE_PATH . DIRECTORY_SEPARATOR . 'pppoe_online_count.json';
            if (file_exists($_pppoe_cache_file) && (time() - filemtime($_pppoe_cache_file)) < 600) {
                $_pppoe_cache = json_decode(file_get_contents($_pppoe_cache_file), true);
                $data['online_users'] = isset($_pppoe_cache['by_router'][$router_id]) ? (int)$_pppoe_cache['by_router'][$router_id] : 0;
            } else {
                $data['online_users'] = ORM::for_table('tbl_user_recharges')
                    ->where('routers', $router_name)
                    ->where('status', 'on')
                    ->where('type', 'PPPOE')
                    ->count();
            }

            // Get online Hotspot users for this router — use router-sourced cache
            $_hotspot_cache_file = $CACHE_PATH . DIRECTORY_SEPARATOR . 'hotspot_online_count.json';
            if (file_exists($_hotspot_cache_file) && (time() - filemtime($_hotspot_cache_file)) < 600) {
                $_hotspot_cache = json_decode(file_get_contents($_hotspot_cache_file), true);
                $data['hotspot_users'] = isset($_hotspot_cache['by_router'][$router_id]) ? (int)$_hotspot_cache['by_router'][$router_id] : 0;
            } else {
                $data['hotspot_users'] = ORM::for_table('tbl_user_recharges')
                    ->where('routers', $router_name)
                    ->where('status', 'on')
                    ->where('type', 'Hotspot')
                    ->count();
            }

            // Calculate total online users for this router
            $data['total_online'] = $data['online_users'] + $data['hotspot_users'];

            // Total PPPoE subscribers for this router (on + off)
            $data['total_pppoe'] = ORM::for_table('tbl_user_recharges')
                ->where('routers', $router_name)
                ->where('type', 'PPPOE')
                ->count();

            // Total customers for this router
            $data['total_customers'] = ORM::for_table('tbl_user_recharges')
                ->where('routers', $router_name)
                ->count();

            // Expired PPPoE for this router
            $data['expired_pppoe'] = ORM::for_table('tbl_user_recharges')
                ->where('routers', $router_name)
                ->where('status', 'off')
                ->where('type', 'PPPOE')
                ->count();

            // Expired Hotspot for this router
            $data['expired_hotspot'] = ORM::for_table('tbl_user_recharges')
                ->where('routers', $router_name)
                ->where('status', 'off')
                ->where('type', 'Hotspot')
                ->count();

            // Total expired for this router
            $data['total_expired'] = $data['expired_pppoe'] + $data['expired_hotspot'];
        }

        // Format the numbers
        $data['income_today'] = number_format((float) $data['income_today'], 0, $_c['dec_point'], $_c['thousands_sep']);
        $data['income_month'] = number_format((float) $data['income_month'], 0, $_c['dec_point'], $_c['thousands_sep']);

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

if (isset($routes[1]) && $routes[1] == 'online-counts') {
    header('Content-Type: application/json');
    $router_id = _post('router_id');
    $router_name = null;

    if ($router_id && $router_id != 'all') {
        $router = ORM::for_table('tbl_routers')->find_one($router_id);
        if ($router) {
            $router_name = $router->name;
        }
    }

    // PPPoE online — read from cache file (written by cron sync)
    $_pppoe_cache_file = $CACHE_PATH . DIRECTORY_SEPARATOR . 'pppoe_online_count.json';
    if (file_exists($_pppoe_cache_file) && (time() - filemtime($_pppoe_cache_file)) < 600) {
        $_pppoe_cache = json_decode(file_get_contents($_pppoe_cache_file), true);
        if ($router_name && isset($_pppoe_cache['by_router'][$router_id])) {
            $pppoe_online = (int)$_pppoe_cache['by_router'][$router_id];
        } else {
            $pppoe_online = isset($_pppoe_cache['total']) ? (int)$_pppoe_cache['total'] : 0;
        }
    } else {
        $q = ORM::for_table('tbl_user_recharges')->where('status', 'on')->where('type', 'PPPOE');
        if ($router_name) $q = $q->where('routers', $router_name);
        $pppoe_online = $q->count();
    }

    // Hotspot online — read from cache file (written by cron sync)
    $_hotspot_cache_file = $CACHE_PATH . DIRECTORY_SEPARATOR . 'hotspot_online_count.json';
    if (file_exists($_hotspot_cache_file) && (time() - filemtime($_hotspot_cache_file)) < 600) {
        $_hotspot_cache = json_decode(file_get_contents($_hotspot_cache_file), true);
        if ($router_name && isset($_hotspot_cache['by_router'][$router_id])) {
            $hotspot_online = (int)$_hotspot_cache['by_router'][$router_id];
        } else {
            $hotspot_online = isset($_hotspot_cache['total']) ? (int)$_hotspot_cache['total'] : 0;
        }
    } else {
        $q = ORM::for_table('tbl_user_recharges')->where('status', 'on')->where('type', 'Hotspot');
        if ($router_name) $q = $q->where('routers', $router_name);
        $hotspot_online = $q->count();
    }

    echo json_encode([
        'online_users'  => $pppoe_online,
        'hotspot_users' => $hotspot_online,
        'total_online'  => $pppoe_online + $hotspot_online,
    ]);
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
