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

/* ===== Professional polish ===== */
body { background: #f1f5f9; }
.hs-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; box-shadow:0 1px 3px rgba(15,23,42,.06); transition:transform .18s ease, box-shadow .18s ease; }
.hs-card:hover { transform:translateY(-2px); box-shadow:0 12px 24px -8px rgba(15,23,42,.14); }
.hs-avatar { width:36px; height:36px; border-radius:50%; background:#e0e7ff; color:#4338ca; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.78rem; flex-shrink:0; letter-spacing:.02em; }
.hs-badge { display:inline-flex; align-items:center; padding:.22rem .6rem; border-radius:9999px; font-size:.72rem; font-weight:600; white-space:nowrap; }
.hs-badge.green { background:#dcfce7; color:#15803d; }
.hs-badge.amber { background:#fef3c7; color:#b45309; }
.hs-badge.red { background:#fee2e2; color:#b91c1c; }
.hs-badge.gray { background:#f1f5f9; color:#64748b; }
.hs-badge.orphan { background:#fef2f2; color:#b91c1c; border:1px dashed #fca5a5; }
.pulse-dot { width:8px; height:8px; border-radius:50%; background:#34d399; display:inline-block; position:relative; }
.pulse-dot::after { content:''; position:absolute; inset:0; border-radius:50%; background:#34d399; animation:ping 1.6s cubic-bezier(0,0,.2,1) infinite; }
@keyframes ping { 75%,100% { transform:scale(2.4); opacity:0; } }
/* DataTables polish */
.dataTables_wrapper .dataTables_filter { display:none; }
#hotspot_users_table thead th { background-color:#f8fafc; color:#475569; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid #e2e8f0; padding:.75rem 1.75rem .75rem 1rem; }
#hotspot_users_table thead th.sorting, #hotspot_users_table thead th.sorting_asc, #hotspot_users_table thead th.sorting_desc { padding-right:1.75rem !important; }
#hotspot_users_table tbody td { padding:.8rem 1rem; border-bottom:1px solid #f1f5f9; font-size:.875rem; color:#334155; vertical-align:middle; }
#hotspot_users_table tbody tr:hover td { background:#f8fafc; }
.dataTables_wrapper .dataTables_length select, .dataTables_wrapper .dataTables_info { font-size:.8rem; color:#64748b; }
.dataTables_wrapper .dataTables_paginate .paginate_button { border-radius:8px !important; border:1px solid #e2e8f0 !important; background:#fff !important; color:#475569 !important; font-size:.8rem; }
.dataTables_wrapper .dataTables_paginate .paginate_button.current { background:#4f46e5 !important; color:#fff !important; border-color:#4f46e5 !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button:hover { background:#eef2ff !important; border-color:#c7d2fe !important; color:#4f46e5 !important; }
.btn-disconnect { display:inline-flex; align-items:center; gap:.3rem; padding:.32rem .65rem; border:none; border-radius:8px; font-size:.72rem; font-weight:600; color:#dc2626; background:#fee2e2; cursor:pointer; transition:all .15s; }
.btn-disconnect:hover { background:#dc2626; color:#fff; }
.btn-primary-hs { display:inline-flex; align-items:center; gap:.4rem; padding:.55rem .95rem; border:none; border-radius:10px; font-size:.8rem; font-weight:600; color:#fff; background:#4f46e5; cursor:pointer; transition:all .15s; box-shadow:0 1px 2px rgba(79,70,229,.3); }
.btn-primary-hs:hover { background:#4338ca; }
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
        <button onclick="refreshStats()" class="btn-primary-hs">
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
            <h3 style="font-size: 1.25rem; font-weight: 600; color: white; margin: 0;">Hotspot Users <span style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.7rem;font-weight:500;color:#e0e7ff;margin-left:0.5rem;"><span class="pulse-dot"></span> Live</span></h3>
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
              <input type="text" id="usernameSearch" style="display: block; width: 100%; padding: 0.75rem 0.75rem 0.75rem 2.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; background: white; outline: none;" placeholder="Search by Username...">
            </div>
          </div>
          <div style="display: flex; gap: 0.75rem;">
            <button onclick="searchUsers()" style="display: inline-flex; align-items: center; padding: 0.75rem 1.5rem; border: 1px solid #d1d5db; font-size: 1rem; font-weight: 500; border-radius: 0.5rem; color: #374151; background: white; cursor: pointer; transition: all 0.2s;">
              <svg style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              Search
            </button>
            <a href="{$_url}hotspot/add" style="display: inline-flex; align-items: center; padding: 0.75rem 1.5rem; border: none; font-size: 1rem; font-weight: 500; border-radius: 0.5rem; color: white; background: #10b981; text-decoration: none; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
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
                  <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Time Left</th>
                  <th style="padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb;">Total Usage</th>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  var _baseUrl = "{$_url}";
{literal}
  let dataTable;
  let refreshTimer;
  
  function showSpinner() {
    $('#total-users, #total-download, #total-upload, #total-bandwidth').html('<span class="spinner" style="display:inline-block;width:20px;height:20px;border:3px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.6s linear infinite;"></span>');
  }

  function loadHotspotStats() {
    var routerId = $('#routerSelect').val() || 'all';
    showSpinner();
    $.ajax({
      url: _baseUrl + "onlineusers/hotspot_stats/" + routerId,
      method: "GET",
      dataType: "json",
      success: function(data) {
        if (data.error) {
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
      error: function() {
        Swal.fire({ icon: 'error', title: 'Stats Error', text: 'Could not load hotspot statistics. Router may be offline.', toast: true, position: 'top-end', showConfirmButton: false, timer: 4000 });
        $('#total-users, #total-download, #total-upload, #total-bandwidth').text('Error');
      }
    });
  }

  function searchUsers() {
    var query = $('#usernameSearch').val();
    if (dataTable) dataTable.search(query).draw();
  }

  $('#usernameSearch').on('keyup', function(e) { if (e.key === 'Enter') searchUsers(); });

  function refreshStats() {
    loadHotspotStats();
    if (dataTable) {
      var routerId = $('#routerSelect').val() || 'all';
      dataTable.ajax.url(_baseUrl + "onlineusers/hotspot_users/" + routerId).load(function() {});
    }
  }

  function disconnectUser(username, routerId) {
    Swal.fire({
      title: 'Disconnect User?',
      html: 'Are you sure you want to disconnect <strong>' + username + '</strong>?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Yes, disconnect'
    }).then(function(result) {
      if (result.isConfirmed) {
        $.ajax({
          url: _baseUrl + "onlineusers/disconnect/" + routerId + "/" + username + "/hotspot",
          method: "POST",
          data: { router: routerId, username: username, userType: 'hotspot' },
          success: function() {
            Swal.fire({ icon: 'success', title: 'Disconnected', text: username + ' has been disconnected.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            refreshStats();
          },
          error: function() {
            Swal.fire({ icon: 'error', title: 'Failed', text: 'Could not disconnect ' + username });
          }
        });
      }
    });
  }

  $(document).ready(function() {
    loadHotspotStats();

    var routerId = $('#routerSelect').val() || 'all';
    dataTable = $('#hotspot_users_table').DataTable({
      "ajax": { "url": _baseUrl + "onlineusers/hotspot_users/" + routerId, "dataSrc": "" },
      "columns": [
        { "data": "username", "render": function(d, t, row) {
            var nm = row.fullname || d || '?';
            var initials = (nm.match(/\b\w/g) || []).slice(0, 2).join('').toUpperCase() || (d || '?').slice(0, 2).toUpperCase();
            var orphanBadge = row.not_in_db ? '<div style="margin-top:0.2rem;"><span class="hs-badge orphan">Not in DB</span></div>' : '';
            return '<div style="display:flex;align-items:center;gap:0.65rem;">' +
              '<div class="hs-avatar">' + initials + '</div>' +
              '<div style="min-width:0;"><div style="font-weight:600;color:#1e293b;">' + (d || '') + '</div>' +
              (row.fullname ? '<div style="font-size:0.75rem;color:#94a3b8;">' + row.fullname + '</div>' : '') + orphanBadge + '</div></div>';
        }},
        { "data": "router_name", "render": function(d) { return d ? '<span style="padding:0.25rem 0.5rem;background:#f3f4f6;color:#374151;border-radius:0.375rem;font-size:0.75rem;font-weight:500;">' + d + '</span>' : '<span style="color:#9ca3af;">-</span>'; }},
        { "data": "address", "render": function(d) { return '<span style="color:#374151;">' + (d||'') + '</span>'; }},
        { "data": "uptime", "render": function(d) { return '<span style="color:#6b7280;font-family:monospace;">' + (d||'') + '</span>'; }},
        { "data": "server", "render": function(d) { return '<span style="padding:0.25rem 0.5rem;background:#dbeafe;color:#1e40af;border-radius:9999px;font-size:0.75rem;font-weight:500;">' + (d||'') + '</span>'; }},
        { "data": "mac", "render": function(d) { return '<span style="color:#6b7280;font-family:monospace;font-size:0.875rem;">' + (d||'') + '</span>'; }},
        { "data": "time_left", "render": function(d) {
            if (!d || d === '0s' || d === '0') return '<span class="hs-badge gray">\u2014</span>';
            if (d === 'Expired') return '<span class="hs-badge red">Expired</span>';
            var cls = (d.indexOf('d') >= 0) ? 'green' : 'amber';
            return '<span class="hs-badge ' + cls + '">' + d + '</span>';
        }},
        { "data": "total", "render": function(d) { return '<span style="font-weight:600;color:#1f2937;">' + (d||'0 B') + '</span>'; }},
        { "data": null, "orderable": false, "render": function(d) {
            if (!d.username) return '';
            return '<button class="btn-disconnect" onclick="disconnectUser(\'' + d.username + '\',\'' + d.router_id + '\')">Disconnect</button>';
        }}
      ],
      "order": [[0, "asc"]],
      "pageLength": 100,
      "lengthMenu": [[100, 150, 200, 300, -1], [100, 150, 200, 300, "All"]],
      "responsive": true,
      "language": {
        "search": "Search:",
        "lengthMenu": "Show _MENU_ users",
        "info": "Showing _START_ to _END_ of _TOTAL_ users",
        "paginate": { "first": "First", "last": "Last", "next": "Next", "previous": "Previous" },
        "emptyTable": "No hotspot users found",
        "zeroRecords": "No matching users found"
      }
    });

    if ($('#routerSelect').val() !== 'all') dataTable.column(1).visible(false);

    $('#routerSelect').on('change', function() {
      dataTable.column(1).visible($(this).val() === 'all');
      refreshStats();
    });

    refreshTimer = setInterval(refreshStats, 30000);
  });
{/literal}
</script>
<style>{literal}@keyframes spin{to{transform:rotate(360deg)}}{/literal}</style>