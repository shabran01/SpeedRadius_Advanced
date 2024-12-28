<?php
/* Smarty version 4.5.3, created on 2024-12-23 06:54:29
  from '/var/www/html/snootylique/ui/ui/customer/profile.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_6768def5928007_86181964',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd51ecc8419f61839ad20e654db7cc1ab58695d63' => 
    array (
      0 => '/var/www/html/snootylique/ui/ui/customer/profile.tpl',
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
function content_6768def5928007_86181964 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:customer/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<!-- user-profile -->

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-primary panel-hovered panel-stacked mb30">
            <div class="panel-heading"><?php echo Lang::T('Data Change');?>
</div>
            <div class="panel-body">
                <form class="form-horizontal" enctype="multipart/form-data" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
accounts/edit-profile-post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
                    <input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['_user']->value['id'];?>
">
                    <center>
                        <img src="<?php echo $_smarty_tpl->tpl_vars['UPLOAD_PATH']->value;
echo $_smarty_tpl->tpl_vars['_user']->value['photo'];?>
.thumb.jpg" width="200"
                            onerror="this.src='<?php echo $_smarty_tpl->tpl_vars['UPLOAD_PATH']->value;?>
/user.default.jpg'" class="img-circle img-responsive"
                            alt="Foto" onclick="return deletePhoto(<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
)">
                    </center><br>
                    <div class="form-group">
                        <label class="col-md-3 col-xs-12 control-label"><?php echo Lang::T('Photo');?>
</label>
                        <div class="col-md-6 col-xs-8">
                            <input type="file" class="form-control" name="photo" accept="image/*">
                        </div>
                        <div class="form-group col-md-3 col-xs-4" title="Not always Working">
                            <label class=""><input type="checkbox" checked name="faceDetect" value="yes"> <?php echo Lang::T('Face Detect');?>
</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Usernames');?>
</label>
                        <div class="col-md-9">
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
                                <input type="text" class="form-control" name="username" id="username" readonly
                                    value="<?php echo $_smarty_tpl->tpl_vars['_user']->value['username'];?>
"
                                    placeholder="<?php if ($_smarty_tpl->tpl_vars['_c']->value['country_code_phone'] != '' || $_smarty_tpl->tpl_vars['_c']->value['registration_username'] == 'phone') {
echo $_smarty_tpl->tpl_vars['_c']->value['country_code_phone'];?>
 <?php echo Lang::T('Phone Number');
} elseif ($_smarty_tpl->tpl_vars['_c']->value['registration_username'] == 'email') {
echo Lang::T('Email');
} else {
echo Lang::T('Username');
}?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Full Name');?>
</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="fullname" name="fullname"
                                value="<?php echo $_smarty_tpl->tpl_vars['_user']->value['fullname'];?>
">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Home Address');?>
</label>
                        <div class="col-md-9">
                            <textarea name="address" id="address" class="form-control"><?php echo $_smarty_tpl->tpl_vars['_user']->value['address'];?>
</textarea>
                        </div>
                    </div>
                    <?php if ($_smarty_tpl->tpl_vars['_c']->value['allow_phone_otp'] != 'yes') {?>
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?php echo Lang::T('Phone Number');?>
</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1"><i class="glyphicon glyphicon-phone-alt"></i></span>
                                    <input type="text" class="form-control" name="phonenumber" id="phonenumber"
                                        value="<?php echo $_smarty_tpl->tpl_vars['_user']->value['phonenumber'];?>
"
                                        placeholder="<?php if ($_smarty_tpl->tpl_vars['_c']->value['country_code_phone'] != '') {
echo $_smarty_tpl->tpl_vars['_c']->value['country_code_phone'];
}?> <?php echo Lang::T('Phone Number');?>
">
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?php echo Lang::T('Phone Number');?>
</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1"><i class="glyphicon glyphicon-phone-alt"></i></span>
                                    <input type="text" class="form-control" name="phonenumber" id="phonenumber"
                                        value="<?php echo $_smarty_tpl->tpl_vars['_user']->value['phonenumber'];?>
" readonly
                                        placeholder="<?php if ($_smarty_tpl->tpl_vars['_c']->value['country_code_phone'] != '') {
echo $_smarty_tpl->tpl_vars['_c']->value['country_code_phone'];
}?> <?php echo Lang::T('Phone Number');?>
">
                                    <span class="input-group-btn">
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
accounts/phone-update" type="button"
                                            class="btn btn-info btn-flat"><?php echo Lang::T('Change');?>
</a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['_c']->value['allow_email_otp'] != 'yes') {?>
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?php echo Lang::T('Email Address');?>
</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="email" name="email" value="<?php echo $_smarty_tpl->tpl_vars['_user']->value['email'];?>
">
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="form-group">
                            <label class="col-md-3 control-label"><?php echo Lang::T('Email Address');?>
</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                    <input type="text" class="form-control" name="email" id="email"
                                        value="<?php echo $_smarty_tpl->tpl_vars['_user']->value['email'];?>
" readonly>
                                    <span class="input-group-btn">
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
accounts/email-update" type="button"
                                            class="btn btn-info btn-flat"><?php echo Lang::T('Change');?>
</a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php }?>
                    <?php echo $_smarty_tpl->tpl_vars['customFields']->value;?>

                    <div class="form-group">
                        <div class="col-md-offset-3 col-md-9">
                            <button class="btn btn-success btn-block" type="submit">
                            <?php echo Lang::T('Save Changes');?>
</button>
                            <br>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
home" class="btn btn-link btn-block"><?php echo Lang::T('Cancel');?>
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
