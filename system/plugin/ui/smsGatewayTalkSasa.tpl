{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">SMS Gateway Configuration</h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" role="form" action="{$_url}plugin/smsGatewayTalkSasa_config">
                    <div class="form-group">
                        <label class="col-md-2 control-label">API Token</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="talksasa_api_token" name="talksasa_api_token" value="{$_c['talksasa_api_token']}">
                            <p class="help-block">Your TalkSasa API Token</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Sender ID</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="talksasa_sender_id" name="talksasa_sender_id" value="{$_c['talksasa_sender_id']}">
                            <p class="help-block">Your assigned Sender ID (alphanumeric, max 11 characters)</p>
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
            <div class="panel-body">                <div class="row">
                    <div class="col-md-6">
                        <h4>Available SMS Units: <span id="sms_balance">Loading...</span></h4>
                        <button class="btn btn-info" onclick="checkBalance()">Check Balance</button>
                        <a href="https://bulksms.talksasa.com/account/top-up" target="_blank" class="btn btn-success ml-2">Top Up Balance</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">Recent SMS Messages</h3>
            </div>
            <div class="panel-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <button class="btn btn-info" onclick="checkMessages()">Refresh Messages</button>
                    </div>
                </div>
                <div class="table-responsive" id="messages_data">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Recipient</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">Loading messages...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Local SMS Logs</h3>
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
                                <td>{$log['message']}</td>
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
        url: '{$_url}plugin/smsGatewayTalkSasa_check_balance',
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
        return data.units || data.balance || JSON.stringify(data);
    }
    return data;
}

function checkMessages() {
    $('#messages_data').text('Checking...');
    
    $.ajax({
        url: '{$_url}plugin/smsGatewayTalkSasa_messages',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if(data.success) {
                $('#messages_data').html(formatMessagesData(data.data));
            } else {
                $('#messages_data').text('Error: ' + data.message);
                console.error('Messages check failed:', data.message);
            }
        },
        error: function(xhr, status, error) {
            $('#messages_data').text('Failed to fetch messages');
            console.error('AJAX error:', status, error);
            console.error('Response:', xhr.responseText);
        }
    });
}

function formatMessagesData(data) {
    if (!Array.isArray(data)) {
        return '<table class="table table-striped table-bordered"><tr><td class="text-center">No messages available</td></tr></table>';
    }

    let html = '<table class="table table-striped table-bordered">';
    html += '<thead><tr>';
    html += '<th>Date/Time</th>';
    html += '<th>Recipient</th>';
    html += '<th>Message</th>';
    html += '<th>Status</th>';
    html += '<th>Cost</th>';
    html += '</tr></thead><tbody>';

    if (data.length === 0) {
        html += '<tr><td colspan="5" class="text-center">No messages found</td></tr>';
    } else {
        data.forEach(function(msg) {
            html += '<tr>';
            html += '<td>' + (msg.created_at || msg.date || '-') + '</td>';
            html += '<td>' + (msg.recipient || msg.phone || '-') + '</td>';
            html += '<td>' + (msg.message || '-') + '</td>';
            html += '<td>' + formatMessageStatus(msg.status) + '</td>';
            html += '<td>' + (msg.cost || msg.units || '-') + '</td>';
            html += '</tr>';
        });
    }

    html += '</tbody></table>';
    return html;
}

function formatMessageStatus(status) {
    if (!status) return '<span class="label label-default">Unknown</span>';
    
    status = status.toLowerCase();
    if (status === 'delivered' || status === 'sent' || status === 'success') {
        return '<span class="label label-success">' + status + '</span>';
    } else if (status === 'failed' || status === 'error') {
        return '<span class="label label-danger">' + status + '</span>';
    } else if (status === 'pending' || status === 'processing') {
        return '<span class="label label-warning">' + status + '</span>';
    }
    return '<span class="label label-info">' + status + '</span>';
}

// Check balance and messages on page load
$(document).ready(function() {
    setTimeout(function() {
        checkBalance();
        checkMessages();
    }, 1000); // Delay initial check by 1 second
});
</script>

{include file="sections/footer.tpl"}
