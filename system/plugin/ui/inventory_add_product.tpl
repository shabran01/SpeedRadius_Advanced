{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12 col-md-8 col-md-offset-2">
        <div class="panel panel-primary panel-hovered mb20">
            <div class="panel-heading">Add New Product</div>
            <div class="panel-body">
                <form method="post" action="{$_url}plugin/inventory&action=save_product" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Product Name <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">SKU</label>
                        <div class="col-md-9">
                            <input type="text" name="sku" class="form-control" placeholder="Product Code">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Category</label>
                        <div class="col-md-9">
                            <select name="category_id" class="form-control">
                                <option value="">-- Select Category --</option>
                                {foreach $categories as $cat}
                                    <option value="{$cat->id}">{$cat->name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Supplier</label>
                        <div class="col-md-9">
                            <select name="supplier_id" class="form-control">
                                <option value="">-- Select Supplier --</option>
                                {foreach $suppliers as $sup}
                                    <option value="{$sup->id}">{$sup->name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Unit Price (Ksh)</label>
                        <div class="col-md-9">
                            <input type="number" name="unit_price" class="form-control" step="0.01" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Selling Price (Ksh)</label>
                        <div class="col-md-9">
                            <input type="number" name="selling_price" class="form-control" step="0.01" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Initial Quantity</label>
                        <div class="col-md-9">
                            <input type="number" name="quantity" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Reorder Level</label>
                        <div class="col-md-9">
                            <input type="number" name="reorder_level" class="form-control" value="10">
                            <small class="text-muted">Alert when stock reaches this level</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Description</label>
                        <div class="col-md-9">
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save Product</button>
                            <a href="{$_url}plugin/inventory&action=products" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}