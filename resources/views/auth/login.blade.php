@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
    <h1>Welcome back</h1>
    <p class="subtitle">Sign in to access your FileVault.</p>

    @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">Email address</label>
            <input
                class="form-control"
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="you@example.com"
                required
                autofocus
                autocomplete="username"
            >
            @error('email')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input
                class="form-control"
                type="password"
                id="password"
                name="password"
                placeholder="••••••••"
                required
                autocomplete="current-password"
            >
            @error('password')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem;">
            <input type="checkbox" id="remember" name="remember" style="accent-color:var(--accent);">
            <label for="remember" style="font-size:.88rem;color:var(--muted);cursor:pointer;">Remember me</label>
        </div>

        <button type="submit" class="btn-submit">Sign In</button>
    </form>

    <div class="auth-footer">
        Don't have an account? <a href="{{ route('register') }}">Create one</a>
    </div>
@endsection
