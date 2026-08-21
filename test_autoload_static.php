<?php
/**
 * Test Autoload Plan Endpoint
 */

require_once 'init.php';

header('Content-Type: text/plain');

echo "=== Test Autoload Plan Endpoint ===\n\n";

// Simulate the AJAX request
$_POST['server'] = 'SPEEDCOM3';
$_POST['jenis'] = 'Static';

echo "Testing autoload with:\n";
echo "  Server: " . $_POST['server'] . "\n";
echo "  Type (jenis): " . $_POST['jenis'] . "\n\n";

// Fetch plans like the autoload controller does
$server = $_POST['server'];
$jenis = $_POST['jenis'];

echo "Query 1: Plans for server '$server' and type '$jenis':\n";
$plans = ORM::for_table('tbl_plans')
    ->where('routers', $server)
    ->where('type', $jenis)
    ->find_many();

echo "Found " . count($plans) . " plan(s)\n\n";

if (count($plans) > 0) {
    foreach ($plans as $plan) {
        echo "Plan: " . $plan->name_plan . " (ID: " . $plan->id . ")\n";
        echo "  Price: " . $plan->price . "\n";
        echo "  Enabled: " . ($plan->enabled ? 'Yes' : 'No') . "\n";
        echo "  Prepaid: " . $plan->prepaid . "\n";
        echo "\n";
    }
    
    echo "HTML Output (what would be returned):\n";
    echo "---\n";
    echo '<option value="">Select Plans</option>' . "\n";
    foreach ($plans as $ds) {
        echo '<option value="' . $ds->id . '">';
        if ($ds->enabled != 1) echo 'DISABLED PLAN &bull; ';
        echo $ds->name_plan . ' &bull; ' . $ds->price;
        if ($ds->prepaid != 'yes') echo ' &bull; POSTPAID';
        echo '</option>' . "\n";
    }
    echo "---\n\n";
    
    echo "✅ Autoload should work correctly!\n\n";
    echo "If plans still not showing:\n";
    echo "1. Clear browser cache (Ctrl+Shift+Delete)\n";
    echo "2. Hard refresh (Ctrl+F5)\n";
    echo "3. Try incognito/private window\n";
    echo "4. Check browser console for JavaScript errors (F12)\n";
} else {
    echo "❌ No plans found!\n";
}

// Also test enabled plans only (for non-admin)
echo "\nQuery 2: Enabled plans only:\n";
$enabledPlans = ORM::for_table('tbl_plans')
    ->where('routers', $server)
    ->where('type', $jenis)
    ->where('enabled', '1')
    ->find_many();
    
echo "Found " . count($enabledPlans) . " enabled plan(s)\n";
