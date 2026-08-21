<?php
/* Smarty version 4.5.3, created on 2026-08-21 13:35:44
  from '/var/www/html/isp/ui/ui/reports-period-view.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_6a882a000fd557_40942877',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '97dadd8afa60d268e91227e4a80b9993c886ebbe' => 
    array (
      0 => '/var/www/html/isp/ui/ui/reports-period-view.tpl',
      1 => 1787308366,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:sections/header.tpl' => 1,
    'file:sections/footer.tpl' => 1,
  ),
),false)) {
function content_6a882a000fd557_40942877 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<!-- reports-period-view -->

<style>
.pv-body { min-height:100vh; padding:2rem 1rem; }
.pv-wrap { max-width:1280px; margin:0 auto; }
.pv-card { background:#fff; border:1px solid #e2e8f0; border-radius:18px; box-shadow:0 1px 3px rgba(15,23,42,.06), 0 14px 34px -14px rgba(15,23,42,.16); overflow:hidden; }
.pv-hero { background:linear-gradient(135deg,#4f46e5 0%,#4338ca 55%,#312e81 100%); padding:1.6rem 2rem; color:#fff; }
.pv-table { width:100%; border-collapse:separate; border-spacing:0; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; margin:0; }
.pv-table thead th { background:#f8fafc; color:#475569; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid #e2e8f0; padding:.8rem 1rem; white-space:nowrap; }
.pv-table tbody td { padding:.75rem 1rem; border-bottom:1px solid #f1f5f9; font-size:.875rem; color:#334155; vertical-align:middle; }
.pv-table tbody tr:last-child td { border-bottom:none; }
.pv-table tbody tr:hover td { background:#f8fafc; }
.btn-export { display:inline-flex; align-items:center; gap:.45rem; padding:.55rem .95rem; border:none; border-radius:10px; font-size:.8rem; font-weight:600; color:#fff; cursor:pointer; transition:all .15s; }
.btn-export.print { background:#0ea5e9; } .btn-export.print:hover { background:#0284c7; }
.btn-export.pdf { background:#ef4444; } .btn-export.pdf:hover { background:#dc2626; }
.btn-back { display:inline-flex; align-items:center; gap:.45rem; padding:.55rem .95rem; border:1.5px solid #e2e8f0; border-radius:10px; font-size:.8rem; font-weight:600; color:#475569; background:#fff; cursor:pointer; text-decoration:none; transition:all .15s; }
.btn-back:hover { background:#f8fafc; }
.total-box { background:linear-gradient(135deg,#ecfdf5,#d1fae5); border:1px solid #a7f3d0; border-radius:16px; padding:1.25rem 1.75rem; }
</style>

<div class="pv-body">
  <div class="pv-wrap">
    <div class="pv-card">
      <div class="pv-hero">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
          <div style="display:flex; align-items:center; gap:1rem;">
            <div style="width:48px; height:48px; border-radius:14px; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; font-size:1.4rem;">&#128202;</div>
            <div>
              <h1 style="font-size:1.35rem; font-weight:700; color:#fff; margin:0;"><?php echo Lang::T('Period Report');?>
</h1>
              <p style="margin:.25rem 0 0 0; color:rgba(255,255,255,.8); font-size:.85rem;">
                <?php if ($_smarty_tpl->tpl_vars['router']->value) {
echo $_smarty_tpl->tpl_vars['router']->value;?>
 &mdash; <?php }
echo $_smarty_tpl->tpl_vars['stype']->value;?>

                [<?php echo date($_smarty_tpl->tpl_vars['_c']->value['date_format'],strtotime($_smarty_tpl->tpl_vars['fdate']->value));?>
 &rarr; <?php echo date($_smarty_tpl->tpl_vars['_c']->value['date_format'],strtotime($_smarty_tpl->tpl_vars['tdate']->value));?>
]
              </p>
            </div>
          </div>
          <div style="display:flex; gap:.6rem; flex-wrap:wrap;">
            <a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
reports/by-period" class="btn-back">&#8592; <?php echo Lang::T('Back to Filters');?>
</a>
            <form method="post" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
export/print-by-period" target="_blank" style="display:inline;">
              <input type="hidden" name="fdate" value="<?php echo $_smarty_tpl->tpl_vars['fdate']->value;?>
">
              <input type="hidden" name="tdate" value="<?php echo $_smarty_tpl->tpl_vars['tdate']->value;?>
">
              <input type="hidden" name="stype" value="<?php echo $_smarty_tpl->tpl_vars['stype']->value;?>
">
              <input type="hidden" name="router" value="<?php echo $_smarty_tpl->tpl_vars['router']->value;?>
">
              <button type="submit" class="btn-export print">&#128424; <?php echo Lang::T('Print');?>
</button>
            </form>
            <form method="post" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
export/pdf-by-period" target="_blank" style="display:inline;">
              <input type="hidden" name="fdate" value="<?php echo $_smarty_tpl->tpl_vars['fdate']->value;?>
">
              <input type="hidden" name="tdate" value="<?php echo $_smarty_tpl->tpl_vars['tdate']->value;?>
">
              <input type="hidden" name="stype" value="<?php echo $_smarty_tpl->tpl_vars['stype']->value;?>
">
              <input type="hidden" name="router" value="<?php echo $_smarty_tpl->tpl_vars['router']->value;?>
">
              <button type="submit" class="btn-export pdf">&#128196; <?php echo Lang::T('Export to PDF');?>
</button>
            </form>
          </div>
        </div>
      </div>

      <div style="padding:1.5rem 2rem;">
        <div style="display:flex; justify-content:flex-end; margin-bottom:1.5rem;">
          <div class="total-box">
            <div style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#047857;"><?php echo Lang::T('Total Income');?>
</div>
            <div style="font-size:1.9rem; font-weight:800; color:#065f46; margin-top:.15rem;"><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['dr']->value);?>
</div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table pv-table">
            <thead>
              <tr>
                <th><?php echo Lang::T('Username');?>
</th>
                <th><?php echo Lang::T('Type');?>
</th>
                <th><?php echo Lang::T('Plan Name');?>
</th>
                <th class="text-right"><?php echo Lang::T('Plan Price');?>
</th>
                <th><?php echo Lang::T('Created On');?>
</th>
                <th><?php echo Lang::T('Expires On');?>
</th>
                <th><?php echo Lang::T('Method');?>
</th>
                <th><?php echo Lang::T('Routers');?>
</th>
              </tr>
            </thead>
            <tbody>
              <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['d']->value, 'ds');
$_smarty_tpl->tpl_vars['ds']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['ds']->value) {
$_smarty_tpl->tpl_vars['ds']->do_else = false;
?>
              <tr>
                <td style="font-weight:600; color:#1e293b;"><?php echo $_smarty_tpl->tpl_vars['ds']->value['username'];?>
</td>
                <td><span style="display:inline-flex; padding:.2rem .55rem; border-radius:9999px; font-size:.72rem; font-weight:600; background:#eef2ff; color:#4338ca;"><?php echo $_smarty_tpl->tpl_vars['ds']->value['type'];?>
</span></td>
                <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['plan_name'];?>
</td>
                <td class="text-right" style="font-weight:600; color:#047857;"><?php echo Lang::moneyFormat($_smarty_tpl->tpl_vars['ds']->value['price']);?>
</td>
                <td><?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['ds']->value['recharged_on'],$_smarty_tpl->tpl_vars['ds']->value['recharged_time']);?>
</td>
                <td><?php echo Lang::dateAndTimeFormat($_smarty_tpl->tpl_vars['ds']->value['expiration'],$_smarty_tpl->tpl_vars['ds']->value['time']);?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['method'];?>
</td>
                <td><?php echo $_smarty_tpl->tpl_vars['ds']->value['routers'];?>
</td>
              </tr>
              <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
