<x-guest-layout :panel-title="__('Confirm password')">
    <p class="mb-6 text-sm leading-relaxed text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

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
                autocomplete="current-password"
                placeholder="{{ __('Password') }}"
                class="w-full rounded-full border border-green-100 bg-green-50/80 py-3.5 pl-12 pr-4 text-gray-900 placeholder:text-gray-400 shadow-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-500/40"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2 px-1" />
        </div>

        <button
            type="submit"
            class="flex w-full items-center justify-center rounded-full bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-3.5 text-sm font-semibold uppercase tracking-widest text-white shadow-md transition hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
        >
            {{ __('Confirm') }}
        </button>
    </form>
</x-guest-layout>
