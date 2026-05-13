<x-app-layout>
    <x-slot name="header">
        <p class="page-crumb">Suppliers / {{ $supplier->name }}</p>
    </x-slot>

    <div class="pb-10" x-data="{ importOpen: false, importSubmitting: false }">
        <div class="page-wrap space-y-5 fade-rise">
            @if (session('status'))
                <div class="status-success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="status-error">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="app-card">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="page-title page-title-xl">{{ $supplier->name }}</h2>
                        <div class="mt-2 flex items-center gap-3">
                            <span class="muted-copy text-sm">ID: {{ $supplier->code ?? 'SUP-'.str_pad((string) $supplier->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="brand-tag">Active Partner</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('suppliers.index') }}" class="btn-secondary">Back</a>
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn-secondary">Edit Supplier</a>
                    </div>
                </div>
            </div>

            <div class="kpi-strip app-card p-0">
                <div class="kpi-item">
                    <p class="kpi-label">Primary Contact</p>
                    <p class="kpi-value">{{ strtolower(str_replace(' ', '.', $supplier->name)) }}@clt.local</p>
                </div>
                <div class="kpi-item">
                    <p class="kpi-label">Location</p>
                    <p class="kpi-value">{{ $supplier->address ?? 'Location not set' }}</p>
                </div>
                <div class="kpi-item">
                    <p class="kpi-label">Total Layups</p>
                    <p class="kpi-value">{{ $supplier->layups->count() }} Layups</p>
                </div>
                <div class="kpi-item">
                    <p class="kpi-label">Last Updated</p>
                    <p class="kpi-value">{{ optional($supplier->updated_at)->format('M d, Y') }}</p>
                </div>
            </div>

            <div class="app-card">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="page-title page-title-lg">Associated Layups</h3>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="btn-secondary" @click="importOpen = true">Import</button>
                        <a href="{{ route('suppliers.export', $supplier) }}" class="btn-secondary">Export</a>
                        <a href="{{ route('suppliers.layups.create', $supplier) }}" class="btn-primary">+ Add Layup</a>
                    </div>
                </div>

                <div class="table-shell">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Layup ID</th>
                                <th>Name</th>
                                <th>Ply Count</th>
                                <th>Updated</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($supplier->layups as $layup)
                                <tr>
                                    <td class="muted-copy text-xs">L-{{ str_pad((string) $layup->id, 3, '0', STR_PAD_LEFT) }}</td>
                                    <td class="font-semibold">{{ $layup->name }}</td>
                                    <td>{{ $layup->layers->count() }}</td>
                                    <td>{{ optional($layup->updated_at)->format('M d, Y') }}</td>
                                    <td><span class="brand-tag">Active</span></td>
                                    <td class="text-right">
                                        <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}" class="app-link">Open</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="muted-copy py-6 text-center">No layups available for this supplier.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($importReport)
                <div class="app-card">
                    <h3 class="text-lg font-semibold mb-3">Import Result</h3>
                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3 text-sm">
                        <p><span class="font-medium">Strategy:</span> {{ $importReport['strategy'] }}</p>
                        <p><span class="font-medium">Created Layups:</span> {{ $importReport['summary']['created_layups'] }}</p>
                        <p><span class="font-medium">Created Layers:</span> {{ $importReport['summary']['created_layers'] }}</p>
                        <p><span class="font-medium">Updated Layers:</span> {{ $importReport['summary']['updated_layers'] }}</p>
                        <p><span class="font-medium">Skipped Layers:</span> {{ $importReport['summary']['skipped_layers'] }}</p>
                        <p><span class="font-medium">Conflicts:</span> {{ $importReport['summary']['conflicts_count'] }}</p>
                    </div>

                    @if (($importReport['strategy'] ?? null) === 'reject' && ($importReport['summary']['conflicts_count'] ?? 0) > 0)
                        <div class="panel-divider mt-4 border-t pt-4">
                            <p class="muted-copy text-sm mb-2">Import was rejected due to conflicts. Open the resolver to decide each conflict manually.</p>
                            <a href="{{ route('suppliers.conflicts', $supplier) }}" class="btn-primary">Open Conflict Resolver</a>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <template x-if="importOpen">
            <div>
                <div class="import-modal-backdrop" @click="importOpen = false"></div>
                <div class="import-modal" role="dialog" aria-modal="true">
                    <div class="import-modal-card">
                        <div class="panel-divider flex items-center justify-between border-b px-5 py-4">
                            <h3 class="section-title">Import Layup Data</h3>
                            <button class="icon-button-muted" type="button" @click="importOpen = false">x</button>
                        </div>
                        <form method="POST" action="{{ route('suppliers.import', $supplier) }}" enctype="multipart/form-data" class="space-y-4 p-5" @submit="importSubmitting = true">
                            @csrf
                            <div class="dropzone">
                                <p class="import-dropzone-title font-semibold">Click to upload</p>
                                <p class="muted-copy text-sm mt-1">CSV or JSON up to 10MB</p>
                                <input type="file" name="file" class="field-input mt-4" required />
                            </div>

                            <div>
                                <label class="block text-sm font-medium">Conflict Resolution Strategy</label>
                                <select name="strategy" class="field-select" required>
                                    <option value="skip">Skip conflicts (Default)</option>
                                    <option value="overwrite">Overwrite existing</option>
                                    <option value="duplicate_layup">Duplicate layup</option>
                                    <option value="reject">Reject import</option>
                                </select>
                            </div>

                            <div class="soft-panel">
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" class="rounded" disabled>
                                    <span>Run as Dry Run</span>
                                </label>
                                <p class="muted-copy text-xs mt-1">Simulate import without saving changes.</p>
                            </div>

                            <div class="status-error">
                                <p class="font-semibold">Potential Conflicts Detected</p>
                                <p class="text-sm mt-1">Conflicting layers will be handled using selected strategy.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium">JSON Payload (optional)</label>
                                <textarea name="payload" rows="4" class="field-textarea" placeholder='{"layups":[...]}'>{{ old('payload') }}</textarea>
                            </div>

                            <div class="panel-divider flex justify-end gap-2 border-t pt-4">
                                <button type="button" class="btn-secondary" @click="importOpen = false" :disabled="importSubmitting">Cancel</button>
                                <button type="submit" class="btn-primary" :disabled="importSubmitting" x-text="importSubmitting ? 'Importing...' : 'Confirm Import'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
