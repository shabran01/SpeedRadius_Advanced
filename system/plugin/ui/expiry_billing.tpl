{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                Expiry Billing Details
            </div>
            <div class="panel-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="expiry_date">Expiry Date</label>
                        <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="{$current_expiry_date|date_format:'%Y-%m-%d'}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Expiry Date</button>
                </form>

                {if $message}
                    <div class="alert alert-success mt-2">{$message}</div>
                {/if}

                <div class="mt-4">
                    <h4>Time Left Until Expiry:</h4>
                    {if $time_left > 0}
                        <p>
                            {assign var="days" value=$time_left / (60 * 60 * 24)}
                            {assign var="hours" value=($time_left % (60 * 60 * 24)) / (60 * 60)}
                            {assign var="minutes" value=($time_left % (60 * 60)) / 60}
                            {assign var="seconds" value=$time_left % 60}
                            {$days|round:0} Days, {$hours|round:0} Hours, {$minutes|round:0} Minutes, {$seconds|round:0} Seconds
                        </p>
                    {else}
                        <p class="text-danger">Your billing has expired.</p>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
