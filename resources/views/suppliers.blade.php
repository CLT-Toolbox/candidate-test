<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Suppliers') }}
                </h2>
                <p class="text-l text-gray-800 dark:text-gray-200 leading-text">
                    {{ __('Manage timber suppliers and materials sourcing') }}
                </p>
            </div>
            <x-link-button href="{{ route('suppliers.create') }}" class="mt-4">
                <span class="material-symbols-outlined text-xl" style="margin-right: 8px">add</span>
                {{ __('Add New Supplier') }}
            </x-link-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <x-success-message :message="session('success')" />
            @endif
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="text-gray-900 dark:text-gray-100">
                    <table class="table-auto w-full border-collapse">
                        <thead class="bg-gray-200 dark:bg-gray-700 rounded-xl">
                            <tr>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Total Layups</th>
                                <th class="px-4 py-2">Created At</th>
                                <th class="px-4 py-2">Updated At</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800">
                            @forelse ($suppliers as $supplier)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="border-b border-gray-200 dark:border-gray-700 py-4 px-4 flex items-center gap-3">
                                        <div class="flex w-full items-center">
                                            @php
                                                $colors = ['bg-red-600', 'bg-blue-600', 'bg-green-600', 'bg-yellow-600', 'bg-purple-600', 'bg-pink-600', 'bg-indigo-600', 'bg-teal-600'];
                                                $randomColor = $colors[array_rand($colors)];
                                            @endphp
                                            <div class="w-8 h-8 rounded-full {{ $randomColor }} mr-2 text-center flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ strtoupper(substr($supplier->name, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div class="flex flex-col">
                                                <p class="font-semibold">{{ $supplier->name }}</p>
                                                <span class="text-sm text-gray-500 dark:text-gray-400">ID: SUP-{{ $supplier->created_at->format("Y") }}-{{ str_pad($supplier->id, 3, '0', STR_PAD_LEFT) }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="border-b border-gray-200 dark:border-gray-700 py-4 text-center">{{ $supplier->layups->count() }}</td>
                                    <td class="border-b border-gray-200 dark:border-gray-700 py-4 text-center">{{ $supplier->created_at }}</td>
                                    <td class="border-b border-gray-200 dark:border-gray-700 py-4 text-center">{{ $supplier->updated_at }}</td>
                                    <td class="border-b border-gray-200 dark:border-gray-700 py-4 text-center">
                                        <x-link-button href="{{ route('suppliers.show', $supplier) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded-md text-sm">
                                            {{ __('View') }}
                                        </x-link-button>

                                        <x-link-button href="{{ route('suppliers.edit', $supplier) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm">
                                            {{ __('Edit') }}
                                        </x-link-button>

                                        {{-- <x-link-button href="{{ route('suppliers.export', $supplier) }}" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-sm" download>
                                            {{ __('Export') }}
                                        </x-link-button>

                                        <x-link-button href="{{ route('suppliers.import.form', $supplier) }}" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded-md text-sm">
                                            {{ __('Import') }}
                                        </x-link-button> --}}

                                        <x-danger-button
                                            x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'confirm-supplier-deletion')"
                                        >
                                            {{ __('Delete') }}
                                        </x-danger-button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 px-4 text-center">No suppliers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($suppliers->hasPages())
                        <div class="px-3 py-4">
                            {{ $suppliers->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(isset($supplier))
        <x-modal name="confirm-supplier-deletion" :show="$errors->supplierDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ route('suppliers.destroy', $supplier) }}" class="p-6">
                @csrf
                @method('delete')

                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Are you sure you want to delete this supplier?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-danger-button class="ms-3">
                        {{ __('Delete Supplier') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    @endif
</x-app-layout>
