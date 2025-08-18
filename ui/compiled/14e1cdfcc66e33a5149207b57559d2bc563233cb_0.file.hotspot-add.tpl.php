<?php
/* Smarty version 4.5.3, created on 2025-08-17 12:20:24
  from '/var/www/html/ISP/ui/ui/hotspot-add.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a19ed80c3133_45357874',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '14e1cdfcc66e33a5149207b57559d2bc563233cb' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/hotspot-add.tpl',
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
function content_68a19ed80c3133_45357874 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-primary panel-hovered panel-stacked mb30">
            <div class="panel-heading"><?php echo Lang::T('Add Service Package');?>
</div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
services/add-post">
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Status');?>

                            <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                data-trigger="focus" data-container="body"
                                data-content="Customer cannot buy disabled Package, but admin can recharge it, use it if you want only admin recharge it">?</a>
                        </label>
                        <div class="col-md-10">
                            <input type="radio" name="enabled" value="1" checked> <?php echo Lang::T('Active');?>

                            <input type="radio" name="enabled" value="0"> <?php echo Lang::T('Not Active');?>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Type');?>

                            <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                data-trigger="focus" data-container="body"
                                data-content="Postpaid will have fix expired date">?</a>
                        </label>
                        <div class="col-md-10">
                            <input type="radio" name="prepaid" onclick="prePaid()" value="yes" checked> <?php echo Lang::T('Prepaid');?>

                            <input type="radio" name="prepaid" onclick="postPaid()" value="no"> <?php echo Lang::T('Postpaid');?>

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Package Type');?>

                            <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                data-trigger="focus" data-container="body"
                                data-content="Personal Plan will only show to personal Customer, Business package will only show to Business Customer">?</a>
                        </label>
                        <div class="col-md-10">
                            <input type="radio" name="plan_type" value="Personal" checked> <?php echo Lang::T('Personal');?>

                            <input type="radio" name="plan_type" value="Business"> <?php echo Lang::T('Business');?>

                        </div>
                    </div>
                    <?php if ($_smarty_tpl->tpl_vars['_c']->value['radius_enable']) {?>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Radius
                                <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                    data-trigger="focus" data-container="body"
                                    data-content="If you enable Radius, choose device to radius, except if you have custom device.">?</a>
                            </label>
                            <div class="col-md-6">
                                <label class="radio-inline">
                                    <input type="checkbox" name="radius" onclick="isRadius(this)" value="1"> <?php echo Lang::T('Radius Package');?>

                                </label>
                            </div>
                            <p class="help-block col-md-4"><?php echo Lang::T('Cannot be change after saved');?>
</p>
                        </div>
                    <?php }?>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Device');?>

                            <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                data-trigger="focus" data-container="body"
                                data-content="This Device are the logic how PHPNuxBill Communicate with Mikrotik or other Devices">?</a>
                        </label>
                        <div class="col-md-6">
                            <select class="form-control" id="device" name="device">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['devices']->value, 'dev');
$_smarty_tpl->tpl_vars['dev']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['dev']->value) {
$_smarty_tpl->tpl_vars['dev']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['dev']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['dev']->value;?>
</option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Package Name');?>
</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="name" name="name" maxlength="40">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Package Type');?>
</label>
                        <div class="col-md-10">
                            <input type="radio" id="Unlimited" name="typebp" value="Unlimited" checked>
                            <?php echo Lang::T('Unlimited');?>

                            <input type="radio" id="Limited" name="typebp" value="Limited"> <?php echo Lang::T('Limited');?>

                        </div>
                    </div>
                    <div style="display:none;" id="Type">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo Lang::T('Limit Type');?>
</label>
                            <div class="col-md-10">
                                <input type="radio" id="Time_Limit" name="limit_type" value="Time_Limit" checked>
                                <?php echo Lang::T('Time Limit');?>

                                <input type="radio" id="Data_Limit" name="limit_type" value="Data_Limit">
                                <?php echo Lang::T('Data Limit');?>

                                <input type="radio" id="Both_Limit" name="limit_type" value="Both_Limit">
                                <?php echo Lang::T('Both Limit');?>

                            </div>
                        </div>
                    </div>
                    <div style="display:none;" id="TimeLimit">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo Lang::T('Time Limit');?>
</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="time_limit" name="time_limit" value="0">
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" id="time_unit" name="time_unit">
                                    <option value="Hrs"><?php echo Lang::T('Hrs');?>
</option>
                                    <option value="Mins"><?php echo Lang::T('Mins');?>
</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div style="display:none;" id="DataLimit">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo Lang::T('Data Limit');?>
</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="data_limit" name="data_limit" value="0">
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" id="data_unit" name="data_unit">
                                    <option value="MB">MBs</option>
                                    <option value="GB">GBs</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><a
                                href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
bandwidth/add"><?php echo Lang::T('Bandwidth Name');?>
</a></label>
                        <div class="col-md-6">
                            <select id="id_bw" name="id_bw" class="form-control select2">
                                <option value=""><?php echo Lang::T('Select Bandwidth');?>
...</option>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['d']->value, 'ds');
$_smarty_tpl->tpl_vars['ds']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ds']->value) {
$_smarty_tpl->tpl_vars['ds']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['ds']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['ds']->value['name_bw'];?>
</option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Package Price');?>
</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['_c']->value['currency_code'];?>
</span>
                                <input type="number" class="form-control" name="price" required>
                            </div>
                        </div>
                        <?php if ($_smarty_tpl->tpl_vars['_c']->value['enable_tax'] == 'yes') {?>
                            <?php if ($_smarty_tpl->tpl_vars['_c']->value['tax_rate'] == 'custom') {?>
                                <p class="help-block col-md-4"><?php echo number_format($_smarty_tpl->tpl_vars['_c']->value['custom_tax_rate'],2);?>
 % <?php echo Lang::T('Tax Rates
                            will be added');?>
</p>
                            <?php } else { ?>
                                <p class="help-block col-md-4"><?php echo number_format($_smarty_tpl->tpl_vars['_c']->value['tax_rate']*100,2);?>
 % <?php echo Lang::T('Tax Rates
                            will be added');?>
</p>
                            <?php }?>
                        <?php }?>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Shared Users');?>

                            <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                data-trigger="focus" data-container="body"
                                data-content="How many devices can online in one Customer account">?</a>
                        </label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="sharedusers" name="sharedusers" value="1">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Package Validity');?>
</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="validity" name="validity">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" id="validity_unit" name="validity_unit">
                            </select>
                        </div>
                        <p class="help-block col-md-4"><?php echo Lang::T('1 Period = 1 Month, Expires the 20th of each month');?>

                        </p>
                    </div>
                    <div class="form-group hidden" id="expired_date">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Expired Date');?>

                            <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                data-trigger="focus" data-container="body"
                                data-content="Expired will be this date every month">?</a>
                        </label>
                        <div class="col-md-6">
                            <input type="number" class="form-control" name="expired_date" maxlength="2" value="20" min="1" max="28" step="1" >
                        </div>
                    </div>
                    <span id="routerChoose" class="">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><a
                                    href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
routers/add"><?php echo Lang::T('Router Name');?>
</a></label>
                            <div class="col-md-6">
                                <select id="routers" name="routers" required class="form-control select2">
                                    <option value=''><?php echo Lang::T('Select Routers');?>
</option>
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['r']->value, 'rs');
$_smarty_tpl->tpl_vars['rs']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['rs']->value) {
$_smarty_tpl->tpl_vars['rs']->do_else = false;
?>
                                        <option value="<?php echo $_smarty_tpl->tpl_vars['rs']->value['name'];?>
"><?php echo $_smarty_tpl->tpl_vars['rs']->value['name'];?>
</option>
                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                </select>
                                <p class="help-block"><?php echo Lang::T('Cannot be change after saved');?>
</p>
                            </div>
                        </div>
                    </span>
                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button class="btn btn-success" onclick="return ask(this, 'Continue the Hotspot Package creation process?')" type="submit"><?php echo Lang::T('Save Changes');?>
</button>
                            Or <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
services/hotspot"><?php echo Lang::T('Cancel');?>
</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
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
    document.addEventListener("DOMContentLoaded", function(event) {
        prePaid()
    })
<?php echo '</script'; ?>
>
<?php if ($_smarty_tpl->tpl_vars['_c']->value['radius_enable']) {?>
    
        <?php echo '<script'; ?>
>
            function isRadius(cek) {
                if (cek.checked) {
                    $("#routerChoose").addClass('hidden');
                    document.getElementById("routers").required = false;
                } else {
                    document.getElementById("routers").required = true;
                    $("#routerChoose").removeClass('hidden');
                }
            }
        <?php echo '</script'; ?>
>
    
<?php }?>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
