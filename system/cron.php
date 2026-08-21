<?php

include "../init.php";
$lockFile = "$CACHE_PATH/router_monitor.lock";

if (!is_dir($CACHE_PATH)) {
    echo "Directory '$CACHE_PATH' does not exist. Exiting...\n";
    exit;
}

$lock = fopen($lockFile, 'c');

if ($lock === false) {
    echo "Failed to open lock file. Exiting...\n";
    exit;
}

if (!flock($lock, LOCK_EX | LOCK_NB)) {
    echo "Script is already running. Exiting...\n";
    fclose($lock);
    exit;
}


$isCli = true;
if (php_sapi_name() !== 'cli') {
    $isCli = false;
    echo "<pre>";
}
echo "PHP Time\t" . date('Y-m-d H:i:s') . "\n";
$res = ORM::raw_execute('SELECT NOW() AS WAKTU;');
$statement = ORM::get_last_statement();
$rows = array();
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    echo "MYSQL Time\t" . $row['WAKTU'] . "\n";
}

$_c = $config;


// Default expired message - will be overridden with service-specific message per user
$textExpired = Lang::getNotifText('expired');

$d = ORM::for_table('tbl_user_recharges')->where('status', 'on')->where_lte('expiration', date("Y-m-d"))->find_many();
echo "Found " . count($d) . " user(s)\n";
run_hook('cronjob'); #HOOK

