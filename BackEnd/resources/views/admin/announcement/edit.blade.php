@extends('layouts.spekta')

@section('title', 'Edit Announcement')
@section('subtitle', 'Perbarui data pengumuman Spekta Academy')

@section('content')
<div class="ae-page">

    <section class="ae-hero">
        <div>
            <span>Announcement Editor</span>
            <h1>Edit Announcement</h1>
            <p>Perbarui judul, deskripsi, atau gambar pengumuman yang sudah dipublikasikan.</p>
        </div>

        <a href="{{ route('admin.announcement.index') }}">
            <i class="fa-solid fa-arrow-left"></i>
            Back to List
        </a>
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

    <section class="ae-grid">
        <form action="{{ route('admin.announcement.update', $announcement->announcement_id) }}" method="POST" enctype="multipart/form-data" class="ae-form-card">
            @csrf
            @method('PUT')

            <div class="ae-card-heading">
                <div>
                    <h2>Form Edit Pengumuman</h2>
                    <p>Password tidak diperlukan. Cukup ubah data pengumuman yang ingin diperbarui.</p>
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
                    <small>Kosongkan file jika tidak ingin mengganti gambar.</small>
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
    .ae-page {
        width: 100%;
    }

    .ae-hero {
        background: linear-gradient(120deg, #c90025 0%, #7b001b 48%, #351225 100%);
        color: #fff;
        border-radius: 22px;
        padding: 30px 34px;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        box-shadow: 0 18px 35px rgba(134, 0, 24, .22);
    }

    .ae-hero span {
        display: block;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        opacity: .78;
        margin-bottom: 8px;
    }

    .ae-hero h1 {
        margin: 0 0 8px;
        font-size: 25px;
        font-weight: 900;
    }

    .ae-hero p {
        margin: 0;
        font-size: 13px;
        font-weight: 500;
        opacity: .9;
    }

    .ae-hero a {
        height: 42px;
        padding: 0 15px;
        display: inline-flex;
        align-items: center;
        gap: 9px;
        border-radius: 12px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.22);
        color: #fff;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .ae-alert {
        border-radius: 16px;
        padding: 15px 17px;
        margin-bottom: 18px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 700;
    }

    .ae-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .ae-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .ae-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 410px;
        gap: 22px;
        align-items: start;
    }

    .ae-form-card,
    .ae-preview-card {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
        padding: 24px;
    }

    .ae-form-card {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .ae-card-heading,
    .ae-input-group.full,
    .ae-current-info,
    .ae-actions {
        grid-column: 1 / -1;
    }

    .ae-card-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 6px;
    }

    .ae-card-heading h2,
    .ae-preview-heading h3 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .ae-card-heading p,
    .ae-preview-heading p {
        margin: 7px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
    }

    .ae-heading-icon {
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        border-radius: 16px;
        background: #ffe8ee;
        color: #d90429;
        flex-shrink: 0;
    }

    .ae-input-group label {
        display: block;
        margin-bottom: 8px;
        color: #374151;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .ae-input-group div {
        position: relative;
    }

    .ae-input-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 13px;
    }

    .ae-input-group input,
    .ae-input-group textarea {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
        outline: none;
        color: #111827;
        font-size: 13px;
        font-weight: 700;
        font-family: inherit;
    }

    .ae-input-group input {
        height: 48px;
        padding: 0 15px 0 42px;
    }

    .ae-input-group input[type="file"] {
        padding-top: 13px;
    }

    .ae-input-group textarea {
        resize: vertical;
        padding: 14px 15px;
        line-height: 1.5;
    }

    .ae-input-group input:focus,
    .ae-input-group textarea:focus {
        background: #fff;
        border-color: #fecdd3;
        box-shadow: 0 0 0 4px rgba(217, 4, 41, .08);
    }

    .ae-current-info {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #edf0f4;
    }

    .ae-mini-avatar {
        width: 54px;
        height: 54px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #ffe8ee;
        color: #d90429;
        font-size: 18px;
        flex-shrink: 0;
    }

    .ae-current-info strong {
        display: block;
        color: #111827;
        font-size: 14px;
        font-weight: 900;
    }

    .ae-current-info span,
    .ae-current-info small {
        display: block;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        margin-top: 3px;
    }

    .ae-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 6px;
    }

    .ae-cancel-btn,
    .ae-submit-btn {
        height: 46px;
        border-radius: 13px;
        padding: 0 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        font-size: 12px;
        font-weight: 900;
        font-family: inherit;
    }

    .ae-cancel-btn {
        background: #f3f4f6;
        color: #374151;
    }

    .ae-submit-btn {
        border: none;
        background: #d90429;
        color: #fff;
        cursor: pointer;
        box-shadow: 0 13px 25px rgba(217, 4, 41, .18);
    }

    .ae-preview-card {
        position: sticky;
        top: 20px;
    }

    .ae-preview-heading {
        margin-bottom: 18px;
    }

    .ae-preview-image {
        width: 100%;
        height: 260px;
        border-radius: 18px;
        border: 1px dashed #d1d5db;
        background: #f8fafc;
        overflow: hidden;
        display: grid;
        place-items: center;
        text-align: center;
        color: #6b7280;
        font-size: 12px;
        font-weight: 800;
    }

    .ae-preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ae-preview-image i {
        display: block;
        color: #d90429;
        font-size: 30px;
        margin-bottom: 8px;
    }

    .ae-preview-note {
        margin-top: 16px;
        display: flex;
        gap: 10px;
        padding: 14px;
        border-radius: 14px;
        background: #fff7f9;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.5;
    }

    .ae-preview-note i {
        color: #d90429;
        margin-top: 2px;
    }

    @media (max-width: 1100px) {
        .ae-grid {
            grid-template-columns: 1fr;
        }

        .ae-preview-card {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .ae-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .ae-form-card {
            grid-template-columns: 1fr;
        }

        .ae-actions {
            flex-direction: column-reverse;
        }

        .ae-cancel-btn,
        .ae-submit-btn {
            width: 100%;
        }
    }
</style>
@endsection