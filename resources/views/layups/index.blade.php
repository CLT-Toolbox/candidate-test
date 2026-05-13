<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Layups</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <a href="{{ route('layups.create') }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded">
            + Add Layup
        </a>

        <div class="mt-4 bg-white shadow rounded-lg overflow-hidden">

            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">Name</th>
                        <th class="p-3 text-left">Supplier</th>
                        <th class="p-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($layups as $layup)
                        <tr class="border-b">
                            <td class="p-3">{{ $layup->name }}</td>
                            <td class="p-3">{{ $layup->supplier->name ?? '-' }}</td>

                            <td class="p-3 text-right space-x-2">

                                <a href="{{ route('layups.edit', $layup) }}"
                                   class="text-yellow-600">
                                    Edit
                                </a>

                                <form class="inline"
                                      action="{{ route('layups.destroy', $layup) }}"
                                      method="POST">
                                    @csrf
                                    @method('DELETE')

                                    <button class="text-red-600">
                                        Delete
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>
</x-app-layout>