{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel">
            <div class="panel-heading">
                {Lang::T('Routers')}
                <div class="float-right">
                    <button id="darkModeToggle" class="btn btn-sm btn-outline-light">
                        <i class="fa fa-moon-o"></i>
                    </button>
                </div>
            </div>
            <div class="panel-body">
                 <div class="md-whiteframe-z1 mb20 text-center" style="padding: 15px">
                     <div class="col-md-8">
                         <form id="site-search" method="post" action="{$_url}routers/list/">
                             <div class="input-group">
                                 <div class="input-group-addon">
                                     <span class="fa fa-search"></span>
                                 </div>
                                 <input type="text" name="name" class="form-control"
                                     placeholder="{Lang::T('Search by Name')}...">
                                 <div class="input-group-btn">
                                     <button class="btn-modern btn-search" type="submit">
                                         <i class="fa fa-search"></i> Search
                                     </button>
                                 </div>
                             </div>
                         </form>
                     </div>
                     <div class="col-md-4">
                         <a href="{$_url}routers/add" class="btn-modern btn-add-router">
                             <i class="fa fa-plus"></i> New Router
                         </a>
                     </div>&nbsp;
                 </div>
                 <div class="md-whiteframe-z1 mb20" style="padding: 15px">
                     <div class="row">
                         <div class="col-md-4">
                             <button id="refreshAllStatuses" class="btn-modern btn-info">
                                 <i class="fa fa-refresh"></i> Refresh All Statuses
                             </button>
                         </div>
                         <div class="col-md-4">
                             <div class="bulk-actions" style="display: none;">
                                 <select id="bulkAction" class="form-control" style="display: inline-block; width: auto;">
                                     <option value="">Bulk Actions</option>
                                     <option value="enable">Enable Selected</option>
                                     <option value="disable">Disable Selected</option>
                                     <option value="delete">Delete Selected</option>
                                     <option value="reboot">Reboot Selected</option>
                                 </select>
                                 <button id="applyBulkAction" class="btn-modern btn-warning" style="margin-left: 10px;">
                                     Apply
                                 </button>
                             </div>
                         </div>
                         <div class="col-md-4 text-right">
                             <button id="exportCSV" class="btn-modern btn-success">
                                 <i class="fa fa-download"></i> Export to CSV
                             </button>
                         </div>
                     </div>
                 </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-condensed">
                         <thead>
                             <tr>
                                 <th><input type="checkbox" id="selectAll"></th>
                                 <th>{Lang::T('Router Name')}</th>
                                 <th>{Lang::T('IP Address')}</th>
                                 <th>{Lang::T('Status')}</th>
                                 <th>{Lang::T('Last Seen')}</th>
                                 <th>{Lang::T('Uptime')}</th>
                                 <th>{Lang::T('Free Memory')}</th>
                                 <th>{Lang::T('CPU Load')}</th>
                                 <th>{Lang::T('Manage')}</th>
                                 <th>ID</th>
                                 <th>{Lang::T('Download')}</th>
                             </tr>
                         </thead>
                         <tbody>
                             {foreach $d as $ds}
                                 <tr {if $ds['enabled'] != 1}class="danger" title="disabled" {/if}>
                                     <td><input type="checkbox" class="router-checkbox" value="{$ds['id']}"></td>
                                     <td>{$ds['name']}</td>
                                     <td>{$ds['ip_address']}</td>
                                     <td class="router-status" data-router-id="{$ds['id']}"></td>
                                     <td class="router-last-seen" data-router-id="{$ds['id']}">Checking...</td>
                                     <td class="router-uptime" data-router-id="{$ds['id']}"></td>
                                     <td class="router-used-memory" data-router-id="{$ds['id']}"></td>
                                     <td class="router-cpu-load" data-router-id="{$ds['id']}"></td>
                                     <td>
                                         <a href="{$_url}routers/edit/{$ds['id']}" class="btn-modern btn-edit">
                                             <i class="fa fa-edit"></i> Edit
                                         </a>
                                         <a href="{$_url}routers/delete/{$ds['id']}" id="{$ds['id']}"
                                             onclick="return confirm('{Lang::T('Delete')}?')"
                                             class="btn-modern btn-delete">
                                             <i class="fa fa-trash"></i>
                                         </a>
                                         <button class="btn-modern btn-reboot btn-reboot" data-router-id="{$ds['id']}">
                                             <i class="fa fa-refresh"></i> Reboot
                                         </button>
                                     </td>
                                     <td>{$ds['id']}</td>
                                     <td>
                                         <form action="{$_url}routers/download" method="post" style="display:inline;">
                                             <input type="hidden" name="router_id" value="{$ds['id']}">
                                             <input type="hidden" name="router_name" value="{$ds['name']}">
                                             <button type="submit" class="btn-modern btn-download">
                                                 <i class="fa fa-download"></i> Download
                                             </button>
                                         </form>
                                     </td>
                                 </tr>
                             {/foreach}
                         </tbody>
                    </table>
                </div>
                {include file="pagination.tpl"}

                <div class="deployment-card">
                    <div class="deployment-header">
                        <i class="fa fa-terminal deployment-icon"></i>
                        <div>
                            <h4 class="deployment-title">Deployment</h4>
                            <p class="deployment-subtitle">How to deploy and re-run the script on any MikroTik router.</p>
                        </div>
                    </div>

                    <!-- Step 1 -->
                    <div class="deployment-step">
                        <div class="step-header">
                            <span class="step-num">1</span>
                            <div>
                                <span class="step-title">Reset Router to Default Configuration</span>
                                <span class="step-subtitle">Required before first-time setup on any router</span>
                            </div>
                        </div>
                        <p class="deployment-desc">Before running the script, reset the router to factory defaults so there are no conflicting settings. In Winbox or the MikroTik terminal run:</p>
                        <div class="deployment-code-block" style="margin-bottom:10px;">
                            <div class="code-label">MikroTik Terminal</div>
                            <div class="code-body">
                                <code id="resetCmd">/system reset-configuration no-defaults=no skip-backup=yes</code>
                                <button class="copy-btn" onclick="copyCmd('resetCmd','resetIcon','resetText')" title="Copy command">
                                    <i class="fa fa-copy" id="resetIcon"></i>
                                    <span id="resetText">Copy</span>
                                </button>
                            </div>
                        </div>
                        <div class="step-note">
                            <i class="fa fa-info-circle" style="color:#4f46e5;margin-right:6px;"></i>
                            In Winbox, go to <strong>System &rarr; Reset Configuration</strong>. When the dialog appears — <strong>do not tick any checkboxes</strong> (leave Keep users, CAPS Mode, No Default Configuration, and Do Not Backup all unchecked) — then click <strong>"Reset Configuration"</strong>. The router will reboot. Wait for it to fully restart before continuing to Step 2.
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="deployment-step">
                        <div class="step-header">
                            <span class="step-num">2</span>
                            <div>
                                <span class="step-title">Run the Hotspot Setup Script</span>
                                <span class="step-subtitle">Option A &mdash; Remote fetch <em>(recommended)</em></span>
                            </div>
                        </div>
                        <p class="deployment-desc">Paste this command into your MikroTik Terminal to download and apply the hotspot configuration:</p>
                        <div class="deployment-code-block">
                            <div class="code-label">MikroTik Terminal</div>
                            <div class="code-body">
                                <code id="deployCmd">/tool fetch url="{$_detected_url}/provision/hotspot.rsc" mode=https dst-path=hotspot.rsc; /import file-name=hotspot.rsc</code>
                                <button class="copy-btn" onclick="copyCmd('deployCmd','copyIcon','copyText')" title="Copy command">
                                    <i class="fa fa-copy" id="copyIcon"></i>
                                    <span id="copyText">Copy</span>
                                </button>
                            </div>
                        </div>
                        <div class="step-note">
                            <i class="fa fa-check-circle" style="color:#16a34a;margin-right:6px;"></i>
                            The script is <strong>idempotent</strong> — it cleans up any previous partial config before applying, so it is safe to re-run on any router state.
                        </div>
                    </div>
                </div>

                <div class="deployment-card" style="margin-top:20px;">
                    <div class="deployment-header">
                        <i class="fa fa-shield deployment-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);"></i>
                        <div>
                            <h4 class="deployment-title">Device Mode Check</h4>
                            <p class="deployment-subtitle">Some RouterOS features are restricted by device mode. Check and fix it here.</p>
                        </div>
                    </div>

                    <div class="deployment-step">
                        <div class="step-header">
                            <span class="step-num">1</span>
                            <div>
                                <span class="step-title">Check Current Mode</span>
                            </div>
                        </div>
                        <div class="deployment-code-block">
                            <div class="code-label">MikroTik Terminal</div>
                            <div class="code-body">
                                <code id="modeCheckCmd">/system/device-mode/print</code>
                                <button class="copy-btn" onclick="copyCmd('modeCheckCmd','modeCheckIcon','modeCheckText')" title="Copy command">
                                    <i class="fa fa-copy" id="modeCheckIcon"></i>
                                    <span id="modeCheckText">Copy</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="deployment-step">
                        <div class="step-header">
                            <span class="step-num">2</span>
                            <div>
                                <span class="step-title">Switch to Advanced Mode</span>
                                <span class="step-subtitle">If features are restricted — use the command for your RouterOS version</span>
                            </div>
                        </div>
                        <div class="deployment-code-block" style="margin-bottom:8px;">
                            <div class="code-label">RouterOS 7.x</div>
                            <div class="code-body">
                                <code id="modeUpdateCmd">/system/device-mode/update mode=advanced</code>
                                <button class="copy-btn" onclick="copyCmd('modeUpdateCmd','modeUpdateIcon','modeUpdateText')" title="Copy command">
                                    <i class="fa fa-copy" id="modeUpdateIcon"></i>
                                    <span id="modeUpdateText">Copy</span>
                                </button>
                            </div>
                        </div>
                        <div class="deployment-code-block">
                            <div class="code-label">Older Versions (alternative)</div>
                            <div class="code-body">
                                <code id="modeUpdateAltCmd">/system/device-mode/update advanced=yes</code>
                                <button class="copy-btn" onclick="copyCmd('modeUpdateAltCmd','modeUpdateAltIcon','modeUpdateAltText')" title="Copy command">
                                    <i class="fa fa-copy" id="modeUpdateAltIcon"></i>
                                    <span id="modeUpdateAltText">Copy</span>
                                </button>
                            </div>
                        </div>
                        <div class="step-note">
                            <i class="fa fa-exclamation-triangle" style="color:#d97706;margin-right:6px;"></i>
                            <strong>Physical confirmation required:</strong> After running the update command, <strong>power off</strong> the router completely, then <strong>power it back on</strong>. The router will reboot and apply the new device mode. Cannot be confirmed remotely — this is a security measure by MikroTik.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}

