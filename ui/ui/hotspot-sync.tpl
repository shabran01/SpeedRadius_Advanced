<?php

/**
 *  Hotspot Customer Sync Web Interface
 *  Provides a web interface to sync expired Hotspot customers between billing system and Mikrotik
 */

include "../init.php";

// Check if user has permission
if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
    _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
}

$message = '';
$error = '';

// Handle sync request
if ($_POST['action'] == 'sync') {
    try {
        // Run the sync script
        $output = [];
        $return_var = 0;
        
        // Execute sync script
        $command = 'php "' . __DIR__ . '/sync_hotspot_customers.php" 2>&1';
        exec($command, $output, $return_var);
        
        if ($return_var === 0) {
            $message = 'Hotspot sync completed successfully!';
        } else {
            $error = 'Hotspot sync completed with errors. Check output below.';
        }
        
        $sync_output = implode("\n", $output);
        
    } catch (Exception $e) {
        $error = 'Error running Hotspot sync: ' . $e->getMessage();
    }
}

// Get current status
$expired_count = ORM::for_table('tbl_user_recharges')
    ->join('tbl_plans', array('tbl_user_recharges.plan_id', '=', 'tbl_plans.id'))
    ->where('tbl_user_recharges.status', 'off')
    ->where('tbl_plans.type', 'Hotspot')
    ->where_lte('tbl_user_recharges.expiration', date('Y-m-d'))
    ->count();

$active_count = ORM::for_table('tbl_user_recharges')
    ->join('tbl_plans', array('tbl_user_recharges.plan_id', '=', 'tbl_plans.id'))
    ->where('tbl_user_recharges.status', 'on')
    ->where('tbl_plans.type', 'Hotspot')
    ->where_gte('tbl_user_recharges.expiration', date('Y-m-d'))
    ->count();

// Get online Hotspot users count
$online_count = ORM::for_table('tbl_user_recharges')
    ->join('tbl_plans', array('tbl_user_recharges.plan_id', '=', 'tbl_plans.id'))
    ->where('tbl_user_recharges.status', 'on')
    ->where('tbl_plans.type', 'Hotspot')
    ->where_gte('tbl_user_recharges.expiration', date('Y-m-d'))
    ->count();

?>

{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-wifi"></i> Hotspot Customer Sync Utility
            </div>
            <div class="panel-body">
                
                <?php if ($message): ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check"></i> <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-info-circle"></i> Current Hotspot Status
                            </div>
                            <div class="panel-body">
                                <table class="table table-striped">
                                    <tr>
                                        <td><strong>Expired Hotspot Customers:</strong></td>
                                        <td class="text-danger"><?php echo $expired_count; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Active Hotspot Customers:</strong></td>
                                        <td class="text-success"><?php echo $active_count; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Hotspot Customers:</strong></td>
                                        <td><?php echo $expired_count + $active_count; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-question-circle"></i> What This Does
                            </div>
                            <div class="panel-body">
                                <p><strong>This utility fixes Hotspot sync issues between billing system and Mikrotik routers:</strong></p>
                                <ul>
                                    <li>Removes expired Hotspot users from Mikrotik</li>
                                    <li>Disconnects active expired connections</li>
                                    <li>Restores active Hotspot users missing on router</li>
                                    <li>Fixes issues when cron job fails to connect to router</li>
                                    <li>Handles manual recharges that weren't synced to router</li>
                                </ul>
                                <p class="text-muted">Recommended when Hotspot users expired in billing system but still have internet access.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <i class="fa fa-cogs"></i> Sync Actions
                            </div>
                            <div class="panel-body">
                                <form method="post" class="form-inline">
                                    <input type="hidden" name="action" value="sync">
                                    <button type="submit" class="btn btn-primary" onclick="return confirm('This will sync all Hotspot customers across all routers. Continue?');">
                                        <i class="fa fa-wifi"></i> Run Hotspot Sync Now
                                    </button>
                                    <span class="help-block">This may take a few minutes depending on the number of Hotspot customers and routers.</span>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (isset($sync_output)): ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-terminal"></i> Sync Output
                            </div>
                            <div class="panel-body">
                                <pre style="background: #f5f5f5; padding: 15px; border-radius: 4px; max-height: 400px; overflow-y: auto;"><?php echo htmlspecialchars($sync_output); ?></pre>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <i class="fa fa-exclamation-triangle"></i> Common Hotspot Issues
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5><i class="fa fa-bug"></i> Problem:</h5>
                                        <p>Hotspot user shows expired in billing system but still has internet access.</p>
                                        
                                        <h5><i class="fa fa-lightbulb"></i> Solution:</h5>
                                        <p>Run this Hotspot sync utility to remove expired users from Mikrotik routers.</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5><i class="fa fa-bug"></i> Problem:</h5>
                                        <p>Hotspot user paid and is active in billing system but cannot login to hotspot.</p>
                                        
                                        <h5><i class="fa fa-lightbulb"></i> Solution:</h5>
                                        <p>Run this Hotspot sync utility to restore active users to Mikrotik routers.</p>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5><i class="fa fa-info-circle"></i> How Hotspot Sync Works:</h5>
                                        <ul>
                                            <li><strong>Expired Users:</strong> Removes hotspot users from /ip/hotspot/user and disconnects from /ip/hotspot/active</li>
                                            <li><strong>Active Users:</strong> Adds missing hotspot users to /ip/hotspot/user with correct profiles</li>
                                            <li><strong>Profile Sync:</strong> Ensures user profiles match current plan assignments</li>
                                            <li><strong>Connection Cleanup:</strong> Forces disconnect of expired active connections</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
