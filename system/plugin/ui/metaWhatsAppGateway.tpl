{include file="sections/header.tpl"}
<style>
.mwa-wrap { padding: 0 0 40px; }
.mwa-hero {
    background: linear-gradient(135deg, #00a884 0%, #128c7e 55%, #075e54 100%);
    border-radius: 16px; padding: 24px 26px; margin-bottom: 24px; color: #fff;
    box-shadow: 0 2px 0 rgba(255,255,255,.15) inset, 0 12px 36px rgba(7,94,84,.45);
    display: flex; align-items: center; gap: 18px; flex-wrap: wrap;
}
.mwa-hero-icon {
    width: 60px; height: 60px; background: rgba(255,255,255,.18); border-radius: 16px;
    display: flex; align-items: center; justify-content: center; font-size: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,.18), 0 1px 0 rgba(255,255,255,.3) inset;
    flex-shrink: 0;
}
.mwa-hero-body h2 { margin: 0 0 4px; font-size: 22px; font-weight: 700; text-shadow: 0 1px 4px rgba(0,0,0,.2); }
.mwa-hero-body p  { margin: 0; opacity: .80; font-size: 13px; }
.mwa-status-badge {
    margin-left: auto; padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 700;
    border: 1.5px solid rgba(255,255,255,.4); background: rgba(255,255,255,.15);
    backdrop-filter: blur(4px);
}
.mwa-status-badge.active   { background: rgba(74,222,128,.25); border-color: rgba(74,222,128,.6); }
.mwa-status-badge.inactive { background: rgba(239,68,68,.25);  border-color: rgba(239,68,68,.6);  }

