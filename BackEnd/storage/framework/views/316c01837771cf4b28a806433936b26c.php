<?php $__env->startSection('title', 'Rekapitulasi Mingguan'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $doneCount = count($doneWeeks);
    $progress = round(($doneCount / 20) * 100);
?>

<div class="abs-page">

    <section class="abs-week-header">
        <div>
            <a href="<?php echo e(route('pengajar.absensi.index')); ?>" class="abs-back">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>

            <span>Weekly Attendance</span>
            <h1><?php echo e($class->program_name); ?></h1>
            <p>Rekapitulasi mingguan untuk bidang ajar <strong><?php echo e($subject); ?></strong>.</p>
        </div>

        <div class="abs-progress-box">
            <strong><?php echo e($doneCount); ?>/20</strong>
            <span>Minggu selesai</span>
            <div>
                <em style="width: <?php echo e($progress); ?>%"></em>
            </div>
        </div>
    </section>

    <section class="abs-week-panel">
        <div class="abs-week-guide">
            <div>
                <i class="fa-solid fa-circle-check"></i>
                <span>Hijau berarti absensi sudah diisi dan dapat dilihat recap-nya.</span>
            </div>

            <div>
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Putih berarti absensi belum diisi dan bisa mulai input data.</span>
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
    .abs-page {
        width: 100%;
    }

    .abs-week-header {
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

    .abs-back {
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

    .abs-week-header span {
        display: block;
        color: rgba(255,255,255,.78);
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .16em;
        margin-bottom: 9px;
    }

    .abs-week-header h1 {
        margin: 0 0 8px;
        font-size: 30px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: -0.04em;
    }

    .abs-week-header p {
        margin: 0;
        color: rgba(255,255,255,.86);
        font-size: 13px;
        font-weight: 600;
    }

    .abs-week-header p strong {
        color: #fff;
        font-weight: 900;
    }

    .abs-progress-box {
        width: 230px;
        flex-shrink: 0;
        padding: 18px;
        border-radius: 20px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.17);
        backdrop-filter: blur(12px);
    }

    .abs-progress-box strong {
        display: block;
        font-size: 28px;
        font-weight: 900;
        line-height: 1;
    }

    .abs-progress-box span {
        margin: 8px 0 14px;
        color: rgba(255,255,255,.75);
        letter-spacing: 0;
    }

    .abs-progress-box div {
        height: 8px;
        border-radius: 999px;
        background: rgba(255,255,255,.25);
        overflow: hidden;
    }

    .abs-progress-box em {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: #fff;
    }

    .abs-week-panel {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        padding: 22px;
        box-shadow: 0 14px 35px rgba(15,23,42,.05);
    }

    .abs-week-guide {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        padding-bottom: 18px;
        border-bottom: 1px solid #edf0f4;
        margin-bottom: 18px;
    }

    .abs-week-guide div {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .abs-week-guide i {
        color: #d90429;
    }

    .abs-week-grid {
        display: grid;
        grid-template-columns: repeat(10, minmax(0, 1fr));
        gap: 10px;
    }

    .abs-week-cell {
        min-height: 108px;
        border-radius: 16px;
        border: 1px solid #edf0f4;
        display: grid;
        place-items: center;
        align-content: center;
        text-align: center;
        transition: .18s ease;
        background: #fff;
    }

    .abs-week-cell small {
        color: #9ca3af;
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .09em;
    }

    .abs-week-cell strong {
        color: #111827;
        font-size: 29px;
        font-weight: 900;
        line-height: 1.15;
        margin: 3px 0 6px;
    }

    .abs-week-cell span {
        color: #6b7280;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .abs-week-cell.done {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .abs-week-cell.done strong,
    .abs-week-cell.done span {
        color: #16a34a;
    }

    .abs-week-cell.open:hover {
        border-color: #fecdd3;
        background: #fff7f9;
        transform: translateY(-2px);
    }

    .abs-week-cell.done:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(22,163,74,.09);
    }

    @media (max-width: 1200px) {
        .abs-week-grid {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .abs-week-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .abs-progress-box {
            width: 100%;
        }

        .abs-week-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\Specta_Academy\BackEnd\resources\views/pengajar/absensi/weeks.blade.php ENDPATH**/ ?>