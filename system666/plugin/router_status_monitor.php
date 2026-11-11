<?php
/**
 * Router Status Monitor Plugin
 * Monitors router status and sends WhatsApp notifications
 */

use PEAR2\Net\RouterOS;
use PEAR2\Net\RouterOS\Client;
use PEAR2\Net\RouterOS\Request;
use PEAR2\Net\RouterOS\Response;

register_menu("Router Status Monitor", true, "router_status_monitor", 'AFTER_SETTINGS', 'ion ion-wifi' ,"Hot", "yellow");

// Create database table if not exists
$sql = file_get_contents(__DIR__ . '/create_router_status_table.sql');
try {
    ORM::raw_execute($sql);
} catch (Exception $e) {
    error_log('Failed to create router status table: ' . $e->getMessage());
}

function checkRouterConnection($router) {
    try {
        // Create a proper RouterOS client with all required parameters
        $client = new Client(
            $router['ip_address'],
            $router['username'],
            $router['password'],
            $router['port'] ? intval($router['port']) : 8728
        );
        
        // First check if router is reachable
        $request = new Request('/system/identity/print');
        $responses = $client->sendSync($request);
        
        if (!$responses || count($responses) === 0) {
            return ['status' => 'offline'];
        }

        // Try to get router uptime
        try {
            $uptimeRequest = new Request('/system/resource/print');
            $uptimeResponse = $client->sendSync($uptimeRequest);
            $uptime = null;
            
            if ($uptimeResponse && count($uptimeResponse) > 0) {
                foreach ($uptimeResponse as $response) {
                    if ($response && method_exists($response, 'getProperty') && $response->getProperty('uptime')) {
                        $uptime = $response->getProperty('uptime');
                        break;
                    }
                }
            }

            return [
                'status' => 'online',
                'uptime' => $uptime
            ];
        } catch (Exception $e) {
            // If we can't get uptime, just return online status
            error_log('Failed to get router uptime: ' . $e->getMessage());
            return ['status' => 'online'];
        }
    } catch (Exception $e) {
        error_log('Router connection error: ' . $e->getMessage());
        return ['status' => 'offline'];
    }
}

function router_status_monitor()
{
    global $ui, $config;
    _admin();
    $admin = Admin::_info();
    $ui->assign('_title', 'Router Status Monitor');
    $ui->assign('_admin', $admin);

    // Handle POST requests for configuration
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['router_id']) && isset($_POST['notification_number'])) {
            saveNotificationNumber($_POST['router_id'], $_POST['notification_number']);
            r2(U . 'plugin/router_status_monitor', 's', 'Notification number updated');
        }
    }

    // Handle AJAX requests
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'check_status':
                checkRouterStatus();
                break;
            case 'test_notification':
                testNotification($_GET['router_id']);
                break;
        }
        exit;
    }

    // Get all routers
    $routers = ORM::for_table('tbl_routers')
        ->where('enabled', '1')
        ->find_many();

    // Get router statuses
    foreach ($routers as &$router) {
        // Check current status
        $statusInfo = checkRouterConnection($router);
        $currentStatus = $statusInfo['status'];
        
        $status = ORM::for_table('tbl_router_status')
            ->where('router_id', $router['id'])
            ->find_one();
        
        if ($status) {
            if ($status['status'] !== $currentStatus) {
                updateRouterStatus($router['id'], $statusInfo);
                $status->status = $currentStatus;
            }
            $router['status'] = $currentStatus;
            $router['last_online'] = $status['last_online'];
            $router['last_offline'] = $status['last_offline'];
            $router['notification_number'] = $status['notification_number'];
            $router['last_notification'] = $status['last_notification_sent'];
            $router['last_uptime'] = $status['last_uptime'];
        } else {
            $router['status'] = $currentStatus;
            $router['last_online'] = '';
            $router['last_offline'] = '';
            $router['notification_number'] = '';
            $router['last_notification'] = '';
            $router['last_uptime'] = '';
            // Create initial status record
            updateRouterStatus($router['id'], $statusInfo);
        }
    }

    // Assign template variables
    $ui->assign('routers', $routers);
    $ui->assign('_system_menu', 'settings');
    
    // Display template
    $ui->display('router_status_monitor.tpl');
}

function saveNotificationNumber($router_id, $number) {
    $status = ORM::for_table('tbl_router_status')
        ->where('router_id', $router_id)
        ->find_one();
    
    if (!$status) {
        $status = ORM::for_table('tbl_router_status')->create();
        $status->router_id = $router_id;
        $status->status = 'unknown';
    }
    
    $status->notification_number = $number;
    $status->save();
}

