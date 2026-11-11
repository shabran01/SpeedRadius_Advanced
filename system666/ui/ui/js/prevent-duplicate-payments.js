/**
 * Frontend Duplicate Transaction Prevention
 * Add this script to your payment pages to prevent users from submitting multiple times
 */

(function() {
    'use strict';
    
    // Prevent double submission of payment forms
    function preventDoubleSubmission() {
        const paymentForms = document.querySelectorAll('form[action*="plugin/initiate"], form[action*="payment"], form[action*="order"]');
        const paymentButtons = document.querySelectorAll('button[type="submit"], input[type="submit"]');
        
        paymentForms.forEach(form => {
            let submitted = false;
            
            form.addEventListener('submit', function(e) {
                if (submitted) {
                    e.preventDefault();
                    return false;
                }
                
                submitted = true;
                
                // Disable all submit buttons in the form
                const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                submitButtons.forEach(button => {
                    button.disabled = true;
                    const originalText = button.textContent || button.value;
                    button.setAttribute('data-original-text', originalText);
                    
                    if (button.tagName === 'BUTTON') {
                        button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
                    } else {
                        button.value = 'Processing...';
                    }
                });
                
                // Re-enable buttons after 10 seconds in case of error
                setTimeout(() => {
                    submitted = false;
                    submitButtons.forEach(button => {
                        button.disabled = false;
                        const originalText = button.getAttribute('data-original-text');
                        if (button.tagName === 'BUTTON') {
                            button.innerHTML = originalText;
                        } else {
                            button.value = originalText;
                        }
                    });
                }, 10000);
            });
        });
    }
    
    // Prevent rapid clicking on payment buttons
    function preventRapidClicking() {
        const paymentButtons = document.querySelectorAll('a[href*="order/buy"], a[href*="plugin/initiate"], button[onclick*="payment"]');
        
        paymentButtons.forEach(button => {
            let lastClick = 0;
            
            button.addEventListener('click', function(e) {
                const now = Date.now();
                if (now - lastClick < 2000) { // 2 seconds cooldown
                    e.preventDefault();
                    return false;
                }
                lastClick = now;
            });
        });
    }
    
    // Session storage to track recent payments
    function trackRecentPayments() {
        const paymentForms = document.querySelectorAll('form[action*="plugin/initiate"]');
        
        paymentForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const formData = new FormData(form);
                const paymentKey = `payment_${formData.get('username')}_${Date.now()}`;
                
                // Check for recent payment attempt
                const recentPayments = JSON.parse(sessionStorage.getItem('recentPayments') || '[]');
                const fiveMinutesAgo = Date.now() - (5 * 60 * 1000);
                
                const recentPayment = recentPayments.find(p => 
                    p.username === formData.get('username') && 
                    p.timestamp > fiveMinutesAgo
                );
                
                if (recentPayment) {
                    e.preventDefault();
                    alert('You have a recent payment attempt. Please wait before trying again.');
                    return false;
                }
                
                // Add this payment attempt to recent payments
                recentPayments.push({
                    username: formData.get('username'),
                    timestamp: Date.now()
                });
                
                // Keep only last 10 payments
                if (recentPayments.length > 10) {
                    recentPayments.splice(0, recentPayments.length - 10);
                }
                
                sessionStorage.setItem('recentPayments', JSON.stringify(recentPayments));
            });
        });
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            preventDoubleSubmission();
            preventRapidClicking();
            trackRecentPayments();
        });
    } else {
        preventDoubleSubmission();
        preventRapidClicking();
        trackRecentPayments();
    }
})();

// Additional jQuery version if jQuery is available
if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function($) {
        // Prevent double clicks on payment buttons
        $('.btn-payment, .payment-btn, button[onclick*="payment"]').on('click', function() {
            var $btn = $(this);
            if ($btn.hasClass('processing')) {
                return false;
            }
            
            $btn.addClass('processing');
            setTimeout(function() {
                $btn.removeClass('processing');
            }, 5000);
        });
        
        // Prevent form double submission
        $('form[action*="plugin/initiate"], form[action*="payment"]').on('submit', function() {
            var $form = $(this);
            if ($form.data('submitted') === true) {
                return false;
            }
            $form.data('submitted', true);
            
            setTimeout(function() {
                $form.data('submitted', false);
            }, 10000);
        });
    });
}
