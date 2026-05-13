<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h2 class="page-title">Edit Supplier</h2>
                <p class="page-subtitle mt-1">Update supplier identity and metadata.</p>
            </div>
            <a href="{{ route('suppliers.show', $supplier) }}" class="btn-secondary">Back</a>
        </div>
    </x-slot>

    <div class="pb-10">
        <div class="page-wrap max-w-3xl">
            <div class="app-card fade-rise">
                <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('suppliers.partials.form')
                    <div class="flex gap-2">
                        <button class="btn-primary" type="submit">Update</button>
                        <a class="btn-secondary" href="{{ route('suppliers.show', $supplier) }}">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
