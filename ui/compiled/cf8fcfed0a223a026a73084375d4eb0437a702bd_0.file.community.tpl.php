<?php
/* Smarty version 4.5.3, created on 2026-08-21 13:47:13
  from '/var/www/html/isp/ui/ui/community.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_6a882cb1a532e2_53601297',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cf8fcfed0a223a026a73084375d4eb0437a702bd' => 
    array (
      0 => '/var/www/html/isp/ui/ui/community.tpl',
      1 => 1787255105,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:sections/header.tpl' => 1,
    'file:sections/footer.tpl' => 1,
  ),
),false)) {
function content_6a882cb1a532e2_53601297 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<style>
    .cm-page { background:radial-gradient(1200px 600px at 50% -10%, #065f46 0%, #022c22 55%, #022c22 100%); min-height:100vh; padding:22px 14px 36px; }
    .cm-wrap { max-width:1000px; margin:0 auto; }
    .cm-card { background:rgba(17,24,39,.92); border:1px solid rgba(255,255,255,.08); border-radius:16px; overflow:hidden; transition:transform .18s ease, box-shadow .18s ease; display:flex; flex-direction:column; }
    .cm-card:hover { transform:translateY(-2px); box-shadow:0 16px 40px -12px rgba(0,0,0,.55); border-color:rgba(52,211,153,.4); }
    .cm-head { display:flex; align-items:center; gap:11px; padding:14px 18px; border-bottom:1px solid rgba(255,255,255,.06); }
    .cm-icon { width:40px; height:40px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
    .cm-title { font-size:.98rem; font-weight:700; color:#f1f5f9; }
    .cm-sub { font-size:.74rem; color:#94a3b8; }
    .cm-body { padding:14px 18px; font-size:.86rem; color:#cbd5e1; line-height:1.6; flex:1; }
    .cm-body b { color:#fff; }
    .cm-body a { color:#6ee7b7; font-weight:600; text-decoration:none; }
    .cm-body a:hover { text-decoration:underline; }
    .cm-stats { display:grid; grid-template-columns:1fr 1fr; gap:10px; padding:0 18px 14px; }
    .cm-stat { background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.07); border-radius:11px; padding:10px 12px; }
    .cm-stat-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#7e95c4; margin-bottom:3px; }
    .cm-stat-value { font-size:1.05rem; font-weight:800; color:#fff; }
    .cm-stat-value.cur { color:#6ee7b7; }
    .cm-stat-value.latest { color:#6ee7b7; }
    .cm-foot { display:flex; flex-wrap:wrap; gap:7px; padding:12px 18px; border-top:1px solid rgba(255,255,255,.06); background:rgba(255,255,255,.02); }
    .cm-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 13px; border-radius:9px; font-size:.78rem; font-weight:600; text-decoration:none !important; transition:all .15s; white-space:nowrap; border:1px solid transparent; }
    .cm-btn:hover { transform:translateY(-1px); filter:brightness(1.08); }
    .cm-btn-primary { background:linear-gradient(135deg,#0d9488,#0f766e); color:#fff; box-shadow:0 2px 8px rgba(13,148,136,.35); }
    .cm-btn-emerald { background:linear-gradient(135deg,#10b981,#059669); color:#fff; }
    .cm-btn-amber { background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; }
    .cm-btn-whatsapp { background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff; }
    .cm-btn-blue { background:linear-gradient(135deg,#3b82f6,#2563eb); color:#fff; }
    .cm-btn-ghost { background:rgba(255,255,255,.06); color:#cbd5e1; border-color:rgba(255,255,255,.12); }
    .cm-btn-ghost:hover { background:rgba(255,255,255,.12); color:#fff; }
    .cm-table { width:100%; border-collapse:collapse; }
    .cm-table td { padding:8px 0; border-bottom:1px solid rgba(255,255,255,.06); font-size:.84rem; }
    .cm-table td:first-child { color:#94a3b8; font-weight:600; width:120px; }
    .cm-table td:last-child { color:#e2e8f0; text-align:right; font-weight:600; }
    .cm-hero { text-align:center; padding:26px 18px; border-radius:16px; margin-bottom:16px; background:linear-gradient(135deg,#059669,#10b981 55%,#34d399); color:#fff; box-shadow:0 18px 45px -14px rgba(16,185,129,.5); position:relative; overflow:hidden; }
    .cm-hero::after { content:''; position:absolute; inset:0; background:radial-gradient(circle at 80% 0%, rgba(255,255,255,.22), transparent 42%); pointer-events:none; }
    .cm-hero-icon { width:52px; height:52px; border-radius:14px; background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; font-size:1.55rem; margin:0 auto 10px; }
    .cm-hero h2 { font-size:1.45rem; font-weight:800; margin:0 0 4px; letter-spacing:-.5px; }
    .cm-hero p { font-size:.84rem; margin:0; color:rgba(255,255,255,.85); }
    .cm-hero p a { color:#fff; font-weight:700; text-decoration:underline; }
    .cm-hero .cm-ver-pill { display:inline-flex; align-items:center; gap:5px; margin-top:12px; padding:4px 12px; border-radius:999px; background:rgba(255,255,255,.18); font-size:.75rem; font-weight:700; }
    .cm-grid { display:grid; grid-template-columns:1fr; gap:14px; }
    @media(min-width:768px){ .cm-grid { grid-template-columns:1fr 1fr; } }
    .cm-full { grid-column:1 / -1; }
</style>

<div class="cm-page">
  <div class="cm-wrap">
    <!-- Hero -->
    <div class="cm-hero">
        <div class="cm-hero-icon">&#x1F680;</div>
        <h2>SpeedRadius</h2>
        <p>Community &amp; Resources &middot; <a href="https://speedcomwifi.co.ke" target="_blank">speedcomwifi.co.ke</a></p>
        <span class="cm-ver-pill">v<?php echo $_smarty_tpl->tpl_vars['version']->value;?>
</span>
    </div>

    <div class="cm-grid">
        <!-- SpeedRadius / Update -->
        <div class="cm-card">
            <div class="cm-head">
                <div class="cm-icon" style="background:rgba(16,185,129,.18);color:#6ee7b7;">&#x1F680;</div>
                <div>
                    <div class="cm-title">SpeedRadius</div>
                    <div class="cm-sub">Billing for MikroTik</div>
                </div>
            </div>
            <div class="cm-body">
                <b>SpeedRadius</b> is a complete billing system for Hotspot, Static IP and PPPoE on MikroTik &mdash; powered by PHP and the MikroTik API.
            </div>
            <div class="cm-stats">
                <div class="cm-stat">
                    <div class="cm-stat-label">Current Version</div>
                    <div class="cm-stat-value cur" id="currentVersion">v<?php echo $_smarty_tpl->tpl_vars['version']->value;?>
</div>
                </div>
                <div class="cm-stat">
                    <div class="cm-stat-label">Latest Version</div>
                    <div class="cm-stat-value latest" id="latestVersion">Checking&hellip;</div>
                </div>
            </div>
            <div class="cm-foot">
                <a href="/update.php?step=4" class="cm-btn cm-btn-emerald">&#x1F5C4; Update Database</a>
                <a href="./update.php" target="_blank" class="cm-btn cm-btn-amber">&#x2B06; Install Latest</a>
                <a href="./CHANGELOG.md" target="_blank" class="cm-btn cm-btn-ghost">&#x1F4C4; Changelog</a>
                <a href="https://github.com/shabran01/SpeedRadius_Advanced/blob/main/CHANGELOG.md" target="_blank" class="cm-btn cm-btn-ghost">&#x1F419; Repo Changelog</a>
            </div>
        </div>

        <!-- WhatsApp Gateway -->
        <div class="cm-card">
            <div class="cm-head">
                <div class="cm-icon" style="background:rgba(16,185,129,.18);color:#6ee7b7;">&#x1F4AC;</div>
                <div>
                    <div class="cm-title">WhatsApp Gateway</div>
                    <div class="cm-sub">Messaging API service</div>
                </div>
            </div>
            <div class="cm-body">
                WhatsApp API gateway for sending and receiving messages, notifications, schedulers and reminders &mdash; for your business or ISP automation.
            </div>
            <div class="cm-foot">
                <a href="https://wa.nux.my.id/login" target="_blank" class="cm-btn cm-btn-whatsapp">&#x1F4AC; WA Gateway</a>
                <a href="https://chat.whatsapp.com/HjnLYIEN6h0A0KMXbfNYP5" target="_blank" class="cm-btn cm-btn-ghost">&#x1F465; Join Group</a>
            </div>
        </div>

        <!-- Donations -->
        <div class="cm-card cm-full">
            <div class="cm-head">
                <div class="cm-icon" style="background:rgba(219,39,119,.18);color:#f9a8d4;">&#x2764;&#xFE0F;</div>
                <div>
                    <div class="cm-title">Support &amp; Donations</div>
                    <div class="cm-sub">Keep development going</div>
                </div>
            </div>
            <div class="cm-body">
                Donations help keep SpeedRadius development going. Any contribution is deeply appreciated!
                <table class="cm-table" style="margin-top:8px;">
                    <tbody>
                        <tr><td>Binance</td><td>912397602</td></tr>
                        <tr><td>M-Pesa Kenya</td><td>0718167262</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="cm-foot">
                <a href="https://www.paypal.com/ncp/payment/Y9JS7KVJ5PZJG" target="_blank" class="cm-btn cm-btn-primary">&#x1F6D2; Buy License</a>
            </div>
        </div>
    </div>
  </div>
</div>

<?php echo '<script'; ?>
>
    window.addEventListener('DOMContentLoaded', function() {
        $.getJSON("https://raw.githubusercontent.com/shabran01/SpeedRadius_Advanced/main/version.json?" + Math.random(), function(data) {
            $('#latestVersion').text('v' + data.version);
        }).fail(function() {
            $('#latestVersion').text('N/A');
        });
    });
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
