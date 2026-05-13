<nav x-data="{ open: false }" class="app-header">
    <!-- Primary Navigation Menu -->
    <div class="page-wrap">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-6">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <span class="brand-mark-shell">
                            <x-application-logo class="brand-mark-icon block h-5 w-5 fill-current" />
                        </span>
                        <span class="leading-tight">
                            <span class="text-main block text-base font-semibold">CLT Manager</span>
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:flex sm:items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                        {{ __('Suppliers') }}
                    </x-nav-link>
                    <x-nav-link :href="route('layups.catalog')" :active="request()->routeIs('layups.catalog') || request()->routeIs('suppliers.layups.*')">
                        {{ __('Layups') }}
                    </x-nav-link>
                    <x-nav-link :href="route('layers.catalog')" :active="request()->routeIs('layers.catalog') || request()->routeIs('suppliers.layups.layers.*')">
                        {{ __('Layers') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                <button class="icon-button-muted" type="button" aria-label="Notifications">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5"/>
                        <path d="M9 17a3 3 0 006 0"/>
                    </svg>
                </button>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="btn-secondary inline-flex items-center gap-2 px-3 py-2 text-sm font-medium">
                            <span class="avatar-pill !h-6 !w-6">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <div class="text-left leading-tight">
                                <span class="block text-sm">{{ Auth::user()->name }}</span>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="icon-button-muted focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="mobile-nav-surface panel-divider hidden border-t sm:hidden">
        <div class="pt-2 pb-3 space-y-1 page-wrap">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                {{ __('Suppliers') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('layups.catalog')" :active="request()->routeIs('layups.catalog') || request()->routeIs('suppliers.layups.*')">
                {{ __('Layups') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('layers.catalog')" :active="request()->routeIs('layers.catalog') || request()->routeIs('suppliers.layups.layers.*')">
                {{ __('Layers') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="panel-divider border-t pb-1 pt-4">
            <div class="page-wrap">
                <div class="text-main text-base font-medium">{{ Auth::user()->name }}</div>
                <div class="text-muted text-sm font-medium">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1 page-wrap">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
