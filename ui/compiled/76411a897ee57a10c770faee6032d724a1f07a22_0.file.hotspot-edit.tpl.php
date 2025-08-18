<?php
/* Smarty version 4.5.3, created on 2025-08-18 16:30:48
  from '/var/www/html/ISP/ui/ui/hotspot-edit.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a32b08b2b1c9_81734998',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '76411a897ee57a10c770faee6032d724a1f07a22' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/hotspot-edit.tpl',
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
function content_68a32b08b2b1c9_81734998 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>


<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
services/edit-post">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading"><?php echo Lang::T('Edit Service Package');?>
 || <?php echo $_smarty_tpl->tpl_vars['d']->value['name_plan'];?>
</div>
                <div class="panel-body">
                    <input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
">
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Status');?>

                            <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                data-trigger="focus" data-container="body"
                                data-content="Customer cannot buy disabled Plan, but admin can recharge it, use it if you want only admin recharge it">?</a>
                        </label>
                        <div class="col-md-9">
                            <input type="radio" name="enabled" value="1" <?php if ($_smarty_tpl->tpl_vars['d']->value['enabled'] == 1) {?>checked<?php }?>> <?php echo Lang::T('Active');?>

                            <input type="radio" name="enabled" value="0" <?php if ($_smarty_tpl->tpl_vars['d']->value['enabled'] == 0) {?>checked<?php }?>> <?php echo Lang::T('Not Active');?>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Type');?>

                            <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                data-trigger="focus" data-container="body"
                                data-content="Postpaid will have fix expired date">?</a>
                        </label>
                        <div class="col-md-9">
                            <input type="radio" name="prepaid" onclick="prePaid()" value="yes"
                                <?php if ($_smarty_tpl->tpl_vars['d']->value['prepaid'] == 'yes') {?>checked<?php }?>>
                            <?php echo Lang::T('Prepaid');?>

                            <input type="radio" name="prepaid" onclick="postPaid()" value="no"
                                <?php if ($_smarty_tpl->tpl_vars['d']->value['prepaid'] == 'no') {?>checked<?php }?>> <?php echo Lang::T('Postpaid');?>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Package Type');?>

                            <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                data-trigger="focus" data-container="body"
                                data-content="Personal Plan will only show to personal Customer, Business plan will only show to Business Customer">?</a>
                        </label>
                        <div class="col-md-9">
                            <input type="radio" name="plan_type" value="Personal"
                                <?php if ($_smarty_tpl->tpl_vars['d']->value['plan_type'] == 'Personal') {?>checked<?php }?>>
                            <?php echo Lang::T('Personal');?>

                            <input type="radio" name="plan_type" value="Business"
                                <?php if ($_smarty_tpl->tpl_vars['d']->value['plan_type'] == 'Business') {?>checked<?php }?>> <?php echo Lang::T('Business');?>

                        </div>
                    </div>
                    <?php if ($_smarty_tpl->tpl_vars['_c']->value['radius_enable'] && $_smarty_tpl->tpl_vars['d']->value['is_radius']) {?>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Radius
                                <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                    data-trigger="focus" data-container="body"
                                    data-content="If you enable Radius, choose device to radius, except if you have custom device.">?</a>
                            </label>
                            <div class="col-md-9">
                                <label class="label label-primary">RADIUS</label>
                            </div>
                        </div>
                    <?php }?>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Device');?>

                            <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                data-trigger="focus" data-container="body"
                                data-content="This Device are the logic how PHPNuxBill Communicate with Mikrotik or other Devices">?</a>
                        </label>
                        <div class="col-md-9">
                            <select class="form-control" id="device" name="device">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['devices']->value, 'dev');
$_smarty_tpl->tpl_vars['dev']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['dev']->value) {
$_smarty_tpl->tpl_vars['dev']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['dev']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['dev']->value == $_smarty_tpl->tpl_vars['d']->value['device']) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['dev']->value;?>
</option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Package Name');?>
</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="name" name="name" maxlength="40"
                                value="<?php echo $_smarty_tpl->tpl_vars['d']->value['name_plan'];?>
">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Package Type');?>
</label>
                        <div class="col-md-9">
                            <input type="radio" id="Unlimited" name="typebp" value="Unlimited"
                                <?php if ($_smarty_tpl->tpl_vars['d']->value['typebp'] == 'Unlimited') {?> checked <?php }?>> <?php echo Lang::T('Unlimited');?>

                            <input type="radio" id="Limited" name="typebp" value="Limited"
                                <?php if ($_smarty_tpl->tpl_vars['d']->value['typebp'] == 'Limited') {?> checked <?php }?>>
                            <?php echo Lang::T('Limited');?>

                        </div>
                    </div>
                    <div <?php if ($_smarty_tpl->tpl_vars['d']->value['typebp'] == 'Unlimited') {?> style="display:none;" <?php }?> id="Type">
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?php echo Lang::T('Limit Type');?>
</label>
                            <div class="col-md-9">
                                <input type="radio" id="Time_Limit" name="limit_type" value="Time_Limit"
                                    <?php if ($_smarty_tpl->tpl_vars['d']->value['limit_type'] == 'Time_Limit') {?> checked <?php }?>> <?php echo Lang::T('Time Limit');?>

                                <input type="radio" id="Data_Limit" name="limit_type" value="Data_Limit"
                                    <?php if ($_smarty_tpl->tpl_vars['d']->value['limit_type'] == 'Data_Limit') {?> checked <?php }?>> <?php echo Lang::T('Data Limit');?>

                                <input type="radio" id="Both_Limit" name="limit_type" value="Both_Limit"
                                    <?php if ($_smarty_tpl->tpl_vars['d']->value['limit_type'] == 'Both_Limit') {?> checked <?php }?>> <?php echo Lang::T('Both Limit');?>

                            </div>
                        </div>
                    </div>
                    <div <?php if ($_smarty_tpl->tpl_vars['d']->value['typebp'] == 'Unlimited') {?> style="display:none;"
                    <?php } elseif (($_smarty_tpl->tpl_vars['d']->value['time_limit']) == '0') {?>
                        style="display:none;" <?php }?> id="TimeLimit">
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?php echo Lang::T('Time Limit');?>
</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="time_limit" name="time_limit"
                                    value="<?php echo $_smarty_tpl->tpl_vars['d']->value['time_limit'];?>
">
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" id="time_unit" name="time_unit">
                                    <option value="Hrs" <?php if ($_smarty_tpl->tpl_vars['d']->value['time_unit'] == 'Hrs') {?> selected <?php }?>><?php echo Lang::T('Hrs');?>

                                    </option>
                                    <option value="Mins" <?php if ($_smarty_tpl->tpl_vars['d']->value['time_unit'] == 'Mins') {?> selected <?php }?>><?php echo Lang::T('Mins');?>

                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div <?php if ($_smarty_tpl->tpl_vars['d']->value['typebp'] == 'Unlimited') {?> style="display:none;"
                    <?php } elseif (($_smarty_tpl->tpl_vars['d']->value['data_limit']) == '0') {?>
                        style="display:none;" <?php }?> id="DataLimit">
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?php echo Lang::T('Data Limit');?>
</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="data_limit" name="data_limit"
                                    value="<?php echo $_smarty_tpl->tpl_vars['d']->value['data_limit'];?>
">
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" id="data_unit" name="data_unit">
                                    <option value="MB" <?php if ($_smarty_tpl->tpl_vars['d']->value['data_unit'] == 'MB') {?> selected <?php }?>>MBs</option>
                                    <option value="GB" <?php if ($_smarty_tpl->tpl_vars['d']->value['data_unit'] == 'GB') {?> selected <?php }?>>GBs</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><a
                                href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
bandwidth/add"><?php echo Lang::T('Bandwidth Name');?>
</a></label>
                        <div class="col-md-9">
                            <select id="id_bw" name="id_bw" class="form-control select2">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['b']->value, 'bs');
$_smarty_tpl->tpl_vars['bs']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['bs']->value) {
$_smarty_tpl->tpl_vars['bs']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['bs']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['d']->value['id_bw'] == $_smarty_tpl->tpl_vars['bs']->value['id']) {?> selected <?php }?>>
                                        <?php echo $_smarty_tpl->tpl_vars['bs']->value['name_bw'];?>
</option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group has-success">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Package Price');?>
</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['_c']->value['currency_code'];?>
</span>
                                <input type="number" class="form-control" name="price" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['price'];?>
" required>
                            </div>
                        </div>
                        <?php if ($_smarty_tpl->tpl_vars['_c']->value['enable_tax'] == 'yes') {?>
                            <?php if ($_smarty_tpl->tpl_vars['_c']->value['tax_rate'] == 'custom') {?>
                                <p class="help-block col-md-3"><?php echo number_format($_smarty_tpl->tpl_vars['_c']->value['custom_tax_rate'],2);?>
 % <?php echo Lang::T('Tax Rates
                            will be added');?>
</p>
                            <?php } else { ?>
                                <p class="help-block col-md-3"><?php echo number_format($_smarty_tpl->tpl_vars['_c']->value['tax_rate']*100,2);?>
 % <?php echo Lang::T('Tax Rates
                            will be added');?>
</p>
                            <?php }?>
                        <?php }?>
                    </div>
                    <div class="form-group has-warning">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Price Before Discount');?>
</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['_c']->value['currency_code'];?>
</span>
                                <input type="number" class="form-control" name="price_old" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['price_old'];?>
">
                            </div>
                            <p class="help-block"><?php echo Lang::T('For Discount Rate, this is price before get discount, must be more expensive with real price');?>
</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Shared Users');?>

                            <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                data-trigger="focus" data-container="body"
                                data-content="How many devices can online in one Customer account">?</a>
                        </label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="sharedusers" name="sharedusers"
                                value="<?php echo $_smarty_tpl->tpl_vars['d']->value['shared_users'];?>
">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Package Validity');?>
</label>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="validity" name="validity"
                                value="<?php echo $_smarty_tpl->tpl_vars['d']->value['validity'];?>
">
                        </div>
                        <div class="col-md-6">
                            <select class="form-control" id="validity_unit" name="validity_unit">
                                <?php if ($_smarty_tpl->tpl_vars['d']->value['prepaid'] == 'yes') {?>
                                    <option value="Mins" <?php if ($_smarty_tpl->tpl_vars['d']->value['validity_unit'] == 'Mins') {?> selected <?php }?>><?php echo Lang::T('Mins');?>

                                    </option>
                                    <option value="Hrs" <?php if ($_smarty_tpl->tpl_vars['d']->value['validity_unit'] == 'Hrs') {?> selected <?php }?>><?php echo Lang::T('Hrs');?>

                                    </option>
                                    <option value="Days" <?php if ($_smarty_tpl->tpl_vars['d']->value['validity_unit'] == 'Days') {?> selected <?php }?>><?php echo Lang::T('Days');?>

                                    </option>
                                    <option value="Months" <?php if ($_smarty_tpl->tpl_vars['d']->value['validity_unit'] == 'Months') {?> selected <?php }?>>
                                        <?php echo Lang::T('Months');?>
</option>
                                <?php } else { ?>
                                    <option value="Period" <?php if ($_smarty_tpl->tpl_vars['d']->value['validity_unit'] == 'Period') {?> selected <?php }?>>
                                        <?php echo Lang::T('Period');?>
</option>
                                <?php }?>
                            </select>
                            <p class="help-block"><?php echo Lang::T('1 Period = 1 Month, Expires the 20th of each month');?>

                            </p>
                        </div>
                    </div>
                    <div class="form-group <?php if ($_smarty_tpl->tpl_vars['d']->value['prepaid'] == 'yes') {?>hidden<?php }?>" id="expired_date">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Expired Date');?>

                            <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                data-trigger="focus" data-container="body"
                                data-content="Expired will be this date every month">?</a>
                        </label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" name="expired_date" maxlength="2"
                                value="<?php if ($_smarty_tpl->tpl_vars['d']->value['expired_date']) {
echo $_smarty_tpl->tpl_vars['d']->value['expired_date'];
} else { ?>20<?php }?>" min="1" max="28"
                                step="1">
                        </div>
                    </div>
                    <span id="routerChoose" class="<?php if ($_smarty_tpl->tpl_vars['d']->value['is_radius']) {?>hidden<?php }?>">
                        <div class="form-group">
                            <label class="col-md-3 control-label"><a
                                    href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
routers/add"><?php echo Lang::T('Router Name');?>
</a></label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="routers" name="routers"
                                    value="<?php echo $_smarty_tpl->tpl_vars['d']->value['routers'];?>
" readonly>
                            </div>
                        </div>
                    </span>
                    <legend><?php echo Lang::T('Expired Action');?>
 <sub><?php echo Lang::T('Optional');?>
</sub></legend>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Expired Internet Package');?>
</label>
                        <div class="col-md-9">
                            <select id="plan_expired" name="plan_expired" class="form-control select2">
                                <option value='0'><?php echo Lang::T('Default - Remove Customer');?>
</option>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['exps']->value, 'exp');
$_smarty_tpl->tpl_vars['exp']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['exp']->value) {
$_smarty_tpl->tpl_vars['exp']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['exp']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['d']->value['plan_expired'] == $_smarty_tpl->tpl_vars['exp']->value['id']) {?> selected <?php }?>>
                                        <?php echo $_smarty_tpl->tpl_vars['exp']->value['name_plan'];?>
</option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </select>
                            <p class="help-block">
                                <?php echo Lang::T('When Expired, customer will be move to selected internet package');?>
</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php if (!$_smarty_tpl->tpl_vars['d']->value['is_radius']) {?>
            <div class="col-md-6">
                <div class="panel panel-primary panel-hovered panel-stacked mb30">
                    <div class="panel-heading"><?php echo Lang::T('on-login / on-up');?>
</div>
                    <div class="panel-body">
                        <textarea class="form-control" id="code" name="on_login"
                            style="font-family: 'Courier New', Courier, monospace;" rows="15"><?php echo $_smarty_tpl->tpl_vars['d']->value['on_login'];?>
</textarea>
                    </div>
                </div>
                <div class="panel panel-primary panel-hovered panel-stacked mb30">
                    <div class="panel-heading"><?php echo Lang::T('on-logout / on-down');?>
</div>
                    <div class="panel-body">
                        <textarea class="form-control" id="code2" name="on_logout"
                            style="font-family: 'Courier New', Courier, monospace;" rows="15"><?php echo $_smarty_tpl->tpl_vars['d']->value['on_logout'];?>
</textarea>
                    </div>
                </div>
            </div>
        <?php }?>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-9">
            <button class="btn btn-success" onclick="return ask(this, 'Continue the process of changing Hotspot Package data?')" type="submit"><?php echo Lang::T('Save Changes');?>
</button>
            Or <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
services/hotspot"><?php echo Lang::T('Cancel');?>
</a>
        </div>
    </div>
</form>

<?php echo '<script'; ?>
>
    var preOpt = `<option value="Mins"><?php echo Lang::T('Mins');?>
</option>
    <option value="Hrs"><?php echo Lang::T('Hrs');?>
</option>
    <option value="Days"><?php echo Lang::T('Days');?>
</option>
    <option value="Months"><?php echo Lang::T('Months');?>
</option>`;
    var postOpt = `<option value="Period"><?php echo Lang::T('Period');?>
</option>`;
    function prePaid() {
        $("#validity_unit").html(preOpt);
        $('#expired_date').addClass('hidden');
    }

    function postPaid() {
        $("#validity_unit").html(postOpt);
        $("#expired_date").removeClass('hidden');
    }
<?php echo '</script'; ?>
>

<?php if ($_smarty_tpl->tpl_vars['_c']->value['radius_enable'] && $_smarty_tpl->tpl_vars['d']->value['is_radius']) {?>
    
        <?php echo '<script'; ?>
>
            function isRadius(cek) {
                if (cek.checked) {
                    $("#routerChoose").addClass('hidden');
                    document.getElementById("routers").required = false;
                    document.getElementById("Limited").disabled = true;
                } else {
                    document.getElementById("Limited").disabled = false;
                    document.getElementById("routers").required = true;
                    $("#routerChoose").removeClass('hidden');
                }
            }
        <?php echo '</script'; ?>
>
    
<?php }?>

<?php echo '<script'; ?>
 language="javascript" type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="javascript" type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/perl/perl.min.js"><?php echo '</script'; ?>
>

<link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css">
</link>
<link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/abbott.min.css">
</link>

<?php echo '<script'; ?>
>
    CodeMirror.fromTextArea(document.getElementById('code'), {
        lineNumbers: true,
        mode: 'text/x-perl',
    });
    CodeMirror.fromTextArea(document.getElementById('code2'), {
        lineNumbers: true,
        mode: 'text/x-perl',
    });
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
