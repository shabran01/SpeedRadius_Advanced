{include file="sections/header.tpl"}

<!-- Modern Customers List -->
<div class="customers-modern">

    <!-- Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <h3 style="margin:0;font-weight:700;color:#1a1a2e;">
            <i class="fa fa-users" style="color:#667eea;margin-right:8px;"></i>{Lang::T('Manage Contact')}
        </h3>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
            <a href="{$_url}customers/add" class="btn-modern btn-primary-modern">
                <i class="ion ion-android-add"></i> {Lang::T('Add Customer')}
            </a>
            <a href="{$_url}customers/upload" class="btn-modern btn-upload">
                <i class="fa fa-upload"></i> {Lang::T('Upload CSV')}
            </a>
            {/if}
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="get" action="{$_url}customers" class="filter-card">
        <input type="hidden" name="_route" value="customers">
        <input type="hidden" name="csrf_token" value="{$csrf_token}">
        <div class="filter-row">
            <div class="filter-field">
                <select name="order" class="filter-select" style="min-width:120px;">
                    <option value="username" {if $order eq 'username'}selected{/if}>{Lang::T('Username')}</option>
                    <option value="fullname" {if $order eq 'fullname'}selected{/if}>{Lang::T('Full Name')}</option>
                    <option value="lastname" {if $order eq 'lastname'}selected{/if}>{Lang::T('Last Name')}</option>
                    <option value="created_at" {if $order eq 'created_at'}selected{/if}>{Lang::T('Created Date')}</option>
                    <option value="balance" {if $order eq 'balance'}selected{/if}>{Lang::T('Balance')}</option>
                    <option value="status" {if $order eq 'status'}selected{/if}>{Lang::T('Status')}</option>
                </select>
            </div>
            <div class="filter-field">
                <select name="orderby" class="filter-select" style="min-width:100px;">
                    <option value="asc" {if $orderby eq 'asc'}selected{/if}>↑ {Lang::T('Asc')}</option>
                    <option value="desc" {if $orderby eq 'desc'}selected{/if}>↓ {Lang::T('Desc')}</option>
                </select>
            </div>
            <div class="filter-field">
                <select name="filter" class="filter-select">
                    {foreach $statuses as $status}
                    <option value="{$status}" {if $filter eq $status}selected{/if}>{Lang::T($status)}</option>
                    {/foreach}
                </select>
            </div>
            <div class="filter-field">
                <select name="service_filter" class="filter-select">
                    <option value="">{Lang::T('All Services')}</option>
                    <option value="PPPoE" {if $service_filter eq 'PPPoE'}selected{/if}>PPPoE</option>
                    <option value="Hotspot" {if $service_filter eq 'Hotspot'}selected{/if}>Hotspot</option>
                    <option value="VPN" {if $service_filter eq 'VPN'}selected{/if}>VPN</option>
                    <option value="Others" {if $service_filter eq 'Others'}selected{/if}>Others</option>
                </select>
            </div>
            <div class="filter-field filter-search">
                <input type="text" name="search" placeholder="{Lang::T('Search...')}" value="{$search}" class="filter-input">
            </div>
            <button type="submit" class="btn-modern btn-filter-go"><i class="fa fa-search"></i></button>
            <button type="submit" name="export" value="csv" class="btn-modern btn-csv"><i class="fa fa-download"></i> CSV</button>
        </div>
    </form>

    <!-- Table -->
    <div class="table-card-modern">
        <div class="table-responsive-wrap">
            <table class="modern-table">
                <thead>
                    <tr>
                        {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                        <th style="width:40px;"><input type="checkbox" id="selectAll" title="Select All" onclick="toggleAll(this)"></th>
                        {/if}
                        <th>{Lang::T('Username')}</th>
                        <th>{Lang::T('Full Name')}</th>
                        <th class="text-right">{Lang::T('Balance')}</th>
                        <th>{Lang::T('Contact')}</th>
                        <th>{Lang::T('Package')}</th>
                        <th>{Lang::T('Service')}</th>
                        <th>{Lang::T('PPPoE')}</th>
                        <th>{Lang::T('Status')}</th>
                        <th>{Lang::T('Created')}</th>
                        <th>{Lang::T('Actions')}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $d as $ds}
                    <tr class="{if $ds['status'] != 'Active'}row-inactive{/if}">
                        {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                        <td><input type="checkbox" name="ids[]" value="{$ds['id']}" class="customer-checkbox"></td>
                        {/if}
                        <td>
                            <a href="{$_url}customers/view/{$ds['id']}"><strong>{$ds['username']}</strong></a>
                        </td>
                        <td>
                            <a href="{$_url}customers/view/{$ds['id']}" class="text-dark">{$ds['fullname']}</a>
                            {if $ds['account_type']}<span class="account-type-badge">{$ds['account_type']}</span>{/if}
                        </td>
                        <td class="text-right"><strong>{Lang::moneyFormat($ds['balance'])}</strong></td>
                        <td>
                            <div class="contact-icons">
                                {if $ds['phonenumber']}
                                <a href="tel:{$ds['phonenumber']}" class="contact-icon" title="{$ds['phonenumber']}">📞</a>
                                {/if}
                                {if $ds['email']}
                                <a href="mailto:{$ds['email']}" class="contact-icon" title="{$ds['email']}">📧</a>
                                {/if}
                                {if $ds['coordinates']}
                                <a href="https://www.google.com/maps/dir//{$ds['coordinates']}/" target="_blank" class="contact-icon" title="{$ds['coordinates']}">📍</a>
                                {/if}
                            </div>
                        </td>
                        <td>
                            {if isset($planStatusMap[$ds['id']])}
                                {foreach $planStatusMap[$ds['id']] as $plan}
                                <span style="display:inline-flex;align-items:center;gap:6px;">
                                    {if isset($onlineMap[$ds['username']]) || (!empty($ds['pppoe_username']) && isset($onlineMap[$ds['pppoe_username']]))}
                                    <span class="online-dot" title="{Lang::T('Online now')}"></span>
                                    {/if}
                                    <span class="label {if $plan['status'] == 'on'}label-primary{else}label-danger{/if} m-1" 
                                        title="{if $plan['status'] == 'on'}Active until{else}Expired{/if}: {$plan['expiration']} {$plan['time']}">
                                        {$plan['name']}
                                    </span>
                                </span><br>
                                {/foreach}
                            {else}
                                <span class="package-dot {if $ds['status'] == 'Active'}active{else}inactive{/if}"></span>
                            {/if}
                        </td>
                        <td>
                            <span class="badge-service {strtolower($ds['service_type'])}">{$ds['service_type']}</span>
                        </td>
                        <td>
                            {if $ds['pppoe_username']}
                            <small>{$ds['pppoe_username']}</small>
                            {if $ds['pppoe_ip']}<br><small class="text-muted">{$ds['pppoe_ip']}</small>{/if}
                            {else}
                            <span class="text-muted">—</span>
                            {/if}
                        </td>
                        <td>
                            {if $ds['status'] == 'Active'}
                            <span class="status-badge status-on">{Lang::T('Active')}</span>
                            {else}
                            <span class="status-badge status-off">{Lang::T($ds['status'])}</span>
                            {/if}
                        </td>
                        <td><small>{Lang::dateTimeFormat($ds['created_at'])}</small></td>
                        <td>
                            <div class="action-btns">
                                <a href="{$_url}customers/view/{$ds['id']}" class="btn-action btn-view">👁 {Lang::T('View')}</a>
                                <a href="{$_url}customers/edit/{$ds['id']}&token={$csrf_token}" class="btn-action btn-edit">✏️ {Lang::T('Edit')}</a>
                                <a href="{$_url}customers/sync/{$ds['id']}&token={$csrf_token}" class="btn-action btn-sync-act">🔄 {Lang::T('Sync')}</a>
                                <a href="{$_url}plan/recharge/{$ds['id']}&token={$csrf_token}" class="btn-action btn-recharge">💰 {Lang::T('Recharge')}</a>
                            </div>
                        </td>
                    </tr>
                    {foreachelse}
                    <tr><td colspan="{if in_array($_admin['user_type'],['SuperAdmin','Admin'])}11{else}10{/if}" class="text-center text-muted" style="padding:40px;">{Lang::T('No customers found')}</td></tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>

    {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
    <!-- Bulk Delete Bar (hidden until checkboxes selected) -->
    <div id="bulkDeleteBar" style="display:none; position:fixed; bottom:20px; left:50%; transform:translateX(-50%); z-index:1000;
        background:#ef4444; color:white; padding:12px 24px; border-radius:12px; box-shadow:0 8px 30px rgba(239,68,68,0.4);
        align-items:center; gap:16px; font-weight:600;">
        <span id="bulkCount">0 selected</span>
        <button type="button" onclick="submitBulkDelete()" class="btn-modern" 
            style="background:white; color:#ef4444; border:none; padding:8px 20px; border-radius:8px; font-weight:700; cursor:pointer;">
            🗑 Delete Selected
        </button>
        <button type="button" onclick="clearSelection()" style="background:transparent; color:white; border:1px solid rgba(255,255,255,0.5); 
            padding:8px 16px; border-radius:8px; cursor:pointer;">Cancel</button>
    </div>
    {/if}

    {include file="pagination.tpl"}

    {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
    <script>
        function toggleAll(source) {
            document.querySelectorAll('.customer-checkbox').forEach(cb => cb.checked = source.checked);
            updateBulkBar();
        }
        function updateBulkBar() {
            var checked = document.querySelectorAll('.customer-checkbox:checked');
            var count = checked.length;
            var bar = document.getElementById('bulkDeleteBar');
            var countEl = document.getElementById('bulkCount');
            if (count > 0) {
                bar.style.display = 'flex';
                countEl.textContent = count + ' selected';
            } else {
                bar.style.display = 'none';
            }
        }
        function clearSelection() {
            document.querySelectorAll('.customer-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            updateBulkBar();
        }
        function submitBulkDelete() {
            var checked = document.querySelectorAll('.customer-checkbox:checked');
            if (checked.length === 0) return;
            if (!confirm('⚠️ Permanently delete ' + checked.length + ' customer(s) and all their data?\n\nThis CANNOT be undone.')) return;
            
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{$_url}customers/bulk-delete';
            form.innerHTML = '<input type="hidden" name="csrf_token" value="{$csrf_token}">';
            checked.forEach(function(cb) {
                form.innerHTML += '<input type="hidden" name="ids[]" value="' + cb.value + '">';
            });
            document.body.appendChild(form);
            form.submit();
        }
        // Attach listeners
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.customer-checkbox').forEach(function(cb) {
                cb.addEventListener('change', updateBulkBar);
            });
        });
    </script>
    {/if}

    {include file="sections/footer.tpl"}

