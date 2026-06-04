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

    
    <section class="tp-stats">

        <div class="tp-stat-card">
            <div class="tp-stat-top">
                <div class="tp-stat-icon red">
                    <i class="fa-solid fa-user-group"></i>
                </div>
                <span class="tp-stat-badge green">Live</span>
            </div>
            <p class="tp-stat-label">Total Pengajar</p>
            <h2 class="tp-stat-val"><?php echo e(number_format($totalPengajar ?? 0)); ?></h2>
            <div class="tp-stat-bar">
                <div class="tp-stat-bar-fill" style="width:100%"></div>
            </div>
            <small class="tp-stat-sub">data pengajar terdaftar</small>
        </div>

        <div class="tp-stat-card">
            <div class="tp-stat-top">
                <div class="tp-stat-icon green">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <span class="tp-stat-badge green">Aktif</span>
            </div>
            <p class="tp-stat-label">Pengajar Aktif</p>
            <h2 class="tp-stat-val"><?php echo e(number_format($pengajarAktif ?? 0)); ?></h2>
            <div class="tp-stat-bar">
                <div class="tp-stat-bar-fill green" style="width:<?php echo e($totalPengajar > 0 ? ($pengajarAktif / $totalPengajar) * 100 : 0); ?>%"></div>
            </div>
            <small class="tp-stat-sub">akun terverifikasi</small>
        </div>

        <div class="tp-stat-card">
            <div class="tp-stat-top">
                <div class="tp-stat-icon blue">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <span class="tp-stat-badge <?php echo e(($growthPengajar ?? 0) >= 0 ? 'green' : 'red'); ?>">
                    <?php echo e(($growthPengajar ?? 0) >= 0 ? '+' : ''); ?><?php echo e($growthPengajar ?? 0); ?>%
                </span>
            </div>
            <p class="tp-stat-label">Pengajar Baru Bulan Ini</p>
            <h2 class="tp-stat-val"><?php echo e(number_format($pengajarBaruBulanIni ?? 0)); ?></h2>
            <div class="tp-stat-bar">
                <div class="tp-stat-bar-fill blue" style="width:42%"></div>
            </div>
            <small class="tp-stat-sub">vs bulan lalu</small>
        </div>

        <div class="tp-stat-card">
            <div class="tp-stat-top">
                <div class="tp-stat-icon purple">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <span class="tp-stat-badge blue">Aktif</span>
            </div>
            <p class="tp-stat-label">Kelas Diajar</p>
            <h2 class="tp-stat-val"><?php echo e(number_format($kelasDiajar ?? 0)); ?></h2>
            <div class="tp-stat-bar">
                <div class="tp-stat-bar-fill purple" style="width:80%"></div>
            </div>
            <small class="tp-stat-sub">penugasan kelas</small>
        </div>

    </section>

    
    <section class="tp-main-grid">

        
        <div class="tp-table-panel">

            
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

                <div class="tp-select-wrap">
                    <i class="fa-solid fa-layer-group"></i>
                    <select name="subject_name" onchange="this.form.submit()">
                        <option value="">Semua Bidang</option>
                        <?php $__currentLoopData = $subjects ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($subject); ?>" <?php echo e(request('subject_name') === $subject ? 'selected' : ''); ?>>
                                <?php echo e($subject); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="tp-select-wrap">
                    <i class="fa-solid fa-filter"></i>
                    <select name="status" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Aktif</option>
                        <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Nonaktif</option>
                    </select>
                </div>

                <button type="submit" class="tp-btn-search">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>

                <button type="button" onclick="window.print()" class="tp-btn-export">
                    <i class="fa-solid fa-download"></i> Export
                </button>
            </form>

            
            <div class="tp-table-wrap">
                <table class="tp-table">
                    <thead>
                        <tr>
                            <th>Nama Pengajar</th>
                            <th>Bidang Ajar</th>
                            <th>Status</th>
                            <th>Kelas Aktif</th>
                            <th>Rating</th>
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

                                $mainSubject = $subjectList->take(2)->implode(', ');
                                $moreSubjectCount = max($subjectList->count() - 2, 0);
                                
                                $initial = strtoupper(substr($teacher->name, 0, 1));
                                $avatarColors = ['#D90429','#7C3AED','#0369A1','#15803D','#C2410C'];
                                $avatarBg = $avatarColors[crc32($teacher->name) % count($avatarColors)];
                                
                                // Random rating between 4.0 - 5.0 for demo (replace with actual rating data)
                                $rating = number_format(4.0 + (crc32($teacher->name) % 100) / 100, 1);
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

                                
                                <td>
                                    <div class="tp-rating">
                                        <span class="tp-rating-val"><?php echo e($rating); ?></span>
                                        <div class="tp-stars">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <?php if($i <= floor($rating)): ?>
                                                    <i class="fa-solid fa-star"></i>
                                                <?php elseif($i - 0.5 <= $rating): ?>
                                                    <i class="fa-solid fa-star-half-alt"></i>
                                                <?php else: ?>
                                                    <i class="fa-regular fa-star"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </td>

                                
                                <td class="tp-date">
                                    <?php echo e($teacher->created_at?->translatedFormat('d M Y') ?? '-'); ?>

                                </td>

                                
                                <td>
                                    <div class="tp-actions">
                                        <a href="<?php echo e(route('admin.assignments.index', ['teacher_id' => $teacher->usersID])); ?>" class="tp-act assignment" title="Penugasan materi">
                                            <i class="fa-solid fa-book-open"></i>
                                        </a>

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
                                <td colspan="7">
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

        
        <aside class="tp-side">

            
            <div class="tp-side-card">
                <div class="tp-side-card-head">
                    <h3>Distribusi Bidang Ajar</h3>
                    <span><?php echo e($totalDistribusiBidang ?? 0); ?> pengajar</span>
                </div>
                <div class="tp-program-list">
                    <?php $__empty_1 = true; $__currentLoopData = $distribusiBidang ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $bidang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $percentage = ($totalDistribusiBidang ?? 0) > 0
                                ? round(($bidang->total / $totalDistribusiBidang) * 100)
                                : 0;
                            $barColors = ['#D90429','#7C3AED','#0369A1','#15803D','#C2410C','#0EA5E9'];
                            $bc = $barColors[$index % count($barColors)];
                        ?>
                        <div class="tp-prog-row">
                            <div class="tp-prog-meta">
                                <span class="tp-prog-dot" style="background:<?php echo e($bc); ?>"></span>
                                <span class="tp-prog-name"><?php echo e($bidang->subject_name ?? $bidang['subject_name'] ?? 'Bidang Ajar'); ?></span>
                                <strong class="tp-prog-pct"><?php echo e($percentage); ?>%</strong>
                            </div>
                            <div class="tp-prog-track">
                                <div class="tp-prog-fill" style="width:<?php echo e($percentage); ?>%; background:<?php echo e($bc); ?>"></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="tp-side-empty">
                            <i class="fa-solid fa-chart-simple"></i>
                            <span>Belum ada data distribusi bidang ajar.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="tp-side-card">
                <div class="tp-side-card-head">
                    <h3>Aktivitas Terbaru</h3>
                </div>
                <div class="tp-activity-list">
                    <?php $__empty_1 = true; $__currentLoopData = $aktivitasTerbaru ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="tp-act-row">
                            <div class="tp-act-icon">
                                <i class="fa-solid <?php echo e($activity['icon'] ?? 'fa-clock'); ?>"></i>
                            </div>
                            <div class="tp-act-body">
                                <strong><?php echo e($activity['title']); ?></strong>
                                <span><?php echo e($activity['description']); ?></span>
                            </div>
                            <small class="tp-act-time"><?php echo e($activity['time']->diffForHumans()); ?></small>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="tp-side-empty">
                            <i class="fa-regular fa-clock"></i>
                            <span>Belum ada aktivitas terbaru.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </aside>

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
    margin-bottom: 18px;
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

