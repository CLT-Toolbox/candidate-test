<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Add Layup</h2>
    </x-slot>

    <div class="p-8 max-w-xl mx-auto">
        <div class="bg-white p-6 rounded-xl shadow">

            <form action="{{ route('clt-layups.store') }}" method="POST">
                @csrf

                <input type="hidden" name="supplier_id" value="{{ $supplierId }}">

                <div class="mb-4">
                    <label class="block text-sm mb-1">Layup Name</label>
                    <input type="text" name="name"
                           class="w-full border rounded-lg px-3 py-2"
                           required>
                </div>

                <div class="flex justify-end gap-2">
                    <button class="bg-emerald-600 text-white px-4 py-2 rounded-lg">
                        Save
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>