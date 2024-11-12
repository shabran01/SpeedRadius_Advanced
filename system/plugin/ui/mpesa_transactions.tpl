{include file="sections/header.tpl"}
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
               Mpesa Transactions
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>First Name</th>
                            <th>Phone</th>
                            <th>Amount</th>
                            <th>Account No</th>
                            <th>Org Account Balance</th>
                            <th>Transaction ID</th>
                            <th>Transaction Type</th>
                            <th>Transaction Time</th>
                            <th>Business Short Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $t as $key => $ts}

                        <tr>
                            <td>{$key + 1}</td>
                            <td>{$ts['FirstName']}</td>
                            <td>{if $ts['MSISDN']}{$ts['MSISDN']|truncate:20:"..."}{else}No MSISDN available{/if}</td>
                            <td>{$ts['TransAmount']}</td>
                            <td>{$ts['BillRefNumber']}</td>
                            <td>{$ts['OrgAccountBalance']}</td>
                            <td>{$ts['TransID']}</td>
                            <td>{$ts['TransactionType']}</td>
                            <td>{$ts['TransTime']}</td>
                            <td>{$ts['BusinessShortCode']}</td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                {include file="pagination.tpl"}
                <div class="bs-callout bs-callout-info" id="callout-navbar-role">
                    <h4>All Mpesa Transaction </h4>
                    <p>Transaction </p>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
