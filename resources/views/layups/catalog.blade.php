<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="page-title">Layup Catalog</h2>
                <p class="page-subtitle mt-1">Browse layups across all suppliers.</p>
            </div>
            <a href="{{ route('suppliers.index') }}" class="btn-secondary">Go to Suppliers</a>
        </div>
    </x-slot>

    <div class="pb-10">
        <div class="page-wrap space-y-4 fade-rise">
            <form class="table-toolbar app-card" method="GET" action="{{ route('layups.catalog') }}">
                <input class="search-input" type="text" name="q" value="{{ $search }}" placeholder="Search layup name, description, supplier..." aria-label="Search layups">
                <div class="flex items-center gap-2">
                    <button class="btn-secondary" type="submit">Filter</button>
                    @if ($search !== '')
                        <a href="{{ route('layups.catalog') }}" class="btn-secondary">Reset</a>
                    @endif
                </div>
            </form>

            <div class="app-card">
                <div class="table-shell">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Layup</th>
                                <th>Supplier</th>
                                <th>Layers</th>
                                <th>Updated</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($layups as $layup)
                                <tr>
                                    <td>
                                        <p class="font-semibold">{{ $layup->name }}</p>
                                        <p class="muted-copy text-xs">{{ $layup->description ?: 'No description' }}</p>
                                    </td>
                                    <td>{{ $layup->supplier?->name ?? '-' }}</td>
                                    <td>{{ $layup->layers_count }}</td>
                                    <td>{{ optional($layup->updated_at)->format('M d, Y') }}</td>
                                    <td class="text-right">
                                        @if ($layup->supplier)
                                            <a class="app-link" href="{{ route('suppliers.layups.show', [$layup->supplier, $layup]) }}">Open</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="muted-copy py-6 text-center">No layups found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $layups->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
