@extends('layouts.components.main')
@section('title', 'Detail Layup')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">@yield('title')</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('supplier.index') }}">Supplier</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ $supplier->name }}</h5>
                            <button id="btnEditSupplier" class="btn btn-light"><i class="mdi mdi-plus me-1"></i> Edit
                                Supplier</button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Associated Layups</h5>
                            <button id="btnAddLayup" class="btn btn-primary"><i class="mdi mdi-plus me-1"></i> Add
                                Layup</button>
                        </div>

                        <div class="card-body">
                            <table id="datatable"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle table-hover"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th width="5px">#</th>
                                        <th>Name</th>
                                        <th width="100px">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- modal -->
    <div id="modalLayup" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalLayupLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formLayup">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalLayupLabel"></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                            <input type="hidden" name="id" id="id">
                            <label for="name" class="form-label">Name <span style="color: red">*</span></label>
                            <input type="text" id="name" name="name" class="form-control">
                            <small class="text-danger errorName"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveLayup">Save</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- modal supplier -->
    <div id="modalSupplier" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalSupplierLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formSupplier">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalSupplierLabel"></h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" name="id" id="supplier_id" value="{{ $supplier->id }}">
                            <label for="supplier_name" class="form-label">Name <span style="color: red">*</span></label>
                            <input type="text" id="supplier_name" name="name" class="form-control">
                            <small class="text-danger errorSupplierName"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveSupplier">Save</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('clt-layup.index') }}",
                    data: function(d) {
                        d.supplier_id = "{{ $supplier->id }}";
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                createdRow: function(row, data, dataIndex) {
                    $(row).css('cursor', 'pointer');
                    $(row).on('click', function(e) {
                        if ($(e.target).closest('button, a').length) return;
                        window.location.href = "{{ url('clt-layup') }}/" + data.id + "/show";
                    });
                }
            });

            $('body').on('click', '#btnAddLayup', function() {
                $('#id').val('');
                $('#modalLayupLabel').html("Add Layup");
                $('#modalLayup').modal('show');
                $('#formLayup').trigger("reset");

                $('.form-control').removeClass('is-invalid');
                $('.text-danger').html('');
            });

            $('body').on('click', '#btnEdit', function() {
                let id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "{{ route('clt-layup.edit', '') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        $('#modalLayupLabel').html("Edit Data");
                        $('#saveLayup').val("edit-data");
                        $('#modalLayup').modal('show');

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');

                        $('#id').val(response.id);
                        $('#name').val(response.name);
                    }
                });
            })

            $('#formLayup').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('clt-layup.store') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#saveLayup').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Saving...'
                        );

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#saveLayup').prop('disabled', false).text('Save');
                    },
                    success: function(response) {
                        $('#modalLayup').modal('hide');
                        $('#formLayup').trigger("reset");
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then(function() {
                            $('#datatable').DataTable().ajax.reload()
                        });

                    },
                    error: function(xhr, status, errorText) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.name) {
                                $('#name').addClass('is-invalid');
                                $('.errorName').html(errors.name.join('<br>'));
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred, please try again.',
                            });
                        }
                    }
                })
            })

            $('body').on('click', '#btnDelete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete Confirmation',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('clt-layup.destroy', '') }}/" + id,
                            dataType: "json",
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted',
                                    text: response.message,
                                }).then(function() {
                                    $('#datatable').DataTable().ajax.reload()
                                });
                            }
                        });
                    }
                })
            })

            $('body').on('click', '#btnEditSupplier', function() {
                let id = $('#supplier_id').val();
                $.ajax({
                    type: "GET",
                    url: "{{ route('supplier.edit', '') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        $('#modalSupplierLabel').html("Edit Data");
                        $('#saveSupplier').val("edit-data");
                        $('#modalSupplier').modal('show');

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');

                        $('#supplier_form_id').val(response.id);
                        $('#supplier_name').val(response.name);
                    }
                });
            })

            $('#formSupplier').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('supplier.store') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#saveSupplier').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Saving...'
                        );

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#saveSupplier').prop('disabled', false).text('Save');
                    },
                    success: function(response) {
                        $('#modalSupplier').modal('hide');
                        $('#formSupplier').trigger("reset");
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: response.message,
                        }).then(function() {
                            window.location.reload();
                        });

                    },
                    error: function(xhr, status, errorText) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.supplier_name) {
                                $('#supplier_name').addClass('is-invalid');
                                $('.errorSupplierName').html(errors.supplier_name.join('<br>'));
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred, please try again.',
                            });
                        }
                    }
                })
            })

            $('body').on('click', '#btnDelete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Delete Confirmation',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('clt-layup.destroy', '') }}/" + id,
                            dataType: "json",
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted',
                                    text: response.message,
                                }).then(function() {
                                    $('#datatable').DataTable().ajax.reload()
                                });
                            }
                        });
                    }
                })
            })
        });
    </script>
@endsection
