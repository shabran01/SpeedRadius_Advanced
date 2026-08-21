{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12 col-md-12">
        <form id="localisation_form" method="post" role="form" action="{$_url}settings/localisation-post">
            <input type="hidden" name="csrf_token" value="{$csrf_token}">

            <!-- ACTION BAR -->
            <div class="panel panel-default mb20">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h3 style="margin-top: 5px; margin-bottom: 5px;">{Lang::T('Localisation')}</h3>
                            <span class="text-muted">{Lang::T('Configure your regional settings, currency, and package names')}</span>
                        </div>
                        <div class="col-md-4 text-right">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-save"></i> {Lang::T('Save Changes')}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- REGIONAL SETTINGS -->
                <div class="col-md-4">
                    <div class="panel panel-primary panel-hovered">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-globe"></i> {Lang::T('Regional Settings')}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label><i class="fa fa-clock-o"></i> {Lang::T('Timezone')}</label>
                                <select name="tzone" id="tzone" class="form-control select2">
                                    {foreach $tlist as $value => $label}
                                        <option value="{$value}" {if $_c['timezone'] eq $value}selected="selected" {/if}>
                                            {$label}
                                        </option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-calendar"></i> {Lang::T('Date Format')}</label>
                                <select class="form-control" name="date_format" id="date_format">
                                    <option value="d/m/Y" {if $_c['date_format'] eq 'd/m/Y'} selected="selected" {/if}>{date('d/m/Y')}</option>
                                    <option value="d.m.Y" {if $_c['date_format'] eq 'd.m.Y'} selected="selected" {/if}>{date('d.m.Y')}</option>
                                    <option value="d-m-Y" {if $_c['date_format'] eq 'd-m-Y'} selected="selected" {/if}>{date('d-m-Y')}</option>
                                    <option value="m/d/Y" {if $_c['date_format'] eq 'm/d/Y'} selected="selected" {/if}>{date('m/d/Y')}</option>
                                    <option value="Y/m/d" {if $_c['date_format'] eq 'Y/m/d'} selected="selected" {/if}>{date('Y/m/d')}</option>
                                    <option value="Y-m-d" {if $_c['date_format'] eq 'Y-m-d'} selected="selected" {/if}>{date('Y-m-d')}</option>
                                    <option value="M d Y" {if $_c['date_format'] eq 'M d Y'} selected="selected" {/if}>{date('M d Y')}</option>
                                    <option value="d M Y" {if $_c['date_format'] eq 'd M Y'} selected="selected" {/if}>{date('d M Y')}</option>
                                    <option value="jS M y" {if $_c['date_format'] eq 'jS M y'} selected="selected" {/if}>{date('jS M y')}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-language"></i> {Lang::T('Default Language')}</label>
                                <select class="form-control" name="lan" id="lan">
                                    {foreach $lani as $lanis}
                                        <option value="{$lanis@key}" {if $_c['language'] eq $lanis@key} selected="selected" {/if}>
                                            {$lanis@key}
                                        </option>
                                    {/foreach}
                                    <option disabled>_________</option>
                                    {foreach $lan as $lans}
                                        <option value="{$lans@key}" {if $_c['language'] eq $lans@key} selected="selected" {/if}>
                                            {$lans@key}
                                        </option>
                                    {/foreach}
                                </select>
                                <p class="help-block"><a href="{$_url}settings/language">{Lang::T('Language Editor')}</a></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CURRENCY SETTINGS -->
                <div class="col-md-4">
                    <div class="panel panel-info panel-hovered">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-money"></i> {Lang::T('Currency & Format')}</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label>{Lang::T('Currency Code')}</label>
                                <input type="text" class="form-control" id="currency_code" name="currency_code" value="{$_c['currency_code']}" placeholder="USD, IDR, etc">
                                <span class="help-block">{Lang::T('Keep it blank to hide')}</span>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{Lang::T('Decimal Point')}</label>
                                        <input type="text" class="form-control" id="dec_point" name="dec_point" value="{$_c['dec_point']}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{Lang::T('Thousands Sep')}</label>
                                        <input type="text" class="form-control" id="thousands_sep" name="thousands_sep" value="{$_c['thousands_sep']}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-phone"></i> {Lang::T('Country Code Phone')}</label>
                                <div class="input-group">
                                    <span class="input-group-addon">+</span>
                                    <input type="text" class="form-control" id="country_code_phone" placeholder="62" name="country_code_phone" value="{$_c['country_code_phone']}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PACKAGE NAMING -->
                <div class="col-md-4">
                    <div class="panel panel-success panel-hovered">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-tags"></i> {Lang::T('Package Naming')}</h3>
                        </div>
                        <div class="panel-body">
                            <span class="help-block" style="margin-top: 0;">{Lang::T('Customize plan titles in user order pages')}</span>
                            <div class="form-group">
                                <label>Hotspot</label>
                                <input type="text" class="form-control" id="hotspot_plan" name="hotspot_plan" value="{if $_c['hotspot_plan']==''}Hotspot Plan{else}{$_c['hotspot_plan']}{/if}">
                            </div>
                            <div class="form-group">
                                <label>PPPoE</label>
                                <input type="text" class="form-control" id="pppoe_plan" name="pppoe_plan" value="{if $_c['pppoe_plan']==''}PPPOE Plan{else}{$_c['pppoe_plan']}{/if}">
                            </div>
                            <div class="form-group">
                                <label>VPN</label>
                                <input type="text" class="form-control" id="vpn_plan" name="vpn_plan" value="{if $_c['vpn_plan']==''}VPN Plan{else}{$_c['vpn_plan']}{/if}">
                            </div>
                            <div class="form-group">
                                <label>Radius</label>
                                <input type="text" class="form-control" id="radius_plan" name="radius_plan" value="{if $_c['radius_plan']==''}Radius Plan{else}{$_c['radius_plan']}{/if}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{include file="sections/footer.tpl"}
