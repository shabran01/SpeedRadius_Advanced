<?php

/**
 * Support Tickets System for ISP Billing
 * Customer support ticket management with priority, status tracking, and assignment
 */

// Register menu item in admin panel
register_menu("Support Tickets", true, "support_tickets", 'SERVICES', 'fa fa-ticket', "New", "success", ['Admin', 'SuperAdmin', 'Sales']);

// Helper function to send ticket SMS
function send_ticket_sms($customer_id, $message) {
    global $config;
    
    // Check if SMS gateway is configured
    if (empty($config['active_sms_gateway'])) {
        _log('SMS disabled - No SMS gateway configured', 'Support Tickets SMS', $customer_id);
        return false;
    }
    
    $customer = ORM::for_table('tbl_customers')->find_one($customer_id);
    if (!$customer) {
        _log('SMS failed - Customer not found: ' . $customer_id, 'Support Tickets SMS', $customer_id);
        return false;
    }
    
    if (empty($customer->phonenumber)) {
        _log('SMS failed - No phone number for customer: ' . $customer->username, 'Support Tickets SMS', $customer_id);
        return false;
    }
    
    $result = Message::sendSMS($customer->phonenumber, $message);
    _log('SMS sent to ' . $customer->phonenumber . ' - Result: ' . ($result ? 'Success' : 'Failed'), 'Support Tickets SMS', $customer_id);
    return $result;
}

// Helper function to send SMS to staff members
function send_staff_sms($staff_username, $message) {
    global $config;
    
    // Check if SMS gateway is configured
    if (empty($config['active_sms_gateway'])) {
        _log('SMS disabled - No SMS gateway configured', 'Support Tickets SMS', 0);
        return false;
    }
    
    $staff = ORM::for_table('tbl_users')->where('username', $staff_username)->find_one();
    if (!$staff) {
        _log('SMS failed - Staff not found: ' . $staff_username, 'Support Tickets SMS', 0);
        return false;
    }
    
    if (empty($staff->phonenumber)) {
        _log('SMS failed - No phone number for staff: ' . $staff_username, 'Support Tickets SMS', 0);
        return false;
    }
    
    $result = Message::sendSMS($staff->phonenumber, $message);
    _log('SMS sent to staff ' . $staff->phonenumber . ' - Result: ' . ($result ? 'Success' : 'Failed'), 'Support Tickets SMS', 0);
    return $result;
}

function support_tickets()
{
    global $ui, $config;
    _admin();
    
    $action = _req('action', 'list');
    
    switch ($action) {
        case 'list':
            tickets_list();
            break;
        case 'view':
            tickets_view();
            break;
        case 'add':
            tickets_add();
            break;
        case 'save':
            tickets_save();
            break;
        case 'reply':
            tickets_reply();
            break;
        case 'close':
            tickets_close();
            break;
        case 'reopen':
            tickets_reopen();
            break;
        case 'assign':
            tickets_assign();
            break;
        case 'delete':
            tickets_delete();
            break;
        case 'categories':
            tickets_categories();
            break;
        case 'save_category':
            tickets_save_category();
            break;
        case 'delete_category':
            tickets_delete_category();
            break;
        case 'dashboard':
            tickets_dashboard();
            break;
        case 'my_tickets':
            tickets_my_tickets();
            break;
        default:
            tickets_list();
            break;
    }
}

