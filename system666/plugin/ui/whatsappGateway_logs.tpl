{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">WhatsApp Message Logs</div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Phone Number</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Sender</th>
                                <th>Response</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $logs as $log}
                                <tr>
                                    <td>{$log['id']}</td>
                                    <td>{$log['phone_number']}</td>
                                    <td>{$log['message']}</td>
                                    <td>
                                        {if $log['status'] eq 'delivered'}
                                            <span class="label label-success">{$log['status']}</span>
                                        {elseif $log['status'] eq 'failed'}
                                            <span class="label label-danger">{$log['status']}</span>
                                        {else}
                                            <span class="label label-warning">{$log['status']}</span>
                                        {/if}
                                    </td>
                                    <td>{$log['sender']}</td>
                                    <td><small>{$log['response']}</small></td>
                                    <td>{$log['created_at']}</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>

                {if $total_pages > 1}
                <ul class="pagination">
                    {if $page > 1}
                        <li><a href="{$_url}plugin/whatsappGateway_logs&page={$page-1}">Previous</a></li>
                    {/if}
                    
                    {for $p=1 to $total_pages}
                        <li {if $p eq $page}class="active"{/if}>
                            <a href="{$_url}plugin/whatsappGateway_logs&page={$p}">{$p}</a>
                        </li>
                    {/for}
                    
                    {if $page < $total_pages}
                        <li><a href="{$_url}plugin/whatsappGateway_logs&page={$page+1}">Next</a></li>
                    {/if}
                </ul>
                {/if}
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
