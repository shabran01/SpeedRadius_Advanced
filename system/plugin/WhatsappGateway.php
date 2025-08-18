<?php

register_menu("Whatsapp Gateway", true, "whatsappGateway", 'AFTER_SETTINGS', 'glyphicon glyphicon-comment', '', '', ['Admin', 'SuperAdmin']);
register_menu("Whatsapp Logs", true, "whatsappGateway_logs", 'AFTER_SETTINGS', 'glyphicon glyphicon-list-alt', '', '', ['Admin', 'SuperAdmin']);

register_hook('send_whatsapp', 'whatsappGateway_hook_send_whatsapp');

function whatsappGateway()
{
    global $ui, $config, $admin;
    _admin();
    $path = whatsappGateway_getPath();

    if (empty($config['whatsapp_gateway_secret'])) {
        r2(U . 'plugin/whatsappGateway_config', 'e', 'Please configure first');
    }

    $files = scandir($path);
    $phones = [];
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) == 'nux') {
            $phone = str_replace(".nux", "", $file);
            $phones[] = $phone;
        }
    }

    $ui->assign('phones', $phones);
    $ui->assign('_title', 'Whatsap Gateway');
    $ui->assign('_system_menu', 'plugin/whatsappGateway');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('whatsappGateway.tpl');
}

function whatsappGateway_config()
{
    global $ui;
    _admin();

    if (!empty(_post('whatsapp_gateway_url')) || !empty(_post('whatsapp_gateway_secret'))) {
        $d = ORM::for_table('tbl_appconfig')->where('setting', 'whatsapp_gateway_url')->find_one();
        if ($d) {
            $d->value = _post('whatsapp_gateway_url');
            $d->save();
        } else {
            $d = ORM::for_table('tbl_appconfig')->create();
            $d->setting = 'whatsapp_gateway_url';
            $d->value = _post('whatsapp_gateway_url');
            $d->save();
        }
        $d = ORM::for_table('tbl_appconfig')->where('setting', 'whatsapp_gateway_secret')->find_one();
        if ($d) {
            $d->value = _post('whatsapp_gateway_secret');
            $d->save();
        } else {
            $d = ORM::for_table('tbl_appconfig')->create();
            $d->setting = 'whatsapp_gateway_secret';
            $d->value = _post('whatsapp_gateway_secret');
            $d->save();
        }
        $d = ORM::for_table('tbl_appconfig')->where('setting', 'whatsapp_country_code_phone')->find_one();
        if ($d) {
            $d->value = _post('whatsapp_country_code_phone');
            $d->save();
        } else {
            $d = ORM::for_table('tbl_appconfig')->create();
            $d->setting = 'whatsapp_country_code_phone';
            $d->value = _post('whatsapp_country_code_phone');
            $d->save();
        }
        r2(U . 'plugin/whatsappGateway_config', 's', 'Configuration saved');
    }
    $ui->assign('_title', 'Whatsap Gateway Configuration');
    $ui->assign('_system_menu', 'plugin/whatsappGateway');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('menu', 'config');
    $ui->display('whatsappGateway.tpl');
}


