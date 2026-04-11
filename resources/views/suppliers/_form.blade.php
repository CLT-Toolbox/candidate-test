@csrf

<div class="space-y-6">
    <div>
        <x-input-label for="name" value="Supplier name" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $supplier->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ isset($supplier) ? route('suppliers.show', $supplier) : route('suppliers.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
            Cancel
        </a>
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
    </div>
</div>
