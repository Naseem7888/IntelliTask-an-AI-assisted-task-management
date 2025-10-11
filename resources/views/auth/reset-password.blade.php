<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', request('email')) }}" required
                autofocus />
            @error('email') <div class="error">{{ $message }}</div> @enderror

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required />
            @error('password') <div class="error">{{ $message }}</div> @enderror

            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required />
            @error('password_confirmation') <div class="error">{{ $message }}</div> @enderror

            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>

</html>