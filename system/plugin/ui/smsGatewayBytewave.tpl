{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">BytewaveSMS Gateway Configuration</h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" role="form" action="{$_url}plugin/smsGatewayBytewave_config">
                    <div class="form-group">
                        <label class="col-md-2 control-label">API Token</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="bytewave_api_token" name="bytewave_api_token" value="{$_c['bytewave_api_token']}">
                            <p class="help-block">Your BytewaveSMS API Token</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Sender ID</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="bytewave_sender_id" name="bytewave_sender_id" value="{$_c['bytewave_sender_id']}">
                            <p class="help-block">Your assigned Sender ID (e.g., BytewaveSMS)</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-primary" type="submit">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">SMS Balance</h3>
            </div>
            <div class="panel-body">
                <p>Current Balance: <span id="sms_balance">Loading...</span></p>
                <button class="btn btn-info" onclick="checkBalance()">Check Balance</button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Last 10 SMS Messages</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Phone</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Message ID</th>
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
                                        <span class="label label-danger" title="{$log['status_message']}">Failed</span>
                                    {/if}
                                </td>
                                <td>{$log['message_id']|default:'-'}</td>
                            </tr>
                            {foreachelse}
                            <tr>
                                <td colspan="5" class="text-center">No SMS messages found</td>
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
function checkBalance() {
    $('#sms_balance').text('Checking...');
    
    $.ajax({
        url: '{$_url}plugin/smsGatewayBytewave_check_balance',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if(data.success) {
                $('#sms_balance').html(formatBalanceData(data.data));
            } else {
                $('#sms_balance').text('Error: ' + data.message);
                console.error('Balance check failed:', data.message);
            }
        },
        error: function(xhr, status, error) {
            $('#sms_balance').text('Failed to fetch balance');
            console.error('AJAX error:', status, error);
            console.error('Response:', xhr.responseText);
        }
    });
}

function formatBalanceData(data) {
    if (typeof data === 'object') {
        return data.balance || data.units || JSON.stringify(data);
    }
    return data;
}

// Check balance on page load
$(document).ready(function() {
    setTimeout(checkBalance, 1000); // Delay initial check by 1 second
});
</script>

{include file="sections/footer.tpl"}