function whatsappGateway_login()
{
    global $ui;
    _admin();

    $phone = alphanumeric(_get('p'));
    $path = whatsappGateway_getPath();
    if (empty($phone)) {
        r2(U . 'plugin/whatsappGateway', 'e', 'Not Found');
    }
    if (!file_exists("$path$phone.nux")) {
        r2(U . 'plugin/whatsappGateway', 'e', 'Not Found.');
    }
    $json = json_decode(file_get_contents("$path$phone.nux"), true);
    if (!isset($json['jwt'])) {
        r2(U . 'plugin/whatsappGateway', 'e', 'Not Connected');
    }
    if ($json['jwt'] == '') {
        r2(U . 'plugin/whatsappGateway', 'e', 'Not Connected.');
    }
    if (strlen($json['jwt']) > 4 && substr($json['jwt'], 0, 5) == 'Error') {
        // repeat request
        $json['jwt'] = whatsappGateway_getJwtApi($phone);
        if (strlen($json['jwt']) > 4 && substr($json['jwt'], 0, 5) == 'Error') {
            r2(U . 'plugin/whatsappGateway', 'e', $json['jwt']);
        } else {
            file_put_contents("$path$phone.nux", json_encode($json));
        }
    }

    $result = whatsappGateway_loginApi($json['jwt']);
    
    if (strlen($result)) {
        if (substr($result, 0, 1) == '{') {
            $resultJson = json_decode($result, true);
            
            // Handle QR Code
            if (!empty($resultJson['data']['qrcode'])) {
                $qrImage = $resultJson['data']['qrcode'];
                $message = "<div style='text-align:center'>";
                $message .= "<img src='$qrImage' alt='WhatsApp QR Code' style='max-width:300px;margin:20px auto'><br>";
                $message .= "<p style='color:#128C7E;font-weight:600'>Scan this QR code with WhatsApp on your phone</p>";
                $message .= "</div>";
            }
            // Handle Pair Code
            else if (!empty($resultJson['data']['paircode'])) {
                $pairCode = $resultJson['data']['paircode'];
                $timeout = $resultJson['data']['timeout'];
                $message = "<div style='text-align:center'>";
                $message .= "<h1 style='font-size:32px;letter-spacing:2px;color:#128C7E'>$pairCode</h1>";
                $message .= "<p>Enter this code in WhatsApp > Linked Devices</p>";
                $message .= "<p>Expires in: $timeout seconds</p>";
                $message .= "</div>";
            }
            // Handle Connected State
            else if (isset($resultJson['message']) && trim($resultJson['message']) == 'WhatsApp Client is Reconnected') {
                $message = '<div class="alert alert-success" style="background:#25d366;color:white;text-align:center;">';
                $message .= '<i class="glyphicon glyphicon-ok-circle"></i> Connected & Ready to Use';
                $message .= '</div>';
            }
            // Handle Other Messages
            else {
                $message = $resultJson['message'];
            }
        } else {
            $message = $result;
        }
    } else {
        $message = '<div class="alert alert-warning">No response from WhatsApp server</div>';
    }
    $ui->assign('message', $message);
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->assign('menu', 'login');
    $ui->display('whatsappGateway.tpl');
}

function whatsappGateway_addPhone()
{
    _admin();
    $path = whatsappGateway_getPath();
    $phone = alphanumeric(_post("phonenumber"));
    if (empty($phone)) {
        r2(U . 'plugin/whatsappGateway', 'e', 'Phone not found');
    }
    if (file_exists("$path$phone.nux")) {
        r2(U . 'plugin/whatsappGateway', 'e', 'Phone already exists');
    }
    $json['jwt'] = whatsappGateway_getJwtApi($phone);
    file_put_contents("$path$phone.nux", json_encode($json));
    if (file_exists("$path$phone.nux")) {
        r2(U . 'plugin/whatsappGateway', 's', 'Phone Added');
    } else {
        r2(U . 'plugin/whatsappGateway', 'e', 'Phone Failed to add');
    }
}

function whatsappGateway_send()
{
    global $config;
    $to = alphanumeric(_req('to'));
    $msg = _req('msg');
    $secret = _req('secret');
    if ($secret != md5($config['whatsapp_gateway_secret'])) {
        showResult(false, 'Invalid secret');
    }
    $result  = whatsappGateway_hook_send_whatsapp([$to, $msg]);
    $json = json_decode($result, true);
    if ($json) {
        showResult(true, '', $json);
    } else {
        showResult(false, '', $result);
    }
}

