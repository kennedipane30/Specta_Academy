<?php $__env->startSection('title', 'Master Tryout - Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-page">

    
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

    
    <section class="tm-hero-header">
        <div class="tm-hero-content">
            <div class="tm-hero-text">
                <span class="tm-pre-title">ADMIN TRYOUT CENTER</span>
                <h1 class="tm-main-title">Manajemen Paket TO</h1>
                <p class="tm-sub-title">Kurasi setoran soal dari pengajar dan publikasikan menjadi paket Tryout resmi untuk aplikasi mobile siswa.</p>
            </div>
        </div>
        
        <div class="tm-hero-summary">
            <div class="summary-card highlight">
                <strong><?php echo e($activePackages->count()); ?></strong>
                <span>Paket Live</span>
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
                                        <strong><?php echo e($c->program_name); ?></strong>
                                        <small>ID Kelas: #<?php echo e($c->class_id); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <?php if($totalSoal > 0): ?>
                                        <span class="badge-status success">
                                            <i class="fa-solid fa-file-circle-check"></i> <?php echo e($totalSoal); ?> Soal Baru
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
                            <i class="fa-solid fa-stopwatch-20"></i>
                        </div>
                        <div class="pkg-info">
                            <strong><?php echo e($pkg->title); ?></strong>
                            <span><?php echo e($pkg->classModel->program_name); ?> • <?php echo e($pkg->questions_count); ?> Soal</span>
                        </div>
                        <form action="<?php echo e(route('admin.tryout.destroy_package', $pkg->tryout_id)); ?>" method="POST" onsubmit="return confirm('Hapus paket ini dari HP siswa?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn-del-pkg" title="Hapus Paket"><i class="fa-solid fa-trash-can"></i></button>
                        </form>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="empty-state-lite">
                        <i class="fa-solid fa-ghost"></i>
                        <p>Belum ada paket dipublikasikan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<style>
    /* Global & Animations */
    .cp-page { padding: 10px; font-family: 'Montserrat', sans-serif; animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Alert Styling */
    .tm-alert-modern { padding: 18px 24px; border-radius: 16px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px; font-weight: 600; border-left: 6px solid; }
    .tm-alert-modern.success { background: #dcfce7; color: #15803d; border-color: #22c55e; }
    .tm-alert-modern.error { background: #fee2e2; color: #b91c1c; border-color: #ef4444; }
    .tm-alert-modern i { font-size: 24px; }
    .tm-alert-modern p { margin: 2px 0 0; font-size: 13px; opacity: 0.9; }

    /* Hero Section */
    .tm-hero-header {
        background: linear-gradient(135deg, #111827 0%, #1e293b 100%);
        border-radius: 28px; padding: 40px; color: white;
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 30px; box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    .tm-main-title { font-size: 32px; font-weight: 900; margin: 8px 0; letter-spacing: -1px; }
    .tm-pre-title { font-size: 10px; font-weight: 800; letter-spacing: 2px; color: #d90429; text-transform: uppercase; }
    .tm-sub-title { font-size: 14px; opacity: 0.7; max-width: 500px; line-height: 1.6; }
    .summary-card { background: #d90429; padding: 20px; border-radius: 22px; text-align: center; min-width: 130px; }
    .summary-card strong { display: block; font-size: 32px; font-weight: 900; }
    .summary-card span { font-size: 10px; font-weight: 700; text-transform: uppercase; opacity: 0.8; }

    /* Grid & Cards */
    .tm-grid-layout { display: grid; grid-template-columns: 1.5fr 1fr; gap: 25px; }
    .cp-main-card { background: white; border-radius: 30px; padding: 30px; border: 1px solid #f1f5f9; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
    
    .title-with-icon { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; }
    .icon-box { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; font-size: 18px; }
    .icon-box.red { background: #fee2e2; color: #d90429; }
    .icon-box.blue { background: #e0f2fe; color: #0369a1; }
    
    .card-header-flex h2 { font-size: 18px; font-weight: 900; color: #111827; margin: 0; }
    .card-header-flex p { font-size: 13px; color: #94a3b8; margin: 4px 0 0; font-weight: 500; }

    /* Table Design */
    .cp-table-modern { width: 100%; border-collapse: collapse; }
    .cp-table-modern th { text-align: left; padding: 12px; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; }
    .cp-table-modern td { padding: 18px 12px; border-bottom: 1px solid #f8fafc; }

    .program-info strong { display: block; font-size: 14px; color: #111827; text-transform: uppercase; }
    .program-info small { font-size: 11px; color: #94a3b8; font-weight: 600; }

    .badge-status { padding: 6px 12px; border-radius: 10px; font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px; }
    .badge-status.success { background: #ecfdf5; color: #10b981; }
    .badge-status.empty { background: #f8fafc; color: #cbd5e1; }

    /* Actions */
    .action-group-end { display: flex; align-items: center; gap: 8px; justify-content: flex-end; }
    .btn-icon-sm { width: 36px; height: 36px; display: grid; place-items: center; border-radius: 10px; transition: 0.2s; text-decoration: none; }
    .btn-icon-sm.green-soft { background: #ecfdf5; color: #10b981; border: 1px solid #d1fae5; }
    .btn-icon-sm:hover { transform: translateY(-2px); filter: brightness(0.95); }

    .btn-review-main { 
        background: #111827; color: white; padding: 8px 18px; border-radius: 12px; 
        font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; text-decoration: none;
    }
    .btn-review-main:hover { background: #d90429; box-shadow: 0 8px 15px rgba(217, 4, 41, 0.2); }

    /* Right Panel List */
    .package-item-card { display: flex; align-items: center; gap: 15px; background: #f8fafc; padding: 15px; border-radius: 20px; margin-bottom: 12px; border: 1px solid #edf2f7; transition: 0.2s; }
    .package-item-card:hover { border-color: #d1d5db; background: white; }
    .pkg-icon { width: 40px; height: 40px; background: white; color: #0369a1; border-radius: 12px; display: grid; place-items: center; font-size: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); }
    .pkg-info { flex: 1; }
    .pkg-info strong { display: block; font-size: 13px; color: #111827; }
    .pkg-info span { font-size: 11px; color: #64748b; font-weight: 600; }
    .btn-del-pkg { background: transparent; border: none; color: #cbd5e1; cursor: pointer; font-size: 14px; transition: 0.2s; }
    .btn-del-pkg:hover { color: #d90429; }

    .empty-state-lite { text-align: center; padding: 40px; color: #cbd5e1; }
    .empty-state-lite i { font-size: 32px; margin-bottom: 10px; display: block; }
    .text-right { text-align: right; }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\Specta_Academy\BackEnd\resources\views/admin/tryout/index.blade.php ENDPATH**/ ?>