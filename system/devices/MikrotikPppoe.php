<?php

/**
 *  PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 *  by https://t.me/ibnux
 *
 * This is Core, don't modification except you want to contribute
 * better create new plugin
 **/

use PEAR2\Net\RouterOS;

class MikrotikPppoe
{
    // show Description
    function description()
    {
        return [
            'title' => 'Mikrotik PPPOE',
            'description' => 'To handle connection between PHPNuxBill with Mikrotik PPPOE',
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
            global $isChangePlan;
            $mikrotik = $this->info($plan['routers']);
            if (!$mikrotik) {
                _log("Router not found for plan: " . $plan['name_plan']);
                return false;
            }
            
            $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            if ($client === null) {
                _log("Skipping customer {$customer['username']} - Router {$plan['routers']} not available");
                return false;
            }
            
            $cid = self::getIdByCustomer($customer, $client);
            $isExp = ORM::for_table('tbl_plans')->select("id")->where('plan_expired', $plan['id'])->find_one();
            if (empty($cid)) {
                //customer not exists, add it
                $this->addPpoeUser($client, $plan, $customer, $isExp);
            }else{
                $setRequest = new RouterOS\Request('/ppp/secret/set');
                $setRequest->setArgument('numbers', $cid);
                if (!empty($customer['pppoe_password'])) {
                    $setRequest->setArgument('password', $customer['pppoe_password']);
                } else {
                    $setRequest->setArgument('password', $customer['password']);
                }
                if (!empty($customer['pppoe_username'])) {
                    $setRequest->setArgument('name', $customer['pppoe_username']);
                } else {
                    $setRequest->setArgument('name', $customer['username']);
                }
                $unsetIP = false;
                if (!empty($customer['pppoe_ip']) && !$isExp){
                    $setRequest->setArgument('remote-address', $customer['pppoe_ip']);
                } else {
                    $unsetIP = true;
                }
                $setRequest->setArgument('profile', $plan['name_plan']);
                
                // Build enhanced comment for PPP Secret
                $pppoe_comment = $customer['fullname'] . ' | ' . $customer['email'] . ' | ' . implode(', ', User::getBillNames($customer['id']));
                
                // Add phone number if available
                if (!empty($customer['phonenumber'])) {
                    $pppoe_comment .= ' | Phone: ' . $customer['phonenumber'];
                }
                
                // Add expiry date — use the actual recharge expiration (matches the website)
                $recharge = ORM::for_table('tbl_user_recharges')
                    ->where('customer_id', $customer['id'])
                    ->where('status', 'on')
                    ->order_by_desc('id')
                    ->find_one();
                if ($recharge && !empty($recharge['expiration'])) {
                    $expiry_date = date('d M Y H:i', strtotime($recharge['expiration'] . ' ' . $recharge['time']));
                    $pppoe_comment .= ' | Expires on: ' . $expiry_date;
                } else {
                    // Fallback: compute from plan validity
                    if ($plan['validity_unit'] == 'Period') {
                        $day_exp = User::getAttribute("Expired Date", $customer['id']);
                        if (!$day_exp) {
                            $day_exp = 20;
                            if ($plan['prepaid'] == 'no') {
                                $day_exp = $plan['expired_date'];
                            }
                            if (empty($day_exp)) {
                                $day_exp = 20;
                            }
                        }
                        $expiry_date = date('d M Y H:i', strtotime("+$day_exp days"));
                        $pppoe_comment .= ' | Expires on: ' . $expiry_date;
                    } else {
                        $expiry_date = date('d M Y H:i', strtotime("+$plan[validity] $plan[validity_unit]"));
                        $pppoe_comment .= ' | Expires on: ' . $expiry_date;
                    }
                }
                
                // Add bandwidth information
                if (!empty($plan['id_bw'])) {
                    $bw = ORM::for_table('tbl_bandwidth')->find_one($plan['id_bw']);
                    if ($bw && !empty($bw['name_bw'])) {
                        $pppoe_comment .= ' | Bandwidth: ' . $bw['name_bw'];
                    }
                }
                
                $setRequest->setArgument('comment', $pppoe_comment);
                $client->sendSync($setRequest);

                if($unsetIP){
                    $unsetRequest = new RouterOS\Request('/ppp/secret/unset');
                    $unsetRequest->setArgument('.id', $cid);
                    $unsetRequest->setArgument('value-name','remote-address');
                    $client->sendSync($unsetRequest);
                }

                //disconnect then
                if(isset($isChangePlan) && $isChangePlan){
                    $this->removePpoeActive($client, $customer['username']);
                    if (!empty($customer['pppoe_username'])) {
                        $this->removePpoeActive($client, $customer['pppoe_username']);
                    }
                }
            }
            
            return true;
        } catch (\Exception $e) {
            _log("Error adding customer {$customer['username']} to router {$plan['routers']}: " . $e->getMessage());
            return false;
        }
    }
	
