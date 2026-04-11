<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-400 mb-1">
                    <a href="{{ route('suppliers.index') }}" class="hover:underline">Suppliers</a>
                    / <a href="{{ route('suppliers.show', $supplier) }}" class="hover:underline">{{ $supplier->name }}</a>
                    / {{ $layup->name }}
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Layup: {{ $layup->name }}</h2>
            </div>
            <button x-data @click="$dispatch('open-modal', 'create-layer')"
                class="inline-flex items-center px-4 py-2 bg-green-700 text-white text-sm rounded-lg hover:bg-green-800">
                + Add Layer
            </button>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4">
                <div class="text-xs text-gray-400 uppercase mb-1">Total Thickness</div>
                <div class="text-2xl font-bold text-green-700">
                    {{ $layup->layers->sum('thickness') }}mm
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-4">
                <div class="text-xs text-gray-400 uppercase mb-1">Total Layers</div>
                <div class="text-2xl font-bold text-green-700">{{ $layup->layers->count() }} Layers</div>
            </div>
        </div>

        <!-- Layers Table -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold text-gray-700">Layer Composition</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thickness (mm)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Width (mm)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Angle (°)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($layup->layers->sortBy('layer_order') as $layer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-700">{{ $layer->layer_order }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $layer->thickness }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $layer->width }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $layer->angle }}°</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button x-data @click="$dispatch('open-modal', 'edit-layer-{{ $layer->id }}')"
                                    class="text-sm text-blue-600 hover:underline">Edit</button>
                                <form method="POST" action="{{ route('suppliers.layups.layers.destroy', [$supplier, $layup, $layer]) }}"
                                    onsubmit="return confirm('Delete this layer?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-sm text-red-500 hover:underline">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Layer Modal -->
                    <x-modal name="edit-layer-{{ $layer->id }}">
                        <form method="POST" action="{{ route('suppliers.layups.layers.update', [$supplier, $layup, $layer]) }}" class="p-6">
                            @csrf @method('PATCH')
                            <h2 class="text-lg font-semibold mb-4">Edit Layer</h2>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-input-label value="Order" />
                                    <x-text-input name="layer_order" type="number" class="mt-1 block w-full" value="{{ $layer->layer_order }}" required />
                                </div>
                                <div>
                                    <x-input-label value="Thickness (mm)" />
                                    <x-text-input name="thickness" type="number" step="0.01" class="mt-1 block w-full" value="{{ $layer->thickness }}" required />
                                </div>
                                <div>
                                    <x-input-label value="Width (mm)" />
                                    <x-text-input name="width" type="number" step="0.01" class="mt-1 block w-full" value="{{ $layer->width }}" required />
                                </div>
                                <div>
                                    <x-input-label value="Angle (°)" />
                                    <x-text-input name="angle" type="number" step="0.01" class="mt-1 block w-full" value="{{ $layer->angle }}" required />
                                </div>
                            </div>
                            <div class="flex justify-end gap-2">
                                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                <x-primary-button>Save</x-primary-button>
                            </div>
                        </form>
                    </x-modal>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">No layers yet. Add one!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Layer Modal -->
    <x-modal name="create-layer">
        <form method="POST" action="{{ route('suppliers.layups.layers.store', [$supplier, $layup]) }}" class="p-6">
            @csrf
            <h2 class="text-lg font-semibold mb-4">Add Layer</h2>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <x-input-label value="Order" />
                    <x-text-input name="layer_order" type="number" class="mt-1 block w-full" placeholder="1" required />
                    <x-input-error :messages="$errors->get('layer_order')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="Thickness (mm)" />
                    <x-text-input name="thickness" type="number" step="0.01" class="mt-1 block w-full" placeholder="40" required />
                    <x-input-error :messages="$errors->get('thickness')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="Width (mm)" />
                    <x-text-input name="width" type="number" step="0.01" class="mt-1 block w-full" placeholder="1200" required />
                    <x-input-error :messages="$errors->get('width')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="Angle (°)" />
                    <x-text-input name="angle" type="number" step="0.01" class="mt-1 block w-full" placeholder="0" required />
                    <x-input-error :messages="$errors->get('angle')" class="mt-1" />
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button>Create</x-primary-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
