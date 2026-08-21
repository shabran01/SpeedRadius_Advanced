{include file="sections/header.tpl"}

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">

                <div class="panel panel-default" style="border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.10)">
                    <div class="panel-heading" style="background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);padding:20px 24px;border:none">
                        <div style="display:flex;align-items:center;gap:14px">
                            <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff">
                                <i class="ion ion-chatbubbles"></i>
                            </div>
                            <div>
                                <div style="color:#fff;font-weight:700;font-size:17px">AI Assistant — Configuration</div>
                                <div style="color:#8090a0;font-size:12px">Configure your DeepSeek API connection</div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-body" style="padding:28px">

                        <form method="POST" action="{$_url}plugin/deepseek_ai/config">
                            <input type="hidden" name="token" value="{$csrf_token}">

                            <!-- API Key -->
                            <div class="form-group">
                                <label style="font-weight:600;color:#333">
                                    <i class="fa fa-key" style="color:#667eea"></i>
                                    DeepSeek API Key <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" id="apiKeyInput" name="deepseek_api_key"
                                           class="form-control"
                                           placeholder="{if !empty($config.deepseek_api_key)}••••••••••••••••••••• (saved){else}sk-xxxxxxxxxxxxxxxxxxxxxxxx{/if}"
                                           autocomplete="new-password">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" onclick="toggleKey()" title="Show/hide key">
                                            <i class="fa fa-eye" id="eyeIcon"></i>
                                        </button>
                                    </span>
                                </div>
                                <p class="help-block" style="margin-top:6px">
                                    Get your API key from
                                    <a href="https://platform.deepseek.com/api_keys" target="_blank" rel="noopener noreferrer">
                                        platform.deepseek.com <i class="fa fa-external-link"></i>
                                    </a>.
                                    Leave blank to keep the current key.
                                </p>
                            </div>

                            <!-- Model -->
                            <div class="form-group">
                                <label style="font-weight:600;color:#333">
                                    <i class="fa fa-microchip" style="color:#667eea"></i>
                                    Model
                                </label>
                                <select name="deepseek_model" class="form-control">
                                    <option value="deepseek-chat" {if empty($config.deepseek_model) || $config.deepseek_model == 'deepseek-chat'}selected{/if}>
                                        deepseek-chat (Recommended — fast &amp; capable)
                                    </option>
                                    <option value="deepseek-reasoner" {if $config.deepseek_model == 'deepseek-reasoner'}selected{/if}>
                                        deepseek-reasoner (Advanced reasoning, slower)
                                    </option>
                                </select>
                                <p class="help-block"><code>deepseek-chat</code> is best for support and general questions.</p>
                            </div>

                            <!-- System Prompt -->
                            <div class="form-group">
                                <label style="font-weight:600;color:#333">
                                    <i class="fa fa-comment" style="color:#667eea"></i>
                                    System Prompt (Optional)
                                </label>
                                <textarea name="deepseek_system_prompt" class="form-control" rows="5"
                                          placeholder="You are a helpful ISP billing assistant for SpeedRadius...">{if !empty($config.deepseek_system_prompt)}{$config.deepseek_system_prompt}{/if}</textarea>
                                <p class="help-block">
                                    Defines the AI's personality and context. Leave empty to use the default ISP-focused prompt.
                                </p>
                            </div>

                            <!-- Divider -->
                            <hr style="margin:20px 0">

                            <!-- Info box -->
                            <div style="background:#f0f4ff;border:1px solid #c5d4ff;border-radius:8px;padding:14px 16px;margin-bottom:20px;font-size:13px">
                                <div style="font-weight:600;color:#3a56c5;margin-bottom:6px">
                                    <i class="fa fa-info-circle"></i> DeepSeek API Pricing
                                </div>
                                <div style="color:#555;line-height:1.7">
                                    • <strong>deepseek-chat</strong>: ~$0.14 / 1M input tokens, ~$0.28 / 1M output tokens<br>
                                    • Very affordable — thousands of messages for a few cents<br>
                                    • API is separate from DeepSeek chat.deepseek.com subscriptions
                                </div>
                            </div>

                            <div style="display:flex;gap:10px">
                                <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#667eea,#764ba2);border:none;padding:10px 28px;border-radius:8px;font-weight:600">
                                    <i class="fa fa-save"></i> Save Configuration
                                </button>
                                <a href="{$_url}plugin/deepseek_ai" class="btn btn-default" style="padding:10px 20px;border-radius:8px">
                                    <i class="fa fa-arrow-left"></i> Back to Chat
                                </a>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{literal}
<script>
function toggleKey() {
    var input = document.getElementById('apiKeyInput');
    var icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fa fa-eye';
    }
}
</script>
{/literal}

{include file="sections/footer.tpl"}
