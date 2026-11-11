{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12 col-md-8 col-md-offset-2">
        <div class="panel panel-warning panel-hovered mb20">
            <div class="panel-heading">
                <i class="fa fa-minus-circle"></i> Stock Out - Remove Inventory
            </div>
            <div class="panel-body">
                <form method="post" action="{$_url}plugin/inventory&action=save_stock_out" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Product <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="product_id" class="form-control" required id="product_select">
                                <option value="">-- Select Product --</option>
                                {foreach $products as $prod}
                                    <option value="{$prod->id}" data-stock="{$prod->quantity}">{$prod->name} - Available: {$prod->quantity}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Quantity <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="number" name="quantity" class="form-control" required min="1" id="quantity_input">
                            <small class="text-info" id="available_stock"></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Reason <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="reason" class="form-control" required>
                                <option value="">-- Select Reason --</option>
                                <option value="Sale">Sale/Sold to Customer</option>
                                <option value="Installation">Used for Installation</option>
                                <option value="Damaged">Damaged/Defective</option>
                                <option value="Lost">Lost/Missing</option>
                                <option value="Return">Returned to Supplier</option>
                                <option value="Transfer">Transfer to Other Location</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Customer/Recipient</label>
                        <div class="col-md-9">
                            <input type="text" name="customer" class="form-control" placeholder="Customer name or employee">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Reference</label>
                        <div class="col-md-9">
                            <input type="text" name="reference" class="form-control" placeholder="Invoice, Job ID, etc.">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Notes</label>
                        <div class="col-md-9">
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            <button type="submit" class="btn btn-warning"><i class="fa fa-minus"></i> Remove Stock</button>
                            <a href="{$_url}plugin/inventory" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#product_select').on('change', function() {
        var stock = $(this).find('option:selected').data('stock');
        if (stock !== undefined) {
            $('#available_stock').text('Available stock: ' + stock + ' units');
            $('#quantity_input').attr('max', stock);
        }
    });
    
    $('#quantity_input').on('change', function() {
        var qty = parseInt($(this).val());
        var maxStock = parseInt($(this).attr('max'));
        if (qty > maxStock) {
            alert('Quantity exceeds available stock (' + maxStock + ')');
            $(this).val(maxStock);
        }
    });
});</script>

{include file="sections/footer.tpl"}
