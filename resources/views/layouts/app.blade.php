<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FileVault') }} — @yield('title', 'Secure File Storage')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:       #0d0f14;
            --surface:  #151820;
            --surface2: #1c2030;
            --border:   #252a3a;
            --accent:   #4f8ef7;
            --accent2:  #7c5cfc;
            --success:  #34d399;
            --danger:   #f87171;
            --warn:     #fbbf24;
            --text:     #e8eaf0;
            --muted:    #6b7280;
            --subtle:   #374151;
            --font:     'DM Sans', sans-serif;
            --mono:     'DM Mono', monospace;
            --radius:   10px;
            --radius-lg:16px;
            --shadow:   0 4px 24px rgba(0,0,0,.4);
        }

        html { font-size: 16px; scroll-behavior: smooth; }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font);
            font-weight: 400;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ─── NAV ─────────────────────────────────────────────── */
        nav.navbar {
            background: rgba(13,15,20,.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 0 1.5rem;
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 62px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: .55rem;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.15rem;
            color: var(--text);
            letter-spacing: -.3px;
        }

        .nav-brand .logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .9rem;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: .25rem;
            list-style: none;
        }

        .nav-links a,
        .nav-links button {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .45rem .85rem;
            border-radius: var(--radius);
            color: var(--muted);
            text-decoration: none;
            font-size: .88rem;
            font-weight: 500;
            font-family: var(--font);
            background: none;
            border: none;
            cursor: pointer;
            transition: color .15s, background .15s;
            white-space: nowrap;
        }

        .nav-links a:hover,
        .nav-links button:hover { color: var(--text); background: var(--surface2); }
        .nav-links a.active    { color: var(--accent); background: rgba(79,142,247,.1); }

        .nav-links .btn-primary {
            background: var(--accent);
            color: #fff;
            padding: .45rem 1rem;
        }
        .nav-links .btn-primary:hover { background: #3a7ae8; color: #fff; }

        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: .4rem;
            background: none;
            border: none;
        }
        .hamburger span {
            display: block;
            width: 22px;
            height: 2px;
            background: var(--text);
            border-radius: 2px;
            transition: .3s;
        }

        .nav-mobile-open .nav-links {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            position: absolute;
            top: 62px;
            left: 0;
            right: 0;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: .75rem 1.5rem 1rem;
            gap: .25rem;
        }

        @media (max-width: 768px) {
            .hamburger { display: flex; }
            .nav-links  { display: none; }
        }

        /* ─── MAIN ────────────────────────────────────────────── */
        main { flex: 1; }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* ─── ALERTS ──────────────────────────────────────────── */
        .alert {
            padding: .85rem 1.1rem;
            border-radius: var(--radius);
            margin-bottom: 1.25rem;
            font-size: .9rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            border: 1px solid transparent;
        }
        .alert-success { background: rgba(52,211,153,.08); border-color: rgba(52,211,153,.25); color: var(--success); }
        .alert-error   { background: rgba(248,113,113,.08); border-color: rgba(248,113,113,.25); color: var(--danger); }

        /* ─── BUTTONS ─────────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .6rem 1.2rem;
            border-radius: var(--radius);
            font-family: var(--font);
            font-size: .9rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all .15s;
            white-space: nowrap;
        }
        .btn-primary   { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: #3a7ae8; }
        .btn-danger    { background: rgba(248,113,113,.12); color: var(--danger); border: 1px solid rgba(248,113,113,.2); }
        .btn-danger:hover { background: rgba(248,113,113,.22); }
        .btn-ghost     { background: var(--surface2); color: var(--text); border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--subtle); }
        .btn-sm        { padding: .4rem .85rem; font-size: .82rem; }

        /* ─── FORMS ───────────────────────────────────────────── */
        .form-group { margin-bottom: 1.1rem; }
        .form-label {
            display: block;
            font-size: .85rem;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: .45rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .form-control {
            display: block;
            width: 100%;
            padding: .7rem 1rem;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--text);
            font-family: var(--font);
            font-size: .95rem;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(79,142,247,.15);
        }
        .form-control::placeholder { color: var(--subtle); }
        .form-error { color: var(--danger); font-size: .82rem; margin-top: .35rem; }

        /* ─── CARD ────────────────────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
        }

        /* ─── FOOTER ──────────────────────────────────────────── */
        footer {
            border-top: 1px solid var(--border);
            padding: 1.25rem 1.5rem;
            text-align: center;
            font-size: .82rem;
            color: var(--muted);
        }

        /* ─── UTILITIES ───────────────────────────────────────── */
        .text-muted     { color: var(--muted); }
        .text-accent    { color: var(--accent); }
        .text-success   { color: var(--success); }
        .text-danger    { color: var(--danger); }
        .text-center    { text-align: center; }
        .mt-1 { margin-top: .5rem; }
        .mt-2 { margin-top: 1rem; }
        .mt-3 { margin-top: 1.5rem; }
        .mt-4 { margin-top: 2rem; }
        .mb-1 { margin-bottom: .5rem; }
        .mb-2 { margin-bottom: 1rem; }
        .mb-3 { margin-bottom: 1.5rem; }
        .d-flex { display: flex; }
        .align-center { align-items: center; }
        .gap-1 { gap: .5rem; }
        .gap-2 { gap: 1rem; }
        .flex-wrap { flex-wrap: wrap; }
        .justify-between { justify-content: space-between; }
    </style>

    @stack('styles')
</head>
<body>

<nav class="navbar" id="navbar">
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="nav-brand">
            <div class="logo-icon">⬡</div>
            FileVault
        </a>

        <button class="hamburger" id="hamburger" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>

        <ul class="nav-links" id="nav-links">
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
            <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a></li>

            @auth
                <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Login</a></li>
                <li><a href="{{ route('register') }}" class="btn-primary {{ request()->routeIs('register') ? 'active' : '' }}">Register</a></li>
            @endauth
        </ul>
    </div>
</nav>

<main>
    <div class="container" style="padding-top:2.5rem; padding-bottom:3rem;">

        @if(session('success'))
            <div class="alert alert-success">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">✗ {{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</main>

<footer>
    <p>© {{ date('Y') }} FileVault — Built with Laravel 12 &amp; ♥</p>
</footer>

<script>
    const hamburger = document.getElementById('hamburger');
    const navbar    = document.getElementById('navbar');
    hamburger?.addEventListener('click', () => {
        navbar.classList.toggle('nav-mobile-open');
    });
</script>

@stack('scripts')
</body>
</html>
