{include file="sections/header.tpl"}

{* ── Mikrotik Import — modern UI. Styles are scoped with .mi- so nothing
   else in the admin panel is affected. Dark-mode rules are at the end. ── *}
<style>
.mi-wrap{max-width:820px}
.mi-card{background:#fff;border:1px solid #e6e9ef;border-radius:14px;margin-bottom:18px;overflow:hidden;
         box-shadow:0 1px 2px rgba(16,24,40,.04),0 10px 28px -18px rgba(16,24,40,.25)}
.mi-head{padding:20px 24px 0}
.mi-title{font-size:18px;font-weight:700;color:#111827;margin:0;letter-spacing:-.01em}
.mi-sub{font-size:13px;color:#6b7280;margin:7px 0 0;line-height:1.65}
.mi-body{padding:20px 24px 24px}
.mi-foot{padding:16px 24px;background:#f9fafb;border-top:1px solid #eef1f6;
         display:flex;align-items:center;gap:14px;flex-wrap:wrap}
.mi-note{font-size:12.5px;color:#6b7280;margin:0}

.mi-list{list-style:none;margin:0;padding:0}
.mi-list li{position:relative;padding:0 0 0 26px;margin:0 0 10px;font-size:13.5px;color:#374151;line-height:1.6}
.mi-list li:last-child{margin-bottom:0}
.mi-list li:before{content:"";position:absolute;left:6px;top:7px;width:7px;height:7px;border-radius:50%;background:#2563eb}
.mi-list li b{color:#111827;font-weight:600}

.mi-tiles{display:grid;grid-template-columns:1fr 1fr;gap:12px}
@media(max-width:600px){.mi-tiles{grid-template-columns:1fr}}
.mi-tile{position:relative;display:block;margin:0;cursor:pointer}
.mi-tile input{position:absolute;opacity:0;width:0;height:0}
.mi-tile-in{display:block;border:1.5px solid #e6e9ef;border-radius:12px;padding:15px 16px;background:#fff;
            transition:border-color .15s,background .15s,box-shadow .15s}
.mi-tile:hover .mi-tile-in{border-color:#c7d2e0}
.mi-tile input:checked + .mi-tile-in{border-color:#2563eb;background:#f5f9ff;box-shadow:0 0 0 3px rgba(37,99,235,.13)}
.mi-tile input:focus-visible + .mi-tile-in{outline:2px solid #2563eb;outline-offset:2px}
.mi-tile-t{display:block;font-size:14px;font-weight:600;color:#111827;margin-bottom:3px}
.mi-tile-d{display:block;font-size:12.5px;color:#6b7280;line-height:1.5}
.mi-tile-ic{float:right;font-size:17px;color:#9aa4b2}
.mi-tile input:checked + .mi-tile-in .mi-tile-ic{color:#2563eb}

.mi-label{display:block;font-size:13px;font-weight:600;color:#374151;margin:0 0 7px}
.mi-select{width:100%;height:42px;border:1.5px solid #e6e9ef;border-radius:11px;padding:0 12px;font-size:13.5px;
           color:#111827;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s}
.mi-select:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.13)}
.mi-hint{font-size:12.5px;color:#6b7280;margin:8px 0 0}

.mi-btn{display:inline-flex;align-items:center;gap:9px;border:0;border-radius:11px;padding:11px 20px;
        font-size:13.5px;font-weight:600;color:#fff;background:#2563eb;cursor:pointer;
        box-shadow:0 6px 14px -6px rgba(37,99,235,.55);transition:background .15s,transform .12s}
.mi-btn:hover{background:#1d4ed8}
.mi-btn:active{transform:translateY(1px)}
.mi-btn-off{opacity:.5;cursor:not-allowed;box-shadow:none}

.mi-empty{background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;border-radius:12px;padding:14px 16px;font-size:13.5px}

.dark-mode .mi-card{background:#2d3748;border-color:#3b4a5f}
.dark-mode .mi-title{color:#e2e8f0}
.dark-mode .mi-sub,.dark-mode .mi-note,.dark-mode .mi-hint,.dark-mode .mi-tile-d{color:#94a3b8}
.dark-mode .mi-list li{color:#cbd5e0}
.dark-mode .mi-list li b{color:#e2e8f0}
.dark-mode .mi-foot{background:#283242;border-top-color:#3b4a5f}
.dark-mode .mi-tile-in{background:#2d3748;border-color:#3b4a5f}
.dark-mode .mi-tile input:checked + .mi-tile-in{background:#1e3a6b;border-color:#3b82f6}
.dark-mode .mi-tile-t{color:#e2e8f0}
.dark-mode .mi-select{background:#2d3748;border-color:#3b4a5f;color:#e2e8f0}
.dark-mode .mi-empty{background:#3a2a17;border-color:#7c4a12;color:#fdba74}
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="mi-wrap">
            <form method="post" action="{$_url}plugin/mikrotik_import_start_ui">

                <div class="mi-card">
                    <div class="mi-head">
                        <h3 class="mi-title">{Lang::T('Import from Mikrotik')}</h3>
                        <p class="mi-sub">{Lang::T('Reads packages and users from the router and creates them here. Nothing on the router is changed.')}</p>
                    </div>
                    <div class="mi-body">
                        <ul class="mi-list">
                            <li>{Lang::T('Packages and users are imported')} — <b>{Lang::T('anything that already exists is skipped, never overwritten')}</b></li>
                            <li>{Lang::T('Active packages are not imported')} — {Lang::T('refill the user or let them buy a new package')}</li>
                            <li>{Lang::T('Imported packages get a placeholder price of')} <b>10000</b> — {Lang::T('set the real price afterwards')}</li>
                        </ul>
                    </div>
                </div>

                <div class="mi-card">
                    <div class="mi-head">
                        <h3 class="mi-title">{Lang::T('What to import')}</h3>
                    </div>
                    <div class="mi-body">
                        <div class="mi-tiles" style="margin-bottom:20px">
                            <label class="mi-tile">
                                <input type="radio" name="type" value="Hotspot" checked>
                                <span class="mi-tile-in">
                                    <i class="glyphicon glyphicon-signal mi-tile-ic"></i>
                                    <span class="mi-tile-t">{Lang::T('Hotspot')}</span>
                                    <span class="mi-tile-d">{Lang::T('Hotspot user profiles and hotspot users')}</span>
                                </span>
                            </label>
                            <label class="mi-tile">
                                <input type="radio" name="type" value="PPPOE">
                                <span class="mi-tile-in">
                                    <i class="glyphicon glyphicon-transfer mi-tile-ic"></i>
                                    <span class="mi-tile-t">{Lang::T('PPPOE')}</span>
                                    <span class="mi-tile-d">{Lang::T('PPP profiles and PPP secrets')}</span>
                                </span>
                            </label>
                        </div>

                        <label class="mi-label" for="server">{Lang::T('Router')}</label>
                        {if $routers}
                            <select class="mi-select" id="server" name="server" required>
                                <option value="" disabled selected>{Lang::T('Select a router')}</option>
                                {foreach $routers as $router}
                                    <option value="{$router.name}">{$router.name} — {$router.ip_address}</option>
                                {/foreach}
                            </select>
                            <p class="mi-hint">{Lang::T('The router you want to read packages and users from.')}</p>
                        {else}
                            <div class="mi-empty">
                                <i class="glyphicon glyphicon-warning-sign"></i>
                                {Lang::T('No routers are configured yet.')}
                                <a href="{$_url}routers/add">{Lang::T('Add a router first')}</a>.
                            </div>
                        {/if}
                    </div>
                    <div class="mi-foot">
                        <button class="mi-btn{if !$routers} mi-btn-off{/if}" type="submit"{if !$routers} disabled{/if}>
                            <i class="glyphicon glyphicon-import"></i> {Lang::T('Start Import')}
                        </button>
                        <p class="mi-note">{Lang::T('On a busy router this can take a while. Please leave this page open until it finishes.')}</p>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
