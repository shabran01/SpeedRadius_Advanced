{include file="sections/header.tpl"}

<style>
.setup-container {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 20px 0;
}

.setup-header {
    background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(26, 115, 232, 0.3);
    position: relative;
    overflow: hidden;
}

.setup-header::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
}

.setup-header h1, .setup-header p { position: relative; z-index: 2; margin: 0; }
.setup-header h1 { font-size: 22px; font-weight: 600; margin-bottom: 8px; }
.setup-header p  { font-size: 13px; opacity: 0.9; }

.setup-header .header-icon {
    position: absolute; right: 30px; top: 50%;
    transform: translateY(-50%);
    font-size: 60px; opacity: 0.15;
}

.card-modern {
    background: white;
    border-radius: 14px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 24px;
}

.card-modern .card-head {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 16px 22px;
    font-size: 14px;
    font-weight: 600;
}

.card-modern .card-body {
    padding: 24px;
}

.form-group label {
    font-weight: 600;
    font-size: 12px;
    color: #495057;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
    display: block;
}

.form-control-modern {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 14px;
    color: #495057;
    transition: border-color 0.25s;
    width: 100%;
}

.form-control-modern:focus {
    outline: none;
    border-color: #1a73e8;
    box-shadow: 0 0 0 3px rgba(26,115,232,0.12);
}

.hint-text {
    font-size: 11px;
    color: #6c757d;
    margin-top: 4px;
}

.steps-legend {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.step-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
}

