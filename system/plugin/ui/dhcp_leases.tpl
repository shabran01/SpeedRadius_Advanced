{include file="sections/header.tpl"}

<style>
@media (max-width: 768px) {
    .small-box .inner h3 {
        font-size: 20px !important;
    }
    .small-box .inner p {
        font-size: 11px !important;
    }
    .table-responsive {
        font-size: 11px !important;
    }
    .btn-group-xs > .btn {
        padding: 2px 5px !important;
        font-size: 10px !important;
    }
    .table th, .table td {
        padding: 6px !important;
    }
}

@media (max-width: 480px) {
    .small-box .inner h3 {
        font-size: 18px !important;
    }
    .table th, .table td {
        padding: 4px !important;
        font-size: 10px !important;
    }
    .label {
        font-size: 9px !important;
        padding: 1px 4px !important;
    }
}

@media (min-width: 769px) {
    .table {
        font-size: 13px !important;
    }
    .table th {
        font-size: 12px !important;
        padding: 10px !important;
    }
    .table td {
        padding: 8px !important;
    }
}

.table-responsive {
    border: 1px solid #ddd;
    border-radius: 4px;
}

.small-box {
    position: relative;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.panel-body {
    padding: 20px;
}

@media (max-width: 767px) {
    .panel-body {
        padding: 15px;
    }
}

.dynamic-lease {
    background-color: #fafbfc !important;
}

.static-lease {
    background-color: #f0f8ff !important;
    border-left: 3px solid #337ab7;
}

.btn-group .btn {
    transition: all 0.2s ease-in-out;
}

.btn-group .btn:hover {
    transform: scale(1.05);
}
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-primary btn-xs" onclick="refreshLeases()" style="font-size: 11px; padding: 4px 8px;">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-success btn-xs" onclick="exportLeases()" style="font-size: 11px; padding: 4px 8px;">
                        <i class="fa fa-download"></i> Export CSV
                    </button>
                </div>
                <span style="font-size: 16px; font-weight: 500;">DHCP Server Leases</span>
            </div>
            <div class="panel-body">
                
                {if $error_message}
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i> {$error_message}
                    </div>
                {/if}
                
                <!-- Router Selection -->
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-lg-4 col-md-6 col-sm-12" style="margin-bottom: 10px;">
                        <label style="font-size: 12px; margin-bottom: 5px; display: block; font-weight: 600;">Select Router:</label>
                        <select class="form-control input-sm" id="router_select" onchange="changeRouter()" style="font-size: 12px; height: 32px;">
                            <option value="">-- Select Router --</option>
                            {foreach $routers as $router}
                                <option value="{$router.id}" {if $router.id == $selected_router}selected{/if}>
                                    {$router.name} ({$router.ip_address})
                                </option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12" style="margin-bottom: 10px;">
                        <label style="font-size: 12px; margin-bottom: 5px; display: block; font-weight: 600;">Filter by Status:</label>
                        <select class="form-control input-sm" id="status_filter" onchange="filterByStatus()" style="font-size: 12px; height: 32px;">
                            <option value="">All Status</option>
                            <option value="bound">Bound</option>
                            <option value="waiting">Waiting</option>
                            <option value="offered">Offered</option>
                            <option value="disabled">Disabled</option>
                            <option value="blocked">Blocked</option>
                        </select>
                    </div>
                    <div class="col-lg-5 col-md-12 col-sm-12" style="margin-bottom: 10px;">
                        <label style="font-size: 12px; margin-bottom: 5px; display: block; font-weight: 600;">Search:</label>
                        <input type="text" class="form-control input-sm" id="search_box" placeholder="Search IP, MAC, or Hostname..." onkeyup="searchLeases()" style="font-size: 12px; height: 32px;">
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 15px;">
                        <div class="small-box bg-blue" style="border-radius: 8px; overflow: hidden;">
                            <div class="inner" style="padding: 15px;">
                                <h3 id="total_leases" style="font-size: 24px; margin: 0 0 5px 0; font-weight: 600;">{$dhcp_leases|count}</h3>
                                <p style="font-size: 12px; margin: 0; opacity: 0.9;">Total Leases</p>
                            </div>
                            <div class="icon" style="position: absolute; top: 10px; right: 15px;">
                                <i class="ion ion-network" style="font-size: 40px; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 15px;">
                        <div class="small-box bg-green" style="border-radius: 8px; overflow: hidden;">
                            <div class="inner" style="padding: 15px;">
                                <h3 id="bound_leases" style="font-size: 24px; margin: 0 0 5px 0; font-weight: 600;">
                                    {assign var="bound_count" value=0}
                                    {foreach $dhcp_leases as $lease}
                                        {if $lease.status == 'bound'}{assign var="bound_count" value=$bound_count+1}{/if}
                                    {/foreach}
                                    {$bound_count}
                                </h3>
                                <p style="font-size: 12px; margin: 0; opacity: 0.9;">Bound</p>
                            </div>
                            <div class="icon" style="position: absolute; top: 10px; right: 15px;">
                                <i class="ion ion-checkmark-circled" style="font-size: 40px; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 15px;">
                        <div class="small-box bg-yellow" style="border-radius: 8px; overflow: hidden;">
                            <div class="inner" style="padding: 15px;">
                                <h3 id="waiting_leases" style="font-size: 24px; margin: 0 0 5px 0; font-weight: 600;">
                                    {assign var="waiting_count" value=0}
                                    {foreach $dhcp_leases as $lease}
                                        {if $lease.status == 'waiting'}{assign var="waiting_count" value=$waiting_count+1}{/if}
                                    {/foreach}
                                    {$waiting_count}
                                </h3>
                                <p style="font-size: 12px; margin: 0; opacity: 0.9;">Waiting</p>
                            </div>
                            <div class="icon" style="position: absolute; top: 10px; right: 15px;">
                                <i class="ion ion-clock" style="font-size: 40px; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 15px;">
                        <div class="small-box bg-red" style="border-radius: 8px; overflow: hidden;">
                            <div class="inner" style="padding: 15px;">
                                <h3 id="static_leases" style="font-size: 24px; margin: 0 0 5px 0; font-weight: 600;">
                                    {assign var="static_count" value=0}
                                    {foreach $dhcp_leases as $lease}
                                        {if $lease.lease_type == 'static'}{assign var="static_count" value=$static_count+1}{/if}
                                    {/foreach}
                                    {$static_count}
                                </h3>
                                <p style="font-size: 12px; margin: 0; opacity: 0.9;">Static</p>
                            </div>
                            <div class="icon" style="position: absolute; top: 10px; right: 15px;">
                                <i class="ion ion-locked" style="font-size: 40px; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Loading indicator -->
                <div id="loading_indicator" style="display: none;" class="text-center">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p>Loading DHCP leases...</p>
                </div>
                
                <!-- DHCP Leases Table -->
                {if $dhcp_leases}
                <div class="table-responsive" style="margin-top: 20px;">
                    <table id="dhcp_table" class="table table-bordered table-striped table-hover" style="font-size: 13px; margin-bottom: 0;">
                        <thead style="background-color: #f8f9fa;">
                            <tr style="font-size: 12px; font-weight: 600;">
                                <th style="padding: 10px; white-space: nowrap;">IP Address</th>
                                <th style="padding: 10px; white-space: nowrap;">MAC Address</th>
                                <th style="padding: 10px; white-space: nowrap;">Client ID</th>
                                <th style="padding: 10px; white-space: nowrap;">Server</th>
                                <th style="padding: 10px; white-space: nowrap;">Status</th>
                                <th style="padding: 10px; white-space: nowrap;">Expires After</th>
                                <th style="padding: 10px; white-space: nowrap;">Last Seen</th>
                                <th style="padding: 10px; white-space: nowrap;">Host Name</th>
                                <th style="padding: 10px; white-space: nowrap;">Type</th>
                                <th style="padding: 10px; white-space: nowrap; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $dhcp_leases as $lease}
                            <tr class="lease-row {if $lease.lease_type == 'static'}static-lease{else}dynamic-lease{/if}" 
                                data-status="{$lease.status}" 
                                data-type="{$lease.lease_type}"
                                data-search="{$lease.address} {$lease.mac_address} {$lease.host_name} {$lease.client_id}">
                                <td style="padding: 8px; vertical-align: middle;">
                                    <strong style="font-size: 13px;">{$lease.address}</strong>
                                    {if $lease.active_address && $lease.active_address != $lease.address}
                                        <br><small class="text-muted" style="font-size: 11px;">Active: {$lease.active_address}</small>
                                    {/if}
                                </td>
                                <td style="padding: 8px; vertical-align: middle;">
                                    <code style="font-size: 12px; background: #f8f9fa; padding: 2px 4px; border-radius: 3px; word-break: break-all;">{$lease.mac_address}</code>
                                    {if $lease.active_mac_address && $lease.active_mac_address != $lease.mac_address}
                                        <br><small class="text-muted" style="font-size: 11px;">Active: <code style="font-size: 11px;">{$lease.active_mac_address}</code></small>
                                    {/if}
                                </td>
                                <td style="padding: 8px; vertical-align: middle; max-width: 100px; word-wrap: break-word; overflow: hidden;">
                                    {if $lease.client_id}
                                        <small style="font-size: 12px; display: block; overflow: hidden; text-overflow: ellipsis;">{$lease.client_id}</small>
                                    {else}
                                        <span class="text-muted" style="font-size: 12px;">-</span>
                                    {/if}
                                </td>
                                <td style="padding: 6px; vertical-align: middle;">
                                    <span class="label label-info" style="font-size: 9px; padding: 2px 6px;">{$lease.server}</span>
                                </td>
                                <td style="padding: 6px; vertical-align: middle;">
                                    <span class="label label-{$lease.status_class}" style="font-size: 9px; padding: 2px 6px;">{$lease.status}</span>
                                    {if $lease.disabled == 'true'}
                                        <br><span class="label label-danger" style="font-size: 8px; padding: 1px 4px; margin-top: 2px;">Disabled</span>
                                    {/if}
                                    {if $lease.blocked == 'true'}
                                        <br><span class="label label-warning" style="font-size: 8px; padding: 1px 4px; margin-top: 2px;">Blocked</span>
                                    {/if}
                                </td>
                                <td style="padding: 6px; vertical-align: middle; font-size: 10px; white-space: nowrap;">
                                    {if $lease.expires_after == 'never'}
                                        <span class="text-muted">Never</span>
                                    {else}
                                        <span title="{$lease.expires_after}">{$lease.expires_after}</span>
                                    {/if}
                                </td>
                                <td style="padding: 6px; vertical-align: middle; font-size: 10px; white-space: nowrap;">
                                    {if $lease.last_seen == 'never'}
                                        <span class="text-muted">Never</span>
                                    {else}
                                        <span title="{$lease.last_seen}">{$lease.last_seen}</span>
                                    {/if}
                                </td>
                                <td style="padding: 6px; vertical-align: middle; max-width: 100px; word-wrap: break-word; overflow: hidden;">
                                    {if $lease.host_name}
                                        <strong style="font-size: 10px; display: block; overflow: hidden; text-overflow: ellipsis;" title="{$lease.host_name}">{$lease.host_name}</strong>
                                    {else}
                                        <span class="text-muted" style="font-size: 10px;">Unknown</span>
                                    {/if}
                                </td>
                                <td style="padding: 6px; vertical-align: middle;">
                                    {if $lease.lease_type == 'static'}
                                        <span class="label label-primary" style="font-size: 9px; padding: 2px 6px;">Static</span>
                                    {else}
                                        <span class="label label-default" style="font-size: 9px; padding: 2px 6px;">Dynamic</span>
                                    {/if}
                                    {if $lease.radius == 'true'}
                                        <br><span class="label label-success" style="font-size: 8px; padding: 1px 4px; margin-top: 2px;">RADIUS</span>
                                    {/if}
                                </td>
                                <td style="padding: 6px; vertical-align: middle; text-align: center; white-space: nowrap;">
                                    <div class="btn-group" style="display: inline-block;">
                                        <button type="button" class="btn btn-info btn-xs" onclick="showLeaseDetails('{$lease.address|escape}')" title="View Details" style="padding: 3px 6px; font-size: 10px; margin-right: 2px;">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-warning btn-xs" onclick="pingHost('{$lease.address|escape}')" title="Ping Host" style="padding: 3px 6px; font-size: 10px; margin-right: 2px;">
                                            <i class="fa fa-signal"></i>
                                        </button>
                                        {if $lease.lease_type == 'dynamic'}
                                        <button type="button" class="btn btn-success btn-xs" onclick="makeStatic('{$lease.address|escape}', '{$lease.mac_address|escape}')" title="Make Static Lease" style="padding: 3px 6px; font-size: 10px; margin-right: 2px;">
                                            <i class="fa fa-lock"></i>
                                        </button>
                                        {/if}
                                        {if $lease.lease_type == 'static'}
                                        <button type="button" class="btn btn-danger btn-xs" onclick="removeLease('{$lease.address|escape}', '{$lease.mac_address|escape}')" title="Remove Static Lease" style="padding: 3px 6px; font-size: 10px;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        {/if}
                                    </div>
                                </td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                {else}
                    {if $selected_router}
                        <div class="alert alert-info text-center">
                            <i class="fa fa-info-circle"></i> No DHCP leases found for the selected router.
                        </div>
                    {else}
                        <div class="alert alert-warning text-center">
                            <i class="fa fa-exclamation-triangle"></i> Please select a router to view DHCP leases.
                        </div>
                    {/if}
                {/if}
                
            </div>
        </div>
    </div>
</div>

<!-- Lease Details Modal -->
<div class="modal fade" id="leaseDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Lease Details</h4>
            </div>
            <div class="modal-body" id="leaseDetailsContent">
                <!-- Lease details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="confirmationTitle">Confirm Action</h4>
            </div>
            <div class="modal-body" id="confirmationMessage">
                <!-- Confirmation message will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmationButton">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
function changeRouter() {
    var routerId = document.getElementById('router_select').value;
    if (routerId) {
        window.location.href = '{$_url}plugin/dhcp_leases/' + routerId;
    } else {
        window.location.href = '{$_url}plugin/dhcp_leases';
    }
}

function refreshLeases() {
    var routerId = document.getElementById('router_select').value;
    if (!routerId) {
        alert('Please select a router first');
        return;
    }
    
    document.getElementById('loading_indicator').style.display = 'block';
    
    fetch('{$_url}plugin/dhcp_leases_refresh/' + routerId)
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading_indicator').style.display = 'none';
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            document.getElementById('loading_indicator').style.display = 'none';
            alert('Network error: ' + error.message);
        });
}

