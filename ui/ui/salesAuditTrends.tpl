{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <i class="glyphicon glyphicon-trending-up"></i> {Lang::T('Sales Trends')}
                <div class="btn-group pull-right">
                    <a href="{$_url}plugin/salesAudit" class="btn btn-default btn-xs">
                        <i class="glyphicon glyphicon-dashboard"></i> Dashboard
                    </a>
                    <a href="{$_url}plugin/salesAudit&action=comparison" class="btn btn-success btn-xs">
                        <i class="glyphicon glyphicon-transfer"></i> Comparison
                    </a>
                </div>
            </div>
            <div class="panel-body">
                
                <!-- Period Selection -->
                <div class="row mb20">
                    <div class="col-md-12">
                        <form method="GET" action="{$_url}plugin/salesAudit" class="form-inline">
                            <input type="hidden" name="action" value="trends">
                            <div class="form-group">
                                <label>Trend Period:</label>
                                <select name="period" class="form-control" onchange="this.form.submit()">
                                    <option value="7days" {if $period == '7days'}selected{/if}>Last 7 Days</option>
                                    <option value="30days" {if $period == '30days'}selected{/if}>Last 30 Days</option>
                                    <option value="12months" {if $period == '12months'}selected{/if}>Last 12 Months</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
                
                {if $trendsData}
                <!-- Trends Summary -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="panel panel-primary">
                            <div class="panel-body text-center">
                                <h4>Total Sales</h4>
                                <h2>{$config.currency_code} 
                                    {assign var="totalSales" value=0}
                                    {foreach from=$trendsData item=trend}
                                        {assign var="totalSales" value=$totalSales + $trend.sales}
                                    {/foreach}
                                    {number_format($totalSales, 2)}
                                </h2>
                                <p class="text-muted">
                                    {if $period == '7days'}Last 7 Days
                                    {elseif $period == '30days'}Last 30 Days
                                    {elseif $period == '12months'}Last 12 Months
                                    {/if}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="panel panel-info">
                            <div class="panel-body text-center">
                                <h4>Total Transactions</h4>
                                <h2>
                                    {assign var="totalTransactions" value=0}
                                    {foreach from=$trendsData item=trend}
                                        {assign var="totalTransactions" value=$totalTransactions + $trend.transactions}
                                    {/foreach}
                                    {$totalTransactions}
                                </h2>
                                <p class="text-muted">
                                    {if $period == '7days'}Last 7 Days
                                    {elseif $period == '30days'}Last 30 Days
                                    {elseif $period == '12months'}Last 12 Months
                                    {/if}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="panel panel-success">
                            <div class="panel-body text-center">
                                <h4>Average Daily Sales</h4>
                                <h2>{$config.currency_code} 
                                    {if count($trendsData) > 0}
                                        {number_format($totalSales / count($trendsData), 2)}
                                    {else}
                                        0.00
                                    {/if}
                                </h2>
                                <p class="text-muted">
                                    {if $period == '7days'}Daily Average
                                    {elseif $period == '30days'}Daily Average
                                    {elseif $period == '12months'}Monthly Average
                                    {/if}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Trends Chart -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4>Sales Trends Chart</h4>
                            </div>
                            <div class="panel-body">
                                <canvas id="trendsChart" width="800" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Trends Analysis -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4>Best Performing Days/Periods</h4>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Date/Period</th>
                                                <th>Sales</th>
                                                <th>Transactions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {assign var="sortedTrends" value=$trendsData}
                                            {* Sort by sales descending *}
                                            {foreach from=$sortedTrends item=trend key=index}
                                                {if $index < 5} {* Top 5 *}
                                                <tr>
                                                    <td>{$index + 1}</td>
                                                    <td>
                                                        {if $period == '12months'}
                                                            {$trend.date|date_format:"%B %Y"}
                                                        {else}
                                                            {$trend.date|date_format:"%b %d, %Y"}
                                                        {/if}
                                                    </td>
                                                    <td>{$config.currency_code} {number_format($trend.sales, 2)}</td>
                                                    <td>{$trend.transactions}</td>
                                                </tr>
                                                {/if}
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4>Performance Statistics</h4>
                            </div>
                            <div class="panel-body">
                                {assign var="highestSales" value=0}
                                {assign var="lowestSales" value=999999}
                                {assign var="totalDays" value=0}
                                {assign var="positiveDays" value=0}
                                
                                {foreach from=$trendsData item=trend}
                                    {if $trend.sales > $highestSales}
                                        {assign var="highestSales" value=$trend.sales}
                                    {/if}
                                    {if $trend.sales < $lowestSales && $trend.sales > 0}
                                        {assign var="lowestSales" value=$trend.sales}
                                    {/if}
                                    {assign var="totalDays" value=$totalDays + 1}
                                    {if $trend.sales > 0}
                                        {assign var="positiveDays" value=$positiveDays + 1}
                                    {/if}
                                {/foreach}
                                
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h5>Highest Sales Day:</h5>
                                        <p><strong>{$config.currency_code} {number_format($highestSales, 2)}</strong></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <h5>Lowest Sales Day:</h5>
                                        <p><strong>{$config.currency_code} {number_format($lowestSales, 2)}</strong></p>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h5>Active Sales Days:</h5>
                                        <p><strong>{$positiveDays} / {$totalDays}</strong></p>
                                    </div>
                                    <div class="col-sm-6">
                                        <h5>Success Rate:</h5>
                                        <p><strong>
                                            {if $totalDays > 0}
                                                {number_format(($positiveDays / $totalDays) * 100, 1)}%
                                            {else}
                                                0%
                                            {/if}
                                        </strong></p>
                                    </div>
                                </div>
                                
                                <div class="progress">
                                    <div class="progress-bar progress-bar-success" style="width: {if $totalDays > 0}{($positiveDays / $totalDays) * 100}{else}0{/if}%">
                                        Sales Activity Rate
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Detailed Trends Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4>Detailed Trends Data</h4>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="trendsTable">
                                        <thead>
                                            <tr>
                                                <th>Date/Period</th>
                                                <th>Sales</th>
                                                <th>Transactions</th>
                                                <th>Avg. Transaction</th>
                                                <th>Performance</th>
                                                <th>Growth</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {foreach from=$trendsData item=trend key=index}
                                            <tr>
                                                <td>
                                                    {if $period == '12months'}
                                                        {$trend.date|date_format:"%B %Y"}
                                                    {else}
                                                        {$trend.date|date_format:"%b %d, %Y"}
                                                    {/if}
                                                </td>
                                                <td>{$config.currency_code} {number_format($trend.sales, 2)}</td>
                                                <td>{$trend.transactions}</td>
                                                <td>
                                                    {$config.currency_code} 
                                                    {if $trend.transactions > 0}
                                                        {number_format($trend.sales / $trend.transactions, 2)}
                                                    {else}
                                                        0.00
                                                    {/if}
                                                </td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-info" style="width: {($trend.sales / $highestSales) * 100}%"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {if $index > 0}
                                                        {assign var="prevSales" value=$trendsData[$index-1].sales}
                                                        {if $prevSales > 0}
                                                            {assign var="growth" value=(($trend.sales - $prevSales) / $prevSales) * 100}
                                                            <span class="label label-{if $growth >= 0}success{else}danger{/if}">
                                                                {if $growth >= 0}↗{else}↘{/if} {number_format($growth, 1)}%
                                                            </span>
                                                        {else}
                                                            <span class="label label-default">N/A</span>
                                                        {/if}
                                                    {else}
                                                        <span class="label label-default">First</span>
                                                    {/if}
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
                {/if}
                
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
{if $trendsData}
// Trends Chart
const trendsCtx = document.getElementById('trendsChart').getContext('2d');
const trendsChart = new Chart(trendsCtx, {
    type: 'line',
    data: {
        labels: [
            {foreach from=$trendsData item=trend key=index}
            '{if $period == "12months"}{$trend.date|date_format:"%b %Y"}{else}{$trend.date|date_format:"%m/%d"}{/if}'{if $index < count($trendsData) - 1},{/if}
            {/foreach}
        ],
        datasets: [{
            label: 'Sales ({$config.currency_code})',
            data: [
                {foreach from=$trendsData item=trend key=index}
                {$trend.sales}{if $index < count($trendsData) - 1},{/if}
                {/foreach}
            ],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1,
            fill: true
        }, {
            label: 'Transactions',
            data: [
                {foreach from=$trendsData item=trend key=index}
                {$trend.transactions}{if $index < count($trendsData) - 1},{/if}
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
                    text: '{if $period == "12months"}Month{else}Date{/if}'
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
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        if (context.datasetIndex === 0) {
                            return 'Sales: {$config.currency_code} ' + context.parsed.y.toFixed(2);
                        } else {
                            return 'Transactions: ' + context.parsed.y;
                        }
                    }
                }
            }
        }
    }
});
{/if}

// Add table sorting functionality
function sortTable(columnIndex) {
    const table = document.getElementById('trendsTable');
    const tbody = table.getElementsByTagName('tbody')[0];
    const rows = Array.from(tbody.rows);
    
    rows.sort((a, b) => {
        const aVal = a.cells[columnIndex].textContent.trim();
        const bVal = b.cells[columnIndex].textContent.trim();
        
        // For numeric columns (remove currency symbol and parse)
        if (columnIndex === 1 || columnIndex === 2 || columnIndex === 3) {
            const aNum = parseFloat(aVal.replace(/[^\d.-]/g, ''));
            const bNum = parseFloat(bVal.replace(/[^\d.-]/g, ''));
            return bNum - aNum; // Descending order
        }
        
        return aVal.localeCompare(bVal);
    });
    
    rows.forEach(row => tbody.appendChild(row));
}
</script>

{include file="sections/footer.tpl"}
