<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ ($title ?? null) ? ($title . ' - IntelliTask') : config('app.name', 'IntelliTask') }}</title>
    <meta name="description" content="IntelliTask – AI-Powered Task Management">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased bg-gray-900 gradient-mesh">
    <div class="min-h-screen relative flex flex-col sm:justify-center items-center p-6">
        <!-- Decorative elements -->
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-primary-500/10 blur-2xl animate-float">
            </div>
            <div class="absolute -bottom-10 -right-10 w-52 h-52 rounded-full bg-accent-500/10 blur-2xl animate-float"
                style="animation-delay: .6s"></div>
        </div>

        <div class="relative z-10 text-center">
            <a href="/" class="inline-flex items-center gap-2">
                <span
                    class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-accent-500 shadow-glow-accent"></span>
                <span class="text-3xl sm:text-4xl font-extrabold gradient-text">IntelliTask</span>
            </a>
            <p class="mt-2 text-gray-300">AI-Powered Task Management</p>
        </div>

        <div class="relative z-10 w-full sm:max-w-lg mt-8 card-glass card-elevated fade-in-up px-8 py-8 rounded-xl">
            {{ $slot }}
        </div>
    </div>
</body>

</html>