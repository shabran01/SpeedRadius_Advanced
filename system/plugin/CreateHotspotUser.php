<?php
function Alloworigins()
{
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit;
    }
    
    $requestUri = $_SERVER['REQUEST_URI'];
    $queryString = parse_url($requestUri, PHP_URL_QUERY);
    
    // Only intercept requests specifically for this plugin
    if ($queryString) {
        parse_str($queryString, $queryParameters);
        
        // Check if this is a request for this specific plugin
        if (isset($queryParameters['_route']) && $queryParameters['_route'] === 'plugin/CreateHotspotuser') {
            $type = isset($queryParameters['type']) ? $queryParameters['type'] : null;
            
            if ($type === "grant") {
                CreateHostspotUser();
                exit;
            } elseif ($type === "verify") {
                VerifyHotspot();
                exit;
            } elseif ($type === "reconnect") {
                ReconnectUser();
                exit;
            } elseif ($type === "voucher") {
                ReconnectVoucher();
                exit;
            } elseif ($type === "mpesa_reconnect") {
                mpesa_reconnect();
                exit;
            } else {
                echo json_encode(['status' => 'error', 'code' => 400, 'message' => 'The parameter is not present in the URL.']);
            }
        }
    }
}

function ReconnectVoucher() {
    header('Content-Type: application/json');

    $rawData = file_get_contents('php://input');
    $postData = json_decode($rawData, true);

    if (!isset($postData['voucher_code'], $postData['account_id'])) {
        echo json_encode([
            'status' => 'error', 
            'code' => 400,
            'message' => 'Missing accountId or voucherCode field'
        ]);
        return;
    }

    $accountId = $postData['account_id'];
    $voucherCode = $postData['voucher_code'];

    // First check if this account ID has any active sessions
    $activeUser = ORM::for_table('tbl_user_recharges')
        ->where('username', $accountId)
        ->where('status', 'on')
        ->find_one();

    if ($activeUser) {
        // Check if session is from same voucher
        $voucher = ORM::for_table('tbl_voucher')
            ->where('code', $voucherCode)
            ->find_one();
            
        if ($voucher && $voucher['user'] == $accountId) {
            echo json_encode([
                'status' => 'success',
                'Resultcode' => '2',
                'voucher' => 'active',
                'message' => 'Your session is still active',
                'username' => $accountId
            ]);
            exit();
        }
    }

    $voucher = ORM::for_table('tbl_voucher')
        ->where('code', $voucherCode)
        ->where('status', '0')
        ->find_one();

    if (!$voucher) {
        echo json_encode([
            'status' => 'error',
            'Resultcode' => '1',
            'voucher' => 'Not Found',
            'message' => 'Invalid Voucher code'
        ]);
        exit();
    }

    if ($voucher['status'] == '1') {
        echo json_encode([
            'status' => 'error',
            'Resultcode' => '3',
            'voucher' => 'Used',
            'message' => 'Voucher code is already used'
        ]);
        exit();
    }

    $planId = $voucher['id_plan'];
    $routername = $voucher['routers'];

    $router = ORM::for_table('tbl_routers')
        ->where('name', $routername)
        ->find_one();

    if (!$router) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Router not found'
        ]);
        exit();
    }

    $routerId = $router['id'];

    if (!ORM::for_table('tbl_plans')->where('id', $planId)->count() || !ORM::for_table('tbl_routers')->where('id', $routerId)->count()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unable to process your request, please refresh the page'
        ]);
        exit();
    }

    $user = ORM::for_table('tbl_customers')->where('username', $accountId)->find_one();
    if (!$user) {
        // Create a new user if not exists
        $user = ORM::for_table('tbl_customers')->create();
        $user->username = $accountId;
        $user->password = '1234';
        $user->fullname = $accountId;
        $user->email = $accountId . '@gmail.com';
        $user->phonenumber = $accountId;
        $user->pppoe_password = '1234';
        $user->address = '';
        $user->service_type = 'Hotspot';
    }

    $user->router_id = $routerId;
    $user->save();

    // Update the voucher with the user ID
    $voucher->user = $user->id;
    $voucher->status = '1';  // Mark as used
    $voucher->save();

    if (Package::rechargeUser($user->id, $routername, $planId, 'Voucher', $voucherCode)) {
        echo json_encode([
            'status' => 'success',
            'Resultcode' => '2',
            'voucher' => 'activated',
            'message' => 'Voucher code has been activated',
            'username' => $user->username,
            'persistId' => true // Flag to indicate the frontend should persist this ID
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to recharge user package'
        ]);
    }
}

