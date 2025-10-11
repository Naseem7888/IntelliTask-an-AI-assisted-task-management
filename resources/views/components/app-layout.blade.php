<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ ($title ?? null) ? ($title . ' - IntelliTask') : (config('app.name', 'IntelliTask')) }}</title>
    <meta name="description" content="IntelliTask – AI-Powered Task Management">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-900" data-theme="dark">
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-[1000] focus:bg-black/80 focus:text-white focus:px-4 focus:py-2 focus:rounded">Skip
        to content</a>
    <div class="min-h-screen relative">
        <div x-data="{ scrolled: false }"
            x-init="scrolled = window.scrollY > 4; window.addEventListener('scroll', () => scrolled = window.scrollY > 4)">
            <div class="sticky top-0 z-30 transition-shadow" :class="{ 'shadow-lg/10': scrolled }">
                @include('layouts.navigation')
            </div>
        </div>

        <!-- Page Heading -->
        @if (isset($header))
            <header class="card-glass border-b border-white/10 sticky top-0 z-30">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="fade-in" id="main-content">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                {{ $slot }}
            </div>
        </main>
    </div>
    <!-- Loading overlay controlled by app.js (livewire:navigating/navigated) -->
    <div id="global-loading-overlay" class="hidden modal-backdrop items-center justify-center">
        <div class="spinner border-t-4 border-accent-400 w-10 h-10 rounded-full animate-spin"></div>
    </div>
    <!-- Optional toast root (toast-manager creates per-position containers as needed) -->
    <div aria-hidden="true" class="sr-only" id="toast-root"></div>
    @livewireScripts
</body>

</html>