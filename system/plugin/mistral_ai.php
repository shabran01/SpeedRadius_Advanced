<?php
/**
 * Mistral AI Chat Assistant Plugin
 * Provides an AI-powered chat assistant for ISP administrators
 * Powered by Mistral AI API (https://mistral.ai)
 */

register_menu("Mistral AI", true, "mistral_ai", 'AFTER_SETTINGS', 'ion ion-chatbubbles', "AI", "purple", ['Admin', 'SuperAdmin']);


/**
 * Main plugin router
 */
function mistral_ai()
{
    global $ui, $config, $routes;
    $action = $routes['2'] ?? '';

    switch ($action) {
        case 'config':
            mistral_ai_config_page();
            return;
        case 'api':
            mistral_ai_handle_api();
            return;
        case 'token':
            mistral_ai_get_token();
            return;
        default:
            mistral_ai_chat_page();
            return;
    }
}

/**
 * Full-page chat interface
 */
function mistral_ai_chat_page()
{
    global $ui, $config;
    _admin();
    $admin = Admin::_info();

    if (empty($config['mistral_api_key'])) {
        r2(U . 'plugin/mistral_ai/config', 'e', 'Please configure your Mistral AI API key first.');
    }

    $ui->assign('_title', 'Mistral AI Assistant');
    $ui->assign('_system_menu', 'plugin/mistral_ai');
    $ui->assign('_admin', $admin);
    $ui->assign('csrf_token', Csrf::generateAndStoreToken());
    $ui->display('mistral_ai.tpl');
}

/**
 * Configuration page (save API key, system prompt, model)
 */
function mistral_ai_config_page()
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
            r2(U . 'plugin/mistral_ai/config', 'e', 'Invalid or Expired CSRF Token.');
        }

        $fields = [
            'mistral_api_key',
            'mistral_system_prompt',
            'mistral_model',
        ];

        foreach ($fields as $field) {
            $val = _post($field);
            // Never store empty API key over a valid one
            if ($field === 'mistral_api_key' && empty($val) && !empty($config['mistral_api_key'])) {
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

        r2(U . 'plugin/mistral_ai', 's', 'Configuration saved successfully.');
    }

    $ui->assign('_title', 'Mistral AI — Configuration');
    $ui->assign('_system_menu', 'plugin/mistral_ai');
    $ui->assign('_admin', $admin);
    $ui->assign('csrf_token', Csrf::generateAndStoreToken());
    $ui->display('mistral_ai_config.tpl');
}

/**
 * Returns a fresh CSRF token as JSON (used by the floating widget)
 */
function mistral_ai_get_token()
{
    _admin();
    header('Content-Type: application/json');
    echo json_encode(['token' => Csrf::generateAndStoreToken()]);
    exit;
}

/**
 * AJAX endpoint — proxies message to Mistral AI API and returns the reply
 */
function mistral_ai_handle_api()
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

    $api_key = $config['mistral_api_key'] ?? '';
    if (empty($api_key)) {
        http_response_code(400);
        echo json_encode(['error' => 'Mistral AI API key not configured. Go to Mistral AI → Configuration.']);
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
    $system_prompt  = !empty($config['mistral_system_prompt']) ? $config['mistral_system_prompt'] : $default_prompt;

    $model = !empty($config['mistral_model']) ? $config['mistral_model'] : 'mistral-large-latest';

    // ── Query live system data ──
    $sysData = mistral_get_system_data();

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

    // ── Streaming: output SSE as chunks arrive from Mistral AI ──
    @ini_set('output_buffering', 'off');
    @ini_set('zlib.output_compression', false);
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('X-Accel-Buffering: no');

    $ch = curl_init('https://api.mistral.ai/v1/chat/completions');
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
        echo "data: " . json_encode(['e' => 'Mistral AI API error (HTTP ' . $http_code . ')']) . "\n\n";
    }
    echo "data: [DONE]\n\n";
    exit;
}

/**
 * Query live system data to inject into AI context
 * MIRRORS THE DASHBOARD EXACTLY — same queries, same filters, same results.
 */
