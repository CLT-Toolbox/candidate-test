<x-app-layout>
    @php
        $supplierForEdit = [
            'supplier_id' => $supplier->supplier_id,
            'name' => $supplier->name,
            'email' => $supplier->email,
            'location' => $supplier->location,
            'certification' => $supplier->certification,
            'audit_date' => $supplier->audit_date ? \Illuminate\Support\Carbon::parse($supplier->audit_date)->format('Y-m-d') : '',
            'status' => $supplier->status ?? \App\Models\Supplier::STATUS_ACTIVE,
        ];
        $layupsExportPayload = $supplier->layups->map(function ($layup) {
            return [
                'name' => $layup->name,
                'description' => $layup->description,
                'thickness' => $layup->thickness,
                'grade' => $layup->grade,
                'status' => $layup->status ?? 'ACTIVE',
                'layers' => $layup->layers->sortBy('layer_order')->values()->map(function ($layer) {
                    return [
                        'layer_order' => (int) $layer->layer_order,
                        'thickness' => $layer->thickness !== null ? (string) $layer->thickness : '',
                        'width' => $layer->width !== null ? (string) $layer->width : '',
                        'angle' => $layer->angle !== null ? (string) $layer->angle : '',
                        'grade' => $layer->grade,
                    ];
                })->all(),
            ];
        })->values()->all();
    @endphp
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($message = Session::get('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex justify-between items-center">
                    <span>{{ $message }}</span>
                    <button type="button" onclick="this.parentElement.style.display='none'" class="text-green-600 hover:text-green-900 font-bold">×</button>
                </div>
            @endif

            <!-- Breadcrumb -->
            <div class="mb-4 text-sm text-gray-600">
                <a href="{{ route('suppliers.index') }}" class="text-blue-600 hover:text-blue-800">Suppliers</a>
                <span class="mx-2">/</span>
                <span>{{ $supplier->name }}</span>
            </div>

            <!-- Header Card -->
            <div class="bg-white shadow-md rounded-lg border border-gray-200 p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h1 class="text-3xl font-bold text-gray-900">{{ $supplier->name }}</h1>
                                @php
                                    $statusKey = $supplier->status ?? \App\Models\Supplier::STATUS_ACTIVE;
                                    $statusLabel = \App\Models\Supplier::STATUSES[$statusKey] ?? $statusKey;
                                    $statusBadgeClass = $statusKey === \App\Models\Supplier::STATUS_ACTIVE
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-gray-200 text-gray-800';
                                @endphp
                                <span class="{{ $statusBadgeClass }} text-xs font-semibold px-3 py-1 rounded-full">{{ $statusLabel }}</span>
                            </div>
                            <p class="text-sm text-gray-600">ID: <span
                                    class="font-mono font-semibold">{{ $supplier->supplier_id }}</span></p>
                        </div>
                    </div>
                    <button type="button"
                        onclick='openEditModal({!! json_encode($supplierForEdit, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!})'
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 inline-flex items-center gap-2 bg-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        {{ __('Edit Supplier') }}
                    </button>
                </div>

                <!-- Info Cards Grid -->
                <div class="grid grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Primary Contact</p>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            <div>
                                <a href="mailto:{{ $supplier->email }}"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    {{ $supplier->email ?? '-' }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Location</p>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <p class="text-sm text-gray-900 font-medium">{{ $supplier->location ?? '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Material
                            Certifications</p>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm text-gray-900 font-medium">{{ $supplier->certification ?? '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Last Audit Date</p>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <p class="text-sm text-gray-900 font-medium">
                                @if ($supplier->audit_date)
                                    {{ \Carbon\Carbon::parse($supplier->audit_date)->format('M d, Y') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Associated Layups Section -->
            <div class="bg-white shadow-md rounded-lg border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Associated Layups</h2>
                    <div class="flex gap-2">
                        <button onclick="openImportModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Import
                        </button>
                        <button type="button" onclick="exportSupplierLayups(event)"
                            title="{{ __('Export CSV (same format as Import). Shift+click: JSON.') }}"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            {{ __('Export') }}
                        </button>
                        <button onclick="openCreateLayupModal()"
                            class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add Layup
                        </button>
                    </div>
                </div>

                <!-- Layups Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 font-semibold text-gray-700">Layup ID</th>
                                <th class="px-6 py-3 font-semibold text-gray-700">Name</th>
                                <th class="px-6 py-3 font-semibold text-gray-700">Thickness</th>
                                <th class="px-6 py-3 font-semibold text-gray-700">Ply Count</th>
                                <th class="px-6 py-3 font-semibold text-gray-700">Species/Grade</th>
                                <th class="px-6 py-3 font-semibold text-gray-700">Revision</th>
                                <th class="px-6 py-3 font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supplier->layups ?? [] as $layup)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $layup->layup_id }}</td>
                                    <td class="px-6 py-4 text-gray-900">{{ $layup->name }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $layup->thickness ?? '-' }}</td>
                                    <td class="px-6 py-4 text-gray-600" data-layup-id="{{ $layup->layup_id }}">
                                        {{ count($layup->layers ?? []) }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $layup->grade ?? '-' }}</td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $layup->updated_at->format('Y-m-d') ?? '-' }}</td>
                                    <td class="px-6 py-4">
                                        @if ($layup->status === 'ACTIVE')
                                            <span
                                                class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">●
                                                Active</span>
                                        @elseif($layup->status === 'DRAFT')
                                            <span
                                                class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">⚠
                                                Draft</span>
                                        @elseif($layup->status === 'ARCHIVED')
                                            <span
                                                class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">✓
                                                Archived</span>
                                        @else
                                            <span
                                                class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">{{ $layup->status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('suppliers.layups.show', [$supplier->supplier_id, $layup->layup_id]) }}"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">View
                                            Layers</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                        No layups associated with this supplier
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($supplier->layups && count($supplier->layups) > 0)
                    <p class="text-xs text-gray-500 mt-4">Showing {{ count($supplier->layups) }} of
                        {{ count($supplier->layups) }} layups</p>
                @endif
            </div>
        </div>
    </div>

    @include('suppliers.modal-edit-supplier')

    @include('suppliers.modal-import-layup')

    @include('suppliers.modal-create-layup')

    @include('suppliers.modal-conflict-resolution')

    <script>
        const supplierLayupsExportData = @json($layupsExportPayload);

        // Global variables for conflict resolution
        let conflictsData = [];
        let currentConflictIndex = 0;
        let conflictResolutions = {};
        let uploadedFile = null;
        let manualResolveMode = false;

        // Create Layup Modal Functions
        function openCreateLayupModal() {
            document.getElementById('createLayupModal').classList.remove('hidden');
            document.getElementById('createLayupForm').reset();
            document.getElementById('createLayupNameError').textContent = '';
        }

        function closeCreateLayupModal() {
            document.getElementById('createLayupModal').classList.add('hidden');
        }

        function csvEscapeCell(val) {
            const s = String(val ?? '');
            if (/[",\r\n]/.test(s)) {
                return '"' + s.replace(/"/g, '""') + '"';
            }
            return s;
        }

        function exportSupplierLayupsCsv() {
            const layups = supplierLayupsExportData || [];
            if (!layups.length) {
                alert(@json(__('No layups to export.')));
                return;
            }
            const header = ['Name', 'Description', 'Thickness', 'Grade', 'Status', 'LayerOrder', 'LayerThickness', 'LayerWidth', 'LayerAngle'];
            const rows = [header.map(csvEscapeCell).join(',')];
            layups.forEach((layup) => {
                const base = [
                    layup.name,
                    layup.description ?? '',
                    layup.thickness ?? '',
                    layup.grade ?? '',
                    layup.status ?? 'ACTIVE',
                ];
                const layers = Array.isArray(layup.layers) && layup.layers.length
                    ? layup.layers
                    : [{ layer_order: 1, thickness: '0', width: '0', angle: '0' }];
                layers.forEach((layer) => {
                    rows.push([
                        ...base,
                        layer.layer_order,
                        layer.thickness,
                        layer.width,
                        layer.angle,
                    ].map(csvEscapeCell).join(','));
                });
            });
            const csv = '\uFEFF' + rows.join('\r\n');
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'layups_{{ $supplier->supplier_id }}_' + new Date().toISOString().slice(0, 10) + '.csv';
            document.body.appendChild(a);
            a.click();
            URL.revokeObjectURL(url);
            a.remove();
        }

        function exportSupplierLayupsJson() {
            const layups = supplierLayupsExportData || [];
            if (!layups.length) {
                alert(@json(__('No layups to export.')));
                return;
            }
            const blob = new Blob([JSON.stringify(layups, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'layups_{{ $supplier->supplier_id }}_' + new Date().toISOString().slice(0, 10) + '.json';
            document.body.appendChild(a);
            a.click();
            URL.revokeObjectURL(url);
            a.remove();
        }

        function exportSupplierLayups(ev) {
            if (ev && ev.shiftKey) {
                exportSupplierLayupsJson();
            } else {
                exportSupplierLayupsCsv();
            }
        }

        function openEditModal(supplier) {
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editSupplierId').value = supplier.supplier_id;
            document.getElementById('editName').value = supplier.name;
            document.getElementById('editEmail').value = supplier.email || '';
            document.getElementById('editLocation').value = supplier.location || '';
            document.getElementById('editCertification').value = supplier.certification || '';
            document.getElementById('editAuditDate').value = (supplier.audit_date || '').toString().slice(0, 10);
            document.getElementById('editStatus').value = supplier.status || 'ACTIVE';
            document.getElementById('editForm').action = '{{ url('/suppliers') }}/' + supplier.supplier_id;
            document.getElementById('editNameError').textContent = '';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Import Modal Functions
        function openImportModal() {
            document.getElementById('importLayupModal').classList.remove('hidden');
            document.getElementById('importLayupForm').reset();
            document.getElementById('selectedFileName').classList.add('hidden');
            document.getElementById('conflictsWarning').classList.add('hidden');
            conflictsData = [];
            conflictResolutions = {};
            manualResolveMode = false;
            uploadedFile = null;
        }

        function closeImportModal() {
            document.getElementById('importLayupModal').classList.add('hidden');
        }

        // Conflict Resolution Modal Functions
        function openConflictModal() {
            document.getElementById('conflictResolutionModal').classList.remove('hidden');
            renderConflictList();
            renderComparison();
        }

        function closeConflictModal() {
            document.getElementById('conflictResolutionModal').classList.add('hidden');
            manualResolveMode = false;
        }

        function getConflictDescription(conflict) {
            const differences = [];
            const existing = conflict.existing_data;
            const incoming = conflict.incoming_data;

            // Check basic properties
            if (existing.thickness !== incoming.thickness) {
                differences.push('Thickness mismatch');
            }
            if (existing.grade !== incoming.grade) {
                differences.push('Grade mismatch');
            }
            if (existing.description !== incoming.description) {
                differences.push('Description changed');
            }

            // Check layers
            const existingLayers = existing.layers || [];
            const incomingLayers = incoming.layers || [];

            const layerDifferences = [];
            for (let i = 0; i < Math.max(existingLayers.length, incomingLayers.length); i++) {
                if (!existingLayers[i] || !incomingLayers[i]) {
                    if (existingLayers.length !== incomingLayers.length) {
                        layerDifferences.push('Layer count changed');
                    }
                } else {
                    const el = existingLayers[i];
                    const il = incomingLayers[i];
                    if (el.thickness !== il.thickness || el.width !== il.width || el.angle !== il.angle) {
                        layerDifferences.push(`Layer ${el.layer_order}`);
                    }
                }
            }

            if (layerDifferences.length > 0) {
                differences.push(`Conflict in ${layerDifferences.length} layers`);
            }

            return differences.length > 0 ? differences.join('; ') : 'Conflict detected';
        }

        function renderConflictList() {
            const list = document.getElementById('conflictsList');

            // Separate resolved and unresolved
            const unresolvedIndices = conflictsData.map((c, i) => ({
                conflict: c,
                index: i
            })).filter(item => !conflictResolutions[item.index]);
            const resolvedIndices = conflictsData.map((c, i) => ({
                conflict: c,
                index: i
            })).filter(item => conflictResolutions[item.index]);

            let html = '';

            // Header with collapse button
            html += `
                <div class="sticky top-0 bg-white border-b border-gray-200 p-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <h3 class="font-semibold text-gray-900">Conflicting Layups (${unresolvedIndices.length})</h3>
                    </div>
                    <div class="flex gap-1">
                        <button class="p-1 hover:bg-gray-100 rounded">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                        </button>
                        <button class="p-1 hover:bg-gray-100 rounded">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;

            // Unresolved conflicts
            html += `<div class="p-4">`;
            unresolvedIndices.forEach(item => {
                const isSelected = item.index === currentConflictIndex;
                html += `
                    <div class="mb-3 p-3 rounded-lg cursor-pointer transition border-2 ${isSelected ? 'bg-green-50 border-green-600' : 'bg-white border-gray-200 hover:border-gray-300'}"
                        onclick="selectConflict(${item.index})">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-gray-900">${item.conflict.incoming_data.name || 'Unknown'}</div>
                                <div class="text-xs text-gray-600 mt-1">${getConflictDescription(item.conflict)}</div>
                            </div>
                            <div class="w-3 h-3 rounded-full bg-red-600 flex-shrink-0 mt-1"></div>
                        </div>
                    </div>
                `;
            });
            html += `</div>`;

            // Resolved section
            if (resolvedIndices.length > 0) {
                html += `
                    <div class="border-t border-gray-200 p-4">
                        <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-3">Resolved</h4>
                        <div class="space-y-2">
                `;
                resolvedIndices.forEach(item => {
                    html += `
                        <div class="p-3 rounded-lg bg-gray-50 border border-gray-200 flex items-start justify-between">
                            <div class="text-sm font-medium text-gray-900">${item.conflict.incoming_data.name || 'Unknown'}</div>
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    `;
                });
                html += `
                        </div>
                    </div>
                `;
            }

            list.innerHTML = html;
        }

        function renderComparison() {
            const conflict = conflictsData[currentConflictIndex];
            const resolution = conflictResolutions[currentConflictIndex] || 'pending';

            const comparisonView = document.getElementById('comparisonView');

            // Get layers based on resolution
            const existingLayers = conflict.existing_data.layers || [];
            const incomingLayers = conflict.incoming_data.layers || [];

            // Determine which data to show based on user resolution
            let selectedLayers = [];
            let selectedData = conflict.incoming_data;

            if (resolution === 'keep') {
                selectedLayers = existingLayers;
                selectedData = conflict.existing_data;
            } else if (resolution === 'accept') {
                selectedLayers = incomingLayers;
                selectedData = conflict.incoming_data;
            } else {
                selectedLayers = null;
            }

            // Build layers table HTML
            let layersHtml = '';
            if (selectedLayers !== null && selectedLayers.length > 0) {
                layersHtml = `
                    <div class="mt-6">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Layer Details (${selectedLayers.length} ${selectedLayers.length === 1 ? 'Layer' : 'Layers'})</h4>
                        <table class="w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 font-semibold text-gray-700 text-left">ORDER</th>
                                    <th class="px-3 py-2 font-semibold text-gray-700 text-left">THICKNESS (MM)</th>
                                    <th class="px-3 py-2 font-semibold text-gray-700 text-left">WIDTH (MM)</th>
                                    <th class="px-3 py-2 font-semibold text-gray-700 text-left">ANGLE (°)</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${selectedLayers.map(layer => `
                                        <tr class="border-b border-gray-200 hover:bg-blue-50">
                                            <td class="px-3 py-2 text-gray-900 font-medium">${layer.layer_order}</td>
                                            <td class="px-3 py-2 text-gray-900">${layer.thickness}</td>
                                            <td class="px-3 py-2 text-gray-900">${layer.width}</td>
                                            <td class="px-3 py-2 text-gray-900">${layer.angle}</td>
                                        </tr>
                                    `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            } else if (selectedLayers === null && (existingLayers.length > 0 || incomingLayers.length > 0)) {
                // Show both for comparison
                layersHtml = `
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Layer Details Comparison</h4>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <h5 class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-2">Existing Layers (${existingLayers.length})</h5>
                                <table class="w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-2 py-2 font-semibold text-gray-700 text-left">ORDER</th>
                                            <th class="px-2 py-2 font-semibold text-gray-700 text-left">THICKNESS</th>
                                            <th class="px-2 py-2 font-semibold text-gray-700 text-left">WIDTH</th>
                                            <th class="px-2 py-2 font-semibold text-gray-700 text-left">ANGLE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${existingLayers.length > 0 ? existingLayers.map(layer => `
                                                <tr class="border-b border-gray-200">
                                                    <td class="px-2 py-1 text-gray-900">${layer.layer_order}</td>
                                                    <td class="px-2 py-1 text-gray-900">${layer.thickness}</td>
                                                    <td class="px-2 py-1 text-gray-900">${layer.width}</td>
                                                    <td class="px-2 py-1 text-gray-900">${layer.angle}</td>
                                                </tr>
                                            `).join('') : '<tr><td colspan="4" class="px-2 py-2 text-gray-500 text-center text-xs">No layers</td></tr>'}
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h5 class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-2">Incoming Layers (${incomingLayers.length})</h5>
                                <table class="w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-2 py-2 font-semibold text-gray-700 text-left">ORDER</th>
                                            <th class="px-2 py-2 font-semibold text-gray-700 text-left">THICKNESS</th>
                                            <th class="px-2 py-2 font-semibold text-gray-700 text-left">WIDTH</th>
                                            <th class="px-2 py-2 font-semibold text-gray-700 text-left">ANGLE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${incomingLayers.length > 0 ? incomingLayers.map(layer => `
                                                <tr class="border-b border-gray-200">
                                                    <td class="px-2 py-1 text-gray-900">${layer.layer_order}</td>
                                                    <td class="px-2 py-1 text-gray-900">${layer.thickness}</td>
                                                    <td class="px-2 py-1 text-gray-900">${layer.width}</td>
                                                    <td class="px-2 py-1 text-gray-900">${layer.angle}</td>
                                                </tr>
                                            `).join('') : '<tr><td colspan="4" class="px-2 py-2 text-gray-500 text-center text-xs">No layers</td></tr>'}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Build content based on whether a resolution was selected
            if (resolution === 'pending') {
                comparisonView.innerHTML = `
                    <div class="mb-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900">${conflict.incoming_data.name}</h3>
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">${incomingLayers.length} LAYERS</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <!-- Existing Layers -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Existing Version</h4>
                            <table class="w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2 font-semibold text-gray-700 text-left">ORDER</th>
                                        <th class="px-3 py-2 font-semibold text-gray-700 text-left">THICKNESS (MM)</th>
                                        <th class="px-3 py-2 font-semibold text-gray-700 text-left">WIDTH (MM)</th>
                                        <th class="px-3 py-2 font-semibold text-gray-700 text-left">ANGLE (°)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${existingLayers.length > 0 ? existingLayers.map(layer => `
                                            <tr class="border-b border-gray-200">
                                                <td class="px-3 py-2 text-gray-900">${layer.layer_order}</td>
                                                <td class="px-3 py-2 text-gray-900">${layer.thickness}</td>
                                                <td class="px-3 py-2 text-gray-900">${layer.width}</td>
                                                <td class="px-3 py-2 text-gray-900">${layer.angle}</td>
                                            </tr>
                                        `).join('') : '<tr><td colspan="4" class="px-3 py-2 text-gray-500 text-center text-xs">No layers</td></tr>'}
                                </tbody>
                            </table>
                        </div>

                        <!-- Importing Layers -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-3">Importing Version</h4>
                            <table class="w-full text-xs border border-gray-200 rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2 font-semibold text-gray-700 text-left">ORDER</th>
                                        <th class="px-3 py-2 font-semibold text-gray-700 text-left">THICKNESS (MM)</th>
                                        <th class="px-3 py-2 font-semibold text-gray-700 text-left">WIDTH (MM)</th>
                                        <th class="px-3 py-2 font-semibold text-gray-700 text-left">ANGLE (°)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${incomingLayers.length > 0 ? incomingLayers.map(layer => `
                                            <tr class="border-b border-gray-200">
                                                <td class="px-3 py-2 text-gray-900">${layer.layer_order}</td>
                                                <td class="px-3 py-2 text-gray-900">${layer.thickness}</td>
                                                <td class="px-3 py-2 text-gray-900">${layer.width}</td>
                                                <td class="px-3 py-2 text-gray-900">${layer.angle}</td>
                                            </tr>
                                        `).join('') : '<tr><td colspan="4" class="px-3 py-2 text-gray-500 text-center text-xs">No layers</td></tr>'}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Resolution Buttons -->
                    <div class="mt-8 flex gap-4 pt-6 border-t border-gray-200">
                        <button type="button" onclick="resolveConflict('keep')"
                            class="flex-1 px-4 py-2 border-2 border-gray-300 text-gray-700 hover:bg-green-50 hover:border-green-500 rounded-md font-medium transition">
                            <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Keep Existing
                        </button>
                        <button type="button" onclick="resolveConflict('accept')"
                            class="flex-1 px-4 py-2 border-2 border-gray-300 text-gray-700 hover:bg-blue-50 hover:border-blue-500 rounded-md font-medium transition">
                            <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Accept New
                        </button>
                    </div>
                `;
            } else {
                // Show selected data only
                comparisonView.innerHTML = `
                    <div class="mb-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900">${selectedData.name}</h3>
                            <div class="flex gap-2">
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">${selectedLayers.length} LAYERS</span>
                                <span class="px-3 py-1 ${resolution === 'keep' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'} text-xs font-semibold rounded-full">
                                    ${resolution === 'keep' ? '✓ Keep Existing' : '✓ Accept New'}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b">
                                <h3 class="text-sm font-semibold text-gray-900">Details</h3>
                            </div>
                            <table class="w-full text-sm">
                                <tbody>
                                    <tr class="border-b border-gray-200">
                                        <td class="px-4 py-3 font-medium bg-gray-50 text-gray-700 w-32">Name</td>
                                        <td class="px-4 py-3 text-gray-900">${selectedData.name}</td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="px-4 py-3 font-medium bg-gray-50 text-gray-700">Description</td>
                                        <td class="px-4 py-3 text-gray-900">${selectedData.description || '-'}</td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="px-4 py-3 font-medium bg-gray-50 text-gray-700">Thickness</td>
                                        <td class="px-4 py-3 text-gray-900">${selectedData.thickness || '-'}</td>
                                    </tr>
                                    <tr class="border-b border-gray-200">
                                        <td class="px-4 py-3 font-medium bg-gray-50 text-gray-700">Grade</td>
                                        <td class="px-4 py-3 text-gray-900">${selectedData.grade || '-'}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 font-medium bg-gray-50 text-gray-700">Status</td>
                                        <td class="px-4 py-3 text-gray-900">${selectedData.status || '-'}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    ${layersHtml}

                    <!-- Change Decision Button -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <button type="button" onclick="resolveConflict('pending')"
                            class="px-4 py-2 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md font-medium transition">
                            ← Change Decision
                        </button>
                    </div>
                `;
            }

            document.getElementById('conflictIndicator').textContent =
                `${currentConflictIndex + 1} of ${conflictsData.length}`;

            document.getElementById('prevConflictBtn').disabled = currentConflictIndex === 0;
            document.getElementById('nextConflictBtn').disabled = currentConflictIndex === conflictsData.length - 1;
        }

        function selectConflict(index) {
            currentConflictIndex = index;
            renderConflictList();
            renderComparison();
        }

        function previousConflict() {
            if (currentConflictIndex > 0) {
                currentConflictIndex--;
                renderConflictList();
                renderComparison();
            }
        }

        function nextConflict() {
            if (currentConflictIndex < conflictsData.length - 1) {
                currentConflictIndex++;
                renderConflictList();
                renderComparison();
            }
        }

        function resolveConflict(resolution) {
            conflictResolutions[currentConflictIndex] = resolution;
            renderConflictList();
            renderComparison();
        }

        // File upload drag and drop
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');

        // Display selected filename and auto-check conflicts
        async function displayFileName() {
            const file = fileInput.files[0];
            if (file) {
                uploadedFile = file;
                document.getElementById('fileNameText').textContent = file.name;
                document.getElementById('selectedFileName').classList.remove('hidden');
                
                await autoCheckConflicts();
            }
        }

        // Auto-check conflicts when file is selected
        async function autoCheckConflicts() {
            const file = fileInput.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', document.querySelector('input[name="_token"]').value);

            const supplierId = '{{ $supplier->supplier_id }}';

            try {
                const response = await fetch(`/suppliers/${supplierId}/check-conflicts`, {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                conflictsData = data.conflicts || [];
                const conflictCount = data.conflict_count || 0;

                const warningDiv = document.getElementById('conflictsWarning');
                const countSpan = document.getElementById('conflictCount');

                if (conflictCount > 0) {
                    countSpan.textContent = conflictCount;
                    warningDiv.classList.remove('hidden');
                } else {
                    warningDiv.classList.add('hidden');
                }
            } catch (error) {
                console.error('Error checking conflicts:', error);
            }
        }

        fileInput.addEventListener('change', displayFileName);

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-blue-400', 'bg-blue-50');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-400', 'bg-blue-50');
            fileInput.files = e.dataTransfer.files;
            displayFileName();
        });

        // Open Conflict Resolution modal from "View details" button
        function openConflictResolutionFromWarning() {
            if (conflictsData.length > 0) {
                manualResolveMode = true;
                currentConflictIndex = 0;
                conflictResolutions = {};
                openConflictModal();
            }
        }

        // Confirm Import from main form button - use dropdown strategy
        function confirmImportFromForm() {
            const form = document.getElementById('importLayupForm');
            
            if (conflictsData.length === 0) {
                form.submit();
            } else if (!manualResolveMode) {
                form.submit();
            } else {
                alert('Please resolve conflicts in the modal window');
            }
        }

        // Confirm and submit from Conflict Resolution modal
        function confirmResolveConflictsAndSubmit() {
            const allResolved = Object.keys(conflictResolutions).length === conflictsData.length;
            if (!allResolved) {
                alert('{{ __('Please resolve all conflicts before continuing') }}');
                return;
            }

            const form = document.getElementById('importLayupForm');
            const resolutionsInput = document.createElement('input');
            resolutionsInput.type = 'hidden';
            resolutionsInput.name = 'conflict_resolutions';
            resolutionsInput.value = JSON.stringify(conflictResolutions);
            form.appendChild(resolutionsInput);

            closeConflictModal();
            form.submit();
        }

        // Import Form Submission
        const importForm = document.getElementById('importLayupForm');

        importForm.addEventListener('submit', function(e) {
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            let editModal = document.getElementById('editModal');
            let createLayupModal = document.getElementById('createLayupModal');
            let importLayupModal = document.getElementById('importLayupModal');
            let conflictModal = document.getElementById('conflictResolutionModal');

            if (editModal && event.target == editModal) {
                editModal.classList.add('hidden');
            }
            if (event.target == createLayupModal) {
                createLayupModal.classList.add('hidden');
            }
            if (event.target == importLayupModal) {
                importLayupModal.classList.add('hidden');
            }
            if (event.target == conflictModal) {
                conflictModal.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
