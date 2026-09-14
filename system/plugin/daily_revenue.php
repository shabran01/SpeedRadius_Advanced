<?php

/**
 * Daily Revenue Summary Plugin for SpeedRadius
 * 
 * Automatically sends a daily revenue summary via SMS & WhatsApp
 * at a configured time. Includes breakdown by payment method,
 * transaction count, top plans, and M-Pesa totals.
 * 
 * @package SpeedRadius
 * @subpackage Plugins
 */

// --- Menu & Hook Registration ---
register_menu("Daily Revenue", true, "daily_revenue", 'REPORTS', 'fa fa-money', '', '', ['Admin', 'SuperAdmin']);
register_hook('cronjob', 'daily_revenue_cron');

// --- Settings defaults ---
function daily_revenue_get_settings()
{
    $config_rows = ORM::for_table('tbl_appconfig')->find_many();
    $conf = [];
    foreach ($config_rows as $c) {
        $conf[$c['setting']] = $c['value'];
    }

    return [
        'enabled'       => $conf['dr_enabled'] ?? 'yes',
        'send_time'     => $conf['dr_send_time'] ?? '21:00',
        'recipients'    => $conf['dr_recipients'] ?? '',
        'include_mpesa' => $conf['dr_include_mpesa'] ?? 'yes',
        'include_plans' => $conf['dr_include_plans'] ?? 'yes',
        'include_count' => $conf['dr_include_count'] ?? 'yes',
        'last_sent'     => $conf['dr_last_sent'] ?? '',
        'tz_offset'     => $conf['dr_tz_offset'] ?? '+03:00',
    ];
}

// --- Save a setting ---
function daily_revenue_save_setting($key, $value)
{
    $d = ORM::for_table('tbl_appconfig')->where('setting', $key)->find_one();
    if ($d) {
        $d->value = $value;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = $key;
        $d->value = $value;
        $d->save();
    }
}

// --- Build the daily summary message ---
function daily_revenue_build_message($date = null)
{
    if (!$date) {
        $date = date('Y-m-d');
    }
    
    $settings = daily_revenue_get_settings();

    // Total revenue from tbl_transactions (exclude internal transfers)
    $totalRow = ORM::for_table('tbl_transactions')
        ->select_expr('SUM(CAST(price AS DECIMAL(10,2)))', 'total')
        ->select_expr('COUNT(*)', 'txn_count')
        ->where('recharged_on', $date)
        ->where_not_equal('method', 'Customer - Balance')
        ->where_not_equal('method', 'Recharge Balance - Administrator')
        ->find_one();

    $totalRevenue = $totalRow ? (float)$totalRow->total : 0;
    $txnCount     = $totalRow ? (int)$totalRow->txn_count : 0;

    // Payment method breakdown
    $methods = ORM::for_table('tbl_transactions')
        ->select('method')
        ->select_expr('SUM(CAST(price AS DECIMAL(10,2)))', 'total')
        ->select_expr('COUNT(*)', 'cnt')
        ->where('recharged_on', $date)
        ->where_not_equal('method', 'Customer - Balance')
        ->where_not_equal('method', 'Recharge Balance - Administrator')
        ->group_by('method')
        ->order_by_desc('total')
        ->find_many();

    // Top plans
    $topPlans = [];
    if ($settings['include_plans'] === 'yes') {
        $plans = ORM::for_table('tbl_transactions')
            ->select('plan_name')
            ->select_expr('SUM(CAST(price AS DECIMAL(10,2)))', 'total')
            ->select_expr('COUNT(*)', 'cnt')
            ->where('recharged_on', $date)
            ->where_not_equal('method', 'Customer - Balance')
            ->where_not_equal('method', 'Recharge Balance - Administrator')
            ->group_by('plan_name')
            ->order_by_desc('total')
            ->limit(5)
            ->find_many();
        foreach ($plans as $p) {
            $topPlans[] = $p;
        }
    }

    // M-Pesa totals
    $mpesaTotal = 0;
    $mpesaCount = 0;
    if ($settings['include_mpesa'] === 'yes') {
        $mpesaStart = date('YmdHis', strtotime($date . ' 00:00:00'));
        $mpesaEnd   = date('YmdHis', strtotime($date . ' 23:59:59'));
        
        $mpesaRow = ORM::for_table('tbl_mpesa_transactions')
            ->select_expr('SUM(CAST(TransAmount AS DECIMAL(10,2)))', 'total')
            ->select_expr('COUNT(*)', 'cnt')
            ->where_gte('TransTime', $mpesaStart)
            ->where_lte('TransTime', $mpesaEnd)
            ->find_one();
        
        $mpesaTotal = $mpesaRow ? (float)$mpesaRow->total : 0;
        $mpesaCount = $mpesaRow ? (int)$mpesaRow->cnt : 0;
    }

    // Build message
    $formattedDate = date('d M Y', strtotime($date));
    $dayName = date('l', strtotime($date));
    
    $msg = "💰 *Daily Revenue Summary*\n";
    $msg .= "📅 {$dayName}, {$formattedDate}\n";
    $msg .= "━━━━━━━━━━━━━━━\n\n";
    
    $msg .= "💵 *Total Revenue: Ksh " . number_format($totalRevenue, 2) . "*\n";
    
    if ($settings['include_count'] === 'yes') {
        $msg .= "📊 Transactions: {$txnCount}\n";
        if ($txnCount > 0) {
            $msg .= "💳 Avg. per txn: Ksh " . number_format($totalRevenue / $txnCount, 2) . "\n";
        }
    }
    
    // Payment methods
    if (count($methods) > 0) {
        $msg .= "\n📌 *By Payment Method:*\n";
        foreach ($methods as $m) {
            $methodName = str_replace(['Paystack', 'MpesatillStk', 'mpesa', 'PayHero', 'PesaPal', 'BankStkPush'], 
                                      ['Paystack', 'M-Pesa Till', 'M-Pesa', 'PayHero', 'PesaPal', 'Bank'], 
                                      $m['method']);
            $methodName = preg_replace('/ - .*$/', '', $methodName);
            $msg .= "  • {$methodName}: Ksh " . number_format((float)$m['total'], 2) . " ({$m['cnt']} txns)\n";
        }
    }
    
    // Top plans
    if (count($topPlans) > 0) {
        $msg .= "\n🏆 *Top Plans:*\n";
        foreach ($topPlans as $i => $p) {
            $num = $i + 1;
            $msg .= "  {$num}. {$p['plan_name']}: Ksh " . number_format((float)$p['total'], 2) . " ({$p['cnt']})\n";
        }
    }
    
    // M-Pesa
    if ($settings['include_mpesa'] === 'yes' && $mpesaCount > 0) {
        $msg .= "\n📱 *M-Pesa:* Ksh " . number_format($mpesaTotal, 2) . " ({$mpesaCount} payments)\n";
    }
    
    $msg .= "\n━━━━━━━━━━━━━━━\n";
    $msg .= "🕐 Generated at " . date('H:i') . "\n";
    $msg .= "SpeedRadius ISP Billing";
    
    return $msg;
}

