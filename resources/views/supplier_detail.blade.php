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

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <!-- Table Toolbar -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Associated Layups</h2>
                <div class="flex items-center gap-2">
                    <button onclick="openImportModal()" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Import
                    </button>
                    <a href="{{ route('clt-layups.export', $supplier->id) }}?format=csv" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Export CSV
                    </a>
                    <a href="{{ route('clt-layups.export', $supplier->id) }}?format=json" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Export JSON
                    </a>
                    <button onclick="openLayupModal({{ $supplier->id }})" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-sm font-medium bg-green-900 text-white hover:bg-green-800 transition-colors">
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
                    @foreach ($layups  as $layup)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3.5"><span class="font-mono text-xs text-gray-500">{{$layup->id}}</span></td>
                            <td class="px-4 py-3.5 font-medium text-gray-900">{{ $layup->name }}</td>
                            <td class="px-4 py-3.5 text-gray-700"> {{ $layup->layers ? $layup->layers->sum('thickness') . 'mm' : '0mm' }}</td>
                            <td class="px-4 py-3.5 text-gray-700"> {{ $layup->layers ? $layup->layers->count() : 0 }}</td>
                            <td class="px-4 py-3.5 text-gray-700">{{ $layup->species_grade ?? 'N/A' }}</td>
                            <td class="px-4 py-3.5 text-gray-600 text-xs">{{$layup->updated_at->format('M d, Y')}}</td>
                            <td class="px-4 py-3.5">
                                @if ($layup->status == 0)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>Draft
                                    </span>
                                @elseif ($layup->status == 1)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Active
                                    </span>
                                @elseif ($layup->status == 2)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>Archived
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 relative">
                                <button onclick="openActionsMenu(event, this)" class="text-gray-400 hover:text-gray-600 transition-colors"
                                    data-id="{{ $layup->id }}"
                                    data-name="{{ $layup->name }}"
                                    data-species="{{ $layup->species_grade ?? '' }}"
                                    data-status="{{ $layup->status }}"
                                    data-supplier-id="{{ $supplier->id }}">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="p-6 flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500">
                    Showing
                    <span class="font-semibold text-gray-700">{{ $layups->firstItem() ?? 0 }}</span>
                    to
                    <span class="font-semibold text-gray-700">{{ $layups->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-semibold text-gray-700">{{ $layups->total() }}</span>
                    results
                </p>
                <div class="flex items-center gap-1">
                    @if ($layups->onFirstPage())
                        <button disabled class="p-1.5 rounded-md bg-white border border-gray-300 text-gray-400 cursor-not-allowed opacity-40">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                        </button>
                    @else
                        <a href="{{ $layups->previousPageUrl() }}" class="p-1.5 rounded-md bg-white border border-gray-300 text-gray-500 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                        </a>
                    @endif

                    @if ($layups->hasMorePages())
                        <a href="{{ $layups->nextPageUrl() }}" class="p-1.5 rounded-md bg-white border border-gray-300 text-gray-500 hover:bg-gray-50 transition-colors">
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

<!-- CLT Layup Modal Backdrop -->
<div id="layupModalBackdrop" class="fixed inset-0 bg-black bg-opacity-40 z-40 transition-opacity hidden" onclick="closeLayupModal()"></div>

<!-- CLT Layup Modal Container -->
<div id="layupModalContainer" class="fixed inset-0 flex items-center justify-center z-50 p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full border border-gray-200" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Add CLT Layup</h3>
            <button onclick="closeLayupModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-6">
            <form id="cltLayupForm" method="POST" action="{{ route('clt-layups.store') }}">
                @csrf
                <input type="hidden" id="cltSupplierId" name="supplier_id" value="">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-1.5">Layup Name</label>
                    <input type="text" id="layupName" name="name" placeholder="e.g., Standard 3-Ply Wall"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-green-700 transition">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-1.5">Species/Grade</label>
                    <input type="text" id="layupSpeciesGrade" name="species_grade" placeholder="Spruce / No. 2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-green-700 transition">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-medium mb-1.5">Status</label>
                    <select id="layupStatus" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-700">
                        <option value="0">Draft</option>
                        <option value="1" selected>Active</option>
                        <option value="2">Archived</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeLayupModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-green-900 hover:bg-green-800 text-white rounded-lg text-sm font-medium transition-colors">
                        Add Layup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Actions menu (edit / view / delete) -->
<div id="actionsMenu" class="hidden absolute z-50 bg-white border border-gray-200 rounded shadow py-1 w-40 text-sm" onclick="event.stopPropagation()">
    <button id="actionsView" class="w-full text-left px-3 py-2 hover:bg-gray-50">View</button>
    <button id="actionsEdit" class="w-full text-left px-3 py-2 hover:bg-gray-50">Edit</button>
    <button id="actionsDelete" class="w-full text-left px-3 py-2 text-red-600 hover:bg-red-50">Delete</button>
</div>

<!-- Import Layup Modal Backdrop -->
<div id="importModalBackdrop" class="fixed inset-0 bg-black bg-opacity-40 z-40 transition-opacity hidden" onclick="closeImportModal()"></div>

<!-- Import Layup Modal Container -->
<div id="importModalContainer" class="fixed inset-0 flex items-center justify-center z-50 p-4 hidden">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full border border-gray-200" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Import Layup Data</h3>
            <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <form id="importForm" method="POST" action="{{ route('clt-layups.import') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="importSupplierId" name="supplier_id" value="{{ $supplier->id }}">

                <!-- File Upload Area -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-medium mb-3">Import File</label>
                    <div id="fileDropZone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-gray-400 transition-colors bg-gray-50">
                        <input type="file" id="importFile" name="file" accept=".csv,.json" class="hidden">
                        <svg class="w-8 h-8 mx-auto mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <p class="text-gray-700 text-sm font-medium mb-0.5">Click to upload</p>
                        <p class="text-gray-500 text-xs"> or drag and drop</p>
                        <p class="text-gray-400 text-xs mt-1">CSV or JSON up to 10MB</p>
                    </div>
                    <p id="fileName" class="text-sm text-gray-600 mt-2 hidden">Selected: <span id="fileNameText"></span></p>
                </div>

                <!-- Conflict Resolution Strategy -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Conflict Resolution Strategy</label>
                    <select id="conflictStrategy" name="conflict_strategy" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-700">
                        <option value="skip">Skip conflicts (Default)</option>
                        <option value="review">Review conflicts</option>
                        <option value="update">Update existing records</option>
                        <option value="duplicate">Duplicate records</option>
                        <option value="reject">Reject Entire Import</option>
                    </select>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-3">
                    <button type="button" onclick="closeImportModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="confirmImportBtn"
                        class="flex-1 px-4 py-2 bg-green-900 hover:bg-green-800 text-white rounded-lg text-sm font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                        Confirm Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Conflict Resolution Modal Backdrop -->
<div id="conflictModalBackdrop" class="fixed inset-0 bg-black bg-opacity-40 transition-opacity hidden" style="z-index:1100" onclick="closeConflictModal()"></div>

<!-- Conflict Resolution Modal Container -->
<div id="conflictModalContainer" class="fixed inset-0 flex items-center justify-center p-4 hidden" style="z-index:1110">
    <div class="bg-white rounded-xl shadow-xl max-w-7xl w-full border border-gray-200 flex flex-col h-[92vh] max-h-[92vh]" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <h3 class="text-base font-semibold text-gray-900">Conflict Resolution: Import <span id="conflictFileName" class="text-gray-600 font-normal"></span></h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Needs Review</span>
            </div>
            <div class="flex items-center gap-3">
                <button id="conflictRejectAll" class="px-3 py-1.5 text-sm text-red-600 border border-red-200 rounded hover:bg-red-50 transition-colors">Reject Import</button>
                <button onclick="closeConflictModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="flex flex-1 overflow-hidden">
            <!-- Left Sidebar -->
            <div class="w-60 border-r border-gray-100 bg-white overflow-y-auto flex-shrink-0">
                <div class="p-4">
                    <!-- Conflicting Layups Section -->
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-3">
                            <h4 class="text-sm font-semibold text-gray-900">Conflicting Layups <span id="conflictCount" class="text-gray-500 font-normal"></span></h4>
                        </div>
                        <ul id="conflictList" class="space-y-1"></ul>
                    </div>

                    <!-- Resolved Section -->
                    <div id="resolvedSection" class="hidden">
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 mt-6 pt-6 border-t border-gray-100">RESOLVED</div>
                        <ul id="resolvedList" class="space-y-1"></ul>
                    </div>
                </div>
            </div>

            <!-- Right Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <div class="flex-1 overflow-y-auto p-6">
                    <!-- Comparison Header -->
                    <div id="comparisonHeader" class="mb-6">
                        <div class="flex items-center gap-3 mb-2">
                            <h2 id="layupTitle" class="text-lg font-semibold text-gray-900"></h2>
                            <span id="layerCountBadge" class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800"></span>
                        </div>
                        <p class="text-sm text-gray-600">Differences highlighted in <span class="inline-block w-3 h-3 bg-red-100 border border-red-300 rounded align-text-bottom ml-1 mr-1"></span><span class="text-red-600 font-medium">Red</span></p>
                    </div>

                    <!-- Version Info & Comparison -->
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <!-- Existing Version -->
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-semibold text-gray-900">Existing Version</span>
                            </div>
                            <div id="existingMetadata" class="text-xs text-gray-500 mb-3"></div>
                            <div id="existingPanel" class="border border-gray-200 rounded bg-white overflow-x-auto max-h-[35vh] overflow-y-auto"></div>
                        </div>

                        <!-- Importing Version -->
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-6"/></svg>
                                <span class="text-sm font-semibold text-gray-900">Importing Version</span>
                            </div>
                            <div id="incomingMetadata" class="text-xs text-gray-500 mb-3"></div>
                            <div id="incomingPanel" class="border border-gray-200 rounded bg-white overflow-x-auto max-h-[35vh] overflow-y-auto"></div>
                        </div>
                    </div>
                </div>

                <!-- Footer with Actions (tidied buttons layout) -->
                <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50 flex-shrink-0">
                    <!-- Left actions -->
                    <div class="flex items-center gap-3">
                        <button id="keepExistingBtn" class="flex items-center gap-2 px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                            Keep Existing
                        </button>

                        <button id="acceptIncomingBtn" class="flex items-center gap-2 px-4 py-2 bg-green-900 text-white rounded-md hover:bg-green-800 transition-colors text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Accept New
                        </button>
                    </div>

                    <!-- Center pager / navigation -->
                    <div class="flex items-center gap-4">
                        <button id="prevBtn" class="flex items-center gap-2 px-3 py-1.5 text-gray-700 border border-gray-300 rounded hover:bg-gray-100 transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>

                        <span id="conflictPager" class="text-sm text-gray-600 font-medium">0 of 0</span>

                        <button id="nextBtn" class="flex items-center gap-2 px-3 py-1.5 text-gray-700 border border-gray-300 rounded hover:bg-gray-100 transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    <!-- Right apply -->
                    <div class="flex-shrink-0">
                        <button id="applyDecisionsBtn" class="px-5 py-2 bg-green-900 text-white rounded-md hover:bg-green-800 transition-colors text-sm font-medium">Apply Resolutions</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden delete form -->
<form id="cltDeleteForm" method="POST" style="display:none">
    @csrf
    <input type="hidden" name="_method" value="DELETE">
</form>

@push('scripts')
    <script src="{{asset('/js/supplier-detail.js')}}"></script>
@endpush
</x-app-layout>