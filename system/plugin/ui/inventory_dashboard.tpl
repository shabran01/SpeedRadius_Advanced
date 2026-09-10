{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a href="{$_url}plugin/inventory&action=products" class="btn btn-primary btn-xs" title="Manage Products">
                        <i class="fa fa-cube"></i> Products
                    </a>
                    <a href="{$_url}plugin/inventory&action=stock_in" class="btn btn-success btn-xs" title="Add Stock">
                        <i class="fa fa-plus"></i> Stock In
                    </a>
                    <a href="{$_url}plugin/inventory&action=stock_out" class="btn btn-warning btn-xs" title="Remove Stock">
                        <i class="fa fa-minus"></i> Stock Out
                    </a>
                    <a href="{$_url}plugin/inventory&action=movements" class="btn btn-info btn-xs" title="View Movements">
                        <i class="fa fa-exchange"></i> Movements
                    </a>
                </div>
                Inventory Dashboard
            </div>
            <div class="panel-body">
                
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="panel panel-primary panel-colorful">
                            <div class="panel-body text-center">
                                <h3 class="margin-0">{$totalProducts}</h3>
                                <p class="margin-0">Total Products</p>
                            </div>
                            <div class="panel-footer text-center">
                                <i class="fa fa-cubes fa-3x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="panel panel-danger panel-colorful">
                            <div class="panel-body text-center">
                                <h3 class="margin-0">{$lowStockProducts}</h3>
                                <p class="margin-0">Low Stock Alerts</p>
                            </div>
                            <div class="panel-footer text-center">
                                <i class="fa fa-exclamation-triangle fa-3x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="panel panel-success panel-colorful">
                            <div class="panel-body text-center">
                                <h3 class="margin-0">Ksh {number_format($stockValue, 2)}</h3>
                                <p class="margin-0">Total Stock Value</p>
                            </div>
                            <div class="panel-footer text-center">
                                <i class="fa fa-money fa-3x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="panel panel-info panel-colorful">
                            <div class="panel-body text-center">
                                <h3 class="margin-0">{count($recentMovements)}</h3>
                                <p class="margin-0">Recent Movements</p>
                            </div>
                            <div class="panel-footer text-center">
                                <i class="fa fa-refresh fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alerts -->
                {if count($lowStockItems) > 0}
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <i class="fa fa-exclamation-triangle"></i> Low Stock Alerts
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                    <th>Reorder Level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach $lowStockItems as $item}
                                <tr>
                                    <td>{$item->name}</td>
                                    <td>{$item->sku}</td>
                                    <td>{$item->category_name}</td>
                                    <td><span class="badge badge-danger">{$item->quantity}</span></td>
                                    <td>{$item->reorder_level}</td>
                                    <td>
                                        <a href="{$_url}plugin/inventory&action=stock_in" class="btn btn-success btn-xs">
                                            <i class="fa fa-plus"></i> Add Stock
                                        </a>
                                    </td>
                                </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
                {/if}

                <!-- Recent Stock Movements -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-list"></i> Recent Stock Movements
                        <a href="{$_url}plugin/inventory&action=movements" class="btn btn-xs btn-primary pull-right">
                            View All
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Cost (Ksh)</th>
                                    <th>By</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach $recentMovements as $movement}
                                <tr>
                                    <td>{$movement->created_at}</td>
                                    <td>{$movement->product_name}</td>
                                    <td>
                                        {if $movement->movement_type == 'IN'}
                                            <span class="label label-success">Stock In</span>
                                        {else}
                                            <span class="label label-warning">Stock Out</span>
                                        {/if}
                                    </td>
                                    <td>{$movement->quantity}</td>
                                    <td>{number_format($movement->total_cost, 2)}</td>
                                    <td>{$movement->created_by}</td>
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

{include file="sections/footer.tpl"}
