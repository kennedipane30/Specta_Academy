<?php $__env->startSection('title', 'Manajemen Absensi'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $totalAssignment = count($assignmentsWithSubjects ?? []);

    // Gunakan 'use' agar variabel dari luar bisa dibaca di dalam filter
    $activeToday = collect($assignmentsWithSubjects ?? [])->filter(function($as) use ($jadwalHariIni) {
        return in_array($as->class_id, $jadwalHariIni);
    })->count();
?>

<div class="abs-page">

    
    <section class="abs-header">
        <div class="abs-header-left">
            <span class="abs-breadcrumb-capsule">Attendance Management</span>
            <h1>Manajemen Absensi</h1>
            <p>Kelola kehadiran siswa berdasarkan program kelas, bidang ajar, dan pertemuan mingguan secara berkala.</p>
        </div>
    </section>

    
    <?php if(session('success')): ?>
        <div class="abs-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    
    <section class="abs-stats">
        <!-- Total Penugasan Kelas -->
        <div class="abs-stat-card card-teal">
            <div class="abs-stat-icon teal"><i class="fa-solid fa-briefcase"></i></div>
            <div class="abs-stat-info">
                <p>Penugasan Kelas</p>
                <h2><?php echo e($totalAssignment); ?> <span>Kelas</span></h2>
            </div>
        </div>

        <!-- Jadwal Hari Ini -->
        <div class="abs-stat-card card-red">
            <div class="abs-stat-icon red"><i class="fa-solid fa-calendar-day"></i></div>
            <div class="abs-stat-info">
                <p>Jadwal Hari Ini</p>
                <h2><?php echo e($activeToday); ?> <span>Kelas</span></h2>
            </div>
            <?php if($activeToday > 0): ?>
                <span class="abs-pulse-dot"></span>
            <?php endif; ?>
        </div>
    </section>

    
    <section class="abs-panel">
        <div class="abs-panel-head">
            <div>
                <h2>Daftar Kelas Absensi</h2>
                <p>Pilih kelas dan bidang ajar di bawah ini untuk membuka daftar pertemuan mingguan.</p>
            </div>
        </div>

        <?php if(empty($assignmentsWithSubjects) || count($assignmentsWithSubjects) == 0): ?>
            <div class="abs-empty">
                <div class="abs-empty-icon"><i class="fa-solid fa-clipboard-list"></i></div>
                <strong>Belum ada penugasan materi.</strong>
                <span>Admin akademik perlu menugaskan Anda pada program dan mata pelajaran tertentu terlebih dahulu.</span>
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
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $__currentLoopData = $assignmentsWithSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $as): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $canAbsenToday = in_array($as->class_id, $jadwalHariIni);
                            ?>

                            <tr>
                                
                                <td>
                                    <div class="abs-class-name">
                                        <strong><?php echo e($as->classModel->program_name ?? 'Program Kelas'); ?></strong>
                                        <span>ID Kelas: #<?php echo e($as->class_id); ?></span>
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
                                            <span class="abs-dot-wrapper">
                                                <i class="abs-dot"></i>
                                                <i class="abs-dot-pulse"></i>
                                            </span>
                                            Aktif Hari Ini
                                        </span>
                                    <?php else: ?>
                                        <span class="abs-status neutral">
                                            <span class="abs-dot-wrapper"><i class="abs-dot"></i></span>
                                            Tidak Ada Jadwal
                                        </span>
                                    <?php endif; ?>
                                </td>

                                
                                <td>
                                    <p class="abs-note">
                                        <?php if($canAbsenToday): ?>
                                            Anda memiliki jadwal mengajar hari ini. Absensi dapat dilakukan melalui daftar minggu.
                                        <?php else: ?>
                                            Anda tetap dapat membuka rekap mingguan meskipun tidak ada jadwal mengajar hari ini.
                                        <?php endif; ?>
                                    </p>
                                </td>

                                
                                <td class="text-right">
                                    <a href="<?php echo e(route('pengajar.absensi.weeks', [$as->class_id, $as->subject_name])); ?>" class="abs-action">
                                        <span>Buka Minggu</span> <i class="fa-solid fa-arrow-right-long"></i>
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

    .abs-page {
        width: 100%;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── Header Minimalis ── */
    .abs-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }
    .abs-breadcrumb-capsule {
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
    .abs-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .abs-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }

    /* ── Stats Summary Grid ── */
    .abs-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .abs-stat-card {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: all 0.2s ease;
        position: relative;
    }
    .abs-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.03);
    }
    .abs-stat-card.card-teal:hover { border-color: var(--spekta-teal); }
    .abs-stat-card.card-red:hover { border-color: var(--spekta-red); }

    .abs-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 16px;
    }
    .abs-stat-icon.red { background: var(--spekta-red-light); color: var(--spekta-red); }
    .abs-stat-icon.teal { background: var(--spekta-teal-light); color: var(--spekta-teal); }

    .abs-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .abs-stat-info h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1;
        display: flex;
        align-items: baseline;
        gap: 4px;
    }
    .abs-stat-info h2 span {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }

    /* Indikator Denyut */
    .abs-pulse-dot {
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

    /* Alerts */
    .abs-alert {
        display: flex;
        gap: 10px;
        align-items: center;
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 24px;
        font-weight: 800;
        font-size: 13px;
    }
    .abs-alert.success { background: #e6f7ed; color: #15803d; border: 1px solid #bbf7d0; }

    /* Main Table Panel */
    .abs-panel { background: var(--spekta-white); border: 1px solid var(--border-soft); border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); }
    .abs-panel-head { margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--spekta-gray-light); }
    .abs-panel-head h2 { margin: 0; color: var(--text-main); font-size: 15px; font-weight: 800; }
    .abs-panel-head p { margin: 4px 0 0; color: var(--text-muted); font-size: 11px; font-weight: 600; }
    
    .abs-table-wrap { overflow-x: auto; }
    .abs-table { width: 100%; border-collapse: collapse; min-width: 800px; }
    .abs-table th { text-align: left; padding: 12px 14px; font-size: 10px; color: var(--text-muted); text-transform: uppercase; border-bottom: 2px solid var(--spekta-gray-light); font-weight: 800; letter-spacing: 0.05em; }
    .abs-table td { padding: 14px; border-bottom: 1px solid var(--spekta-gray-light); vertical-align: middle; }
    .abs-table tbody tr:hover { background: #fafbfc; }
    
    .abs-class-name strong { display: block; color: var(--text-main); font-size: 13px; font-weight: 800; text-transform: uppercase; }
    .abs-class-name span { display: block; margin-top: 4px; color: var(--text-muted); font-size: 10px; font-weight: 700; }
    
    .abs-subject-badge { display: inline-flex; align-items: center; height: 24px; padding: 0 10px; border-radius: 6px; background: var(--spekta-red-light); color: var(--spekta-red-dark); font-size: 10px; font-weight: 800; text-transform: uppercase; white-space: nowrap; }
    
    /* Glowing Indicator */
    .abs-status { display: inline-flex; align-items: center; gap: 6px; height: 22px; padding: 0 10px; border-radius: 6px; font-size: 10px; font-weight: 800; text-transform: uppercase; white-space: nowrap; }
    .abs-dot-wrapper { position: relative; width: 5px; height: 5px; display: inline-block; }
    .abs-dot { width: 5px; height: 5px; border-radius: 99px; background: currentColor; display: block; position: absolute; left: 0; top: 0; }
    .abs-dot-pulse { width: 5px; height: 5px; border-radius: 99px; background: currentColor; display: block; position: absolute; left: 0; top: 0; opacity: 0.4; transform: scale(1); animation: dotGlow 1.8s infinite ease-in-out; }
    @keyframes dotGlow { 0% { transform: scale(1); opacity: 0.8; } 100% { transform: scale(3.2); opacity: 0; } }

    .abs-status.active { background: #e6f7ed; color: #15803d; box-shadow: 0 2px 6px rgba(22, 163, 74, 0.1); }
    .abs-status.neutral { background: var(--spekta-gray-light); color: var(--text-muted); }
    
    .abs-note { margin: 0; color: var(--text-muted); font-size: 12px; font-weight: 600; line-height: 1.5; max-width: 450px; }
    
    .abs-action { height: 32px; display: inline-flex; align-items: center; gap: 6px; padding: 0 12px; border-radius: 8px; background: #1f2937; color: var(--spekta-white) !important; font-size: 11px; font-weight: 800; white-space: nowrap; text-decoration: none; transition: all 0.2s; }
    .abs-action:hover { background: var(--spekta-red); box-shadow: 0 4px 10px rgba(229,57,53,0.25); }
    
    .abs-empty { padding: 40px; text-align: center; color: var(--text-muted); font-size: 12px; font-weight: 700; display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .abs-empty-icon { width: 48px; height: 48px; margin: 0 auto 8px; display: grid; place-items: center; border-radius: 50%; background: var(--spekta-gray-light); color: var(--spekta-gray); font-size: 18px; }
    .abs-empty strong { display: block; color: var(--text-main); font-size: 14px; font-weight: 800; margin-bottom: 4px; }
    
    .text-right { text-align: right; }

    @media (max-width: 900px) {
        .abs-header { flex-direction: column; align-items: flex-start; }
        .abs-stats { grid-template-columns: 1fr; }
        .abs-table-wrap th:nth-child(4), .abs-table-wrap td:nth-child(4) { display: none; } /* Sembunyikan catatan info di layar kecil */
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/pengajar/absensi/index.blade.php ENDPATH**/ ?>