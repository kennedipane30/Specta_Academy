@extends('layouts.spekta')

@section('title', 'Tambah Program - Spekta Academy')

@section('content')
<div class="cp-page">
    <!-- Header Section -->
    <section class="cp-header">
        <div>
            <span>Spekta Control Center</span>
            <h1>Tambah Program Baru</h1>
            <p>Konfigurasi detail program, harga, dan visual untuk sinkronisasi otomatis ke aplikasi mobile siswa.</p>
        </div>
        <a href="{{ route('admin.classes.index') }}" class="cp-back-btn">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali ke Katalog
        </a>
    </section>

    <!-- Main Form Panel -->
    <section class="cp-main-panel">
        <form action="{{ route('admin.classes.store') }}" method="POST" enctype="multipart/form-data" class="cp-form">
            @csrf

            {{-- ====== SECTION 1: INFO UTAMA ====== --}}
            <div class="cp-form-section">
                <div class="section-title">
                    <i class="fa-solid fa-circle-info"></i>
                    Informasi Utama Program
                </div>
                
                <div class="cp-form-grid">
                    <div class="cp-input-group">
                        <label>Nama Program Kelas</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-tag"></i>
                            <input type="text" name="program_name"
                                   value="{{ old('program_name') }}"
                                   placeholder="Misal: Kedinasan Intensif 2024" required>
                        </div>
                        @error('program_name')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="cp-input-group">
                        <label>Harga Investasi (IDR)</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-money-bill-wave"></i>
                            <input type="number" name="price"
                                   value="{{ old('price') }}"
                                   placeholder="Misal: 1500000" required>
                        </div>
                        @error('price')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="cp-input-group full-width">
                        <label>Deskripsi Program</label>
                        <textarea name="description" rows="5"
                                  placeholder="Tuliskan detail program, keunggulan, dan apa saja yang didapat siswa...">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ====== SECTION 2: BANNER ====== --}}
            <div class="cp-form-section mt-4">
                <div class="section-title">
                    <i class="fa-solid fa-image"></i>
                    Media & Visual Banner
                </div>
                
                <div class="upload-container">
                    <div class="upload-box" id="uploadBox">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <div class="upload-text">
                            <strong>Pilih banner program</strong>
                            <span>Drag and drop atau klik untuk upload</span>
                        </div>
                        <input type="file" name="image" id="imageInput" accept="image/*" required>
                    </div>

                    <div id="imagePreviewContainer" class="image-preview-wrapper" style="display:none;">
                        <div class="preview-header">
                            <span><i class="fa-solid fa-eye"></i> Preview Tampilan Banner</span>
                            <button type="button" id="removeImage" class="remove-btn">Ganti Gambar</button>
                        </div>
                        <div class="image-preview-box">
                            <img id="imagePreview" src="" alt="Preview">
                        </div>
                    </div>

                    <small class="upload-hint">Format yang didukung: JPG, PNG, WEBP. Maksimal 2MB. Gunakan rasio landscape.</small>
                </div>
            </div>

            {{-- Footer --}}
            <div class="cp-form-footer">
                <p><i class="fa-solid fa-circle-exclamation"></i> Data akan langsung tampil di aplikasi mobile setelah disimpan.</p>
                <button type="submit" class="cp-primary-btn">
                    <i class="fa-solid fa-paper-plane"></i>
                    Publikasikan Program
                </button>
            </div>
        </form>
    </section>
</div>

