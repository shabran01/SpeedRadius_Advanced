<?php



register_menu("IPDR", true, "ipdr_settings", 'SETTINGS', '');

function ipdr_settings()
{
    global $ui, $admin;
    _admin();
    $admin = Admin::_info();
    try {
        ORM::forTable('plugin_tbl_ipdr')->find_one();
    } catch (Exception $e) {
        ORM::forTable('tbl_customer')->raw_execute("CREATE TABLE IF NOT EXISTS `plugin_tbl_ipdr` (
            `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
            `username` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
            `mac` varchar(32) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
            `type` enum('access','received','login','logout') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
            `src_ip` varchar(39) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
            `src_port` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
            `dst_ip` varchar(39) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
            `dst_port` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
            `protocol` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
            `date_start` datetime NOT NULL,
            `date_end` datetime NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    }
    $filename = '';
    $username = _req("username");
    $ip = _req("ip");
    $dt_start = _req("dt_start", date('Y-m-d'));
    $dt_start_time = _req("dt_start_time", "00:00");
    $dt_end = _req("dt_end", date("Y-m-d"));
    $dt_end_time = _req("dt_end_time", "23:59");
    $append_url = "&ip=" . urlencode($ip)
        . "&dt_start=" . urlencode($dt_start)
        . "&dt_start_time=" . urlencode($dt_start_time)
        . "&dt_end=" . urlencode($dt_end)
        . "&dt_end_time=" . urlencode($dt_end_time);
    $page = _req("page", 0);
    $ui->assign('page', $page);
    $ui->assign('username', $username);
    $ui->assign('ip', $ip);
    $ui->assign('dt_start', $dt_start);
    $ui->assign('dt_start_time', $dt_start_time);
    $ui->assign('dt_end', $dt_end);
    $ui->assign('dt_end_time', $dt_end_time);
    $query = ORM::forTable('plugin_tbl_ipdr')->order_by_desc('id');
    if (!empty($username)) {
        $filename .= $username . '_';
        $query->where('username', $username);
    }
    if (!empty($ip)) {
        $query->whereRaw("src_ip = '$ip' OR dst_ip = '$ip'");
    }
    if (!empty($dt_start)) {
        $query->whereRaw("UNIX_TIMESTAMP(date_start) >= UNIX_TIMESTAMP('$dt_start $dt_start_time:00')");
        if (!empty($dt_end)) {
            $query->whereRaw("UNIX_TIMESTAMP(date_end) <= UNIX_TIMESTAMP('$dt_end $dt_end_time:59')");
        }
    }

    if (_post('export') == 'csv') {
        $filename = 'phpnuxbill_ipdr_' . $filename . '_' . date('Y-m-d_H_i', strtotime("$dt_start $dt_start_time")) . '_' . date('Y-m-d_H_i', strtotime("$dt_end $dt_end_time")) . '.csv';
        set_time_limit(-1);
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Content-type: text/csv");
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        $ds = $query->find_many();
        foreach ($ds as $c) {
            $row = [
                $c['username'],
                $c['mac'],
                $c['src_ip'],
                $c['src_port'],
                $c['type'],
                $c['dst_ip'],
                $c['dst_port'],
                $c['protocol'],
                $c['date_start'],
                $c['date_end']
            ];
            echo '"' . implode('","', $row) . "\"\n";
        }
        die();
    }

    if (_post('delete') == 'yes') {
        $query->delete_many();
    }

    $d = Paginator::findMany($query, ['username' => $username], 20, $append_url);

    $ui->assign('d', $d);
    $ui->assign('_admin', $admin);
    $ui->assign('_title', 'IPDR');
    $ui->assign('_system_menu', 'ipdr_settings');
    $ui->display('ipdr.tpl');
}


function ipdr_log()
{
    $data = file_get_contents('php://input');
    if (!empty($data)) {
        if (_get('tipe') == 'hotspotHost') {
            $datass = ipdr_parsingData($data);
            $path = ipdr_getPath();
            foreach ($datass as $data) {
                file_put_contents($path . DIRECTORY_SEPARATOR . "ip." . $data['address'] . ".nux", json_encode([
                    'mac' => $data['mac-address'],
                    'user' => ''
                ]));
            }
        } else if (_get('tipe') == 'pppoe') {
            $datass = ipdr_parsingData($data);
            $path = ipdr_getPath();
            foreach ($datass as $data) {
                file_put_contents($path . DIRECTORY_SEPARATOR . "ip." . $data['address'] . ".nux", json_encode([
                    'mac' => $data['caller-id'],
                    'user' => $data['name'],
                ]));
            }
            file_put_contents($path . DIRECTORY_SEPARATOR . "user." . $data['name'] . ".nux", $data['address']);
        } else if (_get('tipe') == 'hotspot') {
            $datass = ipdr_parsingData($data);
            $path = ipdr_getPath();
            foreach ($datass as $data) {
                if (file_exists($path . DIRECTORY_SEPARATOR . "ip." . $data['address'] . ".nux")) {
                    $user = json_decode(file_get_contents($path . DIRECTORY_SEPARATOR . "ip." . $data['address'] . ".nux"), true);
                    $user['user'] = $data['user'];
                    file_put_contents($path . DIRECTORY_SEPARATOR . "ip." . $data['address'] . ".nux", json_encode($user));
                } else {
                    file_put_contents($path . DIRECTORY_SEPARATOR . "ip." . $data['address'] . ".nux", json_encode([
                        'mac' => '',
                        'user' => $data['address']
                    ]));
                }
                file_put_contents($path . DIRECTORY_SEPARATOR . "user." . $data['user'] . ".nux", $data['address']);
            }
        } else if (_get('tipe') == 'data') {
            $path = ipdr_getPath();
            $datass = ipdr_parsingData($data);
            foreach ($datass as $data) {
                if ($data['tcp-state'] == 'established') {
                    $d = ORM::forTable("plugin_tbl_ipdr")->create();
                    $d->date_start = date("Y-m-d H:i:s");
                    $d->date_end = date("Y-m-d H:i:s");
                    $d->dst_ip = $data['dst-ip'];
                    $d->dst_port = $data['dst-port'];
                    $d->protocol = $data['protocol'];
                    $d->src_ip = $data['src-ip'];
                    $d->src_port = $data['src-port'];
                    $ip = $data['src-ip'];
                    $user = [];
                    $ada = false;
                    if (file_exists($path . DIRECTORY_SEPARATOR . "ip.$ip.nux")) {
                        $user = json_decode(file_get_contents($path . DIRECTORY_SEPARATOR . "ip.$ip.nux"), true);
                        $d->username = $user['user'];
                        $d->type = 'access';
                        $d->mac = $user['mac'];
                        $ada = true;
                    }
                    $ip = $data['dst-ip'];
                    if (file_exists($path . DIRECTORY_SEPARATOR . "ip.$ip.nux")) {
                        $user = json_decode(file_get_contents($path . DIRECTORY_SEPARATOR . "ip.$ip.nux"), true);
                        $d->username = $user['user'];
                        $d->type = 'received';
                        $d->mac = $user['mac'];
                        $ada = true;
                    }
                    if ($ada) {
                        $time = strtotime("-5 MINUTES");
                        $ds = ORM::forTable('plugin_tbl_ipdr')
                            ->where('username', $user['user'])
                            ->where('mac', $user['mac'])
                            ->where('dst_ip', $data['dst-ip'])
                            ->where('dst_port', $data['dst-port'])
                            ->where('src_ip', $data['src-ip'])
                            ->where('src_port', $data['src-port'])
                            ->where('protocol', $data['protocol'])
                            ->whereRaw("UNIX_TIMESTAMP(date_end) > $time")->find_one();
                        if ($ds) {
                            $ds->date_end = date("Y-m-d H:i:s");
                            $ds->save();
                        } else {
                            $d->save();
                        }
                    }
                    unset($user);
                }
            }
        }
    }
    die("ok");
}

function ipdr_parsingData($data)
{
    $result = [];
    $baris = explode('.id=', $data);
    $pos = 0;
    foreach ($baris as $br) {
        $cols = explode(';', $br);
        if (count($cols) > 2) {
            foreach ($cols as $cl) {
                $parts = explode('=', $cl);
                if (count($parts) == 2) {
                    if (!empty($parts[1])) {
                        $result[$pos][$parts[0]] = $parts[1];
                        if ($parts[0] == 'dst-address') {
                            $ipPort = explode(':', $parts[1]);
                            $result[$pos]['dst-ip'] = $ipPort[0];
                            $result[$pos]['dst-port'] = $ipPort[1];
                        }
                        if ($parts[0] == 'src-address') {
                            $ipPort = explode(':', $parts[1]);
                            $result[$pos]['src-ip'] = $ipPort[0];
                            $result[$pos]['src-port'] = $ipPort[1];
                        }
                    }
                }
            }
            $pos++;
        }
    }
    return $result;
}


function ipdr_onlogin()
{
    $user = _post('user');
    $mac = _post('mac');
    $ip = _post('ip');
    if (empty($user) || empty($mac) || empty($ip)) {
        die("empty data");
    }
    $path = ipdr_getPath();
    file_put_contents($path . DIRECTORY_SEPARATOR . "ip.$ip.nux", json_encode([
        'mac' => $mac,
        'user' => $user
    ]));
    file_put_contents($path . DIRECTORY_SEPARATOR . "user.$user.nux", $ip);
    $d = ORM::forTable("plugin_tbl_ipdr")->create();
    $d->username = $user;
    $d->type = 'login';
    $d->mac = $mac;
    $d->src_ip = $ip;
    $d->date_start = date("Y-m-d H:i:s");
    $d->date_end = date("Y-m-d H:i:s");
    $d->save();
    die("success");
}

function ipdr_onlogout()
{
    $user = _post('user');
    if (empty($user)) {
        die("empty data");
    }
    $path = ipdr_getPath();
    if (file_exists($path . DIRECTORY_SEPARATOR . "user.$user.nux")) {
        $ip = file_get_contents($path . DIRECTORY_SEPARATOR . "user.$user.nux");
        unlink($path . DIRECTORY_SEPARATOR . "user.$user.nux");
        if (file_exists($path . DIRECTORY_SEPARATOR . "ip.$ip.nux")) {
            unlink($path . DIRECTORY_SEPARATOR . "ip.$ip.nux");
        }
    }
    $d = ORM::forTable("plugin_tbl_ipdr")->create();
    $d->username = $user;
    $d->type = 'logout';
    $d->date_start = date("Y-m-d H:i:s");
    $d->date_end = date("Y-m-d H:i:s");
    $d->save();
    die("success");
}


function ipdr_getPath()
{
    global $CACHE_PATH;
    $path = $CACHE_PATH . DIRECTORY_SEPARATOR . "ipdr" . DIRECTORY_SEPARATOR;
    if (!file_exists($path)) {
        mkdir($path);
    }
    return $path;
}
