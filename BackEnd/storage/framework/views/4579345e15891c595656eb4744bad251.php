<?php $__env->startSection('title', 'Student Management'); ?>
<?php $__env->startSection('subtitle', 'Sistem Manajemen Data Siswa Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<div class="ss-page">

    
    <section class="ss-header">
        <div class="ss-header-left">
            <span class="ss-breadcrumb-capsule">Manajemen Akademik</span>
            <h1>Manajemen Siswa</h1>
            <p>Kelola data siswa Spekta Academy secara efisien berdasarkan data pendaftaran dari aplikasi.</p>
        </div>
    </section>

    
    <section class="ss-stats">

        <!-- Card: Total Siswa -->
        <div class="ss-stat-card card-teal">
            <div class="ss-stat-top">
                <div class="ss-stat-icon teal">
                    <i class="fa-solid fa-user-group"></i>
                </div>
                <span class="ss-stat-badge green">Live</span>
            </div>
            <div class="ss-stat-info">
                <p class="ss-stat-label">Total Siswa</p>
                <h2 class="ss-stat-val"><?php echo e(number_format($totalSiswa ?? 0)); ?></h2>
            </div>
            <div class="ss-stat-bar">
                <div class="ss-stat-bar-fill teal" style="width:100%"></div>
            </div>
            <small class="ss-stat-sub">data siswa terdaftar</small>
        </div>

        <!-- Card: Siswa Aktif -->
        <div class="ss-stat-card card-green">
            <div class="ss-stat-top">
                <div class="ss-stat-icon green">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <span class="ss-stat-badge green">Aktif</span>
            </div>
            <div class="ss-stat-info">
                <p class="ss-stat-label">Siswa Aktif</p>
                <h2 class="ss-stat-val"><?php echo e(number_format($siswaAktif ?? 0)); ?></h2>
            </div>
            <div class="ss-stat-bar">
                <div class="ss-stat-bar-fill green" style="width:100%"></div>
            </div>
            <small class="ss-stat-sub">enrollment aktif</small>
        </div>

        <!-- Card: Siswa Baru -->
        <div class="ss-stat-card card-red">
            <div class="ss-stat-top">
                <div class="ss-stat-icon red">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <span class="ss-stat-badge <?php echo e(($growthSiswa ?? 0) >= 0 ? 'red' : 'red-dark'); ?>">
                    <?php echo e(($growthSiswa ?? 0) >= 0 ? '+' : ''); ?><?php echo e($growthSiswa ?? 0); ?>%
                </span>
            </div>
            <div class="ss-stat-info">
                <p class="ss-stat-label">Siswa Baru Bulan Ini</p>
                <h2 class="ss-stat-val"><?php echo e(number_format($siswaBaruBulanIni ?? 0)); ?></h2>
            </div>
            <div class="ss-stat-bar">
                <div class="ss-stat-bar-fill red" style="width:100%"></div>
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
                                $avatarColors = ['#e53935','#2ea8ab','#c5352c','#9e9e9e','#1f2937'];
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
                                        <span class="ss-dot-wrapper">
                                            <i class="ss-dot"></i>
                                            <i class="ss-dot-pulse"></i>
                                        </span>
                                        <?php echo e($st['label']); ?>

                                    </span>
                                </td>

                                
                                <td class="ss-date">
                                    <i class="fa-regular fa-clock"></i> <?php echo e($s->created_at?->translatedFormat('d M Y') ?? '-'); ?>

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
    font-family: 'Montserrat', system-ui, sans-serif;
    color: #1f2937;
    animation: slideInUp 0.4s ease-out;
}

@keyframes slideInUp {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ── Header (Clean & Modern) ──────────────────────────────── */
.ss-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 24px;
}

