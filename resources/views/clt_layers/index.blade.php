<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Layers') }} — {{ $layup->name }}
            </h2>
            <a href="{{ route('suppliers.layups.layers.create', [$supplier, $layup]) }}">
                <x-primary-button type="button">{{ __('New layer') }}</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-600 text-left">
                            <th class="py-2 pr-4">#</th>
                            <th class="py-2 pr-4">{{ __('Thickness') }}</th>
                            <th class="py-2 pr-4">{{ __('Width') }}</th>
                            <th class="py-2 pr-4">{{ __('Angle') }}</th>
                            <th class="py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($layers as $layer)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2 pr-4">{{ $layer->layer_order }}</td>
                                <td class="py-2 pr-4">{{ $layer->thickness }}</td>
                                <td class="py-2 pr-4">{{ $layer->width }}</td>
                                <td class="py-2 pr-4">{{ $layer->angle }}</td>
                                <td class="py-2 text-right">
                                    <a href="{{ route('suppliers.layups.layers.show', [$supplier, $layup, $layer]) }}" class="text-indigo-600 dark:text-indigo-400">{{ __('View') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4">{{ __('No layers.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $layers->links() }}</div>
                <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}" class="text-indigo-600 dark:text-indigo-400 text-sm">{{ __('← Back to layup') }}</a>
            </div>
        </div>
    </div>
</x-app-layout>
