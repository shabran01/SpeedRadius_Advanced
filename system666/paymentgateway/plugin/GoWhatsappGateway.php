<?php

register_menu("Go WhatsApp Gateway", true, "goWhatsappGateway", 'AFTER_SETTINGS', 'glyphicon glyphicon-comment', '', '', ['Admin', 'SuperAdmin']);
register_menu("Go WhatsApp Logs", true, "goWhatsappGateway_logs", 'AFTER_SETTINGS', 'glyphicon glyphicon-list-alt', '', '', ['Admin', 'SuperAdmin']);

register_hook('send_whatsapp', 'goWhatsappGateway_hook_send_whatsapp', 1);

function goWhatsappGateway()
{
    global $ui, $config, $admin;
    _admin();
    $path = goWhatsappGateway_getPath();

    if (empty($config['go_whatsapp_gateway_url'])) {
        r2(U . 'plugin/goWhatsappGateway_config', 'e', 'Please configure first');
    }

    // Fetch live device list from GOWA v8 API
    $sessions = [];
    $resp = Http::getData(rtrim($config['go_whatsapp_gateway_url'], '/') . '/devices', goWhatsappGateway_getAuthHeaders());
    $respJson = json_decode($resp, true);
    if (!empty($respJson['data']) && is_array($respJson['data'])) {
        foreach ($respJson['data'] as $dev) {
            $sessions[] = $dev['device_id'] ?? $dev['id'] ?? '';
        }
    } else {
        // Fallback: read local .session files
        if (file_exists($path)) {
            foreach (scandir($path) as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) == 'session') {
                    $sessions[] = str_replace('.session', '', $file);
                }
            }
        }
    }

    $ui->assign('sessions', $sessions);
    $ui->assign('_title', 'Go WhatsApp Gateway');
    $ui->assign('_system_menu', 'plugin/goWhatsappGateway');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('goWhatsappGateway.tpl');
}

function goWhatsappGateway_config()
{
    global $ui;
    _admin();

    if (!empty(_post('go_whatsapp_gateway_url')) || !empty(_post('go_whatsapp_username')) || !empty(_post('go_whatsapp_password')) || !empty(_post('go_whatsapp_max_per_hour'))) {
        // Save gateway URL
        $d = ORM::for_table('tbl_appconfig')->where('setting', 'go_whatsapp_gateway_url')->find_one();
        if ($d) {
            $d->value = _post('go_whatsapp_gateway_url');
            $d->save();
        } else {
            $d = ORM::for_table('tbl_appconfig')->create();
            $d->setting = 'go_whatsapp_gateway_url';
            $d->value = _post('go_whatsapp_gateway_url');
            $d->save();
        }

        // Save username
        $d = ORM::for_table('tbl_appconfig')->where('setting', 'go_whatsapp_username')->find_one();
        if ($d) {
            $d->value = _post('go_whatsapp_username');
            $d->save();
        } else {
            $d = ORM::for_table('tbl_appconfig')->create();
            $d->setting = 'go_whatsapp_username';
            $d->value = _post('go_whatsapp_username');
            $d->save();
        }

        // Save password
        $d = ORM::for_table('tbl_appconfig')->where('setting', 'go_whatsapp_password')->find_one();
        if ($d) {
            $d->value = _post('go_whatsapp_password');
            $d->save();
        } else {
            $d = ORM::for_table('tbl_appconfig')->create();
            $d->setting = 'go_whatsapp_password';
            $d->value = _post('go_whatsapp_password');
            $d->save();
        }

        // Save rate limit settings
        $rateLimitSettings = [
            'go_whatsapp_max_per_hour' => 10,
            'go_whatsapp_max_per_day' => 50,
            'go_whatsapp_max_overall_per_hour' => 100,
            'go_whatsapp_max_overall_per_day' => 500,
            'go_whatsapp_min_delay' => 5
        ];

        foreach ($rateLimitSettings as $setting => $default) {
            $value = _post($setting) ?: $default;
            $d = ORM::for_table('tbl_appconfig')->where('setting', $setting)->find_one();
            if ($d) {
                $d->value = $value;
                $d->save();
            } else {
                $d = ORM::for_table('tbl_appconfig')->create();
                $d->setting = $setting;
                $d->value = $value;
                $d->save();
            }
        }

        r2(U . 'plugin/goWhatsappGateway_config', 's', 'Configuration saved');
    }
    $ui->assign('_title', 'Go WhatsApp Gateway Configuration');
    $ui->assign('_system_menu', 'plugin/goWhatsappGateway');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('menu', 'config');
    $ui->display('goWhatsappGateway.tpl');
}

