<?php
require_once __DIR__ . '/../autoload/Package.php';
require_once __DIR__ . '/../autoload/Message.php';


$requestUri = $_SERVER['REQUEST_URI'];
$queryString = parse_url($requestUri, PHP_URL_QUERY);
$kind = null;
if ($queryString) {
  parse_str($queryString, $queryParameters);
  if (isset($queryParameters['kind'])) {
    $kind = $queryParameters['kind'];
    if ($kind === "register") {
      RegisterUrl();
      exit;
    } elseif ($kind === "confirmation") {
      ConfirmationURL();
      exit;
    } elseif ($kind === "validation") {
      ValidationURL();
      exit;
    } else {
      echo "This is unknown URL";
      exit;
    }
    exit;
  }
}

function generateAccessToken()
{
  // Get the M-Pesa environment
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
  if ($mpesa_env == "live") {
    $access_token_url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
  } elseif ($mpesa_env == "sandbox") {
    $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
  }
  $headers = ['Content-Type:application/json; charset=utf8'];
  $curl = curl_init($access_token_url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($curl, CURLOPT_HEADER, FALSE);
  curl_setopt($curl, CURLOPT_USERPWD, $mpesa_consumer_key . ':' . $mpesa_consumer_secret);
  $result = curl_exec($curl);
  $result = json_decode($result);
  if (isset($result->access_token)) {
    return $result->access_token;
  } else {
    return null;
  }
}

function sendTelegramNotification($message)
{
  $botToken = "7462277734:AAFat4GJmM82q2GMGWlvrHPFlzqoqgLWoE0";
  $method = "sendMessage";
  $adminChatId = 7511532493;
  $parameters = array(
    "chat_id" => $adminChatId,
    "text" => $message,
    "parseMode" => "html"
  );
  $url = "https://api.telegram.org/bot$botToken/$method";
  if (!$curld = curl_init()) {
    exit;
  }
  curl_setopt($curld, CURLOPT_POST, true);
  curl_setopt($curld, CURLOPT_POSTFIELDS, $parameters);
  curl_setopt($curld, CURLOPT_URL, $url);
  curl_setopt($curld, CURLOPT_RETURNTRANSFER, true);
  $output = curl_exec($curld);
  curl_close($curld);
  return $output;
}




function RegisterUrl()
{
  $access_token = generateAccessToken();
  if ($access_token == null) {
    echo "Failed to generate access token";
    exit;
  } else {
    $mpesa_business_code = ORM::for_table('tbl_appconfig')
      ->where('setting', 'mpesa_business_code')
      ->find_one();
    $mpesa_business_code = ($mpesa_business_code) ? $mpesa_business_code->value : null;
    $mpesa_env = ORM::for_table('tbl_appconfig')
      ->where('setting', 'mpesa_env')
      ->find_one();
    $confirmationUrl = U . 'plugin/c2b&kind=confirmation';
    $validationUrl = U . 'plugin/c2b&kind=validation';
    $BusinessShortCode = $mpesa_business_code;
    $mpesa_env = ($mpesa_env) ? $mpesa_env->value : null;
    // Get the M-Pesa API version
    $mpesa_api_version = ORM::for_table('tbl_appconfig')
      ->where('setting', 'mpesa_api_version')
      ->find_one();
    $mpesa_api_version = ($mpesa_api_version) ? $mpesa_api_version->value : null;
    if ($mpesa_env == "live") {
      if ($mpesa_api_version == "v1") {
        $registerurl = 'https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl';
      } elseif ($mpesa_api_version == "v2") {
        $registerurl = 'https://api.safaricom.co.ke/mpesa/c2b/v2/registerurl';
      }
    } elseif ($mpesa_env == "sandbox") {
      $registerurl = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';
    }
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $registerurl);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Content-Type:application/json',
      'Authorization:Bearer ' . $access_token
    ));
    $data = array(
      'ShortCode' => $BusinessShortCode,
      'ResponseType' => 'Completed',
      'ConfirmationURL' => $confirmationUrl,
      'ValidationURL' => $validationUrl
    );
    $data_string = json_encode($data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    $curl_response = curl_exec($curl);
    $data = json_decode($curl_response);
    if (isset($data->ResponseCode) && $data->ResponseCode == 0) {
      r2(U . 'paymentgateway/mpesa', 's', "M-Pesa C2B URL registered successfully");
    } else {
      $errorMessage = $data->errorMessage;
      sendTelegram("Resister M-Pesa C2B URL Failed\n\n" . json_encode($curl_response, JSON_PRETTY_PRINT));
      sendTelegramNotification("Resister M-Pesa C2B URL Failed\n\n" . json_encode($curl_response, JSON_PRETTY_PRINT));
      r2(U . 'paymentgateway/mpesa', 'e', "Failed to register  M-Pesa C2B URL Error $errorMessage");
    }
  }
}


