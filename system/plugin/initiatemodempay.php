<?php

/**
 * ModemPay Payment Initiator
 * ===========================
 * Called when the customer clicks "Pay Now" on the order view page
 * (or from the captive portal with ?channel=Yes).
 *
 * Flow:
 *   1. Load the active pending payment record (tbl_payment_gateway, status=1)
 *   2. POST /v1/payments to create a Payment Intent on ModemPay
 *   3. Store payment_intent_id → gateway_trx_id, intent_secret → pg_request
 *   4. Redirect the customer to the hosted checkout (payment_link)
 *
 * Docs: https://docs.modempay.com/api-reference  (POST /v1/payments)
 */

function initiatemodempay()
{
    global $config;

    // Make sure the gateway helpers (_modempay_post) are available
    if (!function_exists('_modempay_post') && file_exists(__DIR__ . '/../paymentgateway/ModemPay.php')) {
        require_once __DIR__ . '/../paymentgateway/ModemPay.php';
    }

    // 1. Active pending payment record
    $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
        ->where('status', 1)
        ->order_by_desc('id')
        ->find_one();

    if (!$PaymentGatewayRecord) {
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => "No active payment found. Please try ordering again."]);
        die();
    }

    $username = $PaymentGatewayRecord->username;

    // 2. Customer details (for metadata)
    $ThisUser = ORM::for_table('tbl_customers')->where('username', $username)->find_one();

    $phone    = $ThisUser ? trim($ThisUser->phonenumber) : '';
    $email    = $ThisUser ? trim($ThisUser->email)       : '';
    $fullname = $ThisUser ? trim($ThisUser->fullname)    : $username;

    // Accept phone from POST (captive portal sends it) if empty from customer record
    if (empty($phone) && isset($_POST['phone'])) {
        $phone = trim($_POST['phone']);
    }

    // Normalise phone: strip '+', keep digits
    $phone = preg_replace('/[^0-9]/', '', $phone);

    $amount   = (float) $PaymentGatewayRecord->price;
    $currency = !empty($config['modempay_currency']) ? $config['modempay_currency'] : 'GMD';

    // Redirect / callback URLs
    $trxId      = intval($PaymentGatewayRecord->id);
    $returnUrl  = U . 'order/view/' . $trxId;
    $cancelUrl  = U . 'order/view/' . $trxId;
    $callbackUrl = U . 'callback/ModemPay';

    // 3. Create the Payment Intent
    $body = [
        'data' => [
            'amount'       => $amount,
            'currency'     => $currency,
            'return_url'   => $returnUrl,
            'cancel_url'   => $cancelUrl,
            'callback_url' => $callbackUrl,
            'metadata'     => [
                'trx_id'    => $trxId,
                'username'  => $username,
                'plan_name' => $PaymentGatewayRecord->plan_name,
            ],
        ],
    ];

    if (!empty($phone)) {
        $body['data']['customer_phone'] = $phone;
    }
    if (!empty($email)) {
        $body['data']['customer_email'] = $email;
    }
    if (!empty($fullname)) {
        $body['data']['customer_name'] = $fullname;
    }

    $response = _modempay_post('/v1/payments', $body);

    // Log the raw response for debugging
    if (is_array($response)) {
        file_put_contents('modempay_init.log', "\n--- " . date('Y-m-d H:i:s') . " ---\nIntent request: " . json_encode($body) . "\nResponse: " . json_encode($response) . "\n", FILE_APPEND);
    } else {
        file_put_contents('modempay_init.log', "\n--- " . date('Y-m-d H:i:s') . " ---\nIntent request failed (no/invalid response)\n", FILE_APPEND);
    }

    // Normalise response — ModemPay may wrap the intent in a "data" envelope
    // (SDK shows $payment->data->payment_link) or return it flat.
    $intent = [];
    if (is_array($response)) {
        if (isset($response['data']) && is_array($response['data'])) {
            $intent = $response['data'];
        } else {
            $intent = $response;
        }
    }

    $intentId    = isset($intent['payment_intent_id']) ? $intent['payment_intent_id'] : '';
    $intentSecret = isset($intent['intent_secret'])    ? $intent['intent_secret']    : '';
    $paymentLink  = isset($intent['payment_link'])      ? $intent['payment_link']     : '';

    // 4. Validate response — we need a payment link to send the customer to
    if (empty($intentId) || empty($paymentLink)) {
        $errMsg = isset($response['message']) ? $response['message']
                 : (isset($intent['message']) ? $intent['message']
                 : (isset($response['error']) ? (is_array($response['error']) ? json_encode($response['error']) : $response['error'])
                 : 'Could not create ModemPay payment. Please contact admin.'));

        if (!empty($_POST['channel'])) {
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => $errMsg]);
        } else {
            echo "<script>
                alert('" . addslashes($errMsg) . "');
                window.location.href = '" . U . "order/view/" . $trxId . "';
            </script>";
        }
        die();
    }

    // 5. Store intent ids on the payment record
    $PaymentGatewayRecord->gateway_trx_id   = $intentId;
    $PaymentGatewayRecord->pg_request       = $intentSecret;
    $PaymentGatewayRecord->payment_method   = 'ModemPay';
    $PaymentGatewayRecord->payment_channel  = 'ModemPay';
    $PaymentGatewayRecord->pg_paid_response = 'Payment intent created - awaiting payment';
    $PaymentGatewayRecord->save();

    // 6. Redirect / return the link
    if (!empty($_POST['channel'])) {
        // Captive portal / API — return the payment link so JS can open it
        header('Content-Type: application/json');
        echo json_encode([
            "status"        => "success",
            "message"       => "Redirecting to ModemPay checkout...",
            "redirect_url"  => $paymentLink,
            "trx_id"        => $trxId,
        ]);
    } else {
        // Standard web — redirect straight to the hosted checkout
        header('Location: ' . $paymentLink);
    }
    die();
}
