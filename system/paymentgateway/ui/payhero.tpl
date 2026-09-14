{include file="sections/header.tpl"}

<form class="form-horizontal" method="post" role="form" action="{$_url}paymentgateway/PayHero">
    <div class="row">
        <div class="col-sm-12 col-md-8 col-md-offset-2">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">
                    <b>Pay Hero - STK Push Payment Gateway</b>
                    <small class="pull-right">
                        <a href="https://docs.payhero.co.ke/" target="_blank" style="color:#fff;">
                            <i class="glyphicon glyphicon-question-sign"></i> Docs
                        </a>
                    </small>
                </div>
                <div class="panel-body">

                    <div class="alert alert-info">
                        <b>Setup Instructions:</b>
                        <ol style="margin:5px 0 0 0; padding-left:18px;">
                            <li>Login to your <a href="https://app.payhero.co.ke/" target="_blank">Pay Hero account</a></li>
                            <li>Go to <b>API Keys</b> &rarr; <b>Add new API Key</b> &rarr; copy the <b>Basic Authorization token</b></li>
                            <li>Go to <b>Payment Channels</b> &rarr; <b>My Payment Channels</b> &rarr; copy your <b>Channel ID</b></li>
                        </ol>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Basic Auth Token <span class="text-danger">*</span></label>
                        <div class="col-md-7">
                            <input type="text"
                                   class="form-control"
                                   name="payhero_auth_token"
                                   placeholder="Basic WXNmVjV..."
                                   value="{$_c['payhero_auth_token']}">
                            <p class="help-block">Found under <b>API Keys</b> in your Pay Hero dashboard. Paste the <b>full token including the word "Basic "</b>. Example: <code>Basic WXNmVjVSdHByTGx...</code></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Channel ID <span class="text-danger">*</span></label>
                        <div class="col-md-4">
                            <input type="number"
                                   class="form-control"
                                   name="payhero_channel_id"
                                   placeholder="e.g. 133"
                                   value="{$_c['payhero_channel_id']}">
                            <p class="help-block">Found under <b>Payment Channels &rarr; My Payment Channels</b></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Callback URL</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" readonly
                                   value="{$app_url}/?_route=callback/PayHero"
                                   onclick="this.select()">
                            <p class="help-block">Set this URL as your callback in the Pay Hero dashboard (optional — the gateway sends it automatically per transaction).</p>
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
