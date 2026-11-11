<?php

register_menu("Peak Hours Report", true, "peak_hours_report", 'REPORTS', 'ion ion-ios-analytics', '', '', ['Admin', 'SuperAdmin']);

// This registers the AJAX data endpoint as its own callable route
// URL: plugin/peak_hours_report_data
function peak_hours_report_data()
{
    $period = isset($_GET['period']) ? (int)$_GET['period'] : 30;
    $type   = isset($_GET['type'])   ? $_GET['type']        : 'both';

    if ($period < 1 || $period > 365) $period = 30;

    $date_from = date('Y-m-d', strtotime("-{$period} days"));

    // --- Hourly payment & revenue ---
    $payments_by_hour = array_fill(0, 24, 0);
    $revenue_by_hour  = array_fill(0, 24, 0.0);

    if ($type === 'payments' || $type === 'both') {
        $db   = ORM::getDb();
        $stmt = $db->prepare(
            "SELECT HOUR(recharged_on) AS hr,
                    COUNT(*) AS cnt,
                    SUM(CAST(price AS DECIMAL(10,2))) AS total
             FROM tbl_transactions
             WHERE recharged_on >= ?
               AND method NOT IN ('Customer - Balance','Recharge Balance - Administrator')
             GROUP BY HOUR(recharged_on)"
        );
        $stmt->execute([$date_from]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $h = (int)$row['hr'];
            $payments_by_hour[$h] = (int)$row['cnt'];
            $revenue_by_hour[$h]  = round((float)$row['total'], 2);
        }
    }

    // --- Hourly network activity (session updates) ---
    $usage_by_hour = array_fill(0, 24, 0);

    if ($type === 'usage' || $type === 'both') {
        $db   = ORM::getDb();
        $stmt = $db->prepare(
            "SELECT HOUR(last_updated) AS hr, COUNT(*) AS cnt
             FROM tbl_customer_monthly_usage
             WHERE last_updated >= ?
             GROUP BY HOUR(last_updated)"
        );
        $stmt->execute([$date_from . ' 00:00:00']);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $usage_by_hour[(int)$row['hr']] = (int)$row['cnt'];
        }
    }

    // --- Day-of-week breakdown ---
    $days_label   = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $payments_dow = array_fill(0, 7, 0);
    $revenue_dow  = array_fill(0, 7, 0.0);

    $db   = ORM::getDb();
    $stmt = $db->prepare(
        "SELECT DAYOFWEEK(recharged_on) AS dow,
                COUNT(*) AS cnt,
                SUM(CAST(price AS DECIMAL(10,2))) AS total
         FROM tbl_transactions
         WHERE recharged_on >= ?
           AND method NOT IN ('Customer - Balance','Recharge Balance - Administrator')
         GROUP BY DAYOFWEEK(recharged_on)"
    );
    $stmt->execute([$date_from]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $idx = (int)$row['dow'] - 1; // DAYOFWEEK: 1=Sun
        $payments_dow[$idx] = (int)$row['cnt'];
        $revenue_dow[$idx]  = round((float)$row['total'], 2);
    }

    // --- Peak calculations ---
    $max_pay = max($payments_by_hour);
    $max_rev = max($revenue_by_hour);
    $max_use = max($usage_by_hour);
    $max_dow = max($payments_dow);

    $peak_pay_h = $max_pay > 0 ? array_search($max_pay, $payments_by_hour) : 0;
    $peak_rev_h = $max_rev > 0 ? array_search($max_rev, $revenue_by_hour)  : 0;
    $peak_use_h = $max_use > 0 ? array_search($max_use, $usage_by_hour)    : 0;
    $peak_dow_i = $max_dow > 0 ? array_search($max_dow, $payments_dow)     : 0;

    header('Content-Type: application/json');
    echo json_encode([
        'labels'           => array_map(function($h) { return sprintf('%02d:00', $h); }, range(0, 23)),
        'payments_by_hour' => array_values($payments_by_hour),
        'revenue_by_hour'  => array_values($revenue_by_hour),
        'usage_by_hour'    => array_values($usage_by_hour),
        'days_label'       => $days_label,
        'payments_dow'     => array_values($payments_dow),
        'revenue_dow'      => array_values($revenue_dow),
        'peak_payment_hour'=> sprintf('%02d:00 - %02d:00', $peak_pay_h, $peak_pay_h + 1),
        'peak_revenue_hour'=> sprintf('%02d:00 - %02d:00', $peak_rev_h, $peak_rev_h + 1),
        'peak_usage_hour'  => sprintf('%02d:00 - %02d:00', $peak_use_h, $peak_use_h + 1),
        'peak_day'         => $days_label[$peak_dow_i],
        'total_payments'   => array_sum($payments_by_hour),
        'total_revenue'    => array_sum($revenue_by_hour),
    ]);
    exit;
}

function peak_hours_report()
{
    global $ui, $routes;
    _admin();

    // Route: plugin/peak_hours_report/data → handled by peak_hours_report_data above
    // but if someone hits /data sub-route here, redirect to data function
    $action = isset($routes['2']) ? $routes['2'] : '';
    if ($action === 'data') {
        peak_hours_report_data();
        return;
    }

    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('_title', 'Peak Hours Traffic Report');
    $ui->assign('_system_menu', 'peak_hours_report');
    $ui->display('peak_hours_report.tpl');
}
