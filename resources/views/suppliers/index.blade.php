<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-3xl font-bold text-gray-100">
                Suppliers Management
            </h2>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('suppliers.import-form') }}" class="inline-flex items-center px-4 py-2 bg-gray-700 text-gray-100 rounded-lg hover:bg-gray-600 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3v-6"></path></svg>
                    Import
                </a>
                <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transition transform hover:scale-105 active:scale-95 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    New Supplier
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        @if($suppliers->isEmpty())
            <div class="bg-gray-800 rounded-2xl shadow-lg p-12 text-center border border-gray-700">
                <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-100 mb-2">No suppliers yet</h3>
                <p class="text-gray-400 mb-6">Get started by creating your first supplier</p>
                <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create First Supplier
                </a>
            </div>
        @else
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-gray-800 rounded-xl shadow p-4 border border-gray-700">
                    <p class="text-gray-400 text-sm font-medium">Total Suppliers</p>
                    <p class="text-3xl font-bold text-gray-100 mt-1">{{ $suppliers->count() }}</p>
                </div>
                <div class="bg-gray-800 rounded-xl shadow p-4 border border-gray-700">
                    <p class="text-gray-400 text-sm font-medium">Total Layups</p>
                    <p class="text-3xl font-bold text-gray-100 mt-1">{{ $suppliers->sum(fn($s) => $s->layups->count()) }}</p>
                </div>
                <div class="bg-gray-800 rounded-xl shadow p-4 border border-gray-700">
                    <p class="text-gray-400 text-sm font-medium">Total Layers</p>
                    <p class="text-3xl font-bold text-gray-100 mt-1">{{ $suppliers->sum(fn($s) => $s->layups->sum(fn($l) => $l->layers->count())) }}</p>
                </div>
            </div>

            <!-- Suppliers Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($suppliers as $supplier)
                    <div class="group bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-1 border border-gray-700 hover:border-blue-500">
                        <!-- Card Header -->
                        <div class="h-2 bg-gradient-to-r from-blue-500 to-purple-600 group-hover:via-indigo-500 transition"></div>

                        <!-- Card Body -->
                        <div class="p-6">
                            <!-- Supplier Name -->
                            <h3 class="text-xl font-bold text-gray-100 mb-3 truncate group-hover:text-blue-400 transition">
                                {{ $supplier->name }}
                            </h3>

                            <!-- Stats -->
                            <div class="grid grid-cols-2 gap-3 mb-6 pb-6 border-b border-gray-700">
                                <div class="bg-blue-900/30 rounded-lg p-3">
                                    <p class="text-xs text-gray-400 font-medium">Layups</p>
                                    <p class="text-2xl font-bold text-blue-400">{{ $supplier->layups->count() }}</p>
                                </div>
                                <div class="bg-purple-900/30 rounded-lg p-3">
                                    <p class="text-xs text-gray-400 font-medium">Layers</p>
                                    <p class="text-2xl font-bold text-purple-400">{{ $supplier->layups->sum(fn($l) => $l->layers->count()) }}</p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-900/30 text-blue-400 font-semibold rounded-lg hover:bg-blue-900/50 transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    View
                                </a>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-green-900/30 text-green-400 font-semibold rounded-lg hover:bg-green-900/50 transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit
                                </a>
                                <a href="{{ route('suppliers.export', $supplier) }}" class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-indigo-900/30 text-indigo-400 font-semibold rounded-lg hover:bg-indigo-900/50 transition" title="Export supplier data as JSON">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Export
                                </a>
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="inline flex-1" onsubmit="return confirm('Are you sure? This will delete the supplier and all related data.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 bg-red-900/30 text-red-400 font-semibold rounded-lg hover:bg-red-900/50 transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
