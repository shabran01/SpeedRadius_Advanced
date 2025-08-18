<?php
/* Smarty version 4.5.3, created on 2025-08-17 10:05:04
  from '/var/www/html/ISP/ui/ui/admin_server_stats.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a17f20b03476_19221610',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '09c3bc3244e600badb091bb72dc519713b8ba072' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/admin_server_stats.tpl',
      1 => 1754916133,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a17f20b03476_19221610 (Smarty_Internal_Template $_smarty_tpl) {
if (in_array($_smarty_tpl->tpl_vars['_admin']->value['user_type'],array('SuperAdmin','Admin'))) {?>
<div class="row">
    <!-- CPU Usage -->
    <div class="col-md-4">
        <div class="small-box" style="background: linear-gradient(135deg, #4158D0 0%, #C850C0 100%); color: white;">
            <div class="inner">
                <h4 class="text-bold mb-0" style="font-size: 24px; font-family: 'Segoe UI', sans-serif;">
                    <span class="amount"><?php echo $_smarty_tpl->tpl_vars['server_stats']->value['cpu']['load_1min'];?>
%</span>
                </h4>
                <p style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px;"><?php echo Lang::T('CPU Load');?>
</p>
                <div class="progress" style="height: 4px; margin-bottom: 4px; background: rgba(255,255,255,0.2);">
                    <div class="progress-bar" style="width: <?php echo $_smarty_tpl->tpl_vars['server_stats']->value['cpu']['load_1min'];?>
%; background: rgba(255,255,255,0.8);"></div>
                </div>
                <small style="color: rgba(255,255,255,0.9); font-size: 11px;">
                    1min: <?php echo $_smarty_tpl->tpl_vars['server_stats']->value['cpu']['load_1min'];?>
% | 5min: <?php echo $_smarty_tpl->tpl_vars['server_stats']->value['cpu']['load_5min'];?>
% | 15min: <?php echo $_smarty_tpl->tpl_vars['server_stats']->value['cpu']['load_15min'];?>
%
                </small>
            </div>
            <div class="icon" style="color: rgba(255,255,255,0.2);">
                <i class="fas fa-microchip"></i>
            </div>
        </div>
    </div>
    
    <!-- Memory Usage -->
    <div class="col-md-4">
        <div class="small-box" style="background: linear-gradient(135deg, #43CBFF 0%, #9708CC 100%); color: white;">
            <div class="inner">
                <h4 class="text-bold mb-0" style="font-size: 24px; font-family: 'Segoe UI', sans-serif;">
                    <span class="amount"><?php echo $_smarty_tpl->tpl_vars['server_stats']->value['memory']['percentage'];?>
%</span>
                </h4>
                <p style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px;"><?php echo Lang::T('Memory Usage');?>
</p>
                <div class="progress" style="height: 4px; margin-bottom: 4px; background: rgba(255,255,255,0.2);">
                    <div class="progress-bar" style="width: <?php echo $_smarty_tpl->tpl_vars['server_stats']->value['memory']['percentage'];?>
%; background: rgba(255,255,255,0.8);"></div>
                </div>
                <small style="color: rgba(255,255,255,0.9); font-size: 11px;">
                    <?php echo Lang::T('Used');?>
: <?php echo $_smarty_tpl->tpl_vars['server_stats']->value['memory']['used'];?>
MB | <?php echo Lang::T('Free');?>
: <?php echo $_smarty_tpl->tpl_vars['server_stats']->value['memory']['free'];?>
MB
                </small>
            </div>
            <div class="icon" style="color: rgba(255,255,255,0.2);">
                <i class="fas fa-memory"></i>
            </div>
        </div>
    </div>
    
    <!-- Disk Usage -->
    <div class="col-md-4">
        <div class="small-box" style="background: linear-gradient(135deg, #00B4DB 0%, #0083B0 100%); color: white;">
            <div class="inner">
                <h4 class="text-bold mb-0" style="font-size: 24px; font-family: 'Segoe UI', sans-serif;">
                    <span class="amount"><?php echo $_smarty_tpl->tpl_vars['server_stats']->value['disk']['percentage'];?>
%</span>
                </h4>
                <p style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px;"><?php echo Lang::T('Disk Usage');?>
</p>
                <div class="progress" style="height: 4px; margin-bottom: 4px; background: rgba(255,255,255,0.2);">
                    <div class="progress-bar" style="width: <?php echo $_smarty_tpl->tpl_vars['server_stats']->value['disk']['percentage'];?>
%; background: rgba(255,255,255,0.8);"></div>
                </div>
                <small style="color: rgba(255,255,255,0.9); font-size: 11px;">
                    <?php echo Lang::T('Used');?>
: <?php echo $_smarty_tpl->tpl_vars['server_stats']->value['disk']['used'];?>
GB | <?php echo Lang::T('Free');?>
: <?php echo $_smarty_tpl->tpl_vars['server_stats']->value['disk']['free'];?>
GB
                </small>
            </div>
            <div class="icon" style="color: rgba(255,255,255,0.2);">
                <i class="fas fa-hdd"></i>
            </div>
        </div>
    </div>
</div>
<?php }
}
}
