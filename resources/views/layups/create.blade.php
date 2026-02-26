<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.suppliers.show', $supplier) }}"
               class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Add New Layup') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Info Banner -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-l-4 border-green-500 p-5 mb-8 rounded-r-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-green-800 dark:text-green-300">Create New Layup</h3>
                        <p class="text-sm text-green-700 dark:text-green-400 mt-1">
                            Add a new layup to <strong class="font-medium">{{ $supplier->name }}</strong>. 
                            You can add layers after creating the layup.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
            <div class="bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border-l-4 border-red-500 p-5 mb-8 rounded-r-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-red-800 dark:text-red-300 mb-2">
                            {{ $errors->count() }} error(s) found
                        </h3>
                        <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400 space-y-1">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Form Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl mb-6">
                <!-- Card Header -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        📝 Layup Information
                    </h3>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('dashboard.suppliers.layups.store', $supplier) }}" method="POST">
                        @csrf

                        <!-- Supplier Info -->
                        <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl mb-6">
                            <div class="flex-shrink-0 h-14 w-14 bg-blue-600 dark:bg-blue-500 rounded-full flex items-center justify-center shadow-md">
                                <span class="text-white font-bold text-lg">
                                    {{ substr(strtoupper($supplier->name), 0, 2) }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Parent Supplier</p>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $supplier->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">Supplier ID</p>
                                <p class="text-base font-mono font-medium text-gray-900 dark:text-white">#{{ str_pad($supplier->id, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>

                        <!-- Form Field -->
                        <div class="mb-8">
                            <label class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Layup Name
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   required
                                   placeholder="e.g., Carbon Fiber Layup 001"
                                   class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-500"
                            >
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 ml-1">
                                💡 This name will be displayed throughout the system
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Create Layup
                            </button>
                            <a href="{{ route('dashboard.suppliers.show', $supplier) }}"
                               class="inline-flex items-center px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-xl transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Tips Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        💡 Quick Tips
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex items-start gap-3 p-3 bg-green-50 dark:bg-green-900/10 rounded-lg">
                            <span class="text-green-600 dark:text-green-400 font-bold flex-shrink-0">✓</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Choose a clear, descriptive name for easy identification</span>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-blue-50 dark:bg-blue-900/10 rounded-lg">
                            <span class="text-blue-600 dark:text-blue-400 font-bold flex-shrink-0">✓</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">You can always edit the layup details later</span>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-purple-50 dark:bg-purple-900/10 rounded-lg">
                            <span class="text-purple-600 dark:text-purple-400 font-bold flex-shrink-0">✓</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Add layers after creating the layup</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>