<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.suppliers.show', $supplier) }}" 
               class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Add New Layup') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Info Card --}}
            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 mb-6 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-green-800 dark:text-green-300">
                            Create New Layup
                        </h3>
                        <p class="text-sm text-green-700 dark:text-green-400 mt-1">
                            Add a new layup to <strong>{{ $supplier->name }}</strong>. 
                            You can add layers after creating the layup.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Error Messages --}}
            @if($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
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

            {{-- Create Form Card --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <form action="{{ route('dashboard.suppliers.layups.store', $supplier) }}" method="POST">
                        @csrf

                        {{-- Supplier Info Card --}}
                        <div class="flex items-center gap-4 mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0 h-16 w-16 bg-blue-100 dark:bg-blue-900/30 
                                        rounded-full flex items-center justify-center">
                                <span class="text-blue-600 dark:text-blue-400 font-bold text-xl">
                                    {{ substr($supplier->name, 0, 2) }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Parent Supplier</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $supplier->name }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Supplier ID</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    #{{ $supplier->id }}
                                </p>
                            </div>
                        </div>

                        {{-- Form Field --}}
                        <div class="mb-6">
                            <label class="flex items-center text-sm font-semibold 
                                          text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-5 h-5 mr-2 text-gray-400" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Layup Name
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="text"
                                   name="name"
                                   required
                                   placeholder="e.g., Carbon Fiber Layup 001"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 
                                          rounded-lg focus:ring-2 focus:ring-green-500 
                                          focus:border-green-500 dark:bg-gray-700 dark:text-white 
                                          transition-colors"
                            >
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-1">
                                This name will be displayed throughout the system
                            </p>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 bg-green-600 
                                           hover:bg-green-700 text-white font-semibold rounded-lg 
                                           transition duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" 
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                                Create Layup
                            </button>
                            <a href="{{ route('dashboard.suppliers.show', $supplier) }}" 
                               class="inline-flex items-center px-6 py-3 bg-gray-200 
                                      dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 
                                      text-gray-800 dark:text-gray-200 font-semibold rounded-lg 
                                      transition duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" 
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>