<?php
/* Smarty version 4.5.3, created on 2026-08-21 11:20:55
  from '/var/www/html/isp/ui/ui/dashboard.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_6a880a67c91294_85216367',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd4f61a575bfef54c40c4ef818b36052920f18b80' => 
    array (
      0 => '/var/www/html/isp/ui/ui/dashboard.tpl',
      1 => 1785960852,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:sections/header.tpl' => 1,
    'file:admin_server_stats.tpl' => 1,
    'file:pagination.tpl' => 1,
    'file:sections/footer.tpl' => 1,
  ),
),false)) {
function content_6a880a67c91294_85216367 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/html/isp/system/vendor/smarty/smarty/libs/plugins/modifier.date_format.php','function'=>'smarty_modifier_date_format',),1=>array('file'=>'/var/www/html/isp/system/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),));
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="greeting-box mb-3">
    <div class="greeting-content">
        <h3 id="timeBasedGreeting" class="text-gradient"></h3>
        <div class="greeting-right">
            <div class="live-clock" id="liveClock">--:--:--</div>
            <div class="greeting-icon">
                <i id="weatherIcon" class="ion"></i>
            </div>
        </div>
    </div>
</div>

<?php echo '<script'; ?>
>
function updateGreeting() {
    const hour = new Date().getHours();
    let greeting = '';
    let iconClass = '';
    
    if (hour >= 5 && hour < 12) {
        greeting = 'Good Morning';
        iconClass = 'ion-ios-sunny';
        emoji = '☀️';
    } else if (hour >= 12 && hour < 17) {
        greeting = 'Good Afternoon';
        iconClass = 'ion-ios-sunny-outline';
        emoji = '🌤️';
    } else if (hour >= 17 && hour < 20) {
        greeting = 'Good Evening';
        iconClass = 'ion-ios-moon-outline';
        emoji = '🌅';
    } else {
        greeting = 'Good Night';
        iconClass = 'ion-ios-moon';
        emoji = '🌙';
    }
    
    const options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
    const dateStr = new Date().toLocaleDateString('en-US', options);
    
    document.getElementById('timeBasedGreeting').textContent = greeting + ' ' + emoji + ', ' + '<?php echo $_smarty_tpl->tpl_vars['_c']->value['CompanyName'];?>
' + ' — ' + dateStr;
    document.getElementById('weatherIcon').className = 'ion ' + iconClass;
}

function updateClock() {
    const now = new Date();
    let h = now.getHours();
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    const hh = String(h).padStart(2, '0');
    const mm = String(now.getMinutes()).padStart(2, '0');
    const ss = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('liveClock').textContent = hh + ':' + mm + ':' + ss + ' ' + ampm;
}

updateGreeting();
updateClock();
setInterval(updateGreeting, 60000);
setInterval(updateClock, 1000);
<?php echo '</script'; ?>
>

<?php if ($_smarty_tpl->tpl_vars['expiring_today']->value > 0 && $_smarty_tpl->tpl_vars['_c']->value['hide_uet'] != 'yes') {?>
<div class="expiring-alert">
    <div class="expiring-alert-inner">
        <span class="expiring-icon">⚠️</span>
        <span class="expiring-text">
            <strong><?php echo $_smarty_tpl->tpl_vars['expiring_today']->value;?>
</strong> <?php if ($_smarty_tpl->tpl_vars['expiring_today']->value == 1) {
echo Lang::T('user expiring today');
} else {
echo Lang::T('users expiring today');
}?>
        </span>
        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/list" class="expiring-link"><?php echo Lang::T('View');?>
 →</a>
    </div>
</div>
<?php }?>

<!-- Tailwind CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

<style>
/* ===== GREETING BOX ===== */
.greeting-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    padding: 20px 24px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(102,126,234,0.4);
    margin: 0 15px 20px;
    transform: translateY(0);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
}

.greeting-box::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
}

.greeting-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 40px rgba(102,126,234,0.5);
}

.greeting-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    z-index: 1;
}

.greeting-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

.live-clock {
    font-size: 22px;
    font-weight: 800;
    color: rgba(255,255,255,0.95);
    font-variant-numeric: tabular-nums;
    letter-spacing: 2px;
    text-shadow: 0 2px 8px rgba(0,0,0,0.2);
    font-family: 'Courier New', 'SF Mono', 'Fira Code', monospace;
}

.greeting-box h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #ffffff;
    text-shadow: 0 2px 8px rgba(0,0,0,0.2);
    letter-spacing: 0.3px;
}

.greeting-icon {
    font-size: 28px;
    color: rgba(255,255,255,0.9);
    animation: float 3s ease-in-out infinite;
    filter: drop-shadow(0 2px 6px rgba(0,0,0,0.2));
}

/* Expiring Today Alert */
.expiring-alert {
    margin: 0 15px 16px;
}
.expiring-alert-inner {
    background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
    border: 1px solid #ffc107;
    border-left: 4px solid #ff9800;
    border-radius: 10px;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: #856404;
}
.expiring-icon { font-size: 18px; flex-shrink: 0; }
.expiring-text { flex: 1; }
.expiring-link {
    background: #ff9800; color: #fff; padding: 4px 12px; border-radius: 6px;
    font-size: 12px; font-weight: 700; text-decoration: none; white-space: nowrap;
    transition: background .2s;
}
.expiring-link:hover { background: #e68900; color: #fff; text-decoration: none; }

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-6px); }
    100% { transform: translateY(0px); }
}

/* Modern Dashboard Card Animations */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

@keyframes livePulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(0.75); }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Fix uneven card heights - use flexbox on dashboard card row */
.dashboard-cards-row {
    display: flex !important;
    flex-wrap: wrap !important;
}
.dashboard-cards-row > [class*="col-"] {
    display: flex !important;
    flex-direction: column !important;
}
.dashboard-cards-row > [class*="col-"] > .card-hover-effect {
    flex: 1 !important;
}

/* Enhance card hover effects */
.card-hover-effect {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-hover-effect:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

/* Loading animation for dynamic content */
.loading-shimmer {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

/* Custom scrollbar for router details */
#router-details::-webkit-scrollbar {
    width: 6px;
}

#router-details::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
}

#router-details::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}

#router-details::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-hover-effect:hover {
        transform: translateY(-2px) scale(1.01);
    }
    
    /* Smaller greeting on mobile */
    .greeting-box {
        padding: 15px;
        margin: 0 10px 15px;
    }
    
    .greeting-box h3 {
        font-size: 16px;
    }
    
    .live-clock {
        font-size: 16px;
        letter-spacing: 1px;
    }
    
    .greeting-right {
        gap: 10px;
    }
    
    .expiring-alert { margin: 0 10px 12px; }
    .expiring-alert-inner { font-size: 12px; padding: 8px 12px; }
    .expiring-link { font-size: 11px; padding: 3px 10px; }
    
    .greeting-icon {
        font-size: 20px;
    }
    
    /* 2-column layout on mobile (col-xs-6) */
    .col-xs-6 {
        width: 50% !important;
        float: left !important;
        padding-left: 4px !important;
        padding-right: 4px !important;
        margin-bottom: 0.5rem !important;
    }
    
    /* Better sized cards on mobile */
    .card-hover-effect {
        padding: 0.75rem !important;
        min-height: 120px !important;
    }
    
    .card-hover-effect h4 {
        font-size: 1.1rem !important;
        line-height: 1.3 !important;
        font-weight: bold !important;
    }
    
    .card-hover-effect p,
    .card-hover-effect .text-xs {
        font-size: 0.75rem !important;
        line-height: 1.2 !important;
        margin-bottom: 0.5rem !important;
    }
    
    /* Better sized icons on mobile */
    .card-hover-effect .bg-white.bg-opacity-20 {
        padding: 0.4rem !important;
    }
    
    .card-hover-effect .text-lg {
        font-size: 1rem !important;
    }
    
    /* Readable links */
    .card-hover-effect a {
        font-size: 0.7rem !important;
        margin-top: 0.5rem !important;
    }
    
    .card-hover-effect .fa-arrow-right {
        font-size: 0.6rem !important;
        margin-left: 0.25rem !important;
    }
}

@media (max-width: 576px) {
    /* Even smaller greeting on very small screens */
    .greeting-box {
        padding: 12px;
        margin: 0 5px 12px;
    }
    
    .greeting-box h3 {
        font-size: 14px;
    }
    
    .live-clock {
        font-size: 13px;
        letter-spacing: 1px;
    }
    
    .greeting-right {
        gap: 6px;
    }
    
    .expiring-alert { margin: 0 5px 10px; }
    .expiring-alert-inner { font-size: 11px; padding: 6px 10px; gap: 6px; }
    .expiring-link { font-size: 10px; padding: 2px 8px; }
    
    .greeting-icon {
        font-size: 18px;
    }
    
    /* Maintain 2-column layout with better spacing */
    .col-lg-3,
    .col-lg-4,
    .col-md-6,
    .col-sm-6,
    .col-xs-6 {
        width: 50% !important;
        float: left !important;
        padding-left: 4px !important;
        padding-right: 4px !important;
        margin-bottom: 0.5rem !important;
    }
    
    /* Good sized cards for small screens */
    .card-hover-effect {
        padding: 0.75rem !important;
        min-height: 110px !important;
    }
    
    .card-hover-effect h4 {
        font-size: 1rem !important;
        font-weight: bold !important;
    }
    
    .card-hover-effect .text-xs {
        font-size: 0.7rem !important;
    }
    
    .card-hover-effect a {
        font-size: 0.65rem !important;
    }
}

@media (max-width: 480px) {
    /* Still readable on very small phones */
    .card-hover-effect {
        padding: 0.7rem !important;
        min-height: 100px !important;
    }
    
    .card-hover-effect h4 {
        font-size: 0.95rem !important;
        font-weight: bold !important;
    }
    
    .card-hover-effect .text-xs {
        font-size: 0.65rem !important;
    }
    
    /* Make icons properly sized */
    .card-hover-effect .bg-white.bg-opacity-20 {
        padding: 0.35rem !important;
    }
    
    .card-hover-effect .text-lg {
        font-size: 0.9rem !important;
    }
    
    .card-hover-effect a {
        font-size: 0.6rem !important;
    }
}

/* Ensure proper spacing */
.mb-3 {
    margin-bottom: 1rem !important;
}

/* Improve button responsiveness */
.inline-flex {
    display: inline-flex !important;
    align-items: center !important;
}

/* Better text truncation */
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Ensure flex layout works properly */
.flex-1 {
    flex: 1 !important;
    min-width: 0 !important;
}

/* Clear floats properly */
.row::after {
    content: "";
    display: table;
    clear: both;
}

/* Improve readability on mobile */
@media (max-width: 768px) {
    .card-hover-effect .amount {
        font-weight: 900 !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3) !important;
    }
    
    .card-hover-effect p {
        font-weight: 500 !important;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.2) !important;
    }
}
</style>

