{include file="sections/header.tpl"}
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                Users Data Usage
            </div>
            <div class="panel-body">
                <div class="text-center" style="padding: 15px">
                    <div class="col-md-4">
                        <form id="site-search" method="post" action="{$_url}plugin/UserDataUsageAdmin">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <span class="fa fa-search"></span>
                                </div>
                                <input type="text" name="q" class="form-control" value="{$q}"
                                       placeholder="Search by username">
                                <div class="input-group-btn">
                                    <button class="btn btn-success" type="submit">Search</button>
                                </div>
                            </div>
                        </form>

                    </div>
                    <div class="col-md-1">
                        <form method="post" action="{$_url}plugin/UserDataUsageAdmin">
                            <div class="input-group-btn">
                                <button class="btn btn-danger" type="submit">Clear</button>
                            </div>
                        </form>
                    </div>
                </div>
                &nbsp;
            </div>
            <br>
            <div class="table-responsive">
                <table id="datatable" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Username</th>
                        <th>Input</th>
                        <th>Output</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach $data as $row}
                        <tr>
                            <td>{$row.username}</td>
                            <td>{$row.acctInputOctets}</td>
                            <td>{$row.acctOutputOctets}</td>
                            <td>{$row.totalBytes}</td>
                            <td>{$row.status}</td>
                            <td>{$row.dateAdded}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
            {include file="pagination.tpl"}
        </div>
    </div>
</div>
{include file="sections/footer.tpl"}

<script>
    window.addEventListener('DOMContentLoaded', function() {
        var amolood = "https://github.com/amolood";
        $('#version').html('Plugin by: <a href="' + amolood + '">Amolood</a>');
    });
</script>
