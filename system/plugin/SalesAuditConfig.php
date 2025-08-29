<?php

/**
 * Sales Audit Plugin Configuration
 * Customize the plugin behavior by modifying these settings
 */

// Plugin Configuration Array
$SALES_AUDIT_CONFIG = [
    
    // Display Settings
    'currency_symbol' => '$', // Override system currency if needed
    'date_format' => 'Y-m-d', // PHP date format
    'decimal_places' => 2, // Number of decimal places for currency
    'thousand_separator' => ',', // Thousand separator
    'decimal_separator' => '.', // Decimal separator
    
    // Dashboard Settings
    'auto_refresh_interval' => 300000, // Auto refresh interval in milliseconds (5 minutes)
    'top_plans_limit' => 10, // Number of top plans to show
    'show_zero_sales_days' => true, // Show days with zero sales in trends
    'cache_duration' => 43200, // Cache duration in seconds (12 hours)
    
    // Chart Settings
    'chart_colors' => [
        'primary' => 'rgba(75, 192, 192, 0.8)',
        'secondary' => 'rgba(255, 99, 132, 0.8)',
        'success' => 'rgba(40, 167, 69, 0.8)',
        'warning' => 'rgba(255, 193, 7, 0.8)',
        'danger' => 'rgba(220, 53, 69, 0.8)',
        'info' => 'rgba(23, 162, 184, 0.8)'
    ],
    
    // Comparison Settings
    'default_comparison_period' => 'today', // Default period for comparison page
    'show_percentage_change' => true, // Show percentage changes
    'highlight_positive_growth' => true, // Highlight positive growth in green
    
    // Trends Settings
    'default_trend_period' => '30days', // Default period for trends page
    'show_growth_indicators' => true, // Show growth arrows and percentages
    'trends_decimal_places' => 1, // Decimal places for trend percentages
    
    // Data Filtering
    'exclude_methods' => [ // Payment methods to exclude from reports
        'Customer - Balance',
        'Recharge Balance - Administrator',
        'Manual - Administrator', // Add more as needed
    ],
    
    // Performance Settings
    'max_records_per_query' => 10000, // Maximum records to process per query
    'enable_query_cache' => true, // Enable query result caching
    'log_slow_queries' => false, // Log queries that take longer than threshold
    'slow_query_threshold' => 2, // Slow query threshold in seconds
    
    // Security Settings
    'allowed_user_roles' => ['Admin', 'SuperAdmin'], // User roles that can access plugin
    'require_admin_login' => true, // Require admin login
    'log_access_attempts' => false, // Log plugin access attempts
    
    // API Settings
    'enable_api' => true, // Enable API endpoints
    'api_rate_limit' => 100, // API requests per hour per IP
    'require_api_key' => false, // Require API key for endpoints
    
    // Advanced Settings
    'timezone' => 'UTC', // Timezone for date calculations (leave empty to use system default)
    'week_starts_on' => 'monday', // Week start day: 'monday' or 'sunday'
    'fiscal_year_start' => '01-01', // Fiscal year start (MM-DD format)
    'business_hours_start' => '08:00', // Business hours start (HH:MM format)
    'business_hours_end' => '18:00', // Business hours end (HH:MM format)
    
    // Custom Metrics
    'enable_custom_metrics' => false, // Enable custom metric calculations
    'custom_metrics' => [
        // Example custom metric
        /*
        'avg_hourly_sales' => [
            'name' => 'Average Hourly Sales',
            'description' => 'Average sales per hour during business hours',
            'calculation' => 'total_sales / business_hours',
            'format' => 'currency'
        ]
        */
    ],
    
    // Export Settings
    'enable_export' => true, // Enable data export functionality
    'export_formats' => ['csv', 'xlsx'], // Supported export formats
    'max_export_records' => 50000, // Maximum records per export
    
    // Email Reports
    'enable_email_reports' => false, // Enable scheduled email reports
    'email_recipients' => [], // Array of email addresses
    'email_schedule' => 'daily', // daily, weekly, monthly
    'email_time' => '09:00', // Time to send daily reports (HH:MM)
    
    // Debug Settings
    'debug_mode' => false, // Enable debug mode
    'log_file' => 'sales_audit.log', // Debug log file name
    'verbose_logging' => false, // Enable verbose logging
];

/**
 * Get configuration value
 * @param string $key Configuration key
 * @param mixed $default Default value if key not found
 * @return mixed Configuration value
 */
function getSalesAuditConfig($key, $default = null) {
    global $SALES_AUDIT_CONFIG;
    return isset($SALES_AUDIT_CONFIG[$key]) ? $SALES_AUDIT_CONFIG[$key] : $default;
}

/**
 * Set configuration value
 * @param string $key Configuration key
 * @param mixed $value Configuration value
 */
function setSalesAuditConfig($key, $value) {
    global $SALES_AUDIT_CONFIG;
    $SALES_AUDIT_CONFIG[$key] = $value;
}

/**
 * Get all configuration values
 * @return array All configuration values
 */
function getAllSalesAuditConfig() {
    global $SALES_AUDIT_CONFIG;
    return $SALES_AUDIT_CONFIG;
}

/**
 * Format currency amount according to configuration
 * @param float $amount Amount to format
 * @return string Formatted currency string
 */
function formatSalesAuditCurrency($amount) {
    global $config, $SALES_AUDIT_CONFIG;
    
    $symbol = getSalesAuditConfig('currency_symbol', $config['currency_code'] ?? '$');
    $decimals = getSalesAuditConfig('decimal_places', 2);
    $thousand_sep = getSalesAuditConfig('thousand_separator', ',');
    $decimal_sep = getSalesAuditConfig('decimal_separator', '.');
    
    return $symbol . ' ' . number_format($amount, $decimals, $decimal_sep, $thousand_sep);
}

/**
 * Format percentage according to configuration
 * @param float $percentage Percentage to format
 * @return string Formatted percentage string
 */
function formatSalesAuditPercentage($percentage) {
    $decimals = getSalesAuditConfig('trends_decimal_places', 1);
    return number_format($percentage, $decimals) . '%';
}

/**
 * Check if user has permission to access plugin
 * @return bool True if user has permission
 */
function checkSalesAuditPermission() {
    global $admin;
    
    if (!getSalesAuditConfig('require_admin_login', true)) {
        return true;
    }
    
    $allowedRoles = getSalesAuditConfig('allowed_user_roles', ['Admin', 'SuperAdmin']);
    
    if (!isset($admin['user_type']) || !in_array($admin['user_type'], $allowedRoles)) {
        return false;
    }
    
    return true;
}

/**
 * Log debug message if debug mode is enabled
 * @param string $message Debug message
 * @param string $level Log level (info, warning, error)
 */
function logSalesAuditDebug($message, $level = 'info') {
    if (!getSalesAuditConfig('debug_mode', false)) {
        return;
    }
    
    $logFile = getSalesAuditConfig('log_file', 'sales_audit.log');
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
    
    file_put_contents(__DIR__ . '/' . $logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

?>
