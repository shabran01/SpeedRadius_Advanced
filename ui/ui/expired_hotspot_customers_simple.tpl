{include file="sections/header.tpl"}

<style>
/* Enhanced Progress Bar Styles */
.deletion-progress-container {
    background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
    border-radius: 10px;
    padding: 20px;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

.deletion-progress-bar {
    background: linear-gradient(135deg, #d9534f 0%, #c9302c 50%, #ac2925 100%);
    border-radius: 8px;
    position: relative;
    overflow: hidden;
    transition: width 0.3s ease-in-out;
}

.deletion-progress-bar::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, 
        rgba(255,255,255,0.2) 25%, 
        transparent 25%, 
        transparent 50%, 
        rgba(255,255,255,0.2) 50%, 
        rgba(255,255,255,0.2) 75%, 
        transparent 75%, 
        transparent);
    background-size: 20px 20px;
    animation: progress-stripes 1s linear infinite;
}

@keyframes progress-stripes {
    0% { background-position: 0 0; }
    100% { background-position: 20px 0; }
}

.deletion-status-icon {
    animation: pulse 1.5s infinite;
    color: #d9534f;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.deletion-details-table {
    font-size: 12px;
}

.deletion-details-table td {
    padding: 5px 8px;
}
</style>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-clock-o"></i> Expired Hotspot Customers
                </h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-sm btn-default" onclick="window.location.reload()">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box bg-red">
                            <span class="info-box-icon"><i class="fa fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Expired Hotspot</span>
                                <span class="info-box-number">{$total_count}</span>
                                <span class="info-box-more">Customers</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-orange">
                            <span class="info-box-icon"><i class="fa fa-calendar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Current Page</span>
                                <span class="info-box-number">{$current_page}</span>
                                <span class="info-box-more">of {$total_pages} pages</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-blue">
                            <span class="info-box-icon"><i class="fa fa-list"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Per Page</span>
                                <span class="info-box-number">{$per_page}</span>
                                <span class="info-box-more">Records</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {if $customers}
            <div class="box-body">
                <!-- Bulk Actions Form -->
                <form id="bulk-form" method="post" action="{$_url}plugin/expired_hotspot_customers_simple">
                    <input type="hidden" name="bulk_delete_action" value="delete_selected">
                    <input type="hidden" name="selected_ids" id="selected_ids_input" value="">
                    
                    <!-- Bulk Actions Bar -->
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" id="select-all" onchange="toggleSelectAll()"> 
                                    Select All on this page
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-danger" id="bulk-delete-btn" onclick="confirmBulkDelete()" disabled>
                                <i class="fa fa-trash"></i> Delete Selected (<span id="selected-count">0</span>)
                            </button>
                        </div>
                    </div>
                </form>
                
                <!-- Customers Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="40px">
                                    <input type="checkbox" id="header-select-all" onchange="toggleSelectAll()">
                                </th>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Plan</th>
                                <th>Phone</th>
                                <th>Router</th>
                                <th>Recharged</th>
                                <th>Expired</th>
                                <th>Days Expired</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $customers as $customer}
                            <tr data-id="{$customer.id}">
                                <td>
                                    <input type="checkbox" class="customer-checkbox" value="{$customer.id}" onchange="updateSelectedCount()">
                                </td>
                                <td>
                                    <strong>{$customer.username}</strong>
                                    <br>
                                    <small class="text-muted">ID: {$customer.customer_id}</small>
                                </td>
                                <td>
                                    {if $customer.is_orphaned}
                                        <span class="text-warning">Customer Deleted</span>
                                        <br>
                                        <small class="text-muted">Orphaned Record</small>
                                    {else}
                                        {$customer.fullname}
                                    {/if}
                                </td>
                                <td>
                                    <span class="label label-primary">{$customer.plan_name}</span>
                                </td>
                                <td>
                                    {if $customer.phone && $customer.phone != '0'}
                                        {$customer.phone}
                                    {else}
                                        <span class="text-muted">N/A</span>
                                    {/if}
                                </td>
                                <td>
                                    <span class="label label-info">{$customer.router}</span>
                                </td>
                                <td>
                                    <small>{$customer.recharged_on}</small>
                                </td>
                                <td>
                                    <span class="text-danger">{$customer.expiration}</span>
                                </td>
                                <td>
                                    <span class="label label-danger">{$customer.days_expired} days</span>
                                </td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                {if $total_pages > 1}
                <div class="box-footer clearfix">
                    <ul class="pagination pagination-sm no-margin pull-right">
                        {if $has_prev}
                            <li><a href="?page=1">&laquo; First</a></li>
                            <li><a href="?page={$prev_page}">&lsaquo; Prev</a></li>
                        {else}
                            <li class="disabled"><span>&laquo; First</span></li>
                            <li class="disabled"><span>&lsaquo; Prev</span></li>
                        {/if}
                        
                        {foreach $pages as $page_num}
                            {if $page_num == $current_page}
                                <li class="active"><span>{$page_num}</span></li>
                            {else}
                                <li><a href="?page={$page_num}">{$page_num}</a></li>
                            {/if}
                        {/foreach}
                        
                        {if $has_next}
                            <li><a href="?page={$next_page}">Next &rsaquo;</a></li>
                            <li><a href="?page={$total_pages}">Last &raquo;</a></li>
                        {else}
                            <li class="disabled"><span>Next &rsaquo;</span></li>
                            <li class="disabled"><span>Last &raquo;</span></li>
                        {/if}
                    </ul>
                    <div class="pull-left">
                        <small class="text-muted">
                            Showing {(($current_page - 1) * $per_page) + 1} to {min($current_page * $per_page, $total_count)} 
                            of {$total_count} entries
                        </small>
                    </div>
                </div>
                {/if}
            </div>
            {else}
            <div class="box-body">
                <div class="alert alert-info text-center">
                    <h4><i class="fa fa-info-circle"></i> No Expired Hotspot Customers Found</h4>
                    <p>There are currently no expired hotspot customers in the system.</p>
                </div>
            </div>
            {/if}
        </div>
    </div>
