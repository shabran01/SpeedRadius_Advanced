{include file="sections/header.tpl"}

<style>
    /* Modern Container Styles */
    .logs-container {
        background: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        margin: 1.5rem;
        overflow: hidden;
    }

    /* Header Styles */
    .section-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        padding: 2rem;
        color: white;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1.5rem;
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

    .header-actions {
        display: flex;
        gap: 1rem;
    }

    .action-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 0.75rem;
        color: white;
        text-decoration: none;
        transition: all 0.2s;
    }

    .action-button:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-1px);
        color: white;
    }

    /* Controls Section */
    .controls-section {
        padding: 1.5rem;
        display: flex;
        gap: 2rem;
        align-items: start;
        flex-wrap: wrap;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .search-box {
        flex: 1;
        min-width: 300px;
    }

    .search-input-group {
        position: relative;
        display: flex;
        align-items: center;
        max-width: 500px;
        width: 100%;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        color: #64748b;
        pointer-events: none;
    }

    .search-input-group input {
        padding: 0.875rem 1rem 0.875rem 2.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
        width: 100%;
        font-size: 1rem;
        transition: all 0.2s ease;
        color: #1f2937;
        background: white;
    }

    .search-input-group input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        outline: none;
    }

    .search-input-group input::placeholder {
        color: #94a3b8;
    }

    .search-button {
        position: absolute;
        right: 0.5rem;
        padding: 0.5rem 1rem;
        background: #4f46e5;
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        cursor: pointer;
    }

    .search-button:hover {
        background: #4338ca;
        transform: translateY(-1px);
    }

    .search-button i {
        font-size: 0.875rem;
    }

    @media (max-width: 640px) {
        .search-input-group {
            max-width: 100%;
        }
        
        .search-button span {
            display: none;
        }
        
        .search-button {
            padding: 0.5rem;
        }
        
        .search-button i {
            margin: 0;
        }
    }

    .search-button:hover {
        background: #4338ca;
    }

    .log-management {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .log-retention-form {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .retention-input-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
    }

    .retention-input-group input {
        width: 70px;
        border: none;
        padding: 0.25rem;
        text-align: center;
    }

    .retention-input-group label {
        color: #64748b;
        font-weight: 500;
        margin: 0;
    }

    .clean-logs-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 0.75rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .clean-logs-button:hover {
        background: #dc2626;
    }

    /* Table Styles */
    .table-container {
        padding: 1.5rem;
        overflow-x: auto;
    }

    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .modern-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem;
        text-align: left;
        border-bottom: 2px solid #e2e8f0;
        position: sticky;
        top: 0;
    }

    .modern-table td {
        padding: 1rem;
        color: #1f2937;
        border-bottom: 1px solid #e2e8f0;
    }

    .log-entry {
        transition: all 0.2s;
    }

    .log-entry:hover {
        background: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    .log-id {
        font-family: monospace;
        color: #6366f1;
        font-weight: 500;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .controls-section {
            flex-direction: column;
        }

        .search-box {
            width: 100%;
        }

        .log-management {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
        }

        .log-retention-form {
            flex-direction: column;
        }

        .retention-input-group {
            width: 100%;
        }
    }
</style>

<div class="logs-container">
    <div class="section-header">
        <div class="header-content">
            <div class="header-left">
                <i class="fa fa-history header-icon"></i>
                <div class="header-text">
                    <h1>SpeedRadius Logs</h1>
                    <p>System activity and events timeline</p>
                </div>
            </div>
            {if in_array($_admin['user_type'],['SuperAdmin','Admin'])}
            <div class="header-actions">
                <a class="action-button csv-export" href="{$_url}logs/list-csv" 
                   onclick="return ask(this, 'This will export to CSV?')">
                    <i class="fa fa-download"></i>
                    <span>Export CSV</span>
                </a>
            </div>
            {/if}
        </div>
    </div>
            <div class="controls-section">
                <div class="search-box">
                    <form id="site-search" method="post" action="{$_url}logs/list/">
                        <div class="search-input-group">
                            <i class="fa fa-search search-icon"></i>
                            <input type="text" name="q" class="form-control" value="{$q}"
                                placeholder="Search in logs...">
                            <button type="submit" class="search-button">
                                <i class="fa fa-search"></i>
                                <span>Search</span>
                            </button>
                        </div>
                        <input type="hidden" name="token" value="{$_token}">
                    </form>
                </div>
                <div class="log-management">
                    <form class="log-retention-form" method="post" action="{$_url}logs/list/">
                        <div class="retention-input-group">
                            <label>{Lang::T('Keep Logs')}</label>
                            <input type="number" name="keep" class="form-control" placeholder="90" value="90" min="1">
                            <span class="days-label">{Lang::T('Days')}</span>
                        </div>
                        <button type="submit" class="clean-logs-button"
                            onclick="return ask(this, 'Clear old logs?')">
                            <i class="fa fa-trash"></i>
                            <span>{Lang::T('Clean Logs')}</span>
                        </button>
                    </form>
                </div>
            </div>
                <div class="table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>IP/Location</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $d as $ds}
                                <tr class="log-entry">
                                    <td class="log-id">{$ds['id']}</td>
                                    <td>{Lang::dateTimeFormat($ds['date'])}</td>
                                    <td>{$ds['type']}</td>
                                    <td>{$ds['ip']}</td>
                                    <td style="overflow-x: scroll;">{nl2br($ds['description'])}</td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                {include file="pagination.tpl"}
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
