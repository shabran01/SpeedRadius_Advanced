<?php
/* Smarty version 4.5.3, created on 2025-08-17 11:40:58
  from '/var/www/html/ISP/ui/ui/pppoe-add.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a1959a921909_77697443',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '734c928be14d17bc35e3b66d22b0cfde8480981b' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/pppoe-add.tpl',
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
function content_68a1959a921909_77697443 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-primary panel-hovered panel-stacked mb30">
            <div class="panel-heading"><?php echo Lang::T('Add Service Plan');?>
</div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
services/pppoe-add-post">
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Status');?>

                            <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                data-trigger="focus" data-container="body"
                                data-content="Customer cannot buy disabled Plan, but admin can recharge it, use it if you want only admin recharge it">?</a>
                        </label>
                        <div class="col-md-10">
                            <input type="radio" checked name="enabled" value="1"> <?php echo Lang::T('Enable');?>

                            <input type="radio" name="enabled" value="0"> <?php echo Lang::T('Disable');?>

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
                        <label class="col-md-2 control-label"><?php echo Lang::T('Plan Type');?>

                            <a tabindex="0" class="btn btn-link btn-xs" role="button" data-toggle="popover"
                                data-trigger="focus" data-container="body"
                                data-content="Personal Plan will only show to personal Customer, Business plan will only show to Business Customer">?</a>
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
                                <input type="checkbox" name="radius" onclick="isRadius(this)" value="1"> Radius Plan
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
                        <label class="col-md-2 control-label"><?php echo Lang::T('Plan Name');?>
</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="name_plan" maxlength="40" name="name_plan">
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
                        <label class="col-md-2 control-label"><?php echo Lang::T('Plan Price');?>
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
                        <label class="col-md-2 control-label"><?php echo Lang::T('Plan Validity');?>
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
                    <div class="form-group">
                        <label class="col-md-2 control-label"><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
pool/add"><?php echo Lang::T('IP Pool');?>
</a></label>
                        <div class="col-md-6">
                            <select id="pool_name" name="pool_name" required class="form-control select2">
                                <option value=''><?php echo Lang::T('Select Pool');?>
</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-10">
                            <button class="btn btn-primary" onclick="return ask(this, 'Continue the process of adding the PPPoE Package?')" type="submit"><?php echo Lang::T('Save Changes');?>
</button>
                            Or <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
services/pppoe"><?php echo Lang::T('Cancel');?>
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
                    document.getElementById("routers").required = false;
                    document.getElementById("routers").disabled = true;
                    $.ajax({
                        url: "?_route=autoload/pool",
                        data: "routers=radius",
                        cache: false,
                        success: function(msg) {
                            $("#pool_name").html(msg);
                        }
                    });
                } else {
                    document.getElementById("routers").required = true;
                    document.getElementById("routers").disabled = false;
                }
            }
        <?php echo '</script'; ?>
>
    
<?php }
$_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
