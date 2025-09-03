<?php

/**
 * Expenditure Management Plugin for ISP Billing System
 * Tracks business expenses, operational costs, and financial management
 */

// Register menu item in admin panel under Reports
register_menu("Expenditure", true, "expenditure", 'REPORTS', 'glyphicon glyphicon-usd', "New", "orange", ['Admin', 'SuperAdmin']);

function expenditure()
{
    global $ui, $config;
    _admin();
    
    $action = _req('action', 'dashboard');
    
    switch ($action) {
        case 'dashboard':
            expenditure_dashboard();
            break;
        case 'add':
            expenditure_add();
            break;
        case 'edit':
            expenditure_edit();
            break;
        case 'delete':
            expenditure_delete();
            break;
        case 'list':
            expenditure_list();
            break;
        case 'categories':
            expenditure_categories();
            break;
        case 'reports':
            expenditure_reports();
            break;
        case 'export':
            expenditure_export();
            break;
        case 'save':
            expenditure_save();
            break;
        case 'save_category':
            expenditure_save_category();
            break;
        case 'delete_category':
            expenditure_delete_category();
            break;
        default:
            expenditure_dashboard();
            break;
    }
}

function expenditure_dashboard()
{
    global $ui;
    
    $today = date('Y-m-d');
    $thisMonth = date('Y-m');
    $lastMonth = date('Y-m', strtotime('-1 month'));
    $thisYear = date('Y');
    
    // Get expenditure statistics
    $todayExpenses = ORM::for_table('tbl_expenditures')
        ->where('expense_date', $today)
        ->sum('amount');
    
    $thisMonthExpenses = ORM::for_table('tbl_expenditures')
        ->where_like('expense_date', $thisMonth . '%')
        ->sum('amount');
    
    $lastMonthExpenses = ORM::for_table('tbl_expenditures')
        ->where_like('expense_date', $lastMonth . '%')
        ->sum('amount');
    
    $thisYearExpenses = ORM::for_table('tbl_expenditures')
        ->where_like('expense_date', $thisYear . '%')
        ->sum('amount');
    
    // Get recent expenses
    $recentExpenses = ORM::for_table('tbl_expenditures')
        ->left_outer_join('tbl_expenditure_categories', ['tbl_expenditures.category_id', '=', 'tbl_expenditure_categories.id'])
        ->select('tbl_expenditures.*')
        ->select('tbl_expenditure_categories.name', 'category_name')
        ->order_by_desc('expense_date')
        ->limit(10)
        ->find_many();
    
    // Get top categories this month
    $topCategories = ORM::for_table('tbl_expenditures')
        ->left_outer_join('tbl_expenditure_categories', ['tbl_expenditures.category_id', '=', 'tbl_expenditure_categories.id'])
        ->select('tbl_expenditure_categories.name', 'category_name')
        ->select_expr('SUM(tbl_expenditures.amount)', 'total_amount')
        ->where_like('tbl_expenditures.expense_date', $thisMonth . '%')
        ->group_by('tbl_expenditures.category_id')
        ->order_by_desc('total_amount')
        ->limit(5)
        ->find_many();
    
    $ui->assign('todayExpenses', $todayExpenses ?: 0);
    $ui->assign('thisMonthExpenses', $thisMonthExpenses ?: 0);
    $ui->assign('lastMonthExpenses', $lastMonthExpenses ?: 0);
    $ui->assign('thisYearExpenses', $thisYearExpenses ?: 0);
    $ui->assign('recentExpenses', $recentExpenses);
    $ui->assign('topCategories', $topCategories);
    
    $ui->display('expenditure_dashboard.tpl');
}

function expenditure_add()
{
    global $ui;
    
    $categories = ORM::for_table('tbl_expenditure_categories')
        ->order_by_asc('name')
        ->find_many();
    
    $ui->assign('categories', $categories);
    $ui->assign('_title', 'Add Expenditure');
    $ui->display('expenditure_add.tpl');
}

