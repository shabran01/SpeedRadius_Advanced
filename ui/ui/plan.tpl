{include file="sections/header.tpl"}

<style>
    .bulk-actions {
        padding: 10px 0;
    }
    #select-all {
        cursor: pointer;
    }
    .plan-checkbox {
        cursor: pointer;
    }
    #bulk-delete:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    #bulk-delete {
        transition: all 0.3s ease;
    }
    .mb10 {
        margin-bottom: 10px;
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                <div class="btn-group pull-right">
                    <a class="btn btn-primary btn-xs" title="save" href="{$_url}plan/sync"
                        onclick="return ask(this, 'This will sync/send Caustomer active plan to Mikrotik?')"><span
                            class="glyphicon glyphicon-refresh" aria-hidden="true"></span> sync</a>
                </div>
                {* <div class="btn-group pull-right">
                    <a class="btn btn-info btn-xs" title="save" href="{$_url}plan/csv{$append_url}"
                        onclick="return ask(this, 'This will export to CSV?')"><span class="glyphicon glyphicon-download"
                            aria-hidden="true"></span> CSV</a>
                </div> *}
                {/if}
                &nbsp;
                {Lang::T('Active Customers')}
            </div>
            <input type="hidden" id="_csrf_token" value="{$_csrf_token}">
            </div>
            <form id="site-search" method="post" action="{$_url}plan/list/">
                <div class="panel-body">
                    <div class="row row-no-gutters" style="padding: 5px">
                        <div class="col-lg-2">
                            <div class="input-group">
                                <div class="input-group-btn">
                                    <a class="btn btn-danger" title="Clear Search Query" href="{$_url}plan/list"><span
                                            class="glyphicon glyphicon-remove-circle"></span></a>
                                </div>
                                <input type="text" name="search" class="form-control"
                                    placeholder="{Lang::T('Search')}..." value="{$search}">
                            </div>
                        </div>
                        <div class="col-lg-2 col-xs-4">
                            <select class="form-control" id="router" name="router">
                                <option value="">{Lang::T('Location')}</option>
                                {foreach $routers as $r}
                                <option value="{$r}" {if $router eq $r }selected{/if}>{$r}
                                </option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-lg-2 col-xs-4">
                            <select class="form-control" id="plan" name="plan">
                                <option value="">{Lang::T('Plan Name')}</option>
                                {foreach $plans as $p}
                                <option value="{$p['id']}" {if $plan eq $p['id'] }selected{/if}>{$p['name_plan']}
                                </option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-lg-2 col-xs-4">
                            <select class="form-control" id="status" name="status">
                                <option value="-">{Lang::T('Status')}</option>
                                <option value="on" {if $status eq 'on' }selected{/if}>{Lang::T('Active')}</option>
                                <option value="off" {if $status eq 'off' }selected{/if}>{Lang::T('Expired')}</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-xs-4">
                            <select class="form-control" id="type" name="type">
                                <option value="">{Lang::T('Connection Type')}</option>
                                <option value="PPPOE" {if $type eq 'PPPOE' }selected{/if}>PPPoE</option>
                                <option value="Hotspot" {if $type eq 'Hotspot' }selected{/if}>Hotspot</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-xs-6">
                            <button class="btn btn-success btn-block" type="submit"><span
                                    class="fa fa-search"></span></button>
                        </div>
                        <div class="col-md-2 col-xs-6">
                            <a href="{$_url}plan/recharge" class="btn btn-primary btn-block"><i
                                    class="ion ion-android-add">
                                </i> {Lang::T('Recharge Account')}</a>
                        </div>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <div style="margin-left: 5px; margin-right: 5px;">
                    {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                    <div class="bulk-actions mb10">
                        <button id="bulk-delete" class="btn btn-danger btn-sm" disabled><i class="glyphicon glyphicon-trash"></i> {Lang::T('Delete Selected')}</button>
                    </div>
                    {/if}
                    <table id="datatable" class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                                <th>
                                    <input type="checkbox" id="select-all">
                                </th>
                                {/if}
                                <th>{Lang::T('Username')}</th>
                                <th>{Lang::T('Full Name')}</th>
                                <th>{Lang::T('Plan Name')}</th>
                                <th>{Lang::T('Type')}</th>
                                <th>{Lang::T('Created On')}</th>
                                <th>{Lang::T('Expires On')}</th>
                                <th>{Lang::T('Method')}</th>
                                <th><a href="{$_url}routers/list">{Lang::T('Location')}</a></th>
                                <th>{Lang::T('Manage')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $d as $ds}
                            <tr {if $ds['status']=='off' }class="danger" {/if}>
                                {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                                <td>
                                    <input type="checkbox" class="plan-checkbox" value="{$ds['id']}">
                                </td>
                                {/if}
                                <td>
                                    {if $ds['customer_id'] == '0'}
                                    <a href="{$_url}plan/voucher/&search={$ds['username']}">{$ds['username']}</a>
                                    {else}
                                    <a href="{$_url}customers/viewu/{$ds['username']}">{$ds['username']}</a>
                                    {/if}
                                </td>
                                <td>
                                    {if $ds['customer_id'] == '0'}
                                    <span class="text-muted">-</span>
                                    {else}
                                    {$ds['customer_fullname']|default:'-'}
                                    {/if}
                                </td>
                                <td>
                                {if $ds['type'] == 'Hotspot'}
                                    <a href="{$_url}services/edit/{$ds['plan_id']}">{$ds['namebp']}</a>
                                    <span api-get-text="{$_url}autoload/customer_is_active/{$ds['username']}/{$ds['plan_id']}"></span>
								{elseif $ds['type'] == 'PPPOE'}
                                    <a href="{$_url}services/pppoe-edit/{$ds['plan_id']}">{$ds['namebp']}</a>
                                    <span api-get-text="{$_url}autoload/customer_is_active/{$ds['username']}/{$ds['plan_id']}"></span>
                                {/if}

                                </td>
                                <td>{$ds['type']}</td>
                                <td>{Lang::dateAndTimeFormat($ds['recharged_on'],$ds['recharged_time'])}</td>
                                <td>{Lang::dateAndTimeFormat($ds['expiration'],$ds['time'])}</td>
                                <td>{$ds['method']}</td>
                                <td>{$ds['routers']}</td>
                                <td>
                                    <a href="{$_url}plan/edit/{$ds['id']}" class="btn btn-warning btn-xs"
                                        style="color: black;">{Lang::T('Edit')}</a>
                                    {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                                    <a href="{$_url}plan/delete/{$ds['id']}" id="{$ds['id']}"
                                        onclick="return ask(this, '{Lang::T('Delete')}?')"
                                        class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></a>
                                    {/if}
                                    {if $ds['status']=='off' && $_c['extend_expired']}
                                    <a href="javascript:extend('{$ds['id']}')"
                                        class="btn btn-info btn-xs">{Lang::T('Extend')}</a>
                                    {/if}
                                </td>
                            </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
            {include file="pagination.tpl"}
        </div>
    </div>
</div>

<script>
    function extend(idP) {
        var res = prompt("Extend for many days?", "3");
        if (res) {
            if (confirm("Extend for " + res + " days?")) {
                window.location.href = "{$_url}plan/extend/" + idP + "/" + res + "&stoken={App::getToken()}";
            }
        }
    }

    // Bulk delete functionality
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select-all');
        const planCheckboxes = document.querySelectorAll('.plan-checkbox');
        const bulkDeleteButton = document.getElementById('bulk-delete');
        
        // Select all checkbox functionality
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                
                // Update all checkboxes
                planCheckboxes.forEach(function(checkbox) {
                    checkbox.checked = isChecked;
                });
                
                // Update delete button state
                updateBulkDeleteButton();
            });
        }
        
        // Individual checkbox functionality
        planCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                // If any checkbox is unchecked, uncheck the "select all" checkbox
                if (!this.checked && selectAllCheckbox) {
                    selectAllCheckbox.checked = false;
                }
                
                // If all checkboxes are checked, check the "select all" checkbox
                if (selectAllCheckbox) {
                    const allChecked = Array.from(planCheckboxes).every(function(cb) {
                        return cb.checked;
                    });
                    
                    selectAllCheckbox.checked = allChecked;
                }
                
                // Update delete button state
                updateBulkDeleteButton();
            });
        });
        
        // Update bulk delete button state (enabled/disabled)
        function updateBulkDeleteButton() {
            if (bulkDeleteButton) {
                const anyChecked = Array.from(planCheckboxes).some(function(checkbox) {
                    return checkbox.checked;
                });
                
                bulkDeleteButton.disabled = !anyChecked;
            }
        }
        
        // Bulk delete button click handler
        if (bulkDeleteButton) {
            bulkDeleteButton.addEventListener('click', function() {
                const selectedIds = [];
                
                // Collect all selected IDs
                planCheckboxes.forEach(function(checkbox) {
                    if (checkbox.checked) {
                        selectedIds.push(checkbox.value);
                    }
                });
                
                if (selectedIds.length > 0) {
                    if (confirm('{Lang::T("Are you sure you want to delete")} ' + selectedIds.length + ' {Lang::T("selected items")}?')) {
                        // Perform the bulk delete
                        bulkDeletePlans(selectedIds);
                    }
                }
            });
        }
        
        // Function to handle bulk delete
        function bulkDeletePlans(ids) {
            // Create a form to submit the IDs
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{$_url}plan/bulk-delete';
            form.style.display = 'none';
            
            // Add CSRF token if needed
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'token';
            csrfInput.value = document.getElementById('_csrf_token').value;
            form.appendChild(csrfInput);
            
            // Add the selected IDs
            const idsInput = document.createElement('input');
            idsInput.type = 'hidden';
            idsInput.name = 'ids';
            idsInput.value = ids.join(',');
            form.appendChild(idsInput);
            
            // Submit the form
            document.body.appendChild(form);
            form.submit();
        }
    });
</script>

{include file="sections/footer.tpl"}