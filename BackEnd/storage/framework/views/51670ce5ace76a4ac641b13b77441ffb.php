<?php $__env->startSection('title', 'Banner Management'); ?>
<?php $__env->startSection('subtitle', 'Kelola banner carousel homepage mobile'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $bannersCollection = method_exists($banners, 'getCollection') ? $banners->getCollection() : collect($banners);
    $totalBanners = method_exists($banners, 'total') ? $banners->total() : $bannersCollection->count();
    $activeBanners = $bannersCollection->where('is_active', true)->count();
    $inactiveBanners = max($totalBanners - $activeBanners, 0);
?>

<div class="bn-page">

    
    <section class="bn-header">
        <div class="bn-header-text">
            <span class="bn-kicker">Promosi & Informasi</span>
            <h1>Banner Management</h1>
            <p>Kelola banner carousel untuk homepage mobile Spekta Academy.</p>
        </div>
    </section>

    
    <?php if(session('success')): ?>
        <div class="bn-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    
    <section class="bn-stats">
        <!-- Total Banner -->
        <div class="bn-stat-card card-red">
            <div class="bn-stat-icon red"><i class="fa-solid fa-images"></i></div>
            <div class="bn-stat-info">
                <p>Total Banner</p>
                <h2><?php echo e(number_format($totalBanners)); ?></h2>
            </div>
        </div>

        <!-- Banner Aktif -->
        <div class="bn-stat-card card-teal">
            <div class="bn-stat-icon teal"><i class="fa-solid fa-circle-check"></i></div>
            <div class="bn-stat-info">
                <p>Banner Aktif</p>
                <h2><?php echo e(number_format($activeBanners)); ?></h2>
            </div>
            <?php if($activeBanners > 0): ?>
                <span class="bn-pulse-dot"></span>
            <?php endif; ?>
        </div>

        <!-- Banner Nonaktif -->
        <div class="bn-stat-card card-gray">
            <div class="bn-stat-icon gray"><i class="fa-solid fa-circle-xmark"></i></div>
            <div class="bn-stat-info">
                <p>Nonaktif</p>
                <h2><?php echo e(number_format($inactiveBanners)); ?></h2>
            </div>
        </div>
    </section>

    
    <section class="bn-main-grid">

        <div class="bn-list-panel">
            <div class="bn-panel-heading">
                <div>
                    <h2>Daftar Banner</h2>
                    <p>Atur banner promosi yang muncul pada aplikasi mobile.</p>
                </div>

                <!-- Sembunyikan tombol di kanan atas jika data kosong untuk menghindari redundansi -->
                <?php if($totalBanners > 0): ?>
                    <a href="<?php echo e(route('admin.banners.create')); ?>" class="bn-primary-btn">
                        <i class="fa-solid fa-plus"></i>
                        Tambah Banner Baru
                    </a>
                <?php endif; ?>
            </div>

            <div class="bn-banner-list">
                <?php $__empty_1 = true; $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $imageUrl = $banner->image_url ?? null;

                        if (!$imageUrl && !empty($banner->image)) {
                            if (\Illuminate\Support\Str::startsWith($banner->image, ['http://', 'https://'])) {
                                $imageUrl = $banner->image;
                            } elseif (\Illuminate\Support\Str::startsWith($banner->image, ['storage/'])) {
                                $imageUrl = asset($banner->image);
                            } else {
                                $imageUrl = asset('storage/' . ltrim($banner->image, '/'));
                            }
                        } elseif ($imageUrl && !\Illuminate\Support\Str::startsWith($imageUrl, ['http://', 'https://'])) {
                            $imageUrl = asset($imageUrl);
                        }
                    ?>

                    <article class="bn-banner-card">
                        <div class="bn-banner-image">
                            <?php if($imageUrl): ?>
                                <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($banner->title ?? 'Banner'); ?>">
                            <?php else: ?>
                                <div class="bn-no-image">
                                    <i class="fa-solid fa-image"></i>
                                    <span>No Image</span>
                                </div>
                            <?php endif; ?>

                            <div class="bn-image-badge">
                                #<?php echo e($banner->order_position ?? 0); ?>

                            </div>
                        </div>

                        <div class="bn-banner-content">
                            <div class="bn-banner-title-row">
                                <div>
                                    <h3><?php echo e($banner->title ?? 'Tanpa Judul'); ?></h3>
                                    <p><?php echo e($banner->description ? \Illuminate\Support\Str::limit($banner->description, 145) : 'Tidak ada deskripsi banner.'); ?></p>
                                </div>

                                <?php if($banner->is_active): ?>
                                    <span class="bn-status active">
                                        <span class="bn-dot-wrapper">
                                            <i class="bn-dot"></i>
                                            <i class="bn-dot-pulse"></i>
                                        </span>
                                        Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="bn-status inactive">
                                        <span class="bn-dot-wrapper">
                                            <i class="bn-dot"></i>
                                        </span>
                                        Nonaktif
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="bn-meta-grid">
                                <div>
                                    <span>Link Tujuan</span>
                                    <strong><?php echo e($banner->link ?: 'Tidak ada link'); ?></strong>
                                </div>

                                <div>
                                    <span>Urutan</span>
                                    <strong><?php echo e($banner->order_position ?? 0); ?></strong>
                                </div>

                                <div>
                                    <span>Tanggal Dibuat</span>
                                    <strong><?php echo e($banner->created_at?->translatedFormat('d M Y') ?? '-'); ?></strong>
                                </div>
                            </div>

                            <div class="bn-actions">
                                <a href="<?php echo e(route('admin.banners.edit', $banner)); ?>" class="edit">
                                    <i class="fa-solid fa-pen"></i>
                                    Edit
                                </a>

                                <form action="<?php echo e(route('admin.banners.destroy', $banner)); ?>" method="POST" onsubmit="return confirm('Hapus banner ini secara permanen?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="delete">
                                        <i class="fa-solid fa-trash-can"></i>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="bn-empty">
                        <div class="bn-empty-icon"><i class="fa-solid fa-images"></i></div>
                        <strong>Belum ada banner.</strong>
                        <p>Tambahkan banner pertama untuk carousel homepage mobile Anda.</p>
                        <a href="<?php echo e(route('admin.banners.create')); ?>" class="bn-primary-btn" style="margin-top: 15px;">Tambah Banner Pertama</a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if(method_exists($banners, 'hasPages') && $banners->hasPages()): ?>
                <div class="bn-pagination">
                    <?php echo e($banners->links()); ?>

                </div>
            <?php endif; ?>
        </div>

    </section>
</div>

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

    /* BASE LAYOUT */
    .bn-page {
        width: 100%;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* HEADER */
    .bn-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }
    .bn-kicker {
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

    /* PRIMARY BUTTON */
    .bn-primary-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%);
        color: var(--spekta-white);
        border: none;
        border-radius: 12px;
        padding: 12px 20px;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 6px 15px rgba(229, 57, 53, 0.2);
        white-space: nowrap;
        cursor: pointer;
    }
    .bn-primary-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(229, 57, 53, 0.3);
        color: var(--spekta-white);
    }

    /* ALERTS */
    .bn-alert {
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 24px;
        display: flex;
        gap: 10px;
        align-items: center;
        font-size: 13px;
        font-weight: 800;
    }
    .bn-alert.success { background: #e6f7ed; color: #15803d; border: 1px solid #bbf7d0; }

    /* STATS SUMMARY GRID (3 KOLOM SEIMBANG) */
    .bn-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .bn-stat-card {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: all 0.2s ease;
        position: relative;
    }
    .bn-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.03);
    }
    .bn-stat-card.card-red:hover { border-color: var(--spekta-red); }
    .bn-stat-card.card-teal:hover { border-color: var(--spekta-teal); }
    .bn-stat-card.card-gray:hover { border-color: var(--spekta-gray); }

    .bn-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 16px;
    }
    .bn-stat-icon.red { background: var(--spekta-red-light); color: var(--spekta-red); }
    .bn-stat-icon.teal { background: var(--spekta-teal-light); color: var(--spekta-teal); }
    .bn-stat-icon.gray { background: var(--spekta-gray-light); color: var(--text-muted); }

    .bn-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .bn-stat-info h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1;
    }

    /* Indikator denyut untuk banner aktif */
    .bn-pulse-dot {
        position: absolute;
        top: 14px; right: 14px;
        width: 6px; height: 6px;
        background: var(--spekta-teal);
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(46, 168, 171, 0.7);
        animation: pulseTeal 1.5s infinite;
    }
    @keyframes pulseTeal {
        0% { box-shadow: 0 0 0 0 rgba(46, 168, 171, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(46, 168, 171, 0); }
        100% { box-shadow: 0 0 0 0 rgba(46, 168, 171, 0); }
    }

    /* MAIN PANEL */
    .bn-main-grid {
        display: block;
        margin-bottom: 24px;
    }
    .bn-list-panel {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    }
    .bn-panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--spekta-gray-light);
        flex-wrap: wrap;
        gap: 15px;
    }
    .bn-panel-heading h2 {
        margin: 0 0 4px;
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
    }
    .bn-panel-heading p {
        margin: 0;
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* BANNER LIST & CARDS */
    .bn-banner-list {
        display: grid;
        gap: 16px;
    }
    .bn-banner-card {
        display: grid;
        grid-template-columns: 280px minmax(0, 1fr);
        gap: 20px;
        padding: 16px;
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        background: var(--spekta-white);
        transition: all 0.25s ease;
    }
    .bn-banner-card:hover {
        border-color: var(--spekta-gray);
        box-shadow: 0 8px 20px rgba(0,0,0,0.03);
        transform: translateY(-2px);
    }

    /* CARD IMAGE */
    .bn-banner-image {
        height: 150px;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        background: var(--spekta-gray-light);
        border: 1px solid var(--border-soft);
    }
    .bn-banner-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .bn-no-image {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
    }
    .bn-no-image i {
        font-size: 24px;
        color: var(--spekta-gray);
        margin-bottom: 6px;
    }
    .bn-image-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 3px 10px;
        border-radius: 6px;
        background: rgba(31, 41, 55, 0.85);
        color: var(--spekta-white);
        font-size: 11px;
        font-weight: 800;
        backdrop-filter: blur(4px);
    }

    /* CARD CONTENT */
    .bn-banner-content {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .bn-banner-title-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
    }
    .bn-banner-title-row h3 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 15px;
        font-weight: 800;
    }
    .bn-banner-title-row p {
        margin: 0;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
    }

    /* STATUS BADGE */
    .bn-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 10px;
        font-weight: 800;
    }
    .bn-dot-wrapper {
        position: relative;
        width: 5px; height: 5px;
        display: inline-block;
    }
    .bn-dot {
        width: 5px; height: 5px;
        border-radius: 99px;
        background: currentColor;
        display: block;
        position: absolute;
        left: 0; top: 0;
    }
    .bn-dot-pulse {
        width: 5px; height: 5px;
        border-radius: 99px;
        background: currentColor;
        display: block;
        position: absolute;
        left: 0; top: 0;
        opacity: 0.4;
        transform: scale(1);
        animation: dotGlow 1.8s infinite ease-in-out;
    }
    @keyframes dotGlow {
        0% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(3.2); opacity: 0; }
    }
    .bn-status.active { color: #15803d; }
    .bn-status.inactive { color: var(--spekta-gray); }

    /* META INFO GRID */
    .bn-meta-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr;
        gap: 12px;
        padding: 12px;
        border-radius: 10px;
        background: var(--spekta-gray-light);
        margin: 12px 0;
    }
    .bn-meta-grid span {
        display: block;
        color: var(--text-muted);
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 2px;
    }
    .bn-meta-grid strong {
        display: block;
        color: var(--text-main);
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* CARD ACTIONS */
    .bn-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }
    .bn-actions form { margin: 0; }
    .bn-actions a, .bn-actions button {
        height: 32px;
        border: none;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0 12px;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        font-family: inherit;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .bn-actions .edit { background: var(--spekta-gray-light); color: var(--text-main); }
    .bn-actions .edit:hover { background: var(--border-soft); }
    .bn-actions .delete { background: var(--spekta-red-light); color: var(--spekta-red); }
    .bn-actions .delete:hover { background: #fecaca; }

    /* EMPTY STATE */
    .bn-empty {
        text-align: center;
        padding: 48px;
        background: var(--spekta-gray-light);
        border-radius: 16px;
        border: 1px dashed var(--border-soft);
    }
    .bn-empty-icon {
        width: 48px;
        height: 48px;
        background: var(--spekta-white);
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 18px;
        color: var(--spekta-gray);
        margin: 0 auto 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    .bn-empty strong { display: block; font-size: 14px; color: var(--text-main); margin-bottom: 4px; font-weight: 800;}
    .bn-empty p { margin: 0; font-size: 12px; color: var(--text-muted); font-weight: 600; }

    .bn-pagination { margin-top: 20px; }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .bn-header { flex-direction: column; align-items: flex-start; gap: 14px; }
        .bn-stats { grid-template-columns: 1fr; }
        .bn-banner-card { grid-template-columns: 1fr; }
        .bn-banner-image { height: 160px; }
        .bn-meta-grid { grid-template-columns: 1fr; gap: 12px; }
        .bn-panel-heading { flex-direction: column; align-items: stretch;}
        .bn-primary-btn { justify-content: center; }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/banners/index.blade.php ENDPATH**/ ?>