<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if ($message = Session::get('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex justify-between items-center">
                <span>{{ $message }}</span>
                <button onclick="this.parentElement.style.display='none'" class="text-green-600 hover:text-green-900 font-bold">×</button>
            </div>
            @endif

            <!-- Header Section -->
            <div class="mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Suppliers</h1>
                        <p class="text-gray-600 text-sm mt-1">Manage timber suppliers and material assessing</p>
                    </div>
                    <button onclick="openCreateModal()" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        {{ __('Add Supplier') }}
                    </button>
                </div>

                <!-- Search and Filter Bar -->
                <div class="flex w-full gap-3 items-center justify-between">
                    <div class="flex items-center bg-white border border-gray-300 rounded-md px-4 py-2 w-80 shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35" />
                        </svg>

                        <input type="text" id="searchInput" name="search" placeholder="Search suppliers by name..." class="flex-1 outline-none text-sm text-gray-700 bg-transparent placeholder-gray-400" />
                    </div>

                    <div class="flex gap-2 ml-auto">
                        <button onclick="toggleFilterPanel()" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 whitespace-nowrap bg-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            {{ __('Filter') }}
                        </button>
                        <button onclick="exportSuppliers()" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2 whitespace-nowrap bg-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                </path>
                            </svg>
                            {{ __('Export') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Suppliers Table -->
            <div class="relative overflow-x-auto bg-white shadow-md rounded-lg border border-gray-200">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="text-sm text-gray-700 bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Total Layups
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Created At
                            </th>
                            <th scope="col" class="px-6 py-3 font-medium">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr class="bg-white border-b border-gray-200 hover:bg-gray-50" data-supplier-id="{{ $supplier->supplier_id }}" data-supplier-name="{{ $supplier->name }}">
                            <td class="px-6 py-4 align-middle">
                                <x-supplier-identity :supplier="$supplier" />
                            </td>
                            <td class="px-6 py-4 align-middle whitespace-nowrap text-gray-700 tabular-nums">
                                {{ $supplier->layups_count }}
                            </td>
                            <td class="px-6 py-4 align-middle whitespace-nowrap text-sm text-gray-600">
                                {{ $supplier->created_at?->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4 align-middle">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('suppliers.show', $supplier->supplier_id) }}" title="View"
                                        class="inline-flex items-center justify-center text-gray-600 hover:text-gray-900">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <button type="button" onclick='openEditModal(@json($supplier))' title="Edit"
                                        class="inline-flex items-center justify-center text-blue-600 hover:text-blue-900 p-0 border-0 bg-transparent cursor-pointer">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('suppliers.destroy', $supplier->supplier_id) }}"
                                        class="m-0 inline-flex items-center">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure?')" title="Delete"
                                            class="inline-flex items-center justify-center p-0 border-0 bg-transparent text-red-600 hover:text-red-900 cursor-pointer">
                                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="bg-white border-b border-gray-200">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                {{ __('No suppliers found') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('suppliers.modal-create-supplier')
    @include('suppliers.modal-edit-supplier')

    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
            document.getElementById('createForm').reset();
            document.getElementById('createNameError').textContent = '';
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }

        function openEditModal(supplier) {
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editSupplierId').value = supplier.supplier_id;
            document.getElementById('editName').value = supplier.name;
            document.getElementById('editEmail').value = supplier.email || '';
            document.getElementById('editLocation').value = supplier.location || '';
            document.getElementById('editCertification').value = supplier.certification || '';
            document.getElementById('editAuditDate').value = (supplier.audit_date || '').toString().slice(0, 10);
            document.getElementById('editStatus').value = supplier.status || 'ACTIVE';
            document.getElementById('editForm').action = `/suppliers/${supplier.supplier_id}`;
            document.getElementById('editNameError').textContent = '';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            let createModal = document.getElementById('createModal');
            let editModal = document.getElementById('editModal');
            if (event.target == createModal) {
                createModal.classList.add('hidden');
            }
            if (event.target == editModal) {
                editModal.classList.add('hidden');
            }
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');

            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Toggle filter panel
        function toggleFilterPanel() {
            alert('Filter panel coming soon!');
        }

        // Export functionality
        function exportSuppliers() {
            const suppliers = [];
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                if (row.style.display === 'none') return;
                const id = row.getAttribute('data-supplier-id');
                const name = row.getAttribute('data-supplier-name');
                if (id && name) {
                    suppliers.push({
                        id
                        , name
                    });
                }
            });

            // Create CSV content
            let csv = 'Supplier ID,Name\n';
            suppliers.forEach(supplier => {
                csv += `${supplier.id},${supplier.name}\n`;
            });

            // Download CSV file
            const blob = new Blob([csv], {
                type: 'text/csv'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'suppliers_' + new Date().toISOString().slice(0, 10) + '.csv';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }

    </script>
</x-app-layout>
