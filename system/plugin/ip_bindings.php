<?php

/**
 * IP Bindings Plugin for PHPNuxBill
 * 
 * This plugin displays MikroTik Hotspot IP Bindings information
 * Allows viewing and managing IP to MAC address bindings
 * 
 * @package PHPNuxBill
 * @author GitHub Copilot
 * @version 1.0.0
 */

use PEAR2\Net\RouterOS;

// Register menu item in admin panel
register_menu("IP Bindings", true, "ip_bindings", 'AFTER_SETTINGS', 'ion ion-link', "New", "red", ['Admin', 'SuperAdmin']);

// Register plugin hook
register_hook('ip_bindings', 'ip_bindings');

function ip_bindings() {
    global $ui, $config;
    
    // Check admin authentication
    $admin = Admin::_info();
    if (!$admin) {
        r2(U . 'admin', 'e', $_L['Please login first']);
    }
    
    $action = _post('action');
    
    if ($action == 'refresh_data') {
        // Handle AJAX refresh request
        $router_id = _post('router_id');
        $data = ip_bindings_get_data($router_id);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    } elseif ($action == 'export_csv') {
        // Handle CSV export
        $router_id = _post('router_id');
        ip_bindings_export($router_id);
        exit;
    } elseif ($action == 'add_binding') {
        // Handle add new binding
        $router_id = _post('router_id');
        $result = ip_bindings_add($router_id);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } elseif ($action == 'remove_binding') {
        // Handle remove binding
        $router_id = _post('router_id');
        $binding_id = _post('binding_id');
        $mac_address = _post('mac_address');
        $address = _post('address');
        $result = ip_bindings_remove($router_id, $binding_id, $mac_address, $address);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } elseif ($action == 'enable_binding') {
        // Handle enable binding
        $router_id = _post('router_id');
        $binding_id = _post('binding_id');
        $result = ip_bindings_enable($router_id, $binding_id);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } elseif ($action == 'disable_binding') {
        // Handle disable binding
        $router_id = _post('router_id');
        $binding_id = _post('binding_id');
        $result = ip_bindings_disable($router_id, $binding_id);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } elseif ($action == 'edit_binding') {
        // Handle edit binding
        $router_id = _post('router_id');
        $binding_id = _post('binding_id');
        $result = ip_bindings_edit($router_id, $binding_id);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } else {
        // Display main page
        ip_bindings_display();
    }
}

function ip_bindings_display() {
    global $ui, $config;
    
    try {
        // Get all routers
        $routers = ORM::for_table('tbl_routers')->find_many();
        
        // Get default router data
        $default_router = null;
        $bindings_data = [];
        
        if (!empty($routers)) {
            $default_router = $routers[0];
            $bindings_data = ip_bindings_get_data($default_router['id']);
        }
        
        // Assign variables to template
        $ui->assign('routers', $routers);
        $ui->assign('default_router', $default_router);
        $ui->assign('bindings_data', $bindings_data);
        $ui->assign('_title', 'IP Bindings - Hotspot IP to MAC Bindings');
        
        // Display template
        $ui->display('ip_bindings.tpl');
        
    } catch (Exception $e) {
        // If there's an error, show a basic error page
        echo '<div class="alert alert-danger">Error loading IP Bindings: ' . $e->getMessage() . '</div>';
        error_log('IP Bindings Plugin Error: ' . $e->getMessage());
    }
}

