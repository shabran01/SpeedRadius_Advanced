{include file="sections/header.tpl"}

<style>
.connected-devices {
    margin-top: 8px;
}
.btn-3d {
    position: relative;
    border: none;
    outline: none;
    cursor: pointer;
    border-radius: 5px;
    box-shadow: 0 3px 0 #0e76a8;
    transition: all 0.1s ease;
}

.btn-3d:active {
    transform: translateY(2px);
    box-shadow: 0 1px 0 #0e76a8;
}

.btn-3d-primary {
    background: linear-gradient(to bottom, #3498db, #2980b9);
    color: white;
}

.btn-3d-success {
    background: linear-gradient(to bottom, #2ecc71, #27ae60);
    color: white;
    box-shadow: 0 3px 0 #27ae60;
}

.btn-3d-success:active {
    box-shadow: 0 1px 0 #27ae60;
}

.btn-3d-warning {
    background: linear-gradient(to bottom, #e67e22, #d35400);
    color: white;
    box-shadow: 0 3px 0 #d35400;
}

.btn-3d-warning:active {
    box-shadow: 0 1px 0 #d35400;
}

.btn-3d-danger {
    background: linear-gradient(to bottom, #e74c3c, #c0392b);
    color: white;
    box-shadow: 0 3px 0 #c0392b;
}

.btn-3d-danger:active {
    box-shadow: 0 1px 0 #c0392b;
}

.btn-3d-info {
    background: linear-gradient(to bottom, #3498db, #2980b9);
    color: white;
    box-shadow: 0 3px 0 #2980b9;
}

.btn-3d-info:active {
    box-shadow: 0 1px 0 #2980b9;
}

.btn-3d:hover {
    opacity: 0.9;
    color: white;
}

.btn-change-router {
    padding: 6px 8px;
    font-size: 12px;
    letter-spacing: -0.2px;
}
</style>

<div class="row">
    <div class="col-sm-4 col-md-4">
        <div class="box box-{if $d['status']=='Active'}primary{else}danger{/if}">
            <div class="box-body box-profile">
                <div class="box-tools pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-3d btn-3d-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-gear"></i> {Lang::T('Actions')} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{$_url}customers/sync/{$d['id']}&token={$csrf_token}" onclick="return ask(this, 'This will sync Customer to Mikrotik?')"><i class="fa fa-refresh"></i> {Lang::T('Sync')}</a></li>
                            <li><a href="{$_url}customers/reconnect/{$d['id']}&token={$csrf_token}" onclick="return ask(this, 'This will disconnect and reconnect the customer. Continue?')"><i class="fa fa-power-off"></i> {Lang::T('Reconnect')}</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="{$_url}customers/enable/{$d['id']}&token={$csrf_token}" onclick="return ask(this, 'This will enable the customer on Mikrotik router. Continue?')"><i class="fa fa-play"></i> {Lang::T('Enable Customer')}</a></li>
                            <li><a href="{$_url}customers/disable/{$d['id']}&token={$csrf_token}" onclick="return ask(this, 'This will disable the customer on Mikrotik router and disconnect them. Continue?')"><i class="fa fa-stop"></i> {Lang::T('Disable Customer')}</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="{$_url}message/send/{$d['id']}&token={$csrf_token}"><i class="fa fa-envelope"></i> {Lang::T('Send Message')}</a></li>
                            <li><a href="{$_url}customers/login/{$d['id']}&token={$csrf_token}" target="_blank"><i class="fa fa-sign-in"></i> {Lang::T('Login as Customer')}</a></li>
                        </ul>
                    </div>
                </div>
                <img class="profile-user-img img-responsive img-circle"
                    onclick="window.location.href = '{$UPLOAD_PATH}{$d['photo']}'"
                    src="{$UPLOAD_PATH}{$d['photo']}.thumb.jpg"
                    onerror="this.src='{$UPLOAD_PATH}/user.default.jpg'" alt="avatar">
                <h3 class="profile-username text-center">{$d['fullname']}</h3>
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>{Lang::T('Status')}</b> <span
                            class="pull-right {if $d['status'] !='Active'}bg-red{/if}">&nbsp;{Lang::T($d['status'])}&nbsp;</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Username')}</b> <span class="pull-right">{$d['username']}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Phone Number')}</b> <span class="pull-right">{$d['phonenumber']}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Email')}</b> <span class="pull-right">{$d['email']}</span>
                    </li>
                    <li class="list-group-item">{Lang::nl2br($d['address'])}</li>
                    <li class="list-group-item">
                        <b>{Lang::T('City')}</b> <span class="pull-right">{$d['city']}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('District')}</b> <span class="pull-right">{$d['district']}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('State')}</b> <span class="pull-right">{$d['state']}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Zip')}</b> <span class="pull-right">{$d['zip']}</span>
                    </li>
                    {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                        <li class="list-group-item">
                            <b>{Lang::T('Password')}</b> <input type="password" value="{$d['password']}"
                                style=" border: 0px; text-align: right;" class="pull-right"
                                onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'"
                                onclick="this.select()">
                        </li>
                    {/if}
                    {if $d['pppoe_username'] != ''}
                        <li class="list-group-item">
                            <b>PPPOE {Lang::T('Username')}</b> <span class="pull-right">{$d['pppoe_username']}</span>
                        </li>
                    {/if}
                    {if $d['pppoe_password'] != '' && in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                        <li class="list-group-item">
                            <b>PPPOE {Lang::T('Password')}</b> <input type="password" value="{$d['pppoe_password']}"
                                style=" border: 0px; text-align: right;" class="pull-right"
                                onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'"
                                onclick="this.select()">
                        </li>
                    {/if}
                    {if $d['pppoe_ip'] != ''}
                        <li class="list-group-item">
                            <b>PPPOE Remote IP</b> <span class="pull-right">{$d['pppoe_ip']}</span>
                        </li>
                    {/if}
                    <!--Customers Attributes view start -->
                    {if $customFields}
                        {foreach $customFields as $customField}
                            <li class="list-group-item">
                                <b>{$customField.field_name}</b> <span class="pull-right">
                                    {if strpos($customField.field_value, ':0') === false}
                                        {$customField.field_value}
                                    {else}
                                        <b>{Lang::T('Paid')}</b>
                                    {/if}
                                </span>
                            </li>
                        {/foreach}
                    {/if}
                    <!--Customers Attributes view end -->
                    <li class="list-group-item">
                        <b>{Lang::T('Service Type')}</b> <span class="pull-right">{Lang::T($d['service_type'])}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Account Type')}</b> <span class="pull-right">{Lang::T($d['account_type'])}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Balance')}</b> <span class="pull-right">{Lang::moneyFormat($d['balance'])}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Auto Renewal')}</b> <span class="pull-right">{if
                            $d['auto_renewal']}yes{else}no
                            {/if}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Created On')}</b> <span
                            class="pull-right">{Lang::dateTimeFormat($d['created_at'])}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Last Login')}</b> <span
                            class="pull-right">{Lang::dateTimeFormat($d['last_login'])}</span>
                    </li>
                    {if $d['coordinates']}
                        <li class="list-group-item">
                            <b>{Lang::T('Coordinates')}</b> <span class="pull-right">
                                <i class="glyphicon glyphicon-road"></i> <a style="color: black;"
                                    href="https://www.google.com/maps/dir//{$d['coordinates']}/"
                                    target="_blank">{Lang::T('Get Directions')}</a>
                            </span>
                        </li>
                    {/if}
                </ul>
                <div class="row">
                    <div class="col-xs-4">
                        <button type="button" onclick="confirmDelete('{$_url}customers/delete/{$d['id']}&token={$csrf_token}')" 
                            class="btn btn-3d btn-3d-danger btn-sm btn-block"><i class="fa fa-trash"></i> {Lang::T('Delete')}</button>
                    </div>
                    <div class="col-xs-4">
                        <a href="{$_url}customers/edit/{$d['id']}&token={$csrf_token}"
                            class="btn btn-3d btn-3d-primary btn-sm btn-block"><i class="fa fa-pencil"></i> {Lang::T('Edit')}</a>
                    </div>
                    <div class="col-xs-4">
                        <a href="{$_url}customers/change_router/{$d['id']}&token={$csrf_token}" 
                            class="btn btn-3d btn-3d-info btn-sm btn-block btn-change-router">
                            <i class="fa fa-random"></i> {Lang::T('Change Router')}
                        </a>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-xs-12">
                        <a href="{$_url}customers/list" class="btn btn-3d btn-3d btn-default btn-sm btn-block"><i class="fa fa-arrow-left"></i> {Lang::T('Back')}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-8 col-md-8">
        <div class="box box-success">
            <div class="box-body">
                <div class="row">
                    {if $_c['enable_balance'] == 'yes' && $_c['extend_expired']}
                    <div class="col-xs-6 col-sm-3" style="margin-bottom:6px">
                        <a href="{$_url}plan/recharge/{$d['id']}" class="btn btn-3d btn-3d-success btn-block">
                            <i class="fa fa-credit-card"></i> {Lang::T('Recharge Account')}
                        </a>
                    </div>
                    <div class="col-xs-6 col-sm-3" style="margin-bottom:6px">
                        <a href="{$_url}plan/deposit/{$d['id']}" class="btn btn-3d btn-3d-primary btn-block">
                            <i class="fa fa-money"></i> {Lang::T('Add Balance')}
                        </a>
                    </div>
                    {if $_admin['user_type'] == 'SuperAdmin' || $_admin['user_type'] == 'Admin'}
                    <div class="col-xs-6 col-sm-3" style="margin-bottom:6px">
                        <a href="{$_url}plan/deduct/{$d['id']}" class="btn btn-3d btn-3d-danger btn-block">
                            <i class="fa fa-minus-circle"></i> {Lang::T('Subtract Balance')}
                        </a>
                    </div>
                    {/if}
                    <div class="col-xs-6 col-sm-3" style="margin-bottom:6px">
                        <button onclick="extendCustomerPlan('{$d['id']}')" class="btn btn-3d btn-3d-warning btn-block">
                            <i class="fa fa-clock-o"></i> {Lang::T('Extend')}
                        </button>
                    </div>
                    {elseif $_c['enable_balance'] == 'yes'}
                    <div class="col-xs-6 col-sm-4" style="margin-bottom:6px">
                        <a href="{$_url}plan/recharge/{$d['id']}" class="btn btn-3d btn-3d-success btn-block">
                            <i class="fa fa-credit-card"></i> {Lang::T('Recharge Account')}
                        </a>
                    </div>
                    <div class="col-xs-6 col-sm-4" style="margin-bottom:6px">
                        <a href="{$_url}plan/deposit/{$d['id']}" class="btn btn-3d btn-3d-primary btn-block">
                            <i class="fa fa-money"></i> {Lang::T('Add Balance')}
                        </a>
                    </div>
                    {if $_admin['user_type'] == 'SuperAdmin' || $_admin['user_type'] == 'Admin'}
                    <div class="col-xs-12 col-sm-4" style="margin-bottom:6px">
                        <a href="{$_url}plan/deduct/{$d['id']}" class="btn btn-3d btn-3d-danger btn-block">
                            <i class="fa fa-minus-circle"></i> {Lang::T('Subtract Balance')}
                        </a>
                    </div>
                    {/if}
                    {elseif $_c['extend_expired']}
                    <div class="col-xs-6" style="margin-bottom:6px">
                        <a href="{$_url}plan/recharge/{$d['id']}" class="btn btn-3d btn-3d-success btn-block">
                            <i class="fa fa-credit-card"></i> {Lang::T('Recharge Account')}
                        </a>
                    </div>
                    <div class="col-xs-6" style="margin-bottom:6px">
                        <button onclick="extendCustomerPlan('{$d['id']}')" class="btn btn-3d btn-3d-warning btn-block">
                            <i class="fa fa-clock-o"></i> {Lang::T('Extend')}
                        </button>
                    </div>
                    {else}
                    <div class="col-xs-12">
                        <a href="{$_url}plan/recharge/{$d['id']}" class="btn btn-3d btn-3d-success btn-block">
                            <i class="fa fa-credit-card"></i> {Lang::T('Recharge Account')}
                        </a>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
        <div class="box box-info">
            <ul class="nav nav-tabs">
                <li role="presentation" {if $v=='order' }class="active" {/if}><a
                        href="{$_url}customers/view/{$d['id']}/order">{Lang::T('Order History')}</a></li>
                <li role="presentation" {if $v=='activation' }class="active" {/if}><a
                        href="{$_url}customers/view/{$d['id']}/activation">{Lang::T('Activation History')}</a></li>
                <li role="presentation" {if $v=='tickets' }class="active" {/if}><a
                        href="{$_url}customers/view/{$d['id']}/tickets"><i class="fa fa-ticket"></i> Support Tickets</a></li>
                <li role="presentation" {if $v=='smslogs' }class="active" {/if}><a
                        href="{$_url}customers/view/{$d['id']}/smslogs"><i class="fa fa-comments"></i> SMS Logs</a></li>
                <li role="presentation" {if $v=='mklogs' }class="active" {/if}><a
                        href="{$_url}customers/view/{$d['id']}/mklogs"><i class="fa fa-terminal"></i> MT Logs</a></li>
            </ul>
            <div class="box-body" style="padding:0;">
            {if $v=='activation'}
            <div class="table-responsive">
                <table class="table table-bordered table-striped" style="white-space:nowrap;">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>{Lang::T('Plan Name')}</th>
                            <th>{Lang::T('Plan Price')}</th>
                            <th>{Lang::T('Type')}</th>
                            <th>{Lang::T('Created On')}</th>
                            <th>{Lang::T('Expires On')}</th>
                            <th>{Lang::T('Method')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if Lang::arrayCount($activation)}
                            {foreach $activation as $ds}
                                <tr onclick="window.location.href = '{$_url}plan/view/{$ds['id']}'" style="cursor:pointer;">
                                    <td>{if $ds['invoice']}{$ds['invoice']}{else}#{$ds['id']}{/if}</td>
                                    <td>{$ds['plan_name']}</td>
                                    <td>{Lang::moneyFormat($ds['price'])}</td>
                                    <td>{$ds['type']}</td>
                                    <td class="text-success">{Lang::dateAndTimeFormat($ds['recharged_on'],$ds['recharged_time'])}</td>
                                    <td class="text-danger">{Lang::dateAndTimeFormat($ds['expiration'],$ds['time'])}</td>
                                    <td>{$ds['method']}</td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr><td colspan="7" class="text-center text-muted" style="padding:20px;">{Lang::T('No activation records found.')}</td></tr>
                        {/if}
                    </tbody>
                </table>
            </div>

            {elseif $v=='order'}
            <div class="table-responsive">
                <table class="table table-bordered table-striped" style="white-space:nowrap;">
                    <thead>
                        <tr>
                            <th>{Lang::T('Plan Name')}</th>
                            <th>{Lang::T('Gateway')}</th>
                            <th>{Lang::T('Routers')}</th>
                            <th>{Lang::T('Type')}</th>
                            <th>{Lang::T('Plan Price')}</th>
                            <th>{Lang::T('Created On')}</th>
                            <th>{Lang::T('Expires On')}</th>
                            <th>{Lang::T('Date Done')}</th>
                            <th>{Lang::T('Method')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if Lang::arrayCount($order)}
                            {foreach $order as $ds}
                                <tr>
                                    <td>{$ds['plan_name']}</td>
                                    <td>{$ds['gateway']}</td>
                                    <td>{$ds['routers']}</td>
                                    <td>{$ds['payment_channel']}</td>
                                    <td>{Lang::moneyFormat($ds['price'])}</td>
                                    <td class="text-primary">{Lang::dateTimeFormat($ds['created_date'])}</td>
                                    <td class="text-danger">{Lang::dateTimeFormat($ds['expired_date'])}</td>
                                    <td class="text-success">{if $ds['status']!=1}{Lang::dateTimeFormat($ds['paid_date'])}{/if}</td>
                                    <td>{if $ds['status']==1}{Lang::T('UNPAID')}
                                        {elseif $ds['status']==2}{Lang::T('PAID')}
                                        {elseif $ds['status']==3}{$_L['FAILED']}
                                        {elseif $ds['status']==4}{Lang::T('CANCELED')}
                                        {elseif $ds['status']==5}{Lang::T('UNKNOWN')}
                                        {/if}</td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr><td colspan="9" class="text-center text-muted" style="padding:20px;">{Lang::T('No order records found.')}</td></tr>
                        {/if}
                    </tbody>
                </table>
            </div>

            {elseif $v=='tickets'}
            <div class="table-responsive">
                <table class="table table-bordered table-striped tab-history-table">
                    <thead>
                        <tr>
                            <th>Ticket #</th>
                            <th>Subject</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Last Update</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if $tickets|@count > 0}
                            {foreach $tickets as $ticket}
                                <tr>
                                    <td><strong>{$ticket.ticket_number}</strong></td>
                                    <td>{$ticket.subject}</td>
                                    <td>{if $ticket.category_name}<span class="badge badge-info">{$ticket.category_name}</span>{else}-{/if}</td>
                                    <td>
                                        {if $ticket.priority == 'Urgent'}<span class="label label-danger">Urgent</span>
                                        {elseif $ticket.priority == 'High'}<span class="label label-warning">High</span>
                                        {elseif $ticket.priority == 'Normal'}<span class="label label-info">Normal</span>
                                        {else}<span class="label label-default">Low</span>{/if}
                                    </td>
                                    <td>
                                        {if $ticket.status == 'Closed'}<span class="label label-success">Closed</span>
                                        {elseif $ticket.status == 'In Progress'}<span class="label label-primary">In Progress</span>
                                        {elseif $ticket.status == 'Pending'}<span class="label label-warning">Pending</span>
                                        {else}<span class="label label-default">Open</span>{/if}
                                    </td>
                                    <td>{date('M d, Y', strtotime($ticket.created_at))}</td>
                                    <td>{date('M d, Y H:i', strtotime($ticket.updated_at))}</td>
                                    <td>
                                        <a href="{$_url}plugin/support_tickets&action=view&id={$ticket.id}" class="btn btn-info btn-xs">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="8" class="text-center" style="padding:20px;">
                                    <p class="text-muted">No support tickets found for this customer.</p>
                                    <a href="{$_url}plugin/support_tickets&action=add" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus"></i> Create New Ticket
                                    </a>
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>

            {elseif $v=='smslogs'}
            <div class="table-responsive">
                <table class="table table-bordered table-striped tab-history-table">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>Phone</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Message ID</th>
                            <th>Error Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if $smslogs|@count > 0}
                            {foreach $smslogs as $sms}
                                <tr>
                                    <td>{date('Y-m-d H:i:s', strtotime($sms.created_at))}</td>
                                    <td>{$sms.phone}</td>
                                    <td style="max-width: 300px; word-wrap: break-word; white-space: normal;">{$sms.message}</td>
                                    <td>
                                        {if strtolower($sms.status) == 'sent' || strtolower($sms.status) == 'success'}<span class="label label-success">Sent</span>
                                        {elseif strtolower($sms.status) == 'failed' || strtolower($sms.status) == 'error'}<span class="label label-danger">Failed</span>
                                        {else}<span class="label label-info">{$sms.status}</span>{/if}
                                    </td>
                                    <td><small class="text-muted">{if $sms.message_id}{$sms.message_id}{else}N/A{/if}</small></td>
                                    <td><small class="text-muted" style="color:{if strtolower($sms.status)=='failed'}#c0392b{else}#27ae60{/if};">{if $sms.status_message}{$sms.status_message}{else}-{/if}</small></td>
                                    <td>
                                        <a href="{$_url}customers/resend_sms/{$sms.id}/{$d['id']}&token={$csrf_token}"
                                           class="btn btn-xs btn-default"
                                           onclick="return confirm('Resend this SMS to {$sms.phone}?')">
                                            <i class="fa fa-send"></i> Resend
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 30px;">
                                    <i class="fa fa-comments-o fa-3x text-muted" style="opacity: 0.3;"></i>
                                    <p class="text-muted" style="margin-top: 10px;">No SMS logs found for this customer.</p>
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>

            {elseif $v=='mklogs'}
            <div class="table-responsive">
                <table class="table table-bordered table-striped tab-history-table">
                    <thead>
                        <tr>
                            <th style="width:15%">Time</th>
                            <th style="width:20%">Topics</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody id="mklogs-tbody">
                        <tr>
                            <td colspan="3" class="text-center" style="padding:24px;">
                                <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                                <p class="text-muted" style="margin-top:8px;">Loading MikroTik logs&hellip;</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {/if}
            {include file="pagination.tpl"}
            </div>
        </div>
        <div class="row">
            {foreach $packages as $package}
                <div class="col-md-6">
                    <div class="box box-{if $package['status']=='on'}success{else}danger{/if}" data-package-id="{$package['id']}">
                        <div class="box-body box-profile">
                            <h4 class="text-center">{$package['type']} - {$package['namebp']} <span
                                    api-get-text="{$_url}autoload/customer_is_active/{$package['username']}/{$package['plan_id']}"></span>
                                <small class="text-muted" style="font-size:13px;font-weight:normal;"> &nbsp;|&nbsp; {Lang::moneyFormat($package['price'])}</small>
                            </h4>
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    {Lang::T('Active')} <span class="pull-right">{if
                        $package['status']=='on'}yes{else}no
                                    {/if}</span>
                                </li>
                                <li class="list-group-item">
                                    {Lang::T('Type')} <span class="pull-right">
                                        {if $package['prepaid'] eq yes}Prepaid{else}<b>Postpaid</b>{/if}</span>
                                </li>
                                <li class="list-group-item">
                                    {Lang::T('Bandwidth')} <span class="pull-right">
                                        {$package['name_bw']}</span>
                                </li>
                                <li class="list-group-item">
                                    {Lang::T('Created On')} <span
                                        class="pull-right">{Lang::dateAndTimeFormat($package['recharged_on'],$package['recharged_time'])}</span>
                                </li>
                                <li class="list-group-item">
                                    {Lang::T('Expires On')} <span class="pull-right">{Lang::dateAndTimeFormat($package['expiration'],
                        $package['time'])}</span>
                                </li>
                                <li class="list-group-item">
                                    {$package['routers']} <span class="pull-right">{$package['method']}</span>
                                </li>
                            </ul>
                            <div class="row" style="margin-bottom: 10px;">
                                {if $_c['extend_expired']}
                                <div class="col-xs-4">
                                    <a href="{$_url}customers/deactivate/{$d['id']}/{$package['plan_id']}&token={$csrf_token}" id="{$d['id']}"
                                        class="btn btn-3d btn-3d-danger btn-sm btn-block"
                                        onclick="return ask(this, 'This will deactivate Customer Plan, and make it expired')">{Lang::T('Deactivate')}</a>
                                </div>
                                <div class="col-xs-4">
                                    <a href="{$_url}plan/edit/{$package['id']}&token={$csrf_token}"
                                        class="btn btn-3d btn-3d-primary btn-sm btn-block">{Lang::T('Edit Plan')}</a>
                                </div>
                                <div class="col-xs-4">
                                    <a href="{$_url}customers/recharge/{$d['id']}/{$package['plan_id']}&token={$csrf_token}"
                                        class="btn btn-3d btn-3d-success btn-sm btn-block">{Lang::T('Recharge')}</a>
                                </div>
                                <div class="col-xs-6" style="margin-top: 5px;">
                                    <button onclick="extendPackage('{$package['id']}')" class="btn btn-3d btn-3d-warning btn-sm btn-block">
                                        <i class="fa fa-clock-o"></i> {Lang::T('Extend')}
                                    </button>
                                </div>
                                <div class="col-xs-6" style="margin-top: 5px;">
                                    <a href="{$_url}customers/delete_package/{$d['id']}/{$package['id']}&token={$csrf_token}" id="{$d['id']}"
                                        class="btn btn-3d btn-3d-danger btn-sm btn-block"
                                        onclick="return ask(this, 'This will permanently delete this package. Are you sure?')"><i class="fa fa-trash"></i> Delete</a>
                                </div>
                                {else}
                                <div class="col-xs-4">
                                    <a href="{$_url}customers/deactivate/{$d['id']}/{$package['plan_id']}&token={$csrf_token}" id="{$d['id']}"
                                        class="btn btn-3d btn-3d-danger btn-sm btn-block"
                                        onclick="return ask(this, 'This will deactivate Customer Plan, and make it expired')">{Lang::T('Deactivate')}</a>
                                </div>
                                <div class="col-xs-4">
                                    <a href="{$_url}plan/edit/{$package['id']}&token={$csrf_token}"
                                        class="btn btn-3d btn-3d-primary btn-sm btn-block">{Lang::T('Edit Plan')}</a>
                                </div>
                                <div class="col-xs-4">
                                    <a href="{$_url}customers/recharge/{$d['id']}/{$package['plan_id']}&token={$csrf_token}"
                                        class="btn btn-3d btn-3d-success btn-sm btn-block">{Lang::T('Recharge')}</a>
                                </div>
                                <div class="col-xs-6" style="margin-top: 5px;">
                                    <button onclick="extendPackage('{$package['id']}')" class="btn btn-3d btn-3d-warning btn-sm btn-block">
                                        <i class="fa fa-clock-o"></i> {Lang::T('Extend')}
                                    </button>
                                </div>
                                <div class="col-xs-6" style="margin-top: 5px;">
                                    <a href="{$_url}customers/delete_package/{$d['id']}/{$package['id']}&token={$csrf_token}" id="{$d['id']}"
                                        class="btn btn-3d btn-3d-danger btn-sm btn-block"
                                        onclick="return ask(this, 'This will permanently delete this package. Are you sure?')"><i class="fa fa-trash"></i> Delete</a>
                                </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>

