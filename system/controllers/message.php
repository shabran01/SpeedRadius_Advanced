<?php

/**
 *  PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 *  by https://t.me/ibnux
 **/

_admin();
$ui->assign('_title', Lang::T('Send Message'));
$ui->assign('_system_menu', 'message');

$action = $routes['1'];
$ui->assign('_admin', $admin);

if (empty($action)) {
    $action = 'send';
}

switch ($action) {
    case 'send':
        if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin', 'Agent', 'Sales'])) {
            _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
        }

        $select2_customer = <<<EOT
<script>
document.addEventListener("DOMContentLoaded", function(event) {
    $('#personSelect').select2({
        theme: "bootstrap",
        ajax: {
            url: function(params) {
                if(params.term != undefined){
                    return './?_route=autoload/customer_select2&s='+params.term;
                }else{
                    return './?_route=autoload/customer_select2';
                }
            }
        }
    });
});
</script>
EOT;
        if (isset($routes['2']) && !empty($routes['2'])) {
            $ui->assign('cust', ORM::for_table('tbl_customers')->find_one($routes['2']));
        }
        $id = $routes['2'];
        $ui->assign('id', $id);
        $ui->assign('xfooter', $select2_customer);
        $ui->display('message.tpl');
        break;

    case 'send-post':
        // Check user permissions
        if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin', 'Agent', 'Sales'])) {
            _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
        }

        // Get form data
        $id_customer = $_POST['id_customer'];
        $message = $_POST['message'];
        $via = $_POST['via'];

        // Check if fields are empty
        if ($id_customer == '' or $message == '' or $via == '') {
            r2(U . 'message/send', 'e', Lang::T('All field is required'));
        } else {
            // Get customer details from the database
            $c = ORM::for_table('tbl_customers')->find_one($id_customer);

            // Replace placeholders in the message with actual values
            $message = str_replace('[[name]]', $c['fullname'], $message);
            $message = str_replace('[[user_name]]', $c['username'], $message);
            $message = str_replace('[[phone]]', $c['phonenumber'], $message);
            $message = str_replace('[[company_name]]', $config['CompanyName'], $message);


            //Send the message
            if ($via == 'sms' || $via == 'both') {
                $smsSent = Message::sendSMS($c['phonenumber'], $message);
            }

            if ($via == 'wa' || $via == 'both') {
                $waSent = Message::sendWhatsapp($c['phonenumber'], $message);
            }

            if (isset($smsSent) || isset($waSent)) {
                r2(U . 'message/send', 's', Lang::T('Message Sent Successfully'));
            } else {
                r2(U . 'message/send', 'e', Lang::T('Failed to send message'));
            }
        }
        break;

    case 'send_bulk':
        if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin', 'Agent', 'Sales'])) {
            _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
        }

        if(_post('send') != 'now') {
            // Get list of routers for the dropdown
            $routers = ORM::for_table('tbl_routers')->select('name')->find_array();
            $ui->assign('routers', $routers);
            $ui->display('message-bulk.tpl');
            break;
        }

        // Get form data
        $group = _post('group');
        $message = _post('message');
        $via = _post('via');
        $test = isset($_POST['test']) && $_POST['test'] === 'on' ? 'yes' : 'no';
        $batch = _post('batch');
        $delay = _post('delay');
        $router = _post('router');

        // Initialize counters
        $totalSMSSent = 0;
        $totalSMSFailed = 0;
        $totalWhatsappSent = 0;
        $totalWhatsappFailed = 0;
        $batchStatus = [];

        // Check if fields are empty
        if ($group == '' || $message == '' || $via == '') {
            r2(U . 'message/send_bulk', 'e', Lang::T('All fields are required'));
        } else {
            // Initialize base query
            $query = ORM::for_table('tbl_customers');
            
            // Add router filter if specified
            if(!empty($router)) {
                $query->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                    ->where('tbl_user_recharges.routers', $router)
                    ->where('tbl_user_recharges.status', 'on');
            }

            // Apply group filters
            switch($group) {
                case 'all':
                    // No additional filter needed
                    break;
                case 'new':
                    // Get customers created just a month ago
                    $query->where_raw("DATE(tbl_customers.created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)");
                    break;
                case 'expired':
                    // Get expired user recharges where status is 'off'
                    if(empty($router)) {
                        $query->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                            ->where('tbl_user_recharges.status', 'off');
                    }
                    break;
                case 'active':
                    // Get active user recharges where status is 'on'
                    if(empty($router)) {
                        $query->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                            ->where('tbl_user_recharges.status', 'on');
                    }
                    break;
                case 'active_pppoe':
                    // Get active PPPOE customers
                    if(empty($router)) {
                        $query->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                            ->where('tbl_user_recharges.status', 'on')
                            ->where('tbl_user_recharges.type', 'PPPOE');
                    } else {
                        $query->where('tbl_user_recharges.type', 'PPPOE');
                    }
                    break;
                case 'active_hotspot':
                    // Get active Hotspot customers
                    if(empty($router)) {
                        $query->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                            ->where('tbl_user_recharges.status', 'on')
                            ->where('tbl_user_recharges.type', 'Hotspot');
                    } else {
                        $query->where('tbl_user_recharges.type', 'Hotspot');
                    }
                    break;
            }

            // Select only necessary fields and ensure no duplicates
            $query->select('tbl_customers.*')
                  ->group_by('tbl_customers.id');

            $customers = $query->find_array();

            // Set the batch size
            $batchSize = $batch;

            // Calculate the number of batches
            $totalCustomers = count($customers);
            $totalBatches = ceil($totalCustomers / $batchSize);

            // Loop through batches
            for ($batchIndex = 0; $batchIndex < $totalBatches; $batchIndex++) {
                // Get the starting and ending index for the current batch
                $start = $batchIndex * $batchSize;
                $end = min(($batchIndex + 1) * $batchSize, $totalCustomers);
                $batchCustomers = array_slice($customers, $start, $end - $start);

                // Loop through customers in the current batch and send messages
                foreach ($batchCustomers as $customer) {
                    // Create a copy of the original message for each customer
                    $currentMessage = $message;
                    $currentMessage = str_replace('[[name]]', $customer['fullname'], $currentMessage);
                    $currentMessage = str_replace('[[user_name]]', $customer['username'], $currentMessage);
                    $currentMessage = str_replace('[[phone]]', $customer['phonenumber'], $currentMessage);
                    $currentMessage = str_replace('[[company_name]]', $config['CompanyName'], $currentMessage);

                    if ($test === 'yes') {
                        // Test mode - don't actually send messages
                        $batchStatus[] = [
                            'name' => $customer['fullname'],
                            'phone' => $customer['phonenumber'],
                            'message' => $currentMessage,
                            'status' => 'Test Mode - Message not sent'
                        ];
                        continue;
                    }

                    $messageSent = false;

                    // Send actual messages
                    if ($via == 'sms' || $via == 'both') {
                        $smsSent = Message::sendSMS($customer['phonenumber'], $currentMessage);
                        if ($smsSent) {
                            $totalSMSSent++;
                            $messageSent = true;
                            if ($via != 'both') {
                                $batchStatus[] = [
                                    'name' => $customer['fullname'],
                                    'phone' => $customer['phonenumber'],
                                    'message' => $currentMessage,
                                    'status' => 'SMS Message Sent'
                                ];
                            }
                        } else {
                            $totalSMSFailed++;
                            if ($via != 'both') {
                                $batchStatus[] = [
                                    'name' => $customer['fullname'],
                                    'phone' => $customer['phonenumber'],
                                    'message' => $currentMessage,
                                    'status' => 'SMS Message Failed'
                                ];
                            }
                        }
                    }

                    if ($via == 'wa' || $via == 'both') {
                        $waSent = Message::sendWhatsapp($customer['phonenumber'], $currentMessage);
                        if ($waSent) {
                            $totalWhatsappSent++;
                            $messageSent = true;
                            if ($via != 'both') {
                                $batchStatus[] = [
                                    'name' => $customer['fullname'],
                                    'phone' => $customer['phonenumber'],
                                    'message' => $currentMessage,
                                    'status' => 'WhatsApp Message Sent'
                                ];
                            }
                        } else {
                            $totalWhatsappFailed++;
                            if ($via != 'both') {
                                $batchStatus[] = [
                                    'name' => $customer['fullname'],
                                    'phone' => $customer['phonenumber'],
                                    'message' => $currentMessage,
                                    'status' => 'WhatsApp Message Failed'
                                ];
                            }
                        }
                    }

                    // For 'both' mode, only add one status entry
                    if ($via == 'both') {
                        $status = 'Failed';
                        if ($messageSent) {
                            $status = ($smsSent && $waSent) ? 'Both Messages Sent' :
                                     ($smsSent ? 'SMS Sent, WhatsApp Failed' : 
                                     ($waSent ? 'WhatsApp Sent, SMS Failed' : 'Both Messages Failed'));
                        }
                        $batchStatus[] = [
                            'name' => $customer['fullname'],
                            'phone' => $customer['phonenumber'],
                            'message' => $currentMessage,
                            'status' => $status
                        ];
                    }

                    // Apply delay between messages if specified
                    if ($delay > 0) {
                        sleep($delay);
                    }
                }
            }
        }

        $ui->assign('batchStatus', $batchStatus);
        $ui->assign('totalSMSSent', $totalSMSSent);
        $ui->assign('totalSMSFailed', $totalSMSFailed);
        $ui->assign('totalWhatsappSent', $totalWhatsappSent);
        $ui->assign('totalWhatsappFailed', $totalWhatsappFailed);
        $ui->display('message-bulk.tpl');
        break;

    default:
        r2(U . 'message/send_sms', 'e', 'action not defined');
}
