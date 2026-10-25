<?php

/**
 * Overdue Alert Plugin
 * Show customers whose subscription expires in the next N days.
 *
 * The source of truth is tbl_user_recharges (the active subscription), not the
 * transaction log: there is one row per customer/plan, it carries the real
 * expiry time in `time`, and only rows with status 'on' are active. Expiration
 * is stored as a DATE, so expiration = today is still a valid (active)
 * customer until `time` — it must be included in the window.
 */

register_menu("Overdue Next 5 Days", true, "overdue_alert", "CUSTOMERS", "ion ion-clock", "", "warning", ["Admin", "SuperAdmin"]);

function overdue_alert()
{
    global $ui, $routes, $config;

    _admin();

    // Handle plugin/overdue_alert/view/{username}
    if (isset($routes[2]) && $routes[2] === 'view' && isset($routes[3])) {
        $customer = ORM::for_table('tbl_customers')
            ->select('id')
            ->where('username', $routes[3])
            ->find_one();
        if ($customer) {
            r2(U . 'customers/view/' . $customer['id']);
        }
        r2(U . 'plugin/overdue_alert', 'e', Lang::T('Customer not found'));
    }

    // Window in days: ?days=N, then tbl_appconfig overdue_alert_days, then 5.
    $defaultDays = !empty($config['overdue_alert_days']) ? (int) $config['overdue_alert_days'] : 5;
    $days = (int) _get('days', $defaultDays);
    if ($days < 1 || $days > 90) {
        $days = 5;
    }

    $today = date('Y-m-d');
    // Anchor on midnight so a DST transition can't shift the window by a day.
    $until = date('Y-m-d', strtotime("{$today} +{$days} days"));

    $records = ORM::for_table('tbl_user_recharges')
        ->select('tbl_user_recharges.id', 'recharge_id')
        ->select('tbl_user_recharges.username', 'username')
        ->select('tbl_user_recharges.namebp', 'plan_name')
        ->select('tbl_user_recharges.expiration', 'expiration')
        ->select('tbl_user_recharges.time', 'time')
        ->select('tbl_user_recharges.type', 'type')
        ->select('tbl_user_recharges.routers', 'router')
        ->select('tbl_customers.id', 'customer_id')
        ->select('tbl_customers.fullname', 'fullname')
        ->select('tbl_customers.phonenumber', 'phonenumber')
        ->select('tbl_customers.email', 'email')
        ->join('tbl_customers', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
        ->where('tbl_user_recharges.status', 'on')
        ->where('tbl_customers.status', 'Active')
        ->where_gte('tbl_user_recharges.expiration', $today)
        ->where_lte('tbl_user_recharges.expiration', $until)
        ->order_by_asc('tbl_user_recharges.expiration')
        ->order_by_asc('tbl_user_recharges.time')
        ->find_many();

    $now = time();
    $d = array();
    $stats = array('total' => 0, 'critical' => 0, 'warning' => 0, 'caution' => 0);

    foreach ($records as $record) {
        $row = $record->as_array();

        $expiryTime = !empty($row['time']) ? $row['time'] : '23:59:59';
        $expiresTs = strtotime($row['expiration'] . ' ' . $expiryTime);
        if ($expiresTs === false) {
            // Be tolerant of installs that store expiration as DATETIME.
            $expiresTs = strtotime($row['expiration']);
        }
        if ($expiresTs === false) {
            continue;
        }

        $secondsLeft = $expiresTs - $now;
        // If cron has not flipped the status yet, an already-expired row is
        // still not "expiring soon" — skip it.
        if ($secondsLeft <= 0) {
            continue;
        }

        $daysLeft = (int) floor($secondsLeft / 86400);
        if ($daysLeft >= 1) {
            $daysLeftText = $daysLeft . ' ' . ($daysLeft == 1 ? 'day' : 'days') . ' left';
        } else {
            $hoursLeft = max(1, (int) floor($secondsLeft / 3600));
            $daysLeftText = $hoursLeft . 'h left';
        }

        if ($daysLeft <= 2) {
            $severity = 'critical';
        } elseif ($daysLeft <= 4) {
            $severity = 'warning';
        } else {
            $severity = 'caution';
        }

        $type = strtoupper((string) $row['type']);
        $row['service_type'] = $type === 'PPPOE' ? 'PPPoE' : ($type === 'HOTSPOT' ? 'Hotspot' : ucfirst(strtolower($type)));
        $row['expiry_time_display'] = substr($expiryTime, 0, 5);
        $row['days_left'] = $daysLeft;
        $row['seconds_left'] = $secondsLeft;
        // Fixed-width key so DataTables' (1.10.x) string sort is chronological.
        $row['seconds_key'] = sprintf('%012d', $secondsLeft);
        $row['days_left_text'] = $daysLeftText;
        $row['severity'] = $severity;

        $d[] = $row;
        $stats['total']++;
        $stats[$severity]++;
    }

    // Window selector options; keep a custom configured value visible.
    $dayOptions = array_values(array_unique(array_merge(array(1, 3, 5, 7, 14, 30), array($days))));
    sort($dayOptions);

    $ui->assign('_title', Lang::T('Overdue Alert'));
    $ui->assign('_system_menu', 'customers');
    $ui->assign('d', $d);
    $ui->assign('stats', $stats);
    $ui->assign('days', $days);
    $ui->assign('dayOptions', $dayOptions);
    $ui->assign('xheader', '<link rel="stylesheet" type="text/css" href="ui/lib/dt/jquery.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="ui/lib/c/overdue_alert.css">');
    $ui->assign('xfooter', '<script type="text/javascript" src="ui/lib/dt/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="ui/lib/c/overdue_alert.js"></script>');
    $ui->display('overdue_alert.tpl');
}