{if isset($devices) && count($devices) > 0}
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:8px; padding-bottom:10px;">
                <h3 class="box-title" style="margin:0;"><i class="fa fa-wifi"></i> {Lang::T('Connected Devices')} <span data-toggle="tooltip" title="Total Connected Devices" class="badge bg-blue">{count($devices)}</span></h3>
                <div style="display:flex; flex-wrap:wrap; gap:4px; align-items:center;">
                    {if isset($customer_router_state) && $customer_router_state == 'on'}
                    <a href="{$_url}customers/disable/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will disable the customer on Mikrotik router and disconnect them. Continue?')"
                       class="btn btn-3d btn-sm"
                       style="background:linear-gradient(to bottom,#27ae60,#1e8449);color:#fff;box-shadow:0 3px 0 #145a32;min-width:96px;">
                        <i class="fa fa-toggle-on"></i> {Lang::T('ON')} &mdash; {Lang::T('Turn Off')}
                    </a>
                    {elseif isset($customer_router_state) && $customer_router_state == 'off'}
                    <a href="{$_url}customers/enable/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will enable the customer on Mikrotik router. Continue?')"
                       class="btn btn-3d btn-sm"
                       style="background:linear-gradient(to bottom,#95a5a6,#7f8c8d);color:#fff;box-shadow:0 3px 0 #566573;min-width:96px;">
                        <i class="fa fa-toggle-off"></i> {Lang::T('OFF')} &mdash; {Lang::T('Turn On')}
                    </a>
                    {else}
                    <a href="{$_url}customers/enable/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will enable the customer on Mikrotik router. Continue?')"
                       class="btn btn-3d btn-3d-success btn-sm">
                        <i class="fa fa-play"></i> {Lang::T('Enable')}
                    </a>
                    <a href="{$_url}customers/disable/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will disable the customer on Mikrotik router and disconnect them. Continue?')"
                       class="btn btn-3d btn-3d-danger btn-sm">
                        <i class="fa fa-stop"></i> {Lang::T('Disable')}
                    </a>
                    {/if}
                    <a href="{$_url}customers/reconnect/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will disconnect and reconnect the customer. Continue?')"
                       class="btn btn-3d btn-3d-warning btn-sm">
                        <i class="fa fa-refresh"></i> {Lang::T('Reconnect')}
                    </a>
                </div>
            </div>
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10%">{Lang::T('Type')}</th>
                                <th style="width: 15%">{Lang::T('MAC Address')}</th>
                                <th style="width: 12%">{Lang::T('IP Address')}</th>
                                <th style="width: 18%">{Lang::T('Host Name')}</th>
                                <th style="width: 12%">{Lang::T('Download')}</th>
                                <th style="width: 12%">{Lang::T('Upload')}</th>
                                <th style="width: 13%">{Lang::T('Total Usage')}</th>
                                <th style="width: 8%">{Lang::T('Uptime')}</th>
                                <th style="width: 10%">{Lang::T('Status')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $devices as $device}
                                <tr>
                                    <td>
                                        {if $device['type'] eq 'Hotspot'}
                                            <span class="label label-primary" style="font-size: 12px;">
                                                <i class="fa fa-dot-circle-o"></i> {$device['type']}
                                            </span>
                                        {else}
                                            <span class="label label-info" style="font-size: 12px;">
                                                <i class="fa fa-plug"></i> {$device['type']}
                                            </span>
                                        {/if}
                                    </td>
                                    <td>
                                        <code style="background: #f8f9fa; padding: 5px 8px; border-radius: 4px; color: #495057; font-size: 13px;">
                                            <i class="fa fa-microchip"></i> {$device['mac_address']}
                                        </code>
                                    </td>
                                    <td>
                                        <code style="background: #f8f9fa; padding: 5px 8px; border-radius: 4px; color: #495057; font-size: 13px;">
                                            <i class="fa fa-globe"></i> {$device['ip_address']}
                                        </code>
                                    </td>
                                    <td>
                                        <code style="background: #f8f9fa; padding: 5px 8px; border-radius: 4px; color: #495057; font-size: 13px;">
                                            <i class="fa fa-desktop"></i> {$device['hostname']}
                                        </code>
                                    </td>
                                    <td>
                                        <span class="label label-success" style="font-size: 11px;">
                                            <i class="fa fa-download"></i> 
                                            <span class="data-usage" data-bytes="{$device['bytes_in']}">Loading...</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="label label-info" style="font-size: 11px;">
                                            <i class="fa fa-upload"></i> 
                                            <span class="data-usage" data-bytes="{$device['bytes_out']}">Loading...</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="label label-warning" style="font-size: 11px;">
                                            <i class="fa fa-exchange"></i> 
                                            <span class="total-usage" data-in="{$device['bytes_in']}" data-out="{$device['bytes_out']}">Loading...</span>
                                        </span>
                                    </td>
                                    <td>
                                        <i class="fa fa-clock-o"></i> {$device['uptime']}
                                    </td>
                                    <td>
                                        <span class="label label-success">
                                            <i class="fa fa-check-circle"></i> Active
                                        </span>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box-footer text-center" style="background-color: #f8f9fa; border-top: 1px solid #e9ecef;">
                <small class="text-muted">
                    <i class="fa fa-info-circle"></i> Last Updated: {$smarty.now|date_format:"%H:%M:%S"}
                </small>
            </div>
        </div>
    </div>
