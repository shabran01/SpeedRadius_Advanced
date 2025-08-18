{include file="sections/header.tpl"}

<style>
    /* Modern Container Styles */
    .reports-container {
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
        margin-bottom: 2rem;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 2rem;
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

    /* Search Styles */
    .header-search {
        flex: 1;
        max-width: 500px;
    }

    .search-input-group {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        color: #64748b;
        pointer-events: none;
    }

    .search-input-group input {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 2.75rem;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 0.75rem;
        color: white;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .search-input-group input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .search-input-group input:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        outline: none;
    }

    .search-button {
        position: absolute;
        right: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        border-radius: 0.5rem;
        color: white;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }

    .search-button:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* Table Styles */
    .reports-content {
        padding: 0 1.5rem 1.5rem;
    }

    .table-container {
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        white-space: nowrap;
    }

    .modern-table th {
        background: #f8fafc;
        padding: 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #475569;
        text-align: left;
        border-bottom: 2px solid #e2e8f0;
    }

    .modern-table td {
        padding: 1rem;
        color: #1f2937;
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.2s;
    }

    .table-row {
        transition: all 0.2s;
    }

    .table-row:hover {
        background-color: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    /* Cell Styles */
    .invoice-cell {
        cursor: pointer;
    }

    .invoice-number {
        font-family: 'SF Mono', 'Roboto Mono', monospace;
        color: #6366f1;
        font-weight: 500;
    }

    .username-cell {
        cursor: pointer;
    }

    .username-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.75rem;
        background: #f1f5f9;
        border-radius: 9999px;
        color: #475569;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .username-tag:hover {
        background: #e2e8f0;
        color: #1f2937;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .header-content {
            flex-direction: column;
            align-items: stretch;
        }

        .header-search {
            max-width: none;
        }
    }

    @media (max-width: 768px) {
        .modern-table {
            display: block;
            overflow-x: auto;
        }

        .search-button span {
            display: none;
        }
    }

    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .table-row {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>

<div class="reports-container">
    <div class="section-header">
        <div class="header-content">
            <div class="header-left">
                <i class="fa fa-chart-line header-icon"></i>
                <div class="header-text">
                    <h1>Activation Reports</h1>
                    <p>Track user activations and transactions</p>
                </div>
            </div>
            <div class="header-search">
                <form id="site-search" method="post" action="{$_url}reports/activation">
                    <div class="search-input-group">
                        <i class="fa fa-search search-icon"></i>
                        <input type="text" name="q" class="form-control" value="{$q}"
                            placeholder="Search by invoice number...">
                        <button type="submit" class="search-button">
                            <i class="fa fa-search"></i>
                            <span>{Lang::T('Search')}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="reports-content">
                <div class="table-container">
                    <table id="datatable" class="modern-table">
                        <thead>
                            <tr>
                                <th>{Lang::T('Invoice')}</th>
                                <th>{Lang::T('Username')}</th>
                                <th>{Lang::T('Plan Name')}</th>
                                <th>{Lang::T('Plan Price')}</th>
                                <th>{Lang::T('Type')}</th>
                                <th>{Lang::T('Created On')}</th>
                                <th>{Lang::T('Expires On')}</th>
                                <th>{Lang::T('Method')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $activation as $ds}
                                <tr class="table-row">
                                    <td class="invoice-cell" onclick="window.location.href = '{$_url}plan/view/{$ds['id']}'">
                                        <div class="invoice-number">{$ds['invoice']}</div>
                                    </td>
                                    <td class="username-cell" onclick="window.location.href = '{$_url}customers/viewu/{$ds['username']}'">
                                        <div class="username-tag">
                                            <i class="fa fa-user"></i>
                                            <span>{$ds['username']}</span>
                                        </div>
                                    </td>
                                    <td>{$ds['plan_name']}</td>
                                    <td>{Lang::moneyFormat($ds['price'])}</td>
                                    <td>{$ds['type']}</td>
                                    <td class="text-success">
                                        {Lang::dateAndTimeFormat($ds['recharged_on'],$ds['recharged_time'])}
                                    </td>
                                    <td class="text-danger">{Lang::dateAndTimeFormat($ds['expiration'],$ds['time'])}</td>
                                    <td>{$ds['method']}</td>
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
