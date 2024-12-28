{include file="sections/header.tpl"}

<div class="row mb-3">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group mb-0">
                    <label for="router_filter">{Lang::T('Filter by Router')}:</label>
                    <select class="form-control" id="router_filter" onchange="filterDashboard()">
                        <option value="all">{Lang::T('All Routers')}</option>
                        {foreach $routers as $router}
                            <option value="{$router['id']}">{$router['name']}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {if in_array($_admin['user_type'], ['SuperAdmin', 'Admin', 'Report'])}
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h4 class="text-bold income-today" style="font-size: large;">
                        <sup>{$_c['currency_code']}</sup>
                        <span class="amount">{number_format($iday, 0, $_c['dec_point'], $_c['thousands_sep'])}</span>
                    </h4>
                    <p>{Lang::T('Income Today')}</p>
                </div>
                <div class="icon">
                    <i class="ion ion-cash"></i>
                </div>
                <a href="{$_url}reports/by-date" class="small-box-footer">
                    {Lang::T('View Report')} <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h4 class="text-bold income-month" style="font-size: large;">
                        <sup>{$_c['currency_code']}</sup>
                        <span class="amount">{number_format($imonth, 0, $_c['dec_point'], $_c['thousands_sep'])}</span>
                    </h4>
                    <p>{Lang::T('Income This Month')}</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="{$_url}reports/by-period" class="small-box-footer">
                    {Lang::T('View Report')} <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h4 class="text-bold users-stats" style="font-size: large;">
                        <span class="amount">{$u_act}/{$u_all - $u_act}</span>
                    </h4>
                    <p>{Lang::T('Active')}/{Lang::T('Expired')}</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person"></i>
                </div>
                <a href="{$_url}plan/list" class="small-box-footer">
                    {Lang::T('View Customers')} <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
     <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                 <h4 class="text-bold online-users" style="font-size: large;">
                        <span class="amount">{$online_users}</span>
                    </h4>
                    <p>{Lang::T('Online PPPoE Users')}</p>

            </div>
            <div class="icon">
                <i class="ion ion-network"></i>
            </div>
            <a href="{$_url}plugin/pppoe_monitor_router_menu" class="small-box-footer">
                {Lang::T('Online PPPoE Users')}
            </a>
        </div>
        </div>
    {/if}
</div>