function ip_bindings_get_data($router_id) {
    if (empty($router_id)) {
        return [
            'success' => false,
            'error' => 'No router selected',
            'data' => [],
            'stats' => ['total' => 0, 'active' => 0, 'disabled' => 0]
        ];
    }
    
    // Get router information
    $router = ORM::for_table('tbl_routers')->find_one($router_id);
    if (!$router) {
        return [
            'success' => false,
            'error' => 'Router not found',
            'data' => [],
            'stats' => ['total' => 0, 'active' => 0, 'disabled' => 0]
        ];
    }
    
    try {
        // Connect to MikroTik router
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Get IP bindings from MikroTik
        $response = $client->sendSync(new RouterOS\Request('/ip/hotspot/ip-binding/print'));
        
        if (empty($response)) {
            return [
                'success' => true,
                'router_name' => $router['name'],
                'router_ip' => $router['ip_address'],
                'data' => [],
                'stats' => [
                    'total' => 0,
                    'active' => 0,
                    'disabled' => 0
                ],
                'last_updated' => date('Y-m-d H:i:s')
            ];
        }
        
        // Process and format the data
        $formatted_bindings = [];
        $total_bindings = 0;
        $active_bindings = 0;
        $disabled_bindings = 0;
        
        foreach ($response as $binding) {
            $total_bindings++;
            
            // Get binding properties using getProperty() method
            $id = $binding->getProperty('.id') ?? '';
            $mac_address = $binding->getProperty('mac-address') ?? '';
            $address = $binding->getProperty('address') ?? '';
            $to_address = $binding->getProperty('to-address') ?? '';
            $server = $binding->getProperty('server') ?? 'all';
            $type = $binding->getProperty('type') ?? 'regular';
            $disabled = $binding->getProperty('disabled') ?? 'false';
            $comment = $binding->getProperty('comment') ?? '';
            
            // Determine status
            $is_disabled = ($disabled === 'true');
            $status = $is_disabled ? 'disabled' : 'active';
            
            if ($is_disabled) {
                $disabled_bindings++;
            } else {
                $active_bindings++;
            }
            
            // Format binding data
            $formatted_binding = [
                'id' => $id,
                'mac_address' => strtoupper($mac_address) ?: 'N/A',
                'address' => $address ?: 'N/A',
                'to_address' => $to_address,
                'server' => $server,
                'type' => $type,
                'status' => $status,
                'disabled' => $is_disabled,
                'comment' => $comment
            ];
            
            $formatted_bindings[] = $formatted_binding;
        }
        
        // Sort bindings by IP address
        usort($formatted_bindings, function($a, $b) {
            return ip2long($a['address']) - ip2long($b['address']);
        });
        
        return [
            'success' => true,
            'router_name' => $router['name'],
            'router_ip' => $router['ip_address'],
            'data' => $formatted_bindings,
            'stats' => [
                'total' => $total_bindings,
                'active' => $active_bindings,
                'disabled' => $disabled_bindings
            ],
            'last_updated' => date('Y-m-d H:i:s')
        ];
        
    } catch (Exception $e) {
        // Return error but with safe fallback data structure
        return [
            'success' => false,
            'error' => 'Error connecting to router: ' . $e->getMessage(),
            'data' => [],
            'router_name' => $router['name'] ?? 'Unknown',
            'router_ip' => $router['ip_address'] ?? '',
            'stats' => [
                'total' => 0,
                'active' => 0,
                'disabled' => 0
            ],
            'last_updated' => date('Y-m-d H:i:s')
        ];
    }
}

