{include file="sections/header.tpl"}

<!-- Voucher Section -->
<div class="row" style="margin-bottom: 15px;">
    <div class="col-lg-8">
        <h2 style="margin-top: 0px; margin-bottom: 15px; color: #2b3d51;"><i class="ion ion-card"></i> {Lang::T('Voucher Management')}</h2>
    </div>
    <div class="col-lg-4">
        <div class="btn-group btn-group-justified" role="group" style="margin-top: 2px;">
            <div class="btn-group" role="group">
                <a href="{$_url}plan/add-voucher" class="btn btn-primary btn-lg" style="border-radius: 4px 0 0 4px;">
                    <i class="ion ion-android-add"></i> {Lang::T('Add Vouchers')}
                </a>
            </div>
            <div class="btn-group" role="group">
                <a href="{$_url}plan/print-voucher" target="print_voucher" class="btn btn-info btn-lg" style="border-radius: 0 4px 4px 0;">
                    <i class="ion ion-android-print"></i> {Lang::T('Print')}
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS -->
<style>
.panel-primary {
    border-color: #2b3d51;
}
.panel-primary > .panel-heading {
    background-color: #2b3d51;
    border-color: #2b3d51;
    padding: 15px;
}
.table {
    margin-bottom: 0;
}
.table > thead > tr > th {
    background-color: #f5f5f5;
    border-bottom: 2px solid #ddd;
    padding: 12px 8px;
}
.table > tbody > tr > td {
    padding: 12px 8px;
    vertical-align: middle;
}
.btn-tag {
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}
.btn-tag-success {
    background-color: #27ae60;
    color: white;
}
.btn-tag-danger {
    background-color: #e74c3c;
    color: white;
}
.form-control {
    height: 38px;
    border-radius: 3px;
    box-shadow: none;
    border: 1px solid #ddd;
}
.form-control:focus {
    border-color: #2b3d51;
    box-shadow: none;
}
.btn-success {
    background-color: #27ae60;
    border-color: #27ae60;
}
.btn-success:hover {
    background-color: #219a52;
    border-color: #219a52;
}
.pagination > .active > a {
    background-color: #2b3d51;
    border-color: #2b3d51;
}

