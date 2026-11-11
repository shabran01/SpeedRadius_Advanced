// Wait for jQuery to be available
var checkJquery = setInterval(function() {
    if (typeof jQuery !== 'undefined') {
        clearInterval(checkJquery);
        
        // Apply the fix once jQuery is loaded
        jQuery(document).ready(function($) {
            // Only target the settings/app page
            if (window.location.href.indexOf('settings/app') > -1) {
                console.log('Applying comprehensive fix to settings panels');
                
                // Force all panels to be visible
                $('.panel-collapse').each(function() {
                    $(this).removeClass('panel-collapse collapse').addClass('panel-body-visible');
                    $(this).css({
                        'display': 'block',
                        'height': 'auto',
                        'visibility': 'visible',
                        'opacity': '1'
                    });
                });
                
                // Convert the Bootstrap toggle functionality to simple display toggling
                $('.panel-heading').off('click').on('click', function() {
                    var panelId = $(this).next('.panel-body-visible').attr('id');
                    if (panelId) {
                        $('html, body').animate({
                            scrollTop: $('#' + panelId).offset().top - 80
                        }, 300);
                    }
                });
                
                // Add a help message at the top of the page
                var helpMessage = $('<div class="alert alert-info">' + 
                    '<strong>Navigation:</strong> Click on the section headers to quickly navigate to each section.' +
                    '</div>');
                $('.content-header').after(helpMessage);
                
                // Refresh button removed as requested
                // Panel fixing is now handled automatically without requiring manual refresh
            }
        });
    }
}, 100);

// Fallback solution - directly manipulate the DOM without jQuery
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.href.indexOf('settings/app') > -1) {
        console.log('Applying fallback panel fix');
        
        // Force all panels to be visible
        var panels = document.querySelectorAll('.panel-collapse, [id^="collapse"]');
        for (var i = 0; i < panels.length; i++) {
            var panel = panels[i];
            panel.classList.remove('panel-collapse');
            panel.classList.remove('collapse');
            panel.classList.add('panel-body-visible');
            panel.style.display = 'block';
            panel.style.height = 'auto';
            panel.style.visibility = 'visible';
            panel.style.opacity = '1';
        }
    }
});
