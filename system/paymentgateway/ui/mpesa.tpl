{include file="sections/header.tpl"}
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<style>
.styled-form-group {
    margin-bottom: 20px;
}

.styled-btn {
    color: #28a745;
    border: 1px solid #28a745;
    background-color: #fff;
    padding: 10px 20px;
    font-size: 16px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}

.styled-btn:hover {
    background-color: #28a745;
    color: #fff;
}

.styled-small-text {
    color: blue;
    margin-top: 10px;
    display: block;
    font-size: 14px;
}
</style>

<form class="form-horizontal" method="post" role="form" action="{$_url}paymentgateway/mpesa" >
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">M-Pesa</div>
                <div class="panel-body row">
                  <div class="form-group col-6">
                        <label class="col-md-3 control-label">M-Pesa Environment</label>
                        <div class="col-md-6">
                            <select class="form-control" name="mpesa_env">
                                {foreach $env as $environment}
                                    <option value="{$environment['id']}"
                                    {if $environment['id'] == $_c['mpesa_env']}selected{/if}
                                    >{$environment['id']} - {$environment['name']}</option>
                                {/foreach}
                            </select>
                            <small class="form-text text-muted"><font color="red"><b>Sandbox</b></font> is for testing purpose, please switch to <font color="green"><b>Live</b></font> in production.</small>
                        </div>
                    </div>

                    <div class="form-group col-6">
                        <label class="col-md-3 control-label">Consumer Key</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="mpesa_consumer_key" name="mpesa_consumer_key" placeholder="xxxxxxxxxxxxxxxxx" value="{$_c['mpesa_consumer_key']}">
                            <small class="form-text text-muted"><a href="https://developer.safaricom.co.ke/MyApps" target="_blank">https://developer.safaricom.co.ke/MyApps</a></small>
                        </div>
                    </div>

                    <div class="form-group col-6">
                        <label class="col-md-3 control-label">Consumer Secret</label>
                        <div class="col-md-6">
                            <input type="password" class="form-control" id="mpesa_consumer_secret" name="mpesa_consumer_secret" placeholder="xxxxxxxxxxxxxxxxx" value="{$_c['mpesa_consumer_secret']}">
                        </div>
                    </div>

                    <div class="form-group col-6">
                        <label class="col-md-3 control-label">Mpesa Shortcode Type</label>
                        <div class="col-md-6">
                            <select class="form-control" name="mpesa_shortcode_type" id="mpesa_shortcode_type">
                                <option value="Paybill" {if $_c['mpesa_shortcode_type'] == "Paybill"}selected{/if}>Paybill Number</option>
                                <option value="BuyGoods" {if $_c['mpesa_shortcode_type'] == "BuyGoods"}selected{/if}>BuyGoods Till Number</option>
                            </select>
                        </div>
                    </div>

                    {if $_c['mpesa_shortcode_type'] == "BuyGoods"}
                        <div class="form-group col-6" id="tillNumberContainer">
                            <label class="col-md-3 control-label">Mpesa BuyGoods Till Number</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" value="{$_c['mpesa_buygoods_till_number']}" name="mpesa_buygoods_till_number"  placeholder="Enter Till Number">
                            </div>
                        </div>
                    {else}
                        <div class="form-group col-6" id="tillNumberContainer" style="display: none;">
                            <label class="col-md-3 control-label">Mpesa BuyGoodsTill Number</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="mpesa_buygoods_till_number"  placeholder="Enter Till Number">
                            </div>
                        </div>
                    {/if}


                    




                    <div class="form-group col-6">
                        <label class="col-md-3 control-label">Business Shortcode</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="mpesa_business_code" name="mpesa_business_code" placeholder="xxxxxxx" maxlength="7" value="{$_c['mpesa_business_code']}">
                        </div>
                    </div>
					<div class="form-group col-6">
                        <label class="col-md-3 control-label">Pass Key</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="mpesa_pass_key" name="mpesa_pass_key" placeholder="bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919" maxlength="" value="{$_c['mpesa_pass_key']}">
                        </div>
                    </div>

                  

                    <div class="form-group col-6">
                        <label class="col-md-3 control-label">Support Offline Pay Methods</label>
                        <div class="col-md-6">
                            <select class="form-control" name="mpesa_channel_ofline_online" id="mpesa_channel_ofline_online">
                                <option value="0" {if $_c['mpesa_channel_ofline_online'] == 0}selected{/if}>No</option>
                                <option value="1" {if $_c['mpesa_channel_ofline_online'] == 1}selected{/if}>Yes</option>
                            </select>
                            <small class="form-text text-muted">Enable this if you want to support offline payment methods.</small>
                        </div>
                    </div>

                    <div id="offlinePayFields" style="display: none;">
                        <div class="form-group col-6">
                            <label class="col-md-3 control-label">C2B Version</label>
                            <div class="col-md-6">
                                <select class="form-control" name="mpesa_api_version">
                                    <option value="v1" {if $_c['mpesa_api_version'] == 'v1'}selected{/if}>v1</option>
                                    <option value="v2" {if $_c['mpesa_api_version'] == 'v2'}selected{/if}>v2</option>
                                </select>
                                <small class="form-text text-muted">Select the version of the API you want to use.</small>
                            </div>    
                        </div>

                        {if $_c['mpesa_channel_ofline_online'] == 1}
                            <div class="form-group col-12 styled-form-group">
                                <label class="col-md-3 control-label">Register Url</label>
                                <div class="col-md-6">
                                    <a href="{$_url}plugin/c2b&kind=register" class="btn styled-btn">Click to Register Mpesa C2B Url Support Offline Payment</a>
                                    <small class="form-text text-muted styled-small-text">Click only after you have saved the changes.</small>
                                </div>
                            </div>
                        {/if}

                    </div>

                    <div class="form-group col-6">
                        <div class="col-lg-offset-3 col-lg-10">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">Save Changes</button>
                        </div>
                    </div>


                        <pre>/ip hotspot walled-garden
                   add dst-host=safaricom.co.ke
                   add dst-host=*.safaricom.co.ke</pre>
                </div>
            </div>

        </div>
    </div>

</form>
<script>
        $(document).ready(function() {
            toggleOfflinePayFields();
            $('#mpesa_channel_ofline_online').on('change', function() {
                toggleOfflinePayFields();
            });
            function toggleOfflinePayFields() {
                if ($('#mpesa_channel_ofline_online').val() == '1') {
                    $('#offlinePayFields').show();
                } else {
                    $('#offlinePayFields').hide();
                }
            }
        });
</script>
<script>
        $(document).ready(function() {
            toggleTillNumberInput();
            $('#mpesa_shortcode_type').on('change', function() {
                toggleTillNumberInput();
            });
            function toggleTillNumberInput() {
                if ($('#mpesa_shortcode_type').val() === 'BuyGoods') {
                    $('#tillNumberContainer').show();
                } else {
                    $('#tillNumberContainer').hide();
                }
            }
        });
    </script>
{include file="sections/footer.tpl"}
