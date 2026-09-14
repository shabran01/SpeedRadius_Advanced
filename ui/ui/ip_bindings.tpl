{include file="sections/header.tpl"}

<div class="ip-bindings-container">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Header Section -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                    <div>
                        <h3 class="mb-1" style="color: #495057; font-weight: 600;">IP Bindings</h3>
                        <p class="text-muted mb-0">Hotspot MAC to IP Bindings Management</p>
                    </div>
                    <div class="header-buttons">
                        <select class="router-selector" id="router_select" onchange="changeRouter()">
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
                        <button type="button" class="btn-header btn-refresh" onclick="refreshData()">
                            <i class="glyphicon glyphicon-refresh"></i> Refresh
                        </button>
                        <button type="button" class="btn-header btn-export" onclick="exportCSV()">
                            <i class="glyphicon glyphicon-download-alt"></i> Export CSV
                        </button>
                        <button type="button" class="btn-header btn-add" onclick="showAddBindingModal()">
                            <i class="glyphicon glyphicon-plus"></i> Add Binding
                        </button>
                    </div>
                </div>
                <!-- Statistics Cards -->
                <div class="row mb-5" style="margin-bottom: 35px !important;">
                    <div class="col-lg-3 col-md-6 col-sm-6" style="padding: 0 15px; margin-bottom: 20px;">
                        <div class="stats-card card-primary">
                            <div class="stats-card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stats-label">Total Bindings</div>
                                        <div class="stats-number" id="total_bindings">
                                            {if $bindings_data.stats}{$bindings_data.stats.total}{else}0{/if}
                                        </div>
                                    </div>
                                    <div class="stats-icon">
                                        <i class="glyphicon glyphicon-link"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 col-sm-6" style="padding: 0 15px; margin-bottom: 20px;">
                        <div class="stats-card card-success">
                            <div class="stats-card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stats-label">Active Bindings</div>
                                        <div class="stats-number" id="active_bindings">
                                            {if $bindings_data.stats}{$bindings_data.stats.active}{else}0{/if}
                                        </div>
                                    </div>
                                    <div class="stats-icon">
                                        <i class="glyphicon glyphicon-ok-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 col-sm-6" style="padding: 0 15px; margin-bottom: 20px;">
                        <div class="stats-card card-warning">
                            <div class="stats-card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stats-label">Disabled Bindings</div>
                                        <div class="stats-number" id="disabled_bindings">
                                            {if $bindings_data.stats}{$bindings_data.stats.disabled}{else}0{/if}
                                        </div>
                                    </div>
                                    <div class="stats-icon">
                                        <i class="glyphicon glyphicon-remove-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 col-sm-6" style="padding: 0 15px; margin-bottom: 20px;">
                        <div class="stats-card card-info">
                            <div class="stats-card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="stats-label">Last Updated</div>
                                        <div class="stats-number" id="last_updated" style="font-size: 1.2rem;">
                                            {if $bindings_data.last_updated}{$bindings_data.last_updated|date_format:"%H:%M"}{else}Never{/if}
                                        </div>
                                    </div>
                                    <div class="stats-icon">
                                        <i class="glyphicon glyphicon-time"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <div class="filters-section">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="filter-group">
                                <label class="filter-label">Search</label>
                                <input type="text" class="form-control" id="search_input" 
                                       placeholder="Search by MAC address, IP address, or server...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="filter-group">
                                <label class="filter-label">Status</label>
                                <select class="form-control" id="status_filter">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="disabled">Disabled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="filter-group">
                                <label class="filter-label">Server</label>
                                <select class="form-control" id="server_filter">
                                    <option value="">All Servers</option>
                                </select>
                            </div>
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
                    <table class="table modern-table" id="bindings_table">
                        <thead>
                            <tr>
                                <th>MAC Address</th>
                                <th>IP Address</th>
                                <th>To Address</th>
                                <th>Server</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Comment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="bindings_tbody">
                            {if $bindings_data.data}
                                {foreach $bindings_data.data as $binding}
                                    <tr>
                                        <td class="code-font">{$binding.mac_address}</td>
                                        <td class="code-font">{$binding.address}</td>
                                        <td class="code-font">{$binding.to_address}</td>
                                        <td>{$binding.server}</td>
                                        <td>{$binding.type}</td>
                                        <td>
                                            {if $binding.status == 'active'}
                                                <span class="status-badge status-active">Active</span>
                                            {else}
                                                <span class="status-badge status-disabled">Disabled</span>
                                            {/if}
                                        </td>
                                        <td>{$binding.comment}</td>
                                        <td>
                                            <div class="action-buttons">
                                                <button type="button" class="btn-action btn-edit" onclick="editBinding('{$binding.id}', '{$binding.mac_address}', '{$binding.address}', '{$binding.to_address}', '{$binding.server}', '{$binding.type}', '{$binding.comment}', '{$binding.status}')" title="Edit Binding">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                {if $binding.status == 'active'}
                                                    <button type="button" class="btn-action btn-disable" onclick="disableBinding('{$binding.id}')" title="Disable Binding">
                                                        <i class="fa fa-pause"></i>
                                                    </button>
                                                {else}
                                                    <button type="button" class="btn-action btn-enable" onclick="enableBinding('{$binding.id}')" title="Enable Binding">
                                                        <i class="fa fa-play"></i>
                                                    </button>
                                                {/if}
                                                <button type="button" class="btn-action btn-remove" onclick="removeBinding('{$binding.id}', '{$binding.mac_address}', '{$binding.address}')" title="Remove Binding">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                {/foreach}
                            {else}
                                <tr>
                                    <td colspan="8" class="text-center table-empty">
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
{literal}
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
{/literal}