</div>

<!-- Loading Modal with Progress -->
<div class="modal fade" id="loading-modal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-trash text-danger"></i> Deleting Expired Customers
                </h4>
            </div>
            <div class="modal-body">
                <div class="deletion-progress-container">
                    <div class="progress" style="margin-bottom: 20px; height: 25px; background: #f0f0f0;">
                        <div class="progress-bar deletion-progress-bar progress-bar-striped active" 
                             id="deletion-progress" role="progressbar" 
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%; line-height: 25px;">
                            <span id="progress-text" style="font-weight: bold; text-shadow: 1px 1px 1px rgba(0,0,0,0.3);">0%</span>
                        </div>
                    </div>
                </div>
                
                <div id="deletion-status" class="text-center">
                    <p><i class="fa fa-spinner fa-spin deletion-status-icon"></i> <span id="status-text">Preparing deletion...</span></p>
                    <p class="text-muted"><span id="progress-details">Please wait while we process your request.</span></p>
                </div>
                
                <div id="deletion-results" style="display: none;">
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i> 
                        <strong>Success!</strong> <span id="success-message"></span>
                    </div>
                    <div id="warning-section" style="display: none;">
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i> 
                            <strong>Warnings:</strong>
                            <ul id="warning-list"></ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="modal-footer" style="display: none;">
                <button type="button" class="btn btn-success" onclick="window.location.reload()">
                    <i class="fa fa-refresh"></i> Refresh Page
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirm-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-warning text-danger"></i> Confirm Bulk Deletion
                </h4>
            </div>
            <div class="modal-body">
                <p><strong>Warning:</strong> You are about to permanently delete <span id="confirm-count"></span> expired hotspot customer records.</p>
                <p>This action cannot be undone. Are you sure you want to continue?</p>
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>Note:</strong> Only the user recharge records will be deleted. Customer profile information will be retained.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitBulkDelete()">
                    <i class="fa fa-trash"></i> Yes, Delete Them
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var selectedIds = [];

