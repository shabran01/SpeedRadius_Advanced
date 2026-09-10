{include file="sections/header.tpl"}

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list-alt"></i> GoWAHA Message Logs</h3>
    </div>
    <div class="panel-body">
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-md-8">
                <form method="get" action="{$_url}plugin/gowaha_logs" class="form-inline">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search phone or message..." value="{$search}">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> Search</button>
                        </span>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-right">
                <form method="post" style="display:inline;" onsubmit="return confirm('Delete ALL logs?')">
                    <button type="submit" name="delete_all" class="btn btn-danger"><i class="fa fa-trash"></i> Clear All Logs</button>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Phone</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Response</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    {if $logs}
                        {foreach $logs as $log}
                        <tr>
                            <td>{$log->id}</td>
                            <td>{$log->phone_number}</td>
                            <td style="max-width:250px;word-break:break-all;">{$log->message|truncate:100}</td>
                            <td>
                                {if $log->status == 'delivered'}
                                    <span class="label label-success">Delivered</span>
                                {elseif $log->status == 'sent'}
                                    <span class="label label-info">Sent</span>
                                {else}
                                    <span class="label label-danger">Failed</span>
                                {/if}
                            </td>
                            <td style="max-width:200px;word-break:break-all;font-size:11px;">{$log->response|truncate:80}</td>
                            <td>{$log->created_at}</td>
                        </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td colspan="6" class="text-center">No logs yet</td>
                        </tr>
                    {/if}
                </tbody>
            </table>
        </div>

        {if $totalPages > 1}
        <div class="text-center">
            <ul class="pagination">
                {for $i=1 to $totalPages}
                    <li {if $i == $page}class="active"{/if}>
                        <a href="{$_url}plugin/gowaha_logs&page={$i}&search={$search}">{$i}</a>
                    </li>
                {/for}
            </ul>
        </div>
        {/if}

        <div style="margin-top:10px;color:#94a3b8;font-size:12px;">
            <i class="fa fa-info-circle"></i> Total: <b>{$total}</b> logs | Showing page <b>{$page}</b> of <b>{$totalPages}</b>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
