{include file="sections/header.tpl"}

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-lightbulb-o"></i> SpeedRad Expert AI — Configuration</h3>
            </div>
            <div class="panel-body">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>How it works:</strong> This AI uses the DeepSeek API to answer questions about SpeedRadius. It has complete knowledge of the system embedded in its prompt — every feature, workflow, database table, and troubleshooting step.
                </div>

                <form method="post" action="{$_url}plugin/spexpert_ai/config">
                    <div class="form-group">
                        <label>DeepSeek API Key</label>
                        <input type="password" class="form-control" name="deepseek_api_key"
                            value="{$_c['deepseek_api_key']}"
                            onmouseenter="this.type='text'" onmouseleave="this.type='password'"
                            placeholder="sk-...">
                        <p class="help-block">
                            Get your free API key at <a href="https://platform.deepseek.com/api_keys" target="_blank">platform.deepseek.com</a>.
                            New accounts get free credits.
                        </p>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-save"></i> Save API Key
                    </button>
                </form>

                <hr>

                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i>
                    <strong>Shared with AI Assistant:</strong> This uses the same <code>deepseek_api_key</code> as the general AI Assistant plugin. If you already configured it there, no need to set it again.
                </div>

                <a href="{$_url}plugin/spexpert_ai" class="btn btn-default btn-block">
                    <i class="fa fa-arrow-left"></i> Back to Chat
                </a>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
