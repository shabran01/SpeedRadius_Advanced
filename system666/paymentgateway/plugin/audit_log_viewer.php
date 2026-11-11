<?php

/**
 * Audit Log Viewer Plugin for SpeedRadius
 * Enhanced log monitoring with filters, stats, exports, and cleanup
 */

register_menu("Audit Log Viewer", true, "audit_log_viewer", 'LOGS', 'ion ion-clipboard', "Enhanced", "indigo", ['Admin', 'SuperAdmin']);
register_hook('cronjob', 'audit_log_auto_cleanup_cron');

// Ensure settings exist
function audit_log_ensure_settings()
{
    $defaults = [
        'alv_auto_enabled'       => 'no',
        'alv_keep_days'          => '7',
        'alv_last_cleanup'       => '',
        'alv_last_cron_log_date' => '',
    ];
    foreach ($defaults as $key => $val) {
        $exists = ORM::for_table('tbl_appconfig')->where('setting', $key)->find_one();
        if (!$exists) {
            $c = ORM::for_table('tbl_appconfig')->create();
            $c->setting = $key;
            $c->value = $val;
            $c->save();
        }
    }
}

function audit_log_auto_cleanup_cron()
{
    // Read settings
    $cfg = [];
    $rows = ORM::for_table('tbl_appconfig')->find_many();
    foreach ($rows as $r) {
        $cfg[$r['setting']] = $r['value'];
    }

    $enabled = $cfg['alv_auto_enabled'] ?? 'no';
    if ($enabled !== 'yes') return;

    $keepDays = (int)($cfg['alv_keep_days'] ?? 7);
    if ($keepDays < 1) $keepDays = 7;

    $deleted = ORM::raw_execute(
        "DELETE FROM tbl_logs WHERE UNIX_TIMESTAMP(date) < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL ? DAY))",
        [$keepDays]
    );

    // Update last cleanup timestamp
    $setting = ORM::for_table('tbl_appconfig')->where('setting', 'alv_last_cleanup')->find_one();
    if ($setting) {
        $setting->value = date('Y-m-d H:i:s');
        $setting->save();
    } else {
        $c = ORM::for_table('tbl_appconfig')->create();
        $c->setting = 'alv_last_cleanup';
        $c->value = date('Y-m-d H:i:s');
        $c->save();
    }

    // Only log if something was actually deleted, and throttle: max once per day
    if ($deleted > 0) {
        $lastLog = ORM::for_table('tbl_appconfig')->where('setting', 'alv_last_cron_log_date')->find_one();
        $today = date('Y-m-d');
        $shouldLog = !$lastLog || $lastLog->value !== $today;

        if ($shouldLog) {
            _log("Auto-cleanup: Deleted $deleted logs older than $keepDays days", 'System');
            if ($lastLog) {
                $lastLog->value = $today;
                $lastLog->save();
            } else {
                $c = ORM::for_table('tbl_appconfig')->create();
                $c->setting = 'alv_last_cron_log_date';
                $c->value = $today;
                $c->save();
            }
        }
    }
}

