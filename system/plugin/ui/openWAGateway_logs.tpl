{include file="sections/header.tpl"}

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> OpenWA Message Logs</h3>
            </div>
            <div class="panel-body">
                <form method="get" action="{$_url}plugin/openwa_logs" class="form-inline" style="margin-bottom:15px;">
                    <input type="hidden" name="_route" value="plugin/openwa_logs">
                    <input type="text" name="search" class="form-control" value="{$search}" placeholder="Search number or message">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                    <a href="{$_url}plugin/openwa_logs" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</a>
                    <a href="{$_url}plugin/openwa" class="btn btn-default pull-right"><i class="fa fa-arrow-left"></i> Back</a>
                </form>

                <form method="post" onsubmit="return confirm('Delete every log entry?');">
                    <button type="submit" name="delete_all" value="1" class="btn btn-danger btn-sm" style="margin-bottom:12px;">
                        <i class="fa fa-trash"></i> Delete All
                    </button>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Phone</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Sent At</th>
                            </tr>
                        </thead>
                        <tbody>
                            {if $logs}
                            {foreach $logs as $log}
                            <tr>
                                <td>{$log->id}</td>
                                <td>{$log->phone_number}</td>
                                <td style="max-width:420px;overflow-wrap:break-word;">{$log->message|truncate:100}</td>
                                <td>
                                    {if $log->status == 'delivered'}
                                    <span class="label label-success">delivered</span>
                                    {elseif $log->status == 'failed'}
                                    <span class="label label-danger">failed</span>
                                    {else}
                                    <span class="label label-default">{$log->status}</span>
                                    {/if}
                                </td>
                                <td>{$log->created_at}</td>
                            </tr>
                            {/foreach}
                            {else}
                            <tr><td colspan="5" class="text-center text-muted">No messages yet</td></tr>
                            {/if}
                        </tbody>
                    </table>
                </div>

                {if $totalPages > 1}
                <nav>
                    <ul class="pagination">
                        {for $i=1 to $totalPages}
                        <li class="{if $i == $page}active{/if}">
                            <a href="{$_url}plugin/openwa_logs&page={$i}&search={$search}">{$i}</a>
                        </li>
                        {/for}
                    </ul>
                </nav>
                {/if}

                <p class="text-muted">{$total} total entries</p>
            </div>
        </div>
    </div>
</div>