function ReconnectUser()
{
    header('Content-Type: application/json');
    $rawData = file_get_contents('php://input');
    $postData = json_decode($rawData, true);
    if (!$postData) {
        echo json_encode(['status' => 'error', 'code' => 400, 'message' => 'Invalid JSON DATA']);
        exit();
    }

    if (!isset($postData['mpesa_code'])) {
        echo json_encode(['status' => 'error', 'code' => 400, 'message' => 'missing required fields']);
        exit();
    }

    $mpesaCode = $postData['mpesa_code'];

    // Query the payment gateway table
    $payment = ORM::for_table('tbl_payment_gateway')
        ->where('gateway_trx_id', $mpesaCode)
        ->find_one();

    if (!$payment) {
        $data = array(['status' => 'error', "Resultcode" => "1", 'user' => "Not Found", 'message' => 'Invalid Mpesa Transaction code']);
        echo json_encode($data);
        exit();
    }

    $username = $payment['username'];

    // Query the user recharges table
    $recharge = ORM::for_table('tbl_user_recharges')
        ->where('username', $username)
        ->order_by_desc('id')
        ->find_one();

    if ($recharge) {
        $status = $recharge['status'];
        if ($status == 'on') {
            $data = array(
                "Resultcode" => "2",
                "user" => "Active User",
                "username" => $username,
                "tyhK" => "1234", // Replace with the actual password or token
                "Message" => "We have verified your transaction under the Mpesa Transaction $mpesaCode. Please don't leave this page as we are redirecting you.",
                "Status" => "success"
            );
        } elseif ($status == "off") {
            $data = array(
                "Resultcode" => "3",
                "user" => "Expired User",
                "Message" => "We have verified your transaction under the Mpesa Transaction $mpesaCode. But your Package is already Expired. Please buy a new Package.",
                "Status" => "danger"
            );
        } else {
            $data = array(
                "Message" => "Unexpected status value",
                "Status" => "error"
            );
        }
    } else {
        $data = array(
            "Message" => "Recharge information not found",
            "Status" => "error"
        );
    }

    echo json_encode($data);
    exit();
}


function VerifyHotspot() {
    header('Content-Type: application/json');
    $rawData = file_get_contents('php://input');
    $postData = json_decode($rawData, true);

    if (!$postData) {
        echo json_encode(['Resultcode' => 'error', 'Message' => 'Invalid JSON data']);
        return;
    }

    if (!isset($postData['account_id'])) {
        echo json_encode(['Resultcode' => 'error', 'Message' => 'Missing required fields']);
        return;
    }

    $accountId = $postData['account_id'];
    $user = ORM::for_table('tbl_payment_gateway')
        ->where('username', $accountId)
        ->order_by_desc('id')
        ->find_one();

    if ($user) {
        $status = $user->status;
        $mpesacode = $user->gateway_trx_id;
        $res = $user->pg_paid_response;

        if ($status == 2 && !empty($mpesacode)) {
            echo json_encode([
                "Resultcode" => "3",
                "Message" => "We have received your transaction under the Mpesa Transaction $mpesacode. Please do not leave this page as we are redirecting you.",
                "Status" => "success"
            ]);
        } elseif ($res == "Not enough balance") {
            echo json_encode([
                "Resultcode" => "2",
                "Message" => "Insufficient Balance for the transaction",
                "Status" => "danger"
            ]);
        } elseif ($res == "Wrong Mpesa pin") {
            echo json_encode([
                "Resultcode" => "2",
                "Message" => "You entered Wrong Mpesa pin, please resubmit",
                "Status" => "danger"
            ]);
        } elseif ($status == 4) {
            echo json_encode([
                "Resultcode" => "2",
                "Message" => "You cancelled the transaction, you can enter phone number again to activate",
                "Status" => "info"
            ]);
        } elseif (empty($mpesacode)) {
            echo json_encode([
                "Resultcode" => "1",
                "Message" => "A payment pop up has been sent to your phone. Please enter PIN to continue (Please do not leave or reload the page until redirected).",
                "Status" => "primary"
            ]);
        }
    } else {
        echo json_encode([
            "Resultcode" => "error",
            "Message" => "User not found"
        ]);
    }
}



