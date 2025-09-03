![Speedradius](install/img/logo.png)

# CHANGELOG

## [2.1.28] - 2025-09-03

### NEW: Expenditure Management Plugin
- **💰 Complete Expense Tracking System**: Added comprehensive expenditure management plugin for ISP business financial tracking

  - **Core Features**:
    - Full CRUD operations for expenses and categories
    - Real-time dashboard with expense statistics
    - Advanced search and filtering capabilities
    - Interactive reports with Chart.js visualizations
    - CSV export functionality for accounting integration
    - 10 pre-loaded ISP business categories

  - **New Files Added**:
    - `system/plugin/expenditure.php` - Main plugin file with complete functionality
    - `system/plugin/install_expenditure_plugin.php` - Automated installation script
    - `system/plugin/Expenditure_Plugin_README.md` - Comprehensive documentation
    - `system/plugin/Expenditure_Plugin_CHANGELOG.md` - Detailed changelog
    - `ui/ui/expenditure_dashboard.tpl` - Dashboard with statistics overview
    - `ui/ui/expenditure_add.tpl` - Add expense form
    - `ui/ui/expenditure_edit.tpl` - Edit expense form  
    - `ui/ui/expenditure_list.tpl` - Expense listing with filters
    - `ui/ui/expenditure_categories.tpl` - Category management
    - `ui/ui/expenditure_reports.tpl` - Analytics and reporting

  - **Database Changes**:
    - Added `tbl_expenditure_categories` table for expense categorization
    - Added `tbl_expenditures` table for expense tracking with full audit trail
    - Foreign key relationships for data integrity
    - Automatic default category creation during installation

  - **Business Value**:
    - Track equipment purchases, bandwidth costs, utilities, salaries
    - Generate monthly, daily, and category-based expense reports
    - Export expense data for tax and accounting purposes
    - Gain complete financial visibility for better business decisions
    - Monitor spending trends and budget compliance

  - **Security & Access**:
    - Admin/SuperAdmin role restrictions
    - Input validation and SQL injection protection
    - Audit trail with user tracking and timestamps

## [2.1.27] - 2025-08-30

### REMOVED: Price Before Discount Field
- **🗑️ UI Cleanup**: Removed the "Price Before Discount" field from service edit forms
  
  - **Affected Templates**:
    - `ui/ui/hotspot-edit.tpl` - Removed Price Before Discount form group
    - `ui/ui/pppoe-edit.tpl` - Removed Price Before Discount form group
  
  - **Backend Cleanup**:
    - `system/controllers/services.php` - Removed price_old processing from edit-post handler
    - `system/controllers/services.php` - Removed price_old processing from edit-pppoe-post handler
    - Removed validation logic for price_old field
    - Removed database assignment of price_old values
  
  - **Benefits**:
    - Simplified user interface for service editing
    - Eliminated confusion from unused discount pricing field
    - Cleaner form layout without unnecessary fields
    - Streamlined service management workflow

## [2.1.26] - 2025-08-30

### ADDED: Sales Audit Plugin - Comprehensive Sales Analysis & Comparison System
- **✨ Major New Feature**: Complete sales audit and comparison plugin with advanced analytics and visual reporting
  
  - **🎯 Core Features Implemented**:
    - **Today vs Yesterday**: Real-time daily sales comparison with transaction counts
    - **Weekly Comparison**: Same weekday comparison (e.g., Tuesday vs last Tuesday)
    - **Monthly Comparison**: Same date comparison between months  
    - **Week/Month to Date**: Progressive running totals with percentage changes
    - **Visual Performance Indicators**: Color-coded growth/decline arrows and percentages

  - **📊 Advanced Analytics Dashboard**:
    - **Interactive Charts**: Hourly sales distribution, payment method breakdowns, trend analysis
    - **Top Performing Plans**: Revenue ranking with transaction counts and performance bars
    - **Payment Gateway Analysis**: Distribution by method with percentages and visual charts
    - **Performance Statistics**: Best/worst periods, success rates, growth momentum tracking

  - **📈 Comprehensive Comparison Engine**:
    - **Multiple Period Analysis**: Today, This Week, This Month, Custom date ranges
    - **Detailed Metrics**: Sales amounts, transaction counts, average transaction values
    - **Daily Breakdown Tables**: Side-by-side period comparisons with growth indicators
    - **Percentage Change Calculations**: Accurate growth/decline analysis with visual indicators

  - **📅 Trends Analysis System**:
    - **Time Period Options**: 7 days (daily), 30 days (daily), 12 months (monthly)
    - **Growth Analysis**: Period-over-period percentage changes with trend indicators
    - **Activity Tracking**: Sales success rates and performance consistency metrics
    - **Statistical Insights**: Highest/lowest performing periods, average calculations

  - **📁 Files Created**:
    - `system/plugin/SalesAudit.php` - Main plugin with comprehensive functionality
    - `system/plugin/SalesAuditConfig.php` - Configuration settings and helper functions
    - `system/plugin/install_SalesAudit.php` - Installation verification script
    - `system/plugin/SalesAudit_README.md` - Complete documentation and usage guide
    - `system/plugin/SalesAudit_SUMMARY.md` - Implementation summary and features overview
    - `ui/ui/salesAudit.tpl` - Main dashboard template with comparison cards
    - `ui/ui/salesAuditComparison.tpl` - Detailed comparison analysis interface
    - `ui/ui/salesAuditTrends.tpl` - Trends analysis and visualization template
    - `ui/ui/styles/salesAudit.css` - Enhanced styling for professional presentation

  - **🔧 Technical Implementation**:
    - **Database Integration**: Optimized queries using existing `tbl_transactions` table
    - **Smart Filtering**: Excludes balance transfers and admin adjustments for accurate sales data
    - **Performance Optimization**: 12-hour caching for monthly data, auto-refresh every 5 minutes
    - **Security**: Role-based access (Admin/SuperAdmin), XSS protection, parameterized queries
    - **Responsive Design**: Mobile-friendly interface with Bootstrap compatibility

  - **🎨 Visual Enhancements**:
    - **Chart.js Integration**: Interactive line charts, pie charts, and trend visualizations
    - **Color-Coded Indicators**: Green for growth, red for decline, with percentage displays
    - **Professional Styling**: Gradient backgrounds, hover effects, responsive layouts
    - **Data Visualization**: Progress bars, performance metrics, statistical summaries

  - **🔌 API Endpoints**:
    - `/plugin/salesAudit&action=api&endpoint=comparison` - Sales comparison data
    - `/plugin/salesAudit&action=api&endpoint=trends` - Trend analysis data
    - `/plugin/salesAudit&action=api&endpoint=hourly` - Hourly sales breakdown
    - `/plugin/salesAudit&action=api&endpoint=payment-methods` - Payment method distribution

  - **💡 Business Impact**:
    - **Performance Monitoring**: Real-time visibility into sales performance vs historical data
    - **Trend Identification**: Spot seasonal patterns, growth momentum, and performance issues
    - **Data-Driven Decisions**: Comprehensive analytics for business planning and optimization
    - **Revenue Insights**: Understand top-performing plans and payment method preferences
    - **Growth Tracking**: Monitor business growth with accurate percentage calculations and visual indicators

  - **🚀 Usage Examples**:
    - Daily morning reviews with "Today vs Yesterday" performance cards
    - Weekly business assessments using same-day comparisons
    - Monthly revenue analysis with detailed breakdown tables
    - Long-term trend identification for strategic planning
    - Payment gateway performance optimization

## [2.1.25] - 2025-08-29

### FIXED: Reminder Notification System Not Sending Automatically
- **🐛 Critical Bug Fix**: Resolved issue where reminder notifications (7-day, 3-day, 1-day expiry warnings) were not being sent automatically via SMS and WhatsApp
  
  - **🔍 Root Cause Identified**: 
    - Cron job `system/cron_reminder.php` was not executing automatically on the server
    - Configuration was correct but automation mechanism was failing
    
  - **📁 Files Modified/Created**:
    - `system/cron_reminder.php` - Added comprehensive debugging output
    - `test_reminder.php` - Created diagnostic script to verify notification system functionality
    - `manual_reminder_trigger.php` - Created manual trigger for immediate testing
    - `web_cron_reminder.php` - Created web-based cron alternative for external scheduling
    - `check_cron_status.php` - Created cron diagnostic tool
    - `cron_reminder_debug.php` - Created detailed debug version with logging
    - `setup_windows_reminder_cron.bat` - Created Windows Task Scheduler setup script
    - `run_reminders.bat` - Created manual batch execution script

  - **🔧 Technical Improvements**:
    - Enhanced notification type detection from `$_notifmsg['reminder_notification']` with fallback to `$config['reminder_notification']`
    - Added comprehensive logging and debugging output for troubleshooting
    - Verified notification configuration reads "both" (SMS + WhatsApp) correctly
    - Confirmed `Message::sendPackageNotification()` method handles dual-channel notifications properly
    - Implemented multiple automation solutions for different server environments

  - **✅ Verification Results**:
    - Manual testing confirmed 12 reminder notifications sent successfully
    - Both SMS and WhatsApp channels working correctly
    - Notification messages properly formatted with customer details, pricing, and payment information
    - All expiry periods (1-day, 3-day, 7-day) functioning as expected

  - **🚀 Automation Solutions Provided**:
    - **Linux Servers**: Verified existing cron job syntax and provided troubleshooting steps
    - **Windows Servers**: Created automated Task Scheduler setup
    - **Web-based Alternative**: Implemented external cron service compatibility
    - **Manual Triggers**: Created immediate testing and backup execution methods

  - **💡 Impact**:
    - Customers now receive timely SMS and WhatsApp reminders before package expiration
    - Reduced support requests about unexpected service disconnections
    - Improved customer retention through proactive renewal notifications
    - Enhanced system reliability with multiple backup execution methods

## [2.1.26] - 2025-08-30

