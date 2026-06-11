<?php $__env->startSection('title', 'Materi Saya - Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<div class="tm-container">

    
    <section class="tm-hero-header">
        <div class="tm-hero-content">
            <span class="tm-pre-title">TEACHER PORTAL</span>
            <h1 class="tm-main-title">Materi Pembelajaran</h1>
            <p class="tm-sub-title">
                Pilih bidang ajar Anda untuk mengelola modul materi dan file PDF mingguan.
            </p>
        </div>

        <div class="tm-stat-box">
            <div class="tm-stat-value">
                <?php echo e(count($assignmentsWithSubjects ?? [])); ?>

            </div>
            <div class="tm-stat-label">Total Penugasan</div>
        </div>
    </section>

    
    <section class="tm-card">
        <div class="tm-card-head">
            <div>
                <h2>
                    <i class="fa-solid fa-chalkboard-user"></i>
                    Daftar Bidang Ajar
                </h2>
                <small>Semua kombinasi kelas dan mata pelajaran yang Anda ampu</small>
            </div>
        </div>

        <div class="tm-table-responsive">
            <table class="tm-table">
                <thead>
                    <tr>
                        <th style="width: 30%">Program Kelas</th>
                        <th style="width: 30%">Mata Pelajaran</th>
                        <th style="width: 20%">Durasi</th>
                        <th class="text-end" style="width: 20%">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $assignmentsWithSubjects ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="tm-class-info">
                                    <strong><?php echo e($assign->classModel->program_name ?? 'Kelas'); ?></strong>
                                    <small>ID #<?php echo e($assign->class_id); ?></small>
                                </div>
                            </td
                            <td>
                                <span class="tm-subject-pill">
                                    <i class="fa-solid fa-book-bookmark"></i>
                                    <?php echo e($assign->subject_name ?? 'Mata Pelajaran'); ?>

                                </span>
                            </td
                            <td>
                                <span class="tm-muted">20 Minggu</span>
                            </td
                            <td class="text-end">
                                <a href="<?php echo e(route('pengajar.materi.pilih', ['class_id' => $assign->class_id, 'subject_name' => $assign->subject_name])); ?>"
                                   class="tm-btn-manage">
                                    Kelola Materi
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </td
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4">
                                <div class="tm-empty">
                                    <i class="fa-solid fa-folder-open" style="font-size: 40px; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                                    Belum ada penugasan mengajar yang terdaftar.
                                </div>
                            </td
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
:root {
    --red: #d90429;
    --dark: #0f172a;
    --gray: #64748b;
    --light: #f8fafc;
}

.tm-container {
    padding: 24px;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

/* HERO */
.tm-hero-header {
    background: linear-gradient(135deg, #b40018, #52040d);
    padding: 40px;
    border-radius: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
    color: white;
    gap: 20px;
    flex-wrap: wrap;
}

.tm-pre-title { font-size: 12px; font-weight: 800; letter-spacing: 2px; opacity: .8; text-transform: uppercase; }
.tm-main-title { font-size: 34px; font-weight: 900; margin: 10px 0; }
.tm-sub-title { opacity: .9; max-width: 500px; }

.tm-stat-box {
    background: rgba(255, 255, 255, .15);
    padding: 20px;
    border-radius: 18px;
    min-width: 140px;
    text-align: center;
    backdrop-filter: blur(10px);
}

.tm-stat-value { font-size: 32px; font-weight: 900; }
.tm-stat-label { font-size: 12px; font-weight: 600; }

/* CARD & TABLE */
.tm-card {
    background: white;
    padding: 28px;
    border-radius: 24px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .05);
    border: 1px solid #f1f5f9;
}

.tm-card-head { margin-bottom: 25px; }
.tm-card-head h2 { margin: 0; font-size: 22px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
.tm-card-head small { color: var(--gray); font-weight: 600; margin-left: 35px; }

.tm-table { width: 100%; border-collapse: collapse; }
.tm-table th {
    padding: 18px;
    font-size: 11px;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    border-bottom: 2px solid #f1f5f9;
    text-align: left;
}

/* Class khusus untuk meratakan ke kanan */
.tm-table th.text-end, .tm-table td.text-end {
    text-align: right;
}

.tm-table td { padding: 22px 18px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.tm-table tr:hover { background: #fff7f8; }

.tm-class-info strong { display: block; font-size: 15px; font-weight: 800; color: var(--dark); }
.tm-class-info small { color: var(--gray); font-weight: 700; }

.tm-subject-pill {
    background: #fff1f2;
    padding: 8px 16px;
    border-radius: 12px;
    color: var(--red);
    font-size: 13px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

/* BUTTON */
.tm-btn-manage {
    background: var(--dark);
    padding: 12px 20px;
    border-radius: 14px;
    text-decoration: none;
    color: white;
    font-size: 13px;
    font-weight: 700;
    display: inline-flex;
    gap: 8px;
    align-items: center;
    transition: .3s ease;
}

.tm-btn-manage:hover {
    background: var(--red);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(217, 4, 41, .25);
}

.tm-muted { color: var(--gray); font-weight: 700; font-size: 13px; }
.tm-empty { padding: 50px; text-align: center; color: #94a3b8; font-weight: 600; }

@media(max-width:768px) {
    .tm-table-responsive { overflow-x: auto; }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/pengajar/materi/index.blade.php ENDPATH**/ ?>