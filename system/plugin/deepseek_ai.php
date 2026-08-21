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

    $default_prompt = "You are an AI assistant with FULL access to the live database of " . ($config['CompanyName'] ?? 'SpeedRadius') . ", an ISP management system. You can see real-time revenue, customer records, plans, transactions, routers, and vouchers. NEVER say you don't have access — you DO. Answer questions using the system data provided below each request. Be concise and use Kenyan Shillings (KES) for all amounts.";
    $system_prompt  = !empty($config['deepseek_system_prompt']) ? $config['deepseek_system_prompt'] : $default_prompt;

    $model = !empty($config['deepseek_model']) ? $config['deepseek_model'] : 'deepseek-chat';

    // ── Query live system data ──
    $sysData = deepseek_get_system_data();

    // Build messages array
    $messages = [['role' => 'system', 'content' => $system_prompt]];

    // Inject system data as a standalone system message (harder for AI to ignore)
    if (!empty($sysData)) {
        $messages[] = ['role' => 'system', 'content' => "LIVE DATABASE DATA — Use this to answer the user's question:\n\n" . $sysData];
    }
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
        'stream'      => true,
    ]);

    // ── Streaming: output SSE as chunks arrive from DeepSeek ──
    @ini_set('output_buffering', 'off');
    @ini_set('zlib.output_compression', false);
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('X-Accel-Buffering: no');

    $ch = curl_init('https://api.deepseek.com/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key,
            'Accept: text/event-stream',
        ],
        CURLOPT_TIMEOUT        => 120,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_WRITEFUNCTION  => function($ch, $data) {
            $lines = explode("\n", $data);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || $line === 'data: [DONE]') continue;
                if (strpos($line, 'data: ') === 0) {
                    $json = substr($line, 6);
                    $chunk = json_decode($json, true);
                    $delta = $chunk['choices'][0]['delta']['content'] ?? '';
                    if ($delta !== '') {
                        echo "data: " . json_encode(['c' => $delta]) . "\n\n";
                        if (ob_get_level()) ob_flush();
                        flush();
                    }
                }
            }
            return strlen($data);
        },
    ]);

    curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        echo "data: " . json_encode(['e' => 'Connection error: ' . $curl_error]) . "\n\n";
    } elseif ($http_code !== 200) {
        echo "data: " . json_encode(['e' => 'DeepSeek API error (HTTP ' . $http_code . ')']) . "\n\n";
    }
    echo "data: [DONE]\n\n";
    exit;
}

/**
 * Query live system data to inject into AI context
 */