function goWhatsappGateway_login()
{
    global $ui, $config;
    _admin();

    $session = alphanumeric(_get('s'));
    if (empty($session)) {
        r2(U . 'plugin/goWhatsappGateway', 'e', 'Session not found');
    }

    // Determine if using QR or pairing code based on query parameter
    $usePairingCode = isset($_GET['use_pairing_code']) && $_GET['use_pairing_code'] == '1';

    if ($usePairingCode) {
        // Check if phone number is provided for pairing
        $phone = _get('phone');
        if (empty($phone)) {
            $message = '<div class="alert alert-warning">Phone number required for pairing code login</div>';
        } else {
            $result = goWhatsappGateway_loginWithCodeApi($phone, $session);
        }
    } else {
        $result = goWhatsappGateway_loginApi($session);
    }

    if (strlen($result)) {
        if (substr($result, 0, 1) == '{') {
            $resultJson = json_decode($result, true);

            if ($resultJson['code'] == 'SUCCESS') {
                // Handle QR Code
                if (!empty($resultJson['results']['qr_link'])) {
                    $qrImage = $resultJson['results']['qr_link'];
                    
                    // Try to fetch QR code directly and embed as base64
                    $base64Qr = goWhatsappGateway_fetchQrAsBase64($qrImage);
                    
                    if ($base64Qr) {
                        // Use base64 embedded image
                        $qrSrc = 'data:image/png;base64,' . $base64Qr;
                        $fallbackLink = '';
                    } else {
                        // Fallback to proxy method
                        $qrSrc = $_url . 'plugin/goWhatsappGateway_qr&url=' . urlencode($qrImage);
                        $fallbackLink = "<div id='qr-error' style='display:none; color:#ff5555; padding:10px; border:1px solid #ff5555; border-radius:5px; margin:10px 0;'>";
                        $fallbackLink .= "<i class='glyphicon glyphicon-warning-sign'></i> QR Code failed to load. <a href='$qrImage' target='_blank'>Click here to open in new tab</a>";
                        $fallbackLink .= "</div>";
                    }
                    
                    $message = "<div style='text-align:center'>";
                    $message .= "<img src='$qrSrc' alt='WhatsApp QR Code' style='max-width:300px;margin:20px auto' crossorigin='anonymous' onerror=\"this.style.display='none'; document.getElementById('qr-error').style.display='block';\"><br>";
                    $message .= $fallbackLink;
                    $message .= "<p style='color:#128C7E;font-weight:600'>Scan this QR code with WhatsApp on your phone</p>";
                    if (!empty($resultJson['results']['qr_duration'])) {
                        $message .= "<p>QR Code expires in: {$resultJson['results']['qr_duration']} seconds</p>";
                    }
                    $message .= "</div>";
                }
                // Handle Pair Code
                else if (!empty($resultJson['results']['pair_code'])) {
                    $pairCode = $resultJson['results']['pair_code'];
                    $message = "<div style='text-align:center'>";
                    $message .= "<h1 style='font-size:32px;letter-spacing:2px;color:#128C7E'>$pairCode</h1>";
                    $message .= "<p>Enter this code in WhatsApp > Linked Devices > Link a Device</p>";
                    $message .= "</div>";
                }
                else {
                    $message = '<div class="alert alert-info">' . ($resultJson['message'] ?? 'Login initiated successfully') . '</div>';
                }
            } else {
                $message = '<div class="alert alert-danger">' . ($resultJson['message'] ?? 'Login failed') . '</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Invalid response from server</div>';
        }
    } else {
        $message = '<div class="alert alert-warning">No response from Go WhatsApp server</div>';
    }

    $ui->assign('message', $message);
    $ui->assign('session', $session);
    $ui->assign('usePairingCode', $usePairingCode);
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('menu', 'login');
    $ui->display('goWhatsappGateway.tpl');
}

function goWhatsappGateway_addSession()
{
    global $config;
    _admin();
    $path = goWhatsappGateway_getPath();
    $sessionName = alphanumeric(_post("sessionname"));
    if (empty($sessionName)) {
        r2(U . 'plugin/goWhatsappGateway', 'e', 'Session name not found');
    }
    if (file_exists("$path$sessionName.session")) {
        r2(U . 'plugin/goWhatsappGateway', 'e', 'Session already exists');
    }

    // Register device with GOWA server — v8 API: POST /devices
    if (!empty($config['go_whatsapp_gateway_url'])) {
        $createUrl = rtrim($config['go_whatsapp_gateway_url'], '/') . '/devices';
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $createUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode(['device_id' => $sessionName]),
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => array_merge(goWhatsappGateway_getAuthHeaders(), ['Content-Type: application/json']),
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    // Create local session file
    $sessionData = ['created' => date('Y-m-d H:i:s'), 'device_id' => $sessionName];
    file_put_contents("$path$sessionName.session", json_encode($sessionData));

    if (file_exists("$path$sessionName.session")) {
        r2(U . 'plugin/goWhatsappGateway', 's', 'Session Added');
    } else {
        r2(U . 'plugin/goWhatsappGateway', 'e', 'Session Failed to add');
    }
}

function goWhatsappGateway_send()
{
    global $config;
    
    // Get request method
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'POST') {
        // Get POST data for external API calls
        $input = json_decode(file_get_contents('php://input'), true);
        $to = isset($input['to']) ? $input['to'] : _req('to');
        $message = isset($input['message']) ? $input['message'] : _req('msg');
        $secret = isset($input['secret']) ? $input['secret'] : _req('secret');
    } else {
        // GET request for internal calls
        $to = _req('to');
        $message = _req('msg');
        $secret = _req('secret');
    }

    if (empty($to) || empty($message)) {
        http_response_code(400);
        echo json_encode([
            'code' => 'ERROR',
            'message' => 'Missing required parameters: to and message'
        ]);
        exit;
    }

    // Get or generate secret key
    $storedSecret = goWhatsappGateway_getSecret();
    if (!hash_equals($storedSecret, (string)$secret)) {
        http_response_code(401);
        echo json_encode([
            'code' => 'ERROR', 
            'message' => 'Invalid secret key'
        ]);
        exit;
    }

    $result = goWhatsappGateway_hook_send_whatsapp([$to, $message]);
    $json = json_decode($result, true);
    
    header('Content-Type: application/json');
    if ($json) {
        echo json_encode([
            'code' => 'SUCCESS',
            'message' => 'Message sent successfully',
            'data' => $json
        ]);
    } else {
        echo json_encode([
            'code' => 'ERROR',
            'message' => 'Failed to send message',
            'data' => $result
        ]);
    }
    exit;
}

function goWhatsappGateway_getSecret()
{
    $path = goWhatsappGateway_getPath();
    $secretFile = $path . 'api_secret.key';
    
    if (!file_exists($secretFile)) {
        // Generate new secret key only on first run
        $newSecret = bin2hex(random_bytes(16));
        file_put_contents($secretFile, $newSecret);
        return $newSecret;
    }
    
    return file_get_contents($secretFile);
}

function goWhatsappGateway_generateSecret()
{
    $path = goWhatsappGateway_getPath();
    $secretFile = $path . 'api_secret.key';
    
    // Generate new secret key
    $newSecret = bin2hex(random_bytes(16));
    file_put_contents($secretFile, $newSecret);
    
    header('Content-Type: application/json');
    echo json_encode([
        'code' => 'SUCCESS',
        'secret' => $newSecret,
        'message' => 'New secret key generated'
    ]);
    exit;
}

function goWhatsappGateway_testMessage()
{
    global $ui, $config;
    _admin();

    $testResult = null;
    $testPhone = '';
    $testMessage = '';

    // Check if WhatsApp is configured
    if (empty($config['go_whatsapp_gateway_url'])) {
        $testResult = [
            'status' => 'error',
            'message' => 'Go WhatsApp is not configured. Please configure the server URL first.',
            'response' => null
        ];
    } else {
        // Check if there are any active sessions
        $path = goWhatsappGateway_getPath();
        $sessions = [];
        if (file_exists($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) == 'session') {
                    $sessions[] = str_replace(".session", "", $file);
                }
            }
        }

        if (empty($sessions)) {
            $testResult = [
                'status' => 'warning',
                'message' => 'No WhatsApp sessions found. Please add and connect a session first.',
                'response' => null
            ];
        }
    }

    if (!empty(_post('test_phone')) && !empty(_post('test_message'))) {
        $phone = alphanumeric(_post('test_phone'));
        $message = _post('test_message');
        $testPhone = $phone;
        $testMessage = $message;

        $result = goWhatsappGateway_hook_send_whatsapp([$phone, $message]);
        $json = json_decode($result, true);

        if ($json && isset($json['code']) && $json['code'] == 'SUCCESS') {
            $testResult = [
                'status' => 'success',
                'message' => 'Test message sent successfully!',
                'response' => $json
            ];
        } else {
            $testResult = [
                'status' => 'error',
                'message' => 'Failed to send test message: ' . ($json['message'] ?? $result),
                'response' => $json ?? $result
            ];
        }
    }

    $ui->assign('testResult', $testResult);
    $ui->assign('testPhone', $testPhone);
    $ui->assign('testMessage', $testMessage);
    $ui->assign('_title', 'Go WhatsApp Test Message');
    $ui->assign('_system_menu', 'plugin/goWhatsappGateway');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('menu', 'test');
    $ui->display('goWhatsappGateway.tpl');
}

