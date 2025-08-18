<?php

function initiatepaystack()
{
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    // Format phone number if needed
    $phone = (substr($phone, 0, 1) == '+') ? str_replace('+', '', $phone) : $phone;
    $phone = (substr($phone, 0, 1) == '0') ? preg_replace('/^0/', '234', $phone) : $phone;

    // Check and update user records
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

    // Update user phone
    $ThisUser = ORM::for_table('tbl_customers')
        ->where('username', $username)
        ->order_by_desc('id')
        ->find_one();
    $ThisUser->phonenumber = $phone;
    $ThisUser->save();

    // Get payment gateway record
    $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
        ->where('username', $username)
        ->where('status', 1)
        ->order_by_desc('id')
        ->find_one();

    if (!$PaymentGatewayRecord) {
        // Create a new payment gateway record with default values
        $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')->create();
        $PaymentGatewayRecord->username = $username;
        $PaymentGatewayRecord->gateway = 'paystack';
        $PaymentGatewayRecord->plan_id = 0;
        $PaymentGatewayRecord->plan_name = 'Default Plan';
        $PaymentGatewayRecord->routers_id = 0;
        $PaymentGatewayRecord->routers = 'default';
        $PaymentGatewayRecord->price = 0;
        $PaymentGatewayRecord->pg_url_payment = '#'; // Set default value
        $PaymentGatewayRecord->created_date = date('Y-m-d H:i:s');
        $PaymentGatewayRecord->status = 1;
        $PaymentGatewayRecord->save();
    } else if (empty($PaymentGatewayRecord->pg_url_payment)) {
        // If record exists but pg_url_payment is null, set default value
        $PaymentGatewayRecord->pg_url_payment = '#';
        $PaymentGatewayRecord->save();
    }

    // Get Paystack configuration
    $paystack_secret_key = ORM::for_table('tbl_appconfig')
        ->where('setting', 'paystack_secret_key')
        ->find_one();
    $paystack_secret_key = ($paystack_secret_key) ? $paystack_secret_key->value : null;

    if (!$paystack_secret_key) {
        echo json_encode(["status" => "error", "message" => "Paystack is not properly configured"]);
        exit;
    }

    // Generate reference
    $reference = 'TRX-' . strtoupper(substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 4)), 0, 8));

    // Initialize transaction with Paystack
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . $paystack_secret_key,
            "Content-Type: application/json"
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            "amount" => $PaymentGatewayRecord->price * 100, // Convert to kobo
            "email" => $email,
            "reference" => $reference,
            "callback_url" => U . 'callback/paystack',
            "metadata" => [
                "username" => $username,
                "transaction_id" => $PaymentGatewayRecord->id
            ]
        ])
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo json_encode(["status" => "error", "message" => "cURL Error: " . $err]);
        exit;
    }

    $result = json_decode($response);
    
    if ($result->status) {
        // Update transaction record with Paystack URL
        $PaymentGatewayRecord->gateway_trx_id = $reference;
        $PaymentGatewayRecord->payment_method = 'Paystack';
        $PaymentGatewayRecord->pg_url_payment = $result->data->authorization_url;
        $PaymentGatewayRecord->pg_request = $response;
        $PaymentGatewayRecord->expired_date = date('Y-m-d H:i:s', strtotime('+6 hours'));
        $PaymentGatewayRecord->save();

        echo json_encode([
            "status" => "success",
            "message" => "Payment initiated successfully",
            "redirect_url" => $result->data->authorization_url
        ]);
    } else {
        // Keep the default URL if Paystack initialization fails
        echo json_encode([
            "status" => "error",
            "message" => "Failed to initialize payment: " . $result->message
        ]);
    }
}
