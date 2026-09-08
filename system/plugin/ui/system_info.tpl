{include file="sections/header.tpl"}

{literal}
<style>
.system-info-page { color: #1f2937; font-family: 'Segoe UI', system-ui, -apple-system, Roboto, 'Helvetica Neue', Arial, sans-serif; }
.system-info-hero { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; margin: 10px 0 24px; padding: 24px 28px; border: 1px solid #e6eaf2; border-radius: 14px; background: linear-gradient(120deg, #f8faff 0%, #eef2ff 100%); }
.system-info-page .row { margin-bottom: 0; }
.system-info-page .row + .row { margin-top: 18px; }
.system-info-page [class*="col-"] { margin-bottom: 16px; }
.system-info-page .row + .row [class*="col-"] { margin-bottom: 0; }
.system-info-hero h1 { margin: 0 0 6px; font-size: 26px; font-weight: 700; letter-spacing: -.3px; color: #111827; }
.system-info-hero p { margin: 0; color: #64748b; font-size: 13px; }
.system-info-kicker { margin: 0 0 8px; color: #6366f1; font-size: 11px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; }
.system-info-panel { margin-bottom: 20px; border: 1px solid #e6eaf2; border-radius: 14px; background: #fff; box-shadow: 0 8px 24px rgba(79, 70, 229, .05); overflow: hidden; }
.system-info-panel-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 18px 24px; border-bottom: 1px solid #eef1f6; background: #fbfcff; }
.system-info-panel-head h2 { margin: 0; font-size: 15px; font-weight: 700; letter-spacing: -.2px; color: #111827; }
.system-info-panel-head p { margin: 4px 0 0; color: #94a3b8; font-size: 12px; }
.system-info-panel-body { padding: 22px 24px; }
.system-info-metric { position: relative; display: flex; flex-direction: column; min-height: 180px; padding: 22px 24px; border: 1px solid #e6eaf2; border-radius: 14px; background: #fff; box-shadow: 0 6px 18px rgba(79, 70, 229, .05); overflow: hidden; }
.system-info-metric:before { position: absolute; top: 0; left: 0; width: 5px; height: 100%; content: ''; }
.system-info-metric.cpu:before { background: linear-gradient(180deg, #6366f1, #8b5cf6); }
.system-info-metric.memory:before { background: linear-gradient(180deg, #06b6d4, #3b82f6); }
.system-info-metric.storage:before { background: linear-gradient(180deg, #10b981, #14b8a6); }
.system-info-metric-icon { width: 38px; height: 38px; margin-bottom: 18px; display: flex; align-items: center; justify-content: center; color: #fff; border-radius: 10px; }
.system-info-metric.cpu .system-info-metric-icon { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
.system-info-metric.memory .system-info-metric-icon { background: linear-gradient(135deg, #06b6d4, #3b82f6); }
.system-info-metric.storage .system-info-metric-icon { background: linear-gradient(135deg, #10b981, #14b8a6); }
.system-info-metric-label { margin-bottom: 8px; color: #94a3b8; font-size: 11px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; }
.system-info-metric-value { margin: 0; color: #111827; font-size: 28px; font-weight: 700; letter-spacing: -.4px; line-height: 1.15; }
.system-info-metric-detail { min-height: 18px; margin: 8px 0 20px; color: #64748b; font-size: 12px; }
.system-info-progress { height: 7px; margin: auto 0 0; border-radius: 999px; background: #eef1f6; box-shadow: none; }
.system-info-progress .progress-bar { border-radius: 999px; box-shadow: none; }
.system-info-metric.cpu .system-info-progress .progress-bar { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
.system-info-metric.memory .system-info-progress .progress-bar { background: linear-gradient(90deg, #06b6d4, #3b82f6); }
.system-info-metric.storage .system-info-progress .progress-bar { background: linear-gradient(90deg, #10b981, #14b8a6); }
.system-info-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); border-top: 1px solid #eef1f6; }
.system-info-detail { display: flex; align-items: flex-start; justify-content: space-between; gap: 24px; padding: 15px 24px; border-right: 1px solid #eef1f6; border-bottom: 1px solid #eef1f6; }
.system-info-detail:nth-child(odd) { background: #fbfcff; }
.system-info-detail:nth-child(even) { border-right: 0; }
.system-info-detail dt { color: #94a3b8; font-size: 12px; font-weight: 600; white-space: nowrap; }
.system-info-detail dd { margin: 0; color: #334155; font-size: 12px; font-weight: 600; text-align: right; word-break: break-word; overflow-wrap: anywhere; }
.system-info-service-list { margin: 0; }
.system-info-service { display: flex; align-items: center; justify-content: space-between; min-height: 54px; padding: 12px 0; border-bottom: 1px solid #eef1f6; }
.system-info-service:last-child { border-bottom: 0; padding-bottom: 0; }
.system-info-service:first-child { padding-top: 0; }
.system-info-service-name { color: #334155; font-size: 13px; font-weight: 600; }
.system-info-service-status small { display: inline-block; padding: 5px 12px; border-radius: 999px; font-size: 11px; font-weight: 700; text-transform: capitalize; }
.system-info-service-status .label { float: none; }
.system-info-reload { white-space: nowrap; }
.system-info-reload .btn { border: 0; border-radius: 9px; background: linear-gradient(135deg, #6366f1, #8b5cf6); box-shadow: 0 6px 14px rgba(99, 102, 241, .35); }
.system-info-result { margin: 0 0 20px; padding: 14px 18px; border: 1px solid #a7e3bd; border-left: 4px solid #10b981; border-radius: 10px; background: #f0fdf6; color: #0f766e; font-size: 13px; }
.system-info-result.failed { border-color: #f9c1c1; border-left-color: #ef4444; background: #fef2f2; color: #b91c1c; }
.system-info-result p { margin: 0; }
@media (max-width: 767px) {
    .system-info-hero { display: block; }
    .system-info-hero .system-info-reload { margin-top: 16px; }
    .system-info-detail-grid { grid-template-columns: 1fr; }
    .system-info-detail { border-right: 0; }
    .system-info-detail:nth-child(odd) { background: transparent; }
}
</style>
{/literal}

<div class="system-info-page">
    <div class="system-info-hero">
        <div>
            <p class="system-info-kicker">Operations / Settings</p>
            <h1>System Information</h1>
            <p>A clear view of the application host, resources, and core services.</p>
        </div>
        <form class="system-info-reload" action="{$_url}plugin/system_info" method="post">
            <input type="hidden" name="reload" value="true">
            <input type="hidden" name="csrf_token" value="{$csrf_token}">
            <button type="submit" class="btn btn-primary" title="Reload FreeRADIUS"
                onclick="return confirm('Are you sure you want to reload FreeRADIUS?')">
                <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Reload FreeRADIUS
            </button>
        </form>
    </div>

    <div class="row">
        <div class="col-md-4">
            {assign var='cpuUsageValue' value=$systemInfo['CPU Usage']|default:'Unknown'}
            {assign var='cpuUsageNumber' value=$cpuUsageValue|regex_replace:"/[^0-9.]+/":''}
            <div class="system-info-metric cpu">
                <span class="system-info-metric-icon"><span class="glyphicon glyphicon-dashboard"></span></span>
                <div class="system-info-metric-label">CPU Load</div>
                <p class="system-info-metric-value">{$cpuUsageValue}</p>
                <p class="system-info-metric-detail">{$systemInfo['CPU Cores']|default:'Unknown'} logical cores available</p>
                <div class="progress system-info-progress"><div class="progress-bar" role="progressbar" style="width: {if $cpuUsageNumber != ''}{$cpuUsageNumber}{else}0{/if}%;"></div></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="system-info-metric memory">
                <span class="system-info-metric-icon"><span class="glyphicon glyphicon-tasks"></span></span>
                <div class="system-info-metric-label">Memory</div>
                <p class="system-info-metric-value">{$memory_usage.used|default:0} MB</p>
                <p class="system-info-metric-detail">of {$memory_usage.total|default:0} MB used - {$memory_usage.free|default:0} MB available</p>
                <div class="progress system-info-progress"><div class="progress-bar" role="progressbar" style="width: {$memory_usage.used_percentage|default:0}%;"></div></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="system-info-metric storage">
                <span class="system-info-metric-icon"><span class="glyphicon glyphicon-hdd"></span></span>
                <div class="system-info-metric-label">Storage</div>
                <p class="system-info-metric-value">{$disk_usage['used_percentage']|default:'0%'}</p>
                <p class="system-info-metric-detail">{$disk_usage['used']|default:'0 B'} used - {$disk_usage['free']|default:'0 B'} free</p>
                <div class="progress system-info-progress"><div class="progress-bar" role="progressbar" style="width: {$disk_usage['used_percentage']|default:'0%'};"></div></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <section class="system-info-panel">
                <div class="system-info-panel-head">
                    <div>
                        <h2>Host and Application Details</h2>
                        <p>Runtime information for this installation.</p>
                    </div>
                </div>
                <dl class="system-info-detail-grid">
                    {foreach $systemInfo as $key => $value}
                        <div class="system-info-detail">
                            <dt>{$key|escape}</dt>
                            <dd>{$value|escape}</dd>
                        </div>
                    {/foreach}
                </dl>
            </section>
        </div>
        <div class="col-md-4">
            <section class="system-info-panel">
                <div class="system-info-panel-head">
                    <div>
                        <h2>Service Health</h2>
                        <p>Current process availability.</p>
                    </div>
                </div>
                <div class="system-info-panel-body system-info-service-list">
                    {foreach $serviceTable.rows as $row}
                        <div class="system-info-service">
                            <span class="system-info-service-name">{$row.0|escape}</span>
                            <span class="system-info-service-status">{$row.1}</span>
                        </div>
                    {/foreach}
                </div>
            </section>
        </div>
    </div>

    {if isset($output) && $output != ''}
        <div class="system-info-result{if $returnCode !== 0} failed{/if}">
            {if $returnCode === 0}
                <p><span class="glyphicon glyphicon-ok-circle"></span> FreeRADIUS service reloaded successfully.</p>
            {else}
                <p><span class="glyphicon glyphicon-warning-sign"></span> FreeRADIUS reload failed (code {$returnCode}): {$output|escape}</p>
            {/if}
        </div>
    {/if}
</div>

<script>
    window.addEventListener('DOMContentLoaded', function() {
        var portalLink = "https://github.com/focuslinkstech";
        $('#version').html('System Info Plugin by: <a href="' + portalLink + '">Focuslinks Tech</a>');
    });
</script>

{include file="sections/footer.tpl"}
