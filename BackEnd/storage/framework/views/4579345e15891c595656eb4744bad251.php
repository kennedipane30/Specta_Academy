<?php $__env->startSection('title', 'Student Management'); ?>
<?php $__env->startSection('subtitle', 'Sistem Manajemen Data Siswa Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<div class="ss-page">

    
    <section class="ss-header">
        <div class="ss-header-left">
            <span class="ss-breadcrumb">Manajemen Akademik</span>
            <h1>Manajemen Siswa</h1>
            <p>Kelola data siswa Spekta Academy secara efisien berdasarkan data pendaftaran dari aplikasi.</p>
        </div>
        <div class="ss-header-actions">
            <a href="<?php echo e(route('admin.siswa.pendaftaran')); ?>" class="ss-btn-primary">
                <i class="fa-solid fa-circle-check"></i>
                Konfirmasi Kelas
                <?php if(($pendingEnrollment ?? 0) > 0): ?>
                    <em><?php echo e($pendingEnrollment); ?></em>
                <?php endif; ?>
            </a>
        </div>
    </section>

    
    <section class="ss-stats">

        <div class="ss-stat-card">
            <div class="ss-stat-top">
                <div class="ss-stat-icon red">
                    <i class="fa-solid fa-user-group"></i>
                </div>
                <span class="ss-stat-badge green">Live</span>
            </div>
            <p class="ss-stat-label">Total Siswa</p>
            <h2 class="ss-stat-val"><?php echo e(number_format($totalSiswa ?? 0)); ?></h2>
            <div class="ss-stat-bar">
                <div class="ss-stat-bar-fill" style="width:100%"></div>
            </div>
            <small class="ss-stat-sub">data siswa terdaftar</small>
        </div>

        <div class="ss-stat-card">
            <div class="ss-stat-top">
                <div class="ss-stat-icon green">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <span class="ss-stat-badge green">Aktif</span>
            </div>
            <p class="ss-stat-label">Siswa Aktif</p>
            <h2 class="ss-stat-val"><?php echo e(number_format($siswaAktif ?? 0)); ?></h2>
            <div class="ss-stat-bar">
                <div class="ss-stat-bar-fill green" style="width:68%"></div>
            </div>
            <small class="ss-stat-sub">enrollment aktif</small>
        </div>

        <div class="ss-stat-card">
            <div class="ss-stat-top">
                <div class="ss-stat-icon blue">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <span class="ss-stat-badge <?php echo e(($growthSiswa ?? 0) >= 0 ? 'green' : 'red'); ?>">
                    <?php echo e(($growthSiswa ?? 0) >= 0 ? '+' : ''); ?><?php echo e($growthSiswa ?? 0); ?>%
                </span>
            </div>
            <p class="ss-stat-label">Siswa Baru Bulan Ini</p>
            <h2 class="ss-stat-val"><?php echo e(number_format($siswaBaruBulanIni ?? 0)); ?></h2>
            <div class="ss-stat-bar">
                <div class="ss-stat-bar-fill blue" style="width:42%"></div>
            </div>
            <small class="ss-stat-sub">vs bulan lalu</small>
        </div>

    </section>

    
    <section class="ss-main-grid">

        
        <div class="ss-table-panel">

            
            <form method="GET" action="<?php echo e(route('admin.siswa.index')); ?>" class="ss-toolbar">
                <div class="ss-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input
                        type="text"
                        name="search"
                        value="<?php echo e(request('search')); ?>"
                        placeholder="Cari nama siswa, NIS, atau email..."
                    >
                </div>

                <button type="submit" class="ss-btn-search">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
            </form>

            
            <div class="ss-table-wrap">
                <table class="ss-table">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th>Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $siswas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $student         = $s->student;
                                $latestEnrollment= ($latestEnrollmentMap ?? collect())->get($s->usersID);
                                $activeClass     = $latestEnrollment?->class;
                                $status          = $latestEnrollment?->status ?? 'registered';

                                $statusMap = [
                                    'active'     => ['label' => 'Aktif',      'cls' => 'active'],
                                    'pending'    => ['label' => 'Pending',    'cls' => 'pending'],
                                    'expired'    => ['label' => 'Expired',    'cls' => 'expired'],
                                    'registered' => ['label' => 'Registered', 'cls' => 'registered'],
                                ];
                                $st = $statusMap[$status] ?? $statusMap['registered'];

                                $initial = strtoupper(substr($s->name, 0, 1));
                                $avatarColors = ['#D90429','#7C3AED','#0369A1','#15803D','#C2410C'];
                                $avatarBg = $avatarColors[crc32($s->name) % count($avatarColors)];
                            ?>
                            <tr>
                                
                                <td>
                                    <div class="ss-student">
                                        <div class="ss-avatar" style="background:<?php echo e($avatarBg); ?>">
                                            <?php echo e($initial); ?>

                                        </div>
                                        <div class="ss-student-info">
                                            <strong><?php echo e($s->name); ?></strong>
                                            <span>NIS: <?php echo e($student?->national_id_number ?? '-'); ?></span>
                                            <small><?php echo e($s->email); ?></small>
                                        </div>
                                    </div>
                                </td>

                                
                                <td>
                                    <?php if($activeClass): ?>
                                        <span class="ss-class-badge">Kelas <?php echo e($activeClass->class_id); ?></span>
                                    <?php else: ?>
                                        <span class="ss-muted">—</span>
                                    <?php endif; ?>
                                </td>

                                
                                <td>
                                    <span class="ss-program-name"><?php echo e($activeClass?->program_name ?? '—'); ?></span>
                                </td>

                                
                                <td>
                                    <span class="ss-status <?php echo e($st['cls']); ?>">
                                        <i class="ss-dot"></i><?php echo e($st['label']); ?>

                                    </span>
                                </td>

                                
                                <td class="ss-date">
                                    <?php echo e($s->created_at?->translatedFormat('d M Y') ?? '-'); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="ss-empty">
                                        <div class="ss-empty-icon">
                                            <i class="fa-solid fa-user-slash"></i>
                                        </div>
                                        <strong>Belum ada data siswa</strong>
                                        <span>Data siswa akan muncul setelah siswa mendaftar melalui aplikasi.</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
            <div class="ss-pagination">
                <p>
                    Menampilkan
                    <strong><?php echo e($siswas->firstItem() ?? 0); ?></strong>–<strong><?php echo e($siswas->lastItem() ?? 0); ?></strong>
                    dari <strong><?php echo e(number_format($siswas->total() ?? 0)); ?></strong> siswa
                </p>

                <?php if(method_exists($siswas, 'hasPages') && $siswas->hasPages()): ?>
                    <div class="ss-pages">
                        <?php if($siswas->onFirstPage()): ?>
                            <span class="ss-page-btn disabled"><i class="fa-solid fa-chevron-left"></i></span>
                        <?php else: ?>
                            <a href="<?php echo e($siswas->previousPageUrl()); ?>" class="ss-page-btn">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php $__currentLoopData = range(1, $siswas->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($page == 1 || $page == $siswas->lastPage() || abs($page - $siswas->currentPage()) <= 1): ?>
                                <a href="<?php echo e($siswas->url($page)); ?>"
                                   class="ss-page-btn <?php echo e($page == $siswas->currentPage() ? 'active' : ''); ?>">
                                    <?php echo e($page); ?>

                                </a>
                            <?php elseif(abs($page - $siswas->currentPage()) == 2): ?>
                                <span class="ss-page-dots">…</span>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php if($siswas->hasMorePages()): ?>
                            <a href="<?php echo e($siswas->nextPageUrl()); ?>" class="ss-page-btn">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <span class="ss-page-btn disabled"><i class="fa-solid fa-chevron-right"></i></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </section>

</div>




<style>
/* ── Base ─────────────────────────────────────────────────── */
.ss-page {
    width: 100%;
    font-family: 'Inter', system-ui, sans-serif;
    color: #111827;
}

/* ── Header ───────────────────────────────────────────────── */
.ss-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 28px;
}

.ss-breadcrumb {
    display: block;
    color: #d90429;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: .18em;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.ss-header h1 {
    margin: 0 0 6px;
    font-size: 26px;
    font-weight: 900;
    letter-spacing: -.03em;
    color: #0f172a;
}

.ss-header p {
    margin: 0;
    color: #6b7280;
    font-size: 13px;
    font-weight: 500;
}

.ss-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}

