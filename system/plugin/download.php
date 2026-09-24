<?php
include '../../config.php';

// This file is loaded directly by the hotspot page, so it never goes through
// init.php's autoloader. Text::normalizePhone() is used by the reconnect_phone
// handler below, and without this require it died with "Class Text not found".
require_once __DIR__ . '/../autoload/Text.php';

// ────────────────────────────────────────────────
// CORS — allow requests from MikroTik hotspot
// ────────────────────────────────────────────────
error_reporting(0);
ini_set('display_errors', 0);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// ────────────────────────────────────────────────
// MPESA RECONNECT HANDLER (POST to same page)
// Detects: "UG6PQAGHDN" OR "MpesatillStk - UG6PQAGHDN"
// ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mpesa_reconnect') {
    // RETIRED in v2.2.42. The download page now posts this action to
    // index.php?_route=plugin/CreateHotspotuser&type=mpesa_reconnect, which
    // checks the code against the requesting device's MAC. Leaving this older
    // handler live would be a way around that check, so it fails closed here and
    // the body below is unreachable.
    header('Content-Type: application/json');
    echo json_encode(['Resultcode' => '2', 'Message' => 'Please use the updated Reconnect with M-Pesa Code button.']);
    exit;
}
if (false) { // dead body retained for reference
    header('Content-Type: application/json');
    
    $rawInput = trim($_POST['mpesa_code'] ?? '');
    $mac = trim($_POST['mac'] ?? '');
    $ip = trim($_POST['ip'] ?? '');
    
    if (empty($rawInput)) {
        echo json_encode(['Resultcode' => '2', 'Message' => 'Please enter an MPesa transaction code']);
        exit;
    }
    
    // Extract the actual code — handle both formats:
    // "UG6PQAGHDN"              → UG6PQAGHDN
    // "MpesatillStk - UG6PQAGHDN" → UG6PQAGHDN
    $mpesaCode = $rawInput;
    if (preg_match('/[A-Z0-9]{8,12}$/i', $rawInput, $m)) {
        $mpesaCode = $m[0]; // Extract the last alphanumeric token (the actual code)
    }
    
    // 1. Try EXACT match on gateway_trx_id
    $tx = $mysqli->prepare("SELECT * FROM tbl_payment_gateway WHERE gateway_trx_id = ?");
    $tx->bind_param("s", $mpesaCode);
    $tx->execute();
    $transaction = $tx->get_result()->fetch_assoc();
    
    // 2. Try LIKE match (handles "MpesatillStk - UG6PQAGHDN" in gateway_trx_id)
    if (!$transaction) {
        $txLike = $mysqli->prepare("SELECT * FROM tbl_payment_gateway WHERE gateway_trx_id LIKE ?");
        $likeCode = '%' . $mpesaCode . '%';
        $txLike->bind_param("s", $likeCode);
        $txLike->execute();
        $transaction = $txLike->get_result()->fetch_assoc();
    }
    
    // 3. Try finding via tbl_user_recharges.method (stores "MpesatillStk - CODE")
    if (!$transaction) {
        $rechQ = $mysqli->prepare("
            SELECT pg.* FROM tbl_payment_gateway pg
            INNER JOIN tbl_user_recharges ur ON ur.username = pg.username
            WHERE ur.method LIKE ?
            ORDER BY pg.id DESC LIMIT 1
        ");
        $rechQ->bind_param("s", $likeCode);
        $rechQ->execute();
        $transaction = $rechQ->get_result()->fetch_assoc();
    }
    
    // 4. Check raw M-Pesa transactions table
    if (!$transaction) {
        $raw = $mysqli->prepare("SELECT * FROM tbl_mpesa_transactions WHERE TransID = ?");
        $raw->bind_param("s", $mpesaCode);
        $raw->execute();
        $mpesaRaw = $raw->get_result()->fetch_assoc();
        
        // Also try LIKE on raw transactions
        if (!$mpesaRaw) {
            $rawLike = $mysqli->prepare("SELECT * FROM tbl_mpesa_transactions WHERE TransID LIKE ?");
            $rawLike->bind_param("s", $likeCode);
            $rawLike->execute();
            $mpesaRaw = $rawLike->get_result()->fetch_assoc();
        }
        
        if ($mpesaRaw) {
            // Force-create the payment gateway record
            $phone = $mpesaRaw['MSISDN'] ?? '';
            $amount = $mpesaRaw['TransAmount'] ?? '0';
            $transId = $mpesaRaw['TransID'] ?? '';
            $billRef = $mpesaRaw['BillRefNumber'] ?? '';
            
            $planId = 0;
            $planRouters = '';
            if (!empty($billRef)) {
                $planQ = $mysqli->prepare("SELECT id, routers FROM tbl_plans WHERE name_plan = ? OR id = ?");
                $planQ->bind_param("si", $billRef, $billRef);
                $planQ->execute();
                $plan = $planQ->get_result()->fetch_assoc();
                if ($plan) {
                    $planId = $plan['id'];
                    $planRouters = $plan['routers'];
                }
            }
            
            $username = 'MPESA_' . $phone;
            $insert = $mysqli->prepare("INSERT INTO tbl_payment_gateway (gateway, gateway_trx_id, username, price, status, paid_date, plan_id, routers) VALUES ('mpesa', ?, ?, ?, 2, NOW(), ?, ?)");
            $insert->bind_param("sssis", $transId, $username, $amount, $planId, $planRouters);
            $insert->execute();
            
            // Re-fetch
            $tx->execute();
            $transaction = $tx->get_result()->fetch_assoc();
        }
    }
    
    if (!$transaction) {
        echo json_encode(['Resultcode' => '2', 'Message' => 'MPesa code not found. Please check and try again.']);
        exit;
    }
    
    // Check transaction status
    if ($transaction['status'] != 2) {
        $statusMsgs = ['0' => 'pending', '1' => 'failed', '3' => 'cancelled', '4' => 'timed out'];
        $msg = isset($statusMsgs[$transaction['status']]) ? $statusMsgs[$transaction['status']] : 'unknown';
        echo json_encode(['Resultcode' => '2', 'Message' => 'Payment is ' . $msg . '. Please ensure payment was completed.']);
        exit;
    }
    
    $username = $transaction['username'];
    if (empty($username)) {
        echo json_encode(['Resultcode' => '2', 'Message' => 'No user account linked to this transaction.']);
        exit;
    }
    
    // Activate/reconnect session — search by LIKE on method
    $sessQ = $mysqli->prepare("SELECT * FROM tbl_user_recharges WHERE username = ? AND method LIKE ? ORDER BY id DESC LIMIT 1");
    $sessQ->bind_param("ss", $username, $likeCode);
    $sessQ->execute();
    $session = $sessQ->get_result()->fetch_assoc();
    
    if (!$session) {
        $sessQ2 = $mysqli->prepare("SELECT * FROM tbl_user_recharges WHERE username = ? ORDER BY id DESC LIMIT 1");
        $sessQ2->bind_param("s", $username);
        $sessQ2->execute();
        $session = $sessQ2->get_result()->fetch_assoc();
    }
    
    if ($session) {
        $update = $mysqli->prepare("UPDATE tbl_user_recharges SET status = 'on' WHERE id = ?");
        $update->bind_param("i", $session['id']);
        $update->execute();
    }
    
    echo json_encode([
        'Resultcode' => '3',
        'username' => $username,
        'Message' => 'Reconnected successfully! You will be logged in shortly.',
        'Status' => 'success'
    ]);
    exit;
}

// ────────────────────────────────────────────────
// RECONNECT BY MPESA NUMBER (POST)
// ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reconnect_phone') {
    header('Content-Type: application/json');
    
    try {
        $rawPhone = trim($_POST['phone'] ?? '');
        $mac = trim($_POST['mac'] ?? '');
        
        if (empty($rawPhone)) {
            echo json_encode(['Resultcode' => '2', 'Message' => 'Please enter your M-Pesa phone number']);
            exit;
        }
        
        // Format to 254XXXXXXXXX
        $phone = $rawPhone;
        // One shared normaliser: 0712..., 712..., +254712..., 254712... -> 254712...
        $phone = Text::normalizePhone($phone);
        
        if (strlen($phone) !== 12) {
            echo json_encode(['Resultcode' => '2', 'Message' => 'Invalid phone number. Please enter a valid M-Pesa number.']);
            exit;
        }
        
        // A single M-Pesa number is commonly shared by a whole household, so one
        // phone can legitimately sit on more than one account. This used to end in
        // LIMIT 1, which grabbed an arbitrary row and reconnected the WRONG device:
        // the customer paid for their second TV and the first one was switched on.
        $cust = $mysqli->prepare("SELECT username FROM tbl_customers WHERE phonenumber = ? ORDER BY id ASC");
        $cust->bind_param("s", $phone);
        $cust->execute();
        $custRes = $cust->get_result();
        $matches = [];
        while ($row = $custRes->fetch_assoc()) {
            $matches[] = $row['username'];
        }
        
        if (count($matches) === 0) {
            echo json_encode(['Resultcode' => '2', 'Message' => 'No account found for ' . $rawPhone . '. Please buy a package first.']);
            exit;
        }

        if (count($matches) > 1) {
            // Refuse rather than guess. Only the M-Pesa code identifies the exact
            // payment, so it is the only safe way to tell these accounts apart.
            echo json_encode([
                'Resultcode' => '2',
                'Message' => 'This number is linked to ' . count($matches) . ' accounts, so we cannot tell which device you are on. Please use "Reconnect with M-Pesa Code" instead.'
            ]);
            exit;
        }
        
        $username = $matches[0];
        
        // Check for active plan (not expired) OR recently expired (within 24hrs)
        $sess = $mysqli->prepare("SELECT * FROM tbl_user_recharges WHERE username = ? AND expiration > DATE_SUB(NOW(), INTERVAL 24 HOUR) ORDER BY expiration DESC LIMIT 1");
        $sess->bind_param("s", $username);
        $sess->execute();
        $session = $sess->get_result()->fetch_assoc();
        
        if (!$session) {
            echo json_encode(['Resultcode' => '2', 'Message' => 'No active package found. Please buy a new package.']);
            exit;
        }
        
        $isExpired = strtotime($session['expiration']) < time();
        
        // Reactivate session (status only, macaddress column may not exist)
        $update = $mysqli->prepare("UPDATE tbl_user_recharges SET status = 'on' WHERE id = ?");
        $update->bind_param("i", $session['id']);
        $update->execute();
        
        echo json_encode([
            'Resultcode' => '3',
            'username' => $username,
            'Message' => $isExpired 
                ? 'Your last package expired. Session restored — consider renewing soon.' 
                : 'Welcome back! Your account ' . $username . ' has been reconnected.',
            'Status' => 'success'
        ]);
    } catch (\Throwable $e) {
        echo json_encode(['Resultcode' => '2', 'Message' => 'Server error: ' . $e->getMessage()]);
    }
    exit;
}


// ────────────────────────────────────────────────
// TV / DEVICE BINDING (POST) — for devices that cannot display the hotspot
// login page (most smart TVs). The customer opens this page on their phone,
// enters the TV's MAC, picks a package and pays. Once the payment is confirmed
// we bind the TV's MAC on the router so it gets online without the login page.
// ────────────────────────────────────────────────
//
// How the binding behaves on the router:
//   'bypassed' - the device skips the hotspot entirely and gets internet with no
//                username and no login page. This is what the TV flow uses: a TV
//                cannot type a username, so MAC-login is one moving part too many.
//                The router will NOT expire it, so cron.php removes the binding
//                once the package behind it lapses.
//   'regular'  - MAC login: the device is tied to a hotspot user named after its
//                MAC. Limits are enforced by the router, but it additionally
//                requires 'mac' in the hotspot profile's login-by list.
if (!defined('TV_BINDING_TYPE')) {
    define('TV_BINDING_TYPE', 'bypassed');
}

/**
 * Resolve the router to work on: the one passed in, else the configured one.
 * $settings is loaded further down this file, after the POST handlers run, so
 * the appconfig row is read directly here instead.
 */
function tv_resolve_router($mysqli, $routerId = 0) {
    if ($routerId > 0) {
        $q = $mysqli->prepare("SELECT * FROM tbl_routers WHERE id = ? LIMIT 1");
        $q->bind_param("i", $routerId);
    } else {
        $res  = $mysqli->query("SELECT value FROM tbl_appconfig WHERE setting = 'router_name' LIMIT 1");
        $row  = $res ? $res->fetch_assoc() : null;
        $name = $row['value'] ?? '';
        $q = $mysqli->prepare("SELECT * FROM tbl_routers WHERE name = ? LIMIT 1");
        $q->bind_param("s", $name);
    }
    $q->execute();
    return $q->get_result()->fetch_assoc();
}

/**
 * Does any hotspot profile on this router accept MAC authentication?
 *
 * RouterOS authenticates a MAC-login device by looking for a hotspot user whose
 * NAME is the device's MAC address. Without 'mac' in the profile's login-by
 * list that lookup never happens, so a bound device stays offline while looking
 * perfectly healthy. This is why the check happens BEFORE taking payment.
 */
function tv_mac_login_enabled($client) {
    $req = new \PEAR2\Net\RouterOS\Request('/ip/hotspot/profile/print');
    foreach ($client->sendSync($req) as $pr) {
        if ($pr->getType() === \PEAR2\Net\RouterOS\Response::TYPE_DATA) {
            if (strpos(strtolower((string)$pr->getProperty('login-by')), 'mac') !== false) {
                return true;
            }
        }
    }
    return false;
}

