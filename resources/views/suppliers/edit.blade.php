<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Supplier
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-gray-700 dark:text-gray-200">Name</label>
                        <input type="text"
                               name="name"
                               value="{{ $supplier->name }}"
                               class="w-full mt-1 rounded border-gray-300"
                               required>
                    </div>

                    <div class="mt-4">
                        <button class="px-4 py-2 bg-green-600 text-white rounded">
                            Update
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-app-layout>