function audit_log_viewer()
{
    global $ui, $routes;
    _admin();
    $ui->assign('_title', 'Audit Log Viewer');
    $ui->assign('_system_menu', 'audit_log_viewer');

    $admin = Admin::_info();
    $ui->assign('_admin', $admin);

    $action = $routes['2'] ?? 'list';

    // Ensure settings table has our keys
    audit_log_ensure_settings();

    // Handle auto-cleanup settings save
    if ($action === 'save-settings' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $enabled = $_POST['alv_auto_enabled'] ?? 'no';
        $keepDays = (int)($_POST['alv_keep_days'] ?? 90);
        if ($keepDays < 1) $keepDays = 90;

        foreach (['alv_auto_enabled' => $enabled, 'alv_keep_days' => $keepDays] as $key => $val) {
            $s = ORM::for_table('tbl_appconfig')->where('setting', $key)->find_one();
            if ($s) { $s->value = $val; $s->save(); }
        }
        _notify('Auto-cleanup settings saved.', 's');
        r2(U . 'plugin/audit_log_viewer');
    }

    // Load settings
    $cfg = [];
    $rows = ORM::for_table('tbl_appconfig')->find_many();
    foreach ($rows as $r) { $cfg[$r['setting']] = $r['value']; }
    $ui->assign('alv_auto_enabled', $cfg['alv_auto_enabled'] ?? 'no');
    $ui->assign('alv_keep_days', $cfg['alv_keep_days'] ?? '90');
    $ui->assign('alv_last_cleanup', $cfg['alv_last_cleanup'] ?? 'Never');

    // Handle CSV export
    if ($action === 'export-csv') {
        audit_log_export_csv();
        return;
    }

    // Handle cleanup
    if ($action === 'cleanup' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        audit_log_cleanup();
    }

    // Handle single log delete
    if ($action === 'delete' && isset($routes['3'])) {
        audit_log_delete((int)$routes['3']);
    }

    // --- Filters ---
    $typeFilter  = $_GET['type'] ?? 'all';
    $dateFrom    = $_GET['from'] ?? '';
    $dateTo      = $_GET['to'] ?? '';
    $search      = $_GET['q'] ?? '';
    $page        = max(1, (int)($_GET['page'] ?? 1));
    $perPage     = 30;

    // --- Stats ---
    $totalLogs = ORM::for_table('tbl_logs')->count();

    $typeStats = ORM::for_table('tbl_logs')
        ->select_expr('type', 'type')
        ->select_expr('COUNT(*)', 'cnt')
        ->group_by('type')
        ->order_by_desc('cnt')
        ->find_many();

    $todayCount = ORM::for_table('tbl_logs')
        ->where_raw("DATE(date) = CURDATE()")
        ->count();

    $errorCount = ORM::for_table('tbl_logs')
        ->where_raw("description LIKE '%error%' OR description LIKE '%fail%' OR description LIKE '%invalid%'")
        ->count();

    // --- Build Query ---
    $query = ORM::for_table('tbl_logs');

    if ($typeFilter !== 'all') {
        $query->where('type', $typeFilter);
    }
    if (!empty($dateFrom)) {
        $query->where_gte('date', $dateFrom . ' 00:00:00');
    }
    if (!empty($dateTo)) {
        $query->where_lte('date', $dateTo . ' 23:59:59');
    }
    if (!empty($search)) {
        $query->where_raw('(description LIKE ? OR ip LIKE ? OR type LIKE ? OR userid LIKE ?)',
            ["%$search%", "%$search%", "%$search%", "%$search%"]);
    }

    $totalFiltered = $query->count();
    $totalPages = ceil($totalFiltered / $perPage);
    $offset = ($page - 1) * $perPage;

    $logs = $query->order_by_desc('id')->offset($offset)->limit($perPage)->find_many();

    // Type dropdown list
    $types = ORM::for_table('tbl_logs')
        ->select_expr('DISTINCT type')
        ->order_by_asc('type')
        ->find_many();

    $ui->assign('typeStats', $typeStats);
    $ui->assign('totalLogs', $totalLogs);
    $ui->assign('todayCount', $todayCount);
    $ui->assign('errorCount', $errorCount);
    $ui->assign('totalFiltered', $totalFiltered);
    $ui->assign('totalPages', $totalPages);
    $ui->assign('currentPage', $page);
    $ui->assign('perPage', $perPage);
    $ui->assign('logs', $logs);
    $ui->assign('types', $types);
    $ui->assign('typeFilter', $typeFilter);
    $ui->assign('dateFrom', $dateFrom);
    $ui->assign('dateTo', $dateTo);
    $ui->assign('search', $search);

    $ui->assign('xheader', '
    <style>
        .alv-card { background:#fff; border-radius:14px; box-shadow:0 1px 8px rgba(0,0,0,.05); padding:18px 20px; }
        .alv-stat { text-align:center; padding:12px; border-radius:10px; background:#f8fafc; }
        .alv-stat-lbl { font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#94a3b8; }
        .alv-stat-val { font-size:24px; font-weight:800; color:#1e293b; line-height:1.1; }
        .alv-stat-sub { font-size:10px; color:#64748b; margin-top:2px; }
        .alv-badge { display:inline-block; padding:3px 8px; border-radius:5px; font-size:10px; font-weight:600; }
        .alv-row:hover { background:#f8fafc !important; }
    </style>
    ');

    $ui->display('audit_log_viewer.tpl');
}

function audit_log_export_csv()
{
    $typeFilter = $_GET['type'] ?? 'all';
    $dateFrom   = $_GET['from'] ?? '';
    $dateTo     = $_GET['to'] ?? '';
    $search     = $_GET['q'] ?? '';

    $query = ORM::for_table('tbl_logs');
    if ($typeFilter !== 'all') $query->where('type', $typeFilter);
    if (!empty($dateFrom)) $query->where_gte('date', $dateFrom . ' 00:00:00');
    if (!empty($dateTo)) $query->where_lte('date', $dateTo . ' 23:59:59');
    if (!empty($search)) $query->where_raw('(description LIKE ? OR ip LIKE ?)', ["%$search%", "%$search%"]);

    $logs = $query->order_by_asc('id')->find_array();
    set_time_limit(-1);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment;filename="audit-logs-' . date('Y-m-d_His') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'Date', 'Type', 'Description', 'User ID', 'IP']);
    foreach ($logs as $log) {
        fputcsv($out, [$log['id'], $log['date'], $log['type'], $log['description'], $log['userid'], $log['ip']]);
    }
    fclose($out);
    exit;
}

function audit_log_cleanup()
{
    $keepDays = $_POST['keep_days'] ?? '30';
    $type = $_POST['clean_type'] ?? '';

    if ($keepDays === 'all') {
        // Delete ALL logs
        $query = "DELETE FROM tbl_logs";
        $params = [];
        if (!empty($type) && $type !== 'all') {
            $query .= " WHERE type = ?";
            $params[] = $type;
        }
        ORM::raw_execute($query, $params);
        _notify('All logs deleted' . ($type && $type !== 'all' ? " of type '$type'" : '') . '.', 's');
    } else {
        $days = (int)$keepDays;
        if ($days < 1) $days = 30;

        $query = "DELETE FROM tbl_logs WHERE UNIX_TIMESTAMP(date) < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL ? DAY))";
        $params = [$days];

        if (!empty($type) && $type !== 'all') {
            $query .= " AND type = ?";
            $params[] = $type;
        }

        ORM::raw_execute($query, $params);
        _notify("Deleted logs older than $days days" . ($type && $type !== 'all' ? " of type '$type'" : '') . '.', 's');
    }

    r2(U . 'plugin/audit_log_viewer');
}

function audit_log_delete($id)
{
    $log = ORM::for_table('tbl_logs')->find_one($id);
    if ($log) {
        $log->delete();
        _notify('Log entry #' . $id . ' deleted.', 's');
    }
    r2(U . 'plugin/audit_log_viewer');
}
