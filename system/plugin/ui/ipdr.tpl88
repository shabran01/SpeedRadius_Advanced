{include file="sections/header.tpl"}



<div class="box box-primary">
    <form method="post">
        <div class="table-responsive">
            <table id="datatable" class="table table-bordered table-striped table-condensed">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th colspan="2" class="text-center">Start</th>
                        <th colspan="2" class="text-center">End</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <th>IP Address</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th><input type="text" name="username" value="{$username}" placeholder="username"
                                class="form-control"></th>
                        <th><input type="text" name="ip" value="{$ip}" placeholder="IP Address" class="form-control">
                        </th>
                        <th><input type="date" name="dt_start" value="{$dt_start}" placeholder="{date("Y-m-d")}"
                                class="form-control"></th>
                        <th><input type="time" name="dt_start_time" value="{$dt_start_time}" placeholder="{date("H:i")}"
                                class="form-control"></th>
                        <th><input type="date" name="dt_end" value="{$dt_end}" placeholder="{date("Y-m-d")}"
                                class="form-control"></th>
                        <th><input type="time" name="dt_end_time" value="{$dt_end_time}" placeholder="{date("H:i")}"
                                class="form-control"></th>
                        <th><button type="submit" class="btn btn-info btn-block">query</button></th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>{Lang::T('Username')}</th>
                        <th>Mac / IP Host <sup>(pppoe)</sup></th>
                        <th>{Lang::T('Source')}</th>
                        <th>{Lang::T('Type')}</th>
                        <th>{Lang::T('Destination')}</th>
                        <th>{Lang::T('Method')}</th>
                        <th>{Lang::T('Date')}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $d as $ds}
                        <tr {if $ds['type']=='login'}class="success" {elseif $ds['type']=='logout'}class="danger" {/if}>
                            <td><a href="{$_url}customers/viewu/{$ds['username']}">{$ds['username']}</a></td>
                            <td>{$ds['mac']}</td>
                            <td>
                                {if $ds['type']=='received'}
                                    <a href="https://whatismyipaddress.com/ip/{$ds['src_ip']}" target="_blank"
                                        rel="nofollow noreferrer noopener">{$ds['src_ip']}</a>
                                {else}
                                    {$ds['src_ip']}
                                {/if}
                                <br> {$ds['src_port']}
                            </td>
                            <td>{$ds['type']}</td>
                            <td>
                                {if $ds['type']=='access'}
                                    <a href="https://whatismyipaddress.com/ip/{$ds['dst_ip']}" target="_blank"
                                        rel="nofollow noreferrer noopener">{$ds['dst_ip']}</a>
                                {else}
                                    {$ds['dst_ip']}
                                {/if}
                                <br> {$ds['dst_port']}
                            </td>
                            <td>{$ds['protocol']}</td>
                            <td>
                                {if $ds['date_start'] == $ds['date_end']}
                                    {Lang::dateTimeFormat($ds['date_start'])}
                                {else}
                                    {Lang::dateTimeFormat($ds['date_start'])}<br>{Lang::dateTimeFormat($ds['date_end'])}
                                {{/if}}
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
                <tr>
                    <td></td>
                    <td><button type="submit" class="btn btn-success btn-sm" name="export" value="csv"
                            onclick="return confirm('Export all data based query?')">CSV</button></td>
                    <td colspan="4"></td>
                    <td><button type="submit" class="btn btn-danger btn-sm" name="delete" value="yes"
                            onclick="return confirm('Delete all data based query?')">DELETE</button></td>
                </tr>
            </table>
        </div>
    </form>
    {include file="pagination.tpl"}
</div>
<div class="row">
    <div class="col-md-3 col-sm-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    On Login | Hotspot
                </h3>
            </div>
            <div class="box-body with-border">
                Hotspot
                <textarea class="form-control" rows="5" onclick="this.select()">
:local usermac [/ip hotspot active get [find user=$user] mac-address];
:local userip [/ip hotspot active get [find mac-address=$usermac] address];
/tool fetch http-header-field="Content-Type: application/x-www-form-urlencoded" http-method=post http-data="user=$user&mac=$usermac&ip=userip" keep-result=no url="{$_url}plugin/ipdr_onlogin"
</textarea>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    On Logout | Hotspot
                </h3>
            </div>
            <div class="box-body with-border">
                Hotspot
                <textarea class="form-control" rows="3" onclick="this.select()">
/tool fetch http-header-field="Content-Type: application/x-www-form-urlencoded" http-method=post http-data="user=$user" keep-result=no url="{$_url}plugin/ipdr_onlogout"
</textarea>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    System &gt; script | Hotspot Only
                </h3>
            </div>
            <div class="box-body with-border">
                <textarea class="form-control" rows="5" onclick="this.select()">
:local logcon [/ip firewall connection print as-value];
:put $logcon;
/tool fetch http-header-field="Content-Type: application/x-www-form-urlencoded" http-method=post http-data="data=$logcon" keep-result=no url="{$_url}plugin/ipdr_log"
</textarea>
                <p class="text-muted">Add the script to scheduler, run it every minute or every 5 minutes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    System &gt; script | Hotspot, PPPOE, Radius
                </h3>
            </div>
            <div class="box-body with-border">
                <textarea class="form-control" rows="5" onclick="this.select()">
# Start Hotspot Log, delete if not needed
:local hph [/ip hotspot host print as-value];
:put $hph;
:log info ("send hotspot host");
/tool fetch http-header-field="Content-Type: application/x-www-form-urlencoded" http-method=post http-data="$hph" keep-result=no url="{$_url}plugin/ipdr_log&tipe=hotspotHost"

:local hpt [/ip hotspot active print as-value];
:put $hpt;
:log info ("send hotspot active");
/tool fetch http-header-field="Content-Type: application/x-www-form-urlencoded" http-method=post http-data="$hpt" keep-result=no url="{$_url}plugin/ipdr_log&tipe=hotspot"
# End Hotspot Log

# Start PPPOE Log, delete if not needed
:local ppo [/ppp active print as-value];
:put $ppo;
:log info ("send pppoe");
/tool fetch http-header-field="Content-Type: application/x-www-form-urlencoded" http-method=post http-data="$ppo" keep-result=no url="{$_url}plugin/ipdr_log&tipe=pppoe"
# End PPPOE Log

# Start Log Connection
:local result ""
:local logcon [/ip firewall connection print as-value where tcp-state=established]
:local coun [:len $logcon]
:for i from=0 to=($coun - 1) do={
    :local elemen [:pick $logcon $i]
    :set result ($result . [:tostr $elemen])
    :if (($i > 0 && $i % 300 = 0) || ($i = ($coun - 1))) do={
        :put $i;
        :put $result
        :log info ("send data");
        /tool fetch http-header-field="Content-Type: application/x-www-form-urlencoded" http-method=post http-data="$result" keep-result=no url="{$_url}plugin/ipdr_log&tipe=data"
        :log info ("send log done");
        :set result ""
    }
}
:log info ("send data");
/tool fetch http-header-field="Content-Type: application/x-www-form-urlencoded" http-method=post http-data="$result" keep-result=no url="{$_url}plugin/ipdr_log&tipe=data"
:log info ("send log done");
#End Log Connection
</textarea>
                <p class="text-muted">Add the script to scheduler, run it every minute or every 5 minutes, you can remove services you don't need, example delete hotspot if you only use PPPOE</p>
            </div>
        </div>
    </div>
</div>
{include file="sections/footer.tpl"}