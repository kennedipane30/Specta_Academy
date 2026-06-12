<?php $__env->startSection('title', 'Bank Soal Tryout - Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<?php
    // Menghitung total seluruh soal yang sudah dibuat oleh guru ini di semua kelas/mapel
    $totalSoalSelesai = \DB::table('tryout_drafts')
        ->where('user_id', Auth::user()->usersID)
        ->count();

    $assignmentCollection = collect($assignmentsWithSubjects ?? []);
    $totalAssignment = $assignmentCollection->count();
?>

<div class="cp-page">

    
    <section class="cp-header">
        <div class="cp-header-left">
            <span class="cp-breadcrumb-capsule">Teacher Tryout Portal</span>
            <h1>Tryout Question Center</h1>
            <p>Kontribusikan draf soal terbaik Anda. Admin akan mengkurasi draf tersebut menjadi satu paket Tryout resmi.</p>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="tm-alert-modern success">
            <i class="fa-solid fa-circle-check"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="tm-alert-modern error">
            <i class="fa-solid fa-circle-xmark"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    
    <section class="cp-stats">
        <!-- Penugasan Kelas -->
        <div class="cp-stat-card card-teal">
            <div class="cp-stat-icon teal"><i class="fa-solid fa-briefcase"></i></div>
            <div class="cp-stat-info">
                <p>Penugasan Kelas</p>
                <h2><?php echo e($totalAssignment); ?> <span>Kelas</span></h2>
            </div>
        </div>

        <!-- Total Soal Disetor -->
        <div class="cp-stat-card card-red">
            <div class="cp-stat-icon red"><i class="fa-solid fa-file-circle-check"></i></div>
            <div class="cp-stat-info">
                <p>Soal Disetor</p>
                <h2><?php echo e($totalSoalSelesai); ?> <span>Soal</span></h2>
            </div>
            <?php if($totalSoalSelesai > 0): ?>
                <span class="cp-pulse-dot"></span>
            <?php endif; ?>
        </div>

        <!-- Target Target Selesai -->
        <div class="cp-stat-card card-gray">
            <div class="cp-stat-icon gray"><i class="fa-solid fa-flag-checkered"></i></div>
            <div class="cp-stat-info">
                <p>Target Publikasi</p>
                <h2><?php echo e($totalAssignment); ?> <span>Paket TO</span></h2>
            </div>
        </div>
    </section>

    
    <section class="cp-main-card">
        <div class="card-header-flex">
            <div>
                <h2>Daftar Penugasan Soal</h2>
                <p>Klik tombol input untuk mengelola soal di setiap mata pelajaran.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="cp-table-modern">
                <thead>
                    <tr>
                        <th width="30%">PROGRAM KELAS</th>
                        <th width="25%" class="text-center">MATA PELAJARAN</th>
                        <th width="25%" class="text-center">PROGRES SETORAN</th>
                        <th width="20%" class="text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $assignmentsWithSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            // Ambil nama mapel
                            $subjectName = $assign->subject_name;

                            // HITUNG SOAL
                            $count = \DB::table('tryout_drafts')
                                ->where('user_id', Auth::user()->usersID)
                                ->where('class_id', $assign->class_id)
                                ->where('subject_name', trim($subjectName))
                                ->count();
                        ?>
                        <tr>
                            <td>
                                <div class="program-info">
                                    <div class="program-icon-box">
                                        <i class="fa-solid fa-school-flag"></i>
                                    </div>
                                    <div>
                                        <strong><?php echo e($assign->classModel->program_name ?? 'Program'); ?></strong>
                                        <small>ID Kelas: #<?php echo e($assign->class_id); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="subject-tag">
                                    <i class="fa-solid fa-book-bookmark mr-1"></i>
                                    <?php echo e($subjectName); ?>

                                </span>
                            </td>
                            <td class="text-center">
                                <div class="progress-container-flex">
                                    <div class="contribution-info <?php echo e($count > 0 ? 'active' : ''); ?>">
                                        <div class="info-content">
                                            <strong><?php echo e($count); ?> Soal</strong>
                                            <span><?php echo e($count > 0 ? 'TERUPLOAD' : 'BELUM ADA'); ?></span>
                                        </div>
                                        <?php if($count > 0): ?>
                                            <i class="fa-solid fa-circle-check check-icon" style="color: #10b981;"></i>
                                        <?php else: ?>
                                            <i class="fa-solid fa-circle-minus" style="color: #cbd5e1;"></i>
                                        <?php endif; ?>
                                    </div>

                                    <?php if($count > 0): ?>
                                        <form action="<?php echo e(route('pengajar.tryout.deleteAll')); ?>" method="POST" onsubmit="return confirm('Tarik kembali semua soal <?php echo e($subjectName); ?>?')">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="class_id" value="<?php echo e($assign->class_id); ?>">
                                            <input type="hidden" name="subject_name" value="<?php echo e($subjectName); ?>">
                                            <button type="submit" class="btn-action-delete" title="Tarik/Hapus Semua Draf Mapel Ini">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-right">
                                <a href="<?php echo e(route('pengajar.tryout.create', [$assign->class_id, $subjectName])); ?>"
                                   class="btn-input-modern <?php echo e($count > 0 ? 'btn-has-content' : ''); ?>">
                                    <span><?php echo e($count > 0 ? 'EDIT / TAMBAH' : 'INPUT SOAL'); ?></span>
                                    <div class="icon-circle">
                                        <i class="fa-solid fa-<?php echo e($count > 0 ? 'pen-to-square' : 'pen-nib'); ?>"></i>
                                    </div>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4">
                                <div class="cp-empty-state">
                                    <i class="fa-solid fa-file-circle-xmark"></i>
                                    <span>Belum ada penugasan soal untuk Anda saat ini.</span>
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

    /* Alerts */
    .tm-alert-modern { padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 13px; }
    .tm-alert-modern.success { background: #e6f7ed; color: #15803d; border-left: 5px solid #22c55e; }
    .tm-alert-modern.error { background: #fee2e2; color: #b91c1c; border-left: 5px solid #ef4444; }

    /* Stats Grid */
    .cp-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .cp-stat-card {
        background: var(--spekta-white);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: all 0.25s ease;
        position: relative;
    }
    .cp-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.03);
    }
    .cp-stat-card.card-teal:hover { border-color: var(--spekta-teal); }
    .cp-stat-card.card-red:hover { border-color: var(--spekta-red); }
    .cp-stat-card.card-gray:hover { border-color: var(--spekta-gray); }

    .cp-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 16px;
    }
    .cp-stat-icon.red { background: var(--spekta-red-light); color: var(--spekta-red); }
    .cp-stat-icon.teal { background: var(--spekta-teal-light); color: var(--spekta-teal); }
    .cp-stat-icon.gray { background: var(--spekta-gray-light); color: var(--text-muted); }

    .cp-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .cp-stat-info h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1;
        display: flex;
        align-items: baseline;
        gap: 4px;
    }
    .cp-stat-info h2 span {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }

    .cp-pulse-dot {
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

    /* Table Panel */
    .cp-main-card { background: var(--spekta-white); border-radius: 16px; padding: 20px; border: 1px solid var(--border-soft); box-shadow: 0 4px 15px rgba(0,0,0,0.01); }
    
    .card-header-flex h2 { font-size: 15px; font-weight: 800; color: var(--text-main); margin: 0 0 4px 0; }
    .card-header-flex p { font-size: 11px; color: var(--text-muted); margin: 0; font-weight: 600; }

    .cp-table-modern { width: 100%; border-collapse: collapse; }
    .cp-table-modern th { font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; padding: 12px 14px; border-bottom: 2px solid var(--spekta-gray-light); letter-spacing: 0.05em; }
    .cp-table-modern td { padding: 14px; border-bottom: 1px solid var(--spekta-gray-light); vertical-align: middle; }
    .cp-table-modern tbody tr:last-child td { border-bottom: none; }
    .cp-table-modern tbody tr:hover { background: #fafbfc; }

    .program-info { display: flex; align-items: center; gap: 10px; }
    .program-icon-box { width: 34px; height: 34px; background: var(--spekta-gray-light); border-radius: 8px; display: grid; place-items: center; font-size: 14px; color: var(--text-muted); border: 1px solid var(--border-soft); }
    .program-info strong { display: block; font-size: 13px; font-weight: 800; color: var(--text-main); }
    .program-info small { font-size: 10px; color: var(--text-muted); font-weight: 600; margin-top: 2px; }

    .subject-tag { background: var(--spekta-red-light); color: var(--spekta-red-dark); padding: 4px 10px; border-radius: 6px; font-weight: 800; font-size: 11px; text-transform: uppercase; border: 1px solid rgba(229, 57, 53, 0.1); display: inline-flex; align-items: center; }

    .progress-container-flex { display: inline-flex; align-items: center; gap: 10px; }
    .contribution-info {
        display: flex; align-items: center; gap: 12px;
        padding: 8px 12px; background: var(--spekta-gray-light);
        border-radius: 10px; border: 1px solid var(--border-soft);
        min-width: 150px; text-align: left;
    }
    .contribution-info.active { background: #e6f7ed; border-color: #bbf7d0; }
    .info-content strong { display: block; font-size: 12px; color: var(--text-main); line-height: 1.2; font-weight: 800; }
    .info-content span { font-size: 8px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .check-icon { font-size: 12px; }

    .btn-action-delete {
        background: var(--spekta-red-light); color: var(--spekta-red); border: none;
        width: 32px; height: 32px; border-radius: 8px;
        cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; justify-content: center; font-size: 12px;
    }
    .btn-action-delete:hover { background: #fecaca; color: #991b1b; transform: scale(1.05); }

    .btn-input-modern {
        display: inline-flex; align-items: center; gap: 8px;
        background: #1f2937; color: white !important; padding: 4px 4px 4px 12px;
        border-radius: 8px; text-decoration: none; transition: 0.2s;
        white-space: nowrap; font-weight: 800; font-size: 11px;
    }
    .btn-input-modern.btn-has-content { background: #15803d; }
    .btn-input-modern:hover { transform: translateX(-3px); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .btn-input-modern.btn-has-content:hover { background: #166534; }

    .icon-circle { width: 24px; height: 24px; background: rgba(255,255,255,0.15); border-radius: 6px; display: grid; place-items: center; font-size: 10px; }

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

    .text-center { text-align: center; }
    .text-right { text-align: right; }

    @media (max-width: 1100px) {
        .cp-table-modern th:nth-child(3), .cp-table-modern td:nth-child(3) { display: none; } /* Sembunyikan progress bar di layar kecil agar tidak sesak */
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\perkuliahan\PA 2 - code\PAAAAA2\BackEnd\resources\views/pengajar/tryout/index.blade.php ENDPATH**/ ?>