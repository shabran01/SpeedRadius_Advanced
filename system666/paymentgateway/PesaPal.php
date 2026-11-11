<?php

/**
 * PesaPal Payment Gateway — API v3
 * https://developer.pesapal.com/how-to-integrate/e-commerce/api-30-json/api-reference
 *
 * Supports M-Pesa, Airtel Money, Visa/MC via PesaPal hosted payment page.
 * Uses OAuth 2.0 Bearer token (expires 5 minutes) + IPN notifications.
 **/

define('PESAPAL_LIVE_BASE',    'https://pay.pesapal.com/v3');
define('PESAPAL_SANDBOX_BASE', 'https://cybqa.pesapal.com/pesapalv3');

// ── Private helpers ──────────────────────────────────────────────────────────

function _pesapal_base_url()
{
    $env = ORM::for_table('tbl_appconfig')->where('setting', 'pesapal_environment')->find_one();
    return ($env && $env->value === 'sandbox') ? PESAPAL_SANDBOX_BASE : PESAPAL_LIVE_BASE;
}

function _pesapal_get_token()
{
    $key_row    = ORM::for_table('tbl_appconfig')->where('setting', 'pesapal_consumer_key')->find_one();
    $secret_row = ORM::for_table('tbl_appconfig')->where('setting', 'pesapal_consumer_secret')->find_one();

    if (!$key_row || !$secret_row) {
        return null;
    }

    $base    = _pesapal_base_url();
    $payload = json_encode([
        'consumer_key'    => $key_row->value,
        'consumer_secret' => $secret_row->value,
    ]);

    $ch = curl_init($base . '/api/Auth/RequestToken');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $raw  = curl_exec($ch);
    curl_close($ch);

    $resp = json_decode($raw);
    if ($resp && isset($resp->token) && $resp->status == '200') {
        return $resp->token;
    }

    error_log("PesaPal: Auth failed — " . $raw);
    return null;
}

function _pesapal_get_transaction_status($order_tracking_id, $token)
{
    $base = _pesapal_base_url();
    $url  = $base . '/api/Transactions/GetTransactionStatus?orderTrackingId=' . urlencode($order_tracking_id);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token,
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $raw = curl_exec($ch);
    curl_close($ch);

    return json_decode($raw);
}

// ── Config hooks ─────────────────────────────────────────────────────────────

function PesaPal_validate_config()
{
    global $config;
    if (empty($config['pesapal_consumer_key']) || empty($config['pesapal_consumer_secret'])) {
        sendTelegram("PesaPal payment gateway not configured");
        r2(U . 'order/package', 'w', Lang::T("Admin has not yet setup PesaPal payment gateway, please tell admin"));
    }
}

function PesaPal_show_config()
{
    global $ui, $config;
    $ui->assign('_title', 'PesaPal - Payment Gateway - ' . $config['CompanyName']);
    $ui->display('pesapal.tpl');
}

