{include file="sections/header.tpl"}

<div class="p-4 md:p-6" style="font-family:'Inter','Segoe UI',Arial,sans-serif;">

    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">💬 ApiWap WhatsApp Gateway</h1>
            <p class="text-slate-400 text-sm mt-0.5">Cloud-hosted WhatsApp API — no Docker, no self-hosting</p>
        </div>
        <div class="flex gap-2">
            {if $cfg.enabled == 'yes'}
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">🟢 Connected</span>
            {else}
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">🔴 Disabled</span>
            {/if}
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT: Settings + Test -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Settings Card -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">⚙️ Configuration</h3>
                <form method="post" action="{$_url}plugin/apiwap_whatsapp/save">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-1">Enable Gateway</label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="enabled" value="yes" {if $cfg.enabled == 'yes'}checked{/if} class="w-5 h-5 rounded border-slate-300 text-green-600">
                                <span class="text-sm text-slate-600">Use ApiWap for WhatsApp notifications</span>
                            </label>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-1">API Key</label>
                            <input type="password" name="api_key" value="{$cfg.api_key|escape}" placeholder="Paste your ApiWap API key..." class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-green-400">
                            <p class="text-xs text-slate-400 mt-1">Get your key at <a href="https://account.apiwap.com" target="_blank" class="text-green-600">account.apiwap.com</a></p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-1">Sender Name (optional)</label>
                            <input type="text" name="sender_name" value="{$cfg.sender_name|escape}" placeholder="e.g., Speedcom WiFi" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-green-400">
                        </div>
                        <button type="submit" class="px-6 py-2.5 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700 transition-colors">
                            💾 Save Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Test Card -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-4">🧪 Test Message</h3>
                <form method="post" action="{$_url}plugin/apiwap_whatsapp/test">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-1">Phone Number</label>
                            <input type="text" name="test_phone" placeholder="+254712345678" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-green-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-400 mb-1">Message</label>
                            <input type="text" name="test_message" value="Hello from SpeedRadius! 🚀" class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-green-400">
                        </div>
                    </div>
                    <button type="submit" class="mt-4 px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 transition-colors">
                        📤 Send Test
                    </button>
                </form>
            </div>
        </div>

        <!-- RIGHT: Info + Quick Setup -->
        <div class="space-y-6">
            
            <!-- Quick Setup -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-3">🚀 Quick Setup</h3>
                <ol class="text-sm text-slate-600 space-y-2 pl-4">
                    <li>Go to <a href="https://account.apiwap.com/register" target="_blank" class="text-green-600 font-semibold">account.apiwap.com</a> and create an account</li>
                    <li>Click <strong>"Add New Instance"</strong></li>
                    <li>Scan the QR code with your WhatsApp</li>
                    <li>Copy your <strong>API Key</strong> from the console</li>
                    <li>Paste it in the field on the left</li>
                    <li>Enable the gateway and Save</li>
                </ol>
            </div>

            <!-- API Info -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-3">📋 API Info</h3>
                <table class="text-sm w-full">
                    <tr class="border-b border-slate-100"><td class="py-2 text-slate-400">Provider</td><td class="py-2 text-slate-700 font-medium">KreativeLabsKE 🇰🇪</td></tr>
                    <tr class="border-b border-slate-100"><td class="py-2 text-slate-400">Base URL</td><td class="py-2 text-slate-700 font-mono text-xs">api.apiwap.com/api/v1</td></tr>
                    <tr class="border-b border-slate-100"><td class="py-2 text-slate-400">Auth</td><td class="py-2 text-slate-700">Bearer Token</td></tr>
                    <tr class="border-b border-slate-100"><td class="py-2 text-slate-400">Docs</td><td class="py-2"><a href="https://docs.apiwap.com" target="_blank" class="text-green-600">docs.apiwap.com</a></td></tr>
                </table>
            </div>

            <!-- Recent Logs -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-3">🕐 Recent Messages</h3>
                {if $logs|@count > 0}
                    <div class="space-y-2">
                    {foreach $logs as $log}
                        <div class="border border-slate-100 rounded-lg p-3 text-xs">
                            <div class="flex justify-between">
                                <span class="font-semibold text-slate-700">{$log->phone}</span>
                                {if $log->status == 'sent'}
                                    <span class="text-green-600 font-bold">✓ Sent</span>
                                {else}
                                    <span class="text-red-500 font-bold">✗ Failed</span>
                                {/if}
                            </div>
                            <div class="text-slate-400 mt-1 truncate">{$log->message|truncate:80}</div>
                        </div>
                    {/foreach}
                    </div>
                {else}
                    <p class="text-sm text-slate-400 text-center py-4">No messages sent yet</p>
                {/if}
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
