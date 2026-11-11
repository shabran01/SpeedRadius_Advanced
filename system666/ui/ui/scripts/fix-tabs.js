// Aggressive fix for settings/app tabs to display all content regardless of collapse state
$(document).ready(function() {
    // Only apply to settings/app page
    if (window.location.href.indexOf('settings/app') > -1) {
        // First approach: Force all panels to be visible without collapse functionality
        $('.panel-collapse').removeClass('collapse').addClass('in');
        
        // Second approach: Remove collapse functionality entirely and convert to static panels
        $('.panel-collapse').each(function() {
            $(this).removeClass('panel-collapse collapse').addClass('panel-body-visible');
        });

        // Third approach: For tabs that still don't show content, force display style
        setTimeout(function() {
            $('.panel-collapse, #collapseAPIKey, #collapseProxy, #collapseTaxSystem, #collapseAuthentication, #collapseGeneral').each(function() {
                $(this).css('display', 'block').removeClass('collapse').addClass('in');
            });
        }, 500);
        
        // Create quick navigation links at the top (outside original tab structures)
        var quickNav = $('<div class="quick-nav" style="margin-bottom: 20px;"><strong>Quick Navigation: </strong></div>');
        $('.panel-title a').each(function(index) {
            var linkText = $(this).text().trim();
            var targetId = $(this).attr('href');
            var link = $('<a href="javascript:void(0);" style="margin: 0 10px; text-decoration: underline;" data-target="' + targetId + '">' + linkText + '</a>');
            quickNav.append(link);
            
            // Add click event to scroll to the section
            link.on('click', function() {
                var target = $($(this).data('target'));
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 300);
                }
            });
        });
        
        // Add the quick nav to the page
        $('.content-header').after(quickNav);
        
        // Make sure refresh button is small and discreet if it exists
        $('button[style*="position: fixed"]').each(function() {
            $(this).removeClass('btn-lg').addClass('btn-sm');
            $(this).css({
                'padding': '3px 8px', 
                'font-size': '11px',
                'opacity': '0.8'
            });
            $(this).text('Refresh');
        });
    }
});
