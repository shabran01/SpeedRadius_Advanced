{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">{Lang::T('Change Router and Plan')}</h3>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" role="form" action="{$_url}customers/change_router_post">
                    <input type="hidden" name="id" value="{$d['id']}">
                    <input type="hidden" name="csrf_token" value="{$csrf_token}">
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Current Router')}</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" value="{$current_router}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('New Router')}</label>
                        <div class="col-md-6">
                            <select name="router" class="form-control">
                                <option value="">{Lang::T('Select Router')}</option>
                                {foreach $routers as $router}
                                    <option value="{$router['name']}">{$router['name']}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Current Plan')}</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" value="{$current_plan}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('New Plan')}</label>
                        <div class="col-md-6">
                            <select name="plan" class="form-control">
                                <option value="">{Lang::T('Select Plan')}</option>
                                {foreach $plans as $plan}
                                    <option value="{$plan['id']}">{$plan['name_plan']}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-success" type="submit">{Lang::T('Change')}</button>
                            Or <a href="{$_url}customers/view/{$d['id']}">{Lang::T('Cancel')}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
