@extends('layouts.app')

@section('title', 'Home')

@push('styles')
<style>
    .hero {
        text-align: center;
        padding: 4rem 1rem 3.5rem;
    }
    .hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        background: rgba(79,142,247,.1);
        border: 1px solid rgba(79,142,247,.2);
        color: var(--accent);
        font-size: .8rem;
        font-weight: 600;
        letter-spacing: .06em;
        text-transform: uppercase;
        padding: .35rem .85rem;
        border-radius: 100px;
        margin-bottom: 1.5rem;
    }
    .hero h1 {
        font-size: clamp(2.2rem, 5vw, 3.5rem);
        font-weight: 600;
        line-height: 1.18;
        letter-spacing: -.5px;
        margin-bottom: 1.25rem;
        color: var(--text);
    }
    .hero h1 span {
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .hero p {
        font-size: 1.1rem;
        color: var(--muted);
        max-width: 520px;
        margin: 0 auto 2.25rem;
        line-height: 1.7;
        font-weight: 300;
    }
    .hero-cta {
        display: flex;
        gap: .85rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    .hero-cta .btn-hero {
        padding: .8rem 1.75rem;
        border-radius: var(--radius);
        font-size: .95rem;
        font-weight: 600;
        text-decoration: none;
        transition: all .15s;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
    }
    .btn-hero-primary {
        background: var(--accent);
        color: #fff;
    }
    .btn-hero-primary:hover { background: #3a7ae8; transform: translateY(-1px); }
    .btn-hero-secondary {
        background: var(--surface2);
        color: var(--text);
        border: 1px solid var(--border);
    }
    .btn-hero-secondary:hover { background: var(--subtle); transform: translateY(-1px); }

    /* Feature grid */
    .features {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.25rem;
        margin-top: 3.5rem;
    }
    .feature-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.5rem 1.6rem;
        transition: border-color .2s, transform .2s;
    }
    .feature-card:hover { border-color: rgba(79,142,247,.35); transform: translateY(-2px); }
    .feature-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        margin-bottom: 1rem;
    }
    .icon-blue   { background: rgba(79,142,247,.12); }
    .icon-purple { background: rgba(124,92,252,.12); }
    .icon-green  { background: rgba(52,211,153,.12); }
    .icon-amber  { background: rgba(251,191,36,.12); }
    .feature-card h3 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: .4rem;
        color: var(--text);
    }
    .feature-card p { font-size: .88rem; color: var(--muted); line-height: 1.6; }

    /* Divider */
    .section-divider {
        border: none;
        border-top: 1px solid var(--border);
        margin: 3.5rem 0;
    }

    .cta-strip {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 2.5rem 2rem;
        text-align: center;
    }
    .cta-strip h2 { font-size: 1.6rem; font-weight: 600; margin-bottom: .6rem; }
    .cta-strip p  { color: var(--muted); font-size: .95rem; margin-bottom: 1.5rem; }
</style>
@endpush

@section('content')
<section class="hero">
    <h1>Upload a file.<br><span>Download it later.</span></h1>
    <p>Sign in, keep your own files, and delete them when you don't need them.</p>
    <div class="hero-cta">
        @auth
            <a href="{{ route('dashboard') }}" class="btn-hero btn-hero-primary">Go to Dashboard →</a>
        @else
            <a href="{{ route('register') }}" class="btn-hero btn-hero-primary">Create an account</a>
            <a href="{{ route('login') }}" class="btn-hero btn-hero-secondary">Sign In</a>
        @endauth
    </div>
</section>

<div class="features">
    <div class="feature-card">
        <div class="feature-icon icon-blue">📤</div>
        <h3>Upload</h3>
        <p>Images, documents, and a few other types. 10MB max. The type is checked from the file, not the name.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon icon-purple">📁</div>
        <h3>Your list</h3>
        <p>You only see files you uploaded. Names, sizes, and types are on the dashboard.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon icon-green">⬇️</div>
        <h3>Download</h3>
        <p>The file comes back with the name you gave it.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon icon-amber">🗑️</div>
        <h3>Delete</h3>
        <p>Remove a file when you're done with it.</p>
    </div>
</div>

<hr class="section-divider">

<div class="cta-strip">
    <h2>That's it.</h2>
    <p>An account is just an email and a password.</p>
    @guest
        <a href="{{ route('register') }}" class="btn btn-primary">Create Account</a>
    @else
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Open Dashboard</a>
    @endguest
</div>
@endsection
