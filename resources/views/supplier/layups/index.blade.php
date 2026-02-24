<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Layups Managements') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-3">
                        <a href="{{ route('suppliers.index') }}" class="inline-flex items-center text-sm text-gray-800 hover:text-gray-900 dark:text-gray-200 dark:hover:text-gray-300 hover:underline transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            {{ __('Back to Layups') }}
                        </a>
                    </div>
                    <div class="flex justify-between items-center mb-6">
                        <div class="">
                            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 pb-2">Layups List</h2>
                            <p>Supplier : {{ $supplier->name }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-layup-modal')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                {{ __('Tambah') }}
                            </x-primary-button>
                            <a href="{{ route('suppliers.layups.export', [$supplier->id]) }}">
                                <x-secondary-button>
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    {{ __('Export') }}
                                </x-secondary-button>
                            </a>
                        </div>
                        </div>
                    </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        CLT Layers Count
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($layups as $layup)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $layup->name }}
                                        </th>
                                        <td class="px-6 py-4 text-center">
                                            {{ $layup->clt_layers_count }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-end items-center space-x-2">
                                                <a href="{{ route('suppliers.layups.layers.index', [$supplier->id, $layup->id]) }}">
                                                    <x-primary-button>
                                                        {{ __('Layers') }}
                                                    </x-primary-button>
                                                </a>
                                                <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-layup-modal-{{ $layup->id }}')" class="text-blue-600 dark:text-blue-500 hover:text-blue-900 dark:hover:text-blue-400" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                 Edit
                                            </x-secondary-button>
                                            <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-layup-modal-{{ $layup->id }}')" class="text-red-600 dark:text-red-500 hover:text-red-900 dark:hover:text-red-400" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                 Delete
                                            </x-secondary-button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No layups found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $layups->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <x-modal name="add-layup-modal" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('suppliers.layups.store', $supplier->id) }}" class="p-6">
            @csrf
            @method('post')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Tambah Layup Baru') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Masukkan nama untuk layup baru.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="name" value="{{ __('Name') }}" class="sr-only" />
                <x-text-input
                    id="name"
                    name="name"
                    class="mt-1 block w-full"
                    placeholder="{{ __('Name') }}"
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Simpan') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    @foreach ($layups as $layup)
    <!-- Edit Modal -->
    <x-modal name="edit-layup-modal-{{ $layup->id }}" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('suppliers.layups.update', [$supplier->id, $layup->id]) }}" class="p-6">
            @csrf
            @method('patch')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Edit Layup') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Perbarui nama layup.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="name" value="{{ __('Name') }}" class="sr-only" />

                <x-text-input
                    id="name-{{$layup->id}}"
                    name="name"
                    class="mt-1 block w-full"
                    :value="old('name', $layup->name)"
                    required
                    autofocus
                    placeholder="{{ __('Name') }}"
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-primary-button class="ml-3">
                    {{ __('Simpan') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <!-- Delete Modal -->
     <x-modal name="delete-layup-modal-{{ $layup->id }}" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('suppliers.layups.destroy', [$supplier->id, $layup->id]) }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Apakah Anda yakin ingin menghapus layup ini?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Setelah layup dihapus, semua sumber daya dan datanya akan dihapus secara permanen.') }}
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button class="ml-3">
                    {{ __('Hapus Supplier') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
    @endforeach
</x-app-layout>