function toggleSelectAll() {
    var selectAllChecked = document.getElementById('select-all').checked;
    var headerSelectAllChecked = document.getElementById('header-select-all').checked;
    
    // Sync both select all checkboxes
    if (event.target.id === 'select-all') {
        document.getElementById('header-select-all').checked = selectAllChecked;
    } else {
        document.getElementById('select-all').checked = headerSelectAllChecked;
        selectAllChecked = headerSelectAllChecked;
    }
    
    // Toggle all customer checkboxes
    var checkboxes = document.querySelectorAll('.customer-checkbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = selectAllChecked;
    });
    
    updateSelectedCount();
}

function updateSelectedCount() {
    var checkboxes = document.querySelectorAll('.customer-checkbox:checked');
    var count = checkboxes.length;
    
    document.getElementById('selected-count').textContent = count;
    document.getElementById('bulk-delete-btn').disabled = count === 0;
    
    // Update selectedIds array
    selectedIds = [];
    checkboxes.forEach(function(checkbox) {
        selectedIds.push(checkbox.value);
    });
    
    // Update the hidden input
    document.getElementById('selected_ids_input').value = selectedIds.join(',');
    
    // Update select all checkboxes based on individual selections
    var totalCheckboxes = document.querySelectorAll('.customer-checkbox').length;
    var allSelected = count === totalCheckboxes && totalCheckboxes > 0;
    
    document.getElementById('select-all').checked = allSelected;
    document.getElementById('header-select-all').checked = allSelected;
}

function confirmBulkDelete() {
    if (selectedIds.length === 0) {
        alert('Please select at least one customer to delete.');
        return;
    }
    
    document.getElementById('confirm-count').textContent = selectedIds.length;
    $('#confirm-modal').modal('show');
}

function submitBulkDelete() {
    $('#confirm-modal').modal('hide');
    
    // Reset progress modal
    resetProgressModal();
    $('#loading-modal').modal('show');
    
    // Start progress simulation
    simulateProgress();
    
    // Use AJAX to submit the form
    $.ajax({
        url: '{$_url}plugin/expired_hotspot_customers_simple',
        type: 'POST',
        data: {
            bulk_delete_action: 'delete_selected',
            selected_ids: selectedIds.join(',')
        },
        dataType: 'json',
        beforeSend: function() {
            updateProgress(10, 'Sending deletion request...', 'Contacting server...');
        },
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            
            // Upload progress
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = (evt.loaded / evt.total) * 50; // First 50% for upload
                    updateProgress(percentComplete, 'Uploading request...', 'Sending data to server...');
                }
            }, false);
            
            return xhr;
        },
        success: function(response) {
            updateProgress(90, 'Processing response...', 'Analyzing results...');
            
            setTimeout(function() {
                if (response.success) {
                    updateProgress(100, 'Deletion completed!', 'All selected records have been processed.');
                    showResults(response);
                } else {
                    showError('Error: ' + response.message);
                }
            }, 500);
        },
        error: function(xhr, status, error) {
            // Try to parse error response
            try {
                var response = JSON.parse(xhr.responseText);
                showError('Error: ' + response.message);
            } catch(e) {
                showError('Error: Failed to delete customers. Please check the server logs for details.');
            }
            
            console.error('AJAX Error:', xhr.responseText);
        }
    });
}

function resetProgressModal() {
    // Reset progress bar
    $('#deletion-progress').css('width', '0%').attr('aria-valuenow', 0);
    $('#progress-text').text('0%');
    
    // Reset status
    $('#status-text').text('Preparing deletion...');
    $('#progress-details').text('Please wait while we process your request.');
    
    // Hide results and show status
    $('#deletion-status').show();
    $('#deletion-results').hide();
    $('#modal-footer').hide();
    
    // Reset alerts
    $('#warning-section').hide();
    $('#warning-list').empty();
}

