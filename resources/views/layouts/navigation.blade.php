<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left: Logo + Nav Links -->
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-3 me-8">
                    <div class="shrink-0 flex items-center gap-3 me-8">
                        <img src="{{ asset('images/clt-layup.svg') }}" alt="CLT Layup" class="h-10 w-10">
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900 leading-tight">CLT Layup</div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide leading-tight">Manager</div>
                    </div>
                </div>

                <!-- Nav Links -->
                <div class="hidden sm:flex items-center space-x-1">
                    <a href="#"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition">
                        Overview
                    </a>
                    <a href="{{ route('suppliers.index') }}"
                        class="px-4 py-2 text-sm font-medium transition border-b-2 {{ request()->routeIs('suppliers.*') ? 'text-green-800 border-green-800' : 'text-gray-500 border-transparent hover:text-gray-700' }}">
                        Suppliers
                    </a>
                    <a href="#"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition">
                        Layups
                    </a>
                    <a href="#"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition">
                        Layers
                    </a>
                    <a href="#"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition">
                        Settings
                    </a>
                </div>
            </div>

            <!-- Right: Notification + User -->
            <div class="hidden sm:flex items-center gap-4">
                <!-- Notification Bell -->
                <button class="relative p-2 text-gray-400 hover:text-gray-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                <!-- Divider -->
                <div class="h-8 w-px bg-gray-200"></div>

                <!-- User -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 px-2 py-1 rounded-lg hover:bg-gray-50 transition">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-gray-400">{{ Auth::user()->email }}</div>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('suppliers.index') }}" class="block py-2 text-sm text-gray-700">Suppliers</a>
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200 px-4">
            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
