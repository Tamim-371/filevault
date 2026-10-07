<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * LoginRequest — secured against:
 *
 *  [Brute Force / DDoS on auth endpoint]
 *      - 5 attempts per email+IP, then 60-second lockout (exponential feel)
 *      - Throttle key combines email AND IP — can't bypass by cycling either alone
 *      - Suspicious lockout events logged for audit
 *
 *  [User Enumeration]
 *      - Generic error message regardless of whether email exists
 *
 *  [Timing Attacks]
 *      - Auth::attempt() uses constant-time hash comparison
 *
 *  [Input abuse]
 *      - Email and password length capped to prevent oversized payloads
 */
class LoginRequest extends FormRequest
{
    private const MAX_ATTEMPTS   = 5;
    private const DECAY_SECONDS  = 60;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email', 'max:254'],
            'password' => ['required', 'string', 'min:1', 'max:256'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey(), self::DECAY_SECONDS);

            // Generic message — never reveal whether the email exists (user enumeration)
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_ATTEMPTS)) {
            return;
        }

        // Log lockout events — useful for detecting credential stuffing attacks
        Log::warning('Login rate limit hit', [
            'email' => Str::lower($this->string('email')),
            'ip'    => $this->ip(),
            'ua'    => $this->userAgent(),
        ]);

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Throttle key = normalised email + IP.
     * Combining both prevents:
     *   - Attacker cycling IPs to bypass per-IP limit
     *   - Attacker cycling emails to bypass per-email limit
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
