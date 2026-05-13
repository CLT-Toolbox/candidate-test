<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Edit Supplier
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">

        <div class="bg-white p-6 rounded-xl shadow">

            <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
                @csrf
                @method('PUT')

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Supplier Name
                </label>

                <input type="text"
                       name="name"
                       value="{{ $supplier->name }}"
                       class="w-full border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500">
                
                
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                <div class="mt-4 flex justify-end gap-2">

                    <a href="{{ route('suppliers.index') }}"
                       class="px-4 py-2 bg-gray-200 rounded-lg">
                        Cancel
                    </a>

                    <button class="px-4 py-2 bg-black/40 text-white rounded-lg hover:bg-yellow-600">
                        Update
                    </button>

                </div>
            </form>

        </div>

    </div>
</x-app-layout>