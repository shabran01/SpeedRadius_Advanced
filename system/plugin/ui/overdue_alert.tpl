{include file="sections/header.tpl"}

<div class="oa-page">
    <div class="oa-stats">
        <div class="oa-stat">
            <span class="oa-stat-accent oa-accent-total"></span>
            <div class="oa-stat-value">{$stats.total}</div>
            <div class="oa-stat-label">{Lang::T('Total Expiring')}</div>
        </div>
        <div class="oa-stat">
            <span class="oa-stat-accent oa-accent-critical"></span>
            <div class="oa-stat-value">{$stats.critical}</div>
            <div class="oa-stat-label">{Lang::T('Critical')} (&le;2 {Lang::T('days')})</div>
        </div>
        <div class="oa-stat">
            <span class="oa-stat-accent oa-accent-warning"></span>
            <div class="oa-stat-value">{$stats.warning}</div>
            <div class="oa-stat-label">{Lang::T('Warning')} (3-4 {Lang::T('days')})</div>
        </div>
        <div class="oa-stat">
            <span class="oa-stat-accent oa-accent-caution"></span>
            <div class="oa-stat-value">{$stats.caution}</div>
            <div class="oa-stat-label">{Lang::T('Caution')} (5+ {Lang::T('days')})</div>
        </div>
    </div>

    <div class="oa-card">
        <div class="oa-card-head">
            <div class="oa-card-title">
                <h2>{Lang::T('Customers Expiring Soon')}</h2>
                <p>{Lang::T('Customers expiring in the next')} {$days} {Lang::T('days')} &middot; {Lang::T('sorted by expiry date')}</p>
            </div>
            <form method="get" action="" class="oa-window">
                <input type="hidden" name="_route" value="plugin/overdue_alert">
                <label for="days">{Lang::T('Show next')}</label>
                <select id="days" name="days" onchange="this.form.submit()">
                    {foreach $dayOptions as $opt}
                        <option value="{$opt}"{if $opt == $days} selected{/if}>{$opt} {Lang::T('days')}</option>
                    {/foreach}
                </select>
            </form>
        </div>

        <div class="oa-card-body">
            {if count($d) > 0}
                <div class="oa-filters">
                    <span class="oa-filters-label">{Lang::T('Filter')}</span>
                    <button type="button" class="oa-chip active" data-filter="">{Lang::T('All')}</button>
                    <button type="button" class="oa-chip" data-filter="PPPoE"><i class="fa fa-plug"></i> PPPoE</button>
                    <button type="button" class="oa-chip" data-filter="Hotspot"><i class="fa fa-wifi"></i> Hotspot</button>
                </div>

                <div class="table-responsive oa-table-wrap">
                    <table id="overdue_table" class="oa-table">
                        <thead>
                            <tr>
                                <th>{Lang::T('Customer')}</th>
                                <th>{Lang::T('Username')}</th>
                                <th>{Lang::T('Plan')}</th>
                                <th>{Lang::T('Contact')}</th>
                                <th>{Lang::T('Expires')}</th>
                                <th>{Lang::T('Days Left')}</th>
                                <th>{Lang::T('Actions')}</th>
                                <th>{Lang::T('Service Type')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $d as $ds}
                                <tr class="oa-row-{$ds.severity}">
                                    <td>
                                        <div class="oa-customer-name">{$ds.fullname|escape:'html'}</div>
                                        {if $ds.email}
                                            <div class="oa-customer-email"><i class="fa fa-envelope-o"></i> {$ds.email|escape:'html'}</div>
                                        {/if}
                                    </td>
                                    <td><code class="oa-username">{$ds.username|escape:'html'}</code></td>
                                    <td><span class="oa-plan">{$ds.plan_name|escape:'html'}</span></td>
                                    <td>
                                        {if $ds.phonenumber}
                                            <a class="oa-phone" href="tel:{$ds.phonenumber|escape:'html'}">
                                                <i class="fa fa-phone"></i> {$ds.phonenumber|escape:'html'}
                                            </a>
                                        {else}
                                            <span class="oa-muted">{Lang::T('No phone')}</span>
                                        {/if}
                                    </td>
                                    <td>
                                        <span class="oa-sort-key">{$ds.expiration|escape:'html'} {$ds.expiry_time_display|escape:'html'}</span>
                                        <div class="oa-date">{$ds.expiration|date_format:"%d %b %Y"}</div>
                                        <div class="oa-time">{$ds.expiry_time_display|escape:'html'}</div>
                                    </td>
                                    <td>
                                        <span class="oa-sort-key">{$ds.seconds_key}</span>
                                        <span class="oa-badge oa-badge-{$ds.severity}">{$ds.days_left_text|escape:'html'}</span>
                                    </td>
                                    <td>
                                        <a href="{$_url}customers/view/{$ds.customer_id}" class="oa-btn">
                                            <i class="fa fa-eye"></i> {Lang::T('View')}
                                        </a>
                                    </td>
                                    <td class="oa-service">{$ds.service_type|escape:'html'}</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            {else}
                <div class="oa-empty">
                    <div class="oa-empty-icon"><i class="fa fa-check-circle-o"></i></div>
                    <h3>{Lang::T('All Clear!')}</h3>
                    <p>{Lang::T('No customers will be overdue in the next')} {$days} {Lang::T('days')}.</p>
                </div>
            {/if}
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
