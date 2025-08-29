<?php

/**
 * Sales Audit Plugin for SpeedRadius
 * Provides comprehensive sales analysis and comparison reports
 */

// Register menu item in admin panel under Reports
register_menu("Sales Audit", true, "salesAudit", 'REPORTS', 'glyphicon glyphicon-stats', "New", "red", ['Admin', 'SuperAdmin']);


function salesAudit()
{
    global $ui, $config;
    _admin();
    
    $action = _req('action', 'dashboard');
    
    switch ($action) {
        case 'dashboard':
            salesAudit_dashboard();
            break;
        case 'comparison':
            salesAudit_comparison();
            break;
        case 'trends':
            salesAudit_trends();
            break;
        case 'api':
            salesAudit_api();
            break;
        default:
            salesAudit_dashboard();
            break;
    }
}

function salesAudit_dashboard()
{
    global $ui;
    
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $thisWeekStart = date('Y-m-d', strtotime('monday this week'));
    $lastWeekStart = date('Y-m-d', strtotime('monday last week'));
    $lastWeekEnd = date('Y-m-d', strtotime('sunday last week'));
    $thisMonthStart = date('Y-m-01');
    $lastMonthStart = date('Y-m-01', strtotime('first day of last month'));
    $lastMonthEnd = date('Y-m-t', strtotime('last day of last month'));
    
    // Get sales data
    $salesData = getSalesComparison();
    
    // Get top performing plans
    $topPlans = getTopPerformingPlans(10);
    
    // Get payment method breakdown
    $paymentMethods = getPaymentMethodBreakdown();
    
    // Get hourly sales for today
    $hourlySales = getHourlySales($today);
    
    $ui->assign('salesData', $salesData);
    $ui->assign('topPlans', $topPlans);
    $ui->assign('paymentMethods', $paymentMethods);
    $ui->assign('hourlySales', $hourlySales);
    $ui->assign('_title', 'Sales Audit Dashboard');
    $ui->assign('_system_menu', 'plugin/salesAudit');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('salesAudit.tpl');
}

function salesAudit_comparison()
{
    global $ui;
    
    $period = _req('period', 'today');
    $customFrom = _req('custom_from');
    $customTo = _req('custom_to');
    
    $comparisonData = getDetailedComparison($period, $customFrom, $customTo);
    
    $ui->assign('comparisonData', $comparisonData);
    $ui->assign('period', $period);
    $ui->assign('customFrom', $customFrom);
    $ui->assign('customTo', $customTo);
    $ui->assign('_title', 'Sales Comparison');
    $ui->assign('_system_menu', 'plugin/salesAudit');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('salesAuditComparison.tpl');
}

function salesAudit_trends()
{
    global $ui;
    
    $period = _req('period', '30days');
    $trendsData = getSalesTrends($period);
    
    $ui->assign('trendsData', $trendsData);
    $ui->assign('period', $period);
    $ui->assign('_title', 'Sales Trends');
    $ui->assign('_system_menu', 'plugin/salesAudit');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('salesAuditTrends.tpl');
}

function salesAudit_api()
{
    header('Content-Type: application/json');
    
    $endpoint = _req('endpoint');
    $period = _req('period', 'today');
    
    switch ($endpoint) {
        case 'comparison':
            echo json_encode(getSalesComparison());
            break;
        case 'trends':
            echo json_encode(getSalesTrends($period));
            break;
        case 'hourly':
            $date = _req('date', date('Y-m-d'));
            echo json_encode(getHourlySales($date));
            break;
        case 'payment-methods':
            echo json_encode(getPaymentMethodBreakdown());
            break;
        default:
            echo json_encode(['error' => 'Invalid endpoint']);
    }
    exit;
}

