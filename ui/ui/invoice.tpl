{include file="sections/header.tpl"}

{if !$in}

    <div class="sr-inv">
        <div class="sr-alert">
            <h4>Invoice not found</h4>
            <p>The invoice data could not be loaded. Please check that the invoice ID is correct.</p>
            <a href="{$_url}plan/list" class="sr-btn sr-btn--primary">Back to Plan List</a>
        </div>
    </div>

{else}

<style>
{literal}
/* =====================================================================
   SpeedRadius invoice — screen + print.

   Conventions:
   - Screen sizes are px. Millimetres appear only inside @media print.
   - Every rule is scoped under .sr-inv so nothing leaks into the theme.
   - Every text element sets its own colour, so the dark-mode skin cannot
     bleed into the card.
   - The card stays light on purpose: it mirrors the printed invoice.
   ===================================================================== */

.sr-inv {
    --accent: #4f46e5;
    --accent-dark: #4338ca;
    --accent-soft: #eef2ff;
    --accent-line: #dfe3fb;
    --ink: #0f172a;
    --ink-2: #475569;
    --ink-3: #94a3b8;
    --line: #e2e8f0;
    --line-2: #f1f5f9;
    --surface: #f8fafc;
    --ok: #059669;
    --ok-soft: #ecfdf5;
    --r-lg: 14px;
    --r-md: 10px;
    --r-sm: 8px;

    padding: 0 0 32px;
    color: var(--ink);
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
        "Helvetica Neue", Arial, "Noto Sans", sans-serif;
    font-size: 14px;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
}

.sr-inv *,
.sr-inv *::before,
.sr-inv *::after {
    box-sizing: border-box;
}

/* The plain-text invoice is posted to plan/print; keep it out of the way. */
.sr-inv textarea[hidden] {
    display: none;
}

/* ---------- Card ---------- */

.sr-inv-card {
    position: relative;
    overflow: hidden;
    max-width: 840px;
    margin: 0 auto;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--r-lg);
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04),
        0 14px 34px -14px rgba(15, 23, 42, 0.14);
}

/* ---------- Header ---------- */

.sr-inv-head {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 18px;
    background: var(--accent);
    color: #fff;
}

.sr-inv-brand {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.sr-inv-logo {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.16);
    color: #fff;
    font-size: 16px;
    font-weight: 700;
    text-transform: uppercase;
}

.sr-inv-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.sr-inv-co-name {
    margin: 0;
    color: #fff;
    font-size: 17px;
    font-weight: 600;
    letter-spacing: -0.01em;
    line-height: 1.3;
}

.sr-inv-co-meta {
    margin-top: 3px;
    color: rgba(255, 255, 255, 0.82);
    font-size: 12.5px;
    line-height: 1.45;
    overflow-wrap: anywhere;
}

.sr-inv-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding-top: 14px;
    border-top: 1px solid rgba(255, 255, 255, 0.22);
}

.sr-inv-meta-k {
    color: rgba(255, 255, 255, 0.75);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.sr-inv-meta-id {
    margin-top: 2px;
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    letter-spacing: -0.01em;
    overflow-wrap: anywhere;
}

.sr-inv-badge {
    display: inline-block;
    flex: 0 0 auto;
    padding: 5px 12px;
    border-radius: 999px;
    background: #fff;
    color: var(--ok);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    white-space: nowrap;
}

/* ---------- Body ---------- */

.sr-inv-body {
    position: relative;
    padding: 18px;
}

.sr-inv-mark {
    position: absolute;
    top: 50%;
    left: 50%;
    z-index: 0;
    transform: translate(-50%, -50%) rotate(-32deg);
    color: rgba(15, 23, 42, 0.035);
    font-size: 64px;
    font-weight: 800;
    letter-spacing: 0.04em;
    white-space: nowrap;
    pointer-events: none;
    user-select: none;
}

.sr-inv-stack {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

/* ---------- Sections ---------- */

.sr-inv-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 14px;
}

.sr-inv-sec {
    padding: 16px;
    border: 1px solid var(--line);
    border-radius: var(--r-md);
    background: #fff;
}

.sr-inv-sec-title {
    display: flex;
    align-items: center;
    gap: 7px;
    margin: 0 0 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--line-2);
    color: var(--accent);
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

.sr-inv-sec-title i {
    font-size: 12px;
}

/* ---------- Key / value rows ---------- */

.sr-inv-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 7px 0;
}

.sr-inv-row + .sr-inv-row {
    border-top: 1px solid var(--line-2);
}

.sr-inv-k {
    flex: 0 0 42%;
    color: var(--ink-2);
    font-size: 13px;
    font-weight: 500;
}

