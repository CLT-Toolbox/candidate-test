<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="text-sm font-medium text-gray-500">Supplier</div>
                <h2 class="text-2xl font-semibold leading-tight text-gray-900">{{ $supplier->name }}</h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $supplier->layups->count() }} layups and {{ $supplier->layups->sum(fn ($layup) => $layup->layers->count()) }} layers
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('suppliers.layups.create', $supplier) }}" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">
                    Add layup
                </a>
                <a href="{{ route('suppliers.edit', $supplier) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                    Edit supplier
                </a>
                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Delete this supplier and every related layup/layer?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-lg border border-rose-300 px-4 py-2 text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                        Delete supplier
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('partials.alerts')

            @if (session('import_summary'))
                @php($summary = session('import_summary'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm text-emerald-900">
                    <p class="font-semibold">Import summary</p>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="rounded-xl bg-white/70 px-4 py-3">Created layups: {{ $summary['created_layups'] }}</div>
                        <div class="rounded-xl bg-white/70 px-4 py-3">Created layers: {{ $summary['created_layers'] }}</div>
                        <div class="rounded-xl bg-white/70 px-4 py-3">Updated layers: {{ $summary['updated_layers'] }}</div>
                        <div class="rounded-xl bg-white/70 px-4 py-3">Accepted incoming conflicts: {{ $summary['accepted_incoming_conflicts'] }}</div>
                        <div class="rounded-xl bg-white/70 px-4 py-3">Kept existing conflicts: {{ $summary['kept_existing_conflicts'] }}</div>
                        <div class="rounded-xl bg-white/70 px-4 py-3">Unchanged layers: {{ $summary['unchanged_layers'] }}</div>
                    </div>
                </div>
            @endif

            @if (session('import_report'))
                @php($report = session('import_report'))
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                    <p class="text-sm font-semibold text-amber-900">Conflict report</p>
                    <p class="mt-1 text-sm text-amber-800">
                        Strategy: <span class="font-semibold">{{ ucfirst($report['strategy']) }}</span>. {{ count($report['conflicts']) }} conflict(s) were detected.
                    </p>
                    <div class="mt-4 space-y-3">
                        @foreach ($report['conflicts'] as $conflict)
                            <div class="rounded-xl border border-amber-200 bg-white px-4 py-3 text-sm text-amber-900">
                                <p class="font-semibold">{{ $conflict['layup_name'] }} - Layer {{ $conflict['layer_order'] }}</p>
                                <p class="mt-1 text-amber-700">Different fields: {{ implode(', ', array_keys($conflict['differences'])) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Layups & layers</h3>
                            <p class="mt-1 text-sm text-gray-500">Each layup stays nested under the current supplier, and every layer stays nested under its layup.</p>
                        </div>
                        <a href="{{ route('suppliers.layups.create', $supplier) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                            New layup
                        </a>
                    </div>

                    <div class="mt-6 space-y-5">
                        @forelse ($supplier->layups as $layup)
                            <article class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                                <div class="flex flex-col gap-4 border-b border-gray-200 pb-4 lg:flex-row lg:items-center lg:justify-between">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">{{ $layup->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ $layup->layers->count() }} layers</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('suppliers.layups.layers.create', [$supplier, $layup]) }}" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">
                                            Add layer
                                        </a>
                                        <a href="{{ route('suppliers.layups.edit', [$supplier, $layup]) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                                            Edit layup
                                        </a>
                                        <form method="POST" action="{{ route('suppliers.layups.destroy', [$supplier, $layup]) }}" onsubmit="return confirm('Delete this layup and all of its layers?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-rose-300 px-4 py-2 text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="mt-4 overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            <tr>
                                                <th class="py-3 pe-4">Order</th>
                                                <th class="py-3 pe-4">Thickness</th>
                                                <th class="py-3 pe-4">Width</th>
                                                <th class="py-3 pe-4">Angle</th>
                                                <th class="py-3 text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 text-gray-700">
                                            @forelse ($layup->layers as $layer)
                                                <tr>
                                                    <td class="py-3 pe-4 font-semibold text-gray-900">{{ $layer->layer_order }}</td>
                                                    <td class="py-3 pe-4">{{ number_format((float) $layer->thickness, 2) }}</td>
                                                    <td class="py-3 pe-4">{{ number_format((float) $layer->width, 2) }}</td>
                                                    <td class="py-3 pe-4">{{ number_format((float) $layer->angle, 2) }}</td>
                                                    <td class="py-3">
                                                        <div class="flex justify-end gap-2">
                                                            <a href="{{ route('suppliers.layups.layers.edit', [$supplier, $layup, $layer]) }}" class="rounded-lg border border-gray-300 px-3 py-2 font-medium text-gray-700 transition hover:bg-white">
                                                                Edit
                                                            </a>
                                                            <form method="POST" action="{{ route('suppliers.layups.layers.destroy', [$supplier, $layup, $layer]) }}" onsubmit="return confirm('Delete this layer?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="rounded-lg border border-rose-300 px-3 py-2 font-medium text-rose-600 transition hover:bg-rose-50">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="py-8 text-center text-sm text-gray-500">No layers in this layup yet.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-6 py-12 text-center">
                                <p class="text-base font-semibold text-gray-900">No layups yet.</p>
                                <p class="mt-1 text-sm text-gray-500">Create your first layup, then start adding CLT layers beneath it.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <aside class="space-y-6">
                    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Import / Export</h3>
                                <p class="mt-1 text-sm text-gray-500">JSON payloads include the supplier, all layups, and all layers.</p>
                            </div>
                            <a href="{{ route('suppliers.export', $supplier) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                                Export JSON
                            </a>
                        </div>

                        <form method="POST" action="{{ route('suppliers.import.preview', $supplier) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="import_file" value="Import file" />
                                <input id="import_file" name="import_file" type="file" accept=".json,application/json,text/plain" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500" required>
                            </div>

                            <div>
                                <x-input-label for="conflict_strategy" value="Conflict strategy" />
                                <select id="conflict_strategy" name="conflict_strategy" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-gray-500 focus:ring-gray-500">
                                    @foreach ($importStrategies as $value => $label)
                                        <option value="{{ $value }}" @selected(old('conflict_strategy', \App\Services\Suppliers\SupplierImportService::STRATEGY_MANUAL) === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="rounded-xl bg-gray-50 p-4 text-xs leading-6 text-gray-600">
<pre class="overflow-x-auto whitespace-pre-wrap">{
  "supplier": { "name": "{{ $supplier->name }}" },
  "layups": [
    {
      "name": "Sample Layup",
      "layers": [
        { "layer_order": 1, "thickness": 40, "width": 120, "angle": 0 }
      ]
    }
  ]
}</pre>
                            </div>

                            <button type="submit" class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">
                                Run import
                            </button>
                        </form>
                    </section>

                    @if ($hasPendingConflictResolution)
                        <section class="rounded-2xl border border-sky-200 bg-sky-50 p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-sky-900">Pending manual resolution</h3>
                            <p class="mt-1 text-sm text-sky-800">A previous import is waiting for conflict decisions. You can resume it anytime.</p>
                            <div class="mt-4">
                                <a href="{{ route('suppliers.import.conflicts', $supplier) }}" class="inline-flex items-center rounded-lg bg-sky-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-800">
                                    Resolve conflicts
                                </a>
                            </div>
                        </section>
                    @endif
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
