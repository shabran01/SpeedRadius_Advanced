<?php

/**
 * Pay Hero Payment Gateway
 * https://docs.payhero.co.ke/
 *
 * Integrated into SpeedRadius ISP-MASTER
 * Uses Pay Hero STK Push API with Basic Auth token authentication
 **/


function PayHero_validate_config()
{
    global $config;
    if (empty($config['payhero_auth_token']) || empty($config['payhero_channel_id'])) {
        sendTelegram("Pay Hero payment gateway not configured");
        r2(U . 'order/package', 'w', Lang::T("Admin has not yet setup Pay Hero payment gateway, please tell admin"));
    }
}


function PayHero_show_config()
{
    global $ui, $config;
    $ui->assign('_title', 'Pay Hero - Payment Gateway - ' . $config['CompanyName']);
    $ui->display('payhero.tpl');
}


function PayHero_save_config()
{
    global $admin, $_L;

    $payhero_auth_token = _post('payhero_auth_token');
    $payhero_channel_id = _post('payhero_channel_id');

    $settings = [
        'payhero_auth_token' => $payhero_auth_token,
        'payhero_channel_id' => $payhero_channel_id,
    ];

    foreach ($settings as $key => $value) {
        $d = ORM::for_table('tbl_appconfig')->where('setting', $key)->find_one();
        if ($d) {
            $d->value = $value;
            $d->save();
        } else {
            $d = ORM::for_table('tbl_appconfig')->create();
            $d->setting = $key;
            $d->value = $value;
            $d->save();
        }
    }

    _log('[' . $admin['username'] . ']: Pay Hero Settings Saved', 'Admin', $admin['id']);
    r2(U . 'paymentgateway/PayHero', 's', $_L['Settings_Saved_Successfully']);
}


function PayHero_create_transaction($trx, $user)
{
    $url = U . "plugin/initiatepayhero";

    $d = ORM::for_table('tbl_payment_gateway')
        ->where('username', $user['username'])
        ->where('status', 1)
        ->find_one();

    $d->gateway_trx_id   = '';
    $d->payment_method   = 'Pay Hero STK';
    $d->pg_url_payment   = $url;
    $d->pg_request       = '';
    $d->expired_date     = date('Y-m-d H:i:s', strtotime("+5 minutes"));
    $d->save();

    r2(U . "order/view/" . $d['id'], 's', Lang::T("Create Transaction Success, Please click pay now to process payment"));
    die();
}


