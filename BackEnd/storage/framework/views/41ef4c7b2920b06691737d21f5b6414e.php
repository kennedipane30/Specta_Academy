<?php $__env->startSection('title', 'Master Tryout - Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<?php
    // Kalkulasi dinamis total draf soal yang masuk dari seluruh kelas pengajar
    $totalDraftsCount = collect($classes)->sum(function($c) use ($draftStatus) {
        return $draftStatus[$c->class_id]->total ?? 0;
    });
?>

<div class="cp-page">

    
    <section class="cp-header">
        <div class="cp-header-left">
            <span class="cp-breadcrumb-capsule">Admin Tryout Center</span>
            <h1>Manajemen Paket TO</h1>
            <p>Kurasi setoran soal dari pengajar dan publikasikan menjadi paket Tryout resmi untuk aplikasi mobile siswa.</p>
        </div>
    </section>

    
    <?php if(session('success')): ?>
        <div class="tm-alert-modern success">
            <i class="fa-solid fa-circle-check"></i>
            <div>
                <strong>OPERASI BERHASIL</strong>
                <p><?php echo e(session('success')); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="tm-alert-modern error">
            <i class="fa-solid fa-circle-xmark"></i>
            <div>
                <strong>OPERASI GAGAL</strong>
                <p><?php echo e(session('error')); ?></p>
            </div>
        </div>
    <?php endif; ?>

    
    <section class="cp-stats">
        <!-- Paket Live -->
        <div class="cp-stat-card card-red">
            <div class="cp-stat-icon red"><i class="fa-solid fa-fire"></i></div>
            <div class="cp-stat-info">
                <p>Paket TO Live</p>
                <h2><?php echo e(count($activePackages)); ?> <span>Paket</span></h2>
            </div>
            <?php if(count($activePackages) > 0): ?>
                <span class="cp-pulse-dot"></span>
            <?php endif; ?>
        </div>

        <!-- Antrean Setoran -->
        <div class="cp-stat-card card-teal">
            <div class="cp-stat-icon teal"><i class="fa-solid fa-hourglass-half"></i></div>
            <div class="cp-stat-info">
                <p>Antrean Setoran</p>
                <h2><?php echo e(number_format($totalDraftsCount)); ?> <span>Soal</span></h2>
            </div>
        </div>

        <!-- Total Program Kelas -->
        <div class="cp-stat-card card-gray">
            <div class="cp-stat-icon gray"><i class="fa-solid fa-layer-group"></i></div>
            <div class="cp-stat-info">
                <p>Target Program</p>
                <h2><?php echo e(count($classes)); ?> <span>Kelas</span></h2>
            </div>
        </div>
    </section>

    
    <div class="tm-grid-layout">

        
        <section class="cp-main-card">
            <div class="card-header-flex">
                <div class="title-with-icon">
                    <div class="icon-box red"><i class="fa-solid fa-inbox"></i></div>
                    <div>
                        <h2>Setoran Soal Pengajar</h2>
                        <p>Draf soal masuk yang menunggu antrean publikasi.</p>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="cp-table-modern">
                    <thead>
                        <tr>
                            <th>PROGRAM KELAS</th>
                            <th>STATUS DRAF</th>
                            <th class="text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $totalSoal = $draftStatus[$c->class_id]->total ?? 0;
                            ?>
                            <tr>
                                <td>
                                    <div class="program-info">
                                        <div class="program-icon-box">
                                            <i class="fa-solid fa-school-flag"></i>
                                        </div>
                                        <div>
                                            <strong><?php echo e($c->program_name); ?></strong>
                                            <small>ID Kelas: #<?php echo e($c->class_id); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if($totalSoal > 0): ?>
                                        <span class="badge-status success">
                                            <span class="cp-dot-wrapper">
                                                <i class="cp-dot"></i>
                                                <i class="cp-dot-pulse"></i>
                                            </span>
                                            <?php echo e($totalSoal); ?> Soal Baru
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-status empty">0 Soal Masuk</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <div class="action-group-end">
                                        <?php if($totalSoal > 0): ?>
                                            <a href="<?php echo e(route('admin.tryout.export_draft', $c->class_id)); ?>" class="btn-icon-sm green-soft" title="Download CSV">
                                                <i class="fa-solid fa-file-csv"></i>
                                            </a>
                                        <?php endif; ?>

                                        <a href="<?php echo e(route('admin.tryout.review', $c->class_id)); ?>" class="btn-review-main">
                                            <span>Review</span>
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </section>

        
        <section class="cp-main-card">
            <div class="card-header-flex">
                <div class="title-with-icon">
                    <div class="icon-box blue"><i class="fa-solid fa-paper-plane"></i></div>
                    <div>
                        <h2>Paket TO Terbit</h2>
                        <p>Katalog paket yang sudah aktif di aplikasi siswa.</p>
                    </div>
                </div>
            </div>

            <div class="active-list">
                <?php $__empty_1 = true; $__currentLoopData = $activePackages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pkg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="package-item-card">
                        <div class="pkg-icon">
                            <i class="fa-solid <?php echo e($pkg->total_questions > 0 ? 'fa-stopwatch-20' : 'fa-file-lines'); ?>"></i>
                        </div>
                        <div class="pkg-info">
                            <strong><?php echo e($pkg->title); ?></strong>
                            <span><?php echo e($pkg->class_name); ?> • <?php echo e($pkg->total_questions); ?> Soal • <?php echo e($pkg->duration); ?> menit</span>
                            <small class="pkg-id">ID: #<?php echo e($pkg->tryout_id); ?></small>
                        </div>
                        <form action="<?php echo e(route('admin.tryout.destroy_package', $pkg->tryout_id)); ?>" method="POST" onsubmit="return confirm('Hapus paket ini dari HP siswa?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn-del-pkg" title="Hapus Paket">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state-lite">
                        <i class="fa-solid fa-ghost"></i>
                        <p>Belum ada paket dipublikasikan.</p>
                        <small>Publish paket tryout dari draf yang tersedia.</small>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
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

    .cp-page { padding: 10px; font-family: 'Montserrat', sans-serif; animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* ── Header ── */
    .cp-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }
    .cp-breadcrumb-capsule {
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
    .cp-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .cp-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }

    /* Alerts */
    .tm-alert-modern { padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 15px; font-weight: 800; border-left: 6px solid; font-size: 13px; }
    .tm-alert-modern.success { background: #e6f7ed; color: #15803d; border-color: #22c55e; }
    .tm-alert-modern.error { background: #fee2e2; color: #b91c1c; border-color: #ef4444; }
    .tm-alert-modern i { font-size: 20px; }
    .tm-alert-modern p { margin: 2px 0 0; font-size: 12px; opacity: 0.9; font-weight: 600; }

    /* Stats Grid */
    .cp-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .cp-stat-card {
        background: var(--spekta-white);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: all 0.25s ease;
        position: relative;
    }
    .cp-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.03);
    }
    .cp-stat-card.card-red:hover { border-color: var(--spekta-red); }
    .cp-stat-card.card-teal:hover { border-color: var(--spekta-teal); }
    .cp-stat-card.card-gray:hover { border-color: var(--spekta-gray); }

    .cp-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 16px;
    }
    .cp-stat-icon.red { background: var(--spekta-red-light); color: var(--spekta-red); }
    .cp-stat-icon.teal { background: var(--spekta-teal-light); color: var(--spekta-teal); }
    .cp-stat-icon.gray { background: var(--spekta-gray-light); color: var(--text-muted); }

    .cp-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .cp-stat-info h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1;
        display: flex;
        align-items: baseline;
        gap: 4px;
    }
    .cp-stat-info h2 span {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .cp-pulse-dot {
        position: absolute;
        top: 14px; right: 14px;
        width: 6px; height: 6px;
        background: var(--spekta-red);
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(229, 57, 53, 0.7);
        animation: pulseRed 1.5s infinite;
    }
    @keyframes pulseRed {
        0% { box-shadow: 0 0 0 0 rgba(229, 57, 53, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(229, 57, 53, 0); }
        100% { box-shadow: 0 0 0 0 rgba(229, 57, 53, 0); }
    }

    /* Layout Panel */
    .tm-grid-layout { display: grid; grid-template-columns: 1.35fr 1fr; gap: 24px; }
    .cp-main-card { background: var(--spekta-white); border-radius: 16px; padding: 20px; border: 1px solid var(--border-soft); box-shadow: 0 4px 15px rgba(0,0,0,0.01); }

    .title-with-icon { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
    .icon-box { width: 38px; height: 38px; border-radius: 10px; display: grid; place-items: center; font-size: 16px; }
    .icon-box.red { background: var(--spekta-red-light); color: var(--spekta-red); }
    .icon-box.blue { background: var(--spekta-teal-light); color: var(--spekta-teal); }

    .card-header-flex h2 { font-size: 15px; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0; }
    .card-header-flex p { font-size: 11px; color: var(--text-muted); margin: 0; font-weight: 600; }

    /* Table Design */
    .cp-table-modern { width: 100%; border-collapse: collapse; }
    .cp-table-modern th { font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; padding: 12px 14px; border-bottom: 2px solid var(--spekta-gray-light); letter-spacing: 0.05em; }
    .cp-table-modern td { padding: 14px; border-bottom: 1px solid var(--spekta-gray-light); vertical-align: middle; }
    .cp-table-modern tbody tr:last-child td { border-bottom: none; }
    .cp-table-modern tbody tr:hover { background: #fafbfc; }

    .program-info { display: flex; align-items: center; gap: 10px; }
    .program-icon-box { width: 34px; height: 34px; background: var(--spekta-gray-light); border-radius: 8px; display: grid; place-items: center; font-size: 14px; color: var(--text-muted); border: 1px solid var(--border-soft); }
    .program-info strong { display: block; font-size: 13px; font-weight: 800; color: var(--text-main); }
    .program-info small { font-size: 10px; color: var(--text-muted); font-weight: 600; margin-top: 2px; }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 22px;
        padding: 0 8px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }
    .badge-status .cp-dot-wrapper { position: relative; width: 5px; height: 5px; display: inline-block; }
    .badge-status .cp-dot { width: 5px; height: 5px; border-radius: 99px; background: currentColor; display: block; position: absolute; left: 0; top: 0; }
    .badge-status .cp-dot-pulse { width: 5px; height: 5px; border-radius: 99px; background: currentColor; display: block; position: absolute; left: 0; top: 0; opacity: 0.4; transform: scale(1); animation: dotGlow 1.8s infinite ease-in-out; }
    @keyframes dotGlow { 0% { transform: scale(1); opacity: 0.8; } 100% { transform: scale(3.2); opacity: 0; } }

    .badge-status.success { background: #e6f7ed; color: #15803d; box-shadow: 0 2px 6px rgba(22, 163, 74, 0.1); }
    .badge-status.empty { background: var(--spekta-gray-light); color: var(--text-muted); }

    .action-group-end { display: flex; align-items: center; gap: 8px; justify-content: flex-end; }
    .btn-icon-sm { width: 30px; height: 30px; display: grid; place-items: center; border-radius: 8px; transition: 0.2s; text-decoration: none; }
    .btn-icon-sm.green-soft { background: #e6f7ed; color: #15803d; border: 1px solid #bbf7d0; }
    .btn-icon-sm:hover { transform: translateY(-1px); filter: brightness(0.95); }

    .btn-review-main {
        background: #1f2937; color: white; padding: 6px 14px; border-radius: 8px;
        font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s; text-decoration: none;
    }
    .btn-review-main:hover { background: var(--spekta-red); box-shadow: 0 4px 10px rgba(229, 57, 53, 0.2); }

    .package-item-card { display: flex; align-items: center; gap: 12px; background: var(--spekta-gray-light); padding: 12px; border-radius: 12px; margin-bottom: 12px; border: 1px solid var(--border-soft); transition: 0.2s; }
    .package-item-card:hover { border-color: var(--spekta-gray); background: white; transform: translateY(-1.5px); }
    .pkg-icon { width: 36px; height: 36px; background: white; color: #0269a1; border-radius: 8px; display: grid; place-items: center; font-size: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); border: 1px solid var(--border-soft); }
    .pkg-info { flex: 1; }
    .pkg-info strong { display: block; font-size: 13px; color: var(--text-main); }
    .pkg-info span { font-size: 11px; color: var(--text-muted); font-weight: 600; }
    .pkg-info .pkg-id { font-size: 9px; color: #94a3b8; display: block; margin-top: 2px; }
    .btn-del-pkg { background: transparent; border: none; color: #cbd5e1; cursor: pointer; font-size: 12px; transition: 0.2s; padding: 6px; display: inline-flex; align-items: center; justify-content: center; }
    .btn-del-pkg:hover { color: var(--spekta-red); }

    .empty-state-lite { text-align: center; padding: 30px; color: var(--text-muted); font-weight: 600; }
    .empty-state-lite i { font-size: 24px; margin-bottom: 8px; display: block; color: var(--spekta-gray); }
    .empty-state-lite small { font-size: 11px; display: block; margin-top: 4px; }
    .text-right { text-align: right; }

    @media (max-width: 1100px) {
        .tm-grid-layout { grid-template-columns: 1fr; }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/tryout/index.blade.php ENDPATH**/ ?>