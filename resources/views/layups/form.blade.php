<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            {{ $layup ? 'Edit Layup' : 'Create Layup' }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <form method="POST"
              action="{{ $layup ? route('layups.update', $layup) : route('layups.store') }}"
              class="bg-white p-6 rounded shadow">

            @csrf
            @if($layup) @method('PUT') @endif

            {{-- Supplier --}}
            <div class="mb-4">
                <label>Supplier</label>
                <select name="supplier_id" class="w-full border rounded p-2">
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                            {{ $layup && $layup->supplier_id == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Name --}}
            <div class="mb-4">
                <label>Name</label>
                <input type="text"
                       name="name"
                       value="{{ $layup->name ?? '' }}"
                       class="w-full border rounded p-2">

                
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror
            </div>

            <button class="bg-indigo-600 text-white px-4 py-2 rounded">
                Save
            </button>

        </form>

    </div>
</x-app-layout>