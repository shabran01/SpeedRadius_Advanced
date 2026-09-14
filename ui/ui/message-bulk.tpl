{include file="sections/header.tpl"}
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">

<div class="row">
	<div class="col-sm-12 col-md-12">
		<div class="panel panel-primary panel-hovered panel-stacked mb30">
			<div class="panel-heading">{Lang::T('Send Bulk Message')}</div>
			<div class="panel-body">
				<form class="form-horizontal" role="form" id="bulkMessageForm">
					<div class="form-group">
						<label class="col-md-2 control-label">{Lang::T('Group')}</label>
						<div class="col-md-6">
							<select class="form-control" name="group" id="group">
								<option value="all" selected>{Lang::T('All Customers')}</option>
								<option value="new">{Lang::T('New Customers')}</option>
								<option value="expired">{Lang::T('Expired Customers')}</option>
								<option value="active">{Lang::T('Active Customers')}</option>
								<option value="active_pppoe">{Lang::T('Active PPPOE')}</option>
								<option value="expired_pppoe">{Lang::T('Expired PPPOE')}</option>
								<option value="all_pppoe">{Lang::T('All PPPOE')}</option>
								<option value="active_hotspot">{Lang::T('Active Hotspot')}</option>
								<option value="expired_hotspot">{Lang::T('Expired Hotspot')}</option>
								<option value="all_hotspot">{Lang::T('All Hotspot')}</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-2 control-label">{Lang::T('Router')}</label>
						<div class="col-md-6">
							<select class="form-control" name="router" id="router">
								<option value="" selected>{Lang::T('All Routers')}</option>
								{foreach $routers as $router}
									<option value="{$router['name']}">{$router['name']}</option>
								{/foreach}
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-2 control-label">{Lang::T('Send Via')}</label>
						<div class="col-md-6">
							<select class="form-control" name="via" id="via">
								<option value="sms" selected>{Lang::T('SMS')}</option>
								<option value="wa">{Lang::T('WhatsApp')}</option>
								<option value="both">{Lang::T('SMS and WhatsApp')}</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-2 control-label">{Lang::T('Batch Size')}</label>
						<div class="col-md-6">
							<select class="form-control" name="batch" id="batch">
								<option value="5">{Lang::T('5 Messages per batch')}</option>
								<option value="10" selected>{Lang::T('10 Messages per batch')}</option>
								<option value="20">{Lang::T('20 Messages per batch')}</option>
								<option value="30">{Lang::T('30 Messages per batch')}</option>
								<option value="50">{Lang::T('50 Messages per batch')}</option>
								<option value="100">{Lang::T('100 Messages per batch')}</option>
								<option value="300">{Lang::T('300 Messages per batch')}</option>
								<option value="500">{Lang::T('500 Messages per batch')}</option>
								<option value="1000">{Lang::T('1000 Messages per batch')}</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-2 control-label">{Lang::T('Delay')}</label>
						<div class="col-md-6">
							<select class="form-control" name="delay" id="delay">
								<option value="0" selected>{Lang::T('No Delay')}</option>
								<option value="3">{Lang::T('3 Seconds')}</option>
								<option value="5">{Lang::T('5 Seconds')}</option>
								<option value="10">{Lang::T('10 Seconds')}</option>
								<option value="15">{Lang::T('15 Seconds')}</option>
								<option value="20">{Lang::T('20 Seconds')}</option>
							</select>
							<span class="help-block">{Lang::T('Pause between batches to avoid being rate-limited by your SMS provider')}</span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-2 control-label">{Lang::T('Message')}</label>
						<div class="col-md-6">
							<textarea class="form-control" id="message" name="message"
								placeholder="{Lang::T('Compose your message...')}" rows="5"></textarea>
							<label style="margin-top:5px;font-weight:normal;">
								<input name="test" id="test" type="checkbox"> {Lang::T('Testing [if checked no real message is sent]')}
							</label>
						</div>
						<p class="help-block col-md-4">
							{Lang::T('Use placeholders:')}
							<br><b>[[name]]</b> - {Lang::T('Customer Name')}
							<br><b>[[user_name]]</b> - {Lang::T('Customer Username')}
							<br><b>[[phone]]</b> - {Lang::T('Customer Phone')}
							<br><b>[[company_name]]</b> - {Lang::T('Your Company Name')}
						</p>
					</div>
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-10">
							<button class="btn btn-success" type="button" id="btnSend">
								<i class="fa fa-send"></i> {Lang::T('Send Message')}</button>
							<button class="btn btn-danger" type="button" id="btnStop" style="display:none;">
								<i class="fa fa-stop"></i> {Lang::T('Stop')}</button>
							<a href="{$_url}dashboard" class="btn btn-default">{Lang::T('Cancel')}</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Progress -->