// Add table for Go WhatsApp message logs if it doesn't exist
if (!isTableExist('tbl_go_whatsapp_logs')) {
    ORM::raw_execute("CREATE TABLE IF NOT EXISTS tbl_go_whatsapp_logs (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        phone_number VARCHAR(50) NOT NULL,
        message TEXT NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'sent',
        response TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

function goWhatsappGateway_hook_send_whatsapp($data = [])
{
    global $config;
    list($phone, $txt) = $data;

    // Check if this exact message was sent in the last 10 seconds to prevent spam
    $recent_log = ORM::for_table('tbl_go_whatsapp_logs')
        ->where('phone_number', $phone)
        ->where('message', $txt)
        ->where_gte('created_at', date('Y-m-d H:i:s', strtotime('-10 seconds')))
        ->find_one();

    if ($recent_log) {
        return json_encode([
            'code' => 'DUPLICATE',
            'message' => 'Message already sent recently. Please wait 10 seconds before sending the same message again.',
            'results' => null
        ]);
    }

    // Check rate limits before sending
    $rateLimitCheck = goWhatsappGateway_checkRateLimit($phone);
    if (!$rateLimitCheck['allowed']) {
        return json_encode([
            'code' => 'RATE_LIMIT_EXCEEDED',
            'message' => $rateLimitCheck['message'],
            'reason' => $rateLimitCheck['reason'],
            'retry_after' => $rateLimitCheck['retry_after'],
            'results' => null
        ]);
    }



    if (!empty($config['go_whatsapp_gateway_url'])) {
        $url = rtrim($config['go_whatsapp_gateway_url'], '/') . '/send/message';

        // Pick a device_id — prefer live API list, fall back to local files
        $deviceId = '';
        $devResp  = Http::getData(rtrim($config['go_whatsapp_gateway_url'], '/') . '/devices', goWhatsappGateway_getAuthHeaders());
        $devJson  = json_decode($devResp, true);
        if (!empty($devJson['data']) && is_array($devJson['data'])) {
            $pick     = $devJson['data'][array_rand($devJson['data'])];
            $deviceId = $pick['device_id'] ?? $pick['id'] ?? '';
        } else {
            $sessionFiles = glob(goWhatsappGateway_getPath() . '*.session');
            if (!empty($sessionFiles)) {
                $deviceId = basename($sessionFiles[array_rand($sessionFiles)], '.session');
            }
        }

        // Add intelligent delay before sending
        goWhatsappGateway_addDelayIfNeeded($phone);

        // Create log entry before sending
        $log = ORM::for_table('tbl_go_whatsapp_logs')->create();
        $log->phone_number = $phone;
        $log->message = $txt;
        $log->save();

        $response = Http::postJsonData(
            $url,
            [
                'phone' => $phone,
                'message' => $txt
            ],
            goWhatsappGateway_getAuthHeaders($deviceId)
        );

        // Update log with response
        $log->response = $response;
        $responseJson = json_decode($response, true);
        if ($responseJson && isset($responseJson['code']) && $responseJson['code'] == 'SUCCESS') {
            $log->status = 'delivered';
        } else {
            $log->status = 'failed';
        }
        $log->save();

        return $response;
    }
    return "Go WhatsApp Gateway URL not set";
}

function goWhatsappGateway_delSession()
{
    global $config;
    _admin();
    $path    = goWhatsappGateway_getPath();
    $session = alphanumeric(_get('s'));
    if (empty($session)) {
        r2(U . 'plugin/goWhatsappGateway', 'e', 'Session not found');
    }

    // v8 API: DELETE /devices/:device_id
    if (!empty($config['go_whatsapp_gateway_url'])) {
        $base = rtrim($config['go_whatsapp_gateway_url'], '/');
        $ch   = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => "$base/devices/" . urlencode($session),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'DELETE',
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => goWhatsappGateway_getAuthHeaders($session),
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    // Remove local file if it exists
    if (file_exists("$path$session.session")) {
        unlink("$path$session.session");
    }
    r2(U . 'plugin/goWhatsappGateway', 's', 'Session Deleted');
}

function goWhatsappGateway_status()
{
    $session = alphanumeric(_get('s'));
    if (empty($session)) {
        die('<span class="label label-danger">Not Found</span>');
    }

    // Check connection status via GOWA API (X-Device-Id header)
    $status = goWhatsappGateway_checkStatusApi($session);
    if ($status && strpos($status, 'connected') !== false) {
        die('<span class="label label-success">Logged in</span>');
    } else {
        die('<span class="label label-danger">Not Logged in</span>');
    }
}

function goWhatsappGateway_getPath()
{
    global $UPLOAD_PATH;
    $path = $UPLOAD_PATH . DIRECTORY_SEPARATOR . "go_whatsapp" . DIRECTORY_SEPARATOR;
    if (!file_exists($path)) {
        mkdir($path);
    }
    return $path;
}

function goWhatsappGateway_getAuthHeaders($deviceId = '')
{
    global $config;
    $username = $config['go_whatsapp_username'] ?? 'admin';
    $password = $config['go_whatsapp_password'] ?? 'admin';
    $auth = base64_encode($username . ':' . $password);
    $headers = ["Authorization: Basic $auth"];
    if (!empty($deviceId)) {
        $headers[] = "X-Device-Id: $deviceId";
    }
    return $headers;
}

function goWhatsappGateway_loginApi($deviceId = '')
{
    global $config;
    // GOWA v8: /app/login still handles QR — device_id is passed via X-Device-Id header
    $base    = rtrim($config['go_whatsapp_gateway_url'], '/');
    $url     = "$base/app/login";
    $headers = goWhatsappGateway_getAuthHeaders($deviceId);
    $result  = Http::getData($url, $headers);
    return $result;
}

function goWhatsappGateway_loginWithCodeApi($phone, $deviceId = '')
{
    global $config;
    // GOWA v8: /app/login-with-code still handles pairing codes — device_id via X-Device-Id header
    $base    = rtrim($config['go_whatsapp_gateway_url'], '/');
    $url     = "$base/app/login-with-code?phone=" . urlencode($phone);
    $headers = goWhatsappGateway_getAuthHeaders($deviceId);
    $ch      = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => $headers,
    ]);
    $result   = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode === 200) ? $result : json_encode([
        'code' => 'ERROR', 'message' => 'API Error: HTTP ' . $httpCode, 'results' => []
    ]);
}

