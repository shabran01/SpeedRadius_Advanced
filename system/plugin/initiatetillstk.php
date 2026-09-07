<?php
function initiatetillstk()
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
                     "message" => "Unable to get user information. Please contact support.",
                     "debug" => ["username" => $username, "phone" => $phone]
                 ]);
                 die();
             }

  
         
  
            $phone = (substr($phone, 0,1) == '+') ? str_replace('+', '', $phone) : $phone;
            $phone = (substr($phone, 0,1) == '0') ? preg_replace('/^0/', '254', $phone) : $phone;
            $phone = (substr($phone, 0,1) == '7') ? preg_replace('/^7/', '2547', $phone) : $phone; //cater for phone number prefix 2547XXXX
            $phone = (substr($phone, 0,1) == '1') ? preg_replace('/^1/', '2541', $phone) : $phone; //cater for phone number prefix 2541XXXX
            $phone = (substr($phone, 0,1) == '0') ? preg_replace('/^01/', '2541', $phone) : $phone;
            $phone = (substr($phone, 0,1) == '0') ? preg_replace('/^07/', '2547', $phone) : $phone;
    
            
             $consumer_key = ORM::for_table('tbl_appconfig')
    ->where('setting', 'mpesa_till_consumer_key')
    ->find_one();

     $consumer_secret = ORM::for_table('tbl_appconfig')
    ->where('setting', 'mpesa_till_consumer_secret')
    ->find_one();
    
     $consumer_secret = ORM::for_table('tbl_appconfig')
    ->where('setting', 'mpesa_till_consumer_secret')
    ->find_one();
    
     $BusinessShortCode= ORM::for_table('tbl_appconfig')
    ->where('setting', 'mpesa_till_shortcode_code')
    ->find_one();
    
     $PartyB= ORM::for_table('tbl_appconfig')
    ->where('setting', 'mpesa_till_partyb')
    ->find_one();
    
    
     $LipaNaMpesaPasskey= ORM::for_table('tbl_appconfig')
    ->where('setting', 'mpesa_till_pass_key')
    ->find_one();
    
   
    
      $consumer_key = ($consumer_key) ? $consumer_key->value : null;
      $consumer_secret = ($consumer_secret) ? $consumer_secret->value : null;
      $BusinessShortCode = ($BusinessShortCode) ? $BusinessShortCode->value : null;
      $PartyB = ($PartyB) ? $PartyB->value : null;
      $LipaNaMpesaPasskey = ($LipaNaMpesaPasskey) ? $LipaNaMpesaPasskey->value : null;
    
   
  
  
  
    $cburl = U . 'callback/MpesatillStk' ;
  

    //

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
      



  
            // Update customer phone number if needed
            $ThisUser= ORM::for_table('tbl_customers')
            ->where('username', $username)
            ->order_by_desc('id')
            ->find_one();

            if ($ThisUser && !empty($phone)) {
                $ThisUser->phonenumber=$phone;
                $ThisUser->save();
            }
        
        $amount=$PaymentGatewayRecord->price;


            $TransactionType = 'CustomerBuyGoodsOnline';
            $tokenUrl = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
            $phone= $phone;
            $lipaOnlineUrl = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
         //   $amount= '1';
            $CallBackURL = $cburl;
         date_default_timezone_set('Africa/Nairobi');
            $timestamp = date("YmdHis");
            $password = base64_encode($BusinessShortCode . $LipaNaMpesaPasskey . $timestamp);
         
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $tokenUrl);
            $credentials = base64_encode($consumer_key . ':' . $consumer_secret);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials));
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $curl_response = curl_exec($curl);

            $token = json_decode($curl_response)->access_token;
            $curl2 = curl_init();
            curl_setopt($curl2, CURLOPT_URL, $lipaOnlineUrl);
            curl_setopt($curl2, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $token));



            $curl2_post_data = [
                'BusinessShortCode' => $BusinessShortCode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => $TransactionType,
                'Amount' => $amount,
                'PartyA' => $phone,
                'PartyB' => $PartyB,
                'PhoneNumber' => $phone,
                'CallBackURL' => $CallBackURL,
                'AccountReference' =>  'Payment For Goods',
                'TransactionDesc' => 'Payment for goods',
            ];

            $data2_string = json_encode($curl2_post_data);

            curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl2, CURLOPT_POST, true);
            curl_setopt($curl2, CURLOPT_POSTFIELDS, $data2_string);
            curl_setopt($curl2, CURLOPT_HEADER, false);
            curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl2, CURLOPT_SSL_VERIFYHOST, 0);
            $curl_response = curl_exec($curl2);

$curl_response1 = curl_exec($curl);
//($curl_response);

//echo $curl_response;

  $mpesaResponse = json_decode($curl_response);


//echo $phone;

$responseCode = $mpesaResponse->ResponseCode;
$MerchantRequestID = $mpesaResponse->MerchantRequestID;
$CheckoutRequestID = $mpesaResponse->CheckoutRequestID;
$resultDesc = $mpesaResponse->CustomerMessage;
 // file_put_contents('stk.log',$curl_response,FILE_APPEND);
 
 
 

 
 
 
 
 
 
 
 
// echo $cburl;

 $responseCode  =  $responseCode;
       if($responseCode=="0"){
           date_default_timezone_set('Africa/Nairobi'); 
          $now=date("Y-m-d H:i:s");
          

        //   $username=$phone;


        $PaymentGatewayRecord->pg_paid_response = $resultDesc;
        $PaymentGatewayRecord->checkout = $CheckoutRequestID;
        $PaymentGatewayRecord->username = $username;
       $PaymentGatewayRecord->payment_method = 'Mpesa Stk Push';
       $PaymentGatewayRecord->payment_channel = 'Mpesa Stk Push';
        $PaymentGatewayRecord->save();
        
        if(!empty($_POST['channel'])){
            echo json_encode(["status" => "success", "message" => "Enter Pin to complete","phone"=> $phone]);
        }else{
            // For web interface, show a user-friendly message and redirect
            echo "<script>
                alert('M-Pesa payment initiated successfully! Please check your phone and enter your M-Pesa PIN to complete the payment.');
                setTimeout(function() {
                    window.location.href = '" . U . "order/view/" . $PaymentGatewayRecord->id . "';
                }, 3000);
            </script>";
        }
        
 
  
       }else{
           
           echo "There is an issue with the transaction, please wait for 0 seconds then try again";
       }

  
  
  
  
  
  
  
  
  
  
  
  
  
  
    
    
    }
