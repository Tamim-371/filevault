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
    <h1>About FileVault</h1>
    <p>A clean, minimal file storage application built to demonstrate Laravel 12 and modern Blade templating.</p>
</div>

<div class="about-grid">
    <div class="about-block">
        <div class="block-icon">🎯</div>
        <h3>Purpose</h3>
        <p>FileVault is a personal file management system. Authenticated users can upload, browse, download, and delete their own files — all stored securely on the server.</p>
    </div>
    <div class="about-block">
        <div class="block-icon">🔐</div>
        <h3>Authentication</h3>
        <p>Built on Laravel Breeze, FileVault provides a complete authentication flow including registration, login, and logout functionality with session management.</p>
    </div>
    <div class="about-block">
        <div class="block-icon">💾</div>
        <h3>Storage</h3>
        <p>Files are stored in Laravel's local filesystem under <code style="font-family:var(--mono);font-size:.85em;background:var(--surface2);padding:.1rem .35rem;border-radius:4px;">storage/app/uploads</code>. File metadata is kept in a SQLite database for fast querying.</p>
    </div>
    <div class="about-block">
        <div class="block-icon">⚡</div>
        <h3>Performance</h3>
        <p>SQLite keeps the stack simple and dependency-free. The app is fully server-rendered with Blade templates — no JavaScript frameworks required.</p>
    </div>
</div>

<div class="stack-section">
    <h2>Tech Stack</h2>
    <ul class="tech-list">
        <li><span>🐘</span>PHP 8.2+</li>
        <li><span>🔴</span>Laravel 12</li>
        <li><span>🔑</span>Laravel Breeze</li>
        <li><span>🗄️</span>SQLite</li>
        <li><span>🔷</span>Blade Templates</li>
        <li><span>🎨</span>Pure CSS</li>
        <li><span>📦</span>Eloquent ORM</li>
        <li><span>🛤️</span>Laravel Routing</li>
    </ul>
</div>
@endsection