// Add table for WhatsApp message logs if it doesn't exist
if (!isTableExist('tbl_whatsapp_logs')) {
    ORM::raw_execute("CREATE TABLE IF NOT EXISTS tbl_whatsapp_logs (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        phone_number VARCHAR(50) NOT NULL,
        message TEXT NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'sent',
        response TEXT,
        sender VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

function whatsappGateway_hook_send_whatsapp($data = [])
{
    global $config;
    list($phone, $txt) = $data;
    
    // Check if this exact message was sent in the last minute to prevent duplicates
    $recent_log = ORM::for_table('tbl_whatsapp_logs')
        ->where('phone_number', $phone)
        ->where('message', $txt)
        ->where_gte('created_at', date('Y-m-d H:i:s', strtotime('-1 minute')))
        ->find_one();
        
    if ($recent_log) {
        // Message was already sent recently
        return "Message already sent";
    }

    // Continue with existing send logic
    if (!empty($config['whatsapp_gateway_url'])) {
        if (!empty($config['whatsapp_country_code_phone'])) {
            $phone = whatsappGateway_phoneFormat($phone);
        }
        $path = whatsappGateway_getPath();
        $files = scandir($path);
        $was = [];
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'nux') {
                $was[] = $file;
            }
        }
        
        // Ensure there are WhatsApp accounts configured
        if (empty($was)) {
            return "No WhatsApp accounts configured";
        }
        
        $wa = $was[rand(0, count($was) - 1)];
        $json = json_decode(file_get_contents("$path$wa"), true);
        $url = $config['whatsapp_gateway_url'];
        
        // Create log entry before sending
        $log = ORM::for_table('tbl_whatsapp_logs')->create();
        $log->phone_number = $phone;
        $log->message = $txt;
        $log->sender = str_replace('.nux', '', $wa);
        $log->save();
        
        $response = Http::postData(
            $url . '/send/text',
            array('msisdn' => $phone, 'message' => $txt),
            [
                'Content-Type: application/x-www-form-urlencoded',
                "Authorization: Bearer $json[jwt]"
            ]
        );
        
        // Update log with response
        $log->response = $response;
        if ($response && strpos($response, 'error') === false) {
            $log->status = 'delivered';
        } else {
            $log->status = 'failed';
        }
        $log->save();
        
        return $response;
    }
    return "WhatsApp Gateway URL not set";
}

function whatsappGateway_delPhone()
{
    _admin();
    $path = whatsappGateway_getPath();
    $phone = alphanumeric(_get('p'));
    if (empty($phone)) {
        r2(U . 'plugin/whatsappGateway', 'e', 'Phone not found');
    }
    if (!file_exists("$path$phone.nux")) {
        r2(U . 'plugin/whatsappGateway', 'e', 'Phone not exists');
    }
    if (unlink("$path$phone.nux")) {
        r2(U . 'plugin/whatsappGateway', 's', 'Phone Deleted');
    } else {
        r2(U . 'plugin/whatsappGateway', 'e', 'Phone Failed to Delete');
    }
}

function whatsappGateway_status()
{
    $phone = alphanumeric(_get('p'));
    $path = whatsappGateway_getPath();
    if (empty($phone)) {
        die('<span class="label label-danger">Not Found</span>');
    }
    if (!file_exists("$path$phone.nux")) {
        die('<span class="label label-danger">Not Found.</span>');
    }

    $json = json_decode(file_get_contents("$path$phone.nux"), true);
    if (!isset($json['jwt'])) {
        die('<span class="label label-danger">Not Connected</span>');
    }
    if ($json['jwt'] == '') {
        die('<span class="label label-danger">Not Connected.</span>');
    }
    if (strlen($json['jwt']) > 4 && substr($json['jwt'], 0, 5) == 'Error') {
        // repeat request
        $json['jwt'] = whatsappGateway_getJwtApi($phone);
        if (strlen($json['jwt']) > 4 && substr($json['jwt'], 0, 5) == 'Error') {
            die('<span class="label label-danger">' . $json['jwt'] . '</span>');
        } else {
            file_put_contents("$path$phone.nux", json_encode($json));
        }
    }

    $result = whatsappGateway_loginApi($json['jwt']);
    if (strlen($result)) {
        if (substr($result, 0, 1) == '{') {
            $json = json_decode($result, true);
            if (!empty($json['data']['paircode'])) {
                $message = $json['message'] . '<br><br>';
                $message .= '<h1>' . $json['data']['paircode'] . '</h1><br>';
                $message .= 'Timeout in ' . $json['data']['timeout'] . ' Second(s)<br>';
            } else {
                $message = $json['message'];
            }
        } else {
            $message = $result;
        }
    } else {
        $message = $result;
    }
    if (trim($message) == 'WhatsApp Client is Reconnected') {
        die('<span class="label label-success">Logged in</span>');
    } else {
        die('<span class="label label-danger">Not Logged in</span>');
    }
    die();
}

function whatsappGateway_getPath()
{
    global $UPLOAD_PATH;
    $path = $UPLOAD_PATH . DIRECTORY_SEPARATOR . "whatsapp" . DIRECTORY_SEPARATOR;
    if (!file_exists($path)) {
        mkdir($path);
    }
    return $path;
}


