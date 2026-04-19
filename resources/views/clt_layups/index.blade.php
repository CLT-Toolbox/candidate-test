<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <a href="{{ route('suppliers.show', $supplier) }}" class="text-indigo-600 dark:text-indigo-400">{{ $supplier->name }}</a>
                <span class="text-gray-400"> / </span> {{ __('Layups') }}
            </h2>
            <a href="{{ route('suppliers.layups.create', $supplier) }}">
                <x-primary-button type="button">{{ __('New layup') }}</x-primary-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-600 text-left">
                            <th class="py-2 pr-4">{{ __('Name') }}</th>
                            <th class="py-2 pr-4">{{ __('Layers') }}</th>
                            <th class="py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($layups as $layup)
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <td class="py-2 pr-4">{{ $layup->name }}</td>
                                <td class="py-2 pr-4">{{ $layup->clt_layers_count }}</td>
                                <td class="py-2 text-right">
                                    <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}" class="text-indigo-600 dark:text-indigo-400">{{ __('Open') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4">{{ __('No layups.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $layups->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
