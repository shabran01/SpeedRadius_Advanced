{include file="sections/header.tpl"}

<h3 align=""><u>Server Status and Information</u>:</h3>
    <style>
        /* CSS styles for the table */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
    <table>

		{foreach $systemInfo as $key => $value}
        <tr>
            <th>{$key}</th>
            <th>{$value}</th>
        </tr>
        {/foreach}
		<tr>
            <th>Memory:</th>
			<th>
			<p>Total Memory: {$memory_usage.total} MB</p>
            <p>Free Memory: {$memory_usage.free} MB</p>
            <p>Used Memory: {$memory_usage.used} MB</p>
			<p>Memory Usage: {$memory_usage.used_percentage}%</p>
			</th>
        </tr>
		<tr>
            <th>Storage:</th>
			<th>
			<p>Total: {$disk_usage['total']}</p>
            <p>Used: {$disk_usage['used']}</p>
            <p>Free: {$disk_usage['free']}</p>
           <p>Usage Percentage: {$disk_usage['used_percentage']}</p>
			</th>
        </tr>
    </table>
<hr>
<br>
<div class="panel panel-primary panel-hovered mb20" id="remote-gauges">
    <div class="panel-heading">
        <strong>Remote Server Gauges</strong>
        <span class="pull-right"><span id="remote-gauge-host">Connecting...</span> &middot; <span id="remote-gauge-updated">Refreshing...</span></span>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-3"><strong>CPU</strong><p id="remote-gauge-cpu">--</p></div>
            <div class="col-sm-3"><strong>Memory</strong><p id="remote-gauge-memory">--</p></div>
            <div class="col-sm-3"><strong>Disk</strong><p id="remote-gauge-disk">--</p></div>
            <div class="col-sm-3"><strong>Uptime</strong><p id="remote-gauge-uptime">--</p></div>
        </div>
        <p id="remote-gauge-error" class="text-danger" style="display:none;"></p>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
               <div class="btn-group pull-right">
				<form action="{$_url}plugin/system_info" method="post">
                   <input type="hidden" name="reload" value="true">
                   <input type="hidden" name="csrf_token" value="{$csrf_token}">
                    <button type="submit" class="btn btn-primary btn-xs" title="Reload FreeRadius Server"
                        onclick="return confirm('Are you sure you want to Reload FreeRadius Server?')"><span
                            class="glyphicon glyphicon-refresh" aria-hidden="true"></span>Reload FreeRADIUS</button>
                </form>
               </div>
                Service Status:
            </div>
            <div class="panel-body">
                <div class="md-whiteframe-z1 mb20 text-center" style="padding: 15px">
                </div>
                <div class="table-responsive">
                    <table>
                        <tbody>
                         {foreach $serviceTable.rows as $row}
                            <tr>
                              <th>{$row.0}</th>
                              <th>{$row.1}</th>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
{if isset($output) && $output != ''} <div class="panel panel-primary panel-hovered panel-stacked mb30">
        <div class="panel-heading">Results</div>
        <div class="panel-body">
          <pre>
		  {if $returnCode === 0}
            <p>Freeradius service reload successfully!</p>
            {else}
            <p>Freeradius service reload failed. Return code: {$returnCode} : {$output|escape} </p>
      	  {/if}
		  </pre>
        </div>
      </div>
    </div> {/if}

    <script>
        function refreshRemoteGauges() {
            var endpoint = '{$_url}plugin/system_info&action=gauges&t=' + Date.now();
            fetch(endpoint)
                .then(function(response) {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(function(data) {
                    if (data.error) throw new Error(data.error);
                    var memoryPercent = data.mem_total_mb > 0 ? (data.mem_used_mb / data.mem_total_mb) * 100 : 0;
                    var diskPercent = data.disk_total_mb > 0 ? (data.disk_used_mb / data.disk_total_mb) * 100 : 0;
                    document.getElementById('remote-gauge-host').textContent = data.host || 'Remote server';
                    document.getElementById('remote-gauge-updated').textContent = 'Updated ' + new Date().toLocaleTimeString();
                    document.getElementById('remote-gauge-cpu').textContent = (data.cpu === null ? '--' : data.cpu.toFixed(2) + '%');
                    document.getElementById('remote-gauge-memory').textContent = data.mem_used_mb + ' / ' + data.mem_total_mb + ' MB (' + memoryPercent.toFixed(2) + '%)';
                    document.getElementById('remote-gauge-disk').textContent = data.disk_used_mb + ' / ' + data.disk_total_mb + ' MB (' + diskPercent.toFixed(2) + '%)';
                    document.getElementById('remote-gauge-uptime').textContent = data.uptime || '--';
                    document.getElementById('remote-gauge-error').style.display = 'none';
                })
                .catch(function(error) {
                    var message = document.getElementById('remote-gauge-error');
                    message.textContent = error.message;
                    message.style.display = 'block';
                });
        }
        refreshRemoteGauges();
        window.setInterval(refreshRemoteGauges, 5000);

        window.addEventListener('DOMContentLoaded', function() {
            var portalLink = "https://github.com/focuslinkstech";
            $('#version').html('System Info Plugin by: <a href="' + portalLink + '">Focuslinks Tech</a>');
        });
    </script>

{include file="sections/footer.tpl"}
