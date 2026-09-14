{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-8">
        <div class="panel panel-hovered mb20 panel-info">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a href="{$_url}plugin/daily_revenue/preview" class="btn btn-default btn-xs">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
                <i class="fa fa-cog"></i> Daily Revenue - Settings
            </div>
            <div class="panel-body">
                <form method="post" action="{$_url}plugin/daily_revenue/save_settings" class="form-horizontal">
                    
                    <div class="form-group">
                        <label class="col-md-4 control-label">Enable Auto-Send</label>
                        <div class="col-md-4">
                            <select name="dr_enabled" class="form-control">
                                <option value="yes" {if $settings['enabled'] == 'yes'}selected{/if}>✅ Yes — Send daily</option>
                                <option value="no" {if $settings['enabled'] == 'no'}selected{/if}>❌ No — Manual only</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Send Time (Daily)</label>
                        <div class="col-md-3">
                            <input type="time" name="dr_send_time" class="form-control" 
                                   value="{$settings['send_time']}" required>
                            <span class="help-block">e.g., 21:00 = 9 PM daily</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Timezone Offset</label>
                        <div class="col-md-3">
                            <select name="dr_tz_offset" class="form-control">
                                <option value="+03:00" {if $settings['tz_offset'] == '+03:00'}selected{/if}>EAT (+03:00) — Nairobi</option>
                                <option value="+00:00" {if $settings['tz_offset'] == '+00:00'}selected{/if}>UTC (+00:00)</option>
                                <option value="+01:00" {if $settings['tz_offset'] == '+01:00'}selected{/if}>CET (+01:00)</option>
                                <option value="+02:00" {if $settings['tz_offset'] == '+02:00'}selected{/if}>EET (+02:00)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Recipient Phone Numbers</label>
                        <div class="col-md-6">
                            <textarea name="dr_recipients" class="form-control" rows="3" 
                                      placeholder="0712345678,0723456789,0734567890">{$settings['recipients']}</textarea>
                            <span class="help-block">Comma-separated phone numbers. SMS + WhatsApp will be sent to each.</span>
                        </div>
                    </div>

                    <hr>

                    <h5>Report Content</h5>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Include M-Pesa Breakdown</label>
                        <div class="col-md-4">
                            <select name="dr_include_mpesa" class="form-control">
                                <option value="yes" {if $settings['include_mpesa'] == 'yes'}selected{/if}>Yes</option>
                                <option value="no" {if $settings['include_mpesa'] == 'no'}selected{/if}>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Include Top Plans</label>
                        <div class="col-md-4">
                            <select name="dr_include_plans" class="form-control">
                                <option value="yes" {if $settings['include_plans'] == 'yes'}selected{/if}>Yes — Show top 5</option>
                                <option value="no" {if $settings['include_plans'] == 'no'}selected{/if}>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Include Transaction Count</label>
                        <div class="col-md-4">
                            <select name="dr_include_count" class="form-control">
                                <option value="yes" {if $settings['include_count'] == 'yes'}selected{/if}>Yes</option>
                                <option value="no" {if $settings['include_count'] == 'no'}selected{/if}>No</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <div class="col-md-offset-4 col-md-4">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-save"></i> Save Settings
                            </button>
                        </div>
                    </div>

                    {if $settings['last_sent']}
                        <div class="form-group">
                            <div class="col-md-offset-4 col-md-6">
                                <span class="text-success">
                                    <i class="fa fa-check-circle"></i> Last auto-sent: {$settings['last_sent']}
                                </span>
                            </div>
                        </div>
                    {/if}

                </form>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="panel panel-hovered mb20 panel-default">
            <div class="panel-heading">
                <i class="fa fa-clock-o"></i> How Cron Works
            </div>
            <div class="panel-body">
                <p>The plugin uses your existing <code>cron.php</code> schedule.</p>
                <p>When cron runs and the current time matches (or is within 10 minutes of) your configured send time, the summary is sent.</p>
                <p><strong>Example:</strong> If send time is <code>21:00</code> and cron runs at <code>21:02</code>, it will send.</p>
                <p class="text-muted"><small>Make sure your cron runs at least every 5 minutes for accurate timing.</small></p>
            </div>
        </div>

        <div class="panel panel-hovered mb20 panel-success">
            <div class="panel-heading">
                <i class="fa fa-check-circle"></i> What Gets Sent
            </div>
            <div class="panel-body">
                <ul>
                    <li>📱 <strong>SMS</strong> to all configured numbers</li>
                    <li>💬 <strong>WhatsApp</strong> to all configured numbers</li>
                    <li>📢 <strong>Telegram</strong> to configured admin bot</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