function checkRouterStatus() {
    $routers = ORM::for_table('tbl_routers')
        ->where('enabled', '1')
        ->find_many();
    
    $response = array();
    foreach ($routers as $router) {
        $statusInfo = checkRouterConnection($router);
        updateRouterStatus($router['id'], $statusInfo);
        
        $router_status = ORM::for_table('tbl_router_status')
            ->where('router_id', $router['id'])
            ->find_one();
            
        $response[] = array(
            'router_id' => $router['id'],
            'status' => $statusInfo['status'],
            'last_online' => $router_status->last_online,
            'last_offline' => $router_status->last_offline,
            'last_notification' => $router_status->last_notification_sent,
            'last_uptime' => $router_status->last_uptime
        );
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
}

function updateRouterStatus($router_id, $statusInfo) {
    $router_status = ORM::for_table('tbl_router_status')
        ->where('router_id', $router_id)
        ->find_one();

    if (!$router_status) {
        $router_status = ORM::for_table('tbl_router_status')->create();
        $router_status->router_id = $router_id;
    }

    $current_time = date('Y-m-d H:i:s');
    $status = $statusInfo['status'];
    
    // Send notification if status has changed
    if ($router_status->status !== $status) {
        if ($status === 'online') {
            $router_status->last_online = $current_time;
            
            // Check if router was rebooted by comparing uptime
            $wasRebooted = false;
            if (isset($statusInfo['uptime']) && $statusInfo['uptime'] !== null) {
                $currentUptime = $statusInfo['uptime'];
                $lastUptime = $router_status->last_uptime;
                
                if ($lastUptime && intval($currentUptime) < intval($lastUptime)) {
                    $wasRebooted = true;
                }
                $router_status->last_uptime = $currentUptime;
            }
            
            if ($router_status->last_offline) {
                $downtime = strtotime($current_time) - strtotime($router_status->last_offline);
                sendStatusNotification($router_id, true, $downtime, $wasRebooted);
            }
        } else {
            $router_status->last_offline = $current_time;
            sendStatusNotification($router_id, false);
        }
    }

    $router_status->status = $status;
    $router_status->last_check = $current_time;
    $router_status->save();
    
    return $router_status;
}

function testNotification($router_id) {
    // First check and update the current status
    $router = ORM::for_table('tbl_routers')->find_one($router_id);
    if ($router) {
        $statusInfo = checkRouterConnection($router);
        $router_status = updateRouterStatus($router_id, $statusInfo);
        $success = sendTestNotification($router_id, $statusInfo['status']);
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false]);
    }
}

function sendStatusNotification($router_id, $isOnline, $downtime = null, $wasRebooted = false) {
    global $config;
    
    $router = ORM::for_table('tbl_routers')->find_one($router_id);
    $router_status = ORM::for_table('tbl_router_status')
        ->where('router_id', $router_id)
        ->find_one();
    
    if (!$router || !$router_status || empty($router_status->notification_number)) {
        return false;
    }

    // Create a detailed message
    $message = "🌐 Router Status Alert\n\n";
    $message .= "📡 Router: {$router->name}\n";
    $message .= "📍 IP: {$router->ip_address}\n";
    
    if ($isOnline) {
        $message .= "✅ Status: ONLINE\n";
        if ($downtime !== null) {
            $hours = floor($downtime / 3600);
            $minutes = floor(($downtime % 3600) / 60);
            $seconds = $downtime % 60;
            $message .= "⏱ Previous Downtime: {$hours}h {$minutes}m {$seconds}s\n";
        }
        $message .= "\n";
        if ($wasRebooted) {
            $message .= "🔄 Router has successfully rebooted and is back online!";
        } else {
            $message .= "✨ Router is now back online and operational!";
        }
    } else {
        $message .= "❌ Status: OFFLINE\n";
        $message .= "⚠️ Router is currently unreachable!\n";
        $message .= "🕒 Went offline at: " . date('Y-m-d H:i:s') . "\n";
        $message .= "\n⚡ Please check the router's power and network connection.";
    }
    
    // Use WhatsappGateway plugin's hook
    if (function_exists('whatsappGateway_hook_send_whatsapp')) {
        $result = whatsappGateway_hook_send_whatsapp([
            $router_status->notification_number,
            $message
        ]);
        
        $success = !empty($result);
        
        if ($success) {
            $router_status->last_notification_sent = date('Y-m-d H:i:s');
            $router_status->notification_status = $result;
            $router_status->save();
        }
        
        return $success;
    }
    
    return false;
}

function sendTestNotification($router_id, $currentStatus) {
    global $config;
    
    $router = ORM::for_table('tbl_routers')->find_one($router_id);
    $router_status = ORM::for_table('tbl_router_status')
        ->where('router_id', $router_id)
        ->find_one();
    
    if (!$router || !$router_status || empty($router_status->notification_number)) {
        return false;
    }
    
    $message = "🔔 Router Status Monitor - Test Message\n\n";
    $message .= "📡 Router: {$router->name}\n";
    $message .= "📍 IP: {$router->ip_address}\n";
    $message .= "✅ Current Status: " . strtoupper($currentStatus) . "\n";
    if ($currentStatus === 'online') {
        $message .= "✨ Router is currently operational\n";
    } else {
        $message .= "⚠️ Router is currently unreachable\n";
    }
    $message .= "\nThis is a test message to confirm that WhatsApp notifications are working correctly.";
    
    // Use WhatsappGateway plugin's hook
    if (function_exists('whatsappGateway_hook_send_whatsapp')) {
        $result = whatsappGateway_hook_send_whatsapp([
            $router_status->notification_number,
            $message
        ]);
        
        $success = !empty($result);
        
        if ($success) {
            $router_status->last_notification_sent = date('Y-m-d H:i:s');
            $router_status->notification_status = $result;
            $router_status->save();
        }
        
        return $success;
    }
    
    return false;
}
