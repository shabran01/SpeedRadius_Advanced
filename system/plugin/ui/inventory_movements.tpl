{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info panel-hovered mb20">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a href="{$_url}plugin/inventory" class="btn btn-default btn-xs">
                        <i class="fa fa-dashboard"></i> Dashboard
                    </a>
                    <a href="{$_url}plugin/inventory&action=stock_in" class="btn btn-success btn-xs">
                        <i class="fa fa-plus"></i> Stock In
                    </a>
                    <a href="{$_url}plugin/inventory&action=stock_out" class="btn btn-warning btn-xs">
                        <i class="fa fa-minus"></i> Stock Out
                    </a>
                </div>
                Stock Movements History
            </div>
            <div class="panel-body">
                
                <!-- Filters -->
                <form method="post" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="type" class="form-control">
                                <option value="">All Types</option>
                                <option value="IN" {if $smarty.post.type == 'IN'}selected{/if}>Stock In</option>
                                <option value="OUT" {if $smarty.post.type == 'OUT'}selected{/if}>Stock Out</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_from" class="form-control" placeholder="From Date" value="{$smarty.post.date_from}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_to" class="form-control" placeholder="To Date" value="{$smarty.post.date_to}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                <hr>

                <!-- Movements Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Unit Cost (Ksh)</th>
                                <th>Total Cost (Ksh)</th>
                                <th>Supplier/Customer</th>
                                <th>Reason/Reference</th>
                                <th>By</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $movements as $mov}
                            <tr>
                                <td><small>{$mov->created_at}</small></td>
                                <td><strong>{$mov->product_name}</strong></td>
                                <td>
                                    {if $mov->movement_type == 'IN'}
                                        <span class="label label-success"><i class="fa fa-arrow-down"></i> In</span>
                                    {else}
                                        <span class="label label-warning"><i class="fa fa-arrow-up"></i> Out</span>
                                    {/if}
                                </td>
                                <td><strong>{$mov->quantity}</strong></td>
                                <td>{number_format($mov->unit_cost, 2)}</td>
                                <td><strong>{number_format($mov->total_cost, 2)}</strong></td>
                                <td>
                                    {if $mov->movement_type == 'IN'}
                                        {$mov->supplier_name}
                                    {else}
                                        {$mov->customer}
                                    {/if}
                                </td>
                                <td>
                                    {if $mov->movement_type == 'OUT'}
                                        <span class="label label-default">{$mov->reason}</span><br>
                                    {/if}
                                    <small>{$mov->reference}</small>
                                </td>
                                <td><small>{$mov->created_by}</small></td>
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
