<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Add Layer
        </h2>
    </x-slot>

    <div class="p-8 max-w-xl mx-auto">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">

            <form method="POST" action="{{ route('clt-layers.store') }}">
                @csrf

                <input type="hidden" name="layup_id" value="{{ $layup->id }}">

                <div class="mb-4">
                    <label class="block text-sm mb-1 text-gray-600">Layup</label>
                    <div class="w-full border border-gray-200 rounded-lg px-3 py-2 bg-gray-50 text-gray-700">
                        {{ $layup->name }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm mb-1 text-gray-600">Layer Order</label>
                    <input type="number"
                           name="layer_order"
                           placeholder="e.g. 1"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-100 focus:border-emerald-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm mb-1 text-gray-600">Thickness (mm)</label>
                    <input type="number"
                           step="0.01"
                           name="thickness"
                           placeholder="e.g. 35"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-100 focus:border-emerald-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm mb-1 text-gray-600">Width (mm)</label>
                    <input type="number"
                           step="0.01"
                           name="width"
                           placeholder="e.g. 120"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-100 focus:border-emerald-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm mb-1 text-gray-600">Angle (°)</label>
                    <input type="number"
                           step="0.01"
                           name="angle"
                           placeholder="e.g. 0 / 90"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-100 focus:border-emerald-500">
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('clt-layups.show', $layup->id) }}"
                       class="px-4 py-2 border border-gray-200 rounded-lg text-sm hover:bg-gray-50">
                        Cancel
                    </a>

                    <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm shadow">
                        Save
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>