<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $layup->name }}</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('suppliers.layups.layers.create', [$supplier, $layup]) }}">
                    <x-primary-button type="button">{{ __('New layer') }}</x-primary-button>
                </a>
                <a href="{{ route('suppliers.layups.layers.index', [$supplier, $layup]) }}">
                    <x-secondary-button type="button">{{ __('List layers') }}</x-secondary-button>
                </a>
                <a href="{{ route('suppliers.layups.edit', [$supplier, $layup]) }}">
                    <x-secondary-button type="button">{{ __('Edit layup') }}</x-secondary-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <p class="text-green-600 dark:text-green-400">{{ session('status') }}</p>
            @endif

            <p class="text-sm text-gray-600 dark:text-gray-300"><a href="{{ route('suppliers.show', $supplier) }}" class="text-indigo-600">{{ $supplier->name }}</a></p>
            <p class="text-sm">{{ $layup->description ?? '—' }}</p>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">{{ __('Layers') }}</h3>
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
                        @forelse ($layup->cltLayers as $layer)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2 pr-4">{{ $layer->layer_order }}</td>
                                <td class="py-2 pr-4">{{ $layer->thickness }}</td>
                                <td class="py-2 pr-4">{{ $layer->width }}</td>
                                <td class="py-2 pr-4">{{ $layer->angle }}</td>
                                <td class="py-2 text-right">
                                    <a href="{{ route('suppliers.layups.layers.edit', [$supplier, $layup, $layer]) }}" class="text-indigo-600 dark:text-indigo-400">{{ __('Edit') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-4">{{ __('No layers yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <form method="post" action="{{ route('suppliers.layups.destroy', [$supplier, $layup]) }}" onsubmit="return confirm('Delete this layup and all layers?');">
                @csrf
                @method('delete')
                <x-danger-button type="submit">{{ __('Delete layup') }}</x-danger-button>
            </form>
        </div>
    </div>
</x-app-layout>
