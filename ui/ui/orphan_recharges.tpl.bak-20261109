{include file="sections/header.tpl"}

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-exclamation-triangle text-orange"></i> {Lang::T('Orphan Recharges')}</h3>
        <span class="label label-warning pull-right">{$total}</span>
    </div>
    <div class="box-body">
        <p class="text-muted">
            {Lang::T('Recharge records whose customer account no longer exists. This report is read-only.')}
        </p>

        {if $total > 0}
            <div class="table-responsive">
                <table class="table table-hover table-striped">
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
                                <td>{$row['username']|escape}</td>
                                <td>{$row['cnt']|escape}</td>
                                <td>{$row['last_on']|escape}</td>
                                <td>{$row['plan_name']|escape}</td>
                                <td>{$row['routers']|escape}</td>
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
