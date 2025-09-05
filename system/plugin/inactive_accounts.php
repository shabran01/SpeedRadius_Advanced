<?php

/**
 * Inactive Hotspot Accounts Plugin for PHPNuxBill
 * 
 * This plugin identifies and displays inactive HOTSPOT customer accounts based on:
 * - Last login activity
 * - Account creation date (for accounts that never logged in)
 * - Last recharge activity
 * - Account status
 * 
 * NOTE: This plugin only targets customers with service_type = 'Hotspot'
 * 
 * @package PHPNuxBill
 * @author GitHub Copilot
 * @version 1.1.0
 */

// Register menu item in admin panel
register_menu("Inactive Hotspot Accounts", true, "inactive_accounts", 'AFTER_CUSTOMERS', 'ion ion-person-stalker', '', '', ['Admin', 'SuperAdmin']);

// Register plugin hook
register_hook('inactive_accounts', 'inactive_accounts');

function inactive_accounts() {
    global $ui, $config, $_L;
    
    // Check admin authentication
    $admin = Admin::_info();
    if (!$admin) {
        r2(U . 'admin', 'e', $_L['Please login first']);
    }
    
    $action = _post('action');
    
    if ($action == 'get_inactive_accounts') {
        // Handle AJAX request for inactive accounts
        $days_inactive = _post('days_inactive', 30);
        $account_status = _post('account_status', 'all');
        $service_type = _post('service_type', 'all');
        $router_filter = _post('router_filter', 'all');
        
        $result = inactive_accounts_get_data($days_inactive, $account_status, $service_type, $router_filter);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } elseif ($action == 'export_csv') {
        // Handle CSV export
        $days_inactive = _post('days_inactive', 30);
        $account_status = _post('account_status', 'all');
        $service_type = _post('service_type', 'all');
        $router_filter = _post('router_filter', 'all');
        
        inactive_accounts_export_csv($days_inactive, $account_status, $service_type, $router_filter);
        exit;
    } elseif ($action == 'bulk_action') {
        // Handle bulk actions (disable, delete, etc.)
        $bulk_action = _post('bulk_action');
        $selected_ids = _post('selected_ids');
        
        $result = inactive_accounts_bulk_action($bulk_action, $selected_ids);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } else {
        // Display main page
        inactive_accounts_display();
    }
}

function inactive_accounts_display() {
    global $ui, $config;
    
    try {
        // Get all routers for filter dropdown
        $routers = ORM::for_table('tbl_routers')->find_many();
        
        // Get initial data with default settings (30 days) - Hotspot only
        $inactive_data = inactive_accounts_get_data(30, 'all', 'Hotspot', 'all');
        
        // Debug logging
        error_log('Inactive Accounts Plugin - Data loaded: ' . ($inactive_data['success'] ? 'SUCCESS' : 'FAILED'));
        if ($inactive_data['success']) {
            error_log('Inactive Accounts Plugin - Found ' . count($inactive_data['data']) . ' inactive accounts, ' . $inactive_data['stats']['total_customers'] . ' total customers');
        } else {
            error_log('Inactive Accounts Plugin - Error: ' . $inactive_data['error']);
        }
        
        // Assign variables to template
        $ui->assign('routers', $routers);
        $ui->assign('inactive_data', $inactive_data);
        $ui->assign('_title', 'Inactive Hotspot Accounts - Customer Activity Monitor');
        $ui->assign('_system_menu', 'customers');
        
        // Display template
        $ui->display('inactive_accounts.tpl');
        
    } catch (Exception $e) {
        // If there's an error, show a basic error page
        echo '<div class="alert alert-danger">Error loading Inactive Accounts: ' . $e->getMessage() . '</div>';
        error_log('Inactive Accounts Plugin Error: ' . $e->getMessage());
    }
}

