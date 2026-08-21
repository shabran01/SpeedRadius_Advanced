{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12 col-md-10 col-md-offset-1">

        <div class="panel panel-primary panel-hovered mb20">
            <div class="panel-heading">
                <div class="pull-right">
                    <a href="{$_url}plugin/marketing_scheduler" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Campaigns
                    </a>
                </div>
                <i class="fa fa-calendar-plus-o"></i>
                {if $campaign}Edit Campaign{else}New Marketing Campaign{/if}
            </div>
            <div class="panel-body">

                <form method="post"
                      action="{$_url}plugin/marketing_scheduler/save{if $campaign}/{$campaign.id}{/if}"
                      class="form-horizontal">

                    <!-- Campaign Title -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Campaign Title <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="title"
                                   placeholder="e.g. Friday Morning Promo"
                                   value="{if $campaign}{$campaign.title}{/if}" required>
                        </div>
                    </div>

                    <!-- Scheduled Date & Time -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Scheduled Date &amp; Time <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <input type="datetime-local" class="form-control" name="scheduled_at"
                                   value="{if $campaign}{$campaign.scheduled_at_local}{/if}" required>
                            <span class="help-block">
                                <i class="fa fa-clock-o"></i> Server current time: <strong>{$server_time}</strong>
                                &nbsp;— schedule in the future for auto-send via cron.
                            </span>
                        </div>
                    </div>

                    <!-- Target Group -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Target Group <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <select class="form-control" name="group_filter">
                                <option value="all"            {if !$campaign || $campaign.group_filter=='all'}selected{/if}>All Customers</option>
                                <option value="new"            {if $campaign && $campaign.group_filter=='new'}selected{/if}>New Customers (last 30 days)</option>
                                <option value="expired"        {if $campaign && $campaign.group_filter=='expired'}selected{/if}>Expired Customers</option>
                                <option value="active"         {if $campaign && $campaign.group_filter=='active'}selected{/if}>Active Customers</option>
                                <option value="active_pppoe"   {if $campaign && $campaign.group_filter=='active_pppoe'}selected{/if}>Active PPPOE</option>
                                <option value="expired_pppoe"  {if $campaign && $campaign.group_filter=='expired_pppoe'}selected{/if}>Expired PPPOE</option>
                                <option value="all_pppoe"      {if $campaign && $campaign.group_filter=='all_pppoe'}selected{/if}>All PPPOE</option>
                                <option value="active_hotspot" {if $campaign && $campaign.group_filter=='active_hotspot'}selected{/if}>Active Hotspot</option>
                                <option value="expired_hotspot"{if $campaign && $campaign.group_filter=='expired_hotspot'}selected{/if}>Expired Hotspot</option>
                                <option value="all_hotspot"    {if $campaign && $campaign.group_filter=='all_hotspot'}selected{/if}>All Hotspot</option>
                            </select>
                        </div>
                    </div>

                    <!-- Router Filter -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Router <span class="text-muted">(optional)</span></label>
                        <div class="col-md-8">
                            <select class="form-control" name="router_filter">
                                <option value="">All Routers</option>
                                {foreach $routers as $router}
                                    <option value="{$router.name}"
                                        {if $campaign && $campaign.router_filter == $router.name}selected{/if}>
                                        {$router.name}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                    <!-- Send Via -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Send Via <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <select class="form-control" name="via">
                                <option value="sms"  {if !$campaign || $campaign.via=='sms'}selected{/if}>SMS</option>
                                <option value="wa"   {if $campaign && $campaign.via=='wa'}selected{/if}>WhatsApp</option>
                                <option value="both" {if $campaign && $campaign.via=='both'}selected{/if}>SMS and WhatsApp</option>
                            </select>
                        </div>
                    </div>

                    <!-- Batch Size -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Batch Size</label>
                        <div class="col-md-8">
                            <select class="form-control" name="batch_size">
                                {foreach [10, 20, 50, 100, 300, 500, 1000] as $bs}
                                    <option value="{$bs}"
                                        {if $campaign && $campaign.batch_size == $bs}selected
                                        {elseif !$campaign && $bs == 50}selected{/if}>
                                        {$bs} messages per batch
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                    <!-- Delay Between Batches -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Delay Between Batches</label>
                        <div class="col-md-8">
                            <select class="form-control" name="delay_seconds">
                                <option value="0"  {if !$campaign || $campaign.delay_seconds==0}selected{/if}>No Delay</option>
                                <option value="3"  {if $campaign && $campaign.delay_seconds==3}selected{/if}>3 Seconds</option>
                                <option value="5"  {if $campaign && $campaign.delay_seconds==5}selected{/if}>5 Seconds</option>
                                <option value="10" {if $campaign && $campaign.delay_seconds==10}selected{/if}>10 Seconds</option>
                                <option value="20" {if $campaign && $campaign.delay_seconds==20}selected{/if}>20 Seconds</option>
                                <option value="30" {if $campaign && $campaign.delay_seconds==30}selected{/if}>30 Seconds</option>
                            </select>
                            <span class="help-block">Pause between batches to avoid SMS/WhatsApp provider rate limits.</span>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Message <span class="text-danger">*</span></label>
                        <div class="col-md-8">
                            <textarea class="form-control" name="message" rows="6"
                                      placeholder="Type your promotional message here..." required>{if $campaign}{$campaign.message}{/if}</textarea>
                            <div style="margin-top:8px;">
                                <small class="text-muted">
                                    <strong>Placeholders:</strong>
                                    <code>[[name]]</code> Customer Name &nbsp;&bull;&nbsp;
                                    <code>[[user_name]]</code> Username &nbsp;&bull;&nbsp;
                                    <code>[[phone]]</code> Phone &nbsp;&bull;&nbsp;
                                    <code>[[company_name]]</code> Company Name
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="form-group">
                        <div class="col-md-8 col-md-offset-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i>
                                {if $campaign}Update Campaign{else}Schedule Campaign{/if}
                            </button>
                            <a href="{$_url}plugin/marketing_scheduler" class="btn btn-default">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>

                </form>

            </div><!-- /panel-body -->
        </div><!-- /panel -->
    </div>
</div>

{include file="sections/footer.tpl"}
