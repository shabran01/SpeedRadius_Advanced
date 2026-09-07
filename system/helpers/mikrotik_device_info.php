<?php

/**
 * Get connected devices for a customer from Mikrotik router
 */
function get_customer_devices($router, $username, $pppoe_username = null) {
    $devices = [];
    // Fall back to the regular username if no separate PPPoE username is provided
    if (empty($pppoe_username)) {
        $pppoe_username = $username;
    }
    
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
                    $hostname = $response->getProperty('host-name');
                    
                    // Log all available properties for debugging
                    $properties = [];
                    foreach($response->getIterator() as $key => $value) {
                        $properties[$key] = $value;
                    }
                    _log('All Hotspot device properties: ' . json_encode($properties));
                    
                    // Try multiple possible property names for bandwidth data
                    $bytes_in = $response->getProperty('bytes-in') ?: 
                               $response->getProperty('bytes_in') ?: 
                               $response->getProperty('rx-bytes') ?: 
                               $response->getProperty('rx-bytes') ?: 
                               $response->getProperty('download') ?: 0;
                    
                    $bytes_out = $response->getProperty('bytes-out') ?: 
                                $response->getProperty('bytes_out') ?: 
                                $response->getProperty('tx-bytes') ?: 
                                $response->getProperty('tx-bytes') ?: 
                                $response->getProperty('upload') ?: 0;
                    
                    // Log the data for debugging
                    _log('Hotspot device - MAC: ' . $mac . ', IP: ' . $ip . ', Bytes In: ' . $bytes_in . ', Bytes Out: ' . $bytes_out);
                    
                    // If hostname not found in active connections, try to get from DHCP leases
                    if (empty($hostname)) {
                        $hostname = getHostnameFromDhcpOrArp($client, $ip, $mac);
                    }
                    
                    // If basic bandwidth data is zero, try detailed stats
                    if ($bytes_in == 0 && $bytes_out == 0) {
                        $detailedStats = getDetailedBandwidthStats($client, $ip, $mac);
                        if ($detailedStats['bytes_in'] > 0 || $detailedStats['bytes_out'] > 0) {
                            $bytes_in = $detailedStats['bytes_in'];
                            $bytes_out = $detailedStats['bytes_out'];
                            _log('Using detailed stats for Hotspot device - MAC: ' . $mac . ', Bytes In: ' . $bytes_in . ', Bytes Out: ' . $bytes_out);
                        }
                    }
                    
                    if($mac && $ip) {
                        $devices[] = [
                            'type' => 'Hotspot',
                            'mac_address' => $mac,
                            'ip_address' => $ip,
                            'hostname' => $hostname ?: 'N/A',
                            'uptime' => $response->getProperty('uptime', 'N/A'),
                            'bytes_in' => $bytes_in,
                            'bytes_out' => $bytes_out,
                            'packets_in' => $response->getProperty('packets-in', 0),
                            'packets_out' => $response->getProperty('packets-out', 0)
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            _log('Hotspot device fetch error: ' . $e->getMessage());
        }

        // Get PPPoE active connections — same approach as pppoe_monitor plugin:
        // load all interface stats once, then match by "<pppoe-username>"
        try {
            $ifaceMap = [];
            $ifaceReq = new PEAR2\Net\RouterOS\Request('/interface/print');
            $ifaceResponses = $client->sendSync($ifaceReq);
            foreach ($ifaceResponses as $ir) {
                if ($ir->getType() !== PEAR2\Net\RouterOS\Response::TYPE_DATA) continue;
                $ifName = (string)$ir->getProperty('name');
                if (empty($ifName)) continue;
                $ifaceMap[$ifName] = [
                    'rx' => (int)($ir->getProperty('rx-byte') ?: 0),
                    'tx' => (int)($ir->getProperty('tx-byte') ?: 0),
                ];
            }

            $printRequest = new PEAR2\Net\RouterOS\Request('/ppp/active/print');
            $printRequest->setQuery(
                PEAR2\Net\RouterOS\Query::where('name', $pppoe_username)
            );

            $responses = $client->sendSync($printRequest);

            foreach($responses as $response){
                if($response->getType() !== PEAR2\Net\RouterOS\Response::TYPE_DATA) continue;

                $mac      = $response->getProperty('caller-id');
                $ip       = $response->getProperty('address');
                $hostname = $response->getProperty('host-name');

                $ifaceName = "<pppoe-{$pppoe_username}>";
                $bytes_in  = isset($ifaceMap[$ifaceName]) ? $ifaceMap[$ifaceName]['rx'] : 0;
                $bytes_out = isset($ifaceMap[$ifaceName]) ? $ifaceMap[$ifaceName]['tx'] : 0;

                _log('PPPoE device - MAC: ' . $mac . ', IP: ' . $ip . ', Bytes In: ' . $bytes_in . ', Bytes Out: ' . $bytes_out);

                if (empty($hostname)) {
                    $hostname = getHostnameFromDhcpOrArp($client, $ip, $mac);
                }

                if($mac && $ip) {
                    $devices[] = [
                        'type'        => 'PPPoE',
                        'mac_address' => $mac,
                        'ip_address'  => $ip,
                        'hostname'    => $hostname ?: 'N/A',
                        'uptime'      => $response->getProperty('uptime', 'N/A'),
                        'bytes_in'    => $bytes_in,
                        'bytes_out'   => $bytes_out,
                        'packets_in'  => $response->getProperty('packets-in', 0),
                        'packets_out' => $response->getProperty('packets-out', 0)
                    ];
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

/**
 * Get hostname from DHCP leases or ARP table
 */
function getHostnameFromDhcpOrArp($client, $ip, $mac = null) {
    try {
        // First try DHCP leases
        $dhcpRequest = new PEAR2\Net\RouterOS\Request('/ip dhcp-server lease print');
        if ($ip) {
            $dhcpRequest->setQuery(PEAR2\Net\RouterOS\Query::where('address', $ip));
        } elseif ($mac) {
            $dhcpRequest->setQuery(PEAR2\Net\RouterOS\Query::where('mac-address', $mac));
        }
        
        $dhcpResponses = $client->sendSync($dhcpRequest);
        foreach($dhcpResponses as $response){
            if($response->getType() === PEAR2\Net\RouterOS\Response::TYPE_DATA){
                $hostname = $response->getProperty('host-name');
                if (!empty($hostname)) {
                    return $hostname;
                }
            }
        }
        
        // If not found in DHCP, try ARP table
        if ($ip) {
            $arpRequest = new PEAR2\Net\RouterOS\Request('/ip arp print');
            $arpRequest->setQuery(PEAR2\Net\RouterOS\Query::where('address', $ip));
            
            $arpResponses = $client->sendSync($arpRequest);
            foreach($arpResponses as $response){
                if($response->getType() === PEAR2\Net\RouterOS\Response::TYPE_DATA){
                    $hostname = $response->getProperty('host-name');
                    if (!empty($hostname)) {
                        return $hostname;
                    }
                    // Try comment field as fallback
                    $comment = $response->getProperty('comment');
                    if (!empty($comment)) {
                        return $comment;
                    }
                }
            }
        }
        
    } catch (Exception $e) {
        _log('Hostname fetch error: ' . $e->getMessage());
    }
    
    return 'N/A';
}

/**
 * Format bytes into human readable format
 */
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Calculate total data usage (in + out)
 */
function getTotalDataUsage($bytes_in, $bytes_out) {
    return $bytes_in + $bytes_out;
}

/**
 * Get detailed bandwidth statistics from RouterOS accounting
 */
function getDetailedBandwidthStats($client, $ip, $mac = null) {
    $stats = [
        'bytes_in' => 0,
        'bytes_out' => 0,
        'packets_in' => 0,
        'packets_out' => 0
    ];
    
    try {
        _log('Getting detailed bandwidth stats for IP: ' . $ip . ', MAC: ' . $mac);
        
        // Try to get from accounting table (more accurate)
        $accountRequest = new PEAR2\Net\RouterOS\Request('/ip accounting print');
        $accountResponses = $client->sendSync($accountRequest);
        _log('Accounting responses count: ' . count($accountResponses));
        
        foreach($accountResponses as $response){
            if($response->getType() === PEAR2\Net\RouterOS\Response::TYPE_DATA){
                $srcAddress = $response->getProperty('src-address');
                $dstAddress = $response->getProperty('dst-address');
                $bytes = $response->getProperty('bytes', 0);
                
                _log('Accounting entry - Src: ' . $srcAddress . ', Dst: ' . $dstAddress . ', Bytes: ' . $bytes);
                
                // Check if this entry matches our device IP
                if ($srcAddress === $ip || $dstAddress === $ip) {
                    $packets = $response->getProperty('packets', 0);
                    
                    if ($srcAddress === $ip) {
                        // Outgoing traffic
                        $stats['bytes_out'] += $bytes;
                        $stats['packets_out'] += $packets;
                    } else {
                        // Incoming traffic
                        $stats['bytes_in'] += $bytes;
                        $stats['packets_in'] += $packets;
                    }
                }
            }
        }
        
        _log('Stats after accounting: ' . json_encode($stats));
        
        // If accounting is empty, try queue tree
        if ($stats['bytes_in'] == 0 && $stats['bytes_out'] == 0) {
            _log('Accounting stats are zero, trying queue tree...');
            $queueRequest = new PEAR2\Net\RouterOS\Request('/queue tree print');
            $queueResponses = $client->sendSync($queueRequest);
            _log('Queue tree responses count: ' . count($queueResponses));
            
            foreach($queueResponses as $response){
                if($response->getType() === PEAR2\Net\RouterOS\Response::TYPE_DATA){
                    $name = $response->getProperty('name');
                    $bytes = $response->getProperty('bytes', 0);
                    _log('Queue tree entry - Name: ' . $name . ', Bytes: ' . $bytes);
                    
                    if (strpos($name, $ip) !== false || ($mac && strpos($name, str_replace(':', '', $mac)) !== false)) {
                        $stats['bytes_in'] = $bytes / 2; // Rough estimate
                        $stats['bytes_out'] = $bytes / 2; // Rough estimate
                        _log('Queue tree matched! Estimated stats: ' . json_encode($stats));
                    }
                }
            }
        }
        
        // Try simple interface stats as last resort
        if ($stats['bytes_in'] == 0 && $stats['bytes_out'] == 0) {
            _log('Trying interface stats...');
            $interfaceRequest = new PEAR2\Net\RouterOS\Request('/interface print stats');
            $interfaceResponses = $client->sendSync($interfaceRequest);
            
            foreach($interfaceResponses as $response){
                if($response->getType() === PEAR2\Net\RouterOS\Response::TYPE_DATA){
                    $name = $response->getProperty('name');
                    $rxByte = $response->getProperty('rx-byte', 0);
                    $txByte = $response->getProperty('tx-byte', 0);
                    
                    _log('Interface stats - Name: ' . $name . ', RX: ' . $rxByte . ', TX: ' . $txByte);
                    
                    // Look for interface that might contain this IP
                    if (strpos($name, 'pppoe') !== false || strpos($name, 'hotspot') !== false) {
                        $stats['bytes_in'] = $rxByte;
                        $stats['bytes_out'] = $txByte;
                        _log('Interface matched! Stats: ' . json_encode($stats));
                    }
                }
            }
        }
        
    } catch (Exception $e) {
        _log('Detailed bandwidth stats error: ' . $e->getMessage());
    }
    
    _log('Final detailed stats: ' . json_encode($stats));
    return $stats;
}