function deepseek_get_system_data()
{
    $lines = [];

    try {
        $yesterday  = date('Y-m-d', strtotime('-1 day'));
        $today      = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $weekAgo    = date('Y-m-d', strtotime('-7 days'));

        // ═══════════════════════════════════════════
        // REVENUE
        // ═══════════════════════════════════════════
        $revYesterday = ORM::for_table('tbl_transactions')
            ->where_raw("DATE(created_at) = ?", [$yesterday])->where('status', 'paid')->sum('amount');
        $revToday = ORM::for_table('tbl_transactions')
            ->where_raw("DATE(created_at) = ?", [$today])->where('status', 'paid')->sum('amount');
        $revWeek = ORM::for_table('tbl_transactions')
            ->where_gte('created_at', $weekAgo . ' 00:00:00')->where('status', 'paid')->sum('amount');
        $revMonth = ORM::for_table('tbl_transactions')
            ->where_gte('created_at', $monthStart . ' 00:00:00')->where('status', 'paid')->sum('amount');

        $lines[] = "═══ REVENUE ═══";
        $lines[] = "- Today ($today): KES " . number_format($revToday ?? 0, 2);
        $lines[] = "- Yesterday ($yesterday): KES " . number_format($revYesterday ?? 0, 2);
        $lines[] = "- Last 7 days: KES " . number_format($revWeek ?? 0, 2);
        $lines[] = "- This month: KES " . number_format($revMonth ?? 0, 2);

        // Revenue by payment method this month
        $methods = ORM::for_table('tbl_transactions')
            ->select_many('method')
            ->where_gte('created_at', $monthStart . ' 00:00:00')
            ->where('status', 'paid')
            ->find_many();
        $methodTotals = [];
        foreach ($methods as $m) {
            $method = explode(' - ', $m['method'] ?? 'Unknown')[0];
            if (!isset($methodTotals[$method])) $methodTotals[$method] = 0;
            $methodTotals[$method]++;
        }
        if (!empty($methodTotals)) {
            $lines[] = "- Revenue by payment method this month:";
            foreach ($methodTotals as $method => $count) {
                $lines[] = "  • $method: $count transactions";
            }
        }

        // Day-by-day revenue (last 14 days) — same as reports/by-date
        $dailyLines = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $dayRev = ORM::for_table('tbl_transactions')
                ->where_raw("DATE(created_at) = ?", [$d])
                ->where('status', 'paid')
                ->sum('amount');
            $dayCount = ORM::for_table('tbl_transactions')
                ->where_raw("DATE(created_at) = ?", [$d])
                ->where('status', 'paid')
                ->count();
            $dailyLines[] = "  • $d — KES " . number_format($dayRev ?? 0, 2) . " ($dayCount txns)";
        }
        $lines[] = "- Daily revenue (last 14 days):";
        $lines = array_merge($lines, $dailyLines);

        // Revenue by plan this month
        try {
            $planRevenue = ORM::for_table('tbl_transactions')
                ->select_many('plan_name', 'amount')
                ->where_gte('created_at', $monthStart . ' 00:00:00')
                ->where('status', 'paid')
                ->find_many();
            $planTotals = [];
            foreach ($planRevenue as $pr) {
                $pn = $pr['plan_name'] ?? 'Unknown';
                if (!isset($planTotals[$pn])) $planTotals[$pn] = ['count' => 0, 'amount' => 0];
                $planTotals[$pn]['count']++;
                $planTotals[$pn]['amount'] += ($pr['amount'] ?? 0);
            }
            if (!empty($planTotals)) {
                $lines[] = "- Revenue by plan this month:";
                foreach ($planTotals as $pn => $data) {
                    $lines[] = "  • $pn: {$data['count']} sales, KES " . number_format($data['amount'], 2);
                }
            }
        } catch (Exception $e) {}

        // Revenue by transaction type this month
        try {
            $typeRevenue = ORM::for_table('tbl_transactions')
                ->select_many('type', 'amount')
                ->where_gte('created_at', $monthStart . ' 00:00:00')
                ->where('status', 'paid')
                ->find_many();
            $typeTotals = [];
            foreach ($typeRevenue as $tr) {
                $tp = $tr['type'] ?? 'Unknown';
                if (!isset($typeTotals[$tp])) $typeTotals[$tp] = ['count' => 0, 'amount' => 0];
                $typeTotals[$tp]['count']++;
                $typeTotals[$tp]['amount'] += ($tr['amount'] ?? 0);
            }
            if (!empty($typeTotals)) {
                $lines[] = "- Revenue by transaction type this month:";
                foreach ($typeTotals as $tp => $data) {
                    $lines[] = "  • $tp: {$data['count']} txns, KES " . number_format($data['amount'], 2);
                }
            }
        } catch (Exception $e) {}

        // ═══════════════════════════════════════════
        // CUSTOMERS
        // ═══════════════════════════════════════════
        $totalCust = ORM::for_table('tbl_customers')->count();
        $activeCust = ORM::for_table('tbl_user_recharges')
            ->where('status', 'on')->where_raw("expires > NOW()")->count();
        $expiredCust = ORM::for_table('tbl_user_recharges')
            ->where('status', 'on')->where_raw("expires <= NOW()")->count();
        $expiringSoon = ORM::for_table('tbl_user_recharges')
            ->where('status', 'on')
            ->where_raw("expires > NOW() AND expires <= DATE_ADD(NOW(), INTERVAL 3 DAY)")
            ->count();

        $lines[] = "";
        $lines[] = "═══ CUSTOMERS ═══";
        $lines[] = "- Total customers in system: $totalCust";
        $lines[] = "- Active (service not expired): $activeCust";
        $lines[] = "- Expired (service past due): $expiredCust";
        $lines[] = "- Expiring within 3 days: $expiringSoon";

        // Customer types breakdown
        try {
            $pppoe = ORM::for_table('tbl_customers')->where('service_type', 'PPPoE')->count();
            $hotspot = ORM::for_table('tbl_customers')->where('service_type', 'Hotspot')->count();
            $staticIP = ORM::for_table('tbl_customers')->where('service_type', 'Static')->count();
            $lines[] = "- By service type: PPPoE=$pppoe, Hotspot=$hotspot, Static=$staticIP";
        } catch (Exception $e) {}

        // ═══════════════════════════════════════════
        // PLANS / PACKAGES
        // ═══════════════════════════════════════════
        try {
            $plans = ORM::for_table('tbl_plans')->find_many();
            if (count($plans) > 0) {
                $lines[] = "";
                $lines[] = "═══ PLANS ═══";
                foreach ($plans as $p) {
                    $name = $p['name_plan'] ?? $p['name'] ?? 'Unknown';
                    $price = $p['price'] ?? '0';
                    $type = $p['type'] ?? '';
                    $lines[] = "- $name: KES $price ($type)";
                }
            }
        } catch (Exception $e) {}

        // ═══════════════════════════════════════════
        // ROUTERS
        // ═══════════════════════════════════════════
        try {
            $routers = ORM::for_table('tbl_routers')->find_many();
            if (count($routers) > 0) {
                $lines[] = "";
                $lines[] = "═══ ROUTERS ═══";
                foreach ($routers as $r) {
                    $name = $r['name'] ?? 'Unknown';
                    $ip = $r['ip_address'] ?? '';
                    $lines[] = "- $name ($ip)";
                }
            }
        } catch (Exception $e) {}

        // ═══════════════════════════════════════════
        // VOUCHERS
        // ═══════════════════════════════════════════
        try {
            $vTotal = ORM::for_table('tbl_voucher')->count();
            $vUnused = ORM::for_table('tbl_voucher')->where('status', '0')->count();
            $vUsed = ORM::for_table('tbl_voucher')->where('status', '1')->count();
            $lines[] = "";
            $lines[] = "═══ VOUCHERS ═══";
            $lines[] = "- Total vouchers: $vTotal (Unused: $vUnused, Used: $vUsed)";
        } catch (Exception $e) {}

        // ═══════════════════════════════════════════
        // RECENT ACTIVITY
        // ═══════════════════════════════════════════
        $recentTxns = ORM::for_table('tbl_transactions')
            ->where('status', 'paid')
            ->order_by_desc('id')->limit(10)->find_many();
        if (count($recentTxns) > 0) {
            $lines[] = "";
            $lines[] = "═══ LAST 10 PAYMENTS ═══";
            foreach ($recentTxns as $r) {
                $user = $r['username'] ?? 'unknown';
                $plan = $r['plan_name'] ?? '';
                $amt  = number_format($r['amount'] ?? 0, 2);
                $method = explode(' - ', $r['method'] ?? 'Unknown')[0];
                $date = date('Y-m-d H:i', strtotime($r['created_at'] ?? ''));
                $lines[] = "  • $user — $plan — KES $amt via $method — $date";
            }
        }

        // ═══════════════════════════════════════════
        // EXPIRING CUSTOMERS LIST
        // ═══════════════════════════════════════════
        $expiringList = ORM::for_table('tbl_user_recharges')
            ->where('status', 'on')
            ->where_raw("expires > NOW() AND expires <= DATE_ADD(NOW(), INTERVAL 3 DAY)")
            ->order_by_asc('expires')->limit(10)->find_many();
        if (count($expiringList) > 0) {
            $lines[] = "";
            $lines[] = "═══ EXPIRING WITHIN 3 DAYS ═══";
            foreach ($expiringList as $e) {
                $name = $e['namebp'] ?? $e['username'] ?? 'unknown';
                $exp  = date('Y-m-d H:i', strtotime($e['expires'] ?? ''));
                $lines[] = "  • $name — expires $exp";
            }
        }

        return implode("\n", $lines);
    } catch (Exception $e) {
        return '';
    }
}