### ADDED: Sales Audit Plugin - Comprehensive Sales Analysis & Comparison System
- **✨ Major New Feature**: Complete sales audit and comparison plugin with advanced analytics and visual reporting
  
  - **🎯 Core Features Implemented**:
    - **Today vs Yesterday**: Real-time daily sales comparison with transaction counts
    - **Weekly Comparison**: Same weekday comparison (e.g., Tuesday vs last Tuesday)
    - **Monthly Comparison**: Same date comparison between months  
    - **Week/Month to Date**: Progressive running totals with percentage changes
    - **Visual Performance Indicators**: Color-coded growth/decline arrows and percentages

  - **📊 Advanced Analytics Dashboard**:
    - **Interactive Charts**: Hourly sales distribution, payment method breakdowns, trend analysis
    - **Top Performing Plans**: Revenue ranking with transaction counts and performance bars
    - **Payment Gateway Analysis**: Distribution by method with percentages and visual charts
    - **Performance Statistics**: Best/worst periods, success rates, growth momentum tracking

  - **📈 Comprehensive Comparison Engine**:
    - **Multiple Period Analysis**: Today, This Week, This Month, Custom date ranges
    - **Detailed Metrics**: Sales amounts, transaction counts, average transaction values
    - **Daily Breakdown Tables**: Side-by-side period comparisons with growth indicators
    - **Percentage Change Calculations**: Accurate growth/decline analysis with visual indicators

  - **📅 Trends Analysis System**:
    - **Time Period Options**: 7 days (daily), 30 days (daily), 12 months (monthly)
    - **Growth Analysis**: Period-over-period percentage changes with trend indicators
    - **Activity Tracking**: Sales success rates and performance consistency metrics
    - **Statistical Insights**: Highest/lowest performing periods, average calculations

  - **📁 Files Created**:
    - `system/plugin/SalesAudit.php` - Main plugin with comprehensive functionality
    - `system/plugin/SalesAuditConfig.php` - Configuration settings and helper functions
    - `system/plugin/install_SalesAudit.php` - Installation verification script
    - `system/plugin/SalesAudit_README.md` - Complete documentation and usage guide
    - `system/plugin/SalesAudit_SUMMARY.md` - Implementation summary and features overview
    - `ui/ui/salesAudit.tpl` - Main dashboard template with comparison cards
    - `ui/ui/salesAuditComparison.tpl` - Detailed comparison analysis interface
    - `ui/ui/salesAuditTrends.tpl` - Trends analysis and visualization template
    - `ui/ui/styles/salesAudit.css` - Enhanced styling for professional presentation

  - **🔧 Technical Implementation**:
    - **Database Integration**: Optimized queries using existing `tbl_transactions` table
    - **Smart Filtering**: Excludes balance transfers and admin adjustments for accurate sales data
    - **Performance Optimization**: 12-hour caching for monthly data, auto-refresh every 5 minutes
    - **Security**: Role-based access (Admin/SuperAdmin), XSS protection, parameterized queries
    - **Responsive Design**: Mobile-friendly interface with Bootstrap compatibility

  - **🎨 Visual Enhancements**:
    - **Chart.js Integration**: Interactive line charts, pie charts, and trend visualizations
    - **Color-Coded Indicators**: Green for growth, red for decline, with percentage displays
    - **Professional Styling**: Gradient backgrounds, hover effects, responsive layouts
    - **Data Visualization**: Progress bars, performance metrics, statistical summaries

  - **🔌 API Endpoints**:
    - `/plugin/salesAudit&action=api&endpoint=comparison` - Sales comparison data
    - `/plugin/salesAudit&action=api&endpoint=trends` - Trend analysis data
    - `/plugin/salesAudit&action=api&endpoint=hourly` - Hourly sales breakdown
    - `/plugin/salesAudit&action=api&endpoint=payment-methods` - Payment method distribution

  - **💡 Business Impact**:
    - **Performance Monitoring**: Real-time visibility into sales performance vs historical data
    - **Trend Identification**: Spot seasonal patterns, growth momentum, and performance issues
    - **Data-Driven Decisions**: Comprehensive analytics for business planning and optimization
    - **Revenue Insights**: Understand top-performing plans and payment method preferences
    - **Growth Tracking**: Monitor business growth with accurate percentage calculations and visual indicators

  - **🚀 Usage Examples**:
    - Daily morning reviews with "Today vs Yesterday" performance cards
    - Weekly business assessments using same-day comparisons
    - Monthly revenue analysis with detailed breakdown tables
    - Long-term trend identification for strategic planning
    - Payment gateway performance optimization

## [2.1.24] - 2025-08-27

### FIXED: Template Syntax Error
- **🐛 Bug Fix**: Fixed Smarty template syntax error in customer dashboard
  - **📁 File Modified**:
    - `ui/ui/customer/dashboard.tpl` - Removed erroneous `{/if}` tag on line 231

  - **🔧 Technical Details**:
    - Resolved "unclosed '{foreach}' tag" error caused by misplaced `{/if}` statement
    - Fixed template compilation error that was preventing customer dashboard from loading
    - Corrected Smarty template syntax to ensure proper tag closure

  - **💡 Impact**:
    - Customer dashboard now loads without template compilation errors
    - Improved system stability and user experience
    - Fixed critical issue preventing customers from accessing their dashboard

## [2.1.23] - 2025-08-19

### ADDED: Delete Package Feature
- **✨ New Feature**: Added ability to delete customer packages
  - **📁 Files Modified**:
    - `system/controllers/customers.php` - Added new delete_package function to handle package deletion
    - `ui/ui/customers-view.tpl` - Added Delete button to package management interface

  - **🔧 Technical Implementation**:
    - Created new controller function to safely delete packages
    - Added confirmation dialog to prevent accidental deletions
    - Implements proper cleanup of inactive packages
    - Added responsive button layout with icon for better usability
    - Arranged buttons in two rows for cleaner interface on all screen sizes

  - **💡 Key Benefits**:
    - Administrators can now completely remove unwanted packages from customer accounts
    - Improved package management workflow with direct delete functionality
    - Better cleanup of unused or expired packages in the system

## [2.1.22] - 2025-08-17

### REMOVED: VPN Services
- **🔄 Feature Removal**: Completely removed VPN services from the system
  - **📁 Files Deleted**:
    - `wireguard_manager.php` - Removed WireGuard VPN management functionality
    - `system/devices/MikrotikVpn.php` - Removed VPN device driver

  - **📁 Files Modified**:
    - `ui/ui/sections/header.tpl` - Removed VPN menu item from navigation
    - `ui/ui/customers-add.tpl` - Removed VPN option from service type dropdown
    - `ui/ui/customers-edit.tpl` - Removed VPN option from service type dropdown
    - `ui/ui/recharge.tpl` - Removed VPN option from recharge form
    - `ui/ui/plan.tpl` - Removed VPN references from plan displays
    - `ui/ui/customer/dashboard.tpl` - Removed VPN sections from customer dashboard
    - `ui/ui/customer/orderPlan.tpl` - Removed VPN plan ordering options
    - `system/controllers/order.php` - Removed VPN plan queries and references

  - **🔧 Technical Implementation**:
    - Removed all user interface elements related to VPN services
    - Eliminated backend code handling VPN plan types
    - Ensured no VPN options appear in service selection forms
    - Removed VPN sections from customer dashboard

  - **💡 Key Benefits**:
    - Streamlined system with focus on core Hotspot and PPPoE services
    - Simplified user interface with removal of unused VPN options
    - Cleaner codebase with removal of unused VPN functionality

### FIXED: Blank tabs in Settings/App page
- **🐞 Bug Fix**: Fixed issue where tabs in settings/app page were displaying blank content
  - **📁 Files Created**:
    - `ui/ui/styles/fix-panels.css` - CSS fixes to ensure panel content displays properly
    - `ui/ui/styles/compact-button.css` - CSS for refresh button (later removed)
    - `ui/ui/scripts/panel-fix.js` - JavaScript to force panel visibility
    - `ui/ui/scripts/comprehensive-panel-fix.js` - Additional panel fixing functionality
    - `ui/ui/scripts/button-fallback.js` - Fallback script for panel fixes
    - `ui/ui/scripts/blue-panel-button.js` - Script for positioning refresh button (later removed)

  - **📁 Files Modified**:
    - `ui/ui/sections/header.tpl` - Added CSS file references and cleanup script
    - `ui/ui/sections/footer.tpl` - Added JavaScript file references

  - **🔧 Technical Implementation**:
    - Fixed Bootstrap collapse functionality by removing collapse classes
    - Applied direct CSS styling to force panel visibility
    - Added JavaScript to ensure proper panel display
    - Initially added a refresh button for manual page refresh
    - Subsequently removed the refresh button per user request
    - Added cleanup script to ensure no refresh buttons appear

  - **💡 Key Benefits**:
    - All settings panels now display content properly
    - Users can navigate settings sections without blank content issues
    - Improved user experience with properly functioning tabs
    - Clean interface without unnecessary refresh buttons

## [2.1.21] - 2025-08-14