function tickets_dashboard()
{
    global $ui, $admin;
    
    tickets_check_tables();
    
    // Get ticket statistics
    $total_tickets = ORM::for_table('tbl_support_tickets')->count();
    $open_tickets = ORM::for_table('tbl_support_tickets')
        ->where_in('status', ['Open', 'In Progress'])
        ->count();
    $closed_tickets = ORM::for_table('tbl_support_tickets')
        ->where('status', 'Closed')
        ->count();
    $my_tickets = ORM::for_table('tbl_support_tickets')
        ->where('assigned_to', $admin['username'])
        ->where_in('status', ['Open', 'In Progress'])
        ->count();
    
    // Priority breakdown
    $high_priority = ORM::for_table('tbl_support_tickets')
        ->where('priority', 'High')
        ->where_not_equal('status', 'Closed')
        ->count();
    $urgent_priority = ORM::for_table('tbl_support_tickets')
        ->where('priority', 'Urgent')
        ->where_not_equal('status', 'Closed')
        ->count();
    
    // Recent tickets
    $recent_tickets = ORM::for_table('tbl_support_tickets')
        ->left_outer_join('tbl_customers', ['tbl_support_tickets.customer_id', '=', 'tbl_customers.id'])
        ->left_outer_join('tbl_support_categories', ['tbl_support_tickets.category_id', '=', 'tbl_support_categories.id'])
        ->select('tbl_support_tickets.*')
        ->select('tbl_customers.username', 'customer_username')
        ->select('tbl_customers.fullname', 'customer_name')
        ->select('tbl_support_categories.name', 'category_name')
        ->order_by_desc('tbl_support_tickets.created_at')
        ->limit(10)
        ->find_many();
    
    // Unassigned tickets
    $unassigned = ORM::for_table('tbl_support_tickets')
        ->where_null('assigned_to')
        ->where_not_equal('status', 'Closed')
        ->count();
    
    // Average response time (in hours)
    $avg_response = ORM::for_table('tbl_support_tickets')
        ->where_not_null('first_response_at')
        ->select_expr('AVG(TIMESTAMPDIFF(HOUR, created_at, first_response_at))', 'avg_hours')
        ->find_one();
    $avg_response_hours = $avg_response ? round($avg_response->avg_hours, 1) : 0;
    
    $ui->assign('total_tickets', $total_tickets);
    $ui->assign('open_tickets', $open_tickets);
    $ui->assign('closed_tickets', $closed_tickets);
    $ui->assign('my_tickets', $my_tickets);
    $ui->assign('high_priority', $high_priority);
    $ui->assign('urgent_priority', $urgent_priority);
    $ui->assign('recent_tickets', $recent_tickets);
    $ui->assign('unassigned', $unassigned);
    $ui->assign('avg_response_hours', $avg_response_hours);
    $ui->assign('_title', 'Support Tickets Dashboard');
    $ui->assign('_system_menu', 'support_tickets');
    $ui->assign('_admin', $admin);
    
    $ui->display('support_tickets_dashboard.tpl');
}

function tickets_list()
{
    global $ui, $admin;
    
    tickets_check_tables();
    
    $status_filter = _post('status');
    $priority_filter = _post('priority');
    $category_filter = _post('category');
    $assigned_filter = _post('assigned');
    $search = _post('search');
    
    $query = ORM::for_table('tbl_support_tickets')
        ->left_outer_join('tbl_customers', ['tbl_support_tickets.customer_id', '=', 'tbl_customers.id'])
        ->left_outer_join('tbl_support_categories', ['tbl_support_tickets.category_id', '=', 'tbl_support_categories.id'])
        ->select('tbl_support_tickets.*')
        ->select('tbl_customers.username', 'customer_username')
        ->select('tbl_customers.fullname', 'customer_name')
        ->select('tbl_customers.phonenumber', 'customer_phone')
        ->select('tbl_support_categories.name', 'category_name');
    
    if (!empty($status_filter)) {
        $query->where('tbl_support_tickets.status', $status_filter);
    }
    
    if (!empty($priority_filter)) {
        $query->where('tbl_support_tickets.priority', $priority_filter);
    }
    
    if (!empty($category_filter)) {
        $query->where('tbl_support_tickets.category_id', $category_filter);
    }
    
    if (!empty($assigned_filter)) {
        if ($assigned_filter == 'me') {
            $query->where('tbl_support_tickets.assigned_to', $admin['username']);
        } elseif ($assigned_filter == 'unassigned') {
            $query->where_null('tbl_support_tickets.assigned_to');
        }
    }
    
    if (!empty($search)) {
        $query->where_raw("(tbl_support_tickets.subject LIKE ? OR tbl_support_tickets.ticket_number LIKE ? OR tbl_customers.username LIKE ?)", 
            ["%$search%", "%$search%", "%$search%"]);
    }
    
    $tickets = $query->order_by_desc('tbl_support_tickets.created_at')->find_many();
    $categories = ORM::for_table('tbl_support_categories')->order_by_asc('name')->find_many();
    
    // Get all admins for assignment
    $admins = ORM::for_table('tbl_users')
        ->where_in('user_type', ['Admin', 'SuperAdmin', 'Sales'])
        ->order_by_asc('username')
        ->find_many();
    
    $ui->assign('tickets', $tickets);
    $ui->assign('categories', $categories);
    $ui->assign('admins', $admins);
    $ui->assign('_title', 'Support Tickets');
    $ui->assign('_system_menu', 'support_tickets');
    $ui->assign('_admin', $admin);
    
    $ui->display('support_tickets_list.tpl');
}

