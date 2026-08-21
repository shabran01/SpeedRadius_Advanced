<?php

/**
 *  Hotspot Auto-Sync Cron Job
 *  Automatically syncs Hotspot customers to fix expiration issues
 *  Add this to your main cron job or run separately
 */

// Include the sync utility
require_once 'sync_hotspot_customers.php';

// Additional auto-sync logic can be added here
echo "Hotspot Auto-sync completed at: " . date('Y-m-d H:i:s') . "\n";

?>
