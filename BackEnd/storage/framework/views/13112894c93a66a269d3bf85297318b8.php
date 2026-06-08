<?php $__env->startSection('title', 'Rekap Absensi'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $totalData = $data->count();
    $hadir = $data->where('status', 'h')->count();
    $izin = $data->where('status', 'i')->count();
    $alpa = $data->where('status', 'a')->count();
?>

<div class="abs-page">

    <section class="abs-recap-header">
        <div>
            <a href="<?php echo e(route('pengajar.absensi.weeks', [$class->class_id, $subject])); ?>" class="abs-back">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>

            <span>Attendance Recap</span>
            <h1>Rekap Absensi</h1>
            <p><?php echo e($class->program_name); ?> • <?php echo e($subject); ?> • Minggu <?php echo e($week); ?></p>
        </div>

        <div class="abs-recap-summary">
            <div>
                <strong><?php echo e($hadir); ?></strong>
                <span>Hadir</span>
            </div>

            <div>
                <strong><?php echo e($izin); ?></strong>
                <span>Izin</span>
            </div>

            <div>
                <strong><?php echo e($alpa); ?></strong>
                <span>Alpa</span>
            </div>
        </div>
    </section>

    <section class="abs-panel">
        <div class="abs-panel-head">
            <div>
                <span>Student Attendance</span>
                <h2>Daftar Kehadiran Siswa</h2>
                <p>Total data absensi: <?php echo e($totalData); ?> siswa.</p>
            </div>
        </div>

        <div class="abs-table-wrap">
            <table class="abs-table">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Status Kehadiran</th>
                        <th>Tanggal Input</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="abs-student-name">
                                    <strong><?php echo e($row->user->name ?? 'N/A'); ?></strong>
                                    <span>Siswa</span>
                                </div>
                            </td>

                            <td>
                                <?php if($row->status == 'h'): ?>
                                    <span class="abs-badge hadir">Hadir</span>
                                <?php elseif($row->status == 'i'): ?>
                                    <span class="abs-badge izin">Izin</span>
                                <?php else: ?>
                                    <span class="abs-badge alpa">Alpa</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <span class="abs-date-text">
                                    <?php echo e($row->date ? date('d M Y', strtotime($row->date)) : '-'); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="3">
                                <div class="abs-empty">
                                    <i class="fa-solid fa-folder-open"></i>
                                    <strong>Data absensi tidak ditemukan.</strong>
                                    <span>Belum ada data absensi untuk minggu ini.</span>
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
    .abs-page {
        width: 100%;
    }

    .abs-recap-header {
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

    .abs-recap-header span {
        display: block;
        color: rgba(255,255,255,.78);
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .16em;
        margin-bottom: 9px;
    }

    .abs-recap-header h1 {
        margin: 0 0 8px;
        color: #fff;
        font-size: 30px;
        font-weight: 900;
        letter-spacing: -0.04em;
        text-transform: uppercase;
    }

    .abs-recap-header p {
        margin: 0;
        color: rgba(255,255,255,.86);
        font-size: 13px;
        font-weight: 700;
    }

    .abs-recap-summary {
        display: flex;
        gap: 10px;
        flex-shrink: 0;
    }

    .abs-recap-summary div {
        min-width: 92px;
        padding: 15px;
        border-radius: 18px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.17);
        text-align: center;
    }

    .abs-recap-summary strong {
        display: block;
        color: #fff;
        font-size: 25px;
        font-weight: 900;
        line-height: 1;
    }

    .abs-recap-summary span {
        margin: 8px 0 0;
        color: rgba(255,255,255,.76);
        letter-spacing: 0;
    }

    .abs-panel {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        padding: 22px;
        box-shadow: 0 14px 35px rgba(15,23,42,.05);
    }

    .abs-panel-head {
        margin-bottom: 18px;
    }

    .abs-panel-head span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .16em;
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

    .abs-student-name strong {
        display: block;
        color: #111827;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .abs-student-name span {
        display: block;
        margin-top: 4px;
        color: #9ca3af;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .abs-badge {
        display: inline-flex;
        align-items: center;
        height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .abs-badge.hadir {
        background: #dcfce7;
        color: #16a34a;
    }

    .abs-badge.izin {
        background: #fef3c7;
        color: #d97706;
    }

    .abs-badge.alpa {
        background: #fee2e2;
        color: #dc2626;
    }

    .abs-date-text {
        color: #6b7280;
        font-size: 12px;
        font-weight: 800;
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

    @media (max-width: 850px) {
        .abs-recap-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .abs-recap-summary {
            width: 100%;
        }

        .abs-recap-summary div {
            flex: 1;
        }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/pengajar/absensi/recap.blade.php ENDPATH**/ ?>