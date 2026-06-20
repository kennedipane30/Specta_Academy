<?php $__env->startSection('title', 'Hasil Nilai Siswa'); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-page">

    
    <section class="cp-header">
        <div class="cp-header-left">
            <span class="cp-breadcrumb-capsule">Student Score Center</span>
            <!-- Menampilkan judul dinamis dari variabel $tryoutTitle di controller Anda -->
            <h1>Hasil: <span style="color: var(--spekta-teal);"><?php echo e($tryoutTitle ?? 'Paket Tryout'); ?></span></h1>
            <!-- FIX: Menggunakan count($results) untuk menghitung array biasa -->
            <p>Total <?php echo e(count($results)); ?> siswa telah menyelesaikan ujian ini secara nasional.</p>
        </div>

        <div class="cp-header-actions">
            <a href="<?php echo e(route('admin.scores.index')); ?>" class="cp-secondary-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </section>

    
    <div class="cp-main-card">
        <div class="cp-table-wrap">
            <table class="cp-table">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Jawaban Benar</th>
                        <th>Skor Akhir</th>
                        <th>Tanggal Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $res): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php if($res): ?>
                            <?php
                                // Konversi paksa array bersarang (nested array) dari Go ke Object secara rekursif
                                $resObj = (object) json_decode(json_encode($res));
                                $studentName = $resObj->user_data->name ?? 'Siswa tidak ditemukan';
                                $studentEmail = $resObj->user_data->email ?? '-';
                                $createdAt = isset($resObj->created_at) ? \Carbon\Carbon::parse($resObj->created_at) : null;
                            ?>
                            <tr>
                                
                                <td>
                                    <div class="cp-student-cell">
                                        <div class="cp-student-avatar">
                                            <?php echo e(strtoupper(substr($studentName, 0, 1))); ?>

                                        </div>
                                        <div class="cp-student-info">
                                            <strong><?php echo e($studentName); ?></strong>
                                            <span><?php echo e($studentEmail); ?></span>
                                        </div>
                                    </div>
                                </td>

                                
                                <td class="text-correct">
                                    <i class="fa-solid fa-circle-check"></i> <?php echo e($resObj->total_correct ?? 0); ?> Soal
                                </td>

                                
                                <td>
                                    <span class="cp-score-badge">
                                        <?php echo e($resObj->score ?? 0); ?>

                                    </span>
                                </td>

                                
                                <td class="cp-date-cell">
                                    <i class="fa-regular fa-clock"></i>
                                    <?php echo e($createdAt ? $createdAt->format('d M Y, H:i') : '-'); ?> WIB
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4">
                            <div class="cp-empty-state">
                                <i class="fa-solid fa-user-slash"></i>
                                <span>Belum ada siswa yang mengerjakan tryout ini.</span>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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

    .cp-page {
        font-family: 'Montserrat', sans-serif;
        padding: 10px;
        animation: fadeIn 0.4s ease-out;
    }
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
    .cp-secondary-btn {
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
    .cp-secondary-btn:hover {
        background: var(--spekta-gray-light);
        color: var(--text-main);
        border-color: var(--spekta-gray);
    }

    /* Table Container Card */
    .cp-main-card {
        background: var(--spekta-white);
        border-radius: 16px;
        padding: 20px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    }

    .cp-table-wrap { overflow-x: auto; border-radius: 12px; }
    .cp-table { width: 100%; border-collapse: collapse; min-width: 700px; }
    .cp-table th { text-align: left; padding: 12px 14px; font-size: 10px; color: var(--text-muted); text-transform: uppercase; border-bottom: 2px solid var(--spekta-gray-light); font-weight: 800; letter-spacing: 0.05em; }
    .cp-table td { padding: 14px; border-bottom: 1px solid var(--spekta-gray-light); vertical-align: middle; }
    .cp-table tbody tr:last-child td { border-bottom: none; }
    .cp-table tbody tr:hover { background: #fafbfc; }

    /* Student Cell Profile */
    .cp-student-cell { display: flex; align-items: center; gap: 10px; }
    .cp-student-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        background: var(--spekta-teal-light);
        color: var(--spekta-teal);
        font-weight: 900;
        font-size: 13px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    .cp-student-info strong { display: block; font-size: 13px; font-weight: 800; color: var(--text-main); }
    .cp-student-info span { display: block; font-size: 10px; color: var(--text-muted); font-weight: 600; margin-top: 1px; }

    .text-correct { color: #16a34a; font-weight: 700; font-size: 13px; }
    .text-correct i { font-size: 12px; margin-right: 3px; }

    /* Score Badge */
    .cp-score-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 50px;
        height: 28px;
        padding: 0 12px;
        border-radius: 8px;
        background: #1f2937;
        color: var(--spekta-white);
        font-size: 14px;
        font-weight: 900;
        box-shadow: 0 3px 8px rgba(31, 41, 55, 0.15);
    }

    .cp-date-cell { color: var(--text-muted); font-size: 11px; font-weight: 700; }
    .cp-date-cell i { color: var(--spekta-gray); margin-right: 3px; }

    .cp-empty-state {
        padding: 40px;
        text-align: center;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .cp-empty-state i { font-size: 20px; color: var(--spekta-gray); }

    @media (max-width: 768px) {
        .cp-header { flex-direction: column; align-items: flex-start; gap: 14px; }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/tryout/scores.blade.php ENDPATH**/ ?>