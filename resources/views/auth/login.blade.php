<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4 alert alert-success" :status="session('status')" />

    <div class="space-y-6">
        <header>
            <h2 class="text-2xl font-bold text-white">Welcome Back</h2>
            <p class="text-gray-300">Sign in to your account</p>
        </header>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" glass type="email" name="email" :value="old('email')"
                    required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" glass type="password" name="password" required
                    autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center gap-2">
                    <input id="remember_me" type="checkbox"
                        class="h-4 w-4 rounded border-gray-600 text-accent-500 bg-gray-900 shadow-sm focus:ring-accent-500"
                        name="remember">
                    <span class="text-sm text-gray-300">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-accent-400 hover:text-accent-300 underline-offset-4 hover:underline transition"
                        href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <x-primary-button gradient lg ripple class="w-full">
                {{ __('Log in') }}
            </x-primary-button>

            <div class="divider divider-text">or</div>
            <p class="text-center text-sm text-gray-400">Don't have an account?
                <a href="{{ route('register') }}"
                    class="text-accent-400 hover:text-accent-300 underline-offset-4 hover:underline">Sign up</a>
            </p>
        </form>
    </div>
</x-guest-layout>