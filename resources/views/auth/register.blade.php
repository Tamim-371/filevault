@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
    <h1>Create an account</h1>
    <p class="subtitle">Start storing your files securely today.</p>

    @if ($errors->any() && !$errors->has('name') && !$errors->has('email') && !$errors->has('password'))
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">Full name</label>
            <input
                class="form-control"
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                placeholder="Jane Smith"
                required
                autofocus
                autocomplete="name"
            >
            @error('name')<div class="form-error">{{ $message }}</div>@enderror
        </div>

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
                placeholder="At least 8 characters"
                required
                autocomplete="new-password"
            >
            @error('password')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirm password</label>
            <input
                class="form-control"
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Repeat your password"
                required
                autocomplete="new-password"
            >
        </div>

        <button type="submit" class="btn-submit">Create Account</button>
    </form>

    <div class="auth-footer">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </div>
@endsection
