<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Layer') }} #{{ $layer->layer_order }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-2 text-sm">
                <p><strong>{{ __('Thickness') }}:</strong> {{ $layer->thickness }}</p>
                <p><strong>{{ __('Width') }}:</strong> {{ $layer->width }}</p>
                <p><strong>{{ __('Angle') }}:</strong> {{ $layer->angle }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('suppliers.layups.layers.edit', [$supplier, $layup, $layer]) }}">
                    <x-primary-button type="button">{{ __('Edit') }}</x-primary-button>
                </a>
                <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}">
                    <x-secondary-button type="button">{{ __('Back') }}</x-secondary-button>
                </a>
            </div>
            <form method="post" action="{{ route('suppliers.layups.layers.destroy', [$supplier, $layup, $layer]) }}" onsubmit="return confirm('Delete this layer?');">
                @csrf
                @method('delete')
                <x-danger-button type="submit">{{ __('Delete layer') }}</x-danger-button>
            </form>
        </div>
    </div>
</x-app-layout>
