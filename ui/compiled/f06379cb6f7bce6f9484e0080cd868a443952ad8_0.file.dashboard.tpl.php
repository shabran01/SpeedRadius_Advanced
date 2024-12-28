<?php
/* Smarty version 4.5.3, created on 2024-12-23 06:53:29
  from '/var/www/html/snootylique/ui/ui/customer/dashboard.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_6768deb95e8d82_21909740',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f06379cb6f7bce6f9484e0080cd868a443952ad8' => 
    array (
      0 => '/var/www/html/snootylique/ui/ui/customer/dashboard.tpl',
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
function content_6768deb95e8d82_21909740 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:customer/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<!-- user-dashboard -->

<div class="row">
    <div class="col col-md-6 col-md-push-6">
        <?php if ($_smarty_tpl->tpl_vars['unpaid']->value) {?>
            <div class="box box-danger box-solid">
                <div class="box-header">
                    <h3 class="box-title"><?php echo Lang::T('Unpaid Order');?>
</h3>
                </div>
                <div style="margin-left: 5px; margin-right: 5px;">
                    <table class="table table-condensed table-bordered table-striped table-hover"
                        style="margin-bottom: 0px;">
                        <tbody>
                            <tr>
                                <td><?php echo Lang::T('expired');?>
</td>
                                <td><?php echo Lang::dateTimeFormat($_smarty_tpl->tpl_vars['unpaid']->value['expired_date']);?>
 </td>
                            </tr>
                            <tr>
                                <td><?php echo Lang::T('Package Name');?>
</td>
                                <td><?php echo $_smarty_tpl->tpl_vars['unpaid']->value['plan_name'];?>
</td>
                            </tr>
                            <tr>
                                <td><?php echo Lang::T('Package Price');?>
</td>
                                <td><?php echo $_smarty_tpl->tpl_vars['unpaid']->value['price'];?>
</td>
                            </tr>
                            <tr>
                                <td><?php echo Lang::T('Routers');?>
</td>
                                <td><?php echo $_smarty_tpl->tpl_vars['unpaid']->value['routers'];?>
</td>
                            </tr>
                        </tbody>
                    </table> &nbsp;
                </div>
                <div class="box-footer p-2">
                    <div class="btn-group btn-group-justified mb15">
                        <div class="btn-group">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
order/view/<?php echo $_smarty_tpl->tpl_vars['unpaid']->value['id'];?>
/cancel" class="btn btn-danger btn-sm"
                                onclick="return ask(this, '<?php echo Lang::T('Cancel it?');?>
')">
                                <span class="glyphicon glyphicon-trash"></span>
                                <?php echo Lang::T('Cancel');?>

                            </a>
                        </div>
                        <div class="btn-group">
                            <a class="btn btn-success btn-block btn-sm" href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
order/view/<?php echo $_smarty_tpl->tpl_vars['unpaid']->value['id'];?>
">
                                <span class="icon"><i class="ion ion-card"></i></span>
                                <span><?php echo Lang::T('PAY NOW');?>
</span>
                            </a>
                        </div>
                    </div>

                </div>&nbsp;&nbsp;
            </div>
        <?php }?>
        <div class="box box-info box-solid">
            <div class="box-header">
                <h3 class="box-title"><?php echo Lang::T('Announcement');?>
</h3>
            </div>
            <div class="box-body">
                <?php $_smarty_tpl->_assignInScope('Announcement_Customer', ((string)$_smarty_tpl->tpl_vars['PAGES_PATH']->value)."/Announcement_Customer.html");?>
                <?php if (file_exists($_smarty_tpl->tpl_vars['Announcement_Customer']->value)) {?>
                    <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['Announcement_Customer']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                <?php }?>
            </div>
        </div>
    </div>
    <div class="col col-md-6 col-md-pull-6">
        <div class="box box-primary box-solid">
            <div class="box-header">
                <h3 class="box-title"><?php echo Lang::T('Your Account Information');?>
</h3>
            </div>
            <div style="margin-left: 5px; margin-right: 5px;">
                <table class="table table-bordered table-striped table-bordered table-hover mb-0"
                    style="margin-bottom: 0px;">
                    <tr>
                        <td class="small text-success text-uppercase text-normal"><?php echo Lang::T('Usernames');?>
</td>
                        <td class="small mb15"><?php echo $_smarty_tpl->tpl_vars['_user']->value['username'];?>
</td>
                    </tr>
                    <tr>
                        <td class="small text-success text-uppercase text-normal"><?php echo Lang::T('Password');?>
</td>
                        <td class="small mb15"><input type="password" value="<?php echo $_smarty_tpl->tpl_vars['_user']->value['password'];?>
"
                                style="width:100%; border: 0px;" onmouseleave="this.type = 'password'"
                                onmouseenter="this.type = 'text'" onclick="this.select()"></td>
                    </tr>
                    <tr>
                        <td class="small text-success text-uppercase text-normal"><?php echo Lang::T('Service Type');?>
</td>
                        <td class="small mb15">
                            <?php if ($_smarty_tpl->tpl_vars['_user']->value['service_type'] == 'Hotspot') {?>
                                Hotspot
                            <?php } elseif ($_smarty_tpl->tpl_vars['_user']->value['service_type'] == 'PPPoE') {?>
                                PPPoE
                            <?php } elseif ($_smarty_tpl->tpl_vars['_user']->value['service_type'] == 'VPN') {?>
                                VPN
                            <?php } elseif ($_smarty_tpl->tpl_vars['_user']->value['service_type'] == 'Others' || $_smarty_tpl->tpl_vars['_user']->value['service_type'] == null) {?>
                                Others
                            <?php }?>
                        </td>
                    </tr>

                    <?php if ($_smarty_tpl->tpl_vars['_c']->value['enable_balance'] == 'yes') {?>
                        <tr>
                            <td class="small text-warning text-uppercase text-normal"><?php echo Lang::T('Yours Balance');?>
</td>
                            <td class="small mb15 text-bold">
                                <?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['_user']->value['balance']);?>

                                <?php if ($_smarty_tpl->tpl_vars['_user']->value['auto_renewal'] == 1) {?>
                                    <a class="label label-success pull-right" href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
home&renewal=0"
                                        onclick="return ask(this, '<?php echo Lang::T('Disable auto renewal?');?>
')"><?php echo Lang::T('Auto Renewal
                                On');?>
</a>
                                <?php } else { ?>
                                    <a class="label label-danger pull-right" href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
home&renewal=1"
                                        onclick="return ask(this, '<?php echo Lang::T('Enable auto renewal?');?>
')"><?php echo Lang::T('Auto Renewal
                                Off');?>
</a>
                                <?php }?>
                            </td>
                        </tr>
                    <?php }?>
                </table>&nbsp;&nbsp;
            </div>
            <?php if ($_smarty_tpl->tpl_vars['abills']->value && count($_smarty_tpl->tpl_vars['abills']->value) > 0) {?>
                <div class="box-header">
                    <h3 class="box-title"><?php echo Lang::T('Additional Billing');?>
</h3>
                </div>

                <div style="margin-left: 5px; margin-right: 5px;">
                    <table class="table table-bordered table-striped table-bordered table-hover mb-0"
                        style="margin-bottom: 0px;">
                        <?php $_smarty_tpl->_assignInScope('total', 0);?>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['abills']->value, 'v', false, 'k');
$_smarty_tpl->tpl_vars['v']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->do_else = false;
?>
                            <tr>
                                <td class="small text-success text-uppercase text-normal"><?php echo str_replace(' Bill','',$_smarty_tpl->tpl_vars['k']->value);?>
</td>
                                <td class="small mb15">
                                    <?php if (strpos($_smarty_tpl->tpl_vars['v']->value,':') === false) {?>
                                        <?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['v']->value);?>

                                        <sup title="recurring">∞</sup>
                                        <?php $_smarty_tpl->_assignInScope('total', $_smarty_tpl->tpl_vars['v']->value+$_smarty_tpl->tpl_vars['total']->value);?>
                                    <?php } else { ?>
                                        <?php $_smarty_tpl->_assignInScope('exp', explode(':',$_smarty_tpl->tpl_vars['v']->value));?>
                                        <?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['exp']->value[0]);?>

                                        <sup title="<?php echo $_smarty_tpl->tpl_vars['exp']->value[1];?>
 more times"><?php if ($_smarty_tpl->tpl_vars['exp']->value[1] == 0) {
echo Lang::T('paid
                                off');
} else {
echo $_smarty_tpl->tpl_vars['exp']->value[1];?>
x<?php }?></sup>
                                        <?php if ($_smarty_tpl->tpl_vars['exp']->value[1] > 0) {?>
                                            <?php $_smarty_tpl->_assignInScope('total', $_smarty_tpl->tpl_vars['exp']->value[0]+$_smarty_tpl->tpl_vars['total']->value);?>
                                        <?php }?>
                                    <?php }?>
                                </td>
                            </tr>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        <tr>
                            <td class="small text-success text-uppercase text-normal"><b><?php echo Lang::T('Total');?>
</b></td>
                            <td class="small mb15"><b>
                                    <?php if ($_smarty_tpl->tpl_vars['total']->value == 0) {?>
                                        <?php echo ucwords(Lang::T('paid off'));?>

                                    <?php } else { ?>
                                        <?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['total']->value);?>

                                    <?php }?>
                                </b></td>
                        </tr>
                    </table>
                </div> &nbsp;&nbsp;
            <?php }?>
        </div>
        <?php if ($_smarty_tpl->tpl_vars['_bills']->value) {?>
            <div class="box box-primary box-solid">
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_bills']->value, '_bill');
$_smarty_tpl->tpl_vars['_bill']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['_bill']->value) {
$_smarty_tpl->tpl_vars['_bill']->do_else = false;
?>
                    <?php if ($_smarty_tpl->tpl_vars['_bill']->value['routers'] != 'radius') {?>
                        <div class="box-header">
                            <h3 class="box-title"><?php echo $_smarty_tpl->tpl_vars['_bill']->value['routers'];?>
</h3>
                            <div class="btn-group pull-right">
                                <?php if ($_smarty_tpl->tpl_vars['_bill']->value['type'] == 'Hotspot') {?>
                                    <?php if ($_smarty_tpl->tpl_vars['_c']->value['hotspot_plan'] == '') {?>Hotspot Plan<?php } else {
echo $_smarty_tpl->tpl_vars['_c']->value['hotspot_plan'];
}?>
                                <?php } elseif ($_smarty_tpl->tpl_vars['_bill']->value['type'] == 'PPPOE') {?>
                                    <?php if ($_smarty_tpl->tpl_vars['_c']->value['pppoe_plan'] == '') {?>PPPOE Plan<?php } else {
echo $_smarty_tpl->tpl_vars['_c']->value['pppoe_plan'];
}?>
                                <?php } elseif ($_smarty_tpl->tpl_vars['_bill']->value['type'] == 'VPN') {?>
                                    <?php if ($_smarty_tpl->tpl_vars['_c']->value['pppoe_plan'] == '') {?>VPN Plan<?php } else {
echo $_smarty_tpl->tpl_vars['_c']->value['vpn_plan'];
}?>
                                <?php }?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="box-header">
                            <h3 class="box-title"><?php if ($_smarty_tpl->tpl_vars['_c']->value['radius_plan'] == '') {?>Radius Plan<?php } else {
echo $_smarty_tpl->tpl_vars['_c']->value['radius_plan'];
}?></h3>
                        </div>
                    <?php }?>
                    <div style="margin-left: 5px; margin-right: 5px;">
                        <table class="table table-bordered table-striped table-bordered table-hover"
                            style="margin-bottom: 0px;">
                            <tr>
                                <td class="small text-primary text-uppercase text-normal"><?php echo Lang::T('Package Name');?>
</td>
                                <td class="small mb15">
                                    <?php echo $_smarty_tpl->tpl_vars['_bill']->value['namebp'];?>

                                    <?php if ($_smarty_tpl->tpl_vars['_bill']->value['status'] != 'on') {?>
                                        <a class="label label-danger pull-right"
                                            href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
order/package"><?php echo Lang::T('Expired');?>
</a>
                                    <?php }?>
                                </td>
                            </tr>
                            <?php if ($_smarty_tpl->tpl_vars['_c']->value['show_bandwidth_plan'] == 'yes') {?>
                                <tr>
                                    <td class="small text-primary text-uppercase text-normal"><?php echo Lang::T('Bandwidth');?>
</td>
                                    <td class="small mb15">
                                        <?php echo $_smarty_tpl->tpl_vars['_bill']->value['name_bw'];?>

                                    </td>
                                </tr>
                            <?php }?>
                            <tr>
                                <td class="small text-info text-uppercase text-normal"><?php echo Lang::T('Created On');?>
</td>
                                <td class="small mb15">
                                    <?php if ($_smarty_tpl->tpl_vars['_bill']->value['time'] != '') {
echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['_bill']->value['recharged_on'],$_smarty_tpl->tpl_vars['_bill']->value['recharged_time']);?>

                                <?php }?>&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="small text-danger text-uppercase text-normal"><?php echo Lang::T('Expires On');?>
</td>
                            <td class="small mb15 text-danger">
                                <?php if ($_smarty_tpl->tpl_vars['_bill']->value['time'] != '') {
echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['_bill']->value['expiration'],$_smarty_tpl->tpl_vars['_bill']->value['time']);
}?>&nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td class="small text-success text-uppercase text-normal"><?php echo Lang::T('Type');?>
</td>
                        <td class="small mb15 text-success">
                            <b><?php if ($_smarty_tpl->tpl_vars['_bill']->value['prepaid'] == 'yes') {?>Prepaid<?php } else { ?>Postpaid<?php }?></b>
                            <?php echo $_smarty_tpl->tpl_vars['_bill']->value['plan_type'];?>

                        </td>
                    </tr>
                    <?php if ($_smarty_tpl->tpl_vars['_bill']->value['type'] == 'VPN' && $_smarty_tpl->tpl_vars['_bill']->value['routers'] == $_smarty_tpl->tpl_vars['vpn']->value['routers']) {?>
                        <tr>
                            <td class="small text-success text-uppercase text-normal"><?php echo Lang::T('Public IP');?>
</td>
                            <td class="small mb15"><?php echo $_smarty_tpl->tpl_vars['vpn']->value['public_ip'];?>
 / <?php echo $_smarty_tpl->tpl_vars['vpn']->value['port_name'];?>
</td>
                        </tr>
                        <tr>
                            <td class="small text-success text-uppercase text-normal"><?php echo Lang::T('Private IP');?>
</td>
                            <td class="small mb15"><?php echo $_smarty_tpl->tpl_vars['_user']->value['pppoe_ip'];?>
</td>
                        </tr>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['cf']->value, 'tcf');
$_smarty_tpl->tpl_vars['tcf']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['tcf']->value) {
$_smarty_tpl->tpl_vars['tcf']->do_else = false;
?>
                            <tr>
                                <?php if ($_smarty_tpl->tpl_vars['tcf']->value['field_name'] == 'Winbox' || $_smarty_tpl->tpl_vars['tcf']->value['field_name'] == 'Api' || $_smarty_tpl->tpl_vars['tcf']->value['field_name'] == 'Web') {?>
                                    <td class="small text-info text-uppercase text-normal"><?php echo $_smarty_tpl->tpl_vars['tcf']->value['field_name'];?>
 - Port</td>
                                    <td class="small mb15"><a href="http://<?php echo $_smarty_tpl->tpl_vars['vpn']->value['public_ip'];?>
:<?php echo $_smarty_tpl->tpl_vars['tcf']->value['field_value'];?>
"
                                            target="_blank"><?php echo $_smarty_tpl->tpl_vars['tcf']->value['field_value'];?>
</a></td>
                                </tr>
                            <?php }?>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    <?php }?>

                    <?php if ($_smarty_tpl->tpl_vars['nux_ip']->value != '') {?>
                        <tr>
                            <td class="small text-primary text-uppercase text-normal"><?php echo Lang::T('Current IP');?>
</td>
                            <td class="small mb15"><?php echo $_smarty_tpl->tpl_vars['nux_ip']->value;?>
</td>
                        </tr>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['nux_mac']->value != '') {?>
                        <tr>
                            <td class="small text-primary text-uppercase text-normal"><?php echo Lang::T('Current MAC');?>
</td>
                            <td class="small mb15"><?php echo $_smarty_tpl->tpl_vars['nux_mac']->value;?>
</td>
                        </tr>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['_bill']->value['type'] == 'Hotspot' && $_smarty_tpl->tpl_vars['_bill']->value['status'] == 'on' && $_smarty_tpl->tpl_vars['_bill']->value['routers'] != 'radius' && $_smarty_tpl->tpl_vars['_c']->value['hs_auth_method'] != 'hchap') {?>
                    <tr>
                        <td class="small text-primary text-uppercase text-normal"><?php echo Lang::T('Login Status');?>
</td>
                        <td class="small mb15" id="login_status_<?php echo $_smarty_tpl->tpl_vars['_bill']->value['id'];?>
">
                            <img src="ui/ui/images/loading.gif">
                        </td>
                    </tr>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['_bill']->value['type'] == 'Hotspot' && $_smarty_tpl->tpl_vars['_bill']->value['status'] == 'on' && $_smarty_tpl->tpl_vars['_c']->value['hs_auth_method'] == 'hchap') {?>
                        <tr>
                            <td class="small text-primary text-uppercase text-normal"><?php echo Lang::T('Login Status');?>
</td>
                            <td class="small mb15">
                                <?php if ($_smarty_tpl->tpl_vars['logged']->value == '1') {?>
                                    <a href="http://<?php echo $_smarty_tpl->tpl_vars['hostname']->value;?>
/status" class="btn btn-success btn-xs btn-block"><?php echo Lang::T('You
                                are
                                Online, Check Status');?>
</a>
                                <?php } else { ?>
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
home&mikrotik=login"
                                        onclick="return ask(this, '<?php echo Lang::T('Connect to Internet');?>
')"
                                        class="btn btn-danger btn-xs btn-block"><?php echo Lang::T('Not Online, Login now?');?>
</a>
                                <?php }?>
                            </td>
                        </tr>
                    <?php }?>
                    <tr>
                        <td class="small text-primary text-uppercase text-normal">
                            <?php if ($_smarty_tpl->tpl_vars['_bill']->value['status'] == 'on' && $_smarty_tpl->tpl_vars['_bill']->value['prepaid'] != 'YES') {?>
                                <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
home&deactivate=<?php echo $_smarty_tpl->tpl_vars['_bill']->value['id'];?>
"
                                    onclick="return ask(this, '<?php echo Lang::T('Deactivate');?>
?')" class="btn btn-danger btn-xs"><i
                                        class="glyphicon glyphicon-trash"></i></a>
                            <?php }?>
                        </td>
                        <td class="small row">
                            <?php if ($_smarty_tpl->tpl_vars['_bill']->value['status'] != 'on' && $_smarty_tpl->tpl_vars['_bill']->value['prepaid'] != 'yes' && $_smarty_tpl->tpl_vars['_c']->value['extend_expired']) {?>
                                <a class="btn btn-warning text-black btn-sm"
                                    href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
home&extend=<?php echo $_smarty_tpl->tpl_vars['_bill']->value['id'];?>
&stoken=<?php echo App::getToken();?>
"
                                    onclick="return ask(this, '<?php echo Text::toHex($_smarty_tpl->tpl_vars['_c']->value['extend_confirmation']);?>
')"><?php echo Lang::T('Extend');?>
</a>
                            <?php }?>
                            <a class="btn btn-primary pull-right btn-sm"
                                href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
home&recharge=<?php echo $_smarty_tpl->tpl_vars['_bill']->value['id'];?>
&stoken=<?php echo App::getToken();?>
"
                                onclick="return ask(this, '<?php echo Lang::T('Recharge');?>
?')"><?php echo Lang::T('Recharge');?>
</a>
                            <a class="btn btn-warning text-black pull-right btn-sm"
                                href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
home&sync=<?php echo $_smarty_tpl->tpl_vars['_bill']->value['id'];?>
&stoken=<?php echo App::getToken();?>
"
                                onclick="return ask(this, '<?php echo Lang::T('Sync account if you failed login to internet');?>
?')"
                                data-toggle="tooltip" data-placement="top"
                                title="<?php echo Lang::T('Sync account if you failed login to internet');?>
"><span
                                    class="glyphicon glyphicon-refresh" aria-hidden="true"></span> <?php echo Lang::T('Sync');?>
</a>
                        </td>
                    </tr>
                </table>
            </div>
            &nbsp;&nbsp;
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['disable_voucher'] == 'yes') {?>
            <div class="box-footer">
                <?php if ($_smarty_tpl->tpl_vars['_c']->value['payment_gateway'] != 'none' || $_smarty_tpl->tpl_vars['_c']->value['payment_gateway'] == '') {?>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
order/package" class="btn btn-primary btn-block">
                        <i class="ion ion-ios-cart"></i>
                        <?php echo Lang::T('Order Package');?>

                    </a>
                <?php }?>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_bills']->value) {?>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_bills']->value, '_bill');
$_smarty_tpl->tpl_vars['_bill']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['_bill']->value) {
$_smarty_tpl->tpl_vars['_bill']->do_else = false;
?>
                <?php if ($_smarty_tpl->tpl_vars['_bill']->value['type'] == 'Hotspot' && $_smarty_tpl->tpl_vars['_bill']->value['status'] == 'on' && $_smarty_tpl->tpl_vars['_c']->value['hs_auth_method'] != 'hchap') {?>
                    <?php echo '<script'; ?>
>
                        setTimeout(() => {
                            $.ajax({
                                url: "?_route=autoload_user/isLogin/<?php echo $_smarty_tpl->tpl_vars['_bill']->value['id'];?>
",
                                cache: false,
                                success: function(msg) {
                                    $("#login_status_<?php echo $_smarty_tpl->tpl_vars['_bill']->value['id'];?>
").html(msg);
                                }
                            });
                        }, 2000);
                    <?php echo '</script'; ?>
>
                <?php }?>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['enable_balance'] == 'yes' && $_smarty_tpl->tpl_vars['_c']->value['allow_balance_transfer'] == 'yes') {?>
            <div class="box box-primary box-solid mb30">
                <div class="box-header">
                    <h4 class="box-title"><?php echo Lang::T("Transfer Balance");?>
</h4>
                </div>
                <div class="box-body p-0">
                    <form method="post" onsubmit="return askConfirm()" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
home">
                        <div class="form-group">
                            <div class="col-sm-5">
                                <input type="text" id="username" name="username" class="form-control" required
                                    placeholder="<?php echo Lang::T('Friend Usernames');?>
">
                            </div>
                            <div class="col-sm-5">
                                <input type="number" id="balance" name="balance" autocomplete="off" class="form-control"
                                    required placeholder="<?php echo Lang::T('Balance Amount');?>
">
                            </div>
                            <div class="form-group col-sm-2" align="center">
                                <button class="btn btn-success btn-block" id="sendBtn" type="submit" name="send"
                                    onclick="return ask(this, '<?php echo Lang::T(" Are You Sure?");?>
')" value="balance"><i
                                        class="glyphicon glyphicon-send"></i></button>
                            </div>
                        </div>
                    </form>
                    <?php echo '<script'; ?>
>
                        function askConfirm() {
                            if (confirm('<?php echo Lang::T('Send yours balance ? ');?>
')) {
                            setTimeout(() => {
                                document.getElementById('sendBtn').setAttribute('disabled', '');
                            }, 1000);
                            return true;
                        }
                        return false;
                        }
                    <?php echo '</script'; ?>
>
                </div>
                <div class="box-header">
                    <h4 class="box-title"><?php echo Lang::T("Recharge a friend");?>
</h4>
                </div>
                <div class="box-body p-0">
                    <form method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
home">
                        <div class="form-group">
                            <div class="col-sm-10">
                                <input type="text" id="username" name="username" class="form-control" required
                                    placeholder="<?php echo Lang::T('Usernames');?>
">
                            </div>
                            <div class="form-group col-sm-2" align="center">
                                <button class="btn btn-success btn-block" id="sendBtn" type="submit" name="send"
                                    onclick="return ask(this, '<?php echo Lang::T(" Are You Sure?");?>
')" value="plan"><i
                                        class="glyphicon glyphicon-send"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php }?>
        <br>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['disable_voucher'] != 'yes') {?>
            <div class="box box-primary box-solid mb30">
                <div class="box-header">
                    <h3 class="box-title"><?php echo Lang::T('Voucher Activation');?>
</h3>
                </div>
                <div class="box-body">
                    <form method="post" role="form" class="form-horizontal" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
voucher/activation-post">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <a class="btn btn-default" href="<?php echo APP_URL;?>
/scan/?back=<?php echo urlencode($_smarty_tpl->tpl_vars['_url']->value);
echo urlencode("
                                    home&code=");?>
"><i class="glyphicon glyphicon-qrcode"></i></a>
                            </span>
                            <input type="text" id="code" name="code" class="form-control"
                                placeholder="<?php echo Lang::T('Enter voucher code here');?>
" value="<?php echo $_smarty_tpl->tpl_vars['code']->value;?>
">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit"><?php echo Lang::T('Recharge');?>
</button>
                            </span>
                        </div>
                    </form>
                </div>
                <div class="box-body">
                    <div class="btn-group btn-group-justified" role="group">
                        <a class="btn btn-default" href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
voucher/activation">
                            <i class="ion ion-ios-cart"></i>
                            <?php echo Lang::T('Order Voucher');?>

                        </a>
                        <?php if ($_smarty_tpl->tpl_vars['_c']->value['payment_gateway'] != 'none' || $_smarty_tpl->tpl_vars['_c']->value['payment_gateway'] == '') {?>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
order/package" class="btn btn-default">
                                <i class="ion ion-ios-cart"></i>
                                <?php echo Lang::T('Order Package');?>

                            </a>
                        <?php }?>
                    </div>
                </div>
            </div>
        <?php }?>
    </div>
</div>
<?php if ((isset($_smarty_tpl->tpl_vars['hostname']->value)) && $_smarty_tpl->tpl_vars['hchap']->value == 'true' && $_smarty_tpl->tpl_vars['_c']->value['hs_auth_method'] == 'hchap') {?>
    <?php echo '<script'; ?>
 type="text/javascript" src="/ui/ui/scripts/md5.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type="text/javascript">
        var hostname = "http://<?php echo $_smarty_tpl->tpl_vars['hostname']->value;?>
/login";
        var user = "<?php echo $_smarty_tpl->tpl_vars['_user']->value['username'];?>
";
        var pass = "<?php echo $_smarty_tpl->tpl_vars['_user']->value['password'];?>
";
        var dst = "<?php echo $_smarty_tpl->tpl_vars['apkurl']->value;?>
";
        var authdly = "2";
        var key = hexMD5('<?php echo $_smarty_tpl->tpl_vars['key1']->value;?>
' + pass + '<?php echo $_smarty_tpl->tpl_vars['key2']->value;?>
');
        var auth = hostname + '?username=' + user + '&dst=' + dst + '&password=' + key;
        document.write('<meta http-equiv="refresh" target="_blank" content="' + authdly + '; url=' + auth + '">');
    <?php echo '</script'; ?>
>
<?php }
$_smarty_tpl->_subTemplateRender("file:customer/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