foreach ($d as $ds) {
    try {
        $date_now = strtotime(date("Y-m-d H:i:s"));
        $expiration = strtotime($ds['expiration'] . ' ' . $ds['time']);
        echo $ds['expiration'] . " : " . (($isCli) ? $ds['username'] : Lang::maskText($ds['username']));
        if ($date_now >= $expiration) {
            echo " : EXPIRED \r\n";
            $u = ORM::for_table('tbl_user_recharges')->where('id', $ds['id'])->find_one();
            $c = ORM::for_table('tbl_customers')->where('id', $ds['customer_id'])->find_one();
            $p = ORM::for_table('tbl_plans')->where('id', $u['plan_id'])->find_one();
            if (empty($c)) {
                $c = $u;
            }

            $dvc = Package::getDevice($p);
            if ($_app_stage != 'demo') {
                if (file_exists($dvc)) {
                    require_once $dvc;
                    try {
                        $removed = (new $p['device'])->remove_customer($c, $p);
                        if ($removed === false) {
                            _log("Failed to remove $c[username] from router - will retry next cron run");
                            Message::sendTelegram("Router unreachable: could not remove $c[username] - will retry next cron run");
                            echo "ROUTER REMOVAL FAILED for $c[username] - will retry next cron run\r\n";
                            continue;
                        }
                    } catch (\Exception $e) {
                        _log("Error removing customer from router: " . $e->getMessage());
                        Message::sendTelegram("Error removing customer $c[username] from router: " . $e->getMessage());
                        // Leave status='on' so cron retries next run when router is reachable
                        echo "ROUTER REMOVAL FAILED for $c[username] - will retry next cron run\r\n";
                        continue;
                    }
                } else {
                    $error_msg = "Cron error Devices $p[device] not found, cannot disconnect $c[username]";
                    echo $error_msg . "\n";
                    Message::sendTelegram($error_msg);
                    continue;
                }
            }

            // Mark as expired only after successful router removal (or demo mode)
            $u->status = 'off';
            $u->save();

            // Get expired notification type from notification settings
            global $_notifmsg;
            $expired_notification_type = 'sms'; // default fallback
            if (isset($_notifmsg['expired_notification'])) {
                $expired_notification_type = $_notifmsg['expired_notification'];
            } else if (isset($config['user_notification_expired'])) {
                // Fallback to old config system
                $expired_notification_type = $config['user_notification_expired'];
            }
            // If channel is 'none' or empty, fall back to payment_notification channel
            // This happens when admin sets channel to 'none' intending to block one type
            // but per-type toggles (send_expired_to_pppoe/hotspot) still need a channel
            if (empty($expired_notification_type) || $expired_notification_type === 'none') {
                $expired_notification_type = !empty($_notifmsg['payment_notification']) ? $_notifmsg['payment_notification'] : (!empty($config['user_notification_expired']) ? $config['user_notification_expired'] : 'sms');
                echo "⚠ expired_notification is 'none' - falling back to channel: $expired_notification_type\n";
            }

            // Check if we should send notification based on service type
            $should_send = true;
            $user_type = isset($u['type']) ? strtoupper($u['type']) : 'HOTSPOT';

            if ($user_type == 'PPPOE') {
                // Check if sending to PPPoE users is enabled
                if (isset($_notifmsg['send_expired_to_pppoe']) && $_notifmsg['send_expired_to_pppoe'] != '1') {
                    $should_send = false;
                    echo "⊗ Skipping notification - PPPoE users disabled for expired notifications\n";
                }
            } else {
                // Check if sending to Hotspot users is enabled
                if (isset($_notifmsg['send_expired_to_hotspot']) && $_notifmsg['send_expired_to_hotspot'] != '1') {
                    $should_send = false;
                    echo "⊗ Skipping notification - Hotspot users disabled for expired notifications\n";
                }
            }

            if ($should_send) {
                // Get service-specific expired message
                $expired_message = $textExpired; // default
                $service_suffix = ($user_type == 'PPPOE') ? '_pppoe' : '_hotspot';
                $message_key = 'expired' . $service_suffix;
                $service_message = Lang::getNotifText($message_key);
                // Use service-specific message if available
                if (!empty($service_message) && $service_message != $message_key) {
                    $expired_message = $service_message;
                    echo "✓ Sending expired notification to {$user_type} user: {$c['username']}\n";
                } else {
                    $expired_message = $textExpired;
                    echo "✓ Sending expired notification (default message) to: {$c['username']}\n";
                }

                echo Message::sendPackageNotification($c, $u['namebp'], $p['price'], $expired_message, $expired_notification_type) . "\n";
            }

            // autorenewal from deposit
            if ($config['enable_balance'] == 'yes' && $c['auto_renewal']) {
                list($bills, $add_cost) = User::getBills($ds['customer_id']);
                if ($add_cost != 0) {
                    if (!empty($add_cost)) {
                        $p['price'] += $add_cost;
                    }
                }
                if ($p && $c['balance'] >= $p['price']) {
                    if (Package::rechargeUser($ds['customer_id'], $ds['routers'], $p['id'], 'Customer', 'Balance')) {
                        // if success, then get the balance
                        Balance::min($ds['customer_id'], $p['price']);
                        echo "plan enabled: $p[enabled] | User balance: $c[balance] | price $p[price]\n";
                        echo "auto renewall Success\n";
                    } else {
                        echo "plan enabled: $p[enabled] | User balance: $c[balance] | price $p[price]\n";
                        echo "auto renewall Failed\n";
                        Message::sendTelegram("FAILED RENEWAL #cron\n\n#u$c[username] #buy #Hotspot \n" . $p['name_plan'] .
                            "\nRouter: " . $p['routers'] .
                            "\nPrice: " . $p['price']);
                    }
                } else {
                    echo "no renewall | plan enabled: $p[enabled] | User balance: $c[balance] | price $p[price]\n";
                }
            } else {
                echo "no renewall | balance $config[enable_balance] auto_renewal $c[auto_renewal]\n";
            }
        } else {
            echo " : ACTIVE \r\n";
        }
    } catch (\Exception $e) {
        _log("Error processing user $ds[username]: " . $e->getMessage());
        Message::sendTelegram("Error processing user $ds[username]: " . $e->getMessage());
        continue;
    }
}


