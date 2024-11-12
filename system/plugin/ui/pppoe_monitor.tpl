{include file="sections/header.tpl"}

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>

<style>
    .modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
    }
    .modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 0px;
    border: 1px solid #888;
    width: 80%;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    max-width: 600px;
    }
    .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    }
    .close:hover,
    .close:focus {
    color: black;
    text-decoration: none;
    }
    .card-body {
    padding: 1rem;
    }
    .card-header {
    padding: .75rem 1.25rem;
    margin-bottom: 0;
    background-color: none;
    border-bottom: 1px solid rgba(0,0,0,.125);
    }
    .card-title {
    margin-bottom: .75rem;
    }
    .form-group {
    margin-bottom: 1rem;
    }
    .table-responsive {
    display: block;
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    -ms-overflow-style: -ms-autohiding-scrollbar;
    }
    .table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
    }
    .container {
        padding-top: 20px;
    }
    #ppp-table_wrapper {
        padding: 15px;
    }
    #ppp-table th, #ppp-table td {
        text-align: center;
        padding: 6px;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }
    .panel-default {
        border-color: #ddd;
    }
    .panel-heading {
        background-color: #f5f5f5;
        border-color: #ddd;
    }
    .nav-tabs {
        margin-bottom: 15px;
    }
    .nav-tabs > li > a {
        border-radius: 0;
        color: #555;
        background-color: #f9f9f9;
        border-color: #ddd;
    }
    .nav-tabs > li.active > a,
    .nav-tabs > li.active > a:focus,
    .nav-tabs > li.active > a:hover {
        background-color: #fff;
        color: #333;
        border: 1px solid #ddd;
        border-bottom-color: transparent;
        cursor: default;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }
    .table th {
        background-color: #f5f5f5;
        color: #333;
        font-weight: bold;
        padding: 8px;
    }
    .table-striped > tbody > tr > td {
        background-color: #fff;
    }
    .status-connect {
        color: #5cb85c;
    }
    .status-disconnect {
        color: #d9534f;
    }
      .modalsupport {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgb(0,0,0);
      background-color: rgba(0,0,0,0.4);
      justify-content: center;
      align-items: center;
    }
    .modalsupport-content {
      background-color: #fefefe;
      margin: auto;
      padding: 0px;
      border: 1px solid #888;
      width: 80%;
      max-width: 500px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      text-align: center;
    }
    .modalsupport-close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }
    .modalsupport-close:hover,
    .modalsupport-close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
    .card {
      border: none;
    }
    .card-header {
      background-color: none;
      border-bottom: none;
    }
    .card-body {
      padding: 20px;
    }
    .donate-button {
      margin-top: 10px;
    }
    .modalsupport img {
      width: 100px;
      height: auto;
      margin-top: 15px;
    }
    .dataSize {
        white-space: nowrap;
    }
    .action-icons i {
      cursor: pointer;
      margin-right: 10px;
      color: #007bff;
    }
    .action-icons i:hover {
      color: #0056b3;
    }
    .modal-title {
    text-align: center;
    width: 100%;
    display: block;
    font-size: 20px;
    font-weight: bold;
    margin-top: 20px;
  }
  .table-bordered {
      width: 100%;
      max-width: 100%;
      table-layout: fixed;
  }
  .table-bordered th, .table-bordered td {
      width: auto;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      background: none;
      border: none;
      padding: 10px;
      vertical-align: middle;
      text-align: center;
  }
  .advanced-search-container {
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    }
    .form-inline .form-group {
        margin-right: 10px;
    }
    .dataTables_filter {
        display: none;
    }
    @media (max-width: 768px) {
        .panel-default {
            padding: 10px;
            margin: 0;
        }
        .panel-heading {
            padding: 5px 15px;
        }
        .panel-body {
            padding: 5px 10px;
        }
    .table th, .table td {
        font-size: 15px; /* Mengurangi ukuran font pada tabel */
        }
    }
    .traffic-icon {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 5px;
        vertical-align: middle;
    }
    .traffic-icon-green {
        background-color: green;
    }
    .traffic-icon-yellow {
        background-color: yellow;
    }
    .traffic-icon-red {
        background-color: red;
    }
    .text-left {
    text-align: left !important;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <!-- Form dan navigasi tabs -->
            <form class="form-horizontal" method="post" role="form" action="{$_url}plugin/pppoe_monitor_router_menu">
                <ul class="nav nav-tabs">
                    {foreach $routers as $r}
                    <li role="presentation" {if $r['id']==$router}class="active"{/if}>
                        <a href="{$_url}plugin/pppoe_monitor_router_menu/{$r['id']}">{$r['name']}</a>
                    </li>
                    {/foreach}
                </ul>
            </form>
            <div class="advanced-search-container">
                <form id="advancedSearchForm" class="form-inline">
                    <div class="form-group">
                        <label for="searchUsername">Username:</label>
                        <input type="text" class="form-control" id="searchUsername" placeholder="Enter username">
                    </div>
                    <div class="form-group">
                        <label for="searchStatus">Status:</label>
                        <select class="form-control" id="searchStatus">
                            <option value="">Any</option>
                            <option value="Connected">Connected</option>
                            <option value="Disconnected">Disconnected</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="panel panel-default">
                <div class="table-responsive">
                    <div class="panel-body">
                        <table class="table table-striped" id="ppp-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>IP Address</th>
                                    <th>Uptime</th>
                                    <th>Service</th>
                                    <th>Caller ID</th>
                                    <th>Download</th>
                                    <th>Upload</th>
                                    <th>Max Limit</th>
                                    <th>Total Usage</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Isi tabel akan dimasukkan melalui JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="detailsModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <div class="container-fluid mt-5">
      <div class="card">
        <div class="card-header">
          <h5 class="modal-title">Trafik for <span id="modalUsername"></span></h5>
        </div>
        <div class="card-body">
          <div class="table-responsive mt-4">
            <table class="table table-bordered">
              <thead>
                <tr>
                <input type="hidden" id="interface" value="">
                  <th id="tabletx"><i class="fa fa-download"></i></th>
                  <th id="tablerx"><i class="fa fa-upload"></i></th>
                </tr>
              </thead>
            </table>
          </div>
          <div id="chart" class="mt-3"></div>
          <div id="dailyChart" class="mt-3"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="donationPopup" class="modalsupport">
  <div class="modalsupport-content">
    <span class="modalsupport-close">&times;</span>
    <div class="container-fluid mt-5">
      <div class="card">
        <div class="card-header">
          <h5 class="modal-title">Support Us</h5>
        </div>
        <div class="card-body">
          <p>Your support helps us maintain and improve our services. Consider donating today!</p>
          <button class="btn btn-primary donate-button">Donate Now</button>
          <p class="mt-3">Thank you for your support!</p>
          <img src="https://kodingku.my.id/bmc_qr.png" alt="QR Code">
        </div>
      </div>
    </div>
  </div>
</div>
<script>
var $j = jQuery.noConflict();

$j(document).ready(function() {
    var table = $j('#ppp-table').DataTable({
        responsive: true,
        columns: [
            { data: 'id', visible: false },
            {
                data: 'username',
                className: 'text-left',
                render: function(data, type, row) {
                    return '<div style="width: 150px;"><i class="traffic-icon traffic-icon-green"></i> ' + data + '</div>';
                }
            },
            { data: 'address' },
            { data: 'uptime' },
            { data: 'service' },
            { data: 'caller_id' },
            { data: 'tx', className: 'dataSize' },
            { data: 'rx', className: 'dataSize' },
            {
                data: 'max_limit',
                className: 'dataSize',
                render: function(data, type, row) {
                    return getMaxLimit(row);
                }
            },
            { data: 'total', className: 'dataSize' },
            {
                data: 'status',
                render: function(data) {
                    if (data === 'Connected') {
                        return '<small class="label bg-green">Connected</small>';
                    } else if (data === 'Disconnected') {
                        return '<small class="label bg-red">Disconnected</small>';
                    } else {
                        return '';
                    }
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return '<div class="action-icons" style="display: flex; align-items: center;">' +
                        '<i class="fa fa-area-chart view-details" style="color: blue; cursor: pointer;" title="View Traffic" data-username="' + row.username + '" data-id="' + row.id + '"></i> ' +
                        '<i class="fa fa-retweet reconnect-button" style="color: red; cursor: pointer;" title="Reconnect" data-username="' + row.username + '" data-id="' + row.id + '"></i> ' +
                        '</div>';
                }
            }
        ],
        order: [[0, 'asc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
        dom: 'Bfrtip',
        buttons: ['reset', 'pageLength'],
        paging: true,
        info: true,
        searching: true,
        ajax: {
            url: '{$_url}plugin/pppoe_monitor_router_get_combined_users/{$router}',
            dataSrc: ''
        }
    });


    // Fungsi untuk mendapatkan batas maksimum
    function getMaxLimit(data) {
        if (data.hasOwnProperty('max_limit')) {
            return data.max_limit.toString();
        } else {
            return 'N/A';
        }
    }
    
    // Handle view details icon clicks
    $j('#ppp-table tbody').on('click', '.view-details', function(e) {
        e.preventDefault();
        var username = $j(this).data('username');
        var id = $j(this).data('id');

        viewDetails(id, username);
    });

    // Handle reconnect icon clicks
    $j('#ppp-table tbody').on('click', '.reconnect-button', function(e) {
        e.preventDefault();
        var username = $j(this).data('username');
        var id = $j(this).data('id');

        reconnect(id, username);
    });

    // Function to handle view details
    function viewDetails(id, username) {
    console.log("Viewing details for:", username);
    $j('#modalUsername').text(username);

    $j.ajax({
        url: '{$_url}plugin/pppoe_monitor_router_get_combined_users',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            var user = response.find(function(item) {
                return (item.username && item.username.toString().toLowerCase() === username.toString().toLowerCase());
            });

            if (username !== null && user !== null && user.username !== null) {
                var interfaceValue = '<pppoe-' + user.username + '>';
                $j('#interface').val(interfaceValue);
                $j('#selectedInterface').text(interfaceValue);
                $j('#detailsModal').css('display', 'block');
                createChart();
                createDailyChart(username); // Pass the username to createDailyChart
            } else {
                alert('User not found.');
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            alert('Failed to retrieve user data.');
            console.error('AJAX error:', textStatus, errorThrown);
        }
    });
    }



// Function to handle reconnect
function reconnect(id, username) {
    if (confirm('Are you sure you want to disconnect user ' + username + '?')) {
        $.ajax({
            url: '{$_url}plugin/pppoe_monitor_router_delete_ppp_user/{$router}', // Perbaiki URL AJAX
            method: 'POST',
            data: { id: id, username: username },
            success: function(response) {
                if (response.success) {
                    alert('User ' + username + ' has been disconnected.');
                    setTimeout(function() {
                        table.ajax.reload();
                    }, 2000);
                } else {
                    alert('Failed to disconnect user ' + username + ': ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                alert('Failed to disconnect user ' + username + ': ' + (errorThrown || 'Unknown error'));
                console.error('AJAX error:', textStatus, errorThrown);
            }
        });
    }
}


    // Close modal on click of close button
    $j('.close').click(function() {
        $j('#detailsModal').css('display', 'none');
    });

    // Close modal on click outside the modal
    $j(window).click(function(event) {
        if (event.target == document.getElementById('detailsModal')) {
            $j('#detailsModal').css('display', 'none');
        }
    });

    // Handle advanced search form submission
    $j(document).ready(function() {
        $j('#advancedSearchForm').on('submit', function(e) {
            e.preventDefault(); // Mencegah pengiriman form secara default

            // Mendapatkan nilai dari input
            var username = $j('#searchUsername').val();
            var status = $j('#searchStatus').val();

            // Melakukan pencarian dan menggambar ulang tabel
            table.column(1).search(username).draw(); // Kolom 1 untuk username
            table.column(9).search(status).draw(); // Kolom 9 untuk status
        });

        // Menambahkan ikon search ke dalam tombol
        var searchButton = $j('<button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>');
        $j('#advancedSearchForm').append(searchButton);
    });

});

var chart;
var chartData = {
    txData: [],
    rxData: []
};

function createChart() {
    var options = {
        chart: {
            height: 350,
            type: 'area',
            animations: {
                enabled: true,
                easing: 'linear',
                speed: 200,
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 200
                }
            },
            events: {
                mounted: function() {
                    updateTrafficValues();
                    setInterval(updateTrafficValues, 3000);
                }
            }
        },
        stroke: {
            curve: 'smooth'
        },
        series: [
            { name: 'Download', data: chartData.txData },
            { name: 'Upload', data: chartData.rxData }
        ],
        xaxis: {
            type: 'datetime',
            labels: {
                formatter: function(value) {
                    return new Date(value).toLocaleTimeString();
                }
            }
        },
        yaxis: {
            title: {
                text: 'Trafik Real Time'
            },
            labels: {
                formatter: function(value) {
                    return formatBytes(value);
                }
            }
        },
        tooltip: {
            x: {
                format: 'HH:mm:ss'
            },
            y: {
                formatter: function(value) {
                    return formatBytes(value) + 'ps';
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(value) {
                return formatBytes(value);
            }
        }
    };

    chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
}

var dailyChart; // Declare dailyChart variable globally

function createDailyChart(username) {
    var currentDate = new Date();
    var startOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1).getTime();
    var endOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getTime();

    generateDailyData(username)
        .then(dailyData => {
            var dailyTotals = dailyData.download.map((item, index) => ({
                x: item.x,
                y: item.y + dailyData.upload[index].y
            }));

            if (dailyChart) {
                dailyChart.destroy();
            }

            var options = {
                chart: {
                    height: 350,
                    type: 'bar',
                    animations: {
                        enabled: true,
                        easing: 'linear',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 200
                        }
                    },
                    toolbar: {
                        show: true,
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '15%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                series: [{
                    name: 'Download',
                    data: dailyData.upload
                }, {
                    name: 'Upload',
                    data: dailyData.download
                }, {
                    name: 'Daily Totals',
                    data: dailyTotals
                }],
                xaxis: {
                    type: 'datetime',
                    min: startOfMonth,
                    max: endOfMonth,
                    labels: {
                        formatter: function(value) {
                            return new Date(value).toLocaleDateString();
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Total Usage'
                    },
                    labels: {
                        formatter: function(value) {
                            return formatBytesPerSecond(value);
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return formatBytes(val);
                        }
                    }
                },
                responsive: [
                    {
                        breakpoint: 480,
                        options: {
                            plotOptions: {
                                bar: {
                                    columnWidth: '100%'
                                }
                            }
                        }
                    }
                ]
            };

            dailyChart = new ApexCharts(document.querySelector("#dailyChart"), options);
            dailyChart.render();
        })
        .catch(error => {
            console.error("Failed to fetch daily usage data:", error);
        });
}

// ========================================== NEW FITUR ==========================================//
function generateDailyData(username, startDate, endDate) {
    return new Promise((resolve, reject) => {
        $j.ajax({
            url: '{$_url}plugin/pppoe_monitor_router_daily_data_usage/{$router}',
            data: {
                username: username,
                start_date: startDate,
                end_date: endDate
            },
            dataType: 'json',
            success: function(data) {
                console.log("Raw data from server for username", username, ":", data);

                var dailyData = {
                    download: [],
                    upload: []
                };
                
                // Iterate over dates in data and find the correct user data
                for (var date in data) {
                    var users = data[date].users;
                    
                    // Handle username as number case
                    var userData = users.find(user => user.username === username || user.username == parseInt(username));
                    
                    if (userData) {
                        var rxBytes = convertToBytes(userData.rx);
                        var txBytes = convertToBytes(userData.tx);
                        
                        // Store data in dailyData based on date
                        dailyData.download.push({ x: new Date(date).getTime(), y: rxBytes });
                        dailyData.upload.push({ x: new Date(date).getTime(), y: txBytes });
                    }
                }

                console.log("Filtered daily data for username", username, ":", dailyData);
                resolve(dailyData);
            },
            error: function(xhr, textStatus, errorThrown) {
                console.error("AJAX Error in generateDailyData:", textStatus, errorThrown);
                console.log("Status:", xhr.status);
                console.log("Response Text:", xhr.responseText);
                reject(errorThrown);
            }
        });
    });
}

function convertToBytes(value) {
    let [number, unit] = value.split(' ');
    number = parseFloat(number);
    switch (unit) {
        case 'GB':
            return number * 1024 * 1024 * 1024;
        case 'MB':
            return number * 1024 * 1024;
        case 'KB':
            return number * 1024;
        default:
            return number;
    }
}
// ========================================== NEW FITUR ==========================================//
function formatBytesPerSecond(bytes) {
    if (bytes === 0) {
        return '0 Bps';
    }
    var k = 1024;
    var sizes = ['Bps', 'KBps', 'MBps', 'GBps', 'TBps', 'PBps', 'EBps', 'ZBps', 'YBps'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    var formattedValue = parseFloat((bytes / Math.pow(k, i)).toFixed(2));
    return formattedValue + ' ' + sizes[i];
}

// Fungsi untuk mengubah ukuran dalam byte menjadi format yang lebih mudah dibaca
function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

// Function to update traffic values and icons
function updateTrafficValues() {
    var username = $j('#modalUsername').text().trim();
    var interfaceValue = $j('#interface').val();

    if (!username || !interfaceValue) {
        console.error("Username or interface is undefined or empty.");
        return;
    }

    $j.ajax({
        url: '{$_url}plugin/pppoe_monitor_router_traffic/{$router}',
        dataType: 'json',
        data: { username: username, interface: interfaceValue },
        success: function(data) {
            var timestamp = new Date().getTime();
            var txData = parseInt(data.rows.tx[0]) || 0;
            var rxData = parseInt(data.rows.rx[0]) || 0;

            // Log data tx dan rx untuk debugging
            console.log('txData:', txData, 'rxData:', rxData);

            // Update chart data
            chartData.txData.push({ x: timestamp, y: txData });
            chartData.rxData.push({ x: timestamp, y: rxData });

            var maxDataPoints = 10;
            if (chartData.txData.length > maxDataPoints) {
                chartData.txData.shift();
                chartData.rxData.shift();
            }

            // Update series on the chart
            chart.updateSeries([
                { name: 'Download', data: chartData.txData },
                { name: 'Upload', data: chartData.rxData }
            ]);

            // Find the icon element for the specific user based on username
            var userRow = $j('#ppp-table tbody tr').filter(function() {
                return $j(this).find('td').eq(1).text().trim() === username;
            });

            var iconElement = userRow.find('.traffic-icon');

            // Define thresholds for traffic levels
            var thresholdHigh = 2000; // Adjust these values as needed
            var thresholdMedium = 1500; // Adjust these values as needed

            // Adjust icon color based on traffic levels
            if (txData > thresholdHigh || rxData > thresholdHigh) {
                iconElement.removeClass('traffic-icon-green traffic-icon-yellow').addClass('traffic-icon-red');
            } else if (txData > thresholdMedium || rxData > thresholdMedium) {
                iconElement.removeClass('traffic-icon-green traffic-icon-red').addClass('traffic-icon-yellow');
            } else {
                iconElement.removeClass('traffic-icon-yellow traffic-icon-red').addClass('traffic-icon-green');
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            console.error("Status: " + textStatus);
            console.error("Error: " + errorThrown);
        }
    });
}



// Function to update traffic icons based on table data
function updateTrafficIcons(response) {
    $j('#ppp-table tbody tr').each(function(index) {
        var row = table.row(this).data();
        if (row) {
            var txValue = parseInt(row.tx, 10);
            var rxValue = parseInt(row.rx, 10);
            var iconElement = $j(this).find('.traffic-icon');

            var maxLimit = row.max_limit;
            if (maxLimit === '1M/2M') {
                if (txValue >= 2 * 1024 * 1024 || rxValue >= 2 * 1024 * 1024) {
                    iconElement.removeClass().addClass('traffic-icon traffic-icon-red');
                } else if (txValue >= 1.5 * 1024 * 1024 || rxValue >= 1.5 * 1024 * 1024) {
                    iconElement.removeClass().addClass('traffic-icon traffic-icon-yellow');
                } else {
                    iconElement.removeClass().addClass('traffic-icon traffic-icon-green');
                }
            } else {
                // Default logic for other max limits
                if (txValue >= 2 * 1024 * 1024 || rxValue >= 2 * 1024 * 1024) {
                    iconElement.removeClass().addClass('traffic-icon traffic-icon-red');
                } else if (txValue >= 1.5 * 1024 * 1024 || rxValue >= 1.5 * 1024 * 1024) {
                    iconElement.removeClass().addClass('traffic-icon traffic-icon-yellow');
                } else {
                    iconElement.removeClass().addClass('traffic-icon traffic-icon-green');
                }
            }
        }
    });
}


// Donation Popup
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        document.getElementById('donationPopup').style.display = 'flex';
    }, 1000);
});

document.getElementById('donationPopup').querySelector('.modalsupport-close').addEventListener('click', function() {
    document.getElementById('donationPopup').style.display = 'none';
});

window.addEventListener('click', function(event) {
    if (event.target === document.getElementById('donationPopup')) {
        document.getElementById('donationPopup').style.display = 'none';
    }
});

document.getElementById('donationPopup').querySelector('.donate-button').addEventListener('click', function() {
    window.open('https://buymeacoffee.com/kevindonisaputra', '_blank');
});

</script>



{include file="sections/footer.tpl"}
