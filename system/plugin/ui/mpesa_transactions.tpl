{include file="sections/header.tpl"}
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white border-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-mobile-alt me-3 header-icon"></i>
                        <h4 class="mb-0 fw-600 header-title">Mpesa Transactions</h4>
                    </div>
                </div>
                <style>
                    /* Modern Typography */
                    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
                    
                    * {
                        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    }

                    /* Clean Card Design */
                    .card {
                        border-radius: 16px;
                        overflow: hidden;
                        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08) !important;
                        border: 1px solid rgba(0, 0, 0, 0.05) !important;
                    }

                    .bg-gradient-primary {
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    }

                    .card-header {
                        padding: 2rem 2.5rem;
                        border-bottom: none;
                    }

                    .card-header h4 {
                        font-size: 1.5rem;
                        font-weight: 600;
                        letter-spacing: -0.025em;
                    }

                    /* Enhanced Header Styling */
                    .header-title {
                        color: #ffffff !important;
                        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
                        font-weight: 700;
                        letter-spacing: -0.01em;
                    }

                    .header-icon {
                        font-size: 1.5rem;
                        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
                    }

                    /* Search Section */
                    .search-section {
                        background: #f8fafc;
                        padding: 2rem 2.5rem;
                        border-bottom: 1px solid #e2e8f0;
                    }

                    .form-control-modern {
                        border: 2px solid #e2e8f0;
                        border-radius: 12px;
                        padding: 0.875rem 1.25rem;
                        font-size: 0.95rem;
                        font-weight: 400;
                        transition: all 0.2s ease;
                        background: white;
                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                    }

                    .form-control-modern:focus {
                        border-color: #667eea;
                        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
                        outline: none;
                    }

                    .form-control-modern::placeholder {
                        color: #94a3b8;
                        font-weight: 400;
                    }

                    /* Modern Buttons - Compact Size */
                    .btn-modern {
                        padding: 0.5rem 1rem;
                        border-radius: 8px;
                        font-weight: 500;
                        font-size: 0.875rem;
                        border: none;
                        text-decoration: none;
                        display: inline-flex;
                        align-items: center;
                        gap: 0.5rem;
                        transition: all 0.2s ease;
                        white-space: nowrap;
                        letter-spacing: -0.01em;
                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                        min-height: 38px;
                    }

                    .btn-modern:hover {
                        transform: translateY(-1px);
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                        text-decoration: none;
                    }

                    .btn-modern:active {
                        transform: translateY(0);
                    }

                    .btn-primary-modern {
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                    }

                    .btn-primary-modern:hover {
                        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
                        color: white;
                    }

                    .btn-secondary-modern {
                        background: white;
                        color: #64748b;
                        border: 2px solid #e2e8f0;
                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                    }

                    .btn-secondary-modern:hover {
                        background: #f8fafc;
                        border-color: #cbd5e1;
                        color: #475569;
                    }

                    .btn-success-modern {
                        background: linear-gradient(135deg, #10b981 0%, #047857 100%);
                        color: white;
                    }

                    .btn-success-modern:hover {
                        background: linear-gradient(135deg, #059669 0%, #065f46 100%);
                        color: white;
                    }

                    /* Clean Table */
                    .table-container {
                        background: white;
                        margin: 0;
                        overflow: hidden;
                    }

                    .table-modern {
                        margin: 0;
                        font-size: 0.95rem;
                        border: none;
                        background: white;
                    }

                    .table-modern thead th {
                        background: #f8fafc;
                        border: none;
                        border-bottom: 2px solid #e2e8f0;
                        color: #475569;
                        font-weight: 600;
                        font-size: 0.875rem;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        padding: 1.25rem 1.5rem;
                        white-space: nowrap;
                        position: sticky;
                        top: 0;
                        z-index: 10;
                    }

                    .table-modern tbody td {
                        padding: 1.25rem 1.5rem;
                        border: none;
                        border-bottom: 1px solid #f1f5f9;
                        vertical-align: middle;
                        font-weight: 400;
                        line-height: 1.5;
                    }

                    .table-modern tbody tr {
                        transition: all 0.15s ease;
                    }

                    .table-modern tbody tr:hover {
                        background: #f8fafc;
                    }

                    .table-modern tbody tr:last-child td {
                        border-bottom: none;
                    }

                    /* Typography in table */
                    .customer-name {
                        font-weight: 500;
                        color: #1e293b;
                        font-size: 0.875rem;
                    }

                    .amount-text {
                        font-weight: 600;
                        color: #059669;
                        font-size: 0.9rem;
                    }

                    .phone-text {
                        font-family: 'Monaco', 'Menlo', monospace;
                        color: #64748b;
                        font-size: 0.9rem;
                        background: #f1f5f9;
                        padding: 0.375rem 0.75rem;
                        border-radius: 8px;
                        display: inline-block;
                    }

                    .account-code {
                        font-family: 'Monaco', 'Menlo', monospace;
                        background: #e0e7ff;
                        color: #3730a3;
                        padding: 0.375rem 0.75rem;
                        border-radius: 8px;
                        font-size: 0.875rem;
                        font-weight: 500;
                    }

                    .transaction-id {
                        font-family: 'Monaco', 'Menlo', monospace;
                        color: #64748b;
                        font-size: 0.875rem;
                    }

                    .status-badge {
                        padding: 0.5rem 1rem;
                        border-radius: 20px;
                        font-size: 0.8rem;
                        font-weight: 600;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        background: #dcfce7;
                        color: #166534;
                    }

                    .date-text {
                        color: #475569;
                        font-size: 0.9rem;
                        line-height: 1.4;
                    }

                    .date-time {
                        color: #94a3b8;
                        font-size: 0.85rem;
                    }

                    .short-code-badge {
                        background: #f1f5f9;
                        color: #475569;
                        padding: 0.375rem 0.875rem;
                        border-radius: 12px;
                        font-size: 0.85rem;
                        font-weight: 500;
                    }

                    /* Button Group Styling */
                    .button-group {
                        align-items: center;
                    }

                    .button-group .btn-modern {
                        flex-shrink: 0;
                    }

                    /* Responsive Design */
                    @media (max-width: 992px) {
                        .container-fluid {
                            padding-left: 1rem;
                            padding-right: 1rem;
                        }
                        
                        .card-header {
                            padding: 1.5rem 1.5rem;
                        }
                        
                        .search-section {
                            padding: 1.5rem 1.5rem;
                        }
                        
                        .table-modern thead th,
                        .table-modern tbody td {
                            padding: 1rem 0.75rem;
                        }
                    }

                    @media (max-width: 768px) {
                        .card {
                            border-radius: 12px;
                        }
                        
                        .card-header h4 {
                            font-size: 1.25rem;
                        }
                        
                        .form-control-modern {
                            padding: 0.75rem 1rem;
                            font-size: 0.9rem;
                        }
                        
                        .btn-modern {
                            padding: 0.625rem 1.125rem;
                            font-size: 0.875rem;
                        }
                        
                        .table-modern {
                            font-size: 0.9rem;
                        }
                        
                        .mobile-hide {
                            display: none;
                        }
                        
                        .mobile-stack {
                            display: block;
                            font-size: 0.85rem;
                            line-height: 1.5;
                            color: #64748b;
                            margin-top: 0.5rem;
                        }
                        
                        .mobile-stack strong {
                            color: #475569;
                            font-weight: 600;
                        }
                    }

                    @media (max-width: 576px) {
                        .container-fluid {
                            padding-left: 0.75rem;
                            padding-right: 0.75rem;
                        }
                        
                        .card-header {
                            padding: 1.25rem 1.25rem;
                        }
                        
                        .search-section {
                            padding: 1.25rem 1.25rem;
                        }
                        
                        .table-modern thead th,
                        .table-modern tbody td {
                            padding: 0.875rem 0.5rem;
                        }
                        
                        .btn-modern {
                            width: 100%;
                            justify-content: center;
                            margin-bottom: 0.5rem;
                            padding: 0.5rem 1rem;
                            font-size: 0.875rem;
                        }
                    }

                    /* Responsive Table */
                    .table-responsive {
                        border-radius: 0;
                        border: none;
                        box-shadow: none;
                        overflow-x: hidden;
                        width: 100%;
                    }

                    /* Mobile responsive table - stack columns vertically */
                    @media (max-width: 768px) {
                        .table-responsive {
                            overflow-x: hidden;
                        }
                        
                        .table-modern thead {
                            display: none;
                        }
                        
                        .table-modern tbody tr {
                            display: block;
                            border: 1px solid #e2e8f0;
                            border-radius: 8px;
                            margin-bottom: 1rem;
                            padding: 1rem;
                            background: white;
                        }
                        
                        .table-modern tbody td {
                            display: block;
                            text-align: left !important;
                            border: none;
                            padding: 0.5rem 0;
                            border-bottom: 1px solid #f1f5f9;
                        }
                        
                        .table-modern tbody td:last-child {
                            border-bottom: none;
                        }
                        
                        .table-modern tbody td:before {
                            content: attr(data-label) ": ";
                            font-weight: 600;
                            color: #64748b;
                            display: inline-block;
                            width: 100px;
                            font-size: 0.875rem;
                        }
                    }

                    /* Loading states */
                    .loading-overlay {
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background: rgba(255, 255, 255, 0.9);
                        display: none;
                        align-items: center;
                        justify-content: center;
                        z-index: 1000;
                    }

                    .mobile-stack {
                        display: none;
                    }
                </style>

                <div class="search-section">
                    <form class="row g-3" method="GET" action="">
                        <input type="hidden" name="_route" value="plugin/mpesa_transactions">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control form-control-modern" placeholder="Search transactions..." value="{$search}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_from" class="form-control form-control-modern" value="{$date_from}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_to" class="form-control form-control-modern" value="{$date_to}">
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex gap-2 flex-wrap button-group">
                                <button type="submit" class="btn btn-modern btn-primary-modern">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <a href="?_route=plugin/mpesa_transactions" class="btn btn-modern btn-secondary-modern">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                                <a href="?_route=plugin/mpesa_transactions&export=pdf{if $search}&search={$search}{/if}{if $date_from}&date_from={$date_from}{/if}{if $date_to}&date_to={$date_to}{/if}" class="btn btn-modern btn-success-modern" target="_blank">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th class="mobile-hide">Phone</th>
                                    <th>Amount</th>
                                    <th class="mobile-hide">Account</th>
                                    <th class="mobile-hide">Balance</th>
                                    <th class="mobile-hide">Transaction ID</th>
                                    <th class="mobile-hide">Type</th>
                                    <th>Date</th>
                                    <th class="mobile-hide">Short Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach $t as $key => $ts}
                                <tr>
                                    <td data-label="#"><span class="fw-500">{($current_page - 1) * 10 + $key + 1}</span></td>
                                    <td data-label="Customer">
                                        <div class="customer-name">{$ts['FirstName']}</div>
                                        <div class="mobile-stack">
                                            <strong>Phone:</strong> {if $ts['MSISDN']}{$ts['MSISDN']|truncate:15:"..."}{else}N/A{/if}<br>
                                            <strong>Account:</strong> {$ts['BillRefNumber']}<br>
                                            <strong>ID:</strong> {$ts['TransID']|truncate:10:"..."}
                                        </div>
                                    </td>
                                    <td data-label="Phone" class="mobile-hide">
                                        {if $ts['MSISDN']}
                                            <span class="phone-text">{$ts['MSISDN']|truncate:20:"..."}</span>
                                        {else}
                                            <span class="text-muted small">No MSISDN</span>
                                        {/if}
                                    </td>
                                    <td data-label="Amount">
                                        <span class="amount-text">KSh {$ts['TransAmount']|number_format:2}</span>
                                    </td>
                                    <td data-label="Account" class="mobile-hide">
                                        <span class="account-code">{$ts['BillRefNumber']}</span>
                                    </td>
                                    <td data-label="Balance" class="mobile-hide">
                                        <span class="text-muted">KSh {$ts['OrgAccountBalance']|number_format:2}</span>
                                    </td>
                                    <td data-label="Transaction ID" class="mobile-hide">
                                        <span class="transaction-id">{$ts['TransID']}</span>
                                    </td>
                                    <td data-label="Type" class="mobile-hide">
                                        <span class="status-badge">{$ts['TransactionType']}</span>
                                    </td>
                                    <td data-label="Date">
                                        <div class="date-text">
                                            {$ts['TransTime']|date_format:"%b %d, %Y"}
                                            <div class="date-time">{$ts['TransTime']|date_format:"%H:%M"}</div>
                                        </div>
                                    </td>
                                    <td data-label="Short Code" class="mobile-hide">
                                        <span class="short-code-badge">{$ts['BusinessShortCode']}</span>
                                    </td>
                                </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light border-0">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <nav aria-label="Transaction pagination">
                                <ul class="pagination pagination-modern mb-0">
                                    <li class="page-item {if $current_page == 1}disabled{/if}">
                                        <a class="page-link" href="{if $current_page > 1}?_route=plugin/mpesa_transactions&page={$current_page-1}{if $search}&search={$search}{/if}{if $date_from}&date_from={$date_from}{/if}{if $date_to}&date_to={$date_to}{/if}{else}#{/if}">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>

                                    {assign var=start value=max(1, $current_page-2)}
                                    {assign var=end value=min($total_pages, $start+4)}
                                    {if $end - $start < 4}
                                        {assign var=start value=max(1, $end-4)}
                                    {/if}

                                    {if $start > 1}
                                        <li class="page-item">
                                            <a class="page-link" href="?_route=plugin/mpesa_transactions&page=1{if $search}&search={$search}{/if}{if $date_from}&date_from={$date_from}{/if}{if $date_to}&date_to={$date_to}{/if}">1</a>
                                        </li>
                                        {if $start > 2}<li class="page-item disabled"><span class="page-link">...</span></li>{/if}
                                    {/if}

                                    {for $i=$start to $end}
                                        <li class="page-item{if $current_page == $i} active{/if}">
                                            <a class="page-link" href="?_route=plugin/mpesa_transactions&page={$i}{if $search}&search={$search}{/if}{if $date_from}&date_from={$date_from}{/if}{if $date_to}&date_to={$date_to}{/if}">{$i}</a>
                                        </li>
                                    {/for}

                                    {if $end < $total_pages}
                                        {if $end < $total_pages-1}<li class="page-item disabled"><span class="page-link">...</span></li>{/if}
                                        <li class="page-item">
                                            <a class="page-link" href="?_route=plugin/mpesa_transactions&page={$total_pages}{if $search}&search={$search}{/if}{if $date_from}&date_from={$date_from}{/if}{if $date_to}&date_to={$date_to}{/if}">{$total_pages}</a>
                                        </li>
                                    {/if}

                                    <li class="page-item {if $current_page == $total_pages}disabled{/if}">
                                        <a class="page-link" href="{if $current_page < $total_pages}?_route=plugin/mpesa_transactions&page={$current_page+1}{if $search}&search={$search}{/if}{if $date_from}&date_from={$date_from}{/if}{if $date_to}&date_to={$date_to}{/if}{else}#{/if}">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        <div class="col-md-6 text-md-end mt-2 mt-md-0">
                            <span class="text-muted pagination-info">
                                Showing {($current_page - 1) * 10 + 1} to {min($current_page * 10, $total_items)} of {$total_items} transactions
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-4 bg-gradient-info rounded">
                        <div class="d-flex align-items-center">
                            <div class="info-icon me-3">
                                <i class="fas fa-info-circle text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-600">Transaction Management</h6>
                                <p class="mb-0 text-muted small">All Mpesa transactions are automatically synchronized and verified in real-time</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modern Pagination */
    .pagination-modern .page-link {
        padding: 0.625rem 1rem;
        font-size: 0.95rem;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        color: #64748b;
        transition: all 0.2s ease;
        margin: 0 2px;
        font-weight: 500;
    }

    .pagination-modern .page-link:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1;
        color: #475569;
        transform: translateY(-1px);
    }

    .pagination-modern .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    .pagination-modern .page-item.disabled .page-link {
        color: #cbd5e1;
        background-color: #f8fafc;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }

    .pagination-info {
        font-size: 0.95rem;
        font-weight: 500;
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);
        border: 1px solid #e0f7fa;
    }

    .info-icon {
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .pagination-modern {
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .pagination-modern .page-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
            margin: 1px;
        }
        
        .pagination-info {
            text-align: center !important;
            font-size: 0.875rem;
        }
        
        .card-footer .row > div {
            text-align: center !important;
        }
    }

    @media (max-width: 576px) {
        .pagination-modern .page-link {
            padding: 0.375rem 0.625rem;
            font-size: 0.85rem;
        }
    }
</style>

{include file="sections/footer.tpl"}