<div class="row mb-4">
    <div class="col-md-12">
        <div style="background:linear-gradient(135deg,#1a1a2e 0%,#16213e 100%);border-radius:14px;box-shadow:0 6px 20px rgba(26,26,46,0.4);padding:14px 18px;border:1px solid rgba(255,255,255,0.07);">
            <div class="flex items-center space-x-4">
                <label for="router_filter" style="color:rgba(255,255,255,0.8);font-size:13px;font-weight:600;white-space:nowrap;">
                    <i class="fa fa-filter" style="margin-right:8px;color:#00b4db;"></i>
                    <?php echo Lang::T('Filter by Router');?>
:
                </label>
                <select style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:8px;color:white;padding:6px 14px;font-size:13px;" id="router_filter" onchange="filterDashboard()">
                    <option value="all" style="background:#1a1a2e;"><?php echo Lang::T('All Routers');?>
</option>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['routers']->value, 'router');
$_smarty_tpl->tpl_vars['router']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['router']->value) {
$_smarty_tpl->tpl_vars['router']->do_else = false;
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['router']->value['id'];?>
" style="background:#1a1a2e;"><?php echo $_smarty_tpl->tpl_vars['router']->value['name'];?>
</option>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="row dashboard-cards-row">
    <?php if (in_array($_smarty_tpl->tpl_vars['_admin']->value['user_type'],array('SuperAdmin','Admin','Report','Viewer'))) {?>
        <!-- Income Today Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div style="background:linear-gradient(135deg,#00c6ff 0%,#0072ff 100%);border-radius:14px;box-shadow:0 8px 24px rgba(0,114,255,0.35);padding:18px;color:white;transition:all .3s cubic-bezier(.4,0,.2,1);" class="card-hover-effect" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 36px rgba(0,114,255,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(0,114,255,0.35)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-blue-100 text-xs font-medium mb-1"><?php echo Lang::T('Income Today');?>
</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="text-xs"><?php echo $_smarty_tpl->tpl_vars['_c']->value['currency_code'];?>
</span>
                            <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_income_today'] == 'yes') {?>
                                <span class="amount income-today-value" id="income-today-val" style="filter: blur(6px); user-select:none;" title="<?php echo Lang::T('Click eye to reveal');?>
">
                                    <?php echo number_format($_smarty_tpl->tpl_vars['iday']->value,0,$_smarty_tpl->tpl_vars['_c']->value['dec_point'],$_smarty_tpl->tpl_vars['_c']->value['thousands_sep']);?>

                                </span>
                            <?php } else { ?>
                                <span class="amount" id="income-today-val">
                                    <?php echo number_format($_smarty_tpl->tpl_vars['iday']->value,0,$_smarty_tpl->tpl_vars['_c']->value['dec_point'],$_smarty_tpl->tpl_vars['_c']->value['thousands_sep']);?>

                                </span>
                            <?php }?>
                        </h4>
                    </div>
                    <div class="flex flex-col items-center gap-1 ml-2">
                        <div class="bg-white bg-opacity-20 rounded-full p-2">
                            <i class="ion ion-cash text-lg"></i>
                        </div>
                        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_income_today'] == 'yes') {?>
                        <button type="button" onclick="toggleIncome('income-today-val', this)" title="<?php echo Lang::T('Show/Hide Income');?>
"
                            style="background:rgba(255,255,255,0.2);border:none;border-radius:50%;width:26px;height:26px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-eye-slash text-white" style="font-size:12px;"></i>
                        </button>
                        <?php }?>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
reports/by-date" class="inline-flex items-center text-blue-100 hover:text-white text-xs font-medium">
                        <?php echo Lang::T('View Report');?>
 
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Income This Month Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);border-radius:14px;box-shadow:0 8px 24px rgba(17,153,142,0.35);padding:18px;color:white;transition:all .3s cubic-bezier(.4,0,.2,1);" class="card-hover-effect" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 36px rgba(17,153,142,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(17,153,142,0.35)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-green-100 text-xs font-medium mb-1"><?php echo Lang::T('Income This Month');?>
</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="text-xs"><?php echo $_smarty_tpl->tpl_vars['_c']->value['currency_code'];?>
</span>
                            <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_income_month'] == 'yes') {?>
                                <span class="amount income-month-value" id="income-month-val" style="filter: blur(6px); user-select:none;" title="<?php echo Lang::T('Click eye to reveal');?>
">
                                    <?php echo number_format($_smarty_tpl->tpl_vars['imonth']->value,0,$_smarty_tpl->tpl_vars['_c']->value['dec_point'],$_smarty_tpl->tpl_vars['_c']->value['thousands_sep']);?>

                                </span>
                            <?php } else { ?>
                                <span class="amount" id="income-month-val">
                                    <?php echo number_format($_smarty_tpl->tpl_vars['imonth']->value,0,$_smarty_tpl->tpl_vars['_c']->value['dec_point'],$_smarty_tpl->tpl_vars['_c']->value['thousands_sep']);?>

                                </span>
                            <?php }?>
                        </h4>
                        <?php if ($_smarty_tpl->tpl_vars['revenue_change_percent']->value > 0) {?>
                        <div class="flex items-center gap-1 mt-1">
                            <span style="background:rgba(255,255,255,0.25);border-radius:20px;padding:2px 8px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                                &#8593;&nbsp;<?php echo $_smarty_tpl->tpl_vars['revenue_change_percent']->value;?>
% &nbsp;vs last month
                            </span>
                        </div>
                        <?php } elseif ($_smarty_tpl->tpl_vars['revenue_change_percent']->value < 0) {?>
                        <div class="flex items-center gap-1 mt-1">
                            <span style="background:rgba(255,255,255,0.25);border-radius:20px;padding:2px 8px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                                &#8595;&nbsp;<?php echo abs($_smarty_tpl->tpl_vars['revenue_change_percent']->value);?>
% &nbsp;vs last month
                            </span>
                        </div>
                        <?php } else { ?>
                        <div class="flex items-center gap-1 mt-1">
                            <span style="background:rgba(255,255,255,0.25);border-radius:20px;padding:2px 8px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                                &#8594;&nbsp;0% &nbsp;vs last month
                            </span>
                        </div>
                        <?php }?>
                    </div>
                    <div class="flex flex-col items-center gap-1 ml-2">
                        <div class="bg-white bg-opacity-20 rounded-full p-2">
                            <i class="ion ion-stats-bars text-lg"></i>
                        </div>
                        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_income_month'] == 'yes') {?>
                        <button type="button" onclick="toggleIncome('income-month-val', this)" title="<?php echo Lang::T('Show/Hide Income');?>
"
                            style="background:rgba(255,255,255,0.2);border:none;border-radius:50%;width:26px;height:26px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-eye-slash text-white" style="font-size:12px;"></i>
                        </button>
                        <?php }?>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
reports/by-period" class="inline-flex items-center text-green-100 hover:text-white text-xs font-medium">
                        <?php echo Lang::T('View Report');?>
 
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <?php echo '<script'; ?>
>
        function toggleIncome(spanId, btn) {
            var span = document.getElementById(spanId);
            var icon = btn.querySelector('i');
            if (span.style.filter === 'blur(6px)') {
                span.style.filter = 'none';
                span.style.userSelect = 'auto';
                icon.className = 'fa fa-eye text-white';
            } else {
                span.style.filter = 'blur(6px)';
                span.style.userSelect = 'none';
                icon.className = 'fa fa-eye-slash text-white';
            }
        }
        <?php echo '</script'; ?>
