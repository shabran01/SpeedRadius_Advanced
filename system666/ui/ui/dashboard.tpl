{include file="sections/header.tpl"}

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

<script>
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
    
    document.getElementById('timeBasedGreeting').textContent = greeting + ' ' + emoji + ', ' + '{$_c['CompanyName']}' + ' — ' + dateStr;
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
</script>

{if $expiring_today > 0 && $_c['hide_uet'] != 'yes'}
<div class="expiring-alert">
    <div class="expiring-alert-inner">
        <span class="expiring-icon">⚠️</span>
        <span class="expiring-text">
            <strong>{$expiring_today}</strong> {if $expiring_today == 1}{Lang::T('user expiring today')}{else}{Lang::T('users expiring today')}{/if}
        </span>
        <a href="{$_url}plan/list" class="expiring-link">{Lang::T('View')} →</a>
    </div>
</div>
{/if}

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
                    {Lang::T('Filter by Router')}:
                </label>
                <select style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:8px;color:white;padding:6px 14px;font-size:13px;" id="router_filter" onchange="filterDashboard()">
                    <option value="all" style="background:#1a1a2e;">{Lang::T('All Routers')}</option>
                    {foreach $routers as $router}
                        <option value="{$router['id']}" style="background:#1a1a2e;">{$router['name']}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
</div>

