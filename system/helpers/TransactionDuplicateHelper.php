<?php

/**
 * Transaction Duplicate Prevention Helper
 * Provides centralized duplicate checking for all payment gateways
 */

class TransactionDuplicateHelper {
    private static $cache = [];
    const DUPLICATE_WINDOW = 300; // 5 minutes window to check for duplicates
    
    /**
     * Check if a transaction is a duplicate based on multiple criteria
     * @param string $username Username
     * @param string $plan_name Plan name
     * @param float $price Transaction price
     * @param string $gateway Payment gateway
     * @param string $trx_id Transaction ID from gateway (optional)
     * @return bool True if duplicate, False if not
     */
    public static function isDuplicateTransaction($username, $plan_name, $price, $gateway, $trx_id = '') {
        global $config;
        
        // Generate unique key for this transaction
        $key = self::generateTransactionKey($username, $plan_name, $price, $gateway);
        
        // Check memory cache first
        if (isset(self::$cache[$key]) && (time() - self::$cache[$key]) < self::DUPLICATE_WINDOW) {
            return true;
        }
        
        // Check if transaction with same gateway_trx_id already exists
        if (!empty($trx_id)) {
            $existing_trx = ORM::for_table('tbl_payment_gateway')
                ->where('gateway_trx_id', $trx_id)
                ->where('status', 2) // Already paid
                ->find_one();
            
            if ($existing_trx) {
                self::$cache[$key] = time();
                return true;
            }
        }
        
        // Check for recent duplicate transactions in database
        $recent_transaction = ORM::for_table('tbl_transactions')
            ->where('username', $username)
            ->where('plan_name', $plan_name)
            ->where('price', $price)
            ->where_gte('recharged_on', date('Y-m-d', strtotime('-1 day')))
            ->where_like('method', '%' . $gateway . '%')
            ->find_one();
            
        if ($recent_transaction) {
            // Check if it's within the duplicate window
            $transaction_time = strtotime($recent_transaction->recharged_on . ' ' . $recent_transaction->recharged_time);
            $current_time = time();
            
            if (($current_time - $transaction_time) < self::DUPLICATE_WINDOW) {
                // Add to cache
                self::$cache[$key] = time();
                return true;
            }
        }
        
        // Not a duplicate - add to cache
        self::$cache[$key] = time();
        return false;
    }
    
    /**
     * Check if a payment gateway record already exists and is paid
     * @param string $checkout_id Checkout/Transaction ID from payment provider
     * @return bool True if already processed, False if not
     */
    public static function isPaymentAlreadyProcessed($checkout_id) {
        if (empty($checkout_id)) {
            return false;
        }
        
        $existing_payment = ORM::for_table('tbl_payment_gateway')
            ->where('checkout', $checkout_id)
            ->where('status', 2) // Status 2 = Paid
            ->find_one();
            
        return $existing_payment ? true : false;
    }
    
    /**
     * Check if transaction with same gateway transaction ID exists
     * @param string $gateway_trx_id Gateway transaction ID (mpesa code, etc)
     * @return bool True if exists, False if not
     */
    public static function isGatewayTransactionExists($gateway_trx_id) {
        if (empty($gateway_trx_id)) {
            return false;
        }
        
        $existing_trx = ORM::for_table('tbl_payment_gateway')
            ->where('gateway_trx_id', $gateway_trx_id)
            ->find_one();
            
        return $existing_trx ? true : false;
    }
    
    /**
     * Generate a unique key for transaction caching
     */
    private static function generateTransactionKey($username, $plan_name, $price, $gateway) {
        return md5($username . '|' . $plan_name . '|' . $price . '|' . $gateway . '|' . date('Y-m-d H:i'));
    }
    
    /**
     * Clean old entries from cache
     */
    public static function cleanCache() {
        $now = time();
        foreach (self::$cache as $key => $timestamp) {
            if (($now - $timestamp) >= self::DUPLICATE_WINDOW) {
                unset(self::$cache[$key]);
            }
        }
    }
    
    /**
     * Log potential duplicate attempt for monitoring
     */
    public static function logDuplicateAttempt($username, $plan_name, $price, $gateway, $reason = '') {
        $log_message = "DUPLICATE TRANSACTION PREVENTED: User: {$username}, Plan: {$plan_name}, Price: {$price}, Gateway: {$gateway}";
        if (!empty($reason)) {
            $log_message .= ", Reason: {$reason}";
        }
        
        // Log to file
        error_log($log_message, 3, 'duplicate_transactions.log');
        
        // Log to database if logging table exists
        try {
            $log = ORM::for_table('tbl_logs')->create();
            $log->datetime = date('Y-m-d H:i:s');
            $log->level = 'WARNING';
            $log->message = $log_message;
            $log->save();
        } catch (Exception $e) {
            // Ignore if logging table doesn't exist
        }
    }
}