>


        <!-- Active/Expired Users Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div style="background:linear-gradient(135deg,#f7971e 0%,#ff4757 100%);border-radius:14px;box-shadow:0 8px 24px rgba(255,71,87,0.35);padding:18px;color:white;transition:all .3s cubic-bezier(.4,0,.2,1);" class="card-hover-effect" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 36px rgba(255,71,87,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(255,71,87,0.35)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-orange-100 text-xs font-medium mb-1"><?php echo Lang::T('Active');?>
/<?php echo Lang::T('Expired');?>
</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="amount" id="active-expired-val"><?php echo $_smarty_tpl->tpl_vars['u_act']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['u_all']->value-$_smarty_tpl->tpl_vars['u_act']->value;?>
</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-person text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/list" class="inline-flex items-center text-orange-100 hover:text-white text-xs font-medium">
                        <?php echo Lang::T('View Customers');?>
 
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Online PPPoE Users Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div style="background:linear-gradient(135deg,#a18cd1 0%,#4776e6 100%);border-radius:14px;box-shadow:0 8px 24px rgba(71,118,230,0.35);padding:18px;color:white;transition:all .3s cubic-bezier(.4,0,.2,1);" class="card-hover-effect" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 36px rgba(71,118,230,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(71,118,230,0.35)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-purple-100 text-xs font-medium mb-1"><?php echo Lang::T('Online PPPoE Users');?>
</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="amount" id="pppoe-online-val"><?php echo $_smarty_tpl->tpl_vars['online_users']->value;?>
</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-network text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plugin/pppoe_monitor_router_menu" class="inline-flex items-center text-purple-100 hover:text-white text-xs font-medium">
                        <?php echo Lang::T('Online PPPoE Users');?>

                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php }?>

    <?php if (in_array($_smarty_tpl->tpl_vars['_admin']->value['user_type'],array('SuperAdmin','Admin','Report'))) {?>
        <!-- Online Hotspot Users Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div style="background:linear-gradient(135deg,#f7971e 0%,#ffd200 100%);border-radius:14px;box-shadow:0 8px 24px rgba(247,151,30,0.35);padding:18px;color:white;transition:all .3s cubic-bezier(.4,0,.2,1);" class="card-hover-effect" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 36px rgba(247,151,30,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(247,151,30,0.35)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-amber-100 text-xs font-medium mb-1">
                            <?php echo Lang::T('Online Hotspot Users');?>

                            <span style="display:inline-flex;align-items:center;gap:3px;background:rgba(255,255,255,0.2);border-radius:20px;padding:1px 7px;font-size:10px;font-weight:700;letter-spacing:.5px;margin-left:4px;">
                                <span id="hotspot-live-dot" style="width:7px;height:7px;border-radius:50%;background:#4ade80;display:inline-block;animation:livePulse 1.2s ease-in-out infinite;"></span>
                                LIVE
                            </span>
                        </p>
                        <h4 class="text-xl font-bold" id="online-hotspot-users"><?php echo $_smarty_tpl->tpl_vars['hotspot_users']->value;?>
</h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-wifi text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
onlineusers/hotspot" class="inline-flex items-center text-amber-100 hover:text-white text-xs font-medium">
                        <?php echo Lang::T('View Details');?>
 
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Online Users Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div style="background:linear-gradient(135deg,#6a11cb 0%,#2575fc 100%);border-radius:14px;box-shadow:0 8px 24px rgba(37,117,252,0.35);padding:18px;color:white;transition:all .3s cubic-bezier(.4,0,.2,1);" class="card-hover-effect" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 36px rgba(37,117,252,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(37,117,252,0.35)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-blue-100 text-xs font-medium mb-1"><?php echo Lang::T('Total Online Users');?>
</p>
                        <h4 class="text-xl font-bold" id="total-online-users"><?php echo $_smarty_tpl->tpl_vars['total_online']->value;?>
</h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-ios-people text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
reports/by-date" class="inline-flex items-center text-blue-100 hover:text-white text-xs font-medium">
                        <?php echo Lang::T('View Details');?>
 
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total PPPoE Users Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);border-radius:14px;box-shadow:0 8px 24px rgba(17,153,142,0.35);padding:18px;color:white;transition:all .3s cubic-bezier(.4,0,.2,1);" class="card-hover-effect" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 36px rgba(17,153,142,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(17,153,142,0.35)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-green-100 text-xs font-medium mb-1"><?php echo Lang::T('Total PPPoE Users');?>
</p>
                        <h4 class="text-xl font-bold" id="total-pppoe-val"><?php echo $_smarty_tpl->tpl_vars['total_pppoe']->value;?>
</h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-network text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/list&type=PPPOE" class="inline-flex items-center text-green-100 hover:text-white text-xs font-medium">
                        <?php echo Lang::T('View Details');?>
 
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Customers Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div style="background:linear-gradient(135deg,#00b4db 0%,#009688 100%);border-radius:14px;box-shadow:0 8px 24px rgba(0,180,219,0.35);padding:18px;color:white;transition:all .3s cubic-bezier(.4,0,.2,1);" class="card-hover-effect" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 36px rgba(0,180,219,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(0,180,219,0.35)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-teal-100 text-xs font-medium mb-1"><?php echo Lang::T('Total Customers');?>
</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount" id="total-customers-val"><?php echo $_smarty_tpl->tpl_vars['c_all']->value;?>
</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-android-contacts text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/list" class="inline-flex items-center text-teal-100 hover:text-white text-xs font-medium">
                        <?php echo Lang::T('View Details');?>
 
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php }?>

    <?php if (in_array($_smarty_tpl->tpl_vars['_admin']->value['user_type'],array('SuperAdmin','Admin','Report'))) {?>
        <!-- Expired PPPoE Users Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div class="card-hover-effect" style="background:linear-gradient(135deg,#fc4a1a 0%,#f7b733 100%);border-radius:14px;box-shadow:0 8px 24px rgba(252,74,26,0.3);padding:18px;color:white;transition:all .3s;" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-yellow-100 text-xs font-medium mb-1"><?php echo Lang::T('Expired PPPoE Users');?>
</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount" id="expired-pppoe-val"><?php echo $_smarty_tpl->tpl_vars['expired_pppoe']->value;?>
</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-network text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/list&status=off&type=PPPOE" class="inline-flex items-center text-yellow-100 hover:text-white text-xs font-medium">
                        <?php echo Lang::T('View Details');?>
 
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Expired Hotspot Users Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div class="card-hover-effect" style="background:linear-gradient(135deg,#e53935 0%,#e91e8c 100%);border-radius:14px;box-shadow:0 8px 24px rgba(229,57,53,0.3);padding:18px;color:white;transition:all .3s;" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-red-100 text-xs font-medium mb-1"><?php echo Lang::T('Expired Hotspot Users');?>
</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount" id="expired-hotspot-val"><?php echo $_smarty_tpl->tpl_vars['expired_hotspot']->value;?>
</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-wifi text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/list&status=off&type=Hotspot" class="inline-flex items-center text-red-100 hover:text-white text-xs font-medium">
                        <?php echo Lang::T('View Details');?>
 
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Expired Users Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div class="card-hover-effect" style="background:linear-gradient(135deg,#373b44 0%,#4286f4 100%);border-radius:14px;box-shadow:0 8px 24px rgba(55,59,68,0.35);padding:18px;color:white;transition:all .3s;" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-gray-100 text-xs font-medium mb-1"><?php echo Lang::T('Total Expired Users');?>
</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount" id="total-expired-val"><?php echo $_smarty_tpl->tpl_vars['total_expired']->value;?>
</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-ios-people text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/list&status=off" class="inline-flex items-center text-gray-100 hover:text-white text-xs font-medium">
                        <?php echo Lang::T('View Details');?>
 
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Active Users Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div class="card-hover-effect" style="background:linear-gradient(135deg,#0f9b0f 0%,#00b09b 100%);border-radius:14px;box-shadow:0 8px 24px rgba(15,155,15,0.35);padding:18px;color:white;transition:all .3s;" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-green-100 text-xs font-medium mb-1"><?php echo Lang::T('Active Users');?>
</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount" id="active-users-val"><?php echo $_smarty_tpl->tpl_vars['u_act']->value;?>
</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-ios-person text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/list" class="inline-flex items-center text-green-100 hover:text-white text-xs font-medium">
                        <?php echo Lang::T('View Details');?>
 
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php }?>
</div>

<!-- Data Usage Row -->
<div class="row">
    <?php if (in_array($_smarty_tpl->tpl_vars['_admin']->value['user_type'],array('SuperAdmin','Admin','Report'))) {?>
        <div class="col-lg-12 col-xs-12">
            <div style="background:linear-gradient(135deg,#1a1a2e 0%,#16213e 40%,#0f3460 70%,#533483 100%);border-radius:16px;box-shadow:0 12px 32px rgba(26,26,46,0.5);padding:20px;color:white;margin-bottom:16px;border:1px solid rgba(255,255,255,0.07);">
                <!-- Section Title -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <i class="fa fa-calendar mr-2 text-indigo-300"></i>
                        <span class="text-white font-semibold text-sm">Monthly Data Usage</span>
                        <span class="ml-2 text-indigo-300 text-xs">&mdash; <?php if ((isset($_smarty_tpl->tpl_vars['total_data_usage']->value['current_month']))) {
echo $_smarty_tpl->tpl_vars['total_data_usage']->value['current_month'];
} else {
echo smarty_modifier_date_format("now","%B %Y");
}?></span>
                    </div>
                    <span class="text-indigo-400 text-xs"><i class="fa fa-refresh mr-1"></i>Resets on the 1st</span>
                </div>
                <!-- Main Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <!-- Downloaded -->
                    <div class="text-center bg-white bg-opacity-10 rounded-lg p-3 backdrop-blur-sm">
                        <div class="flex items-center justify-center mb-2">
                            <div class="bg-white bg-opacity-20 rounded-full p-2 mr-2">
                                <i class="fa fa-download text-sm"></i>
                            </div>
                            <h4 class="text-lg font-bold">
                                <span class="amount"><?php if ((isset($_smarty_tpl->tpl_vars['total_data_usage']->value['total_rx']))) {
echo $_smarty_tpl->tpl_vars['total_data_usage']->value['total_rx'];
} else { ?>0 B<?php }?></span>
                            </h4>
                        </div>
                        <p class="text-indigo-100 text-xs font-medium">Downloaded This Month</p>
                    </div>
                    
                    <!-- Uploaded -->
                    <div class="text-center bg-white bg-opacity-10 rounded-lg p-3 backdrop-blur-sm">
                        <div class="flex items-center justify-center mb-2">
                            <div class="bg-white bg-opacity-20 rounded-full p-2 mr-2">
                                <i class="fa fa-upload text-sm"></i>
                            </div>
                            <h4 class="text-lg font-bold">
                                <span class="amount"><?php if ((isset($_smarty_tpl->tpl_vars['total_data_usage']->value['total_tx']))) {
echo $_smarty_tpl->tpl_vars['total_data_usage']->value['total_tx'];
} else { ?>0 B<?php }?></span>
                            </h4>
                        </div>
                        <p class="text-indigo-100 text-xs font-medium">Uploaded This Month</p>
                    </div>
                    
                    <!-- Total Usage -->
                    <div class="text-center bg-white bg-opacity-10 rounded-lg p-3 backdrop-blur-sm">
                        <div class="flex items-center justify-center mb-2">
                            <div class="bg-white bg-opacity-20 rounded-full p-2 mr-2">
                                <i class="fa fa-exchange text-sm"></i>
                            </div>
                            <h4 class="text-lg font-bold">
                                <span class="amount"><?php if ((isset($_smarty_tpl->tpl_vars['total_data_usage']->value['total_usage']))) {
echo $_smarty_tpl->tpl_vars['total_data_usage']->value['total_usage'];
} else { ?>0 B<?php }?></span>
                            </h4>
                        </div>
                        <p class="text-indigo-100 text-xs font-medium">Total Data Usage This Month</p>
                    </div>
                </div>

                <!-- Router Info -->
                <div class="border-t border-white border-opacity-20 pt-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center text-indigo-100 text-xs flex-wrap gap-2">
                            <span><i class="fa fa-router mr-1"></i><?php if ((isset($_smarty_tpl->tpl_vars['total_data_usage']->value['active_routers']))) {
echo $_smarty_tpl->tpl_vars['total_data_usage']->value['active_routers'];
} else { ?>0<?php }?> Active Routers</span>
                            <span class="hidden sm:inline">|</span>
                            <span><i class="fa fa-database mr-1"></i>Stored in DB &bull; Resets 1st of every month</span>
                            <?php if ((isset($_smarty_tpl->tpl_vars['total_data_usage']->value['resets_on']))) {?>
                                <span class="hidden sm:inline">|</span>
                                <span class="hidden sm:inline">Next reset: <?php echo $_smarty_tpl->tpl_vars['total_data_usage']->value['resets_on'];?>
</span>
                            <?php }?>
                        </div>
                        <div class="flex items-center space-x-2">
                            <?php if ((isset($_smarty_tpl->tpl_vars['total_data_usage']->value['router_details'])) && count($_smarty_tpl->tpl_vars['total_data_usage']->value['router_details']) > 0) {?>
                                <button onclick="toggleRouterDetails()" class="inline-flex items-center px-2 py-1 bg-white bg-opacity-20 rounded-full text-xs hover:bg-opacity-30 transition-all duration-200">
                                    <i class="fa fa-eye mr-1"></i>
                                    <span class="hidden sm:inline">View Details</span>
                                </button>
                            <?php }?>
                            <button onclick="refreshDataUsage()" class="inline-flex items-center px-3 py-1 bg-white bg-opacity-20 rounded-full text-xs hover:bg-opacity-30 transition-all duration-200">
                                <i class="fa fa-refresh mr-1"></i>
                                <span class="hidden sm:inline">Refresh</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Router Details (Hidden by default) -->
                <?php if ((isset($_smarty_tpl->tpl_vars['total_data_usage']->value['router_details'])) && count($_smarty_tpl->tpl_vars['total_data_usage']->value['router_details']) > 0) {?>
                <div id="router-details" style="display: none;" class="mt-4 bg-white bg-opacity-10 rounded-lg p-3 backdrop-blur-sm">
                    <h6 class="text-white font-medium mb-3 flex items-center text-sm">
                        <i class="fa fa-info-circle mr-2"></i>
                        Router WAN Interface Details:
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['total_data_usage']->value['router_details'], 'router');
$_smarty_tpl->tpl_vars['router']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['router']->value) {
$_smarty_tpl->tpl_vars['router']->do_else = false;
?>
                        <div class="bg-white bg-opacity-5 rounded-lg p-2 text-xs">
                            <div class="font-medium text-white mb-1"><?php echo $_smarty_tpl->tpl_vars['router']->value['name'];?>
</div>
                            <div class="text-indigo-100 text-xs space-y-1">
                                <div>RX: <?php echo $_smarty_tpl->tpl_vars['router']->value['rx'];?>
</div>
                                <div>TX: <?php echo $_smarty_tpl->tpl_vars['router']->value['tx'];?>
</div>
                                <div>Total: <?php echo $_smarty_tpl->tpl_vars['router']->value['total'];?>
</div>
                                <div class="flex items-center">
                                    <?php if (!$_smarty_tpl->tpl_vars['router']->value['wan_found']) {?>
                                        <span class="inline-flex items-center px-1 py-0.5 bg-yellow-500 bg-opacity-20 text-yellow-200 rounded text-xs">
                                            <i class="fa fa-exclamation-triangle mr-1"></i>
                                            Auto-detected
                                        </span>
                                    <?php } else { ?>
                                        <span class="inline-flex items-center px-1 py-0.5 bg-green-500 bg-opacity-20 text-green-200 rounded text-xs">
                                            <i class="fa fa-check mr-1"></i>
                                            Confirmed
                                        </span>
                                    <?php }?>
                                </div>
                            </div>
                        </div>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </div>
                </div>
                <?php }?>
            </div>
        </div>
    <?php }?>
</div>

<?php if (in_array($_smarty_tpl->tpl_vars['_admin']->value['user_type'],array('SuperAdmin','Admin'))) {?>
    <?php $_smarty_tpl->_subTemplateRender("file:admin_server_stats.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}?>

<?php if (in_array($_smarty_tpl->tpl_vars['_admin']->value['user_type'],array('SuperAdmin','Admin','Report','Viewer'))) {?>
<style>
.analytics-widgets {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin-bottom: 15px;
}

@media (max-width: 768px) {
    .analytics-widgets {
        grid-template-columns: 1fr;
        gap: 8px;
    }
}

/* Disable all links and buttons for Viewer role */
<?php if ($_smarty_tpl->tpl_vars['_admin']->value['user_type'] == 'Viewer') {?>
.dashboard-card a, .analytics-widgets a, .btn-view-report, .btn-view-details,
.dashboard-card button, .analytics-widgets button,
.dashboard .col-lg-3 a, .dashboard .col-md-6 a, .dashboard .col-sm-6 a, .dashboard .col-xs-6 a,
.inline-flex.items-center, button[onclick], a[href*="reports"], a[href*="customers"], a[href*="plan"], a[href*="onlineusers"] {
    pointer-events: none !important;
    cursor: not-allowed !important;
    opacity: 0.6 !important;
    text-decoration: none !important;
}

.dashboard-card a:hover, .analytics-widgets a:hover,
.dashboard .col-lg-3 a:hover, .dashboard .col-md-6 a:hover, .dashboard .col-sm-6 a:hover, .dashboard .col-xs-6 a:hover,
.inline-flex.items-center:hover {
    transform: none !important;
    box-shadow: none !important;
    color: inherit !important;
}
<?php }?>

.compact-card {
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.04);
}

.compact-card:hover {
    box-shadow: 0 12px 30px rgba(0,0,0,0.14);
    transform: translateY(-3px);
}

.card-gradient-header {
    padding: 10px 14px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.revenue-header {
    background: linear-gradient(135deg, #4776e6 0%, #8e54e9 100%);
    box-shadow: 0 4px 15px rgba(71,118,230,0.4);
}

.plans-header {
    background: linear-gradient(135deg, #f953c6 0%, #b91d73 100%);
    box-shadow: 0 4px 15px rgba(249,83,198,0.4);
}

.yoy-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    box-shadow: 0 4px 15px rgba(17,153,142,0.4);
}

.revenue-divider {
    text-align: center;
    margin: 8px 0;
    border-top: 1px solid #e2e8f0;
    position: relative;
}
.revenue-divider span {
    position: relative;
    top: -10px;
    background: #fff;
    padding: 0 12px;
    color: #a0aec0;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.header-title {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 15px;
    font-weight: 600;
    margin: 0;
}

.card-body-compact {
    padding: 12px;
}

.revenue-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-bottom: 0;
}

.stat-mini {
    text-align: center;
    padding: 12px 6px;
    background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
    border-radius: 10px;
    transition: all 0.2s;
    border: 1px solid rgba(71,118,230,0.1);
}

.stat-mini:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(71,118,230,0.18);
}

.stat-label {
    font-size: 9px;
    text-transform: uppercase;
    color: #6c757d;
    font-weight: 600;
    letter-spacing: 0.3px;
    margin-bottom: 3px;
}

.stat-value {
    font-size: 18px;
    font-weight: 800;
    color: #2d3748;
    margin-bottom: 2px;
    letter-spacing: -0.5px;
}

.stat-month {
    font-size: 10px;
    color: #718096;
}

.change-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px;
    border-radius: 6px;
    font-weight: 600;
}

.change-positive {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
}

.change-negative {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
}

.change-icon {
    font-size: 16px;
    margin-right: 6px;
}

.change-amount {
    font-size: 16px;
    margin: 0 4px;
}

.change-percent {
    font-size: 13px;
}

.plan-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.plan-row {
    display: flex;
    align-items: center;
    padding: 8px 10px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 6px;
    transition: all 0.2s;
    border-left: 3px solid transparent;
}

.plan-row:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
    transform: translateX(3px);
}

.plan-row.rank-1 { border-left-color: #FFD700; }
.plan-row.rank-2 { border-left-color: #C0C0C0; }
.plan-row.rank-3 { border-left-color: #CD7F32; }

.rank-badge {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 12px;
    flex-shrink: 0;
}

.rank-1 { 
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: #000;
    box-shadow: 0 2px 6px rgba(255, 215, 0, 0.3);
}
.rank-2 { 
    background: linear-gradient(135deg, #C0C0C0, #A8A8A8);
    color: #000;
    box-shadow: 0 2px 6px rgba(192, 192, 192, 0.3);
}
.rank-3 { 
    background: linear-gradient(135deg, #CD7F32, #B8860B);
    color: #fff;
    box-shadow: 0 2px 6px rgba(205, 127, 50, 0.3);
}
.rank-other { 
    background: #e9ecef;
    color: #495057;
    font-weight: 600;
}

.plan-details {
    flex: 1;
    margin: 0 10px;
    min-width: 0;
}

.plan-title {
    font-size: 13px;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.plan-stats {
    display: flex;
    align-items: center;
    gap: 6px;
}

.stat-badge {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 10px;
    padding: 2px 6px;
    background: #e3f2fd;
    color: #1976d2;
    border-radius: 10px;
    font-weight: 600;
}

.plan-amount {
    font-size: 14px;
    font-weight: 700;
    color: #16a085;
    white-space: nowrap;
}

.empty-state {
    text-align: center;
    padding: 20px 15px;
    color: #6c757d;
}

.empty-state i {
    font-size: 32px;
    opacity: 0.3;
    margin-bottom: 8px;
}
</style>

<div class="analytics-widgets">
    <!-- Revenue Comparison Card -->
    <div class="compact-card">
        <div class="card-gradient-header revenue-header">
            <h3 class="header-title">
                <i class="fa fa-line-chart"></i>
                <?php echo Lang::T('Revenue Comparison');?>

            </h3>
        </div>
        <div class="card-body-compact">
            <div class="revenue-grid">
                <div class="stat-mini">
                    <div class="stat-label"><?php echo Lang::T('This Month');?>
</div>
                    <div class="stat-value"><?php echo number_format($_smarty_tpl->tpl_vars['revenue_this_month']->value,0);?>
</div>
                    <div class="stat-month"><?php echo $_smarty_tpl->tpl_vars['first_day_this_month']->value;?>
</div>
                </div>
                <div class="stat-mini">
                    <div class="stat-label"><?php echo Lang::T('Last Month');?>
</div>
                    <div class="stat-value"><?php echo number_format($_smarty_tpl->tpl_vars['revenue_last_month']->value,0);?>
</div>
                    <div class="stat-month"><?php echo $_smarty_tpl->tpl_vars['first_day_last_month']->value;?>
</div>
                </div>
                <div class="stat-mini <?php if ($_smarty_tpl->tpl_vars['revenue_change']->value >= 0) {?>change-positive<?php } else { ?>change-negative<?php }?>">
                    <div class="stat-label"><?php echo Lang::T('Change');?>
</div>
                    <div class="stat-value">
                        <?php if ($_smarty_tpl->tpl_vars['revenue_change']->value >= 0) {?>+<?php } else { ?>-<?php }
echo abs((float)$_smarty_tpl->tpl_vars['revenue_change_percent']->value);?>
%
                    </div>
                    <div class="stat-month">
                        <?php if ($_smarty_tpl->tpl_vars['revenue_change']->value >= 0) {?>
                            <i class="fa fa-arrow-up"></i> <?php echo Lang::T('Growth');?>

                        <?php } else { ?>
                            <i class="fa fa-arrow-down"></i> <?php echo Lang::T('Decline');?>

                        <?php }?>
                    </div>
                </div>
            </div>
            <div class="revenue-divider"><span>vs Last Year (YTD)</span></div>
            <div class="revenue-grid">
                <div class="stat-mini">
                    <div class="stat-label"><?php echo $_smarty_tpl->tpl_vars['yoy_current_label']->value;?>
</div>
                    <div class="stat-value"><?php echo number_format($_smarty_tpl->tpl_vars['revenue_this_ytd']->value,0);?>
</div>
                    <div class="stat-month"><?php echo Lang::T('This Year');?>
</div>
                </div>
                <div class="stat-mini">
                    <div class="stat-label"><?php echo $_smarty_tpl->tpl_vars['yoy_last_label']->value;?>
</div>
                    <div class="stat-value"><?php echo number_format($_smarty_tpl->tpl_vars['revenue_last_ytd']->value,0);?>
</div>
                    <div class="stat-month"><?php echo Lang::T('Last Year');?>
</div>
                </div>
                <div class="stat-mini <?php if ($_smarty_tpl->tpl_vars['yoy_change']->value >= 0) {?>change-positive<?php } else { ?>change-negative<?php }?>">
                    <div class="stat-label"><?php echo Lang::T('YoY Change');?>
</div>
                    <div class="stat-value">
                        <?php if ($_smarty_tpl->tpl_vars['yoy_change']->value >= 0) {?>+<?php } else { ?>-<?php }
echo abs((float)$_smarty_tpl->tpl_vars['yoy_change_percent']->value);?>
%
                    </div>
                    <div class="stat-month">
                        <?php if ($_smarty_tpl->tpl_vars['yoy_change']->value >= 0) {?>
                            <i class="fa fa-arrow-up"></i> <?php echo Lang::T('Growth');?>

                        <?php } else { ?>
                            <i class="fa fa-arrow-down"></i> <?php echo Lang::T('Decline');?>

                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Most Popular Plans Card -->
    <div class="compact-card">
        <div class="card-gradient-header plans-header">
            <h3 class="header-title">
                <i class="fa fa-trophy"></i>
                <?php echo Lang::T('Most Popular Plans');?>

            </h3>
        </div>
        <div class="card-body-compact">
            <?php if ($_smarty_tpl->tpl_vars['most_popular_plans']->value) {?>
                <div class="plan-list">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['most_popular_plans']->value, 'plan', false, 'index');
$_smarty_tpl->tpl_vars['plan']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['index']->value => $_smarty_tpl->tpl_vars['plan']->value) {
$_smarty_tpl->tpl_vars['plan']->do_else = false;
?>
                        <div class="plan-row rank-<?php echo $_smarty_tpl->tpl_vars['index']->value+1;?>
">
                            <div class="rank-badge <?php if ($_smarty_tpl->tpl_vars['index']->value == 0) {?>rank-1<?php } elseif ($_smarty_tpl->tpl_vars['index']->value == 1) {?>rank-2<?php } elseif ($_smarty_tpl->tpl_vars['index']->value == 2) {?>rank-3<?php } else { ?>rank-other<?php }?>">
                                <?php if ($_smarty_tpl->tpl_vars['index']->value < 3) {?><i class="fa fa-trophy"></i><?php } else {
echo $_smarty_tpl->tpl_vars['index']->value+1;
}?>
                            </div>
                            <div class="plan-details">
                                <div class="plan-title"><?php echo $_smarty_tpl->tpl_vars['plan']->value['plan_name'];?>
</div>
                                <div class="plan-stats">
                                    <span class="stat-badge">
                                        <i class="fa fa-shopping-cart"></i>
                                        <?php echo $_smarty_tpl->tpl_vars['plan']->value['subscription_count'];?>

                                    </span>
                                </div>
                            </div>
                            <div class="plan-amount"><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['plan']->value['total_revenue']);?>
</div>
                        </div>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                </div>
            <?php } else { ?>
                <div class="empty-state">
                    <i class="fa fa-info-circle"></i>
                    <div><?php echo Lang::T('No plan data available');?>
</div>
                </div>
            <?php }?>
        </div>
    </div>
</div>
<?php }?>

<div class="row">
    <div class="col-md-7">
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_mrc'] != 'yes') {?>
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
                <div style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:14px 18px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="color:#fff;font-weight:700;font-size:15px;">
                        <i class="fa fa-users" style="margin-right:8px;opacity:.85;"></i>
                        <?php echo Lang::T('Monthly Registered Customers');?>

                    </span>
                    <div>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
dashboard&refresh" style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;color:#fff;padding:5px 12px;font-size:12px;text-decoration:none;">
                            <i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </div>
                <div style="padding:16px;">
                    <canvas class="chart" id="chart" style="height: 250px;"></canvas>
                </div>
            </div>
        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_tms'] != 'yes') {?>
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
                <div style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);padding:14px 18px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="color:#fff;font-weight:700;font-size:15px;">
                        <i class="fa fa-line-chart" style="margin-right:8px;opacity:.85;"></i>
                        <?php echo Lang::T('Total Monthly Sales');?>

                    </span>
                    <div>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
dashboard&refresh" style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;color:#fff;padding:5px 12px;font-size:12px;text-decoration:none;">
                            <i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </div>
                <div style="padding:16px;">
                    <canvas class="chart" id="salesChart" style="height: 250px;"></canvas>
                </div>
            </div>
        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_tws'] != 'yes') {?>
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
                <div style="background:linear-gradient(135deg,#f953c6 0%,#b91d73 100%);padding:14px 18px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="color:#fff;font-weight:700;font-size:15px;">
                        <i class="fa fa-calendar" style="margin-right:8px;opacity:.85;"></i>
                        <?php echo Lang::T('Total Weekly Sales');?>

                    </span>
                    <div>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
dashboard&refresh" style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;color:#fff;padding:5px 12px;font-size:12px;text-decoration:none;">
                            <i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </div>
                <div style="padding:16px;">
                    <canvas class="chart" id="weeklySalesChart" style="height: 250px;"></canvas>
                </div>
            </div>
        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_lt'] != 'yes') {?>
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
                <div style="background:linear-gradient(135deg,#1a1a2e 0%,#0f3460 100%);padding:14px 18px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="color:#fff;font-weight:700;font-size:15px;">
                        <i class="fa fa-money" style="margin-right:8px;color:#f7b733;"></i>
                        <?php echo Lang::T('Last 5 Transactions');?>

                    </span>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
reports/by-period" style="background:rgba(255,255,255,0.15);border-radius:8px;color:#fff;padding:5px 12px;font-size:12px;text-decoration:none;">
                        <i class="fa fa-list"></i> View All
                    </a>
                </div>
                <div style="padding:0;">
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo Lang::T('Invoice');?>
</th>
                                    <th><?php echo Lang::T('Username');?>
</th>
                                    <th><?php echo Lang::T('Plan');?>
</th>
                                    <th><?php echo Lang::T('Price');?>
</th>
                                    <th><?php echo Lang::T('Date');?>
</th>
                                    <th><?php echo Lang::T('Method');?>
</th>
                                    <th><?php echo Lang::T('Router');?>
</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($_smarty_tpl->tpl_vars['lastTransactions']->value) {?>
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['lastTransactions']->value, 'trans');
$_smarty_tpl->tpl_vars['trans']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['trans']->value) {
$_smarty_tpl->tpl_vars['trans']->do_else = false;
?>
                                        <tr>
                                            <td><?php echo $_smarty_tpl->tpl_vars['trans']->value['id'];?>
</td>
                                            <td><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
reports/by-period" title="View Details"><?php echo $_smarty_tpl->tpl_vars['trans']->value['invoice'];?>
</a></td>
                                            <td><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/viewu/<?php echo $_smarty_tpl->tpl_vars['trans']->value['username'];?>
"><?php echo $_smarty_tpl->tpl_vars['trans']->value['username'];?>
</a></td>
                                            <td><?php echo $_smarty_tpl->tpl_vars['trans']->value['plan_name'];?>
</td>
                                            <td><strong><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['trans']->value['price']);?>
</strong></td>
                                            <td><small><?php echo $_smarty_tpl->tpl_vars['trans']->value['recharged_on'];?>
 <?php echo $_smarty_tpl->tpl_vars['trans']->value['recharged_time'];?>
</small></td>
                                            <td><span class="label label-info"><?php echo $_smarty_tpl->tpl_vars['trans']->value['method'];?>
</span></td>
                                            <td><?php echo $_smarty_tpl->tpl_vars['trans']->value['routers'];?>
</td>
                                        </tr>
                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="8" class="text-center"><?php echo Lang::T('No transactions found');?>
</td>
                                    </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php }?>

                <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
            <div style="background:linear-gradient(135deg,#f7971e 0%,#ffd200 100%);padding:14px 18px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="color:#fff;font-weight:700;font-size:15px;">
                        <i class="fa fa-star" style="margin-right:8px;"></i>
                        <?php echo Lang::T('Top 5 Most Active Users');?>

                    </span>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
reports/by-period" style="background:rgba(255,255,255,0.25);border-radius:8px;color:#fff;padding:5px 12px;font-size:12px;text-decoration:none;">
                        <i class="fa fa-list"></i> View All
                    </a>
            </div>
            <div style="padding:0;">
                <p class="text-muted"><small><?php echo Lang::T('The most active users in the last 30 days.');?>
</small></p>
                <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo Lang::T('Username');?>
</th>
                                <th><?php echo Lang::T('Full Name');?>
</th>
                                <th><?php echo Lang::T('Phone');?>
</th>
                                <th><?php echo Lang::T('Transactions');?>
</th>
                                <th><?php echo Lang::T('Total Spent');?>
</th>
                                <th><?php echo Lang::T('Last Recharge');?>
</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($_smarty_tpl->tpl_vars['topActiveUsers']->value) {?>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['topActiveUsers']->value, 'user', false, 'index');
$_smarty_tpl->tpl_vars['user']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['index']->value => $_smarty_tpl->tpl_vars['user']->value) {
$_smarty_tpl->tpl_vars['user']->do_else = false;
?>
                                    <tr>
                                        <td>
                                            <?php if ($_smarty_tpl->tpl_vars['index']->value == 0) {?>
                                                <i class="fa fa-trophy text-warning" style="font-size: 16px;"></i>
                                            <?php } elseif ($_smarty_tpl->tpl_vars['index']->value == 1) {?>
                                                <i class="fa fa-trophy text-muted" style="font-size: 14px;"></i>
                                            <?php } elseif ($_smarty_tpl->tpl_vars['index']->value == 2) {?>
                                                <i class="fa fa-trophy text-danger" style="font-size: 12px;"></i>
                                            <?php } else { ?>
                                                <?php echo $_smarty_tpl->tpl_vars['index']->value+1;?>

                                            <?php }?>
                                        </td>
                                        <td><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/viewu/<?php echo $_smarty_tpl->tpl_vars['user']->value['username'];?>
"><?php echo $_smarty_tpl->tpl_vars['user']->value['username'];?>
</a></td>
                                        <td><?php echo $_smarty_tpl->tpl_vars['user']->value['fullname'];?>
</td>
                                        <td><?php echo $_smarty_tpl->tpl_vars['user']->value['phonenumber'];?>
</td>
                                        <td><span class="badge badge-info"><?php echo $_smarty_tpl->tpl_vars['user']->value['transaction_count'];?>
</span></td>
                                        <td><strong><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['user']->value['total_spent']);?>
</strong></td>
                                        <td><small><?php echo $_smarty_tpl->tpl_vars['user']->value['last_recharge'];?>
</small></td>
                                    </tr>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            <?php } else { ?>
                                <tr>
                                    <td colspan="7" class="text-center"><?php echo Lang::T('No active users found');?>
</td>
                                </tr>
                            <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_vs'] != 'yes') {?>
            <?php if ($_smarty_tpl->tpl_vars['_c']->value['disable_voucher'] != 'yes' && $_smarty_tpl->tpl_vars['stocks']->value['unused'] > 0 || $_smarty_tpl->tpl_vars['stocks']->value['used'] > 0) {?>
                <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
                    <div style="background:linear-gradient(135deg,#f7971e 0%,#ffd200 100%);padding:12px 16px;">
                        <span style="color:#fff;font-weight:700;font-size:15px;"><i class="fa fa-ticket" style="margin-right:8px;"></i>Vouchers Stock</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <th><?php echo Lang::T('Package Name');?>
</th>
                                    <th>unused</th>
                                    <th>used</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['plans']->value, 'stok');
$_smarty_tpl->tpl_vars['stok']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['stok']->value) {
$_smarty_tpl->tpl_vars['stok']->do_else = false;
?>
                                    <tr>
                                        <td><?php echo $_smarty_tpl->tpl_vars['stok']->value['name_plan'];?>
</td>
                                        <td><?php echo $_smarty_tpl->tpl_vars['stok']->value['unused'];?>
</td>
                                        <td><?php echo $_smarty_tpl->tpl_vars['stok']->value['used'];?>
</td>
                                    </tr>
                                </tbody>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            <tr>
                                <td>Total</td>
                                <td><?php echo $_smarty_tpl->tpl_vars['stocks']->value['unused'];?>
</td>
                                <td><?php echo $_smarty_tpl->tpl_vars['stocks']->value['used'];?>
</td>
                            </tr>
                        </table>
                    </div>
                </div>
            <?php }?>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_uet'] != 'yes') {?>
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(252,74,26,0.1);overflow:hidden;margin-bottom:20px;border:1px solid rgba(252,74,26,0.15);">
                <div style="background:linear-gradient(135deg,#fc4a1a 0%,#f7b733 100%);padding:12px 16px;">
                    <span style="color:#fff;font-weight:700;font-size:15px;"><i class="fa fa-user-times" style="margin-right:8px;"></i><?php echo Lang::T('User Expired, Today');?>
</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th><?php echo Lang::T('Username');?>
</th>
                                <th><?php echo Lang::T('Created / Expired');?>
</th>
                                <th><?php echo Lang::T('Internet Package');?>
</th>
                                <th><?php echo Lang::T('Location');?>
</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['expire']->value, 'expired');
$_smarty_tpl->tpl_vars['expired']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['expired']->value) {
$_smarty_tpl->tpl_vars['expired']->do_else = false;
?>
                                <?php $_smarty_tpl->_assignInScope('rem_exp', ((string)$_smarty_tpl->tpl_vars['expired']->value['expiration'])." ".((string)$_smarty_tpl->tpl_vars['expired']->value['time']));?>
                                <?php $_smarty_tpl->_assignInScope('rem_started', ((string)$_smarty_tpl->tpl_vars['expired']->value['recharged_on'])." ".((string)$_smarty_tpl->tpl_vars['expired']->value['recharged_time']));?>
                                <tr>
                                    <td><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/viewu/<?php echo $_smarty_tpl->tpl_vars['expired']->value['username'];?>
"><?php echo $_smarty_tpl->tpl_vars['expired']->value['username'];?>
</a></td>
                                    <td><small data-toggle="tooltip" data-placement="top"
                                            title="<?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['expired']->value['recharged_on'],$_smarty_tpl->tpl_vars['expired']->value['recharged_time']);?>
"><?php echo Lang::timeElapsed($_smarty_tpl->tpl_vars['rem_started']->value);?>
</small>
                                        /
                                        <span data-toggle="tooltip" data-placement="top"
                                            title="<?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['expired']->value['expiration'],$_smarty_tpl->tpl_vars['expired']->value['time']);?>
"><?php echo Lang::timeElapsed($_smarty_tpl->tpl_vars['rem_exp']->value);?>
</span>
                                    </td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['expired']->value['namebp'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['expired']->value['routers'];?>
</td>
                                </tr>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </tbody>
                    </table>
                </div>
                &nbsp; <?php $_smarty_tpl->_subTemplateRender("file:pagination.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
            </div>
        <?php }?>

                <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_uet'] != 'yes') {?>
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(106,17,203,0.12);overflow:hidden;margin-bottom:20px;border:1px solid rgba(106,17,203,0.15);">
                <div style="background:linear-gradient(135deg,#6a11cb 0%,#e53935 100%);padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="color:#fff;font-weight:700;font-size:15px;">
                        <i class="fa fa-plug" style="margin-right:8px;"></i>Expired PPPoE Users
                    </span>
                    <span style="background:rgba(255,255,255,0.25);border-radius:20px;color:#fff;padding:3px 10px;font-size:12px;font-weight:700;">
                        <?php echo $_smarty_tpl->tpl_vars['expire_pppoe_count']->value;?>

                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th><?php echo Lang::T('Username');?>
</th>
                                <th><?php echo Lang::T('Created / Expired');?>
</th>
                                <th><?php echo Lang::T('Internet Package');?>
</th>
                                <th><?php echo Lang::T('Location');?>
</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['expire_pppoe_today']->value, 'ep');
$_smarty_tpl->tpl_vars['ep']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ep']->value) {
$_smarty_tpl->tpl_vars['ep']->do_else = false;
?>
                                <?php $_smarty_tpl->_assignInScope('ep_exp', ((string)$_smarty_tpl->tpl_vars['ep']->value['expiration'])." ".((string)$_smarty_tpl->tpl_vars['ep']->value['time']));?>
                                <?php $_smarty_tpl->_assignInScope('ep_started', ((string)$_smarty_tpl->tpl_vars['ep']->value['recharged_on'])." ".((string)$_smarty_tpl->tpl_vars['ep']->value['recharged_time']));?>
                                <tr>
                                    <td><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/viewu/<?php echo $_smarty_tpl->tpl_vars['ep']->value['username'];?>
"><?php echo $_smarty_tpl->tpl_vars['ep']->value['username'];?>
</a></td>
                                    <td>
                                        <small data-toggle="tooltip" data-placement="top"
                                            title="<?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['ep']->value['recharged_on'],$_smarty_tpl->tpl_vars['ep']->value['recharged_time']);?>
"><?php echo Lang::timeElapsed($_smarty_tpl->tpl_vars['ep_started']->value);?>
</small>
                                        /
                                        <span data-toggle="tooltip" data-placement="top"
                                            title="<?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['ep']->value['expiration'],$_smarty_tpl->tpl_vars['ep']->value['time']);?>
"><?php echo Lang::timeElapsed($_smarty_tpl->tpl_vars['ep_exp']->value);?>
</span>
                                    </td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ep']->value['namebp'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ep']->value['routers'];?>
