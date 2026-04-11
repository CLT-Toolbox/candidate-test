<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard - Supplier Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                
                <div class="p-6 text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700">
                    {{ __("You're logged in!") }}
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <div class="space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Export Supplier</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Unduh data Supplier, Layups, dan Layers ke JSON.</p>
                        
                        <div class="flex gap-2">
                            <input type="number" id="supplierId" placeholder="ID Supplier" 
                                class="w-full rounded-md border-gray-300 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:ring-indigo-500">
                            <button onclick="handleExport()" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-bold transition">
                                EXPORT
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4 border-t md:border-t-0 md:border-l border-gray-200 dark:border-gray-700 pt-6 md:pt-0 md:pl-8">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Import & Sync</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Upload JSON untuk update data (Conflict Resolution: Overwrite).</p>

                        @if (session('message'))
                            <div class="p-2 text-sm bg-green-500 text-white rounded mb-2">
                                {{ session('message') }}
                            </div>
                        @endif
                        
                        @if (session('error'))
                            <div class="p-2 text-sm bg-red-500 text-white rounded mb-2">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('supplier.import') }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                            @csrf
                            <input type="file" name="file" accept=".json" required
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:bg-gray-700 file:text-gray-200">
                            <button type="submit" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-bold transition uppercase">
                                Sync Data
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function handleExport() {
            const id = document.getElementById('supplierId').value;
            if (id) {
                window.location.href = `/supplier/export/${id}`;
            } else {
                alert('Masukkan ID Supplier!');
            }
        }
    </script>
</x-app-layout>