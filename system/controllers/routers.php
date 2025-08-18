<?php

/**
 *  PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 *  by https://t.me/ibnux
 **/

_admin();
$ui->assign('_title', Lang::T('Network'));
$ui->assign('_system_menu', 'network');

$action = $routes['1'];
$ui->assign('_admin', $admin);

use PEAR2\Net\RouterOS;

require_once 'system/autoload/PEAR2/Autoload.php';

if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
    _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
}

use RouterOS\Exceptions\Socket\TimeoutException;
use RouterOS\Exceptions\Socket\ConnectionException;

function mikrotik_get_resources($router_id) {
    static $cache = [];
    $cache_ttl = 25; // 25 seconds cache

    $router = ORM::for_table('tbl_routers')->find_one($router_id);
    if (!$router) {
        return [
            'status' => 'Offline',
            'uptime' => 'N/A',
            'freeMemory' => 'N/A',
            'cpuLoad' => 'N/A',
            'lastSeen' => 'Never'
        ];
    }

    // Check cache first
    if (isset($cache[$router_id]) && (time() - $cache[$router_id]['time']) < $cache_ttl) {
        return $cache[$router_id]['data'];
    }

    try {
        // Quick connection test first
        $fp = @fsockopen($router['ip_address'], $router['port'] ?: 8728, $errno, $errstr, 2);
        if (!$fp) {
            throw new Exception('Connection failed');
        }
        fclose($fp);

        // Get router status info
        $router_status = ORM::for_table('tbl_router_status')
            ->where('router_id', $router_id)
            ->find_one();
            
        // Full RouterOS connection
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Get system resources
        $request = new RouterOS\Request('/system/resource/print');
        $response = $client->sendSync($request);
        
        if ($response && count($response) > 0) {
            $resource = $response[0];
            $current_time = date('Y-m-d H:i:s');
            
            // Update router status
            if (!$router_status) {
                $router_status = ORM::for_table('tbl_router_status')->create();
                $router_status->router_id = $router_id;
            }
            $router_status->last_online = $current_time;
            $router_status->last_check = $current_time;  // Update last check time
            $router_status->status = 'online';
            $router_status->save();
            
            $result = [
                'status' => 'Online',
                'uptime' => $resource->getProperty('uptime'),
                'freeMemory' => formatBytes($resource->getProperty('free-memory')),
                'cpuLoad' => $resource->getProperty('cpu-load') . '%',
                'lastSeen' => $current_time
            ];
            
            // Cache the result
            $cache[$router_id] = [
                'time' => time(),
                'data' => $result
            ];
            
            return $result;
        }
        
        throw new Exception('Invalid response from router');

    } catch (Exception $e) {
        error_log("Router {$router['name']} connection error: " . $e->getMessage());
        
        $lastSeen = ($router_status && $router_status->last_online) ? $router_status->last_online : 'Never';
        
        $result = [
            'status' => 'Offline',
            'uptime' => 'N/A',
            'freeMemory' => 'N/A',
            'cpuLoad' => 'N/A',
            'lastSeen' => $lastSeen
        ];
        
        // Cache offline status for shorter time
        $cache[$router_id] = [
            'time' => time(),
            'data' => $result
        ];
        
        return $result;
    }
}

