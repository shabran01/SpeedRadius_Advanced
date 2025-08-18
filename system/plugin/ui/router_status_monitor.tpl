{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Router Status Monitor</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Router Name</th>
                                <th>IP Address</th>
                                <th>Status</th>
                                <th>Last Seen</th>
                                <th>Uptime</th>
                                <th>Notification Number</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $routers as $router}
                                <tr data-router-id="{$router.id}">
                                    <td>{$router.name}</td>
                                    <td>{$router.ip_address}</td>
                                    <td class="router-status">
                                        <span class="label label-{if $router.status == 'online'}success{else}danger{/if}">
                                            {$router.status|ucfirst}
                                        </span>
                                    </td>
                                    <td class="last-seen">{$router.last_check}</td>
                                    <td class="uptime">{$router.last_uptime}</td>
                                    <td>
                                        <form class="notification-form" method="post">
                                            <input type="hidden" name="router_id" value="{$router.id}">
                                            <div class="input-group">
                                                <input type="text" class="form-control input-sm" name="notification_number" 
                                                    value="{$router.notification_number}" placeholder="WhatsApp number">
                                                <span class="input-group-btn">
                                                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                                </span>
                                            </div>
                                        </form>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm test-notification" 
                                            data-router-id="{$router.id}" 
                                            {if !$router.notification_number}disabled{/if}>
                                            Test Notification
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
    var baseURL = '{$_url}';
</script>
<script src="{$_theme}/js/router_monitor.js"></script>
{include file="sections/footer.tpl"}
