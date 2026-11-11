<?php

/**
 * Inventory Management Plugin for ISP Billing System
 * Track products, stock levels, suppliers, and inventory movements
 * Currency: Kenyan Shillings (Ksh)
 */

// Register menu item in admin panel under Reports section
register_menu("Inventory", true, "inventory", 'REPORTS', 'fa fa-cubes', "New", "purple", ['Admin', 'SuperAdmin']);

function inventory()
{
    global $ui, $config;
    _admin();
    
    $action = _req('action', 'dashboard');
    
    switch ($action) {
        case 'dashboard':
            inventory_dashboard();
            break;
        case 'products':
            inventory_products();
            break;
        case 'add_product':
            inventory_add_product();
            break;
        case 'edit_product':
            inventory_edit_product();
            break;
        case 'save_product':
            inventory_save_product();
            break;
        case 'delete_product':
            inventory_delete_product();
            break;
        case 'stock_in':
            inventory_stock_in();
            break;
        case 'stock_out':
            inventory_stock_out();
            break;
        case 'save_stock_in':
            inventory_save_stock_in();
            break;
        case 'save_stock_out':
            inventory_save_stock_out();
            break;
        case 'movements':
            inventory_movements();
            break;
        case 'suppliers':
            inventory_suppliers();
            break;
        case 'add_supplier':
            inventory_add_supplier();
            break;
        case 'save_supplier':
            inventory_save_supplier();
            break;
        case 'delete_supplier':
            inventory_delete_supplier();
            break;
        case 'categories':
            inventory_categories();
            break;
        case 'save_category':
            inventory_save_category();
            break;
        case 'delete_category':
            inventory_delete_category();
            break;
        case 'reports':
            inventory_reports();
            break;
        case 'low_stock':
            inventory_low_stock();
            break;
        default:
            inventory_dashboard();
            break;
    }
}

function inventory_dashboard()
{
    global $ui, $admin;
    
    // Check if tables exist, if not create them
    inventory_check_tables();
    
    // Get statistics
    $totalProducts = ORM::for_table('tbl_inventory_products')->count();
    $lowStockProducts = ORM::for_table('tbl_inventory_products')
        ->where_raw('quantity <= reorder_level')
        ->count();
    
    $totalStockValue = ORM::for_table('tbl_inventory_products')
        ->select_expr('SUM(quantity * unit_price)', 'total_value')
        ->find_one();
    
    $stockValue = $totalStockValue ? $totalStockValue->total_value : 0;
    
    // Recent stock movements
    $recentMovements = ORM::for_table('tbl_inventory_movements')
        ->left_outer_join('tbl_inventory_products', ['tbl_inventory_movements.product_id', '=', 'tbl_inventory_products.id'])
        ->select('tbl_inventory_movements.*')
        ->select('tbl_inventory_products.name', 'product_name')
        ->order_by_desc('tbl_inventory_movements.created_at')
        ->limit(10)
        ->find_many();
    
    // Low stock alerts
    $lowStockItems = ORM::for_table('tbl_inventory_products')
        ->left_outer_join('tbl_inventory_categories', ['tbl_inventory_products.category_id', '=', 'tbl_inventory_categories.id'])
        ->select('tbl_inventory_products.*')
        ->select('tbl_inventory_categories.name', 'category_name')
        ->where_raw('tbl_inventory_products.quantity <= tbl_inventory_products.reorder_level')
        ->order_by_asc('tbl_inventory_products.quantity')
        ->find_many();
    
    // Monthly stock in/out trends
    $monthlyData = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i month"));
        
        $stockIn = ORM::for_table('tbl_inventory_movements')
            ->where('movement_type', 'IN')
            ->where_like('created_at', $month . '%')
            ->sum('quantity');
        
        $stockOut = ORM::for_table('tbl_inventory_movements')
            ->where('movement_type', 'OUT')
            ->where_like('created_at', $month . '%')
            ->sum('quantity');
        
        $monthlyData[] = [
            'month' => date('M Y', strtotime($month . '-01')),
            'stock_in' => $stockIn ? $stockIn : 0,
            'stock_out' => $stockOut ? $stockOut : 0
        ];
    }
    
    $ui->assign('totalProducts', $totalProducts);
    $ui->assign('lowStockProducts', $lowStockProducts);
    $ui->assign('stockValue', $stockValue);
    $ui->assign('recentMovements', $recentMovements);
    $ui->assign('lowStockItems', $lowStockItems);
    $ui->assign('monthlyData', json_encode($monthlyData));
    $ui->assign('_title', 'Inventory Dashboard');
    $ui->assign('_system_menu', 'inventory');
    $ui->assign('_admin', $admin);
    
    $ui->display('inventory_dashboard.tpl');
}

