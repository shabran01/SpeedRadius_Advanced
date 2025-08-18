<?php
/* Smarty version 4.5.3, created on 2025-08-18 09:22:10
  from '/var/www/html/ISP/ui/ui/system_users.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a2c6924d3da1_34606375',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '64ddece1405e572262a2a14259de98a03d6cf26d' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/system_users.tpl',
      1 => 1754916133,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:sections/header.tpl' => 1,
    'file:sections/footer.tpl' => 1,
  ),
),false)) {
function content_68a2c6924d3da1_34606375 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-primary btn-xs" onclick="refreshData()">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-success btn-xs" onclick="exportCSV()">
                        <i class="fa fa-download"></i> Export CSV
                    </button>
                    <button type="button" class="btn btn-info btn-xs" onclick="showAddUserModal()">
                        <i class="fa fa-plus"></i> Add User
                    </button>
                </div>
                System Users Management
            </div>
            <div class="panel-body">
                <!-- Router Selection -->
                <div class="row mb20">
                    <div class="col-md-4">
                        <label>Select Router:</label>
                        <select class="form-control" id="router_select" onchange="changeRouter()">
                            <?php if ($_smarty_tpl->tpl_vars['routers']->value) {?>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['routers']->value, 'router');
$_smarty_tpl->tpl_vars['router']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['router']->value) {
$_smarty_tpl->tpl_vars['router']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['router']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['default_router']->value && $_smarty_tpl->tpl_vars['router']->value['id'] == $_smarty_tpl->tpl_vars['default_router']->value['id']) {?>selected<?php }?>>
                                        <?php echo $_smarty_tpl->tpl_vars['router']->value['name'];?>
 (<?php echo $_smarty_tpl->tpl_vars['router']->value['ip_address'];?>
)
                                    </option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            <?php } else { ?>
                                <option value="">No routers configured</option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <div class="alert alert-info" style="margin-bottom: 0;">
                            <i class="fa fa-info-circle"></i> 
                            <strong>Router Info:</strong> 
                            <span id="router_info">
                                <?php if ($_smarty_tpl->tpl_vars['default_router']->value) {?>
                                    <?php echo $_smarty_tpl->tpl_vars['default_router']->value['name'];?>
 - <?php echo $_smarty_tpl->tpl_vars['default_router']->value['ip_address'];?>

                                <?php } else { ?>
                                    No router selected
                                <?php }?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb20" id="stats_container">
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card total-users">
                            <div class="stats-icon">
                                <i class="fa fa-users"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number" id="total_users">
                                    <?php if ($_smarty_tpl->tpl_vars['users_data']->value['success']) {
echo $_smarty_tpl->tpl_vars['users_data']->value['stats']['total'];
} else { ?>0<?php }?>
                                </div>
                                <div class="stats-label">Total Users</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card active-users">
                            <div class="stats-icon">
                                <i class="fa fa-check-circle"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number" id="active_users">
                                    <?php if ($_smarty_tpl->tpl_vars['users_data']->value['success']) {
echo $_smarty_tpl->tpl_vars['users_data']->value['stats']['active'];
} else { ?>0<?php }?>
                                </div>
                                <div class="stats-label">Active Users</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card disabled-users">
                            <div class="stats-icon">
                                <i class="fa fa-ban"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number" id="disabled_users">
                                    <?php if ($_smarty_tpl->tpl_vars['users_data']->value['success']) {
echo $_smarty_tpl->tpl_vars['users_data']->value['stats']['disabled'];
} else { ?>0<?php }?>
                                </div>
                                <div class="stats-label">Disabled Users</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card last-updated">
                            <div class="stats-icon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-number" id="last_updated" style="font-size: 14px;">
                                    <?php if ($_smarty_tpl->tpl_vars['users_data']->value['success']) {
echo $_smarty_tpl->tpl_vars['users_data']->value['last_updated'];
} else { ?>Never<?php }?>
                                </div>
                                <div class="stats-label">Last Updated</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#users_tab" aria-controls="users_tab" role="tab" data-toggle="tab">
                            <i class="fa fa-users"></i> Users
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#groups_tab" aria-controls="groups_tab" role="tab" data-toggle="tab">
                            <i class="fa fa-group"></i> Groups
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#active_tab" aria-controls="active_tab" role="tab" data-toggle="tab">
                            <i class="fa fa-signal"></i> Active Users
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Users Tab -->
                    <div role="tabpanel" class="tab-pane active" id="users_tab">
                        <div class="row mt20">
                            <div class="col-md-12">
                                <!-- Search and Filter Controls -->
                                <div class="row mb15">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="search_input" placeholder="Search users..." onkeyup="filterUsers()">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control" id="status_filter" onchange="filterUsers()">
                                            <option value="">All Status</option>
                                            <option value="active">Active Only</option>
                                            <option value="disabled">Disabled Only</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control" id="group_filter" onchange="filterUsers()">
                                            <option value="">All Groups</option>
                                            <?php if ($_smarty_tpl->tpl_vars['groups_data']->value['success']) {?>
                                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['groups_data']->value['data'], 'group');
$_smarty_tpl->tpl_vars['group']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['group']->value) {
$_smarty_tpl->tpl_vars['group']->do_else = false;
?>
                                                    <option value="<?php echo $_smarty_tpl->tpl_vars['group']->value['name'];?>
"><?php echo $_smarty_tpl->tpl_vars['group']->value['name'];?>
</option>
                                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                            <?php }?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-default btn-block" onclick="clearFilters()">
                                            <i class="fa fa-eraser"></i> Clear
                                        </button>
                                    </div>
                                </div>

                                <!-- Users Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="users_table">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Group</th>
                                                <th>Allowed Address</th>
                                                <th>Last Logged In</th>
                                                <th>Status</th>
                                                <th>Comment</th>
                                                <th width="120">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="users_tbody">
                                            <?php if ($_smarty_tpl->tpl_vars['users_data']->value['success'] && $_smarty_tpl->tpl_vars['users_data']->value['data']) {?>
                                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['users_data']->value['data'], 'user');
$_smarty_tpl->tpl_vars['user']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['user']->value) {
$_smarty_tpl->tpl_vars['user']->do_else = false;
?>
                                                    <tr data-username="<?php echo $_smarty_tpl->tpl_vars['user']->value['name'];?>
" data-group="<?php echo $_smarty_tpl->tpl_vars['user']->value['group'];?>
" data-status="<?php echo $_smarty_tpl->tpl_vars['user']->value['status'];?>
">
                                                        <td><strong><?php echo $_smarty_tpl->tpl_vars['user']->value['name'];?>
</strong></td>
                                                        <td>
                                                            <span class="label label-default"><?php echo $_smarty_tpl->tpl_vars['user']->value['group'];?>
</span>
                                                        </td>
                                                        <td><code><?php echo $_smarty_tpl->tpl_vars['user']->value['address'];?>
</code></td>
                                                        <td><?php echo $_smarty_tpl->tpl_vars['user']->value['last_logged_in'];?>
</td>
                                                        <td>
                                                            <?php if ($_smarty_tpl->tpl_vars['user']->value['status'] == 'active') {?>
                                                                <span class="label label-success">
                                                                    <i class="fa fa-check"></i> Active
                                                                </span>
                                                            <?php } else { ?>
                                                                <span class="label label-warning">
                                                                    <i class="fa fa-ban"></i> Disabled
                                                                </span>
                                                            <?php }?>
                                                        </td>
                                                        <td><?php echo $_smarty_tpl->tpl_vars['user']->value['comment'];?>
</td>
                                                        <td>
                                                            <div class="btn-group btn-group-xs">
                                                                <button type="button" class="btn btn-primary" onclick="editUser('<?php echo $_smarty_tpl->tpl_vars['user']->value['id'];?>
', '<?php echo $_smarty_tpl->tpl_vars['user']->value['name'];?>
', '<?php echo $_smarty_tpl->tpl_vars['user']->value['group'];?>
', '<?php echo $_smarty_tpl->tpl_vars['user']->value['address'];?>
', '<?php echo $_smarty_tpl->tpl_vars['user']->value['comment'];?>
', <?php if ($_smarty_tpl->tpl_vars['user']->value['disabled']) {?>true<?php } else { ?>false<?php }?>)" title="Edit">
                                                                    <i class="fa fa-edit"></i>
                                                                </button>
                                                                <?php if ($_smarty_tpl->tpl_vars['user']->value['status'] == 'active') {?>
                                                                    <button type="button" class="btn btn-warning" onclick="disableUser('<?php echo $_smarty_tpl->tpl_vars['user']->value['id'];?>
', '<?php echo $_smarty_tpl->tpl_vars['user']->value['name'];?>
')" title="Disable">
                                                                        <i class="fa fa-ban"></i>
                                                                    </button>
                                                                <?php } else { ?>
                                                                    <button type="button" class="btn btn-success" onclick="enableUser('<?php echo $_smarty_tpl->tpl_vars['user']->value['id'];?>
', '<?php echo $_smarty_tpl->tpl_vars['user']->value['name'];?>
')" title="Enable">
                                                                        <i class="fa fa-check"></i>
                                                                    </button>
                                                                <?php }?>
                                                                <?php if ($_smarty_tpl->tpl_vars['user']->value['name'] != 'admin') {?>
                                                                    <button type="button" class="btn btn-danger" onclick="removeUser('<?php echo $_smarty_tpl->tpl_vars['user']->value['id'];?>
', '<?php echo $_smarty_tpl->tpl_vars['user']->value['name'];?>
')" title="Remove">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                <?php }?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">
                                                        <?php if ($_smarty_tpl->tpl_vars['users_data']->value['success']) {?>
                                                            No system users found
                                                        <?php } else { ?>
                                                            <div class="alert alert-danger">
                                                                Error: <?php echo $_smarty_tpl->tpl_vars['users_data']->value['error'];?>

                                                            </div>
                                                        <?php }?>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Groups Tab -->
                    <div role="tabpanel" class="tab-pane" id="groups_tab">
                        <div class="row mt20">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Group Name</th>
                                                <th>Policies</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($_smarty_tpl->tpl_vars['groups_data']->value['success'] && $_smarty_tpl->tpl_vars['groups_data']->value['data']) {?>
                                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['groups_data']->value['data'], 'group');
$_smarty_tpl->tpl_vars['group']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['group']->value) {
$_smarty_tpl->tpl_vars['group']->do_else = false;
?>
                                                    <tr>
                                                        <td><strong><?php echo $_smarty_tpl->tpl_vars['group']->value['name'];?>
</strong></td>
                                                        <td><code><?php echo $_smarty_tpl->tpl_vars['group']->value['policy'];?>
</code></td>
                                                    </tr>
                                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="2" class="text-center">
                                                        <?php if ($_smarty_tpl->tpl_vars['groups_data']->value['success']) {?>
                                                            No user groups found
                                                        <?php } else { ?>
                                                            <div class="alert alert-danger">
                                                                Error: <?php echo $_smarty_tpl->tpl_vars['groups_data']->value['error'];?>

                                                            </div>
                                                        <?php }?>
                                                    </td>
                                                </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Users Tab -->
                    <div role="tabpanel" class="tab-pane" id="active_tab">
                        <div class="row mt20">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary btn-sm mb15" onclick="refreshActiveUsers()">
                                    <i class="fa fa-refresh"></i> Refresh Active Users
                                </button>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="active_users_table">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Address</th>
                                                <th>Via</th>
                                                <th>When</th>
                                            </tr>
                                        </thead>
                                        <tbody id="active_users_tbody">
                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    <em>Click "Refresh Active Users" to load data</em>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Add New System User</h4>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="form-group">
                        <label for="add_name">Username</label>
                        <input type="text" class="form-control" id="add_name" name="name" required>
                        <small class="text-muted">Letters, numbers, underscore and dash only</small>
                    </div>
                    <div class="form-group">
                        <label for="add_group">Group</label>
                        <select class="form-control" id="add_group" name="group">
                            <?php if ($_smarty_tpl->tpl_vars['groups_data']->value['success']) {?>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['groups_data']->value['data'], 'group');
$_smarty_tpl->tpl_vars['group']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['group']->value) {
$_smarty_tpl->tpl_vars['group']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['group']->value['name'];?>
"><?php echo $_smarty_tpl->tpl_vars['group']->value['name'];?>
</option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            <?php }?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="add_address">Allowed Address</label>
                        <input type="text" class="form-control" id="add_address" name="address" value="0.0.0.0/0">
                        <small class="text-muted">IP address or network (e.g., 192.168.1.0/24)</small>
                    </div>
                    <div class="form-group">
                        <label for="add_password">Password</label>
                        <input type="password" class="form-control" id="add_password" name="password" required>
                        <small class="text-muted">Minimum 3 characters</small>
                    </div>
                    <div class="form-group">
                        <label for="add_confirm_password">Confirm Password</label>
                        <input type="password" class="form-control" id="add_confirm_password">
                    </div>
                    <div class="form-group">
                        <label for="add_comment">Comment</label>
                        <input type="text" class="form-control" id="add_comment" name="comment">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="add_disabled" name="disabled"> Disabled
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addUser()">Add User</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Edit System User</h4>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="edit_user_id">
                    <div class="form-group">
                        <label for="edit_name">Username</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                        <small class="text-muted">Letters, numbers, underscore and dash only</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_group">Group</label>
                        <select class="form-control" id="edit_group" name="group">
                            <?php if ($_smarty_tpl->tpl_vars['groups_data']->value['success']) {?>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['groups_data']->value['data'], 'group');
$_smarty_tpl->tpl_vars['group']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['group']->value) {
$_smarty_tpl->tpl_vars['group']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['group']->value['name'];?>
"><?php echo $_smarty_tpl->tpl_vars['group']->value['name'];?>
</option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            <?php }?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_address">Allowed Address</label>
                        <input type="text" class="form-control" id="edit_address" name="address">
                        <small class="text-muted">IP address or network (e.g., 192.168.1.0/24)</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_password">New Password</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                        <small class="text-muted">Leave blank to keep current password</small>
                    </div>
                    <div class="form-group">
                        <label for="edit_confirm_password">Confirm New Password</label>
                        <input type="password" class="form-control" id="edit_confirm_password">
                    </div>
                    <div class="form-group">
                        <label for="edit_comment">Comment</label>
                        <input type="text" class="form-control" id="edit_comment" name="comment">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="edit_disabled" name="disabled"> Disabled
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateUser()">Update User</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced Statistics Cards */
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
    pointer-events: none;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
}

