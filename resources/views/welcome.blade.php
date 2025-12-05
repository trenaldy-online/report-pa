<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>AHCC Data System</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 selection:bg-blue-500 selection:text-white">
        
        <div class="relative min-h-screen flex flex-col items-center justify-center overflow-hidden">
            
            {{-- Background Decoration --}}
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
                <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
                <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
                <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
            </div>

            {{-- Main Content --}}
            <div class="relative z-10 w-full max-w-2xl px-6 lg:px-8 text-center">
                
                {{-- Logo Area --}}
                <div class="mx-auto mb-8 flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-xl text-blue-600">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>

                <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl mb-4">
                    AHCC <span class="text-blue-600">Data System</span>
                </h1>
                
                <p class="mt-2 text-lg leading-8 text-gray-600">
                    Platform Terintegrasi Manajemen Data Pasien & Analisis Marketing <br class="hidden sm:block">Adhi Husada Cancer Center.
                </p>

                <div class="mt-10 flex items-center justify-center gap-x-6">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="rounded-xl bg-blue-600 px-8 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all hover:scale-105">
                                Buka Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-xl bg-blue-600 px-8 py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-200 hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all hover:scale-105">
                                Log in System
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-sm font-semibold leading-6 text-gray-900 hover:text-blue-600 transition">
                                    Register <span aria-hidden="true">→</span>
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="absolute bottom-6 text-xs text-gray-400">
                &copy; {{ date('Y') }} AHCC. All rights reserved.
            </div>

        </div>
    </body>
</html>