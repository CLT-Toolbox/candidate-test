{{-- Layup Form Modal (Create/Edit) --}}
<x-modal name="layup-form" :show="false" focusable>
    <div class="border-b border-gray-100 bg-white px-6 py-4 dark:border-gray-700 dark:bg-gray-800">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            <span x-show="!isEditing">{{ __('Add New Layup') }}</span>
            <span x-show="isEditing">{{ __('Edit Layup') }}</span>
        </h2>
    </div>

    <div class="bg-white px-6 py-4 dark:bg-gray-800">
        <form id="layup-form" method="POST" :action="formAction">
            @csrf
            <template x-if="isEditing">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="mb-4">
                <label for="layup-name"
                    class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Layup Name') }}
                </label>
                <input id="layup-name" name="name" type="text" x-model="formData.name"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-400 transition focus:border-transparent focus:ring-2 focus:ring-[#3f7a5c] dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                    placeholder="e.g., Standard 3-Ply Wall" required />
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </form>
    </div>

    <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
        <button type="button" @click="closeModal()"
            class="rounded-lg border border-gray-300 px-4 py-2 font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
            {{ __('Cancel') }}
        </button>
        <button type="submit" form="layup-form"
            class="rounded-lg bg-[#3f7a5c] px-4 py-2 font-medium text-white transition hover:bg-[#2d5b45]">
            <span x-show="!isEditing">{{ __('Add Layup') }}</span>
            <span x-show="isEditing">{{ __('Update Layup') }}</span>
        </button>
    </div>
</x-modal>
