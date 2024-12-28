<?php
/* Smarty version 4.5.3, created on 2024-12-23 06:46:27
  from '/var/www/html/snootylique/system/plugin/ui/overdue_alert.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_6768dd13652c26_88745890',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c88975dba5bfbe806e36b2ddb6e01693d845b8af' => 
    array (
      0 => '/var/www/html/snootylique/system/plugin/ui/overdue_alert.tpl',
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
function content_6768dd13652c26_88745890 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title">Overdue Customers</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="overdue_table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Username</th>
                                <th>Plan</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Expiration Date</th>
                                <th>Days Left</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['d']->value, 'ds');
$_smarty_tpl->tpl_vars['ds']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ds']->value) {
$_smarty_tpl->tpl_vars['ds']->do_else = false;
?>
                                <tr>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['fullname'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['username'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['plan_name'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['phonenumber'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['email'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['expiration'];?>
</td>
                                    <td><?php echo ceil((strtotime($_smarty_tpl->tpl_vars['ds']->value['expiration'])-time())/86400);?>
</td>
                                    <td>
                                        <a href="index.php?_route=customers/view/<?php echo $_smarty_tpl->tpl_vars['ds']->value['id'];?>
" class="btn btn-info btn-xs">View</a>
                                    </td>
                                </tr>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
