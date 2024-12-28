<?php
register_menu("SMS Balance", true, "sms_balance", 'AFTER_REPORTS', 'ion ion-ios-list', '', '', ['Admin', 'SuperAdmin']);

function sms_balance()
{
    global $ui, $config, $admin;

    _admin();

    // Check if the form was submitted to update the API key or token
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['api_key'])) {
            // Sanitize and save the new API key
            $new_api_key = htmlspecialchars(trim($_POST['api_key']));
            file_put_contents('api_key.txt', $new_api_key);
            $ui->assign('message', 'API key updated successfully.');
        }

        if (isset($_POST['api_token'])) {
            // Sanitize and save the new API token
            $new_api_token = htmlspecialchars(trim($_POST['api_token']));
            file_put_contents('api_token.txt', $new_api_token);
            $ui->assign('message', 'API token updated successfully.');
        }
    }

    // Load the current API key and token
    $api_key = file_get_contents('api_key.txt');
    $api_token = file_get_contents('api_token.txt');

    // Fetch credit balance
    $credit_balance = null;
    $credit_error_message = null;

    if (!empty($api_key)) {
        $url = "https://blessedtexts.com/api/sms/v1/credit-balance";

        // Initialize cURL session for credit balance
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Accept: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['api_key' => $api_key]));

        // Execute cURL request
        $response = curl_exec($ch);

        // Check for errors during the cURL request
        if (curl_errno($ch)) {
            $credit_error_message = 'Error: ' . curl_error($ch);
        } else {
            $result = json_decode($response, true);
            if ($result && $result['status_code'] === '1000') {
                $credit_balance = $result['balance'];
            } else {
                $credit_error_message = isset($result['message']) ? $result['message'] : 'Unknown error occurred.';
            }
        }

        // Close the cURL session
        curl_close($ch);
    } else {
        $credit_error_message = 'API key is missing. Please provide a valid API key.';
    }

    // Fetch SMS balance
    $sms_balance = null;
    $sms_error_message = null;

    if (!empty($api_token)) {
        $url = "https://portal.bytewavenetworks.com/api/v3/balance";

        // Initialize cURL session for SMS balance
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $api_token",
            "Content-Type: application/json",
            "Accept: application/json"
        ]);

        // Execute cURL request
        $response = curl_exec($ch);

        // Check for errors during the cURL request
        if (curl_errno($ch)) {
            $sms_error_message = 'Error: ' . curl_error($ch);
        } else {
            $result = json_decode($response, true);
            if ($result && $result['status'] === 'success') {
                $sms_balance = $result['data']['remaining_balance'];
            } else {
                $sms_error_message = isset($result['message']) ? $result['message'] : 'Unknown error occurred.';
            }
        }

        // Close the cURL session
        curl_close($ch);
    } else {
        $sms_error_message = 'API token is missing. Please provide a valid API token.';
    }

    // Assign data to UI
    $ui->assign('credit_balance', $credit_balance);
    $ui->assign('credit_error_message', $credit_error_message);
    $ui->assign('sms_balance', $sms_balance);
    $ui->assign('sms_error_message', $sms_error_message);
    $ui->assign('current_api_key', $api_key);
    $ui->assign('current_api_token', $api_token);

    // Assign admin info
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);

    // Display the template
    $ui->assign('_title', 'SMS Balance');
    $ui->display('sms_balance.tpl');
}
?>