if ($config['router_check']) {
    echo "Checking router status...\n";
    $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
    if (!$routers) {
        echo "No active routers found in the database.\n";
        flock($lock, LOCK_UN);
        fclose($lock);
        unlink($lockFile);
        exit;
    }

    $offlineRouters = [];
    $errors = [];

    foreach ($routers as $router) {
        $previous_status = $router->status; // Capture previous status for plugin

        // check if custom port
        if (strpos($router->ip_address, ':') === false) {
            $ip = $router->ip_address;
            $port = 8728;
        } else {
            [$ip, $port] = explode(':', $router->ip_address);
        }
        $isOnline = false;

        try {
            $timeout = 5;
            if (is_callable('fsockopen') && false === stripos(ini_get('disable_functions'), 'fsockopen')) {
                $fsock = @fsockopen($ip, $port, $errno, $errstr, $timeout);
                if ($fsock) {
                    fclose($fsock);
                    $isOnline = true;
                } else {
                    throw new Exception("Unable to connect to $ip on port $port using fsockopen: $errstr ($errno)");
                }
            } elseif (is_callable('stream_socket_client') && false === stripos(ini_get('disable_functions'), 'stream_socket_client')) {
                $connection = @stream_socket_client("$ip:$port", $errno, $errstr, $timeout);
                if ($connection) {
                    fclose($connection);
                    $isOnline = true;
                } else {
                    throw new Exception("Unable to connect to $ip on port $port using stream_socket_client: $errstr ($errno)");
                }
            } else {
                throw new Exception("Neither fsockopen nor stream_socket_client are enabled on the server.");
            }
        } catch (Exception $e) {
            _log($e->getMessage());
            $errors[] = "Error with router $ip: " . $e->getMessage();
        }

        if ($isOnline) {
            $router->last_seen = date('Y-m-d H:i:s');
            $router->status = 'Online';
        } else {
            $router->status = 'Offline';
            $offlineRouters[] = $router;
        }

        $router->save();

        // Trigger Plugin Hook
        run_hook('monitor_router_finished', [$router, $previous_status]);
    }

    if (!empty($offlineRouters)) {
        $message = "Dear Administrator,\n";
        $message .= "The following routers are offline:\n";
        foreach ($offlineRouters as $router) {
            $message .= "Name: {$router->name}, IP: {$router->ip_address}, Last Seen: {$router->last_seen}\n";
        }
        $message .= "\nPlease check the router's status and take appropriate action.\n\nBest regards,\nRouter Monitoring System";

        $adminEmail = $config['mail_from'];
        $subject = "Router Offline Alert";
        Message::SendEmail($adminEmail, $subject, $message);
        sendTelegram($message);
    }

    if (!empty($errors)) {
        $message = "The following errors occurred during router monitoring:\n";
        foreach ($errors as $error) {
            $message .= "$error\n";
        }

        $adminEmail = $config['mail_from'];
        $subject = "Router Monitoring Error Alert";
        Message::SendEmail($adminEmail, $subject, $message);
        sendTelegram($message);
    }
    echo "Router monitoring finished\n";
}


if (defined('PHP_SAPI') && PHP_SAPI === 'cli') {
    echo "Cronjob finished\n";
} else {
    echo "</pre>";
}

flock($lock, LOCK_UN);
fclose($lock);
unlink($lockFile);

