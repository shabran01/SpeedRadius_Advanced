<!DOCTYPE html>
<html>
<head>
    <title>{$_title} - {$_c['CompanyName']}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="ui/ui/styles/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="ui/ui/images/favicon.ico">

    <style type="text/css">
        @media print
        {
            .no-print, .no-print *
            {
                display: none !important;
            }
            body { background: #fff !important; padding: 0 !important; }
            .report-card { box-shadow: none !important; border-radius: 0 !important; border: none !important; }
            .report-head, .rpt-table thead th, .type-pill, .total-box {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        body { background: #f1f5f9; font-family: 'Segoe UI', Tahoma, Arial, sans-serif; padding: 2rem 1rem; font-size: 1rem; }
        .report-wrap { max-width: 1400px; margin: 0 auto; }
        .report-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 10px 30px -12px rgba(15,23,42,.15); overflow: hidden; }
        .report-head { background: linear-gradient(135deg,#4f46e5,#4338ca); color: #fff; padding: 2rem 2.25rem; }
        .report-head h4 { margin: 0 0 .25rem 0; font-weight: 700; font-size: 1.75rem; }
        .report-head p { margin: 0; color: rgba(255,255,255,.9); font-size: 1.05rem; }
        .report-body { padding: 2rem 2.25rem; }
        .rpt-table { width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
        .rpt-table thead th { background: #f8fafc; color: #475569; font-weight: 700; font-size: .9rem; text-transform: uppercase; letter-spacing: .03em; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.1rem; text-align: left; white-space: nowrap; }
        .rpt-table tbody td { padding: .95rem 1.1rem; border-bottom: 1px solid #f1f5f9; font-size: 1.05rem; color: #334155; vertical-align: middle; }
        .rpt-table tbody tr:nth-child(even) td { background: #fafbfc; }
        .rpt-table tbody tr:last-child td { border-bottom: none; }
        .type-pill { display: inline-flex; padding: .2rem .65rem; border-radius: 9999px; font-size: .9rem; font-weight: 600; background: #eef2ff; color: #4338ca; }
        .total-box { margin: 2rem 0 0 auto; width: fit-content; background: linear-gradient(135deg,#ecfdf5,#d1fae5); border: 1px solid #a7f3d0; border-radius: 14px; padding: 1.25rem 2rem; text-align: right; }
        .total-box .lbl { font-size: .9rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #047857; }
        .total-box .val { font-size: 2.2rem; font-weight: 800; color: #065f46; }
        .print-btn { margin-top: 2rem; font-size: 1rem !important; padding: .7rem 1.4rem !important; }
    </style>
</head>

<body>
<div class="report-wrap">
    <div class="report-card">
        <div class="report-head">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
                <div>
                    <div style="font-size:1rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase; color:rgba(255,255,255,.7);">{$_c['CompanyName']}</div>
                    <h4>{Lang::T('Period Report')}</h4>
                    <p>{Lang::T('All Transactions at Date')}: {date( $_c['date_format'], strtotime($fdate))} &rarr; {date( $_c['date_format'], strtotime($tdate))}{if $stype} &mdash; {$stype}{/if}</p>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:.9rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:rgba(255,255,255,.7);">{Lang::T('Total Income')}</div>
                    <div style="font-size:2rem; font-weight:800; color:#fff;">{Lang::moneyFormat($dr)}</div>
                </div>
            </div>
        </div>
        <div class="report-body">
            <div class="table-responsive">
                <table class="rpt-table">
                    <thead>
                        <tr>
                            <th>{Lang::T('Username')}</th>
                            <th>{Lang::T('Plan Name')}</th>
                            <th>{Lang::T('Type')}</th>
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
                            <td style="font-weight:600;">{$ds['username']}</td>
                            <td>{$ds['plan_name']}</td>
                            <td><span class="type-pill">{$ds['type']}</span></td>
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
            <div class="total-box">
                <div class="lbl">{Lang::T('Total Income')}</div>
                <div class="val">{Lang::moneyFormat($dr)}</div>
            </div>
            <button type="button" id="actprint" class="btn btn-primary no-print print-btn">{Lang::T('Click Here to Print')}</button>
        </div>
    </div>
</div>
<script src="ui/ui/scripts/jquery.min.js"></script>
<script src="ui/ui/scripts/bootstrap.min.js"></script>
{if isset($xfooter)}
    {$xfooter}
{/if}
<script>
    jQuery(document).ready(function() {
        // initiate layout and plugins
        $("#actprint").click(function() {
            window.print();
            return false;
        });
    });
</script>

</body>
</html>