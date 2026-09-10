<?php

/**
 * Marketing Scheduler Plugin
 * Schedule bulk SMS/WhatsApp marketing/promotional messages
 * to be sent automatically at a specific date and time.
 */

// --- Menu & hook registration (MUST be at top, before any function calls) ----
register_menu("Marketing Scheduler", true, "marketing_scheduler", 'MESSAGE', 'fa fa-calendar');
register_hook('cronjob', 'marketing_scheduler_cron');

// --- Auto-create table -------------------------------------------------------
function marketing_scheduler_ensure_table()
{
    try {
        $db = ORM::getDb();
        $db->exec("CREATE TABLE IF NOT EXISTS `tbl_marketing_campaigns` (
            `id`               INT(11)      NOT NULL AUTO_INCREMENT,
            `title`            VARCHAR(255) NOT NULL,
            `group_filter`     VARCHAR(50)  NOT NULL DEFAULT 'all',
            `router_filter`    VARCHAR(100) NOT NULL DEFAULT '',
            `message`          TEXT         NOT NULL,
            `via`              VARCHAR(10)  NOT NULL DEFAULT 'sms',
            `batch_size`       INT(11)      NOT NULL DEFAULT 50,
            `delay_seconds`    INT(11)      NOT NULL DEFAULT 0,
            `scheduled_at`     DATETIME     NOT NULL,
            `status`           VARCHAR(20)  NOT NULL DEFAULT 'pending',
            `total_recipients` INT(11)      NOT NULL DEFAULT 0,
            `total_sent`       INT(11)      NOT NULL DEFAULT 0,
            `total_failed`     INT(11)      NOT NULL DEFAULT 0,
            `sent_at`          DATETIME     NULL,
            `created_by`       VARCHAR(100) NOT NULL DEFAULT '',
            `created_at`       DATETIME     NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    } catch (\Throwable $e) {
        // Silent fail — table may already exist
    }
}

// --- Cron hook function ------------------------------------------------------

function marketing_scheduler_cron()
{
    global $config;
    set_time_limit(0);

    $now       = date('Y-m-d H:i:s');
    $campaigns = ORM::for_table('tbl_marketing_campaigns')
        ->where('status', 'pending')
        ->where_lte('scheduled_at', $now)
        ->find_many();

    foreach ($campaigns as $campaign) {
        $campaign->status = 'running';
        $campaign->save();

        echo "[Marketing Scheduler] Processing: {$campaign->title}\n";

        try {
            $results = marketing_scheduler_process_campaign($campaign->as_array());

            $campaign->status           = 'sent';
            $campaign->total_sent       = $results['sent'];
            $campaign->total_failed     = $results['failed'];
            $campaign->total_recipients = $results['total'];
            $campaign->sent_at          = date('Y-m-d H:i:s');
            $campaign->save();

            echo "[Marketing Scheduler] Done — Sent: {$results['sent']}, Failed: {$results['failed']}\n";
        } catch (Exception $e) {
            $campaign->status = 'failed';
            $campaign->save();
            echo "[Marketing Scheduler] FAILED — " . $e->getMessage() . "\n";
        }
    }
}

// --- Process a campaign (used by cron and "Send Now") -----------------------
function marketing_scheduler_process_campaign($campaign)
{
    global $config;

    $group   = $campaign['group_filter'];
    $router  = $campaign['router_filter'];
    $message = $campaign['message'];
    $via     = $campaign['via'];
    $batch   = max(1, (int) $campaign['batch_size']);
    $delay   = max(0, (int) $campaign['delay_seconds']);

    $applyFilters = marketing_scheduler_build_filters($group, $router);

    // Count total using DISTINCT to avoid GROUP BY issue
    $countRow = $applyFilters(ORM::for_table('tbl_customers'))
        ->select_expr('COUNT(DISTINCT `tbl_customers`.`id`)', 'cnt')
        ->find_one();
    $total = ($countRow !== false && isset($countRow->cnt)) ? (int) $countRow->cnt : 0;

    $sent   = 0;
    $failed = 0;
    $offset = 0;

    while ($offset < $total) {
        $customers = $applyFilters(ORM::for_table('tbl_customers'))
            ->select('tbl_customers.*')
            ->group_by('tbl_customers.id')
            ->limit($batch)
            ->offset($offset)
            ->find_array();

        if (empty($customers)) break;

        foreach ($customers as $customer) {
            $msg = $message;
            $msg = str_replace('[[name]]',         $customer['fullname'],        $msg);
            $msg = str_replace('[[user_name]]',     $customer['username'],        $msg);
            $msg = str_replace('[[phone]]',         $customer['phonenumber'],     $msg);
            $msg = str_replace('[[company_name]]',  $config['CompanyName'] ?? '', $msg);

            $ok = false;
            if ($via === 'sms' || $via === 'both') {
                $smsOk = Message::sendSMS($customer['phonenumber'], $msg);
                $ok    = $ok || $smsOk;
            }
            if ($via === 'wa' || $via === 'both') {
                $waOk = Message::sendWhatsapp($customer['phonenumber'], $msg);
                $ok   = $ok || $waOk;
            }

            if ($ok) { $sent++; } else { $failed++; }
        }

        $offset += count($customers);

        if ($delay > 0 && $offset < $total) {
            sleep($delay);
        }
    }

    return ['sent' => $sent, 'failed' => $failed, 'total' => $total];
}

// --- Shared filter builder (mirrors message.php logic) -----------------------
function marketing_scheduler_build_filters($group, $router)
{
    return function ($q) use ($group, $router) {
        $expiredGroups = ['expired', 'expired_pppoe', 'expired_hotspot'];

        if (!empty($router)) {
            $q->join('tbl_user_recharges', ['tbl_customers.id', '=', 'tbl_user_recharges.customer_id'])
              ->where('tbl_user_recharges.routers', $router);
            if (!in_array($group, $expiredGroups)) {
                $q->where('tbl_user_recharges.status', 'on');
            }
        }

        switch ($group) {
            case 'new':
                $q->where_raw("DATE(tbl_customers.created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)");
                break;
            case 'expired':
                if (empty($router)) {
                    $q->join('tbl_user_recharges', ['tbl_customers.id', '=', 'tbl_user_recharges.customer_id'])
                      ->where('tbl_user_recharges.status', 'off');
                }
                break;
            case 'active':
                if (empty($router)) {
                    $q->join('tbl_user_recharges', ['tbl_customers.id', '=', 'tbl_user_recharges.customer_id'])
                      ->where('tbl_user_recharges.status', 'on');
                }
                break;
            case 'active_pppoe':
                if (empty($router)) {
                    $q->join('tbl_user_recharges', ['tbl_customers.id', '=', 'tbl_user_recharges.customer_id'])
                      ->where('tbl_user_recharges.status', 'on')
                      ->where('tbl_user_recharges.type', 'PPPOE');
                } else {
                    $q->where('tbl_user_recharges.type', 'PPPOE');
                }
                break;
            case 'expired_pppoe':
                if (empty($router)) {
                    $q->join('tbl_user_recharges', ['tbl_customers.id', '=', 'tbl_user_recharges.customer_id'])
                      ->where('tbl_user_recharges.status', 'off')
                      ->where('tbl_user_recharges.type', 'PPPOE');
                } else {
                    $q->where('tbl_user_recharges.status', 'off')
                      ->where('tbl_user_recharges.type', 'PPPOE');
                }
                break;
            case 'all_pppoe':
                if (empty($router)) {
                    $q->join('tbl_user_recharges', ['tbl_customers.id', '=', 'tbl_user_recharges.customer_id'])
                      ->where('tbl_user_recharges.type', 'PPPOE');
                } else {
                    $q->where('tbl_user_recharges.type', 'PPPOE');
                }
                break;
            case 'active_hotspot':
                if (empty($router)) {
                    $q->join('tbl_user_recharges', ['tbl_customers.id', '=', 'tbl_user_recharges.customer_id'])
                      ->where('tbl_user_recharges.status', 'on')
                      ->where('tbl_user_recharges.type', 'Hotspot');
                } else {
                    $q->where('tbl_user_recharges.type', 'Hotspot');
                }
                break;
            case 'expired_hotspot':
                if (empty($router)) {
                    $q->join('tbl_user_recharges', ['tbl_customers.id', '=', 'tbl_user_recharges.customer_id'])
                      ->where('tbl_user_recharges.status', 'off')
                      ->where('tbl_user_recharges.type', 'Hotspot');
                } else {
                    $q->where('tbl_user_recharges.status', 'off')
                      ->where('tbl_user_recharges.type', 'Hotspot');
                }
                break;
            case 'all_hotspot':
                if (empty($router)) {
                    $q->join('tbl_user_recharges', ['tbl_customers.id', '=', 'tbl_user_recharges.customer_id'])
                      ->where('tbl_user_recharges.type', 'Hotspot');
                } else {
                    $q->where('tbl_user_recharges.type', 'Hotspot');
                }
                break;
            // 'all' — no filter, returns every customer
        }

        return $q;
    };
}

// --- Main dispatcher ---------------------------------------------------------
function marketing_scheduler()
{
    marketing_scheduler_ensure_table();
    global $ui, $routes;
    _admin();
    $ui->assign('_title', 'Marketing Scheduler');
    $ui->assign('_system_menu', 'message');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);

    $action = isset($routes['2']) ? $routes['2'] : 'list';
    $id     = isset($routes['3']) ? (int) $routes['3'] : null;

    switch ($action) {
        case 'add':
            _marketing_scheduler_add();
            break;
        case 'edit':
            _marketing_scheduler_edit($id);
            break;
        case 'save':
            _marketing_scheduler_save($id);
            break;
        case 'view':
            _marketing_scheduler_view($id);
            break;
        case 'delete':
            _marketing_scheduler_delete($id);
            break;
        case 'send_now':
            _marketing_scheduler_send_now($id);
            break;
        case 'cancel':
            _marketing_scheduler_cancel($id);
            break;
        case 'list':
        default:
            _marketing_scheduler_list();
            break;
    }
}

