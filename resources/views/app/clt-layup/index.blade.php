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
                            <div>
                                <a href="{{ route('clt-layup.template') }}" class="btn btn-outline-secondary me-2">
                                    <i class="mdi mdi-download me-1"></i> Download Template
                                </a>
                                <button id="btnImport" class="btn btn-success me-2">
                                    <i class="mdi mdi-upload me-1"></i> Import
                                </button>
                                <button id="btnAddLayup" class="btn btn-primary"><i class="mdi mdi-plus me-1"></i> Add
                                    Layup</button>
                            </div>
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

    {{-- Modal Import --}}
    <div id="modalImport" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Layups & Layers</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="dropzone" class="border border-2 border-dashed rounded p-5 text-center mb-3"
                        style="cursor:pointer; border-style: dashed !important;">
                        <i class="mdi mdi-cloud-upload-outline fs-1 text-muted"></i>
                        <p class="mt-2 mb-1 fw-semibold">Click to upload or drag & drop</p>
                        <small class="text-muted">Excel (.xlsx, .xls)</small>
                        <input type="file" id="importFile" accept=".xlsx,.xls" class="d-none">
                    </div>
                    <div id="fileInfo" class="alert alert-info d-none">
                        <i class="mdi mdi-file-excel me-2"></i>
                        <span id="fileName"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="btnUploadImport" disabled>
                        <i class="mdi mdi-upload me-1"></i> Upload & Check
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Conflict Resolution --}}
    <div id="modalConflict" class="modal fade" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Conflict Resolution</h5>
                        <small class="text-muted" id="conflictCounter"></small>
                    </div>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <button class="btn btn-sm btn-outline-secondary" id="btnPrevConflict" disabled>
                            <i class="mdi mdi-chevron-left"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" id="btnNextConflict">
                            <i class="mdi mdi-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    {{-- Info layup & layer --}}
                    <div class="mb-3 p-3 bg-light rounded">
                        <span class="fw-semibold">Layup: </span>
                        <span id="conflictLayupName"></span>
                        <span class="ms-3 fw-semibold">Layer Order: </span>
                        <span id="conflictLayerOrder"></span>
                    </div>

                    {{-- Side by side --}}
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="card h-100" style="border: 1.5px solid #dee2e6;">
                                <div class="card-header text-dark text-center py-2" style="background-color: #f8f9fa;">
                                    <i class="mdi mdi-database me-1"></i> Existing Data
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm mb-0">
                                        <tbody>
                                            <tr id="existingThicknessRow">
                                                <td class="ps-3 text-muted" width="45%">Thickness</td>
                                                <td id="existingThickness" class="fw-semibold"></td>
                                            </tr>
                                            <tr id="existingWidthRow">
                                                <td class="ps-3 text-muted">Width</td>
                                                <td id="existingWidth" class="fw-semibold"></td>
                                            </tr>
                                            <tr id="existingAngleRow">
                                                <td class="ps-3 text-muted">Angle</td>
                                                <td id="existingAngle" class="fw-semibold"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer text-center" style="background-color: #f8f9fa;">
                                    <button class="btn btn-outline-secondary btn-sm w-100" id="btnKeepExisting">
                                        ✔ Keep Existing
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="card h-100 border-success">
                                <div class="card-header bg-success text-white text-center py-2">
                                    <i class="mdi mdi-upload me-1"></i> Incoming Data
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm mb-0">
                                        <tbody>
                                            <tr id="incomingThicknessRow">
                                                <td class="ps-3 text-muted" width="45%">Thickness</td>
                                                <td id="incomingThickness" class="fw-semibold"></td>
                                            </tr>
                                            <tr id="incomingWidthRow">
                                                <td class="ps-3 text-muted">Width</td>
                                                <td id="incomingWidth" class="fw-semibold"></td>
                                            </tr>
                                            <tr id="incomingAngleRow">
                                                <td class="ps-3 text-muted">Angle</td>
                                                <td id="incomingAngle" class="fw-semibold"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="card-footer text-center" style="background-color: #f0faf4;">
                                    <button class="btn btn-success btn-sm w-100" id="btnAcceptIncoming">
                                        ✔ Accept Incoming
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Progress --}}
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Progress</small>
                            <small id="conflictProgress" class="text-muted"></small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" id="conflictProgressBar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success d-none" id="btnFinishImport">
                        <i class="mdi mdi-check me-1"></i> Finish Import
                    </button>
                </div>
            </div>
        </div>
    </div>
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
                            if (errors.name) {
                                $('#supplier_name').addClass('is-invalid');
                                $('.errorSupplierName').html(errors.name.join('<br>'));
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

            const supplierId = {{ $supplier->id }};
            let conflicts = [];
            let cleanData = [];
            let resolvedStatus = [];
            let resolvedData = [];
            let currentConflict = 0;

            $('body').on('click', '#btnImport', function() {
                $('#importFile').val('');
                $('#fileInfo').addClass('d-none');
                $('#btnUploadImport').prop('disabled', true);
                $('#modalImport').modal('show');
            });

            $('#dropzone').on('click', function(e) {
                if ($(e.target).is('#importFile')) return;
                $('#importFile').trigger('click');
            });


            $('#dropzone').on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('border-primary');
            });

            $('#dropzone').on('dragleave', function() {
                $(this).removeClass('border-primary');
            });

            $('#dropzone').on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('border-primary');
                let file = e.originalEvent.dataTransfer.files[0];
                if (file) handleFileSelect(file);
            });

            $('#importFile').on('change', function() {
                if (this.files[0]) handleFileSelect(this.files[0]);
            });

            function handleFileSelect(file) {
                $('#fileName').text(file.name);
                $('#fileInfo').removeClass('d-none');
                $('#btnUploadImport').prop('disabled', false);
            }

            $('#btnUploadImport').on('click', function() {
                let file = $('#importFile')[0].files[0];
                if (!file) return;

                let formData = new FormData();
                formData.append('file', file);
                formData.append('supplier_id', supplierId);
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                $('#btnUploadImport').prop('disabled', true).html(
                    '<i class="mdi mdi-loading mdi-spin me-1"></i> Checking...'
                );

                $.ajax({
                    url: "{{ route('clt-layup.upload-import') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        $('#btnUploadImport').prop('disabled', false).html(
                            '<i class="mdi mdi-upload me-1"></i> Upload & Check'
                        );
                        $('#modalImport').modal('hide');

                        conflicts = res.conflicts;
                        cleanData = res.clean_data;
                        resolvedConflicts = [];
                        currentConflict = 0;

                        if (conflicts.length === 0) {
                            finishImport();
                        } else {
                            showConflictModal();
                        }
                    },
                    error: function() {
                        $('#btnUploadImport').prop('disabled', false).html(
                            '<i class="mdi mdi-upload me-1"></i> Upload & Check'
                        );
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to read file.'
                        });
                    }
                });
            });

            function showConflictModal() {
                currentConflict = 0;
                resolvedStatus = new Array(conflicts.length).fill(undefined);
                resolvedData = new Array(conflicts.length).fill(null);
                renderConflict(0);
                $('#modalConflict').modal('show');
            }

            function renderConflict(index) {
                let c = conflicts[index];
                let total = conflicts.length;

                $('#conflictCounter').text('Conflict ' + (index + 1) + ' of ' + total);
                $('#conflictLayupName').text(c.layup_name);
                $('#conflictLayerOrder').text(c.layer_order);

                ['ThicknessRow', 'WidthRow', 'AngleRow'].forEach(function(suffix) {
                    $('#existing' + suffix).removeClass('table-warning table-success');
                    $('#incoming' + suffix).removeClass('table-warning table-success');
                });

                let fields = ['thickness', 'width', 'angle'];
                fields.forEach(function(field) {
                    let isDiff = parseFloat(c.existing[field]) !== parseFloat(c.incoming[field]);
                    let cap = field.charAt(0).toUpperCase() + field.slice(1);

                    $('#existing' + cap).text(c.existing[field]);
                    $('#incoming' + cap).text(c.incoming[field]);

                    if (isDiff) {
                        $('#existing' + cap + 'Row').css('background-color', '#fff3cd');
                        $('#incoming' + cap + 'Row').css('background-color', '#d1f0e0');
                    } else {
                        $('#existing' + cap + 'Row').css('background-color', '');
                        $('#incoming' + cap + 'Row').css('background-color', '');
                    }
                });


                let status = resolvedStatus[index];
                if (status === 'kept') {
                    $('#btnKeepExisting').removeClass('btn-outline-secondary').addClass('btn-secondary');
                    $('#btnAcceptIncoming').removeClass('btn-success').addClass('btn-outline-success');
                } else if (status === 'accepted') {
                    $('#btnKeepExisting').removeClass('btn-secondary').addClass('btn-outline-secondary');
                    $('#btnAcceptIncoming').removeClass('btn-outline-success').addClass('btn-success');
                } else {
                    $('#btnKeepExisting').removeClass('btn-secondary').addClass('btn-outline-secondary');
                    $('#btnAcceptIncoming').removeClass('btn-outline-success').addClass('btn-success');
                }

                let resolved = resolvedStatus.filter(r => r !== undefined).length;
                let progress = total > 0 ? Math.round((resolved / total) * 100) : 0;
                $('#conflictProgress').text(resolved + ' / ' + total + ' resolved');
                $('#conflictProgressBar').css('width', progress + '%');

                $('#btnPrevConflict').prop('disabled', index === 0);
                $('#btnNextConflict').prop('disabled', index === total - 1);

                let allResolved = resolvedStatus.filter(r => r !== undefined).length === total;
                $('#btnFinishImport').toggleClass('d-none', !allResolved);
            }

            $('#btnKeepExisting').on('click', function() {
                resolvedStatus[currentConflict] = 'kept';
                moveToNext();
            });

            $('#btnAcceptIncoming').on('click', function() {
                let c = conflicts[currentConflict];
                resolvedStatus[currentConflict] = 'accepted';
                resolvedData[currentConflict] = {
                    layup_name: c.layup_name,
                    layer_order: c.layer_order,
                    thickness: c.incoming.thickness,
                    width: c.incoming.width,
                    angle: c.incoming.angle,
                };
                moveToNext();
            });

            function moveToNext() {
                renderConflict(currentConflict);
                if (currentConflict < conflicts.length - 1) {
                    currentConflict++;
                    renderConflict(currentConflict);
                }
            }

            $('#btnPrevConflict').on('click', function() {
                if (currentConflict > 0) {
                    currentConflict--;
                    renderConflict(currentConflict);
                }
            });

            $('#btnNextConflict').on('click', function() {
                if (currentConflict < conflicts.length - 1) {
                    currentConflict++;
                    renderConflict(currentConflict);
                }
            });

            $('#btnFinishImport').on('click', function() {
                finishImport();
            });

            function finishImport() {
                let accepted = resolvedData.filter((r, i) => resolvedStatus[i] === 'accepted' && r !== null);

                $.ajax({
                    url: "{{ route('clt-layup.process-import') }}",
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        supplier_id: supplierId,
                        clean_data: cleanData,
                        resolved_conflicts: accepted,
                    }),
                    success: function(res) {
                        $('#modalConflict').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Import Successful',
                            text: res.message,
                        }).then(function() {
                            $('#datatable').DataTable().ajax.reload();
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Import failed, please try again.'
                        });
                    }
                });
            }
        });
    </script>
@endsection
