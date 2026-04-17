<x-guest-layout>
    <!-- Heading -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-white mb-2">
            Welcome Back
        </h1>
        <p class="text-gray-400">
            Sign in to your account to continue
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-sm font-bold text-white" />
            <x-text-input 
                id="email" 
                class="block mt-2 w-full px-4 py-3 rounded-lg border border-gray-600 bg-gray-700 text-white placeholder-gray-500 focus:bg-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition" 
                type="email" 
                name="email" 
                :value="old('email')" 
                placeholder="you@example.com"
                required 
                autofocus 
                autocomplete="username" 
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-500" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-white" />
            <x-text-input 
                id="password" 
                class="block mt-2 w-full px-4 py-3 rounded-lg border border-gray-600 bg-gray-700 text-white placeholder-gray-500 focus:bg-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition"
                type="password"
                name="password"
                placeholder="••••••••"
                required 
                autocomplete="current-password" 
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-500" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    class="rounded w-4 h-4 border-gray-600 bg-gray-700 text-blue-600 focus:ring-blue-500 cursor-pointer" 
                    name="remember"
                >
                <span class="ms-2 text-sm text-gray-300 cursor-pointer select-none">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a 
                    class="text-sm font-medium text-blue-400 hover:text-blue-300 transition" 
                    href="{{ route('password.request') }}"
                >
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Sign in Button -->
        <button
            type="submit"
            class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition transform hover:scale-105 active:scale-95 shadow-lg"
        >
            {{ __('Sign in') }}
        </button>

        <!-- Register link -->
        <p class="text-center text-gray-300 text-sm">
            Don't have an account?
            <a 
                href="{{ route('register') }}" 
                class="font-semibold text-blue-400 hover:text-blue-300 transition"
            >
                Sign up
            </a>
        </p>
    </form>

    <!-- Demo credentials hint -->
    <div class="mt-8 pt-6 border-t border-gray-700">
        <p class="text-center text-xs text-gray-500 mb-2">📧 Demo Account (for testing)</p>
        <div class="bg-gray-700 rounded-lg p-3 space-y-1 text-xs text-gray-300 text-center">
            <p><strong>Email:</strong> test@example.com</p>
            <p><strong>Password:</strong> password</p>
        </div>
    </div>
</x-guest-layout>
