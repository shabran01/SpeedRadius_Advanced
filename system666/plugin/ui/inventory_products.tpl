{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a href="{$_url}plugin/inventory" class="btn btn-default btn-xs">
                        <i class="fa fa-dashboard"></i> Dashboard
                    </a>
                    <a href="{$_url}plugin/inventory&action=add_product" class="btn btn-success btn-xs">
                        <i class="fa fa-plus"></i> Add Product
                    </a>
                    <a href="{$_url}plugin/inventory&action=categories" class="btn btn-info btn-xs">
                        <i class="fa fa-tags"></i> Categories
                    </a>
                    <a href="{$_url}plugin/inventory&action=suppliers" class="btn btn-warning btn-xs">
                        <i class="fa fa-truck"></i> Suppliers
                    </a>
                </div>
                Products List
            </div>
            <div class="panel-body">
                
                <!-- Search and Filter -->
                <form method="post" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Search</label>
                                <div class="col-md-9">
                                    <input type="text" name="search" class="form-control" placeholder="Product name or SKU" value="{$smarty.post.search}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-md-4 control-label">Category</label>
                                <div class="col-md-8">
                                    <select name="category" class="form-control">
                                        <option value="">All Categories</option>
                                        {foreach $categories as $cat}
                                            <option value="{$cat->id}" {if $smarty.post.category == $cat->id}selected{/if}>{$cat->name}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                <hr>

                <!-- Products Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Supplier</th>
                                <th>Quantity</th>
                                <th>Unit Price (Ksh)</th>
                                <th>Selling Price (Ksh)</th>
                                <th>Stock Value (Ksh)</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $products as $product}
                            <tr class="{if $product->quantity <= $product->reorder_level}danger{/if}">
                                <td>{$product->sku}</td>
                                <td><strong>{$product->name}</strong></td>
                                <td>{$product->category_name}</td>
                                <td>{$product->supplier_name}</td>
                                <td>
                                    <strong>{$product->quantity}</strong>
                                    {if $product->quantity <= $product->reorder_level}
                                        <span class="label label-danger">Low Stock</span>
                                    {/if}
                                </td>
                                <td>{number_format($product->unit_price, 2)}</td>
                                <td>{number_format($product->selling_price, 2)}</td>
                                <td><strong>{number_format($product->quantity * $product->unit_price, 2)}</strong></td>
                                <td>
                                    {if $product->quantity > 0}
                                        <span class="label label-success">In Stock</span>
                                    {else}
                                        <span class="label label-danger">Out of Stock</span>
                                    {/if}
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{$_url}plugin/inventory&action=edit_product&id={$product->id}" class="btn btn-info btn-xs" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="{$_url}plugin/inventory&action=delete_product&id={$product->id}" 
                                           onclick="return confirm('Are you sure you want to delete this product?')" 
                                           class="btn btn-danger btn-xs" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
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

{include file="sections/footer.tpl"}
