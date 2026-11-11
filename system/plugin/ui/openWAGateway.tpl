{include file="sections/header.tpl"}

<style>
{literal}
    .gwa-wrap { max-width:1040px; margin:0 auto; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif; }
    .gwa-wrap h1, .gwa-wrap h2, .gwa-wrap h3,
    .gwa-wrap h4, .gwa-wrap h5, .gwa-wrap h6 { min-height:0; }
    .gwa-btn { min-height:44px; }
    .gwa-panel, .gwa-field, .gwa-stat, .gwa-note { min-width:0; overflow-wrap:break-word; }
    .gwa-hero { position:relative; overflow:hidden; border-radius:18px; padding:24px 26px; margin-bottom:18px; color:#fff;
        background:linear-gradient(120deg,#0b1f33 0%,#123c52 55%,#0e7a5f 130%); box-shadow:0 10px 30px rgba(10,30,50,.25); }
    .gwa-hero:after { content:""; position:absolute; right:-60px; top:-60px; width:220px; height:220px; border-radius:50%;
        background:radial-gradient(circle,rgba(37,211,102,.35),transparent 70%); }
    .gwa-hero h2 { margin:0 0 4px; font-size:22px; font-weight:800; letter-spacing:.3px; }
    .gwa-hero .sub { font-size:12.5px; color:#cfe6dd; margin:0; }
    .gwa-chip { display:inline-flex; align-items:center; gap:7px; padding:5px 12px; border-radius:999px; font-size:12px; font-weight:700;
        background:rgba(255,255,255,.12); }
    .gwa-dot { width:9px; height:9px; border-radius:50%; background:#f87171; }
    .gwa-dot.ok { background:#34d399; box-shadow:0 0 8px rgba(52,211,153,.9); animation:gwaPulse 1.8s infinite; }
    @keyframes gwaPulse { 0%{box-shadow:0 0 0 0 rgba(52,211,153,.7)} 70%{box-shadow:0 0 0 9px rgba(52,211,153,0)} 100%{box-shadow:0 0 0 0 rgba(52,211,153,0)} }
    .gwa-grid { display:grid; grid-template-columns:1.55fr 1fr; gap:18px; }
    .gwa-grid > * { min-width:0; }
    @media (max-width:860px){ .gwa-grid { grid-template-columns:1fr; gap:14px; } }
    .gwa-panel { background:#fff; border-radius:16px; border:1px solid #e8eef4; box-shadow:0 4px 18px rgba(20,40,60,.06); padding:20px; margin-bottom:18px; }
    .gwa-panel h3 { margin:0 0 14px; font-size:15px; font-weight:800; color:#0f2b3d; }
    .gwa-panel h3 i { color:#10b981; margin-right:6px; }
    .gwa-stat { display:flex; align-items:center; gap:13px; padding:13px 15px; border-radius:13px; background:#f6fafb; margin-bottom:10px; }
    .gwa-stat .ic { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0; }
    .gwa-stat .num { font-size:21px; font-weight:800; color:#0f2b3d; line-height:1; margin-left:auto; }
    .gwa-stat .t { font-size:12.5px; color:#6b7f8c; font-weight:600; }
    .ic-g { background:#e7f9ef; color:#059669; } .ic-b { background:#e7f0fb; color:#2563eb; }
    .ic-r { background:#fdeaea; color:#dc2626; } .ic-o { background:#fef3e2; color:#d97706; }
    .gwa-btn { display:flex; align-items:center; justify-content:center; gap:8px; width:100%; padding:11px 14px; border-radius:12px;
        font-size:13px; font-weight:700; border:none; cursor:pointer; text-decoration:none; margin-bottom:9px; transition:transform .12s,box-shadow .12s; }
    .gwa-btn:hover { transform:translateY(-1px); color:#fff; }
    .gwa-btn:last-child { margin-bottom:0; }
    .b-g { background:#25d366; color:#fff; box-shadow:0 6px 16px rgba(37,211,102,.3); } .b-g:hover{ background:#1fb657; }
    .b-dk { background:#0f2b3d; color:#fff; } .b-dk:hover{ background:#14384f; }
    .b-r { background:#ef4444; color:#fff; } .b-r:hover{ background:#dc2626; }
    .b-o { background:#fff; color:#334e63; border:1.5px solid #dce6ec; } .b-o:hover{ background:#f4f8fa; color:#334e63; }
    .gwa-qr { text-align:center; }
    .gwa-phone { position:relative; display:inline-block; padding:26px 14px 18px; border-radius:26px; background:#0f2b3d;
        box-shadow:inset 0 0 0 3px #1d445c, 0 14px 34px rgba(15,43,61,.3); margin:4px 0 14px; max-width:100%; }
    .gwa-phone:before { content:""; position:absolute; top:9px; left:50%; transform:translateX(-50%); width:52px; height:6px; border-radius:6px; background:#1d445c; }
    .gwa-phone img { display:block; width:100%; max-width:220px; height:auto; margin:0 auto; border-radius:10px; background:#fff; }
    .gwa-muted { color:#8ba0ae; font-size:12px; }
    .gwa-ok-hero { text-align:center; padding:10px 0 6px; }
    .gwa-ok-hero .big { width:74px; height:74px; margin:0 auto 14px; border-radius:50%; background:#e7f9ef; color:#059669;
        display:flex; align-items:center; justify-content:center; font-size:34px; box-shadow:0 0 0 9px #f0fbf5; }
    .gwa-note { font-size:12.5px; background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; padding:10px 13px; border-radius:12px; margin-bottom:16px; }
    .gwa-pair { margin-top:14px; padding:14px; border-radius:12px; background:#e7f9ef; border:1px solid #a7f3d0; text-align:center; }
    .gwa-pair-lbl { font-size:11.5px; font-weight:700; letter-spacing:.07em; text-transform:uppercase; color:#059669; }
    .gwa-pair-val { font-size:26px; font-weight:800; letter-spacing:.14em; color:#0f2b3d; margin:6px 0 4px; font-variant-numeric:tabular-nums; }
    .gwa-field { margin-bottom:12px; }
    .gwa-field label { display:block; font-size:12px; font-weight:700; color:#334e63; margin-bottom:5px; }
    .gwa-field .hint { font-size:11px; color:#8ba0ae; margin-top:4px; overflow-wrap:break-word; }
    .gwa-field input, .gwa-field select { width:100%; padding:9px 11px; border:1.5px solid #dce6ec; border-radius:10px; font-size:13px; }
    .gwa-row { display:flex; flex-wrap:wrap; gap:8px; }
    .gwa-row > * { flex:1 1 200px; min-width:0; }
    .gwa-code { background:#0f2b3d; color:#d7f5e8; padding:14px 16px; border-radius:12px; font-size:12px; overflow-x:auto; margin:0; max-width:100%; }
    @media (max-width:600px){
        .gwa-hero { padding:18px; border-radius:14px; }
        .gwa-hero h2 { font-size:19px; }
        .gwa-panel { padding:15px; border-radius:13px; }
        .gwa-phone { padding:20px 12px 14px; border-radius:22px; }
        .gwa-code { font-size:11px; padding:12px 13px; }
        .gwa-btn { font-size:12.5px; }
    }
{/literal}
</style>

<div class="gwa-wrap">

{if $menu == 'config'}

<div class="gwa-hero">
    <h2><i class="fa fa-sliders"></i> OpenWA Configuration</h2>
    <p class="sub">Point SpeedRadius at your self-hosted OpenWA gateway, then scan once to link the device.
        <span class="gwa-chip">plugin v{$gwPluginVersion}</span></p>
</div>

<div class="gwa-grid">
    <div class="gwa-panel">
        <h3><i class="fa fa-plug"></i> Server</h3>
        <form method="post" action="{$_url}plugin/openwa_config">
            <div class="gwa-field">
                <label>Base URL</label>
                <input type="text" name="openwa_url" value="{$gw_url}" placeholder="http://127.0.0.1:2785">
                <div class="hint">OpenWA's API and dashboard share one port. Do not add /api — the plugin does that.</div>
            </div>
            <div class="gwa-field">
                <label>API Key (admin)</label>
                <input type="password" name="openwa_api_key" value="{$gw_api_key}" placeholder="owa_k1_...">
                <div class="hint">Stored at <code>/opt/openwa/data/.api-key</code> inside the container.</div>
            </div>
            <div class="gwa-field">
                <label>Session Name</label>
                <input type="text" name="openwa_session_name" value="{$gw_session_name}" placeholder="speedradius">
                <div class="hint">Alphanumeric and hyphens, 3–50 characters. Created automatically on first use.</div>
            </div>
            <div class="gwa-field">
                <label>Linked Number</label>
                <input type="text" name="openwa_number" value="{$gw_number}" placeholder="254712345678">
                <div class="hint">Display only — the real number comes from the linked WhatsApp session.</div>
            </div>
            <div class="gwa-field">
                <label>Status</label>
                <select name="openwa_enabled">
                    <option value="no" {if !$gw_enabled}selected{/if}>Disabled</option>
                    <option value="yes" {if $gw_enabled}selected{/if}>Enabled — send system messages through OpenWA</option>
                </select>
            </div>
            <div class="gwa-field">
                <label>Public API Key</label>
                <input type="text" name="openwa_public_api_key" value="{$gw_public_key}" placeholder="Random secret for the public API">
                <div class="hint">Required by <code>plugin/openwa_api</code>. Leave empty to disable that endpoint.</div>
            </div>
            <button type="submit" name="save" value="1" class="gwa-btn b-g"><i class="fa fa-save"></i> Save Configuration</button>
            <a href="{$_url}plugin/openwa" class="gwa-btn b-o"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
        </form>
    </div>

    <div>
        <div class="gwa-panel">
            <h3><i class="fa fa-terminal"></i> Public API</h3>
            <pre class="gwa-code">GET {$_detected_url}/?_route=plugin/openwa_api
    &amp;key=YOUR_PUBLIC_KEY
    &amp;to=254712345678
    &amp;message=Hello</pre>
            <p class="gwa-muted" style="margin-top:10px;">Returns JSON <code>{literal}{"code":"SUCCESS"}{/literal}</code> on success.</p>
        </div>
        <div class="gwa-panel">
            <h3><i class="fa fa-info-circle"></i> How it works</h3>
            <p class="gwa-muted" style="line-height:1.7;">
                OpenWA runs in Docker on this server and listens on <code>127.0.0.1:2785</code>.
                SpeedRadius talks to it over that loopback port, so the API key never crosses the internet.
                Keeping the same session linked is safer than re-linking: WhatsApp may refuse a second
                companion link for an account.
            </p>
        </div>
    </div>
</div>

{else}

<div class="gwa-hero">
    <h2><i class="fa fa-whatsapp"></i> OpenWA Gateway</h2>
    <p class="sub">
        <span class="gwa-chip"><span class="gwa-dot {if $connected}ok{/if}"></span>
            {if $connected}Connected{elseif !$serverOk}Server unreachable{else}Not linked{/if}
        </span>
        <span class="gwa-chip">plugin v{$gwVersion}</span>
        {if $gwSession}<span class="gwa-chip">session: {$gwSession}</span>{/if}
    </p>
</div>

{if $error}
<div class="gwa-note"><i class="fa fa-exclamation-triangle"></i> {$error}</div>
{/if}

<div class="gwa-grid">
    <div class="gwa-panel">
        <h3><i class="fa fa-qrcode"></i> Device</h3>

        {if $connected}
        <div class="gwa-ok-hero">
            <div class="big"><i class="fa fa-check"></i></div>
            <h4 style="margin:0 0 6px;">WhatsApp is linked</h4>
            <p class="gwa-muted" style="margin:0;">
                {if $me}+{$me}{/if}
                {if $waStatus} &middot; status: <strong>{$waStatus}</strong>{/if}
            </p>
        </div>
        {else}
        <div class="gwa-qr">
            <div class="gwa-phone">
                <div id="qrContainer">
                    {if $qrCode}
                    <img id="qrImg" src="{$qrCode}" alt="WhatsApp QR">
                    {else}
                    <div style="width:220px;height:220px;display:flex;align-items:center;justify-content:center;background:#fff;border-radius:10px;">
                        <span class="gwa-muted"><i class="fa fa-spinner fa-spin"></i> waiting for QR</span>
                    </div>
                    {/if}
                </div>
            </div>
            <p class="gwa-muted">Open WhatsApp → Settings → Linked devices → Link a device, then scan.</p>
            <div id="qrError" style="display:none;color:#dc2626;font-size:12px;margin-top:8px;"></div>
            <p class="gwa-muted" id="lastChecked"></p>
        </div>
        {/if}

        {if $pairCode}
        <div class="gwa-pair" id="pairBox" data-expires="{$pairExpires}" data-ttl="{$pairTtl}">
            <div class="gwa-pair-lbl">Pairing code for +{$pairPhone}</div>
            <div class="gwa-pair-val" id="pairCodeValue">{$pairCode}</div>
            <div class="gwa-muted">Expires in <span id="pairLeft">{$pairTtl}</span>s</div>
            <div id="pairExpired" style="display:none;color:#dc2626;font-size:12px;margin-top:6px;">Code expired — request a new one.</div>
        </div>
        {/if}

        {if !$connected}
        <form action="{$_url}plugin/openwa_pair_code" method="post" class="gwa-row" style="margin-top:16px;">
            <input type="tel" name="phone" placeholder="254712345678" style="padding:9px 11px;border:1.5px solid #dce6ec;border-radius:10px;font-size:13px;" required>
            <button type="submit" class="gwa-btn b-o" style="margin:0;"><i class="fa fa-mobile"></i> Pairing code</button>
        </form>
        {/if}
    </div>

    <div>
        <div class="gwa-panel">
            <h3><i class="fa fa-chart-simple"></i> Today</h3>
            <div class="gwa-stat">
                <div class="ic ic-g"><i class="fa fa-paper-plane"></i></div>
                <div class="t">Delivered today</div>
                <div class="num">{$todaySent}</div>
            </div>
            <div class="gwa-stat">
                <div class="ic ic-b"><i class="fa fa-check-double"></i></div>
                <div class="t">Delivered total</div>
                <div class="num">{$totalSent}</div>
            </div>
            <div class="gwa-stat">
                <div class="ic ic-r"><i class="fa fa-triangle-exclamation"></i></div>
                <div class="t">Failed total</div>
                <div class="num">{$totalFailed}</div>
            </div>
        </div>

        <div class="gwa-panel">
            <h3><i class="fa fa-bolt"></i> Actions</h3>
            {if !$connected}
            <a href="{$_url}plugin/openwa_connect" class="gwa-btn b-g"><i class="fa fa-qrcode"></i> Generate QR Code</a>
            {/if}
            {if $connected}
            <a href="{$_url}plugin/openwa_logout" class="gwa-btn b-r" onclick="return confirm('Unlink this WhatsApp device? You will need to scan the QR again.')"><i class="fa fa-sign-out"></i> Unlink Device</a>
            {/if}
            <a href="{$_url}plugin/openwa_config" class="gwa-btn b-dk"><i class="fa fa-gear"></i> Configuration</a>
            <a href="{$_url}plugin/openwa_logs" class="gwa-btn b-dk"><i class="fa fa-list"></i> Message Logs</a>
        </div>
    </div>
</div>

<div class="gwa-panel">
    <h3><i class="fa fa-paper-plane"></i> Send a test message</h3>
    <div class="gwa-row">
        <input type="tel" id="qsPhone" placeholder="254712345678" style="padding:10px 12px;border:1.5px solid #dce6ec;border-radius:10px;font-size:13px;">
        <input type="text" id="qsMsg" placeholder="Message text" style="padding:10px 12px;border:1.5px solid #dce6ec;border-radius:10px;font-size:13px;">
        <button type="button" class="gwa-btn b-g" style="margin:0;width:auto;flex:0 0 150px;" onclick="quickSend()"><i class="fa fa-paper-plane"></i> Send</button>
    </div>
    <div id="qsResult" style="margin-top:10px;font-size:13px;"></div>
</div>

{/if}
</div>

<script>
var ajaxUrl = "{$_url}";
var connected = {if $connected}true{else}false{/if};
{literal}
function quickSend() {
    var p = document.getElementById('qsPhone').value.trim();
    var m = document.getElementById('qsMsg').value.trim();
    if (!p || !m) return alert('Phone and message required');
    var r = document.getElementById('qsResult');
    r.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
    fetch(ajaxUrl + 'plugin/openwa_send&to=' + encodeURIComponent(p) + '&message=' + encodeURIComponent(m))
        .then(function(res){ return res.json(); })
        .then(function(d){
            r.innerHTML = d.code === 'SUCCESS'
                ? '<span style="color:#059669;"><i class="fa fa-check"></i> Sent!</span>'
                : '<span style="color:#dc2626;"><i class="fa fa-times"></i> ' + (d.message||'Failed') + '</span>';
        }).catch(function(){ r.innerHTML = '<span style="color:#dc2626;">Request failed</span>'; });
}

function checkStatus(force) {
    var el = document.getElementById('lastChecked');
    if (el) el.textContent = 'Polling...';
    fetch(ajaxUrl + 'plugin/openwa_refresh_qr' + (force ? '&force=1' : ''))
        .then(function(r){ return r.json(); })
        .then(function(d){
            if (d.connected) { location.reload(); return; }
            if (d.qr) {
                var img = document.getElementById('qrImg');
                if (img) { img.src = d.qr; }
                else {
                    var c = document.getElementById('qrContainer');
                    if (c) c.innerHTML = '<img src="'+d.qr+'" id="qrImg" style="display:block;width:220px;height:220px;border-radius:10px;background:#fff;">';
                }
                var errEl = document.getElementById('qrError');
                if (errEl) errEl.style.display = 'none';
            }
            if (d.error) {
                var errEl2 = document.getElementById('qrError');
                if (errEl2) { errEl2.style.display = 'block'; errEl2.innerHTML = '<i class="fa fa-exclamation-circle"></i> ' + d.error; }
            }
            if (el) el.textContent = 'Checked ' + new Date().toLocaleTimeString();
        }).catch(function(){
            if (el) el.textContent = 'API unreachable';
        });
}

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
        if (qrSec <= 0) { qrSec = 20; checkStatus(true); }
    }, 1000);
}

var pairTimer = null;
function startPairCountdown() {
    var box = document.getElementById('pairBox');
    if (!box) return;
    var exp = parseInt(box.getAttribute('data-expires'), 10) || 0;
    var ttl = parseInt(box.getAttribute('data-ttl'), 10) || 60;
    if (!exp) return;
    var left = document.getElementById('pairLeft');
    var dead = document.getElementById('pairExpired');
    if (pairTimer) clearInterval(pairTimer);
    pairTimer = setInterval(function(){
        var s = exp - Math.floor(Date.now() / 1000);
        if (s <= 0) {
            clearInterval(pairTimer);
            if (left) left.textContent = '0';
            if (dead) dead.style.display = 'block';
            return;
        }
        if (left) left.textContent = s;
    }, 1000);
}

document.addEventListener('DOMContentLoaded', function(){
    startPairCountdown();
    if (!connected) {
        setTimeout(function(){ checkStatus(false); }, 3000);
        setInterval(function(){ if (!connected) checkStatus(false); }, 20000);
    }
});
{/literal}
</script>
