<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Suppliers') }}</h2>
            <a href="{{ route('suppliers.create') }}">
                <x-primary-button type="button">{{ __('New supplier') }}</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <p class="mb-4 text-green-600 dark:text-green-400">{{ session('status') }}</p>
            @endif
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-600 text-left">
                                <th class="py-2 pr-4">{{ __('Name') }}</th>
                                <th class="py-2 pr-4">{{ __('Code') }}</th>
                                <th class="py-2 pr-4">{{ __('Layups') }}</th>
                                <th class="py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($suppliers as $supplier)
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <td class="py-2 pr-4">{{ $supplier->name }}</td>
                                    <td class="py-2 pr-4">{{ $supplier->code ?? '—' }}</td>
                                    <td class="py-2 pr-4">{{ $supplier->clt_layups_count }}</td>
                                    <td class="py-2 text-right space-x-2">
                                        <a href="{{ route('suppliers.show', $supplier) }}" class="text-indigo-600 dark:text-indigo-400">{{ __('View') }}</a>
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="text-indigo-600 dark:text-indigo-400">{{ __('Edit') }}</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-4">{{ __('No suppliers yet.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $suppliers->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
