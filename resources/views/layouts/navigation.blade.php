<nav x-data="{ open: false }" class="bg-white border-b border-surface-100 sticky top-0 z-50">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 shadow-lg">
        <div class="flex justify-between h-16 items-center">

            <div class="flex items-center sm:w-1/4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 bg-brand-50 p-2 rounded-xl border border-brand-100/50">
                        <i data-lucide="layers" class="w-5 h-5 text-brand-600"></i>
                    </div>
                    <div class="leading-tight">
                        <h1 class="text-[15px] font-bold text-surface-800">Layup Manager</h1>
                        <p class="text-[10px] text-surface-400 font-medium uppercase tracking-wider">Engineering Admin
                        </p>
                    </div>
                </div>
            </div>

            <div class="hidden lg:flex flex-1 justify-center items-center space-x-1">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="layout-dashboard">
                    Dashboard
                </x-nav-link>

                <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" icon="users">
                    Suppliers
                </x-nav-link>

                <x-nav-link href="#" :active="false" icon="package">
                    Inventory
                </x-nav-link>

                <x-nav-link href="#" :active="false" icon="factory">
                    Production
                </x-nav-link>

                <x-nav-link href="#" :active="false" icon="settings">
                    Settings
                </x-nav-link>
            </div>

            <div class="flex items-center justify-end sm:w-1/4 space-x-4">
                <button class="p-2 text-surface-400 hover:text-brand-600 transition-colors">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                </button>
                <div class="hidden md:flex items-center space-x-4">
                    <div class="h-6 w-px bg-surface-200"></div>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center space-x-3 focus:outline-none group text-right">
                                <div class="hidden lg:block leading-tight">
                                    <p
                                        class="text-[14px] font-bold text-surface-800 group-hover:text-brand-600 transition-colors">
                                        {{ Auth::user()->name }}
                                    </p>
                                    <p class="text-[11px] text-surface-400 font-medium whitespace-nowrap">
                                        Head Engineer
                                    </p>
                                </div>
                                <div
                                    class="h-9 w-9 rounded-xl bg-brand-50 flex items-center justify-center border border-brand-100/50 text-brand-600 group-hover:bg-brand-100 transition-colors">
                                    <i data-lucide="user" class="w-5 h-5"></i>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>

                <div class="flex items-center lg:hidden">
                    <button @click="open = ! open"
                        class="p-2 rounded-xl text-surface-400 hover:text-brand-600 hover:bg-brand-50 transition border border-transparent">
                        <i x-show="!open" data-lucide="menu" class="w-6 h-6"></i>
                        <i x-show="open" data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        class="lg:hidden border-t border-surface-100 bg-white shadow-xl">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="layout-dashboard">
                Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" icon="users">
                Suppliers
            </x-responsive-nav-link>
            <x-responsive-nav-link href="#" icon="package">
                Inventory
            </x-responsive-nav-link>
            <x-responsive-nav-link href="#" icon="factory">
                Production
            </x-responsive-nav-link>
            <x-responsive-nav-link href="#" icon="settings">
                Settings
            </x-responsive-nav-link>
        </div>

        <div class="md:hidden pt-4 pb-1 border-t border-surface-100 bg-surface-50/50 px-4">
            <div class="flex items-center space-x-3 mb-3">
                <div class="h-10 w-10 rounded-xl bg-brand-100 flex items-center justify-center text-brand-600">
                    <i data-lucide="user" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="font-bold text-surface-800">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-surface-500">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="space-y-1 pb-4">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-500">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