</div>
{else}
<div class="row">
    <div class="col-sm-12">
        <div class="box box-info">
            <div class="box-header with-border" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:8px; padding-bottom:10px;">
                <h3 class="box-title" style="margin:0;"><i class="fa fa-wifi"></i> {Lang::T('Router Control')}</h3>
                <div style="display:flex; flex-wrap:wrap; gap:4px; align-items:center;">
                    {if isset($customer_router_state) && $customer_router_state == 'on'}
                    <a href="{$_url}customers/disable/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will disable the customer on Mikrotik router and disconnect them. Continue?')"
                       class="btn btn-3d btn-sm"
                       style="background:linear-gradient(to bottom,#27ae60,#1e8449);color:#fff;box-shadow:0 3px 0 #145a32;min-width:96px;">
                        <i class="fa fa-toggle-on"></i> {Lang::T('ON')} &mdash; {Lang::T('Turn Off')}
                    </a>
                    {elseif isset($customer_router_state) && $customer_router_state == 'off'}
                    <a href="{$_url}customers/enable/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will enable the customer on Mikrotik router. Continue?')"
                       class="btn btn-3d btn-sm"
                       style="background:linear-gradient(to bottom,#95a5a6,#7f8c8d);color:#fff;box-shadow:0 3px 0 #566573;min-width:96px;">
                        <i class="fa fa-toggle-off"></i> {Lang::T('OFF')} &mdash; {Lang::T('Turn On')}
                    </a>
                    {else}
                    <a href="{$_url}customers/enable/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will enable the customer on Mikrotik router. Continue?')"
                       class="btn btn-3d btn-3d-success btn-sm">
                        <i class="fa fa-play"></i> {Lang::T('Enable')}
                    </a>
                    <a href="{$_url}customers/disable/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will disable the customer on Mikrotik router and disconnect them. Continue?')"
                       class="btn btn-3d btn-3d-danger btn-sm">
                        <i class="fa fa-stop"></i> {Lang::T('Disable')}
                    </a>
                    {/if}
                    <a href="{$_url}customers/reconnect/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will disconnect and reconnect the customer. Continue?')"
                       class="btn btn-3d btn-3d-warning btn-sm">
                        <i class="fa fa-refresh"></i> {Lang::T('Reconnect')}
                    </a>
                </div>
            </div>
            <div class="box-body text-center" style="padding: 40px;">
                <i class="fa fa-wifi" style="font-size: 48px; color: #bbb; margin-bottom: 15px;"></i>
                <h4 style="color: #666;">{Lang::T('No Connected Devices')}</h4>
                <p class="text-muted">{Lang::T('This customer has no active connections at the moment.')}</p>
            </div>
        </div>
    </div>
