<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Import JSON') }} — {{ $supplier->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                    {{ __('Paste JSON with structure: { "layups": [ { "name", "description", "layers": [ { "layer_order", "thickness", "width", "angle" } ] } ] }. Strategy applies to layup/layer conflicts per skill test readme.') }}
                </p>

                @if ($errors->has('import'))
                    <p class="text-red-600 dark:text-red-400 mb-2">{{ $errors->first('import') }}</p>
                @endif

                @if (session('conflicts'))
                    <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 rounded text-sm overflow-x-auto">
                        <pre class="whitespace-pre-wrap">{{ json_encode(session('conflicts'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                @endif

                <form method="post" action="{{ route('suppliers.import', $supplier) }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="strategy" :value="__('Conflict strategy')" />
                        <select id="strategy" name="strategy" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required>
                            <option value="overwrite" @selected(old('strategy') === 'overwrite')>{{ __('Overwrite existing') }}</option>
                            <option value="skip" @selected(old('strategy') === 'skip')>{{ __('Skip conflicting layers') }}</option>
                            <option value="duplicate" @selected(old('strategy') === 'duplicate')>{{ __('Duplicate layup (imported)') }}</option>
                            <option value="reject" @selected(old('strategy') === 'reject')>{{ __('Reject entire import on conflict') }}</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="json_payload" :value="__('JSON payload')" />
                        <textarea id="json_payload" name="json_payload" rows="16" class="mt-1 block w-full font-mono text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm" required>{{ old('json_payload', '{"layups":[]}') }}</textarea>
                        <x-input-error :messages="$errors->get('json_payload')" class="mt-2" />
                        <x-input-error :messages="$errors->get('payload')" class="mt-2" />
                    </div>
                    <div class="flex gap-2">
                        <x-primary-button>{{ __('Run import') }}</x-primary-button>
                        <a href="{{ route('suppliers.show', $supplier) }}">
                            <x-secondary-button type="button">{{ __('Cancel') }}</x-secondary-button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
