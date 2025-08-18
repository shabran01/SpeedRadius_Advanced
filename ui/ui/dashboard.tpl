{include file="sections/header.tpl"}

<div class="greeting-box mb-3">
    <div class="greeting-content">
        <h3 id="timeBasedGreeting" class="text-gradient"></h3>
        <div class="greeting-icon">
            <i id="weatherIcon" class="ion"></i>
        </div>
    </div>
</div>

<script>
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
    
    document.getElementById('timeBasedGreeting').textContent = greeting + ', ' + '{$_c['CompanyName']}';
    document.getElementById('weatherIcon').className = 'ion ' + iconClass;
}

// Update greeting immediately and then every minute
updateGreeting();
setInterval(updateGreeting, 60000);
</script>

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
                    {Lang::T('Filter by Router')}:
                </label>
                <select class="form-control bg-gray-50 border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" id="router_filter" onchange="filterDashboard()">
                    <option value="all">{Lang::T('All Routers')}</option>
                    {foreach $routers as $router}
                        <option value="{$router['id']}">{$router['name']}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {if in_array($_admin['user_type'], ['SuperAdmin', 'Admin', 'Report'])}
        <!-- Income Today Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div class="bg-gradient-to-r from-cyan-500 to-blue-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 text-white transform hover:-translate-y-1 card-hover-effect">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-blue-100 text-xs font-medium mb-1">{Lang::T('Income Today')}</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="text-xs">{$_c['currency_code']}</span>
                            <span class="amount">{number_format($iday, 0, $_c['dec_point'], $_c['thousands_sep'])}</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-cash text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{$_url}reports/by-date" class="inline-flex items-center text-blue-100 hover:text-white text-xs font-medium">
                        {Lang::T('View Report')} 
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
                        <p class="text-green-100 text-xs font-medium mb-1">{Lang::T('Income This Month')}</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="text-xs">{$_c['currency_code']}</span>
                            <span class="amount">{number_format($imonth, 0, $_c['dec_point'], $_c['thousands_sep'])}</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-stats-bars text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{$_url}reports/by-period" class="inline-flex items-center text-green-100 hover:text-white text-xs font-medium">
                        {Lang::T('View Report')} 
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
                        <p class="text-orange-100 text-xs font-medium mb-1">{Lang::T('Active')}/{Lang::T('Expired')}</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="amount">{$u_act}/{$u_all - $u_act}</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-person text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{$_url}plan/list" class="inline-flex items-center text-orange-100 hover:text-white text-xs font-medium">
                        {Lang::T('View Customers')} 
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
                        <p class="text-purple-100 text-xs font-medium mb-1">{Lang::T('Online PPPoE Users')}</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="amount">{$online_users}</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-network text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{$_url}plugin/pppoe_monitor_router_menu" class="inline-flex items-center text-purple-100 hover:text-white text-xs font-medium">
                        {Lang::T('Online PPPoE Users')}
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    {/if}
</div>

<div class="row">
    {if in_array($_admin['user_type'], ['SuperAdmin', 'Admin', 'Report'])}
        <!-- Online Hotspot Users Card -->
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 text-white transform hover:-translate-y-1 card-hover-effect">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-amber-100 text-xs font-medium mb-1">{Lang::T('Online Hotspot Users')}</p>
                        <h4 class="text-xl font-bold" id="online-hotspot-users">0</h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-wifi text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{$_url}onlineusers/hotspot" class="inline-flex items-center text-amber-100 hover:text-white text-xs font-medium">
                        {Lang::T('View Details')} 
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
                        <p class="text-blue-100 text-xs font-medium mb-1">{Lang::T('Total Online Users')}</p>
                        <h4 class="text-xl font-bold" id="total-online-users">0</h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-ios-people text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{$_url}reports/by-date" class="inline-flex items-center text-blue-100 hover:text-white text-xs font-medium">
                        {Lang::T('View Details')} 
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
                        <p class="text-teal-100 text-xs font-medium mb-1">{Lang::T('Total Customers')}</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount">{$c_all}</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-android-contacts text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{$_url}customers/list" class="inline-flex items-center text-teal-100 hover:text-white text-xs font-medium">
                        {Lang::T('View Details')} 
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    {/if}
</div>

