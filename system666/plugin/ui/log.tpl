{include file="sections/header.tpl"}
    <div class="log-container">
        <div class="section-header">
            <div class="header-content">
                <i class="fa fa-terminal header-icon"></i>
                <div class="header-text">
                    <h1>Mikrotik Logs</h1>
                    <p>Real-time activity and event monitoring</p>
                </div>
            </div>
        </div>
        <div class="router-tabs">
            <form class="form-horizontal" method="post" role="form" action="{$_url}plugin/log_ui">
                <ul class="nav nav-pills"> 
                    {foreach $routers as $r} 
                        <li role="presentation" {if $r['id']==$router}class="active" {/if}>
                            <a href="{$_url}plugin/log_ui/{$r['id']}">
                                <i class="fa fa-router"></i>
                                <span>{$r['name']}</span>
                            </a>
                        </li> 
                    {/foreach} 
                </ul>
            </form>
        </div>

        <div class="controls-wrapper">
            <div class="entries-control">
                <label for="show-entries">Show entries:</label>
                <select id="show-entries" class="form-control">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>
<style>
    :root {
        --primary-color: #4f46e5;
        --primary-hover: #4338ca;
        --success-bg: #dcfce7;
        --success-text: #166534;
        --error-bg: #fee2e2;
        --error-text: #991b1b;
        --warning-bg: #fef3c7;
        --warning-text: #92400e;
        --info-bg: #dbeafe;
        --info-text: #1e40af;
        --border-color: #e5e7eb;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-bg: #ffffff;
    }

    /* Modern Layout & Responsiveness */
    body {
        background-color: #f9fafb;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        color: #1f2937;
    }
    
    /* Modern Header Styles */
    .log-container {
        background: var(--card-bg);
        border-radius: 1rem;
        box-shadow: var(--card-shadow);
        margin-bottom: 2rem;
    }

    .section-header {
        padding: 2rem;
        background: linear-gradient(to right, #4f46e5, #6366f1);
        border-radius: 1rem 1rem 0 0;
        margin-bottom: 1rem;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: white;
    }

    .header-icon {
        font-size: 2rem;
        background: rgba(255, 255, 255, 0.2);
        padding: 1rem;
        border-radius: 1rem;
    }

    .header-text h1 {
        font-size: 1.875rem;
        font-weight: 600;
        margin: 0;
        line-height: 1.2;
    }

    .header-text p {
        margin: 0.5rem 0 0;
        opacity: 0.9;
        font-size: 0.975rem;
    }

    .container {
        max-width: 1200px;
        margin: 2rem auto;
        background-color: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        padding: 1.5rem;
    }

    /* Header Layout */
    .log-header {
        margin-bottom: 2rem;
    }

    .controls-wrapper {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        margin-top: 1.5rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 0.75rem;
    }

    .entries-control {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .entries-control label {
        color: #4b5563;
        font-weight: 500;
        white-space: nowrap;
    }

    .form-control {
        min-width: 100px;
        padding: 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        background: white;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    @media (max-width: 768px) {
        .controls-wrapper {
            flex-direction: column;
            align-items: stretch;
        }

        .search-control {
            max-width: 100%;
        }

        .entries-control {
            justify-content: space-between;
        }
    }

    /* Router Tabs */
    .router-tabs {
        margin-bottom: 2rem;
        overflow-x: auto;
    }

    .nav-pills {
        display: flex;
        gap: 0.5rem;
        padding: 0.5rem;
        list-style: none;
        border-radius: 0.75rem;
        background: #f3f4f6;
    }

    .nav-pills li a {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        color: #4b5563;
        text-decoration: none;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }

    .nav-pills li.active a {
        background: var(--primary-color);
        color: white;
    }
    /* Modern Table Styles */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 1rem 0;
        border-radius: 1rem;
        box-shadow: var(--card-shadow);
        background: var(--card-bg);
    }

    .table {
        width: 100%;
        margin-bottom: 0;
        background-color: #fff;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table th {
        position: sticky;
        top: 0;
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1.25rem 1rem;
        text-align: left;
        border-bottom: 2px solid #e2e8f0;
    }

    .table td {
        padding: 1rem;
        color: #1f2937;
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    .table tbody tr:hover {
        background-color: #f8fafc;
    }

    /* Table Responsiveness */
    @media (max-width: 768px) {
        .table {
            display: block;
        }
        
        .table th {
            display: none;
        }
        
        .table td {
            display: block;
            padding: 0.5rem 1rem;
        }
        
        .table td:before {
            content: attr(data-label);
            float: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
        }
    }

    /* Modern Form Controls */
    .form-control {
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.25rem;
        margin-top: 1.5rem;
    }

    .pagination .page-item .page-link {
        border: none;
        padding: 0.5rem 1rem;
        color: #4b5563;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }

    .pagination .page-item .page-link:hover {
        background-color: #f3f4f6;
        color: var(--primary-color);
    }

    .pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        color: white;
    }
    /* Modern Status Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.875rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        border-radius: 9999px;
        letter-spacing: 0.025em;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
    }

    .badge::before {
        content: '';
        display: inline-block;
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        margin-right: 0.5rem;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(0, 0, 0, 0.2);
        }
        70% {
            box-shadow: 0 0 0 4px rgba(0, 0, 0, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(0, 0, 0, 0);
        }
    }

    .badge-success {
        color: var(--success-text);
        background-color: var(--success-bg);
    }

    .badge-success::before {
        background-color: var(--success-text);
    }

    .badge-danger {
        color: var(--error-text);
        background-color: var(--error-bg);
    }

    .badge-danger::before {
        background-color: var(--error-text);
    }

    .badge-warning {
        color: var(--warning-text);
        background-color: var(--warning-bg);
    }

    .badge-warning::before {
        background-color: var(--warning-text);
    }

    .badge-info {
        color: var(--info-text);
        background-color: var(--info-bg);
    }

    .badge-info::before {
        background-color: var(--info-text);
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .table tbody tr {
        animation: fadeIn 0.3s ease-out forwards;
    }

    /* Loading State */
    .loading {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 200px;
    }

    .loading::after {
        content: '';
        width: 2rem;
        height: 2rem;
        border: 2px solid #e2e8f0;
        border-top-color: var(--primary-color);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive Utilities */
    @media (max-width: 640px) {
        .container {
            margin: 1rem;
            padding: 1rem;
        }

        .badge {
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
        }
    }
    </style>
</style>

<div id="logsys-mikrotik" class="container">
    <div class="row">
        <div class="col-sm-4 col-md-10">
            <div class="dataTables_length" id="data_length">
                <label>Show entries
                    <select name="data_length" aria-controls="data" class="custom-select custom-select-sm form-control form-control-sm" onchange="updatePerPage(this.value)">
                        <option value="5" {if $per_page == 5}selected{/if}>5</option>
                        <option value="10" {if $per_page == 10}selected{/if}>10</option>
                        <option value="25" {if $per_page == 25}selected{/if}>25</option>
                        <option value="50" {if $per_page == 50}selected{/if}>50</option>
                        <option value="100" {if $per_page == 100}selected{/if}>100</option>
                    </select>
                </label>
            </div>
        </div>
        <div class="col-sm-2 col-md-2">
            <div id="data_filter" class="dataTables_filter">
                <label>Search:<input type="search" id="logSearch" class="form-control form-control-sm" placeholder="" aria-controls="data" onkeyup="filterLogs()"></label>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Time</th>
                <th>Topic</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody id="logTableBody">
            {assign var=current_page value=$smarty.get.page|default:1}
            {assign var=per_page value=$smarty.get.per_page|default:10}
            {assign var=start_index value=($current_page - 1) * $per_page}
            
            {foreach from=$logs|array_reverse item=log name=logLoop}
                {if $smarty.foreach.logLoop.index >= $start_index && $smarty.foreach.logLoop.index < ($start_index + $per_page)}
                    <tr class="log-entry">
                        <td>{$log.time}</td>
                        <td>{$log.topics}</td>
                        <td class="log-message">
                            {if $log.message|lower|strpos:'failed' !== false}
                                <span class="badge badge-danger">Error</span>
                            {elseif $log.message|lower|strpos:'trying' !== false}
                                <span class="badge badge-warning">Warning</span>
                            {elseif $log.message|lower|strpos:'logged in' !== false}
                                <span class="badge badge-success">Success</span>
                            {elseif $log.message|lower|strpos:'login failed' !== false}
                                <span class="badge badge-info">Login Info</span>
                            {else}
                                <span class="badge badge-info">Info</span>
                            {/if}
                            {$log.message}
                        </td>
                    </tr>
                {/if}
            {/foreach}

        </tbody>
    </table>

    {assign var=total_logs value=$logs|@count}
    {assign var=last_page value=ceil($total_logs / $per_page)}

    <nav aria-label="Page navigation">
        <div class="pagination-container">
            <ul class="pagination">
                {if $current_page > 1}
                    <li class="page-item">
                        <a class="page-link" href="index.php?_route=plugin/log_ui&page=1&per_page={$per_page}" aria-label="First">
                            <span aria-hidden="true">&laquo;&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="index.php?_route=plugin/log_ui&page={$current_page-1}&per_page={$per_page}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                            <span class="sr-only">Previous</span>
                        </a>
                    </li>
                {/if}

                {assign var=max_links value=5}

                {assign var=start_page value=max(1, $current_page - floor($max_links / 2))}
                {assign var=end_page value=min($last_page, $start_page + $max_links - 1)}

                {if $start_page > 1}
                    <li class="page-item">
                        <a class="page-link" href="index.php?_route=plugin/log_ui&page={$start_page-1}&per_page={$per_page}" aria-label="Previous">
                            <span aria-hidden="true">&hellip;</span>
                        </a>
                    </li>
                {/if}

                {foreach from=range($start_page, $end_page) item=page}
                    <li class="page-item {if $page == $current_page}active{/if}">
                        <a class="page-link" href="index.php?_route=plugin/log_ui&page={$page}&per_page={$per_page}">
                            {$page}
                        </a>
                    </li>
                {/foreach}

                {if $end_page < $last_page}
                    <li class="page-item">
                        <a class="page-link" href="index.php?_route=plugin/log_ui&page={$end_page+1}&per_page={$per_page}" aria-label="Next">
                            <span aria-hidden="true">&hellip;</span>
                        </a>
                    </li>
                {/if}

                {if $current_page < $last_page}
                    <li class="page-item">
                        <a class="page-link" href="index.php?_route=plugin/log_ui&page={$current_page+1}&per_page={$per_page}" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                            <span class="sr-only">Next</span>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="index.php?_route=plugin/log_ui&page={$last_page}&per_page={$per_page}" aria-label="Last">
                            <span aria-hidden="true">&raquo;&raquo;</span>
                        </a>
                    </li>
                {/if}
            </ul>
        </div>
    </nav>
</div>

<script>
  window.addEventListener('DOMContentLoaded', function() {
    var portalLink = "https://github.com/kevindoni";
    $('#version').html('Log Mikrotik | Ver: 1.0 | by: <a href="' + portalLink + '">Kevin Doni</a>');
  });

  function updatePerPage(value) {
    var urlParams = new URLSearchParams(window.location.search);
    urlParams.set('per_page', value);
    urlParams.set('page', 1); // Reset to first page
    window.location.search = urlParams.toString();
  }

  function filterLogs() {
    var input = document.getElementById('logSearch').value.toLowerCase();
    var table = document.getElementById('logTableBody');
    var tr = table.getElementsByClassName('log-entry');

    for (var i = 0; i < tr.length; i++) {
      var logMessage = tr[i].getElementsByClassName('log-message')[0].textContent || tr[i].getElementsByClassName('log-message')[0].innerText;
      if (logMessage.toLowerCase().indexOf(input) > -1) {
        tr[i].style.display = '';
      } else {
        tr[i].style.display = 'none';
      }
    }
  }
</script>

{include file="sections/footer.tpl"}
