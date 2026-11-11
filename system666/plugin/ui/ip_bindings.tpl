{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-primary btn-sm" onclick="refreshData()">
                        <i class="glyphicon glyphicon-refresh"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="exportCSV()">
                        <i class="glyphicon glyphicon-download-alt"></i> Export CSV
                    </button>
                    <button type="button" class="btn btn-info btn-sm" onclick="showAddBindingModal()">
                        <i class="glyphicon glyphicon-plus"></i> Add Binding
                    </button>
                </div>
                <div class="btn-group pull-right" style="margin-right: 10px;">
                    <select class="form-control input-sm" id="router_select" onchange="changeRouter()" style="width: 200px;">
                        {if $routers}
                            {foreach $routers as $router}
                                <option value="{$router.id}" {if $default_router && $router.id == $default_router.id}selected{/if}>
                                    {$router.name} ({$router.ip_address})
                                </option>
                            {/foreach}
                        {else}
                            <option value="">No routers configured</option>
                        {/if}
                    </select>
                </div>
                IP Bindings - Hotspot MAC to IP Bindings
            </div>
            <div class="panel-body">
                
                <!-- Statistics Cards -->
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-lg-3 col-md-6">
                        <div class="card border-left-primary shadow h-100 py-2" style="border-left: 4px solid #4e73df; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); background: white; border-radius: 0.35rem;">
                            <div class="card-body" style="padding: 1.25rem;">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="font-size: 11px; font-weight: 700; color: #5a5c69;">
                                            Total Bindings
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" style="font-size: 24px; font-weight: 700; color: #5a5c69;" id="total_bindings">
                                            {if $bindings_data.stats}{$bindings_data.stats.total}{else}0{/if}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-link fa-2x text-gray-300" style="font-size: 2em; color: #dddfeb;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card border-left-success shadow h-100 py-2" style="border-left: 4px solid #1cc88a; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); background: white; border-radius: 0.35rem;">
                            <div class="card-body" style="padding: 1.25rem;">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="font-size: 11px; font-weight: 700; color: #1cc88a;">
                                            Active Bindings
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" style="font-size: 24px; font-weight: 700; color: #5a5c69;" id="active_bindings">
                                            {if $bindings_data.stats}{$bindings_data.stats.active}{else}0{/if}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300" style="font-size: 2em; color: #dddfeb;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card border-left-warning shadow h-100 py-2" style="border-left: 4px solid #f6c23e; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); background: white; border-radius: 0.35rem;">
                            <div class="card-body" style="padding: 1.25rem;">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="font-size: 11px; font-weight: 700; color: #f6c23e;">
                                            Disabled Bindings
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" style="font-size: 24px; font-weight: 700; color: #5a5c69;" id="disabled_bindings">
                                            {if $bindings_data.stats}{$bindings_data.stats.disabled}{else}0{/if}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-times-circle fa-2x text-gray-300" style="font-size: 2em; color: #dddfeb;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="card border-left-info shadow h-100 py-2" style="border-left: 4px solid #36b9cc; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); background: white; border-radius: 0.35rem;">
                            <div class="card-body" style="padding: 1.25rem;">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1" style="font-size: 11px; font-weight: 700; color: #36b9cc;">
                                            Last Updated
                                        </div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800" style="font-size: 14px; font-weight: 600; color: #5a5c69;" id="last_updated">
                                            {if $bindings_data.last_updated}{$bindings_data.last_updated}{else}Never{/if}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300" style="font-size: 2em; color: #dddfeb;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="search_input" style="font-size: 13px; font-weight: 600;">Search:</label>
                            <input type="text" class="form-control input-sm" id="search_input" placeholder="Search by MAC address, IP address, or server..." style="font-size: 13px;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status_filter" style="font-size: 13px; font-weight: 600;">Status:</label>
                            <select class="form-control input-sm" id="status_filter" style="font-size: 13px;">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="disabled">Disabled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="server_filter" style="font-size: 13px; font-weight: 600;">Server:</label>
                            <select class="form-control input-sm" id="server_filter" style="font-size: 13px;">
                                <option value="">All Servers</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Loading Indicator -->
                <div id="loading_indicator" style="display: none; text-align: center; padding: 20px;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p style="margin-top: 10px; font-size: 14px;">Loading IP bindings data...</p>
                </div>

                <!-- Error Message -->
                <div id="error_message" class="alert alert-danger" style="display: none;">
                    <strong>Error:</strong> <span id="error_text"></span>
                </div>

                <!-- Data Table -->
                <div class="table-responsive" id="bindings_table_container">
                    <table class="table table-bordered table-striped table-hover" id="bindings_table" style="font-size: 13px;">
                        <thead style="background-color: #f8f9fc;">
                            <tr>
                                <th style="font-size: 13px; font-weight: 600; padding: 12px 8px;">MAC Address</th>
                                <th style="font-size: 13px; font-weight: 600; padding: 12px 8px;">IP Address</th>
                                <th style="font-size: 13px; font-weight: 600; padding: 12px 8px;">To Address</th>
                                <th style="font-size: 13px; font-weight: 600; padding: 12px 8px;">Server</th>
                                <th style="font-size: 13px; font-weight: 600; padding: 12px 8px;">Type</th>
                                <th style="font-size: 13px; font-weight: 600; padding: 12px 8px;">Status</th>
                                <th style="font-size: 13px; font-weight: 600; padding: 12px 8px;">Comment</th>
                            </tr>
                        </thead>
                        <tbody id="bindings_tbody">
                            {if $bindings_data.data}
                                {foreach $bindings_data.data as $binding}
                                    <tr>
                                        <td style="font-family: 'Courier New', monospace; font-size: 13px; padding: 8px;">{$binding.mac_address}</td>
                                        <td style="font-family: 'Courier New', monospace; font-size: 13px; padding: 8px;">{$binding.address}</td>
                                        <td style="font-family: 'Courier New', monospace; font-size: 13px; padding: 8px;">{$binding.to_address}</td>
                                        <td style="font-size: 13px; padding: 8px;">{$binding.server}</td>
                                        <td style="font-size: 13px; padding: 8px;">{$binding.type}</td>
                                        <td style="padding: 8px;">
                                            {if $binding.status == 'active'}
                                                <span class="label label-success" style="font-size: 11px;">Active</span>
                                            {else}
                                                <span class="label label-warning" style="font-size: 11px;">Disabled</span>
                                            {/if}
                                        </td>
                                        <td style="font-size: 13px; padding: 8px;">{$binding.comment}</td>
                                    </tr>
                                {/foreach}
                            {else}
                                <tr>
                                    <td colspan="7" class="text-center" style="padding: 20px; font-size: 14px;">
                                        {if $routers}
                                            No IP bindings found for this router.
                                        {else}
                                            No routers configured. Please add a router first.
                                        {/if}
                                    </td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Info -->
                <div class="row" style="margin-top: 15px;">
                    <div class="col-sm-6">
                        <div id="table_info" style="font-size: 13px; padding: 10px 0;">
                            Showing <span id="showing_count">0</span> of <span id="total_count">0</span> bindings
                        </div>
                    </div>
                    <div class="col-sm-6 text-right">
                        <div style="font-size: 12px; color: #6c757d; padding: 10px 0;">
                            Router: <strong id="current_router">{if $default_router}{$default_router.name} ({$default_router.ip_address}){else}None{/if}</strong>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script type="text/javascript">
$(document).ready(function() {
    // Initialize filters
    setupFilters();
    updateTableInfo();
    populateServerFilter();
});

function refreshData() {
    var router_id = $('#router_select').val();
    if (!router_id) {
        showError('Please select a router');
        return;
    }
    
    $('#loading_indicator').show();
    $('#error_message').hide();
    $('#bindings_table_container').hide();
    
    $.post('', {
        action: 'refresh_data',
        router_id: router_id
    }, function(response) {
        $('#loading_indicator').hide();
        
        if (response.success) {
            updateDisplay(response);
            $('#bindings_table_container').show();
        } else {
            showError(response.error || 'Failed to load data');
        }
    }, 'json').fail(function() {
        $('#loading_indicator').hide();
        showError('Network error occurred');
    });
}

function changeRouter() {
    refreshData();
}

function exportCSV() {
    var router_id = $('#router_select').val();
    if (!router_id) {
        alert('Please select a router');
        return;
    }
    
    var form = $('<form method="post" action="">');
    form.append('<input type="hidden" name="action" value="export_csv">');
    form.append('<input type="hidden" name="router_id" value="' + router_id + '">');
    $('body').append(form);
    form.submit();
    form.remove();
}

function showAddBindingModal() {
    var router_id = $('#router_select').val();
    if (!router_id) {
        alert('Please select a router first');
        return;
    }
    
    // Reset form
    $('#addBindingForm')[0].reset();
    $('#binding_mac_address').focus();
    
    // Populate server dropdown with available servers
    populateServerDropdown();
    
    // Show modal
    $('#addBindingModal').modal('show');
}

function submitAddBinding() {
    var router_id = $('#router_select').val();
    if (!router_id) {
        alert('Please select a router');
        return;
    }
    
    // Validate required fields
    var mac_address = $('#binding_mac_address').val().trim();
    var address = $('#binding_address').val().trim();
    
    if (!mac_address) {
        alert('MAC Address is required');
        $('#binding_mac_address').focus();
        return;
    }
    
    if (!address) {
        alert('IP Address is required');
        $('#binding_address').focus();
        return;
    }
    
    // Validate MAC address format
    var macPattern = /^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/;
    if (!macPattern.test(mac_address)) {
        alert('Please enter a valid MAC address (XX:XX:XX:XX:XX:XX)');
        $('#binding_mac_address').focus();
        return;
    }
    
    // Validate IP address format
    var ipPattern = /^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/;
    if (!ipPattern.test(address)) {
        alert('Please enter a valid IP address');
        $('#binding_address').focus();
        return;
    }
    
    // Validate to_address if provided
    var to_address = $('#binding_to_address').val().trim();
    if (to_address && !ipPattern.test(to_address)) {
        alert('Please enter a valid To Address IP');
        $('#binding_to_address').focus();
        return;
    }
    
    // Prepare form data
    var formData = {
        action: 'add_binding',
        router_id: router_id,
        mac_address: mac_address,
        address: address,
        to_address: to_address,
        server: $('#binding_server').val(),
        type: $('#binding_type').val(),
        comment: $('#binding_comment').val().trim(),
        disabled: $('#binding_disabled').is(':checked') ? 'true' : 'false'
    };
    
    // Show loading
    var submitBtn = $('#addBindingModal').find('button[onclick="submitAddBinding()"]');
    var originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Adding...').prop('disabled', true);
    
    // Submit via AJAX
    $.post('', formData, function(response) {
        submitBtn.html(originalText).prop('disabled', false);
        
        if (response.success) {
            $('#addBindingModal').modal('hide');
            alert('IP Binding added successfully!');
            refreshData(); // Refresh the data to show new binding
        } else {
            alert('Error: ' + (response.error || 'Failed to add binding'));
        }
    }, 'json').fail(function() {
        submitBtn.html(originalText).prop('disabled', false);
        alert('Network error occurred while adding binding');
    });
}

function populateServerDropdown() {
    // Get unique servers from current bindings data
    var servers = new Set(['all']);
    
    $('#bindings_tbody tr').each(function() {
        var row = $(this);
        if (row.find('td').length > 1) {
            var server = row.find('td:eq(3)').text().trim();
            if (server && server !== 'all') {
                servers.add(server);
            }
        }
    });
    
    var serverSelect = $('#binding_server');
    var currentValue = serverSelect.val();
    serverSelect.empty();
    
    servers.forEach(function(server) {
        serverSelect.append('<option value="' + server + '">' + server + '</option>');
    });
    
    // Restore previous selection or default to 'all'
    if (servers.has(currentValue)) {
        serverSelect.val(currentValue);
    } else {
        serverSelect.val('all');
    }
}

function updateDisplay(data) {
    // Update statistics
    $('#total_bindings').text(data.stats.total);
    $('#active_bindings').text(data.stats.active);
    $('#disabled_bindings').text(data.stats.disabled);
    $('#last_updated').text(data.last_updated);
    $('#current_router').text(data.router_name + ' (' + data.router_ip + ')');
    
    // Update table
    var tbody = $('#bindings_tbody');
    tbody.empty();
    
    if (data.data.length > 0) {
        $.each(data.data, function(index, binding) {
            var statusClass = binding.status === 'active' ? 'success' : 'warning';
            var statusText = binding.status === 'active' ? 'Active' : 'Disabled';
            
            var row = '<tr>' +
                '<td style="font-family: \'Courier New\', monospace; font-size: 13px; padding: 8px;">' + binding.mac_address + '</td>' +
                '<td style="font-family: \'Courier New\', monospace; font-size: 13px; padding: 8px;">' + binding.address + '</td>' +
                '<td style="font-family: \'Courier New\', monospace; font-size: 13px; padding: 8px;">' + binding.to_address + '</td>' +
                '<td style="font-size: 13px; padding: 8px;">' + binding.server + '</td>' +
                '<td style="font-size: 13px; padding: 8px;">' + binding.type + '</td>' +
                '<td style="padding: 8px;"><span class="label label-' + statusClass + '" style="font-size: 11px;">' + statusText + '</span></td>' +
                '<td style="font-size: 13px; padding: 8px;">' + binding.comment + '</td>' +
                '</tr>';
            tbody.append(row);
        });
    } else {
        tbody.append('<tr><td colspan="7" class="text-center" style="padding: 20px; font-size: 14px;">No IP bindings found for this router.</td></tr>');
    }
    
    // Re-apply filters
    applyFilters();
    updateTableInfo();
    populateServerFilter();
}

function setupFilters() {
    $('#search_input').on('keyup', function() {
        applyFilters();
    });
    
    $('#status_filter').on('change', function() {
        applyFilters();
    });
    
    $('#server_filter').on('change', function() {
        applyFilters();
    });
}

function applyFilters() {
    var searchTerm = $('#search_input').val().toLowerCase();
    var statusFilter = $('#status_filter').val();
    var serverFilter = $('#server_filter').val();
    
    var visibleRows = 0;
    
    $('#bindings_tbody tr').each(function() {
        var row = $(this);
        var visible = true;
        
        if (row.find('td').length === 1) {
            // Skip empty state row
            return;
        }
        
        // Search filter
        if (searchTerm) {
            var rowText = row.text().toLowerCase();
            if (rowText.indexOf(searchTerm) === -1) {
                visible = false;
            }
        }
        
        // Status filter
        if (statusFilter && visible) {
            var statusLabel = row.find('.label');
            if (statusLabel.length > 0) {
                var hasStatus = statusLabel.hasClass('label-success') && statusFilter === 'active' ||
                               statusLabel.hasClass('label-warning') && statusFilter === 'disabled';
                if (!hasStatus) {
                    visible = false;
                }
            }
        }
        
        // Server filter
        if (serverFilter && visible) {
            var serverCell = row.find('td:eq(3)').text();
            if (serverCell !== serverFilter) {
                visible = false;
            }
        }
        
        if (visible) {
            row.show();
            visibleRows++;
        } else {
            row.hide();
        }
    });
    
    updateTableInfo();
}

function populateServerFilter() {
    var servers = new Set();
    $('#bindings_tbody tr').each(function() {
        var row = $(this);
        if (row.find('td').length > 1) {
            var server = row.find('td:eq(3)').text().trim();
            if (server) {
                servers.add(server);
            }
        }
    });
    
    var serverFilter = $('#server_filter');
    var currentValue = serverFilter.val();
    serverFilter.find('option:not(:first)').remove();
    
    servers.forEach(function(server) {
        serverFilter.append('<option value="' + server + '">' + server + '</option>');
    });
    
    serverFilter.val(currentValue);
}

function updateTableInfo() {
    var totalRows = $('#bindings_tbody tr:visible').length;
    var totalBindings = $('#total_bindings').text();
    
    // Don't count the empty state row
    if ($('#bindings_tbody tr:visible td[colspan]').length > 0) {
        totalRows = 0;
    }
    
    $('#showing_count').text(totalRows);
    $('#total_count').text(totalBindings);
}

function showError(message) {
    $('#error_text').text(message);
    $('#error_message').show();
    $('#bindings_table_container').hide();
}

// Responsive table handling
$(window).resize(function() {
    // Handle table responsiveness on window resize
    var table = $('#bindings_table');
    if ($(window).width() < 768) {
        table.addClass('table-sm');
    } else {
        table.removeClass('table-sm');
    }
});

// Initialize responsive behavior
$(window).trigger('resize');
</script>

<!-- Custom CSS for better mobile responsiveness -->
<style>
@media (max-width: 768px) {
    .table-responsive {
        font-size: 12px;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .h5 {
        font-size: 20px !important;
    }
    
    .text-xs {
        font-size: 10px !important;
    }
    
    .btn-group .btn {
        padding: 5px 10px;
        font-size: 12px;
    }
    
    #router_select {
        width: 150px !important;
    }
}

@media (max-width: 576px) {
    .col-lg-3 {
        margin-bottom: 15px;
    }
    
    .panel-heading .btn-group {
        margin-bottom: 10px;
        float: none !important;
        display: block;
    }
    
    .table th, .table td {
        padding: 6px 4px !important;
        font-size: 11px !important;
    }
}
</style>

<!-- Add IP Binding Modal -->
<div class="modal fade" id="addBindingModal" tabindex="-1" role="dialog" aria-labelledby="addBindingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="addBindingModalLabel">
                    <i class="glyphicon glyphicon-plus"></i> Add New IP Binding
                </h4>
            </div>
            <div class="modal-body">
                <form id="addBindingForm">
                    <div class="form-group">
                        <label for="binding_mac_address">MAC Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="binding_mac_address" name="mac_address" 
                               placeholder="00:11:22:33:44:55" pattern="([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})" 
                               title="Please enter a valid MAC address (XX:XX:XX:XX:XX:XX)" required>
                        <small class="form-text text-muted">Format: XX:XX:XX:XX:XX:XX</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="binding_address">IP Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="binding_address" name="address" 
                               placeholder="192.168.1.100" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$" 
                               title="Please enter a valid IP address" required>
                        <small class="form-text text-muted">Single IP address to bind</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="binding_to_address">To Address</label>
                        <input type="text" class="form-control" id="binding_to_address" name="to_address" 
                               placeholder="192.168.1.200 (optional)" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$">
                        <small class="form-text text-muted">Optional: End IP for range binding</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="binding_server">Hotspot Server</label>
                        <select class="form-control" id="binding_server" name="server">
                            <option value="all">all</option>
                        </select>
                        <small class="form-text text-muted">Select the hotspot server for this binding</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="binding_type">Binding Type</label>
                        <select class="form-control" id="binding_type" name="type">
                            <option value="regular">regular</option>
                            <option value="bypassed">bypassed</option>
                            <option value="blocked">blocked</option>
                        </select>
                        <small class="form-text text-muted">Type of IP binding</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="binding_comment">Comment</label>
                        <input type="text" class="form-control" id="binding_comment" name="comment" 
                               placeholder="Optional comment">
                        <small class="form-text text-muted">Optional description for this binding</small>
                    </div>
                    
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="binding_disabled" name="disabled"> 
                            Create as disabled
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="glyphicon glyphicon-remove"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="submitAddBinding()">
                    <i class="glyphicon glyphicon-plus"></i> Add Binding
                </button>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
