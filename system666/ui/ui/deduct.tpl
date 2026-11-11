{include file="sections/header.tpl"}

<style>
#deduct-wrap input:focus, #deduct-wrap textarea:focus {
    box-shadow: 0 0 0 3px rgba(239,68,68,0.15);
    border-color: #f87171 !important;
}
</style>

<div id="deduct-wrap" style="padding:16px 12px">
    <div style="max-width:480px;margin:0 auto">

        <!-- breadcrumb -->
        <a href="{$_url}customers/view/{$c['id']}"
           style="display:inline-flex;align-items:center;gap:4px;font-size:12px;color:#9ca3af;text-decoration:none;margin-bottom:10px">
            &#8592; Back to Customer
        </a>

        <!-- title -->
        <div style="margin-bottom:12px">
            <h1 style="font-size:20px;font-weight:700;color:#1f2937;margin:0 0 2px 0">&#x1F4B8; Subtract Balance</h1>
            <p style="font-size:12px;color:#9ca3af;margin:0">
                Manually deduct from customer wallet &mdash;
                <span style="color:#f59e0b;font-weight:500">won&rsquo;t affect income reports</span>
            </p>
        </div>

        {if $_c['enable_balance'] neq 'yes'}
        <!-- balance disabled -->
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:14px;padding:14px 16px;display:flex;align-items:flex-start;gap:10px">
            <span style="font-size:18px;line-height:1;margin-top:2px">&#x1F6AB;</span>
            <div>
                <p style="font-size:13px;font-weight:600;color:#b91c1c;margin:0 0 2px 0">Balance Feature Disabled</p>
                <p style="font-size:12px;color:#ef4444;margin:0">Enable it in System Settings first.</p>
            </div>
        </div>
        {else}

        <!-- customer info card -->
        <div style="background:#fff;border-radius:16px;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(0,0,0,0.06);padding:12px 14px;margin-bottom:10px;display:flex;align-items:center;gap:12px">
            <div style="width:40px;height:40px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;font-weight:700;background:linear-gradient(135deg,#6366f1,#8b5cf6)">
                {if $c['fullname']}{$c['fullname'][0]|upper}{else}{$c['username'][0]|upper}{/if}
            </div>
            <div style="flex:1;min-width:0;overflow:hidden">
                <p style="font-size:13px;font-weight:600;color:#1f2937;margin:0 0 1px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    {if $c['fullname']}{$c['fullname']}{else}{$c['username']}{/if}
                </p>
                <p style="font-size:11px;color:#9ca3af;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    @{$c['username']}{if $c['phonenumber']} &middot; {$c['phonenumber']}{/if}
                </p>
            </div>
            <div style="text-align:right;flex-shrink:0">
                <p style="font-size:10px;color:#9ca3af;font-weight:600;letter-spacing:.05em;text-transform:uppercase;margin:0 0 2px 0">Wallet</p>
                <p style="font-size:15px;font-weight:700;margin:0;color:{if $c['balance'] > 0}#059669{else}#9ca3af{/if}">
                    {Lang::moneyFormat($c['balance'])}
                </p>
            </div>
        </div>

        <!-- form card -->
        <div style="background:#fff;border-radius:16px;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(0,0,0,0.06);overflow:hidden">
            <div style="height:3px;background:linear-gradient(90deg,#f43f5e,#ec4899,#a855f7)"></div>
            <div style="padding:16px 14px">
                <form method="post" action="{$_url}plan/deduct-post" id="deductForm">
                    <input type="hidden" name="id_customer" value="{$c['id']}">

                    <!-- amount -->
                    <div style="margin-bottom:12px">
                        <label style="display:block;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">
                            &#x1F4B0; Amount to Subtract
                        </label>
                        <div style="position:relative">
                            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:12px;font-weight:600;color:#9ca3af;pointer-events:none">Ksh</span>
                            <input type="number" name="amount" id="amountInput"
                                min="1" step="any" required placeholder="0"
                                oninput="updatePreview(this.value)"
                                style="width:100%;box-sizing:border-box;padding:10px 12px 10px 44px;border:1px solid #e5e7eb;border-radius:12px;font-size:14px;font-weight:600;color:#111827;background:#f9fafb;outline:none;transition:border-color .15s">
                        </div>
                        <div id="balancePreview" style="display:none;margin-top:6px">
                            <div style="display:flex;align-items:center;justify-content:space-between;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:6px 10px">
                                <span style="font-size:11px;color:#9ca3af">Balance after:</span>
                                <span id="previewAmount" style="font-size:12px;font-weight:700"></span>
                            </div>
                        </div>
                        <p style="font-size:11px;color:#9ca3af;margin:5px 0 0 0">
                            &#x26A0;&#xFE0F; Not recorded as income &mdash; deducted silently.
                        </p>
                    </div>

                    <!-- note -->
                    <div style="margin-bottom:14px">
                        <label style="display:block;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">
                            &#x1F4DD; Reason <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#9ca3af">(optional)</span>
                        </label>
                        <textarea name="note" rows="2"
                            placeholder="e.g. Overcharge correction, manual adjustment..."
                            style="width:100%;box-sizing:border-box;padding:10px 12px;border:1px solid #e5e7eb;border-radius:12px;font-size:13px;color:#1f2937;background:#f9fafb;outline:none;resize:none;transition:border-color .15s"></textarea>
                    </div>

                    <!-- buttons -->
                    <div style="display:flex;align-items:center;gap:8px">
                        <button type="submit"
                            style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:10px 16px;color:#fff;font-size:14px;font-weight:600;border:none;border-radius:12px;cursor:pointer;background:linear-gradient(135deg,#f43f5e,#e11d48);box-shadow:0 2px 8px rgba(244,63,94,0.35);transition:opacity .15s">
                            &#x2796; Subtract Balance
                        </button>
                        <a href="{$_url}customers/view/{$c['id']}"
                           style="padding:10px 16px;background:#f3f4f6;color:#6b7280;font-size:14px;font-weight:600;border-radius:12px;text-decoration:none;white-space:nowrap;transition:background .15s">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
        {/if}

    </div>
</div>

<script>
var currentBalance = parseFloat('{$c['balance']}') || 0;
{literal}
function updatePreview(val) {
    var amount = parseFloat(val);
    var preview = document.getElementById('balancePreview');
    var previewAmt = document.getElementById('previewAmount');
    if (!isNaN(amount) && amount > 0) {
        var after = currentBalance - amount;
        previewAmt.textContent = 'Ksh ' + after.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2});
        previewAmt.style.color = after < 0 ? '#f43f5e' : '#059669';
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}
document.getElementById('deductForm').addEventListener('submit', function(e) {
    var amount = parseFloat(document.getElementById('amountInput').value);
    if (isNaN(amount) || amount <= 0) {
        e.preventDefault();
        alert('Please enter a valid amount greater than 0.');
        return;
    }
    if (!confirm('Subtract Ksh ' + amount.toLocaleString() + ' from this customer\'s balance?\n\nThis cannot be undone.')) {
        e.preventDefault();
    }
});
{/literal}
</script>

{include file="sections/footer.tpl"}
