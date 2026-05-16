<div id="createLayupModal"
    class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Create Layup') }}</h3>
            <button type="button" onclick="closeCreateLayupModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('suppliers.layups.store', $supplier->supplier_id) }}"
            id="createLayupForm">
            @csrf
            <div class="mb-4">
                <label for="createLayupName" class="block text-sm font-medium text-gray-700">
                    {{ __('Name') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" id="createLayupName" name="name" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900">
                <span class="text-red-600 text-sm" id="createLayupNameError"></span>
            </div>
            <div class="mb-4">
                <label for="createLayupDescription" class="block text-sm font-medium text-gray-700">
                    {{ __('Description') }}
                </label>
                <textarea id="createLayupDescription" name="description" rows="3"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900"></textarea>
            </div>
            <div class="mb-4">
                <label for="createLayupThickness" class="block text-sm font-medium text-gray-700">
                    {{ __('Thickness') }}
                </label>
                <input type="text" id="createLayupThickness" name="thickness"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900">
            </div>
            <div class="mb-4">
                <label for="createLayupGrade" class="block text-sm font-medium text-gray-700">
                    {{ __('Grade') }}
                </label>
                <input type="text" id="createLayupGrade" name="grade"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCreateLayupModal()"
                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">
                    {{ __('Create') }}
                </button>
            </div>
        </form>
    </div>
</div>
