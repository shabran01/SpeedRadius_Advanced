{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-warning panel-hovered mb20">
            <div class="panel-heading">Ticket Categories</div>
            <div class="panel-body">
                <form method="post" action="{$_url}plugin/support_tickets&action=save_category" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-md-4 control-label">Category Name</label>
                        <div class="col-md-8">
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Description</label>
                        <div class="col-md-8">
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Category
                            </button>
                            <a href="{$_url}plugin/support_tickets" class="btn btn-default">Back</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Existing Categories</div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $categories as $cat}
                        <tr>
                            <td><strong>{$cat->name}</strong></td>
                            <td><small>{$cat->description}</small></td>
                            <td>
                                <a href="{$_url}plugin/support_tickets&action=delete_category&id={$cat->id}" 
                                   onclick="return confirm('Delete this category?')" 
                                   class="btn btn-danger btn-xs">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6">
        <div class="panel panel-info">
            <div class="panel-heading">Common Categories</div>
            <div class="panel-body">
                <ul>
                    <li><strong>Technical Issues</strong> - Connection problems, speed issues, equipment failures</li>
                    <li><strong>Billing</strong> - Payment queries, invoices, pricing</li>
                    <li><strong>Account Management</strong> - Password resets, profile updates</li>
                    <li><strong>Installation</strong> - New installations, relocations</li>
                    <li><strong>Service Request</strong> - Plan upgrades, downgrades, changes</li>
                    <li><strong>Complaints</strong> - Service quality, support issues</li>
                    <li><strong>General Inquiry</strong> - Questions, information requests</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
