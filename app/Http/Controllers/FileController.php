<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * FileController — secured against:
 *
 *  [1] Malware / Trojan upload
 *      - Server-side finfo MIME detection (not client header)
 *      - Strict WHITELIST of allowed types (not a blacklist)
 *      - Double-extension attack prevention (evil.php.jpg)
 *      - UUID filenames so nothing user-controlled touches the filesystem
 *      - Files stored outside webroot — can never be executed via URL
 *      - Force application/octet-stream on download — browser never renders
 *
 *  [2] Security misconfiguration
 *      - Max upload size enforced server-side (not just client-side)
 *      - Suspicious uploads logged with user ID + IP for audit trail
 *
 *  [3] IDOR (Insecure Direct Object Reference)
 *      - Ownership verified before download and delete
 */
class FileController extends Controller
{
    /**
     * WHITELIST approach — only these MIME types are accepted.
     * Anything not on this list is rejected, period.
     */
    private const ALLOWED_MIMES = [
        // Images
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        // Documents
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        // Plain text
        'text/plain', 'text/csv',
        // Archives
        'application/zip',
        'application/x-zip-compressed',
        'application/x-rar-compressed',
        'application/x-7z-compressed',
        // Media
        'audio/mpeg', 'audio/ogg', 'audio/wav',
        'video/mp4', 'video/webm', 'video/ogg',
    ];

    /**
     * WHITELIST of allowed extensions (must match MIME — both checks must pass).
     */
    private const ALLOWED_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'txt', 'csv',
        'zip', 'rar', '7z',
        'mp3', 'ogg', 'wav',
        'mp4', 'webm',
    ];

    /**
     * Extensions that are NEVER allowed, regardless of claimed MIME type.
     * Defence-in-depth: even if MIME check is bypassed somehow, this blocks.
     */
    private const DANGEROUS_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar', 'phps', 'phpt',
        'exe', 'dll', 'sh', 'bash', 'bat', 'cmd', 'com', 'msi', 'scr',
        'jar', 'jsp', 'asp', 'aspx', 'py', 'pyc', 'rb', 'pl', 'cgi',
        'hta', 'html', 'htm', 'js', 'jsx', 'ts', 'vbs', 'wsf', 'ps1', 'psm1',
        'reg', 'inf', 'apk', 'dex', 'elf', 'so', 'dylib', 'htaccess', 'htpasswd',
    ];

    /**
     * Max file size in KB. Also enforced in validation below.
     */
    private const MAX_SIZE_KB = 10240; // 10 MB

    public function store(Request $request)
    {
        // [A] Size + presence check
        $request->validate([
            'file' => ['required', 'file', 'max:' . self::MAX_SIZE_KB],
        ]);

        $uploaded = $request->file('file');

        // [B] Real MIME via PHP finfo — reads actual file bytes, ignores browser claim
        $realMime = $this->detectRealMime($uploaded->getRealPath());

        // [C] MIME must be on the whitelist
        if (!in_array($realMime, self::ALLOWED_MIMES, true)) {
            $this->logSuspicious($request, 'blocked_mime', $realMime);
            throw ValidationException::withMessages([
                'file' => 'This file type is not allowed.',
            ]);
        }

        // [D] Extension must be on the whitelist
        $ext = strtolower($uploaded->getClientOriginalExtension());
        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            $this->logSuspicious($request, 'blocked_extension', $ext);
            throw ValidationException::withMessages([
                'file' => 'This file extension is not allowed.',
            ]);
        }

        // [E] Extension must NOT be on the danger list (defence-in-depth)
        if (in_array($ext, self::DANGEROUS_EXTENSIONS, true)) {
            $this->logSuspicious($request, 'dangerous_extension', $ext);
            throw ValidationException::withMessages([
                'file' => 'This file type is not allowed.',
            ]);
        }

        // [F] Double-extension check: "malware.php.jpg" → middle part is php
        $originalName = $uploaded->getClientOriginalName();
        $parts = explode('.', $originalName);
        if (count($parts) > 2) {
            foreach (array_slice($parts, 1, -1) as $inner) {
                if (in_array(strtolower($inner), self::DANGEROUS_EXTENSIONS, true)) {
                    $this->logSuspicious($request, 'double_extension', $originalName);
                    throw ValidationException::withMessages([
                        'file' => 'File name contains a disallowed extension.',
                    ]);
                }
            }
        }

        // [G] Sanitise original filename before storing in DB
        $safeOriginalName = $this->sanitizeFilename($originalName);

        // [H] UUID-based stored name — zero user input touches the path
        $storedFilename = Str::uuid()->toString() . '.' . $ext;
        $storedPath = $uploaded->storeAs(
            'uploads/' . auth()->id(),
            $storedFilename,
            'local'  // outside webroot — can never be accessed directly via URL
        );

        File::create([
            'user_id'       => auth()->id(),
            'filename'      => $storedFilename,
            'original_name' => $safeOriginalName,
            'file_path'     => $storedPath,
            'mime_type'     => $realMime,           // store verified MIME, not client claim
            'file_size'     => $uploaded->getSize(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', '"' . $safeOriginalName . '" uploaded successfully.');
    }

    public function download(File $file)
    {
        // IDOR: verify ownership before serving
        if ($file->user_id !== auth()->id()) {
            abort(403);
        }

        if (!Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        // Force download with octet-stream — browser never renders/executes the file
        return Storage::disk('local')->download(
            $file->file_path,
            $file->original_name,
            [
                'Content-Type'              => 'application/octet-stream',
                'X-Content-Type-Options'    => 'nosniff',
                'Content-Disposition'       => 'attachment; filename="' . addslashes($file->original_name) . '"',
            ]
        );
    }

    public function destroy(File $file)
    {
        // IDOR: verify ownership before deleting
        if ($file->user_id !== auth()->id()) {
            abort(403);
        }

        Storage::disk('local')->delete($file->file_path);
        $file->delete();

        return redirect()->route('dashboard')->with('success', 'File deleted successfully.');
    }

    /**
     * Detect the real MIME type by reading actual file bytes via finfo.
     * This cannot be spoofed by changing the Content-Type header.
     */
    private function detectRealMime(string $path): string
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $path);
            finfo_close($finfo);
            if ($mime) return $mime;
        }
        // Fallback to mime_content_type (still reads file bytes)
        return mime_content_type($path) ?: 'application/octet-stream';
    }

    /**
     * Sanitise filename: strip null bytes, path chars, control chars, limit length.
     */
    private function sanitizeFilename(string $name): string
    {
        $name = basename($name);                              // strip any path
        $name = str_replace("\0", '', $name);                 // null bytes
        $name = str_replace(['/', '\\', '..'], '', $name);   // path traversal
        $name = preg_replace('/[\x00-\x1F\x7F]/u', '', $name) ?? $name; // control chars
        $name = mb_substr(trim($name), 0, 255);
        return $name !== '' ? $name : 'unnamed_file';
    }

    /**
     * Log suspicious upload attempts with enough context for forensics.
     */
    private function logSuspicious(Request $request, string $reason, string $detail): void
    {
        Log::warning('Suspicious upload blocked', [
            'reason'  => $reason,
            'detail'  => $detail,
            'user_id' => auth()->id(),
            'ip'      => $request->ip(),
            'ua'      => $request->userAgent(),
        ]);
    }
}
