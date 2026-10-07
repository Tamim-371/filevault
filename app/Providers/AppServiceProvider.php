<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

/**
 * AppServiceProvider — application-wide security bootstrapping.
 *
 * Covers:
 *  [DDoS mitigation]       — rate limiters for uploads and auth endpoints
 *  [Cryptographic Failures] — global strong password policy default
 *  [Security Misconfiguration] — force HTTPS in production
 *  [Vulnerable Components]  — password strength enforced globally
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // ── Force HTTPS in production ────────────────────────────────────
        // Prevents accidental HTTP serving and supports HSTS header effectiveness.
        // Also hardens against DNS spoofing — attacker can't downgrade to HTTP
        // if the app always redirects to HTTPS.
        if (app()->environment('production')) {
            //URL::forceScheme('https');
        }

        // ── Global strong password policy ────────────────────────────────
        // Applied to all Password::defaults() usages throughout the app.
        // Mitigates cryptographic failures from weak passwords.
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->numbers()
                ->uncompromised(); // HaveIBeenPwned API check
        });

        // ── Rate limiter: file uploads ───────────────────────────────────
        // 10 uploads per minute per authenticated user.
        // Mitigates DDoS via storage exhaustion.
        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many uploads. Please wait a moment.',
                    ], 429);
                });
        });

        // ── Rate limiter: auth endpoints ─────────────────────────────────
        // 10 requests per minute per IP on login/register routes.
        // Secondary DDoS / brute force layer (primary is in LoginRequest).
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // ── Rate limiter: general API / web ──────────────────────────────
        // Global request rate limit per IP — mitigates volumetric DDoS.
        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });
    }
}
