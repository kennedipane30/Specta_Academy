

<?php $__env->startSection('title', 'Rekap Nilai - Pilih Kelas'); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-page">
    <section class="tm-hero-header" style="background: linear-gradient(135deg, #111827 0%, #1e293b 100%);">
        <div class="tm-hero-text">
            <span class="tm-pre-title">STUDENT SCORE CENTER</span>
            <h1 class="tm-main-title">Rekap Nilai Tryout</h1>
            <p class="tm-sub-title">Pilih program kelas untuk melihat daftar paket tryout dan hasil ujian siswa.</p>
        </div>
    </section>

    <div class="cp-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="cp-main-card" style="background: white; border-radius: 20px; padding: 20px; border: 1px solid #f1f5f9; text-align: center;">
            <div style="width: 60px; height: 60px; background: #ffe8ee; color: #d90429; border-radius: 15px; display: grid; place-items: center; margin: 0 auto 15px; font-size: 24px;">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h3 style="font-size: 18px; font-weight: 900; color: #111827; margin-bottom: 5px;"><?php echo e($class->program_name); ?></h3>
            <p style="font-size: 12px; color: #94a3b8; margin-bottom: 20px;">ID Kelas: #<?php echo e($class->class_id); ?></p>
            
            <a href="<?php echo e(route('admin.scores.pilih_tryout', $class->class_id)); ?>" class="cp-primary-btn" style="background: #111827; display: block; text-decoration: none; color: white; padding: 12px; border-radius: 12px; font-weight: 800; font-size: 12px;">
                LIHAT DAFTAR PAKET
            </a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Perkuliahan\SEMESTER 4\PA ll\Specta_Academy\BackEnd\resources\views/admin/tryout/pilih_kelas.blade.php ENDPATH**/ ?>