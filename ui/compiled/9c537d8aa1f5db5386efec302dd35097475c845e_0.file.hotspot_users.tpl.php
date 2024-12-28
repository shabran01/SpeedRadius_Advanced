<?php
/* Smarty version 4.5.3, created on 2024-12-23 06:47:38
  from '/var/www/html/snootylique/ui/ui/hotspot_users.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_6768dd5aa9e1c7_62933996',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9c537d8aa1f5db5386efec302dd35097475c845e' => 
    array (
      0 => '/var/www/html/snootylique/ui/ui/hotspot_users.tpl',
      1 => 1734662748,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:sections/header.tpl' => 1,
    'file:pagination.tpl' => 1,
    'file:sections/footer.tpl' => 1,
  ),
),false)) {
function content_6768dd5aa9e1c7_62933996 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<div class="row">
  <div class="col-sm-12">
    <div class="panel panel-hovered mb20 panel">
      <div class="panel-heading"><?php echo Lang::T('Hotspot Users');?>
</div>
      <div class="panel-body">
        <div class="md-whiteframe-z1 mb20 text-center" style="padding: 15px">
          <div class="col-md-8">
            <form id="site-search" method="post" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
hotspot_users/list/">
              <div class="input-group">
                <div class="input-group-addon">
                  <span class="fa fa-search"></span>
                </div>
                <input type="text" name="username" class="form-control" placeholder="<?php echo Lang::T('Search by Username');?>
...">
                <div class="input-group-btn">
                  <button class="btn btn-success" type="submit"><?php echo Lang::T('Search');?>
</button>
                </div>
              </div>
            </form>
          </div>
          <div class="col-md-4">
            <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
hotspot_users/add" class="btn btn-primary btn-block"><i class="ion ion-android-add"> </i> <?php echo Lang::T('New Hotspot User');?>
</a>
          </div>&nbsp;
        </div>
        <div class="table-responsive">
          <table id="hotspot_users_table" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th><?php echo Lang::T('Username');?>
</th>
                <th><?php echo Lang::T('Address');?>
</th>
                <th><?php echo Lang::T('Uptime');?>
</th>
                <th><?php echo Lang::T('Server');?>
</th>
                <th><?php echo Lang::T('MAC Address');?>
</th>
                <th><?php echo Lang::T('Session Time');?>
</th>
                <th style="color: red;"><?php echo Lang::T('Upload');?>
</th>
                <th style="color: green;"><?php echo Lang::T('Download');?>
</th>
                <th><?php echo Lang::T('Total');?>
</th>
                <th><?php echo Lang::T('Action');?>
</th>
              </tr>
            </thead>
            <tbody>
              <!-- DataTables will populate the table body dynamically -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <?php $_smarty_tpl->_subTemplateRender("file:pagination.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
  </div>
</div>
<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<!-- Include jQuery and DataTables JS CDN -->
<?php echo '<script'; ?>
 src="https://code.jquery.com/jquery-3.6.0.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
>
  $(document).ready(function() {
    $('#hotspot_users_table').DataTable({
      "ajax": {
        "url": "<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
onlineusers/hotspot_users",
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
            return '<form method="post" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
onlineusers/disconnect/<?php echo $_smarty_tpl->tpl_vars['routerId']->value;?>
/' + row.username + '/hotspot">' +
                   '<button type="submit" class="btn btn-danger btn-sm" title="Disconnect"><i class="fa fa-times"></i></button>' +
                   '</form>';
          }
        }
      ]
    });
  });
<?php echo '</script'; ?>
><?php }
}