function ip_bindings_add($router_id) {
    if (empty($router_id)) {
        return [
            'success' => false,
            'error' => 'No router selected'
        ];
    }
    
    // Get router information
    $router = ORM::for_table('tbl_routers')->find_one($router_id);
    if (!$router) {
        return [
            'success' => false,
            'error' => 'Router not found'
        ];
    }
    
    // Get form data
    $mac_address = _post('mac_address');
    $address = _post('address');
    $to_address = _post('to_address');
    $server = _post('server') ?: 'all';
    $type = _post('type') ?: 'regular';
    $comment = _post('comment');
    $disabled = _post('disabled') === 'true' ? 'true' : 'false';
    
    // Validate required fields
    if (empty($mac_address)) {
        return [
            'success' => false,
            'error' => 'MAC Address is required'
        ];
    }
    
    if (empty($address)) {
        return [
            'success' => false,
            'error' => 'IP Address is required'
        ];
    }
    
    // Validate MAC address format
    if (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac_address)) {
        return [
            'success' => false,
            'error' => 'Invalid MAC address format'
        ];
    }
    
    // Validate IP address format
    if (!filter_var($address, FILTER_VALIDATE_IP)) {
        return [
            'success' => false,
            'error' => 'Invalid IP address format'
        ];
    }
    
    // Validate to_address if provided
    if (!empty($to_address) && !filter_var($to_address, FILTER_VALIDATE_IP)) {
        return [
            'success' => false,
            'error' => 'Invalid To Address IP format'
        ];
    }
    
    try {
        // Connect to MikroTik router
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Prepare the request parameters
        $params = [
            'mac-address' => $mac_address,
            'address' => $address,
            'server' => $server,
            'type' => $type
        ];
        
        // Add optional parameters
        if (!empty($to_address)) {
            $params['to-address'] = $to_address;
        }
        
        if (!empty($comment)) {
            $params['comment'] = $comment;
        }
        
        if ($disabled === 'true') {
            $params['disabled'] = 'true';
        }
        
        // Create the RouterOS request
        $request = new RouterOS\Request('/ip/hotspot/ip-binding/add');
        foreach ($params as $key => $value) {
            $request->setArgument($key, $value);
        }
        
        // Send the request
        $response = $client->sendSync($request);
        
        // Check if the response indicates success
        if ($response->getType() === RouterOS\Response::TYPE_FATAL) {
            return [
                'success' => false,
                'error' => 'MikroTik Error: ' . $response->getProperty('message')
            ];
        }
        
        return [
            'success' => true,
            'message' => 'IP Binding added successfully',
            'data' => [
                'mac_address' => $mac_address,
                'address' => $address,
                'to_address' => $to_address,
                'server' => $server,
                'type' => $type,
                'comment' => $comment,
                'disabled' => $disabled === 'true'
            ]
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Error connecting to router: ' . $e->getMessage()
        ];
    }
}

function ip_bindings_remove($router_id, $binding_id, $mac_address, $address) {
    global $config;
    
    try {
        // Get router details
        $router = ORM::for_table('tbl_routers')->find_one($router_id);
        if (!$router) {
            throw new Exception('Router not found');
        }
        
        // Get MikroTik API client
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Remove the IP binding
        $request = new RouterOS\Request('/ip/hotspot/ip-binding/remove');
        $request->setArgument('numbers', $binding_id);
        $response = $client->sendSync($request);
        
        return [
            'success' => true,
            'message' => 'IP binding removed successfully'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Failed to remove IP binding: ' . $e->getMessage()
        ];
    }
}

function ip_bindings_enable($router_id, $binding_id) {
    global $config;
    
    try {
        // Get router details
        $router = ORM::for_table('tbl_routers')->find_one($router_id);
        if (!$router) {
            throw new Exception('Router not found');
        }
        
        // Get MikroTik API client
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Enable the IP binding
        $request = new RouterOS\Request('/ip/hotspot/ip-binding/enable');
        $request->setArgument('numbers', $binding_id);
        $response = $client->sendSync($request);
        
        return [
            'success' => true,
            'message' => 'IP binding enabled successfully'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Failed to enable IP binding: ' . $e->getMessage()
        ];
    }
}

function ip_bindings_disable($router_id, $binding_id) {
    global $config;
    
    try {
        // Get router details
        $router = ORM::for_table('tbl_routers')->find_one($router_id);
        if (!$router) {
            throw new Exception('Router not found');
        }
        
        // Get MikroTik API client
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Disable the IP binding
        $request = new RouterOS\Request('/ip/hotspot/ip-binding/disable');
        $request->setArgument('numbers', $binding_id);
        $response = $client->sendSync($request);
        
        return [
            'success' => true,
            'message' => 'IP binding disabled successfully'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Failed to disable IP binding: ' . $e->getMessage()
        ];
    }
}

