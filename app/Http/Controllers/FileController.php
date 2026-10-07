<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FileController extends Controller
{
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

    private const ALLOWED_EXTENSIONS = [
        'jpg', 'jpeg', 'png', 'gif', 'webp',
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        'txt', 'csv',
        'zip', 'rar', '7z',
        'mp3', 'ogg', 'wav',
        'mp4', 'webm',
    ];

    private const DANGEROUS_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar', 'phps', 'phpt',
        'exe', 'dll', 'sh', 'bash', 'bat', 'cmd', 'com', 'msi', 'scr',
        'jar', 'jsp', 'asp', 'aspx', 'py', 'pyc', 'rb', 'pl', 'cgi',
        'hta', 'html', 'htm', 'js', 'jsx', 'ts', 'vbs', 'wsf', 'ps1', 'psm1',
        'reg', 'inf', 'apk', 'dex', 'elf', 'so', 'dylib', 'htaccess', 'htpasswd',
    ];

    private const MAX_SIZE_KB = 10240;

    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:' . self::MAX_SIZE_KB],
        ]);

        $uploaded = $request->file('file');

        $realMime = $this->detectRealMime($uploaded->getRealPath());

        if (!in_array($realMime, self::ALLOWED_MIMES, true)) {
            $this->logSuspicious($request, 'blocked_mime', $realMime);
            throw ValidationException::withMessages([
                'file' => 'This file type is not allowed.',
            ]);
        }

        $ext = strtolower($uploaded->getClientOriginalExtension());
        if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
            $this->logSuspicious($request, 'blocked_extension', $ext);
            throw ValidationException::withMessages([
                'file' => 'This file extension is not allowed.',
            ]);
        }

        if (in_array($ext, self::DANGEROUS_EXTENSIONS, true)) {
            $this->logSuspicious($request, 'dangerous_extension', $ext);
            throw ValidationException::withMessages([
                'file' => 'This file type is not allowed.',
            ]);
        }

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

        $safeOriginalName = $this->sanitizeFilename($originalName);

        $storedFilename = Str::uuid()->toString() . '.' . $ext;
        $storedPath = $uploaded->storeAs(
            'uploads/' . auth()->id(),
            $storedFilename,
            'local'
        );

        File::create([
            'user_id'       => auth()->id(),
            'filename'      => $storedFilename,
            'original_name' => $safeOriginalName,
            'file_path'     => $storedPath,
            'mime_type'     => $realMime,
            'file_size'     => $uploaded->getSize(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', '"' . $safeOriginalName . '" uploaded successfully.');
    }

    public function download(File $file)
    {
        if ($file->user_id !== auth()->id()) {
            abort(403);
        }

        if (!Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

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
        if ($file->user_id !== auth()->id()) {
            abort(403);
        }

        Storage::disk('local')->delete($file->file_path);
        $file->delete();

        return redirect()->route('dashboard')->with('success', 'File deleted successfully.');
    }

    private function detectRealMime(string $path): string
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $path);
            finfo_close($finfo);
            if ($mime) return $mime;
        }
        return mime_content_type($path) ?: 'application/octet-stream';
    }

    private function sanitizeFilename(string $name): string
    {
        $name = basename($name);
        $name = str_replace("\0", '', $name);
        $name = str_replace(['/', '\\', '..'], '', $name);
        $name = preg_replace('/[\x00-\x1F\x7F]/u', '', $name) ?? $name;
        $name = mb_substr(trim($name), 0, 255);
        return $name !== '' ? $name : 'unnamed_file';
    }

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
