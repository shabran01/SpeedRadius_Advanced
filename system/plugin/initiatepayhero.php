<?php

/**
 * Pay Hero STK Push Initiator
 * Called via AJAX / "Pay Now" button from the order view page.
 * POST https://backend.payhero.co.ke/api/v2/payments
 **/

function initiatepayhero()
{
    // Get the transaction for this user (account id from POST or query params)
    $posted_username = null;
    if (!empty($_POST['username'])) {
        $posted_username = trim($_POST['username']);
    } elseif (!empty($_GET['account'])) {
        $posted_username = trim($_GET['account']);
    }
    $posted_trx = isset($_GET['trx']) ? intval($_GET['trx']) : 0;
    $pgQuery = ORM::for_table('tbl_payment_gateway')->where('status', 1);
    if ($posted_trx > 0) {
        $pgQuery = $pgQuery->where('id', $posted_trx);
    } elseif (!empty($posted_username)) {
        $pgQuery = $pgQuery->where('username', $posted_username);
    }
    $PaymentGatewayRecord = $pgQuery->order_by_desc('id')->find_one();

    if (!$PaymentGatewayRecord) {
        echo json_encode([
            "status"  => "error",
            "message" => "No active payment found. Please try ordering again.",
        ]);
        die();
    }

    $username = $PaymentGatewayRecord->username;

    // Get phone from POST or fall back to customer record
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    if (empty($phone)) {
        $ThisUser = ORM::for_table('tbl_customers')
            ->where('username', $username)
            ->find_one();
        $phone = $ThisUser ? $ThisUser->phonenumber : '';
    }

    if (empty($username) || empty($phone)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Unable to get user information. Please contact support.",
        ]);
        die();
    }

    // Normalise phone to 2547XXXXXXXX or 2541XXXXXXXX
    $phone = Text::normalizePhone($phone);

    // Load Pay Hero settings from tbl_appconfig
    $auth_token = ORM::for_table('tbl_appconfig')->where('setting', 'payhero_auth_token')->find_one();
    $channel_id = ORM::for_table('tbl_appconfig')->where('setting', 'payhero_channel_id')->find_one();

    $auth_token = ($auth_token) ? $auth_token->value : null;
    $channel_id = ($channel_id) ? (int)$channel_id->value : null;

    if (empty($auth_token) || empty($channel_id)) {
        echo json_encode([
            "status"  => "error",
            "message" => "Pay Hero is not configured. Please contact admin.",
        ]);
        die();
    }

    // Update customer phone number if changed
    $ThisUser = ORM::for_table('tbl_customers')
        ->where('username', $username)
        ->order_by_desc('id')
        ->find_one();

    if ($ThisUser && !empty($phone)) {
        $ThisUser->phonenumber = $phone;
        $ThisUser->save();
    }

    $amount        = (int) $PaymentGatewayRecord->price;
    $callback_url  = U . 'callback/PayHero';
    $external_ref  = 'TRX-' . $PaymentGatewayRecord->id;

    // Build STK Push request to Pay Hero
    $payload = json_encode([
        'amount'             => $amount,
        'phone_number'       => $phone,
        'channel_id'         => $channel_id,
        'provider'           => 'm-pesa',
        'external_reference' => $external_ref,
        'customer_name'      => $username,
        'callback_url'       => $callback_url,
    ]);

    $curl = curl_init('https://backend.payhero.co.ke/api/v2/payments');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Authorization: ' . $auth_token,
        'Content-Type: application/json',
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    $curl_response = curl_exec($curl);
    $http_code     = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    file_put_contents('payhero_stk.log', "\n--- " . date('Y-m-d H:i:s') . " ---\nRequest: " . $payload . "\nResponse [" . $http_code . "]: " . $curl_response . "\n", FILE_APPEND);

    $mpesaResponse = json_decode($curl_response);

    if (!$mpesaResponse || !isset($mpesaResponse->success) || !$mpesaResponse->success) {
        $errorMsg = isset($mpesaResponse->error) ? $mpesaResponse->error : 'Unknown error from Pay Hero';
        error_log("PayHero initiatepayhero: STK Push failed — " . $errorMsg);
        echo json_encode([
            "status"  => "error",
            "message" => "Could not initiate payment: " . $errorMsg . ". Please try again.",
        ]);
        die();
    }

    $checkout_request_id = isset($mpesaResponse->CheckoutRequestID) ? $mpesaResponse->CheckoutRequestID : '';
    $reference           = isset($mpesaResponse->reference)          ? $mpesaResponse->reference          : '';

    // Save the CheckoutRequestID so the callback can match it
    $PaymentGatewayRecord->checkout         = $checkout_request_id;
    $PaymentGatewayRecord->gateway_trx_id   = $reference;
    $PaymentGatewayRecord->username         = $username;
    $PaymentGatewayRecord->payment_method   = 'Pay Hero STK';
    $PaymentGatewayRecord->payment_channel  = 'Pay Hero STK';
    $PaymentGatewayRecord->pg_paid_response = 'STK Push sent — awaiting PIN';
    $PaymentGatewayRecord->save();

    if (!empty($_POST['channel'])) {
        // API / AJAX call — return JSON
        echo json_encode([
            "status"  => "success",
            "message" => "M-Pesa payment prompt sent. Enter your PIN to complete.",
            "phone"   => $phone,
        ]);
    } else {
        // Web redirect — show alert then redirect to order view
        echo "<script>
            alert('M-Pesa payment initiated successfully! Please check your phone and enter your M-Pesa PIN to complete the payment.');
            setTimeout(function() {
                window.location.href = '" . U . "order/view/" . $PaymentGatewayRecord->id . "';
            }, 3000);
        </script>";
    }
}
