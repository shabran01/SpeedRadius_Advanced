{include file="sections/header.tpl"}

<style>
    .gw-card { background:#fff; border-radius:14px; padding:20px; margin-bottom:16px; box-shadow:0 2px 12px rgba(0,0,0,0.06); border:1px solid #e2e8f0; }
    .stats-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:12px; margin-bottom:16px; }
    .stat { background:#fff; border-radius:10px; padding:14px; text-align:center; box-shadow:0 2px 8px rgba(0,0,0,0.05); border-left:3px solid #e2e8f0; }
    .stat .big { font-size:22px; font-weight:800; color:#1e293b; line-height:1.2; }
    .stat .lbl { font-size:10px; color:#94a3b8; text-transform:uppercase; margin-top:3px; }
    .stat.grn { border-left-color:#25d366; }
    .stat.red { border-left-color:#ef4444; }
    .stat.blu { border-left-color:#4f46e5; }
    .stat.org { border-left-color:#f59e0b; }
    .btn { padding:8px 14px; border-radius:8px; font-weight:600; font-size:12px; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:5px; text-decoration:none; transition:all .2s; }
    .btn-g { background:#25d366; color:#fff; } .btn-g:hover { background:#128C7E; color:#fff; }
    .btn-r { background:#ef4444; color:#fff; } .btn-r:hover { background:#dc2626; color:#fff; }
    .btn-b { background:#4f46e5; color:#fff; } .btn-b:hover { background:#4338ca; color:#fff; }
    .btn-y { background:#f59e0b; color:#fff; } .btn-y:hover { background:#d97706; color:#fff; }
    .btn-gr { background:#6b7280; color:#fff; } .btn-gr:hover { background:#4b5563; color:#fff; }
    .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
    .badge-WORKING  { background:#d1fae5; color:#065f46; }
    .badge-SCAN_QR_CODE { background:#fef3c7; color:#92400e; }
    .badge-STARTING { background:#dbeafe; color:#1e40af; }
    .badge-STOPPED  { background:#f1f5f9; color:#64748b; }
    .badge-FAILED   { background:#fee2e2; color:#991b1b; }
    .badge-NOT_CREATED { background:#f1f5f9; color:#64748b; }
    .badge-CHECKING { background:#dbeafe; color:#1e40af; }
    pre { background:#1e293b; color:#e2e8f0; padding:14px; border-radius:8px; font-size:11px; overflow-x:auto; }
</style>

<div style="max-width:920px;margin:0 auto;">

{* ============ CONFIG PAGE ============ *}
{if $menu == 'config'}
<div class="gw-card">
    <h3 style="margin:0 0 16px;"><i class="fa fa-cog"></i> GoWAHA Configuration</h3>
    <form method="post" class="form-horizontal">
        <div class="form-group">
            <label class="col-md-3 control-label">WAHA API URL</label>
            <div class="col-md-7">
                <input type="text" name="gowaha_url" class="form-control" value="{$_c['gowaha_url']}" placeholder="http://84.46.244.95:3000">
                <span class="help-block">Base URL only — no trailing slash, no /api</span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">API Key</label>
            <div class="col-md-7">
                <input type="password" name="gowaha_key" class="form-control" value="{$_c['gowaha_key']}" placeholder="WAHA_API_KEY from docker env">
                <span class="help-block">Check WAHA container logs for auto-generated key, or set <code>WAHA_API_KEY</code> env var</span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">Default Session</label>
            <div class="col-md-7">
                <input type="text" name="gowaha_default_session" class="form-control" value="{$_c['gowaha_default_session']}" placeholder="default">
                <span class="help-block">Session used for system-wide WhatsApp notifications (payment alerts, OTP, etc.)</span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">My Sessions</label>
            <div class="col-md-7">
                <input type="text" name="gowaha_my_sessions" class="form-control" value="{$_c['gowaha_my_sessions']}" placeholder="e.g. default,support,sales">
                <span class="help-block">Comma-separated session names that belong to THIS system. Leave empty to show ALL sessions on the WAHA server.</span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">External API Key</label>
            <div class="col-md-7">
                <input type="text" name="gowaha_api_key" class="form-control" value="{$_c['gowaha_api_key']}" placeholder="Generate a random key for external access">
                <span class="help-block">Allows external systems to send WhatsApp messages via API (see usage below)</span>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-offset-3 col-md-7">
                <button type="submit" class="btn btn-g"><i class="fa fa-save"></i> Save Config</button>
                <a href="{$_url}plugin/gowaha" class="btn btn-gr" style="margin-left:6px;"><i class="fa fa-arrow-left"></i> Back</a>
            </div>
        </div>
    </form>
</div>

<div class="gw-card">
    <h4 style="margin:0 0 12px;"><i class="fa fa-server"></i> Docker Setup on VPS</h4>
    <pre>docker run -d \
  --name waha \
  --restart unless-stopped \
  -p 3000:3000 \
  -v waha-sessions:/app/.sessions \
  -e WAHA_API_KEY=your-secret-key \
  devlikeapro/waha</pre>
    <p style="font-size:11px;color:#94a3b8;margin:8px 0 0;">
        <i class="fa fa-info-circle"></i>
        After container starts, visit <a href="{$_c['gowaha_url']}/dashboard" target="_blank">{$_c['gowaha_url']}/dashboard</a>
        (default login: <code>admin/admin</code>)
    </p>
</div>

<div class="gw-card">
    <h4 style="margin:0 0 12px;"><i class="fa fa-code"></i> External API Usage</h4>
    <p style="font-size:12px;margin-bottom:8px;">Send WhatsApp messages from any external system — your website, cron job, another app.</p>
    <pre style="font-size:11px;">GET {$_detected_url}/?_route=plugin/gowaha_api&key=<b>YOUR_API_KEY</b>&to=254712345678&message=Hello

POST {$_detected_url}/?_route=plugin/gowaha_api
  key=YOUR_API_KEY&to=254712345678&message=Hello+World

Optional: &session=support  (uses default session if omitted)</pre>
    <p style="font-size:11px;color:#94a3b8;margin:8px 0 0;">
        <i class="fa fa-check-circle"></i> Response: {literal}<code>{"code":"SUCCESS","message":"Sent"}</code>{/literal}
    </p>
</div>
{/if}

{* ============ MAIN DASHBOARD ============ *}
{if !$menu}

{* ── Session Selector ── *}
<div class="gw-card" style="padding:12px 18px;">
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <span style="font-weight:600;font-size:13px;">Active Session:</span>
        <select onchange="if(this.value) window.location.href='{$_url}plugin/gowaha&session='+encodeURIComponent(this.value)" style="padding:6px 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;min-width:160px;">
            {foreach from=$allSessions item=s}
                <option value="{$s.name}" {if $s.name == $sessionName}selected{/if}>
                    {$s.name} — {($s.status)|upper}
                    {if $s.me.id} (+{($s.me.id)|replace:'@c.us':''}){/if}
                </option>
            {/foreach}
            {if empty($allSessions)}
                <option value="{$sessionName}">{$sessionName} (offline)</option>
            {/if}
        </select>
        <span style="margin-left:auto;font-size:11px;color:#94a3b8;" id="lastChecked"></span>
    </div>
</div>

{* ── Create New Session ── *}
<div class="gw-card" style="padding:12px 18px;background:#f8fafc;">
    <form action="{$_url}plugin/gowaha_create_session" method="get" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        <span style="font-weight:600;font-size:13px;"><i class="fa fa-plus-circle" style="color:#25d366;"></i> New Session:</span>
        <input type="text" name="name" class="form-control" placeholder="Session name (e.g. support, sales)" style="max-width:200px;" required pattern="[a-zA-Z0-9_-]+">
        <button type="submit" class="btn btn-g"><i class="fa fa-plus"></i> Create</button>
    </form>
</div>

<div class="stats-row">
    <div class="stat {if $connected}grn{elseif $sessionStatus == 'SCAN_QR_CODE'}org{elseif $sessionStatus == 'STARTING'}blu{else}red{/if}">
        <div class="big">
            {if $connected}🟢{elseif $sessionStatus == 'SCAN_QR_CODE'}🟡{elseif $sessionStatus == 'STARTING'}🔵{else}🔴{/if}
        </div>
        <div class="lbl">WhatsApp</div>
    </div>
    {if $me}
    <div class="stat grn">
        <div class="big" style="font-size:14px;">+{$me}</div>
        <div class="lbl">Connected Number</div>
    </div>
    {/if}
    <div class="stat blu">
        <div class="big">{$todaySent}</div>
        <div class="lbl">Sent Today</div>
    </div>
    <div class="stat grn">
        <div class="big">{$totalSent}</div>
        <div class="lbl">Total Sent</div>
    </div>
    <div class="stat red">
        <div class="big">{$totalFailed}</div>
        <div class="lbl">Failed</div>
    </div>
</div>

<div class="gw-card" style="padding:14px 18px;">
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <span style="font-weight:600;font-size:13px;">Session:</span>
        <span class="badge badge-{$sessionStatus}">{$sessionStatus}</span>
        {if $me}<span style="font-size:12px;color:#64748b;">· +{$me}</span>{/if}
        <span style="margin-left:auto;font-size:11px;color:#94a3b8;" id="lastChecked"></span>
    </div>
</div>

<div class="gw-card" style="padding:14px 18px;">
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{$_url}plugin/gowaha_config" class="btn btn-b"><i class="fa fa-cog"></i> Config</a>
        <a href="{$_url}plugin/gowaha_logs" class="btn btn-b"><i class="fa fa-list"></i> Logs</a>

        {if $sessionStatus == 'NOT_CREATED'}
            <a href="{$_url}plugin/gowaha_create_session&name={$sessionName}" class="btn btn-g" onclick="return confirm('Create WhatsApp session \'{$sessionName}\'?')"><i class="fa fa-plus"></i> Create Session</a>
        {elseif $sessionStatus == 'STOPPED'}
            <a href="{$_url}plugin/gowaha_start_session&session={$sessionName}" class="btn btn-g"><i class="fa fa-play"></i> Start</a>
            <a href="{$_url}plugin/gowaha_delete_session&session={$sessionName}" class="btn btn-r" onclick="return confirm('Delete session \'{$sessionName}\' completely?')"><i class="fa fa-trash"></i> Delete</a>
        {elseif $sessionStatus == 'WORKING'}
            <a href="{$_url}plugin/gowaha_restart_session&session={$sessionName}" class="btn btn-y" onclick="return confirm('Restart \'{$sessionName}\'?')"><i class="fa fa-refresh"></i> Restart</a>
            <a href="{$_url}plugin/gowaha_stop_session&session={$sessionName}" class="btn btn-gr" onclick="return confirm('Stop \'{$sessionName}\'?')"><i class="fa fa-stop"></i> Stop</a>
            <a href="{$_url}plugin/gowaha_logout&session={$sessionName}" class="btn btn-r" onclick="return confirm('Logout \'{$sessionName}\'? You must scan QR again.')"><i class="fa fa-sign-out"></i> Logout</a>
        {else}
            <a href="{$_url}plugin/gowaha_restart_session&session={$sessionName}" class="btn btn-y"><i class="fa fa-refresh"></i> Restart</a>
            <a href="{$_url}plugin/gowaha_logout&session={$sessionName}" class="btn btn-r" onclick="return confirm('Logout \'{$sessionName}\' and clear auth?')"><i class="fa fa-sign-out"></i> Logout</a>
        {/if}
        <button class="btn btn-gr" onclick="checkStatus()"><i class="fa fa-heartbeat"></i> Refresh</button>
    </div>
</div>

{if $sessionStatus == 'SCAN_QR_CODE'}
<div class="gw-card" style="text-align:center;" id="qrSection">
    <h4 style="margin:0 0 6px;"><i class="fa fa-qrcode"></i> Scan QR Code</h4>
    <p style="font-size:12px;color:#94a3b8;margin:0 0 12px;">QR expires every 20s — auto-refreshing below</p>
    <div id="qrContainer">
        {if $qrCode}
            <img src="{$qrCode}" id="qrImg" style="max-width:260px;margin:0 auto 10px;display:block;border-radius:10px;border:3px solid #25d366;padding:5px;">
        {else}
            <div style="padding:30px;color:#94a3b8;"><i class="fa fa-spinner fa-spin fa-2x"></i><br><br>Loading QR...</div>
        {/if}
    </div>
    <div style="background:#f1f5f9;border-radius:8px;height:5px;margin:8px auto;max-width:260px;overflow:hidden;">
        <div id="qrFill" style="background:#25d366;height:100%;width:100%;transition:width 1s linear;"></div>
    </div>
    <p style="font-size:11px;color:#94a3b8;margin:4px 0 16px;">Auto-refreshing in <span id="qrSec">20</span>s</p>
    <p style="font-size:12px;color:#128C7E;">WhatsApp → Settings → Linked Devices → Scan QR</p>
    <hr style="margin:16px 0;">
    <h5 style="margin:0 0 10px;">Or use Pairing Code (no QR scan needed)</h5>
    <form action="{$_url}plugin/gowaha_pair_code&session={$sessionName}" method="post" style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
        <input type="text" name="phone" class="form-control" placeholder="254712345678" style="max-width:220px;">
        <button type="submit" class="btn btn-g"><i class="fa fa-link"></i> Get Code</button>
    </form>
    <p style="font-size:11px;color:#94a3b8;margin-top:6px;">WhatsApp → Linked Devices → Link with Phone Number → Enter 8-digit code</p>
</div>
{/if}

{if $sessionStatus == 'NOT_CREATED'}
<div class="gw-card" style="text-align:center;padding:40px;">
    <i class="fa fa-whatsapp" style="font-size:48px;color:#25d366;"></i>
    <h4 style="margin:12px 0 6px;">No Session '{$sessionName}' Yet</h4>
    <p style="color:#94a3b8;margin-bottom:16px;">WAHA Docker must be running first. Then create this session.</p>
    <a href="{$_url}plugin/gowaha_create_session&name={$sessionName}" class="btn btn-g" onclick="return confirm('Create session \'{$sessionName}\'?')">
        <i class="fa fa-plus"></i> Create '{$sessionName}'
    </a>
    <a href="{$_url}plugin/gowaha_config" class="btn btn-b" style="margin-left:8px;"><i class="fa fa-cog"></i> Config</a>
</div>
{/if}

{if $sessionStatus == 'CHECKING' || $sessionStatus == 'STARTING'}
<div class="gw-card" style="text-align:center;padding:30px;">
    <i class="fa fa-spinner fa-spin" style="font-size:40px;color:#4f46e5;"></i>
    <h4 style="margin:12px 0 6px;">Session '{$sessionName}' Initializing...</h4>
    <p style="color:#94a3b8;">WAHA is starting the session — this may take 30-60 seconds. The QR will appear automatically.</p>
    <p style="color:#94a3b8;font-size:11px;">Auto-checking every 3 seconds...</p>
</div>
{/if}

{if $sessionStatus == 'STOPPED'}
<div class="gw-card" style="text-align:center;padding:30px;">
    <i class="fa fa-pause-circle" style="font-size:40px;color:#f59e0b;"></i>
    <h4 style="margin:12px 0 6px;">Session '{$sessionName}' Stopped</h4>
    <p style="color:#94a3b8;">The session exists but is not running. Click Start to launch it and scan the QR code.</p>
    <a href="{$_url}plugin/gowaha_start_session&session={$sessionName}" class="btn btn-g" style="margin-top:8px;">
        <i class="fa fa-play"></i> Start '{$sessionName}'
    </a>
</div>
{/if}

{if $sessionStatus == 'FAILED'}
<div class="gw-card" style="text-align:center;padding:30px;">
    <i class="fa fa-exclamation-triangle" style="font-size:40px;color:#ef4444;"></i>
    <h4 style="margin:12px 0 6px;color:#ef4444;">Session '{$sessionName}' Failed</h4>
    <p style="color:#94a3b8;">Try Restart. If it keeps failing — Logout and scan QR again.</p>
</div>
{/if}

{if $connected}
<div class="gw-card">
    <h4 style="margin:0 0 12px;"><i class="fa fa-paper-plane"></i> Send Test Message</h4>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <input type="text" id="qsPhone" class="form-control" placeholder="Phone: 07xx or 2547xx" style="flex:1;min-width:140px;">
        <input type="text" id="qsMsg" class="form-control" placeholder="Message..." style="flex:2;min-width:200px;">
        <button class="btn btn-g" onclick="quickSend()"><i class="fa fa-send"></i> Send</button>
    </div>
    <div id="qsResult" style="margin-top:8px;font-size:12px;"></div>
</div>
{/if}

{/if}
</div>

<script>
var ajaxUrl = "{$_url}";
var currentStatus = "{$sessionStatus}";
var isConnected = {if $connected}true{else}false{/if};
var autoPoll = {if isset($autoPoll)}true{else}false{/if};
var sessionName = "{$sessionName}";

{literal}
function quickSend() {
    var p = document.getElementById('qsPhone').value.trim();
    var m = document.getElementById('qsMsg').value.trim();
    if (!p || !m) return alert('Phone and message required');
    var r = document.getElementById('qsResult');
    r.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
    fetch(ajaxUrl + 'plugin/gowaha_send&session=' + encodeURIComponent(sessionName) + '&to=' + encodeURIComponent(p) + '&message=' + encodeURIComponent(m))
        .then(function(res){ return res.json(); })
        .then(function(d){
            r.innerHTML = d.code === 'SUCCESS'
                ? '<span style="color:#25d366;"><i class="fa fa-check"></i> Sent!</span>'
                : '<span style="color:#ef4444;"><i class="fa fa-times"></i> ' + (d.message||'Failed') + '</span>';
        }).catch(function(){ r.innerHTML = '<span style="color:#ef4444;">Request failed</span>'; });
}

function checkStatus() {
    var el = document.getElementById('lastChecked');
    if (el) el.textContent = 'Polling...';
    fetch(ajaxUrl + 'plugin/gowaha_refresh_qr&session=' + encodeURIComponent(sessionName) + '&status=' + currentStatus)
        .then(function(r){ return r.json(); })
        .then(function(d){
            // Connected! Auto-reload to show working dashboard
            if (d.connected || d.status === 'WORKING') { location.reload(); return; }
            // Status changed — reload to reflect new state
            if (d.status !== currentStatus) { location.reload(); return; }
            // Still scanning — update QR image
            if (d.qr) {
                var img = document.getElementById('qrImg');
                if (img) { img.src = d.qr; }
                else {
                    var c = document.getElementById('qrContainer');
                    if (c) c.innerHTML = '<img src="'+d.qr+'" id="qrImg" style="max-width:260px;margin:0 auto 10px;display:block;border-radius:10px;border:3px solid #25d366;padding:5px;">';
                }
            }
            if (el) el.textContent = 'Checked: ' + new Date().toLocaleTimeString();
        }).catch(function(){
            if (el) el.textContent = 'API unreachable — retrying...';
        });
}

// QR countdown bar — refreshes QR image every 20s
var qrSec = 20;
var countdownTimer = null;
function startCountdown() {
    var fill = document.getElementById('qrFill');
    var secEl = document.getElementById('qrSec');
    if (!fill || !secEl) return;
    if (countdownTimer) clearInterval(countdownTimer);
    qrSec = 20;
    countdownTimer = setInterval(function(){
        qrSec--;
        if (secEl) secEl.textContent = qrSec;
        if (fill) fill.style.width = (qrSec / 20 * 100) + '%';
        if (qrSec <= 0) {
            qrSec = 20;
            checkStatus(); // refresh QR image
        }
    }, 1000);
}

// Status polling — only runs for transitional states (NOT when connected)
var scanInterval = null;
function startPolling() {
    if (scanInterval) clearInterval(scanInterval);
    scanInterval = setInterval(checkStatus, 3000);
}

// Only poll on dashboard, not config — and only for transitional states
if (autoPoll && !isConnected && currentStatus && currentStatus !== 'WORKING') {
    checkStatus();
    startCountdown();
    startPolling();
}
{/literal}
</script>

{include file="sections/footer.tpl"}
