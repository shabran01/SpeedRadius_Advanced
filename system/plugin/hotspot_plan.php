<?php
// Assuming you have ORM or database access configured correctly

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['routername'])) {
    // Example of fetching data (simplified)
    $routerName = $_POST['routername'];

    // Fetch routers and hotspot plans from database
    $routers = ORM::for_table('tbl_routers')->find_many();
    // Only Active plans may be purchased. This matches the customer portal
    // (order.php), which also filters on enabled = '1'. Without this filter a
    // package switched to "Not Active" stayed visible on the hotspot page.
    $plans_hotspot = ORM::for_table('tbl_plans')
        ->where('type', 'hotspot')
        ->where('enabled', '1')
        ->find_many();

    // Fetch bandwidth limits for all plans
    $bandwidth_limits = ORM::for_table('tbl_bandwidth')->find_many();
    $bandwidth_map = [];
    foreach ($bandwidth_limits as $limit) {
        $bandwidth_map[$limit['plan_id']] = [
            'downlimit' => $limit['rate_down'],
            'uplimit' => $limit['rate_up'],
        ];
    }

    // Fetch currency from tbl_appconfig using the correct column names
    $currency_config = ORM::for_table('tbl_appconfig')->where('setting', 'currency_code')->find_one();
    $currency = $currency_config ? $currency_config->value : 'Ksh'; // Default to 'Ksh' if not found

    // Initialize empty data array to store router-specific plans
    $data = [];

    // Process each router to filter and collect hotspot plans
    foreach ($routers as $router) {
        if ($router['name'] === $routerName) { // Check if router name matches POSTed routername
            $routerData = [
                'name' => $router['name'],
                'router_id' => $router['id'],
                'description' => $router['description'],
                'plans_hotspot' => [],
            ];

            // Which plan do customers on THIS router actually buy most?
            // Counted from tbl_transactions, which inserts one row per purchase.
            // (tbl_user_recharges cannot be used here: a repeat purchase updates
            // the existing row, so it counts subscribers, not purchases.)
            $popularPlanName = '';
            try {
                $popularStmt = ORM::get_db()->prepare(
                    "SELECT plan_name, COUNT(*) AS buys
                     FROM tbl_transactions
                     WHERE routers = ?
                       AND recharged_on >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                       AND method NOT IN ('Customer - Balance', 'Recharge Balance - Administrator')
                     GROUP BY plan_name
                     ORDER BY buys DESC
                     LIMIT 1"
                );
                $popularStmt->execute([$router['name']]);
                $popularRow = $popularStmt->fetch(PDO::FETCH_ASSOC);
                if ($popularRow) {
                    $popularPlanName = (string) $popularRow['plan_name'];
                }
            } catch (Exception $e) {
                $popularPlanName = '';
            }

            // Filter and collect hotspot plans associated with the router
            foreach ($plans_hotspot as $plan) {
                if ($router['name'] == $plan['routers']) {
                    $plan_id = $plan['id'];
                    $bandwidth_data = isset($bandwidth_map[$plan_id]) ? $bandwidth_map[$plan_id] : [];
                    
                    // Build the payment link from this installation's own URL.
                    $paymentlink = APP_URL . "/index.php?_route=plugin/hotspot_pay&routerName=" . urlencode($router['name']) . "&planId={$plan['id']}&routerId={$router['id']}";
                    
                    // Prepare plan data to be sent in JSON response
                    $routerData['plans_hotspot'][] = [
                        'plantype' => $plan['type'],
                        'planname' => $plan['name_plan'],
                        'currency' => $currency,
                        'price' => $plan['price'],
                        'validity' => $plan['validity'],
                        'device' => $plan['shared_users'],
                        'datalimit' => $plan['data_limit'],
                        'timelimit' => $plan['validity_unit'] ?? null,
                        'downlimit' => $bandwidth_data['downlimit'] ?? null,
                        'uplimit' => $bandwidth_data['uplimit'] ?? null,
                        'paymentlink' => $paymentlink,
                        'planId' => $plan['id'],
                        'routerName' => $router['name'],
                        'routerId' => $router['id'],
                        'popular' => ($popularPlanName !== '' && $plan['name_plan'] === $popularPlanName)
                    ];
                }
            }

            // Add router data to $data array
            $data[] = $routerData;
        }
    }

    // Fetch company name and phone from tbl_appconfig for real-time injection
    $company_config = ORM::for_table('tbl_appconfig')->where('setting', 'CompanyName')->find_one();
    $phone_config   = ORM::for_table('tbl_appconfig')->where('setting', 'phone')->find_one();
    $company_name   = $company_config ? $company_config->value : '';
    $phone_number   = $phone_config   ? $phone_config->value   : '';

    // Respond with JSON data
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *'); // Adjust this based on your CORS requirements
    echo json_encode([
        'data'    => $data,
        'company' => $company_name,
        'phone'   => $phone_number,
    ], JSON_PRETTY_PRINT);
    exit();
}
?>