function goWhatsappGateway_checkStatusApi($deviceId = '')
{
    global $config;
    // GOWA v8: /app/status with X-Device-Id header (device-path stubs not implemented)
    $base    = rtrim($config['go_whatsapp_gateway_url'], '/');
    $url     = "$base/app/status";
    $headers = goWhatsappGateway_getAuthHeaders($deviceId);
    $result  = Http::getData($url, $headers);

    $json = json_decode($result, true);
    // v8 status values: online, connected, loggedIn, logged_in
    if (!empty($json['data']['status']) && in_array(strtolower($json['data']['status']), ['online', 'connected', 'loggedin'])) {
        return 'connected';
    }
    // Old API / legacy fallback
    if (!empty($json['code']) && $json['code'] == 'SUCCESS' && !empty($json['results'])) {
        return 'connected';
    }
    return $result;
}

function goWhatsappGateway_logs()
{
    global $ui, $admin;
    _admin();

    // Handle delete actions
    if ($_POST) {
        if (isset($_POST['delete_all'])) {
            ORM::for_table('tbl_go_whatsapp_logs')->delete_many();
            r2(U . 'plugin/goWhatsappGateway_logs', 's', 'All Go WhatsApp logs have been deleted successfully');
        } elseif (isset($_POST['delete_selected']) && isset($_POST['selected_logs'])) {
            $selected_ids = array_filter(array_map('intval', (array)$_POST['selected_logs']));
            if (!empty($selected_ids)) {
                ORM::for_table('tbl_go_whatsapp_logs')
                    ->where_in('id', $selected_ids)
                    ->delete_many();
                $count = count($selected_ids);
                r2(U . 'plugin/goWhatsappGateway_logs', 's', $count . ' Go WhatsApp log(s) have been deleted successfully');
            } else {
                r2(U . 'plugin/goWhatsappGateway_logs', 'w', 'No logs selected for deletion');
            }
        } elseif (isset($_POST['resend_id'])) {
            $resend_id = intval($_POST['resend_id']);
            $log = ORM::for_table('tbl_go_whatsapp_logs')->find_one($resend_id);
            if ($log) {
                $result = goWhatsappGateway_hook_send_whatsapp([$log->phone_number, $log->message]);
                $json   = json_decode($result, true);
                if ($json && isset($json['code']) && $json['code'] === 'SUCCESS') {
                    r2(U . 'plugin/goWhatsappGateway_logs', 's', 'Message resent successfully to ' . $log->phone_number);
                } else {
                    r2(U . 'plugin/goWhatsappGateway_logs', 'e', 'Resend failed: ' . ($json['message'] ?? $result));
                }
            } else {
                r2(U . 'plugin/goWhatsappGateway_logs', 'e', 'Log entry not found');
            }
        }
    }

    // CSV export
    if (isset($_GET['export']) && $_GET['export'] === 'csv') {
        $q = ORM::for_table('tbl_go_whatsapp_logs')->order_by_desc('created_at');
        $search     = trim($_GET['search'] ?? '');
        $filter_status = $_GET['status'] ?? '';
        $date_from  = $_GET['date_from'] ?? '';
        $date_to    = $_GET['date_to'] ?? '';
        if ($search)        { $q->where_raw('(phone_number LIKE ? OR message LIKE ?)', ["%$search%", "%$search%"]); }
        if ($filter_status) { $q->where('status', $filter_status); }
        if ($date_from)     { $q->where_gte('created_at', $date_from . ' 00:00:00'); }
        if ($date_to)       { $q->where_lte('created_at', $date_to . ' 23:59:59'); }
        $rows = $q->find_many();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="whatsapp_logs_' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Phone Number', 'Message', 'Status', 'Response', 'Date']);
        foreach ($rows as $row) {
            fputcsv($out, [$row->id, $row->phone_number, $row->message, $row->status, $row->response, $row->created_at]);
        }
        fclose($out);
        exit;
    }

    $page          = max(1, intval($_GET['page'] ?? 1));
    $per_page      = intval($_GET['per_page'] ?? 50);
    $search        = trim($_GET['search'] ?? '');
    $filter_status = $_GET['status'] ?? '';
    $date_from     = $_GET['date_from'] ?? '';
    $date_to       = $_GET['date_to'] ?? '';

    $allowed_per_page = [10, 50, 100, 150, 200, 500];
    if (!in_array($per_page, $allowed_per_page)) { $per_page = 50; }

    // Base filtered query
    $base = ORM::for_table('tbl_go_whatsapp_logs');
    if ($search)        { $base->where_raw('(phone_number LIKE ? OR message LIKE ?)', ["%$search%", "%$search%"]); }
    if ($filter_status) { $base->where('status', $filter_status); }
    if ($date_from)     { $base->where_gte('created_at', $date_from . ' 00:00:00'); }
    if ($date_to)       { $base->where_lte('created_at', $date_to . ' 23:59:59'); }

    $total_logs  = (clone $base)->count();
    $total_pages = max(1, (int) ceil($total_logs / $per_page));
    if ($page > $total_pages) { $page = $total_pages; }

    $logs = (clone $base)->order_by_desc('created_at')->limit($per_page)->offset(($page - 1) * $per_page)->find_many();

    // Stats (always across all records, ignoring filter)
    $stats_total     = ORM::for_table('tbl_go_whatsapp_logs')->count();
    $stats_delivered = ORM::for_table('tbl_go_whatsapp_logs')->where('status', 'delivered')->count();
    $stats_failed    = ORM::for_table('tbl_go_whatsapp_logs')->where('status', 'failed')->count();
    $stats_rate      = $stats_total > 0 ? round(($stats_delivered / $stats_total) * 100) : 0;

    $ui->assign('logs', $logs);
    $ui->assign('page', $page);
    $ui->assign('per_page', $per_page);
    $ui->assign('total_pages', $total_pages);
    $ui->assign('total_logs', $total_logs);
    $ui->assign('search', $search);
    $ui->assign('filter_status', $filter_status);
    $ui->assign('date_from', $date_from);
    $ui->assign('date_to', $date_to);
    $ui->assign('stats_total', $stats_total);
    $ui->assign('stats_delivered', $stats_delivered);
    $ui->assign('stats_failed', $stats_failed);
    $ui->assign('stats_rate', $stats_rate);
    $ui->assign('_title', 'Go WhatsApp Message Logs');
    $ui->assign('_system_menu', 'plugin/goWhatsappGateway');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('goWhatsappGateway_logs.tpl');
}