function mistral_get_system_data()
{
    global $config;
    $lines = [];

    try {
        $today      = date('Y-m-d');
        $yesterday  = date('Y-m-d', strtotime('-1 day'));
        $currentMonth = date('Y-m');

        // ═══════════════════════════════════════════
        // BILLING CYCLE (matches dashboard exactly)
        // ═══════════════════════════════════════════
        $reset_day = isset($config['reset_day']) ? (int)$config['reset_day'] : 1;
        $reset_day = max(1, min(28, $reset_day));
        $current_day = (int)date('d');
        $billing_year = (int)date('Y');
        $billing_month = (int)date('m');
        if ($current_day < $reset_day) {
            $billing_month--;
            if ($billing_month < 1) {
                $billing_month = 12;
                $billing_year--;
            }
        }
        $billingStart = sprintf('%04d-%02d-%02d', $billing_year, $billing_month, $reset_day);
        $calendarMonthStart = date('Y-m-01');
        $lastMonthStart = date('Y-m-01', strtotime('-1 month'));
        $lastMonthEnd   = date('Y-m-t', strtotime('-1 month'));

        // ── INCOME FILTER (matches dashboard: excludes Balance transactions) ──
        $excludeMethods = ["Customer - Balance", "Recharge Balance - Administrator"];

        // ═══════════════════════════════════════════
        // 1. INCOME — TODAY (matches $iday on dashboard)
        // ═══════════════════════════════════════════
        $iday = ORM::for_table('tbl_transactions')
            ->where('recharged_on', $today)
            ->where_not_in('method', $excludeMethods)
            ->sum('price');

        // ═══════════════════════════════════════════
        // 2. INCOME — BILLING MONTH (matches $imonth on dashboard)
        // ═══════════════════════════════════════════
        $imonth = ORM::for_table('tbl_transactions')
            ->where_not_in('method', $excludeMethods)
            ->where_gte('recharged_on', $billingStart)
            ->where_lte('recharged_on', $today)
            ->sum('price');

        // ═══════════════════════════════════════════
        // 3. REVENUE — THIS CALENDAR MONTH vs LAST MONTH
        // ═══════════════════════════════════════════
        $thisMonthRevenue = ORM::for_table('tbl_transactions')
            ->where_not_in('method', $excludeMethods)
            ->where_gte('recharged_on', $calendarMonthStart)
            ->where_lte('recharged_on', date('Y-m-t'))
            ->sum('price');
        $lastMonthRevenue = ORM::for_table('tbl_transactions')
            ->where_not_in('method', $excludeMethods)
            ->where_gte('recharged_on', $lastMonthStart)
            ->where_lte('recharged_on', $lastMonthEnd)
            ->sum('price');
        $revenueChange = ($thisMonthRevenue ?? 0) - ($lastMonthRevenue ?? 0);
        $revenueChangePct = ($lastMonthRevenue > 0) ? round(($revenueChange / $lastMonthRevenue) * 100, 1) : 0;

        // ═══════════════════════════════════════════
        // INCOME SUMMARY (matches dashboard cards exactly)
        // ═══════════════════════════════════════════
        $lines[] = "═══ INCOME (DASHBOARD-ACCURATE) ═══";
        $lines[] = "Today ($today): KES " . number_format($iday ?? 0, 2);
        $lines[] = "Billing Month (since $billingStart): KES " . number_format($imonth ?? 0, 2);
        $lines[] = "This Calendar Month: KES " . number_format($thisMonthRevenue ?? 0, 2);
        $lines[] = "Last Calendar Month: KES " . number_format($lastMonthRevenue ?? 0, 2);
        $lines[] = "Month-over-Month Change: " . ($revenueChange >= 0 ? '+' : '') . "KES " . number_format($revenueChange, 2) . " (" . ($revenueChangePct >= 0 ? '+' : '') . $revenueChangePct . "%)";

        // ═══════════════════════════════════════════
        // 4. TOP 5 PLANS THIS MONTH (matches dashboard)
        // ═══════════════════════════════════════════
        try {
            $topPlans = ORM::for_table('tbl_transactions')
                ->select_many('plan_name')
                ->select_expr('COUNT(*)', 'subscription_count')
                ->select_expr('SUM(price)', 'total_revenue')
                ->where_not_in('method', $excludeMethods)
                ->where_gte('recharged_on', $calendarMonthStart)
                ->group_by('plan_name')
                ->order_by_desc('subscription_count')
                ->limit(5)
                ->find_many();
            if (count($topPlans) > 0) {
                $lines[] = "";
                $lines[] = "═══ TOP 5 PLANS THIS MONTH ═══";
                $rank = 1;
                foreach ($topPlans as $tp) {
                    $pn = $tp['plan_name'] ?? 'Unknown';
                    $sc = $tp['subscription_count'] ?? 0;
                    $tr = number_format($tp['total_revenue'] ?? 0, 2);
                    $lines[] = "  $rank. $pn — $sc subscriptions, KES $tr";
                    $rank++;
                }
            }
        } catch (Exception $e) {}

        // ═══════════════════════════════════════════
        // 5. ONLINE USERS (matches dashboard: hotspot + PPPoE online)
        // ═══════════════════════════════════════════
        $onlineHotspot = ORM::for_table('tbl_user_recharges')
            ->where('status', 'on')->where('type', 'Hotspot')->count();
        $onlinePPPoE = ORM::for_table('tbl_user_recharges')
            ->where('status', 'on')->where('type', 'PPPOE')->count();
        $totalOnline = ($onlineHotspot ?? 0) + ($onlinePPPoE ?? 0);

        // ═══════════════════════════════════════════
        // 6. SUBSCRIPTION COUNTS (matches dashboard)
        // ═══════════════════════════════════════════
        $u_all = ORM::for_table('tbl_user_recharges')->count();        // Total subscriptions
        $u_act = ORM::for_table('tbl_user_recharges')->where('status', 'on')->count(); // Active subscriptions
        $u_exp = $u_all - $u_act;                                       // Expired/Inactive

        // ═══════════════════════════════════════════
        // 7. TOTAL CUSTOMERS (matches dashboard)
        // ═══════════════════════════════════════════
        $c_all = ORM::for_table('tbl_customers')->count();

        // ═══════════════════════════════════════════
        // 8. EXPIRED BY TYPE (matches dashboard)
        // ═══════════════════════════════════════════
        $expiredPPPoE = ORM::for_table('tbl_user_recharges')
            ->where('status', 'off')->where('type', 'PPPOE')
            ->where_lte('expiration', $today)
            ->count();
        $expiredHotspot = ORM::for_table('tbl_user_recharges')
            ->where('status', 'off')->where('type', 'Hotspot')
            ->where_lte('expiration', $today)
            ->count();
        $totalExpired = ($expiredPPPoE ?? 0) + ($expiredHotspot ?? 0);

        $lines[] = "";
        $lines[] = "═══ SUBSCRIPTIONS & CUSTOMERS ═══";
        $lines[] = "Total Customers: $c_all";
        $lines[] = "Total Subscriptions: $u_all";
        $lines[] = "Active Subscriptions (status=on): $u_act";
        $lines[] = "Expired/Inactive Subscriptions: $u_exp";
        $lines[] = "Currently Online: $totalOnline (Hotspot: " . ($onlineHotspot ?? 0) . ", PPPoE: " . ($onlinePPPoE ?? 0) . ")";
        $lines[] = "Expired Subscriptions: $totalExpired (PPPoE: " . ($expiredPPPoE ?? 0) . ", Hotspot: " . ($expiredHotspot ?? 0) . ")";

        // ═══════════════════════════════════════════
        // 9. CUSTOMER BALANCES (if enabled)
        // ═══════════════════════════════════════════
        if (isset($config['enable_balance']) && $config['enable_balance'] == 'yes') {
            $totalBalance = ORM::for_table('tbl_customers')
                ->where_gte('balance', 0)->sum('balance');
            $lines[] = "Total Customer Balances: KES " . number_format($totalBalance ?? 0, 2);
        }

        // ═══════════════════════════════════════════
        // 10. CUSTOMERS EXPIRING TODAY (matches dashboard)
        // ═══════════════════════════════════════════
        $expToday = ORM::for_table('tbl_user_recharges')
            ->where_lte('expiration', $today)
            ->where_gte('expiration', date('Y-m-d', strtotime('-1 day')))
            ->count();
        if ($expToday > 0) {
            $lines[] = "Users Expiring Today: $expToday";
        }

        // ═══════════════════════════════════════════
        // 11. SERVICE TYPE BREAKDOWN
        // ═══════════════════════════════════════════
        try {
            $pppoeCust = ORM::for_table('tbl_customers')->where('service_type', 'PPPoE')->count();
            $hotspotCust = ORM::for_table('tbl_customers')->where('service_type', 'Hotspot')->count();
            $staticCust = ORM::for_table('tbl_customers')->where('service_type', 'Static')->count();
            $lines[] = "By Service Type: PPPoE=$pppoeCust, Hotspot=$hotspotCust, Static=$staticCust";
        } catch (Exception $e) {}

        // ═══════════════════════════════════════════
        // 12. ALL PLANS (with active subscriber counts)
        // ═══════════════════════════════════════════
        try {
            $plans = ORM::for_table('tbl_plans')->find_many();
            if (count($plans) > 0) {
                $lines[] = "";
                $lines[] = "═══ ALL PLANS ═══";
                foreach ($plans as $p) {
                    $name = $p['name_plan'] ?? $p['name'] ?? 'Unknown';
                    $price = $p['price'] ?? '0';
                    $type = $p['type'] ?? '';
                    $planId = $p['id'] ?? 0;
                    $activeOnPlan = ORM::for_table('tbl_user_recharges')
                        ->where('plan_id', $planId)->where('status', 'on')->count();
                    $lines[] = "- $name: KES $price ($type) — $activeOnPlan active subscribers";
                }
            }
        } catch (Exception $e) {}

        // ═══════════════════════════════════════════
        // 13. ROUTERS
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
        // 14. VOUCHERS
        // ═══════════════════════════════════════════
        try {
            $vTotal = ORM::for_table('tbl_voucher')->count();
            $vUnused = ORM::for_table('tbl_voucher')->where('status', '0')->count();
            $vUsed = ORM::for_table('tbl_voucher')->where('status', '1')->count();
            $lines[] = "";
            $lines[] = "═══ VOUCHERS ═══";
            $lines[] = "Total: $vTotal | Unused: $vUnused | Used: $vUsed";
        } catch (Exception $e) {}

        // ═══════════════════════════════════════════
        // 15. RECENT ACTIVITY LOG (last 5 entries)
        // ═══════════════════════════════════════════
        try {
            $logs = ORM::for_table('tbl_logs')->order_by_desc('id')->limit(5)->find_many();
            if (count($logs) > 0) {
                $lines[] = "";
                $lines[] = "═══ RECENT ACTIVITY LOG ═══";
                foreach ($logs as $l) {
                    $desc = $l['description'] ?? '';
                    $type = $l['type'] ?? '';
                    $d = isset($l['created_at']) ? date('Y-m-d H:i', strtotime($l['created_at'])) : '';
                    $lines[] = "  • [$type] $desc — $d";
                }
            }
        } catch (Exception $e) {}

        // ═══════════════════════════════════════════
        // 16. LAST 10 PAYMENTS (paid transactions)
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
                $amt  = number_format($r['price'] ?? $r['amount'] ?? 0, 2);
                $method = explode(' - ', $r['method'] ?? 'Unknown')[0];
                $date = isset($r['recharged_on']) ? $r['recharged_on'] : (isset($r['created_at']) ? date('Y-m-d H:i', strtotime($r['created_at'])) : '');
                $lines[] = "  • $user — $plan — KES $amt via $method — $date";
            }
        }

        // ═══════════════════════════════════════════
        // 17. EXPIRING WITHIN 3 DAYS
        // ═══════════════════════════════════════════
        $expiringList = ORM::for_table('tbl_user_recharges')
            ->where('status', 'on')
            ->where_raw("expiration > NOW() AND expiration <= DATE_ADD(NOW(), INTERVAL 3 DAY)")
            ->order_by_asc('expiration')->limit(10)->find_many();
        if (count($expiringList) > 0) {
            $lines[] = "";
            $lines[] = "═══ EXPIRING WITHIN 3 DAYS ═══";
            foreach ($expiringList as $e) {
                $name = $e['namebp'] ?? $e['username'] ?? 'unknown';
                $exp  = isset($e['expiration']) ? date('Y-m-d H:i', strtotime($e['expiration'])) : (isset($e['expires']) ? date('Y-m-d H:i', strtotime($e['expires'])) : '');
                $lines[] = "  • $name — expires $exp";
            }
        }

        // ═══════════════════════════════════════════
        // 18. TOP DATA USERS THIS MONTH
        // ═══════════════════════════════════════════
        try {
            $topHotspotUsers = ORM::raw_execute(
                "SELECT mu.download_bytes, mu.upload_bytes, c.username, c.fullname
                 FROM tbl_customer_monthly_usage mu
                 INNER JOIN tbl_customers c ON c.id = mu.customer_id
                 WHERE mu.month = :month
                 AND (c.pppoe_username IS NULL OR c.pppoe_username = '')
                 ORDER BY mu.download_bytes DESC
                 LIMIT 5",
                ['month' => $currentMonth]
            )->find_many();
            if (count($topHotspotUsers) > 0) {
                $lines[] = "";
                $lines[] = "═══ TOP 5 HOTSPOT DATA USERS ($currentMonth) ═══";
                $rank = 1;
                foreach ($topHotspotUsers as $u) {
                    $name = $u['fullname'] ?? $u['username'] ?? 'unknown';
                    $dl = mistral_format_bytes($u['download_bytes'] ?? 0);
                    $ul = mistral_format_bytes($u['upload_bytes'] ?? 0);
                    $lines[] = "  $rank. $name — ↓$dl ↑$ul";
                    $rank++;
                }
            }

            $topPPPoEUsers = ORM::raw_execute(
                "SELECT mu.download_bytes, mu.upload_bytes, c.username, c.fullname, c.pppoe_username
                 FROM tbl_customer_monthly_usage mu
                 INNER JOIN tbl_customers c ON c.id = mu.customer_id
                 WHERE mu.month = :month
                 AND c.pppoe_username IS NOT NULL
                 AND c.pppoe_username != ''
                 ORDER BY mu.download_bytes DESC
                 LIMIT 5",
                ['month' => $currentMonth]
            )->find_many();
            if (count($topPPPoEUsers) > 0) {
                $lines[] = "";
                $lines[] = "═══ TOP 5 PPPoE DATA USERS ($currentMonth) ═══";
                $rank = 1;
                foreach ($topPPPoEUsers as $u) {
                    $name = $u['fullname'] ?? $u['username'] ?? 'unknown';
                    $dl = mistral_format_bytes($u['download_bytes'] ?? 0);
                    $ul = mistral_format_bytes($u['upload_bytes'] ?? 0);
                    $lines[] = "  $rank. $name — ↓$dl ↑$ul";
                    $rank++;
                }
            }

            // Network-wide usage total
            $netUsage = ORM::raw_execute(
                "SELECT COALESCE(SUM(download_bytes),0) AS dl, COALESCE(SUM(upload_bytes),0) AS ul
                 FROM tbl_router_monthly_usage
                 WHERE month = :month",
                ['month' => $currentMonth]
            )->find_one();
            if ($netUsage) {
                $dl = mistral_format_bytes($netUsage['dl'] ?? 0);
                $ul = mistral_format_bytes($netUsage['ul'] ?? 0);
                $lines[] = "";
                $lines[] = "═══ NETWORK USAGE ($currentMonth) ═══";
                $lines[] = "Total Network: ↓$dl ↑$ul";
            }
        } catch (Exception $e) {}

        return implode("\n", $lines);
    } catch (Exception $e) {
        return '';
    }
}

/**
 * Format bytes to human-readable
 */
function mistral_format_bytes($bytes)
{
    $bytes = (int)$bytes;
    if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576)    return round($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024)       return round($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}