### NEW: Dark Mode Toggle for Admin Dashboard
- **🌙 Dark Mode Implementation**: Added functional dark mode toggle button to admin dashboard header
  - **📁 Files Modified**:
    - `ui/ui/sections/header.tpl` - Added comprehensive dark mode CSS styles and enhanced toggle button styling
    - `ui/ui/sections/footer.tpl` - Added JavaScript functionality for dark mode toggle

  - **🎯 Toggle Button Features**:
    - **Header Integration**: Dark mode toggle button positioned in top navigation bar next to search
    - **Dynamic Icons**: Button displays 🌞 (sun) for light mode and 🌙 (moon) for dark mode
    - **Smooth Animations**: Hover effects and CSS transitions for polished user experience
    - **One-Click Toggle**: Simple click interaction to switch between themes

  - **💾 Persistent Theme Storage**:
    - **localStorage Integration**: User's dark mode preference saved in browser localStorage
    - **Auto-Apply**: Theme preference automatically restored on page load/refresh
    - **Cross-Session**: Dark mode setting persists across browser sessions

  - **🎨 Comprehensive Dark Mode Styling**:
    - **Complete Coverage**: All dashboard elements styled for dark mode including:
      - Navigation header and sidebar with dark backgrounds
      - Content areas with dark gray backgrounds
      - Tables with proper dark styling and hover effects
      - Forms and input fields with dark themes
      - Buttons with appropriate dark mode colors
      - Dropdowns and select2 elements with dark styling
      - Modals and alerts with dark backgrounds
      - Pagination and breadcrumb components
    - **Color Scheme**: Professional dark theme using grays (#1a202c, #2d3748, #4a5568) with proper contrast
    - **Enhanced Visibility**: Improved text contrast and visual hierarchy in dark mode

  - **🔧 Technical Implementation**:
    - **JavaScript Functions**: 
      - `initDarkModeToggle()` - Initialize dark mode functionality
      - Event listeners for toggle button clicks
      - Theme state management and icon updates
    - **CSS Classes**: `.dark-mode` class applied to body element
    - **Smooth Transitions**: CSS transitions for seamless theme switching
    - **Browser Compatibility**: Works across modern browsers with localStorage support

  - **💡 Key Benefits**:
    - **Eye Strain Reduction**: Dark mode reduces eye strain during extended use
    - **Professional Appearance**: Modern dark theme for contemporary UI experience
    - **User Preference**: Allows users to choose their preferred viewing mode
    - **Battery Saving**: Dark mode can help save battery on OLED/AMOLED displays

## [2.1.20] - 2025-08-14

### NEW: Total Data Usage Dashboard Widget - MikroTik WAN Interface Monitoring
- **📊 WAN Data Usage Tracking**: Added comprehensive total data usage monitoring widget to admin dashboard
  - **📁 Files Modified**:
    - `system/controllers/dashboard.php` - Added WAN interface detection and data usage functions
    - `ui/ui/dashboard.tpl` - Added beautiful data usage dashboard widget

  - **🎯 Smart WAN Interface Detection**:
    - **Intelligent WAN Discovery**: Multi-level detection algorithm for accurate WAN interface identification:
      1. **Primary**: Analyzes routing table for default gateway (0.0.0.0/0) routes
      2. **Secondary**: Pattern matching for common WAN interface names (`ether1`, `wan`, `internet`, `fiber`, `adsl`, `pppoe-out`)
      3. **Fallback**: Auto-selects `ether1` as standard WAN interface
    - **Multi-Router Support**: Aggregates data usage from all enabled MikroTik routers
    - **Virtual Interface Filtering**: Excludes internal interfaces (loopback, VPN, virtual) for accurate calculations

  - **🎨 Beautiful Dashboard Widget**:
    - **Gradient Design**: Purple gradient background with modern card styling
    - **Three-Column Layout**: 
      - Total Downloaded (with download icon)
      - Total Uploaded (with upload icon)  
      - Total Data Usage (with exchange icon)
    - **Router Information**: Shows active router count and last update timestamp
    - **Manual Refresh**: Click-to-refresh button with loading animations
    - **Expandable Details**: "View Details" link shows per-router breakdown

  - **🔧 Advanced Features**:
    - **Intelligent Caching**: 5-minute cache to prevent router overload
    - **Real-time Updates**: AJAX refresh without page reload
    - **Error Handling**: Graceful handling of offline routers
    - **Debug Information**: Detailed router breakdown with WAN interface confirmation
    - **Data Format**: Human-readable format (B, KB, MB, GB, TB)

  - **📈 Technical Implementation**:
    - **RouterOS API Integration**: Uses existing PEAR2\Net\RouterOS library
    - **Function**: `getTotalDataUsage()` - Core data collection function
    - **Function**: `formatBytes()` - Human-readable data formatting
    - **Cache Management**: Automatic cache invalidation and refresh
    - **Multi-Router Aggregation**: Combines data from all enabled routers
    - **Connection Testing**: Quick socket test before API connection

  - **🚀 New API Endpoints**:
    - `POST /dashboard/refresh-data-usage` - Manual data usage refresh endpoint
    - Returns JSON with formatted data usage statistics and router details

  - **💡 Key Benefits**:
    - **Accurate WAN Monitoring**: Only counts actual internet traffic, not internal LAN traffic
    - **ISP-Grade Statistics**: Perfect for monitoring customer data consumption
    - **Multi-Router Support**: Ideal for distributed network setups
    - **Performance Optimized**: Cached results prevent excessive router queries
    - **Visual Dashboard Integration**: Seamlessly integrated with existing dashboard design

## [2.1.19] - 2025-08-14

### ENHANCED: Online Users Dashboard - Modern Statistics & Monthly Usage Tracking
- **📊 Completely Redesigned Dashboard**: Transformed the Online Users page into a modern, responsive statistics dashboard
  - **📁 Files Modified**:
    - `system/controllers/onlineusers.php` - Enhanced with new statistics endpoints and monthly usage integration
    - `ui/ui/hotspot_users.tpl` - Complete UI overhaul with modern responsive design

  - **🎨 Modern UI Features**:
    - **Real-time Statistics Cards**: Four beautiful gradient cards displaying:
      - Total Users (with user icon)
      - Total Download (with download icon)  
      - Total Upload (with upload icon)
      - Total Bandwidth (with chart icon)
    - **Responsive Design**: CSS Grid layout with breakpoints for desktop (4 columns), tablet (2 columns), and mobile (1 column)
    - **Auto-refresh**: Statistics update every 30 seconds automatically
    - **Smooth Animations**: fadeInUp animations with staggered delays for visual appeal
    - **Hover Effects**: Interactive cards with elevation and color transitions

  - **📈 Monthly Usage Tracking System**:
    - **Database Integration**: New tables for persistent monthly usage storage
      - `tbl_monthly_usage` - Monthly aggregated statistics
      - `tbl_daily_usage_snapshots` - Daily data collection points
      - `tbl_usage_settings` - System configuration settings
    - **Automated Data Collection**: Daily snapshots at 23:59 preserve usage data
    - **Monthly Reset**: Automatic monthly reset on 1st of each month
    - **Historical Reporting**: Month/year selector for viewing past usage data
    - **Manual Controls**: "Take Snapshot" and "Reset Monthly" buttons for manual management

  - **🔧 New API Endpoints**:
    - `GET /onlineusers.php/hotspot_stats` - Real-time hotspot statistics
    - `GET /onlineusers.php/monthly_usage?year=X&month=Y` - Monthly usage data
    - `POST /onlineusers.php/take_snapshot` - Manual snapshot creation
    - `POST /onlineusers.php/reset_monthly` - Manual monthly reset

  - **⚡ Technical Improvements**:
    - **MikroTik Integration**: Enhanced `mikrotik_get_hotspot_stats()` function with error handling
    - **Data Persistence**: Router reboot protection through database storage
    - **Responsive JavaScript**: Auto-refresh with visual feedback and error handling
    - **Toast Notifications**: Modern notification system for user feedback
    - **Performance Optimized**: Efficient SQL queries with proper indexing

  - **🛡️ Data Protection Features**:
    - **Router Reboot Resilience**: Monthly data survives router restarts
    - **Automated Backups**: Daily snapshots preserve usage history
    - **Manual Override**: Emergency snapshot and reset capabilities
    - **Logging System**: Comprehensive logging for troubleshooting

  - **📱 Cross-Device Compatibility**:
    - **Mobile Responsive**: Optimized layouts for phones and tablets
    - **Touch-Friendly**: Large buttons and touch targets
    - **Fast Loading**: Optimized CSS and JavaScript for performance
    - **Modern Browsers**: Support for all modern web browsers

  - **🔄 Automation Features**:
    - **Cron Job Integration**: Automated daily snapshots and monthly resets
    - **Windows Task Scheduler**: Support for Windows-based installations
    - **Installation Scripts**: Automated setup for both Linux and Windows
    - **Configuration Management**: Settings stored in database for easy management

## [2.1.18] - 2025-08-14

### ENHANCED: DHCP Leases Plugin - Static Lease Management
- **🔧 Added Make Static & Remove Functionality**: Enhanced DHCP leases plugin with comprehensive static lease management capabilities
  - **📁 Files Modified**:
    - `system/plugin/dhcp_leases.php` - Added new functions for static lease management
    - `system/plugin/ui/dhcp_leases.tpl` - Enhanced UI with action buttons and modals

  - **🆕 New Functions Added**:
    - `dhcp_leases_make_static()` - Converts dynamic DHCP leases to static leases
    - `dhcp_leases_remove()` - Removes static DHCP leases from MikroTik router
    - Enhanced `dhcp_leases_get_data()` to include lease IDs for proper identification

  - **🎨 User Interface Enhancements**:
    - **Action Buttons**: Added contextual action buttons based on lease type
      - Green "Make Static" button (🔒) for dynamic leases
      - Red "Remove" button (🗑️) for static leases
      - Maintained existing "View Details" and "Ping" buttons
    - **Visual Improvements**: 
      - Color-coded table rows (blue tint for static, light gray for dynamic)
      - Blue left border accent for static leases
      - Smooth hover animations on action buttons
      - Enhanced tooltips with clear action descriptions

  - **🔧 Modal Dialog System**:
    - Replaced browser alerts with professional confirmation modals
    - Color-coded confirmation buttons (green for make static, red for remove)
    - Detailed information display with IP and MAC addresses
    - Warning messages for destructive actions
    - Success/error feedback with proper styling

  - **🛡️ Security & Validation Features**:
    - Admin authentication required for all operations
    - Input validation for router ID, IP address, and MAC address
    - Conflict prevention - checks for existing static leases before creation
    - Comprehensive error handling with detailed feedback messages
    - Safe lease identification and removal process

  - **⚡ Technical Improvements**:
    - AJAX-based operations for seamless user experience
    - Automatic page refresh after successful operations
    - Loading indicators during API calls to MikroTik routers
    - RESTful endpoint structure (`/plugin/dhcp_leases_make_static`, `/plugin/dhcp_leases_remove`)
    - Enhanced data structure with lease IDs for proper tracking

  - **🔌 MikroTik Integration**:
    - Direct RouterOS API calls for lease manipulation
    - Automatic comment addition with timestamp for created static leases
    - Proper lease matching by IP address and MAC address
    - Safe removal process with lease validation
    - Support for all MikroTik DHCP server configurations

## [2.1.17] - 2025-08-13

### NEW: Zettatel SMS Gateway Plugin
- **📱 Added Zettatel SMS Gateway Support**: New SMS gateway plugin for Zettatel API integration
  - **� Files Created**:
    - `system/plugin/ZettatelGateway.php` - Main plugin file with SMS sending logic
    - `system/plugin/ui/smsGatewayZettatel.tpl` - Smarty template for admin interface

  - **📁 Files Modified**:
    - `system/plugin/SMS_Gateway_Manager.php` - Added Zettatel routing and dropdown option
    - `ui/ui/app-settings.tpl` - Added Zettatel to SMS Gateway selection dropdown

  - **�🔧 Plugin Implementation**: 
    - Created ZettatelGateway.php with full SMS sending functionality
    - Added configuration interface with API key, user ID, password, and sender ID fields
    - Integrated with existing SMS Gateway Manager for seamless switching
    - Implemented duplicate message prevention using SMSLock system
    - Added comprehensive logging to tbl_sms_logs with gateway identification

  - **🎨 User Interface Components**: 
    - Created smsGatewayZettatel.tpl template with dashboard and configuration tabs
    - Added Zettatel option to SMS Gateway dropdown in main settings
    - Follows consistent UI patterns with other SMS gateway plugins
    - Dashboard displays recent SMS logs with status information

  - **🔌 API Integration**: 
    - Supports Zettatel portal.zettatel.com/SMSApi/send endpoint
    - Implements proper phone number formatting for Kenya (+254)
    - Handles JSON response parsing with error reporting
    - Includes CURL-based HTTP requests with proper headers and authentication

  - **⚙️ Configuration Management**: 
    - Stores settings in tbl_appconfig: zettatel_api_key, zettatel_user_id, zettatel_password, zettatel_sender_id
    - Admin menu integration under "Zettatel SMS Gateway" 
    - Compatible with existing SMS notification settings (expired, payment, reminder notifications)

## [2.1.16] - 2025-08-03

### ENHANCED: M-Pesa Reconnection System
- **🔧 Critical M-Pesa Reconnection Functionality Fix**: Resolved major issue where users with active packages received "expired package" errors during reconnection attempts
  - **🐛 Database Session Lookup Enhancement**: 
    - Fixed session matching logic to prioritize M-Pesa transaction code matching
    - Added fallback session lookup for cases where M-Pesa code isn't stored in session method field
    - Implemented proper session selection for multi-session users

  - **⏰ Expiry Time Calculation Improvements**: 
    - Fixed null expiry_date handling in database that caused strtotime() to return false
    - Added fallback expiry calculation using transaction paid_date + plan validity duration
    - Enhanced time difference calculations for accurate remaining time display
    - Implemented strict expiry enforcement - expired packages cannot reconnect

  - **🔄 Router Communication Integration**: 
    - Restored Package::rechargeUser() function call for proper router notification
    - Added dual-approach reconnection: database update + router communication
    - Implemented fallback success response if router communication fails
    - Enhanced session status management with proper 'on' status activation

  - **📊 Advanced Debugging and Monitoring**: 
    - Added comprehensive debug information in API responses
    - Included timing calculations, session status, and expiry details
    - Enhanced error messages with specific failure reasons
    - Added transaction validation and status checking

  - **🛡️ Security and Validation Enhancements**: 
    - Strict package expiry enforcement - no grace periods for expired packages
    - Multi-device session conflict detection and prevention
    - Transaction status validation (only completed transactions allowed)
    - Enhanced M-Pesa code validation and verification

  - **💡 User Experience Improvements**: 
    - Clear error messaging for different failure scenarios
    - Accurate remaining time calculations and display
    - Proper success notifications with session details
    - Enhanced response codes for frontend handling

## [2.1.15] - 2025-08-02

### NEW FEATURE: IP Bindings Plugin
- **Complete Hotspot IP Bindings Management System**: Brand new plugin for comprehensive IP binding monitoring and management
  - **📊 Real-time Binding Monitoring**: 
    - View all hotspot IP bindings from connected MikroTik routers via API
    - Live status updates (Active, Disabled) with visual indicators
    - Support for multiple router selection and switching
    - Automatic IP address sorting for logical display organization

  - **📈 Interactive Statistics Dashboard**: 
    - Total bindings counter with real-time updates
    - Active bindings indicator (green badge)
    - Disabled bindings indicator (yellow badge)
    - Last updated timestamp with automatic refresh capabilities
    - Statistics update dynamically with filtering and search

  - **🔍 Advanced Filtering and Search Capabilities**:
    - Global search across MAC addresses, IP addresses, and server names
    - Status-based filtering (Active/Disabled)
    - Server-specific filtering for multi-server environments
    - Real-time filtering without page reload for smooth user experience
    - Combined filter support for precise data queries

  - **📱 Fully Responsive Design Implementation**:
    - Mobile-first responsive layout with Bootstrap framework
    - Optimized font sizing (13px base, 24px statistics headers)
    - Responsive table design with horizontal scrolling on mobile
    - Touch-friendly interface with proper button sizing
    - Adaptive layout for tablets and desktop screens

  - **🔄 Comprehensive Data Management**:
    - One-click data refresh from MikroTik routers
    - CSV export functionality with timestamp and router identification
    - Multi-router support with seamless switching
    - Real-time error handling and connection status monitoring
    - Automatic data validation and formatting

  - **💾 Export and Reporting Features**:
    - CSV export with comprehensive binding information
    - Filename includes router name and timestamp for organization
    - Export includes MAC address, IP address, to address, server, type, status, and comments
    - Filtered export - only visible data is included in export
    - Professional formatting suitable for external analysis

  - **🛡️ Security and Error Handling**:
    - Admin authentication required for all operations
    - Comprehensive error messages for connection failures
    - Data validation before API operations
    - Graceful handling of router connectivity issues

### NEW FEATURE: System Users Plugin
- **Complete MikroTik System Users Management**: Brand new plugin for comprehensive system user administration
  - **📊 Real-time Statistics Dashboard**: 
    - Total system users counter with live updates
    - Active users indicator showing enabled accounts
    - Disabled users indicator for temporarily deactivated accounts
    - Last updated timestamp with automatic refresh capabilities
    - HD color scheme with modern gradient-based statistics cards

  - **👥 Full CRUD Operations for System Users**:
    - **Create New Users**: Add system users with full configuration options
    - **Read User Data**: View comprehensive user information and status
    - **Update Users**: Edit existing user properties, passwords, and settings
    - **Delete Users**: Remove users with admin protection safeguards
    - **Enable/Disable**: Toggle user status without permanent deletion

  - **🔍 Advanced Filtering and Search System**:
    - Global search across usernames and user groups
    - Status-based filtering (Active/Disabled users)
    - Group-specific filtering for permission-based management
    - Real-time filtering without page reload for smooth experience
    - Combined filter support for precise user queries

  - **📱 Enhanced Responsive Design**:
    - HD color scheme with premium gradient statistics cards
    - Professional spacing and typography optimization
    - Mobile-optimized layout with touch-friendly controls
    - Fully responsive interface for all device sizes
    - Modern UI matching PHPNuxBill theme standards

  - **📑 Multi-Tab Interface Architecture**:
    - **Users Tab**: Complete user management with CRUD operations
    - **Groups Tab**: View available user groups and permission policies
    - **Active Users Tab**: Real-time monitoring of currently logged-in users
    - Seamless tab switching with preserved filter states

  - **🔐 Comprehensive User Management Features**:
    - Username validation (letters, numbers, underscore, dash only)
    - Secure password management with confirmation requirements
    - User group assignment (full, read, write, custom)
    - IP address restrictions with allowed address configuration
    - Comment system for user documentation and notes
    - Last login tracking and session monitoring

  - **🔄 Advanced Data Operations**:
    - Real-time refresh from MikroTik routers via API
    - CSV export functionality with comprehensive user data
    - Multi-router support with seamless switching
    - Auto-sorting by username for logical organization
    - Active user session monitoring with connection details

  - **💾 Export and Reporting Capabilities**:
    - CSV export with complete user information
    - Filename includes router name and timestamp
    - Export includes username, group, allowed address, status, and comments
    - Filtered export - only visible users included
    - Professional formatting for external analysis

  - **🛡️ Security and Protection Features**:
    - Admin user protection prevents accidental removal
    - Secure password handling with validation
    - Admin authentication required for all operations
    - Data validation before MikroTik API operations
    - Comprehensive error handling and user feedback

  - **🔧 Technical Implementation**:
    - MikroTik RouterOS API integration via PEAR2\Net\RouterOS
    - Real-time data retrieval with no local caching
    - Smarty template engine compatibility with JavaScript
    - Bootstrap framework with custom CSS enhancements
    - Comprehensive error handling and connection management

### FIXES: Template Engine Compatibility
- **Smarty Template JavaScript Conflicts Resolution**:
  - Fixed JavaScript template literal syntax conflicts with Smarty engine
  - Converted ES6 template literals (`${}`) to string concatenation
  - Resolved "Unexpected '.'" syntax errors in template processing
  - Improved template compatibility for complex JavaScript operations

### UI IMPROVEMENTS: Button and Interface Optimization  
- **Header Button Font Size Optimization**:
  - Reduced header action button font sizes from default to 11px
  - Optimized icon sizing to 10px for better visual balance
  - Improved button padding and line-height for professional appearance
  - Enhanced visual hierarchy in panel headers

  - **🛡️ Security and Error Handling**:
    - Admin authentication requirement for access
    - Comprehensive error messages for troubleshooting
    - Graceful handling of router connection failures
    - Input validation and sanitization
    - Secure data transmission and display

  - **⚡ Performance Optimizations**:
    - Efficient API communication with MikroTik routers
    - Optimized data processing for large binding lists
    - Minimal server resource usage with smart caching
    - Fast loading times with progressive enhancement
    - Memory-efficient data structures and algorithms

### Technical Implementation Details:
- **MikroTik API Integration**: Uses `/ip/hotspot/ip-binding/print` command for real-time data
- **Plugin Architecture**: Follows PHPNuxBill plugin standards with register_menu() and register_hook()
- **Template Engine**: Smarty templating with responsive Bootstrap components
- **JavaScript Framework**: jQuery-based with AJAX for seamless user experience
- **Database Integration**: Uses existing ORM system with tbl_routers table
- **Cross-browser Compatibility**: Tested on modern browsers with mobile optimization
- **Documentation**: Complete README with installation, usage, and troubleshooting guides

### Files Created/Modified:
- **Backend Plugin**: `system/plugin/ip_bindings.php` (490+ lines)
  - Complete CRUD operations for IP bindings management
  - MikroTik API integration with error handling
  - Action handlers: add, edit, remove, enable, disable, export
  - JSON response system for AJAX operations
  - Admin authentication and security validation

- **Frontend Template**: `ui/ui/ip_bindings.tpl` (1300+ lines)
  - Modern responsive interface with Bootstrap framework
  - Statistics dashboard with real-time updates
  - Advanced search and filtering capabilities
  - Action buttons with modern styling
  - Add/Edit modals with form validation
  - Custom CSS with clean color scheme and responsive breakpoints
  - JavaScript functions for all CRUD operations

## [2.1.14] - 2025-08-02

### NEW FEATURE: DHCP Server Leases Plugin
- **Complete DHCP Lease Management System**: Brand new plugin for comprehensive DHCP lease monitoring and management
  - **📊 Real-time Lease Monitoring**: 
    - View all DHCP leases from connected MikroTik routers via API
    - Live status updates (Bound, Waiting, Offered, Disabled, Blocked)
    - Support for multiple router selection and switching
    - Automatic IP address sorting for organized display

  - **📈 Interactive Statistics Dashboard**: 
    - Total leases counter with real-time updates
    - Bound leases indicator (green badge)
    - Waiting leases indicator (yellow badge)
    - Static leases indicator (red badge)
    - Statistics update dynamically with filtering and search

  - **🔍 Advanced Filtering & Search System**:
    - Status-based filtering (All, Bound, Waiting, Offered, Disabled, Blocked)
    - Real-time search across IP addresses, MAC addresses, and hostnames
    - Client ID search functionality
    - Instant result filtering without page reload

  - **📋 Comprehensive Lease Information Display**:
    - IP addresses (both configured and active if different)
    - MAC addresses with code formatting and active MAC detection
    - Client ID information with overflow handling
    - DHCP server assignment with color-coded labels
    - Lease expiration times with "Never" handling
    - Last seen timestamps with proper formatting
    - Hostname resolution with unknown device indication
    - Lease type classification (Static/Dynamic)
    - RADIUS integration status badges

  - **🎨 Responsive User Interface Design**:
    - Mobile-first responsive layout with proper breakpoints
    - Color-coded status indicators (Green=Bound, Yellow=Waiting, Red=Issues)
    - Professional table design with hover effects
    - Modal popup for detailed lease information
    - Clean, modern card-based statistics display
    - Optimized font sizes for readability (13px table, 12px headers)

  - **⚡ Performance & Usability Features**:
    - Manual refresh functionality with loading indicators
    - Router selection dropdown with IP display
    - CSV export capability with timestamped filenames
    - Efficient MikroTik API integration using PEAR2\Net\RouterOS
    - Error handling and recovery mechanisms
    - Proper authentication and security controls

  - **🔧 Technical Implementation**:
    - **Backend**: `system/plugin/dhcp_leases.php` with full API integration
    - **Frontend**: `system/plugin/ui/dhcp_leases.tpl` with responsive design
    - **Menu Integration**: Added to Settings menu with network icon
    - **API Endpoints**: `/plugin/dhcp_leases`, `/plugin/dhcp_leases_refresh/[id]`, `/plugin/dhcp_leases_export/[id]`
    - **MikroTik API**: Uses `/ip/dhcp-server/lease/print` command
    - **Security**: Admin/SuperAdmin access only with proper token validation

  - **📱 Cross-Device Compatibility**:
    - Desktop optimization (769px+): Full feature set with 13px readable fonts
    - Tablet adaptation (768px and below): Compact 11px fonts with maintained functionality
    - Mobile responsive (480px and below): Optimized 10px fonts for space efficiency
    - Touch-friendly buttons and controls across all devices

  - **📄 Documentation & Support**:
    - Complete README.md with usage instructions and troubleshooting
    - Status indicator legend and lease type explanations
    - API endpoint documentation and security considerations
    - Browser compatibility information and technical requirements

## [2.1.13] - 2025-08-01

### Notification System Fixes
- **Fixed WhatsApp Expired Notifications**: Resolved critical issue where expired package notifications were not being sent via WhatsApp
  - **Root Cause**: Cron jobs were using legacy database configuration (`$config['user_notification_expired']`) instead of new JSON-based notification system (`$_notifmsg['expired_notification']`)
  - **Updated system/cron.php**: Modified expired notification detection logic (lines 84-90) to properly read from JSON notification settings
  - **Updated system/cron_reminder.php**: Enhanced reminder notification type detection (lines 50-56) to use unified notification configuration
  - **Added Fallback Support**: Implemented proper fallback mechanism to legacy config system for backward compatibility
  - **Verified Configuration**: Confirmed notifications.json contains correct WhatsApp settings (`"expired_notification":"wa"`)
  - **Impact**: Both PPPoE and Hotspot expired notifications now properly use WhatsApp when configured, while reminder notifications (7 days, 3 days, 1 day) continue working as expected

## [2.1.12] - 2025-07-31

### WhatsApp Gateway Plugin Enhancements
- **UI/UX IMPROVEMENTS**: Comprehensive overhaul of WhatsApp Gateway interface and functionality
  - **Button Optimization**: Reduced button sizes for better interface proportions
    - Decreased padding from `8px 16px` to `6px 12px` for regular buttons
    - Reduced font size from `14px` to `12px` for better space utilization
    - Created compact `4px 8px` padding and `11px` font size for table action buttons
    - Added `.action-buttons` container class for improved table cell spacing
    - Minimized shadow effects and hover animations for cleaner appearance

  - **Check Status Functionality Fix**: Resolved continuous loading issue
    - Changed button from `type="submit"` to `type="button"` with proper click handler
    - Added `checkWhatsAppStatus()` JavaScript function with visual feedback
    - Implemented spinning icon animation during status check
    - Added auto-refresh functionality every 60 seconds for QR/pair code updates
    - Enhanced URL parameter preservation for phone number and pair mode
    - Added proper loading states and error prevention

- **WhatsApp Logs Management System**: Complete logs management overhaul
  - **Bulk Delete Operations**: Added comprehensive delete functionality
    - **Delete All Logs**: Double-confirmation system for complete log cleanup
    - **Selective Delete**: Checkbox-based selection system for targeted deletion
    - Enhanced PHP backend with `delete_all` and `delete_selected` handlers
    - Added proper validation and user feedback messages
    - Implemented CSRF protection for secure deletion operations

  - **Advanced Pagination Control**: Flexible records per page filtering
    - Added records per page selector with options: 10, 50, 100, 150, 200, 500
    - Smart pagination URL handling preserving per_page parameters
    - Dynamic record information display showing "X to Y of Z records"
    - Automatic page reset to 1 when changing per_page value
    - Optimized database queries with proper LIMIT and OFFSET usage

  - **Enhanced User Interface**: Modern, responsive design improvements
    - Three-column control layout: Records selector | Select all | Delete buttons
    - Real-time checkbox synchronization between header and main select-all
    - Dynamic button state management (disabled when no selection)
    - Professional styling with gradients and hover effects
    - Mobile-responsive design maintaining usability across devices

- **Files Modified**:
  - `system/plugin/ui/whatsappGateway.tpl` - Complete UI overhaul with button optimization and status fix
  - `system/plugin/ui/whatsappGateway_logs.tpl` - New logs management interface with delete and pagination
  - `system/plugin/WhatsappGateway.php` - Enhanced backend with delete operations and pagination logic

- **Technical Enhancements**:
  - **JavaScript Improvements**: Modern ES6+ functions with proper error handling
  - **CSS Optimization**: Reduced redundancy and improved performance
  - **Database Operations**: Efficient bulk operations with proper validation
  - **Security**: CSRF protection and input validation for all operations
  - **Performance**: Optimized queries and reduced server load with pagination

- **User Experience Benefits**:
  - **Faster Navigation**: Compact buttons improve interface efficiency
  - **Better Log Management**: Easy bulk operations for large log volumes
  - **Flexible Viewing**: Customizable records per page for different use cases
  - **Reliable Status Checking**: Fixed continuous loading with proper feedback
  - **Professional Interface**: Modern design matching contemporary web standards

## [2.1.11] - 2025-07-27

### Voucher Revenue Tracking Fix
- **CRITICAL FIX**: Modified voucher activation to prevent adding revenue to dashboard
  - Fixed issue where voucher activations were incorrectly adding payment amounts to dashboard revenue
  - Updated `Package::rechargeUser()` function in `system/autoload/Package.php`
  - Changed voucher transaction logic to always set price to 0 for all voucher types
  - Removed dependency on specific voucher code patterns (User::isUserVoucher check)
  - Voucher activations now create transaction records with $0 price regardless of voucher format

- **Technical Changes**:
  - Modified transaction recording logic for gateway "Voucher" 
  - Simplified conditional logic to treat all vouchers as pre-paid (price = 0)
  - Applied fix to both active plan extension and new plan activation scenarios
  - Maintained transaction logging and tracking functionality
  - Preserved voucher activation workflow and customer service provisioning

- **Business Logic Improvement**:
  - Vouchers now correctly represent pre-paid services in revenue tracking
  - Dashboard revenue calculations exclude voucher activations as intended
  - Transaction records still maintain full audit trail of voucher usage
  - Revenue reporting now accurately reflects cash/payment gateway transactions only
  - Improved financial reporting accuracy for ISP operators

## [2.1.10] - 2025-07-24

### Customer CSV Upload Feature
- **NEW FEATURE**: Added CSV import functionality for bulk customer uploads
  - Created comprehensive CSV upload interface with drag-and-drop styling
  - CSV format validation with required and optional column detection
  - Duplicate prevention for usernames and phone numbers
  - Automatic password generation for imported customers
  - Real-time upload progress with error reporting

- **Files Created**:
  - `ui/ui/customers-upload.tpl` - Modern upload interface with format guidelines
  - `sample_customers.csv` - Downloadable sample CSV template for users

- **Enhanced Customer Management**:
  - Added "Upload CSV" button to main customers page with 3D styling
  - Updated `system/controllers/customers.php` with upload processing logic
  - Added `upload` and `upload_process` cases for file handling
  - Added `sample_csv` case for downloading template file
  - Enhanced error handling with detailed validation messages

- **Upload Features**:
  - Supports required fields: username, fullname, phonenumber
  - Optional fields: email, address, balance, service_type
  - CSV header validation with clear error messaging
  - Row-by-row processing with skip on errors
  - Comprehensive import summary with success/failure counts
  - File format validation (CSV only)
  - Default balance of 0.00 for new customers (unless specified)
  - Automatic password generation using system's secure method

- **Admin Interface Enhancements**:
  - Added modern upload area with visual feedback
  - Integrated sample CSV download functionality
  - Real-time file selection with visual confirmation
  - Detailed CSV format requirements table
  - Error display system for failed imports
  - Professional upload interface with tooltips and guidelines

- **Data Validation & Security**:
  - CSRF token protection for upload forms
  - Username uniqueness validation across existing customers
  - Phone number uniqueness validation
  - Service type validation with fallback to default
  - Proper error handling for malformed CSV data
  - Secure file upload processing with type validation

- **Integration Benefits**:
  - Seamless integration with existing customer management system
  - Compatible with all customer service types (Hotspot, PPPoE, VPN, Others)
  - Maintains existing data integrity and validation rules
  - No database schema changes required
  - Works with existing customer workflow and permissions

## [2.1.9] - 2025-07-23

### BytewaveSMS Gateway Integration
- **NEW SMS GATEWAY**: Added complete BytewaveSMS integration to the SMS Gateway system
  - Created comprehensive BytewaveSMS plugin with full API integration
  - API Endpoint: `https://portal.bytewavenetworks.com/api/v3/sms/send`
  - Bearer token authentication support
  - Balance checking functionality via API endpoint
  - Phone number auto-formatting for Kenya (+254) numbers

- **Files Created**:
  - `system/plugin/BytewaveSMSGateway.php` - Main plugin file with complete SMS functionality
  - `system/plugin/ui/smsGatewayBytewave.tpl` - Admin configuration interface
  - `system/plugin/bytewave_test.php` - Independent API testing script
  - `docs/bytewave_sms_setup.md` - Complete setup and troubleshooting guide

- **Enhanced SMS Gateway Manager**:
  - Updated `system/plugin/SMS_Gateway_Manager.php` to support BytewaveSMS routing
  - Added BytewaveSMS as third gateway option alongside Blessed Texts and Talk Sasa
  - Implemented priority-based routing system for three-gateway support
  - Enhanced fallback mechanism for improved reliability

- **Admin Interface Enhancements**:
  - Added "BytewaveSMS Gateway" menu item in admin panel
  - Real-time balance checking with AJAX functionality
  - SMS logs display showing last 10 messages with delivery status
  - Configuration page for API token and sender ID management
  - Updated Settings > SMS Notification dropdown to include BytewaveSMS option

- **Advanced Features Implemented**:
  - Duplicate message prevention using existing SMSLock system (5-minute window)
  - Comprehensive error handling and logging integration
  - Database logging with gateway identification and status tracking
  - Phone number validation and formatting for international standards
  - CURL error management with detailed response handling

- **Testing & Validation Tools**:
  - Independent test script for API validation without system dependencies
  - Balance check testing endpoint
  - Direct SMS sending validation
  - Legacy format compatibility for backward compatibility
  - Comprehensive error reporting and debugging capabilities

- **Integration Benefits**:
  - Seamless integration with existing customer notifications system
  - Compatible with payment confirmations and package expiry reminders
  - Works with OTP verification and bulk messaging features
  - Maintains existing duplicate prevention and logging infrastructure
  - No database schema changes required - uses existing `tbl_sms_logs` structure

## [2.1.8] - 2025-07-16

### Router Connectivity & Error Handling Improvements
- **MAJOR FIX**: Enhanced router connection resilience to prevent system crashes
  - Fixed "Could not connect to router after multiple attempts" errors that were crashing entire sync operations
  - Implemented robust error handling across all Mikrotik router operations
  - Added graceful degradation when individual routers are offline or unreachable
  - System now continues operating with available routers instead of complete failure

- **Enhanced MikrotikHotspot.php**
  - Added comprehensive error handling to `getClient()` method with 3 retry attempts and 5-second timeout
  - Enhanced all router operation methods with try-catch blocks:
    - `add_plan()`, `add_customer()`, `sync_customer()`, `remove_customer()`
  - Added router connectivity checking function for diagnostics
  - Implemented detailed logging for connection failures and successes
  - Added proper null checks and validation before router operations

- **Enhanced MikrotikPppoe.php**
  - Improved existing error handling in `getClient()` method
  - Added comprehensive error handling to all router operation methods:
    - `add_customer()`, `add_plan()`, `update_plan()`, `remove_plan()`
    - `add_pool()`, `update_pool()`, `remove_pool()`, `change_username()`
  - Added router connectivity checking function for diagnostics
  - Enhanced connection retry logic with better error reporting
  - Added proper router validation and IP address checking

- **Enhanced Controllers**
  - **services.php**: Added comprehensive error handling for hotspot and PPPoE service sync
    - Implemented success/failure counters with detailed reporting
    - Added visual indicators (✓ SUCCESS, ✗ FAILED) for operation status
    - Enhanced user feedback with summary statistics
  - **pool.php**: Enhanced pool sync operations with robust error handling
    - Added try-catch blocks around pool update operations
    - Implemented detailed status reporting (✓ SUCCESS, ✗ FAILED, ⚬ SKIPPED)
    - Added operation summaries with success/failure counts

- **New Diagnostic Tools**
  - Added `router_connectivity_check.php` - Backend API for testing router connectivity
  - Added `admin/router_diagnostic.html` - Visual diagnostic tool for administrators
    - Real-time router connectivity testing for all configured routers
    - Visual status indicators for PPPoE and Hotspot connections
    - Detailed error messages and connection status reporting
    - Easy-to-use interface for troubleshooting router issues

- **System Resilience Improvements**
  - Connection timeout set to 5 seconds to prevent hanging operations
  - 3 retry attempts with 2-second delays between connection attempts
  - Graceful failure handling - system continues with available routers
  - Enhanced logging system for better debugging and monitoring
  - Proper error propagation without system crashes

### Benefits
- ✅ **System Stability**: No more crashes from single router failures
- ✅ **Continued Operation**: Available routers keep working when others fail
- ✅ **Better Monitoring**: Clear visibility of router status and operation results
- ✅ **Easier Troubleshooting**: Diagnostic tools and detailed error logging
- ✅ **Graceful Degradation**: System operates with partial functionality when needed

### Files Modified
- `system/devices/MikrotikHotspot.php` - Enhanced error handling and connectivity checking
- `system/devices/MikrotikPppoe.php` - Improved error handling across all methods
- `system/controllers/services.php` - Added comprehensive sync error handling
- `system/controllers/pool.php` - Enhanced pool operations with error resilience
- `router_connectivity_check.php` - New diagnostic API endpoint
- `admin/router_diagnostic.html` - New diagnostic interface for administrators
- `ROUTER_FIX_SUMMARY.md` - Detailed documentation of all improvements

## [2.1.7] - 2025-07-10

### Customer Router Control Enhancement
- Added customer enable/disable functionality for Mikrotik router management
  - Implemented "Enable Customer" and "Disable Customer" actions in customer controller
  - Added enable/disable buttons to customer view page with proper styling
  - Added enable/disable options to customer actions dropdown menu
  - Enhanced router control interface for customers with and without connected devices
  - Supports both PPPoE and Hotspot customer types across multiple routers
  - Automatic customer detection across all configured routers
  - Proper error handling and user feedback with success/error messages
  - Integrated with existing Mikrotik API for seamless router communication

- Customer View Interface Improvements
  - Added prominent enable/disable/reconnect buttons in connected devices section
  - Enhanced customer actions dropdown with logical grouping and separators
  - Added router control section for customers without active connections
  - Implemented consistent 3D button styling with appropriate colors (green for enable, red for disable, orange for reconnect)
  - Added confirmation dialogs with descriptive messages for all router actions
  - Maintained existing functionality while adding new control features

- Mikrotik Integration Enhancements
  - Leveraged existing RouterOS API for customer enable/disable operations
  - Added proper connection cleanup when disabling customers
  - Enhanced error handling for router communication failures
  - Support for multiple router configurations with automatic failover
  - Integrated with existing customer synchronization and management system

### Files Modified
- `system/controllers/customers.php`
  - Added `enable` and `disable` actions for customer router control
  - Implemented multi-router customer detection and management
  - Added proper error handling and user feedback messaging
  - Enhanced security with CSRF token validation and permission checks
- `ui/ui/customers-view.tpl`
  - Added enable/disable buttons to connected devices section
  - Enhanced customer actions dropdown with new router control options
  - Added router control interface for customers without active connections
  - Implemented consistent styling and user experience improvements

## [2.1.6] - 2025-07-09

### Customer Management Enhancements
- Added "Extend" button functionality to customer profile pages
  - Implemented smart extend button in customer view page (customers-view.tpl)
  - Added extend functionality to both top action section and individual package sections
  - Button only appears when extend_expired configuration is enabled
  - Smart package selection automatically chooses active packages for extension
  - Enhanced JavaScript functions for seamless plan extension workflow
  - Maintained consistent 3D button styling throughout the interface

- Enhanced Plan List with Customer Full Name Column
  - Added "Full Name" column to the plan/list table for better customer identification
  - Implemented database join between tbl_user_recharges and tbl_customers tables
  - Added proper handling for voucher customers (displays "-" for non-customer accounts)
  - Enhanced backend query with LEFT OUTER JOIN for reliable data retrieval
  - Improved customer identification alongside existing username display
  - Maintained existing functionality while adding new identification features

### Files Modified
- `ui/ui/customers-view.tpl`
  - Added "Extend" buttons to top action section and package sections
  - Implemented configuration-aware button display logic
  - Added JavaScript functions for extend functionality
- `ui/ui/plan.tpl`
  - Added "Full Name" column header to plan list table
  - Implemented full name data display with proper fallback handling
- `system/controllers/plan.php`
  - Enhanced database query with customer table join
  - Added customer_fullname field to query results
  - Updated search and filter logic with qualified table names

## [2.1.5] - 2025-06-29

### MPesa Reconnect Feature Fix
- Fixed critical issue with "Reconnect with MPesa Code" functionality not properly logging in customers
  - Enhanced JavaScript response handling to correctly parse backend response structure
  - Fixed frontend form population and submission reliability
  - Improved error handling and debugging capabilities
  - Added support for both quick login and manual login forms
  - Implemented proper username field population across all form types
  - Added automatic password setting for manual login form reconnections
  - Enhanced cookie and localStorage persistence for user sessions
  - Added comprehensive console logging for easier troubleshooting

- Backend Integration Improvements
  - Validated CreateHotspotUser.php backend response format compatibility
  - Ensured proper handling of Resultcode, Message, and username fields
  - Maintained backward compatibility with existing payment verification system
  - Added robust validation to prevent empty username login attempts

- User Experience Enhancements
  - Automatic form submission after successful MPesa code validation
  - Improved error messaging for invalid transaction codes
  - Added visual feedback during reconnection process
  - Enhanced transaction code validation with proper format checking
  - Streamlined reconnection workflow for better user experience

### Files Modified
- `system/plugin/download.php`
  - Updated `reconnectWithMpesa()` JavaScript function with proper response handling
  - Enhanced form field population logic for all username inputs
  - Added automatic password setting for manual login forms
  - Implemented comprehensive error handling and logging
  - Added validation for username field presence before login attempts

## [2.1.4] - 2025-06-28

### Dashboard Expired Users Enhancement
- Added comprehensive expired users tracking to admin dashboard
  - New dashboard boxes for Expired PPPoE Users count
  - New dashboard boxes for Expired Hotspot Users count  
  - New dashboard boxes for Total Expired Users count
  - Modern styling with distinct colors (orange, red, gray respectively)
  - Interactive boxes with hover effects and proper icons
  - Direct navigation to filtered expired user lists

- Plan List Filtering Improvements
  - Enhanced plan list controller to support connection type filtering
  - Added new "Connection Type" dropdown filter (PPPoE/Hotspot)
  - Improved URL parameter handling for expired user filtering
  - Fixed filtering logic to properly handle expired status and user types
  - Added proper template support for type-based filtering

- Plugin Conflict Resolution
  - Fixed CreateHotspotUser plugin interference with plan list URLs
  - Resolved 400 "parameter not present in URL" errors
  - Improved plugin routing to only intercept intended requests
  - Enhanced plugin specificity to prevent global URL interception
  - Maintained plugin functionality while fixing conflicts

- Language Support
  - Added new language keys for expired user categories
  - Enhanced English language file with connection type translations
  - Improved internationalization support for new features

### Files Modified
- `system/controllers/dashboard.php`
  - Added expired users count calculations
  - Implemented database queries for PPPoE and Hotspot expired users
  - Enhanced template variable assignments
- `ui/ui/dashboard.tpl`
  - Added three new dashboard boxes for expired users
  - Implemented modern styling with gradient effects
  - Added proper navigation links to filtered lists
- `system/controllers/plan.php`
  - Enhanced filtering functionality with type parameter support
  - Improved URL parameter handling and validation
  - Added connection type filtering to database queries
- `ui/ui/plan.tpl`
  - Added Connection Type filter dropdown
  - Enhanced form controls for better filtering
  - Improved user interface for plan management
- `system/lan/english.json`
  - Added expired user category translations
  - Added connection type filter translations
- `system/plugin/CreateHotspotUser.php`
  - Fixed global URL interception issue
  - Improved plugin routing specificity
  - Enhanced request validation logic

## [2.1.3] - 2025-06-16

### Logs Interface Modernization
- Complete redesign of Mikrotik Logs and SpeedRadius Logs pages
  - Modern UI with gradient headers and improved typography
  - Enhanced table design with hover effects and better spacing
  - Improved status indicators with animations
  - Better visual hierarchy and organization
  - Fully responsive design for all devices

- Search Functionality Improvements
  - Enhanced search capabilities across multiple columns
  - Real-time search feedback
  - Improved search input design with clear visual feedback
  - Added search across description, IP, type, and userid fields
  - Better error handling for search queries

- Log Management Enhancements
  - Improved log cleanup functionality with validation
  - Enhanced CSV export button design
  - Added proper confirmation dialogs
  - Improved date/time display format
  - Better error handling and user feedback

- Security Improvements
  - Added CSRF token protection for forms
  - Implemented parameterized queries for search
  - Added input validation for log cleanup
  - Improved error handling and logging

### Files Modified
- `system/plugin/ui/log.tpl`
  - Modernized Mikrotik logs interface
  - Added responsive design elements
  - Enhanced status indicators
- `ui/ui/logs.tpl`
  - Redesigned SpeedRadius logs interface
  - Improved search functionality
  - Enhanced table design
- `system/controllers/logs.php`
  - Enhanced search functionality
  - Improved log cleanup validation
  - Added security measures

## [2.1.2] - 2025-06-15

### Voucher Management Page Enhancement
- Complete UI/UX overhaul of the voucher management page
  - Added modern styling with consistent color scheme
  - Enhanced table design with better spacing and typography
  - Improved status indicators and button designs
  - Added tooltips for better user guidance
  - Implemented responsive design for all screen sizes

- Functionality Improvements
  - Fixed "Delete Used Vouchers" functionality
  - Added proper confirmation dialogs for bulk actions
  - Enhanced error handling and user feedback
  - Improved date/time display format with AM/PM
  - Added visual feedback for row selection

- Code Structure and Performance
  - Refactored voucher listing code for better maintainability
  - Optimized database queries for voucher management
  - Added proper error handling in the backend
  - Improved template syntax and JavaScript organization

### Files Modified
- `system/controllers/plan.php`
  - Added remove-used-vouchers functionality
  - Enhanced voucher query optimization
  - Improved error handling
- `ui/ui/voucher.tpl`
  - Complete template modernization
  - Added enhanced styling
  - Improved JavaScript functionality
- `system/autoload/Lang.php`
  - Updated date format to include AM/PM

## [2.1.1] - 2025-06-14

### SMS System Improvements
- Implemented robust SMS duplicate prevention system
  - New Files Created:
    - `system/helpers/SMSLock.php`: Core locking mechanism to prevent duplicate SMS messages
    - `system/cache/sms_locks/`: Directory for storing temporary SMS lock files
  - Modified Files:
    - `system/plugin/SMS_Gateway_Manager.php`: Added SMSLock integration
    - `system/plugin/BlessedTextsGateway.php`: Implemented duplicate prevention
    - `system/plugin/TalkSasaGateway.php`: Implemented duplicate prevention
  - Features:
    - File-based locking mechanism for reliable duplicate detection
    - 5-minute duplicate prevention window
    - Integrated with both Blessed Texts and Talk Sasa gateways
    - Reduced SMS credit usage by preventing duplicate messages
    - Automatic lock cleanup mechanism
    - Enhanced logging for duplicate detection events

## [2.1.0] - 2025-06-15

### Voucher Printing Page Modernization
- Comprehensive UI/UX Overhaul
  - Redesigned control panel with compact, efficient layout
  - Added stylish page title with animated ticket icon
  - Implemented gradient accents and modern visual effects
  - Enhanced form controls with improved usability
  - Optimized spacing and typography for better readability

- Voucher Grid Improvements
  - Implemented 3-column voucher layout for better space utilization
  - Enhanced voucher card design with modern styling
  - Added responsive breakpoints for different screen sizes
  - Optimized print layout for professional output
  - Improved page break handling for better printing results

- Form Controls Enhancement
  - Streamlined input fields with clear labels
  - Added visual feedback for form interactions
  - Implemented better validation with error messages
  - Enhanced button styling and interactions
  - Added loading states for better user feedback

- Print Optimization
  - Improved print layout with consistent 3-column grid
  - Enhanced spacing and margins for printed output
  - Added print-specific styling for professional results
  - Optimized page breaks and voucher distribution
  - Maintained clean formatting in print preview

## [2.1.0] - 2025-06-05

### Customer View Interface Improvements
- Restructured Recharge Button Location
  - Moved recharge buttons outside of package-dependent section
  - Made recharge functionality always accessible
  - Ensured buttons visibility regardless of package status
  - Added persistent top placement for better accessibility
  - Maintained consistent user experience across states
- Mobile UI Optimization
  - Removed btn-lg class for smaller button size
  - Added btn-sm class for better mobile usability
  - Changed grid classes from col-sm-6 to col-xs-6
  - Improved touch targets and spacing
  - Enhanced mobile responsiveness
- System Architecture Updates
  - Decoupled recharge UI from package status
  - Preserved CSRF security implementation
  - Maintained conditional balance feature
  - Kept all security checks intact
  - Improved overall user flow

## [2.1.0] - 2025-05-29

### Mpesa Transactions Page Enhancement
- Added modern 3D button styling with gradient effects
- Improved search interface with enhanced visual feedback
- Added loading spinners for better user experience
- Enhanced table design with hover effects and animations
- Implemented professional pagination system
- Added responsive form controls with modern styling
- Improved overall visual hierarchy and organization

### Router Status Monitoring System Overhaul
- Fixed inconsistent online/offline status switching issue
- Implemented smart retry mechanism (2 retries before marking offline)
- Added status state management to prevent false offline reports
- Improved connection stability with 10-second timeout
- Enhanced error detection and recovery
- Added quick retry (2 seconds) for temporary failures
- Implemented graduated response to connection failures

### Router Status Updates and Display
- Fixed "Last Seen" timestamp synchronization
- Added real-time status updates with smart polling
- Implemented intelligent update intervals (30s for online, 60s for offline)
- Added visual transition effects for status changes
- Improved status accuracy with verification system
- Enhanced error reporting and user feedback
- Added connection quality monitoring

### UI Improvements
- Added modern 3D styling with gradient effects
- Improved visual feedback for status changes
- Added hover effects and animations for better interactivity
- Implemented responsive design for all screen sizes
- Added pulse animations for metric updates

### Performance
- Optimized status checking intervals
- Added smart retry delays for offline routers
- Improved memory usage tracking
- Enhanced CPU load monitoring
- Added efficient state caching

### Bug Fixes
- Fixed inconsistent online/offline status display
- Fixed timestamp synchronization issues
- Resolved status flickering during updates
- Fixed dark mode persistence issues
- Improved error handling for timeout scenarios

## 2025.5.28

- Fixed Notification System
  - Fixed sendBalanceNotification to properly handle both SMS and WhatsApp notifications
  - Updated cron_reminder.php to use correct reminder_notification setting
  - Fixed expiration notifications in cron.php
  - Added proper logging for notification tracking
  - Fixed price calculation in expired notifications
  - Improved error handling for expired customer processing
  - Added debugging information for notification failures
  - Ensured notifications are sent before auto-renewal processing
  - Aligned notification settings with UI configuration
  - Fixed reminder notifications to use proper channel settings
  - Improved reliability of dual-channel notifications

- Enhanced WhatsApp Gateway Interface
  - Improved QR code and pair code display with modern design
  - Added automatic status checking with visual feedback
  - Implemented efficient caching system for faster status updates
  - Added loading spinners and progress indicators
  - Enhanced error handling and user feedback
  - Optimized backend performance for status checks
  - Added 3D button effects and modern UI elements
  - Improved connection status display

## 2025.5.22

- Enhanced Customer Management Interface
  - Added 3D-styled Add Customer button with interactive effects
  - Implemented service type filter (PPPoE/Hotspot/VPN/Others)
  - Added colored service type badges (Green for PPPoE, Blue for Hotspot, Purple for VPN)
  - Reorganized customer management buttons layout
  - Improved visual hierarchy in customer listing
  - Added smooth hover and click animations
  - Enhanced button accessibility

- Added Dynamic Time-based Greeting to Dashboard
  - Added modern 3D-styled greeting box with animations
  - Dynamic company name detection from system settings
  - Time-based icons (sun/moon) that change throughout the day
  - Responsive design with hover effects
  - Automatic updates every minute
  - Smooth transitions and modern gradients
  - Neumorphic design elements

## 2025.5.14

- Added Live Traffic Monitor Plugin
  - Real-time interface traffic monitoring
  - Supports multiple routers and interfaces
  - Interactive graph with upload/download speeds
  - Automatic unit conversion (bps to Gbps)
  - Interface selection dropdown
  - Live speed indicators
  - Mobile responsive design
  - Error handling and reconnection
  
- Fixed SMS and WhatsApp Duplicate Messages Issue
  - Added duplicate message prevention system for all gateways
  - Implemented 1-minute cooldown between identical messages
  - Added message tracking and improved logging
  - Enhanced error handling for all gateways
  - Fixed multiple gateway conflicts
  - Improved message delivery reliability
- Fixed Database Schema
  - Added default value 'pending' to tbl_sms_logs.status field
  - SQL code : ALTER TABLE tbl_sms_logs MODIFY COLUMN status varchar(20) NOT NULL DEFAULT 'pending'
  - Prevents SQL errors when inserting new message records

## 2025.5.13

- Added SMS Gateway Management System
  - Implemented gateway switching between Talk Sasa and Blessed Texts
  - Added dropdown selection in SMS Notification settings
  - Created unified gateway manager with priority-based routing
  - Added separate configuration pages for each gateway
  - Improved SMS delivery reliability
  - Added comprehensive HTML documentation
  - Fixed issues with multiple gateway conflicts

## 2024.10.23

- Custom Balance admin refill Requested by Javi Tech
- Only Admin can edit Customer Requested by Fiberwan
- Only Admin can show password Requested by Fiberwan

## 2024.10.18

- Single Session Admin Can be set in the Settings
- Auto expired unpaid transaction
- Registration Type
- Can Login as User from Customer View
- Can select customer register must using OTP or not
- Add Meta.php for additional information

## 2024.10.15

- CSRF Security
- Admin can only have 1 active session
- Move Miscellaneous Settings to new page
- Fix Customer Online
- Count Shared user online for Radius REST
- Fix Invoice Print

## 2024.10.7

- Show Customer is Online or not
- Change Invoice Theme for printing
- Rearange Customer View

## 2024.9.23

- Discount Price
- Burst Preset

## 2024.9.20

- Forgot Password
- Forgot Username
- Public header template

## 2024.9.13

- Add Selling Mikrotik VPN By @agstrxyz
- Theme Redesign by @Focuslinkstech
- Fix That and this


## 2024.8.28

- add Router Status Offline/Online by @Focuslinkstech
- Show Router Offline in the Dashbord
- Fix Translation by by @ahmadhusein17
- Add Payment Info Page, to show to customer before buy
- Voucher Template
- Change Niceedit to summernote
- Customer can change their language by @Focuslinkstech
- Fix Voucher case sensitive
- 3 Tabs Plugin Manager

## 2024.8.19

- New Page, Payment Info, To Inform Customer, which payment gateway is good
- Move Customer UI to user-ui folder
- Voucher Template
- Change editor to summernote
- Customer can change language

## 2024.8.6

- Fix QRCode Scanner
- Simplify Chap verification password
- Quota based Freeradius Rest
- Fix Payment Gateway Audit

## 2024.8.6

- Fix Customer pppoe username

## 2024.8.5

- Add Customer Mail Inbox
- Add pppoe customer and pppoe IP to make static username and IP
- Add Sync button
- Allow Mac Address Username
- Router Maps

## 2024.8.1

- Show Bandwidth Plan in the customer dashboard
- Add Audit Payment Gateway
- Fix Plugin Manager

## 2024.7.23

- add Voucher Used Date
- Reports page just 1 for all
- fix start date at dashboard
- fix installation parameter

## 2024.7.23

- Add Additional Bill Info to Customer
- Add Voucher only Login, without username
- Add Additional Bill info to Mikrotik Comment
- Add dynamic Application URL For Installation
- Fix Active Customers for Voucher

## 2024.7.15

- Radius Rest API
- Getting Started Documentation
- Only Show new update just once

## 2024.6.21

- Add filter result in voucher and internet plan
- Add input script on-login and on-logout
- Add local ip for pppoe

## 2024.6.19

- new system for device, it can support non mikrotik devices, as long someone create device file
- add local ip in the pool
- Custom Fix Expired Date for postpaid
- Expired customer can move to another Internet Plan
- Plugin installer
- refresh plugin manager cache
- Docker File by George Njeri (@Swagfin)

## 2024.5.21

- Add Maintenance Mode by @freeispradius
- Add Tax System by @freeispradius
- Add Export Customer List to CSV with Filter
- Fix some Radius Variable by @freeispradius
- Add Rollback update

## 2024.5.17

- Status Customer: Active/Banned/Disabled
- Add search with order in Customer list

## 2024.5.16

- Confirm can change Using

## 2024.5.14

- Show Plan and Location on expired list
- Customizeable payment for recharge

## 2024.5.8

- Fix bugs burst by @Gerandonk
- Fix sync for burst by @Gerandonk

## 2024.5.7

- Fix time for period Days
- Fix Free radius attributes by @agstrxyz
- Add Numeric Voucher Code by @pro-cms

## 2024.4.30

- CRITICAL UPDATE: last update Logic recharge not check is status on or off, it make expired customer stay in expired pool
- Prevent double submit for recharge balance

## 2024.4.29

- Maps Pagination
- Maps Search
- Fix extend logic
- Fix logic customer recharge to not delete when customer not change the plan

## 2024.4.23

- Fix Pagination Voucher
- Fix Languange Translation
- Fix Alert Confirmation for requesting Extend
- Send Telegram Notification when Customer request to extend expiration
- prepaid users export list by @freeispradius
- fix show voucher by @agstrxyz

## 2024.4.21

- Restore old cron

## 2024.4.15

- Postpaid Customer can request extends expiration day if it enabled
- Some Code Fixing by @ahmadhusein17 and @agstrxyz

## 2024.4.4

- Data Tables for Customers List by @Focuslinkstech
- Add Bills to Reminder
- Prevent double submit for recharge and renew

## 2024.4.3

- Export logs to CSV by @agstrxyz
- Change to Username if Country code empty

## 2024.4.2

- Fix REST API
- Fix Log IP Cloudflare by @Gerandonk
- Show Personal or Business in customer dashboard

## 2024.3.26

- Change paginator, to make easy customization using pagination.tpl

## 2024.3.25

- Fix maps on HTTP
- Fix Cancel payment

## 2024.3.23

- Maps full height
- Show Get Directions instead Coordinates
- Maps Label always show

## 2024.3.22

- Fix Broadcast Message by @Focuslinkstech
- Add Location Picker

## 2024.3.20

- Fixing some bugs

## 2024.3.19

- Add Customer Type Personal or Bussiness by @pro-cms
- Fix Broadcast Message by @Focuslinkstech
- Add Customer Geolocation by @Focuslinkstech
- Change Customer Menu

## 2024.3.18

- Add Broadcasting SMS by @Focuslinkstech
- Fix Notification with Bills

## 2024.3.16

- Fix Zero Charging
- Fix Disconnect Customer from Radius without loop by @Gerandonk

## 2024.3.15

- Fix Customer View to list active Plan
- Additional Bill using Customer Attributes

## 2024.3.14

- Add Note to Invoices
- Add Additional Bill
- View Invoice from Customer side

## 2024.3.13

- Postpaid System
- Additional Cost

## 2024.3.12

- Check if Validity Period, so calculate price will not affected other validity
- Add firewall using .htaccess for apache only
- Multiple Payment Gateway by @Focuslinkstech
- Fix Logic Multiple Payment gateway
- Fix delete Attribute
- Allow Delete Payment Gateway
- Allow Delete Plugin

## 2024.3.6

- change attributes view

## 2024.3.4

- add [[username]] for reminder
- fix agent show when editing
- fix password admin when sending notification
- add file exists for pages

## 2024.3.3

- Change loading button by @Focuslinkstech
- Add Customer Announcements by @Gerandonk
- Add PPPOE Period Validity by @Gerandonk

## 2024.2.29

- Fix Hook Functionality
- Change Customer Menu

## 2024.2.28

- Fix Buy Plan with Balance
- Add Expired date for reminder

## 2024.2.27

- fix path notification
- redirect to dashboard if already login

## 2024.2.26

- Clean Unused JS and CSS
- Add some Authorization check
- Custom Path for folder
- fix some bugs

## 2024.2.23

- Integrate with PhpNuxBill Printer
- Fix Invoice
- add admin ID in transaction

## 2024.2.22

- Add Loading when click submit
- link to settings when hide widget

## 2024.2.21

- Fix SQL Installer
- remove multiple space in language
- Change Phone Number require OTP by @Focuslinkstech
- Change burst Form
- Delete Table Responsive, first Column Freeze

## 2024.2.20

- Fix list admin
- Burst Limit
- Pace Loading by @Focuslinkstech

## 2024.2.19

- Start API Development
- Multiple Admin Level
- Customer Attributes by @Focuslinkstech
- Radius Menu

## 2024.2.13

- Auto translate language
- change language structur to json
- save collapse menu

## 2024.2.12

- Admin Level : SuperAdmin,Admin,Report,Agent,Sales
- Export Customers to CSV
- Session using Cookie

## 2024.2.7

- Hide Dashboard content

## 2024.2.6

- Cache graph for faster opening graph

## 2024.2.5

- Admin Dashboard Update
  - Add Monthly Registered Customers
  - Total Monthly Sales
  - Active Users

## 2024.2.2

- Fix edit plan for user

## 2024.1.24

- Add Send test for SMS, Whatsapp and Telegram

## 2024.1.19

- Paid Plugin, Theme, and payment gateway marketplace using codecanyon.net
- Fix Plugin manager List

## 2024.1.18

- fix(mikrotik): set pool $poolId always empty

## 2024.1.17

- Add minor change, for plugin, menu can have notifications by @Focuslinkstech

## 2024.1.16

- Add yellow color to table for plan not allowed to purchase
- Fix Radius pool select
- add price to reminder notification
- Support thermal printer for invoice

## 2024.1.15

- Fix cron job for Plan only for admin by @Focuslinkstech

## 2024.1.11

- Add Plan only for admin by @Focuslinkstech
- Fix Plugin Manager

## 2024.1.9

- Add Prefix when generate Voucher

## 2024.1.8

- User Expired Order by Expired Date

## 2024.1.2

- Pagination User Expired by @Focuslinkstech

## 2023.12.21

- Modern AdminLTE by @sabtech254
- Update user-dashboard.tpl by @Focuslinkstech

## 2023.12.19

- Fix Search Customer
- Disable Registration, Customer just activate voucher Code, and the voucher will be their password
- Remove all used voucher codes

## 2023.12.18

- Split sms to 160 characters only for Mikrotik Modem

## 2023.12.14

- Can send SMS using Mikrotik with Modem Installed
- Add Customer Type, so Customer can only show their PPPOE or Hotspot Package or both

## 2023.11.17

- Error details not show in Customer

## 2023.11.15

- Customer Multi Router package
- Fix edit package, Admin can change Customer to another router

## 2023.11.9

- fix bug variable in cron
- fix update plan

## 2023.10.27

- Backup and restore database
- Fix checking radius client

## 2023.10.25

- fix wrong file check in cron, error only for newly installed

## 2023.10.24

- Fix logic cronjob
- assign router to NAS, but not yet used
- Fix Pagination
- Move Alert from hardcode

## 2023.10.20

- View Invoice
- Resend Invoice
- Custom Voucher

## 2023.10.17

- Happy Birthday To Me 🎂 \(^o^)/
- Support FreeRadius with Mysql
- Bring back Themes support
- Log Viewer

## 2023.9.21

- Customer can extend Plan
- Customer can Deactivate active plan
- add variable nux-router to select  only plan from that router
- Show user expired until 30 items

## 2023.9.20

- Fix Customer balance header
- Deactivate Customer active plan
- Sync Customer Plan to Mikrotik
- Recharge Customer from Customer Details
- Add Privacy Policy and Terms and Conditions Pages

## 2023.9.13

- add Current balance in notification
- Buy Plan for Friend
- Recharge Friend plan
- Fix recharge Plan
- Show Customer active plan in Customer list
- Fix Customer counter in dashboard
- Show Customer Balance in header
- Fix Plugin Manager using Http::Get
- Show Some error page when crash
## 2023.9.7

- Fix PPPOE Delete Customer
- Remove active Customer before deleting
- Show IP and Mac even if it not Hotspot

## 2023.9.6

- Expired Pool
Customer can be move to expired pool after plan expired by cron
- Fix Delete customer
- tbl_language removed

## 2023.9.1.1

- Fix cronjob Delete customer
- Fix reminder text

## 2023.9.1

- Critical bug fixes, bug happen when user buy package, expired time will be calculated from last expired, not from when they buy the package
- Time not change after user buy package for extending
- Add Cancel Button to user dashboard when it show unpaid package
- Fix username in user dashboard

## 2023.8.30

- Upload Logo from settings
- Fix Print value
- Fix Time when editing prepaid

## 2023.8.28

- Extend expiration if buy same package
- Fix calendar
- Add recharge time
- Fix allow balance transfer

## 2023.8.24

- Balance transfer between Customer
- Optimize Cronjob
- View Customer Info
- Ajax for select customer

## 2023.8.18

- Fix Auto Renewall Cronjob
- Add comment to Mikrotik User

## 2023.8.16

- Admin Can Add Balance to Customer
- Show Balance in user
- Using Select2 for Dropdown

## 2023.8.15

- Fix PPPOE Delete Customer
- Fix Header Admin and Customer
- Fix PDF Export by Period
- Add pppoe_password for Customer, this pppoe_password only admin can change
- Country Code Number Settings
- Customer Meta Table for Customers Attributess
- Fix Add and Edit Customer Form for admin
- add Notification Message Editor
- cron reminder
- Balance System, Customer can deposit money
- Auto renewal when package expired using Customer Balance


## 2023.8.1

- Add Update file script, one click updating PHPNuxBill
- Add Custom UI folder, to custome your own template
- Delete debug text
- Fix Vendor JS

## 2023.7.28

- Fix link buy Voucher
- Add email field to registration form
- Change registration design Form
- Add Setting to disable Voucher
- Fix Title for PPPOE plans
- Fix Plugin Cache
## 2023.6.20

- Hide time for Created date.
  Because the first time phpmixbill created, plan validity only for days and Months, many request ask for minutes and hours, i change it, but not the database.
## 2023.6.15

- Customer can connect to internet from Customer Dashboard
- Fix Confirm when delete
- Change Logo PHPNuxBill
- Using Composer
- Fix Search Customer
- Fix Customer check, if not found will logout
- Customer password show but hidden
- Voucher code hidden

## 2023.6.8

- Fixing registration without OTP
- Username will not go to phonenumber if OTP registration is not enabled
- Fix Bug PPOE

## [2.1.0] - 2025-05-29

### Router Status Monitoring System Overhaul
- Fixed inconsistent online/offline status switching issue
- Implemented smart retry mechanism (2 retries before marking offline)
- Added status state management to prevent false offline reports
- Improved connection stability with 10-second timeout
- Enhanced error detection and recovery
- Added quick retry (2 seconds) for temporary failures
- Implemented graduated response to connection failures

### Router Status Updates and Display
- Fixed "Last Seen" timestamp synchronization
- Added real-time status updates with smart polling
- Implemented intelligent update intervals (30s for online, 60s for offline)
- Added visual transition effects for status changes
- Improved status accuracy with verification system
- Enhanced error reporting and user feedback
- Added connection quality monitoring

### UI Improvements
- Added modern 3D styling with gradient effects
- Improved visual feedback for status changes
- Added hover effects and animations for better interactivity
- Implemented responsive design for all screen sizes
- Added pulse animations for metric updates

### Performance
- Optimized status checking intervals
- Added smart retry delays for offline routers
- Improved memory usage tracking
- Enhanced CPU load monitoring
- Added efficient state caching

### Bug Fixes
- Fixed inconsistent online/offline status display
- Fixed timestamp synchronization issues
- Resolved status flickering during updates
- Fixed dark mode persistence issues
- Improved error handling for timeout scenarios

## [2.1.0] - 2025-06-15

### Voucher Printing Page Modernization
- Comprehensive UI/UX Overhaul
  - Redesigned control panel with compact, efficient layout
  - Added stylish page title with animated ticket icon
  - Implemented gradient accents and modern visual effects
  - Enhanced form controls with improved usability
  - Optimized spacing and typography for better readability

- Voucher Grid Improvements
  - Implemented 3-column voucher layout for better space utilization
  - Enhanced voucher card design with modern styling
  - Added responsive breakpoints for different screen sizes
  - Optimized print layout for professional output
  - Improved page break handling for better printing results

- Form Controls Enhancement
  - Streamlined input fields with clear labels
  - Added visual feedback for form interactions
  - Implemented better validation with error messages
  - Enhanced button styling and interactions
  - Added loading states for better user feedback

- Print Optimization
  - Improved print layout with consistent 3-column grid
  - Enhanced spacing and margins for printed output
  - Added print-specific styling for professional results
  - Optimized page breaks and voucher distribution
  - Maintained clean formatting in print preview