function PesaPal_save_config()
{
    global $admin, $_L;

    $settings = [
        'pesapal_consumer_key'    => _post('pesapal_consumer_key'),
        'pesapal_consumer_secret' => _post('pesapal_consumer_secret'),
        'pesapal_ipn_id'          => _post('pesapal_ipn_id'),
        'pesapal_environment'     => _post('pesapal_environment'),
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

    _log('[' . $admin['username'] . ']: PesaPal Settings Saved', 'Admin', $admin['id']);
    r2(U . 'paymentgateway/PesaPal', 's', $_L['Settings_Saved_Successfully']);
}

// ── Transaction hooks ────────────────────────────────────────────────────────

function PesaPal_create_transaction($trx, $user)
{
    $url = U . "plugin/initiatepesapal";

    $d = ORM::for_table('tbl_payment_gateway')
        ->where('username', $user['username'])
        ->where('status', 1)
        ->find_one();

    $d->gateway_trx_id = '';
    $d->payment_method = 'PesaPal';
    $d->pg_url_payment = $url;
    $d->pg_request     = '';
    $d->expired_date   = date('Y-m-d H:i:s', strtotime("+30 minutes"));
    $d->save();

    r2(U . "order/view/" . $d['id'], 's', Lang::T("Create Transaction Success, Please click pay now to process payment"));
    die();
}

function PesaPal_payment_notification()
{
    // PesaPal sends GET requests for both IPN and browser callback redirect
    $order_tracking_id  = isset($_GET['OrderTrackingId'])        ? trim($_GET['OrderTrackingId'])        : '';
    $merchant_reference = isset($_GET['OrderMerchantReference']) ? trim($_GET['OrderMerchantReference']) : '';
    $notification_type  = isset($_GET['OrderNotificationType'])  ? trim($_GET['OrderNotificationType'])  : '';

    file_put_contents('pesapal_callback.log',
        "\n--- " . date('Y-m-d H:i:s') . " ---\n" .
        "Type: $notification_type | Tracking: $order_tracking_id | Ref: $merchant_reference\n",
        FILE_APPEND
    );

    // ── Missing tracking ID ──────────────────────────────────────────────────
    if (empty($order_tracking_id)) {
        if ($notification_type === 'IPNCHANGE') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 500, 'message' => 'Missing OrderTrackingId']);
        } else {
            _pesapal_callback_page('error', 'Invalid callback — missing tracking ID. Please contact support.');
        }
        exit();
    }

    // ── Authenticate ─────────────────────────────────────────────────────────
    $token = _pesapal_get_token();
    if (!$token) {
        if ($notification_type === 'IPNCHANGE') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 500, 'message' => 'Auth failed']);
        } else {
            _pesapal_callback_page('error', 'PesaPal authentication failed. Please contact admin.');
        }
        exit();
    }

    // ── GetTransactionStatus (with 1 retry for race condition on CALLBACKURL) ─
    $txStatus    = _pesapal_get_transaction_status($order_tracking_id, $token);
    $status_code = ($txStatus && isset($txStatus->status_code)) ? (int)$txStatus->status_code : -1;

    // If browser redirect fires before PesaPal finalises, retry once after 2s
    if ($status_code !== 1 && $notification_type === 'CALLBACKURL') {
        sleep(2);
        $txStatus    = _pesapal_get_transaction_status($order_tracking_id, $token);
        $status_code = ($txStatus && isset($txStatus->status_code)) ? (int)$txStatus->status_code : -1;
    }

    file_put_contents('pesapal_callback.log',
        "Status response [$status_code]: " . json_encode($txStatus) . "\n",
        FILE_APPEND
    );

    // ── Find matching gateway record ─────────────────────────────────────────
    // Check both pending (status=1) and already-recorded (status=2) to avoid double-processing
    $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
        ->where('checkout', $order_tracking_id)
        ->order_by_desc('id')
        ->find_one();

    if (!$PaymentGatewayRecord && !empty($merchant_reference)) {
        $trx_id = str_replace('TRX-', '', $merchant_reference);
        if (is_numeric($trx_id)) {
            $PaymentGatewayRecord = ORM::for_table('tbl_payment_gateway')
                ->find_one((int) $trx_id);
        }
    }

    if (!$PaymentGatewayRecord) {
        error_log("PesaPal: No record found for tracking=$order_tracking_id ref=$merchant_reference");
        if ($notification_type === 'IPNCHANGE') {
            header('Content-Type: application/json');
            echo json_encode([
                'orderNotificationType'  => $notification_type,
                'orderTrackingId'        => $order_tracking_id,
                'orderMerchantReference' => $merchant_reference,
                'status'                 => 200,
            ]);
        } else {
            _pesapal_callback_page('pending', 'Payment received. Your internet access will be activated shortly. You can close this tab and return to the WiFi login page.');
        }
        exit();
    }

    // ── Process payment if still pending ────────────────────────────────────
    $already_recorded = ($PaymentGatewayRecord->status == 2);

    if (!$already_recorded && $status_code === 1) {
        // COMPLETED — grant internet access
        $confirmation_code = isset($txStatus->confirmation_code) && !empty($txStatus->confirmation_code)
            ? $txStatus->confirmation_code
            : $order_tracking_id;

        $PaymentGatewayRecord->gateway_trx_id   = $confirmation_code;
        $PaymentGatewayRecord->pg_paid_response  = 'COMPLETED — ' . $confirmation_code;
        $PaymentGatewayRecord->status            = 2;
        $PaymentGatewayRecord->paid_date         = date('Y-m-d H:i:s');
        $PaymentGatewayRecord->save();

        $username = $PaymentGatewayRecord->username;
        $router   = $PaymentGatewayRecord->routers;
        $plan_id  = $PaymentGatewayRecord->plan_id;

        $cust = ORM::for_table('tbl_customers')->where('username', $username)->find_one();
        if ($cust) {
            Package::rechargeUser($cust->id, $router, $plan_id, 'PesaPal', $confirmation_code);
            _log("PesaPal: Payment COMPLETED for $username — $confirmation_code", 'Payment', $cust->id);
        }

    } elseif (!$already_recorded && in_array($status_code, [2, 3])) {
        // FAILED or REVERSED
        $desc = isset($txStatus->payment_status_description) ? $txStatus->payment_status_description : 'Failed';
        $PaymentGatewayRecord->pg_paid_response = $desc;
        $PaymentGatewayRecord->status           = 4;
        $PaymentGatewayRecord->save();
    }

    // ── Respond ──────────────────────────────────────────────────────────────
    if ($notification_type === 'IPNCHANGE') {
        // IPN server-to-server call — MUST return exact JSON acknowledgement
        header('Content-Type: application/json');
        echo json_encode([
            'orderNotificationType'  => $notification_type,
            'orderTrackingId'        => $order_tracking_id,
            'orderMerchantReference' => $merchant_reference,
            'status'                 => 200,
        ]);
    } else {
        // CALLBACKURL — customer's browser redirect
        // Do NOT redirect to order/package (hotspot users don't have member accounts)
        // Show a standalone page they can close and return to the WiFi hotspot from
        if ($status_code === 1 || $already_recorded) {
            _pesapal_callback_page('success', 'Payment confirmed! Your internet access has been activated. You can close this tab and start browsing.');
        } elseif (in_array($status_code, [2, 3])) {
            $desc = isset($txStatus->payment_status_description) ? $txStatus->payment_status_description : 'Payment not completed';
            _pesapal_callback_page('error', $desc . '. Please go back and try again.');
        } else {
            _pesapal_callback_page('pending', 'Your payment is being confirmed. Please wait — your internet access will be activated automatically. You can close this tab.');
        }
    }

    exit();
}

