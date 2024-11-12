<?php
register_menu("Mpesa Transactions", true, "mpesa_transactions", 'AFTER_REPORTS', 'ion ion-ios-list', '', '', ['Admin', 'SuperAdmin']);



function mpesa_transactions()
{
    global $ui, $config, $admin;
    _admin();
    $query = ORM::for_table('tbl_mpesa_transactions')
    ->order_by_asc('id')
    ->find_one();
    $t = $query->findMany();
    $ui->assign('t', $t);  
    $ui->assign('_title', 'Mpesa Transactions');
    $ui->assign('_system_menu', 'plugin/mpesa_transactions');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('mpesa_transactions.tpl');
}


