@extends('layouts.spekta')

@section('title', 'Tambah Program Baru')
@section('subtitle', 'Publikasikan program kelas baru ke aplikasi siswa')

@section('content')
<div class="cpf-page">

    <section class="cpf-hero">
        <div>
            <span>Program Builder</span>
            <h1>Tambah Program Baru</h1>
            <p>Data ini akan otomatis muncul di aplikasi mobile siswa setelah disimpan.</p>
        </div>

        <a href="{{ route('admin.classes.index') }}">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </section>

    @if ($errors->any())
        <div class="cpf-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Terjadi Kesalahan Input</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <section class="cpf-grid">
        <form action="{{ route('admin.classes.store') }}" method="POST" enctype="multipart/form-data" class="cpf-form-card">
            @csrf

            <div class="cpf-card-heading">
                <div>
                    <h2>Informasi Program</h2>
                    <p>Lengkapi nama program, harga, deskripsi, dan visual banner.</p>
                </div>

                <div class="cpf-heading-icon">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
            </div>

            <div class="cpf-input-group">
                <label>Nama Program</label>
                <div>
                    <i class="fa-solid fa-graduation-cap"></i>
                    <input type="text" name="program_name" value="{{ old('program_name') }}" placeholder="Contoh: PTN & UNHAN" required>
                </div>
            </div>

            <div class="cpf-input-group">
                <label>Harga Investasi</label>
                <div>
                    <i class="fa-solid fa-rupiah-sign"></i>
                    <input type="number" name="price" value="{{ old('price') }}" placeholder="900000" required>
                </div>
            </div>

            <div class="cpf-input-group full">
                <label>Deskripsi Aplikasi</label>
                <textarea name="description" rows="6" placeholder="Tuliskan deskripsi lengkap program..." required>{{ old('description') }}</textarea>
            </div>

            <div class="cpf-input-group">
                <label>Visual Banner</label>
                <div>
                    <i class="fa-solid fa-upload"></i>
                    <input type="file" name="banner_image" id="bannerInput" accept="image/*" required>
                </div>
            </div>

            <div class="cpf-info-box">
                <i class="fa-solid fa-circle-info"></i>
                <span>Gunakan gambar rasio lebar agar terlihat rapi pada halaman katalog dan aplikasi mobile.</span>
            </div>

            <div class="cpf-actions">
                <a href="{{ route('admin.classes.index') }}" class="cpf-cancel">Batal</a>

                <button type="submit" class="cpf-submit">
                    <i class="fa-solid fa-rocket"></i>
                    Publikasikan Program
                </button>
            </div>
        </form>

        <aside class="cpf-preview-card">
            <div class="cpf-preview-heading">
                <h3>Preview Tampilan</h3>
                <p>Pratinjau visual banner program sebelum dipublikasikan.</p>
            </div>

            <div class="cpf-preview-image" id="previewBox">
                <div>
                    <i class="fa-solid fa-image"></i>
                    <span>Preview gambar akan tampil di sini</span>
                </div>
            </div>

            <div class="cpf-preview-note">
                <i class="fa-solid fa-mobile-screen-button"></i>
                <span>Perubahan program akan digunakan oleh aplikasi mobile siswa.</span>
            </div>
        </aside>
    </section>

</div>

<script>
    const bannerInput = document.getElementById('bannerInput');
    const previewBox = document.getElementById('previewBox');

    if (bannerInput && previewBox) {
        bannerInput.addEventListener('change', function () {
            const file = this.files[0];

            if (!file) return;

            const reader = new FileReader();

            reader.onload = function (event) {
                previewBox.innerHTML = `<img src="${event.target.result}" alt="Preview Banner">`;
            };

            reader.readAsDataURL(file);
        });
    }
</script>

<style>
    .cpf-page { width: 100%; }

    .cpf-hero {
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

    .cpf-hero span {
        display: block;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        opacity: .78;
        margin-bottom: 8px;
    }

    .cpf-hero h1 {
        margin: 0 0 8px;
        font-size: 25px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .cpf-hero p {
        margin: 0;
        font-size: 13px;
        font-weight: 500;
        opacity: .9;
    }

    .cpf-hero a {
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

    .cpf-alert {
        border-radius: 16px;
        padding: 15px 17px;
        margin-bottom: 18px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 700;
    }

    .cpf-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .cpf-alert strong {
        display: block;
        margin-bottom: 4px;
    }

    .cpf-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .cpf-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 410px;
        gap: 22px;
        align-items: start;
    }

    .cpf-form-card,
    .cpf-preview-card {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
        padding: 24px;
    }

    .cpf-form-card {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .cpf-card-heading,
    .cpf-input-group.full,
    .cpf-info-box,
    .cpf-actions {
        grid-column: 1 / -1;
    }

    .cpf-card-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 6px;
    }

    .cpf-card-heading h2,
    .cpf-preview-heading h3 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .cpf-card-heading p,
    .cpf-preview-heading p {
        margin: 7px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
    }

    .cpf-heading-icon {
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        border-radius: 16px;
        background: #ffe8ee;
        color: #d90429;
        flex-shrink: 0;
    }

    .cpf-input-group label {
        display: block;
        margin-bottom: 8px;
        color: #374151;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .cpf-input-group div {
        position: relative;
    }

    .cpf-input-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 13px;
    }

    .cpf-input-group input,
    .cpf-input-group textarea {
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

    .cpf-input-group input {
        height: 48px;
        padding: 0 15px 0 42px;
    }

    .cpf-input-group input[type="file"] {
        padding-top: 13px;
    }

    .cpf-input-group textarea {
        resize: vertical;
        padding: 14px 15px;
        line-height: 1.5;
    }

    .cpf-input-group input:focus,
    .cpf-input-group textarea:focus {
        background: #fff;
        border-color: #fecdd3;
        box-shadow: 0 0 0 4px rgba(217, 4, 41, .08);
    }

    .cpf-info-box {
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

    .cpf-info-box i {
        color: #d90429;
        margin-top: 2px;
    }

    .cpf-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 6px;
    }

    .cpf-cancel,
    .cpf-submit {
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

    .cpf-cancel {
        background: #f3f4f6;
        color: #374151;
    }

    .cpf-submit {
        border: none;
        background: #d90429;
        color: #fff;
        cursor: pointer;
        box-shadow: 0 13px 25px rgba(217, 4, 41, .18);
        text-transform: uppercase;
    }

    .cpf-preview-card {
        position: sticky;
        top: 20px;
    }

    .cpf-preview-heading {
        margin-bottom: 18px;
    }

    .cpf-preview-image {
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

    .cpf-preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cpf-preview-image i {
        display: block;
        color: #d90429;
        font-size: 30px;
        margin-bottom: 8px;
    }

    .cpf-preview-note {
        margin-top: 16px;
        display: flex;
        gap: 10px;
        padding: 14px;
        border-radius: 14px;
        background: #f8fafc;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.5;
    }

    .cpf-preview-note i {
        color: #d90429;
        margin-top: 2px;
    }

    @media (max-width: 1100px) {
        .cpf-grid {
            grid-template-columns: 1fr;
        }

        .cpf-preview-card {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .cpf-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .cpf-form-card {
            grid-template-columns: 1fr;
        }

        .cpf-actions {
            flex-direction: column-reverse;
        }

        .cpf-cancel,
        .cpf-submit {
            width: 100%;
        }
    }
</style>
@endsection