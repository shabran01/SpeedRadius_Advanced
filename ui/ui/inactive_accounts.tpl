{include file="sections/header.tpl"}

<!-- Inactive Accounts Plugin CSS -->
<style>
    .inactive-stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .inactive-stats-card h3 {
        color: white;
        margin-bottom: 15px;
        font-size: 18px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    
    .stat-item {
        background: rgba(255, 255, 255, 0.1);
        padding: 15px;
        border-radius: 8px;
        text-align: center;
    }
    
    .stat-number {
        display: block;
        font-size: 28px;
        font-weight: bold;
        color: #fff;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.8);
        text-transform: uppercase;
    }
    
    .filter-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }
    
    .inactive-reason {
        font-size: 12px;
        color: #dc3545;
        font-style: italic;
    }
    
    .inactive-score {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: bold;
        color: white;
    }
    
    .score-1 { background-color: #ffc107; }
    .score-2 { background-color: #fd7e14; }
    .score-3 { background-color: #dc3545; }
    .score-4 { background-color: #6f42c1; }
    
    .bulk-actions {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: none;
    }
    
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .table th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        font-weight: 600;
        padding: 12px 8px;
        font-size: 13px;
    }
    
    .table td {
        padding: 10px 8px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }
    
    .loading-spinner {
        color: white;
        font-size: 24px;
    }
    
    .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
    }
    
    .text-truncate {
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-primary btn-sm" onclick="refreshData()">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="exportCSV()">
                        <i class="fa fa-download"></i> Export CSV
                    </button>
                </div>
                <h3 class="panel-title">
                    <i class="ion ion-person-stalker"></i> Inactive Hotspot Accounts Monitor
                </h3>
            </div>
            
            <div class="panel-body">
                <!-- Statistics Cards -->
                <div class="inactive-stats-card">
                    <h3><i class="fa fa-bar-chart"></i> Hotspot Activity Statistics</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="stat-number" id="total_customers">0</span>
                            <span class="stat-label">Total Hotspot Customers</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" id="total_inactive">0</span>
                            <span class="stat-label">Inactive Hotspot Accounts</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" id="never_logged_in">0</span>
                            <span class="stat-label">Never Logged In</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" id="completely_inactive">0</span>
                            <span class="stat-label">Completely Inactive</span>
                        </div>
                    </div>
                </div>
                
                <!-- Filter Section -->
                <div class="filter-section">
                    <h4><i class="fa fa-filter"></i> Filters <small class="text-muted">(Hotspot Customers Only)</small></h4>
                    <div class="row">
                        <div class="col-md-4">
                            <label>Days Inactive</label>
                            <select class="form-control" id="days_inactive">
                                <option value="7">7 days</option>
                                <option value="14">14 days</option>
                                <option value="30" selected>30 days</option>
                                <option value="60">60 days</option>
                                <option value="90">90 days</option>
                                <option value="180">180 days</option>
                                <option value="365">1 year</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Account Status</label>
                            <select class="form-control" id="account_status">
                                <option value="all">All Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Disabled">Disabled</option>
                                <option value="Suspended">Suspended</option>
                                <option value="Banned">Banned</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Router</label>
                            <select class="form-control" id="router_filter">
                                <option value="all">All Routers</option>
                                {foreach $routers as $router}
                                    <option value="{$router.id}">{$router.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 15px;">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                <i class="fa fa-search"></i> Apply Filters
                            </button>
                            <button type="button" class="btn btn-default" onclick="resetFilters()">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Bulk Actions -->
                <div class="bulk-actions" id="bulk_actions">
                    <h5><i class="fa fa-tasks"></i> Bulk Actions</h5>
                    <div class="row">
                        <div class="col-md-8">
                            <select class="form-control" id="bulk_action_select">
                                <option value="">Select Action...</option>
                                <option value="disable">Disable Accounts</option>
                                <option value="activate">Activate Accounts</option>
                                <option value="suspend">Suspend Accounts</option>
                                <option value="delete">Delete Accounts (SuperAdmin only)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-warning" onclick="executeBulkAction()">
                                <i class="fa fa-cogs"></i> Execute Action
                            </button>
                            <button type="button" class="btn btn-default" onclick="clearSelection()">
                                <i class="fa fa-times"></i> Clear
                            </button>
                        </div>
                    </div>
                    <div style="margin-top: 10px;">
                        <small class="text-muted">
                            <span id="selected_count">0</span> accounts selected
                        </small>
                    </div>
                </div>
                
                <!-- Results Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="select_all" onchange="toggleSelectAll()">
                                </th>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Contact</th>
                                <th>Account Type</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Last Login</th>
                                <th>Last Recharge</th>
                                <th>Inactive Reason</th>
                                <th>Score</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="inactive_accounts_tbody">
                            <!-- Data will be populated by JavaScript on page load -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Last Updated Info -->
                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-12">
                        <small class="text-muted">
                            <i class="fa fa-clock-o"></i> Last updated: <span id="last_updated">Loading...</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loading_overlay">
    <div class="loading-spinner">
        <i class="fa fa-spinner fa-spin fa-3x"></i>
        <div style="margin-top: 10px;">Processing...</div>
    </div>
</div>

<!-- JavaScript -->
<script>
let selectedAccounts = new Set();

$(document).ready(function() {
    // Load initial data from server-side
    {if $inactive_data.success}
        var statsData = {$inactive_data.stats|@json_encode};
        var accountsData = {$inactive_data.data|@json_encode};
        var lastUpdated = '{$inactive_data.last_updated}';
        
        console.log('Server data loaded successfully');
        console.log('Stats:', statsData);
        console.log('Accounts count:', accountsData.length);
        
        updateStatistics(statsData);
        updateAccountsTable(accountsData);
        updateLastUpdated(lastUpdated);
        
        if (accountsData.length > 0) {
            showAlert('success', 'Loaded ' + accountsData.length + ' inactive hotspot accounts');
        } else {
            showAlert('info', 'No inactive hotspot accounts found. All accounts seem to be active!');
        }
    {else}
        console.error('Server error:', '{$inactive_data.error}');
        showAlert('danger', 'Error loading data: {$inactive_data.error}');
        updateAccountsTable([]);
    {/if}
});

function showLoading(show = true) {
    const overlay = document.getElementById('loading_overlay');
    overlay.style.display = show ? 'flex' : 'none';
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible" role="alert">' +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
        '<span aria-hidden="true">&times;</span>' +
        '</button>' +
        message +
        '</div>';
    
    // Remove existing alerts
    $('.alert').remove();
    
    // Add new alert at the top of the panel body
    $('.panel-body').prepend(alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}

function refreshData() {
    applyFilters();
}

function applyFilters() {
    showLoading(true);
    
    const days_inactive = $('#days_inactive').val();
    const account_status = $('#account_status').val();
    const router_filter = $('#router_filter').val();
    
    $.post('', {
        action: 'get_inactive_accounts',
        days_inactive: days_inactive,
        account_status: account_status,
        service_type: 'Hotspot', // Always Hotspot only
        router_filter: router_filter
    })
    .done(function(response) {
        if (response.success) {
            updateStatistics(response.stats);
            updateAccountsTable(response.data);
            updateLastUpdated(response.last_updated);
            clearSelection();
        } else {
            showAlert('danger', 'Error: ' + response.error);
        }
    })
    .fail(function() {
        showAlert('danger', 'Failed to load inactive accounts. Please try again.');
    })
    .always(function() {
        showLoading(false);
    });
}

function resetFilters() {
    $('#days_inactive').val('30');
    $('#account_status').val('all');
    $('#router_filter').val('all');
    applyFilters();
}

function updateStatistics(stats) {
    $('#total_customers').text(stats.total_customers || 0);
    $('#total_inactive').text(stats.total_inactive || 0);
    $('#never_logged_in').text(stats.never_logged_in || 0);
    $('#completely_inactive').text(stats.completely_inactive || 0);
}

function updateAccountsTable(accounts) {
    console.log('Updating accounts table with', accounts.length, 'accounts');
    const tbody = $('#inactive_accounts_tbody');
    tbody.empty();
    
    if (!accounts || accounts.length === 0) {
        tbody.html('<tr><td colspan="12" class="text-center">' +
            '<div style="padding: 20px;">' +
            '<i class="fa fa-info-circle fa-2x text-muted" style="margin-bottom: 10px;"></i><br>' +
            'No inactive hotspot accounts found with current filters.<br>' +
            '<small class="text-muted">Try adjusting the filter settings or check if you have hotspot customers.</small>' +
            '</div>' +
            '</td></tr>');
        return;
    }
    
    accounts.forEach(function(account) {
        const row = '<tr>' +
            '<td>' +
                '<input type="checkbox" class="account-checkbox" value="' + account.id + '" onchange="updateSelection()">' +
            '</td>' +
            '<td>' +
                '<strong>' + account.username + '</strong>' +
                '<br><small class="text-muted">#' + account.id + '</small>' +
            '</td>' +
            '<td class="text-truncate" title="' + account.fullname + '">' +
                account.fullname +
            '</td>' +
            '<td>' +
                '<small>' +
                    account.email + '<br>' +
                    account.phonenumber +
                '</small>' +
            '</td>' +
            '<td>' +
                '<span class="label label-primary">Hotspot</span>' +
                '<br><small>' + account.account_type + '</small>' +
            '</td>' +
            '<td>' +
                '<span class="label ' + getStatusClass(account.status) + '">' + account.status + '</span>' +
                '<br><small>Bal: ' + account.balance + '</small>' +
            '</td>' +
            '<td>' +
                '<small>' + formatDate(account.created_at) + '</small>' +
                (account.days_since_created ? '<br><span class="text-muted">' + account.days_since_created + ' days ago</span>' : '') +
            '</td>' +
            '<td>' +
                '<small>' + (account.last_login ? formatDate(account.last_login) : 'Never') + '</small>' +
                (account.days_since_login ? '<br><span class="text-muted">' + account.days_since_login + ' days ago</span>' : '') +
            '</td>' +
            '<td>' +
                '<small>' + (account.last_recharge_date ? formatDate(account.last_recharge_date) : 'Never') + '</small>' +
                (account.days_since_recharge ? '<br><span class="text-muted">' + account.days_since_recharge + ' days ago</span>' : '') +
            '</td>' +
            '<td>' +
                '<div class="inactive-reason">' + account.inactive_reason + '</div>' +
            '</td>' +
            '<td>' +
                '<span class="inactive-score score-' + account.inactive_score + '">' + account.inactive_score + '</span>' +
            '</td>' +
            '<td>' +
                '<div class="btn-group btn-group-xs">' +
                    '<a href="{$_url}customers/edit/' + account.id + '" class="btn btn-info" title="Edit">' +
                        '<i class="fa fa-edit"></i>' +
                    '</a>' +
                    '<button type="button" class="btn btn-warning" onclick="toggleAccountStatus(' + account.id + ', \'' + account.status + '\')" title="Toggle Status">' +
                        '<i class="fa fa-power-off"></i>' +
                    '</button>' +
                '</div>' +
            '</td>' +
        '</tr>';
        tbody.append(row);
    });
}

function getStatusClass(status) {
    switch (status) {
        case 'Active': return 'label-success';
        case 'Inactive': return 'label-warning';
        case 'Disabled': return 'label-danger';
        case 'Suspended': return 'label-info';
        case 'Banned': return 'label-danger';
        default: return 'label-default';
    }
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}

function updateLastUpdated(timestamp) {
    $('#last_updated').text(timestamp || 'Unknown');
}

function toggleSelectAll() {
    const selectAll = $('#select_all').is(':checked');
    $('.account-checkbox').prop('checked', selectAll);
    updateSelection();
}

function updateSelection() {
    selectedAccounts.clear();
    $('.account-checkbox:checked').each(function() {
        selectedAccounts.add($(this).val());
    });
    
    $('#selected_count').text(selectedAccounts.size);
    
    if (selectedAccounts.size > 0) {
        $('#bulk_actions').show();
    } else {
        $('#bulk_actions').hide();
    }
}

function clearSelection() {
    selectedAccounts.clear();
    $('.account-checkbox').prop('checked', false);
    $('#select_all').prop('checked', false);
    $('#bulk_actions').hide();
    $('#selected_count').text('0');
}

function executeBulkAction() {
    const action = $('#bulk_action_select').val();
    if (!action) {
        showAlert('danger', 'Please select an action first');
        return;
    }
    
    if (selectedAccounts.size === 0) {
        showAlert('danger', 'Please select accounts first');
        return;
    }
    
    const actionNames = {
        'disable': 'disable',
        'activate': 'activate', 
        'suspend': 'suspend',
        'delete': 'delete'
    };
    
    if (!confirm('Are you sure you want to ' + actionNames[action] + ' ' + selectedAccounts.size + ' selected accounts?')) {
        return;
    }
    
    showLoading(true);
    
    $.post('', {
        action: 'bulk_action',
        bulk_action: action,
        selected_ids: Array.from(selectedAccounts).join(',')
    })
    .done(function(response) {
        if (response.success) {
            showAlert('success', response.message);
            refreshData();
        } else {
            showAlert('danger', response.message);
        }
    })
    .fail(function() {
        showAlert('danger', 'Failed to execute bulk action. Please try again.');
    })
    .always(function() {
        showLoading(false);
    });
}

function exportCSV() {
    const days_inactive = $('#days_inactive').val();
    const account_status = $('#account_status').val();
    const router_filter = $('#router_filter').val();
    
    // Create a hidden form to submit the export request
    const form = $('<form>', {
        method: 'POST',
        action: '',
        style: 'display: none;'
    });
    
    form.append($('<input>', { name: 'action', value: 'export_csv' }));
    form.append($('<input>', { name: 'days_inactive', value: days_inactive }));
    form.append($('<input>', { name: 'account_status', value: account_status }));
    form.append($('<input>', { name: 'service_type', value: 'Hotspot' })); // Always Hotspot
    form.append($('<input>', { name: 'router_filter', value: router_filter }));
    
    $('body').append(form);
    form.submit();
    form.remove();
    
    showAlert('success', 'CSV export started. File will download shortly.');
}

function toggleAccountStatus(customerId, currentStatus) {
    const newStatus = currentStatus === 'Active' ? 'Disabled' : 'Active';
    
    if (!confirm('Change account status from ' + currentStatus + ' to ' + newStatus + '?')) {
        return;
    }
    
    // This would need to be implemented as a separate action
    // For now, just show a message
    showAlert('info', 'Account status toggle would be implemented here. Use bulk actions for now.');
}
</script>

{include file="sections/footer.tpl"}