// --- List ---------------------------------------------------------------------
function _marketing_scheduler_list()
{
    global $ui;
    $campaigns = ORM::for_table('tbl_marketing_campaigns')
        ->order_by_desc('scheduled_at')
        ->find_array();
    $ui->assign('campaigns', $campaigns);
    $ui->display('marketing_scheduler_list.tpl');
}

// --- Add form -----------------------------------------------------------------
function _marketing_scheduler_add()
{
    global $ui;
    $routers = ORM::for_table('tbl_routers')->select('name')->find_array();
    $ui->assign('routers', $routers);
    $ui->assign('campaign', null);
    $ui->assign('server_time', date('Y-m-d H:i:s'));
    $ui->display('marketing_scheduler_form.tpl');
}

// --- Edit form ----------------------------------------------------------------
function _marketing_scheduler_edit($id)
{
    global $ui;
    $campaign = ORM::for_table('tbl_marketing_campaigns')->find_one($id);
    if (!$campaign) {
        r2(U . 'plugin/marketing_scheduler', 'e', 'Campaign not found');
    }
    if ($campaign['status'] !== 'pending') {
        r2(U . 'plugin/marketing_scheduler', 'w', 'Only pending campaigns can be edited');
    }
    $routers = ORM::for_table('tbl_routers')->select('name')->find_array();
    $data    = $campaign->as_array();
    // Format scheduled_at for datetime-local input (YYYY-MM-DDTHH:MM)
    $data['scheduled_at_local'] = date('Y-m-d\TH:i', strtotime($data['scheduled_at']));
    $ui->assign('campaign', $data);
    $ui->assign('routers', $routers);
    $ui->assign('server_time', date('Y-m-d H:i:s'));
    $ui->display('marketing_scheduler_form.tpl');
}

