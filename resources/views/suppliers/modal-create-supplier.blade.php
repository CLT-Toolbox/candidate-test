<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Create Supplier') }}</h3>
            <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('suppliers.store') }}" id="createForm">
            @csrf
            <div class="mb-4">
                <label for="createName" class="block text-sm font-medium text-gray-700">
                    {{ __('Name') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" id="createName" name="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900">
                <span class="text-red-600 text-sm" id="createNameError"></span>
            </div>
            <div class="mb-4">
                <label for="createEmail" class="block text-sm font-medium text-gray-700">
                    {{ __('Email') }}
                </label>
                <input type="email" id="createEmail" name="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900">
            </div>
            <div class="mb-4">
                <label for="createLocation" class="block text-sm font-medium text-gray-700">
                    {{ __('Location') }}
                </label>
                <input type="text" id="createLocation" name="location" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900">
            </div>
            <div class="mb-4">
                <label for="createCertification" class="block text-sm font-medium text-gray-700">
                    {{ __('Certification') }}
                </label>
                <input type="text" id="createCertification" name="certification" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900">
            </div>
            <div class="mb-4">
                <label for="createAuditDate" class="block text-sm font-medium text-gray-700">
                    {{ __('Audit Date') }}
                </label>
                <input type="date" id="createAuditDate" name="audit_date" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">
                    {{ __('Create') }}
                </button>
            </div>
        </form>
    </div>
</div>
