{include file="sections/header.tpl"}

<style>
.cm-body-wrap { min-height:100vh; padding:2rem 1rem; background:radial-gradient(1100px 500px at 50% -8%, #ecfdf5 0%, #f8fafc 55%, #f1f5f9 100%); }
.cm-wrap { max-width:1080px; margin:0 auto; }
.cm-hero { position:relative; overflow:hidden; background:linear-gradient(135deg,#059669 0%,#0d9488 50%,#0f766e 100%); border-radius:22px; padding:2.4rem 2rem; color:#fff; box-shadow:0 24px 50px -18px rgba(13,148,136,.55); }
.cm-hero::after { content:''; position:absolute; inset:0; background:radial-gradient(circle at 85% 0%, rgba(255,255,255,.25), transparent 45%); pointer-events:none; }
.cm-hero-inner { position:relative; z-index:1; display:flex; align-items:center; gap:1.2rem; flex-wrap:wrap; justify-content:space-between; }
.cm-hero-left { display:flex; align-items:center; gap:1rem; min-width:250px; }
.cm-hero-logo { width:60px; height:60px; border-radius:16px; background:rgba(255,255,255,.18); display:flex; align-items:center; justify-content:center; font-size:1.7rem; backdrop-filter:blur(4px); flex-shrink:0; }
.cm-hero h1 { margin:0; font-size:1.7rem; font-weight:800; letter-spacing:-.5px; }
.cm-hero p { margin:.2rem 0 0; font-size:.9rem; color:rgba(255,255,255,.85); }
.cm-hero p a { color:#fff; font-weight:700; text-decoration:underline; }
.cm-ver-row { display:flex; gap:.5rem; flex-wrap:wrap; }
.cm-ver-pill { display:inline-flex; align-items:center; gap:.4rem; padding:.42rem .9rem; border-radius:999px; background:rgba(255,255,255,.16); font-size:.78rem; font-weight:700; }
.cm-ver-pill .dot { width:8px; height:8px; border-radius:50%; background:#34d399; box-shadow:0 0 0 3px rgba(52,211,153,.35); }
.cm-grid { display:grid; grid-template-columns:1fr; gap:1.1rem; margin-top:1.3rem; }
@media(min-width:820px){ .cm-grid { grid-template-columns:1fr 1fr; } }
.cm-card { background:#fff; border:1px solid #e2e8f0; border-radius:18px; box-shadow:0 1px 3px rgba(15,23,42,.05), 0 12px 30px -18px rgba(15,23,42,.25); display:flex; flex-direction:column; overflow:hidden; transition:transform .18s ease, box-shadow .18s ease; border-top:3px solid #10b981; }
.cm-card:hover { transform:translateY(-3px); box-shadow:0 20px 40px -18px rgba(15,23,42,.3); }
.cm-head { display:flex; align-items:center; gap:.75rem; padding:1.05rem 1.25rem; border-bottom:1px solid #f1f5f9; }
.cm-icon { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.25rem; flex-shrink:0; }
.cm-title { font-size:1rem; font-weight:700; color:#1e293b; }
.cm-sub { font-size:.76rem; color:#94a3b8; }
.cm-body { padding:1.1rem 1.25rem; font-size:.88rem; color:#475569; line-height:1.65; flex:1; }
.cm-body b { color:#0f172a; }
.cm-chips { display:flex; flex-wrap:wrap; gap:.4rem; margin-top:.85rem; }
.cm-chip { display:inline-flex; align-items:center; gap:.3rem; padding:.28rem .65rem; border-radius:999px; background:#ecfdf5; border:1px solid #a7f3d0; color:#047857; font-size:.72rem; font-weight:600; }
.cm-stats { display:grid; grid-template-columns:1fr 1fr; gap:.8rem; padding:0 1.25rem 1.1rem; }
.cm-stat { background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:.8rem .9rem; }
.cm-stat-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#64748b; margin-bottom:3px; }
.cm-stat-value { font-size:1.1rem; font-weight:800; color:#0f172a; }
.cm-stat-value.ok { color:#059669; }
.cm-foot { display:flex; flex-wrap:wrap; gap:.5rem; padding:1rem 1.25rem; border-top:1px solid #f1f5f9; background:#fbfdfd; }
.cm-btn { display:inline-flex; align-items:center; gap:.45rem; padding:.55rem .95rem; border-radius:10px; font-size:.78rem; font-weight:600; text-decoration:none !important; transition:all .15s; white-space:nowrap; border:1px solid transparent; cursor:pointer; }
.cm-btn:hover { transform:translateY(-1px); filter:brightness(1.05); }
.cm-btn-primary { background:linear-gradient(135deg,#10b981,#059669); color:#fff; box-shadow:0 6px 16px -8px rgba(16,185,129,.6); }
.cm-btn-amber { background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; }
.cm-btn-ghost { background:#fff; color:#475569; border-color:#e2e8f0; }
.cm-btn-ghost:hover { background:#f8fafc; color:#0f172a; }
.cm-btn-whatsapp { background:linear-gradient(135deg,#25d366,#1da851); color:#fff; box-shadow:0 6px 16px -8px rgba(37,211,102,.6); }
.cm-table { width:100%; border-collapse:collapse; }
.cm-table td { padding:.7rem .2rem; border-bottom:1px solid #f1f5f9; font-size:.85rem; vertical-align:middle; }
.cm-table tr:last-child td { border-bottom:none; }
.cm-table td:first-child { color:#475569; font-weight:600; }
.cm-table .val { display:inline-flex; align-items:center; gap:.5rem; float:right; font-weight:700; color:#0f172a; font-family:ui-monospace,SFMono-Regular,Consolas,monospace; }
.cm-copy { background:#fff; border:1px solid #e2e8f0; color:#64748b; border-radius:7px; padding:.15rem .55rem; font-size:.68rem; cursor:pointer; transition:all .15s; }
.cm-copy:hover { background:#ecfdf5; border-color:#6ee7b7; color:#047857; }
.cm-note { margin-top:.9rem; font-size:.82rem; color:#94a3b8; display:flex; align-items:center; gap:.4rem; }
.cm-footer-note { text-align:center; color:#94a3b8; font-size:.75rem; margin-top:1.4rem; }
</style>

<div class="cm-body-wrap">
  <div class="cm-wrap">
    <!-- Hero -->
    <div class="cm-hero">
      <div class="cm-hero-inner">
        <div class="cm-hero-left">
          <div class="cm-hero-logo">&#x1F680;</div>
          <div>
            <h1>SpeedRadius</h1>
            <p>Community &amp; Resources &middot; <a href="https://speedcomwifi.co.ke" target="_blank">speedcomwifi.co.ke</a></p>
          </div>
        </div>
        <div class="cm-ver-row">
          <span class="cm-ver-pill"><span class="dot"></span> v{$version}</span>
        </div>
      </div>
    </div>

    <div class="cm-grid">
      <!-- System card -->
      <div class="cm-card">
        <div class="cm-head">
          <div class="cm-icon" style="background:#ecfdf5;color:#059669;">&#x1F4E6;</div>
          <div>
            <div class="cm-title">SpeedRadius System</div>
            <div class="cm-sub">Billing for MikroTik</div>
          </div>
        </div>
        <div class="cm-body">
          <b>SpeedRadius</b> is a complete billing system for Hotspot, Static IP and PPPoE on MikroTik &mdash; powered by PHP and the MikroTik API.
          <div class="cm-chips">
            <span class="cm-chip">&#x1F6A8; Hotspot</span>
            <span class="cm-chip">&#x1F4C8; PPPoE</span>
            <span class="cm-chip">&#x1F310; Static IP</span>
            <span class="cm-chip">&#x1F4AC; SMS &amp; WhatsApp</span>
            <span class="cm-chip">&#x1F4B3; Payment Gateways</span>
            <span class="cm-chip">&#x1F30D; Multi-Language</span>
          </div>
        </div>
        <div class="cm-stats">
          <div class="cm-stat">
            <div class="cm-stat-label">Current Version</div>
            <div class="cm-stat-value ok" id="currentVersion">v{$version}</div>
          </div>
          <div class="cm-stat">
            <div class="cm-stat-label">Latest Version</div>
            <div class="cm-stat-value ok" id="latestVersion">Checking&hellip;</div>
          </div>
        </div>
        <div class="cm-foot">
          <a href="/update.php?step=4" class="cm-btn cm-btn-primary">&#x1F5C4; Update Database</a>
          <a href="./update.php" target="_blank" class="cm-btn cm-btn-amber">&#x2B06; Install Latest</a>
          <a href="./CHANGELOG.md" target="_blank" class="cm-btn cm-btn-ghost">&#x1F4C4; Changelog</a>
          <a href="https://github.com/shabran01/SpeedRadius_Advanced/blob/main/CHANGELOG.md" target="_blank" class="cm-btn cm-btn-ghost">&#x1F419; Repo Changelog</a>
        </div>
      </div>

      <!-- Support & Donations -->
      <div class="cm-card">
        <div class="cm-head">
          <div class="cm-icon" style="background:#fdf2f8;color:#db2777;">&#x2764;&#xFE0F;</div>
          <div>
            <div class="cm-title">Support &amp; Donations</div>
            <div class="cm-sub">Keep development going</div>
          </div>
        </div>
        <div class="cm-body">
          Donations help keep SpeedRadius development going. Any contribution is deeply appreciated!
          <table class="cm-table" style="margin-top:.6rem;">
            <tbody>
              <tr>
                <td>Binance</td>
                <td class="val"><span class="val-txt">912397602</span><button type="button" class="cm-copy" onclick="copyVal(this)">Copy</button></td>
              </tr>
              <tr>
                <td>M-Pesa Kenya</td>
                <td class="val"><span class="val-txt">0718167262</span><button type="button" class="cm-copy" onclick="copyVal(this)">Copy</button></td>
              </tr>
            </tbody>
          </table>
          <div class="cm-note">&#x1F4AC; For support, join the official WhatsApp group:</div>
        </div>
        <div class="cm-foot">
          <a href="https://chat.whatsapp.com/HjnLYIEN6h0A0KMXbfNYP5" target="_blank" class="cm-btn cm-btn-whatsapp">&#x1F4AC; Support</a>
        </div>
      </div>
    </div>

    <p class="cm-footer-note">SpeedRadius &middot; PHP MikroTik Billing System</p>
  </div>
</div>

<script>
{literal}
window.addEventListener('DOMContentLoaded', function() {
    $.getJSON("https://raw.githubusercontent.com/shabran01/SpeedRadius_Advanced/main/version.json?" + Math.random(), function(data) {
        $('#latestVersion').text('v' + data.version);
    }).fail(function() {
        $('#latestVersion').text('N/A');
    });
});
function copyVal(btn) {
    var text = btn.closest('td').querySelector('.val-txt').textContent.trim();
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            btn.textContent = 'Copied';
            setTimeout(function(){ btn.textContent = 'Copy'; }, 1500);
        });
    } else {
        var ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        btn.textContent = 'Copied';
        setTimeout(function(){ btn.textContent = 'Copy'; }, 1500);
    }
}
{/literal}
</script>

{include file="sections/footer.tpl"}