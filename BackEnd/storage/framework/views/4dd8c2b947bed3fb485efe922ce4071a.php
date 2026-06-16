<?php $__env->startSection('head'); ?>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
<?php $__env->stopSection(); ?>

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
        <div class="pq-header-left">
            <a href="<?php echo e(route('pengajar.latihan.index')); ?>" class="pq-back-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <span class="pq-breadcrumb-capsule">Practice Weekly Manager</span>
            <h1><?php echo e($subject_name); ?></h1>
            <p><?php echo e($class->program_name); ?> • Kelola latihan soal untuk 20 minggu pertemuan.</p>
        </div>

        <div class="pq-progress-box">
            <strong><?php echo e($filledWeeks); ?>/20</strong>
            <span>Minggu Terisi</span>
            <div class="progress-bar-wrap">
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
                <div class="pq-input-wrap">
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
                <div class="pq-input-wrap">
                    <i class="fa-solid fa-file-csv"></i>
                    <input type="file" name="file_csv" accept=".csv,text/csv" required>
                </div>
            </div>

            <button type="submit" class="pq-submit">
                <i class="fa-solid fa-file-import"></i> Proses Import
            </button>
        </form>

        <div class="pq-format-note">
            <i class="fa-solid fa-circle-info"></i>
            <span>Pastikan format CSV sudah sesuai template sistem agar seluruh soal dapat terbaca dengan benar di aplikasi siswa.</span>
        </div>
    </section>

    
    <section class="pq-week-panel">
        <div class="pq-panel-head">
            <div>
                <h2>Ringkasan Soal Terupload</h2>
                <p>Pantau minggu yang sudah memiliki bank soal dan kelola data latihan per pertemuan.</p>
            </div>
        </div>

        <!-- Dots Mingguan Berpendar -->
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
                        <th class="text-right">Aksi</th>
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
                                    <span class="pq-dot-wrapper">
                                        <i class="pq-dot"></i>
                                        <i class="pq-dot-pulse"></i>
                                    </span>
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

                            <td class="text-right">
                                <form action="<?php echo e(route('pengajar.latihan.destroy_week', [
                                    'class_id' => $class->class_id,
                                    'subject_name' => $subject_name,
                                    'week' => $p->week
                                ])); ?>"
                                      method="POST"
                                      onsubmit="return confirm('Hapus semua soal di Minggu ke-<?php echo e($p->week); ?>?')"
                                      style="display: inline-flex;">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>

                                    <button type="submit" class="pq-delete">
                                        <i class="fa-solid fa-trash-can"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5">
                                <div class="pq-empty">
                                    <div class="pq-empty-icon"><i class="fa-solid fa-file-circle-plus"></i></div>
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

    .pq-page {
        width: 100%;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Header Minimalis */
    .pq-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }
    
    .pq-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid var(--border-soft);
        background: var(--spekta-white);
        color: var(--text-muted);
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s;
        margin-bottom: 12px;
    }
    .pq-back-btn:hover {
        background: var(--spekta-gray-light);
        color: var(--text-main);
        border-color: var(--spekta-gray);
    }

    .pq-breadcrumb-capsule {
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
    .pq-detail-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .pq-detail-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }

    /* Progres Box di Kanan */
    .pq-progress-box {
        width: 200px;
        flex-shrink: 0;
        padding: 14px;
        border-radius: 12px;
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
    }
    .pq-progress-box strong { display: block; font-size: 22px; font-weight: 900; color: var(--text-main); line-height: 1; }
    .pq-progress-box span { display: block; margin: 4px 0 10px; color: var(--text-muted); font-size: 10px; font-weight: 800; text-transform: uppercase; }
    .progress-bar-wrap { height: 6px; border-radius: 999px; background: var(--spekta-gray-light); overflow: hidden; }
    .progress-bar-wrap em { display: block; height: 100%; border-radius: 999px; background: var(--spekta-teal); box-shadow: 0 0 8px rgba(46,168,171,0.3); transition: width 0.6s ease; }

    /* Alerts */
    .pq-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 24px;
        font-size: 13px;
        font-weight: 800;
    }
    .pq-alert.success { background: #e6f7ed; color: #15803d; border: 1px solid #bbf7d0; }
    .pq-alert.error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    .pq-alert ul { margin: 4px 0 0; padding-left: 18px; }

    /* Panel Card */
    .pq-upload-panel,
    .pq-week-panel {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
        margin-bottom: 24px;
    }

    .pq-upload-head,
    .pq-panel-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--spekta-gray-light);
    }
    .pq-upload-head h2,
    .pq-panel-head h2 { margin: 0; color: var(--text-main); font-size: 15px; font-weight: 800; }
    .pq-upload-head p,
    .pq-panel-head p { margin: 4px 0 0; color: var(--text-muted); font-size: 11px; font-weight: 600; }

    .pq-total-box {
        min-width: 100px;
        padding: 10px;
        border-radius: 10px;
        background: var(--spekta-gray-light);
        border: 1px solid var(--border-soft);
        text-align: center;
    }
    .pq-total-box strong { display: block; color: var(--text-main); font-size: 20px; font-weight: 900; line-height: 1; }
    .pq-total-box span { margin-top: 2px; color: var(--text-muted); font-size: 9px; font-weight: 800; text-transform: uppercase; }

    /* Form Layout */
    .pq-upload-form {
        display: grid;
        grid-template-columns: 180px minmax(0, 1fr) 160px;
        gap: 15px;
        align-items: end;
    }

    .pq-field label {
        display: block;
        color: var(--text-muted);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 6px;
    }
    .pq-input-wrap { position: relative; display: flex; }
    .pq-input-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--spekta-gray);
        font-size: 12px;
        pointer-events: none;
    }

    .pq-field select,
    .pq-field input {
        width: 100%;
        height: 40px;
        border: 1px solid var(--border-soft);
        border-radius: 10px;
        background: var(--spekta-gray-light);
        padding: 0 14px 0 38px;
        color: var(--text-main);
        font-size: 12px;
        font-weight: 600;
        outline: none;
        font-family: inherit;
        transition: all 0.25s;
    }
    .pq-field input[type="file"] { padding-top: 10px; }
    .pq-field select:focus,
    .pq-field input:focus {
        background: var(--spekta-white);
        border-color: var(--spekta-teal);
        box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12);
    }

    /* Buttons */
    .pq-submit {
        height: 40px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%);
        color: var(--spekta-white);
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        cursor: pointer;
        font-family: inherit;
        box-shadow: 0 4px 10px rgba(229, 57, 53, 0.15);
        transition: all 0.2s ease;
    }
    .pq-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(229, 57, 53, 0.25);
    }

    .pq-format-note {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-top: 14px;
        padding: 12px;
        border-radius: 10px;
        background: #fff7f9;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 600;
        line-height: 1.5;
    }
    .pq-format-note i { color: var(--spekta-red); margin-top: 2px; }

    /* 20-Week Progress Strip */
    .pq-week-strip {
        display: grid;
        grid-template-columns: repeat(20, minmax(0, 1fr));
        gap: 6px;
        padding: 12px;
        border-radius: 12px;
        background: var(--spekta-gray-light);
        border: 1px solid var(--border-soft);
        margin-bottom: 18px;
    }
    .pq-week-dot {
        min-height: 30px;
        display: grid;
        place-items: center;
        border-radius: 8px;
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
    }
    .pq-week-dot span { color: var(--spekta-gray); font-size: 10px; font-weight: 800; }
    .pq-week-dot.filled { background: #e6f7ed; border-color: #bbf7d0; }
    .pq-week-dot.filled span { color: #15803d; }

    /* Table */
    .pq-table-wrap { overflow-x: auto; }
    .pq-table { width: 100%; border-collapse: collapse; min-width: 750px; }
    .pq-table th { text-align: left; padding: 12px 14px; border-bottom: 2px solid var(--spekta-gray-light); color: var(--text-muted); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; white-space: nowrap; }
    .pq-table td { padding: 14px; border-bottom: 1px solid var(--spekta-gray-light); vertical-align: middle; }
    .pq-table tbody tr:hover { background: #fafbfc; }

    .pq-week-badge {
        display: inline-flex;
        align-items: center;
        height: 24px;
        padding: 0 10px;
        border-radius: 6px;
        background: var(--spekta-red-light);
        color: var(--spekta-red-dark);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        white-space: nowrap;
    }

    /* Glowing Badge Status */
    .pq-content-status { display: inline-flex; align-items: center; gap: 5px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
    .pq-dot-wrapper { position: relative; width: 5px; height: 5px; display: inline-block; }
    .pq-dot { width: 5px; height: 5px; border-radius: 99px; background: currentColor; display: block; position: absolute; left: 0; top: 0; }
    .pq-dot-pulse { width: 5px; height: 5px; border-radius: 99px; background: currentColor; display: block; position: absolute; left: 0; top: 0; opacity: 0.4; transform: scale(1); animation: dotGlow 1.8s infinite ease-in-out; }
    @keyframes dotGlow { 0% { transform: scale(1); opacity: 0.8; } 100% { transform: scale(3.2); opacity: 0; } }
    .pq-content-status.active { color: #15803d; }

    .pq-question-count strong { color: var(--text-main); font-size: 15px; font-weight: 900; }
    .pq-question-count span { color: var(--text-muted); font-size: 11px; font-weight: 800; margin-left: 4px; }
    .pq-note-text { color: var(--text-muted); font-size: 12px; font-weight: 600; }

    .pq-delete {
        height: 30px;
        border: none;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0 10px;
        background: var(--spekta-red-light);
        color: var(--spekta-red);
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.2s;
    }
    .pq-delete:hover { background: #fecaca; color: #991b1b; transform: scale(1.05); }

    .pq-empty { padding: 40px; text-align: center; color: var(--text-muted); font-size: 11px; font-weight: 700; display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .pq-empty-icon { width: 48px; height: 48px; margin: 0 auto 8px; display: grid; place-items: center; border-radius: 50%; background: var(--spekta-gray-light); color: var(--spekta-gray); font-size: 18px; }
    .pq-empty strong { display: block; color: var(--text-main); font-size: 14px; font-weight: 800; margin-bottom: 4px; }
    .text-right { text-align: right; }

    @media (max-width: 1200px) {
        .pq-upload-form { grid-template-columns: 1fr 1fr; }
        .pq-submit { grid-column: 1 / -1; }
        .pq-week-strip { grid-template-columns: repeat(10, minmax(0, 1fr)); }
    }

    @media (max-width: 760px) {
        .pq-detail-header, .pq-upload-head { flex-direction: column; align-items: flex-start; gap: 14px; }
        .pq-progress-box, .pq-total-box { width: 100%; }
        .pq-upload-form { grid-template-columns: 1fr; }
        .pq-week-strip { grid-template-columns: repeat(5, minmax(0, 1fr)); }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/pengajar/Latihan/pilih.blade.php ENDPATH**/ ?>