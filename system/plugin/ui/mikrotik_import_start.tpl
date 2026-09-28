{include file="sections/header.tpl"}

<style>
.mi2-wrap{max-width:820px}
.mi2-card{background:#fff;border:1px solid #e6e9ef;border-radius:14px;margin-bottom:18px;overflow:hidden;
          box-shadow:0 1px 2px rgba(16,24,40,.04),0 10px 28px -18px rgba(16,24,40,.25)}
.mi2-head{padding:20px 24px 0}
.mi2-title{font-size:18px;font-weight:700;color:#111827;margin:0;letter-spacing:-.01em}
.mi2-sub{font-size:13px;color:#6b7280;margin:7px 0 0;line-height:1.65}
.mi2-body{padding:20px 24px 24px}
.mi2-foot{padding:16px 24px;background:#f9fafb;border-top:1px solid #eef1f6;
          display:flex;align-items:center;gap:14px;flex-wrap:wrap}

.mi2-badge{display:inline-block;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;
           border-radius:999px;padding:3px 11px;font-size:12px;font-weight:600}

.mi2-list{list-style:none;margin:0;padding:0;max-height:460px;overflow:auto}
.mi2-list li{position:relative;padding:9px 12px 9px 30px;margin:0 0 4px;font-size:13px;color:#374151;
             line-height:1.55;border-radius:9px;background:#f9fafb;border:1px solid #eef1f6}
.mi2-list li:before{content:"";position:absolute;left:13px;top:16px;width:6px;height:6px;border-radius:50%;background:#94a3b8}
.mi2-list li:last-child{margin-bottom:0}

.mi2-empty{background:#f9fafb;border:1px dashed #d8dee9;color:#6b7280;border-radius:12px;
           padding:18px;font-size:13.5px;text-align:center}

.mi2-btn{display:inline-flex;align-items:center;gap:9px;border:1px solid #e6e9ef;border-radius:11px;
         padding:10px 18px;font-size:13.5px;font-weight:600;color:#374151;background:#fff;text-decoration:none;
         transition:background .15s,border-color .15s}
.mi2-btn:hover{background:#f3f4f6;border-color:#c7d2e0;color:#111827;text-decoration:none}
.mi2-note{font-size:12.5px;color:#6b7280;margin:0}

.dark-mode .mi2-card{background:#2d3748;border-color:#3b4a5f}
.dark-mode .mi2-title{color:#e2e8f0}
.dark-mode .mi2-sub,.dark-mode .mi2-note{color:#94a3b8}
.dark-mode .mi2-list li{background:#283242;border-color:#3b4a5f;color:#cbd5e0}
.dark-mode .mi2-empty{background:#283242;border-color:#3b4a5f;color:#94a3b8}
.dark-mode .mi2-foot{background:#283242;border-top-color:#3b4a5f}
.dark-mode .mi2-btn{background:#2d3748;border-color:#3b4a5f;color:#cbd5e0}
.dark-mode .mi2-badge{background:#1e3a6b;border-color:#3b82f6;color:#bfdbfe}
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="mi2-wrap">
            <div class="mi2-card">
                <div class="mi2-head">
                    <h3 class="mi2-title">{Lang::T('Import finished')}</h3>
                    <p class="mi2-sub">{Lang::T('Everything below was checked against your existing data. Entries that already existed were left untouched.')}</p>
                </div>
                <div class="mi2-body">
                    {if $results}
                        <p style="margin:0 0 12px"><span class="mi2-badge">{$results|@count} {Lang::T('steps')}</span></p>
                        <ul class="mi2-list">
                            {foreach $results as $result}
                                <li>{$result}</li>
                            {/foreach}
                        </ul>
                    {else}
                        <div class="mi2-empty">
                            {Lang::T('Nothing was imported. Check that the router is reachable and that it has profiles to read.')}
                        </div>
                    {/if}
                </div>
                <div class="mi2-foot">
                    <a class="mi2-btn" href="{$_url}plugin/mikrotik_import_ui">
                        <i class="glyphicon glyphicon-arrow-left"></i> {Lang::T('Back to Import')}
                    </a>
                    <p class="mi2-note">{Lang::T('Imported packages need their price, time limit and validity set before you sell them.')}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
