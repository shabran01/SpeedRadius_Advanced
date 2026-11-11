// Router Status Monitor Javascript
$(document).ready(function() {
    'use strict';
    var updateInterval = 60000; // 1 minute

    // Function to show notifications
    function showNotification(type, message) {
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            alert(message);
        }
    }

    function updateRouterStatus() {
        if (!window.baseURL) {
            console.error('baseURL not defined');
            return;
        }

        $.get(window.baseURL + 'plugin/router_status_monitor?action=check_status')
            .done(function(data) {
                if (!Array.isArray(data)) {
                    console.error('Invalid response format');
                    return;
                }

                data.forEach(function(router) {
                    var row = $('tr[data-router-id="' + router.router_id + '"]');
                    if (!row.length) return;

                    // Update status
                    var statusLabel = router.status === 'online' ? 
                        '<span class="label label-success">Online</span>' : 
                        '<span class="label label-danger">Offline</span>';
                    row.find('.router-status').html(statusLabel);
                    
                    // Update last seen
                    row.find('.last-seen').text(router.last_online || 'Never');
                    
                    // Update uptime
                    if (router.last_uptime) {
                        row.find('.uptime').text(router.last_uptime);
                    }
                    
                    // Visual feedback for status change
                    if (row.data('previous-status') !== router.status) {
                        row.addClass('status-changed');
                        setTimeout(function() {
                            row.removeClass('status-changed');
                        }, 2000);
                    }
                    row.data('previous-status', router.status);
                });
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Failed to update router status:', textStatus, errorThrown);
            });
    }

    // Initial update
    updateRouterStatus();
    
    // Set up periodic updates
    setInterval(updateRouterStatus, updateInterval);
    
    // Handle notification test
    $(document).on('click', '.test-notification', function(e) {
        e.preventDefault();
        var btn = $(this);
        var routerId = btn.data('router-id');
        
        if (!window.baseURL) {
            showNotification('error', 'System configuration error: baseURL not defined');
            return;
        }
        
        // First check if the button is disabled due to no number
        if (btn.prop('disabled') && !btn.hasClass('testing')) {
            showNotification('error', 'Please enter a WhatsApp number first and click Save');
            return;
        }
        
        // Add testing class to track state
        btn.addClass('testing');
        btn.prop('disabled', true)
           .html('<i class="fa fa-spinner fa-spin"></i> Testing...');
           
        $.get(window.baseURL + 'plugin/router_status_monitor', {
            action: 'test_notification',
            router_id: routerId
        })
        .done(function(response) {
            if (response.success) {
                showNotification('success', 'Test notification sent successfully');
            } else {
                showNotification('error', response.message || 'Failed to send test notification');
            }
        })
        .fail(function(jqXHR) {
            var message = 'Failed to send test notification';
            if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                message += ': ' + jqXHR.responseJSON.message;
            }
            showNotification('error', message);
        })
        .always(function() {
            btn.removeClass('testing');
            // Only re-enable if there's a notification number
            var form = btn.closest('tr').find('form.notification-form');
            var number = form.find('input[name="notification_number"]').val();
            btn.prop('disabled', !number)
               .html('Test Notification');
        });
    });
    
    // Save notification number
    $(document).on('submit', 'form.notification-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        
        if (!window.baseURL) {
            showNotification('error', 'System configuration error: baseURL not defined');
            return;
        }

        btn.prop('disabled', true);
        
        $.post(window.baseURL + 'plugin/router_status_monitor', form.serialize())
            .done(function(response) {
                if (response.success) {
                    showNotification('success', 'Notification settings saved');
                } else {
                    showNotification('error', response.message || 'Failed to save notification settings');
                }
            })
            .fail(function(jqXHR) {
                var message = 'Failed to save notification settings';
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    message += ': ' + jqXHR.responseJSON.message;
                }
                showNotification('error', message);
            })
            .always(function() {
                btn.prop('disabled', false);
            });
    });
    
    // Add some CSS for status changes
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .status-changed {
                animation: flash-row 2s ease-out;
            }
            @keyframes flash-row {
                0% { background-color: transparent; }
                50% { background-color: rgba(255, 255, 0, 0.3); }
                100% { background-color: transparent; }
            }
        `)
        .appendTo('head');
});
