<?php
/* Smarty version 4.5.3, created on 2025-08-17 10:13:04
  from '/var/www/html/ISP/ui/ui/customers-view.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a1810061bad2_30314240',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd7f2dd8b1b6e088109fd308fc692dda87a61dbc2' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/customers-view.tpl',
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
function content_68a1810061bad2_30314240 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/html/ISP/system/vendor/smarty/smarty/libs/plugins/modifier.date_format.php','function'=>'smarty_modifier_date_format',),));
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<style>
.connected-devices {
    margin-top: 8px;
}
.btn-3d {
    position: relative;
    border: none;
    outline: none;
    cursor: pointer;
    border-radius: 5px;
    box-shadow: 0 3px 0 #0e76a8;
    transition: all 0.1s ease;
}

.btn-3d:active {
    transform: translateY(2px);
    box-shadow: 0 1px 0 #0e76a8;
}

.btn-3d-primary {
    background: linear-gradient(to bottom, #3498db, #2980b9);
    color: white;
}

.btn-3d-success {
    background: linear-gradient(to bottom, #2ecc71, #27ae60);
    color: white;
    box-shadow: 0 3px 0 #27ae60;
}

.btn-3d-success:active {
    box-shadow: 0 1px 0 #27ae60;
}

.btn-3d-warning {
    background: linear-gradient(to bottom, #e67e22, #d35400);
    color: white;
    box-shadow: 0 3px 0 #d35400;
}

.btn-3d-warning:active {
    box-shadow: 0 1px 0 #d35400;
}

.btn-3d-danger {
    background: linear-gradient(to bottom, #e74c3c, #c0392b);
    color: white;
    box-shadow: 0 3px 0 #c0392b;
}

.btn-3d-danger:active {
    box-shadow: 0 1px 0 #c0392b;
}

.btn-3d-info {
    background: linear-gradient(to bottom, #3498db, #2980b9);
    color: white;
    box-shadow: 0 3px 0 #2980b9;
}

.btn-3d-info:active {
    box-shadow: 0 1px 0 #2980b9;
}

.btn-3d:hover {
    opacity: 0.9;
    color: white;
}

.btn-change-router {
    padding: 6px 8px;
    font-size: 12px;
    letter-spacing: -0.2px;
}
</style>

<div class="row">
    <div class="col-sm-4 col-md-4">
        <div class="box box-<?php if ($_smarty_tpl->tpl_vars['d']->value['status'] == 'Active') {?>primary<?php } else { ?>danger<?php }?>">
            <div class="box-body box-profile">
                <div class="box-tools pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-3d btn-3d-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-gear"></i> <?php echo Lang::T('Actions');?>
 <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/sync/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" onclick="return ask(this, 'This will sync Customer to Mikrotik?')"><i class="fa fa-refresh"></i> <?php echo Lang::T('Sync');?>
</a></li>
                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/reconnect/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" onclick="return ask(this, 'This will disconnect and reconnect the customer. Continue?')"><i class="fa fa-power-off"></i> <?php echo Lang::T('Reconnect');?>
</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/enable/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" onclick="return ask(this, 'This will enable the customer on Mikrotik router. Continue?')"><i class="fa fa-play"></i> <?php echo Lang::T('Enable Customer');?>
</a></li>
                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/disable/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" onclick="return ask(this, 'This will disable the customer on Mikrotik router and disconnect them. Continue?')"><i class="fa fa-stop"></i> <?php echo Lang::T('Disable Customer');?>
</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
message/send/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
"><i class="fa fa-envelope"></i> <?php echo Lang::T('Send Message');?>
</a></li>
                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/login/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" target="_blank"><i class="fa fa-sign-in"></i> <?php echo Lang::T('Login as Customer');?>
</a></li>
                        </ul>
                    </div>
                </div>
                <img class="profile-user-img img-responsive img-circle"
                    onclick="window.location.href = '<?php echo $_smarty_tpl->tpl_vars['UPLOAD_PATH']->value;
echo $_smarty_tpl->tpl_vars['d']->value['photo'];?>
'"
                    src="<?php echo $_smarty_tpl->tpl_vars['UPLOAD_PATH']->value;
echo $_smarty_tpl->tpl_vars['d']->value['photo'];?>
.thumb.jpg"
                    onerror="this.src='<?php echo $_smarty_tpl->tpl_vars['UPLOAD_PATH']->value;?>
/user.default.jpg'" alt="avatar">
                <h3 class="profile-username text-center"><?php echo $_smarty_tpl->tpl_vars['d']->value['fullname'];?>
</h3>
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b><?php echo Lang::T('Status');?>
</b> <span
                            class="pull-right <?php if ($_smarty_tpl->tpl_vars['d']->value['status'] != 'Active') {?>bg-red<?php }?>">&nbsp;<?php echo Lang::T($_smarty_tpl->tpl_vars['d']->value['status']);?>
&nbsp;</span>
                    </li>
                    <li class="list-group-item">
                        <b><?php echo Lang::T('Username');?>
</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['d']->value['username'];?>
</span>
                    </li>
                    <li class="list-group-item">
                        <b><?php echo Lang::T('Phone Number');?>
</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['d']->value['phonenumber'];?>
</span>
                    </li>
                    <li class="list-group-item">
                        <b><?php echo Lang::T('Email');?>
</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['d']->value['email'];?>
</span>
                    </li>
                    <li class="list-group-item"><?php echo Lang::nl2br($_smarty_tpl->tpl_vars['d']->value['address']);?>
</li>
                    <li class="list-group-item">
                        <b><?php echo Lang::T('City');?>
</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['d']->value['city'];?>
</span>
                    </li>
                    <li class="list-group-item">
                        <b><?php echo Lang::T('District');?>
</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['d']->value['district'];?>
</span>
                    </li>
                    <li class="list-group-item">
                        <b><?php echo Lang::T('State');?>
</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['d']->value['state'];?>
</span>
                    </li>
                    <li class="list-group-item">
                        <b><?php echo Lang::T('Zip');?>
</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['d']->value['zip'];?>
</span>
                    </li>
                    <?php if (in_array($_smarty_tpl->tpl_vars['_admin']->value['user_type'],array('SuperAdmin','Admin'))) {?>
                        <li class="list-group-item">
                            <b><?php echo Lang::T('Password');?>
</b> <input type="password" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['password'];?>
"
                                style=" border: 0px; text-align: right;" class="pull-right"
                                onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'"
                                onclick="this.select()">
                        </li>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['d']->value['pppoe_username'] != '') {?>
                        <li class="list-group-item">
                            <b>PPPOE <?php echo Lang::T('Username');?>
</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['d']->value['pppoe_username'];?>
</span>
                        </li>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['d']->value['pppoe_password'] != '' && in_array($_smarty_tpl->tpl_vars['_admin']->value['user_type'],array('SuperAdmin','Admin'))) {?>
                        <li class="list-group-item">
                            <b>PPPOE <?php echo Lang::T('Password');?>
</b> <input type="password" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['pppoe_password'];?>
"
                                style=" border: 0px; text-align: right;" class="pull-right"
                                onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'"
                                onclick="this.select()">
                        </li>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['d']->value['pppoe_ip'] != '') {?>
                        <li class="list-group-item">
                            <b>PPPOE Remote IP</b> <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['d']->value['pppoe_ip'];?>
</span>
                        </li>
                    <?php }?>
                    <!--Customers Attributes view start -->
                    <?php if ($_smarty_tpl->tpl_vars['customFields']->value) {?>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['customFields']->value, 'customField');
$_smarty_tpl->tpl_vars['customField']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['customField']->value) {
$_smarty_tpl->tpl_vars['customField']->do_else = false;
?>
                            <li class="list-group-item">
                                <b><?php echo $_smarty_tpl->tpl_vars['customField']->value['field_name'];?>
</b> <span class="pull-right">
                                    <?php if (strpos($_smarty_tpl->tpl_vars['customField']->value['field_value'],':0') === false) {?>
                                        <?php echo $_smarty_tpl->tpl_vars['customField']->value['field_value'];?>

                                    <?php } else { ?>
                                        <b><?php echo Lang::T('Paid');?>
</b>
                                    <?php }?>
                                </span>
                            </li>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    <?php }?>
                    <!--Customers Attributes view end -->
                    <li class="list-group-item">
                        <b><?php echo Lang::T('Service Type');?>
</b> <span class="pull-right"><?php echo Lang::T($_smarty_tpl->tpl_vars['d']->value['service_type']);?>
</span>
                    </li>
                    <li class="list-group-item">
                        <b><?php echo Lang::T('Account Type');?>
</b> <span class="pull-right"><?php echo Lang::T($_smarty_tpl->tpl_vars['d']->value['account_type']);?>
</span>
                    </li>
                    <li class="list-group-item">
                        <b><?php echo Lang::T('Balance');?>
</b> <span class="pull-right"><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['d']->value['balance']);?>
</span>
                    </li>
                    <li class="list-group-item">
                        <b><?php echo Lang::T('Auto Renewal');?>
</b> <span class="pull-right"><?php if ($_smarty_tpl->tpl_vars['d']->value['auto_renewal']) {?>yes<?php } else { ?>no
                            <?php }?></span>
                    </li>
                    <li class="list-group-item">
                        <b><?php echo Lang::T('Created On');?>
</b> <span
                            class="pull-right"><?php echo Lang::dateTimeFormat($_smarty_tpl->tpl_vars['d']->value['created_at']);?>
</span>
                    </li>
                    <li class="list-group-item">
                        <b><?php echo Lang::T('Last Login');?>
</b> <span
                            class="pull-right"><?php echo Lang::dateTimeFormat($_smarty_tpl->tpl_vars['d']->value['last_login']);?>
</span>
                    </li>
                    <?php if ($_smarty_tpl->tpl_vars['d']->value['coordinates']) {?>
                        <li class="list-group-item">
                            <b><?php echo Lang::T('Coordinates');?>
</b> <span class="pull-right">
                                <i class="glyphicon glyphicon-road"></i> <a style="color: black;"
                                    href="https://www.google.com/maps/dir//<?php echo $_smarty_tpl->tpl_vars['d']->value['coordinates'];?>
/"
                                    target="_blank"><?php echo Lang::T('Get Directions');?>
</a>
                            </span>
                        </li>
                    <?php }?>
                </ul>
                <div class="row">
                    <div class="col-xs-4">
                        <button type="button" onclick="confirmDelete('<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/delete/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
')" 
                            class="btn btn-3d btn-3d-danger btn-sm btn-block"><i class="fa fa-trash"></i> <?php echo Lang::T('Delete');?>
</button>
                    </div>
                    <div class="col-xs-4">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/edit/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
"
                            class="btn btn-3d btn-3d-primary btn-sm btn-block"><i class="fa fa-pencil"></i> <?php echo Lang::T('Edit');?>
</a>
                    </div>
                    <div class="col-xs-4">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/change_router/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" 
                            class="btn btn-3d btn-3d-info btn-sm btn-block btn-change-router">
                            <i class="fa fa-random"></i> <?php echo Lang::T('Change Router');?>

                        </a>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-xs-12">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/list" class="btn btn-3d btn-3d btn-default btn-sm btn-block"><i class="fa fa-arrow-left"></i> <?php echo Lang::T('Back');?>
</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-8 col-md-8">
        <div class="box box-success">
            <div class="box-body">
                <div class="row">
                    <?php if ($_smarty_tpl->tpl_vars['_c']->value['enable_balance'] == 'yes' && $_smarty_tpl->tpl_vars['_c']->value['extend_expired']) {?>
                    <div class="col-xs-4">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/recharge/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
" class="btn btn-3d btn-3d-success btn-sm btn-block">
                            <i class="fa fa-credit-card"></i> <?php echo Lang::T('Recharge Account');?>

                        </a>
                    </div>
                    <div class="col-xs-4">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/deposit/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
" class="btn btn-3d btn-3d-primary btn-sm btn-block">
                            <i class="fa fa-money"></i> <?php echo Lang::T('Add Balance');?>

                        </a>
                    </div>
                    <div class="col-xs-4">
                        <button onclick="extendCustomerPlan('<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
')" class="btn btn-3d btn-3d-warning btn-sm btn-block">
                            <i class="fa fa-clock-o"></i> <?php echo Lang::T('Extend');?>

                        </button>
                    </div>
                    <?php } elseif ($_smarty_tpl->tpl_vars['_c']->value['enable_balance'] == 'yes') {?>
                    <div class="col-xs-6">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/recharge/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
" class="btn btn-3d btn-3d-success btn-sm btn-block">
                            <i class="fa fa-credit-card"></i> <?php echo Lang::T('Recharge Account');?>

                        </a>
                    </div>
                    <div class="col-xs-6">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/deposit/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
" class="btn btn-3d btn-3d-primary btn-sm btn-block">
                            <i class="fa fa-money"></i> <?php echo Lang::T('Add Balance');?>

                        </a>
                    </div>
                    <?php } elseif ($_smarty_tpl->tpl_vars['_c']->value['extend_expired']) {?>
                    <div class="col-xs-6">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/recharge/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
" class="btn btn-3d btn-3d-success btn-sm btn-block">
                            <i class="fa fa-credit-card"></i> <?php echo Lang::T('Recharge Account');?>

                        </a>
                    </div>
                    <div class="col-xs-6">
                        <button onclick="extendCustomerPlan('<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
')" class="btn btn-3d btn-3d-warning btn-sm btn-block">
                            <i class="fa fa-clock-o"></i> <?php echo Lang::T('Extend');?>

                        </button>
                    </div>
                    <?php } else { ?>
                    <div class="col-xs-12">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/recharge/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
" class="btn btn-3d btn-3d-success btn-sm btn-block">
                            <i class="fa fa-credit-card"></i> <?php echo Lang::T('Recharge Account');?>

                        </a>
                    </div>
                    <?php }?>
                </div>
            </div>
        </div>
        <div class="box box-info">
            <ul class="nav nav-tabs">
                <li role="presentation" <?php if ($_smarty_tpl->tpl_vars['v']->value == 'order') {?>class="active" <?php }?>><a
                        href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/view/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
/order">30 <?php echo Lang::T('Order History');?>
</a></li>
                <li role="presentation" <?php if ($_smarty_tpl->tpl_vars['v']->value == 'activation') {?>class="active" <?php }?>><a
                        href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/view/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
/activation">30 <?php echo Lang::T('Activation History');?>
</a></li>
            </ul>
            <div class="table-responsive" style="background-color: white;">
                <table id="datatable" class="table table-bordered table-striped">
                    <?php if (Lang::arrayCount($_smarty_tpl->tpl_vars['activation']->value)) {?>
                        <thead>
                            <tr>
                                <th><?php echo Lang::T('Invoice');?>
</th>
                                <th><?php echo Lang::T('Username');?>
</th>
                                <th><?php echo Lang::T('Plan Name');?>
</th>
                                <th><?php echo Lang::T('Plan Price');?>
</th>
                                <th><?php echo Lang::T('Type');?>
</th>
                                <th><?php echo Lang::T('Created On');?>
</th>
                                <th><?php echo Lang::T('Expires On');?>
</th>
                                <th><?php echo Lang::T('Method');?>
</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['activation']->value, 'ds');
$_smarty_tpl->tpl_vars['ds']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ds']->value) {
$_smarty_tpl->tpl_vars['ds']->do_else = false;
?>
                                <tr onclick="window.location.href = '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/view/<?php echo $_smarty_tpl->tpl_vars['ds']->value['id'];?>
'" style="cursor:pointer;">
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['invoice'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['username'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['plan_name'];?>
</td>
                                    <td><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['ds']->value['price']);?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['type'];?>
</td>
                                    <td class="text-success">
                                        <?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['ds']->value['recharged_on'],$_smarty_tpl->tpl_vars['ds']->value['recharged_time']);?>

                                    </td>
                                    <td class="text-danger"><?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['ds']->value['expiration'],$_smarty_tpl->tpl_vars['ds']->value['time']);?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['method'];?>
</td>
                                </tr>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </tbody>
                    <?php }?>
                    <?php if (Lang::arrayCount($_smarty_tpl->tpl_vars['order']->value)) {?>
                        <thead>
                            <tr>
                                <th><?php echo Lang::T('Plan Name');?>
</th>
                                <th><?php echo Lang::T('Gateway');?>
</th>
                                <th><?php echo Lang::T('Routers');?>
</th>
                                <th><?php echo Lang::T('Type');?>
</th>
                                <th><?php echo Lang::T('Plan Price');?>
</th>
                                <th><?php echo Lang::T('Created On');?>
</th>
                                <th><?php echo Lang::T('Expires On');?>
</th>
                                <th><?php echo Lang::T('Date Done');?>
</th>
                                <th><?php echo Lang::T('Method');?>
</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['order']->value, 'ds');
$_smarty_tpl->tpl_vars['ds']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ds']->value) {
$_smarty_tpl->tpl_vars['ds']->do_else = false;
?>
                                <tr>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['plan_name'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['gateway'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['routers'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['payment_channel'];?>
</td>
                                    <td><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['ds']->value['price']);?>
</td>
                                    <td class="text-primary"><?php echo Lang::dateTimeFormat($_smarty_tpl->tpl_vars['ds']->value['created_date']);?>
</td>
                                    <td class="text-danger"><?php echo Lang::dateTimeFormat($_smarty_tpl->tpl_vars['ds']->value['expired_date']);?>
</td>
                                    <td class="text-success"><?php if ($_smarty_tpl->tpl_vars['ds']->value['status'] != 1) {
echo Lang::dateTimeFormat($_smarty_tpl->tpl_vars['ds']->value['paid_date']);
}?>
                                    </td>
                                    <td><?php if ($_smarty_tpl->tpl_vars['ds']->value['status'] == 1) {
echo Lang::T('UNPAID');?>

                                        <?php } elseif ($_smarty_tpl->tpl_vars['ds']->value['status'] == 2) {
echo Lang::T('PAID');?>

                                        <?php } elseif ($_smarty_tpl->tpl_vars['ds']->value['status'] == 3) {
echo $_smarty_tpl->tpl_vars['_L']->value['FAILED'];?>

                                        <?php } elseif ($_smarty_tpl->tpl_vars['ds']->value['status'] == 4) {
echo Lang::T('CANCELED');?>

                                        <?php } elseif ($_smarty_tpl->tpl_vars['ds']->value['status'] == 5) {
echo Lang::T('UNKNOWN');?>

                                        <?php }?></td>
                                </tr>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </tbody>
                    <?php }?>
                </table>
            </div>
            <?php $_smarty_tpl->_subTemplateRender("file:pagination.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
        </div>
        <div class="row">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['packages']->value, 'package');
$_smarty_tpl->tpl_vars['package']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['package']->value) {
$_smarty_tpl->tpl_vars['package']->do_else = false;
?>
                <div class="col-md-6">
                    <div class="box box-<?php if ($_smarty_tpl->tpl_vars['package']->value['status'] == 'on') {?>success<?php } else { ?>danger<?php }?>" data-package-id="<?php echo $_smarty_tpl->tpl_vars['package']->value['id'];?>
">
                        <div class="box-body box-profile">
                            <h4 class="text-center"><?php echo $_smarty_tpl->tpl_vars['package']->value['type'];?>
 - <?php echo $_smarty_tpl->tpl_vars['package']->value['namebp'];?>
 <span
                                    api-get-text="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
autoload/customer_is_active/<?php echo $_smarty_tpl->tpl_vars['package']->value['username'];?>
/<?php echo $_smarty_tpl->tpl_vars['package']->value['plan_id'];?>
"></span>
                            </h4>
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <?php echo Lang::T('Active');?>
 <span class="pull-right"><?php if ($_smarty_tpl->tpl_vars['package']->value['status'] == 'on') {?>yes<?php } else { ?>no
                                    <?php }?></span>
                                </li>
                                <li class="list-group-item">
                                    <?php echo Lang::T('Type');?>
 <span class="pull-right">
                                        <?php if ($_smarty_tpl->tpl_vars['package']->value['prepaid'] == 'yes') {?>Prepaid<?php } else { ?><b>Postpaid</b><?php }?></span>
                                </li>
                                <li class="list-group-item">
                                    <?php echo Lang::T('Bandwidth');?>
 <span class="pull-right">
                                        <?php echo $_smarty_tpl->tpl_vars['package']->value['name_bw'];?>
</span>
                                </li>
                                <li class="list-group-item">
                                    <?php echo Lang::T('Created On');?>
 <span
                                        class="pull-right"><?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['package']->value['recharged_on'],$_smarty_tpl->tpl_vars['package']->value['recharged_time']);?>
</span>
                                </li>
                                <li class="list-group-item">
                                    <?php echo Lang::T('Expires On');?>
 <span class="pull-right"><?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['package']->value['expiration'],$_smarty_tpl->tpl_vars['package']->value['time']);?>
</span>
                                </li>
                                <li class="list-group-item">
                                    <?php echo $_smarty_tpl->tpl_vars['package']->value['routers'];?>
 <span class="pull-right"><?php echo $_smarty_tpl->tpl_vars['package']->value['method'];?>
</span>
                                </li>
                            </ul>
                            <div class="row">
                                <?php if ($_smarty_tpl->tpl_vars['_c']->value['extend_expired']) {?>
                                <div class="col-xs-3">
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/deactivate/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
/<?php echo $_smarty_tpl->tpl_vars['package']->value['plan_id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
"
                                        class="btn btn-3d btn-3d-danger btn-sm btn-block"
                                        onclick="return ask(this, 'This will deactivate Customer Plan, and make it expired')"><?php echo Lang::T('Deactivate');?>
</a>
                                </div>
                                <div class="col-xs-3">
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/edit/<?php echo $_smarty_tpl->tpl_vars['package']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
"
                                        class="btn btn-3d btn-3d-primary btn-sm btn-block"><?php echo Lang::T('Edit Plan');?>
</a>
                                </div>
                                <div class="col-xs-3">
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/recharge/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
/<?php echo $_smarty_tpl->tpl_vars['package']->value['plan_id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
"
                                        class="btn btn-3d btn-3d-success btn-sm btn-block"><?php echo Lang::T('Recharge');?>
</a>
                                </div>
                                <div class="col-xs-3">
                                    <button onclick="extendPackage('<?php echo $_smarty_tpl->tpl_vars['package']->value['id'];?>
')" class="btn btn-3d btn-3d-warning btn-sm btn-block">
                                        <i class="fa fa-clock-o"></i> <?php echo Lang::T('Extend');?>

                                    </button>
                                </div>
                                <?php } else { ?>
                                <div class="col-xs-4">
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/deactivate/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
/<?php echo $_smarty_tpl->tpl_vars['package']->value['plan_id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
"
                                        class="btn btn-3d btn-3d-danger btn-sm btn-block"
                                        onclick="return ask(this, 'This will deactivate Customer Plan, and make it expired')"><?php echo Lang::T('Deactivate');?>
</a>
                                </div>
                                <div class="col-xs-4">
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/edit/<?php echo $_smarty_tpl->tpl_vars['package']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
"
                                        class="btn btn-3d btn-3d-primary btn-sm btn-block"><?php echo Lang::T('Edit Plan');?>
</a>
                                </div>
                                <div class="col-xs-4">
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/recharge/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
/<?php echo $_smarty_tpl->tpl_vars['package']->value['plan_id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
"
                                        class="btn btn-3d btn-3d-success btn-sm btn-block"><?php echo Lang::T('Recharge');?>
</a>
                                </div>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div>
    </div>
</div>

<?php if ((isset($_smarty_tpl->tpl_vars['devices']->value)) && count($_smarty_tpl->tpl_vars['devices']->value) > 0) {?>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-wifi"></i> <?php echo Lang::T('Connected Devices');?>
</h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/enable/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" 
                       onclick="return ask(this, 'This will enable the customer on Mikrotik router. Continue?')"
                       class="btn btn-3d btn-3d-success btn-sm" style="margin-right: 5px;">
                        <i class="fa fa-play"></i> <?php echo Lang::T('Enable');?>

                    </a>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/disable/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" 
                       onclick="return ask(this, 'This will disable the customer on Mikrotik router and disconnect them. Continue?')"
                       class="btn btn-3d btn-3d-danger btn-sm" style="margin-right: 5px;">
                        <i class="fa fa-stop"></i> <?php echo Lang::T('Disable');?>

                    </a>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/reconnect/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" 
                       onclick="return ask(this, 'This will disconnect and reconnect the customer. Continue?')"
                       class="btn btn-3d btn-3d-warning btn-sm" style="margin-right: 10px;">
                        <i class="fa fa-refresh"></i> <?php echo Lang::T('Reconnect');?>

                    </a>
                    <span data-toggle="tooltip" title="Total Connected Devices" class="badge bg-blue"><?php echo count($_smarty_tpl->tpl_vars['devices']->value);?>
</span>
                </div>
            </div>
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="width: 15%"><?php echo Lang::T('Type');?>
</th>
                                <th style="width: 30%"><?php echo Lang::T('MAC Address');?>
</th>
                                <th style="width: 25%"><?php echo Lang::T('IP Address');?>
</th>
                                <th style="width: 20%"><?php echo Lang::T('Uptime');?>
</th>
                                <th style="width: 10%"><?php echo Lang::T('Status');?>
</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['devices']->value, 'device');
$_smarty_tpl->tpl_vars['device']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['device']->value) {
$_smarty_tpl->tpl_vars['device']->do_else = false;
?>
                                <tr>
                                    <td>
                                        <?php if ($_smarty_tpl->tpl_vars['device']->value['type'] == 'Hotspot') {?>
                                            <span class="label label-primary" style="font-size: 12px;">
                                                <i class="fa fa-dot-circle-o"></i> <?php echo $_smarty_tpl->tpl_vars['device']->value['type'];?>

                                            </span>
                                        <?php } else { ?>
                                            <span class="label label-info" style="font-size: 12px;">
                                                <i class="fa fa-plug"></i> <?php echo $_smarty_tpl->tpl_vars['device']->value['type'];?>

                                            </span>
                                        <?php }?>
                                    </td>
                                    <td>
                                        <code style="background: #f8f9fa; padding: 5px 8px; border-radius: 4px; color: #495057; font-size: 13px;">
                                            <i class="fa fa-microchip"></i> <?php echo $_smarty_tpl->tpl_vars['device']->value['mac_address'];?>

                                        </code>
                                    </td>
                                    <td>
                                        <code style="background: #f8f9fa; padding: 5px 8px; border-radius: 4px; color: #495057; font-size: 13px;">
                                            <i class="fa fa-globe"></i> <?php echo $_smarty_tpl->tpl_vars['device']->value['ip_address'];?>

                                        </code>
                                    </td>
                                    <td>
                                        <i class="fa fa-clock-o"></i> <?php echo $_smarty_tpl->tpl_vars['device']->value['uptime'];?>

                                    </td>
                                    <td>
                                        <span class="label label-success">
                                            <i class="fa fa-check-circle"></i> Active
                                        </span>
                                    </td>
                                </tr>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box-footer text-center" style="background-color: #f8f9fa; border-top: 1px solid #e9ecef;">
                <small class="text-muted">
                    <i class="fa fa-info-circle"></i> Last Updated: <?php echo smarty_modifier_date_format(time(),"%H:%M:%S");?>

                </small>
            </div>
        </div>
    </div>
</div>
<?php } else { ?>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-wifi"></i> <?php echo Lang::T('Router Control');?>
</h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/enable/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" 
                       onclick="return ask(this, 'This will enable the customer on Mikrotik router. Continue?')"
                       class="btn btn-3d btn-3d-success btn-sm" style="margin-right: 5px;">
                        <i class="fa fa-play"></i> <?php echo Lang::T('Enable');?>

                    </a>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/disable/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" 
                       onclick="return ask(this, 'This will disable the customer on Mikrotik router and disconnect them. Continue?')"
                       class="btn btn-3d btn-3d-danger btn-sm" style="margin-right: 5px;">
                        <i class="fa fa-stop"></i> <?php echo Lang::T('Disable');?>

                    </a>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/reconnect/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" 
                       onclick="return ask(this, 'This will disconnect and reconnect the customer. Continue?')"
                       class="btn btn-3d btn-3d-warning btn-sm" style="margin-right: 10px;">
                        <i class="fa fa-refresh"></i> <?php echo Lang::T('Reconnect');?>

                    </a>
                </div>
            </div>
            <div class="box-body text-center" style="padding: 40px;">
                <i class="fa fa-wifi" style="font-size: 48px; color: #bbb; margin-bottom: 15px;"></i>
                <h4 style="color: #666;"><?php echo Lang::T('No Connected Devices');?>
</h4>
                <p class="text-muted"><?php echo Lang::T('This customer has no active connections at the moment.');?>
</p>
            </div>
        </div>
    </div>
</div>
<?php }?>

<hr>
<div class="row">
    <div class="col-xs-6 col-md-3">
        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/list" class="btn btn-3d btn-3d btn-default btn-sm btn-block"><?php echo Lang::T('Back');?>
</a>
    </div>
    <div class="col-xs-6 col-md-3">
        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/sync/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" onclick="return ask(this, 'This will sync Customer to Mikrotik?')"
            class="btn btn-3d btn-3d-info btn-sm btn-block"><?php echo Lang::T('Sync');?>
</a>
    </div>
    <div class="col-xs-6 col-md-3">
        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
message/send/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" class="btn btn-3d btn-3d-success btn-sm btn-block">
            <?php echo Lang::T('Send Message');?>

        </a>
    </div>
    <div class="col-xs-6 col-md-3">
        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/login/<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
&token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" target="_blank" class="btn btn-3d btn-3d-primary btn-sm btn-block">
            <?php echo Lang::T('Login as Customer');?>

        </a>
    </div>
</div>

<?php if ($_smarty_tpl->tpl_vars['d']->value['coordinates']) {?>
    
        <?php echo '<script'; ?>
 src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
>
            function setupMap(lat, lon) {
                var map = L.map('map').setView([lat, lon], 17);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/light_all/{z}/{x}/{y}.png', {
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    subdomains: 'abcd',
                    maxZoom: 20
            }).addTo(map);
            var marker = L.marker([lat, lon]).addTo(map);
            }
            window.onload = function() {
                setupMap(<?php echo $_smarty_tpl->tpl_vars['d']->value['coordinates'];?>
);
            }
        <?php echo '</script'; ?>
>
    
<?php }?>

<?php echo '<script'; ?>
>
function confirmDelete(url) {
    if (confirm('<?php echo Lang::T('Delete');?>
?')) {
        window.location.href = url;
    }
}

function extendCustomerPlan(customerId) {
    // Get customer's active packages to extend
    var packages = document.querySelectorAll('[data-package-id]');
    if (packages.length === 0) {
        alert('No active packages found for this customer');
        return;
    }
    
    // If only one package, extend it directly
    if (packages.length === 1) {
        var packageId = packages[0].getAttribute('data-package-id');
        extendPackage(packageId);
        return;
    }
    
    // If multiple packages, let user choose or extend the first active one
    var activePackages = [];
    for (var i = 0; i < packages.length; i++) {
        var packageBox = packages[i];
        if (packageBox.classList.contains('box-success')) {
            activePackages.push(packageBox.getAttribute('data-package-id'));
        }
    }
    
    if (activePackages.length > 0) {
        // Extend the first active package
        extendPackage(activePackages[0]);
    } else {
        // No active packages, extend the first one
        extendPackage(packages[0].getAttribute('data-package-id'));
    }
}

function extendPackage(packageId) {
    var days = prompt("Extend for how many days?", "3");
    if (days) {
        if (confirm("Extend for " + days + " days?")) {
            window.location.href = "<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/extend/" + packageId + "/" + days + "&stoken=<?php echo App::getToken();?>
";
        }
    }
}
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
