<?php


function BankStkPush_validate_config()
{
    global $config;
    if (empty($config['Stkbankacc']) || empty($config['Stkbankname']) ) {
        sendTelegram("Bank Stk payment gateway not configured");
        r2(U . 'order/balance', 'w', Lang::T("Admin has not yet setup the payment gateway, please tell admin"));
    }
}

function BankStkPush_show_config()
{
    global $ui, $config;
    //$ui->assign('env', json_decode(file_get_contents('system/paymentgateway/kopokopo_env.json'), true));
    $ui->assign('_title', 'Bank Stk Push - ' . $config['CompanyName']);
    $ui->display('bankstkpush.tpl');
}

function BankStkPush_save_config()
{
    global $admin, $_L;
    $bankacc = _post('account');
    $bankname = _post('bankname');
    $d = ORM::for_table('tbl_appconfig')->where('setting', 'Stkbankacc')->find_one();
    if ($d) {
        $d->value = $bankacc;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'Stkbankacc';
        $d->value = $bankacc;
        $d->save();
    }
    $d = ORM::for_table('tbl_appconfig')->where('setting', 'Stkbankname')->find_one();
    if ($d) {
        $d->value = $bankname;
        $d->save();
    } else {
        $d = ORM::for_table('tbl_appconfig')->create();
        $d->setting = 'Stkbankname';
        $d->value = $bankname;
        $d->save();
    }

    _log('[' . $admin['username'] . ']: Stk Bank details ' . $_L['Settings_Saved_Successfully'], 'Admin', $admin['id']);

    r2(U . 'paymentgateway/BankStkPush', 's', $_L['Settings_Saved_Successfully']);
}


function BankStkPush_create_transaction($trx, $user )
{
    
    
  $url=(U. "plugin/initiatebankstk");
    
     $d = ORM::for_table('tbl_payment_gateway')
        ->where('username', $user['username'])
        ->where('status', 1)
        ->find_one();
    $d->gateway_trx_id = '';
    $d->payment_method = 'Bank Stk Push';
    $d->pg_url_payment = $url;
    $d->pg_request = '';
    $d->expired_date = date('Y-m-d H:i:s', strtotime("+5 minutes"));
    $d->save();

    r2(U . "order/view/" . $d['id'], 's', Lang::T("Create Transaction Success, Please click pay now to process payment"));

    die();
    
    
    
    
    

}



