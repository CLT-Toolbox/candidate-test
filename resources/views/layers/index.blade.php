<x-app-layout>
    @php($nested = isset($supplier) && isset($layup))

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($message = Session::get('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex justify-between items-center">
                <span>{{ $message }}</span>
                <button onclick="this.parentElement.style.display='none'" class="text-green-600 hover:text-green-900 font-bold">×</button>
            </div>
            @endif

            <div class="mb-6">
                @if ($nested)
                <nav class="text-sm text-gray-500 mb-3">
                    <a href="{{ route('suppliers.index') }}" class="text-blue-600 hover:text-blue-800">{{ __('Suppliers') }}</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('suppliers.show', $supplier) }}" class="text-blue-600 hover:text-blue-800">{{ $supplier->name }}</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('suppliers.layups.index', $supplier) }}" class="text-blue-600 hover:text-blue-800">{{ __('Layups') }}</a>
                    <span class="mx-1">/</span>
                    <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}" class="text-blue-600 hover:text-blue-800">{{ $layup->name }}</a>
                    <span class="mx-1">/</span>
                    <span class="text-gray-700">{{ __('Layers') }}</span>
                </nav>
                @endif

                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            @if ($nested)
                            {{ $layup->name }} — {{ __('Layers') }}
                            @else
                            {{ __('Layers') }}
                            @endif
                        </h1>
                        <p class="text-gray-600 text-sm mt-1">
                            @if ($nested)
                            {{ __('Stack sequence for this layup.') }}
                            @else
                            {{ __('Browse all layers across layups and suppliers.') }}
                            @endif
                        </p>
                    </div>
                    @if ($nested)
                    <a href="{{ route('suppliers.layups.layers.create', [$supplier, $layup]) }}" class="inline-flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ __('Add Layer') }}
                    </a>
                    @endif
                </div>

                <div class="flex w-full flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
                    <div class="flex items-center bg-white border border-gray-300 rounded-md px-4 py-2 w-80 shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35" />
                        </svg>

                        <input type="text" id="layersSearchInput" name="search" placeholder="Search layers by name..." class="flex-1 outline-none text-sm text-gray-700 bg-transparent placeholder-gray-400" />
                    </div>
                    <div class="flex gap-2 sm:ml-auto">
                        <button type="button" onclick="toggleLayersFilter()" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 flex items-center justify-center gap-2 whitespace-nowrap bg-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            {{ __('Filter') }}
                        </button>
                        <button type="button" onclick="exportLayersTable()" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 flex items-center justify-center gap-2 whitespace-nowrap bg-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                </path>
                            </svg>
                            {{ __('Export') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="relative overflow-x-auto bg-white shadow-md rounded-lg border border-gray-200">
                <table class="w-full text-sm text-left text-gray-700 layers-data-table">
                    <thead class="text-sm text-gray-700 bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-medium whitespace-nowrap">{{ __('Order') }}</th>
                            @unless ($nested)
                            <th scope="col" class="px-6 py-3 font-medium whitespace-nowrap">{{ __('Layup ID') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Layup') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium">{{ __('Supplier') }}</th>
                            @endunless
                            <th scope="col" class="px-6 py-3 font-medium whitespace-nowrap">{{ __('Thickness') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium whitespace-nowrap">{{ __('Width') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium whitespace-nowrap">{{ __('Angle') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium whitespace-nowrap">{{ __('Grade') }}</th>
                            <th scope="col" class="px-6 py-3 font-medium whitespace-nowrap">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($layers as $layer)
                        @php($rowLayup = $nested ? $layup : $layer->layup)
                        @php($rowSupplier = $nested ? $supplier : ($layer->layup?->supplier))
                        <tr class="bg-white border-b border-gray-200 hover:bg-gray-50"
                            @unless ($nested)
                                @if ($rowSupplier)
                                    data-csv-supplier="{{ $rowSupplier->name }} ({{ $rowSupplier->supplier_id }})"
                                @endif
                            @endunless>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">{{ $layer->layer_order }}</td>
                            @unless ($nested)
                            <td class="px-6 py-4 whitespace-nowrap">{{ $rowLayup?->layup_id ?? '—' }}</td>
                            <td class="px-6 py-4">
                                @if ($rowLayup && $rowSupplier)
                                <a href="{{ route('suppliers.layups.show', [$rowSupplier, $rowLayup]) }}" class="text-blue-600 hover:text-blue-800">{{ $rowLayup->name }}</a>
                                @else
                                —
                                @endif
                            </td>
                            <td class="px-6 py-4 align-middle">
                                @if ($rowSupplier)
                                    <x-supplier-identity :supplier="$rowSupplier" />
                                @else
                                    —
                                @endif
                            </td>
                            @endunless
                            <td class="px-6 py-4 whitespace-nowrap">{{ $layer->thickness }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $layer->width }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $layer->angle }}°</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $layer->grade ?? '—' }}</td>
                            <td class="px-6 py-4">
                                @if ($rowLayup && $rowSupplier)
                                <div class="flex gap-3 items-center">
                                    <form method="POST" action="{{ route('suppliers.layups.layers.destroy', [$rowSupplier, $rowLayup, $layer]) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('{{ __('Are you sure?') }}')" title="{{ __('Delete') }}" class="text-red-600 hover:text-red-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                @else
                                <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr class="bg-white border-b border-gray-200">
                            <td colspan="{{ $nested ? 6 : 9 }}" class="px-6 py-4 text-center text-gray-500">
                                {{ __('No layers found.') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('layersSearchInput')?.addEventListener('keyup', function(e) {
            const q = e.target.value.toLowerCase();
            document.querySelectorAll('.layers-data-table tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });

        function toggleLayersFilter() {
            alert(@json(__('Filter panel coming soon!')));
        }

        function exportLayersTable() {
            const nested = @json($nested);
            const header = nested ? ['Order', 'Thickness', 'Width', 'Angle', 'Grade'] : ['Order', 'Layup ID', 'Layup', 'Supplier', 'Thickness', 'Width', 'Angle', 'Grade'];
            const lines = [header.join(',')];
            document.querySelectorAll('.layers-data-table tbody tr').forEach(row => {
                if (row.style.display === 'none') return;
                const cells = row.querySelectorAll('td');
                if (!cells.length) return;
                if (nested && cells.length >= 6) {
                    lines.push([
                        cells[0].textContent.trim()
                        , cells[1].textContent.trim()
                        , cells[2].textContent.trim()
                        , cells[3].textContent.trim()
                        , cells[4].textContent.trim()
                    , ].join(','));
                }
                if (!nested && cells.length >= 9) {
                    const supplierCsv = row.getAttribute('data-csv-supplier')
                        || cells[3].textContent.replace(/\s+/g, ' ').trim().replaceAll(',', ';');
                    lines.push([
                        cells[0].textContent.trim(),
                        cells[1].textContent.trim(),
                        cells[2].textContent.trim().replaceAll(',', ';'),
                        supplierCsv,
                        cells[4].textContent.trim(),
                        cells[5].textContent.trim(),
                        cells[6].textContent.trim(),
                        cells[7].textContent.trim(),
                    ].join(','));
                }
            });
            const blob = new Blob([lines.join('\n')], {
                type: 'text/csv'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'layers_' + new Date().toISOString().slice(0, 10) + '.csv';
            document.body.appendChild(a);
            a.click();
            URL.revokeObjectURL(url);
            a.remove();
        }

    </script>
</x-app-layout>
