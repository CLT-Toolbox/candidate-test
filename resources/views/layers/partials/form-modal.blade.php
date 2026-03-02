{{-- Layer Form Modal (Create/Edit) --}}
<x-modal name="layer-form" :show="false" focusable>
    <div class="border-b border-gray-100 bg-white px-6 py-4 dark:border-gray-700 dark:bg-gray-800">
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            <span x-show="!isEditing">{{ __('Add New Layer') }}</span>
            <span x-show="isEditing">{{ __('Edit Layer') }}</span>
        </h2>
    </div>

    <div class="bg-white px-6 py-4 dark:bg-gray-800">
        <form id="layer-form" method="POST" :action="formAction">
            @csrf
            <template x-if="isEditing">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <div class="mb-4">
                <label for="layer-order"
                    class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Layer Order') }}
                </label>
                <input id="layer-order" name="layer_order" type="number" x-model="formData.layer_order" min="1"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-400 transition focus:border-transparent focus:ring-2 focus:ring-[#3f7a5c] dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                    placeholder="e.g., 1" required />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Position in the layup sequence (1 = top layer)') }}
                </p>
                @error('layer_order')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="thickness"
                    class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Thickness (mm)') }}
                </label>
                <input id="thickness" name="thickness" type="number" step="0.01" x-model="formData.thickness"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-400 transition focus:border-transparent focus:ring-2 focus:ring-[#3f7a5c] dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                    placeholder="e.g., 0.25" required />
                @error('thickness')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="width"
                    class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Width (mm)') }}
                </label>
                <input id="width" name="width" type="number" step="0.01" x-model="formData.width"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-400 transition focus:border-transparent focus:ring-2 focus:ring-[#3f7a5c] dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                    placeholder="e.g., 50.00" required />
                @error('width')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="angle"
                    class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Angle (degrees)') }}
                </label>
                <input id="angle" name="angle" type="number" step="0.1" x-model="formData.angle"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 placeholder-gray-400 transition focus:border-transparent focus:ring-2 focus:ring-[#3f7a5c] dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                    placeholder="e.g., 45.0" required />
                @error('angle')
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
        <button type="submit" form="layer-form"
            class="rounded-lg bg-[#3f7a5c] px-4 py-2 font-medium text-white transition hover:bg-[#2d5b45]">
            <span x-show="!isEditing">{{ __('Add Layer') }}</span>
            <span x-show="isEditing">{{ __('Update Layer') }}</span>
        </button>
    </div>
</x-modal>