function goWhatsappGateway_fetchQrAsBase64($qrUrl)
{
    $headers = goWhatsappGateway_getAuthHeaders();
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $qrUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($imageData !== false && $httpCode === 200) {
        return base64_encode($imageData);
    }
    
    return false;
}

function goWhatsappGateway_qr()
{
    _admin();
    $qrUrl = _get('url');
    if (empty($qrUrl)) {
        http_response_code(400);
        die('Missing QR URL parameter');
    }

    // Validate URL to prevent SSRF attacks
    $parsedUrl = parse_url($qrUrl);
    if (!$parsedUrl || !in_array($parsedUrl['scheme'], ['http', 'https'])) {
        http_response_code(400);
        die('Invalid URL scheme');
    }
    // Block private/internal IPs
    $host = $parsedUrl['host'] ?? '';
    $ip   = filter_var($host, FILTER_VALIDATE_IP) ? $host : @gethostbyname($host);
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        http_response_code(403);
        die('Access to private/internal addresses is not allowed');
    }

    // Get QR code image data using cURL for better control
    $headers = goWhatsappGateway_getAuthHeaders();
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $qrUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    
    if ($imageData === false || $httpCode !== 200) {
        http_response_code(500);
        die('Failed to fetch QR code. HTTP Code: ' . $httpCode);
    }

    // Set appropriate headers for image display
    if ($contentType && strpos($contentType, 'image') === 0) {
        header('Content-Type: ' . $contentType);
    } else {
        header('Content-Type: image/png');
    }
    header('Cache-Control: public, max-age=30'); // Cache for 30 seconds
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 30) . ' GMT');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Headers: Content-Type');
    
    echo $imageData;
    exit;
}