<div class="row">
     <div class="col-lg-3 col-xs-6 d-flex">
        <div class="small-box bg-orange flex-fill">
            <div class="inner">
                <h4 class="text-2xl font-bold" id="online-hotspot-users">0</h4>
            </div>
            <div class="icon">
                <i class="ion ion-wifi"></i>
            </div>
            <a href="{$_url}onlineusers/hotspot" class="small-box-footer">
                {Lang::T('Online Hotspot Users')}
            </a>
        </div>
    </div>
  <div class="col-lg-3 col-xs-6 d-flex">
        <div class="small-box bg-purple flex-fill">
            <div class="inner">
                <h4 class="text-2xl font-bold" id="total-online-users">0</h4>
            </div>
            <div class="icon">
                <i class="ion ion-ios-people"></i>
            </div>
            <a href="{$_url}reports/by-date" class="small-box-footer">
                {Lang::T('Total Online Users')}
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6 d-flex">
        <div class="small-box bg-red flex-fill">
            <div class="inner">
                <h4 class="text-bold total-customers" style="font-size: large;">
                    <span class="amount">{$c_all}</span>
                </h4>
                <p>{Lang::T('Total Customers')}</p>
            </div>
            <div class="icon">
                <i class="ion ion-android-contacts"></i>
            </div>
          
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-7">

        <!-- solid sales graph -->
        {if $_c['hide_mrc'] != 'yes'}
            <div class="box box-solid ">
                <div class="box-header">
                    <i class="fa fa-th"></i>

                    <h3 class="box-title">{Lang::T('Monthly Registered Customers')}</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <a href="{$_url}dashboard&refresh" class="btn bg-teal btn-sm"><i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </div>
                <div class="box-body border-radius-none">
                    <canvas class="chart" id="chart" style="height: 250px;"></canvas>
                </div>
            </div>
        {/if}

        <!-- solid sales graph -->
        {if $_c['hide_tms'] != 'yes'}
            <div class="box box-solid ">
                <div class="box-header">
                    <i class="fa fa-inbox"></i>

                    <h3 class="box-title">{Lang::T('Total Monthly Sales')}</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <a href="{$_url}dashboard&refresh" class="btn bg-teal btn-sm"><i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </div>
                <div class="box-body border-radius-none">
                    <canvas class="chart" id="salesChart" style="height: 250px;"></canvas>
                </div>
            </div>
        {/if}
        {if $_c['disable_voucher'] != 'yes' && $stocks['unused']>0 || $stocks['used']>0}
            {if $_c['hide_vs'] != 'yes'}
                <div class="panel panel-primary mb20 panel-hovered project-stats table-responsive">
                    <div class="panel-heading">Vouchers Stock</div>
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <th>{Lang::T('Package Name')}</th>
                                    <th>unused</th>
                                    <th>used</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach $plans as $stok}
                                    <tr>
                                        <td>{$stok['name_plan']}</td>
                                        <td>{$stok['unused']}</td>
                                        <td>{$stok['used']}</td>
                                    </tr>
                                </tbody>
                            {/foreach}
                            <tr>
                                <td>Total</td>
                                <td>{$stocks['unused']}</td>
                                <td>{$stocks['used']}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            {/if}
        {/if}
        {if $_c['hide_uet'] != 'yes'}
            <div class="panel panel-warning mb20 panel-hovered project-stats table-responsive">
                <div class="panel-heading">{Lang::T('User Expired, Today')}</div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>{Lang::T('Username')}</th>
                                <th>{Lang::T('Created / Expired')}</th>
                                <th>{Lang::T('Internet Package')}</th>
                                <th>{Lang::T('Location')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $expire as $expired}
                                {assign var="rem_exp" value="{$expired['expiration']} {$expired['time']}"}
                                {assign var="rem_started" value="{$expired['recharged_on']} {$expired['recharged_time']}"}
                                <tr>
                                    <td><a href="{$_url}customers/viewu/{$expired['username']}">{$expired['username']}</a></td>
                                    <td><small data-toggle="tooltip" data-placement="top"
                                            title="{Lang::dateAndTimeFormat($expired['recharged_on'],$expired['recharged_time'])}">{Lang::timeElapsed($rem_started)}</small>
                                        /
                                        <span data-toggle="tooltip" data-placement="top"
                                            title="{Lang::dateAndTimeFormat($expired['expiration'],$expired['time'])}">{Lang::timeElapsed($rem_exp)}</span>
                                    </td>
                                    <td>{$expired['namebp']}</td>
                                    <td>{$expired['routers']}</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                &nbsp; {include file="pagination.tpl"}
            </div>
        {/if}
    </div>


    <div class="col-md-5">
        {if $_c['router_check'] && count($routeroffs)> 0}
            <div class="panel panel-danger">
                <div class="panel-heading text-bold">{Lang::T('Routers Offline')}</div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tbody>
                            {foreach $routeroffs as $ros}
                                <tr>
                                    <td><a href="{$_url}routers/edit/{$ros['id']}" class="text-bold text-red">{$ros['name']}</a></td>
                                    <td data-toggle="tooltip" data-placement="top" class="text-red"
                                            title="{Lang::dateTimeFormat($ros['last_seen'])}">{Lang::timeElapsed($ros['last_seen'])}
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        {/if}
        {if $run_date}
        {assign var="current_time" value=$smarty.now}
        {assign var="run_time" value=strtotime($run_date)}
        {if $current_time - $run_time > 3600}
        <div class="panel panel-cron-warning panel-hovered mb20 activities">
            <div class="panel-heading"><i class="fa fa-clock-o"></i> &nbsp; {Lang::T('Cron has not run for over 1 hour. Please
                check your setup.')}</div>
        </div>
        {else}
        <div class="panel panel-cron-success panel-hovered mb20 activities">
            <div class="panel-heading">{Lang::T('Cron Job last ran on')}: {$run_date}</div>
        </div>
        {/if}
        {else}
        <div class="panel panel-cron-danger panel-hovered mb20 activities">
            <div class="panel-heading"><i class="fa fa-warning"></i> &nbsp; {Lang::T('Cron appear not been setup, please check
                your cron setup.')}</div>
        </div>
        {/if}
        {if $_c['hide_pg'] != 'yes'}
            <div class="panel panel-success panel-hovered mb20 activities">
                <div class="panel-heading">{Lang::T('Payment Gateway')}: {str_replace(',',', ',$_c['payment_gateway'])}
                </div>
            </div>
        {/if}
        {if $_c['hide_aui'] != 'yes'}
            <div class="panel panel-info panel-hovered mb20 activities">
                <div class="panel-heading">{Lang::T('All Users Insights')}</div>
                <div class="panel-body">
                    <canvas id="userRechargesChart"></canvas>
                </div>
            </div>
        {/if}
        {if $_c['hide_al'] != 'yes'}
            <div class="panel panel-info panel-hovered mb20 activities">
                <div class="panel-heading"><a href="{$_url}logs">{Lang::T('Activity Log')}</a></div>
                <div class="panel-body">
                    <ul class="list-unstyled">
                        {foreach $dlog as $dlogs}
                            <li class="primary">
                                <span class="point"></span>
                                <span class="time small text-muted">{Lang::timeElapsed($dlogs['date'],true)}</span>
                                <p>{$dlogs['description']}</p>
                            </li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        {/if}
    </div>


</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"></script>

<script type="text/javascript">
    {if $_c['hide_mrc'] != 'yes'}
        {literal}
            document.addEventListener("DOMContentLoaded", function() {
                var counts = JSON.parse('{/literal}{$monthlyRegistered|json_encode}{literal}');

                var monthNames = [
                    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                ];

                var labels = [];
                var data = [];

                for (var i = 1; i <= 12; i++) {
                    var month = counts.find(count => count.date === i);
                    labels.push(month ? monthNames[i - 1] : monthNames[i - 1].substring(0, 3));
                    data.push(month ? month.count : 0);
                }

                var ctx = document.getElementById('chart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Registered Members',
                            data: data,
                            backgroundColor: 'rgba(0, 0, 255, 0.5)',
                            borderColor: 'rgba(0, 0, 255, 0.7)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            }
                        }
                    }
                });
            });
        {/literal}
    {/if}
    {if $_c['hide_tmc'] != 'yes'}
        {literal}
            document.addEventListener("DOMContentLoaded", function() {
                var monthlySales = JSON.parse('{/literal}{$monthlySales|json_encode}{literal}');

                var monthNames = [
                    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                ];

                var labels = [];
                var data = [];

                for (var i = 1; i <= 12; i++) {
                    var month = findMonthData(monthlySales, i);
                    labels.push(month ? monthNames[i - 1] : monthNames[i - 1].substring(0, 3));
                    data.push(month ? month.totalSales : 0);
                }

                var ctx = document.getElementById('salesChart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Monthly Sales',
                            data: data,
                            backgroundColor: 'rgba(2, 10, 242)', // Customize the background color
                            borderColor: 'rgba(255, 99, 132, 1)', // Customize the border color
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            }
                        }
                    }
                });
            });

            function findMonthData(monthlySales, month) {
                for (var i = 0; i < monthlySales.length; i++) {
                    if (monthlySales[i].month === month) {
                        return monthlySales[i];
                    }
                }
                return null;
            }
        {/literal}
    {/if}
    {if $_c['hide_aui'] != 'yes'}
        {literal}
            document.addEventListener("DOMContentLoaded", function() {
                // Get the data from PHP and assign it to JavaScript variables
                var u_act = '{/literal}{$u_act}{literal}';
                var c_all = '{/literal}{$c_all}{literal}';
                var u_all = '{/literal}{$u_all}{literal}';
                //lets calculate the inactive users as reported
                var expired = u_all - u_act;
                var inactive = c_all - u_all;
                if (inactive < 0) {
                    inactive = 0;
                }
                // Create the chart data
                var data = {
                    labels: ['Active Users', 'Expired Users', 'Inactive Users'],
                    datasets: [{
                        label: 'User Recharges',
                        data: [parseInt(u_act), parseInt(expired), parseInt(inactive)],
                        backgroundColor: ['rgba(4, 191, 13)', 'rgba(191, 35, 4)', 'rgba(0, 0, 255, 0.5'],
                        borderColor: ['rgba(0, 255, 0, 1)', 'rgba(255, 99, 132, 1)', 'rgba(0, 0, 255, 0.7'],
                        borderWidth: 1
                    }]
                };

                // Create chart options
                var options = {
                    responsive: true,
                    aspectRatio: 1,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 15
                            }
                        }
                    }
                };

                // Get the canvas element and create the chart
                var ctx = document.getElementById('userRechargesChart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'pie',
                    data: data,
                    options: options
                });
            });
        {/literal}
    {/if}
