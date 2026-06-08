<?php $__env->startSection('title', 'Teacher Management'); ?>
<?php $__env->startSection('subtitle', 'Sistem Manajemen Data Pengajar Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<div class="tp-page">

    
    <section class="tp-header">
        <div class="tp-header-left">
            <span class="tp-breadcrumb">Manajemen Akademik</span>
            <h1>Manajemen Pengajar</h1>
            <p>Kelola data pengajar Spekta Academy secara efisien.</p>
        </div>
        <div class="tp-header-actions">
            <a href="<?php echo e(route('admin.manajemen-pengajar.create')); ?>" class="tp-btn-primary">
                <i class="fa-solid fa-plus"></i>
                Tambah Pengajar
            </a>
        </div>
    </section>

    
    <?php if(session('success')): ?>
        <div class="tp-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    
    <section class="tp-main-grid">

        
        <div class="tp-table-panel">

            
            <div class="tp-table-top-bar">

                
                <div class="tp-total-stat">
                    <div class="tp-total-icon">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                    <div class="tp-total-info">
                        <span class="tp-total-label">Total Pengajar</span>
                        <div class="tp-total-val-wrap">
                            <span class="tp-total-val"><?php echo e(number_format($totalPengajar ?? 0)); ?></span>
                            <span class="tp-total-sub">terdaftar</span>
                        </div>
                    </div>
                </div>

                
                <form method="GET" action="<?php echo e(route('admin.manajemen-pengajar.index')); ?>" class="tp-toolbar">
                    <div class="tp-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input
                            type="text"
                            name="search"
                            value="<?php echo e(request('search')); ?>"
                            placeholder="Cari nama pengajar, NIP, atau email..."
                        >
                    </div>

                    <button type="submit" class="tp-btn-search">
                        <i class="fa-solid fa-magnifying-glass"></i> Cari
                    </button>
                </form>

            </div>

            
            <div class="tp-table-wrap">
                <table class="tp-table">
                    <thead>
                        <tr>
                            <th>Nama Pengajar</th>
                            <th>Bidang Ajar</th>
                            <th>Status</th>
                            <th>Kelas Aktif</th>
                            <th>Tanggal Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $teacherAssignments = ($assignmentMap ?? collect())->get($teacher->usersID, collect());
                                $subjectList = $teacherAssignments->pluck('subject_name')->filter()->unique()->values();
                                $classCount = $teacherAssignments->pluck('class_id')->filter()->unique()->count();

                                if (($scheduleCountMap ?? collect())->has($teacher->usersID)) {
                                    $classCount = max($classCount, (int) $scheduleCountMap[$teacher->usersID]);
                                }

                                $initial = strtoupper(substr($teacher->name, 0, 1));
                                $avatarColors = ['#D90429','#7C3AED','#0369A1','#15803D','#C2410C'];
                                $avatarBg = $avatarColors[crc32($teacher->name) % count($avatarColors)];
                            ?>
                            <tr>
                                
                                <td>
                                    <div class="tp-teacher">
                                        <div class="tp-avatar" style="background:<?php echo e($avatarBg); ?>">
                                            <?php echo e($initial); ?>

                                        </div>
                                        <div class="tp-teacher-info">
                                            <strong><?php echo e($teacher->name); ?></strong>
                                            <span>NIP: <?php echo e(str_pad($teacher->usersID, 6, '0', STR_PAD_LEFT)); ?></span>
                                            <small><?php echo e($teacher->email); ?></small>
                                        </div>
                                    </div>
                                </td>

                                
                                <td>
                                    <?php if($subjectList->count() > 0): ?>
                                        <div class="tp-subject-list">
                                            <?php $__currentLoopData = $subjectList->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="tp-subject-badge"><?php echo e($subject); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($subjectList->count() > 3): ?>
                                                <span class="tp-subject-badge more">+<?php echo e($subjectList->count() - 3); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="tp-muted">Belum ditugaskan</span>
                                    <?php endif; ?>
                                </td>

                                
                                <td>
                                    <span class="tp-status <?php echo e($teacher->is_verified ? 'active' : 'inactive'); ?>">
                                        <i class="tp-dot"></i><?php echo e($teacher->is_verified ? 'AKTIF' : 'NONAKTIF'); ?>

                                    </span>
                                </td>

                                
                                <td class="tp-class-count">
                                    <?php if($classCount > 0): ?>
                                        <span class="tp-class-badge"><?php echo e($classCount); ?> kelas</span>
                                    <?php else: ?>
                                        <span class="tp-muted">—</span>
                                    <?php endif; ?>
                                </td>

                                
                                <td class="tp-date">
                                    <?php echo e($teacher->created_at?->translatedFormat('d M Y') ?? '-'); ?>

                                </td>

                                
                                <td>
                                    <div class="tp-actions">
                                        <a href="<?php echo e(route('admin.manajemen-pengajar.edit', $teacher->usersID)); ?>" class="tp-act edit" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <form action="<?php echo e(route('admin.manajemen-pengajar.destroy', $teacher->usersID)); ?>" method="POST" onsubmit="return confirm('Hapus akun pengajar ini?')" style="display: inline;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="tp-act delete" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="tp-empty">
                                        <div class="tp-empty-icon">
                                            <i class="fa-solid fa-user-slash"></i>
                                        </div>
                                        <strong>Belum ada data pengajar</strong>
                                        <span>Tambahkan akun pengajar melalui tombol Tambah Pengajar.</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <div class="tp-pagination">
                <p>
                    Menampilkan
                    <strong><?php echo e($teachers->firstItem() ?? 0); ?></strong>–<strong><?php echo e($teachers->lastItem() ?? 0); ?></strong>
                    dari <strong><?php echo e(number_format($teachers->total() ?? 0)); ?></strong> pengajar
                </p>

                <?php if(method_exists($teachers, 'hasPages') && $teachers->hasPages()): ?>
                    <div class="tp-pages">
                        <?php if($teachers->onFirstPage()): ?>
                            <span class="tp-page-btn disabled"><i class="fa-solid fa-chevron-left"></i></span>
                        <?php else: ?>
                            <a href="<?php echo e($teachers->previousPageUrl()); ?>" class="tp-page-btn">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php $__currentLoopData = range(1, $teachers->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($page == 1 || $page == $teachers->lastPage() || abs($page - $teachers->currentPage()) <= 1): ?>
                                <a href="<?php echo e($teachers->url($page)); ?>"
                                   class="tp-page-btn <?php echo e($page == $teachers->currentPage() ? 'active' : ''); ?>">
                                    <?php echo e($page); ?>

                                </a>
                            <?php elseif(abs($page - $teachers->currentPage()) == 2): ?>
                                <span class="tp-page-dots">…</span>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php if($teachers->hasMorePages()): ?>
                            <a href="<?php echo e($teachers->nextPageUrl()); ?>" class="tp-page-btn">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <span class="tp-page-btn disabled"><i class="fa-solid fa-chevron-right"></i></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </section>

</div>

<style>
/* ── Base ─────────────────────────────────────────────────── */
.tp-page {
    width: 100%;
    font-family: 'Inter', system-ui, sans-serif;
    color: #111827;
}

/* ── Header ───────────────────────────────────────────────── */
.tp-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 28px;
}

.tp-breadcrumb {
    display: block;
    color: #d90429;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: .18em;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.tp-header h1 {
    margin: 0 0 6px;
    font-size: 26px;
    font-weight: 900;
    letter-spacing: -.03em;
    color: #0f172a;
}

.tp-header p {
    margin: 0;
    color: #6b7280;
    font-size: 13px;
    font-weight: 500;
}

.tp-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}

