<div id="conflictResolutionModal"
    class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Conflict Resolution: Import') }}</h3>
            <button type="button" onclick="closeConflictModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="flex flex-1 overflow-hidden">
            <div class="w-72 border-r border-gray-200 overflow-y-auto bg-white flex flex-col">
                <div id="conflictsList" class="flex-1 overflow-y-auto">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-6">
                <div id="comparisonView" class="space-y-6">
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex gap-2">
                <button type="button" id="prevConflictBtn" onclick="previousConflict()"
                    class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 transition font-medium text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 19l-7-7 7-7"></path>
                    </svg>
                    {{ __('Previous Conflict') }}
                </button>
                <span id="conflictIndicator" class="px-3 py-2 text-sm text-gray-600">1 of 3</span>
                <button type="button" id="nextConflictBtn" onclick="nextConflict()"
                    class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 transition font-medium text-sm flex items-center gap-2">
                    {{ __('Next Conflict') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeConflictModal()"
                    class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 transition font-medium text-sm">
                    {{ __('Cancel Import') }}
                </button>
                <button type="button" id="confirmResolveBtn" onclick="confirmResolveConflictsAndSubmit()"
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
</div>
