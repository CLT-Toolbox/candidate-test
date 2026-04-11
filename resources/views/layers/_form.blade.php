@csrf

<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <x-input-label for="layer_order" value="Layer order" />
        <x-text-input id="layer_order" name="layer_order" type="number" min="1" class="mt-1 block w-full" :value="old('layer_order', $layer->layer_order ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('layer_order')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="angle" value="Angle" />
        <x-text-input id="angle" name="angle" type="number" step="0.01" class="mt-1 block w-full" :value="old('angle', $layer->angle ?? '')" required />
        <x-input-error :messages="$errors->get('angle')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="thickness" value="Thickness" />
        <x-text-input id="thickness" name="thickness" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('thickness', $layer->thickness ?? '')" required />
        <x-input-error :messages="$errors->get('thickness')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="width" value="Width" />
        <x-text-input id="width" name="width" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('width', $layer->width ?? '')" required />
        <x-input-error :messages="$errors->get('width')" class="mt-2" />
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('suppliers.show', $supplier) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
        Cancel
    </a>
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
</div>
