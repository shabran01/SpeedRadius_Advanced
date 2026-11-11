<?php

/**
 * Overdue Alert Plugin
 * Show customers who will be overdue in next 5 days
 */

register_menu("Overdue Next 5 Days", true, "overdue_alert", "CUSTOMERS", "ion ion-clock", "", "warning", ["Admin", "SuperAdmin"]);

function overdue_alert()
{
    global $ui, $routes;
    _admin();
    $ui->assign('_title', 'Overdue Next 5 Days');
    $ui->assign('_system_menu', 'customers');

    if (isset($routes[2]) && $routes[2] === 'view' && isset($routes[3])) {
        $username = $routes[3];
        // Get customer ID from username
        $customer = ORM::for_table('tbl_customers')
            ->select('id')
            ->where('username', $username)
            ->find_one();
        if ($customer) {
            r2(U . 'customers/view/' . $customer['id']);
        } else {
            r2(U . 'plugin/overdue_alert', 'e', 'Customer not found');
        }
    }

    $today = date('Y-m-d');
    $five_days_later = date('Y-m-d', strtotime('+5 days'));

    // Active packages (tbl_user_recharges.status = 'on') expiring in the next 5 days.
    // We use tbl_user_recharges (current active packages), not tbl_transactions (payment
    // history), because transactions only store the expiry date of each past top-up and
    // therefore return stale/duplicate rows.
    $d = ORM::for_table('tbl_user_recharges')
        ->table_alias('ur')
        ->select('ur.username', 'username')
        ->select('ur.namebp', 'plan_name')
        ->select('ur.expiration', 'expiration')
        ->select('ur.type', 'type')
        ->select('c.id', 'id')
        ->select('c.fullname', 'fullname')
        ->select('c.phonenumber', 'phonenumber')
        ->select('c.email', 'email')
        ->select('c.service_type', 'service_type')
        ->join('tbl_customers', array('ur.customer_id', '=', 'c.id'), 'c')
        ->where('ur.status', 'on')
        ->where_gte('ur.expiration', $today)
        ->where_lte('ur.expiration', $five_days_later)
        ->where('c.status', 'Active')
        ->order_by_asc('ur.expiration')
        ->find_many();

    // One row per customer (keep the earliest expiring active package).
    // days_left: 0 = expiring today, 1 = tomorrow, ... 5 = in 5 days.
    $today_dt = new DateTime($today);
    $seen = array();
    $filtered_d = array();
    foreach ($d as $record) {
        $customer_id = $record->id;
        if (isset($seen[$customer_id])) {
            continue;
        }
        $seen[$customer_id] = true;

        $exp = new DateTime($record->expiration);
        $days_left = (int) $today_dt->diff($exp)->format('%r%a');
        if ($days_left < 0) {
            $days_left = 0;
        }

        $row = $record->as_array();
        $row['days_left'] = $days_left;
        $filtered_d[] = $row;
    }

    $ui->assign('d', $filtered_d);
    $ui->assign('xheader', '<link rel="stylesheet" type="text/css" href="ui/lib/c/overdue_alert.css">');
    $ui->assign('xfooter', '<script type="text/javascript" src="ui/lib/c/overdue_alert.js"></script>');
    $ui->display('overdue_alert.tpl');
}