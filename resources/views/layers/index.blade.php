<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="mb-2 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <a href="{{ route('suppliers.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">
                        {{ __('Suppliers') }}
                    </a>
                    <span>/</span>
                    <a href="{{ route('suppliers.layups', $supplier->id) }}"
                        class="hover:text-gray-700 dark:hover:text-gray-300">
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
                <div
                    class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

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

            {{-- Main Content: Table + Visualizer --}}
            <div class="grid grid-cols-2 gap-4">
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
                                            <span
                                                class="flex h-8 w-8 items-center justify-center rounded-full bg-[#3f7a5c] text-sm font-bold text-white">
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
                                                            stroke-linejoin="round" d="M1 5h22" stroke-width="1.5">
                                                        </path>
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            d="M14.25 1h-4.5c-0.39782 0 -0.77936 0.15804 -1.06066 0.43934C8.40804 1.72064 8.25 2.10218 8.25 2.5V5h7.5V2.5c0 -0.39782 -0.158 -0.77936 -0.4393 -1.06066C15.0294 1.15804 14.6478 1 14.25 1Z"
                                                            stroke-width="1.5"></path>
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" d="M9.75 17.75v-7.5"
                                                            stroke-width="1.5">
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
                                        <td colspan="5"
                                            class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            {{ __('No layers found for this layup.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Structure Visualizer --}}
                <div
                    class="flex flex-col overflow-hidden border border-[#D1D5DB] bg-white shadow-sm sm:rounded-lg dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex-1 overflow-y-auto p-6">
                        <div class="flex flex-col items-center">
                            <!-- Header -->
                            <h2 class="mb-6 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ __('Structure Visualizer') }}
                            </h2>

                            <div class="mb-6 flex items-center gap-4 text-xs text-gray-600 dark:text-gray-400">
                                <span class="flex items-center gap-1.5">
                                    <span class="h-3 w-3 rounded-sm bg-[#d4a574]"></span>
                                    <span>{{ __('Longitudinal (0°)') }}</span>
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <span class="h-3 w-3 rounded-sm bg-[#b8935f]"></span>
                                    <span>{{ __('Transverse (90°)') }}</span>
                                </span>
                            </div>

                            <!-- 3D Stack Container -->
                            <div class="relative flex w-full items-center" style="min-height: 350px;">
                                <!-- Side Labels -->
                                <div class="mr-4 flex flex-col justify-between self-stretch text-center"
                                    style="min-height: 300px;">
                                    <div
                                        class="text-[9px] font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                        Top<br>(Out)
                                    </div>
                                    <div
                                        class="text-[9px] font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                                        Bottom<br>(In)
                                    </div>
                                </div>

                                <!-- Layers Stack -->
                                <div class="flex flex-1 flex-col items-center justify-center gap-1.5">
                                    @forelse ($layers as $layer)
                                        <div class="relative flex items-center justify-center rounded-lg shadow-sm transition-all duration-300"
                                            style="
                                                width: {{ $layer->angle == 0 ? '85%' : '70%' }};
                                                height: {{ max(28, $layer->thickness * 0.9) }}px;
                                                background-color: {{ $layer->angle == 0 ? '#d4a574' : '#b8935f' }};
                                                transform: perspective(600px) rotateX(2deg);
                                            ">
                                            <span class="text-[10px] font-semibold text-gray-700 dark:text-gray-900"
                                                title="Layer {{ $layer->layer_order }}: {{ $layer->thickness }}mm @ {{ $layer->angle }}°">
                                                L{{ $layer->layer_order }} ({{ $layer->thickness }}mm)
                                            </span>
                                            <span class="absolute right-2 text-gray-700 dark:text-gray-900">
                                                @if ($layer->angle == 0)
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                    </svg>
                                                @else
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0019 16V8a1 1 0 00-1.6-.8l-5.333 4zM4.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0011 16V8a1 1 0 00-1.6-.8l-5.334 4z">
                                                        </path>
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                    @empty
                                        <div class="py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('No layers to visualize') }}
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="mt-6 w-full space-y-1 border-t border-gray-200 pt-4 dark:border-gray-700">
                                <p class="text-center text-xs font-medium text-gray-900 dark:text-gray-100">
                                    {{ __('Cross-Laminated Assembly') }}
                                </p>
                                <p class="text-center text-[10px] text-gray-600 dark:text-gray-400">
                                    {{ __('Total: ') }} {{ $layup->ply_count ?? 0 }} {{ __('layers') }},
                                    {{ number_format($layup->total_thickness, 0) }}mm
                                </p>
                            </div>
                        </div>
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
                            this.formAction = `/suppliers/layups/{{ $layup->id }}/layers/${data.id}`;
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