// --- Cron job ---
function daily_revenue_cron()
{
    $settings = daily_revenue_get_settings();
    
    if ($settings['enabled'] !== 'yes') {
        return;
    }
    
    $sendTime = $settings['send_time'];
    $tzOffset = $settings['tz_offset'];
    
    // Calculate current time with timezone offset
    $now = new DateTime('now');
    $tz = new DateTimeZone($tzOffset);
    $now->setTimezone($tz);
    $currentTime = $now->format('H:i');
    $currentDate = $now->format('Y-m-d');
    
    // Check if it's time to send
    if ($currentTime < $sendTime) {
        return;
    }
    
    // Check if already sent today
    $lastSent = $settings['last_sent'] ?? '';
    if ($lastSent === $currentDate) {
        return;
    }
    
    // Allow a 5-minute window (cron might run at :00, send_time might be :03)
    $sendHourMin = explode(':', $sendTime);
    $sendMinutes = (int)$sendHourMin[0] * 60 + (int)$sendHourMin[1];
    $currMinutes = (int)$now->format('H') * 60 + (int)$now->format('i');
    
    if ($currMinutes < $sendMinutes || $currMinutes > $sendMinutes + 10) {
        return;
    }
    
    // Build and send
    $message = daily_revenue_build_message($currentDate);
    daily_revenue_send($message);
    
    // Mark as sent
    daily_revenue_save_setting('dr_last_sent', $currentDate);
    echo "[Daily Revenue] Summary sent for {$currentDate}\n";
}

// --- Send the message ---
function daily_revenue_send($message)
{
    $settings = daily_revenue_get_settings();
    $recipients_str = $settings['recipients'];
    
    if (empty($recipients_str)) {
        echo "[Daily Revenue] No recipients configured. Skipping send.\n";
        return;
    }
    
    $recipients = array_filter(array_map('trim', explode(',', $recipients_str)));
    
    // Also send to Telegram
    Message::sendTelegram($message);
    
    foreach ($recipients as $phone) {
        if (!empty($phone)) {
            Message::sendSMS($phone, $message);
            Message::sendWhatsapp($phone, $message);
        }
    }
    
    // Log
    _log("Daily revenue summary sent to " . count($recipients) . " recipients", 'DailyRevenue', 0);
}

// --- Main plugin router ---
function daily_revenue()
{
    global $ui, $config, $routes, $admin;
    _admin();

    $action = $routes['2'] ?? 'preview';

    switch ($action) {
        case 'preview':
            daily_revenue_preview();
            return;
        case 'send_now':
            daily_revenue_send_now();
            return;
        case 'settings':
            daily_revenue_settings_page();
            return;
        case 'save_settings':
            daily_revenue_save_settings();
            return;
        case 'history':
            daily_revenue_history();
            return;
        default:
            daily_revenue_preview();
            return;
    }
}

