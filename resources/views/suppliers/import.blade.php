<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Import Supplier') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Info Card -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 mb-6 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-300">Import Information</h3>
                        <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                            Upload a JSON file exported from this system. The file should contain supplier data with layups and layers.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            <!-- Import Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <form action="{{ route('dashboard.suppliers.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- File Upload -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Upload JSON File
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                        <label for="file-upload" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-blue-600 dark:text-blue-400 hover:text-blue-500 focus-within:outline-none">
                                            <span>Upload a file</span>
                                            <input id="file-upload" name="file" type="file" accept=".json,.txt" required class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        JSON, TXT up to 10MB
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Conflict Strategy -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                                Conflict Resolution Strategy
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <label class="relative flex items-start p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                                    <input type="radio" name="conflict_strategy" value="overwrite" required class="h-4 w-4 text-blue-600 mt-1">
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900 dark:text-white">Overwrite</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">Replace existing data</span>
                                    </div>
                                </label>
                                
                                <label class="relative flex items-start p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                                    <input type="radio" name="conflict_strategy" value="skip" required class="h-4 w-4 text-blue-600 mt-1">
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900 dark:text-white">Skip</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">Keep current data</span>
                                    </div>
                                </label>
                                
                                <label class="relative flex items-start p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                                    <input type="radio" name="conflict_strategy" value="duplicate" required class="h-4 w-4 text-blue-600 mt-1">
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900 dark:text-white">Duplicate</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">Create new with suffix</span>
                                    </div>
                                </label>
                                
                                <label class="relative flex items-start p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                                    <input type="radio" name="conflict_strategy" value="reject" required class="h-4 w-4 text-blue-600 mt-1">
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900 dark:text-white">Reject</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400">Abort on conflict</span>
                                    </div>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                💡 Choose how to handle conflicts when imported data differs from existing records.
                            </p>
                        </div>

                        <!-- Strategy Info Box -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Strategy Comparison</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                <div class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span class="text-gray-600 dark:text-gray-400"><strong>Overwrite:</strong> Best for updating existing data</span>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-blue-600 mr-2">✓</span>
                                    <span class="text-gray-600 dark:text-gray-400"><strong>Skip:</strong> Best for preserving current data</span>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-purple-600 mr-2">✓</span>
                                    <span class="text-gray-600 dark:text-gray-400"><strong>Duplicate:</strong> Best for keeping both versions</span>
                                </div>
                                <div class="flex items-start">
                                    <span class="text-red-600 mr-2">✓</span>
                                    <span class="text-gray-600 dark:text-gray-400"><strong>Reject:</strong> Best for strict data integrity</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-3">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Import Data
                            </button>
                            
                            <a href="{{ route('dashboard.suppliers.index') }}" 
                               class="inline-flex items-center px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold rounded-lg transition duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sample JSON Format -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                        📄 Sample JSON Format
                    </h3>
                    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-xs"><code>{
  "supplier": {
    "name": "Example Supplier"
  },
  "layups": [
    {
      "name": "Layup 1",
      "layers": [
        {
          "layer_order": 1,
          "thickness": 5.00,
          "width": 100.00,
          "angle": 45.00
        }
      ]
    }
  ]
}</code></pre>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>