<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Import Supplier Data') }}
                </h2>
                <p class="text-l text-gray-800 dark:text-gray-200 leading-text">
                    {{ __('for supplier') }}: <strong>{{ $supplier->name }}</strong>
                </p>
            </div>
            <a href="{{ route('suppliers.show', $supplier) }}" class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-md">
                            <h3 class="font-semibold text-red-800 dark:text-red-200 mb-2">{{ __('Error') }}</h3>
                            <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('suppliers.import.upload', $supplier) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <label for="json_file" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('JSON File') }}
                            </label>
                            <input
                                type="file"
                                id="json_file"
                                name="json_file"
                                accept=".json"
                                required
                                class="block w-full text-sm text-gray-500 dark:text-gray-400
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    dark:file:bg-blue-900/20 dark:file:text-blue-300
                                    hover:file:bg-blue-100 dark:hover:file:bg-blue-900/40"
                            />
                            <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                                {{ __('Select a JSON file exported from this or another supplier') }}
                            </p>
                        </div>

                        <div>
                            <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-3">{{ __('Expected JSON Format') }}</h3>
                            <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded-md overflow-auto text-xs"><code>{
  "supplier": {
    "name": "Supplier Name",
    "exported_at": "{{ now()->toDateTimeString() }}",
  },
  "layups": [
    {
      "name": "Layup Name",
      "layers": [
        {
          "layer_order": 1,
          "thickness": 20.5,
          "width": 150.0,
          "angle": 0.0
        }
      ]
    }
  ]
}</code></pre>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Upload & Check') }}
                                </button>
                            <a href="{{ route('suppliers.show', $supplier) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