<style>
    @import url('https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&display=swap');

    .loader {
        display: inline-block;
        animation: loading 1s infinite;
        margin: 0;
    }

    @keyframes loading {
        0% { content: "." }
        33% { content: ".." }
        66% { content: "..." }
    }

    .table {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        overflow: hidden;
    }

    .table thead th {
        background: linear-gradient(135deg, #1a237e, #303f9f);
        color: white;
        font-family: 'Rajdhani', sans-serif;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 10px;
        border: none;
        font-size: 12px;
    }

    .table tbody td {
        padding: 12px 10px;
        font-size: 13px;
        vertical-align: middle;
    }

    .router-uptime {
        color: #28a745;
        font-weight: bold;
        font-family: 'Rajdhani', sans-serif;
        text-shadow: 1px 1px 1px rgba(0,0,0,0.1);
    }

    .router-used-memory {
        color: #fd7e14;
        font-weight: bold;
        font-family: 'Rajdhani', sans-serif;
        text-shadow: 1px 1px 1px rgba(0,0,0,0.1);
    }

    .router-cpu-load {
        color: #d63384;
        font-weight: bold;
        font-family: 'Rajdhani', sans-serif;
        text-shadow: 1px 1px 1px rgba(0,0,0,0.1);
    }

    .router-status {
        font-weight: bold;
        font-family: 'Rajdhani', sans-serif;
    }

    .router-status .status {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .router-status .online {
        background: linear-gradient(135deg, #1eb980, #28a745);
        color: white;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    .router-status .offline {
        background: linear-gradient(135deg, #dc3545, #ff4444);
        color: white;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    .btn {
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-family: 'Rajdhani', sans-serif;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin: 2px;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .btn:active {
        transform: translateY(0);
    }

    .btn-info {
        background: linear-gradient(45deg, #2196F3, #4CAF50);
        color: white;
    }

    .btn-danger {
        background: linear-gradient(45deg, #f44336, #ff5252);
        color: white;
    }

    .btn-warning {
        background: linear-gradient(45deg, #ff9800, #ffc107);
        color: white;
    }

    .btn-success {
        background: linear-gradient(45deg, #4CAF50, #8BC34A);
        color: white;
    }

    /* Modern compact buttons */
    .btn-modern {
        padding: 8px 16px;
        border-radius: 20px;
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 12px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin: 2px;
        cursor: pointer;
        min-width: 80px;
        justify-content: center;
    }

    .btn-edit {
        background: linear-gradient(135deg, #17a2b8, #20c997);
        color: white;
    }

    .btn-edit:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(23, 162, 184, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-delete {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }

    .btn-delete:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-reboot {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
        color: white;
    }

    .btn-reboot:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
        color: white;
    }

    .btn-download {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .btn-download:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        color: white;
    }

    .btn-search {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 8px 16px;
        font-size: 12px;
        min-width: 80px;
    }

    .btn-search:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-add-router {
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        padding: 10px 20px;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        justify-content: center;
        border-radius: 20px;
    }

    .btn-add-router:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        color: white;
        text-decoration: none;
    }

    .panel {
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        overflow: hidden;
    }

    .panel-heading {
        background: linear-gradient(45deg, #1a237e, #303f9f);
        color: white;
        font-family: 'Rajdhani', sans-serif;
        font-weight: 700;
        font-size: 1.5em;
        text-transform: uppercase;
        letter-spacing: 2px;
        padding: 20px;
        border-bottom: none;
    }

    /* Dark mode styles */
    body.dark-mode {
        background: #1a1a1a;
        color: #ffffff;
    }

    body.dark-mode .panel {
        background: #2d2d2d;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    body.dark-mode .table {
        background: rgba(45, 45, 45, 0.9);
        color: #ffffff;
    }

    body.dark-mode .table tbody tr {
        border-color: #404040;
    }

    body.dark-mode .table tbody td {
        color: #ffffff;
    }

    /* Animated stats */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .stats-highlight {
        animation: pulse 2s infinite;
    }

    /* Hover effect for rows */
    .table tbody tr {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        position: relative;
        z-index: 1;
    }

    /* Loading animation */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive design fixes */
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .btn-modern {
            font-size: 11px;
            padding: 6px 12px;
            margin: 1px;
            min-width: 70px;
        }

        .btn-search {
            padding: 8px 16px;
            font-size: 13px;
            min-width: 90px;
        }

        .btn-add-router {
            padding: 10px 20px;
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .md-whiteframe-z1 .col-md-8,
        .md-whiteframe-z1 .col-md-4 {
            width: 100%;
            margin-bottom: 10px;
        }

        .btn-search {
            padding: 6px 12px;
            font-size: 11px;
            min-width: 70px;
        }

        .btn-add-router {
            padding: 8px 16px;
            font-size: 12px;
        }
    }

    /* Deployment Card */
    .deployment-card {
        margin-top: 24px;
        background: #f8faff;
        border: 1px solid #d0d7f0;
        border-left: 4px solid #4f46e5;
        border-radius: 10px;
        padding: 20px 24px;
    }

    .deployment-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 14px;
    }

    .deployment-icon {
        font-size: 22px;
        color: #4f46e5;
        background: #ede9fe;
        padding: 10px;
        border-radius: 8px;
    }

    .deployment-title {
        margin: 0 0 2px;
        font-size: 16px;
        font-weight: 700;
        color: #1e1b4b;
    }

    .deployment-subtitle {
        margin: 0;
        font-size: 13px;
        color: #64748b;
    }

    .deployment-option {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .option-badge {
        background: #4f46e5;
        color: white;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        letter-spacing: 0.5px;
    }

    .option-label {
        font-size: 14px;
        font-weight: 600;
        color: #334155;
    }

    .deployment-desc {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 10px;
    }

    .deployment-code-block {
        background: #1e1b4b;
        border-radius: 8px;
        overflow: hidden;
    }

    .code-label {
        background: #312e81;
        color: #a5b4fc;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        padding: 6px 14px;
    }

    .code-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        gap: 12px;
    }

    .code-body code {
        font-family: 'Courier New', 'SF Mono', monospace;
        font-size: 13px;
        color: #a5f3fc;
        background: none;
        padding: 0;
        word-break: break-all;
        flex: 1;
    }

    .copy-btn {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 13px;
        cursor: pointer;
        transition: background 0.2s;
    }

    .copy-btn:hover {
        background: rgba(255,255,255,0.22);
    }

    .copy-btn.copied {
        background: #16a34a;
        border-color: #16a34a;
    }

    /* Deployment Steps */
    .deployment-step {
        background: #f8faff;
        border: 1px solid #e2e8f8;
        border-radius: 8px;
        padding: 16px 18px;
        margin-bottom: 12px;
    }

    .step-header {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 10px;
    }

    .step-num {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 800;
        margin-top: 1px;
    }

    .step-title {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: #1e1b4b;
        line-height: 1.3;
    }

    .step-subtitle {
        display: block;
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }

    .step-note {
        margin-top: 10px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 10px 14px;
        font-size: 13px;
        color: #334155;
        line-height: 1.6;
    }
</style>

<div class="loading-overlay">
    <div class="loading-spinner"></div>
</div>

<script>
function copyCmd(codeId, iconId, textId) {
    var cmd = document.getElementById(codeId).innerText;
    var btn = document.getElementById(iconId).closest('.copy-btn');
    var icon = document.getElementById(iconId);
    var text = document.getElementById(textId);

    function showCopied() {
        btn.classList.add('copied');
        icon.className = 'fa fa-check';
        text.innerText = 'Copied!';
        setTimeout(function() {
            btn.classList.remove('copied');
            icon.className = 'fa fa-copy';
            text.innerText = 'Copy';
        }, 2000);
    }

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(cmd).then(showCopied);
    } else {
        var ta = document.createElement('textarea');
        ta.value = cmd;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        showCopied();
    }
}

function copyDeployCmd() { copyCmd('deployCmd','copyIcon','copyText'); }

$(document).ready(function() {
    // Always force https:// in the deploy command regardless of server protocol detection
    var cmd = document.getElementById('deployCmd');
    if (cmd) {
        cmd.textContent = cmd.textContent.replace(/url="http:\/\//g, 'url="https://');
    }

    function formatTimeAgo(dateString) {
        if (!dateString || dateString === 'Never') return 'Never';
        
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        
        if (seconds < 60) return 'Just now';
        if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
        if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
        if (seconds < 604800) return Math.floor(seconds / 86400) + ' days ago';
        
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }

    var routerStates = {};
    var pendingChecks = {};
    var statusUpdateInterval = 30000; // 30 seconds

    var retryCount = {};
    var maxRetries = 2; // Number of retries before marking as offline
    
    function updateRouterStatus(routerId) {
        var row = $('[data-router-id="' + routerId + '"]').first().closest('tr');
        
        // Don't check if there's a pending check
        if (pendingChecks[routerId]) return;
        
        // Initialize retry count if not exists
        if (!retryCount[routerId]) {
            retryCount[routerId] = 0;
        }
        
        // Show checking status only on first check
        if (!routerStates[routerId]) {
            row.find('.router-status').html('<span class="status" style="background-color: #6c757d;">Checking...</span>');
            row.find('.router-uptime, .router-used-memory, .router-cpu-load, .router-last-seen').html('<i class="fa fa-spinner fa-spin"></i>');
        }

        pendingChecks[routerId] = true;
        
        $.ajax({
            url: '{$_url}routers/get_resources',
            data: { router_id: routerId },
            dataType: 'json',
            timeout: 10000, // Increased timeout to 10 seconds
            success: function(resources) {
                // Reset retry count on successful response
                retryCount[routerId] = 0;
                
                var statusClass = (resources.status === 'Online') ? 'online' : 'offline';
                var statusHtml = '<span class="status ' + statusClass + '">' + resources.status + '</span>';
                
                // Only update display if status actually changed
                if (!routerStates[routerId] || routerStates[routerId].status !== resources.status) {
                    row.find('.router-status').html(statusHtml);
                }
                
                // Always update metrics
                row.find('.router-uptime').html(resources.uptime || 'N/A');
                row.find('.router-used-memory').html(resources.freeMemory || 'N/A');
                row.find('.router-cpu-load').html(resources.cpuLoad || 'N/A');
                
                // Update last seen
                if (resources.status === 'Online') {
                    row.find('.router-last-seen').html('Now');
                } else {
                    row.find('.router-last-seen').html(formatTimeAgo(resources.lastSeen));
                }
                
                routerStates[routerId] = resources;
                delete pendingChecks[routerId];

                // Schedule next update only if router is online
                if (resources.status === 'Online') {
                    setTimeout(function() {
                        updateRouterStatus(routerId);
                    }, statusUpdateInterval);
                }
            },
            error: function() {
                retryCount[routerId]++;
                
                // Only mark as offline after max retries
                if (retryCount[routerId] >= maxRetries) {
                    if (!routerStates[routerId] || routerStates[routerId].status !== 'Offline') {
                        row.find('.router-status').html('<span class="status offline">Offline</span>');
                        row.find('.router-uptime, .router-used-memory, .router-cpu-load, .router-last-seen').html('N/A');
                        routerStates[routerId] = { status: 'Offline' };
                    }
                    // Reset retry count
                    retryCount[routerId] = 0;
                } else {
                    // If we haven't reached max retries, try again quickly
                    setTimeout(function() {
                        delete pendingChecks[routerId];
                        updateRouterStatus(routerId);
                    }, 2000); // Retry after 2 seconds
                    return;
                }
                
                delete pendingChecks[routerId];
                
                // For confirmed offline routers, check less frequently
                setTimeout(function() {
                    updateRouterStatus(routerId);
                }, statusUpdateInterval * 2);
            }
        });
    }

    // Initial status check for all routers
    $('.router-status').each(function() {
        var routerId = $(this).data('router-id');
        updateRouterStatus(routerId);
    });

    // Manual refresh button handler
    $('.btn-refresh').on('click', function() {
        var routerId = $(this).data('router-id');
        delete routerStates[routerId];
        updateRouterStatus(routerId);
    });

    // Refresh all statuses
    $('#refreshAllStatuses').on('click', function() {
        $('.router-status').each(function() {
            var routerId = $(this).data('router-id');
            delete routerStates[routerId];
            updateRouterStatus(routerId);
        });
    });

    // Select all checkboxes
    $('#selectAll').on('change', function() {
        $('.router-checkbox').prop('checked', $(this).prop('checked'));
        toggleBulkActions();
    });

    $('.router-checkbox').on('change', function() {
        toggleBulkActions();
        $('#selectAll').prop('checked', $('.router-checkbox:checked').length === $('.router-checkbox').length);
    });

    function toggleBulkActions() {
        if ($('.router-checkbox:checked').length > 0) {
            $('.bulk-actions').show();
        } else {
            $('.bulk-actions').hide();
        }
    }

    // Apply bulk actions
    $('#applyBulkAction').on('click', function() {
        var action = $('#bulkAction').val();
        var selectedIds = $('.router-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (!action || selectedIds.length === 0) {
            alert('Please select an action and at least one router.');
            return;
        }

        if (confirm('Are you sure you want to ' + action + ' the selected routers?')) {
            // Show loading
            $('.loading-overlay').show();

            $.ajax({
                url: '{$_url}routers/bulk_action',
                method: 'POST',
                data: {
                    action: action,
                    router_ids: selectedIds
                },
                dataType: 'json',
                success: function(response) {
                    $('.loading-overlay').hide();
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    $('.loading-overlay').hide();
                    alert('An error occurred while processing the bulk action.');
                }
            });
        }
    });

    // Export to CSV
    $('#exportCSV').on('click', function() {
        var csv = 'Router Name,IP Address,Status,Last Seen,Uptime,Free Memory,CPU Load,ID\n';
        $('tbody tr').each(function() {
            var row = [];
            $(this).find('td:not(:first-child):not(:last-child)').each(function() {
                row.push('"' + $(this).text().trim() + '"');
            });
            csv += row.join(',') + '\n';
        });

        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        if (link.download !== undefined) {
            var url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'routers_export.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    });

    // Reboot router
    $('.btn-reboot').on('click', function() {
        var routerId = $(this).data('router-id');
        if (confirm('Are you sure you want to reboot this router?')) {
            $.ajax({
                url: '{$_url}routers/reboot',
                data: { router_id: routerId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'Rebooting') {
                        alert(response.message);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Failed to send reboot command. Please try again.');
                }
            });
        }
    });

    // Dark mode toggle
    const darkModeToggle = $('#darkModeToggle');
    const body = $('body');
    
    // Check for saved dark mode preference
    if (localStorage.getItem('darkMode') === 'enabled') {
        body.addClass('dark-mode');
        darkModeToggle.find('i').removeClass('fa-moon-o').addClass('fa-sun-o');
    }

    darkModeToggle.click(function() {
        body.toggleClass('dark-mode');
        const isDarkMode = body.hasClass('dark-mode');
        localStorage.setItem('darkMode', isDarkMode ? 'enabled' : 'disabled');
        darkModeToggle.find('i').toggleClass('fa-moon-o fa-sun-o');
    });
});
</script>
