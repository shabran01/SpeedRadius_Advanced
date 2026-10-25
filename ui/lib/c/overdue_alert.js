$(document).ready(function () {
    var $overdueTable = $('#overdue_table').DataTable({
        pageLength: 10,
        lengthMenu: [[10], [10]],
        lengthChange: false,
        order: [[4, 'asc']],
        pagingType: 'full_numbers',
        autoWidth: false,
        columnDefs: [
            {
                "targets": 6, // Actions
                "orderable": false
            },
            {
                "targets": 7, // Service Type (hidden, used for filtering)
                "visible": false,
                "searchable": true
            }
        ],
        "language": {
            "emptyTable": "No customers will be overdue in the selected period",
            "info": "Showing _START_ to _END_ of _TOTAL_ customers",
            "infoEmpty": "No customers found",
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
        "initComplete": function () {
            $('.dataTables_filter input').attr('placeholder', 'Search customers...');

            $('.paginate_button.previous').html('<i class="fa fa-chevron-left"></i> Previous');
            $('.paginate_button.next').html('Next <i class="fa fa-chevron-right"></i>');
            $('.paginate_button.first').html('<i class="fa fa-fast-backward"></i> First');
            $('.paginate_button.last').html('Last <i class="fa fa-fast-forward"></i>');
        }
    });

    // Service-type filter chips (column 7 holds the service type)
    $(document).on('click', '.oa-chip', function () {
        $('.oa-chip').removeClass('active');
        $(this).addClass('active');
        $overdueTable.column(7).search($(this).data('filter') || '').draw();
    });
});
