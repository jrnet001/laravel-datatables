<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Print Refunds</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/v/bs5/dt-2.1.8/r-3.0.3/datatables.min.css" rel="stylesheet">

    <!-- FontAwesome (if needed) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" async>
    </script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/v/bs5/dt-2.1.8/r-3.0.3/datatables.min.js" async></script>
</head>

<body>

    <div class="container my-4">
        <div class="card my-4">
            <h2 class="card-header"><i class="fa-regular fa-credit-card"></i> Print Refunds</h2>
            <div class="card-body">
                <table class="table table-striped table-bordered data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Library Card</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Refund Form -->
    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="refundForm" name="refundForm" class="form-horizontal">
                        <input type="hidden" name="refund_id" id="refund_id">
                        @csrf
                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul></ul>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Name:</label>
                            <input readonly type="text" class="form-control" id="name" name="name"
                                maxlength="50">
                        </div>

                        <div class="mb-3">
                            <label for="refund_notes" class="form-label">Notes:</label>
                            <textarea id="refund_notes" name="refund_notes" placeholder="Enter Details" class="form-control"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success mt-2" id="saveBtn">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Showing Refund Details -->
    <div class="modal fade" id="showModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"><i class="fa-regular fa-eye"></i> Show Refund</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Name:</strong> <span class="show-name"></span></p>
                    <p><strong>Amount:</strong> <span class="show-amount"></span></p>
                    <p><strong>Notes:</strong> <span class="show-notes"></span></p>
                    <p><strong>Status:</strong> <span class="show-status"></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="liveToast" class="toast bg-success text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-body">
                Refund requested!
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(function() {
            /*------------------------------------------
             Pass Header Token
            --------------------------------------------*/
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            /*------------------------------------------
            Render DataTable
            --------------------------------------------*/
            const statusMap = {
                0: 'Pending',
                1: 'Requested',
                2: 'Approved',
                3: 'Rejected',
            };

            var table = $('.data-table').DataTable({
                processing: true,
                stateSave: true,
                serverSide: true,
                responsive: true,
                searchDelay: 800,
                ajax: {
                    url: "{{ route('refunds.index') }}",
                    url: "{{ route('refunds.index') }}",
                    dataSrc: function(json) {
                        return json.data ? json.data.map(refund => ({
                            DT_RowIndex: refund.DT_RowIndex,
                            id: refund.id,
                            libraryCard: refund.libraryCard,
                            full_name: refund.firstName + ' ' + refund.lastName,
                            phone: refund.phone,
                            refund_status: statusMap[refund.refund_status] || 'Unknown',
                            firstName: refund.firstName,
                            lastName: refund.lastName,
                        })) : [];
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        visible: false
                    },
                    {
                        data: 'libraryCard',
                        searchable: true
                    },
                    {
                        data: 'full_name',
                        searchable: false
                    },
                    {
                        data: 'phone',
                        searchable: true
                    },
                    {
                        data: 'refund_status',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <div class="d-flex justify-content-center gap-2 mb-3">
                                    <button class="btn btn-primary btn-sm edit-btn" data-id="${row.id}"><i class="fa fa-edit"></i> Notes</button>
                                    <button ${row.refund_status === 'Pending' ? '' : 'disabled'} class="btn btn-success btn-sm refund-btn" data-id="${row.id}"><i class="fa fa-money-bill-wave"></i> Refund</button>
                                </div>`;
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'firstName',
                        name: 'firstName',
                        orderable: false,
                        searchable: true,
                        visible: false
                    },
                    {
                        data: 'lastName',
                        name: 'lastName',
                        orderable: false,
                        searchable: true,
                        visible: false
                    }
                ]
            });

            /*------------------------------------------
            Click to Edit Button
            --------------------------------------------*/
            $(document).on('click', '.edit-btn', function() {
                const refund_id = $(this).data('id');
                $.get("{{ route('refunds.index') }}" + '/' + refund_id + '/edit', function(data) {
                    $('#modelHeading').html("Notes");
                    $('#refund_id').val(data.id);
                    $('#name').val(data.fullname);
                    $('#refund_notes').val(data.notes);
                    $('#ajaxModel').modal('show');
                });
            });

            /*------------------------------------------
            Create Refund Code
            --------------------------------------------*/
            $('#refundForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $('#saveBtn').html('Sending...');

                $.ajax({
                    type: 'POST',
                    url: "{{ route('refunds.store') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#saveBtn').html('Submit');
                        $('#refundForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.draw();
                        showToast("Refund successfully submitted!");
                    },
                    error: function(response) {
                        $('#saveBtn').html('Submit');
                        displayErrors(response);
                    }
                });
            });

            /*------------------------------------------
            Update Refund Code
            --------------------------------------------*/
            $(document).on('click', '.refund-btn', function() {
                const refund_id = $(this).data("id");
                if (confirm("Are you sure you want to request this refund?")) {
                    $.ajax({
                        type: "PATCH",
                        url: "{{ route('refunds.update', ':id') }}".replace(':id', refund_id),
                        data: {
                            refund_id,
                            refund_status: '1',
                            _token: '{{ csrf_token() }}' // Include CSRF token
                        },
                        success: function(data) {
                            table.draw();
                            showToast("Refund requested for: " + data.refund.firstName + " " +
                                data.refund.lastName);
                        }
                    });
                }
            });

            /*------------------------------------------
            Show Refund Details
            --------------------------------------------*/
            $(document).on('click', '.show-btn', function() {
                const refund_id = $(this).data('id');
                $.get("{{ route('refunds.index') }}" + '/' + refund_id, function(data) {
                    $('.show-name').text(data.fullname);
                    $('.show-amount').text(data.amount);
                    $('.show-notes').text(data.notes);
                    $('.show-status').text(data.refund_status);
                    $('#showModel').modal('show');
                });
            });

            /*------------------------------------------
            Toast Notification
            --------------------------------------------*/
            function showToast(message) {
                $('.toast-body').text(message);
                const toast = new bootstrap.Toast(document.getElementById('liveToast'));
                toast.show();
            }

            /*------------------------------------------
            Display Validation Errors
            --------------------------------------------*/
            function displayErrors(response) {
                let errors = response.responseJSON.errors;
                $('.print-error-msg').find("ul").html('');
                $('.print-error-msg').css('display', 'none');
                $.each(errors, function(key, value) {
                    $('.print-error-msg').find("ul").append('<li>' + value + '</li>');
                });
                $('.print-error-msg').css('display', 'block');
            }
        });
    </script>
</body>

</html>
