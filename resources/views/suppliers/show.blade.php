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
                    <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-layups')" class="bg-yellow-500 hover:bg-yellow-600">
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
                                    <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-3">{{ $layup->name }}</h4>

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

    <x-modal name="add-layups" focusable>
        <form method="POST" action="{{ route('suppliers.layups.store', $supplier) }}" class="p-6">
            @csrf
            <h3 class="text-lg font-semibold mb-4">{{ __('Add New Layup') }}</h3>

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Layup Name') }}</label>
                <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
            </div>

            <div x-data="layupForm()" class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Layers') }}</label>
                <div id="layers-container">
                    <template x-for="(layer, index) in layers" :key="index">
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold">{{ __('Layer') }} <span x-text="index + 1"></span></h4>
                                <button type="button" @click="removeLayer(index)" class="text-red-500 hover:text-red-700" x-show="layers.length > 1">{{ __('Remove') }}</button>
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
                <x-secondary-button x-on:click="$dispatch('close-modal', 'add-layups')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-secondary-button type="submit" class="bg-green-500 hover:bg-green-600">
                    {{ __('Add Layup') }}
                </x-secondary-button>
            </div>
        </form>

        <script>
            function layupForm() {
                return {
                    layers: [
                        { layer_order: 1, thickness: '', width: '', angle: '' }
                    ],
                    addLayer() {
                        this.layers.push({ layer_order: this.layers.length + 1, thickness: '', width: '', angle: '' });
                    },
                    removeLayer(index) {
                        if (this.layers.length > 1) {
                            this.layers.splice(index, 1);
                        }
                    }
                }
            }
        </script>
    </x-modal>
</x-app-layout>
