<?php

/**
 * PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 * This file for reminding user about expiration
 * Example to run every at 7:00 in the morning
 * 0 7 * * * /usr/bin/php /var/www/system/cron_reminder.php
 **/

include "../init.php";

$isCli = true;
if (php_sapi_name() !== 'cli') {
    $isCli = false;
    echo "<pre>";
}

$d = ORM::for_table('tbl_user_recharges')->where('status', 'on')->whereNotEqual('customer_id', '0')->find_many();

run_hook('cronjob_reminder'); #HOOK


echo "PHP Time\t" . date('Y-m-d H:i:s') . "\n";
$res = ORM::raw_execute('SELECT NOW() AS WAKTU;');
$statement = ORM::get_last_statement();
$rows = array();
while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
    echo "MYSQL Time\t" . $row['WAKTU'] . "\n";
}


$day7 = date('Y-m-d', strtotime("+7 day"));
$day3 = date('Y-m-d', strtotime("+3 day"));
$day1 = date('Y-m-d', strtotime("+1 day"));
print_r([$day1, $day3, $day7]);
foreach ($d as $ds) {
    if (in_array($ds['expiration'], [$day1, $day3, $day7])) {
        $u = ORM::for_table('tbl_user_recharges')->where('id', $ds['id'])->find_one();
        $p = ORM::for_table('tbl_plans')->where('id', $u['plan_id'])->find_one();
        $c = ORM::for_table('tbl_customers')->where('id', $ds['customer_id'])->find_one();
        if ($p['validity_unit'] == 'Period') {
			// Postpaid price from field
			$add_inv = User::getAttribute("Invoice", $ds['customer_id']);
			if (empty ($add_inv) or $add_inv == 0) {
				$price = $p['price'];
			} else {
				$price = $add_inv;
			}
        } else {
                $price = $p['price'];
        }

        // Get reminder notification type from notification settings
        global $_notifmsg;
        $notif_type = 'sms'; // default fallback
        if (isset($_notifmsg['reminder_notification'])) {
            $notif_type = $_notifmsg['reminder_notification'];
            echo "Using notification setting from _notifmsg: " . $notif_type . "\n";
        } else if (isset($config['reminder_notification'])) {
            // Fallback to old config system
            $notif_type = $config['reminder_notification'];
            echo "Using notification setting from config: " . $notif_type . "\n";
        } else {
            $notif_type = 'sms';
            echo "Using default notification setting: " . $notif_type . "\n";
        }
        echo "Final notification type: " . $notif_type . "\n";
        
        // Check if we should send reminder based on service type
        $user_type = isset($u['type']) ? strtoupper($u['type']) : 'HOTSPOT';
        $should_send = true;
        
        if ($user_type == 'PPPOE') {
            // Check if sending to PPPoE users is enabled
            if (isset($_notifmsg['send_reminder_to_pppoe']) && $_notifmsg['send_reminder_to_pppoe'] != '1') {
                $should_send = false;
                echo "⊗ Skipping reminder - PPPoE users disabled for reminder notifications\n";
            }
        } else {
            // Check if sending to Hotspot users is enabled
            if (isset($_notifmsg['send_reminder_to_hotspot']) && $_notifmsg['send_reminder_to_hotspot'] != '1') {
                $should_send = false;
                echo "⊗ Skipping reminder - Hotspot users disabled for reminder notifications\n";
            }
        }
        
        if (!$should_send) {
            continue; // Skip this user
        }
        
        // Determine service type suffix for message templates
        $service_suffix = ($user_type == 'PPPOE') ? '_pppoe' : '_hotspot';
        echo "✓ Service type: {$user_type} - will send reminder\n";
        
        if ($ds['expiration'] == $day7) {
            echo "Sending 7-day reminder to: " . $c['fullname'] . " (" . $c['phonenumber'] . ") via: " . $notif_type . "\n";
            $message_key = 'reminder_7_day' . $service_suffix;
            $message_text = Lang::getNotifText($message_key);
            // Fallback to generic message if service-specific not found
            if (empty($message_text) || $message_text == $message_key) {
                $message_text = Lang::getNotifText('reminder_7_day');
                echo "Using fallback message: reminder_7_day\n";
            }
            echo Message::sendPackageNotification($c, $p['name_plan'], $price, $message_text, $notif_type) . "\n";
        } else if ($ds['expiration'] == $day3) {
            echo "Sending 3-day reminder to: " . $c['fullname'] . " (" . $c['phonenumber'] . ") via: " . $notif_type . "\n";
            $message_key = 'reminder_3_day' . $service_suffix;
            $message_text = Lang::getNotifText($message_key);
            // Fallback to generic message if service-specific not found
            if (empty($message_text) || $message_text == $message_key) {
                $message_text = Lang::getNotifText('reminder_3_day');
                echo "Using fallback message: reminder_3_day\n";
            }
            echo Message::sendPackageNotification($c, $p['name_plan'], $price, $message_text, $notif_type) . "\n";
        } else if ($ds['expiration'] == $day1) {
            echo "Sending 1-day reminder to: " . $c['fullname'] . " (" . $c['phonenumber'] . ") via: " . $notif_type . "\n";
            $message_key = 'reminder_1_day' . $service_suffix;
            $message_text = Lang::getNotifText($message_key);
            // Fallback to generic message if service-specific not found
            if (empty($message_text) || $message_text == $message_key) {
                $message_text = Lang::getNotifText('reminder_1_day');
                echo "Using fallback message: reminder_1_day\n";
            }
            echo Message::sendPackageNotification($c, $p['name_plan'], $price, $message_text, $notif_type) . "\n";
        }
    }
}