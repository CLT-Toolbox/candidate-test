<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Supplier Detail
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white p-6 rounded shadow">
            <p><strong>ID:</strong> {{ $supplier->id }}</p>
            <p><strong>Name:</strong> {{ $supplier->name }}</p>
        </div>

    </div>
</x-app-layout>