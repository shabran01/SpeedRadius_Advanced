<?php

// Ensure Logs Table Exists
if (!isTableExist('tbl_router_notif_logs')) {
    ORM::raw_execute("CREATE TABLE IF NOT EXISTS tbl_router_notif_logs (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        router_id INT(11) NOT NULL,
        message TEXT NOT NULL,
        type VARCHAR(20) NOT NULL,
        recipients TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

register_hook('monitor_router_finished', 'plugin_router_status_notifier');
register_hook('cronjob', 'router_status_notifier_cron');

function plugin_router_status_notifier($args)
{
    // $args: [$router (ORM object), $previous_status (string)]
    $router = $args[0];
    $previous_status = $args[1];

    if (!$router || !isset($router->status)) {
        return;
    }

    // Load Config
    $config = ORM::for_table('tbl_appconfig')->find_many();
    $conf = [];
    foreach ($config as $c) {
        $conf[$c['setting']] = $c['value'];
    }

    // 1. Check Ignore List
    $ignored_routers = isset($conf['router_notif_ignore']) ? explode(',', $conf['router_notif_ignore']) : [];
    if (in_array($router['id'], $ignored_routers)) {
        return;
    }

    $current_time = date('Y-m-d H:i:s');
    $type = '';

    $cur_status  = strtolower($router->status);
    $prev_status = strtolower($previous_status);

    // Check Status Change
    if ($prev_status !== 'offline' && $cur_status === 'offline') {
        $type = 'offline';
        // Record offline_since for downtime tracking and cron reminders
        if (empty($router->offline_since)) {
            $router->offline_since = $current_time;
            $router->save();
        }
        // NOW send the offline alert immediately (not just wait for cron)
    } elseif ($cur_status !== 'offline' && $prev_status === 'offline') {
        $type = 'online';
        // Capture offline_since BEFORE clearing — needed for [[downtime]] in the back-online message
        $was_offline_since = $router->offline_since;
        // Clear offline_since now that router is back
        $router->offline_since = null;
        $router->save();
    } else {
        return; // No relevant change
    }

    // 2. Flapping Detection — apply to ALL notification types (including online)
    $flap_seconds = isset($conf['router_notif_flap_seconds']) ? intval($conf['router_notif_flap_seconds']) : 60;
    if ($flap_seconds > 0) {
        $last_log = ORM::for_table('tbl_router_notif_logs')
            ->where('router_id', $router['id'])
            ->order_by_desc('created_at')
            ->find_one();

        if ($last_log) {
            $last_time = strtotime($last_log['created_at']);
            if ((time() - $last_time) < $flap_seconds) {
                // Flapping detected, skip notification
                echo "Plugin: Flapping detected for Router {$router['name']}. Skipping notification.\n";
                return;
            }
        }
    }

    // 3. Prepare Message (Templates) — with emojis
    $tpl_offline = isset($conf['router_notif_tpl_offline']) ? $conf['router_notif_tpl_offline'] : "🔴 Router [[name]] ([[ip]]) is OFFLINE.\n⏰ Time: [[time]]\n⏬ Downtime: [[downtime]]";
    $tpl_online  = isset($conf['router_notif_tpl_online'])  ? $conf['router_notif_tpl_online']  : "🟢 Router [[name]] ([[ip]]) is back ONLINE.\n⏰ Time: [[time]]\n⏬ Downtime: [[downtime]]";

    $template = ($type == 'offline') ? $tpl_offline : $tpl_online;

    // Calculate downtime for [[downtime]] variable
    // For 'online' type use $was_offline_since captured before clearing; for 'offline' type use current field
    $downtime_str = 'N/A';
    $offline_ref = isset($was_offline_since) ? $was_offline_since : $router['offline_since'];
    if (!empty($offline_ref)) {
        $diff_secs = time() - strtotime($offline_ref);
        $mins = floor($diff_secs / 60);
        $secs = $diff_secs % 60;
        $downtime_str = "{$mins}m {$secs}s";
    }

    $message = str_replace(
        ['[[name]]', '[[ip]]', '[[time]]', '[[downtime]]'],
        [$router['name'], $router['ip_address'], $current_time, $downtime_str],
        $template
    );

    // 4. Recipients
    $recipients_str = isset($conf['router_notif_recipients']) ? $conf['router_notif_recipients'] : '';
    $recipients = array_filter(explode(',', $recipients_str));

    // Fallback to Admin Phone if empty
    if (empty($recipients)) {
        if (!empty($conf['admin_phone'])) {
            $recipients[] = $conf['admin_phone'];
        } else {
            $adminUser = ORM::for_table('tbl_users')->where_not_null('phonenumber')->find_one();
            if ($adminUser) {
                $recipients[] = $adminUser->phonenumber;
            } else if (!empty($conf['phone'])) {
                $recipients[] = $conf['phone'];
            }
        }
    }

    // Send
    Message::sendTelegram($message);
    $sent_to = [];
    foreach ($recipients as $phone) {
        $phone = trim($phone);
        if (!empty($phone)) {
            Message::sendWhatsapp($phone, $message);
            Message::sendSMS($phone, $message);
            $sent_to[] = $phone;
        }
    }

    // 5. Save Log
    $log = ORM::for_table('tbl_router_notif_logs')->create();
    $log->router_id = $router['id'];
    $log->message = $message;
    $log->type = $type;
    $log->recipients = implode(', ', $sent_to);
    $log->created_at = date('Y-m-d H:i:s');
    $log->save();
}

// ─── Cron: send SMS after confirmed offline delay ────────────────────────────
function router_status_notifier_cron()
{
    // Load config
    $config_rows = ORM::for_table('tbl_appconfig')->find_many();
    $conf = [];
    foreach ($config_rows as $c) {
        $conf[$c['setting']] = $c['value'];
    }

    $delay_minutes = isset($conf['router_notif_offline_delay']) ? max(1, (int)$conf['router_notif_offline_delay']) : 3;
    $ignored_routers = isset($conf['router_notif_ignore']) ? array_filter(explode(',', $conf['router_notif_ignore'])) : [];

    // Find routers that have been offline for >= delay_minutes
    $threshold = date('Y-m-d H:i:s', strtotime("-{$delay_minutes} minutes"));

    $offline_routers = ORM::for_table('tbl_routers')
        ->where_raw("LOWER(status) = 'offline'")
        ->where_not_null('offline_since')
        ->where_lte('offline_since', $threshold)
        ->where('enabled', '1')
        ->find_many();

    foreach ($offline_routers as $router) {
        // Skip ignored routers
        if (in_array($router->id, $ignored_routers)) {
            continue;
        }

        // Check if we already sent an offline notification since offline_since
        $already_notified = ORM::for_table('tbl_router_notif_logs')
            ->where('router_id', $router->id)
            ->where('type', 'offline')
            ->where_gte('created_at', $router->offline_since)
            ->find_one();

        if ($already_notified) {
            continue; // Already sent, don't spam
        }

        // Flapping protection
        $flap_seconds = isset($conf['router_notif_flap_seconds']) ? (int)$conf['router_notif_flap_seconds'] : 60;
        if ($flap_seconds > 0) {
            $last_log = ORM::for_table('tbl_router_notif_logs')
                ->where('router_id', $router->id)
                ->order_by_desc('created_at')
                ->find_one();
            if ($last_log && (time() - strtotime($last_log->created_at)) < $flap_seconds) {
                continue;
            }
        }

        // Build message
        $tpl = isset($conf['router_notif_tpl_offline'])
            ? $conf['router_notif_tpl_offline']
            : "🔴 ALERT: Router [[name]] ([[ip]]) has been OFFLINE for [[downtime]].\n⏰ First detected: [[time]]";

        $diff_secs = time() - strtotime($router->offline_since);
        $mins      = floor($diff_secs / 60);
        $secs      = $diff_secs % 60;
        $downtime  = "{$mins}m {$secs}s";

        $message = str_replace(
            ['[[name]]', '[[ip]]', '[[time]]', '[[downtime]]'],
            [$router->name, $router->ip_address, $router->offline_since, $downtime],
            $tpl
        );

        // Recipients
        $recipients_str = isset($conf['router_notif_recipients']) ? $conf['router_notif_recipients'] : '';
        $recipients = array_filter(explode(',', $recipients_str));
        if (empty($recipients)) {
            if (!empty($conf['admin_phone']))        $recipients[] = $conf['admin_phone'];
            elseif (!empty($conf['phone']))          $recipients[] = $conf['phone'];
        }

        // Send
        Message::sendTelegram($message);
        $sent_to = [];
        foreach ($recipients as $phone) {
            $phone = trim($phone);
            if (!empty($phone)) {
                Message::sendSMS($phone, $message);
                Message::sendWhatsapp($phone, $message);
                $sent_to[] = $phone;
            }
        }

        // Log
        if (!empty($sent_to)) {
            $log = ORM::for_table('tbl_router_notif_logs')->create();
            $log->router_id  = $router->id;
            $log->message    = $message;
            $log->type       = 'offline';
            $log->recipients = implode(', ', $sent_to);
            $log->created_at = date('Y-m-d H:i:s');
            $log->save();

            echo "[Router Notifier] Sent offline alert for {$router->name} (down {$downtime})\n";
        }
    }
}

// Register Menu
register_menu(" Router Notifier", true, "router_status_notifier_ui", 'NETWORK', 'ion ion-ios-bell', "Router Notifier", "purple");

function router_status_notifier_ui()
{
    global $ui, $config;
    _admin();
    $ui->assign('_title', 'Router Status Notifications');
    $ui->assign('_system_menu', 'network');

    $admin = Admin::_info();
    $ui->assign('_admin', $admin);

    // Handle Settings Save
    if (isset($_POST['save_settings'])) {
        $settings = [
            'router_notif_recipients',
            'router_notif_tpl_offline',
            'router_notif_tpl_online',
            'router_notif_flap_seconds',
            'router_notif_offline_delay',
        ];

        foreach ($settings as $setting) {
            $val = isset($_POST[$setting]) ? $_POST[$setting] : '';
            $d = ORM::for_table('tbl_appconfig')->where('setting', $setting)->find_one();
            if (!$d) {
                $d = ORM::for_table('tbl_appconfig')->create();
                $d->setting = $setting;
            }
            $d->value = $val;
            $d->save();
        }

        // Handle Ignore List (Array to CSV)
        $ignored = isset($_POST['router_notif_ignore']) ? implode(',', $_POST['router_notif_ignore']) : '';
        $d = ORM::for_table('tbl_appconfig')->where('setting', 'router_notif_ignore')->find_one();
        if (!$d) {
            $d = ORM::for_table('tbl_appconfig')->create();
            $d->setting = 'router_notif_ignore';
        }
        $d->value = $ignored;
        $d->save();

        $ui->assign('notify', 'Settings Saved Successfully');
        $ui->assign('notify_type', 'success');
    }

    // Handle Test Send
    if (isset($_POST['send_test'])) {
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
        $message = isset($_POST['message']) ? $_POST['message'] : '';

        if (!empty($phone) && !empty($message)) {
            Message::sendWhatsapp($phone, "[TEST WA] " . $message);
            Message::sendSMS($phone, "[TEST SMS] " . $message);

            // Save to Log
            $log = ORM::for_table('tbl_router_notif_logs')->create();
            $log->router_id = 0; // 0 for System/Test
            $log->message = "[TEST] " . $message;
            $log->type = 'test';
            $log->recipients = $phone;
            $log->created_at = date('Y-m-d H:i:s');
            $log->save();

            $msg = "Test Command Sent.<br>Check your phone for WhatsApp/SMS.<br>Log entry created.";
            $ui->assign('notify', $msg);
            $ui->assign('notify_type', 'success');
        } else {
            $ui->assign('notify', 'Phone number and message are required');
            $ui->assign('notify_type', 'danger');
        }
    }

    // Handle Template Simulation
    if (isset($_POST['test_offline_tpl']) || isset($_POST['test_online_tpl'])) {
        $sim_type = isset($_POST['test_offline_tpl']) ? 'offline' : 'online';

        // Load Config to ensure latest
        $config_sim = ORM::for_table('tbl_appconfig')->find_many();
        $c_sim = [];
        foreach ($config_sim as $c) {
            $c_sim[$c['setting']] = $c['value'];
        }

        // Defaults
        $tpl_off = isset($c_sim['router_notif_tpl_offline']) ? $c_sim['router_notif_tpl_offline'] : "Router [[name]] ([[ip]]) is OFFLINE.\nTime: [[time]]";
        $tpl_on = isset($c_sim['router_notif_tpl_online']) ? $c_sim['router_notif_tpl_online'] : "Router [[name]] ([[ip]]) is back ONLINE.\nTime: [[time]]";

        $template = ($sim_type == 'offline') ? $tpl_off : $tpl_on;

        // Check if real router selected
        $r_name = 'TEST-ROUTER';
        $r_ip = '192.168.88.1';

        if (!empty($_POST['sim_router_id'])) {
            $sim_r = ORM::for_table('tbl_routers')->find_one($_POST['sim_router_id']);
            if ($sim_r) {
                $r_name = $sim_r['name'];
                $r_ip = $sim_r['ip_address'];
            }
        }

        $sim_msg = str_replace(
            ['[[name]]', '[[ip]]', '[[time]]'],
            [$r_name, $r_ip, date('Y-m-d H:i:s')],
            $template
        );

        // Get Recipients
        $recipients_str = isset($c_sim['router_notif_recipients']) ? $c_sim['router_notif_recipients'] : '';
        $recipients = array_filter(explode(',', $recipients_str));

        // Fallback recipient logic
        if (empty($recipients)) {
            if (!empty($c_sim['admin_phone'])) {
                $recipients[] = $c_sim['admin_phone'];
            } else {
                $adminUser = ORM::for_table('tbl_users')->where_not_null('phonenumber')->find_one();
                if ($adminUser) {
                    $recipients[] = $adminUser->phonenumber;
                } else if (!empty($c_sim['phone'])) {
                    $recipients[] = $c_sim['phone'];
                }
            }
        }

        // Send logic
        $sent_count = 0;
        foreach ($recipients as $phone) {
            $phone = trim($phone);
            if (!empty($phone)) {
                Message::sendWhatsapp($phone, "[SIMUL] " . $sim_msg);
                Message::sendSMS($phone, "[SIMUL] " . $sim_msg);
                $sent_count++;
            }
        }

        if ($sent_count > 0) {
            $ui->assign('notify', "Simulation Sent to $sent_count recipient(s).<br>Message: $sim_msg");
            $ui->assign('notify_type', 'success');
        } else {
            $ui->assign('notify', "Simulation Failed: No valid recipients found in Settings.");
            $ui->assign('notify_type', 'warning');
        }
    }

    // Load Settings
    $config_latest = ORM::for_table('tbl_appconfig')->find_many();
    $pconf = [];
    foreach ($config_latest as $c) {
        $pconf[$c['setting']] = $c['value'];
    }

    // Set defaults if not set
    if (!isset($pconf['router_notif_tpl_offline']))
        $pconf['router_notif_tpl_offline'] = "ALERT: Router [[name]] ([[ip]]) has been OFFLINE for [[downtime]].\nFirst detected: [[time]]";
    if (!isset($pconf['router_notif_tpl_online']))
        $pconf['router_notif_tpl_online'] = "Router [[name]] ([[ip]]) is back ONLINE.\nTime: [[time]]";
    if (!isset($pconf['router_notif_flap_seconds']))
        $pconf['router_notif_flap_seconds'] = 60;
    if (!isset($pconf['router_notif_recipients']))
        $pconf['router_notif_recipients'] = '';
    if (!isset($pconf['router_notif_offline_delay']))
        $pconf['router_notif_offline_delay'] = 3;

    $ui->assign('pconf', $pconf);

    // Get Routers for Ignore List list
    $routers = ORM::for_table('tbl_routers')->find_many();
    $ui->assign('routers', $routers);
    $ui->assign('ignored_routers', isset($pconf['router_notif_ignore']) ? explode(',', $pconf['router_notif_ignore']) : []);

    // Get Logs
    $logs = ORM::for_table('tbl_router_notif_logs')
        ->select('tbl_router_notif_logs.*')
        ->select('tbl_routers.name', 'router_name')
        ->left_outer_join('tbl_routers', ['tbl_router_notif_logs.router_id', '=', 'tbl_routers.id'])
        ->order_by_desc('created_at')
        ->limit(50)
        ->find_many();

    // Process logs to handle 0 router_id (Test)
    $processed_logs = [];
    foreach ($logs as $l) {
        if ($l->router_id == 0) {
            $l->router_name = 'SYSTEM (TEST)';
        }
        $processed_logs[] = $l;
    }

    $ui->assign('logs', $processed_logs);

    $ui->display('router_status_notifier.tpl');
}
