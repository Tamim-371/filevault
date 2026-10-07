@extends('layouts.app')

@section('title', 'About')

@push('styles')
<style>
    .about-header {
        text-align: center;
        padding: 2.5rem 1rem 2rem;
    }
    .about-header h1 {
        font-size: 2.4rem;
        font-weight: 600;
        letter-spacing: -.4px;
        margin-bottom: .75rem;
    }
    .about-header p {
        color: var(--muted);
        font-size: 1rem;
        max-width: 480px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .about-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-top: 2.5rem;
    }
    @media (max-width: 640px) { .about-grid { grid-template-columns: 1fr; } }

    .about-block {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.6rem;
    }
    .about-block .block-icon {
        font-size: 1.8rem;
        margin-bottom: .85rem;
    }
    .about-block h3 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: .5rem;
    }
    .about-block p {
        font-size: .9rem;
        color: var(--muted);
        line-height: 1.65;
    }

    .stack-section {
        margin-top: 2.5rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.75rem;
    }
    .stack-section h2 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1.25rem;
        color: var(--text);
    }
    .tech-list {
        display: flex;
        flex-wrap: wrap;
        gap: .65rem;
        list-style: none;
    }
    .tech-list li {
        background: var(--surface2);
        border: 1px solid var(--border);
        border-radius: 100px;
        padding: .35rem .9rem;
        font-size: .83rem;
        font-weight: 500;
        color: var(--text);
        font-family: var(--mono);
    }
    .tech-list li span { margin-right: .35rem; }
</style>
@endpush

@section('content')
<div class="about-header">
    <h1>About</h1>
    <p>A small Laravel app for keeping your own files.</p>
</div>

<div class="about-grid">
    <div class="about-block">
        <div class="block-icon">🎯</div>
        <h3>What it does</h3>
        <p>You sign in, upload a file, and can download or delete it later. Someone else's file returns a 403.</p>
    </div>
    <div class="about-block">
        <div class="block-icon">🔐</div>
        <h3>Sign-in</h3>
        <p>Register, log in, and log out. Passwords have to be mixed case with a number, and they're rejected if they've shown up in a breach.</p>
    </div>
    <div class="about-block">
        <div class="block-icon">💾</div>
        <h3>Where files go</h3>
        <p>On disk under storage/app/uploads, named with a UUID. The original name is only kept in the database.</p>
    </div>
    <div class="about-block">
        <div class="block-icon">⚡</div>
        <h3>Stack</h3>
        <p>Laravel, Blade, and SQLite. No extra frontend framework.</p>
    </div>
</div>

@endsection