function tickets_view()
{
    global $ui, $admin;
    
    $id = _req('id');
    $ticket = ORM::for_table('tbl_support_tickets')
        ->left_outer_join('tbl_customers', ['tbl_support_tickets.customer_id', '=', 'tbl_customers.id'])
        ->left_outer_join('tbl_support_categories', ['tbl_support_tickets.category_id', '=', 'tbl_support_categories.id'])
        ->select('tbl_support_tickets.*')
        ->select('tbl_customers.username', 'customer_username')
        ->select('tbl_customers.fullname', 'customer_name')
        ->select('tbl_customers.phonenumber', 'customer_phone')
        ->select('tbl_customers.email', 'customer_email')
        ->select('tbl_support_categories.name', 'category_name')
        ->where('tbl_support_tickets.id', $id)
        ->find_one();
    
    if (!$ticket) {
        r2(U . 'plugin/support_tickets', 'e', 'Ticket not found');
    }
    
    // Get ticket replies
    $replies = ORM::for_table('tbl_support_replies')
        ->where('ticket_id', $id)
        ->order_by_asc('created_at')
        ->find_many();
    
    // Get all admins for assignment
    $admins = ORM::for_table('tbl_users')
        ->where_in('user_type', ['Admin', 'SuperAdmin', 'Sales'])
        ->order_by_asc('username')
        ->find_many();
    
    $categories = ORM::for_table('tbl_support_categories')->order_by_asc('name')->find_many();
    
    $ui->assign('ticket', $ticket);
    $ui->assign('replies', $replies);
    $ui->assign('admins', $admins);
    $ui->assign('categories', $categories);
    $ui->assign('_title', 'Ticket #' . $ticket->ticket_number);
    $ui->assign('_system_menu', 'support_tickets');
    $ui->assign('_admin', $admin);
    
    $ui->display('support_tickets_view.tpl');
}

function tickets_add()
{
    global $ui, $admin;
    
    $customers = ORM::for_table('tbl_customers')
        ->where('status', 'Active')
        ->order_by_asc('username')
        ->find_many();
    $categories = ORM::for_table('tbl_support_categories')->order_by_asc('name')->find_many();
    
    $ui->assign('customers', $customers);
    $ui->assign('categories', $categories);
    $ui->assign('_title', 'Create New Ticket');
    $ui->assign('_system_menu', 'support_tickets');
    $ui->assign('_admin', $admin);
    
    $ui->display('support_tickets_add.tpl');
}

function tickets_save()
{
    global $admin;
    
    $customer_id = _post('customer_id');
    $category_id = _post('category_id');
    $subject = _post('subject');
    $priority = _post('priority');
    $description = _post('description');
    
    if (empty($customer_id) || empty($subject) || empty($description)) {
        r2(U . 'plugin/support_tickets&action=add', 'e', 'Customer, subject and description are required');
    }
    
    // Generate ticket number
    $ticket_number = 'TKT-' . date('Ymd') . '-' . rand(1000, 9999);
    
    $ticket = ORM::for_table('tbl_support_tickets')->create();
    $ticket->ticket_number = $ticket_number;
    $ticket->customer_id = $customer_id;
    $ticket->category_id = !empty($category_id) ? $category_id : null;
    $ticket->subject = $subject;
    $ticket->description = $description;
    $ticket->priority = $priority ? $priority : 'Normal';
    $ticket->status = 'Open';
    $ticket->created_by = $admin['username'];
    $ticket->created_at = date('Y-m-d H:i:s');
    $ticket->updated_at = date('Y-m-d H:i:s');
    $ticket->save();
    
    // Send SMS notification to customer
    $sms_message = "Support Ticket Created\n";
    $sms_message .= "Ticket: " . $ticket_number . "\n";
    $sms_message .= "Subject: " . $subject . "\n";
    $sms_message .= "Priority: " . $ticket->priority . "\n";
    $sms_message .= "We will respond shortly.";
    send_ticket_sms($customer_id, $sms_message);
    
    // Log activity
    _log($ticket->id . ' - Ticket created: ' . $subject, 'Support Tickets', $customer_id);
    
    r2(U . 'plugin/support_tickets&action=view&id=' . $ticket->id(), 's', 'Ticket created successfully');
}

