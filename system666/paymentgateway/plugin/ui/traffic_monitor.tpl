{include file="sections/header.tpl"}
<!-- Tailwind CDN - preflight disabled to avoid Bootstrap conflicts -->
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config = {literal}{ corePlugins: { preflight: false } }{/literal};</script>
<style>
{literal}
    #tm-app { font-family: 'Inter', 'Segoe UI', Arial, sans-serif; }

    /* Pulse dot */
    @keyframes tm-pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
    .tm-pulse { animation: tm-pulse 2s cubic-bezier(.4,0,.6,1) infinite; }

    /* Smooth bar */
    .tm-bar { height:100%; border-radius:9999px; transition: width .6s cubic-bezier(.4,0,.2,1); }

    /* Router tabs */
    .tm-router-tab { transition: all .15s; }
    .tm-router-tab.tm-active { background:#4f46e5; color:#fff !important; border-color:#4f46e5 !important; box-shadow: 0 4px 12px rgba(79,70,229,.3); }

    /* Speed cards */
    .tm-speed-card { background:#fff; border-radius:20px; box-shadow: 0 2px 16px rgba(0,0,0,.07); overflow:hidden; position:relative; transition: box-shadow .2s; }
    .tm-speed-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,.12); }

    /* Chart wrapper */
    .tm-chart-wrap { background:#fff; border-radius:20px; box-shadow: 0 2px 16px rgba(0,0,0,.07); padding: 20px 24px; }

    /* Interface select */
    .tm-select { border: 1.5px solid #e2e8f0; background:#fff; border-radius:12px; padding: 9px 16px; font-size:14px; font-weight:500; color:#334155; outline:none; transition: border-color .2s, box-shadow .2s; }
    .tm-select:focus { border-color:#6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.15); }

    /* Stats badge */
    .tm-stat { background:#f8fafc; border-radius:12px; padding:10px 18px; text-align:center; min-width:100px; }
    .tm-stat-lbl { font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; margin-bottom:2px; }
    .tm-stat-val { font-size:18px; font-weight:800; line-height:1; }

    /* Scrollbar */
    ::-webkit-scrollbar{width:5px;height:5px}
    ::-webkit-scrollbar-track{background:#f1f5f9}
    ::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:3px}
{/literal}
</style>

<div id="tm-app" class="p-4 md:p-6">

    <!-- Page Header -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">&#x1F4CA; Live Traffic Monitor</h1>
            <p class="text-slate-400 text-sm mt-0.5">Real-time bandwidth usage per interface</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-400 font-medium">
            <span class="relative flex h-2 w-2">
                <span class="tm-pulse animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            Live &bull; updates every 1s
        </div>
    </div>

    <!-- Router Tabs -->
    <div class="flex flex-wrap gap-2 mb-6">
        {foreach $routers as $r}
        <a href="{$_url}plugin/traffic_monitor_ui/{$r['id']}"
           class="tm-router-tab {if $r['id']==$router}tm-active{/if} px-5 py-2 text-sm font-semibold rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200">
            {$r['name']}
        </a>
        {/foreach}
    </div>

    <!-- Interface selector + peak stats -->
    <div class="flex flex-wrap items-end gap-4 mb-6">
        <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">Interface</label>
            <select id="iface-select" class="tm-select">
                {foreach $interfaces as $iface}
                <option value="{$iface}">{$iface}</option>
                {/foreach}
            </select>
        </div>
        <div class="flex gap-3">
            <div class="tm-stat">
                <div class="tm-stat-lbl" style="color:#0ea5e9">Peak RX</div>
                <div class="tm-stat-val" id="peak-rx" style="color:#0369a1">&mdash;</div>
            </div>
            <div class="tm-stat">
                <div class="tm-stat-lbl" style="color:#8b5cf6">Peak TX</div>
                <div class="tm-stat-val" id="peak-tx" style="color:#6d28d9">&mdash;</div>
            </div>
            <div class="tm-stat">
                <div class="tm-stat-lbl" style="color:#64748b">Samples</div>
                <div class="tm-stat-val" id="sample-count" style="color:#334155">0</div>
            </div>
        </div>
    </div>

    <!-- Speed Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

        <!-- Download RX -->
        <div class="tm-speed-card p-6">
            <div class="absolute top-0 left-0 right-0 h-1.5 rounded-t-2xl" style="background:linear-gradient(90deg,#0ea5e9,#38bdf8)"></div>
            <div class="flex items-center gap-2 mb-3">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="tm-pulse animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-sky-500"></span>
                </span>
                <span class="text-xs font-bold uppercase tracking-widest text-sky-500">Download &darr; (RX)</span>
            </div>
            <div id="rx-speed" class="text-5xl font-extrabold text-sky-600 leading-none mb-4">0 bps</div>
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                <div id="rx-bar" class="tm-bar" style="width:0%;background:linear-gradient(90deg,#0ea5e9,#38bdf8)"></div>
            </div>
        </div>

        <!-- Upload TX -->
        <div class="tm-speed-card p-6">
            <div class="absolute top-0 left-0 right-0 h-1.5 rounded-t-2xl" style="background:linear-gradient(90deg,#8b5cf6,#a78bfa)"></div>
            <div class="flex items-center gap-2 mb-3">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="tm-pulse animate-ping absolute inline-flex h-full w-full rounded-full bg-violet-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-violet-500"></span>
                </span>
                <span class="text-xs font-bold uppercase tracking-widest text-violet-500">Upload &uarr; (TX)</span>
            </div>
            <div id="tx-speed" class="text-5xl font-extrabold text-violet-600 leading-none mb-4">0 bps</div>
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                <div id="tx-bar" class="tm-bar" style="width:0%;background:linear-gradient(90deg,#8b5cf6,#a78bfa)"></div>
            </div>
        </div>

    </div>

    <!-- Chart -->
    <div class="tm-chart-wrap">
        <div class="flex items-center justify-between mb-4">
            <span class="text-sm font-bold text-slate-600 uppercase tracking-widest" style="font-size:11px">Bandwidth History</span>
            <button id="btn-clear" class="text-xs text-slate-400 hover:text-indigo-500 font-semibold transition-colors px-3 py-1 rounded-lg hover:bg-indigo-50">&#x21BA; Clear</button>
        </div>
        <canvas id="trafficChart" style="max-height:320px"></canvas>
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
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14,165,233,0.12)',
                    borderWidth: 2, tension: 0.4, fill: 'start', pointRadius: 0
                },
                {
                    label: 'Upload (TX) \u2191',
                    data: txData,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139,92,246,0.12)',
                    borderWidth: 2, tension: 0.4, fill: 'start', pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            animation: { duration: 200 },
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: {
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    title: { display: true, text: 'Time', font: { size: 11 } },
                    ticks: { maxTicksLimit: 8, font: { size: 10 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    title: { display: true, text: 'Speed', font: { size: 11 } },
                    ticks: { callback: function(v) { return fmtSpeed(v); }, font: { size: 10 } }
                }
            },
            plugins: {
                legend: { position: 'top', labels: { font: { size: 12 }, usePointStyle: true } },
                tooltip: {
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

    function resetAll() {
        rxData.fill(0); txData.fill(0); chartLabels.fill('');
        peakRx = 0; peakTx = 0; sampleCount = 0;
        document.getElementById('peak-rx').textContent = '\u2014';
        document.getElementById('peak-tx').textContent = '\u2014';
        document.getElementById('sample-count').textContent = '0';
        document.getElementById('rx-bar').style.width = '0%';
        document.getElementById('tx-bar').style.width = '0%';
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
