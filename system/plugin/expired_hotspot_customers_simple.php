<?php

/**
 * Simplified Expired Hotspot Customers Plugin for PHPNuxBill
 * 
 * This version uses direct POST handling instead of complex routing
 * 
 * @package PHPNuxBill
 * @author GitHub Copilot
 * @version 1.1.0
 */

// Register menu item in admin panel
register_menu("Expired Hotspot Customers", true, "expired_hotspot_customers_simple", 'AFTER_CUSTOMERS', 'ion ion-clock', '', '', ['Admin', 'SuperAdmin']);

// Register plugin hook
register_hook('expired_hotspot_customers_simple', 'expired_hotspot_customers_simple');

function expired_hotspot_customers_simple() {
    global $ui, $config, $_L;
    
    // Check admin authentication
    $admin = Admin::_info();
    if (!$admin) {
        r2(U . 'admin', 'e', $_L['Please login first']);
    }
    
    // Handle AJAX bulk delete request
    if (isset($_POST['bulk_delete_action']) && $_POST['bulk_delete_action'] == 'delete_selected') {
        header('Content-Type: application/json');
        
        $selected_ids = isset($_POST['selected_ids']) ? $_POST['selected_ids'] : '';
        
        if (empty($selected_ids)) {
            echo json_encode(['success' => false, 'message' => 'No customers selected for deletion']);
            exit;
        }
        
        // Convert comma-separated IDs to array
        $ids_array = explode(',', $selected_ids);
        $ids_array = array_map('intval', $ids_array);
        $ids_array = array_filter($ids_array);
        
        if (empty($ids_array)) {
            echo json_encode(['success' => false, 'message' => 'Invalid customer IDs provided']);
            exit;
        }
        
        $deleted_count = 0;
        $errors = array();
        $processed_users = array();
        
        // Add small delay to show progress (remove this in production if not needed)
        usleep(100000); // 0.1 second delay
        
        try {
            $total_items = count($ids_array);
            $current_item = 0;
            
            foreach ($ids_array as $recharge_id) {
                $current_item++;
                
                // Get the recharge record to verify it's an expired hotspot customer
                $recharge = ORM::for_table('tbl_user_recharges')->find_one($recharge_id);
                
                if ($recharge && $recharge->type == 'Hotspot' && $recharge->status == 'off') {
                    $customer_id = $recharge->customer_id;
                    $username = $recharge->username;
                    $expiration = $recharge->expiration;
                    
                    // Delete the recharge record
                    if ($recharge->delete()) {
                        $deleted_count++;
                        $processed_users[] = [
                            'username' => $username,
                            'expiration' => $expiration,
                            'status' => 'deleted'
                        ];
                        
                        // Log the deletion
                        $log_message = "Expired hotspot customer deleted: Username=$username, Customer_ID=$customer_id, Recharge_ID=$recharge_id by admin: " . $admin['username'];
                        error_log($log_message);
                    } else {
                        $errors[] = "Failed to delete record for username: $username";
                        $processed_users[] = [
                            'username' => $username,
                            'expiration' => $expiration,
                            'status' => 'failed'
                        ];
                    }
                    
                } else {
                    $errors[] = "Invalid recharge ID: $recharge_id (not an expired hotspot customer)";
                }
                
                // Small processing delay to show progress
                if ($total_items > 10) {
                    usleep(50000); // 0.05 second delay for larger batches
                }
            }
            
            $response = [
                'success' => true,
                'message' => "Successfully deleted $deleted_count out of " . count($ids_array) . " expired hotspot customer records",
                'deleted_count' => $deleted_count,
                'total_requested' => count($ids_array),
                'processed_users' => $processed_users,
                'execution_time' => round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 2) . 'ms'
            ];
            
            if (!empty($errors)) {
                $response['warnings'] = $errors;
            }
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error during bulk deletion: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    // Display main page
    $ui->assign('_title', 'Expired Hotspot Customers');
    $ui->assign('_system_menu', 'customers');
    
    // Get current page from URL parameter
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max(1, $page);
    
    $per_page = 200;
    $offset = ($page - 1) * $per_page;
    
    // Get expired hotspot customers
    $current_date = date('Y-m-d');
    
    // Build query for expired hotspot customers (using LEFT JOIN to handle orphaned records)
    $query = ORM::for_table('tbl_user_recharges')
        ->table_alias('ur')
        ->select_many(
            'ur.id',
            'ur.customer_id',
            'ur.username',
            'ur.namebp',
            'ur.recharged_on',
            'ur.expiration',
            'ur.routers',
            'ur.status',
            'c.fullname',
            'c.phonenumber',
            'c.email'
        )
        ->left_outer_join('tbl_customers', array('ur.customer_id', '=', 'c.id'), 'c')
        ->where('ur.type', 'Hotspot')
        ->where('ur.status', 'off')
        ->where_lte('ur.expiration', $current_date)
        ->order_by_desc('ur.expiration')
        ->limit($per_page)
        ->offset($offset);
    
    $expired_customers = $query->find_many();
    
    // Get total count for pagination
    $total_count = ORM::for_table('tbl_user_recharges')
        ->where('type', 'Hotspot')
        ->where('status', 'off')
        ->where_lte('expiration', $current_date)
        ->count();
    
    $total_pages = ceil($total_count / $per_page);
    
    // Prepare data for template
    $customers_data = array();
    foreach ($expired_customers as $customer) {
        $customers_data[] = array(
            'id' => $customer->id,
            'customer_id' => $customer->customer_id,
            'username' => $customer->username,
            'fullname' => $customer->fullname ? $customer->fullname : 'Customer Deleted',
            'plan_name' => $customer->namebp,
            'recharged_on' => $customer->recharged_on,
            'expiration' => $customer->expiration,
            'router' => $customer->routers,
            'phone' => $customer->phonenumber ? $customer->phonenumber : 'N/A',
            'email' => $customer->email ? $customer->email : 'N/A',
            'days_expired' => floor((strtotime($current_date) - strtotime($customer->expiration)) / 86400),
            'is_orphaned' => $customer->fullname ? false : true
        );
    }
    
    $ui->assign('customers', $customers_data);
    $ui->assign('total_count', $total_count);
    $ui->assign('current_page', $page);
    $ui->assign('total_pages', $total_pages);
    $ui->assign('per_page', $per_page);
    $ui->assign('has_prev', $page > 1);
    $ui->assign('has_next', $page < $total_pages);
    $ui->assign('prev_page', $page - 1);
    $ui->assign('next_page', $page + 1);
    
    // Calculate pagination range
    $start_page = max(1, $page - 5);
    $end_page = min($total_pages, $page + 5);
    $pages = range($start_page, $end_page);
    $ui->assign('pages', $pages);
    
    $ui->display('expired_hotspot_customers_simple.tpl');
}

?>
