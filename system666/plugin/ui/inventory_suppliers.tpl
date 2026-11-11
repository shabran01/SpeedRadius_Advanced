{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-warning panel-hovered mb20">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a href="{$_url}plugin/inventory" class="btn btn-default btn-xs">
                        <i class="fa fa-dashboard"></i> Dashboard
                    </a>
                </div>
                Suppliers Management
            </div>
            <div class="panel-body">
                
                <div class="row">
                    <div class="col-md-5">
                        <div class="panel panel-primary">
                            <div class="panel-heading">Add New Supplier</div>
                            <div class="panel-body">
                                <form method="post" action="{$_url}plugin/inventory&action=save_supplier">
                                    <div class="form-group">
                                        <label>Supplier Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Contact Person</label>
                                        <input type="text" name="contact_person" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="text" name="phone" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Address</label>
                                        <textarea name="address" class="form-control" rows="3"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fa fa-save"></i> Save Supplier
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="panel panel-default">
                            <div class="panel-heading">Suppliers List</div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Supplier Name</th>
                                            <th>Contact Person</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach $suppliers as $sup}
                                        <tr>
                                            <td><strong>{$sup->name}</strong></td>
                                            <td>{$sup->contact_person}</td>
                                            <td>{$sup->phone}</td>
                                            <td>{$sup->email}</td>
                                            <td>
                                                <a href="{$_url}plugin/inventory&action=delete_supplier&id={$sup->id}" 
                                                   onclick="return confirm('Delete this supplier?')" 
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
                </div>

            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
