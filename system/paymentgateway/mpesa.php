<?php

/**
 * PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 *
 *
 * Payment Gateway M-Pesa https://developer.safaricom.co.ke/
 * 
 * 
 * created by @alvin-kiveu
 *
 **/


function mpesa_validate_config()
{
  global $config;
  if (empty($config['mpesa_consumer_key']) || empty($config['mpesa_consumer_secret'])) {
    sendTelegram("M-Pesa payment gateway not configured");
    r2(U . 'order/package', 'w', Lang::T("Admin has not yet setup M-Pesa payment gateway, please tell admin"));
  }
}

function mpesa_show_config()
{
  global $ui, $config;
  $ui->assign('env', json_decode(file_get_contents('system/paymentgateway/mpesa_env.json'), true));
  $ui->assign('_title', 'M-Pesa - Payment Gateway - ' . $config['CompanyName']);
  $ui->display('mpesa.tpl');
}

function mpesa_save_config()
{
  global $admin, $_L;
  $mpesa_consumer_key = _post('mpesa_consumer_key');
  $mpesa_consumer_secret = _post('mpesa_consumer_secret');
  $mpesa_shortcode_type = _post('mpesa_shortcode_type');
  $mpesa_business_code = _post('mpesa_business_code');
  $mpesa_pass_key = _post('mpesa_pass_key');
  $mpesa_env = _post('mpesa_env');
  $mpesa_channel_ofline_online = _post('mpesa_channel_ofline_online');
  $mpesa_api_version = _post('mpesa_api_version');


  $d = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_shortcode_type')->find_one();
  if ($d) {
    $d->value = $mpesa_shortcode_type;
    $d->save();
  } else {
    $d = ORM::for_table('tbl_appconfig')->create();
    $d->setting = 'mpesa_shortcode_type';
    $d->value = $mpesa_shortcode_type;
    $d->save();
  }

  if ($mpesa_shortcode_type == 'BuyGoods') {
    $mpesa_buygoods_till_number = _post('mpesa_buygoods_till_number');
    $d = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_buygoods_till_number')->find_one();
    if ($d) {
      $d->value = $mpesa_buygoods_till_number;
      $d->save();
    } else {
      $d = ORM::for_table('tbl_appconfig')->create();
      $d->setting = 'mpesa_buygoods_till_number';
      $d->value = $mpesa_buygoods_till_number;
      $d->save();
    }
  }

  $d = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_api_version')->find_one();
  if ($d) {
    $d->value = $mpesa_api_version;
    $d->save();
  } else {
    $d = ORM::for_table('tbl_appconfig')->create();
    $d->setting = 'mpesa_api_version';
    $d->value = $mpesa_api_version;
    $d->save();
  }

  $d = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_channel_ofline_online')->find_one();
  if ($d) {
    $d->value = $mpesa_channel_ofline_online;
    $d->save();
  } else {
    $d = ORM::for_table('tbl_appconfig')->create();
    $d->setting = 'mpesa_channel_ofline_online';
    $d->value = $mpesa_channel_ofline_online;
    $d->save();
  }



  $d = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_consumer_key')->find_one();
  if ($d) {
    $d->value = $mpesa_consumer_key;
    $d->save();
  } else {
    $d = ORM::for_table('tbl_appconfig')->create();
    $d->setting = 'mpesa_consumer_key';
    $d->value = $mpesa_consumer_key;
    $d->save();
  }


  $d = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_consumer_secret')->find_one();
  if ($d) {
    $d->value = $mpesa_consumer_secret;
    $d->save();
  } else {
    $d = ORM::for_table('tbl_appconfig')->create();
    $d->setting = 'mpesa_consumer_secret';
    $d->value = $mpesa_consumer_secret;
    $d->save();
  }

  $d = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_business_code')->find_one();
  if ($d) {
    $d->value = $mpesa_business_code;
    $d->save();
  } else {
    $d = ORM::for_table('tbl_appconfig')->create();
    $d->setting = 'mpesa_business_code';
    $d->value = $mpesa_business_code;
    $d->save();
  }


  $d = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_pass_key')->find_one();
  if ($d) {
    $d->value = $mpesa_pass_key;
    $d->save();
  } else {
    $d = ORM::for_table('tbl_appconfig')->create();
    $d->setting = 'mpesa_pass_key';
    $d->value = $mpesa_pass_key;
    $d->save();
  }


  $d = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_env')->find_one();
  if ($d) {
    $d->value = $mpesa_env;
    $d->save();
  } else {
    $d = ORM::for_table('tbl_appconfig')->create();
    $d->setting = 'mpesa_env';
    $d->value = $mpesa_env;
    $d->save();
  }


  _log('[' . $admin['username'] . ']: M-Pesa ' . $_L['Settings_Saved_Successfully'] . json_encode($_POST['mpesa_channel']), 'Admin', $admin['id']);

  r2(U . 'paymentgateway/mpesa', 's', $_L['Settings_Saved_Successfully']);
}


