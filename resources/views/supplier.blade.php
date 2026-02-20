<x-app-layout>
    <div class="min-h-screen bg-gray-50" id="supplierContainer">
        <div class="max-w-7xl mx-auto px-6 py-8">

            @if(session('success'))
                <div id="successAlert" class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div id="errorAlert" class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Main Card -->
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <div class="p-6">

                    <!-- Header -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Suppliers</h1>
                            <p class="text-gray-500 mt-1 text-sm">Manage your suppliers and material sourcing.</p>
                        </div>
                        <button onclick="openAddModal()" class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium bg-green-900 text-white hover:bg-green-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Add Supplier
                        </button>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex items-center gap-2 mb-6">
                        <div class="flex-1">
                            <input type="text" placeholder="Search suppliers by name..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-green-700 transition">
                        </div>
                        <button class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                            Filter
                        </button>
                        <button class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Export
                        </button>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50">
                                    <th class="px-6 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Total Layouts</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Created At</th>
                                    <th class="px-4 py-3 text-left text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($suppliers as $supp)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-3.5 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="h-9 w-9 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                                                    <span class="text-green-800 font-semibold text-xs">
                                                        {{ collect(explode(' ', $supp->name))
                                                            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                                            ->take(2)
                                                            ->implode('') }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="text-gray-900 font-medium">{{ $supp->name }}</p>
                                                    <p class="text-gray-500 text-xs font-mono">ID: {{ $supp->id }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-gray-700">{{ 20 }}</td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-gray-600">{{ $supp->created_at->format('M j, Y') }}</td>
                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('suppliers.show', $supp->id) }}"
                                                    class="px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                                                    View
                                                </a>
                                                <button
                                                    onclick="openEditModal('{{ $supp->id }}', '{{ $supp->name }}', '{{ $supp->email ?? '' }}', '{{ $supp->address ?? '' }}', '{{ $supp->is_active ?? '' }}')"
                                                    class="px-3 py-1.5 text-xs font-semibold text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors">
                                                    Edit
                                                </button>
                                                <button
                                                    onclick="confirmDelete('{{ $supp->id }}', '{{ $supp->name }}')"
                                                    class="px-3 py-1.5 text-xs font-semibold text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500">
                            Showing
                            <span class="font-semibold text-gray-700">{{ $suppliers->firstItem() ?? 0 }}</span>
                            to
                            <span class="font-semibold text-gray-700">{{ $suppliers->lastItem() ?? 0 }}</span>
                            of
                            <span class="font-semibold text-gray-700">{{ $suppliers->total() }}</span>
                            results
                        </p>
                        <div class="flex items-center gap-1">
                            @if ($suppliers->onFirstPage())
                                <button disabled class="p-1.5 rounded-md bg-white border border-gray-300 text-gray-400 cursor-not-allowed opacity-40">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                                </button>
                            @else
                                <a href="{{ $suppliers->previousPageUrl() }}" class="p-1.5 rounded-md bg-white border border-gray-300 text-gray-500 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                                </a>
                            @endif

                            @if ($suppliers->hasMorePages())
                                <a href="{{ $suppliers->nextPageUrl() }}" class="p-1.5 rounded-md bg-white border border-gray-300 text-gray-500 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                                </a>
                            @else
                                <button disabled class="p-1.5 rounded-md bg-white border border-gray-300 text-gray-400 cursor-not-allowed opacity-40">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <form id="deleteForm" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>

        <!-- Modal Backdrop -->
        <div id="modalBackdrop" class="fixed inset-0 bg-black bg-opacity-40 z-40 transition-opacity hidden" onclick="closeModal()"></div>

        <!-- Modal Container -->
        <div id="modalContainer" class="fixed inset-0 flex items-center justify-center z-50 p-4 hidden">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full border border-gray-200" onclick="event.stopPropagation()">

                <!-- Modal Header -->
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
                    <h3 id="modalTitle" class="text-base font-semibold text-gray-900">Add New Supplier</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6">
                    <form id="supplierForm" method="POST">
                        @csrf
                        <input type="hidden" id="methodInput" name="_method" value="POST">

                        <!-- Supplier Name -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-1.5">Supplier Name</label>
                            <input type="text" id="supplierName" name="name" placeholder="e.g., Nordic Timber Co."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-green-700 transition">
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-1.5">Email Address</label>
                            <input type="email" id="supplierEmail" name="email" placeholder="supplier@example.com"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-green-700 transition">
                        </div>

                        <!-- Address -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-medium mb-1.5">Address</label>
                            <input type="text" id="supplierAddress" name="address" placeholder="123 Main Street, City, Country"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-green-700 transition">
                        </div>

                        <!-- Is Active -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-medium mb-3">
                                Status
                            </label>

                            <div class="flex gap-6">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="is_active" value="1" checked
                                        class="text-green-700 focus:ring-green-700">
                                    <span class="text-sm">Active</span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="is_active" value="0"
                                        class="text-red-600 focus:ring-red-600">
                                    <span class="text-sm">Inactive</span>
                                </label>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex gap-3">
                            <button type="button" onclick="closeModal()"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" id="submitBtn"
                                class="flex-1 px-4 py-2 bg-green-900 hover:bg-green-800 text-white rounded-lg text-sm font-medium transition-colors">
                                Add Supplier
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{asset('/js/supplier.js')}}"></script>
    @endpush
</x-app-layout>