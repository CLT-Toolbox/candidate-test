@extends('layouts.components.main')
@section('title', 'Detail Clt Layer')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">@yield('title')</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('supplier.index') }}">Supplier</a></li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('supplier.show', $cltLayup->supplier_id) }}">Layup</a></li>
                                <li class="breadcrumb-item active">@yield('title')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Card --}}
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="mb-1">Layup: {{ $cltLayup->name }}</h3>
                            <p class="text-muted">Supplier: {{ $cltLayup->supplier->name }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="d-flex justify-content-md-end gap-2 align-items-center">
                                <div class="px-3 border-start">
                                    <p class="text-muted mb-0">Total Thickness</p>
                                    <h4 class="mb-0" id="totalThickness">0mm</h4>
                                </div>
                                <div class="px-3 border-start">
                                    <p class="text-muted mb-0">Total Layers</p>
                                    <h4 class="mb-0" id="totalLayers">0 Layers</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Table --}}
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Layer Composition</h5>
                            <button class="btn btn-sm btn-primary" id="btnAdd">+ Add Layer</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered align-middle" style="width:100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Order</th>
                                            <th>Thickness (mm)</th>
                                            <th>Width (mm)</th>
                                            <th>Angle (°)</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Visualizer --}}
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header border-0">
                            <h5 class="card-title mb-0">Structure Visualizer</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="d-flex justify-content-center gap-3 mb-4">
                                <small><i class="ri-checkbox-blank-fill text-warning"></i> Longitudinal (0°)</small>
                                <small><i class="ri-checkbox-blank-fill text-secondary"></i> Transverse (90°)</small>
                            </div>
                            <div class="clt-visualizer mx-auto" id="visualizer" style="max-width: 250px;">
                                <p class="text-muted small">No layers yet.</p>
                            </div>
                            <p class="mt-3 text-muted small">Cross-Laminated Structural Assembly</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div id="modal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="layup_id" id="layup_id" value="{{ $cltLayup->id }}">

                        <div class="mb-3">
                            <label class="form-label">Layer Order <span style="color: red">*</span></label>
                            <input type="number" name="layer_order" id="layer_order" class="form-control" min="1">
                            <small class="text-danger errorLayerOrder"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thickness (mm) <span style="color: red">*</span></label>
                            <input type="number" step="0.01" name="thickness" id="thickness" class="form-control">
                            <small class="text-danger errorThickness"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Width (mm) <span style="color: red">*</span></label>
                            <input type="number" step="0.01" name="width" id="width" class="form-control">
                            <small class="text-danger errorWidth"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Angle (°) <span style="color: red">*</span></label>
                            <input type="number" step="0.01" name="angle" id="angle" class="form-control"
                                min="0" max="360">
                            <small class="text-danger errorAngle"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="save">Save</button>
                    </div>
                </form>
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

            const layupId = {{ $cltLayup->id }};

            const table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('clt-layer.index') }}",
                    data: {
                        layup_id: layupId
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'layer_order',
                        name: 'layer_order'
                    },
                    {
                        data: 'thickness',
                        name: 'thickness'
                    },
                    {
                        data: 'width',
                        name: 'width'
                    },
                    {
                        data: 'angle',
                        name: 'angle'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                drawCallback: function() {
                    updateSummary();
                    updateVisualizer();
                }
            });

            $('body').on('click', '#btnAdd', function() {
                $('#id').val('');
                $('#form').trigger('reset');
                $('#layup_id').val(layupId);
                $('#modalLabel').text('Add Layer');

                $('.form-control').removeClass('is-invalid');
                $('.text-danger').html('');

                $('#modal').modal('show');
            });

            $('body').on('click', '#btnEdit', function() {
                let id = $(this).data('id');
                $.ajax({
                    type: 'GET',
                    url: '/clt-layer/' + id,
                    success: function(res) {
                        $('#id').val(res.id);
                        $('#layup_id').val(res.layup_id);
                        $('#layer_order').val(res.layer_order);
                        $('#thickness').val(res.thickness);
                        $('#width').val(res.width);
                        $('#angle').val(res.angle);

                        $('#modalLabel').text('Edit Layer');

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');

                        $('#modal').modal('show');
                    }
                });
            });

            $('#form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('clt-layer.store') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('#save').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Saving...');
                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#save').prop('disabled', false).text('Save');
                    },
                    success: function(res) {
                        $('#modal').modal('hide');
                        $('#form').trigger('reset');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message
                        });
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.layer_order) {
                                $('#layer_order').addClass('is-invalid');
                                $('.errorLayerOrder').html(errors.layer_order.join('<br>'));
                            }
                            if (errors.thickness) {
                                $('#thickness').addClass('is-invalid');
                                $('.errorThickness').html(errors.thickness.join('<br>'));
                            }
                            if (errors.width) {
                                $('#width').addClass('is-invalid');
                                $('.errorWidth').html(errors.width.join('<br>'));
                            }
                            if (errors.angle) {
                                $('#angle').addClass('is-invalid');
                                $('.errorAngle').html(errors.angle.join('<br>'));
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred, please try again.'
                            });
                        }
                    }
                });
            });

            $('body').on('click', '#btnDelete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: 'DELETE',
                            url: '/clt-layer/' + id,
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted',
                                    text: res.message
                                });
                                table.ajax.reload();
                            }
                        });
                    }
                });
            });

            function updateSummary() {
                let total = 0;
                let count = 0;
                $('#datatable tbody tr').each(function() {
                    let thickness = parseFloat($(this).find('td:eq(2)').text()) || 0;
                    total += thickness;
                    count++;
                });
                $('#totalThickness').text(total.toFixed(2) + 'mm');
                $('#totalLayers').text(count + ' Layers');
            }

            function updateVisualizer() {
                let html = '';
                let rows = [];

                $('#datatable tbody tr').each(function() {
                    let order = $(this).find('td:eq(1)').text().trim();
                    let thickness = $(this).find('td:eq(2)').text().trim();
                    let angle = parseFloat($(this).find('td:eq(4)').text()) || 0;
                    if (order && thickness) {
                        rows.push({
                            order,
                            thickness,
                            angle
                        });
                    }
                });

                if (rows.length === 0) {
                    html = '<p class="text-muted small">No layers yet.</p>';
                } else {
                    rows.forEach(function(row) {
                        let isTransverse = row.angle == 90;
                        let bgClass = isTransverse ? 'bg-secondary-subtle' : 'bg-warning-subtle';
                        let symbol = isTransverse ? '↔' : '↑';
                        let py = row.thickness > 25 ? 'py-3' : 'py-2';
                        html += `<div class="mb-1 ${py} border rounded shadow-sm ${bgClass} text-dark">
                            L${row.order} (${row.thickness}mm) ${symbol}
                         </div>`;
                    });
                }

                $('#visualizer').html(html);
            }
        });
    </script>
@endsection
