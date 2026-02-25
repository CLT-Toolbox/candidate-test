<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="mb-2 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <a href="{{ route('suppliers.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">
                        {{ __('Suppliers') }}
                    </a>
                    <span>/</span>
                    <a href="{{ route('suppliers.layups', $supplier->id) }}" class="hover:text-gray-700 dark:hover:text-gray-300">
                        {{ $supplier->name }}
                    </a>
                    <span>/</span>
                    <span class="text-gray-900 dark:text-gray-100">{{ $layup->name }}</span>
                </nav>
            </div>
        </div>
    </x-slot>

    <div class="py-1" x-data="layerManager()" @open-create-modal.window="openCreateModal()"
        @open-edit-modal.window="openEditModal($event.detail)"
        @open-delete-modal.window="openDeleteModal($event.detail)">

        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('success'))
                <div
                    class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4">
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

            <!-- LEFT SIDE -->
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        Layup Specification: L-{{ $layup->id }}
                    </h2>


                </div>

                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Standard {{ $layup->ply_count ?? 0 }}-layer panel for residential structural walls.
                </p>
            </div>

            <!-- RIGHT SIDE -->
            <div class="flex flex-wrap divide-x divide-gray-200 dark:divide-gray-700">




                <div class="px-6">
                    <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Total Thickness
                    </p>
                    <p class="mt-1 font-semibold text-[#3f7a5c]">
                        {{ number_format($layup->total_thickness, 0) }}mm
                    </p>
                </div>

                <div class="px-6">
                    <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Total Layers
                    </p>
                    <p class="mt-1 font-semibold text-[#3f7a5c]">
                        {{ $layup->ply_count ?? 0 }} Layers
                    </p>
                </div>

            </div>

        </div>
    </div>
</div>

            {{-- Actions Bar --}}
            <div class="mb-4 border-b border-[#E5E7EB] pb-4 dark:border-gray-700">
                <div class="flex justify-between gap-2">
                    <div class="max-w-xs flex-1">
                        <h1 class="pt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('Layers Composition') }}
                        </h1>
                    </div>
                    <div class="flex gap-2">
                        
                        <button x-data @click="window.dispatchEvent(new CustomEvent('open-create-modal'))"
                            class="flex items-center gap-2 rounded-lg bg-[#3f7a5c] px-4 py-2 font-medium text-white transition hover:bg-[#2d5b45]">
                            <span>+</span> {{ __('Add Layer') }}
                        </button>
                    </div>
                </div>
            </div>

            {{-- Layers Table --}}
            <div
                class="overflow-hidden border border-[#D1D5DB] bg-white shadow-sm sm:rounded-lg dark:border-gray-700 dark:bg-gray-800">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-[#F9FAFB] dark:bg-gray-700">
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th
                                    class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-widest text-[#4B5563] dark:text-gray-400">
                                    {{ __('Layer Order') }}
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-widest text-[#4B5563] dark:text-gray-400">
                                    {{ __('Thickness') }}
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-widest text-[#4B5563] dark:text-gray-400">
                                    {{ __('Width') }}
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-widest text-[#4B5563] dark:text-gray-400">
                                    {{ __('Angle') }}
                                </th>
                                <th
                                    class="px-6 py-4 text-right text-[11px] font-semibold uppercase tracking-widest text-[#4B5563] dark:text-gray-400">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($layers as $layer)
                                <tr
                                    class="border-b border-gray-100 transition-colors duration-150 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#3f7a5c] text-sm font-bold text-white">
                                            {{ $layer->layer_order }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ number_format($layer->thickness, 2) }}mm
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ number_format($layer->width, 2) }}mm
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ number_format($layer->angle, 1) }}°
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm text-gray-600 transition-all duration-150 hover:border-gray-400 hover:bg-gray-100 dark:border-gray-600 dark:text-gray-400 dark:hover:border-gray-500 dark:hover:bg-gray-700"
                                                @click="window.dispatchEvent(new CustomEvent('open-edit-modal', { detail: { id: {{ $layer->id }}, layer_order: {{ $layer->layer_order }}, thickness: {{ $layer->thickness }}, width: {{ $layer->width }}, angle: {{ $layer->angle }} } }))">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="-0.5 -0.5 16 16"
                                                    fill="none" height="25" width="16"
                                                    class="text-gray-700 dark:text-gray-300">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="m8.75 3.75 1.433125 -1.433125a0.625 0.625 0 0 1 0.8837499999999999 0l1.61625 1.61625a0.625 0.625 0 0 1 0 0.8837499999999999L11.25 6.25m-2.5 -2.5 -6.0668750000000005 6.0668750000000005a0.625 0.625 0 0 0 -0.18312499999999998 0.44187499999999996V11.875a0.625 0.625 0 0 0 0.625 0.625h1.61625a0.625 0.625 0 0 0 0.44187499999999996 -0.18312499999999998L11.25 6.25m-2.5 -2.5 2.5 2.5"
                                                        stroke-width="1"></path>
                                                </svg>
                                            </button>
                                            <button type="button"
                                                class="rounded-lg border border-red-300 px-3 py-1.5 text-sm text-red-600 transition-all duration-150 hover:border-red-400 hover:bg-red-50 dark:border-red-700 dark:text-red-400 dark:hover:border-red-600 dark:hover:bg-red-900/30"
                                                @click="window.dispatchEvent(new CustomEvent('open-delete-modal', { detail: { id: {{ $layer->id }}, layer_order: {{ $layer->layer_order }} } }))">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" height="24" width="16"
                                                    class="text-gray-700 dark:text-gray-300">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" d="M1 5h22" stroke-width="1.5"></path>
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M14.25 1h-4.5c-0.39782 0 -0.77936 0.15804 -1.06066 0.43934C8.40804 1.72064 8.25 2.10218 8.25 2.5V5h7.5V2.5c0 -0.39782 -0.158 -0.77936 -0.4393 -1.06066C15.0294 1.15804 14.6478 1 14.25 1Z"
                                                        stroke-width="1.5"></path>
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" d="M9.75 17.75v-7.5" stroke-width="1.5">
                                                    </path>
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" d="M14.25 17.75v-7.5"
                                                        stroke-width="1.5"></path>
                                                    <path stroke="currentColor" stroke-linecap="round"
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
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        {{ __('No layers found for this layup.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div
                    class="flex items-center justify-between border-t border-gray-200 bg-white px-6 py-3 dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm text-gray-700 dark:text-gray-400">
                        {{ __('Showing') }} {{ $layers->firstItem() ?? 0 }} {{ __('to') }}
                        {{ $layers->lastItem() ?? 0 }} {{ __('of') }} {{ $layers->total() }}
                        {{ __('results') }}
                    </p>
                    <div class="flex gap-2">
                        @if ($layers->onFirstPage())
                            <button disabled
                                class="cursor-not-allowed rounded-md border border-gray-300 bg-white px-2 py-1 text-gray-400 dark:border-gray-600 dark:bg-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="h-4 w-4"
                                    fill="currentColor">
                                    <path
                                        d="M3.8486400000000005 7.209265000000001c-0.437355 0.437355 -0.437355 1.147615 0 1.5849700000000002l6.71775 6.71775c0.437355 0.437355 1.147615 0.437355 1.5849700000000002 0s0.437355 -1.147615 0 -1.5849700000000002L6.2243450000000005 8 12.147860000000001 2.072985c0.437355 -0.437355 0.437355 -1.147615 0 -1.5849700000000002s-1.147615 -0.437355 -1.5849700000000002 0l-6.71775 6.71775Z" />
                                </svg>
                            </button>
                        @else
                            <a href="{{ $layers->previousPageUrl() }}"
                                class="rounded-md border border-gray-300 bg-white px-2 py-1 text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="h-4 w-4"
                                    fill="currentColor">
                                    <path
                                        d="M3.8486400000000005 7.209265000000001c-0.437355 0.437355 -0.437355 1.147615 0 1.5849700000000002l6.71775 6.71775c0.437355 0.437355 1.147615 0.437355 1.5849700000000002 0s0.437355 -1.147615 0 -1.5849700000000002L6.2243450000000005 8 12.147860000000001 2.072985c0.437355 -0.437355 0.437355 -1.147615 0 -1.5849700000000002s-1.147615 -0.437355 -1.5849700000000002 0l-6.71775 6.71775Z" />
                                </svg>
                            </a>
                        @endif

                        @if ($layers->hasMorePages())
                            <a href="{{ $layers->nextPageUrl() }}"
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

            {{-- Include Modals --}}
            @include('layers.partials.form-modal')
            @include('layers.partials.delete-modal')

            {{-- Alpine.js Component --}}
            <script>
                function layerManager() {
                    return {
                        isEditing: false,
                        formData: {
                            layer_order: '',
                            thickness: '',
                            width: '',
                            angle: ''
                        },
                        formAction: '{{ route('layups.layers.store', $layup->id) }}',
                        deleteData: {
                            id: null,
                            layer_order: ''
                        },
                        deleteAction: '',

                        openCreateModal() {
                            this.isEditing = false;
                            this.formData = {
                                layer_order: '',
                                thickness: '',
                                width: '',
                                angle: ''
                            };
                            this.formAction = '{{ route('layups.layers.store', $layup->id) }}';
                            this.$dispatch('open-modal', 'layer-form');
                        },

                        openEditModal(data) {
                            this.isEditing = true;
                            this.formData = {
                                layer_order: data.layer_order,
                                thickness: data.thickness,
                                width: data.width,
                                angle: data.angle
                            };
                            this.formAction = `/layups/{{ $layup->id }}/layers/${data.id}`;
                            this.$dispatch('open-modal', 'layer-form');
                        },

                        closeModal() {
                            this.$dispatch('close-modal', 'layer-form');
                        },

                        openDeleteModal(data) {
                            this.deleteData.id = data.id;
                            this.deleteData.layer_order = data.layer_order;
                            this.deleteAction = `/layups/{{ $layup->id }}/layers/${data.id}`;
                            this.$dispatch('open-modal', 'delete-confirmation');
                        },

                        closeDeleteModal() {
                            this.$dispatch('close-modal', 'delete-confirmation');
                        },

                        confirmDelete() {
                            document.getElementById('delete-form').submit();
                        }
                    }
                }
            </script>
        </div>
    </div>
</x-app-layout>
