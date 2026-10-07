@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    /* ─── TOP BAR ──────────────────────────────── */
    .dash-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.75rem;
    }
    .dash-header h1 { font-size: 1.6rem; font-weight: 600; letter-spacing: -.3px; }
    .dash-header .welcome { font-size: .9rem; color: var(--muted); margin-top: .15rem; }

    /* ─── STATS ────────────────────────────────── */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
        margin-bottom: 1.75rem;
    }
    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 1.1rem 1.3rem;
    }
    .stat-card .stat-label { font-size: .78rem; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; margin-bottom: .3rem; }
    .stat-card .stat-value { font-size: 1.5rem; font-weight: 600; color: var(--text); font-variant-numeric: tabular-nums; }
    .stat-card .stat-sub   { font-size: .82rem; color: var(--muted); margin-top: .2rem; }

    /* ─── UPLOAD ───────────────────────────────── */
    .upload-zone {
        background: var(--surface);
        border: 2px dashed var(--border);
        border-radius: var(--radius-lg);
        padding: 2rem 1.5rem;
        text-align: center;
        margin-bottom: 1.75rem;
        transition: border-color .2s, background .2s;
        cursor: pointer;
        position: relative;
    }
    .upload-zone.drag-over {
        border-color: var(--accent);
        background: rgba(79,142,247,.05);
    }
    .upload-zone .upload-icon { font-size: 2.2rem; margin-bottom: .65rem; }
    .upload-zone h3 { font-size: 1rem; font-weight: 600; margin-bottom: .35rem; }
    .upload-zone p  { font-size: .85rem; color: var(--muted); margin-bottom: 1.1rem; }
    .upload-zone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
    }
    .upload-preview {
        display: none;
        align-items: center;
        gap: .75rem;
        background: var(--surface2);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: .75rem 1rem;
        margin-top: .75rem;
        text-align: left;
        font-size: .9rem;
    }
    .upload-preview.visible { display: flex; }
    .upload-preview .file-name { flex: 1; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .upload-preview .file-size { color: var(--muted); font-size: .82rem; white-space: nowrap; }

    /* ─── FILE TABLE ───────────────────────────── */
    .files-section { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; }
    .files-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.1rem 1.5rem;
        border-bottom: 1px solid var(--border);
    }
    .files-header h2 { font-size: 1rem; font-weight: 600; }
    .files-header .file-count {
        background: var(--surface2);
        border: 1px solid var(--border);
        border-radius: 100px;
        padding: .2rem .7rem;
        font-size: .78rem;
        color: var(--muted);
        font-family: var(--mono);
    }

    .file-table { width: 100%; border-collapse: collapse; }
    .file-table th {
        text-align: left;
        font-size: .75rem;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .05em;
        padding: .7rem 1.5rem;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
    }
    .file-table td {
        padding: .9rem 1.5rem;
        border-bottom: 1px solid rgba(37,42,58,.6);
        font-size: .9rem;
        vertical-align: middle;
    }
    .file-table tr:last-child td { border-bottom: none; }
    .file-table tr:hover td { background: rgba(255,255,255,.02); }

    .file-name-cell {
        display: flex;
        align-items: center;
        gap: .65rem;
        max-width: 280px;
    }
    .file-type-badge {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .badge-image   { background: rgba(52,211,153,.1); }
    .badge-pdf     { background: rgba(248,113,113,.1); }
    .badge-video   { background: rgba(251,191,36,.1); }
    .badge-archive { background: rgba(124,92,252,.1); }
    .badge-file    { background: rgba(107,114,128,.1); }
    .badge-document{ background: rgba(79,142,247,.1); }

    .file-original-name {
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 220px;
        display: block;
    }
    .file-date { color: var(--muted); font-size: .82rem; font-family: var(--mono); white-space: nowrap; }
    .file-size-cell { color: var(--muted); font-family: var(--mono); font-size: .85rem; white-space: nowrap; }
    .file-mime { color: var(--subtle); font-size: .78rem; font-family: var(--mono); }
    .file-actions { display: flex; gap: .5rem; justify-content: flex-end; }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 3.5rem 2rem;
        color: var(--muted);
    }
    .empty-state .empty-icon { font-size: 2.8rem; margin-bottom: 1rem; opacity: .5; }
    .empty-state h3 { font-size: 1rem; font-weight: 500; margin-bottom: .5rem; color: var(--text); opacity: .7; }
    .empty-state p  { font-size: .88rem; }

    @media (max-width: 640px) {
        .file-table .hide-mobile { display: none; }
        .file-name-cell { max-width: 160px; }
        .file-original-name { max-width: 130px; }
    }
</style>
@endpush

@section('content')

<div class="dash-header">
    <div>
        <h1>Dashboard</h1>
        <p class="welcome">Welcome back, {{ auth()->user()->name }} 👋</p>
    </div>
</div>

