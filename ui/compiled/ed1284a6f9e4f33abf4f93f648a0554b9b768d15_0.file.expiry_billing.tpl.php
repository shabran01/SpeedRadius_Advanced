<?php
/* Smarty version 4.5.3, created on 2024-12-23 06:47:16
  from '/var/www/html/snootylique/system/plugin/ui/expiry_billing.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_6768dd445a5e89_28563755',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ed1284a6f9e4f33abf4f93f648a0554b9b768d15' => 
    array (
      0 => '/var/www/html/snootylique/system/plugin/ui/expiry_billing.tpl',
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
function content_6768dd445a5e89_28563755 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/html/snootylique/system/vendor/smarty/smarty/libs/plugins/modifier.date_format.php','function'=>'smarty_modifier_date_format',),));
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                Expiry Billing Details
            </div>
            <div class="panel-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="expiry_date">Expiry Date</label>
                        <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['current_expiry_date']->value,'%Y-%m-%d');?>
" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Expiry Date</button>
                </form>

                <?php if ($_smarty_tpl->tpl_vars['message']->value) {?>
                    <div class="alert alert-success mt-2"><?php echo $_smarty_tpl->tpl_vars['message']->value;?>
</div>
                <?php }?>

                <div class="mt-4">
                    <h4>Time Left Until Expiry:</h4>
                    <?php if ($_smarty_tpl->tpl_vars['time_left']->value > 0) {?>
                        <p>
                            <?php $_smarty_tpl->_assignInScope('days', $_smarty_tpl->tpl_vars['time_left']->value/(60*60*24));?>
                            <?php $_smarty_tpl->_assignInScope('hours', ($_smarty_tpl->tpl_vars['time_left']->value%(60*60*24))/(60*60));?>
                            <?php $_smarty_tpl->_assignInScope('minutes', ($_smarty_tpl->tpl_vars['time_left']->value%(60*60))/60);?>
                            <?php $_smarty_tpl->_assignInScope('seconds', $_smarty_tpl->tpl_vars['time_left']->value%60);?>
                            <?php echo round((float) $_smarty_tpl->tpl_vars['days']->value, (int) 0, (int) 1);?>
 Days, <?php echo round((float) $_smarty_tpl->tpl_vars['hours']->value, (int) 0, (int) 1);?>
 Hours, <?php echo round((float) $_smarty_tpl->tpl_vars['minutes']->value, (int) 0, (int) 1);?>
 Minutes, <?php echo round((float) $_smarty_tpl->tpl_vars['seconds']->value, (int) 0, (int) 1);?>
 Seconds
                        </p>
                    <?php } else { ?>
                        <p class="text-danger">Your billing has expired.</p>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
