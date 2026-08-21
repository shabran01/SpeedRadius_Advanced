{include file="sections/header.tpl"}

<!-- Modern Plan List -->
<div class="plan-list-modern">

    <!-- Header -->
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <h3 style="margin:0;font-weight:700;color:#1a1a2e;">
            <i class="fa fa-list-alt" style="color:#667eea;margin-right:8px;"></i>{Lang::T('Active Customers')}
        </h3>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
            <a href="{$_url}plan/sync{if $router}?router={$router|escape:'url'}{/if}" class="btn-modern btn-sync">
                <i class="glyphicon glyphicon-refresh"></i> {Lang::T('Sync')}{if $router} [{$router}]{/if}
            </a>
            {/if}
            <a href="{$_url}plan/csv{$append_url}" class="btn-modern btn-csv">
                <i class="fa fa-download"></i> CSV
            </a>
            <a href="{$_url}plan/recharge" class="btn-modern btn-primary-modern">
                <i class="ion ion-android-add"></i> {Lang::T('Recharge')}
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="post" action="{$_url}plan/list/" class="filter-card">
        <div class="filter-row">
            <div class="filter-field filter-search">
                <input type="text" name="search" placeholder="{Lang::T('Search username...')}" value="{$search}" class="filter-input">
                {if $search}<a href="{$_url}plan/list" class="filter-clear" title="{Lang::T('Clear')}">✕</a>{/if}
            </div>
            <div class="filter-field">
                <select name="router" class="filter-select">
                    <option value="">{Lang::T('All Locations')}</option>
                    {foreach $routers as $r}
                    <option value="{$r}" {if $router eq $r}selected{/if}>{$r}</option>
                    {/foreach}
                </select>
            </div>
            <div class="filter-field">
                <select name="plan" class="filter-select">
                    <option value="">{Lang::T('All Plans')}</option>
                    {foreach $plans as $p}
                    <option value="{$p['id']}" {if $plan eq $p['id']}selected{/if}>{$p['name_plan']}</option>
                    {/foreach}
                </select>
            </div>
            <div class="filter-field">
                <select name="status" class="filter-select">
                    <option value="-">{Lang::T('All Status')}</option>
                    <option value="on" {if $status eq 'on'}selected{/if}>{Lang::T('Active')}</option>
                    <option value="off" {if $status eq 'off'}selected{/if}>{Lang::T('Expired')}</option>
                </select>
            </div>
            <div class="filter-field">
                <select name="type" class="filter-select">
                    <option value="">{Lang::T('All Types')}</option>
                    <option value="PPPOE" {if $type eq 'PPPOE'}selected{/if}>PPPoE</option>
                    <option value="Hotspot" {if $type eq 'Hotspot'}selected{/if}>Hotspot</option>
                </select>
            </div>
            <button type="submit" class="btn-modern btn-filter-go"><i class="fa fa-search"></i></button>
        </div>
    </form>

    <!-- Bulk Actions -->
    {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
    <div class="bulk-bar" id="bulk-bar" style="display:none;">
        <span class="bulk-count" id="bulk-count">0 {Lang::T('selected')}</span>
        <button id="bulk-delete" class="btn-modern btn-danger-modern" disabled>
            <i class="glyphicon glyphicon-trash"></i> {Lang::T('Delete Selected')}
        </button>
    </div>
    {/if}

    <!-- Table -->
    <div class="table-card-modern">
        <div class="table-responsive-wrap">
            <table class="modern-table">
                <thead>
                    <tr>
                        {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                        <th style="width:40px;"><input type="checkbox" id="select-all" title="{Lang::T('Select All')}"></th>
                        {/if}
                        <th>{Lang::T('Username')}</th>
                        <th>{Lang::T('Full Name')}</th>
                        <th>{Lang::T('Plan')}</th>
                        <th class="text-right">{Lang::T('Price')}</th>
                        <th>{Lang::T('Type')}</th>
                        <th>{Lang::T('Created')}</th>
                        <th>{Lang::T('Expires')}</th>
                        <th>{Lang::T('Method')}</th>
                        <th>{Lang::T('Router')}</th>
                        <th>{Lang::T('Actions')}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $d as $ds}
                    <tr class="{if $ds['status']=='off'}row-expired{/if}">
                        {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                        <td><input type="checkbox" class="plan-checkbox" value="{$ds['id']}"></td>
                        {/if}
                        <td>
                            {if $ds['customer_id'] == '0'}
                            <a href="{$_url}plan/voucher/&search={$ds['username']}">{$ds['username']}</a>
                            {else}
                            <a href="{$_url}customers/viewu/{$ds['username']}"><strong>{$ds['username']}</strong></a>
                            {/if}
                        </td>
                        <td><span class="text-muted-sm">{if $ds['customer_id'] == '0'}-{else}{$ds['customer_fullname']|default:'-'}{/if}</span></td>
                        <td>
                            {if $ds['type'] == 'Hotspot'}
                            <a href="{$_url}services/edit/{$ds['plan_id']}">{$ds['namebp']}</a>
                            {elseif $ds['type'] == 'PPPOE'}
                            <a href="{$_url}services/pppoe-edit/{$ds['plan_id']}">{$ds['namebp']}</a>
                            {else}
                            {$ds['namebp']}
                            {/if}
                            {if $ds['status']=='on'}<span class="badge-status active" title="{Lang::T('Active')}"></span>{/if}
                        </td>
                        <td class="text-right"><strong>{$_c['currency_code']} {number_format($ds['plan_price'], 0, $_c['dec_point'], $_c['thousands_sep'])}</strong></td>
                        <td><span class="badge-type {strtolower($ds['type'])}">{$ds['type']}</span></td>
                        <td><small>{Lang::dateAndTimeFormat($ds['recharged_on'],$ds['recharged_time'])}</small></td>
                        <td><small>{Lang::dateAndTimeFormat($ds['expiration'],$ds['time'])}</small></td>
                        <td><small>{$ds['method']}</small></td>
                        <td><small>{$ds['routers']}</small></td>
                        <td>
                            <div class="action-btns">
                                <a href="{$_url}plan/edit/{$ds['id']}" class="btn-action btn-edit" title="{Lang::T('Edit')}">✏️ {Lang::T('Edit')}</a>
                                {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                                <a href="{$_url}plan/delete/{$ds['id']}" class="btn-action btn-delete" onclick="return ask(this, '{Lang::T('Delete')}?')" title="{Lang::T('Delete')}">🗑️ {Lang::T('Delete')}</a>
                                {/if}
                                {if $ds['status']=='off' && $_c['extend_expired']}
                                <a href="javascript:extend('{$ds['id']}')" class="btn-action btn-extend" title="{Lang::T('Extend')}">🔁 {Lang::T('Extend')}</a>
                                {/if}
                            </div>
                        </td>
                    </tr>
                    {foreachelse}
                    <tr><td colspan="11" class="text-center text-muted" style="padding:40px;">{Lang::T('No records found')}</td></tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>

    {include file="pagination.tpl"}

</div>

<input type="hidden" id="_csrf_token" value="{$_csrf_token}">

<script>
function extend(idP) {
    var res = prompt("Extend for many days?", "3");
    if (res) {
        if (confirm("Extend for " + res + " days?")) {
            window.location.href = "{$_url}plan/extend/" + idP + "/" + res + "&stoken={App::getToken()}";
        }
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.plan-checkbox');
    const bulkBar = document.getElementById('bulk-bar');
    const bulkCount = document.getElementById('bulk-count');
    const bulkDelete = document.getElementById('bulk-delete');

    function updateBulk() {
        const checked = document.querySelectorAll('.plan-checkbox:checked');
        const count = checked.length;
        if (bulkBar) bulkBar.style.display = count > 0 ? 'flex' : 'none';
        if (bulkCount) bulkCount.textContent = count + ' ' + '{Lang::T("selected")}';
        if (bulkDelete) bulkDelete.disabled = count === 0;
        if (selectAll) selectAll.checked = count === checkboxes.length && count > 0;
    }
    if (selectAll) selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulk();
    });
    checkboxes.forEach(cb => cb.addEventListener('change', updateBulk));
    if (bulkDelete) bulkDelete.addEventListener('click', function() {
        const ids = Array.from(document.querySelectorAll('.plan-checkbox:checked')).map(cb => cb.value);
        if (ids.length && confirm('{Lang::T("Are you sure you want to delete")} ' + ids.length + ' {Lang::T("selected items")}?')) {
            const form = document.createElement('form');
            form.method = 'POST'; form.action = '{$_url}plan/bulk-delete'; form.style.display = 'none';
            form.innerHTML = '<input type="hidden" name="token" value="' + document.getElementById('_csrf_token').value + '"><input type="hidden" name="ids" value="' + ids.join(',') + '">';
            document.body.appendChild(form); form.submit();
        }
    });
});
</script>

<style>
/* Modern Plan List */
.plan-list-modern { padding: 0 15px 30px; }
.plan-list-modern * { box-sizing: border-box; }

.btn-modern {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
    text-decoration: none !important; cursor: pointer; border: none;
    transition: all .2s; white-space: nowrap;
}
.btn-primary-modern { background: #667eea; color: #fff; }
.btn-primary-modern:hover { background: #5a6fd6; color: #fff; }
.btn-sync { background: #e2e8f0; color: #475569; }
.btn-sync:hover { background: #cbd5e1; color: #334155; }
.btn-csv { background: #11998e; color: #fff; }
.btn-csv:hover { background: #0d7d73; color: #fff; }
.btn-filter-go { background: #667eea; color: #fff; padding: 8px 18px; min-width: 44px; justify-content: center; }
.btn-filter-go:hover { background: #5a6fd6; }
.btn-danger-modern { background: #ef4444; color: #fff; }
.btn-danger-modern:hover { background: #dc2626; color: #fff; }
.btn-danger-modern:disabled { opacity: .5; cursor: not-allowed; }

/* Filter Card */
.filter-card {
    background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    padding: 14px 18px; margin-bottom: 14px;
}
.filter-row { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }
.filter-field { flex: 1; min-width: 130px; }
.filter-search { flex: 2; min-width: 180px; position: relative; }
.filter-input, .filter-select {
    width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px;
    font-size: 13px; color: #1e293b; background: #fafbfc; transition: border-color .2s;
    min-height: 38px;
}
.filter-input:focus, .filter-select:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,0.1); }
.filter-clear { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #ef4444; font-weight: 700; text-decoration: none; font-size: 14px; }

/* Bulk Bar */
.bulk-bar {
    display: flex; align-items: center; gap: 10px;
    background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px;
    padding: 8px 16px; margin-bottom: 10px;
}
.bulk-count { font-size: 13px; font-weight: 600; color: #991b1b; }

/* Table Card */
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
.modern-table tbody td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
.modern-table tbody tr:hover { background: #f8fafc; }
.row-expired { background: #fff5f5; }
.row-expired:hover { background: #fef2f2 !important; }

.badge-type {
    display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700;
}
.badge-type.hotspot { background: #fff7ed; color: #ea580c; }
.badge-type.pppoe { background: #eef2ff; color: #4f46e5; }
.badge-status {
    display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-left: 6px;
}
.badge-status.active { background: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,0.2); }

.action-btns { display: flex; gap: 4px; flex-wrap: wrap; }
.btn-action {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: 11px; font-weight: 600; text-decoration: none;
    padding: 4px 10px; border-radius: 6px; white-space: nowrap;
    transition: all .15s;
}
.btn-edit { background: #fef3c7; color: #92400e; }
.btn-edit:hover { background: #fde68a; color: #78350f; }
.btn-delete { background: #fee2e2; color: #991b1b; }
.btn-delete:hover { background: #fecaca; color: #7f1d1d; }
.btn-extend { background: #dbeafe; color: #1e40af; }
.btn-extend:hover { background: #bfdbfe; color: #1e3a8a; }

.text-right { text-align: right !important; }
.text-center { text-align: center !important; }
.text-muted { color: #94a3b8; }
.text-muted-sm { color: #94a3b8; font-size: 12px; }

@media (max-width: 768px) {
    .filter-row { gap: 8px; }
    .filter-field { min-width: 100px; }
    .filter-search { min-width: 140px; }
    .modern-table { font-size: 11px; }
    .modern-table thead th, .modern-table tbody td { padding: 6px 8px; }
    .btn-modern { font-size: 11px; padding: 5px 10px; }
}
</style>

{include file="sections/footer.tpl"}