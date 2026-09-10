{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12 col-md-8 col-md-offset-2">
        <div class="panel panel-success panel-hovered mb20">
            <div class="panel-heading">
                <i class="fa fa-plus-circle"></i> Stock In - Add Inventory
            </div>
            <div class="panel-body">
                <form method="post" action="{$_url}plugin/inventory&action=save_stock_in" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Product <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="product_id" class="form-control" required id="product_select">
                                <option value="">-- Select Product --</option>
                                {foreach $products as $prod}
                                    <option value="{$prod->id}" data-price="{$prod->unit_price}">{$prod->name} (SKU: {$prod->sku}) - Current Stock: {$prod->quantity}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Quantity <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="number" name="quantity" class="form-control" required min="1" id="quantity_input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Unit Cost (Ksh)</label>
                        <div class="col-md-9">
                            <input type="number" name="unit_cost" class="form-control" step="0.01" id="unit_cost_input">
                            <small class="text-muted">Leave empty to use current unit price</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Total Cost (Ksh)</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="total_cost" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Supplier</label>
                        <div class="col-md-9">
                            <select name=\"supplier_id\" class=\"form-control\">\n                                <option value=\"\">-- Select Supplier --</option>\n                                {foreach $suppliers as $sup}\n                                    <option value=\"{$sup->id}\">{$sup->name}</option>\n                                {/foreach}\n                            </select>\n                        </div>\n                    </div>\n                    <div class=\"form-group\">\n                        <label class=\"col-md-3 control-label\">Reference/Invoice</label>\n                        <div class=\"col-md-9\">\n                            <input type=\"text\" name=\"reference\" class=\"form-control\" placeholder=\"PO Number, Invoice Number, etc.\">\n                        </div>\n                    </div>\n                    <div class=\"form-group\">\n                        <label class=\"col-md-3 control-label\">Notes</label>\n                        <div class=\"col-md-9\">\n                            <textarea name=\"notes\" class=\"form-control\" rows=\"3\"></textarea>\n                        </div>\n                    </div>\n                    <div class=\"form-group\">\n                        <div class=\"col-md-9 col-md-offset-3\">\n                            <button type=\"submit\" class=\"btn btn-success\"><i class=\"fa fa-save\"></i> Add Stock</button>\n                            <a href=\"{$_url}plugin/inventory\" class=\"btn btn-default\">Cancel</a>\n                        </div>\n                    </div>\n                </form>\n            </div>\n        </div>\n    </div>\n</div>\n\n<script>\n$(document).ready(function() {\n    function calculateTotal() {\n        var qty = parseFloat($('#quantity_input').val()) || 0;\n        var unitCost = parseFloat($('#unit_cost_input').val());\n        \n        if (isNaN(unitCost)) {\n            var selectedOption = $('#product_select option:selected');\n            unitCost = parseFloat(selectedOption.data('price')) || 0;\n        }\n        \n        var total = qty * unitCost;\n        $('#total_cost').val('Ksh ' + total.toFixed(2));\n    }\n    \n    $('#quantity_input, #unit_cost_input, #product_select').on('change keyup', calculateTotal);\n});\n</script>\n\n{include file=\"sections/footer.tpl\"}
