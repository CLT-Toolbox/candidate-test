<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h2 class="text-xl font-bold text-gray-900 tracking-tight">
            Associated Layups
        </h2>
        
        <div class="flex flex-col md:flex-row items-center gap-3">
            <form action="{{ url()->current() }}" method="GET" class="w-full md:w-64">
                <x-ui.search-input 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search layups..." 
                />
            </form>

            <div class="flex items-center gap-2">
                <x-ui.button variant="secondary" icon="file-up" class="rounded-lg font-bold"
                    @click="$dispatch('open-modal', 'import-layups')">
                    Import
                </x-ui.button>
                <a href="{{ route('suppliers.export', $supplier->id) }}" class="inline-flex">
                    <x-ui.button variant="secondary" icon="download" class="rounded-lg font-bold">
                        Export
                    </x-ui.button>
                </a>
                <x-ui.button icon="plus" class="rounded-lg font-bold bg-brand-800"
                    @click="$dispatch('open-modal', 'create-layup'); $dispatch('create-layup', { id: {{ $supplier->id }}, name: '{{ addslashes($supplier->name) }}' })">
                    Add Layup
                </x-ui.button>
            </div>
        </div>
    </div>

    <x-ui.table>
        <x-slot name="thead">
            <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] text-left">Layup ID</th>
            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] text-left">Name</th>
            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] text-left">Thickness</th>
            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] text-left">Ply Count</th>
            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] text-left">Species/Grade</th>
            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] text-left">Revision</th>
            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] text-left">Status</th>
            <th class="px-8 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-[0.15em] text-right">Actions</th>
        </x-slot>

        {{-- Real Data from Database --}}
        @forelse($layups as $layup)
            <tr class="group hover:bg-gray-50/50 transition-all duration-200">
                <td class="px-8 py-4">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">L-{{ str_pad($layup->id, 3, '0', STR_PAD_LEFT) }}-A</span>
                </td>
                <td class="px-6 py-4">
                    <span class="text-xs font-bold text-gray-900 tracking-tight">{{ $layup->name }}</span>
                </td>
                <td class="px-6 py-4 text-xs font-semibold text-gray-500">{{ number_format($layup->layers_sum_thickness ?? 0, 0) }}mm</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center justify-center px-2 py-0.5 bg-gray-100 text-gray-700 text-[10px] font-bold rounded-md ring-1 ring-gray-200">
                        {{ $layup->layers_count ?? 0 }}
                    </span>
                </td>
                <td class="px-6 py-4 text-xs font-semibold text-gray-500">Spruce / No. 2</td>
                <td class="px-6 py-4 text-xs font-semibold text-gray-400">Rev 1 ({{ $layup->created_at->format('M d') }})</td>
                <td class="px-6 py-4">
                    <x-ui.badge variant="success" size="xs" class="px-2 py-0.5 font-bold uppercase tracking-wider text-[9px]">
                        Active
                    </x-ui.badge>
                </td>
                <td class="px-8 py-4 text-right">
                    <div class="flex items-center justify-end gap-1 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('suppliers.layups.show', [$supplier->id, $layup->id]) }}">
                            <x-ui.button variant="ghost" icon="eye" size="icon"
                                class="text-blue-400 hover:text-blue-600 hover:bg-blue-50 cursor-pointer" />
                        </a>
                        <form action="{{ route('suppliers.layups.duplicate', [$supplier->id, $layup->id]) }}" method="POST" class="inline">
                            @csrf
                            <x-ui.button variant="ghost" icon="copy" size="icon"
                                class="text-green-500 hover:text-green-700 hover:bg-green-50 cursor-pointer" />
                        </form>
                        <x-ui.button variant="ghost" icon="edit-3" size="icon"
                            class="text-yellow-400 hover:text-yellow-600 hover:bg-yellow-50 cursor-pointer" 
                            @click="$dispatch('open-modal', 'edit-layup'); $dispatch('edit-layup', { id: {{ $layup->id }}, name: '{{ addslashes($layup->name) }}', supplier_id: {{ $supplier->id ?? $layup->supplier_id }}, supplier_name: '{{ addslashes($supplier->name ?? ($layup->supplier->name ?? '')) }}' })" />
                        <x-ui.button variant="ghost" icon="trash-2" size="icon"
                            class="text-red-400 hover:text-red-600 hover:bg-red-50 cursor-pointer" 
                            @click="$dispatch('open-modal', 'delete-layup'); $dispatch('delete-layup', { id: {{ $layup->id }}, name: '{{ addslashes($layup->name) }}' })" />
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-8 py-10 text-center">
                    <div class="flex flex-col items-center gap-2 text-gray-400">
                        <i data-lucide="info" class="w-8 h-8 opacity-20"></i>
                        <span class="text-xs font-medium">No layups found for this criteria.</span>
                    </div>
                </td>
            </tr>
        @endforelse

        @if($layups->hasPages())
            <x-slot name="footer">
                <x-ui.pagination :paginator="$layups" resourceName="layups" />
            </x-slot>
        @endif
    </x-ui.table>
</div>
