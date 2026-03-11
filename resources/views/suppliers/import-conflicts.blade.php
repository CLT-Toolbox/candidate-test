<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Resolve Import Conflicts') }}
                </h2>
                <p class="text-l text-gray-800 dark:text-gray-200 leading-text">
                    {{ $total_conflicts }} {{ __('conflict(s) need your attention') }}
                </p>
            </div>
            <a href="{{ route('suppliers.show', $supplier) }}" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('suppliers.import.resolve', $supplier) }}" class="space-y-6">
                @csrf

                @foreach ($conflicts as $index => $conflict)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold mb-2">
                                {{ __('Conflict') }} {{ $index + 1 }} {{ __('of') }} {{ $total_conflicts }}:
                                <span class="text-blue-600 dark:text-blue-400">{{ $conflict['layup_name'] }}</span>
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ count($conflict['layer_conflicts']) }} {{ __('layer(s) have conflicting data') }}
                            </p>
                        </div>

                        <div class="p-6 space-y-6">
                            @foreach ($conflict['layer_conflicts'] as $layerConflict)
                                <div class="border border-yellow-200 dark:border-yellow-800 bg-yellow-50 dark:bg-yellow-900/10 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">
                                        {{ __('Layer') }} #{{ $layerConflict['layer_order'] }}
                                    </h4>

                                    <div class="grid grid-cols-2 gap-4">
                                        @foreach ($layerConflict['differences'] as $field => $values)
                                            <div class="space-y-2">
                                                <h5 class="font-medium text-sm text-gray-700 dark:text-gray-300">{{ ucfirst($field) }}</h5>

                                                <div class="bg-white dark:bg-gray-800 rounded p-3">
                                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('Current') }}:</p>
                                                    <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                                        {{ $values['existing'] }}
                                                    </p>
                                                </div>

                                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded p-3">
                                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('Incoming') }}:</p>
                                                    <p class="text-lg font-semibold text-blue-700 dark:text-blue-400">
                                                        {{ $values['incoming'] }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                                <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-3">{{ __('Resolution Strategy') }}</h4>
                                <div class="space-y-2">
                                    <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer">
                                        <input
                                            type="radio"
                                            name="resolution[{{ $conflict['layup_index'] }}]"
                                            value="skip"
                                            checked
                                            class="w-4 h-4 text-gray-600 dark:text-gray-400"
                                        />
                                        <span class="ml-3">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('Skip Conflict') }}</span>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Keep current data, ignore incoming changes') }}</p>
                                        </span>
                                    </label>

                                    <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer">
                                        <input
                                            type="radio"
                                            name="resolution[{{ $conflict['layup_index'] }}]"
                                            value="overwrite"
                                            class="w-4 h-4 text-blue-600 dark:text-blue-400"
                                        />
                                        <span class="ml-3">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('Overwrite Existing') }}</span>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Replace current data with incoming data') }}</p>
                                        </span>
                                    </label>

                                    <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer">
                                        <input
                                            type="radio"
                                            name="resolution[{{ $conflict['layup_index'] }}]"
                                            value="duplicate"
                                            class="w-4 h-4 text-green-600 dark:text-green-400"
                                        />
                                        <span class="ml-3">
                                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('Create Duplicate') }}</span>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Create a new layup with "(imported)" suffix') }}</p>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="flex gap-3 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        {{ __('Continue with Import') }}
                    </button>
                    <a href="{{ route('suppliers.show', $supplier) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
