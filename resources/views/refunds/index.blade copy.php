<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel 11 Ajax CRUD Tutorial Example</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/v/bs5/dt-2.1.8/r-3.0.3/datatables.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>

<body>
    <div class="container my-4">
        <div class="card my-4">
            <h2 class="card-header"><i class="fa-regular fa-credit-card"></i> Ajax CRUD Example</h2>
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

    <!-- Modal for Creating/Editing -->
    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                </div>
                <div class="modal-body">
                    <form id="productForm" name="productForm" class="form-horizontal">
                        <input type="hidden" name="refund_id" id="refund_id">
                        @csrf
                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul></ul>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name:</label>
                            <input readonly type="text" class="form-control" id="name" name="name" maxlength="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status:</label>
                            <textarea id="refund_notes" name="refund_notes" placeholder="Enter Details" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success" id="saveBtn">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Viewing -->
    <div class="modal fade" id="showModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"><i class="fa-regular fa-eye"></i> Show Product</h4>
                </div>
                <div class="modal-body">
                    <p><strong>Name:</strong> <span class="show-name"></span></p>
                    <p><strong>Notes:</strong> <span class="show-notes"></span></p>
                    <p><strong>Status:</strong> <span class="show-status"></span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/v/bs5/dt-2.1.8/r-3.0.3/datatables.min.js"></script>

    <script type="text/javascript">
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const statusMap = {
                0: 'Pending',
                1: 'Requested',
                2: 'Approved',
                3: 'Rejected',
            };

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('refunds.index') }}",
                    dataSrc: function (json) {
                        return json.data ? json.data.map(refund => ({
                            DT_RowIndex: refund.DT_RowIndex,
                            id: refund.id,
                            libraryCard: refund.libraryCard,
                            full_name: `${refund.firstName} ${refund.lastName}`,
                            phone: refund.phone,
                            refund_status: statusMap[refund.refund_status] || 'Unknown',
                        })) : [];
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                    { data: 'libraryCard', name: 'libraryCard' },
                    { data: 'full_name', name: 'full_name', searchable: false },
                    { data: 'phone', name: 'phone' },
                    { data: 'refund_status', name: 'refund_status', orderable: false, searchable: false },
                    {
                        data: null,
                        render: function (data, type, row) {
                            return `
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-primary btn-sm edit-btn" data-id="${row.id}">Edit</button>
                                    <button class="btn btn-danger btn-sm refund-btn" data-id="${row.id}">Refund</button>
                                </div>`;
                        },
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $(document).on('click', '.edit-btn', function () {
                var refund_id = $(this).data('id');
                $.get("{{ route('refunds.index') }}" + '/' + refund_id + '/edit', function (data) {
                    $('#modelHeading').text("Edit Product");
                    $('#refund_id').val(data.id);
                    $('#name').val(data.fullname);
                    $('#refund_notes').val(data.notes);
                    $('#ajaxModel').modal('show');
                });
            });

            $('#productForm').submit(function (e) {
                e.preventDefault();
                let formData = new FormData(this);
                $('#saveBtn').html('Sending...');

                $.ajax({
                    type: 'POST',
                    url: "{{ route('refunds.store') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $('#saveBtn').html('Submit');
                        $('#productForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.draw();
                    },
                    error: function (response) {
                        $('#saveBtn').html('Submit');
                        $('#productForm').find(".print-error-msg").find("ul").html('');
                        $('#productForm').find(".print-error-msg").css('display', 'block');
                        $.each(response.responseJSON.errors, function (key, value) {
                            $('#productForm').find(".print-error-msg").find("ul").append('<li>' + value + '</li>');
                        });
                    }
                });
            });

            $(document).on('click', '.refund-btn', function () {
                var refund_id = $(this).data("id");
                if (confirm("Are you sure you want to refund?")) {
                    $.ajax({
                        type: "PATCH",
                        url: "{{ route('refunds.update', ':id') }}".replace(':id', refund_id),
                        data: { refund_status: '1', _token: '{{ csrf_token() }}' },
                        success: function () {
                            table.draw();
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>
