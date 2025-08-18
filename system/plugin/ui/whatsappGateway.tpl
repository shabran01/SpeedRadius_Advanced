{include file="sections/header.tpl"}

<style>
/* Modern WhatsApp Interface Styles */
.whatsapp-container {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.whatsapp-title {
    color: #128C7E;
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.whatsapp-title:before {
    content: '';
    display: inline-block;
    width: 24px;
    height: 24px;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23128C7E"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-11h2v5h-2v-5zm0 6h2v2h-2v-2z"/></svg>') no-repeat center center;
}

.btn-3d {
    position: relative;
    border: none;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    color: white;
    background: linear-gradient(145deg, #25d366, #128C7E);
    box-shadow: 0 2px 6px rgba(37, 211, 102, 0.3),
                0 1px 2px rgba(0, 0, 0, 0.2);
    transform: translateY(0);
    transition: all 0.2s ease;
    margin: 0 2px;
    cursor: pointer;
    display: inline-block;
    text-decoration: none;
    line-height: 1.2;
}

.btn-3d:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(37, 211, 102, 0.4),
                0 2px 3px rgba(0, 0, 0, 0.2);
}

.btn-3d:active {
    transform: translateY(0px);
    box-shadow: 0 1px 4px rgba(37, 211, 102, 0.2);
}

.btn-3d.remove {
    background: linear-gradient(145deg, #ff5555, #dc3545);
    box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
}

.btn-3d.remove:hover {
    box-shadow: 0 3px 8px rgba(220, 53, 69, 0.4);
}

.btn-3d.qr {
    background: linear-gradient(145deg, #6c5ce7, #5851db);
    box-shadow: 0 2px 6px rgba(108, 92, 231, 0.3);
}

.btn-3d.qr:hover {
    box-shadow: 0 3px 8px rgba(108, 92, 231, 0.4);
}

.btn-3d.pair {
    background: linear-gradient(145deg, #00b894, #00a884);
    box-shadow: 0 2px 6px rgba(0, 184, 148, 0.3);
}

.btn-3d.pair:hover {
    box-shadow: 0 3px 8px rgba(0, 184, 148, 0.4);
}

.btn-3d.logout {
    background: linear-gradient(145deg, #3498db, #2980b9);
    box-shadow: 0 2px 6px rgba(52, 152, 219, 0.3);
}

.btn-3d.logout:hover {
    box-shadow: 0 3px 8px rgba(52, 152, 219, 0.4),
                0 2px 3px rgba(0, 0, 0, 0.2);
}

.btn-3d.logout:active {
    transform: translateY(0px);
    box-shadow: 0 1px 4px rgba(52, 152, 219, 0.2);
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    display: inline-block;
}

.status-badge.logged-in {
    background: #25d366;
    color: white;
}

.status-badge.not-logged-in {
    background: #ff5555;
    color: white;
}

.whatsapp-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.whatsapp-table th {
    background: #128C7E;
    color: white;
    padding: 15px;
    font-weight: 600;
}

.whatsapp-table td {
    padding: 8px 12px;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.whatsapp-table tr:last-child td {
    border-bottom: none;
}

/* Compact button container for table cells */
.action-buttons {
    white-space: nowrap;
}

.action-buttons .btn-3d {
    margin: 0 1px;
    padding: 4px 8px;
    font-size: 11px;
}

.whatsapp-form {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.form-input {
    width: 100%;
    padding: 10px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 15px;
    transition: border-color 0.2s ease;
}

.form-input:focus {
    border-color: #128C7E;
    outline: none;
}

.status-container {
    text-align: center;
    margin: 20px 0;
}

.api-info {
    background: #f8f9fa;
    border-left: 4px solid #128C7E;
    padding: 15px;
    margin: 20px 0;
    border-radius: 0 8px 8px 0;
}
</style>

{if $menu == 'login'}
    <div class="whatsapp-container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="glyphicon glyphicon-qrcode"></i> WhatsApp Connection
                        </h3>
                    </div>
                    <div class="box-body with-border">
                        <div class="login-status text-center">
                            {if isset($message)}
                                <div class="qr-container">
                                    {$message}
                                </div>
                                {if strpos($message, 'paircode') !== false || strpos($message, 'Timeout') !== false}
                                    <div class="alert alert-info">
                                        <i class="glyphicon glyphicon-info-sign"></i> 
                                        Open WhatsApp on your phone, go to Settings > Linked Devices > Link a Device
                                    </div>
                                {/if}
                            {else}
                                <div class="alert alert-warning">
                                    <i class="glyphicon glyphicon-warning-sign"></i> Disconnected
                                </div>
                            {/if}
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-xs-4">
                                <a class="btn-3d" href="{$_url}plugin/whatsappGateway">Back</a>
                            </div>
                            <div class="col-xs-8">
                                <button class="btn-3d refresh" type="button" onclick="checkWhatsAppStatus()">Check Status</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .qr-container {
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin: 20px 0;
    }
    .qr-container h1 {
        font-size: 24px;
        color: #128C7E;
        margin: 10px 0;
    }
    .btn-3d.refresh {
        background: linear-gradient(145deg, #4CAF50, #45a049);
        min-width: 120px; /* Ensure consistent width when text changes */
    }
    
    .btn-3d.refresh:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
        background: linear-gradient(145deg, #81C784, #66BB6A) !important;
    }
    
    .btn-3d.refresh:disabled:hover {
        transform: none !important;
        box-shadow: 0 2px 6px rgba(76, 175, 80, 0.3) !important;
    }
    
    /* Spinning animation for glyphicon */
    .glyphicon-spin {
        animation: spin 1s infinite linear;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .login-status {
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .alert {
        width: 100%;
        padding: 15px;
        border-radius: 8px;
        margin: 10px 0;
    }
    .alert-info {
        background: #e3f2fd;
        color: #1976d2;
        border: 1px solid #bbdefb;
    }
    .alert-warning {
        background: #fff3e0;
        color: #f57c00;
        border: 1px solid #ffe0b2;
    }
    </style>

    <script>
    function checkWhatsAppStatus() {
        const button = document.querySelector('.btn-3d.refresh');
        const originalText = button.innerHTML;
        
        // Show loading state
        button.innerHTML = '<i class="glyphicon glyphicon-refresh glyphicon-spin"></i> Checking...';
        button.disabled = true;
        button.style.pointerEvents = 'none';
        
        // Get current URL parameters to maintain phone number
        const urlParams = new URLSearchParams(window.location.search);
        const currentPhone = urlParams.get('p');
        const isPairMode = urlParams.has('pair');
        
        // Construct the refresh URL with current parameters
        let refreshUrl = window.location.href;
        
        // Add a timestamp to force refresh and avoid cache
        const separator = refreshUrl.includes('?') ? '&' : '?';
        refreshUrl += separator + '_t=' + Date.now();
        
        // Refresh the page after a short delay
        setTimeout(function() {
            window.location.href = refreshUrl;
        }, 1000);
    }
    
    // Auto-refresh every 60 seconds when on login page
    let autoRefreshInterval;
    
    function startAutoRefresh() {
        autoRefreshInterval = setInterval(function() {
            // Only refresh if we're still on the same page
            if (window.location.href.indexOf('whatsappGateway_login') !== -1) {
                window.location.reload();
            } else {
                clearInterval(autoRefreshInterval);
            }
        }, 60000); // 60 seconds
    }    // Start auto-refresh when page loads
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.href.indexOf('whatsappGateway_login') !== -1) {
            // Start auto-refresh after initial page load
            setTimeout(startAutoRefresh, 3000); // Wait 3 seconds before starting auto-refresh
        }
    });
    
    // Stop auto-refresh when user leaves the page
    window.addEventListener('beforeunload', function() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
    });
    </script>
{elseif $menu == 'config'}

    <form class="form" method="post" role="form" action="{$_url}plugin/whatsappGateway_config">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            Configuration
                        </h3>
                    </div>
                    <div class="box-body with-border">
                        <div class="form-group">
                            <label>Server URL</label>
                            <input type="text" class="form-control" name="whatsapp_gateway_url"
                                value="{$_c['whatsapp_gateway_url']}" required placeholder="http://localhost:3000">
                            <a href="https://github.com/dimaskiddo/go-whatsapp-multidevice-rest" class="btn-3d pull-right"
                                target="_blank">Go WhatsApp Multi-Device</a>
                        </div>
                        <div class="form-group">
                            <label>Auth Basic Password</label>
                            <input type="text" class="form-control" name="whatsapp_gateway_secret" required
                                placeholder="AUTH_BASIC_PASSWORD" value="{$_c['whatsapp_gateway_secret']}">
                            <span class="text-muted">AUTH_BASIC_PASSWORD From .env, change this will change secret for API</span>
                        </div>
                        <div class="form-group">
                            <label class="control-label">{Lang::T('Country Code Phone')}</label>
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1">+</span>
                                <input type="text" class="form-control" id="whatsapp_country_code_phone" placeholder="62"
                                    name="whatsapp_country_code_phone" value="{$_c['whatsapp_country_code_phone']}">
                            </div>
                            <span class="text-muted">if you put 62, Phone started with 0xxxx will change to 62xxxx</span>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-xs-4">
                                <a class="btn-3d btn-default btn-block" href="{$_url}plugin/whatsappGateway">Back</a>
                            </div>
                            <div class="col-xs-8">
                                <button class="btn-3d btn-block" type="submit">Save Changes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
{else}
    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="glyphicon glyphicon-plus"></i>
                        Add Phone
                    </h3>
                    <div class="box-tools pull-right">
                        <a href="{$_url}plugin/whatsappGateway_config" class="btn btn-box-tool" data-toggle="tooltip"
                            data-placement="top" title="Configuration"><i class="glyphicon glyphicon-cog"></i></a>
                    </div>
                </div>
                <div class="box-body with-border">
                    <form class="form-horizontal" method="post" role="form" action="{$_url}plugin/whatsappGateway_addPhone">
                        <div class="form-group">
                            <div class="col-md-12">
                                <label>{Lang::T('Phone Number')}</label>
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1"><i
                                            class="glyphicon glyphicon-phone"></i></span>
                                    <input type="text" class="form-control" name="phonenumber" required
                                        placeholder="{$_c['country_code_phone']} {Lang::T('Phone Number')}">
                                </div>
                                <span class="pull-right">Use Country Code as whatsapp need it</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <button class="btn btn-success btn-block btn-sm" type="submit">Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Registered Phone</h3>
                    <div class="box-tools pull-right">
                        <a href="{$_url}plugin/whatsappGateway_config" class="btn btn-box-tool" data-toggle="tooltip"
                            data-placement="top" title="Configuration"><i class="glyphicon glyphicon-cog"></i></a>
                    </div>
                </div>
                <table class="table table-condensed table-bordered">
                    <thead>
                        <tr>
                            <th>Phone Number</th>
                            <th>Status</th>
                            <th>Actions</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $phones as $phone}
                            <tr>
                                <td>{$phone}</td>
                                <td api-get-text='{$_url}plugin/whatsappGateway_status&p={$phone}'><span
                                        class="label label-default">&nbsp;</span></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{$_url}plugin/whatsappGateway_login&p={$phone}"
                                            class="btn-3d qr">QRCode</a>
                                        <a href="{$_url}plugin/whatsappGateway_login&p={$phone}&pair"
                                            class="btn-3d pair">Paircode</a>
                                        <a href="{$_url}plugin/whatsappGateway_logout&p={$phone}"
                                            class="btn-3d logout" onclick="return confirm('Are you sure you want to logout {$phone}?')">Logout</a>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{$_url}plugin/whatsappGateway_delPhone&p={$phone}" 
                                            class="btn-3d remove"
                                            onclick="return confirm('Remove {$phone}?')">Remove</a>
                                    </div>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            <div class="bs-callout bs-callout-warning well">
                <h4>API To send directly</h4>
                <input type="text" class="form-control" readonly onclick="this.select();"
                    value="{$_url}plugin/whatsappGateway_send&to=[number]&msg=[text]&secret={md5($_c['whatsapp_gateway_secret'])}">
                <span class="text-muted">Change Auth Basic Password will change secret. No need to change whatsapp URL in PHPNuxBill with this. the plugin will work directly.</span>
            </div>
        </div>
    </div>
{/if}

<div class="bs-callout bs-callout-warning well">
    <h4>Sending WhatsApp</h4>
    <p>If you put multiple number, it will send random to any existed phone number. even if it not logged in to
        WhatsApp.</p>
    <p><b>Empty Whatsapp Server URL in Speedradius configuration</b>, this plugin will overide sending WhatsApp.</p>
    <p>This plugin only support <a href="https://github.com/dimaskiddo/go-whatsapp-multidevice-rest" target="_blank">Go
            WhatsApp Multi-Device</a>
</div>
{include file="sections/footer.tpl"}