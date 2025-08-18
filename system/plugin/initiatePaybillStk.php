<?php

function initiatePaybillPayment()
{
    // Ensure POST variables are set and sanitize input
    $username = isset($_POST['username']) ? filter_var($_POST['username'], FILTER_SANITIZE_STRING) : null;
    $phone = isset($_POST['phone']) ? filter_var($_POST['phone'], FILTER_SANITIZE_STRING) : null;

    if (!$username || !$phone) {
        echo "<script>toastr.error('Invalid input data');</script>";
        return;
    }

    // Normalize phone number
    $phone = preg_replace(['/^\+/', '/^0/', '/^7/', '/^1/'], ['', '254', '2547', '2541'], $phone);

    // Retrieve bank details from the database
    $bankaccount = ORM::for_table('tbl_appconfig')->where('setting', 'PaybillAcc')->find_one();
    $bankname = ORM::for_table('tbl_appconfig')->where('setting', 'PaybillName')->find_one();
    $bankaccount = $bankaccount ? $bankaccount->value : null;
    $bankname = $bankname ? $bankname->value : null;

    if (!$bankaccount || !$bankname) {
        echo "<script>toastr.error('Could not complete the payment req, please contact admin');</script>";
        return;
    }

    // Check for existing user details
    $CheckId = ORM::for_table('tbl_customers')->where('username', $username)->order_by_desc('id')->find_one();
    $CheckUser = ORM::for_table('tbl_customers')->where('phonenumber', $phone)->find_many();
    $UserId = $CheckId ? $CheckId->id : null;

    if ($CheckUser) {
        ORM::for_table('tbl_customers')->where('phonenumber', $phone)->where_not_equal('id', $UserId)->delete_many();
    }

    // Retrieve payment gateway record
    $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
        ->where('username', $username)
        ->where('status', 1)
        ->order_by_desc('id')
        ->find_one();

    if (!$PaymentGatewayRecord) {
        echo "<script>toastr.error('Could not complete the payment req, please contact administrator');</script>";
        return;
    }

    // Update user phone number
    $ThisUser = ORM::for_table('tbl_customers')->where('username', $username)->order_by_desc('id')->find_one();
    if ($ThisUser) {
        $ThisUser->phonenumber = $phone;
        $ThisUser->save();
    }

    $amount = $PaymentGatewayRecord->price;

    // Safaricom API credentials
    $consumerKey = 'YOUR_CONSUMER_KEY';
    $consumerSecret = 'YOUR_CONSUMER_SECRET';

    // Get access token
    $access_token_url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $curl = curl_init($access_token_url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json; charset=utf8']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_USERPWD, "$consumerKey:$consumerSecret");
    $result = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($status !== 200) {
        echo "<script>toastr.error('Failed to get access token');</script>";
        return;
    }

    $result = json_decode($result);
    $access_token = $result->access_token;

    // Initiate Paybill payment
    $paybill_url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $Timestamp = date("YmdHis");
    $BusinessShortCode = 'YOUR_BUSINESS_SHORTCODE';
    $Passkey = 'YOUR_PASSKEY';
    $Password = base64_encode($BusinessShortCode . $Passkey . $Timestamp);
    $CallBackURL = U . 'callback/PaybillCallback';

    $curl_post_data = [
        'BusinessShortCode' => $BusinessShortCode,
        'Password' => $Password,
        'Timestamp' => $Timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => $BusinessShortCode,
        'PhoneNumber' => $phone,
        'CallBackURL' => $CallBackURL,
        'AccountReference' => $bankaccount,
        'TransactionDesc' => 'PayBill Payment'
    ];

    $curl = curl_init($paybill_url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
    $curl_response = curl_exec($curl);
    curl_close($curl);

    $mpesaResponse = json_decode($curl_response);
    $responseCode = $mpesaResponse->ResponseCode;
    $resultDesc = $mpesaResponse->resultDesc;
    $MerchantRequestID = $mpesaResponse->MerchantRequestID;
    $CheckoutRequestID = $mpesaResponse->CheckoutRequestID;

    if ($responseCode == "0") {
        date_default_timezone_set('Africa/Nairobi');
        $now = date("Y-m-d H:i:s");

        $PaymentGatewayRecord->pg_paid_response = $resultDesc;
        $PaymentGatewayRecord->username = $username;
        $PaymentGatewayRecord->checkout = $CheckoutRequestID;
        $PaymentGatewayRecord->payment_method = 'Mpesa PayBill';
        $PaymentGatewayRecord->payment_channel = 'Mpesa PayBill';
        $PaymentGatewayRecord->save();

        if (!empty($_POST['channel'])) {
            echo json_encode(["status" => "success", "message" => "Enter Pin to complete"]);
        } else {
            echo "<script>toastr.success('Enter Mpesa Pin to complete');</script>";
        }
    } else {
        echo "<script>toastr.error('We could not complete the payment for you, please contact administrator');</script>";
    }
}

?>
