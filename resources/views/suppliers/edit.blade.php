<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
            Edit Supplier
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-gray-800 rounded-2xl shadow-lg border border-gray-700 overflow-hidden">
                <!-- Header -->
                <div class="h-2 bg-gradient-to-r from-green-500 via-emerald-500 to-teal-500"></div>

                <!-- Content -->
                <div class="p-8">
                    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Supplier Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-200 mb-2">
                                Supplier Name
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name', $supplier->name) }}" 
                                placeholder="e.g., Acme Supply Co."
                                class="w-full px-4 py-3 rounded-lg border-2 border-gray-600 bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:border-blue-400 transition"
                                required
                            >
                            @error('name')
                                <div class="mt-2 flex items-start gap-2 text-red-400">
                                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                    <span class="text-sm">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Current Data Info -->
                        <div class="bg-gray-700/50 rounded-lg p-4 border border-gray-600">
                            <p class="text-sm text-gray-400">
                                <span class="font-semibold">Current Data:</span> {{ $supplier->layups->count() }} layups with {{ $supplier->layups->sum(fn($l) => $l->layers->count()) }} layers
                            </p>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex gap-3 pt-6 border-t border-gray-700">
                            <a 
                                href="{{ route('suppliers.show', $supplier) }}" 
                                class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gray-700 text-gray-300 font-semibold rounded-lg hover:bg-gray-600 transition"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                Cancel
                            </a>
                            <button 
                                type="submit" 
                                class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-700 hover:to-emerald-700 transition transform hover:scale-105 active:scale-95 shadow-lg"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Update Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
