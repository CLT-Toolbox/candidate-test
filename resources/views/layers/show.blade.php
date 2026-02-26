<x-app-layout>
<x-slot name="header">
    <div class="flex items-center gap-3">
        <a href="{{ route('dashboard.layups.show', $layer->layup) }}"
           class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Layer Details') }}
        </h2>
    </div>
</x-slot>

<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Layer Info Card -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-6">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 h-16 w-16 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                            <span class="text-purple-600 dark:text-purple-400 font-bold text-xl">
                                L{{ $layer->layer_order }}
                            </span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                                Layer #{{ $layer->layer_order }}
                            </h1>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Layup: {{ $layer->layup->name }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Layer ID</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">#{{ $layer->id }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Layer Specifications -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    📊 Layer Specifications
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Thickness</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($layer->thickness, 2) }} mm</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Width</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($layer->width, 2) }} mm</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Angle</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($layer->angle, 2) }}°</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Created</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $layer->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.layers.edit', $layer) }}"
               class="inline-flex items-center px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Layer
            </a>
            <a href="{{ route('dashboard.layups.show', $layer->layup) }}"
               class="inline-flex items-center px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-semibold rounded-lg transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Back to Layup
            </a>
        </div>
    </div>
</div>
</x-app-layout>