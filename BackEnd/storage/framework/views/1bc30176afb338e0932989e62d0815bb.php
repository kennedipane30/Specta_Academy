<?php $__env->startSection('title', 'Teacher Management'); ?>
<?php $__env->startSection('subtitle', 'Sistem Manajemen Data Pengajar Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<div class="tp-page">

    
    <section class="tp-header">
        <div class="tp-header-left">
            <span class="tp-breadcrumb-capsule">Manajemen Akademik</span>
            <h1>Manajemen Pengajar</h1>
            <p>Kelola data pengajar Spekta Academy secara efisien.</p>
        </div>
        <div class="tp-header-actions">
            <a href="<?php echo e(route('admin.manajemen-pengajar.create')); ?>" class="tp-btn-primary">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Pengajar</span>
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
                                $avatarColors = ['#e53935','#2ea8ab','#c5352c','#9e9e9e','#1f2937'];
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
                                    <?php if($teacher->is_verified): ?>
                                        <span class="tp-status active">
                                            <span class="tp-dot-wrapper">
                                                <i class="tp-dot"></i>
                                                <i class="tp-dot-pulse"></i>
                                            </span>
                                            AKTIF
                                        </span>
                                    <?php else: ?>
                                        <span class="tp-status inactive">
                                            <span class="tp-dot-wrapper">
                                                <i class="tp-dot"></i>
                                            </span>
                                            NONAKTIF
                                        </span>
                                    <?php endif; ?>
                                </td>

                                
                                <td class="tp-class-count">
                                    <?php if($classCount > 0): ?>
                                        <span class="tp-class-badge"><?php echo e($classCount); ?> kelas</span>
                                    <?php else: ?>
                                        <span class="tp-muted">—</span>
                                    <?php endif; ?>
                                </td>

                                
                                <td class="tp-date">
                                    <i class="fa-regular fa-clock"></i> <?php echo e($teacher->created_at?->translatedFormat('d M Y') ?? '-'); ?>

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
    font-family: 'Montserrat', system-ui, sans-serif;
    color: #1f2937;
    animation: slideInUp 0.4s ease-out;
}

@keyframes slideInUp {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ── Header ───────────────────────────────────────────────── */
.tp-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 24px;
}

.tp-breadcrumb-capsule {
    display: inline-block;
    background: rgba(229, 57, 53, 0.08);
    color: #c5352c;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 6px;
    margin-bottom: 8px;
}

.tp-header h1 {
    margin: 0 0 6px;
    font-size: 24px;
    font-weight: 900;
    letter-spacing: -0.03em;
    color: #111827;
}

.tp-header p {
    margin: 0;
    color: #6b7280;
    font-size: 13px;
    font-weight: 600;
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
    gap: 8px;
    height: 42px;
    padding: 0 18px;
    background: linear-gradient(135deg, #e53935 0%, #c5352c 100%);
    color: #fff;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 800;
    white-space: nowrap;
    box-shadow: 0 6px 15px rgba(229, 57, 53, 0.2);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
}
.tp-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(229, 57, 53, 0.3);
    color: #fff;
}

/* Alert */
.tp-alert {
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 24px;
    display: flex;
    gap: 10px;
    align-items: center;
    font-size: 13px;
    font-weight: 800;
}
.tp-alert.success {
    background: #e6f7ed;
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
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(15,23,42,.01);
}

/* Top Control Bar (Total + Search Terintegrasi Rapi) */
.tp-table-top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 18px;
    padding-bottom: 18px;
    border-bottom: 1px solid #f3f4f6;
    flex-wrap: wrap;
}

