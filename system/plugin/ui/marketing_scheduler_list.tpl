{include file="sections/header.tpl"}
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">

<div class="row">
    <div class="col-sm-12">

        {if $smarty.get.s == 's'}
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-check-circle"></i> {$smarty.get.m}
            </div>
        {elseif $smarty.get.s == 'e'}
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-exclamation-circle"></i> {$smarty.get.m}
            </div>
        {elseif $smarty.get.s == 'w'}
            <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-exclamation-triangle"></i> {$smarty.get.m}
            </div>
        {/if}

        <div class="panel panel-primary panel-hovered mb20">
            <div class="panel-heading">
                <div class="pull-right">
                    <a href="{$_url}plugin/marketing_scheduler/add" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> New Campaign
                    </a>
                </div>
                <i class="fa fa-calendar"></i> Marketing Campaigns
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="campaignsTable" class="table table-bordered table-striped table-hover table-condensed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Scheduled At</th>
                                <th>Target Group</th>
                                <th>Via</th>
                                <th>Status</th>
                                <th>Sent / Failed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $campaigns as $c}
                            <tr>
                                <td>{$c.id}</td>
                                <td><strong>{$c.title}</strong></td>
                                <td>{$c.scheduled_at}</td>
                                <td>
                                    {$c.group_filter}
                                    {if !empty($c.router_filter)}
                                        <span class="text-muted">/ {$c.router_filter}</span>
                                    {/if}
                                </td>
                                <td>
                                    {if $c.via == 'sms'}
                                        <span class="label label-info">SMS</span>
                                    {elseif $c.via == 'wa'}
                                        <span class="label label-success">WhatsApp</span>
                                    {else}
                                        <span class="label label-primary">SMS + WA</span>
                                    {/if}
                                </td>
                                <td>
                                    {if $c.status == 'pending'}
                                        <span class="label label-warning"><i class="fa fa-clock-o"></i> Pending</span>
                                    {elseif $c.status == 'running'}
                                        <span class="label label-info"><i class="fa fa-spinner fa-spin"></i> Running</span>
                                    {elseif $c.status == 'sent'}
                                        <span class="label label-success"><i class="fa fa-check"></i> Sent</span>
                                    {elseif $c.status == 'cancelled'}
                                        <span class="label label-default"><i class="fa fa-ban"></i> Cancelled</span>
                                    {elseif $c.status == 'failed'}
                                        <span class="label label-danger"><i class="fa fa-times"></i> Failed</span>
                                    {else}
                                        <span class="label label-default">{$c.status}</span>
                                    {/if}
                                </td>
                                <td>
                                    {if $c.status == 'sent' || $c.status == 'failed'}
                                        <span class="text-success">{$c.total_sent}</span>
                                        /
                                        <span class="text-danger">{$c.total_failed}</span>
                                        <br><small class="text-muted">{$c.total_recipients} total</small>
                                    {else}
                                        <span class="text-muted">—</span>
                                    {/if}
                                </td>
                                <td nowrap>
                                    <a href="{$_url}plugin/marketing_scheduler/view/{$c.id}"
                                       class="btn btn-xs btn-info" title="View Details">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    {if $c.status == 'pending'}
                                        <a href="{$_url}plugin/marketing_scheduler/edit/{$c.id}"
                                           class="btn btn-xs btn-warning" title="Edit">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="{$_url}plugin/marketing_scheduler/send_now/{$c.id}"
                                           class="btn btn-xs btn-success"
                                           onclick="return confirm('Send this campaign right now?')"
                                           title="Send Now">
                                            <i class="fa fa-send"></i>
                                        </a>
                                        <a href="{$_url}plugin/marketing_scheduler/cancel/{$c.id}"
                                           class="btn btn-xs btn-default"
                                           onclick="return confirm('Cancel this campaign?')"
                                           title="Cancel">
                                            <i class="fa fa-ban"></i>
                                        </a>
                                    {/if}
                                    {if $c.status == 'failed'}
                                        <a href="{$_url}plugin/marketing_scheduler/send_now/{$c.id}"
                                           class="btn btn-xs btn-success"
                                           onclick="return confirm('Retry sending this campaign now?')"
                                           title="Retry">
                                            <i class="fa fa-refresh"></i> Retry
                                        </a>
                                    {/if}
                                    {if $c.status != 'running'}
                                        <a href="{$_url}plugin/marketing_scheduler/delete/{$c.id}"
                                           class="btn btn-xs btn-danger"
                                           onclick="return confirm('Permanently delete this campaign?')"
                                           title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    {/if}
                                </td>
                            </tr>
                            {foreachelse}
                            <tr>
                                <td colspan="8" class="text-center text-muted" style="padding:30px;">
                                    <i class="fa fa-calendar-o fa-2x"></i><br><br>
                                    No campaigns yet.
                                    <a href="{$_url}plugin/marketing_scheduler/add">Create your first campaign</a>
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

<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    $('#campaignsTable').DataTable({
        order: [[2, 'desc']],
        pageLength: 25,
        columnDefs: [{ orderable: false, targets: 7 }]
    });
});
</script>

{include file="sections/footer.tpl"}
