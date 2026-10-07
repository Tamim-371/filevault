<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeaders — applied to every response.
 *
 * Protects against:
 *  [Security Misconfiguration]
 *      - Removes X-Powered-By and Server headers that reveal technology stack
 *      - Enforces HSTS on HTTPS to prevent protocol downgrade
 *
 *  [Phishing / Clickjacking]
 *      - X-Frame-Options: DENY — page cannot be embedded in iframes on other sites
 *      - frame-ancestors 'none' in CSP — modern equivalent
 *      - form-action 'self' — forms can only submit to this origin (blocks phishing form hijack)
 *
 *  [Zero-Day / XSS mitigation]
 *      - Content-Security-Policy — restricts what scripts, styles, and resources can load
 *      - X-XSS-Protection — activates legacy browser XSS auditor as a fallback
 *
 *  [DNS Spoofing / Cache Poisoning mitigation]
 *      - Strict-Transport-Security — forces HTTPS, preventing downgrade to HTTP where
 *        DNS poisoning is easiest to exploit (attacker-controlled HTTP responses)
 *      - Referrer-Policy — limits what URL info leaks to third parties
 *
 *  [Cryptographic Failures]
 *      - upgrade-insecure-requests in CSP — browser upgrades all HTTP sub-resources to HTTPS
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // ── Clickjacking / Phishing ──────────────────────────────────────
        $response->headers->set('X-Frame-Options', 'DENY');

        // ── MIME Sniffing ────────────────────────────────────────────────
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // ── Legacy XSS filter ───────────────────────────────────────────
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // ── Referrer leakage ────────────────────────────────────────────
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // ── Browser feature access ───────────────────────────────────────
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), payment=(), usb=(), interest-cohort=()'
        );

        // ── Content Security Policy ──────────────────────────────────────
        // upgrade-insecure-requests: sub-resources load over HTTPS even if linked as HTTP
        // form-action 'self': blocks phishing attacks that hijack form submissions
        // frame-ancestors 'none': modern clickjacking protection
        // base-uri 'self': prevents base-tag injection attacks
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline'; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
            "font-src 'self' https://fonts.gstatic.com; " .
            "img-src 'self' data: blob:; " .
            "connect-src 'self'; " .
            "frame-ancestors 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self'; " .
            "upgrade-insecure-requests;"
        );

        // ── HSTS — forces HTTPS, hardens against DNS spoofing/downgrade ─
        // Even if an attacker poisons DNS to redirect to HTTP, the browser
        // refuses to connect over HTTP for the max-age duration.
        if ($request->isSecure() || app()->environment('production')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // ── Remove headers that leak server info ─────────────────────────
        // Attackers use X-Powered-By / Server to fingerprint and target known vulns
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // ── Cache control for authenticated pages ────────────────────────
        // Prevents sensitive dashboard content from being cached by proxies/browsers
        if ($request->is('dashboard*', 'files*', 'profile*')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}
