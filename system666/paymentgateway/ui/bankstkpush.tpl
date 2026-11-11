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
                                <option value="Equity" {if $_c['Stkbankname'] == 'Equity'}selected{/if}>Equity Bank</option>
                                <option value="KCB" {if $_c['Stkbankname'] == 'KCB'}selected{/if}>Kenya Commercial Bank</option>
                                <option value="Coop" {if $_c['Stkbankname'] == 'Coop'}selected{/if}>Cooperative Bank of Kenya</option>
                                <option value="Absa" {if $_c['Stkbankname'] == 'Absa'}selected{/if}>Absa Bank Kenya</option>
                                <option value="DTB" {if $_c['Stkbankname'] == 'DTB'}selected{/if}>Diamond Trust Bank (DTB)</option>
                                <option value="NCBA" {if $_c['Stkbankname'] == 'NCBA'}selected{/if}>NCBA Bank</option>
                                <option value="GAB" {if $_c['Stkbankname'] == 'GAB'}selected{/if}>GAB Bank</option>
                                <option value="Speedcom" {if $_c['Stkbankname'] == 'Speedcom'}selected{/if}>Speedcom</option>
                                <option value="StanChart" {if $_c['Stkbankname'] == 'StanChart'}selected{/if}>Standard Chartered Bank</option>
                                <option value="I&M Bank Kenya" {if $_c['Stkbankname'] == 'I&M Bank Kenya'}selected{/if}>I&M Bank Kenya</option>
                                <option value="NCBA Loop" {if $_c['Stkbankname'] == 'NCBA Loop'}selected{/if}>NCBA Loop</option>
                                <option value="SasaPay" {if $_c['Stkbankname'] == 'SasaPay'}selected{/if}>SasaPay</option>
                                <option value="Family Bank" {if $_c['Stkbankname'] == 'Family Bank'}selected{/if}>Family Bank</option> <!-- Added Family Bank -->
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