</td>
                                </tr>
                            <?php
}
if ($_smarty_tpl->tpl_vars['ep']->do_else) {
?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted" style="padding:16px;">
                                        <i class="fa fa-check-circle text-success" style="margin-right:6px;"></i>No expired PPPoE users found
                                    </td>
                                </tr>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php }?>
    </div>


    <div class="col-md-5">
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['router_check'] && count($_smarty_tpl->tpl_vars['routeroffs']->value) > 0) {?>
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(229,57,53,0.15);overflow:hidden;margin-bottom:16px;border:1px solid rgba(229,57,53,0.2);">
                <div style="background:linear-gradient(135deg,#e53935 0%,#e91e8c 100%);padding:12px 16px;">
                    <span style="color:#fff;font-weight:700;font-size:14px;"><i class="fa fa-exclamation-circle" style="margin-right:8px;"></i><?php echo Lang::T('Routers Offline');?>
</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tbody>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['routeroffs']->value, 'ros');
$_smarty_tpl->tpl_vars['ros']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ros']->value) {
$_smarty_tpl->tpl_vars['ros']->do_else = false;
?>
                                <tr>
                                    <td><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
routers/edit/<?php echo $_smarty_tpl->tpl_vars['ros']->value['id'];?>
" class="text-bold text-red"><?php echo $_smarty_tpl->tpl_vars['ros']->value['name'];?>
</a></td>
                                    <td data-toggle="tooltip" data-placement="top" class="text-red"
                                            title="<?php echo Lang::dateTimeFormat($_smarty_tpl->tpl_vars['ros']->value['last_seen']);?>
