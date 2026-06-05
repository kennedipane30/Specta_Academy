<?php $__env->startSection('title', 'Kelola Latihan ' . $subject_name); ?>
<?php $__env->startSection('subtitle', 'Import dan kelola latihan soal mingguan'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $practiceCollection = collect($practices);
    $practiceByWeek = $practiceCollection->keyBy('week');
    $filledWeeks = $practiceCollection->pluck('week')->unique()->count();
    $progress = round(($filledWeeks / 20) * 100);
    $totalQuestions = $practiceCollection->sum('total_soal');
?>

<div class="pq-page">

    
    <section class="pq-detail-header">
        <div>
            <a href="<?php echo e(route('pengajar.latihan.index')); ?>" class="pq-back">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>

            <span>Practice Weekly Manager</span>
            <h1><?php echo e($subject_name); ?></h1>
            <p><?php echo e($class->program_name); ?> • Kelola latihan soal untuk 20 minggu.</p>
        </div>

        <div class="pq-progress-box">
            <strong><?php echo e($filledWeeks); ?>/20</strong>
            <span>Minggu terisi</span>
            <div>
                <em style="width: <?php echo e($progress); ?>%"></em>
            </div>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="pq-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="pq-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="pq-alert error">
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

    
    <section class="pq-upload-panel">
        <div class="pq-upload-head">
            <div>
                <span>CSV Import</span>
                <h2>Import Latihan Soal</h2>
                <p>Pilih minggu pembelajaran, lalu unggah file CSV berisi soal latihan.</p>
            </div>

            <div class="pq-total-box">
                <strong><?php echo e(number_format($totalQuestions)); ?></strong>
                <span>Total Soal</span>
            </div>
        </div>

        <form action="<?php echo e(route('pengajar.latihan.store', $class->class_id)); ?>" method="POST" enctype="multipart/form-data" class="pq-upload-form">
            <?php echo csrf_field(); ?>

            <input type="hidden" name="subject" value="<?php echo e($subject_name); ?>">

            <div class="pq-field">
                <label>Pilih Minggu</label>
                <div>
                    <i class="fa-solid fa-calendar-week"></i>
                    <select name="week" required>
                        <?php for($i = 1; $i <= 20; $i++): ?>
                            <option value="<?php echo e($i); ?>" <?php echo e(old('week') == $i ? 'selected' : ''); ?>>
                                Minggu Ke-<?php echo e($i); ?>

                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="pq-field file">
                <label>File CSV</label>
                <div>
                    <i class="fa-solid fa-file-csv"></i>
                    <input type="file" name="file_csv" accept=".csv,text/csv" required>
                </div>
            </div>

            <button type="submit" class="pq-submit">
                <i class="fa-solid fa-file-import"></i>
                Proses Import
            </button>
        </form>

        <div class="pq-format-note">
            <i class="fa-solid fa-circle-info"></i>
            <span>Pastikan format CSV sudah sesuai template sistem agar seluruh soal dapat terbaca dengan benar.</span>
        </div>
    </section>

    
    <section class="pq-week-panel">
        <div class="pq-panel-head">
            <div>
                <span>Weekly Question Bank</span>
                <h2>Ringkasan Soal Terupload</h2>
                <p>Pantau minggu yang sudah memiliki bank soal dan kelola data latihan per pertemuan.</p>
            </div>
        </div>

        <div class="pq-week-strip">
            <?php for($i = 1; $i <= 20; $i++): ?>
                <?php $hasPractice = $practiceByWeek->has($i); ?>

                <div class="pq-week-dot <?php echo e($hasPractice ? 'filled' : ''); ?>">
                    <span><?php echo e($i); ?></span>
                </div>
            <?php endfor; ?>
        </div>

        <div class="pq-table-wrap">
            <table class="pq-table">
                <thead>
                    <tr>
                        <th>Pertemuan</th>
                        <th>Status Konten</th>
                        <th>Jumlah Soal</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $practices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <span class="pq-week-badge">
                                    Minggu <?php echo e($p->week); ?>

                                </span>
                            </td>

                            <td>
                                <span class="pq-content-status active">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Tersedia
                                </span>
                            </td>

                            <td>
                                <div class="pq-question-count">
                                    <strong><?php echo e(number_format($p->total_soal)); ?></strong>
                                    <span>soal</span>
                                </div>
                            </td>

                            <td>
                                <span class="pq-note-text">
                                    Latihan minggu ke-<?php echo e($p->week); ?> sudah dapat digunakan siswa.
                                </span>
                            </td>

                            <td>
                                <form action="<?php echo e(route('pengajar.latihan.destroy_week', [$class->class_id, $subject_name, $p->week])); ?>"
                                      method="POST"
                                      onsubmit="return confirm('Hapus semua soal di Minggu ke-<?php echo e($p->week); ?>?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>

                                    <button type="submit" class="pq-delete">
                                        <i class="fa-solid fa-trash"></i>
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5">
                                <div class="pq-empty">
                                    <i class="fa-solid fa-file-circle-plus"></i>
                                    <strong>Belum ada latihan soal yang diunggah.</strong>
                                    <span>Gunakan form import di atas untuk mengunggah bank soal pertama.</span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</div>

<style>
    .pq-page {
        width: 100%;
    }

    .pq-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 24px;
        margin-bottom: 22px;
        padding: 28px 30px;
        border-radius: 24px;
        color: #fff;
        background: linear-gradient(120deg, #cf002b 0%, #85001d 52%, #182033 100%);
        box-shadow: 0 18px 38px rgba(134, 0, 24, .18);
    }

    .pq-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        background: rgba(255,255,255,.13);
        border: 1px solid rgba(255,255,255,.17);
        color: #fff;
        font-size: 11px;
        font-weight: 900;
        margin-bottom: 18px;
    }

    .pq-detail-header span {
        display: block;
        color: rgba(255,255,255,.78);
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .16em;
        margin-bottom: 9px;
    }

    .pq-detail-header h1 {
        margin: 0 0 8px;
        color: #fff;
        font-size: 30px;
        font-weight: 900;
        letter-spacing: -0.04em;
        text-transform: uppercase;
    }

    .pq-detail-header p {
        margin: 0;
        color: rgba(255,255,255,.86);
        font-size: 13px;
        font-weight: 700;
    }

    .pq-progress-box {
        width: 230px;
        flex-shrink: 0;
        padding: 18px;
        border-radius: 20px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.17);
        backdrop-filter: blur(12px);
    }

    .pq-progress-box strong {
        display: block;
        color: #fff;
        font-size: 28px;
        font-weight: 900;
        line-height: 1;
    }

    .pq-progress-box span {
        margin: 8px 0 14px;
        color: rgba(255,255,255,.75);
        letter-spacing: 0;
    }

    .pq-progress-box div {
        height: 8px;
        border-radius: 999px;
        background: rgba(255,255,255,.25);
        overflow: hidden;
    }

    .pq-progress-box em {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: #fff;
    }

    .pq-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        border-radius: 15px;
        margin-bottom: 18px;
        font-size: 12px;
        font-weight: 800;
    }

    .pq-alert.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .pq-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .pq-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .pq-upload-panel,
    .pq-week-panel {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        padding: 22px;
        box-shadow: 0 14px 35px rgba(15,23,42,.05);
        margin-bottom: 22px;
    }

    .pq-upload-head,
    .pq-panel-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 18px;
    }

    .pq-upload-head span,
    .pq-panel-head span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .16em;
        margin-bottom: 8px;
    }

    .pq-upload-head h2,
    .pq-panel-head h2 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .pq-upload-head p,
    .pq-panel-head p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
    }

    .pq-total-box {
        min-width: 120px;
        padding: 14px 16px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #edf0f4;
        text-align: center;
    }

    .pq-total-box strong {
        display: block;
        color: #111827;
        font-size: 25px;
        font-weight: 900;
        line-height: 1;
    }

    .pq-total-box span {
        margin: 7px 0 0;
        color: #6b7280;
        font-size: 10px;
        letter-spacing: 0;
    }

    .pq-upload-form {
        display: grid;
        grid-template-columns: 180px minmax(0, 1fr) 170px;
        gap: 14px;
        align-items: end;
    }

    .pq-field label {
        display: block;
        color: #374151;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 8px;
    }

    .pq-field div {
        position: relative;
    }

    .pq-field i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 13px;
    }

    .pq-field select,
    .pq-field input {
        width: 100%;
        height: 48px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
        padding: 0 15px 0 42px;
        color: #111827;
        font-size: 12px;
        font-weight: 800;
        outline: none;
        font-family: inherit;
    }

    .pq-field input[type="file"] {
        padding-top: 13px;
    }

    .pq-field select:focus,
    .pq-field input:focus {
        background: #fff;
        border-color: #fecdd3;
        box-shadow: 0 0 0 4px rgba(217, 4, 41, .08);
    }

    .pq-submit {
        height: 48px;
        border: none;
        border-radius: 14px;
        background: #d90429;
        color: #fff;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 9px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        cursor: pointer;
        font-family: inherit;
        box-shadow: 0 14px 28px rgba(217, 4, 41, .20);
    }

    .pq-format-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 16px;
        padding: 13px 14px;
        border-radius: 15px;
        background: #f8fafc;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.5;
    }

    .pq-format-note i {
        color: #d90429;
        margin-top: 2px;
    }

    .pq-week-strip {
        display: grid;
        grid-template-columns: repeat(20, minmax(0, 1fr));
        gap: 6px;
        padding: 14px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #edf0f4;
        margin-bottom: 18px;
    }

    .pq-week-dot {
        min-height: 34px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: #fff;
        border: 1px solid #e5e7eb;
    }

    .pq-week-dot span {
        color: #9ca3af;
        font-size: 10px;
        font-weight: 900;
    }

    .pq-week-dot.filled {
        background: #dcfce7;
        border-color: #bbf7d0;
    }

    .pq-week-dot.filled span {
        color: #16a34a;
    }

    .pq-table-wrap {
        overflow-x: auto;
    }

    .pq-table {
        width: 100%;
        border-collapse: collapse;
    }

    .pq-table th {
        text-align: left;
        padding: 14px 12px;
        border-bottom: 1px solid #edf0f4;
        color: #6b7280;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
        white-space: nowrap;
    }

    .pq-table td {
        padding: 16px 12px;
        border-bottom: 1px solid #edf0f4;
        vertical-align: middle;
    }

    .pq-table tbody tr:hover {
        background: #fff7f9;
    }

    .pq-week-badge,
    .pq-content-status {
        display: inline-flex;
        align-items: center;
        height: 30px;
        padding: 0 11px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .pq-week-badge {
        background: #fff1f2;
        color: #d90429;
    }

    .pq-content-status {
        gap: 7px;
    }

    .pq-content-status.active {
        background: #dcfce7;
        color: #16a34a;
    }

    .pq-question-count strong {
        color: #111827;
        font-size: 17px;
        font-weight: 900;
    }

    .pq-question-count span {
        color: #6b7280;
        font-size: 11px;
        font-weight: 800;
        margin-left: 4px;
    }

    .pq-note-text {
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .pq-delete {
        height: 34px;
        border: none;
        border-radius: 11px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0 12px;
        background: #fee2e2;
        color: #dc2626;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        cursor: pointer;
        font-family: inherit;
    }

    .pq-empty {
        padding: 42px;
        text-align: center;
        background: #f8fafc;
        border-radius: 18px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .pq-empty i {
        width: 58px;
        height: 58px;
        margin: 0 auto 14px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #ffe8ee;
        color: #d90429;
        font-size: 22px;
    }

    .pq-empty strong {
        display: block;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    @media (max-width: 1200px) {
        .pq-upload-form {
            grid-template-columns: 1fr 1fr;
        }

        .pq-submit {
            grid-column: 1 / -1;
        }

        .pq-week-strip {
            grid-template-columns: repeat(10, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .pq-detail-header,
        .pq-upload-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .pq-progress-box,
        .pq-total-box {
            width: 100%;
        }

        .pq-upload-form {
            grid-template-columns: 1fr;
        }

        .pq-week-strip {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/pengajar/Latihan/pilih.blade.php ENDPATH**/ ?>