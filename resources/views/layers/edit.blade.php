<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Layer</h2>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('partials.alerts')

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <p class="mb-2 text-sm text-gray-500">Supplier: <span class="font-semibold text-gray-900">{{ $supplier->name }}</span></p>
                <p class="mb-6 text-sm text-gray-500">Layup: <span class="font-semibold text-gray-900">{{ $layup->name }}</span></p>

                <form method="POST" action="{{ route('suppliers.layups.layers.update', [$supplier, $layup, $layer]) }}">
                    @method('PATCH')
                    @include('layers._form', ['submitLabel' => 'Update layer'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
