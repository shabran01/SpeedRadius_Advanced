<?php
/* Smarty version 4.5.3, created on 2025-08-17 10:49:36
  from '/var/www/html/ISP/ui/ui/recharge.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a18990e1b3b1_03065369',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd57568a345207250470c322f2a5049e18763d512' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/recharge.tpl',
      1 => 1755416924,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:sections/header.tpl' => 1,
    'file:sections/footer.tpl' => 1,
  ),
),false)) {
function content_68a18990e1b3b1_03065369 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-primary panel-hovered panel-stacked mb30">
            <div class="panel-heading"><?php echo Lang::T('Recharge Account');?>
</div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/recharge-confirm">
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Select Account');?>
</label>
                        <div class="col-md-6">
                            <select <?php if ($_smarty_tpl->tpl_vars['cust']->value) {
} else { ?>id="personSelect"<?php }?> class="form-control select2"
                                name="id_customer" style="width: 100%" data-placeholder="<?php echo Lang::T('Select a customer');?>
...">
                                <?php if ($_smarty_tpl->tpl_vars['cust']->value) {?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['cust']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['cust']->value['username'];?>
 &bull; <?php echo $_smarty_tpl->tpl_vars['cust']->value['fullname'];?>
 &bull; <?php echo $_smarty_tpl->tpl_vars['cust']->value['email'];?>
</option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Type');?>
</label>
                        <div class="col-md-6">
                            <label><input type="radio" id="Hot" name="type" value="Hotspot"> <?php echo Lang::T('Hotspot Plans');?>
</label>
                            <label><input type="radio" id="POE" name="type" value="PPPOE"> <?php echo Lang::T('PPPOE Plans');?>
</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Routers');?>
</label>
                        <div class="col-md-6">
                            <select id="server" data-type="server" name="server" class="form-control select2">
                                <option value=''><?php echo Lang::T('Select Routers');?>
</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Service Plan');?>
</label>
                        <div class="col-md-6">
                            <select id="plan" name="plan" class="form-control select2">
                                <option value=''><?php echo Lang::T('Select Plans');?>
</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Using');?>
</label>
                        <div class="col-md-6">
                            <select name="using" class="form-control">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['usings']->value, 'using');
$_smarty_tpl->tpl_vars['using']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['using']->value) {
$_smarty_tpl->tpl_vars['using']->do_else = false;
?>
                                    <option value="<?php echo trim($_smarty_tpl->tpl_vars['using']->value);?>
"><?php echo trim(ucWords($_smarty_tpl->tpl_vars['using']->value));?>
</option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                <?php if ($_smarty_tpl->tpl_vars['_c']->value['enable_balance'] == 'yes') {?>
                                    <option value="balance"><?php echo Lang::T('Customer Balance');?>
</option>
                                <?php }?>
                                <option value="zero"><?php echo $_smarty_tpl->tpl_vars['_c']->value['currency_code'];?>
 0</option>
                            </select>
                        </div>
                        <p class="help-block col-md-4"><?php echo Lang::T('Postpaid Recharge for the first time use');?>
 <?php echo $_smarty_tpl->tpl_vars['_c']->value['currency_code'];?>
 0</p>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-success" onclick="return ask(this, 'Continue the Recharge process?')"
                                type="submit"><?php echo Lang::T('Recharge');?>
</button>
                            <?php echo Lang::T('Or');?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/list"><?php echo Lang::T('Cancel');?>
</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
