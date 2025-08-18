{include file="sections/header.tpl"}

<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- DataTables CSS with custom styling -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<style>
/* Reset and base styles */
* {
  box-sizing: border-box;
}

/* Custom animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeInUp {
  animation: fadeInUp 0.6s ease-out forwards;
}

/* Gradient backgrounds */
.gradient-bg-1 {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.gradient-bg-2 {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.gradient-bg-3 {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.gradient-bg-4 {
  background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

/* Card hover effects */
.stat-card {
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Override DataTables default styling */
.dataTables_wrapper {
  font-family: inherit;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
  padding: 0.5rem 0.75rem !important;
  margin: 0 0.125rem !important;
  border-radius: 0.375rem !important;
  border: 1px solid #e2e8f0 !important;
  background: white !important;
  color: #4a5568 !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
  background: #3b82f6 !important;
  color: white !important;
  border-color: #3b82f6 !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
  background: #f7fafc !important;
  border-color: #cbd5e0 !important;
}

/* Ensure content is visible */
.main-content {
  min-height: 100vh;
  z-index: 1;
  position: relative;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .main-content {
    padding: 1rem 0.5rem !important;
  }
  
  .stat-card {
    padding: 0.75rem !important;
  }
  
  .stat-card p {
    font-size: 1.25rem !important;
  }
  
  .stat-card .text-label {
    font-size: 0.625rem !important;
  }
  
  /* Make cards responsive - 2 columns on tablet */
  .stats-grid {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 0.75rem !important;
  }
}

@media (max-width: 480px) {
  .main-content {
    padding: 0.75rem 0.25rem !important;
  }
  
  .stat-card {
    padding: 0.75rem !important;
  }
  
  .stat-card p {
    font-size: 1.125rem !important;
  }
  
  /* Make cards single column on mobile */
  .stats-grid {
    grid-template-columns: 1fr !important;
    gap: 0.75rem !important;
  }
  
  /* Adjust card layout for mobile */
  .stat-card div {
    flex-direction: row !important;
    align-items: center !important;
    justify-content: space-between !important;
  }
}
</style>

<!-- Main Content Container -->
<div class="main-content" style="background: linear-gradient(135deg, #f0f9ff 0%, #ffffff 50%, #f0fdfa 100%); padding: 2rem 1rem; min-height: 100vh;">
  <!-- Page Header -->
  <div style="max-width: 1280px; margin: 0 auto; margin-bottom: 2rem;">
    <div style="display: flex; flex-direction: column; gap: 1rem;">
      <div style="flex: 1;">
        <h1 style="font-size: 2rem; font-weight: bold; color: #1f2937; margin-bottom: 0.5rem; margin-top: 0;">Online Users Dashboard</h1>
        <p style="font-size: 1.125rem; color: #6b7280; margin: 0;">Monitor and manage your hotspot users in real-time</p>
      </div>
      <div style="margin-top: 1rem;">
        <button onclick="refreshStats()" style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; border: none; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; color: white; background: #3b82f6; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);" onmouseover="this.style.background='#2563eb'; this.style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.1)'" onmouseout="this.style.background='#3b82f6'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'">
          <svg style="width: 1rem; height: 1rem; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Refresh Stats
        </button>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div style="max-width: 1280px; margin: 0 auto; margin-bottom: 2rem;">
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
      <!-- Total Users Card -->
      <div class="stat-card gradient-bg-1 animate-fadeInUp" style="border-radius: 1rem; padding: 1.25rem; color: white; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
          <div>
            <p style="color: rgba(255, 255, 255, 0.8); font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Total Users</p>
            <p style="font-size: 1.75rem; font-weight: bold; margin: 0.375rem 0 0 0;" id="total-users">0</p>
          </div>
          <div style="padding: 0.625rem; background: rgba(255, 255, 255, 0.2); border-radius: 50%;">
            <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- Total Download Card -->
      <div class="stat-card gradient-bg-2 animate-fadeInUp" style="border-radius: 1rem; padding: 1.25rem; color: white; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); animation-delay: 0.1s;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
          <div>
            <p style="color: rgba(255, 255, 255, 0.8); font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Total Download</p>
            <p style="font-size: 1.75rem; font-weight: bold; margin: 0.375rem 0 0 0;" id="total-download">0 B</p>
          </div>
          <div style="padding: 0.625rem; background: rgba(255, 255, 255, 0.2); border-radius: 50%;">
            <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- Total Upload Card -->
      <div class="stat-card gradient-bg-3 animate-fadeInUp" style="border-radius: 1rem; padding: 1.25rem; color: white; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); animation-delay: 0.2s;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
          <div>
            <p style="color: rgba(255, 255, 255, 0.8); font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Total Upload</p>
            <p style="font-size: 1.75rem; font-weight: bold; margin: 0.375rem 0 0 0;" id="total-upload">0 B</p>
          </div>
          <div style="padding: 0.625rem; background: rgba(255, 255, 255, 0.2); border-radius: 50%;">
            <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
          </div>
        </div>
      </div>

      <!-- Total Bandwidth Card -->
      <div class="stat-card gradient-bg-4 animate-fadeInUp" style="border-radius: 1rem; padding: 1.25rem; color: white; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); animation-delay: 0.3s;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
          <div>
            <p style="color: rgba(255, 255, 255, 0.8); font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Total Bandwidth</p>
            <p style="font-size: 1.75rem; font-weight: bold; margin: 0.375rem 0 0 0;" id="total-bandwidth">0 B</p>
          </div>
          <div style="padding: 0.625rem; background: rgba(255, 255, 255, 0.2); border-radius: 50%;">
            <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Main Content Area -->
  <div style="max-width: 1280px; margin: 0 auto;">
    <div style="background: white; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); overflow: hidden;" class="animate-fadeInUp">
      <!-- Panel Header -->
      <div style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); padding: 1.5rem;">
        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <div style="display: flex; align-items: center;">
            <svg style="width: 1.5rem; height: 1.5rem; color: white; margin-right: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path>
            </svg>
            <h3 style="font-size: 1.25rem; font-weight: 600; color: white; margin: 0;">Hotspot Users</h3>
          </div>
          <div>
            <select id="routerSelect" style="background: white; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 1rem; color: #374151; outline: none;">
              <option value="all" selected>All Routers</option>
              {if isset($routers)}
                {foreach $routers as $router}
                  <option value="{$router->id}">
                    {$router->name} ({$router->ip_address})
                  </option>
                {/foreach}
              {else}
                <option value="1">No routers available</option>
              {/if}
            </select>
          </div>
        </div>
      </div>

      <!-- Panel Body -->
      <div style="padding: 1.5rem;">
        <!-- Search and Add User Section -->
        <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
          <div style="flex: 1;">
            <div style="position: relative;">
              <div style="position: absolute; top: 50%; left: 0.75rem; transform: translateY(-50%); pointer-events: none;">
                <svg style="height: 1.25rem; width: 1.25rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              <input type="text" name="username" style="display: block; width: 100%; padding: 0.75rem 0.75rem 0.75rem 2.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background: white; outline: none;" placeholder="Search by Username...">
            </div>
          </div>
          <div style="display: flex; gap: 0.75rem;">
            <button style="display: inline-flex; align-items: center; padding: 0.75rem 1.5rem; border: 1px solid #d1d5db; font-size: 1rem; font-weight: 500; border-radius: 0.5rem; color: #374151; background: white; cursor: pointer; transition: all 0.2s;">
              <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              Search
            </button>
            <a href="{$_url}hotspot_users/add" style="display: inline-flex; align-items: center; padding: 0.75rem 1.5rem; border: none; font-size: 1rem; font-weight: 500; border-radius: 0.5rem; color: white; background: #10b981; text-decoration: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
              <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              New Hotspot User
            </a>
          </div>
        </div>

        <!-- Data Table Container -->
        <div style="background: white; border-radius: 0.5rem; border: 1px solid #e5e7eb; overflow: hidden; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);">
          <div style="overflow-x: auto;">
            <table id="hotspot_users_table" style="min-width: 100%; border-collapse: separate; border-spacing: 0;">
              <thead style="background: #f9fafb;">
                <tr>
                  <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Username</th>
                  <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Router</th>
                  <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Address</th>
                  <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Uptime</th>
                  <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Server</th>
                  <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">MAC Address</th>
                  <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Session Time</th>
                  <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #ef4444; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Upload</th>
                  <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #10b981; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Download</th>
                  <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Total</th>
                  <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Action</th>
                </tr>
              </thead>
              <tbody style="background: white;">
                <!-- DataTables will populate the table body dynamically -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{include file="sections/footer.tpl"}

<!-- Include jQuery and DataTables JS CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
  let dataTable;
  
  function loadHotspotStats() {
    let routerId = $('#routerSelect').val() || 'all';
    let statsUrl = "{$_url}onlineusers/hotspot_stats/" + routerId;
    
    $.ajax({
      url: statsUrl,
      method: "GET",
      dataType: "json",
      success: function(data) {
        console.log('Stats loaded:', data);
        if (data.error) {
          console.error('Error in stats:', data.error);
          $('#total-users').text('N/A');
          $('#total-download').text('N/A');
          $('#total-upload').text('N/A');
          $('#total-bandwidth').text('N/A');
        } else {
          $('#total-users').text(data.total_users || '0');
          $('#total-download').text(data.total_download || '0 B');
          $('#total-upload').text(data.total_upload || '0 B');
          $('#total-bandwidth').text(data.total_bandwidth || '0 B');
        }
      },
      error: function(xhr, status, error) {
        console.error('Error loading hotspot stats:', error);
        $('#total-users').text('Error');
        $('#total-download').text('Error');
        $('#total-upload').text('Error');
        $('#total-bandwidth').text('Error');
      }
    });
  }
  
  function refreshStats() {
    console.log('Refreshing stats...');
    loadHotspotStats();
    if (dataTable) {
      let routerId = $('#routerSelect').val() || 'all';
      dataTable.ajax.url("{$_url}onlineusers/hotspot_users/" + routerId).load();
    }
  }
  
  function disconnectUser(username) {
    if (confirm('Are you sure you want to disconnect user: ' + username + '?')) {
      let routerId = $('#routerSelect').val() || 'all';
      
      $.ajax({
        url: "{$_url}onlineusers/disconnect/" + routerId + "/" + username + "/hotspot",
        method: "POST",
        success: function(response) {
          alert('User ' + username + ' disconnected successfully');
          refreshStats();
        },
        error: function(xhr, status, error) {
          alert('Error disconnecting user: ' + username);
        }
      });
    }
  }
  
  $(document).ready(function() {
    console.log('Document ready, initializing...');
    
    // Load statistics on page load
    loadHotspotStats();
    
    // Initialize DataTable
    let routerId = $('#routerSelect').val() || 'all';
    console.log('Initializing DataTable with router ID:', routerId);
    
    dataTable = $('#hotspot_users_table').DataTable({
      "ajax": {
        "url": "{$_url}onlineusers/hotspot_users/" + routerId,
        "dataSrc": ""
      },
      "columns": [
        { 
          "data": "username", 
          "render": function(data, type, row) {
            return '<span style="font-weight: 600; color: #3b82f6;">' + (data || '') + '</span>';
          }
        },
        { 
          "data": "router_name",
          "render": function(data, type, row) {
            if (data) {
              return '<span style="padding: 0.25rem 0.5rem; background: #f3f4f6; color: #374151; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500;">' + data + '</span>';
            }
            return '<span style="color: #9ca3af;">-</span>';
          }
        },
        { 
          "data": "address",
          "render": function(data, type, row) {
            return '<span style="color: #374151;">' + (data || '') + '</span>';
          }
        },
        { 
          "data": "uptime",
          "render": function(data, type, row) {
            return '<span style="color: #6b7280; font-family: monospace;">' + (data || '') + '</span>';
          }
        },
        { 
          "data": "server",
          "render": function(data, type, row) {
            return '<span style="padding: 0.25rem 0.5rem; background: #dbeafe; color: #1e40af; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;">' + (data || '') + '</span>';
          }
        },
        { 
          "data": "mac",
          "render": function(data, type, row) {
            return '<span style="color: #6b7280; font-family: monospace; font-size: 0.875rem;">' + (data || '') + '</span>';
          }
        },
        { 
          "data": "session_time",
          "render": function(data, type, row) {
            return '<span style="color: #6b7280; font-family: monospace;">' + (data || '') + '</span>';
          }
        },
        { 
          "data": "rx_bytes",
          "render": function(data, type, row) {
            return '<span style="font-weight: 600; color: #ef4444;">' + (data || '0 B') + '</span>';
          }
        },
        { 
          "data": "tx_bytes",
          "render": function(data, type, row) {
            return '<span style="font-weight: 600; color: #10b981;">' + (data || '0 B') + '</span>';
          }
        },
        { 
          "data": "total",
          "render": function(data, type, row) {
            return '<span style="font-weight: 600; color: #1f2937;">' + (data || '0 B') + '</span>';
          }
        },
        { 
          "data": null, 
          "render": function(data, type, row) {
            if (!row.username) return '';
            return '<button onclick="disconnectUser(\'' + row.username + '\')" style="display: inline-flex; align-items: center; justify-content: center; padding: 0.25rem 0.5rem; border: none; font-size: 0.75rem; font-weight: 500; border-radius: 0.25rem; color: white; background: #ef4444; cursor: pointer; transition: all 0.2s; min-width: 32px; height: 28px;" onmouseover="this.style.background=\'#dc2626\'" onmouseout="this.style.background=\'#ef4444\'">' +
                   '<svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                   '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' +
                   '</svg>' +
                   '</button>';
          }
        }
      ],
      "order": [[ 0, "asc" ]],
      "pageLength": 25,
      "responsive": true,
      "language": {
        "search": "Search:",
        "lengthMenu": "Show _MENU_ users",
        "info": "Showing _START_ to _END_ of _TOTAL_ users",
        "paginate": {
          "first": "First",
          "last": "Last",
          "next": "Next",
          "previous": "Previous"
        },
        "emptyTable": "No hotspot users found",
        "zeroRecords": "No matching users found"
      }
    });
    
    // Set initial router column visibility
    const initialRouterValue = $('#routerSelect').val();
    if (initialRouterValue === 'all') {
      dataTable.column(1).visible(true); // Show router column for "All Routers"
    } else {
      dataTable.column(1).visible(false); // Hide router column for specific router
    }
    
    // Handle router selection change
    $('#routerSelect').on('change', function() {
      console.log('Router changed to:', $(this).val());
      const selectedValue = $(this).val();
      
      // Show/hide router column based on selection
      if (selectedValue === 'all') {
        dataTable.column(1).visible(true); // Show router column
      } else {
        dataTable.column(1).visible(false); // Hide router column
      }
      
      refreshStats();
    });
    
    // Auto-refresh every 30 seconds
    setInterval(function() {
      refreshStats();
    }, 30000);
    
    console.log('Initialization complete');
  });
</script>