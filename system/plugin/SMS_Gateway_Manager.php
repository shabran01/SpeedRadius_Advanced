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
    
    // Map gateway key → its hook function
    $map = [
        'talksasa'      => 'smsGatewayTalkSasa_hook_send_sms',
        'blessed_texts' => 'smsGateway_hook_send_sms',
        'bytewave'      => 'smsGatewayBytewave_hook_send_sms',
        'smsgate'       => 'smsGatewaySmSGate_hook_send_sms',
        'texin'         => 'smsGatewayTexin_hook_send_sms',
    ];
    
    // Try the selected gateway first, then fall back to every other gateway so
    // a payment/expiry SMS is never lost due to a misconfigured/offline gateway.
    $order = array_keys($map);
    if (($key = array_search($selected_gateway, $order)) !== false) {
        unset($order[$key]);
        array_unshift($order, $selected_gateway);
    }
    
    foreach ($order as $gw) {
        $fn = $map[$gw];
        if (function_exists($fn)) {
            $result = call_user_func($fn, $data);
            if ($result === true) {
                return true; // delivered by this gateway
            }
            // Failed (misconfigured / offline) — try the next configured gateway
        }
    }
    
    // If no gateway could deliver, let other hooks try
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
                <option value="smsgate"' . ($active_gateway == 'smsgate' ? ' selected' : '') . '>SmSGate</option>
                <option value="texin"' . ($active_gateway == 'texin' ? ' selected' : '') . '>Texin SMS</option>
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
