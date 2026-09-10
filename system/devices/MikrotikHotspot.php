<?php

/**
 *  PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 *  by https://t.me/ibnux
 *
 * This is Core, don't modification except you want to contribute
 * better create new plugin
 **/

use PEAR2\Net\RouterOS;

class MikrotikHotspot
{

    // show Description
    function description()
    {
        return [
            'title' => 'Mikrotik Hotspot',
            'description' => 'To handle connection between PHPNuxBill with Mikrotik Hotspot',
            'author' => 'ibnux',
            'url' => [
                'Github' => 'https://github.com/hotspotbilling/phpnuxbill/',
                'Telegram' => 'https://t.me/phpnuxbill',
                'Donate' => 'https://paypal.me/ibnux'
            ]
        ];
    }


    function add_customer($customer, $plan)
    {
        try {
            $mikrotik = $this->info($plan['routers']);
            $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            
            if ($client === null) {
                _log("Skipping customer {$customer['username']} - Router {$plan['routers']} not available");
                return false;
            }
            
            $isExp = ORM::for_table('tbl_plans')->select("id")->where('plan_expired', $plan['id'])->find_one();
            $this->removeHotspotUser($client, $customer['username']);
            if ($isExp){
                $this->removeHotspotActiveUser($client, $customer['username']);
            }
            $this->addHotspotUser($client, $plan, $customer);
            
            return true;
        } catch (\Exception $e) {
            _log("Error adding customer {$customer['username']} to router {$plan['routers']}: " . $e->getMessage());
            return false;
        }
    }
	
	function sync_customer($customer, $plan)
	{
		try {
			$mikrotik = $this->info($plan['routers']);
			$client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
			
			if ($client === null) {
				_log("Skipping sync for customer {$customer['username']} - Router {$plan['routers']} not available");
				return false;
			}
			
			$t = ORM::for_table('tbl_user_recharges')->where('username', $customer['username'])->where('status', 'on')->find_one();
			if ($t) {
				$printRequest = new RouterOS\Request('/ip/hotspot/user/print');
				$printRequest->setArgument('.proplist', '.id,limit-uptime,limit-bytes-total');
				$printRequest->setQuery(RouterOS\Query::where('name', $customer['username']));
				$userInfo = $client->sendSync($printRequest);
				$id = $userInfo->getProperty('.id');
				$uptime = $userInfo->getProperty('limit-uptime');
				$data = $userInfo->getProperty('limit-bytes-total');
				if (!empty($id) && (!empty($uptime) || !empty($data))) {
					$setRequest = new RouterOS\Request('/ip/hotspot/user/set');
					$setRequest->setArgument('numbers', $id);
					$setRequest->setArgument('profile', $t['namebp']);
					$client->sendSync($setRequest);
				} else {
					$this->add_customer($customer, $plan);
				}
			}
			
			return true;
		} catch (\Exception $e) {
			_log("Error syncing customer {$customer['username']} to router {$plan['routers']}: " . $e->getMessage());
			return false;
		}
	}


