<?php
/* Smarty version 4.5.3, created on 2025-08-17 10:05:04
  from '/var/www/html/ISP/ui/ui/dashboard.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a17f209ee046_89657550',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f88e6118c44d75061d7b360de7b5e6f7ce51d401' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/dashboard.tpl',
      1 => 1755230002,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:sections/header.tpl' => 1,
    'file:admin_server_stats.tpl' => 1,
    'file:pagination.tpl' => 2,
    'file:sections/footer.tpl' => 1,
  ),
),false)) {
function content_68a17f209ee046_89657550 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="greeting-box mb-3">
    <div class="greeting-content">
        <h3 id="timeBasedGreeting" class="text-gradient"></h3>
        <div class="greeting-icon">
            <i id="weatherIcon" class="ion"></i>
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
    } else if (hour >= 12 && hour < 17) {
        greeting = 'Good Afternoon';
        iconClass = 'ion-ios-sunny-outline';
    } else if (hour >= 17 && hour < 20) {
        greeting = 'Good Evening';
        iconClass = 'ion-ios-moon-outline';
    } else {
        greeting = 'Good Night';
        iconClass = 'ion-ios-moon';
    }
    
    document.getElementById('timeBasedGreeting').textContent = greeting + ', ' + '<?php echo $_smarty_tpl->tpl_vars['_c']->value['CompanyName'];?>
';
    document.getElementById('weatherIcon').className = 'ion ' + iconClass;
}

// Update greeting immediately and then every minute
updateGreeting();
setInterval(updateGreeting, 60000);
<?php echo '</script'; ?>
>

<style>
.greeting-box {
    background: linear-gradient(145deg, #ffffff, #f0f0f0);
    padding: 20px;
    border-radius: 15px;
    box-shadow: 8px 8px 15px #d1d1d1, -8px -8px 15px #ffffff;
    margin: 0 15px 20px;
    transform: translateY(0);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.greeting-box:hover {
    transform: translateY(-5px);
    box-shadow: 12px 12px 20px #d1d1d1, -12px -12px 20px #ffffff;
}

.greeting-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.greeting-box h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    background: linear-gradient(45deg, #2193b0, #6dd5ed);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

.greeting-icon {
    font-size: 22px;
    color: #2193b0;
    margin-left: 15px;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-5px); }
    100% { transform: translateY(0px); }
}

/* Modern Dashboard Card Animations */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
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
    
    .greeting-icon {
        font-size: 20px;
        margin-left: 10px;
    }
    
    /* 2-column layout on mobile (col-xs-6) */
    .col-xs-6 {
        width: 50% !important;
        float: left !important;
        padding-left: 6px !important;
        padding-right: 6px !important;
        margin-bottom: 0.75rem !important;
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
    
    .greeting-icon {
        font-size: 18px;
        margin-left: 8px;
    }
    
    /* Maintain 2-column layout with better spacing */
    .col-lg-3,
    .col-lg-4,
    .col-md-6,
    .col-sm-6,
    .col-xs-6 {
        width: 50% !important;
        float: left !important;
        padding-left: 5px !important;
        padding-right: 5px !important;
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center space-x-4">
                <label for="router_filter" class="text-sm font-medium text-gray-700 whitespace-nowrap">
                    <i class="fa fa-filter mr-2 text-blue-500"></i>
                    <?php echo Lang::T('Filter by Router');?>
:
                </label>
                <select class="form-control bg-gray-50 border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" id="router_filter" onchange="filterDashboard()">
                    <option value="all"><?php echo Lang::T('All Routers');?>
</option>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['routers']->value, 'router');
$_smarty_tpl->tpl_vars['router']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['router']->value) {
$_smarty_tpl->tpl_vars['router']->do_else = false;
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['router']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['router']->value['name'];?>
</option>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php if (in_array($_smarty_tpl->tpl_vars['_admin']->value['user_type'],array('SuperAdmin','Admin','Report'))) {?>
        <!-- Income Today Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div class="bg-gradient-to-r from-cyan-500 to-blue-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 text-white transform hover:-translate-y-1 card-hover-effect">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-blue-100 text-xs font-medium mb-1"><?php echo Lang::T('Income Today');?>
</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="text-xs"><?php echo $_smarty_tpl->tpl_vars['_c']->value['currency_code'];?>
</span>
                            <span class="amount"><?php echo number_format($_smarty_tpl->tpl_vars['iday']->value,0,$_smarty_tpl->tpl_vars['_c']->value['dec_point'],$_smarty_tpl->tpl_vars['_c']->value['thousands_sep']);?>
</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-cash text-lg"></i>
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
            <div class="bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 text-white transform hover:-translate-y-1 card-hover-effect">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-green-100 text-xs font-medium mb-1"><?php echo Lang::T('Income This Month');?>
</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="text-xs"><?php echo $_smarty_tpl->tpl_vars['_c']->value['currency_code'];?>
</span>
                            <span class="amount"><?php echo number_format($_smarty_tpl->tpl_vars['imonth']->value,0,$_smarty_tpl->tpl_vars['_c']->value['dec_point'],$_smarty_tpl->tpl_vars['_c']->value['thousands_sep']);?>
</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-stats-bars text-lg"></i>
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

        <!-- Active/Expired Users Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 text-white transform hover:-translate-y-1 card-hover-effect">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-orange-100 text-xs font-medium mb-1"><?php echo Lang::T('Active');?>
/<?php echo Lang::T('Expired');?>
</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="amount"><?php echo $_smarty_tpl->tpl_vars['u_act']->value;?>
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
            <div class="bg-gradient-to-r from-purple-500 to-indigo-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 text-white transform hover:-translate-y-1 card-hover-effect">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-purple-100 text-xs font-medium mb-1"><?php echo Lang::T('Online PPPoE Users');?>
</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="amount"><?php echo $_smarty_tpl->tpl_vars['online_users']->value;?>
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
</div>

<div class="row">
    <?php if (in_array($_smarty_tpl->tpl_vars['_admin']->value['user_type'],array('SuperAdmin','Admin','Report'))) {?>
        <!-- Online Hotspot Users Card -->
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 text-white transform hover:-translate-y-1 card-hover-effect">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-amber-100 text-xs font-medium mb-1"><?php echo Lang::T('Online Hotspot Users');?>
</p>
                        <h4 class="text-xl font-bold" id="online-hotspot-users">0</h4>
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
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 text-white transform hover:-translate-y-1 card-hover-effect">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-blue-100 text-xs font-medium mb-1"><?php echo Lang::T('Total Online Users');?>
</p>
                        <h4 class="text-xl font-bold" id="total-online-users">0</h4>
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

        <!-- Total Customers Card -->
        <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 mb-3">
            <div class="bg-gradient-to-r from-teal-500 to-cyan-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 text-white transform hover:-translate-y-1 card-hover-effect">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-teal-100 text-xs font-medium mb-1"><?php echo Lang::T('Total Customers');?>
</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount"><?php echo $_smarty_tpl->tpl_vars['c_all']->value;?>
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
</div>

<div class="row">
    <?php if (in_array($_smarty_tpl->tpl_vars['_admin']->value['user_type'],array('SuperAdmin','Admin','Report'))) {?>
        <!-- Expired PPPoE Users Card -->
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 text-white transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-yellow-100 text-xs font-medium mb-1"><?php echo Lang::T('Expired PPPoE Users');?>
</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount"><?php echo $_smarty_tpl->tpl_vars['expired_pppoe']->value;?>
</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-network text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/list?status=off&type=PPPOE" class="inline-flex items-center text-yellow-100 hover:text-white text-xs font-medium">
                        <?php echo Lang::T('View Details');?>
 
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Expired Hotspot Users Card -->
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div class="bg-gradient-to-r from-red-500 to-pink-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 text-white transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-red-100 text-xs font-medium mb-1"><?php echo Lang::T('Expired Hotspot Users');?>
</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount"><?php echo $_smarty_tpl->tpl_vars['expired_hotspot']->value;?>
</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-wifi text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/list?status=off&type=Hotspot" class="inline-flex items-center text-red-100 hover:text-white text-xs font-medium">
                        <?php echo Lang::T('View Details');?>
 
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Expired Users Card -->
        <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 mb-3">
            <div class="bg-gradient-to-r from-gray-500 to-slate-600 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 text-white transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-gray-100 text-xs font-medium mb-1"><?php echo Lang::T('Total Expired Users');?>
</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount"><?php echo $_smarty_tpl->tpl_vars['total_expired']->value;?>
</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-ios-people text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/list?status=off" class="inline-flex items-center text-gray-100 hover:text-white text-xs font-medium">
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
            <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-800 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 text-white mb-3">
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
                        <p class="text-indigo-100 text-xs font-medium">Total Downloaded</p>
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
                        <p class="text-indigo-100 text-xs font-medium">Total Uploaded</p>
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
                        <p class="text-indigo-100 text-xs font-medium">Total Data Usage</p>
                    </div>
                </div>

                <!-- Router Info -->
                <div class="border-t border-white border-opacity-20 pt-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center text-indigo-100 text-xs">
                            <i class="fa fa-router mr-1"></i>
                            <span><?php if ((isset($_smarty_tpl->tpl_vars['total_data_usage']->value['active_routers']))) {
echo $_smarty_tpl->tpl_vars['total_data_usage']->value['active_routers'];
} else { ?>0<?php }?> Active Routers</span>
                            <?php if ((isset($_smarty_tpl->tpl_vars['total_data_usage']->value['last_updated']))) {?>
                                <span class="mx-2 hidden sm:inline">|</span>
                                <span class="hidden sm:inline">Last Updated: <?php echo $_smarty_tpl->tpl_vars['total_data_usage']->value['last_updated'];?>
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

<div class="row">
    <div class="col-md-7">
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_mrc'] != 'yes') {?>
            <div class="box box-solid ">
                <div class="box-header">
                    <i class="fa fa-th"></i>

                    <h3 class="box-title"><?php echo Lang::T('Monthly Registered Customers');?>
</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
dashboard&refresh" class="btn bg-teal btn-sm"><i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </div>
                <div class="box-body border-radius-none">
                    <canvas class="chart" id="chart" style="height: 250px;"></canvas>
                </div>
            </div>
        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_tms'] != 'yes') {?>
            <div class="box box-solid ">
                <div class="box-header">
                    <i class="fa fa-inbox"></i>

                    <h3 class="box-title"><?php echo Lang::T('Total Monthly Sales');?>
</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
dashboard&refresh" class="btn bg-teal btn-sm"><i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </div>
                <div class="box-body border-radius-none">
                    <canvas class="chart" id="salesChart" style="height: 250px;"></canvas>
                </div>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['disable_voucher'] != 'yes' && $_smarty_tpl->tpl_vars['stocks']->value['unused'] > 0 || $_smarty_tpl->tpl_vars['stocks']->value['used'] > 0) {?>
            <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_vs'] != 'yes') {?>
                <div class="panel panel-primary mb20 panel-hovered project-stats table-responsive">
                    <div class="panel-heading">Vouchers Stock</div>
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
            <div class="panel panel-warning mb20 panel-hovered project-stats table-responsive">
                <div class="panel-heading"><?php echo Lang::T('User Expired, Today');?>
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
            
            <!-- All Expired Users Section -->
            <div class="panel panel-danger mb20 panel-hovered project-stats table-responsive">
                <div class="panel-heading">
                    <i class="fa fa-user-times"></i> <?php echo Lang::T('All Expired Users');?>

                    <div class="pull-right">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/list?filter=Inactive" class="btn btn-xs btn-primary">
                            <i class="fa fa-list"></i> <?php echo Lang::T('View All');?>

                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th><?php echo Lang::T('Username');?>
</th>
                                <th><?php echo Lang::T('Expired On');?>
</th>
                                <th><?php echo Lang::T('Internet Package');?>
</th>
                                <th><?php echo Lang::T('Location');?>
</th>
                                <th><?php echo Lang::T('Actions');?>
</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['all_expired']->value, 'expired');
$_smarty_tpl->tpl_vars['expired']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['expired']->value) {
$_smarty_tpl->tpl_vars['expired']->do_else = false;
?>
                                <?php $_smarty_tpl->_assignInScope('rem_exp', ((string)$_smarty_tpl->tpl_vars['expired']->value['expiration'])." ".((string)$_smarty_tpl->tpl_vars['expired']->value['time']));?>
                                <tr>
                                    <td><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/viewu/<?php echo $_smarty_tpl->tpl_vars['expired']->value['username'];?>
"><?php echo $_smarty_tpl->tpl_vars['expired']->value['username'];?>
</a></td>
                                    <td>
                                        <span class="text-danger" data-toggle="tooltip" data-placement="top"
                                            title="<?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['expired']->value['expiration'],$_smarty_tpl->tpl_vars['expired']->value['time']);?>
"><?php echo Lang::timeElapsed($_smarty_tpl->tpl_vars['rem_exp']->value);?>
</span>
                                    </td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['expired']->value['namebp'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['expired']->value['routers'];?>
</td>
                                    <td>
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/view/<?php echo $_smarty_tpl->tpl_vars['expired']->value['customer_id'];?>
" class="btn btn-xs btn-info">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/recharge/<?php echo $_smarty_tpl->tpl_vars['expired']->value['customer_id'];?>
/<?php echo $_smarty_tpl->tpl_vars['expired']->value['plan_id'];?>
?token=<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
" class="btn btn-xs btn-success">
                                            <i class="fa fa-refresh"></i> <?php echo Lang::T('Recharge');?>

                                        </a>
                                    </td>
                                </tr>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </tbody>
                    </table>
                </div>
                &nbsp; <?php $_smarty_tpl->_subTemplateRender("file:pagination.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
            </div>
        <?php }?>
    </div>


    <div class="col-md-5">
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['router_check'] && count($_smarty_tpl->tpl_vars['routeroffs']->value) > 0) {?>
            <div class="panel panel-danger">
                <div class="panel-heading text-bold"><?php echo Lang::T('Routers Offline');?>
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
        <div class="panel panel-cron-warning panel-hovered mb20 activities">
            <div class="panel-heading"><i class="fa fa-clock-o"></i> &nbsp; <?php echo Lang::T('Cron has not run for over 1 hour. Please
                check your setup.');?>
</div>
        </div>
        <?php } else { ?>
        <div class="panel panel-cron-success panel-hovered mb20 activities">
            <div class="panel-heading"><?php echo Lang::T('Cron Job last ran on');?>
: <?php echo $_smarty_tpl->tpl_vars['run_date']->value;?>
</div>
        </div>
        <?php }?>
        <?php } else { ?>
        <div class="panel panel-cron-danger panel-hovered mb20 activities">
            <div class="panel-heading"><i class="fa fa-warning"></i> &nbsp; <?php echo Lang::T('Cron appear not been setup, please check
                your cron setup.');?>
</div>
        </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_pg'] != 'yes') {?>
            <div class="panel panel-success panel-hovered mb20 activities">
                <div class="panel-heading"><?php echo Lang::T('Payment Gateway');?>
: <?php echo str_replace(',',', ',$_smarty_tpl->tpl_vars['_c']->value['payment_gateway']);?>

                </div>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_aui'] != 'yes') {?>
            <div class="panel panel-info panel-hovered mb20 activities">
                <div class="panel-heading"><?php echo Lang::T('All Users Insights');?>
</div>
                <div class="panel-body">
                    <canvas id="userRechargesChart"></canvas>
                </div>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_al'] != 'yes') {?>
            <div class="panel panel-info panel-hovered mb20 activities">
                <div class="panel-heading"><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
logs"><?php echo Lang::T('Activity Log');?>
</a></div>
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
                            backgroundColor: 'rgba(0, 0, 255, 0.5)',
                            borderColor: 'rgba(0, 0, 255, 0.7)',
                            borderWidth: 1
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
                            backgroundColor: 'rgba(2, 10, 242)', // Customize the background color
                            borderColor: 'rgba(255, 99, 132, 1)', // Customize the border color
                            borderWidth: 1
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
                        backgroundColor: ['rgba(4, 191, 13)', 'rgba(191, 35, 4)', 'rgba(0, 0, 255, 0.5'],
                        borderColor: ['rgba(0, 255, 0, 1)', 'rgba(255, 99, 132, 1)', 'rgba(0, 0, 255, 0.7'],
                        borderWidth: 1
                    }]
                };

                // Create chart options
                var options = {
                    responsive: true,
                    aspectRatio: 1,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 15
                            }
                        }
                    }
                };

                // Get the canvas element and create the chart
                var ctx = document.getElementById('userRechargesChart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'pie',
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
                // Update cards using the new structure - find by gradient backgrounds
                $('.bg-gradient-to-r.from-cyan-500 .amount').text(data.income_today);
                $('.bg-gradient-to-r.from-green-500 .amount').text(data.income_month);
                $('.bg-gradient-to-r.from-orange-500 .amount').text(data.active_users + '/' + data.expired_users);
                $('.bg-gradient-to-r.from-purple-500 .amount').text(data.online_users);
                $('.bg-gradient-to-r.from-amber-500 #online-hotspot-users').text(data.hotspot_users);
                $('.bg-gradient-to-r.from-blue-600 #total-online-users').text(data.total_online);
                $('.bg-gradient-to-r.from-teal-500 .amount').text(data.total_customers);
                
                // Update expired users cards
                $('.bg-gradient-to-r.from-yellow-500 .amount').text(data.expired_pppoe);
                $('.bg-gradient-to-r.from-red-500 .amount').text(data.expired_hotspot);
                $('.bg-gradient-to-r.from-gray-500 .amount').text(data.total_expired);
                
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

<?php echo '<script'; ?>
>
$(document).ready(function() {
    $.ajax({
        url: "<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
onlineusers/summary", // Adjust this URL to your actual endpoint
        type: 'GET',
        dataType: 'json', // Ensure the expected response is JSON
        success: function(data) {
            console.log('Data fetched successfully:', data);
            $('#total-online-users').text(data.total_users);
            $('#online-hotspot-users').text(data.hotspot_users);
            $('#online-ppp-users').text(data.ppoe_users);
				
        },
        error: function(error) {
            console.log('Error fetching data:', error);
        }
    });
});
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
