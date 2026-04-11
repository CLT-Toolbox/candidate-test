<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Suppliers</h2>
                <p class="text-sm text-gray-500 mt-1">Manage timber suppliers and material sourcing.</p>
            </div>
            <button
                x-data
                @click="$dispatch('open-modal', 'create-supplier')"
                class="inline-flex items-center px-4 py-2 bg-green-700 text-white text-sm font-medium rounded-lg hover:bg-green-800 transition">
                + Add Supplier
            </button>
        </div>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">{{ session('error') }}</div>
        @endif

        <!-- Filter & Actions -->
        <div class="mb-6 flex items-center justify-between gap-4">
            <div class="flex-1">
                <input type="text" placeholder="Search suppliers by name..."
                    x-data
                    @input="
                        const q = $event.target.value.toLowerCase();
                        document.querySelectorAll('[data-supplier]').forEach(el => {
                            el.style.display = el.dataset.supplier.toLowerCase().includes(q) ? '' : 'none';
                        });
                    "
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
            </div>
            <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-50 transition">
                Filter
            </button>
            <a href="#" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm rounded-lg hover:bg-gray-50 transition">
                Export
            </a>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Total Layups</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Created At</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($suppliers as $supplier)
                    <tr data-supplier="{{ $supplier->name }}" class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <!-- Avatar with dynamic color -->
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-green-600 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                                    {{ strtoupper(substr($supplier->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $supplier->name }}</div>
                                    <div class="text-xs text-gray-500">ID: SUP-{{ str_pad($supplier->id, 4, '0', STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700 font-medium">{{ $supplier->layups_count }}</td>
                        <td class="px-6 py-4 text-gray-500 text-sm">{{ $supplier->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('suppliers.show', $supplier) }}"
                                    class="text-sm font-medium text-green-700 hover:underline transition">View</a>
                                <button
                                    x-data
                                    @click="$dispatch('open-modal', 'edit-supplier-{{ $supplier->id }}')"
                                    class="text-sm text-blue-600 hover:underline transition">Edit</button>
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}"
                                    onsubmit="return confirm('Delete this supplier?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm text-red-500 hover:underline transition">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <x-modal name="edit-supplier-{{ $supplier->id }}">
                        <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="p-6">
                            @csrf @method('PATCH')
                            <h2 class="text-lg font-semibold mb-4">Edit Supplier</h2>
                            <div class="mb-4">
                                <x-input-label for="name" value="Name" />
                                <x-text-input name="name" class="mt-1 block w-full" value="{{ $supplier->name }}" required />
                            </div>
                            <div class="flex justify-end gap-2">
                                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                <x-primary-button>Save</x-primary-button>
                            </div>
                        </form>
                    </x-modal>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                            <div class="text-sm">No suppliers found.</div>
                            <button x-data @click="$dispatch('open-modal', 'create-supplier')" class="text-green-700 hover:underline mt-2 text-sm">
                                Create one
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if($suppliers->hasPages())
            <div class="px-6 py-4 border-t bg-gray-50">
                {{ $suppliers->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Create Modal -->
    <x-modal name="create-supplier">
        <form method="POST" action="{{ route('suppliers.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-semibold mb-4">Add Supplier</h2>
            <div class="mb-4">
                <x-input-label for="name" value="Name" />
                <x-text-input name="name" class="mt-1 block w-full" placeholder="e.g. Nordic Timber Co." required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>
            <div class="flex justify-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Create</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