function tickets_reply()
{
    global $admin;
    
    $ticket_id = _post('ticket_id');
    $message = _post('message');
    $status = _post('status');
    
    if (empty($message)) {
        r2(U . 'plugin/support_tickets&action=view&id=' . $ticket_id, 'e', 'Reply message is required');
    }
    
    $ticket = ORM::for_table('tbl_support_tickets')->find_one($ticket_id);
    if (!$ticket) {
        r2(U . 'plugin/support_tickets', 'e', 'Ticket not found');
    }
    
    // Add reply
    $reply = ORM::for_table('tbl_support_replies')->create();
    $reply->ticket_id = $ticket_id;
    $reply->message = $message;
    $reply->replied_by = $admin['username'];
    $reply->is_staff = 1;
    $reply->created_at = date('Y-m-d H:i:s');
    $reply->save();
    
    // Update ticket
    if (!$ticket->first_response_at) {
        $ticket->first_response_at = date('Y-m-d H:i:s');
    }
    if (!empty($status)) {
        $ticket->status = $status;
    }
    $ticket->updated_at = date('Y-m-d H:i:s');
    $ticket->save();
    
    // Send SMS notification to customer with actual reply content
    $sms_message = "Ticket Update\n";
    $sms_message .= "Ticket: " . $ticket->ticket_number . "\n";
    $sms_message .= "Status: " . $ticket->status . "\n";
    $sms_message .= "Reply: " . substr($message, 0, 100) . (strlen($message) > 100 ? "..." : "") . "\n";
    $sms_message .= "Login to view full conversation.";
    send_ticket_sms($ticket->customer_id, $sms_message);
    
    // Log activity
    _log($ticket_id . ' - Reply added to ticket', 'Support Tickets', $ticket->customer_id);
    
    r2(U . 'plugin/support_tickets&action=view&id=' . $ticket_id, 's', 'Reply added successfully');
}

function tickets_close()
{
    global $admin;
    
    $id = _req('id');
    $ticket = ORM::for_table('tbl_support_tickets')->find_one($id);
    
    if ($ticket) {
        $ticket->status = 'Closed';
        $ticket->closed_by = $admin['username'];
        $ticket->closed_at = date('Y-m-d H:i:s');
        $ticket->updated_at = date('Y-m-d H:i:s');
        $ticket->save();
        
        // Send SMS notification to customer
        $sms_message = "Ticket Closed\n";
        $sms_message .= "Ticket: " . $ticket->ticket_number . "\n";
        $sms_message .= "Subject: " . $ticket->subject . "\n";
        $sms_message .= "Your ticket has been resolved and closed.\n";
        $sms_message .= "Thank you for contacting us!";
        send_ticket_sms($ticket->customer_id, $sms_message);
        
        _log($id . ' - Ticket closed', 'Support Tickets', $ticket->customer_id);
        
        r2(U . 'plugin/support_tickets&action=view&id=' . $id, 's', 'Ticket closed successfully');
    }
    
    r2(U . 'plugin/support_tickets', 'e', 'Ticket not found');
}

function tickets_reopen()
{
    global $admin;
    
    $id = _req('id');
    $ticket = ORM::for_table('tbl_support_tickets')->find_one($id);
    
    if ($ticket) {
        $ticket->status = 'Open';
        $ticket->closed_by = null;
        $ticket->closed_at = null;
        $ticket->updated_at = date('Y-m-d H:i:s');
        $ticket->save();
        
        // Send SMS notification to customer
        $sms_message = "Ticket Reopened\n";
        $sms_message .= "Ticket: " . $ticket->ticket_number . "\n";
        $sms_message .= "Your ticket has been reopened.\n";
        $sms_message .= "We are looking into this again.";
        send_ticket_sms($ticket->customer_id, $sms_message);
        
        _log($id . ' - Ticket reopened', 'Support Tickets', $ticket->customer_id);
        
        r2(U . 'plugin/support_tickets&action=view&id=' . $id, 's', 'Ticket reopened successfully');
    }
    
    r2(U . 'plugin/support_tickets', 'e', 'Ticket not found');
}