.tp-total-stat {
    display: flex;
    align-items: center;
    gap: 12px;
}
.tp-total-icon {
    width: 42px;
    height: 42px;
    background: var(--spekta-teal-light);
    color: var(--spekta-teal);
    border-radius: 10px;
    display: grid;
    place-items: center;
    font-size: 16px;
}
.tp-total-info {
    display: flex;
    flex-direction: column;
}
.tp-total-label {
    font-size: 10px;
    color: #9e9e9e;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 2px;
}
.tp-total-val-wrap {
    display: flex;
    align-items: baseline;
    gap: 4px;
}
.tp-total-val {
    font-size: 22px;
    font-weight: 900;
    color: #111827;
    line-height: 1;
}
.tp-total-sub {
    font-size: 11px;
    color: #9e9e9e;
    font-weight: 600;
}

/* toolbar / search */
.tp-toolbar {
    display: flex;
    gap: 10px;
    flex: 1;
    max-width: 450px;
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
    color: #9e9e9e;
    font-size: 12px;
    pointer-events: none;
}
.tp-search input {
    width: 100%;
    height: 40px;
    padding: 0 14px 0 38px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #f9fafb;
    font-size: 12px;
    font-weight: 600;
    color: #1f2937;
    outline: none;
    transition: all 0.2s ease;
}
.tp-search input:focus {
    background: #fff;
    border-color: #2ea8ab;
    box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12);
}

.tp-btn-search {
    height: 40px;
    padding: 0 16px;
    background: #1f2937;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
    transition: background .15s;
}
.tp-btn-search:hover { background: var(--spekta-red); }

/* table */
.tp-table-wrap { overflow-x: auto; border-radius: 12px; }

.tp-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

.tp-table thead tr {
    background: #f9fafb;
}

.tp-table th {
    padding: 10px 14px;
    color: #6b7280;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    text-align: left;
    white-space: nowrap;
    border-bottom: 1px solid #e5e7eb;
}

.tp-table td {
    padding: 12px 14px;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle; /* Jaminan mutlak semua data vertikal sejajar tengah */
}

.tp-table tbody tr:last-child td { border-bottom: none; }
.tp-table tbody tr { transition: background-color 0.15s ease; }
.tp-table tbody tr:hover { background: #fafbfc; }

/* teacher cell */
.tp-teacher {
    display: flex;
    align-items: center;
    gap: 10px;
}

.tp-avatar {
    width: 34px;
    height: 34px;
    flex-shrink: 0;
    border-radius: 99px;
    display: grid;
    place-items: center;
    color: #fff;
    font-size: 13px;
    font-weight: 900;
    box-shadow: 0 3px 8px rgba(0,0,0,0.06);
}

.tp-teacher-info strong {
    display: block;
    color: #111827;
    font-size: 13px;
    font-weight: 800;
}

.tp-teacher-info span,
.tp-teacher-info small {
    display: block;
    color: #9e9e9e;
    font-size: 10px;
    font-weight: 600;
    margin-top: 1px;
}

/* subject list */
.tp-subject-list {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}

.tp-subject-badge {
    display: inline-flex;
    align-items: center;
    height: 22px;
    padding: 0 8px;
    background: var(--spekta-teal-light);
    color: var(--spekta-teal);
    border-radius: 6px;
    font-size: 10px;
    font-weight: 800;
    white-space: nowrap;
}
.tp-subject-badge.more {
    background: var(--spekta-gray-light);
    color: var(--text-muted);
}

/* status (Glow style & Pulsing Rata Tengah Sempurna) */
.tp-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    height: 22px;
    padding: 0 8px;
    border-radius: 6px;
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    white-space: nowrap;
}

.tp-dot-wrapper {
    position: relative;
    width: 5px;
    height: 5px;
    display: inline-block;
}

.tp-status .tp-dot {
    width: 5px;
    height: 5px;
    border-radius: 99px;
    background: currentColor;
    display: block;
    position: absolute;
    left: 0; top: 0;
}

.tp-status .tp-dot-pulse {
    width: 5px;
    height: 5px;
    border-radius: 99px;
    background: currentColor;
    display: block;
    position: absolute;
    left: 0; top: 0;
    opacity: 0.4;
    transform: scale(1);
    animation: pulseGlow 1.8s infinite ease-in-out;
}