function mpesa_create_transaction($trx, $user)
{
  global $config, $routes;
  $environment = $config['mpesa_env'];
  $consumer_key = $config['mpesa_consumer_key'];
  $consumer_secret = $config['mpesa_consumer_secret'];
  $Business_Code = $config['mpesa_business_code'];
  $Passkey = $config['mpesa_pass_key'];
  $phone_number = $user['phonenumber'];
  $total_amount = $trx['price'];
  $CallBackURL = U . 'callback/mpesa';
  $Time_Stamp = date("Ymdhis");
  $mpesa_shortcode_type = $config['mpesa_shortcode_type'];
  if ($mpesa_shortcode_type == 'BuyGoods') {
    $PartyB = $config['mpesa_buygoods_till_number'];
    $Type_of_Transaction = 'CustomerBuyGoodsOnline';
  } else {
    $PartyB = $phone_number;
    $Type_of_Transaction = 'CustomerPayBillOnline';
  }
  $password = base64_encode($Business_Code . $Passkey . $Time_Stamp);
  if ($environment == "live") {
    $OnlinePayment = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $Token_URL = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
  } elseif ($environment == "sandbox") {
    $OnlinePayment = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $Token_URL = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
  } else {
    return json_encode(["Message" => "invalid application status"]);
  };
  $curl_Tranfer = curl_init();
  curl_setopt($curl_Tranfer, CURLOPT_URL, $Token_URL);
  $credentials = base64_encode($consumer_key . ':' . $consumer_secret);
  curl_setopt($curl_Tranfer, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
  curl_setopt($curl_Tranfer, CURLOPT_HEADER, false);
  curl_setopt($curl_Tranfer, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl_Tranfer, CURLOPT_SSL_VERIFYPEER, false);
  $curl_Tranfer_response = curl_exec($curl_Tranfer);
  $token = json_decode($curl_Tranfer_response)->access_token;
  $curl_Tranfer2 = curl_init();
  curl_setopt($curl_Tranfer2, CURLOPT_URL, $OnlinePayment);
  curl_setopt($curl_Tranfer2, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $token));
  $curl_Tranfer2_post_data = [
    'BusinessShortCode' => $Business_Code,
    'Password' => $password,
    'Timestamp' => $Time_Stamp,
    'TransactionType' => $Type_of_Transaction,
    'Amount' => $trx['price'],
    'PartyA' => $phone_number,
    'PartyB' => $PartyB,
    'PhoneNumber' => $phone_number,
    'CallBackURL' => $CallBackURL,
    'AccountReference' => $phone_number,
    'TransactionDesc' => $trx['plan_name']
  ];
  $data2_string = json_encode($curl_Tranfer2_post_data);
  curl_setopt($curl_Tranfer2, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl_Tranfer2, CURLOPT_POST, true);
  curl_setopt($curl_Tranfer2, CURLOPT_POSTFIELDS, $data2_string);
  curl_setopt($curl_Tranfer2, CURLOPT_HEADER, false);
  curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYHOST, 0);
  $curl_Tranfer2_response = json_decode(curl_exec($curl_Tranfer2));
  if (isset($curl_Tranfer2_response->ResponseCode) && $curl_Tranfer2_response->ResponseCode == "0") {
    $d = ORM::for_table('tbl_payment_gateway')
      ->where('username', $user['username'])
      ->where('status', 1)
      ->find_one();
    $d->gateway_trx_id = $curl_Tranfer2_response->CheckoutRequestID;
    $d->pg_url_payment = $Time_Stamp;
    $d->pg_request = $user['id'];
    $d->expired_date = date('Y-m-d H:i:s', strtotime("+5 minutes"));
    $d->save();
    r2(U . "order/view/" . $d['id'], 's', Lang::T("Create Transaction Success, Please check your phone to process payment"));
  } else {
    sendTelegram("M-Pesa payment failed\n\n" . json_encode($curl_Tranfer2_response, JSON_PRETTY_PRINT));
    r2(U . 'order/package', 'e', Lang::T("Failed to create transaction."));
  }
}


