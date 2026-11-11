<?php

// Orphan Recharges report: lists recharge records whose customer row no longer
// exists. Read-only - helps admins spot accounts destroyed by the old
// delete-duplicates bug so they can recreate the customer deliberately.

register_menu("Orphan Recharges", true, "orphan_recharges", 'CUSTOMERS', 'ion ion-alert', 'New', 'orange', ['Admin', 'SuperAdmin']);

function orphan_recharges()
{
    global $ui, $_L;

    $admin = Admin::_info();
    if (!$admin) {
        r2(U . 'admin', 'e', $_L['Please login first']);
    }

    $rows = [];
    $db = ORM::get_db();
    $stmt = $db->query(
        "SELECT r.username, COUNT(*) AS cnt, MAX(r.recharged_on) AS last_on,
                MAX(r.namebp) AS plan_name, MAX(r.routers) AS routers
         FROM tbl_user_recharges r
         LEFT JOIN tbl_customers c ON c.username = r.username
         WHERE c.id IS NULL
         GROUP BY r.username
         ORDER BY last_on DESC
         LIMIT 500"
    );
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $row;
    }

    $ui->assign('_title', 'Orphan Recharges');
    $ui->assign('_system_menu', 'customers');
    $ui->assign('rows', $rows);
    $ui->assign('total', count($rows));
    $ui->display('orphan_recharges.tpl');
}
