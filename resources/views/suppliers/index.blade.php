<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    Suppliers
                </h2>
                <p class="text-sm text-gray-500">
                    Manage timber suppliers and material sourcing.
                </p>
            </div>

            <a href="{{ route('suppliers.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-medium rounded-lg shadow">
                + Add Supplier
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex items-center justify-between mb-4">
                <div class="w-1/3">
                    <input type="text"
                           placeholder="Search suppliers by name..."
                           class="w-full px-4 py-2 border border-bordersoft rounded-lg text-sm focus:ring-2 focus:ring-primary-light focus:border-primary">
                </div>

                <div class="flex gap-2">
                    <button class="px-3 py-2 border rounded-lg text-sm">Filter</button>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-xl border border-bordersoft overflow-hidden">

                @if(session('success'))
                    <div class="p-4 text-green-600">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-textsub uppercase text-xs">
                        <tr>
                            <th class="p-4 text-left">Name</th>
                            <th class="p-4 text-left">Total Layups</th>
                            <th class="p-4 text-left">Created</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @foreach($suppliers as $supplier)
                            <tr class="hover:bg-primary-light transition">
                                
                                <td class="p-4 flex items-center gap-3">
                                    <div class="w-10 h-10 flex items-center justify-center rounded-full bg-green-100 text-green-700 font-semibold">
                                        {{ strtoupper(substr($supplier->name, 0, 2)) }}
                                    </div>

                                    <div>
                                        <div class="font-medium text-textsub">
                                            {{ $supplier->name }}
                                        </div>
                                    </div>
                                </td>

                                <td class="p-4">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                        {{ $supplier->layups_count }}
                                    </span>
                                </td>

                                <td class="p-4 text-gray-600">
                                    {{ $supplier->created_at->format('M d, Y') }}
                                </td>

                                <td class="p-4 text-right space-x-2">

                                    <a href="{{ route('suppliers.show', $supplier->id) }}"
                                    class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded-md text-xs">
                                        Detail
                                    </a>

                                    <a href="{{ route('suppliers.edit', $supplier->id) }}"
                                    class="px-3 py-1 bg-yellow-600 text-white rounded-md text-xs">
                                        Edit
                                    </a>

                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}"
                                        method="POST"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')

                                        <button onclick="return confirm('Delete this supplier?')"
                                                class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-md text-xs">
                                            Delete
                                        </button>
                                    </form>

                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $suppliers->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>