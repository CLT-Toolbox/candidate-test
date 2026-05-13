<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="page-title">Layer #{{ $layer->layer_order }}</h2>
            <p class="page-subtitle mt-1">Detailed properties for layup {{ $layup->name }}.</p>
        </div>
    </x-slot>

    <div class="pb-10">
        <div class="page-wrap max-w-3xl space-y-4 fade-rise">
            @if (session('status'))
                <div class="status-success">{{ session('status') }}</div>
            @endif
            <div class="app-card">
                <div class="mb-4 flex justify-end">
                    <a href="{{ route('suppliers.layups.show', [$supplier, $layup]) }}" class="btn-secondary">Back</a>
                </div>
                <p><span class="font-medium">Layer Order:</span> {{ $layer->layer_order }}</p>
                <p><span class="font-medium">Thickness:</span> {{ $layer->thickness }}</p>
                <p><span class="font-medium">Width:</span> {{ $layer->width }}</p>
                <p><span class="font-medium">Angle:</span> {{ $layer->angle }}</p>

                <div class="mt-4 flex gap-4">
                    <a href="{{ route('suppliers.layups.layers.edit', [$supplier, $layup, $layer]) }}" class="btn-secondary">Edit</a>
                    <form method="POST" action="{{ route('suppliers.layups.layers.destroy', [$supplier, $layup, $layer]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger" onclick="return confirm('Delete this layer?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