.tp-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    height: 44px;
    padding: 0 20px;
    background: #d90429;
    color: #fff;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 800;
    white-space: nowrap;
    box-shadow: 0 8px 24px rgba(217,4,41,.28);
    transition: transform .15s, box-shadow .15s;
    text-decoration: none;
}
.tp-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 28px rgba(217,4,41,.35);
    color: #fff;
}

/* Alert */
.tp-alert {
    border-radius: 16px;
    padding: 15px 17px;
    margin-bottom: 20px;
    display: flex;
    gap: 12px;
    align-items: flex-start;
    font-size: 13px;
    font-weight: 800;
}
.tp-alert.success {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}

/* ── Main Grid & Table Panel ──────────────────────────────── */
.tp-main-grid {
    display: block;
    margin-bottom: 22px;
}

.tp-table-panel {
    background: #fff;
    border: 1px solid #edf0f4;
    border-radius: 22px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(15,23,42,.04);
}

/* Top Control Bar (Total + Search) */
.tp-table-top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 1px solid #f1f5f9;
    flex-wrap: wrap;
}

.tp-total-stat {
    display: flex;
    align-items: center;
    gap: 14px;
}
.tp-total-icon {
    width: 48px;
    height: 48px;
    background: #fff1f2;
    color: #d90429;
    border-radius: 14px;
    display: grid;
    place-items: center;
    font-size: 18px;
}
.tp-total-info {
    display: flex;
    flex-direction: column;
}
.tp-total-label {
    font-size: 11px;
    color: #6b7280;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 2px;
}
.tp-total-val-wrap {
    display: flex;
    align-items: baseline;
    gap: 6px;
}
.tp-total-val {
    font-size: 26px;
    font-weight: 900;
    color: #0f172a;
    line-height: 1;
}
.tp-total-sub {
    font-size: 12px;
    color: #9ca3af;
    font-weight: 600;
}

/* toolbar / search */
.tp-toolbar {
    display: flex;
    gap: 10px;
    flex: 1;
    max-width: 500px;
    justify-content: flex-end;
}

.tp-search {
    position: relative;
    flex: 1;
    min-width: 200px;
}
.tp-search > i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 13px;
    pointer-events: none;
}
.tp-search input {
    width: 100%;
    height: 44px;
    padding: 0 14px 0 40px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #f8fafc;
    font-size: 13px;
    font-weight: 500;
    color: #111827;
    outline: none;
    transition: border-color .15s, box-shadow .15s, background .15s;
}
.tp-search input:focus {
    background: #fff;
    border-color: #fca5a5;
    box-shadow: 0 0 0 3px rgba(217,4,41,.08);
}