<div id="progressSection" style="display:none;">
	<div class="panel panel-info">
		<div class="panel-heading"><i class="fa fa-spinner fa-spin"></i> <span id="progressTitle">{Lang::T('Sending...')}</span></div>
		<div class="panel-body">
			<div class="progress" style="height:25px;margin-bottom:10px;">
				<div id="progressBar" class="progress-bar progress-bar-striped active" role="progressbar"
					style="width:0%;min-width:3em;line-height:25px;">0%</div>
			</div>
			<p id="progressText" style="margin:0;"></p>
			<p style="margin-top:8px;">
				<span class="label label-success" id="lblSmsSent">{Lang::T('SMS Sent')}: 0</span>
				<span class="label label-danger" id="lblSmsFailed">{Lang::T('SMS Failed')}: 0</span>
				<span class="label label-success" id="lblWaSent">{Lang::T('WhatsApp Sent')}: 0</span>
				<span class="label label-danger" id="lblWaFailed">{Lang::T('WhatsApp Failed')}: 0</span>
			</p>
		</div>
	</div>
</div>

<!-- Results Table -->
<div class="box">
	<div class="box-header">
		<h3 class="box-title">{Lang::T('Message Results')}</h3>
	</div>
	<div class="box-body">
		<table id="messageResultsTable" class="table table-bordered table-striped table-condensed">
			<thead>
				<tr>
					<th>#</th>
					<th>{Lang::T('Name')}</th>
					<th>{Lang::T('Phone')}</th>
					<th>{Lang::T('Message')}</th>
					<th>{Lang::T('Status')}</th>
				</tr>
			</thead>
			<tbody id="resultsBody">
			</tbody>
		</table>
	</div>
</div>

