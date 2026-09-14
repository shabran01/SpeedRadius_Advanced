<?php

/**
 * DHCP Server Leases Plugin for MikroTik Billing System
 * Displays DHCP lease information from MikroTik routers
 */

use PEAR2\Net\RouterOS;

// Register menu item in admin panel
register_menu("DHCP Leases", true, "dhcp_leases", 'AFTER_SETTINGS', 'ion ion-network', "New", "red", ['Admin', 'SuperAdmin']);

function dhcp_leases()
{
    global $ui, $routes;
    _admin();
    
    $ui->assign('_title', 'DHCP Server Leases');
    $ui->assign('_system_menu', 'dhcp_leases');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    
    // Get all enabled routers
    $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
    $ui->assign('routers', $routers);
    
    // Get selected router (default to first router if none selected)
    $selected_router = $routes['2'] ?? ($routers[0]['id'] ?? null);
    $ui->assign('selected_router', $selected_router);
    
    // Get DHCP leases if router is selected
    $dhcp_leases = [];
    $error_message = '';
    
    if (!empty($selected_router)) {
        try {
            $dhcp_leases = dhcp_leases_get_data($selected_router);
        } catch (Exception $e) {
            $error_message = 'Error connecting to router: ' . $e->getMessage();
        }
    }
    
    $ui->assign('dhcp_leases', $dhcp_leases);
    $ui->assign('error_message', $error_message);
    $ui->display('dhcp_leases.tpl');
}

function dhcp_leases_refresh()
{
    global $routes;
    _admin();
    
    $router_id = $routes['2'] ?? _post('router_id');
    
    if (empty($router_id)) {
        showResult(false, 'Router ID is required');
        return;
    }
    
    try {
        $dhcp_leases = dhcp_leases_get_data($router_id);
        showResult(true, 'DHCP leases refreshed successfully', $dhcp_leases);
    } catch (Exception $e) {
        showResult(false, 'Error: ' . $e->getMessage());
    }
}

function dhcp_leases_get_data($router_id)
{
    // Get router details
    $router = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router_id);
    
    if (!$router) {
        throw new Exception('Router not found or disabled');
    }
    
    // Connect to MikroTik
    try {
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Get DHCP server leases
        $leases = $client->sendSync(new RouterOS\Request('/ip/dhcp-server/lease/print'));
        
        $lease_list = [];
        
        foreach ($leases as $lease) {
            $lease_id = $lease->getProperty('.id') ?? '';
            $address = $lease->getProperty('address') ?? '';
            $mac_address = $lease->getProperty('mac-address') ?? '';
            $client_id = $lease->getProperty('client-id') ?? '';
            $server = $lease->getProperty('server') ?? '';
            $status = $lease->getProperty('status') ?? '';
            $expires_after = $lease->getProperty('expires-after') ?? '';
            $last_seen = $lease->getProperty('last-seen') ?? '';
            $active_address = $lease->getProperty('active-address') ?? '';
            $active_mac_address = $lease->getProperty('active-mac-address') ?? '';
            $active_client_id = $lease->getProperty('active-client-id') ?? '';
            $active_server = $lease->getProperty('active-server') ?? '';
            $host_name = $lease->getProperty('host-name') ?? '';
            $radius = $lease->getProperty('radius') ?? '';
            $disabled = $lease->getProperty('disabled') ?? 'false';
            $dynamic = $lease->getProperty('dynamic') ?? 'false';
            $blocked = $lease->getProperty('blocked') ?? 'false';
            
            // Format expires-after time
            $expires_formatted = dhcp_leases_format_time($expires_after);
            $last_seen_formatted = dhcp_leases_format_time($last_seen);
            
            // Determine lease type
            $lease_type = 'static';
            if ($dynamic == 'true') {
                $lease_type = 'dynamic';
            }
            
            // Determine status with color coding
            $status_class = 'info';
            $status_display = $status;
            
            switch ($status) {
                case 'bound':
                    $status_class = 'success';
                    break;
                case 'waiting':
                    $status_class = 'warning';
                    break;
                case 'offered':
                    $status_class = 'info';
                    break;
                default:
                    $status_class = 'default';
            }
            
            if ($disabled == 'true') {
                $status_display = 'disabled';
                $status_class = 'danger';
            }
            
            if ($blocked == 'true') {
                $status_display = 'blocked';
                $status_class = 'danger';
            }
            
            $lease_list[] = [
                'lease_id' => $lease_id,
                'address' => $address,
                'mac_address' => $mac_address,
                'client_id' => $client_id,
                'server' => $server,
                'status' => $status_display,
                'status_class' => $status_class,
                'expires_after' => $expires_formatted,
                'last_seen' => $last_seen_formatted,
                'active_address' => $active_address,
                'active_mac_address' => $active_mac_address,
                'active_client_id' => $active_client_id,
                'active_server' => $active_server,
                'host_name' => $host_name,
                'radius' => $radius,
                'lease_type' => $lease_type,
                'disabled' => $disabled,
                'dynamic' => $dynamic,
                'blocked' => $blocked,
                'raw_expires' => $expires_after,
                'raw_last_seen' => $last_seen
            ];
        }
        
        // Sort by IP address
        usort($lease_list, function($a, $b) {
            return ip2long($a['address']) - ip2long($b['address']);
        });
        
        return $lease_list;
        
    } catch (Exception $e) {
        throw new Exception('Failed to connect to MikroTik router: ' . $e->getMessage());
    }
}