</div>

<style>
.customers-modern { padding: 0 15px 30px; }
.customers-modern * { box-sizing: border-box; }

.btn-modern {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
    text-decoration: none !important; cursor: pointer; border: none;
    transition: all .2s; white-space: nowrap;
}
.btn-primary-modern { background: #667eea; color: #fff; }
.btn-primary-modern:hover { background: #5a6fd6; color: #fff; }
.btn-upload { background: #11998e; color: #fff; }
.btn-upload:hover { background: #0d7d73; color: #fff; }
.btn-csv { background: #475569; color: #fff; }
.btn-csv:hover { background: #334155; color: #fff; }
.btn-filter-go { background: #667eea; color: #fff; padding: 8px 18px; min-width: 44px; justify-content: center; }
.btn-filter-go:hover { background: #5a6fd6; }

/* Filter Card */
.filter-card {
    background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    padding: 14px 18px; margin-bottom: 14px;
}
.filter-row { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
.filter-field { flex: 1; min-width: 110px; }
.filter-search { flex: 2; min-width: 160px; }
.filter-input, .filter-select {
    width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px;
    font-size: 13px; color: #1e293b; background: #fafbfc; transition: border-color .2s;
    min-height: 38px;
}
.filter-input:focus, .filter-select:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,0.1); }

/* Table */
.table-card-modern {
    background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); overflow: hidden;
}
.table-responsive-wrap { overflow-x: auto; }
.modern-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.modern-table thead th {
    background: #fafbfc; padding: 10px 12px; text-align: left; font-weight: 700; color: #64748b;
    font-size: 11px; text-transform: uppercase; letter-spacing: .5px; border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
}
.modern-table tbody td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
.modern-table tbody tr:hover { background: #f8fafc; }
.row-inactive { background: #fefce8; }
.row-inactive:hover { background: #fef9c3 !important; }

.account-type-badge {
    display: inline-block; padding: 1px 6px; border-radius: 4px; font-size: 10px; font-weight: 700;
    background: #f1f5f9; color: #64748b; margin-left: 6px; vertical-align: middle;
}

.contact-icons { display: flex; gap: 4px; }
.contact-icon { font-size: 15px; text-decoration: none; opacity: .7; transition: opacity .15s; }
.contact-icon:hover { opacity: 1; }

.package-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; }
.package-dot.active { background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.2); }
.package-dot.inactive { background: #94a3b8; }
.online-dot {
    display: inline-block; width: 9px; height: 9px; border-radius: 50%;
    background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.2);
    animation: onlinePulse 1.5s ease-in-out infinite;
}
@keyframes onlinePulse {
    0%, 100% { box-shadow: 0 0 0 2px rgba(34,197,94,0.15); }
    50% { box-shadow: 0 0 0 5px rgba(34,197,94,0.05); }
}

.badge-service {
    display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700;
}
.badge-service.pppoe { background: #eef2ff; color: #4f46e5; }
.badge-service.hotspot { background: #fff7ed; color: #ea580c; }
.badge-service.vpn { background: #f3e8ff; color: #7c3aed; }
.badge-service.others { background: #f1f5f9; color: #64748b; }

.status-badge {
    display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700;
}
.status-on { background: #dcfce7; color: #16a34a; }
.status-off { background: #fee2e2; color: #dc2626; }

.action-btns { display: flex; gap: 4px; flex-wrap: wrap; }
.btn-action {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: 10px; font-weight: 600; text-decoration: none;
    padding: 4px 8px; border-radius: 6px; white-space: nowrap;
    transition: all .15s;
}
.btn-view { background: #dcfce7; color: #166534; }
.btn-view:hover { background: #bbf7d0; }
.btn-edit { background: #fef3c7; color: #92400e; }
.btn-edit:hover { background: #fde68a; }
.btn-sync-act { background: #dbeafe; color: #1e40af; }
.btn-sync-act:hover { background: #bfdbfe; }
.btn-recharge { background: #fce7f3; color: #9d174d; }
.btn-recharge:hover { background: #fbcfe8; }

.text-right { text-align: right !important; }
.text-center { text-align: center !important; }
.text-muted { color: #94a3b8; }
.text-dark { color: #1e293b; text-decoration: none; }
.text-dark:hover { color: #667eea; }

@media (max-width: 768px) {
    .filter-row { gap: 8px; }
    .filter-field { min-width: 90px; }
    .filter-search { min-width: 130px; }
    .modern-table { font-size: 11px; }
    .modern-table thead th, .modern-table tbody td { padding: 6px 8px; }
    .btn-modern { font-size: 11px; padding: 5px 10px; }
    .btn-action { font-size: 9px; padding: 3px 6px; }
}
</style>

{include file="sections/footer.tpl"}