<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Supplier Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h1 class="text-2xl font-bold mb-4">{{ $supplier->name }}</h1>

                    <div class="flex gap-2 mb-4">
                        <a href="{{ route('dashboard.suppliers.layups.create', $supplier) }}" 
                           class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Add Layup
                        </a>
                        
                        <a href="{{ route('dashboard.suppliers.export', $supplier) }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Export JSON
                        </a>
                    </div>

                    <h2 class="text-xl font-bold mb-2">Layups</h2>
                    
                    @if($supplier->layups->count() > 0)
                        <table class="min-w-full border mb-4">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border px-4 py-2">Name</th>
                                    <th class="border px-4 py-2">Layers</th>
                                    <th class="border px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplier->layups as $layup)
                                <tr>
                                    <td class="border px-4 py-2">{{ $layup->name }}</td>
                                    <td class="border px-4 py-2">{{ $layup->layers->count() }}</td>
                                    <td class="border px-4 py-2">
                                        <a href="{{ route('dashboard.layups.show', $layup) }}" 
                                           class="text-blue-600 hover:underline">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500">No layups yet.</p>
                    @endif
                    
                    <a href="{{ route('dashboard.suppliers.index') }}" 
                       class="text-gray-600 hover:underline">← Back to Suppliers</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>