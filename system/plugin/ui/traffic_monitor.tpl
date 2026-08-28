{include file="sections/header.tpl"}
<!-- Tailwind CDN - preflight disabled to avoid Bootstrap conflicts -->
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config = {literal}{ corePlugins: { preflight: false } }{/literal};</script>
<style>
{literal}
    #tm-app {
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        min-height: 100vh;
        background: linear-gradient(160deg, #f8fafc 0%, #eef2ff 60%, #f5f3ff 100%);
        color: #1e293b;
    }

    /* Pulse dot */
    @keyframes tm-pulse { 0%,100%{opacity:1} 50%{opacity:.35} }
    .tm-pulse { animation: tm-pulse 1.6s cubic-bezier(.4,0,.6,1) infinite; }

    /* Smooth bar */
    .tm-bar { height:100%; border-radius:9999px; transition: width .5s cubic-bezier(.4,0,.2,1); }

    /* Router pill tabs */
    .tm-router-tab { transition: all .18s; }
    .tm-router-tab.tm-active {
        background: linear-gradient(135deg,#4f46e5,#7c3aed);
        color:#fff !important; border-color:transparent !important;
        box-shadow: 0 6px 18px rgba(79,70,229,.35);
    }

    /* Cards */
    .tm-card {
        background:#fff; border:1px solid #e2e8f0; border-radius:20px;
        box-shadow: 0 4px 16px rgba(15,23,42,.06);
    }

    /* Interface select */
    .tm-select {
        border:1.5px solid #e2e8f0; background:#fff; border-radius:12px;
        padding:9px 16px; font-size:14px; font-weight:600; color:#334155;
        outline:none; transition: border-color .2s, box-shadow .2s;
    }
    .tm-select:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.18); }

    /* Stats badge */
    .tm-stat { background:#f8fafc; border:1px solid #e2e8f0; border-radius:14px; padding:10px 16px; text-align:center; min-width:96px; }
    .tm-stat-lbl { font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; margin-bottom:2px; }
    .tm-stat-val { font-size:18px; font-weight:800; line-height:1; }

    /* Ring gauges */
    .tm-ring { transform: rotate(-90deg); }
    .tm-ring-track { fill:none; stroke:#e2e8f0; stroke-width:11; }
    .tm-ring-fill { fill:none; stroke-width:11; stroke-linecap:round; stroke-dasharray:326.7; stroke-dashoffset:326.7; transition: stroke-dashoffset .5s cubic-bezier(.4,0,.2,1); }

    /* Scrollbar */
    ::-webkit-scrollbar{width:5px;height:5px}
    ::-webkit-scrollbar-track{background:#f1f5f9}
    ::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:3px}
{/literal}
</style>

<div id="tm-app" class="p-3 sm:p-5 lg:p-7">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
        <div>
            <h1 class="text-lg sm:text-xl lg:text-2xl font-extrabold tracking-tight text-slate-800">&#x1F4CA; Live Traffic Monitor</h1>
            <p class="text-slate-400 text-xs sm:text-sm mt-0.5">Real-time bandwidth usage per interface</p>
        </div>
        <div id="tm-live-badge" class="inline-flex items-center justify-center gap-2 text-xs font-semibold rounded-full px-3 py-1.5 bg-white border border-emerald-200 text-emerald-600 w-fit">
            <span class="relative flex h-2 w-2">
                <span id="tm-ping" class="tm-pulse animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span id="tm-dot" class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span id="tm-status-text">LIVE &bull; 1s refresh</span>
        </div>
    </div>

    <!-- Router Tabs -->
    <div class="flex flex-wrap gap-2 mb-5">
        {foreach $routers as $r}
        <a href="{$_url}plugin/traffic_monitor_ui/{$r['id']}"
           class="tm-router-tab {if $r['id']==$router}tm-active{/if} px-3 py-1.5 text-sm font-semibold rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-indigo-600 hover:border-indigo-300">
            {$r['name']}
        </a>
        {/foreach}
    </div>

    <!-- Interface selector + peak stats -->
    <div class="flex flex-wrap items-end gap-3 mb-5">
        <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Interface</label>
            <select id="iface-select" class="tm-select w-full sm:w-auto">
                {foreach $interfaces as $iface}
                <option value="{$iface}">{$iface}</option>
                {/foreach}
            </select>
        </div>
        <div class="flex flex-wrap gap-2">
            <div class="tm-stat">
                <div class="tm-stat-lbl" style="color:#0284c7">Peak RX</div>
                <div class="tm-stat-val" id="peak-rx" style="color:#0369a1">&mdash;</div>
            </div>
            <div class="tm-stat">
                <div class="tm-stat-lbl" style="color:#7c3aed">Peak TX</div>
                <div class="tm-stat-val" id="peak-tx" style="color:#6d28d9">&mdash;</div>
            </div>
            <div class="tm-stat">
                <div class="tm-stat-lbl" style="color:#64748b">Samples</div>
                <div class="tm-stat-val" id="sample-count" style="color:#334155">0</div>
            </div>
        </div>
    </div>

    <!-- Speed Gauge Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">

        <!-- Download RX -->
        <div class="tm-card p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="tm-pulse animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-sky-500"></span>
                    </span>
                    <span class="text-xs font-bold uppercase tracking-widest text-sky-600">Download &darr; (RX)</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                <svg width="120" height="120" viewBox="0 0 120 120" class="tm-ring shrink-0">
                    <defs>
                        <linearGradient id="rxGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#38bdf8"/>
                            <stop offset="100%" stop-color="#0284c7"/>
                        </linearGradient>
                    </defs>
                    <circle class="tm-ring-track" cx="60" cy="60" r="52"></circle>
                    <circle class="tm-ring-fill" id="rx-ring" cx="60" cy="60" r="52" stroke="url(#rxGrad)"></circle>
                </svg>
                <div class="text-center">
                    <div id="rx-speed" class="text-2xl sm:text-3xl font-extrabold leading-none mb-2 text-sky-600">0 bps</div>
                    <div class="text-[10px] uppercase tracking-widest text-slate-400 mb-1.5">of peak</div>
                    <div class="w-36 mx-auto bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div id="rx-bar" class="tm-bar" style="width:0%;background:linear-gradient(90deg,#0284c7,#38bdf8)"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload TX -->
        <div class="tm-card p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="tm-pulse animate-ping absolute inline-flex h-full w-full rounded-full bg-violet-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-violet-500"></span>
                    </span>
                    <span class="text-xs font-bold uppercase tracking-widest text-violet-600">Upload &uarr; (TX)</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                <svg width="120" height="120" viewBox="0 0 120 120" class="tm-ring shrink-0">
                    <defs>
                        <linearGradient id="txGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#a78bfa"/>
                            <stop offset="100%" stop-color="#7c3aed"/>
                        </linearGradient>
                    </defs>
                    <circle class="tm-ring-track" cx="60" cy="60" r="52"></circle>
                    <circle class="tm-ring-fill" id="tx-ring" cx="60" cy="60" r="52" stroke="url(#txGrad)"></circle>
                </svg>
                <div class="text-center">
                    <div id="tx-speed" class="text-2xl sm:text-3xl font-extrabold leading-none mb-2 text-violet-600">0 bps</div>
                    <div class="text-[10px] uppercase tracking-widest text-slate-400 mb-1.5">of peak</div>
                    <div class="w-36 mx-auto bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div id="tx-bar" class="tm-bar" style="width:0%;background:linear-gradient(90deg,#7c3aed,#a78bfa)"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart -->
    <div class="tm-card p-3 sm:p-5">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Bandwidth History</span>
            <button id="btn-clear" class="text-xs font-semibold transition-colors px-2.5 py-1 rounded-lg text-slate-500 hover:text-indigo-600 border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50">&#x21BA; Clear</button>
        </div>
        <div style="height:220px;">
            <canvas id="trafficChart"></canvas>
        </div>
    </div>

</div><!-- /tm-app -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var tmBaseUrl = '{$_url}';
    var tmRouter  = '{$router}';
</script>
<script>
{literal}
    var MAX_POINTS = 40;
    var chartLabels = Array(MAX_POINTS).fill('');
    var rxData      = Array(MAX_POINTS).fill(0);
    var txData      = Array(MAX_POINTS).fill(0);
    var peakRx = 0, peakTx = 0, sampleCount = 0;
    var lastUpdateTime = null;

    var ctx = document.getElementById('trafficChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [
                {
                    label: 'Download (RX) \u2193',
                    data: rxData,
                    borderColor: '#0284c7',
                    backgroundColor: 'rgba(2,132,199,0.10)',
                    borderWidth: 2.5, tension: 0.4, fill: 'start', pointRadius: 0,
                    borderCapStyle: 'round'
                },
                {
                    label: 'Upload (TX) \u2191',
                    data: txData,
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124,58,237,0.10)',
                    borderWidth: 2.5, tension: 0.4, fill: 'start', pointRadius: 0,
                    borderCapStyle: 'round'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 250 },
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: {
                    grid: { color: 'rgba(148,163,184,0.15)' },
                    title: { display: true, text: 'Time', font: { size: 11 }, color: '#94a3b8' },
                    ticks: { maxTicksLimit: 8, font: { size: 10 }, color: '#64748b' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148,163,184,0.15)' },
                    title: { display: true, text: 'Speed', font: { size: 11 }, color: '#94a3b8' },
                    ticks: { callback: function(v) { return fmtSpeed(v); }, font: { size: 10 }, color: '#64748b' }
                }
            },
            plugins: {
                legend: { position: 'top', labels: { font: { size: 12 }, usePointStyle: true, color: '#475569' } },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#f1f5f9',
                    bodyColor: '#cbd5e1',
                    callbacks: {
                        label: function(c) { return c.dataset.label + ': ' + fmtSpeed(c.parsed.y); }
                    }
                }
            }
        }
    });

    function fmtSpeed(bps) {
        if (!bps) return '0 bps';
        if (bps >= 1e9) return (bps / 1e9).toFixed(2) + ' Gbps';
        if (bps >= 1e6) return (bps / 1e6).toFixed(2) + ' Mbps';
        if (bps >= 1e3) return (bps / 1e3).toFixed(2) + ' Kbps';
        return Math.round(bps) + ' bps';
    }

    // Update a ring gauge: fraction 0..1
    function setGauge(id, fraction) {
        var el = document.getElementById(id);
        if (!el) return;
        var CIRC = 326.7;
        var f = Math.max(0, Math.min(1, fraction || 0));
        el.style.strokeDashoffset = CIRC * (1 - f);
    }

    function resetAll() {
        rxData.fill(0); txData.fill(0); chartLabels.fill('');
        peakRx = 0; peakTx = 0; sampleCount = 0;
        document.getElementById('peak-rx').textContent = '\u2014';
        document.getElementById('peak-tx').textContent = '\u2014';
        document.getElementById('sample-count').textContent = '0';
        document.getElementById('rx-bar').style.width = '0%';
        document.getElementById('tx-bar').style.width = '0%';
        setGauge('rx-ring', 0);
        setGauge('tx-ring', 0);
        chart.update();
    }

    function fetchData() {
        var ifaceName = document.getElementById('iface-select').value;
        fetch(tmBaseUrl + 'plugin/traffic_monitor_get_data/' + tmRouter + '?interface=' + encodeURIComponent(ifaceName))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.error || !data.rows || !data.rows.tx || !data.rows.rx) {
                    // Show offline status with the real reason
                    var reason = (data.error && data.error !== 'null') ? 'OFFLINE &bull; ' + data.error : 'OFFLINE &bull; check router';
                    if (reason.length > 40) reason = reason.substring(0, 40) + '...';
                    document.getElementById('tm-status-text').textContent = reason;
                    document.getElementById('tm-dot').className = 'relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500';
                    document.getElementById('tm-ping').className = 'tm-pulse animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75';
                    document.getElementById('tm-live-badge').className = 'inline-flex items-center justify-center gap-2.5 text-xs font-semibold rounded-full px-4 py-2 bg-white border border-red-200 text-red-600 w-fit';
                    return;
                }
                var rx  = parseInt(data.rows.rx[0]) || 0;
                var tx  = parseInt(data.rows.tx[0]) || 0;

                // Update status badge based on fresh/online flag
                if (data.fresh || data.online) {
                    var now = new Date();
                    lastUpdateTime = now.toLocaleTimeString();
                    document.getElementById('tm-status-text').textContent = 'LIVE &bull; last: ' + lastUpdateTime;
                    document.getElementById('tm-dot').className = 'relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500';
                    document.getElementById('tm-ping').className = 'tm-pulse animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75';
                    document.getElementById('tm-live-badge').className = 'inline-flex items-center justify-center gap-2.5 text-xs font-semibold rounded-full px-4 py-2 bg-white border border-emerald-200 text-emerald-600 w-fit';
                }
                var lbl = data.labels[0] || '';

                if (rx > peakRx) { peakRx = rx; document.getElementById('peak-rx').textContent = fmtSpeed(rx); }
                if (tx > peakTx) { peakTx = tx; document.getElementById('peak-tx').textContent = fmtSpeed(tx); }
                sampleCount++;
                document.getElementById('sample-count').textContent = sampleCount;

                document.getElementById('rx-speed').textContent = fmtSpeed(rx);
                document.getElementById('tx-speed').textContent = fmtSpeed(tx);

                document.getElementById('rx-bar').style.width = (peakRx > 0 ? Math.min((rx / peakRx) * 100, 100) : 0) + '%';
                document.getElementById('tx-bar').style.width = (peakTx > 0 ? Math.min((tx / peakTx) * 100, 100) : 0) + '%';

                setGauge('rx-ring', peakRx > 0 ? rx / peakRx : 0);
                setGauge('tx-ring', peakTx > 0 ? tx / peakTx : 0);

                chartLabels.push(lbl); chartLabels.shift();
                rxData.push(rx);       rxData.shift();
                txData.push(tx);       txData.shift();
                chart.update('none');
            })
            .catch(function(e) { console.error('Traffic fetch error:', e); });
    }

    document.getElementById('iface-select').addEventListener('change', resetAll);
    document.getElementById('btn-clear').addEventListener('click', resetAll);

    fetchData();
    setInterval(fetchData, 1000);
{/literal}
</script>
{include file="sections/footer.tpl"}
