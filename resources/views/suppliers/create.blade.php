<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Create Supplier
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">

        <div class="bg-white p-6 rounded-xl shadow">

            <form method="POST" action="{{ route('suppliers.store') }}">
                @csrf

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Supplier Name
                </label>

                <input type="text"
                       name="name"
                       class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="Enter supplier name"
                       required>

                <div class="mt-4 flex justify-end gap-2">

                    <a href="{{ route('suppliers.index') }}"
                       class="px-4 py-2 bg-gray-200 rounded-lg">
                        Cancel
                    </a>

                    <button class="px-4 py-2 bg-black/40 text-black rounded-lg hover:bg-blue-700">
                        Save
                    </button>

                </div>
            </form>

        </div>

    </div>
</x-app-layout>