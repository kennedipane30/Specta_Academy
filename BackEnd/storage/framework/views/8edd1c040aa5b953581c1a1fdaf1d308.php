<?php $__env->startSection('title', 'Rekapitulasi Mingguan'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $doneCount = count($doneWeeks);
    $progress = round(($doneCount / 20) * 100);
?>

<div class="abs-page">

    
    <section class="abs-header">
        <div class="abs-header-left">
            <a href="<?php echo e(route('pengajar.absensi.index')); ?>" class="abs-back-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <span class="abs-breadcrumb-capsule">Weekly Attendance</span>
            <h1><?php echo e($class->program_name); ?></h1>
            <p>Rekapitulasi mingguan untuk bidang ajar <strong style="color: var(--spekta-teal);"><?php echo e($subject); ?></strong>.</p>
        </div>

        <!-- Progres Bar Pertemuan yang Kece -->
        <div class="abs-progress-box">
            <strong><?php echo e($doneCount); ?>/20</strong>
            <span>Minggu Selesai</span>
            <div class="progress-bar-wrap">
                <em style="width: <?php echo e($progress); ?>%"></em>
            </div>
        </div>
    </section>

    
    <section class="abs-panel">
        <div class="abs-week-guide">
            <div>
                <i class="fa-solid fa-circle-check" style="color: #16a34a;"></i>
                <span>Hijau berarti absensi sudah diisi (rekap dapat dilihat).</span>
            </div>

            <div>
                <i class="fa-solid fa-pen-to-square" style="color: var(--spekta-gray);"></i>
                <span>Putih berarti absensi kosong (bisa mulai input).</span>
            </div>
        </div>

        <div class="abs-week-grid">
            <?php for($i = 1; $i <= 20; $i++): ?>
                <?php $isDone = in_array($i, $doneWeeks); ?>

                <a href="<?php echo e($isDone ? route('pengajar.absensi.recap', [$class->class_id, $subject, $i]) : route('pengajar.absensi.create', [$class->class_id, $subject, $i])); ?>"
                   class="abs-week-cell <?php echo e($isDone ? 'done' : 'open'); ?>">
                    <small>Week</small>
                    <strong><?php echo e($i); ?></strong>
                    <span>
                        <?php echo e($isDone ? 'Lihat Rekap' : 'Mulai Absen'); ?>

                    </span>
                </a>
            <?php endfor; ?>
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

    /* Header Minimalis */
    .abs-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }
    
    .abs-back-btn {
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
    .abs-back-btn:hover {
        background: var(--spekta-gray-light);
        color: var(--text-main);
        border-color: var(--spekta-gray);
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

    /* Progres Box di Kanan */
    .abs-progress-box {
        width: 200px;
        flex-shrink: 0;
        padding: 14px;
        border-radius: 12px;
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
    }
    .abs-progress-box strong { display: block; font-size: 22px; font-weight: 900; color: var(--text-main); line-height: 1; }
    .abs-progress-box span { display: block; margin: 4px 0 10px; color: var(--text-muted); font-size: 10px; font-weight: 800; text-transform: uppercase; }
    .progress-bar-wrap { height: 6px; border-radius: 999px; background: var(--spekta-gray-light); overflow: hidden; }
    .progress-bar-wrap em { display: block; height: 100%; border-radius: 999px; background: var(--spekta-teal); box-shadow: 0 0 8px rgba(46,168,171,0.3); transition: width 0.6s ease; }

    /* Grid Area */
    .abs-panel { background: var(--spekta-white); border: 1px solid var(--border-soft); border-radius: 16px; padding: 20px; }
    .abs-week-guide { display: flex; gap: 16px; flex-wrap: wrap; padding-bottom: 14px; border-bottom: 1px solid var(--spekta-gray-light); margin-bottom: 18px; }
    .abs-week-guide div { display: inline-flex; align-items: center; gap: 6px; color: var(--text-muted); font-size: 11px; font-weight: 800; }
    
    .abs-week-grid { display: grid; grid-template-columns: repeat(10, minmax(0, 1fr)); gap: 10px; }
    .abs-week-cell { min-height: 94px; border-radius: 12px; border: 1px solid var(--border-soft); display: grid; place-items: center; align-content: center; text-align: center; transition: all 0.2s ease; background: var(--spekta-white); text-decoration: none; }
    .abs-week-cell small { color: var(--text-muted); font-size: 8px; font-weight: 900; text-transform: uppercase; letter-spacing: .05em; }
    .abs-week-cell strong { color: var(--text-main); font-size: 24px; font-weight: 900; line-height: 1; margin: 2px 0 4px; }
    .abs-week-cell span { color: var(--text-muted); font-size: 9px; font-weight: 800; text-transform: uppercase; }
    
    /* Perbedaan Visual Minggu */
    .abs-week-cell.done { background: #e6f7ed; border-color: #bbf7d0; }
    .abs-week-cell.done strong, .abs-week-cell.done span { color: #15803d; }
    
    .abs-week-cell.open:hover { border-color: var(--spekta-teal); background: var(--spekta-teal-light); transform: translateY(-2px); }
    .abs-week-cell.done:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(22,163,74,.1); }

    @media (max-width: 1200px) { .abs-week-grid { grid-template-columns: repeat(5, minmax(0, 1fr)); } }
    @media (max-width: 760px) { 
        .abs-header { flex-direction: column; align-items: flex-start; gap: 14px; } 
        .abs-progress-box { width: 100%; } 
        .abs-week-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } 
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/pengajar/absensi/weeks.blade.php ENDPATH**/ ?>