</script>
<script type="text/javascript">
    function filterDashboard() {
        var routerId = $('#router_filter').val();
        console.log('Filtering for router:', routerId);
        
        $.ajax({
            url: '{$_url}dashboard/filter',
            type: 'POST',
            data: { router_id: routerId },
            dataType: 'json',
            success: function(data) {
                // Update income today
                $('.income-today .amount').text(data.income_today);
                
                // Update income this month
                $('.income-month .amount').text(data.income_month);
                
                // Update users stats
                $('.users-stats .amount').text(data.active_users + '/' + data.expired_users);
                
                // Update online PPPoE users
                $('.online-users .amount').text(data.online_users);
                
                // Update hotspot users
                $('.hotspot-users .amount').text(data.hotspot_users);
                
                // Update total online users
                $('.total-online .amount').text(data.total_online);
            },
            error: function(xhr, status, error) {
                console.error('Error filtering dashboard:', error);
            }
        });
    }
    
    // Store original values when page loads
    $(document).ready(function() {
        $('.amount').each(function() {
            var $this = $(this);
            $this.data('original-value', $this.html());
        });
        
        // Add change event listener to router filter
        $('#router_filter').on('change', filterDashboard);
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $.ajax({
            url: "{$_url}onlineusers/sms_balance",
            method: 'GET',
            dataType: 'json',
            success: function(response) {
            if (response.status === 'success' && response.data && response.data.remaining_balance) {
                $('#sms-balance').text(response.data.remaining_balance);
            } else if (response.message) {
                $('#sms-balance').text('Error: ' + response.message);
            } else {
                $('#sms-balance').text('Unknown error');
            }
        },
            error: function() {
                $('#sms-balance').text('Failed to fetch balance');
            }
        });
    });
    </script>

<script>
$(document).ready(function() {
    $.ajax({
        url: "{$_url}onlineusers/summary", // Adjust this URL to your actual endpoint
        type: 'GET',
        dataType: 'json', // Ensure the expected response is JSON
        success: function(data) {
            console.log('Data fetched successfully:', data);
            $('#total-online-users').text(data.total_users);
            $('#online-hotspot-users').text(data.hotspot_users);
            $('#online-ppp-users').text(data.ppoe_users);
				
        },
        error: function(error) {
            console.log('Error fetching data:', error);
        }
    });
});
</script>

{include file="sections/footer.tpl"}
