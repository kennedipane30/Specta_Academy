@extends('layouts.spekta')

@section('title', 'Management - Announcement')
@section('subtitle', 'Sistem Manajemen Pengumuman Spekta Academy')

@section('content')
@php
    $announcementCollection = method_exists($announcements, 'getCollection') ? $announcements->getCollection() : collect($announcements);
    $totalAnnouncement = method_exists($announcements, 'total') ? $announcements->total() : $announcementCollection->count();
@endphp

<div class="an-page">

    {{-- HEADER --}}
    <section class="an-header">
        <div class="an-header-text">
            <span class="an-kicker">Promosi & Informasi</span>
            <h1>Management Announcement</h1>
            <p>Buat, kelola, dan publikasikan pengumuman untuk pengguna Spekta Academy.</p>
        </div>

        <a href="#announcementForm" class="an-primary-btn">
            <i class="fa-solid fa-plus"></i>
            Buat Pengumuman
        </a>
    </section>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="an-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <div>
                <strong>Berhasil!</strong>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="an-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Data belum valid.</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- STATISTIK (DIBUAT FULL 2 KOLOM) --}}
    <section class="an-stats">
        <div class="an-stat-card">
            <div class="an-stat-icon">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <div class="an-stat-info">
                <p>Total Pengumuman</p>
                <h2>{{ number_format($totalAnnouncement) }}</h2>
            </div>
        </div>

        <div class="an-stat-card">
            <div class="an-stat-icon success-icon">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div class="an-stat-info">
                <p>Bulan Ini</p>
                <h2>{{ number_format($announcementCollection->filter(fn($item) => $item->created_at && $item->created_at->isCurrentMonth())->count()) }}</h2>
            </div>
        </div>
    </section>

    {{-- MAIN FORM & PREVIEW --}}
    <section class="an-main-grid">

        <div class="an-form-panel" id="announcementForm">
            <div class="an-panel-heading">
                <div>
                    <h2>Create New Announcement</h2>
                    <p>Isi judul, gambar sampul, dan deskripsi pengumuman yang akan ditampilkan.</p>
                </div>
            </div>

            <form action="{{ route('admin.announcement.store') }}" method="POST" enctype="multipart/form-data" class="an-form">
                @csrf

                <div class="an-input-group full">
                    <label>Announcement Title</label>
                    <div class="an-input-wrap">
                        <i class="fa-solid fa-heading"></i>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="Enter headline..." required>
                    </div>
                </div>

                <div class="an-input-group full">
                    <label>Cover Image</label>
                    <div class="an-input-wrap">
                        <i class="fa-solid fa-upload"></i>
                        <input type="file" name="image" id="announcementImageInput" accept="image/*" required>
                    </div>
                </div>

                <div class="an-input-group full">
                    <label>Full Description</label>
                    <div class="an-input-wrap no-icon">
                        <textarea name="description" rows="5" placeholder="Write the announcement details here..." required>{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="an-form-action">
                    <button type="submit" class="an-submit-btn">
                        <i class="fa-solid fa-paper-plane"></i>
                        Publish Announcement Now
                    </button>
                </div>
            </form>
        </div>

        <aside class="an-preview-panel">
            <div class="an-panel-heading">
                <h2>Preview Cover</h2>
                <p>Pratinjau gambar pengumuman yang akan diunggah.</p>
            </div>

            <div class="an-preview-image" id="announcementPreviewBox">
                <div class="an-preview-empty">
                    <i class="fa-solid fa-image"></i>
                    <span>Preview gambar akan tampil di sini</span>
                </div>
            </div>

            <div class="an-preview-note">
                <i class="fa-solid fa-circle-info"></i>
                <span>Gunakan gambar yang jelas agar pengumuman terlihat menarik di aplikasi.</span>
            </div>
        </aside>

    </section>

    {{-- LIST PENGUMUMAN --}}
    <section class="an-list-panel">
        <div class="an-panel-heading">
            <div>
                <h2>Daftar Pengumuman</h2>
                <p>Kelola semua pengumuman yang sudah dipublikasikan.</p>
            </div>
        </div>

        <div class="an-card-grid">
            @forelse($announcements as $row)
                <article class="an-card">
                    <div class="an-card-image">
                        @if($row->image)
                            <img src="{{ asset('storage/' . $row->image) }}" alt="{{ $row->title }}">
                        @else
                            <div class="an-no-image">
                                <i class="fa-solid fa-image"></i>
                                <span>No Image</span>
                            </div>
                        @endif

                        <div class="an-date-badge">
                            {{ $row->created_at?->translatedFormat('d M Y') ?? '-' }}
                        </div>
                    </div>

                    <div class="an-card-body">
                        <h3>{{ $row->title }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit($row->description, 120) }}</p>

                        <div class="an-card-meta">
                            <span>
                                <i class="fa-regular fa-clock"></i>
                                {{ $row->created_at?->diffForHumans() ?? '-' }}
                            </span>
                        </div>

                        <div class="an-actions">
                            <a href="{{ route('admin.announcement.edit', $row->announcement_id) }}" class="edit">
                                <i class="fa-solid fa-pen"></i>
                                Edit
                            </a>

                            <form action="{{ route('admin.announcement.destroy', $row->announcement_id) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini secara permanen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="an-empty">
                    <div class="an-empty-icon"><i class="fa-solid fa-bullhorn"></i></div>
                    <strong>Belum ada pengumuman.</strong>
                    <p>Buat pengumuman pertama Anda melalui form di atas.</p>
                </div>
            @endforelse
        </div>

        @if(method_exists($announcements, 'hasPages') && $announcements->hasPages())
            <div class="an-pagination">
                {{ $announcements->links() }}
            </div>
        @endif
    </section>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const announcementImageInput = document.getElementById('announcementImageInput');
        const announcementPreviewBox = document.getElementById('announcementPreviewBox');

        if (announcementImageInput && announcementPreviewBox) {
            announcementImageInput.addEventListener('change', function () {
                const file = this.files[0];

                if (!file) {
                    announcementPreviewBox.innerHTML = `
                        <div class="an-preview-empty">
                            <i class="fa-solid fa-image"></i>
                            <span>Preview gambar akan tampil di sini</span>
                        </div>`;
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (event) {
                    announcementPreviewBox.innerHTML = `<img src="${event.target.result}" alt="Preview Announcement" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">`;
                };
                reader.readAsDataURL(file);
            });
        }
    });