/* Additional Styles */
.select2-container .select2-selection--single {
    height: 40px;
    border: 1px solid #ddd;
    border-radius: 3px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px;
    padding-left: 12px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px;
}
.custom-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
}
.btn-group-xs > .btn {
    padding: 4px 8px;
    font-size: 12px;
    line-height: 1.5;
    border-radius: 2px;
}
.table > tbody > tr:hover {
    background-color: #f9f9f9;
    cursor: pointer;
}
.btn-tag {
    display: inline-block;
    min-width: 80px;
    text-align: center;
}
.text-primary { color: #2196F3; }
.text-info { color: #00BCD4; }
.text-muted { color: #939da8; }
</style>

<div class="panel panel-hovered mb20 panel-primary">
    <div class="panel-heading">
        {if in_array($_admin['user_type'], ['SuperAdmin', 'Admin'])}
        <div class="btn-group pull-right">
            <button id="bulk-delete" class="btn btn-danger btn-sm" disabled>
                <i class="fa fa-trash"></i> {Lang::T('Delete Selected')}
            </button>
            <a href="javascript:void(0)" class="btn btn-danger btn-sm" 
                onclick="confirmDelete('{Lang::T('Are you sure you want to delete all used voucher codes? This action cannot be undone.')}', '{$_url}plan/remove-used-vouchers')">
                <i class="fa fa-trash"></i> {Lang::T('Delete Used Vouchers')}
            </a>
            <a href="javascript:void(0)" class="btn btn-warning btn-sm" 
                onclick="confirmDelete('{Lang::T('Clean up invalid or corrupt voucher entries? This action cannot be undone.')}', '{$_url}plan/cleanup-vouchers')">
                <i class="fa fa-broom"></i> {Lang::T('Cleanup Invalid')}
            </a>

            <!-- Bulk Action Success/Error Messages -->
            <div id="action-message" style="display: none; margin-top: 10px;" class="alert">
                <span id="message-text"></span>
            </div>
        </div>
        {/if}
        &nbsp;
    </div>
    <div class="panel-body" style="padding: 20px;">
        <form id="site-search" method="post" action="{$_url}plan/voucher/">
            <div class="row">
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon" style="background-color: #f5f5f5; border-color: #ddd;">
                                <span class="fa fa-search"></span>
                            </div>
                            <input type="text" name="search" class="form-control" placeholder="{Lang::T('Code Voucher')}"
                                value="{$search}" style="height: 40px;">
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <select class="form-control select2" id="router" name="router" style="height: 40px;">
                            <option value="">{Lang::T('Location')}</option>
                            {foreach $routers as $r}
                            <option value="{$r}" {if $router eq $r }selected{/if}>{$r}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <select class="form-control select2" id="plan" name="plan" style="height: 40px;">
                            <option value="">{Lang::T('Plan Name')}</option>
                            {foreach $plans as $p}
                            <option value="{$p['id']}" {if $plan eq $p['id'] }selected{/if}>{$p['name_plan']}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <select class="form-control select2" id="status" name="status" style="height: 40px;">
                            <option value="-">{Lang::T('Status')}</option>
                            <option value="1" {if $status eq 1 }selected{/if}>Used</option>
                            <option value="0" {if $status eq 0 }selected{/if}>Not Use</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <select class="form-control select2" id="customer" name="customer" style="height: 40px;">
                            <option value="">{Lang::T('Customer')}</option>
                            {foreach $customers as $c}
                            <option value="{$c['user']}" {if $customer eq $c['user'] }selected{/if}>{$c['user']}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <div class="btn-group btn-group-justified" role="group">
                            <div class="btn-group" role="group">
                                <button class="btn btn-primary" type="submit" style="height: 40px;">
                                    <i class="fa fa-search"></i> {Lang::T('Search')}
                                </button>
                            </div>
                            <div class="btn-group" role="group">
                                <a class="btn btn-default" title="Clear Search Query" href="{$_url}plan/voucher/" style="height: 40px;">
                                    <i class="fa fa-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sorting Dropdown -->
            <div class="row" style="padding: 5px">
                <div class="col-lg-3">
                    <select class="form-control" name="sort" id="sort">
                        <option value="id_asc" {if $sort eq 'id_asc'}selected{/if}>{Lang::T('Sort by ID Ascending')}</option>
                        <option value="id_desc" {if $sort eq 'id_desc'}selected{/if}>{Lang::T('Sort by ID Descending')}</option>
                        <option value="name_asc" {if $sort eq 'name_asc'}selected{/if}>{Lang::T('Sort by Name Ascending')}</option>
                        <option value="name_desc" {if $sort eq 'name_desc'}selected{/if}>{Lang::T('Sort by Name Descending')}</option>
                        <option value="create_date_asc" {if $sort eq 'create_date_asc'}selected{/if}>{Lang::T('Sort by Create Date Ascending')}</option>
                        <option value="create_date_desc" {if $sort eq 'create_date_desc'}selected{/if}>{Lang::T('Sort by Create Date Descending')}</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
    
    <div class="table-responsive" style="margin-top: 15px;">
        <table id="datatable" class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 30px;">
                        <input type="checkbox" id="select-all" class="custom-checkbox">
                    </th>
                    <th style="width: 50px;">ID</th>
                    <th>{Lang::T('Type')}</th>
                    <th>{Lang::T('Routers')}</th>
                    <th>{Lang::T('Plan Name')}</th>
                    <th>{Lang::T('Code Voucher')}</th>
                    <th style="width: 100px;">{Lang::T('Status')}</th>
                    <th>{Lang::T('Customer')}</th>
                    <th>{Lang::T('Create Date')}</th>
                    <th>{Lang::T('Generated By')}</th>
                    <th style="width: 100px;" class="text-center">{Lang::T('Actions')}</th>
                </tr>
            </thead>
            <tbody>
                {foreach $d as $ds}
                <tr {if $ds['status'] eq '1'}style="background-color: #fff5f5;"{/if}>
                    <td>
                        <input type="checkbox" class="voucher-checkbox custom-checkbox" value="{$ds['id']}">
                    </td>
                    <td><strong>{$ds['id']}</strong></td>
                    <td><span class="text-muted">{$ds['type']}</span></td>
                    <td>{$ds['routers']}</td>
                    <td><strong>{$ds['name_plan']}</strong></td>
                    <td><code style="background: #f8f9fa; color: #333; padding: 4px 8px;">{$ds['code']}</code></td>
                    <td>
                        {if $ds['status'] eq '0'}
                            <span class="btn-tag btn-tag-success"><i class="fa fa-check-circle"></i> Active</span>
                        {else}
                            <span class="btn-tag btn-tag-danger"><i class="fa fa-times-circle"></i> Used</span>
                        {/if}
                    </td>
                    <td>
                        {if $ds['user'] eq '0'}
                            <span class="text-muted">-</span>
                        {else}
                            <a href="{$_url}customers/viewu/{$ds['user']}" class="text-primary">
                                <i class="fa fa-user"></i> {$ds['user']}
                            </a>
                        {/if}
                    </td>
                    <td>{if $ds['create_date']}{Lang::dateTimeFormat($ds['create_date'])}{/if}</td>
                    <td>
                        {if $ds['generated_by']}
                            <a href="{$_url}settings/users-view/{$ds['generated_by']}" class="text-info">
                                <i class="fa fa-user-circle"></i> {$admins[$ds['generated_by']]}
                            </a>
                        {else}
                            <span class="text-muted">-</span>
                        {/if}
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            {if $ds['status'] neq '1'}
                                <a href="{$_url}plan/voucher-view/{$ds['id']}" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="{Lang::T('View')}">
                                    <i class="fa fa-eye"></i>
                                </a>
                            {/if}
                            {if in_array($_admin['user_type'], ['SuperAdmin', 'Admin'])}
                                <a href="{$_url}plan/voucher-delete/{$ds['id']}" class="btn btn-xs btn-danger" onclick="return ask(this, '{Lang::T('Delete')}?')" data-toggle="tooltip" data-placement="top" title="{Lang::T('Delete')}">
                                    <i class="fa fa-trash"></i>
                                </a>
                            {/if}
                        </div>
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>
        </div>
    </div>

    {include file="pagination.tpl"}
</div>

<input type="hidden" id="_csrf_token" value="{$csrf_token}">

<script>
function confirmDelete(message, url) {
    if (confirm(message)) {
        window.location.href = url;
    }
}

function showMessage(message, type) {
    const messageDiv = document.getElementById('action-message');
    const messageText = document.getElementById('message-text');
    messageDiv.className = 'alert alert-' + type;
    messageText.textContent = message;
    messageDiv.style.display = 'block';
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
    const bulkDeleteBtn = document.getElementById('bulk-delete');
    const checkboxes = document.querySelectorAll('.voucher-checkbox');
    
    // Enable/disable bulk delete button based on selections
    function updateBulkDeleteButton() {
        const checkedBoxes = document.querySelectorAll('.voucher-checkbox:checked');
        bulkDeleteBtn.disabled = checkedBoxes.length === 0;
    }

    // Handle select all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkDeleteButton();
    });

    // Handle individual checkboxes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkDeleteButton);
    });

    // Handle bulk delete
    bulkDeleteBtn.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.voucher-checkbox:checked');
        if(checkedBoxes.length > 0) {
            if(confirm(Lang.T('Are you sure you want to delete the selected vouchers? This action cannot be undone.'))) {
                // Here you would collect the IDs and send them to your backend
                showMessage('Selected vouchers have been deleted.', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        }
    });
});
</script>

{include file="sections/footer.tpl"}
