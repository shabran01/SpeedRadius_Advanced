{include file="sections/header.tpl"}

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">

                <div class="panel panel-default" style="border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.10)">
                    <div class="panel-heading" style="background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#2d1b69 100%);padding:20px 24px;border:none">
                        <div style="display:flex;align-items:center;gap:14px">
                            <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#a78bfa);display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff">
                                <i class="ion ion-chatbubbles"></i>
                            </div>
                            <div>
                                <div style="color:#fff;font-weight:700;font-size:17px">Mistral AI — Configuration</div>
                                <div style="color:#a090c0;font-size:12px">Configure your Mistral AI API connection</div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-body" style="padding:28px">

                        <form method="POST" action="{$_url}plugin/mistral_ai/config">
                            <input type="hidden" name="token" value="{$csrf_token}">

                            <!-- API Key -->
                            <div class="form-group">
                                <label style="font-weight:600;color:#333">
                                    <i class="fa fa-key" style="color:#7c3aed"></i>
                                    Mistral AI API Key <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" id="apiKeyInput" name="mistral_api_key"
                                           class="form-control"
                                           placeholder="{if !empty($config.mistral_api_key)}••••••••••••••••••••• (saved){else}Enter your Mistral AI API key...{/if}"
                                           autocomplete="new-password">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default" onclick="toggleKey()" title="Show/hide key">
                                            <i class="fa fa-eye" id="eyeIcon"></i>
                                        </button>
                                    </span>
                                </div>
                                <p class="help-block" style="margin-top:6px">
                                    Get your API key from
                                    <a href="https://console.mistral.ai/api-keys/" target="_blank" rel="noopener noreferrer">
                                        console.mistral.ai <i class="fa fa-external-link"></i>
                                    </a>.
                                    Leave blank to keep the current key.
                                </p>
                            </div>

                            <!-- Model -->
                            <div class="form-group">
                                <label style="font-weight:600;color:#333">
                                    <i class="fa fa-microchip" style="color:#7c3aed"></i>
                                    Model
                                </label>
                                <select name="mistral_model" class="form-control">
                                    <optgroup label="🏆 Flagship Models">
                                        <option value="mistral-large-latest" {if empty($config.mistral_model) || $config.mistral_model == 'mistral-large-latest'}selected{/if}>
                                            mistral-large-latest (Recommended — most capable)
                                        </option>
                                        <option value="mistral-medium" {if $config.mistral_model == 'mistral-medium'}selected{/if}>
                                            mistral-medium (Balanced performance)
                                        </option>
                                    </optgroup>
                                    <optgroup label="⚡ Fast & Efficient">
                                        <option value="mistral-small-latest" {if $config.mistral_model == 'mistral-small-latest'}selected{/if}>
                                            mistral-small-latest (Fast, cost-effective)
                                        </option>
                                        <option value="open-mistral-nemo" {if $config.mistral_model == 'open-mistral-nemo'}selected{/if}>
                                            open-mistral-nemo (Open-source, efficient)
                                        </option>
                                    </optgroup>
                                    <optgroup label="📐 Small & Lightweight">
                                        <option value="ministral-8b-latest" {if $config.mistral_model == 'ministral-8b-latest'}selected{/if}>
                                            ministral-8b-latest (Lightweight 8B)
                                        </option>
                                        <option value="ministral-3b-latest" {if $config.mistral_model == 'ministral-3b-latest'}selected{/if}>
                                            ministral-3b-latest (Ultra-light 3B)
                                        </option>
                                    </optgroup>
                                    <optgroup label="💻 Code Specialist">
                                        <option value="codestral-latest" {if $config.mistral_model == 'codestral-latest'}selected{/if}>
                                            codestral-latest (Code generation expert)
                                        </option>
                                    </optgroup>
                                </select>
                                <p class="help-block"><code>mistral-large-latest</code> is best for support and general questions. Use <code>mistral-small-latest</code> for lower cost.</p>
                            </div>

                            <!-- System Prompt -->
                            <div class="form-group">
                                <label style="font-weight:600;color:#333">
                                    <i class="fa fa-comment" style="color:#7c3aed"></i>
                                    System Prompt (Optional)
                                </label>
                                <textarea name="mistral_system_prompt" class="form-control" rows="5"
                                          placeholder="You are a helpful ISP billing assistant for SpeedRadius...">{if !empty($config.mistral_system_prompt)}{$config.mistral_system_prompt}{/if}</textarea>
                                <p class="help-block">
                                    Defines the AI's personality and context. Leave empty to use the default ISP-focused prompt.
                                </p>
                            </div>

                            <!-- Divider -->
                            <hr style="margin:20px 0">

                            <!-- Info box -->
                            <div style="background:#f5f0ff;border:1px solid #d4c5ff;border-radius:8px;padding:14px 16px;margin-bottom:20px;font-size:13px">
                                <div style="font-weight:600;color:#6d28d9;margin-bottom:6px">
                                    <i class="fa fa-info-circle"></i> Mistral AI API Pricing
                                </div>
                                <div style="color:#555;line-height:1.7">
                                    • <strong>mistral-large-latest</strong>: $2.00 / 1M input tokens, $6.00 / 1M output tokens<br>
                                    • <strong>mistral-small-latest</strong>: $0.20 / 1M input tokens, $0.60 / 1M output tokens<br>
                                    • <strong>codestral-latest</strong>: $1.00 / 1M input tokens, $3.00 / 1M output tokens<br>
                                    • <strong>ministral-8b-latest</strong>: $0.10 / 1M input tokens, $0.10 / 1M output tokens<br>
                                    • <strong>Free tier</strong>: 1 request per second, 1B tokens/month on select models<br>
                                    • Visit <a href="https://mistral.ai/pricing" target="_blank" rel="noopener noreferrer">mistral.ai/pricing</a> for latest pricing
                                </div>
                            </div>

                            <div style="display:flex;gap:10px">
                                <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#7c3aed,#a78bfa);border:none;padding:10px 28px;border-radius:8px;font-weight:600">
                                    <i class="fa fa-save"></i> Save Configuration
                                </button>
                                <a href="{$_url}plugin/mistral_ai" class="btn btn-default" style="padding:10px 20px;border-radius:8px">
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
