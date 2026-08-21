<?php

/**
 * PesaPal Order Initiator
 *
 * Called by:
 *   - Captive portal: SendSTKcred() in CreateHotspotUser.php (POST with username/phone/channel=Yes)
 *     → returns JSON {status:'success', redirect_url:'https://pay.pesapal.com/...'}
 *   - Member portal: "Pay Now" button on order/view (no channel param)
 *     → redirects browser directly to PesaPal payment URL
 *
 * Flow:
 *   1. Authenticate with PesaPal → get Bearer token
 *   2. Register IPN URL if ipn_id not yet stored
 *   3. Submit order → get PesaPal redirect_url + order_tracking_id
 *   4. Save order_tracking_id to tbl_payment_gateway.checkout
 *   5. Return/redirect appropriately
 **/

function initiatepesapal()
{
    // ── Find pending DB record ───────────────────────────────────────────────
    $posted_username = isset($_POST['username']) ? trim($_POST['username']) : null;
    $pgQuery = ORM::for_table('tbl_payment_gateway')->where('status', 1);
    if (!empty($posted_username)) {
        $pgQuery = $pgQuery->where('username', $posted_username);
    }
    $record = $pgQuery->order_by_desc('id')->find_one();

    if (!$record) {
        echo json_encode(['status' => 'error', 'message' => 'No active payment found. Please try ordering again.']);
        die();
    }

    $username = $record->username;

    // ── Get phone ────────────────────────────────────────────────────────────
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    if (empty($phone)) {
        $cust  = ORM::for_table('tbl_customers')->where('username', $username)->find_one();
        $phone = $cust ? $cust->phonenumber : '';
    }

    // Normalise to 254XXXXXXXXX
    $phone = (substr($phone, 0, 1) == '+') ? str_replace('+', '', $phone)         : $phone;
    $phone = (substr($phone, 0, 1) == '0') ? preg_replace('/^0/', '254', $phone)  : $phone;
    $phone = (substr($phone, 0, 1) == '7') ? preg_replace('/^7/', '2547', $phone) : $phone;
    $phone = (substr($phone, 0, 1) == '1') ? preg_replace('/^1/', '2541', $phone) : $phone;

    // ── Load PesaPal settings ────────────────────────────────────────────────
    $key_row     = ORM::for_table('tbl_appconfig')->where('setting', 'pesapal_consumer_key')->find_one();
    $secret_row  = ORM::for_table('tbl_appconfig')->where('setting', 'pesapal_consumer_secret')->find_one();
    $ipn_id_row  = ORM::for_table('tbl_appconfig')->where('setting', 'pesapal_ipn_id')->find_one();
    $env_row     = ORM::for_table('tbl_appconfig')->where('setting', 'pesapal_environment')->find_one();

    $env      = ($env_row && $env_row->value === 'sandbox') ? 'sandbox' : 'live';
    $base_url = ($env === 'sandbox') ? 'https://cybqa.pesapal.com/pesapalv3' : 'https://pay.pesapal.com/v3';
    $ipn_id   = ($ipn_id_row && !empty($ipn_id_row->value)) ? $ipn_id_row->value : null;

    if (!$key_row || empty($key_row->value) || !$secret_row || empty($secret_row->value)) {
        echo json_encode(['status' => 'error', 'message' => 'PesaPal is not configured. Please contact admin.']);
        die();
    }

    $auth_headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];

    // ── Step 1: Authenticate ─────────────────────────────────────────────────
    $auth_payload = json_encode([
        'consumer_key'    => $key_row->value,
        'consumer_secret' => $secret_row->value,
    ]);

    $ch = curl_init($base_url . '/api/Auth/RequestToken');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $auth_payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $auth_headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $auth_raw  = curl_exec($ch);
    curl_close($ch);

    $auth_resp = json_decode($auth_raw);

    if (!$auth_resp || !isset($auth_resp->token) || $auth_resp->status != '200') {
        $err = isset($auth_resp->error->message) ? $auth_resp->error->message : 'unknown';
        error_log("PesaPal initiate: Auth failed — $auth_raw");
        echo json_encode(['status' => 'error', 'message' => 'PesaPal authentication failed: ' . $err . '. Please try again.']);
        die();
    }

    $token = $auth_resp->token;

    $bearer_headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token,
    ];

    // ── Step 2: Register IPN if not yet stored ───────────────────────────────
    if (empty($ipn_id)) {
        $ipn_payload = json_encode([
            'url'                   => U . 'callback/PesaPal',
            'ipn_notification_type' => 'GET',
        ]);

        $ch = curl_init($base_url . '/api/URLSetup/RegisterIPN');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $ipn_payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $bearer_headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $ipn_raw  = curl_exec($ch);
        curl_close($ch);

        $ipn_resp = json_decode($ipn_raw);

        if (!$ipn_resp || !isset($ipn_resp->ipn_id) || $ipn_resp->status != '200') {
            error_log("PesaPal initiate: IPN registration failed — $ipn_raw");
            echo json_encode(['status' => 'error', 'message' => 'Could not register PesaPal IPN. Please contact admin.']);
            die();
        }

        $ipn_id = $ipn_resp->ipn_id;

        // Persist IPN ID so it is reused for all future orders
        $d = ORM::for_table('tbl_appconfig')->where('setting', 'pesapal_ipn_id')->find_one();
        if ($d) {
            $d->value = $ipn_id;
            $d->save();
        } else {
            $d          = ORM::for_table('tbl_appconfig')->create();
            $d->setting = 'pesapal_ipn_id';
            $d->value   = $ipn_id;
            $d->save();
        }
    }

    // ── Step 3: Submit Order ─────────────────────────────────────────────────
    $amount        = (float) $record->price;
    $merchant_ref  = 'TRX-' . $record->id;
    $callback_url  = U . 'callback/PesaPal';
    $description   = substr('SpeedRadius: ' . $record->plan_name, 0, 100);

    $order_payload = json_encode([
        'id'              => $merchant_ref,
        'currency'        => 'KES',
        'amount'          => $amount,
        'description'     => $description,
        'callback_url'    => $callback_url,
        'notification_id' => $ipn_id,
        'billing_address' => [
            'phone_number' => $phone,
            'first_name'   => $username,
            'country_code' => 'KE',
        ],
    ]);

    $ch = curl_init($base_url . '/api/Transactions/SubmitOrderRequest');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $order_payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $bearer_headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $order_raw   = curl_exec($ch);
    $http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    file_put_contents('pesapal_stk.log',
        "\n--- " . date('Y-m-d H:i:s') . " ---\nRequest: $order_payload\nResponse [$http_code]: $order_raw\n",
        FILE_APPEND
    );

    $order_resp = json_decode($order_raw);

    if (!$order_resp || $order_resp->status != '200' || empty($order_resp->redirect_url)) {
        $err = isset($order_resp->error->message) ? $order_resp->error->message : 'Order creation failed';
        error_log("PesaPal initiate: Order submit failed [$http_code] — $order_raw");
        echo json_encode(['status' => 'error', 'message' => 'Could not create PesaPal order: ' . $err]);
        die();
    }

    // ── Save tracking ID ─────────────────────────────────────────────────────
    $record->checkout         = $order_resp->order_tracking_id;
    $record->gateway_trx_id   = '';
    $record->payment_method   = 'PesaPal';
    $record->payment_channel  = 'PesaPal';
    $record->pg_paid_response = 'Order created — awaiting payment';
    $record->save();

    // ── Respond ──────────────────────────────────────────────────────────────
    if (!empty($_POST['channel'])) {
        // Captive portal (called via SendSTKcred) — return JSON with redirect_url
        // download.php JS will open this URL in a new tab and start polling
        echo json_encode([
            'status'       => 'success',
            'redirect_url' => $order_resp->redirect_url,
            'message'      => 'Tap the button to open the PesaPal payment page and complete your payment.',
        ]);
    } else {
        // Member portal — redirect browser directly to PesaPal
        header('Location: ' . $order_resp->redirect_url);
        exit();
    }
}