function expenditure_edit()
{
    global $ui;
    
    $id = _req('id');
    $expense = ORM::for_table('tbl_expenditures')->find_one($id);
    
    if (!$expense) {
        r2(U . 'plugin/expenditure', 'e', 'Expenditure not found');
    }
    
    $categories = ORM::for_table('tbl_expenditure_categories')
        ->order_by_asc('name')
        ->find_many();
    
    $ui->assign('expense', $expense);
    $ui->assign('categories', $categories);
    $ui->assign('_title', 'Edit Expenditure');
    $ui->display('expenditure_edit.tpl');
}

function expenditure_save()
{
    global $admin;
    
    $id = _req('id');
    $description = _req('description');
    $amount = _req('amount');
    $category_id = _req('category_id');
    $expense_date = _req('expense_date');
    $receipt_number = _req('receipt_number');
    $vendor = _req('vendor');
    $notes = _req('notes');
    
    // Validation
    if (empty($description)) {
        r2(U . 'plugin/expenditure&action=add', 'e', 'Description is required');
    }
    
    if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
        r2(U . 'plugin/expenditure&action=add', 'e', 'Valid amount is required');
    }
    
    if (empty($expense_date)) {
        r2(U . 'plugin/expenditure&action=add', 'e', 'Expense date is required');
    }
    
    if ($id) {
        // Update existing
        $expense = ORM::for_table('tbl_expenditures')->find_one($id);
        if (!$expense) {
            r2(U . 'plugin/expenditure', 'e', 'Expenditure not found');
        }
    } else {
        // Create new
        $expense = ORM::for_table('tbl_expenditures')->create();
        $expense->created_by = $admin['id'];
        $expense->created_at = date('Y-m-d H:i:s');
    }
    
    $expense->description = $description;
    $expense->amount = $amount;
    $expense->category_id = $category_id ?: null;
    $expense->expense_date = $expense_date;
    $expense->receipt_number = $receipt_number;
    $expense->vendor = $vendor;
    $expense->notes = $notes;
    $expense->updated_at = date('Y-m-d H:i:s');
    
    $expense->save();
    
    r2(U . 'plugin/expenditure', 's', 'Expenditure saved successfully');
}

function expenditure_delete()
{
    $id = _req('id');
    $expense = ORM::for_table('tbl_expenditures')->find_one($id);
    
    if ($expense) {
        $expense->delete();
        r2(U . 'plugin/expenditure', 's', 'Expenditure deleted successfully');
    } else {
        r2(U . 'plugin/expenditure', 'e', 'Expenditure not found');
    }
}

function expenditure_list()
{
    global $ui;
    
    $search = _req('search');
    $category = _req('category');
    $date_from = _req('date_from');
    $date_to = _req('date_to');
    
    $query = ORM::for_table('tbl_expenditures')
        ->left_outer_join('tbl_expenditure_categories', ['tbl_expenditures.category_id', '=', 'tbl_expenditure_categories.id'])
        ->select('tbl_expenditures.*')
        ->select('tbl_expenditure_categories.name', 'category_name');
    
    if ($search) {
        $query->where_raw("(tbl_expenditures.description LIKE ? OR tbl_expenditures.vendor LIKE ? OR tbl_expenditures.receipt_number LIKE ?)", 
            ["%$search%", "%$search%", "%$search%"]);
    }
    
    if ($category) {
        $query->where('tbl_expenditures.category_id', $category);
    }
    
    if ($date_from) {
        $query->where_gte('tbl_expenditures.expense_date', $date_from);
    }
    
    if ($date_to) {
        $query->where_lte('tbl_expenditures.expense_date', $date_to);
    }
    
    $expenses = $query->order_by_desc('expense_date')->find_many();
    
    $categories = ORM::for_table('tbl_expenditure_categories')
        ->order_by_asc('name')
        ->find_many();
    
    $ui->assign('expenses', $expenses);
    $ui->assign('categories', $categories);
    $ui->assign('search', $search);
    $ui->assign('category', $category);
    $ui->assign('date_from', $date_from);
    $ui->assign('date_to', $date_to);
    $ui->assign('_title', 'Expenditure List');
    $ui->display('expenditure_list.tpl');
}

