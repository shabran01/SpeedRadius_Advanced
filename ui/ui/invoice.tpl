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
   - Mobile first: the base rules are the phone layout, media queries
     only add width for larger screens.
   - Screen sizes are px. Millimetres appear only inside @media print.
   - Every rule is scoped under .sr-inv so nothing leaks into the theme.
   - Every text element sets its own colour, so the dark-mode skin cannot
     bleed into the card. The card stays light on purpose: it mirrors the
     printed invoice.
   - Rows stack label-over-value on phones so long values can never be
     squeezed into a two-line wrap mid-token.
   ===================================================================== */

.sr-inv {
    --accent: #4f46e5;
    --accent-2: #7c3aed;
    --accent-dark: #3730a3;
    --accent-soft: #eef2ff;
    --accent-soft-2: #f5f3ff;
    --accent-line: #dcdffb;
    --cyan: #0891b2;
    --cyan-soft: #ecfeff;
    --ink: #0b1220;
    --ink-2: #475569;
    --ink-3: #8b97a8;
    --line: #e5e9f0;
    --line-2: #f1f5f9;
    --surface: #f8fafc;
    --ok: #059669;
    --ok-soft: #ecfdf5;
    --r-lg: 16px;
    --r-md: 12px;
    --r-sm: 9px;
    --gap: 16px;

    padding: 0 0 28px;
    color: var(--ink);
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
        "Helvetica Neue", Arial, "Noto Sans", sans-serif;
    font-size: 14px;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
    -webkit-text-size-adjust: 100%;
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

/* Min-width guards: stop long values from pushing siblings out of the box. */
.sr-inv-brand > div:last-child,
.sr-inv-meta > div {
    min-width: 0;
}

/* ---------- Card ---------- */

.sr-inv-card {
    position: relative;
    overflow: hidden;
    max-width: 820px;
    margin: 0 auto;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--r-lg);
    box-shadow: 0 1px 2px rgba(11, 18, 32, 0.04),
        0 18px 40px -20px rgba(11, 18, 32, 0.22);
}

/* ---------- Header ---------- */

.sr-inv-head {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 20px;
    background-color: var(--accent);
    background-image:
        radial-gradient(130% 110% at 100% 0%, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0) 58%),
        linear-gradient(135deg, #4f46e5 0%, #6d28d9 58%, #7c3aed 100%);
    color: #fff;
}

.sr-inv-brand {
    display: flex;
    align-items: center;
    gap: 13px;
    min-width: 0;
}

.sr-inv-logo {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 46px;
    height: 46px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.34);
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.18);
    color: #fff;
    font-size: 17px;
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
    font-size: 18px;
    font-weight: 700;
    letter-spacing: -0.015em;
    line-height: 1.25;
}

.sr-inv-co-meta {
    margin-top: 4px;
    color: rgba(255, 255, 255, 0.85);
    font-size: 12.5px;
    line-height: 1.5;
    overflow-wrap: break-word;
}

.sr-inv-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding-top: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.24);
}

.sr-inv-meta-k {
    color: rgba(255, 255, 255, 0.78);
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.sr-inv-meta-id {
    margin-top: 3px;
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: -0.01em;
    overflow-wrap: break-word;
}

.sr-inv-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex: 0 0 auto;
    padding: 6px 13px;
    border-radius: 999px;
    background: #fff;
    color: var(--ok);
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    white-space: nowrap;
    box-shadow: 0 3px 10px -2px rgba(5, 150, 105, 0.35);
}

/* ---------- Body ---------- */

.sr-inv-body {
    position: relative;
    padding: 20px;
}

.sr-inv-mark {
    position: absolute;
    top: 50%;
    left: 50%;
    z-index: 0;
    transform: translate(-50%, -50%) rotate(-30deg);
    color: rgba(79, 70, 229, 0.05);
    font-size: 72px;
    font-weight: 800;
    letter-spacing: 0.06em;
    white-space: nowrap;
    pointer-events: none;
    user-select: none;
}

.sr-inv-stack {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    gap: var(--gap);
}

/* ---------- Sections ---------- */

.sr-inv-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--gap);
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
    gap: 10px;
    margin: 0 0 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--line-2);
    color: var(--ink);
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.sr-inv-sec-title i {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 8px;
    background: var(--accent-soft);
    color: var(--accent);
    font-size: 12px;
}

.sr-inv-sec--s .sr-inv-sec-title i {
    background: var(--accent-soft-2);
    color: var(--accent-2);
}

.sr-inv-sec--c .sr-inv-sec-title i {
    background: var(--cyan-soft);
    color: var(--cyan);
}

/* ---------- Key / value rows (phone: stacked) ---------- */

.sr-inv-row {
    padding: 10px 0;
}

.sr-inv-row + .sr-inv-row {
    border-top: 1px solid var(--line-2);
}

