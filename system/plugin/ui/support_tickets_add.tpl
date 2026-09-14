{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12 col-md-8 col-md-offset-2">
        <div class="panel panel-success panel-hovered mb20">
            <div class="panel-heading">Create New Support Ticket</div>
            <div class="panel-body">
                <form method="post" action="{$_url}plugin/support_tickets&action=save" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Customer <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="customer_id" class="form-control select2" required>
                                <option value="">-- Select Customer --</option>
                                {foreach $customers as $cust}
                                    <option value="{$cust->id}">{$cust->fullname} ({$cust->username})</option>
                                {/foreach}
                            </select>
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
                        <label class="col-md-3 control-label">Priority</label>
                        <div class="col-md-9">
                            <select name="priority" class="form-control">
                                <option value="Low">Low</option>
                                <option value="Normal" selected>Normal</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Subject <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Description <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <textarea name="description" class="form-control" rows="6" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Create Ticket</button>
                            <a href="{$_url}plugin/support_tickets" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
