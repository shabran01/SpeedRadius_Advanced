<?php
/* Smarty version 4.5.3, created on 2025-08-17 23:16:30
  from '/var/www/html/ISP/ui/ui/reports-activation.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a2389edcad23_96777276',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '24468ef237afbe2d96cdfcb5b6c94f55fd1d101f' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/reports-activation.tpl',
      1 => 1754916133,
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
function content_68a2389edcad23_96777276 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<style>
    /* Modern Container Styles */
    .reports-container {
        background: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        margin: 1.5rem;
        overflow: hidden;
    }

    /* Header Styles */
    .section-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .header-icon {
        font-size: 2rem;
        background: rgba(255, 255, 255, 0.2);
        padding: 1rem;
        border-radius: 1rem;
    }

    .header-text h1 {
        font-size: 1.875rem;
        font-weight: 600;
        margin: 0;
        line-height: 1.2;
    }

    .header-text p {
        margin: 0.5rem 0 0;
        opacity: 0.9;
        font-size: 0.975rem;
    }

    /* Search Styles */
    .header-search {
        flex: 1;
        max-width: 500px;
    }

    .search-input-group {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        color: #64748b;
        pointer-events: none;
    }

    .search-input-group input {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 2.75rem;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 0.75rem;
        color: white;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .search-input-group input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .search-input-group input:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        outline: none;
    }

    .search-button {
        position: absolute;
        right: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        border-radius: 0.5rem;
        color: white;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }

    .search-button:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* Table Styles */
    .reports-content {
        padding: 0 1.5rem 1.5rem;
    }

    .table-container {
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        white-space: nowrap;
    }

    .modern-table th {
        background: #f8fafc;
        padding: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #475569;
        text-align: left;
        border-bottom: 2px solid #e2e8f0;
    }

    .modern-table td {
        padding: 1rem;
        color: #1f2937;
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.2s;
    }

    .table-row {
        transition: all 0.2s;
    }

    .table-row:hover {
        background-color: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    /* Cell Styles */
    .invoice-cell {
        cursor: pointer;
    }

    .invoice-number {
        font-family: 'SF Mono', 'Roboto Mono', monospace;
        color: #6366f1;
        font-weight: 500;
    }

    .username-cell {
        cursor: pointer;
    }

    .username-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.75rem;
        background: #f1f5f9;
        border-radius: 9999px;
        color: #475569;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .username-tag:hover {
        background: #e2e8f0;
        color: #1f2937;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .header-content {
            flex-direction: column;
            align-items: stretch;
        }

        .header-search {
            max-width: none;
        }
    }

    @media (max-width: 768px) {
        .modern-table {
            display: block;
            overflow-x: auto;
        }

        .search-button span {
            display: none;
        }
    }

    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .table-row {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>

<div class="reports-container">
    <div class="section-header">
        <div class="header-content">
            <div class="header-left">
                <i class="fa fa-chart-line header-icon"></i>
                <div class="header-text">
                    <h1>Activation Reports</h1>
                    <p>Track user activations and transactions</p>
                </div>
            </div>
            <div class="header-search">
                <form id="site-search" method="post" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
reports/activation">
                    <div class="search-input-group">
                        <i class="fa fa-search search-icon"></i>
                        <input type="text" name="q" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['q']->value;?>
"
                            placeholder="Search by invoice number...">
                        <button type="submit" class="search-button">
                            <i class="fa fa-search"></i>
                            <span><?php echo Lang::T('Search');?>
</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="reports-content">
                <div class="table-container">
                    <table id="datatable" class="modern-table">
                        <thead>
                            <tr>
                                <th><?php echo Lang::T('Invoice');?>
</th>
                                <th><?php echo Lang::T('Username');?>
</th>
                                <th><?php echo Lang::T('Plan Name');?>
</th>
                                <th><?php echo Lang::T('Plan Price');?>
</th>
                                <th><?php echo Lang::T('Type');?>
</th>
                                <th><?php echo Lang::T('Created On');?>
</th>
                                <th><?php echo Lang::T('Expires On');?>
</th>
                                <th><?php echo Lang::T('Method');?>
</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['activation']->value, 'ds');
$_smarty_tpl->tpl_vars['ds']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ds']->value) {
$_smarty_tpl->tpl_vars['ds']->do_else = false;
?>
                                <tr class="table-row">
                                    <td class="invoice-cell" onclick="window.location.href = '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
plan/view/<?php echo $_smarty_tpl->tpl_vars['ds']->value['id'];?>
'">
                                        <div class="invoice-number"><?php echo $_smarty_tpl->tpl_vars['ds']->value['invoice'];?>
</div>
                                    </td>
                                    <td class="username-cell" onclick="window.location.href = '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/viewu/<?php echo $_smarty_tpl->tpl_vars['ds']->value['username'];?>
'">
                                        <div class="username-tag">
                                            <i class="fa fa-user"></i>
                                            <span><?php echo $_smarty_tpl->tpl_vars['ds']->value['username'];?>
</span>
                                        </div>
                                    </td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['plan_name'];?>
</td>
                                    <td><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['ds']->value['price']);?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['type'];?>
</td>
                                    <td class="text-success">
                                        <?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['ds']->value['recharged_on'],$_smarty_tpl->tpl_vars['ds']->value['recharged_time']);?>

                                    </td>
                                    <td class="text-danger"><?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['ds']->value['expiration'],$_smarty_tpl->tpl_vars['ds']->value['time']);?>
</td>
                                    <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['method'];?>
</td>
                                </tr>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </tbody>
                    </table>
                </div>
                <?php $_smarty_tpl->_subTemplateRender("file:pagination.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
            </div>
        </div>
    </div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
