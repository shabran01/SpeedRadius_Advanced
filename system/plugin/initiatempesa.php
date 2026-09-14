<?php

function initiatempesa()
{
  // Identify the transaction this request belongs to (account id from POST or
  // the account/trx query params) so a concurrent purchase is never billed.
  $accountHint = '';
  if (!empty($_POST['username'])) {
      $accountHint = trim($_POST['username']);
  } elseif (!empty($_GET['account'])) {
      $accountHint = trim($_GET['account']);
  }
  $trxHint = isset($_GET['trx']) ? intval($_GET['trx']) : 0;

  // Only fall back to the newest unpaid row when no identity was given.
  $pgQuery = ORM::for_table('tbl_payment_gateway')->where('status', 1);
  if ($trxHint > 0) {
      $pgQuery->where('id', $trxHint);
  } elseif ($accountHint !== '') {
      $pgQuery->where('username', $accountHint);
  }
  $PaymentGatewayRecord = $pgQuery->order_by_desc('id')->find_one();
     
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
  // One shared normaliser: 0712..., 712..., +254712..., 254712... -> 254712...
  $phone = Text::normalizePhone($phone);
  // NOTE: this flow used to DELETE every other customer row sharing this phone
  // number, destroying live accounts (they showed as "Not in DB" on Online
  // Users). Now removed - duplicates are left untouched.
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
        // For web interface, show a user-friendly message and redirect
        echo "<script>
            alert('M-Pesa payment initiated successfully! Please check your phone and enter your M-Pesa PIN to complete the payment.');
            setTimeout(function() {
                window.location.href = '" . U . "order/view/" . $PaymentGatewayRecord->id . "';
            }, 3000);
        </script>";
      }
    } else {
      echo json_encode(["status" => "error", "message" => "Failed to save the payment gateway record"]);
    }
  } else {
    $errorMessage = $curl_Tranfer2_response->errorMessage;
    echo json_encode(["status" => "error", "message" => $errorMessage]);
  }
}