function mpesa_payment_notification()
{
  echo "THIS IS MPESA STK CALLBACK URL";
  global $config;
  $userType = 'OneTimeUser';
  if ($userType == 'OneTimeUser') {
    $captureLogs = file_get_contents("php://input");
    $analizzare = json_decode($captureLogs);
    file_put_contents('MpesaStk.log', $captureLogs, FILE_APPEND);
    $response_code   = $analizzare->Body->stkCallback->ResultCode;
    $resultDesc      = ($analizzare->Body->stkCallback->ResultDesc);
    $merchant_req_id = ($analizzare->Body->stkCallback->MerchantRequestID);
    $checkout_req_id = ($analizzare->Body->stkCallback->CheckoutRequestID);
    $amount_paid     = ($analizzare->Body->stkCallback->CallbackMetadata->Item['0']->Value); //get the amount value
    $mpesa_code      = ($analizzare->Body->stkCallback->CallbackMetadata->Item['1']->Value); //mpesa transaction code..
    $sender_phone    = ($analizzare->Body->stkCallback->CallbackMetadata->Item['4']->Value); //Telephone Number
    $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
      ->where('checkout', $checkout_req_id)
      ->where('status', 1) // Add this line to filter by status
      ->order_by_desc('id')
      ->find_one();
    $uname = $PaymentGatewayRecord->username;
    $plan_id = $PaymentGatewayRecord->plan_id;
    $mac_address = $PaymentGatewayRecord->mac_address;
    $user = $PaymentGatewayRecord;
    $userid = ORM::for_table('tbl_customers')
      ->where('username', $uname)
      ->order_by_desc('id')
      ->find_one();
    $userid->username = $uname;
    $userid->save();
    $plans = ORM::for_table('tbl_plans')
      ->where('id', $plan_id)
      ->order_by_desc('id')
      ->find_one();
    if ($response_code == "1032") {
      $now = date('Y-m-d H:i:s');
      $PaymentGatewayRecord->paid_date = $now;
      $PaymentGatewayRecord->status = 4;
      $PaymentGatewayRecord->save();
      exit();
    }
    if ($response_code == "1037") {
      $PaymentGatewayRecord->status = 1;
      $PaymentGatewayRecord->pg_paid_response = 'User failed to enter pin';
      $PaymentGatewayRecord->save();
      exit();
    }
    if ($response_code == "1") {
      $PaymentGatewayRecord->status = 1;
      $PaymentGatewayRecord->pg_paid_response = 'Not enough balance';
      $PaymentGatewayRecord->save();
      exit();
    }
    if ($response_code == "2001") {
      $PaymentGatewayRecord->status = 1;
      $PaymentGatewayRecord->pg_paid_response = 'Wrong Mpesa pin';
      $PaymentGatewayRecord->save();
      exit();
    }
    if ($response_code == "0") {
      $now = date('Y-m-d H:i:s');
      $date = date('Y-m-d');
      $time = date('H:i:s');
      $check_mpesa = ORM::for_table('tbl_payment_gateway')
        ->where('gateway_trx_id', $mpesa_code)
        ->find_one();
      if ($check_mpesa) {
        echo "double callback, ignore one";
        die;
      }
      $plan_type = $plans->type;
      $UserId = $userid->id;
      if (!Package::rechargeUser($UserId, $user['routers'], $user['plan_id'], $user['gateway'], $mpesa_code)) {
        $PaymentGatewayRecord->status = 2;
        $PaymentGatewayRecord->paid_date = $now;
        $PaymentGatewayRecord->gateway_trx_id = $mpesa_code;
        $PaymentGatewayRecord->save();
        $username = $PaymentGatewayRecord->username;
        // Save transaction data to tbl_transactions
        $transaction = ORM::for_table('tbl_transactions')->create();
        $transaction->invoice = $mpesa_code;
        $transaction->username = $PaymentGatewayRecord->username;
        $transaction->plan_name = $PaymentGatewayRecord->plan_name;
        $transaction->price = $amount_paid;
        $transaction->recharged_on = $date;
        $transaction->recharged_time = $time;
        $transaction->expiration = $now;
        $transaction->time = $now;
        $transaction->method = $PaymentGatewayRecord->payment_method;
        $transaction->routers = 0;
        $transaction->Type = 'Balance';
        $transaction->save();
      } else {
        $PaymentGatewayRecord->status = 2;
        $PaymentGatewayRecord->paid_date = $now;
        $PaymentGatewayRecord->gateway_trx_id = $mpesa_code;
        $PaymentGatewayRecord->save();
      }
    }
  } else {
    $userType = 'RegisteredUser';
    //CHECK IF OFFLINE IS ENABLED
    $OFFLineMode = ORM::for_table('tbl_appconfig')->where('setting', 'mpesa_channel_ofline_online')->find_one();
    if ($OFFLineMode['value'] == 1) {
    } else {
      //responce from mpesa server after payment is initiated and processed
      $callbackJSONData = file_get_contents('php://input');
      $callbackData = json_decode($callbackJSONData);
      $resultCode = $callbackData->Body->stkCallback->ResultCode;
      $resultDesc = $callbackData->Body->stkCallback->ResultDesc;
      $checkoutRequestID = $callbackData->Body->stkCallback->CheckoutRequestID;
      $trx = ORM::for_table('tbl_payment_gateway')
        ->where('gateway_trx_id', $checkoutRequestID)
        ->find_one();
      if (!$trx) {
        return;
      }
      $user = ORM::for_table('tbl_customers')
        ->where('username', $trx['username'])
        ->find_one();
      if (!$trx) {
        return;
      }
      if ($resultDesc == "Confirmation Service not accepted" || $resultCode == 1) {
        $trx->status = 3;
        $trx->save();
        exit();
      } elseif ($resultDesc == "The service request is processed successfully." && $resultCode == 0 && $trx['status'] != 2) {
        if (!Package::rechargeUser($user['id'], $trx['routers'], $trx['plan_id'], $trx['gateway'], 'mpesa')) {
          _log('[' . $resultDesc . ']: M-Pesa ' . "Payment Successfull,But Failed to activate your Package" . json_encode($callbackData));
        }
        _log('[' . $resultDesc . ']: M-Pesa ' . "Payment Successfull" . json_encode($callbackData));

        $trx->pg_paid_response = json_encode($callbackData);
        $trx->payment_method = 'M-Pesa';
        $trx->payment_channel = 'M-Pesa StkPush';
        $trx->paid_date = date('Y-m-d H:i:s');
        $trx->status = 2;
        $trx->save();
      } else {
        $trx->status = 1;
        $trx->save();
        exit();
      }
    }
  }
}


