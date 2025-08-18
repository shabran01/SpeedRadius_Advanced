<?php
/* Smarty version 4.5.3, created on 2025-08-17 10:05:15
  from '/var/www/html/ISP/ui/ui/app-settings.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a17f2beb7ed0_40909321',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ef877c071b1ce73579c537badb616f0ec575532a' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/app-settings.tpl',
      1 => 1755413491,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:sections/header.tpl' => 1,
    'file:sections/footer.tpl' => 1,
  ),
),false)) {
function content_68a17f2beb7ed0_40909321 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<style>
    .panel-title {
        font-weight: bolder;
        font-size: large;
    }
    
    /* Direct fix for collapsed panels */
    .panel-collapse {
        display: block !important;
        height: auto !important;
        visibility: visible !important;
    }
    
    /* Make each section more distinguishable */
    .panel {
        margin-bottom: 25px;
        border: 1px solid #e7e7e7;
    }
    
    .panel-heading {
        background-color: #f8f8f8 !important;
        cursor: pointer;
    }
    
    .panel-heading:hover {
        background-color: #f0f0f0 !important;
    }
    
    .panel-body {
        padding: 15px;
        display: block !important;
    }
</style>
<?php echo '<script'; ?>
>
    // Immediately executed function to fix panels
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            // Convert all collapsible panels to visible panels
            var allPanels = document.querySelectorAll('.panel-collapse');
            for(var i = 0; i < allPanels.length; i++) {
                allPanels[i].classList.remove('collapse');
                allPanels[i].classList.remove('panel-collapse');
                allPanels[i].classList.add('panel-body-visible');
                allPanels[i].style.display = 'block';
                allPanels[i].style.height = 'auto';
                allPanels[i].style.visibility = 'visible';
            }
            
            // Replace any existing refresh buttons with our compact style
            setTimeout(function() {
                var refreshButtons = document.querySelectorAll('button[style*="position: fixed"]');
                for(var i = 0; i < refreshButtons.length; i++) {
                    var btn = refreshButtons[i];
                    btn.className = 'compact-refresh-btn';
                    btn.textContent = 'Refresh';
                }
            }, 500);
        });
    })();
<?php echo '</script'; ?>
>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel-heading" role="tab" id="General">
            <h3 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseGeneral"
                    aria-expanded="true" aria-controls="collapseGeneral">
                    <?php echo Lang::T('General');?>

                </a>
            </h3>
        </div>
        <div id="collapseGeneral" class="panel-body-visible" role="tabpanel" aria-labelledby="General">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Application Name / Company
                        Name');?>
</label>
                    <div class="col-md-6">
                        <input type="text" required class="form-control" id="CompanyName" name="CompanyName"
                            value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['CompanyName'];?>
