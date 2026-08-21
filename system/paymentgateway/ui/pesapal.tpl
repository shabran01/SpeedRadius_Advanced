{include file="sections/header.tpl"}

<form class="form-horizontal" method="post" role="form" action="{$_url}paymentgateway/PesaPal">
    <div class="row">
        <div class="col-sm-12 col-md-8 col-md-offset-2">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">
                    <b>PesaPal - Payment Gateway (API v3)</b>
                    <small class="pull-right">
                        <a href="https://developer.pesapal.com/how-to-integrate/e-commerce/api-30-json/api-reference" target="_blank" style="color:#fff;">
                            <i class="glyphicon glyphicon-question-sign"></i> Docs
                        </a>
                    </small>
                </div>
                <div class="panel-body">

                    <div class="alert alert-info">
                        <b>Setup Instructions:</b>
                        <ol style="margin:5px 0 0 0; padding-left:18px;">
                            <li>Login to your <a href="https://pay.pesapal.com/dashboard/account/login" target="_blank">PesaPal Live account</a></li>
                            <li>Go to <b>Integrations &rarr; API Keys</b> &rarr; copy your <b>Consumer Key</b> and <b>Consumer Secret</b></li>
                            <li>Paste the <b>IPN / Callback URL</b> below into PesaPal under <b>Integrations &rarr; IPN Settings</b> &rarr; click Register</li>
                            <li>Copy the generated <b>IPN ID</b> and paste it in the field below (or leave blank — it will auto-register on the first payment)</li>
                        </ol>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Consumer Key <span class="text-danger">*</span></label>
                        <div class="col-md-7">
                            <input type="text"
                                   class="form-control"
                                   name="pesapal_consumer_key"
                                   placeholder="Your PesaPal consumer_key"
                                   value="{$_c['pesapal_consumer_key']}">
                            <p class="help-block">Found under <b>Integrations &rarr; API Keys</b> in your PesaPal merchant dashboard.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Consumer Secret <span class="text-danger">*</span></label>
                        <div class="col-md-7">
                            <input type="password"
                                   class="form-control"
                                   name="pesapal_consumer_secret"
                                   placeholder="Your PesaPal consumer_secret"
                                   value="{$_c['pesapal_consumer_secret']}">
                            <p class="help-block">Found alongside your Consumer Key. Keep this private.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Environment</label>
                        <div class="col-md-4">
                            <select name="pesapal_environment" class="form-control">
                                <option value="live" {if $config['pesapal_environment'] == 'live' || empty($config['pesapal_environment'])}selected{/if}>
                                    Live (Production — pay.pesapal.com)
                                </option>
                                <option value="sandbox" {if $config['pesapal_environment'] == 'sandbox'}selected{/if}>
                                    Sandbox (Testing — cybqa.pesapal.com)
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">IPN / Callback URL</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" readonly
                                   value="{$app_url}/?_route=callback/PesaPal"
                                   onclick="this.select()">
                            <p class="help-block">
                                Register this URL in your PesaPal dashboard: <b>Integrations &rarr; IPN Settings</b>.<br>
                                Or use the online form:
                                <a href="https://pay.pesapal.com/iframe/PesapalIframe3/IpnRegistration" target="_blank">
                                    Live IPN Registration &rarr;
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">IPN ID</label>
                        <div class="col-md-7">
                            <input type="text"
                                   class="form-control"
                                   name="pesapal_ipn_id"
                                   placeholder="e.g. 84740ab4-3cd9-47da-8a4f-dd1db53494b5"
                                   value="{$_c['pesapal_ipn_id']}">
                            <p class="help-block">
                                The GUID returned after registering the IPN URL above. <b>Leave blank</b> and it will be auto-registered on the first payment attempt. Once populated, do not clear it.
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
