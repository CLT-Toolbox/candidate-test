<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Feature Test Toolbox') }} - Register</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <div class="h-screen bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
            <img id="background" class="absolute h-full w-full object-cover" src="https://app.clttoolbox.com.au/images/login-bg.jpg" alt="CLT Toolbox background" />
            <div class="absolute inset-0 bg-black/40 animate-blur-in"></div>
            
            <div class="relative h-full flex flex-col">
                <!-- Header -->
                <header class="py-4 px-10">
                    @if (Route::has('login'))
                        <nav class="flex justify-end animate-fade-in">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="rounded-md px-3 py-2 ring-1 ring-transparent transition hover:text-gray-100 focus:outline-none focus-visible:ring-[#FF2D20] text-gray-200">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="transition hover:opacity-80 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#FF2D20] rounded-md">
                                    <img src="https://app.clttoolbox.com.au/images/logos/logo_color_white.png" alt="CLT Toolbox" class="h-8">
                                </a>
                            @endauth
                        </nav>
                    @endif
                </header>

                <!-- Main Content -->
                <div class="flex-1 flex flex-col items-center justify-center px-4 py-8 selection:bg-[#FF2D20] selection:text-white overflow-y-auto">
                    <div class="w-full max-w-md animate-fade-in">
                        <!-- Logo -->
                        <div class="flex justify-center mb-8">
                            <img src="https://app.clttoolbox.com.au/images/logos/logo_color_white.png" alt="CLT Toolbox" class="h-16">
                        </div>

                        <!-- Glasmorphism Card -->
                        <div class="backdrop-blur-md bg-white/10 border border-white/20 rounded-2xl shadow-2xl p-8 hover:bg-white/15 transition duration-300">
                            <h2 class="text-2xl font-bold text-white mb-2 text-center">Create Account</h2>
                            <p class="text-white/70 text-center text-sm mb-6">Join CLT Toolbox today</p>

                            <!-- Validation Errors -->
                            @if ($errors->any())
                                <div class="mb-4 p-4 bg-red-500/20 border border-red-500/50 rounded-lg">
                                    <div class="text-red-200 text-sm font-medium">
                                        <strong>Oops!</strong> There were some problems with your input.
                                    </div>
                                    <ul class="mt-2 text-red-200 text-sm list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                                @csrf

                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-white/90 mb-2">Full Name</label>
                                    <input id="name" 
                                        class="w-full px-4 py-2.5 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-[#FF2D20] focus:border-transparent transition backdrop-blur-sm"
                                        type="text" 
                                        name="name" 
                                        value="{{ old('name') }}" 
                                        required 
                                        autofocus 
                                        autocomplete="name"
                                        placeholder="John Doe" />
                                    @error('name')
                                        <p class="mt-1 text-red-300 text-xs">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email Address -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-white/90 mb-2">Email Address</label>
                                    <input id="email" 
                                        class="w-full px-4 py-2.5 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-[#FF2D20] focus:border-transparent transition backdrop-blur-sm"
                                        type="email" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        required 
                                        autocomplete="username"
                                        placeholder="you@example.com" />
                                    @error('email')
                                        <p class="mt-1 text-red-300 text-xs">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-white/90 mb-2">Password</label>
                                    <input id="password" 
                                        class="w-full px-4 py-2.5 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-[#FF2D20] focus:border-transparent transition backdrop-blur-sm"
                                        type="password" 
                                        name="password" 
                                        required 
                                        autocomplete="new-password"
                                        placeholder="••••••••" />
                                    @error('password')
                                        <p class="mt-1 text-red-300 text-xs">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-white/90 mb-2">Confirm Password</label>
                                    <input id="password_confirmation" 
                                        class="w-full px-4 py-2.5 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-[#FF2D20] focus:border-transparent transition backdrop-blur-sm"
                                        type="password" 
                                        name="password_confirmation" 
                                        required 
                                        autocomplete="new-password"
                                        placeholder="••••••••" />
                                    @error('password_confirmation')
                                        <p class="mt-1 text-red-300 text-xs">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Register Button -->
                                <button type="submit" class="w-full px-4 py-2.5 bg-[#FF2D20] hover:bg-[#e6280f] text-white font-semibold rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FF2D20] mt-6">
                                    Create Account
                                </button>
                            </form>

                            <!-- Divider -->
                            <div class="relative my-6">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-white/20"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-2 bg-gradient-to-b from-white/5 via-white/5 to-transparent text-white/50">Already have an account?</span>
                                </div>
                            </div>

                            <a href="{{ route('login') }}" class="w-full inline-block text-center px-4 py-2.5 bg-white/5 hover:bg-white/10 border border-white/20 rounded-lg text-white/90 font-medium transition duration-200">
                                Sign In
                            </a>
                        </div>
                        <footer class="mt-8 text-center text-xs text-white/50">
                            <p>&copy; 2026 CLT Toolbox. All rights reserved.</p>
                        </footer>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>