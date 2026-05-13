<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="page-title">Layer Catalog</h2>
                <p class="page-subtitle mt-1">Browse individual layers across all layups.</p>
            </div>
            <a href="{{ route('suppliers.index') }}" class="btn-secondary">Go to Suppliers</a>
        </div>
    </x-slot>

    <div class="pb-10">
        <div class="page-wrap space-y-4 fade-rise">
            <form class="table-toolbar app-card" method="GET" action="{{ route('layers.catalog') }}">
                <input class="search-input" type="text" name="q" value="{{ $search }}" placeholder="Search by layup or supplier..." aria-label="Search layers">
                <div class="flex items-center gap-2">
                    <button class="btn-secondary" type="submit">Filter</button>
                    @if ($search !== '')
                        <a href="{{ route('layers.catalog') }}" class="btn-secondary">Reset</a>
                    @endif
                </div>
            </form>

            <div class="app-card">
                <div class="table-shell">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Supplier</th>
                                <th>Layup</th>
                                <th>Order</th>
                                <th>Thickness</th>
                                <th>Width</th>
                                <th>Angle</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($layers as $layer)
                                <tr>
                                    <td>{{ $layer->layup?->supplier?->name ?? '-' }}</td>
                                    <td>{{ $layer->layup?->name ?? '-' }}</td>
                                    <td>{{ $layer->layer_order }}</td>
                                    <td>{{ $layer->thickness }}</td>
                                    <td>{{ $layer->width }}</td>
                                    <td>{{ $layer->angle }}</td>
                                    <td class="text-right">
                                        @if ($layer->layup && $layer->layup->supplier)
                                            <a class="app-link" href="{{ route('suppliers.layups.layers.show', [$layer->layup->supplier, $layer->layup, $layer]) }}">Open</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="muted-copy py-6 text-center">No layers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $layers->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
