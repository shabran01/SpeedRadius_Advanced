{include file="sections/header.tpl"}

<form class="form-horizontal" method="post" role="form" action="{$_url}paymentgateway/BankStkPush">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">Fill the details below to complete the bank STK Push</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Enter Bank account number</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="kopokopo_app_key" name="account" placeholder="*************************" value="{$_c['Stkbankacc']}">
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <label class="col-md-2 control-label">Bank Name</label>
                        <div class="col-md-6">
                            <select class="form-control" name="bankname" id="bankstk">
                                <option value="Equity" {if $_c['Stkbankname'] == 'Equity'}selected{/if}>Equity bank</option>
                                <option value="KCB" {if $_c['Stkbankname'] == 'KCB'}selected{/if}>Kenya Commercial Bank</option>
                                <option value="Coop" {if $_c['Stkbankname'] == 'Coop'}selected{/if}>Cooperative Bank of Kenya</option>
                                <option value="Absa" {if $_c['Stkbankname'] == 'Absa'}selected{/if}>Absa Bank Kenya</option>
                                <option value="DTB" {if $_c['Stkbankname'] == 'Dtb'}selected{/if}>Diamond Trust Bank (DTB)</option>
                                <option value="NCBA" {if $_c['Stkbankname'] == 'NCBA'}selected{/if}>NCBA Bank</option>
                                <option value="GAB" {if $_c['Stkbankname'] == 'GAB'}selected{/if}>GAB Bank</option> <!-- Added GAB option -->
                                <option value="Speedcom" {if $_c['Stkbankname'] == 'Speedcom'}selected{/if}>Speedcom</option> <!-- Added Speedcom option -->
                                <option value="StanChart" {if $_c['Stkbankname'] == 'StanChart'}selected{/if}>Standard Chartered Bank</option> <!-- Added StanChart -->
                                <option value="IM" {if $_c['Stkbankname'] == 'IM'}selected{/if}>I&M Bank Kenya</option> <!-- Added I&M Bank -->
                                <option value="NCBALoop" {if $_c['Stkbankname'] == 'NCBALoop'}selected{/if}>NCBA Loop Bank</option> <!-- Added NCBA Loop -->
                            </select>
                        </div>
                    </div>
                    <pre>After applying these changes, the funds shall be going to the saved bank account, please make sure the bank name and account match</pre>
                   
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">Save</button>
                        </div>
                    </div>
                        
                </div>
            </div>
        </div>
    </div>
</form>

{include file="sections/footer.tpl"}
