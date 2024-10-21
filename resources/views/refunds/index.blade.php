<!DOCTYPE html>
<html>

<head>
    <title>Laravel 11 Ajax CRUD Tutorial Example</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" ></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" /> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link href="https://cdn.datatables.net/v/bs5/dt-2.1.8/r-3.0.3/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/dt-2.1.8/r-3.0.3/datatables.min.js"></script>
</head>

<body>

    <div class="container my-4">
        <div class="card my-4">
            <h2 class="card-header"><i class="fa-regular fa-credit-card"></i> Ajax CRUD Example</h2>
            <div class="card-body">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-3">
                    <a class="btn btn-success btn-sm" href="javascript:void(0)" id="createNewProduct"> <i
                            class="fa fa-plus"></i> Create New Product</a>
                </div>

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
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

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

                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Name:</label>
                            <div class="col-sm-12">
                                <input readonly type="text" class="form-control" id="name" name="name"
                                    placeholder="Enter Name" value="" maxlength="50">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Status:</label>
                            <div class="col-sm-12">
                                <textarea id="refund_notes" name="refund_notes" placeholder="Enter Details" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success mt-2" id="saveBtn" value="create">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-save" viewBox="0 0 16 16">
                                    <path
                                        d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1z">
                                    </path>
                                </svg>
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="showModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"><i class="fa-regular fa-eye"></i> Show Product</h4>
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

</body>