{literal}
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

function removeBinding(bindingId, macAddress, ipAddress) {
    var router_id = $('#router_select').val();
    if (!router_id) {
        alert('Please select a router');
        return;
    }
    
    // Confirm deletion
    if (!confirm('Are you sure you want to remove this IP binding?\n\nMAC: ' + macAddress + '\nIP: ' + ipAddress + '\n\nThis action cannot be undone.')) {
        return;
    }
    
    // Submit removal request
    $.post('', {
        action: 'remove_binding',
        router_id: router_id,
        binding_id: bindingId,
        mac_address: macAddress,
        address: ipAddress
    }, function(response) {
        if (response.success) {
            alert('IP Binding removed successfully!');
            refreshData(); // Refresh the data to show updated list
        } else {
            alert('Error removing binding: ' + (response.error || 'Unknown error occurred'));
        }
    }, 'json').fail(function() {
        alert('Network error occurred while removing binding');
    });
}

function enableBinding(bindingId) {
    var router_id = $('#router_select').val();
    if (!router_id) {
        alert('Please select a router');
        return;
    }
    
    // Submit enable request
    $.post('', {
        action: 'enable_binding',
        router_id: router_id,
        binding_id: bindingId
    }, function(response) {
        if (response.success) {
            alert('IP Binding enabled successfully!');
            refreshData(); // Refresh the data to show updated status
        } else {
            alert('Error enabling binding: ' + (response.error || 'Unknown error occurred'));
        }
    }, 'json').fail(function() {
        alert('Network error occurred while enabling binding');
    });
}

function disableBinding(bindingId) {
    var router_id = $('#router_select').val();
    if (!router_id) {
        alert('Please select a router');
        return;
    }
    
    // Submit disable request
    $.post('', {
        action: 'disable_binding',
        router_id: router_id,
        binding_id: bindingId
    }, function(response) {
        if (response.success) {
            alert('IP Binding disabled successfully!');
            refreshData(); // Refresh the data to show updated status
        } else {
            alert('Error disabling binding: ' + (response.error || 'Unknown error occurred'));
        }
    }, 'json').fail(function() {
        alert('Network error occurred while disabling binding');
    });
}