<div class="row dashboard-cards-row">
    {if in_array($_admin['user_type'], ['SuperAdmin', 'Admin', 'Report', 'Viewer'])}
        <!-- Income Today Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div style="background:linear-gradient(135deg,#00c6ff 0%,#0072ff 100%);border-radius:14px;box-shadow:0 8px 24px rgba(0,114,255,0.35);padding:18px;color:white;transition:all .3s cubic-bezier(.4,0,.2,1);" class="card-hover-effect" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 36px rgba(0,114,255,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(0,114,255,0.35)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-blue-100 text-xs font-medium mb-1">{Lang::T('Income Today')}</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="text-xs">{$_c['currency_code']}</span>
                            {if $_c['hide_income_today'] == 'yes'}
                                <span class="amount income-today-value" id="income-today-val" style="filter: blur(6px); user-select:none;" title="{Lang::T('Click eye to reveal')}">
                                    {number_format($iday, 0, $_c['dec_point'], $_c['thousands_sep'])}
                                </span>
                            {else}
                                <span class="amount" id="income-today-val">
                                    {number_format($iday, 0, $_c['dec_point'], $_c['thousands_sep'])}
                                </span>
                            {/if}
                        </h4>
                    </div>
                    <div class="flex flex-col items-center gap-1 ml-2">
                        <div class="bg-white bg-opacity-20 rounded-full p-2">
                            <i class="ion ion-cash text-lg"></i>
                        </div>
                        {if $_c['hide_income_today'] == 'yes'}
                        <button type="button" onclick="toggleIncome('income-today-val', this)" title="{Lang::T('Show/Hide Income')}"
                            style="background:rgba(255,255,255,0.2);border:none;border-radius:50%;width:26px;height:26px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-eye-slash text-white" style="font-size:12px;"></i>
                        </button>
                        {/if}
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
            <div style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);border-radius:14px;box-shadow:0 8px 24px rgba(17,153,142,0.35);padding:18px;color:white;transition:all .3s cubic-bezier(.4,0,.2,1);" class="card-hover-effect" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 36px rgba(17,153,142,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(17,153,142,0.35)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-green-100 text-xs font-medium mb-1">{Lang::T('Income This Month')}</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="text-xs">{$_c['currency_code']}</span>
                            {if $_c['hide_income_month'] == 'yes'}
                                <span class="amount income-month-value" id="income-month-val" style="filter: blur(6px); user-select:none;" title="{Lang::T('Click eye to reveal')}">
                                    {number_format($imonth, 0, $_c['dec_point'], $_c['thousands_sep'])}
                                </span>
                            {else}
                                <span class="amount" id="income-month-val">
                                    {number_format($imonth, 0, $_c['dec_point'], $_c['thousands_sep'])}
                                </span>
                            {/if}
                        </h4>
                        {if $revenue_change_percent > 0}
                        <div class="flex items-center gap-1 mt-1">
                            <span style="background:rgba(255,255,255,0.25);border-radius:20px;padding:2px 8px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                                &#8593;&nbsp;{$revenue_change_percent}% &nbsp;vs last month
                            </span>
                        </div>
                        {elseif $revenue_change_percent < 0}
                        <div class="flex items-center gap-1 mt-1">
                            <span style="background:rgba(255,255,255,0.25);border-radius:20px;padding:2px 8px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                                &#8595;&nbsp;{$revenue_change_percent|abs}% &nbsp;vs last month
                            </span>
                        </div>
                        {else}
                        <div class="flex items-center gap-1 mt-1">
                            <span style="background:rgba(255,255,255,0.25);border-radius:20px;padding:2px 8px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                                &#8594;&nbsp;0% &nbsp;vs last month
                            </span>
                        </div>
                        {/if}
                    </div>
                    <div class="flex flex-col items-center gap-1 ml-2">
                        <div class="bg-white bg-opacity-20 rounded-full p-2">
                            <i class="ion ion-stats-bars text-lg"></i>
                        </div>
                        {if $_c['hide_income_month'] == 'yes'}
                        <button type="button" onclick="toggleIncome('income-month-val', this)" title="{Lang::T('Show/Hide Income')}"
                            style="background:rgba(255,255,255,0.2);border:none;border-radius:50%;width:26px;height:26px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                            <i class="fa fa-eye-slash text-white" style="font-size:12px;"></i>
                        </button>
                        {/if}
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

        <script>
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
        </script>


        <!-- Active/Expired Users Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div style="background:linear-gradient(135deg,#f7971e 0%,#ff4757 100%);border-radius:14px;box-shadow:0 8px 24px rgba(255,71,87,0.35);padding:18px;color:white;transition:all .3s cubic-bezier(.4,0,.2,1);" class="card-hover-effect" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 36px rgba(255,71,87,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(255,71,87,0.35)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-orange-100 text-xs font-medium mb-1">{Lang::T('Active')}/{Lang::T('Expired')}</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="amount" id="active-expired-val">{$u_act}/{$u_all - $u_act}</span>
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
            <div style="background:linear-gradient(135deg,#a18cd1 0%,#4776e6 100%);border-radius:14px;box-shadow:0 8px 24px rgba(71,118,230,0.35);padding:18px;color:white;transition:all .3s cubic-bezier(.4,0,.2,1);" class="card-hover-effect" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 36px rgba(71,118,230,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(71,118,230,0.35)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-purple-100 text-xs font-medium mb-1">{Lang::T('Online PPPoE Users')}</p>
                        <h4 class="text-lg font-bold truncate">
                            <span class="amount" id="pppoe-online-val">{$online_users}</span>
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

    {if in_array($_admin['user_type'], ['SuperAdmin', 'Admin', 'Report'])}
        <!-- Online Hotspot Users Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div style="background:linear-gradient(135deg,#f7971e 0%,#ffd200 100%);border-radius:14px;box-shadow:0 8px 24px rgba(247,151,30,0.35);padding:18px;color:white;transition:all .3s cubic-bezier(.4,0,.2,1);" class="card-hover-effect" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 36px rgba(247,151,30,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(247,151,30,0.35)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-amber-100 text-xs font-medium mb-1">
                            {Lang::T('Online Hotspot Users')}
                            <span style="display:inline-flex;align-items:center;gap:3px;background:rgba(255,255,255,0.2);border-radius:20px;padding:1px 7px;font-size:10px;font-weight:700;letter-spacing:.5px;margin-left:4px;">
                                <span id="hotspot-live-dot" style="width:7px;height:7px;border-radius:50%;background:#4ade80;display:inline-block;animation:livePulse 1.2s ease-in-out infinite;"></span>
                                LIVE
                            </span>
                        </p>
                        <h4 class="text-xl font-bold" id="online-hotspot-users">{$hotspot_users}</h4>
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
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div style="background:linear-gradient(135deg,#6a11cb 0%,#2575fc 100%);border-radius:14px;box-shadow:0 8px 24px rgba(37,117,252,0.35);padding:18px;color:white;transition:all .3s cubic-bezier(.4,0,.2,1);" class="card-hover-effect" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 36px rgba(37,117,252,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(37,117,252,0.35)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-blue-100 text-xs font-medium mb-1">{Lang::T('Total Online Users')}</p>
                        <h4 class="text-xl font-bold" id="total-online-users">{$total_online}</h4>
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

        <!-- Total PPPoE Users Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);border-radius:14px;box-shadow:0 8px 24px rgba(17,153,142,0.35);padding:18px;color:white;transition:all .3s cubic-bezier(.4,0,.2,1);" class="card-hover-effect" onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 16px 36px rgba(17,153,142,0.45)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 8px 24px rgba(17,153,142,0.35)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-green-100 text-xs font-medium mb-1">{Lang::T('Total PPPoE Users')}</p>
                        <h4 class="text-xl font-bold" id="total-pppoe-val">{$total_pppoe}</h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-network text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{$_url}plan/list&type=PPPOE" class="inline-flex items-center text-green-100 hover:text-white text-xs font-medium">
                        {Lang::T('View Details')} 
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
                        <p class="text-teal-100 text-xs font-medium mb-1">{Lang::T('Total Customers')}</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount" id="total-customers-val">{$c_all}</span>
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

    {if in_array($_admin['user_type'], ['SuperAdmin', 'Admin', 'Report'])}
        <!-- Expired PPPoE Users Card -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-3">
            <div class="card-hover-effect" style="background:linear-gradient(135deg,#fc4a1a 0%,#f7b733 100%);border-radius:14px;box-shadow:0 8px 24px rgba(252,74,26,0.3);padding:18px;color:white;transition:all .3s;" onmouseover="this.style.transform='translateY(-6px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-yellow-100 text-xs font-medium mb-1">{Lang::T('Expired PPPoE Users')}</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount" id="expired-pppoe-val">{$expired_pppoe}</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-network text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{$_url}plan/list&status=off&type=PPPOE" class="inline-flex items-center text-yellow-100 hover:text-white text-xs font-medium">
                        {Lang::T('View Details')} 
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
                        <p class="text-red-100 text-xs font-medium mb-1">{Lang::T('Expired Hotspot Users')}</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount" id="expired-hotspot-val">{$expired_hotspot}</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-wifi text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{$_url}plan/list&status=off&type=Hotspot" class="inline-flex items-center text-red-100 hover:text-white text-xs font-medium">
                        {Lang::T('View Details')} 
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
                        <p class="text-gray-100 text-xs font-medium mb-1">{Lang::T('Total Expired Users')}</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount" id="total-expired-val">{$total_expired}</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-ios-people text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{$_url}plan/list&status=off" class="inline-flex items-center text-gray-100 hover:text-white text-xs font-medium">
                        {Lang::T('View Details')} 
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
                        <p class="text-green-100 text-xs font-medium mb-1">{Lang::T('Active Users')}</p>
                        <h4 class="text-xl font-bold">
                            <span class="amount" id="active-users-val">{$u_act}</span>
                        </h4>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-2 ml-2">
                        <i class="ion ion-ios-person text-lg"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{$_url}customers/list" class="inline-flex items-center text-green-100 hover:text-white text-xs font-medium">
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
            <div style="background:linear-gradient(135deg,#1a1a2e 0%,#16213e 40%,#0f3460 70%,#533483 100%);border-radius:16px;box-shadow:0 12px 32px rgba(26,26,46,0.5);padding:20px;color:white;margin-bottom:16px;border:1px solid rgba(255,255,255,0.07);">
                <!-- Section Title -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <i class="fa fa-calendar mr-2 text-indigo-300"></i>
                        <span class="text-white font-semibold text-sm">Monthly Data Usage</span>
                        <span class="ml-2 text-indigo-300 text-xs">&mdash; {if isset($total_data_usage.current_month)}{$total_data_usage.current_month}{else}{"now"|date_format:"%B %Y"}{/if}</span>
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
                                <span class="amount">{if isset($total_data_usage.total_rx)}{$total_data_usage.total_rx}{else}0 B{/if}</span>
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
                                <span class="amount">{if isset($total_data_usage.total_tx)}{$total_data_usage.total_tx}{else}0 B{/if}</span>
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
                                <span class="amount">{if isset($total_data_usage.total_usage)}{$total_data_usage.total_usage}{else}0 B{/if}</span>
                            </h4>
                        </div>
                        <p class="text-indigo-100 text-xs font-medium">Total Data Usage This Month</p>
                    </div>
                </div>

                <!-- Router Info -->
                <div class="border-t border-white border-opacity-20 pt-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center text-indigo-100 text-xs flex-wrap gap-2">
                            <span><i class="fa fa-router mr-1"></i>{if isset($total_data_usage.active_routers)}{$total_data_usage.active_routers}{else}0{/if} Active Routers</span>
                            <span class="hidden sm:inline">|</span>
                            <span><i class="fa fa-database mr-1"></i>Stored in DB &bull; Resets 1st of every month</span>
                            {if isset($total_data_usage.resets_on)}
                                <span class="hidden sm:inline">|</span>
                                <span class="hidden sm:inline">Next reset: {$total_data_usage.resets_on}</span>
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

{* Revenue & Plans Widgets - Compact Modern Design *}
{if in_array($_admin['user_type'], ['SuperAdmin', 'Admin', 'Report', 'Viewer'])}
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
{if $_admin['user_type'] == 'Viewer'}
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
{/if}

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
                {Lang::T('Revenue Comparison')}
            </h3>
        </div>
        <div class="card-body-compact">
            <div class="revenue-grid">
                <div class="stat-mini">
                    <div class="stat-label">{Lang::T('This Month')}</div>
                    <div class="stat-value">{number_format($revenue_this_month, 0)}</div>
                    <div class="stat-month">{$first_day_this_month}</div>
                </div>
                <div class="stat-mini">
                    <div class="stat-label">{Lang::T('Last Month')}</div>
                    <div class="stat-value">{number_format($revenue_last_month, 0)}</div>
                    <div class="stat-month">{$first_day_last_month}</div>
                </div>
                <div class="stat-mini {if $revenue_change >= 0}change-positive{else}change-negative{/if}">
                    <div class="stat-label">{Lang::T('Change')}</div>
                    <div class="stat-value">
                        {if $revenue_change >= 0}+{else}-{/if}{abs((float)$revenue_change_percent)}%
                    </div>
                    <div class="stat-month">
                        {if $revenue_change >= 0}
                            <i class="fa fa-arrow-up"></i> {Lang::T('Growth')}
                        {else}
                            <i class="fa fa-arrow-down"></i> {Lang::T('Decline')}
                        {/if}
                    </div>
                </div>
            </div>
            <div class="revenue-divider"><span>vs Last Year (YTD)</span></div>
            <div class="revenue-grid">
                <div class="stat-mini">
                    <div class="stat-label">{$yoy_current_label}</div>
                    <div class="stat-value">{number_format($revenue_this_ytd, 0)}</div>
                    <div class="stat-month">{Lang::T('This Year')}</div>
                </div>
                <div class="stat-mini">
                    <div class="stat-label">{$yoy_last_label}</div>
                    <div class="stat-value">{number_format($revenue_last_ytd, 0)}</div>
                    <div class="stat-month">{Lang::T('Last Year')}</div>
                </div>
                <div class="stat-mini {if $yoy_change >= 0}change-positive{else}change-negative{/if}">
                    <div class="stat-label">{Lang::T('YoY Change')}</div>
                    <div class="stat-value">
                        {if $yoy_change >= 0}+{else}-{/if}{abs((float)$yoy_change_percent)}%
                    </div>
                    <div class="stat-month">
                        {if $yoy_change >= 0}
                            <i class="fa fa-arrow-up"></i> {Lang::T('Growth')}
                        {else}
                            <i class="fa fa-arrow-down"></i> {Lang::T('Decline')}
                        {/if}
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
                {Lang::T('Most Popular Plans')}
            </h3>
        </div>
        <div class="card-body-compact">
            {if $most_popular_plans}
                <div class="plan-list">
                    {foreach $most_popular_plans as $index => $plan}
                        <div class="plan-row rank-{$index+1}">
                            <div class="rank-badge {if $index == 0}rank-1{elseif $index == 1}rank-2{elseif $index == 2}rank-3{else}rank-other{/if}">
                                {if $index < 3}<i class="fa fa-trophy"></i>{else}{$index + 1}{/if}
                            </div>
                            <div class="plan-details">
                                <div class="plan-title">{$plan['plan_name']}</div>
                                <div class="plan-stats">
                                    <span class="stat-badge">
                                        <i class="fa fa-shopping-cart"></i>
                                        {$plan['subscription_count']}
                                    </span>
                                </div>
                            </div>
                            <div class="plan-amount">{Lang::moneyFormat($plan['total_revenue'])}</div>
                        </div>
                    {/foreach}
                </div>
            {else}
                <div class="empty-state">
                    <i class="fa fa-info-circle"></i>
                    <div>{Lang::T('No plan data available')}</div>
                </div>
            {/if}
        </div>
    </div>
</div>
{/if}

<div class="row">
    <div class="col-md-7">
        {if $_c['hide_mrc'] != 'yes'}
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
                <div style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:14px 18px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="color:#fff;font-weight:700;font-size:15px;">
                        <i class="fa fa-users" style="margin-right:8px;opacity:.85;"></i>
                        {Lang::T('Monthly Registered Customers')}
                    </span>
                    <div>
                        <a href="{$_url}dashboard&refresh" style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;color:#fff;padding:5px 12px;font-size:12px;text-decoration:none;">
                            <i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </div>
                <div style="padding:16px;">
                    <canvas class="chart" id="chart" style="height: 250px;"></canvas>
                </div>
            </div>
        {/if}

        {if $_c['hide_tms'] != 'yes'}
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
                <div style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);padding:14px 18px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="color:#fff;font-weight:700;font-size:15px;">
                        <i class="fa fa-line-chart" style="margin-right:8px;opacity:.85;"></i>
                        {Lang::T('Total Monthly Sales')}
                    </span>
                    <div>
                        <a href="{$_url}dashboard&refresh" style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;color:#fff;padding:5px 12px;font-size:12px;text-decoration:none;">
                            <i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </div>
                <div style="padding:16px;">
                    <canvas class="chart" id="salesChart" style="height: 250px;"></canvas>
                </div>
            </div>
        {/if}

        {if $_c['hide_tws'] != 'yes'}
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
                <div style="background:linear-gradient(135deg,#f953c6 0%,#b91d73 100%);padding:14px 18px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="color:#fff;font-weight:700;font-size:15px;">
                        <i class="fa fa-calendar" style="margin-right:8px;opacity:.85;"></i>
                        {Lang::T('Total Weekly Sales')}
                    </span>
                    <div>
                        <a href="{$_url}dashboard&refresh" style="background:rgba(255,255,255,0.2);border:none;border-radius:8px;color:#fff;padding:5px 12px;font-size:12px;text-decoration:none;">
                            <i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </div>
                <div style="padding:16px;">
                    <canvas class="chart" id="weeklySalesChart" style="height: 250px;"></canvas>
                </div>
            </div>
        {/if}

        {if $_c['hide_lt'] != 'yes'}
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
                <div style="background:linear-gradient(135deg,#1a1a2e 0%,#0f3460 100%);padding:14px 18px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="color:#fff;font-weight:700;font-size:15px;">
                        <i class="fa fa-money" style="margin-right:8px;color:#f7b733;"></i>
                        {Lang::T('Last 5 Transactions')}
                    </span>
                    <a href="{$_url}reports/by-period" style="background:rgba(255,255,255,0.15);border-radius:8px;color:#fff;padding:5px 12px;font-size:12px;text-decoration:none;">
                        <i class="fa fa-list"></i> View All
                    </a>
                </div>
                <div style="padding:0;">
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{Lang::T('Invoice')}</th>
                                    <th>{Lang::T('Username')}</th>
                                    <th>{Lang::T('Plan')}</th>
                                    <th>{Lang::T('Price')}</th>
                                    <th>{Lang::T('Date')}</th>
                                    <th>{Lang::T('Method')}</th>
                                    <th>{Lang::T('Router')}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {if $lastTransactions}
                                    {foreach $lastTransactions as $trans}
                                        <tr>
                                            <td>{$trans['id']}</td>
                                            <td><a href="{$_url}reports/by-period" title="View Details">{$trans['invoice']}</a></td>
                                            <td><a href="{$_url}customers/viewu/{$trans['username']}">{$trans['username']}</a></td>
                                            <td>{$trans['plan_name']}</td>
                                            <td><strong>{Lang::moneyFormat($trans['price'])}</strong></td>
                                            <td><small>{$trans['recharged_on']} {$trans['recharged_time']}</small></td>
                                            <td><span class="label label-info">{$trans['method']}</span></td>
                                            <td>{$trans['routers']}</td>
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr>
                                        <td colspan="8" class="text-center">{Lang::T('No transactions found')}</td>
                                    </tr>
                                {/if}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        {/if}

        {* Top 5 Most Active Users *}
        <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
            <div style="background:linear-gradient(135deg,#f7971e 0%,#ffd200 100%);padding:14px 18px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="color:#fff;font-weight:700;font-size:15px;">
                        <i class="fa fa-star" style="margin-right:8px;"></i>
                        {Lang::T('Top 5 Most Active Users')}
                    </span>
                    <a href="{$_url}reports/by-period" style="background:rgba(255,255,255,0.25);border-radius:8px;color:#fff;padding:5px 12px;font-size:12px;text-decoration:none;">
                        <i class="fa fa-list"></i> View All
                    </a>
            </div>
            <div style="padding:0;">
                <p class="text-muted"><small>{Lang::T('The most active users in the last 30 days.')}</small></p>
                <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{Lang::T('Username')}</th>
                                <th>{Lang::T('Full Name')}</th>
                                <th>{Lang::T('Phone')}</th>
                                <th>{Lang::T('Transactions')}</th>
                                <th>{Lang::T('Total Spent')}</th>
                                <th>{Lang::T('Last Recharge')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {if $topActiveUsers}
                                {foreach $topActiveUsers as $index => $user}
                                    <tr>
                                        <td>
                                            {if $index == 0}
                                                <i class="fa fa-trophy text-warning" style="font-size: 16px;"></i>
                                            {elseif $index == 1}
                                                <i class="fa fa-trophy text-muted" style="font-size: 14px;"></i>
                                            {elseif $index == 2}
                                                <i class="fa fa-trophy text-danger" style="font-size: 12px;"></i>
                                            {else}
                                                {$index + 1}
                                            {/if}
                                        </td>
                                        <td><a href="{$_url}customers/viewu/{$user['username']}">{$user['username']}</a></td>
                                        <td>{$user['fullname']}</td>
                                        <td>{$user['phonenumber']}</td>
                                        <td><span class="badge badge-info">{$user['transaction_count']}</span></td>
                                        <td><strong>{Lang::moneyFormat($user['total_spent'])}</strong></td>
                                        <td><small>{$user['last_recharge']}</small></td>
                                    </tr>
                                {/foreach}
                            {else}
                                <tr>
                                    <td colspan="7" class="text-center">{Lang::T('No active users found')}</td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {if $_c['hide_vs'] != 'yes'}
            {if $_c['disable_voucher'] != 'yes' && $stocks['unused']>0 || $stocks['used']>0}
                <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
                    <div style="background:linear-gradient(135deg,#f7971e 0%,#ffd200 100%);padding:12px 16px;">
                        <span style="color:#fff;font-weight:700;font-size:15px;"><i class="fa fa-ticket" style="margin-right:8px;"></i>Vouchers Stock</span>
                    </div>
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
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(252,74,26,0.1);overflow:hidden;margin-bottom:20px;border:1px solid rgba(252,74,26,0.15);">
                <div style="background:linear-gradient(135deg,#fc4a1a 0%,#f7b733 100%);padding:12px 16px;">
                    <span style="color:#fff;font-weight:700;font-size:15px;"><i class="fa fa-user-times" style="margin-right:8px;"></i>{Lang::T('User Expired, Today')}</span>
                </div>
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
        {/if}

        {* Expired PPPoE Users Today *}
        {if $_c['hide_uet'] != 'yes'}
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(106,17,203,0.12);overflow:hidden;margin-bottom:20px;border:1px solid rgba(106,17,203,0.15);">
                <div style="background:linear-gradient(135deg,#6a11cb 0%,#e53935 100%);padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="color:#fff;font-weight:700;font-size:15px;">
                        <i class="fa fa-plug" style="margin-right:8px;"></i>Expired PPPoE Users
                    </span>
                    <span style="background:rgba(255,255,255,0.25);border-radius:20px;color:#fff;padding:3px 10px;font-size:12px;font-weight:700;">
                        {$expire_pppoe_count}
                    </span>
                </div>
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
                            {foreach $expire_pppoe_today as $ep}
                                {assign var="ep_exp" value="{$ep['expiration']} {$ep['time']}"}
                                {assign var="ep_started" value="{$ep['recharged_on']} {$ep['recharged_time']}"}
                                <tr>
                                    <td><a href="{$_url}customers/viewu/{$ep['username']}">{$ep['username']}</a></td>
                                    <td>
                                        <small data-toggle="tooltip" data-placement="top"
                                            title="{Lang::dateAndTimeFormat($ep['recharged_on'],$ep['recharged_time'])}">{Lang::timeElapsed($ep_started)}</small>
                                        /
                                        <span data-toggle="tooltip" data-placement="top"
                                            title="{Lang::dateAndTimeFormat($ep['expiration'],$ep['time'])}">{Lang::timeElapsed($ep_exp)}</span>
                                    </td>
                                    <td>{$ep['namebp']}</td>
                                    <td>{$ep['routers']}</td>
                                </tr>
                            {foreachelse}
                                <tr>
                                    <td colspan="4" class="text-center text-muted" style="padding:16px;">
                                        <i class="fa fa-check-circle text-success" style="margin-right:6px;"></i>No expired PPPoE users found
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        {/if}
    </div>


    <div class="col-md-5">
        {if $_c['router_check'] && count($routeroffs)> 0}
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(229,57,53,0.15);overflow:hidden;margin-bottom:16px;border:1px solid rgba(229,57,53,0.2);">
                <div style="background:linear-gradient(135deg,#e53935 0%,#e91e8c 100%);padding:12px 16px;">
                    <span style="color:#fff;font-weight:700;font-size:14px;"><i class="fa fa-exclamation-circle" style="margin-right:8px;"></i>{Lang::T('Routers Offline')}</span>
                </div>
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
        <div style="background:linear-gradient(135deg,#fc4a1a 0%,#f7b733 100%);border-radius:14px;padding:12px 18px;margin-bottom:16px;box-shadow:0 4px 14px rgba(252,74,26,0.3);">
            <span style="color:#fff;font-weight:600;font-size:13px;"><i class="fa fa-clock-o" style="margin-right:8px;"></i>{Lang::T('Cron has not run for over 1 hour. Please check your setup.')}</span>
        </div>
        {else}
        <div style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);border-radius:14px;padding:12px 18px;margin-bottom:16px;box-shadow:0 4px 14px rgba(17,153,142,0.3);">
            <span style="color:#fff;font-weight:600;font-size:13px;"><i class="fa fa-check-circle" style="margin-right:8px;"></i>{Lang::T('Cron Job last ran on')}: {$run_date}</span>
        </div>
        {/if}
        {else}
        <div style="background:linear-gradient(135deg,#e53935 0%,#e91e8c 100%);border-radius:14px;padding:12px 18px;margin-bottom:16px;box-shadow:0 4px 14px rgba(229,57,53,0.3);">
            <span style="color:#fff;font-weight:600;font-size:13px;"><i class="fa fa-warning" style="margin-right:8px;"></i>{Lang::T('Cron appear not been setup, please check your cron setup.')}</span>
        </div>
        {/if}
        {if $_c['hide_pg'] != 'yes'}
            <div style="background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);border-radius:14px;box-shadow:0 6px 18px rgba(17,153,142,0.3);padding:14px 18px;margin-bottom:16px;">
                <span style="color:#fff;font-weight:700;font-size:14px;">
                    <i class="fa fa-credit-card" style="margin-right:8px;"></i>
                    {Lang::T('Payment Gateway')}: {str_replace(',',', ',$_c['payment_gateway'])}
                </span>
            </div>
        {/if}
        {if $_c['hide_aui'] != 'yes'}
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
                <div style="background:linear-gradient(135deg,#6a11cb 0%,#2575fc 100%);padding:14px 18px;">
                    <span style="color:#fff;font-weight:700;font-size:15px;"><i class="fa fa-pie-chart" style="margin-right:8px;"></i>{Lang::T('All Users Insights')}</span>
                </div>
                <div style="padding:16px;max-width:320px;margin:0 auto;">
                    <canvas id="userRechargesChart"></canvas>
                </div>
            </div>
        {/if}
        {if $_c['hide_al'] != 'yes'}
            <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
                <div style="background:linear-gradient(135deg,#373b44 0%,#4286f4 100%);padding:14px 18px;">
                    <a href="{$_url}logs" style="color:#fff;font-weight:700;font-size:15px;text-decoration:none;"><i class="fa fa-list-alt" style="margin-right:8px;"></i>{Lang::T('Activity Log')}</a>
                </div>
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

        {* ── Top 5 Hotspot Downloaders ── *}
        <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
            <div style="background:linear-gradient(135deg,#00b4db 0%,#0083b0 100%);padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
                <span>
                    <i class="fa fa-wifi" style="margin-right:7px;color:#74b9ff;"></i>
                    <strong>Top 5 Hotspot Downloaders</strong>
                </span>
                <span style="font-size:.75rem;background:rgba(255,255,255,.12);padding:2px 10px;border-radius:12px;color:#b2bec3;">
                    {$top_downloaders_month}
                </span>
            </div>
            {if $top_hotspot_downloaders|count > 0}
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
                        {foreach $top_hotspot_downloaders as $rank => $d}
                        <tr style="transition:background .15s;" onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background=''">
                            <td style="padding:8px 14px;color:#b2bec3;font-weight:700;">{$rank+1}</td>
                            <td style="padding:8px 14px;">
                                <strong>{if $d['fullname']}{$d['fullname']}{else}{$d['username']}{/if}</strong>
                                <small class="text-muted" style="display:block;">{$d['username']}</small>
                            </td>
                            <td style="padding:8px 14px;color:#2980b9;font-weight:600;">{$d['download_fmt']}</td>
                            <td style="padding:8px 14px;color:#27ae60;font-weight:600;">{$d['upload_fmt']}</td>
                            <td style="padding:8px 14px;color:#2c3e50;font-weight:700;">{$d['total_fmt']}</td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            {else}
            <div class="panel-body" style="text-align:center;color:#b2bec3;padding:20px;">
                <i class="fa fa-inbox" style="font-size:1.5rem;display:block;margin-bottom:6px;"></i>
                No hotspot usage recorded this month
            </div>
            {/if}
        </div>

        {* ── Top 5 PPPoE Downloaders ── *}
        <div style="background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,0.08);overflow:hidden;margin-bottom:20px;border:1px solid rgba(0,0,0,0.04);">
            <div style="background:linear-gradient(135deg,#a18cd1 0%,#4776e6 100%);padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
                <span>
                    <i class="fa fa-plug" style="margin-right:7px;color:#55efc4;"></i>
                    <strong>Top 5 PPPoE Downloaders</strong>
                </span>
                <span style="font-size:.75rem;background:rgba(255,255,255,.12);padding:2px 10px;border-radius:12px;color:#b2bec3;">
                    {$top_downloaders_month}
                </span>
            </div>
            {if $top_pppoe_downloaders|count > 0}
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
                        {foreach $top_pppoe_downloaders as $rank => $d}
                        <tr style="transition:background .15s;" onmouseover="this.style.background='#f0f4f8'" onmouseout="this.style.background=''">
                            <td style="padding:8px 14px;color:#b2bec3;font-weight:700;">{$rank+1}</td>
                            <td style="padding:8px 14px;">
                                <strong>{if $d['fullname']}{$d['fullname']}{else}{$d['pppoe_username']}{/if}</strong>
                                <small class="text-muted" style="display:block;">{$d['pppoe_username']}</small>
                            </td>
                            <td style="padding:8px 14px;color:#2980b9;font-weight:600;">{$d['download_fmt']}</td>
                            <td style="padding:8px 14px;color:#27ae60;font-weight:600;">{$d['upload_fmt']}</td>
                            <td style="padding:8px 14px;color:#2c3e50;font-weight:700;">{$d['total_fmt']}</td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            {else}
            <div class="panel-body" style="text-align:center;color:#b2bec3;padding:20px;">
                <i class="fa fa-inbox" style="font-size:1.5rem;display:block;margin-bottom:6px;"></i>
                No PPPoE usage recorded this month
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
        {/literal}
    {/if}
    {if $_c['hide_tws'] != 'yes'}
        {literal}
            document.addEventListener("DOMContentLoaded", function() {
                var weeklySales = JSON.parse('{/literal}{$weeklySales|json_encode}{literal}');

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
                url: '{$_url}onlineusers/hotspot_stats/' + routerId,
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


{include file="sections/footer.tpl"}
