<?php

/**
 * System Users Plugin for PHPNuxBill
 * 
 * This plugin displays and manages MikroTik System Users
 * Allows viewing, adding, editing, and managing router system users
 * 
 * @package PHPNuxBill
 * @author GitHub Copilot
 * @version 1.0.0
 */

use PEAR2\Net\RouterOS;

// Register menu item in admin panel
register_menu("System Users", true, "system_users", 'AFTER_SETTINGS', 'ion ion-person', '', '', ['Admin', 'SuperAdmin']);

// Register plugin hook
register_hook('system_users', 'system_users');

function system_users() {
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
        $data = system_users_get_data($router_id);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    } elseif ($action == 'export_csv') {
        // Handle CSV export
        $router_id = _post('router_id');
        system_users_export($router_id);
        exit;
    } elseif ($action == 'add_user') {
        // Handle add new user
        $router_id = _post('router_id');
        $result = system_users_add($router_id);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } elseif ($action == 'remove_user') {
        // Handle remove user
        $router_id = _post('router_id');
        $user_id = _post('user_id');
        $username = _post('username');
        $result = system_users_remove($router_id, $user_id, $username);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } elseif ($action == 'enable_user') {
        // Handle enable user
        $router_id = _post('router_id');
        $user_id = _post('user_id');
        $result = system_users_enable($router_id, $user_id);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } elseif ($action == 'disable_user') {
        // Handle disable user
        $router_id = _post('router_id');
        $user_id = _post('user_id');
        $result = system_users_disable($router_id, $user_id);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } elseif ($action == 'edit_user') {
        // Handle edit user
        $router_id = _post('router_id');
        $user_id = _post('user_id');
        $result = system_users_edit($router_id, $user_id);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } elseif ($action == 'get_groups') {
        // Handle get groups
        $router_id = _post('router_id');
        $result = system_users_get_groups($router_id);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } elseif ($action == 'get_active_users') {
        // Handle get active users
        $router_id = _post('router_id');
        $result = system_users_get_active($router_id);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } else {
        // Display main page
        system_users_display();
    }
}

function system_users_display() {
    global $ui, $config;
    
    try {
        // Get all routers
        $routers = ORM::for_table('tbl_routers')->find_many();
        
        // Get default router data
        $default_router = null;
        $users_data = [];
        $groups_data = [];
        
        if (!empty($routers)) {
            $default_router = $routers[0];
            $users_data = system_users_get_data($default_router['id']);
            $groups_data = system_users_get_groups($default_router['id']);
        }
        
        // Assign variables to template
        $ui->assign('routers', $routers);
        $ui->assign('default_router', $default_router);
        $ui->assign('users_data', $users_data);
        $ui->assign('groups_data', $groups_data);
        $ui->assign('_title', 'System Users - MikroTik Router Users Management');
        
        // Display template
        $ui->display('system_users.tpl');
        
    } catch (Exception $e) {
        // If there's an error, show a basic error page
        echo '<div class="alert alert-danger">Error loading System Users: ' . $e->getMessage() . '</div>';
        error_log('System Users Plugin Error: ' . $e->getMessage());
    }
}

