<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ $supplier->name }} - Layups
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700">
                <div class="p-6 text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Layups</h3>
                        <a href="{{ route('suppliers.layups.create', $supplier) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Create Layup') }}
                        </a>
                    </div>

                    @if($supplier->layups->isEmpty())
                        <p class="text-gray-500">{{ __('No layups found.') }}</p>
                    @else
                        <table class="w-full text-sm text-left rtl:text-right text-gray-400">
                            <thead class="text-xs text-gray-300 uppercase bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Name</th>
                                    <th scope="col" class="px-6 py-3">Layers</th>
                                    <th scope="col" class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplier->layups as $layup)
                                    <tr class="bg-gray-800 border-b border-gray-700 hover:bg-gray-700">
                                        <td class="px-6 py-4">{{ $layup->name }}</td>
                                        <td class="px-6 py-4">{{ $layup->layers->count() }}</td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('suppliers.show', $supplier) }}" class="text-blue-400 hover:underline">View</a>
                                            <a href="{{ route('suppliers.layups.edit', [$supplier, $layup]) }}" class="text-green-400 hover:underline ml-2">Edit</a>
                                            <form method="POST" action="{{ route('suppliers.layups.destroy', [$supplier, $layup]) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:underline ml-2" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