function CreateHostspotUser()
{
    header('Content-Type: application/json');
    $rawData = file_get_contents('php://input');
    $postData = json_decode($rawData, true);
    if (!$postData) {
        echo json_encode(['status' => 'error', 'code' => 400, 'message' => 'Invalid JSON DATA' . $postData . ' n tes ']);
    } else {
        $phone = $postData['phone_number'];
        $planId = $postData['plan_id'];
        $routerId = $postData['router_id'];
		  $accountId = $postData['account_id'];



        if (!isset( $postData['phone_number'], $postData['plan_id'], $postData['router_id'], $postData['account_id'])) {
            echo json_encode(['status' => 'error', 'code' => 400, 'message' => 'missing required fields' . $postData,  'phone' => $phone,  'planId' => $planId, 'routerId' => $routerId, 'accountId' => $accountId]);
        } else {
            $phone = (substr($phone, 0, 1) == '+') ? str_replace('+', '', $phone) : $phone;
            $phone = (substr($phone, 0, 1) == '0') ? preg_replace('/^0/', '254', $phone) : $phone;
            $phone = (substr($phone, 0, 1) == '7') ? preg_replace('/^7/', '2547', $phone) : $phone; //cater for phone number prefix 2547XXXX
            $phone = (substr($phone, 0, 1) == '1') ? preg_replace('/^1/', '2541', $phone) : $phone; //cater for phone number prefix 2541XXXX
            $phone = (substr($phone, 0, 1) == '0') ? preg_replace('/^01/', '2541', $phone) : $phone;
            $phone = (substr($phone, 0, 1) == '0') ? preg_replace('/^07/', '2547', $phone) : $phone;
            if (strlen($phone) !== 12) {
                echo json_encode(['status' => 'error', 'code' => 1, 'message' => 'Phone number ' . $phone . ' is invalid. Please confirm.']);
            }
            if (strlen($phone) == 12 && !empty($planId) && !empty($routerId)) {
                $PlanExist = ORM::for_table('tbl_plans')->where('id', $planId)->count() > 0;
                $RouterExist = ORM::for_table('tbl_routers')->where('id', $routerId)->count() > 0;
                if (!$PlanExist || !$RouterExist)
                    echo json_encode(["status" => "error", "message" => "Unable to process your request, please refresh the page."]);
            }
            $Userexist = ORM::for_table('tbl_customers')->where('username', $accountId)->find_one();
            if ($Userexist) {
                $Userexist->router_id = $routerId;
                $Userexist->save();
                InitiateStkpush($phone, $planId, $accountId, $routerId);
            } else {
                try {
                    $defpass = '1234';
                    $defaddr = 'SpeedRadius';
                    $defmail = $phone . '@gmail.com';
                    $createUser = ORM::for_table('tbl_customers')->create();
                    $createUser->username = $accountId;
                    $createUser->password = $defpass;
                    $createUser->fullname = $phone;
                    $createUser->router_id = $routerId;
                    $createUser->phonenumber = $phone;
                    $createUser->pppoe_password = $defpass;
                    $createUser->address = $defaddr;
                    $createUser->email = $defmail;
                    $createUser->service_type = 'Hotspot';
                    if ($createUser->save()) {
                        InitiateStkpush($phone, $planId, $accountId, $routerId);
                    } else {
                        echo json_encode(["status" => "error", "message" => "There was a system error when registering user, please contact support."]);
                    }
                } catch (Exception $e) {
                    echo json_encode(["status" => "error", "message" => "Error creating user: " . $e->getMessage()]);
                }
            }
        }
    }
}


