<nav class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between items-center h-16">

            <!-- LEFT -->
            <div class="flex items-center gap-10">

                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 bg-green-600 rounded flex items-center justify-center text-white font-bold text-sm">
                        A
                    </div>
                    <div class="text-sm font-semibold text-gray-800">
                        CLT Layup<br>
                        <span class="text-xs text-gray-400">Manager</span>
                    </div>
                </div>

                <!-- Menu -->
                <div class="flex gap-6 text-sm">

                    <!-- Overview -->
                    <a href="{{ route('dashboard') }}"
                        class="pb-1 border-b-2 
                        {{ request()->routeIs('dashboard') ? 'border-green-600 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Overview
                    </a>

                    <!-- Suppliers -->
                    <a href="{{ route('suppliers.index') }}"
                        class="pb-1 border-b-2 
                        {{ request()->routeIs('suppliers.*') ? 'border-green-600 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Suppliers
                    </a>

                    <!-- Static menu -->
                    <span class="text-gray-400">Layups</span>
                    <span class="text-gray-400">Layers</span>
                    <span class="text-gray-400">Settings</span>

                </div>
            </div>

            <!-- RIGHT -->
            <div class="flex items-center gap-4">

                <!-- Notification -->
                <div class="text-gray-400">
                    🔔
                </div>

                <!-- User -->
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-sm font-medium">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>

                    <div class="text-sm">
                        <div class="text-gray-800 font-medium">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="text-xs text-gray-400">
                            Engineering Lead
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</nav>
