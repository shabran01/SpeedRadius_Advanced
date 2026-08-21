{include file="sections/header.tpl"}

<style>
.service-type-tabs {
    display: flex;
    gap: 5px;
    margin-bottom: 15px;
    border-bottom: 2px solid #e0e0e0;
}
.service-tab {
    padding: 10px 20px;
    background: #f5f5f5;
    border: 1px solid #e0e0e0;
    border-bottom: none;
    cursor: pointer;
    border-radius: 5px 5px 0 0;
    transition: all 0.3s;
}
.service-tab.active {
    background: #3c8dbc;
    color: white;
    font-weight: bold;
}
.service-tab:hover:not(.active) {
    background: #e8e8e8;
}
.service-content {
    display: none;
}
.service-content.active {
    display: block;
}
.service-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    margin-left: 8px;
}
.badge-pppoe {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.badge-hotspot {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}
</style>

<form class="form-horizontal" method="post" role="form" action="{$_url}settings/notifications-post">
    <input type="hidden" name="csrf_token" value="{$csrf_token}">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">
                    <div class="btn-group pull-right">
                        <button class="btn btn-primary btn-xs" title="save" type="submit"><span
                                class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span></button>
                    </div>
                    {Lang::T('User Notification')}
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Expired Notification')}</label>
                        <div class="col-md-6">
                            <select class="form-control" id="expired_notification" name="expired_notification">
                                <option value="none" {if $_json['expired_notification'] eq 'none'}selected{/if}>{Lang::T('No Notification')}</option>
                                <option value="sms" {if $_json['expired_notification'] eq 'sms'}selected{/if}>{Lang::T('SMS Only')}</option>
                                <option value="wa" {if $_json['expired_notification'] eq 'wa'}selected{/if}>{Lang::T('WhatsApp Only')}</option>
                                <option value="both" {if $_json['expired_notification'] eq 'both'}selected{/if}>{Lang::T('Both SMS & WhatsApp')}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">Send To</label>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="send_expired_to_pppoe" value="1" {if $_json['send_expired_to_pppoe'] eq '1'}checked{/if}>
                                        <i class="fa fa-network-wired"></i> <strong>Send expiry notifications to PPPoE users</strong>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="send_expired_to_hotspot" value="1" {if $_json['send_expired_to_hotspot'] eq '1'}checked{/if}>
                                        <i class="fa fa-wifi"></i> <strong>Send expiry notifications to Hotspot users</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Expired Notification Message')}</label>
                        <div class="col-md-10">
                            <div class="service-type-tabs">
                                <div class="service-tab active" onclick="switchServiceTab(event, 'expired')">
                                    <i class="fa fa-network-wired"></i> PPPoE
                                    <span class="service-badge badge-pppoe">PPPOE</span>
                                </div>
                                <div class="service-tab" onclick="switchServiceTab(event, 'expired')">
                                    <i class="fa fa-wifi"></i> Hotspot
                                    <span class="service-badge badge-hotspot">HOTSPOT</span>
                                </div>
                            </div>
                            
                            <div class="service-content active" data-service="pppoe">
                                <textarea class="form-control" name="expired_pppoe"
                                    placeholder="{Lang::T('Hello')} [[name]], {Lang::T('your PPPoE package')} [[package]] {Lang::T('has been expired')}"
                                    rows="4">{if $_json['expired_pppoe']!=''}{Lang::htmlspecialchars($_json['expired_pppoe'])}{else}{if $_json['expired']!=''}{Lang::htmlspecialchars($_json['expired'])}{else}{Lang::T('Hello')} [[name]], {Lang::T('your PPPoE internet package')} [[package]] {Lang::T('has been expired')}.{/if}{/if}</textarea>
                                <p class="help-block">
                                    <strong><i class="fa fa-info-circle"></i> For PPPoE Users Only</strong><br>
                                    <b>[[name]]</b> - {Lang::T('Customer Name')} | 
                                    <b>[[username]]</b> - {Lang::T('Customer username')} | 
                                    <b>[[package]]</b> - {Lang::T('Package name')} | 
                                    <b>[[price]]</b> - {Lang::T('Package price')}<br>
                                    <b>[[bills]]</b> - {Lang::T('additional bills')} | 
                                    <b>[[payment_link]]</b> - <a href="./docs/#Reminder%20with%20payment%20link" target="_blank">payment link</a>
                                </p>
                            </div>
                            
                            <div class="service-content" data-service="hotspot">
                                <textarea class="form-control" name="expired_hotspot"
                                    placeholder="{Lang::T('Hello')} [[name]], {Lang::T('your Hotspot package')} [[package]] {Lang::T('has been expired')}"
                                    rows="4">{if $_json['expired_hotspot']!=''}{Lang::htmlspecialchars($_json['expired_hotspot'])}{else}{if $_json['expired']!=''}{Lang::htmlspecialchars($_json['expired'])}{else}{Lang::T('Hello')} [[name]], {Lang::T('your Hotspot internet package')} [[package]] {Lang::T('has been expired')}.{/if}{/if}</textarea>
                                <p class="help-block">
                                    <strong><i class="fa fa-info-circle"></i> For Hotspot Users Only</strong><br>
                                    <b>[[name]]</b> - {Lang::T('Customer Name')} | 
                                    <b>[[username]]</b> - {Lang::T('Customer username')} | 
                                    <b>[[package]]</b> - {Lang::T('Package name')} | 
                                    <b>[[price]]</b> - {Lang::T('Package price')}<br>
                                    <b>[[bills]]</b> - {Lang::T('additional bills')} | 
                                    <b>[[payment_link]]</b> - <a href="./docs/#Reminder%20with%20payment%20link" target="_blank">payment link</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Payment Notification')}</label>
                        <div class="col-md-6">
                            <select class="form-control" id="payment_notification" name="payment_notification">
                                <option value="none" {if $_json['payment_notification'] eq 'none'}selected{/if}>{Lang::T('No Notification')}</option>
                                <option value="sms" {if $_json['payment_notification'] eq 'sms'}selected{/if}>{Lang::T('SMS Only')}</option>
                                <option value="wa" {if $_json['payment_notification'] eq 'wa'}selected{/if}>{Lang::T('WhatsApp Only')}</option>
                                <option value="both" {if $_json['payment_notification'] eq 'both'}selected{/if}>{Lang::T('Both SMS & WhatsApp')}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">Send To</label>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="send_payment_to_pppoe" value="1" {if $_json['send_payment_to_pppoe'] eq '1'}checked{/if}>
                                        <i class="fa fa-network-wired"></i> <strong>Send payment confirmation SMS to PPPoE users</strong>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="send_payment_to_hotspot" value="1" {if $_json['send_payment_to_hotspot'] eq '1'}checked{/if}>
                                        <i class="fa fa-wifi"></i> <strong>Send payment confirmation SMS to Hotspot users</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Invoice Notification Payment')}</label>
                        <div class="col-md-6">
                            <textarea class="form-control" id="invoice_paid" name="invoice_paid"
                                placeholder="{Lang::T('Hello')} [[name]], {Lang::T('your internet package')} [[package]] {Lang::T('has been expired')}"
                                rows="20">{Lang::htmlspecialchars($_json['invoice_paid'])}</textarea>
                        </div>
                        <p class="col-md-4 help-block">
                            <b>[[company_name]]</b> {Lang::T('Your Company Name at Settings')}.<br>
                            <b>[[address]]</b> {Lang::T('Your Company Address at Settings')}.<br>
                            <b>[[phone]]</b> - {Lang::T('Your Company Phone at Settings')}.<br>
                            <b>[[invoice]]</b> - {Lang::T('Invoice number')}.<br>
                            <b>[[date]]</b> - {Lang::T('Date invoice created')}.<br>
                            <b>[[payment_gateway]]</b> - {Lang::T('Payment gateway user paid from')}.<br>
                            <b>[[payment_channel]]</b> - {Lang::T('Payment channel user paid from')}.<br>
                            <b>[[type]]</b> - {Lang::T('is Hotspot or PPPOE')}.<br>
                            <b>[[plan_name]]</b> - {Lang::T('Internet Package')}.<br>
                            <b>[[plan_price]]</b> - {Lang::T('Internet Package Prices')}.<br>
                            <b>[[name]]</b> - {Lang::T('Receiver name')}.<br>
                            <b>[[user_name]]</b> - {Lang::T('Username internet')}.<br>
                            <b>[[user_password]]</b> - {Lang::T('User password')}.<br>
                            <b>[[expired_date]]</b> - {Lang::T('Expired datetime')}.<br>
                            <b>[[footer]]</b> - {Lang::T('Invoice Footer')}.<br>
                            <b>[[note]]</b> - {Lang::T('For Notes by admin')}.<br>
                        </p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Reminder Notification')}</label>
                        <div class="col-md-6">
                            <select class="form-control" id="reminder_notification" name="reminder_notification">
                                <option value="none" {if $_json['reminder_notification'] eq 'none'}selected{/if}>{Lang::T('No Notification')}</option>
                                <option value="sms" {if $_json['reminder_notification'] eq 'sms'}selected{/if}>{Lang::T('SMS Only')}</option>
                                <option value="wa" {if $_json['reminder_notification'] eq 'wa'}selected{/if}>{Lang::T('WhatsApp Only')}</option>
                                <option value="both" {if $_json['reminder_notification'] eq 'both'}selected{/if}>{Lang::T('Both SMS & WhatsApp')}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">Send To</label>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="send_reminder_to_pppoe" value="1" {if $_json['send_reminder_to_pppoe'] eq '1'}checked{/if}>
                                        <i class="fa fa-network-wired"></i> <strong>Send expiry reminder notifications to PPPoE users</strong>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="send_reminder_to_hotspot" value="1" {if $_json['send_reminder_to_hotspot'] eq '1'}checked{/if}>
                                        <i class="fa fa-wifi"></i> <strong>Send expiry reminder notifications to Hotspot users</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Reminder 7 days')}</label>
                        <div class="col-md-10">
                            <div class="service-type-tabs">
                                <div class="service-tab active" onclick="switchServiceTab(event, 'reminder7')">
                                    <i class="fa fa-network-wired"></i> PPPoE
                                    <span class="service-badge badge-pppoe">PPPOE</span>
                                </div>
                                <div class="service-tab" onclick="switchServiceTab(event, 'reminder7')">
                                    <i class="fa fa-wifi"></i> Hotspot
                                    <span class="service-badge badge-hotspot">HOTSPOT</span>
                                </div>
                            </div>
                            
                            <div class="service-content active" data-service="pppoe">
                                <textarea class="form-control" name="reminder_7_day_pppoe" rows="4">{if $_json['reminder_7_day_pppoe']!=''}{Lang::htmlspecialchars($_json['reminder_7_day_pppoe'])}{else}{Lang::htmlspecialchars($_json['reminder_7_day'])}{/if}</textarea>
                                <p class="help-block"><strong><i class="fa fa-info-circle"></i> PPPoE Users - 7 days reminder</strong></p>
                            </div>
                            
                            <div class="service-content" data-service="hotspot">
                                <textarea class="form-control" name="reminder_7_day_hotspot" rows="4">{if $_json['reminder_7_day_hotspot']!=''}{Lang::htmlspecialchars($_json['reminder_7_day_hotspot'])}{else}{Lang::htmlspecialchars($_json['reminder_7_day'])}{/if}</textarea>
                                <p class="help-block"><strong><i class="fa fa-info-circle"></i> Hotspot Users - 7 days reminder</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Reminder 3 days')}</label>
                        <div class="col-md-10">
                            <div class="service-type-tabs">
                                <div class="service-tab active" onclick="switchServiceTab(event, 'reminder3')">
                                    <i class="fa fa-network-wired"></i> PPPoE
                                    <span class="service-badge badge-pppoe">PPPOE</span>
                                </div>
                                <div class="service-tab" onclick="switchServiceTab(event, 'reminder3')">
                                    <i class="fa fa-wifi"></i> Hotspot
                                    <span class="service-badge badge-hotspot">HOTSPOT</span>
                                </div>
                            </div>
                            
                            <div class="service-content active" data-service="pppoe">
                                <textarea class="form-control" name="reminder_3_day_pppoe" rows="4">{if $_json['reminder_3_day_pppoe']!=''}{Lang::htmlspecialchars($_json['reminder_3_day_pppoe'])}{else}{Lang::htmlspecialchars($_json['reminder_3_day'])}{/if}</textarea>
                                <p class="help-block"><strong><i class="fa fa-info-circle"></i> PPPoE Users - 3 days reminder</strong></p>
                            </div>
                            
                            <div class="service-content" data-service="hotspot">
                                <textarea class="form-control" name="reminder_3_day_hotspot" rows="4">{if $_json['reminder_3_day_hotspot']!=''}{Lang::htmlspecialchars($_json['reminder_3_day_hotspot'])}{else}{Lang::htmlspecialchars($_json['reminder_3_day'])}{/if}</textarea>
                                <p class="help-block"><strong><i class="fa fa-info-circle"></i> Hotspot Users - 3 days reminder</strong></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Reminder 1 day')}</label>
                        <div class="col-md-10">
                            <div class="service-type-tabs">
                                <div class="service-tab active" onclick="switchServiceTab(event, 'reminder1')">
                                    <i class="fa fa-network-wired"></i> PPPoE
                                    <span class="service-badge badge-pppoe">PPPOE</span>
                                </div>
                                <div class="service-tab" onclick="switchServiceTab(event, 'reminder1')">
                                    <i class="fa fa-wifi"></i> Hotspot
                                    <span class="service-badge badge-hotspot">HOTSPOT</span>
                                </div>
                            </div>
                            
                            <div class="service-content active" data-service="pppoe">
                                <textarea class="form-control" name="reminder_1_day_pppoe" rows="4">{if $_json['reminder_1_day_pppoe']!=''}{Lang::htmlspecialchars($_json['reminder_1_day_pppoe'])}{else}{Lang::htmlspecialchars($_json['reminder_1_day'])}{/if}</textarea>
                                <p class="help-block"><strong><i class="fa fa-info-circle"></i> PPPoE Users - 1 day reminder</strong></p>
                            </div>
                            
                            <div class="service-content" data-service="hotspot">
                                <textarea class="form-control" name="reminder_1_day_hotspot" rows="4">{if $_json['reminder_1_day_hotspot']!=''}{Lang::htmlspecialchars($_json['reminder_1_day_hotspot'])}{else}{Lang::htmlspecialchars($_json['reminder_1_day'])}{/if}</textarea>
                                <p class="help-block"><strong><i class="fa fa-info-circle"></i> Hotspot Users - 1 day reminder</strong></p>
                            </div>
                            <p class="help-block col-md-12">
                                <b>[[name]]</b> - Customer Name | 
                                <b>[[username]]</b> - Customer username | 
                                <b>[[package]]</b> - Package name | 
                                <b>[[price]]</b> - Package price<br>
                                <b>[[expired_date]]</b> - Expiration date | 
                                <b>[[bills]]</b> - additional bills | 
                                <b>[[payment_link]]</b> - <a href="./docs/#Reminder%20with%20payment%20link" target="_blank">payment link</a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Balance Notification Payment')}</label>
                        <div class="col-md-6">
                            <textarea class="form-control" id="invoice_balance" name="invoice_balance"
                                placeholder="{Lang::T('Hello')} [[name]], {Lang::T('your internet package')} [[package]] {Lang::T('has been expired')}"
                                rows="20">{Lang::htmlspecialchars($_json['invoice_balance'])}</textarea>
                        </div>
                        <p class="col-md-4 help-block">
                            <b>[[company_name]]</b> - {Lang::T('Your Company Name at Settings')}.<br>
                            <b>[[address]]</b> - {Lang::T('Your Company Address at Settings')}.<br>
                            <b>[[phone]]</b> - {Lang::T('Your Company Phone at Settings')}.<br>
                            <b>[[invoice]]</b> - {Lang::T('Invoice number')}.<br>
                            <b>[[date]]</b> - {Lang::T('Date invoice created')}.<br>
                            <b>[[payment_gateway]]</b> - {Lang::T('Payment gateway user paid from')}.<br>
                            <b>[[payment_channel]]</b> - {Lang::T('Payment channel user paid from')}.<br>
                            <b>[[type]]</b> - {Lang::T('is Hotspot or PPPOE')}.<br>
                            <b>[[plan_name]]</b> - {Lang::T('Internet Package')}.<br>
                            <b>[[plan_price]]</b> - {Lang::T('Internet Package Prices')}.<br>
                            <b>[[name]]</b> - {Lang::T('Receiver name')}.<br>
                            <b>[[user_name]]</b> - {Lang::T('Username internet')}.<br>
                            <b>[[user_password]]</b> - {Lang::T('User password')}.<br>
                            <b>[[trx_date]]</b> - {Lang::T('Transaction datetime')}.<br>
                            <b>[[balance_before]]</b> - {Lang::T('Balance Before')}.<br>
                            <b>[[balance]]</b> - {Lang::T('Balance After')}.<br>
                            <b>[[footer]]</b> - {Lang::T('Invoice Footer')}.
                        </p>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Welcome Message')}</label>
                        <div class="col-md-10">
                            <div class="service-type-tabs">
                                <div class="service-tab active" onclick="switchServiceTab(event, 'welcome')">
                                    <i class="fa fa-network-wired"></i> PPPoE
                                    <span class="service-badge badge-pppoe">PPPOE</span>
                                </div>
                                <div class="service-tab" onclick="switchServiceTab(event, 'welcome')">
                                    <i class="fa fa-wifi"></i> Hotspot
                                    <span class="service-badge badge-hotspot">HOTSPOT</span>
                                </div>
                            </div>
                            
                            <div class="service-content active" data-service="pppoe">
                                <textarea class="form-control" name="welcome_message_pppoe" rows="4">{if $_json['welcome_message_pppoe']!=''}{Lang::htmlspecialchars($_json['welcome_message_pppoe'])}{else}{Lang::htmlspecialchars($_json['welcome_message'])}{/if}</textarea>
                                <p class="help-block">
                                    <strong><i class="fa fa-info-circle"></i> For New PPPoE Customers</strong><br>
                                    <b>[[name]]</b> - Customer Name | 
                                    <b>[[username]]</b> - Customer username | 
                                    <b>[[password]]</b> - Customer password | 
                                    <b>[[url]]</b> - Portal URL | 
                                    <b>[[company]]</b> - Company Name
                                </p>
                            </div>
                            
                            <div class="service-content" data-service="hotspot">
                                <textarea class="form-control" name="welcome_message_hotspot" rows="4">{if $_json['welcome_message_hotspot']!=''}{Lang::htmlspecialchars($_json['welcome_message_hotspot'])}{else}{Lang::htmlspecialchars($_json['welcome_message'])}{/if}</textarea>
                                <p class="help-block">
                                    <strong><i class="fa fa-info-circle"></i> For New Hotspot Customers</strong><br>
                                    <b>[[name]]</b> - Customer Name | 
                                    <b>[[username]]</b> - Customer username | 
                                    <b>[[password]]</b> - Customer password | 
                                    <b>[[url]]</b> - Portal URL | 
                                    <b>[[company]]</b> - Company Name
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                {if $_c['enable_balance'] == 'yes'}
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label">{Lang::T('Send Balance')}</label>
                            <div class="col-md-6">
                                <textarea class="form-control" id="balance_send" name="balance_send"
                                    rows="4">{if $_json['balance_send']}{Lang::htmlspecialchars($_json['balance_send'])}{else}{Lang::htmlspecialchars($_default['balance_send'])}{/if}</textarea>
                            </div>
                            <p class="col-md-4 help-block">
                                <b>[[name]]</b> - {Lang::T('Receiver name')}.<br>
                                <b>[[balance]]</b> - {Lang::T('how much balance have been send')}.<br>
                                <b>[[current_balance]]</b> - {Lang::T('Current Balance')}.
                            </p>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label">{Lang::T('Received Balance')}</label>
                            <div class="col-md-6">
                                <textarea class="form-control" id="balance_received" name="balance_received"
                                    rows="4">{if $_json['balance_received']}{Lang::htmlspecialchars($_json['balance_received'])}{else}{Lang::htmlspecialchars($_default['balance_received'])}{/if}</textarea>
                            </div>
                            <p class="col-md-4 help-block">
                                <b>[[name]]</b> - {Lang::T('Sender name')}.<br>
                                <b>[[balance]]</b> - {Lang::T('how much balance have been received')}.<br>
                                <b>[[current_balance]]</b> - {Lang::T('Current Balance')}.
                            </p>
                        </div>
                    </div>
                {/if}
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <button class="btn btn-success btn-block" type="submit">{Lang::T('Save Changes')}</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function switchServiceTab(event, groupName) {
    event.preventDefault();
    
    // Get the clicked tab and its parent group
    const clickedTab = event.currentTarget;
    const tabGroup = clickedTab.parentElement;
    const contentGroup = tabGroup.nextElementSibling.parentElement;
    
    // Get all tabs and contents in this group
    const tabs = tabGroup.querySelectorAll('.service-tab');
    const contents = contentGroup.querySelectorAll('.service-content');
    
    // Determine which tab was clicked (0 = PPPoE, 1 = Hotspot)
    const tabIndex = Array.from(tabs).indexOf(clickedTab);
    
    // Remove active class from all tabs and contents
    tabs.forEach(tab => tab.classList.remove('active'));
    contents.forEach(content => content.classList.remove('active'));
    
    // Add active class to clicked tab and corresponding content
    clickedTab.classList.add('active');
    contents[tabIndex].classList.add('active');
}
</script>

{include file="sections/footer.tpl"}