    function remove_customer($customer, $plan)
    {
        try {
            $mikrotik = $this->info($plan['routers']);
            $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            
            if ($client === null) {
                _log("Skipping removal for customer {$customer['username']} - Router {$plan['routers']} not available");
                return false;
            }
            
            if (!empty($plan['plan_expired'])) {
                $p = ORM::for_table("tbl_plans")->find_one($plan['plan_expired']);
                if($p){
                    $this->add_customer($customer, $p);
                    $this->removeHotspotActiveUser($client, $customer['username']);
                    return true;
                }
            }
            $this->removeHotspotUser($client, $customer['username']);
            $this->removeHotspotActiveUser($client, $customer['username']);
            
            return true;
        } catch (\Exception $e) {
            _log("Error removing customer {$customer['username']} from router {$plan['routers']}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Permanently delete a customer from the MikroTik router.
     *
     * Unlike remove_customer() (which moves the user to an expiry plan), this
     * removes the /ip/hotspot/user entry and disconnects any active session.
     * Used when an admin deletes a customer.
     */
    function delete_customer($customer, $plan)
    {
        try {
            $mikrotik = $this->info($plan['routers']);
            if (!$mikrotik) {
                _log("Router not found for plan: " . $plan['name_plan']);
                return false;
            }

            $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            if ($client === null) {
                _log("Skipping removal for customer {$customer['username']} - Router {$plan['routers']} not available");
                return false;
            }

            // removeHotspotUser() disconnects active sessions before removing the user entry
            $this->removeHotspotUser($client, $customer['username']);

            _log("Deleted Hotspot user {$customer['username']} from router {$plan['routers']}");
            return true;
        } catch (\Exception $e) {
            _log("Error deleting customer {$customer['username']} from router {$plan['routers']}: " . $e->getMessage());
            return false;
        }
    }

    // customer change username
    public function change_username($plan, $from, $to)
    {
        $mikrotik = $this->info($plan['routers']);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        //check if customer exists
        $printRequest = new RouterOS\Request('/ip/hotspot/user/print');
        $printRequest->setArgument('.proplist', '.id');
        $printRequest->setQuery(RouterOS\Query::where('name', $from));
        $id = $client->sendSync($printRequest)->getProperty('.id');

        if (!empty($cid)) {
            $setRequest = new RouterOS\Request('/ip/hotspot/user/set');
            $setRequest->setArgument('numbers', $id);
            $setRequest->setArgument('name', $to);
            $client->sendSync($setRequest);
            //disconnect then
            $this->removeHotspotActiveUser($client, $from);
        }
    }

    function add_plan($plan)
    {
        try {
            $mikrotik = $this->info($plan['routers']);
            $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            
            if ($client === null) {
                _log("Skipping plan {$plan['name_plan']} - Router {$plan['routers']} not available");
                return false;
            }
            
            $bw = ORM::for_table("tbl_bandwidth")->find_one($plan['id_bw']);
            if ($bw['rate_down_unit'] == 'Kbps') {
                $unitdown = 'K';
            } else {
                $unitdown = 'M';
            }
            if ($bw['rate_up_unit'] == 'Kbps') {
                $unitup = 'K';
            }
        $rate = $bw['rate_up'] . $unitup . "/" . $bw['rate_down'] . $unitdown;
        if (!empty(trim($bw['burst']))) {
            $rate .= ' ' . $bw['burst'];
        }
        $addRequest = new RouterOS\Request('/ip/hotspot/user/profile/add');
        $client->sendSync(
            $addRequest
                ->setArgument('name', $plan['name_plan'])
                ->setArgument('shared-users', $plan['shared_users'])
                ->setArgument('rate-limit', $rate)
        );
        
        return true;
        } catch (\Exception $e) {
            _log("Error adding plan {$plan['name_plan']} to router {$plan['routers']}: " . $e->getMessage());
            return false;
        }
    }

    function online_customer($customer, $router_name)
    {
        $mikrotik = $this->info($router_name);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        $printRequest = new RouterOS\Request(
            '/ip hotspot active print',
            RouterOS\Query::where('user', $customer['username'])
        );
        $id =  $client->sendSync($printRequest)->getProperty('.id');
        return $id;
    }

    function connect_customer($customer, $ip, $mac_address, $router_name)
    {
        $mikrotik = $this->info($router_name);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        $addRequest = new RouterOS\Request('/ip/hotspot/active/login');
        $client->sendSync(
            $addRequest
                ->setArgument('user', $customer['username'])
                ->setArgument('password', $customer['password'])
                ->setArgument('ip', $ip)
                ->setArgument('mac-address', $mac_address)
        );
    }

    function disconnect_customer($customer, $router_name)
    {
        $mikrotik = $this->info($router_name);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        $printRequest = new RouterOS\Request(
            '/ip hotspot active print',
            RouterOS\Query::where('user', $customer['username'])
        );
        $id = $client->sendSync($printRequest)->getProperty('.id');
        $removeRequest = new RouterOS\Request('/ip/hotspot/active/remove');
        $client->sendSync(
            $removeRequest
                ->setArgument('numbers', $id)
        );
    }


    function update_plan($old_plan, $new_plan)
    {
        $mikrotik = $this->info($new_plan['routers']);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

        $printRequest = new RouterOS\Request(
            '/ip hotspot user profile print .proplist=.id',
            RouterOS\Query::where('name', $old_plan['name_plan'])
        );
        $profileID = $client->sendSync($printRequest)->getProperty('.id');
        if (empty($profileID)) {
            $this->add_plan($new_plan);
        } else {
            $bw = ORM::for_table("tbl_bandwidth")->find_one($new_plan['id_bw']);
            if ($bw['rate_down_unit'] == 'Kbps') {
                $unitdown = 'K';
            } else {
                $unitdown = 'M';
            }
            if ($bw['rate_up_unit'] == 'Kbps') {
                $unitup = 'K';
            } else {
                $unitup = 'M';
            }
            $rate = $bw['rate_up'] . $unitup . "/" . $bw['rate_down'] . $unitdown;
            if (!empty(trim($bw['burst']))) {
                $rate .= ' ' . $bw['burst'];
            }
            $setRequest = new RouterOS\Request('/ip/hotspot/user/profile/set');
            $client->sendSync(
                $setRequest
                    ->setArgument('numbers', $profileID)
                    ->setArgument('name', $new_plan['name_plan'])
                    ->setArgument('shared-users', $new_plan['shared_users'])
                    ->setArgument('rate-limit', $rate)
                    ->setArgument('on-login', $new_plan['on_login'])
                    ->setArgument('on-logout', $new_plan['on_logout'])
            );
        }
    }

    function remove_plan($plan)
    {
        $mikrotik = $this->info($plan['routers']);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        $printRequest = new RouterOS\Request(
            '/ip hotspot user profile print .proplist=.id',
            RouterOS\Query::where('name', $plan['name_plan'])
        );
        $profileID = $client->sendSync($printRequest)->getProperty('.id');
        $removeRequest = new RouterOS\Request('/ip/hotspot/user/profile/remove');
        $client->sendSync(
            $removeRequest
                ->setArgument('numbers', $profileID)
        );
    }

    function info($name)
    {
        return ORM::for_table('tbl_routers')->where('name', $name)->find_one();
    }

    function getClient($ip, $user, $pass)
    {
        global $_app_stage;
        if ($_app_stage == 'Demo') {
            return null;
        }
        
        $maxRetries = 3;
        $retryDelay = 2; // seconds
        $attempt = 0;
        
        while ($attempt < $maxRetries) {
            try {
                $iport = explode(":", $ip);
                if (empty($iport[0])) {
                    _log("Error: Empty IP address for router");
                    throw new \Exception("Invalid router IP address");
                }
                
                // Add timeout parameters
                $client = new RouterOS\Client(
                    $iport[0],
                    $user,
                    $pass,
                    ($iport[1]) ? $iport[1] : null,
                    null,
                    5 // connection timeout in seconds
                );
                
                // Test the connection
                $pingRequest = new RouterOS\Request('/system/resource/print');
                $client->sendSync($pingRequest);
                
                return $client;
            } catch (\Exception $e) {
                $attempt++;
                _log("Connection attempt $attempt failed for IP $ip: " . $e->getMessage());
                
                if ($attempt >= $maxRetries) {
                    _log("Failed to connect to router after $maxRetries attempts for IP $ip");
                    throw new \Exception("Could not connect to router after multiple attempts: " . $e->getMessage());
                }
                
                sleep($retryDelay);
            }
        }
    }

    function removeHotspotUser($client, $username)
    {
        global $_app_stage;
        if ($_app_stage == 'Demo') {
            return null;
        }
        // Remove any active sessions first (silently — user may not be online)
        $this->removeHotspotActiveUser($client, $username);

        // Remove the user entry — exceptions propagate to remove_customer()
        // so the cron can detect failure, return false, and retry next run
        // instead of marking the user as 'off' while they're still on Mikrotik.
        // NOTE: RouterOS does NOT throw for "user not found" — it returns empty.
        // Any exception here is a real connection/API error that must not be swallowed.
        $printRequest = new RouterOS\Request('/ip/hotspot/user/print');
        $printRequest->setArgument('.proplist', '.id');
        $printRequest->setQuery(RouterOS\Query::where('name', $username));
        $userID = $client->sendSync($printRequest)->getProperty('.id');

        if ($userID) {
            $removeRequest = new RouterOS\Request('/ip/hotspot/user/remove');
            $removeRequest->setArgument('numbers', $userID);
            $client->sendSync($removeRequest);
        }
    }

    function addHotspotUser($client, $plan, $customer)
    {
        global $_app_stage;
        if ($_app_stage == 'Demo') {
            return null;
        }

        try {
            // First ensure the profile exists and is properly configured
            $profileRequest = new RouterOS\Request('/ip/hotspot/user/profile/print');
            $profileRequest->setQuery(RouterOS\Query::where('name', $plan['name_plan']));
            $profileResponse = $client->sendSync($profileRequest);
            
            if (!$profileResponse->getProperty('.id')) {
                // Profile doesn't exist, create it
                $this->add_plan($plan);
            }

            // Remove existing user if any
            try {
                $this->removeHotspotUser($client, $customer['username']);
            } catch (\Exception $e) {
                // Ignore if user doesn't exist
            }

            // Add new user with proper configuration
            $addRequest = new RouterOS\Request('/ip/hotspot/user/add');
            $addRequest->setArgument('name', $customer['username'])
                      ->setArgument('profile', $plan['name_plan'])
                      ->setArgument('password', $customer['password'])
                      ->setArgument('comment', $customer['fullname'] . ' | ' . implode(', ', User::getBillNames($customer['id'])))
                      ->setArgument('email', $customer['email']);

            // Add limits based on plan type
            if ($plan['typebp'] == "Limited") {
                if ($plan['limit_type'] == "Time_Limit") {
                    $timelimit = ($plan['time_unit'] == 'Hrs') 
                        ? $plan['time_limit'] . ":00:00" 
                        : "00:" . $plan['time_limit'] . ":00";
                    $addRequest->setArgument('limit-uptime', $timelimit);
                } else if ($plan['limit_type'] == "Data_Limit") {
                    $datalimit = ($plan['data_unit'] == 'GB')
                        ? $plan['data_limit'] . "000000000"
                        : $plan['data_limit'] . "000000";
                    $addRequest->setArgument('limit-bytes-total', $datalimit);
                } else if ($plan['limit_type'] == "Both_Limit") {
                    $timelimit = ($plan['time_unit'] == 'Hrs')
                        ? $plan['time_limit'] . ":00:00"
                        : "00:" . $plan['time_limit'] . ":00";
                    $datalimit = ($plan['data_unit'] == 'GB')
                        ? $plan['data_limit'] . "000000000"
                        : $plan['data_limit'] . "000000";
                    $addRequest->setArgument('limit-uptime', $timelimit)
                              ->setArgument('limit-bytes-total', $datalimit);
                }
            }

            // Send the request and verify
            $response = $client->sendSync($addRequest);
            
            // Verify user was created
            $verifyRequest = new RouterOS\Request('/ip/hotspot/user/print');
            $verifyRequest->setQuery(RouterOS\Query::where('name', $customer['username']));
            $verifyResponse = $client->sendSync($verifyRequest);
            
            if (!$verifyResponse->getProperty('.id')) {
                throw new \Exception('Failed to verify user creation');
            }

            // Enable the user explicitly (needed for 7.18.2+)
            $enableRequest = new RouterOS\Request('/ip/hotspot/user/enable');
            $enableRequest->setArgument('numbers', $verifyResponse->getProperty('.id'));
            $client->sendSync($enableRequest);

        } catch (\Exception $e) {
            _log("Error adding hotspot user: " . $e->getMessage());
            throw $e;
        }
    }

    function setHotspotUser($client, $user, $pass)
    {
        global $_app_stage;
        if ($_app_stage == 'Demo') {
            return null;
        }
        $printRequest = new RouterOS\Request('/ip/hotspot/user/print');
        $printRequest->setArgument('.proplist', '.id');
        $printRequest->setQuery(RouterOS\Query::where('name', $user));
        $id = $client->sendSync($printRequest)->getProperty('.id');

        $setRequest = new RouterOS\Request('/ip/hotspot/user/set');
        $setRequest->setArgument('numbers', $id);
        $setRequest->setArgument('password', $pass);
        $client->sendSync($setRequest);
    }

    function setHotspotUserPackage($client, $username, $plan_name)
    {
        global $_app_stage;
        if ($_app_stage == 'Demo') {
            return null;
        }
        $printRequest = new RouterOS\Request('/ip/hotspot/user/print');
        $printRequest->setArgument('.proplist', '.id');
        $printRequest->setQuery(RouterOS\Query::where('name', $username));
        $id = $client->sendSync($printRequest)->getProperty('.id');

        $setRequest = new RouterOS\Request('/ip/hotspot/user/set');
        $setRequest->setArgument('numbers', $id);
        $setRequest->setArgument('profile', $plan_name);
        $client->sendSync($setRequest);
    }

    function removeHotspotActiveUser($client, $username)
    {
        global $_app_stage;
        if ($_app_stage == 'Demo') {
            return null;
        }
        
        try {
            // Get all active sessions — also fetch 'address' so we can clear conntrack + DHCP
            $onlineRequest = new RouterOS\Request('/ip/hotspot/active/print');
            $onlineRequest->setArgument('.proplist', '.id,address');
            $onlineRequest->setQuery(RouterOS\Query::where('user', $username));
            $activeUsers = $client->sendSync($onlineRequest);

            $ipsToClean = [];

            // Check if we have any active sessions
            if ($activeUsers->getType() === RouterOS\Response::TYPE_DATA) {
                // Handle multiple active sessions
                foreach ($activeUsers as $activeUser) {
                    $id  = $activeUser->getProperty('.id');
                    $ip  = $activeUser->getProperty('address');
                    if (!empty($id)) {
                        $removeRequest = new RouterOS\Request('/ip/hotspot/active/remove');
                        $removeRequest->setArgument('numbers', $id);
                        $client->sendSync($removeRequest);
                        _log("Removed active hotspot session for user: $username (ID: $id, IP: $ip)");
                    }
                    if (!empty($ip)) {
                        $ipsToClean[] = $ip;
                    }
                }
            } else {
                // Single response
                $id = $activeUsers->getProperty('.id');
                $ip = $activeUsers->getProperty('address');
                if (!empty($id)) {
                    $removeRequest = new RouterOS\Request('/ip/hotspot/active/remove');
                    $removeRequest->setArgument('numbers', $id);
                    $client->sendSync($removeRequest);
                    _log("Removed active hotspot session for user: $username (ID: $id, IP: $ip)");
                } else {
                    _log("No active hotspot session found for user: $username");
                }
                if (!empty($ip)) {
                    $ipsToClean[] = $ip;
                }
            }

            // For each IP the user had — clear conntrack + DHCP lease.
            // Without this, the device keeps using existing TCP connections
            // and never gets redirected to the captive portal login page.
            foreach ($ipsToClean as $ip) {
                // Step 2: Remove firewall connection tracking entries for this IP.
                // This kills existing TCP sessions so the device must open new ones,
                // which will be intercepted by the hotspot and trigger the login redirect.
                try {
                    $connRequest = new RouterOS\Request('/ip/firewall/connection/remove');
                    $connRequest->setQuery(RouterOS\Query::where('src-address', $ip));
                    $client->sendSync($connRequest);
                    _log("Cleared conntrack entries for IP: $ip (user: $username)");
                } catch (\Exception $e) {
                    _log("Could not clear conntrack for IP $ip: " . $e->getMessage());
                }

                // Step 3: Remove DHCP lease so the device requests a new IP on reconnect,
                // which guarantees the captive portal is triggered immediately.
                try {
                    $leaseRequest = new RouterOS\Request('/ip/dhcp-server/lease/remove');
                    $leaseRequest->setQuery(RouterOS\Query::where('address', $ip));
                    $client->sendSync($leaseRequest);
                    _log("Removed DHCP lease for IP: $ip (user: $username)");
                } catch (\Exception $e) {
                    _log("Could not remove DHCP lease for IP $ip: " . $e->getMessage());
                }
            }

            // Step 4: Remove all MAC cookies for this user.
            // MAC cookies survive active session removal and auto-reconnect
            // the device even after the package expires — this fixes that.
            try {
                $cookieRequest = new RouterOS\Request('/ip/hotspot/cookie/print');
                $cookieRequest->setQuery(RouterOS\Query::where('user', $username));
                $cookies = $client->sendSync($cookieRequest);
                
                if ($cookies->getType() === RouterOS\Response::TYPE_DATA) {
                    foreach ($cookies as $cookie) {
                        $cookieId = $cookie->getProperty('.id');
                        if (!empty($cookieId)) {
                            $removeRequest = new RouterOS\Request('/ip/hotspot/cookie/remove');
                            $removeRequest->setArgument('numbers', $cookieId);
                            $client->sendSync($removeRequest);
                            _log("Removed MAC cookie for user: $username (ID: $cookieId)");
                        }
                    }
                } else {
                    $cookieId = $cookies->getProperty('.id');
                    if (!empty($cookieId)) {
                        $removeRequest = new RouterOS\Request('/ip/hotspot/cookie/remove');
                        $removeRequest->setArgument('numbers', $cookieId);
                        $client->sendSync($removeRequest);
                        _log("Removed MAC cookie for user: $username (ID: $cookieId)");
                    }
                }
            } catch (\Exception $e) {
                _log("Could not remove MAC cookies for $username: " . $e->getMessage());
            }

        } catch (\Exception $e) {
            _log("Error removing active hotspot user $username: " . $e->getMessage());
            // Don't throw — user may simply not be online at expiry time
        }
    }

    function getIpHotspotUser($client, $username)
    {
        global $_app_stage;
        if ($_app_stage == 'Demo') {
            return null;
        }
        $printRequest = new RouterOS\Request(
            '/ip hotspot active print',
            RouterOS\Query::where('user', $username)
        );
        return $client->sendSync($printRequest)->getProperty('address');
    }
    
    /**
     * Check if router is reachable and responsive
     */
    function checkRouterConnectivity($routerName)
    {
        try {
            $mikrotik = $this->info($routerName);
            if (!$mikrotik) {
                return ['status' => false, 'message' => 'Router not found in database'];
            }
            
            if (empty($mikrotik['ip_address'])) {
                return ['status' => false, 'message' => 'Router IP address is empty'];
            }
            
            $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            if ($client === null) {
                return ['status' => false, 'message' => 'Failed to establish connection'];
            }
            
            return ['status' => true, 'message' => 'Router is connected and responsive'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => 'Connection error: ' . $e->getMessage()];
        }
    }
}
