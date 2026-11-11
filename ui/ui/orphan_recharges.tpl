{include file="sections/header.tpl"}

<style>
{literal}
    .orphan-box,
    .orphan-box *,
    .orphan-box *::before,
    .orphan-box *::after {
        box-sizing: border-box;
    }
    .orphan-box .box-header {
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    }
    .orphan-box .box-title {
        flex: 1 1 auto; min-width: 0; margin: 0;
        overflow-wrap: anywhere;
    }
    .orphan-box .orphan-count {
        flex: 0 0 auto; margin: 0;
    }
    .orphan-box .orphan-note {
        font-size: 13px; line-height: 1.5; margin-bottom: 14px;
    }
    .orphan-table-wrap {
        overflow-x: auto; -webkit-overflow-scrolling: touch;
    }
    .orphan-table {
        width: 100%; border-collapse: collapse;
    }
    .orphan-table th,
    .orphan-table td {
        padding: 8px 10px; vertical-align: top;
        overflow-wrap: anywhere; word-break: break-word;
    }
    .orphan-table td.orphan-num { white-space: nowrap; }

    /* Phones / small tablets: each row becomes its own card, labels on the left. */
    @media (max-width: 767px) {
        .orphan-table-wrap { overflow-x: hidden; }
        .orphan-table thead { display: none; }
        .orphan-table,
        .orphan-table tbody,
        .orphan-table tr,
        .orphan-table td { display: block; width: 100%; }

        .orphan-table tr {
            border: 1px solid #e3e9ef; border-radius: 10px;
            margin-bottom: 10px; padding: 6px 4px; background: #fff;
        }
        .orphan-table td {
            border: none; border-bottom: 1px dashed #eef2f6;
            display: flex; justify-content: space-between; align-items: flex-start;
            gap: 12px; padding: 8px 10px;
            text-align: right;
        }
        .orphan-table td:last-child { border-bottom: none; }
        .orphan-table td::before {
            content: attr(data-label);
            flex: 0 0 42%; text-align: left;
            font-weight: 700; font-size: 12px; color: #6b7f8c;
        }
        .orphan-table td.orphan-num { white-space: normal; }
        .orphan-box .box-body { padding: 12px; }
    }

    @media (max-width: 420px) {
        .orphan-table td { flex-direction: column; align-items: stretch; text-align: left; gap: 2px; }
        .orphan-table td::before { flex: none; }
    }
{/literal}
</style>

<div class="box box-primary orphan-box">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-exclamation-triangle text-orange"></i>
            {Lang::T('Orphan Recharges')}
        </h3>
        <span class="label label-warning orphan-count">{$total}</span>
    </div>
    <div class="box-body">
        <p class="text-muted orphan-note">
            {Lang::T('Recharge records whose customer account no longer exists. This report is read-only.')}
        </p>

        {if $total > 0}
            <div class="orphan-table-wrap">
                <table class="table table-hover table-striped orphan-table">
                    <thead>
                        <tr>
                            <th>{Lang::T('Username')}</th>
                            <th>{Lang::T('Recharge Count')}</th>
                            <th>{Lang::T('Last Recharge')}</th>
                            <th>{Lang::T('Plan')}</th>
                            <th>{Lang::T('Router')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $rows as $row}
                            <tr>
                                <td data-label="{Lang::T('Username')}">{$row['username']|escape}</td>
                                <td data-label="{Lang::T('Recharge Count')}" class="orphan-num">{$row['cnt']|escape}</td>
                                <td data-label="{Lang::T('Last Recharge')}">{$row['last_on']|escape}</td>
                                <td data-label="{Lang::T('Plan')}">{$row['plan_name']|escape}</td>
                                <td data-label="{Lang::T('Router')}">{$row['routers']|escape}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        {else}
            <div class="alert alert-success">
                <i class="fa fa-check-circle"></i>
                {Lang::T('No orphan recharge records were found.')}
            </div>
        {/if}
    </div>
</div>

{include file="sections/footer.tpl"}
