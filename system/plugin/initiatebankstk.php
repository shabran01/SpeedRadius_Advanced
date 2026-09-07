<?php

function initiatebankstk()
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
  
  
  
            $phone = (substr($phone, 0,1) == '+') ? str_replace('+', '', $phone) : $phone;
            $phone = (substr($phone, 0,1) == '0') ? preg_replace('/^0/', '254', $phone) : $phone;
            $phone = (substr($phone, 0,1) == '7') ? preg_replace('/^7/', '2547', $phone) : $phone; //cater for phone number prefix 2547XXXX
            $phone = (substr($phone, 0,1) == '1') ? preg_replace('/^1/', '2541', $phone) : $phone; //cater for phone number prefix 2541XXXX
            $phone = (substr($phone, 0,1) == '0') ? preg_replace('/^01/', '2541', $phone) : $phone;
            $phone = (substr($phone, 0,1) == '0') ? preg_replace('/^07/', '2547', $phone) : $phone;
    
  
  

        $bankaccount = ORM::for_table('tbl_appconfig')
    ->where('setting', 'Stkbankacc')
    ->find_one();

     $bankname = ORM::for_table('tbl_appconfig')
    ->where('setting', 'Stkbankname')
    ->find_one();

       $bankaccount = ($bankaccount) ? $bankaccount->value : null;
          $bankname = ($bankname) ? $bankname->value : null;

       // echo $bankname;
          

       $CheckId = ORM::for_table('tbl_customers')
       ->where('username', $username)
       ->order_by_desc('id')
       ->find_one();
   
       $CheckUser = ORM::for_table('tbl_customers')
       ->where('phonenumber', $phone)
       ->find_many();
   
       $UserId=$CheckId->id;
   
         if(!empty($CheckUser)){
   
   
       ORM::for_table('tbl_customers')
       ->where('phonenumber', $phone)
       ->where_not_equal('id', $UserId)
       ->delete_many();
   
   
         }
         


          
          
        if (empty($bankaccount) || empty($bankname)) {
            
            
      echo json_encode(["status" => "error", "message" => "Could not complete the payment req, please contact admin"]);
      
      
    die();
 }

           $getpaybill = ORM::for_table('tbl_banks')
    ->where('name', $bankname)
    ->find_one();
          
      
          $paybill=$getpaybill->paybill;
          
          
          
        // echo $paybill;
          

          
          $cburl = U . 'callback/BankStkPush' ;
          
          
    // Update customer phone number if needed
    $ThisUser= ORM::for_table('tbl_customers')
        ->where('username', $username)
        ->order_by_desc('id')
        ->find_one();

    if ($ThisUser && !empty($phone)) {
        $ThisUser->phonenumber=$phone;
        $ThisUser->save();
    }


    
 
          
          if(!$PaymentGatewayRecord){
              echo json_encode(["status" => "error", "message" => "Could not complete the payment req, please contact administrator"]);
              die();
          }
          
         $amount=$PaymentGatewayRecord->price;
          
    
          
            
  $consumerKey = 'AfFmBfoTA93kjkWeR36Y4VJFtUIMgWU8hPOgmp6TOVYcP7MK'; //Fill with your app Consumer Key
  $consumerSecret = 'bGojphiSAfVAyElAC83WjAGgz1BnXJgGXE0DAtHAanFhGpkvBsVOww4HkA3Jn7T2'; // Fill with your app Secret

  $headers = ['Content-Type:application/json; charset=utf8'];

  $access_token_url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

  $curl = curl_init($access_token_url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($curl, CURLOPT_HEADER, FALSE);('');

  curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
  $result = curl_exec($curl);
  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
  $result = json_decode($result);

  $access_token = $result->access_token;

 // echo  $access_token;
  
  curl_close($curl);


// Initiate Stk push

$stk_url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$PartyA = $phone; // This is your phone number, 
  $AccountReference = $bankaccount; 
  $TransactionDesc = 'TestMapayment';
  $Amount = $amount;
  $BusinessShortCode='4134527';
  $Passkey='d5549a89787be68c65d064b2da7910b9a6580be3a8ae26167ee2c0a8579883e8';
  $Timestamp = date("YmdHis",time());    
  $Password = base64_encode($BusinessShortCode.$Passkey.$Timestamp);
  $CallBackURL = $cburl; 
 

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $stk_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token)); //setting custom header


$curl_post_data = array(
  //Fill in the request parameters with valid values
  'BusinessShortCode' => $BusinessShortCode,
  'Password' => $Password,
  'Timestamp' => $Timestamp,
  'TransactionType' => 'CustomerPayBillOnline',
  'Amount' => $Amount,
  'PartyA' => $PartyA,
  'PartyB' => $paybill,
  'PhoneNumber' => $PartyA,
  'CallBackURL' => $CallBackURL,
  'AccountReference' => $AccountReference,
  'TransactionDesc' => $TransactionDesc
);

$data_string = json_encode($curl_post_data);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

$curl_response = curl_exec($curl);
//print_r($curl_response);

// echo $curl_response;
// die;

$mpesaResponse = json_decode($curl_response);




$responseCode = $mpesaResponse->ResponseCode;
$resultDesc = $mpesaResponse->resultDesc;
$MerchantRequestID = $mpesaResponse->MerchantRequestID;
$CheckoutRequestID = $mpesaResponse->CheckoutRequestID;
              

       if($responseCode=="0"){
           date_default_timezone_set('Africa/Nairobi'); 
          $now=date("Y-m-d H:i:s");

// $username=$phone;
          
        $PaymentGatewayRecord->pg_paid_response = $resultDesc;
        $PaymentGatewayRecord->username = $username;
        $PaymentGatewayRecord->checkout = $CheckoutRequestID;
       $PaymentGatewayRecord->payment_method = 'Mpesa Stk Push';
       $PaymentGatewayRecord->payment_channel = 'Mpesa Stk Push';
        $PaymentGatewayRecord->save();
        
        
        
        if(!empty($_POST['channel'])){
            // API response
            echo json_encode(["status" => "success", "message" => "Enter Pin to complete"]);
        }else{
            // Web interface response
            echo "<script>
                alert('M-Pesa payment initiated successfully! Please check your phone and enter your M-Pesa PIN to complete the payment.');
                setTimeout(function() {
                    window.location.href = '" . U . "order/view/" . $PaymentGatewayRecord->id . "';
                }, 3000);
            </script>";
        }
  
       }else{
           
       echo json_encode(["status" => "error", "message" => "We could not complete the payment for you, please contact administrator"]);
       }

}


?>
