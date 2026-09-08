{include file="sections/header.tpl"}

{literal}
<style>
.system-info-page { color: #26364a; }
.system-info-hero { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; margin: 8px 0 24px; }
.system-info-hero h1 { margin: 0 0 6px; font-size: 26px; font-weight: 700; color: #172b4d; }
.system-info-hero p { margin: 0; color: #718096; font-size: 13px; }
.system-info-kicker { margin: 0 0 8px; color: #168aad; font-size: 11px; font-weight: 700; letter-spacing: 1.4px; text-transform: uppercase; }
.system-info-panel { margin-bottom: 20px; border: 1px solid #e5eaf0; border-radius: 8px; background: #fff; box-shadow: 0 4px 16px rgba(30, 55, 90, .06); overflow: hidden; }
.system-info-panel-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 20px; border-bottom: 1px solid #edf1f5; background: #fbfcfe; }
.system-info-panel-head h2 { margin: 0; color: #20344f; font-size: 15px; font-weight: 700; }
.system-info-panel-head p { margin: 3px 0 0; color: #8795a8; font-size: 12px; }
.system-info-panel-body { padding: 20px; }
.system-info-metric { position: relative; min-height: 148px; padding: 20px; border: 1px solid #e6ebf1; border-radius: 8px; background: #fff; }
.system-info-metric:before { position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: #168aad; content: ''; }
.system-info-metric.memory:before { background: #e09f3e; }
.system-info-metric.storage:before { background: #3a86a8; }
.system-info-metric-label { margin-bottom: 10px; color: #718096; font-size: 12px; font-weight: 700; letter-spacing: .7px; text-transform: uppercase; }
.system-info-metric-value { margin: 0; color: #172b4d; font-size: 26px; font-weight: 700; line-height: 1.15; }
.system-info-metric-detail { min-height: 18px; margin: 8px 0 16px; color: #8492a6; font-size: 12px; }
.system-info-progress { height: 6px; margin: 0; border-radius: 4px; background: #edf1f5; box-shadow: none; }
.system-info-progress .progress-bar { border-radius: 4px; background: #168aad; box-shadow: none; }
.memory .system-info-progress .progress-bar { background: #e09f3e; }
.storage .system-info-progress .progress-bar { background: #3a86a8; }
.system-info-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); border-top: 1px solid #edf1f5; }
.system-info-detail { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding: 13px 20px; border-right: 1px solid #edf1f5; border-bottom: 1px solid #edf1f5; }
.system-info-detail:nth-child(even) { border-right: 0; }
.system-info-detail dt { color: #8795a8; font-size: 12px; font-weight: 600; }
.system-info-detail dd { margin: 0; color: #34495e; font-size: 12px; font-weight: 600; text-align: right; word-break: break-word; }
.system-info-service-list { margin: 0; }
.system-info-service { display: flex; align-items: center; justify-content: space-between; min-height: 48px; padding: 10px 0; border-bottom: 1px solid #edf1f5; }
.system-info-service:last-child { border-bottom: 0; padding-bottom: 0; }
.system-info-service:first-child { padding-top: 0; }
.system-info-service-name { color: #34495e; font-size: 13px; font-weight: 600; }
.system-info-service-status small { display: inline-block; padding: 4px 9px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: capitalize; }
.system-info-service-status .label { float: none; }
.system-info-reload { white-space: nowrap; }
.system-info-result { margin: 0 0 20px; padding: 14px 18px; border: 1px solid #b8dfc8; border-left: 4px solid #2a9d5b; border-radius: 6px; background: #f1fbf4; color: #24613b; font-size: 13px; }
.system-info-result.failed { border-color: #f2c0c0; border-left-color: #d9534f; background: #fff5f5; color: #8b3030; }
.system-info-result p { margin: 0; }
@media (max-width: 767px) {
    .system-info-hero { display: block; }
    .system-info-hero .system-info-reload { margin-top: 16px; }
    .system-info-detail-grid { grid-template-columns: 1fr; }
    .system-info-detail { border-right: 0; }
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
            <div class="system-info-metric">
                <div class="system-info-metric-label"><span class="glyphicon glyphicon-dashboard"></span> CPU Load</div>
                <p class="system-info-metric-value">{$systemInfo['CPU Usage']|default:'Unknown'}</p>
                <p class="system-info-metric-detail">{$systemInfo['CPU Cores']|default:'Unknown'} logical cores available</p>
                <div class="progress system-info-progress"><div class="progress-bar" role="progressbar" style="width: {$systemInfo['CPU Usage']|default:'0%'};"></div></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="system-info-metric memory">
                <div class="system-info-metric-label"><span class="glyphicon glyphicon-tasks"></span> Memory</div>
                <p class="system-info-metric-value">{$memory_usage.used|default:0} MB</p>
                <p class="system-info-metric-detail">of {$memory_usage.total|default:0} MB used - {$memory_usage.free|default:0} MB available</p>
                <div class="progress system-info-progress"><div class="progress-bar" role="progressbar" style="width: {$memory_usage.used_percentage|default:0}%;"></div></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="system-info-metric storage">
                <div class="system-info-metric-label"><span class="glyphicon glyphicon-hdd"></span> Storage</div>
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
