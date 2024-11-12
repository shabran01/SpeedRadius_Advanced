{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <!-- SMS Balance Panel -->
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                Blessedtexts SMS Balance
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Remaining Credit Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {if $credit_balance}
                                    <span class="text-success">Available</span>
                                {else if $credit_error_message}
                                    <span class="text-danger">Error</span>
                                {else}
                                    <span class="text-warning">Unknown</span>
                                {/if}
                            </td>
                            <td>
                                {if $credit_balance}
                                    {$credit_balance}
                                {else if $credit_error_message}
                                    <span class="error">{$credit_error_message}</span>
                                {else}
                                    Unable to retrieve credit balance at the moment.
                                {/if}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="panel-footer">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="api_key">API Key</label>
                        <input type="text" class="form-control" id="api_key" name="api_key" value="{$current_api_key}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save API Key</button>
                </form>
                {if $message}
                    <div class="alert alert-success mt-2">{$message}</div>
                {/if}
                <div class="bs-callout bs-callout-info" id="callout-navbar-role">
                    <h4>Top Up Your Balance</h4>
                    <p>You can top up your account by following the instructions on the portal.</p>
                    <a href="https://sms.blessedtexts.com/credit/topup" class="btn btn-primary">Top Up Balance</a>
                </div>
            </div>
        </div>

        <!-- Bytewave SMS Balance Panel -->
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                Bytewave SMS Balance
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Remaining SMS Units</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {if $sms_balance}
                                    <span class="text-success">Available</span>
                                {else if $sms_error_message}
                                    <span class="text-danger">Error</span>
                                {else}
                                    <span class="text-warning">Unknown</span>
                                {/if}
                            </td>
                            <td>
                                {if $sms_balance}
                                    {$sms_balance}
                                {else if $sms_error_message}
                                    <span class="error">{$sms_error_message}</span>
                                {else}
                                    Unable to retrieve SMS balance at the moment.
                                {/if}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="panel-footer">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="api_token">API Token</label>
                        <input type="text" class="form-control" id="api_token" name="api_token" value="{$current_api_token}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Save API Token</button>
                </form>
                {if $message}
                    <div class="alert alert-success mt-2">{$message}</div>
                {/if}
                <div class="bs-callout bs-callout-info" id="callout-navbar-role">
                    <h4>Top Up SMS Units</h4>
                    <p>You can top up your SMS units by clicking the button above.</p>
                    <a href="https://portal.bytewavenetworks.com/account/top-up" class="btn btn-primary">Top Up SMS Units</a>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