"><?php echo Lang::timeElapsed($_smarty_tpl->tpl_vars['ros']->value['last_seen']);?>

                                    </td>
                                </tr>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['run_date']->value) {?>
        <?php $_smarty_tpl->_assignInScope('current_time', time());?>
        <?php $_smarty_tpl->_assignInScope('run_time', strtotime($_smarty_tpl->tpl_vars['run_date']->value));?>
        <?php if ($_smarty_tpl->tpl_vars['current_time']->value-$_smarty_tpl->tpl_vars['run_time']->value > 3600) {?>
        <div style="background:linear-gradient(135deg,#fc4a1a 0%,#f7b733 100%);border-radius:14px;padding:12px 18px;margin-bottom:16px;box-shadow:0 4px 14px rgba(252,74,26,0.3);">
            <span style="color:#fff;font-weight:600;font-size:13px;"><i class="fa fa-clock-o" style="margin-right:8px;"></i><?php echo Lang::T('Cron has not run for over 1 hour. Please check your setup.');?>
</span>
        </div>
        <?php } else { ?>
        <div style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);border-radius:14px;padding:12px 18px;margin-bottom:16px;box-shadow:0 4px 14px rgba(17,153,142,0.3);">
            <span style="color:#fff;font-weight:600;font-size:13px;"><i class="fa fa-check-circle" style="margin-right:8px;"></i><?php echo Lang::T('Cron Job last ran on');?>
: <?php echo $_smarty_tpl->tpl_vars['run_date']->value;?>
</span>
        </div>
        <?php }?>
        <?php } else { ?>
        <div style="background:linear-gradient(135deg,#e53935 0%,#e91e8c 100%);border-radius:14px;padding:12px 18px;margin-bottom:16px;box-shadow:0 4px 14px rgba(229,57,53,0.3);">
            <span style="color:#fff;font-weight:600;font-size:13px;"><i class="fa fa-warning" style="margin-right:8px;"></i><?php echo Lang::T('Cron appear not been setup, please check your cron setup.');?>
</span>
        </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_pg'] != 'yes') {?>
            <div style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);border-radius:14px;box-shadow:0 6px 18px rgba(17,153,142,0.3);padding:14px 18px;margin-bottom:16px;">
                <span style="color:#fff;font-weight:700;font-size:14px;">
                    <i class="fa fa-credit-card" style="margin-right:8px;"></i>
                    <?php echo Lang::T('Payment Gateway');?>
