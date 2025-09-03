{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a class="btn btn-primary btn-xs" title="Add Expenditure" href="{$_url}plugin/expenditure&action=add">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Expenditure
                    </a>
                    <a class="btn btn-default btn-xs" title="Back to Dashboard" href="{$_url}plugin/expenditure">
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Dashboard
                    </a>
                </div>
                {$_title}
            </div>
            <div class="panel-body">
                
                <!-- Search Filters -->
                <form method="get" class="form-inline" style="margin-bottom: 20px;">
                    <input type="hidden" name="_route" value="plugin/expenditure">
                    <input type="hidden" name="action" value="list">
                    
                    <div class="form-group">
                        <input type="text" class="form-control" name="search" value="{$search}" placeholder="Search description, vendor, receipt...">
                    </div>
                    
                    <div class="form-group">
                        <select class="form-control" name="category">
                            <option value="">All Categories</option>
                            {foreach $categories as $cat}
                            <option value="{$cat.id}" {if $cat.id == $category}selected{/if}>{$cat.name}</option>
                            {/foreach}
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <input type="date" class="form-control" name="date_from" value="{$date_from}" placeholder="From Date">
                    </div>
                    
                    <div class="form-group">
                        <input type="date" class="form-control" name="date_to" value="{$date_to}" placeholder="To Date">
                    </div>
                    
                    <button type="submit" class="btn btn-info">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Search
                    </button>
                    
                    <a href="{$_url}plugin/expenditure&action=list" class="btn btn-default">
                        <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Reset
                    </a>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-download" aria-hidden="true"></span> Export <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="{$_url}plugin/expenditure&action=export&format=csv&search={$search}&category={$category}&date_from={$date_from}&date_to={$date_to}">
                                <i class="fa fa-file-text-o"></i> CSV
                            </a></li>
                            <li><a href="{$_url}plugin/expenditure&action=export_pdf&search={$search}&category={$category}&date_from={$date_from}&date_to={$date_to}">
                                <i class="fa fa-file-pdf-o"></i> PDF Report
                            </a></li>
                            <li><a href="{$_url}plugin/expenditure&action=export_excel&search={$search}&category={$category}&date_from={$date_from}&date_to={$date_to}">
                                <i class="fa fa-file-excel-o"></i> Excel
                            </a></li>
                        </ul>
                    </div>
                </form>
                
                <!-- Expenses Table -->
                {if $expenses}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Vendor</th>
                                <th>Receipt#</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {assign var="total" value=0}
                            {foreach $expenses as $expense}
                            <tr>
                                <td>{$expense.expense_date}</td>
                                <td>
                                    <strong>{$expense.description}</strong>
                                    {if $expense.notes}
                                    <br><small class="text-muted">{$expense.notes}</small>
                                    {/if}
                                </td>
                                <td>
                                    {if $expense.category_name}
                                    <span class="label label-info">{$expense.category_name}</span>
                                    {else}
                                    <span class="label label-default">Uncategorized</span>
                                    {/if}
                                </td>
                                <td class="text-right">
                                    <strong>{$currency_code} {number_format($expense.amount, 2)}</strong>
                                </td>
                                <td>{$expense.vendor}</td>
                                <td>{$expense.receipt_number}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{$_url}plugin/expenditure&action=edit&id={$expense.id}" class="btn btn-xs btn-warning" title="Edit">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <a href="{$_url}plugin/expenditure&action=delete&id={$expense.id}" class="btn btn-xs btn-danger" title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this expenditure?')">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            {assign var="total" value=$total+$expense.amount}
                            {/foreach}
                        </tbody>
                        <tfoot>
                            <tr class="info">
                                <th colspan="3">Total</th>
                                <th class="text-right"><strong>{$currency_code} {number_format($total, 2)}</strong></th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                {else}
                <div class="alert alert-info">
                    <h4>No expenses found</h4>
                    <p>No expenses match your search criteria. Try adjusting your filters or <a href="{$_url}plugin/expenditure&action=add">add a new expenditure</a>.</p>
                </div>
                {/if}
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
