<?php $__env->startSection('title', 'Hasil Nilai Siswa'); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-page">
    <section class="cp-header" style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            
            <h1 style="font-size: 22px; font-weight: 900;">Hasil: <?php echo e($tryout->title ?? 'Paket Tryout'); ?></h1>
            <p style="color: #64748b;">Total <?php echo e($results->count()); ?> siswa telah menyelesaikan ujian ini.</p>
        </div>
        <a href="<?php echo e(route('admin.scores.index')); ?>" class="cp-back-btn" style="text-decoration: none; color: #111827; font-weight: 800; background: #f1f5f9; padding: 10px 20px; border-radius: 12px;">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </section>

    <div class="cp-main-card" style="background: white; border-radius: 24px; padding: 25px; border: 1px solid #f1f5f9;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; border-bottom: 2px solid #f8fafc;">
                    <th style="padding: 15px; font-size: 11px; color: #94a3b8; text-transform: uppercase;">Nama Siswa</th>
                    <th style="padding: 15px; font-size: 11px; color: #94a3b8; text-transform: uppercase;">Benar</th>
                    <th style="padding: 15px; font-size: 11px; color: #94a3b8; text-transform: uppercase;">Skor Akhir</th>
                    <th style="padding: 15px; font-size: 11px; color: #94a3b8; text-transform: uppercase;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 15px;">
                        <strong style="color: #111827; display: block;"><?php echo e($res->user_data->name ?? 'Siswa tidak ditemukan'); ?></strong>
                        <small style="color: #94a3b8;"><?php echo e($res->user_data->email ?? '-'); ?></small>
                    </td>
                    <td style="padding: 15px; font-weight: 700; color: #10b981;"><?php echo e($res->total_correct); ?> Soal</td>
                    <td style="padding: 15px;">
                        <span style="background: #111827; color: white; padding: 5px 12px; border-radius: 8px; font-weight: 900; font-size: 16px;">
                            <?php echo e($res->score); ?>

                        </span>
                    </td>
                    <td style="padding: 15px; color: #64748b; font-size: 12px;">
                        <?php echo e($res->created_at ? $res->created_at->format('d M Y, H:i') : '-'); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" style="padding: 50px; text-align: center; color: #94a3b8; font-style: italic;">Belum ada siswa yang mengerjakan tryout ini.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/tryout/scores.blade.php ENDPATH**/ ?>