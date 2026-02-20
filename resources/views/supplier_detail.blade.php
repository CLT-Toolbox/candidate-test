<x-app-layout>
<div class="min-h-screen bg-gray-50 font-sans">
    <!-- Page Content -->
    <main class="max-w-5xl mx-auto px-6 py-6">
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

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-1.5 text-sm text-gray-500 mb-5">
            <a href="#" class="hover:text-gray-700 transition-colors">Suppliers</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
            <span class="text-gray-900 font-medium">{{ $supplier->name }}</span>
        </nav>

        <!-- Supplier Header Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-5">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-1.5">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $supplier->name }}</h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $supplier->is_active ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'}}">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $supplier->is_active ? 'Active Partner' : 'Inactive Partner' }}
                        </span>
                    </div>
                    <p class="font-mono text-xs text-gray-500">ID: {{ $supplier->id }}</p>
                </div>
                <button onclick="openEditModal('{{ $supplier->id }}', '{{ $supplier->name }}', '{{ $supplier->email ?? '' }}', '{{ $supplier->address ?? '' }}', '{{ $supplier->is_active ?? '' }}')"
                     class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit Supplier
                </button>
            </div>

            <!-- Meta info -->
            <div class="grid grid-cols-4 divide-x divide-gray-100 mt-5">
                <div class="pr-6">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Primary Contact</p>
                    <div class="flex items-center gap-1.5 text-sm text-gray-700">
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        {{ $supplier->email ?? 'No email provided' }}
                    </div>
                </div>
                <div class="px-6">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Location</p>
                    <div class="flex items-center gap-1.5 text-sm text-gray-700">
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $supplier->address ?? 'No location provided' }}
                    </div>
                </div>
                <div class="px-6">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Material Certifications</p>
                    <div class="flex items-center gap-1.5 text-sm text-gray-700">
                        <svg class="w-3.5 h-3.5 text-green-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        EXAMPLE: FSC Certified
                    </div>
                </div>
                <div class="pl-6">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Last Audit Date</p>
                    <div class="flex items-center gap-1.5 text-sm text-gray-700">
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ $supplier->updated_at->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Associated Layups Table -->
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

            <!-- Table Toolbar -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Associated Layups</h2>
                <div class="flex items-center gap-2">
                    <button class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Import
                    </button>
                    <button class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Export
                    </button>
                    <button class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-sm font-medium bg-green-900 text-white hover:bg-green-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Add Layup
                    </button>
                </div>
            </div>

            <!-- Table -->
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="text-left px-6 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Layup ID</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Thickness</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Ply Count</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Species/Grade</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Revision</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <!-- Row 1 - Active -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3.5"><span class="font-mono text-xs text-gray-500">L-204-A</span></td>
                        <td class="px-4 py-3.5 font-medium text-gray-900">Standard 3-Ply Wall</td>
                        <td class="px-4 py-3.5 text-gray-700">105mm</td>
                        <td class="px-4 py-3.5 text-gray-700">3</td>
                        <td class="px-4 py-3.5 text-gray-700">Spruce / No. 2</td>
                        <td class="px-4 py-3.5 text-gray-600 text-xs">Rev 2 (Oct 10)</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Active
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <button class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                            </button>
                        </td>
                    </tr>
                    <!-- Row 2 - Active -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3.5"><span class="font-mono text-xs text-gray-500">L-204-B</span></td>
                        <td class="px-4 py-3.5 font-medium text-gray-900">Heavy Floor Panel</td>
                        <td class="px-4 py-3.5 text-gray-700">175mm</td>
                        <td class="px-4 py-3.5 text-gray-700">5</td>
                        <td class="px-4 py-3.5 text-gray-700">Spruce / Select</td>
                        <td class="px-4 py-3.5 text-gray-600 text-xs">Rev 1 (Sep 22)</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Active
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <button class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                            </button>
                        </td>
                    </tr>
                    <!-- Row 3 - Draft -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3.5"><span class="font-mono text-xs text-gray-500">L-580-X</span></td>
                        <td class="px-4 py-3.5 font-medium text-gray-900">Custom Span Beam</td>
                        <td class="px-4 py-3.5 text-gray-700">245mm</td>
                        <td class="px-4 py-3.5 text-gray-700">7</td>
                        <td class="px-4 py-3.5 text-gray-700">Pine / No. 1</td>
                        <td class="px-4 py-3.5 text-gray-600 text-xs">Draft v2</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span>Draft
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <button class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                            </button>
                        </td>
                    </tr>
                    <!-- Row 4 - Archived -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3.5"><span class="font-mono text-xs text-gray-500">L-295-C</span></td>
                        <td class="px-4 py-3.5 font-medium text-gray-900">Standard 3-Ply Floor</td>
                        <td class="px-4 py-3.5 text-gray-700">105mm</td>
                        <td class="px-4 py-3.5 text-gray-700">3</td>
                        <td class="px-4 py-3.5 text-gray-700">Spruce / No. 2</td>
                        <td class="px-4 py-3.5 text-gray-600 text-xs">Rev 1 (Jan 15)</td>
                        <td class="px-4 py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Archived
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <button class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination Footer -->
            <div class="flex items-center justify-between px-6 py-3 border-t border-gray-100 bg-gray-50">
                <p id="pagination-label" class="text-xs text-gray-500">Showing 4 of 12 layups</p>
                <div class="flex items-center gap-1">
                    <button id="prevPage" onclick="changePage(-1)" class="p-1.5 rounded-md bg-white border border-gray-300 text-gray-400 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <button id="nextPage" onclick="changePage(1)" class="p-1.5 rounded-md bg-white border border-gray-300 text-gray-500 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>

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

<script>
    let page = 1;
    const perPage = 4;
    const total = 12;
    const totalPages = Math.ceil(total / perPage);

    function changePage(dir) {
        page = Math.max(1, Math.min(totalPages, page + dir));
        const from = (page - 1) * perPage + 1;
        const to = Math.min(page * perPage, total);
        document.getElementById('pagination-label').textContent = `Showing ${from}–${to} of ${total} layups`;
        document.getElementById('prevPage').disabled = page === 1;
        document.getElementById('nextPage').disabled = page === totalPages;
    }
</script>

@push('scripts')
    <script src="{{asset('/js/supplier-detail.js')}}"></script>
@endpush
</x-app-layout>