</div>
{/if}

<hr>
<div class="row">
    <div class="col-xs-6 col-md-3">
        <a href="{$_url}customers/list" class="btn btn-3d btn-3d btn-default btn-sm btn-block">{Lang::T('Back')}</a>
    </div>
    <div class="col-xs-6 col-md-3">
        <a href="{$_url}customers/sync/{$d['id']}&token={$csrf_token}" onclick="return ask(this, 'This will sync Customer to Mikrotik?')"
            class="btn btn-3d btn-3d-info btn-sm btn-block">{Lang::T('Sync')}</a>
    </div>
    <div class="col-xs-6 col-md-3">
        <a href="{$_url}message/send/{$d['id']}&token={$csrf_token}" class="btn btn-3d btn-3d-success btn-sm btn-block">
            {Lang::T('Send Message')}
        </a>
    </div>
    <div class="col-xs-6 col-md-3">
        <a href="{$_url}customers/login/{$d['id']}&token={$csrf_token}" target="_blank" class="btn btn-3d btn-3d-primary btn-sm btn-block">
            {Lang::T('Login as Customer')}
        </a>
    </div>
</div>

{if $d['coordinates']}
    {literal}
        <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
        <script>
            function setupMap(lat, lon) {
                var map = L.map('map').setView([lat, lon], 17);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/light_all/{z}/{x}/{y}.png', {
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    subdomains: 'abcd',
                    maxZoom: 20
            }).addTo(map);
            var marker = L.marker([lat, lon]).addTo(map);
            }
            window.onload = function() {
                {/literal}setupMap({$d['coordinates']});{literal}
            }
        </script>
    {/literal}
{/if}
{literal}
<script>
function confirmDelete(url) {
    if (confirm('{/literal}{Lang::T('Delete')}?{literal}')) {
        window.location.href = url;
    }
}

