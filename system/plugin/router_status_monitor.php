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
            $response = ['success' => true, 'message' => 'Notification number updated'];
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }
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

    // Check WhatsApp Gateway configuration
    $whatsapp_configured = !empty($config['whatsapp_gateway_url']) && function_exists('whatsappGateway_hook_send_whatsapp');
    
    // Assign template variables
    $ui->assign('routers', $routers);
    $ui->assign('_system_menu', 'settings');
    $ui->assign('whatsapp_configured', $whatsapp_configured);
    
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
    $current_time = date('Y-m-d H:i:s');
    $status = $statusInfo['status'];
    
    $router_status = ORM::for_table('tbl_router_status')
        ->where('router_id', $router_id)
        ->find_one();
        
    if (!$router_status) {
        $router_status = ORM::for_table('tbl_router_status')->create();
        $router_status->router_id = $router_id;
        $router_status->status = $status;
        $router_status->last_check = $current_time;
        if ($status === 'online') {
            $router_status->last_online = $current_time;
        } else {
            $router_status->last_offline = $current_time;
        }
    } else {
        // Always update last_check time when checking status
        $router_status->last_check = $current_time;
        
        // Check if status changed
        if ($router_status->status !== $status) {
            // Get router name for notification
            $router = ORM::for_table('tbl_routers')->find_one($router_id);
            $routerName = $router ? $router->name : "Router #$router_id";
            
            if ($status === 'online') {
                $router_status->last_online = $current_time;
                // Send back online notification
                sendNotification($router_id, 'online', $routerName);
            } else {
                $router_status->last_offline = $current_time;
                // Send offline notification
                sendNotification($router_id, 'offline', $routerName);
            }
        }
        
        // If router is online, always update last_check
        if ($status === 'online') {
            $router_status->last_check = $current_time;
            $router_status->last_online = $current_time;
        }
        
        $router_status->status = $status;
        $router_status->last_check = $current_time;
        if (isset($statusInfo['uptime'])) {
            $router_status->last_uptime = $statusInfo['uptime'];
        }
    }
    
    $router_status->save();
    return $router_status;
}

function testNotification($router_id) {
    // Enable error logging
    error_log("Test Notification called for router ID: $router_id");
    
    // Check if router exists
    $router = ORM::for_table('tbl_routers')->find_one($router_id);
    if (!$router) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Router not found'
        ]);
        return;
    }

    // Get router status
    $router_status = ORM::for_table('tbl_router_status')
        ->where('router_id', $router_id)
        ->find_one();

    // Validate notification number
    if (!$router_status || empty($router_status->notification_number)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'WhatsApp number not configured for this router'
        ]);
        return;
    }

    // Validate WhatsApp Gateway configuration
    if (!function_exists('whatsappGateway_hook_send_whatsapp')) {
        error_log("WhatsApp Gateway function not available");
        
        // Try to include the WhatsApp Gateway plugin
        $whatsapp_plugin_path = __DIR__ . '/WhatsappGateway.php';
        if (file_exists($whatsapp_plugin_path)) {
            include_once $whatsapp_plugin_path;
        }
        
        // Check again after inclusion attempt
        if (!function_exists('whatsappGateway_hook_send_whatsapp')) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'WhatsApp Gateway not properly configured. Please make sure WhatsApp Gateway plugin is enabled.'
            ]);
            return;
        }
    }

    // Prepare test message
    $message = "🔔 Router Monitor Test Message\n\n";
    $message .= "📡 Router: {$router->name}\n";
    $message .= "📍 IP: {$router->ip_address}\n";
    $message .= "✅ Status: " . ($router_status->status === 'online' ? "ONLINE" : "OFFLINE") . "\n";
    $message .= "📅 Date: " . date('Y-m-d H:i:s') . "\n\n";
    $message .= "This is a test message to verify WhatsApp notifications are working correctly.";

    try {
        global $config;
        
        // Check WhatsApp Gateway URL configuration
        if (empty($config['whatsapp_gateway_url'])) {
            error_log("WhatsApp Gateway URL not configured");
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'WhatsApp Gateway URL not configured. Please set it in System Settings.'
            ]);
            return;
        }
        
        // Send test message
        error_log("Attempting to send test message to: " . $router_status->notification_number);
        $result = whatsappGateway_hook_send_whatsapp([
            $router_status->notification_number,
            $message
        ]);
        
        // Log the result
        error_log("WhatsApp send result: " . ($result ? $result : "Empty result"));

        if ($result && strpos($result, 'error') === false) {
            // Update last notification time
            $router_status->last_notification_sent = date('Y-m-d H:i:s');
            $router_status->save();

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Test notification sent successfully'
            ]);
        } else {
            $error_msg = $result ? $result : 'Failed to send WhatsApp message';
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $error_msg
            ]);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

