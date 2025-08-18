{include file="sections/header.tpl"}

<style>
.delete-controls {
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.btn-delete-all {
    background: linear-gradient(145deg, #dc3545, #c82333);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
    margin-right: 10px;
    transition: all 0.2s ease;
}

.btn-delete-all:hover {
    background: linear-gradient(145deg, #c82333, #a71e2a);
    transform: translateY(-1px);
}

.btn-delete-selected {
    background: linear-gradient(145deg, #ffc107, #e0a800);
    color: #212529;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
    margin-right: 10px;
    transition: all 0.2s ease;
}

.btn-delete-selected:hover {
    background: linear-gradient(145deg, #e0a800, #d39e00);
    transform: translateY(-1px);
}

.btn-delete-selected:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.select-all-container {
    margin-bottom: 10px;
}

.checkbox-column {
    width: 40px;
    text-align: center;
}

.records-per-page {
    margin-bottom: 15px;
}

.records-per-page select {
    padding: 5px 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
    margin-left: 8px;
}

.records-info {
    color: #666;
    font-size: 13px;
    margin-top: 5px;
}
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">WhatsApp Message Logs</div>
            <div class="panel-body">
                <form method="post" id="logsForm">
                    <div class="delete-controls">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="records-per-page">
                                    <label for="perPageSelect">
                                        <i class="glyphicon glyphicon-list"></i> Records per page:
                                        <select id="perPageSelect" onchange="changePerPage()">
                                            <option value="10" {if $per_page == 10}selected{/if}>10</option>
                                            <option value="50" {if $per_page == 50}selected{/if}>50</option>
                                            <option value="100" {if $per_page == 100}selected{/if}>100</option>
                                            <option value="150" {if $per_page == 150}selected{/if}>150</option>
                                            <option value="200" {if $per_page == 200}selected{/if}>200</option>
                                            <option value="500" {if $per_page == 500}selected{/if}>500</option>
                                        </select>
                                    </label>
                                    <div class="records-info">
                                        Showing {if $logs|@count > 0}{($page-1)*$per_page+1} to {($page-1)*$per_page+$logs|@count}{else}0{/if} of {$total_logs} records
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="select-all-container">
                                    <label>
                                        <input type="checkbox" id="selectAll"> Select All Logs
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <button type="button" class="btn-delete-selected" id="deleteSelectedBtn" disabled onclick="deleteSelected()">
                                    <i class="glyphicon glyphicon-trash"></i> Delete Selected
                                </button>
                                <button type="button" class="btn-delete-all" onclick="deleteAll()">
                                    <i class="glyphicon glyphicon-remove"></i> Delete All Logs
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="checkbox-column">
                                        <input type="checkbox" id="selectAllHeader">
                                    </th>
                                    <th>ID</th>
                                    <th>Phone Number</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Sender</th>
                                    <th>Response</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach $logs as $log}
                                    <tr>
                                        <td class="checkbox-column">
                                            <input type="checkbox" name="selected_logs[]" value="{$log['id']}" class="log-checkbox">
                                        </td>
                                        <td>{$log['id']}</td>
                                        <td>{$log['phone_number']}</td>
                                        <td>{$log['message']}</td>
                                        <td>
                                            {if $log['status'] eq 'delivered'}
                                                <span class="label label-success">{$log['status']}</span>
                                            {elseif $log['status'] eq 'failed'}
                                                <span class="label label-danger">{$log['status']}</span>
                                            {else}
                                                <span class="label label-warning">{$log['status']}</span>
                                            {/if}
                                        </td>
                                        <td>{$log['sender']}</td>
                                        <td><small>{$log['response']}</small></td>
                                        <td>{$log['created_at']}</td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                </form>

                {if $total_pages > 1}
                <ul class="pagination">
                    {if $page > 1}
                        <li><a href="{$_url}plugin/whatsappGateway_logs&page={$page-1}&per_page={$per_page}">Previous</a></li>
                    {/if}
                    
                    {for $p=1 to $total_pages}
                        <li {if $p eq $page}class="active"{/if}>
                            <a href="{$_url}plugin/whatsappGateway_logs&page={$p}&per_page={$per_page}">{$p}</a>
                        </li>
                    {/for}
                    
                    {if $page < $total_pages}
                        <li><a href="{$_url}plugin/whatsappGateway_logs&page={$page+1}&per_page={$per_page}">Next</a></li>
                    {/if}
                </ul>
                {/if}
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllMain = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const logCheckboxes = document.querySelectorAll('.log-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    
    // Sync both "Select All" checkboxes
    function syncSelectAll() {
        const checkedCount = document.querySelectorAll('.log-checkbox:checked').length;
        const allChecked = checkedCount === logCheckboxes.length && logCheckboxes.length > 0;
        
        selectAllMain.checked = allChecked;
        selectAllHeader.checked = allChecked;
        
        // Enable/disable delete selected button
        deleteSelectedBtn.disabled = checkedCount === 0;
    }
    
    // Handle main "Select All" checkbox
    selectAllMain.addEventListener('change', function() {
        logCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        syncSelectAll();
    });
    
    // Handle header "Select All" checkbox
    selectAllHeader.addEventListener('change', function() {
        logCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        syncSelectAll();
    });
    
    // Handle individual checkboxes
    logCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', syncSelectAll);
    });
    
    // Initial state
    syncSelectAll();
});

function deleteSelected() {
    const checkedBoxes = document.querySelectorAll('.log-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select at least one log to delete.');
        return;
    }
    
    if (confirm('Are you sure you want to delete ' + checkedBoxes.length + ' selected log(s)? This action cannot be undone.')) {
        const form = document.getElementById('logsForm');
        const deleteInput = document.createElement('input');
        deleteInput.type = 'hidden';
        deleteInput.name = 'delete_selected';
        deleteInput.value = '1';
        form.appendChild(deleteInput);
        form.submit();
    }
}

function deleteAll() {
    if (confirm('Are you sure you want to delete ALL WhatsApp logs? This action cannot be undone and will permanently remove all message logs.')) {
        if (confirm('This is your final warning. Are you absolutely sure you want to delete ALL logs?')) {
            const form = document.getElementById('logsForm');
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'delete_all';
            deleteInput.value = '1';
            form.appendChild(deleteInput);
            form.submit();
        }
    }
}

function changePerPage() {
    const perPageSelect = document.getElementById('perPageSelect');
    const perPage = perPageSelect.value;
    const currentUrl = new URL(window.location.href);
    
    // Update the per_page parameter
    currentUrl.searchParams.set('per_page', perPage);
    // Reset to page 1 when changing per page
    currentUrl.searchParams.set('page', '1');
    
    // Redirect to the new URL
    window.location.href = currentUrl.toString();
}
</script>

{include file="sections/footer.tpl"}
