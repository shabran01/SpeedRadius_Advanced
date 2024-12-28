<?php
/* Smarty version 4.5.3, created on 2024-12-25 11:21:54
  from '/var/www/html/snootylique/ui/ui/app-notifications.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_676bc0a274f833_61233357',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '69ce1810a75908607a27db8388f7b797246928cb' => 
    array (
      0 => '/var/www/html/snootylique/ui/ui/app-notifications.tpl',
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
function content_676bc0a274f833_61233357 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/notifications-post">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">
                    <div class="btn-group pull-right">
                        <button class="btn btn-primary btn-xs" title="save" type="submit"><span
                                class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>
                    </div>
                    <?php echo Lang::T('User Notification');?>

                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Expired Notification Message');?>
</label>
                        <div class="col-md-6">
                            <textarea class="form-control" id="expired" name="expired"
                                placeholder="<?php echo Lang::T('Hello');?>
 [[name]], <?php echo Lang::T('your internet package');?>
 [[package]] <?php echo Lang::T('has been expired');?>
"
                                rows="4"><?php if ($_smarty_tpl->tpl_vars['_json']->value['expired'] != '') {
echo Lang::htmlspecialchars($_smarty_tpl->tpl_vars['_json']->value['expired']);
} else {
echo Lang::T('Hello');?>
 [[name]], <?php echo Lang::T('your internet package');?>
 [[package]] <?php echo Lang::T('has been expired');?>
.<?php }?></textarea>
                        </div>
                        <p class="help-block col-md-4">
                            <b>[[name]]</b> - <?php echo Lang::T('will be replaced with Customer Name');?>
.<br>
                            <b>[[username]]</b> - <?php echo Lang::T('will be replaced with Customer username');?>
.<br>
                            <b>[[package]]</b> - <?php echo Lang::T('will be replaced with Package name');?>
.<br>
                            <b>[[price]]</b> - <?php echo Lang::T('will be replaced with Package price');?>
.<br>
                            <b>[[bills]]</b> - <?php echo Lang::T('additional bills for customers');?>
.<br>
                            <b>[[payment_link]]</b> - <a href="./docs/#Reminder%20with%20payment%20link" target="_blank">read documentation</a>.
                        </p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Reminder 7 days');?>
</label>
                        <div class="col-md-6">
                            <textarea class="form-control" id="reminder_7_day" name="reminder_7_day"
                                rows="4"><?php echo Lang::htmlspecialchars($_smarty_tpl->tpl_vars['_json']->value['reminder_7_day']);?>
</textarea>
                        </div>
                        <p class="help-block col-md-4">
                            <b>[[name]]</b> - <?php echo Lang::T('will be replaced with Customer Name');?>
.<br>
                            <b>[[username]]</b> - <?php echo Lang::T('will be replaced with Customer username');?>
.<br>
                            <b>[[package]]</b> - <?php echo Lang::T('will be replaced with Package name');?>
.<br>
                            <b>[[price]]</b> - <?php echo Lang::T('will be replaced with Package price');?>
.<br>
                            <b>[[expired_date]]</b> - <?php echo Lang::T('will be replaced with Expiration date');?>
.<br>
                            <b>[[bills]]</b> - <?php echo Lang::T('additional bills for customers');?>
.<br>
                            <b>[[payment_link]]</b> - <a href="./docs/#Reminder%20with%20payment%20link" target="_blank">read documentation</a>.
                        </p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Reminder 3 days');?>
</label>
                        <div class="col-md-6">
                            <textarea class="form-control" id="reminder_3_day" name="reminder_3_day"
                                rows="4"><?php echo Lang::htmlspecialchars($_smarty_tpl->tpl_vars['_json']->value['reminder_3_day']);?>
</textarea>
                        </div>
                        <p class="help-block col-md-4">
                            <b>[[name]]</b> - <?php echo Lang::T('will be replaced with Customer Name');?>
.<br>
                            <b>[[username]]</b> - <?php echo Lang::T('will be replaced with Customer username');?>
.<br>
                            <b>[[package]]</b> - <?php echo Lang::T('will be replaced with Package name');?>
.<br>
                            <b>[[price]]</b> - <?php echo Lang::T('will be replaced with Package price');?>
.<br>
                            <b>[[expired_date]]</b> - <?php echo Lang::T('will be replaced with Expiration date');?>
.<br>
                            <b>[[bills]]</b> - <?php echo Lang::T('additional bills for customers');?>
.<br>
                            <b>[[payment_link]]</b> - <a href="./docs/#Reminder%20with%20payment%20link" target="_blank">read documentation</a>.
                        </p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Reminder 1 day');?>
</label>
                        <div class="col-md-6">
                            <textarea class="form-control" id="reminder_1_day" name="reminder_1_day"
                                rows="4"><?php echo Lang::htmlspecialchars($_smarty_tpl->tpl_vars['_json']->value['reminder_1_day']);?>
</textarea>
                        </div>
                        <p class="help-block col-md-4">
                            <b>[[name]]</b> - <?php echo Lang::T('will be replaced with Customer Name');?>
.<br>
                            <b>[[username]]</b> - <?php echo Lang::T('will be replaced with Customer username');?>
.<br>
                            <b>[[package]]</b> - <?php echo Lang::T('will be replaced with Package name');?>
.<br>
                            <b>[[price]]</b> - <?php echo Lang::T('will be replaced with Package price');?>
.<br>
                            <b>[[expired_date]]</b> - <?php echo Lang::T('will be replaced with Expiration date');?>
.<br>
                            <b>[[bills]]</b> - <?php echo Lang::T('additional bills for customers');?>
.<br>
                            <b>[[payment_link]]</b> - <a href="./docs/#Reminder%20with%20payment%20link" target="_blank">read documentation</a>.
                        </p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Invoice Notification Payment');?>
</label>
                        <div class="col-md-6">
                            <textarea class="form-control" id="invoice_paid" name="invoice_paid"
                                placeholder="<?php echo Lang::T('Hello');?>
 [[name]], <?php echo Lang::T('your internet package');?>
 [[package]] <?php echo Lang::T('has been expired');?>
"
                                rows="20"><?php echo Lang::htmlspecialchars($_smarty_tpl->tpl_vars['_json']->value['invoice_paid']);?>
</textarea>
                        </div>
                        <p class="col-md-4 help-block">
                            <b>[[company_name]]</b> <?php echo Lang::T('Your Company Name at Settings');?>
.<br>
                            <b>[[address]]</b> <?php echo Lang::T('Your Company Address at Settings');?>
.<br>
                            <b>[[phone]]</b> - <?php echo Lang::T('Your Company Phone at Settings');?>
.<br>
                            <b>[[invoice]]</b> - <?php echo Lang::T('Invoice number');?>
.<br>
                            <b>[[date]]</b> - <?php echo Lang::T('Date invoice created');?>
.<br>
                            <b>[[payment_gateway]]</b> - <?php echo Lang::T('Payment gateway user paid from');?>
.<br>
                            <b>[[payment_channel]]</b> - <?php echo Lang::T('Payment channel user paid from');?>
.<br>
                            <b>[[type]]</b> - <?php echo Lang::T('is Hotspot or PPPOE');?>
.<br>
                            <b>[[plan_name]]</b> - <?php echo Lang::T('Internet Package');?>
.<br>
                            <b>[[plan_price]]</b> - <?php echo Lang::T('Internet Package Prices');?>
.<br>
                            <b>[[name]]</b> - <?php echo Lang::T('Receiver name');?>
.<br>
                            <b>[[user_name]]</b> - <?php echo Lang::T('Username internet');?>
.<br>
                            <b>[[user_password]]</b> - <?php echo Lang::T('User password');?>
.<br>
                            <b>[[expired_date]]</b> - <?php echo Lang::T('Expired datetime');?>
.<br>
                            <b>[[footer]]</b> - <?php echo Lang::T('Invoice Footer');?>
.<br>
                            <b>[[note]]</b> - <?php echo Lang::T('For Notes by admin');?>
.<br>
                        </p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Balance Notification Payment');?>
</label>
                        <div class="col-md-6">
                            <textarea class="form-control" id="invoice_balance" name="invoice_balance"
                                placeholder="<?php echo Lang::T('Hello');?>
 [[name]], <?php echo Lang::T('your internet package');?>
 [[package]] <?php echo Lang::T('has been expired');?>
"
                                rows="20"><?php echo Lang::htmlspecialchars($_smarty_tpl->tpl_vars['_json']->value['invoice_balance']);?>
</textarea>
                        </div>
                        <p class="col-md-4 help-block">
                            <b>[[company_name]]</b> - <?php echo Lang::T('Your Company Name at Settings');?>
.<br>
                            <b>[[address]]</b> - <?php echo Lang::T('Your Company Address at Settings');?>
.<br>
                            <b>[[phone]]</b> - <?php echo Lang::T('Your Company Phone at Settings');?>
.<br>
                            <b>[[invoice]]</b> - <?php echo Lang::T('Invoice number');?>
.<br>
                            <b>[[date]]</b> - <?php echo Lang::T('Date invoice created');?>
.<br>
                            <b>[[payment_gateway]]</b> - <?php echo Lang::T('Payment gateway user paid from');?>
.<br>
                            <b>[[payment_channel]]</b> - <?php echo Lang::T('Payment channel user paid from');?>
.<br>
                            <b>[[type]]</b> - <?php echo Lang::T('is Hotspot or PPPOE');?>
.<br>
                            <b>[[plan_name]]</b> - <?php echo Lang::T('Internet Package');?>
.<br>
                            <b>[[plan_price]]</b> - <?php echo Lang::T('Internet Package Prices');?>
.<br>
                            <b>[[name]]</b> - <?php echo Lang::T('Receiver name');?>
.<br>
                            <b>[[user_name]]</b> - <?php echo Lang::T('Username internet');?>
.<br>
                            <b>[[user_password]]</b> - <?php echo Lang::T('User password');?>
.<br>
                            <b>[[trx_date]]</b> - <?php echo Lang::T('Transaction datetime');?>
.<br>
                            <b>[[balance_before]]</b> - <?php echo Lang::T('Balance Before');?>
.<br>
                            <b>[[balance]]</b> - <?php echo Lang::T('Balance After');?>
.<br>
                            <b>[[footer]]</b> - <?php echo Lang::T('Invoice Footer');?>
.
                        </p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Welcome Message');?>
</label>
                        <div class="col-md-6">
                            <textarea class="form-control" id="welcome_message" name="welcome_message"
                                rows="4"><?php echo Lang::htmlspecialchars($_smarty_tpl->tpl_vars['_json']->value['welcome_message']);?>
</textarea>
                        </div>
                        <p class="help-block col-md-4">
                            <b>[[name]]</b> - <?php echo Lang::T('will be replaced with Customer Name');?>
.<br>
                            <b>[[username]]</b> - <?php echo Lang::T('will be replaced with Customer username');?>
.<br>
                            <b>[[password]]</b> - <?php echo Lang::T('will be replaced with Customer password');?>
.<br>
                            <b>[[url]]</b> - <?php echo Lang::T('will be replaced with Customer Portal URL');?>
.<br>
                            <b>[[company]]</b> - <?php echo Lang::T('will be replaced with Company Name');?>
.<br>
                        </p>
                    </div>
                </div>
                <?php if ($_smarty_tpl->tpl_vars['_c']->value['enable_balance'] == 'yes') {?>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo Lang::T('Send Balance');?>
</label>
                            <div class="col-md-6">
                                <textarea class="form-control" id="balance_send" name="balance_send"
                                    rows="4"><?php if ($_smarty_tpl->tpl_vars['_json']->value['balance_send']) {
echo Lang::htmlspecialchars($_smarty_tpl->tpl_vars['_json']->value['balance_send']);
} else {
echo Lang::htmlspecialchars($_smarty_tpl->tpl_vars['_default']->value['balance_send']);
}?></textarea>
                            </div>
                            <p class="col-md-4 help-block">
                                <b>[[name]]</b> - <?php echo Lang::T('Receiver name');?>
.<br>
                                <b>[[balance]]</b> - <?php echo Lang::T('how much balance have been send');?>
.<br>
                                <b>[[current_balance]]</b> - <?php echo Lang::T('Current Balance');?>
.
                            </p>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo Lang::T('Received Balance');?>
</label>
                            <div class="col-md-6">
                                <textarea class="form-control" id="balance_received" name="balance_received"
                                    rows="4"><?php if ($_smarty_tpl->tpl_vars['_json']->value['balance_received']) {
echo Lang::htmlspecialchars($_smarty_tpl->tpl_vars['_json']->value['balance_received']);
} else {
echo Lang::htmlspecialchars($_smarty_tpl->tpl_vars['_default']->value['balance_received']);
}?></textarea>
                            </div>
                            <p class="col-md-4 help-block">
                                <b>[[name]]</b> - <?php echo Lang::T('Sender name');?>
.<br>
                                <b>[[balance]]</b> - <?php echo Lang::T('how much balance have been received');?>
.<br>
                                <b>[[current_balance]]</b> - <?php echo Lang::T('Current Balance');?>
.
                            </p>
                        </div>
                    </div>
                <?php }?>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <button class="btn btn-success btn-block" type="submit"><?php echo Lang::T('Save Changes');?>
</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
