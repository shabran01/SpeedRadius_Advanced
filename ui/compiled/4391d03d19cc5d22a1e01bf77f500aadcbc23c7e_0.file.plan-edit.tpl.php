<?php
/* Smarty version 4.5.3, created on 2025-08-18 16:19:50
  from '/var/www/html/ISP/ui/ui/plan-edit.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a32876d0fb84_95509149',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4391d03d19cc5d22a1e01bf77f500aadcbc23c7e' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/plan-edit.tpl',
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
function content_68a32876d0fb84_95509149 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Edit Plan</h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/edit-post">
                    <input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
">
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Select Account');?>
</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?php echo $_smarty_tpl->tpl_vars['d']->value['username'];?>
" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Service Plan');?>
</label>
                        <div class="col-md-6">

                            <select id="id_plan" name="id_plan" class="form-control select2">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['p']->value, 'ps');
$_smarty_tpl->tpl_vars['ps']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ps']->value) {
$_smarty_tpl->tpl_vars['ps']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['ps']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['d']->value['plan_id'] == $_smarty_tpl->tpl_vars['ps']->value['id']) {?> selected <?php }?>>
                                        <?php if ($_smarty_tpl->tpl_vars['ps']->value['enabled'] != 1) {?>DISABLED PLAN &bull; <?php }?>
                                        <?php echo $_smarty_tpl->tpl_vars['ps']->value['name_plan'];?>
 &bull;
                                        <?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['ps']->value['price']);?>

                                        <?php if ($_smarty_tpl->tpl_vars['ps']->value['prepaid'] != 'yes') {?> &bull; POSTPAID <?php }?>
                                    </option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Created On');?>
</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" readonly
                                value="<?php echo $_smarty_tpl->tpl_vars['d']->value['recharged_on'];?>
">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" placeholder="00:00:00" readonly
                                value="<?php echo $_smarty_tpl->tpl_vars['d']->value['recharged_time'];?>
">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Expires On');?>
</label>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="expiration" name="expiration"
                                value="<?php echo $_smarty_tpl->tpl_vars['d']->value['expiration'];?>
">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="time" name="time" placeholder="00:00:00"
                                value="<?php echo $_smarty_tpl->tpl_vars['d']->value['time'];?>
">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-success" onclick="return ask(this, 'Continue the package change process?')" type="submit"><?php echo Lang::T('Edit');?>
</button>
                            Or <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/list"><?php echo Lang::T('Cancel');?>
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