// --- Preview page ---
function daily_revenue_preview()
{
    global $ui, $admin;
    
    $date = _get('date', date('Y-m-d'));
    $message = daily_revenue_build_message($date);
    $settings = daily_revenue_get_settings();
    
    $ui->assign('message', $message);
    $ui->assign('date', $date);
    $ui->assign('settings', $settings);
    $ui->assign('_title', 'Daily Revenue Summary');
    $ui->assign('_system_menu', 'daily_revenue');
    $ui->assign('_admin', $admin);
    
    $ui->display('daily_revenue.tpl');
}

// --- Manual send now ---
function daily_revenue_send_now()
{
    $date = _req('date', date('Y-m-d'));
    $message = daily_revenue_build_message($date);
    daily_revenue_send($message);
    
    // Also mark today as sent if sending today's report
    if ($date === date('Y-m-d')) {
        daily_revenue_save_setting('dr_last_sent', $date);
    }
    
    r2(U . 'plugin/daily_revenue', 's', 'Daily revenue summary sent successfully via SMS, WhatsApp & Telegram!');
}

// --- Settings page ---
function daily_revenue_settings_page()
{
    global $ui, $admin;
    
    $settings = daily_revenue_get_settings();
    
    $ui->assign('settings', $settings);
    $ui->assign('_title', 'Daily Revenue - Settings');
    $ui->assign('_system_menu', 'daily_revenue');
    $ui->assign('_admin', $admin);
    
    $ui->display('daily_revenue_settings.tpl');
}

// --- Save settings ---
function daily_revenue_save_settings()
{
    daily_revenue_save_setting('dr_enabled', _post('dr_enabled', 'yes'));
    daily_revenue_save_setting('dr_send_time', _post('dr_send_time', '21:00'));
    daily_revenue_save_setting('dr_recipients', _post('dr_recipients', ''));
    daily_revenue_save_setting('dr_include_mpesa', _post('dr_include_mpesa', 'yes'));
    daily_revenue_save_setting('dr_include_plans', _post('dr_include_plans', 'yes'));
    daily_revenue_save_setting('dr_include_count', _post('dr_include_count', 'yes'));
    daily_revenue_save_setting('dr_tz_offset', _post('dr_tz_offset', '+03:00'));
    
    r2(U . 'plugin/daily_revenue/settings', 's', 'Settings saved successfully!');
}

// --- History page ---
function daily_revenue_history()
{
    global $ui, $admin;
    
    $history = [];
    
    // Get last 30 days of daily totals
    $dailyTotals = ORM::for_table('tbl_transactions')
        ->select_expr('recharged_on', 'date')
        ->select_expr('SUM(CAST(price AS DECIMAL(10,2)))', 'total')
        ->select_expr('COUNT(*)', 'txn_count')
        ->where_gte('recharged_on', date('Y-m-d', strtotime('-30 days')))
        ->where_not_equal('method', 'Customer - Balance')
        ->where_not_equal('method', 'Recharge Balance - Administrator')
        ->group_by('date')
        ->order_by_desc('date')
        ->find_many();
    
    foreach ($dailyTotals as $day) {
        $history[] = [
            'date'    => $day->date,
            'total'   => (float)$day->total,
            'txns'    => (int)$day->txn_count,
            'avg'     => $day->txn_count > 0 ? round($day->total / $day->txn_count, 2) : 0,
        ];
    }
    
    // Best day
    $bestDay = ORM::for_table('tbl_transactions')
        ->select_expr('recharged_on', 'date')
        ->select_expr('SUM(CAST(price AS DECIMAL(10,2)))', 'total')
        ->select_expr('COUNT(*)', 'txn_count')
        ->where_not_equal('method', 'Customer - Balance')
        ->where_not_equal('method', 'Recharge Balance - Administrator')
        ->group_by('date')
        ->order_by_desc('total')
        ->limit(1)
        ->find_one();
    
    $thisMonthTotal = 0;
    $thisMonthTxns  = 0;
    $thisMonth = date('Y-m-01');
    foreach ($history as $h) {
        if ($h['date'] >= $thisMonth) {
            $thisMonthTotal += $h['total'];
            $thisMonthTxns  += $h['txns'];
        }
    }
    $daysInMonth = (int)date('d');
    $dailyAvg = $daysInMonth > 0 ? round($thisMonthTotal / $daysInMonth, 2) : 0;
    
    $ui->assign('history', $history);
    $ui->assign('bestDay', $bestDay ? ['date' => $bestDay->date, 'total' => (float)$bestDay->total, 'txns' => (int)$bestDay->txn_count] : null);
    $ui->assign('thisMonthTotal', $thisMonthTotal);
    $ui->assign('thisMonthTxns', $thisMonthTxns);
    $ui->assign('dailyAvg', $dailyAvg);
    $ui->assign('_title', 'Daily Revenue - History');
    $ui->assign('_system_menu', 'daily_revenue');
    $ui->assign('_admin', $admin);
    
    $ui->display('daily_revenue_history.tpl');
}
