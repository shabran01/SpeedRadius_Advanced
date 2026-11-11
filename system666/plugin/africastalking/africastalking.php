<?php
namespace HotspotBilling\Plugin\AfricaTalking;

use AfricasTalking\SDK\AfricasTalking;

class AfricaTalkingSMS
{
    private $username;
    private $apiKey;
    private $senderId;

    public function __construct()
    {
        $this->username = 'sandbox'; // Default to sandbox
        $this->apiKey = 'atsk_43b21d8d71f4b496e293b1407a5ff740f6406962578297e0bb3a979e98cc4ecea517b59f';
        $this->senderId = 'ACCESSNET';
    }

    public function sendSMS($recipient, $message)
    {
        try {
            $AT = new AfricasTalking($this->username, $this->apiKey);
            $sms = $AT->sms();

            $result = $sms->send([
                'to' => $recipient,
                'message' => $message,
                'from' => $this->senderId
            ]);

            return [
                'status' => 'success',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getBalance()
    {
        try {
            $AT = new AfricasTalking($this->username, $this->apiKey);
            $application = $AT->application();
            $data = $application->fetchApplicationData();
            return $data['UserData']['balance'];
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}

// Register plugin
return [
    'name' => 'AfricaTalking SMS',
    'description' => 'Send SMS via Africa\'s Talking API',
    'version' => '1.0',
    'author' => 'HotspotBilling',
    'routes' => [
        'sms/send' => ['AfricaTalkingSMS', 'sendSMS'],
        'sms/balance' => ['AfricaTalkingSMS', 'getBalance']
    ]
];