function editBinding(bindingId, macAddress, address, toAddress, server, type, comment, status) {
    var router_id = $('#router_select').val();
    if (!router_id) {
        alert('Please select a router first');
        return;
    }
    
    // Populate edit form with current values
    $('#edit_binding_id').val(bindingId);
    $('#edit_binding_mac_address').val(macAddress);
    $('#edit_binding_address').val(address);
    $('#edit_binding_to_address').val(toAddress);
    $('#edit_binding_server').val(server);
    $('#edit_binding_type').val(type);
    $('#edit_binding_comment').val(comment);
    $('#edit_binding_disabled').prop('checked', status === 'disabled');
    
    // Populate server dropdown for edit modal
    populateEditServerDropdown();
    
    // Show modal
    $('#editBindingModal').modal('show');
}

function submitEditBinding() {
    var router_id = $('#router_select').val();
    if (!router_id) {
        alert('Please select a router');
        return;
    }
    
    // Validate required fields
    var mac_address = $('#edit_binding_mac_address').val().trim();
    var address = $('#edit_binding_address').val().trim();
    
    if (!mac_address) {
        alert('MAC Address is required');
        $('#edit_binding_mac_address').focus();
        return;
    }
    
    if (!address) {
        alert('IP Address is required');
        $('#edit_binding_address').focus();
        return;
    }
    
    // Validate MAC address format
    var macPattern = /^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/;
    if (!macPattern.test(mac_address)) {
        alert('Please enter a valid MAC address (XX:XX:XX:XX:XX:XX)');
        $('#edit_binding_mac_address').focus();
        return;
    }
    
    // Validate IP address format
    var ipPattern = /^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$/;
    if (!ipPattern.test(address)) {
        alert('Please enter a valid IP address');
        $('#edit_binding_address').focus();
        return;
    }
    
    // Validate to_address if provided
    var to_address = $('#edit_binding_to_address').val().trim();
    if (to_address && !ipPattern.test(to_address)) {
        alert('Please enter a valid To Address IP');
        $('#edit_binding_to_address').focus();
        return;
    }
    
    // Prepare form data
    var formData = {
        action: 'edit_binding',
        router_id: router_id,
        binding_id: $('#edit_binding_id').val(),
        mac_address: mac_address,
        address: address,
        to_address: to_address,
        server: $('#edit_binding_server').val(),
        type: $('#edit_binding_type').val(),
        comment: $('#edit_binding_comment').val().trim(),
        disabled: $('#edit_binding_disabled').is(':checked') ? 'true' : 'false'
    };
    
    // Show loading
    var submitBtn = $('#editBindingModal').find('button[onclick="submitEditBinding()"]');
    var originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
    
    // Submit via AJAX
    $.post('', formData, function(response) {
        submitBtn.html(originalText).prop('disabled', false);
        
        if (response.success) {
            $('#editBindingModal').modal('hide');
            alert('IP Binding updated successfully!');
            refreshData(); // Refresh the data to show updated binding
        } else {
            alert('Error: ' + (response.error || 'Failed to update binding'));
        }
    }, 'json').fail(function() {
        submitBtn.html(originalText).prop('disabled', false);
        alert('Network error occurred while updating binding');
    });
}

