<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-ascender text-xl font-semibold leading-tight text-[#262b2f] dark:text-gray-200">
                    {{ __('Suppliers') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Manage timber suppliers and material sourcing.') }}</p>
            </div>
            <!-- TOMBOL ADD -->
            <button x-data @click="window.dispatchEvent(new CustomEvent('open-create-modal'))"
                class="flex items-center gap-2 rounded-lg bg-[#3f7a5c] px-4 py-2 font-medium text-white transition hover:bg-[#2d5b45]">
                <span>+</span> {{ __('Add Supplier') }}
            </button>
        </div>
    </x-slot>

    <div class="py-1" x-data="supplierManager()" @open-create-modal.window="openCreateModal()"
        @open-edit-modal.window="openEditModal($event.detail)"
        @open-delete-modal.window="openDeleteModal($event.detail)">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('success'))
                <div
                    class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            <div class="border-b border-gray-100 pb-4 dark:border-gray-700">
                <div class="flex justify-between gap-2">
                    <div class="max-w-xs flex-1">
                        <form method="GET" action="{{ route('suppliers.index') }}" id="searchForm">
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search"
                                    placeholder="{{ __('Search suppliers by name...') }}"
                                    value="{{ request('search') }}"
                                    class="w-full rounded-md border border-gray-300 bg-white py-1.5 pl-10 pr-3 text-sm text-gray-900 placeholder-gray-400 transition focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
                            </div>
                        </form>
                    </div>
                    <div class="flex gap-2">
                        <button @click="$dispatch('open-modal', 'filter-modal')"
                            class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                            {{ __('Filter') }}
                        </button>
                        <x-modal name="filter-modal" maxWidth="md">
                            <div class="p-6">
                                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ __('Filter Suppliers') }}
                                </h2>

                                <form method="GET" action="{{ route('suppliers.index') }}">
                                    <!-- Preserve search parameter if exists -->
                                    @if (request('search'))
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                    @endif

                                    <div class="space-y-4">
                                        <!-- Sort By -->
                                        <div>
                                            <label
                                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('Sort By') }}
                                            </label>
                                            <select name="sort_by"
                                                class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                <option value="created_at"
                                                    {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>
                                                    {{ __('Date Created') }}
                                                </option>
                                                <option value="name"
                                                    {{ request('sort_by') == 'name' ? 'selected' : '' }}>
                                                    {{ __('Name') }}
                                                </option>
                                                <option value="updated_at"
                                                    {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>
                                                    {{ __('Last Updated') }}
                                                </option>
                                                <option value="id"
                                                    {{ request('sort_by') == 'id' ? 'selected' : '' }}>
                                                    {{ __('ID') }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Order -->
                                        <div>
                                            <label
                                                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ __('Order') }}
                                            </label>
                                            <select name="order"
                                                class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                                <option value="desc"
                                                    {{ request('order', 'desc') == 'desc' ? 'selected' : '' }}>
                                                    {{ __('Descending (Newest First)') }}
                                                </option>
                                                <option value="asc"
                                                    {{ request('order') == 'asc' ? 'selected' : '' }}>
                                                    {{ __('Ascending (Oldest First)') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Modal Actions -->
                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" @click="$dispatch('close-modal', 'filter-modal')"
                                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                            {{ __('Cancel') }}
                                        </button>

                                        <a href="{{ route('suppliers.index') }}"
                                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                        {{ __('Reset') }}
                                        </a>
                                        <button type="submit"
                                            class="rounded-md bg-[#3f7a5c] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#365f4a] focus:outline-none focus:ring-2 focus:ring-[#3f7a5c] focus:ring-offset-2">
                                            {{ __('Apply Filter') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </x-modal>
                        <button
                            class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                            {{ __('Export') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th
                                    class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-widest text-[#4B5563] dark:text-gray-400">
                                    {{ __('Name') }}</th>
                                <th
                                    class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-widest text-[#4B5563] dark:text-gray-400">
                                    {{ __('Total Layups') }}</th>
                                <th
                                    class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-widest text-[#4B5563] dark:text-gray-400">
                                    {{ __('Created At') }}</th>
                                <th
                                    class="px-6 py-4 text-right text-[11px] font-semibold uppercase tracking-widest text-[#4B5563] dark:text-gray-400">
                                    {{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($suppliers as $supplier)
                                <tr
                                    class="border-b border-gray-100 transition-colors duration-150 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 text-sm font-semibold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                                {{ strtoupper(substr($supplier->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ $supplier->name }}</p>
                                                <p class="font-mono text-xs text-gray-500 dark:text-gray-400">ID:
                                                    SUP-{{ str_pad($supplier->id, 4, '0', STR_PAD_LEFT) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $supplier->layups_count }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $supplier->created_at->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- TOMBOL EDIT -->
                                            <button
                                                class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 transition-all duration-150 hover:border-gray-400 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-400 dark:hover:border-gray-500 dark:hover:bg-gray-700"
                                                @click="window.dispatchEvent(new CustomEvent('open-edit-modal', { detail: { id: {{ $supplier->id }}, name: '{{ addslashes($supplier->name) }}' } }))">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="-0.5 -0.5 16 16"
                                                    fill="none" id="Edit-Pen-2-Line--Streamline-Majesticons"
                                                    height="25" width="16">

                                                    <path stroke="#000000" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="m8.75 3.75 1.433125 -1.433125a0.625 0.625 0 0 1 0.8837499999999999 0l1.61625 1.61625a0.625 0.625 0 0 1 0 0.8837499999999999L11.25 6.25m-2.5 -2.5 -6.0668750000000005 6.0668750000000005a0.625 0.625 0 0 0 -0.18312499999999998 0.44187499999999996V11.875a0.625 0.625 0 0 0 0.625 0.625h1.61625a0.625 0.625 0 0 0 0.44187499999999996 -0.18312499999999998L11.25 6.25m-2.5 -2.5 2.5 2.5"
                                                        stroke-width="1"></path>
                                                </svg>
                                            </button>
                                            <!-- TOMBOL DELETE - GANTI JADI TRIGGER MODAL -->
                                            <button type="button"
                                                class="rounded-lg border border-red-300 px-3 py-1.5 text-sm text-red-600 transition-all duration-150 hover:border-red-400 hover:bg-red-50 dark:border-red-700 dark:text-red-400 dark:hover:border-red-600 dark:hover:bg-red-900/30"
                                                @click="window.dispatchEvent(new CustomEvent('open-delete-modal', { detail: { id: {{ $supplier->id }}, name: '{{ addslashes($supplier->name) }}' } }))">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" id="Bin-1--Streamline-Ultimate"
                                                    height="24" width="16">

                                                    <path stroke="#000000" stroke-linecap="round"
                                                        stroke-linejoin="round" d="M1 5h22" stroke-width="1.5"></path>
                                                    <path stroke="#000000" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M14.25 1h-4.5c-0.39782 0 -0.77936 0.15804 -1.06066 0.43934C8.40804 1.72064 8.25 2.10218 8.25 2.5V5h7.5V2.5c0 -0.39782 -0.158 -0.77936 -0.4393 -1.06066C15.0294 1.15804 14.6478 1 14.25 1Z"
                                                        stroke-width="1.5"></path>
                                                    <path stroke="#000000" stroke-linecap="round"
                                                        stroke-linejoin="round" d="M9.75 17.75v-7.5"
                                                        stroke-width="1.5">
                                                    </path>
                                                    <path stroke="#000000" stroke-linecap="round"
                                                        stroke-linejoin="round" d="M14.25 17.75v-7.5"
                                                        stroke-width="1.5"></path>
                                                    <path stroke="#000000" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M18.86 21.62c-0.0278 0.3758 -0.197 0.7271 -0.4735 0.9832 -0.2764 0.256 -0.6397 0.3978 -1.0165 0.3968H6.63c-0.37683 0.001 -0.74006 -0.1408 -1.01653 -0.3968 -0.27647 -0.2561 -0.44565 -0.6074 -0.47347 -0.9832L3.75 5h16.5l-1.39 16.62Z"
                                                        stroke-width="1.5"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        {{ __('No suppliers found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div
                    class="flex items-center justify-between border-t border-gray-200 bg-white px-6 py-3 dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm text-gray-700 dark:text-gray-400">
                        {{ __('Showing') }} {{ $suppliers->firstItem() ?? 0 }} {{ __('to') }}
                        {{ $suppliers->lastItem() ?? 0 }} {{ __('of') }} {{ $suppliers->total() }}
                        {{ __('results') }}
                    </p>
                    <div class="flex gap-2">
                        @if ($suppliers->onFirstPage())
                            <button disabled
                                class="cursor-not-allowed rounded-md border border-gray-300 bg-white px-2 py-1 text-gray-400 dark:border-gray-600 dark:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="h-4 w-4"
                                    fill="currentColor">
                                    <path
                                        d="M3.8486400000000005 7.209265000000001c-0.437355 0.437355 -0.437355 1.147615 0 1.5849700000000002l6.71775 6.71775c0.437355 0.437355 1.147615 0.437355 1.5849700000000002 0s0.437355 -1.147615 0 -1.5849700000000002L6.2243450000000005 8 12.147860000000001 2.072985c0.437355 -0.437355 0.437355 -1.147615 0 -1.5849700000000002s-1.147615 -0.437355 -1.5849700000000002 0l-6.71775 6.71775Z" />
                                </svg>
                            </button>
                        @else
                            <a href="{{ $suppliers->previousPageUrl() }}"
                                class="rounded-md border border-gray-300 bg-white px-2 py-1 text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="h-4 w-4"
                                    fill="currentColor">
                                    <path
                                        d="M3.8486400000000005 7.209265000000001c-0.437355 0.437355 -0.437355 1.147615 0 1.5849700000000002l6.71775 6.71775c0.437355 0.437355 1.147615 0.437355 1.5849700000000002 0s0.437355 -1.147615 0 -1.5849700000000002L6.2243450000000005 8 12.147860000000001 2.072985c0.437355 -0.437355 0.437355 -1.147615 0 -1.5849700000000002s-1.147615 -0.437355 -1.5849700000000002 0l-6.71775 6.71775Z" />
                                </svg>
                            </a>
                        @endif

                        @if ($suppliers->hasMorePages())
                            <a href="{{ $suppliers->nextPageUrl() }}"
                                class="rounded-md border border-gray-300 bg-white px-2 py-1 text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="h-4 w-4"
                                    fill="currentColor">
                                    <path
                                        d="M12.15136 7.209265000000001c0.437355 0.437355 0.437355 1.147615 0 1.5849700000000002l-6.71775 6.71775c-0.437355 0.437355 -1.147615 0.437355 -1.5849700000000002 0s-0.437355 -1.147615 0 -1.5849700000000002L9.775655 8 3.8521400000000003 2.072985c-0.437355 -0.437355 -0.437355 -1.147615 0 -1.5849700000000002s1.147615 -0.437355 1.5849700000000002 0l6.71775 6.71775Z" />
                                </svg>
                            </a>
                        @else
                            <button disabled
                                class="cursor-not-allowed rounded-md border border-gray-300 bg-white px-2 py-1 text-gray-400 dark:border-gray-600 dark:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="h-4 w-4"
                                    fill="currentColor">
                                    <path
                                        d="M12.15136 7.209265000000001c0.437355 0.437355 0.437355 1.147615 0 1.5849700000000002l-6.71775 6.71775c-0.437355 0.437355 -1.147615 0.437355 -1.5849700000000002 0s-0.437355 -1.147615 0 -1.5849700000000002L9.775655 8 3.8521400000000003 2.072985c-0.437355 -0.437355 -0.437355 -1.147615 0 -1.5849700000000002s1.147615 -0.437355 1.5849700000000002 0l6.71775 6.71775Z" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Supplier Form Modal -->
            <x-modal name="supplier-form" :show="false" focusable>
                <div class="border-b border-gray-100 bg-white px-6 py-4 dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        <span x-show="!isEditing">{{ __('Add New Supplier') }}</span>
                        <span x-show="isEditing">{{ __('Edit Supplier') }}</span>
                    </h2>
                </div>

                <div class="bg-white px-6 py-4 dark:bg-gray-800">
                    <form id="supplier-form" method="POST" :action="formAction">
                        @csrf
                        <template x-if="isEditing">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="mb-4">
                            <label for="supplier-name"
                                class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Supplier Name') }}
                            </label>
                            <input id="supplier-name" name="name" type="text" x-model="formData.name"
                                class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-400 transition focus:border-transparent focus:ring-2 focus:ring-[#3f7a5c] dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                                placeholder="e.g., Nordic Timber Co." required />
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </form>
                </div>

                <div
                    class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
                    <button type="button" @click="closeModal()"
                        class="rounded-lg border border-gray-300 px-4 py-2 font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" form="supplier-form"
                        class="rounded-lg bg-[#3f7a5c] px-4 py-2 font-medium text-white transition hover:bg-[#2d5b45]">
                        <span x-show="!isEditing">{{ __('Add Supplier') }}</span>
                        <span x-show="isEditing">{{ __('Update Supplier') }}</span>
                    </button>
                </div>
            </x-modal>

            <!-- Delete Confirmation Modal -->
            <x-modal name="delete-confirmation" :show="false" focusable>
                <div class="bg-white px-6 py-4 dark:bg-gray-800">
                    <div class="flex items-start gap-4">
                        <!-- Icon Warning -->
                        <div
                            class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>

                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ __('Delete Supplier') }}
                            </h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Are you sure you want to delete') }}
                                <span class="font-semibold text-gray-900 dark:text-gray-100"
                                    x-text="deleteData.name"></span>?
                            </p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">
                                {{ __('This action cannot be undone.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
                    <button type="button" @click="closeDeleteModal()"
                        class="rounded-lg border border-gray-300 px-4 py-2 font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" @click="confirmDelete()"
                        class="rounded-lg bg-red-600 px-4 py-2 font-medium text-white transition hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700">
                        {{ __('Delete') }}
                    </button>
                </div>
            </x-modal>

            <!-- Hidden Delete Form -->
            <form id="delete-form" method="POST" :action="deleteAction" style="display: none;">
                @csrf
                @method('DELETE')
            </form>

            <script>
                function supplierManager() {
                    return {
                        isEditing: false,
                        formData: {
                            name: ''
                        },
                        formAction: '{{ route('suppliers.store') }}',
                        deleteData: {
                            id: null,
                            name: ''
                        },
                        deleteAction: '',

                        openCreateModal() {
                            console.log('Opening CREATE modal');
                            this.isEditing = false;
                            this.formData.name = '';
                            this.formAction = '{{ route('suppliers.store') }}';
                            this.$dispatch('open-modal', 'supplier-form');
                        },

                        openEditModal(data) {
                            console.log('Opening EDIT modal', data);
                            this.isEditing = true;
                            this.formData.name = data.name;
                            this.formAction = `/suppliers/${data.id}`;
                            this.$dispatch('open-modal', 'supplier-form');
                        },

                        closeModal() {
                            this.$dispatch('close-modal', 'supplier-form');
                        },

                        openDeleteModal(data) {
                            console.log('Opening DELETE modal', data);
                            this.deleteData.id = data.id;
                            this.deleteData.name = data.name;
                            this.deleteAction = `/suppliers/${data.id}`;
                            this.$dispatch('open-modal', 'delete-confirmation');
                        },

                        closeDeleteModal() {
                            this.$dispatch('close-modal', 'delete-confirmation');
                        },

                        confirmDelete() {
                            // Submit hidden form
                            document.getElementById('delete-form').submit();
                        }
                    }
                }
            </script>
        </div>
</x-app-layout>