.ss-breadcrumb-capsule {
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

.ss-header h1 {
    margin: 0 0 6px;
    font-size: 24px;
    font-weight: 900;
    letter-spacing: -0.03em;
    color: #111827;
}

.ss-header p {
    margin: 0;
    color: #6b7280;
    font-size: 13px;
    font-weight: 600;
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
}
.ss-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(229, 57, 53, 0.3);
    color: #fff;
}
.ss-btn-primary em {
    min-width: 20px;
    height: 20px;
    display: grid;
    place-items: center;
    background: #fff;
    color: #e53935;
    border-radius: 99px;
    font-size: 10px;
    font-style: normal;
    font-weight: 900;
}

.bounce-pulse {
    animation: bouncePulse 2s infinite;
}

@keyframes bouncePulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* ── Stat Cards (Tactile Hover & Glow) ────────────────────── */
.ss-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0,1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.ss-stat-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 18px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.01);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

/* Garis aksen tipis di bagian atas kartu stat */
.ss-stat-card::before {
    content: "";
    position: absolute;
    left: 0; right: 0; top: 0;
    height: 4px;
    transition: height 0.2s ease;
}

.card-teal::before { background: #2ea8ab; }
.card-green::before { background: #16a34a; }
.card-red::before { background: #e53935; }

.ss-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.03);
}
.card-teal:hover { border-color: #2ea8ab; box-shadow: 0 8px 24px rgba(46,168,171,0.08); }
.card-green:hover { border-color: #16a34a; box-shadow: 0 8px 24px rgba(22,163,74,0.08); }
.card-red:hover { border-color: #e53935; box-shadow: 0 8px 24px rgba(229,57,53,0.08); }

.ss-stat-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.ss-stat-icon {
    width: 38px;
    height: 38px;
    display: grid;
    place-items: center;
    border-radius: 10px;
    font-size: 15px;
    transition: transform 0.2s ease;
}
.ss-stat-card:hover .ss-stat-icon {
    transform: scale(1.1);
}

.ss-stat-icon.red    { background: rgba(229, 57, 53, 0.08); color: #e53935; }
.ss-stat-icon.green  { background: rgba(22, 163, 74, 0.08); color: #16a34a; }
.ss-stat-icon.teal   { background: rgba(46, 168, 171, 0.08); color: #2ea8ab; }

.ss-stat-badge {
    height: 20px;
    display: inline-flex;
    align-items: center;
    padding: 0 8px;
    border-radius: 6px;
    font-size: 9px;
    font-weight: 800;
}
.ss-stat-badge.green     { background: #e6f7ed; color: #15803d; }
.ss-stat-badge.blue      { background: #e0f2fe; color: #0369a1; }
.ss-stat-badge.red       { background: #fee2e2; color: #b91c1c; }
.ss-stat-badge.red-dark  { background: rgba(197, 53, 44, 0.1); color: #c5352c; }

.ss-stat-label {
    margin: 0 0 4px;
    font-size: 10px;
    font-weight: 800;
    color: #9e9e9e;
    text-transform: uppercase;
    letter-spacing: .06em;
}

.ss-stat-val {
    margin: 0 0 10px;
    font-size: 26px;
    font-weight: 900;
    letter-spacing: -.03em;
    color: #111827;
}

.ss-stat-bar {
    height: 4px;
    background: #f3f4f6;
    border-radius: 99px;
    overflow: hidden;
    margin-bottom: 6px;
}
.ss-stat-bar-fill {
    height: 100%;
    border-radius: 99px;
    transition: width .6s ease;
}
.ss-stat-bar-fill.red    { background: #e53935; }
.ss-stat-bar-fill.green  { background: #16a34a; }
.ss-stat-bar-fill.teal   { background: #2ea8ab; }

.ss-stat-sub {
    font-size: 10px;
    color: #9e9e9e;
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
    border-radius: 16px;
    padding: 18px;
    box-shadow: 0 2px 12px rgba(15,23,42,.01);
}

/* toolbar */
.ss-toolbar {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 16px;
}

.ss-search {
    position: relative;
    flex: 1;
    min-width: 200px;
}
.ss-search > i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9e9e9e;
    font-size: 12px;
    pointer-events: none;
}
.ss-search input {
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
.ss-search input:focus {
    background: #fff;
    border-color: #2ea8ab;
    box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12);
}

.ss-btn-search {
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
.ss-btn-search:hover { background: var(--spekta-red); }

/* table */
.ss-table-wrap { overflow-x: auto; border-radius: 12px; }

.ss-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 650px;
}

.ss-table thead tr {
    background: #f9fafb;
}

.ss-table th {
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

.ss-table td {
    padding: 12px 14px;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.ss-table tbody tr:last-child td { border-bottom: none; }

.ss-table tbody tr {
    transition: background-color 0.15s ease;
}
.ss-table tbody tr:hover {
    background: #fafbfc;
}

/* student cell */
.ss-student {
    display: flex;
    align-items: center;
    gap: 10px;
}

.ss-avatar {
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

.ss-student-info strong {
    display: block;
    color: #111827;
    font-size: 13px;
    font-weight: 800;
}

.ss-student-info span,
.ss-student-info small {
    display: block;
    color: #9e9e9e;
    font-size: 10px;
    font-weight: 600;
    margin-top: 1px;
}

/* class badge */
.ss-class-badge {
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

/* program name */
.ss-program-name {
    font-size: 11px;
    font-weight: 700;
    color: #4b5563;
}

/* status (Glow style) */
.ss-status {
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

.ss-dot-wrapper {
    position: relative;
    width: 5px;
    height: 5px;
    display: inline-block;
}

.ss-status .ss-dot {
    width: 5px;
    height: 5px;
    border-radius: 99px;
    background: currentColor;
    display: block;
    position: absolute;
    left: 0; top: 0;
}

.ss-status .ss-dot-pulse {
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

.ss-status.active     { background: #e6f7ed; color: #16a34a; box-shadow: 0 2px 6px rgba(22, 163, 74, 0.12); }
.ss-status.pending    { background: #fff7ed; color: #c2410c; }
.ss-status.expired    { background: #fee2e2; color: #dc2626; }
.ss-status.registered { background: #e0f2fe; color: #0269a1; }

/* date */
.ss-date {
    color: #6b7280;
    font-size: 11px;
    font-weight: 700;
}
.ss-date i {
    color: #9e9e9e;
    margin-right: 3px;
}

/* muted */
.ss-muted { color: #d1d5db; font-size: 14px; }

/* empty */
.ss-empty {
    padding: 36px 18px;
    text-align: center;
}
.ss-empty-icon {
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
.ss-empty strong {
    display: block;
    color: #111827;
    font-size: 14px;
    font-weight: 800;
    margin-bottom: 4px;
}
.ss-empty span {
    display: block;
    color: #9e9e9e;
    font-size: 12px;
    font-weight: 600;
}

/* pagination */
.ss-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid #f3f4f6;
    margin-top: 4px;
    flex-wrap: wrap;
}
.ss-pagination p {
    margin: 0;
    font-size: 11px;
    color: #6b7280;
    font-weight: 700;
}
.ss-pagination p strong { color: #111827; font-weight: 800; }

.ss-pages {
    display: flex;
    align-items: center;
    gap: 4px;
}
.ss-page-btn {
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
.ss-page-btn:hover:not(.disabled):not(.active) {
    border-color: #2ea8ab;
    color: #2ea8ab;
    background: rgba(46, 168, 171, 0.04);
}
.ss-page-btn.active {
    background: #1f2937;
    color: #fff;
    border-color: #1f2937;
    box-shadow: 0 3px 8px rgba(31, 41, 55, 0.2);
}
.ss-page-btn.disabled { opacity: .4; pointer-events: none; }
.ss-page-dots { color: #9ca3af; font-size: 12px; }

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width: 1280px) {
    .ss-stats { grid-template-columns: repeat(2,1fr); }
}

@media (max-width: 768px) {
    .ss-header { flex-direction: column; gap: 14px; }
    .ss-stats { grid-template-columns: 1fr; }
    .ss-toolbar { flex-direction: column; }
    .ss-pagination { flex-direction: column; align-items: flex-start; }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/siswa/index.blade.php ENDPATH**/ ?>