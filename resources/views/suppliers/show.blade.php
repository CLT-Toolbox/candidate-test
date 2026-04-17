<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    {{ $supplier->name }}
                </h2>
                <p class="text-gray-400 mt-1 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>
                    {{ $supplier->layups->count() }} Layups • {{ $supplier->layups->sum(fn($l) => $l->layers->count()) }} Layers
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('suppliers.export', $supplier) }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-900/30 text-indigo-300 font-semibold rounded-lg hover:bg-indigo-900/50 transition" download="supplier-{{ $supplier->id }}.json">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export
                </a>
                <a href="{{ route('suppliers.import-form') }}" class="inline-flex items-center justify-center px-4 py-2 bg-yellow-900/30 text-yellow-300 font-semibold rounded-lg hover:bg-yellow-900/50 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Import
                </a>
                <a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-900/30 text-blue-300 font-semibold rounded-lg hover:bg-blue-900/50 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        @if ($message = session('success'))
            <div class="mb-6 bg-green-900/20 border border-green-800 text-green-200 px-6 py-4 rounded-lg flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span>{{ $message }}</span>
            </div>
        @endif

        <!-- Layups Section -->
        <div class="bg-gray-800 rounded-2xl shadow-lg border border-gray-700 overflow-hidden mb-8">
            <div class="px-8 py-6 border-b border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white">Layups</h3>
                </div>
                <a href="{{ route('suppliers.layups.create', $supplier) }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Layup
                </a>
            </div>

            <div class="p-8">
                @if($supplier->layups->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="text-gray-400 font-medium">No layups yet</p>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($supplier->layups as $layup)
                            <div class="border-l-4 border-blue-500 bg-gray-700/50 rounded-r-xl p-6">
                                <!-- Layup Header -->
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-900/50 flex items-center justify-center text-blue-400 font-bold text-sm">
                                            {{ $loop->iteration }}
                                        </div>
                                        <h4 class="text-lg font-bold text-white">{{ $layup->name }}</h4>
                                        <span class="px-3 py-1 bg-blue-900/30 text-blue-300 text-xs font-semibold rounded-full">
                                            {{ $layup->layers->count() }} layers
                                        </span>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('suppliers.layups.edit', [$supplier, $layup]) }}" class="inline-flex items-center px-3 py-1 text-blue-400 bg-blue-900/30 rounded hover:bg-blue-900/50 transition text-sm font-medium">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('suppliers.layups.destroy', [$supplier, $layup]) }}" class="inline" onsubmit="return confirm('Delete this layup and all its layers?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1 text-red-400 bg-red-900/30 rounded hover:bg-red-900/50 transition text-sm font-medium">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Layers Section -->
                                @if($layup->layers->isEmpty())
                                    <div class="bg-gray-700 rounded-lg p-4 text-center mb-4">
                                        <p class="text-gray-400 text-sm">No layers added yet</p>
                                    </div>
                                @else
                                    <div class="bg-gray-700 rounded-lg overflow-hidden mb-4 border border-gray-600">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-600 border-b border-gray-600">
                                                <tr>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-300">Order</th>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-300">Thickness (mm)</th>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-300">Width (mm)</th>
                                                    <th class="px-4 py-3 text-left font-semibold text-gray-300">Angle (°)</th>
                                                    <th class="px-4 py-3 text-center font-semibold text-gray-300">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-600">
                                                @foreach($layup->layers as $layer)
                                                    <tr class="hover:bg-gray-600 transition">
                                                        <td class="px-4 py-3">
                                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-purple-900/30 text-purple-300 font-semibold text-xs">
                                                                {{ $layer->layer_order }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 font-medium text-white">{{ $layer->thickness }}</td>
                                                        <td class="px-4 py-3 font-medium text-white">{{ $layer->width }}</td>
                                                        <td class="px-4 py-3 font-medium text-white">{{ $layer->angle }}</td>
                                                        <td class="px-4 py-3 text-center">
                                                            <div class="flex justify-center gap-2">
                                                                <a href="{{ route('suppliers.layups.layers.edit', [$supplier, $layup, $layer]) }}" class="text-blue-400 hover:text-blue-300 font-medium transition" title="Edit layer">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                                </a>
                                                                <form method="POST" action="{{ route('suppliers.layups.layers.destroy', [$supplier, $layup, $layer]) }}" class="inline" onsubmit="return confirm('Delete this layer?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-red-400 hover:text-red-300 font-medium transition" title="Delete layer">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                <a href="{{ route('suppliers.layups.layers.create', [$supplier, $layup]) }}" class="inline-flex items-center px-4 py-2 bg-purple-900/30 text-purple-300 font-semibold rounded-lg hover:bg-purple-900/50 transition text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Add Layer
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Delete Supplier Section -->
        <div class="bg-red-900/20 border-2 border-red-800 rounded-2xl p-8">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg bg-red-900/50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-red-200 mb-2">Danger Zone</h3>
                    <p class="text-red-300 text-sm mb-4">Deleting this supplier will permanently remove it along with all its layups and layers. This action cannot be undone.</p>
                    <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="inline" onsubmit="return confirm('⚠️ Are you absolutely sure? This will delete all related layups and layers. This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition transform hover:scale-105 active:scale-95">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Delete Supplier & All Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
