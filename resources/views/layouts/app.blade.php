<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'App')</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f4f6fb;
            --panel: #ffffff;
            --text: #111827;
            --muted: #6b7280;
            --border: #d1d5db;
            --primary: #4f46e5;
            --primary-soft: #eef2ff;
            --danger: #dc2626;
            --shadow: 0 18px 45px rgba(17, 24, 39, 0.1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", Arial, sans-serif;
            background: radial-gradient(circle at top right, #e0e7ff 0%, transparent 45%), var(--bg);
            color: var(--text);
            padding: 20px;
        }

        .app {
            max-width: 100%;
            margin: 0 auto;
            background: var(--panel);
            border: 1px solid rgba(209, 213, 219, 0.8);
            border-radius: 18px;
            box-shadow: var(--shadow);
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: calc(100vh - 40px);
            overflow: hidden;
        }

        .sidebar {
            padding: 20px;
            border-right: 1px solid var(--border);
            background: #f9fafb;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .brand {
            margin: 0 0 10px;
            font-size: 1.05rem;
            color: var(--muted);
            font-weight: 600;
        }

        .link,
        .logout-btn {
            width: 100%;
            display: block;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            color: var(--text);
            background: transparent;
            cursor: pointer;
            text-align: left;
        }

        .link:hover,
        .logout-btn:hover {
            background: #f3f4f6;
        }

        .active {
            background: var(--primary-soft);
            border-color: #c7d2fe;
            color: var(--primary);
        }

        .logout-form {
            margin-top: auto;
        }

        .logout-btn {
            color: var(--danger);
        }

        .content {
            padding: 30px;
        }

        h1 {
            margin: 0;
            font-size: 1.8rem;
        }

        @media (max-width: 860px) {
            body {
                padding: 12px;
            }

            .app {
                grid-template-columns: 1fr;
                min-height: calc(100vh - 24px);
            }

            .sidebar {
                border-right: 0;
                border-bottom: 1px solid var(--border);
            }

            .logout-form {
                margin-top: 0;
            }
        }
    </style>
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <p class="brand">Menu</p>
            <a class="link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
            <a class="link {{ request()->routeIs('prompts') ? 'active' : '' }}" href="{{ route('prompts') }}">Prompts</a>

            <form class="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" type="submit">Logout</button>
            </form>
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>
</body>
</html>
