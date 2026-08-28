{include file="sections/header.tpl"}
<!-- Tailwind CDN - preflight disabled to avoid Bootstrap conflicts -->
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config = {literal}{ corePlugins: { preflight: false } }{/literal};</script>
<style>
{literal}
    #tm-app {
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        min-height: 100vh;
        background: radial-gradient(1200px 600px at 15% -10%, rgba(99,102,241,.22), transparent 60%),
                    radial-gradient(1000px 500px at 90% 0%, rgba(217,70,239,.16), transparent 55%),
                    linear-gradient(160deg, #0b1020 0%, #0e1228 45%, #101736 100%);
        color: #e2e8f0;
    }

    /* ── Animated background glow blobs ── */
    .tm-blob { position:fixed; border-radius:50%; filter:blur(90px); opacity:.16; pointer-events:none; z-index:0; }
    .tm-blob-1 { width:420px; height:420px; background:#6366f1; top:-120px; left:-80px; }
    .tm-blob-2 { width:380px; height:380px; background:#d946ef; bottom:-120px; right:-60px; }

    /* Pulse dot */
    @keyframes tm-pulse { 0%,100%{opacity:1} 50%{opacity:.35} }
    .tm-pulse { animation: tm-pulse 1.6s cubic-bezier(.4,0,.6,1) infinite; }

    /* Smooth bar */
    .tm-bar { height:100%; border-radius:9999px; transition: width .5s cubic-bezier(.4,0,.2,1); }

    /* Router pill tabs */
    .tm-router-tab { transition: all .18s; backdrop-filter: blur(6px); }
    .tm-router-tab.tm-active {
        background: linear-gradient(135deg,#6366f1,#8b5cf6);
        color:#fff !important; border-color:transparent !important;
        box-shadow: 0 8px 24px rgba(99,102,241,.45);
    }

    /* Glass cards */
    .tm-glass {
        background: rgba(255,255,255,.045);
        border: 1px solid rgba(255,255,255,.09);
        backdrop-filter: blur(14px);
        border-radius: 22px;
        box-shadow: 0 20px 50px rgba(0,0,0,.35);
    }

    /* Speed gauge card */
    .tm-gauge-card { position:relative; overflow:hidden; }
    .tm-gauge-card::before {
        content:''; position:absolute; inset:0;
        background: radial-gradient(220px 220px at 50% -20%, var(--glow), transparent 70%);
        opacity:.35; pointer-events:none;
    }

    /* Interface select */
    .tm-select {
        border: 1.5px solid rgba(255,255,255,.14); background: rgba(255,255,255,.06);
        border-radius: 12px; padding: 9px 16px; font-size:14px; font-weight:600; color:#e2e8f0;
        outline:none; transition: border-color .2s, box-shadow .2s; backdrop-filter: blur(6px);
    }
    .tm-select option { background:#11152e; color:#e2e8f0; }
    .tm-select:focus { border-color:#818cf8; box-shadow: 0 0 0 3px rgba(99,102,241,.25); }

    /* Stats badge */
    .tm-stat { background: rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.08); border-radius:14px; padding:10px 18px; text-align:center; min-width:108px; }
    .tm-stat-lbl { font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; margin-bottom:2px; }
    .tm-stat-val { font-size:19px; font-weight:800; line-height:1; }

    /* Gradient text */
    .tm-grad-text { background-clip:text; -webkit-background-clip:text; -webkit-text-fill-color:transparent; }

    /* Ring gauges */
    .tm-ring { transform: rotate(-90deg); }
    .tm-ring-track { fill:none; stroke:rgba(255,255,255,.08); stroke-width:10; }
    .tm-ring-fill { fill:none; stroke-width:10; stroke-linecap:round; stroke-dasharray:326.7; stroke-dashoffset:326.7; transition: stroke-dashoffset .5s cubic-bezier(.4,0,.2,1); filter: drop-shadow(0 0 6px currentColor); }

    /* Scrollbar */
    ::-webkit-scrollbar{width:5px;height:5px}
    ::-webkit-scrollbar-track{background:rgba(255,255,255,.05)}
    ::-webkit-scrollbar-thumb{background:#4f5b7a;border-radius:3px}
{/literal}
</style>

<div id="tm-app" class="relative p-4 md:p-7" style="z-index:1;">
    <div class="tm-blob tm-blob-1"></div>
    <div class="tm-blob tm-blob-2"></div>

    <div class="relative" style="z-index:2;">

        <!-- Page Header -->
        <div class="flex flex-wrap items-center justify-between gap-3 mb-7">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">&#x1F4CA; Live Traffic Monitor</h1>
                <p class="text-slate-400 text-sm mt-1">Real-time bandwidth usage per interface &bull; router live view</p>
            </div>
            <div class="flex items-center gap-2.5 text-xs font-semibold rounded-full px-4 py-2 tm-glass" style="color:#a5b4fc;">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="tm-pulse animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                LIVE &bull; 1s refresh
            </div>
        </div>

        <!-- Router Tabs -->
        <div class="flex flex-wrap gap-2 mb-6">
            {foreach $routers as $r}
            <a href="{$_url}plugin/traffic_monitor_ui/{$r['id']}"
               class="tm-router-tab {if $r['id']==$router}tm-active{/if} px-5 py-2 text-sm font-semibold rounded-xl border border-white/10 bg-white/5 text-slate-400 hover:text-white hover:bg-white/10">
                {$r['name']}
            </a>
            {/foreach}
        </div>

        <!-- Interface selector + peak stats -->
        <div class="flex flex-wrap items-end gap-4 mb-6">
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest mb-1.5" style="color:#818cf8;">Interface</label>
                <select id="iface-select" class="tm-select">
                    {foreach $interfaces as $iface}
                    <option value="{$iface}">{$iface}</option>
                    {/foreach}
                </select>
            </div>
            <div class="flex gap-3">
                <div class="tm-stat">
                    <div class="tm-stat-lbl" style="color:#38bdf8">Peak RX</div>
                    <div class="tm-stat-val" id="peak-rx" style="color:#7dd3fc">&mdash;</div>
                </div>
                <div class="tm-stat">
                    <div class="tm-stat-lbl" style="color:#c084fc">Peak TX</div>
                    <div class="tm-stat-val" id="peak-tx" style="color:#d8b4fe">&mdash;</div>
                </div>
                <div class="tm-stat">
                    <div class="tm-stat-lbl" style="color:#94a3b8">Samples</div>
                    <div class="tm-stat-val" id="sample-count" style="color:#cbd5e1">0</div>
                </div>
            </div>
        </div>

        <!-- Speed Gauge Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">

            <!-- Download RX -->
            <div class="tm-glass tm-gauge-card p-6" style="--glow:#0ea5e9;">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="tm-pulse animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-sky-400"></span>
                        </span>
                        <span class="text-xs font-bold uppercase tracking-widest" style="color:#38bdf8;">Download &darr; (RX)</span>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-8 flex-wrap">
                    <svg width="150" height="150" viewBox="0 0 120 120" class="tm-ring">
                        <defs>
                            <linearGradient id="rxGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#38bdf8"/>
                                <stop offset="100%" stop-color="#0ea5e9"/>
                            </linearGradient>
                        </defs>
                        <circle class="tm-ring-track" cx="60" cy="60" r="52"></circle>
                        <circle class="tm-ring-fill" id="rx-ring" cx="60" cy="60" r="52" stroke="url(#rxGrad)" style="color:#0ea5e9;"></circle>
                    </svg>
                    <div class="text-center">
                        <div id="rx-speed" class="tm-grad-text text-5xl font-extrabold leading-none mb-3" style="background-image:linear-gradient(135deg,#7dd3fc,#0ea5e9);">0 bps</div>
                        <div class="text-[11px] uppercase tracking-widest text-slate-500 mb-2">of peak</div>
                        <div class="w-44 bg-white/10 rounded-full h-2 overflow-hidden">
                            <div id="rx-bar" class="tm-bar" style="width:0%;background:linear-gradient(90deg,#0ea5e9,#38bdf8)"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload TX -->
            <div class="tm-glass tm-gauge-card p-6" style="--glow:#8b5cf6;">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="tm-pulse animate-ping absolute inline-flex h-full w-full rounded-full bg-violet-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-violet-400"></span>
                        </span>
                        <span class="text-xs font-bold uppercase tracking-widest" style="color:#c084fc;">Upload &uarr; (TX)</span>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-8 flex-wrap">
                    <svg width="150" height="150" viewBox="0 0 120 120" class="tm-ring">
                        <defs>
                            <linearGradient id="txGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#a78bfa"/>
                                <stop offset="100%" stop-color="#8b5cf6"/>
                            </linearGradient>
                        </defs>
                        <circle class="tm-ring-track" cx="60" cy="60" r="52"></circle>
                        <circle class="tm-ring-fill" id="tx-ring" cx="60" cy="60" r="52" stroke="url(#txGrad)" style="color:#8b5cf6;"></circle>
                    </svg>
                    <div class="text-center">
                        <div id="tx-speed" class="tm-grad-text text-5xl font-extrabold leading-none mb-3" style="background-image:linear-gradient(135deg,#d8b4fe,#8b5cf6);">0 bps</div>
                        <div class="text-[11px] uppercase tracking-widest text-slate-500 mb-2">of peak</div>
                        <div class="w-44 bg-white/10 rounded-full h-2 overflow-hidden">
                            <div id="tx-bar" class="tm-bar" style="width:0%;background:linear-gradient(90deg,#8b5cf6,#a78bfa)"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Chart -->
        <div class="tm-glass p-6">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-widest" style="color:#94a3b8;">Bandwidth History</span>
                <button id="btn-clear" class="text-xs font-semibold transition-colors px-3 py-1.5 rounded-lg hover:bg-white/10" style="color:#94a3b8;border:1px solid rgba(255,255,255,.12);">&#x21BA; Clear</button>
            </div>
            <canvas id="trafficChart" style="max-height:320px"></canvas>
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

    var ctx = document.getElementById('trafficChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [
                {
                    label: 'Download (RX) \u2193',
                    data: rxData,
                    borderColor: '#38bdf8',
                    backgroundColor: 'rgba(56,189,248,0.14)',
                    borderWidth: 2.5, tension: 0.4, fill: 'start', pointRadius: 0,
                    borderCapStyle: 'round'
                },
                {
                    label: 'Upload (TX) \u2191',
                    data: txData,
                    borderColor: '#a78bfa',
                    backgroundColor: 'rgba(167,139,250,0.14)',
                    borderWidth: 2.5, tension: 0.4, fill: 'start', pointRadius: 0,
                    borderCapStyle: 'round'
                }
            ]
        },
        options: {
            responsive: true,
            animation: { duration: 250 },
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: {
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    title: { display: true, text: 'Time', font: { size: 11 }, color: '#94a3b8' },
                    ticks: { maxTicksLimit: 8, font: { size: 10 }, color: '#64748b' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    title: { display: true, text: 'Speed', font: { size: 11 }, color: '#94a3b8' },
                    ticks: { callback: function(v) { return fmtSpeed(v); }, font: { size: 10 }, color: '#64748b' }
                }
            },
            plugins: {
                legend: { position: 'top', labels: { font: { size: 12 }, usePointStyle: true, color: '#cbd5e1' } },
                tooltip: {
                    backgroundColor: 'rgba(17,21,46,0.92)',
                    borderColor: 'rgba(255,255,255,0.12)',
                    borderWidth: 1,
                    titleColor: '#e2e8f0',
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
                if (data.error || !data.rows || !data.rows.tx || !data.rows.rx) return;
                var rx  = parseInt(data.rows.rx[0]) || 0;
                var tx  = parseInt(data.rows.tx[0]) || 0;
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