function expenditure_categories()
{
    global $ui;
    
    $categories = ORM::for_table('tbl_expenditure_categories')
        ->order_by_asc('name')
        ->find_many();
    
    $ui->assign('categories', $categories);
    $ui->assign('_title', 'Expenditure Categories');
    $ui->display('expenditure_categories.tpl');
}

function expenditure_save_category()
{
    $id = _req('id');
    $name = _req('name');
    $description = _req('description');
    
    if (empty($name)) {
        r2(U . 'plugin/expenditure&action=categories', 'e', 'Category name is required');
    }
    
    // Check for duplicate
    $existing = ORM::for_table('tbl_expenditure_categories')
        ->where('name', $name);
    
    if ($id) {
        $existing->where_not_equal('id', $id);
    }
    
    if ($existing->find_one()) {
        r2(U . 'plugin/expenditure&action=categories', 'e', 'Category name already exists');
    }
    
    if ($id) {
        $category = ORM::for_table('tbl_expenditure_categories')->find_one($id);
        if (!$category) {
            r2(U . 'plugin/expenditure&action=categories', 'e', 'Category not found');
        }
    } else {
        $category = ORM::for_table('tbl_expenditure_categories')->create();
    }
    
    $category->name = $name;
    $category->description = $description;
    $category->save();
    
    r2(U . 'plugin/expenditure&action=categories', 's', 'Category saved successfully');
}

function expenditure_delete_category()
{
    $id = _req('id');
    
    // Check if category is in use
    $inUse = ORM::for_table('tbl_expenditures')
        ->where('category_id', $id)
        ->find_one();
    
    if ($inUse) {
        r2(U . 'plugin/expenditure&action=categories', 'e', 'Cannot delete category that is in use');
    }
    
    $category = ORM::for_table('tbl_expenditure_categories')->find_one($id);
    if ($category) {
        $category->delete();
        r2(U . 'plugin/expenditure&action=categories', 's', 'Category deleted successfully');
    } else {
        r2(U . 'plugin/expenditure&action=categories', 'e', 'Category not found');
    }
}

function expenditure_reports()
{
    global $ui;
    
    $report_type = _req('report_type', 'monthly');
    $year = _req('year', date('Y'));
    $month = _req('month', date('m'));
    
    $data = [];
    
    switch ($report_type) {
        case 'monthly':
            // Monthly breakdown for the year
            for ($i = 1; $i <= 12; $i++) {
                $monthStr = sprintf('%04d-%02d', $year, $i);
                $amount = ORM::for_table('tbl_expenditures')
                    ->where_like('expense_date', $monthStr . '%')
                    ->sum('amount');
                $data[] = [
                    'period' => date('F', mktime(0, 0, 0, $i, 1)),
                    'amount' => $amount ?: 0
                ];
            }
            break;
            
        case 'category':
            // Category breakdown for the selected month
            $monthStr = sprintf('%04d-%02d', $year, $month);
            $data = ORM::for_table('tbl_expenditures')
                ->left_outer_join('tbl_expenditure_categories', ['tbl_expenditures.category_id', '=', 'tbl_expenditure_categories.id'])
                ->select('tbl_expenditure_categories.name', 'category_name')
                ->select_expr('SUM(tbl_expenditures.amount)', 'amount')
                ->where_like('tbl_expenditures.expense_date', $monthStr . '%')
                ->group_by('tbl_expenditures.category_id')
                ->order_by_desc('amount')
                ->find_array();
            break;
            
        case 'daily':
            // Daily breakdown for the selected month
            $monthStr = sprintf('%04d-%02d', $year, $month);
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $i);
                $amount = ORM::for_table('tbl_expenditures')
                    ->where('expense_date', $dateStr)
                    ->sum('amount');
                $data[] = [
                    'period' => $i,
                    'amount' => $amount ?: 0
                ];
            }
            break;
    }
    
    $ui->assign('report_type', $report_type);
    $ui->assign('year', $year);
    $ui->assign('month', $month);
    $ui->assign('data', $data);
    $ui->assign('_title', 'Expenditure Reports');
    $ui->display('expenditure_reports.tpl');
}