function inactive_accounts_get_data($days_inactive = 30, $account_status = 'all', $service_type = 'all', $router_filter = 'all') {
    try {
        // Calculate the cutoff date
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days_inactive} days"));
        
        // First, get all customers with filters - ONLY HOTSPOT CUSTOMERS
        $customers_query = ORM::for_table('tbl_customers')
            ->where('service_type', 'Hotspot'); // Only target Hotspot customers
        
        // Apply account status filter
        if ($account_status != 'all') {
            $customers_query->where('status', $account_status);
        }
        
        // Note: Service type filter removed since we only target Hotspot customers
        
        // Apply router filter
        if ($router_filter != 'all') {
            $router = ORM::for_table('tbl_routers')->find_one($router_filter);
            if ($router) {
                $customers_query->where('router_id', $router_filter);
            }
        }
        
        $customers = $customers_query->find_many();
        
        $inactive_accounts = array();
        $stats = array(
            'total_customers' => count($customers),
            'never_logged_in' => 0,
            'inactive_by_login' => 0,
            'inactive_by_recharge' => 0,
            'completely_inactive' => 0
        );
        
        foreach ($customers as $customer) {
            $is_inactive = false;
            $inactive_reason = array();
            $days_since_login = null;
            $days_since_recharge = null;
            $days_since_created = null;
            
            // Get the latest recharge for this customer
            $latest_recharge = ORM::for_table('tbl_user_recharges')
                ->where('customer_id', $customer->id)
                ->order_by_desc('recharged_on')
                ->find_one();
            
            // Check last login
            if (empty($customer->last_login)) {
                $inactive_reason[] = 'Never logged in';
                $stats['never_logged_in']++;
                $is_inactive = true;
                
                // Calculate days since account creation
                $days_since_created = floor((time() - strtotime($customer->created_at)) / (60 * 60 * 24));
                if ($days_since_created >= $days_inactive) {
                    $inactive_reason[] = "Account created {$days_since_created} days ago";
                }
            } else {
                $last_login_timestamp = strtotime($customer->last_login);
                $days_since_login = floor((time() - $last_login_timestamp) / (60 * 60 * 24));
                
                if ($days_since_login >= $days_inactive) {
                    $inactive_reason[] = "Last login {$days_since_login} days ago";
                    $stats['inactive_by_login']++;
                    $is_inactive = true;
                }
            }
            
            // Check last recharge activity
            if ($latest_recharge) {
                $last_recharge_timestamp = strtotime($latest_recharge->recharged_on);
                $days_since_recharge = floor((time() - $last_recharge_timestamp) / (60 * 60 * 24));
                
                if ($days_since_recharge >= $days_inactive) {
                    if (!in_array("Last recharge {$days_since_recharge} days ago", $inactive_reason)) {
                        $inactive_reason[] = "Last recharge {$days_since_recharge} days ago";
                        $stats['inactive_by_recharge']++;
                    }
                    $is_inactive = true;
                }
            } else {
                $inactive_reason[] = 'No recharge history';
                $is_inactive = true;
            }
            
            // Only include if account meets inactivity criteria
            if ($is_inactive && (empty($customer->last_login) ? $days_since_created >= $days_inactive : $days_since_login >= $days_inactive)) {
                $inactive_accounts[] = array(
                    'id' => $customer->id,
                    'username' => $customer->username,
                    'fullname' => $customer->fullname,
                    'email' => $customer->email,
                    'phonenumber' => $customer->phonenumber,
                    'service_type' => $customer->service_type,
                    'account_type' => $customer->account_type,
                    'status' => $customer->status,
                    'balance' => $customer->balance,
                    'created_at' => $customer->created_at,
                    'last_login' => $customer->last_login,
                    'last_recharge_date' => $latest_recharge ? $latest_recharge->recharged_on : null,
                    'last_expiration' => $latest_recharge ? $latest_recharge->expiration : null,
                    'recharge_status' => $latest_recharge ? $latest_recharge->status : null,
                    'recharge_type' => $latest_recharge ? $latest_recharge->type : null,
                    'last_router' => $latest_recharge ? $latest_recharge->routers : null,
                    'days_since_login' => $days_since_login,
                    'days_since_recharge' => $days_since_recharge,
                    'days_since_created' => $days_since_created,
                    'inactive_reason' => implode(', ', $inactive_reason),
                    'inactive_score' => count($inactive_reason) // Higher score = more inactive
                );
            }
        }
        
        // Sort by inactive score (most inactive first), then by days since last activity
        usort($inactive_accounts, function($a, $b) {
            if ($a['inactive_score'] != $b['inactive_score']) {
                return $b['inactive_score'] - $a['inactive_score'];
            }
            
            $a_days = max($a['days_since_login'] ?? 0, $a['days_since_recharge'] ?? 0, $a['days_since_created'] ?? 0);
            $b_days = max($b['days_since_login'] ?? 0, $b['days_since_recharge'] ?? 0, $b['days_since_created'] ?? 0);
            
            return $b_days - $a_days;
        });
        
        $stats['total_inactive'] = count($inactive_accounts);
        $stats['completely_inactive'] = count(array_filter($inactive_accounts, function($account) {
            return $account['inactive_score'] >= 2;
        }));
        
        return array(
            'success' => true,
            'data' => $inactive_accounts,
            'stats' => $stats,
            'filters' => array(
                'days_inactive' => $days_inactive,
                'account_status' => $account_status,
                'service_type' => $service_type,
                'router_filter' => $router_filter
            ),
            'last_updated' => date('Y-m-d H:i:s')
        );
        
    } catch (Exception $e) {
        return array(
            'success' => false,
            'error' => 'Error fetching inactive accounts: ' . $e->getMessage(),
            'data' => array(),
            'stats' => array()
        );
    }
}

