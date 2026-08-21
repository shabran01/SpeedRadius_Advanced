{include file="sections/header.tpl"}
<div class="row">
  <div class="col-sm-12">
    <div class="panel panel-hovered mb20 panel">
      <div class="panel-heading">{Lang::T('Hotspot Users')}</div>
      <div class="panel-body">
        <div class="md-whiteframe-z1 mb20 text-center" style="padding: 15px">
          <div class="col-md-8">
            <form id="site-search" method="post" action="{$_url}hotspot_users/list/">
              <div class="input-group">
                <div class="input-group-addon">
                  <span class="fa fa-search"></span>
                </div>
                <input type="text" name="username" class="form-control" placeholder="{Lang::T('Search by Username')}...">
                <div class="input-group-btn">
                  <button class="btn btn-success" type="submit">{Lang::T('Search')}</button>
                </div>
              </div>
            </form>
          </div>
          <div class="col-md-4">
            <a href="{$_url}hotspot_users/add" class="btn btn-primary btn-block"><i class="ion ion-android-add"> </i> {Lang::T('New Hotspot User')}</a>
          </div>&nbsp;
        </div>
        <div class="table-responsive">
          <table id="hotspot_users_table" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>{Lang::T('Username')}</th>
                <th>{Lang::T('Address')}</th>
                <th>{Lang::T('Uptime')}</th>
                <th>{Lang::T('Server')}</th>
                <th>{Lang::T('MAC Address')}</th>
                <th>{Lang::T('Session Time')}</th>
                <th style="color: red;">{Lang::T('Upload')}</th>
                <th style="color: green;">{Lang::T('Download')}</th>
                <th>{Lang::T('Total')}</th>
                <th>{Lang::T('Action')}</th>
              </tr>
            </thead>
            <tbody>
              <!-- DataTables will populate the table body dynamically -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
    {include file="pagination.tpl"}
  </div>
</div>
{include file="sections/footer.tpl"}

<!-- Include jQuery and DataTables JS CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
  $(document).ready(function() {
    $('#hotspot_users_table').DataTable({
      "ajax": {
        "url": "{$_url}onlineusers/hotspot_users",
        "dataSrc": ""
      },
      "columns": [
        { "data": "username", "render": function(data, type, row) {
            return '<span style="color: blue;">' + data + '</span>';
          }
        },
        { "data": "address" },
        { "data": "uptime" },
        { "data": "server" },
        { "data": "mac" },
        { "data": "session_time" },
        { "data": "rx_bytes" },
        { "data": "tx_bytes" },
        { "data": "total" },
        { "data": null, "render": function(data, type, row) {
            return '<form method="post" action="{$_url}onlineusers/disconnect/{$routerId}/' + row.username + '/hotspot">' +
                   '<button type="submit" class="btn btn-danger btn-sm" title="Disconnect"><i class="fa fa-times"></i></button>' +
                   '</form>';
          }
        }
      ]
    });
  });
</script>