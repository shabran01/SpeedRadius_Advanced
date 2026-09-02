<?php

/**
 * ModemPay Payment Gateway
 * https://docs.modempay.com/
 *
 * Integrated into SpeedRadius ISP-MASTER
 * Uses ModemPay Payment Intents API with Bearer token authentication.
 * Flow: Create Intent → redirect to hosted checkout → webhook notifies on charge.succeeded
 *
 * API Base URL  : https://api.modempay.com
 * Create Intent : POST /v1/payments
 * Webhook header: x-modem-signature  (HMAC-SHA512 of raw body using webhook secret)
 **/


// ── Config hooks ─────────────────────────────────────────────────────────────

function ModemPay_validate_config()
{
    global $config;
    if (empty($config['modempay_secret_key'])) {
        sendTelegram("ModemPay payment gateway not configured");
        r2(U . 'order/package', 'w', Lang::T("Admin has not yet setup ModemPay payment gateway, please tell admin"));
    }
}

function ModemPay_show_config()
{
    global $ui, $config;
    $ui->assign('_title', 'ModemPay - Payment Gateway - ' . $config['CompanyName']);
    $ui->display('modempay.tpl');
}

function ModemPay_save_config()
{
    global $admin, $_L;

    $settings = [
        'modempay_secret_key'     => _post('modempay_secret_key'),
        'modempay_webhook_secret' => _post('modempay_webhook_secret'),
        'modempay_currency'       => _post('modempay_currency'),
        'modempay_environment'    => _post('modempay_environment'),
    ];

    foreach ($settings as $key => $value) {
        $d = ORM::for_table('tbl_appconfig')->where('setting', $key)->find_one();
        if ($d) {
            $d->value = $value;
            $d->save();
        } else {
            $d          = ORM::for_table('tbl_appconfig')->create();
            $d->setting = $key;
            $d->value   = $value;
            $d->save();
        }
    }

    _log('[' . $admin['username'] . ']: ModemPay Settings Saved', 'Admin', $admin['id']);
    r2(U . 'paymentgateway/ModemPay', 's', $_L['Settings_Saved_Successfully']);
}


// ── Helpers ───────────────────────────────────────────────────────────────────

/**
 * Returns the ModemPay API base URL based on the configured environment.
 * Test uses a separate subdomain; live hits api.modempay.com directly.
 */
function _modempay_base_url()
{
    global $config;
    $env = isset($config['modempay_environment']) ? $config['modempay_environment'] : 'live';
    // Both environments share the same API base; test/live is determined by the API key prefix.
    return 'https://api.modempay.com';
}

/**
 * POST to ModemPay API with Bearer auth.
 * Returns decoded response array or false on failure.
 */
function _modempay_post(string $endpoint, array $body)
{
    global $config;

    $url = _modempay_base_url() . $endpoint;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $config['modempay_secret_key'],
        'Content-Type: application/json',
        'Accept: application/json',
    ]);

    $raw      = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($raw === false) {
        return false;
    }

    $decoded = json_decode($raw, true);
    return $decoded;
}

/**
 * Verify the HMAC-SHA512 signature sent in the x-modem-signature header.
 * Returns true if valid, false otherwise.
 */
function _modempay_verify_signature(string $rawPayload, string $signature, string $secret): bool
{
    if (empty($signature) || empty($secret)) {
        return false;
    }
    $computed = hash_hmac('sha512', $rawPayload, $secret);
    return hash_equals($computed, strtolower($signature));
}


// ── Transaction hooks ─────────────────────────────────────────────────────────

function ModemPay_create_transaction($trx, $user)
{
    // Defer the actual payment intent creation to the initiatemodempay plugin,
    // which is called when the customer clicks "Pay Now" on the order view page.
    // This matches the PayHero / BankStkPush / PesaPal two-step pattern.
    $url = U . 'plugin/initiatemodempay';

    $d = ORM::for_table('tbl_payment_gateway')
        ->where('username', $user['username'])
        ->where('status', 1)
        ->find_one();

    $d->gateway_trx_id  = '';
    $d->payment_method  = 'ModemPay';
    $d->pg_url_payment  = $url;
    $d->pg_request      = '';
    $d->expired_date    = date('Y-m-d H:i:s', strtotime('+30 minutes'));
    $d->save();

    r2(U . 'order/view/' . $d['id'], 's', Lang::T("Create Transaction Success, Please click pay now to process payment"));
    die();
}


