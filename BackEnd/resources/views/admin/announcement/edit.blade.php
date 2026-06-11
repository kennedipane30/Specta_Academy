@extends('layouts.spekta')

@section('title', 'Edit Announcement')
@section('subtitle', 'Perbarui data pengumuman Spekta Academy')

@section('content')
<div class="ae-page">

    {{-- ── 1. HEADER MINIMALIS MODERN ── --}}
    <section class="ae-header">
        <div class="ae-header-left">
            <span class="ae-breadcrumb-capsule">Announcement Editor</span>
            <h1>Edit Announcement</h1>
            <p>Perbarui judul, deskripsi, atau gambar pengumuman yang sudah dipublikasikan.</p>
        </div>
        <div class="ae-header-actions">
            <a href="{{ route('admin.announcement.index') }}" class="ae-secondary-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </section>

    @if($errors->any())
        <div class="ae-alert error">
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

    {{-- ── 2. FORM GRID WITH REAL-TIME PREVIEW ── --}}
    <section class="ae-grid">
        <form action="{{ route('admin.announcement.update', $announcement->announcement_id) }}" method="POST" enctype="multipart/form-data" class="ae-form-card">
            @csrf
            @method('PUT')

            <div class="ae-card-heading">
                <div>
                    <h2>Form Edit Pengumuman</h2>
                    <p>Cukup ubah data pengumuman yang ingin diperbarui.</p>
                </div>

                <div class="ae-heading-icon">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
            </div>

            <div class="ae-input-group">
                <label>Headline Title</label>
                <div>
                    <i class="fa-solid fa-heading"></i>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title', $announcement->title) }}"
                        required
                    >
                </div>
            </div>

            <div class="ae-input-group full">
                <label>Content Details</label>
                <textarea name="description" rows="7" required>{{ old('description', $announcement->description) }}</textarea>
            </div>

            <div class="ae-input-group">
                <label>Replace Visual Media</label>
                <div>
                    <i class="fa-solid fa-upload"></i>
                    <input
                        type="file"
                        name="image"
                        id="announcementImageInput"
                        accept="image/*"
                    >
                </div>
            </div>

            <div class="ae-current-info">
                <div class="ae-mini-avatar">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div>
                    <strong>{{ $announcement->title }}</strong>
                    <span>Dibuat {{ $announcement->created_at?->translatedFormat('d M Y, H:i') }}</span>
                    <small>Kosongkan file jika tidak ingin mengganti gambar lama.</small>
                </div>
            </div>

            <div class="ae-actions">
                <a href="{{ route('admin.announcement.index') }}" class="ae-cancel-btn">Batal</a>

                <button type="submit" class="ae-submit-btn">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Update Announcement Data
                </button>
            </div>
        </form>

        <aside class="ae-preview-card">
            <div class="ae-preview-heading">
                <h3>Visual Preview</h3>
                <p>Gambar saat ini atau gambar baru yang dipilih.</p>
            </div>

            <div class="ae-preview-image" id="announcementPreviewBox">
                @if($announcement->image)
                    <img src="{{ asset('storage/' . $announcement->image) }}" alt="{{ $announcement->title }}">
                @else
                    <div>
                        <i class="fa-solid fa-image"></i>
                        <span>Tidak ada gambar</span>
                    </div>
                @endif
            </div>

            <div class="ae-preview-note">
                <i class="fa-solid fa-circle-info"></i>
                <span>Gambar yang rapi akan membuat pengumuman terlihat lebih profesional pada aplikasi.</span>
            </div>
        </aside>
    </section>

</div>

<script>
    const announcementImageInput = document.getElementById('announcementImageInput');
    const announcementPreviewBox = document.getElementById('announcementPreviewBox');

    if (announcementImageInput && announcementPreviewBox) {
        announcementImageInput.addEventListener('change', function () {
            const file = this.files[0];

            if (!file) return;

            const reader = new FileReader();

            reader.onload = function (event) {
                announcementPreviewBox.innerHTML = `<img src="${event.target.result}" alt="Preview Announcement">`;
            };

            reader.readAsDataURL(file);
        });
    }
</script>

