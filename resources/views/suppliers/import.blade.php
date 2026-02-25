<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import Supplier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('dashboard.suppliers.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Upload JSON File
                            </label>
                            <input type="file" name="file" accept=".json,.txt" required
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Conflict Strategy
                            </label>
                            <select name="conflict_strategy" required
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="overwrite">Overwrite Existing (Incoming data replaces current)</option>
                                <option value="skip">Skip Conflict (Keep current data, ignore incoming)</option>
                                <option value="duplicate">Duplicate Layup (Create new with suffix)</option>
                                <option value="reject">Reject Entire Import (Abort on conflict)</option>
                            </select>
                            <p class="text-gray-500 text-sm mt-1">
                                Choose how to handle conflicts when imported data differs from existing records.
                            </p>
                        </div>

                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Import
                        </button>
                        
                        <a href="{{ route('dashboard.suppliers.index') }}" 
                           class="ml-2 text-gray-600 hover:underline">
                            Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>