function ModemPay_payment_notification()
{
    global $config;

    // Respond 200 immediately to prevent retries while we process
    http_response_code(200);

    $rawPayload = file_get_contents('php://input');

    // Log all incoming webhooks for debugging
    file_put_contents(
        'modempay_webhook.log',
        "\n--- " . date('Y-m-d H:i:s') . " ---\n" . $rawPayload . "\n",
        FILE_APPEND
    );

    if (empty($rawPayload)) {
        error_log("ModemPay: Empty webhook payload received");
        exit();
    }

    // Validate webhook signature if a secret is configured
    $webhookSecret = isset($config['modempay_webhook_secret']) ? $config['modempay_webhook_secret'] : '';
    if (!empty($webhookSecret)) {
        // PHP converts header 'x-modem-signature' → HTTP_X_MODEM_SIGNATURE
        $signature = '';
        if (isset($_SERVER['HTTP_X_MODEM_SIGNATURE'])) {
            $signature = $_SERVER['HTTP_X_MODEM_SIGNATURE'];
        } elseif (isset($_SERVER['HTTP_X_MODEMPAY_SIGNATURE'])) {
            $signature = $_SERVER['HTTP_X_MODEMPAY_SIGNATURE'];
        }

        if (!_modempay_verify_signature($rawPayload, $signature, $webhookSecret)) {
            error_log("ModemPay: Invalid webhook signature — aborting");
            file_put_contents('modempay_webhook.log', "SIGNATURE INVALID — rejected\n", FILE_APPEND);
            exit();
        }
    }

    $event = json_decode($rawPayload, true);

    if (!$event || !isset($event['event'])) {
        error_log("ModemPay: Malformed webhook payload");
        exit();
    }

    $eventType = $event['event'];
    $payload   = isset($event['payload']) ? $event['payload'] : [];

    file_put_contents(
        'modempay_webhook.log',
        "Event: $eventType\n",
        FILE_APPEND
    );

    // Only process successful charges
    if ($eventType !== 'charge.succeeded') {
        exit();
    }

    $txRef      = isset($payload['transaction_reference']) ? $payload['transaction_reference'] : '';
    $intentId   = isset($payload['payment_intent_id'])     ? $payload['payment_intent_id']     : '';
    $amountPaid = isset($payload['amount'])                ? $payload['amount']                : 0;
    $status     = isset($payload['status'])                ? $payload['status']                : '';

    if ($status !== 'completed') {
        error_log("ModemPay: charge.succeeded but status is '$status', skipping");
        exit();
    }

    // Duplicate transaction guard
    $existing = ORM::for_table('tbl_payment_gateway')
        ->where('gateway_trx_id', $txRef)
        ->where('status', 2)
        ->find_one();

    if ($existing) {
        file_put_contents('modempay_webhook.log', "DUPLICATE — already processed: $txRef\n", FILE_APPEND);
        exit();
    }

    // Match pending gateway record by payment_intent_id stored in gateway_trx_id
    $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
        ->where('gateway_trx_id', $intentId)
        ->where('status', 1)
        ->order_by_desc('id')
        ->find_one();

    if (!$PaymentGatewayRecord) {
        // Fallback: match by metadata trx_id embedded in intent
        error_log("ModemPay: No matching pending transaction for intent: $intentId / ref: $txRef");
        file_put_contents('modempay_webhook.log', "NO MATCH for intent: $intentId\n", FILE_APPEND);
        exit();
    }

    $uname  = $PaymentGatewayRecord->username;
    $userId = ORM::for_table('tbl_customers')
        ->where('username', $uname)
        ->find_one();

    if (!$userId) {
        error_log("ModemPay: Customer not found for username: $uname");
        exit();
    }

    $now = date('Y-m-d H:i:s');

    if (!Package::rechargeUser($userId->id, $PaymentGatewayRecord['routers'], $PaymentGatewayRecord['plan_id'], $PaymentGatewayRecord['gateway'], $txRef)) {
        // Payment received but package activation failed
        $PaymentGatewayRecord->status           = 2;
        $PaymentGatewayRecord->paid_date        = $now;
        $PaymentGatewayRecord->gateway_trx_id   = $txRef;
        $PaymentGatewayRecord->pg_paid_response = 'Payment received but package activation failed — contact support';
        $PaymentGatewayRecord->payment_method   = 'ModemPay';
        $PaymentGatewayRecord->payment_channel  = isset($payload['payment_method']) ? $payload['payment_method'] : 'ModemPay';
        $PaymentGatewayRecord->save();

        error_log("ModemPay: Payment OK but activation failed for user: $uname, Ref: $txRef");
    } else {
        // Full success
        $PaymentGatewayRecord->status           = 2;
        $PaymentGatewayRecord->paid_date        = $now;
        $PaymentGatewayRecord->gateway_trx_id   = $txRef;
        $PaymentGatewayRecord->pg_paid_response = json_encode($payload);
        $PaymentGatewayRecord->payment_method   = 'ModemPay';
        $PaymentGatewayRecord->payment_channel  = isset($payload['payment_method']) ? $payload['payment_method'] : 'ModemPay';
        $PaymentGatewayRecord->save();

        error_log("ModemPay: Payment and activation successful for user: $uname, Amount: $amountPaid, Ref: $txRef");
    }

    exit();
}


