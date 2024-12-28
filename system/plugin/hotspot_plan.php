<?php
// Assuming you have ORM or database access configured correctly

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['routername'])) {
    // Example of fetching data (simplified)
    $routerName = $_POST['routername'];

    // Fetch routers and hotspot plans from database
    $routers = ORM::for_table('tbl_routers')->find_many();
    $plans_hotspot = ORM::for_table('tbl_plans')->where('type', 'hotspot')->find_many(); // Filter for hotspot plans

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

            // Filter and collect hotspot plans associated with the router
            foreach ($plans_hotspot as $plan) {
                if ($router['name'] == $plan['routers']) {
                    $plan_id = $plan['id'];
                    $bandwidth_data = isset($bandwidth_map[$plan_id]) ? $bandwidth_map[$plan_id] : [];
                    
                    // Construct payment link using $_url
                    $paymentlink = "https://codevibeisp.co.ke/index.php?_route=plugin/hotspot_pay&routerName={$router['name']}&planId={$plan['id']}&routerId={$router['id']}";
                    
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
                        'routerId' => $router['id']
                    ];
                }
            }

            // Add router data to $data array
            $data[] = $routerData;
        }
    }

    // Respond with JSON data
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *'); // Adjust this based on your CORS requirements
    echo json_encode(['data' => $data], JSON_PRETTY_PRINT);
    exit();
}
?>
