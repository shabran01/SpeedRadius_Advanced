{include file="sections/header.tpl"}
{include file="mwaElectron_style.tpl"}

<div class="mw-hero">
    <h2><i class="fa fa-cog"></i> M-WA Electron Configuration</h2>
    <p class="sub">Point SpeedRadius at the MWA-Electron desktop app</p>
    <div class="mw-chip-row">
        <span class="mw-chip"><span class="mw-dot {if $gwReachable}ok{/if}"></span> {if $gwReachable}API Reachable{else}App Not Running{/if}</span>
        <span class="mw-chip"><i class="fa fa-tag"></i> plugin v{$gwPluginVersion}</span>
    </div>
</div>

{if !$gwReachable}
<div class="mw-note err">
    <b>M-WA could not be reached with the address currently saved.</b> {$gwError}
</div>
{/if}

<div class="mw-panel">
    <h3><i class="fa fa-sliders"></i> Connection</h3>
    <form action="{$_url}plugin/mwa_electron_config" method="post">

        <div class="mw-field">
            <label>Base URL</label>
            <input type="text" name="mwa_url" value="{$gw_url|escape:'html'}" placeholder="http://127.0.0.1:3000">
            <div class="hint">
                Where M-WA's Express server listens. It uses <code>PORT</code> from the app's <code>.env</code>, which defaults to <b>3000</b>.
                If M-WA runs on another computer, use that machine's LAN address and make sure its firewall allows the port.
            </div>
        </div>

        <div class="mw-field">
            <label>Bot Session</label>
            <input type="text" name="mwa_session_id" list="mwSessionList" value="{$gw_session_id|escape:'html'}" placeholder="session-1">
            <datalist id="mwSessionList">
                {foreach $gwSessions as $s}
                <option value="{$s.sessionId}">{$s.status}</option>
                {/foreach}
            </datalist>
            <div class="hint">
                The Baileys bot session to send through, as shown in <b>M-WA &rarr; Bot</b>.
                {if $gwReachable}
                    Detected right now:
                    {foreach $gwSessions as $s}
                        <code>{$s.sessionId}</code> &mdash; {$s.status}{if !$s@last}, {/if}
                    {/foreach}
                {else}
                    Start M-WA and press <b>Refresh</b> below to detect sessions automatically.
                {/if}
            </div>
        </div>

        <div class="mw-row">
            <div class="mw-field">
                <label>Default Country Code</label>
                <input type="text" name="mwa_country_code" value="{$gw_country_code|escape:'html'}" placeholder="254">
                <div class="hint">Digits prepended to local numbers (<code>0712&hellip;</code>) and bare national numbers.</div>
            </div>
            <div class="mw-field">
                <label>Request Timeout (seconds)</label>
                <input type="number" name="mwa_timeout" min="5" max="120" value="{$gw_timeout}">
                <div class="hint">M-WA hands the message to WhatsApp immediately, so this only needs to cover network latency.</div>
            </div>
        </div>

        <div class="mw-row">
            <div class="mw-field">
                <label>Extra Header Name <span class="mw-muted">(optional)</span></label>
                <input type="text" name="mwa_header_name" value="{$gw_header_name|escape:'html'}" placeholder="X-Api-Key">
            </div>
            <div class="mw-field">
                <label>Extra Header Value <span class="mw-muted">(optional)</span></label>
                <input type="text" name="mwa_header_value" value="{$gw_header_value|escape:'html'}" placeholder="value">
                <div class="hint">Sent on every request. See the security note below.</div>
            </div>
        </div>

        <div class="mw-field">
            <label>Message Signature <span class="mw-muted">(optional)</span></label>
            <input type="text" name="mwa_signature" value="{$gw_signature|escape:'html'}" placeholder="Example: SpeedRadius Billing">
            <div class="hint">Appended as a final line to every outgoing message, so recipients can tell automated notices apart from typed ones.</div>
        </div>

        <div class="mw-field">
            <label>Enable This Gateway</label>
            <select name="mwa_enabled">
                <option value="no" {if !$gw_enabled}selected{/if}>No — do not send notifications through M-WA</option>
                <option value="yes" {if $gw_enabled}selected{/if}>Yes — use M-WA for system notifications</option>
            </select>
            <div class="hint">
                When enabled this runs alongside the other WhatsApp gateways. GoWhatsApp keeps priority if it is also enabled,
                so notifications do not silently change sender when both are on.
            </div>
        </div>

        <button type="submit" name="save" value="1" class="mw-btn b-g"><i class="fa fa-save"></i> Save Configuration</button>
    </form>
</div>

<div class="mw-panel">
    <h3><i class="fa fa-shield"></i> Before You Enable This</h3>
    <div class="mw-note">
        <b>M-WA is a desktop app, not a server.</b> Its API only answers while the window is open. Close the app, sign out,
        or reboot the machine and every notification fails until it is running again with the bot session connected.
        For unattended billing reminders it is safer to keep GoWhatsApp (GOWA, containerised on the server) as the primary
        and use M-WA as a supplementary sender.
    </div>
    <div class="mw-note err">
        <b>The <code>/webhook/*</code> endpoints have no authentication.</b> Anyone who can reach the port can send WhatsApp
        messages as you. Keep M-WA on a trusted network — never expose its port to the internet as-is. If it must be
        reachable across networks, put an authenticating reverse proxy in front of it and use the extra header fields above
        so this plugin sends the credential the proxy expects.
    </div>
    <div class="mw-note">
        <b>Sessions are started by hand.</b> In <b>M-WA &rarr; Bot</b>, press <b>Connect</b> and scan the QR. M-WA keeps the
        session files on disk but does not reconnect them automatically on launch, so after every restart this page will show
        <code>not_connected</code> until someone presses Connect again.
    </div>
</div>

<div class="mw-panel">
    <h3><i class="fa fa-wrench"></i> Actions</h3>
    <button type="button" class="mw-btn b-o" onclick="location.reload()"><i class="fa fa-refresh"></i> Refresh Detected Sessions</button>
    <a href="{$_url}plugin/mwa_electron" class="mw-btn b-dk"><i class="fa fa-dashboard"></i> Back To Dashboard</a>
    <a href="{$_url}plugin/mwa_electron_logs" class="mw-btn b-dk"><i class="fa fa-list"></i> Message Logs</a>
</div>

</div>

{include file="sections/footer.tpl"}
