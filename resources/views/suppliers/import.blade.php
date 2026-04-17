<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('Import Supplier Data') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700">
                <div class="p-6 text-gray-100">
                    @if ($errors->any())
                        <div class="mb-4 bg-red-900/30 border border-red-700 text-red-200 px-4 py-3 rounded relative">
                            <strong>{{ __('Error!') }}</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('suppliers.import') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="file"
                                class="block text-sm font-medium text-gray-300">JSON File</label>
                            <input type="file" id="file" name="file" accept=".json" class="mt-1 block w-full text-gray-100 bg-gray-700 border border-gray-600 rounded-lg p-2"
                                required>
                            @error('file')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-400 mb-2">Expected JSON format:</p>
                            <pre class="bg-gray-900 p-3 rounded text-xs overflow-x-auto text-gray-300 border border-gray-700"><code>{
                                "name": "Supplier Name",
                                "layups": [
                                    {
                                    "name": "Layup Name",
                                    "layers": [
                                        {
                                        "layer_order": 1,
                                        "thickness": 2.5,
                                        "width": 100,
                                        "angle": 45
                                        }
                                    ]
                                    }
                                ]
                            }</code></pre>
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('suppliers.index') }}"
                                class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded mr-2">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Import') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
