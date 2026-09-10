{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info panel-hovered mb20">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a href="{$_url}plugin/support_tickets" class="btn btn-default btn-xs">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                    {if $ticket->status != 'Closed'}
                        <a href="{$_url}plugin/support_tickets&action=close&id={$ticket->id}" 
                           onclick="return confirm('Close this ticket?')" 
                           class="btn btn-success btn-xs">
                            <i class="fa fa-check"></i> Close Ticket
                        </a>
                    {else}
                        <a href="{$_url}plugin/support_tickets&action=reopen&id={$ticket->id}" 
                           class="btn btn-warning btn-xs">
                            <i class="fa fa-undo"></i> Reopen
                        </a>
                    {/if}
                </div>
                Ticket #{$ticket->ticket_number}
            </div>
            <div class="panel-body">
                
                <!-- Ticket Information -->
                <div class="row">
                    <div class="col-md-8">
                        <h4>{$ticket->subject}</h4>
                        <div class="well">
                            {$ticket->description|nl2br}
                        </div>
                        
                        <p>
                            <strong>Category:</strong> <span class="label label-default">{$ticket->category_name}</span>
                            <strong>Priority:</strong> 
                            {if $ticket->priority == 'Urgent'}
                                <span class="label label-danger">{$ticket->priority}</span>
                            {elseif $ticket->priority == 'High'}
                                <span class="label label-warning">{$ticket->priority}</span>
                            {else}
                                <span class="label label-info">{$ticket->priority}</span>
                            {/if}
                            <strong>Status:</strong>
                            {if $ticket->status == 'Closed'}
                                <span class="label label-success">{$ticket->status}</span>
                            {else}
                                <span class="label label-warning">{$ticket->status}</span>
                            {/if}
                        </p>
                        
                        <hr>
                        
                        <!-- Replies -->
                        <h4><i class="fa fa-comments"></i> Conversation</h4>
                        {foreach $replies as $reply}
                        <div class="panel {if $reply->is_staff}panel-info{else}panel-default{/if}">
                            <div class="panel-body">
                                <p>{$reply->message|nl2br}</p>
                                <small class="text-muted">
                                    <i class="fa fa-user"></i> {$reply->replied_by} | 
                                    <i class="fa fa-clock-o"></i> {$reply->created_at}
                                </small>
                            </div>
                        </div>
                        {/foreach}
                        
                        <!-- Reply Form -->
                        {if $ticket->status != 'Closed'}
                        <div class="panel panel-primary">
                            <div class="panel-heading">Add Reply</div>
                            <div class="panel-body">
                                <form method="post" action="{$_url}plugin/support_tickets&action=reply">
                                    <input type="hidden" name="ticket_id" value="{$ticket->id}">
                                    <div class="form-group">
                                        <textarea name="message" class="form-control" rows="4" required placeholder="Type your reply..."></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Change Status:</label>
                                        <select name="status" class="form-control">
                                            <option value="">Keep Current</option>
                                            <option value="Open">Open</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Pending">Pending Customer</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-reply"></i> Send Reply
                                    </button>
                                </form>
                            </div>
                        </div>
                        {/if}
                    </div>
                    
                    <!-- Sidebar -->
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">Customer Information</div>
                            <div class="panel-body">
                                <p><strong>Name:</strong> {$ticket->customer_name}</p>
                                <p><strong>Username:</strong> {$ticket->customer_username}</p>
                                <p><strong>Phone:</strong> {$ticket->customer_phone}</p>
                                <p><strong>Email:</strong> {$ticket->customer_email}</p>
                                <a href="{$_url}customers/viewu/{$ticket->customer_username}" class="btn btn-info btn-sm btn-block">
                                    <i class="fa fa-user"></i> View Customer
                                </a>
                            </div>
                        </div>
                        
                        <div class="panel panel-default">
                            <div class="panel-heading">Ticket Details</div>
                            <div class="panel-body">
                                <p><strong>Created:</strong><br><small>{$ticket->created_at}</small></p>
                                <p><strong>Created By:</strong> {$ticket->created_by}</p>
                                <p><strong>Last Updated:</strong><br><small>{$ticket->updated_at}</small></p>
                                {if $ticket->first_response_at}
                                <p><strong>First Response:</strong><br><small>{$ticket->first_response_at}</small></p>
                                {/if}
                                {if $ticket->closed_at}
                                <p><strong>Closed:</strong><br><small>{$ticket->closed_at}</small></p>
                                <p><strong>Closed By:</strong> {$ticket->closed_by}</p>
                                {/if}
                            </div>
                        </div>
                        
                        <div class="panel panel-warning">
                            <div class="panel-heading">Assign Ticket</div>
                            <div class="panel-body">
                                <form method="post" action="{$_url}plugin/support_tickets&action=assign">
                                    <input type="hidden" name="id" value="{$ticket->id}">
                                    <div class="form-group">
                                        <select name="assign_to" class="form-control">
                                            <option value="">-- Unassigned --</option>
                                            {foreach $admins as $adm}
                                                <option value="{$adm->username}" {if $ticket->assigned_to == $adm->username}selected{/if}>
                                                    {$adm->fullname} ({$adm->username})
                                                </option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-warning btn-block">
                                        <i class="fa fa-user-plus"></i> Assign
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
