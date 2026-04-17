<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ __('Edit Layer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700">
                <div class="p-6 text-gray-100">
                    <form method="POST" action="{{ route('suppliers.layups.layers.update', [$supplier, $layup, $layer]) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <label for="layer_order" class="block text-sm font-medium text-gray-300">Layer Order</label>
                            <input type="number" id="layer_order" name="layer_order" value="{{ old('layer_order', $layer->layer_order) }}" class="mt-1 block w-full border-gray-600 rounded-md shadow-sm bg-gray-700 text-white" required min="1">
                            @error('layer_order')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="thickness" class="block text-sm font-medium text-gray-300">Thickness</label>
                            <input type="number" id="thickness" name="thickness" value="{{ old('thickness', $layer->thickness) }}" class="mt-1 block w-full border-gray-600 rounded-md shadow-sm bg-gray-700 text-white" required min="0.0001" step="0.0001">
                            @error('thickness')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="width" class="block text-sm font-medium text-gray-300">Width</label>
                            <input type="number" id="width" name="width" value="{{ old('width', $layer->width) }}" class="mt-1 block w-full border-gray-600 rounded-md shadow-sm bg-gray-700 text-white" required min="0.0001" step="0.0001">
                            @error('width')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="angle" class="block text-sm font-medium text-gray-300">Angle (0-360)</label>
                            <input type="number" id="angle" name="angle" value="{{ old('angle', $layer->angle) }}" class="mt-1 block w-full border-gray-600 rounded-md shadow-sm bg-gray-700 text-white" required min="0" max="360" step="0.0001">
                            @error('angle')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('suppliers.show', $supplier) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
