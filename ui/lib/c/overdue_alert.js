$(document).ready(function() {
    // Initialize modern DataTable with exactly 10 customers per page
    var $overdueTable = $('#overdue_table').DataTable({
        "responsive": true,
        "pageLength": 10,
        "lengthMenu": [[10], [10]], // Force exactly 10 per page only
        "lengthChange": false, // Hide length menu since we only want 10 per page
        "order": [[ 4, "asc" ]],  // Sort by expiration date ascending (earliest first)
        "pagingType": "full_numbers", // Shows numbered pagination: 1 2 3 4 5 6 7 8 9 10
        "columnDefs": [
            {
                "targets": 4, // Expiration Date column
                "type": "date"
            },
            {
                "targets": 5, // Days Left column
                "type": "numeric",
                "render": function(data, type, row) {
                    if (type === 'sort' || type === 'type') {
                        // Extract number for sorting
                        return parseInt(data.match(/\d+/));
                    }
                    return data;
                }
            },
            {
                "targets": 6, // Actions column
                "orderable": false
            }
        ],
        "language": {
            "emptyTable": "No customers will be overdue in the next 5 days",
            "info": "Showing _START_ to _END_ of _TOTAL_ overdue customers (10 per page)",
            "infoEmpty": "No overdue customers found",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "search": "Search customers:",
            "paginate": {
                "first": "First",
                "last": "Last", 
                "next": "Next",
                "previous": "Previous"
            },
            "zeroRecords": "No customers found matching your search"
        },
        "dom": "<'row'<'col-sm-6'><'col-sm-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "drawCallback": function(settings) {
            var api = this.api();
            var pageInfo = api.page.info();
            
            // Always show pagination for consistency, even with fewer than 10 records
            $(this).parent().find('.dataTables_paginate').show();
            
            // Ensure exactly 10 records per page is enforced
            if (api.page.len() !== 10) {
                api.page.len(10).draw('page');
            }
            
            // Add hover effects to rows
            $(this).find('tbody tr').hover(
                function() {
                    $(this).addClass('table-hover-effect');
                },
                function() {
                    $(this).removeClass('table-hover-effect');
                }
            );
        },
        "initComplete": function(settings, json) {
            // Add custom styling after initialization
            $('.dataTables_filter input').attr('placeholder', 'Search customers...');
            
            // Add icons to pagination buttons
            $('.paginate_button.previous').html('<i class="fa fa-chevron-left"></i> Previous');
            $('.paginate_button.next').html('Next <i class="fa fa-chevron-right"></i>');
            $('.paginate_button.first').html('<i class="fa fa-fast-backward"></i> First');
            $('.paginate_button.last').html('Last <i class="fa fa-fast-forward"></i>');
        }
    });

    // Add custom search functionality
    $('.dataTables_filter input').on('keyup', function() {
        var value = this.value;
        if (value.length >= 2 || value.length === 0) {
            $overdueTable.search(value).draw();
        }
    });

    // Highlight critical customers (1-2 days left)
    $overdueTable.on('draw', function() {
        $overdueTable.rows().every(function() {
            var data = this.data();
            var daysLeftText = $(data[5]).text() || data[5];
            var daysLeft = parseInt(daysLeftText.match(/\d+/));
            
            if (daysLeft <= 2) {
                $(this.node()).addClass('critical-row');
                $(this.node()).css({
                    'border-left': '4px solid #ff6b6b',
                    'background': 'rgba(255, 107, 107, 0.05)'
                });
            } else if (daysLeft <= 4) {
                $(this.node()).addClass('warning-row');
                $(this.node()).css({
                    'border-left': '4px solid #ffa726',
                    'background': 'rgba(255, 167, 38, 0.05)'
                });
            }
        });
    });

    // Add click animation to action buttons
    $(document).on('click', '.btn-modern', function() {
        var $this = $(this);
        $this.addClass('animate-click');
        setTimeout(function() {
            $this.removeClass('animate-click');
        }, 150);
    });

    // Add tooltips to badges
    $('[data-toggle="tooltip"]').tooltip();
    
    // Auto-refresh every 5 minutes
    setInterval(function() {
        if (confirm('Auto-refresh: Do you want to refresh the overdue list to get the latest data?')) {
            location.reload();
        }
    }, 300000); // 5 minutes
});
