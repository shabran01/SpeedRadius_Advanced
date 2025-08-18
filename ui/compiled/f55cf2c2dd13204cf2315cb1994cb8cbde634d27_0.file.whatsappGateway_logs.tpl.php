<?php
/* Smarty version 4.5.3, created on 2025-08-17 10:23:16
  from '/var/www/html/ISP/system/plugin/ui/whatsappGateway_logs.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a18364ce3114_20881120',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f55cf2c2dd13204cf2315cb1994cb8cbde634d27' => 
    array (
      0 => '/var/www/html/ISP/system/plugin/ui/whatsappGateway_logs.tpl',
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
function content_68a18364ce3114_20881120 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/html/ISP/system/vendor/smarty/smarty/libs/plugins/modifier.count.php','function'=>'smarty_modifier_count',),));
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<style>
.delete-controls {
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.btn-delete-all {
    background: linear-gradient(145deg, #dc3545, #c82333);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
    margin-right: 10px;
    transition: all 0.2s ease;
}

.btn-delete-all:hover {
    background: linear-gradient(145deg, #c82333, #a71e2a);
    transform: translateY(-1px);
}

.btn-delete-selected {
    background: linear-gradient(145deg, #ffc107, #e0a800);
    color: #212529;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
    margin-right: 10px;
    transition: all 0.2s ease;
}

.btn-delete-selected:hover {
    background: linear-gradient(145deg, #e0a800, #d39e00);
    transform: translateY(-1px);
}

.btn-delete-selected:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.select-all-container {
    margin-bottom: 10px;
}

.checkbox-column {
    width: 40px;
    text-align: center;
}

.records-per-page {
    margin-bottom: 15px;
}

.records-per-page select {
    padding: 5px 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
    margin-left: 8px;
}

.records-info {
    color: #666;
    font-size: 13px;
    margin-top: 5px;
}
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">WhatsApp Message Logs</div>
            <div class="panel-body">
                <form method="post" id="logsForm">
                    <div class="delete-controls">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="records-per-page">
                                    <label for="perPageSelect">
                                        <i class="glyphicon glyphicon-list"></i> Records per page:
                                        <select id="perPageSelect" onchange="changePerPage()">
                                            <option value="10" <?php if ($_smarty_tpl->tpl_vars['per_page']->value == 10) {?>selected<?php }?>>10</option>
                                            <option value="50" <?php if ($_smarty_tpl->tpl_vars['per_page']->value == 50) {?>selected<?php }?>>50</option>
                                            <option value="100" <?php if ($_smarty_tpl->tpl_vars['per_page']->value == 100) {?>selected<?php }?>>100</option>
                                            <option value="150" <?php if ($_smarty_tpl->tpl_vars['per_page']->value == 150) {?>selected<?php }?>>150</option>
                                            <option value="200" <?php if ($_smarty_tpl->tpl_vars['per_page']->value == 200) {?>selected<?php }?>>200</option>
                                            <option value="500" <?php if ($_smarty_tpl->tpl_vars['per_page']->value == 500) {?>selected<?php }?>>500</option>
                                        </select>
                                    </label>
                                    <div class="records-info">
                                        Showing <?php if (smarty_modifier_count($_smarty_tpl->tpl_vars['logs']->value) > 0) {
echo ($_smarty_tpl->tpl_vars['page']->value-1)*$_smarty_tpl->tpl_vars['per_page']->value+1;?>
 to <?php echo ($_smarty_tpl->tpl_vars['page']->value-1)*$_smarty_tpl->tpl_vars['per_page']->value+smarty_modifier_count($_smarty_tpl->tpl_vars['logs']->value);
} else { ?>0<?php }?> of <?php echo $_smarty_tpl->tpl_vars['total_logs']->value;?>
 records
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="select-all-container">
                                    <label>
                                        <input type="checkbox" id="selectAll"> Select All Logs
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <button type="button" class="btn-delete-selected" id="deleteSelectedBtn" disabled onclick="deleteSelected()">
                                    <i class="glyphicon glyphicon-trash"></i> Delete Selected
                                </button>
                                <button type="button" class="btn-delete-all" onclick="deleteAll()">
                                    <i class="glyphicon glyphicon-remove"></i> Delete All Logs
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="checkbox-column">
                                        <input type="checkbox" id="selectAllHeader">
                                    </th>
                                    <th>ID</th>
                                    <th>Phone Number</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Sender</th>
                                    <th>Response</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['logs']->value, 'log');
$_smarty_tpl->tpl_vars['log']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['log']->value) {
$_smarty_tpl->tpl_vars['log']->do_else = false;
?>
                                    <tr>
                                        <td class="checkbox-column">
                                            <input type="checkbox" name="selected_logs[]" value="<?php echo $_smarty_tpl->tpl_vars['log']->value['id'];?>
" class="log-checkbox">
                                        </td>
                                        <td><?php echo $_smarty_tpl->tpl_vars['log']->value['id'];?>
</td>
                                        <td><?php echo $_smarty_tpl->tpl_vars['log']->value['phone_number'];?>
</td>
                                        <td><?php echo $_smarty_tpl->tpl_vars['log']->value['message'];?>
</td>
                                        <td>
                                            <?php if ($_smarty_tpl->tpl_vars['log']->value['status'] == 'delivered') {?>
                                                <span class="label label-success"><?php echo $_smarty_tpl->tpl_vars['log']->value['status'];?>
</span>
                                            <?php } elseif ($_smarty_tpl->tpl_vars['log']->value['status'] == 'failed') {?>
                                                <span class="label label-danger"><?php echo $_smarty_tpl->tpl_vars['log']->value['status'];?>
</span>
                                            <?php } else { ?>
                                                <span class="label label-warning"><?php echo $_smarty_tpl->tpl_vars['log']->value['status'];?>
</span>
                                            <?php }?>
                                        </td>
                                        <td><?php echo $_smarty_tpl->tpl_vars['log']->value['sender'];?>
</td>
                                        <td><small><?php echo $_smarty_tpl->tpl_vars['log']->value['response'];?>
</small></td>
                                        <td><?php echo $_smarty_tpl->tpl_vars['log']->value['created_at'];?>
</td>
                                    </tr>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </tbody>
                        </table>
                    </div>
                </form>

                <?php if ($_smarty_tpl->tpl_vars['total_pages']->value > 1) {?>
                <ul class="pagination">
                    <?php if ($_smarty_tpl->tpl_vars['page']->value > 1) {?>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plugin/whatsappGateway_logs&page=<?php echo $_smarty_tpl->tpl_vars['page']->value-1;?>
&per_page=<?php echo $_smarty_tpl->tpl_vars['per_page']->value;?>
">Previous</a></li>
                    <?php }?>
                    
                    <?php
$_smarty_tpl->tpl_vars['p'] = new Smarty_Variable(null, $_smarty_tpl->isRenderingCache);$_smarty_tpl->tpl_vars['p']->step = 1;$_smarty_tpl->tpl_vars['p']->total = (int) ceil(($_smarty_tpl->tpl_vars['p']->step > 0 ? $_smarty_tpl->tpl_vars['total_pages']->value+1 - (1) : 1-($_smarty_tpl->tpl_vars['total_pages']->value)+1)/abs($_smarty_tpl->tpl_vars['p']->step));
if ($_smarty_tpl->tpl_vars['p']->total > 0) {
for ($_smarty_tpl->tpl_vars['p']->value = 1, $_smarty_tpl->tpl_vars['p']->iteration = 1;$_smarty_tpl->tpl_vars['p']->iteration <= $_smarty_tpl->tpl_vars['p']->total;$_smarty_tpl->tpl_vars['p']->value += $_smarty_tpl->tpl_vars['p']->step, $_smarty_tpl->tpl_vars['p']->iteration++) {
$_smarty_tpl->tpl_vars['p']->first = $_smarty_tpl->tpl_vars['p']->iteration === 1;$_smarty_tpl->tpl_vars['p']->last = $_smarty_tpl->tpl_vars['p']->iteration === $_smarty_tpl->tpl_vars['p']->total;?>
                        <li <?php if ($_smarty_tpl->tpl_vars['p']->value == $_smarty_tpl->tpl_vars['page']->value) {?>class="active"<?php }?>>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plugin/whatsappGateway_logs&page=<?php echo $_smarty_tpl->tpl_vars['p']->value;?>
&per_page=<?php echo $_smarty_tpl->tpl_vars['per_page']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['p']->value;?>
</a>
                        </li>
                    <?php }
}
?>
                    
                    <?php if ($_smarty_tpl->tpl_vars['page']->value < $_smarty_tpl->tpl_vars['total_pages']->value) {?>
                        <li><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plugin/whatsappGateway_logs&page=<?php echo $_smarty_tpl->tpl_vars['page']->value+1;?>
&per_page=<?php echo $_smarty_tpl->tpl_vars['per_page']->value;?>
">Next</a></li>
                    <?php }?>
                </ul>
                <?php }?>
            </div>
        </div>
    </div>
</div>

<?php echo '<script'; ?>
>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllMain = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const logCheckboxes = document.querySelectorAll('.log-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    
    // Sync both "Select All" checkboxes
    function syncSelectAll() {
        const checkedCount = document.querySelectorAll('.log-checkbox:checked').length;
        const allChecked = checkedCount === logCheckboxes.length && logCheckboxes.length > 0;
        
        selectAllMain.checked = allChecked;
        selectAllHeader.checked = allChecked;
        
        // Enable/disable delete selected button
        deleteSelectedBtn.disabled = checkedCount === 0;
    }
    
    // Handle main "Select All" checkbox
    selectAllMain.addEventListener('change', function() {
        logCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        syncSelectAll();
    });
    
    // Handle header "Select All" checkbox
    selectAllHeader.addEventListener('change', function() {
        logCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        syncSelectAll();
    });
    
    // Handle individual checkboxes
    logCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', syncSelectAll);
    });
    
    // Initial state
    syncSelectAll();
});

function deleteSelected() {
    const checkedBoxes = document.querySelectorAll('.log-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select at least one log to delete.');
        return;
    }
    
    if (confirm('Are you sure you want to delete ' + checkedBoxes.length + ' selected log(s)? This action cannot be undone.')) {
        const form = document.getElementById('logsForm');
        const deleteInput = document.createElement('input');
        deleteInput.type = 'hidden';
        deleteInput.name = 'delete_selected';
        deleteInput.value = '1';
        form.appendChild(deleteInput);
        form.submit();
    }
}

function deleteAll() {
    if (confirm('Are you sure you want to delete ALL WhatsApp logs? This action cannot be undone and will permanently remove all message logs.')) {
        if (confirm('This is your final warning. Are you absolutely sure you want to delete ALL logs?')) {
            const form = document.getElementById('logsForm');
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'delete_all';
            deleteInput.value = '1';
            form.appendChild(deleteInput);
            form.submit();
        }
    }
}

function changePerPage() {
    const perPageSelect = document.getElementById('perPageSelect');
    const perPage = perPageSelect.value;
    const currentUrl = new URL(window.location.href);
    
    // Update the per_page parameter
    currentUrl.searchParams.set('per_page', perPage);
    // Reset to page 1 when changing per page
    currentUrl.searchParams.set('page', '1');
    
    // Redirect to the new URL
    window.location.href = currentUrl.toString();
}
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
