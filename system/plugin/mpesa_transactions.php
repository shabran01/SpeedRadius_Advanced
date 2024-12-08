<?php
register_menu("Mpesa Transactions", true, "mpesa_transactions", 'AFTER_REPORTS', 'ion ion-ios-list', '', '', ['Admin', 'SuperAdmin']);

function mpesa_transactions_page($page = '') {
    mpesa_transactions();
}

function mpesa_transactions()
{
    global $ui, $config, $admin;
    _admin();

    // Get page from GET parameter
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($current_page < 1) $current_page = 1;
    
    $items_per_page = 10;
    $offset = ($current_page - 1) * $items_per_page;

    // Get total count for pagination
    $total_items = ORM::for_table('tbl_mpesa_transactions')->count();
    $total_pages = ceil($total_items / $items_per_page);

    // Ensure page doesn't exceed total pages
    if ($current_page > $total_pages && $total_pages > 0) $current_page = $total_pages;

    // Get paginated records
    $t = ORM::for_table('tbl_mpesa_transactions')
        ->order_by_desc('TransTime')
        ->offset($offset)
        ->limit($items_per_page)
        ->find_many();

    // Format the transaction times
    foreach($t as $transaction) {
        $time = $transaction->TransTime;
        $formatted_time = date('Y-m-d H:i:s', 
            strtotime(
                substr($time, 0, 4) . '-' . 
                substr($time, 4, 2) . '-' . 
                substr($time, 6, 2) . ' ' . 
                substr($time, 8, 2) . ':' . 
                substr($time, 10, 2) . ':' . 
                substr($time, 12, 2)
            )
        );
        $transaction->TransTime = $formatted_time;
    }

    $ui->assign('t', $t);
    $ui->assign('_title', 'Mpesa Transactions');
    $ui->assign('_system_menu', 'plugin/mpesa_transactions');
    $ui->assign('total_pages', $total_pages);
    $ui->assign('current_page', $current_page);
    $ui->assign('total_items', $total_items);
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('mpesa_transactions.tpl');
}