/**
 * Output a simple standalone HTML response page for the customer browser redirect.
 * Does NOT use Smarty or the SpeedRadius layout — works for hotspot users
 * who don't have member portal accounts.
 */
function _pesapal_callback_page($type, $message)
{
    $icons   = ['success' => '✅', 'error' => '❌', 'pending' => '⏳'];
    $colors  = ['success' => '#27ae60', 'error' => '#e74c3c', 'pending' => '#f39c12'];
    $titles  = ['success' => 'Payment Successful', 'error' => 'Payment Failed', 'pending' => 'Processing Payment'];
    $icon    = isset($icons[$type])  ? $icons[$type]  : '⏳';
    $color   = isset($colors[$type]) ? $colors[$type] : '#3498db';
    $title   = isset($titles[$type]) ? $titles[$type] : 'Payment Status';

    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>' . htmlspecialchars($title) . '</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: "Segoe UI", sans-serif; background:#f0f4f8; display:flex; align-items:center; justify-content:center; min-height:100vh; }
  .card { background:#fff; border-radius:16px; padding:40px 32px; max-width:420px; width:90%; text-align:center; box-shadow:0 8px 30px rgba(0,0,0,0.12); }
  .icon { font-size:3.5em; margin-bottom:16px; }
  h1 { font-size:1.5em; color:' . $color . '; margin-bottom:12px; }
  p  { color:#555; line-height:1.6; margin-bottom:24px; }
  .btn { display:inline-block; background:' . $color . '; color:#fff; padding:12px 28px; border-radius:8px; text-decoration:none; font-weight:600; cursor:pointer; border:none; font-size:1em; }
  .note { font-size:0.82em; color:#999; margin-top:16px; }
</style>
</head>
<body>
<div class="card">
  <div class="icon">' . $icon . '</div>
  <h1>' . htmlspecialchars($title) . '</h1>
  <p>' . htmlspecialchars($message) . '</p>
  <button class="btn" onclick="window.close()">Close Tab</button>
  <p class="note">If this tab does not close, you can close it manually and return to the WiFi login page.</p>
</div>
<script>
  // Auto-close after 5 seconds if this was opened by window.open()
  setTimeout(function(){ try { window.close(); } catch(e){} }, 5000);
</script>
</body>
</html>';
}

function PesaPal_get_status($trx, $user)
{
    $order_tracking_id = isset($trx['checkout']) ? $trx['checkout'] : '';
    if (empty($order_tracking_id)) {
        return false;
    }

    $token = _pesapal_get_token();
    if (!$token) {
        return false;
    }

    $txStatus = _pesapal_get_transaction_status($order_tracking_id, $token);
    return ($txStatus && isset($txStatus->status_code) && (int)$txStatus->status_code === 1);
}
