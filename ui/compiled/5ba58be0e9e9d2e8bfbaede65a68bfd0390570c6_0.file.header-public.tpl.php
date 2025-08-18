<?php
/* Smarty version 4.5.3, created on 2025-08-17 13:31:48
  from '/var/www/html/ISP/ui/ui/customer/header-public.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a1af947c97b8_44608530',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5ba58be0e9e9d2e8bfbaede65a68bfd0390570c6' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/customer/header-public.tpl',
      1 => 1754916133,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a1af947c97b8_44608530 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo $_smarty_tpl->tpl_vars['_title']->value;?>
 - <?php echo $_smarty_tpl->tpl_vars['_c']->value['CompanyName'];?>
</title>
    <link rel="shortcut icon" href="ui/ui/images/logo.png" type="image/x-icon" />

    <link rel="stylesheet" href="ui/ui/styles/bootstrap.min.css">
    <link rel="stylesheet" href="ui/ui/styles/modern-AdminLTE.min.css">
    <link rel="stylesheet" href="ui/ui/styles/sweetalert2.min.css" />
    <?php echo '<script'; ?>
 src="ui/ui/scripts/sweetalert2.all.min.js"><?php echo '</script'; ?>
>



</head>

<body id="app" class="app off-canvas body-full">
    <div class="container">
        <div class="form-head mb20">
            <h1 class="site-logo h2 mb5 mt5 text-center text-uppercase text-bold"
                style="text-shadow: 2px 2px 4px #757575;"><?php echo $_smarty_tpl->tpl_vars['_c']->value['CompanyName'];?>
</h1>
            <hr>
        </div>
        <?php if ((isset($_smarty_tpl->tpl_vars['notify']->value))) {?>
            <?php echo '<script'; ?>
>
                // Display SweetAlert toast notification
                Swal.fire({
                    icon: '<?php if ($_smarty_tpl->tpl_vars['notify_t']->value == "s") {?>success<?php } else { ?>warning<?php }?>',
                    title: '<?php echo $_smarty_tpl->tpl_vars['notify']->value;?>
',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            <?php echo '</script'; ?>
>
        <?php }
}
}