<style>
    :root {
        --spekta-red-dark: #c5352c;
        --spekta-red: #e53935;
        --spekta-teal: #2ea8ab;
        --spekta-teal-light: rgba(46, 168, 171, 0.08);
        --spekta-red-light: rgba(229, 57, 53, 0.06);
        --spekta-gray: #9e9e9e;
        --spekta-gray-light: #f3f4f6;
        --spekta-white: #ffffff;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border-soft: #e5e7eb;
    }

    .ae-page {
        width: 100%;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        padding-bottom: 40px; /* Tambahan ruang bernapas di bawah */
    }

    /* Header Minimalis */
    .ae-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }
    .ae-breadcrumb-capsule {
        display: inline-block;
        background: var(--spekta-red-light);
        color: var(--spekta-red-dark);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 6px;
        margin-bottom: 8px;
    }
    .ae-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .ae-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }
    .ae-secondary-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid var(--border-soft);
        background: var(--spekta-white);
        color: var(--text-muted);
        border-radius: 12px;
        padding: 10px 16px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s;
    }
    .ae-secondary-btn:hover {
        background: var(--spekta-gray-light);
        color: var(--text-main);
        border-color: var(--spekta-gray);
    }

    .ae-alert {
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 24px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 800;
    }
    .ae-alert.error {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .ae-alert ul { margin: 4px 0 0; padding-left: 18px; }

    /* Layout Form Grid */
    .ae-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 350px;
        gap: 24px;
        align-items: start;
    }

    .ae-form-card,
    .ae-preview-card {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
        padding: 20px;
    }

    .ae-card-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 20px;
    }
    .ae-card-heading h2,
    .ae-preview-heading h3 {
        margin: 0;
        color: var(--text-main);
        font-size: 15px;
        font-weight: 800;
    }
    .ae-card-heading p,
    .ae-preview-heading p {
        margin: 6px 0 0;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 600;
        line-height: 1.5;
    }

    .ae-heading-icon {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: var(--spekta-red-light);
        color: var(--spekta-red);
        flex-shrink: 0;
    }

    .ae-form-card {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 15px;
    }

    .ae-card-heading,
    .ae-input-group.full,
    .ae-current-info,
    .ae-actions {
        grid-column: 1 / -1;
    }

    .ae-input-group label {
        display: block;
        margin-bottom: 6px;
        color: var(--text-muted);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .ae-input-group div { position: relative; }
    .ae-input-group i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--spekta-gray);
        font-size: 13px;
    }

    .ae-input-group input,
    .ae-input-group textarea {
        width: 100%;
        border: 1px solid var(--border-soft);
        border-radius: 10px;
        background: var(--spekta-gray-light);
        outline: none;
        color: var(--text-main);
        font-size: 12px;
        font-weight: 600;
        font-family: inherit;
        transition: all 0.25s;
    }

    .ae-input-group input {
        height: 40px;
        padding: 0 14px 0 38px;
    }

    .ae-input-group textarea {
        resize: vertical;
        padding: 12px 14px;
        line-height: 1.5;
    }

    .ae-input-group input[type="file"] {
        padding-top: 10px;
    }

    .ae-input-group input:focus,
    .ae-input-group textarea:focus {
        background: var(--spekta-white);
        border-color: var(--spekta-teal);
        box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12);
    }

    /* Current Info */
    .ae-current-info {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 12px;
        background: var(--spekta-gray-light);
        border: 1px solid var(--border-soft);
    }
    .ae-mini-avatar {
        width: 44px;
        height: 44px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: var(--spekta-red-light);
        color: var(--spekta-red);
        font-size: 14px;
        flex-shrink: 0;
    }
    .ae-current-info strong { display: block; color: var(--text-main); font-size: 12px; font-weight: 800; }
    .ae-current-info span { display: block; color: var(--text-muted); font-size: 10px; font-weight: 700; margin-top: 2px; }
    .ae-current-info small { display: block; color: var(--spekta-red); font-size: 9px; font-weight: 800; margin-top: 2px; }

    /* Form Actions Row */
    .ae-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 10px;
    }

    .ae-cancel-btn,
    .ae-submit-btn {
        height: 40px;
        border-radius: 10px;
        padding: 0 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 800;
        font-family: inherit;
        text-decoration: none;
        transition: all 0.2s;
    }

    .ae-cancel-btn {
        background: var(--spekta-gray-light);
        color: var(--text-main);
    }
    .ae-cancel-btn:hover { background: var(--border-soft); }

    .ae-submit-btn {
        border: none;
        background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%);
        color: var(--spekta-white);
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(229, 57, 53, 0.2);
    }
    .ae-submit-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(229, 57, 53, 0.3);
    }

    .ae-preview-card {
        position: sticky;
        top: 20px;
    }
    .ae-preview-heading { margin-bottom: 14px; }

    /* Preview Image Box */
    .ae-preview-image {
        width: 100%;
        height: 220px;
        border-radius: 12px;
        border: 1px dashed var(--spekta-gray);
        background: var(--spekta-gray-light);
        overflow: hidden;
        display: grid;
        place-items: center;
        text-align: center;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
    }
    .ae-preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .ae-preview-image i {
        display: block;
        color: var(--spekta-red);
        font-size: 24px;
        margin-bottom: 6px;
    }

    .ae-preview-note {
        margin-top: 14px;
        display: flex;
        gap: 8px;
        padding: 12px;
        border-radius: 10px;
        background: #fff7f9;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 600;
        line-height: 1.5;
    }
    .ae-preview-note i { color: var(--spekta-red); margin-top: 2px; }

    @media (max-width: 1100px) {
        .ae-grid { grid-template-columns: 1fr; }
        .ae-preview-card { position: static; }
    }

    @media (max-width: 768px) {
        .ae-header { flex-direction: column; align-items: flex-start; gap: 14px; }
        .ae-form-card { grid-template-columns: 1fr; }
        .ae-actions { flex-direction: column-reverse; }
        .ae-cancel-btn, .ae-submit-btn { width: 100%; }
    }
</style>
@endsection