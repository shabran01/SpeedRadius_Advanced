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
                    <div class="col-xs-4">
                        <a href="{$_url}plan/recharge/{$d['id']}" class="btn btn-3d btn-3d-success btn-sm btn-block">
                            <i class="fa fa-credit-card"></i> {Lang::T('Recharge Account')}
                        </a>
                    </div>
                    <div class="col-xs-4">
                        <a href="{$_url}plan/deposit/{$d['id']}" class="btn btn-3d btn-3d-primary btn-sm btn-block">
                            <i class="fa fa-money"></i> {Lang::T('Add Balance')}
                        </a>
                    </div>
                    <div class="col-xs-4">
                        <button onclick="extendCustomerPlan('{$d['id']}')" class="btn btn-3d btn-3d-warning btn-sm btn-block">
                            <i class="fa fa-clock-o"></i> {Lang::T('Extend')}
                        </button>
                    </div>
                    {elseif $_c['enable_balance'] == 'yes'}
                    <div class="col-xs-6">
                        <a href="{$_url}plan/recharge/{$d['id']}" class="btn btn-3d btn-3d-success btn-sm btn-block">
                            <i class="fa fa-credit-card"></i> {Lang::T('Recharge Account')}
                        </a>
                    </div>
                    <div class="col-xs-6">
                        <a href="{$_url}plan/deposit/{$d['id']}" class="btn btn-3d btn-3d-primary btn-sm btn-block">
                            <i class="fa fa-money"></i> {Lang::T('Add Balance')}
                        </a>
                    </div>
                    {elseif $_c['extend_expired']}
                    <div class="col-xs-6">
                        <a href="{$_url}plan/recharge/{$d['id']}" class="btn btn-3d btn-3d-success btn-sm btn-block">
                            <i class="fa fa-credit-card"></i> {Lang::T('Recharge Account')}
                        </a>
                    </div>
                    <div class="col-xs-6">
                        <button onclick="extendCustomerPlan('{$d['id']}')" class="btn btn-3d btn-3d-warning btn-sm btn-block">
                            <i class="fa fa-clock-o"></i> {Lang::T('Extend')}
                        </button>
                    </div>
                    {else}
                    <div class="col-xs-12">
                        <a href="{$_url}plan/recharge/{$d['id']}" class="btn btn-3d btn-3d-success btn-sm btn-block">
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
                        href="{$_url}customers/view/{$d['id']}/order">30 {Lang::T('Order History')}</a></li>
                <li role="presentation" {if $v=='activation' }class="active" {/if}><a
                        href="{$_url}customers/view/{$d['id']}/activation">30 {Lang::T('Activation History')}</a></li>
            </ul>
            <div class="table-responsive" style="background-color: white;">
                <table id="datatable" class="table table-bordered table-striped">
                    {if Lang::arrayCount($activation)}
                        <thead>
                            <tr>
                                <th>{Lang::T('Invoice')}</th>
                                <th>{Lang::T('Username')}</th>
                                <th>{Lang::T('Plan Name')}</th>
                                <th>{Lang::T('Plan Price')}</th>
                                <th>{Lang::T('Type')}</th>
                                <th>{Lang::T('Created On')}</th>
                                <th>{Lang::T('Expires On')}</th>
                                <th>{Lang::T('Method')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $activation as $ds}
                                <tr onclick="window.location.href = '{$_url}plan/view/{$ds['id']}'" style="cursor:pointer;">
                                    <td>{$ds['invoice']}</td>
                                    <td>{$ds['username']}</td>
                                    <td>{$ds['plan_name']}</td>
                                    <td>{Lang::moneyFormat($ds['price'])}</td>
                                    <td>{$ds['type']}</td>
                                    <td class="text-success">
                                        {Lang::dateAndTimeFormat($ds['recharged_on'],$ds['recharged_time'])}
                                    </td>
                                    <td class="text-danger">{Lang::dateAndTimeFormat($ds['expiration'],$ds['time'])}</td>
                                    <td>{$ds['method']}</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    {/if}
                    {if Lang::arrayCount($order)}
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
                            {foreach $order as $ds}
                                <tr>
                                    <td>{$ds['plan_name']}</td>
                                    <td>{$ds['gateway']}</td>
                                    <td>{$ds['routers']}</td>
                                    <td>{$ds['payment_channel']}</td>
                                    <td>{Lang::moneyFormat($ds['price'])}</td>
                                    <td class="text-primary">{Lang::dateTimeFormat($ds['created_date'])}</td>
                                    <td class="text-danger">{Lang::dateTimeFormat($ds['expired_date'])}</td>
                                    <td class="text-success">{if $ds['status']!=1}{Lang::dateTimeFormat($ds['paid_date'])}{/if}
                                    </td>
                                    <td>{if $ds['status']==1}{Lang::T('UNPAID')}
                                        {elseif $ds['status']==2}{Lang::T('PAID')}
                                        {elseif $ds['status']==3}{$_L['FAILED']}
                                        {elseif $ds['status']==4}{Lang::T('CANCELED')}
                                        {elseif $ds['status']==5}{Lang::T('UNKNOWN')}
                                        {/if}</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    {/if}
                </table>
            </div>
            {include file="pagination.tpl"}
        </div>
        <div class="row">
            {foreach $packages as $package}
                <div class="col-md-6">
                    <div class="box box-{if $package['status']=='on'}success{else}danger{/if}" data-package-id="{$package['id']}">
                        <div class="box-body box-profile">
                            <h4 class="text-center">{$package['type']} - {$package['namebp']} <span
                                    api-get-text="{$_url}autoload/customer_is_active/{$package['username']}/{$package['plan_id']}"></span>
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
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-wifi"></i> {Lang::T('Connected Devices')}</h3>
                <div class="box-tools pull-right">
                    <a href="{$_url}customers/enable/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will enable the customer on Mikrotik router. Continue?')"
                       class="btn btn-3d btn-3d-success btn-sm" style="margin-right: 5px;">
                        <i class="fa fa-play"></i> {Lang::T('Enable')}
                    </a>
                    <a href="{$_url}customers/disable/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will disable the customer on Mikrotik router and disconnect them. Continue?')"
                       class="btn btn-3d btn-3d-danger btn-sm" style="margin-right: 5px;">
                        <i class="fa fa-stop"></i> {Lang::T('Disable')}
                    </a>
                    <a href="{$_url}customers/reconnect/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will disconnect and reconnect the customer. Continue?')"
                       class="btn btn-3d btn-3d-warning btn-sm" style="margin-right: 10px;">
                        <i class="fa fa-refresh"></i> {Lang::T('Reconnect')}
                    </a>
                    <span data-toggle="tooltip" title="Total Connected Devices" class="badge bg-blue">{count($devices)}</span>
                </div>
            </div>
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="width: 15%">{Lang::T('Type')}</th>
                                <th style="width: 30%">{Lang::T('MAC Address')}</th>
                                <th style="width: 25%">{Lang::T('IP Address')}</th>
                                <th style="width: 20%">{Lang::T('Uptime')}</th>
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
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-wifi"></i> {Lang::T('Router Control')}</h3>
                <div class="box-tools pull-right">
                    <a href="{$_url}customers/enable/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will enable the customer on Mikrotik router. Continue?')"
                       class="btn btn-3d btn-3d-success btn-sm" style="margin-right: 5px;">
                        <i class="fa fa-play"></i> {Lang::T('Enable')}
                    </a>
                    <a href="{$_url}customers/disable/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will disable the customer on Mikrotik router and disconnect them. Continue?')"
                       class="btn btn-3d btn-3d-danger btn-sm" style="margin-right: 5px;">
                        <i class="fa fa-stop"></i> {Lang::T('Disable')}
                    </a>
                    <a href="{$_url}customers/reconnect/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will disconnect and reconnect the customer. Continue?')"
                       class="btn btn-3d btn-3d-warning btn-sm" style="margin-right: 10px;">
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
</script>
{/literal}
{include file="sections/footer.tpl"}