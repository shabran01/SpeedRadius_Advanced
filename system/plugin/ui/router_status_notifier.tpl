{include file="sections/header.tpl"}

<style>
{literal}
/* ================= Router Status Notifier UI ================= */
.rn-wrap{padding:0 4px}
.rn-head{background:linear-gradient(135deg,#2563eb 0%,#1d4ed8 55%,#0f172a 130%);border-radius:16px;padding:24px 28px;color:#fff;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:22px;box-shadow:0 10px 30px rgba(37,99,235,.22)}
.rn-head h3{margin:0;font-weight:700;font-size:20px;letter-spacing:.2px}
.rn-head p{margin:3px 0 0;opacity:.85;font-size:13px}
.rn-status-pill{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.25);padding:8px 16px;border-radius:999px;font-size:13px;font-weight:600}
.rn-dot{width:9px;height:9px;border-radius:50%;background:#4ade80;box-shadow:0 0 0 3px rgba(74,222,128,.25)}
.rn-stat{background:#fff;border:1px solid #eef0f4;border-radius:14px;padding:18px 20px;box-shadow:0 2px 10px rgba(15,23,42,.04);transition:transform .15s ease,box-shadow .15s ease;height:100%}
.rn-stat:hover{transform:translateY(-3px);box-shadow:0 10px 24px rgba(15,23,42,.08)}
.rn-stat .ic{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:12px}
.rn-stat .val{font-size:26px;font-weight:800;line-height:1;color:#0f172a}
.rn-stat .lbl{font-size:12.5px;color:#64748b;margin-top:6px;font-weight:500;letter-spacing:.3px}
.rn-card{background:#fff;border:1px solid #eef0f4;border-radius:14px;box-shadow:0 2px 10px rgba(15,23,42,.04);overflow:hidden;margin-bottom:20px}
.rn-card .card-head{display:flex;align-items:center;justify-content:space-between;padding:15px 20px;border-bottom:1px solid #f1f5f9;background:#fafbfc}
.rn-card .card-head h4{margin:0;font-size:14px;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:9px}
.rn-card .card-body{padding:20px}
.rn-card .card-head .hint{font-size:12px;color:#94a3b8}
.rn-tabs{border:1px solid #eef0f4;border-radius:14px;background:#fff;box-shadow:0 2px 10px rgba(15,23,42,.04);overflow:hidden;margin-bottom:20px}
.rn-tabs .nav-tabs{border-bottom:1px solid #f1f5f9;background:#fafbfc;padding:0 8px}
.rn-tabs .nav-tabs>li{margin-bottom:0}
.rn-tabs .nav-tabs>li>a{border:none;border-radius:10px 10px 0 0;color:#64748b;font-weight:600;font-size:13px;padding:13px 20px;margin:6px 2px 0;transition:color .15s ease,background .15s ease}
.rn-tabs .nav-tabs>li>a:hover{color:#2563eb;background:#fff}
.rn-tabs .nav-tabs>li.active>a,.rn-tabs .nav-tabs>li.active>a:hover,.rn-tabs .nav-tabs>li.active>a:focus{border:none;background:#fff;color:#2563eb;box-shadow:inset 0 2px 0 0 #2563eb}
.rn-tabs .tab-content{padding:22px}
.rn-label{display:inline-block;padding:4px 11px;border-radius:999px;font-size:11.5px;font-weight:700;letter-spacing:.4px}
.rn-label-offline{background:#fef2f2;color:#dc2626;border:1px solid #fecaca}
.rn-label-online{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0}
.rn-label-test{background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0}
.rn-btn{border:none;border-radius:10px;font-weight:600;font-size:13px;padding:10px 18px;transition:all .15s ease}
.rn-btn-primary{background:#2563eb;color:#fff}
.rn-btn-primary:hover{background:#1d4ed8;color:#fff}
.rn-btn-danger{background:#dc2626;color:#fff}
.rn-btn-danger:hover{background:#b91c1c;color:#fff}
.rn-btn-success{background:#16a34a;color:#fff}
.rn-btn-success:hover{background:#15803d;color:#fff}
.rn-btn-block{width:100%;display:block}
.rn-btn+.rn-btn{margin-top:8px}
.rn-input{border:1px solid #e2e8f0;border-radius:10px;padding:10px 14px;font-size:13.5px;box-shadow:none;transition:border-color .15s ease,box-shadow .15s ease}
.rn-input:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.12)}
.rn-help{font-size:12px;color:#94a3b8;margin-top:6px}
.rn-table{width:100%;border-collapse:separate;border-spacing:0;font-size:13px}
.rn-table thead th{background:#f8fafc;color:#475569;font-size:11.5px;text-transform:uppercase;letter-spacing:.6px;padding:12px 14px;border-bottom:1px solid #e2e8f0;font-weight:700}
.rn-table tbody td{padding:12px 14px;border-bottom:1px solid #f1f5f9;color:#334155;vertical-align:middle}
.rn-table tbody tr:hover td{background:#f8fafc}
.rn-table tbody tr:last-child td{border-bottom:none}
.rn-kbd{background:#f1f5f9;border:1px solid #e2e8f0;border-radius:6px;padding:1px 7px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;color:#475569}
.rn-alert{border:none;border-radius:12px;padding:13px 16px;font-size:13.5px}
@media(max-width:768px){.rn-head{padding:18px}.rn-tabs .tab-content{padding:16px}}
{/literal}
</style>

<div class="rn-wrap">

    {if isset($notify)}
        <div class="alert alert-{$notify_type} rn-alert">
            <i class="fa fa-{if $notify_type == 'success'}check-circle{elseif $notify_type == 'warning'}exclamation-triangle{else}times-circle{/if}"></i>
            {$notify}
        </div>
    {/if}

    <!-- Header Banner -->
    <div class="rn-head">
        <div>
            <h3><i class="fa fa-bell" style="margin-right:8px;"></i> Router Status Notifier</h3>
            <p>Monitor MikroTik routers &amp; get instant SMS / WhatsApp / Telegram alerts on downtime.</p>
        </div>
        <span class="rn-status-pill"><span class="rn-dot"></span> Monitoring Active</span>
    </div>

    <!-- Stat Cards -->
    {assign var="offlineCount" value=0}
    {assign var="onlineCount" value=0}
    {foreach $routers as $r}
        {if $r.status|lower == 'offline'}
            {assign var="offlineCount" value=$offlineCount+1}
        {elseif $r.status|lower == 'up' || $r.status|lower == 'online'}
            {assign var="onlineCount" value=$onlineCount+1}
        {/if}
    {/foreach}
    {assign var="totalRouters" value=$routers|count}

    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="rn-stat">
                <div class="ic" style="background:#eff6ff;color:#2563eb;"><i class="fa fa-server"></i></div>
                <div class="val">{$totalRouters}</div>
                <div class="lbl">TOTAL ROUTERS</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="rn-stat">
                <div class="ic" style="background:#fef2f2;color:#dc2626;"><i class="fa fa-exclamation-circle"></i></div>
                <div class="val" style="color:#dc2626;">{$offlineCount}</div>
                <div class="lbl">OFFLINE NOW</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="rn-stat">
                <div class="ic" style="background:#f0fdf4;color:#16a34a;"><i class="fa fa-check-circle"></i></div>
                <div class="val" style="color:#16a34a;">{$onlineCount}</div>
                <div class="lbl">ONLINE NOW</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="rn-stat">
                <div class="ic" style="background:#faf5ff;color:#9333ea;"><i class="fa fa-envelope-open"></i></div>
                <div class="val">{$logs|count}</div>
                <div class="lbl">RECENT NOTIFICATIONS</div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="rn-tabs">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#dashboard" data-toggle="tab"><i class="fa fa-tachometer"></i> Dashboard</a></li>
            <li><a href="#logs" data-toggle="tab"><i class="fa fa-list-alt"></i> Notification Logs</a></li>
            <li><a href="#settings" data-toggle="tab"><i class="fa fa-cog"></i> Settings</a></li>
        </ul>
        <div class="tab-content">

            <!-- ============ DASHBOARD TAB ============ -->
            <div class="tab-pane active" id="dashboard">
                <div class="row">
                    <div class="col-md-6">
                        <div class="rn-card">
                            <div class="card-head">
                                <h4><span style="width:32px;height:32px;border-radius:9px;background:#eff6ff;color:#2563eb;display:inline-flex;align-items:center;justify-content:center;"><i class="fa fa-paper-plane"></i></span> Send Test Notification</h4>
                                <span class="hint">Verify SMS / WhatsApp gateway</span>
                            </div>
                            <div class="card-body">
                                <form method="post" action="">
                                    <input type="hidden" name="csrf_token" value="{$csrf_token}">
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" class="form-control rn-input" name="phone" placeholder="e.g 2547..." required>
                                    </div>
                                    <div class="form-group">
                                        <label>Message</label>
                                        <textarea class="form-control rn-input" name="message" rows="3" required>Test notification from SpeedRadius</textarea>
                                    </div>
                                    <button type="submit" name="send_test" class="rn-btn rn-btn-primary"><i class="fa fa-send"></i> Send Test Notification</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="rn-card">
                            <div class="card-head">
                                <h4><span style="width:32px;height:32px;border-radius:9px;background:#faf5ff;color:#9333ea;display:inline-flex;align-items:center;justify-content:center;"><i class="fa fa-flask"></i></span> Template Simulator</h4>
                                <span class="hint">Preview before it goes live</span>
                            </div>
                            <div class="card-body">
                                <p style="font-size:13px;color:#64748b;margin-top:0;">Test your configured Offline / Online templates with real or dummy router data.</p>
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="{$csrf_token}">
                                    <div class="form-group">
                                        <label>Select Router <small class="text-muted">(optional)</small></label>
                                        <select name="sim_router_id" class="form-control rn-input select2" style="width:100%;">
                                            <option value="">-- Use Dummy Data --</option>
                                            {foreach $routers as $r}
                                                <option value="{$r.id}">{$r.name} ({$r.ip_address})</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <button type="submit" name="test_offline_tpl" class="rn-btn rn-btn-danger rn-btn-block"><i class="fa fa-power-off"></i> Simulate Offline Event</button>
                                    <button type="submit" name="test_online_tpl" class="rn-btn rn-btn-success rn-btn-block"><i class="fa fa-wifi"></i> Simulate Online Event</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============ LOGS TAB ============ -->
            <div class="tab-pane" id="logs">
                <div class="rn-card">
                    <div class="card-head">
                        <h4><span style="width:32px;height:32px;border-radius:9px;background:#eff6ff;color:#2563eb;display:inline-flex;align-items:center;justify-content:center;"><i class="fa fa-list-alt"></i></span> Recent Notifications</h4>
                        <span class="hint">Latest 50 events</span>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <div style="overflow-x:auto;">
                            <table class="rn-table" id="logs_table">
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
                                            <td style="white-space:nowrap;">{$log.created_at}</td>
                                            <td><strong>{$log.router_name}</strong></td>
                                            <td>
                                                {if $log.type == 'offline'}
                                                    <span class="rn-label rn-label-offline">● Offline</span>
                                                {elseif $log.type == 'test'}
                                                    <span class="rn-label rn-label-test">● Test</span>
                                                {else}
                                                    <span class="rn-label rn-label-online">● Online</span>
                                                {/if}
                                            </td>
                                            <td style="max-width:340px;word-break:break-word;">{$log.message}</td>
                                            <td style="white-space:nowrap;">{$log.recipients}</td>
                                        </tr>
                                    {foreachelse}
                                        <tr>
                                            <td colspan="5" style="text-align:center;color:#94a3b8;padding:30px;">
                                                <i class="fa fa-inbox" style="font-size:26px;display:block;margin-bottom:8px;"></i>
                                                No notifications yet.
                                            </td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============ SETTINGS TAB ============ -->
            <div class="tab-pane" id="settings">
                <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="{$csrf_token}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="rn-card">
                                <div class="card-head">
                                    <h4><span style="width:32px;height:32px;border-radius:9px;background:#fef2f2;color:#dc2626;display:inline-flex;align-items:center;justify-content:center;"><i class="fa fa-users"></i></span> Recipient Settings</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Recipients <small class="text-muted">(comma separated)</small></label>
                                        <input type="text" class="form-control rn-input" name="router_notif_recipients" value="{$pconf.router_notif_recipients}" placeholder="e.g 2547123456,2547000000">
                                        <p class="rn-help">Leave empty to use System Admin Phone.</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Flapping Protection <small class="text-muted">(seconds)</small></label>
                                        <input type="number" class="form-control rn-input" name="router_notif_flap_seconds" value="{$pconf.router_notif_flap_seconds}" placeholder="300">
                                        <p class="rn-help">Minimum time between alerts for the same router (prevents spam).</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Offline Grace Period <small class="text-muted">(minutes)</small></label>
                                        <input type="number" class="form-control rn-input" name="router_notif_offline_delay" value="{$pconf.router_notif_offline_delay}" min="1">
                                        <p class="rn-help">Wait this many minutes of confirmed downtime before sending the SMS alert.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="rn-card">
                                <div class="card-head">
                                    <h4><span style="width:32px;height:32px;border-radius:9px;background:#f0fdf4;color:#16a34a;display:inline-flex;align-items:center;justify-content:center;"><i class="fa fa-envelope"></i></span> Message Templates</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Offline Message</label>
                                        <textarea class="form-control rn-input" name="router_notif_tpl_offline" rows="3">{$pconf.router_notif_tpl_offline}</textarea>
                                        <p class="rn-help">Variables: <span class="rn-kbd">[[name]]</span> <span class="rn-kbd">[[ip]]</span> <span class="rn-kbd">[[time]]</span> <span class="rn-kbd">[[downtime]]</span></p>
                                    </div>
                                    <div class="form-group">
                                        <label>Online Message</label>
                                        <textarea class="form-control rn-input" name="router_notif_tpl_online" rows="3">{$pconf.router_notif_tpl_online}</textarea>
                                        <p class="rn-help">Variables: <span class="rn-kbd">[[name]]</span> <span class="rn-kbd">[[ip]]</span> <span class="rn-kbd">[[time]]</span> <span class="rn-kbd">[[downtime]]</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rn-card">
                        <div class="card-head">
                            <h4><span style="width:32px;height:32px;border-radius:9px;background:#f1f5f9;color:#475569;display:inline-flex;align-items:center;justify-content:center;"><i class="fa fa-ban"></i></span> Ignored Routers</h4>
                            <span class="hint">These routers will never trigger alerts</span>
                        </div>
                        <div class="card-body">
                            <div class="form-group" style="margin-bottom:0;">
                                <select class="form-control rn-input select2" name="router_notif_ignore[]" multiple style="width:100%;">
                                    {foreach $routers as $r}
                                        <option value="{$r.id}" {if in_array($r.id, $ignored_routers)}selected{/if}>{$r.name} ({$r.ip_address})</option>
                                    {/foreach}
                                </select>
                                <p class="rn-help">Select the routers you want to exclude from monitoring alerts.</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="save_settings" class="rn-btn rn-btn-primary" style="padding:12px 26px;"><i class="fa fa-save"></i> Save Settings</button>
                </form>
            </div>

        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
{literal}
$(document).ready(function() {
    $('.select2').select2();
    if ($.fn.DataTable) {
        $('#logs_table').DataTable({
            "order": [[ 0, "desc" ]]
        });
    }
});
{/literal}
</script>
