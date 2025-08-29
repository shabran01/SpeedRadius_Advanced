{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-transfer"></i> {Lang::T('Sales Comparison')}
                <div class="btn-group pull-right">
                    <a href="{$_url}plugin/salesAudit" class="btn btn-default btn-xs">
                        <i class="glyphicon glyphicon-dashboard"></i> Dashboard
                    </a>
                    <a href="{$_url}plugin/salesAudit&action=trends" class="btn btn-info btn-xs">
                        <i class="glyphicon glyphicon-trending-up"></i> Trends
                    </a>
                </div>
            </div>
            <div class="panel-body">
                
                <!-- Period Selection -->
                <div class="row mb20">
                    <div class="col-md-12">
                        <form method="GET" action="{$_url}plugin/salesAudit" class="form-inline">
                            <input type="hidden" name="action" value="comparison">
                            <div class="form-group">
                                <label>Comparison Period:</label>
                                <select name="period" class="form-control" onchange="toggleCustomDates(this.value)">
                                    <option value="today" {if $period == 'today'}selected{/if}>Today vs Yesterday</option>
                                    <option value="this_week" {if $period == 'this_week'}selected{/if}>This Week vs Last Week</option>
                                    <option value="this_month" {if $period == 'this_month'}selected{/if}>This Month vs Last Month</option>
                                    <option value="custom" {if $period == 'custom'}selected{/if}>Custom Period</option>
                                </select>
                            </div>
                            
                            <div id="customDates" style="display: {if $period == 'custom'}inline-block{else}none{/if};">
                                <div class="form-group">
                                    <label>From:</label>
                                    <input type="date" name="custom_from" class="form-control" value="{$customFrom}">
                                </div>
                                <div class="form-group">
                                    <label>To:</label>
                                    <input type="date" name="custom_to" class="form-control" value="{$customTo}">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="glyphicon glyphicon-search"></i> Compare
                            </button>
                        </form>
                    </div>
                </div>
                
                {if $comparisonData}
                <!-- Comparison Summary -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="panel panel-{if $comparisonData.changes.sales >= 0}success{else}danger{/if}">
                            <div class="panel-body text-center">
                                <h4>Sales Comparison</h4>
                                <h2>{$config.currency_code} {number_format($comparisonData.current.sales, 2)}</h2>
                                <p class="text-muted">Previous: {$config.currency_code} {number_format($comparisonData.previous.sales, 2)}</p>
                                <span class="label label-{if $comparisonData.changes.sales >= 0}success{else}danger{/if} label-lg">
                                    {if $comparisonData.changes.sales >= 0}↗{else}↘{/if} {$comparisonData.changes.sales}%
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="panel panel-{if $comparisonData.changes.transactions >= 0}success{else}danger{/if}">
                            <div class="panel-body text-center">
                                <h4>Transactions</h4>
                                <h2>{$comparisonData.current.transactions}</h2>
                                <p class="text-muted">Previous: {$comparisonData.previous.transactions}</p>
                                <span class="label label-{if $comparisonData.changes.transactions >= 0}success{else}danger{/if} label-lg">
                                    {if $comparisonData.changes.transactions >= 0}↗{else}↘{/if} {$comparisonData.changes.transactions}%
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="panel panel-{if $comparisonData.changes.avg_transaction >= 0}success{else}danger{/if}">
                            <div class="panel-body text-center">
                                <h4>Avg. Transaction</h4>
                                <h2>{$config.currency_code} {number_format($comparisonData.current.avg_transaction, 2)}</h2>
                                <p class="text-muted">Previous: {$config.currency_code} {number_format($comparisonData.previous.avg_transaction, 2)}</p>
                                <span class="label label-{if $comparisonData.changes.avg_transaction >= 0}success{else}danger{/if} label-lg">
                                    {if $comparisonData.changes.avg_transaction >= 0}↗{else}↘{/if} {$comparisonData.changes.avg_transaction}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Period Information -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h4>Period Information</h4>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Current Period</h5>
                                        <p><strong>From:</strong> {$comparisonData.dates.current_start}</p>
                                        <p><strong>To:</strong> {$comparisonData.dates.current_end}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Previous Period</h5>
                                        <p><strong>From:</strong> {$comparisonData.dates.previous_start}</p>
                                        <p><strong>To:</strong> {$comparisonData.dates.previous_end}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Daily Breakdown Chart -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4>Daily Sales Comparison</h4>
                            </div>
                            <div class="panel-body">
                                <canvas id="dailyComparisonChart" width="800" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Detailed Daily Breakdown Table -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h4>Current Period - Daily Breakdown</h4>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Sales</th>
                                                <th>Transactions</th>
                                                <th>Avg.</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {foreach from=$comparisonData.daily_breakdown.current item=day}
                                            <tr>
                                                <td>{$day.date|date_format:"%b %d, %Y"}</td>
                                                <td>{$config.currency_code} {number_format($day.sales, 2)}</td>
                                                <td>{$day.transactions}</td>
                                                <td>{$config.currency_code} {if $day.transactions > 0}{number_format($day.sales / $day.transactions, 2)}{else}0.00{/if}</td>
                                            </tr>
                                            {/foreach}
                                            <tr class="success">
                                                <td><strong>Total</strong></td>
                                                <td><strong>{$config.currency_code} {number_format($comparisonData.current.sales, 2)}</strong></td>
                                                <td><strong>{$comparisonData.current.transactions}</strong></td>
                                                <td><strong>{$config.currency_code} {number_format($comparisonData.current.avg_transaction, 2)}</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="panel panel-warning">
                            <div class="panel-heading">
                                <h4>Previous Period - Daily Breakdown</h4>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Sales</th>
                                                <th>Transactions</th>
                                                <th>Avg.</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {foreach from=$comparisonData.daily_breakdown.previous item=day}
                                            <tr>
                                                <td>{$day.date|date_format:"%b %d, %Y"}</td>
                                                <td>{$config.currency_code} {number_format($day.sales, 2)}</td>
                                                <td>{$day.transactions}</td>
                                                <td>{$config.currency_code} {if $day.transactions > 0}{number_format($day.sales / $day.transactions, 2)}{else}0.00{/if}</td>
                                            </tr>
                                            {/foreach}
                                            <tr class="warning">
                                                <td><strong>Total</strong></td>
                                                <td><strong>{$config.currency_code} {number_format($comparisonData.previous.sales, 2)}</strong></td>
                                                <td><strong>{$comparisonData.previous.transactions}</strong></td>
                                                <td><strong>{$config.currency_code} {number_format($comparisonData.previous.avg_transaction, 2)}</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {/if}
                
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function toggleCustomDates(period) {
    const customDates = document.getElementById('customDates');
    if (period === 'custom') {
        customDates.style.display = 'inline-block';
    } else {
        customDates.style.display = 'none';
    }
}

{if $comparisonData}
// Daily Comparison Chart
const dailyComparisonCtx = document.getElementById('dailyComparisonChart').getContext('2d');
const dailyComparisonChart = new Chart(dailyComparisonCtx, {
    type: 'line',
    data: {
        labels: [
            {foreach from=$comparisonData.daily_breakdown.current item=day key=index}
            '{$day.date|date_format:"%m/%d"}'{if $index < count($comparisonData.daily_breakdown.current) - 1},{/if}
            {/foreach}
        ],
        datasets: [{
            label: 'Current Period Sales',
            data: [
                {foreach from=$comparisonData.daily_breakdown.current item=day key=index}
                {$day.sales}{if $index < count($comparisonData.daily_breakdown.current) - 1},{/if}
                {/foreach}
            ],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }, {
            label: 'Previous Period Sales',
            data: [
                {foreach from=$comparisonData.daily_breakdown.previous item=day key=index}
                {$day.sales}{if $index < count($comparisonData.daily_breakdown.previous) - 1},{/if}
                {/foreach}
            ],
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            x: {
                display: true,
                title: {
                    display: true,
                    text: 'Date'
                }
            },
            y: {
                display: true,
                title: {
                    display: true,
                    text: 'Sales ({$config.currency_code})'
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': {$config.currency_code} ' + context.parsed.y.toFixed(2);
                    }
                }
            }
        }
    }
});
{/if}
</script>

{include file="sections/footer.tpl"}
