{include file="sections/header.tpl"}
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config = {literal}{ corePlugins: { preflight: false } }{/literal};</script>
<style>
{literal}
#phr-app { font-family: 'Inter','Segoe UI',Arial,sans-serif; }
.phr-card { background:#fff; border-radius:18px; box-shadow:0 2px 16px rgba(0,0,0,.07); padding:20px 24px; }
.phr-stat { background:#f8fafc; border-radius:12px; padding:14px 20px; text-align:center; }
.phr-stat-lbl { font-size:10px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#94a3b8; margin-bottom:4px; }
.phr-stat-val { font-size:22px; font-weight:800; color:#1e293b; line-height:1; }
.phr-stat-sub { font-size:11px; color:#64748b; margin-top:3px; }
.phr-tab { padding:7px 18px; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; border:1.5px solid #e2e8f0; color:#64748b; transition:all .15s; }
.phr-tab.active { background:#4f46e5; color:#fff; border-color:#4f46e5; }
{/literal}
</style>

<div id="phr-app" class="p-4 md:p-6">

    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">&#x23F0; Peak Hours Traffic Report</h1>
            <p class="text-slate-400 text-sm mt-0.5">Discover when your network is busiest — payments, revenue & usage by hour</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="phr-card mb-5 flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Period</label>
            <select id="phr-period" class="border border-slate-200 rounded-xl px-4 py-2 text-sm font-medium text-slate-700 focus:outline-none focus:border-indigo-400">
                <option value="7">Last 7 days</option>
                <option value="14">Last 14 days</option>
                <option value="30" selected>Last 30 days</option>
                <option value="60">Last 60 days</option>
                <option value="90">Last 90 days</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Data Type</label>
            <div class="flex gap-2">
                <button class="phr-tab active" data-type="both">All</button>
                <button class="phr-tab" data-type="payments">Payments</button>
                <button class="phr-tab" data-type="usage">Usage</button>
            </div>
        </div>
        <button id="phr-refresh" class="ml-auto px-5 py-2 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 transition-colors">
            &#x21BB; Refresh
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5" id="phr-stats">
        <div class="phr-stat">
            <div class="phr-stat-lbl">&#x1F4B3; Peak Payment Hour</div>
            <div class="phr-stat-val" id="stat-peak-pay">—</div>
            <div class="phr-stat-sub">Most transactions</div>
        </div>
        <div class="phr-stat">
            <div class="phr-stat-lbl">&#x1F4B0; Peak Revenue Hour</div>
            <div class="phr-stat-val" id="stat-peak-rev">—</div>
            <div class="phr-stat-sub">Highest earnings</div>
        </div>
        <div class="phr-stat">
            <div class="phr-stat-lbl">&#x1F4F6; Peak Usage Hour</div>
            <div class="phr-stat-val" id="stat-peak-use">—</div>
            <div class="phr-stat-sub">Most active sessions</div>
        </div>
        <div class="phr-stat">
            <div class="phr-stat-lbl">&#x1F4C5; Busiest Day</div>
            <div class="phr-stat-val" id="stat-peak-day">—</div>
            <div class="phr-stat-sub">Most payments</div>
        </div>
    </div>

    <!-- Hourly Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
        <div class="phr-card">
            <div class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">&#x1F4B3; Transactions by Hour</div>
            <canvas id="chart-payments" style="max-height:260px"></canvas>
        </div>
        <div class="phr-card">
            <div class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">&#x1F4B0; Revenue by Hour (Ksh)</div>
            <canvas id="chart-revenue" style="max-height:260px"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
        <div class="phr-card">
            <div class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">&#x1F4F6; Network Activity by Hour</div>
            <canvas id="chart-usage" style="max-height:260px"></canvas>
        </div>
        <div class="phr-card">
            <div class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">&#x1F4C5; Payments by Day of Week</div>
            <canvas id="chart-dow" style="max-height:260px"></canvas>
        </div>
    </div>

    <!-- Hourly Table -->
    <div class="phr-card">
        <div class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">&#x1F4CB; Hourly Breakdown Table</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="phr-table">
                <thead>
                    <tr class="text-left text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <th class="pb-3 pr-4">Hour</th>
                        <th class="pb-3 pr-4">Transactions</th>
                        <th class="pb-3 pr-4">Revenue (Ksh)</th>
                        <th class="pb-3 pr-4">Activity Sessions</th>
                        <th class="pb-3">Traffic Level</th>
                    </tr>
                </thead>
                <tbody id="phr-tbody"></tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var phrBaseUrl = '{$_url}';
{literal}
var chartPayments, chartRevenue, chartUsage, chartDow;
var currentType = 'both';
// URL uses the registered function name directly: plugin/peak_hours_report_data

function makeChart(id, type, labels, datasets, opts) {
    var ctx = document.getElementById(id).getContext('2d');
    return new Chart(ctx, {
        type: type,
        data: { labels: labels, datasets: datasets },
        options: Object.assign({
            responsive: true,
            animation: { duration: 400 },
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 10 } } },
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: { size: 10 } } }
            }
        }, opts || {})
    });
}

function barColor(data, peak, base, highlight) {
    return data.map(function(v, i) { return i === peak ? highlight : base; });
}

function fmtKsh(v) {
    return 'Ksh ' + Number(v).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}

function trafficBadge(val, max) {
    if (max === 0) return '<span class="text-slate-300 text-xs">—</span>';
    var pct = val / max;
    if (pct >= 0.8) return '<span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">🔴 Peak</span>';
    if (pct >= 0.5) return '<span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700">🟠 High</span>';
    if (pct >= 0.25) return '<span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">🟡 Medium</span>';
    return '<span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">🟢 Low</span>';
}

function loadData() {
    var period = document.getElementById('phr-period').value;
    var url = phrBaseUrl + 'plugin/peak_hours_report/data&period=' + period + '&type=' + currentType + '&_=' + Date.now();

    fetch(url)
        .then(function(r) { return r.json(); })
        .then(function(d) {
            // Summary cards
            document.getElementById('stat-peak-pay').textContent = d.peak_payment_hour;
            document.getElementById('stat-peak-rev').textContent = d.peak_revenue_hour;
            document.getElementById('stat-peak-use').textContent = d.peak_usage_hour;
            document.getElementById('stat-peak-day').textContent = d.peak_day;

            var peakPay = d.payments_by_hour.indexOf(Math.max.apply(null, d.payments_by_hour));
            var peakRev = d.revenue_by_hour.indexOf(Math.max.apply(null, d.revenue_by_hour));
            var peakUse = d.usage_by_hour.indexOf(Math.max.apply(null, d.usage_by_hour));
            var peakDow = d.payments_dow.indexOf(Math.max.apply(null, d.payments_dow));

            // Payments chart
            var payColors = barColor(d.payments_by_hour, peakPay, 'rgba(99,102,241,0.6)', 'rgba(99,102,241,1)');
            if (chartPayments) {
                chartPayments.data.datasets[0].data = d.payments_by_hour;
                chartPayments.data.datasets[0].backgroundColor = payColors;
                chartPayments.update();
            } else {
                chartPayments = makeChart('chart-payments', 'bar', d.labels, [{
                    label: 'Transactions', data: d.payments_by_hour,
                    backgroundColor: payColors, borderRadius: 6
                }]);
            }

            // Revenue chart
            var revColors = barColor(d.revenue_by_hour, peakRev, 'rgba(16,185,129,0.6)', 'rgba(16,185,129,1)');
            if (chartRevenue) {
                chartRevenue.data.datasets[0].data = d.revenue_by_hour;
                chartRevenue.data.datasets[0].backgroundColor = revColors;
                chartRevenue.update();
            } else {
                chartRevenue = makeChart('chart-revenue', 'bar', d.labels, [{
                    label: 'Revenue', data: d.revenue_by_hour,
                    backgroundColor: revColors, borderRadius: 6
                }], {
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: function(c) { return fmtKsh(c.parsed.y); } } }
                    }
                });
            }

            // Usage chart
            var useColors = barColor(d.usage_by_hour, peakUse, 'rgba(245,158,11,0.6)', 'rgba(245,158,11,1)');
            if (chartUsage) {
                chartUsage.data.datasets[0].data = d.usage_by_hour;
                chartUsage.data.datasets[0].backgroundColor = useColors;
                chartUsage.update();
            } else {
                chartUsage = makeChart('chart-usage', 'bar', d.labels, [{
                    label: 'Sessions', data: d.usage_by_hour,
                    backgroundColor: useColors, borderRadius: 6
                }]);
            }

            // Day of week chart
            var dowColors = barColor(d.payments_dow, peakDow, 'rgba(139,92,246,0.6)', 'rgba(139,92,246,1)');
            if (chartDow) {
                chartDow.data.datasets[0].data = d.payments_dow;
                chartDow.data.datasets[0].backgroundColor = dowColors;
                chartDow.update();
            } else {
                chartDow = makeChart('chart-dow', 'bar', d.days_label, [{
                    label: 'Payments', data: d.payments_dow,
                    backgroundColor: dowColors, borderRadius: 6
                }]);
            }

            // Table
            var maxPay = Math.max.apply(null, d.payments_by_hour);
            var tbody = document.getElementById('phr-tbody');
            tbody.innerHTML = '';
            for (var h = 0; h < 24; h++) {
                var isPeak = h === peakPay;
                var row = '<tr class="border-b border-slate-50 hover:bg-slate-50 ' + (isPeak ? 'bg-indigo-50' : '') + '">';
                row += '<td class="py-2 pr-4 font-semibold text-slate-700">' + d.labels[h] + (isPeak ? ' <span class="text-indigo-500 text-xs">★ Peak</span>' : '') + '</td>';
                row += '<td class="py-2 pr-4 text-slate-600">' + d.payments_by_hour[h].toLocaleString() + '</td>';
                row += '<td class="py-2 pr-4 text-slate-600">' + fmtKsh(d.revenue_by_hour[h]) + '</td>';
                row += '<td class="py-2 pr-4 text-slate-600">' + d.usage_by_hour[h].toLocaleString() + '</td>';
                row += '<td class="py-2">' + trafficBadge(d.payments_by_hour[h], maxPay) + '</td>';
                row += '</tr>';
                tbody.innerHTML += row;
            }
        })
        .catch(function(e) { console.error('Peak hours fetch error:', e); });
}

// Tab switching
document.querySelectorAll('.phr-tab').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.phr-tab').forEach(function(b) { b.classList.remove('active'); });
        btn.classList.add('active');
        currentType = btn.dataset.type;
        loadData();
    });
});

document.getElementById('phr-refresh').addEventListener('click', loadData);
document.getElementById('phr-period').addEventListener('change', loadData);

loadData();
{/literal}
</script>
{include file="sections/footer.tpl"}