.tp-btn-search {
    height: 44px;
    padding: 0 20px;
    background: #d90429;
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
    transition: background .15s, transform .1s;
}
.tp-btn-search:hover { background: #b80222; transform: translateY(-1px); }

/* table */
.tp-table-wrap { overflow-x: auto; border-radius: 14px; }

.tp-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

.tp-table thead tr {
    background: #f8fafc;
}

.tp-table th {
    padding: 14px 16px;
    color: #6b7280;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    text-align: left;
    white-space: nowrap;
    border-bottom: 1px solid #edf0f4;
}

.tp-table td {
    padding: 16px;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.tp-table tbody tr:last-child td { border-bottom: none; }
.tp-table tbody tr:hover { background: #fffbfc; }

/* teacher cell */
.tp-teacher {
    display: flex;
    align-items: center;
    gap: 12px;
}

.tp-avatar {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    border-radius: 99px;
    display: grid;
    place-items: center;
    color: #fff;
    font-size: 14px;
    font-weight: 900;
}

.tp-teacher-info strong {
    display: block;
    color: #111827;
    font-size: 14px;
    font-weight: 800;
}

.tp-teacher-info span,
.tp-teacher-info small {
    display: block;
    color: #9ca3af;
    font-size: 11px;
    font-weight: 600;
    margin-top: 2px;
}

/* subject list */
.tp-subject-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.tp-subject-badge {
    display: inline-flex;
    align-items: center;
    height: 24px;
    padding: 0 10px;
    background: #fff1f2;
    color: #d90429;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 800;
    white-space: nowrap;
}
.tp-subject-badge.more {
    background: #f1f5f9;
    color: #64748b;
}

/* status */
.tp-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    height: 24px;
    padding: 0 10px;
    border-radius: 99px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .04em;
    white-space: nowrap;
}
.tp-status .tp-dot {
    width: 5px;
    height: 5px;
    border-radius: 99px;
    background: currentColor;
    display: inline-block;
    flex-shrink: 0;
}
.tp-status.active     { background: #dcfce7; color: #16a34a; }
.tp-status.inactive   { background: #fee2e2; color: #dc2626; }

/* class badge */
.tp-class-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 8px;
    background: #dbeafe;
    color: #1e40af;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
}

/* date */
.tp-date {
    color: #6b7280;
    font-size: 12px;
}

/* muted */
.tp-muted { color: #9ca3af; font-size: 12px; }

/* actions */
.tp-actions {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.tp-act {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 10px;
    display: inline-grid;
    place-items: center;
    font-size: 12px;
    cursor: pointer;
    text-decoration: none;
    transition: transform .12s, box-shadow .12s;
}
.tp-act:hover { transform: scale(1.05); }
.tp-act.edit      { background: #dbeafe; color: #2563eb; }
.tp-act.delete    { background: #fee2e2; color: #dc2626; }

/* empty */
.tp-empty {
    padding: 50px 20px;
    text-align: center;
}
.tp-empty-icon {
    width: 60px;
    height: 60px;
    display: grid;
    place-items: center;
    margin: 0 auto 16px;
    background: #fff1f2;
    color: #d90429;
    border-radius: 99px;
    font-size: 24px;
}
.tp-empty strong {
    display: block;
    color: #111827;
    font-size: 16px;
    font-weight: 900;
    margin-bottom: 6px;
}
.tp-empty span {
    display: block;
    color: #9ca3af;
    font-size: 14px;
}

/* pagination */
.tp-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
    margin-top: 10px;
    flex-wrap: wrap;
}
.tp-pagination p {
    margin: 0;
    font-size: 13px;
    color: #6b7280;
    font-weight: 600;
}
.tp-pagination p strong { color: #111827; font-weight: 800; }

.tp-pages {
    display: flex;
    align-items: center;
    gap: 6px;
}
.tp-page-btn {
    min-width: 36px;
    height: 36px;
    display: grid;
    place-items: center;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    color: #6b7280;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: background .15s, border-color .15s;
}
.tp-page-btn:hover:not(.disabled):not(.active) {
    border-color: #fca5a5;
    color: #d90429;
}
.tp-page-btn.active {
    background: #d90429;
    color: #fff;
    border-color: #d90429;
}
.tp-page-btn.disabled { opacity: .4; pointer-events: none; cursor: default; }
.tp-page-dots { color: #9ca3af; font-size: 14px; }

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width: 768px) {
    .tp-header              { flex-direction: column; gap: 14px; }
    .tp-table-top-bar       { flex-direction: column; align-items: flex-start; gap: 16px; }
    .tp-toolbar             { width: 100%; max-width: none; }
    .tp-pagination          { flex-direction: column; align-items: flex-start; }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/pengajar/index.blade.php ENDPATH**/ ?>