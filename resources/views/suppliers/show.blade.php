<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between gap-2 rounded-lg bg-gray-100 dark:bg-gray-700 p-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $supplier->name }}
                </h2>
                <p class="text-l text-gray-800 dark:text-gray-200 leading-text">
                    ID: SUP-{{ $supplier->created_at->format("Y") }}-{{ str_pad($supplier->id, 3, '0', STR_PAD_LEFT) }}
                </p>
            </div>
            <div class="mt-4 flex gap-2">
                <x-link-button href="{{ route('suppliers.edit', $supplier) }}" class="bg-blue-500 hover:bg-blue-600">
                    <span class="material-symbols-outlined" style="margin-right: 8px">edit</span>
                    {{ __('Edit Supplier') }}
                </x-link-button>

                <x-link-button href="{{ route('suppliers') }}" class="bg-gray-500 hover:bg-gray-600">
                    {{ __('Back') }}
                </x-link-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Associated Layups</h1>
                <div>
                    <x-link-button href="{{ route('suppliers.export', $supplier) }}" class="bg-green-500 hover:bg-green-600">
                        <span class="material-symbols-outlined" style="margin-right: 8px">download</span>
                        {{ __('Export') }}
                    </x-link-button>
                    <x-link-button href="{{ route('suppliers.import.form', $supplier) }}" class="bg-purple-500 hover:bg-purple-600">
                        <span class="material-symbols-outlined" style="margin-right: 8px">upload</span>
                        {{ __('Import') }}
                    </x-link-button>
                    <x-secondary-button x-data="" x-on:click.prevent="window.dispatchEvent(new CustomEvent('add-layup'))" class="bg-yellow-500 hover:bg-yellow-600">
                        <span class="material-symbols-outlined" style="margin-right: 8px">add</span>
                        {{ __('Add Layups') }}
                    </x-secondary-button>
                </div>
            </div>

            @if (session('success'))
                <x-success-message :message="session('success')" />
            @endif

            @if (session('error'))
                <x-error-message :message="session('error')" />
            @endif

            @if ($errors->any())
                <x-input-error :errors="$errors" />
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Layups') }} ({{ count($supplier->layups) }})</h3>

                    @if ($supplier->layups->isEmpty())
                        <p class="text-gray-600 dark:text-gray-400">{{ __('No layups found for this supplier.') }}</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($supplier->layups as $layup)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-semibold text-gray-800 dark:text-gray-200">{{ $layup->name }}</h4>
                                        <div>
                                            <x-secondary-button x-data="" x-on:click.prevent="window.dispatchEvent(new CustomEvent('edit-layup', { detail: { layup: window.layupsData.find(l => l.id === {{ $layup->id }}) } }))" class="bg-blue-500 hover:bg-blue-600 h-8 mr-2">
                                                <span class="material-symbols-outlined" style="margin-right: 8px">edit</span>
                                                {{ __('Edit') }}
                                            </x-secondary-button>
                                            <x-danger-button x-data="" x-on:click.prevent="window.dispatchEvent(new CustomEvent('delete-layup', { detail: { layupId: {{ $layup->id }} } }))" class="h-8">
                                                <span class="material-symbols-outlined" style="margin-right: 8px">delete</span>
                                                {{ __('Delete') }}
                                            </x-danger-button>
                                        </div>
                                    </div>

                                    @if ($layup->layers->isEmpty())
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('No layers in this layup.') }}</p>
                                    @else
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-sm">
                                                <thead class="bg-gray-100 dark:bg-gray-700">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left">{{ __('Layer Order') }}</th>
                                                        <th class="px-4 py-2 text-left">{{ __('Thickness') }}</th>
                                                        <th class="px-4 py-2 text-left">{{ __('Width') }}</th>
                                                        <th class="px-4 py-2 text-left">{{ __('Angle') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($layup->layers->sortBy('layer_order') as $layer)
                                                        <tr class="border-t border-gray-200 dark:border-gray-700">
                                                            <td class="px-4 py-2">{{ $layer->layer_order }}</td>
                                                            <td class="px-4 py-2">{{ $layer->thickness }} mm</td>
                                                            <td class="px-4 py-2">{{ $layer->width }} mm</td>
                                                            <td class="px-4 py-2">{{ $layer->angle }}°</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-modal name="confirm-layup-deletion" focusable>
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">{{ __('Confirm Layup Deletion') }}</h3>
            <p class="mb-4 text-gray-600 dark:text-gray-400">{{ __('Are you sure you want to delete this layup? This action cannot be undone.') }}</p>
            <div class="flex justify-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close-modal', 'confirm-layup-deletion')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <form
                    x-data="deleteForm()"
                    method="POST"
                    :action="'{{ route('suppliers.layups.destroy', ['supplier' => $supplier->id, 'layup' => '__LAYUP_ID__']) }}'.replace('__LAYUP_ID__', layupId)"
                >
                    @csrf
                    @method('DELETE')
                    <x-danger-button type="submit">
                        {{ __('Delete Layup') }}
                    </x-danger-button>
                </form>
            </div>
        </div>
    </x-modal>

    <x-modal name="add-layups" focusable>
        <form
            x-data="layupModal()"
            method="POST"
            :action="isEdit ? '{{ route('suppliers.layups.update', ['supplier' => $supplier->id, 'layup' => '__LAYUP_ID__']) }}'.replace('__LAYUP_ID__', currentLayup.id) : '{{ route('suppliers.layups.store', $supplier) }}'"
            class="p-6"
        >
            @csrf
            <input type="hidden" name="_method" x-show="isEdit" value="PUT"></input>
            <h3 class="text-lg font-semibold mb-4" x-text="isEdit ? 'Edit Layup' : 'Add New Layup'"></h3>

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Layup Name') }}</label>
                <input type="text" name="name" id="name" x-model="formData.name" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Layers') }}</label>
                <div id="layers-container">
                    <template x-for="(layer, index) in formData.layers" :key="index">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold">{{ __('Layer') }} <span x-text="index + 1"></span></h4>
                                <button type="button" @click="removeLayer(index)" class="text-red-500 hover:text-red-700" x-show="formData.layers.length > 1">{{ __('Remove') }}</button>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label :for="'layer_order_' + index" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Layer Order') }}</label>
                                    <input type="number" :name="'layers[' + index + '][layer_order]'" :id="'layer_order_' + index" x-model="layer.layer_order" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required min="1">
                                </div>
                                <div>
                                    <label :for="'thickness_' + index" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Thickness (mm)') }}</label>
                                    <input type="number" step="0.01" :name="'layers[' + index + '][thickness]'" :id="'thickness_' + index" x-model="layer.thickness" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required min="0">
                                </div>
                                <div>
                                    <label :for="'width_' + index" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Width (mm)') }}</label>
                                    <input type="number" step="0.01" :name="'layers[' + index + '][width]'" :id="'width_' + index" x-model="layer.width" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required min="0">
                                </div>
                                <div>
                                    <label :for="'angle_' + index" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Angle (°)') }}</label>
                                    <input type="number" step="0.01" :name="'layers[' + index + '][angle]'" :id="'angle_' + index" x-model="layer.angle" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required min="0" max="360">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <button type="button" @click="addLayer()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">{{ __('Add Layer') }}</button>
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <x-secondary-button x-on:click="closeModal()">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-secondary-button type="submit" class="bg-green-500 hover:bg-green-600" x-text="isEdit ? 'Update Layup' : 'Add Layup'" />
            </div>
        </form>
    </x-modal>

    <script>
        function deleteForm() {
            return {
                layupId: null,
                init() {
                    window.addEventListener('delete-layup', (event) => {
                        this.layupId = event.detail.layupId;
                        this.$dispatch('open-modal', 'confirm-layup-deletion');
                    });
                }
            }
        }

        function layupModal() {
            return {
                isEdit: false,
                currentLayup: null,
                formData: {
                    name: '',
                    layers: [
                        { layer_order: 1, thickness: '', width: '', angle: '' }
                    ]
                },
                init() {
                    window.addEventListener('edit-layup', (event) => {
                        this.openEditModal(event.detail.layup);
                    });

                    window.addEventListener('add-layup', () => {
                        this.openAddModal();
                    });
                },
                openAddModal() {
                    this.isEdit = false;
                    this.currentLayup = null;
                    this.formData = {
                        name: '',
                        layers: [
                            { layer_order: 1, thickness: '', width: '', angle: '' }
                        ]
                    };
                    this.$dispatch('open-modal', 'add-layups');
                },
                openEditModal(layup) {
                    this.isEdit = true;
                    this.currentLayup = layup;
                    this.formData.name = layup.name;
                    this.formData.layers = layup.layers.map(layer => ({
                        layer_order: layer.layer_order,
                        thickness: layer.thickness,
                        width: layer.width,
                        angle: layer.angle
                    }));
                    this.$dispatch('open-modal', 'add-layups');
                },
                addLayer() {
                    this.formData.layers.push({
                        layer_order: this.formData.layers.length + 1,
                        thickness: '',
                        width: '',
                        angle: ''
                    });
                },
                removeLayer(index) {
                    if (this.formData.layers.length > 1) {
                        this.formData.layers.splice(index, 1);
                    }
                },
                closeModal() {
                    this.isEdit = false;
                    this.currentLayup = null;
                    this.formData = {
                        name: '',
                        layers: [
                            { layer_order: 1, thickness: '', width: '', angle: '' }
                        ]
                    };
                    this.$dispatch('close-modal', 'add-layups');
                }
            }
        }

        window.layupsData = @json($supplier->layups);
    </script>
</x-app-layout>
