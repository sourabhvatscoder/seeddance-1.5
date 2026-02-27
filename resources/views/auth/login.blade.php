<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f4f6fb;
            --card-bg: #ffffff;
            --text: #111827;
            --muted: #6b7280;
            --border: #d1d5db;
            --border-focus: #4f46e5;
            --danger-bg: #fef2f2;
            --danger-border: #fecaca;
            --danger-text: #991b1b;
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --shadow: 0 18px 45px rgba(17, 24, 39, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", Arial, sans-serif;
            background: radial-gradient(circle at top right, #e0e7ff 0%, transparent 45%), var(--bg);
            color: var(--text);
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            background: var(--card-bg);
            border: 1px solid rgba(209, 213, 219, 0.8);
            border-radius: 16px;
            padding: 30px;
            box-shadow: var(--shadow);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 1.7rem;
            line-height: 1.2;
        }

        .subtitle {
            margin: 0 0 24px;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .error-banner {
            margin-bottom: 18px;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid var(--danger-border);
            background: var(--danger-bg);
            color: var(--danger-text);
            font-size: 0.92rem;
        }

        .field {
            margin-bottom: 16px;
        }

        label {
            display: inline-block;
            margin-bottom: 6px;
            font-size: 0.92rem;
            font-weight: 600;
        }

        input {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.95rem;
            color: var(--text);
            background: #fff;
            transition: border-color 120ms ease, box-shadow 120ms ease;
        }

        input:focus {
            outline: none;
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        button {
            width: 100%;
            border: 0;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #ffffff;
            background: var(--primary);
            cursor: pointer;
            transition: background 120ms ease;
        }

        button:hover {
            background: var(--primary-hover);
        }

        button:focus-visible {
            outline: 3px solid rgba(79, 70, 229, 0.25);
            outline-offset: 2px;
        }

        @media (max-width: 480px) {
            body {
                padding: 14px;
            }

            .login-container {
                padding: 22px;
                border-radius: 14px;
            }
        }
    </style>
</head>
<body>
    <main class="login-container">
        <h1>Welcome back</h1>
        <p class="subtitle">Sign in to continue to your account.</p>

        @if ($errors->any())
            <div class="error-banner" role="alert">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="field">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" required>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
            </div>

            <button type="submit">Login</button>
        </form>
    </main>
</body>
</html>
