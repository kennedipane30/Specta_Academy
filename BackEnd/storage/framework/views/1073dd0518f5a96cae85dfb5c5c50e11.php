<?php $__env->startSection('title', 'Manajemen Absensi'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $totalAssignment = $assignments->count();

    // Gunakan 'use' agar variabel dari luar bisa dibaca di dalam filter
    $activeToday = $assignments->filter(function($as) use ($jadwalHariIni) {
        return in_array($as->class_id, $jadwalHariIni);
    })->count();
?>

<div class="abs-page">

    <section class="abs-hero">
        <div>
            <span>Attendance Management</span>
            <h1>Manajemen Absensi</h1>
            <p>Kelola kehadiran siswa berdasarkan program kelas, bidang ajar, dan pertemuan mingguan.</p>
        </div>

        <div class="abs-hero-summary">
            <div>
                <strong><?php echo e($totalAssignment); ?></strong>
                <span>Penugasan</span>
            </div>

            <div>
                <strong><?php echo e($activeToday); ?></strong>
                <span>Jadwal Hari Ini</span>
            </div>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="abs-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <section class="abs-panel">
        <div class="abs-panel-head">
            <div>
                <span>Teaching Assignment</span>
                <h2>Daftar Kelas Absensi</h2>
                <p>Pilih kelas dan bidang ajar untuk membuka daftar pertemuan mingguan.</p>
            </div>
        </div>

        <?php if($assignments->isEmpty()): ?>
            <div class="abs-empty">
                <i class="fa-solid fa-clipboard-list"></i>
                <strong>Belum ada penugasan materi.</strong>
                <span>Admin perlu menugaskan Anda pada program dan mata pelajaran tertentu.</span>
            </div>
        <?php else: ?>
            <div class="abs-table-wrap">
                <table class="abs-table">
                    <thead>
                        <tr>
                            <th>Program Kelas</th>
                            <th>Bidang Ajar</th>
                            <th>Status Hari Ini</th>
                            <th>Informasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $as): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $canAbsenToday = in_array($as->class_id, $jadwalHariIni);
                            ?>

                            <tr>
                                <td>
                                    <div class="abs-class-name">
                                        <strong><?php echo e($as->classModel->program_name ?? 'Program Kelas'); ?></strong>
                                        <span>ID Kelas: <?php echo e($as->class_id); ?></span>
                                    </div>
                                </td>

                                <td>
                                    <span class="abs-subject-badge">
                                        <?php echo e($as->subject_name); ?>

                                    </span>
                                </td>

                                <td>
                                    <?php if($canAbsenToday): ?>
                                        <span class="abs-status active">
                                            <i class="fa-solid fa-circle"></i>
                                            Aktif Hari Ini
                                        </span>
                                    <?php else: ?>
                                        <span class="abs-status neutral">
                                            <i class="fa-solid fa-circle"></i>
                                            Tidak Ada Jadwal
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <p class="abs-note">
                                        <?php if($canAbsenToday): ?>
                                            Anda memiliki jadwal mengajar hari ini. Absensi dapat dilakukan melalui daftar minggu.
                                        <?php else: ?>
                                            Anda tetap dapat membuka rekap mingguan meskipun tidak ada jadwal hari ini.
                                        <?php endif; ?>
                                    </p>
                                </td>

                                <td>
                                                    <a href="<?php echo e(route('pengajar.absensi.weeks', [$as->class_id, $as->subject->material_name])); ?>" class="abs-action">
                        Buka Minggu
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                                                    </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

</div>

<style>
    .abs-page {
        width: 100%;
    }

    .abs-hero {
        background: linear-gradient(120deg, #cf002b 0%, #85001d 52%, #182033 100%);
        border-radius: 24px;
        padding: 30px 34px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 22px;
        box-shadow: 0 18px 38px rgba(134, 0, 24, .20);
        overflow: hidden;
        position: relative;
    }

    .abs-hero::after {
        content: "";
        width: 260px;
        height: 260px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .09);
        position: absolute;
        right: -90px;
        top: -120px;
    }

    .abs-hero > div {
        position: relative;
        z-index: 2;
    }

    .abs-hero span,
    .abs-panel-head span {
        display: block;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .abs-hero > div:first-child > span {
        color: rgba(255,255,255,.78);
        margin-bottom: 10px;
    }

    .abs-hero h1 {
        margin: 0 0 8px;
        color: #fff;
        font-size: 31px;
        font-weight: 900;
        letter-spacing: -0.04em;
        text-transform: uppercase;
    }

    .abs-hero p {
        margin: 0;
        color: rgba(255,255,255,.86);
        font-size: 13px;
        font-weight: 600;
        line-height: 1.6;
        max-width: 760px;
    }

    .abs-hero-summary {
        display: flex;
        gap: 12px;
        flex-shrink: 0;
    }

    .abs-hero-summary div {
        min-width: 125px;
        padding: 16px;
        border-radius: 18px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.16);
        backdrop-filter: blur(12px);
        text-align: center;
    }

    .abs-hero-summary strong {
        display: block;
        font-size: 28px;
        font-weight: 900;
        line-height: 1;
    }

    .abs-hero-summary span {
        margin-top: 7px;
        color: rgba(255,255,255,.75);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0;
    }

    .abs-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        border-radius: 15px;
        margin-bottom: 18px;
        font-size: 12px;
        font-weight: 800;
    }

    .abs-alert.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .abs-panel {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        padding: 22px;
        box-shadow: 0 14px 35px rgba(15,23,42,.05);
    }

    .abs-panel-head {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
    }

    .abs-panel-head span {
        color: #d90429;
        margin-bottom: 8px;
    }

    .abs-panel-head h2 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .abs-panel-head p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
    }

    .abs-table-wrap {
        overflow-x: auto;
    }

    .abs-table {
        width: 100%;
        border-collapse: collapse;
    }

    .abs-table th {
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

    .abs-table td {
        padding: 16px 12px;
        border-bottom: 1px solid #edf0f4;
        vertical-align: middle;
    }

    .abs-table tbody tr:hover {
        background: #fff7f9;
    }

    .abs-class-name strong {
        display: block;
        color: #111827;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .abs-class-name span {
        display: block;
        margin-top: 4px;
        color: #9ca3af;
        font-size: 10px;
        font-weight: 700;
    }

    .abs-subject-badge {
        display: inline-flex;
        align-items: center;
        height: 30px;
        padding: 0 11px;
        border-radius: 999px;
        background: #fff1f2;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .abs-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 30px;
        padding: 0 11px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .abs-status i {
        font-size: 7px;
    }

    .abs-status.active {
        background: #dcfce7;
        color: #16a34a;
    }

    .abs-status.neutral {
        background: #f3f4f6;
        color: #6b7280;
    }

    .abs-note {
        margin: 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
        max-width: 480px;
    }

    .abs-action {
        height: 38px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0 14px;
        border-radius: 12px;
        background: #d90429;
        color: #fff;
        font-size: 11px;
        font-weight: 900;
        white-space: nowrap;
    }

    .abs-empty {
        padding: 42px;
        text-align: center;
        background: #f8fafc;
        border-radius: 18px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .abs-empty i {
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

    .abs-empty strong {
        display: block;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    @media (max-width: 900px) {
        .abs-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .abs-hero-summary {
            width: 100%;
        }

        .abs-hero-summary div {
            flex: 1;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\Specta_Academy\BackEnd\resources\views/pengajar/absensi/index.blade.php ENDPATH**/ ?>