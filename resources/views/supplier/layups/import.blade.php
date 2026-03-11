<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Conflict Resolution') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium text-red-600 dark:text-red-400 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('Conflict Resolution') }} ({{ count($conflicts) }} {{ __('rows') }})
                    </h3>

                    <form action="{{ route('suppliers.layups.import.resolve', $supplier->id) }}" method="POST">
                        @csrf
                        @forelse($conflicts as $index => $conflict)
                            <input type="hidden" name="conflicts[{{ $index }}][layup_name]" value="{{ $conflict['layup_name'] }}">
                            <input type="hidden" name="conflicts[{{ $index }}][layer_id]" value="{{ $conflict['layer_id'] }}">
                            <input type="hidden" name="conflicts[{{ $index }}][order]" value="{{ $conflict['order'] }}">
                            <input type="hidden" name="conflicts[{{ $index }}][incoming][thickness]" value="{{ $conflict['incoming']['thickness'] }}">
                            <input type="hidden" name="conflicts[{{ $index }}][incoming][width]" value="{{ $conflict['incoming']['width'] }}">
                            <input type="hidden" name="conflicts[{{ $index }}][incoming][angle]" value="{{ $conflict['incoming']['angle'] }}">

                            <div class="mt-6 border rounded-lg overflow-hidden dark:border-gray-700">
                                <div class="bg-gray-100 dark:bg-gray-800 p-3 border-b dark:border-gray-700">
                                    Layup: <strong>{{ $conflict['layup_name'] }}</strong> | Layer Order: {{ $conflict['order'] }}
                                    <div class="mt-4 flex space-x-4">
                                        <label class="flex items-center">
                                            <input type="radio" name="conflicts[{{ $index }}][resolution]" value="keep" checked class="mr-2">
                                            <span class="text-sm">Keep Existing</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="conflicts[{{ $index }}][resolution]" value="overwrite" class="mr-2">
                                            <span class="text-sm text-blue-600 dark:text-blue-300">Overwrite with Incoming</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 p-4 bg-white dark:bg-gray-900">
                                    <div class="p-3 border rounded border-blue-200 bg-blue-50 dark:bg-blue-900/20">
                                        <span class="text-xs font-bold text-blue-600 uppercase">Existing Version</span>
                                        <ul class="mt-2 text-sm">
                                            <li>Thickness: {{ $conflict['existing']['thickness'] }}</li>
                                            <li>Width: {{ $conflict['existing']['width'] }}</li>
                                            <li>Angle: {{ $conflict['existing']['angle'] }}</li>
                                        </ul>
                                    </div>

                                    <div class="p-3 border rounded border-yellow-200 bg-yellow-50 dark:bg-yellow-900/20">
                                        <span class="text-xs font-bold text-yellow-600 dark:text-yellow-400 uppercase">Incoming Version (Imported)</span>
                                        <ul class="mt-2 text-sm">
                                            @foreach(['thickness', 'width', 'angle'] as $field)
                                                <li class="{{ $conflict['existing'][$field] != $conflict['incoming'][$field] ? 'text-red-600 dark:text-red-500 font-bold' : '' }}">
                                                    {{ ucfirst($field) }}: {{ $conflict['incoming'][$field] }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-4 text-sm text-center text-gray-500 italic">
                                {{ __('No conflicts found.') }}
                            </div>
                        @endforelse

                        <input type="hidden" name="clean_data" value="{{ json_encode($clean) }}">


                        <div class="mt-10 border-t pt-6 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-green-600 dark:text-green-400 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Ready to Import') }} ({{ count($clean) }} {{ __('rows') }})
                            </h3>

                            <div class="mt-4 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Layup</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Layer Order</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Thickness</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Width</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Angle</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($clean as $data)
                                            <tr>
                                                <td class="px-4 py-2 text-sm whitespace-nowrap">{{ $data['layup_name'] }}</td>
                                                <td class="px-4 py-2 text-sm text-center whitespace-nowrap">{{ $data['layer_order'] }}</td>
                                                <td class="px-4 py-2 text-sm text-center whitespace-nowrap">{{ $data['thickness'] }}</td>
                                                <td class="px-4 py-2 text-sm text-center whitespace-nowrap">{{ $data['width'] }}</td>
                                                <td class="px-4 py-2 text-sm text-center whitespace-nowrap">{{ $data['angle'] }}°</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-4 py-4 text-sm text-center text-gray-500 italic">
                                                    {{ __('No clean records found.') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-8 flex justify-end space-x-4">
                            <x-danger-button onclick="window.history.back()" type="button">
                                {{ __('Cancel') }}
                            </x-danger-button>

                            <x-primary-button>
                                {{ __('Confirm & Accept Incoming Data') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
