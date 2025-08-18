<?php
/* Smarty version 4.5.3, created on 2025-08-17 12:33:06
  from '/var/www/html/ISP/ui/ui/recharge-confirm.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a1a1d2a84546_19474321',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cae15376cc808969e40785f7d4ea3ab9b356c45d' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/recharge-confirm.tpl',
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
function content_68a1a1d2a84546_19474321 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-primary panel-hovered panel-stacked mb30">
            <div class="panel-heading"><?php echo Lang::T('Confirm');?>
</div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/recharge-post">
                    <center><b><?php echo Lang::T('Customer');?>
</b></center>
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b><?php echo Lang::T('Username');?>
</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['cust']->value['username'];?>
</span>
                        </li>
                        <li class="list-group-item">
                            <b><?php echo Lang::T('Name');?>
</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['cust']->value['fullname'];?>
</span>
                        </li>
                        <li class="list-group-item">
                            <b><?php echo Lang::T('Phone Number');?>
</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['cust']->value['phonenumber'];?>
</span>
                        </li>
                        <li class="list-group-item">
                            <b><?php echo Lang::T('Email');?>
</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['cust']->value['email'];?>
</span>
                        </li>
                        <li class="list-group-item">
                            <b><?php echo Lang::T('Address');?>
</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['cust']->value['address'];?>
</span>
                        </li>
                        <li class="list-group-item">
                            <b><?php echo Lang::T('Balance');?>
</b> <span
                                class="pull-right"><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['cust']->value['balance']);?>
</span>
                        </li>
                    </ul>
                    <center><b><?php echo Lang::T('Plan');?>
</b></center>
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b><?php echo Lang::T('Plan Name');?>
</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['plan']->value['name_plan'];?>
</span>
                        </li>
                        <li class="list-group-item">
                            <b><?php echo Lang::T('Location');?>
</b> <span
                                class="pull-right"><?php if ($_smarty_tpl->tpl_vars['plan']->value['is_radius']) {?>Radius<?php } else {
echo $_smarty_tpl->tpl_vars['plan']->value['routers'];
}?></span>
                        </li>
                        <li class="list-group-item">
                            <b><?php echo Lang::T('Type');?>
</b> <span
                                class="pull-right"><?php if ($_smarty_tpl->tpl_vars['plan']->value['prepaid'] == 'yes') {?>Prepaid<?php } else { ?>Postpaid<?php }?>
                                <?php echo $_smarty_tpl->tpl_vars['plan']->value['type'];?>
</span>
                        </li>
                        <tr>
                            <td><?php echo Lang::T('Bandwidth');?>
</td>
                            <td api-get-text="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
autoload/bw_name/<?php echo $_smarty_tpl->tpl_vars['plan']->value['id_bw'];?>
"></td>
                        </tr>
                        <li class="list-group-item">
                            <b><?php echo Lang::T('Plan Price');?>
</b> <span
                                class="pull-right"><?php if ($_smarty_tpl->tpl_vars['using']->value == 'zero') {
echo Lang::moneyFormat(0);
} else {
echo Lang::moneyFormat($_smarty_tpl->tpl_vars['plan']->value['price']);
}?></span>
                        </li>
                        <li class="list-group-item">
                            <b><?php echo Lang::T('Plan Validity');?>
</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['plan']->value['validity'];?>

                                <?php echo $_smarty_tpl->tpl_vars['plan']->value['validity_unit'];?>
</span>
                        </li>
                        <li class="list-group-item">
                            <b><?php echo Lang::T('Using');?>
</b> <span class="pull-right">
                                <select name="using"
                                    style="background-color: white;outline: 1px;border: 1px solid #b7b7b7;">
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['usings']->value, 'us');
$_smarty_tpl->tpl_vars['us']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['us']->value) {
$_smarty_tpl->tpl_vars['us']->do_else = false;
?>
                                        <option value="<?php echo trim($_smarty_tpl->tpl_vars['us']->value);?>
" <?php if ($_smarty_tpl->tpl_vars['using']->value == trim($_smarty_tpl->tpl_vars['us']->value)) {?>selected<?php }?>>
                                            <?php echo trim(ucWords($_smarty_tpl->tpl_vars['us']->value));?>
</option>
                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                    <?php if ($_smarty_tpl->tpl_vars['_c']->value['enable_balance'] == 'yes') {?>
                                        <option value="balance" <?php if ($_smarty_tpl->tpl_vars['using']->value == 'balance') {?>selected<?php }?>>
                                            <?php echo Lang::T('Customer Balance');?>
</option>
                                    <?php }?>
                                    <option value="zero" <?php if ($_smarty_tpl->tpl_vars['using']->value == 'zero') {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['_c']->value['currency_code'];?>
 0
                                    </option>
                                </select>
                            </span>
                        </li>
                    </ul>
                    <center><b><?php echo Lang::T('Total');?>
</b></center>
                    <ul class="list-group list-group-unbordered">
					<?php if ($_smarty_tpl->tpl_vars['tax']->value) {?>
						<li class="list-group-item">
                            <b><?php echo Lang::T('Tax');?>
</b> <span class="pull-right"><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['tax']->value);?>
</span>
                        </li>
                        <?php if ($_smarty_tpl->tpl_vars['using']->value != 'zero' && $_smarty_tpl->tpl_vars['add_cost']->value != 0) {?>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['abills']->value, 'v', false, 'k');
$_smarty_tpl->tpl_vars['v']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->do_else = false;
?>

                                <?php if (strpos($_smarty_tpl->tpl_vars['v']->value,':') === false) {?>
                                    <li class="list-group-item">
                                        <b><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
</b> <span class="pull-right">
                                            <?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['v']->value);?>

                                            <sup title="recurring">∞</sup>
                                            <?php $_smarty_tpl->_assignInScope('total', $_smarty_tpl->tpl_vars['v']->value+$_smarty_tpl->tpl_vars['total']->value);?>
                                        </span>
                                    </li>
                                <?php } else { ?>
                                    <?php $_smarty_tpl->_assignInScope('exp', explode(':',$_smarty_tpl->tpl_vars['v']->value));?>
                                    <?php if ($_smarty_tpl->tpl_vars['exp']->value[1] > 0) {?>
                                        <li class="list-group-item">
                                            <b><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
</b> <span class="pull-right">
                                            <sup title="<?php echo $_smarty_tpl->tpl_vars['exp']->value[1];?>
 more times">(<?php echo $_smarty_tpl->tpl_vars['exp']->value[1];?>
x)  </sup>
                                                <?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['exp']->value[0]);?>

                                            </span>
                                        </li>
                                    <?php }?>
                                <?php }?>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            <li class="list-group-item">
                                <b><?php echo Lang::T('Additional Cost');?>
</b> <span
                                    class="pull-right"><b><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['add_cost']->value);?>
</b></span>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo $_smarty_tpl->tpl_vars['plan']->value['name_plan'];?>
</b> <span
                                    class="pull-right"><?php if ($_smarty_tpl->tpl_vars['using']->value == 'zero') {
echo Lang::moneyFormat(0);
} else {
echo Lang::moneyFormat($_smarty_tpl->tpl_vars['plan']->value['price']);
}?></span>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo Lang::T('Total');?>
</b> <small>(<?php echo Lang::T('Plan Price');?>

                                    +<?php echo Lang::T('Additional Cost');?>
)</small><span class="pull-right"
                                    style="font-size: large; font-weight:bolder; font-family: 'Courier New', Courier, monospace; "><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['plan']->value['price']+$_smarty_tpl->tpl_vars['add_cost']->value+$_smarty_tpl->tpl_vars['tax']->value);?>
</span>
                            </li>
                        <?php } else { ?>
                            <li class="list-group-item">
                                <b><?php echo Lang::T('Total');?>
</b>  <small>(<?php echo Lang::T('Plan Price');?>
 + <?php echo Lang::T('Tax');?>
)</small><span class="pull-right"
                                    style="font-size: large; font-weight:bolder; font-family: 'Courier New', Courier, monospace; "><?php if ($_smarty_tpl->tpl_vars['using']->value == 'zero') {
echo Lang::moneyFormat(0);
} else {
echo Lang::moneyFormat($_smarty_tpl->tpl_vars['plan']->value['price']+$_smarty_tpl->tpl_vars['tax']->value);
}?></span>
                            </li>
                        <?php }?>
					<?php } else { ?>
						<?php if ($_smarty_tpl->tpl_vars['using']->value != 'zero' && $_smarty_tpl->tpl_vars['add_cost']->value != 0) {?>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['abills']->value, 'v', false, 'k');
$_smarty_tpl->tpl_vars['v']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->do_else = false;
?>

                                <?php if (strpos($_smarty_tpl->tpl_vars['v']->value,':') === false) {?>
                                    <li class="list-group-item">
                                        <b><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
</b> <span class="pull-right">
                                            <?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['v']->value);?>

                                            <sup title="recurring">∞</sup>
                                            <?php $_smarty_tpl->_assignInScope('total', $_smarty_tpl->tpl_vars['v']->value+$_smarty_tpl->tpl_vars['total']->value);?>
                                        </span>
                                    </li>
                                <?php } else { ?>
                                    <?php $_smarty_tpl->_assignInScope('exp', explode(':',$_smarty_tpl->tpl_vars['v']->value));?>
                                    <?php if ($_smarty_tpl->tpl_vars['exp']->value[1] > 0) {?>
                                        <li class="list-group-item">
                                            <b><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
</b> <span class="pull-right">
                                            <sup title="<?php echo $_smarty_tpl->tpl_vars['exp']->value[1];?>
 more times">(<?php echo $_smarty_tpl->tpl_vars['exp']->value[1];?>
x)  </sup>
                                                <?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['exp']->value[0]);?>

                                            </span>
                                        </li>
                                    <?php }?>
                                <?php }?>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            <li class="list-group-item">
                                <b><?php echo Lang::T('Additional Cost');?>
</b> <span
                                    class="pull-right"><b><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['add_cost']->value);?>
</b></span>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo $_smarty_tpl->tpl_vars['plan']->value['name_plan'];?>
</b> <span
                                    class="pull-right"><?php if ($_smarty_tpl->tpl_vars['using']->value == 'zero') {
echo Lang::moneyFormat(0);
} else {
echo Lang::moneyFormat($_smarty_tpl->tpl_vars['plan']->value['price']);
}?></span>
                            </li>
                            <li class="list-group-item">
                                <b><?php echo Lang::T('Total');?>
</b> <small>(<?php echo Lang::T('Plan Price');?>

                                    +<?php echo Lang::T('Additional Cost');?>
)</small><span class="pull-right"
                                    style="font-size: large; font-weight:bolder; font-family: 'Courier New', Courier, monospace; "><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['plan']->value['price']+$_smarty_tpl->tpl_vars['add_cost']->value);?>
</span>
                            </li>
                        <?php } else { ?>
                            <li class="list-group-item">
                                <b><?php echo Lang::T('Total');?>
</b> <span class="pull-right"
                                    style="font-size: large; font-weight:bolder; font-family: 'Courier New', Courier, monospace; "><?php if ($_smarty_tpl->tpl_vars['using']->value == 'zero') {
echo Lang::moneyFormat(0);
} else {
echo Lang::moneyFormat($_smarty_tpl->tpl_vars['plan']->value['price']);
}?></span>
                            </li>
                        <?php }?>
					<?php }?>
                    </ul>
                    <input type="hidden" name="id_customer" value="<?php echo $_smarty_tpl->tpl_vars['cust']->value['id'];?>
">
                    <input type="hidden" name="plan" value="<?php echo $_smarty_tpl->tpl_vars['plan']->value['id'];?>
">
                    <input type="hidden" name="server" value="<?php echo $_smarty_tpl->tpl_vars['server']->value;?>
">
                    <input type="hidden" name="stoken" value="<?php echo App::getToken();?>
">
                    <center>
                        <button class="btn btn-success" type="submit"><?php echo Lang::T('Recharge');?>
</button><br>
                        <a class="btn btn-link" href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/recharge"><?php echo Lang::T('Cancel');?>
</a>
                    </center>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
