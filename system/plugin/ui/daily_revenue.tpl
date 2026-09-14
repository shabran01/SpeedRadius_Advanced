{include file="sections/header.tpl"}

<style>
    .revenue-preview {
        background: #fff;
        border: 2px solid #25D366;
        border-radius: 12px;
        max-width: 400px;
        margin: 0 auto 20px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        box-shadow: 0 4px 20px rgba(37,211,102,0.15);
    }
    .revenue-preview .preview-header {
        background: linear-gradient(135deg, #075e54, #128C7E);
        color: #fff;
        padding: 12px 16px;
        border-radius: 10px 10px 0 0;
        font-size: 12px;
        text-align: center;
    }
    .revenue-preview .preview-body {
        padding: 16px;
        white-space: pre-line;
        font-size: 13px;
        line-height: 1.6;
        color: #333;
        max-height: 500px;
        overflow-y: auto;
    }
    .stat-mini {
        text-align: center;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        color: #fff;
    }
    .stat-mini .big { font-size: 24px; font-weight: bold; }
    .stat-mini .lbl { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
    .bg-grad-green { background: linear-gradient(135deg, #11998e, #38ef7d); }
    .bg-grad-blue  { background: linear-gradient(135deg, #667eea, #764ba2); }
    .bg-grad-orange { background: linear-gradient(135deg, #f093fb, #f5576c); }
    .bg-grad-dark { background: linear-gradient(135deg, #434343, #000000); }
</style>

<div class="row">
    <div class="col-sm-8">
        <div class="panel panel-hovered mb20 panel-info">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a href="{$_url}plugin/daily_revenue/settings" class="btn btn-warning btn-xs">
                        <i class="fa fa-cog"></i> Settings
                    </a>
                    <a href="{$_url}plugin/daily_revenue/history" class="btn btn-default btn-xs">
                        <i class="fa fa-history"></i> History
                    </a>
                    <a href="{$_url}plugin/daily_revenue/preview" class="btn btn-default btn-xs">
                        <i class="fa fa-refresh"></i> Today
                    </a>
                </div>
                <i class="fa fa-money"></i> Daily Revenue Summary
            </div>
            <div class="panel-body">
                
                <form method="get" action="{$_url}plugin/daily_revenue/preview" class="form-inline" style="margin-bottom: 15px;">
                    <input type="hidden" name="_route" value="daily_revenue/preview">
                    <div class="form-group">
                        <label>View date:</label>
                        <input type="date" name="date" class="form-control" value="{$date}" style="margin-left: 8px;">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm" style="margin-left: 8px;">
                        <i class="fa fa-eye"></i> Preview
                    </button>
                    <a href="{$_url}plugin/daily_revenue/send_now&date={$date}" 
                       class="btn btn-success btn-sm" style="margin-left: 5px;"
                       onclick="return confirm('Send this summary via SMS, WhatsApp & Telegram now?');">
                        <i class="fa fa-paper-plane"></i> Send Now!
                    </a>
                </form>

                <div class="revenue-preview">
                    <div class="preview-header">
                        📱 WhatsApp Preview — {$date}
                    </div>
                    <div class="preview-body">{$message|nl2br}</div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="stat-mini bg-grad-green">
            <div class="big">{$settings['send_time']}</div>
            <div class="lbl">Scheduled Time</div>
        </div>
        <div class="stat-mini bg-grad-blue">
            <div class="big">{if $settings['enabled'] == 'yes'}✅ Active{else}❌ Disabled{/if}</div>
            <div class="lbl">Status</div>
        </div>
        <div class="stat-mini bg-grad-dark">
            <div class="big" style="font-size:16px;">{$settings['recipients']|default:'None'|truncate:30}</div>
            <div class="lbl">Recipients</div>
        </div>

        <div class="panel panel-hovered mb20 panel-default">
            <div class="panel-heading">
                <i class="fa fa-info-circle"></i> How It Works
            </div>
            <div class="panel-body">
                <ol>
                    <li>System calculates today's total revenue</li>
                    <li>Breaks it down by payment method</li>
                    <li>Shows top selling plans</li>
                    <li>Includes M-Pesa totals</li>
                    <li>Sends via <strong>SMS</strong> + <strong>WhatsApp</strong> + <strong>Telegram</strong></li>
                    <li>Auto-runs daily at your configured time</li>
                </ol>
                <p class="text-muted"><small>No more logging in to check — get revenue straight to your phone!</small></p>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