function getSalesComparison()
{
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    
    // Today vs Yesterday
    $todaySales = getTotalSales($today, $today);
    $yesterdaySales = getTotalSales($yesterday, $yesterday);
    $todayVsYesterday = calculatePercentageChange($yesterdaySales, $todaySales);
    
    // This week vs Last week (same day)
    $currentWeekday = date('w'); // 0 (Sunday) to 6 (Saturday)
    $thisWeekSameDay = $today;
    $lastWeekSameDay = date('Y-m-d', strtotime('-1 week', strtotime($today)));
    
    $thisWeekSameDaySales = getTotalSales($thisWeekSameDay, $thisWeekSameDay);
    $lastWeekSameDaySales = getTotalSales($lastWeekSameDay, $lastWeekSameDay);
    $weekComparison = calculatePercentageChange($lastWeekSameDaySales, $thisWeekSameDaySales);
    
    // This month vs Last month (same date)
    $thisMonthSameDate = $today;
    $lastMonthSameDate = date('Y-m-d', strtotime('-1 month', strtotime($today)));
    
    $thisMonthSameDateSales = getTotalSales($thisMonthSameDate, $thisMonthSameDate);
    $lastMonthSameDateSales = getTotalSales($lastMonthSameDate, $lastMonthSameDate);
    $monthComparison = calculatePercentageChange($lastMonthSameDateSales, $thisMonthSameDateSales);
    
    // Week to date comparison
    $thisWeekStart = date('Y-m-d', strtotime('monday this week'));
    $lastWeekStart = date('Y-m-d', strtotime('monday last week'));
    $lastWeekSameWeekday = date('Y-m-d', strtotime($lastWeekStart . ' +' . $currentWeekday . ' days'));
    
    $thisWeekToDate = getTotalSales($thisWeekStart, $today);
    $lastWeekToSameDay = getTotalSales($lastWeekStart, $lastWeekSameWeekday);
    $weekToDateComparison = calculatePercentageChange($lastWeekToSameDay, $thisWeekToDate);
    
    // Month to date comparison
    $thisMonthStart = date('Y-m-01');
    $lastMonthStart = date('Y-m-01', strtotime('first day of last month'));
    $lastMonthSameDayOfMonth = date('Y-m-d', strtotime($lastMonthStart . ' +' . (date('j') - 1) . ' days'));
    
    $thisMonthToDate = getTotalSales($thisMonthStart, $today);
    $lastMonthToSameDay = getTotalSales($lastMonthStart, $lastMonthSameDayOfMonth);
    $monthToDateComparison = calculatePercentageChange($lastMonthToSameDay, $thisMonthToDate);
    
    return [
        'todayVsYesterday' => [
            'today' => $todaySales,
            'yesterday' => $yesterdaySales,
            'change' => $todayVsYesterday,
            'transactions_today' => getTransactionCount($today, $today),
            'transactions_yesterday' => getTransactionCount($yesterday, $yesterday)
        ],
        'weekComparison' => [
            'this_week_same_day' => $thisWeekSameDaySales,
            'last_week_same_day' => $lastWeekSameDaySales,
            'change' => $weekComparison,
            'day_name' => date('l')
        ],
        'monthComparison' => [
            'this_month_same_date' => $thisMonthSameDateSales,
            'last_month_same_date' => $lastMonthSameDateSales,
            'change' => $monthComparison,
            'date' => date('F j')
        ],
        'weekToDate' => [
            'this_week' => $thisWeekToDate,
            'last_week' => $lastWeekToSameDay,
            'change' => $weekToDateComparison
        ],
        'monthToDate' => [
            'this_month' => $thisMonthToDate,
            'last_month' => $lastMonthToSameDay,
            'change' => $monthToDateComparison
        ]
    ];
}

function getTotalSales($startDate, $endDate)
{
    $result = ORM::for_table('tbl_transactions')
        ->select_expr('SUM(CAST(price AS DECIMAL(10,2)))', 'total')
        ->where_gte('recharged_on', $startDate)
        ->where_lte('recharged_on', $endDate)
        ->where_not_equal('method', 'Customer - Balance')
        ->where_not_equal('method', 'Recharge Balance - Administrator')
        ->find_one();
    
    return $result ? (float)$result->total : 0;
}

function getTransactionCount($startDate, $endDate)
{
    $result = ORM::for_table('tbl_transactions')
        ->select_expr('COUNT(*)', 'count')
        ->where_gte('recharged_on', $startDate)
        ->where_lte('recharged_on', $endDate)
        ->where_not_equal('method', 'Customer - Balance')
        ->where_not_equal('method', 'Recharge Balance - Administrator')
        ->find_one();
    
    return $result ? (int)$result->count : 0;
}

function calculatePercentageChange($oldValue, $newValue)
{
    if ($oldValue == 0) {
        return $newValue > 0 ? 100 : 0;
    }
    
    return round((($newValue - $oldValue) / $oldValue) * 100, 2);
}

function getTopPerformingPlans($limit = 10)
{
    $startDate = date('Y-m-01'); // This month
    $endDate = date('Y-m-d');
    
    $results = ORM::for_table('tbl_transactions')
        ->select('plan_name')
        ->select_expr('COUNT(*)', 'transaction_count')
        ->select_expr('SUM(CAST(price AS DECIMAL(10,2)))', 'total_revenue')
        ->where_gte('recharged_on', $startDate)
        ->where_lte('recharged_on', $endDate)
        ->where_not_equal('method', 'Customer - Balance')
        ->where_not_equal('method', 'Recharge Balance - Administrator')
        ->group_by('plan_name')
        ->order_by_desc('total_revenue')
        ->limit($limit)
        ->find_many();
    
    $plans = [];
    foreach ($results as $result) {
        $plans[] = [
            'plan_name' => $result->plan_name,
            'transaction_count' => (int)$result->transaction_count,
            'total_revenue' => (float)$result->total_revenue,
            'avg_price' => $result->transaction_count > 0 ? round($result->total_revenue / $result->transaction_count, 2) : 0
        ];
    }
    
    return $plans;
}