function inactive_accounts_export_csv($days_inactive, $account_status, $service_type, $router_filter) {
    $data = inactive_accounts_get_data($days_inactive, $account_status, $service_type, $router_filter);
    
    if (!$data['success']) {
        die('Error generating CSV: ' . $data['error']);
    }
    
    $filename = 'inactive_accounts_' . date('Y-m-d_H-i-s') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    $output = fopen('php://output', 'w');
    
    // CSV headers
    fputcsv($output, array(
        'ID',
        'Username',
        'Full Name',
        'Email',
        'Phone Number',
        'Service Type',
        'Account Type',
        'Status',
        'Balance',
        'Created Date',
        'Last Login',
        'Days Since Login',
        'Last Recharge',
        'Days Since Recharge',
        'Inactive Reason',
        'Inactive Score'
    ));
    
    // CSV data
    foreach ($data['data'] as $account) {
        fputcsv($output, array(
            $account['id'],
            $account['username'],
            $account['fullname'],
            $account['email'],
            $account['phonenumber'],
            $account['service_type'],
            $account['account_type'],
            $account['status'],
            $account['balance'],
            $account['created_at'],
            $account['last_login'] ?: 'Never',
            $account['days_since_login'] ?: 'N/A',
            $account['last_recharge_date'] ?: 'Never',
            $account['days_since_recharge'] ?: 'N/A',
            $account['inactive_reason'],
            $account['inactive_score']
        ));
    }
    
    fclose($output);
}

function inactive_accounts_bulk_action($action, $selected_ids) {
    try {
        if (empty($selected_ids)) {
            return array('success' => false, 'message' => 'No accounts selected');
        }
        
        $ids_array = explode(',', $selected_ids);
        $ids_array = array_map('intval', $ids_array);
        $ids_array = array_filter($ids_array);
        
        if (empty($ids_array)) {
            return array('success' => false, 'message' => 'Invalid account IDs provided');
        }
        
        $affected_count = 0;
        $errors = array();
        
        foreach ($ids_array as $customer_id) {
            $customer = ORM::for_table('tbl_customers')->find_one($customer_id);
            if (!$customer) {
                $errors[] = "Customer ID {$customer_id} not found";
                continue;
            }
            
            switch ($action) {
                case 'disable':
                    $customer->status = 'Disabled';
                    $customer->save();
                    $affected_count++;
                    break;
                    
                case 'activate':
                    $customer->status = 'Active';
                    $customer->save();
                    $affected_count++;
                    break;
                    
                case 'suspend':
                    $customer->status = 'Suspended';
                    $customer->save();
                    $affected_count++;
                    break;
                    
                case 'delete':
                    // Only allow deletion if user has appropriate permissions
                    $admin = Admin::_info();
                    if ($admin['user_type'] == 'SuperAdmin') {
                        $customer->delete();
                        $affected_count++;
                    } else {
                        $errors[] = "Insufficient permissions to delete customer {$customer->username}";
                    }
                    break;
                    
                default:
                    $errors[] = "Unknown action: {$action}";
            }
        }
        
        $message = "{$affected_count} accounts were processed successfully.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }
        
        return array(
            'success' => $affected_count > 0,
            'message' => $message,
            'affected_count' => $affected_count,
            'errors' => $errors
        );
        
    } catch (Exception $e) {
        return array(
            'success' => false,
            'message' => 'Error processing bulk action: ' . $e->getMessage()
        );
    }
}