: <?php echo str_replace(',',', ',$_smarty_tpl->tpl_vars['_c']->value['payment_gateway']);?>

                </span>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_aui'] != 'yes') {?>
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
                <div style="background:linear-gradient(135deg,#6a11cb 0%,#2575fc 100%);padding:14px 18px;">
                    <span style="color:#fff;font-weight:700;font-size:15px;"><i class="fa fa-pie-chart" style="margin-right:8px;"></i><?php echo Lang::T('All Users Insights');?>
</span>
                </div>
                <div style="padding:16px;max-width:320px;margin:0 auto;">
                    <canvas id="userRechargesChart"></canvas>
                </div>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_al'] != 'yes') {?>
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
                <div style="background:linear-gradient(135deg,#373b44 0%,#4286f4 100%);padding:14px 18px;">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
logs" style="color:#fff;font-weight:700;font-size:15px;text-decoration:none;"><i class="fa fa-list-alt" style="margin-right:8px;"></i><?php echo Lang::T('Activity Log');?>
</a>
                </div>
                <div class="panel-body">
                    <ul class="list-unstyled">
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['dlog']->value, 'dlogs');
$_smarty_tpl->tpl_vars['dlogs']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['dlogs']->value) {
$_smarty_tpl->tpl_vars['dlogs']->do_else = false;
?>
                            <li class="primary">
                                <span class="point"></span>
                                <span class="time small text-muted"><?php echo Lang::timeElapsed($_smarty_tpl->tpl_vars['dlogs']->value['date'],true);?>
</span>
                                <p><?php echo $_smarty_tpl->tpl_vars['dlogs']->value['description'];?>
</p>
                            </li>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </ul>
                </div>
            </div>
        <?php }?>

                <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
            <div style="background:linear-gradient(135deg,#00b4db 0%,#0083b0 100%);padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
                <span>
                    <i class="fa fa-wifi" style="margin-right:7px;color:#74b9ff;"></i>
                    <strong>Top 5 Hotspot Downloaders</strong>
                </span>
                <span style="font-size:.75rem;background:rgba(255,255,255,.12);padding:2px 10px;border-radius:12px;color:#b2bec3;">
                    <?php echo $_smarty_tpl->tpl_vars['top_downloaders_month']->value;?>

                </span>
            </div>
            <?php if (smarty_modifier_count($_smarty_tpl->tpl_vars['top_hotspot_downloaders']->value) > 0) {?>
            <div class="table-responsive" style="margin:0;">
                <table class="table table-condensed" style="margin:0;font-size:.85rem;">
                    <thead>
                        <tr style="background:#f5f7fa;">
                            <th style="padding:7px 14px;font-size:.7rem;text-transform:uppercase;color:#636e72;border-bottom:2px solid #dde1e7;">#</th>
                            <th style="padding:7px 14px;font-size:.7rem;text-transform:uppercase;color:#636e72;border-bottom:2px solid #dde1e7;">Customer</th>
                            <th style="padding:7px 14px;font-size:.7rem;text-transform:uppercase;color:#636e72;border-bottom:2px solid #dde1e7;">Download</th>
                            <th style="padding:7px 14px;font-size:.7rem;text-transform:uppercase;color:#636e72;border-bottom:2px solid #dde1e7;">Upload</th>
                            <th style="padding:7px 14px;font-size:.7rem;text-transform:uppercase;color:#636e72;border-bottom:2px solid #dde1e7;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['top_hotspot_downloaders']->value, 'd', false, 'rank');
$_smarty_tpl->tpl_vars['d']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['rank']->value => $_smarty_tpl->tpl_vars['d']->value) {
$_smarty_tpl->tpl_vars['d']->do_else = false;
?>
                        <tr style="transition:background .15s;" onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background=''">
                            <td style="padding:8px 14px;color:#b2bec3;font-weight:700;"><?php echo $_smarty_tpl->tpl_vars['rank']->value+1;?>
</td>
                            <td style="padding:8px 14px;">
                                <strong><?php if ($_smarty_tpl->tpl_vars['d']->value['fullname']) {
echo $_smarty_tpl->tpl_vars['d']->value['fullname'];
} else {
echo $_smarty_tpl->tpl_vars['d']->value['username'];
}?></strong>
                                <small class="text-muted" style="display:block;"><?php echo $_smarty_tpl->tpl_vars['d']->value['username'];?>
</small>
                            </td>
                            <td style="padding:8px 14px;color:#2980b9;font-weight:600;"><?php echo $_smarty_tpl->tpl_vars['d']->value['download_fmt'];?>
</td>
                            <td style="padding:8px 14px;color:#27ae60;font-weight:600;"><?php echo $_smarty_tpl->tpl_vars['d']->value['upload_fmt'];?>
</td>
                            <td style="padding:8px 14px;color:#2c3e50;font-weight:700;"><?php echo $_smarty_tpl->tpl_vars['d']->value['total_fmt'];?>
</td>
                        </tr>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </tbody>
                </table>
            </div>
            <?php } else { ?>
            <div class="panel-body" style="text-align:center;color:#b2bec3;padding:20px;">
                <i class="fa fa-inbox" style="font-size:1.5rem;display:block;margin-bottom:6px;"></i>
                No hotspot usage recorded this month
            </div>
            <?php }?>
        </div>

                <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
            <div style="background:linear-gradient(135deg,#a18cd1 0%,#4776e6 100%);padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
                <span>
                    <i class="fa fa-plug" style="margin-right:7px;color:#55efc4;"></i>
                    <strong>Top 5 PPPoE Downloaders</strong>
                </span>
                <span style="font-size:.75rem;background:rgba(255,255,255,.12);padding:2px 10px;border-radius:12px;color:#b2bec3;">
                    <?php echo $_smarty_tpl->tpl_vars['top_downloaders_month']->value;?>

                </span>
            </div>
            <?php if (smarty_modifier_count($_smarty_tpl->tpl_vars['top_pppoe_downloaders']->value) > 0) {?>
            <div class="table-responsive" style="margin:0;">
                <table class="table table-condensed" style="margin:0;font-size:.85rem;">
                    <thead>
                        <tr style="background:#f5f7fa;">
                            <th style="padding:7px 14px;font-size:.7rem;text-transform:uppercase;color:#636e72;border-bottom:2px solid #dde1e7;">#</th>
                            <th style="padding:7px 14px;font-size:.7rem;text-transform:uppercase;color:#636e72;border-bottom:2px solid #dde1e7;">Customer</th>
                            <th style="padding:7px 14px;font-size:.7rem;text-transform:uppercase;color:#636e72;border-bottom:2px solid #dde1e7;">Download</th>
                            <th style="padding:7px 14px;font-size:.7rem;text-transform:uppercase;color:#636e72;border-bottom:2px solid #dde1e7;">Upload</th>
                            <th style="padding:7px 14px;font-size:.7rem;text-transform:uppercase;color:#636e72;border-bottom:2px solid #dde1e7;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['top_pppoe_downloaders']->value, 'd', false, 'rank');
$_smarty_tpl->tpl_vars['d']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['rank']->value => $_smarty_tpl->tpl_vars['d']->value) {
$_smarty_tpl->tpl_vars['d']->do_else = false;
?>
                        <tr style="transition:background .15s;" onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background=''">
                            <td style="padding:8px 14px;color:#b2bec3;font-weight:700;"><?php echo $_smarty_tpl->tpl_vars['rank']->value+1;?>
</td>
                            <td style="padding:8px 14px;">
                                <strong><?php if ($_smarty_tpl->tpl_vars['d']->value['fullname']) {
echo $_smarty_tpl->tpl_vars['d']->value['fullname'];
} else {
echo $_smarty_tpl->tpl_vars['d']->value['pppoe_username'];
}?></strong>
                                <small class="text-muted" style="display:block;"><?php echo $_smarty_tpl->tpl_vars['d']->value['pppoe_username'];?>
</small>
                            </td>
                            <td style="padding:8px 14px;color:#2980b9;font-weight:600;"><?php echo $_smarty_tpl->tpl_vars['d']->value['download_fmt'];?>
</td>
                            <td style="padding:8px 14px;color:#27ae60;font-weight:600;"><?php echo $_smarty_tpl->tpl_vars['d']->value['upload_fmt'];?>
</td>
                            <td style="padding:8px 14px;color:#2c3e50;font-weight:700;"><?php echo $_smarty_tpl->tpl_vars['d']->value['total_fmt'];?>
</td>
                        </tr>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </tbody>
                </table>
            </div>
            <?php } else { ?>
            <div class="panel-body" style="text-align:center;color:#b2bec3;padding:20px;">
                <i class="fa fa-inbox" style="font-size:1.5rem;display:block;margin-bottom:6px;"></i>
                No PPPoE usage recorded this month
            </div>
            <?php }?>
        </div>


