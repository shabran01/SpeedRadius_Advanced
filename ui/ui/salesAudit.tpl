{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-stats"></i> {Lang::T('Sales Audit Dashboard')}
                <div class="btn-group pull-right">
                    <a href="{$_url}plugin/salesAudit&action=comparison" class="btn btn-success btn-xs">
                        <i class="glyphicon glyphicon-transfer"></i> Comparison
                    </a>
                    <a href="{$_url}plugin/salesAudit&action=trends" class="btn btn-info btn-xs">
                        <i class="glyphicon glyphicon-trending-up"></i> Trends
                    </a>
                </div>
            </div>
            <div class="panel-body">
                
                <!-- Sales Comparison Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="panel panel-{if $salesData.todayVsYesterday.change >= 0}success{else}danger{/if}">
                            <div class="panel-body">
                                <div class="text-center">
                                    <h4>Today vs Yesterday</h4>
                                    <h2>{$config.currency_code} {number_format($salesData.todayVsYesterday.today, 2)}</h2>
                                    <p class="text-muted">Yesterday: {$config.currency_code} {number_format($salesData.todayVsYesterday.yesterday, 2)}</p>
                                    <span class="label label-{if $salesData.todayVsYesterday.change >= 0}success{else}danger{/if}">
                                        {if $salesData.todayVsYesterday.change >= 0}↗{else}↘{/if} {$salesData.todayVsYesterday.change}%
                                    </span>
                                    <div class="mt10">
                                        <small>Transactions: {$salesData.todayVsYesterday.transactions_today} vs {$salesData.todayVsYesterday.transactions_yesterday}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="panel panel-{if $salesData.weekComparison.change >= 0}success{else}danger{/if}">
                            <div class="panel-body">
                                <div class="text-center">
                                    <h4>{$salesData.weekComparison.day_name} Comparison</h4>
                                    <h2>{$config.currency_code} {number_format($salesData.weekComparison.this_week_same_day, 2)}</h2>
                                    <p class="text-muted">Last Week: {$config.currency_code} {number_format($salesData.weekComparison.last_week_same_day, 2)}</p>
                                    <span class="label label-{if $salesData.weekComparison.change >= 0}success{else}danger{/if}">
                                        {if $salesData.weekComparison.change >= 0}↗{else}↘{/if} {$salesData.weekComparison.change}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="panel panel-{if $salesData.monthComparison.change >= 0}success{else}danger{/if}">
                            <div class="panel-body">
                                <div class="text-center">
                                    <h4>{$salesData.monthComparison.date} Comparison</h4>
                                    <h2>{$config.currency_code} {number_format($salesData.monthComparison.this_month_same_date, 2)}</h2>
                                    <p class="text-muted">Last Month: {$config.currency_code} {number_format($salesData.monthComparison.last_month_same_date, 2)}</p>
                                    <span class="label label-{if $salesData.monthComparison.change >= 0}success{else}danger{/if}">
                                        {if $salesData.monthComparison.change >= 0}↗{else}↘{/if} {$salesData.monthComparison.change}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="panel panel-info">
                            <div class="panel-body">
                                <div class="text-center">
                                    <h4>Week to Date</h4>
                                    <h2>{$config.currency_code} {number_format($salesData.weekToDate.this_week, 2)}</h2>
                                    <p class="text-muted">Last Week: {$config.currency_code} {number_format($salesData.weekToDate.last_week, 2)}</p>
                                    <span class="label label-{if $salesData.weekToDate.change >= 0}success{else}danger{/if}">
                                        {if $salesData.weekToDate.change >= 0}↗{else}↘{/if} {$salesData.weekToDate.change}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Month to Date -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-warning">
                            <div class="panel-body">
                                <div class="text-center">
                                    <h4>Month to Date Performance</h4>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h3>{$config.currency_code} {number_format($salesData.monthToDate.this_month, 2)}</h3>
                                            <p>This Month</p>
                                        </div>
                                        <div class="col-md-4">
                                            <h3>{$config.currency_code} {number_format($salesData.monthToDate.last_month, 2)}</h3>
                                            <p>Last Month (Same Period)</p>
                                        </div>
                                        <div class="col-md-4">
                                            <h3 class="text-{if $salesData.monthToDate.change >= 0}success{else}danger{/if}">
                                                {if $salesData.monthToDate.change >= 0}↗{else}↘{/if} {$salesData.monthToDate.change}%
                                            </h3>
                                            <p>Change</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Charts and Analytics -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4>Today's Hourly Sales</h4>
                            </div>
                            <div class="panel-body">
                                <canvas id="hourlySalesChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4>Payment Methods (This Month)</h4>
                            </div>
                            <div class="panel-body">
                                <canvas id="paymentMethodsChart" width="200" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Top Performing Plans -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4>Top Performing Plans (This Month)</h4>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Plan Name</th>
                                                <th>Transactions</th>
                                                <th>Total Revenue</th>
                                                <th>Avg. Price</th>
                                                <th>Performance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {foreach from=$topPlans item=plan key=index}
                                            <tr>
                                                <td>{$index + 1}</td>
                                                <td><strong>{$plan.plan_name}</strong></td>
                                                <td>{$plan.transaction_count}</td>
                                                <td>{$config.currency_code} {number_format($plan.total_revenue, 2)}</td>
                                                <td>{$config.currency_code} {number_format($plan.avg_price, 2)}</td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-success" style="width: {($plan.total_revenue / $topPlans[0].total_revenue) * 100}%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Methods Breakdown -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4>Payment Methods Breakdown (This Month)</h4>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Payment Method</th>
                                                <th>Transactions</th>
                                                <th>Total Revenue</th>
                                                <th>Percentage</th>
                                                <th>Share</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {foreach from=$paymentMethods item=method}
                                            <tr>
                                                <td><strong>{$method.payment_method}</strong></td>
                                                <td>{$method.transaction_count}</td>
                                                <td>{$config.currency_code} {number_format($method.total_revenue, 2)}</td>
                                                <td>{$method.percentage}%</td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-info" style="width: {$method.percentage}%"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Hourly Sales Chart
const hourlySalesCtx = document.getElementById('hourlySalesChart').getContext('2d');
const hourlySalesChart = new Chart(hourlySalesCtx, {
    type: 'line',
    data: {
        labels: [
            {foreach from=$hourlySales item=hour key=index}
            '{$hour.hour}:00'{if $index < count($hourlySales) - 1},{/if}
            {/foreach}
        ],
        datasets: [{
            label: 'Sales ({$config.currency_code})',
            data: [
                {foreach from=$hourlySales item=hour key=index}
                {$hour.total}{if $index < count($hourlySales) - 1},{/if}
                {/foreach}
            ],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }, {
            label: 'Transactions',
            data: [
                {foreach from=$hourlySales item=hour key=index}
                {$hour.count}{if $index < count($hourlySales) - 1},{/if}
                {/foreach}
            ],
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1,
            yAxisID: 'y1',
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
                    text: 'Hour of Day'
                }
            },
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Sales ({$config.currency_code})'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Transactions'
                },
                grid: {
                    drawOnChartArea: false,
                },
            }
        }
    }
});

// Payment Methods Pie Chart
const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
const paymentMethodsChart = new Chart(paymentMethodsCtx, {
    type: 'doughnut',
    data: {
        labels: [
            {foreach from=$paymentMethods item=method key=index}
            '{$method.payment_method}'{if $index < count($paymentMethods) - 1},{/if}
            {/foreach}
        ],
        datasets: [{
            data: [
                {foreach from=$paymentMethods item=method key=index}
                {$method.total_revenue}{if $index < count($paymentMethods) - 1},{/if}
                {/foreach}
            ],
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 205, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 205, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': {$config.currency_code} ' + context.parsed.toFixed(2);
                    }
                }
            }
        }
    }
});

// Auto refresh every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>

{include file="sections/footer.tpl"}