function getPaymentMethodBreakdown()
{
    $startDate = date('Y-m-01'); // This month
    $endDate = date('Y-m-d');
    
    $results = ORM::for_table('tbl_transactions')
        ->select_expr('SUBSTRING_INDEX(method, " - ", 1)', 'payment_method')
        ->select_expr('COUNT(*)', 'transaction_count')
        ->select_expr('SUM(CAST(price AS DECIMAL(10,2)))', 'total_revenue')
        ->where_gte('recharged_on', $startDate)
        ->where_lte('recharged_on', $endDate)
        ->where_not_equal('method', 'Customer - Balance')
        ->where_not_equal('method', 'Recharge Balance - Administrator')
        ->group_by_expr('SUBSTRING_INDEX(method, " - ", 1)')
        ->order_by_desc('total_revenue')
        ->find_many();
    
    $methods = [];
    $totalRevenue = 0;
    
    foreach ($results as $result) {
        $totalRevenue += (float)$result->total_revenue;
    }
    
    foreach ($results as $result) {
        $revenue = (float)$result->total_revenue;
        $percentage = $totalRevenue > 0 ? round(($revenue / $totalRevenue) * 100, 2) : 0;
        
        $methods[] = [
            'payment_method' => $result->payment_method,
            'transaction_count' => (int)$result->transaction_count,
            'total_revenue' => $revenue,
            'percentage' => $percentage
        ];
    }
    
    return $methods;
}

function getHourlySales($date)
{
    $hours = [];
    
    for ($hour = 0; $hour < 24; $hour++) {
        $hourStart = sprintf('%02d:00:00', $hour);
        $hourEnd = sprintf('%02d:59:59', $hour);
        
        $result = ORM::for_table('tbl_transactions')
            ->select_expr('SUM(CAST(price AS DECIMAL(10,2)))', 'total')
            ->select_expr('COUNT(*)', 'count')
            ->where('recharged_on', $date)
            ->where_gte('recharged_time', $hourStart)
            ->where_lte('recharged_time', $hourEnd)
            ->where_not_equal('method', 'Customer - Balance')
            ->where_not_equal('method', 'Recharge Balance - Administrator')
            ->find_one();
        
        $hours[] = [
            'hour' => $hour,
            'total' => $result ? (float)$result->total : 0,
            'count' => $result ? (int)$result->count : 0
        ];
    }
    
    return $hours;
}

function getDetailedComparison($period, $customFrom = null, $customTo = null)
{
    $dates = getComparisonDates($period, $customFrom, $customTo);
    
    $currentPeriodSales = getTotalSales($dates['current_start'], $dates['current_end']);
    $previousPeriodSales = getTotalSales($dates['previous_start'], $dates['previous_end']);
    
    $currentTransactions = getTransactionCount($dates['current_start'], $dates['current_end']);
    $previousTransactions = getTransactionCount($dates['previous_start'], $dates['previous_end']);
    
    $salesChange = calculatePercentageChange($previousPeriodSales, $currentPeriodSales);
    $transactionChange = calculatePercentageChange($previousTransactions, $currentTransactions);
    
    // Get daily breakdown
    $dailyBreakdown = getDailyBreakdown($dates['current_start'], $dates['current_end'], $dates['previous_start'], $dates['previous_end']);
    
    return [
        'period' => $period,
        'dates' => $dates,
        'current' => [
            'sales' => $currentPeriodSales,
            'transactions' => $currentTransactions,
            'avg_transaction' => $currentTransactions > 0 ? round($currentPeriodSales / $currentTransactions, 2) : 0
        ],
        'previous' => [
            'sales' => $previousPeriodSales,
            'transactions' => $previousTransactions,
            'avg_transaction' => $previousTransactions > 0 ? round($previousPeriodSales / $previousTransactions, 2) : 0
        ],
        'changes' => [
            'sales' => $salesChange,
            'transactions' => $transactionChange,
            'avg_transaction' => calculatePercentageChange(
                $previousTransactions > 0 ? $previousPeriodSales / $previousTransactions : 0,
                $currentTransactions > 0 ? $currentPeriodSales / $currentTransactions : 0
            )
        ],
        'daily_breakdown' => $dailyBreakdown
    ];
}

