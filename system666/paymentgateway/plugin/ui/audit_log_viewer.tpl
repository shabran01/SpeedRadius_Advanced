{include file="sections/header.tpl"}

<div class="p-4 md:p-6" style="font-family:'Inter','Segoe UI',Arial,sans-serif;">

    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">📋 Audit Log Viewer</h1>
            <p class="text-slate-400 text-sm mt-0.5">Search, filter, and export system activity logs</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('settings-modal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-xl text-sm font-semibold hover:bg-indigo-200 transition-colors">
                ⚙️ Auto-Cleanup
            </button>
            <a href="{$_url}plugin/audit_log_viewer/export-csv?type={$typeFilter}&from={$dateFrom}&to={$dateTo}&q={$search}" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 transition-colors">
                📥 Export CSV
            </a>
            <button onclick="document.getElementById('cleanup-modal').classList.remove('hidden')" class="px-4 py-2 bg-rose-600 text-white rounded-xl text-sm font-semibold hover:bg-rose-700 transition-colors">
                🗑️ Cleanup
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-5">
        <div class="alv-stat">
            <div class="alv-stat-lbl">📊 Total Logs</div>
            <div class="alv-stat-val">{$totalLogs|number_format}</div>
            <div class="alv-stat-sub">All time</div>
        </div>
        <div class="alv-stat">
            <div class="alv-stat-lbl">📅 Today</div>
            <div class="alv-stat-val">{$todayCount|number_format}</div>
            <div class="alv-stat-sub">New entries</div>
        </div>
        <div class="alv-stat">
            <div class="alv-stat-lbl">⚠️ Errors</div>
            <div class="alv-stat-val" style="color:{if $errorCount > 0}#ef4444{else}#1e293b{/if}">{$errorCount|number_format}</div>
            <div class="alv-stat-sub">Failures & errors</div>
        </div>
        <div class="alv-stat">
            <div class="alv-stat-lbl">🔍 Filtered</div>
            <div class="alv-stat-val">{$totalFiltered|number_format}</div>
            <div class="alv-stat-sub">Current view</div>
        </div>
        <div class="alv-stat">
            <div class="alv-stat-lbl">📂 Types</div>
            <div class="alv-stat-val">{$types|@count}</div>
            <div class="alv-stat-sub">Unique categories</div>
        </div>
    </div>

    <!-- Type Stats Badges -->
    {if $typeStats|@count > 0}
    <div class="flex flex-wrap gap-2 mb-5">
        {foreach $typeStats as $ts}
            <a href="{$_url}plugin/audit_log_viewer?type={$ts->type}&from={$dateFrom}&to={$dateTo}&q={$search}"
               class="alv-badge {if $typeFilter == $ts->type}bg-indigo-100 text-indigo-700 font-bold{else}bg-slate-100 text-slate-600{/if} hover:bg-indigo-50 transition-colors no-underline">
                {$ts->type} <strong>{$ts->cnt}</strong>
            </a>
        {/foreach}
        {if $typeFilter != 'all'}
            <a href="{$_url}plugin/audit_log_viewer?from={$dateFrom}&to={$dateTo}&q={$search}" class="alv-badge bg-rose-100 text-rose-600 font-bold hover:bg-rose-200 transition-colors no-underline">
                ✕ Clear Filter
            </a>
        {/if}
    </div>
    {/if}

    <!-- Filter Bar -->
    <div class="alv-card mb-5">
        <form method="get" action="{$_url}plugin/audit_log_viewer" class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="_route" value="plugin/audit_log_viewer">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-1">Type</label>
                <select name="type" class="border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700">
                    <option value="all">All Types</option>
                    {foreach $types as $t}
                        <option value="{$t->type}" {if $typeFilter == $t->type}selected{/if}>{$t->type}</option>
                    {/foreach}
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-1">From</label>
                <input type="date" name="from" value="{$dateFrom}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-1">To</label>
                <input type="date" name="to" value="{$dateTo}" class="border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700">
            </div>
            <div style="flex:1; min-width:200px;">
                <label class="block text-xs font-bold uppercase text-slate-400 mb-1">Search</label>
                <input type="text" name="q" value="{$search|escape}" placeholder="Search logs..." class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-700">
            </div>
            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Filter</button>
            <a href="{$_url}plugin/audit_log_viewer" class="px-5 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm font-semibold hover:bg-slate-50">Reset</a>
        </form>
    </div>

    <!-- Log Table -->
    <div class="alv-card">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <th class="pb-3 pr-3 w-16">ID</th>
                        <th class="pb-3 pr-3 w-40">Date / Time</th>
                        <th class="pb-3 pr-3 w-32">Type</th>
                        <th class="pb-3 pr-3">Description</th>
                        <th class="pb-3 pr-3 w-24">User</th>
                        <th class="pb-3 pr-3 w-36">IP / Source</th>
                        <th class="pb-3 w-16">Action</th>
                    </tr>
                </thead>
                <tbody>
                    {if $logs|@count > 0}
                        {foreach $logs as $log}
                        <tr class="border-b border-slate-50 alv-row">
                            <td class="py-2 pr-3 text-slate-400 text-xs">#{$log->id}</td>
                            <td class="py-2 pr-3 text-slate-600 whitespace-nowrap">{$log->date}</td>
                            <td class="py-2 pr-3">
                                {if strpos(strtolower($log->type), 'error') !== false || strpos(strtolower($log->type), 'fail') !== false}
                                    <span class="alv-badge bg-red-100 text-red-700">{$log->type}</span>
                                {elseif strpos(strtolower($log->type), 'admin') !== false || strpos(strtolower($log->type), 'superadmin') !== false}
                                    <span class="alv-badge bg-purple-100 text-purple-700">{$log->type}</span>
                                {elseif strpos(strtolower($log->type), 'customer') !== false}
                                    <span class="alv-badge bg-blue-100 text-blue-700">{$log->type}</span>
                                {else}
                                    <span class="alv-badge bg-slate-100 text-slate-600">{$log->type}</span>
                                {/if}
                            </td>
                            <td class="py-2 pr-3 text-slate-700">
                                {if $search && strpos(strtolower($log->description), strtolower($search)) !== false}
                                    {$log->description|replace:$search:"<mark class='bg-yellow-200 px-0.5 rounded'>$search</mark>"}
                                {else}
                                    {$log->description}
                                {/if}
                            </td>
                            <td class="py-2 pr-3 text-slate-500 text-xs">
                                {if $log->userid}
                                <a href="{$_url}plugin/audit_log_viewer?q={$log->userid}&type={$typeFilter}&from={$dateFrom}&to={$dateTo}"
                                   title="Filter all logs from this user"
                                   class="text-indigo-500 hover:text-indigo-700 hover:underline">{$log->userid}</a>
                                {else}—{/if}
                            <td class="py-2 pr-3 text-slate-400 text-xs font-mono">
                                <a href="https://whatismyipaddress.com/ip/{$log->ip|escape:'url'}" target="_blank" rel="noopener"
                                   title="Lookup IP location — opens in new tab"
                                   class="text-indigo-500 hover:text-indigo-700 hover:underline">
                                   {$log->ip|default:'—'} 🌍</a>
                            <td class="py-2">
                                <a href="{$_url}plugin/audit_log_viewer/delete/{$log->id}" onclick="return confirm('Delete log #{$log->id}?')" class="text-rose-500 hover:text-rose-700 text-xs font-semibold">Del</a>
                            </td>
                        </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                📭 No log entries found matching your filters.
                            </td>
                        </tr>
                    {/if}
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        {if $totalPages > 1}
        <div class="flex justify-between items-center mt-4 pt-3 border-t border-slate-100">
            <span class="text-xs text-slate-400">Page {$currentPage} of {$totalPages} · {$totalFiltered|number_format} entries · {$perPage}/page</span>
            <div class="flex gap-1">
                {if $currentPage > 1}
                    <a href="{$_url}plugin/audit_log_viewer?type={$typeFilter}&from={$dateFrom}&to={$dateTo}&q={$search}&page={$currentPage-1}" class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm text-slate-600 hover:bg-slate-50">← Prev</a>
                {/if}
                {for $p=max(1,$currentPage-3) to min($totalPages,$currentPage+3)}
                    <a href="{$_url}plugin/audit_log_viewer?type={$typeFilter}&from={$dateFrom}&to={$dateTo}&q={$search}&page={$p}"
                       class="px-3 py-1.5 border rounded-lg text-sm {if $p == $currentPage}bg-indigo-600 text-white border-indigo-600{else}border-slate-200 text-slate-600 hover:bg-slate-50{/if}">{$p}</a>
                {/for}
                {if $currentPage < $totalPages}
                    <a href="{$_url}plugin/audit_log_viewer?type={$typeFilter}&from={$dateFrom}&to={$dateTo}&q={$search}&page={$currentPage+1}" class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm text-slate-600 hover:bg-slate-50">Next →</a>
                {/if}
            </div>
        </div>
        {elseif $logs|@count > 0}
        <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-400">
            Page 1 of 1 · {$totalFiltered|number_format} entries · {$perPage}/page
        </div>
        {/if}
    </div>
</div>

<!-- Auto-Cleanup Settings Modal -->
<div id="settings-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl">
        <h3 class="text-lg font-bold text-slate-800 mb-4">⚙️ Auto-Cleanup Settings</h3>
        <form method="post" action="{$_url}plugin/audit_log_viewer/save-settings">
            <div class="bg-slate-50 rounded-xl p-4 mb-4">
                <label class="flex items-center gap-3 cursor-pointer mb-4">
                    <input type="checkbox" name="alv_auto_enabled" value="yes" {if $alv_auto_enabled == 'yes'}checked{/if}
                           onchange="document.getElementById('alv-keep-days').disabled = !this.checked"
                           class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm font-semibold text-slate-700">Enable automatic log cleanup via cron</span>
                </label>
                <label class="block text-xs font-bold uppercase text-slate-400 mb-2">Delete logs older than:</label>
                <select name="alv_keep_days" id="alv-keep-days" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm" {if $alv_auto_enabled != 'yes'}disabled{/if}>
                    <option value="7" {if $alv_keep_days == '7'}selected{/if}>7 days (recommended)</option>
                    <option value="30" {if $alv_keep_days == '30'}selected{/if}>30 days</option>
                    <option value="90" {if $alv_keep_days == '90'}selected{/if}>90 days</option>
                    <option value="180" {if $alv_keep_days == '180'}selected{/if}>180 days</option>
                    <option value="365" {if $alv_keep_days == '365'}selected{/if}>1 year</option>
                </select>
                {if $alv_last_cleanup && $alv_last_cleanup != 'Never'}
                <p class="text-xs text-slate-400 mt-3">⏱️ Last auto-cleanup: {$alv_last_cleanup}</p>
                {/if}
            </div>
            <p class="text-xs text-slate-400 mb-4">💡 When enabled, the system cron will automatically delete logs older than the selected period. No manual cleanup needed.</p>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('settings-modal').classList.add('hidden')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<!-- Cleanup Modal -->
<div id="cleanup-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl">
        <h3 class="text-lg font-bold text-slate-800 mb-4">🗑️ Clean Up Old Logs</h3>
        <form method="post" action="{$_url}plugin/audit_log_viewer/cleanup">
            <label class="block text-sm font-semibold text-slate-600 mb-2">Delete logs older than:</label>
            <select name="keep_days" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm mb-3">
                <option value="1">1 day</option>
                <option value="7">7 days</option>
                <option value="30" selected>30 days</option>
                <option value="60">60 days</option>
                <option value="90">90 days</option>
                <option value="180">180 days</option>
                <option value="365">1 year</option>
                <option value="all">🗑️ All (Delete everything)</option>
            </select>
            <label class="block text-sm font-semibold text-slate-600 mb-2">Only for type:</label>
            <select name="clean_type" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm mb-4">
                <option value="all">All Types</option>
                {foreach $types as $t}
                    <option value="{$t->type}">{$t->type}</option>
                {/foreach}
            </select>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('cleanup-modal').classList.add('hidden')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-sm font-semibold hover:bg-rose-700">Delete Logs</button>
            </div>
        </form>
    </div>
</div>

{include file="sections/footer.tpl"}
