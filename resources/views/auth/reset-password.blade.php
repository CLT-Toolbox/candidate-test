<x-guest-layout :panel-title="__('Reset password')">
    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-green-700/60">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </span>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $request->email) }}"
                required
                autofocus
                autocomplete="username"
                placeholder="{{ __('Email') }}"
                class="w-full rounded-full border border-green-100 bg-green-50/80 py-3.5 pl-12 pr-4 text-gray-900 placeholder:text-gray-400 shadow-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-500/40"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2 px-1" />
        </div>

        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-green-700/60">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </span>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="{{ __('Password') }}"
                class="w-full rounded-full border border-green-100 bg-green-50/80 py-3.5 pl-12 pr-4 text-gray-900 placeholder:text-gray-400 shadow-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-500/40"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2 px-1" />
        </div>

        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-green-700/60">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </span>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="{{ __('Confirm password') }}"
                class="w-full rounded-full border border-green-100 bg-green-50/80 py-3.5 pl-12 pr-4 text-gray-900 placeholder:text-gray-400 shadow-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-500/40"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 px-1" />
        </div>

        <button
            type="submit"
            class="mt-2 flex w-full items-center justify-center rounded-full bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-3.5 text-sm font-semibold uppercase tracking-widest text-white shadow-md transition hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
        >
            {{ __('Reset Password') }}
        </button>
    </form>
</x-guest-layout>
