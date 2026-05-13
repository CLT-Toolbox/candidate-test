<x-app-layout>
    <x-slot name="header">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h2 class="page-title">Edit Layer</h2>
                <p class="page-subtitle mt-1">Layup: {{ $layup->name }}</p>
            </div>
            <a href="{{ route('suppliers.layups.layers.show', [$supplier, $layup, $layer]) }}" class="btn-secondary">Back</a>
        </div>
    </x-slot>

    <div class="pb-10">
        <div class="page-wrap max-w-3xl">
            <div class="app-card fade-rise">
                <form method="POST" action="{{ route('suppliers.layups.layers.update', [$supplier, $layup, $layer]) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('layers.partials.form')
                    <div class="flex gap-2">
                        <button class="btn-primary" type="submit">Update</button>
                        <a class="btn-secondary" href="{{ route('suppliers.layups.layers.show', [$supplier, $layup, $layer]) }}">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
