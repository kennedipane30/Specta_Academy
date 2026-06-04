<?php $__env->startSection('title', 'Katalog Program - Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $classCollection = method_exists($classes, 'getCollection') ? $classes->getCollection() : collect($classes);
    $totalProgram = method_exists($classes, 'total') ? $classes->total() : $classCollection->count();
    $avgPrice = $classCollection->count() > 0 ? round($classCollection->avg('price')) : 0;
?>

<div class="cp-page">

    <section class="cp-header">
        <div>
            <span class="cp-tagline">SPEKTA CONTROL CENTER</span>
            <h1 class="cp-title">Katalog Program</h1>
            <p class="cp-subtitle">Manajemen pusat konten. Monitor harga, deskripsi, dan penugasan pengajar dalam satu dashboard.</p>
        </div>
        <a href="<?php echo e(route('admin.classes.create')); ?>" class="cp-primary-btn">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Program</span>
        </a>
    </section>

    <!-- Stats Section -->
    <section class="cp-stats">
        <div class="cp-stat-card border-red">
            <div class="cp-stat-content">
                <p>Total Program</p>
                <h2><?php echo e(number_format($totalProgram)); ?></h2>
            </div>
            <div class="cp-stat-icon"><i class="fa-solid fa-layer-group"></i></div>
        </div>
        <div class="cp-stat-card border-green">
            <div class="cp-stat-content">
                <p>Rata-rata Harga</p>
                <h2>Rp <?php echo e(number_format($avgPrice, 0, ',', '.')); ?></h2>
            </div>
            <div class="cp-stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
        </div>
        <div class="cp-stat-card border-blue">
            <div class="cp-stat-content">
                <p>Pengajar Aktif</p>
                <h2><?php echo e(\App\Models\User::where('role_id', 2)->count()); ?></h2>
            </div>
            <div class="cp-stat-icon"><i class="fa-solid fa-user-tie"></i></div>
        </div>
        <div class="cp-stat-card border-purple">
            <div class="cp-stat-content">
                <p>Status Sinkron</p>
                <h2>Terhubung</h2>
            </div>
            <div class="cp-stat-icon"><i class="fa-solid fa-signal"></i></div>
        </div>
    </section>

    <section class="cp-main-panel">
        <div class="cp-panel-heading">
            <h2>Daftar Program & Penugasan</h2>
            <p>Klik ikon buku untuk mengatur mata pelajaran dan menunjuk guru pengajar.</p>
        </div>

        <div class="cp-program-list">
            <?php $__empty_1 = true; $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $imageUrl = $item->image_url ?? asset('storage/' . $item->image);

                    // QUERY FIX: Melakukan join secara eksplisit ke tabel users dan subjects
                    $assignments = \DB::table('teacher_assignments')
                        ->join('users', 'teacher_assignments.user_id', '=', 'users.usersID')
                        ->join('subjects', 'teacher_assignments.subject_id', '=', 'subjects.subject_id')
                        ->where('teacher_assignments.class_id', $item->class_id)
                        ->select('subjects.name as subject_name', 'users.name as teacher_name')
                        ->get();
                ?>

                <div class="cp-card">
                    <div class="cp-card-img">
                        <img src="<?php echo e($imageUrl); ?>" alt="">
                        <div class="cp-badge-price">Rp <?php echo e(number_format($item->price, 0, ',', '.')); ?></div>
                    </div>

                    <div class="cp-card-content">
                        <div class="cp-card-header">
                            <span class="cp-id">ID: #<?php echo e($item->class_id); ?></span>
                            <h3 class="cp-class-name"><?php echo e($item->program_name); ?></h3>
                            <p class="cp-class-desc"><?php echo e($item->description); ?></p>
                        </div>

                        <div class="cp-curriculum-section">
                            <div class="cp-section-label">
                                <i class="fa-solid fa-book-bookmark"></i> KURIKULUM & PENGAJAR
                            </div>
                            <div class="cp-subject-grid">
                                <?php $__empty_2 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                    <div class="cp-subject-item">
                                        <div class="cp-subject-name"><?php echo e($assign->subject_name); ?></div>
                                        <div class="cp-teacher-name">
                                            <i class="fa-solid fa-chalkboard-user"></i> <?php echo e($assign->teacher_name); ?>

                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                    <div class="cp-empty-subject">Belum ada mata pelajaran & pengajar.</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="cp-card-actions">
                            <a href="<?php echo e(route('admin.classes.edit', $item->class_id)); ?>" class="btn-main dark">
                                <i class="fa-solid fa-sliders"></i> Konfigurasi
                            </a>
                            <div class="btn-group">
                                <a href="<?php echo e(route('admin.assignments.index', ['class_id' => $item->class_id])); ?>" class="btn-icon blue" title="Atur Kurikulum">
                                    <i class="fa-solid fa-book"></i>
                                </a>
                                <form action="<?php echo e(route('admin.classes.destroy', $item->class_id)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn-icon red" onclick="return confirm('Hapus program?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="cp-empty-state">Belum ada program kelas yang dibuat.</div>
            <?php endif; ?>
        </div>
    </section>
</div>

<style>
    /* CSS tetap dipertahankan sesuai aslinya */
    .cp-page { padding: 10px; font-family: 'Plus Jakarta Sans', sans-serif; }
    .cp-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .cp-tagline { color: #d90429; font-weight: 800; font-size: 11px; letter-spacing: 1.5px; }
    .cp-title { font-size: 32px; font-weight: 900; color: #111827; margin: 5px 0; }
    .cp-subtitle { color: #6b7280; font-size: 14px; font-weight: 500; }
    .cp-primary-btn { background: #d90429; color: white; padding: 12px 24px; border-radius: 14px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 20px rgba(217, 4, 41, 0.2); transition: 0.3s; }
    .cp-primary-btn:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(217, 4, 41, 0.3); }
    .cp-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 35px; }
    .cp-stat-card { background: white; padding: 25px; border-radius: 22px; border: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; position: relative; overflow: hidden; }
    .cp-stat-card.border-red { border-bottom: 4px solid #d90429; }
    .cp-stat-card.border-green { border-bottom: 4px solid #10b981; }
    .cp-stat-card.border-blue { border-bottom: 4px solid #3b82f6; }
    .cp-stat-card.border-purple { border-bottom: 4px solid #8b5cf6; }
    .cp-stat-content p { color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 5px; }
    .cp-stat-content h2 { font-size: 24px; font-weight: 800; color: #1e293b; margin: 0; }
    .cp-stat-icon { font-size: 24px; color: #cbd5e1; }
    .cp-main-panel { background: white; padding: 30px; border-radius: 30px; border: 1px solid #f1f5f9; }
    .cp-panel-heading h2 { font-size: 20px; font-weight: 800; color: #111827; margin: 0; }
    .cp-panel-heading p { color: #64748b; font-size: 14px; margin: 5px 0 25px; }
    .cp-card { display: flex; background: #fff; border-radius: 28px; border: 1px solid #f1f5f9; padding: 18px; gap: 25px; margin-bottom: 25px; transition: 0.3s; }
    .cp-card:hover { border-color: #d9042940; box-shadow: 0 20px 40px rgba(0,0,0,0.03); }
    .cp-card-img { width: 300px; height: 200px; border-radius: 22px; overflow: hidden; position: relative; flex-shrink: 0; }
    .cp-card-img img { width: 100%; height: 100%; object-fit: cover; }
    .cp-badge-price { position: absolute; top: 15px; left: 15px; background: rgba(255,255,255,0.9); padding: 8px 15px; border-radius: 12px; font-weight: 800; color: #d90429; font-size: 13px; backdrop-filter: blur(5px); }
    .cp-card-content { flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
    .cp-id { font-size: 11px; font-weight: 800; color: #94a3b8; }
    .cp-class-name { font-size: 24px; font-weight: 900; color: #111827; margin: 2px 0 8px; }
    .cp-class-desc { font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 15px; }
    .cp-curriculum-section { background: #f8fafc; border-radius: 20px; padding: 15px 20px; margin-bottom: 20px; border: 1px solid #f1f5f9; }
    .cp-section-label { font-size: 11px; font-weight: 800; color: #d90429; letter-spacing: 0.5px; margin-bottom: 12px; }
    .cp-subject-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .cp-subject-item { background: white; padding: 10px 15px; border-radius: 14px; border: 1px solid #edf2f7; }
    .cp-subject-name { font-size: 13px; font-weight: 800; color: #1e293b; }
    .cp-teacher-name { font-size: 11px; color: #64748b; font-weight: 600; margin-top: 3px; }
    .cp-teacher-name i { color: #d90429; margin-right: 4px; }
    .cp-empty-subject { font-size: 12px; color: #94a3b8; font-style: italic; }
    .cp-card-actions { display: flex; gap: 10px; align-items: center; }
    .btn-main { flex-grow: 1; height: 48px; display: flex; align-items: center; justify-content: center; gap: 10px; border-radius: 15px; font-weight: 700; font-size: 13px; text-decoration: none; transition: 0.3s; }
    .btn-main.dark { background: #111827; color: white; }
    .btn-main:hover { opacity: 0.9; transform: translateY(-2px); }
    .btn-group { display: flex; gap: 8px; }
    .btn-icon { width: 48px; height: 48px; display: grid; place-items: center; border-radius: 15px; font-size: 16px; border: none; cursor: pointer; transition: 0.3s; }
    .btn-icon.blue { background: #eff6ff; color: #3b82f6; }
    .btn-icon.red { background: #fef2f2; color: #ef4444; }
    .btn-icon:hover { transform: translateY(-2px); filter: brightness(0.95); }
    @media (max-width: 1024px) {
        .cp-stats { grid-template-columns: repeat(2, 1fr); }
        .cp-card { flex-direction: column; }
        .cp-card-img { width: 100%; height: 220px; }
        .cp-subject-grid { grid-template-columns: 1fr; }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\Specta_Academy\BackEnd\resources\views/admin/classes/index.blade.php ENDPATH**/ ?>