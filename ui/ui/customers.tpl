{include file="sections/header.tpl"}
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        display: inline-block;
        padding: 5px 10px;
        margin-right: 5px;
        border: 1px solid #ccc;
        background-color: #fff;
        color: #333;
        cursor: pointer;
    }

    .badge-service-type {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        color: white;
    }

    .badge-service-type.pppoe {
        background-color: #28a745;
    }

    .badge-service-type.hotspot {
        background-color: #17a2b8;
    }

    .badge-service-type.vpn {
        background-color: #6610f2;
    }

    .badge-service-type.others {
        background-color: #6c757d;
    }

    .btn-3d {
        position: relative;
        transform-style: preserve-3d;
        transition: all 0.2s ease;
    }

    .btn-3d.btn-success {
        background: linear-gradient(to bottom, #28a745, #218838);
        box-shadow: 0 4px 0 #145523;
        transform: translateY(-2px);
    }

    .btn-3d.btn-success:hover {
        background: linear-gradient(to bottom, #218838, #1e7e34);
        transform: translateY(0);
        box-shadow: 0 2px 0 #145523;
    }

    .btn-3d.btn-success:active {
        transform: translateY(2px);
        box-shadow: 0 0 0 #145523;
    }

    .btn-3d.btn-info {
        background: linear-gradient(to bottom, #17a2b8, #138496);
        box-shadow: 0 4px 0 #0c5460;
        transform: translateY(-2px);
    }

    .btn-3d.btn-info:hover {
        background: linear-gradient(to bottom, #138496, #10707f);
        transform: translateY(0);
        box-shadow: 0 2px 0 #0c5460;
    }

    .btn-3d.btn-info:active {
        transform: translateY(2px);
        box-shadow: 0 0 0 #0c5460;
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                    <div class="btn-group pull-right">
                        <a href="{$_url}customers/add" class="btn btn-success btn-xs btn-3d" style="margin-right: 5px; padding: 8px 15px; font-size: 14px;">
                            <i class="ion ion-android-add"></i> {Lang::T('Add Customer')}
                        </a>
                        <a href="{$_url}customers/upload" class="btn btn-info btn-xs btn-3d" style="margin-right: 5px; padding: 8px 15px; font-size: 14px;">
                            <i class="fa fa-upload"></i> Upload CSV
                        </a>
                        <a class="btn btn-primary btn-xs" title="save" href="{$_url}customers/csv&token={$csrf_token}"
                            onclick="return ask(this, 'This will export to CSV?')">
                            <span class="glyphicon glyphicon-download" aria-hidden="true"></span> CSV
                        </a>
                    </div>
                {/if}
                {Lang::T('Manage Contact')}
            </div>
            <div class="panel-body">
                <form id="site-search" method="post" action="{$_url}customers">
                    <input type="hidden" name="csrf_token" value="{$csrf_token}">
                    <div class="md-whiteframe-z1 mb20 text-center" style="padding: 15px">
                        <div class="col-lg-4">
                            <div class="input-group">
                                <span class="input-group-addon">Order&nbsp;&nbsp;</span>
                                <div class="row row-no-gutters">
                                    <div class="col-xs-8">
                                        <select class="form-control" id="order" name="order">
                                            <option value="username" {if $order eq 'username' }selected{/if}>
                                                {Lang::T('Username')}</option>
                                            <option value="fullname" {if $order eq 'fullname' }selected{/if}>
                                                {Lang::T('First Name')}</option>
                                            <option value="lastname" {if $order eq 'lastname' }selected{/if}>
                                                {Lang::T('Last Name')}</option>
                                            <option value="created_at" {if $order eq 'created_at' }selected{/if}>
                                                {Lang::T('Created Date')}</option>
                                            <option value="balance" {if $order eq 'balance' }selected{/if}>
                                                {Lang::T('Balance')}</option>
                                            <option value="status" {if $order eq 'status' }selected{/if}>
                                                {Lang::T('Status')}</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-4">
                                        <select class="form-control" id="orderby" name="orderby">
                                            <option value="asc" {if $orderby eq 'asc' }selected{/if}>
                                                {Lang::T('Ascending')}</option>
                                            <option value="desc" {if $orderby eq 'desc' }selected{/if}>
                                                {Lang::T('Descending')}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="input-group">
                                <span class="input-group-addon">Status</span>
                                <select class="form-control" id="filter" name="filter">
                                    {foreach $statuses as $status}
                                        <option value="{$status}" {if $filter eq $status }selected{/if}>{Lang::T($status)}
                                        </option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="input-group">
                                <span class="input-group-addon">Service</span>
                                <select class="form-control" id="service_filter" name="service_filter">
                                    <option value="">All Services</option>
                                    <option value="PPPoE" {if $service_filter eq 'PPPoE'}selected{/if}>PPPoE Only</option>
                                    <option value="Hotspot" {if $service_filter eq 'Hotspot'}selected{/if}>Hotspot Only</option>
                                    <option value="VPN" {if $service_filter eq 'VPN'}selected{/if}>VPN Only</option>
                                    <option value="Others" {if $service_filter eq 'Others'}selected{/if}>Others</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="{Lang::T('Search')}..." value="{$search}">
                                <div class="input-group-btn">
                                    <button class="btn btn-primary" type="submit"><span
                                            class="fa fa-search"></span> {Lang::T('Search')}</button>
                                    <button class="btn btn-info" type="submit" name="export" value="csv">
                                        <span class="glyphicon glyphicon-download" aria-hidden="true"></span> CSV
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
                <br>&nbsp;
                <div class="table-responsive table_mobile">
                    <table id="customerTable" class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>{Lang::T('Username')}</th>
                                <th>Photo</th>
                                <th>{Lang::T('Account Type')}</th>
                                <th>{Lang::T('Full Name')}</th>
                                <th>{Lang::T('Balance')}</th>
                                <th>{Lang::T('Contact')}</th>
                                <th>{Lang::T('Package')}</th>
                                <th>{Lang::T('Service Type')}</th>
                                <th>PPPOE</th>
                                <th>{Lang::T('Status')}</th>
                                <th>{Lang::T('Created On')}</th>
                                <th>{Lang::T('Manage')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $d as $ds}
                                <tr {if $ds['status'] != 'Active'}class="danger" {/if}>
                                    <td onclick="window.location.href = '{$_url}customers/view/{$ds['id']}'"
                                        style="cursor:pointer;">{$ds['username']}</td>
                                    <td>
                                        <a href="{$UPLOAD_PATH}{$ds['photo']}" target="photo">
                                            <img src="{$UPLOAD_PATH}{$ds['photo']}.thumb.jpg" width="32" alt="">
                                        </a>
                                    </td>
                                    <td>{$ds['account_type']}</td>
                                    <td onclick="window.location.href = '{$_url}customers/view/{$ds['id']}'"
                                        style="cursor: pointer;">{$ds['fullname']}</td>
                                    <td>{Lang::moneyFormat($ds['balance'])}</td>
                                    <td align="center">
                                        {if $ds['phonenumber']}
                                            <a href="tel:{$ds['phonenumber']}" class="btn btn-default btn-xs"
                                                title="{$ds['phonenumber']}"><i class="glyphicon glyphicon-earphone"></i></a>
                                        {/if}
                                        {if $ds['email']}
                                            <a href="mailto:{$ds['email']}" class="btn btn-default btn-xs"
                                                title="{$ds['email']}"><i class="glyphicon glyphicon-envelope"></i></a>
                                        {/if}
                                        {if $ds['coordinates']}
                                            <a href="https://www.google.com/maps/dir//{$ds['coordinates']}/" target="_blank"
                                                class="btn btn-default btn-xs" title="{$ds['coordinates']}"><i
                                                    class="glyphicon glyphicon-map-marker"></i></a>
                                        {/if}
                                    </td>
                                    <td align="center" api-get-text="{$_url}autoload/plan_is_active/{$ds['id']}">
                                        <span class="label label-default">&bull;</span>
                                    </td>
                                    <td>
                                        <span class="badge-service-type {strtolower($ds['service_type'])}">
                                            {$ds['service_type']}
                                        </span>
                                    </td>
                                    <td>{$ds['pppoe_username']}
                                        {if !empty($ds['pppoe_username']) && !empty($ds['pppoe_ip'])}:{/if}
                                        {$ds['pppoe_ip']}
                                    </td>
                                    <td>{Lang::T($ds['status'])}</td>
                                    <td>{Lang::dateTimeFormat($ds['created_at'])}</td>
                                    <td align="center">
                                        <a href="{$_url}customers/view/{$ds['id']}" id="{$ds['id']}"
                                            style="margin: 0px; color:black"
                                            class="btn btn-success btn-xs">&nbsp;&nbsp;{Lang::T('View')}&nbsp;&nbsp;</a>
                                        <a href="{$_url}customers/edit/{$ds['id']}&token={$csrf_token}" id="{$ds['id']}"
                                            style="margin: 0px; color:black"
                                            class="btn btn-info btn-xs">&nbsp;&nbsp;{Lang::T('Edit')}&nbsp;&nbsp;</a>
                                        <a href="{$_url}customers/sync/{$ds['id']}&token={$csrf_token}" id="{$ds['id']}"
                                            style="margin: 5px; color:black"
                                            class="btn btn-success btn-xs">&nbsp;&nbsp;{Lang::T('Sync')}&nbsp;&nbsp;</a>
                                        <a href="{$_url}plan/recharge/{$ds['id']}&token={$csrf_token}" id="{$ds['id']}"
                                            style="margin: 0px;" class="btn btn-primary btn-xs">{Lang::T('Recharge')}</a>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                {include file="pagination.tpl"}
            </div>
        </div>
    </div>
</div>
{include file="sections/footer.tpl"}