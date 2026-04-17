<x-guest-layout>
    <!-- Heading -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-white mb-2">
            Create Account
        </h1>
        <p class="text-gray-400">
            Join CLT Toolbox and start managing your data
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" class="text-sm font-semibold text-white" />
            <x-text-input 
                id="name" 
                class="block mt-2 w-full px-4 py-3 rounded-lg border border-gray-600 bg-gray-700 text-white placeholder-gray-500 focus:bg-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition" 
                type="text" 
                name="name" 
                :value="old('name')" 
                placeholder="John Doe"
                required 
                autofocus 
                autocomplete="name" 
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-red-500" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" class="text-sm font-semibold text-white" />
            <x-text-input 
                id="email" 
                class="block mt-2 w-full px-4 py-3 rounded-lg border border-gray-600 bg-gray-700 text-white placeholder-gray-500 focus:bg-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition" 
                type="email" 
                name="email" 
                :value="old('email')" 
                placeholder="you@example.com"
                required 
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
                autocomplete="new-password" 
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-400" />
            <p class="text-xs text-gray-400 mt-1">At least 8 characters recommended</p>
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-sm font-semibold text-white" />
            <x-text-input 
                id="password_confirmation" 
                class="block mt-2 w-full px-4 py-3 rounded-lg border border-gray-600 bg-gray-700 text-white placeholder-gray-500 focus:bg-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition"
                type="password"
                name="password_confirmation"
                placeholder="••••••••"
                required 
                autocomplete="new-password" 
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-500" />
        </div>

        <!-- Register Button -->
        <button
            type="submit"
            class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition transform hover:scale-105 active:scale-95 shadow-lg"
        >
            {{ __('Create Account') }}
        </button>

        <!-- Login link -->
        <p class="text-center text-gray-300 text-sm">
            Already have an account?
            <a 
                href="{{ route('login') }}" 
                class="font-semibold text-blue-400 hover:text-blue-300 transition"
            >
                Sign in
            </a>
        </p>
    </form>

    <!-- Agreement notice -->
    <div class="mt-8 pt-6 border-t border-gray-700">
        <p class="text-xs text-gray-500 text-center">
            By creating an account, you agree to our terms and conditions
        </p>
    </div>
</x-guest-layout>