function PayHero_payment_notification()
{
    $captureLogs = file_get_contents("php://input");
    file_put_contents('payhero_callback.log', "\n--- " . date('Y-m-d H:i:s') . " ---\n" . $captureLogs . "\n", FILE_APPEND);

    $data = json_decode($captureLogs);

    if (!$data || !isset($data->response)) {
        error_log("PayHero: Invalid callback payload");
        exit();
    }

    $response        = $data->response;
    $result_code     = isset($response->ResultCode) ? $response->ResultCode : -1;
    $result_desc     = isset($response->ResultDesc) ? $response->ResultDesc : '';
    $checkout_req_id = isset($response->CheckoutRequestID) ? $response->CheckoutRequestID : '';
    $mpesa_code      = isset($response->MpesaReceiptNumber) ? $response->MpesaReceiptNumber : '';
    $amount_paid     = isset($response->Amount) ? $response->Amount : 0;
    $status_text     = isset($response->Status) ? $response->Status : '';
    $external_ref    = isset($response->ExternalReference) ? $response->ExternalReference : '';

    // Match pending gateway record — try CheckoutRequestID first, then ExternalReference
    $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
        ->where('checkout', $checkout_req_id)
        ->where('status', 1)
        ->order_by_desc('id')
        ->find_one();

    // Fallback: match by ExternalReference (TRX-{id}) if checkout didn't match
    if (!$PaymentGatewayRecord && !empty($external_ref)) {
        $trx_id = str_replace('TRX-', '', $external_ref);
        if (is_numeric($trx_id)) {
            $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
                ->where('status', 1)
                ->find_one((int)$trx_id);
        }
    }

    if (!$PaymentGatewayRecord) {
        error_log("PayHero: No matching pending transaction for CheckoutRequestID: " . $checkout_req_id . " / ExternalReference: " . $external_ref);
        exit();
    }

    $now = date('Y-m-d H:i:s');

    // Handle non-success result codes
    if ($result_code != 0) {
        $PaymentGatewayRecord->status          = 1; // Keep as unpaid — allow retry
        $PaymentGatewayRecord->pg_paid_response = $result_desc;
        $PaymentGatewayRecord->save();
        error_log("PayHero: Payment failed for CheckoutRequestID: " . $checkout_req_id . " | ResultCode: " . $result_code . " | " . $result_desc);
        exit();
    }

    // ResultCode = 0 — payment successful

    // Duplicate transaction guard
    $check_existing = ORM::for_table('tbl_payment_gateway')
        ->where('gateway_trx_id', $mpesa_code)
        ->find_one();

    if ($check_existing) {
        echo "double callback, ignore one";
        die;
    }

    $uname   = $PaymentGatewayRecord->username;
    $UserId  = ORM::for_table('tbl_customers')->where('username', $uname)->find_one()->id;

    if (!Package::rechargeUser($UserId, $PaymentGatewayRecord['routers'], $PaymentGatewayRecord['plan_id'], $PaymentGatewayRecord['gateway'], $mpesa_code)) {
        // Payment received but package activation failed
        $PaymentGatewayRecord->status          = 2;
        $PaymentGatewayRecord->paid_date       = $now;
        $PaymentGatewayRecord->gateway_trx_id  = $mpesa_code;
        $PaymentGatewayRecord->pg_paid_response = 'Payment successful but package activation failed - please contact support';
        $PaymentGatewayRecord->save();

        error_log("PayHero: Payment OK but activation failed for user: " . $uname . ", Code: " . $mpesa_code);
    } else {
        // Full success
        $PaymentGatewayRecord->status          = 2;
        $PaymentGatewayRecord->paid_date       = $now;
        $PaymentGatewayRecord->gateway_trx_id  = $mpesa_code;
        $PaymentGatewayRecord->pg_paid_response = 'Payment successful and package activated';
        $PaymentGatewayRecord->save();

        error_log("PayHero: Payment and activation successful for user: " . $uname . ", Amount: " . $amount_paid . ", Code: " . $mpesa_code);
    }

    exit();
}


function PayHero_get_status($trx, $user)
{
    global $config;

    if (empty($config['payhero_auth_token'])) {
        r2(U . 'order/view/' . $trx['id'], 'e', Lang::T("Pay Hero not configured"));
    }

    if ($trx['status'] == 2) {
        r2(U . 'order/view/' . $trx['id'], 's', Lang::T("Payment Successful"));
    }

    if ($trx['status'] == 4) {
        r2(U . 'order/view/' . $trx['id'], 'e', Lang::T("Payment Cancelled"));
    }

    // Check via Pay Hero transaction status API
    // Use the Pay Hero reference (gateway_trx_id) or fall back to TRX-{id}
    $reference = !empty($trx['gateway_trx_id']) ? $trx['gateway_trx_id'] : 'TRX-' . $trx['id'];

    $curl = curl_init('https://backend.payhero.co.ke/api/v2/transaction-status?reference=' . urlencode($reference));
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Authorization: ' . $config['payhero_auth_token'],
        'Content-Type: application/json',
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $raw = curl_exec($curl);
    curl_close($curl);

    $result = json_decode($raw);

    if ($result && isset($result->status)) {
        if ($result->status === 'SUCCESS') {
            r2(U . 'order/view/' . $trx['id'], 's', Lang::T("Payment Confirmed"));
        } elseif ($result->status === 'FAILED') {
            r2(U . 'order/view/' . $trx['id'], 'e', Lang::T("Payment Failed"));
        }
    }

    // Still queued or unknown
    r2(U . 'order/view/' . $trx['id'], 'w', Lang::T("Payment is still being processed, please wait"));
}