.sr-inv-k {
    display: block;
    color: var(--ink-3);
    font-size: 10.5px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.sr-inv-v {
    display: block;
    margin-top: 4px;
    color: var(--ink);
    font-size: 14px;
    font-weight: 600;
    overflow-wrap: break-word;
}

/* ---------- Amount ---------- */

.sr-inv-amount {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 20px;
    border: 1px solid var(--accent-line);
    border-radius: var(--r-md);
    background-image: linear-gradient(135deg, #eef2ff 0%, #f5f3ff 55%, #ecfeff 100%);
}

.sr-inv-amount-k {
    color: var(--accent-dark);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.sr-inv-amount-v {
    color: var(--accent-dark);
    font-size: 26px;
    font-weight: 800;
    letter-spacing: -0.025em;
    white-space: nowrap;
    font-variant-numeric: tabular-nums;
}

/* ---------- Buttons ---------- */

.sr-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 46px;
    padding: 11px 18px;
    border: 1px solid transparent;
    border-radius: var(--r-sm);
    font-family: inherit;
    font-size: 13.5px;
    font-weight: 600;
    line-height: 1;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.16s ease, border-color 0.16s ease,
        color 0.16s ease, box-shadow 0.16s ease;
}

.sr-btn:hover,
.sr-btn:focus {
    text-decoration: none;
}

.sr-btn--primary {
    background-image: linear-gradient(135deg, #4f46e5, #7c3aed);
    color: #fff;
    box-shadow: 0 8px 18px -10px rgba(79, 70, 229, 0.75);
}

.sr-btn--primary:hover,
.sr-btn--primary:focus {
    background-image: linear-gradient(135deg, #4338ca, #6d28d9);
    color: #fff;
}

.sr-btn--ok {
    background-image: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
    box-shadow: 0 8px 18px -10px rgba(5, 150, 105, 0.75);
}

.sr-btn--ok:hover,
.sr-btn--ok:focus {
    background-image: linear-gradient(135deg, #059669, #047857);
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
    gap: 10px;
    margin: 0 0 12px;
    color: var(--ink-2);
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 0.08em;
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
    padding: 15px 20px;
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
   Large phones and up — rows go inline
   ===================================================================== */

@media (min-width: 560px) {

    .sr-inv-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 16px;
        padding: 9px 0;
    }

    .sr-inv-k {
        flex: 0 1 auto;
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: normal;
        text-transform: none;
    }

    .sr-inv-v {
        flex: 0 1 auto;
        min-width: 0;
        margin-top: 0;
        font-size: 13.5px;
        text-align: right;
    }
}

/* =====================================================================
   Tablet and up
   ===================================================================== */

@media (min-width: 768px) {

    .sr-inv-head {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 28px;
        padding: 24px 28px;
    }

    .sr-inv-meta {
        flex: 0 0 auto;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
        padding-top: 0;
        border-top: 0;
    }

    .sr-inv-body {
        padding: 28px;
    }

    .sr-inv-grid {
        grid-template-columns: 1fr 1fr;
    }

    .sr-inv-actions {
        flex-direction: row;
        flex-wrap: wrap;
    }

    .sr-inv-actions .sr-btn {
        flex: 1 1 auto;
    }

    .sr-inv-dl-grid {
        flex-direction: row;
        flex-wrap: wrap;
    }

    .sr-inv-dl-grid form {
        flex: 1 1 150px;
    }

    .sr-inv-mark {
        font-size: 104px;
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
        padding: 32px;
    }

    .sr-inv-mark {
        font-size: 124px;
    }

    .sr-inv-amount-v {
        font-size: 30px;
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
        background-color: #fff !important;
        background-image: none !important;
        color: var(--ink) !important;
        border-bottom: 2px solid var(--accent) !important;
    }

    .sr-inv-logo {
        width: 40px;
        height: 40px;
        background: var(--accent-soft) !important;
        border-color: var(--accent-line) !important;
        color: var(--accent) !important;
    }

    .sr-inv-co-name {
        color: var(--ink) !important;
        font-size: 16px;
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
        box-shadow: none !important;
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

    .sr-inv-sec-title {
        margin-bottom: 10px;
        padding-bottom: 9px;
        font-size: 10.5px;
    }

    .sr-inv-sec-title i {
        width: 20px;
        height: 20px;
        font-size: 10px;
    }

    /* Force inline rows: the print area is under every screen breakpoint. */
    .sr-inv-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 14px;
        padding: 7px 0;
    }

    .sr-inv-k {
        flex: 0 1 auto;
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: normal;
        text-transform: none;
    }

    .sr-inv-v {
        flex: 0 1 auto;
        min-width: 0;
        margin-top: 0;
        font-size: 10.5px;
        text-align: right;
    }

    .sr-inv-amount {
        padding: 12px 14px;
        background-image: none !important;
        background-color: var(--accent-soft) !important;
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
                <div class="sr-inv-badge"><i class="fa fa-check-circle"></i> Paid</div>
            </div>
        </div>

        <div class="sr-inv-body">
            <div class="sr-inv-mark" aria-hidden="true">PAID</div>

            <div class="sr-inv-stack">

                <div class="sr-inv-grid">
                    <div class="sr-inv-sec sr-inv-sec--t">
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

                    <div class="sr-inv-sec sr-inv-sec--s">
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

                <div class="sr-inv-sec sr-inv-sec--c">
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