<div class="row">
    {if in_array($_admin['user_type'], ['SuperAdmin', 'Admin', 'Report'])}
        <!-- Expired PPPoE Users Card -->
        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 p-4 text-white transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-yellow-100 text-xs font-medium mb-1">{Lang::T('Expired PPPoE Users')}</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount">{$expired_pppoe}</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-network text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{$_url}plan/list?status=off&type=PPPOE" class="inline-flex items-center text-yellow-100 hover:text-white text-xs font-medium">
                        {Lang::T('View Details')} 
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
                        <p class="text-red-100 text-xs font-medium mb-1">{Lang::T('Expired Hotspot Users')}</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount">{$expired_hotspot}</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-wifi text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{$_url}plan/list?status=off&type=Hotspot" class="inline-flex items-center text-red-100 hover:text-white text-xs font-medium">
                        {Lang::T('View Details')} 
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
                        <p class="text-gray-100 text-xs font-medium mb-1">{Lang::T('Total Expired Users')}</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount">{$total_expired}</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-ios-people text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{$_url}plan/list?status=off" class="inline-flex items-center text-gray-100 hover:text-white text-xs font-medium">
                        {Lang::T('View Details')} 
                        <i class="fa fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    {/if}
</div>

<!-- Data Usage Row -->
<div class="row">
    {if in_array($_admin['user_type'], ['SuperAdmin', 'Admin', 'Report'])}
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
                                <span class="amount">{if isset($total_data_usage.total_rx)}{$total_data_usage.total_rx}{else}0 B{/if}</span>
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
                                <span class="amount">{if isset($total_data_usage.total_tx)}{$total_data_usage.total_tx}{else}0 B{/if}</span>
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
                                <span class="amount">{if isset($total_data_usage.total_usage)}{$total_data_usage.total_usage}{else}0 B{/if}</span>
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
                            <span>{if isset($total_data_usage.active_routers)}{$total_data_usage.active_routers}{else}0{/if} Active Routers</span>
                            {if isset($total_data_usage.last_updated)}
                                <span class="mx-2 hidden sm:inline">|</span>
                                <span class="hidden sm:inline">Last Updated: {$total_data_usage.last_updated}</span>
                            {/if}
                        </div>
                        <div class="flex items-center space-x-2">
                            {if isset($total_data_usage.router_details) && count($total_data_usage.router_details) > 0}
                                <button onclick="toggleRouterDetails()" class="inline-flex items-center px-2 py-1 bg-white bg-opacity-20 rounded-full text-xs hover:bg-opacity-30 transition-all duration-200">
                                    <i class="fa fa-eye mr-1"></i>
                                    <span class="hidden sm:inline">View Details</span>
                                </button>
                            {/if}
                            <button onclick="refreshDataUsage()" class="inline-flex items-center px-3 py-1 bg-white bg-opacity-20 rounded-full text-xs hover:bg-opacity-30 transition-all duration-200">
                                <i class="fa fa-refresh mr-1"></i>
                                <span class="hidden sm:inline">Refresh</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Router Details (Hidden by default) -->
                {if isset($total_data_usage.router_details) && count($total_data_usage.router_details) > 0}
                <div id="router-details" style="display: none;" class="mt-4 bg-white bg-opacity-10 rounded-lg p-3 backdrop-blur-sm">
                    <h6 class="text-white font-medium mb-3 flex items-center text-sm">
                        <i class="fa fa-info-circle mr-2"></i>
                        Router WAN Interface Details:
                    </h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                        {foreach $total_data_usage.router_details as $router}
                        <div class="bg-white bg-opacity-5 rounded-lg p-2 text-xs">
                            <div class="font-medium text-white mb-1">{$router.name}</div>
                            <div class="text-indigo-100 text-xs space-y-1">
                                <div>RX: {$router.rx}</div>
                                <div>TX: {$router.tx}</div>
                                <div>Total: {$router.total}</div>
                                <div class="flex items-center">
                                    {if !$router.wan_found}
                                        <span class="inline-flex items-center px-1 py-0.5 bg-yellow-500 bg-opacity-20 text-yellow-200 rounded text-xs">
                                            <i class="fa fa-exclamation-triangle mr-1"></i>
                                            Auto-detected
                                        </span>
                                    {else}
                                        <span class="inline-flex items-center px-1 py-0.5 bg-green-500 bg-opacity-20 text-green-200 rounded text-xs">
                                            <i class="fa fa-check mr-1"></i>
                                            Confirmed
                                        </span>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        {/foreach}
                    </div>
                </div>
                {/if}
            </div>
        </div>
    {/if}
</div>

{if in_array($_admin['user_type'], ['SuperAdmin', 'Admin'])}
    {include file="admin_server_stats.tpl"}
{/if}

