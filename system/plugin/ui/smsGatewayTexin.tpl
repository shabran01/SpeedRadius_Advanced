{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">📨 Texin SMS Gateway — texin.co.ke</h3>
            </div>
            <ul class="nav nav-tabs" style="margin:0 15px;">
                <li {if $menu neq 'config'}class="active"{/if}>
                    <a href="{$_url}plugin/smsGatewayTexin">Dashboard</a>
                </li>
                <li {if $menu eq 'config'}class="active"{/if}>
                    <a href="{$_url}plugin/smsGatewayTexin_config">Configuration</a>
                </li>
            </ul>

            {if $menu eq 'config'}
            <div class="panel-body">
                <form class="form-horizontal" method="post" role="form" action="{$_url}plugin/smsGatewayTexin_config">
                    <div class="form-group">
                        <label class="col-md-3 control-label">API Key <span class="text-danger">*</span></label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="texin_api_key" value="{$_c['texin_api_key']}" placeholder="Your Texin API key">
                            <p class="help-block">Get your API key from your Texin account dashboard at <a href="https://texin.co.ke" target="_blank">texin.co.ke</a></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Sender ID <small class="text-muted">(optional)</small></label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="texin_sender_id" value="{$_c['texin_sender_id']}" placeholder="e.g. SpeedRadius">
                            <p class="help-block">Leave blank to use the default Texin sender ID. Must be approved by your Texin account.</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-3 col-md-6">
                            <button class="btn btn-primary" type="submit">
                                <i class="glyphicon glyphicon-floppy-disk"></i> Save Configuration
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {else}
            <div class="panel-body">

                {* Status / info boxes *}
                <div class="row" style="margin-bottom:20px;">
                    <div class="col-sm-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="glyphicon glyphicon-phone"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Gateway</span>
                                <span class="info-box-number">Texin SMS</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="glyphicon glyphicon-key"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">API Key</span>
                                <span class="info-box-number">
                                    {if $_c['texin_api_key']}
                                        {$_c['texin_api_key']|truncate:12:'...':true}
                                    {else}
                                        <span class="text-danger">Not set</span>
                                    {/if}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="info-box">
                            <span class="info-box-icon {if $texin_balance !== null}bg-yellow{else}bg-red{/if}">
                                <i class="glyphicon glyphicon-usd"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">SMS Balance</span>
                                <span class="info-box-number">
                                    {if $texin_balance !== null}
                                        {$texin_balance} units
                                    {elseif $texin_balance_error}
                                        <span class="text-danger" title="{$texin_balance_error}">Error</span>
                                    {else}
                                        —
                                    {/if}
                                </span>
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
                                    <input type="text" class="form-control" id="test_phone" placeholder="07XXXXXXXX or 2547XXXXXXXX">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Message</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="test_message" value="Test message from SpeedRadius via Texin SMS">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-offset-2 col-md-6">
                                    <button class="btn btn-success" onclick="sendTexinTestSMS()">
                                        <i class="glyphicon glyphicon-send"></i> Send Test
                                    </button>
                                    <span id="texin_test_result" style="margin-left:12px;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {* SMS Logs *}
                <div class="panel panel-default">
                    <div class="panel-heading"><h4 class="panel-title">Last 10 SMS Messages</h4></div>
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
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $sms_logs as $log}
                                    <tr>
                                        <td>{$log['created_at']}</td>
                                        <td>{$log['phone']}</td>
                                        <td>{$log['message']|truncate:60}</td>
                                        <td>
                                            {if $log['status'] eq 'sent'}
                                                <span class="label label-success">Sent</span>
                                            {else}
                                                <span class="label label-danger">Failed</span>
                                            {/if}
                                        </td>
                                        <td>{$log['message_id']|default:'-'}</td>
                                        <td class="text-muted small">{$log['status_message']|truncate:50}</td>
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

<script>var _texin_test_url = '{$_url}plugin/smsGatewayTexin_test';</script>
{literal}
<script>
function sendTexinTestSMS() {
    var phone   = document.getElementById('test_phone').value.trim();
    var message = document.getElementById('test_message').value.trim();
    var resultEl = document.getElementById('texin_test_result');

    if (!phone || !message) {
        resultEl.innerHTML = '<span class="text-danger">Phone and message are required.</span>';
        return;
    }

    resultEl.innerHTML = '<span class="text-muted"><i class="glyphicon glyphicon-refresh"></i> Sending...</span>';

    $.ajax({
        url: _texin_test_url,
        method: 'POST',
        data: { phone: phone, message: message },
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                resultEl.innerHTML = '<span class="text-success"><b>&#10003; Sent successfully!</b></span>';
            } else {
                resultEl.innerHTML = '<span class="text-danger"><b>&#10007; Failed:</b> ' + resp.message + '</span>';
            }
        },
        error: function() {
            resultEl.innerHTML = '<span class="text-danger">Request error. Check server logs.</span>';
        }
    });
}
</script>
{/literal}

{include file="sections/footer.tpl"}
