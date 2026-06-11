<?php $__env->startSection('title', 'Tambah Banner'); ?>
<?php $__env->startSection('subtitle', 'Upload banner carousel homepage mobile'); ?>

<?php $__env->startSection('content'); ?>
<div class="bn-form-page">

    
    <section class="bn-header">
        <div class="bn-header-left">
            <span class="bn-breadcrumb-capsule">Manajemen Banner</span>
            <h1>Tambah Banner</h1>
            <p>Upload banner baru untuk carousel homepage mobile Spekta Academy.</p>
        </div>
        <div class="bn-header-actions">
            <a href="<?php echo e(route('admin.banners.index')); ?>" class="bn-secondary-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </section>

    <?php if($errors->any()): ?>
        <div class="bn-form-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Data belum valid.</strong>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    
    <section class="bn-form-grid">
        <form action="<?php echo e(route('admin.banners.store')); ?>" method="POST" enctype="multipart/form-data" class="bn-form-card">
            <?php echo csrf_field(); ?>

            <div class="bn-card-heading">
                <div>
                    <h2>Informasi Banner</h2>
                    <p>Lengkapi informasi banner agar tampil rapi di aplikasi mobile.</p>
                </div>

                <div class="bn-heading-icon">
                    <i class="fa-solid fa-image"></i>
                </div>
            </div>

            <div class="bn-input-group">
                <label>Title</label>
                <div>
                    <i class="fa-solid fa-heading"></i>
                    <input type="text" name="title" value="<?php echo e(old('title')); ?>" placeholder="Contoh: Promo Mei 2026">
                </div>
            </div>

            <div class="bn-input-group full">
                <label>Description</label>
                <textarea name="description" rows="4" placeholder="Tulis deskripsi singkat banner..."><?php echo e(old('description')); ?></textarea>
            </div>

            <div class="bn-input-group">
                <label>Image</label>
                <div>
                    <i class="fa-solid fa-upload"></i>
                    <input type="file" name="image" id="bannerImageInput" accept="image/*" required>
                </div>
            </div>

            <div class="bn-input-group">
                <label>Link</label>
                <div>
                    <i class="fa-solid fa-link"></i>
                    <input type="text" name="link" value="<?php echo e(old('link')); ?>" placeholder="/promo atau https://...">
                </div>
            </div>

            <div class="bn-input-group">
                <label>Order</label>
                <div>
                    <i class="fa-solid fa-arrow-down-1-9"></i>
                    <input type="number" name="order_position" value="<?php echo e(old('order_position', 0)); ?>" min="0">
                </div>
            </div>

            <div class="bn-switch-box">
                <label class="bn-switch">
                    <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', '1') == '1' ? 'checked' : ''); ?>>
                    <span></span>
                </label>
                <div>
                    <strong>Aktifkan Banner</strong>
                    <small>Banner aktif akan muncul pada carousel mobile.</small>
                </div>
            </div>

            <div class="bn-form-actions">
                <a href="<?php echo e(route('admin.banners.index')); ?>" class="bn-cancel-btn">Batal</a>

                <button type="submit" class="bn-submit-btn">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Banner
                </button>
            </div>
        </form>

        <aside class="bn-preview-card">
            <div class="bn-preview-heading">
                <h3>Preview Banner</h3>
                <p>Pratinjau gambar yang akan diunggah.</p>
            </div>

            <div class="bn-preview-image" id="bannerPreviewBox">
                <div>
                    <i class="fa-solid fa-image"></i>
                    <span>Preview gambar akan tampil di sini</span>
                </div>
            </div>

            <div class="bn-preview-note">
                <i class="fa-solid fa-circle-info"></i>
                <span>Gunakan rasio gambar lebar seperti 16:9 agar banner terlihat maksimal di carousel.</span>
            </div>
        </aside>
    </section>
</div>

