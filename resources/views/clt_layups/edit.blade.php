<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            Edit Layup
        </h2>
    </x-slot>

    <div class="p-8 max-w-xl mx-auto">
        <div class="bg-white p-6 rounded-xl shadow">

            <form method="POST" action="{{ route('clt-layups.update', $layup->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm mb-1">Layup Name</label>
                    <input type="text"
                           name="name"
                           value="{{ $layup->name }}"
                           class="w-full border rounded-lg px-3 py-2"
                           required>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('clt-layups.index') }}"
                       class="px-4 py-2 border border-bordersoft rounded-lg text-sm">
                        Cancel
                    </a>

                    <button class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm shadow">
                        Update
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout> 