function extendCustomerPlan(customerId) {
    // Get customer's active packages to extend
    var packages = document.querySelectorAll('[data-package-id]');
    if (packages.length === 0) {
        alert('No active packages found for this customer');
        return;
    }
    
    // If only one package, extend it directly
    if (packages.length === 1) {
        var packageId = packages[0].getAttribute('data-package-id');
        extendPackage(packageId);
        return;
    }
    
    // If multiple packages, let user choose or extend the first active one
    var activePackages = [];
    for (var i = 0; i < packages.length; i++) {
        var packageBox = packages[i];
        if (packageBox.classList.contains('box-success')) {
            activePackages.push(packageBox.getAttribute('data-package-id'));
        }
    }
    
    if (activePackages.length > 0) {
        // Extend the first active package
        extendPackage(activePackages[0]);
    } else {
        // No active packages, extend the first one
        extendPackage(packages[0].getAttribute('data-package-id'));
    }
}

function extendPackage(packageId) {
    var days = prompt("Extend for how many days?", "3");
    if (days) {
        if (confirm("Extend for " + days + " days?")) {
            window.location.href = "{/literal}{$_url}plan/extend/{literal}" + packageId + "/" + days + "&stoken={/literal}{App::getToken()}{literal}";
        }
    }
}

// Format bytes into human readable format
function formatBytes(bytes, precision = 2) {
    const units = ['B', 'KB', 'MB', 'GB', 'TB'];
    let i = 0;
    
    for (i = 0; bytes > 1024 && i < units.length - 1; i++) {
        bytes /= 1024;
    }
    
    return bytes.toFixed(precision) + ' ' + units[i];
}

