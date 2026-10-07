# FileVault — Security Documentation

This document describes every security control implemented in the application
and which threat each one addresses.

---

## 1. Security Misconfiguration

**Files:** `.env.example`, `public/.htaccess`, `bootstrap/app.php`, `config/session.php`

| Fix | Where |
|-----|-------|
| `APP_DEBUG=false` — never expose stack traces in production | `.env.example` |
| `LOG_LEVEL=error` — never log debug info in production | `.env.example` |
| `SESSION_ENCRYPT=true` — session payload encrypted at rest | `.env.example` |
| `SESSION_DRIVER=database` — sessions stored server-side, not in cookies | `.env.example` |
| `BCRYPT_ROUNDS=12` — strong bcrypt cost factor explicitly set | `.env.example` |
| Block `.env`, `.log`, `.sqlite`, `.sql`, `.bak` via `.htaccess` | `public/.htaccess` |
| Remove `X-Powered-By` and `Server` headers (stops fingerprinting) | `SecurityHeaders.php` |
| Block common scanner paths (`wp-admin`, `phpmyadmin`, `shell`, etc.) | `public/.htaccess` |
| PHP engine disabled in storage directories | `public/.htaccess` |

---

## 2. Cryptographic Failures

**Files:** `AppServiceProvider.php`, `RegisteredUserController.php`, `.env.example`

| Fix | Detail |
|-----|--------|
| Strong global password policy | min 8 chars, mixed case, numbers, HaveIBeenPwned check |
| `bcrypt` cost factor 12 | Set via `BCRYPT_ROUNDS=12` in env |
| `SESSION_ENCRYPT=true` | Laravel encrypts the session payload using `APP_KEY` (AES-256) |
| `upgrade-insecure-requests` in CSP | Browser upgrades HTTP sub-resources to HTTPS automatically |
| HSTS header | Forces HTTPS at the browser level; cached for 1 year |

---

## 3. Malware / Trojan Upload

**File:** `app/Http/Controllers/FileController.php`

| Fix | Detail |
|-----|--------|
| **MIME whitelist** (not blacklist) | Server reads actual file bytes via PHP `finfo` — cannot be spoofed by renaming |
| **Extension whitelist** | Only `jpg`, `pdf`, `docx`, `zip`, etc. accepted |
| **Dangerous extension hard-block** | `php`, `exe`, `sh`, `bat`, `py`, `js`, `aspx`... always rejected |
| **Double-extension attack blocked** | `evil.php.jpg` detected by scanning all middle extensions |
| **UUID filenames** | Stored name is always `uuid.ext` — no user input in filesystem path |
| **Files outside webroot** | `Storage::disk('local')` — files cannot be accessed via direct URL |
| **Force download header** | Downloads send `Content-Type: application/octet-stream` — browser never renders/runs file |
| **Suspicious upload logging** | Blocked attempts logged with user ID, IP, and user-agent for forensics |

---

## 4. DDoS Mitigation

**Files:** `AppServiceProvider.php`, `routes/web.php`, `LoginRequest.php`

| Fix | Detail |
|-----|--------|
| Upload rate limit | 10 uploads per minute per authenticated user (`throttle:uploads`) |
| Auth rate limit | 10 requests per minute per IP on login/register (`throttle:auth`) |
| Global web rate limit | 120 requests per minute per IP on all web routes (`throttle:web`) |
| Login lockout | 5 failed attempts → 60-second lockout per email+IP |
| Registration rate limit | 5 registrations per minute per IP |
| File size cap | Max 10 MB per upload (enforced server-side) |
| `.htaccess` request body limit | `LimitRequestBody` comment ready to enable in Apache |

---

## 5. Phishing

**Files:** `SecurityHeaders.php`, `public/robots.txt`, `public/.htaccess`

| Fix | Detail |
|-----|--------|
| `X-Frame-Options: DENY` | Page cannot be embedded in iframes on attacker-controlled sites |
| `frame-ancestors 'none'` in CSP | Modern equivalent — overrides X-Frame-Options in modern browsers |
| `form-action 'self'` in CSP | Forms can only submit to this origin — attacker cannot hijack form submissions |
| `robots.txt` disallows auth pages | `/login`, `/register`, `/dashboard` not indexed — harder to clone for phishing |
| `base-uri 'self'` in CSP | Prevents base-tag injection that redirects all relative links to attacker domain |

---

## 6. Zero-Day Exploit Mitigation

**Files:** `SecurityHeaders.php`, `public/.htaccess`

Zero-day exploits cannot be patched directly (by definition), but the attack
surface can be reduced:

| Fix | Detail |
|-----|--------|
| Remove server version headers | Attackers can't fingerprint exact Laravel/PHP version to look up known CVEs |
| Block scanner probe paths in `.htaccess` | Common exploit paths (`/shell`, `/c99`, `/wp-admin`) return 404 before PHP loads |
| Content Security Policy | Limits blast radius of any XSS-based zero-day — injected scripts can't load external resources |
| `upgrade-insecure-requests` | Prevents downgrade attacks that exploit HTTP-only zero-days |
| Files outside webroot | Even a zero-day in upload handling can't make files executable via URL |
| Dependency pinning in `composer.json` | `^12.0` and `^2.10.1` keep deps in known-good ranges |

---

## 7. DNS Spoofing / Cache Poisoning

**Files:** `SecurityHeaders.php`, `AppServiceProvider.php`

DNS spoofing redirects your domain to an attacker's server. The app can't
prevent DNS attacks (that's infrastructure-level), but it can reduce impact:

| Fix | Detail |
|-----|--------|
| `Strict-Transport-Security` (HSTS) | Even if DNS is poisoned and attacker serves HTTP, browser refuses to connect — it cached HTTPS-only for 1 year |
| `preload` in HSTS | Domain can be submitted to browser preload lists — HTTPS enforced before any DNS lookup |
| `includeSubDomains` in HSTS | All subdomains also protected |
| `URL::forceScheme('https')` in production | All generated URLs use HTTPS — no accidental HTTP links to exploit |
| `upgrade-insecure-requests` in CSP | Sub-resources forced to HTTPS — mixed content can't be injected via DNS poisoning |

---

## 8. Vulnerable & Outdated Components

**File:** `composer.json`

| Fix | Detail |
|-----|--------|
| Laravel `^12.0` | Latest major version with active security support |
| `prefer-stable: true` | Only stable releases pulled — no alpha/beta with unknown vulnerabilities |
| `composer audit` | Run this command regularly to check for known CVEs in dependencies |
| No unnecessary packages | Minimal dependency footprint — fewer packages = smaller attack surface |

**Recommended ongoing actions:**
```bash
composer audit          # check for known CVEs in installed packages
composer update         # apply security patches
```

---

## CSRF Protection

Built into Laravel's `VerifyCsrfToken` middleware (runs automatically on all web routes).
Every state-changing form includes `@csrf`. No additional code needed.

