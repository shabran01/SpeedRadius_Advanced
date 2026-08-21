{include file="sections/header.tpl"}

<form class="form-horizontal" method="post" role="form" action="{$_url}paymentgateway/ModemPay">
    <div class="row">
        <div class="col-sm-12 col-md-8 col-md-offset-2">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">
                    <b>ModemPay - Payment Gateway</b>
                    <small class="pull-right">
                        <a href="https://docs.modempay.com/" target="_blank" style="color:#fff;">
                            <i class="glyphicon glyphicon-question-sign"></i> Docs
                        </a>
                    </small>
                </div>
                <div class="panel-body">

                    <div class="alert alert-info">
                        <b>Setup Instructions:</b>
                        <ol style="margin:5px 0 0 0; padding-left:18px;">
                            <li>Register at <a href="https://merchant.modempay.com/" target="_blank">merchant.modempay.com</a></li>
                            <li>Go to <b>Developers &rarr; API Keys</b> and copy your <b>Secret Key</b></li>
                            <li>Go to <b>Developers &rarr; Webhooks</b>, add your <b>Webhook URL</b> below, and copy the <b>Webhook Secret</b></li>
                            <li>Complete <b>KYC</b> on the dashboard before going live</li>
                        </ol>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Secret API Key <span class="text-danger">*</span></label>
                        <div class="col-md-7">
                            <input type="text"
                                   class="form-control"
                                   name="modempay_secret_key"
                                   placeholder="sk_live_... or sk_test_..."
                                   value="{$_c['modempay_secret_key']}">
                            <p class="help-block">
                                Found under <b>Developers &rarr; API Keys</b> in your ModemPay dashboard.
                                Use <code>sk_test_...</code> for testing and <code>sk_live_...</code> for production.
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Webhook Secret</label>
                        <div class="col-md-7">
                            <input type="text"
                                   class="form-control"
                                   name="modempay_webhook_secret"
                                   placeholder="whsec_..."
                                   value="{$_c['modempay_webhook_secret']}">
                            <p class="help-block">
                                Found under <b>Developers &rarr; Webhooks</b>. Used to verify webhook authenticity via HMAC-SHA512.
                                <strong>Recommended</strong> for security — leave blank to skip signature validation.
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Currency <span class="text-danger">*</span></label>
                        <div class="col-md-3">
                            <input type="text"
                                   class="form-control"
                                   name="modempay_currency"
                                   placeholder="e.g. GMD, KES, USD"
                                   value="{$_c['modempay_currency']}">
                            <p class="help-block">ISO 4217 currency code supported by your ModemPay account.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Environment <span class="text-danger">*</span></label>
                        <div class="col-md-4">
                            <select class="form-control" name="modempay_environment">
                                <option value="live" {if $_c['modempay_environment'] == 'live'}selected{/if}>Live</option>
                                <option value="test" {if $_c['modempay_environment'] == 'test'}selected{/if}>Test / Sandbox</option>
                            </select>
                            <p class="help-block">
                                In <b>Test</b> mode, use a <code>sk_test_...</code> key and transactions go to the sandbox checkout.
                                In <b>Live</b> mode, use a <code>sk_live_...</code> key for real payments.
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Webhook URL</label>
                        <div class="col-md-7">
                            <input type="text"
                                   class="form-control"
                                   readonly
                                   value="{$app_url}/?_route=callback/ModemPay"
                                   onclick="this.select()">
                            <p class="help-block">
                                Add this URL in your ModemPay dashboard under <b>Developers &rarr; Webhooks</b>.
                                Subscribe to the <code>charge.succeeded</code> event.
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-offset-3 col-lg-9">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">
                                <i class="glyphicon glyphicon-floppy-disk"></i> Save Settings
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>

{include file="sections/footer.tpl"}
