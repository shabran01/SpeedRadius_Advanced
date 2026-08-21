<?php
/* Smarty version 4.5.3, created on 2026-08-21 13:41:15
  from '/var/www/html/isp/ui/ui/customer/error.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_6a882b4b01fce8_06086635',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b80316da0894aa079052238c6c377cc73ff0493e' => 
    array (
      0 => '/var/www/html/isp/ui/ui/customer/error.tpl',
      1 => 1764497753,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6a882b4b01fce8_06086635 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo ucwords(Lang::T("Error"));?>
 - <?php echo $_smarty_tpl->tpl_vars['_c']->value['CompanyName'];?>
</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="ui/ui/images/logo.png" type="image/x-icon" />
    <link rel="stylesheet" href="ui/ui/styles/bootstrap.min.css">
    <link rel="stylesheet" href="ui/ui/styles/modern-AdminLTE.min.css">
</head>

<body class="hold-transition lockscreen">
    <div class="lockscreen-wrapper">
        <div class="panel panel-danger">
            <div class="panel-heading"><?php echo ucwords(Lang::T("Internal Error"));?>
</div>
            <div class="panel-body">
                <?php echo Lang::T("Sorry, the software failed to process the request, if it still happening, please tell");?>

                <?php echo $_smarty_tpl->tpl_vars['_c']->value['CompanyName'];?>

            </div>
            <div class="panel-footer">
                <a href="<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
" id="button" class="btn btn-danger btn-block"><?php echo Lang::T('Try Again');?>
</a>
            </div>
        </div>
        <div class="lockscreen-footer text-center">
            <?php echo $_smarty_tpl->tpl_vars['_c']->value['CompanyName'];?>

        </div>
    </div>

    <?php if ($_smarty_tpl->tpl_vars['_c']->value['tawkto'] != '') {?>
        <!--Start of Tawk.to Script-->
        <?php echo '<script'; ?>
 type="text/javascript">
            var Tawk_API = Tawk_API || {},
                Tawk_LoadStart = new Date();
            (function() {
                var s1 = document.createElement("script"),
                    s0 = document.getElementsByTagName("script")[0];
                s1.async = true;
                s1.src='https://embed.tawk.to/<?php echo $_smarty_tpl->tpl_vars['_c']->value['tawkto'];?>
';
                s1.charset = 'UTF-8';
                s1.setAttribute('crossorigin', '*');
                s0.parentNode.insertBefore(s1, s0);
            })();
        <?php echo '</script'; ?>
>
        <!--End of Tawk.to Script-->
    <?php }?>

</body>

</html><?php }
}
