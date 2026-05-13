<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Supplier Management
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg shadow">
                {{ session('success') }}
            </div>
        @endif

       <div class="flex justify-between items-center mb-4">

            <h3 class="text-lg font-medium text-gray-700">
                List Suppliers
            </h3>

            <div class="flex gap-2">

                <a href="{{ route('suppliers.export.csv') }}"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">
                    Export CSV
                </a>

                <a href="{{ route('suppliers.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow">
                    + New Supplier
                </a>

            </div>

        </div>

        <div class="bg-white shadow rounded-xl overflow-hidden">
            <table class="w-full text-sm text-left">

                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-6 py-4">{{ $supplier->id }}</td>

                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $supplier->name }}
                            </td>

                            <td class="px-6 py-4 text-right space-x-2">

                                <a href="{{ route('suppliers.edit', $supplier) }}"
                                   class="text-yellow-600 hover:underline">
                                    Edit
                                </a>

                                <form class="inline"
                                      action="{{ route('suppliers.destroy', $supplier) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this supplier?')">

                                    @csrf
                                    @method('DELETE')

                                    <button class="text-red-600 hover:underline">
                                        Delete
                                    </button>
                                </form>

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-6 text-gray-500">
                                No suppliers found
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>