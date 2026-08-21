{include file="sections/header.tpl"}

<style>
{literal}
    .gwa-wrap { max-width:1040px; margin:0 auto; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif; }
    .gwa-hero { position:relative; overflow:hidden; border-radius:18px; padding:24px 26px; margin-bottom:18px; color:#fff;
        background:linear-gradient(120deg,#0b1f33 0%,#123c52 55%,#0e7a5f 130%); box-shadow:0 10px 30px rgba(10,30,50,.25); }
    .gwa-hero:after { content:""; position:absolute; right:-60px; top:-60px; width:220px; height:220px; border-radius:50%;
        background:radial-gradient(circle,rgba(37,211,102,.35),transparent 70%); }
    .gwa-hero h2 { margin:0 0 4px; font-size:22px; font-weight:800; letter-spacing:.3px; }
    .gwa-hero .sub { font-size:12.5px; color:#cfe6dd; margin:0; }
    .gwa-chip { display:inline-flex; align-items:center; gap:7px; padding:5px 12px; border-radius:999px; font-size:12px; font-weight:700;
        background:rgba(255,255,255,.12); backdrop-filter:blur(3px); }
    .gwa-dot { width:9px; height:9px; border-radius:50%; background:#f87171; box-shadow:0 0 0 0 rgba(248,113,113,.6); }
    .gwa-dot.ok { background:#34d399; box-shadow:0 0 8px rgba(52,211,153,.9); animation:gwaPulse 1.8s infinite; }
    @keyframes gwaPulse { 0%{box-shadow:0 0 0 0 rgba(52,211,153,.7)} 70%{box-shadow:0 0 0 9px rgba(52,211,153,0)} 100%{box-shadow:0 0 0 0 rgba(52,211,153,0)} }
    .gwa-grid { display:grid; grid-template-columns:1.55fr 1fr; gap:18px; }
    @media (max-width:860px){ .gwa-grid { grid-template-columns:1fr; } }
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
    .b-o { background:#fff; color:#334e63; border:1.5px solid #dce6ec; } .b-o:hover{ background:#f4f8fa; }
    .gwa-qr { text-align:center; }
    .gwa-phone { position:relative; display:inline-block; padding:26px 14px 18px; border-radius:26px; background:#0f2b3d;
        box-shadow:inset 0 0 0 3px #1d445c, 0 14px 34px rgba(15,43,61,.3); margin:4px 0 14px; }
    .gwa-phone:before { content:""; position:absolute; top:9px; left:50%; transform:translateX(-50%); width:52px; height:6px; border-radius:6px; background:#1d445c; }
    .gwa-phone img { display:block; width:220px; height:220px; border-radius:10px; background:#fff; }
    .gwa-ring { display:inline-block; animation:gwaSpin 1.6s linear infinite; }
    @keyframes gwaSpin { 100%{ transform:rotate(360deg); } }
    .gwa-bar { background:#e8eef4; border-radius:8px; height:6px; width:240px; margin:10px auto; overflow:hidden; }
    .gwa-bar > div { background:linear-gradient(90deg,#25d366,#0e7a5f); height:100%; width:100%; transition:width 1s linear; border-radius:8px; }
    .gwa-muted { color:#8ba0ae; font-size:12px; }
    .gwa-ok-hero { text-align:center; padding:10px 0 6px; }
    .gwa-ok-hero .big { width:74px; height:74px; margin:0 auto 14px; border-radius:50%; background:#e7f9ef; color:#059669;
        display:flex; align-items:center; justify-content:center; font-size:34px; box-shadow:0 0 0 9px #f0fbf5; }
    .gwa-note { font-size:12.5px; background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; padding:10px 13px; border-radius:12px; margin-bottom:16px; }
    .gwa-field { margin-bottom:12px; }
    .gwa-field label { display:block; font-size:12px; font-weight:700; color:#334e63; margin-bottom:5px; }
    .gwa-field .hint { font-size:11px; color:#8ba0ae; margin-top:4px; }
    .gwa-row { display:flex; gap:8px; }
    .gwa-code { background:#0f2b3d; color:#d7f5e8; padding:14px 16px; border-radius:12px; font-size:12px; overflow-x:auto; margin:0; }
{/literal}
</style>

<div class="gwa-wrap">

{* ============ CONFIG PAGE ============ *}
{if $menu == 'config'}

<div class="gwa-hero">
    <h2><i class="fa fa-sliders"></i> GoWhatsApp Configuration</h2>
    <p class="sub">Point SpeedRadius at your self-hosted WhatsApp API, then scan once to link the device.</p>
</div>

<div class="gwa-grid">
    <div class="gwa-panel">
        <h3><i class="fa fa-plug"></i> Connection</h3>
        <form method="post">
            <div class="gwa-field">
                <label>API Base URL</label>
                <input type="text" name="gowhatsapp_url" class="form-control" value="{$gw_url}" placeholder="https://wa-api.speedcomwifi.co.ke/api/v1/whatsapp">
                <div class="hint">Include the full path <code>/api/v1/whatsapp</code> — no trailing slash.</div>
            </div>
            <div class="gwa-row">
                <div class="gwa-field" style="flex:1;">
                    <label>Basic Auth Username</label>
                    <input type="text" name="gowhatsapp_username" class="form-control" value="{$gw_username}" placeholder="ThisIsUsername">
                </div>
                <div class="gwa-field" style="flex:1;">
                    <label>Basic Auth Password</label>
                    <input type="password" name="gowhatsapp_password" class="form-control" value="{$gw_password}" placeholder="ThisIsPassword">
                </div>
            </div>
            <div class="gwa-field">
                <label>Enable as primary WhatsApp sender</label>
                <select name="gowhatsapp_enabled" class="form-control">
                    <option value="no"  {if !$gw_enabled}selected{/if}>No — keep it off for now</option>
                    <option value="yes" {if $gw_enabled}selected{/if}>Yes — route all system WhatsApp through this gateway</option>
                </select>
            </div>
            <div class="gwa-field">
                <label>Linked WhatsApp Number <span class="gwa-muted">(optional, shown on dashboard)</span></label>
                <input type="text" name="gowhatsapp_number" class="form-control" value="{$gw_number}" placeholder="254712345678">
            </div>
            <div class="gwa-field">
                <label>External API Key</label>
                <input type="text" name="gowhatsapp_api_key" class="form-control" value="{$gw_api_key}" placeholder="Random secret for the public API">
                <div class="hint">Lets cron jobs / other systems send WhatsApp via this gateway.</div>
            </div>
            <button type="submit" name="save" value="1" class="gwa-btn b-g"><i class="fa fa-check"></i> Save &amp; Test Connection</button>
            <a href="{$_url}plugin/gowhatsapp" class="gwa-btn b-o"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>
        </form>
    </div>

    <div class="gwa-panel">
        <h3><i class="fa fa-code"></i> External API</h3>
        <p class="gwa-muted" style="margin-top:-6px;">Send a WhatsApp message from anywhere:</p>
        <pre class="gwa-code">GET {$_detected_url}/?_route=plugin/gowhatsapp_api
  &amp;key=YOUR_API_KEY
  &amp;to=254712345678
  &amp;message=Hello</pre>
        <p class="gwa-muted" style="margin-top:10px;"><i class="fa fa-reply"></i> Response: <code>{literal}{"code":"SUCCESS","message":"Sent"}{/literal}</code></p>
        <hr style="margin:16px 0;border-color:#eef3f6;">
        <h3 style="margin-top:0;"><i class="fa fa-info-circle"></i> Docker Reference</h3>
        <pre class="gwa-code">docker run -d --restart unless-stopped \
  -p 127.0.0.1:3000:3000 \
  -v gowam-data:/usr/app/gowam-rest/dbs \
  -e AUTH_BASIC_USERNAME=you \
  -e AUTH_BASIC_PASSWORD=secret \
  dimaskiddo/go-whatsapp-multidevice-rest:latest</pre>
    </div>
</div>

{/if}

{* ============ MAIN DASHBOARD ============ *}
{if !$menu}

<div class="gwa-hero">
    <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;position:relative;z-index:1;">
        <div>
            <h2><i class="fa fa-whatsapp"></i> GoWhatsApp Console</h2>
            <p class="sub">Self-hosted WhatsApp Multi-Device gateway</p>
        </div>
        <div style="margin-left:auto;display:flex;gap:8px;flex-wrap:wrap;position:relative;z-index:1;">
            <span class="gwa-chip"><span class="gwa-dot {if $serverOk}ok{/if}"></span> {if $serverOk}API Online{else}API Offline{/if}</span>
            <span class="gwa-chip"><span class="gwa-dot {if $connected}ok{/if}"></span> {if $connected}Linked{else}Not Linked{/if}</span>
        </div>
    </div>
</div>

{if $error && !$connected && !$qrCode}
<div class="gwa-note"><i class="fa fa-exclamation-triangle"></i>
    {if $serverOk}{$error}{else}Cannot reach the WhatsApp API — check the base URL and that the Docker container is running.{/if}
</div>
{/if}

<div class="gwa-grid">
    {* LEFT — connection / QR *}
    <div>
        {if $connected}
        <div class="gwa-panel">
            <div class="gwa-ok-hero">
                <div class="big"><i class="fa fa-whatsapp"></i></div>
                <h3 style="margin:0 0 4px;font-size:18px;">Device Linked</h3>
                <p class="gwa-muted" style="margin:0 0 16px;">{if $me}+{$me}{else}WhatsApp is connected and ready to send{/if}</p>
            </div>
            <a href="{$_url}plugin/gowhatsapp_logout" class="gwa-btn b-r" onclick="return confirm('Unlink this WhatsApp device? You will need to scan QR again.')"><i class="fa fa-sign-out"></i> Unlink Device</a>
        </div>
        {else}
        <div class="gwa-panel gwa-qr" id="qrSection">
            <h3><i class="fa fa-qrcode"></i> Link WhatsApp</h3>
            <div class="gwa-phone">
                <div id="qrContainer">
                    {if $qrCode}
                        <img src="{$qrCode}" id="qrImg">
                    {else}
                        <div style="width:220px;height:220px;display:flex;align-items:center;justify-content:center;background:#fff;border-radius:10px;color:#8ba0ae;">
                            <i class="fa fa-spinner fa-spin fa-3x gwa-ring"></i>
                        </div>
                    {/if}
                </div>
            </div>
            <p class="gwa-muted" style="margin:0;">Open WhatsApp → <b>Linked Devices</b> → <b>Link a Device</b> and scan.</p>
            <div id="qrError" style="display:none;color:#dc2626;font-size:12px;margin:10px 0 0;"></div>
            <div class="gwa-bar"><div id="qrFill"></div></div>
            <p class="gwa-muted">QR refreshes in <span id="qrSec">20</span>s <span style="float:right;" id="lastChecked"></span></p>

            <hr style="margin:18px 0;border-color:#eef3f6;">
            <h3 style="margin-bottom:6px;"><i class="fa fa-link"></i> Prefer a pairing code?</h3>
            <p class="gwa-muted" style="margin:-4px 0 12px;">WhatsApp → Linked Devices → <b>Link with Phone Number</b>.</p>
            <form action="{$_url}plugin/gowhatsapp_pair_code" method="post" class="gwa-row">
                <input type="text" name="phone" class="form-control" placeholder="2547XXXXXXXX" style="flex:1;">
                <button type="submit" class="gwa-btn b-dk" style="width:auto;margin:0;"><i class="fa fa-key"></i> Get Code</button>
            </form>
        </div>
        {/if}
    </div>

    {* RIGHT — stats + actions + send *}
    <div>
        <div class="gwa-panel">
            <h3><i class="fa fa-chart-bar"></i> Activity</h3>
            <div class="gwa-stat"><span class="ic ic-b"><i class="fa fa-clock-o"></i></span><span class="t">Sent Today</span><span class="num">{$todaySent}</span></div>
            <div class="gwa-stat"><span class="ic ic-g"><i class="fa fa-paper-plane"></i></span><span class="t">Total Sent</span><span class="num">{$totalSent}</span></div>
            <div class="gwa-stat"><span class="ic ic-r"><i class="fa fa-exclamation-circle"></i></span><span class="t">Failed</span><span class="num">{$totalFailed}</span></div>
        </div>

        <div class="gwa-panel">
            <h3><i class="fa fa-wrench"></i> Actions</h3>
            <a href="{$_url}plugin/gowhatsapp_config" class="gwa-btn b-dk"><i class="fa fa-cog"></i> Configuration</a>
            <a href="{$_url}plugin/gowhatsapp_logs" class="gwa-btn b-dk"><i class="fa fa-list"></i> Message Logs</a>
            {if !$connected}
                <a href="{$_url}plugin/gowhatsapp_connect" class="gwa-btn b-g"><i class="fa fa-qrcode"></i> Generate QR Code</a>
            {/if}
            <button class="gwa-btn b-o" onclick="checkStatus()"><i class="fa fa-refresh"></i> Refresh Status</button>
        </div>

        {if $connected}
        <div class="gwa-panel">
            <h3><i class="fa fa-send"></i> Quick Send</h3>
            <div class="gwa-field">
                <label>Phone</label>
                <input type="text" id="qsPhone" class="form-control" placeholder="07xx or 2547xx">
            </div>
            <div class="gwa-field">
                <label>Message</label>
                <input type="text" id="qsMsg" class="form-control" placeholder="Type your message...">
            </div>
            <button class="gwa-btn b-g" onclick="quickSend()"><i class="fa fa-paper-plane"></i> Send Message</button>
            <div id="qsResult" style="margin-top:8px;font-size:12px;"></div>
        </div>

        <div class="gwa-panel">
            <h3><i class="fa fa-image"></i> Send Image</h3>
            <form action="{$_url}plugin/gowhatsapp_send_image" method="post" enctype="multipart/form-data">
                <div class="gwa-field">
                    <label>Phone</label>
                    <input type="text" name="to" class="form-control" placeholder="07xx or 2547xx" required>
                </div>
                <div class="gwa-field">
                    <label>Caption <span class="gwa-muted">(optional)</span></label>
                    <input type="text" name="caption" class="form-control" placeholder="Image caption...">
                </div>
                <div class="gwa-field">
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*" required style="font-size:12px;padding:6px;">
                </div>
                <button type="submit" class="gwa-btn b-dk"><i class="fa fa-upload"></i> Send Image</button>
            </form>
        </div>
        {/if}
    </div>
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
    fetch(ajaxUrl + 'plugin/gowhatsapp_send&to=' + encodeURIComponent(p) + '&message=' + encodeURIComponent(m))
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
    var url = ajaxUrl + 'plugin/gowhatsapp_refresh_qr' + (force ? '&force=1' : '');
    fetch(url)
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

var scanInterval = null;
function startPolling() {
    if (scanInterval) clearInterval(scanInterval);
    scanInterval = setInterval(function(){ checkStatus(false); }, 6000);
}

if (!connected && document.getElementById('qrSection')) {
    checkStatus(false);
    startCountdown();
    startPolling();
}
{/literal}
</script>

{include file="sections/footer.tpl"}