function updateProgress(percent, statusText, detailsText) {
    percent = Math.min(100, Math.max(0, percent));
    
    $('#deletion-progress').css('width', percent + '%').attr('aria-valuenow', percent);
    $('#progress-text').text(Math.round(percent) + '%');
    $('#status-text').text(statusText);
    $('#progress-details').text(detailsText);
}

function simulateProgress() {
    // Simulate initial progress while waiting for server response
    var progress = 0;
    var interval = setInterval(function() {
        progress += Math.random() * 15;
        if (progress > 70) {
            clearInterval(interval);
            return;
        }
        
        var messages = [
            'Validating selected records...',
            'Checking permissions...',
            'Preparing database queries...',
            'Processing deletion request...'
        ];
        
        var details = [
            'Verifying that selected customers are expired hotspot users...',
            'Ensuring you have permission to delete these records...',
            'Setting up secure database connections...',
            'Executing deletion commands safely...'
        ];
        
        var messageIndex = Math.floor(progress / 20);
        updateProgress(progress, messages[messageIndex] || 'Processing...', details[messageIndex] || 'Please wait...');
    }, 200);
}

function showResults(response) {
    $('#deletion-status').hide();
    
    // Show success message with details
    var successMessage = response.message;
    if (response.execution_time) {
        successMessage += ' (Completed in ' + response.execution_time + ')';
    }
    $('#success-message').text(successMessage);
    
    // Show detailed results if available
    if (response.processed_users && response.processed_users.length > 0) {
        var detailsHtml = '<div class="panel panel-default" style="margin-top: 15px;">';
        detailsHtml += '<div class="panel-heading"><strong><i class="fa fa-list"></i> Deletion Details (' + response.processed_users.length + ' items)</strong></div>';
        detailsHtml += '<div class="panel-body" style="max-height: 250px; overflow-y: auto; padding: 0;">';
        detailsHtml += '<table class="table table-condensed table-striped deletion-details-table" style="margin: 0;">';
        detailsHtml += '<thead><tr><th>Username</th><th>Expired Date</th><th>Status</th></tr></thead><tbody>';
        
        response.processed_users.forEach(function(user) {
            var statusClass = user.status === 'deleted' ? 'success' : 'danger';
            var statusIcon = user.status === 'deleted' ? 'check' : 'times';
            detailsHtml += '<tr>';
            detailsHtml += '<td><strong>' + user.username + '</strong></td>';
            detailsHtml += '<td>' + user.expiration + '</td>';
            detailsHtml += '<td><span class="label label-' + statusClass + '"><i class="fa fa-' + statusIcon + '"></i> ' + 
                          (user.status === 'deleted' ? 'Deleted' : 'Failed') + '</span></td>';
            detailsHtml += '</tr>';
        });
        
        detailsHtml += '</tbody></table></div></div>';
        $('#success-message').after(detailsHtml);
    }
    
    // Show warnings if any
    if (response.warnings && response.warnings.length > 0) {
        $('#warning-section').show();
        response.warnings.forEach(function(warning) {
            $('#warning-list').append('<li>' + warning + '</li>');
        });
    }
    
    $('#deletion-results').show();
    $('#modal-footer').show();
}

function showError(message) {
    $('#deletion-status').hide();
    
    $('#deletion-results').html(
        '<div class="alert alert-danger">' +
        '<i class="fa fa-exclamation-circle"></i> ' +
        '<strong>Error!</strong> ' + message +
        '</div>'
    ).show();
    
    $('#modal-footer').html(
        '<button type="button" class="btn btn-default" data-dismiss="modal">' +
        '<i class="fa fa-times"></i> Close' +
        '</button>'
    ).show();
}

// Initialize selected count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
});
</script>

{include file="sections/footer.tpl"}
