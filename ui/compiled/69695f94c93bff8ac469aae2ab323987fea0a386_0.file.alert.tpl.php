<?php
/* Smarty version 4.5.3, created on 2024-12-23 06:53:17
  from '/var/www/html/snootylique/ui/ui/alert.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_6768dead31c278_24277043',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '69695f94c93bff8ac469aae2ab323987fea0a386' => 
    array (
      0 => '/var/www/html/snootylique/ui/ui/alert.tpl',
      1 => 1734662748,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6768dead31c278_24277043 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo ucwords(Lang::T($_smarty_tpl->tpl_vars['type']->value));?>
 - <?php echo $_smarty_tpl->tpl_vars['_c']->value['CompanyName'];?>
</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="ui/ui/images/logo.png" type="image/x-icon" />
    <link rel="stylesheet" href="ui/ui/styles/bootstrap.min.css">
    <link rel="stylesheet" href="ui/ui/styles/modern-AdminLTE.min.css">
    <meta http-equiv="refresh" content="<?php echo $_smarty_tpl->tpl_vars['time']->value;?>
; url=<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
">
</head>

<body class="hold-transition lockscreen">
    <div class="lockscreen-wrapper">
        <div class="panel panel-<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
">
            <div class="panel-heading"><?php echo ucwords(Lang::T($_smarty_tpl->tpl_vars['type']->value));?>
</div>
            <div class="panel-body">
                <?php echo $_smarty_tpl->tpl_vars['text']->value;?>

            </div>
            <div class="panel-footer">
                <a href="<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
" id="button" class="btn btn-<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
 btn-block btn-block"><?php echo Lang::T('Click Here');?>
 (<?php echo $_smarty_tpl->tpl_vars['time']->value;?>
)</a>
            </div>
        </div>
        <div class="lockscreen-footer text-center">
            <?php echo $_smarty_tpl->tpl_vars['_c']->value['CompanyName'];?>

        </div>
    </div>

    <?php echo '<script'; ?>
>
        var time = <?php echo $_smarty_tpl->tpl_vars['time']->value;?>
;
        timer();

        function timer() {
            setTimeout(() => {
                time--;
                if (time > -1) {
                    document.getElementById("button").innerHTML = "<?php echo Lang::T('Click Here');?>
 (" + time + ")";
                    timer();
                }
            }, 1000);
        }
    <?php echo '</script'; ?>
>
</body>

</html><?php }
}