function ModemPay_get_status($trx, $user)
{
    global $config;

    if (empty($config['modempay_secret_key'])) {
        r2(U . 'order/view/' . $trx['id'], 'e', Lang::T("ModemPay not configured"));
    }

    // The intent_secret is stored in pg_request; use it to verify
    $intentSecret = $trx['pg_request'];

    if (empty($intentSecret)) {
        r2(U . 'order/view/' . $trx['id'], 'w', Lang::T("Transaction not initiated yet"));
    }

    $url = _modempay_base_url() . '/v1/payments/verify?intent_secret=' . urlencode($intentSecret);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $config['modempay_secret_key'],
        'Accept: application/json',
    ]);
    $raw = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($raw, true);

    if (!$result || empty($result['status'])) {
        r2(U . 'order/view/' . $trx['id'], 'w', Lang::T("Unable to check payment status"));
    }

    $intent = isset($result['data']) ? $result['data'] : [];
    $status = isset($intent['status']) ? $intent['status'] : '';

    if ($status === 'completed') {
        // Webhook may not have arrived yet — complete the payment here as a fallback.
        // Only process once (duplicate-safe via status=1 + gateway_trx_id check).
        $alreadyPaid = ORM::for_table('tbl_payment_gateway')
            ->where('id', $trx['id'])
            ->where('status', 2)
            ->find_one();
        if (!$alreadyPaid) {
            $txRef = isset($intent['transaction_reference']) ? $intent['transaction_reference']
                   : (isset($result['transaction_reference']) ? $result['transaction_reference'] : '');
            $now = date('Y-m-d H:i:s');

            $cust = ORM::for_table('tbl_customers')->where('username', $trx['username'])->find_one();
            if ($cust) {
                $rechargeOk = Package::rechargeUser($cust->id, $trx['routers'], $trx['plan_id'], $trx['gateway'], $txRef);
                // Mark paid regardless (payment was received); note activation result
                $trx->status           = 2;
                $trx->paid_date        = $now;
                $trx->gateway_trx_id   = $txRef ? $txRef : $trx->gateway_trx_id;
                $trx->pg_paid_response = $rechargeOk
                    ? 'Payment successful and package activated'
                    : 'Payment successful but package activation failed - please contact support';
                $trx->payment_method   = 'ModemPay';
                $trx->payment_channel  = 'ModemPay';
                $trx->save();
            } else {
                $trx->status = 2;
                $trx->paid_date = $now;
                $trx->pg_paid_response = 'Payment successful (customer not found for activation)';
                $trx->save();
            }
        }
        r2(U . 'order/view/' . $trx['id'], 's', Lang::T("Transaction successful"));
    } elseif ($status === 'cancelled' || $status === 'expired') {
        r2(U . 'order/view/' . $trx['id'], 'd', Lang::T("Transaction was cancelled or expired"));
    } else {
        r2(U . 'order/view/' . $trx['id'], 'w', Lang::T("Transaction still pending"));
    }
}
