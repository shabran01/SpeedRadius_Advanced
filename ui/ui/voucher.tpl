{include file="sections/header.tpl"}

<!-- Custom CSS -->
<style>
.panel-primary {
    border-color: transparent;
}
.table {
    margin-bottom: 0;
}
.table > thead > tr > th {
    background: linear-gradient(180deg, #f8fafc, #f1f5f9);
    border-bottom: 2px solid #e2e8f0;
    padding: 12px 10px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #475569;
    white-space: nowrap;
}
.table > tbody > tr > td {
    padding: 12px 10px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13px;
}
.table > tbody > tr:hover {
    background-color: #f8fafc;
}
.btn-tag {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    min-width: 80px;
    text-align: center;
}
.btn-tag-success {
    background: linear-gradient(135deg, #059669, #10b981);
    color: white;
}
.btn-tag-danger {
    background: linear-gradient(135deg, #dc2626, #ef4444);
    color: white;
}
.form-control {
    height: 40px;
    border-radius: 8px;
    box-shadow: none;
    border: 2px solid #e2e8f0;
    background: #f8fafc;
    transition: all 0.2s;
}
.form-control:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    background: #fff;
}
.pagination > .active > a {
    background-color: #4f46e5;
    border-color: #4f46e5;
}
.select2-container .select2-selection--single {
    height: 40px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
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
    accent-color: #4f46e5;
}
.text-primary { color: #4f46e5; }
.text-info { color: #06b6d4; }
.text-muted { color: #94a3b8; }

/* ============================================
   MOBILE RESPONSIVE — Up to 768px (Phones)
   ============================================ */
@media (max-width: 768px) {
    /* Title + Buttons row */
    .voucher-header-row {
        flex-direction: column !important;
        gap: 12px !important;
    }
    .voucher-header-row .col-sm-5,
    .voucher-header-row .col-sm-7 {
        width: 100% !important;
        text-align: center;
    }
    .voucher-header-row h2 {
        font-size: 20px;
        text-align: center;
    }
    .voucher-header-btns {
        flex-wrap: wrap !important;
        gap: 8px !important;
    }
    .voucher-header-btns a {
        flex: 1 1 calc(50% - 4px) !important;
        min-width: 100px !important;
        padding: 12px 8px !important;
        font-size: 12px !important;
        justify-content: center !important;
    }
    .voucher-header-btns a span:first-child {
        font-size: 18px !important;
    }

    /* Panel heading action buttons */
    .voucher-action-btns {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 4px !important;
        float: none !important;
        margin-bottom: 10px;
    }
    .voucher-action-btns .btn {
        flex: 1 1 auto;
        font-size: 10px;
        padding: 6px 8px;
        border-radius: 4px !important;
    }
    .panel-heading-title {
        display: block;
        text-align: center;
        clear: both;
    }

    /* Filter form — stack all fields */
    #site-search .row > div {
        width: 100% !important;
        margin-bottom: 8px;
    }
    #site-search .btn-group-justified {
        display: flex;
        gap: 8px;
    }
    #site-search .btn-group-justified .btn-group {
        flex: 1;
    }
    #site-search .btn-group-justified .btn {
        width: 100%;
    }

    /* Sort dropdown */
    #site-search .col-lg-3 {
        width: 100% !important;
    }

    /* Table — enable horizontal scroll */
    .table-responsive {
        -webkit-overflow-scrolling: touch;
        border: 0;
    }
    .table-responsive table {
        min-width: 900px;
    }
    .table > thead > tr > th {
        font-size: 10px;
        padding: 8px 6px;
    }
    .table > tbody > tr > td {
        font-size: 11px;
        padding: 8px 6px;
    }

    /* Hide less important columns on very small screens */
    @media (max-width: 480px) {
        .voucher-header-btns a {
            flex: 1 1 100% !important;
        }
        .panel-heading .btn-group .btn {
            font-size: 9px;
            padding: 5px 6px;
        }
        .btn-tag {
            font-size: 10px;
            padding: 3px 8px;
            min-width: 60px;
        }
    }
}

/* ============================================
   TABLET — 769px to 991px
   ============================================ */
@media (min-width: 769px) and (max-width: 991px) {
    .voucher-header-btns {
        gap: 6px !important;
    }
    .voucher-header-btns a {
        padding: 10px 10px !important;
        font-size: 12px !important;
    }
    #site-search .col-lg-2 {
        width: 33.33% !important;
        float: left;
    }
    .table > thead > tr > th {
        font-size: 10px;
        padding: 8px 6px;
    }
    .table > tbody > tr > td {
        font-size: 11px;
        padding: 8px 6px;
    }
}

/* ============================================
   DESKTOP — 992px+
   ============================================ */
@media (min-width: 992px) {
    .table > tbody > tr > td {
        white-space: nowrap;
    }
}
</style>

