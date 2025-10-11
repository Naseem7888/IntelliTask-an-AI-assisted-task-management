<x-guest-layout>
    <div class="space-y-6">
        <header>
            <h2 class="text-2xl font-bold text-white">Create Account</h2>
            <p class="text-gray-300">Join IntelliTask today</p>
        </header>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="block mt-1 w-full" glass type="text" name="name"
                    :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" glass type="email" name="email"
                    :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" glass type="password" name="password"
                    required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                <div class="mt-2 progress">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" glass type="password"
                    name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <x-primary-button gradient lg ripple class="w-full">
                {{ __('Register') }}
            </x-primary-button>

            <div class="divider divider-text">or</div>
            <p class="text-center text-sm text-gray-400">Already have an account?
                <a href="{{ route('login') }}"
                    class="text-accent-400 hover:text-accent-300 underline-offset-4 hover:underline">Sign in</a>
            </p>

            <p class="text-xs text-gray-500 text-center">By signing up, you agree to our <a href="#"
                    class="text-accent-400 hover:text-accent-300">Terms</a> and <a href="#"
                    class="text-accent-400 hover:text-accent-300">Privacy Policy</a>.</p>
        </form>
    </div>
</x-guest-layout>