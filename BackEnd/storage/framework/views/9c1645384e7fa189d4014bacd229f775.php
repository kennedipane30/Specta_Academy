<?php $__env->startSection('title', 'Edit Banner'); ?>
<?php $__env->startSection('subtitle', 'Perbarui banner carousel homepage mobile'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $currentImageUrl = $banner->image_url ?? null;

    if (!$currentImageUrl && !empty($banner->image)) {
        if (\Illuminate\Support\Str::startsWith($banner->image, ['http://', 'https://'])) {
            $currentImageUrl = $banner->image;
        } elseif (\Illuminate\Support\Str::startsWith($banner->image, ['storage/'])) {
            $currentImageUrl = asset($banner->image);
        } else {
            $currentImageUrl = asset('storage/' . ltrim($banner->image, '/'));
        }
    } elseif ($currentImageUrl && !\Illuminate\Support\Str::startsWith($currentImageUrl, ['http://', 'https://'])) {
        $currentImageUrl = asset($currentImageUrl);
    }
?>

<div class="bn-form-page">

    <section class="bn-form-hero">
        <div>
            <span>Banner Carousel</span>
            <h1>Edit Banner</h1>
            <p>Perbarui data banner carousel homepage mobile Spekta Academy.</p>
        </div>

        <a href="<?php echo e(route('admin.banners.index')); ?>">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
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
        <form action="<?php echo e(route('admin.banners.update', $banner)); ?>" method="POST" enctype="multipart/form-data" class="bn-form-card">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="bn-card-heading">
                <div>
                    <h2>Informasi Banner</h2>
                    <p>Ubah informasi banner sesuai kebutuhan promosi.</p>
                </div>

                <div class="bn-heading-icon">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
            </div>

            <div class="bn-input-group">
                <label>Title</label>
                <div>
                    <i class="fa-solid fa-heading"></i>
                    <input type="text" name="title" value="<?php echo e(old('title', $banner->title)); ?>" placeholder="Contoh: Promo Mei 2026">
                </div>
            </div>

            <div class="bn-input-group full">
                <label>Description</label>
                <textarea name="description" rows="4" placeholder="Tulis deskripsi singkat banner..."><?php echo e(old('description', $banner->description)); ?></textarea>
            </div>

            <div class="bn-input-group">
                <label>Ganti Image</label>
                <div>
                    <i class="fa-solid fa-upload"></i>
                    <input type="file" name="image" id="bannerImageInput" accept="image/*">
                </div>
            </div>

            <div class="bn-input-group">
                <label>Link</label>
                <div>
                    <i class="fa-solid fa-link"></i>
                    <input type="text" name="link" value="<?php echo e(old('link', $banner->link)); ?>" placeholder="/promo atau https://...">
                </div>
            </div>

            <div class="bn-input-group">
                <label>Order</label>
                <div>
                    <i class="fa-solid fa-arrow-down-1-9"></i>
                    <input type="number" name="order_position" value="<?php echo e(old('order_position', $banner->order_position)); ?>" min="0">
                </div>
            </div>

            <div class="bn-switch-box">
                <label class="bn-switch">
                    <input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $banner->is_active ? '1' : '0') == '1' ? 'checked' : ''); ?>>
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
                    Update Banner
                </button>
            </div>
        </form>

        <aside class="bn-preview-card">
            <div class="bn-preview-heading">
                <h3>Preview Banner</h3>
                <p>Gambar saat ini atau gambar baru yang dipilih.</p>
            </div>

            <div class="bn-preview-image" id="bannerPreviewBox">
                <?php if($currentImageUrl): ?>
                    <img src="<?php echo e($currentImageUrl); ?>" alt="<?php echo e($banner->title ?? 'Banner'); ?>">
                <?php else: ?>
                    <div>
                        <i class="fa-solid fa-image"></i>
                        <span>Tidak ada gambar</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bn-preview-note">
                <i class="fa-solid fa-circle-info"></i>
                <span>Kosongkan input gambar jika tidak ingin mengganti gambar lama.</span>
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
    .bn-form-page { width: 100%; }

    .bn-form-hero {
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

    .bn-form-hero span {
        display: block;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        opacity: .78;
        margin-bottom: 8px;
    }

    .bn-form-hero h1 {
        margin: 0 0 8px;
        font-size: 25px;
        font-weight: 900;
    }

    .bn-form-hero p {
        margin: 0;
        font-size: 13px;
        font-weight: 500;
        opacity: .9;
    }

    .bn-form-hero a {
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

    .bn-form-alert {
        border-radius: 16px;
        padding: 15px 17px;
        margin-bottom: 18px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 700;
    }

    .bn-form-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .bn-form-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .bn-form-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 390px;
        gap: 22px;
        align-items: start;
    }

    .bn-form-card,
    .bn-preview-card {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
        padding: 24px;
    }

    .bn-card-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 24px;
    }

    .bn-card-heading h2,
    .bn-preview-heading h3 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .bn-card-heading p,
    .bn-preview-heading p {
        margin: 7px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
    }

    .bn-heading-icon {
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        border-radius: 16px;
        background: #ffe8ee;
        color: #d90429;
        flex-shrink: 0;
    }

    .bn-form-card {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .bn-card-heading,
    .bn-input-group.full,
    .bn-switch-box,
    .bn-form-actions {
        grid-column: 1 / -1;
    }

    .bn-input-group label {
        display: block;
        margin-bottom: 8px;
        color: #374151;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .bn-input-group div {
        position: relative;
    }

    .bn-input-group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 13px;
    }

    .bn-input-group input,
    .bn-input-group textarea {
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

    .bn-input-group input {
        height: 48px;
        padding: 0 15px 0 42px;
    }

    .bn-input-group textarea {
        resize: vertical;
        padding: 14px 15px;
        line-height: 1.5;
    }

    .bn-input-group input[type="file"] {
        padding-top: 13px;
    }

    .bn-input-group input:focus,
    .bn-input-group textarea:focus {
        background: #fff;
        border-color: #fecdd3;
        box-shadow: 0 0 0 4px rgba(217, 4, 41, .08);
    }

    .bn-switch-box {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #edf0f4;
    }

    .bn-switch {
        position: relative;
        width: 48px;
        height: 28px;
        flex-shrink: 0;
    }

    .bn-switch input {
        display: none;
    }

    .bn-switch span {
        position: absolute;
        inset: 0;
        border-radius: 999px;
        background: #d1d5db;
        cursor: pointer;
        transition: .2s ease;
    }

    .bn-switch span::after {
        content: "";
        position: absolute;
        width: 22px;
        height: 22px;
        top: 3px;
        left: 3px;
        border-radius: 999px;
        background: #fff;
        transition: .2s ease;
        box-shadow: 0 2px 8px rgba(15,23,42,.18);
    }

    .bn-switch input:checked + span {
        background: #d90429;
    }

    .bn-switch input:checked + span::after {
        transform: translateX(20px);
    }

    .bn-switch-box strong {
        display: block;
        color: #111827;
        font-size: 13px;
        font-weight: 900;
    }

    .bn-switch-box small {
        display: block;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        margin-top: 3px;
    }

    .bn-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 6px;
    }

    .bn-cancel-btn,
    .bn-submit-btn {
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

    .bn-cancel-btn {
        background: #f3f4f6;
        color: #374151;
    }

    .bn-submit-btn {
        border: none;
        background: #d90429;
        color: #fff;
        cursor: pointer;
        box-shadow: 0 13px 25px rgba(217, 4, 41, .18);
    }

    .bn-preview-card {
        position: sticky;
        top: 20px;
    }

    .bn-preview-heading {
        margin-bottom: 18px;
    }

    .bn-preview-image {
        width: 100%;
        height: 220px;
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

    .bn-preview-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .bn-preview-image i {
        display: block;
        color: #d90429;
        font-size: 30px;
        margin-bottom: 8px;
    }

    .bn-preview-note {
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

    .bn-preview-note i {
        color: #d90429;
        margin-top: 2px;
    }

    @media (max-width: 1100px) {
        .bn-form-grid {
            grid-template-columns: 1fr;
        }

        .bn-preview-card {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .bn-form-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .bn-form-card {
            grid-template-columns: 1fr;
        }

        .bn-form-actions {
            flex-direction: column-reverse;
        }

        .bn-cancel-btn,
        .bn-submit-btn {
            width: 100%;
        }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\Specta_Academy\BackEnd\resources\views/admin/banners/edit.blade.php ENDPATH**/ ?>