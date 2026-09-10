{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">📱 SMS Gate Gateway — sms-gate.app</h3>
            </div>
            <ul class="nav nav-tabs" style="margin:0 15px;">
                <li {if $menu neq 'config'}class="active"{/if}>
                    <a href="{$_url}plugin/smsGatewaySmSGate">Dashboard</a>
                </li>
                <li {if $menu eq 'config'}class="active"{/if}>
                    <a href="{$_url}plugin/smsGatewaySmSGate_config">Configuration</a>
                </li>
            </ul>

            {if $menu eq 'config'}
            <div class="panel-body">
                <form class="form-horizontal" method="post" role="form" action="{$_url}plugin/smsGatewaySmSGate_config">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Username</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="smsgate_username" value="{$_c['smsgate_username']}">
                            <p class="help-block">From the app's Home screen (e.g. HSHN-U)</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Password</label>
                        <div class="col-md-6">
                            <input type="password" class="form-control" name="smsgate_password" value="{$_c['smsgate_password']}"
                                onmouseenter="this.type='text'" onmouseleave="this.type='password'">
                            <p class="help-block">From the app's Home screen</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Device ID <small class="text-muted">(optional)</small></label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="smsgate_device_id" value="{$_c['smsgate_device_id']}">
                            <p class="help-block">From the app's Home screen. Only needed if you have multiple devices. Leave blank to use default.</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Connection Mode</label>
                        <div class="col-md-6">
                            <select name="smsgate_mode" class="form-control">
                                <option value="cloud" {if !$_c['smsgate_mode'] || $_c['smsgate_mode'] == 'cloud'}selected{/if}>
                                    🌐 External (Cloud/Private Server)
                                </option>
                                <option value="local" {if $_c['smsgate_mode'] == 'local'}selected{/if}>
                                    🏠 Internal (Local Phone)
                                </option>
                            </select>
                            <p class="help-block">
                                <strong>External:</strong> Via cloud — works from anywhere.<br>
                                <strong>Internal:</strong> Direct to phone on LAN — faster, no internet needed.
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Cloud Provider</label>
                        <div class="col-md-6">
                            <select name="smsgate_cloud_provider" class="form-control" onchange="toggleApiUrl(this.value)">
                                <option value="official" {if !$_c['smsgate_cloud_provider'] || $_c['smsgate_cloud_provider'] == 'official'}selected{/if}>
                                    🟢 Official Cloud — api.sms-gate.app
                                </option>
                                <option value="private" {if $_c['smsgate_cloud_provider'] == 'private'}selected{/if}>
                                    🔵 Private Server — textsms.speedcomwifi.xyz
                                </option>
                                <option value="custom" {if $_c['smsgate_cloud_provider'] == 'custom'}selected{/if}>
                                    ⚪ Custom URL — Enter manually
                                </option>
                            </select>
                            <p class="help-block">Choose which cloud server to use when in <strong>External</strong> mode.</p>
                        </div>
                    </div>
                    <div class="form-group" id="apiUrlGroup" style="display:{if $_c['smsgate_cloud_provider'] == 'custom'}block{else}none{/if};">
                        <label class="col-md-3 control-label">Custom API URL</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="smsgate_api_url" 
                                   value="{$_c['smsgate_api_url']}" placeholder="https://your-server.com/api/3rdparty/v1/messages">
                            <p class="help-block">Full API endpoint URL for your custom server.</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Local Phone URL</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="smsgate_local_url" 
                                   value="{$_c['smsgate_local_url']}" placeholder="http://192.168.1.100:8080">
                            <p class="help-block">Only needed if mode is <strong>Internal</strong>. From the app's Home screen. Example: <code>http://192.168.1.100:8080</code> → sends to <code>/message</code></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-3 col-md-6">
                            <button class="btn btn-primary" type="submit">Save Configuration</button>
                        </div>
                    </div>
                </form>

                <hr>

                {* Android App Installation Guide *}
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h4 class="panel-title">📱 Android App Installation Steps</h4>
                    </div>
                    <div class="panel-body">
                        <ol style="font-size:14px; line-height:2;">
                            <li><strong>Download the APK</strong> — 
                                <a href="https://github.com/capcom6/android-sms-gateway/releases/latest/download/app-release.apk" 
                                   class="btn btn-success btn-xs" target="_blank">
                                    <i class="glyphicon glyphicon-download"></i> Download app-release.apk
                                </a>
                                <br><small class="text-muted">From GitHub Releases: <code>github.com/capcom6/android-sms-gateway</code></small>
                            </li>
                            <li><strong>Install on Android phone</strong> — Allow "Unknown Sources" if prompted.
                                <br><small class="text-muted">Requires Android 5.0+</small>
                            </li>
                            <li><strong>Open the app</strong> — Choose your mode:
                                <ul>
                                    <li>🏠 <strong>Local Server:</strong> Toggle ON → Tap "Offline" to go Online → credentials appear</li>
                                    <li>🌐 <strong>Cloud Server:</strong> Toggle ON → Tap "Offline" to go Online → credentials appear automatically</li>
                                </ul>
                            </li>
                            <li><strong>Copy credentials</strong> — Username & Password are shown on the Home screen</li>
                            <li><strong>Paste above</strong> — Enter the Username, Password, and Device ID in the form above</li>
                            <li><strong>Save Configuration</strong> — Click the Save button</li>
                            <li><strong>Test it!</strong> — Go to the Dashboard tab and send a test SMS</li>
                        </ol>
                        <div class="alert alert-info" style="margin-top:15px;">
                            <i class="glyphicon glyphicon-info-sign"></i>
                            <strong>No registration required!</strong> Credentials are auto-generated in the app. No email or account creation needed.
                        </div>
                    </div>
                </div>
            </div>

            {else}
            <div class="panel-body">

                {* Status box *}
                <div class="row" style="margin-bottom:20px;">
                    <div class="col-sm-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="glyphicon glyphicon-phone"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Gateway</span>
                                <span class="info-box-number">sms-gate.app</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="glyphicon glyphicon-signal"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Mode</span>
                                <span class="info-box-number">
                                    {if $_c['smsgate_mode'] == 'local'}🏠 Internal (Local){else}🌐 External (Cloud){/if}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-orange"><i class="glyphicon glyphicon-user"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Username</span>
                                <span class="info-box-number">{if $_c['smsgate_username']}{$_c['smsgate_username']}{else}<span class="text-danger">Not set</span>{/if}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {* Test SMS *}
                <div class="panel panel-default">
                    <div class="panel-heading"><h4 class="panel-title">Send Test SMS</h4></div>
                    <div class="panel-body">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Phone</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="test_phone" placeholder="+254712345678">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Message</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="test_message" value="Test from SpeedRadius SMS Gate">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-offset-2 col-md-6">
                                    <button class="btn btn-success" onclick="sendTestSMS()">
                                        <i class="glyphicon glyphicon-send"></i> Send Test
                                    </button>
                                    <span id="test_result" style="margin-left:12px;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {* SMS Logs *}
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title" style="display:inline;">Last 10 SMS Gate Messages</h4>
                        <button class="btn btn-danger btn-xs pull-right" onclick="clearSMSLogs()" style="margin-top:-3px;">
                            <i class="glyphicon glyphicon-trash"></i> Clear All Logs
                        </button>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date / Time</th>
                                        <th>Phone</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Message ID</th>
                                        <th>Error Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $sms_logs as $log}
                                    <tr>
                                        <td>{$log['created_at']}</td>
                                        <td>{$log['phone']}</td>
                                        <td>{$log['message']|truncate:50}</td>
                                        <td>
                                            {if $log['status'] eq 'sent'}
                                                <span class="label label-success">Sent</span>
                                            {else}
                                                <span class="label label-danger">Failed</span>
                                            {/if}
                                        </td>
                                        <td style="font-size:11px;">{$log['message_id']|default:'-'}</td>
                                        <td style="font-size:11px; max-width:150px;">
                                            {if $log['status'] neq 'sent' && $log['status_message']}
                                                <span class="text-danger">{$log['status_message']|truncate:80}</span>
                                            {else}
                                                <span class="text-muted">-</span>
                                            {/if}
                                        </td>
                                    </tr>
                                    {foreachelse}
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No SMS messages yet</td>
                                    </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            {/if}
        </div>
    </div>
