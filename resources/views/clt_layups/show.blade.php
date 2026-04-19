<x-app-layout>
    <x-slot name="header">
        <div class=" text-white px-8 py-4">
            <div class="flex items-center justify-between max-w-7xl mx-auto">
                <div class="flex items-center gap-3">
                    <div>
                        <div class="font-semibold text-xl">Layers</div>
                        <div class="text-xs text-gray-500 -mt-1">Engineering Admin</div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="p-8 max-w-7xl mx-auto">

        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('suppliers.index') }}">Suppliers</a>
            <span>›</span>
            <a href="{{ route('suppliers.show', $layup->supplier->id) }}">
                {{ $layup->supplier->name }}
            </a>
            <span>›</span>
            <span class="text-gray-800 font-medium">{{ $layup->name }}</span>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border p-8 mb-8">
            <div class="flex justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">{{ $layup->name }}</h1>
                    <p class="text-gray-500 mt-1">
                        Supplier: {{ $layup->supplier->name }}
                    </p>
                </div>

                <a href="{{ route('clt-layups.edit', $layup->id) }}"
                   class="px-5 py-2 border rounded-xl hover:bg-gray-50 text-sm">
                    ✏️ Edit
                </a>
            </div>
        </div>

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Layers</h2>

            <a href="{{ route('clt-layers.create', ['layup_id' => $layup->id]) }}"
               class="px-5 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 text-sm">
                + Add Layer
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-4 text-left">Order</th>
                        <th class="px-6 py-4 text-left">Thickness</th>
                        <th class="px-6 py-4 text-left">Width</th>
                        <th class="px-6 py-4 text-left">Angle</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse($layup->layers as $layer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">
                                Layer {{ $layer->layer_order }}
                            </td>
                            <td class="px-6 py-4">{{ rtrim(rtrim($layer->thickness, '0'), '.') }} mm</td>
                            <td class="px-6 py-4">{{ rtrim(rtrim($layer->width, '0'), '.') }} mm</td>
                            <td class="px-6 py-4">{{ rtrim(rtrim($layer->angle, '0'), '.') }}°</td>

                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('clt-layers.edit', $layer->id) }}"
                                   class="text-emerald-600 text-sm">Edit</a>

                                <form action="{{ route('clt-layers.destroy', $layer->id) }}"
                                      method="POST"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')

                                    <button class="text-red-600 text-sm"
                                            onclick="return confirm('Delete layer?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-500">
                                No layers yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>