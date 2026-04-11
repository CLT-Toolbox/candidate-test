@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 min-h-screen p-6">

        <div class="max-w-6xl mx-auto">

            <!-- HEADER -->
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">
                    {{ $supplier->name }}
                </h1>
                <p class="text-sm text-gray-500">
                    ID: {{ $supplier->code }}
                </p>
            </div>

            <!-- INFO CARD -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <div class="grid grid-cols-3 gap-4 text-sm">

                    <div>
                        <p class="text-gray-400">Created At</p>
                        <p class="text-gray-800 font-medium">
                            {{ $supplier->created_at->format('M d, Y') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-400">Total Layups</p>
                        <p class="text-gray-800 font-medium">
                            {{ $supplier->layups->count() }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-400">Status</p>
                        <p class="text-green-600 font-medium">
                            Active
                        </p>
                    </div>

                </div>
            </div>

            <!-- LAYUPS TABLE -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">

                <div class="p-4 border-b">
                    <h2 class="font-semibold text-gray-700">
                        Layups
                    </h2>
                </div>

                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="text-left p-4">Name</th>
                            <th class="text-center p-4">Total Layers</th>
                            <th class="text-center p-4">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($supplier->layups as $layup)
                            <tr class="border-t hover:bg-gray-50">

                                <td class="p-4 text-gray-800 font-medium">
                                    {{ $layup->name }}
                                </td>

                                <td class="text-center">
                                    {{ $layup->layers->count() }}
                                </td>

                                <td class="text-center">
                                    <a href="#" class="text-blue-600 hover:underline">
                                        View
                                    </a>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

        </div>

    </div>
@endsection