// Update data usage displays
function updateDataUsage() {
    // Update individual data usage
    document.querySelectorAll('.data-usage').forEach(function(element) {
        const bytes = parseInt(element.getAttribute('data-bytes'));
        if (!isNaN(bytes) && bytes > 0) {
            element.textContent = formatBytes(bytes);
        } else {
            element.textContent = '0 B';
        }
    });
    
    // Update total usage
    document.querySelectorAll('.total-usage').forEach(function(element) {
        const bytesIn = parseInt(element.getAttribute('data-in'));
        const bytesOut = parseInt(element.getAttribute('data-out'));
        if (!isNaN(bytesIn) && !isNaN(bytesOut)) {
            const total = bytesIn + bytesOut;
            element.textContent = formatBytes(total);
        } else {
            element.textContent = '0 B';
        }
    });
}

// Initialize data usage when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateDataUsage();
});

// Auto-refresh data usage every 30 seconds
setInterval(function() {
    // You could add AJAX call here to refresh device data
    // For now, just update the formatting
    updateDataUsage();
}, 30000);
</script>
{/literal}

{* ── Live Bandwidth Graph ──────────────────────────────────────────────── *}
<div class="row" style="margin-top:8px;">
    <div class="col-sm-12">
        <div class="box box-primary" id="live-graph-box">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-line-chart"></i> Live Bandwidth</h3>
                <div class="box-tools pull-right" style="display:flex;align-items:center;gap:8px;">
                    <span id="live-session-type" class="label label-default" style="font-size:11px;">Detecting&hellip;</span>
                    <span id="live-ip" class="text-muted" style="font-size:12px;"></span>
                    <span id="live-uptime" class="text-muted" style="font-size:12px;"></span>
                    <button id="live-toggle" class="btn btn-xs btn-default" title="Pause/Resume">
                        <i class="fa fa-pause" id="live-toggle-icon"></i>
                    </button>
                </div>
            </div>
            <div class="box-body" style="padding-bottom:6px;">
                <div class="row" style="margin-bottom:10px;">
                    <div class="col-xs-4">
                        <div style="background:#f0faf2;border-left:3px solid #27ae60;padding:8px 12px;border-radius:4px;">
                            <div style="font-size:10px;font-weight:600;color:#27ae60;text-transform:uppercase;letter-spacing:.5px;">Download</div>
                            <div id="live-dl-speed" style="font-size:20px;font-weight:700;color:#1e8449;">0 bps</div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div style="background:#f0f5ff;border-left:3px solid #2980b9;padding:8px 12px;border-radius:4px;">
                            <div style="font-size:10px;font-weight:600;color:#2980b9;text-transform:uppercase;letter-spacing:.5px;">Upload</div>
                            <div id="live-ul-speed" style="font-size:20px;font-weight:700;color:#1f618d;">0 bps</div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div style="background:#fff8f0;border-left:3px solid #e67e22;padding:8px 12px;border-radius:4px;">
                            <div style="font-size:10px;font-weight:600;color:#e67e22;text-transform:uppercase;letter-spacing:.5px;">Session Total DL</div>
                            <div id="live-total-dl" style="font-size:20px;font-weight:700;color:#ca6f1e;">0 B</div>
                        </div>
                    </div>
                </div>
                <div style="position:relative;height:180px;">
                    <canvas id="liveChart"></canvas>
                </div>
            </div>
            <div class="box-footer" style="padding:6px 12px;background:#f8f9fa;">
                <small class="text-muted"><i class="fa fa-refresh"></i> Updates every 3 seconds &nbsp;|&nbsp; <span id="live-status-text">Starting&hellip;</span></small>
            </div>
        </div>
    </div>
</div>
{* ── End Live Bandwidth Graph ────────────────────────────────────────────── *}

{* ── Monthly Data Usage ─────────────────────────────────────────────────── *}
<style>
.usage-card {
    border-radius: 8px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}
