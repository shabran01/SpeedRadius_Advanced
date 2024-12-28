<?php
/* Smarty version 4.5.3, created on 2024-12-24 13:10:03
  from '/var/www/html/snootylique/system/plugin/ui/smsGateway.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_676a887b712a67_92135224',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3115ae2b46a91ea905ecea26f1f8fcf92eddf078' => 
    array (
      0 => '/var/www/html/snootylique/system/plugin/ui/smsGateway.tpl',
      1 => 1734662748,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:sections/header.tpl' => 1,
    'file:sections/footer.tpl' => 1,
  ),
),false)) {
function content_676a887b712a67_92135224 (Smarty_Internal_Template $_smarty_tpl) {
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
plugin/smsGateway_config">
                    <div class="form-group">
                        <label class="col-md-2 control-label">API Key</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="blessed_texts_api_key" name="blessed_texts_api_key" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['blessed_texts_api_key'];?>
">
                            <p class="help-block">Your Blessed Texts API Key</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Sender ID</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="blessed_texts_sender_id" name="blessed_texts_sender_id" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['blessed_texts_sender_id'];?>
">
                            <p class="help-block">Your assigned Sender ID (e.g., 23107)</p>
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
    $('#debug_info').hide();
    $('#debug_output').text('');
    
    $.ajax({
        url: '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plugin/smsGateway_check_balance',
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            if(data.success) {
                $('#sms_balance').text(data.balance + ' credits');
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

// Check balance on page load
$(document).ready(function() {
    setTimeout(checkBalance, 1000); // Delay initial check by 1 second
});
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