@keyframes pulseGlow {
    0% { transform: scale(1); opacity: 0.8; }
    100% { transform: scale(3.2); opacity: 0; }
}

.tp-status.active     { background: #e6f7ed; color: #16a34a; box-shadow: 0 2px 6px rgba(22, 163, 74, 0.12); }
.tp-status.inactive   { background: #fee2e2; color: #dc2626; }

/* class badge */
.tp-class-badge {
    display: inline-flex;
    align-items: center;
    height: 22px;
    padding: 0 8px;
    background: var(--spekta-gray-light);
    color: #4b5563;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 800;
    white-space: nowrap;
}

/* date */
.tp-date {
    color: #6b7280;
    font-size: 11px;
    font-weight: 700;
}
.tp-date i { color: #9e9e9e; margin-right: 3px; }

/* muted */
.tp-muted { color: #d1d5db; font-size: 14px; }

/* actions (PERBAIKAN HOVER DAN KESEJAJARAN) */
.tp-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    justify-content: flex-start;
}

.tp-act {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center; /* Ikon diposisikan pas di tengah button */
    border: none;
    font-size: 12px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.tp-act:hover {
    transform: translateY(-1px);
}

.tp-act.edit {
    background: #e0f2fe;
    color: #0369a1;
}
.tp-act.edit:hover {
    background: #bae6fd;
    color: #02507d;
    box-shadow: 0 4px 10px rgba(3, 105, 161, 0.15);
}

.tp-act.delete {
    background: #fee2e2;
    color: #b91c1c;
}
.tp-act.delete:hover {
    background: #fecaca;
    color: #991b1b;
    box-shadow: 0 4px 10px rgba(185, 28, 28, 0.15);
}

/* empty */
.tp-empty { padding: 36px 18px; text-align: center; }
.tp-empty-icon {
    width: 48px;
    height: 48px;
    display: grid;
    place-items: center;
    margin: 0 auto 12px;
    background: var(--spekta-red-light);
    color: var(--spekta-red);
    border-radius: 99px;
    font-size: 18px;
}
.tp-empty strong { display: block; color: #111827; font-size: 14px; font-weight: 800; margin-bottom: 4px; }
.tp-empty span { display: block; color: #9e9e9e; font-size: 12px; font-weight: 600; }

/* pagination */
.tp-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid #f3f4f6;
    margin-top: 4px;
    flex-wrap: wrap;
}
.tp-pagination p { margin: 0; font-size: 11px; color: #6b7280; font-weight: 700; }
.tp-pagination p strong { color: #111827; font-weight: 800; }

.tp-pages { display: flex; align-items: center; gap: 4px; }
.tp-page-btn {
    min-width: 30px;
    height: 30px;
    display: grid;
    place-items: center;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    color: #6b7280;
    font-size: 11px;
    font-weight: 800;
    text-decoration: none;
    transition: all 0.2s ease;
}
.tp-page-btn:hover:not(.disabled):not(.active) {
    border-color: #2ea8ab;
    color: #2ea8ab;
    background: rgba(46, 168, 171, 0.04);
}
.tp-page-btn.active {
    background: #1f2937;
    color: #fff;
    border-color: #1f2937;
    box-shadow: 0 3px 8px rgba(31, 41, 55, 0.2);
}
.tp-page-btn.disabled { opacity: .4; pointer-events: none; cursor: default; }
.tp-page-dots { color: #9ca3af; font-size: 12px; }

/* Responsive Layout */
@media (max-width: 768px) {
    .tp-header { flex-direction: column; gap: 14px; }
    .tp-table-top-bar { flex-direction: column; align-items: flex-start; gap: 16px; }
    .tp-toolbar { width: 100%; max-width: none; }
    .tp-pagination { flex-direction: column; align-items: flex-start; }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\perkuliahan\PA 2 - code\PAAAAA2\BackEnd\resources\views/admin/pengajar/index.blade.php ENDPATH**/ ?>