.stats-card.total-users {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stats-card.active-users {
    background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
}

.stats-card.disabled-users {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stats-card.last-updated {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stats-card .stats-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 2.5em;
    color: rgba(255, 255, 255, 0.3);
    z-index: 1;
}

.stats-card .stats-content {
    position: relative;
    z-index: 2;
}

.stats-card .stats-number {
    font-size: 2.5em;
    font-weight: bold;
    color: #ffffff;
    line-height: 1;
    margin-bottom: 8px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.stats-card .stats-label {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.95em;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

/* Table Enhancements */
.table-responsive {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.table thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85em;
    letter-spacing: 0.5px;
}

.table tbody tr:hover {
    background-color: #f8f9ff;
}

/* Button Enhancements */
.btn-group-xs .btn {
    border-radius: 4px;
    margin: 0 1px;
}

/* Header Action Buttons */
.panel-heading .btn-group .btn {
    font-size: 11px;
    padding: 4px 8px;
    line-height: 1.2;
}

.panel-heading .btn-group .btn i {
    font-size: 10px;
    margin-right: 3px;
}

/* Modal Enhancements */
.modal-content {
    border-radius: 10px;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px 10px 0 0;
}

/* Tab Enhancements */
.nav-tabs > li > a {
    border-radius: 8px 8px 0 0;
    margin-right: 2px;
}

.nav-tabs > li.active > a {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}

.tab-content {
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 8px 8px;
    padding: 20px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .stats-card {
        margin-bottom: 15px;
    }
    
    .stats-card .stats-number {
        font-size: 2em;
    }
    
    .stats-card .stats-icon {
        font-size: 2em;
    }
    
    .btn-group-xs {
        display: flex;
        flex-direction: column;
    }
    
    .btn-group-xs .btn {
        margin: 1px 0;
    }
}
</style>

<?php echo '<script'; ?>
>
let currentRouterID = '<?php if ($_smarty_tpl->tpl_vars['default_router']->value) {
echo $_smarty_tpl->tpl_vars['default_router']->value['id'];
}?>';

function changeRouter() {
    const select = document.getElementById('router_select');
    const routerID = select.value;
    
    if (routerID) {
        currentRouterID = routerID;
        const selectedOption = select.options[select.selectedIndex];
        const routerText = selectedOption.text;
        
        document.getElementById('router_info').textContent = routerText;
        
        // Refresh data for new router
        refreshData();
    }
}

function refreshData() {
    if (!currentRouterID) {
        showAlert('danger', 'Please select a router first');
        return;
    }
    
    showLoading(true);
    
    $.post('', {
        action: 'refresh_data',
        router_id: currentRouterID
    })
    .done(function(response) {
        if (response.success) {
            updateStatistics(response.stats);
            updateUsersTable(response.data);
            document.getElementById('last_updated').textContent = response.last_updated;
            showAlert('success', 'Data refreshed successfully');
        } else {
            showAlert('danger', 'Error: ' + response.error);
        }
    })
    .fail(function() {
        showAlert('danger', 'Failed to refresh data. Please try again.');
    })
    .always(function() {
        showLoading(false);
    });
}

function updateStatistics(stats) {
    document.getElementById('total_users').textContent = stats.total;
    document.getElementById('active_users').textContent = stats.active;
    document.getElementById('disabled_users').textContent = stats.disabled;
}

function updateUsersTable(users) {
    const tbody = document.getElementById('users_tbody');
    tbody.innerHTML = '';
    
    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No system users found</td></tr>';
        return;
    }
    
    users.forEach(function(user) {
        const row = document.createElement('tr');
        row.setAttribute('data-username', user.name);
        row.setAttribute('data-group', user.group);
        row.setAttribute('data-status', user.status);
        
        const statusLabel = user.status === 'active' 
            ? '<span class="label label-success"><i class="fa fa-check"></i> Active</span>'
            : '<span class="label label-warning"><i class="fa fa-ban"></i> Disabled</span>';
            
        const actionButtons = 
            '<div class="btn-group btn-group-xs">' +
                '<button type="button" class="btn btn-primary" onclick="editUser(\'' + user.id + '\', \'' + user.name + '\', \'' + user.group + '\', \'' + user.address + '\', \'' + user.comment + '\', ' + user.disabled + ')" title="Edit">' +
                    '<i class="fa fa-edit"></i>' +
                '</button>' +
                (user.status === 'active' 
                    ? '<button type="button" class="btn btn-warning" onclick="disableUser(\'' + user.id + '\', \'' + user.name + '\')" title="Disable"><i class="fa fa-ban"></i></button>'
                    : '<button type="button" class="btn btn-success" onclick="enableUser(\'' + user.id + '\', \'' + user.name + '\')" title="Enable"><i class="fa fa-check"></i></button>'
                ) +
                (user.name !== 'admin' 
                    ? '<button type="button" class="btn btn-danger" onclick="removeUser(\'' + user.id + '\', \'' + user.name + '\')" title="Remove"><i class="fa fa-trash"></i></button>'
                    : ''
                ) +
            '</div>';
        
        row.innerHTML = 
            '<td><strong>' + user.name + '</strong></td>' +
            '<td><span class="label label-default">' + user.group + '</span></td>' +
            '<td><code>' + user.address + '</code></td>' +
            '<td>' + user.last_logged_in + '</td>' +
            '<td>' + statusLabel + '</td>' +
            '<td>' + user.comment + '</td>' +
            '<td>' + actionButtons + '</td>';
        
        tbody.appendChild(row);
    });
}

function filterUsers() {
    const searchTerm = document.getElementById('search_input').value.toLowerCase();
    const statusFilter = document.getElementById('status_filter').value;
    const groupFilter = document.getElementById('group_filter').value;
    
    const rows = document.querySelectorAll('#users_tbody tr');
    
    rows.forEach(function(row) {
        const username = row.getAttribute('data-username') || '';
        const group = row.getAttribute('data-group') || '';
        const status = row.getAttribute('data-status') || '';
        
        const matchesSearch = username.toLowerCase().includes(searchTerm) ||
                            group.toLowerCase().includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        const matchesGroup = !groupFilter || group === groupFilter;
        
        if (matchesSearch && matchesStatus && matchesGroup) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function clearFilters() {
    document.getElementById('search_input').value = '';
    document.getElementById('status_filter').value = '';
    document.getElementById('group_filter').value = '';
    filterUsers();
}

function showAddUserModal() {
    document.getElementById('addUserForm').reset();
    $('#addUserModal').modal('show');
}

function addUser() {
    const password = document.getElementById('add_password').value;
    const confirmPassword = document.getElementById('add_confirm_password').value;
    
    if (password !== confirmPassword) {
        showAlert('danger', 'Passwords do not match');
        return;
    }
    
    if (password.length < 3) {
        showAlert('danger', 'Password must be at least 3 characters long');
        return;
    }
    
    const formData = {
        action: 'add_user',
        router_id: currentRouterID,
        name: document.getElementById('add_name').value,
        group: document.getElementById('add_group').value,
        address: document.getElementById('add_address').value,
        password: password,
        comment: document.getElementById('add_comment').value,
        disabled: document.getElementById('add_disabled').checked ? 'true' : 'false'
    };
    
    showLoading(true);
    
    $.post('', formData)
    .done(function(response) {
        if (response.success) {
            $('#addUserModal').modal('hide');
            showAlert('success', response.message);
            refreshData();
        } else {
            showAlert('danger', 'Error: ' + response.error);
        }
    })
    .fail(function() {
        showAlert('danger', 'Failed to add user. Please try again.');
    })
    .always(function() {
        showLoading(false);
    });
}

function editUser(userID, name, group, address, comment, disabled) {
    document.getElementById('edit_user_id').value = userID;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_group').value = group;
    document.getElementById('edit_address').value = address;
    document.getElementById('edit_comment').value = comment;
    document.getElementById('edit_disabled').checked = disabled;
    document.getElementById('edit_password').value = '';
    document.getElementById('edit_confirm_password').value = '';
    
    $('#editUserModal').modal('show');
}

function updateUser() {
    const password = document.getElementById('edit_password').value;
    const confirmPassword = document.getElementById('edit_confirm_password').value;
    
    if (password && password !== confirmPassword) {
        showAlert('danger', 'Passwords do not match');
        return;
    }
    
    if (password && password.length < 3) {
        showAlert('danger', 'Password must be at least 3 characters long');
        return;
    }
    
    const formData = {
        action: 'edit_user',
        router_id: currentRouterID,
        user_id: document.getElementById('edit_user_id').value,
        name: document.getElementById('edit_name').value,
        group: document.getElementById('edit_group').value,
        address: document.getElementById('edit_address').value,
        password: password,
        comment: document.getElementById('edit_comment').value,
        disabled: document.getElementById('edit_disabled').checked ? 'true' : 'false'
    };
    
    showLoading(true);
    
    $.post('', formData)
    .done(function(response) {
        if (response.success) {
            $('#editUserModal').modal('hide');
            showAlert('success', response.message);
            refreshData();
        } else {
            showAlert('danger', 'Error: ' + response.error);
        }
    })
    .fail(function() {
        showAlert('danger', 'Failed to update user. Please try again.');
    })
    .always(function() {
        showLoading(false);
    });
}

function enableUser(userID, username) {
    if (confirm('Are you sure you want to enable user "' + username + '"?')) {
        showLoading(true);
        
        $.post('', {
            action: 'enable_user',
            router_id: currentRouterID,
            user_id: userID
        })
        .done(function(response) {
            if (response.success) {
                showAlert('success', response.message);
                refreshData();
            } else {
                showAlert('danger', 'Error: ' + response.error);
            }
        })
        .fail(function() {
            showAlert('danger', 'Failed to enable user. Please try again.');
        })
        .always(function() {
            showLoading(false);
        });
    }
}

function disableUser(userID, username) {
    if (confirm('Are you sure you want to disable user "' + username + '"?')) {
        showLoading(true);
        
        $.post('', {
            action: 'disable_user',
            router_id: currentRouterID,
            user_id: userID
        })
        .done(function(response) {
            if (response.success) {
                showAlert('success', response.message);
                refreshData();
            } else {
                showAlert('danger', 'Error: ' + response.error);
            }
        })
        .fail(function() {
            showAlert('danger', 'Failed to disable user. Please try again.');
        })
        .always(function() {
            showLoading(false);
        });
    }
}

function removeUser(userID, username) {
    if (username === 'admin') {
        showAlert('danger', 'Cannot remove admin user for security reasons');
        return;
    }
    
    if (confirm('Are you sure you want to permanently remove user "' + username + '"?\n\nThis action cannot be undone.')) {
        showLoading(true);
        
        $.post('', {
            action: 'remove_user',
            router_id: currentRouterID,
            user_id: userID,
            username: username
        })
        .done(function(response) {
            if (response.success) {
                showAlert('success', response.message);
                refreshData();
            } else {
                showAlert('danger', 'Error: ' + response.error);
            }
        })
        .fail(function() {
            showAlert('danger', 'Failed to remove user. Please try again.');
        })
        .always(function() {
            showLoading(false);
        });
    }
}

function refreshActiveUsers() {
    if (!currentRouterID) {
        showAlert('danger', 'Please select a router first');
        return;
    }
    
    showLoading(true);
    
    $.post('', {
        action: 'get_active_users',
        router_id: currentRouterID
    })
    .done(function(response) {
        if (response.success) {
            updateActiveUsersTable(response.data);
            showAlert('success', 'Active users refreshed successfully');
        } else {
            showAlert('danger', 'Error: ' + response.error);
        }
    })
    .fail(function() {
        showAlert('danger', 'Failed to refresh active users. Please try again.');
    })
    .always(function() {
        showLoading(false);
    });
}

function updateActiveUsersTable(activeUsers) {
    const tbody = document.getElementById('active_users_tbody');
    tbody.innerHTML = '';
    
    if (activeUsers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">No active users found</td></tr>';
        return;
    }
    
    activeUsers.forEach(function(user) {
        const row = document.createElement('tr');
        row.innerHTML = 
            '<td><strong>' + user.name + '</strong></td>' +
            '<td><code>' + user.address + '</code></td>' +
            '<td>' + user.via + '</td>' +
            '<td>' + user.when + '</td>';
        tbody.appendChild(row);
    });
}

function exportCSV() {
    if (!currentRouterID) {
        showAlert('danger', 'Please select a router first');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '';
    
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'export_csv';
    
    const routerInput = document.createElement('input');
    routerInput.type = 'hidden';
    routerInput.name = 'router_id';
    routerInput.value = currentRouterID;
    
    form.appendChild(actionInput);
    form.appendChild(routerInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    showAlert('success', 'CSV export started...');
}

function showAlert(type, message) {
    const alertHTML = 
        '<div class="alert alert-' + type + ' alert-dismissible" role="alert" style="position: fixed; top: 70px; right: 20px; z-index: 9999; min-width: 300px;">' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            message +
        '</div>';
    
    $('body').append(alertHTML);
    
    setTimeout(function() {
        $('.alert').fadeOut(500, function() {
            $(this).remove();
        });
    }, 3000);
}

function showLoading(show) {
    if (show) {
        $('body').append('<div id="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9998; display: flex; align-items: center; justify-content: center;"><div style="background: white; padding: 20px; border-radius: 8px;"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div></div>');
    } else {
        $('#loading-overlay').remove();
    }
}

// Initialize page
$(document).ready(function() {
    // Auto-refresh every 30 seconds if enabled
    // setInterval(refreshData, 30000);
});
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
