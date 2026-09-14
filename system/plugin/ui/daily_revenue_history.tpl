{include file="sections/header.tpl"}

<style>
    .stat-card {
        text-align: center;
        padding: 20px 10px;
        border-radius: 8px;
        color: #fff;
        margin-bottom: 15px;
    }
    .stat-card .big { font-size: 28px; font-weight: bold; }
    .stat-card .lbl { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-info">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a href="{$_url}plugin/daily_revenue/preview" class="btn btn-default btn-xs">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
                <i class="fa fa-history"></i> Daily Revenue - History (Last 30 Days)
            </div>
            <div class="panel-body">

                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-md-3 col-xs-6">
                        <div class="stat-card" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                            <div class="big">Ksh {$thisMonthTotal|number_format:0}</div>
                            <div class="lbl">This Month Revenue</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-xs-6">
                        <div class="stat-card" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                            <div class="big">{$thisMonthTxns|number_format}</div>
                            <div class="lbl">This Month Transactions</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-xs-6">
                        <div class="stat-card" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                            <div class="big">Ksh {$dailyAvg|number_format:0}</div>
                            <div class="lbl">Daily Average</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-xs-6">
                        <div class="stat-card" style="background: linear-gradient(135deg, #434343, #000000);">
                            <div class="big" style="font-size:16px;">{if $bestDay}{$bestDay['date']}{else}N/A{/if}</div>
                            <div class="lbl">Best Day {if $bestDay}(Ksh {$bestDay['total']|number_format:0}){/if}</div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Bar Chart -->
                <div style="display: flex; align-items: flex-end; height: 200px; gap: 2px; margin-bottom: 15px;">
                    {assign var="maxVal" value=0}
                    {foreach $history as $h}
                        {if $h['total'] > $maxVal}{assign var="maxVal" value=$h['total']}{/if}
                    {/foreach}
                    {assign var="maxVal" value=max(1, $maxVal)}
                    {foreach array_reverse($history) as $h}
                        {assign var="barH" value=($h['total']/$maxVal)*100}
                        <div style="flex:1; background: linear-gradient(to top, #11998e, #38ef7d); 
                             height: {max(2, $barH)}%; border-radius: 3px 3px 0 0; position: relative;"
                             title="{$h['date']}: Ksh {$h['total']|number_format:0} ({$h['txns']} txns)">
                        </div>
                    {/foreach}
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-condensed">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Revenue</th>
                                <th>Transactions</th>
                                <th>Avg / Txn</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $history as $h}
                                <tr>
                                    <td><strong>{$h['date']}</strong></td>
                                    <td><small>{$h['date']|date_format:'%A'}</small></td>
                                    <td>
                                        <strong>Ksh {$h['total']|number_format:2}</strong>
                                        {if $h['total'] >= ($dailyAvg * 1.5) && $dailyAvg > 0}
                                            <span class="label label-success">🔥 Above Avg</span>
                                        {elseif $h['total'] < ($dailyAvg * 0.5) && $dailyAvg > 0}
                                            <span class="label label-warning">📉 Below Avg</span>
                                        {/if}
                                    </td>
                                    <td><span class="badge">{$h['txns']}</span></td>
                                    <td>Ksh {$h['avg']|number_format:2}</td>
                                    <td>
                                        <a href="{$_url}plugin/daily_revenue/preview&date={$h['date']}" 
                                           class="btn btn-info btn-xs">
                                            <i class="fa fa-eye"></i> Preview
                                        </a>
                                        <a href="{$_url}plugin/daily_revenue/send_now&date={$h['date']}" 
                                           class="btn btn-success btn-xs"
                                           onclick="return confirm('Resend summary for {$h['date']}?');">
                                            <i class="fa fa-paper-plane"></i> Resend
                                        </a>
                                    </td>
                                </tr>
                            {foreachelse}
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <br><i class="fa fa-inbox fa-3x"></i><br><br>
                                        No revenue data found for the last 30 days.
                                        <br><br>
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

{include file="sections/footer.tpl"}
