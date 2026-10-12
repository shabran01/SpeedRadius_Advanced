{include file="sections/header.tpl"}
{include file="mwaElectron_style.tpl"}

<div class="mw-hero">
    <h2><i class="fa fa-whatsapp"></i> M-WA Electron</h2>
    <p class="sub">WhatsApp via the MWA-Electron desktop bot API</p>
    <div class="mw-chip-row">
        <span class="mw-chip"><span class="mw-dot {if $gwReachable}ok{/if}"></span> {if $gwReachable}API Reachable{else}App Not Running{/if}</span>
        {if $gwStatus == 'connected'}
            <span class="mw-chip"><span class="mw-dot ok"></span> Session Connected</span>
        {elseif $gwStatus == 'not_connected'}
            <span class="mw-chip"><span class="mw-dot"></span> Session Not Started</span>
        {elseif $gwStatus == 'missing'}
            <span class="mw-chip"><span class="mw-dot"></span> Session Missing</span>
        {elseif $gwStatus == 'not_set'}
            <span class="mw-chip"><span class="mw-dot"></span> No Session Selected</span>
        {else}
            <span class="mw-chip"><span class="mw-dot"></span> Status Unknown</span>
        {/if}
        <span class="mw-chip"><i class="fa fa-tag"></i> plugin v{$gwPluginVersion}</span>
    </div>
</div>

{if !$gwReachable}
<div class="mw-note err">
    <b>M-WA is not reachable.</b> {$gwError}<br>
    <span class="mw-muted">M-WA is a desktop application, not a service — it only answers while it is open. If it runs on another machine, the base URL has to point at that machine's LAN address, not <code>127.0.0.1</code>.</span>
</div>
{elseif $gwStatus == 'not_connected'}
<div class="mw-note">
    <b>The bot session is not connected.</b> Open <b>M-WA &rarr; Bot</b> and press <b>Connect</b> on <code>{$gwSession}</code>. M-WA starts bot sessions by hand, and every restart of the app clears them, so notifications will fail until it is reconnected.
</div>
{elseif $gwStatus == 'missing'}
<div class="mw-note err">
    <b>The selected session does not exist on M-WA.</b> <code>{$gwSession}</code> is not in the session list below. Create it under <b>M-WA &rarr; Bot</b>, or open <a href="{$_url}plugin/mwa_electron_config">Configuration</a> and pick one that does.
</div>
{elseif $gwStatus == 'not_set'}
<div class="mw-note">
    <b>No bot session is selected.</b> Open <a href="{$_url}plugin/mwa_electron_config">Configuration</a> and pick the session notifications should be sent through.
</div>
{elseif $gwStatus == 'connected'}
<div class="mw-note ok">
    <b>Ready.</b> Sending through session <code>{$gwSession}</code>.
</div>
{/if}

