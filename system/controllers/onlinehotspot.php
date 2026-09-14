<?php

/**
 *  PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 *  by https://t.me/ibnux
 **/

_admin();
$ui->assign('_title', Lang::T('online'));
$ui->assign('_system_menu', 'onlineusers');
$ui->assign('onlineusers', $online);

$action = $routes['1'];
$ui->assign('_admin', $admin);

use PEAR2\Net\RouterOS;

function handle_action($action)
{
    switch ($action) {
        case 'get_hotspot_online_users':
            mikrotik_get_hotspot_online_users();
            break;
        case 'disconnect_online_user':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                mikrotik_disconnect_online_user($_POST['router'], $_POST['username'], $_POST['userType']);
            }
            break;
        case 'list':
            // Assuming you have a function to fetch the data for the online hotspot users list
            $onlineHotspotUsers = fetch_online_hotspot_users();
            $ui->assign('onlineHotspotUsers', $onlineHotspotUsers);
            $ui->display('onlinehotspot.tpl');
            break;
        default:
            // Handle default case, maybe return an error or redirect
            break;
    }
}

function mikrotik_get_hotspot_online_users()
{
    global $routes;
    $router = $routes['2'];
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
    $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
    $hotspotActive = $client->sendSync(new RouterOS\Request('/ip/hotspot/active/print'));
    $hotspotList = [];

    foreach ($hotspotActive as $hotspot) {
        $username = $hotspot->getProperty('user');
        $address = $hotspot->getProperty('address');
        $uptime = $hotspot->getProperty('uptime');
        $server = $hotspot->getProperty('server');
        $mac = $hotspot->getProperty('mac-address');
        $sessionTime = $hotspot->getProperty('session-time-left');
        $rxBytes = $hotspot->getProperty('bytes-in');
        $txBytes = $hotspot->getProperty('bytes-out');
        $hotspotList[] = [
            'username' => $username,
            'address' => $address,
            'uptime' => $uptime,
            'server' => $server,
            'mac' => $mac,
            'session_time' => $sessionTime,
            'rx_bytes' => mikrotik_formatBytes($rxBytes),
            'tx_bytes' => mikrotik_formatBytes($txBytes),
            'total' => mikrotik_formatBytes($txBytes + $rxBytes),
        ];
    }

    // Return the Hotspot online user list as JSON
    header('Content-Type: application/json');
    echo json_encode($hotspotList);
}

function mikrotik_disconnect_online_user($router, $username, $userType)
{
    // Check if the form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve the form data
        $router = $_POST['router'];
        $username = $_POST['username'];
        $userType = $_POST['userType'];

        $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);
        if (!$mikrotik) {
            // Handle the error response or redirection
            return;
        }

        try {
            $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            if ($userType == 'hotspot') {
                Mikrotik::removeHotspotActiveUser($client, $username);
                // Handle the success response or redirection
            } elseif ($userType == 'pppoe') {
                Mikrotik::removePpoeActive($client, $username);
                // Handle the success response or redirection
            } else {
                // Handle the error response or redirection
                return;
            }
        } catch (Exception $e) {
            // Handle the error response or redirection
        } finally {
            // Disconnect from the MikroTik router
            if (isset($client)) {
                $client->disconnect();
            }
        }
    }
}

// Helper function to format bytes
function mikrotik_formatBytes($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.2f %s", $bytes / pow(1024, $factor), @$units[$factor]);
}

// Call the main function with the action provided in the URL
$action = $routes['1'] ?? ''; // Assuming $routes is defined elsewhere
handle_action($action);

$ui->assign('onlineusers', $online);

$ui->display('onlinehotspot.tpl');
?>