function goWhatsappGateway_logout()
{
    global $config;
    _admin();

    $session = alphanumeric(_get('s'));
    $path    = goWhatsappGateway_getPath();

    if (empty($session)) {
        r2(U . 'plugin/goWhatsappGateway', 'e', 'Session not found');
    }

    // GOWA v8: POST /app/logout with X-Device-Id header
    $base = rtrim($config['go_whatsapp_gateway_url'], '/');
    $url  = "$base/app/logout";
    $ch   = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => '{}',
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => array_merge(goWhatsappGateway_getAuthHeaders($session), ['Content-Type: application/json']),
    ]);
    $result   = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Clean up local session file if it exists
    if (file_exists("$path$session.session")) {
        unlink("$path$session.session");
    }

    if ($httpCode === 200) {
        r2(U . 'plugin/goWhatsappGateway', 's', 'Successfully logged out');
    } else {
        r2(U . 'plugin/goWhatsappGateway', 'w', 'Session removed locally. API response: HTTP ' . $httpCode);
    }
}

function goWhatsappGateway_reconnect()
{
    global $config;
    _admin();

    $session = alphanumeric(_get('s'));

    if (empty($session)) {
        r2(U . 'plugin/goWhatsappGateway', 'e', 'Session not found');
    }

    // GOWA v8: POST /app/reconnect with X-Device-Id header
    $base = rtrim($config['go_whatsapp_gateway_url'], '/');
    $url  = "$base/app/reconnect";
    $ch   = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => '{}',
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => array_merge(goWhatsappGateway_getAuthHeaders($session), ['Content-Type: application/json']),
    ]);
    $result   = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        r2(U . 'plugin/goWhatsappGateway', 's', 'Reconnect command sent successfully');
    } else {
        r2(U . 'plugin/goWhatsappGateway', 'e', 'Failed to reconnect: ' . $result);
    }
}

