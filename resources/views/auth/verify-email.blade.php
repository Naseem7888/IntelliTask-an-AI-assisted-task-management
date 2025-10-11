<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <style>
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            margin: 2rem;
        }

        .container {
            max-width: 640px;
            margin: 0 auto;
        }

        .muted {
            color: #4b5563;
        }

        .success {
            color: #16a34a;
        }

        .row {
            display: flex;
            gap: 1rem;
            align-items: center;
            justify-content: space-between;
            margin-top: 1rem;
        }

        button {
            background: #111827;
            color: #fff;
            padding: .6rem 1rem;
            border: 0;
            border-radius: .375rem;
            cursor: pointer;
        }

        button.link {
            background: transparent;
            color: #111827;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <p class="muted">
            Thanks for signing up! Before getting started, please verify your email address by clicking the link we just
            emailed to you. If you didn't receive the email, you can request another below.
        </p>

        @if (session('status') == 'verification-link-sent')
            <p class="success">A new verification link has been sent to your email address.</p>
        @endif

        <div class="row">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit">Resend Verification Email</button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="link">Log Out</button>
            </form>
        </div>
    </div>
</body>

</html>