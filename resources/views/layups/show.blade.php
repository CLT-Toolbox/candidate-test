<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-200 leading-tight">
                {{ $layup->name }}
            </h2>
            <a href="{{ route('suppliers.show', $supplier) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ __('Back to Supplier') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700">
                <div class="p-6 text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Layers</h3>
                    <a href="{{ route('suppliers.layups.layers.create', [$supplier, $layup]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4 inline-block">
                        {{ __('Add Layer') }}
                    </a>

                    @if($layup->layers->isEmpty())
                        <p class="text-gray-500">{{ __('No layers found.') }}</p>
                    @else
                        <table class="w-full text-sm text-left rtl:text-right text-gray-400">
                            <thead class="text-xs text-gray-300 uppercase bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Order</th>
                                    <th scope="col" class="px-6 py-3">Thickness</th>
                                    <th scope="col" class="px-6 py-3">Width</th>
                                    <th scope="col" class="px-6 py-3">Angle</th>
                                    <th scope="col" class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($layup->layers as $layer)
                                    <tr class="bg-gray-800 border-b border-gray-700 hover:bg-gray-700">
                                        <td class="px-6 py-4">{{ $layer->layer_order }}</td>
                                        <td class="px-6 py-4">{{ $layer->thickness }}</td>
                                        <td class="px-6 py-4">{{ $layer->width }}</td>
                                        <td class="px-6 py-4">{{ $layer->angle }}</td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('suppliers.layups.layers.edit', [$supplier, $layup, $layer]) }}" class="text-green-400 hover:underline">Edit</a>
                                            <form method="POST" action="{{ route('suppliers.layups.layers.destroy', [$supplier, $layup, $layer]) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:underline ml-2" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
