<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CLT Toolbox') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col md:flex-row">
            {{-- Panel kiri: welcome + background --}}
            <div class="relative md:w-[58%] min-h-[38vh] md:min-h-screen flex flex-col justify-center px-8 py-12 sm:px-12 lg:px-16 text-white">
                <div
                    class="absolute inset-0 bg-cover bg-center bg-no-repeat"
                    style="background-image: url('https://app.clttoolbox.com.au/images/login-bg.jpg');"
                    aria-hidden="true"
                ></div>
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-950/88 via-green-900/78 to-emerald-900/85" aria-hidden="true"></div>
                <div class="relative z-10 max-w-xl">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mb-8 text-white/90 hover:text-white transition">
                        <img src="https://app.clttoolbox.com.au/images/logos/logo_color_white.png" class="h-5 w-auto" alt="">
                    </a>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight leading-tight mb-5">
                        {{ __('Welcome to CLT Toolbox') }}
                    </h1>
                    <p class="text-green-50/95 text-base sm:text-lg leading-relaxed">
                        {{ __('Manage suppliers, layups, and layer specifications in one place. Sign in to continue to your dashboard and material workflows.') }}
                    </p>
                </div>
            </div>

            {{-- Panel kanan: form --}}
            <div class="flex-1 md:w-[42%] bg-white flex flex-col justify-center px-6 py-10 sm:px-10 lg:px-14 border-t border-gray-100 md:border-t-0 md:border-l md:border-gray-100">
                <div class="w-full max-w-md mx-auto">
                    @if ($panelTitle)
                        <p class="text-sm font-semibold tracking-[0.2em] text-green-700 mb-8 uppercase">
                            {{ $panelTitle }}
                        </p>
                    @endif
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
