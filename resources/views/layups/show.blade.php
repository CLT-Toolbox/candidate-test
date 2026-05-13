<x-app-layout>
    <x-slot name="header">
        <p class="page-crumb">Suppliers / {{ $supplier->name }} / Layups / {{ $layup->name }}</p>
    </x-slot>

    <div class="pb-10">
        <div class="page-wrap space-y-5 fade-rise">
            @if (session('status'))
                <div class="status-success">{{ session('status') }}</div>
            @endif

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('suppliers.layups.index', $supplier) }}" class="btn-secondary">Back</a>
                <form method="POST" action="{{ route('suppliers.layups.duplicate', [$supplier, $layup]) }}">
                    @csrf
                    <button type="submit" class="btn-secondary">Duplicate</button>
                </form>
                <a href="{{ route('suppliers.layups.edit', [$supplier, $layup]) }}" class="btn-primary">Edit Layup</a>
            </div>

            <div class="app-card">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="page-title page-title-xl">Layup Specification: {{ $layup->name }}</h2>
                        <p class="page-subtitle mt-1">{{ $layup->description ?? 'Cross-laminated panel for structural usage.' }}</p>
                    </div>
                    <div class="brand-tag">Active</div>
                </div>

                <div class="kpi-strip mt-5 p-0">
                    <div class="kpi-item">
                        <p class="kpi-label">Created By</p>
                        <p class="kpi-value">Engineering Team</p>
                    </div>
                    <div class="kpi-item">
                        <p class="kpi-label">Last Modified</p>
                        <p class="kpi-value">{{ optional($layup->updated_at)->format('M d, Y') }}</p>
                    </div>
                    <div class="kpi-item">
                        <p class="kpi-label">Total Thickness</p>
                        <p class="kpi-value">{{ number_format($layup->layers->sum('thickness'), 2) }} mm</p>
                    </div>
                    <div class="kpi-item">
                        <p class="kpi-label">Total Layers</p>
                        <p class="kpi-value">{{ $layup->layers->count() }} Layers</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-5 lg:grid-cols-2">
                <div class="space-y-4">
                    <div class="app-card">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="section-title">Layer Composition</h3>
                            <a href="{{ route('suppliers.layups.layers.create', [$supplier, $layup]) }}" class="app-link">+ Add Layer</a>
                        </div>

                        <div class="table-shell">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Thickness</th>
                                        <th>Width</th>
                                        <th>Angle</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($layup->layers as $layer)
                                        <tr>
                                            <td>{{ $layer->layer_order }}</td>
                                            <td>{{ $layer->thickness }} mm</td>
                                            <td>{{ $layer->width }} mm</td>
                                            <td>
                                                <span class="badge">{{ $layer->angle }} deg</span>
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ route('suppliers.layups.layers.show', [$supplier, $layup, $layer]) }}" class="app-link">Open</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="muted-copy py-4 text-center">No layers yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="soft-panel">
                        <p class="font-semibold">Engineering Note</p>
                        <p class="muted-copy text-sm mt-1">Verify layer sequence orientation before committing supplier-wide changes.</p>
                    </div>
                </div>

                <div class="app-card">
                    <h3 class="section-title mb-3">Structure Visualizer</h3>
                    <div class="layer-stack">
                        @forelse($layup->layers->sortBy('layer_order') as $layer)
                            <div class="layer-block">
                                <span>L{{ $layer->layer_order }} ({{ rtrim(rtrim(number_format((float) $layer->thickness, 2, '.', ''), '0'), '.') }}mm)</span>
                                <span>{{ $layer->angle == 0 ? '||' : ($layer->angle > 0 ? '+'.$layer->angle : $layer->angle) }} deg</span>
                            </div>
                        @empty
                            <p class="muted-copy text-sm text-center py-6">Add layers to visualize stack orientation.</p>
                        @endforelse
                    </div>
                    <p class="muted-copy text-xs mt-3 text-center">Cross-laminated structural assembly preview.</p>
                </div>
            </div>

            <div class="flex justify-start">
                <form method="POST" action="{{ route('suppliers.layups.destroy', [$supplier, $layup]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger" onclick="return confirm('Delete this layup?')">Delete Layup</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
