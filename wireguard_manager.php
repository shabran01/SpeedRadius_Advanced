<?php
require 'vendor/autoload.php';

use phpseclib3\Net\SSH2;

// Remote server configuration
$remote_server = [
    'host' => '84.46.244.95',
    'user' => 'root',
    'password' => 'Mutichikweyu05',
    'port' => 22
];

// Initialize SSH connection
$ssh = new SSH2($remote_server['host'], $remote_server['port']);
if (!$ssh->login($remote_server['user'], $remote_server['password'])) {
    die('Login Failed');
}

// Function to execute commands on the remote server
function executeCommand($command) {
    global $ssh;
    return $ssh->exec($command);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $ip = $_POST['ip'];
        
        // Add user to WireGuard
        $command = "wg genkey | tee /etc/wireguard/privatekeys/{$username}.key | wg pubkey | tee /etc/wireguard/publickeys/{$username}.pub";
        executeCommand($command);
        
        $command = "echo -e '[Peer]\nPublicKey = $(cat /etc/wireguard/publickeys/{$username}.pub)\nAllowedIPs = {$ip}/32\n' | tee -a /etc/wireguard/wg0.conf";
        executeCommand($command);
        
        $command = "systemctl restart wg-quick@wg0";
        executeCommand($command);
        
        echo "User {$username} added successfully!";
    } elseif (isset($_POST['delete_user'])) {
        $username = $_POST['username'];
        
        // Delete user from WireGuard
        $command = "sed -i '/\\[Peer\\]/{:a;N;/AllowedIPs = {$username}\\/32/!ba;N;N;d}' /etc/wireguard/wg0.conf";
        executeCommand($command);
        
        $command = "rm /etc/wireguard/privatekeys/{$username}.key /etc/wireguard/publickeys/{$username}.pub";
        executeCommand($command);
        
        $command = "systemctl restart wg-quick@wg0";
        executeCommand($command);
        
        echo "User {$username} deleted successfully!";
    }
}

// Fetch existing users
$command = "grep -A 2 '\\[Peer\\]' /etc/wireguard/wg0.conf | grep 'AllowedIPs' | awk '{print $3}'";
$users = explode("\n", trim(executeCommand($command)));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>WireGuard User Management</title>
</head>
<body>
    <h1>WireGuard User Management</h1>
    
    <h2>Add User</h2>
    <form method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="ip">IP Address:</label>
        <input type="text" id="ip" name="ip" required>
        <br>
        <button type="submit" name="add_user">Add User</button>
    </form>
    
    <h2>Existing Users</h2>
    <ul>
        <?php foreach ($users as $user): ?>
            <li>
                <?php echo $user; ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="username" value="<?php echo basename($user, '/32'); ?>">
                    <button type="submit" name="delete_user">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>