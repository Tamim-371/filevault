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
    <div class="hero-eyebrow">🔒 Secure · Private · Fast</div>
    <h1>Your files, <span>safely stored</span><br>and always accessible</h1>
    <p>FileVault gives you a personal cloud space to upload, manage, and download your files — all in one clean interface.</p>
    <div class="hero-cta">
        @auth
            <a href="{{ route('dashboard') }}" class="btn-hero btn-hero-primary">Go to Dashboard →</a>
        @else
            <a href="{{ route('register') }}" class="btn-hero btn-hero-primary">Get Started Free</a>
            <a href="{{ route('login') }}" class="btn-hero btn-hero-secondary">Sign In</a>
        @endauth
    </div>
</section>

<div class="features">
    <div class="feature-card">
        <div class="feature-icon icon-blue">📤</div>
        <h3>Easy Uploads</h3>
        <p>Drag and drop or browse to upload any file type up to 50MB per file. Supported formats: images, documents, archives, and more.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon icon-purple">📁</div>
        <h3>Organized Library</h3>
        <p>All your files in one place, sorted by most recent. See file names, sizes, and types at a glance.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon icon-green">⬇️</div>
        <h3>Instant Downloads</h3>
        <p>Download any of your files at any time, from anywhere. Files are served directly with their original filenames.</p>
    </div>
    <div class="feature-card">
        <div class="feature-icon icon-amber">🗑️</div>
        <h3>Full Control</h3>
        <p>Delete files you no longer need. Your storage is yours — manage it exactly how you want.</p>
    </div>
</div>

<hr class="section-divider">

<div class="cta-strip">
    <h2>Ready to get started?</h2>
    <p>Create a free account in seconds — no credit card required.</p>
    @guest
        <a href="{{ route('register') }}" class="btn btn-primary">Create Account</a>
    @else
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Open Dashboard</a>
    @endguest
</div>
@endsection
