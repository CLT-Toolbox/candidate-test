<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
            Create New Supplier
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-gray-800 rounded-2xl shadow-lg border border-gray-700 overflow-hidden">
                <!-- Header -->
                <div class="h-2 bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500"></div>

                <!-- Content -->
                <div class="p-8">
                    <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-6">
                        @csrf

                        <!-- Supplier Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-200 mb-2">
                                Supplier Name
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name') }}" 
                                placeholder="e.g., Acme Supply Co."
                                class="w-full px-4 py-3 rounded-lg border-2 border-gray-600 bg-gray-700 text-gray-100 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:border-blue-400 transition"
                                required
                            >
                            @error('name')
                                <div class="mt-2 flex items-start gap-2 text-red-400">
                                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                    <span class="text-sm">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex gap-3 pt-6 border-t border-gray-700">
                            <a 
                                href="{{ route('suppliers.index') }}" 
                                class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gray-700 text-gray-300 font-semibold rounded-lg hover:bg-gray-600 transition"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                Cancel
                            </a>
                            <button 
                                type="submit" 
                                class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transition transform hover:scale-105 active:scale-95 shadow-lg"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Create Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="mt-6 bg-blue-900/30 border border-blue-800 rounded-xl p-6">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0zM8 9a1 1 0 100-2 1 1 0 000 2zm5-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"></path></svg>
                    <div>
                        <h4 class="font-semibold text-blue-300">Tip</h4>
                        <p class="text-sm text-blue-200 mt-1">After creating a supplier, you'll be able to add layups and layers to organize your data structure.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