function populateEditServerDropdown() {
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
    
    var serverSelect = $('#edit_binding_server');
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
{/literal}

{literal}
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
            
            // Create action buttons based on status
            var actionButtons = '<button type="button" class="btn btn-info btn-xs" onclick="editBinding(\'' + binding.id + '\', \'' + binding.mac_address + '\', \'' + binding.address + '\', \'' + binding.to_address + '\', \'' + binding.server + '\', \'' + binding.type + '\', \'' + binding.comment + '\', \'' + binding.status + '\')" title="Edit Binding">' +
                               '<i class="glyphicon glyphicon-edit"></i></button> ';
            if (binding.status === 'active') {
                actionButtons += '<button type="button" class="btn btn-warning btn-xs" onclick="disableBinding(\'' + binding.id + '\')" title="Disable Binding">' +
                               '<i class="glyphicon glyphicon-pause"></i></button> ';
            } else {
                actionButtons += '<button type="button" class="btn btn-success btn-xs" onclick="enableBinding(\'' + binding.id + '\')" title="Enable Binding">' +
                               '<i class="glyphicon glyphicon-play"></i></button> ';
            }
            actionButtons += '<button type="button" class="btn btn-danger btn-xs" onclick="removeBinding(\'' + binding.id + '\', \'' + binding.mac_address + '\', \'' + binding.address + '\')" title="Remove Binding">' +
                            '<i class="glyphicon glyphicon-trash"></i></button>';
            
            var row = '<tr>' +
                '<td style="font-family: \'Courier New\', monospace; font-size: 13px; padding: 8px;">' + binding.mac_address + '</td>' +
                '<td style="font-family: \'Courier New\', monospace; font-size: 13px; padding: 8px;">' + binding.address + '</td>' +
                '<td style="font-family: \'Courier New\', monospace; font-size: 13px; padding: 8px;">' + binding.to_address + '</td>' +
                '<td style="font-size: 13px; padding: 8px;">' + binding.server + '</td>' +
                '<td style="font-size: 13px; padding: 8px;">' + binding.type + '</td>' +
                '<td style="padding: 8px;"><span class="label label-' + statusClass + '" style="font-size: 11px;">' + statusText + '</span></td>' +
                '<td style="font-size: 13px; padding: 8px;">' + binding.comment + '</td>' +
                '<td style="padding: 8px; text-align: center;"><div class="btn-group" style="display: inline-block;">' + actionButtons + '</div></td>' +
                '</tr>';
            tbody.append(row);
        });
    } else {
        tbody.append('<tr><td colspan="8" class="text-center" style="padding: 20px; font-size: 14px;">No IP bindings found for this router.</td></tr>');
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
{/literal}
</script>

<!-- Custom CSS for better responsiveness and clean colors -->
<style>
/* Clean, modern color scheme */
.ip-bindings-container {
    background: #f8f9fa;
    min-height: 100vh;
}

.stats-card {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 20px;
    box-shadow: 
        0 10px 40px rgba(0, 0, 0, 0.08),
        0 4px 20px rgba(0, 0, 0, 0.05),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
    margin-bottom: 25px;
    overflow: hidden;
    position: relative;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: var(--card-gradient);
    border-radius: 20px 20px 0 0;
}

.stats-card:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 
        0 20px 60px rgba(0, 0, 0, 0.15),
        0 8px 30px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 1);
}

.stats-card-body {
    padding: 2rem 1.75rem;
}

.stats-number {
    font-size: 2.5rem;
    font-weight: 800;
    margin: 0.5rem 0;
    color: #2c3e50;
    line-height: 1.2;
    display: block;
    width: 100%;
}

.stats-label {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.75rem;
    color: #64748b;
}

.stats-icon {
    font-size: 3rem;
    opacity: 0.15;
    color: var(--icon-color);
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
}