function goWhatsappGateway_checkRateLimit($phone, $messageType = 'general')
{
    global $config;
    
    // Get rate limit settings from config or use defaults
    $maxPerHour = $config['go_whatsapp_max_per_hour'] ?? 10;
    $maxPerDay = $config['go_whatsapp_max_per_day'] ?? 50;
    $maxOverallPerHour = $config['go_whatsapp_max_overall_per_hour'] ?? 100;
    $maxOverallPerDay = $config['go_whatsapp_max_overall_per_day'] ?? 500;
    
    $now = date('Y-m-d H:i:s');
    $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
    $oneDayAgo = date('Y-m-d H:i:s', strtotime('-1 day'));
    
    // Check per-recipient limits
    $hourlyCount = ORM::for_table('tbl_go_whatsapp_logs')
        ->where('phone_number', $phone)
        ->where_gte('created_at', $oneHourAgo)
        ->count();
        
    $dailyCount = ORM::for_table('tbl_go_whatsapp_logs')
        ->where('phone_number', $phone)
        ->where_gte('created_at', $oneDayAgo)
        ->count();
    
    // Check overall limits
    $overallHourlyCount = ORM::for_table('tbl_go_whatsapp_logs')
        ->where_gte('created_at', $oneHourAgo)
        ->count();
        
    $overallDailyCount = ORM::for_table('tbl_go_whatsapp_logs')
        ->where_gte('created_at', $oneDayAgo)
        ->count();
    
    // Check if limits are exceeded
    if ($hourlyCount >= $maxPerHour) {
        return [
            'allowed' => false,
            'reason' => 'hourly_limit_exceeded',
            'message' => "Hourly limit of {$maxPerHour} messages per recipient exceeded. Please wait before sending more messages to this number.",
            'retry_after' => 3600 // 1 hour
        ];
    }
    
    if ($dailyCount >= $maxPerDay) {
        return [
            'allowed' => false,
            'reason' => 'daily_limit_exceeded',
            'message' => "Daily limit of {$maxPerDay} messages per recipient exceeded. Please try again tomorrow.",
            'retry_after' => 86400 // 24 hours
        ];
    }
    
    if ($overallHourlyCount >= $maxOverallPerHour) {
        return [
            'allowed' => false,
            'reason' => 'overall_hourly_limit_exceeded',
            'message' => "Overall hourly limit of {$maxOverallPerHour} messages exceeded. Please wait before sending more messages.",
            'retry_after' => 3600 // 1 hour
        ];
    }
    
    if ($overallDailyCount >= $maxOverallPerDay) {
        return [
            'allowed' => false,
            'reason' => 'overall_daily_limit_exceeded',
            'message' => "Overall daily limit of {$maxOverallPerDay} messages exceeded. Please try again tomorrow.",
            'retry_after' => 86400 // 24 hours
        ];
    }
    
    return [
        'allowed' => true,
        'reason' => 'within_limits',
        'message' => 'Message sending allowed',
        'retry_after' => 0
    ];
}

