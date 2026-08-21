<?php
/**
 * Test script for Mobitech SMS Gateway Balance Check
 */

// Include necessary files (adjust paths as needed)
require_once(__DIR__ . '/system/boot.php');

// Test balance check function
function testMobitechBalance() {
    // Test API key - replace with your actual Mobitech API key
    $api_key = '472e60b925fd12b81a50578626af1ba7c1f1b9eb2edd3fae';
    
    // Mobitech balance check endpoint
    $url = 'https://app.mobitechtechnologies.com/sms/getbalance';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'h_api_key: ' . $api_key,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    echo "=== Mobitech SMS Balance Check Test ===\n";
    echo "URL: " . $url . "\n";
    echo "HTTP Code: " . $httpCode . "\n";
    
    if ($error) {
        echo "CURL Error: " . $error . "\n";
        return false;
    }

    echo "Raw Response: " . $response . "\n\n";
    
    $result = json_decode($response, true);
    
    if ($httpCode == 200) {
        echo "✅ Balance check successful!\n";
        echo "Response Data: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
        
        if (isset($result['credit_balance'])) {
            echo "Current Balance: " . $result['credit_balance'] . "\n";
        }
        return true;
    } else {
        echo "❌ Balance check failed!\n";
        echo "Error Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
        return false;
    }
}

// Test mobile validation function
function testMobileValidation() {
    echo "\n=== Mobile Number Validation Test ===\n";
    
    $api_key = '472e60b925fd12b81a50578626af1ba7c1f1b9eb2edd3fae';
    $test_mobile = '0712345678'; // Test with a Kenyan number
    
    $url = 'https://app.mobitechtechnologies.com/sms/mobile?mobile=' . urlencode($test_mobile) . '&return=json';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'h_api_key: ' . $api_key,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    echo "Testing Mobile: " . $test_mobile . "\n";
    echo "HTTP Code: " . $httpCode . "\n";
    
    if ($error) {
        echo "CURL Error: " . $error . "\n";
        return false;
    }

    echo "Validation Response: " . $response . "\n";
    
    $result = json_decode($response, true);
    
    if ($httpCode == 200) {
        echo "✅ Mobile validation successful!\n";
        return true;
    } else {
        echo "❌ Mobile validation failed!\n";
        return false;
    }
}

// Run tests
echo "Testing Mobitech SMS Gateway API...\n\n";

testMobitechBalance();
testMobileValidation();

echo "\n=== Test Complete ===\n";
echo "Note: Replace the API key with your actual Mobitech API key for real testing.\n";
?>
