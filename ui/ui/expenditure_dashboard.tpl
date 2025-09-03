{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a class="btn btn-primary btn-xs" title="Add Expenditure" href="{$_url}plugin/expenditure&action=add">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Expenditure
                    </a>
                    <a class="btn btn-info btn-xs" title="View List" href="{$_url}plugin/expenditure&action=list">
                        <span class="glyphicon glyphicon-list" aria-hidden="true"></span> View List
                    </a>
                    <a class="btn btn-warning btn-xs" title="Categories" href="{$_url}plugin/expenditure&action=categories">
                        <span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Categories
                    </a>
                    <a class="btn btn-success btn-xs" title="Reports" href="{$_url}plugin/expenditure&action=reports">
                        <span class="glyphicon glyphicon-stats" aria-hidden="true"></span> Reports
                    </a>
                </div>
                Expenditure Dashboard
            </div>
            <div class="panel-body">
                
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                <h4 class="text-info">{if $todayExpenses}{$currency_code} {number_format($todayExpenses, 2)}{else}{$currency_code} 0.00{/if}</h4>
                                <p class="text-muted">Today's Expenses</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                <h4 class="text-warning">{if $thisMonthExpenses}{$currency_code} {number_format($thisMonthExpenses, 2)}{else}{$currency_code} 0.00{/if}</h4>
                                <p class="text-muted">This Month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                <h4 class="text-danger">{if $lastMonthExpenses}{$currency_code} {number_format($lastMonthExpenses, 2)}{else}{$currency_code} 0.00{/if}</h4>
                                <p class="text-muted">Last Month</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-body text-center">
                                <h4 class="text-success">{if $thisYearExpenses}{$currency_code} {number_format($thisYearExpenses, 2)}{else}{$currency_code} 0.00{/if}</h4>
                                <p class="text-muted">This Year</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Expenses and Top Categories -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Recent Expenses</h3>
                            </div>
                            <div class="panel-body">
                                {if $recentExpenses}
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Description</th>
                                                <th>Category</th>
                                                <th>Amount</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {foreach $recentExpenses as $expense}
                                            <tr>
                                                <td>{$expense.expense_date}</td>
                                                <td>{$expense.description}</td>
                                                <td>{if $expense.category_name}{$expense.category_name}{else}<span class="text-muted">Uncategorized</span>{/if}</td>
                                                <td>{$currency_code} {number_format($expense.amount, 2)}</td>
                                                <td>
                                                    <a href="{$_url}plugin/expenditure&action=edit&id={$expense.id}" class="btn btn-xs btn-warning">
                                                        <span class="glyphicon glyphicon-edit"></span>
                                                    </a>
                                                </td>
                                            </tr>
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                                {else}
                                <p class="text-muted">No recent expenses found.</p>
                                {/if}
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Top Categories This Month</h3>
                            </div>
                            <div class="panel-body">
                                {if $topCategories}
                                <div class="list-group">
                                    {foreach $topCategories as $category}
                                    <div class="list-group-item">
                                        <div class="row">
                                            <div class="col-xs-8">
                                                <strong>{if $category.category_name}{$category.category_name}{else}Uncategorized{/if}</strong>
                                            </div>
                                            <div class="col-xs-4 text-right">
                                                <span class="badge">{$currency_code} {number_format($category.total_amount, 2)}</span>
                                            </div>
                                        </div>
                                    </div>
                                    {/foreach}
                                </div>
                                {else}
                                <p class="text-muted">No expenses found for this month.</p>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