function system_users_get_data($router_id) {
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
        
        // Get system users from MikroTik
        $response = $client->sendSync(new RouterOS\Request('/user/print'));
        
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
        $formatted_users = [];
        $total_users = 0;
        $active_users = 0;
        $disabled_users = 0;
        
        foreach ($response as $user) {
            $total_users++;
            
            // Get user properties using getProperty() method
            $id = $user->getProperty('.id') ?? '';
            $name = $user->getProperty('name') ?? '';
            $group = $user->getProperty('group') ?? '';
            $address = $user->getProperty('address') ?? '';
            $last_logged_in = $user->getProperty('last-logged-in') ?? '';
            $disabled = $user->getProperty('disabled') ?? 'false';
            $comment = $user->getProperty('comment') ?? '';
            
            // Determine status
            $is_disabled = ($disabled === 'true');
            $status = $is_disabled ? 'disabled' : 'active';
            
            if ($is_disabled) {
                $disabled_users++;
            } else {
                $active_users++;
            }
            
            // Format user data
            $formatted_user = [
                'id' => $id,
                'name' => $name ?: 'N/A',
                'group' => $group ?: 'full',
                'address' => $address ?: '0.0.0.0/0',
                'last_logged_in' => $last_logged_in ? date('M/d/Y H:i:s', strtotime($last_logged_in)) : 'Never',
                'status' => $status,
                'disabled' => $is_disabled,
                'comment' => $comment
            ];
            
            $formatted_users[] = $formatted_user;
        }
        
        // Sort users by name
        usort($formatted_users, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        
        return [
            'success' => true,
            'router_name' => $router['name'],
            'router_ip' => $router['ip_address'],
            'data' => $formatted_users,
            'stats' => [
                'total' => $total_users,
                'active' => $active_users,
                'disabled' => $disabled_users
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

function system_users_get_groups($router_id) {
    if (empty($router_id)) {
        return [
            'success' => false,
            'error' => 'No router selected',
            'data' => []
        ];
    }
    
    // Get router information
    $router = ORM::for_table('tbl_routers')->find_one($router_id);
    if (!$router) {
        return [
            'success' => false,
            'error' => 'Router not found',
            'data' => []
        ];
    }
    
    try {
        // Connect to MikroTik router
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Get user groups from MikroTik
        $response = $client->sendSync(new RouterOS\Request('/user/group/print'));
        
        $groups = [];
        if (!empty($response)) {
            foreach ($response as $group) {
                $name = $group->getProperty('name') ?? '';
                $policy = $group->getProperty('policy') ?? '';
                
                if (!empty($name)) {
                    $groups[] = [
                        'name' => $name,
                        'policy' => $policy
                    ];
                }
            }
        }
        
        // Add default groups if none found
        if (empty($groups)) {
            $groups = [
                ['name' => 'full', 'policy' => 'local,telnet,ssh,ftp,reboot,read,write,policy,test,winbox,password,web,sniff,sensitive,api,romon,dude,tikapp'],
                ['name' => 'read', 'policy' => 'local,telnet,ssh,ftp,read,winbox,web,api'],
                ['name' => 'write', 'policy' => 'local,telnet,ssh,ftp,read,write,winbox,web,api']
            ];
        }
        
        return [
            'success' => true,
            'data' => $groups
        ];
        
    } catch (Exception $e) {
        // Return default groups on error
        return [
            'success' => false,
            'error' => 'Error connecting to router: ' . $e->getMessage(),
            'data' => [
                ['name' => 'full', 'policy' => 'local,telnet,ssh,ftp,reboot,read,write,policy,test,winbox,password,web,sniff,sensitive,api,romon,dude,tikapp'],
                ['name' => 'read', 'policy' => 'local,telnet,ssh,ftp,read,winbox,web,api'],
                ['name' => 'write', 'policy' => 'local,telnet,ssh,ftp,read,write,winbox,web,api']
            ]
        ];
    }
}

function system_users_get_active($router_id) {
    if (empty($router_id)) {
        return [
            'success' => false,
            'error' => 'No router selected',
            'data' => []
        ];
    }
    
    // Get router information
    $router = ORM::for_table('tbl_routers')->find_one($router_id);
    if (!$router) {
        return [
            'success' => false,
            'error' => 'Router not found',
            'data' => []
        ];
    }
    
    try {
        // Connect to MikroTik router
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Get active users from MikroTik
        $response = $client->sendSync(new RouterOS\Request('/user/active/print'));
        
        $active_users = [];
        if (!empty($response)) {
            foreach ($response as $active_user) {
                $name = $active_user->getProperty('name') ?? '';
                $address = $active_user->getProperty('address') ?? '';
                $via = $active_user->getProperty('via') ?? '';
                $when = $active_user->getProperty('when') ?? '';
                
                $active_users[] = [
                    'name' => $name,
                    'address' => $address,
                    'via' => $via,
                    'when' => $when
                ];
            }
        }
        
        return [
            'success' => true,
            'data' => $active_users
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Error connecting to router: ' . $e->getMessage(),
            'data' => []
        ];
    }
}

function system_users_add($router_id) {
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
    $name = _post('name');
    $group = _post('group') ?: 'read';
    $address = _post('address') ?: '0.0.0.0/0';
    $password = _post('password');
    $comment = _post('comment');
    $disabled = _post('disabled') === 'true' ? 'true' : 'false';
    
    // Validate required fields
    if (empty($name)) {
        return [
            'success' => false,
            'error' => 'Username is required'
        ];
    }
    
    if (empty($password)) {
        return [
            'success' => false,
            'error' => 'Password is required'
        ];
    }
    
    // Validate username format
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
        return [
            'success' => false,
            'error' => 'Username can only contain letters, numbers, underscore and dash'
        ];
    }
    
    // Validate password length
    if (strlen($password) < 3) {
        return [
            'success' => false,
            'error' => 'Password must be at least 3 characters long'
        ];
    }
    
    try {
        // Connect to MikroTik router
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Prepare the request parameters
        $params = [
            'name' => $name,
            'group' => $group,
            'address' => $address,
            'password' => $password
        ];
        
        // Add optional parameters
        if (!empty($comment)) {
            $params['comment'] = $comment;
        }
        
        if ($disabled === 'true') {
            $params['disabled'] = 'true';
        }
        
        // Create the RouterOS request
        $request = new RouterOS\Request('/user/add');
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
            'message' => 'System user added successfully',
            'data' => [
                'name' => $name,
                'group' => $group,
                'address' => $address,
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

function system_users_remove($router_id, $user_id, $username) {
    global $config;
    
    try {
        // Get router details
        $router = ORM::for_table('tbl_routers')->find_one($router_id);
        if (!$router) {
            throw new Exception('Router not found');
        }
        
        // Prevent removal of admin user
        if ($username === 'admin') {
            return [
                'success' => false,
                'error' => 'Cannot remove admin user for security reasons'
            ];
        }
        
        // Get MikroTik API client
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Remove the user
        $request = new RouterOS\Request('/user/remove');
        $request->setArgument('numbers', $user_id);
        $response = $client->sendSync($request);
        
        return [
            'success' => true,
            'message' => 'System user removed successfully'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Failed to remove system user: ' . $e->getMessage()
        ];
    }
}

function system_users_enable($router_id, $user_id) {
    global $config;
    
    try {
        // Get router details
        $router = ORM::for_table('tbl_routers')->find_one($router_id);
        if (!$router) {
            throw new Exception('Router not found');
        }
        
        // Get MikroTik API client
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Enable the user
        $request = new RouterOS\Request('/user/enable');
        $request->setArgument('numbers', $user_id);
        $response = $client->sendSync($request);
        
        return [
            'success' => true,
            'message' => 'System user enabled successfully'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Failed to enable system user: ' . $e->getMessage()
        ];
    }
}

function system_users_disable($router_id, $user_id) {
    global $config;
    
    try {
        // Get router details
        $router = ORM::for_table('tbl_routers')->find_one($router_id);
        if (!$router) {
            throw new Exception('Router not found');
        }
        
        // Get MikroTik API client
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Disable the user
        $request = new RouterOS\Request('/user/disable');
        $request->setArgument('numbers', $user_id);
        $response = $client->sendSync($request);
        
        return [
            'success' => true,
            'message' => 'System user disabled successfully'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Failed to disable system user: ' . $e->getMessage()
        ];
    }
}

function system_users_edit($router_id, $user_id) {
    if (empty($router_id)) {
        return [
            'success' => false,
            'error' => 'No router selected'
        ];
    }
    
    if (empty($user_id)) {
        return [
            'success' => false,
            'error' => 'No user ID provided'
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
    $name = _post('name');
    $group = _post('group') ?: 'read';
    $address = _post('address') ?: '0.0.0.0/0';
    $password = _post('password');
    $comment = _post('comment');
    $disabled = _post('disabled') === 'true' ? 'true' : 'false';
    
    // Validate required fields
    if (empty($name)) {
        return [
            'success' => false,
            'error' => 'Username is required'
        ];
    }
    
    // Validate username format
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
        return [
            'success' => false,
            'error' => 'Username can only contain letters, numbers, underscore and dash'
        ];
    }
    
    // Validate password length if provided
    if (!empty($password) && strlen($password) < 3) {
        return [
            'success' => false,
            'error' => 'Password must be at least 3 characters long'
        ];
    }
    
    try {
        // Connect to MikroTik router
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Prepare the request parameters for setting
        $params = [
            'name' => $name,
            'group' => $group,
            'address' => $address
        ];
        
        // Add optional parameters
        if (!empty($password)) {
            $params['password'] = $password;
        }
        
        if (!empty($comment)) {
            $params['comment'] = $comment;
        }
        
        if ($disabled === 'true') {
            $params['disabled'] = 'true';
        }
        
        // Create the RouterOS request to modify the user
        $request = new RouterOS\Request('/user/set');
        $request->setArgument('numbers', $user_id);
        
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
            'message' => 'System user updated successfully',
            'data' => [
                'user_id' => $user_id,
                'name' => $name,
                'group' => $group,
                'address' => $address,
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

function system_users_export($router_id) {
    $data = system_users_get_data($router_id);
    
    if (!$data['success']) {
        die('Error: ' . $data['error']);
    }
    
    $router_name = $data['router_name'];
    $users = $data['data'];
    
    // Set headers for CSV download
    $filename = 'system_users_' . preg_replace('/[^a-zA-Z0-9]/', '_', $router_name) . '_' . date('Y-m-d_H-i-s') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Write CSV header
    fputcsv($output, [
        'Username',
        'Group',
        'Allowed Address',
        'Last Logged In',
        'Status',
        'Comment'
    ]);
    
    // Write data rows
    foreach ($users as $user) {
        fputcsv($output, [
            $user['name'],
            $user['group'],
            $user['address'],
            $user['last_logged_in'],
            $user['status'],
            $user['comment']
        ]);
    }
    
    fclose($output);
    exit;
}
