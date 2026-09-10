{include file="sections/header.tpl"}

<style>
{literal}
/* ── Layout ── */
.lq-wrap { background:#f8f9fa; border-radius:12px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08); }
.lq-header { background:linear-gradient(135deg,#0d7377 0%,#14a085 100%); padding:14px 16px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
.lq-stats-row { display:flex; gap:12px; padding:12px 16px; background:#fff; border-bottom:1px solid #e8e8e8; flex-wrap:wrap; }
.lq-stat { flex:1; min-width:120px; text-align:center; padding:10px; border-radius:8px; background:#f0fdf4; }
.lq-stat.total   { background:#f0f4ff; }
.lq-stat.active  { background:#f0fdf4; }
.lq-stat.tx      { background:#fff7ed; }
.lq-stat.rx      { background:#fdf2f8; }
.lq-stat .val { font-size:20px; font-weight:700; color:#333; }
.lq-stat .lbl { font-size:11px; color:#888; text-transform:uppercase; letter-spacing:0.5px; }

/* ── Table ── */
.lq-table-wrap { padding:0; overflow-x:auto; }
.lq-table { width:100%; border-collapse:collapse; font-size:13px; }
.lq-table thead th { background:#1a2a3a; color:#fff; padding:10px 12px; font-weight:600; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap; position:sticky; top:0; z-index:2; }
.lq-table tbody td { padding:9px 12px; border-bottom:1px solid #eee; white-space:nowrap; }
.lq-table tbody tr:hover { background:#f0f9ff; }
.lq-table tbody tr.top-talker { background:#fff5f5; }
.lq-table tbody tr.top-talker td:first-child { border-left:3px solid #e74c3c; }
.lq-table tbody tr.disabled { opacity:0.45; }
.bar-wrap { width:100px; height:8px; background:#e8e8e8; border-radius:4px; overflow:hidden; display:inline-block; vertical-align:middle; }
.bar-fill { height:100%; border-radius:4px; transition:width 0.3s; }
.bar-fill.tx { background:linear-gradient(90deg,#f97316,#fb923c); }
.bar-fill.rx { background:linear-gradient(90deg,#ec4899,#f472b6); }

/* ── Controls ── */
.lq-controls { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.lq-controls select { width:220px; }
.lq-refresh-indicator { font-size:11px; color:#a0d0c0; display:flex; align-items:center; gap:5px; }
.lq-refresh-indicator .pulse { width:8px; height:8px; background:#0f0; border-radius:50%; animation:pulse 1s infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.3} }
.lq-error { background:#fff5f5; border:1px solid #fecaca; border-radius:8px; padding:16px; margin:16px; color:#b91c1c; display:none; }

/* ── Mobile ── */
@media (max-width:767px) {
    .lq-header { padding:10px 12px; }
    .lq-header h4 { font-size:14px !important; }
    .lq-controls select { width:100%; }
    .lq-stats-row { gap:6px; padding:8px; }
    .lq-stat { min-width:70px; padding:6px; }
    .lq-stat .val { font-size:16px; }
    .lq-stat .lbl { font-size:9px; }
    .lq-table thead { display:none; }
    .lq-table tbody td { display:block; padding:5px 8px; font-size:12px; border:none; }
    .lq-table tbody td::before { content:attr(data-label); display:block; font-weight:700; font-size:9px; color:#0d7377; text-transform:uppercase; margin-bottom:2px; }
    .lq-table tbody tr { display:block; margin-bottom:8px; border:1px solid #e0e0e0; border-radius:8px; padding:6px; background:#fff; }
    .lq-table tbody tr.top-talker td:first-child { border-left:none; border-top:3px solid #e74c3c; }
    .bar-wrap { width:60px; }
}
{/literal}
</style>

<section class="content">
<div class="container-fluid">

<div class="lq-wrap">

    <!-- Header -->
    <div class="lq-header">
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff">
                <i class="ion ion-speedometer"></i>
            </div>
            <div>
                <h4 style="color:#fff;margin:0;font-weight:600">Live Queue Monitor</h4>
                <div style="color:rgba(255,255,255,0.7);font-size:11px;margin-top:2px" id="routerBadge">
                    Select a router to begin
                </div>
            </div>
        </div>
        <div class="lq-controls">
            <select class="form-control input-sm" id="routerSelect" onchange="changeRouter()">
                <option value="">-- Select Router --</option>
                {foreach $routers as $r}
                    <option value="{$r.id}">{$r.name} ({$r.ip_address})</option>
                {/foreach}
            </select>
            <button class="btn btn-sm {if empty($smarty.session.lq_paused)}btn-warning{else}btn-success{/if}" id="pauseBtn" onclick="togglePause()" title="Pause/Resume auto-refresh">
                <i class="fa fa-{if empty($smarty.session.lq_paused)}pause{else}play{/if}"></i>
            </button>
            <span class="lq-refresh-indicator" id="refreshIndicator" style="display:none">
                <span class="pulse"></span> Live
            </span>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="lq-stats-row">
        <div class="lq-stat total"><div class="val" id="statTotal">-</div><div class="lbl">Total Queues</div></div>
        <div class="lq-stat active"><div class="val" id="statActive">-</div><div class="lbl">Active Queues</div></div>
        <div class="lq-stat tx"><div class="val" id="statTx">-</div><div class="lbl">Total Upload</div></div>
        <div class="lq-stat rx"><div class="val" id="statRx">-</div><div class="lbl">Total Download</div></div>
    </div>

    <!-- Error -->
    <div class="lq-error" id="lqError"></div>

    <!-- Table -->
    <div class="lq-table-wrap">
        <table class="lq-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name / Target</th>
                    <th>Type</th>
                    <th>Upload Rate</th>
                    <th>Upload Bar</th>
                    <th>Download Rate</th>
                    <th>Download Bar</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody id="queueBody">
                <tr><td colspan="8" style="text-align:center;padding:40px;color:#999">
                    <i class="fa fa-arrow-up" style="font-size:28px;opacity:0.3;display:block;margin-bottom:8px"></i>
                    Select a router above to start monitoring
                </td></tr>
            </tbody>
        </table>
    </div>

</div>

</div>
</section>

{literal}
<script>
var currentRouter = '';
var refreshTimer = null;
var isPaused = false;
var maxRate = 1; // Track max rate for bar scaling

function changeRouter() {
    currentRouter = $('#routerSelect').val();
    if (!currentRouter) {
        $('#routerBadge').text('Select a router to begin');
        $('#queueBody').html('<tr><td colspan="8" style="text-align:center;padding:40px;color:#999">Select a router above</td></tr>');
        $('#refreshIndicator').hide();
        clearInterval(refreshTimer);
        return;
    }
    var txt = $('#routerSelect option:selected').text();
    $('#routerBadge').text(txt);
    $('#refreshIndicator').show();
    fetchData();
    if (!isPaused) startAutoRefresh();
}

function fetchData() {
    if (!currentRouter) return;
    $.get('?_route=plugin/live_queue_monitor/api&router_id=' + currentRouter)
    .done(function(r) {
        if (!r.success) { showError(r.error); return; }
        hideError();
        renderTable(r);
    })
    .fail(function() { showError('Failed to connect to router'); });
}

function renderTable(r) {
    // Stats
    $('#statTotal').text(r.total_count);
    $('#statActive').text(r.active_count);
    $('#statTx').text(r.total_tx_str);
    $('#statRx').text(r.total_rx_str);

    // Find max rate for bar scaling
    maxRate = 1;
    r.data.forEach(function(q) { maxRate = Math.max(maxRate, q.tx_rate, q.rx_rate); });

    var html = '';
    if (r.data.length === 0) {
        html = '<tr><td colspan="8" style="text-align:center;padding:20px;color:#999">No queues found on this router</td></tr>';
    } else {
        r.data.forEach(function(q, i) {
            var isTop = (q.tx_rate + q.rx_rate) > (maxRate * 0.7) && (q.tx_rate + q.rx_rate) > 0;
            var isDisabled = q.disabled === 'true';
            var rowClass = (isTop && !isDisabled ? 'top-talker' : '') + (isDisabled ? ' disabled' : '');
            var nameDisplay = q.name || 'N/A';
            if (q.target && q.target !== '') nameDisplay += ' &rarr; <code>' + q.target + '</code>';
            if (q.type === 'tree' && q.parent) nameDisplay += ' <small class="text-muted">(parent: ' + q.parent + ')</small>';

            var txPct = maxRate > 0 ? Math.round((q.tx_rate / maxRate) * 100) : 0;
            var rxPct = maxRate > 0 ? Math.round((q.rx_rate / maxRate) * 100) : 0;

            html += '<tr class="' + rowClass + '">' +
                '<td data-label="#">' + (i + 1) + '</td>' +
                '<td data-label="Name">' + nameDisplay + '</td>' +
                '<td data-label="Type"><span class="label ' + (q.type === 'tree' ? 'label-info' : 'label-primary') + '">' + q.type + '</span></td>' +
                '<td data-label="Upload" style="font-weight:600;color:#ea580c">' + q.tx_rate_str + '</td>' +
                '<td data-label="TX Bar"><div class="bar-wrap"><div class="bar-fill tx" style="width:' + txPct + '%"></div></div></td>' +
                '<td data-label="Download" style="font-weight:600;color:#db2777">' + q.rx_rate_str + '</td>' +
                '<td data-label="RX Bar"><div class="bar-wrap"><div class="bar-fill rx" style="width:' + rxPct + '%"></div></div></td>' +
                '<td data-label="Comment" style="font-size:11px;color:#888">' + (q.comment || '-') + '</td>' +
            '</tr>';
        });
    }
    $('#queueBody').html(html);
}

function startAutoRefresh() {
    clearInterval(refreshTimer);
    refreshTimer = setInterval(fetchData, 5000);
}

function togglePause() {
    isPaused = !isPaused;
    var btn = $('#pauseBtn');
    if (isPaused) {
        clearInterval(refreshTimer);
        btn.removeClass('btn-warning').addClass('btn-success');
        btn.html('<i class="fa fa-play"></i>');
        $('#refreshIndicator').hide();
    } else {
        fetchData();
        startAutoRefresh();
        btn.removeClass('btn-success').addClass('btn-warning');
        btn.html('<i class="fa fa-pause"></i>');
        $('#refreshIndicator').show();
    }
}

function showError(msg) {
    $('#lqError').text(msg).show();
}

function hideError() {
    $('#lqError').hide();
}
</script>
{/literal}

{include file="sections/footer.tpl"}