">
                    </div>
                    <span class="help-block col-md-4"><?php echo Lang::T('This Name will be shown on the
                        Title');?>
</span>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Company Logo');?>
</label>
                    <div class="col-md-6">
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                        <span class="help-block"><?php echo Lang::T('For PDF Reports | Best size 1078 x 200 |
                            uploaded image
                            will be
                            autosize');?>
</span>
                    </div>
                    <span class="help-block col-md-4">
                        <a href="./<?php echo $_smarty_tpl->tpl_vars['logo']->value;?>
" target="_blank"><img src="./<?php echo $_smarty_tpl->tpl_vars['logo']->value;?>
" height="48" alt="logo for PDF"></a>
                    </span>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Company Footer');?>
</label>
                    <div class="col-md-6">
                        <input type="text" required class="form-control" id="CompanyFooter" name="CompanyFooter"
                            value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['CompanyFooter'];?>
">
                    </div>
                    <span class="help-block col-md-4"><?php echo Lang::T('Will show below user pages');?>
</span>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Address');?>
</label>
                    <div class="col-md-6">
                        <textarea class="form-control" id="address" name="address"
                            rows="3"><?php echo Lang::htmlspecialchars($_smarty_tpl->tpl_vars['_c']->value['address']);?>
</textarea>
                    </div>
                    <span class="help-block col-md-4"><?php echo Lang::T('You can use html tag');?>
</span>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Phone Number');?>
</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['phone'];?>
">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Invoice Footer');?>
</label>
                    <div class="col-md-6">
                        <textarea class="form-control" id="note" name="note"
                            rows="3"><?php echo Lang::htmlspecialchars($_smarty_tpl->tpl_vars['_c']->value['note']);?>
</textarea>
                        <span class="help-block"><?php echo Lang::T('You can use html tag');?>
</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><i class="glyphicon glyphicon-print"></i>
                        <?php echo Lang::T('Print Max Char');?>
</label>
                    <div class="col-md-6">
                        <input type="number" required class="form-control" id="printer_cols" placeholder="37"
                            name="printer_cols" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['printer_cols'];?>
">
                    </div>
                    <span class="help-block col-md-4"><?php echo Lang::T('For invoice print using Thermal
                        Printer');?>
</span>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Theme');?>
</label>
                    <div class="col-md-6">
                        <select name="theme" id="theme" class="form-control">
                            <option value="default" <?php if ($_smarty_tpl->tpl_vars['_c']->value['theme'] == 'default') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('Default');?>

                            </option>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['themes']->value, 'theme');
$_smarty_tpl->tpl_vars['theme']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['theme']->value) {
$_smarty_tpl->tpl_vars['theme']->do_else = false;
?>
                                <option value="<?php echo $_smarty_tpl->tpl_vars['theme']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['_c']->value['theme'] == $_smarty_tpl->tpl_vars['theme']->value) {?>selected="selected" <?php }?>>
                                    <?php echo Lang::ucWords($_smarty_tpl->tpl_vars['theme']->value);?>
</option>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </select>
                    </div>
                    <p class="help-block col-md-4"><a href="https://github.com/hotspotbilling/phpnuxbill/wiki/Themes"
                            target="_blank"><?php echo Lang::T('Theme Info');?>
</a></p>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Theme Settings');?>
</label>
                    <div class="col-md-6">
                        <div class="theme-settings">
                            <div class="setting-item">
                                <label><?php echo Lang::T('Select Theme');?>
</label>
                                <select class="form-control" id="theme-selector" name="selected_theme">
                                    <option value="default"><?php echo Lang::T('Default Theme');?>
</option>
                                    <option value="modern-light"><?php echo Lang::T('Modern Light');?>
</option>
                                    <option value="modern-dark"><?php echo Lang::T('Modern Dark');?>
</option>
                                </select>
                            </div>
                            <div class="setting-item">
                                <label><?php echo Lang::T('Font Family');?>
</label>
                                <select class="form-control" id="font-selector" name="selected_font">
                                    <option value="Roboto">Roboto</option>
                                    <option value="Open Sans">Open Sans</option>
                                    <option value="Nunito">Nunito</option>
                                    <option value="Poppins">Poppins</option>
                                </select>
                            </div>
                            <div class="setting-item">
                                <label><?php echo Lang::T('Text Size');?>
</label>
                                <select class="form-control" id="size-selector" name="text_size">
                                    <option value="compact"><?php echo Lang::T('Compact');?>
</option>
                                    <option value="normal"><?php echo Lang::T('Normal');?>
</option>
                                    <option value="comfortable"><?php echo Lang::T('Comfortable');?>
</option>
                                </select>
                            </div>
                            <div class="setting-item colors-section" id="theme-colors" style="display: none;">
                                <label><?php echo Lang::T('Theme Colors');?>
</label>
                                <div class="color-pickers">
                                    <div class="color-picker">
                                        <label><?php echo Lang::T('Primary Color');?>
</label>
                                        <input type="color" name="primary_color" class="form-control">
                                    </div>
                                    <div class="color-picker">
                                        <label><?php echo Lang::T('Secondary Color');?>
</label>
                                        <input type="color" name="secondary_color" class="form-control">
                                    </div>
                                    <div class="color-picker">
                                        <label><?php echo Lang::T('Success Color');?>
</label>
                                        <input type="color" name="success_color" class="form-control">
                                    </div>
                                    <div class="color-picker">
                                        <label><?php echo Lang::T('Info Color');?>
</label>
                                        <input type="color" name="info_color" class="form-control">
                                    </div>
                                    <div class="color-picker">
                                        <label><?php echo Lang::T('Warning Color');?>
</label>
                                        <input type="color" name="warning_color" class="form-control">
                                    </div>
                                    <div class="color-picker">
                                        <label><?php echo Lang::T('Danger Color');?>
</label>
                                        <input type="color" name="danger_color" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <span class="help-block col-md-4"><?php echo Lang::T('Customize the appearance of your application');?>
</span>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Recharge Using');?>
</label>
                    <div class="col-md-6">
                        <input type="text" name="payment_usings" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['payment_usings'];?>
"
                            placeholder="<?php echo Lang::T('Cash');?>
, <?php echo Lang::T('Bank Transfer');?>
">
                    </div>
                    <p class="help-block col-md-4">
                        <?php echo Lang::T('This used for admin to select payment in recharge, using comma for every new options');?>

                    </p>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Income reset date');?>
</label>
                    <div class="col-md-6">
                        <input type="number" required class="form-control" id="reset_day" placeholder="20" min="1"
                            max="28" step="1" name="reset_day" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['reset_day'];?>
">
                    </div>
                    <span class="help-block col-md-4"><?php echo Lang::T('Income will reset every this
                        day');?>
</span>
                </div>
                <button class="btn btn-success btn-block" name="general" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>

        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="HideDashboardContent">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseHideDashboardContent" aria-expanded="false"
                    aria-controls="collapseHideDashboardContent">
                    <?php echo Lang::T('Hide Dashboard Content');?>

                </a>
            </h4>
        </div>
        <div id="collapseHideDashboardContent" class="panel-body-visible" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-3 control-label"><input type="checkbox" name="hide_mrc" value="yes" <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_mrc'] == 'yes') {?>checked<?php }?>>
                        <?php echo Lang::T('Monthly Registered Customers');?>
</label>
                    <label class="col-md-2 control-label"><input type="checkbox" name="hide_tms" value="yes" <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_tms'] == 'yes') {?>checked<?php }?>> <?php echo Lang::T('Total Monthly Sales');?>
</label>
                    <label class="col-md-2 control-label"><input type="checkbox" name="hide_aui" value="yes" <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_aui'] == 'yes') {?>checked<?php }?>> <?php echo Lang::T('All Users Insights');?>
</label>
                    <label class="col-md-2 control-label"><input type="checkbox" name="hide_al" value="yes" <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_al'] == 'yes') {?>checked<?php }?>> <?php echo Lang::T('Activity Log');?>
</label>
                    <label class="col-md-2 control-label"><input type="checkbox" name="hide_uet" value="yes" <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_uet'] == 'yes') {?>checked<?php }?>> <?php echo Lang::T('User Expired, Today');?>
</label>
                    <label class="col-md-2 control-label"><input type="checkbox" name="hide_vs" value="yes" <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_vs'] == 'yes') {?>checked<?php }?>> Vouchers Stock</label>
                    <label class="col-md-2 control-label"><input type="checkbox" name="hide_pg" value="yes" <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_pg'] == 'yes') {?>checked<?php }?>> Payment Gateway</label>
                </div>
                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="Registration">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseRegistration" aria-expanded="false" aria-controls="collapseRegistration">
                    <?php echo Lang::T('Registration');?>

                </a>
            </h4>
        </div>
        <div id="collapseRegistration" class="panel-body-visible" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Allow Registration');?>
</label>
                    <div class="col-md-6">
                        <select name="disable_registration" id="disable_registration" class="form-control">
                            <option value="no" <?php if ($_smarty_tpl->tpl_vars['_c']->value['disable_registration'] == 'no') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('Yes');?>

                            </option>
                            <?php if ($_smarty_tpl->tpl_vars['_c']->value['disable_voucher'] != 'yes') {?>
                                <option value="yes" <?php if ($_smarty_tpl->tpl_vars['_c']->value['disable_registration'] == 'yes') {?>selected="selected" <?php }?>>
                                    <?php echo Lang::T('Voucher Only');?>

                                </option>
                            <?php }?>
                            <option value="noreg" <?php if ($_smarty_tpl->tpl_vars['_c']->value['disable_registration'] == 'noreg') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('No Registration');?>

                            </option>
                        </select>
                    </div>
                    <p class="help-block col-md-4">
                        <?php echo Lang::T('Customer just Login with Phone number and Voucher Code, Voucher will be password');?>

                    </p>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Registration Username');?>
</label>
                    <div class="col-md-6">
                        <select name="registration_username" id="voucher_format" class="form-control">
                            <option value="username" <?php if ($_smarty_tpl->tpl_vars['_c']->value['registration_username'] == 'username') {?>selected="selected"
                                <?php }?>>Username
                            </option>
                            <option value="email" <?php if ($_smarty_tpl->tpl_vars['_c']->value['registration_username'] == 'email') {?>selected="selected" <?php }?>>
                                Email
                            </option>
                            <option value="phone" <?php if ($_smarty_tpl->tpl_vars['_c']->value['registration_username'] == 'phone') {?>selected="selected" <?php }?>>
                                Phone Number
                            </option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('SMS OTP Registration');?>
</label>
                    <div class="col-md-6">
                        <select name="sms_otp_registration" id="sms_otp_registration" class="form-control">
                            <option value="no">
                                <?php echo Lang::T('No');?>

                            </option>
                            <option value="yes" <?php if ($_smarty_tpl->tpl_vars['_c']->value['sms_otp_registration'] == 'yes') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('Yes');?>

                            </option>
                        </select>
                    </div>
                    <p class="help-block col-md-4">
                        <?php echo Lang::T('Customer Registration need to validate using OTP');?>

                    </p>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('OTP Method');?>
</label>
                    <div class="col-md-6">
                        <select name="phone_otp_type" id="phone_otp_type" class="form-control">
                            <option value="sms" <?php if ($_smarty_tpl->tpl_vars['_c']->value['phone_otp_type'] == 'sms') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('By SMS');?>
</option>
                            <option value="whatsapp" <?php if ($_smarty_tpl->tpl_vars['_c']->value['phone_otp_type'] == 'whatsapp') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('by WhatsApp');?>
</option>
                            <option value="both" <?php if ($_smarty_tpl->tpl_vars['_c']->value['phone_otp_type'] == 'both') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('By WhatsApp and SMS');?>

                            </option>
                        </select>
                    </div>
                    <p class="help-block col-md-4"><?php echo Lang::T('The method which OTP will be sent to user');?>
<br>
                        <?php echo Lang::T('For Registration and Update Phone Number');?>
</p>
                </div>
                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>


<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="Security">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseSecurity" aria-expanded="false" aria-controls="collapseSecurity">
                    <?php echo Lang::T('Security');?>

                </a>
            </h4>
        </div>
        <div id="collapseSecurity" class="panel-body-visible" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo Lang::T('Enable Session Timeout');?>
</label>
                    <div class="col-md-5">
                        <label class="switch">
                            <input type="checkbox" id="enable_session_timeout" value="1"
                                name="enable_session_timeout" <?php if ($_smarty_tpl->tpl_vars['_c']->value['enable_session_timeout'] == 1) {?>checked<?php }?>>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <p class="help-block col-md-4">
                        <?php echo Lang::T('Logout Admin if not Available/Online a period of time');?>
</p>
                </div>
                <div class="form-group" id="timeout_duration_input" style="display: none;">
                    <label class="col-md-3 control-label"><?php echo Lang::T('Timeout Duration');?>
</label>
                    <div class="col-md-5">
                        <input type="number" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['session_timeout_duration'];?>
" class="form-control"
                            name="session_timeout_duration" id="session_timeout_duration"
                            placeholder="<?php echo Lang::T('Enter the session timeout duration (minutes)');?>
" min="1">
                    </div>
                    <p class="help-block col-md-4"><?php echo Lang::T('Idle Timeout, Logout Admin if Idle for xx
                            minutes');?>

                    </p>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo Lang::T('Single Admin Session');?>
</label>
                    <div class="col-md-5">
                        <select name="single_session" id="single_session" class="form-control">
                            <option value="no">
                                <?php echo Lang::T('No');?>
</option>
                            <option value="yes" <?php if ($_smarty_tpl->tpl_vars['_c']->value['single_session'] == 'yes') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('Yes');?>

                            </option>
                        </select>
                    </div>
                    <p class="help-block col-md-4">
                        <?php echo Lang::T('Admin can only have single session login, it will logout another session');?>

                    </p>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo Lang::T('Enable CSRF Validation');?>
</label>
                    <div class="col-md-5">
                        <select name="csrf_enabled" id="csrf_enabled" class="form-control">
                            <option value="no">
                                <?php echo Lang::T('No');?>
</option>
                            <option value="yes" <?php if ($_smarty_tpl->tpl_vars['_c']->value['csrf_enabled'] == 'yes') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('Yes');?>

                            </option>
                        </select>
                    </div>
                    <p class="help-block col-md-4">
                        <a href="https://en.wikipedia.org/wiki/Cross-site_request_forgery" target="_blank"><?php echo Lang::T('Cross-site request forgery');?>
</a>
                    </p>
                </div>
                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="Voucher">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseVoucher" aria-expanded="false" aria-controls="collapseVoucher">
                    Voucher
                </a>
            </h4>
        </div>
        <div id="collapseVoucher" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Disable Voucher');?>
</label>
                    <div class="col-md-6">
                        <select name="disable_voucher" id="disable_voucher" class="form-control">
                            <option value="no" <?php if ($_smarty_tpl->tpl_vars['_c']->value['disable_voucher'] == 'no') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('No');?>

                            </option>
                            <option value="yes" <?php if ($_smarty_tpl->tpl_vars['_c']->value['disable_voucher'] == 'yes') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('Yes');?>

                            </option>
                        </select>
                    </div>
                    <p class="help-block col-md-4"><?php echo Lang::T('Voucher activation menu will be hidden');?>
</p>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Voucher Format');?>
</label>
                    <div class="col-md-6">
                        <select name="voucher_format" id="voucher_format" class="form-control">
                            <option value="up" <?php if ($_smarty_tpl->tpl_vars['_c']->value['voucher_format'] == 'up') {?>selected="selected" <?php }?>>UPPERCASE
                            </option>
                            <option value="low" <?php if ($_smarty_tpl->tpl_vars['_c']->value['voucher_format'] == 'low') {?>selected="selected" <?php }?>>
                                lowercase
                            </option>
                            <option value="rand" <?php if ($_smarty_tpl->tpl_vars['_c']->value['voucher_format'] == 'rand') {?>selected="selected" <?php }?>>
                                RaNdoM
                            </option>
                            <option value="numbers" <?php if ($_smarty_tpl->tpl_vars['_c']->value['voucher_format'] == 'numbers') {?>selected="selected" <?php }?>>
                                Numbers
                            </option>
                        </select>
                    </div>
                    <p class="help-block col-md-4">UPPERCASE lowercase RaNdoM</p>
                </div>
                <?php if ($_smarty_tpl->tpl_vars['_c']->value['disable_voucher'] != 'yes') {?>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo Lang::T('Redirect URL after Activation');?>
</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="voucher_redirect" name="voucher_redirect"
                                placeholder="https://192.168.88.1/status" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['voucher_redirect'];?>
">
                        </div>
                        <p class="help-block col-md-4">
                            <?php echo Lang::T('After Customer activate voucher or login, customer will be redirected to this
                        url');?>

                        </p>
                    </div>
                <?php }?>
                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="FreeRadius">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseFreeRadius" aria-expanded="false" aria-controls="collapseFreeRadius">
                    FreeRadius
                </a>
            </h4>
        </div>
        <div id="collapseFreeRadius" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Enable Radius');?>
</label>
                    <div class="col-md-6">
                        <select name="radius_enable" id="radius_enable" class="form-control text-muted">
                            <option value="0"><?php echo Lang::T('No');?>
</option>
                            <option value="1" <?php if ($_smarty_tpl->tpl_vars['_c']->value['radius_enable']) {?>selected="selected" <?php }?>><?php echo Lang::T('Yes');?>

                            </option>
                        </select>
                    </div>
                    <p class="help-block col-md-4"><a
                            href="https://github.com/hotspotbilling/phpnuxbill/wiki/FreeRadius"
                            target="_blank"><?php echo Lang::T('Radius Instructions');?>
</a></p>
                </div>
                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="ExtendPostpaidExpiration">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseExtendPostpaidExpiration" aria-expanded="false"
                    aria-controls="collapseExtendPostpaidExpiration">
                    <?php echo Lang::T('Extend Postpaid Expiration');?>

                </a>
            </h4>
        </div>
        <div id="collapseExtendPostpaidExpiration" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Allow Extend');?>
</label>
                    <div class="col-md-6">
                        <select name="extend_expired" id="extend_expired" class="form-control text-muted">
                            <option value="0"><?php echo Lang::T('No');?>
</option>
                            <option value="1" <?php if ($_smarty_tpl->tpl_vars['_c']->value['extend_expired'] == 1) {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('Yes');?>
</option>
                        </select>
                    </div>
                    <p class="help-block col-md-4"><?php echo Lang::T('Customer can request to extend expirations');?>
</p>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Extend Days');?>
</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="extend_days" placeholder="3"
                            value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['extend_days'];?>
">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Confirmation Message');?>
</label>
                    <div class="col-md-6">
                        <textarea type="text" rows="4" class="form-control" name="extend_confirmation"
                            placeholder="<?php echo Lang::T('i agree to extends and will paid full after this');?>
"><?php echo $_smarty_tpl->tpl_vars['_c']->value['extend_confirmation'];?>
</textarea>
                    </div>
                </div>
                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="CustomerBalanceSystem">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseCustomerBalanceSystem" aria-expanded="false"
                    aria-controls="collapseCustomerBalanceSystem">
                    <?php echo Lang::T('Customer Balance System');?>

                </a>
            </h4>
        </div>
        <div id="collapseCustomerBalanceSystem" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Enable System');?>
</label>
                    <div class="col-md-6">
                        <select name="enable_balance" id="enable_balance" class="form-control">
                            <option value="no" <?php if ($_smarty_tpl->tpl_vars['_c']->value['enable_balance'] == 'no') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('No');?>

                            </option>
                            <option value="yes" <?php if ($_smarty_tpl->tpl_vars['_c']->value['enable_balance'] == 'yes') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('Yes');?>

                            </option>
                        </select>
                    </div>
                    <p class="help-block col-md-4"><?php echo Lang::T('Customer can deposit money to buy voucher');?>
</p>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Allow Transfer');?>
</label>
                    <div class="col-md-6">
                        <select name="allow_balance_transfer" id="allow_balance_transfer" class="form-control">
                            <option value="no" <?php if ($_smarty_tpl->tpl_vars['_c']->value['allow_balance_transfer'] == 'no') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('No');?>
</option>
                            <option value="yes" <?php if ($_smarty_tpl->tpl_vars['_c']->value['allow_balance_transfer'] == 'yes') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('Yes');?>
</option>
                        </select>
                    </div>
                    <p class="help-block col-md-4"><?php echo Lang::T('Allow balance transfer between customers');?>
</p>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Minimum Balance Transfer');?>
</label>
                    <div class="col-md-6">
                        <input type="number" class="form-control" id="minimum_transfer" name="minimum_transfer"
                            value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['minimum_transfer'];?>
">
                    </div>
                </div>
                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="TelegramNotification">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseTelegramNotification" aria-expanded="false"
                    aria-controls="collapseTelegramNotification">
                    <?php echo Lang::T('Telegram Notification');?>

                    <div class="btn-group pull-right">
                        <a class="btn btn-success btn-xs" style="color: black;" href="javascript:testTg()">Test TG</a>
                    </div>
                </a>
            </h4>
        </div>
        <div id="collapseTelegramNotification" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Telegram Bot Token');?>
</label>
                    <div class="col-md-6">
                        <input type="password" class="form-control" id="telegram_bot" name="telegram_bot"
                            onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'"
                            value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['telegram_bot'];?>
" placeholder="123456:asdasgdkuasghddlashdashldhalskdklasd">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Telegram User/Channel/Group ID');?>
</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="telegram_target_id" name="telegram_target_id"
                            value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['telegram_target_id'];?>
" placeholder="12345678">
                    </div>
                </div>
                <small id="emailHelp" class="form-text text-muted">
                    <?php echo Lang::T('You will get Payment and Error notification');?>

                </small>
                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="SMSNotification">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseSMSNotification" aria-expanded="false" aria-controls="collapseSMSNotification">
                    <?php echo Lang::T('SMS Notification');?>

                    <div class="btn-group pull-right">
                        <a class="btn btn-success btn-xs" style="color: black;" href="javascript:testSms()">
                            <?php echo Lang::T('Test SMS');?>

                        </a>
                    </div>
                </a>
            </h4>
        </div>
        <div id="collapseSMSNotification" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('SMS Gateway');?>
</label>
                    <div class="col-md-6">
                        <select name="active_sms_gateway" id="active_sms_gateway" class="form-control">
                            <option value="blessed_texts" <?php if ($_smarty_tpl->tpl_vars['_c']->value['active_sms_gateway'] == 'blessed_texts') {?>selected="selected"<?php }?>>
                                Blessed Texts
                            </option>
                            <option value="talksasa" <?php if ($_smarty_tpl->tpl_vars['_c']->value['active_sms_gateway'] == 'talksasa') {?>selected="selected"<?php }?>>
                                Talk Sasa
                            </option>
                            <option value="bytewave" <?php if ($_smarty_tpl->tpl_vars['_c']->value['active_sms_gateway'] == 'bytewave') {?>selected="selected"<?php }?>>
                                BytewaveSMS
                            </option>
                            <option value="zettatel" <?php if ($_smarty_tpl->tpl_vars['_c']->value['active_sms_gateway'] == 'zettatel') {?>selected="selected"<?php }?>>
                                Zettatel
                            </option>
                        </select>
                    </div>
                    <p class="help-block col-md-4"><?php echo Lang::T('Select which SMS gateway to use');?>
</p>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('SMS Server URL');?>
</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="sms_url" name="sms_url" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['sms_url'];?>
"
                            placeholder="https://domain/?param_number=[number]&param_text=[text]&secret=">
                    </div>
                    <p class="help-block col-md-4"><?php echo Lang::T('Must include');?>
 <b>[text]</b> &amp; <b>[number]</b>,
                        <?php echo Lang::T('it will be replaced.');?>

                    </p>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Or use Mikrotik SMS');?>
</label>
                    <div class="col-md-6">
                        <select class="form-control" onchange="document.getElementById('sms_url').value = this.value">
                            <option value=""><?php echo Lang::T('Select Router');?>
</option>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['r']->value, 'rs');
$_smarty_tpl->tpl_vars['rs']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['rs']->value) {
$_smarty_tpl->tpl_vars['rs']->do_else = false;
?>
                                <option value="<?php echo $_smarty_tpl->tpl_vars['rs']->value['name'];?>
" <?php if ($_smarty_tpl->tpl_vars['rs']->value['name'] == $_smarty_tpl->tpl_vars['_c']->value['sms_url']) {?>selected<?php }?>>
                                    <?php echo $_smarty_tpl->tpl_vars['rs']->value['name'];?>
</option>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </select>
                    </div>
                    <p class="help-block col-md-4"><?php echo Lang::T('Must include');?>
 <b>[text]</b> &amp; <b>[number]</b>,
                        <?php echo Lang::T('it will be replaced.');?>

                    </p>
                </div>
                <small id="emailHelp" class="form-text text-muted"><?php echo Lang::T('You can use');?>
 WhatsApp
                    <?php echo Lang::T('in here too.');?>
 <a href="https://wa.nux.my.id/login" target="_blank"><?php echo Lang::T('Free
                        Server');?>
</a></small>

                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="WhatsappNotification">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseWhatsappNotification" aria-expanded="false"
                    aria-controls="collapseWhatsappNotification">
                    <?php echo Lang::T('Whatsapp Notification');?>

                    <div class="btn-group pull-right">
                        <a class="btn btn-success btn-xs" style="color: black;" href="javascript:testWa()">Test WA</a>
                    </div>
                </a>
            </h4>
        </div>
        <div id="collapseWhatsappNotification" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('WhatsApp Server URL');?>
</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="wa_url" name="wa_url" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['wa_url'];?>
"
                            placeholder="https://domain/?param_number=[number]&param_text=[text]&secret=">
                    </div>
                    <p class="help-block col-md-4"><?php echo Lang::T('Must include');?>
 <b>[text]</b> &amp; <b>[number]</b>,
                        <?php echo Lang::T('it will be replaced.');?>
</p>
                </div>
                <small id="emailHelp" class="form-text text-muted"><?php echo Lang::T('You can use');?>
 WhatsApp
                    <?php echo Lang::T('in here too.');?>
 <a href="https://wa.nux.my.id/login" target="_blank"><?php echo Lang::T('Free
                        Server');?>
</a></small>
                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="EmailNotification">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseEmailNotification" aria-expanded="false" aria-controls="collapseEmailNotification">
                    <?php echo Lang::T('Email Notification');?>

                    <div class="btn-group pull-right">
                        <a class="btn btn-success btn-xs" style="color: black;" href="javascript:testEmail()">Test
                            Email</a>
                    </div>
                </a>
            </h4>
        </div>
        <div id="collapseEmailNotification" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label">SMTP Host : Port</label>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="smtp_host" name="smtp_host"
                            value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['smtp_host'];?>
" placeholder="smtp.host.tld">
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" id="smtp_port" name="smtp_port"
                            value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['smtp_port'];?>
" placeholder="465 587 port">
                    </div>
                    <p class="help-block col-md-4"><?php echo Lang::T('Empty this to use internal mail() PHP');?>
</p>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('SMTP Username');?>
</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="smtp_user" name="smtp_user"
                            value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['smtp_user'];?>
" placeholder="user@host.tld">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('SMTP Password');?>
</label>
                    <div class="col-md-6">
                        <input type="password" class="form-control" id="smtp_pass" name="smtp_pass"
                            value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['smtp_pass'];?>
" onmouseleave="this.type = 'password'"
                            onmouseenter="this.type = 'text'">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('SMTP Security');?>
</label>
                    <div class="col-md-6">
                        <select name="smtp_ssltls" id="smtp_ssltls" class="form-control">
                            <option value="" <?php if ($_smarty_tpl->tpl_vars['_c']->value['smtp_ssltls'] == '') {?>selected="selected" <?php }?>>Not Secure
                            </option>
                            <option value="ssl" <?php if ($_smarty_tpl->tpl_vars['_c']->value['smtp_ssltls'] == 'ssl') {?>selected="selected" <?php }?>>SSL
                            </option>
                            <option value="tls" <?php if ($_smarty_tpl->tpl_vars['_c']->value['smtp_ssltls'] == 'tls') {?>selected="selected" <?php }?>>TLS
                            </option>
                        </select>
                    </div>
                    <p class="help-block col-md-4">UPPERCASE lowercase RaNdoM</p>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Mail <?php echo Lang::T('From');?>
</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="mail_from" name="mail_from"
                            value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['mail_from'];?>
" placeholder="noreply@host.tld">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Mail Reply To');?>
</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="mail_reply_to" name="mail_reply_to"
                            value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['mail_reply_to'];?>
" placeholder="support@host.tld">
                    </div>
                    <p class="help-block col-md-4">
                        <?php echo Lang::T('Customer will reply email to this address, empty if you want to use From
                        Address');?>

                    </p>
                </div>

                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="UserNotification">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseUserNotification" aria-expanded="false" aria-controls="collapseUserNotification">
                    <?php echo Lang::T('User Notification');?>

                </a>
            </h4>
        </div>
        <div id="collapseUserNotification" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Expired Notification');?>
</label>
                    <div class="col-md-6">
                        <select class="form-control" id="expired_notification" name="expired_notification">
                            <option value="none"><?php echo Lang::T('None');?>
</option>
                            <option value="sms" <?php if ($_smarty_tpl->tpl_vars['_c']->value['expired_notification'] == 'sms') {?>selected="selected"<?php }?>><?php echo Lang::T('By SMS');?>
</option>
                            <option value="wa" <?php if ($_smarty_tpl->tpl_vars['_c']->value['expired_notification'] == 'wa') {?>selected="selected"<?php }?>><?php echo Lang::T('By WhatsApp');?>
</option>
                            <option value="both" <?php if ($_smarty_tpl->tpl_vars['_c']->value['expired_notification'] == 'both') {?>selected="selected"<?php }?>><?php echo Lang::T('By SMS & WhatsApp');?>
</option>
                        </select>
                    </div>
                    <span class="help-block col-md-4"><?php echo Lang::T('User will get notification when package expired');?>
</span>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Payment Notification');?>
</label>
                    <div class="col-md-6">
                        <select class="form-control" id="payment_notification" name="payment_notification">
                            <option value="none"><?php echo Lang::T('None');?>
</option>
                            <option value="sms" <?php if ($_smarty_tpl->tpl_vars['_c']->value['payment_notification'] == 'sms') {?>selected="selected"<?php }?>><?php echo Lang::T('By SMS');?>
</option>
                            <option value="wa" <?php if ($_smarty_tpl->tpl_vars['_c']->value['payment_notification'] == 'wa') {?>selected="selected"<?php }?>><?php echo Lang::T('By WhatsApp');?>
</option>
                            <option value="both" <?php if ($_smarty_tpl->tpl_vars['_c']->value['payment_notification'] == 'both') {?>selected="selected"<?php }?>><?php echo Lang::T('By SMS & WhatsApp');?>
</option>
                        </select>
                    </div>
                    <span class="help-block col-md-4"><?php echo Lang::T('User will get invoice notification when buy package or package refilled');?>
</span>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Reminder Notification');?>
</label>
                    <div class="col-md-6">
                        <select class="form-control" id="reminder_notification" name="reminder_notification">
                            <option value="none"><?php echo Lang::T('None');?>
</option>
                            <option value="sms" <?php if ($_smarty_tpl->tpl_vars['_c']->value['reminder_notification'] == 'sms') {?>selected="selected"<?php }?>><?php echo Lang::T('By SMS');?>
</option>
                            <option value="wa" <?php if ($_smarty_tpl->tpl_vars['_c']->value['reminder_notification'] == 'wa') {?>selected="selected"<?php }?>><?php echo Lang::T('By WhatsApp');?>
</option>
                            <option value="both" <?php if ($_smarty_tpl->tpl_vars['_c']->value['reminder_notification'] == 'both') {?>selected="selected"<?php }?>><?php echo Lang::T('By SMS & WhatsApp');?>
</option>
                        </select>
                    </div>
                    <span class="help-block col-md-4"><?php echo Lang::T('User will get reminder notification before package expires');?>
</span>
                </div>
                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="TawkToChatWidget">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseTawkToChatWidget" aria-expanded="false" aria-controls="collapseTawkToChatWidget">
                    <?php echo Lang::T('Tawk.to Chat Widget');?>

                </a>
            </h4>
        </div>
        <div id="collapseTawkToChatWidget" class="panel-collapse collapse" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label">https://tawk.to/chat/</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="tawkto" name="tawkto" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['tawkto'];?>
"
                            placeholder="62f1ca7037898912e961f5/1ga07df">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label">Tawk.to Javascript API key</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="tawkto_api_key" name="tawkto_api_key" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['tawkto_api_key'];?>
"
                            placeholder="39e52264cxxxxxxxxxxxxxdd078af5342e8">
                    </div>
                </div>
                <label class="col-md-2"></label>
                <p class="col-md-6 help-block">/ip hotspot walled-garden<br>
                    add dst-host=tawk.to<br>
                    add dst-host=*.tawk.to</p>
                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="APIKey">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseAPIKey" aria-expanded="false" aria-controls="collapseAPIKey">
                    API Key
                </a>
            </h4>
        </div>
        <div id="collapseAPIKey" class="panel-body-visible" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Access Token');?>
</label>
                    <div class="col-md-6">
                        <input type="password" class="form-control" id="api_key" name="api_key" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['api_key'];?>
"
                            placeholder="<?php echo Lang::T('Empty this to randomly created API key');?>
"
                            onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'">
                    </div>
                    <p class="col-md-4 help-block"><?php echo Lang::T('This Token will act as SuperAdmin/Admin');?>
</p>
                </div>
                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="Proxy">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseProxy"
                    aria-expanded="false" aria-controls="collapseProxy">
                    <?php echo Lang::T('Proxy');?>

                </a>
            </h4>
        </div>
        <div id="collapseProxy" class="panel-body-visible" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Proxy Server');?>
</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="http_proxy" name="http_proxy"
                            value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['http_proxy'];?>
" placeholder="127.0.0.1:3128">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Proxy Server Login');?>
</label>
                    <div class="col-md-6">
                        <input type="password" class="form-control" id="http_proxyauth" name="http_proxyauth"
                            autocomplete="off" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['http_proxyauth'];?>
" placeholder="username:password"
                            onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'">
                    </div>
                </div>
                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="TaxSystem">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseTaxSystem" aria-expanded="false" aria-controls="collapseTaxSystem">
                    <?php echo Lang::T('Tax System');?>

                </a>
            </h4>
        </div>
        <div id="collapseTaxSystem" class="panel-body-visible" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Enable Tax System');?>
</label>
                    <div class="col-md-6">
                        <select name="enable_tax" id="enable_tax" class="form-control">
                            <option value="no" <?php if ($_smarty_tpl->tpl_vars['_c']->value['enable_tax'] == 'no') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('No');?>

                            </option>
                            <option value="yes" <?php if ($_smarty_tpl->tpl_vars['_c']->value['enable_tax'] == 'yes') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('Yes');?>

                            </option>
                        </select>
                    </div>
                    <p class="help-block col-md-4"><?php echo Lang::T('Tax will be calculated in Internet Plan Price');?>
</p>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Tax Rate');?>
</label>
                    <div class="col-md-6">
                        <select name="tax_rate" id="tax_rate" class="form-control">
                            <option value="0.5" <?php if ($_smarty_tpl->tpl_vars['_c']->value['tax_rate'] == '0.5') {?>selected="selected" <?php }?>>
                                0.5
                            </option>
                            <option value="1" <?php if ($_smarty_tpl->tpl_vars['_c']->value['tax_rate'] == '1') {?>selected="selected" <?php }?>>
                                1
                            </option>
                            <option value="1.5" <?php if ($_smarty_tpl->tpl_vars['_c']->value['tax_rate'] == '1.5') {?>selected="selected" <?php }?>>
                                1.5
                            </option>
                            <option value="2" <?php if ($_smarty_tpl->tpl_vars['_c']->value['tax_rate'] == '2') {?>selected="selected" <?php }?>>
                                2
                            </option>
                            <option value="5" <?php if ($_smarty_tpl->tpl_vars['_c']->value['tax_rate'] == '5') {?>selected="selected" <?php }?>>
                                5
                            </option>
                            <option value="10" <?php if ($_smarty_tpl->tpl_vars['_c']->value['tax_rate'] == '10') {?>selected="selected" <?php }?>>
                                10
                            </option>
                            <!-- Custom tax rate option -->
                            <option value="custom" <?php if ($_smarty_tpl->tpl_vars['_c']->value['tax_rate'] == 'custom') {?>selected="selected" <?php }?>>
                                <?php echo Lang::T('Custome');?>
</option>
                        </select>
                    </div>
                    <p class="help-block col-md-4"><?php echo Lang::T('Tax Rates by percentage');?>
</p>
                </div>
                <!-- Custom tax rate input field (initially hidden) -->
                <div class="form-group" id="customTaxRate" style="display: none;">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Custome Tax Rate');?>
</label>
                    <div class="col-md-6">
                        <input type="text" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['custom_tax_rate'];?>
" class="form-control" name="custom_tax_rate"
                            id="custom_tax_rate" placeholder="<?php echo Lang::T('Enter Custome Tax Rate');?>
">
                    </div>
                    <p class="help-block col-md-4"><?php echo Lang::T('Enter the custom tax rate (e.g., 3.75 for 3.75%)');?>
</p>
                </div>

                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app-post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="panel">
        <div class="panel-heading" role="tab" id="GithubAuthentication">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                    href="#collapseAuthentication" aria-expanded="false" aria-controls="collapseAuthentication">
                    Github <?php echo Lang::T('Authentication');?>

                </a>
            </h4>
        </div>
        <div id="collapseAuthentication" class="panel-body-visible" role="tabpanel">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Github Username');?>
</label>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-addon">https://github.com/</span>
                            <input type="text" class="form-control" id="github_username" name="github_username"
                                value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['github_username'];?>
" placeholder="ibnux">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo Lang::T('Github Token');?>
</label>
                    <div class="col-md-6">
                        <input type="password" class="form-control" id="github_token" name="github_token"
                            value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['github_token'];?>
" placeholder="ghp_........"
                            onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'">
                    </div>
                    <span class="help-block col-md-4"><a href="https://github.com/settings/tokens/new"
                            target="_blank"><?php echo Lang::T('Create GitHub personal access token');?>
 (classic)</a>,
                        <?php echo Lang::T('only need repo
                        scope');?>
</span>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-offset-2 col-md-8" style="text-align: left;"><?php echo Lang::T('This
                        will allow
                        you to download plugin from private/paid repository');?>
</label>
                </div>
                <button class="btn btn-success btn-block" type="submit">
                    <?php echo Lang::T('Save Changes');?>

                </button>
            </div>
        </div>
    </div>
</form>

<div class="bs-callout bs-callout-info" id="callout-navbar-role">
    <h4><b><?php echo Lang::T('Settings For Mikrotik');?>
</b></h4>
    <p>/ip hotspot walled-garden <br>
        add dst-host=<?php echo $_smarty_tpl->tpl_vars['_domain']->value;?>
 <br>
        add dst-host=*.<?php echo $_smarty_tpl->tpl_vars['_domain']->value;?>

    </p>
    <br>
    <h4><b><?php echo Lang::T('Settings For Cron Expired');?>
</b></h4>
    <p>
        # <?php echo Lang::T('Expired Cronjob Every 5 Minutes [Recommended]');?>
<br>
        */5 * * * * cd <?php echo $_smarty_tpl->tpl_vars['dir']->value;?>
 && <?php echo $_smarty_tpl->tpl_vars['php']->value;?>
 cron.php
        <br><br>
        # <?php echo Lang::T('Expired Cronjob Every 1 Hour');?>
<br>
        0 * * * * cd <?php echo $_smarty_tpl->tpl_vars['dir']->value;?>
 && <?php echo $_smarty_tpl->tpl_vars['php']->value;?>
 cron.php
    </p>
    <br>
    <h4><b><?php echo Lang::T('Settings For Cron Reminder');?>
</b></h4>
    <p>
        # <?php echo Lang::T('Reminder Cronjob Every 7 AM');?>
<br>
        0 7 * * * cd <?php echo $_smarty_tpl->tpl_vars['dir']->value;?>
 && <?php echo $_smarty_tpl->tpl_vars['php']->value;?>
 cron_reminder.php
    </p>
</div>

<?php echo '<script'; ?>
>
    function testWa() {
        var target = prompt("Phone number\nSave First before Test", "");
        if (target != null) {
            window.location.href = '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app&testWa=' + target;
        }
    }

    function testSms() {
        var target = prompt("Phone number\nSave First before Test", "");
        if (target != null) {
            window.location.href = '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app&testSms=' + target;
        }
    }


    function testEmail() {
        var target = prompt("Email\nSave First before Test", "");
        if (target != null) {
            window.location.href = '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app&testEmail=' + target;
        }
    }

    function testTg() {
        window.location.href = '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
settings/app&testTg=test';
    }
<?php echo '</script'; ?>
>


<?php echo '<script'; ?>
>
    document.addEventListener('DOMContentLoaded', function () {
        var sectionTimeoutCheckbox = document.getElementById('enable_session_timeout');
        var timeoutDurationInput = document.getElementById('timeout_duration_input');
        var timeoutDurationField = document.getElementById('session_timeout_duration');

        if (sectionTimeoutCheckbox.checked) {
            timeoutDurationInput.style.display = 'block';
            timeoutDurationField.required = true;
        }

        sectionTimeoutCheckbox.addEventListener('change', function () {
            if (this.checked) {
                timeoutDurationInput.style.display = 'block';
                timeoutDurationField.required = true;
            } else {
                timeoutDurationInput.style.display = 'none';
                timeoutDurationField.required = false;
            }
        });

        document.querySelector('form').addEventListener('submit', function (event) {
            if (sectionTimeoutCheckbox.checked && (!timeoutDurationField.value || isNaN(
                timeoutDurationField.value))) {
                event.preventDefault();
                alert('Please enter a valid session timeout duration.');
                timeoutDurationField.focus();
            }
        });
    });
<?php echo '</script'; ?>
>

<?php echo '<script'; ?>
>
    document.addEventListener("DOMContentLoaded", function() {
        // Function to toggle visibility of custom tax rate input field
        function toggleCustomTaxRate() {
            var taxRateSelect = document.getElementById("tax_rate");
            var customTaxRateInput = document.getElementById("customTaxRate");

            if (taxRateSelect.value === "custom") {
                customTaxRateInput.style.display = "block";
            } else {
                customTaxRateInput.style.display = "none";
            }
        }

        // Call the function when the page loads
        toggleCustomTaxRate();

        // Call the function whenever the tax rate dropdown value changes
        document.getElementById("tax_rate").addEventListener("change", toggleCustomTaxRate);
    });
<?php echo '</script'; ?>
>
<?php echo '<script'; ?>
>
// Direct fix for blank panels
$(document).ready(function() {
    // Remove all panel-collapse and collapse classes to force panels to be visible
    $('.panel-collapse').removeClass('panel-collapse collapse').addClass('panel-body');
    
    // Handle clicking on panel headers for smooth UX
    $('.panel-heading').click(function() {
        var target = $(this).attr('id');
        $('html, body').animate({
            scrollTop: $('#' + target).offset().top - 100
        }, 500);
    });

    // Create navigation tabs at the top for easier navigation
    var tabNav = '<ul class="nav nav-tabs" style="margin-bottom: 20px;">';
    $('.panel-title a').each(function(index) {
        var tabText = $(this).text().trim();
        var tabTarget = $(this).attr('href');
        var activeClass = index === 0 ? 'class="active"' : '';
        tabNav += '<li ' + activeClass + '><a href="' + tabTarget + '">' + tabText + '</a></li>';
    });
    tabNav += '</ul>';
    $('.content-header').after(tabNav);
    
    // Make the tab navigation functional
    $('.nav-tabs a').click(function(e) {
        e.preventDefault();
        var target = $($(this).attr('href'));
        $('html, body').animate({
            scrollTop: target.offset().top - 100
        }, 500);
        // Highlight the clicked tab
        $('.nav-tabs li').removeClass('active');
        $(this).parent().addClass('active');
    });
});
<?php echo '</script'; ?>
>
<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
