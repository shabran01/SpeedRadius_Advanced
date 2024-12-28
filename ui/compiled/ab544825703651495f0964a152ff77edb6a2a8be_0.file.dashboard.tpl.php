<?php
/* Smarty version 4.5.3, created on 2024-12-21 22:54:52
  from '/var/www/html/snootylique/ui/ui/dashboard.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_67671d0c374410_16574945',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ab544825703651495f0964a152ff77edb6a2a8be' => 
    array (
      0 => '/var/www/html/snootylique/ui/ui/dashboard.tpl',
      1 => 1734662748,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:sections/header.tpl' => 1,
    'file:pagination.tpl' => 1,
    'file:sections/footer.tpl' => 1,
  ),
),false)) {
function content_67671d0c374410_16574945 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group mb-0">
                    <label for="router_filter"><?php echo Lang::T('Filter by Router');?>
:</label>
                    <select class="form-control" id="router_filter" onchange="filterDashboard()">
                        <option value="all"><?php echo Lang::T('All Routers');?>
</option>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['routers']->value, 'router');
$_smarty_tpl->tpl_vars['router']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['router']->value) {
$_smarty_tpl->tpl_vars['router']->do_else = false;
?>
                            <option value="<?php echo $_smarty_tpl->tpl_vars['router']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['router']->value['name'];?>
</option>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php if (in_array($_smarty_tpl->tpl_vars['_admin']->value['user_type'],array('SuperAdmin','Admin','Report'))) {?>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h4 class="text-bold income-today" style="font-size: large;">
                        <sup><?php echo $_smarty_tpl->tpl_vars['_c']->value['currency_code'];?>
</sup>
                        <span class="amount"><?php echo number_format($_smarty_tpl->tpl_vars['iday']->value,0,$_smarty_tpl->tpl_vars['_c']->value['dec_point'],$_smarty_tpl->tpl_vars['_c']->value['thousands_sep']);?>
</span>
                    </h4>
                    <p><?php echo Lang::T('Income Today');?>
</p>
                </div>
                <div class="icon">
                    <i class="ion ion-cash"></i>
                </div>
                <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
reports/by-date" class="small-box-footer">
                    <?php echo Lang::T('View Report');?>
 <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h4 class="text-bold income-month" style="font-size: large;">
                        <sup><?php echo $_smarty_tpl->tpl_vars['_c']->value['currency_code'];?>
</sup>
                        <span class="amount"><?php echo number_format($_smarty_tpl->tpl_vars['imonth']->value,0,$_smarty_tpl->tpl_vars['_c']->value['dec_point'],$_smarty_tpl->tpl_vars['_c']->value['thousands_sep']);?>
</span>
                    </h4>
                    <p><?php echo Lang::T('Income This Month');?>
</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
reports/by-period" class="small-box-footer">
                    <?php echo Lang::T('View Report');?>
 <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h4 class="text-bold users-stats" style="font-size: large;">
                        <span class="amount"><?php echo $_smarty_tpl->tpl_vars['u_act']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['u_all']->value-$_smarty_tpl->tpl_vars['u_act']->value;?>
</span>
                    </h4>
                    <p><?php echo Lang::T('Active');?>
/<?php echo Lang::T('Expired');?>
</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person"></i>
                </div>
                <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/list" class="small-box-footer">
                    <?php echo Lang::T('View Customers');?>
 <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
     <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                 <h4 class="text-bold online-users" style="font-size: large;">
                        <span class="amount"><?php echo $_smarty_tpl->tpl_vars['online_users']->value;?>
</span>
                    </h4>
                    <p><?php echo Lang::T('Online PPPoE Users');?>
</p>

            </div>
            <div class="icon">
                <i class="ion ion-network"></i>
            </div>
            <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plugin/pppoe_monitor_router_menu" class="small-box-footer">
                <?php echo Lang::T('Online PPPoE Users');?>

            </a>
        </div>
        </div>
    <?php }?>
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
            <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
onlineusers/hotspot" class="small-box-footer">
                <?php echo Lang::T('Online Hotspot Users');?>

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
            <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
reports/by-date" class="small-box-footer">
                <?php echo Lang::T('Total Online Users');?>

            </a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6 d-flex">
        <div class="small-box bg-red flex-fill">
            <div class="inner">
                <h4 class="text-bold total-customers" style="font-size: large;">
                    <span class="amount"><?php echo $_smarty_tpl->tpl_vars['c_all']->value;?>
</span>
                </h4>
                <p><?php echo Lang::T('Total Customers');?>
</p>
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
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_mrc'] != 'yes') {?>
            <div class="box box-solid ">
                <div class="box-header">
                    <i class="fa fa-th"></i>

                    <h3 class="box-title"><?php echo Lang::T('Monthly Registered Customers');?>
</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
dashboard&refresh" class="btn bg-teal btn-sm"><i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </div>
                <div class="box-body border-radius-none">
                    <canvas class="chart" id="chart" style="height: 250px;"></canvas>
                </div>
            </div>
        <?php }?>

        <!-- solid sales graph -->
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_tms'] != 'yes') {?>
            <div class="box box-solid ">
                <div class="box-header">
                    <i class="fa fa-inbox"></i>

                    <h3 class="box-title"><?php echo Lang::T('Total Monthly Sales');?>
</h3>

                    <div class="box-tools pull-right">
                        <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
dashboard&refresh" class="btn bg-teal btn-sm"><i class="fa fa-refresh"></i>
                        </a>
                    </div>
                </div>
                <div class="box-body border-radius-none">
                    <canvas class="chart" id="salesChart" style="height: 250px;"></canvas>
                </div>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['disable_voucher'] != 'yes' && $_smarty_tpl->tpl_vars['stocks']->value['unused'] > 0 || $_smarty_tpl->tpl_vars['stocks']->value['used'] > 0) {?>
            <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_vs'] != 'yes') {?>
                <div class="panel panel-primary mb20 panel-hovered project-stats table-responsive">
                    <div class="panel-heading">Vouchers Stock</div>
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <th><?php echo Lang::T('Package Name');?>
</th>
                                    <th>unused</th>
                                    <th>used</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['plans']->value, 'stok');
$_smarty_tpl->tpl_vars['stok']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['stok']->value) {
$_smarty_tpl->tpl_vars['stok']->do_else = false;
?>
                                    <tr>
                                        <td><?php echo $_smarty_tpl->tpl_vars['stok']->value['name_plan'];?>
</td>
                                        <td><?php echo $_smarty_tpl->tpl_vars['stok']->value['unused'];?>
</td>
                                        <td><?php echo $_smarty_tpl->tpl_vars['stok']->value['used'];?>
</td>
                                    </tr>
                                </tbody>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            <tr>
                                <td>Total</td>
                                <td><?php echo $_smarty_tpl->tpl_vars['stocks']->value['unused'];?>
</td>
                                <td><?php echo $_smarty_tpl->tpl_vars['stocks']->value['used'];?>
</td>
                            </tr>
                        </table>
                    </div>
                </div>
            <?php }?>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_uet'] != 'yes') {?>
            <div class="panel panel-warning mb20 panel-hovered project-stats table-responsive">
                <div class="panel-heading"><?php echo Lang::T('User Expired, Today');?>
</div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th><?php echo Lang::T('Username');?>
</th>
                                <th><?php echo Lang::T('Created / Expired');?>
</th>
                                <th><?php echo Lang::T('Internet Package');?>
</th>
                                <th><?php echo Lang::T('Location');?>
</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['expire']->value, 'expired');
$_smarty_tpl->tpl_vars['expired']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['expired']->value) {
$_smarty_tpl->tpl_vars['expired']->do_else = false;
?>
                                <?php $_smarty_tpl->_assignInScope('rem_exp', ((string)$_smarty_tpl->tpl_vars['expired']->value['expiration'])." ".((string)$_smarty_tpl->tpl_vars['expired']->value['time']));?>
                                <?php $_smarty_tpl->_assignInScope('rem_started', ((string)$_smarty_tpl->tpl_vars['expired']->value['recharged_on'])." ".((string)$_smarty_tpl->tpl_vars['expired']->value['recharged_time']));?>
                                <tr>
                                    <td><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/viewu/<?php echo $_smarty_tpl->tpl_vars['expired']->value['username'];?>
"><?php echo $_smarty_tpl->tpl_vars['expired']->value['username'];?>
</a></td>
                                    <td><small data-toggle="tooltip" data-placement="top"
                                            title="<?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['expired']->value['recharged_on'],$_smarty_tpl->tpl_vars['expired']->value['recharged_time']);?>
"><?php echo Lang::timeElapsed($_smarty_tpl->tpl_vars['rem_started']->value);?>
</small>
                                        /
                                        <span data-toggle="tooltip" data-placement="top"
                                            title="<?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['expired']->value['expiration'],$_smarty_tpl->tpl_vars['expired']->value['time']);?>
"><?php echo Lang::timeElapsed($_smarty_tpl->tpl_vars['rem_exp']->value);?>
</span>
                                    </td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['expired']->value['namebp'];?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['expired']->value['routers'];?>
</td>
                                </tr>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </tbody>
                    </table>
                </div>
                &nbsp; <?php $_smarty_tpl->_subTemplateRender("file:pagination.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
            </div>
        <?php }?>
    </div>


    <div class="col-md-5">
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['router_check'] && count($_smarty_tpl->tpl_vars['routeroffs']->value) > 0) {?>
            <div class="panel panel-danger">
                <div class="panel-heading text-bold"><?php echo Lang::T('Routers Offline');?>
</div>
                <div class="table-responsive">
                    <table class="table table-condensed">
                        <tbody>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['routeroffs']->value, 'ros');
$_smarty_tpl->tpl_vars['ros']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ros']->value) {
$_smarty_tpl->tpl_vars['ros']->do_else = false;
?>
                                <tr>
                                    <td><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
routers/edit/<?php echo $_smarty_tpl->tpl_vars['ros']->value['id'];?>
" class="text-bold text-red"><?php echo $_smarty_tpl->tpl_vars['ros']->value['name'];?>
</a></td>
                                    <td data-toggle="tooltip" data-placement="top" class="text-red"
                                            title="<?php echo Lang::dateTimeFormat($_smarty_tpl->tpl_vars['ros']->value['last_seen']);?>
"><?php echo Lang::timeElapsed($_smarty_tpl->tpl_vars['ros']->value['last_seen']);?>

                                    </td>
                                </tr>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['run_date']->value) {?>
        <?php $_smarty_tpl->_assignInScope('current_time', time());?>
        <?php $_smarty_tpl->_assignInScope('run_time', strtotime($_smarty_tpl->tpl_vars['run_date']->value));?>
        <?php if ($_smarty_tpl->tpl_vars['current_time']->value-$_smarty_tpl->tpl_vars['run_time']->value > 3600) {?>
        <div class="panel panel-cron-warning panel-hovered mb20 activities">
            <div class="panel-heading"><i class="fa fa-clock-o"></i> &nbsp; <?php echo Lang::T('Cron has not run for over 1 hour. Please
                check your setup.');?>
</div>
        </div>
        <?php } else { ?>
        <div class="panel panel-cron-success panel-hovered mb20 activities">
            <div class="panel-heading"><?php echo Lang::T('Cron Job last ran on');?>
: <?php echo $_smarty_tpl->tpl_vars['run_date']->value;?>
</div>
        </div>
        <?php }?>
        <?php } else { ?>
        <div class="panel panel-cron-danger panel-hovered mb20 activities">
            <div class="panel-heading"><i class="fa fa-warning"></i> &nbsp; <?php echo Lang::T('Cron appear not been setup, please check
                your cron setup.');?>
</div>
        </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_pg'] != 'yes') {?>
            <div class="panel panel-success panel-hovered mb20 activities">
                <div class="panel-heading"><?php echo Lang::T('Payment Gateway');?>
: <?php echo str_replace(',',', ',$_smarty_tpl->tpl_vars['_c']->value['payment_gateway']);?>

                </div>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_aui'] != 'yes') {?>
            <div class="panel panel-info panel-hovered mb20 activities">
                <div class="panel-heading"><?php echo Lang::T('All Users Insights');?>
</div>
                <div class="panel-body">
                    <canvas id="userRechargesChart"></canvas>
                </div>
            </div>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_al'] != 'yes') {?>
            <div class="panel panel-info panel-hovered mb20 activities">
                <div class="panel-heading"><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
logs"><?php echo Lang::T('Activity Log');?>
</a></div>
                <div class="panel-body">
                    <ul class="list-unstyled">
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['dlog']->value, 'dlogs');
$_smarty_tpl->tpl_vars['dlogs']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['dlogs']->value) {
$_smarty_tpl->tpl_vars['dlogs']->do_else = false;
?>
                            <li class="primary">
                                <span class="point"></span>
                                <span class="time small text-muted"><?php echo Lang::timeElapsed($_smarty_tpl->tpl_vars['dlogs']->value['date'],true);?>
</span>
                                <p><?php echo $_smarty_tpl->tpl_vars['dlogs']->value['description'];?>
</p>
                            </li>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </ul>
                </div>
            </div>
        <?php }?>
    </div>


</div>


<?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1/dist/chart.min.js"><?php echo '</script'; ?>
>

<?php echo '<script'; ?>
 type="text/javascript">
    <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_mrc'] != 'yes') {?>
        
            document.addEventListener("DOMContentLoaded", function() {
                var counts = JSON.parse('<?php echo json_encode($_smarty_tpl->tpl_vars['monthlyRegistered']->value);?>
');

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
        
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_tmc'] != 'yes') {?>
        
            document.addEventListener("DOMContentLoaded", function() {
                var monthlySales = JSON.parse('<?php echo json_encode($_smarty_tpl->tpl_vars['monthlySales']->value);?>
');

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
        
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['_c']->value['hide_aui'] != 'yes') {?>
        
            document.addEventListener("DOMContentLoaded", function() {
                // Get the data from PHP and assign it to JavaScript variables
                var u_act = '<?php echo $_smarty_tpl->tpl_vars['u_act']->value;?>
';
                var c_all = '<?php echo $_smarty_tpl->tpl_vars['c_all']->value;?>
';
                var u_all = '<?php echo $_smarty_tpl->tpl_vars['u_all']->value;?>
';
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
        
    <?php }
echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript">
    function filterDashboard() {
        var routerId = $('#router_filter').val();
        console.log('Filtering for router:', routerId);
        
        $.ajax({
            url: '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
dashboard/filter',
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
<?php echo '</script'; ?>
>

<?php echo '<script'; ?>
 src="https://code.jquery.com/jquery-3.6.0.min.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
>
    $(document).ready(function() {
        $.ajax({
            url: "<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
onlineusers/sms_balance",
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
    <?php echo '</script'; ?>
>

<?php echo '<script'; ?>
>
$(document).ready(function() {
    $.ajax({
        url: "<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
onlineusers/summary", // Adjust this URL to your actual endpoint
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
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