	function sync_customer($customer, $plan)
    {	
        $this->add_customer($customer, $plan);
    }

    function remove_customer($customer, $plan)
    {
        try {
            $mikrotik = $this->info($plan['routers']);
            if (!$mikrotik) {
                _log("Router not found for plan: " . $plan['name_plan']);
                return false;
            }

            $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            if (!$client) {
                _log("Could not establish connection to router: " . $mikrotik['ip_address']);
                return false;
            }

            // Always try to move to expiry plan instead of deleting
            $expiryPlan = null;
            
            // First, check if plan has a specific expiry plan configured
            if (!empty($plan['plan_expired'])) {
                $expiryPlan = ORM::for_table("tbl_plans")->find_one($plan['plan_expired']);
            }
            
            // If no specific expiry plan, look for a default expiry plan
            if (!$expiryPlan) {
                $expiryPlan = ORM::for_table("tbl_plans")
                    ->where('name_plan', 'EXPIRED-PPPOE')
                    ->where('type', 'PPPOE')
                    ->find_one();
            }
            
            // If still no expiry plan found, create a default one
            if (!$expiryPlan) {
                _log("Creating default EXPIRED-PPPOE plan for expired customers");
                $expiryPlan = $this->createDefaultExpiryPlan($plan['routers']);
            }
            
            if ($expiryPlan) {
                // Move customer to expiry plan instead of deleting
                _log("Moving expired PPPoE customer {$customer['username']} to expiry plan: {$expiryPlan['name_plan']}");
                $this->add_customer($customer, $expiryPlan);
                $this->removePpoeActive($client, $customer['username']);
                if (!empty($customer['pppoe_username'])) {
                    $this->removePpoeActive($client, $customer['pppoe_username']);
                }
                return;
            } else {
                // Only delete if we absolutely cannot create an expiry plan
                _log("WARNING: Could not create expiry plan, removing PPPoE user {$customer['username']} from router");
                try {
                    $this->removePpoeUser($client, $customer['username']);
                    if (!empty($customer['pppoe_username'])) {
                        $this->removePpoeUser($client, $customer['pppoe_username']);
                    }
                } catch (\Exception $e) {
                    _log("Error removing PPPoE user: " . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            _log("Error in remove_customer: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Permanently delete a customer from the MikroTik router.
     *
     * Unlike remove_customer() (which moves the user to an expiry plan so the
     * expiry cron can block them), this removes the /ppp/secret entry and
     * disconnects any active session. Used when an admin deletes a customer.
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
            if (!$client) {
                _log("Could not establish connection to router: " . $mikrotik['ip_address']);
                return false;
            }

            $names = array_unique(array_filter([
                $customer['username'],
                isset($customer['pppoe_username']) ? $customer['pppoe_username'] : null,
            ]));

            // Disconnect any active sessions first
            foreach ($names as $uname) {
                try {
                    $this->removePpoeActive($client, $uname);
                } catch (\Exception $e) {
                    // Not online — ignore
                }
            }

            // Remove the /ppp/secret entry (by username and pppoe_username)
            foreach ($names as $uname) {
                try {
                    $printRequest = new RouterOS\Request('/ppp/secret/print');
                    $printRequest->setArgument('.proplist', '.id');
                    $printRequest->setQuery(RouterOS\Query::where('name', $uname));
                    $id = $client->sendSync($printRequest)->getProperty('.id');
                    if (!empty($id)) {
                        $removeRequest = new RouterOS\Request('/ppp/secret/remove');
                        $removeRequest->setArgument('numbers', $id);
                        $client->sendSync($removeRequest);
                        _log("Deleted PPPoE secret {$uname} from router {$plan['routers']}");
                    }
                } catch (\Exception $e) {
                    _log("Error deleting PPPoE secret {$uname}: " . $e->getMessage());
                }
            }

            return true;
        } catch (\Exception $e) {
            _log("Error deleting customer {$customer['username']} from router {$plan['routers']}: " . $e->getMessage());
            return false;
        }
    }

    // customer change username
    public function change_username($plan, $from, $to)
    {
        try {
            $mikrotik = $this->info($plan['routers']);
            if (!$mikrotik) {
                _log("Router not found for plan: " . $plan['name_plan']);
                return false;
            }
            
            $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            if ($client === null) {
                _log("Skipping username change from {$from} to {$to} - Router {$plan['routers']} not available");
                return false;
            }
            
            //check if customer exists
            $printRequest = new RouterOS\Request('/ppp/secret/print');
            $printRequest->setQuery(RouterOS\Query::where('name', $from));
            $cid = $client->sendSync($printRequest)->getProperty('.id');
            if (!empty($cid)) {
                $setRequest = new RouterOS\Request('/ppp/secret/set');
                $setRequest->setArgument('numbers', $cid);
                $setRequest->setArgument('name', $to);
                $client->sendSync($setRequest);
                //disconnect then
                $this->removePpoeActive($client, $from);
            }
            
            return true;
        } catch (\Exception $e) {
            _log("Error changing username from {$from} to {$to} on router {$plan['routers']}: " . $e->getMessage());
            return false;
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

            //Add Pool

            $bw = ORM::for_table("tbl_bandwidth")->find_one($plan['id_bw']);
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
            if(!empty(trim($bw['burst']))){
                $rate .= ' '.$bw['burst'];
            }
            $pool = ORM::for_table("tbl_pool")->where("pool_name", $plan['pool'])->find_one();
            if (!$pool) {
                // Pool not in tbl_pool — use the plan's pool name directly
                $localAddress = $plan['pool'];
                $remoteAddress = $plan['pool'];
            } else {
                $localAddress = (!empty($pool['local_ip'])) ? $pool['local_ip'] : $pool['pool_name'];
                $remoteAddress = $pool['pool_name'];
            }
            $addRequest = new RouterOS\Request('/ppp/profile/add');
            $client->sendSync(
                $addRequest
                    ->setArgument('name', $plan['name_plan'])
                    ->setArgument('local-address', $localAddress)
                    ->setArgument('remote-address', $remoteAddress)
                    ->setArgument('rate-limit', $rate)
            );
            
            return true;
        } catch (\Exception $e) {
            _log("Error adding plan {$plan['name_plan']} to router {$plan['routers']}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Function to ID by username from Mikrotik
     */
    function getIdByCustomer($customer, $client){
        $printRequest = new RouterOS\Request('/ppp/secret/print');
        $printRequest->setQuery(RouterOS\Query::where('name', $customer['username']));
        $id = $client->sendSync($printRequest)->getProperty('.id');
        if(empty($id)){
            if (!empty($customer['pppoe_username'])) {
                $printRequest = new RouterOS\Request('/ppp/secret/print');
                $printRequest->setQuery(RouterOS\Query::where('name', $customer['pppoe_username']));
                $id = $client->sendSync($printRequest)->getProperty('.id');
            }
        }
        return $id;
    }

    function update_plan($old_name, $new_plan)
    {
        try {
            $mikrotik = $this->info($new_plan['routers']);
            if (!$mikrotik) {
                _log("Router not found for plan: " . $new_plan['name_plan']);
                return false;
            }
            
            $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            if ($client === null) {
                _log("Skipping plan update {$new_plan['name_plan']} - Router {$new_plan['routers']} not available");
                return false;
            }

            $printRequest = new RouterOS\Request(
                '/ppp profile print .proplist=.id',
                RouterOS\Query::where('name', $old_name['name_plan'])
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
                if(!empty(trim($bw['burst']))){
                    $rate .= ' '.$bw['burst'];
                }
                $pool = ORM::for_table("tbl_pool")->where("pool_name", $new_plan['pool'])->find_one();
                $setRequest = new RouterOS\Request('/ppp/profile/set');
                $client->sendSync(
                    $setRequest
                        ->setArgument('numbers', $profileID)
                        ->setArgument('local-address', (!empty($pool['local_ip'])) ? $pool['local_ip']: $pool['pool_name'])
                        ->setArgument('remote-address', $pool['pool_name'])
                        ->setArgument('rate-limit', $rate)
                        ->setArgument('on-up', $new_plan['on_login'])
                        ->setArgument('on-down', $new_plan['on_logout'])
                );
            }
            
            return true;
        } catch (\Exception $e) {
            _log("Error updating plan {$new_plan['name_plan']} on router {$new_plan['routers']}: " . $e->getMessage());
            return false;
        }
    }

    function remove_plan($plan)
    {
        try {
            $mikrotik = $this->info($plan['routers']);
            if (!$mikrotik) {
                _log("Router not found for plan: " . $plan['name_plan']);
                return false;
            }
            
            $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            if ($client === null) {
                _log("Skipping plan removal {$plan['name_plan']} - Router {$plan['routers']} not available");
                return false;
            }
            
            $printRequest = new RouterOS\Request(
                '/ppp profile print .proplist=.id',
                RouterOS\Query::where('name', $plan['name_plan'])
            );
            $profileID = $client->sendSync($printRequest)->getProperty('.id');

            $removeRequest = new RouterOS\Request('/ppp/profile/remove');
            $client->sendSync(
                $removeRequest
                    ->setArgument('numbers', $profileID)
            );
            
            return true;
        } catch (\Exception $e) {
            _log("Error removing plan {$plan['name_plan']} from router {$plan['routers']}: " . $e->getMessage());
            return false;
        }
    }

    function add_pool($pool){
        global $_app_stage;
        if ($_app_stage == 'demo') {
            return null;
        }
        
        try {
            $mikrotik = $this->info($pool['routers']);
            if (!$mikrotik) {
                _log("Router not found: " . $pool['routers']);
                return false;
            }
            
            $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            if ($client === null) {
                _log("Skipping pool creation {$pool['pool_name']} - Router {$pool['routers']} not available");
                return false;
            }
            
            $addRequest = new RouterOS\Request('/ip/pool/add');
            $client->sendSync(
                $addRequest
                    ->setArgument('name', $pool['pool_name'])
                    ->setArgument('ranges', $pool['range_ip'])
            );
            
            return true;
        } catch (\Exception $e) {
            _log("Error adding pool {$pool['pool_name']} to router {$pool['routers']}: " . $e->getMessage());
            return false;
        }
    }

    function update_pool($old_pool, $new_pool){
        global $_app_stage;
        if ($_app_stage == 'demo') {
            return null;
        }
        
        try {
            $mikrotik = $this->info($new_pool['routers']);
            if (!$mikrotik) {
                _log("Router not found: " . $new_pool['routers']);
                return false;
            }
            
            $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            if ($client === null) {
                _log("Skipping pool update {$new_pool['pool_name']} - Router {$new_pool['routers']} not available");
                return false;
            }
            
            $printRequest = new RouterOS\Request(
                '/ip pool print .proplist=.id',
                RouterOS\Query::where('name', $old_pool['pool_name'])
            );
            $poolID = $client->sendSync($printRequest)->getProperty('.id');
            if (empty($poolID)) {
                $this->add_pool($new_pool);
            } else {
                $setRequest = new RouterOS\Request('/ip/pool/set');
                $client->sendSync(
                    $setRequest
                        ->setArgument('numbers', $poolID)
                        ->setArgument('name', $new_pool['pool_name'])
                        ->setArgument('ranges', $new_pool['range_ip'])
                );
            }
            
            return true;
        } catch (\Exception $e) {
            _log("Error updating pool {$new_pool['pool_name']} on router {$new_pool['routers']}: " . $e->getMessage());
            return false;
        }
    }

    function remove_pool($pool){
        global $_app_stage;
        if ($_app_stage == 'demo') {
            return null;
        }
        
        try {
            $mikrotik = $this->info($pool['routers']);
            if (!$mikrotik) {
                _log("Router not found: " . $pool['routers']);
                return false;
            }
            
            $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
            if ($client === null) {
                _log("Skipping pool removal {$pool['pool_name']} - Router {$pool['routers']} not available");
                return false;
            }
            
            $printRequest = new RouterOS\Request(
                '/ip pool print .proplist=.id',
                RouterOS\Query::where('name', $pool['pool_name'])
            );
            $poolID = $client->sendSync($printRequest)->getProperty('.id');
            $removeRequest = new RouterOS\Request('/ip/pool/remove');
            $client->sendSync(
                $removeRequest
                    ->setArgument('numbers', $poolID)
            );
            
            return true;
        } catch (\Exception $e) {
            _log("Error removing pool {$pool['pool_name']} from router {$pool['routers']}: " . $e->getMessage());
            return false;
        }
    }


    function online_customer($customer, $router_name)
    {
        $mikrotik = $this->info($router_name);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        $printRequest = new RouterOS\Request(
            '/ppp active print',
            RouterOS\Query::where('name', $customer['username'])
        );
        return $client->sendSync($printRequest)->getProperty('.id');
    }

    function info($name)
    {
        return ORM::for_table('tbl_routers')->where('name', $name)->find_one();
    }

    function getClient($ip, $user, $pass)
    {
        global $_app_stage;
        if ($_app_stage == 'demo') {
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
                _log("Connection attempt $attempt failed: " . $e->getMessage());
                
                if ($attempt >= $maxRetries) {
                    _log("Failed to connect to router after $maxRetries attempts");
                    throw new \Exception("Could not connect to router after multiple attempts: " . $e->getMessage());
                }
                
                sleep($retryDelay);
            }
        }
    }

    function removePpoeUser($client, $username)
    {
        global $_app_stage;
        if ($_app_stage == 'demo') {
            return null;
        }
        $printRequest = new RouterOS\Request('/ppp/secret/print');
        //$printRequest->setArgument('.proplist', '.id');
        $printRequest->setQuery(RouterOS\Query::where('name', $username));
        $id = $client->sendSync($printRequest)->getProperty('.id');
        $removeRequest = new RouterOS\Request('/ppp/secret/remove');
        $removeRequest->setArgument('numbers', $id);
        $client->sendSync($removeRequest);
    }

    function addPpoeUser($client, $plan, $customer, $isExp = false)
    {
        $setRequest = new RouterOS\Request('/ppp/secret/add');
        $setRequest->setArgument('service', 'pppoe');
        $setRequest->setArgument('profile', $plan['name_plan']);
        
        // Build enhanced comment for PPP Secret
        $pppoe_comment = $customer['fullname'] . ' | ' . $customer['email'] . ' | ' . implode(', ', User::getBillNames($customer['id']));
        
        // Add phone number if available
        if (!empty($customer['phonenumber'])) {
            $pppoe_comment .= ' | Phone: ' . $customer['phonenumber'];
        }
        
        // Add expiry date — use the actual recharge expiration (matches the website)
        $recharge = ORM::for_table('tbl_user_recharges')
            ->where('customer_id', $customer['id'])
            ->where('status', 'on')
            ->order_by_desc('id')
            ->find_one();
        if ($recharge && !empty($recharge['expiration'])) {
            $expiry_date = date('d M Y H:i', strtotime($recharge['expiration'] . ' ' . $recharge['time']));
            $pppoe_comment .= ' | Expires on: ' . $expiry_date;
        } else {
            // Fallback: compute from plan validity
            if ($plan['validity_unit'] == 'Period') {
                $day_exp = User::getAttribute("Expired Date", $customer['id']);
                if (!$day_exp) {
                    $day_exp = 20;
                    if ($plan['prepaid'] == 'no') {
                        $day_exp = $plan['expired_date'];
                    }
                    if (empty($day_exp)) {
                        $day_exp = 20;
                    }
                }
                $expiry_date = date('d M Y H:i', strtotime("+$day_exp days"));
                $pppoe_comment .= ' | Expires on: ' . $expiry_date;
            } else {
                $expiry_date = date('d M Y H:i', strtotime("+$plan[validity] $plan[validity_unit]"));
                $pppoe_comment .= ' | Expires on: ' . $expiry_date;
            }
        }
        
        // Add bandwidth information
        if (!empty($plan['id_bw'])) {
            $bw = ORM::for_table('tbl_bandwidth')->find_one($plan['id_bw']);
            if ($bw && !empty($bw['name_bw'])) {
                $pppoe_comment .= ' | Bandwidth: ' . $bw['name_bw'];
            }
        }
        
        $setRequest->setArgument('comment', $pppoe_comment);
        if (!empty($customer['pppoe_password'])) {
            $setRequest->setArgument('password', $customer['pppoe_password']);
        } else {
            $setRequest->setArgument('password', $customer['password']);
        }
        if (!empty($customer['pppoe_username'])) {
            $setRequest->setArgument('name', $customer['pppoe_username']);
        } else {
            $setRequest->setArgument('name', $customer['username']);
        }
        if (!empty($customer['pppoe_ip']) && !$isExp) {
            $setRequest->setArgument('remote-address', $customer['pppoe_ip']);
        }
        $client->sendSync($setRequest);
    }

    function removePpoeActive($client, $username)
    {
        global $_app_stage;
        if ($_app_stage == 'demo') {
            return null;
        }
        $onlineRequest = new RouterOS\Request('/ppp/active/print');
        $onlineRequest->setArgument('.proplist', '.id');
        $onlineRequest->setQuery(RouterOS\Query::where('name', $username));
        $id = $client->sendSync($onlineRequest)->getProperty('.id');

        $removeRequest = new RouterOS\Request('/ppp/active/remove');
        $removeRequest->setArgument('numbers', $id);
        $client->sendSync($removeRequest);
    }

    function getIpHotspotUser($client, $username)
    {
        global $_app_stage;
        if ($_app_stage == 'demo') {
            return null;
        }
        $printRequest = new RouterOS\Request(
            '/ip hotspot active print',
            RouterOS\Query::where('user', $username)
        );
        return $client->sendSync($printRequest)->getProperty('address');
    }

    function addIpToAddressList($client, $ip, $listName, $comment = '')
    {
        global $_app_stage;
        if ($_app_stage == 'demo') {
            return null;
        }
        $addRequest = new RouterOS\Request('/ip/firewall/address-list/add');
        $client->sendSync(
            $addRequest
                ->setArgument('address', $ip)
                ->setArgument('comment', $comment)
                ->setArgument('list', $listName)
        );
    }

    function removeIpFromAddressList($client, $ip)
    {
        global $_app_stage;
        if ($_app_stage == 'demo') {
            return null;
        }
        $printRequest = new RouterOS\Request(
            '/ip firewall address-list print .proplist=.id',
            RouterOS\Query::where('address', $ip)
        );
        $id = $client->sendSync($printRequest)->getProperty('.id');
        $removeRequest = new RouterOS\Request('/ip/firewall/address-list/remove');
        $client->sendSync(
            $removeRequest
                ->setArgument('numbers', $id)
        );
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
    
    /**
     * Create a default expiry plan for PPPoE customers
     * This ensures expired customers are moved to a restricted plan instead of being deleted
     */
    private function createDefaultExpiryPlan($routerName)
    {
        try {
            // Check if we already have a default expiry bandwidth
            $expiryBandwidth = ORM::for_table('tbl_bandwidth')
                ->where('name_bw', 'EXPIRED-PPPOE')
                ->find_one();
                
            if (!$expiryBandwidth) {
                // Create a very limited bandwidth for expired users (64k/64k)
                $expiryBandwidth = ORM::for_table('tbl_bandwidth')->create();
                $expiryBandwidth->name_bw = 'EXPIRED-PPPOE';
                $expiryBandwidth->rate_down = '64';
                $expiryBandwidth->rate_down_unit = 'Kbps';
                $expiryBandwidth->rate_up = '64';
                $expiryBandwidth->rate_up_unit = 'Kbps';
                $expiryBandwidth->burst = '';
                $expiryBandwidth->save();
                _log("Created default expiry bandwidth: EXPIRED-PPPOE");
            }
            
            // Check if we have a default pool for expired users
            $expiryPool = ORM::for_table('tbl_pool')
                ->where('pool_name', 'EXPIRED-POOL')
                ->where('routers', $routerName)
                ->find_one();
                
            if (!$expiryPool) {
                // Create a default pool for expired users
                $expiryPool = ORM::for_table('tbl_pool')->create();
                $expiryPool->pool_name = 'EXPIRED-POOL';
                $expiryPool->range_ip = '90.0.0.2-90.0.0.254';
                $expiryPool->local_ip = '90.0.0.1';
                $expiryPool->routers = $routerName;
                $expiryPool->save();
                _log("Created default expiry pool: EXPIRED-POOL");
                
                // Add the pool to the router
                $this->add_pool($expiryPool);
            }
            
            // Create the expiry plan - unlimited time with 64k bandwidth until they pay
            $expiryPlan = ORM::for_table('tbl_plans')->create();
            $expiryPlan->name_plan = 'EXPIRED-PPPOE';
            $expiryPlan->id_bw = $expiryBandwidth->id;
            $expiryPlan->pool = $expiryPool->pool_name;
            $expiryPlan->routers = $routerName;
            $expiryPlan->type = 'PPPOE';
            $expiryPlan->typebp = 'Unlimited';  // Changed to Unlimited
            $expiryPlan->limit_type = 'Unlimited';  // No time/data limits
            $expiryPlan->time_limit = '0';  // No time limit
            $expiryPlan->time_unit = 'Hrs';
            $expiryPlan->data_limit = '0';  // No data limit
            $expiryPlan->data_unit = 'MB';
            $expiryPlan->price = '0';
            $expiryPlan->enabled = '1';
            $expiryPlan->save();
            
            // Add the plan to the router
            $this->add_plan($expiryPlan);
            
            _log("Created default expiry plan: EXPIRED-PPPOE (64k unlimited until payment) for router: $routerName");
            return $expiryPlan;
            
        } catch (\Exception $e) {
            _log("Error creating default expiry plan: " . $e->getMessage());
            return null;
        }
    }
}
