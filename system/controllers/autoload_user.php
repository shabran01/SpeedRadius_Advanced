<?php

/**
 *  PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 *  by https://t.me/ibnux
 **/

/**
 * used for ajax
 **/

_auth();

$action = $routes['1'];
$user = User::_info();

switch ($action) {
    case 'isLogin':
        $bill = ORM::for_table('tbl_user_recharges')->where('id', $routes['2'])->where('username', $user['username'])->findOne();
        if ($bill['type'] == 'Hotspot' && $bill['status'] == 'on') {
            $p = ORM::for_table('tbl_plans')->find_one($bill['plan_id']);
            $dvc = Package::getDevice($p);
            if ($_app_stage != 'demo') {
                try {
                    if (file_exists($dvc)) {
                        require_once $dvc;
                        if ((new $p['device'])->online_customer($user, $bill['routers'])) {
                            die('<a href="' . U . 'home&mikrotik=logout&id=' . $bill['id'] . '" onclick="return confirm(\'' . Lang::T('Disconnect Internet?') . '\')" class="btn btn-success btn-xs btn-block">' . Lang::T('You are Online, Logout?') . '</a>');
                        } else {
                            if (!empty($_SESSION['nux-mac']) && !empty($_SESSION['nux-ip'])) {
                                die('<a href="' . U . 'home&mikrotik=login&id=' . $bill['id'] . '" onclick="return confirm(\'' . Lang::T('Connect to Internet?') . '\')" class="btn btn-danger btn-xs btn-block">' . Lang::T('Not Online, Login now?') . '</a>');
                            } else {
                                die(Lang::T('-'));
                            }
                        }
                    } else {
                        die(Lang::T('-'));
                    }
                } catch (Exception $e) {
                    die(Lang::T('Failed to connect to device'));
                }
            }
            die(Lang::T('-'));
        } else {
            die('--');
        }
        break;
    case 'bw_name':
        $bw = ORM::for_table('tbl_bandwidth')->select("name_bw")->find_one($routes['2']);
        echo $bw['name_bw'];
        die();
    case 'inbox_unread':
        $count =  ORM::for_table('tbl_customers_inbox')->where('customer_id', $user['id'])->whereRaw('date_read is null')->count('id');
        if ($count > 0) {
            echo $count;
        }
        die();
    case 'inbox':
        $inboxs = ORM::for_table('tbl_customers_inbox')->selects(['id', 'subject', 'date_created'])->where('customer_id', $user['id'])->whereRaw('date_read is null')->order_by_desc('date_created')->limit(10)->find_many();
        foreach ($inboxs as $inbox) {
            echo '<li><a href="' . U . 'mail/view/' . $inbox['id'] . '">' . $inbox['subject'] . '<br><sub class="text-muted">' . Lang::dateTimeFormat($inbox['date_created']) . '</sub></a></li>';
        }
        die();
    case 'language':
        $select = _get('select');
        $folders = [];
        $files = scandir('system/lan/');
        foreach ($files as $file) {
            if (is_file('system/lan/' . $file) && !in_array($file, ['index.html', 'country.json', '.DS_Store'])) {
                $file = str_replace(".json", "", $file);
                if(!empty($file)){
                    echo '<li><a href="' . U . 'accounts/language-update-post&lang=' . $file. '">';
                    if($select == $file){
                        echo '<span class="glyphicon glyphicon-ok"></span> ';
                    }
                    echo ucwords($file) . '</a></li>';
                }
            }
        }
        die();
    case 'live_bandwidth':
        header('Content-Type: application/json');
        $bill_id = (int)($routes['2'] ?? 0);
        $bill = ORM::for_table('tbl_user_recharges')
            ->where('id', $bill_id)
            ->where('username', $user['username'])
            ->find_one();
        if (!$bill || $bill['status'] != 'on' || $bill['routers'] == 'radius') {
            echo json_encode(['error' => 'Not available']);
            die();
        }
        if ($_app_stage == 'demo') {
            echo json_encode(['error' => 'Not available in demo']);
            die();
        }
        $p = ORM::for_table('tbl_plans')->find_one($bill['plan_id']);
        $dvc = Package::getDevice($p);
        if (!file_exists($dvc)) {
            echo json_encode(['error' => 'Device not found']);
            die();
        }
        try {
            require_once $dvc;
            $device = new $p['device']();
            $router = $device->info($bill['routers']);
            if (!$router) {
                echo json_encode(['error' => 'Router not found']);
                die();
            }
            $client = $device->getClient($router['ip_address'], $router['username'], $router['password']);

            if ($bill['type'] == 'Hotspot') {
                // Hotspot: bytes-in / bytes-out from /ip/hotspot/active/print
                $req = new \PEAR2\Net\RouterOS\Request('/ip/hotspot/active/print');
                $responses = $client->sendSync($req);
                $rx_bytes = 0; $tx_bytes = 0; $ip = ''; $uptime = '';
                foreach ($responses as $item) {
                    if ($item->getProperty('user') === $user['username']) {
                        $rx_bytes = (int)$item->getProperty('bytes-in');
                        $tx_bytes = (int)$item->getProperty('bytes-out');
                        $ip      = (string)$item->getProperty('address');
                        $uptime  = (string)$item->getProperty('uptime');
                        break;
                    }
                }
                echo json_encode([
                    'type'     => 'Hotspot',
                    'ip'       => $ip,
                    'uptime'   => $uptime,
                    'rx_bytes' => $rx_bytes,
                    'tx_bytes' => $tx_bytes,
                ]);
            } else {
                // PPPoE: read cumulative bytes from the dynamic interface <pppoe-username>
                $pppUsername = !empty($user['pppoe_username']) ? $user['pppoe_username'] : $user['username'];

                // Step 1: get IP and uptime from /ppp/active/print
                $activeReq = new \PEAR2\Net\RouterOS\Request('/ppp/active/print');
                $activeResp = $client->sendSync($activeReq);
                $ip = ''; $uptime = '';
                foreach ($activeResp as $item) {
                    if ($item->getProperty('name') === $pppUsername) {
                        $ip     = (string)$item->getProperty('address');
                        $uptime = (string)$item->getProperty('uptime');
                        break;
                    }
                }

                // Step 2: get rx-byte / tx-byte from the dynamic interface <pppoe-username>
                $ifaceName = "<pppoe-{$pppUsername}>";
                $ifaceReq  = new \PEAR2\Net\RouterOS\Request('/interface/print');
                $ifaceResp = $client->sendSync($ifaceReq);
                $rx_bytes = 0; $tx_bytes = 0;
                foreach ($ifaceResp as $iface) {
                    if ($iface->getProperty('name') === $ifaceName) {
                        $rx_bytes = (int)$iface->getProperty('rx-byte');
                        $tx_bytes = (int)$iface->getProperty('tx-byte');
                        break;
                    }
                }

                echo json_encode([
                    'type'     => 'PPPoE',
                    'ip'       => $ip,
                    'uptime'   => $uptime,
                    'rx_bytes' => $rx_bytes,
                    'tx_bytes' => $tx_bytes,
                ]);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        die();
        break;
    default:
        $ui->display('404.tpl');
}