.ss-btn-primary {
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
}
.ss-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 28px rgba(217,4,41,.35);
    color: #fff;
}
.ss-btn-primary em {
    min-width: 22px;
    height: 22px;
    display: grid;
    place-items: center;
    background: #fff;
    color: #d90429;
    border-radius: 99px;
    font-size: 10px;
    font-style: normal;
    font-weight: 900;
}

/* ── Stat Cards ───────────────────────────────────────────── */
.ss-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0,1fr)); /* Diubah menjadi 3 kolom */
    gap: 18px;
    margin-bottom: 26px;
}

.ss-stat-card {
    background: #fff;
    border: 1px solid #edf0f4;
    border-radius: 22px;
    padding: 22px 20px 18px;
    box-shadow: 0 2px 12px rgba(15,23,42,.04);
    transition: box-shadow .2s, transform .2s;
}
.ss-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(15,23,42,.08);
}

.ss-stat-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.ss-stat-icon {
    width: 44px;
    height: 44px;
    display: grid;
    place-items: center;
    border-radius: 14px;
    font-size: 17px;
}
.ss-stat-icon.red    { background: #fff1f2; color: #d90429; }
.ss-stat-icon.green  { background: #dcfce7; color: #16a34a; }
.ss-stat-icon.blue   { background: #dbeafe; color: #2563eb; }

.ss-stat-badge {
    height: 22px;
    display: inline-flex;
    align-items: center;
    padding: 0 9px;
    border-radius: 99px;
    font-size: 10px;
    font-weight: 800;
}
.ss-stat-badge.green { background: #dcfce7; color: #16a34a; }
.ss-stat-badge.blue  { background: #dbeafe; color: #2563eb; }
.ss-stat-badge.red   { background: #fee2e2; color: #dc2626; }

.ss-stat-label {
    margin: 0 0 4px;
    font-size: 11px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .06em;
}

.ss-stat-val {
    margin: 0 0 14px;
    font-size: 30px;
    font-weight: 900;
    letter-spacing: -.04em;
    color: #0f172a;
}

.ss-stat-bar {
    height: 4px;
    background: #f1f5f9;
    border-radius: 99px;
    overflow: hidden;
    margin-bottom: 8px;
}
.ss-stat-bar-fill {
    height: 100%;
    border-radius: 99px;
    background: #d90429;
    transition: width .6s ease;
}
.ss-stat-bar-fill.green  { background: #16a34a; }
.ss-stat-bar-fill.blue   { background: #2563eb; }

.ss-stat-sub {
    font-size: 11px;
    color: #9ca3af;
    font-weight: 600;
}

/* ── Main Grid ────────────────────────────────────────────── */
.ss-main-grid {
    display: block;
    margin-bottom: 22px;
}

/* ── Table Panel ──────────────────────────────────────────── */
.ss-table-panel {
    background: #fff;
    border: 1px solid #edf0f4;
    border-radius: 22px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(15,23,42,.04);
}

/* toolbar */
.ss-toolbar {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 18px;
}

.ss-search {
    position: relative;
    flex: 1;
    min-width: 200px;
}
.ss-search > i {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 12px;
    pointer-events: none;
}
.ss-search input {
    width: 100%;
    height: 42px;
    padding: 0 14px 0 38px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #f8fafc;
    font-size: 13px;
    font-weight: 500;
    color: #111827;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.ss-search input:focus {
    background: #fff;
    border-color: #fca5a5;
    box-shadow: 0 0 0 3px rgba(217,4,41,.08);
}

.ss-btn-search {
    height: 42px;
    padding: 0 16px;
    background: #d90429;
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
    transition: background .15s;
}
.ss-btn-search:hover { background: #b80222; }

/* table */
.ss-table-wrap { overflow-x: auto; border-radius: 14px; }

.ss-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 650px;
}

.ss-table thead tr {
    background: #f8fafc;
}

.ss-table th {
    padding: 12px 14px;
    color: #6b7280;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    text-align: left;
    white-space: nowrap;
    border-bottom: 1px solid #edf0f4;
}

.ss-table td {
    padding: 14px;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.ss-table tbody tr:last-child td { border-bottom: none; }

.ss-table tbody tr:hover { background: #fffbfc; }

/* student cell */
.ss-student {
    display: flex;
    align-items: center;
    gap: 11px;
}

.ss-avatar {
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    border-radius: 99px;
    display: grid;
    place-items: center;
    color: #fff;
    font-size: 14px;
    font-weight: 900;
}

.ss-student-info strong {
    display: block;
    color: #111827;
    font-size: 13px;
    font-weight: 800;
}

.ss-student-info span,
.ss-student-info small {
    display: block;
    color: #9ca3af;
    font-size: 10px;
    font-weight: 600;
    margin-top: 2px;
}

/* class badge */
.ss-class-badge {
    display: inline-flex;
    align-items: center;
    height: 24px;
    padding: 0 10px;
    background: #f1f5f9;
    color: #475569;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 800;
    white-space: nowrap;
}

/* program name */
.ss-program-name {
    font-size: 12px;
    font-weight: 700;
    color: #374151;
}

/* status */
.ss-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    height: 24px;
    padding: 0 10px;
    border-radius: 99px;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    white-space: nowrap;
}
.ss-status .ss-dot {
    width: 5px;
    height: 5px;
    border-radius: 99px;
    background: currentColor;
    display: inline-block;
    flex-shrink: 0;
}
.ss-status.active     { background: #dcfce7; color: #16a34a; }
.ss-status.pending    { background: #fff7ed; color: #c2410c; }
.ss-status.expired    { background: #fee2e2; color: #dc2626; }
.ss-status.registered { background: #dbeafe; color: #1d4ed8; }

/* date */
.ss-date {
    color: #6b7280;
    font-size: 12px;
}

/* muted */
.ss-muted { color: #d1d5db; font-size: 16px; }

/* empty */
.ss-empty {
    padding: 40px 20px;
    text-align: center;
}
.ss-empty-icon {
    width: 54px;
    height: 54px;
    display: grid;
    place-items: center;
    margin: 0 auto 14px;
    background: #fff1f2;
    color: #d90429;
    border-radius: 99px;
    font-size: 20px;
}
.ss-empty strong {
    display: block;
    color: #111827;
    font-size: 15px;
    font-weight: 900;
    margin-bottom: 5px;
}
.ss-empty span {
    display: block;
    color: #9ca3af;
    font-size: 13px;
}

/* pagination */
.ss-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-top: 18px;
    border-top: 1px solid #f1f5f9;
    margin-top: 4px;
    flex-wrap: wrap;
}
.ss-pagination p {
    margin: 0;
    font-size: 12px;
    color: #6b7280;
    font-weight: 600;
}
.ss-pagination p strong { color: #111827; font-weight: 800; }

.ss-pages {
    display: flex;
    align-items: center;
    gap: 6px;
}
.ss-page-btn {
    min-width: 34px;
    height: 34px;
    display: grid;
    place-items: center;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    color: #6b7280;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    transition: background .15s, border-color .15s;
}
.ss-page-btn:hover:not(.disabled):not(.active) {
    border-color: #fca5a5;
    color: #d90429;
}
.ss-page-btn.active {
    background: #d90429;
    color: #fff;
    border-color: #d90429;
}
.ss-page-btn.disabled { opacity: .4; pointer-events: none; }
.ss-page-dots { color: #9ca3af; font-size: 13px; }

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width: 1280px) {
    .ss-stats               { grid-template-columns: repeat(2,1fr); }
}

@media (max-width: 768px) {
    .ss-header              { flex-direction: column; gap: 14px; }
    .ss-stats               { grid-template-columns: 1fr; }
    .ss-toolbar             { flex-direction: column; }
    .ss-pagination          { flex-direction: column; align-items: flex-start; }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/siswa/index.blade.php ENDPATH**/ ?>