function inventory_products()
{
    global $ui, $admin;
    
    $search = _post('search');
    $categoryFilter = _post('category');
    
    $query = ORM::for_table('tbl_inventory_products')
        ->left_outer_join('tbl_inventory_categories', ['tbl_inventory_products.category_id', '=', 'tbl_inventory_categories.id'])
        ->left_outer_join('tbl_inventory_suppliers', ['tbl_inventory_products.supplier_id', '=', 'tbl_inventory_suppliers.id'])
        ->select('tbl_inventory_products.*')
        ->select('tbl_inventory_categories.name', 'category_name')
        ->select('tbl_inventory_suppliers.name', 'supplier_name');
    
    if (!empty($search)) {
        $query->where_raw("(tbl_inventory_products.name LIKE ? OR tbl_inventory_products.sku LIKE ?)", ["%$search%", "%$search%"]);
    }
    
    if (!empty($categoryFilter)) {
        $query->where('tbl_inventory_products.category_id', $categoryFilter);
    }
    
    $products = $query->order_by_asc('tbl_inventory_products.name')->find_many();
    $categories = ORM::for_table('tbl_inventory_categories')->order_by_asc('name')->find_many();
    
    $ui->assign('products', $products);
    $ui->assign('categories', $categories);
    $ui->assign('_title', 'Products List');
    $ui->assign('_system_menu', 'inventory');
    $ui->assign('_admin', $admin);
    
    $ui->display('inventory_products.tpl');
}

function inventory_add_product()
{
    global $ui, $admin;
    
    $categories = ORM::for_table('tbl_inventory_categories')->order_by_asc('name')->find_many();
    $suppliers = ORM::for_table('tbl_inventory_suppliers')->order_by_asc('name')->find_many();
    
    $ui->assign('categories', $categories);
    $ui->assign('suppliers', $suppliers);
    $ui->assign('_title', 'Add Product');
    $ui->assign('_system_menu', 'inventory');
    $ui->assign('_admin', $admin);
    
    $ui->display('inventory_add_product.tpl');
}

function inventory_edit_product()
{
    global $ui, $admin;
    
    $id = _post('id');
    $product = ORM::for_table('tbl_inventory_products')->find_one($id);
    
    if (!$product) {
        r2(U . 'plugin/inventory&action=products', 'e', 'Product not found');
    }
    
    $categories = ORM::for_table('tbl_inventory_categories')->order_by_asc('name')->find_many();
    $suppliers = ORM::for_table('tbl_inventory_suppliers')->order_by_asc('name')->find_many();
    
    $ui->assign('product', $product);
    $ui->assign('categories', $categories);
    $ui->assign('suppliers', $suppliers);
    $ui->assign('_title', 'Edit Product');
    $ui->assign('_system_menu', 'inventory');
    $ui->assign('_admin', $admin);
    
    $ui->display('inventory_edit_product.tpl');
}