.sr-inv-v {
    flex: 1 1 auto;
    min-width: 0;
    color: var(--ink);
    font-size: 13px;
    font-weight: 600;
    text-align: right;
    overflow-wrap: anywhere;
}

/* ---------- Amount ---------- */

.sr-inv-amount {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 18px;
    border: 1px solid var(--accent-line);
    border-radius: var(--r-md);
    background: var(--accent-soft);
}

.sr-inv-amount-k {
    color: var(--accent);
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

.sr-inv-amount-v {
    color: var(--accent);
    font-size: 26px;
    font-weight: 700;
    letter-spacing: -0.02em;
    white-space: nowrap;
    font-variant-numeric: tabular-nums;
}

/* ---------- Buttons ---------- */

.sr-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 44px;
    padding: 10px 18px;
    border: 1px solid transparent;
    border-radius: var(--r-sm);
    font-family: inherit;
    font-size: 13.5px;
    font-weight: 600;
    line-height: 1;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.15s ease, border-color 0.15s ease,
        color 0.15s ease;
}

.sr-btn:hover,
.sr-btn:focus {
    text-decoration: none;
}

.sr-btn--primary {
    background: var(--accent);
    color: #fff;
}

.sr-btn--primary:hover,
.sr-btn--primary:focus {
    background: var(--accent-dark);
    color: #fff;
}

.sr-btn--ok {
    background: var(--ok);
    color: #fff;
}

.sr-btn--ok:hover,
.sr-btn--ok:focus {
    background: #047857;
    color: #fff;
}

.sr-btn--ghost {
    background: #fff;
    border-color: var(--line);
    color: var(--ink-2);
}

.sr-btn--ghost:hover,
.sr-btn--ghost:focus {
    background: var(--line-2);
    border-color: #cbd5e1;
    color: var(--ink);
}

.sr-inv-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* ---------- Download panel ---------- */

.sr-inv-dl {
    padding: 16px;
    border: 1px solid var(--line);
    border-radius: var(--r-md);
    background: var(--surface);
}

.sr-inv-dl-title {
    display: flex;
    align-items: center;
    gap: 7px;
    margin: 0 0 12px;
    color: var(--ink-2);
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
}

