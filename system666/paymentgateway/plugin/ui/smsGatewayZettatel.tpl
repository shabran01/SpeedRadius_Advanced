{include file="sections/header.tpl"}

<div class="row">
  <div class="col-sm-12">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title">Zettatel SMS Gateway</h3>
      </div>
      <div class="panel-body">
        <ul class="nav nav-tabs">
          <li class="{if $menu!='config'}active{/if}"><a href="{$_url}plugin/smsGatewayZettatel">Dashboard</a></li>
          <li class="{if $menu=='config'}active{/if}"><a href="{$_url}plugin/smsGatewayZettatel_config">Configuration</a></li>
        </ul>
        <br>
        {if $menu=='config'}
          <form class="form-horizontal" method="post" role="form" action="{$_url}plugin/smsGatewayZettatel_config">
            <div class="form-group">
              <label class="col-md-2 control-label">API Key</label>
              <div class="col-md-6">
                <input type="text" class="form-control" name="zettatel_api_key" value="{$_c['zettatel_api_key']}">
                <p class="help-block">Your Zettatel API Key</p>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label">User ID</label>
              <div class="col-md-6">
                <input type="text" class="form-control" name="zettatel_user_id" value="{$_c['zettatel_user_id']}">
                <p class="help-block">Your Zettatel User ID</p>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label">Password</label>
              <div class="col-md-6">
                <input type="text" class="form-control" name="zettatel_password" value="{$_c['zettatel_password']}">
                <p class="help-block">Your Zettatel Password</p>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-2 control-label">Sender ID</label>
              <div class="col-md-6">
                <input type="text" class="form-control" name="zettatel_sender_id" value="{$_c['zettatel_sender_id']}">
                <p class="help-block">Approved Sender ID</p>
              </div>
            </div>
            <div class="form-group">
              <div class="col-lg-offset-2 col-lg-10">
                <button class="btn btn-primary" type="submit">Save Changes</button>
              </div>
            </div>
          </form>
        {else}
          <h4>Recent Logs</h4>
          <table class="table table-bordered table-striped">
            <thead><tr><th>Date</th><th>Phone</th><th>Message</th><th>Status</th><th>Info</th></tr></thead>
            <tbody>
              {foreach $sms_logs as $l}
                <tr>
                  <td>{$l.created_at}</td>
                  <td>{$l.phone}</td>
                  <td style="max-width:300px;white-space:normal;">{$l.message|escape}</td>
                  <td>{$l.status}</td>
                  <td>{$l.status_message}</td>
                </tr>
              {/foreach}
            </tbody>
          </table>
          <p class="help-block">Only last 10 logs shown.</p>
        {/if}
      </div>
    </div>
  </div>
</div>

{include file="sections/footer.tpl"}
