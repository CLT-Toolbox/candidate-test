<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-white antialiased" style="background: linear-gradient(135deg, #111827 0%, #1a2d3d 25%, #1a1f3a 50%, #1a2d3d 75%, #111827 100%); background-attachment: fixed; min-height: 100vh;">
        <!-- Decorative blobs -->
        <div class="fixed top-0 left-0 w-96 h-96 bg-blue-900 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse"></div>
        <div class="fixed top-0 right-0 w-96 h-96 bg-purple-900 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse" style="animation-delay: 2s;"></div>
        <div class="fixed bottom-0 left-1/2 w-96 h-96 bg-indigo-900 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse" style="animation-delay: 4s;"></div>

        <div class="min-h-screen flex flex-col justify-center items-center relative z-10">
            <div class="flex flex-col items-center">
                <!-- Logo -->
                <div class="mb-8">
                    <a href="/" class="inline-block">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg hover:shadow-xl transition-shadow">
                            <x-application-logo class="w-12 h-12 fill-current text-white" />
                        </div>
                    </a>
                </div>

                <!-- Card -->
                <div class="w-full sm:max-w-md px-8 py-8 bg-gray-800 rounded-2xl shadow-2xl backdrop-blur-xl relative border border-gray-700">
                    
                    {{ $slot }}
                </div>

                <!-- Footer text -->
                <p class="mt-6 text-center text-gray-400 text-sm">
                    CLT Toolbox • Feature Assignment
                </p>
            </div>
        </div>

        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            
            .animate-float {
                animation: float 3s ease-in-out infinite;
            }
        </style>
    </body>
</html>
