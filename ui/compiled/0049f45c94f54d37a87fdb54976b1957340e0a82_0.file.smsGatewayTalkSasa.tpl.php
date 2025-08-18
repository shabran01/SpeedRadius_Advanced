<?php
/* Smarty version 4.5.3, created on 2025-08-17 11:37:42
  from '/var/www/html/ISP/system/plugin/ui/smsGatewayTalkSasa.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a194d69449e4_82325014',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0049f45c94f54d37a87fdb54976b1957340e0a82' => 
    array (
      0 => '/var/www/html/ISP/system/plugin/ui/smsGatewayTalkSasa.tpl',
      1 => 1754916133,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:sections/header.tpl' => 1,
    'file:sections/footer.tpl' => 1,
  ),
),false)) {
function content_68a194d69449e4_82325014 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">SMS Gateway Configuration</h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plugin/smsGatewayTalkSasa_config">
                    <div class="form-group">
                        <label class="col-md-2 control-label">API Token</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="talksasa_api_token" name="talksasa_api_token" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['talksasa_api_token'];?>
">
                            <p class="help-block">Your TalkSasa API Token</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Sender ID</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="talksasa_sender_id" name="talksasa_sender_id" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['talksasa_sender_id'];?>
">
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
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sms_logs']->value, 'log');
$_smarty_tpl->tpl_vars['log']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['log']->value) {
$_smarty_tpl->tpl_vars['log']->do_else = false;
?>
                            <tr>
                                <td><?php echo $_smarty_tpl->tpl_vars['log']->value['created_at'];?>
</td>
                                <td><?php echo $_smarty_tpl->tpl_vars['log']->value['phone'];?>
</td>
                                <td><?php echo $_smarty_tpl->tpl_vars['log']->value['message'];?>
</td>
                                <td>
                                    <?php if ($_smarty_tpl->tpl_vars['log']->value['status'] == 'sent') {?>
                                        <span class="label label-success">Sent</span>
                                    <?php } else { ?>
                                        <span class="label label-danger" title="<?php echo $_smarty_tpl->tpl_vars['log']->value['status_message'];?>
">Failed</span>
                                    <?php }?>
                                </td>
                                <td><?php echo (($tmp = $_smarty_tpl->tpl_vars['log']->value['message_id'] ?? null)===null||$tmp==='' ? '-' ?? null : $tmp);?>
</td>
                            </tr>
                            <?php
}
if ($_smarty_tpl->tpl_vars['log']->do_else) {
?>
                            <tr>
                                <td colspan="5" class="text-center">No SMS messages found</td>
                            </tr>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo '<script'; ?>
>
function checkBalance() {
    $('#sms_balance').text('Checking...');
    
    $.ajax({
        url: '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plugin/smsGatewayTalkSasa_check_balance',
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
        url: '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plugin/smsGatewayTalkSasa_messages',
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
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