function inventory_save_product()
{
    global $admin;
    
    $id = _post('id');
    $name = _post('name');
    $sku = _post('sku');
    $category_id = _post('category_id');
    $supplier_id = _post('supplier_id');
    $unit_price = _post('unit_price');
    $selling_price = _post('selling_price');
    $quantity = _post('quantity');
    $reorder_level = _post('reorder_level');
    $description = _post('description');
    
    if (empty($name)) {
        r2(U . 'plugin/inventory&action=add_product', 'e', 'Product name is required');
    }
    
    if ($id) {
        $product = ORM::for_table('tbl_inventory_products')->find_one($id);
        if (!$product) {
            r2(U . 'plugin/inventory&action=products', 'e', 'Product not found');
        }
    } else {
        $product = ORM::for_table('tbl_inventory_products')->create();
        $product->created_at = date('Y-m-d H:i:s');
    }
    
    $product->name = $name;
    $product->sku = $sku;
    $product->category_id = !empty($category_id) ? $category_id : null;
    $product->supplier_id = !empty($supplier_id) ? $supplier_id : null;
    $product->unit_price = $unit_price ? $unit_price : 0;
    $product->selling_price = $selling_price ? $selling_price : 0;
    $product->quantity = $quantity ? $quantity : 0;
    $product->reorder_level = $reorder_level ? $reorder_level : 10;
    $product->description = $description;
    $product->updated_at = date('Y-m-d H:i:s');
    $product->updated_by = $admin['username'];
    
    $product->save();
    
    r2(U . 'plugin/inventory&action=products', 's', 'Product saved successfully');
}

function inventory_delete_product()
{
    $id = _post('id');
    $product = ORM::for_table('tbl_inventory_products')->find_one($id);
    
    if ($product) {
        $product->delete();
        r2(U . 'plugin/inventory&action=products', 's', 'Product deleted successfully');
    }
    
    r2(U . 'plugin/inventory&action=products', 'e', 'Product not found');
}

function inventory_stock_in()
{
    global $ui, $admin;
    
    $products = ORM::for_table('tbl_inventory_products')->order_by_asc('name')->find_many();
    $suppliers = ORM::for_table('tbl_inventory_suppliers')->order_by_asc('name')->find_many();
    
    $ui->assign('products', $products);
    $ui->assign('suppliers', $suppliers);
    $ui->assign('_title', 'Stock In');
    $ui->assign('_system_menu', 'inventory');
    $ui->assign('_admin', $admin);
    
    $ui->display('inventory_stock_in.tpl');
}

function inventory_save_stock_in()
{
    global $admin;
    
    $product_id = _post('product_id');
    $quantity = _post('quantity');
    $unit_cost = _post('unit_cost');
    $supplier_id = _post('supplier_id');
    $reference = _post('reference');
    $notes = _post('notes');
    
    if (empty($product_id) || empty($quantity)) {
        r2(U . 'plugin/inventory&action=stock_in', 'e', 'Product and quantity are required');
    }
    
    // Get product
    $product = ORM::for_table('tbl_inventory_products')->find_one($product_id);
    if (!$product) {
        r2(U . 'plugin/inventory&action=stock_in', 'e', 'Product not found');
    }
    
    // Update product quantity
    $product->quantity = $product->quantity + $quantity;
    if (!empty($unit_cost)) {
        $product->unit_price = $unit_cost;
    }
    $product->updated_at = date('Y-m-d H:i:s');
    $product->save();
    
    // Record movement
    $movement = ORM::for_table('tbl_inventory_movements')->create();
    $movement->product_id = $product_id;
    $movement->movement_type = 'IN';
    $movement->quantity = $quantity;
    $movement->unit_cost = $unit_cost ? $unit_cost : $product->unit_price;
    $movement->total_cost = $quantity * ($unit_cost ? $unit_cost : $product->unit_price);
    $movement->supplier_id = !empty($supplier_id) ? $supplier_id : null;
    $movement->reference = $reference;
    $movement->notes = $notes;
    $movement->created_by = $admin['username'];
    $movement->created_at = date('Y-m-d H:i:s');
    $movement->save();
    
    r2(U . 'plugin/inventory&action=movements', 's', 'Stock added successfully');
}

function inventory_stock_out()
{
    global $ui, $admin;
    
    $products = ORM::for_table('tbl_inventory_products')
        ->where_gt('quantity', 0)
        ->order_by_asc('name')
        ->find_many();
    
    $ui->assign('products', $products);
    $ui->assign('_title', 'Stock Out');
    $ui->assign('_system_menu', 'inventory');
    $ui->assign('_admin', $admin);
    
    $ui->display('inventory_stock_out.tpl');
}

