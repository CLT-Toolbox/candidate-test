<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.layups.show', $layup) }}"
               class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Add New Layer') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Info Card -->
            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 mb-6 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-green-800 dark:text-green-300">Create New Layer</h3>
                        <p class="text-sm text-green-700 dark:text-green-400 mt-1">
                            Add a new layer to <strong>{{ $layup->name }}</strong>. Define the order, thickness, width, and angle.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            <!-- Create Form -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <form action="{{ route('dashboard.layups.layers.store', $layup) }}" method="POST">
                        @csrf

                        <!-- Layup Info Card -->
                        <div class="flex items-center gap-4 mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex-shrink-0 h-16 w-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                <span class="text-green-600 dark:text-green-400 font-bold text-xl">
                                    {{ substr($layup->name, 0, 2) }}
                                </span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Parent Layup</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $layup->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Layup ID</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">#{{ $layup->id }}</p>
                            </div>
                        </div>

                        <!-- Form Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <!-- Layer Order -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                    </svg>
                                    Layer Order
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="number"
                                       name="layer_order"
                                       required
                                       min="1"
                                       placeholder="e.g., 1"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors"
                                >
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Sequential order of this layer
                                </p>
                            </div>

                            <!-- Thickness -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                    </svg>
                                    Thickness (mm)
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="number"
                                       name="thickness"
                                       required
                                       step="0.01"
                                       min="0"
                                       placeholder="e.g., 5.00"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors"
                                >
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Layer thickness in millimeters
                                </p>
                            </div>

                            <!-- Width -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                    </svg>
                                    Width (mm)
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="number"
                                       name="width"
                                       required
                                       step="0.01"
                                       min="0"
                                       placeholder="e.g., 100.00"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors"
                                >
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Layer width in millimeters
                                </p>
                            </div>

                            <!-- Angle -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Angle (°)
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input type="number"
                                       name="angle"
                                       required
                                       step="0.01"
                                       min="0"
                                       max="360"
                                       placeholder="e.g., 45.00"
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors"
                                >
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Fiber orientation angle (0-360°)
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Create Layer
                            </button>
                            <a href="{{ route('dashboard.layups.show', $layup) }}"
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

            <!-- Quick Tips -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        💡 Quick Tips
                    </h3>
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-start">
                            <span class="text-green-600 mr-2">✓</span>
                            <span>Layer order determines the stacking sequence</span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-green-600 mr-2">✓</span>
                            <span>Angle affects the mechanical properties of the layup</span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-green-600 mr-2">✓</span>
                            <span>Common angles: 0°, 45°, 90°, -45°</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>