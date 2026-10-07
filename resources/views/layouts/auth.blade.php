<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'FileVault') }} — @yield('title')</title>

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
            --text:     #e8eaf0;
            --muted:    #6b7280;
            --subtle:   #374151;
            --font:     'DM Sans', sans-serif;
            --radius:   10px;
            --radius-lg:16px;
        }

        html, body { height: 100%; font-family: var(--font); }

        body {
            background: var(--bg);
            color: var(--text);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        /* Background decoration */
        body::before {
            content: '';
            position: fixed;
            top: -30%;
            left: 50%;
            transform: translateX(-50%);
            width: 900px;
            height: 600px;
            background: radial-gradient(ellipse, rgba(79,142,247,.07) 0%, transparent 70%);
            pointer-events: none;
        }

        .auth-logo {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-weight: 600;
            font-size: 1.3rem;
            color: var(--text);
            text-decoration: none;
            margin-bottom: 2rem;
        }

        .auth-logo .logo-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .auth-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 2.25rem 2rem;
            width: 100%;
            max-width: 420px;
            position: relative;
        }

        .auth-card h1 {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: .35rem;
            letter-spacing: -.3px;
        }

        .auth-card .subtitle {
            color: var(--muted);
            font-size: .9rem;
            margin-bottom: 1.75rem;
        }

        .form-group { margin-bottom: 1.1rem; }
        .form-label {
            display: block;
            font-size: .82rem;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: .45rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .form-control {
            display: block;
            width: 100%;
            padding: .72rem 1rem;
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
        .form-error { color: #f87171; font-size: .82rem; margin-top: .35rem; }

        .btn-submit {
            width: 100%;
            padding: .75rem;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-family: var(--font);
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1.25rem;
            transition: background .15s, transform .1s;
        }
        .btn-submit:hover    { background: #3a7ae8; }
        .btn-submit:active   { transform: scale(.99); }

        .auth-footer {
            margin-top: 1.25rem;
            text-align: center;
            font-size: .88rem;
            color: var(--muted);
        }
        .auth-footer a { color: var(--accent); text-decoration: none; font-weight: 500; }
        .auth-footer a:hover { text-decoration: underline; }

        .alert-error {
            background: rgba(248,113,113,.08);
            border: 1px solid rgba(248,113,113,.2);
            color: #f87171;
            padding: .75rem 1rem;
            border-radius: var(--radius);
            font-size: .88rem;
            margin-bottom: 1.25rem;
        }
    </style>
</head>
<body>

<a href="{{ route('home') }}" class="auth-logo">
    <div class="logo-icon">⬡</div>
    FileVault
</a>

<div class="auth-card">
    @yield('content')
</div>

</body>
</html>
