// Script has been disabled - refresh button removed as requested
document.addEventListener('DOMContentLoaded', function() {
    // Only run on settings/app page
    if (window.location.href.indexOf('settings/app') > -1) {
        // Remove any existing refresh buttons
        var existingButtons = document.querySelectorAll('.compact-refresh-btn');
        for (var i = 0; i < existingButtons.length; i++) {
            if (existingButtons[i] && existingButtons[i].parentNode) {
                existingButtons[i].parentNode.removeChild(existingButtons[i]);
            }
        }
    }
});
