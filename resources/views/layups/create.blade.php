<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h2 class="page-title">Create Layup</h2>
                <p class="page-subtitle mt-1">Supplier: {{ $supplier->name }}</p>
            </div>
            <a href="{{ route('suppliers.layups.index', $supplier) }}" class="btn-secondary">Back</a>
        </div>
    </x-slot>

    <div class="pb-10">
        <div class="page-wrap max-w-3xl">
            <div class="app-card fade-rise">
                <form method="POST" action="{{ route('suppliers.layups.store', $supplier) }}" class="space-y-4">
                    @csrf
                    @include('layups.partials.form')
                    <div class="flex gap-2">
                        <button class="btn-primary" type="submit">Save</button>
                        <a class="btn-secondary" href="{{ route('suppliers.layups.index', $supplier) }}">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
