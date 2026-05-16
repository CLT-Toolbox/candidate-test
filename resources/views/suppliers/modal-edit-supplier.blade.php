<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Edit Supplier') }}</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form method="POST" action="" id="editForm">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label for="editSupplierId" class="block text-sm font-medium text-gray-700">
                    {{ __('Supplier ID') }}
                </label>
                <input type="text" id="editSupplierId" disabled class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed">
            </div>
            <div class="mb-4">
                <label for="editName" class="block text-sm font-medium text-gray-700">
                    {{ __('Name') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" id="editName" name="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900">
                <span class="text-red-600 text-sm" id="editNameError"></span>
            </div>
            <div class="mb-4">
                <label for="editEmail" class="block text-sm font-medium text-gray-700">
                    {{ __('Email') }}
                </label>
                <input type="email" id="editEmail" name="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900">
            </div>
            <div class="mb-4">
                <label for="editLocation" class="block text-sm font-medium text-gray-700">
                    {{ __('Location') }}
                </label>
                <input type="text" id="editLocation" name="location" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900">
            </div>
            <div class="mb-4">
                <label for="editCertification" class="block text-sm font-medium text-gray-700">
                    {{ __('Certification') }}
                </label>
                <input type="text" id="editCertification" name="certification" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900">
            </div>
            <div class="mb-4">
                <label for="editStatus" class="block text-sm font-medium text-gray-700">
                    {{ __('Status') }} <span class="text-red-500">*</span>
                </label>
                <select id="editStatus" name="status" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900">
                    <option value="ACTIVE">
                        {{ __('Active') }}
                    </option>
                    <option value="INACTIVE">
                        {{ __('Inactive') }}
                    </option>
                </select>
            </div>
            <div class="mb-4">
                <label for="editAuditDate" class="block text-sm font-medium text-gray-700">
                    {{ __('Audit Date') }}
                </label>
                <input type="date" id="editAuditDate" name="audit_date" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 bg-white text-gray-900">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">
                    {{ __('Update') }}
                </button>
            </div>
        </form>
    </div>
</div>
