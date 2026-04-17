<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="text-3xl font-bold text-gray-100">
                Welcome, {{ Auth::user()->name }}! 👋
            </h2>
            <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transition transform hover:scale-105 active:scale-95 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                New Supplier
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mb-8">
            <!-- Card 1: Total Suppliers -->
            <div class="group bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1 overflow-hidden border border-gray-700">
                <div class="h-1.5 bg-gradient-to-r from-blue-500 to-blue-600"></div>
                <div class="p-8">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-xl bg-blue-900/30 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-900/50 transition">
                            <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs uppercase tracking-wide font-semibold text-gray-400">Total</p>
                            <h3 class="text-4xl font-bold text-gray-100 mt-1">{{ $totalSuppliers ?? 0 }}</h3>
                            <p class="text-sm text-gray-400 mt-2">Suppliers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access -->
        <div class="bg-gray-800 rounded-2xl shadow-lg p-8 mb-8 border border-gray-700">
            <h3 class="text-xl font-bold text-gray-100 mb-6">Quick Access</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('suppliers.index') }}" class="group p-4 rounded-xl bg-gray-700/50 hover:bg-blue-900/30 border border-gray-600 hover:border-blue-500 transition transform hover:scale-105">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-900/30 flex items-center justify-center group-hover:bg-blue-900/50 transition">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <span class="font-semibold text-gray-200">Suppliers</span>
                    </div>
                </a>

                <a href="{{ route('suppliers.create') }}" class="group p-4 rounded-xl bg-gray-700/50 hover:bg-green-900/30 border border-gray-600 hover:border-green-500 transition transform hover:scale-105">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-green-900/30 flex items-center justify-center group-hover:bg-green-900/50 transition">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <span class="font-semibold text-gray-200">Add Supplier</span>
                    </div>
                </a>

                <a href="{{ route('profile.edit') }}" class="group p-4 rounded-xl bg-gray-700/50 hover:bg-purple-900/30 border border-gray-600 hover:border-purple-500 transition transform hover:scale-105">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-900/30 flex items-center justify-center group-hover:bg-purple-900/50 transition">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                        </div>
                        <span class="font-semibold text-gray-200">Settings</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
