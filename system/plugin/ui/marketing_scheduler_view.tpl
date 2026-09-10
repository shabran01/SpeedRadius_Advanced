{include file="sections/header.tpl"}

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
        {/if}

        <div class="panel panel-hovered mb20
            {if $campaign.status == 'sent'}panel-success
            {elseif $campaign.status == 'pending'}panel-warning
            {elseif $campaign.status == 'running'}panel-info
            {elseif $campaign.status == 'failed'}panel-danger
            {else}panel-default{/if}">

            <div class="panel-heading">
                <div class="pull-right">
                    <a href="{$_url}plugin/marketing_scheduler" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                    {if $campaign.status == 'pending'}
                        <a href="{$_url}plugin/marketing_scheduler/edit/{$campaign.id}"
                           class="btn btn-warning btn-sm">
                            <i class="fa fa-pencil"></i> Edit
                        </a>
                        <a href="{$_url}plugin/marketing_scheduler/send_now/{$campaign.id}"
                           class="btn btn-success btn-sm"
                           onclick="return confirm('Send this campaign right now instead of waiting for the scheduled time?')">
                            <i class="fa fa-send"></i> Send Now
                        </a>
                        <a href="{$_url}plugin/marketing_scheduler/cancel/{$campaign.id}"
                           class="btn btn-default btn-sm"
                           onclick="return confirm('Cancel this campaign? It will not be sent.')">
                            <i class="fa fa-ban"></i> Cancel
                        </a>
                    {/if}
                    {if $campaign.status == 'failed'}
                        <a href="{$_url}plugin/marketing_scheduler/send_now/{$campaign.id}"
                           class="btn btn-success btn-sm"
                           onclick="return confirm('Retry sending this campaign now?')">
                            <i class="fa fa-refresh"></i> Retry Now
                        </a>
                    {/if}
                    {if $campaign.status != 'running'}
                        <a href="{$_url}plugin/marketing_scheduler/delete/{$campaign.id}"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Permanently delete this campaign?')">
                            <i class="fa fa-trash"></i> Delete
                        </a>
                    {/if}
                </div>
                <i class="fa fa-bullhorn"></i> {$campaign.title}
            </div><!-- /panel-heading -->

            <div class="panel-body">
                <div class="row">

                    <!-- Left column: Details -->
                    <div class="col-md-5">
                        <table class="table table-condensed table-bordered">
                            <tr>
                                <th style="width:40%">Status</th>
                                <td>
                                    {if $campaign.status == 'pending'}
                                        <span class="label label-warning label-lg">
                                            <i class="fa fa-clock-o"></i> Pending — waiting for scheduled time
                                        </span>
                                    {elseif $campaign.status == 'running'}
                                        <span class="label label-info label-lg">
                                            <i class="fa fa-spinner fa-spin"></i> Running...
                                        </span>
                                    {elseif $campaign.status == 'sent'}
                                        <span class="label label-success label-lg">
                                            <i class="fa fa-check"></i> Sent Successfully
                                        </span>
                                    {elseif $campaign.status == 'cancelled'}
                                        <span class="label label-default label-lg">
                                            <i class="fa fa-ban"></i> Cancelled
                                        </span>
                                    {elseif $campaign.status == 'failed'}
                                        <span class="label label-danger label-lg">
                                            <i class="fa fa-times"></i> Failed
                                        </span>
                                    {/if}
                                </td>
                            </tr>
                            <tr>
                                <th>Scheduled At</th>
                                <td>{$campaign.scheduled_at}</td>
                            </tr>
                            {if $campaign.sent_at}
                            <tr>
                                <th>Sent At</th>
                                <td>{$campaign.sent_at}</td>
                            </tr>
                            {/if}
                            <tr>
                                <th>Target Group</th>
                                <td>
                                    {$campaign.group_filter}
                                    {if !empty($campaign.router_filter)}
                                        <span class="text-muted">/ {$campaign.router_filter}</span>
                                    {/if}
                                </td>
                            </tr>
                            <tr>
                                <th>Send Via</th>
                                <td>
                                    {if $campaign.via == 'sms'}
                                        <span class="label label-info">SMS</span>
                                    {elseif $campaign.via == 'wa'}
                                        <span class="label label-success">WhatsApp</span>
                                    {else}
                                        <span class="label label-primary">SMS + WhatsApp</span>
                                    {/if}
                                </td>
                            </tr>
                            <tr>
                                <th>Batch Size</th>
                                <td>{$campaign.batch_size} per batch</td>
                            </tr>
                            <tr>
                                <th>Delay</th>
                                <td>{$campaign.delay_seconds} seconds between batches</td>
                            </tr>
                            <tr>
                                <th>Created By</th>
                                <td>{$campaign.created_by}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{$campaign.created_at}</td>
                            </tr>
                        </table>
                    </div><!-- /col -->

                    <!-- Right column: Stats + Message Preview -->
                    <div class="col-md-7">

                        {if $campaign.status == 'sent' || $campaign.status == 'failed'}
                        <!-- Stats row -->
                        <div class="row text-center" style="margin-bottom:20px;">
                            <div class="col-xs-4">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <h2 class="text-primary" style="margin:0;">{$campaign.total_recipients}</h2>
                                        <small class="text-muted">Total Recipients</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <h2 class="text-success" style="margin:0;">{$campaign.total_sent}</h2>
                                        <small class="text-muted">Sent</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <h2 class="text-danger" style="margin:0;">{$campaign.total_failed}</h2>
                                        <small class="text-muted">Failed</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {/if}

                        <!-- Message Preview -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-comment-o"></i> Message Preview
                            </div>
                            <div class="panel-body">
                                <pre style="white-space:pre-wrap;font-family:inherit;background:transparent;border:none;padding:0;">{$campaign.message}</pre>
                            </div>
                        </div>

                        {if $campaign.status == 'pending'}
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            This campaign will be sent automatically by the system cron job when
                            <strong>{$campaign.scheduled_at}</strong> arrives.
                            You can also click <strong>Send Now</strong> to trigger it immediately.
                        </div>
                        {/if}

                    </div><!-- /col -->
                </div><!-- /row -->
            </div><!-- /panel-body -->
        </div><!-- /panel -->
    </div>
</div>

{include file="sections/footer.tpl"}