function inventory_save_stock_out()
{
    global $admin;
    
    $product_id = _post('product_id');
    $quantity = _post('quantity');
    $reason = _post('reason');
    $customer = _post('customer');
    $reference = _post('reference');
    $notes = _post('notes');
    
    if (empty($product_id) || empty($quantity)) {
        r2(U . 'plugin/inventory&action=stock_out', 'e', 'Product and quantity are required');
    }
    
    // Get product
    $product = ORM::for_table('tbl_inventory_products')->find_one($product_id);
    if (!$product) {
        r2(U . 'plugin/inventory&action=stock_out', 'e', 'Product not found');
    }
    
    if ($product->quantity < $quantity) {
        r2(U . 'plugin/inventory&action=stock_out', 'e', 'Insufficient stock. Available: ' . $product->quantity);
    }
    
    // Update product quantity
    $product->quantity = $product->quantity - $quantity;
    $product->updated_at = date('Y-m-d H:i:s');
    $product->save();
    
    // Record movement
    $movement = ORM::for_table('tbl_inventory_movements')->create();
    $movement->product_id = $product_id;
    $movement->movement_type = 'OUT';
    $movement->quantity = $quantity;
    $movement->unit_cost = $product->unit_price;
    $movement->total_cost = $quantity * $product->unit_price;
    $movement->reason = $reason;
    $movement->customer = $customer;
    $movement->reference = $reference;
    $movement->notes = $notes;
    $movement->created_by = $admin['username'];
    $movement->created_at = date('Y-m-d H:i:s');
    $movement->save();
    
    r2(U . 'plugin/inventory&action=movements', 's', 'Stock removed successfully');
}

function inventory_movements()
{
    global $ui, $admin;
    
    $type_filter = _post('type');
    $date_from = _post('date_from');
    $date_to = _post('date_to');
    
    $query = ORM::for_table('tbl_inventory_movements')
        ->left_outer_join('tbl_inventory_products', ['tbl_inventory_movements.product_id', '=', 'tbl_inventory_products.id'])
        ->left_outer_join('tbl_inventory_suppliers', ['tbl_inventory_movements.supplier_id', '=', 'tbl_inventory_suppliers.id'])
        ->select('tbl_inventory_movements.*')
        ->select('tbl_inventory_products.name', 'product_name')
        ->select('tbl_inventory_suppliers.name', 'supplier_name');
    
    if (!empty($type_filter)) {
        $query->where('tbl_inventory_movements.movement_type', $type_filter);
    }
    
    if (!empty($date_from)) {
        $query->where_gte('tbl_inventory_movements.created_at', $date_from . ' 00:00:00');
    }
    
    if (!empty($date_to)) {
        $query->where_lte('tbl_inventory_movements.created_at', $date_to . ' 23:59:59');
    }
    
    $movements = $query->order_by_desc('tbl_inventory_movements.created_at')->find_many();
    
    $ui->assign('movements', $movements);
    $ui->assign('_title', 'Stock Movements');
    $ui->assign('_system_menu', 'inventory');
    $ui->assign('_admin', $admin);
    
    $ui->display('inventory_movements.tpl');
}

function inventory_suppliers()
{
    global $ui, $admin;
    
    $suppliers = ORM::for_table('tbl_inventory_suppliers')->order_by_asc('name')->find_many();
    
    $ui->assign('suppliers', $suppliers);
    $ui->assign('_title', 'Suppliers');
    $ui->assign('_system_menu', 'inventory');
    $ui->assign('_admin', $admin);
    
    $ui->display('inventory_suppliers.tpl');
}

