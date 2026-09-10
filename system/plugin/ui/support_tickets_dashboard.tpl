{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-info">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a href="{$_url}plugin/support_tickets&action=add" class="btn btn-success btn-xs">
                        <i class="fa fa-plus"></i> New Ticket
                    </a>
                    <a href="{$_url}plugin/support_tickets&action=categories" class="btn btn-warning btn-xs">
                        <i class="fa fa-tags"></i> Categories
                    </a>
                    <a href="{$_url}plugin/support_tickets&action=dashboard" class="btn btn-primary btn-xs">
                        <i class="fa fa-dashboard"></i> Dashboard
                    </a>
                </div>
                Support Tickets Dashboard
            </div>
            <div class="panel-body">
                
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-primary panel-colorful">
                            <div class="panel-body text-center">
                                <h3 class="margin-0">{$total_tickets}</h3>
                                <p class="margin-0">Total Tickets</p>
                            </div>
                            <div class="panel-footer text-center">
                                <i class="fa fa-ticket fa-3x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-warning panel-colorful">
                            <div class="panel-body text-center">
                                <h3 class="margin-0">{$open_tickets}</h3>
                                <p class="margin-0">Open Tickets</p>
                            </div>
                            <div class="panel-footer text-center">
                                <i class="fa fa-folder-open fa-3x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-success panel-colorful">
                            <div class="panel-body text-center">
                                <h3 class="margin-0">{$closed_tickets}</h3>
                                <p class="margin-0">Closed Tickets</p>
                            </div>
                            <div class="panel-footer text-center">
                                <i class="fa fa-check-circle fa-3x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-info panel-colorful">
                            <div class="panel-body text-center">
                                <h3 class="margin-0">{$my_tickets}</h3>
                                <p class="margin-0">Assigned to Me</p>
                            </div>
                            <div class="panel-footer text-center">
                                <i class="fa fa-user fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Priority Alerts -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i> 
                            <strong>{$urgent_priority}</strong> Urgent Priority Tickets
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-circle"></i> 
                            <strong>{$high_priority}</strong> High Priority Tickets
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="alert alert-info">
                            <i class="fa fa-clock-o"></i> 
                            Avg Response: <strong>{$avg_response_hours}h</strong> | Unassigned: <strong>{$unassigned}</strong>
                        </div>
                    </div>
                </div>

                <!-- Recent Tickets -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-list"></i> Recent Tickets
                        <a href="{$_url}plugin/support_tickets&action=list" class="btn btn-xs btn-primary pull-right">
                            View All Tickets
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Customer</th>
                                    <th>Subject</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Assigned To</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach $recent_tickets as $ticket}
                                <tr>
                                    <td><strong>{$ticket->ticket_number}</strong></td>
                                    <td>
                                        <a href="{$_url}customers/viewu/{$ticket->customer_username}">
                                            {$ticket->customer_name}
                                        </a>
                                    </td>
                                    <td>{$ticket->subject}</td>
                                    <td><span class="label label-default">{$ticket->category_name}</span></td>
                                    <td>
                                        {if $ticket->priority == 'Urgent'}
                                            <span class="label label-danger">{$ticket->priority}</span>
                                        {elseif $ticket->priority == 'High'}
                                            <span class="label label-warning">{$ticket->priority}</span>
                                        {else}
                                            <span class="label label-info">{$ticket->priority}</span>
                                        {/if}
                                    </td>
                                    <td>
                                        {if $ticket->status == 'Closed'}
                                            <span class="label label-success">{$ticket->status}</span>
                                        {elseif $ticket->status == 'In Progress'}
                                            <span class="label label-primary">{$ticket->status}</span>
                                        {else}
                                            <span class="label label-warning">{$ticket->status}</span>
                                        {/if}
                                    </td>
                                    <td>{$ticket->assigned_to}</td>
                                    <td><small>{$ticket->created_at}</small></td>
                                    <td>
                                        <a href="{$_url}plugin/support_tickets&action=view&id={$ticket->id}" 
                                           class="btn btn-info btn-xs">
                                            <i class="fa fa-eye"></i> View
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

{include file="sections/footer.tpl"}
