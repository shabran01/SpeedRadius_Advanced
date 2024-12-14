{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Router Status Monitor</h3>
            </div>
            <div class="panel-body">
                {if !$_c['whatsapp_gateway_url']}
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> WhatsApp Gateway is not configured. 
                        Please <a href="{$_url}plugin/whatsappGateway_config">configure it here</a> to enable notifications.
                    </div>
                {/if}
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Router Name</th>
                                <th>IP Address</th>
                                <th>Status</th>
                                <th>Last Online</th>
                                <th>Last Offline</th>
                                <th>WhatsApp Number</th>
                                <th>Last Notification</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="routerStatusTable">
                            {foreach $routers as $router}
                            <tr id="router_{$router.id}">
                                <td>{$router.name}</td>
                                <td>{$router.ip_address}</td>
                                <td>
                                    <span class="label label-{if $router.status == 'online'}success{else}danger{/if}" id="status_{$router.id}">
                                        {$router.status|upper}
                                    </span>
                                </td>
                                <td id="lastOnline_{$router.id}">{$router.last_online}</td>
                                <td id="lastOffline_{$router.id}">{$router.last_offline}</td>
                                <td>
                                    <form method="post" class="form-inline">
                                        <input type="hidden" name="router_id" value="{$router.id}">
                                        <div class="input-group">
                                            <input type="text" class="form-control input-sm" name="notification_number" 
                                                value="{$router.notification_number}" placeholder="+1234567890">
                                            <span class="input-group-btn">
                                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                                <button type="button" class="btn btn-info btn-sm" onclick="testNotification({$router.id})">Test</button>
                                            </span>
                                        </div>
                                    </form>
                                </td>
                                <td id="lastNotification_{$router.id}">{$router.last_notification}</td>
                                <td>
                                    <button class="btn btn-xs btn-info" onclick="checkStatus({$router.id})">
                                        <i class="fa fa-refresh"></i> Check Now
                                    </button>
                                </td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var normalInterval = 20000; // 20 seconds
var rebootInterval = 5000;  // 5 seconds
var currentInterval = normalInterval;
var updateTimer;
var offlineRouters = {};

// Update status with dynamic interval
function updateStatus() {
    $.get('?_route=plugin/router_status_monitor&action=check_status', function(data) {
        var hasOfflineRouters = false;
        
        data.forEach(function(router) {
            var statusLabel = router.status === 'online' ? 'success' : 'danger';
            var wasOffline = offlineRouters[router.router_id];
            
            // Update status display
            $('#status_' + router.router_id)
                .attr('class', 'label label-' + statusLabel)
                .text(router.status.toUpperCase());
                
            // Update timestamps
            if(router.last_online) $('#lastOnline_' + router.router_id).text(router.last_online);
            if(router.last_offline) $('#lastOffline_' + router.router_id).text(router.last_offline);
            if(router.last_notification) $('#lastNotification_' + router.router_id).text(router.last_notification);
            
            // Track offline routers
            if(router.status === 'offline') {
                hasOfflineRouters = true;
                offlineRouters[router.router_id] = true;
            } else {
                delete offlineRouters[router.router_id];
            }
        });
        
        // Adjust check interval based on router status
        var newInterval = hasOfflineRouters ? rebootInterval : normalInterval;
        if (newInterval !== currentInterval) {
            currentInterval = newInterval;
            clearInterval(updateTimer);
            updateTimer = setInterval(updateStatus, currentInterval);
        }
    });
}

function checkStatus(routerId) {
    var btn = $(event.target);
    var originalHtml = btn.html();
    btn.html('<i class="fa fa-spin fa-spinner"></i> Checking...');
    
    $.get('?_route=plugin/router_status_monitor&action=check_status', function(data) {
        data.forEach(function(router) {
            if(router.router_id == routerId) {
                var statusLabel = router.status === 'online' ? 'success' : 'danger';
                $('#status_' + router.router_id)
                    .attr('class', 'label label-' + statusLabel)
                    .text(router.status.toUpperCase());
                if(router.last_online) $('#lastOnline_' + router.router_id).text(router.last_online);
                if(router.last_offline) $('#lastOffline_' + router.router_id).text(router.last_offline);
                if(router.last_notification) $('#lastNotification_' + router.router_id).text(router.last_notification);
            }
        });
        btn.html(originalHtml);
    });
}

function testNotification(routerId) {
    $.get('?_route=plugin/router_status_monitor&action=test_notification&router_id=' + routerId, function(response) {
        if(response.success) {
            alert('Test notification sent successfully!');
        } else {
            alert('Failed to send test notification. Please check your WhatsApp configuration.');
        }
    });
}

// Start status updates
updateTimer = setInterval(updateStatus, currentInterval);
updateStatus(); // Initial check
</script>

{include file="sections/footer.tpl"}
