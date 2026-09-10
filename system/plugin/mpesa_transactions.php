<?php
register_menu("Mpesa Transactions", true, "mpesa_transactions", 'AFTER_REPORTS', 'ion ion-ios-list', '', '', ['Admin', 'SuperAdmin']);

function mpesa_transactions_page($page = '') {
    mpesa_transactions();
}

function mpesa_transactions()
{
    global $ui, $config, $admin;
    _admin();

    // Get search parameters
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
    $date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
    $export = isset($_GET['export']) ? $_GET['export'] : '';

    // Get page from GET parameter
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($current_page < 1) $current_page = 1;
    
    $items_per_page = 10;
    $offset = ($current_page - 1) * $items_per_page;

    // Build the query
    $query = ORM::for_table('tbl_mpesa_transactions');
    
    if (!empty($search)) {
        $query->where_raw('(FirstName LIKE ? OR MSISDN LIKE ? OR TransID LIKE ? OR BillRefNumber LIKE ?)', 
            ["%$search%", "%$search%", "%$search%", "%$search%"]);
    }
    
    if (!empty($date_from)) {
        $from_date = date('YmdHis', strtotime($date_from . ' 00:00:00'));
        $query->where_gte('TransTime', $from_date);
    }
    
    if (!empty($date_to)) {
        $to_date = date('YmdHis', strtotime($date_to . ' 23:59:59'));
        $query->where_lte('TransTime', $to_date);
    }

    // Handle PDF export
    if ($export === 'pdf') {
        require_once 'mpesa_transactions_pdf.php';
        $transactions_orm = $query->order_by_desc('TransTime')->find_many();
        
        // Convert ORM objects to arrays and format transaction times for PDF
        $transactions = [];
        foreach($transactions_orm as $transaction) {
            $trans_array = $transaction->as_array();
            $time = $trans_array['TransTime'];
            $trans_array['TransTime'] = date('Y-m-d H:i:s', 
                strtotime(
                    substr($time, 0, 4) . '-' . 
                    substr($time, 4, 2) . '-' . 
                    substr($time, 6, 2) . ' ' . 
                    substr($time, 8, 2) . ':' . 
                    substr($time, 10, 2) . ':' . 
                    substr($time, 12, 2)
                )
            );
            $transactions[] = $trans_array;
        }
        
        generate_mpesa_transactions_pdf($transactions);
        exit;
    }

    // Get total count for pagination
    $total_items = $query->count();
    $total_pages = ceil($total_items / $items_per_page);

    // Ensure page doesn't exceed total pages
    if ($current_page > $total_pages && $total_pages > 0) $current_page = $total_pages;

    // Get paginated records
    $t = $query->order_by_desc('TransTime')
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

    $ui->assign('search', $search);
    $ui->assign('date_from', $date_from);
    $ui->assign('date_to', $date_to);
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
