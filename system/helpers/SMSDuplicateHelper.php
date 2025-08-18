<?php

/**
 * SMS Duplicate Prevention Helper
 * Provides centralized duplicate checking for all SMS gateways
 */

class SMSDuplicateHelper {
    private static $cache = [];
    const DUPLICATE_WINDOW = 300; // 5 minutes window to check for duplicates
    
    /**
     * Check if a message is a duplicate
     * @param string $phone Phone number
     * @param string $message Message content
     * @param string $gateway Gateway identifier
     * @return bool True if duplicate, False if not
     */
    public static function isDuplicate($phone, $message, $gateway = '') {
        global $config;
        
        // Generate unique key for this message
        $key = self::generateKey($phone, $message);
        
        // Check memory cache first
        if (isset(self::$cache[$key]) && (time() - self::$cache[$key]) < self::DUPLICATE_WINDOW) {
            return true;
        }
        
        // Check database
        $recent_log = ORM::for_table('tbl_sms_logs')
            ->where('phone', $phone)
            ->where('message', $message)
            ->where_gte('created_at', date('Y-m-d H:i:s', strtotime('-' . self::DUPLICATE_WINDOW . ' seconds')))
            ->find_one();
            
        if ($recent_log) {
            // Add to cache
            self::$cache[$key] = time();
            return true;
        }
        
        // Not a duplicate - add to cache
        self::$cache[$key] = time();
        return false;
    }
    
    /**
     * Generate a unique key for caching
     */
    private static function generateKey($phone, $message) {
        return md5($phone . '|' . $message);
    }
    
    /**
     * Clear old entries from cache
     */
    public static function cleanCache() {
        $now = time();
        foreach (self::$cache as $key => $timestamp) {
            if (($now - $timestamp) >= self::DUPLICATE_WINDOW) {
                unset(self::$cache[$key]);
            }
        }
    }
}
