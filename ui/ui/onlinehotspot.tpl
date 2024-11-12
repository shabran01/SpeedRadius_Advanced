<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Hotspot Users</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
    <style>
        table.dataTable thead th,
        table.dataTable thead td {
            padding: 10px 18px;
            border-bottom: 1px solid #e2e8f0;
        }
        table.dataTable tbody td {
            padding: 10px 18px;
            border-bottom: 1px solid #e2e8f0;
        }
        .disconnect-btn {
            padding: 5px 10px;
            background-color: #d53f8c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4">Online Hotspot Users</h1>
        <table id="hotspotUsersTable" class="stripe hover" style="width:100%">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Address</th>
                    <th>Uptime</th>
                    <th>Server</th>
                    <th>MAC</th>
                    <th>Session Time</th>
                    <th>Rx Bytes</th>
                    <th>Tx Bytes</th>
                    <th>Total Bytes</th>
                    <th>Action</th> <!-- Added column for action -->
                </tr>
            </thead>
            <tbody>
                {foreach $onlineHotspotUsers as $user}
                <tr>
                    <td>{$user.username}</td>
                    <td>{$user.address}</td>
                    <td>{$user.uptime}</td>
                    <td>{$user.server}</td>
                    <td>{$user.mac}</td>
                    <td>{$user.session_time}</td>
                    <td>{$user.rx_bytes}</td>
                    <td>{$user.tx_bytes}</td>
                    <td>{$user.total}</td>
                    <td><button class="disconnect-btn" onclick="disconnectUser('{$user.username}')">Disconnect</button></td> <!-- Disconnect button -->
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function() {
            $('#hotspotUsersTable').DataTable();
        });

        function disconnectUser(username) {
            // You can perform disconnect action here, such as making an AJAX call to disconnect the user
            alert('Disconnecting user: ' + username);
        }
    </script>
</body>
</html>