$timestampFile = "$UPLOAD_PATH/cron_last_run.txt";
file_put_contents($timestampFile, time());
// ─────────────────────────────────────────────────────────────────────────────
// Monthly Data-Usage Tracking
// Snapshots active hotspot / PPPoE session bytes for every customer and
// accumulates incremental deltas into tbl_customer_monthly_usage.
// tbl_customer_monthly_usage is keyed by (customer_id, YYYY-MM) so data
// resets automatically on the 1st of each month.
// ─────────────────────────────────────────────────────────────────────────────
try {
    require_once __DIR__ . '/helpers/monthly_usage.php';
    require_once __DIR__ . '/autoload/Mikrotik.php';
    require_once __DIR__ . '/autoload/PEAR2/Autoload.php';

    monthly_usage_ensure_tables();
    monthly_usage_ensure_router_table();

    $active_routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
    echo "\n[DataUsage] Scanning " . count($active_routers) . " router(s)...\n";

    // Track actual online count per router for dashboard cache
    $pppoe_online_by_router = [];
    $hotspot_online_by_router = [];

    foreach ($active_routers as $router) {
        $router_id = (int)$router['id'];
        try {
            $client = Mikrotik::getClient(
                $router['ip_address'],
                $router['username'],
                $router['password']
            );
            if (!$client) {
                echo "[DataUsage] Cannot connect to router {$router['name']}\n";
                continue;
            }

            // ── Hotspot active users ──────────────────────────────────────
            $hotspot_router_count = 0;
            try {
                $req = new PEAR2\Net\RouterOS\Request('/ip hotspot active print');
                $responses = $client->sendSync($req);

                foreach ($responses as $resp) {
                    if ($resp->getType() !== PEAR2\Net\RouterOS\Response::TYPE_DATA) continue;

                    $hs_username  = $resp->getProperty('user');
                    $bytes_in     = (int)($resp->getProperty('bytes-in')  ?: $resp->getProperty('rx-bytes')  ?: 0);
                    $bytes_out    = (int)($resp->getProperty('bytes-out') ?: $resp->getProperty('tx-bytes') ?: 0);

                    if (empty($hs_username)) continue;
                    $hotspot_router_count++;

                    $customer = ORM::for_table('tbl_customers')
                        ->where('username', $hs_username)
                        ->find_one();
                    if (!$customer) continue;

                    monthly_usage_process_session(
                        $customer['id'], $hs_username, $router_id,
                        $bytes_in, $bytes_out
                    );
                    echo "[DataUsage] Hotspot {$hs_username}: in={$bytes_in} out={$bytes_out}\n";
                }
                $hotspot_online_by_router[$router_id] = $hotspot_router_count;
                echo "[DataUsage] Hotspot online on router {$router['name']}: {$hotspot_router_count}\n";
            } catch (Exception $e) {
                echo "[DataUsage] Hotspot scan error on {$router['name']}: " . $e->getMessage() . "\n";
            }

            // ── PPPoE active connections ──────────────────────────────────
            // Uses exact same approach as the pppoe_monitor plugin:
            // fetch ALL interface stats once, then look up by "<pppoe-username>"
            try {
                // Step 1: load all interface stats into a map  name => [rx, tx]
                $ifaceMap = [];
                $ifaceReq = new PEAR2\Net\RouterOS\Request('/interface/print');
                $ifaceResponses = $client->sendSync($ifaceReq);
                foreach ($ifaceResponses as $ir) {
                    if ($ir->getType() !== PEAR2\Net\RouterOS\Response::TYPE_DATA) continue;
                    $ifName = (string)$ir->getProperty('name');
                    if (empty($ifName)) continue;
                    $ifaceMap[$ifName] = [
                        'rx' => (int)($ir->getProperty('rx-byte') ?: 0),
                        'tx' => (int)($ir->getProperty('tx-byte') ?: 0),
                    ];
                }

                // Step 2: iterate PPP active sessions and look up bytes by "<pppoe-username>"
                $pppReq = new PEAR2\Net\RouterOS\Request('/ppp/active/print');
                $pppResponses = $client->sendSync($pppReq);

                $pppoe_router_count = 0;
                foreach ($pppResponses as $resp) {
                    if ($resp->getType() !== PEAR2\Net\RouterOS\Response::TYPE_DATA) continue;

                    $ppp_username = $resp->getProperty('name');
                    if (empty($ppp_username)) continue;
                    $pppoe_router_count++;

                    // MikroTik names the virtual interface "<pppoe-username>"
                    $ifaceName = "<pppoe-{$ppp_username}>";
                    $bytes_in  = isset($ifaceMap[$ifaceName]) ? $ifaceMap[$ifaceName]['rx'] : 0;
                    $bytes_out = isset($ifaceMap[$ifaceName]) ? $ifaceMap[$ifaceName]['tx'] : 0;

                    // Try matching by pppoe_username first, then fall back to username
                    $customer = ORM::for_table('tbl_customers')
                        ->where('pppoe_username', $ppp_username)
                        ->find_one();
                    if (!$customer) {
                        $customer = ORM::for_table('tbl_customers')
                            ->where('username', $ppp_username)
                            ->find_one();
                    }
                    if (!$customer) continue;

                    monthly_usage_process_session(
                        $customer['id'], $customer['username'], $router_id,
                        $bytes_in, $bytes_out
                    );
                    echo "[DataUsage] PPPoE {$ppp_username}: in={$bytes_in} out={$bytes_out}\n";
                }
                $pppoe_online_by_router[$router_id] = $pppoe_router_count;
                echo "[DataUsage] PPPoE online on router {$router['name']}: {$pppoe_router_count}\n";
            } catch (Exception $e) {
                echo "[DataUsage] PPPoE scan error on {$router['name']}: " . $e->getMessage() . "\n";
            }

            // ── Router WAN byte tracking (persists monthly totals across reboots) ──
            try {
                $wanRx = 0;
                $wanTx = 0;
                $wanFound = false;

                // Fetch all interface stats
                $wanIfReq = new PEAR2\Net\RouterOS\Request('/interface/print');
                $wanIfResp = $client->sendSync($wanIfReq);
                $allIfaces = [];
                foreach ($wanIfResp as $wi) {
                    if ($wi->getType() !== PEAR2\Net\RouterOS\Response::TYPE_DATA) continue;
                    $allIfaces[] = [
                        'name' => (string)$wi->getProperty('name'),
                        'type' => (string)$wi->getProperty('type'),
                        'rx'   => (int)($wi->getProperty('rx-byte') ?: 0),
                        'tx'   => (int)($wi->getProperty('tx-byte') ?: 0),
                    ];
                }

                // Detect WAN interface via default route
                $wanIfaceNames = [];
                try {
                    $routeReq = new PEAR2\Net\RouterOS\Request('/ip/route/print');
                    $routeReq->setQuery(PEAR2\Net\RouterOS\Query::where('dst-address', '0.0.0.0/0'));
                    $routeResp = $client->sendSync($routeReq);
                    foreach ($routeResp as $rt) {
                        $iname = $rt->getProperty('interface');
                        if (!empty($iname)) $wanIfaceNames[] = $iname;
                    }
                } catch (Exception $e) {}

                foreach ($allIfaces as $iface) {
                    if (in_array($iface['name'], $wanIfaceNames)) {
                        $wanRx   += $iface['rx'];
                        $wanTx   += $iface['tx'];
                        $wanFound = true;
                    }
                }

                // Fallback: common WAN naming patterns
                if (!$wanFound) {
                    foreach ($allIfaces as $iface) {
                        if (
                            preg_match('/^(ether1|wan|internet|pppoe-out|fiber|adsl)/i', $iface['name']) ||
                            $iface['type'] === 'pppoe-out'
                        ) {
                            $wanRx   += $iface['rx'];
                            $wanTx   += $iface['tx'];
                            $wanFound = true;
                            break;
                        }
                    }
                }

                if ($wanFound && ($wanRx > 0 || $wanTx > 0)) {
                    monthly_usage_process_router_wan($router_id, $router['name'], $wanRx, $wanTx);
                    echo "[DataUsage] WAN {$router['name']}: rx=" . $wanRx . " tx=" . $wanTx . "\n";
                }
            } catch (Exception $e) {
                echo "[DataUsage] WAN tracking error on {$router['name']}: " . $e->getMessage() . "\n";
            }

        } catch (Exception $e) {
            echo "[DataUsage] Router {$router['name']} connection error: " . $e->getMessage() . "\n";
        }
    }

    // Write actual PPPoE online count to cache for the dashboard
    $pppoe_cache_file = "$CACHE_PATH/pppoe_online_count.json";
    file_put_contents($pppoe_cache_file, json_encode([
        'total'      => array_sum($pppoe_online_by_router),
        'by_router'  => $pppoe_online_by_router,
        'updated_at' => time(),
    ]));
    echo "[DataUsage] PPPoE online cache updated: " . array_sum($pppoe_online_by_router) . " total.\n";

    // Write actual Hotspot online count to cache for the dashboard
    $hotspot_cache_file = "$CACHE_PATH/hotspot_online_count.json";
    file_put_contents($hotspot_cache_file, json_encode([
        'total'      => array_sum($hotspot_online_by_router),
        'by_router'  => $hotspot_online_by_router,
        'updated_at' => time(),
    ]));
    echo "[DataUsage] Hotspot online cache updated: " . array_sum($hotspot_online_by_router) . " total.\n";

    echo "[DataUsage] Done.\n";
} catch (Exception $e) {
    echo "[DataUsage] Fatal error: " . $e->getMessage() . "\n";
}

run_hook('cronjob_end'); #HOOK