<style>
    .cp-page { width: 100%; animation: fadeIn 0.5s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .cp-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .cp-header span { color: #d90429; font-size: 10px; font-weight: 900; letter-spacing: .2em; text-transform: uppercase; }
    .cp-header h1 { font-size: 28px; font-weight: 900; color: #111827; margin: 5px 0; }
    .cp-header p  { color: #6b7280; font-size: 13px; margin: 0; }

    .cp-back-btn { display: inline-flex; align-items: center; gap: 8px; background: #f3f4f6; color: #4b5563; padding: 10px 18px; border-radius: 12px; font-size: 12px; font-weight: 800; text-decoration: none; }
    .cp-back-btn:hover { background: #e5e7eb; color: #111827; }

    .cp-main-panel { background: #fff; border-radius: 24px; padding: 30px; border: 1px solid #edf0f4; box-shadow: 0 20px 40px rgba(0,0,0,0.03); }
    .cp-form-section { margin-bottom: 10px; }
    .mt-4 { margin-top: 28px; }

    .section-title { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 900; color: #111827; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #f8fafc; }
    .section-title i { color: #d90429; }

    .cp-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
    .cp-input-group.full-width { grid-column: span 2; }
    .cp-input-group label { display: block; font-size: 11px; font-weight: 800; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 8px; }

    .input-wrapper { position: relative; }
    .input-wrapper > i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; z-index: 1; }
    .input-wrapper input,
    textarea {
        width: 100%; padding: 12px 15px 12px 45px;
        background: #f8fafc; border: 1px solid #edf2f7;
        border-radius: 14px; font-size: 14px; font-weight: 600;
        color: #111827; outline: none; transition: 0.2s;
        box-sizing: border-box;
    }
    textarea { padding: 15px; }
    .input-wrapper input:focus,
    textarea:focus { background: #fff; border-color: #d90429; box-shadow: 0 0 0 4px rgba(217,4,41,0.05); }

    .field-error { display: block; color: #ef4444; font-size: 11px; font-weight: 700; margin-top: 6px; }

    .upload-box { border: 2px dashed #e5e7eb; background: #f9fafb; padding: 35px; text-align: center; border-radius: 18px; position: relative; cursor: pointer; transition: 0.3s; }
    .upload-box:hover { border-color: #d90429; background: #fff1f2; }
    .upload-box > i { font-size: 32px; color: #d90429; margin-bottom: 12px; }
    .upload-box input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .upload-text strong { display: block; font-size: 14px; color: #111827; margin-bottom: 4px; }
    .upload-text span   { font-size: 12px; color: #9ca3af; }

    .image-preview-wrapper { margin-top: 20px; border: 1px solid #edf2f7; border-radius: 16px; overflow: hidden; }
    .preview-header { padding: 10px 15px; background: #111827; color: #fff; display: flex; justify-content: space-between; align-items: center; font-size: 11px; font-weight: 800; text-transform: uppercase; }
    .remove-btn { background: #ef4444; border: none; color: white; padding: 4px 10px; border-radius: 6px; font-size: 10px; cursor: pointer; }
    .image-preview-box { width: 100%; padding: 10px; display: flex; justify-content: center; background: #f1f5f9; }
    .image-preview-box img { max-width: 100%; height: auto; max-height: 350px; border-radius: 8px; object-fit: contain; }
    .upload-hint { display: block; margin-top: 10px; font-size: 11px; color: #9ca3af; }

    .cp-form-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 25px; border-top: 1px solid #f1f5f9; }
    .cp-form-footer p { font-size: 12px; color: #9ca3af; display: flex; align-items: center; gap: 6px; margin: 0; }
    .cp-primary-btn { background: #d90429; color: #fff; padding: 0 25px; height: 48px; border-radius: 14px; font-size: 13px; font-weight: 800; border: none; cursor: pointer; transition: 0.3s; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 10px 20px rgba(217,4,41,0.2); }
    .cp-primary-btn:hover { transform: translateY(-3px); box-shadow: 0 15px 25px rgba(217,4,41,0.3); }

    @media (max-width: 768px) {
        .cp-form-grid { grid-template-columns: 1fr; }
        .cp-input-group.full-width { grid-column: span 1; }
        .cp-header { flex-direction: column; align-items: flex-start; gap: 12px; }
    }
</style>

<script>
    const imageInput       = document.getElementById('imageInput');
    const imagePreview     = document.getElementById('imagePreview');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const uploadBox        = document.getElementById('uploadBox');
    const removeBtn        = document.getElementById('removeImage');

    imageInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = ev => {
            imagePreview.src = ev.target.result;
            previewContainer.style.display = 'block';
            uploadBox.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });

    removeBtn.addEventListener('click', function () {
        imageInput.value = '';
        previewContainer.style.display = 'none';
        uploadBox.style.display = 'block';
    });
</script>
@endsection 