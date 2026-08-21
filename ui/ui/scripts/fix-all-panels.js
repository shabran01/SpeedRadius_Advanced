// This script runs in the global scope and converts all collapsed panels to visible panels
// It's specifically targeting the blank tabs issue in the settings page
(function() {
    // Run on page load and again after a slight delay to catch any late-rendered elements
    function convertAllPanels() {
        console.log('Converting all collapsed panels to visible panels');
        
        // Get all collapsed panels
        var collapsedPanels = document.querySelectorAll('.panel-collapse, [class*="collapse"], [id^="collapse"]');
        
        // Convert each one
        for(var i = 0; i < collapsedPanels.length; i++) {
            var panel = collapsedPanels[i];
            
            // Remove collapse-related classes
            panel.classList.remove('collapse');
            panel.classList.remove('panel-collapse');
            
            // Add visible classes
            panel.classList.add('panel-body-visible');
            
            // Force display using inline styles
            panel.style.display = 'block';
            panel.style.height = 'auto';
            panel.style.visibility = 'visible';
            panel.style.opacity = '1';
        }
    }
    
    // Run the conversion immediately when this script loads
    convertAllPanels();
    
    // Run again after DOM is fully loaded
    document.addEventListener('DOMContentLoaded', convertAllPanels);
    
    // Run again after a delay to catch any dynamically loaded content
    setTimeout(convertAllPanels, 500);
    setTimeout(convertAllPanels, 1000);
})();
