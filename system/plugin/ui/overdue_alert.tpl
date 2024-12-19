{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title">Overdue Customers</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="overdue_table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Username</th>
                                <th>Plan</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Expiration Date</th>
                                <th>Days Left</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $d as $ds}
                                <tr>
                                    <td>{$ds['fullname']}</td>
                                    <td>{$ds['username']}</td>
                                    <td>{$ds['plan_name']}</td>
                                    <td>{$ds['phonenumber']}</td>
                                    <td>{$ds['email']}</td>
                                    <td>{$ds['expiration']}</td>
                                    <td>{ceil((strtotime($ds['expiration']) - time()) / 86400)}</td>
                                    <td>
                                        <a href="index.php?_route=customers/view/{$ds['id']}" class="btn btn-info btn-xs">View</a>
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