function sendUMSSMS($phone, $message)
{
  $apiUrl = "https://comms.umeskiasoftwares.com/api/v1/sms/send";
  $apiKey = "1c6a1be80408681b45ce68dbc1955068";  // Replace with your actual API key
  $appId = "UMSC409883";    // Replace with your actual App ID
  $senderId = "UMS_TX";      // Use UMS_TX for transactional or UMS_SMS for promotional SMS
  // Prepare the data payload
  $postData = [
    "api_key"   => $apiKey,
    "app_id"    => $appId,
    "sender_id" => $senderId,
    "message"   => $message,
    "phone"     => $phone
  ];
  // Initialize cURL
  $ch = curl_init($apiUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
  ]);
  // Execute cURL request
  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  // Handle response
  if (curl_errno($ch)) {
    // cURL error
    $error = curl_error($ch);
    curl_close($ch);
    return ["status" => "error", "message" => "cURL Error: $error"];
  }
  curl_close($ch);
  // Decode and return the response
  $result = json_decode($response, true);
  if ($httpCode == 200 && isset($result['status']) && $result['status'] == 'complete') {
    return ["status" => "success", "message" => "SMS sent successfully", "response" => $result];
  } else {
    return ["status" => "error", "message" => $result['message'] ?? 'Failed to send SMS', "response" => $result];
  }
}



