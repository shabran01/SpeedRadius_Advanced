<?php
/* Smarty version 4.5.3, created on 2025-08-17 13:31:48
  from '/var/www/html/ISP/ui/ui/customer/login.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a1af947a2d47_18058523',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2715c1f5b0526de91da1da6e2177d0aca13e40ea' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/customer/login.tpl',
      1 => 1754916133,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:customer/header-public.tpl' => 1,
    'file:customer/footer-public.tpl' => 1,
  ),
),false)) {
function content_68a1af947a2d47_18058523 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:customer/header-public.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="hidden-xs" style="height:100px"></div>
<div class="row">
    <div class="col-sm-6 col-sm-offset-1">
        <div class="panel panel-info">
            <div class="panel-heading"><?php echo Lang::T('Announcement');?>
</div>
            <div class="panel-body">
                <?php $_smarty_tpl->_assignInScope('Announcement', ((string)$_smarty_tpl->tpl_vars['PAGES_PATH']->value)."/Announcement.html");?>
                <?php if (file_exists($_smarty_tpl->tpl_vars['Announcement']->value)) {?>
                    <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['Announcement']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                <?php }?>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-primary">
            <div class="panel-heading"><?php echo Lang::T('Log in to Member Panel');?>
</div>
            <div class="panel-body">
                <form action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
login/post" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
                    <div class="form-group">
                        <label>
                            <?php if ($_smarty_tpl->tpl_vars['_c']->value['registration_username'] == 'phone') {?>
                                <?php echo Lang::T('Phone Number');?>

                            <?php } elseif ($_smarty_tpl->tpl_vars['_c']->value['registration_username'] == 'email') {?>
                                <?php echo Lang::T('Email');?>

                            <?php } else { ?>
                                <?php echo Lang::T('Usernames');?>

                            <?php }?>
                        </label>
                        <div class="input-group">
                            <?php if ($_smarty_tpl->tpl_vars['_c']->value['registration_username'] == 'phone') {?>
                                <span class="input-group-addon" id="basic-addon1"><i
                                        class="glyphicon glyphicon-phone-alt"></i></span>
                            <?php } elseif ($_smarty_tpl->tpl_vars['_c']->value['registration_username'] == 'email') {?>
                                <span class="input-group-addon" id="basic-addon1"><i
                                        class="glyphicon glyphicon-envelope"></i></span>
                            <?php } else { ?>
                                <span class="input-group-addon" id="basic-addon1"><i
                                        class="glyphicon glyphicon-user"></i></span>
                            <?php }?>
                            <input type="text" class="form-control" name="username"
                                placeholder="<?php if ($_smarty_tpl->tpl_vars['_c']->value['country_code_phone'] != '' || $_smarty_tpl->tpl_vars['_c']->value['registration_username'] == 'phone') {
echo $_smarty_tpl->tpl_vars['_c']->value['country_code_phone'];?>
 <?php echo Lang::T('Phone Number');
} elseif ($_smarty_tpl->tpl_vars['_c']->value['registration_username'] == 'email') {
echo Lang::T('Email');
} else {
echo Lang::T('Usernames');
}?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php echo Lang::T('Password');?>
</label>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2"><i
                                    class="glyphicon glyphicon-lock"></i></span>
                            <input type="password" class="form-control" name="password"
                                placeholder="<?php echo Lang::T('Password');?>
">
                        </div>
                    </div>

                    <div class="clearfix hidden">
                        <div class="ui-checkbox ui-checkbox-primary right">
                            <label>
                                <input type="checkbox">
                                <span>Remember me</span>
                            </label>
                        </div>
                    </div>
                    <div class="btn-group btn-group-justified mb15">
                        <?php if ($_smarty_tpl->tpl_vars['_c']->value['disable_registration'] != 'noreg') {?>
                            <div class="btn-group">
                                <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
register" class="btn btn-success"><?php echo Lang::T('Register');?>
</a>
                            </div>
                        <?php }?>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary"><?php echo Lang::T('Login');?>
</button>
                        </div>
                    </div>
                    <br>
                    <center>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
forgot" class="btn btn-link"><?php echo Lang::T('Forgot Password');?>
</a>
                        <br>
                        <a href="javascript:showPrivacy()">Privacy</a>
                        &bull;
                        <a href="javascript:showTaC()">T &amp; C</a>
                    </center>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="HTMLModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="HTMLModal_konten"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">&times;</button>
            </div>
        </div>
    </div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:customer/footer-public.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
