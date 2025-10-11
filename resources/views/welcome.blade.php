<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'IntelliTask') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-900 text-white">
    <!-- Navigation -->
    <header class="sticky top-0 z-40">
        <div class="card-glass backdrop-blur-md border-b border-white/10">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <a href="/" class="text-2xl font-bold text-white hover:opacity-90 transition">IntelliTask</a>
                <nav aria-label="Top navigation" class="flex items-center gap-6">
                    <a href="#features" class="text-gray-300 hover:text-white transition-colors">Features</a>
                    <a href="#stats" class="text-gray-300 hover:text-white transition-colors">Stats</a>
                    <a href="#cta" class="text-gray-300 hover:text-white transition-colors">Get Started</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-glass btn-sm">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-gradient btn-sm btn-ripple">Sign Up</a>
                            @endif
                        @endauth
                    @endif
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="relative gradient-mesh overflow-hidden">
        <div class="absolute inset-0 opacity-30"></div>
        <div
            class="relative max-w-7xl mx-auto px-6 py-24 sm:py-32 min-h-[70vh] hero-min flex flex-col md:flex-row items-center gap-10">
            <div class="flex-1 fade-in-up">
                <span class="badge badge-gradient">NEW</span>
                <h1 class="mt-4 text-4xl sm:text-6xl font-extrabold leading-tight">
                    AI‑Powered Task Management
                    <span class="block gradient-text">Organize. Prioritize. Succeed.</span>
                </h1>
                <p class="mt-6 text-lg text-gray-300 max-w-2xl">Let IntelliTask help you break down complex goals into
                    actionable steps with smart suggestions, beautiful organization, and effortless productivity.</p>
                <div class="mt-10 flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('register') }}" class="btn btn-gradient btn-lg btn-ripple">Get Started Free</a>
                    <a href="#features" class="btn btn-outline btn-lg">Learn More</a>
                </div>
            </div>
            <div class="flex-1 w-full">
                <div class="card card-glass card-hover fade-in-up p-3">
                    <img src="{{ asset('Images/' . rawurlencode('landing page image.jpg')) }}"
                        alt="IntelliTask – Organize, Prioritize, Succeed"
                        class="w-full h-64 sm:h-80 object-cover rounded-xl shadow-lg shadow-glow-accent animate-float animate-glow"
                        loading="eager"
                        onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');" />
                    <div
                        class="hidden w-full h-64 sm:h-80 flex items-center justify-center rounded-xl bg-gradient-to-br from-gray-800/80 to-gray-700/60 border border-white/10">
                        <span class="text-2xl font-bold text-white">IntelliTask</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="max-w-7xl mx-auto px-6 py-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card card-glass card-hover p-6 scroll-reveal">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xl font-semibold">AI‑Powered Suggestions</h3>
                    <span class="badge badge-gradient">AI</span>
                </div>
                <p class="text-gray-400">Turn vague objectives into concrete, actionable subtasks instantly.</p>
            </div>
            <div class="card card-glass card-hover p-6 scroll-reveal">
                <h3 class="text-xl font-semibold mb-2">Smart Task Management</h3>
                <p class="text-gray-400">Focus on what matters with filters, priorities, and seamless organization.</p>
            </div>
            <div class="card card-glass card-hover p-6 scroll-reveal">
                <h3 class="text-xl font-semibold mb-2">Seamless Organization</h3>
                <p class="text-gray-400">Beautiful cards, hover effects, and responsive layouts out of the box.</p>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section id="stats" class="max-w-7xl mx-auto px-6 py-8">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div class="card card-elevated p-6 text-center">
                <div class="text-3xl font-bold">1000+</div>
                <div class="text-gray-400">Tasks Completed</div>
            </div>
            <div class="card card-elevated p-6 text-center">
                <div class="text-3xl font-bold">500+</div>
                <div class="text-gray-400">Active Users</div>
            </div>
            <div class="card card-elevated p-6 text-center">
                <div class="text-3xl font-bold">99%</div>
                <div class="text-gray-400">Satisfaction</div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section id="cta" class="mt-10">
        <div class="gradient-mesh py-16">
            <div class="max-w-3xl mx-auto px-6 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4">Ready to get started?</h2>
                <p class="text-gray-200 mb-6">Sign up in seconds and start turning ideas into results.</p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="btn btn-gradient btn-lg btn-ripple">Sign Up Free</a>
                    <a href="{{ route('login') }}" class="text-accent-300 hover:text-accent-200 transition">Already have
                        an account? Log in</a>
                </div>
            </div>
        </div>
    </section>
</body>

</html>