function ConfirmationURL()
{
  header("Content-Type: application/json");
  $mpesaResponse = file_get_contents('php://input');
  $logFile = "C2bConfirmationResponse.json";
  $log = fopen($logFile, "a");
  fwrite($log, $mpesaResponse);
  fclose($log);
  $content = json_decode($mpesaResponse);
  // Ensure $content is decoded properly
  if (json_last_error() !== JSON_ERROR_NONE) {
    sendTelegramNotification("Failed to decode JSON response.");
    return;
  }
  // GET ALL MPESA USER INFORMATION
  $TransactionType = $content->TransactionType;
  $TransID = $content->TransID;
  $TransTime = $content->TransTime;
  $TransAmount = $content->TransAmount;
  $BusinessShortCode = $content->BusinessShortCode;
  $BillRefNumber = $content->BillRefNumber;
  $OrgAccountBalance = $content->OrgAccountBalance;
  $MSISDN = $content->MSISDN;
  $FirstName = $content->FirstName;

  //sendTelegramNotification("DEV ALVO IS HERE");
  storeTransaction($TransactionType, $TransID, $TransTime, $TransAmount, $BusinessShortCode, $BillRefNumber, $OrgAccountBalance, $MSISDN, $FirstName);
  $mpesa_channel_ofline_online = ORM::for_table('tbl_appconfig')
    ->where('setting', 'mpesa_channel_ofline_online')
    ->find_one();
  $mpesa_channel_ofline_online = ($mpesa_channel_ofline_online) ? $mpesa_channel_ofline_online->value : null;
  if ($mpesa_channel_ofline_online == 1) {
    $user = ORM::for_table('tbl_user_recharges')
      ->where('username', $BillRefNumber)
      ->where('type', 'PPPOE')
      ->find_one();
    if ($user) {
      $plan_id =  $user['plan_id'];
      $user = ORM::for_table('tbl_user_recharges')
        ->where('username', $BillRefNumber)
        ->where('type', 'PPPOE')
        ->find_one();

      if ($user) {
        $plan_id = $user['plan_id'];

        // Fetch the plan details
        $plan = ORM::for_table('tbl_plans')
          ->where('id', $plan_id)
          ->find_one();

        if ($plan) {
          $package_price = $plan['price'];  // Assuming 'price' column stores the package price
          if ($TransAmount >= $package_price) {
            //id the cutomer has paid more than the package price
            // Update the user balance in tbl_customers
            if ($TransAmount > $package_price) {
              updateCustomerMpesaBalance($BillRefNumber, $TransAmount - $package_price);
            }
            // Update the user balance in tbl_user_recharges
            if (class_exists('Package')) {
              try {
                if (!class_exists('Package')) {
                  throw new Exception("Error: Package class does not exist.");
                }



                $channel_mode = "Mpesa C2B - $TransID";
                if (!Package::rechargeUser($user['customer_id'], $user['routers'], $user['plan_id'], 'mpesa', $channel_mode)) {
                  _log("Mpesa Payment Successful, but failed to activate your package.");
                  sendTelegramNotification("Mpesa Payment Successful, but failed to activate your package.");
                }

                //GET CUSTOMER PHONE NUMBER FROM  tbl_customers
                $customer = ORM::for_table('tbl_customers')
                  ->where('id', $user['customer_id'])
                  ->find_one();
                $phone = $customer['phonenumber'];
                $plan_name = $plan['name_plan'];

                $in = ORM::for_table('tbl_transactions')
                  ->where('username', $BillRefNumber)
                  ->order_by_desc('id')
                  ->find_one();

                $expration_date = $in['expiration'];

                // Send recharge SMS using built-in messaging system
                $message = "Dear $BillRefNumber, your $plan_name subscription is successful. Expiry: $expration_date. Thank you!";

                // Use the built-in Message class to send SMS
                $response = Message::sendSMS($phone, $message);

                //SEND TELEGRAM NOTIFICATION
                // SEND TELEGRAM NOTIFICATION
                sendTelegramNotification("
INVOICE CREATED SUCCESSFULLY
---------------------------
Customer Username: {$in['username']}
Account: $BillRefNumber
Plan Name: {$in['plan_name']}
Amount: {$in['price']}
Receipt Number: {$in['invoice']}
Payment Method: {$in['method']}
Recharge on: {$in['recharged_on']}
Expiration: {$in['expiration']}
Router: {$in['routers']}
Type: {$in['type']}
");
              } catch (Exception $e) {
                _log("Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
                sendTelegramNotification("Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
              }
            } else {
              _log("Error: Package class does not exist.");
              sendTelegramNotification("Error: Package class does not exist.");
            }
          } else {
            sendTelegramNotification("The deposited amount is less than the package price.");
            updateCustomerMpesaBalance($BillRefNumber, $TransAmount);
          }
        } else {
          sendTelegramNotification("Plan not found.");
        }
      } else {
        sendTelegramNotification("User not found.");
      }
    } else {
      // Handle case where no matching user recharges record is found
      sendTelegramNotification("No matching user recharges record found for BillRefNumber: " . $BillRefNumber);
    }
  } else {
    sendTelegramNotification("You have received an offline payment of Amount: $TransAmount BillRefNumber: $BillRefNumber");
  }
}


function ValidationURL()
{
  echo "THIS IS Mpesa ValidationURL";
  sendTelegramNotification("THIS IS Mpesa ValidationURL");
  echo "THIS IS Mpesa ValidationURL 101 test";
  header("Content-Type: application/json");
  $mpesaResponse = file_get_contents('php://input');
  $logFile = "C2bValidationResponse.txt";
  $log = fopen($logFile, "a");
  fwrite($log, $mpesaResponse);
  fclose($log);
}


function updateCustomerMpesaBalance($username, $amountDeposited)
{
  $customer = ORM::for_table('tbl_customers')
    ->where('username', $username)  // Assuming username is used to link customers
    ->find_one();
  if ($customer) {
    // Update the customer's balance
    $customer->balance += $amountDeposited;
    $customer->save();
    sendTelegramNotification("Balance updated successfully. New balance: {$customer->balance}");
    return [
      'status' => 'success',
      'message' => "Balance updated successfully. New balance: {$customer->balance}"
    ];
  } else {
    sendTelegramNotification("Customer not found in tbl_customers for username: {$username}");
    return [
      'status' => 'error',
      'message' => "Customer not found in tbl_customers for username: {$username}"
    ];
  }
}


function storeTransaction($TransactionType, $TransID, $TransTime, $TransAmount, $BusinessShortCode, $BillRefNumber, $OrgAccountBalance, $MSISDN, $FirstName)
{
  // Ensure the tbl_mpesa_transactions table exists
  createTableIfNotExists();

  // Create the receipt
  $receipt = "Payment Receipt\n" .
    "-------------------------------\n" .
    "Transaction Type: $TransactionType\n" .
    "Transaction ID: $TransID\n" .
    "Transaction Time: $TransTime\n" .
    "Transaction Amount: KES $TransAmount\n" .
    "-------------------------------\n" .
    "Business Short Code: $BusinessShortCode\n" .
    "Bill Reference Number: $BillRefNumber\n" .
    "-------------------------------\n" .
    "Account Balance: KES $OrgAccountBalance\n" .
    "Customer MSISDN: $MSISDN\n" .
    "Customer Name: $FirstName\n" .
    "-------------------------------\n";

  // Send the notification
  sendTelegramNotification($receipt);

  // Check if the transaction already exists
  $transaction = ORM::for_table('tbl_mpesa_transactions')->where('TransID', $TransID)->find_one();

  // Update existing transaction or create a new one
  if ($transaction) {
    $transaction->TransactionType = $TransactionType;
    $transaction->TransTime = $TransTime;
    $transaction->TransAmount = $TransAmount;
    $transaction->BusinessShortCode = $BusinessShortCode;
    $transaction->BillRefNumber = $BillRefNumber;
    $transaction->OrgAccountBalance = $OrgAccountBalance;
    $transaction->MSISDN = $MSISDN;
    $transaction->FirstName = $FirstName;
    $transaction->save();
  } else {
    $transaction = ORM::for_table('tbl_mpesa_transactions')->create();
    $transaction->TransID = $TransID;
    $transaction->TransactionType = $TransactionType;
    $transaction->TransTime = $TransTime;
    $transaction->TransAmount = $TransAmount;
    $transaction->BusinessShortCode = $BusinessShortCode;
    $transaction->BillRefNumber = $BillRefNumber;
    $transaction->OrgAccountBalance = $OrgAccountBalance;
    $transaction->MSISDN = $MSISDN;
    $transaction->FirstName = $FirstName;
    $transaction->save();
  }
}


function createTableIfNotExists()
{
  $db = ORM::get_db();
  $tableCheckQuery = "CREATE TABLE IF NOT EXISTS tbl_mpesa_transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        TransID VARCHAR(255) NOT NULL,
        TransactionType VARCHAR(255) NOT NULL,
        TransTime VARCHAR(255) NOT NULL,
        TransAmount DECIMAL(10, 2) NOT NULL,
        BusinessShortCode VARCHAR(255) NOT NULL,
        BillRefNumber VARCHAR(255) NOT NULL,
        OrgAccountBalance DECIMAL(10, 2) NOT NULL,
        MSISDN VARCHAR(255) NOT NULL,
        FirstName VARCHAR(255) NOT NULL
    )";
  $db->exec($tableCheckQuery);
}
