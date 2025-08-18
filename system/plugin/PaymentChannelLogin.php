<?php
require_once '../../config.php';
require_once '../../init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['status' => 'error', 'message' => 'Method not allowed']));
}

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);
$phone_number = isset($data['phone_number']) ? $data['phone_number'] : '';

if (empty($phone_number)) {
    die(json_encode(['status' => 'error', 'message' => 'Phone number is required']));
}

// Connect to database
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($mysqli->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed']));
}

// Check for active subscription
$query = $mysqli->prepare("SELECT u.*, p.name_plan, p.validity, p.validity_unit 
                         FROM tbl_transactions t 
                         JOIN tbl_user_recharges u ON t.user_id = u.user_id 
                         JOIN tbl_plans p ON u.plan_id = p.id 
                         WHERE t.payment_channel IS NOT NULL 
                         AND t.phone_number = ? 
                         AND u.status = 'active' 
                         ORDER BY t.id DESC LIMIT 1");

$query->bind_param("s", $phone_number);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if ($user) {
    // Generate authentication token or session
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['phone'] = $phone_number;
    $_SESSION['plan'] = $user['name_plan'];
    
    // Return success response with redirect URL
    echo json_encode([
        'status' => 'success',
        'message' => 'Login successful',
        'redirect_url' => APP_URL . '/index.php?_route=dashboard',
        'user' => [
            'phone' => $phone_number,
            'plan' => $user['name_plan'],
            'validity' => $user['validity'] . ' ' . $user['validity_unit']
        ]
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No active subscription found for this phone number'
    ]);
}

$query->close();
$mysqli->close();
