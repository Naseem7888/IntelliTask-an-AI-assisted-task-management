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
    <style>
        /* Auth glass input overrides (no build required) */
        .form-input-glass {
            background: rgba(255, 255, 255, 0.035);
            color: #cbd5e1;
            /* light slate */
            border: 1px solid rgba(255, 255, 255, 0.18);
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            border-radius: 9999px;
            /* pill */
            padding: 0.75rem 1rem;
            caret-color: #cbd5e1;
            text-shadow: 0 0 6px rgba(203, 213, 225, 0.22);
        }

        .form-input-glass::placeholder {
            color: rgba(203, 213, 225, 0.6);
        }

        .form-input-glass:focus {
            outline: none;
            border-color: rgba(167, 139, 250, 0.7);
            /* primary-ish */
            box-shadow: 0 0 0 3px rgba(167, 139, 250, 0.2);
            background: rgba(255, 255, 255, 0.055);
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased bg-gray-900"
    style="background-image: url('{{ asset('Images/background.jpg') }}'); background-size: cover; background-position: center; background-attachment: fixed;">
    <div class="min-h-screen relative flex flex-col sm:justify-center items-center p-6">
        <!-- Background video -->
        <video class="pointer-events-none absolute inset-0 w-full h-full object-cover" aria-hidden="true" autoplay muted
            loop playsinline preload="auto" poster="{{ asset('Images/background.jpg') }}">
            <source src="{{ asset('Images/login background.MP4.mp4') }}" type="video/mp4">
        </video>
        <!-- Background overlay for readability over the image -->
        <div class="absolute inset-0 bg-black/20"></div>
        <!-- Decorative elements -->
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full bg-primary-500/10 blur-2xl animate-float">
            </div>
            <div class="absolute -bottom-10 -right-10 w-52 h-52 rounded-full bg-accent-500/10 blur-2xl animate-float"
                style="animation-delay: .6s"></div>
        </div>

        <div class="relative z-10 text-center">
            <a href="/" class="inline-flex items-center gap-3">
                <img src="{{ asset('Images/' . rawurlencode('IntelliTask logo.png')) }}" alt="IntelliTask logo"
                    class="w-9 h-9 rounded-lg" onerror="this.remove()">
                <span class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">IntelliTask</span>
            </a>
            <p class="mt-2 text-gray-300">AI-Powered Task Management</p>
        </div>

        <div class="relative z-10 w-full sm:max-w-md md:max-w-lg mt-8 rounded-2xl card-glass card-elevated card-hover backdrop-blur-xl border shadow-2xl px-8 py-8"
            style="background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.08);">
            {{ $slot }}
        </div>
    </div>
</body>

</html>