.step-num {
    min-width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1a73e8, #0d47a1);
    color: white;
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

.step-desc {
    font-size: 13px;
    color: #495057;
    padding-top: 4px;
    line-height: 1.5;
}

.btn-run {
    background: linear-gradient(135deg, #1a73e8, #0d47a1);
    color: white;
    border: none;
    padding: 12px 32px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-run:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(26,115,232,0.35);
}

/* Results */
.result-list { display: flex; flex-direction: column; gap: 10px; }

.result-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 13px;
}

.result-item.ok {
    background: #f0fff4;
    border-left: 4px solid #28a745;
}

.result-item.fail {
    background: #fff5f5;
    border-left: 4px solid #dc3545;
}

.result-icon { font-size: 16px; }
.result-step { font-weight: 600; color: #343a40; min-width: 280px; }
.result-msg  { color: #6c757d; }

.result-item.ok .result-icon  { color: #28a745; }
.result-item.fail .result-icon { color: #dc3545; }

.summary-bar {
    display: flex;
    gap: 16px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.summary-pill {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}

.pill-ok   { background: #d4edda; color: #155724; }
.pill-fail { background: #f8d7da; color: #721c24; }
</style>

<div class="setup-container">
    <div class="row">
        <div class="col-sm-12">

            <!-- Header -->
            <div class="setup-header">
                <i class="fa fa-server header-icon"></i>
                <h1><i class="fa fa-magic"></i> Hotspot Server Setup</h1>
                <p>Automate MikroTik bridge, port assignment, IP address, and DHCP server configuration in one click</p>
            </div>

            <div class="row">
                <!-- Left: Form -->
                <div class="col-md-7">
                    <div class="card-modern">
                        <div class="card-head"><i class="fa fa-sliders"></i> Configuration</div>
                        <div class="card-body">
                            <form method="POST" action="{$_url}hotspot_server_setup" id="setup-form">
                                <input type="hidden" name="action" value="run_setup">

                                <!-- Router -->
                                <div class="form-group" style="margin-bottom:18px;">
                                    <label>Router</label>
                                    <select name="router_id" class="form-control-modern" required>
                                        <option value="">-- Select Router --</option>
                                        {foreach $routers as $r}
                                            <option value="{$r.id}">{$r.name} ({$r.ip_address})</option>
                                        {/foreach}
                                    </select>
                                </div>

                                <!-- Bridge name -->
                                <div class="form-group" style="margin-bottom:18px;">
                                    <label>Bridge Name</label>
                                    <input type="text" name="bridge_name" class="form-control-modern"
                                           value="Hotspot-Server" maxlength="15"
                                           pattern="[A-Za-z0-9_\-]+" required>
                                    <div class="hint-text">Letters, numbers, dash or underscore only (max 15 chars)</div>
                                </div>

                                <!-- Interfaces -->
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group" style="margin-bottom:18px;">
                                            <label>Interface 1</label>
                                            <input type="text" name="iface1" class="form-control-modern"
                                                   value="ether2" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group" style="margin-bottom:18px;">
                                            <label>Interface 2</label>
                                            <input type="text" name="iface2" class="form-control-modern"
                                                   value="ether3" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- IP Address -->
                                <div class="form-group" style="margin-bottom:18px;">
                                    <label>IP Address / Prefix</label>
                                    <input type="text" name="ip_address" class="form-control-modern"
                                           value="10.0.0.1/22"
                                           pattern="\d{1,3}(\.\d{1,3}){3}\/\d{1,2}" required>
                                    <div class="hint-text">Gateway IP on the bridge. DHCP pool is derived automatically. Example: 10.0.0.1/22</div>
                                </div>

                                <!-- DNS -->
                                <div class="form-group" style="margin-bottom:24px;">
                                    <label>DNS Servers</label>
                                    <input type="text" name="dns_servers" class="form-control-modern"
                                           value="8.8.8.8,8.8.4.4">
                                    <div class="hint-text">Comma-separated, pushed to DHCP clients</div>
                                </div>

                                <button type="submit" class="btn-run" id="run-btn">
                                    <i class="fa fa-play-circle"></i> Run Setup
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right: Steps legend -->
                <div class="col-md-5">
                    <div class="card-modern">
                        <div class="card-head"><i class="fa fa-list-ol"></i> What Will Be Created</div>
                        <div class="card-body">
                            <div class="steps-legend">
                                <div class="step-item">
                                    <div class="step-num">1</div>
                                    <div class="step-desc"><strong>Bridge</strong> — Creates a new bridge interface (e.g. Hotspot-Server)</div>
                                </div>
                                <div class="step-item">
                                    <div class="step-num">2</div>
                                    <div class="step-desc"><strong>Bridge Ports</strong> — Adds ether2 and ether3 as bridge slave ports</div>
                                </div>
                                <div class="step-item">
                                    <div class="step-num">3</div>
                                    <div class="step-desc"><strong>IP Address</strong> — Assigns 10.0.0.1/22 to the bridge interface</div>
                                </div>
                                <div class="step-item">
                                    <div class="step-num">4a</div>
                                    <div class="step-desc"><strong>DHCP Pool</strong> — Creates address pool (10.0.0.2–10.0.3.254 for /22)</div>
                                </div>
                                <div class="step-item">
                                    <div class="step-num">4b</div>
                                    <div class="step-desc"><strong>DHCP Network</strong> — Sets gateway &amp; DNS for the subnet</div>
                                </div>
                                <div class="step-item">
                                    <div class="step-num">4c</div>
                                    <div class="step-desc"><strong>DHCP Server</strong> — Enables DHCP server on the bridge (equivalent to DHCP Setup wizard)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results -->
            {if $executed}
                <div class="card-modern">
                    <div class="card-head"><i class="fa fa-terminal"></i> Setup Results</div>
                    <div class="card-body">
                        {if count($results) > 0}
                            {assign var="ok_count" value=0}
                            {assign var="fail_count" value=0}
                            {foreach $results as $r}
                                {if $r.ok}{assign var="ok_count" value=$ok_count+1}{else}{assign var="fail_count" value=$fail_count+1}{/if}
                            {/foreach}
                            <div class="summary-bar">
                                <span class="summary-pill pill-ok"><i class="fa fa-check-circle"></i> {$ok_count} Passed</span>
                                {if $fail_count > 0}
                                    <span class="summary-pill pill-fail"><i class="fa fa-times-circle"></i> {$fail_count} Failed</span>
                                {/if}
                            </div>
                            <div class="result-list">
                                {foreach $results as $r}
                                    <div class="result-item {if $r.ok}ok{else}fail{/if}">
                                        <span class="result-icon">
                                            {if $r.ok}<i class="fa fa-check-circle"></i>{else}<i class="fa fa-times-circle"></i>{/if}
                                        </span>
                                        <span class="result-step">{$r.step}</span>
                                        <span class="result-msg">{$r.msg}</span>
                                    </div>
                                {/foreach}
                            </div>
                        {else}
                            <p class="text-muted">No results to show.</p>
                        {/if}
                    </div>
                </div>
            {/if}

        </div>
    </div>
</div>

<script>
document.getElementById('setup-form').addEventListener('submit', function() {
    var btn = document.getElementById('run-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Running...';
});
</script>

{include file="sections/footer.tpl"}
