<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Import Supplier Data</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto">

            @if(session('success'))
                <div class="text-green-600 mb-2">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="text-red-600 mb-2">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('suppliers.import') }}" enctype="multipart/form-data">
                @csrf

                <input type="file" name="file" class="border p-2 w-full">

                <button class="mt-4 px-4 py-2 bg-blue-600 text-white rounded">
                    Import JSON
                </button>
            </form>

        </div>
    </div>
</x-app-layout>