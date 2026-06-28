<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-brand-dark antialiased bg-gray-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-[#f1f9f5] via-white to-[#e6f2f0] relative overflow-hidden px-4 py-8">
            <!-- Subtle Decorative Orbs -->
            <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-brand-secondary/15 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-40 -right-40 w-96 h-96 rounded-full bg-brand-primary/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col items-center mb-2">
                <a href="/" class="transition-transform duration-300 hover:scale-105 drop-shadow-sm">
                    <x-application-logo class="w-24 h-24" />
                </a>
                <h1 class="text-3xl font-extrabold text-brand-dark mt-4 tracking-tight">SIM RW 047</h1>
                <p class="text-sm text-gray-500 mt-1.5 font-medium text-center max-w-xs">Sistem Informasi Manajemen Lingkungan RW 047</p>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white/95 backdrop-blur-md border border-slate-100 shadow-[0_20px_50px_-20px_rgba(0,77,64,0.15)] sm:rounded-2xl relative z-10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

