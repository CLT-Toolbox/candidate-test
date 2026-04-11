<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Suppliers</h2>
                <p class="mt-1 text-sm text-gray-500">Manage supplier records and drill into layups plus layers.</p>
            </div>
            <a href="{{ route('suppliers.create') }}" class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">
                Add supplier
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('partials.alerts')

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-6 py-4">Supplier</th>
                                <th class="px-6 py-4">Layups</th>
                                <th class="px-6 py-4">Layers</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-700">
                            @forelse ($suppliers as $supplier)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-gray-900">{{ $supplier->name }}</div>
                                        <div class="text-xs text-gray-500">Created {{ $supplier->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4">{{ $supplier->layups_count }}</td>
                                    <td class="px-6 py-4">{{ $supplier->layers_count }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('suppliers.show', $supplier) }}" class="rounded-lg border border-gray-300 px-3 py-2 font-medium text-gray-700 transition hover:bg-gray-100">
                                                Open
                                            </a>
                                            <a href="{{ route('suppliers.edit', $supplier) }}" class="rounded-lg border border-gray-300 px-3 py-2 font-medium text-gray-700 transition hover:bg-gray-100">
                                                Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="mx-auto max-w-md space-y-2">
                                            <p class="text-base font-semibold text-gray-900">No suppliers yet.</p>
                                            <p class="text-sm text-gray-500">Create the first supplier to start building layups and layers.</p>
                                            <div class="pt-2">
                                                <a href="{{ route('suppliers.create') }}" class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">
                                                    Create supplier
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($suppliers->hasPages())
                    <div class="border-t border-gray-200 px-6 py-4">
                        {{ $suppliers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