.sr-inv-dl-grid {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.sr-inv-dl-grid form {
    display: flex;
    margin: 0;
}

.sr-inv-dl-grid .sr-btn {
    flex: 1 1 auto;
}

/* The nux:// scheme only works inside the Android app, i.e. narrow screens. */
.sr-inv-nux {
    display: none;
}

/* ---------- Footer note ---------- */

.sr-inv-note {
    padding: 14px 18px;
    border-top: 1px solid var(--line);
    background: var(--surface);
    color: var(--ink-2);
    font-size: 12.5px;
    text-align: center;
}

/* ---------- Error state ---------- */

.sr-alert {
    max-width: 560px;
    margin: 24px auto;
    padding: 20px;
    border: 1px solid #fecaca;
    border-radius: var(--r-md);
    background: #fef2f2;
    color: #991b1b;
}

.sr-alert h4 {
    margin: 0 0 8px;
    color: #991b1b;
    font-size: 16px;
    font-weight: 700;
}

.sr-alert p {
    margin: 0 0 16px;
    color: #b91c1c;
}

/* =====================================================================
   Tablet and up
   ===================================================================== */

@media (min-width: 640px) {

    .sr-inv-head {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        padding: 22px 26px;
    }

    .sr-inv-meta {
        flex: 0 0 auto;
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
        padding-top: 0;
        border-top: 0;
    }

    .sr-inv-body {
        padding: 26px;
    }

    .sr-inv-stack {
        gap: 16px;
    }

    .sr-inv-grid {
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .sr-inv-amount {
        padding: 18px 22px;
    }

    .sr-inv-actions {
        flex-direction: row;
        flex-wrap: wrap;
    }

    .sr-btn {
        flex: 0 0 auto;
    }

    .sr-inv-dl-grid {
        flex-direction: row;
        flex-wrap: wrap;
    }

    .sr-inv-dl-grid form {
        flex: 1 1 160px;
    }

    .sr-inv-mark {
        font-size: 96px;
    }
}

/* Matches the original hidden-md hidden-lg behaviour of the NuxPrint link. */
@media (max-width: 991px) {
    .sr-inv-nux {
        display: inline-flex;
    }
}

/* =====================================================================
   Desktop
   ===================================================================== */

@media (min-width: 1024px) {

    .sr-inv-body {
        padding: 30px;
    }

    .sr-inv-mark {
        font-size: 118px;
    }

    .sr-inv-amount-v {
        font-size: 30px;
    }
}

/* =====================================================================
   Small phones
   ===================================================================== */

@media (max-width: 400px) {

    .sr-inv-head,
    .sr-inv-body {
        padding: 16px;
    }

    .sr-inv-row {
        gap: 8px;
    }

    .sr-inv-k {
        flex-basis: 38%;
    }

    .sr-inv-amount-v {
        font-size: 22px;
    }
}

/* =====================================================================
   Print — A4. This is the only place millimetres are used.
   ===================================================================== */

@media print {

    @page {
        size: A4;
        margin: 12mm;
    }

    body {
        background: #fff !important;
    }

    .main-header,
    .main-sidebar,
    .content-header,
    .main-footer,
    .sr-no-print {
        display: none !important;
    }

    .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        min-height: 0 !important;
        background: #fff !important;
    }

    .sr-inv {
        padding: 0;
        font-size: 11.5px;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .sr-inv-card {
        max-width: none;
        margin: 0;
        overflow: visible;
        border: 0;
        border-radius: 0;
        box-shadow: none;
    }

    .sr-inv-head {
        flex-direction: row;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 0 0 14px;
        background: #fff !important;
        color: var(--ink) !important;
        border-bottom: 2px solid var(--accent) !important;
    }

    .sr-inv-logo {
        width: 38px;
        height: 38px;
        background: var(--accent-soft) !important;
        border-color: var(--accent-line) !important;
        color: var(--accent) !important;
    }

    .sr-inv-co-name {
        color: var(--ink) !important;
    }

    .sr-inv-co-meta {
        color: var(--ink-2) !important;
    }

    .sr-inv-meta {
        flex-direction: column;
        align-items: flex-end;
        gap: 5px;
        padding-top: 0;
        border-top: 0;
    }

    .sr-inv-meta-k {
        color: var(--ink-3) !important;
    }

    .sr-inv-meta-id {
        color: var(--ink) !important;
    }

    .sr-inv-badge {
        background: var(--ok-soft) !important;
        color: var(--ok) !important;
        border: 1px solid #a7f3d0;
    }

    .sr-inv-body {
        padding: 16px 0 0;
    }

    .sr-inv-mark {
        display: none !important;
    }

    .sr-inv-stack {
        gap: 12px;
    }

    .sr-inv-grid {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .sr-inv-sec {
        padding: 12px;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .sr-inv-amount {
        padding: 12px 14px;
        background: var(--accent-soft) !important;
        border-color: var(--accent-line) !important;
    }

    .sr-inv-amount-v {
        font-size: 20px;
    }

    .sr-inv-note {
        padding: 10px 12px;
        background: var(--surface) !important;
        border-top: 1px solid var(--line) !important;
    }
}
{/literal}
</style>

<div class="sr-inv">
    <div class="sr-inv-card">

        <div class="sr-inv-head">
            <div class="sr-inv-brand">
                <div class="sr-inv-logo">
                    {if $_c.logo}
                        <img src="{$UPLOAD_PATH}/{$_c.logo}" alt="{$_c.CompanyName|escape:'html'}">
                    {else}
                        {$_c.CompanyName|substr:0:1}
                    {/if}
                </div>
                <div>
                    <h1 class="sr-inv-co-name">{$_c.CompanyName}</h1>
                    <div class="sr-inv-co-meta">
                        {if $_c.address}<div>{$_c.address}</div>{/if}
                        {if $_c.phone}<div>{$_c.phone}</div>{/if}
                    </div>
                </div>
            </div>

            <div class="sr-inv-meta">
                <div>
                    <div class="sr-inv-meta-k">Invoice</div>
                    <div class="sr-inv-meta-id">{$in.invoice}</div>
                </div>
                <div class="sr-inv-badge">Paid</div>
            </div>
        </div>

        <div class="sr-inv-body">
            <div class="sr-inv-mark" aria-hidden="true">PAID</div>

            <div class="sr-inv-stack">

                <div class="sr-inv-grid">
                    <div class="sr-inv-sec">
                        <h2 class="sr-inv-sec-title"><i class="fa fa-calendar"></i> Transaction Details</h2>
                        <div class="sr-inv-row">
                            <span class="sr-inv-k">Date</span>
                            <span class="sr-inv-v">{Lang::dateAndTimeFormat($in.recharged_on, $in.recharged_time)}</span>
                        </div>
                        <div class="sr-inv-row">
                            <span class="sr-inv-k">Sales Person</span>
                            <span class="sr-inv-v">{if $in.admin_id > 0}{$_admin.fullname}{else}System{/if}</span>
                        </div>
                        <div class="sr-inv-row">
                            <span class="sr-inv-k">Payment Method</span>
                            <span class="sr-inv-v">{$in.method}</span>
                        </div>
                    </div>

                    <div class="sr-inv-sec">
                        <h2 class="sr-inv-sec-title"><i class="fa fa-cog"></i> Service Details</h2>
                        <div class="sr-inv-row">
                            <span class="sr-inv-k">Service Type</span>
                            <span class="sr-inv-v">{$in.type}</span>
                        </div>
                        <div class="sr-inv-row">
                            <span class="sr-inv-k">Plan Name</span>
                            <span class="sr-inv-v">{$in.plan_name}</span>
                        </div>
                        {if $in.type != 'Balance'}
                        <div class="sr-inv-row">
                            <span class="sr-inv-k">Expires On</span>
                            <span class="sr-inv-v">{Lang::dateAndTimeFormat($in.expiration, $in.time)}</span>
                        </div>
                        {/if}
                    </div>
                </div>

                <div class="sr-inv-sec">
                    <h2 class="sr-inv-sec-title"><i class="fa fa-user"></i> Customer Information</h2>
                    <div class="sr-inv-grid">
                        <div>
                            <div class="sr-inv-row">
                                <span class="sr-inv-k">Full Name</span>
                                <span class="sr-inv-v">{if $customer}{$customer.fullname}{else}N/A{/if}</span>
                            </div>
                            <div class="sr-inv-row">
                                <span class="sr-inv-k">Username</span>
                                <span class="sr-inv-v">{$in.username}</span>
                            </div>
                        </div>
                        <div>
                            <div class="sr-inv-row">
                                <span class="sr-inv-k">Password</span>
                                <span class="sr-inv-v">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</span>
                            </div>
                            {if $customer && $customer.phonenumber}
                            <div class="sr-inv-row">
                                <span class="sr-inv-k">Phone</span>
                                <span class="sr-inv-v">{$customer.phonenumber}</span>
                            </div>
                            {/if}
                        </div>
                    </div>
                </div>

                <div class="sr-inv-amount">
                    <span class="sr-inv-amount-k">Total Amount</span>
                    <span class="sr-inv-amount-v">{Lang::moneyFormat($in.price)}</span>
                </div>

                <div class="sr-inv-actions sr-no-print">
                    <a href="{$_url}plan/list" class="sr-btn sr-btn--ghost">
                        <i class="fa fa-arrow-left"></i> {Lang::T('Finish')}
                    </a>
                    <a href="https://api.whatsapp.com/send/?text={$whatsapp}" target="_blank" rel="noopener" class="sr-btn sr-btn--ok">
                        <i class="fa fa-whatsapp"></i> WhatsApp
                    </a>
                    <a href="{$_url}plan/view/{$in.id}/send" class="sr-btn sr-btn--primary">
                        <i class="fa fa-paper-plane"></i> {Lang::T("Resend")}
                    </a>
                </div>

                <div class="sr-inv-dl sr-no-print">
                    <h2 class="sr-inv-dl-title"><i class="fa fa-download"></i> Download Invoice</h2>
                    <div class="sr-inv-dl-grid">
                        <a href="{$_url}plan/print/{$in.id}" target="_blank" rel="noopener" class="sr-btn sr-btn--primary">
                            <i class="fa fa-file-pdf-o"></i> PDF
                        </a>
                        <form method="post" action="{$_url}plan/print" target="_blank">
                            <textarea name="content" hidden>{$invoice}</textarea>
                            <input type="hidden" name="id" value="{$in.id}">
                            <input type="hidden" name="nux" value="text">
                            <button type="submit" class="sr-btn sr-btn--ghost">
                                <i class="fa fa-file-text-o"></i> Text
                            </button>
                        </form>
                        <a href="{$_url}plan/print/{$in.id}?format=html" target="_blank" rel="noopener" class="sr-btn sr-btn--ghost">
                            <i class="fa fa-file-code-o"></i> HTML
                        </a>
                        <a href="nux://print?text={$invoice|escape:'url'}" class="sr-btn sr-btn--ghost sr-inv-nux">
                            <i class="fa fa-mobile"></i> NuxPrint
                        </a>
                    </div>
                </div>

            </div>
        </div>

        {if $_c.note}
        <div class="sr-inv-note">
            <i class="fa fa-info-circle"></i> {$_c.note}
        </div>
        {/if}

    </div>
</div>

{/if}

{include file="sections/footer.tpl"}
