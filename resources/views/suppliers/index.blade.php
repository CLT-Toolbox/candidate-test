<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Suppliers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex gap-2 mb-4">
                        <a href="{{ route('dashboard.suppliers.create') }}" 
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Add Supplier
                        </a>
                        
                        <a href="{{ route('dashboard.suppliers.import.form') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Import Supplier
                        </a>
                    </div>

                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">ID</th>
                                <th class="border px-4 py-2">Name</th>
                                <th class="border px-4 py-2">Layups</th>
                                <th class="border px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $supplier)
                            <tr>
                                <td class="border px-4 text-white py-2">{{ $supplier->id }}</td>
                                <td class="border px-4 text-white py-2">{{ $supplier->name }}</td>
                                <td class="border px-4 text-white py-2">{{ $supplier->layups->count() }}</td>
                                <td class="border px-4 text-white py-2">
                                    <a href="{{ route('dashboard.suppliers.show', $supplier) }}" 
                                       class="text-blue-600 hover:underline">View</a>
                                    <a href="{{ route('dashboard.suppliers.edit', $supplier) }}" 
                                       class="text-yellow-600 hover:underline ml-2">Edit</a>
                                    <form action="{{ route('dashboard.suppliers.destroy', $supplier) }}" 
                                          method="POST" style="display:inline;"
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline ml-2">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>