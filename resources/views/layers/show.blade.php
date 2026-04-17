<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">>
            {{ __('Layer Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700">
                <div class="p-6 text-gray-100">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-400">Layer Order</p>
                            <p class="font-semibold">{{ $layer->layer_order }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Thickness</p>
                            <p class="font-semibold">{{ $layer->thickness }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Width</p>
                            <p class="font-semibold">{{ $layer->width }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Angle</p>
                            <p class="font-semibold">{{ $layer->angle }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <a href="{{ route('suppliers.show', $supplier) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Back') }}
                        </a>
                        <a href="{{ route('suppliers.layups.layers.edit', [$supplier, $layup, $layer]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Edit') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