<script>
    const bannerImageInput = document.getElementById('bannerImageInput');
    const bannerPreviewBox = document.getElementById('bannerPreviewBox');

    if (bannerImageInput && bannerPreviewBox) {
        bannerImageInput.addEventListener('change', function () {
            const file = this.files[0];

            if (!file) return;

            const reader = new FileReader();

            reader.onload = function (event) {
                bannerPreviewBox.innerHTML = `<img src="${event.target.result}" alt="Preview Banner">`;
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

    .bn-form-page { 
        width: 100%; 
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        padding-bottom: 40px; /* Tambahan ruang bernapas di bawah */
    }

    /* Header Minimalis */
    .bn-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }
    .bn-breadcrumb-capsule {
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
    .bn-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .bn-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }
    .bn-secondary-btn {
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
    .bn-secondary-btn:hover {
        background: var(--spekta-gray-light);
        color: var(--text-main);
        border-color: var(--spekta-gray);
    }

    .bn-form-alert {
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 24px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 800;
    }
    .bn-form-alert.error {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .bn-form-alert ul { margin: 4px 0 0; padding-left: 18px; }

    /* Layout Form Grid */
    .bn-form-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 350px;
        gap: 24px;
        align-items: start;
    }

    .bn-form-card,
    .bn-preview-card {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
        padding: 20px;
    }

    .bn-card-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 20px;
    }
    .bn-card-heading h2,
    .bn-preview-heading h3 {
        margin: 0;
        color: var(--text-main);
        font-size: 15px;
        font-weight: 800;
    }
    .bn-card-heading p,
    .bn-preview-heading p {
        margin: 6px 0 0;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 600;
        line-height: 1.5;
    }

    .bn-heading-icon {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: var(--spekta-red-light);
        color: var(--spekta-red);
        flex-shrink: 0;
    }

    .bn-form-card {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 15px;
    }

    .bn-card-heading,
    .bn-input-group.full,
    .bn-switch-box,
    .bn-form-actions {
        grid-column: 1 / -1;
    }

    .bn-input-group label {
        display: block;
        margin-bottom: 6px;
        color: var(--text-muted);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .bn-input-group div { position: relative; }
    .bn-input-group i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--spekta-gray);
        font-size: 13px;
    }

    .bn-input-group input,
    .bn-input-group textarea {
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

    .bn-input-group input {
        height: 40px;
        padding: 0 14px 0 38px;
    }

    .bn-input-group textarea {
        resize: vertical;
        padding: 12px 14px;
        line-height: 1.5;
    }

    .bn-input-group input[type="file"] {
        padding-top: 10px;
    }

    .bn-input-group input:focus,
    .bn-input-group textarea:focus {
        background: var(--spekta-white);
        border-color: var(--spekta-teal);
        box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12);
    }

    /* Switch Box */
    .bn-switch-box {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 12px;
        background: var(--spekta-gray-light);
        border: 1px solid var(--border-soft);
    }

    .bn-switch {
        position: relative;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }
    .bn-switch input { display: none; }
    .bn-switch span {
        position: absolute;
        inset: 0;
        border-radius: 999px;
        background: var(--spekta-gray);
        cursor: pointer;
        transition: .2s ease;
    }
    .bn-switch span::after {
        content: "";
        position: absolute;
        width: 18px;
        height: 18px;
        top: 3px;
        left: 3px;
        border-radius: 999px;
        background: var(--spekta-white);
        transition: .2s ease;
        box-shadow: 0 2px 6px rgba(15,23,42,.15);
    }
    .bn-switch input:checked + span { background: var(--spekta-teal); }
    .bn-switch input:checked + span::after { transform: translateX(20px); }

    .bn-switch-box strong { display: block; color: var(--text-main); font-size: 12px; font-weight: 800; }
    .bn-switch-box small { display: block; color: var(--text-muted); font-size: 10px; font-weight: 600; margin-top: 2px; }

    /* Form Actions Row */
    .bn-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 10px;
    }

    .bn-cancel-btn,
    .bn-submit-btn {
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

    .bn-cancel-btn {
        background: var(--spekta-gray-light);
        color: var(--text-main);
    }
    .bn-cancel-btn:hover { background: var(--border-soft); }

    .bn-submit-btn {
        border: none;
        background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%);
        color: var(--spekta-white);
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(229, 57, 53, 0.2);
    }
    .bn-submit-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(229, 57, 53, 0.3);
    }

    .bn-preview-card {
        position: sticky;
        top: 20px;
    }
    .bn-preview-heading { margin-bottom: 14px; }

    /* Preview Image Box */
    .bn-preview-image {
        width: 100%;
        height: 180px;
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
    .bn-preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .bn-preview-image i {
        display: block;
        color: var(--spekta-red);
        font-size: 24px;
        margin-bottom: 6px;
    }

    .bn-preview-note {
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
    .bn-preview-note i { color: var(--spekta-red); margin-top: 2px; }

    @media (max-width: 1100px) {
        .bn-form-grid { grid-template-columns: 1fr; }
        .bn-preview-card { position: static; }
    }

    @media (max-width: 768px) {
        .bn-header { flex-direction: column; align-items: flex-start; gap: 14px; }
        .bn-form-card { grid-template-columns: 1fr; }
        .bn-form-actions { flex-direction: column-reverse; }
        .bn-cancel-btn, .bn-submit-btn { width: 100%; }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\perkuliahan\PA 2 - code\PAAAAA2\BackEnd\resources\views/admin/banners/create.blade.php ENDPATH**/ ?>