function BankStkPush_payment_notification()
{
    $captureLogs = file_get_contents("php://input");
    
    // Debug: Log all incoming data
    file_put_contents('back.log', "\n--- NEW CALLBACK ---\n" . date('Y-m-d H:i:s') . "\n" . $captureLogs . "\n", FILE_APPEND);
    
    $analizzare = json_decode($captureLogs);
    
    if (!$analizzare || !isset($analizzare->Body->stkCallback)) {
        error_log("BankStkPush: Invalid callback data received");
        file_put_contents('back.log', "\nERROR: Invalid callback data\n", FILE_APPEND);
        exit();
    }
    
    $response_code   = $analizzare->Body->stkCallback->ResultCode;
    // Debug: Log the response code
    error_log("BankStkPush: Received response code: " . $response_code);
    file_put_contents('back.log', "\nResponse Code: " . $response_code . "\n", FILE_APPEND);
    
    $resultDesc      = ($analizzare->Body->stkCallback->ResultDesc);
    $merchant_req_id = ($analizzare->Body->stkCallback->MerchantRequestID);
    $checkout_req_id = ($analizzare->Body->stkCallback->CheckoutRequestID);
    
    
        $amount_paid     = ($analizzare->Body->stkCallback->CallbackMetadata->Item['0']->Value);//get the amount value
         $mpesa_code      = ($analizzare->Body->stkCallback->CallbackMetadata->Item['1']->Value);//mpesa transaction code..
         $sender_phone    = ($analizzare->Body->stkCallback->CallbackMetadata->Item['4']->Value);//Telephone Number
        
        
        
       
       
        
        $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
        ->where('checkout', $checkout_req_id)
        ->where('status', 1) // Add this line to filter by status
        ->order_by_desc('id')
        ->find_one();

        $uname=$PaymentGatewayRecord->username;
        
        
            $plan_id=$PaymentGatewayRecord->plan_id;
        
        
        $mac_address=$PaymentGatewayRecord->mac_address;
        
        $user=$PaymentGatewayRecord;


        $userid = ORM::for_table('tbl_customers')
        ->where('username', $uname)
        ->order_by_desc('id')
        ->find_one();

       $userid->username=$uname;
       $userid->save();


       

  $plans = ORM::for_table('tbl_plans')
        ->where('id', $plan_id)
        
        ->order_by_desc('id')
        ->find_one();







  
        
        
       
       if ($response_code=="1032")
         {
         $now = date('Y-m-d H:i:s');   
        $PaymentGatewayRecord->paid_date = $now;
        $PaymentGatewayRecord->status = 4;
        $PaymentGatewayRecord->save();
        
        exit();
            
         }
         
         
       
         
        if($response_code=="1037"){
            
            
       $PaymentGatewayRecord->status = 1;
       $PaymentGatewayRecord->pg_paid_response = 'User failed to enter pin';
        $PaymentGatewayRecord->save();
        
        exit();
            
            
        }
        
         if($response_code=="1"){
            
            
       $PaymentGatewayRecord->status = 1;
       $PaymentGatewayRecord->pg_paid_response = 'Not enough balance';
        $PaymentGatewayRecord->save();
        
        exit();
            
            
        }
        
        
           if($response_code=="2001"){
            
            
       $PaymentGatewayRecord->status = 1;
       $PaymentGatewayRecord->pg_paid_response = 'Wrong Mpesa pin';
        $PaymentGatewayRecord->save();
        
        exit();
            
            
        }
        
          if($response_code=="0"){

            $now = date('Y-m-d H:i:s');
            $date = date('Y-m-d');
            $time = date('H:i:s');

            // ===== ATOMIC DUPLICATE PREVENTION =====
            // Use a direct SQL UPDATE to atomically "claim" this callback.
            // Only the FIRST callback to execute this UPDATE will succeed;
            // subsequent callbacks affect 0 rows and are rejected immediately.
            $db = ORM::get_db();
            $claimed = $db->exec(
                "UPDATE tbl_payment_gateway SET gateway_trx_id = " . $db->quote($mpesa_code) .
                " WHERE id = " . intval($PaymentGatewayRecord->id) .
                " AND (gateway_trx_id = '' OR gateway_trx_id IS NULL)"
            );

            if (!$claimed) {
                // Another callback already claimed and is processing this transaction
                echo "duplicate callback rejected (atomic lock)";
                die;
            }

            // Reload the record after the atomic update to get fresh state
            $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
                ->find_one($PaymentGatewayRecord->id);
            // ===== END ATOMIC DUPLICATE PREVENTION =====

            // Load TransactionDuplicateHelper for logging
            if (file_exists(__DIR__ . '/../helpers/TransactionDuplicateHelper.php')) {
                require_once(__DIR__ . '/../helpers/TransactionDuplicateHelper.php');
            }

            // ⚠️ Custom Balance top-up (plan_id = 0, no plan row) — must be handled
            // separately or $plans->type crashes the callback after payment taken.
            if ($user['routers'] == 'Custom Balance' || !$plans || $plan_id == 0) {
                if (!class_exists('Package', false)) {
                    require_once __DIR__ . '/../autoload/Package.php';
                }
                $rechargeResult = Package::rechargeCustomBalance($userid, null, $user['gateway'], $mpesa_code);
            } else {
                $plan_type = $plans->type;
                $UserId = $userid->id;

                $rechargeResult = Package::rechargeUser($UserId, $user['routers'], $user['plan_id'], $user['gateway'], $mpesa_code);
            }

            if ($rechargeResult === false) {
                // Package activation FAILED - but payment was successful
                $PaymentGatewayRecord->status = 2;
                $PaymentGatewayRecord->paid_date = $now;
                $PaymentGatewayRecord->pg_paid_response = 'Payment successful but package activation failed - please contact support';
                $PaymentGatewayRecord->save();

                error_log("BankStkPush: Payment successful but package activation FAILED for user: " . $PaymentGatewayRecord->username . ", Amount: " . $amount_paid . ", Mpesa Code: " . $mpesa_code);

            } elseif ($rechargeResult === 'duplicate') {
                // Package::rechargeUser detected a duplicate within 60 seconds
                // Payment was successful (money already received), but package already activated
                $PaymentGatewayRecord->status = 2;
                $PaymentGatewayRecord->paid_date = $now;
                $PaymentGatewayRecord->pg_paid_response = 'Payment successful (duplicate callback, package not re-activated)';
                $PaymentGatewayRecord->save();

                error_log("BankStkPush: Duplicate recharge prevented for user: " . $PaymentGatewayRecord->username . ", Amount: " . $amount_paid . ", Mpesa Code: " . $mpesa_code);

                if (class_exists('TransactionDuplicateHelper')) {
                    TransactionDuplicateHelper::logDuplicateAttempt(
                        $PaymentGatewayRecord->username,
                        $plans->name_plan,
                        $amount_paid,
                        'BankStkPush',
                        'Duplicate prevented in BankStkPush callback (rechargeUser returned duplicate)'
                    );
                }

            } else {
                // Package activation / balance top-up SUCCESS
                $isBalanceTopup = ($user['routers'] == 'Custom Balance' || !$plans || $plan_id == 0);
                $PaymentGatewayRecord->status = 2;
                $PaymentGatewayRecord->paid_date = $now;
                $PaymentGatewayRecord->pg_paid_response = $isBalanceTopup
                    ? 'Payment successful - account balance credited'
                    : 'Payment successful and package activated';
                $PaymentGatewayRecord->save();

                error_log("BankStkPush: Payment successful for user: " . $PaymentGatewayRecord->username . ", Amount: " . $amount_paid . ", Mpesa Code: " . $mpesa_code . ($isBalanceTopup ? " (Balance top-up)" : " (Package)"));
            }











              /*
              
              
                  $checkid = ORM::for_table('tbl_customers')
        ->where('username', $username)
        ->find_one();
              
              
              
              
              
              $customerid=$checkid->id;
              
              
              
              
              
              
              
              
             $recharge = ORM::for_table('tbl_user_recharges')->create();
             $recharge->customer_id = $customerid;
             $recharge->username = $PaymentGatewayRecord->username;
             $recharge->plan_id = $PaymentGatewayRecord->plan_id;
             $recharge->price = $amount_paid;
             $recharge->recharged_on = $date;
             $recharge->recharged_time = $time;
             $recharge->expiration = $now;
              $recharge->time = $now;
              $recharge->method = $PaymentGatewayRecord->payment_method;
             $recharge->routers = 0;
             $recharge->Type = 'Balance';
            $recharge->save();
              
              
              
              */
              
              
              
              
            //   $user = ORM::for_table('tbl_customers')
            //   ->where('username', $username)
            //   ->find_one();
              
            //   $currentBalance = $user->balance;
              
            //     $user->balance = $currentBalance + $amount_paid;
            //     $user->save();
              
            //   exit();
             
             
             
         }
        
            
         
            
            
}

function BankStkPush_get_status($trx, $user)
{
    global $config, $routes;

    if ($trx->status == 2) {
        r2(U . "order/view/" . $trx['id'], 's', Lang::T("Transaction has been completed."));
        die();
    } elseif ($trx->status == 1) {
        // Payment is still pending - show pending status
        r2(U . "order/view/" . $trx['id'], 'w', Lang::T("Payment is still pending. Please complete the M-Pesa transaction on your phone."));
        die();
    } elseif ($trx->status == 3) {
        // Payment failed
        r2(U . "order/view/" . $trx['id'], 'e', Lang::T("Payment failed. Please try again."));
        die();
    } elseif ($trx->status == 4) {
        // Payment cancelled
        r2(U . "order/view/" . $trx['id'], 'e', Lang::T("Payment was cancelled."));
        die();
    } else {
        // Unknown status
        r2(U . "order/view/" . $trx['id'], 'w', Lang::T("Payment status unknown. Please contact support."));
        die();
    }
}
