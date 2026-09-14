{include file="sections/header.tpl"}

<!-- Modern Plan Edit -->
<div class="plan-edit-modern">

    <!-- Header -->
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
        <a href="{$_url}plan/list" class="back-link" title="{Lang::T('Back')}">
            <i class="fa fa-arrow-left"></i>
        </a>
        <h3 style="margin:0;font-weight:700;color:#1a1a2e;">
            <i class="fa fa-pencil-square-o" style="color:#667eea;margin-right:8px;"></i>{Lang::T('Edit Plan')}
        </h3>
    </div>

    <!-- Customer Info Card -->
    <div class="info-card">
        <div class="info-row">
            <div class="info-item">
                <div class="info-label">{Lang::T('Username')}</div>
                <div class="info-value">
                    <i class="fa fa-user" style="color:#667eea;margin-right:6px;"></i>
                    <strong>{$d['username']}</strong>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">{Lang::T('Current Plan')}</div>
                <div class="info-value">
                    <span class="badge-type {strtolower($d['type'])}">{$d['type']}</span>
                    <strong>{$d['namebp']}</strong>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">{Lang::T('Status')}</div>
                <div class="info-value">
                    {if $d['status'] == 'on'}
                    <span class="status-badge status-on"><span class="status-dot"></span> {Lang::T('Active')}</span>
                    {else}
                    <span class="status-badge status-off"><span class="status-dot"></span> {Lang::T('Expired')}</span>
                    {/if}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">{Lang::T('Router')}</div>
                <div class="info-value"><i class="fa fa-server" style="color:#94a3b8;margin-right:6px;"></i> {$d['routers']}</div>
            </div>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="form-card">
        <form method="post" action="{$_url}plan/edit-post">
            <input type="hidden" name="id" value="{$d['id']}">
            <input type="hidden" name="username" value="{$d['username']}">

            <div class="form-section">
                <div class="form-group-modern">
                    <label class="form-label">{Lang::T('Service Plan')}</label>
                    <select id="id_plan" name="id_plan" class="form-select-lg">
                        {foreach $p as $ps}
                        <option value="{$ps['id']}" {if $d['plan_id'] eq $ps['id']}selected{/if}>
                            {if $ps['enabled'] neq 1}⚠ {Lang::T('DISABLED')} — {/if}
                            {$ps['name_plan']} — {Lang::moneyFormat($ps['price'])}
                            {if $ps['prepaid'] neq 'yes'} — {Lang::T('POSTPAID')}{/if}
                        </option>
                        {/foreach}
                    </select>
                    <div class="form-hint">{Lang::T('Changing the plan will update the customer on the router automatically.')}</div>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-title">{Lang::T('Dates & Times')}</h4>
                <div class="form-row">
                    <div class="form-group-modern">
                        <label class="form-label">{Lang::T('Created On')}</label>
                        <div class="form-row-inner">
                            <input type="date" class="form-input" value="{$d['recharged_on']}" readonly>
                            <input type="text" class="form-input form-input-sm" value="{$d['recharged_time']}" readonly>
                        </div>
                    </div>
                    <div class="form-group-modern">
                        <label class="form-label">{Lang::T('Expires On')}</label>
                        <div class="form-row-inner">
                            <input type="date" class="form-input" id="expiration" name="expiration" value="{$d['expiration']}">
                            <input type="text" class="form-input form-input-sm" id="time" name="time" value="{$d['time']}" placeholder="00:00:00">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit" onclick="return ask(this, '{Lang::T('Continue the package change process?')}')">
                    <i class="fa fa-check"></i> {Lang::T('Save Changes')}
                </button>
                <a href="{$_url}plan/list" class="btn-cancel">{Lang::T('Cancel')}</a>
            </div>
        </form>
    </div>

</div>

<style>
.plan-edit-modern { padding: 0 15px 30px; max-width: 900px; }
.plan-edit-modern * { box-sizing: border-box; }

.back-link {
    display: inline-flex; align-items: center; justify-content: center;
    width: 36px; height: 36px; border-radius: 10px; background: #f1f5f9;
    color: #475569; font-size: 14px; text-decoration: none; transition: all .2s;
}
.back-link:hover { background: #e2e8f0; color: #1e293b; }

/* Info Card */
.info-card {
    background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    padding: 16px 20px; margin-bottom: 16px;
}
.info-row { display: flex; flex-wrap: wrap; gap: 0; }
.info-item { flex: 1; min-width: 140px; padding: 8px 16px; }
.info-item:not(:last-child) { border-right: 1px solid #f1f5f9; }
.info-label { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
.info-value { font-size: 14px; color: #1e293b; display: flex; align-items: center; gap: 6px; }

.badge-type {
    display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 700;
}
.badge-type.hotspot { background: #fff7ed; color: #ea580c; }
.badge-type.pppoe { background: #eef2ff; color: #4f46e5; }

.status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700;
}
.status-on { background: #dcfce7; color: #16a34a; }
.status-off { background: #fee2e2; color: #dc2626; }
.status-dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
.status-on .status-dot { background: #22c55e; }
.status-off .status-dot { background: #ef4444; }

/* Form Card */
.form-card {
    background: #fff; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    padding: 24px;
}
.form-section { margin-bottom: 24px; }
.form-section:last-child { margin-bottom: 0; }
.section-title {
    font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase;
    letter-spacing: .5px; margin: 0 0 14px; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9;
}
.form-group-modern { margin-bottom: 16px; }
.form-group-modern:last-child { margin-bottom: 0; }
.form-label {
    display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;
}
.form-input, .form-select-lg {
    width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 9px 12px;
    font-size: 13px; color: #1e293b; background: #fafbfc; transition: border-color .2s;
    min-height: 40px;
}
.form-select-lg { cursor: pointer; }
.form-input:focus, .form-select-lg:focus {
    outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
}
.form-input[readonly] { background: #f8fafc; color: #94a3b8; cursor: default; }
.form-hint { font-size: 11px; color: #94a3b8; margin-top: 5px; }

.form-row { display: flex; gap: 20px; flex-wrap: wrap; }
.form-row .form-group-modern { flex: 1; min-width: 200px; }
.form-row-inner { display: flex; gap: 8px; }
.form-row-inner .form-input { flex: 2; }
.form-input-sm { max-width: 110px; }

/* Buttons */
.form-actions {
    display: flex; align-items: center; gap: 12px; margin-top: 24px;
    padding-top: 20px; border-top: 1px solid #f1f5f9;
}
.btn-submit {
    display: inline-flex; align-items: center; gap: 6px;
    background: #667eea; color: #fff; border: none; border-radius: 8px;
    padding: 10px 24px; font-size: 14px; font-weight: 700; cursor: pointer;
    transition: background .2s;
}
.btn-submit:hover { background: #5a6fd6; }
.btn-cancel {
    display: inline-flex; align-items: center;
    padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600;
    color: #64748b; text-decoration: none; transition: all .2s;
}
.btn-cancel:hover { background: #f1f5f9; color: #334155; }

@media (max-width: 768px) {
    .info-item { flex: 1 1 50%; border-right: none !important; border-bottom: 1px solid #f1f5f9; }
    .info-item:nth-child(odd) { border-right: 1px solid #f1f5f9 !important; }
    .form-card { padding: 16px; }
    .form-row { flex-direction: column; gap: 12px; }
}
@media (max-width: 480px) {
    .info-item { flex: 1 1 100%; border-right: none !important; }
}
</style>

{include file="sections/footer.tpl"}
