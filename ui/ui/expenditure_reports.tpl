{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a class="btn btn-default btn-xs" title="Back to Dashboard" href="{$_url}plugin/expenditure">
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Dashboard
                    </a>
                </div>
                {$_title}
            </div>
            <div class="panel-body">
                
                <!-- Report Controls -->
                <form method="get" class="form-inline" style="margin-bottom: 20px;">
                    <input type="hidden" name="_route" value="plugin/expenditure">
                    <input type="hidden" name="action" value="reports">
                    
                    <div class="form-group">
                        <label for="report_type">Report Type:</label>
                        <select class="form-control" id="report_type" name="report_type" onchange="toggleDateFields()">
                            <option value="monthly" {if $report_type == 'monthly'}selected{/if}>Monthly Breakdown</option>
                            <option value="category" {if $report_type == 'category'}selected{/if}>Category Breakdown</option>
                            <option value="daily" {if $report_type == 'daily'}selected{/if}>Daily Breakdown</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="year">Year:</label>
                        <select class="form-control" id="year" name="year">
                            {for $y=2020 to 2030}
                            <option value="{$y}" {if $y == $year}selected{/if}>{$y}</option>
                            {/for}
                        </select>
                    </div>
                    
                    <div class="form-group" id="month_field" {if $report_type == 'monthly'}style="display:none;"{/if}>
                        <label for="month">Month:</label>
                        <select class="form-control" id="month" name="month">
                            <option value="01" {if $month == '01'}selected{/if}>January</option>
                            <option value="02" {if $month == '02'}selected{/if}>February</option>
                            <option value="03" {if $month == '03'}selected{/if}>March</option>
                            <option value="04" {if $month == '04'}selected{/if}>April</option>
                            <option value="05" {if $month == '05'}selected{/if}>May</option>
                            <option value="06" {if $month == '06'}selected{/if}>June</option>
                            <option value="07" {if $month == '07'}selected{/if}>July</option>
                            <option value="08" {if $month == '08'}selected{/if}>August</option>
                            <option value="09" {if $month == '09'}selected{/if}>September</option>
                            <option value="10" {if $month == '10'}selected{/if}>October</option>
                            <option value="11" {if $month == '11'}selected{/if}>November</option>
                            <option value="12" {if $month == '12'}selected{/if}>December</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-info">
                        <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Generate Report
                    </button>
                </form>
                
                <!-- Report Display -->
                {if $data}
                <div class="row">
                    <div class="col-md-7 col-lg-8">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    {if $report_type == 'monthly'}Monthly Expenses for {$year}
                                    {elseif $report_type == 'category'}Category Breakdown for {$month|date_format:"%B"} {$year}
                                    {elseif $report_type == 'daily'}Daily Expenses for {$month|date_format:"%B"} {$year}
                                    {/if}
                                </h4>
                            </div>
                            <div class="panel-body">
                                <div class="chart-container" style="position: relative; height: 400px; max-height: 400px;">
                                    <canvas id="reportChart" style="max-width: 100%; max-height: 100%;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-5 col-lg-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">Summary</h4>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th>{if $report_type == 'category'}Category{else}Period{/if}</th>
                                                <th class="text-right">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {assign var="total" value=0}
                                            {foreach $data as $item}
                                            <tr>
                                                <td>
                                                    {if $report_type == 'category'}
                                                        <span class="label label-info" style="font-size: 11px;">
                                                            {$item.category_name|default:"Uncategorized"}
                                                        </span>
                                                    {else}
                                                        <strong>{$item.period}</strong>
                                                    {/if}
                                                </td>
                                                <td class="text-right">
                                                    <strong>{$currency_code} {number_format($item.amount, 2)}</strong>
                                                </td>
                                            </tr>
                                            {assign var="total" value=$total+$item.amount}
                                            {/foreach}
                                        </tbody>
                                        <tfoot>
                                            <tr class="info">
                                                <th>Total</th>
                                                <th class="text-right"><strong>{$currency_code} {number_format($total, 2)}</strong></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {else}
                <div class="alert alert-info">
                    <h4>No data found</h4>
                    <p>No expense data found for the selected criteria.</p>
                </div>
                {/if}
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.chart-container {
    position: relative;
    margin: auto;
}

@media (max-width: 768px) {
    .chart-container {
        height: 300px !important;
        max-height: 300px !important;
    }
    
    .form-inline .form-group {
        display: block;
        margin-bottom: 10px;
    }
    
    .form-inline .form-control {
        width: 100%;
    }
    
    .panel-title {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .chart-container {
        height: 250px !important;
        max-height: 250px !important;
    }
}

.table-condensed td, .table-condensed th {
    padding: 4px 8px;
    font-size: 12px;
}
</style>
<script>
function toggleDateFields() {
    var reportType = document.getElementById('report_type').value;
    var monthField = document.getElementById('month_field');
    
    if (reportType === 'monthly') {
        monthField.style.display = 'none';
    } else {
        monthField.style.display = 'inline-block';
    }
}

{if $data}
// Chart data
var chartData = {
    labels: [
        {foreach $data as $item}
        '{if $report_type == "category"}{$item.category_name|default:"Uncategorized"|escape:"javascript"}{else}{$item.period|escape:"javascript"}{/if}'{if !$item@last},{/if}
        {/foreach}
    ],
    datasets: [{
        label: 'Amount ({$currency_code})',
        data: [
            {foreach $data as $item}
            {$item.amount}{if !$item@last},{/if}
            {/foreach}
        ],
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 205, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(199, 199, 199, 0.2)',
            'rgba(83, 102, 255, 0.2)',
            'rgba(255, 99, 255, 0.2)',
            'rgba(99, 255, 132, 0.2)',
            'rgba(255, 205, 99, 0.2)',
            'rgba(54, 255, 235, 0.2)'
        ],
        borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 205, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(199, 199, 199, 1)',
            'rgba(83, 102, 255, 1)',
            'rgba(255, 99, 255, 1)',
            'rgba(99, 255, 132, 1)',
            'rgba(255, 205, 99, 1)',
            'rgba(54, 255, 235, 1)'
        ],
        borderWidth: 1
    }]
};

// Chart configuration
var ctx = document.getElementById('reportChart').getContext('2d');
var chart = new Chart(ctx, {
    type: '{if $report_type == "category"}pie{else}bar{/if}',
    data: chartData,
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: {if $report_type == "category"}1.2{else}2{/if},
        plugins: {
            legend: {
                display: {if $report_type == "category"}true{else}false{/if},
                position: '{if $report_type == "category"}bottom{else}top{/if}',
                labels: {
                    boxWidth: 12,
                    font: {
                        size: 11
                    },
                    padding: 10
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        var label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += '{$currency_code} ' + context.parsed{if $report_type == "category"}.toFixed(2){else}y.toFixed(2){/if};
                        {if $report_type == "category"}
                        // Add percentage for pie chart
                        var total = context.dataset.data.reduce((a, b) => a + b, 0);
                        var percentage = ((context.parsed / total) * 100).toFixed(1);
                        label += ' (' + percentage + '%)';
                        {/if}
                        return label;
                    }
                }
            }
        },
        {if $report_type != "category"}
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '{$currency_code} ' + value.toFixed(2);
                    },
                    font: {
                        size: 10
                    }
                }
            },
            x: {
                ticks: {
                    font: {
                        size: 10
                    }
                }
            }
        }
        {/if}
    }
});
{/if}
</script>

{include file="sections/footer.tpl"}
