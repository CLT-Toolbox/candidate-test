<div id="importLayupModal"
    class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-xl">
        <div class="flex justify-between items-center px-6 py-4" style="border-bottom: 1px solid #e5e7eb;">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Import Layup Data') }}</h3>
            <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="px-6 py-6">
            <form method="POST" action="{{ route('suppliers.layups.import', $supplier->supplier_id) }}"
                id="importLayupForm" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <div style="border: 2px dashed #d1d5db;"
                        class="rounded-xl py-12 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition duration-300"
                        id="dropZone" onclick="document.getElementById('fileInput').click()">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" style="color: #10b981;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10">
                            </path>
                        </svg>
                        <p class="text-gray-900 font-medium mb-1">{{ __('Click to upload') }} <span
                                class="text-gray-500">{{ __('or drag and drop') }}</span></p>
                        <p class="text-gray-500 text-xs">{{ __('CSV or JSON up to 10MB') }}</p>
                    </div>
                    <input type="file" id="fileInput" name="file" class="hidden" accept=".csv,.json"
                        required>
                    <div id="selectedFileName" class="mt-3 p-3 bg-green-50 rounded-lg hidden"
                        style="border: 1px solid #d1f4e6;">
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold">{{ __('Selected File:') }}</span>
                            <span id="fileNameText" class="text-green-700 font-medium ml-2"></span>
                        </p>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="conflictStrategy" class="block text-sm font-semibold text-gray-900 mb-2">
                        {{ __('Conflict Resolution Strategy') }}
                    </label>
                    <select id="conflictStrategy" name="conflict_strategy"
                        class="w-full px-4 py-2 rounded-md text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        style="border: 1px solid #d1d5db;">
                        <option value="skip" selected>{{ __('Skip Conflict (Default)') }}</option>
                        <option value="overwrite">{{ __('Overwrite Existing') }}</option>
                        <option value="duplicate">{{ __('Duplicate Layup') }}</option>
                        <option value="reject">{{ __('Reject Entire Import') }}</option>
                    </select>
                </div>

                <div class="mb-6 bg-red-50 rounded-lg p-4 hidden" id="conflictsWarning"
                    style="border: 1px solid #fca5a5;">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-red-800">{{ __('Potential Conflicts Detected') }}
                            </p>
                            <p class="text-sm text-red-700 mt-1"><span id="conflictCount">0</span>
                                {{ __('Layups differ significantly from current suppliers in the database.') }} <button
                                    type="button" onclick="openConflictResolutionFromWarning()" class="underline font-medium hover:text-red-900 transition">{{ __('View details') }}</button></p>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="flex justify-end gap-3 px-6 py-4 bg-gray-50" style="border-top: 1px solid #e5e7eb;">
            <button type="button" onclick="closeImportModal()"
                class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 transition font-medium text-sm">
                {{ __('Cancel') }}
            </button>
            <button type="button" id="confirmImportBtn" onclick="confirmImportFromForm()"
                class="px-6 py-2 text-white bg-green-600 rounded-md hover:bg-green-700 transition font-medium text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                {{ __('Confirm Import') }}
            </button>
        </div>
    </div>
</div>
