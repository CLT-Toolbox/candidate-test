<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Supplier</h2>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('partials.alerts')

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
                    @method('PATCH')
                    @include('suppliers._form', ['submitLabel' => 'Update supplier'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
