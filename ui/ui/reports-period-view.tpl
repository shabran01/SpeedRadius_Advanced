{include file="sections/header.tpl"}
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
              <h1 style="font-size:1.35rem; font-weight:700; color:#fff; margin:0;">{Lang::T('Period Report')}</h1>
              <p style="margin:.25rem 0 0 0; color:rgba(255,255,255,.8); font-size:.85rem;">
                {if $router}{$router} &mdash; {/if}{$stype}
                [{date( $_c['date_format'], strtotime($fdate))} &rarr; {date( $_c['date_format'], strtotime($tdate))}]
              </p>
            </div>
          </div>
          <div style="display:flex; gap:.6rem; flex-wrap:wrap;">
            <a href="{$_url}reports/by-period" class="btn-back">&#8592; {Lang::T('Back to Filters')}</a>
            <form method="post" action="{$_url}export/print-by-period" target="_blank" style="display:inline;">
              <input type="hidden" name="fdate" value="{$fdate}">
              <input type="hidden" name="tdate" value="{$tdate}">
              <input type="hidden" name="stype" value="{$stype}">
              <input type="hidden" name="router" value="{$router}">
              <button type="submit" class="btn-export print">&#128424; {Lang::T('Print')}</button>
            </form>
            <form method="post" action="{$_url}export/pdf-by-period" target="_blank" style="display:inline;">
              <input type="hidden" name="fdate" value="{$fdate}">
              <input type="hidden" name="tdate" value="{$tdate}">
              <input type="hidden" name="stype" value="{$stype}">
              <input type="hidden" name="router" value="{$router}">
              <button type="submit" class="btn-export pdf">&#128196; {Lang::T('Export to PDF')}</button>
            </form>
          </div>
        </div>
      </div>

      <div style="padding:1.5rem 2rem;">
        <div style="display:flex; justify-content:flex-end; margin-bottom:1.5rem;">
          <div class="total-box">
            <div style="font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#047857;">{Lang::T('Total Income')}</div>
            <div style="font-size:1.9rem; font-weight:800; color:#065f46; margin-top:.15rem;">{Lang::moneyFormat($dr)}</div>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table pv-table">
            <thead>
              <tr>
                <th>{Lang::T('Username')}</th>
                <th>{Lang::T('Type')}</th>
                <th>{Lang::T('Plan Name')}</th>
                <th class="text-right">{Lang::T('Plan Price')}</th>
                <th>{Lang::T('Created On')}</th>
                <th>{Lang::T('Expires On')}</th>
                <th>{Lang::T('Method')}</th>
                <th>{Lang::T('Routers')}</th>
              </tr>
            </thead>
            <tbody>
              {foreach $d as $ds}
              <tr>
                <td style="font-weight:600; color:#1e293b;">{$ds['username']}</td>
                <td><span style="display:inline-flex; padding:.2rem .55rem; border-radius:9999px; font-size:.72rem; font-weight:600; background:#eef2ff; color:#4338ca;">{$ds['type']}</span></td>
                <td>{$ds['plan_name']}</td>
                <td class="text-right" style="font-weight:600; color:#047857;">{Lang::moneyFormat($ds['price'])}</td>
                <td>{Lang::dateAndTimeFormat($ds['recharged_on'],$ds['recharged_time'])}</td>
                <td>{Lang::dateAndTimeFormat($ds['expiration'],$ds['time'])}</td>
                <td>{$ds['method']}</td>
                <td>{$ds['routers']}</td>
              </tr>
              {/foreach}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{include file="sections/footer.tpl"}