<div class="row">
    <div class="col-md-7">
        {if $_c['hide_mrc'] != 'yes'}
            <div class="box box-solid ">
                <div class="box-header">
                    <i class="fa fa-th"></i>

                    <h3 class="box-title">{Lang::T('Monthly Registered Customers')}</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <a href="{$_url}dashboard&refresh" class="btn bg-teal btn-sm"><i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </div>
                <div class="box-body border-radius-none">
                    <canvas class="chart" id="chart" style="height: 250px;"></canvas>
                </div>
            </div>
        {/if}

        {if $_c['hide_tms'] != 'yes'}
            <div class="box box-solid ">
                <div class="box-header">
                    <i class="fa fa-inbox"></i>

                    <h3 class="box-title">{Lang::T('Total Monthly Sales')}</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <a href="{$_url}dashboard&refresh" class="btn bg-teal btn-sm"><i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </div>
                <div class="box-body border-radius-none">
                    <canvas class="chart" id="salesChart" style="height: 250px;"></canvas>
                </div>
            </div>
        {/if}
        {if $_c['disable_voucher'] != 'yes' && $stocks['unused']>0 || $stocks['used']>0}
            {if $_c['hide_vs'] != 'yes'}
                <div class="panel panel-primary mb20 panel-hovered project-stats table-responsive">
                    <div class="panel-heading">Vouchers Stock</div>
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <th>{Lang::T('Package Name')}</th>
                                    <th>unused</th>
                                    <th>used</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach $plans as $stok}
                                    <tr>
                                        <td>{$stok['name_plan']}</td>
                                        <td>{$stok['unused']}</td>
                                        <td>{$stok['used']}</td>
                                    </tr>
                                </tbody>
                            {/foreach}
                            <tr>
                                <td>Total</td>
                                <td>{$stocks['unused']}</td>
                                <td>{$stocks['used']}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            {/if}
        {/if}
        {if $_c['hide_uet'] != 'yes'}
            <div class="panel panel-warning mb20 panel-hovered project-stats table-responsive">
                <div class="panel-heading">{Lang::T('User Expired, Today')}</div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>{Lang::T('Username')}</th>
                                <th>{Lang::T('Created / Expired')}</th>
                                <th>{Lang::T('Internet Package')}</th>
                                <th>{Lang::T('Location')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $expire as $expired}
                                {assign var="rem_exp" value="{$expired['expiration']} {$expired['time']}"}
                                {assign var="rem_started" value="{$expired['recharged_on']} {$expired['recharged_time']}"}
                                <tr>
                                    <td><a href="{$_url}customers/viewu/{$expired['username']}">{$expired['username']}</a></td>
                                    <td><small data-toggle="tooltip" data-placement="top"
                                            title="{Lang::dateAndTimeFormat($expired['recharged_on'],$expired['recharged_time'])}">{Lang::timeElapsed($rem_started)}</small>
                                        /
                                        <span data-toggle="tooltip" data-placement="top"
                                            title="{Lang::dateAndTimeFormat($expired['expiration'],$expired['time'])}">{Lang::timeElapsed($rem_exp)}</span>
                                    </td>
                                    <td>{$expired['namebp']}</td>
                                    <td>{$expired['routers']}</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                &nbsp; {include file="pagination.tpl"}
            </div>
            
            <!-- All Expired Users Section -->
            <div class="panel panel-danger mb20 panel-hovered project-stats table-responsive">
                <div class="panel-heading">
                    <i class="fa fa-user-times"></i> {Lang::T('All Expired Users')}
                    <div class="pull-right">
                        <a href="{$_url}customers/list?filter=Inactive" class="btn btn-xs btn-primary">
                            <i class="fa fa-list"></i> {Lang::T('View All')}
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>{Lang::T('Username')}</th>
                                <th>{Lang::T('Expired On')}</th>
                                <th>{Lang::T('Internet Package')}</th>
                                <th>{Lang::T('Location')}</th>
                                <th>{Lang::T('Actions')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $all_expired as $expired}
                                {assign var="rem_exp" value="{$expired['expiration']} {$expired['time']}"}
                                <tr>
                                    <td><a href="{$_url}customers/viewu/{$expired['username']}">{$expired['username']}</a></td>
                                    <td>
                                        <span class="text-danger" data-toggle="tooltip" data-placement="top"
                                            title="{Lang::dateAndTimeFormat($expired['expiration'],$expired['time'])}">{Lang::timeElapsed($rem_exp)}</span>
                                    </td>
                                    <td>{$expired['namebp']}</td>
                                    <td>{$expired['routers']}</td>
                                    <td>
                                        <a href="{$_url}customers/view/{$expired['customer_id']}" class="btn btn-xs btn-info">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{$_url}customers/recharge/{$expired['customer_id']}/{$expired['plan_id']}?token={$csrf_token}" class="btn btn-xs btn-success">
                                            <i class="fa fa-refresh"></i> {Lang::T('Recharge')}
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                &nbsp; {include file="pagination.tpl"}
            </div>
        {/if}
    </div>


    <div class="col-md-5">
        {if $_c['router_check'] && count($routeroffs)> 0}
            <div class="panel panel-danger">
                <div class="panel-heading text-bold">{Lang::T('Routers Offline')}</div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tbody>
                            {foreach $routeroffs as $ros}
                                <tr>
                                    <td><a href="{$_url}routers/edit/{$ros['id']}" class="text-bold text-red">{$ros['name']}</a></td>
                                    <td data-toggle="tooltip" data-placement="top" class="text-red"
                                            title="{Lang::dateTimeFormat($ros['last_seen'])}">{Lang::timeElapsed($ros['last_seen'])}
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        {/if}
        {if $run_date}
        {assign var="current_time" value=$smarty.now}
        {assign var="run_time" value=strtotime($run_date)}
        {if $current_time - $run_time > 3600}
        <div class="panel panel-cron-warning panel-hovered mb20 activities">
            <div class="panel-heading"><i class="fa fa-clock-o"></i> &nbsp; {Lang::T('Cron has not run for over 1 hour. Please
                check your setup.')}</div>
        </div>
        {else}
        <div class="panel panel-cron-success panel-hovered mb20 activities">
            <div class="panel-heading">{Lang::T('Cron Job last ran on')}: {$run_date}</div>
        </div>
        {/if}
        {else}
        <div class="panel panel-cron-danger panel-hovered mb20 activities">
            <div class="panel-heading"><i class="fa fa-warning"></i> &nbsp; {Lang::T('Cron appear not been setup, please check
                your cron setup.')}</div>
        </div>
        {/if}
        {if $_c['hide_pg'] != 'yes'}
            <div class="panel panel-success panel-hovered mb20 activities">
                <div class="panel-heading">{Lang::T('Payment Gateway')}: {str_replace(',',', ',$_c['payment_gateway'])}
                </div>
            </div>
        {/if}
        {if $_c['hide_aui'] != 'yes'}
            <div class="panel panel-info panel-hovered mb20 activities">
                <div class="panel-heading">{Lang::T('All Users Insights')}</div>
                <div class="panel-body">
                    <canvas id="userRechargesChart"></canvas>
                </div>
            </div>
        {/if}
        {if $_c['hide_al'] != 'yes'}
            <div class="panel panel-info panel-hovered mb20 activities">
                <div class="panel-heading"><a href="{$_url}logs">{Lang::T('Activity Log')}</a></div>
                <div class="panel-body">
                    <ul class="list-unstyled">
                        {foreach $dlog as $dlogs}
                            <li class="primary">
                                <span class="point"></span>
                                <span class="time small text-muted">{Lang::timeElapsed($dlogs['date'],true)}</span>
                                <p>{$dlogs['description']}</p>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        {/if}
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>

<script type="text/javascript">
    {if $_c['hide_mrc'] != 'yes'}
        {literal}
            document.addEventListener("DOMContentLoaded", function() {
                var counts = JSON.parse('{/literal}{$monthlyRegistered|json_encode}{literal}');

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
        {/literal}
    {/if}
    {if $_c['hide_tmc'] != 'yes'}
        {literal}
            document.addEventListener("DOMContentLoaded", function() {
                var monthlySales = JSON.parse('{/literal}{$monthlySales|json_encode}{literal}');

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
        {/literal}
    {/if}
    {if $_c['hide_aui'] != 'yes'}
        {literal}
            document.addEventListener("DOMContentLoaded", function() {
                // Get the data from PHP and assign it to JavaScript variables
                var u_act = '{/literal}{$u_act}{literal}';
                var c_all = '{/literal}{$c_all}{literal}';
                var u_all = '{/literal}{$u_all}{literal}';
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
        {/literal}
    {/if}
</script>

<script type="text/javascript">
    function filterDashboard() {
        var routerId = $('#router_filter').val();
        console.log('Filtering for router:', routerId);
        
        $.ajax({
            url: '{$_url}dashboard/filter',
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
            url: '{$_url}dashboard/refresh-data-usage',
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
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $.ajax({
            url: "{$_url}onlineusers/sms_balance",
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
    </script>

<script>
$(document).ready(function() {
    $.ajax({
        url: "{$_url}onlineusers/summary", // Adjust this URL to your actual endpoint
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
</script>

{include file="sections/footer.tpl"}