function getComparisonDates($period, $customFrom = null, $customTo = null)
{
    switch ($period) {
        case 'today':
            return [
                'current_start' => date('Y-m-d'),
                'current_end' => date('Y-m-d'),
                'previous_start' => date('Y-m-d', strtotime('-1 day')),
                'previous_end' => date('Y-m-d', strtotime('-1 day'))
            ];
            
        case 'this_week':
            $weekStart = date('Y-m-d', strtotime('monday this week'));
            $weekEnd = date('Y-m-d', strtotime('sunday this week'));
            $lastWeekStart = date('Y-m-d', strtotime('monday last week'));
            $lastWeekEnd = date('Y-m-d', strtotime('sunday last week'));
            
            return [
                'current_start' => $weekStart,
                'current_end' => $weekEnd,
                'previous_start' => $lastWeekStart,
                'previous_end' => $lastWeekEnd
            ];
            
        case 'this_month':
            $monthStart = date('Y-m-01');
            $monthEnd = date('Y-m-t');
            $lastMonthStart = date('Y-m-01', strtotime('first day of last month'));
            $lastMonthEnd = date('Y-m-t', strtotime('last day of last month'));
            
            return [
                'current_start' => $monthStart,
                'current_end' => $monthEnd,
                'previous_start' => $lastMonthStart,
                'previous_end' => $lastMonthEnd
            ];
            
        case 'custom':
            if ($customFrom && $customTo) {
                $days = (strtotime($customTo) - strtotime($customFrom)) / (60 * 60 * 24);
                $previousEnd = date('Y-m-d', strtotime($customFrom . ' -1 day'));
                $previousStart = date('Y-m-d', strtotime($previousEnd . ' -' . $days . ' days'));
                
                return [
                    'current_start' => $customFrom,
                    'current_end' => $customTo,
                    'previous_start' => $previousStart,
                    'previous_end' => $previousEnd
                ];
            }
            // fallthrough to default
            
        default:
            return getComparisonDates('today');
    }
}

function getDailyBreakdown($currentStart, $currentEnd, $previousStart, $previousEnd)
{
    $currentDays = [];
    $previousDays = [];
    
    // Get current period daily sales
    $current = $currentStart;
    while (strtotime($current) <= strtotime($currentEnd)) {
        $sales = getTotalSales($current, $current);
        $transactions = getTransactionCount($current, $current);
        
        $currentDays[] = [
            'date' => $current,
            'sales' => $sales,
            'transactions' => $transactions
        ];
        
        $current = date('Y-m-d', strtotime($current . ' +1 day'));
    }
    
    // Get previous period daily sales
    $previous = $previousStart;
    while (strtotime($previous) <= strtotime($previousEnd)) {
        $sales = getTotalSales($previous, $previous);
        $transactions = getTransactionCount($previous, $previous);
        
        $previousDays[] = [
            'date' => $previous,
            'sales' => $sales,
            'transactions' => $transactions
        ];
        
        $previous = date('Y-m-d', strtotime($previous . ' +1 day'));
    }
    
    return [
        'current' => $currentDays,
        'previous' => $previousDays
    ];
}

function getSalesTrends($period)
{
    $endDate = date('Y-m-d');
    
    switch ($period) {
        case '7days':
            $startDate = date('Y-m-d', strtotime('-6 days'));
            $groupBy = 'DATE(recharged_on)';
            break;
        case '30days':
            $startDate = date('Y-m-d', strtotime('-29 days'));
            $groupBy = 'DATE(recharged_on)';
            break;
        case '12months':
            $startDate = date('Y-m-d', strtotime('-11 months'));
            $groupBy = 'DATE_FORMAT(recharged_on, "%Y-%m")';
            break;
        default:
            $startDate = date('Y-m-d', strtotime('-29 days'));
            $groupBy = 'DATE(recharged_on)';
    }
    
    $results = ORM::for_table('tbl_transactions')
        ->select_expr($groupBy, 'date_group')
        ->select_expr('SUM(CAST(price AS DECIMAL(10,2)))', 'total_sales')
        ->select_expr('COUNT(*)', 'transaction_count')
        ->where_gte('recharged_on', $startDate)
        ->where_lte('recharged_on', $endDate)
        ->where_not_equal('method', 'Customer - Balance')
        ->where_not_equal('method', 'Recharge Balance - Administrator')
        ->group_by_expr($groupBy)
        ->order_by_asc('date_group')
        ->find_many();
    
    $trends = [];
    foreach ($results as $result) {
        $trends[] = [
            'date' => $result->date_group,
            'sales' => (float)$result->total_sales,
            'transactions' => (int)$result->transaction_count
        ];
    }
    
    return $trends;
}

?>