</div>

<style>
    .small-box {
        border-radius: 8px;
        transition: transform 0.2s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        background: linear-gradient(135deg, var(--start-color) 0%, var(--end-color) 100%);
        border: none;
        margin-bottom: 15px;
    }
    .small-box:hover {
        transform: translateY(-2px);
    }
    .income-today-box {
        --start-color: #00B4DB;
        --end-color: #0083B0;
    }
    .income-month-box {
        --start-color: #00b09b;
        --end-color: #96c93d;
    }
    .active-expired-box {
        --start-color: #ff5f6d;
        --end-color: #ffc371;
    }
    .online-pppoe-box {
        --start-color: #396afc;
        --end-color: #2948ff;
    }
    .online-hotspot-box {
        --start-color: #fc4a1a;
        --end-color: #f7b733;
    }
    .total-online-box {
        --start-color: #654ea3;
        --end-color: #947bd3;
    }
    .total-customers-box {
        --start-color: #f46b45;
        --end-color: #eea849;
    }
    .small-box .inner {
        padding: 10px 15px;
    }
    .small-box h4 {
        color: white;
        font-size: 24px !important;
        margin: 0;
        font-weight: bold;
    }
    .small-box p {
        color: rgba(255,255,255,0.95);
        margin: 5px 0;
    }
    .small-box .icon {
        position: absolute;
        right: 10px;
        top: 10px;
        opacity: 0.3;
        font-size: 24px;
    }
    .small-box .small-box-footer {
        background: rgba(0,0,0,0.1);
        color: white;
        padding: 3px 0;
        text-align: center;
        display: block;
    }
    .small-box .small-box-footer:hover {
        background: rgba(0,0,0,0.15);
    }
    
    /* Styles for the expired users section */
    .panel-danger .panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .panel-danger .panel-heading i.fa-user-times {
        margin-right: 5px;
    }
    
    .panel-danger .table-condensed {
        margin-bottom: 0;
    }
    
    .panel-danger .text-danger {
        font-weight: bold;
    }
    
    .panel-danger .btn-success {
        margin-left: 5px;
    }
</style>

<?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"><?php echo '</script'; ?>
>

