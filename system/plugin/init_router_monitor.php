<?php

/**
 * Initialization script for Router Status Monitor
 * Sets up required configurations and database tables
 */

// Ensure WhatsApp Gateway is available and configured
function checkWhatsappGateway() {
    global $config;
    
    if (empty($config['whatsapp_gateway_url'])) {
        error_log('WhatsApp Gateway URL not configured');
        return false;
    }
    
    if (empty($config['whatsapp_gateway_secret'])) {
        error_log('WhatsApp Gateway secret not configured');
        return false;
    }
    
    return true;
}

// Initialize monitoring settings
function initializeMonitorSettings() {
    // Default monitor settings
    $defaults = [
        'monitor_interval' => 300, // 5 minutes
        'notification_cooldown' => 300, // 5 minutes
        'monitor_enabled' => 1
    ];
    
    foreach ($defaults as $setting => $value) {
        $config = ORM::for_table('tbl_appconfig')
            ->where('setting', 'router_' . $setting)
            ->find_one();
            
        if (!$config) {
            $config = ORM::for_table('tbl_appconfig')->create();
            $config->setting = 'router_' . $setting;
            $config->value = $value;
            $config->save();
        }
    }
}

// Run initialization
checkWhatsappGateway();
initializeMonitorSettings();
