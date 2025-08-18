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
            r2(U . 'overdue_alert', 'e', 'Customer not found');
        }
    }

    $today = date('Y-m-d');
    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $five_days_later = date('Y-m-d', strtotime('+5 days'));

    $d = ORM::for_table('tbl_transactions')
        ->select('tbl_transactions.*')
        ->select('tbl_customers.id')
        ->select('tbl_customers.fullname')
        ->select('tbl_customers.phonenumber')
        ->select('tbl_customers.email')
        ->join('tbl_customers', array('tbl_transactions.username', '=', 'tbl_customers.username'))
        ->where_gte('expiration', $tomorrow) // Only show from tomorrow onwards
        ->where_lte('expiration', $five_days_later)
        ->where('tbl_customers.status', 'Active')
        ->order_by_desc('expiration')
        ->find_many();

    // Filter out any records with less than 1 day remaining
    $filtered_d = array();
    foreach ($d as $record) {
        $days_left = ceil((strtotime($record['expiration']) - time()) / 86400);
        if ($days_left >= 1) {
            $record['days_left'] = $days_left;
            $filtered_d[] = $record;
        }
    }

    $ui->assign('d', $filtered_d);
    $ui->assign('xheader', '<link rel="stylesheet" type="text/css" href="ui/lib/c/overdue_alert.css">');
    $ui->assign('xfooter', '<script type="text/javascript" src="ui/lib/c/overdue_alert.js"></script>');
    $ui->display('overdue_alert.tpl');
}