/* Tabs */
.mwa-tabs { display: flex; gap: 4px; margin-bottom: 20px; flex-wrap: wrap; }
.mwa-tab {
    padding: 8px 18px; border-radius: 10px; font-size: 13px; font-weight: 600;
    border: 1.5px solid #e2e8f0; background: #f8fafc; color: #64748b;
    text-decoration: none !important; cursor: pointer; transition: all .15s;
    display: inline-flex; align-items: center; gap: 6px;
}
.mwa-tab:hover { background: #eff6ff; border-color: #93c5fd; color: #1d4ed8; }
.mwa-tab.active { background: #1d4ed8; border-color: #1d4ed8; color: #fff !important; box-shadow: 0 4px 12px rgba(29,78,216,.30); }

/* Cards */
.mwa-card {
    background: #fff; border-radius: 16px; border: 1px solid #e2e8f0;
    box-shadow: 0 1px 0 rgba(255,255,255,.9) inset, 0 4px 16px rgba(0,0,0,.06);
    margin-bottom: 20px; overflow: hidden;
}
.mwa-card-hdr {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    padding: 14px 20px; border-bottom: 1px solid #e2e8f0;
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
}
.mwa-card-hdr h4 { margin: 0; font-size: 15px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px; }
.mwa-card-body  { padding: 20px; }

/* Form */
.mwa-fg { margin-bottom: 16px; }
.mwa-fg label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #64748b; margin-bottom: 6px; }
.mwa-input {
    width: 100%; padding: 9px 13px; border: 1.5px solid #e2e8f0; border-radius: 10px;
    font-size: 13px; color: #0f172a; box-sizing: border-box;
    background: linear-gradient(180deg, #f8fafc, #fff);
    transition: border-color .15s, box-shadow .15s; outline: none; font-family: inherit;
}
.mwa-input:focus { border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,.10); }
.mwa-input[readonly] { background: #f1f5f9; color: #64748b; cursor: default; }
.mwa-hint { font-size: 11px; color: #94a3b8; margin-top: 4px; }
.mwa-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px; }

/* Buttons */
.mwa-btn {
    display: inline-flex; align-items: center; gap: 6px; padding: 9px 20px;
    border-radius: 10px; font-size: 13px; font-weight: 700; border: 0;
    cursor: pointer; text-decoration: none !important; transition: all .15s;
    box-shadow: 0 1px 0 rgba(255,255,255,.25) inset;
}
.mwa-btn-green  { background: linear-gradient(180deg,#25d366,#128c7e); color:#fff!important; box-shadow:0 4px 14px rgba(18,140,126,.45),0 2px 0 #075e54; }
.mwa-btn-green:hover  { filter: brightness(1.08); transform: translateY(-1px); }
.mwa-btn-blue   { background: linear-gradient(180deg,#4f95ff,#1d4ed8); color:#fff!important; box-shadow:0 4px 14px rgba(29,78,216,.40),0 2px 0 #1235a0; }
.mwa-btn-blue:hover   { filter: brightness(1.08); transform: translateY(-1px); }
.mwa-btn-orange { background: linear-gradient(180deg,#fbbf24,#f59e0b); color:#fff!important; box-shadow:0 4px 14px rgba(245,158,11,.40),0 2px 0 #b45309; }
.mwa-btn-orange:hover { filter: brightness(1.08); transform: translateY(-1px); }
.mwa-btn-gray   { background: linear-gradient(180deg,#fff,#f1f5f9); color:#374151!important; border:1.5px solid #e2e8f0; box-shadow:0 2px 6px rgba(0,0,0,.07); }
.mwa-btn-gray:hover   { background: #e2e8f0; }
.mwa-btn-block  { width: 100%; box-sizing: border-box; justify-content: center; }
.mwa-btn-sm     { padding: 6px 14px; font-size: 12px; }
.mwa-btn-danger { background: linear-gradient(180deg,#f87171,#ef4444); color:#fff!important; box-shadow:0 4px 14px rgba(239,68,68,.40),0 2px 0 #b91c1c; }

/* Log table */
.mwa-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.mwa-table th { background: #f8fafc; padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: #64748b; border-bottom: 1.5px solid #e2e8f0; }
.mwa-table td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #374151; }
.mwa-table tr:last-child td { border-bottom: 0; }
.mwa-table tr:hover td { background: #f8fafc; }
.mwa-badge-ok  { background: #dcfce7; color: #15803d; padding: 2px 9px; border-radius:20px; font-size:11px; font-weight:700; }
.mwa-badge-err { background: #fee2e2; color: #b91c1c; padding: 2px 9px; border-radius:20px; font-size:11px; font-weight:700; }

/* Webhook setup */
.mwa-code { background: #1e293b; color: #e2e8f0; font-size: 12px; font-family: monospace; padding: 14px 16px; border-radius: 10px; overflow-x: auto; line-height: 1.7; margin: 10px 0; }
.mwa-step { display: flex; gap: 12px; margin-bottom: 16px; }
.mwa-step-num { width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg,#00a884,#128c7e); color:#fff; font-weight:700; font-size:13px; display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.mwa-step-body { flex: 1; }
.mwa-step-body strong { display: block; font-size: 13px; color: #0f172a; margin-bottom: 4px; }
.mwa-step-body p { font-size: 13px; color:#64748b; margin: 0; line-height:1.6; }

/* Alert */
.mwa-alert { padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 16px; display: flex; gap: 10px; align-items: flex-start; }
.mwa-alert-warn { background: #fef9c3; border: 1px solid #fde047; color: #854d0e; }
.mwa-alert-info { background: #eff6ff; border: 1px solid #93c5fd; color: #1e40af; }
.mwa-alert-ok   { background: #dcfce7; border: 1px solid #86efac; color: #15803d; }
</style>

<div class="mwa-wrap">

{* ── Hero ── *}
<div class="mwa-hero">
    <div class="mwa-hero-icon">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="white"><path d="M17.498 14.382c-.301-.15-1.767-.867-2.04-.966-.273-.101-.473-.15-.673.15-.197.295-.771.964-.944 1.162-.175.195-.349.21-.646.075-.3-.15-1.263-.465-2.403-1.485-.888-.795-1.484-1.77-1.66-2.07-.174-.3-.019-.465.13-.615.136-.135.301-.345.451-.523.146-.181.194-.301.297-.496.1-.21.049-.375-.025-.524-.075-.15-.672-1.62-.922-2.206-.24-.584-.487-.51-.672-.51-.172-.015-.371-.015-.571-.015-.2 0-.523.074-.797.359-.273.3-1.045 1.02-1.045 2.475s1.07 2.865 1.219 3.075c.149.195 2.105 3.195 5.1 4.485.714.3 1.27.48 1.704.629.714.227 1.365.195 1.88.121.574-.091 1.767-.721 2.016-1.426.255-.705.255-1.29.18-1.425-.074-.135-.27-.21-.57-.345m-5.446 7.443h-.016c-1.77 0-3.524-.48-5.055-1.38l-.36-.214-3.75.975 1.005-3.645-.239-.375c-.99-1.576-1.516-3.391-1.516-5.26 0-5.445 4.455-9.885 9.942-9.885 2.654 0 5.145 1.035 7.021 2.91 1.875 1.859 2.909 4.35 2.909 6.99-.004 5.444-4.46 9.885-9.935 9.885M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .104 5.334.101 11.893c0 2.096.549 4.14 1.595 5.945L0 24l6.335-1.652c1.746.943 3.71 1.444 5.71 1.447h.006c6.585 0 11.946-5.336 11.949-11.896 0-3.176-1.24-6.165-3.495-8.411"/></svg>
    </div>
    <div class="mwa-hero-body">
        <h2>Meta WhatsApp Cloud API</h2>
        <p>Official Meta Business WhatsApp — expiry reminders, payments, OTPs &amp; more</p>
    </div>
    {if $_c['meta_wa_active'] == 'meta'}
        <span class="mwa-status-badge active"><span style="color:#4ade80;">&#9679;</span> &nbsp;Active</span>
    {else}
        <span class="mwa-status-badge inactive"><span style="color:#f87171;">&#9679;</span> &nbsp;Inactive</span>
    {/if}
</div>

{* ── Nav Tabs ── *}
<div class="mwa-tabs">
    <a href="{$_url}plugin/metaWhatsApp" class="mwa-tab {if !$menu or $menu=='dashboard'}active{/if}">
        <span class="glyphicon glyphicon-dashboard"></span> Dashboard
    </a>
    <a href="{$_url}plugin/metaWhatsApp_config" class="mwa-tab {if $menu=='config'}active{/if}">
        <span class="glyphicon glyphicon-cog"></span> Settings
    </a>
    <a href="{$_url}plugin/metaWhatsApp_broadcast" class="mwa-tab {if $menu=='broadcast'}active{/if}">
        <span class="glyphicon glyphicon-send"></span> Broadcast
    </a>
    <a href="{$_url}plugin/metaWhatsApp_logs" class="mwa-tab {if $menu=='logs'}active{/if}">
        <span class="glyphicon glyphicon-list-alt"></span> Logs
    </a>
</div>

{* ════════════════════════ DASHBOARD ════════════════════════ *}
{if !$menu or $menu == 'dashboard'}

<div class="row">
<div class="col-md-6">

<div class="mwa-card">
    <div class="mwa-card-hdr">
        <h4><span class="glyphicon glyphicon-send" style="color:#00a884;"></span> Send Test Message</h4>
    </div>
    <div class="mwa-card-body">
        {if $_c['meta_wa_active'] != 'meta'}
        <div class="mwa-alert mwa-alert-warn">
            <span class="glyphicon glyphicon-warning-sign"></span>
            Gateway is not set to <b>Active (meta)</b>. Go to <a href="{$_url}plugin/metaWhatsApp_config">Settings</a> and set Gateway Active = <b>meta</b>.
        </div>
        {/if}
        <div class="mwa-fg">
            <label>Phone Number (E.164 or local Kenya)</label>
            <input type="text" id="mwa_test_phone" class="mwa-input" placeholder="e.g. 0712345678 or 254712345678">
        </div>
        <div class="mwa-fg">
            <label>Message</label>
            <textarea id="mwa_test_msg" class="mwa-input" rows="4"
                placeholder="Hello! This is a test from {$_c['CompanyName']}"></textarea>
        </div>
        <button class="mwa-btn mwa-btn-green mwa-btn-block" onclick="mwaSendTest()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M17.498 14.382c-.301-.15-1.767-.867-2.04-.966-.273-.101-.473-.15-.673.15-.197.295-.771.964-.944 1.162-.175.195-.349.21-.646.075-.3-.15-1.263-.465-2.403-1.485-.888-.795-1.484-1.77-1.66-2.07-.174-.3-.019-.465.13-.615.136-.135.301-.345.451-.523.146-.181.194-.301.297-.496.1-.21.049-.375-.025-.524-.075-.15-.672-1.62-.922-2.206-.24-.584-.487-.51-.672-.51-.172-.015-.371-.015-.571-.015-.2 0-.523.074-.797.359-.273.3-1.045 1.02-1.045 2.475s1.07 2.865 1.219 3.075c.149.195 2.105 3.195 5.1 4.485.714.3 1.27.48 1.704.629.714.227 1.365.195 1.88.121.574-.091 1.767-.721 2.016-1.426.255-.705.255-1.29.18-1.425-.074-.135-.27-.21-.57-.345m-5.446 7.443h-.016c-1.77 0-3.524-.48-5.055-1.38l-.36-.214-3.75.975 1.005-3.645-.239-.375c-.99-1.576-1.516-3.391-1.516-5.26 0-5.445 4.455-9.885 9.942-9.885 2.654 0 5.145 1.035 7.021 2.91 1.875 1.859 2.909 4.35 2.909 6.99-.004 5.444-4.46 9.885-9.935 9.885M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .104 5.334.101 11.893c0 2.096.549 4.14 1.595 5.945L0 24l6.335-1.652c1.746.943 3.71 1.444 5.71 1.447h.006c6.585 0 11.946-5.336 11.949-11.896 0-3.176-1.24-6.165-3.495-8.411"/></svg>
            Send Test Message
        </button>
        <div id="mwa_test_result" style="margin-top:14px;display:none;" class="mwa-alert"></div>
    </div>
</div>

<div class="mwa-card">
    <div class="mwa-card-hdr">
        <h4><span class="glyphicon glyphicon-info-sign" style="color:#3b82f6;"></span> Current Configuration</h4>
    </div>
    <div class="mwa-card-body">
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
            <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:8px 0;color:#64748b;font-weight:700;width:45%;">Phone Number ID</td>
                <td style="padding:8px 0;font-family:monospace;color:#0f172a;">
                    {if $_c['meta_wa_phone_id']}{$_c['meta_wa_phone_id']|truncate:24}{else}<span style="color:#94a3b8;">Not set</span>{/if}
                </td>
            </tr>
            <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:8px 0;color:#64748b;font-weight:700;">Access Token</td>
                <td style="padding:8px 0;color:#0f172a;">
                    {if $_c['meta_wa_access_token']}<span style="color:#10b981;font-weight:700;">&#10003; Configured</span>{else}<span style="color:#ef4444;">Not set</span>{/if}
                </td>
            </tr>
            <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:8px 0;color:#64748b;font-weight:700;">API Version</td>
                <td style="padding:8px 0;color:#0f172a;">{$_c['meta_wa_api_version']|default:'v19.0'}</td>
            </tr>
            <tr>
                <td style="padding:8px 0;color:#64748b;font-weight:700;">Gateway&nbsp;Active</td>
                <td style="padding:8px 0;">
                    {if $_c['meta_wa_active'] == 'meta'}
                        <span class="mwa-badge-ok">meta (active)</span>
                    {else}
                        <span class="mwa-badge-err">{$_c['meta_wa_active']|default:'off'}</span>
                    {/if}
                </td>
            </tr>
        </table>
        <div style="margin-top:14px;">
            <a href="{$_url}plugin/metaWhatsApp_config" class="mwa-btn mwa-btn-gray mwa-btn-sm">
                <span class="glyphicon glyphicon-cog"></span> Edit Settings
            </a>
        </div>
    </div>
</div>

</div>{* col-md-6 *}

<div class="col-md-6">

<div class="mwa-card">
    <div class="mwa-card-hdr">
        <h4><span class="glyphicon glyphicon-wrench" style="color:#8b5cf6;"></span> Webhook Setup</h4>
    </div>
    <div class="mwa-card-body">
        <div class="mwa-alert mwa-alert-info">
            <span class="glyphicon glyphicon-info-sign"></span>
            Webhooks let Meta deliver real-time delivery receipts back to SpeedRadius. Optional but recommended.
        </div>

        <div class="mwa-step">
            <div class="mwa-step-num">1</div>
            <div class="mwa-step-body">
                <strong>Go to <a href="https://developers.facebook.com/apps/" target="_blank">Meta Developers</a> → Your App → WhatsApp → Configuration</strong>
                <p>Under <em>Webhook</em>, click <b>Edit</b>.</p>
            </div>
        </div>
        <div class="mwa-step">
            <div class="mwa-step-num">2</div>
            <div class="mwa-step-body">
                <strong>Callback URL</strong>
                <div class="mwa-code">https://{$smarty.server.HTTP_HOST}/?_route=plugin/metaWhatsApp_webhook</div>
            </div>
        </div>
        <div class="mwa-step">
            <div class="mwa-step-num">3</div>
            <div class="mwa-step-body">
                <strong>Verify Token</strong>
                <p>Use the value you set in <b>Settings → Webhook Verify Token</b> below.</p>
                <div class="mwa-code">{$_c['meta_wa_webhook_token']|default:'(not set yet)'}</div>
            </div>
        </div>
        <div class="mwa-step">
            <div class="mwa-step-num">4</div>
            <div class="mwa-step-body">
                <strong>Subscribe to: <code>messages</code></strong>
                <p>Enable the <em>messages</em> webhook field so delivery updates are received.</p>
            </div>
        </div>
    </div>
</div>

<div class="mwa-card">
    <div class="mwa-card-hdr">
        <h4><span class="glyphicon glyphicon-list-alt" style="color:#f59e0b;"></span> Recent Messages</h4>
        <a href="{$_url}plugin/metaWhatsApp_logs" class="mwa-btn mwa-btn-gray mwa-btn-sm">View All</a>
    </div>
    <div class="mwa-card-body" style="padding:0;overflow:auto;">
        <table class="mwa-table">
            <thead>
                <tr>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                {if $logs}
                    {foreach $logs as $log}
                        {if $log@iteration > 8}{break}{/if}
                    <tr>
                        <td style="font-family:monospace;">{$log['phone']}</td>
                        <td>
                            {if $log['status'] eq 'sent'}
                                <span class="mwa-badge-ok">&#10003; Sent</span>
                            {else}
                                <span class="mwa-badge-err" title="{$log['status_message']}">&#10007; Failed</span>
                            {/if}
                        </td>
                        <td style="color:#94a3b8;font-size:11px;">{$log['created_at']}</td>
                    </tr>
                    {/foreach}
                {else}
                    <tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:20px;">No messages yet</td></tr>
                {/if}
            </tbody>
        </table>
    </div>
</div>

</div>{* col-md-6 *}
</div>{* row *}
{/if}

{* ════════════════════════ SETTINGS ════════════════════════ *}
{if $menu == 'config'}
<div class="mwa-card">
    <div class="mwa-card-hdr">
        <h4><span class="glyphicon glyphicon-cog" style="color:#0f172a;"></span> Meta WhatsApp Cloud API — Settings</h4>
    </div>
    <div class="mwa-card-body">
        <form method="post" action="{$_url}plugin/metaWhatsApp_config" role="form">
            <div class="row">
                <div class="col-md-8">

                    <div class="mwa-fg">
                        <label>Phone Number ID <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="meta_wa_phone_id" class="mwa-input"
                               value="{$_c['meta_wa_phone_id']}"
                               placeholder="e.g. 123456789012345">
                        <div class="mwa-hint">
                            Found in <a href="https://developers.facebook.com/apps/" target="_blank">Meta Developers</a>
                            → Your App → WhatsApp → API Setup → Phone Number ID
                        </div>
                    </div>

                    <div class="mwa-fg">
                        <label>Permanent Access Token <span style="color:#ef4444;">*</span></label>
                        <input type="password" name="meta_wa_access_token" class="mwa-input"
                               value="{$_c['meta_wa_access_token']}"
                               placeholder="EAAxxxxxxxxxxxxxxx...">
                        <div class="mwa-hint">
                            Generate a <b>permanent token</b> via
                            <a href="https://business.facebook.com/settings/system-users" target="_blank">Meta Business → System Users</a>.
                            Do NOT use the temporary token from the API setup page.
                        </div>
                    </div>

                    <div class="mwa-fg">
                        <label>API Version</label>
                        <input type="text" name="meta_wa_api_version" class="mwa-input"
                               value="{$_c['meta_wa_api_version']|default:'v19.0'}"
                               placeholder="v19.0">
                        <div class="mwa-hint">Leave as <code>v19.0</code> unless Meta instructs you to upgrade.</div>
                    </div>

                    <div class="mwa-fg">
                        <label>Webhook Verify Token</label>
                        <input type="text" name="meta_wa_webhook_token" class="mwa-input"
                               value="{$_c['meta_wa_webhook_token']}"
                               placeholder="any_random_secret_string">
                        <div class="mwa-hint">A secret string you choose. Paste the same value in Meta Developers → Webhook → Verify Token.</div>
                    </div>

                    <div class="mwa-fg">
                        <label>Gateway Active</label>
                        <select name="meta_wa_active" class="mwa-input mwa-select">
                            <option value="off"  {if $_c['meta_wa_active'] != 'meta'}selected{/if}>off — Disabled (system uses other WA gateway)</option>
                            <option value="meta" {if $_c['meta_wa_active'] == 'meta'}selected{/if}>meta — Active (all WhatsApp notifications go via Meta)</option>
                        </select>
                        <div class="mwa-hint">
                            Set to <b>meta</b> to make this the active WhatsApp gateway.
                            If you also have Go WhatsApp Gateway enabled, set it to <b>off</b> to avoid double-sending.
                        </div>
                    </div>

                    <button type="submit" class="mwa-btn mwa-btn-green">
                        <span class="glyphicon glyphicon-floppy-disk"></span> Save Configuration
                    </button>
                </div>

                <div class="col-md-4">
                    <div class="mwa-alert mwa-alert-info" style="margin-top:0;">
                        <div>
                            <strong style="display:block;margin-bottom:6px;">&#128073; How to get your credentials</strong>
                            <ol style="padding-left:18px;font-size:12px;line-height:1.8;margin:0;">
                                <li>Go to <a href="https://developers.facebook.com/apps/" target="_blank">developers.facebook.com</a></li>
                                <li>Create or select a Meta App (type: Business)</li>
                                <li>Add <b>WhatsApp</b> product</li>
                                <li>Under <b>API Setup</b> copy the <b>Phone Number ID</b></li>
                                <li>Go to <b>Business Settings → System Users</b></li>
                                <li>Create a System User, assign WhatsApp permission</li>
                                <li>Generate a <b>Never-expiring</b> token</li>
                                <li>Add a real phone number and complete business verification</li>
                            </ol>
                        </div>
                    </div>
                    <div class="mwa-alert mwa-alert-warn">
                        <div>
                            <strong>&#9888;  Template Messages</strong>
                            <p style="font-size:12px;margin:4px 0 0;">
                                Meta requires pre-approved <b>message templates</b> for proactive notifications
                                (outside a 24-hour customer-initiated window).
                                Create templates in the <a href="https://business.facebook.com/wa/manage/message-templates/" target="_blank">WhatsApp Manager</a>
                                or this plugin will send free-form text which works once a customer has messaged you first.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
{/if}

{* ════════════════════════ BROADCAST ════════════════════════ *}
{if $menu == 'broadcast'}
<div class="row">
<div class="col-md-7">
<div class="mwa-card">
    <div class="mwa-card-hdr">
        <h4><span class="glyphicon glyphicon-send" style="color:#00a884;"></span> Broadcast WhatsApp Message</h4>
    </div>
    <div class="mwa-card-body">
        <div class="mwa-alert mwa-alert-warn">
            <span class="glyphicon glyphicon-warning-sign"></span>
            Messages are sent one-by-one. Large batches may take time. Meta rate limits apply.
        </div>
        <div class="mwa-fg">
            <label>Target Customers</label>
            <select id="mwa_target" class="mwa-input mwa-select">
                <option value="active">Active subscribers only</option>
                <option value="all">All customers (including expired)</option>
            </select>
        </div>
        <div class="mwa-fg">
            <label>Message <span style="color:#ef4444;">*</span></label>
            <textarea id="mwa_bc_msg" class="mwa-input" rows="6"
                placeholder="Type your broadcast message here...
You can use [[name]], [[username]], [[plan]], [[price]]"></textarea>
            <div class="mwa-hint">Supported placeholders: <code>[[name]]</code> <code>[[username]]</code> <code>[[plan]]</code> <code>[[price]]</code></div>
        </div>
        <button class="mwa-btn mwa-btn-orange mwa-btn-block" onclick="mwaBroadcast()">
            <span class="glyphicon glyphicon-send"></span> Send Broadcast
        </button>
        <div id="mwa_bc_result" style="margin-top:14px;display:none;" class="mwa-alert"></div>
        <div id="mwa_bc_progress" style="display:none;margin-top:14px;">
            <div style="background:#f1f5f9;border-radius:8px;overflow:hidden;height:8px;">
                <div style="background:linear-gradient(90deg,#00a884,#128c7e);height:100%;width:0%;transition:width .3s;" id="mwa_bc_bar"></div>
            </div>
            <div style="font-size:12px;color:#64748b;margin-top:6px;" id="mwa_bc_bar_text">Sending...</div>
        </div>
    </div>
</div>
</div>
<div class="col-md-5">
    <div class="mwa-card">
        <div class="mwa-card-hdr"><h4><span class="glyphicon glyphicon-info-sign" style="color:#3b82f6;"></span> Tips</h4></div>
        <div class="mwa-card-body" style="font-size:13px;color:#374151;line-height:1.8;">
            <ul style="padding-left:18px;margin:0;">
                <li>Only customers with a saved phone number will receive the message.</li>
                <li>Meta requires customers to have <b>messaged your business first</b> within the past 24 hours for free-form text — otherwise use pre-approved templates.</li>
                <li>For bulk campaigns, consider using <b>Message Templates</b> approved in WhatsApp Manager.</li>
                <li>Keep messages concise — very long messages may be truncated.</li>
            </ul>
        </div>
    </div>
</div>
</div>
{/if}

{* ════════════════════════ LOGS ════════════════════════ *}
{if $menu == 'logs'}
<div class="mwa-card">
    <div class="mwa-card-hdr">
        <h4><span class="glyphicon glyphicon-list-alt" style="color:#8b5cf6;"></span> WhatsApp Message Logs
            <small style="font-size:11px;font-weight:400;color:#64748b;">({$total} total)</small>
        </h4>
    </div>
    <div style="overflow:auto;">
        <table class="mwa-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Time</th>
                    <th>Phone</th>
                    <th>Message</th>
                    <th>WAMID</th>
                    <th>Status</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                {if $logs}
                    {foreach $logs as $log}
                    <tr>
                        <td style="color:#94a3b8;">{$log['id']}</td>
                        <td style="font-size:11px;color:#94a3b8;white-space:nowrap;">{$log['created_at']}</td>
                        <td style="font-family:monospace;">{$log['phone']}</td>
                        <td style="max-width:280px;word-break:break-word;">{$log['message']|truncate:80}</td>
                        <td style="font-family:monospace;font-size:11px;color:#64748b;">{$log['message_id']|default:'—'|truncate:24}</td>
                        <td>
                            {if $log['status'] eq 'sent'}
                                <span class="mwa-badge-ok">&#10003; Sent</span>
                            {else}
                                <span class="mwa-badge-err">&#10007; Failed</span>
                            {/if}
                        </td>
                        <td style="font-size:11px;color:#94a3b8;max-width:180px;word-break:break-word;">{$log['status_message']}</td>
                    </tr>
                    {/foreach}
                {else}
                    <tr><td colspan="7" style="text-align:center;padding:30px;color:#94a3b8;">No messages logged yet</td></tr>
                {/if}
            </tbody>
        </table>
    </div>
    {if $pages > 1}
    <div style="padding:14px 20px;border-top:1px solid #e2e8f0;display:flex;gap:6px;flex-wrap:wrap;">
        {section name=p loop=$pages}
            {assign var=pn value=$smarty.section.p.index+1}
            <a href="{$_url}plugin/metaWhatsApp_logs&page={$pn}"
               class="mwa-btn mwa-btn-sm {if $pn == $page}mwa-btn-blue{else}mwa-btn-gray{/if}">{$pn}</a>
        {/section}
    </div>
    {/if}
</div>
{/if}

</div>{* .mwa-wrap *}

<script>
var MWA_URL_TEST      = '{$_url}plugin/metaWhatsApp_test';
var MWA_URL_BROADCAST = '{$_url}plugin/metaWhatsApp_broadcast';
</script>
<script>
{literal}
function mwaSendTest() {
    var phone = document.getElementById('mwa_test_phone').value.trim();
    var msg   = document.getElementById('mwa_test_msg').value.trim();
    var res   = document.getElementById('mwa_test_result');

    if (!phone || !msg) {
        res.className = 'mwa-alert mwa-alert-warn';
        res.innerHTML = '<span class="glyphicon glyphicon-warning-sign"></span> Please enter both phone and message.';
        res.style.display = 'flex';
        return;
    }

    res.className = 'mwa-alert mwa-alert-info';
    res.innerHTML = '<span class="glyphicon glyphicon-refresh"></span> Sending...';
    res.style.display = 'flex';

    $.post(MWA_URL_TEST, {phone: phone, message: msg}, function(d) {
        if (d.success) {
            res.className = 'mwa-alert mwa-alert-ok';
            res.innerHTML = '<span class="glyphicon glyphicon-ok"></span> <b>Message sent!</b> WAMID: ' + (d.wamid || 'n/a');
        } else {
            res.className = 'mwa-alert mwa-alert-warn';
            res.innerHTML = '<span class="glyphicon glyphicon-remove"></span> <b>Failed:</b> ' + (d.message || 'Unknown error');
        }
    }, 'json').fail(function() {
        res.className = 'mwa-alert mwa-alert-warn';
        res.innerHTML = 'Request failed. Check console.';
    });
}

function mwaBroadcast() {
    var target = document.getElementById('mwa_target').value;
    var msg    = document.getElementById('mwa_bc_msg').value.trim();
    var res    = document.getElementById('mwa_bc_result');
    var prog   = document.getElementById('mwa_bc_progress');

    if (!msg) {
        res.className = 'mwa-alert mwa-alert-warn';
        res.innerHTML = 'Message cannot be empty.';
        res.style.display = 'flex';
        return;
    }

    if (!confirm('Send this message to all ' + target + ' customers?')) return;

    res.style.display = 'none';
    prog.style.display = 'block';
    document.getElementById('mwa_bc_bar').style.width = '30%';
    document.getElementById('mwa_bc_bar_text').textContent = 'Sending...';

    $.post(MWA_URL_BROADCAST, {target: target, message: msg}, function(d) {
        document.getElementById('mwa_bc_bar').style.width = '100%';
        prog.style.display = 'none';

        if (d.success) {
            res.className = 'mwa-alert mwa-alert-ok';
            res.innerHTML = '<span class="glyphicon glyphicon-ok"></span> <b>Broadcast complete!</b> Sent: <b>' + d.sent + '</b> &nbsp; Failed: <b>' + d.failed + '</b>';
        } else {
            res.className = 'mwa-alert mwa-alert-warn';
            res.innerHTML = '<b>Error:</b> ' + (d.message || 'Unknown error');
        }
        res.style.display = 'flex';
    }, 'json').fail(function() {
        prog.style.display = 'none';
        res.className = 'mwa-alert mwa-alert-warn';
        res.innerHTML = 'Request failed. Check error logs.';
        res.style.display = 'flex';
    });
}
{/literal}
</script>

{include file="sections/footer.tpl"}
