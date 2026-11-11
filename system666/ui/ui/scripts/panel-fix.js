// Direct fix for API Key tab and other tabs
jQuery(document).ready(function($) {
    // Only run on settings/app page
    if (window.location.href.indexOf('settings/app') > -1) {
        // Fix all collapsed panels 
        var problemPanels = [
            // Originally identified panels
            '#collapseAPIKey', 
            '#collapseProxy', 
            '#collapseTaxSystem', 
            '#collapseGeneral',
            '#collapseAuthentication',
            // Additional panels
            '#collapseHideDashboardContent',
            '#collapseRegistration',
            '#collapseSecurity',
            '#collapseVoucher',
            '#collapseFreeRadius',
            '#collapseExtendPostpaidExpiration',
            '#collapseCustomerBalanceSystem',
            '#collapseTelegramNotification',
            '#collapseSMSNotification',
            '#collapseWhatsappNotification',
            '#collapseEmailNotification',
            '#collapseUserNotification',
            '#collapseTawkToChatWidget'
        ];
        
        // First remove collapse classes
        $(problemPanels.join(', ')).each(function() {
            $(this).removeClass('collapse panel-collapse').addClass('panel-body-visible');
        });
        
        // Force display style directly
        $(problemPanels.join(', ')).css({
            'display': 'block',
            'height': 'auto',
            'visibility': 'visible',
            'opacity': '1'
        });
        
        // Add click handlers to panel headings for navigation
        $('.panel-heading').click(function() {
            var panelId = $(this).next('.panel-body-visible').attr('id');
            if (panelId) {
                $('html, body').animate({
                    scrollTop: $('#' + panelId).offset().top - 80
                }, 300);
            }
        });
        
        // Fix specific issue with API Key panel
        setTimeout(function() {
            $('#collapseAPIKey').html($('#collapseAPIKey').html());
        }, 100);
        
        // Direct panel fix applied
        console.log('Direct panel fix applied');
        
        // Refresh button removed as requested
        // Focus on ensuring panels display properly without needing a manual refresh
    }
});