// --- Save (create or update) --------------------------------------------------
function _marketing_scheduler_save($id)
{
    $admin = Admin::_info();

    $title    = trim(_post('title'));
    $group    = _post('group_filter');
    $router   = _post('router_filter');
    $message  = trim(_post('message'));
    $via      = _post('via');
    $batch    = max(1, (int) _post('batch_size'));
    $delay    = max(0, (int) _post('delay_seconds'));
    $sched    = _post('scheduled_at'); // datetime-local format: YYYY-MM-DDTHH:MM

    if (empty($title) || empty($message) || empty($via) || empty($sched)) {
        $back = $id ? (U . 'plugin/marketing_scheduler/edit/' . $id) : (U . 'plugin/marketing_scheduler/add');
        r2($back, 'e', 'All fields are required');
    }

    $scheduled_at = date('Y-m-d H:i:s', strtotime($sched));
    if (!$scheduled_at || $scheduled_at === '1970-01-01 00:00:00') {
        r2(U . 'plugin/marketing_scheduler/add', 'e', 'Invalid scheduled date/time');
    }

    if (!empty($id)) {
        $campaign = ORM::for_table('tbl_marketing_campaigns')->find_one($id);
        if (!$campaign || $campaign['status'] !== 'pending') {
            r2(U . 'plugin/marketing_scheduler', 'e', 'Cannot edit this campaign');
        }
    } else {
        $campaign             = ORM::for_table('tbl_marketing_campaigns')->create();
        $campaign->created_by = $admin['username'];
        $campaign->created_at = date('Y-m-d H:i:s');
        $campaign->status     = 'pending';
        $campaign->total_sent = 0;
        $campaign->total_failed = 0;
        $campaign->total_recipients = 0;
    }

    $campaign->title         = $title;
    $campaign->group_filter  = $group;
    $campaign->router_filter = $router;
    $campaign->message       = $message;
    $campaign->via           = $via;
    $campaign->batch_size    = $batch;
    $campaign->delay_seconds = $delay;
    $campaign->scheduled_at  = $scheduled_at;
    $campaign->save();

    r2(U . 'plugin/marketing_scheduler', 's', 'Campaign saved successfully');
}

