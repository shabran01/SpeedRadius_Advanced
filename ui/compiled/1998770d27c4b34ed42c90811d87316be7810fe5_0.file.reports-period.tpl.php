<?php
/* Smarty version 4.5.3, created on 2026-08-21 13:35:22
  from '/var/www/html/isp/ui/ui/reports-period.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_6a8829eae93c03_64568026',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1998770d27c4b34ed42c90811d87316be7810fe5' => 
    array (
      0 => '/var/www/html/isp/ui/ui/reports-period.tpl',
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
function content_6a8829eae93c03_64568026 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<!-- reports-period -->

<style>
.rp-body { min-height: 100vh; padding: 2rem 1rem; }
.rp-wrap { max-width: 1080px; margin: 0 auto; }
.rp-card { background:#fff; border:1px solid #e2e8f0; border-radius:18px; box-shadow:0 1px 3px rgba(15,23,42,.06), 0 14px 34px -14px rgba(15,23,42,.16); overflow:hidden; }
.rp-hero { background: linear-gradient(135deg,#4f46e5 0%,#4338ca 55%,#312e81 100%); padding:1.75rem 2rem; color:#fff; }
.rp-label { display:block; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:.45rem; }
.rp-input { display:block; width:100%; padding:.72rem .9rem; border:1.5px solid #e2e8f0; border-radius:12px; background:#f8fafc; font-size:.9rem; color:#1e293b; outline:none; transition:all .15s; }
.rp-input:focus { border-color:#4f46e5; background:#fff; box-shadow:0 0 0 3px rgba(79,70,229,.12); }
.rp-select { display:block; width:100%; padding:.72rem .9rem; border:1.5px solid #e2e8f0; border-radius:12px; background:#f8fafc; font-size:.9rem; color:#1e293b; outline:none; cursor:pointer; transition:all .15s; }
.rp-select:focus { border-color:#4f46e5; background:#fff; box-shadow:0 0 0 3px rgba(79,70,229,.12); }
.rp-chip { display:inline-flex; align-items:center; padding:.35rem .75rem; border:1px solid #e2e8f0; border-radius:9999px; font-size:.75rem; font-weight:600; color:#475569; background:#fff; cursor:pointer; transition:all .15s; user-select:none; }
.rp-chip:hover { border-color:#c7d2fe; color:#4f46e5; background:#eef2ff; }
.btn-generate { display:inline-flex; align-items:center; gap:.5rem; padding:.8rem 1.7rem; border:none; border-radius:12px; font-size:.92rem; font-weight:600; color:#fff; background:linear-gradient(135deg,#4f46e5,#4338ca); cursor:pointer; box-shadow:0 10px 22px -8px rgba(79,70,229,.55); transition:all .2s; }
.btn-generate:hover { transform:translateY(-1px); box-shadow:0 14px 28px -8px rgba(79,70,229,.65); }
.btn-reset { display:inline-flex; align-items:center; gap:.5rem; padding:.8rem 1.4rem; border:1.5px solid #e2e8f0; border-radius:12px; font-size:.9rem; font-weight:600; color:#475569; background:#fff; cursor:pointer; transition:all .15s; }
.btn-reset:hover { background:#f8fafc; border-color:#cbd5e1; }
</style>

<div class="rp-body">
  <div class="rp-wrap">
    <!-- Hero header -->
    <div class="rp-card">
      <div class="rp-hero">
        <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap; justify-content:space-between;">
          <div style="display:flex; align-items:center; gap:1rem;">
            <div style="width:52px; height:52px; border-radius:14px; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; font-size:1.5rem;">&#128202;</div>
            <div>
              <h1 style="font-size:1.5rem; font-weight:700; color:#fff; margin:0;">Period Reports</h1>
              <p style="margin:.25rem 0 0 0; color:rgba(255,255,255,.75); font-size:.85rem;">Revenue &amp; transactions between two dates</p>
            </div>
          </div>
          <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
            <button type="button" class="rp-chip" onclick="setRange('today')" style="border-color:rgba(255,255,255,.35); color:#fff; background:rgba(255,255,255,.1);">Today</button>
            <button type="button" class="rp-chip" onclick="setRange(7)" style="border-color:rgba(255,255,255,.35); color:#fff; background:rgba(255,255,255,.1);">7 Days</button>
            <button type="button" class="rp-chip" onclick="setRange(30)" style="border-color:rgba(255,255,255,.35); color:#fff; background:rgba(255,255,255,.1);">30 Days</button>
            <button type="button" class="rp-chip" onclick="setRange('month')" style="border-color:rgba(255,255,255,.35); color:#fff; background:rgba(255,255,255,.1);">This Month</button>
          </div>
        </div>
      </div>

      <!-- Form -->
      <form method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
reports/period-view" style="padding:2rem;">
        <div class="row" style="margin:0 -0.5rem;">
          <div class="col-md-6" style="padding:0 0.5rem;">
            <label class="rp-label"><?php echo Lang::T('From Date');?>
</label>
            <input type="date" class="rp-input" value="<?php echo $_smarty_tpl->tpl_vars['tdate']->value;?>
" name="fdate" id="fdate" style="margin-bottom:1.25rem;">
          </div>
          <div class="col-md-6" style="padding:0 0.5rem;">
            <label class="rp-label"><?php echo Lang::T('To Date');?>
</label>
            <input type="date" class="rp-input" value="<?php echo $_smarty_tpl->tpl_vars['mdate']->value;?>
" name="tdate" id="tdate" style="margin-bottom:1.25rem;">
          </div>
        </div>

        <div class="row" style="margin:0 -0.5rem;">
          <div class="col-md-6" style="padding:0 0.5rem;">
            <label class="rp-label"><?php echo Lang::T('Type');?>
</label>
            <select class="rp-select" id="stype" name="stype" style="margin-bottom:1.5rem;">
              <option value="" selected=""><?php echo Lang::T('All Transactions');?>
</option>
              <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['types']->value, 'tp');
$_smarty_tpl->tpl_vars['tp']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['tp']->value) {
$_smarty_tpl->tpl_vars['tp']->do_else = false;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['tp']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['tp']->value;?>
</option>
              <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </select>
          </div>
          <div class="col-md-6" style="padding:0 0.5rem;">
            <label class="rp-label"><?php echo Lang::T('Router');?>
</label>
            <select class="rp-select" id="router" name="router" style="margin-bottom:1.5rem;">
              <option value="" selected=""><?php echo Lang::T('All Routers');?>
</option>
              <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['routers']->value, 'router');
$_smarty_tpl->tpl_vars['router']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['router']->value) {
$_smarty_tpl->tpl_vars['router']->do_else = false;
?>
                <option value="<?php echo $_smarty_tpl->tpl_vars['router']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['router']->value;?>
</option>
              <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </select>
          </div>
        </div>

        <div style="display:flex; gap:.75rem; align-items:center; flex-wrap:wrap; margin-top:.5rem;">
          <button type="submit" id="submit" class="btn-generate">
            &#128269; <?php echo Lang::T('Generate Report');?>

          </button>
          <button type="button" class="btn-reset" onclick="resetDates()">&#8635; <?php echo Lang::T('Reset');?>
</button>
        </div>
      </form>
    </div>

    <p style="text-align:center; color:#94a3b8; font-size:.78rem; margin-top:1.25rem;">Results open a printable report that you can also export to PDF or print.</p>
  </div>
</div>

<?php echo '<script'; ?>
>

  function toDateStr(dt) {
    var m = String(dt.getMonth() + 1).padStart(2, '0');
    var d = String(dt.getDate()).padStart(2, '0');
    return dt.getFullYear() + '-' + m + '-' + d;
  }
  function setRange(v) {
    var today = new Date();
    var f = new Date(today);
    if (v === 'today') {
      // from = today
    } else if (v === 'month') {
      f = new Date(today.getFullYear(), today.getMonth(), 1);
    } else {
      f.setDate(today.getDate() - v);
    }
    document.getElementById('fdate').value = toDateStr(f);
    document.getElementById('tdate').value = toDateStr(today);
  }
  function resetDates() {
    var today = new Date();
    var f = new Date(today);
    f.setDate(today.getDate() - 30);
    document.getElementById('fdate').value = toDateStr(f);
    document.getElementById('tdate').value = toDateStr(today);
  }

<?php echo '</script'; ?>
>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