<!-- Stats -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-label">Total Files</div>
        <div class="stat-value">{{ $totalFiles }}</div>
        <div class="stat-sub">uploaded by you</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Storage Used</div>
        @php
            $bytes = $totalSize;
            if ($bytes < 1024) $formatted = $bytes . ' B';
            elseif ($bytes < 1048576) $formatted = round($bytes/1024, 1) . ' KB';
            elseif ($bytes < 1073741824) $formatted = round($bytes/1048576, 1) . ' MB';
            else $formatted = round($bytes/1073741824, 2) . ' GB';
        @endphp
        <div class="stat-value">{{ $formatted }}</div>
        <div class="stat-sub">across all files</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Account</div>
        <div class="stat-value" style="font-size:1rem;margin-top:.25rem;">{{ auth()->user()->email }}</div>
        <div class="stat-sub">member since {{ auth()->user()->created_at->format('M Y') }}</div>
    </div>
</div>

<!-- Upload Zone -->
<form method="POST" action="{{ route('files.store') }}" enctype="multipart/form-data" id="upload-form">
    @csrf
    <div class="upload-zone" id="upload-zone">
        <input type="file" name="file" id="file-input" accept="*/*">
        <div class="upload-icon">📤</div>
        <h3>Drop a file here or click to browse</h3>
        <p>Any file type · Max 50 MB per upload</p>
        <div class="upload-preview" id="upload-preview">
            <span>📄</span>
            <span class="file-name" id="preview-name"></span>
            <span class="file-size" id="preview-size"></span>
            <button type="submit" class="btn btn-primary btn-sm" onclick="event.stopPropagation()">Upload</button>
        </div>
    </div>

    @error('file')
        <div class="alert alert-error" style="margin-top:.75rem;">✗ {{ $message }}</div>
    @enderror
</form>

<!-- File List -->
<div class="files-section">
    <div class="files-header">
        <h2>Your Files</h2>
        <span class="file-count">{{ $totalFiles }} {{ Str::plural('file', $totalFiles) }}</span>
    </div>

    @if($files->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">🗂️</div>
            <h3>No files yet</h3>
            <p>Upload your first file using the area above.</p>
        </div>
    @else
        <table class="file-table">
            <thead>
                <tr>
                    <th>File</th>
                    <th class="hide-mobile">Type</th>
                    <th class="hide-mobile">Size</th>
                    <th class="hide-mobile">Uploaded</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($files as $file)
                    @php
                        $icon = match($file->icon) {
                            'image'      => ['🖼️', 'badge-image'],
                            'pdf'        => ['📕', 'badge-pdf'],
                            'video'      => ['🎬', 'badge-video'],
                            'archive'    => ['📦', 'badge-archive'],
                            'document'   => ['📝', 'badge-document'],
                            'spreadsheet'=> ['📊', 'badge-document'],
                            default      => ['📄', 'badge-file'],
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="file-name-cell">
                                <div class="file-type-badge {{ $icon[1] }}">{{ $icon[0] }}</div>
                                <span class="file-original-name" title="{{ $file->original_name }}">{{ $file->original_name }}</span>
                            </div>
                        </td>
                        <td class="hide-mobile">
                            <span class="file-mime">{{ $file->mime_type }}</span>
                        </td>
                        <td class="hide-mobile">
                            <span class="file-size-cell">{{ $file->formatted_size }}</span>
                        </td>
                        <td class="hide-mobile">
                            <span class="file-date">{{ $file->created_at->format('d M Y') }}</span>
                        </td>
                        <td>
                            <div class="file-actions">
                                <a href="{{ route('files.download', $file) }}" class="btn btn-ghost btn-sm">⬇ Download</a>
                                <form method="POST" action="{{ route('files.destroy', $file) }}" class="delete-file-form" data-filename="{{ $file->original_name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">🗑</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection

@push('scripts')
<script>
    const fileInput   = document.getElementById('file-input');
    const uploadZone  = document.getElementById('upload-zone');
    const preview     = document.getElementById('upload-preview');
    const previewName = document.getElementById('preview-name');
    const previewSize = document.getElementById('preview-size');

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes/1024).toFixed(1) + ' KB';
        return (bytes/1048576).toFixed(1) + ' MB';
    }

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (file) {
            previewName.textContent = file.name;
            previewSize.textContent = formatSize(file.size);
            preview.classList.add('visible');
        }
    });

    uploadZone.addEventListener('dragover', (e) => { e.preventDefault(); uploadZone.classList.add('drag-over'); });
    uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('drag-over'));
    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('drag-over');
        const dt = e.dataTransfer;
        if (dt.files.length) {
            fileInput.files = dt.files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });

    // Secure delete confirmation — filename is read from a data attribute
    // so it is properly HTML-escaped by Blade and safely handled in JS.
    document.querySelectorAll('.delete-file-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            const name = this.dataset.filename || 'this file';
            if (!confirm('Delete "' + name.replace(/"/g, '\\"') + '"?')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