function dhcp_leases_format_time($time_string)
{
    if (empty($time_string) || $time_string == 'never') {
        return 'never';
    }
    
    // Handle MikroTik time format (e.g., "1w2d3h4m5s")
    if (preg_match('/(\d+w)?(\d+d)?(\d+h)?(\d+m)?(\d+s)?/', $time_string, $matches)) {
        $formatted_parts = [];
        
        if (!empty($matches[1])) {
            $formatted_parts[] = $matches[1];
        }
        if (!empty($matches[2])) {
            $formatted_parts[] = $matches[2];
        }
        if (!empty($matches[3])) {
            $formatted_parts[] = $matches[3];
        }
        if (!empty($matches[4])) {
            $formatted_parts[] = $matches[4];
        }
        if (!empty($matches[5])) {
            $formatted_parts[] = $matches[5];
        }
        
        return implode(' ', $formatted_parts);
    }
    
    return $time_string;
}

function dhcp_leases_make_static()
{
    global $routes;
    _admin();
    
    $router_id = $routes['2'] ?? _post('router_id');
    $ip_address = $routes['3'] ?? _post('ip_address');
    $mac_address = $routes['4'] ?? _post('mac_address');
    
    if (empty($router_id) || empty($ip_address) || empty($mac_address)) {
        showResult(false, 'Router ID, IP address, and MAC address are required');
        return;
    }
    
    try {
        // Get router details
        $router = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router_id);
        
        if (!$router) {
            showResult(false, 'Router not found or disabled');
            return;
        }
        
        // Connect to MikroTik
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // First, check if a static lease already exists for this IP or MAC
        $existingLeases = $client->sendSync(new RouterOS\Request('/ip/dhcp-server/lease/print'));
        
        foreach ($existingLeases as $lease) {
            $leaseAddress = $lease->getProperty('address');
            $leaseMac = $lease->getProperty('mac-address');
            $dynamic = $lease->getProperty('dynamic');
            
            if (($leaseAddress == $ip_address || $leaseMac == $mac_address) && $dynamic == 'false') {
                showResult(false, 'A static lease already exists for this IP address or MAC address');
                return;
            }
        }
        
        // Create a new static DHCP lease
        $addRequest = new RouterOS\Request('/ip/dhcp-server/lease/add');
        $addRequest->setArgument('address', $ip_address);
        $addRequest->setArgument('mac-address', $mac_address);
        $addRequest->setArgument('comment', 'Created via DHCP Leases Plugin on ' . date('Y-m-d H:i:s'));
        
        $client->sendSync($addRequest);
        
        showResult(true, 'Static DHCP lease created successfully for ' . $ip_address . ' (' . $mac_address . ')');
        
    } catch (Exception $e) {
        showResult(false, 'Failed to create static lease: ' . $e->getMessage());
    }
}

function dhcp_leases_remove()
{
    global $routes;
    _admin();
    
    $router_id = $routes['2'] ?? _post('router_id');
    $ip_address = $routes['3'] ?? _post('ip_address');
    $mac_address = $routes['4'] ?? _post('mac_address');
    
    if (empty($router_id) || empty($ip_address)) {
        showResult(false, 'Router ID and IP address are required');
        return;
    }
    
    try {
        // Get router details
        $router = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router_id);
        
        if (!$router) {
            showResult(false, 'Router not found or disabled');
            return;
        }
        
        // Connect to MikroTik
        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        
        // Find the lease to remove
        $leases = $client->sendSync(new RouterOS\Request('/ip/dhcp-server/lease/print'));
        $leaseId = null;
        
        foreach ($leases as $lease) {
            $leaseAddress = $lease->getProperty('address');
            $leaseMac = $lease->getProperty('mac-address');
            $dynamic = $lease->getProperty('dynamic');
            
            // Match by IP address and optionally MAC address for static leases
            if ($leaseAddress == $ip_address) {
                if ($dynamic == 'false' || ($mac_address && $leaseMac == $mac_address)) {
                    $leaseId = $lease->getProperty('.id');
                    break;
                }
            }
        }
        
        if (!$leaseId) {
            showResult(false, 'DHCP lease not found for IP address: ' . $ip_address);
            return;
        }
        
        // Remove the lease
        $removeRequest = new RouterOS\Request('/ip/dhcp-server/lease/remove');
        $removeRequest->setArgument('.id', $leaseId);
        
        $client->sendSync($removeRequest);
        
        showResult(true, 'DHCP lease removed successfully for ' . $ip_address);
        
    } catch (Exception $e) {
        showResult(false, 'Failed to remove lease: ' . $e->getMessage());
    }
}

function dhcp_leases_export()
{
    global $routes;
    _admin();
    
    $router_id = $routes['2'] ?? _post('router_id');
    
    if (empty($router_id)) {
        r2(U . 'plugin/dhcp_leases', 'e', 'Router ID is required');
        return;
    }
    
    try {
        $dhcp_leases = dhcp_leases_get_data($router_id);
        $router = ORM::for_table('tbl_routers')->find_one($router_id);
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="dhcp_leases_' . $router['name'] . '_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'IP Address',
            'MAC Address', 
            'Client ID',
            'Server',
            'Status',
            'Expires After',
            'Last Seen',
            'Host Name',
            'Type',
            'Active Address',
            'Active MAC',
            'Radius'
        ]);
        
        // CSV data
        foreach ($dhcp_leases as $lease) {
            fputcsv($output, [
                $lease['address'],
                $lease['mac_address'],
                $lease['client_id'],
                $lease['server'],
                $lease['status'],
                $lease['expires_after'],
                $lease['last_seen'],
                $lease['host_name'],
                $lease['lease_type'],
                $lease['active_address'],
                $lease['active_mac_address'],
                $lease['radius']
            ]);
        }
        
        fclose($output);
        exit;
        
    } catch (Exception $e) {
        r2(U . 'plugin/dhcp_leases', 'e', 'Export failed: ' . $e->getMessage());
    }
}