.usage-card .usage-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.usage-card .usage-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
}
.usage-card .usage-value {
    font-size: 18px;
    font-weight: 700;
    line-height: 1;
}
.usage-card-dl  { background:#f0faf2; border-left: 3px solid #27ae60; }
.usage-card-ul  { background:#f0f5ff; border-left: 3px solid #2980b9; }
.usage-card-tot { background:#fff8f0; border-left: 3px solid #e67e22; }
.usage-card-dl  .usage-icon { background:#27ae60; color:#fff; }
.usage-card-ul  .usage-icon { background:#2980b9; color:#fff; }
.usage-card-tot .usage-icon { background:#e67e22; color:#fff; }
.usage-card-dl  .usage-label { color:#27ae60; }
.usage-card-ul  .usage-label { color:#2980b9; }
.usage-card-tot .usage-label { color:#e67e22; }
.usage-card-dl  .usage-value { color:#1e8449; }
.usage-card-ul  .usage-value { color:#1f618d; }
.usage-card-tot .usage-value { color:#ca6f1e; }
.usage-history-table thead th {
    background: #f8f9fa;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #555;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding: 8px 12px;
    white-space: nowrap;
}
.usage-history-table tbody td {
    font-size: 13px;
    padding: 8px 12px;
    vertical-align: middle;
}
.usage-history-table tbody tr.current-month {
    background: #fffbea !important;
}
.usage-badge-current {
    display: inline-block;
    background: #f39c12;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 10px;
    letter-spacing: 0.3px;
    vertical-align: middle;
    margin-right: 4px;
}
.usage-section-header {
    background: #2c3e50;
    border-radius: 8px 8px 0 0;
    padding: 11px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.usage-section-header .title {
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    margin: 0;
}
.usage-section-header .subtitle {
    color: #aab7c4;
    font-size: 11px;
}
</style>

<div class="row" style="margin-top:8px;">
    <div class="col-sm-12">
        <div class="box box-default" style="border-radius:8px; overflow:hidden; border:none; box-shadow:0 1px 4px rgba(0,0,0,0.1);">
            <div class="usage-section-header">
                <span class="title"><i class="fa fa-bar-chart" style="margin-right:6px;"></i>Monthly Data Usage</span>
                <span class="subtitle"><i class="fa fa-refresh" style="margin-right:4px;"></i>Resets on the 1st of every month</span>
            </div>
            <div class="box-body" style="padding:14px 14px 6px;">

                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <div class="usage-card usage-card-dl">
                            <div class="usage-icon"><i class="fa fa-download"></i></div>
                            <div>
                                <div class="usage-label">Downloaded</div>
                                <div class="usage-value">
                                    {if $monthly_usage_current}{$monthly_usage_current['download_fmt']}{else}0 B{/if}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="usage-card usage-card-ul">
                            <div class="usage-icon"><i class="fa fa-upload"></i></div>
                            <div>
                                <div class="usage-label">Uploaded</div>
                                <div class="usage-value">
                                    {if $monthly_usage_current}{$monthly_usage_current['upload_fmt']}{else}0 B{/if}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="usage-card usage-card-tot">
                            <div class="usage-icon"><i class="fa fa-exchange"></i></div>
                            <div>
                                <div class="usage-label">Total This Month</div>
                                <div class="usage-value">
                                    {if $monthly_usage_current}{$monthly_usage_current['total_fmt']}{else}0 B{/if}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {if $monthly_usage_history|@count > 0}
                    <div class="table-responsive" style="margin-top:6px;">
                        <table class="table table-hover usage-history-table" style="margin-bottom:0;">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th><i class="fa fa-download" style="color:#27ae60;"></i> Downloaded</th>
                                    <th><i class="fa fa-upload" style="color:#2980b9;"></i> Uploaded</th>
                                    <th><i class="fa fa-exchange" style="color:#e67e22;"></i> Total</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach $monthly_usage_history as $mu}
                                    <tr {if $mu['is_current']}class="current-month"{/if}>
                                        <td>
                                            {if $mu['is_current']}<span class="usage-badge-current">NOW</span>{/if}
                                            {$mu['month_label']}
                                        </td>
                                        <td style="color:#1e8449; font-weight:600;">{$mu['download_fmt']}</td>
                                        <td style="color:#1f618d; font-weight:600;">{$mu['upload_fmt']}</td>
                                        <td style="color:#ca6f1e; font-weight:600;">{$mu['total_fmt']}</td>
                                        <td><small class="text-muted">{$mu['last_updated']}</small></td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                {else}
                    <div class="text-center" style="padding:24px 0 16px; color:#bbb;">
                        <i class="fa fa-bar-chart" style="font-size:28px; opacity:0.35;"></i>
                        <p style="margin:8px 0 0; font-size:13px; color:#999;">No data usage recorded yet.
                            <br><small>Updated automatically on each cron run.</small>
                        </p>
                    </div>
                {/if}

            </div>
        </div>
    </div>
</div>
{* ── End Monthly Data Usage ──────────────────────────────────────────────── *}

{* ── Live Graph + MT Logs JS ─────────────────────────────────────────────── *}
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
{literal}
<script>
(function () {
    'use strict';

    // ── helpers ──────────────────────────────────────────────────────────────
    function fmtSpeed(bytesPerSec) {
        var b = bytesPerSec * 8; // convert to bits/s
        if (b >= 1e9)  return (b / 1e9).toFixed(2) + ' Gbps';
        if (b >= 1e6)  return (b / 1e6).toFixed(2) + ' Mbps';
        if (b >= 1e3)  return (b / 1e3).toFixed(2) + ' Kbps';
        return b.toFixed(0) + ' bps';
    }
    function fmtBytes(bytes) {
        if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
        if (bytes >= 1048576)    return (bytes / 1048576).toFixed(2) + ' MB';
        if (bytes >= 1024)       return (bytes / 1024).toFixed(2) + ' KB';
        return bytes + ' B';
    }
    function nowLabel() {
        var d = new Date();
        return d.getHours().toString().padStart(2,'0') + ':' +
               d.getMinutes().toString().padStart(2,'0') + ':' +
               d.getSeconds().toString().padStart(2,'0');
    }

    // ── Chart setup ──────────────────────────────────────────────────────────
    var MAX_PTS = 60;
    var labels  = Array(MAX_PTS).fill('');
    var dlData  = Array(MAX_PTS).fill(0);
    var ulData  = Array(MAX_PTS).fill(0);

    var ctx = document.getElementById('liveChart');
    if (!ctx) return;

    var liveChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Download',
                    data: dlData,
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39,174,96,0.08)',
                    borderWidth: 2,
                    pointRadius: 0,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Upload',
                    data: ulData,
                    borderColor: '#2980b9',
                    backgroundColor: 'rgba(41,128,185,0.08)',
                    borderWidth: 2,
                    pointRadius: 0,
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 0 },
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } } },
            scales: {
                x: { ticks: { font: { size: 10 }, maxTicksLimit: 10 }, grid: { display: false } },
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: { size: 10 },
                        callback: function(v) {
                            if (v === 0) return '0';
                            return fmtSpeed(v);
                        }
                    },
                    title: { display: true, text: 'Speed', font: { size: 10 } }
                }
            }
        }
    });

    // ── Live polling ─────────────────────────────────────────────────────────
    var prevBytes    = null;
    var paused       = false;
    var pollInterval = null;
{/literal}
    var customerId   = '{$d['id']}';
    var apiBase      = '{$_url}customers/live_stats/';
{literal}

    document.getElementById('live-toggle').addEventListener('click', function () {
        paused = !paused;
        var icon = document.getElementById('live-toggle-icon');
        icon.className = paused ? 'fa fa-play' : 'fa fa-pause';
        document.getElementById('live-status-text').textContent = paused ? 'Paused' : 'Running…';
    });

    function pollLiveStats() {
        if (paused) return;
        fetch(apiBase + customerId)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var statusEl = document.getElementById('live-status-text');
                if (!data.success) {
                    statusEl.textContent = data.error || 'Error';
                    return;
                }

                // Session type badge
                var badge = document.getElementById('live-session-type');
                if (data.type === 'offline') {
                    badge.className = 'label label-danger';
                    badge.textContent = 'Offline';
                    statusEl.textContent = 'No active session';
                } else {
                    badge.className = 'label label-success';
                    badge.textContent = data.type;
                    statusEl.textContent = 'Live';
                }

                // IP + uptime
                document.getElementById('live-ip').textContent     = data.ip     ? ('IP: ' + data.ip)     : '';
                document.getElementById('live-uptime').textContent = data.uptime ? ('Up: ' + data.uptime) : '';

                // Session total download
                document.getElementById('live-total-dl').textContent = fmtBytes(data.bytes_in);

                // Compute speed from delta
                var dlSpeed = 0, ulSpeed = 0;
                if (prevBytes && data.type !== 'offline') {
                    var dt = data.timestamp - prevBytes.ts;
                    if (dt > 0) {
                        dlSpeed = Math.max(0, (data.bytes_in  - prevBytes.in)  / dt);
                        ulSpeed = Math.max(0, (data.bytes_out - prevBytes.out) / dt);
                    }
                }
                prevBytes = { in: data.bytes_in, out: data.bytes_out, ts: data.timestamp };

                // Update speed cards
                document.getElementById('live-dl-speed').textContent = fmtSpeed(dlSpeed);
                document.getElementById('live-ul-speed').textContent = fmtSpeed(ulSpeed);

                // Push to chart
                labels.push(nowLabel());   labels.shift();
                dlData.push(dlSpeed);      dlData.shift();
                ulData.push(ulSpeed);      ulData.shift();
                liveChart.update('none');
            })
            .catch(function() {
                document.getElementById('live-status-text').textContent = 'Connection error';
            });
    }

    // Start polling immediately then every 3 s
    pollLiveStats();
    pollInterval = setInterval(pollLiveStats, 3000);

    // ── MikroTik Logs (mklogs tab) ───────────────────────────────────────────
    var mklogsTbody = document.getElementById('mklogs-tbody');
    if (mklogsTbody) {
{/literal}
        var logsApiUrl = '{$_url}customers/mikrotik_logs/{$d['id']}';
{literal}
        // topic → { bg, border, icon, text }
        var TOPIC_STYLE = {
            'info':     { bg:'#e8f4fd', border:'#3498db', icon:'fa-info-circle',   text:'#1a6a9a' },
            'warning':  { bg:'#fff8e1', border:'#f39c12', icon:'fa-exclamation-triangle', text:'#9a6800' },
            'error':    { bg:'#fdecea', border:'#e74c3c', icon:'fa-times-circle',   text:'#a93226' },
            'critical': { bg:'#fdecea', border:'#c0392b', icon:'fa-bomb',           text:'#7b241c' },
            'debug':    { bg:'#f0f0f0', border:'#95a5a6', icon:'fa-bug',            text:'#555' },
            'firewall': { bg:'#fef9e7', border:'#e67e22', icon:'fa-shield',         text:'#9a4f00' },
            'ppp':      { bg:'#eaf7fb', border:'#16a085', icon:'fa-plug',           text:'#0e6655' },
            'hotspot':  { bg:'#eaf5ea', border:'#27ae60', icon:'fa-wifi',           text:'#1a7a40' },
            'dhcp':     { bg:'#f4ecf7', border:'#8e44ad', icon:'fa-sitemap',        text:'#6c3483' },
            'system':   { bg:'#eaf0fb', border:'#2980b9', icon:'fa-cogs',           text:'#1a5276' }
        };

        function getTopicStyle(topics) {
            var t = (topics || '').toLowerCase();
            for (var key in TOPIC_STYLE) {
                if (t.indexOf(key) !== -1) return TOPIC_STYLE[key];
            }
            return { bg:'#f8f9fa', border:'#bdc3c7', icon:'fa-list', text:'#555' };
        }

        fetch(logsApiUrl)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success || !data.logs || data.logs.length === 0) {
                    mklogsTbody.innerHTML =
                        '<tr><td colspan="3" class="text-center" style="padding:40px 20px;">' +
                        '<i class="fa fa-list-alt" style="font-size:36px;color:#ccc;display:block;margin-bottom:10px;"></i>' +
                        '<span style="color:#999;font-size:13px;">' + escHtml(data.error || 'No log entries found for this user.') + '</span>' +
                        '</td></tr>';
                    return;
                }
                var rows = '';
                data.logs.forEach(function(log, idx) {
                    var s = getTopicStyle(log.topics);
                    rows +=
                        '<tr style="background:' + s.bg + ';border-left:4px solid ' + s.border + ';">' +
                        '<td style="white-space:nowrap;font-size:11px;color:#666;vertical-align:middle;padding:8px 10px;">' +
                            '<i class="fa fa-clock-o" style="margin-right:3px;"></i>' + escHtml(log.time) +
                        '</td>' +
                        '<td style="vertical-align:middle;padding:8px 10px;">' +
                            '<span style="display:inline-flex;align-items:center;gap:4px;background:' + s.border + ';' +
                            'color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;letter-spacing:.3px;">' +
                            '<i class="fa ' + s.icon + '"></i>' +
                            escHtml(log.topics) +
                            '</span>' +
                        '</td>' +
                        '<td style="font-size:12px;color:' + s.text + ';word-break:break-all;vertical-align:middle;padding:8px 10px;font-weight:500;">' +
                            escHtml(log.message) +
                        '</td>' +
                        '</tr>';
                });
                mklogsTbody.innerHTML = rows;
            })
            .catch(function() {
                mklogsTbody.innerHTML =
                    '<tr><td colspan="3" class="text-center" style="padding:20px;color:#e74c3c;">' +
                    '<i class="fa fa-exclamation-circle"></i> Failed to load MikroTik logs.</td></tr>';
            });
    }

    function escHtml(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

})();
</script>
{/literal}
{* ── End Live Graph + MT Logs JS ─────────────────────────────────────────── *}

{include file="sections/footer.tpl"}