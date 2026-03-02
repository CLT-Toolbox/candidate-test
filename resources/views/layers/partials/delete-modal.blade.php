{{-- Delete Confirmation Modal --}}
<x-modal name="delete-confirmation" :show="false" focusable>
    <div class="bg-white px-6 py-4 dark:bg-gray-800">
        <div class="flex items-start gap-4">
            {{-- Icon Warning --}}
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('Delete Layer') }}
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Are you sure you want to delete Layer #') }}<span class="font-semibold text-gray-900 dark:text-gray-100" x-text="deleteData.layer_order"></span>?
                </p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-500">
                    {{ __('This action cannot be undone. All layers below this will be automatically reordered.') }}
                </p>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
        <button type="button" @click="closeDeleteModal()"
            class="rounded-lg border border-gray-300 px-4 py-2 font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
            {{ __('Cancel') }}
        </button>
        <button type="button" @click="confirmDelete()"
            class="rounded-lg bg-red-600 px-4 py-2 font-medium text-white transition hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700">
            {{ __('Delete') }}
        </button>
    </div>
</x-modal>

{{-- Hidden Delete Form --}}
<form id="delete-form" method="POST" :action="deleteAction" style="display: none;">
    @csrf
    @method('DELETE')
</form>
