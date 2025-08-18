{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Live Traffic Monitor</h3>
            </div>
            <div class="panel-body">
                <!-- Router Selection -->
                <div class="mb-3">
                    <form class="form-horizontal" method="post" role="form">
                        <ul class="nav nav-tabs">
                            {foreach $routers as $r}
                            <li role="presentation" {if $r['id']==$router}class="active" {/if}>
                                <a href="{$_url}plugin/traffic_monitor_ui/{$r['id']}">{$r['name']}</a>
                            </li>
                            {/foreach}
                        </ul>
                    </form>
                </div>

                <!-- Interface Selection -->
                <div class="mb-3">
                    <label for="interface-select">Select Interface:</label>
                    <select id="interface-select" class="form-control" style="width: 200px;">
                        {foreach $interfaces as $interface}
                        <option value="{$interface}">{$interface}</option>
                        {/foreach}
                    </select>
                </div>

                <!-- Traffic Graph -->
                <div class="traffic-container">
                    <canvas id="trafficChart"></canvas>
                </div>

                <!-- Current Speed Display -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-aqua"><i class="ion ion-arrow-down-c"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Download Speed</span>
                                <span class="info-box-number" id="rx-speed">0 Mbps</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="ion ion-arrow-up-c"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Upload Speed</span>
                                <span class="info-box-number" id="tx-speed">0 Mbps</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{literal}
<style>
    .traffic-container {
        background: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        margin: 20px 0;
        height: 400px;
    }
    .info-box {
        min-height: 100px;
        background: #fff;
        width: 100%;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        border-radius: 2px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }
    .info-box-icon {
        border-top-left-radius: 2px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 2px;
        display: block;
        float: left;
        height: 100px;
        width: 100px;
        text-align: center;
        font-size: 45px;
        line-height: 100px;
        background: rgba(0,0,0,0.2);
        color: white;
    }
    .info-box-content {
        padding: 5px 10px;
        margin-left: 100px;
    }
    .info-box-text {
        display: block;
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .info-box-number {
        display: block;
        font-weight: bold;
        font-size: 24px;
    }
    .bg-aqua {
        background-color: #00c0ef !important;
    }
    .bg-green {
        background-color: #00a65a !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('trafficChart').getContext('2d');
    const maxDataPoints = 30;
    
    // Initialize the chart
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: Array(maxDataPoints).fill(''),
            datasets: [
                {
                    label: 'Download',
                    borderColor: '#00c0ef',
                    backgroundColor: 'rgba(0, 192, 239, 0.1)',
                    data: Array(maxDataPoints).fill(0),
                    fill: true
                },
                {
                    label: 'Upload',
                    borderColor: '#00a65a',
                    backgroundColor: 'rgba(0, 166, 90, 0.1)',
                    data: Array(maxDataPoints).fill(0),
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Speed (Mbps)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Time'
                    }
                }
            },
            animation: {
                duration: 0
            }
        }
    });

    function formatSpeed(bitsPerSecond) {
        if (bitsPerSecond >= 1000000000) {
            return (bitsPerSecond / 1000000000).toFixed(2) + ' Gbps';
        } else if (bitsPerSecond >= 1000000) {
            return (bitsPerSecond / 1000000).toFixed(2) + ' Mbps';
        } else if (bitsPerSecond >= 1000) {
            return (bitsPerSecond / 1000).toFixed(2) + ' Kbps';
        } else {
            return bitsPerSecond.toFixed(2) + ' bps';
        }
    }

    function updateTrafficData() {
        const interface = document.getElementById('interface-select').value;
        const currentRouter = '{/literal}{$router}{literal}';
        
        fetch(`{/literal}{$_url}{literal}plugin/traffic_monitor_get_data/${currentRouter}?interface=${interface}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }
                
                if (!data.rows || !data.rows.tx || !data.rows.rx) {
                    return;
                }

                const txRate = data.rows.tx[0];
                const rxRate = data.rows.rx[0];

                // Update speed displays
                document.getElementById('rx-speed').textContent = formatSpeed(rxRate);
                document.getElementById('tx-speed').textContent = formatSpeed(txRate);

                // Update chart
                chart.data.labels.push(data.labels[0]);
                chart.data.labels.shift();

                chart.data.datasets[0].data.push(rxRate / 1000000); // Convert to Mbps
                chart.data.datasets[0].data.shift();
                
                chart.data.datasets[1].data.push(txRate / 1000000); // Convert to Mbps
                chart.data.datasets[1].data.shift();
                
                chart.update('none');
            })
            .catch(error => console.error('Error fetching traffic data:', error));
    }

    // Update every second
    setInterval(updateTrafficData, 1000);
    
    // Initial update
    updateTrafficData();
    
    // Handle interface change
    document.getElementById('interface-select').addEventListener('change', function() {
        // Reset chart data
        chart.data.datasets[0].data = Array(maxDataPoints).fill(0);
        chart.data.datasets[1].data = Array(maxDataPoints).fill(0);
        chart.data.labels = Array(maxDataPoints).fill('');
        chart.update();
    });
});
</script>
{/literal}

{include file="sections/footer.tpl"}
