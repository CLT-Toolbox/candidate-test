<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $supplier->name }}</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('suppliers.layups.create', $supplier) }}">
                    <x-primary-button type="button">{{ __('New layup') }}</x-primary-button>
                </a>
                <a href="{{ route('suppliers.export', $supplier) }}">
                    <x-secondary-button type="button">{{ __('Export JSON') }}</x-secondary-button>
                </a>
                <a href="{{ route('suppliers.import.form', $supplier) }}">
                    <x-secondary-button type="button">{{ __('Import') }}</x-secondary-button>
                </a>
                <a href="{{ route('suppliers.edit', $supplier) }}">
                    <x-secondary-button type="button">{{ __('Edit supplier') }}</x-secondary-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <p class="text-green-600 dark:text-green-400">{{ session('status') }}</p>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 dark:text-gray-300"><strong>{{ __('Code') }}:</strong> {{ $supplier->code ?? '—' }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-2"><strong>{{ __('Notes') }}:</strong> {{ $supplier->notes ?? '—' }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">{{ __('CLT Layups') }}</h3>
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-600 text-left">
                                <th class="py-2 pr-4">{{ __('Name') }}</th>
                                <th class="py-2 pr-4">{{ __('Layers') }}</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($supplier->cltLayups as $layup)
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <td class="py-2 pr-4">{{ $layup->name }}</td>
                                    <td class="py-2 pr-4">{{ $layup->clt_layers_count }}</td>
                                    <td class="py-2 text-right">
                                        <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}" class="text-indigo-600 dark:text-indigo-400">{{ __('Open') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-4">{{ __('No layups yet.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <form method="post" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Delete this supplier and all layups/layers?');">
                @csrf
                @method('delete')
                <x-danger-button type="submit">{{ __('Delete supplier') }}</x-danger-button>
            </form>
        </div>
    </div>
</x-app-layout>
