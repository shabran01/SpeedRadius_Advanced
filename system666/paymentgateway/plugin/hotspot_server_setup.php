<?php

/**
 * Hotspot Server Setup Plugin
 * Automates MikroTik bridge, port, IP, and DHCP server configuration
 * Steps:
 *   1. Create bridge "Hotspot-Server"
 *   2. Assign ether2 and ether3 to bridge
 *   3. Add IP address 10.0.0.1/22 on Hotspot-Server
 *   4. Create DHCP pool, network, and server on Hotspot-Server
 */

use PEAR2\Net\RouterOS;

register_menu("Hotspot Server Setup", true, "hotspot_server_setup", "AFTER_SETTINGS", "ion ion-settings", "", "primary", ["Admin", "SuperAdmin"]);

function hotspot_server_setup()
{
    global $ui, $routes;
    _admin();

    $ui->assign('_title', 'Hotspot Server Setup');
    $ui->assign('_system_menu', 'hotspot_server_setup');

    $routers = ORM::for_table('tbl_routers')->where('enabled', '1')->find_many();
    $ui->assign('routers', $routers);

    $results = [];
    $executed = false;

    if (_post('action') === 'run_setup') {
        $executed = true;

        // --- Collect & sanitise inputs ---
        $router_id   = (int) _post('router_id');
        $bridge_name = trim(_post('bridge_name', 'Hotspot-Server'));
        $iface1      = trim(_post('iface1', 'ether2'));
        $iface2      = trim(_post('iface2', 'ether3'));
        $ip_address  = trim(_post('ip_address', '10.0.0.1/22'));
        $dns_servers = trim(_post('dns_servers', '8.8.8.8,8.8.4.4'));

        // Validate bridge name (alphanumeric, dash, underscore only)
        if (!preg_match('/^[A-Za-z0-9_\-]{1,15}$/', $bridge_name)) {
            $results[] = ['step' => 'Validation', 'ok' => false, 'msg' => 'Invalid bridge name. Use only letters, numbers, dash, or underscore (max 15 chars).'];
            $ui->assign('results', $results);
            $ui->assign('executed', $executed);
            $ui->display('hotspot_server_setup.tpl');
            return;
        }

        // Validate IP/prefix
        if (!preg_match('/^\d{1,3}(\.\d{1,3}){3}\/\d{1,2}$/', $ip_address)) {
            $results[] = ['step' => 'Validation', 'ok' => false, 'msg' => 'Invalid IP address format. Example: 10.0.0.1/22'];
            $ui->assign('results', $results);
            $ui->assign('executed', $executed);
            $ui->display('hotspot_server_setup.tpl');
            return;
        }

        $router = ORM::for_table('tbl_routers')->where('enabled', '1')->find_one($router_id);
        if (!$router) {
            $results[] = ['step' => 'Router', 'ok' => false, 'msg' => 'Router not found or disabled.'];
            $ui->assign('results', $results);
            $ui->assign('executed', $executed);
            $ui->display('hotspot_server_setup.tpl');
            return;
        }

        // Derive DHCP pool range and network from IP/prefix
        list($pool_start, $pool_end, $network_cidr) = hotspot_server_setup_derive_dhcp($ip_address);
        $pool_name   = 'pool_' . strtolower(str_replace([' ', '-'], '_', $bridge_name));
        $server_name = 'dhcp_' . strtolower(str_replace([' ', '-'], '_', $bridge_name));

        // --- Connect ---
        try {
            $client = Mikrotik::getClient(
                $router['ip_address'],
                $router['username'],
                $router['password']
            );
        } catch (\Exception $e) {
            $results[] = ['step' => 'Connect', 'ok' => false, 'msg' => 'Cannot connect to router: ' . $e->getMessage()];
            $ui->assign('results', $results);
            $ui->assign('executed', $executed);
            $ui->display('hotspot_server_setup.tpl');
            return;
        }

        if ($client === null) {
            $results[] = ['step' => 'Connect', 'ok' => false, 'msg' => 'Router connection returned null (demo mode?).'];
            $ui->assign('results', $results);
            $ui->assign('executed', $executed);
            $ui->display('hotspot_server_setup.tpl');
            return;
        }

        $results[] = ['step' => 'Connect', 'ok' => true, 'msg' => 'Connected to router ' . htmlspecialchars($router['name']) . ' (' . htmlspecialchars($router['ip_address']) . ')'];

        // --- Step 1: Create Bridge ---
        $results[] = hotspot_server_setup_exec(
            $client,
            'Step 1: Create Bridge',
            '/interface/bridge/add',
            ['name' => $bridge_name, 'comment' => 'Hotspot Server Bridge']
        );

        // --- Step 2a: Add ether2 to bridge ---
        $results[] = hotspot_server_setup_exec(
            $client,
            'Step 2a: Add ' . $iface1 . ' to bridge',
            '/interface/bridge/port/add',
            ['bridge' => $bridge_name, 'interface' => $iface1]
        );

        // --- Step 2b: Add ether3 to bridge ---
        $results[] = hotspot_server_setup_exec(
            $client,
            'Step 2b: Add ' . $iface2 . ' to bridge',
            '/interface/bridge/port/add',
            ['bridge' => $bridge_name, 'interface' => $iface2]
        );

        // --- Step 3: Add IP address ---
        $results[] = hotspot_server_setup_exec(
            $client,
            'Step 3: Add IP address ' . $ip_address,
            '/ip/address/add',
            ['address' => $ip_address, 'interface' => $bridge_name, 'comment' => 'Hotspot Server IP']
        );

        // --- Step 4a: Create DHCP pool ---
        $results[] = hotspot_server_setup_exec(
            $client,
            'Step 4a: Create DHCP pool (' . $pool_start . '-' . $pool_end . ')',
            '/ip/pool/add',
            ['name' => $pool_name, 'ranges' => $pool_start . '-' . $pool_end]
        );

        // --- Step 4b: Create DHCP network ---
        $results[] = hotspot_server_setup_exec(
            $client,
            'Step 4b: Create DHCP network (' . $network_cidr . ')',
            '/ip/dhcp-server/network/add',
            [
                'address'    => $network_cidr,
                'gateway'    => explode('/', $ip_address)[0],
                'dns-server' => $dns_servers,
                'comment'    => 'Hotspot Server Network'
            ]
        );

        // --- Step 4c: Create DHCP server ---
        $results[] = hotspot_server_setup_exec(
            $client,
            'Step 4c: Create DHCP server on ' . $bridge_name,
            '/ip/dhcp-server/add',
            [
                'name'         => $server_name,
                'interface'    => $bridge_name,
                'address-pool' => $pool_name,
                'disabled'     => 'no',
                'comment'      => 'Hotspot Server DHCP'
            ]
        );
    }

    $ui->assign('results', $results);
    $ui->assign('executed', $executed);
    $ui->display('hotspot_server_setup.tpl');
}

