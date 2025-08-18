<?php
use Selcom\ApigwClient\Client;

$Vendor = 'TILL61056399';
$APIKey  = 'TILL61056399-ed669eb84bee8a8e';
$APISecret = '621a499113380bc0a1ce580ce4acb936878826';
$BaseURL = 'https://apigw.selcommobile.com/v1';

$client = new Client($BaseURL, $APIKey, $APISecret);
$utilityPaymentArray = array(
    "transid" => "1218d5Qb",
    "utilitycode" => "LUKU",
    "utilityref" => "654944949",
    "amount" => 8000,
    "vendor" => "66546846845",
    "pin" => "48585",
    "msisdn" => "255055555555"
);
$utilityPaymentPath = "/v1/utilitypayment/process";
$response = $client->postFunc($utilityPaymentPath, $utilityPaymentArray);
print_r($response);



echo "Selecom Pay";
exit;
