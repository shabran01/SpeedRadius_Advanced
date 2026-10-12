{include file="sections/header.tpl"}
{include file="mwaElectron_style.tpl"}

<div class="mw-hero">
    <h2><i class="fa fa-list"></i> M-WA Electron Logs</h2>
    <p class="sub">{$total} message{if $total != 1}s{/if} recorded</p>
</div>

<div class="mw-panel">
    <h3><i class="fa fa-search"></i> Search</h3>
    <form method="get" action="{$_url}plugin/mwa_electron_logs" class="mw-row">
        {* A browser drops the query string already present in a form's action when it
           submits a GET, so _route has to be resubmitted or the search falls through
           to the dashboard instead of filtering the logs. *}
        <input type="hidden" name="_route" value="plugin/mwa_electron_logs">
        <input type="text" name="search" value="{$search|escape:'html'}" placeholder="Number, message or error..." style="flex:1;padding:9px 11px;border:1px solid #dce6ec;border-radius:10px;font-size:13px;">
        <button type="submit" class="mw-btn b-dk" style="width:auto;margin:0;padding:11px 22px;"><i class="fa fa-search"></i> Search</button>
        {if $search}
        <a href="{$_url}plugin/mwa_electron_logs" class="mw-btn b-o" style="width:auto;margin:0;padding:11px 22px;">Clear</a>
        {/if}
    </form>
</div>

<div class="mw-panel">
    <h3><i class="fa fa-envelope-o"></i> Log Entries</h3>
    <div class="mw-scroll">
        <table class="mw-table">
            <thead>
                <tr>
                    <th style="width:60px;">ID</th>
                    <th style="width:130px;">Number</th>
                    <th>Message</th>
                    <th style="width:100px;">Status</th>
                    <th>Response</th>
                    <th style="width:150px;">When</th>
                </tr>
            </thead>
            <tbody>
            {if $logs}
                {foreach $logs as $log}
                <tr>
                    <td>{$log->id}</td>
                    <td>{$log->phone_number}</td>
                    <td>{$log->message|truncate:120}</td>
                    <td>
                        {if $log->status == 'delivered'}
                            <span class="mw-badge bg-ok">delivered</span>
                        {elseif $log->status == 'failed'}
                            <span class="mw-badge bg-bad">failed</span>
                        {else}
                            <span class="mw-badge bg-neutral">{$log->status}</span>
                        {/if}
                    </td>
                    <td class="mw-muted">{$log->response|truncate:160}</td>
                    <td class="mw-muted">{$log->created_at}</td>
                </tr>
                {/foreach}
            {else}
                <tr><td colspan="6" class="mw-muted">No log entries found.</td></tr>
            {/if}
            </tbody>
        </table>
    </div>

    {if $totalPages > 1}
    <div class="mw-row" style="margin-top:16px;align-items:center;">
        <div>
            {if $page > 1}
                <a href="{$_url}plugin/mwa_electron_logs&page={$page-1}{if $search}&search={$search|escape:'url'}{/if}" class="mw-btn b-o" style="width:auto;margin:0;padding:9px 18px;"><i class="fa fa-arrow-left"></i> Previous</a>
            {/if}
        </div>
        <div class="mw-muted" style="text-align:center;">Page {$page} of {$totalPages}</div>
        <div style="text-align:right;">
            {if $page < $totalPages}
                <a href="{$_url}plugin/mwa_electron_logs&page={$page+1}{if $search}&search={$search|escape:'url'}{/if}" class="mw-btn b-o" style="width:auto;margin:0;padding:9px 18px;">Next <i class="fa fa-arrow-right"></i></a>
            {/if}
        </div>
    </div>
    {/if}
</div>

<div class="mw-panel">
    <h3><i class="fa fa-wrench"></i> Actions</h3>
    <a href="{$_url}plugin/mwa_electron" class="mw-btn b-dk"><i class="fa fa-dashboard"></i> Back To Dashboard</a>
    <a href="{$_url}plugin/mwa_electron_config" class="mw-btn b-dk"><i class="fa fa-cog"></i> Configuration</a>
    <form action="{$_url}plugin/mwa_electron_logs" method="post" style="margin:0;">
        <button type="submit" name="delete_all" value="1" class="mw-btn b-r" onclick="return confirm('Delete every M-WA log entry? This cannot be undone.')">
            <i class="fa fa-trash"></i> Delete All Logs
        </button>
    </form>
</div>

</div>

{include file="sections/footer.tpl"}
