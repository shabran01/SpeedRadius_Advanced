<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{$_title} | {$_c['CompanyName']}</title>
  <link rel="shortcut icon" type="image/x-icon" href="ui/ui/images/logo.png">
  <link rel="stylesheet" href="ui/ui/fonts/font-awesome/css/font-awesome.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root {
      --primary: #4f46e5;
      --primary-dark: #4338ca;
      --primary-light: #e0e7ff;
      --accent: #06b6d4;
      --accent-dark: #0891b2;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --bg: #f1f5f9;
      --card: #ffffff;
      --text: #1e293b;
      --text-muted: #64748b;
      --border: #e2e8f0;
      --shadow: 0 1px 3px 0 rgba(0,0,0,0.06), 0 1px 2px -1px rgba(0,0,0,0.06);
      --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
      --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.04);
      --radius: 12px;
      --radius-sm: 8px;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
      font-size: 14px;
      line-height: 1.6;
      color: var(--text);
      background: var(--bg);
      min-height: 100vh;
      -webkit-font-smoothing: antialiased;
    }

    /* ===== TOP NAVBAR ===== */
    .topbar {
      background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4f46e5 100%);
      color: #fff;
      padding: 14px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 4px 20px rgba(79, 70, 229, 0.25);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .topbar .brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .topbar .brand-icon {
      width: 40px;
      height: 40px;
      background: rgba(255,255,255,0.15);
      backdrop-filter: blur(10px);
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      border: 1px solid rgba(255,255,255,0.2);
    }
    .topbar .brand h1 {
      font-size: 18px;
      font-weight: 700;
      letter-spacing: -0.3px;
    }
    .topbar .brand span {
      font-size: 11px;
      opacity: 0.8;
      display: block;
      font-weight: 400;
      letter-spacing: 0.5px;
    }
    .topbar .stats-pill {
      background: rgba(255,255,255,0.12);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.15);
      padding: 8px 18px;
      border-radius: 50px;
      font-size: 13px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* ===== MAIN LAYOUT ===== */
    .main-container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 24px;
    }

    /* ===== CONTROLS CARD ===== */
    .controls-card {
      background: var(--card);
      border-radius: var(--radius);
      box-shadow: var(--shadow-lg);
      padding: 24px;
      margin-bottom: 28px;
      border: 1px solid var(--border);
    }
    .controls-card .card-header {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
      padding-bottom: 16px;
      border-bottom: 2px solid var(--border);
    }
    .controls-card .card-header .icon-circle {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: linear-gradient(135deg, #dbeafe, #bfdbfe);
      color: #2563eb;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
    }
    .controls-card .card-header h2 {
      font-size: 17px;
      font-weight: 700;
      color: var(--text);
    }
    .controls-card .card-header .badge {
      font-size: 11px;
      background: #dbeafe;
      color: #1d4ed8;
      padding: 3px 10px;
      border-radius: 50px;
      font-weight: 600;
      margin-left: auto;
    }

    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 14px;
      margin-bottom: 16px;
    }

    /* ===== PAPER SIZE SELECTOR ===== */
    .paper-selector {
      display: flex;
      gap: 10px;
      margin-bottom: 16px;
      flex-wrap: wrap;
    }
    .paper-option {
      flex: 1;
      min-width: 90px;
    }
    .paper-option input { display: none; }
    .paper-option label {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
      padding: 12px 10px;
      border: 2px solid var(--border);
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: all 0.2s ease;
      background: #f8fafc;
      text-align: center;
      font-family: inherit;
      user-select: none;
    }
    .paper-option label:hover {
      border-color: #a5b4fc;
      background: #eef2ff;
    }
    .paper-option input:checked + label {
      border-color: var(--primary);
      background: var(--primary-light);
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
    }
    .paper-option label .paper-icon {
      font-size: 22px;
      line-height: 1;
    }
    .paper-option label .paper-name {
      font-size: 13px;
      font-weight: 700;
      color: var(--text);
    }
    .paper-option label .paper-size {
      font-size: 10px;
      color: var(--text-muted);
      font-weight: 500;
    }
    .paper-option input:checked + label .paper-name {
      color: var(--primary);
    }
    .paper-option input:checked + label .paper-icon {
      color: var(--primary);
    }
    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .form-group label {
      font-size: 12px;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .form-group input,
    .form-group select {
      padding: 10px 14px;
      font-size: 14px;
      border: 2px solid var(--border);
      border-radius: var(--radius-sm);
      background: #f8fafc;
      color: var(--text);
      transition: all 0.2s ease;
      font-family: inherit;
      outline: none;
      width: 100%;
    }
    .form-group input:focus,
    .form-group select:focus {
      border-color: var(--primary);
      background: #fff;
      box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.08);
    }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 10px 22px;
      font-size: 14px;
      font-weight: 600;
      border: none;
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: all 0.2s ease;
      font-family: inherit;
      text-decoration: none;
    }
    .btn-primary {
      background: linear-gradient(135deg, #4f46e5, #6366f1);
      color: #fff;
      box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #4338ca, #4f46e5);
      transform: translateY(-1px);
      box-shadow: 0 4px 14px rgba(79, 70, 229, 0.4);
    }
    .btn-outline {
      background: #fff;
      color: var(--primary);
      border: 2px solid var(--primary);
    }
    .btn-outline:hover {
      background: var(--primary-light);
    }
    .btn-success {
      background: linear-gradient(135deg, #059669, #10b981);
      color: #fff;
      box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }
    .btn-success:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);
    }
    .btn-lg { padding: 12px 28px; font-size: 15px; border-radius: 10px; }

    .action-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
      padding-top: 8px;
    }
    .info-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 16px;
      background: #f1f5f9;
      border-radius: 50px;
      font-size: 13px;
      font-weight: 600;
      color: var(--text);
    }
    .info-pill .count {
      background: var(--primary);
      color: #fff;
      padding: 2px 10px;
      border-radius: 50px;
      font-size: 12px;
      font-weight: 700;
    }

    /* ===== VOUCHER GRID ===== */
    .section-title {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 18px;
    }
    .section-title .dot {
      width: 10px;
      height: 10px;
      background: var(--primary);
      border-radius: 50%;
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.5; transform: scale(1.3); }
    }
    .section-title h3 {
      font-size: 15px;
      font-weight: 700;
      color: var(--text);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .voucher-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 16px;
    }

    .voucher-card {
      background: var(--card);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      border: 1px solid var(--border);
      overflow: hidden;
      transition: all 0.25s ease;
      position: relative;
      page-break-inside: avoid;
      break-inside: avoid;
    }
    .voucher-card:hover {
      transform: translateY(-3px);
      box-shadow: var(--shadow-lg);
      border-color: #c7d2fe;
    }
    .voucher-card .card-stripe {
      height: 4px;
      background: linear-gradient(90deg, var(--primary), var(--accent), #8b5cf6);
    }
    .voucher-card .card-body {
      padding: 18px 16px 14px;
    }
    .voucher-card .counter-badge {
      position: absolute;
      top: 10px;
      right: 12px;
      font-size: 10px;
      font-weight: 700;
      color: var(--text-muted);
      background: #f1f5f9;
      padding: 2px 8px;
      border-radius: 50px;
      letter-spacing: 0.3px;
    }
    .voucher-card .v-code {
      font-size: 22px;
      font-weight: 800;
      letter-spacing: 2px;
      text-align: center;
      color: var(--primary-dark);
      font-family: 'SF Mono', 'Fira Code', 'Consolas', monospace;
      background: linear-gradient(180deg, #eef2ff 0%, #e0e7ff 100%);
      padding: 10px 8px;
      border-radius: var(--radius-sm);
      margin-bottom: 10px;
      word-break: break-all;
      border: 1px dashed #c7d2fe;
    }
    .voucher-card .v-company {
      font-size: 10px;
      font-weight: 700;
      text-align: center;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--text-muted);
      margin-bottom: 6px;
    }
    .voucher-card .v-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 8px;
    }
    .voucher-card .v-plan {
      font-size: 13px;
      font-weight: 600;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 4px;
    }
    .voucher-card .v-plan::before {
      content: '';
      display: inline-block;
      width: 6px;
      height: 6px;
      background: var(--accent);
      border-radius: 50%;
    }
    .voucher-card .v-price {
      font-size: 15px;
      font-weight: 800;
      color: #059669;
      white-space: nowrap;
    }

    /* ===== VOUCHER COLOR VARIANTS ===== */
    .voucher-card.color-0 .card-stripe { background: linear-gradient(90deg, #4f46e5, #818cf8); }
    .voucher-card.color-0 .v-code { background: linear-gradient(180deg, #eef2ff, #e0e7ff); color: #4338ca; border-color: #a5b4fc; }
    .voucher-card.color-0 .v-price { color: #4f46e5; }
    .voucher-card.color-0 .v-plan::before { background: #4f46e5; }

    .voucher-card.color-1 .card-stripe { background: linear-gradient(90deg, #059669, #34d399); }
    .voucher-card.color-1 .v-code { background: linear-gradient(180deg, #ecfdf5, #d1fae5); color: #065f46; border-color: #6ee7b7; }
    .voucher-card.color-1 .v-price { color: #059669; }
    .voucher-card.color-1 .v-plan::before { background: #059669; }

    .voucher-card.color-2 .card-stripe { background: linear-gradient(90deg, #d97706, #fbbf24); }
    .voucher-card.color-2 .v-code { background: linear-gradient(180deg, #fffbeb, #fef3c7); color: #92400e; border-color: #fcd34d; }
    .voucher-card.color-2 .v-price { color: #d97706; }
    .voucher-card.color-2 .v-plan::before { background: #d97706; }

    .voucher-card.color-3 .card-stripe { background: linear-gradient(90deg, #dc2626, #f87171); }
    .voucher-card.color-3 .v-code { background: linear-gradient(180deg, #fef2f2, #fee2e2); color: #991b1b; border-color: #fca5a5; }
    .voucher-card.color-3 .v-price { color: #dc2626; }
    .voucher-card.color-3 .v-plan::before { background: #dc2626; }

    .voucher-card.color-4 .card-stripe { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
    .voucher-card.color-4 .v-code { background: linear-gradient(180deg, #f5f3ff, #ede9fe); color: #5b21b6; border-color: #c4b5fd; }
    .voucher-card.color-4 .v-price { color: #7c3aed; }
    .voucher-card.color-4 .v-plan::before { background: #7c3aed; }

    .voucher-card.color-5 .card-stripe { background: linear-gradient(90deg, #0891b2, #22d3ee); }
    .voucher-card.color-5 .v-code { background: linear-gradient(180deg, #ecfeff, #cffafe); color: #155e75; border-color: #67e8f9; }
    .voucher-card.color-5 .v-price { color: #0891b2; }
    .voucher-card.color-5 .v-plan::before { background: #0891b2; }

    .voucher-card.color-6 .card-stripe { background: linear-gradient(90deg, #e11d48, #fb7185); }
    .voucher-card.color-6 .v-code { background: linear-gradient(180deg, #fff1f2, #ffe4e6); color: #9f1239; border-color: #fda4af; }
    .voucher-card.color-6 .v-price { color: #e11d48; }
    .voucher-card.color-6 .v-plan::before { background: #e11d48; }

    .voucher-card.color-7 .card-stripe { background: linear-gradient(90deg, #ca8a04, #facc15); }
    .voucher-card.color-7 .v-code { background: linear-gradient(180deg, #fefce8, #fef08a); color: #854d0e; border-color: #fde047; }
    .voucher-card.color-7 .v-price { color: #ca8a04; }
    .voucher-card.color-7 .v-plan::before { background: #ca8a04; }

    /* ===== EMPTY STATE ===== */
    .empty-state {
      text-align: center;
      padding: 60px 24px;
      color: var(--text-muted);
    }
    .empty-state .empty-icon {
      font-size: 48px;
      margin-bottom: 16px;
      opacity: 0.4;
    }
    .empty-state p {
      font-size: 15px;
      font-weight: 500;
    }

    /* ===== FOOTER ===== */
    .main-footer {
      text-align: center;
      padding: 20px 24px 28px;
      color: var(--text-muted);
      font-size: 12px;
      border-top: 1px solid var(--border);
      margin-top: 32px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
      .topbar {
        padding: 12px 16px;
        flex-direction: column;
        gap: 8px;
        text-align: center;
      }
      .topbar .brand { flex-direction: column; gap: 4px; }
      .topbar .brand h1 { font-size: 16px; }
      .main-container { padding: 12px; }
      .controls-card { padding: 16px; }
      .form-grid { grid-template-columns: 1fr; }
      .voucher-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px; }
      .action-bar { flex-direction: column; align-items: stretch; }
      .action-bar .btn { width: 100%; justify-content: center; }
      .voucher-card .v-code { font-size: 18px; letter-spacing: 1.5px; padding: 8px 6px; }
    }

    @media (max-width: 480px) {
      .voucher-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
      .voucher-card .card-body { padding: 12px 10px 10px; }
      .voucher-card .v-code { font-size: 15px; letter-spacing: 1px; }
      .voucher-card .v-plan { font-size: 11px; }
      .voucher-card .v-price { font-size: 13px; }
    }

    /* ===== PAPER SIZE: THERMAL 80MM ===== */
    body.paper-80mm .voucher-grid {
      display: block;
      max-width: 80mm;
      margin: 0 auto;
    }
    body.paper-80mm .voucher-card {
      border-radius: 0;
      box-shadow: none;
      border: none;
      border-bottom: 1px dashed #ccc;
      background: #fff;
      page-break-inside: avoid;
      break-inside: avoid;
    }
    body.paper-80mm .voucher-card:last-child { border-bottom: none; }
    body.paper-80mm .voucher-card .card-stripe { display: none; }
    body.paper-80mm .voucher-card .card-body { padding: 6px 4px 4px; }
    body.paper-80mm .voucher-card .counter-badge {
      position: static;
      display: block;
      text-align: right;
      font-size: 7px;
      background: none;
      padding: 0;
      margin-bottom: 2px;
      color: #999;
    }
    body.paper-80mm .voucher-card .v-code {
      font-size: 20px;
      font-weight: 900;
      letter-spacing: 3px;
      background: #f8f8f8;
      border: 1px dashed #bbb;
      color: #111;
      border-radius: 2px;
      padding: 6px;
      font-family: 'Courier New', Courier, monospace;
    }
    body.paper-80mm .voucher-card .v-plan {
      font-size: 11px;
      color: #333;
    }
    body.paper-80mm .voucher-card .v-plan::before { display: none; }
    body.paper-80mm .voucher-card .v-company {
      font-size: 9px;
      font-weight: 700;
      text-align: center;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #555;
      margin-bottom: 4px;
    }
    body.paper-80mm .voucher-card .v-price {
      font-size: 13px;
      color: #111;
      font-weight: 900;
    }

    /* ===== PAPER SIZE: THERMAL 58MM ===== */
    body.paper-58mm .voucher-grid {
      display: block;
      max-width: 58mm;
      margin: 0 auto;
    }
    body.paper-58mm .voucher-card {
      border-radius: 0;
      box-shadow: none;
      border: none;
      border-bottom: 1px dashed #ccc;
      background: #fff;
      page-break-inside: avoid;
      break-inside: avoid;
    }
    body.paper-58mm .voucher-card:last-child { border-bottom: none; }
    body.paper-58mm .voucher-card .card-stripe { display: none; }
    body.paper-58mm .voucher-card .card-body { padding: 4px 2px 3px; }
    body.paper-58mm .voucher-card .counter-badge {
      position: static;
      display: block;
      text-align: right;
      font-size: 6px;
      background: none;
      padding: 0;
      margin-bottom: 1px;
      color: #999;
    }
    body.paper-58mm .voucher-card .v-code {
      font-size: 16px;
      font-weight: 900;
      letter-spacing: 2px;
      background: #f8f8f8;
      border: 1px dashed #bbb;
      color: #111;
      border-radius: 2px;
      padding: 4px;
      font-family: 'Courier New', Courier, monospace;
    }
    body.paper-58mm .voucher-card .v-plan {
      font-size: 9px;
      color: #333;
    }
    body.paper-58mm .voucher-card .v-plan::before { display: none; }
    body.paper-58mm .voucher-card .v-company {
      font-size: 8px;
      font-weight: 700;
      text-align: center;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #555;
      margin-bottom: 3px;
    }
    body.paper-58mm .voucher-card .v-price {
      font-size: 11px;
      color: #111;
      font-weight: 900;
    }

    /* ===== PAPER SIZE: A4 (default, same as base) ===== */
    body.paper-a4 .voucher-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 16px;
    }

    /* ===== PRINT STYLES ===== */
    @media print {
      body {
        background: #fff;
        font-size: 11px;
      }
      .no-print,
      .topbar,
      .controls-card,
      .main-footer { display: none !important; }

      .main-container {
        max-width: 100%;
        padding: 0;
        margin: 0;
      }

      .voucher-card {
        box-shadow: none;
        break-inside: avoid;
        page-break-inside: avoid;
      }
      .voucher-card .v-price { font-weight: 700; }
      .section-title .dot { display: none; }

      /* A4 print */
      body.paper-a4 .voucher-grid { display: block; }
      body.paper-a4 .voucher-card {
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-bottom: 6px;
      }
      body.paper-a4 .voucher-card .card-stripe { height: 2px; background: #333; }
      body.paper-a4 .voucher-card .v-code {
        font-size: 16px;
        letter-spacing: 1px;
        background: #f5f5f5;
        border: 1px dashed #999;
        color: #000;
      }
      body.paper-a4 .voucher-card .counter-badge { color: #999; }
      body.paper-a4 .voucher-card .v-plan::before { display: none; }
      body.paper-a4 .section-title h3 { font-size: 13px; }

      /* 80mm print */
      body.paper-80mm .voucher-grid {
        display: block;
        max-width: 80mm;
        margin: 0;
      }
      body.paper-80mm .voucher-card .v-code { font-size: 18px; letter-spacing: 2px; }
      body.paper-80mm .voucher-card .v-plan { font-size: 10px; }
      body.paper-80mm .voucher-card .v-price { font-size: 12px; }

      /* 58mm print */
      body.paper-58mm .voucher-grid {
        display: block;
        max-width: 58mm;
        margin: 0;
      }
      body.paper-58mm .voucher-card .v-code { font-size: 14px; letter-spacing: 1.5px; }
      body.paper-58mm .voucher-card .v-plan { font-size: 8px; }
      body.paper-58mm .voucher-card .v-price { font-size: 10px; }

      @page {
        size: A4;
        margin: 8mm;
      }
    }

    /* Override @page based on body class (via JS sets --page-size) */
    @media print {
      body.paper-80mm { --page-width: 80mm; }
      body.paper-58mm { --page-width: 58mm; }
    }
  </style>
</head>
<body>

  <!-- ===== TOP NAVBAR ===== -->
  <div class="topbar no-print">
    <div class="brand">
      <div class="brand-icon"><i class="fa fa-ticket"></i></div>
      <div>
        <h1>{$_c['CompanyName']}</h1>
        <span>POS Voucher Print System</span>
      </div>
    </div>
    <div class="stats-pill">
      <i class="fa fa-database"></i>
      <strong>{$vc}</strong> vouchers available
    </div>
  </div>

  <div class="main-container">

    <!-- ===== CONTROLS ===== -->
    <div class="controls-card no-print">
      <div class="card-header">
        <div class="icon-circle"><i class="fa fa-sliders"></i></div>
        <h2>{Lang::T('POS Print Vouchers')}</h2>
        <span class="badge">
          <i class="fa fa-ticket"></i> {Lang::T('Showing')} {$voucher|@count} / {$vc}
        </span>
      </div>

      <form method="post" action="{$_url}plan/print-voucher-pos/" id="controlsForm">
        <div class="form-grid">
          <div class="form-group">
            <label><i class="fa fa-hashtag"></i> {Lang::T('From ID')}</label>
            <input type="number" name="from_id" value="{$from_id}" placeholder="Start from ID" min="0">
          </div>
          <div class="form-group">
            <label><i class="fa fa-list-ol"></i> {Lang::T('Limit')}</label>
            <input type="number" name="limit" value="{$limit}" placeholder="40" min="1" max="200">
          </div>
          <div class="form-group">
            <label><i class="fa fa-cubes"></i> {Lang::T('Plan')}</label>
            <select name="planid">
              <option value="0">{Lang::T('All Plans')}</option>
              {foreach $plans as $plan}
                <option value="{$plan['id']}" {if $plan['id']==$planid}selected{/if}>{$plan['name_plan']}</option>
              {/foreach}
            </select>
          </div>
        </div>

        <label style="font-size:12px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;display:block;">
          <i class="fa fa-print"></i> {Lang::T('Paper Size')}
        </label>
        <div class="paper-selector">
          <div class="paper-option">
            <input type="radio" name="paper_size" value="80mm" id="paper-80mm" checked>
            <label for="paper-80mm">
              <span class="paper-icon">🧾</span>
              <span class="paper-name">80mm Thermal</span>
              <span class="paper-size">Receipt Roll</span>
            </label>
          </div>
          <div class="paper-option">
            <input type="radio" name="paper_size" value="58mm" id="paper-58mm">
            <label for="paper-58mm">
              <span class="paper-icon">🖨️</span>
              <span class="paper-name">58mm Thermal</span>
              <span class="paper-size">Portable Printer</span>
            </label>
          </div>
        </div>

        <div class="action-bar">
          <button type="submit" class="btn btn-primary btn-lg">
            <i class="fa fa-refresh"></i> {Lang::T('Generate')}
          </button>
          <div class="info-pill">
            <span>{Lang::T('Total')}</span>
            <span class="count">{$voucher|@count}</span>
            <span>{Lang::T('voucher(s)')}</span>
          </div>
          <button type="button" id="printBtn" class="btn btn-success btn-lg">
            <i class="fa fa-print"></i> {Lang::T('Print POS')}
          </button>
        </div>
      </form>
    </div>

    <!-- ===== VOUCHER SECTION ===== -->
    <div class="section-title">
      <span class="dot"></span>
      <h3><i class="fa fa-tags"></i> {Lang::T('Voucher Slips')}</h3>
    </div>

    {if $voucher|@count > 0}
    <div class="voucher-grid" id="voucherGrid">
      {foreach $voucher as $vs}
      <div class="voucher-card color-{($vs.counter-1)%8}">
        <div class="card-stripe"></div>
        <div class="card-body">
          <span class="counter-badge">#{$vs.counter}</span>
          <div class="v-company">{$_c['CompanyName']}</div>
          <div class="v-code">{$vs.code}</div>
          <div class="v-meta">
            <span class="v-plan">{$vs.plan}</span>
            <span class="v-price">{$vs.price}</span>
          </div>
        </div>
      </div>
      {/foreach}
    </div>
    {else}
    <div class="empty-state">
      <div class="empty-icon"><i class="fa fa-ticket"></i></div>
      <p>{Lang::T('No vouchers to display')}</p>
      <p style="font-size:12px;margin-top:6px;">{Lang::T('Use the controls above to generate vouchers for printing.')}</p>
    </div>
    {/if}

    <!-- ===== FOOTER ===== -->
    <div class="main-footer">
      <strong>{$_c['CompanyName']}</strong> &mdash; {Lang::dateTimeFormat(time())}
    </div>

  </div>

  <script>
  var paperRadios = document.querySelectorAll('input[name="paper_size"]');
  var printBtn = document.getElementById('printBtn');
  var sectionTitle = document.querySelector('.section-title h3');

  function setPaperSize(size) {
    document.body.className = document.body.className.replace(/\bpaper-\S+/g, '');
    document.body.classList.add('paper-' + size);
    localStorage.setItem('pos_paper_size', size);

    var labels = {
      '80mm': 'Voucher Slips — 80mm Thermal Roll',
      '58mm': 'Voucher Slips — 58mm Thermal Roll'
    };
    if (sectionTitle) sectionTitle.textContent = labels[size] || labels['80mm'];
  }

  // Init from localStorage
  var savedSize = localStorage.getItem('pos_paper_size') || '80mm';
  var savedRadio = document.getElementById('paper-' + savedSize);
  if (savedRadio) savedRadio.checked = true;
  setPaperSize(savedSize);

  // Listen for changes
  paperRadios.forEach(function(radio) {
    radio.addEventListener('change', function() {
      setPaperSize(this.value);
    });
  });

  // Print button
  printBtn.addEventListener('click', function() {
    var paper = localStorage.getItem('pos_paper_size') || '80mm';
    var style = document.createElement('style');
    style.id = 'thermal-print-page';
    style.textContent = '@media print { @page { size: ' + paper + ' auto; margin: 2mm; } }';
    document.head.appendChild(style);
    window.print();
    setTimeout(function() {
      var el = document.getElementById('thermal-print-page');
      if (el) el.remove();
    }, 1000);
  });
  </script>
</body>
</html>