<script type="text/javascript">
    $(function() {

        /*------------------------------------------
         --------------------------------------------
         Pass Header Token
         --------------------------------------------
         --------------------------------------------*/
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        /*------------------------------------------
        --------------------------------------------
        Render DataTable
        --------------------------------------------
        --------------------------------------------*/

        const statusMap = {
            0: 'Pending',
            1: 'Requested',
            2: 'Approved',
            3: 'Rejected',
            // Add more mappings as necessary
        };


        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            search: {
                caseInsensitive: true
            },
            ajax: {
                url: "{{ route('refunds.index') }}",
                dataSrc: function(json) {
                    //console.log(json); // Log the entire response
                    return json.data ? json.data.map(refund => {
                        return {
                            DT_RowIndex: refund.DT_RowIndex,
                            id: refund.id,
                            libraryCard: refund.libraryCard,
                            firstName: refund.firstName,
                            lastName: refund.lastName,
                            full_name: refund.firstName + ' ' + refund
                                .lastName, // Concatenating here
                            phone: refund.phone,
                            refund_status: statusMap[refund.refund_status] ||
                                'Unknown', // Map status here
                        };
                    }) : [];
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false
                },
                {
                    data: 'libraryCard',
                    name: 'libraryCard'
                },
                {
                    data: 'full_name',
                    name: 'full_name',
                    searchable: false
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'refund_status',
                    name: 'refund_status',
                    orderable: false,
                    searchable: false
                }, // Column for refund status
                {
                    data: null,
                    render: function(data, type, row) {
                        //    console.log(row); // Debugging line
                        //    console.log(data); // Debugging line
                        //    console.log(type); // Debugging line

                        return `
<div class="d-flex  justify-content-center  gap-2 mb-3">
                        <button class="btn btn-primary btn-sm edit-btn" data-id="${row.id}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-bookmark" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M6 8V1h1v6.117L8.743 6.07a.5.5 0 0 1 .514 0L11 7.117V1h1v7a.5.5 0 0 1-.757.429L9 7.083 6.757 8.43A.5.5 0 0 1 6 8"></path>
  <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"></path>
  <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"></path>
</svg>Notes</button>
                        <button class="btn btn-danger btn-sm refund-btn" data-id="${row.id}"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cash-coin" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8m5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0"></path>
  <path d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195z"></path>
  <path d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083q.088-.517.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1z"></path>
  <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 6 6 0 0 1 3.13-1.567"></path>
</svg> Refund</button>
              {{--          <button class="btn btn-info btn-sm view-btn" data-id="${row.id}">View</button> --}}
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
                },
            ]
        });

        /*------------------------------------------
        --------------------------------------------
        Click to Button
        --------------------------------------------
        --------------------------------------------*/
        $('#createNewProduct').click(function() {
            $('#saveBtn').val("create-product");
            $('#product_id').val('');
            $('#productForm').trigger("reset");
            $('#modelHeading').html("<i class='fa fa-plus'></i> Create New Product");
            $('#ajaxModel').modal('show');
        });

        /*------------------------------------------
        --------------------------------------------
        Click to View Button
        --------------------------------------------
        --------------------------------------------*/
        $(document).on('click', '.view-btn', function() {
            var refund_id = $(this).data('id');
            $.get("{{ route('refunds.index') }}" + '/' + refund_id, function(data) {
                $('#showModel').modal('show');
                $('.show-name').text(data.fullname);
                $('.show-notes').text(data.notes);
                $('.show-amount').text(data.amount);
                $('.show-status').text(data.refund_status);
            })
        });

        /*------------------------------------------
        --------------------------------------------
        Click to Edit Button
        --------------------------------------------
        --------------------------------------------*/
        $(document).on('click', '.edit-btn', function() {
            var product_id = $(this).data('id');
            $.get("{{ route('refunds.index') }}" + '/' + product_id + '/edit', function(data) {
                $('#modelHeading').html(
                    "<i class='fa-regular fa-pen-to-square'></i> Edit Product"
                ); //where is the icon?
                $('#saveBtn').val("edit-user");
                $('#ajaxModel').modal('show');
                $('#refund_id').val(data.id);
                $('#name').val(data.fullname);
                $('#refund_notes').val(data.notes);
            })
        });

        /*------------------------------------------
        --------------------------------------------
        Create Product Code
        --------------------------------------------
        --------------------------------------------*/
        $('#productForm').submit(function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            $('#saveBtn').html('Sending...');

            $.ajax({
                type: 'POST',
                url: "{{ route('refunds.store') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: (response) => {
                    $('#saveBtn').html('Submit');
                    $('#productForm').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    table.draw();
                },
                error: function(response) {
                    $('#saveBtn').html('Submit');
                    $('#productForm').find(".print-error-msg").find("ul").html('');
                    $('#productForm').find(".print-error-msg").css('display', 'block');
                    $.each(response.responseJSON.errors, function(key, value) {
                        $('#productForm').find(".print-error-msg").find("ul")
                            .append('<li>' + value + '</li>');
                    });
                }
            });

        });

        /*------------------------------------------
        --------------------------------------------
        Delete Product Code
        --------------------------------------------
        --------------------------------------------*/
        // $(document).on('click', '.delete-btn', function() {

        //     var product_id = $(this).data("id");
        //     confirm("Are you sure want to Refund?");

        //     $.ajax({
        //         type: "DELETE",
        //         url: "{{ route('refunds.store') }}" + '/' + product_id,
        //         success: function(data) {
        //             table.draw();
        //         },
        //         error: function(data) {
        //             console.log('Error:', data);
        //         }
        //     });
        // });


        /*------------------------------------------
        --------------------------------------------
        Update Product Code
        --------------------------------------------
        --------------------------------------------*/
        $(document).on('click', '.refund-btn', function() {

            var product_id = $(this).data("id");
            confirm("Are you sure want to Refund?");

            $.ajax({
                type: "PATCH",
                url: "{{ route('refunds.update', ':id') }}".replace(':id',
                    product_id), // Replace ':id' with actual ID
                data: {
                    // libraryCard: $('#libraryCard').val(),
                    // firstName: $('#firstName').val(),
                    // lastName: $('#lastName').val(),
                    // phone: $('#phone').val(),
                    //refund_status: $('#refundStatus').val(),
                    refund_status: '1',
                    _token: '{{ csrf_token() }}' // Include CSRF token
                },
                success: function(data) {
                    table.draw(); // Refresh the DataTable or handle success
                },
                error: function(data) {
                    console.log('Error:', data);
                    // Optionally handle validation errors
                }
            });
        });

    });
</script>

</html>