function goWhatsappGateway_checkOptIn($phone)
{
    // Check if user has opted in to receive WhatsApp messages
    // This checks the customers table for whatsapp_consent field
    $customer = ORM::for_table('tbl_customers')
        ->where('phonenumber', $phone)
        ->find_one();
    
    if (!$customer) {
        // If customer not found, allow for admin/test messages but log it
        return [
            'allowed' => true,
            'reason' => 'customer_not_found',
            'message' => 'Customer not found in database - proceeding with caution'
        ];
    }
    
    // Check if whatsapp_consent column exists
    try {
        $whatsappConsent = $customer->whatsapp_consent ?? 0;
        
        // Check if customer has opted in for WhatsApp messages
        if ($whatsappConsent == 0) {
            return [
                'allowed' => false,
                'reason' => 'no_consent',
                'message' => 'Customer has not opted in to receive WhatsApp messages'
            ];
        }
    } catch (Exception $e) {
        // Column doesn't exist, allow all messages
        return [
            'allowed' => true,
            'reason' => 'consent_disabled',
            'message' => 'WhatsApp consent tracking is disabled'
        ];
    }
    
    return [
        'allowed' => true,
        'reason' => 'consent_given',
        'message' => 'Customer has given consent for WhatsApp messages'
    ];
}

function goWhatsappGateway_addDelayIfNeeded($phone)
{
    // Add intelligent delays between messages to avoid spam detection
    $path = goWhatsappGateway_getPath();
    $lastSentFile = $path . 'last_sent_time.txt';
    
    $lastSentTime = 0;
    if (file_exists($lastSentFile)) {
        $lastSentTime = (int)file_get_contents($lastSentFile);
    }
    
    $currentTime = time();
    $timeSinceLastMessage = $currentTime - $lastSentTime;
    $minDelay = 5; // Minimum 5 seconds between messages
    
    if ($timeSinceLastMessage < $minDelay) {
        $sleepTime = $minDelay - $timeSinceLastMessage;
        sleep($sleepTime);
    }
    
    // Update last sent time
    file_put_contents($lastSentFile, $currentTime);
}
