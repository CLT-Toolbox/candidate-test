<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="page-title">Suppliers</h2>
                <p class="page-subtitle mt-1">Manage timber suppliers and material sourcing.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="btn-secondary">Back</a>
                <a href="{{ route('suppliers.create') }}" class="btn-primary">+ Add Supplier</a>
            </div>
        </div>
    </x-slot>

    <div class="pb-10">
        <div class="page-wrap space-y-4 fade-rise">
            @if (session('status'))
                <div class="status-success">{{ session('status') }}</div>
            @endif

            <div class="app-card">
                <form class="table-toolbar" method="GET" action="{{ route('suppliers.index') }}">
                    <input class="search-input" type="text" name="q" value="{{ $search }}" placeholder="Search suppliers by name, code, address..." aria-label="Search suppliers">
                    <div class="flex items-center gap-2">
                        <button class="btn-secondary" type="submit">Filter</button>
                        @if ($search !== '')
                            <a href="{{ route('suppliers.index') }}" class="btn-secondary">Reset</a>
                        @endif
                        <a href="{{ route('suppliers.export.index', ['q' => $search]) }}" class="btn-secondary">Export CSV</a>
                    </div>
                </form>

                <div class="table-shell">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Total Layups</th>
                                <th>Created At</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($suppliers as $supplier)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <span class="avatar-pill">{{ strtoupper(substr($supplier->name, 0, 2)) }}</span>
                                            <div>
                                                <p class="font-semibold">{{ $supplier->name }}</p>
                                                <p class="muted-copy text-xs">ID: {{ $supplier->code ?? 'SUP-'.$supplier->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $supplier->layups_count }}</td>
                                    <td>{{ optional($supplier->created_at)->format('M d, Y') }}</td>
                                    <td class="space-x-3 text-right">
                                        <a class="app-link" href="{{ route('suppliers.show', $supplier) }}">View</a>
                                        <a class="app-link-warn" href="{{ route('suppliers.edit', $supplier) }}">Edit</a>
                                        <form class="inline" method="POST" action="{{ route('suppliers.destroy', $supplier) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="app-link-danger" onclick="return confirm('Delete this supplier?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="muted-copy py-5 text-center">No suppliers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4 flex items-center justify-between text-xs">
                        <p class="muted-copy">Showing {{ $suppliers->count() }} of {{ $suppliers->total() }} suppliers</p>
                        <div>{{ $suppliers->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