function ip_bindings_edit($router_id, $binding_id) {
    if (empty($router_id)) {
        return [
            'success' => false,
            'error' => 'No router selected'
        ];
    }
    
    if (empty($binding_id)) {
        return [
            'success' => false,
            'error' => 'No binding ID provided'
        ];
    }
    
    // Get router information
    $router = ORM::for_table('tbl_routers')->find_one($router_id);
    if (!$router) {
        return [
            'success' => false,
            'error' => 'Router not found'
        ];
    }
    
    // Get form data
    $mac_address = _post('mac_address');
    $address = _post('address');
    $to_address = _post('to_address');
    $server = _post('server') ?: 'all';
    $type = _post('type') ?: 'regular';
    $comment = _post('comment');
    $disabled = _post('disabled') === 'true' ? 'true' : 'false';
    
    // Validate required fields
    if (empty($mac_address)) {
        return [
            'success' => false,
            'error' => 'MAC Address is required'
        ];
    }
    
    if (empty($address)) {
        return [
            'success' => false,
            'error' => 'IP Address is required'
        ];
    }
    
    // Validate MAC address format
    if (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac_address)) {
        return [
            'success' => false,
            'error' => 'Invalid MAC address format'
        ];
    }
    
    // Validate IP address format
    if (!filter_var($address, FILTER_VALIDATE_IP)) {
        return [
            'success' => false,
            'error' => 'Invalid IP address format'
        ];
    }
    
    // Validate to_address if provided
    if (!empty($to_address) && !filter_var($to_address, FILTER_VALIDATE_IP)) {
        return [
            'success' => false,
            'error' => 'Invalid To Address IP format'
        ];
    }
    
    try {
        // Connect to MikroTik router
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Prepare the request parameters for setting
        $params = [
            'mac-address' => $mac_address,
            'address' => $address,
            'server' => $server,
            'type' => $type
        ];
        
        // Add optional parameters
        if (!empty($to_address)) {
            $params['to-address'] = $to_address;
        }
        
        if (!empty($comment)) {
            $params['comment'] = $comment;
        }
        
        if ($disabled === 'true') {
            $params['disabled'] = 'true';
        }
        
        // Create the RouterOS request to modify the binding
        $request = new RouterOS\Request('/ip/hotspot/ip-binding/set');
        $request->setArgument('numbers', $binding_id);
        
        foreach ($params as $key => $value) {
            $request->setArgument($key, $value);
        }
        
        // Send the request
        $response = $client->sendSync($request);
        
        // Check if the response indicates success
        if ($response->getType() === RouterOS\Response::TYPE_FATAL) {
            return [
                'success' => false,
                'error' => 'MikroTik Error: ' . $response->getProperty('message')
            ];
        }
        
        return [
            'success' => true,
            'message' => 'IP Binding updated successfully',
            'data' => [
                'binding_id' => $binding_id,
                'mac_address' => $mac_address,
                'address' => $address,
                'to_address' => $to_address,
                'server' => $server,
                'type' => $type,
                'comment' => $comment,
                'disabled' => $disabled === 'true'
            ]
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Error connecting to router: ' . $e->getMessage()
        ];
    }
}

function ip_bindings_refresh($router_id) {
    // Get fresh data from router
    $data = ip_bindings_get_data($router_id);
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function ip_bindings_export($router_id) {
    $data = ip_bindings_get_data($router_id);
    
    if (!$data['success']) {
        die('Error: ' . $data['error']);
    }
    
    $router_name = $data['router_name'];
    $bindings = $data['data'];
    
    // Set headers for CSV download
    $filename = 'ip_bindings_' . preg_replace('/[^a-zA-Z0-9]/', '_', $router_name) . '_' . date('Y-m-d_H-i-s') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Write CSV header
    fputcsv($output, [
        'MAC Address',
        'IP Address', 
        'To Address',
        'Server',
        'Type',
        'Status',
        'Comment'
    ]);
    
    // Write data rows
    foreach ($bindings as $binding) {
        fputcsv($output, [
            $binding['mac_address'],
            $binding['address'],
            $binding['to_address'],
            $binding['server'],
            $binding['type'],
            $binding['status'],
            $binding['comment']
        ]);
    }
    
    fclose($output);
    exit;
}