function whatsappGateway_getJwtApi($phone)
{
    global $config;
    $url = $config['whatsapp_gateway_url'] . '/auth';
    $result = Http::getData(
        $url,
        [
            "Authorization: Basic " . base64_encode($phone . ":" . $config['whatsapp_gateway_secret'])
        ]
    );
    $json = json_decode($result, true);
    if ($json['status'] == 200) {
        return $json['data']['token'];
    } else {
        if (isset($json['message'])) {
            return "Error: " . $json['message'];
        } else {
            return "Error: " . $result;
        }
    }
}


function whatsappGateway_loginApi($jwt_whatsapp)
{
    global $config;
    if (isset($_GET['pair'])) {
        $url = $config['whatsapp_gateway_url'] . '/login/pair';
    } else {
        $url = $config['whatsapp_gateway_url'] . '/login';
    }
    $result = Http::postData(
        $url,
        [],
        [
            'Content-Type: application/x-www-form-urlencoded',
            "Authorization: Bearer $jwt_whatsapp"
        ]
    );
    return $result;
}


function whatsappGateway_phoneFormat($phone)
{
    global $config;
    if (!empty($phone) && !empty($config['whatsapp_country_code_phone'])) {
        return preg_replace('/^0/',  $config['whatsapp_country_code_phone'], $phone);
    } else {
        return $phone;
    }
}

function whatsappGateway_logs()
{
    global $ui, $admin;
    _admin();

    // Handle delete actions
    if ($_POST) {
        if (isset($_POST['delete_all'])) {
            // Delete all logs
            ORM::for_table('tbl_whatsapp_logs')->delete_many();
            r2(U . 'plugin/whatsappGateway_logs', 's', 'All WhatsApp logs have been deleted successfully');
        } elseif (isset($_POST['delete_selected']) && isset($_POST['selected_logs'])) {
            // Delete selected logs
            $selected_ids = $_POST['selected_logs'];
            if (!empty($selected_ids)) {
                ORM::for_table('tbl_whatsapp_logs')
                    ->where_in('id', $selected_ids)
                    ->delete_many();
                $count = count($selected_ids);
                r2(U . 'plugin/whatsappGateway_logs', 's', $count . ' WhatsApp log(s) have been deleted successfully');
            } else {
                r2(U . 'plugin/whatsappGateway_logs', 'w', 'No logs selected for deletion');
            }
        }
    }

    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 50;
    
    // Validate per_page values
    $allowed_per_page = [10, 50, 100, 150, 200, 500];
    if (!in_array($per_page, $allowed_per_page)) {
        $per_page = 50; // default fallback
    }
    
    $limit = $per_page;
    $offset = ($page - 1) * $limit;

    $logs = ORM::for_table('tbl_whatsapp_logs')
        ->order_by_desc('created_at')
        ->limit($limit)
        ->offset($offset)
        ->find_many();
    
    $total_logs = ORM::for_table('tbl_whatsapp_logs')->count();
    $total_pages = ceil($total_logs / $limit);

    $ui->assign('logs', $logs);
    $ui->assign('page', $page);
    $ui->assign('per_page', $per_page);
    $ui->assign('total_pages', $total_pages);
    $ui->assign('total_logs', $total_logs);
    $ui->assign('_title', 'WhatsApp Message Logs');
    $ui->assign('_system_menu', 'plugin/whatsappGateway');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $ui->display('whatsappGateway_logs.tpl');
}

function whatsappGateway_logout()
{
    global $config;
    _admin();
    
    $phone = alphanumeric(_get('p'));
    $path = whatsappGateway_getPath();
    
    if (empty($phone)) {
        r2(U . 'plugin/whatsappGateway', 'e', 'Phone not found');
    }
    
    if (!file_exists("$path$phone.nux")) {
        r2(U . 'plugin/whatsappGateway', 'e', 'Phone not found');
    }
    
    $json = json_decode(file_get_contents("$path$phone.nux"), true);
    if (!isset($json['jwt']) || empty($json['jwt'])) {
        r2(U . 'plugin/whatsappGateway', 'e', 'Not Connected');
    }

    $url = $config['whatsapp_gateway_url'] . '/logout';
    $result = Http::postData(
        $url,
        [],
        [
            'Content-Type: application/x-www-form-urlencoded',
            "Authorization: Bearer {$json['jwt']}"
        ]
    );

    // Delete the .nux file to complete logout
    unlink("$path$phone.nux");
    r2(U . 'plugin/whatsappGateway', 's', 'Successfully logged out');
}
