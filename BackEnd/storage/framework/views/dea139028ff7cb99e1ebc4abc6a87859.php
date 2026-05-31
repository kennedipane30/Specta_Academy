

<?php $__env->startSection('title', 'Pilih Paket Tryout'); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-page">
    <section class="cp-header" style="margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 24px; font-weight: 900;">Paket Tryout: <?php echo e($class->program_name); ?></h1>
            <p style="color: #64748b;">Pilih salah satu paket di bawah ini untuk melihat daftar nilai siswa.</p>
        </div>
        <a href="<?php echo e(route('admin.scores.index')); ?>" class="cp-back-btn" style="text-decoration: none; color: #64748b; font-weight: 700;">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </section>

    <div class="cp-main-card" style="background: white; border-radius: 24px; padding: 25px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #f8fafc;">
                    <th style="padding: 15px; font-size: 11px; color: #94a3b8; text-transform: uppercase;">Nama Paket</th>
                    <th style="padding: 15px; font-size: 11px; color: #94a3b8; text-transform: uppercase;">Durasi</th>
                    <th style="padding: 15px; font-size: 11px; color: #94a3b8; text-transform: uppercase; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $tryouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $to): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr style="border-bottom: 1px solid #f8fafc;">
                    <td style="padding: 15px; font-weight: 800; color: #111827;"><?php echo e($to->title); ?></td>
                    <td style="padding: 15px; color: #64748b;"><?php echo e($to->duration); ?> Menit</td>
                    <td style="padding: 15px; text-align: right;">
                        <a href="<?php echo e(route('admin.scores.result', $to->tryout_id)); ?>" style="background: #d90429; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 11px; font-weight: 800;">
                            REKAP NILAI <i class="fa-solid fa-chevron-right" style="margin-left: 5px;"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="3" style="padding: 40px; text-align: center; color: #94a3b8;">Belum ada paket tryout yang diterbitkan untuk kelas ini.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Perkuliahan\SEMESTER 4\PA ll\Specta_Academy\BackEnd\resources\views/admin/tryout/pilih_paket.blade.php ENDPATH**/ ?>