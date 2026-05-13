<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Import CSV</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <form method="POST"
              action="{{ route('suppliers.import.csv') }}"
              enctype="multipart/form-data"
              class="bg-white p-6 rounded shadow">

            @csrf

            <div class="mb-4">
                <label class="block mb-2">Upload CSV</label>

                <input type="file"
                       name="file"
                       class="w-full border p-2 rounded"
                       accept=".csv">

                @error('file')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-2 font-medium text-gray-700">
                    Conflict Strategy
                </label>

                <select name="strategy"
                        class="w-full border rounded p-2">

                    <option value="overwrite">Overwrite Existing</option>
                    <option value="skip">Skip Conflict</option>
                    <option value="duplicate">Duplicate Layup</option>
                    <option value="reject">Reject Entire Import</option>

                </select>

                <div class="mt-2 text-sm text-gray-600 space-y-1">

                    <p><strong>Overwrite:</strong> Data lama akan diganti dengan data baru jika terjadi konflik.</p>

                    <p><strong>Skip:</strong> Data lama tetap dipertahankan, data baru yang konflik akan diabaikan.</p>

                    <p><strong>Duplicate Layup:</strong> Layup akan dibuat baru dengan suffix "(imported)".</p>

                    <p><strong>Reject:</strong> Seluruh proses import akan dibatalkan jika ditemukan konflik.</p>

                </div>
            </div>

            <button class="bg-indigo-600 text-white px-4 py-2 rounded">
                Import
            </button>

        </form>

    </div>
</x-app-layout>