{include file="sections/header.tpl"}

<style>
/* ── Activation / by-date report — modern clean UI (.ra-*) ────────────── */
.ra-wrap{max-width:1280px;margin:0 auto;padding:18px}
.ra-card{background:#fff;border:1px solid #e6e9ef;border-radius:16px;overflow:hidden;
         box-shadow:0 1px 2px rgba(16,24,40,.05),0 14px 34px -28px rgba(16,24,40,.35)}

.ra-head{position:relative;padding:22px 24px 20px;border-bottom:1px solid #eef1f6}
.ra-head:before{content:"";position:absolute;top:0;left:0;right:0;height:3px;
  background:linear-gradient(90deg,#4f46e5 0%,#7c3aed 55%,#0ea5e9 100%)}
.ra-head-in{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
.ra-title-wrap{display:flex;align-items:center;gap:14px;min-width:0}
.ra-ic{width:46px;height:46px;border-radius:12px;background:#eef2ff;color:#4f46e5;
       display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.ra-title{font-size:18px;font-weight:700;color:#111827;margin:0;letter-spacing:-.01em}
.ra-sub{font-size:12.5px;color:#6b7280;margin:3px 0 0}

.ra-search{margin-left:auto;width:100%;max-width:560px}
.ra-search form{display:flex;gap:8px;flex-wrap:wrap}
.ra-select,.ra-input,.ra-btn{height:40px;border-radius:10px;font-size:13px;outline:none;
  transition:border-color .15s,box-shadow .15s}
.ra-select{border:1.5px solid #e6e9ef;background:#fff;color:#374151;padding:0 10px;cursor:pointer}
.ra-input{flex:1;min-width:160px;border:1.5px solid #e6e9ef;background:#fff;color:#111827;padding:0 12px}
.ra-input::placeholder{color:#9aa3b2}
.ra-select:focus,.ra-input:focus{border-color:#4f46e5;box-shadow:0 0 0 3px rgba(79,70,229,.13)}
.ra-btn{display:inline-flex;align-items:center;gap:7px;border:0;padding:0 16px;font-weight:600;
       color:#fff;background:#4f46e5;cursor:pointer;box-shadow:0 6px 14px -8px rgba(79,70,229,.7)}
.ra-btn:hover{background:#4338ca}
.ra-btn i{font-size:13px}

.ra-body{padding:20px 24px 24px}
.ra-toolbar{display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap}
.ra-count{display:inline-flex;align-items:center;gap:7px;background:#f1f5f9;color:#475569;
  border-radius:999px;padding:4px 12px;font-size:12px;font-weight:600}
.ra-count b{color:#111827}

.ra-tablebox{overflow-x:auto;border:1px solid #eef1f6;border-radius:12px}
.ra-table{width:100%;border-collapse:collapse;white-space:nowrap;font-size:13px}
.ra-table thead th{position:sticky;top:0;background:#f8fafc;color:#475569;text-align:left;
  font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;
  padding:11px 14px;border-bottom:1px solid #e6e9ef;z-index:1}
.ra-table tbody td{padding:12px 14px;color:#1f2937;border-bottom:1px solid #f1f3f7;vertical-align:middle}
.ra-table tbody tr{transition:background .12s}
.ra-table tbody tr:hover{background:#f8fafc}
.ra-table tbody tr:last-child td{border-bottom:0}

.ra-table td.ra-invoice{font-family:'SF Mono','Roboto Mono',ui-monospace,Consolas,monospace;
  color:#4f46e5;font-weight:600;font-size:12.5px;cursor:pointer}
.ra-user{display:inline-flex;align-items:center;gap:8px;background:#f1f5f9;border-radius:999px;
  padding:4px 10px 4px 12px;color:#374151;font-weight:600;cursor:pointer;transition:background .12s}
.ra-user:hover{background:#e2e8f0}
.ra-user i{color:#4f46e5;font-size:11px}
.ra-chip{display:inline-flex;align-items:center;background:#f8fafc;border:1px solid #e6e9ef;
  color:#475569;border-radius:7px;padding:2px 8px;font-size:11.5px;font-weight:600}
.ra-chip--type{background:#eff6ff;border-color:#bfdbfe;color:#1d4ed8}
.ra-method{display:inline-block;max-width:230px;overflow:hidden;text-overflow:ellipsis;
  vertical-align:bottom;color:#6b7280;font-size:12.5px}
.ra-dot{display:inline-block;width:7px;height:7px;border-radius:50%;margin-right:7px;vertical-align:middle}
.ra-table td.ra-created{color:#059669;font-weight:600}
.ra-created .ra-dot{background:#10b981}
.ra-table td.ra-expires{color:#b45309;font-weight:600}
.ra-expires .ra-dot{background:#f59e0b}

.ra-empty{text-align:center;padding:44px 16px;color:#9aa3b2}
.ra-empty i{display:block;font-size:30px;margin-bottom:10px;color:#d6dbe3}

.ra-pagination{display:flex;justify-content:center;margin-top:18px}
.ra-pagination .pagination{margin:0}
.ra-pagination .pagination>li>a,.ra-pagination .pagination>li>span{color:#4f46e5;
  border:1px solid #e6e9ef;border-radius:8px;margin:0 2px}
.ra-pagination .pagination>li>a:hover{background:#eef2ff;border-color:#c7d2fe}
.ra-pagination .pagination>.active>a{background:#4f46e5;border-color:#4f46e5;color:#fff}
.ra-pagination .pagination>.disabled>a{color:#c3c9d4}

/* dark mode */
.dark-mode .ra-card{background:#2d3748;border-color:#3b4a5f}
.dark-mode .ra-head{background:#2d3748;border-bottom-color:#3b4a5f}
.dark-mode .ra-title{color:#e2e8f0}
.dark-mode .ra-sub{color:#94a3b8}
.dark-mode .ra-ic{background:#283242;color:#a5b4fc}
.dark-mode .ra-select,.dark-mode .ra-input{background:#283242;border-color:#3b4a5f;color:#e2e8f0}
.dark-mode .ra-count{background:#283242;color:#cbd5e0}
.dark-mode .ra-count b{color:#e2e8f0}
.dark-mode .ra-tablebox{border-color:#3b4a5f}
.dark-mode .ra-table thead th{background:#283242;color:#94a3b8;border-color:#3b4a5f}
.dark-mode .ra-table tbody td{color:#cbd5e0;border-color:#334155}
.dark-mode .ra-table tbody tr:hover{background:#283242}
.dark-mode .ra-table td.ra-invoice{color:#a5b4fc}
.dark-mode .ra-user{background:#283242;color:#cbd5e0}
.dark-mode .ra-user:hover{background:#334155}
.dark-mode .ra-user i{color:#a5b4fc}
.dark-mode .ra-chip{background:#283242;border-color:#3b4a5f;color:#cbd5e0}
.dark-mode .ra-chip--type{background:#1e3a6b;border-color:#3b82f6;color:#bfdbfe}
.dark-mode .ra-method{color:#94a3b8}
.dark-mode .ra-table td.ra-created{color:#6ee7b7}
.dark-mode .ra-table td.ra-expires{color:#fcd34d}
.dark-mode .ra-empty{color:#64748b}
.dark-mode .ra-empty i{color:#475569}
.dark-mode .ra-pagination .pagination>li>a{color:#a5b4fc;border-color:#3b4a5f;background:#283242}
.dark-mode .ra-pagination .pagination>li>a:hover{background:#334155}
.dark-mode .ra-pagination .pagination>.active>a{background:#4f46e5;border-color:#4f46e5;color:#fff}

@media(max-width:640px){
  .ra-search{max-width:none}
  .ra-btn span{display:none}
}
</style>

<div class="ra-wrap">
  <div class="ra-card">
    <div class="ra-head">
      <div class="ra-head-in">
        <div class="ra-title-wrap">
          <span class="ra-ic"><i class="fa fa-bar-chart"></i></span>
          <div>
            <h1 class="ra-title">{Lang::T('Activation Reports')}</h1>
            <p class="ra-sub">{Lang::T('Track user activations and transactions')}</p>
          </div>
        </div>
        <div class="ra-search">
          <form id="site-search" method="post" action="{$_url}reports/activation">
            <select name="search_type" class="ra-select">
              <option value="invoice" {if $search_type eq 'invoice' or !$search_type}selected{/if}>{Lang::T('Invoice')}</option>
              <option value="username" {if $search_type eq 'username'}selected{/if}>{Lang::T('Username')}</option>
              <option value="method" {if $search_type eq 'method'}selected{/if}>{Lang::T('Method')}</option>
            </select>
            <input type="text" name="q" value="{$q}" class="ra-input"
                   placeholder="{Lang::T('Search by invoice, username or method')}...">
            <button type="submit" class="ra-btn"><i class="fa fa-search"></i><span>{Lang::T('Search')}</span></button>
          </form>
        </div>
      </div>
    </div>

    <div class="ra-body">
      {if $activation}
        <div class="ra-toolbar">
          <span class="ra-count"><i class="fa fa-list"></i> <b>{$activation|@count}</b> {Lang::T('records on this page')}</span>
        </div>
      {/if}
      <div class="ra-tablebox">
        <table id="datatable" class="ra-table">
          <thead>
            <tr>
              <th>{Lang::T('Invoice')}</th>
              <th>{Lang::T('Username')}</th>
              <th>{Lang::T('Plan Name')}</th>
              <th>{Lang::T('Plan Price')}</th>
              <th>{Lang::T('Type')}</th>
              <th>{Lang::T('Created On')}</th>
              <th>{Lang::T('Expires On')}</th>
              <th>{Lang::T('Method')}</th>
              <th>{Lang::T('Routers')}</th>
            </tr>
          </thead>
          <tbody>
            {foreach $activation as $ds}
              <tr>
                <td class="ra-invoice" onclick="window.location.href = '{$_url}plan/view/{$ds['id']}'">{$ds['invoice']}</td>
                <td>
                  <span class="ra-user" onclick="window.location.href = '{$_url}customers/viewu/{$ds['username']}'">
                    <i class="fa fa-user"></i>{$ds['username']}
                  </span>
                </td>
                <td>{$ds['plan_name']}</td>
                <td>{Lang::moneyFormat($ds['price'])}</td>
                <td><span class="ra-chip ra-chip--type">{$ds['type']}</span></td>
                <td class="ra-created"><span class="ra-dot"></span>{Lang::dateAndTimeFormat($ds['recharged_on'],$ds['recharged_time'])}</td>
                <td class="ra-expires"><span class="ra-dot"></span>{Lang::dateAndTimeFormat($ds['expiration'],$ds['time'])}</td>
                <td title="{$ds['method']}"><span class="ra-method">{$ds['method']}</span></td>
                <td><span class="ra-chip">{$ds['routers']}</span></td>
              </tr>
            {foreachelse}
              <tr><td colspan="9" class="ra-empty"><i class="fa fa-inbox"></i>{Lang::T('No transactions found')}</td></tr>
            {/foreach}
          </tbody>
        </table>
      </div>
      <div class="ra-pagination">{include file="pagination.tpl"}</div>
    </div>
  </div>
</div>

{include file="sections/footer.tpl"}
