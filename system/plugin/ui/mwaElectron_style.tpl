{* Shared chrome for the M-WA Electron pages.
   Included by mwaElectron.tpl / _config.tpl / _logs.tpl so the theme hardening
   below exists in exactly one place. *}
<style>
{literal}
    .mw-wrap { max-width:1040px; margin:0 auto; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif; }

    /* ---- Theme hardening ------------------------------------------------
       modern-AdminLTE.min.css ships a blanket `min-height: 100%` on html,
       h1-h6, section, article, aside, button, body, input, textarea and
       select. Inside a stretched grid item that inflates headings until they
       fill the panel and neighbouring blocks collide. Class selectors
       (0,1,0) out-rank the theme's bare element selectors (0,0,1). */
    .mw-wrap h1, .mw-wrap h2, .mw-wrap h3,
    .mw-wrap h4, .mw-wrap h5, .mw-wrap h6 { min-height:0; }
    .mw-btn { min-height:44px; }

    /* Long URLs / session ids must never force a sideways scrollbar. */
    .mw-panel, .mw-field, .mw-stat, .mw-note { min-width:0; overflow-wrap:break-word; }

    .mw-hero { position:relative; overflow:hidden; border-radius:18px; padding:24px 26px; margin-bottom:18px; color:#fff;
        background:linear-gradient(120deg,#0b1f33 0%,#123c52 55%,#0e7a5f 130%); box-shadow:0 10px 30px rgba(10,30,50,.25); }
    .mw-hero:after { content:""; position:absolute; right:-60px; top:-60px; width:220px; height:220px; border-radius:50%;
        background:radial-gradient(circle,rgba(37,211,102,.35),transparent 70%); }
    .mw-hero h2 { margin:0 0 4px; font-size:22px; font-weight:800; letter-spacing:.3px; }
    .mw-hero .sub { font-size:12.5px; color:#cfe6dd; margin:0; }
    .mw-chip { display:inline-flex; align-items:center; gap:7px; padding:5px 12px; border-radius:999px; font-size:12px; font-weight:700;
        background:rgba(255,255,255,.12); backdrop-filter:blur(3px); }
    .mw-dot { width:9px; height:9px; border-radius:50%; background:#f87171; }
    .mw-dot.ok { background:#34d399; box-shadow:0 0 8px rgba(52,211,153,.9); animation:mwPulse 1.8s infinite; }
    @keyframes mwPulse { 0%{box-shadow:0 0 0 0 rgba(52,211,153,.7)} 70%{box-shadow:0 0 0 9px rgba(52,211,153,0)} 100%{box-shadow:0 0 0 0 rgba(52,211,153,0)} }
    .mw-chip-row { display:flex; flex-wrap:wrap; gap:8px; margin-top:14px; }

    .mw-grid { display:grid; grid-template-columns:1.5fr 1fr; gap:18px; }
    .mw-grid > * { min-width:0; }
    @media (max-width:860px){ .mw-grid { grid-template-columns:1fr; gap:14px; } }

    .mw-panel { background:#fff; border-radius:16px; border:1px solid #e8eef4; box-shadow:0 4px 18px rgba(20,40,60,.06); padding:20px; margin-bottom:18px; }
    .mw-panel h3 { margin:0 0 14px; font-size:15px; font-weight:800; color:#0f2b3d; }
    .mw-panel h3 i { color:#10b981; margin-right:6px; }

    .mw-stat { display:flex; align-items:center; gap:13px; padding:13px 15px; border-radius:13px; background:#f6fafb; margin-bottom:10px; }
    .mw-stat .ic { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0; }
    .mw-stat .num { font-size:21px; font-weight:800; color:#0f2b3d; line-height:1; margin-left:auto; }
    .mw-stat .t { font-size:12.5px; color:#6b7f8c; font-weight:600; }
    .ic-g { background:#e7f9ef; color:#059669; } .ic-b { background:#e7f0fb; color:#2563eb; }
    .ic-r { background:#fdeaea; color:#dc2626; } .ic-o { background:#fef3e2; color:#d97706; }

    .mw-btn { display:flex; align-items:center; justify-content:center; gap:8px; width:100%; padding:11px 14px; border-radius:12px;
        font-size:13px; font-weight:700; border:none; cursor:pointer; text-decoration:none; margin-bottom:9px; transition:transform .12s,box-shadow .12s; }
    .mw-btn:hover { transform:translateY(-1px); color:#fff; text-decoration:none; }
    .mw-btn:last-child { margin-bottom:0; }
    .b-g { background:#25d366; color:#fff; box-shadow:0 6px 16px rgba(37,211,102,.3); } .b-g:hover{ background:#1fb657; }
    .b-dk { background:#0f2b3d; color:#fff; } .b-dk:hover{ background:#14384f; }
    .b-r { background:#ef4444; color:#fff; } .b-r:hover{ background:#dc2626; }
    .b-o { background:#fff; color:#334e63; border:1.5px solid #dce6ec; } .b-o:hover{ background:#f4f8fa; color:#334e63; }

    .mw-muted { color:#8ba0ae; font-size:12px; }
    .mw-note { font-size:12.5px; background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; padding:11px 14px; border-radius:12px; margin-bottom:16px; }
    .mw-note.err { background:#fef2f2; border-color:#fecaca; color:#991b1b; }
    .mw-note.ok { background:#e7f9ef; border-color:#a7f3d0; color:#065f46; }

    .mw-field { margin-bottom:13px; }
    .mw-field label { display:block; font-size:12px; font-weight:700; color:#334e63; margin-bottom:5px; }
    .mw-field .hint { font-size:11px; color:#8ba0ae; margin-top:5px; overflow-wrap:break-word; }
    .mw-field input[type=text], .mw-field input[type=password], .mw-field input[type=number], .mw-field select {
        width:100%; padding:9px 11px; border:1px solid #dce6ec; border-radius:10px; font-size:13px; background:#fff; color:#0f2b3d; }
    .mw-row { display:flex; flex-wrap:wrap; gap:8px; }
    .mw-row > * { flex:1 1 200px; min-width:0; }

    .mw-table { width:100%; border-collapse:collapse; font-size:12.5px; }
    .mw-table th { text-align:left; padding:9px 10px; background:#f6fafb; color:#334e63; font-weight:700; border-bottom:1px solid #e8eef4; white-space:nowrap; }
    .mw-table td { padding:9px 10px; border-bottom:1px solid #f1f5f8; color:#41556b; vertical-align:top; overflow-wrap:break-word; }
    .mw-table tr:last-child td { border-bottom:none; }
    .mw-badge { display:inline-block; padding:3px 9px; border-radius:999px; font-size:11px; font-weight:700; white-space:nowrap; }
    .bg-ok { background:#e7f9ef; color:#059669; } .bg-bad { background:#fdeaea; color:#dc2626; }
    .bg-warn { background:#fef3e2; color:#d97706; } .bg-neutral { background:#eef3f6; color:#5b7285; }
    .mw-scroll { overflow-x:auto; }

    @media (max-width:600px){
        .mw-hero { padding:18px; border-radius:14px; }
        .mw-hero h2 { font-size:19px; }
        .mw-panel { padding:16px; border-radius:14px; }
        .mw-table th, .mw-table td { padding:8px 7px; }
    }
{/literal}
</style>

<div class="mw-wrap">