<?php echo '<script'; ?>
 type="text/javascript">
    <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_mrc'] != 'yes') {?>
        
            document.addEventListener("DOMContentLoaded", function() {
                var counts = JSON.parse('<?php echo json_encode($_smarty_tpl->tpl_vars['monthlyRegistered']->value);?>
');

                var monthNames = [
                    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                ];

                var labels = [];
                var data = [];

                for (var i = 1; i <= 12; i++) {
                    var month = counts.find(count => count.date === i);
                    labels.push(month ? monthNames[i - 1] : monthNames[i - 1].substring(0, 3));
                    data.push(month ? month.count : 0);
                }

                var ctx = document.getElementById('chart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Registered Members',
                            data: data,
                            backgroundColor: function(ctx) {
                                var gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
                                gradient.addColorStop(0, 'rgba(102,126,234,0.85)');
                                gradient.addColorStop(1, 'rgba(118,75,162,0.3)');
                                return gradient;
                            },
                            borderColor: 'rgba(102,126,234,1)',
                            borderWidth: 2,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            }
                        }
                    }
                });
            });
        
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_tmc'] != 'yes') {?>
        
            document.addEventListener("DOMContentLoaded", function() {
                var monthlySales = JSON.parse('<?php echo json_encode($_smarty_tpl->tpl_vars['monthlySales']->value);?>
');

                var monthNames = [
                    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                ];

                var labels = [];
                var data = [];

                for (var i = 1; i <= 12; i++) {
                    var month = findMonthData(monthlySales, i);
                    labels.push(month ? monthNames[i - 1] : monthNames[i - 1].substring(0, 3));
                    data.push(month ? month.totalSales : 0);
                }

                var ctx = document.getElementById('salesChart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Monthly Sales',
                            data: data,
                            backgroundColor: function(ctx) {
                                var gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
                                gradient.addColorStop(0, 'rgba(17,153,142,0.85)');
                                gradient.addColorStop(1, 'rgba(56,239,125,0.3)');
                                return gradient;
                            },
                            borderColor: 'rgba(17,153,142,1)',
                            borderWidth: 2,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            }
                        }
                    }
                });
            });

            function findMonthData(monthlySales, month) {
                for (var i = 0; i < monthlySales.length; i++) {
                    if (monthlySales[i].month === month) {
                        return monthlySales[i];
                    }
                }
                return null;
            }
        
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_tws'] != 'yes') {?>
        
            document.addEventListener("DOMContentLoaded", function() {
                var weeklySales = JSON.parse('<?php echo json_encode($_smarty_tpl->tpl_vars['weeklySales']->value);?>
');

                var labels = [];
                var data = [];

                for (var i = 0; i < weeklySales.length; i++) {
                    labels.push(weeklySales[i].week_label);
                    data.push(weeklySales[i].totalSales);
                }

                var ctx = document.getElementById('weeklySalesChart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Weekly Sales',
                            data: data,
                            backgroundColor: function(ctx) {
                                var gradient = ctx.chart.ctx.createLinearGradient(0, 0, 0, 300);
                                gradient.addColorStop(0, 'rgba(249,83,198,0.85)');
                                gradient.addColorStop(1, 'rgba(185,29,115,0.3)');
                                return gradient;
                            },
                            borderColor: 'rgba(249,83,198,1)',
                            borderWidth: 2,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 10
                                    },
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            }
                        }
                    }
                });
            });
        
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_aui'] != 'yes') {?>
        
            document.addEventListener("DOMContentLoaded", function() {
                // Get the data from PHP and assign it to JavaScript variables
                var u_act = '<?php echo $_smarty_tpl->tpl_vars['u_act']->value;?>
';
                var c_all = '<?php echo $_smarty_tpl->tpl_vars['c_all']->value;?>
';
                var u_all = '<?php echo $_smarty_tpl->tpl_vars['u_all']->value;?>
';
                //lets calculate the inactive users as reported
                var expired = u_all - u_act;
                var inactive = c_all - u_all;
                if (inactive < 0) {
                    inactive = 0;
                }
                // Create the chart data
                var data = {
                    labels: ['Active Users', 'Expired Users', 'Inactive Users'],
                    datasets: [{
                        label: 'User Recharges',
                        data: [parseInt(u_act), parseInt(expired), parseInt(inactive)],
                        backgroundColor: ['#10b981', '#ef4444', '#6366f1'],
                        borderColor: '#fff',
                        borderWidth: 3,
                        hoverBorderColor: '#fff',
                        hoverBorderWidth: 4,
                        hoverOffset: 8
                    }]
                };

                // Create chart options
                var options = {
                    responsive: true,
                    aspectRatio: 1.3,
                    cutout: '45%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 16,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { size: 12, weight: '600' },
                                color: '#475569'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: { size: 13, weight: '700' },
                            bodyFont: { size: 12 },
                            padding: 12,
                            cornerRadius: 10,
                            displayColors: true,
                            boxPadding: 6
                        }
                    }
                };

                // Get the canvas element and create the chart
                var ctx = document.getElementById('userRechargesChart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'doughnut',
                    data: data,
                    options: options
                });
            });
        
    <?php }
echo '</script'; ?>
>

<?php echo '<script'; ?>
 type="text/javascript">
    function filterDashboard() {
        var routerId = $('#router_filter').val();
        console.log('Filtering for router:', routerId);
        
        $.ajax({
            url: '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
dashboard/filter',
            type: 'POST',
            data: { router_id: routerId },
            dataType: 'json',
            success: function(data) {
                // Update cards by their unique IDs
                $('#income-today-val').text(data.income_today);
                $('#income-month-val').text(data.income_month);
                $('#active-expired-val').text(data.active_users + '/' + data.expired_users);
                $('#pppoe-online-val').text(data.online_users);
                $('#online-hotspot-users').text(data.hotspot_users);
                $('#total-online-users').text(data.total_online);
                $('#total-pppoe-val').text(data.total_pppoe);
                $('#total-customers-val').text(data.total_customers);
                $('#expired-pppoe-val').text(data.expired_pppoe);
                $('#expired-hotspot-val').text(data.expired_hotspot);
                $('#total-expired-val').text(data.total_expired);
                
                console.log('Dashboard filtered successfully');
            },
            error: function(xhr, status, error) {
                console.error('Error filtering dashboard:', error);
                alert('Error filtering dashboard data');
            }
        });
    }
    
    // Function to refresh data usage
    function refreshDataUsage() {
        // Show loading state
        $('button[onclick="refreshDataUsage()"]').html('<i class="fa fa-spinner fa-spin mr-1"></i><span class="hidden sm:inline">Loading...</span>');
        $('button[onclick="refreshDataUsage()"]').prop('disabled', true);
        
        $.ajax({
            url: '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
dashboard/refresh-data-usage',
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    alert('Error: ' + data.error);
                } else {
                    // Update the display values using the new structure
                    $('.bg-gradient-to-br .amount').each(function(index) {
                        switch(index) {
                            case 0:
                                $(this).text(data.total_rx);
                                break;
                            case 1:
                                $(this).text(data.total_tx);
                                break;
                            case 2:
                                $(this).text(data.total_usage);
                                break;
                        }
                    });
                    
                    // Update router count and last updated in the new structure
                    var routerInfoText = data.active_routers + ' Active Routers';
                    if (data.last_updated) {
                        routerInfoText += ' | Last Updated: ' + data.last_updated;
                    }
                    
                    // Update the router info text
                    $('.text-indigo-100.text-xs').first().html('<i class="fa fa-router mr-1"></i>' + routerInfoText);
                    
                    // Update router details if they exist
                    if (data.router_details && data.router_details.length > 0) {
                        var detailsHtml = '<h6 class="text-white font-medium mb-3 flex items-center text-sm"><i class="fa fa-info-circle mr-2"></i>Router WAN Interface Details:</h6>';
                        detailsHtml += '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">';
                        
                        data.router_details.forEach(function(router) {
                            var statusBadge = router.wan_found ? 
                                '<span class="inline-flex items-center px-1 py-0.5 bg-green-500 bg-opacity-20 text-green-200 rounded text-xs"><i class="fa fa-check mr-1"></i>Confirmed</span>' :
                                '<span class="inline-flex items-center px-1 py-0.5 bg-yellow-500 bg-opacity-20 text-yellow-200 rounded text-xs"><i class="fa fa-exclamation-triangle mr-1"></i>Auto-detected</span>';
                            
                            detailsHtml += '<div class="bg-white bg-opacity-5 rounded-lg p-2 text-xs">';
                            detailsHtml += '<div class="font-medium text-white mb-1">' + router.name + '</div>';
                            detailsHtml += '<div class="text-indigo-100 text-xs space-y-1">';
                            detailsHtml += '<div>RX: ' + router.rx + '</div>';
                            detailsHtml += '<div>TX: ' + router.tx + '</div>';
                            detailsHtml += '<div>Total: ' + router.total + '</div>';
                            detailsHtml += '<div class="flex items-center">' + statusBadge + '</div>';
                            detailsHtml += '</div></div>';
                        });
                        detailsHtml += '</div>';
                        $('#router-details').html(detailsHtml);
                        
                        // Show the View Details button if it's hidden
                        $('button[onclick="toggleRouterDetails()"]').show();
                    }
                }
                
                // Restore button
                $('button[onclick="refreshDataUsage()"]').html('<i class="fa fa-refresh mr-1"></i><span class="hidden sm:inline">Refresh</span>');
                $('button[onclick="refreshDataUsage()"]').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                console.error('Error refreshing data usage:', error);
                alert('Error refreshing data usage: ' + error);
                
                // Restore button
                $('button[onclick="refreshDataUsage()"]').html('<i class="fa fa-refresh mr-1"></i><span class="hidden sm:inline">Refresh</span>');
                $('button[onclick="refreshDataUsage()"]').prop('disabled', false);
            }
        });
    }
    
    // Function to toggle router details
    function toggleRouterDetails() {
        $('#router-details').toggle();
    }
    
    // Store original values when page loads
    $(document).ready(function() {
        $('.amount').each(function() {
            var $this = $(this);
            $this.data('original-value', $this.html());
        });
        
        // Add change event listener to router filter
        $('#router_filter').on('change', filterDashboard);

        // ── Real-time online user polling (every 30 seconds) ──────────────
        function pollOnlineCounts() {
            var routerId = $('#router_filter').val() || 'all';
            // Dim the live dot while fetching
            $('#hotspot-live-dot').css('background', 'rgba(255,255,255,0.4)');

            function flashUpdate($el, newVal) {
                var old = $el.text();
                if (String(old) !== String(newVal)) {
                    $el.animate({ opacity: 0 }, 150, function() {
                        $el.text(newVal).animate({ opacity: 1 }, 150);
                    });
                }
            }

            // Use the proven hotspot_stats endpoint — queries MikroTik live
            $.ajax({
                url: '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
onlineusers/hotspot_stats/' + routerId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var hotspotCount = parseInt(data.total_users) || 0;
                    var pppoeCount   = parseInt($('#pppoe-online-val').text()) || 0;
                    flashUpdate($('#online-hotspot-users'), hotspotCount);
                    flashUpdate($('#total-online-users'),   hotspotCount + pppoeCount);
                    // Restore live dot and show timestamp
                    $('#hotspot-live-dot').css('background', '#4ade80');
                    var now = new Date();
                    var h = now.getHours().toString().padStart(2,'0');
                    var m = now.getMinutes().toString().padStart(2,'0');
                    var s = now.getSeconds().toString().padStart(2,'0');
                    $('#hotspot-last-updated').text('Updated ' + h + ':' + m + ':' + s);
                },
                error: function() {
                    $('#hotspot-live-dot').css('background', '#f87171'); // red on error
                }
            });
        }

        // Run immediately on load, then every 10 seconds
        pollOnlineCounts();
        setInterval(pollOnlineCounts, 10000);
        // ─────────────────────────────────────────────────────────────────
    });
<?php echo '</script'; ?>
>

<?php echo '<script'; ?>
 src="https://code.jquery.com/jquery-3.6.0.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
>
    $(document).ready(function() {
        $.ajax({
            url: "<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
onlineusers/sms_balance",
            method: 'GET',
            dataType: 'json',
            success: function(response) {
            if (response.status === 'success' && response.data && response.data.remaining_balance) {
                $('#sms-balance').text(response.data.remaining_balance);
            } else if (response.message) {
                $('#sms-balance').text('Error: ' + response.message);
            } else {
                $('#sms-balance').text('Unknown error');
            }
        },
            error: function() {
                $('#sms-balance').text('Failed to fetch balance');
            }
        });
    });
    <?php echo '</script'; ?>
>


<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
