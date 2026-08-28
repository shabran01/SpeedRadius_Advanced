<?php

function initiatempesa()
{
  // Get user data from the most recent active payment gateway record
  $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
     ->where('status', 1)
     ->order_by_desc('id')
     ->find_one();
     
  if(!$PaymentGatewayRecord){
      echo json_encode([
          "status" => "error", 
          "message" => "No active payment found. Please try ordering again."
      ]);
      die();
  }
  
  $username = $PaymentGatewayRecord->username;
  
  // Get phone from customer record or POST data
  $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
  if(empty($phone)) {
      $ThisUser = ORM::for_table('tbl_customers')
         ->where('username', $username)
         ->find_one();
      $phone = $ThisUser ? $ThisUser->phonenumber : '';
  }
  
  // Validate we have the required data
  if(empty($username) || empty($phone)) {
      echo json_encode([
          "status" => "error", 
          "message" => "Unable to get user information. Please contact support."
      ]);
      die();
  }
  $phone = (substr($phone, 0, 1) == '+') ? str_replace('+', '', $phone) : $phone;
  $phone = (substr($phone, 0, 1) == '0') ? preg_replace('/^0/', '254', $phone) : $phone;
  $phone = (substr($phone, 0, 1) == '7') ? preg_replace('/^7/', '2547', $phone) : $phone; //cater for phone number prefix 2547XXXX
  $phone = (substr($phone, 0, 1) == '1') ? preg_replace('/^1/', '2541', $phone) : $phone; //cater for phone number prefix 2541XXXX
  $phone = (substr($phone, 0, 1) == '0') ? preg_replace('/^01/', '2541', $phone) : $phone;
  $phone = (substr($phone, 0, 1) == '0') ? preg_replace('/^07/', '2547', $phone) : $phone;
  $CheckId = ORM::for_table('tbl_customers')
    ->where('username', $username)
    ->order_by_desc('id')
    ->find_one();
  $CheckUser = ORM::for_table('tbl_customers')
    ->where('phonenumber', $phone)
    ->find_many();
  $UserId = $CheckId->id;
  if (!empty($CheckUser)) {
    ORM::for_table('tbl_customers')
      ->where('phonenumber', $phone)
      ->where_not_equal('id', $UserId)
      ->delete_many();
  }
  $CallBackURL = U . 'callback/mpesa';
  
  // Update customer phone number if needed
  $ThisUser = ORM::for_table('tbl_customers')
    ->where('username', $username)
    ->order_by_desc('id')
    ->find_one();
  if ($ThisUser && !empty($phone)) {
    $ThisUser->phonenumber = $phone;
    $ThisUser->save();
  }
  
  $amount = $PaymentGatewayRecord->price;
  // Get the M-Pesa mpesa_env
  $mpesa_env = ORM::for_table('tbl_appconfig')
    ->where('setting', 'mpesa_env')
    ->find_one();
  $mpesa_env = ($mpesa_env) ? $mpesa_env->value : null;
  // Get the M-Pesa consumer key
  $mpesa_consumer_key = ORM::for_table('tbl_appconfig')
    ->where('setting', 'mpesa_consumer_key')
    ->find_one();
  $mpesa_consumer_key = ($mpesa_consumer_key) ? $mpesa_consumer_key->value : null;
  // Get the M-Pesa consumer secret
  $mpesa_consumer_secret = ORM::for_table('tbl_appconfig')
    ->where('setting', 'mpesa_consumer_secret')
    ->find_one();
  $mpesa_consumer_secret = ($mpesa_consumer_secret) ? $mpesa_consumer_secret->value : null;
  $mpesa_business_code = ORM::for_table('tbl_appconfig')
    ->where('setting', 'mpesa_business_code')
    ->find_one();
  $mpesa_business_code = ($mpesa_business_code) ? $mpesa_business_code->value : null;
  $mpesa_shortcode_type = ORM::for_table('tbl_appconfig')
    ->where('setting', 'mpesa_shortcode_type')
    ->find_one();
  if ($mpesa_shortcode_type == 'BuyGoods') {
    $mpesa_buygoods_till_number = ORM::for_table('tbl_appconfig')
      ->where('setting', 'mpesa_buygoods_till_number')
      ->find_one();
    $mpesa_buygoods_till_number = ($mpesa_buygoods_till_number) ? $mpesa_buygoods_till_number->value : null;
    $PartyB = $mpesa_buygoods_till_number;
    $Type_of_Transaction = 'CustomerBuyGoodsOnline';
  } else {
    $PartyB = $mpesa_business_code;
    $Type_of_Transaction = 'CustomerPayBillOnline';
  }
  $Passkey = ORM::for_table('tbl_appconfig')
    ->where('setting', 'mpesa_pass_key')
    ->find_one();
  $Passkey = ($Passkey) ? $Passkey->value : null;
  $Time_Stamp = date("Ymdhis");
  $password = base64_encode($mpesa_business_code . $Passkey . $Time_Stamp);
  if ($mpesa_env == "live") {
    $OnlinePayment = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $Token_URL = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
  } elseif ($mpesa_env == "sandbox") {
    $OnlinePayment = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $Token_URL = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
  } else {
    return json_encode(["Message" => "invalid application status"]);
  };
  $headers = ['Content-Type:application/json; charset=utf8'];
  $curl = curl_init($Token_URL);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($curl, CURLOPT_HEADER, FALSE);
  curl_setopt($curl, CURLOPT_USERPWD, $mpesa_consumer_key . ':' . $mpesa_consumer_secret);
  $result = curl_exec($curl);
  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  $result = json_decode($result);
  $access_token = $result->access_token;
  curl_close($curl);
  $password = base64_encode($mpesa_business_code . $Passkey . $Time_Stamp);
  $stkpushheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];
  //INITIATE CURL
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $OnlinePayment);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $stkpushheader); //setting custom header
  $curl_post_data = array(
    //Fill in the request parameters with valid values
    'BusinessShortCode' => $mpesa_business_code,
    'Password' => $password,
    'Timestamp' => $Time_Stamp,
    'TransactionType' => $Type_of_Transaction,
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $PartyB,
    'PhoneNumber' => $phone,
    'CallBackURL' => $CallBackURL,
    'AccountReference' => $phone,
    'TransactionDesc' => 'Payment for ' . $username
  );
  $data_string = json_encode($curl_post_data);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
  $curl_response = curl_exec($curl);
  $curl_Tranfer2_response = json_decode($curl_response);
  if (isset($curl_Tranfer2_response->ResponseCode) && $curl_Tranfer2_response->ResponseCode == "0") {
    $resultDesc = $curl_Tranfer2_response->resultDesc;
    $CheckoutRequestID = $curl_Tranfer2_response->CheckoutRequestID;
    date_default_timezone_set('Africa/Nairobi');
    $now = date("Y-m-d H:i:s");
    // $username=$phone;
    $PaymentGatewayRecord->pg_paid_response = $resultDesc;
    $PaymentGatewayRecord->username = $username;
    $PaymentGatewayRecord->checkout = $CheckoutRequestID;
    $PaymentGatewayRecord->payment_method = 'Mpesa Stk Push';
    $PaymentGatewayRecord->payment_channel = 'Mpesa Stk Push';
    $saveGateway = $PaymentGatewayRecord->save();
    if ($saveGateway) {
      if (!empty($_POST['channel'])) {
        echo json_encode(["status" => "success", "message" => "Enter Mpesa Pin to complete $mpesa_business_code  $Type_of_Transaction , Party B: $PartyB, Amount: $amount, Phone: $phone, CheckoutRequestID: $CheckoutRequestID"]);
      } else {
        // Web interface: live payment-progress page (auto-detect + auto-redirect)
        if (file_exists(__DIR__ . '/lib_payment_progress.php')) {
            require_once __DIR__ . '/lib_payment_progress.php';
            payment_progress_render($PaymentGatewayRecord->id);
        } else {
            echo "<script>
                alert('M-Pesa payment initiated successfully! Please check your phone and enter your M-Pesa PIN to complete the payment.');
                setTimeout(function() {
                    window.location.href = '" . U . "order/view/" . $PaymentGatewayRecord->id . "';
                }, 3000);
            </script>";
        }
      }
    } else {
      echo json_encode(["status" => "error", "message" => "Failed to save the payment gateway record"]);
    }
  } else {
    $errorMessage = $curl_Tranfer2_response->errorMessage;
    echo json_encode(["status" => "error", "message" => $errorMessage]);
  }
}
