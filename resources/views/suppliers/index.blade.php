<x-app-layout>
    <div class="py-12 bg-surface-50/50 min-h-screen" x-data>
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            <x-ui.page-header title="Suppliers" subtitle="Manage timber suppliers and material sourcing.">
                <x-slot name="actions">
                    <x-ui.button icon="plus" x-on:click.prevent="$dispatch('open-modal', 'create-supplier')">
                        Add Supplier
                    </x-ui.button>
                </x-slot>
            </x-ui.page-header>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div class="w-full md:w-[320px]">
                    <form action="{{ route('suppliers.index') }}" method="GET">
                        <x-ui.search-input name="search" value="{{ request('search') }}"
                            placeholder="Search suppliers by name..." />
                    </form>
                </div>

                <div class="flex items-center gap-3">
                    <x-ui.button variant="secondary" icon="list-filter">Filter</x-ui.button>
                    <x-ui.button variant="secondary" icon="download">Export</x-ui.button>
                </div>
            </div>

            <x-ui.table>
                <x-slot name="thead">
                    <th class="px-8 py-4 text-xxs font-bold text-gray-800 uppercase text-left">Supplier</th>
                    <th class="px-6 py-4 text-xxs font-bold text-gray-800 uppercase text-left">Total Layups</th>
                    <th class="px-6 py-4 text-xxs font-bold text-gray-800 uppercase text-left">Created At</th>
                    <th class="px-8 py-4 text-xxs font-bold text-gray-800 uppercase text-right">Actions</th>
                </x-slot>

                @forelse($suppliers as $supplier)
                    <tr
                        class="group hover:bg-brand-100/30 transition-all duration-200 last:border-0">
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-4">
                                <x-ui.avatar :name="$supplier->name" size="md" class="ring-2 ring-white shadow-sm" />
                                <div class="flex flex-col">
                                    <span
                                        class="text-xs font-semibold text-surface-950 tracking-tight leading-none mb-1">
                                        {{ $supplier->name }}
                                    </span>
                                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-widest">
                                        SUP-{{ str_pad($supplier->id, 3, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-medium text-surface-600">
                                    {{ $supplier->layups_count }} Items
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-medium text-surface-600">
                                    {{ $supplier->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-4 text-right">
                            <div
                                class="flex items-center justify-end gap-1 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('suppliers.show', $supplier) }}">
                                    <x-ui.button variant="ghost" icon="eye" size="icon"
                                        class="text-blue-400 hover:text-blue-600 hover:bg-blue-50 cursor-pointer" />
                                </a>
                                <x-ui.button variant="ghost" icon="edit-3" size="icon"
                                    class="text-yellow-400 hover:text-yellow-600 hover:bg-yellow-50 cursor-pointer"
                                    x-on:click="$dispatch('open-modal', 'edit-supplier'); $dispatch('edit-supplier', { id: {{ $supplier->id }}, name: '{{ addslashes($supplier->name) }}' })" />
                                <x-ui.button variant="ghost" icon="trash-2" size="icon"
                                    class="text-red-400 hover:text-red-600 hover:bg-red-50 cursor-pointer"
                                    x-on:click="$dispatch('open-modal', 'delete-supplier'); $dispatch('delete-supplier', { id: {{ $supplier->id }}, name: '{{ addslashes($supplier->name) }}' })" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 bg-surface-50 rounded-full flex items-center justify-center mb-4">
                                    <i data-lucide="package-search" class="w-6 h-6 text-surface-300"></i>
                                </div>
                                <p class="text-surface-400 text-xxs font-bold uppercase tracking-[0.2em]">No suppliers
                                    found in the network</p>
                            </div>
                        </td>
                    </tr>
                @endforelse

                @if ($suppliers->hasPages())
                    <x-slot name="footer">
                        <x-ui.pagination :paginator="$suppliers" resourceName="Suppliers" />
                    </x-slot>
                @endif
            </x-ui.table>

        </div>
    </div>

    @include('suppliers.partials.create_modal')
    @include('suppliers.partials.edit_modal')
    @include('suppliers.partials.delete_modal')
</x-app-layout>
