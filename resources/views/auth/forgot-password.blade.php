<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            margin: 2rem;
        }

        .container {
            max-width: 480px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin: .5rem 0 .25rem;
        }

        input {
            width: 100%;
            padding: .6rem .7rem;
            border: 1px solid #d1d5db;
            border-radius: .375rem;
        }

        .error {
            color: #dc2626;
            font-size: .9rem;
        }

        .status {
            color: #16a34a;
            font-size: .9rem;
        }

        button {
            background: #111827;
            color: #fff;
            padding: .6rem 1rem;
            border: 0;
            border-radius: .375rem;
            cursor: pointer;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <p>Forgot your password? Enter your email address and we'll send you a reset link.</p>

        @if (session('status'))
            <p class="status">{{ session('status') }}</p>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus />
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror

            <button type="submit">Email Password Reset Link</button>
        </form>
    </div>
</body>

</html>
<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>