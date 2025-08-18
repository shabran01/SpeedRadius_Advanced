{include file="sections/header.tpl"}

<style>
.modern-overdue-container {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 20px 0;
}

.overdue-header {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
    position: relative;
    overflow: hidden;
}

.overdue-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
}

.overdue-header h1 {
    font-size: 20px;
    font-weight: 300;
    margin: 0 0 8px 0;
    position: relative;
    z-index: 2;
}

.overdue-header p {
    font-size: 14px;
    opacity: 0.9;
    margin: 0;
    position: relative;
    z-index: 2;
}

.overdue-icon {
    position: absolute;
    right: 30px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 40px;
    opacity: 0.2;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border-left: 4px solid #ff6b6b;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

.stat-number {
    font-size: 24px;
    font-weight: 700;
    color: #ff6b6b;
    margin-bottom: 5px;
}

.stat-label {
    color: #6c757d;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.modern-table-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
}

.table-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 25px;
    border-bottom: none;
}

.table-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.table-body {
    padding: 25px;
}

.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.modern-table thead th {
    background: #f8f9fa;
    color: #495057;
    font-weight: 600;
    padding: 12px 15px;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e9ecef;
    position: relative;
}

.modern-table tbody td {
    padding: 12px 15px;
    border-bottom: 1px solid #f8f9fa;
    vertical-align: middle;
    font-size: 13px;
}

.modern-table tbody tr:hover {
    background: #f8f9fa;
}

.days-badge {
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.days-critical {
    background: linear-gradient(135deg, #ff6b6b, #ee5a52);
    color: white;
}

.days-warning {
    background: linear-gradient(135deg, #ffa726, #ff9800);
    color: white;
}

.days-caution {
    background: linear-gradient(135deg, #ffeb3b, #ffc107);
    color: #333;
}

.btn-modern {
    padding: 6px 12px;
    border-radius: 15px;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 10px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.btn-view {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.btn-view:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    color: white;
    text-decoration: none;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state-icon {
    font-size: 60px;
    color: #dee2e6;
    margin-bottom: 15px;
}

.empty-state h3 {
    color: #495057;
    margin-bottom: 10px;
}

.empty-state p {
    color: #6c757d;
    font-size: 16px;
}

@media (max-width: 768px) {
    .overdue-header {
        padding: 15px;
        text-align: center;
    }
    
    .overdue-header h1 {
        font-size: 18px;
    }
    
    .overdue-header p {
        font-size: 13px;
    }
    
    .overdue-icon {
        position: relative;
        right: auto;
        top: auto;
        transform: none;
        display: block;
        margin: 15px auto 0;
        font-size: 30px;
    }
    
    .stats-cards {
        grid-template-columns: 1fr;
    }
    
    .table-body {
        padding: 15px;
    }
    
    .modern-table {
        font-size: 12px;
    }
    
    .modern-table thead th,
    .modern-table tbody td {
        padding: 8px 10px;
    }
}
</style>

<div class="modern-overdue-container">
    <div class="row">
        <div class="col-sm-12">
            <!-- Header Section -->
            <div class="overdue-header">
                <i class="fa fa-clock-o overdue-icon"></i>
                <h1><i class="fa fa-exclamation-triangle"></i> Overdue Alert System</h1>
                <p>Customers expiring in the next 5 days - Take action to prevent service interruption</p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-number">{count($d)}</div>
                    <div class="stat-label">Total Overdue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">
                        {assign var="critical" value=0}
                        {foreach $d as $ds}
                            {assign var="days" value=ceil((strtotime($ds.expiration) - time()) / 86400)}
                            {if $days <= 2}{assign var="critical" value=$critical+1}{/if}
                        {/foreach}
                        {$critical}
                    </div>
                    <div class="stat-label">Critical (≤2 days)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">
                        {assign var="warning" value=0}
                        {foreach $d as $ds}
                            {assign var="days" value=ceil((strtotime($ds.expiration) - time()) / 86400)}
                            {if $days >= 3 && $days <= 4}{assign var="warning" value=$warning+1}{/if}
                        {/foreach}
                        {$warning}
                    </div>
                    <div class="stat-label">Warning (3-4 days)</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">
                        {assign var="caution" value=0}
                        {foreach $d as $ds}
                            {assign var="days" value=ceil((strtotime($ds.expiration) - time()) / 86400)}
                            {if $days == 5}{assign var="caution" value=$caution+1}{/if}
                        {/foreach}
                        {$caution}
                    </div>
                    <div class="stat-label">Caution (5 days)</div>
                </div>
            </div>

            <!-- Main Table -->
            <div class="modern-table-container">
                <div class="table-header">
                    <h3><i class="fa fa-users"></i> Customer Overdue List</h3>
                </div>
                <div class="table-body">
                    {if count($d) > 0}
                        <div class="table-responsive">
                            <table id="overdue_table" class="modern-table">
                                <thead>
                                    <tr>
                                        <th><i class="fa fa-user"></i> Customer</th>
                                        <th><i class="fa fa-tag"></i> Username</th>
                                        <th><i class="fa fa-wifi"></i> Plan</th>
                                        <th><i class="fa fa-phone"></i> Contact</th>
                                        <th><i class="fa fa-calendar"></i> Expires</th>
                                        <th><i class="fa fa-clock-o"></i> Days Left</th>
                                        <th><i class="fa fa-cogs"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $d as $ds}
                                        {assign var="days_left" value=ceil((strtotime($ds.expiration) - time()) / 86400)}
                                        <tr>
                                            <td>
                                                <div style="font-weight: 600; color: #495057; font-size: 13px;">{$ds.fullname}</div>
                                                {if $ds.email}
                                                    <div style="font-size: 11px; color: #6c757d; margin-top: 2px;">
                                                        <i class="fa fa-envelope"></i> {$ds.email}
                                                    </div>
                                                {/if}
                                            </td>
                                            <td>
                                                <code style="background: #f8f9fa; padding: 3px 6px; border-radius: 4px; color: #495057; font-size: 11px;">
                                                    {$ds.username}
                                                </code>
                                            </td>
                                            <td>
                                                <span style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 3px 8px; border-radius: 12px; font-size: 10px; font-weight: 600;">
                                                    {$ds.plan_name}
                                                </span>
                                            </td>
                                            <td>
                                                {if $ds.phonenumber}
                                                    <a href="tel:{$ds.phonenumber}" style="color: #28a745; text-decoration: none; font-size: 12px;">
                                                        <i class="fa fa-phone"></i> {$ds.phonenumber}
                                                    </a>
                                                {else}
                                                    <span style="color: #6c757d; font-size: 12px;">No phone</span>
                                                {/if}
                                            </td>
                                            <td>
                                                <div style="font-weight: 600; color: #495057; font-size: 12px;">
                                                    {$ds.expiration|date_format:"%d %b %Y"}
                                                </div>
                                                <div style="font-size: 10px; color: #6c757d;">
                                                    {$ds.expiration|date_format:"%H:%M"}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="days-badge {if $days_left <= 2}days-critical{elseif $days_left <= 4}days-warning{else}days-caution{/if}">
                                                    {if $days_left == 1}
                                                        {$days_left} day left
                                                    {else}
                                                        {$days_left} days left
                                                    {/if}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{$_url}customers/view/{$ds.id}" class="btn-modern btn-view">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    {else}
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fa fa-check-circle"></i>
                            </div>
                            <h3>All Clear!</h3>
                            <p>No customers will be overdue in the next 5 days.</p>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