function inventory_save_supplier()
{
    $id = _post('id');
    $name = _post('name');
    $contact_person = _post('contact_person');
    $phone = _post('phone');
    $email = _post('email');
    $address = _post('address');
    
    if (empty($name)) {
        r2(U . 'plugin/inventory&action=suppliers', 'e', 'Supplier name is required');
    }
    
    if ($id) {
        $supplier = ORM::for_table('tbl_inventory_suppliers')->find_one($id);
    } else {
        $supplier = ORM::for_table('tbl_inventory_suppliers')->create();
    }
    
    $supplier->name = $name;
    $supplier->contact_person = $contact_person;
    $supplier->phone = $phone;
    $supplier->email = $email;
    $supplier->address = $address;
    $supplier->updated_at = date('Y-m-d H:i:s');
    $supplier->save();
    
    r2(U . 'plugin/inventory&action=suppliers', 's', 'Supplier saved successfully');
}

function inventory_delete_supplier()
{
    $id = _post('id');
    $supplier = ORM::for_table('tbl_inventory_suppliers')->find_one($id);
    
    if ($supplier) {
        $supplier->delete();
        r2(U . 'plugin/inventory&action=suppliers', 's', 'Supplier deleted successfully');
    }
    
    r2(U . 'plugin/inventory&action=suppliers', 'e', 'Supplier not found');
}

function inventory_categories()
{
    global $ui, $admin;
    
    $categories = ORM::for_table('tbl_inventory_categories')->order_by_asc('name')->find_many();
    
    $ui->assign('categories', $categories);
    $ui->assign('_title', 'Categories');
    $ui->assign('_system_menu', 'inventory');
    $ui->assign('_admin', $admin);
    
    $ui->display('inventory_categories.tpl');
}

function inventory_save_category()
{
    $id = _post('id');
    $name = _post('name');
    $description = _post('description');
    
    if (empty($name)) {
        r2(U . 'plugin/inventory&action=categories', 'e', 'Category name is required');
    }
    
    if ($id) {
        $category = ORM::for_table('tbl_inventory_categories')->find_one($id);
    } else {
        $category = ORM::for_table('tbl_inventory_categories')->create();
    }
    
    $category->name = $name;
    $category->description = $description;
    $category->save();
    
    r2(U . 'plugin/inventory&action=categories', 's', 'Category saved successfully');
}

function inventory_delete_category()
{
    $id = _post('id');
    $category = ORM::for_table('tbl_inventory_categories')->find_one($id);
    
    if ($category) {
        $category->delete();
        r2(U . 'plugin/inventory&action=categories', 's', 'Category deleted successfully');
    }
    
    r2(U . 'plugin/inventory&action=categories', 'e', 'Category not found');
}

function inventory_check_tables()
{
    $db = ORM::get_db();
    
    // Check and create products table
    $db->exec("CREATE TABLE IF NOT EXISTS tbl_inventory_products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        sku VARCHAR(100),
        category_id INT,
        supplier_id INT,
        description TEXT,
        unit_price DECIMAL(10,2) DEFAULT 0,
        selling_price DECIMAL(10,2) DEFAULT 0,
        quantity INT DEFAULT 0,
        reorder_level INT DEFAULT 10,
        created_at DATETIME,
        updated_at DATETIME,
        updated_by VARCHAR(100),
        INDEX(category_id),
        INDEX(supplier_id),
        INDEX(sku)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Check and create movements table
    $db->exec("CREATE TABLE IF NOT EXISTS tbl_inventory_movements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        movement_type ENUM('IN', 'OUT') NOT NULL,
        quantity INT NOT NULL,
        unit_cost DECIMAL(10,2) DEFAULT 0,
        total_cost DECIMAL(10,2) DEFAULT 0,
        supplier_id INT,
        reason VARCHAR(255),
        customer VARCHAR(255),
        reference VARCHAR(100),
        notes TEXT,
        created_by VARCHAR(100),
        created_at DATETIME,
        INDEX(product_id),
        INDEX(movement_type),
        INDEX(created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Check and create categories table
    $db->exec("CREATE TABLE IF NOT EXISTS tbl_inventory_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Check and create suppliers table
    $db->exec("CREATE TABLE IF NOT EXISTS tbl_inventory_suppliers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        contact_person VARCHAR(255),
        phone VARCHAR(50),
        email VARCHAR(255),
        address TEXT,
        updated_at DATETIME
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}
