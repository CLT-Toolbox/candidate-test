<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Layers Managements') }}
        </h2>
    </x-slot>
<div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-3">
                        <a href="{{ route('suppliers.layups.index', $supplier->id) }}" class="inline-flex items-center text-sm text-gray-800 hover:text-gray-900 dark:text-gray-200 dark:hover:text-gray-300 hover:underline transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            {{ __('Back to Layups') }}
                        </a>
                    </div>
                    <div class="flex justify-between items-center mb-6">
                        <div class="">
                            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 pb-2">Layers List</h2>
                            <p>Supplier : {{ $supplier->name }}</p>
                            <p>Layup : {{ $layup->name }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-layer-modal')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                {{ __('Tambah') }}
                            </x-primary-button>
                        </div>
                        </div>
                    </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Layer Order
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        Thickness
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        Width
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center">
                                        Angle
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($layers as $layer)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $layer->layer_order }}
                                        </th>
                                        <td class="px-6 py-4 text-center">
                                            {{ $layer->thickness }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            {{ $layer->width }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            {{ $layer->angle }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-end items-center space-x-2">
                                                <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'edit-layer-modal-{{ $layer->id }}')" class="text-blue-600 dark:text-blue-500 hover:text-blue-900 dark:hover:text-blue-400" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                 Edit
                                            </x-secondary-button>
                                            <x-secondary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-layer-modal-{{ $layer->id }}')" class="text-red-600 dark:text-red-500 hover:text-red-900 dark:hover:text-red-400" title="Delete">
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
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No layers found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $layers->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <x-modal name="add-layer-modal" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('suppliers.layups.layers.store', [$supplier->id, $layup->id]) }}" class="p-6">
            @csrf
            @method('post')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Tambah Layer Baru') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Masukkan data untuk layer baru.') }}
            </p>

            {{-- <div class="mt-6">
                <x-input-label for="layer_order" value="{{ __('Layer order') }}" class="sr-only" />
                <x-text-input
                    id="layer_order"
                    type="number"
                    name="layer_order"
                    class="mt-1 block w-full"
                    placeholder="{{ __('Layer Order') }}"
                />
                <x-input-error :messages="$errors->get('layer_order')" class="mt-2" />
            </div> --}}
            <div class="mt-6">
                <x-input-label for="thickness" value="{{ __('Thickness') }}" class="sr-only" />
                <x-text-input
                    id="thickness"
                    type="number"
                    name="thickness"
                    class="mt-1 block w-full"
                    placeholder="{{ __('Thickness') }}"
                />
                <x-input-error :messages="$errors->get('thickness')" class="mt-2" />
            </div>
            <div class="mt-6">
                <x-input-label for="width" value="{{ __('Width') }}" class="sr-only" />
                <x-text-input
                    id="width"
                    type="number"
                    name="width"
                    class="mt-1 block w-full"
                    placeholder="{{ __('Width') }}"
                />
                <x-input-error :messages="$errors->get('width')" class="mt-2" />
            </div>
            <div class="mt-6">
                <x-input-label for="angle" value="{{ __('Angle') }}" class="sr-only" />
                <x-text-input
                    id="angle"
                    type="number"
                    name="angle"
                    class="mt-1 block w-full"
                    placeholder="{{ __('Angle') }}"
                />
                <x-input-error :messages="$errors->get('angle')" class="mt-2" />
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

    @foreach ($layers as $layer)
    <!-- Edit Modal -->
    <x-modal name="edit-layer-modal-{{ $layer->id }}" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('suppliers.layups.layers.update', [$supplier->id, $layup->id, $layer->id]) }}" class="p-6">
            @csrf
            @method('put')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Edit Layup') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Perbarui data layup.') }}
            </p>

            <div class="mt-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Layer Order : ') }} {{ $layer->layer_order }}
                </p>
            </div>
            <div class="mt-6">
                <x-input-label for="thickness" value="{{ __('Thickness') }}" class="sr-only" />
                <x-text-input
                    id="thickness"
                    type="number"
                    name="thickness"
                    class="mt-1 block w-full"
                    :value="old('thickness', $layer->thickness)"
                    placeholder="{{ __('Thickness') }}"
                />
                <x-input-error :messages="$errors->get('thickness')" class="mt-2" />
            </div>
            <div class="mt-6">
                <x-input-label for="width" value="{{ __('Width') }}" class="sr-only" />
                <x-text-input
                    id="width"
                    type="number"
                    name="width"
                    class="mt-1 block w-full"
                    :value="old('width', $layer->width)"
                    placeholder="{{ __('Width') }}"
                />
                <x-input-error :messages="$errors->get('width')" class="mt-2" />
            </div>
            <div class="mt-6">
                <x-input-label for="angle" value="{{ __('Angle') }}" class="sr-only" />
                <x-text-input
                    id="angle"
                    type="number"
                    name="angle"
                    class="mt-1 block w-full"
                    :value="old('angle', $layer->angle)"
                    placeholder="{{ __('Angle') }}"
                />
                <x-input-error :messages="$errors->get('angle')" class="mt-2" />
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
     <x-modal name="delete-layer-modal-{{ $layer->id }}" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('suppliers.layups.layers.destroy', [$supplier->id, $layup->id, $layer->id]) }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Apakah Anda yakin ingin menghapus layer ini?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Setelah layer dihapus, semua sumber daya dan datanya akan dihapus secara permanen.') }}
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button class="ml-3">
                    {{ __('Hapus Layer') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
    @endforeach
</x-app-layout>