</script>

<style>
    /* BASE LAYOUT */
    .an-page {
        width: 100%;
        font-family: 'Inter', system-ui, sans-serif;
        color: #334155;
    }

    /* HEADER */
    .an-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
    }
    .an-kicker {
        display: block;
        color: #d90429;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .an-header h1 {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: 28px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .an-header p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }
    .an-primary-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #d90429;
        color: #fff;
        border-radius: 12px;
        padding: 12px 20px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(217, 4, 41, 0.2);
    }
    .an-primary-btn:hover {
        background: #b80222;
        transform: translateY(-1px);
    }

    /* ALERTS */
    .an-alert {
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 14px;
    }
    .an-alert.success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .an-alert.error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .an-alert strong { display: block; margin-bottom: 4px; font-weight: 700;}
    .an-alert ul { margin: 4px 0 0; padding-left: 20px; }

    /* STATS (2 KOLOM PENUH) */
    .an-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    .an-stat-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
    .an-stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 24px;
        background: #fef2f2;
        color: #d90429;
    }
    .success-icon { background: #ecfdf5; color: #059669; }
    .an-stat-info p {
        margin: 0 0 4px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .an-stat-info h2 {
        margin: 0;
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    /* MAIN GRID (FORM & PREVIEW) */
    .an-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 350px;
        gap: 24px;
        align-items: start;
        margin-bottom: 24px;
    }
    .an-form-panel, .an-preview-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    }
    .an-preview-panel {
        position: sticky;
        top: 24px;
    }
    .an-panel-heading {
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .an-panel-heading h2 {
        margin: 0 0 6px;
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }
    .an-panel-heading p {
        margin: 0;
        font-size: 13px;
        color: #64748b;
    }

    /* FORM STYLES */
    .an-form {
        display: grid;
        gap: 20px;
    }
    .an-input-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
    }
    .an-input-wrap {
        position: relative;
        display: flex;
    }
    .an-input-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
    }
    .an-input-wrap input,
    .an-input-wrap textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #f8fafc;
        font-size: 14px;
        color: #1e293b;
        font-family: inherit;
        outline: none;
        transition: all 0.2s;
    }
    .an-input-wrap input {
        height: 46px;
        padding: 0 14px 0 40px;
    }
    .an-input-wrap input[type="file"] {
        padding-top: 11px;
    }
    .an-input-wrap.no-icon textarea {
        padding: 12px 14px;
        resize: vertical;
    }
    .an-input-wrap input:focus,
    .an-input-wrap textarea:focus {
        background: #fff;
        border-color: #d90429;
        box-shadow: 0 0 0 3px rgba(217, 4, 41, 0.1);
    }
    .an-form-action {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }
    .an-submit-btn {
        background: #d90429;
        color: #fff;
        border: none;
        padding: 14px 28px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
        box-shadow: 0 4px 12px rgba(217, 4, 41, 0.2);
    }
    .an-submit-btn:hover { background: #b80222; transform: translateY(-1px);}

    /* PREVIEW PANEL STYLES */
    .an-preview-image {
        width: 100%;
        height: 220px;
        border-radius: 12px;
        border: 2px dashed #cbd5e1;
        background: #f8fafc;
        display: grid;
        place-items: center;
        margin-bottom: 16px;
    }
    .an-preview-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #94a3b8;
        font-size: 13px;
        font-weight: 600;
    }
    .an-preview-empty i {
        font-size: 32px;
        margin-bottom: 8px;
        color: #cbd5e1;
    }
    .an-preview-note {
        display: flex;
        gap: 10px;
        padding: 12px 16px;
        border-radius: 10px;
        background: #eff6ff;
        color: #1e40af;
        font-size: 12px;
        line-height: 1.5;
    }
    .an-preview-note i { margin-top: 2px; }

    /* LIST PANEL & CARDS */
    .an-list-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    }
    .an-card-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    .an-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #fff;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .an-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
        transform: translateY(-2px);
    }
    .an-card-image {
        height: 160px;
        background: #f8fafc;
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid #e2e8f0;
    }
    .an-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .an-no-image {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 700;
    }
    .an-no-image i { font-size: 28px; margin-bottom: 8px; color: #cbd5e1; }
    .an-date-badge {
        position: absolute;
        bottom: 12px;
        left: 12px;
        background: rgba(15, 23, 42, 0.85);
        color: #fff;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 800;
        backdrop-filter: blur(4px);
    }
    .an-card-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .an-card-body h3 {
        margin: 0 0 8px;
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.4;
    }
    .an-card-body p {
        margin: 0;
        font-size: 13px;
        color: #64748b;
        line-height: 1.6;
        flex-grow: 1;
    }
    .an-card-meta {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
        font-size: 12px;
        color: #94a3b8;
        font-weight: 600;
        display: flex;
        align-items: center;
    }
    .an-card-meta span { display: flex; align-items: center; gap: 6px; }

    .an-actions {
        display: flex;
        gap: 10px;
        margin-top: 16px;
    }
    .an-actions form { flex: 1; margin: 0; }
    .an-actions a, .an-actions button {
        width: 100%;
        height: 38px;
        border: none;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        font-family: inherit;
        text-decoration: none;
        transition: background 0.2s;
    }
    .an-actions .edit { background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }
    .an-actions .edit:hover { background: #e2e8f0; }
    .an-actions .delete { background: #fef2f2; color: #dc2626; }
    .an-actions .delete:hover { background: #fecaca; }

    /* EMPTY STATE */
    .an-empty {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        background: #f8fafc;
        border-radius: 16px;
        border: 1px dashed #cbd5e1;
    }
    .an-empty-icon {
        width: 64px;
        height: 64px;
        background: #fff;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 24px;
        color: #94a3b8;
        margin: 0 auto 16px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .an-empty strong { display: block; font-size: 16px; color: #1e293b; margin-bottom: 4px;}
    .an-empty p { margin: 0; font-size: 14px; color: #64748b; }

    .an-pagination { margin-top: 24px; }

    /* RESPONSIVE */
    @media (max-width: 1200px) {
        .an-card-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 900px) {
        .an-header { flex-direction: column; align-items: flex-start; }
        .an-stats { grid-template-columns: 1fr; }
        .an-main-grid { grid-template-columns: 1fr; }
        .an-preview-panel { position: static; }
        .an-card-grid { grid-template-columns: 1fr; }
        .an-form-action { justify-content: flex-start; }
        .an-submit-btn { width: 100%; justify-content: center;}
    }
</style>
@endsection