</div>

<script>var _smsgate_test_url = '{$_url}plugin/smsGatewaySmSGate_test';
var _smsgate_clear_url = '{$_url}plugin/smsGatewaySmSGate_clear';</script>
{literal}
<script>
function toggleApiUrl(val) {
    var g = document.getElementById('apiUrlGroup');
    if (g) g.style.display = (val === 'custom') ? 'block' : 'none';
}
function sendTestSMS() {
    var phone = document.getElementById('test_phone').value.trim();
    var message = document.getElementById('test_message').value.trim();
    var resultEl = document.getElementById('test_result');

    if (!phone || !message) {
        resultEl.innerHTML = '<span class="text-danger">Phone and message are required.</span>';
        return;
    }

    resultEl.innerHTML = '<span class="text-muted">Sending...</span>';

    $.ajax({
        url: _smsgate_test_url,
        method: 'POST',
        data: { phone: phone, message: message },
        success: function(resp) {
            if (resp.success) {
                resultEl.innerHTML = '<span class="text-success"><b>✓ Sent successfully!</b></span>';
            } else {
                resultEl.innerHTML = '<span class="text-danger"><b>✗ Failed:</b> ' + resp.message + '</span>';
            }
        },
        error: function() {
            resultEl.innerHTML = '<span class="text-danger">Request error. Check server logs.</span>';
        }
    });
}
function clearSMSLogs() {
    if (!confirm('Delete ALL SMS Gate logs? This cannot be undone.')) return;

    $.ajax({
        url: _smsgate_clear_url,
        method: 'POST',
        success: function(resp) {
            if (resp.success) {
                alert(resp.message);
                location.reload();
            } else {
                alert('Failed: ' + resp.message);
            }
        },
        error: function() {
            alert('Request error. Check server logs.');
        }
    });
}
</script>
{/literal}

{include file="sections/footer.tpl"}
