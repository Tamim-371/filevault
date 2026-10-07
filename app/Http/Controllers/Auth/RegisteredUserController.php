<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * RegisteredUserController — secured against:
 *
 *  [DDoS / Mass account creation]
 *      - Rate limited: 5 registrations per IP per minute
 *
 *  [Cryptographic Failures]
 *      - Strong password policy enforced server-side
 *      - Password checked against HaveIBeenPwned breach database (uncompromised())
 *      - bcrypt via Hash::make() with cost factor from BCRYPT_ROUNDS env var (default 12)
 *
 *  [Security Misconfiguration]
 *      - Email lowercased before uniqueness check (prevents duplicate accounts via casing)
 *      - Name sanitised with strip_tags before saving
 */
class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // Rate-limit registrations per IP to slow down bot mass-registration
        $key = 'register|' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Too many registration attempts. Please wait {$seconds} seconds.",
            ]);
        }
        RateLimiter::hit($key, 60);

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:254', 'unique:' . User::class],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()    // upper + lower
                    ->numbers()      // at least one number
                    ->uncompromised(), // HaveIBeenPwned check
            ],
        ]);

        $user = User::create([
            'name'     => strip_tags(trim($request->name)),
            'email'    => strtolower(trim($request->email)),
            'password' => Hash::make($request->password), // bcrypt, cost from BCRYPT_ROUNDS
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
