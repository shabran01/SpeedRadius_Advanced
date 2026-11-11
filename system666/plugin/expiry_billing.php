<?php
register_menu("Expiry Billing", true, "expiry_billing", 'AFTER_REPORTS', 'ion ion-ios-time', '', '', ['Admin', 'SuperAdmin']);

function expiry_billing()
{
    global $ui, $config, $admin;

    _admin();

    // Load existing expiry details if they exist
    $expiry_data_file = 'expiry_data.json';
    $expiry_data = file_exists($expiry_data_file) ? json_decode(file_get_contents($expiry_data_file), true) : [];

    // Check if form was submitted to update expiry date
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expiry_date'])) {
        // Sanitize and save the new expiry date
        $new_expiry_date = htmlspecialchars(trim($_POST['expiry_date']));
        $expiry_data['expiry_date'] = $new_expiry_date;

        // Save the expiry data to a JSON file
        file_put_contents($expiry_data_file, json_encode($expiry_data));
        $ui->assign('message', 'Expiry date updated successfully.');
    }

    // Assign data to UI
    $current_expiry_date = $expiry_data['expiry_date'] ?? null;
    $time_left = null;
    $expiry_timestamp = $current_expiry_date ? strtotime($current_expiry_date) : null;

    if ($expiry_timestamp) {
        $time_left = max(0, $expiry_timestamp - time());
    }

    $ui->assign('current_expiry_date', $current_expiry_date);
    $ui->assign('time_left', $time_left);
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);

    // Display the template
    $ui->assign('_title', 'Expiry Billing');
    $ui->display('expiry_billing.tpl');
}
