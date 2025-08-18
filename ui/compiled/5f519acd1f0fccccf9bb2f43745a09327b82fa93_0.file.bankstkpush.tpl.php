<?php
/* Smarty version 4.5.3, created on 2025-08-18 17:09:43
  from '/var/www/html/ISP/system/paymentgateway/ui/bankstkpush.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a33427609724_67612475',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5f519acd1f0fccccf9bb2f43745a09327b82fa93' => 
    array (
      0 => '/var/www/html/ISP/system/paymentgateway/ui/bankstkpush.tpl',
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
function content_68a33427609724_67612475 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<form class="form-horizontal" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
paymentgateway/BankStkPush">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">Fill the details below to complete the bank STK Push</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Enter Bank account number</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="kopokopo_app_key" name="account" placeholder="*************************" value="<?php echo $_smarty_tpl->tpl_vars['_c']->value['Stkbankacc'];?>
">
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <label class="col-md-2 control-label">Bank Name</label>
                        <div class="col-md-6">
                            <select class="form-control" name="bankname" id="bankstk">
                                <option value="Equity" <?php if ($_smarty_tpl->tpl_vars['_c']->value['Stkbankname'] == 'Equity') {?>selected<?php }?>>Equity Bank</option>
                                <option value="KCB" <?php if ($_smarty_tpl->tpl_vars['_c']->value['Stkbankname'] == 'KCB') {?>selected<?php }?>>Kenya Commercial Bank</option>
                                <option value="Coop" <?php if ($_smarty_tpl->tpl_vars['_c']->value['Stkbankname'] == 'Coop') {?>selected<?php }?>>Cooperative Bank of Kenya</option>
                                <option value="Absa" <?php if ($_smarty_tpl->tpl_vars['_c']->value['Stkbankname'] == 'Absa') {?>selected<?php }?>>Absa Bank Kenya</option>
                                <option value="DTB" <?php if ($_smarty_tpl->tpl_vars['_c']->value['Stkbankname'] == 'DTB') {?>selected<?php }?>>Diamond Trust Bank (DTB)</option>
                                <option value="NCBA" <?php if ($_smarty_tpl->tpl_vars['_c']->value['Stkbankname'] == 'NCBA') {?>selected<?php }?>>NCBA Bank</option>
                                <option value="GAB" <?php if ($_smarty_tpl->tpl_vars['_c']->value['Stkbankname'] == 'GAB') {?>selected<?php }?>>GAB Bank</option>
                                <option value="Speedcom" <?php if ($_smarty_tpl->tpl_vars['_c']->value['Stkbankname'] == 'Speedcom') {?>selected<?php }?>>Speedcom</option>
                                <option value="StanChart" <?php if ($_smarty_tpl->tpl_vars['_c']->value['Stkbankname'] == 'StanChart') {?>selected<?php }?>>Standard Chartered Bank</option>
                                <option value="I&M Bank Kenya" <?php if ($_smarty_tpl->tpl_vars['_c']->value['Stkbankname'] == 'I&M Bank Kenya') {?>selected<?php }?>>I&M Bank Kenya</option>
                                <option value="NCBA Loop" <?php if ($_smarty_tpl->tpl_vars['_c']->value['Stkbankname'] == 'NCBA Loop') {?>selected<?php }?>>NCBA Loop</option>
                                <option value="SasaPay" <?php if ($_smarty_tpl->tpl_vars['_c']->value['Stkbankname'] == 'SasaPay') {?>selected<?php }?>>SasaPay</option>
                                <option value="Family Bank" <?php if ($_smarty_tpl->tpl_vars['_c']->value['Stkbankname'] == 'Family Bank') {?>selected<?php }?>>Family Bank</option> <!-- Added Family Bank -->
                            </select>
                        </div>
                    </div>

                    <pre>After applying these changes, the funds shall be going to the saved bank account, please make sure the bank name and account match</pre>
                   
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">Save</button>
                        </div>
                    </div>
                        
                </div>
            </div>
        </div>
    </div>
</form>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
