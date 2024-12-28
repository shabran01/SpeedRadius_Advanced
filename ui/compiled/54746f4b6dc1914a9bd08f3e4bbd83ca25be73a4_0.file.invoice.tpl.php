<?php
/* Smarty version 4.5.3, created on 2024-12-23 06:56:01
  from '/var/www/html/snootylique/ui/ui/invoice.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_6768df51190ac0_60927862',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '54746f4b6dc1914a9bd08f3e4bbd83ca25be73a4' => 
    array (
      0 => '/var/www/html/snootylique/ui/ui/invoice.tpl',
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
function content_6768df51190ac0_60927862 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="row">
    <div class="col-md-6 col-sm-12 col-md-offset-3">
        <div class="panel panel-hovered panel-primary panel-stacked mb30">
            <div class="panel-heading"><?php echo $_smarty_tpl->tpl_vars['in']->value['invoice'];?>
</div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/print" target="_blank">
                    <pre id="content" style="text-align: center;"></pre>
                    <textarea class="hidden" id="formcontent" name="content"><?php echo $_smarty_tpl->tpl_vars['invoice']->value;?>
</textarea>
                    <input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['in']->value['id'];?>
">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/list" class="btn btn-default btn-sm"><i
                            class="ion-reply-all"></i><?php echo Lang::T('Finish');?>
</a>
                    <a href="https://api.whatsapp.com/send/?text=<?php echo $_smarty_tpl->tpl_vars['whatsapp']->value;?>
" target="_blank"
                        class="btn btn-primary btn-sm">
                        <i class="glyphicon glyphicon-share"></i> WhatsApp</a>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/view/<?php echo $_smarty_tpl->tpl_vars['in']->value['id'];?>
/send" class="btn btn-info text-black btn-sm"><i
                            class="glyphicon glyphicon-envelope"></i> <?php echo Lang::T("Resend");?>
</a>
                    <hr>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/print/<?php echo $_smarty_tpl->tpl_vars['in']->value['id'];?>
" target="_print" class="btn btn-info text-black btn-sm"><i
                            class="glyphicon glyphicon-print"></i>
                        <?php echo Lang::T('Print');?>
 HTML</a>
                    <button type="submit" class="btn btn-info text-black btn-sm"><i
                            class="glyphicon glyphicon-print"></i>
                        <?php echo Lang::T('Print');?>
 Text</button>
                    <a href="nux://print?text=<?php echo urlencode($_smarty_tpl->tpl_vars['invoice']->value);?>
"
                        class="btn btn-success text-black btn-sm hidden-md hidden-lg">
                        <i class="glyphicon glyphicon-phone"></i>
                        NuxPrint
                    </a>
                    <a href="https://github.com/hotspotbilling/android-printer"
                        class="btn btn-success text-black btn-sm hidden-xs hidden-sm" target="_blank">
                        <i class="glyphicon glyphicon-phone"></i>
                        NuxPrint
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo '<script'; ?>
 type="text/javascript">
    var s5_taf_parent = window.location;
    document.getElementById('content').innerHTML = document.getElementById('formcontent').innerHTML;
<?php echo '</script'; ?>
>
<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
