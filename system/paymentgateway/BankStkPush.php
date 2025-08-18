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
                  $time= date('H:i:s');






   $check_mpesa = ORM::for_table('tbl_payment_gateway')
        ->where('gateway_trx_id', $mpesa_code)
        ->find_one();

    // Load TransactionDuplicateHelper
    if (file_exists(__DIR__ . '/../helpers/TransactionDuplicateHelper.php')) {
        require_once(__DIR__ . '/../helpers/TransactionDuplicateHelper.php');
    }

if($check_mpesa){
    
    echo "double callback, ignore one";
    
    die;
    
    
}

// Additional duplicate check using helper
if (class_exists('TransactionDuplicateHelper')) {
    if (TransactionDuplicateHelper::isGatewayTransactionExists($mpesa_code)) {
        echo "duplicate transaction detected by helper";
        die;
    }
    if (TransactionDuplicateHelper::isPaymentAlreadyProcessed($checkout_req_id)) {
        echo "payment already processed";
        die;
    }
}




 $plan_type=$plans->type;
              
           $UserId=$userid->id;    
            
              
               
          
                  if (!Package::rechargeUser($UserId, $user['routers'], $user['plan_id'], $user['gateway'], $mpesa_code)){

                    // Package activation failed, but payment was successful - keep status as paid
                    $PaymentGatewayRecord->status = 2; // Keep as paid status since payment succeeded
                    $PaymentGatewayRecord->paid_date = $now;
                    $PaymentGatewayRecord->gateway_trx_id = $mpesa_code;
                    $PaymentGatewayRecord->pg_paid_response = 'Payment successful but package activation failed - please contact support';
                    $PaymentGatewayRecord->save();
                    
                    // Log the failure for admin attention
                    error_log("BankStkPush: Payment successful but package activation failed for user: " . $PaymentGatewayRecord->username . ", Amount: " . $amount_paid . ", Mpesa Code: " . $mpesa_code);

                  } else {
                    // Package activation SUCCESS - this is the normal flow
                    $PaymentGatewayRecord->status = 2; // Set to paid status
                    $PaymentGatewayRecord->paid_date = $now;
                    $PaymentGatewayRecord->gateway_trx_id = $mpesa_code;
                    $PaymentGatewayRecord->pg_paid_response = 'Payment successful and package activated';
                    $PaymentGatewayRecord->save();
                    
                    // Log the success
                    error_log("BankStkPush: Payment successful for user: " . $PaymentGatewayRecord->username . ", Amount: " . $amount_paid . ", Mpesa Code: " . $mpesa_code);
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