function exportLeases() {
    var routerId = document.getElementById('router_select').value;
    if (!routerId) {
        alert('Please select a router first');
        return;
    }
    
    window.location.href = '{$_url}plugin/dhcp_leases_export/' + routerId;
}

function filterByStatus() {
    var status = document.getElementById('status_filter').value.toLowerCase();
    var rows = document.querySelectorAll('.lease-row');
    
    rows.forEach(function(row) {
        if (!status || row.dataset.status.toLowerCase() === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    updateStatistics();
}

function searchLeases() {
    var searchTerm = document.getElementById('search_box').value.toLowerCase();
    var rows = document.querySelectorAll('.lease-row');
    
    rows.forEach(function(row) {
        var searchData = row.dataset.search.toLowerCase();
        if (!searchTerm || searchData.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    updateStatistics();
}

function updateStatistics() {
    var visibleRows = document.querySelectorAll('.lease-row:not([style*="display: none"])');
    var boundCount = 0;
    var waitingCount = 0;
    var staticCount = 0;
    
    visibleRows.forEach(function(row) {
        if (row.dataset.status === 'bound') boundCount++;
        if (row.dataset.status === 'waiting') waitingCount++;
        if (row.dataset.type === 'static') staticCount++;
    });
    
    document.getElementById('total_leases').textContent = visibleRows.length;
    document.getElementById('bound_leases').textContent = boundCount;
    document.getElementById('waiting_leases').textContent = waitingCount;
    document.getElementById('static_leases').textContent = staticCount;
}

function showLeaseDetails(ipAddress) {
    // Find the lease data from the table
    var rows = document.querySelectorAll('.lease-row');
    var leaseData = null;
    
    rows.forEach(function(row) {
        var cells = row.querySelectorAll('td');
        if (cells[0].textContent.includes(ipAddress)) {
            leaseData = {
                ip: cells[0].textContent.trim(),
                mac: cells[1].textContent.trim(),
                clientId: cells[2].textContent.trim(),
                server: cells[3].textContent.trim(),
                status: cells[4].textContent.trim(),
                expires: cells[5].textContent.trim(),
                lastSeen: cells[6].textContent.trim(),
                hostname: cells[7].textContent.trim(),
                type: cells[8].textContent.trim()
            };
        }
    });
    
    if (leaseData) {
        var content = '<div class="row">';
        content += '<div class="col-md-6"><strong>IP Address:</strong><br>' + leaseData.ip + '</div>';
        content += '<div class="col-md-6"><strong>MAC Address:</strong><br>' + leaseData.mac + '</div>';
        content += '<div class="col-md-6"><strong>Client ID:</strong><br>' + leaseData.clientId + '</div>';
        content += '<div class="col-md-6"><strong>Server:</strong><br>' + leaseData.server + '</div>';
        content += '<div class="col-md-6"><strong>Status:</strong><br>' + leaseData.status + '</div>';
        content += '<div class="col-md-6"><strong>Type:</strong><br>' + leaseData.type + '</div>';
        content += '<div class="col-md-6"><strong>Expires After:</strong><br>' + leaseData.expires + '</div>';
        content += '<div class="col-md-6"><strong>Last Seen:</strong><br>' + leaseData.lastSeen + '</div>';
        content += '<div class="col-md-12"><strong>Hostname:</strong><br>' + leaseData.hostname + '</div>';
        content += '</div>';
        
        document.getElementById('leaseDetailsContent').innerHTML = content;
        $('#leaseDetailsModal').modal('show');
    }
}

function pingHost(ipAddress) {
    showConfirmation('Ping Information', 'Ping functionality can be implemented to test connectivity to <strong>' + ipAddress + '</strong><br><br>This feature can be extended to perform actual network connectivity tests.', function() {}, 'btn-info', 'OK');
}

function makeStatic(ipAddress, macAddress) {
    var routerId = document.getElementById('router_select').value;
    if (!routerId) {
        showConfirmation('Error', 'Please select a router first', function() {}, 'btn-danger', 'OK');
        return;
    }
    
    var message = 'Are you sure you want to make this lease static?<br><br><strong>IP:</strong> ' + ipAddress + '<br><strong>MAC:</strong> ' + macAddress;
    
    showConfirmation('Make Static Lease', message, function() {
        document.getElementById('loading_indicator').style.display = 'block';
        
        var formData = new FormData();
        formData.append('router_id', routerId);
        formData.append('ip_address', ipAddress);
        formData.append('mac_address', macAddress);
        
        fetch('{$_url}plugin/dhcp_leases_make_static', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading_indicator').style.display = 'none';
            if (data.success) {
                showConfirmation('Success', data.message, function() {
                    location.reload();
                }, 'btn-success', 'OK');
            } else {
                showConfirmation('Error', data.message, function() {}, 'btn-danger', 'OK');
            }
        })
        .catch(error => {
            document.getElementById('loading_indicator').style.display = 'none';
            showConfirmation('Error', 'Network error: ' + error.message, function() {}, 'btn-danger', 'OK');
        });
    }, 'btn-success', 'Make Static');
}

function removeLease(ipAddress, macAddress) {
    var routerId = document.getElementById('router_select').value;
    if (!routerId) {
        showConfirmation('Error', 'Please select a router first', function() {}, 'btn-danger', 'OK');
        return;
    }
    
    var message = 'Are you sure you want to remove this static lease?<br><br><strong>IP:</strong> ' + ipAddress + '<br><strong>MAC:</strong> ' + macAddress + '<br><br><span class="text-danger"><strong>WARNING:</strong> This action cannot be undone!</span>';
    
    showConfirmation('Remove Static Lease', message, function() {
        document.getElementById('loading_indicator').style.display = 'block';
        
        var formData = new FormData();
        formData.append('router_id', routerId);
        formData.append('ip_address', ipAddress);
        formData.append('mac_address', macAddress);
        
        fetch('{$_url}plugin/dhcp_leases_remove', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading_indicator').style.display = 'none';
            if (data.success) {
                showConfirmation('Success', data.message, function() {
                    location.reload();
                }, 'btn-success', 'OK');
            } else {
                showConfirmation('Error', data.message, function() {}, 'btn-danger', 'OK');
            }
        })
        .catch(error => {
            document.getElementById('loading_indicator').style.display = 'none';
            showConfirmation('Error', 'Network error: ' + error.message, function() {}, 'btn-danger', 'OK');
        });
    }, 'btn-danger', 'Remove');
}

function showConfirmation(title, message, callback, buttonClass, buttonText) {
    document.getElementById('confirmationTitle').innerHTML = title;
    document.getElementById('confirmationMessage').innerHTML = message;
    
    var confirmButton = document.getElementById('confirmationButton');
    confirmButton.className = 'btn ' + (buttonClass || 'btn-primary');
    confirmButton.innerHTML = buttonText || 'Confirm';
    
    // Remove any existing event listeners
    var newButton = confirmButton.cloneNode(true);
    confirmButton.parentNode.replaceChild(newButton, confirmButton);
    
    // Add new event listener
    document.getElementById('confirmationButton').addEventListener('click', function() {
        $('#confirmationModal').modal('hide');
        if (callback && typeof callback === 'function') {
            callback();
        }
    });
    
    $('#confirmationModal').modal('show');
}

// Auto-refresh every 30 seconds if enabled
var autoRefresh = false;
function toggleAutoRefresh() {
    autoRefresh = !autoRefresh;
    if (autoRefresh) {
        setInterval(function() {
            if (autoRefresh) {
                refreshLeases();
            }
        }, 30000);
    }
}
</script>

{include file="sections/footer.tpl"}
