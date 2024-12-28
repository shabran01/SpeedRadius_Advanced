<?php
/* Smarty version 4.5.3, created on 2024-12-24 12:42:05
  from '/var/www/html/snootylique/system/plugin/ui/mpesa_transactions.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_676a81edd46a39_18881524',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '54af56858dd50bd3ba4ac0ae28c51f6f62fa478e' => 
    array (
      0 => '/var/www/html/snootylique/system/plugin/ui/mpesa_transactions.tpl',
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
function content_676a81edd46a39_18881524 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/html/snootylique/system/vendor/smarty/smarty/libs/plugins/modifier.truncate.php','function'=>'smarty_modifier_truncate',),));
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
               Mpesa Transactions
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>First Name</th>
                            <th>Phone</th>
                            <th>Amount</th>
                            <th>Account No</th>
                            <th>Org Account Balance</th>
                            <th>Transaction ID</th>
                            <th>Transaction Type</th>
                            <th>Transaction Time</th>
                            <th>Business Short Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['t']->value, 'ts', false, 'key');
$_smarty_tpl->tpl_vars['ts']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['ts']->value) {
$_smarty_tpl->tpl_vars['ts']->do_else = false;
?>

                        <tr>
                            <td><?php echo $_smarty_tpl->tpl_vars['key']->value+1;?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['ts']->value['FirstName'];?>
</td>
                            <td><?php if ($_smarty_tpl->tpl_vars['ts']->value['MSISDN']) {
echo smarty_modifier_truncate($_smarty_tpl->tpl_vars['ts']->value['MSISDN'],20,"...");
} else { ?>No MSISDN available<?php }?></td>
                            <td><?php echo $_smarty_tpl->tpl_vars['ts']->value['TransAmount'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['ts']->value['BillRefNumber'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['ts']->value['OrgAccountBalance'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['ts']->value['TransID'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['ts']->value['TransactionType'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['ts']->value['TransTime'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['ts']->value['BusinessShortCode'];?>
</td>
                        </tr>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <ul class="pagination">
                            <?php if ($_smarty_tpl->tpl_vars['current_page']->value > 1) {?>
                                <li><a href="?_route=plugin/mpesa_transactions&page=<?php echo $_smarty_tpl->tpl_vars['current_page']->value-1;?>
">&laquo; Previous</a></li>
                            <?php }?>
                            
                            <?php
$_smarty_tpl->tpl_vars['p'] = new Smarty_Variable(null, $_smarty_tpl->isRenderingCache);$_smarty_tpl->tpl_vars['p']->step = 1;$_smarty_tpl->tpl_vars['p']->total = (int) ceil(($_smarty_tpl->tpl_vars['p']->step > 0 ? $_smarty_tpl->tpl_vars['total_pages']->value+1 - (1) : 1-($_smarty_tpl->tpl_vars['total_pages']->value)+1)/abs($_smarty_tpl->tpl_vars['p']->step));
if ($_smarty_tpl->tpl_vars['p']->total > 0) {
for ($_smarty_tpl->tpl_vars['p']->value = 1, $_smarty_tpl->tpl_vars['p']->iteration = 1;$_smarty_tpl->tpl_vars['p']->iteration <= $_smarty_tpl->tpl_vars['p']->total;$_smarty_tpl->tpl_vars['p']->value += $_smarty_tpl->tpl_vars['p']->step, $_smarty_tpl->tpl_vars['p']->iteration++) {
$_smarty_tpl->tpl_vars['p']->first = $_smarty_tpl->tpl_vars['p']->iteration === 1;$_smarty_tpl->tpl_vars['p']->last = $_smarty_tpl->tpl_vars['p']->iteration === $_smarty_tpl->tpl_vars['p']->total;?>
                                <li <?php if ($_smarty_tpl->tpl_vars['p']->value == $_smarty_tpl->tpl_vars['current_page']->value) {?>class="active"<?php }?>>
                                    <a href="?_route=plugin/mpesa_transactions&page=<?php echo $_smarty_tpl->tpl_vars['p']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['p']->value;?>
</a>
                                </li>
                            <?php }
}
?>
                            
                            <?php if ($_smarty_tpl->tpl_vars['current_page']->value < $_smarty_tpl->tpl_vars['total_pages']->value) {?>
                                <li><a href="?_route=plugin/mpesa_transactions&page=<?php echo $_smarty_tpl->tpl_vars['current_page']->value+1;?>
">Next &raquo;</a></li>
                            <?php }?>
                        </ul>
                        <p class="text-muted">Showing <?php echo ($_smarty_tpl->tpl_vars['current_page']->value-1)*10+1;?>
 to <?php echo min($_smarty_tpl->tpl_vars['current_page']->value*10,$_smarty_tpl->tpl_vars['total_items']->value);?>
 of <?php echo $_smarty_tpl->tpl_vars['total_items']->value;?>
 entries</p>
                    </div>
                </div>
                <div class="bs-callout bs-callout-info" id="callout-navbar-role">
                    <h4>All Mpesa Transaction </h4>
                    <p>Transaction </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
