<?php

/**
 * SMS Lock Manager
 * Provides a strict locking mechanism to prevent duplicate SMS messages
 */

class SMSLock {
    private static $lockFile = 'system/cache/sms_locks/';
    
    public static function init() {
        if (!file_exists(self::$lockFile)) {
            mkdir(self::$lockFile, 0777, true);
        }
    }
    
    public static function acquireLock($phone, $message) {
        self::init();
        $lockId = md5($phone . $message);
        $lockPath = self::$lockFile . $lockId;
        
        // Check if a lock exists and is still valid (less than 5 minutes old)
        if (file_exists($lockPath)) {
            $lockTime = filemtime($lockPath);
            if (time() - $lockTime < 300) { // 5 minutes
                return false; // Lock exists and is valid
            }
            // Lock expired, remove it
            @unlink($lockPath);
        }
        
        // Create new lock
        file_put_contents($lockPath, time());
        return true;
    }
    
    public static function releaseLock($phone, $message) {
        $lockId = md5($phone . $message);
        $lockPath = self::$lockFile . $lockId;
        if (file_exists($lockPath)) {
            @unlink($lockPath);
        }
    }
    
    public static function cleanOldLocks() {
        self::init();
        $files = glob(self::$lockFile . '*');
        $now = time();
        foreach ($files as $file) {
            if ($now - filemtime($file) >= 300) {
                @unlink($file);
            }
        }
    }
}