function expenditure_export()
{
    $format = _req('format', 'csv');
    $date_from = _req('date_from');
    $date_to = _req('date_to');
    $category = _req('category');
    
    $query = ORM::for_table('tbl_expenditures')
        ->left_outer_join('tbl_expenditure_categories', ['tbl_expenditures.category_id', '=', 'tbl_expenditure_categories.id'])
        ->select('tbl_expenditures.*')
        ->select('tbl_expenditure_categories.name', 'category_name');
    
    if ($date_from) {
        $query->where_gte('expense_date', $date_from);
    }
    
    if ($date_to) {
        $query->where_lte('expense_date', $date_to);
    }
    
    if ($category) {
        $query->where('category_id', $category);
    }
    
    $expenses = $query->order_by_desc('expense_date')->find_many();
    
    if ($format == 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="expenditures_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, ['Date', 'Description', 'Category', 'Amount', 'Vendor', 'Receipt Number', 'Notes']);
        
        // Data
        foreach ($expenses as $expense) {
            fputcsv($output, [
                $expense->expense_date,
                $expense->description,
                $expense->category_name ?: 'Uncategorized',
                $expense->amount,
                $expense->vendor,
                $expense->receipt_number,
                $expense->notes
            ]);
        }
        
        fclose($output);
        exit;
    }
}

// Initialize database tables if they don't exist
function expenditure_init_db()
{
    $db = ORM::get_db();
    
    // Create expenditure categories table
    $db->exec("CREATE TABLE IF NOT EXISTS `tbl_expenditure_categories` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `description` text,
        PRIMARY KEY (`id`),
        UNIQUE KEY `name` (`name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    
    // Create expenditures table
    $db->exec("CREATE TABLE IF NOT EXISTS `tbl_expenditures` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `description` varchar(255) NOT NULL,
        `amount` decimal(10,2) NOT NULL,
        `category_id` int(11) DEFAULT NULL,
        `expense_date` date NOT NULL,
        `receipt_number` varchar(100) DEFAULT NULL,
        `vendor` varchar(100) DEFAULT NULL,
        `notes` text,
        `created_by` int(11) DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `expense_date` (`expense_date`),
        KEY `category_id` (`category_id`),
        FOREIGN KEY (`category_id`) REFERENCES `tbl_expenditure_categories` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    
    // Insert default categories if table is empty
    $categoryCount = ORM::for_table('tbl_expenditure_categories')->count();
    if ($categoryCount == 0) {
        $defaultCategories = [
            ['name' => 'Equipment', 'description' => 'Network equipment, servers, hardware'],
            ['name' => 'Internet Bandwidth', 'description' => 'Upstream internet connectivity costs'],
            ['name' => 'Utilities', 'description' => 'Electricity, water, cooling'],
            ['name' => 'Office Supplies', 'description' => 'Stationery, consumables'],
            ['name' => 'Maintenance', 'description' => 'Equipment servicing and repairs'],
            ['name' => 'Software Licenses', 'description' => 'Software subscriptions and licenses'],
            ['name' => 'Staff Salaries', 'description' => 'Employee compensation'],
            ['name' => 'Marketing', 'description' => 'Advertising and promotional costs'],
            ['name' => 'Transport', 'description' => 'Vehicle fuel, maintenance, travel'],
            ['name' => 'Professional Services', 'description' => 'Legal, accounting, consulting']
        ];
        
        foreach ($defaultCategories as $cat) {
            $category = ORM::for_table('tbl_expenditure_categories')->create();
            $category->name = $cat['name'];
            $category->description = $cat['description'];
            $category->save();
        }
    }
}

// Initialize the database when plugin loads
expenditure_init_db();
