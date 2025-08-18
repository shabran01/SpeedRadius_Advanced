<?php

/**
 * Get connected devices for a customer from Mikrotik router
 */
function get_customer_devices($router, $username) {
    $devices = [];
    
    try {
        if(empty($router) || empty($username)){
            return [];
        }

        $client = Mikrotik::getClient($router['ip_address'], $router['username'], $router['password']);
        if(!$client) {
            return [];
        }

        // Get Hotspot active users
        try {
            $printRequest = new PEAR2\Net\RouterOS\Request(
                '/ip hotspot active print'
            );
            $printRequest->setQuery(
                PEAR2\Net\RouterOS\Query::where('user', $username)
            );
            
            $responses = $client->sendSync($printRequest);
            
            foreach($responses as $response){
                if($response->getType() === PEAR2\Net\RouterOS\Response::TYPE_DATA){
                    $mac = $response->getProperty('mac-address');
                    $ip = $response->getProperty('address');
                    if($mac && $ip) {
                        $devices[] = [
                            'type' => 'Hotspot',
                            'mac_address' => $mac,
                            'ip_address' => $ip,
                            'uptime' => $response->getProperty('uptime', 'N/A')
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            _log('Hotspot device fetch error: ' . $e->getMessage());
        }

        // Get PPPoE active connections
        try {
            $printRequest = new PEAR2\Net\RouterOS\Request(
                '/ppp active print'
            );
            $printRequest->setQuery(
                PEAR2\Net\RouterOS\Query::where('name', $username)
            );
            
            $responses = $client->sendSync($printRequest);
            
            foreach($responses as $response){
                if($response->getType() === PEAR2\Net\RouterOS\Response::TYPE_DATA){
                    $mac = $response->getProperty('caller-id');
                    $ip = $response->getProperty('address');
                    if($mac && $ip) {
                        $devices[] = [
                            'type' => 'PPPoE',
                            'mac_address' => $mac,
                            'ip_address' => $ip,
                            'uptime' => $response->getProperty('uptime', 'N/A')
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            _log('PPPoE device fetch error: ' . $e->getMessage());
        }
        
    } catch (Exception $e) {
        _log('Device info error: ' . $e->getMessage());
        return [];
    }
    
    return $devices;
}
