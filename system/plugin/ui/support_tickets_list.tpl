{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-info">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a href="{$_url}plugin/support_tickets&action=dashboard" class="btn btn-default btn-xs">
                        <i class="fa fa-dashboard"></i> Dashboard
                    </a>
                    <a href="{$_url}plugin/support_tickets&action=add" class="btn btn-success btn-xs">
                        <i class="fa fa-plus"></i> New Ticket
                    </a>
                    <a href="{$_url}plugin/support_tickets&action=categories" class="btn btn-warning btn-xs">
                        <i class="fa fa-tags"></i> Categories
                    </a>
                </div>
                All Support Tickets
            </div>
            <div class="panel-body">
                
                <!-- Filters -->
                <form method="post" class="form-horizontal">
                    <div class="row">
                        <div class="col-md-2">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="Open" {if $smarty.post.status == 'Open'}selected{/if}>Open</option>
                                <option value="In Progress" {if $smarty.post.status == 'In Progress'}selected{/if}>In Progress</option>
                                <option value="Pending" {if $smarty.post.status == 'Pending'}selected{/if}>Pending</option>
                                <option value="Closed" {if $smarty.post.status == 'Closed'}selected{/if}>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="priority" class="form-control">
                                <option value="">All Priorities</option>
                                <option value="Low" {if $smarty.post.priority == 'Low'}selected{/if}>Low</option>
                                <option value="Normal" {if $smarty.post.priority == 'Normal'}selected{/if}>Normal</option>
                                <option value="High" {if $smarty.post.priority == 'High'}selected{/if}>High</option>
                                <option value="Urgent" {if $smarty.post.priority == 'Urgent'}selected{/if}>Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="category" class="form-control">
                                <option value="">All Categories</option>
                                {foreach $categories as $cat}
                                    <option value="{$cat->id}" {if $smarty.post.category == $cat->id}selected{/if}>{$cat->name}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="assigned" class="form-control">
                                <option value="">All Assignments</option>
                                <option value="me" {if $smarty.post.assigned == 'me'}selected{/if}>Assigned to Me</option>
                                <option value="unassigned" {if $smarty.post.assigned == 'unassigned'}selected{/if}>Unassigned</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="{$smarty.post.search}">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <hr>

                <!-- Tickets Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="ticketsTable">
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $tickets as $ticket}
                            <tr>
                                <td><strong>{$ticket->ticket_number}</strong></td>
                                <td>
                                    <strong>{$ticket->customer_name}</strong><br>
                                    <small>{$ticket->customer_username}</small><br>
                                    <small>{$ticket->customer_phone}</small>
                                </td>
                                <td>{$ticket->subject}</td>
                                <td><span class="label label-default">{$ticket->category_name}</span></td>
                                <td>
                                    {if $ticket->priority == 'Urgent'}
                                        <span class="label label-danger">{$ticket->priority}</span>
                                    {elseif $ticket->priority == 'High'}
                                        <span class="label label-warning">{$ticket->priority}</span>
                                    {elseif $ticket->priority == 'Low'}
                                        <span class="label label-default">{$ticket->priority}</span>
                                    {else}
                                        <span class="label label-info">{$ticket->priority}</span>
                                    {/if}
                                </td>
                                <td>
                                    {if $ticket->status == 'Closed'}
                                        <span class="label label-success">{$ticket->status}</span>
                                    {elseif $ticket->status == 'In Progress'}
                                        <span class="label label-primary">{$ticket->status}</span>
                                    {elseif $ticket->status == 'Pending'}
                                        <span class="label label-warning">{$ticket->status}</span>
                                    {else}
                                        <span class="label label-warning">{$ticket->status}</span>
                                    {/if}
                                </td>
                                <td>
                                    {if $ticket->assigned_to}
                                        <span class="label label-info">{$ticket->assigned_to}</span>
                                    {else}
                                        <span class="label label-default">Unassigned</span>
                                    {/if}
                                </td>
                                <td><small>{$ticket->created_at}</small></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{$_url}plugin/support_tickets&action=view&id={$ticket->id}" 
                                           class="btn btn-info btn-xs" title="View">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{$_url}plugin/support_tickets&action=delete&id={$ticket->id}" 
                                           onclick="return confirm('Delete this ticket?')" 
                                           class="btn btn-danger btn-xs" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
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

<script>
$(document).ready(function() {
    $('#ticketsTable').DataTable({
        "order": [[7, "desc"]],
        "pageLength": 25
    });
});
</script>

{include file="sections/footer.tpl"}