function sendStatusNotification($router_id, $isOnline, $downtime = null, $wasRebooted = false) {
    global $config;
    
    $router = ORM::for_table('tbl_routers')->find_one($router_id);
    $router_status = ORM::for_table('tbl_router_status')
        ->where('router_id', $router_id)
        ->find_one();
    
    if (!$router || !$router_status || empty($router_status->notification_number)) {
        error_log('Router Status Monitor: Missing router information or notification number');
        return false;
    }

    // Validate WhatsApp number format
    $whatsapp_number = trim($router_status->notification_number);
    if (!preg_match('/^\+?[1-9]\d{1,14}$/', $whatsapp_number)) {
        error_log('Router Status Monitor: Invalid WhatsApp number format');
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
        try {
            $result = whatsappGateway_hook_send_whatsapp([
                $whatsapp_number,
                $message
            ]);
            
            $success = !empty($result);
            
            if ($success) {
                $router_status->last_notification_sent = date('Y-m-d H:i:s');
                $router_status->notification_status = $result;
                $router_status->save();
            } else {
                error_log('Router Status Monitor: WhatsApp notification failed - empty result');
            }
            
            return $success;
        } catch (Exception $e) {
            error_log('Router Status Monitor: WhatsApp notification error - ' . $e->getMessage());
            return false;
        }
    } else {
        error_log('Router Status Monitor: WhatsappGateway plugin hook not found');
        return false;
    }
}

function sendTestNotification($router_id, $currentStatus) {
    global $config;
    
    $router = ORM::for_table('tbl_routers')->find_one($router_id);
    $router_status = ORM::for_table('tbl_router_status')
        ->where('router_id', $router_id)
        ->find_one();
    
    if (!$router || !$router_status) {
        error_log('Router Status Monitor: Router or status record not found');
        return false;
    }

    if (empty($router_status->notification_number)) {
        error_log('Router Status Monitor: No WhatsApp number configured');
        return false;
    }

    // Validate WhatsApp number format
    $whatsapp_number = trim($router_status->notification_number);
    if (!preg_match('/^\+?[1-9]\d{1,14}$/', $whatsapp_number)) {
        error_log('Router Status Monitor: Invalid WhatsApp number format');
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
        try {
            $result = whatsappGateway_hook_send_whatsapp([
                $whatsapp_number,
                $message
            ]);
            
            $success = !empty($result);
            
            if ($success) {
                $router_status->last_notification_sent = date('Y-m-d H:i:s');
                $router_status->notification_status = $result;
                $router_status->save();
            } else {
                error_log('Router Status Monitor: Test notification failed - empty result');
            }
            
            return $success;
        } catch (Exception $e) {
            error_log('Router Status Monitor: Test notification error - ' . $e->getMessage());
            return false;
        }
    } else {
        error_log('Router Status Monitor: WhatsappGateway plugin hook not found');
        return false;
    }
}

function sendNotification($router_id, $status, $routerName = '') {
    global $config;
    
    // Get router status record
    $router_status = ORM::for_table('tbl_router_status')
        ->where('router_id', $router_id)
        ->find_one();
        
    if (!$router_status || !$router_status->notification_enabled || empty($router_status->notification_number)) {
        return false;
    }
    
    // Check if we should send notification (prevent spam)
    $now = new DateTime();
    if ($router_status->last_notification_sent) {
        $last_sent = new DateTime($router_status->last_notification_sent);
        $interval = $now->diff($last_sent);
        
        // Don't send notifications more frequently than every 5 minutes
        if ($interval->i < 5) {
            return false;
        }
    }
    
    // Prepare message
    $message = "";
    if ($status === 'offline') {
        $message = "🔴 Router '$routerName' is now OFFLINE\n";
        $message .= "Time: " . date('Y-m-d H:i:s');
    } else {
        $message = "🟢 Router '$routerName' is back ONLINE\n";
        $message .= "Time: " . date('Y-m-d H:i:s');
        if ($router_status->last_offline) {
            $offline_time = new DateTime($router_status->last_offline);
            $downtime = $now->diff($offline_time);
            $message .= "\nDowntime: " . formatDowntime($downtime);
        }
    }
    
    // Send WhatsApp message
    $phone = $router_status->notification_number;
    $result = whatsappGateway_hook_send_whatsapp([$phone, $message]);
    
    // Update last notification time
    $router_status->last_notification_sent = date('Y-m-d H:i:s');
    $router_status->save();
    
    return true;
}

function formatDowntime($interval) {
    $parts = [];
    if ($interval->d > 0) $parts[] = $interval->d . " days";
    if ($interval->h > 0) $parts[] = $interval->h . " hours";
    if ($interval->i > 0) $parts[] = $interval->i . " minutes";
    return implode(", ", $parts);
}