<script>
var _bulkUrl = '{$_url}message/send_bulk_process';
</script>
{literal}
<script>
(function(){
    var stopped = false;
    var totals = { smsSent:0, smsFailed:0, waSent:0, waFailed:0 };
    var rowCount = 0;

    document.getElementById('btnSend').addEventListener('click', function(){
        var msg = document.getElementById('message').value.trim();
        if(!msg){ alert('Please write a message'); return; }
        if(!confirm('Send bulk messages now?')) return;

        stopped = false;
        totals = { smsSent:0, smsFailed:0, waSent:0, waFailed:0 };
        rowCount = 0;
        document.getElementById('resultsBody').innerHTML = '';
        document.getElementById('progressSection').style.display = 'block';
        document.getElementById('btnSend').style.display = 'none';
        document.getElementById('btnStop').style.display = 'inline-block';
        updateCounters();
        sendBatch(0);
    });

    document.getElementById('btnStop').addEventListener('click', function(){
        stopped = true;
        this.style.display = 'none';
        document.getElementById('btnSend').style.display = 'inline-block';
        document.getElementById('progressTitle').innerHTML = '<i class="fa fa-stop-circle"></i> Stopped';
        var bar = document.getElementById('progressBar');
        bar.className = 'progress-bar progress-bar-danger';
    });

    function sendBatch(offset){
        if(stopped) return;

        var form = document.getElementById('bulkMessageForm');
        var fd = new FormData(form);
        fd.append('offset', offset);
        if(document.getElementById('test').checked) fd.append('test', 'on');

        var xhr = new XMLHttpRequest();
        xhr.open('POST', _bulkUrl, true);
        xhr.onload = function(){
            if(stopped) return;
            try {
                var data = JSON.parse(xhr.responseText);
            } catch(e) {
                document.getElementById('progressTitle').innerHTML = '<i class="fa fa-exclamation-triangle"></i> Server Error';
                document.getElementById('progressText').textContent = 'Invalid response from server';
                document.getElementById('btnStop').style.display = 'none';
                document.getElementById('btnSend').style.display = 'inline-block';
                return;
            }

            if(data.error){
                alert(data.error);
                document.getElementById('btnStop').style.display = 'none';
                document.getElementById('btnSend').style.display = 'inline-block';
                return;
            }

            // Update totals
            totals.smsSent   += data.smsSent || 0;
            totals.smsFailed += data.smsFailed || 0;
            totals.waSent    += data.waSent || 0;
            totals.waFailed  += data.waFailed || 0;
            updateCounters();

            // Append rows
            var tbody = document.getElementById('resultsBody');
            for(var i=0; i < data.results.length; i++){
                rowCount++;
                var r = data.results[i];
                var cls = (r.status.indexOf('Sent') !== -1) ? 'success' : (r.status.indexOf('Test') !== -1 ? 'info' : 'danger');
                tbody.insertAdjacentHTML('beforeend',
                    '<tr class="'+cls+'"><td>'+rowCount+'</td><td>'+esc(r.name)+'</td><td>'+esc(r.phone)+'</td><td>'+esc(r.message)+'</td><td>'+esc(r.status)+'</td></tr>'
                );
            }

            // Update progress
            var pct = data.total > 0 ? Math.round(data.processed / data.total * 100) : 100;
            var bar = document.getElementById('progressBar');
            bar.style.width = pct + '%';
            bar.textContent = pct + '%';
            document.getElementById('progressText').textContent = data.processed + ' / ' + data.total + ' customers processed';

            if(data.done){
                bar.className = 'progress-bar progress-bar-success';
                document.getElementById('progressTitle').innerHTML = '<i class="fa fa-check-circle"></i> Complete!';
                document.getElementById('btnStop').style.display = 'none';
                document.getElementById('btnSend').style.display = 'inline-block';
            } else {
                var delay = parseInt(document.getElementById('delay').value) || 0;
                if(delay > 0){
                    document.getElementById('progressTitle').innerHTML = '<i class="fa fa-clock-o"></i> Waiting ' + delay + 's before next batch...';
                    setTimeout(function(){
                        if(!stopped){
                            document.getElementById('progressTitle').innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
                            sendBatch(data.processed);
                        }
                    }, delay * 1000);
                } else {
                    sendBatch(data.processed);
                }
            }
        };
        xhr.onerror = function(){
            document.getElementById('progressTitle').innerHTML = '<i class="fa fa-exclamation-triangle"></i> Network Error';
            document.getElementById('progressText').textContent = 'Could not reach server. Check your connection and try again.';
            document.getElementById('btnStop').style.display = 'none';
            document.getElementById('btnSend').style.display = 'inline-block';
        };
        xhr.onreadystatechange = function(){
            if(xhr.readyState === 4 && xhr.status !== 200 && xhr.status !== 0){
                document.getElementById('progressTitle').innerHTML = '<i class="fa fa-exclamation-triangle"></i> Server Error (HTTP ' + xhr.status + ')';
                document.getElementById('progressText').textContent = xhr.responseText ? xhr.responseText.substring(0, 300) : 'No response body';
                document.getElementById('btnStop').style.display = 'none';
                document.getElementById('btnSend').style.display = 'inline-block';
                stopped = true;
            }
        };
        xhr.send(fd);
    }

    function updateCounters(){
        document.getElementById('lblSmsSent').textContent   = 'SMS Sent: '   + totals.smsSent;
        document.getElementById('lblSmsFailed').textContent = 'SMS Failed: ' + totals.smsFailed;
        document.getElementById('lblWaSent').textContent    = 'WA Sent: '    + totals.waSent;
        document.getElementById('lblWaFailed').textContent  = 'WA Failed: '  + totals.waFailed;
    }

    function esc(s){
        if(!s) return '';
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }
})();
</script>
{/literal}

{include file="sections/footer.tpl"}
