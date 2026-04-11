@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 min-h-screen p-6">

        <div class="max-w-6xl mx-auto">

            <!-- HEADER -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Suppliers</h1>
                    <p class="text-gray-500 text-sm">Manage timber suppliers and material sourcing.</p>
                </div>

                <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm shadow-sm">
                    + Add Supplier
                </button>
            </div>

            <!-- SEARCH + ACTION -->
            <div class="flex justify-between items-center mb-4">

                <!-- SEARCH INPUT -->


                <form method="GET" action="{{ route('suppliers.index') }}" class="relative w-1/3">
                    <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search suppliers..."
                        class="w-full pl-10 pr-3 py-2 rounded-lg bg-white border-none shadow-sm focus:ring-0 text-sm">
                </form>

                <!-- BUTTONS -->
                <div class="flex gap-2">


                    @php
                        $currentSort = request('sort');
                        $nextSort = $currentSort === 'asc' ? 'desc' : 'asc';
                    @endphp

                    <a href="{{ route('suppliers.index', ['sort' => $nextSort, 'search' => request('search')]) }}"
                        class="flex items-center gap-1 border px-3 py-2 rounded-lg text-sm text-gray-600 bg-white shadow-sm">

                        ⚙️ Filter

                        @if ($currentSort === 'asc')
                            ↑
                        @elseif($currentSort === 'desc')
                            ↓
                        @endif

                    </a>

                    <a href="{{ route('suppliers.export') }}"
                        class="border px-3 py-2 rounded-lg text-sm bg-white shadow-sm">
                        ⬇️ Export
                    </a>
                </div>
            </div>

            <!-- TABLE CARD -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">

                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="text-left p-4">Name</th>
                            <th class="text-center p-4">Total Layups</th>
                            <th class="text-center p-4">Created At</th>
                            <th class="text-center p-4">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr class="border-t hover:bg-gray-50">

                                <!-- NAME + AVATAR -->
                                <td class="p-4 flex items-center gap-3">

                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold">
                                        {{-- {{ strtoupper(substr($supplier->name, 0, 2)) }} --}}
                                    </div>

                                    <div>
                                        <div class="text-gray-800 font-medium">
                                            {{ $supplier->name }}
                                        </div>
                                        <div class="text-xs text-gray-400">


                                            ID: {{ $supplier->code }}
                                        </div>
                                    </div>

                                </td>

                                <!-- TOTAL -->
                                <td class="text-center text-gray-700">
                                    {{ $supplier->layups_count }}
                                </td>

                                <!-- DATE -->
                                <td class="text-center text-gray-500">
                                    {{ $supplier->created_at->format('M d, Y') }}
                                </td>

                                <!-- ACTION -->
                                <td class="text-center">
                                    <a href="{{ route('suppliers.show', $supplier->id) }}"
                                        class="text-blue-600 hover:underline text-sm">
                                        View
                                    </a>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- FOOTER -->
                <div class="p-4 text-xs text-gray-400 flex justify-between items-center">
                    <span>
                        Showing 1 to {{ count($suppliers) }} results
                    </span>

                    <div class="flex gap-2">
                        <button class="border px-2 py-1 rounded">‹</button>
                        <button class="border px-2 py-1 rounded">›</button>
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