function mpesa_get_status($trx, $user)
{
  global $config, $routes;

  if ($trx->status == 2) {
    r2(U . "order/view/" . $trx['id'], 's', Lang::T("Transaction has been completed."));
    die();
  } elseif ($trx->status == 1) {
    $environment = $config['mpesa_env'];
    $consumer_key = $config['mpesa_consumer_key'];
    $consumer_secret = $config['mpesa_consumer_secret'];
    $Business_Code = $config['mpesa_business_code'];
    $Passkey = $config['mpesa_pass_key'];
    //Timestamp that we save earlier in pg_url_payment database
    $Time_Stamp = $trx['pg_url_payment'];
    $password = base64_encode($Business_Code . $Passkey . $Time_Stamp);
    if ($environment == "live") {
      $OnlinePayment = 'https://api.safaricom.co.ke/mpesa/stkpushquery/v1/query';
      $Token_URL = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    } elseif ($environment == "sandbox") {
      $OnlinePayment = 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query';
      $Token_URL = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    } else {
      return json_encode(["Message" => "invalid application status"]);
    };
    $curl_Tranfer = curl_init();
    curl_setopt($curl_Tranfer, CURLOPT_URL, $Token_URL);
    $credentials = base64_encode($consumer_key . ':' . $consumer_secret);
    curl_setopt($curl_Tranfer, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
    curl_setopt($curl_Tranfer, CURLOPT_HEADER, false);
    curl_setopt($curl_Tranfer, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_Tranfer, CURLOPT_SSL_VERIFYPEER, false);
    $curl_Tranfer_response = curl_exec($curl_Tranfer);
    $token = json_decode($curl_Tranfer_response)->access_token;
    // die(json_encode($curl_Tranfer2_post_data,JSON_PRETTY_PRINT));
    $curl_Tranfer2 = curl_init();
    curl_setopt($curl_Tranfer2, CURLOPT_URL, $OnlinePayment);
    curl_setopt($curl_Tranfer2, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $token));
    //lest verify the transaction by sending data to mpesa transaction query portal from nuxbil
    $curl_Tranfer2_post_data = [
      'BusinessShortCode' => $Business_Code,
      'Password' => $password,
      'Timestamp' => $Time_Stamp,
      'CheckoutRequestID' => $trx['gateway_trx_id']
    ];
    //die(json_encode($curl_Tranfer2_post_data,JSON_PRETTY_PRINT));
    $data2_string = json_encode($curl_Tranfer2_post_data);
    curl_setopt($curl_Tranfer2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_Tranfer2, CURLOPT_POST, true);
    curl_setopt($curl_Tranfer2, CURLOPT_POSTFIELDS, $data2_string);
    curl_setopt($curl_Tranfer2, CURLOPT_HEADER, false);
    curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl_Tranfer2, CURLOPT_SSL_VERIFYHOST, 0);
    $curl_Tranfer2_response = json_decode(curl_exec($curl_Tranfer2));
    $callbackJSONData = file_get_contents('php://input');
    $callbackData = json_decode($callbackJSONData);
    $responseCode = $callbackData->ResponseCode;
    $responseDescription = $callbackData->ResponseDescription;
    $merchantRequestID = $callbackData->MerchantRequestID;
    $checkoutRequestID = $callbackData->CheckoutRequestID;
    $resultCode = $callbackData->ResultCode;
    $resultDesc = $callbackData->ResultDesc;
    //if responce is Failed
    if ($responseDescription === "The service request has failed" || $resultDesc === "Request canceled by the user" ||  $responseCode === 1) {
      r2(U . "order/view/" . $trx['id'], 'w', Lang::T("Transaction still unpaid."));
      //if responce is Successfull, activate the plan or balance
    } elseif (($responseDescription === "The service request has been accepted successfully." || $resultDesc == "The service request is processed successfully"  || $responseCode === 0) && $trx['status'] != 2) {
      if (!Package::rechargeUser($user['id'], $trx['routers'], $trx['plan_id'], $trx['gateway'],  'M-Pesa')) {
        r2(U . "order/view/" . $trx['id'], 'd', Lang::T("Failed to activate your Package, try again later."));
      }
      _log('[' . $checkoutRequestID . ']: M-Pesa ' . "Payment Successfull" . json_encode($callbackData));
      $trx->pg_paid_response = json_encode($callbackData);
      $trx->payment_method = 'M-Pesa';
      $trx->payment_channel = 'M-Pesa StkPush';
      $trx->paid_date = date('Y-m-d H:i:s');
      $trx->status = 2;
      $trx->save();
      r2(U . "order/view/" . $trx['id'], 's', Lang::T("Transaction has been paid."));
    } else if ($trx['status'] == 2) {
      r2(U . "order/view/" . $trx['id'], 'd', Lang::T("Transaction has been paid.."));
    }
  }
}