/* HD Color themes for stats cards */
.card-primary { 
    --card-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --number-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --icon-color: #667eea;
    background: linear-gradient(145deg, #ffffff 0%, #f7f8fc 100%);
}

.card-primary .stats-number {
    color: #667eea;
}

.card-success { 
    --card-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --number-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --icon-color: #11998e;
    background: linear-gradient(145deg, #ffffff 0%, #f0fff4 100%);
}

.card-success .stats-number {
    color: #11998e;
}

.card-warning { 
    --card-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --number-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --icon-color: #f093fb;
    background: linear-gradient(145deg, #ffffff 0%, #fff5f5 100%);
}

.card-warning .stats-number {
    color: #f093fb;
}

.card-info { 
    --card-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --number-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --icon-color: #4facfe;
    background: linear-gradient(145deg, #ffffff 0%, #f0f9ff 100%);
}

.card-info .stats-number {
    color: #4facfe;
}

/* Modern table styling */
.modern-table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.modern-table .table {
    margin: 0;
    border: none;
}

.modern-table .table thead th {
    background: #f1f3f5;
    border: none;
    font-weight: 600;
    color: #495057;
    padding: 1rem 0.75rem;
    font-size: 0.875rem;
}

.modern-table .table tbody td {
    border-top: 1px solid #e9ecef;
    padding: 0.875rem 0.75rem;
    vertical-align: middle;
}

.modern-table .table tbody tr:hover {
    background-color: #f8f9fa;
}

/* Action buttons styling */
.action-buttons {
    display: flex;
    gap: 4px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-action {
    padding: 4px 8px;
    border-radius: 4px;
    border: none;
    font-size: 12px;
    min-width: 32px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.btn-edit { background: #007bff; color: white; }
.btn-edit:hover { background: #0056b3; color: white; }

.btn-enable { background: #28a745; color: white; }
.btn-enable:hover { background: #1e7e34; color: white; }

.btn-disable { background: #ffc107; color: #212529; }
.btn-disable:hover { background: #e0a800; color: #212529; }

.btn-remove { background: #dc3545; color: white; }
.btn-remove:hover { background: #c82333; color: white; }

/* Status labels */
.status-active {
    background: #d4edda;
    color: #155724;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.status-disabled {
    background: #fff3cd;
    color: #856404;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

/* Search and filter section */
.filters-section {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.filter-group {
    margin-bottom: 1rem;
}

.filter-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    display: block;
}

/* Header buttons */
.header-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
}

.btn-header {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-refresh { background: #007bff; color: white; border: none; }
.btn-refresh:hover { background: #0056b3; color: white; }

.btn-export { background: #28a745; color: white; border: none; }
.btn-export:hover { background: #1e7e34; color: white; }

.btn-add { background: #17a2b8; color: white; border: none; }
.btn-add:hover { background: #138496; color: white; }

/* Router selector */
.router-selector {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 14px;
    min-width: 200px;
    transition: border-color 0.2s ease;
}

.router-selector:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

/* Responsive breakpoints */
@media (max-width: 1200px) {
    .stats-card-body { padding: 1.25rem; }
    .stats-number { font-size: 1.75rem; }
}

@media (max-width: 992px) {
    .stats-card { margin-bottom: 15px; }
    .header-buttons { justify-content: center; margin-bottom: 15px; }
    .router-selector { min-width: 180px; }
}

@media (max-width: 768px) {
    .stats-card-body { padding: 1rem; }
    .stats-number { font-size: 1.5rem; }
    .stats-label { font-size: 0.75rem; }
    .stats-icon { font-size: 2rem; }
    
    .modern-table .table thead th,
    .modern-table .table tbody td {
        padding: 0.5rem 0.375rem;
        font-size: 0.8rem;
    }
    
    .btn-action {
        padding: 2px 6px;
        min-width: 28px;
        height: 24px;
        font-size: 11px;
    }
    
    .action-buttons {
        gap: 2px;
    }
    
    .filters-section {
        padding: 1rem;
    }
    
    .btn-header {
        padding: 6px 12px;
        font-size: 12px;
    }
    
    .router-selector {
        min-width: 150px;
        font-size: 12px;
    }
}

@media (max-width: 576px) {
    .stats-number { font-size: 1.25rem; }
    .stats-label { font-size: 0.7rem; }
    
    .header-buttons {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-header {
        width: 100%;
        justify-content: center;
    }
    
    .router-selector {
        width: 100%;
        margin-bottom: 10px;
    }
    
    .filters-section .row {
        margin: 0;
    }
    
    .filters-section .col-md-6,
    .filters-section .col-md-3 {
        padding: 0;
        margin-bottom: 15px;
    }
}

/* Loading and error states */
.loading-spinner {
    color: #007bff;
}

.error-message {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    border-radius: 6px;
    padding: 1rem;
}

/* Modal improvements */
.modal-content {
    border-radius: 8px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.modal-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    border-radius: 8px 8px 0 0;
}

.modal-footer {
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    border-radius: 0 0 8px 8px;
}
/* Modal styling */
.modern-modal-header {
    padding: 20px 25px;
}

.modern-modal-header .modal-title {
    font-size: 18px;
    font-weight: 600;
    color: white;
}

.modern-modal-header .close {
    color: white;
    opacity: 0.8;
    font-size: 24px;
    padding: 0;
    margin: 0;
}

.modern-modal-header .close:hover {
    opacity: 1;
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: none;
    border-radius: 8px 8px 0 0;
}

.modal-content {
    border-radius: 8px;
    border: none;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.modal-body {
    padding: 25px;
}

.modal-footer {
    padding: 20px 25px;
    border-top: 1px solid #e9ecef;
}
</style>

<!-- Add IP Binding Modal -->
<div class="modal fade" id="addBindingModal" tabindex="-1" role="dialog" aria-labelledby="addBindingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modern-modal-header">
                <h4 class="modal-title" id="addBindingModalLabel">
                    <i class="fa fa-plus-circle text-primary"></i> Add New IP Binding
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addBindingForm">
                    <div class="form-group">
                        <label for="binding_mac_address">MAC Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="binding_mac_address" name="mac_address" 
                               placeholder="00:11:22:33:44:55" 
                               title="Please enter a valid MAC address (XX:XX:XX:XX:XX:XX)" required>
                        <small class="form-text text-muted">Format: XX:XX:XX:XX:XX:XX</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="binding_address">IP Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="binding_address" name="address" 
                               placeholder="192.168.1.100" 
                               title="Please enter a valid IP address" required>
                        <small class="form-text text-muted">Single IP address to bind</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="binding_to_address">To Address</label>
                        <input type="text" class="form-control" id="binding_to_address" name="to_address" 
                               placeholder="192.168.1.200 (optional)">
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

<!-- Edit IP Binding Modal -->
<div class="modal fade" id="editBindingModal" tabindex="-1" role="dialog" aria-labelledby="editBindingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modern-modal-header">
                <h4 class="modal-title" id="editBindingModalLabel">
                    <i class="fa fa-edit text-info"></i> Edit IP Binding
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editBindingForm">
                    <input type="hidden" id="edit_binding_id" name="binding_id">
                    
                    <div class="form-group">
                        <label for="edit_binding_mac_address">MAC Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_binding_mac_address" name="mac_address" 
                               placeholder="00:11:22:33:44:55" 
                               title="Please enter a valid MAC address (XX:XX:XX:XX:XX:XX)" required>
                        <small class="form-text text-muted">Format: XX:XX:XX:XX:XX:XX</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_binding_address">IP Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_binding_address" name="address" 
                               placeholder="192.168.1.100" 
                               title="Please enter a valid IP address" required>
                        <small class="form-text text-muted">Single IP address to bind</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_binding_to_address">To Address</label>
                        <input type="text" class="form-control" id="edit_binding_to_address" name="to_address" 
                               placeholder="192.168.1.200 (optional)">
                        <small class="form-text text-muted">Optional: End IP for range binding</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_binding_server">Hotspot Server</label>
                        <select class="form-control" id="edit_binding_server" name="server">
                            <option value="all">all</option>
                        </select>
                        <small class="form-text text-muted">Select the hotspot server for this binding</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_binding_type">Binding Type</label>
                        <select class="form-control" id="edit_binding_type" name="type">
                            <option value="regular">regular</option>
                            <option value="bypassed">bypassed</option>
                            <option value="blocked">blocked</option>
                        </select>
                        <small class="form-text text-muted">Type of IP binding</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_binding_comment">Comment</label>
                        <input type="text" class="form-control" id="edit_binding_comment" name="comment" 
                               placeholder="Optional comment">
                        <small class="form-text text-muted">Optional description for this binding</small>
                    </div>
                    
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="edit_binding_disabled" name="disabled"> 
                            Disabled
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="glyphicon glyphicon-remove"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="submitEditBinding()">
                    <i class="glyphicon glyphicon-save"></i> Update Binding
                </button>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
