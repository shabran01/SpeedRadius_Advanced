{include file="sections/header.tpl"}

{* Early, so the logs table is styled on first paint instead of repainting when the
   stylesheet finally arrives at the bottom of the document. *}
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

{* The alert sits outside the tab panes on purpose: saving Settings re-renders the
   page on the #dashboard tab, so a message rendered inside that pane was never
   actually seen. *}
{if isset($notify)}
    <div class="alert alert-{$notify_type}">{$notify}</div>
{/if}

<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#dashboard" data-toggle="tab">Dashboard</a></li>
                <li><a href="#logs" data-toggle="tab">Notification Logs</a></li>
                <li><a href="#settings" data-toggle="tab">Settings</a></li>
            </ul>
            <div class="tab-content">
                
                <!-- DASHBOARD TAB -->
                <div class="tab-pane active" id="dashboard">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                            <div class="panel panel-default">
                                <div class="panel-heading">Test Notification</div>
                                <div class="panel-body">
                                    <form method="post" action="">
                                        <input type="hidden" name="csrf_token" value="{$csrf_token}">
                                        <div class="form-group">
                                            <label>Phone Number</label>
                                            <input type="text" class="form-control" name="phone" placeholder="e.g 2547..." required>
                                        </div>
                                         <div class="form-group">
                                            <label>Message</label>
                                            <textarea class="form-control" name="message" required>Test notification from SpeedRadius</textarea>
                                        </div>
                                        <button type="submit" name="send_test" class="btn btn-primary">Send Test Notification</button>
                                    </form>
                                </div>
                            </div>

                            <div class="panel panel-info">
                                <div class="panel-heading">Template Simulator</div>
                                <div class="panel-body">
                                    <p>Test your configured <b>Offline</b> and <b>Online</b> templates with dummy data.</p>
                                    <form method="post">
                                        <input type="hidden" name="csrf_token" value="{$csrf_token}">
                                        <div class="form-group">
                                            <label>Select Router (Optional)</label>
                                            <select name="sim_router_id" class="form-control select2" style="width: 100%;">
                                                <option value="">-- Use Dummy Data --</option>
                                                {foreach $routers as $r}
                                                    <option value="{$r.id}">{$r.name} ({$r.ip_address})</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                        <button type="submit" name="test_offline_tpl" class="btn btn-danger btn-block" style="margin-bottom: 5px;">Simulate Offline Event</button>
                                        <button type="submit" name="test_online_tpl" class="btn btn-success btn-block">Simulate Online Event</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LOGS TAB -->
                <div class="tab-pane" id="logs">
                    <div class="panel panel-default">
                        <div class="panel-heading">Recent Notifications</div>
                        <div class="panel-body">
                            {* Without this wrapper a five-column table forces the whole page
                               into horizontal scroll on a phone. *}
                            <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="logs_table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Router</th>
                                        <th>Type</th>
                                        <th>Message</th>
                                        <th>Recipients</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $logs as $log}
                                        <tr>
                                            <td>{$log.created_at}</td>
                                            <td>{$log.router_name}</td>
                                            <td>
                                                {if $log.type == 'offline'}
                                                    <span class="label label-danger">Offline</span>
                                                {elseif $log.type == 'test'}
                                                    <span class="label label-default">Test</span>
                                                {else}
                                                    <span class="label label-success">Online</span>
                                                {/if}
                                            </td>
                                            <td>{$log.message}</td>
                                            <td>{$log.recipients}</td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SETTINGS TAB -->
                <div class="tab-pane" id="settings">
                    {* The original markup opened this pane with two malformed tags, so no
                       form element was ever created: "Save Settings" had nothing to submit
                       and the CSRF token was never sent. *}
                    <form method="post" action="">
                        <input type="hidden" name="csrf_token" value="{$csrf_token}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Recipient Settings</div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label>Recipients (Comma Separated)</label>
                                            <input type="text" class="form-control" name="router_notif_recipients" value="{$pconf.router_notif_recipients}" placeholder="e.g 2547123456,2547000000">
                                            <p class="help-block">Leave empty to use System Admin Phone.</p>
                                        </div>
                                        <div class="form-group">
                                            <label>Flapping Protection (Seconds)</label>
                                            <input type="number" class="form-control" name="router_notif_flap_seconds" value="{$pconf.router_notif_flap_seconds}">
                                            <p class="help-block">Minimum time between alerts for the same router (prevents spam).</p>
                                        </div>
                                        <div class="form-group">
                                            <label>Offline Grace Period (Minutes)</label>
                                            <input type="number" class="form-control" name="router_notif_offline_delay" value="{$pconf.router_notif_offline_delay}" min="1">
                                            <p class="help-block">Backstop only. The alert is sent the moment the monitor reports a router offline; this delay is how long the cron waits before re-checking for any router it may have missed. Default: 3 minutes.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Message Templates</div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label>Offline Message</label>
                                            <textarea class="form-control" name="router_notif_tpl_offline" rows="3">{$pconf.router_notif_tpl_offline}</textarea>
                                            <p class="help-block">Variables: [[name]], [[ip]], [[time]], [[downtime]]</p>
                                        </div>
                                        <div class="form-group">
                                            <label>Online Message</label>
                                            <textarea class="form-control" name="router_notif_tpl_online" rows="3">{$pconf.router_notif_tpl_online}</textarea>
                                            <p class="help-block">Variables: [[name]], [[ip]], [[time]], [[downtime]]</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Ignored Routers</div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label>Select Routers to Ignore</label>
                                            <select class="form-control select2" name="router_notif_ignore[]" multiple>
                                                {foreach $routers as $r}
                                                    <option value="{$r.id}" {if in_array($r.id, $ignored_routers)}selected{/if}>{$r.name} ({$r.ip_address})</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="save_settings" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

{* Everything below runs before the footer include so the document is not emitted
   past </body></html>. `defer` keeps DataTables from evaluating before jQuery, which
   footer.tpl loads; the inline block then waits for DOMContentLoaded, which fires
   only after all deferred scripts have run. *}
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js" defer></script>
<script>
{literal}
document.addEventListener('DOMContentLoaded', function () {
    if (typeof jQuery === 'undefined') return;
    var $ = jQuery;
    if ($.fn.select2) { $('.select2').select2(); }
    if ($.fn.DataTable && document.getElementById('logs_table')) {
        $('#logs_table').DataTable({ order: [[0, 'desc']], pageLength: 25 });
    }
});
{/literal}
</script>

{include file="sections/footer.tpl"}
