<?php
/**
 * DeepSeek AI Chat Assistant Plugin
 * Provides an AI-powered chat assistant for ISP administrators
 * Powered by DeepSeek API (https://platform.deepseek.com)
 */

register_menu("AI Assistant", true, "deepseek_ai", 'AFTER_SETTINGS', 'ion ion-chatbubbles', "AI", "green", ['Admin', 'SuperAdmin']);


/**
 * Main plugin router
 */
function deepseek_ai()
{
    global $ui, $config, $routes;
    $action = $routes['2'] ?? '';

    switch ($action) {
        case 'config':
            deepseek_ai_config_page();
            return;
        case 'api':
            deepseek_ai_handle_api();
            return;
        case 'token':
            deepseek_ai_get_token();
            return;
        default:
            deepseek_ai_chat_page();
            return;
    }
}

/**
 * Full-page chat interface
 */
function deepseek_ai_chat_page()
{
    global $ui, $config;
    _admin();
    $admin = Admin::_info();

    if (empty($config['deepseek_api_key'])) {
        r2(U . 'plugin/deepseek_ai/config', 'e', 'Please configure your DeepSeek API key first.');
    }

    $ui->assign('_title', 'AI Assistant');
    $ui->assign('_system_menu', 'plugin/deepseek_ai');
    $ui->assign('_admin', $admin);
    $ui->assign('csrf_token', Csrf::generateAndStoreToken());
    $ui->display('deepseek_ai.tpl');
}

/**
 * Configuration page (save API key, system prompt, model)
 */
function deepseek_ai_config_page()
{
    global $ui, $config;
    _admin();
    $admin = Admin::_info();

    if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
        _alert('You do not have permission to access this page', 'danger', 'dashboard');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $csrf_token = _post('token');
        if (!Csrf::check($csrf_token)) {
            r2(U . 'plugin/deepseek_ai/config', 'e', 'Invalid or Expired CSRF Token.');
        }

        $fields = [
            'deepseek_api_key',
            'deepseek_system_prompt',
            'deepseek_model',
        ];

        foreach ($fields as $field) {
            $val = _post($field);
            // Never store empty API key over a valid one
            if ($field === 'deepseek_api_key' && empty($val) && !empty($config['deepseek_api_key'])) {
                continue;
            }
            $d = ORM::for_table('tbl_appconfig')->where('setting', $field)->find_one();
            if ($d) {
                $d->value = $val;
                $d->save();
            } else {
                $d = ORM::for_table('tbl_appconfig')->create();
                $d->setting = $field;
                $d->value   = $val;
                $d->save();
            }
        }

        r2(U . 'plugin/deepseek_ai', 's', 'Configuration saved successfully.');
    }

    $ui->assign('_title', 'AI Assistant — Configuration');
    $ui->assign('_system_menu', 'plugin/deepseek_ai');
    $ui->assign('_admin', $admin);
    $ui->assign('csrf_token', Csrf::generateAndStoreToken());
    $ui->display('deepseek_ai_config.tpl');
}

/**
 * Returns a fresh CSRF token as JSON (used by the floating widget)
 */
function deepseek_ai_get_token()
{
    _admin();
    header('Content-Type: application/json');
    echo json_encode(['token' => Csrf::generateAndStoreToken()]);
    exit;
}

/**
 * AJAX endpoint — proxies message to DeepSeek API and returns the reply
 */
function deepseek_ai_handle_api()
{
    global $config;
    _admin();
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'POST required.']);
        exit;
    }

    $csrf_token = _post('token');
    if (!Csrf::check($csrf_token)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid or expired CSRF token.']);
        exit;
    }

    $api_key = $config['deepseek_api_key'] ?? '';
    if (empty($api_key)) {
        http_response_code(400);
        echo json_encode(['error' => 'DeepSeek API key not configured. Go to AI Assistant → Configuration.']);
        exit;
    }

    $message = trim(_post('message') ?? '');
    if (empty($message)) {
        echo json_encode(['error' => 'Empty message.']);
        exit;
    }

    // Sanitize and limit message length
    $message = strip_tags($message);
    if (strlen($message) > 4000) {
        $message = substr($message, 0, 4000);
    }

    // Parse conversation history
    $raw_history = _post('history') ?? '[]';
    $history = json_decode($raw_history, true);
    if (!is_array($history)) {
        $history = [];
    }
    // Keep last 20 exchanges to avoid token overflow
    $history = array_slice($history, -40);

    $default_prompt = "You are a helpful AI assistant for " . ($config['CompanyName'] ?? 'SpeedRadius') . ", an ISP management system. Answer questions helpfully and concisely.";
    $system_prompt  = !empty($config['deepseek_system_prompt']) ? $config['deepseek_system_prompt'] : $default_prompt;

    $model = !empty($config['deepseek_model']) ? $config['deepseek_model'] : 'deepseek-chat';

    // Build messages array
    $messages = [['role' => 'system', 'content' => $system_prompt]];
    foreach ($history as $h) {
        $role    = $h['role']    ?? '';
        $content = $h['content'] ?? '';
        if (in_array($role, ['user', 'assistant']) && !empty($content)) {
            $messages[] = [
                'role'    => $role,
                'content' => strip_tags($content),
            ];
        }
    }
    $messages[] = ['role' => 'user', 'content' => $message];

    $payload = json_encode([
        'model'       => $model,
        'messages'    => $messages,
        'temperature' => 0.7,
        'max_tokens'  => 1024,
        'stream'      => false,
    ]);

    $ch = curl_init('https://api.deepseek.com/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key,
        ],
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response   = curl_exec($ch);
    $http_code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        echo json_encode(['error' => 'Connection error: ' . $curl_error]);
        exit;
    }

    $decoded = json_decode($response, true);

    if ($http_code !== 200 || !isset($decoded['choices'][0]['message']['content'])) {
        $errMsg = isset($decoded['error']['message'])
            ? $decoded['error']['message']
            : 'DeepSeek API error (HTTP ' . $http_code . ')';
        echo json_encode(['error' => $errMsg]);
        exit;
    }

    $reply  = $decoded['choices'][0]['message']['content'];
    $usage  = $decoded['usage'] ?? [];

    echo json_encode([
        'reply'  => $reply,
        'tokens' => intval($usage['total_tokens'] ?? 0),
    ]);
    exit;
}
