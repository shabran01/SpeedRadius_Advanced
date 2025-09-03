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
                <form class="form-horizontal" method="post" role="form" action="{$_url}plugin/expenditure&action=save">
                    <input type="hidden" name="id" value="{$expense.id}">
                    
                    <div class="form-group">
                        <label for="description" class="col-sm-2 control-label">Description *</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="description" name="description" value="{$expense.description}" placeholder="Enter expense description" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount" class="col-sm-2 control-label">Amount *</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <span class="input-group-addon">{$currency_code}</span>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{$expense.amount}" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id" class="col-sm-2 control-label">Category</label>
                        <div class="col-sm-6">
                            <select class="form-control" id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                {foreach $categories as $category}
                                <option value="{$category.id}" {if $category.id == $expense.category_id}selected{/if}>{$category.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="expense_date" class="col-sm-2 control-label">Expense Date *</label>
                        <div class="col-sm-6">
                            <input type="date" class="form-control" id="expense_date" name="expense_date" value="{$expense.expense_date}" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="vendor" class="col-sm-2 control-label">Vendor</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="vendor" name="vendor" value="{$expense.vendor}" placeholder="Enter vendor/supplier name">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="receipt_number" class="col-sm-2 control-label">Receipt Number</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="receipt_number" name="receipt_number" value="{$expense.receipt_number}" placeholder="Enter receipt/invoice number">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes" class="col-sm-2 control-label">Notes</label>
                        <div class="col-sm-6">
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes or details">{$expense.notes}</textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">
                                <span class="glyphicon glyphicon-save" aria-hidden="true"></span> Update Expenditure
                            </button>
                            <a href="{$_url}plugin/expenditure" class="btn btn-default">Cancel</a>
                            <a href="{$_url}plugin/expenditure&action=delete&id={$expense.id}" class="btn btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this expenditure?')">
                                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
