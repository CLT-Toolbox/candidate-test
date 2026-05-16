<x-guest-layout :panel-title="__('Forgot password')">
    <p class="mb-6 text-sm leading-relaxed text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </p>

    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

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
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="{{ __('Email') }}"
                class="w-full rounded-full border border-green-100 bg-green-50/80 py-3.5 pl-12 pr-4 text-gray-900 placeholder:text-gray-400 shadow-sm transition focus:border-green-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-500/40"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2 px-1" />
        </div>

        <button
            type="submit"
            class="flex w-full items-center justify-center rounded-full bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-3.5 text-sm font-semibold uppercase tracking-widest text-white shadow-md transition hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
        >
            {{ __('Email Password Reset Link') }}
        </button>
    </form>

    <p class="mt-10 text-center text-sm text-gray-600">
        <a href="{{ route('login') }}" class="font-semibold text-green-700 hover:text-green-800">{{ __('Back to login') }}</a>
    </p>
</x-guest-layout>
