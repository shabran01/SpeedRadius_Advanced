$(document).ready(function() {
    var $overdueTable = $('#overdue_table').DataTable({
        "responsive": true,
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        "order": [[ 5, "desc" ]],  // Sort by expiration date descending by default
        "columnDefs": [
            {
                "targets": 5, // Expiration Date column
                "type": "date"
            },
            {
                "targets": 6, // Days Left column
                "type": "numeric"
            },
            {
                "targets": 7, // Actions column
                "orderable": false
            }
        ],
        "language": {
            "emptyTable": "No customers will be overdue in the next 5 days",
            "lengthMenu": "Show _MENU_ records per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ overdue customers",
            "infoEmpty": "No overdue customers found",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "search": "Search customers:",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "drawCallback": function(settings) {
            var api = this.api();
            var pageInfo = api.page.info();
            
            // Hide pagination if we have 10 or fewer records
            if (pageInfo.recordsDisplay <= 10) {
                $(this).parent().find('.dataTables_paginate').hide();
            } else {
                $(this).parent().find('.dataTables_paginate').show();
            }
        }
    });

    // Add custom sort indicators to column headers
    $('#overdue_table thead th').each(function() {
        if (!$(this).hasClass('no-sort')) {
            $(this).append('<span class="sort-icon"></span>');
        }
    });
});