function formatBytes($bytes) {
    if ($bytes > 1073741824) {
        return round($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes > 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes > 1024) {
        return round($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' B';
}

// Function to reboot MikroTik router
function mikrotik_reboot($routerId)
{
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($routerId);

    if (!$mikrotik) {
        return [
            'status' => 'Offline',
            'message' => 'Router not found'
        ];
    }

    try {
        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

        // Send the reboot command
        $client->sendSync(new RouterOS\Request('/system reboot'));

        return [
            'status' => 'Rebooting',
            'message' => 'Router is rebooting'
        ];
    } catch (TimeoutException | ConnectionException $e) {
        return [
            'status' => 'Error',
            'message' => 'Failed to connect to the router'
        ];
    }
}

switch ($action) {
    case 'list':
        $ui->assign('xfooter', '<script type="text/javascript" src="ui/lib/c/routers.js"></script>');

        $name = _post('name');
        if ($name != '') {
            $query = ORM::for_table('tbl_routers')->where_like('name', '%' . $name . '%')->order_by_desc('id');
            $d = Paginator::findMany($query, ['name' => $name]);
        } else {
            $query = ORM::for_table('tbl_routers')->order_by_desc('id');
            $d = Paginator::findMany($query);
        }

        $ui->assign('d', $d);
        run_hook('view_list_routers'); #HOOK
        $ui->display('routers.tpl');
        break;

    case 'get_resources':
        if (isset($_GET['router_id'])) {
            $routerId = $_GET['router_id'];
            $resources = mikrotik_get_resources($routerId);
            echo json_encode($resources);
            exit;
        }
        break;

    case 'reboot':
        if (isset($_GET['router_id'])) {
            $routerId = $_GET['router_id'];
            $result = mikrotik_reboot($routerId);
            echo json_encode($result);
            exit;
        }
        break;

    case 'add':
        run_hook('view_add_routers'); #HOOK
        $ui->display('routers-add.tpl');
        break;

    case 'edit':
        $id  = $routes['2'];
        $d = ORM::for_table('tbl_routers')->find_one($id);
        if (!$d) {
            $d = ORM::for_table('tbl_routers')->where_equal('name', _get('name'))->find_one();
        }
        if ($d) {
            $ui->assign('d', $d);
            run_hook('view_router_edit'); #HOOK
            $ui->display('routers-edit.tpl');
        } else {
            r2(U . 'routers/list', 'e', Lang::T('Account Not Found'));
        }
        break;

    case 'delete':
        $id  = $routes['2'];
        run_hook('router_delete'); #HOOK
        $d = ORM::for_table('tbl_routers')->find_one($id);
        if ($d) {
            $d->delete();
            r2(U . 'routers/list', 's', Lang::T('Data Deleted Successfully'));
        }
        break;

    case 'add-post':
        $name = _post('name');
        $ip_address = _post('ip_address');
        $username = _post('username');
        $password = _post('password');
        $description = _post('description');
        $enabled = _post('enabled');

        $msg = '';
        if (Validator::Length($name, 30, 4) == false) {
            $msg .= 'Name should be between 5 to 30 characters' . '<br>';
        }
        if ($ip_address == '' or $username == '') {
            $msg .= Lang::T('All field is required') . '<br>';
        }

        $d = ORM::for_table('tbl_routers')->where('ip_address', $ip_address)->find_one();
        if ($d) {
            $msg .= Lang::T('IP Router Already Exist') . '<br>';
        }
        if (strtolower($name) == 'radius') {
            $msg .= '<b>Radius</b> name is reserved<br>';
        }

        if ($msg == '') {
            Mikrotik::getClient($ip_address, $username, $password);
            run_hook('add_router'); #HOOK
            $d = ORM::for_table('tbl_routers')->create();
            $d->name = $name;
            $d->ip_address = $ip_address;
            $d->username = $username;
            $d->password = $password;
            $d->description = $description;
            $d->enabled = $enabled;
            $d->save();

            r2(U . 'routers/list', 's', Lang::T('Data Created Successfully'));
        } else {
            r2(U . 'routers/add', 'e', $msg);
        }
        break;

    case 'download':
        $routerId = _post('router_id');
        $routerName = _post('router_name');

        if ($routerId && $routerName) {
            $updateRouterIdStmt = $conn->prepare("UPDATE tbl_appconfig SET value = :router_id WHERE setting = 'router_id'");
            $updateRouterIdStmt->execute(['router_id' => $routerId]);

            $updateRouterNameStmt = $conn->prepare("UPDATE tbl_appconfig SET value = :router_name WHERE setting = 'router_name'");
            $updateRouterNameStmt->execute(['router_name' => $routerName]);

            header("Location: {$app_url}/system/plugin/download.php?download=1");
            exit;
        } else {
            r2(U . 'routers/list', 'e', Lang::T('Invalid router ID or name'));
        }
        break;

    case 'edit-post':
        $name = _post('name');
        $ip_address = _post('ip_address');
        $username = _post('username');
        $password = _post('password');
        $description = _post('description');
        $enabled = $_POST['enabled'];
        $msg = '';
        if (Validator::Length($name, 30, 4) == false) {
            $msg .= 'Name should be between 5 to 30 characters' . '<br>';
        }
        if ($ip_address == '' or $username == '') {
            $msg .= Lang::T('All field is required') . '<br>';
        }

        $id = _post('id');
        $d = ORM::for_table('tbl_routers')->find_one($id);
        if ($d) {
        } else {
            $msg .= Lang::T('Data Not Found') . '<br>';
        }

        if ($d['name'] != $name) {
            $c = ORM::for_table('tbl_routers')->where('name', $name)->where_not_equal('id', $id)->find_one();
            if ($c) {
                $msg .= 'Name Already Exists<br>';
            }
        }
        $oldname = $d['name'];

        if ($d['ip_address'] != $ip_address) {
            $c = ORM::for_table('tbl_routers')->where('ip_address', $ip_address)->where_not_equal('id', $id)->find_one();
            if ($c) {
                $msg .= 'IP Already Exists<br>';
            }
        }

        if (strtolower($name) == 'radius') {
            $msg .= '<b>Radius</b> name is reserved<br>';
        }

        if ($msg == '') {
            Mikrotik::getClient($ip_address, $username, $password);
            run_hook('router_edit'); #HOOK
            $d->name = $name;
            $d->ip_address = $ip_address;
            $d->username = $username;
            $d->password = $password;
            $d->description = $description;
            $d->enabled = $enabled;
            $d->save();
            if ($name != $oldname) {
                $p = ORM::for_table('tbl_plans')->where('routers', $oldname)->find_result_set();
                $p->set('routers', $name);
                $p->save();
                $p = ORM::for_table('tbl_payment_gateway')->where('routers', $oldname)->find_result_set();
                $p->set('routers', $name);
                $p->save();
                $p = ORM::for_table('tbl_pool')->where('routers', $oldname)->find_result_set();
                $p->set('routers', $name);
                $p->save();
                $p = ORM::for_table('tbl_transactions')->where('routers', $oldname)->find_result_set();
                $p->set('routers', $name);
                $p->save();
                $p = ORM::for_table('tbl_user_recharges')->where('routers', $oldname)->find_result_set();
                $p->set('routers', $name);
                $p->save();
                $p = ORM::for_table('tbl_voucher')->where('routers', $oldname)->find_result_set();
                $p->set('routers', $name);
                $p->save();
            }
            r2(U . 'routers/list', 's', Lang::T('Data Updated Successfully'));
        } else {
            r2(U . 'routers/edit/' . $id, 'e', $msg);
        }
        break;

    default:
        r2(U . 'routers/list/', 's', '');
}

?>
