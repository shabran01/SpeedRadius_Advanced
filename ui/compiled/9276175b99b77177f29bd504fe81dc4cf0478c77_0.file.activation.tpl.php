<?php
/* Smarty version 4.5.3, created on 2024-12-23 06:53:50
  from '/var/www/html/snootylique/ui/ui/customer/activation.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_6768deceea3b31_84473970',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9276175b99b77177f29bd504fe81dc4cf0478c77' => 
    array (
      0 => '/var/www/html/snootylique/ui/ui/customer/activation.tpl',
      1 => 1734662748,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:customer/header.tpl' => 1,
    'file:customer/footer.tpl' => 1,
  ),
),false)) {
function content_6768deceea3b31_84473970 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:customer/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<!-- user-activation -->

<div class="row">
    <div class="col-md-8">
        <div class="box box-primary box-solid">
            <div class="box-header">
                <h3 class="box-title"><?php echo Lang::T('Order Voucher');?>
</h3>
            </div>
            <div class="box-body">
                <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['PAGES_PATH']->value)."/Order_Voucher.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box box-primary box-solid">
            <div class="box-header"><?php echo Lang::T('Voucher Activation');?>
</div>
            <div class="box-body">
                <form method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
voucher/activation-post">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" id="code" name="code" value="<?php echo $_smarty_tpl->tpl_vars['code']->value;?>
"
                                placeholder="<?php echo Lang::T('Enter voucher code here');?>
">
                            <span class="input-group-btn">
                                <a class="btn btn-default" href="<?php echo APP_URL;?>
/scan/?back=<?php echo urlencode($_smarty_tpl->tpl_vars['_url']->value);
echo urlencode("voucher/activation&code=");?>
"><i class="glyphicon glyphicon-qrcode"></i></a>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-success" type="submit"><?php echo Lang::T('Recharge');?>
</button>
                            Or <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
home"><?php echo Lang::T('Cancel');?>
</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:customer/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