function InitiateStkpush($phone, $planId, $accountId, $routerId)
{
    $gateway = ORM::for_table('tbl_appconfig')
        ->where('setting', 'payment_gateway')
        ->find_one();
    $gateway = ($gateway) ? $gateway->value : null;
    if ($gateway == "MpesatillStk") {
        $url = U . "plugin/initiatetillstk";
    } elseif ($gateway == "BankStkPush") {
        $url = U . "plugin/initiatebankstk";
    } elseif ($gateway == "PaybillStk") {
        $url = U . "plugin/initiatePaybillStk";
    } elseif ($gateway == "mpesa") {
        $url = U . "plugin/initiatempesa";
    } else {
        $url = null; // or handle the default case appropriately
    }
    $Planname = ORM::for_table('tbl_plans')
        ->where('id', $planId)
        ->order_by_desc('id')
        ->find_one();
    $Findrouter = ORM::for_table('tbl_routers')
        ->where('id', $routerId)
        ->order_by_desc('id')
        ->find_one();
    $rname = $Findrouter->name;
    $price = $Planname->price;
    $Planname = $Planname->name_plan;
    $Checkorders = ORM::for_table('tbl_payment_gateway')
        ->where('username', $accountId)
        ->where('status', 1)
        ->order_by_desc('id')
        ->find_many();
    if ($Checkorders) {
        foreach ($Checkorders as $Dorder) {
            $Dorder->delete();
        }
    }
    try {
        $d = ORM::for_table('tbl_payment_gateway')->create();
        $d->username = $accountId;
        $d->gateway = $gateway;
        $d->plan_id = $planId;
        $d->plan_name = $Planname;
        $d->routers_id = $routerId;
        $d->routers = $rname;
        $d->price = $price;
        $d->payment_method = $gateway;
        $d->payment_channel = $gateway;
        $d->created_date = date('Y-m-d H:i:s');
        $d->paid_date = date('Y-m-d H:i:s');
        $d->expired_date = date('Y-m-d H:i:s');
        $d->pg_url_payment = $url;
        $d->status = 1;
        $d->save();
    } catch (Exception $e) {
        error_log('Error saving payment gateway record: ' . $e->getMessage());
        throw $e;
    }
    SendSTKcred($phone, $url, $accountId);
}

