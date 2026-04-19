<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('New layer') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="post" action="{{ route('suppliers.layups.layers.store', [$supplier, $layup]) }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="layer_order" :value="__('Layer order')" />
                        <x-text-input id="layer_order" name="layer_order" type="number" min="0" class="mt-1 block w-full" :value="old('layer_order')" required />
                        <x-input-error :messages="$errors->get('layer_order')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="thickness" :value="__('Thickness')" />
                        <x-text-input id="thickness" name="thickness" type="text" class="mt-1 block w-full" :value="old('thickness')" required />
                        <x-input-error :messages="$errors->get('thickness')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="width" :value="__('Width')" />
                        <x-text-input id="width" name="width" type="text" class="mt-1 block w-full" :value="old('width')" required />
                    </div>
                    <div>
                        <x-input-label for="angle" :value="__('Angle (°)')" />
                        <x-text-input id="angle" name="angle" type="text" class="mt-1 block w-full" :value="old('angle', '0')" required />
                    </div>
                    <div class="flex gap-2">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                        <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}"><x-secondary-button type="button">{{ __('Cancel') }}</x-secondary-button></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
