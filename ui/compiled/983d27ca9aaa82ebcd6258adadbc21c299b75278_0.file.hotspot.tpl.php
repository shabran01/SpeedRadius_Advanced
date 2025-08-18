<?php
/* Smarty version 4.5.3, created on 2025-08-17 12:20:21
  from '/var/www/html/ISP/ui/ui/hotspot.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a19ed53195a6_96433971',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '983d27ca9aaa82ebcd6258adadbc21c299b75278' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/hotspot.tpl',
      1 => 1754916133,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:sections/header.tpl' => 1,
    'file:pagination.tpl' => 1,
    'file:sections/footer.tpl' => 1,
  ),
),false)) {
function content_68a19ed53195a6_96433971 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a class="btn btn-primary btn-xs" title="save" href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
services/sync/hotspot"
                        onclick="return ask(this, 'This will sync/send hotspot package to Mikrotik?')"><span
                            class="glyphicon glyphicon-refresh" aria-hidden="true"></span> sync</a>
                </div><?php echo Lang::T('Hotspot Plans');?>

            </div>
            <form id="site-search" method="post" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
services/hotspot/">
                <div class="panel-body">
                    <div class="row row-no-gutters" style="padding: 5px">
                        <div class="col-lg-2">
                            <div class="input-group">
                                <div class="input-group-btn">
                                    <a class="btn btn-danger" title="Clear Search Query"
                                        href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
services/hotspot/"><span
                                            class="glyphicon glyphicon-remove-circle"></span></a>
                                </div>
                                <input type="text" name="name" class="form-control"
                                    placeholder="<?php echo Lang::T('Search by Name');?>
...">
                            </div>
                        </div>
                        <div class="col-lg-1 col-xs-4">
                            <select class="form-control" id="type1" name="type1">
                                <option value=""><?php echo Lang::T('Prepaid');?>
 &amp; <?php echo Lang::T('Postpaid');?>
</option>
                                <option value="yes" <?php if ($_smarty_tpl->tpl_vars['type1']->value == 'yes') {?>selected<?php }?>><?php echo Lang::T('Prepaid');?>
</option>
                                <option value="no" <?php if ($_smarty_tpl->tpl_vars['type1']->value == 'no') {?>selected<?php }?>><?php echo Lang::T('Postpaid');?>
</option>
                            </select>
                        </div>
                        <div class="col-lg-1 col-xs-4">
                            <select class="form-control" id="type2" name="type2">
                                <option value=""><?php echo Lang::T('Type');?>
</option>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['type2s']->value, 't');
$_smarty_tpl->tpl_vars['t']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['t']->value) {
$_smarty_tpl->tpl_vars['t']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['t']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['type2']->value == $_smarty_tpl->tpl_vars['t']->value) {?>selected<?php }?>><?php echo Lang::T($_smarty_tpl->tpl_vars['t']->value);?>

                                    </option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </select>
                        </div>
                        <div class="col-lg-1 col-xs-4">
                            <select class="form-control" id="bandwidth" name="bandwidth">
                                <option value="">Bandwidth</option>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['bws']->value, 'b');
$_smarty_tpl->tpl_vars['b']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['b']->value) {
$_smarty_tpl->tpl_vars['b']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['b']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['bandwidth']->value == $_smarty_tpl->tpl_vars['b']->value['id']) {?>selected<?php }?>>
                                        <?php echo $_smarty_tpl->tpl_vars['b']->value['name_bw'];?>

                                    </option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </select>
                        </div>
                        <div class="col-lg-1 col-xs-4">
                            <select class="form-control" id="type3" name="type3">
                                <option value=""><?php echo Lang::T('Category');?>
</option>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['type3s']->value, 't');
$_smarty_tpl->tpl_vars['t']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['t']->value) {
$_smarty_tpl->tpl_vars['t']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['t']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['type3']->value == $_smarty_tpl->tpl_vars['t']->value) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['t']->value;?>

                                    </option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </select>
                        </div>
                        <div class="col-lg-1 col-xs-4">
                            <select class="form-control" id="valid" name="valid">
                                <option value=""><?php echo Lang::T('Validity');?>
</option>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['valids']->value, 'v');
$_smarty_tpl->tpl_vars['v']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['v']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['valid']->value == $_smarty_tpl->tpl_vars['v']->value) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['v']->value;?>

                                    </option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </select>
                        </div>
                        <div class="col-lg-1 col-xs-4">
                            <select class="form-control" id="router" name="router">
                                <option value=""><?php echo Lang::T('Location');?>
</option>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['routers']->value, 'r');
$_smarty_tpl->tpl_vars['r']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['r']->value) {
$_smarty_tpl->tpl_vars['r']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['r']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['router']->value == $_smarty_tpl->tpl_vars['r']->value) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['r']->value;?>
</option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                <option value="radius" <?php if ($_smarty_tpl->tpl_vars['router']->value == 'radius') {?>selected<?php }?>>Radius</option>
                            </select>
                        </div>
                        <div class="col-lg-1 col-xs-4">
                            <select class="form-control" id="device" name="device">
                                <option value=""><?php echo Lang::T('Device');?>
</option>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['devices']->value, 'r');
$_smarty_tpl->tpl_vars['r']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['r']->value) {
$_smarty_tpl->tpl_vars['r']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['r']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['device']->value == $_smarty_tpl->tpl_vars['r']->value) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['r']->value;?>
</option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </select>
                        </div>
                        <div class="col-lg-1 col-xs-4">
                            <select class="form-control" id="status" name="status">
                                <option value="-"><?php echo Lang::T('Status');?>
</option>
                                <option value="1" <?php if ($_smarty_tpl->tpl_vars['status']->value == '1') {?>selected<?php }?>><?php echo Lang::T('Active');?>
</option>
                                <option value="0" <?php if ($_smarty_tpl->tpl_vars['status']->value == '0') {?>selected<?php }?>><?php echo Lang::T('Not Active');?>
</option>
                            </select>
                        </div>
                        <div class="col-lg-1 col-xs-8">
                            <button class="btn btn-success btn-block" type="submit"><span
                                    class="fa fa-search"></span></button>
                        </div>
                        <div class="col-lg-1 col-xs-4">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
services/add" class="btn btn-primary btn-block"
                                title="<?php echo Lang::T('New Service Package');?>
"><i class="ion ion-android-add"></i></a>
                        </div>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <div style="margin-left: 5px; margin-right: 5px;">
                    <table class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <th></th>
                                <th colspan="5" class="text-center"><?php echo Lang::T('Internet Package');?>
</th>
                                <th colspan="2" class="text-center" style="background-color: rgb(246, 244, 244);">
                                    <?php echo Lang::T('Limit');?>
</th>
                                <th colspan="2"></th>
                                <th colspan="2" class="text-center" style="background-color: rgb(243, 241, 172);">
                                    <?php echo Lang::T('Expired');?>
</th>
                                <th colspan="3"></th>
                            </tr>
                            <tr>
                                <th><?php echo Lang::T('Name');?>
</th>
                                <th><?php echo Lang::T('Type');?>
</th>
                                <th><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
bandwidth/list">Bandwidth</a></th>
                                <th><?php echo Lang::T('Category');?>
</th>
                                <th><?php echo Lang::T('Price');?>
</th>
                                <th><?php echo Lang::T('Validity');?>
</th>
                                <th style="background-color: rgb(246, 244, 244);"><?php echo Lang::T('Time');?>
</th>
                                <th style="background-color: rgb(246, 244, 244);"><?php echo Lang::T('Data');?>
</th>
                                <th><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
routers/list"><?php echo Lang::T('Location');?>
</a></th>
                                <th><?php echo Lang::T('Device');?>
</th>
                                <th style="background-color: rgb(243, 241, 172);"><?php echo Lang::T('Internet Package');?>
</th>
                                <th style="background-color: rgb(243, 241, 172);"><?php echo Lang::T('Date');?>
</th>
                                <th><?php echo Lang::T('ID');?>
</th>
                                <th><?php echo Lang::T('Manage');?>
</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['d']->value, 'ds');
$_smarty_tpl->tpl_vars['ds']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ds']->value) {
$_smarty_tpl->tpl_vars['ds']->do_else = false;
?>
                                <tr <?php if ($_smarty_tpl->tpl_vars['ds']->value['enabled'] != 1) {?>class="danger" title="disabled" <?php } elseif ($_smarty_tpl->tpl_vars['ds']->value['prepaid'] != 'yes') {?>class="warning" title="Postpaid" <?php }?>>
                                    <td class="headcol"><?php echo $_smarty_tpl->tpl_vars['ds']->value['name_plan'];?>
</td>
                                    <td><?php if ($_smarty_tpl->tpl_vars['ds']->value['prepaid'] == 'no') {?><b>Postpaid</b><?php } else { ?>Prepaid<?php }?> <?php echo $_smarty_tpl->tpl_vars['ds']->value['plan_type'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['name_bw'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['typebp'];?>
</td>
                                    <td><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['ds']->value['price']);?>

                                        <?php if (!empty($_smarty_tpl->tpl_vars['ds']->value['price_old'])) {?>
                                            <sup style="text-decoration: line-through; color: red"><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['ds']->value['price_old']);?>
</sup>
                                                <?php }?>
                                    </td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['validity'];?>
 <?php echo $_smarty_tpl->tpl_vars['ds']->value['validity_unit'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['time_limit'];?>
 <?php echo $_smarty_tpl->tpl_vars['ds']->value['time_unit'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['data_limit'];?>
 <?php echo $_smarty_tpl->tpl_vars['ds']->value['data_unit'];?>
</td>
                                    <td>
                                        <?php if ($_smarty_tpl->tpl_vars['ds']->value['is_radius']) {?>
                                            <span class="label label-primary">RADIUS</span>
                                        <?php } else { ?>
                                            <?php if ($_smarty_tpl->tpl_vars['ds']->value['routers'] != '') {?>
                                                <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
routers/edit/0&name=<?php echo $_smarty_tpl->tpl_vars['ds']->value['routers'];?>
"><?php echo $_smarty_tpl->tpl_vars['ds']->value['routers'];?>
</a>
                                            <?php }?>
                                        <?php }?>
                                    </td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['device'];?>
</td>
                                    <td><?php if ($_smarty_tpl->tpl_vars['ds']->value['plan_expired']) {?><a
                                            href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
services/edit/<?php echo $_smarty_tpl->tpl_vars['ds']->value['plan_expired'];?>
"><?php echo Lang::T('Yes');?>
</a><?php } else {
echo Lang::T('No');?>

                                        <?php }?></td>
                                    <td><?php if ($_smarty_tpl->tpl_vars['ds']->value['prepaid'] == 'no') {
echo $_smarty_tpl->tpl_vars['ds']->value['expired_date'];
}?></td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['id'];?>
</td>
                                    <td>
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
services/edit/<?php echo $_smarty_tpl->tpl_vars['ds']->value['id'];?>
"
                                            class="btn btn-info btn-xs"><?php echo Lang::T('Edit');?>
</a>
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
services/delete/<?php echo $_smarty_tpl->tpl_vars['ds']->value['id'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['ds']->value['id'];?>
"
                                            onclick="return ask(this, '<?php echo Lang::T('Delete');?>
?')"
                                            class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></a>
                                    </td>
                                </tr>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel-footer">
                <?php $_smarty_tpl->_subTemplateRender("file:pagination.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                <div class="bs-callout bs-callout-info" id="callout-navbar-role">
                    <h4><?php echo Lang::T('Create expired Internet Package');?>
</h4>
                    <p><?php echo Lang::T('When customer expired, you can move it to Expired Internet Package');?>
</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
