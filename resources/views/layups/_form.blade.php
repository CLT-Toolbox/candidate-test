@csrf

<div class="space-y-6">
    <div>
        <x-input-label for="name" value="Layup name" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $layup->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('suppliers.show', $supplier) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
            Cancel
        </a>
        <x-primary-button>{{ $submitLabel }}</x-primary-button>
    </div>
</div>