// --- View detail --------------------------------------------------------------
function _marketing_scheduler_view($id)
{
    global $ui;
    $campaign = ORM::for_table('tbl_marketing_campaigns')->find_one($id);
    if (!$campaign) {
        r2(U . 'plugin/marketing_scheduler', 'e', 'Campaign not found');
    }
    $ui->assign('campaign', $campaign->as_array());
    $ui->display('marketing_scheduler_view.tpl');
}

// --- Delete -------------------------------------------------------------------
function _marketing_scheduler_delete($id)
{
    $campaign = ORM::for_table('tbl_marketing_campaigns')->find_one($id);
    if ($campaign) {
        $campaign->delete();
    }
    r2(U . 'plugin/marketing_scheduler', 's', 'Campaign deleted');
}

// --- Cancel (pending ? cancelled) --------------------------------------------
function _marketing_scheduler_cancel($id)
{
    $campaign = ORM::for_table('tbl_marketing_campaigns')->find_one($id);
    if ($campaign && $campaign['status'] === 'pending') {
        $campaign->status = 'cancelled';
        $campaign->save();
    }
    r2(U . 'plugin/marketing_scheduler', 's', 'Campaign cancelled');
}

// --- Send Now (manual trigger) ------------------------------------------------
function _marketing_scheduler_send_now($id)
{
    global $config;
    $campaign = ORM::for_table('tbl_marketing_campaigns')->find_one($id);
    if (!$campaign || !in_array($campaign['status'], ['pending', 'failed'])) {
        r2(U . 'plugin/marketing_scheduler', 'e', 'Cannot send this campaign right now');
    }

    $campaign->status = 'running';
    $campaign->save();

    try {
        set_time_limit(0);
        $results = marketing_scheduler_process_campaign($campaign->as_array());

        $campaign->status           = 'sent';
        $campaign->total_sent       = $results['sent'];
        $campaign->total_failed     = $results['failed'];
        $campaign->total_recipients = $results['total'];
        $campaign->sent_at          = date('Y-m-d H:i:s');
        $campaign->save();

        r2(U . 'plugin/marketing_scheduler/view/' . $id, 's',
            "Done! Sent: {$results['sent']}, Failed: {$results['failed']}, Total: {$results['total']}");
    } catch (Exception $e) {
        $campaign->status = 'failed';
        $campaign->save();
        r2(U . 'plugin/marketing_scheduler', 'e', 'Send failed: ' . $e->getMessage());
    }
}