function SendSTKcred($phone, $url, $accountId )
{
    $link = $url;
    $fields = array(
        'username' => $accountId,
        'phone' => $phone,
        'channel' => 'Yes',
    );
    $postvars = http_build_query($fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
    $result = curl_exec($ch);
}

function mpesa_reconnect() {
    header('Content-Type: application/json');
    $rawData = file_get_contents('php://input');
    $postData = json_decode($rawData, true);

    if (!$postData) {
        echo json_encode(['Resultcode' => '2', 'Message' => 'Invalid request data', 'Status' => 'danger']);
        exit();
    }

    if (!isset($postData['mpesa_code'])) {
        echo json_encode(['Resultcode' => '2', 'Message' => 'MPesa code is required', 'Status' => 'danger']);
        exit();
    }

    $mpesaCode = $postData['mpesa_code'];

    // First check if the transaction exists and is completed
    $transaction = ORM::for_table('tbl_payment_gateway')
        ->where('gateway_trx_id', $mpesaCode)
        ->find_one();

    if (!$transaction) {
        echo json_encode([
            'Resultcode' => '2',
            'user' => 'Invalid',
            'Message' => 'This MPesa code was not found in our system. Please check and try again.',
            'Status' => 'danger'
        ]);
        exit();
    }

    // Check if the transaction is completed (status = 2)
    if ($transaction->status != 2) {
        // Debug: Log what status we found
        error_log("MPesa Reconnect: Transaction status is " . $transaction->status . " for code " . $mpesaCode);
        
        $statusMessages = [
            '0' => 'Transaction is pending. Please wait.',
            '1' => 'Transaction failed. Please try making a new payment.',
            '3' => 'Transaction was cancelled. Please make a new payment.',
            '4' => 'Transaction timed out. Please make a new payment.'
        ];
        $message = isset($statusMessages[$transaction->status]) 
            ? $statusMessages[$transaction->status] 
            : 'Transaction was not successful. Please make a new payment.';
            
        echo json_encode([
            'Resultcode' => '2',
            'user' => 'Invalid',
            'Message' => $message,
            'Status' => 'danger'
        ]);
        exit();
    }

    // Get user details
    $username = $transaction->username;
    $userid = ORM::for_table('tbl_customers')
        ->where('username', $username)
        ->find_one();

    if (!$userid) {
        echo json_encode([
            'Resultcode' => '2',
            'user' => 'Not Found',
            'Message' => 'No user account was found for this transaction. Please contact support.',
            'Status' => 'danger'
        ]);
        exit();
    }

    // Check current session status - Find session that matches this transaction
    $current_session = ORM::for_table('tbl_user_recharges')
        ->where('username', $username)
        ->where('method', $mpesaCode)
        ->order_by_desc('id')
        ->find_one();

    // If no session found with the M-Pesa code, try to find the latest session
    if (!$current_session) {
        $current_session = ORM::for_table('tbl_user_recharges')
            ->where('username', $username)
            ->order_by_desc('id')
            ->find_one();
    }

    if ($current_session) {
        $now = date('Y-m-d H:i:s');
        $date_now = strtotime($now);
        
        // Get the package duration from plans table
        $plan = ORM::for_table('tbl_plans')
            ->find_one($transaction->plan_id);
            
        if (!$plan) {
            echo json_encode([
                'Resultcode' => '2',
                'user' => 'Error',
                'Message' => 'Could not find the package details. Please contact support.',
                'Status' => 'danger'
            ]);
            exit();
        }

        // Check if the package has expired using the actual expiry date from user recharges
        $expiry_time = strtotime($current_session->expiry_date);
        
        // If expiry_date is null or invalid, calculate it from transaction time and plan duration
        if (!$expiry_time || $current_session->expiry_date === null) {
            $transaction_time = strtotime($transaction->paid_date);
            $plan_duration_mins = intval($plan->validity);
            $expiry_time = $transaction_time + ($plan_duration_mins * 60);
        }
        
        // Debug information - add to response for troubleshooting
        $debug_info = [
            'mpesa_code' => $mpesaCode,
            'username' => $username,
            'current_time_str' => date('Y-m-d H:i:s', $date_now),
            'current_time_timestamp' => $date_now,
            'expiry_date_str' => $current_session->expiry_date,
            'expiry_time_timestamp' => $expiry_time,
            'expiry_formatted' => date('Y-m-d H:i:s', $expiry_time),
            'is_expired' => ($date_now > $expiry_time),
            'time_difference' => ($expiry_time - $date_now),
            'session_status' => $current_session->status,
            'transaction_paid_date' => $transaction->paid_date,
            'plan_validity_mins' => $plan->validity,
            'calculated_expiry' => ($current_session->expiry_date === null)
        ];
        
        // If the package has expired, don't allow reconnection
        if ($date_now > $expiry_time) {
            echo json_encode([
                'Resultcode' => '2',
                'user' => 'Expired Package',
                'Message' => 'This package has expired. You cannot reconnect using this MPesa code. Please purchase a new package.',
                'Status' => 'danger',
                'debug' => $debug_info  // Add debug info to response
            ]);
            exit();
        }

        // Check if someone else is using this account
        $active_sessions = ORM::for_table('tbl_user_recharges')
            ->where('username', $username)
            ->where('status', 'on')
            ->where_not_equal('id', $current_session->id)
            ->count();

        if ($active_sessions > 0) {
            echo json_encode([
                'Resultcode' => '2',
                'user' => 'In Use',
                'Message' => 'This account is currently active on another device. Please disconnect from other device first.',
                'Status' => 'warning'
            ]);
            exit();
        }

        // Simply activate the existing session and communicate with router
        try {
            $remaining_time = max(0, $expiry_time - $date_now);
            $remaining_mins = ceil($remaining_time / 60);
            
            // Update the current session status to 'on'
            $current_session->status = 'on';
            $current_session->save();
            
            // Also need to use Package::rechargeUser to properly activate on router
            // This ensures the router/hotspot system knows about the reconnection
            if (Package::rechargeUser($userid->id, $transaction->routers, $transaction->plan_id, $transaction->gateway, $mpesaCode)) {
                echo json_encode([
                    'Resultcode' => '3',
                    'user' => 'Reconnected',
                    'username' => $username,
                    'tyhK' => '1234',
                    'Message' => 'MPesa code verified. Your session has been reactivated with remaining time: ' . $remaining_mins . ' minutes.',
                    'Status' => 'success',
                    'debug' => $debug_info  // Add debug info to success response too
                ]);
            } else {
                // Fallback: just database update worked
                echo json_encode([
                    'Resultcode' => '3',
                    'user' => 'Reconnected',
                    'username' => $username,
                    'tyhK' => '1234',
                    'Message' => 'Session reactivated in database. If connection issues persist, please try again or contact support.',
                    'Status' => 'success',
                    'debug' => $debug_info
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'Resultcode' => '2',
                'user' => 'Error',
                'Message' => 'System error occurred: ' . $e->getMessage(),
                'Status' => 'danger'
            ]);
        }
    } else {
        echo json_encode([
            'Resultcode' => '2',
            'user' => 'No Session',
            'Message' => 'No active or expired session found for this MPesa code. Please make a new purchase.',
            'Status' => 'danger'
        ]);
        exit();
    }
}

Alloworigins();