/**
 * Execute a single RouterOS command and return a result row.
 */
function hotspot_server_setup_exec($client, $step, $command, $args = [])
{
    try {
        $req = new RouterOS\Request($command);
        foreach ($args as $key => $value) {
            $req->setArgument($key, $value);
        }
        $response = $client->sendSync($req);

        // Check for API error response
        foreach ($response as $r) {
            if ($r->getType() === RouterOS\Response::TYPE_ERROR) {
                $msg = $r->getProperty('message') ?: 'Unknown error';
                return ['step' => $step, 'ok' => false, 'msg' => htmlspecialchars($msg)];
            }
        }

        return ['step' => $step, 'ok' => true, 'msg' => 'Done'];
    } catch (\Exception $e) {
        return ['step' => $step, 'ok' => false, 'msg' => htmlspecialchars($e->getMessage())];
    }
}

/**
 * Derive DHCP pool start/end and network CIDR from an IP/prefix.
 * Example: 10.0.0.1/22 → pool 10.0.0.2–10.0.3.254, network 10.0.0.0/22
 */
function hotspot_server_setup_derive_dhcp($ip_prefix)
{
    list($ip, $prefix) = explode('/', $ip_prefix);
    $prefix  = (int) $prefix;
    $ip_long = ip2long($ip);
    $mask    = ~((1 << (32 - $prefix)) - 1);

    $network   = $ip_long & $mask;
    $broadcast = $network | ~$mask;

    $pool_start = long2ip($network + 2);    // skip gateway (.1) and network address
    $pool_end   = long2ip($broadcast - 1);  // skip broadcast
    $net_cidr   = long2ip($network) . '/' . $prefix;

    return [$pool_start, $pool_end, $net_cidr];
}
