# Sales Audit Plugin for SpeedRadius

## Overview
The Sales Audit plugin provides comprehensive sales analysis and comparison reports for your ISP management system. It offers detailed insights into your revenue performance with visual charts and statistical comparisons.

## Features

### 1. Dashboard Overview
- **Today vs Yesterday**: Real-time comparison of daily sales performance
- **Weekly Comparison**: Same day comparison between this week and last week (e.g., Tuesday vs last Tuesday)
- **Monthly Comparison**: Same date comparison between this month and last month
- **Week to Date**: Running total comparison for current week vs same period last week
- **Month to Date**: Running total comparison for current month vs same period last month

### 2. Sales Comparison
- **Multiple Period Options**:
  - Today vs Yesterday
  - This Week vs Last Week
  - This Month vs Last Month
  - Custom Date Range Comparison
- **Detailed Metrics**:
  - Total Sales Amount
  - Number of Transactions
  - Average Transaction Value
  - Percentage Changes with Visual Indicators

### 3. Sales Trends
- **Time Period Analysis**:
  - Last 7 Days (Daily breakdown)
  - Last 30 Days (Daily breakdown)
  - Last 12 Months (Monthly breakdown)
- **Performance Statistics**:
  - Best performing periods
  - Success rate calculations
  - Growth trend analysis

### 4. Visual Analytics
- **Interactive Charts**:
  - Hourly sales distribution for today
  - Daily comparison charts
  - Trend line charts with dual-axis (sales + transactions)
  - Payment method breakdown (pie chart)
- **Real-time Updates**: Dashboard refreshes every 5 minutes

### 5. Top Performance Reports
- **Top Performing Plans**: Revenue ranking with transaction counts
- **Payment Method Breakdown**: Distribution by payment gateway
- **Performance Bars**: Visual representation of relative performance

## Installation

1. Copy the plugin file to your SpeedRadius installation:
   ```
   /system/plugin/SalesAudit.php
   ```

2. Copy the template files to the UI directory:
   ```
   /ui/ui/salesAudit.tpl
   /ui/ui/salesAuditComparison.tpl
   /ui/ui/salesAuditTrends.tpl
   ```

3. The plugin will automatically register in the admin menu under "Reports"

## Usage

### Accessing the Plugin
1. Login to your admin panel
2. Navigate to the main menu
3. Look for "Sales Audit" in the Reports section
4. Click to access the dashboard

### Dashboard Navigation
- **Dashboard**: Main overview with key metrics and charts
- **Comparison**: Detailed period-to-period comparisons
- **Trends**: Long-term trend analysis and statistics

### Comparison Analysis
1. Select your comparison period:
   - Today vs Yesterday
   - This Week vs Last Week
   - This Month vs Last Month
   - Custom Date Range
2. View percentage changes with color-coded indicators:
   - Green (↗): Positive growth
   - Red (↘): Decline
3. Analyze daily breakdowns in detailed tables

### Trend Analysis
1. Choose your analysis period:
   - 7 Days: Daily performance over the last week
   - 30 Days: Daily performance over the last month
   - 12 Months: Monthly performance over the last year
2. Review performance statistics and growth patterns
3. Identify best and worst performing periods

## Key Metrics Explained

### Sales Metrics
- **Total Sales**: Sum of all transaction amounts (excluding balance transfers)
- **Transaction Count**: Number of completed transactions
- **Average Transaction**: Total sales divided by transaction count

### Comparison Metrics
- **Percentage Change**: ((New Value - Old Value) / Old Value) × 100
- **Growth Indicators**: Visual arrows showing positive/negative trends
- **Period Performance**: Side-by-side comparison with detailed breakdowns

### Trend Metrics
- **Performance Statistics**: Best/worst days, success rates
- **Growth Analysis**: Period-over-period growth calculations
- **Activity Rate**: Percentage of days with sales activity

## Technical Details

### Database Tables Used
- `tbl_transactions`: Main transaction data
- `tbl_plans`: Plan information for top performers
- Excludes internal transfers and balance adjustments

### Data Filtering
- Automatically excludes:
  - "Customer - Balance" transactions
  - "Recharge Balance - Administrator" transactions
- Includes all genuine customer purchases and renewals

### Chart Technology
- Uses Chart.js library for interactive visualizations
- Responsive design for mobile and desktop viewing
- Real-time data updates via AJAX endpoints

### Performance Considerations
- Dashboard caches data for 12 hours for monthly sales
- Hourly refresh for current day statistics
- Optimized queries with proper indexing

## API Endpoints

The plugin provides REST API endpoints for external integrations:

### Available Endpoints
- `/plugin/salesAudit&action=api&endpoint=comparison`: Sales comparison data
- `/plugin/salesAudit&action=api&endpoint=trends&period=30days`: Trend data
- `/plugin/salesAudit&action=api&endpoint=hourly&date=2024-08-30`: Hourly sales
- `/plugin/salesAudit&action=api&endpoint=payment-methods`: Payment breakdown

### Response Format
All endpoints return JSON formatted data with appropriate error handling.

## Customization

### Modifying Time Periods
Edit the `getComparisonDates()` function in `SalesAudit.php` to add custom comparison periods.

### Adding New Metrics
Extend the `getSalesComparison()` function to include additional KPIs or calculations.

### Styling Charts
Modify the Chart.js configuration in the template files to customize colors, styling, and behavior.

## Troubleshooting

### Common Issues
1. **No Data Showing**: Check if transactions exist in the database
2. **Charts Not Loading**: Ensure Chart.js CDN is accessible
3. **Permission Errors**: Verify admin access levels
4. **Slow Loading**: Check database indexes on transaction tables

### Debug Mode
Add debug output by modifying the plugin to log query results and performance metrics.

## Security Considerations

### Access Control
- Plugin respects existing admin authentication
- Only accessible to Admin and SuperAdmin roles
- All data queries use parameterized statements

### Data Privacy
- No customer personal information is displayed
- Only aggregated sales statistics are shown
- Follows existing PHPNuxBill security standards

## Support and Updates

For support, customization requests, or bug reports, please refer to the main PHPNuxBill documentation or community forums.

The plugin is designed to be self-contained and should work with standard PHPNuxBill installations without additional dependencies.
