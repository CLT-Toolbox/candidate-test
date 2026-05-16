<x-guest-layout :panel-title="__('Verify email')">
    <p class="mb-6 text-sm leading-relaxed text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
            @csrf
            <button
                type="submit"
                class="flex w-full items-center justify-center rounded-full bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-3.5 text-sm font-semibold uppercase tracking-widest text-white shadow-md transition hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:w-auto"
            >
                {{ __('Resend Verification Email') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="w-full rounded-full border border-gray-200 bg-white px-6 py-3.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 sm:w-auto"
            >
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
