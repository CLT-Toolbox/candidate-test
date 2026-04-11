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
            <div class="flex-1 relative" x-data="{ searchQuery: '' }">
                <div class="relative">
                    <input type="text"
                        placeholder="Search suppliers by name..."
                        x-model="searchQuery"
                        @input="
                            const q = searchQuery.toLowerCase();
                            let visibleCount = 0;
                            document.querySelectorAll('[data-supplier]').forEach(el => {
                                const matches = el.dataset.supplier.toLowerCase().includes(q);
                                el.style.display = matches ? '' : 'none';
                                if (matches) visibleCount++;
                            });
                            document.getElementById('searchCount').textContent = visibleCount;
                            document.getElementById('totalCount').textContent = document.querySelectorAll('[data-supplier]').length;
                        "
                        style="width:320px; max-width:320px; height:40px; opacity:1; transform:rotate(0deg);"
                        class="px-4 pr-10 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-600"
                    >
                    <!-- Clear Button -->
                    <button
                        x-show="searchQuery.length > 0"
                        @click="
                            searchQuery = '';
                            document.querySelectorAll('[data-supplier]').forEach(el => {
                                el.style.display = '';
                            });
                            document.getElementById('searchCount').textContent = document.querySelectorAll('[data-supplier]').length;
                            document.getElementById('totalCount').textContent = document.querySelectorAll('[data-supplier]').length;
                        "
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <!-- Search Results Counter -->
                <div x-show="searchQuery.length > 0" class="absolute left-0 top-full mt-1 text-xs text-gray-500 bg-gray-50 px-3 py-1 rounded border border-gray-200">
                    Found <span id="searchCount" class="font-semibold">{{ count($suppliers->items()) }}</span> of
                    <span id="totalCount" class="font-semibold">{{ count($suppliers->items()) }}</span> results
                </div>
            </div>

            <!-- Sort Filter Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm rounded-lg hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707l-6.414 6.414A2 2 0 0013 14.586V19a1 1 0 01-1.447.894l-2-1A1 1 0 019 18v-3.414a2 2 0 00-.293-1.172L2.293 6.707A1 1 0 012 6V4z" />
                    </svg>
                    Filter
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10"
                    x-transition>
                    <a href="?sort=recent&per_page={{ $perPage }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 first:rounded-t-lg {{ request('sort') === 'recent' ? 'bg-green-50 text-green-700 font-semibold' : '' }}">Recent Update</a>
                    <a href="?sort=created&per_page={{ $perPage }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('sort') === 'created' || !request('sort') ? 'bg-green-50 text-green-700 font-semibold' : '' }}">Date Added</a>
                    <a href="?sort=updated&per_page={{ $perPage }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request('sort') === 'updated' ? 'bg-green-50 text-green-700 font-semibold' : '' }}">Last Updated</a>
                    <a href="?sort=name&per_page={{ $perPage }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 last:rounded-b-lg {{ request('sort') === 'name' ? 'bg-green-50 text-green-700 font-semibold' : '' }}">Name</a>
                </div>
            </div>

            <!-- Per Page Filter Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm rounded-lg hover:bg-gray-50 transition">
                    Show {{ $perPage }}
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-32 bg-white border border-gray-200 rounded-lg shadow-lg z-10"
                    x-transition>
                    <a href="?sort={{ request('sort', 'created') }}&per_page=5" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 first:rounded-t-lg {{ $perPage === 5 ? 'bg-green-50 text-green-700 font-semibold' : '' }}">Show 5</a>
                    <a href="?sort={{ request('sort', 'created') }}&per_page=10" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $perPage === 10 ? 'bg-green-50 text-green-700 font-semibold' : '' }}">Show 10</a>
                    <a href="?sort={{ request('sort', 'created') }}&per_page=15" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $perPage === 15 ? 'bg-green-50 text-green-700 font-semibold' : '' }}">Show 15</a>
                    <a href="?sort={{ request('sort', 'created') }}&per_page=20" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 last:rounded-b-lg {{ $perPage === 20 ? 'bg-green-50 text-green-700 font-semibold' : '' }}">Show 20</a>
                </div>
            </div>

            <!-- Export Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm rounded-lg hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16v2a2 2 0 002 2h14a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                    </svg>
                    Export
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-10"
                    x-transition>
                    <a href="{{ route('suppliers.export-list') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 first:rounded-t-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        JSON
                    </a>
                    <a href="{{ route('suppliers.export-list-csv') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        CSV
                    </a>
                    <a href="{{ route('suppliers.export-list-pdf') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 last:rounded-b-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Showing Results -->
        <!-- <div class="mb-4 text-sm text-gray-500">
            Showing {{ $suppliers->firstItem() }} to {{ $suppliers->lastItem() }} of {{ $suppliers->total() }} results
        </div> -->

        <!-- Table -->
        <div class="bg-white rounded-xl overflow-hidden border border-gray-200">
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
                            @php
                                $bgColors = [
                                    'rgba(96, 165, 250, 0.2)',    // blue-400 - 70% opacity
                                    'rgba(74, 222, 128, 0.2)',    // green-400 - 70% opacity
                                    'rgba(251, 146, 60, 0.2)',    // orange-400 - 70% opacity
                                    'rgba(192, 132, 250, 0.2)',   // purple-400 - 70% opacity
                                    'rgba(34, 211, 238, 0.2)',    // cyan-400 - 70% opacity
                                    'rgba(244, 114, 182, 0.2)',   // pink-400 - 70% opacity
                                    'rgba(129, 140, 248, 0.2)',   // indigo-400 - 70% opacity
                                    'rgba(251, 113, 133, 0.2)',   // rose-400 - 70% opacity
                                ];
                                $strongColors = [
                                    'rgb(37, 99, 235)',      // blue-600
                                    'rgb(22, 163, 74)',      // green-600
                                    'rgb(234, 88, 12)',      // orange-600
                                    'rgb(147, 51, 234)',     // purple-600
                                    'rgb(8, 145, 178)',      // cyan-600
                                    'rgb(219, 39, 119)',     // pink-600
                                    'rgb(79, 70, 229)',      // indigo-600
                                    'rgb(225, 29, 72)',      // rose-600
                                ];

                                $colorIndex = abs(crc32($supplier->name)) % count($bgColors);
                                $selectedBgColor = $bgColors[$colorIndex];
                                $selectedStrongColor = $strongColors[$colorIndex];
                            @endphp
                            <div
                                class="w-10 h-10 rounded-full font-bold flex items-center justify-center text-sm shadow-sm"
                                style="
                                    background-color: {{ $selectedBgColor }};
                                    border: 1px solid {{ $selectedStrongColor }};
                                    color: {{ $selectedStrongColor }};
                                ">
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

            <!-- Pagination at Bottom of Table -->
            <div class="px-6 py-4 border-t bg-white flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Showing {{ $suppliers->firstItem() }} to {{ $suppliers->lastItem() }} of {{ $suppliers->total() }} results
                </div>
                <div class="flex gap-2 items-center">
                    @if ($suppliers->onFirstPage())
                        <button disabled class="px-3 py-2 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                    @else
                        <a href="{{ $suppliers->previousPageUrl() }}&per_page={{ $perPage }}" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    @endif

                    @if ($suppliers->hasMorePages())
                        <a href="{{ $suppliers->nextPageUrl() }}&per_page={{ $perPage }}" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @else
                        <button disabled class="px-3 py-2 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
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