/* ── Stat Cards ───────────────────────────────────────────── */
.tp-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0,1fr));
    gap: 18px;
    margin-bottom: 26px;
}

.tp-stat-card {
    background: #fff;
    border: 1px solid #edf0f4;
    border-radius: 22px;
    padding: 22px 20px 18px;
    box-shadow: 0 2px 12px rgba(15,23,42,.04);
    transition: box-shadow .2s, transform .2s;
}
.tp-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(15,23,42,.08);
}

.tp-stat-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.tp-stat-icon {
    width: 44px;
    height: 44px;
    display: grid;
    place-items: center;
    border-radius: 14px;
    font-size: 17px;
}
.tp-stat-icon.red    { background: #fff1f2; color: #d90429; }
.tp-stat-icon.green  { background: #dcfce7; color: #16a34a; }
.tp-stat-icon.blue   { background: #dbeafe; color: #2563eb; }
.tp-stat-icon.purple { background: #ede9fe; color: #7c3aed; }

.tp-stat-badge {
    height: 22px;
    display: inline-flex;
    align-items: center;
    padding: 0 9px;
    border-radius: 99px;
    font-size: 10px;
    font-weight: 800;
}
.tp-stat-badge.green { background: #dcfce7; color: #16a34a; }
.tp-stat-badge.blue  { background: #dbeafe; color: #2563eb; }
.tp-stat-badge.red   { background: #fee2e2; color: #dc2626; }

.tp-stat-label {
    margin: 0 0 4px;
    font-size: 11px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .06em;
}

.tp-stat-val {
    margin: 0 0 14px;
    font-size: 30px;
    font-weight: 900;
    letter-spacing: -.04em;
    color: #0f172a;
}

.tp-stat-bar {
    height: 4px;
    background: #f1f5f9;
    border-radius: 99px;
    overflow: hidden;
    margin-bottom: 8px;
}
.tp-stat-bar-fill {
    height: 100%;
    border-radius: 99px;
    background: #d90429;
    transition: width .6s ease;
}
.tp-stat-bar-fill.green  { background: #16a34a; }
.tp-stat-bar-fill.blue   { background: #2563eb; }
.tp-stat-bar-fill.purple { background: #7c3aed; }

.tp-stat-sub {
    font-size: 11px;
    color: #9ca3af;
    font-weight: 600;
}

/* ── Main Grid ────────────────────────────────────────────── */
.tp-main-grid {
    display: grid;
    grid-template-columns: minmax(0,1fr) 300px;
    gap: 22px;
    align-items: start;
    margin-bottom: 22px;
}

/* ── Table Panel ──────────────────────────────────────────── */
.tp-table-panel {
    background: #fff;
    border: 1px solid #edf0f4;
    border-radius: 22px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(15,23,42,.04);
}

/* toolbar */
.tp-toolbar {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 18px;
}

.tp-search {
    position: relative;
    flex: 1;
    min-width: 200px;
}
.tp-search > i {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 12px;
    pointer-events: none;
}
.tp-search input {
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
.tp-search input:focus {
    background: #fff;
    border-color: #fca5a5;
    box-shadow: 0 0 0 3px rgba(217,4,41,.08);
}

.tp-select-wrap {
    position: relative;
}
.tp-select-wrap > i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 11px;
    pointer-events: none;
    z-index: 1;
}
.tp-select-wrap select {
    height: 42px;
    padding: 0 14px 0 34px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #f8fafc;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    outline: none;
    cursor: pointer;
    appearance: none;
    transition: border-color .15s, box-shadow .15s;
}
.tp-select-wrap select:focus {
    background: #fff;
    border-color: #fca5a5;
    box-shadow: 0 0 0 3px rgba(217,4,41,.08);
}

.tp-btn-search {
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
.tp-btn-search:hover { background: #b80222; }

.tp-btn-export {
    height: 42px;
    padding: 0 16px;
    background: #fff;
    color: #374151;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
    font-family: inherit;
    transition: border-color .15s;
}
.tp-btn-export:hover { border-color: #d90429; color: #d90429; }

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

.tp-table td {
    padding: 14px;
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
    gap: 11px;
}

.tp-avatar {
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

.tp-teacher-info strong {
    display: block;
    color: #111827;
    font-size: 13px;
    font-weight: 800;
}

.tp-teacher-info span,
.tp-teacher-info small {
    display: block;
    color: #9ca3af;
    font-size: 10px;
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

/* rating */
.tp-rating {
    display: flex;
    align-items: center;
    gap: 6px;
}
.tp-rating-val {
    font-size: 12px;
    font-weight: 800;
    color: #374151;
}
.tp-stars {
    display: inline-flex;
    gap: 2px;
}
.tp-stars i {
    color: #f59e0b;
    font-size: 10px;
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
    transition: transform .12s;
}
.tp-act:hover { transform: scale(1.05); }
.tp-act.assignment { background: #fff1f2; color: #d90429; }
.tp-act.edit      { background: #dbeafe; color: #2563eb; }
.tp-act.delete    { background: #fee2e2; color: #dc2626; }

/* empty */
.tp-empty {
    padding: 40px 20px;
    text-align: center;
}
.tp-empty-icon {
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
.tp-empty strong {
    display: block;
    color: #111827;
    font-size: 15px;
    font-weight: 900;
    margin-bottom: 5px;
}
.tp-empty span {
    display: block;
    color: #9ca3af;
    font-size: 13px;
}

/* pagination */
.tp-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-top: 18px;
    border-top: 1px solid #f1f5f9;
    margin-top: 4px;
    flex-wrap: wrap;
}
.tp-pagination p {
    margin: 0;
    font-size: 12px;
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
.tp-page-dots { color: #9ca3af; font-size: 13px; }

/* ── Sidebar ──────────────────────────────────────────────── */
.tp-side { display: grid; gap: 18px; }

.tp-side-card {
    background: #fff;
    border: 1px solid #edf0f4;
    border-radius: 22px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(15,23,42,.04);
}

.tp-side-card-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}
.tp-side-card-head h3 {
    margin: 0;
    font-size: 14px;
    font-weight: 900;
    color: #0f172a;
}
.tp-side-card-head span {
    font-size: 11px;
    font-weight: 700;
    color: #9ca3af;
}

/* program list */
.tp-program-list { display: grid; gap: 14px; }

.tp-prog-row { display: grid; gap: 6px; }

.tp-prog-meta {
    display: flex;
    align-items: center;
    gap: 8px;
}

.tp-prog-dot {
    width: 9px;
    height: 9px;
    border-radius: 99px;
    flex-shrink: 0;
}

.tp-prog-name {
    flex: 1;
    font-size: 12px;
    font-weight: 700;
    color: #374151;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.tp-prog-pct {
    font-size: 13px;
    font-weight: 900;
    color: #111827;
    white-space: nowrap;
}

.tp-prog-track {
    height: 5px;
    background: #f1f5f9;
    border-radius: 99px;
    overflow: hidden;
}
.tp-prog-fill {
    height: 100%;
    border-radius: 99px;
    transition: width .6s ease;
}

/* activity list */
.tp-activity-list { display: grid; gap: 14px; }

.tp-act-row {
    display: grid;
    grid-template-columns: 36px minmax(0,1fr) auto;
    gap: 10px;
    align-items: center;
}

.tp-act-icon {
    width: 36px;
    height: 36px;
    display: grid;
    place-items: center;
    background: #fff1f2;
    color: #d90429;
    border-radius: 11px;
    font-size: 14px;
    flex-shrink: 0;
}

.tp-act-body strong {
    display: block;
    font-size: 12px;
    font-weight: 800;
    color: #111827;
}
.tp-act-body span {
    display: block;
    font-size: 11px;
    color: #6b7280;
    font-weight: 600;
    margin-top: 2px;
}

.tp-act-time {
    font-size: 10px;
    color: #9ca3af;
    font-weight: 700;
    white-space: nowrap;
}

.tp-side-empty {
    padding: 30px 20px;
    text-align: center;
    background: #f8fafc;
    border-radius: 12px;
    color: #9ca3af;
    font-size: 12px;
    font-weight: 600;
}
.tp-side-empty i {
    display: block;
    margin-bottom: 8px;
    font-size: 20px;
}

/* ── Responsive ───────────────────────────────────────────── */
@media (max-width: 1280px) {
    .tp-stats               { grid-template-columns: repeat(2,1fr); }
    .tp-main-grid           { grid-template-columns: 1fr; }
    .tp-side                { grid-template-columns: repeat(2,1fr); }
}

@media (max-width: 768px) {
    .tp-header              { flex-direction: column; gap: 14px; }
    .tp-stats               { grid-template-columns: 1fr; }
    .tp-side                { grid-template-columns: 1fr; }
    .tp-toolbar             { flex-direction: column; }
    .tp-select-wrap select  { width: 100%; }
    .tp-pagination          { flex-direction: column; align-items: flex-start; }
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\Specta_Academy\BackEnd\resources\views/admin/pengajar/index.blade.php ENDPATH**/ ?>