<!-- Voucher Section -->
<div class="row voucher-header-row" style="margin-bottom: 20px;">
    <div class="col-sm-5">
        <h2 style="margin-top: 0; margin-bottom: 0; color: #2b3d51; font-weight: 700;">
            <i class="ion ion-card"></i> {Lang::T('Voucher Management')}
        </h2>
    </div>
    <div class="col-sm-7">
        <div class="voucher-header-btns" style="display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap;">
            <a href="{$_url}plan/add-voucher" 
               style="display: flex; align-items: center; gap: 8px; padding: 14px 16px; background: linear-gradient(135deg, #4f46e5, #6366f1); color: #fff; border-radius: 10px; text-decoration: none; box-shadow: 0 3px 12px rgba(79,70,229,0.3); transition: all 0.2s; font-weight: 600; font-size: 14px; white-space: nowrap; flex: 1; justify-content: center;"
               onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(79,70,229,0.4)';"
               onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 3px 12px rgba(79,70,229,0.3)';">
                <span style="font-size: 22px; line-height: 1;">＋</span>
                <span>{Lang::T('Add Vouchers')}</span>
            </a>
            <a href="{$_url}plan/print-voucher" target="print_voucher"
               style="display: flex; align-items: center; gap: 8px; padding: 14px 16px; background: linear-gradient(135deg, #0891b2, #06b6d4); color: #fff; border-radius: 10px; text-decoration: none; box-shadow: 0 3px 12px rgba(8,145,178,0.3); transition: all 0.2s; font-weight: 600; font-size: 14px; white-space: nowrap; flex: 1; justify-content: center;"
               onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(8,145,178,0.4)';"
               onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 3px 12px rgba(8,145,178,0.3)';">
                <span style="font-size: 22px; line-height: 1;">🖨</span>
                <span>{Lang::T('Print')}</span>
            </a>
            <a href="{$_url}plan/print-voucher-pos" target="print_voucher_pos"
               style="display: flex; align-items: center; gap: 8px; padding: 14px 16px; background: linear-gradient(135deg, #059669, #10b981); color: #fff; border-radius: 10px; text-decoration: none; box-shadow: 0 3px 12px rgba(16,185,129,0.3); transition: all 0.2s; font-weight: 600; font-size: 14px; white-space: nowrap; flex: 1; justify-content: center;"
               onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(16,185,129,0.4)';"
               onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 3px 12px rgba(16,185,129,0.3)';">
                <span style="font-size: 22px; line-height: 1;">🧾</span>
                <span>{Lang::T('POS Print')}</span>
            </a>
        </div>
    </div>
</div>

<div class="panel panel-hovered mb20 panel-primary" style="border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
    <div class="panel-heading" style="background: linear-gradient(135deg, #1e293b, #334155); border: none; padding: 14px 20px;">
        {if in_array($_admin['user_type'], ['SuperAdmin', 'Admin'])}
        <div class="btn-group pull-right voucher-action-btns">
            <button id="bulk-delete" class="btn btn-danger btn-sm" disabled style="border-radius: 6px; font-weight: 600;">
                <i class="fa fa-trash"></i> {Lang::T('Delete Selected')}
            </button>
            <a href="javascript:void(0)" class="btn btn-danger btn-sm" style="border-radius: 6px; font-weight: 600;"
                onclick="confirmDelete('{Lang::T('Are you sure you want to delete all used voucher codes? This action cannot be undone.')}', '{$_url}plan/remove-used-vouchers')">
                <i class="fa fa-trash"></i> {Lang::T('Delete Used Vouchers')}
            </a>
            <a href="javascript:void(0)" class="btn btn-warning btn-sm" style="border-radius: 6px; font-weight: 600;"
                onclick="confirmDelete('{Lang::T('Clean up invalid or corrupt voucher entries? This action cannot be undone.')}', '{$_url}plan/cleanup-vouchers')">
                <i class="fa fa-broom"></i> {Lang::T('Cleanup Invalid')}
            </a>
        </div>
        {/if}
        <span style="color: #fff; font-weight: 600; font-size: 15px;" class="panel-heading-title">
            <i class="fa fa-search"></i> {Lang::T('Filter & Search Vouchers')}
        </span>
    </div>

    <!-- Bulk Action Message -->
    <div id="action-message" style="display: none; margin: 15px 20px 0;" class="alert">
        <span id="message-text"></span>
    </div>
    <div class="panel-body" style="padding: 20px; background: #fff;">
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
            if(confirm('Are you sure you want to delete the selected ' + checkedBoxes.length + ' vouchers? This action cannot be undone.')) {
                // Collect selected IDs
                const ids = Array.from(checkedBoxes).map(checkbox => checkbox.value);
                
                // Create form data
                const formData = new FormData();
                formData.append('ids', ids.join(','));
                formData.append('csrf_token', document.getElementById('_csrf_token').value);
                
                // Send to backend
                fetch('{$_url}plan/voucher-bulk-delete/', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        showMessage('Successfully deleted ' + checkedBoxes.length + ' vouchers.', 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showMessage('Error deleting vouchers: ' + (data.message || 'Unknown error'), 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Error deleting vouchers. Please try again.', 'danger');
                });
            }
        }
    });
});
</script>

{include file="sections/footer.tpl"}
