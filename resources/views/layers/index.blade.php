<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Layers</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <a href="{{ route('layers.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded">
            + Add Layer
        </a>

        <div class="mt-4 bg-white shadow rounded-lg overflow-hidden">

            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3">Layup</th>
                        <th class="p-3">Order</th>
                        <th class="p-3">Thickness</th>
                        <th class="p-3">Width</th>
                        <th class="p-3">Angle</th>
                        <th class="p-3">Added At</th>
                        <th class="p-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($layers as $layer)
                        <tr class="border-b">
                            <td class="p-3">{{ $layer->layup->name ?? '-' }}</td>
                            <td class="p-3">{{ $layer->layer_order }}</td>
                            <td class="p-3">{{ $layer->thickness }}</td>
                            <td class="p-3">{{ $layer->width }}</td>
                            <td class="p-3">{{ $layer->angle }}</td>
                            <td class="p-3">{{ $layer->created_at }}</td>

                            <td class="p-3 text-right space-x-2">

                                <a href="{{ route('layers.edit', $layer) }}"
                                   class="text-yellow-600">Edit</a>

                                <form class="inline"
                                      method="POST"
                                      action="{{ route('layers.destroy', $layer) }}">
                                    @csrf
                                    @method('DELETE')

                                    <button class="text-red-600">
                                        Delete
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>
</x-app-layout>