<?php

/**
 * SMS Gateway Manager Plugin for SpeedRadius
 * Allows switching between different SMS gateways
 */

// Include the SMS Lock helper
require_once(__DIR__ . '/../helpers/SMSLock.php');

// Register hook for managing SMS gateway selection with highest priority
register_hook('send_sms', 'sms_gateway_manager_hook_send_sms', 1);

function sms_gateway_manager_hook_send_sms($data = []) {
    global $config;
    
    // Get the currently selected gateway
    $selected_gateway = isset($config['active_sms_gateway']) ? $config['active_sms_gateway'] : 'blessed_texts';
    
    // Make sure we have a phone number and message
    if (!is_array($data) || count($data) < 2) {
        return true; // Let other hooks handle invalid data
    }

    list($phone, $message) = $data;
    
    // Route to the appropriate gateway
    if ($selected_gateway === 'talksasa') {
        // Use Talk Sasa gateway
        if(function_exists('smsGatewayTalkSasa_hook_send_sms')) {
            $result = smsGatewayTalkSasa_hook_send_sms($data);
            return $result === false; // Only allow other hooks if this one failed
        }
    } else if ($selected_gateway === 'blessed_texts') {
        // Use Blessed Texts gateway
        if(function_exists('smsGateway_hook_send_sms')) {
            $result = smsGateway_hook_send_sms($data);
            return $result === false; // Only allow other hooks if this one failed
        }
    } else if ($selected_gateway === 'bytewave') {
        // Use BytewaveSMS gateway
        if(function_exists('smsGatewayBytewave_hook_send_sms')) {
            $result = smsGatewayBytewave_hook_send_sms($data);
            return $result === false; // Only allow other hooks if this one failed
        }
    } else if ($selected_gateway === 'zettatel') {
        // Use Zettatel gateway
        if(function_exists('smsGatewayZettatel_hook_send_sms')) {
            $result = smsGatewayZettatel_hook_send_sms($data);
            return $result === false; // Only allow other hooks if this one failed
        }
    }
    
    // If selected gateway is not available or failed, let other hooks try
    return true;
}

// Add gateway selection to the settings page
add_hook('settings_app_end', 'sms_gateway_manager_settings');

function sms_gateway_manager_settings() {
    global $ui, $config;
    
    // Get current gateway selection
    $active_gateway = isset($config['active_sms_gateway']) ? $config['active_sms_gateway'] : 'blessed_texts';
    
    $html = '
    <div class="form-group">
        <label class="col-md-2 control-label">SMS Gateway</label>
        <div class="col-md-6">
            <select class="form-control" id="active_sms_gateway" name="active_sms_gateway">
                <option value="blessed_texts"' . ($active_gateway == 'blessed_texts' ? ' selected' : '') . '>Blessed Texts</option>
                <option value="talksasa"' . ($active_gateway == 'talksasa' ? ' selected' : '') . '>Talk Sasa</option>
                <option value="bytewave"' . ($active_gateway == 'bytewave' ? ' selected' : '') . '>BytewaveSMS</option>
                <option value="zettatel"' . ($active_gateway == 'zettatel' ? ' selected' : '') . '>Zettatel</option>
            </select>
            <p class="help-block">Select which SMS gateway to use for sending messages</p>
        </div>
    </div>';
    
    return $html;
}

// Save the gateway selection
add_hook('settings_app_post', 'sms_gateway_manager_save_settings');

function sms_gateway_manager_save_settings() {
    if(isset($_POST['active_sms_gateway'])) {
        $d = ORM::for_table('tbl_appconfig')->where('setting', 'active_sms_gateway')->find_one();
        if($d) {
            $d->value = $_POST['active_sms_gateway'];
            $d->save();
        } else {
            $d = ORM::for_table('tbl_appconfig')->create();
            $d->setting = 'active_sms_gateway';
            $d->value = $_POST['active_sms_gateway'];
            $d->save();
        }
    }
}
