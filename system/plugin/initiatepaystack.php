<?php

function initiatepaystack()
{
    // Get user data from the most recent active payment gateway record
    $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
        ->where('status', 1)
        ->order_by_desc('id')
        ->find_one();

    if (!$PaymentGatewayRecord) {
        echo json_encode([
            "status" => "error",
            "message" => "No active payment found. Please try ordering again."
        ]);
        die();
    }

    $username = $PaymentGatewayRecord->username;

    // Get phone and email from customer record or POST data
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';

    if (empty($phone) || empty($email)) {
        $ThisUser = ORM::for_table('tbl_customers')
            ->where('username', $username)
            ->find_one();
        if ($ThisUser) {
            $phone = empty($phone) ? $ThisUser->phonenumber : $phone;
            $email = empty($email) ? $ThisUser->email : $email;
        }
    }

    // Validate we have the required data
    if (empty($username) || empty($phone) || empty($email)) {
        echo json_encode([
            "status" => "error",
            "message" => "Unable to get user information (Phone or Email missing)."
        ]);
        die();
    }

    // Format phone number for Paystack (Kenya usually requires 254...)
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (substr($phone, 0, 1) == '0') {
        $phone = '254' . substr($phone, 1);
    } elseif (substr($phone, 0, 1) == '+') {
        $phone = substr($phone, 1);
    }
    // Ensure it starts with 254 for Kenya M-Pesa
    if (substr($phone, 0, 3) != '254') {
        // Warning: Number might be invalid for M-Pesa
    }

    // Get Paystack configuration
    $paystack_secret_key = ORM::for_table('tbl_appconfig')
        ->where('setting', 'paystack_secret_key')
        ->find_one();
    $paystack_secret_key = ($paystack_secret_key) ? $paystack_secret_key->value : null;

    if (!$paystack_secret_key) {
        echo json_encode(["status" => "error", "message" => "Paystack is not properly configured"]);
        die();
    }

    // Generate unique reference
    $reference = 'PSK-' . strtoupper(substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 4)), 0, 8)) . '-' . time();

    // Prepare Payload for Charge API
    $payload = [
        "email" => $email,
        "amount" => $PaymentGatewayRecord->price * 100, // Amount in kobo/cents
        "currency" => "KES",
        "reference" => $reference,
        "mobile_money" => [
            "phone" => $phone,
            "provider" => "mpesa"
        ],
        "metadata" => [
            "username" => $username,
            "transaction_id" => $PaymentGatewayRecord->id
        ]
    ];

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/charge");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $paystack_secret_key,
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        echo json_encode(["status" => "error", "message" => "cURL Error: " . $err]);
        die();
    }

    $result = json_decode($response);

    if ($result && $result->status) {
        // Success initiation

        // Ensure values are set to avoid NULL constraints
        $PaymentGatewayRecord->gateway_trx_id = $reference;
        $PaymentGatewayRecord->payment_method = 'Paystack STK';
        $PaymentGatewayRecord->pg_url_payment = 'stk_initiated'; // Explicit string value
        $PaymentGatewayRecord->pg_request = json_encode($result);
        $PaymentGatewayRecord->expired_date = date('Y-m-d H:i:s', strtotime('+6 hours'));

        // Save using ORM
        $PaymentGatewayRecord->save();

        $message = "Payment initiated. Please check your phone ($phone) and enter your PIN.";
        if (isset($result->data->display_text)) {
            $message = $result->data->display_text;
        }

        if (!empty($_POST['channel'])) {
            echo json_encode([
                "status" => "success",
                "message" => $message,
                "reference" => $reference
            ]);
            exit;
        } else {
            // Web interface response
            echo "<script>
                alert('" . addslashes($message) . "');
                setTimeout(function() {
                    window.location.href = '" . U . "order/view/" . $PaymentGatewayRecord->id . "';
                }, 3000);
            </script>";
            exit;
        }

    } else {
        // Failed
        $errMsg = "Payment initiation failed.";
        if (isset($result->message)) {
            $errMsg .= " " . $result->message;
        }

        echo json_encode([
            "status" => "error",
            "message" => $errMsg
        ]);
    }
}
