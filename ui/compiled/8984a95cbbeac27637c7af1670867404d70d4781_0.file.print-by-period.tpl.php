<?php
/* Smarty version 4.5.3, created on 2026-08-21 13:44:31
  from '/var/www/html/isp/ui/ui/print-by-period.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_6a882c0f03e253_62315411',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8984a95cbbeac27637c7af1670867404d70d4781' => 
    array (
      0 => '/var/www/html/isp/ui/ui/print-by-period.tpl',
      1 => 1787309056,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6a882c0f03e253_62315411 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html>
<head>
    <title><?php echo $_smarty_tpl->tpl_vars['_title']->value;?>
 - <?php echo $_smarty_tpl->tpl_vars['_c']->value['CompanyName'];?>
</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="ui/ui/styles/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="ui/ui/images/favicon.ico">

    <style type="text/css">
        @media print
        {
            .no-print, .no-print *
            {
                display: none !important;
            }
            body { background: #fff !important; padding: 0 !important; }
            .report-card { box-shadow: none !important; border-radius: 0 !important; border: none !important; }
            .report-head, .rpt-table thead th, .type-pill, .total-box {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        body { background: #f1f5f9; font-family: 'Segoe UI', Tahoma, Arial, sans-serif; padding: 2rem 1rem; font-size: 1rem; }
        .report-wrap { max-width: 1400px; margin: 0 auto; }
        .report-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 10px 30px -12px rgba(15,23,42,.15); overflow: hidden; }
        .report-head { background: linear-gradient(135deg,#4f46e5,#4338ca); color: #fff; padding: 2rem 2.25rem; }
        .report-head h4 { margin: 0 0 .25rem 0; font-weight: 700; font-size: 1.75rem; }
        .report-head p { margin: 0; color: rgba(255,255,255,.9); font-size: 1.05rem; }
        .report-body { padding: 2rem 2.25rem; }
        .rpt-table { width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
        .rpt-table thead th { background: #f8fafc; color: #475569; font-weight: 700; font-size: .9rem; text-transform: uppercase; letter-spacing: .03em; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.1rem; text-align: left; white-space: nowrap; }
        .rpt-table tbody td { padding: .95rem 1.1rem; border-bottom: 1px solid #f1f5f9; font-size: 1.05rem; color: #334155; vertical-align: middle; }
        .rpt-table tbody tr:nth-child(even) td { background: #fafbfc; }
        .rpt-table tbody tr:last-child td { border-bottom: none; }
        .type-pill { display: inline-flex; padding: .2rem .65rem; border-radius: 9999px; font-size: .9rem; font-weight: 600; background: #eef2ff; color: #4338ca; }
        .total-box { margin: 2rem 0 0 auto; width: fit-content; background: linear-gradient(135deg,#ecfdf5,#d1fae5); border: 1px solid #a7f3d0; border-radius: 14px; padding: 1.25rem 2rem; text-align: right; }
        .total-box .lbl { font-size: .9rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #047857; }
        .total-box .val { font-size: 2.2rem; font-weight: 800; color: #065f46; }
        .print-btn { margin-top: 2rem; font-size: 1rem !important; padding: .7rem 1.4rem !important; }
    </style>
</head>

<body>
<div class="report-wrap">
    <div class="report-card">
        <div class="report-head">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
                <div>
                    <div style="font-size:1rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase; color:rgba(255,255,255,.7);"><?php echo $_smarty_tpl->tpl_vars['_c']->value['CompanyName'];?>
</div>
                    <h4><?php echo Lang::T('Period Report');?>
</h4>
                    <p><?php echo Lang::T('All Transactions at Date');?>
: <?php echo date($_smarty_tpl->tpl_vars['_c']->value['date_format'],strtotime($_smarty_tpl->tpl_vars['fdate']->value));?>
 &rarr; <?php echo date($_smarty_tpl->tpl_vars['_c']->value['date_format'],strtotime($_smarty_tpl->tpl_vars['tdate']->value));
if ($_smarty_tpl->tpl_vars['stype']->value) {?> &mdash; <?php echo $_smarty_tpl->tpl_vars['stype']->value;
}?></p>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:.9rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:rgba(255,255,255,.7);"><?php echo Lang::T('Total Income');?>
</div>
                    <div style="font-size:2rem; font-weight:800; color:#fff;"><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['dr']->value);?>
</div>
                </div>
            </div>
        </div>
        <div class="report-body">
            <div class="table-responsive">
                <table class="rpt-table">
                    <thead>
                        <tr>
                            <th><?php echo Lang::T('Username');?>
</th>
                            <th><?php echo Lang::T('Plan Name');?>
</th>
                            <th><?php echo Lang::T('Type');?>
</th>
                            <th class="text-right"><?php echo Lang::T('Plan Price');?>
</th>
                            <th><?php echo Lang::T('Created On');?>
</th>
                            <th><?php echo Lang::T('Expires On');?>
</th>
                            <th><?php echo Lang::T('Method');?>
</th>
                            <th><?php echo Lang::T('Routers');?>
</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['d']->value, 'ds');
$_smarty_tpl->tpl_vars['ds']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ds']->value) {
$_smarty_tpl->tpl_vars['ds']->do_else = false;
?>
                        <tr>
                            <td style="font-weight:600;"><?php echo $_smarty_tpl->tpl_vars['ds']->value['username'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['plan_name'];?>
</td>
                            <td><span class="type-pill"><?php echo $_smarty_tpl->tpl_vars['ds']->value['type'];?>
</span></td>
                            <td class="text-right" style="font-weight:600; color:#047857;"><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['ds']->value['price']);?>
</td>
                            <td><?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['ds']->value['recharged_on'],$_smarty_tpl->tpl_vars['ds']->value['recharged_time']);?>
</td>
                            <td><?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['ds']->value['expiration'],$_smarty_tpl->tpl_vars['ds']->value['time']);?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['method'];?>
</td>
                            <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['routers'];?>
</td>
                        </tr>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </tbody>
                </table>
            </div>
            <div class="total-box">
                <div class="lbl"><?php echo Lang::T('Total Income');?>
</div>
                <div class="val"><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['dr']->value);?>
</div>
            </div>
            <button type="button" id="actprint" class="btn btn-primary no-print print-btn"><?php echo Lang::T('Click Here to Print');?>
</button>
        </div>
    </div>
</div>
<?php echo '<script'; ?>
 src="ui/ui/scripts/jquery.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="ui/ui/scripts/bootstrap.min.js"><?php echo '</script'; ?>
>
<?php if ((isset($_smarty_tpl->tpl_vars['xfooter']->value))) {?>
    <?php echo $_smarty_tpl->tpl_vars['xfooter']->value;?>

<?php }
echo '<script'; ?>
>
    jQuery(document).ready(function() {
        // initiate layout and plugins
        $("#actprint").click(function() {
            window.print();
            return false;
        });
    });
<?php echo '</script'; ?>
>

</body>
</html><?php }
}