<div class="mw-grid">
    {* LEFT — connection + sessions *}
    <div>
        <div class="mw-panel">
            <h3><i class="fa fa-plug"></i> Connection</h3>

            <div class="mw-stat">
                <span class="ic ic-b"><i class="fa fa-server"></i></span>
                <span class="t">Base URL</span>
                <span class="num" style="font-size:12.5px;font-weight:600;">{$gwUrl}</span>
            </div>
            <div class="mw-stat">
                <span class="ic ic-g"><i class="fa fa-mobile"></i></span>
                <span class="t">Bot Session</span>
                <span class="num" style="font-size:12.5px;font-weight:600;">{if $gwSession}{$gwSession}{else}&mdash;{/if}</span>
            </div>

            <p class="mw-muted" style="margin:14px 0 8px;">
                Sessions reported by M-WA. A session must read <b>connected</b> for messages to go out.
            </p>
            <div class="mw-scroll">
                <table class="mw-table">
                    <thead><tr><th>Session ID</th><th>Status</th></tr></thead>
                    <tbody id="mwSessionRows">
                    {if $gwSessions}
                        {foreach $gwSessions as $s}
                        <tr>
                            <td><code>{$s.sessionId}</code></td>
                            <td>
                                {if $s.status == 'connected'}
                                    <span class="mw-badge bg-ok">connected</span>
                                {else}
                                    <span class="mw-badge bg-warn">{$s.status}</span>
                                {/if}
                            </td>
                        </tr>
                        {/foreach}
                    {else}
                        <tr><td colspan="2" class="mw-muted">No sessions found. Create one under M-WA &rarr; Bot.</td></tr>
                    {/if}
                    </tbody>
                </table>
            </div>
            <p class="mw-muted" style="margin:10px 0 0;" id="mwChecked"></p>
        </div>

        <div class="mw-panel">
            <h3><i class="fa fa-wrench"></i> Actions</h3>
            <a href="{$_url}plugin/mwa_electron_config" class="mw-btn b-dk"><i class="fa fa-cog"></i> Configuration</a>
            <a href="{$_url}plugin/mwa_electron_logs" class="mw-btn b-dk"><i class="fa fa-list"></i> Message Logs</a>
            <button type="button" class="mw-btn b-o" onclick="mwCheckStatus()"><i class="fa fa-refresh"></i> Refresh Status</button>
        </div>
    </div>

    {* RIGHT — stats + recent + quick send *}
    <div>
        <div class="mw-panel">
            <h3><i class="fa fa-chart-bar"></i> Activity</h3>
            <div class="mw-stat"><span class="ic ic-b"><i class="fa fa-clock-o"></i></span><span class="t">Sent Today</span><span class="num">{$todaySent}</span></div>
            <div class="mw-stat"><span class="ic ic-g"><i class="fa fa-paper-plane"></i></span><span class="t">Total Sent</span><span class="num">{$totalSent}</span></div>
            <div class="mw-stat"><span class="ic ic-r"><i class="fa fa-exclamation-circle"></i></span><span class="t">Failed</span><span class="num">{$totalFailed}</span></div>
        </div>

        {if $gwEnabled}
        <div class="mw-panel">
            <h3><i class="fa fa-send"></i> Quick Send</h3>
            <div class="mw-field">
                <label>Phone</label>
                <input type="text" id="mwPhone" placeholder="07xx or 2547xx">
            </div>
            <div class="mw-field">
                <label>Message</label>
                <input type="text" id="mwMsg" placeholder="Type your message...">
            </div>
            <button type="button" class="mw-btn b-g" onclick="mwQuickSend()"><i class="fa fa-paper-plane"></i> Send Message</button>
            <div id="mwSendResult" style="margin-top:10px;font-size:12.5px;"></div>
        </div>
        {/if}

        <div class="mw-panel">
            <h3><i class="fa fa-history"></i> Recent</h3>
            <div class="mw-scroll">
                <table class="mw-table">
                    <thead><tr><th>Number</th><th>Status</th><th>When</th></tr></thead>
                    <tbody>
                    {if $recent}
                        {foreach $recent as $r}
                        <tr>
                            <td>{$r->phone_number}</td>
                            <td>
                                {if $r->status == 'delivered'}
                                    <span class="mw-badge bg-ok">delivered</span>
                                {elseif $r->status == 'failed'}
                                    <span class="mw-badge bg-bad">failed</span>
                                {else}
                                    <span class="mw-badge bg-neutral">{$r->status}</span>
                                {/if}
                            </td>
                            <td class="mw-muted">{$r->created_at}</td>
                        </tr>
                        {/foreach}
                    {else}
                        <tr><td colspan="3" class="mw-muted">Nothing sent yet.</td></tr>
                    {/if}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div>

<script>
var mwAjaxUrl = "{$_url}";

{literal}
function mwCheckStatus() {
    var el = document.getElementById('mwChecked');
    if (el) el.textContent = 'Checking...';
    fetch(mwAjaxUrl + 'plugin/mwa_electron_sessions')
        .then(function(r){ return r.json(); })
        .then(function(d){
            var body = document.getElementById('mwSessionRows');
            if (body) {
                if (!d.success) {
                    body.innerHTML = '<tr><td colspan="2" class="mw-muted">' + (d.error || 'Unreachable') + '</td></tr>';
                } else if (!d.sessions.length) {
                    body.innerHTML = '<tr><td colspan="2" class="mw-muted">No sessions found. Create one under M-WA -> Bot.</td></tr>';
                } else {
                    var html = '';
                    d.sessions.forEach(function(s){
                        var badge = (s.status === 'connected')
                            ? '<span class="mw-badge bg-ok">connected</span>'
                            : '<span class="mw-badge bg-warn">' + s.status + '</span>';
                        html += '<tr><td><code>' + s.sessionId + '</code></td><td>' + badge + '</td></tr>';
                    });
                    body.innerHTML = html;
                }
            }
            if (el) el.textContent = 'Checked ' + new Date().toLocaleTimeString();
        })
        .catch(function(){
            if (el) el.textContent = 'M-WA unreachable';
        });
}

function mwQuickSend() {
    var p = document.getElementById('mwPhone').value.trim();
    var m = document.getElementById('mwMsg').value.trim();
    var out = document.getElementById('mwSendResult');
    if (!p || !m) { alert('Phone and message are required'); return; }
    out.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
    fetch(mwAjaxUrl + 'plugin/mwa_electron_quick_send&to=' + encodeURIComponent(p) + '&message=' + encodeURIComponent(m))
        .then(function(r){ return r.json(); })
        .then(function(d){
            out.innerHTML = d.success
                ? '<span style="color:#059669;"><i class="fa fa-check"></i> Sent!</span>'
                : '<span style="color:#dc2626;"><i class="fa fa-times"></i> ' + (d.error || 'Failed') + '</span>';
        })
        .catch(function(){ out.innerHTML = '<span style="color:#dc2626;">Request failed</span>'; });
}
{/literal}
</script>

{include file="sections/footer.tpl"}