function tickets_assign()
{
    global $admin;
    
    $id = _req('id');
    $assign_to = _req('assign_to');
    
    $ticket = ORM::for_table('tbl_support_tickets')
        ->left_outer_join('tbl_customers', ['tbl_support_tickets.customer_id', '=', 'tbl_customers.id'])
        ->select('tbl_support_tickets.*')
        ->select('tbl_customers.fullname', 'customer_name')
        ->select('tbl_customers.username', 'customer_username')
        ->where('tbl_support_tickets.id', $id)
        ->find_one();
    
    if ($ticket) {
        $ticket->assigned_to = !empty($assign_to) ? $assign_to : null;
        $ticket->updated_at = date('Y-m-d H:i:s');
        $ticket->save();
        
        // Send SMS notification to assigned staff member
        if (!empty($assign_to)) {
            $customer_name = $ticket->customer_name ? $ticket->customer_name : $ticket->customer_username;
            $staff_message = "New Ticket Assignment\n";
            $staff_message .= "Ticket: " . $ticket->ticket_number . "\n";
            $staff_message .= "Subject: " . $ticket->subject . "\n";
            $staff_message .= "Priority: " . $ticket->priority . "\n";
            $staff_message .= "Customer: " . $customer_name . "\n";
            $staff_message .= "Please attend to this ticket.";
            send_staff_sms($assign_to, $staff_message);
        }
        
        _log($id . ' - Ticket assigned to ' . $assign_to, 'Support Tickets', $ticket->customer_id);
        
        r2(U . 'plugin/support_tickets&action=view&id=' . $id, 's', 'Ticket assigned successfully');
    }
    
    r2(U . 'plugin/support_tickets', 'e', 'Ticket not found');
}

function tickets_delete()
{
    $id = _req('id');
    $ticket = ORM::for_table('tbl_support_tickets')->find_one($id);
    
    if ($ticket) {
        // Delete replies first
        ORM::for_table('tbl_support_replies')->where('ticket_id', $id)->delete_many();
        
        $ticket->delete();
        r2(U . 'plugin/support_tickets', 's', 'Ticket deleted successfully');
    }
    
    r2(U . 'plugin/support_tickets', 'e', 'Ticket not found');
}

function tickets_categories()
{
    global $ui, $admin;
    
    $categories = ORM::for_table('tbl_support_categories')->order_by_asc('name')->find_many();
    
    $ui->assign('categories', $categories);
    $ui->assign('_title', 'Ticket Categories');
    $ui->assign('_system_menu', 'support_tickets');
    $ui->assign('_admin', $admin);
    
    $ui->display('support_tickets_categories.tpl');
}

function tickets_save_category()
{
    $id = _post('id');
    $name = _post('name');
    $description = _post('description');
    
    if (empty($name)) {
        r2(U . 'plugin/support_tickets&action=categories', 'e', 'Category name is required');
    }
    
    if ($id) {
        $category = ORM::for_table('tbl_support_categories')->find_one($id);
    } else {
        $category = ORM::for_table('tbl_support_categories')->create();
    }
    
    $category->name = $name;
    $category->description = $description;
    $category->save();
    
    r2(U . 'plugin/support_tickets&action=categories', 's', 'Category saved successfully');
}

function tickets_delete_category()
{
    $id = _post('id');
    $category = ORM::for_table('tbl_support_categories')->find_one($id);
    
    if ($category) {
        $category->delete();
        r2(U . 'plugin/support_tickets&action=categories', 's', 'Category deleted successfully');
    }
    
    r2(U . 'plugin/support_tickets&action=categories', 'e', 'Category not found');
}

function tickets_check_tables()
{
    $db = ORM::get_db();
    
    // Create tickets table
    $db->exec("CREATE TABLE IF NOT EXISTS tbl_support_tickets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ticket_number VARCHAR(50) UNIQUE NOT NULL,
        customer_id INT NOT NULL,
        category_id INT,
        subject VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        priority ENUM('Low', 'Normal', 'High', 'Urgent') DEFAULT 'Normal',
        status ENUM('Open', 'In Progress', 'Pending', 'Closed') DEFAULT 'Open',
        assigned_to VARCHAR(100),
        created_by VARCHAR(100),
        created_at DATETIME,
        updated_at DATETIME,
        first_response_at DATETIME,
        closed_at DATETIME,
        closed_by VARCHAR(100),
        INDEX(ticket_number),
        INDEX(customer_id),
        INDEX(status),
        INDEX(priority),
        INDEX(assigned_to),
        INDEX(created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Create replies table
    $db->exec("CREATE TABLE IF NOT EXISTS tbl_support_replies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ticket_id INT NOT NULL,
        message TEXT NOT NULL,
        replied_by VARCHAR(100),
        is_staff TINYINT(1) DEFAULT 1,
        created_at DATETIME,
        INDEX(ticket_id),
        INDEX(created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Create categories table
    $db->exec("CREATE TABLE IF NOT EXISTS tbl_support_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}
