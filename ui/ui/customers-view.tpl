{include file="sections/header.tpl"}

<style>
.connected-devices {
    margin-top: 8px;
}
.btn-3d {
    position: relative;
    border: none;
    outline: none;
    cursor: pointer;
    border-radius: 5px;
    box-shadow: 0 3px 0 #0e76a8;
    transition: all 0.1s ease;
}

.btn-3d:active {
    transform: translateY(2px);
    box-shadow: 0 1px 0 #0e76a8;
}

.btn-3d-primary {
    background: linear-gradient(to bottom, #3498db, #2980b9);
    color: white;
}

.btn-3d-success {
    background: linear-gradient(to bottom, #2ecc71, #27ae60);
    color: white;
    box-shadow: 0 3px 0 #27ae60;
}

.btn-3d-success:active {
    box-shadow: 0 1px 0 #27ae60;
}

.btn-3d-warning {
    background: linear-gradient(to bottom, #e67e22, #d35400);
    color: white;
    box-shadow: 0 3px 0 #d35400;
}

.btn-3d-warning:active {
    box-shadow: 0 1px 0 #d35400;
}

.btn-3d-danger {
    background: linear-gradient(to bottom, #e74c3c, #c0392b);
    color: white;
    box-shadow: 0 3px 0 #c0392b;
}

.btn-3d-danger:active {
    box-shadow: 0 1px 0 #c0392b;
}

.btn-3d-info {
    background: linear-gradient(to bottom, #3498db, #2980b9);
    color: white;
    box-shadow: 0 3px 0 #2980b9;
}

.btn-3d-info:active {
    box-shadow: 0 1px 0 #2980b9;
}

.btn-3d:hover {
    opacity: 0.9;
    color: white;
}

.btn-change-router {
    padding: 6px 8px;
    font-size: 12px;
    letter-spacing: -0.2px;
}

/* ── Modern customer profile card ───────────────────────────────── */
.sr-profile {
    border: 1px solid #e9edf3 !important;
    border-radius: 16px !important;
    box-shadow: 0 1px 2px rgba(16,24,40,.04), 0 16px 32px -24px rgba(16,24,40,.45) !important;
    overflow: hidden;
    background: #fff;
}
.sr-profile.box-primary { border-top: 3px solid #2563eb !important; }
.sr-profile.box-danger  { border-top: 3px solid #dc2626 !important; }

.sr-profile .box-profile { padding: 18px 18px 16px !important; }

.sr-profile .box-tools { margin: 0 0 6px; }
.sr-profile .box-tools .btn-3d {
    background: #f8fafc !important;
    color: #475569 !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: none !important;
    border-radius: 10px !important;
    padding: 6px 12px !important;
    font-size: 12px !important;
    font-weight: 700;
    transition: background .15s ease, color .15s ease;
}
.sr-profile .box-tools .btn-3d:hover { background: #eef2f7 !important; color: #0f172a !important; }

.sr-profile .profile-user-img {
    width: 92px; height: 92px;
    object-fit: cover;
    display: block;
    margin: 4px auto 12px;
    border: 3px solid #fff;
    box-shadow: 0 0 0 1px #e6eaf0, 0 10px 22px -14px rgba(16,24,40,.5);
    cursor: pointer;
    transition: transform .18s ease, box-shadow .18s ease;
}
.sr-profile .profile-user-img:hover {
    transform: translateY(-2px);
    box-shadow: 0 0 0 1px #dbe2ea, 0 16px 26px -14px rgba(16,24,40,.5);
}

.sr-profile .profile-username {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -.2px;
    margin: 0 0 14px;
}

.sr-profile .list-group { margin: 0 -18px; padding: 0; }
.sr-profile .list-group-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 11px 18px !important;
    border: 0 !important;
    border-bottom: 1px solid #f1f5f9 !important;
    background: transparent !important;
    font-size: 13px;
    transition: background .12s ease;
}
.sr-profile .list-group-item:hover { background: #f8fafc !important; }
.sr-profile .list-group-item:last-child { border-bottom: 0 !important; }
.sr-profile .list-group-item > b {
    flex: 0 0 auto;
    color: #64748b;
    font-weight: 700;
    font-size: 10.5px;
    letter-spacing: .06em;
    text-transform: uppercase;
}
.sr-profile .list-group-item .pull-right {
    float: none !important;
    margin-left: auto;
    text-align: right;
    color: #0f172a;
    font-weight: 600;
    word-break: break-word;
}
.sr-profile .pull-right.bg-red {
    background: #fee2e2 !important;
    color: #b91c1c !important;
    border-radius: 999px;
    padding: 3px 11px !important;
    font-size: 11px;
    font-weight: 700;
}
.sr-profile .pull-right.bg-green {
    background: #dcfce7 !important;
    color: #15803d !important;
    border-radius: 999px;
    padding: 3px 11px !important;
    font-size: 11px;
    font-weight: 700;
}
.sr-profile input[type="password"].pull-right {
    border: 0;
    background: #f1f5f9;
    border-radius: 8px;
    padding: 4px 10px;
    max-width: 60%;
    font-family: ui-monospace, SFMono-Regular, Consolas, monospace;
    font-size: 12.5px;
    color: #0f172a;
    cursor: pointer;
    outline: none !important;
}

.sr-profile hr { border: 0; border-top: 1px solid #eef2f7; margin: 16px 0; }

.sr-profile .btn-3d {
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 0 10px !important;
    border-radius: 10px !important;
    border: 1px solid transparent !important;
    box-shadow: none !important;
    font-size: 12.5px !important;
    font-weight: 700;
    letter-spacing: 0;
    transition: transform .12s ease, background .12s ease, box-shadow .12s ease;
}
.sr-profile .btn-3d:hover { transform: translateY(-1px); opacity: 1 !important; }
.sr-profile .btn-3d:active { transform: translateY(0); box-shadow: none !important; }
.sr-profile .btn-3d-danger { background: #fee2e2 !important; color: #b91c1c !important; border-color: #fecaca !important; }
.sr-profile .btn-3d-danger:hover { background: #fecaca !important; color: #991b1b !important; }
.sr-profile .btn-3d-primary { background: #2563eb !important; color: #fff !important; box-shadow: 0 8px 16px -10px rgba(37,99,235,.9) !important; }
.sr-profile .btn-3d-primary:hover { background: #1d4ed8 !important; }
.sr-profile .btn-3d-info { background: #f1f5f9 !important; color: #334155 !important; border-color: #e2e8f0 !important; }
.sr-profile .btn-3d-info:hover { background: #e2e8f0 !important; color: #0f172a !important; }
.sr-profile .btn-default { background: #fff !important; color: #475569 !important; border-color: #e2e8f0 !important; }
.sr-profile .btn-default:hover { background: #f8fafc !important; color: #0f172a !important; }
.sr-profile .btn-change-router { padding: 0 6px !important; white-space: nowrap; }

.sr-profile > .box-body > .row { margin-left: -4px; margin-right: -4px; }
.sr-profile > .box-body > .row > [class*="col-"] { padding-left: 4px; padding-right: 4px; margin-bottom: 8px; }

.sr-profile .dropdown-menu {
    border-radius: 12px;
    border: 1px solid #e9edf3;
    box-shadow: 0 12px 28px -14px rgba(16,24,40,.45);
    padding: 6px;
}
.sr-profile .dropdown-menu > li > a { border-radius: 8px; font-size: 13px; padding: 8px 12px; }
.sr-profile .dropdown-menu > li > a:hover { background: #f1f5f9; }

/* ===== Whole-page modernisation (scoped to .sr-cust) ===================== */
.sr-cust {
    --c-border: #e9edf3;
    --c-line: #f1f5f9;
    --c-ink: #0f172a;
    --c-muted: #64748b;
    --c-soft: #f8fafc;
    --c-blue: #2563eb;
}

/* Cards */
.sr-cust .box {
    border: 1px solid var(--c-border) !important;
    border-radius: 14px !important;
    box-shadow: 0 1px 2px rgba(16,24,40,.04), 0 12px 26px -22px rgba(16,24,40,.5) !important;
    background: #fff;
    overflow: hidden;
    margin-bottom: 16px;
}
.sr-cust .box.box-primary,
.sr-cust .box.box-info,
.sr-cust .box.box-success,
.sr-cust .box.box-danger,
.sr-cust .box.box-warning { border-top: 1px solid var(--c-border) !important; }

/* Card headers */
.sr-cust .box-header {
    border-bottom: 1px solid var(--c-line) !important;
    padding: 13px 16px !important;
    background: #fff !important;
}
.sr-cust .box-title {
    font-size: 13.5px !important;
    font-weight: 700 !important;
    color: var(--c-ink) !important;
}
.sr-cust .box-title i { color: var(--c-blue); margin-right: 6px; }

/* Card body - no !important so the tab container keeps its inline padding:0 */
.sr-cust .box-body { padding: 16px; }

/* Tabs */
.sr-cust .nav-tabs {
    border-bottom: 1px solid var(--c-line) !important;
    padding: 6px 8px 0 !important;
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
    background: #fff !important;
}
.sr-cust .nav-tabs > li { margin: 0 !important; float: none !important; }
.sr-cust .nav-tabs > li > a {
    border: 0 !important;
    border-bottom: 2px solid transparent !important;
    border-radius: 0 !important;
    margin: 0 !important;
    padding: 9px 13px !important;
    font-size: 12.5px !important;
    font-weight: 600 !important;
    color: var(--c-muted) !important;
    background: transparent !important;
    transition: color .15s ease, border-color .15s ease;
}
.sr-cust .nav-tabs > li > a:hover {
    color: var(--c-ink) !important;
    border-bottom-color: #cbd5e1 !important;
    background: transparent !important;
}
.sr-cust .nav-tabs > li.active > a,
.sr-cust .nav-tabs > li.active > a:hover,
.sr-cust .nav-tabs > li.active > a:focus {
    color: var(--c-blue) !important;
    border-bottom-color: var(--c-blue) !important;
    background: transparent !important;
    font-weight: 700 !important;
}

/* Tables */
.sr-cust .table { margin-bottom: 0 !important; }
.sr-cust .table > thead > tr > th {
    background: var(--c-soft) !important;
    border: 0 !important;
    border-bottom: 1px solid var(--c-line) !important;
    padding: 10px 12px !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: var(--c-muted) !important;
    white-space: nowrap;
}
.sr-cust .table > tbody > tr > td {
    border: 0 !important;
    border-bottom: 1px solid var(--c-line) !important;
    padding: 11px 12px !important;
    font-size: 13px !important;
    color: #334155 !important;
    vertical-align: middle !important;
}
.sr-cust .table > tbody > tr:last-child > td { border-bottom: 0 !important; }
.sr-cust .table-striped > tbody > tr:nth-of-type(odd),
.sr-cust .table-striped > tbody > tr:nth-of-type(even) { background: transparent !important; }
.sr-cust .table-hover > tbody > tr:hover { background: var(--c-soft) !important; }

/* Status labels and badges -> soft pills */
.sr-cust .label,
.sr-cust .badge {
    border-radius: 999px !important;
    padding: 4px 10px !important;
    font-size: 10.5px !important;
    font-weight: 700 !important;
    box-shadow: none !important;
    text-shadow: none !important;
}
.sr-cust .label-default { background: #eef2f7 !important; color: #475569 !important; }
.sr-cust .label-primary { background: #e0e7ff !important; color: #4338ca !important; }
.sr-cust .label-info    { background: #e0f2fe !important; color: #0369a1 !important; }
.sr-cust .label-success { background: #dcfce7 !important; color: #15803d !important; }
.sr-cust .label-warning { background: #fef3c7 !important; color: #b45309 !important; }
.sr-cust .label-danger  { background: #fee2e2 !important; color: #b91c1c !important; }
.sr-cust .badge.bg-blue  { background: #e0e7ff !important; color: #4338ca !important; }
.sr-cust .badge.bg-green { background: #dcfce7 !important; color: #15803d !important; }
.sr-cust .badge.bg-red   { background: #fee2e2 !important; color: #b91c1c !important; }

/* Live online / error dot.
   autoload/customer_is_active returns a label containing only &nbsp;, so it
   renders as a dot rather than text. Keep this one solid, not pastel. */
.sr-cust .label-success[title="online"],
.sr-cust .label-danger[title="error"] {
    display: inline-block;
    width: 13px;
    height: 13px;
    min-width: 13px;
    padding: 0 !important;
    font-size: 0 !important;
    line-height: 0 !important;
    border-radius: 999px !important;
    vertical-align: middle;
    margin-left: 6px;
    position: relative;
    top: -1px;
}
.sr-cust .label-success[title="online"] {
    background: #166534 !important;
    box-shadow: 0 0 0 3px rgba(22,101,52,.22) !important;
}
.sr-cust .label-danger[title="error"] {
    background: #dc2626 !important;
    box-shadow: 0 0 0 3px rgba(220,38,38,.22) !important;
}

/* Buttons */
.sr-cust .btn {
    border-radius: 10px !important;
    font-weight: 700 !important;
    font-size: 12.5px !important;
    box-shadow: none !important;
    border-width: 1px !important;
    transition: background .15s ease, color .15s ease, transform .12s ease;
}
.sr-cust .btn-sm { font-size: 12px !important; padding: 6px 12px !important; }
.sr-cust .btn:hover { transform: translateY(-1px); }
.sr-cust .btn:active { transform: translateY(0); box-shadow: none !important; }
.sr-cust .btn-default { background: #fff !important; color: #475569 !important; border-color: #e2e8f0 !important; }
.sr-cust .btn-default:hover { background: var(--c-soft) !important; color: var(--c-ink) !important; }
.sr-cust .btn-primary, .sr-cust .btn-3d-primary { background: var(--c-blue) !important; color: #fff !important; border-color: var(--c-blue) !important; }
.sr-cust .btn-primary:hover, .sr-cust .btn-3d-primary:hover { background: #1d4ed8 !important; border-color: #1d4ed8 !important; }
.sr-cust .btn-success, .sr-cust .btn-3d-success { background: #16a34a !important; color: #fff !important; border-color: #16a34a !important; }
.sr-cust .btn-success:hover, .sr-cust .btn-3d-success:hover { background: #15803d !important; border-color: #15803d !important; }
.sr-cust .btn-danger, .sr-cust .btn-3d-danger { background: #fee2e2 !important; color: #b91c1c !important; border-color: #fecaca !important; }
.sr-cust .btn-danger:hover, .sr-cust .btn-3d-danger:hover { background: #fecaca !important; color: #991b1b !important; }
.sr-cust .btn-warning, .sr-cust .btn-3d-warning { background: #fef3c7 !important; color: #b45309 !important; border-color: #fde68a !important; }
.sr-cust .btn-warning:hover, .sr-cust .btn-3d-warning:hover { background: #fde68a !important; color: #92400e !important; }
.sr-cust .btn-info, .sr-cust .btn-3d-info { background: #f1f5f9 !important; color: #334155 !important; border-color: #e2e8f0 !important; }
.sr-cust .btn-info:hover, .sr-cust .btn-3d-info:hover { background: #e2e8f0 !important; color: var(--c-ink) !important; }

/* Package cards (everything except the profile card) */
.sr-cust .box:not(.sr-profile) .box-profile > h4 {
    font-size: 14px !important;
    font-weight: 700 !important;
    color: var(--c-ink) !important;
    margin: 0 0 14px !important;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--c-line);
}
.sr-cust .box:not(.sr-profile) .box-profile > h4 small { color: var(--c-muted) !important; font-weight: 600 !important; }
.sr-cust .box:not(.sr-profile) .list-group { margin: 0 !important; }
.sr-cust .box:not(.sr-profile) .list-group-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 0 !important;
    border: 0 !important;
    border-bottom: 1px solid var(--c-line) !important;
    background: transparent !important;
    font-size: 12.5px;
}
.sr-cust .box:not(.sr-profile) .list-group-item:last-child { border-bottom: 0 !important; }
.sr-cust .box:not(.sr-profile) .list-group-item > b {
    flex: 0 0 auto;
    color: var(--c-muted) !important;
    font-weight: 700 !important;
    font-size: 10.5px !important;
    letter-spacing: .06em;
    text-transform: uppercase;
}
.sr-cust .box:not(.sr-profile) .list-group-item .pull-right {
    float: none !important;
    margin-left: auto;
    text-align: right;
    color: var(--c-ink) !important;
    font-weight: 600 !important;
    word-break: break-word;
}

/* ---- Package card colour ----
   The rows above are plain grey-on-white; these rules give the card a status tint
   and turn the key values into readable chips. Scoped with :not(.sr-profile) so the
   customer profile card on the left is untouched. */
.sr-cust .box:not(.sr-profile).box-success { border-top: 3px solid #16a34a !important; }
.sr-cust .box:not(.sr-profile).box-danger  { border-top: 3px solid #dc2626 !important; }

.sr-cust .box:not(.sr-profile) .box-profile > h4 {
    padding: 10px 12px !important;
    border: 1px solid #e9edf3;
    border-radius: 10px;
    background: #f8fafc;
    color: #334155 !important;
}
.sr-cust .box:not(.sr-profile).box-success .box-profile > h4 {
    background: #f0fdf4;
    border-color: #bbf7d0;
    color: #166534 !important;
}
.sr-cust .box:not(.sr-profile).box-danger .box-profile > h4 {
    background: #fef2f2;
    border-color: #fecaca;
    color: #991b1b !important;
}
.sr-cust .box:not(.sr-profile) .box-profile > h4 small {
    color: #64748b !important;
    font-weight: 600 !important;
    font-size: 12px !important;
}

/* Label icons sit in a fixed slot so every row aligns */
.sr-cust .box:not(.sr-profile) .list-group-item > b i {
    width: 14px;
    margin-right: 6px;
    text-align: center;
    font-size: 11px;
    color: #cbd5e1;
}

/* Row rhythm - padding gives the hover tint somewhere to breathe */
.sr-cust .box:not(.sr-profile) .list-group-item {
    padding: 9px 8px !important;
    margin: 0 -8px;
    border-radius: 8px;
}
.sr-cust .box:not(.sr-profile) .list-group-item:hover { background: #f8fafc !important; }

/* Value chips */
.sr-cust .box:not(.sr-profile) .list-group-item .sr-chip {
    display: inline-block;
    padding: 3px 10px !important;
    border: 1px solid transparent;
    border-radius: 999px !important;
    font-size: 11.5px !important;
    font-weight: 700 !important;
    line-height: 1.5;
    white-space: nowrap;
}
.sr-cust .box:not(.sr-profile) .list-group-item .sr-chip i { margin-right: 4px; }
.sr-cust .box:not(.sr-profile) .list-group-item .sr-chip-ok {
    background: #dcfce7 !important; color: #15803d !important; border-color: #bbf7d0 !important;
}
.sr-cust .box:not(.sr-profile) .list-group-item .sr-chip-bad {
    background: #fee2e2 !important; color: #b91c1c !important; border-color: #fecaca !important;
}
.sr-cust .box:not(.sr-profile) .list-group-item .sr-chip-bw {
    background: #eff6ff !important; color: #1d4ed8 !important; border-color: #bfdbfe !important;
}
.sr-cust .box:not(.sr-profile) .list-group-item .sr-chip-exp {
    background: #fff7ed !important; color: #b45309 !important; border-color: #fed7aa !important;
}
.sr-cust .box:not(.sr-profile) .list-group-item .sr-chip-mono {
    background: #f1f5f9 !important;
    color: #334155 !important;
    border-color: #e2e8f0 !important;
    border-radius: 8px !important;
    font-family: ui-monospace, SFMono-Regular, Consolas, monospace;
    font-size: 11px !important;
    font-weight: 600 !important;
    max-width: 62%;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Forms and menus */
.sr-cust .form-control {
    border-radius: 10px !important;
    border-color: #e2e8f0 !important;
    box-shadow: none !important;
    font-size: 13px !important;
}
.sr-cust .form-control:focus {
    border-color: #93c5fd !important;
    box-shadow: 0 0 0 3px rgba(37,99,235,.12) !important;
}
.sr-cust .dropdown-menu {
    border-radius: 12px;
    border: 1px solid var(--c-border);
    box-shadow: 0 12px 28px -14px rgba(16,24,40,.45);
    padding: 6px;
}
.sr-cust .dropdown-menu > li > a { border-radius: 8px; font-size: 13px; padding: 8px 12px; }
.sr-cust .dropdown-menu > li > a:hover { background: var(--c-soft); }

/* Pagination */
.sr-cust .pagination { margin: 14px 0 !important; }
.sr-cust .pagination > li > a,
.sr-cust .pagination > li > span {
    border: 1px solid var(--c-border) !important;
    color: #475569 !important;
    border-radius: 8px !important;
    margin: 0 2px;
    font-size: 12.5px;
}
.sr-cust .pagination > .active > a,
.sr-cust .pagination > .active > span {
    background: var(--c-blue) !important;
    border-color: var(--c-blue) !important;
    color: #fff !important;
}

/* Alerts, misc */
.sr-cust .alert { border-radius: 12px !important; border-width: 1px !important; }
.sr-cust .alert-success { background: #f0fdf4 !important; border-color: #bbf7d0 !important; color: #166534 !important; }
.sr-cust .alert-danger  { background: #fef2f2 !important; border-color: #fecaca !important; color: #991b1b !important; }
.sr-cust .alert-warning { background: #fffbeb !important; border-color: #fde68a !important; color: #92400e !important; }
.sr-cust .alert-info    { background: #eff6ff !important; border-color: #bfdbfe !important; color: #1e40af !important; }
.sr-cust hr { border: 0 !important; border-top: 1px solid var(--c-line) !important; margin: 16px 0 !important; }
.sr-cust code { background: #f1f5f9; border-radius: 6px; padding: 2px 6px; color: #334155; font-size: 12px; }

/* ---- Footer action bar ---- */
.sr-cust .sr-actionbar {
    background: #fff;
    border: 1px solid var(--c-border);
    border-radius: 14px;
    padding: 12px !important;
    margin: 0 0 18px !important;
    box-shadow: 0 1px 2px rgba(16,24,40,.04), 0 12px 26px -22px rgba(16,24,40,.5);
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.sr-cust .sr-actionbar > [class*="col-"] {
    padding: 0 !important;
    margin: 0 !important;
    float: none !important;
    width: auto !important;
    flex: 1 1 160px;
}
.sr-cust .sr-actionbar .btn {
    width: 100%;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    font-size: 12.5px !important;
}
.sr-cust .sr-actionbar .btn-3d-success { box-shadow: 0 8px 16px -10px rgba(22,163,74,.85) !important; }
.sr-cust .sr-actionbar .btn-3d-primary { box-shadow: 0 8px 16px -10px rgba(37,99,235,.85) !important; }
.sr-cust .sr-actionbar .btn-default {
    background: #f8fafc !important;
    border-color: #dbe3ec !important;
    color: #334155 !important;
}
.sr-cust .sr-actionbar .btn-default:hover {
    background: #eef2f7 !important;
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}
.sr-cust .sr-actionbar .btn-3d-info {
    background: #f59e0b !important;
    border-color: #f59e0b !important;
    color: #fff !important;
    box-shadow: 0 8px 16px -10px rgba(217,119,6,.9) !important;
}
.sr-cust .sr-actionbar .btn-3d-info:hover {
    background: #d97706 !important;
    border-color: #d97706 !important;
    color: #fff !important;
}

/* ---- Live bandwidth metric tiles ---- */
.sr-cust .sr-metric {
    border-radius: 12px;
    padding: 10px 13px;
    border: 1px solid transparent;
}
.sr-cust .sr-metric-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 2px;
}
.sr-cust .sr-metric-value {
    font-size: 20px;
    font-weight: 700;
    letter-spacing: -.4px;
}
.sr-cust .sr-metric-dl { background: #f0fdf4; border-color: #bbf7d0; }
.sr-cust .sr-metric-dl .sr-metric-label { color: #15803d; }
.sr-cust .sr-metric-dl .sr-metric-value { color: #166534; }
.sr-cust .sr-metric-ul { background: #eff6ff; border-color: #bfdbfe; }
.sr-cust .sr-metric-ul .sr-metric-label { color: #1d4ed8; }
.sr-cust .sr-metric-ul .sr-metric-value { color: #1e40af; }
.sr-cust .sr-metric-total { background: #fff7ed; border-color: #fed7aa; }
.sr-cust .sr-metric-total .sr-metric-label { color: #b45309; }
.sr-cust .sr-metric-total .sr-metric-value { color: #9a3412; }

/* ---- Live bandwidth panel ---- */
.sr-cust #live-graph-box .box-footer {
    background: transparent !important;
    border-top: 1px solid var(--c-line) !important;
    padding: 9px 16px !important;
}
.sr-cust #live-toggle {
    width: 30px;
    height: 30px;
    padding: 0 !important;
    border-radius: 50% !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9 !important;
    border: 1px solid #e2e8f0 !important;
    color: #334155 !important;
}
.sr-cust #live-toggle:hover { background: #e2e8f0 !important; color: #0f172a !important; }
.sr-cust #live-session-type { font-size: 10.5px !important; }
.sr-cust .sr-lastupdated {
    background: transparent !important;
    border-top: 1px solid var(--c-line) !important;
    padding: 9px 16px !important;
}

/* ---- Dropdown clipping fix -------------------------------------------
   The rounded-card treatment set `overflow: hidden` on .box / .sr-profile to
   clip child backgrounds into the radius. That also clipped the "Actions"
   dropdown menu, hiding it behind the card. Nothing inside these cards
   actually reaches a corner: .sr-cust .box-header is forced to #fff, both
   box-footers are transparent, and list groups sit inside the body padding -
   so the clipping was only ever hiding the menu.
   Declared after the rules above (same specificity, later wins). Cards that
   set overflow:hidden inline keep their own clip, since inline beats this. */
.sr-cust .box,
.sr-cust .sr-profile { overflow: visible; }

/* Anchor the Actions menu to the right edge so it stays inside the card
   instead of running off it, and stop the labels wrapping mid-word. */
.sr-profile .box-tools .dropdown-menu {
    right: 0;
    left: auto;
    min-width: 200px;
    margin-top: 6px;
}
.sr-profile .box-tools .dropdown-menu > li > a { white-space: nowrap; }
.sr-profile .box-tools .dropdown-menu > li > a > i {
    width: 16px;
    text-align: center;
    margin-right: 4px;
    color: #94a3b8;
}
.sr-profile .box-tools .dropdown-menu > li > a:hover > i { color: #475569; }
</style>

<div class="sr-cust">
<div class="row">
    <div class="col-sm-4 col-md-4">
        <div class="box box-{if $d['status']=='Active'}primary{else}danger{/if} sr-profile">
            <div class="box-body box-profile">
                <div class="box-tools pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-3d btn-3d-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-gear"></i> {Lang::T('Actions')} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{$_url}customers/sync/{$d['id']}&token={$csrf_token}" onclick="return ask(this, 'This will sync Customer to Mikrotik?')"><i class="fa fa-refresh"></i> {Lang::T('Sync')}</a></li>
                            <li><a href="{$_url}customers/reconnect/{$d['id']}&token={$csrf_token}" onclick="return ask(this, 'This will disconnect and reconnect the customer. Continue?')"><i class="fa fa-power-off"></i> {Lang::T('Reconnect')}</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="{$_url}customers/enable/{$d['id']}&token={$csrf_token}" onclick="return ask(this, 'This will enable the customer on Mikrotik router. Continue?')"><i class="fa fa-play"></i> {Lang::T('Enable Customer')}</a></li>
                            <li><a href="{$_url}customers/disable/{$d['id']}&token={$csrf_token}" onclick="return ask(this, 'This will disable the customer on Mikrotik router and disconnect them. Continue?')"><i class="fa fa-stop"></i> {Lang::T('Disable Customer')}</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="{$_url}message/send/{$d['id']}&token={$csrf_token}"><i class="fa fa-envelope"></i> {Lang::T('Send Message')}</a></li>
                            <li><a href="{$_url}customers/login/{$d['id']}&token={$csrf_token}" target="_blank"><i class="fa fa-sign-in"></i> {Lang::T('Login as Customer')}</a></li>
                        </ul>
                    </div>
                </div>
                <img class="profile-user-img img-responsive img-circle"
                    onclick="window.location.href = '{$UPLOAD_PATH}{$d['photo']}'"
                    src="{$UPLOAD_PATH}{$d['photo']}.thumb.jpg"
                    onerror="this.src='{$UPLOAD_PATH}/user.default.jpg'" alt="avatar">
                <h3 class="profile-username text-center">{$d['fullname']}</h3>
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>{Lang::T('Status')}</b> <span
                            class="pull-right {if $d['status'] !='Active'}bg-red{else}bg-green{/if}">{Lang::T($d['status'])}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Username')}</b> <span class="pull-right">{$d['username']}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Phone Number')}</b> <span class="pull-right">{$d['phonenumber']}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Email')}</b> <span class="pull-right">{$d['email']}</span>
                    </li>
                    <li class="list-group-item">{Lang::nl2br($d['address'])}</li>
                    <li class="list-group-item">
                        <b>{Lang::T('City')}</b> <span class="pull-right">{$d['city']}</span>
                    </li>
                    {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                        <li class="list-group-item">
                            <b>{Lang::T('Password')}</b> <input type="password" value="{$d['password']}"
                                style=" border: 0px; text-align: right;" class="pull-right"
                                onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'"
                                onclick="this.select()">
                        </li>
                    {/if}
                    {if $d['pppoe_username'] != ''}
                        <li class="list-group-item">
                            <b>PPPOE {Lang::T('Username')}</b> <span class="pull-right">{$d['pppoe_username']}</span>
                        </li>
                    {/if}
                    {if $d['pppoe_password'] != '' && in_array($_admin['user_type'],['SuperAdmin','Admin'])}
                        <li class="list-group-item">
                            <b>PPPOE {Lang::T('Password')}</b> <input type="password" value="{$d['pppoe_password']}"
                                style=" border: 0px; text-align: right;" class="pull-right"
                                onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'"
                                onclick="this.select()">
                        </li>
                    {/if}
                    {if $d['pppoe_ip'] != ''}
                        <li class="list-group-item">
                            <b>PPPOE Remote IP</b> <span class="pull-right">{$d['pppoe_ip']}</span>
                        </li>
                    {/if}
                    <!--Customers Attributes view start -->
                    {if $customFields}
                        {foreach $customFields as $customField}
                            <li class="list-group-item">
                                <b>{$customField.field_name}</b> <span class="pull-right">
                                    {if strpos($customField.field_value, ':0') === false}
                                        {$customField.field_value}
                                    {else}
                                        <b>{Lang::T('Paid')}</b>
                                    {/if}
                                </span>
                            </li>
                        {/foreach}
                    {/if}
                    <!--Customers Attributes view end -->
                    <li class="list-group-item">
                        <b>{Lang::T('Service Type')}</b> <span class="pull-right">{Lang::T($d['service_type'])}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Account Type')}</b> <span class="pull-right">{Lang::T($d['account_type'])}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Balance')}</b> <span class="pull-right">{Lang::moneyFormat($d['balance'])}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Auto Renewal')}</b> <span class="pull-right">{if
                            $d['auto_renewal']}yes{else}no
                            {/if}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Created On')}</b> <span
                            class="pull-right">{Lang::dateTimeFormat($d['created_at'])}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{Lang::T('Last Login')}</b> <span
                            class="pull-right">{Lang::dateTimeFormat($d['last_login'])}</span>
                    </li>
                    {if $d['coordinates']}
                        <li class="list-group-item">
                            <b>{Lang::T('Coordinates')}</b> <span class="pull-right">
                                <i class="glyphicon glyphicon-road"></i> <a style="color: black;"
                                    href="https://www.google.com/maps/dir//{$d['coordinates']}/"
                                    target="_blank">{Lang::T('Get Directions')}</a>
                            </span>
                        </li>
                    {/if}
                </ul>
                <div class="row">
                    <div class="col-xs-4">
                        <button type="button" onclick="confirmDelete('{$_url}customers/delete/{$d['id']}&token={$csrf_token}')" 
                            class="btn btn-3d btn-3d-danger btn-sm btn-block"><i class="fa fa-trash"></i> {Lang::T('Delete')}</button>
                    </div>
                    <div class="col-xs-4">
                        <a href="{$_url}customers/edit/{$d['id']}&token={$csrf_token}"
                            class="btn btn-3d btn-3d-primary btn-sm btn-block"><i class="fa fa-pencil"></i> {Lang::T('Edit')}</a>
                    </div>
                    <div class="col-xs-4">
                        <a href="{$_url}customers/change_router/{$d['id']}&token={$csrf_token}" 
                            class="btn btn-3d btn-3d-info btn-sm btn-block btn-change-router">
                            <i class="fa fa-random"></i> {Lang::T('Change Router')}
                        </a>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-xs-12">
                        <a href="{$_url}customers/list" class="btn btn-3d btn-3d btn-default btn-sm btn-block"><i class="fa fa-arrow-left"></i> {Lang::T('Back')}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-8 col-md-8">
        <div class="box box-success">
            <div class="box-body">
                <div class="row">
                    {if $_c['enable_balance'] == 'yes' && $_c['extend_expired']}
                    <div class="col-xs-6 col-sm-3" style="margin-bottom:6px">
                        <a href="{$_url}plan/recharge/{$d['id']}" class="btn btn-3d btn-3d-success btn-block">
                            <i class="fa fa-credit-card"></i> {Lang::T('Recharge Account')}
                        </a>
                    </div>
                    <div class="col-xs-6 col-sm-3" style="margin-bottom:6px">
                        <a href="{$_url}plan/deposit/{$d['id']}" class="btn btn-3d btn-3d-primary btn-block">
                            <i class="fa fa-money"></i> {Lang::T('Add Balance')}
                        </a>
                    </div>
                    {if $_admin['user_type'] == 'SuperAdmin' || $_admin['user_type'] == 'Admin'}
                    <div class="col-xs-6 col-sm-3" style="margin-bottom:6px">
                        <a href="{$_url}plan/deduct/{$d['id']}" class="btn btn-3d btn-3d-danger btn-block">
                            <i class="fa fa-minus-circle"></i> {Lang::T('Subtract Balance')}
                        </a>
                    </div>
                    {/if}
                    <div class="col-xs-6 col-sm-3" style="margin-bottom:6px">
                        <button onclick="extendCustomerPlan('{$d['id']}')" class="btn btn-3d btn-3d-warning btn-block">
                            <i class="fa fa-clock-o"></i> {Lang::T('Extend')}
                        </button>
                    </div>
                    {elseif $_c['enable_balance'] == 'yes'}
                    <div class="col-xs-6 col-sm-4" style="margin-bottom:6px">
                        <a href="{$_url}plan/recharge/{$d['id']}" class="btn btn-3d btn-3d-success btn-block">
                            <i class="fa fa-credit-card"></i> {Lang::T('Recharge Account')}
                        </a>
                    </div>
                    <div class="col-xs-6 col-sm-4" style="margin-bottom:6px">
                        <a href="{$_url}plan/deposit/{$d['id']}" class="btn btn-3d btn-3d-primary btn-block">
                            <i class="fa fa-money"></i> {Lang::T('Add Balance')}
                        </a>
                    </div>
                    {if $_admin['user_type'] == 'SuperAdmin' || $_admin['user_type'] == 'Admin'}
                    <div class="col-xs-12 col-sm-4" style="margin-bottom:6px">
                        <a href="{$_url}plan/deduct/{$d['id']}" class="btn btn-3d btn-3d-danger btn-block">
                            <i class="fa fa-minus-circle"></i> {Lang::T('Subtract Balance')}
                        </a>
                    </div>
                    {/if}
                    {elseif $_c['extend_expired']}
                    <div class="col-xs-6" style="margin-bottom:6px">
                        <a href="{$_url}plan/recharge/{$d['id']}" class="btn btn-3d btn-3d-success btn-block">
                            <i class="fa fa-credit-card"></i> {Lang::T('Recharge Account')}
                        </a>
                    </div>
                    <div class="col-xs-6" style="margin-bottom:6px">
                        <button onclick="extendCustomerPlan('{$d['id']}')" class="btn btn-3d btn-3d-warning btn-block">
                            <i class="fa fa-clock-o"></i> {Lang::T('Extend')}
                        </button>
                    </div>
                    {else}
                    <div class="col-xs-12">
                        <a href="{$_url}plan/recharge/{$d['id']}" class="btn btn-3d btn-3d-success btn-block">
                            <i class="fa fa-credit-card"></i> {Lang::T('Recharge Account')}
                        </a>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
        <div class="box box-info">
            <ul class="nav nav-tabs">
                <li role="presentation" {if $v=='order' }class="active" {/if}><a
                        href="{$_url}customers/view/{$d['id']}/order">{Lang::T('Order History')}</a></li>
                <li role="presentation" {if $v=='activation' }class="active" {/if}><a
                        href="{$_url}customers/view/{$d['id']}/activation">{Lang::T('Activation History')}</a></li>
                <li role="presentation" {if $v=='tickets' }class="active" {/if}><a
                        href="{$_url}customers/view/{$d['id']}/tickets"><i class="fa fa-ticket"></i> Support Tickets</a></li>
                <li role="presentation" {if $v=='smslogs' }class="active" {/if}><a
                        href="{$_url}customers/view/{$d['id']}/smslogs"><i class="fa fa-comments"></i> SMS Logs</a></li>
                <li role="presentation" {if $v=='mklogs' }class="active" {/if}><a
                        href="{$_url}customers/view/{$d['id']}/mklogs"><i class="fa fa-terminal"></i> MT Logs</a></li>
            </ul>
            <div class="box-body" style="padding:0;">
            {if $v=='activation'}
            <div class="table-responsive">
                <table class="table table-bordered table-striped" style="white-space:nowrap;">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>{Lang::T('Plan Name')}</th>
                            <th>{Lang::T('Plan Price')}</th>
                            <th>{Lang::T('Type')}</th>
                            <th>{Lang::T('Created On')}</th>
                            <th>{Lang::T('Expires On')}</th>
                            <th>{Lang::T('Method')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if Lang::arrayCount($activation)}
                            {foreach $activation as $ds}
                                <tr onclick="window.location.href = '{$_url}plan/view/{$ds['id']}'" style="cursor:pointer;">
                                    <td>{if $ds['invoice']}{$ds['invoice']}{else}#{$ds['id']}{/if}</td>
                                    <td>{$ds['plan_name']}</td>
                                    <td>{Lang::moneyFormat($ds['price'])}</td>
                                    <td>{$ds['type']}</td>
                                    <td class="text-success">{Lang::dateAndTimeFormat($ds['recharged_on'],$ds['recharged_time'])}</td>
                                    <td class="text-danger">{Lang::dateAndTimeFormat($ds['expiration'],$ds['time'])}</td>
                                    <td>{$ds['method']}</td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr><td colspan="7" class="text-center text-muted" style="padding:20px;">{Lang::T('No activation records found.')}</td></tr>
                        {/if}
                    </tbody>
                </table>
            </div>

            {elseif $v=='order'}
            <div class="table-responsive">
                <table class="table table-bordered table-striped" style="white-space:nowrap;">
                    <thead>
                        <tr>
                            <th>{Lang::T('Plan Name')}</th>
                            <th>{Lang::T('Gateway')}</th>
                            <th>{Lang::T('Routers')}</th>
                            <th>{Lang::T('Type')}</th>
                            <th>{Lang::T('Plan Price')}</th>
                            <th>{Lang::T('Created On')}</th>
                            <th>{Lang::T('Expires On')}</th>
                            <th>{Lang::T('Date Done')}</th>
                            <th>{Lang::T('Method')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if Lang::arrayCount($order)}
                            {foreach $order as $ds}
                                <tr>
                                    <td>{$ds['plan_name']}</td>
                                    <td>{$ds['gateway']}</td>
                                    <td>{$ds['routers']}</td>
                                    <td>{$ds['payment_channel']}</td>
                                    <td>{Lang::moneyFormat($ds['price'])}</td>
                                    <td class="text-primary">{Lang::dateTimeFormat($ds['created_date'])}</td>
                                    <td class="text-danger">{Lang::dateTimeFormat($ds['expired_date'])}</td>
                                    <td class="text-success">{if $ds['status']!=1}{Lang::dateTimeFormat($ds['paid_date'])}{/if}</td>
                                    <td>{if $ds['status']==1}{Lang::T('UNPAID')}
                                        {elseif $ds['status']==2}{Lang::T('PAID')}
                                        {elseif $ds['status']==3}{$_L['FAILED']}
                                        {elseif $ds['status']==4}{Lang::T('CANCELED')}
                                        {elseif $ds['status']==5}{Lang::T('UNKNOWN')}
                                        {/if}</td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr><td colspan="9" class="text-center text-muted" style="padding:20px;">{Lang::T('No order records found.')}</td></tr>
                        {/if}
                    </tbody>
                </table>
            </div>

            {elseif $v=='tickets'}
            <div class="table-responsive">
                <table class="table table-bordered table-striped tab-history-table">
                    <thead>
                        <tr>
                            <th>Ticket #</th>
                            <th>Subject</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Last Update</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if $tickets|@count > 0}
                            {foreach $tickets as $ticket}
                                <tr>
                                    <td><strong>{$ticket.ticket_number}</strong></td>
                                    <td>{$ticket.subject}</td>
                                    <td>{if $ticket.category_name}<span class="badge badge-info">{$ticket.category_name}</span>{else}-{/if}</td>
                                    <td>
                                        {if $ticket.priority == 'Urgent'}<span class="label label-danger">Urgent</span>
                                        {elseif $ticket.priority == 'High'}<span class="label label-warning">High</span>
                                        {elseif $ticket.priority == 'Normal'}<span class="label label-info">Normal</span>
                                        {else}<span class="label label-default">Low</span>{/if}
                                    </td>
                                    <td>
                                        {if $ticket.status == 'Closed'}<span class="label label-success">Closed</span>
                                        {elseif $ticket.status == 'In Progress'}<span class="label label-primary">In Progress</span>
                                        {elseif $ticket.status == 'Pending'}<span class="label label-warning">Pending</span>
                                        {else}<span class="label label-default">Open</span>{/if}
                                    </td>
                                    <td>{date('M d, Y', strtotime($ticket.created_at))}</td>
                                    <td>{date('M d, Y H:i', strtotime($ticket.updated_at))}</td>
                                    <td>
                                        <a href="{$_url}plugin/support_tickets&action=view&id={$ticket.id}" class="btn btn-info btn-xs">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="8" class="text-center" style="padding:20px;">
                                    <p class="text-muted">No support tickets found for this customer.</p>
                                    <a href="{$_url}plugin/support_tickets&action=add" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus"></i> Create New Ticket
                                    </a>
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>

            {elseif $v=='smslogs'}
            <div class="table-responsive">
                <table class="table table-bordered table-striped tab-history-table">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>Phone</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Message ID</th>
                            <th>Error Reason</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if $smslogs|@count > 0}
                            {foreach $smslogs as $sms}
                                <tr>
                                    <td>{date('Y-m-d H:i:s', strtotime($sms.created_at))}</td>
                                    <td>{$sms.phone}</td>
                                    <td style="max-width: 300px; word-wrap: break-word; white-space: normal;">{$sms.message}</td>
                                    <td>
                                        {if strtolower($sms.status) == 'sent' || strtolower($sms.status) == 'success'}<span class="label label-success">Sent</span>
                                        {elseif strtolower($sms.status) == 'failed' || strtolower($sms.status) == 'error'}<span class="label label-danger">Failed</span>
                                        {else}<span class="label label-info">{$sms.status}</span>{/if}
                                    </td>
                                    <td><small class="text-muted">{if $sms.message_id}{$sms.message_id}{else}N/A{/if}</small></td>
                                    <td><small class="text-muted" style="color:{if strtolower($sms.status)=='failed'}#c0392b{else}#27ae60{/if};">{if $sms.status_message}{$sms.status_message}{else}-{/if}</small></td>
                                    <td>
                                        <a href="{$_url}customers/resend_sms/{$sms.id}/{$d['id']}&token={$csrf_token}"
                                           class="btn btn-xs btn-default"
                                           onclick="return confirm('Resend this SMS to {$sms.phone}?')">
                                            <i class="fa fa-send"></i> Resend
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 30px;">
                                    <i class="fa fa-comments-o fa-3x text-muted" style="opacity: 0.3;"></i>
                                    <p class="text-muted" style="margin-top: 10px;">No SMS logs found for this customer.</p>
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>

            {elseif $v=='mklogs'}
            <div class="table-responsive">
                <table class="table table-bordered table-striped tab-history-table">
                    <thead>
                        <tr>
                            <th style="width:15%">Time</th>
                            <th style="width:20%">Topics</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody id="mklogs-tbody">
                        <tr>
                            <td colspan="3" class="text-center" style="padding:24px;">
                                <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                                <p class="text-muted" style="margin-top:8px;">Loading MikroTik logs&hellip;</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {/if}
            {include file="pagination.tpl"}
            </div>
        </div>
        <div class="row">
            {foreach $packages as $package}
                <div class="col-md-6">
                    <div class="box box-{if $package['status']=='on'}success{else}danger{/if}" data-package-id="{$package['id']}">
                        <div class="box-body box-profile">
                            <h4 class="text-center">{$package['type']} - {$package['namebp']} <span
                                    api-get-text="{$_url}autoload/customer_is_active/{$package['username']}/{$package['plan_id']}"></span>
                                <small class="text-muted" style="font-size:13px;font-weight:normal;"> &nbsp;|&nbsp; {Lang::moneyFormat($package['price'])}</small>
                            </h4>
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                    <b><i class="fa fa-power-off"></i>{Lang::T('Active')}</b>
                                    <span class="pull-right sr-chip {if $package['status']=='on'}sr-chip-ok{else}sr-chip-bad{/if}">{if $package['status']=='on'}{Lang::T('Yes')}{else}{Lang::T('No')}{/if}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fa fa-tag"></i>{Lang::T('Type')}</b>
                                    <span class="pull-right">{if $package['prepaid'] eq yes}{Lang::T('Prepaid')}{else}{Lang::T('Postpaid')}{/if}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fa fa-tachometer"></i>{Lang::T('Bandwidth')}</b>
                                    <span class="pull-right sr-chip sr-chip-bw">{$package['name_bw']}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fa fa-calendar-plus-o"></i>{Lang::T('Created On')}</b>
                                    <span class="pull-right">{Lang::dateAndTimeFormat($package['recharged_on'],$package['recharged_time'])}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fa fa-hourglass-half"></i>{Lang::T('Expires On')}</b>
                                    <span class="pull-right sr-chip sr-chip-exp">{Lang::dateAndTimeFormat($package['expiration'],$package['time'])}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fa fa-server"></i>{Lang::T('Router')}</b>
                                    <span class="pull-right sr-chip sr-chip-mono">{$package['routers']}</span>
                                </li>
                                <li class="list-group-item">
                                    <b><i class="fa fa-credit-card"></i>{Lang::T('Method')}</b>
                                    <span class="pull-right sr-chip sr-chip-mono">{$package['method']}</span>
                                </li>
                            </ul>
                            <div class="row" style="margin-bottom: 10px;">
                                {if $_c['extend_expired']}
                                <div class="col-xs-4">
                                    <a href="{$_url}customers/deactivate/{$d['id']}/{$package['plan_id']}&token={$csrf_token}" id="{$d['id']}"
                                        class="btn btn-3d btn-3d-danger btn-sm btn-block"
                                        onclick="return ask(this, 'This will deactivate Customer Plan, and make it expired')">{Lang::T('Deactivate')}</a>
                                </div>
                                <div class="col-xs-4">
                                    <a href="{$_url}plan/edit/{$package['id']}&token={$csrf_token}"
                                        class="btn btn-3d btn-3d-primary btn-sm btn-block">{Lang::T('Edit Plan')}</a>
                                </div>
                                <div class="col-xs-4">
                                    <a href="{$_url}customers/recharge/{$d['id']}/{$package['plan_id']}&token={$csrf_token}"
                                        class="btn btn-3d btn-3d-success btn-sm btn-block">{Lang::T('Recharge')}</a>
                                </div>
                                <div class="col-xs-6" style="margin-top: 5px;">
                                    <button onclick="extendPackage('{$package['id']}')" class="btn btn-3d btn-3d-warning btn-sm btn-block">
                                        <i class="fa fa-clock-o"></i> {Lang::T('Extend')}
                                    </button>
                                </div>
                                <div class="col-xs-6" style="margin-top: 5px;">
                                    <a href="{$_url}customers/delete_package/{$d['id']}/{$package['id']}&token={$csrf_token}" id="{$d['id']}"
                                        class="btn btn-3d btn-3d-danger btn-sm btn-block"
                                        onclick="return ask(this, 'This will permanently delete this package. Are you sure?')"><i class="fa fa-trash"></i> Delete</a>
                                </div>
                                {else}
                                <div class="col-xs-4">
                                    <a href="{$_url}customers/deactivate/{$d['id']}/{$package['plan_id']}&token={$csrf_token}" id="{$d['id']}"
                                        class="btn btn-3d btn-3d-danger btn-sm btn-block"
                                        onclick="return ask(this, 'This will deactivate Customer Plan, and make it expired')">{Lang::T('Deactivate')}</a>
                                </div>
                                <div class="col-xs-4">
                                    <a href="{$_url}plan/edit/{$package['id']}&token={$csrf_token}"
                                        class="btn btn-3d btn-3d-primary btn-sm btn-block">{Lang::T('Edit Plan')}</a>
                                </div>
                                <div class="col-xs-4">
                                    <a href="{$_url}customers/recharge/{$d['id']}/{$package['plan_id']}&token={$csrf_token}"
                                        class="btn btn-3d btn-3d-success btn-sm btn-block">{Lang::T('Recharge')}</a>
                                </div>
                                <div class="col-xs-6" style="margin-top: 5px;">
                                    <button onclick="extendPackage('{$package['id']}')" class="btn btn-3d btn-3d-warning btn-sm btn-block">
                                        <i class="fa fa-clock-o"></i> {Lang::T('Extend')}
                                    </button>
                                </div>
                                <div class="col-xs-6" style="margin-top: 5px;">
                                    <a href="{$_url}customers/delete_package/{$d['id']}/{$package['id']}&token={$csrf_token}" id="{$d['id']}"
                                        class="btn btn-3d btn-3d-danger btn-sm btn-block"
                                        onclick="return ask(this, 'This will permanently delete this package. Are you sure?')"><i class="fa fa-trash"></i> Delete</a>
                                </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>

{if isset($devices) && count($devices) > 0}
<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:8px; padding-bottom:10px;">
                <h3 class="box-title" style="margin:0;"><i class="fa fa-wifi"></i> {Lang::T('Connected Devices')} <span data-toggle="tooltip" title="Total Connected Devices" class="badge bg-blue">{count($devices)}</span></h3>
                <div style="display:flex; flex-wrap:wrap; gap:4px; align-items:center;">
                    {if $customer_enabled === true}
                        <a href="{$_url}customers/disable/{$d['id']}&token={$csrf_token}"
                           onclick="return ask(this, 'This will disable the customer on Mikrotik router and disconnect them. Continue?')"
                           class="btn btn-3d btn-3d-success btn-sm">
                            <i class="fa fa-toggle-on"></i> ON — {Lang::T('Turn Off')}
                        </a>
                    {elseif $customer_enabled === false}
                        <a href="{$_url}customers/enable/{$d['id']}&token={$csrf_token}"
                           onclick="return ask(this, 'This will enable the customer on Mikrotik router. Continue?')"
                           class="btn btn-3d btn-3d-danger btn-sm">
                            <i class="fa fa-toggle-off"></i> OFF — {Lang::T('Turn On')}
                        </a>
                    {else}
                        <span class="label label-default" style="padding:8px 10px;"><i class="fa fa-question-circle"></i> {Lang::T('Status unavailable')}</span>
                    {/if}
                    <a href="{$_url}customers/reconnect/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will disconnect and reconnect the customer. Continue?')"
                       class="btn btn-3d btn-3d-warning btn-sm">
                        <i class="fa fa-refresh"></i> {Lang::T('Reconnect')}
                    </a>
                </div>
            </div>
            <div class="box-body no-padding">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="width: 10%">{Lang::T('Type')}</th>
                                <th style="width: 15%">{Lang::T('MAC Address')}</th>
                                <th style="width: 12%">{Lang::T('IP Address')}</th>
                                <th style="width: 18%">{Lang::T('Host Name')}</th>
                                <th style="width: 12%">{Lang::T('Download')}</th>
                                <th style="width: 12%">{Lang::T('Upload')}</th>
                                <th style="width: 13%">{Lang::T('Total Usage')}</th>
                                <th style="width: 8%">{Lang::T('Uptime')}</th>
                                <th style="width: 10%">{Lang::T('Status')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $devices as $device}
                                <tr>
                                    <td>
                                        {if $device['type'] eq 'Hotspot'}
                                            <span class="label label-primary" style="font-size: 12px;">
                                                <i class="fa fa-dot-circle-o"></i> {$device['type']}
                                            </span>
                                        {else}
                                            <span class="label label-info" style="font-size: 12px;">
                                                <i class="fa fa-plug"></i> {$device['type']}
                                            </span>
                                        {/if}
                                    </td>
                                    <td>
                                        <code style="background: #f8f9fa; padding: 5px 8px; border-radius: 4px; color: #495057; font-size: 13px;">
                                            <i class="fa fa-microchip"></i> {$device['mac_address']}
                                        </code>
                                    </td>
                                    <td>
                                        <a href="http://{$device['ip_address']}" target="_blank" rel="noopener" title="Open device web interface" style="text-decoration:none;">
                                            <code style="background: #eef4ff; padding: 5px 8px; border-radius: 4px; color: #2563eb; font-size: 13px; cursor: pointer;">
                                                <i class="fa fa-globe"></i> {$device['ip_address']}
                                            </code>
                                        </a>
                                    </td>
                                    <td>
                                        <code style="background: #f8f9fa; padding: 5px 8px; border-radius: 4px; color: #495057; font-size: 13px;">
                                            <i class="fa fa-desktop"></i> {$device['hostname']}
                                        </code>
                                    </td>
                                    <td>
                                        <span class="label label-success" style="font-size: 11px;">
                                            <i class="fa fa-download"></i> 
                                            <span class="data-usage" data-bytes="{$device['bytes_out']}">Loading...</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="label label-info" style="font-size: 11px;">
                                            <i class="fa fa-upload"></i> 
                                            <span class="data-usage" data-bytes="{$device['bytes_in']}">Loading...</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="label label-warning" style="font-size: 11px;">
                                            <i class="fa fa-exchange"></i> 
                                            <span class="total-usage" data-in="{$device['bytes_in']}" data-out="{$device['bytes_out']}">Loading...</span>
                                        </span>
                                    </td>
                                    <td>
                                        <i class="fa fa-clock-o"></i> {$device['uptime']}
                                    </td>
                                    <td>
                                        <span class="label label-success">
                                            <i class="fa fa-check-circle"></i> Active
                                        </span>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box-footer text-center sr-lastupdated">
                <small class="text-muted">
                    <i class="fa fa-info-circle"></i> Last Updated: {$smarty.now|date_format:"%H:%M:%S"}
                </small>
            </div>
        </div>
    </div>
</div>
{else}
<div class="row">
    <div class="col-sm-12">
        <div class="box box-info">
            <div class="box-header with-border" style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:8px; padding-bottom:10px;">
                <h3 class="box-title" style="margin:0;"><i class="fa fa-wifi"></i> {Lang::T('Router Control')}</h3>
                <div style="display:flex; flex-wrap:wrap; gap:4px; align-items:center;">
                    {if $customer_enabled === true}
                        <a href="{$_url}customers/disable/{$d['id']}&token={$csrf_token}"
                           onclick="return ask(this, 'This will disable the customer on Mikrotik router and disconnect them. Continue?')"
                           class="btn btn-3d btn-3d-success btn-sm">
                            <i class="fa fa-toggle-on"></i> ON — {Lang::T('Turn Off')}
                        </a>
                    {elseif $customer_enabled === false}
                        <a href="{$_url}customers/enable/{$d['id']}&token={$csrf_token}"
                           onclick="return ask(this, 'This will enable the customer on Mikrotik router. Continue?')"
                           class="btn btn-3d btn-3d-danger btn-sm">
                            <i class="fa fa-toggle-off"></i> OFF — {Lang::T('Turn On')}
                        </a>
                    {else}
                        <span class="label label-default" style="padding:8px 10px;"><i class="fa fa-question-circle"></i> {Lang::T('Status unavailable')}</span>
                    {/if}
                    <a href="{$_url}customers/reconnect/{$d['id']}&token={$csrf_token}" 
                       onclick="return ask(this, 'This will disconnect and reconnect the customer. Continue?')"
                       class="btn btn-3d btn-3d-warning btn-sm">
                        <i class="fa fa-refresh"></i> {Lang::T('Reconnect')}
                    </a>
                </div>
            </div>
            <div class="box-body text-center" style="padding: 40px;">
                <i class="fa fa-wifi" style="font-size: 48px; color: #bbb; margin-bottom: 15px;"></i>
                <h4 style="color: #666;">{Lang::T('No Connected Devices')}</h4>
                <p class="text-muted">{Lang::T('This customer has no active connections at the moment.')}</p>
            </div>
        </div>
    </div>
</div>
{/if}

<div class="row sr-actionbar">
    <div class="col-xs-6 col-md-3">
        <a href="{$_url}customers/list" class="btn btn-3d btn-default btn-sm btn-block"><i class="fa fa-arrow-left"></i> {Lang::T('Back')}</a>
    </div>
    <div class="col-xs-6 col-md-3">
        <a href="{$_url}customers/sync/{$d['id']}&token={$csrf_token}" onclick="return ask(this, 'This will sync Customer to Mikrotik?')"
            class="btn btn-3d btn-3d-info btn-sm btn-block"><i class="fa fa-refresh"></i> {Lang::T('Sync')}</a>
    </div>
    <div class="col-xs-6 col-md-3">
        <a href="{$_url}message/send/{$d['id']}&token={$csrf_token}" class="btn btn-3d btn-3d-success btn-sm btn-block">
            <i class="fa fa-envelope"></i> {Lang::T('Send Message')}
        </a>
    </div>
    <div class="col-xs-6 col-md-3">
        <a href="{$_url}customers/login/{$d['id']}&token={$csrf_token}" target="_blank" class="btn btn-3d btn-3d-primary btn-sm btn-block">
            <i class="fa fa-sign-in"></i> {Lang::T('Login as Customer')}
        </a>
    </div>
</div>

{if $d['coordinates']}
    {literal}
        <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
        <script>
            function setupMap(lat, lon) {
                var map = L.map('map').setView([lat, lon], 17);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/light_all/{z}/{x}/{y}.png', {
                attribution:
                    '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    subdomains: 'abcd',
                    maxZoom: 20
            }).addTo(map);
            var marker = L.marker([lat, lon]).addTo(map);
            }
            window.onload = function() {
                {/literal}setupMap({$d['coordinates']});{literal}
            }
        </script>
    {/literal}
{/if}
{literal}
<script>
function confirmDelete(url) {
    if (confirm('{/literal}{Lang::T('Delete')}?{literal}')) {
        window.location.href = url;
    }
}

function extendCustomerPlan(customerId) {
    // Get customer's active packages to extend
    var packages = document.querySelectorAll('[data-package-id]');
    if (packages.length === 0) {
        alert('No active packages found for this customer');
        return;
    }
    
    // If only one package, extend it directly
    if (packages.length === 1) {
        var packageId = packages[0].getAttribute('data-package-id');
        extendPackage(packageId);
        return;
    }
    
    // If multiple packages, let user choose or extend the first active one
    var activePackages = [];
    for (var i = 0; i < packages.length; i++) {
        var packageBox = packages[i];
        if (packageBox.classList.contains('box-success')) {
            activePackages.push(packageBox.getAttribute('data-package-id'));
        }
    }
    
    if (activePackages.length > 0) {
        // Extend the first active package
        extendPackage(activePackages[0]);
    } else {
        // No active packages, extend the first one
        extendPackage(packages[0].getAttribute('data-package-id'));
    }
}

function extendPackage(packageId) {
    var days = prompt("Extend for how many days?", "3");
    if (days) {
        if (confirm("Extend for " + days + " days?")) {
            window.location.href = "{/literal}{$_url}plan/extend/{literal}" + packageId + "/" + days + "&stoken={/literal}{App::getToken()}{literal}";
        }
    }
}

// Format bytes into human readable format
function formatBytes(bytes, precision = 2) {
    const units = ['B', 'KB', 'MB', 'GB', 'TB'];
    let i = 0;
    
    for (i = 0; bytes > 1024 && i < units.length - 1; i++) {
        bytes /= 1024;
    }
    
    return bytes.toFixed(precision) + ' ' + units[i];
}

// Update data usage displays
function updateDataUsage() {
    // Update individual data usage
    document.querySelectorAll('.data-usage').forEach(function(element) {
        const bytes = parseInt(element.getAttribute('data-bytes'));
        if (!isNaN(bytes) && bytes > 0) {
            element.textContent = formatBytes(bytes);
        } else {
            element.textContent = '0 B';
        }
    });
    
    // Update total usage
    document.querySelectorAll('.total-usage').forEach(function(element) {
        const bytesIn = parseInt(element.getAttribute('data-in'));
        const bytesOut = parseInt(element.getAttribute('data-out'));
        if (!isNaN(bytesIn) && !isNaN(bytesOut)) {
            const total = bytesIn + bytesOut;
            element.textContent = formatBytes(total);
        } else {
            element.textContent = '0 B';
        }
    });
}

// Initialize data usage when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateDataUsage();
});

// Auto-refresh data usage every 30 seconds
setInterval(function() {
    // You could add AJAX call here to refresh device data
    // For now, just update the formatting
    updateDataUsage();
}, 30000);
</script>
{/literal}

{* ── Live Bandwidth Graph ──────────────────────────────────────────────── *}
<div class="row" style="margin-top:8px;">
    <div class="col-sm-12">
        <div class="box box-primary" id="live-graph-box">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-line-chart"></i> Live Bandwidth</h3>
                <div class="box-tools pull-right" style="display:flex;align-items:center;gap:8px;">
                    <span id="live-session-type" class="label label-default" style="font-size:11px;">Detecting&hellip;</span>
                    <span id="live-ip" class="text-muted" style="font-size:12px;"></span>
                    <span id="live-uptime" class="text-muted" style="font-size:12px;"></span>
                    <button id="live-toggle" class="btn btn-xs btn-default" title="Pause/Resume">
                        <i class="fa fa-pause" id="live-toggle-icon"></i>
                    </button>
                </div>
            </div>
            <div class="box-body" style="padding-bottom:6px;">
                <div class="row" style="margin-bottom:10px;">
                    <div class="col-xs-4">
                        <div class="sr-metric sr-metric-dl">
                            <div class="sr-metric-label">Download</div>
                            <div id="live-dl-speed" class="sr-metric-value">0 bps</div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="sr-metric sr-metric-ul">
                            <div class="sr-metric-label">Upload</div>
                            <div id="live-ul-speed" class="sr-metric-value">0 bps</div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="sr-metric sr-metric-total">
                            <div class="sr-metric-label">Session Total DL</div>
                            <div id="live-total-dl" class="sr-metric-value">0 B</div>
                        </div>
                    </div>
                </div>
                <div style="position:relative;height:180px;">
                    <canvas id="liveChart"></canvas>
                </div>
            </div>
            <div class="box-footer" style="padding:6px 12px;background:#f8f9fa;">
                <small class="text-muted"><i class="fa fa-refresh"></i> Updates every 3 seconds &nbsp;|&nbsp; <span id="live-status-text">Starting&hellip;</span></small>
            </div>
        </div>
    </div>
</div>
{* ── End Live Bandwidth Graph ────────────────────────────────────────────── *}

{* ── Monthly Data Usage ─────────────────────────────────────────────────── *}
<style>
.usage-card {
    border-radius: 8px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}
.usage-card .usage-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.usage-card .usage-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
}
.usage-card .usage-value {
    font-size: 18px;
    font-weight: 700;
    line-height: 1;
}
.usage-card-dl  { background:#f0faf2; border-left: 3px solid #27ae60; }
.usage-card-ul  { background:#f0f5ff; border-left: 3px solid #2980b9; }
.usage-card-tot { background:#fff8f0; border-left: 3px solid #e67e22; }
.usage-card-dl  .usage-icon { background:#27ae60; color:#fff; }
.usage-card-ul  .usage-icon { background:#2980b9; color:#fff; }
.usage-card-tot .usage-icon { background:#e67e22; color:#fff; }
.usage-card-dl  .usage-label { color:#27ae60; }
.usage-card-ul  .usage-label { color:#2980b9; }
.usage-card-tot .usage-label { color:#e67e22; }
.usage-card-dl  .usage-value { color:#1e8449; }
.usage-card-ul  .usage-value { color:#1f618d; }
.usage-card-tot .usage-value { color:#ca6f1e; }
.usage-history-table thead th {
    background: #f8f9fa;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #555;
    font-weight: 600;
    border-bottom: 2px solid #e9ecef;
    padding: 8px 12px;
    white-space: nowrap;
}
.usage-history-table tbody td {
    font-size: 13px;
    padding: 8px 12px;
    vertical-align: middle;
}
.usage-history-table tbody tr.current-month {
    background: #fffbea !important;
}
.usage-badge-current {
    display: inline-block;
    background: #f39c12;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 10px;
    letter-spacing: 0.3px;
    vertical-align: middle;
    margin-right: 4px;
}
.usage-section-header {
    background: #2c3e50;
    border-radius: 8px 8px 0 0;
    padding: 11px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.usage-section-header .title {
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    margin: 0;
}
.usage-section-header .subtitle {
    color: #aab7c4;
    font-size: 11px;
}
</style>

<div class="row" style="margin-top:8px;">
    <div class="col-sm-12">
        <div class="box box-default" style="border-radius:8px; overflow:hidden; border:none; box-shadow:0 1px 4px rgba(0,0,0,0.1);">
            <div class="usage-section-header">
                <span class="title"><i class="fa fa-bar-chart" style="margin-right:6px;"></i>Monthly Data Usage</span>
                <span class="subtitle"><i class="fa fa-refresh" style="margin-right:4px;"></i>Resets on the 1st of every month</span>
            </div>
            <div class="box-body" style="padding:14px 14px 6px;">

                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <div class="usage-card usage-card-dl">
                            <div class="usage-icon"><i class="fa fa-download"></i></div>
                            <div>
                                <div class="usage-label">Downloaded</div>
                                <div class="usage-value">
                                    {if $monthly_usage_current}{$monthly_usage_current['download_fmt']}{else}0 B{/if}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="usage-card usage-card-ul">
                            <div class="usage-icon"><i class="fa fa-upload"></i></div>
                            <div>
                                <div class="usage-label">Uploaded</div>
                                <div class="usage-value">
                                    {if $monthly_usage_current}{$monthly_usage_current['upload_fmt']}{else}0 B{/if}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="usage-card usage-card-tot">
                            <div class="usage-icon"><i class="fa fa-exchange"></i></div>
                            <div>
                                <div class="usage-label">Total This Month</div>
                                <div class="usage-value">
                                    {if $monthly_usage_current}{$monthly_usage_current['total_fmt']}{else}0 B{/if}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {if $monthly_usage_history|@count > 0}
                    <div class="table-responsive" style="margin-top:6px;">
                        <table class="table table-hover usage-history-table" style="margin-bottom:0;">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th><i class="fa fa-download" style="color:#27ae60;"></i> Downloaded</th>
                                    <th><i class="fa fa-upload" style="color:#2980b9;"></i> Uploaded</th>
                                    <th><i class="fa fa-exchange" style="color:#e67e22;"></i> Total</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach $monthly_usage_history as $mu}
                                    <tr {if $mu['is_current']}class="current-month"{/if}>
                                        <td>
                                            {if $mu['is_current']}<span class="usage-badge-current">NOW</span>{/if}
                                            {$mu['month_label']}
                                        </td>
                                        <td style="color:#1e8449; font-weight:600;">{$mu['download_fmt']}</td>
                                        <td style="color:#1f618d; font-weight:600;">{$mu['upload_fmt']}</td>
                                        <td style="color:#ca6f1e; font-weight:600;">{$mu['total_fmt']}</td>
                                        <td><small class="text-muted">{$mu['last_updated']}</small></td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                {else}
                    <div class="text-center" style="padding:24px 0 16px; color:#bbb;">
                        <i class="fa fa-bar-chart" style="font-size:28px; opacity:0.35;"></i>
                        <p style="margin:8px 0 0; font-size:13px; color:#999;">No data usage recorded yet.
                            <br><small>Updated automatically on each cron run.</small>
                        </p>
                    </div>
                {/if}

            </div>
        </div>
    </div>
</div>
{* ── End Monthly Data Usage ──────────────────────────────────────────────── *}

{* ── Live Graph + MT Logs JS ─────────────────────────────────────────────── *}
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
{literal}
<script>
(function () {
    'use strict';

    // ── helpers ──────────────────────────────────────────────────────────────
    function fmtSpeed(bytesPerSec) {
        var b = bytesPerSec * 8; // convert to bits/s
        if (b >= 1e9)  return (b / 1e9).toFixed(2) + ' Gbps';
        if (b >= 1e6)  return (b / 1e6).toFixed(2) + ' Mbps';
        if (b >= 1e3)  return (b / 1e3).toFixed(2) + ' Kbps';
        return b.toFixed(0) + ' bps';
    }
    function fmtBytes(bytes) {
        if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
        if (bytes >= 1048576)    return (bytes / 1048576).toFixed(2) + ' MB';
        if (bytes >= 1024)       return (bytes / 1024).toFixed(2) + ' KB';
        return bytes + ' B';
    }
    function nowLabel() {
        var d = new Date();
        return d.getHours().toString().padStart(2,'0') + ':' +
               d.getMinutes().toString().padStart(2,'0') + ':' +
               d.getSeconds().toString().padStart(2,'0');
    }

    // ── Chart setup ──────────────────────────────────────────────────────────
    var MAX_PTS = 60;
    var labels  = Array(MAX_PTS).fill('');
    var dlData  = Array(MAX_PTS).fill(0);
    var ulData  = Array(MAX_PTS).fill(0);

    var ctx = document.getElementById('liveChart');
    if (!ctx) return;

    var liveChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Download',
                    data: dlData,
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39,174,96,0.08)',
                    borderWidth: 2,
                    pointRadius: 0,
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Upload',
                    data: ulData,
                    borderColor: '#2980b9',
                    backgroundColor: 'rgba(41,128,185,0.08)',
                    borderWidth: 2,
                    pointRadius: 0,
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 0 },
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } } },
            scales: {
                x: { ticks: { font: { size: 10 }, maxTicksLimit: 10 }, grid: { display: false } },
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: { size: 10 },
                        callback: function(v) {
                            if (v === 0) return '0';
                            return fmtSpeed(v);
                        }
                    },
                    title: { display: true, text: 'Speed', font: { size: 10 } }
                }
            }
        }
    });

    // ── Live polling ─────────────────────────────────────────────────────────
    var prevBytes    = null;
    var paused       = false;
    var pollInterval = null;
{/literal}
    var customerId   = '{$d['id']}';
    var apiBase      = '{$_url}customers/live_stats/';
{literal}

    document.getElementById('live-toggle').addEventListener('click', function () {
        paused = !paused;
        var icon = document.getElementById('live-toggle-icon');
        icon.className = paused ? 'fa fa-play' : 'fa fa-pause';
        document.getElementById('live-status-text').textContent = paused ? 'Paused' : 'Running…';
    });

    function pollLiveStats() {
        if (paused) return;
        fetch(apiBase + customerId)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var statusEl = document.getElementById('live-status-text');
                if (!data.success) {
                    statusEl.textContent = data.error || 'Error';
                    return;
                }

                // Session type badge
                var badge = document.getElementById('live-session-type');
                if (data.type === 'offline') {
                    badge.className = 'label label-danger';
                    badge.textContent = 'Offline';
                    statusEl.textContent = 'No active session';
                } else {
                    badge.className = 'label label-success';
                    badge.textContent = data.type;
                    statusEl.textContent = 'Live';
                }

                // IP + uptime
                document.getElementById('live-ip').textContent     = data.ip     ? ('IP: ' + data.ip)     : '';
                document.getElementById('live-uptime').textContent = data.uptime ? ('Up: ' + data.uptime) : '';

                // Session total download
                document.getElementById('live-total-dl').textContent = fmtBytes(data.download);

                // Compute speed from delta
                var dlSpeed = 0, ulSpeed = 0;
                if (prevBytes && data.type !== 'offline') {
                    var dt = data.timestamp - prevBytes.ts;
                    if (dt > 0) {
                        dlSpeed = Math.max(0, (data.download - prevBytes.dl) / dt);
                        ulSpeed = Math.max(0, (data.upload   - prevBytes.ul) / dt);
                    }
                }
                prevBytes = { dl: data.download, ul: data.upload, ts: data.timestamp };

                // Update speed cards
                document.getElementById('live-dl-speed').textContent = fmtSpeed(dlSpeed);
                document.getElementById('live-ul-speed').textContent = fmtSpeed(ulSpeed);

                // Push to chart
                labels.push(nowLabel());   labels.shift();
                dlData.push(dlSpeed);      dlData.shift();
                ulData.push(ulSpeed);      ulData.shift();
                liveChart.update('none');
            })
            .catch(function() {
                document.getElementById('live-status-text').textContent = 'Connection error';
            });
    }

    // Start polling immediately then every 3 s
    pollLiveStats();
    pollInterval = setInterval(pollLiveStats, 3000);

    // ── MikroTik Logs (mklogs tab) ───────────────────────────────────────────
    var mklogsTbody = document.getElementById('mklogs-tbody');
    if (mklogsTbody) {
{/literal}
        var logsApiUrl = '{$_url}customers/mikrotik_logs/{$d['id']}';
{literal}
        // topic → { bg, border, icon, text }
        var TOPIC_STYLE = {
            'info':     { bg:'#e8f4fd', border:'#3498db', icon:'fa-info-circle',   text:'#1a6a9a' },
            'warning':  { bg:'#fff8e1', border:'#f39c12', icon:'fa-exclamation-triangle', text:'#9a6800' },
            'error':    { bg:'#fdecea', border:'#e74c3c', icon:'fa-times-circle',   text:'#a93226' },
            'critical': { bg:'#fdecea', border:'#c0392b', icon:'fa-bomb',           text:'#7b241c' },
            'debug':    { bg:'#f0f0f0', border:'#95a5a6', icon:'fa-bug',            text:'#555' },
            'firewall': { bg:'#fef9e7', border:'#e67e22', icon:'fa-shield',         text:'#9a4f00' },
            'ppp':      { bg:'#eaf7fb', border:'#16a085', icon:'fa-plug',           text:'#0e6655' },
            'hotspot':  { bg:'#eaf5ea', border:'#27ae60', icon:'fa-wifi',           text:'#1a7a40' },
            'dhcp':     { bg:'#f4ecf7', border:'#8e44ad', icon:'fa-sitemap',        text:'#6c3483' },
            'system':   { bg:'#eaf0fb', border:'#2980b9', icon:'fa-cogs',           text:'#1a5276' }
        };

        function getTopicStyle(topics) {
            var t = (topics || '').toLowerCase();
            for (var key in TOPIC_STYLE) {
                if (t.indexOf(key) !== -1) return TOPIC_STYLE[key];
            }
            return { bg:'#f8f9fa', border:'#bdc3c7', icon:'fa-list', text:'#555' };
        }

        fetch(logsApiUrl)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success || !data.logs || data.logs.length === 0) {
                    mklogsTbody.innerHTML =
                        '<tr><td colspan="3" class="text-center" style="padding:40px 20px;">' +
                        '<i class="fa fa-list-alt" style="font-size:36px;color:#ccc;display:block;margin-bottom:10px;"></i>' +
                        '<span style="color:#999;font-size:13px;">' + escHtml(data.error || 'No log entries found for this user.') + '</span>' +
                        '</td></tr>';
                    return;
                }
                var rows = '';
                data.logs.forEach(function(log, idx) {
                    var s = getTopicStyle(log.topics);
                    rows +=
                        '<tr style="background:' + s.bg + ';border-left:4px solid ' + s.border + ';">' +
                        '<td style="white-space:nowrap;font-size:11px;color:#666;vertical-align:middle;padding:8px 10px;">' +
                            '<i class="fa fa-clock-o" style="margin-right:3px;"></i>' + escHtml(log.time) +
                        '</td>' +
                        '<td style="vertical-align:middle;padding:8px 10px;">' +
                            '<span style="display:inline-flex;align-items:center;gap:4px;background:' + s.border + ';' +
                            'color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;letter-spacing:.3px;">' +
                            '<i class="fa ' + s.icon + '"></i>' +
                            escHtml(log.topics) +
                            '</span>' +
                        '</td>' +
                        '<td style="font-size:12px;color:' + s.text + ';word-break:break-all;vertical-align:middle;padding:8px 10px;font-weight:500;">' +
                            escHtml(log.message) +
                        '</td>' +
                        '</tr>';
                });
                mklogsTbody.innerHTML = rows;
            })
            .catch(function() {
                mklogsTbody.innerHTML =
                    '<tr><td colspan="3" class="text-center" style="padding:20px;color:#e74c3c;">' +
                    '<i class="fa fa-exclamation-circle"></i> Failed to load MikroTik logs.</td></tr>';
            });
    }

    function escHtml(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

})();
</script>
{/literal}
{* ── End Live Graph + MT Logs JS ─────────────────────────────────────────── *}

</div>{* /.sr-cust *}

{include file="sections/footer.tpl"}