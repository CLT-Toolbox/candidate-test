<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            {{ $layer ? 'Edit Layer' : 'Create Layer' }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <form method="POST"
              action="{{ $layer ? route('layers.update', $layer) : route('layers.store') }}"
              class="bg-white p-6 rounded-xl shadow space-y-4">

            @csrf
            @if($layer) @method('PUT') @endif

            {{-- LAYUP --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Layup
                </label>

                <select name="layup_id"
                        class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500
                        @error('layup_id') border-red-500 @enderror">

                    <option value="">-- Select Layup --</option>

                    @foreach($layups as $layup)
                        <option value="{{ $layup->id }}"
                            {{ old('layup_id', $layer->layup_id ?? '') == $layup->id ? 'selected' : '' }}>
                            {{ $layup->name }}
                        </option>
                    @endforeach
                </select>

                @error('layup_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- LAYER ORDER --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Layer Order
                </label>

                <input type="number"
                       name="layer_order"
                       value="{{ old('layer_order', $layer->layer_order ?? '') }}"
                       class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500
                       @error('layer_order') border-red-500 @enderror">

                @error('layer_order')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- THICKNESS --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Thickness
                </label>

                <input type="number"
                       step="0.01"
                       name="thickness"
                       value="{{ old('thickness', $layer->thickness ?? '') }}"
                       class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500
                       @error('thickness') border-red-500 @enderror">

                @error('thickness')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- WIDTH --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Width
                </label>

                <input type="number"
                       step="0.01"
                       name="width"
                       value="{{ old('width', $layer->width ?? '') }}"
                       class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500
                       @error('width') border-red-500 @enderror">

                @error('width')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ANGLE --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Angle
                </label>

                <input type="number"
                       step="0.01"
                       name="angle"
                       value="{{ old('angle', $layer->angle ?? '') }}"
                       class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500
                       @error('angle') border-red-500 @enderror">

                @error('angle')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- BUTTONS --}}
            <div class="flex justify-end gap-2 pt-2">

                <a href="{{ route('layers.index') }}"
                   class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Cancel
                </a>

                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    {{ $layer ? 'Update' : 'Save' }}
                </button>

            </div>

        </form>

    </div>
</x-app-layout>