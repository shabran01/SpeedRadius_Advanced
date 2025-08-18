<?php

/**
 *  SMS Balance Widget
 **/

header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Origin: *');

// Define the API endpoint and token
$api_url = 'https://portal.bytewavenetworks.com/api/http/balance?api_token=108|yS1EznTgtmhRM5prqTyXTvashAddg6zP509JhC2U6262587d';

// Initialize cURL session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));

// Execute the cURL request
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
    $response_data = ['status' => 'error', 'message' => $error_msg];
    curl_close($ch);
    echo json_encode($response_data);
    exit();
}

// Close cURL session
curl_close($ch);

// Decode the JSON response
$response_data = json_decode($response, true);

// HTML content
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Balance Widget</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #6e45e2, #88d3ce);
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
        }
        nav {
            width: 100%;
            background-color: #ffffff;
            padding: 10px 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        nav a {
            color: #6e45e2;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.2rem;
            padding: 10px 20px;
            border-radius: 8px;
            background: linear-gradient(145deg, #ffffff, #e6e6e6);
            box-shadow: 6px 6px 12px #d1d1d1, -6px -6px 12px #ffffff;
            transition: all 0.3s ease;
        }
        nav a:hover {
            color: #fff;
            background: linear-gradient(145deg, #88d3ce, #6e45e2);
            box-shadow: 6px 6px 12px #545454, -6px -6px 12px #b4b4b4;
        }
        h1 {
            font-size: 2.5rem;
            color: #fff;
            margin-bottom: 20px;
            text-shadow: 2px 2px 6px rgba(0,0,0,0.3);
        }
        .small-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin: 20px;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 12px 24px rgba(0,0,0,0.2);
            border: 2px solid #6e45e2;
            color: #333;
            width: 100%;
            max-width: 350px;
            text-align: center;
        }
        .bg-purple {
            background-color: #6e45e2; /* Cool purple */
            color: #fff; /* White text */
        }
        .small-box .inner {
            padding: 10px;
        }
        .small-box .inner h4 {
            font-size: 3rem;
            margin: 0;
            color: #fff; /* White text */
            text-shadow: 1px 1px 4px rgba(0,0,0,0.3);
        }
        .small-box .inner p {
            margin: 5px 0;
            color: #fff; /* White text */
            font-weight: bold;
        }
        .icon {
            font-size: 3rem;
            color: #fff; /* White text */
        }
        @media (max-width: 600px) {
            h1 {
                font-size: 2rem;
            }
            .small-box .inner h4 {
                font-size: 2rem;
            }
            .icon {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <nav>
        <a href="https://isp.cloudpoa.co.ke/index.php?_route=dashboard">Dashboard</a>
        <a href="https://isp.cloudpoa.co.ke/index.php?_route=plugin/mpesa_transactions">Mpesa Transactions</a>
        <a href="https://isp.cloudpoa.co.ke/index.php?_route=plugin/system_info">System Information</a>
    </nav>
    <div>
        <h1>SMS Balance</h1>
        <div class="small-box bg-purple">
            <div class="inner">
                <h4 id="sms-balance">
                    <?php
                    if ($response_data['status'] === 'success') {
                        // Extract the remaining balance
                        $remaining_balance = htmlspecialchars($response_data['data']['remaining_balance']);
                        echo $remaining_balance . '/=';
                    } else {
                        // Display error message
                        echo 'Error';
                    }
                    ?>
                </h4>
                <p>SMS Balance</p>
            </div>
            <div class="icon">
                <i class="fas fa-credit-card"></i>
            </div>
        </div>
    </div>
</body>
</html>