// ────────────────────────────────────────────────
// TV CAPABILITY CHECK (POST) — run before payment
// ────────────────────────────────────────────────
// Nothing worse than taking a customer's money for a device we cannot actually
// connect, so the page asks this first and blocks the pay button if the router
// is not ready for MAC login.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tv_check') {
    header('Content-Type: application/json');

    try {
        require_once __DIR__ . '/../autoload/PEAR2/Autoload.php';

        $router = tv_resolve_router($mysqli, (int)($_POST['router_id'] ?? 0));
        if (!$router) {
            echo json_encode(['status' => 'error', 'message' => 'Router not configured.']);
            exit;
        }

        $rip   = explode(':', $router['ip_address']);
        $rport = !empty($rip[1]) ? $rip[1] : 8728;
        $client = new \PEAR2\Net\RouterOS\Client($rip[0], $router['username'], $router['password'], $rport, false, 8);

        $macLogin = tv_mac_login_enabled($client);
        // A bypassed binding never authenticates, so MAC login is irrelevant; only
        // the 'regular' (MAC login) type depends on it.
        $blocked = (TV_BINDING_TYPE === 'regular' && !$macLogin);

        echo json_encode([
            'status'       => 'success',
            'mac_login'    => !$blocked,
            'binding_type' => TV_BINDING_TYPE,
            'message'      => $blocked ? 'Device sign-in is not enabled on this network yet.' : ''
        ]);
    } catch (\Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => 'Could not check the router: ' . $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tv_bind') {
    header('Content-Type: application/json');

    try {
        $rawMac     = trim($_POST['mac'] ?? '');
        $deviceName = trim($_POST['device_name'] ?? '');
        $accountId  = trim($_POST['account_id'] ?? '');
        $routerId   = (int)($_POST['router_id'] ?? 0);

        // Normalise the MAC: accept AA:BB:CC:DD:EE:FF, aa-bb-..., AABBCCDDEEFF
        $mac = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', $rawMac));
        if (strlen($mac) !== 12) {
            echo json_encode(['status' => 'error', 'message' => 'That MAC address looks wrong. It needs 12 characters, like AA:BB:CC:DD:EE:FF.']);
            exit;
        }
        $mac = implode(':', str_split($mac, 2));

        if ($accountId === '') {
            echo json_encode(['status' => 'error', 'message' => 'Missing account reference. Please start the payment again.']);
            exit;
        }

        // ── Payment gate ──────────────────────────────────────────────────────
        // Never bind a device before the money is confirmed. The recharge row is
        // only set to 'on' by the payment verification step, so requiring an
        // active row is what stops a free bind.
        $accQ = $mysqli->prepare("SELECT username FROM tbl_customers WHERE username = ? LIMIT 1");
        $accQ->bind_param("s", $accountId);
        $accQ->execute();
        $account = $accQ->get_result()->fetch_assoc();
        if (!$account) {
            echo json_encode(['status' => 'error', 'message' => 'We could not find an account for this payment.']);
            exit;
        }

        $payQ = $mysqli->prepare("SELECT id, expiration FROM tbl_user_recharges WHERE username = ? AND status = 'on' ORDER BY id DESC LIMIT 1");
        $payQ->bind_param("s", $account['username']);
        $payQ->execute();
        $paid = $payQ->get_result()->fetch_assoc();
        if (!$paid) {
            echo json_encode(['status' => 'error', 'message' => 'This payment is not active yet. Please wait a moment and try again.']);
            exit;
        }

        // ── Resolve the router ────────────────────────────────────────────────
        $router = tv_resolve_router($mysqli, $routerId);
        if (!$router) {
            echo json_encode(['status' => 'error', 'message' => 'Router not configured. Please contact support.']);
            exit;
        }

        require_once __DIR__ . '/../autoload/PEAR2/Autoload.php';

        $rip = explode(':', $router['ip_address']);
        $rhost = $rip[0];
        $rport = !empty($rip[1]) ? $rip[1] : 8728;

        $client = new \PEAR2\Net\RouterOS\Client($rhost, $router['username'], $router['password'], $rport, false, 8);

        // ── The binding needs an IP, not just a MAC ───────────────────────────
        // /ip hotspot ip-binding requires an 'address'. Two sources are consulted
        // and the result is VALIDATED, because the first version trusted the DHCP
        // lease alone and happily wrote a binding pointing at a broadcast address
        // (10.0.2.255). It looked completely healthy and did nothing.
        $ipCandidates = [];

        // 1) The hotspot's own host table - the address the device is really using.
        try {
            $hostReq = new \PEAR2\Net\RouterOS\Request('/ip/hotspot/host/print');
            $hostReq->setArgument('.proplist', '.id,address,mac-address');
            $hostReq->setQuery(\PEAR2\Net\RouterOS\Query::where('mac-address', $mac));
            foreach ($client->sendSync($hostReq) as $hRow) {
                if ($hRow->getType() === \PEAR2\Net\RouterOS\Response::TYPE_DATA) {
                    $ipCandidates[] = trim((string)$hRow->getProperty('address'));
                }
            }
        } catch (\Throwable $e) {
            // optional source - carry on with the lease table
        }

        // 2) The DHCP lease, which exists as soon as the device has joined the WiFi.
        try {
            $leaseReq = new \PEAR2\Net\RouterOS\Request('/ip/dhcp-server/lease/print');
            $leaseReq->setArgument('.proplist', '.id,address,mac-address,status');
            $leaseReq->setQuery(\PEAR2\Net\RouterOS\Query::where('mac-address', $mac));
            foreach ($client->sendSync($leaseReq) as $lease) {
                if ($lease->getType() === \PEAR2\Net\RouterOS\Response::TYPE_DATA) {
                    $ipCandidates[] = trim((string)$lease->getProperty('address'));
                }
            }
        } catch (\Throwable $e) {
            // optional source
        }

        // Take the first candidate that is a valid host address.
        //
        // NOTE: deliberately NO network/broadcast heuristic here. An earlier version
        // rejected anything ending in .0 or .255 on the assumption of a /24 subnet,
        // which wrongly refused 10.0.2.255 - an entirely ordinary host address on this
        // router's hotspot subnet. The router is authoritative about where the device
        // is, so its answer is taken as given. Only values that can never be a host
        // are discarded.
        $ip       = '';
        $rejected = [];
        foreach ($ipCandidates as $candidate) {
            if ($candidate === '') {
                continue;
            }
            if (!filter_var($candidate, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $rejected[] = $candidate;
                continue;
            }
            // 0.0.0.0 and the multicast range are never a device's address. This is
            // subnet-independent, unlike the last-octet guess it replaces.
            $asLong = ip2long($candidate);
            if ($candidate === '0.0.0.0' || $asLong === false || $asLong >= ip2long('224.0.0.0')) {
                $rejected[] = $candidate;
                continue;
            }
            $ip = $candidate;
            break;
        }

        if ($ip === '') {
            $detail = $rejected
                ? ' The router reported ' . implode(', ', array_unique($rejected)) . ', which is not a valid device address.'
                : '';
            echo json_encode([
                'status'  => 'error',
                'message' => 'We could not find this device on the network.' . $detail . ' Make sure the device is connected to the WiFi, wait about 30 seconds, then try again.'
            ]);
            exit;
        }

        // Comment records who the binding belongs to and when it lapses, so it can
        // be audited (and cleaned up) from the router.
        $comment = 'SR|' . $account['username'] . '|exp ' . ($paid['expiration'] ?? '-');
        if ($deviceName !== '') {
            $comment .= '|' . str_replace('|', '/', substr($deviceName, 0, 40));
        }

        // ── The device needs its OWN hotspot user, named after the MAC ───────
        // login-by=mac does not log a device in as "whoever owns that MAC".
        // RouterOS looks for a hotspot user whose NAME is the MAC address. The
        // purchased account is named after the account id, so the MAC can never
        // match it - which is exactly why the first version left the device
        // offline while the binding looked fine. We mirror the purchased user's
        // profile and limits onto a MAC-named user so the device signs in by
        // itself AND the plan's limits still apply.
        $srcUser = null;
        $srcReq  = new \PEAR2\Net\RouterOS\Request('/ip/hotspot/user/print');
        $srcReq->setArgument('.proplist', '.id,profile,limit-uptime,limit-bytes-total');
        $srcReq->setQuery(\PEAR2\Net\RouterOS\Query::where('name', $account['username']));
        foreach ($client->sendSync($srcReq) as $su) {
            if ($su->getType() === \PEAR2\Net\RouterOS\Response::TYPE_DATA) {
                $srcUser = $su;
                break;
            }
        }

        $profileName = $srcUser ? (string)$srcUser->getProperty('profile') : '';
        if ($profileName === '') {
            // Fall back to the profile name recorded on the recharge row.
            $profileName = (string)($paid['namebp'] ?? '');
        }
        if ($profileName === '' && TV_BINDING_TYPE === 'regular') {
            echo json_encode(['status' => 'error', 'message' => 'Could not work out which package profile to apply. Please contact support.']);
            exit;
        }

        // Idempotent: update the MAC user if it exists, otherwise add it.
        // A bypassed device skips the hotspot entirely and never authenticates, so
        // it needs no login account at all. The MAC-named user is only required for
        // the 'regular' (MAC login) binding type.
        if (TV_BINDING_TYPE === 'regular') {
            $macUserId = '';
            $macQ = new \PEAR2\Net\RouterOS\Request('/ip/hotspot/user/print');
            $macQ->setArgument('.proplist', '.id');
            $macQ->setQuery(\PEAR2\Net\RouterOS\Query::where('name', $mac));
            foreach ($client->sendSync($macQ) as $mu) {
                if ($mu->getType() === \PEAR2\Net\RouterOS\Response::TYPE_DATA) {
                    $macUserId = (string)$mu->getProperty('.id');
                    break;
                }
            }

            if ($macUserId !== '') {
                $uReq = new \PEAR2\Net\RouterOS\Request('/ip/hotspot/user/set');
                $uReq->setArgument('.id', $macUserId);
            } else {
                $uReq = new \PEAR2\Net\RouterOS\Request('/ip/hotspot/user/add');
                $uReq->setArgument('name', $mac);
                $uReq->setArgument('password', $mac);
            }
            $uReq->setArgument('profile', $profileName);
            if ($srcUser) {
                foreach (['limit-uptime', 'limit-bytes-total'] as $limitProp) {
                    $val = $srcUser->getProperty($limitProp);
                    if ($val !== null && $val !== '') {
                        $uReq->setArgument($limitProp, $val);
                    }
                }
            }
            $uReq->setArgument('comment', $comment);

            $uResp = $client->sendSync($uReq);
            if ($uResp->getType() === \PEAR2\Net\RouterOS\Response::TYPE_FATAL) {
                echo json_encode(['status' => 'error', 'message' => 'Router rejected the device account: ' . $uResp->getProperty('message')]);
                exit;
            }

            // Explicitly enable it - required on RouterOS 7.18+ (same as addHotspotUser).
            $enableReq = new \PEAR2\Net\RouterOS\Request('/ip/hotspot/user/enable');
            $enableReq->setArgument('numbers', $mac);
            $client->sendSync($enableReq);
        }

        // ── Bind the MAC, which pins the device to its IP ────────────────────
        // Idempotent: update the binding if this MAC already has one, otherwise add.
        $existingId = '';
        $bindReq = new \PEAR2\Net\RouterOS\Request('/ip/hotspot/ip-binding/print');
        $bindReq->setQuery(\PEAR2\Net\RouterOS\Query::where('mac-address', $mac));
        foreach ($client->sendSync($bindReq) as $b) {
            if ($b->getType() === \PEAR2\Net\RouterOS\Response::TYPE_DATA) {
                $existingId = (string)$b->getProperty('.id');
                break;
            }
        }

        if ($existingId !== '') {
            $req = new \PEAR2\Net\RouterOS\Request('/ip/hotspot/ip-binding/set');
            $req->setArgument('.id', $existingId);
        } else {
            $req = new \PEAR2\Net\RouterOS\Request('/ip/hotspot/ip-binding/add');
        }
        $req->setArgument('mac-address', $mac);
        $req->setArgument('address', $ip);
        $req->setArgument('type', TV_BINDING_TYPE);
        $req->setArgument('server', 'all');
        $req->setArgument('comment', $comment);

        $resp = $client->sendSync($req);
        if ($resp->getType() === \PEAR2\Net\RouterOS\Response::TYPE_FATAL) {
            echo json_encode(['status' => 'error', 'message' => 'Router rejected the binding: ' . $resp->getProperty('message')]);
            exit;
        }

        // ── Speed limit ───────────────────────────────────────────────────────
        // A bypassed device never authenticates, so the hotspot never applies the
        // plan's profile and therefore never shapes it - it would run at full line
        // speed. The limit is applied here instead, as a simple queue for the
        // device's IP. The rate is COPIED off the hotspot profile rather than
        // rebuilt from the plan, so the device gets exactly what a normal login
        // would have got and the two cannot drift apart.
        $rateLimit = '';
        $pfReq = new \PEAR2\Net\RouterOS\Request('/ip/hotspot/user/profile/print');
        $pfReq->setArgument('.proplist', '.id,rate-limit');
        $pfReq->setQuery(\PEAR2\Net\RouterOS\Query::where('name', $profileName));
        foreach ($client->sendSync($pfReq) as $pf) {
            if ($pf->getType() === \PEAR2\Net\RouterOS\Response::TYPE_DATA) {
                $rateLimit = trim((string)$pf->getProperty('rate-limit'));
                break;
            }
        }

        // Fall back to the plan's bandwidth table if the profile has no rate-limit.
        if ($rateLimit === '' && $profileName !== '') {
            $bwQ = $mysqli->prepare("SELECT b.rate_up, b.rate_up_unit, b.rate_down, b.rate_down_unit
                                     FROM tbl_plans p LEFT JOIN tbl_bandwidth b ON b.id = p.id_bw
                                     WHERE p.name_plan = ? LIMIT 1");
            $bwQ->bind_param("s", $profileName);
            $bwQ->execute();
            $bw = $bwQ->get_result()->fetch_assoc();
            if ($bw && !empty($bw['rate_down'])) {
                $upRate   = $bw['rate_up']   . ($bw['rate_up_unit']   === 'Kbps' ? 'K' : 'M');
                $downRate = $bw['rate_down'] . ($bw['rate_down_unit'] === 'Kbps' ? 'K' : 'M');
                $rateLimit = $upRate . '/' . $downRate;
            }
        }

        // A simple queue takes one 'up/down' pair, but a profile rate-limit can carry
        // a burst spec after the first token, which the queue would reject.
        $maxLimit = '';
        if ($rateLimit !== '') {
            $rateBits = preg_split('/\s+/', $rateLimit);
            $maxLimit = $rateBits[0] ?? '';
        }

        $shapeWarning = '';
        $queueName    = 'SR-tv-' . str_replace(':', '', $mac);

        // The address has already been validated as a real host address above, so
        // there is no network/broadcast case left to handle here.
        if ($maxLimit === '') {
            $shapeWarning = 'Connected, but no speed limit was found for this package, so it is running unshaped.';
        } else {
            try {
                // Idempotent: drop any earlier queue for this device so re-purchasing
                // does not stack queues.
                $qPrint = new \PEAR2\Net\RouterOS\Request('/queue/simple/print');
                $qPrint->setQuery(\PEAR2\Net\RouterOS\Query::where('name', $queueName));
                foreach ($client->sendSync($qPrint) as $qOld) {
                    if ($qOld->getType() === \PEAR2\Net\RouterOS\Response::TYPE_DATA) {
                        $qRm = new \PEAR2\Net\RouterOS\Request('/queue/simple/remove');
                        $qRm->setArgument('numbers', (string)$qOld->getProperty('.id'));
                        $client->sendSync($qRm);
                        break;
                    }
                }

                $qAdd = new \PEAR2\Net\RouterOS\Request('/queue/simple/add');
                $qAdd->setArgument('name', $queueName);
                $qAdd->setArgument('target', $ip . '/32');
                $qAdd->setArgument('max-limit', $maxLimit);
                $qAdd->setArgument('comment', $comment);

                $qResp = $client->sendSync($qAdd);
                if ($qResp->getType() === \PEAR2\Net\RouterOS\Response::TYPE_FATAL) {
                    // Never fail the bind over shaping - the device is already online.
                    $shapeWarning = 'Connected, but the speed limit could not be applied: ' . $qResp->getProperty('message');
                }
            } catch (\Throwable $e) {
                $shapeWarning = 'Connected, but the speed limit could not be applied: ' . $e->getMessage();
            }
        }

        // Report the profile state too, so a silent MAC-login misconfiguration is
        // visible during testing instead of showing up as "customer paid, TV dead".
        // Only meaningful for the MAC-login binding type - a bypassed device never
        // authenticates at all, so there is nothing to warn about.
        $macLoginOn = (TV_BINDING_TYPE === 'regular') ? tv_mac_login_enabled($client) : true;

        echo json_encode([
            'status'            => 'success',
            'mac'               => $mac,
            'ip'                => $ip,
            'type'              => TV_BINDING_TYPE,
            'username'          => $account['username'],
            'profile'           => $profileName,
            'mac_login_enabled' => $macLoginOn,
            'max_limit'         => $maxLimit,
            'shape_warning'     => $shapeWarning,
            'message'           => 'Done! Your device should now connect without the login page.'
        ]);
    } catch (\Throwable $e) {
        echo json_encode(['status' => 'error', 'message' => 'Could not reach the router: ' . $e->getMessage()]);
    }
    exit;
}


// Batch load all settings in 1 query — 6× faster than 5 separate queries
$settings = [];
$allSettings = $mysqli->query("SELECT setting, value FROM tbl_appconfig WHERE setting IN ('hotspot_title','description','phone','CompanyName','router_name','router_id')");
while ($row = $allSettings->fetch_assoc()) {
    $settings[$row['setting']] = $row['value'];
}
$hotspotTitle = $settings['hotspot_title'] ?? '';
$description  = $settings['description'] ?? '';
$phone        = $settings['phone'] ?? '';
$company      = $settings['CompanyName'] ?? 'ISP';
$routerName   = $settings['router_name'] ?? '';
$routerId     = $settings['router_id'] ?? '';

// Package cards are rendered client-side by fetchData() -> plugin/hotspot_plan,
// which applies the active/enabled filter. No server-side plan query is needed here.

// Initialize HTML content variable
$htmlContent = "";
$htmlContent .= "<!DOCTYPE html>\n";
$htmlContent .= "<html lang=\"en\">\n";
$htmlContent .= "<head>\n";
$htmlContent .= "    <meta charset=\"UTF-8\">\n";
$htmlContent .= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
$htmlContent .= "    <title>$company</title>\n";
$htmlContent .= "    <script src=\"https://cdn.tailwindcss.com\"></script>\n";
$htmlContent .= "    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css\">\n";
$htmlContent .= "    <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/glider-js@1.7.7/glider.min.css\" />\n";
$htmlContent .= "    <script src=\"https://cdn.jsdelivr.net/npm/glider-js@1.7.7/glider.min.js\"></script>\n";
$htmlContent .= "    <link rel=\"preconnect\" href=\"https://cdn.jsdelivr.net\">\n";
$htmlContent .= "    <link rel=\"preconnect\" href=\"https://cdnjs.cloudflare.com\" crossorigin>\n";
$htmlContent .= "    <link rel=\"stylesheet\" href=\"https://rsms.me/inter/inter.css\">\n";
$htmlContent .= "    <!-- <link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\"> -->\n";

$htmlContent .= "    <style>\n";
$htmlContent .= "        .btn-3d-green {\n";
$htmlContent .= "            background: linear-gradient(145deg, #4ade80, #22c55e);\n";
$htmlContent .= "            border: 1px solid rgba(255,255,255,0.2);\n";
$htmlContent .= "            box-shadow: 0 4px 15px rgba(74, 222, 128, 0.3),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.3);\n";
$htmlContent .= "            text-shadow: 0 1px 2px rgba(0,0,0,0.2);\n";
$htmlContent .= "            transform-style: preserve-3d;\n";
$htmlContent .= "            transition: all 0.3s ease;\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-green:hover {\n";
$htmlContent .= "            background: linear-gradient(145deg, #22c55e, #4ade80);\n";
$htmlContent .= "            transform: translateY(-2px) scale(1.02);\n";
$htmlContent .= "            box-shadow: 0 8px 20px rgba(74, 222, 128, 0.4),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.4);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-green:active {\n";
$htmlContent .= "            transform: translateY(1px);\n";
$htmlContent .= "            box-shadow: 0 2px 10px rgba(74, 222, 128, 0.2);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-blue {\n";
$htmlContent .= "            background: linear-gradient(145deg, #60a5fa, #3b82f6);\n";
$htmlContent .= "            border: 1px solid rgba(255,255,255,0.2);\n";
$htmlContent .= "            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.3);\n";
$htmlContent .= "            text-shadow: 0 1px 2px rgba(0,0,0,0.2);\n";
$htmlContent .= "            transform-style: preserve-3d;\n";
$htmlContent .= "            transition: all 0.3s ease;\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-blue:hover {\n";
$htmlContent .= "            background: linear-gradient(145deg, #3b82f6, #60a5fa);\n";
$htmlContent .= "            transform: translateY(-2px) scale(1.02);\n";
$htmlContent .= "            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.4);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-blue:active {\n";
$htmlContent .= "            transform: translateY(1px);\n";
$htmlContent .= "            box-shadow: 0 2px 10px rgba(59, 130, 246, 0.2);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d {\n";
$htmlContent .= "            position: relative;\n";
$htmlContent .= "            border-radius: 8px;\n";
$htmlContent .= "            overflow: hidden;\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d::before {\n";
$htmlContent .= "            content: '';\n";
$htmlContent .= "            position: absolute;\n";
$htmlContent .= "            top: 0;\n";
$htmlContent .= "            left: -100%;\n";
$htmlContent .= "            width: 100%;\n";
$htmlContent .= "            height: 100%;\n";
$htmlContent .= "            background: linear-gradient(\n";
$htmlContent .= "                120deg,\n";
$htmlContent .= "                transparent,\n";
$htmlContent .= "                rgba(255, 255, 255, 0.3),\n";
$htmlContent .= "                transparent\n";
$htmlContent .= "            );\n";
$htmlContent .= "            transition: 0.5s;\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d:hover::before {\n";
$htmlContent .= "            left: 100%;\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-gold {\n";
$htmlContent .= "            background: linear-gradient(145deg, #ffd700, #ffa500);\n";
$htmlContent .= "            border: 1px solid rgba(255,255,255,0.3);\n";
$htmlContent .= "            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.5);\n";
$htmlContent .= "            text-shadow: 0 1px 2px rgba(0,0,0,0.2);\n";
$htmlContent .= "            transform-style: preserve-3d;\n";
$htmlContent .= "            transition: all 0.3s ease;\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-gold:hover {\n";
$htmlContent .= "            background: linear-gradient(145deg, #ffa500, #ffd700);\n";
$htmlContent .= "            transform: translateY(-2px) scale(1.02);\n";
$htmlContent .= "            box-shadow: 0 8px 20px rgba(255, 215, 0, 0.4),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.6);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-purple {\n";
$htmlContent .= "            background: linear-gradient(145deg, #9333ea, #7e22ce);\n";
$htmlContent .= "            border: 1px solid rgba(255,255,255,0.2);\n";
$htmlContent .= "            box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.3);\n";
$htmlContent .= "            text-shadow: 0 1px 2px rgba(0,0,0,0.2);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-purple:hover {\n";
$htmlContent .= "            background: linear-gradient(145deg, #7e22ce, #9333ea);\n";
$htmlContent .= "            transform: translateY(-2px) scale(1.02);\n";
$htmlContent .= "            box-shadow: 0 8px 20px rgba(147, 51, 234, 0.4),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.4);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-rose {\n";
$htmlContent .= "            background: linear-gradient(145deg, #f43f5e, #e11d48);\n";
$htmlContent .= "            border: 1px solid rgba(255,255,255,0.2);\n";
$htmlContent .= "            box-shadow: 0 4px 15px rgba(244, 63, 94, 0.3),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.3);\n";
$htmlContent .= "            text-shadow: 0 1px 2px rgba(0,0,0,0.2);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-rose:hover {\n";
$htmlContent .= "            background: linear-gradient(145deg, #e11d48, #f43f5e);\n";
$htmlContent .= "            transform: translateY(-2px) scale(1.02);\n";
$htmlContent .= "            box-shadow: 0 8px 20px rgba(244, 63, 94, 0.4),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.4);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-emerald {\n";
$htmlContent .= "            background: linear-gradient(145deg, #34d399, #059669);\n";
$htmlContent .= "            border: 1px solid rgba(255,255,255,0.2);\n";
$htmlContent .= "            box-shadow: 0 4px 15px rgba(52, 211, 153, 0.3),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.3);\n";
$htmlContent .= "            text-shadow: 0 1px 2px rgba(0,0,0,0.2);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-emerald:hover {\n";
$htmlContent .= "            background: linear-gradient(145deg, #059669, #34d399);\n";
$htmlContent .= "            transform: translateY(-2px) scale(1.02);\n";
$htmlContent .= "            box-shadow: 0 8px 20px rgba(52, 211, 153, 0.4),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.4);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-cyan {\n";
$htmlContent .= "            background: linear-gradient(145deg, #22d3ee, #0891b2);\n";
$htmlContent .= "            border: 1px solid rgba(255,255,255,0.2);\n";
$htmlContent .= "            box-shadow: 0 4px 15px rgba(34, 211, 238, 0.3),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.3);\n";
$htmlContent .= "            text-shadow: 0 1px 2px rgba(0,0,0,0.2);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-cyan:hover {\n";
$htmlContent .= "            background: linear-gradient(145deg, #0891b2, #22d3ee);\n";
$htmlContent .= "            transform: translateY(-2px) scale(1.02);\n";
$htmlContent .= "            box-shadow: 0 8px 20px rgba(34, 211, 238, 0.4),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.4);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-black {\n";
$htmlContent .= "            background: linear-gradient(145deg, #2d3748, #1a202c);\n";
$htmlContent .= "            border: 1px solid rgba(255,255,255,0.1);\n";
$htmlContent .= "            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.1);\n";
$htmlContent .= "            text-shadow: 0 1px 2px rgba(0,0,0,0.5);\n";
$htmlContent .= "            transform-style: preserve-3d;\n";
$htmlContent .= "            transition: all 0.3s ease;\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-black:hover {\n";
$htmlContent .= "            background: linear-gradient(145deg, #1a202c, #2d3748);\n";
$htmlContent .= "            transform: translateY(-2px) scale(1.02);\n";
$htmlContent .= "            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4),\n";
$htmlContent .= "                        inset 0 2px 4px rgba(255, 255, 255, 0.2);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        .btn-3d-black:active {\n";
$htmlContent .= "            transform: translateY(1px);\n";
$htmlContent .= "            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);\n";
$htmlContent .= "        }\n";
$htmlContent .= "    </style>\n";
$htmlContent .= "</head>\n";

$htmlContent .= "<body class=\"font-sans antialiased text-gray-900 bg-gray-900 font-inter\">\n";
$htmlContent .= "    <!-- Main Content -->\n";
$htmlContent .= "    <div class=\"mx-auto max-w-screen-2xl px-4 md:px-4\">\n";
$htmlContent .= "        <div class=\"max-h-34 relative mx-auto mt-4 flex max-w-lg flex-1 shrink-0 items-center justify-center overflow-hidden shadow-lg rounded-lg bg-green-100\">\n";
$htmlContent .= "            <!-- overlay - start -->\n";
$htmlContent .= "            <!-- <div class=\"absolute inset-0  mix-blend-multiply\"></div> -->\n";
$htmlContent .= "            <!-- overlay - end -->\n";
$htmlContent .= "            <!-- text start -->\n";
$htmlContent .= "            <div class=\"relative flex flex-col items-center p-4 sm:max-w-xl\">\n";
$htmlContent .= "                <p id=\"company-name\" class=\"mb-4 text-center text-2xl font-bold text-gray-800 sm:text-xl md:mb-2 \">$company</p>\n";
$htmlContent .= "                <h3 class=\"text-lg italic text-gray-700 mb-2\">How to Purchase:</h3>\n"; // Smaller and italicized title
$htmlContent .= "                <ol class=\"text-base text-left text-gray-800 mb-1 list-decimal pl-6\">\n";
$htmlContent .= "                    <li>Click on your preferred package Buy</li>\n";
$htmlContent .= "                    <li>Enter Your Mpesa No.</li>\n";
$htmlContent .= "                    <li>Enter pin</li>\n";
$htmlContent .= "                    <li>Wait for 30sec to be connected</li>\n";
$htmlContent .= "                </ol>\n";
$htmlContent .= "                <p id=\"customer-care\" class=\"mb-4 text-center text-lg font-medium text-gray-700 sm:text-1xl md:mb-1  md:text-xl\">CUSTOMER CARE : $phone</p>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "            <!-- text end -->\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";

$htmlContent .= "    <div class=\"py-2 sm:py-4 lg:py-4\">\n";
$htmlContent .= "        <div class=\"mx-auto max-w-screen-2xl px-4 md:px-4\">\n";
$htmlContent .= "            <div class=\"mx-auto max-w-lg\">\n";
$htmlContent .= "                <div class=\"flex flex-col gap-4\">\n";
$htmlContent .= "                    <button type=\"button\" class=\"btn-3d btn-3d-green flex items-center justify-center gap-2 rounded-lg px-8 py-3 text-center text-sm font-semibold text-white outline-none md:text-base\" onclick=\"redeemVoucher()\">\n";
$htmlContent .= "                        <svg class=\"w-5 h-5 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\">\n";
$htmlContent .= "                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7\"></path>\n";
$htmlContent .= "                        </svg>\n";
$htmlContent .= "                        Redeem Voucher\n";
$htmlContent .= "                    </button>\n";
$htmlContent .= "                    <button type=\"button\" class=\"btn-3d btn-3d-blue flex items-center justify-center gap-2 rounded-lg px-8 py-3 text-center text-sm font-semibold text-white outline-none md:text-base\" onclick=\"reconnectWithNumber()\">\n";
$htmlContent .= "                        <svg class=\"w-5 h-5 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\">\n";
$htmlContent .= "                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z\"></path>\n";
$htmlContent .= "                        </svg>\n";
$htmlContent .= "                        Reconnect with Phone Number\n";
$htmlContent .= "                    </button>\n";
$htmlContent .= "                    <button type=\"button\" class=\"btn-3d btn-3d-blue flex items-center justify-center gap-2 rounded-lg px-8 py-3 text-center text-sm font-semibold text-white outline-none md:text-base\" onclick=\"reconnectWithMpesa()\">\n";
$htmlContent .= "                        <svg class=\"w-5 h-5 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\">\n";
$htmlContent .= "                            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\"></path>\n";
$htmlContent .= "                        </svg>\n";
$htmlContent .= "                        Reconnect with M-Pesa Code\n";
$htmlContent .= "                    </button>\n";
$htmlContent .= "                    <button type=\"button\" class=\"btn-3d btn-3d-green flex items-center justify-center gap-2 rounded-lg px-8 py-3 text-center text-sm font-semibold text-white outline-none md:text-base\" onclick=\"document.getElementById('submitBtn').click()\">\n";
$htmlContent .= "                        Already Have an Active Package?\n";
$htmlContent .= "                    </button>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";

$htmlContent .= "    <div class=\"text-center\">\n";
$htmlContent .= "        $(if trial == 'yes')\n";
$htmlContent .= "        <a href=\"$(link-login-only)?dst=$(link-orig-esc)&amp;username=T-$(mac-esc)\" class=\"btn-3d text-space\" style=\"display: inline-block; background-color: #4CAF50; color: white; padding: 10px 20px; text-align: center; text-decoration: none; font-weight: bold; border-radius: 5px;\">\n";
$htmlContent .= "            FREE TRIAL 10 MINS\n";
$htmlContent .= "        </a>\n";
$htmlContent .= "        $(endif)\n";
$htmlContent .= "    </div>\n";
$htmlContent .= "    <div><br></div>\n"; // Added line break for spacing

$htmlContent .= "    <div id=\"hotspot-ads-container\" class=\"mx-auto max-w-lg px-4 mb-2\"></div>\n";

$htmlContent .= "    <div class=\"py-2 sm:py-4 lg:py-6\">\n";
$htmlContent .= "        <div class=\"mx-auto max-w-screen-2xl px-4 md:px-8\">\n";
$htmlContent .= "            <div class=\"mx-auto max-w-lg grid grid-cols-2 sm:grid-cols-3 gap-1 p-1\" id=\"cards-container\">\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";

// ── Pay For a TV ────────────────────────────────────────────────────────
// Some smart TVs cannot render the hotspot login page at all. The customer
// opens this page on their phone instead and supplies the TV's MAC.
// Attributes below use single quotes on purpose: this whole page is built as a
// double-quoted PHP string, so single-quoted HTML avoids a layer of escaping.
// This card sits ABOVE the account-login form on purpose: a customer whose TV
// cannot load the page needs this path first, not below two login forms.
$htmlContent .= "    <div class=\"container mx-auto px-4 mb-4\">\n";
$htmlContent .= "        <div class=\"max-w-md mx-auto rounded-2xl overflow-hidden\" style=\"background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);backdrop-filter:blur(8px);\">\n";
$htmlContent .= "            <div class=\"px-5 py-4\">\n";
$htmlContent .= "                <p style='color:#fff;font-size:14px;font-weight:700;margin:0 0 6px;'>TV not showing the login page?</p>\n";
$htmlContent .= "                <p style='color:rgba(255,255,255,0.55);font-size:12.5px;line-height:1.6;margin:0 0 12px;'>Smart TVs usually cannot open the hotspot sign-in page. Enter your TV's MAC address, choose a package and pay &mdash; we will connect the TV for you.</p>\n";
$htmlContent .= "                <button type=\"button\" onclick=\"openTvModal()\" class=\"btn-3d btn-3d-blue w-full flex items-center justify-center gap-2 rounded-lg px-6 py-3 text-sm font-semibold text-white outline-none\">\n";
$htmlContent .= "                    <i class=\"fas fa-tv\"></i> Pay For a TV\n";
$htmlContent .= "                </button>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";

$htmlContent .= "    <div class=\"container mx-auto px-4 mb-4\">\n";
$htmlContent .= "        <div class=\"max-w-md mx-auto bg-white rounded-lg overflow-hidden md:max-w-lg\">\n";
$htmlContent .= "            <div class=\"md:flex\">\n";
$htmlContent .= "                <div class=\"w-full p-5\">\n";
$htmlContent .= "                    <div class=\"text-center\">\n";
$htmlContent .= "                        <h3 class=\"text-2xl text-gray-900\">Already Have an Active Package?</h3>\n";
$htmlContent .= "                    </div>\n";
$htmlContent .= "                    <form id=\"loginForm\" class=\"form\" name=\"login\" action=\"$(link-login-only)\" method=\"post\" $(if chap-id)onSubmit=\"return doLogin()\" $(endif)>\n";
$htmlContent .= "                        <input type=\"hidden\" name=\"dst\" value=\"$(link-orig)\" />\n";
$htmlContent .= "                        <input type=\"hidden\" name=\"popup\" value=\"true\" />\n";
$htmlContent .= "                        <div class=\"text-center\">\n";
$htmlContent .= "                            <label class=\"block text-gray-700 text-sm font-bold mb-2\" for=\"username\">Enter your account number or user name</label>\n";
$htmlContent .= "                            <div>\n";
$htmlContent .= "                                <input id=\"usernameInput\" name=\"username\" type=\"text\" value=\"\" placeholder=\"e.g ACC123456\" class=\"w-full rounded-lg border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring\" />\n";
$htmlContent .= "                                <button id=\"submitBtn\" class=\"btn-3d btn-3d-green w-full mt-3 flex items-center justify-center gap-2 rounded-lg px-8 py-3 text-center text-sm font-semibold text-white outline-none md:text-base\" type=\"button\" onclick=\"submitLogin()\">\n";
$htmlContent .= "                                    Connect\n";
$htmlContent .= "                                </button>\n";
$htmlContent .= "                            </div>\n";
$htmlContent .= "                        </div>\n";
$htmlContent .= "                        <input type=\"hidden\" name=\"password\" value=\"1234\">\n";
$htmlContent .= "                    </form>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";

$htmlContent .= "    <div class=\"container mx-auto px-4 mb-4\">\n";
$htmlContent .= "        <div class=\"max-w-md mx-auto bg-white rounded-lg overflow-hidden md:max-w-lg\">\n";
$htmlContent .= "            <div class=\"md:flex\">\n";
$htmlContent .= "                <div class=\"w-full p-5\">\n";
$htmlContent .= "                    <div class=\"text-center\">\n";
$htmlContent .= "                        <h3 class=\"text-2xl text-gray-900\">User Login</h3>\n";
$htmlContent .= "                    </div>\n";

$htmlContent .= "                    <form class=\"login\" name=\"login\" action=\"$(link-login-only)\" method=\"post\">\n";
$htmlContent .= "                        <input type=\"hidden\" name=\"dst\" value=\"$(link-orig)\" />\n";
$htmlContent .= "                        <p class=\"logo-area text-center\" id=\"wifi_name\"></p>\n";
$htmlContent .= "                        <p class=\"text-center\" style=\"color: black;\">(Enter your username and password to login.)</p>\n";
$htmlContent .= "                        <input type=\"hidden\" name=\"popup\" value=\"true\" />\n";
$htmlContent .= "                        <div class=\"s-login\">\n";
$htmlContent .= "                            <hr/>\n";
$htmlContent .= "                            <input type=\"text\" name=\"username\" class=\"username w-full rounded-lg border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring\" placeholder=\"Username\" required />\n";
$htmlContent .= "                            <div class=\"relative mt-4\">\n";  // Added margin-top for spacing
$htmlContent .= "                                <input type=\"password\" name=\"password\" id=\"password\" class=\"password w-full rounded-lg border bg-gray-50 px-3 py-2 text-gray-800 outline-none ring-indigo-300 transition duration-100 focus:ring\" placeholder=\"Password\" required />\n";  // Removed value attribute to make it empty
$htmlContent .= "                                <input type=\"checkbox\" onclick=\"togglePasswordVisibility()\" class=\"absolute top-2 right-3\" style=\"cursor: pointer;\">  \n";
$htmlContent .= "                            </div>\n";
$htmlContent .= "                        </div>\n";
$htmlContent .= "                        <button type=\"submit\" class=\"btn-3d btn-3d-green btn btn-login w-full mt-3 flex items-center justify-center gap-2 rounded-lg px-8 py-3 text-center text-sm font-semibold text-white outline-none md:text-base\"><b>LOGIN</b></button>\n";
$htmlContent .= "                    </form>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";

// Device Info Card (MikroTik substitutes $(ip) and $(mac) automatically)
$htmlContent .= "    <div class=\"container mx-auto px-4 mb-6\">\n";
$htmlContent .= "        <div class=\"max-w-md mx-auto rounded-2xl overflow-hidden\" style=\"background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);backdrop-filter:blur(8px);\">\n";
$htmlContent .= "            <div class=\"px-5 py-3 border-b\" style=\"border-color:rgba(255,255,255,0.08);\">\n";
$htmlContent .= "                <p style=\"color:rgba(255,255,255,0.5);font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;text-align:center;margin:0;\">Your Device Info</p>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "            <div class=\"px-5 py-4\">\n";
// IP Address row
$htmlContent .= "                <div class=\"flex items-center justify-between py-2\" style=\"border-bottom:1px solid rgba(255,255,255,0.07);\">\n";
$htmlContent .= "                    <div class=\"flex items-center gap-2\" style=\"color:rgba(255,255,255,0.5);font-size:13px;\">\n";
$htmlContent .= "                        <svg xmlns=\"http://www.w3.org/2000/svg\" style=\"width:15px;height:15px;flex-shrink:0;\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\" stroke-width=\"2\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9\"/></svg>\n";
$htmlContent .= "                        IP Address\n";
$htmlContent .= "                    </div>\n";
$htmlContent .= "                    <span style=\"color:#fff;font-family:monospace;font-size:13px;font-weight:600;background:rgba(99,102,241,0.2);border:1px solid rgba(99,102,241,0.3);padding:2px 10px;border-radius:6px;\">$(ip)</span>\n";
$htmlContent .= "                </div>\n";
// MAC Address row
$htmlContent .= "                <div class=\"flex items-center justify-between py-2\">\n";
$htmlContent .= "                    <div class=\"flex items-center gap-2\" style=\"color:rgba(255,255,255,0.5);font-size:13px;\">\n";
$htmlContent .= "                        <svg xmlns=\"http://www.w3.org/2000/svg\" style=\"width:15px;height:15px;flex-shrink:0;\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\" stroke-width=\"2\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" d=\"M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2v-4M9 21H5a2 2 0 01-2-2v-4m0 0h18\"/></svg>\n";
$htmlContent .= "                        MAC Address\n";
$htmlContent .= "                    </div>\n";
$htmlContent .= "                    <span style=\"color:#fff;font-family:monospace;font-size:13px;font-weight:600;background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);padding:2px 10px;border-radius:6px;\">$(mac)</span>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";

$htmlContent .= "<script>\n";
$htmlContent .= "function togglePasswordVisibility() {\n";
$htmlContent .= "    var passwordInput = document.getElementById('password');\n";
$htmlContent .= "    if (passwordInput.type === 'password') {\n";
$htmlContent .= "        passwordInput.type = 'text';\n";
$htmlContent .= "    } else {\n";
$htmlContent .= "        passwordInput.type = 'password';\n";
$htmlContent .= "    }\n";
$htmlContent .= "}\n";
$htmlContent .= "</script>\n";

// ── Pay For a TV (modal) ────────────────────────────────────────────────
// The trigger card was moved up, above the "Already Have an Active Package?"
// form, so a customer whose TV cannot load the page meets it first.
// The modal stays here on purpose: it is position:fixed, so where it sits in the
// DOM makes no difference to where it appears.
$htmlContent .= "    <div id='tv-modal' style='display:none;position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,.65);overflow-y:auto;padding:16px;'>\n";
$htmlContent .= "        <div style='max-width:460px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 24px 60px -20px rgba(0,0,0,.5);'>\n";
$htmlContent .= "            <div style='padding:14px 18px;border-bottom:1px solid #eef2f7;display:flex;align-items:center;justify-content:space-between;'>\n";
$htmlContent .= "                <strong style='font-size:15px;color:#0f172a;'>Pay For a TV</strong>\n";
$htmlContent .= "                <button type='button' onclick='closeTvModal()' style='background:none;border:0;font-size:22px;line-height:1;color:#94a3b8;cursor:pointer;'>&times;</button>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "            <div style='padding:16px 18px 20px;'>\n";
$htmlContent .= "                <div style='background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;border-radius:10px;padding:10px 12px;font-size:12px;line-height:1.6;margin-bottom:12px;'>\n";
$htmlContent .= "                    <strong>Tip:</strong> If your TV can open this page, you do not need to enter a MAC address &mdash; just pick a package above and buy normally. Only use this form if your TV cannot show the sign-in page.\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "                <p style='font-size:12.5px;color:#334155;margin:0 0 12px;'>Enter the MAC address from your TV settings, choose a package and pay.</p>\n";
$htmlContent .= "                <details style='margin-bottom:14px;'>\n";
$htmlContent .= "                    <summary style='cursor:pointer;font-size:12.5px;font-weight:700;color:#2563eb;'>How do I find my TV MAC address?</summary>\n";
$htmlContent .= "                    <div style='font-size:12px;color:#475569;line-height:1.8;margin-top:8px;background:#f8fafc;border-radius:10px;padding:10px 12px;'>\n";
$htmlContent .= "                        <p style='margin:0 0 6px;'>On your TV, go to:</p>\n";
$htmlContent .= "                        <ul style='margin:0 0 8px;padding-left:18px;'>\n";
$htmlContent .= "                            <li><strong>Vitron</strong> &mdash; Settings &rarr; Network &rarr; Network Status / About</li>\n";
$htmlContent .= "                            <li><strong>Samsung</strong> &mdash; Settings &rarr; General &rarr; Network &rarr; Network Status</li>\n";
$htmlContent .= "                            <li><strong>LG</strong> &mdash; Settings &rarr; Network &rarr; Wi-Fi &rarr; Advanced Settings</li>\n";
$htmlContent .= "                            <li><strong>Sony / Android TV</strong> &mdash; Settings &rarr; About &rarr; Status</li>\n";
$htmlContent .= "                            <li><strong>Hisense</strong> &mdash; Settings &rarr; Network &rarr; Network Information</li>\n";
$htmlContent .= "                            <li><strong>TCL / Roku</strong> &mdash; Settings &rarr; Network &rarr; About</li>\n";
$htmlContent .= "                        </ul>\n";
$htmlContent .= "                        <p style='margin:0;'>Look for <strong>MAC Address</strong> or <strong>Wi-Fi Address</strong> (format AA:BB:CC:DD:EE:FF).</p>\n";
$htmlContent .= "                    </div>\n";
$htmlContent .= "                </details>\n";
$htmlContent .= "                <label style='display:block;font-size:11.5px;font-weight:700;color:#334155;margin-bottom:5px;'>Device MAC Address</label>\n";
$htmlContent .= "                <input id='tv-mac' type='text' autocomplete='off' placeholder='e.g. AA:BB:CC:DD:EE:FF' oninput='tvFormatMac(this)' style='width:100%;border:1px solid #e2e8f0;border-radius:9px;padding:10px 12px;font-size:13px;font-family:ui-monospace,Consolas,monospace;margin-bottom:12px;'>\n";
$htmlContent .= "                <label style='display:block;font-size:11.5px;font-weight:700;color:#334155;margin-bottom:5px;'>Device Name (optional)</label>\n";
$htmlContent .= "                <input id='tv-name' type='text' autocomplete='off' placeholder='e.g. Living Room TV' style='width:100%;border:1px solid #e2e8f0;border-radius:9px;padding:10px 12px;font-size:13px;margin-bottom:12px;'>\n";
$htmlContent .= "                <label style='display:block;font-size:11.5px;font-weight:700;color:#334155;margin-bottom:5px;'>Select Package</label>\n";
$htmlContent .= "                <select id='tv-plan' style='width:100%;border:1px solid #e2e8f0;border-radius:9px;padding:10px 12px;font-size:13px;background:#fff;margin-bottom:12px;'>\n";
$htmlContent .= "                    <option value=''>Loading packages&hellip;</option>\n";
$htmlContent .= "                </select>\n";
$htmlContent .= "                <div style='background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;border-radius:10px;padding:9px 12px;font-size:11.5px;line-height:1.6;margin-bottom:12px;'>\n";
$htmlContent .= "                    <strong>One device per package.</strong> This package works on this one device only. Please do not buy one package for several devices.\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "                <label style='display:block;font-size:11.5px;font-weight:700;color:#334155;margin-bottom:5px;'>Phone Number (M-Pesa)</label>\n";
$htmlContent .= "                <input id='tv-phone' type='tel' autocomplete='off' placeholder='e.g. 0712345678' style='width:100%;border:1px solid #e2e8f0;border-radius:9px;padding:10px 12px;font-size:13px;margin-bottom:14px;'>\n";
$htmlContent .= "                <div id='tv-msg' style='display:none;border-radius:9px;padding:9px 11px;font-size:12px;line-height:1.5;margin-bottom:12px;'></div>\n";
$htmlContent .= "                <button type='button' id='tv-submit' onclick='submitTvPay()' style='width:100%;background:#2563eb;color:#fff;border:0;border-radius:10px;padding:13px;font-size:14px;font-weight:700;cursor:pointer;margin-bottom:8px;'>Bind &amp; Pay</button>\n";
$htmlContent .= "                <button type='button' onclick='closeTvModal()' style='width:100%;background:#f1f5f9;color:#475569;border:0;border-radius:10px;padding:12px;font-size:13px;font-weight:600;cursor:pointer;'>Cancel</button>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";

$htmlContent .= "    <div class=\"mx-auto max-w-screen-2xl px-4 md:px-8\">\n";
$htmlContent .= "        <div class=\"mx-auto mb-4 max-w-lg\">\n";
$htmlContent .= "            <div class=\"border-t py-4\">\n";
$htmlContent .= "                <p class=\"text-xs text-center\" style=\"color: white; font-weight: bold;\">&copy;  All rights reserved. Created by SpeedRadius</p>\n";
$htmlContent .= "            </div>\n";
$htmlContent .= "        </div>\n";
$htmlContent .= "    </div>\n";
$htmlContent .= "<script>\n";
$htmlContent .= "// ---- Pay For a TV -------------------------------------------------------\n";
$htmlContent .= "function openTvModal() {\n";
$htmlContent .= "    var m = document.getElementById('tv-modal');\n";
$htmlContent .= "    if (m) m.style.display = 'block';\n";
$htmlContent .= "    tvHideMsg();\n";
$htmlContent .= "    // Fresh start: the button goes back to the payment step.\n";
$htmlContent .= "    var b = document.getElementById('tv-submit');\n";
$htmlContent .= "    if (b) { b.onclick = submitTvPay; b.disabled = false; b.style.opacity = '1'; b.textContent = 'Bind & Pay'; b.style.background = '#2563eb'; }\n";
$htmlContent .= "    tvCheckCapability();\n";
$htmlContent .= "}\n";
$htmlContent .= "// Asks the router whether it can sign devices in by MAC at all. Checked BEFORE\n";
$htmlContent .= "// payment: taking money for a device we cannot connect is the one failure worth\n";
$htmlContent .= "// blocking up front.\n";
$htmlContent .= "function tvCheckCapability() {\n";
$htmlContent .= "    var body = new URLSearchParams();\n";
$htmlContent .= "    body.append('action', 'tv_check');\n";
$htmlContent .= "    fetch('" . APP_URL . "/system/plugin/download.php', { method: 'POST', body: body })\n";
$htmlContent .= "    .then(function (r) { return r.json(); })\n";
$htmlContent .= "    .then(function (data) {\n";
$htmlContent .= "        if (!data || data.status !== 'success' || data.mac_login !== false) return;\n";
$htmlContent .= "        tvShowMsg('Device sign-in is not switched on for this network yet, so a TV cannot be connected right now. Please contact support.<br><strong>You have not been charged.</strong>', 'err');\n";
$htmlContent .= "        var b = document.getElementById('tv-submit');\n";
$htmlContent .= "        if (b) { b.disabled = true; b.style.opacity = '0.5'; b.textContent = 'Unavailable'; }\n";
$htmlContent .= "    })\n";
$htmlContent .= "    .catch(function () { /* non-fatal: let the customer try */ });\n";
$htmlContent .= "}\n";
$htmlContent .= "function closeTvModal() {\n";
$htmlContent .= "    var m = document.getElementById('tv-modal');\n";
$htmlContent .= "    if (m) m.style.display = 'none';\n";
$htmlContent .= "}\n";
$htmlContent .= "function tvHideMsg() {\n";
$htmlContent .= "    var el = document.getElementById('tv-msg');\n";
$htmlContent .= "    if (el) { el.style.display = 'none'; el.innerHTML = ''; }\n";
$htmlContent .= "}\n";
$htmlContent .= "function tvShowMsg(text, kind) {\n";
$htmlContent .= "    var el = document.getElementById('tv-msg');\n";
$htmlContent .= "    if (!el) return;\n";
$htmlContent .= "    var bg = '#fef2f2', fg = '#b91c1c', bd = '#fecaca';\n";
$htmlContent .= "    if (kind === 'ok') { bg = '#f0fdf4'; fg = '#166634'; bd = '#bbf7d0'; }\n";
$htmlContent .= "    if (kind === 'info') { bg = '#eff6ff'; fg = '#1e40af'; bd = '#bfdbfe'; }\n";
$htmlContent .= "    el.style.display = 'block';\n";
$htmlContent .= "    el.style.background = bg; el.style.color = fg; el.style.border = '1px solid ' + bd;\n";
$htmlContent .= "    el.innerHTML = text;\n";
$htmlContent .= "}\n";
$htmlContent .= "// Live-formats the MAC while typing and accepts AABBCCDDEEFF or AA-BB-...\n";
$htmlContent .= "function tvFormatMac(input) {\n";
$htmlContent .= "    var v = (input.value || '').toUpperCase().replace(/[^0-9A-F]/g, '').substring(0, 12);\n";
$htmlContent .= "    var out = '';\n";
$htmlContent .= "    for (var i = 0; i < v.length; i++) {\n";
$htmlContent .= "        if (i > 0 && i % 2 === 0) out += ':';\n";
$htmlContent .= "        out += v.charAt(i);\n";
$htmlContent .= "    }\n";
$htmlContent .= "    input.value = out;\n";
$htmlContent .= "}\n";
$htmlContent .= "function tvResetBtn(text) {\n";
$htmlContent .= "    var b = document.getElementById('tv-submit');\n";
$htmlContent .= "    if (!b) return;\n";
$htmlContent .= "    b.disabled = false; b.style.opacity = '1'; b.textContent = text;\n";
$htmlContent .= "}\n";
$htmlContent .= "// Once the money is taken, a failed bind must NEVER send the customer back to\n";
$htmlContent .= "// the payment step - that would charge them twice. From this point the button\n";
$htmlContent .= "// only retries the binding, which the server allows because the package is\n";
$htmlContent .= "// already active.\n";
$htmlContent .= "var tvBindCtx = null;\n";
$htmlContent .= "function tvOfferBindRetry(accountId, mac, deviceName, routerId, note) {\n";
$htmlContent .= "    tvBindCtx = {accountId: accountId, mac: mac, deviceName: deviceName, routerId: routerId};\n";
$htmlContent .= "    tvShowMsg('<strong>Your payment went through - do not pay again.</strong><br>' + note, 'err');\n";
$htmlContent .= "    var b = document.getElementById('tv-submit');\n";
$htmlContent .= "    if (b) {\n";
$htmlContent .= "        b.onclick = tvRetryBind;\n";
$htmlContent .= "        b.disabled = false; b.style.opacity = '1'; b.textContent = 'Retry Connection';\n";
$htmlContent .= "    }\n";
$htmlContent .= "}\n";
$htmlContent .= "function tvRetryBind() {\n";
$htmlContent .= "    if (!tvBindCtx) return;\n";
$htmlContent .= "    tvBindDevice(tvBindCtx.accountId, tvBindCtx.mac, tvBindCtx.deviceName, tvBindCtx.routerId, document.getElementById('tv-submit'));\n";
$htmlContent .= "}\n";
$htmlContent .= "// The success button reuses the submit button, so it needs a real action when\n";
$htmlContent .= "// it becomes 'Done' - it used to be left disabled with no handler, which made it\n";
$htmlContent .= "// look like a working button that did nothing.\n";
$htmlContent .= "function tvFinish() {\n";
$htmlContent .= "    closeTvModal();\n";
$htmlContent .= "    // Clear the per-device fields so a second TV starts from a blank form, but\n";
$htmlContent .= "    // keep the package and phone so buying for another TV is quick.\n";
$htmlContent .= "    var m = document.getElementById('tv-mac');  if (m) m.value = '';\n";
$htmlContent .= "    var n = document.getElementById('tv-name'); if (n) n.value = '';\n";
$htmlContent .= "    tvHideMsg();\n";
$htmlContent .= "    var b = document.getElementById('tv-submit');\n";
$htmlContent .= "    if (b) { b.onclick = submitTvPay; b.disabled = false; b.style.opacity = '1'; b.textContent = 'Bind & Pay'; b.style.background = '#2563eb'; }\n";
$htmlContent .= "}\n";
$htmlContent .= "function submitTvPay() {\n";
$htmlContent .= "    tvHideMsg();\n";
$htmlContent .= "    var mac = (document.getElementById('tv-mac').value || '').toUpperCase().replace(/[^0-9A-F]/g, '');\n";
$htmlContent .= "    if (mac.length !== 12) {\n";
$htmlContent .= "        tvShowMsg('Please enter the full 12-character MAC address, like AA:BB:CC:DD:EE:FF.', 'err');\n";
$htmlContent .= "        return;\n";
$htmlContent .= "    }\n";
$htmlContent .= "    var macFormatted = mac.match(/.{2}/g).join(':');\n";
$htmlContent .= "    var deviceName = (document.getElementById('tv-name').value || '').trim();\n";
$htmlContent .= "    var sel = document.getElementById('tv-plan');\n";
$htmlContent .= "    if (!sel.value) { tvShowMsg('Please choose a package.', 'err'); return; }\n";
$htmlContent .= "    var planId = sel.value;\n";
$htmlContent .= "    var opt = sel.options[sel.selectedIndex];\n";
$htmlContent .= "    var routerId = opt.getAttribute('data-router');\n";
$htmlContent .= "    var price = opt.getAttribute('data-price');\n";
$htmlContent .= "    var phoneRaw = (document.getElementById('tv-phone').value || '').trim();\n";
$htmlContent .= "    if (!phoneRaw) { tvShowMsg('Please enter the M-Pesa number to charge.', 'err'); return; }\n";
$htmlContent .= "    var phone = formatPhoneNumber(phoneRaw);\n";
$htmlContent .= "    if (phone.length !== 12) { tvShowMsg('That phone number does not look right. Use the format 07XXXXXXXX.', 'err'); return; }\n";
$htmlContent .= "    if (!confirm('Pay for this TV?\\n\\nMAC: ' + macFormatted + '\\nPackage: ' + price + '\\nM-Pesa: ' + phone)) return;\n";
$htmlContent .= "    var btn = document.getElementById('tv-submit');\n";
$htmlContent .= "    btn.disabled = true; btn.style.opacity = '0.7'; btn.textContent = 'Sending request...';\n";
$htmlContent .= "    // A TV purchase gets its OWN account reference. Using persistAccountId() here\n";
$htmlContent .= "    // meant the account created for a TV was written into this device's cookie,\n";
$htmlContent .= "    // so the account box above started showing a TV's account number on a phone\n";
$htmlContent .= "    // that had not bought anything for itself.\n";
$htmlContent .= "    var accountId = getCookie('tvAccountId');\n";
$htmlContent .= "    if (!accountId) { accountId = generateAccountId(); }\n";
$htmlContent .= "    setCookie('tvAccountId', accountId, 7);\n";
$htmlContent .= "    fetch('" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=grant', {\n";
$htmlContent .= "        method: 'POST',\n";
$htmlContent .= "        headers: {'Content-Type': 'application/json'},\n";
$htmlContent .= "        body: JSON.stringify({phone_number: phone, plan_id: planId, router_id: routerId, account_id: accountId}),\n";
$htmlContent .= "    })\n";
$htmlContent .= "    .then(function (r) { return r.text().then(safeJson); })\n";
$htmlContent .= "    .then(function (data) {\n";
$htmlContent .= "        if (data.status === 'error') { throw new Error(data.message || 'Payment could not be started.'); }\n";
$htmlContent .= "        if (data.account_id) { accountId = data.account_id; setCookie('tvAccountId', accountId, 7); }\n";
$htmlContent .= "        if (data.redirect_url) { window.open(data.redirect_url, '_blank'); }\n";
$htmlContent .= "        tvShowMsg('Check your phone and enter your M-Pesa PIN. Waiting for confirmation...', 'info');\n";
$htmlContent .= "        tvPollPayment(accountId, macFormatted, deviceName, routerId, btn);\n";
$htmlContent .= "    })\n";
$htmlContent .= "    .catch(function (e) {\n";
$htmlContent .= "        tvShowMsg(e.message, 'err');\n";
$htmlContent .= "        tvResetBtn('Bind & Pay');\n";
$htmlContent .= "    });\n";
$htmlContent .= "}\n";
$htmlContent .= "// Waits for the payment to be confirmed before anything is bound. Binding is\n";
$htmlContent .= "// driven entirely by the server confirming an active recharge, so this poll\n";
$htmlContent .= "// cannot be used to obtain a free bind.\n";
$htmlContent .= "function tvPollPayment(accountId, mac, deviceName, routerId, btn) {\n";
$htmlContent .= "    var tries = 0, maxTries = 40; // ~2 minutes at 3s\n";
$htmlContent .= "    var iv = setInterval(function () {\n";
$htmlContent .= "        tries++;\n";
$htmlContent .= "        if (tries > maxTries) {\n";
$htmlContent .= "            clearInterval(iv);\n";
$htmlContent .= "            // The bind endpoint re-checks the payment server-side, so retrying is\n";
$htmlContent .= "            // safe even when we are unsure whether M-Pesa confirmed.\n";
$htmlContent .= "            tvOfferBindRetry(accountId, mac, deviceName, routerId, 'We did not receive the M-Pesa confirmation in time. If the payment shows on your phone, press Retry Connection - nothing will be charged again.');\n";
$htmlContent .= "            return;\n";
$htmlContent .= "        }\n";
$htmlContent .= "        fetch('" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=verify', {\n";
$htmlContent .= "            method: 'POST',\n";
$htmlContent .= "            headers: {'Content-Type': 'application/json'},\n";
$htmlContent .= "            body: JSON.stringify({account_id: accountId}),\n";
$htmlContent .= "        })\n";
$htmlContent .= "        .then(function (r) { return r.text().then(safeJson); })\n";
$htmlContent .= "        .then(function (data) {\n";
$htmlContent .= "            if (data && data.Resultcode === '3') {\n";
$htmlContent .= "                clearInterval(iv);\n";
$htmlContent .= "                tvBindDevice(accountId, mac, deviceName, routerId, btn);\n";
$htmlContent .= "            }\n";
$htmlContent .= "        })\n";
$htmlContent .= "        .catch(function () { /* keep waiting */ });\n";
$htmlContent .= "    }, 3000);\n";
$htmlContent .= "}\n";
$htmlContent .= "function tvBindDevice(accountId, mac, deviceName, routerId, btn) {\n";
$htmlContent .= "    tvShowMsg('Payment received. Connecting your TV...', 'info');\n";
$htmlContent .= "    var body = new URLSearchParams();\n";
$htmlContent .= "    body.append('action', 'tv_bind');\n";
$htmlContent .= "    body.append('mac', mac);\n";
$htmlContent .= "    body.append('device_name', deviceName);\n";
$htmlContent .= "    body.append('account_id', accountId);\n";
$htmlContent .= "    body.append('router_id', routerId || '');\n";
$htmlContent .= "    // Absolute URL: this page is proxied by the hotspot, so a path relative\n";
$htmlContent .= "    // to the browser address bar would not resolve.\n";
$htmlContent .= "    fetch('" . APP_URL . "/system/plugin/download.php', { method: 'POST', body: body })\n";
$htmlContent .= "    .then(function (r) { return r.json(); })\n";
$htmlContent .= "    .then(function (data) {\n";
$htmlContent .= "        if (data.status === 'success') {\n";
$htmlContent .= "            var extra = '';\n";
$htmlContent .= "            if (data.mac_login_enabled === false) {\n";
$htmlContent .= "                extra = '<br><strong>Note:</strong> device sign-in is not enabled on this network, so the device may still need help connecting. Please contact support.';\n";
$htmlContent .= "            }\n";
$htmlContent .= "            if (data.shape_warning) {\n";
$htmlContent .= "                extra += '<br><span style=\"color:#b45309;\">' + data.shape_warning + '</span>';\n";
$htmlContent .= "            }\n";
$htmlContent .= "            tvShowMsg('<strong>' + data.message + '</strong><br>Device MAC: ' + data.mac + '<br>Package: ' + data.profile + extra, 'ok');\n";
$htmlContent .= "            btn.onclick = tvFinish;\n";
$htmlContent .= "            btn.disabled = false;\n";
$htmlContent .= "            btn.style.opacity = '1';\n";
$htmlContent .= "            btn.textContent = 'Done';\n";
$htmlContent .= "            btn.style.background = '#16a34a';\n";
$htmlContent .= "        } else {\n";
$htmlContent .= "            tvOfferBindRetry(accountId, mac, deviceName, routerId, (data.message || 'We could not connect your TV yet.'));\n";
$htmlContent .= "        }\n";
$htmlContent .= "    })\n";
$htmlContent .= "    .catch(function () {\n";
$htmlContent .= "        tvOfferBindRetry(accountId, mac, deviceName, routerId, 'We could not reach the server to finish the setup.');\n";
$htmlContent .= "    });\n";
$htmlContent .= "}\n";
$htmlContent .= "</script>\n";

$htmlContent .= "</body>\n";

$htmlContent .= "<script>\n";
$htmlContent .= "    document.addEventListener('DOMContentLoaded', function() {\n";
$htmlContent .= "        var accountId = getCookie('accountid');\n";
$htmlContent .= "        if (accountId) {\n";
$htmlContent .= "            document.getElementById('usernameInput').value = accountId;\n";
$htmlContent .= "        }\n";
$htmlContent .= "    });\n";
$htmlContent .= "\n";
$htmlContent .= "</script>\n";

$htmlContent .= "<script>\n";
$htmlContent .= "function fetchData() {\n";
$htmlContent .= "    let domain = '" . APP_URL . "/';\n";
$htmlContent .= "    let siteUrl = domain + \"/index.php?_route=plugin/hotspot_plan\";\n";
$htmlContent .= "    let request = new XMLHttpRequest();\n";
$htmlContent .= "    const routerName = encodeURIComponent(\"$routerName\");\n";
$htmlContent .= "    const dataparams = `routername=\${routerName}`;\n";
$htmlContent .= "    request.open(\"POST\", siteUrl, true);\n";
$htmlContent .= "    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');\n";
$htmlContent .= "    request.onload = () => {\n";
$htmlContent .= "        if (request.readyState === XMLHttpRequest.DONE) {\n";
$htmlContent .= "            if (request.status === 200) {\n";
$htmlContent .= "                let fetchedData = JSON.parse(request.responseText);\n";
$htmlContent .= "                populateCards(fetchedData);\n";
$htmlContent .= "            } else {\n";
$htmlContent .= "                console.log(`Error \${request.status}: \${request.statusText}`);\n";
$htmlContent .= "            }\n";
$htmlContent .= "        }\n";
$htmlContent .= "    };\n";
$htmlContent .= "    request.onerror = () => {\n";
$htmlContent .= "        console.error(\"Network error\");\n";
$htmlContent .= "    };\n";
$htmlContent .= "    request.send(dataparams);\n";
$htmlContent .= "}\n";

$htmlContent .= "function populateCards(data) {\n";
$htmlContent .= "    // Update company name and customer care in real-time from DB\n";
$htmlContent .= "    if (data.company) {\n";
$htmlContent .= "        var companyEl = document.getElementById('company-name');\n";
$htmlContent .= "        if (companyEl) companyEl.textContent = data.company;\n";
$htmlContent .= "    }\n";
$htmlContent .= "    if (data.phone) {\n";
$htmlContent .= "        var careEl = document.getElementById('customer-care');\n";
$htmlContent .= "        if (careEl) careEl.textContent = 'CUSTOMER CARE : ' + data.phone;\n";
$htmlContent .= "    }\n";
$htmlContent .= "    var cardsContainer = document.getElementById('cards-container');\n";
$htmlContent .= "    cardsContainer.innerHTML = ''; // Clear existing content\n";
$htmlContent .= "    data.data.forEach(router => {\n";
$htmlContent .= "        router.plans_hotspot.forEach(item => {\n";
$htmlContent .= "            var cardDiv = document.createElement('div');\n";
$htmlContent .= "            cardDiv.className = 'bg-white border border-black rounded-lg shadow-md overflow-hidden transition duration-300 hover:shadow-lg flex flex-col items-center justify-between mx-auto mb-4 w-40';\n";
$htmlContent .= "            cardDiv.innerHTML = `\n";
$htmlContent .= "                <div class=\"bg-green-500 text-white w-full py-1\">\n";
$htmlContent .= "                    <h2 class=\"text-sm font-medium uppercase text-center\" style=\"font-size: clamp(0.75rem, 1.5vw, 1rem); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;\">\n";
$htmlContent .= "                        \${item.planname}\n";
$htmlContent .= "                    </h2>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "                \${item.popular ? '<div class=\"bg-amber-100 text-amber-800 text-center font-bold\" style=\"font-size:10px;padding:3px 0;letter-spacing:.05em;\">🔥 MOST POPULAR</div>' : ''}\n";
$htmlContent .= "                <div class=\"px-4 py-2 flex-grow\">\n";
$htmlContent .= "                    <p class=\"text-2xl font-bold text-green-600 mb-1\">\n";
$htmlContent .= "                        <span class=\"text-lg font-medium text-black\">\${item.currency}</span>\n";
$htmlContent .= "                        \${item.price}\n";
$htmlContent .= "                    </p>\n";
$htmlContent .= "                    <p class=\"text-sm text-black mb-2\">\n";
$htmlContent .= "                        Valid for \${item.validity} \${item.timelimit}\n";
$htmlContent .= "                    </p>\n";
$htmlContent .= "                    <hr class=\"border-black mb-2\">\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "                <div class=\"px-4 py-2 flex-shrink-0\">\n";
$htmlContent .= "                    <a href=\"#\" class=\"btn-3d btn-3d-black inline-block font-semibold py-2 px-6 rounded-lg text-white text-md\"\n";
$htmlContent .= "                        onclick=\"handlePhoneNumberSubmission('\${item.planId}', '\${item.routerId}', '\${item.price}'); return false;\"\n";
$htmlContent .= "                        data-plan-id=\"\${item.planId}\"\n";
$htmlContent .= "                        data-router-id=\"\${item.routerId}\">\n";
$htmlContent .= "                            Buy\n";
$htmlContent .= "                    </a>\n";
$htmlContent .= "                </div>\n";
$htmlContent .= "            `;\n";
$htmlContent .= "            cardsContainer.appendChild(cardDiv);\n";
$htmlContent .= "        });\n";
$htmlContent .= "    });\n";
$htmlContent .= "    // The same package list feeds the Pay For a TV dropdown, so the two\n";
$htmlContent .= "    // can never disagree about what is on sale.\n";
$htmlContent .= "    var tvSel = document.getElementById('tv-plan');\n";
$htmlContent .= "    if (tvSel) {\n";
$htmlContent .= "        var tvOpts = \"<option value=''>-- Choose a package --</option>\";\n";
$htmlContent .= "        data.data.forEach(function (rt) {\n";
$htmlContent .= "            rt.plans_hotspot.forEach(function (it) {\n";
$htmlContent .= "                tvOpts += \"<option value='\" + it.planId + \"' data-router='\" + it.routerId + \"' data-price='\" + it.price + \"'>\" + it.planname + \" - \" + it.currency + \" \" + it.price + \"</option>\";\n";
$htmlContent .= "            });\n";
$htmlContent .= "        });\n";
$htmlContent .= "        tvSel.innerHTML = tvOpts;\n";
$htmlContent .= "    }\n";
$htmlContent .= "}\n";
$htmlContent .= "fetchData();\n";
$htmlContent .= "</script>\n";

$htmlContent .= "<script src=\"https://cdn.jsdelivr.net/npm/sweetalert2@11\"></script>\n";
$htmlContent .= "<script>\n";
$htmlContent .= "    function formatPhoneNumber(phoneNumber) {\n";
$htmlContent .= "        if (phoneNumber.startsWith('+')) {\n";
$htmlContent .= "            phoneNumber = phoneNumber.substring(1);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        if (phoneNumber.startsWith('0')) {\n";
$htmlContent .= "            phoneNumber = '254' + phoneNumber.substring(1);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        if (phoneNumber.match(/^(7|1)/)) {\n";
$htmlContent .= "            phoneNumber = '254' + phoneNumber;\n";
$htmlContent .= "        }\n";
$htmlContent .= "        return phoneNumber;\n";
$htmlContent .= "    }\n";
$htmlContent .= "\n";
$htmlContent .= "    // Parses the first complete JSON object in a response. Payment gateways\n";
$htmlContent .= "    // can print extra output around the JSON body; without this the request\n";
$htmlContent .= "    // fails with 'Unexpected non-whitespace character after JSON'.\n";
$htmlContent .= "    function safeJson(text) {\n";
$htmlContent .= "        if (!text) return {};\n";
$htmlContent .= "        try { return JSON.parse(text); } catch (e) { }\n";
$htmlContent .= "        var start = text.indexOf('{');\n";
$htmlContent .= "        if (start !== -1) {\n";
$htmlContent .= "            var idx = text.indexOf('}', start);\n";
$htmlContent .= "            while (idx !== -1) {\n";
$htmlContent .= "                try {\n";
$htmlContent .= "                    var parsed = JSON.parse(text.substring(start, idx + 1));\n";
$htmlContent .= "                    console.warn('Ignored trailing output after JSON.');\n";
$htmlContent .= "                    return parsed;\n";
$htmlContent .= "                } catch (e2) {\n";
$htmlContent .= "                    idx = text.indexOf('}', idx + 1);\n";
$htmlContent .= "                }\n";
$htmlContent .= "            }\n";
$htmlContent .= "        }\n";
$htmlContent .= "        console.error('Invalid JSON response:', text);\n";
$htmlContent .= "        return { status: 'error', Resultcode: '2', message: 'Unexpected server response. Please try again.' };\n";
$htmlContent .= "    }\n";
$htmlContent .= "\n";
$htmlContent .= "    function setCookie(name, value, days) {\n";
$htmlContent .= "        var expires = \"\";\n";
$htmlContent .= "        if (days) {\n";
$htmlContent .= "            var date = new Date();\n";
$htmlContent .= "            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));\n";
$htmlContent .= "            expires = \"; expires=\" + date.toUTCString();\n";
$htmlContent .= "        }\n";
$htmlContent .= "        document.cookie = name + \"=\" + (value || \"\") + expires + \"; path=/\";\n";
$htmlContent .= "        // Also store in localStorage as backup\n";
$htmlContent .= "        try {\n";
$htmlContent .= "            localStorage.setItem(name, value);\n";
$htmlContent .= "        } catch (e) {\n";
$htmlContent .= "            console.log('LocalStorage not available');\n";
$htmlContent .= "        }\n";
$htmlContent .= "    }\n";
$htmlContent .= "\n";
$htmlContent .= "    function getCookie(name) {\n";
$htmlContent .= "        var nameEQ = name + \"=\";\n";
$htmlContent .= "        var ca = document.cookie.split(';');\n";
$htmlContent .= "        for (var i = 0; i < ca.length; i++) {\n";
$htmlContent .= "            var c = ca[i];\n";
$htmlContent .= "            while (c.charAt(0) == ' ') c = c.substring(1, c.length);\n";
$htmlContent .= "            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);\n";
$htmlContent .= "        }\n";
$htmlContent .= "        // Try getting from localStorage if cookie not found\n";
$htmlContent .= "        try {\n";
$htmlContent .= "            var localValue = localStorage.getItem(name);\n";
$htmlContent .= "            if (localValue) {\n";
$htmlContent .= "                // Restore cookie from localStorage\n";
$htmlContent .= "                setCookie(name, localValue, 365);\n";
$htmlContent .= "                return localValue;\n";
$htmlContent .= "            }\n";
$htmlContent .= "        } catch (e) {\n";
$htmlContent .= "            console.log('LocalStorage not available');\n";
$htmlContent .= "        }\n";
$htmlContent .= "        return null;\n";
$htmlContent .= "    }\n";
$htmlContent .= "\n";
$htmlContent .= "    function persistAccountId() {\n";
$htmlContent .= "        var accountId = getCookie('accountId');\n";
$htmlContent .= "        if (!accountId) {\n";
$htmlContent .= "            accountId = generateAccountId();\n";
$htmlContent .= "            setCookie('accountId', accountId, 365); // Store for 1 year\n";
$htmlContent .= "        }\n";
$htmlContent .= "        return accountId;\n";
$htmlContent .= "    }\n";
$htmlContent .= "\n";
$htmlContent .= "    document.addEventListener('DOMContentLoaded', function() {\n";
$htmlContent .= "        var accountId = persistAccountId();\n";
$htmlContent .= "        var usernameInput = document.getElementById('usernameInput');\n";
$htmlContent .= "        if (usernameInput) {\n";
$htmlContent .= "            usernameInput.value = accountId;\n";
$htmlContent .= "        }\n";
$htmlContent .= "    });\n";
$htmlContent .= "\n";
$htmlContent .= "    function generateAccountId() {\n";
$htmlContent .= "        // Client value is only a temporary request hint; the server assigns the final unique ID.\n";
$htmlContent .= "        return '' + Math.floor(10000 + Math.random() * 90000);\n";
$htmlContent .= "    }\n";
$htmlContent .= "\n";

$htmlContent .= "var loginTimeout; // Variable to store the timeout ID\n";
$htmlContent .= "function handlePhoneNumberSubmission(planId, routerId, price) {\n";
$htmlContent .= "    // Fallback: if price not passed, read from the card\n";
$htmlContent .= "    if (!price || price === 'undefined') {\n";
$htmlContent .= "        var btn = event.target.closest('a');\n";
$htmlContent .= "        if (btn) {\n";
$htmlContent .= "            var card = btn.closest('.bg-white');\n";
$htmlContent .= "            if (card) {\n";
$htmlContent .= "                var priceEl = card.querySelector('.text-green-600');\n";
$htmlContent .= "                if (priceEl) {\n";
$htmlContent .= "                    var match = priceEl.textContent.match(/[\\d,]+/);\n";
$htmlContent .= "                    if (match) price = match[0].replace(/,/g, '');\n";
$htmlContent .= "                }\n";
$htmlContent .= "            }\n";
$htmlContent .= "        }\n";
$htmlContent .= "    }\n";
$htmlContent .= "    var displayPrice = price || '...';\n";
$htmlContent .= "    var msg = 'You are about to pay <strong style=\"font-size:1.3em;color:#16a34a;\">Ksh: ' + displayPrice + '/=</strong><br>Enter phonenumber below and click pay now to initialize payment';\n";
$htmlContent .= "    swal.fire({\n";
$htmlContent .= "        title: 'Enter Your Mpesa Number',\n";
$htmlContent .= "        html: msg,\n";
$htmlContent .= "        input: 'number',\n";
$htmlContent .= "        inputAttributes: {\n";
$htmlContent .= "            required: 'true'\n";
$htmlContent .= "        },\n";
$htmlContent .= "        inputValidator: function(value) {\n";
$htmlContent .= "            if (value === '') {\n";
$htmlContent .= "                return 'You need to write your phonenumber!';\n";
$htmlContent .= "            }\n";
$htmlContent .= "        },\n";
$htmlContent .= "        showCancelButton: true,\n";
$htmlContent .= "        confirmButtonColor: '#3085d6',\n";
$htmlContent .= "        cancelButtonColor: '#d33',\n";
$htmlContent .= "        confirmButtonText: 'Pay Now',\n";
$htmlContent .= "        showLoaderOnConfirm: true,\n";
$htmlContent .= "        preConfirm: (phoneNumber) => {\n";
$htmlContent .= "            var formattedPhoneNumber = formatPhoneNumber(phoneNumber);\n";
$htmlContent .= "            var accountId = getCookie('accountId');\n";
$htmlContent .= "            if (!accountId) {\n";
$htmlContent .= "                accountId = generateAccountId(); // Generate a new account ID\n";
$htmlContent .= "                setCookie('accountId', accountId, 4); // Set account ID as a cookie\n";
$htmlContent .= "            }\n";
$htmlContent .= "            document.getElementById('usernameInput').value = accountId; // Use account ID as the new username\n";
$htmlContent .= "            console.log(\"Phone number for autofill:\", formattedPhoneNumber);\n";
$htmlContent .= "\n";
$htmlContent .= "            return fetch('" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=grant', {\n";
$htmlContent .= "                method: 'POST',\n";
$htmlContent .= "                headers: {'Content-Type': 'application/json'},\n";
$htmlContent .= "                body: JSON.stringify({phone_number: formattedPhoneNumber, plan_id: planId, router_id: routerId, account_id: accountId}),\n";
$htmlContent .= "            })\n";
$htmlContent .= "            .then(response => {\n";
$htmlContent .= "                if (!response.ok) throw new Error('Network response was not ok');\n";
$htmlContent .= "                return response.text().then(safeJson);\n";
$htmlContent .= "            })\n";
$htmlContent .= "            .then(data => {\n";
$htmlContent .= "                if (data.status === 'error') throw new Error(data.message);\n";
$htmlContent .= "                if (data.account_id) {\n";
$htmlContent .= "                    accountId = data.account_id;\n";
$htmlContent .= "                    setCookie('accountId', accountId, 7);\n";
$htmlContent .= "                    document.getElementById('usernameInput').value = accountId;\n";
$htmlContent .= "                }\n";
$htmlContent .= "                // For redirect-based gateways (e.g. PesaPal), open the payment URL in a new tab\n";
$htmlContent .= "                if (data.redirect_url) {\n";
$htmlContent .= "                    window.open(data.redirect_url, '_blank');\n";
$htmlContent .= "                }\n";
$htmlContent .= "                Swal.fire({\n";
$htmlContent .= "                    icon: 'info',\n";
$htmlContent .= "                    title: 'Processing..',\n";
$htmlContent .= "                    html: `A payment request has been sent to your phone. Please wait while we process your payment.`,\n";
$htmlContent .= "                    showConfirmButton: false,\n";
$htmlContent .= "                    allowOutsideClick: false,\n";
$htmlContent .= "                    didOpen: () => {\n";
$htmlContent .= "                        Swal.showLoading();\n";
$htmlContent .= "                        checkPaymentStatus(formattedPhoneNumber);\n";
$htmlContent .= "                    }\n";
$htmlContent .= "                });\n";
$htmlContent .= "                return formattedPhoneNumber;\n";
$htmlContent .= "            })\n";
$htmlContent .= "            .catch(error => {\n";
$htmlContent .= "                Swal.fire({\n";
$htmlContent .= "                    icon: 'error',\n";
$htmlContent .= "                    title: 'Oops...',\n";
$htmlContent .= "                    text: error.message,\n";
$htmlContent .= "                });\n";
$htmlContent .= "            });\n";
$htmlContent .= "        },\n";
$htmlContent .= "        allowOutsideClick: () => !Swal.isLoading()\n";
$htmlContent .= "    });\n";
$htmlContent .= "}\n";
$htmlContent .= "\n";
$htmlContent .= "function checkPaymentStatus(phoneNumber) {\n";
$htmlContent .= "    let checkInterval = setInterval(() => {\n";
$htmlContent .= "        $.ajax({\n";
$htmlContent .= "            url: '" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=verify',\n";
$htmlContent .= "            method: 'POST',\n";
$htmlContent .= "            data: JSON.stringify({account_id: document.getElementById('usernameInput').value}),\n";
$htmlContent .= "            contentType: 'application/json',\n";
$htmlContent .= "            dataType: 'json',\n";
$htmlContent .= "            success: function(data) {\n";
$htmlContent .= "                console.log('Raw Response:', data); // Debugging\n";
$htmlContent .= "                if (data.Resultcode === '3') { // Success\n";
$htmlContent .= "                    clearInterval(checkInterval);\n";
$htmlContent .= "                    Swal.fire({\n";
$htmlContent .= "                        icon: 'success',\n";
$htmlContent .= "                        title: 'Payment Successful',\n";
$htmlContent .= "                        text: data.Message,\n";
$htmlContent .= "                        showConfirmButton: false\n";
$htmlContent .= "                    });\n";
$htmlContent .= "                    if (loginTimeout) {\n";
$htmlContent .= "                        clearTimeout(loginTimeout);\n";
$htmlContent .= "                    }\n";
$htmlContent .= "                    loginTimeout = setTimeout(function() {\n";
$htmlContent .= "                        document.getElementById('loginForm').submit();\n";
$htmlContent .= "                    }, 2000);\n";
$htmlContent .= "                } else if (data.Resultcode === '2') { // Error\n";
$htmlContent .= "                    clearInterval(checkInterval);\n";
$htmlContent .= "                    let iconType = data.Status === 'danger' ? 'error' : data.Status;\n";
$htmlContent .= "                    Swal.fire({\n";
$htmlContent .= "                        icon: iconType,\n";
$htmlContent .= "                        title: 'Payment Issue',\n";
$htmlContent .= "                        text: data.Message,\n";
$htmlContent .= "                    });\n";
$htmlContent .= "                } else if (data.Resultcode === '1') { // Primary\n";
$htmlContent .= "                    // Continue checking\n";
$htmlContent .= "                }\n";
$htmlContent .= "            },\n";
$htmlContent .= "            error: function(xhr, textStatus, errorThrown) {\n";
$htmlContent .= "                console.log('Error: ' + errorThrown);\n";
$htmlContent .= "            }\n";
$htmlContent .= "        });\n";
$htmlContent .= "    }, 2000);\n";
$htmlContent .= "\n";
$htmlContent .= "    setTimeout(() => {\n";
$htmlContent .= "        clearInterval(checkInterval);\n";
$htmlContent .= "        Swal.fire({\n";
$htmlContent .= "            icon: 'warning',\n";
$htmlContent .= "            title: 'Timeout',\n";
$htmlContent .= "            text: 'Payment verification timed out. Please try again.',\n";
$htmlContent .= "        });\n";
$htmlContent .= "    }, 60000); // Stop checking after 60 seconds\n";
$htmlContent .= "}\n";
$htmlContent .= "</script>\n";

$htmlContent .= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js\"></script>\n";

$htmlContent .= "<script>\n";
$htmlContent .= "document.addEventListener('DOMContentLoaded', function() {\n";
$htmlContent .= "     // Ensure the button is correctly targeted by its ID.\n";
$htmlContent .= "     var submitBtn = document.getElementById('submitBtn');\n";
$htmlContent .= "     \n";
$htmlContent .= "     // Add a click event listener to the \"Login Now\" button.\n";
$htmlContent .= "     submitBtn.addEventListener('click', function(event) {\n";
$htmlContent .= "         event.preventDefault(); // Prevent the default button action.\n";
$htmlContent .= "         \n";
$htmlContent .= "         // Optional: Log to console for debugging purposes.\n";
$htmlContent .= "         console.log(\"Login Now button clicked.\");\n";
$htmlContent .= " \n";
$htmlContent .= "         // Direct form submission, bypassing the doLogin function for simplicity.\n";
$htmlContent .= "         var form = document.getElementById('loginForm');\n";
$htmlContent .= "         form.submit(); // Submit the form directly.\n";
$htmlContent .= "     });\n";
$htmlContent .= "});\n";
$htmlContent .= "</script>\n";

$htmlContent .= "<script>\n";
$htmlContent .= "document.addEventListener('DOMContentLoaded', function() {\n";
$htmlContent .= "     // Ensure the top button is correctly targeted by its ID.\n";
$htmlContent .= "     var submitBtnTop = document.getElementById('submitBtnTop');\n";
$htmlContent .= "     \n";
$htmlContent .= "     // Add a click event listener to the top \"Connect\" button.\n";
$htmlContent .= "     submitBtnTop.addEventListener('click', function(event) {\n";
$htmlContent .= "         event.preventDefault(); // Prevent the default button action.\n";
$htmlContent .= "         \n";
$htmlContent .= "         // Optional: Log to console for debugging purposes.\n";
$htmlContent .= "         console.log(\"Top Connect button clicked.\");\n";
$htmlContent .= " \n";
$htmlContent .= "         // Direct form submission, bypassing the doLogin function for simplicity.\n";
$htmlContent .= "         var form = document.getElementById('loginFormTop');\n";
$htmlContent .= "         form.submit(); // Submit the form directly.\n";
$htmlContent .= "     });\n";
$htmlContent .= "});\n";
$htmlContent .= "</script>\n";

$htmlContent .= "<script>\n";
$htmlContent .= "var loginTimeout; // Variable to store the timeout ID\n";
$htmlContent .= "function redeemVoucher() {\n";
$htmlContent .= "    Swal.fire({\n";
$htmlContent .= "        title: 'Redeem Voucher',\n";
$htmlContent .= "        input: 'text',\n";
$htmlContent .= "        inputPlaceholder: 'Enter voucher code',\n";
$htmlContent .= "        inputValidator: function(value) {\n";
$htmlContent .= "            if (!value) {\n";
$htmlContent .= "                return 'You need to enter a voucher code!';\n";
$htmlContent .= "            }\n";
$htmlContent .= "        },\n";
$htmlContent .= "        confirmButtonColor: '#3085d6',\n";
$htmlContent .= "        cancelButtonColor: '#d33',\n";
$htmlContent .= "        confirmButtonText: 'Redeem',\n";
$htmlContent .= "        showLoaderOnConfirm: true,\n";
$htmlContent .= "        preConfirm: (voucherCode) => {\n";
$htmlContent .= "            var accountId = getCookie('accountId');\n";
$htmlContent .= "            if (!accountId) {\n";
$htmlContent .= "                accountId = generateAccountId();\n";
$htmlContent .= "                setCookie('accountId', accountId, 7);\n";
$htmlContent .= "            }\n";
$htmlContent .= "            return fetch('" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=voucher', {\n";
$htmlContent .= "                method: 'POST',\n";
$htmlContent .= "                headers: {'Content-Type': 'application/json'},\n";
$htmlContent .= "                body: JSON.stringify({voucher_code: voucherCode, account_id: accountId}),\n";
$htmlContent .= "            })\n";
$htmlContent .= "            .then(response => {\n";
$htmlContent .= "                if (!response.ok) throw new Error('Network response was not ok');\n";
$htmlContent .= "                return response.text().then(safeJson);\n";
$htmlContent .= "            })\n";
$htmlContent .= "            .then(data => {\n";
$htmlContent .= "                if (data.status === 'error') throw new Error(data.message);\n";
$htmlContent .= "                return data;\n";
$htmlContent .= "            });\n";
$htmlContent .= "        },\n";
$htmlContent .= "        allowOutsideClick: () => !Swal.isLoading()\n";
$htmlContent .= "    }).then((result) => {\n";
$htmlContent .= "        if (result.isConfirmed) {\n";
$htmlContent .= "            Swal.fire({\n";
$htmlContent .= "                icon: 'success',\n";
$htmlContent .= "                title: 'Voucher Redeemed',\n";
$htmlContent .= "                text: result.value.message,\n";
$htmlContent .= "                showConfirmButton: false,\n";
$htmlContent .= "                allowOutsideClick: false,\n";
$htmlContent .= "                didOpen: () => {\n";
$htmlContent .= "                    Swal.showLoading();\n";
$htmlContent .= "                    var username = result.value.username;\n";
$htmlContent .= "                    console.log('Received username from server:', username);\n";
$htmlContent .= "                    var usernameInput = document.querySelector('input[name=\"username\"]');\n";
$htmlContent .= "                    if (usernameInput) {\n";
$htmlContent .= "                        console.log('Found username input element.');\n";
$htmlContent .= "                        usernameInput.value = username;\n";
$htmlContent .= "                        loginTimeout = setTimeout(function() {\n";
$htmlContent .= "                            var loginForm = document.getElementById('loginForm');\n";
$htmlContent .= "                            if (loginForm) {\n";
$htmlContent .= "                                loginForm.submit();\n";
$htmlContent .= "                            } else {\n";
$htmlContent .= "                                console.error('Login form not found.');\n";
$htmlContent .= "                                Swal.fire({\n";
$htmlContent .= "                                    icon: 'error',\n";
$htmlContent .= "                                    title: 'Error',\n";
$htmlContent .= "                                    text: 'Login form not found. Please try again.',\n";
$htmlContent .= "                                });\n";
$htmlContent .= "                            }\n";
$htmlContent .= "                        }, 2000);\n";
$htmlContent .= "                    } else {\n";
$htmlContent .= "                        console.error('Username input element not found.');\n";
$htmlContent .= "                        Swal.fire({\n";
$htmlContent .= "                            icon: 'error',\n";
$htmlContent .= "                            title: 'Error',\n";
$htmlContent .= "                            text: 'Username input not found. Please try again.',\n";
$htmlContent .= "                        });\n";
$htmlContent .= "                    }\n";
$htmlContent .= "                }\n";
$htmlContent .= "            });\n";
$htmlContent .= "        }\n";
$htmlContent .= "    }).catch(error => {\n";
$htmlContent .= "        Swal.fire({\n";
$htmlContent .= "            icon: 'error',\n";
$htmlContent .= "            title: 'Oops...',\n";
$htmlContent .= "            text: error.message,\n";
$htmlContent .= "        });\n";
$htmlContent .= "    });\n";
$htmlContent .= "}\n";
$htmlContent .= "</script>\n";

$htmlContent .= "<script>\n";
$htmlContent .= "function reconnectWithNumber() {\n";
$htmlContent .= "    Swal.fire({\n";
$htmlContent .= "        title: 'Reconnect with Your Phone Number',\n";
$htmlContent .= "        input: 'tel',\n";
$htmlContent .= "        inputPlaceholder: 'e.g. 0712 345 678',\n";
$htmlContent .= "        text: 'Enter the M-Pesa number you used when buying your package.',\n";
$htmlContent .= "        inputValidator: function(value) {\n";
$htmlContent .= "            if (!value) return 'Please enter your phone number!';\n";
$htmlContent .= "            var cleaned = value.replace(/[\\s\\-\\(\\)]/g, '');\n";
$htmlContent .= "            if (cleaned.length < 9) return 'Please enter a valid phone number';\n";
$htmlContent .= "        },\n";
$htmlContent .= "        confirmButtonColor: '#3085d6',\n";
$htmlContent .= "        cancelButtonColor: '#d33',\n";
$htmlContent .= "        confirmButtonText: 'Reconnect',\n";
$htmlContent .= "        showLoaderOnConfirm: true,\n";
$htmlContent .= "        preConfirm: (rawPhone) => {\n";
$htmlContent .= "            var macAddr = '$(mac)';\n";
$htmlContent .= "            var formData = new FormData();\n";
$htmlContent .= "            formData.append('action', 'reconnect_phone');\n";
$htmlContent .= "            formData.append('phone', rawPhone);\n";
$htmlContent .= "            formData.append('mac', macAddr);\n";
$htmlContent .= "            return fetch('" . APP_URL . "/system/plugin/download.php', {\n";
$htmlContent .= "                method: 'POST',\n";
$htmlContent .= "                body: formData,\n";
$htmlContent .= "            }).then(function(r) { return r.text(); }).then(safeJson)\n";
$htmlContent .= "            .then(function(data) {\n";
$htmlContent .= "                if (data.Resultcode !== '3') throw new Error(data.Message || 'Reconnection failed');\n";
$htmlContent .= "                return data;\n";
$htmlContent .= "            });\n";
$htmlContent .= "        },\n";
$htmlContent .= "        allowOutsideClick: () => !Swal.isLoading()\n";
$htmlContent .= "    }).then(function(result) {\n";
$htmlContent .= "        if (result.isConfirmed && result.value.username) {\n";
$htmlContent .= "            Swal.fire({\n";
$htmlContent .= "                icon: 'success',\n";
$htmlContent .= "                title: 'Welcome Back!',\n";
$htmlContent .= "                text: result.value.Message,\n";
$htmlContent .= "                showConfirmButton: false,\n";
$htmlContent .= "                timer: 2000\n";
$htmlContent .= "            });\n";
$htmlContent .= "            var username = result.value.username;\n";
$htmlContent .= "            var inputs = document.querySelectorAll('input[name=\"username\"]');\n";
$htmlContent .= "            inputs.forEach(function(inp) { inp.value = username; });\n";
$htmlContent .= "            setCookie('accountId', username, 7);\n";
$htmlContent .= "            setTimeout(function() {\n";
$htmlContent .= "                var form = document.getElementById('loginForm');\n";
$htmlContent .= "                if (form) form.submit();\n";
$htmlContent .= "            }, 2500);\n";
$htmlContent .= "        }\n";
$htmlContent .= "    }).catch(function(err) {\n";
$htmlContent .= "        Swal.fire({ icon: 'error', title: 'Failed', text: err.message });\n";
$htmlContent .= "    });\n";
$htmlContent .= "}\n";
$htmlContent .= "\n";
$htmlContent .= "function reconnectWithMpesa() {\n";
$htmlContent .= "    Swal.fire({\n";
$htmlContent .= "        title: 'Reconnect with MPesa',\n";
$htmlContent .= "        input: 'text',\n";
$htmlContent .= "        inputPlaceholder: 'Enter MPesa Transaction Code',\n";
$htmlContent .= "        inputValidator: function(value) {\n";
$htmlContent .= "            if (!value) {\n";
$htmlContent .= "                return 'You need to enter an MPesa code!';\n";
$htmlContent .= "            }\n";
$htmlContent .= "            // Accept only the code: e.g. UG6PQAGHDN\n";
$htmlContent .= "            if (!/^[A-Z0-9]{8,12}$/i.test(value.trim())) {\n";
$htmlContent .= "                return 'Enter the transaction code only (e.g. UG6PQAGHDN)';\n";
$htmlContent .= "            }\n";
$htmlContent .= "        },\n";
$htmlContent .= "        confirmButtonColor: '#3085d6',\n";
$htmlContent .= "        cancelButtonColor: '#d33',\n";
$htmlContent .= "        confirmButtonText: 'Reconnect',\n";
$htmlContent .= "        showLoaderOnConfirm: true,\n";
$htmlContent .= "        preConfirm: (mpesaCode) => {\n";
$htmlContent .= "            // Get device info from the page (MikroTik substitutes these)\n";
$htmlContent .= "            var macAddr = '$(mac)';\n";
$htmlContent .= "            var ipAddr = '$(ip)';\n";
$htmlContent .= "            var accountId = getCookie('accountId') || generateAccountId();\n";
$htmlContent .= "            setCookie('accountId', accountId, 7);\n";
$htmlContent .= "            \n";
$htmlContent .= "            // POST to the server — CORS headers are set\n";
$htmlContent .= "            // Posts to the CreateHotspotuser plugin \u2014 the same endpoint this page\n";
$htmlContent .= "            // already uses for Buy / Verify / Voucher. That handler is the one that\n";
$htmlContent .= "            // checks the code against THIS device's MAC, so one code cannot be\n";
$htmlContent .= "            // reused to switch a second device on.\n";
$htmlContent .= "            return fetch('" . APP_URL . "/index.php?_route=plugin/CreateHotspotuser&type=mpesa_reconnect', {\n";
$htmlContent .= "                method: 'POST',\n";
$htmlContent .= "                headers: {'Content-Type': 'application/json'},\n";
$htmlContent .= "                body: JSON.stringify({mpesa_code: mpesaCode, mac: macAddr, ip: ipAddr, account_id: accountId}),\n";
$htmlContent .= "            })\n";
$htmlContent .= "            .then(response => {\n";
$htmlContent .= "                if (!response.ok) throw new Error('Network response was not ok');\n";
$htmlContent .= "                return response.text().then(safeJson);\n";
$htmlContent .= "            })\n";
$htmlContent .= "            .then(data => {\n";
$htmlContent .= "                console.log('MPesa reconnect response:', data);\n";
$htmlContent .= "                if (data.Resultcode === '2') {\n";
$htmlContent .= "                    throw new Error(data.Message || 'Reconnection failed');\n";
$htmlContent .= "                }\n";
$htmlContent .= "                if (data.Resultcode !== '3') {\n";
$htmlContent .= "                    throw new Error(data.Message || 'Unexpected response');\n";
$htmlContent .= "                }\n";
$htmlContent .= "                return data;\n";
$htmlContent .= "            });\n";
$htmlContent .= "        },\n";
$htmlContent .= "        allowOutsideClick: () => !Swal.isLoading()\n";
$htmlContent .= "    }).then((result) => {\n";
$htmlContent .= "        if (result.isConfirmed) {\n";
$htmlContent .= "            Swal.fire({\n";
$htmlContent .= "                icon: 'success',\n";
$htmlContent .= "                title: 'Reconnection Successful',\n";
$htmlContent .= "                text: result.value.Message,\n";
$htmlContent .= "                showConfirmButton: false,\n";
$htmlContent .= "                allowOutsideClick: false,\n";
$htmlContent .= "                didOpen: () => {\n";
$htmlContent .= "                    Swal.showLoading();\n";
$htmlContent .= "                    var username = result.value.username;\n";
$htmlContent .= "                    console.log('Received username from MPesa reconnect:', username);\n";
$htmlContent .= "                    \n";
$htmlContent .= "                    if (!username || username === '') {\n";
$htmlContent .= "                        console.error('No username received from server');\n";
$htmlContent .= "                        Swal.fire({\n";
$htmlContent .= "                            icon: 'error',\n";
$htmlContent .= "                            title: 'Error',\n";
$htmlContent .= "                            text: 'No username received from server. Please contact support.',\n";
$htmlContent .= "                        });\n";
$htmlContent .= "                        return;\n";
$htmlContent .= "                    }\n";
$htmlContent .= "                    \n";
$htmlContent .= "                    // Update all possible username inputs\n";
$htmlContent .= "                    var usernameInputs = document.querySelectorAll('input[name=\"username\"]');\n";
$htmlContent .= "                    var accountInput = document.getElementById('usernameInput');\n";
$htmlContent .= "                    \n";
$htmlContent .= "                    if (usernameInputs.length > 0) {\n";
$htmlContent .= "                        usernameInputs.forEach(function(input) {\n";
$htmlContent .= "                            input.value = username;\n";
$htmlContent .= "                            console.log('Updated username input:', input);\n";
$htmlContent .= "                        });\n";
$htmlContent .= "                    }\n";
$htmlContent .= "                    \n";
$htmlContent .= "                    if (accountInput) {\n";
$htmlContent .= "                        accountInput.value = username;\n";
$htmlContent .= "                        console.log('Updated account input:', accountInput);\n";
$htmlContent .= "                    }\n";
$htmlContent .= "                    \n";
$htmlContent .= "                    // Store the username in cookie for persistence\n";
$htmlContent .= "                    setCookie('accountId', username, 7);\n";
$htmlContent .= "                    \n";
$htmlContent .= "                    setTimeout(function() {\n";
$htmlContent .= "                        console.log('Attempting to login with username:', username);\n";
$htmlContent .= "                        // Try to submit the quick login form first (loginForm)\n";
$htmlContent .= "                        var loginForm = document.getElementById('loginForm');\n";
$htmlContent .= "                        if (loginForm) {\n";
$htmlContent .= "                            console.log('Found loginForm, submitting...');\n";
$htmlContent .= "                            console.log('Form action:', loginForm.action);\n";
$htmlContent .= "                            console.log('Username input value:', loginForm.querySelector('input[name=\"username\"]').value);\n";
$htmlContent .= "                            loginForm.submit();\n";
$htmlContent .= "                        } else {\n";
$htmlContent .= "                            console.log('loginForm not found, trying manual login form');\n";
$htmlContent .= "                            // Fallback to manual login form\n";
$htmlContent .= "                            var manualLoginForm = document.querySelector('form.login');\n";
$htmlContent .= "                            if (manualLoginForm) {\n";
$htmlContent .= "                                console.log('Found manual login form, submitting...');\n";
$htmlContent .= "                                console.log('Form action:', manualLoginForm.action);\n";
$htmlContent .= "                                var usernameField = manualLoginForm.querySelector('input[name=\"username\"]');\n";
$htmlContent .= "                                var passwordField = manualLoginForm.querySelector('input[name=\"password\"]');\n";
$htmlContent .= "                                if (usernameField) {\n";
$htmlContent .= "                                    console.log('Username field value:', usernameField.value);\n";
$htmlContent .= "                                }\n";
$htmlContent .= "                                if (passwordField && passwordField.type === 'password') {\n";
$htmlContent .= "                                    passwordField.value = '1234'; // Set default password for reconnection\n";
$htmlContent .= "                                    console.log('Set password field for manual form');\n";
$htmlContent .= "                                }\n";
$htmlContent .= "                                manualLoginForm.submit();\n";
$htmlContent .= "                            } else {\n";
$htmlContent .= "                                console.error('No login form found.');\n";
$htmlContent .= "                                Swal.fire({\n";
$htmlContent .= "                                    icon: 'error',\n";
$htmlContent .= "                                    title: 'Error',\n";
$htmlContent .= "                                    text: 'Login form not found. Please manually enter the username: ' + username + ' and login.',\n";
$htmlContent .= "                                });\n";
$htmlContent .= "                            }\n";
$htmlContent .= "                        }\n";
$htmlContent .= "                    }, 2000);\n";
$htmlContent .= "                }\n";
$htmlContent .= "            });\n";
$htmlContent .= "        }\n";
$htmlContent .= "    }).catch(error => {\n";
$htmlContent .= "        Swal.fire({\n";
$htmlContent .= "            icon: 'error',\n";
$htmlContent .= "            title: 'Oops...',\n";
$htmlContent .= "            text: error.message,\n";
$htmlContent .= "        });\n";
$htmlContent .= "    });\n";
$htmlContent .= "}\n";
$htmlContent .= "</script>\n";

$htmlContent .= "<script>\n";
$htmlContent .= "function loadHotspotAds() {\n";
$htmlContent .= "    fetch('" . APP_URL . "/index.php?_route=plugin/hotspot_ads/get_active')\n";
$htmlContent .= "        .then(function(r) { return r.text(); }).then(safeJson)\n";
$htmlContent .= "        .then(function(ads) {\n";
$htmlContent .= "            var container = document.getElementById('hotspot-ads-container');\n";
$htmlContent .= "            if (!container || !ads.length) return;\n";
$htmlContent .= "            container.innerHTML = '';\n";
$htmlContent .= "            ads.forEach(function(ad, index) {\n";
$htmlContent .= "                var el = document.createElement('div');\n";
$htmlContent .= "                el.className = 'mb-2 text-center';\n";
$htmlContent .= "                if (ad.type === 'text') {\n";
$htmlContent .= "                    el.innerHTML = '<p class=\"text-white text-sm font-medium\">' + ad.content + '</p>';\n";
$htmlContent .= "                } else if (ad.type === 'video') {\n";
$htmlContent .= "                    var vid_id = 'ad-video-' + index;\n";
$htmlContent .= "                    var btn_id = 'ad-unmute-' + index;\n";
$htmlContent .= "                    el.style.cssText = 'position:relative;display:inline-block;';\n";
$htmlContent .= "                    el.innerHTML =\n";
$htmlContent .= "                        '<video id=\"' + vid_id + '\" src=\"' + ad.content + '\" autoplay muted loop playsinline class=\"mx-auto max-w-full rounded-lg\" style=\"display:block;\"></video>' +\n";
$htmlContent .= "                        '<button id=\"' + btn_id + '\" onclick=\"(function(){' +\n";
$htmlContent .= "                            'var v=document.getElementById(\\'' + vid_id + '\\');' +\n";
$htmlContent .= "                            'var b=document.getElementById(\\'' + btn_id + '\\');' +\n";
$htmlContent .= "                            'v.muted=!v.muted;' +\n";
$htmlContent .= "                            'b.textContent=v.muted?\\'🔇 Tap for sound\\':\\'🔊 Sound on\\';' +\n";
$htmlContent .= "                        '})()\" style=\"position:absolute;bottom:8px;right:8px;background:rgba(0,0,0,0.55);color:#fff;border:none;border-radius:20px;padding:4px 12px;font-size:12px;cursor:pointer;\">🔇 Tap for sound</button>';\n";
$htmlContent .= "                } else {\n";
$htmlContent .= "                    el.innerHTML = '<img src=\"' + ad.content + '\" alt=\"' + ad.title + '\" class=\"mx-auto max-w-full rounded-lg\">';\n";
$htmlContent .= "                }\n";
$htmlContent .= "                container.appendChild(el);\n";
$htmlContent .= "            });\n";
$htmlContent .= "        })\n";
$htmlContent .= "        .catch(function(e) { console.log('Ads load error:', e); });\n";
$htmlContent .= "}\n";
$htmlContent .= "loadHotspotAds();\n";
$htmlContent .= "</script>\n";

// Google Analytics Tracking
$htmlContent .= "<script async src='https://www.googletagmanager.com/gtag/js?id=G-MKTVFMD7HE'></script>\n";
$htmlContent .= "<script>\n";
$htmlContent .= "  window.dataLayer = window.dataLayer || [];\n";
$htmlContent .= "  function gtag(){dataLayer.push(arguments);}\n";
$htmlContent .= "  gtag('js', new Date());\n";
$htmlContent .= "  gtag('config', 'G-MKTVFMD7HE');\n";
$htmlContent .= "</script>\n";

$htmlContent .= "</html>\n";

$mysqli->close();
// Check if the download parameter is set
if (isset($_GET['download']) && $_GET['download'] == '1') {
   // Prepare the HTML content for download
   // ... build your HTML content ...

   // Specify the filename for the download
   $filename = "login.html";

   // Send headers to force download
   header('Content-Type: application/octet-stream');
   header('Content-Disposition: attachment; filename='.basename($filename));
   header('Expires: 0');
   header('Cache-Control: must-revalidate');
   header('Pragma: public');
   header('Content-Length: ' . strlen($htmlContent));

   // Output the content
   echo $htmlContent;

   // Prevent any further output
   exit;
}

// Regular page content goes here
// ... HTML and PHP code to display the page ...



