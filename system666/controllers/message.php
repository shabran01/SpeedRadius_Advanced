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

        // Get list of routers for the dropdown
        $routers = ORM::for_table('tbl_routers')->select('name')->find_array();
        $ui->assign('routers', $routers);
        $ui->display('message-bulk.tpl');
        break;

    case 'send_bulk_process':
        // AJAX endpoint — sends one batch at a time, returns JSON
        if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin', 'Agent', 'Sales'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Permission denied']);
            exit;
        }

        header('Content-Type: application/json');

        $group   = _post('group');
        $message = _post('message');
        $via     = _post('via');
        $test    = isset($_POST['test']) && $_POST['test'] === 'on' ? 'yes' : 'no';
        $batch   = max(1, (int) _post('batch'));
        $offset  = max(0, (int) _post('offset'));
        $router  = _post('router');

        if ($group == '' || $message == '' || $via == '') {
            echo json_encode(['error' => 'All fields are required']);
            exit;
        }

        // Helper to apply group/router filters to a query
        $applyFilters = function($q) use ($group, $router) {
            // Expired groups should NOT force status='on' even when a router is selected
            $expiredGroups = ['expired', 'expired_pppoe', 'expired_hotspot'];
            if (!empty($router)) {
                $q->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                    ->where('tbl_user_recharges.routers', $router);
                if (!in_array($group, $expiredGroups)) {
                    $q->where('tbl_user_recharges.status', 'on');
                }
            }
            switch ($group) {
                case 'new':
                    $q->where_raw("DATE(tbl_customers.created_at) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)");
                    break;
                case 'expired':
                    if (empty($router)) {
                        $q->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                            ->where('tbl_user_recharges.status', 'off');
                    }
                    break;
                case 'active':
                    if (empty($router)) {
                        $q->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                            ->where('tbl_user_recharges.status', 'on');
                    }
                    break;
                case 'active_pppoe':
                    if (empty($router)) {
                        $q->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                            ->where('tbl_user_recharges.status', 'on')
                            ->where('tbl_user_recharges.type', 'PPPOE');
                    } else {
                        $q->where('tbl_user_recharges.type', 'PPPOE');
                    }
                    break;
                case 'expired_pppoe':
                    if (empty($router)) {
                        $q->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                            ->where('tbl_user_recharges.status', 'off')
                            ->where('tbl_user_recharges.type', 'PPPOE');
                    } else {
                        $q->where('tbl_user_recharges.status', 'off')
                          ->where('tbl_user_recharges.type', 'PPPOE');
                    }
                    break;
                case 'all_pppoe':
                    if (empty($router)) {
                        $q->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                            ->where('tbl_user_recharges.type', 'PPPOE');
                    } else {
                        $q->where('tbl_user_recharges.type', 'PPPOE');
                    }
                    break;
                case 'active_hotspot':
                    if (empty($router)) {
                        $q->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                            ->where('tbl_user_recharges.status', 'on')
                            ->where('tbl_user_recharges.type', 'Hotspot');
                    } else {
                        $q->where('tbl_user_recharges.type', 'Hotspot');
                    }
                    break;
                case 'expired_hotspot':
                    if (empty($router)) {
                        $q->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                            ->where('tbl_user_recharges.status', 'off')
                            ->where('tbl_user_recharges.type', 'Hotspot');
                    } else {
                        $q->where('tbl_user_recharges.status', 'off')
                          ->where('tbl_user_recharges.type', 'Hotspot');
                    }
                    break;
                case 'all_hotspot':
                    if (empty($router)) {
                        $q->join('tbl_user_recharges', array('tbl_customers.id', '=', 'tbl_user_recharges.customer_id'))
                            ->where('tbl_user_recharges.type', 'Hotspot');
                    } else {
                        $q->where('tbl_user_recharges.type', 'Hotspot');
                    }
                    break;
            }
            return $q;
        };

        // COUNT with DISTINCT to correctly handle JOINs (avoids GROUP BY breaking count())
        $total = $applyFilters(ORM::for_table('tbl_customers'))
            ->select_expr('COUNT(DISTINCT `tbl_customers`.`id`)', 'cnt')
            ->find_one();
        $total = ($total !== false && isset($total->cnt)) ? (int)$total->cnt : 0;

        // Data fetch: add SELECT + GROUP BY only here to avoid duplicate rows from JOINs
        $customers = $applyFilters(ORM::for_table('tbl_customers'))
            ->select('tbl_customers.*')
            ->group_by('tbl_customers.id')
            ->limit($batch)->offset($offset)->find_array();

        $results    = [];
        $smsSent    = 0;
        $smsFailed  = 0;
        $waSent     = 0;
        $waFailed   = 0;

        foreach ($customers as $customer) {
            $currentMessage = $message;
            $currentMessage = str_replace('[[name]]', $customer['fullname'], $currentMessage);
            $currentMessage = str_replace('[[user_name]]', $customer['username'], $currentMessage);
            $currentMessage = str_replace('[[phone]]', $customer['phonenumber'], $currentMessage);
            $currentMessage = str_replace('[[company_name]]', $config['CompanyName'], $currentMessage);

            if ($test === 'yes') {
                $results[] = [
                    'name'    => $customer['fullname'],
                    'phone'   => $customer['phonenumber'],
                    'message' => $currentMessage,
                    'status'  => 'Test Mode'
                ];
                continue;
            }

            $smsOk = false;
            $waOk  = false;

            if ($via == 'sms' || $via == 'both') {
                $smsOk = Message::sendSMS($customer['phonenumber'], $currentMessage);
                if ($smsOk) { $smsSent++; } else { $smsFailed++; }
            }
            if ($via == 'wa' || $via == 'both') {
                $waOk = Message::sendWhatsapp($customer['phonenumber'], $currentMessage);
                if ($waOk) { $waSent++; } else { $waFailed++; }
            }

            $status = 'Failed';
            if ($via == 'sms') {
                $status = $smsOk ? 'SMS Sent' : 'SMS Failed';
            } elseif ($via == 'wa') {
                $status = $waOk ? 'WhatsApp Sent' : 'WhatsApp Failed';
            } elseif ($via == 'both') {
                if ($smsOk && $waOk) $status = 'Both Sent';
                elseif ($smsOk) $status = 'SMS Sent, WhatsApp Failed';
                elseif ($waOk) $status = 'WhatsApp Sent, SMS Failed';
                else $status = 'Both Failed';
            }

            $results[] = [
                'name'    => $customer['fullname'],
                'phone'   => $customer['phonenumber'],
                'message' => $currentMessage,
                'status'  => $status
            ];
        }

        $processed = $offset + count($customers);
        echo json_encode([
            'total'      => $total,
            'processed'  => $processed,
            'done'       => $processed >= $total,
            'results'    => $results,
            'smsSent'    => $smsSent,
            'smsFailed'  => $smsFailed,
            'waSent'     => $waSent,
            'waFailed'   => $waFailed,
        ]);
        exit;

    default:
        r2(U . 'message/send_sms', 'e', 'action not defined');
}
