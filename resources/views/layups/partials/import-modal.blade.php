{{-- Import Layup Data Modal --}}
<x-modal name="import-layup" :show="false" focusable maxWidth="2xl">
    <div class="border-b border-gray-100 bg-white px-6 py-4 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Import Layup Data') }}
            </h2>
            <button @click="$dispatch('close-modal', 'import-layup'); clearFile()"
                class="text-gray-400 hover:text-gray-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div class="space-y-4 bg-white px-6 py-4 dark:bg-gray-800">
        {{-- Error Message --}}
        <div x-show="importError"
            class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 flex-shrink-0 text-red-600 dark:text-red-400" fill="currentColor"
                    viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm text-red-700 dark:text-red-300" x-text="importError"></p>
                </div>
                <button type="button" @click="importError = null" class="text-red-600 hover:text-red-800">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- File Upload Area --}}
        <div @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
            @drop.prevent="handleFileDrop" :class="isDragging ? 'border-[#3f7a5c] bg-green-50' : 'border-gray-300'"
            class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed px-4 py-8 transition-colors">
            <input x-ref="fileInput" type="file" id="import-file" name="file" @change="handleFileSelect"
                accept=".csv,.json,.xlsx,.xls" class="hidden" />
            <div x-show="!selectedFile">
                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    <button type="button" @click="$refs.fileInput.click()"
                        class="font-medium text-[#3f7a5c] hover:text-[#2d5b45]">
                        {{ __('Click to upload') }}
                    </button>
                    {{ __(' or drag and drop') }}
                </p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('CSV, JSON, or XLSX up to 10MB') }}
                </p>
            </div>
            <div x-show="selectedFile" class="flex items-center justify-center gap-3">
                <svg class="h-8 w-8 text-[#3f7a5c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <div class="text-left">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="selectedFile?.name"></p>
                    <p class="text-xs text-gray-500" x-text="formatFileSize(selectedFile?.size)"></p>
                </div>
                <button type="button" @click="clearFile" class="ml-4 text-red-600 hover:text-red-800">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Conflict Resolution Strategy --}}
        <div x-show="selectedFile">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Conflict Resolution Strategy') }}
            </label>
            <select x-model="conflictStrategy" name="conflict_strategy"
                class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 transition focus:border-transparent focus:ring-2 focus:ring-[#3f7a5c] dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                <option value="skip">{{ __('Skip conflicts (Default)') }}</option>
                <option value="overwrite">{{ __('Overwrite existing') }}</option>
                <option value="manual">{{ __('Manual resolution') }}</option>
            </select>
        </div>

        {{-- Alert: Potential Conflicts --}}
        <div x-show="detectedConflicts && conflicts.length > 0"
            class="rounded-lg border border-red-200 bg-red-50 px-4 py-3.5 dark:border-red-800 dark:bg-red-900/20">
            <div class="flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="mt-0.5 h-5 w-5 flex-shrink-0 text-red-600 dark:text-red-400" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01M10.29 3.86l-8.58 14.86A1 1 0 002.59 20h18.82a1 1 0 00.88-1.28l-8.58-14.86a1 1 0 00-1.42 0z" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-red-800 dark:text-red-200">
                        {{ __('Potential Conflicts Detected') }}</p>
                    <p class="mt-1 text-xs leading-relaxed text-red-700 dark:text-red-300">
                        <span x-text="conflicts.length"></span>
                        {{ __('Layups differ significantly from current suppliers in the database.') }}
                        <button type="button" @click="showConflictModal = true"
                            class="font-medium text-red-600 underline hover:no-underline dark:text-red-400">
                            {{ __('View details') }}
                        </button>
                    </p>
                    {{-- Expandable details --}}
                    <div x-show="showConflictDetails" x-transition
                        class="mt-2 max-h-40 space-y-1 overflow-y-auto rounded border border-red-200 bg-white px-3 py-2 text-xs text-gray-700 dark:border-red-800 dark:bg-gray-900 dark:text-gray-300">
                        <template x-for="(conflict, idx) in conflicts" :key="idx">
                            <p><span x-text="'- Layup: ' + conflict.name"></span></p>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
        <button type="button" @click="$dispatch('close-modal', 'import-layup'); clearFile()"
            class="rounded-lg border border-gray-300 px-4 py-2 font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
            {{ __('Cancel') }}
        </button>

        {{-- View Details button (show only if conflicts detected) --}}
        

        {{-- Confirm Import button --}}
        <button x-show="selectedFile" type="button" @click="processImport" :disabled="isProcessing"
            :class="isProcessing ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#2d5b45]'"
            class="flex items-center gap-2 rounded-lg bg-[#3f7a5c] px-4 py-2 font-medium text-white transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            <span x-show="!isProcessing">{{ __('Confirm Import') }}</span>
            <span x-show="isProcessing">{{ __('Processing...') }}</span>
        </button>
    </div>
</x-modal>
