<?php

use PEAR2\Net\RouterOS;

// Register the PPPoE Secrets & Connections menu
register_menu("PPPoE Secrets & Connections", true, "pppoe_secrets_connections_menu", 'AFTER_SETTINGS', 'ion ion-ios-people', "New", "green");

function pppoe_secrets_connections_menu()
{
    global $ui, $routes;
    _admin();
    $ui->assign('_title', 'PPPoE Secrets & Connections');
    $ui->assign('_system_menu', 'PPPoE Secrets & Connections');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
    
    if (empty($routers)) {
        r2(U . 'dashboard', 'e', 'No active routers found');
    }
    
    $router = isset($routes['2']) && $routes['2'] ? $routes['2'] : ($routers[0]['id'] ?? '');
    
    $ui->assign('routers', $routers);
    $ui->assign('router', $router);
    
    $ui->display('pppoe_secrets_connections.tpl');
}

// Function to get PPPoE secrets
function pppoe_secrets_get() {
    global $routes;
    $router = isset($routes['2']) ? $routes['2'] : null;
    
    if (!$router) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No router specified']);
        return;
    }
    
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);

    if (!$mikrotik) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Router not found']);
        return;
    }

    try {
        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

        // Fetch PPP secrets
        $pppSecrets = $client->sendSync(new RouterOS\Request('/ppp/secret/print'));

        $secretList = [];
        foreach ($pppSecrets as $secret) {
            $name = $secret->getProperty('name');
            if (empty($name)) {
                continue;
            }
            
            $service = $secret->getProperty('service') ?: 'pppoe';
            $profile = $secret->getProperty('profile') ?: 'default';
            $lastLogout = $secret->getProperty('last-logout') ?: 'N/A';
            $disabled = $secret->getProperty('disabled') ?: 'false';
            $comment = $secret->getProperty('comment') ?: '';
            $id = $secret->getProperty('.id');

            $secretList[] = [
                'id' => $id,
                'username' => $name,
                'service' => $service,
                'profile' => $profile,
                'last_logout' => $lastLogout,
                'disabled' => $disabled,
                'comment' => $comment
            ];
        }

        // Return the secrets list as JSON
        header('Content-Type: application/json');
        echo json_encode($secretList);
    } catch (Exception $e) {
        error_log('PPPoE Secrets Error: ' . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Could not connect to router: ' . $e->getMessage()]);
    }
}

// Function to get active PPPoE connections
function pppoe_active_connections_get() {
    global $routes;
    $router = isset($routes['2']) ? $routes['2'] : null;
    
    if (!$router) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No router specified']);
        return;
    }
    
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);

    if (!$mikrotik) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Router not found']);
        return;
    }

    try {
        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

        // Fetch PPP active connections
        $pppActive = $client->sendSync(new RouterOS\Request('/ppp/active/print'));

        $activeList = [];
        foreach ($pppActive as $active) {
            $name = $active->getProperty('name');
            if (empty($name)) {
                continue;
            }
            
            $service = $active->getProperty('service') ?: 'pppoe';
            $address = $active->getProperty('address') ?: 'N/A';
            $uptime = $active->getProperty('uptime') ?: '0s';
            $encoding = $active->getProperty('encoding') ?: 'N/A';
            $callerId = $active->getProperty('caller-id') ?: 'N/A';
            $id = $active->getProperty('.id');

            $activeList[] = [
                'id' => $id,
                'name' => $name,
                'service' => $service,
                'address' => $address,
                'uptime' => $uptime,
                'encoding' => $encoding,
                'caller_id' => $callerId
            ];
        }

        // Return the active connections list as JSON
        header('Content-Type: application/json');
        echo json_encode($activeList);
    } catch (Exception $e) {
        error_log('PPPoE Active Connections Error: ' . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Could not connect to router: ' . $e->getMessage()]);
    }
}

// Function to disconnect a PPPoE user
function pppoe_disconnect_user() {
    global $routes;
    $router = isset($routes['2']) ? $routes['2'] : null;
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    
    if (!$router) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No router specified']);
        return;
    }
    
    if (!$id) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No user ID provided']);
        return;
    }
    
    $mikrotik = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router);

    if (!$mikrotik) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Router not found']);
        return;
    }

    try {
        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        
        // Create the remove request
        $removeRequest = new RouterOS\Request('/ppp/active/remove');
        $removeRequest->setArgument('numbers', $id);
        $client->sendSync($removeRequest);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        error_log('PPPoE Disconnect Error: ' . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Could not disconnect user: ' . $e->getMessage()]);
    }
}

// Register API endpoints
add_hook('render_template', 1, function ($data) {
    global $routes;
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    if (!empty($routes[0]) && $routes[0] === 'pppoe_secrets_connections' && !empty($action)) {
        switch ($action) {
            case 'get_secrets':
                pppoe_secrets_get();
                break;
            case 'get_active':
                pppoe_active_connections_get();
                break;
            case 'disconnect':
                pppoe_disconnect_user();
                break;
            default:
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Invalid action']);
        }
        exit;
    }
    return $data;
});
