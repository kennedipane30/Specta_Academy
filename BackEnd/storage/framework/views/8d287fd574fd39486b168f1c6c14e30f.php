<?php $__env->startSection('title', 'Tutor Request Management'); ?>
<?php $__env->startSection('subtitle', 'Sistem Manajemen Permintaan Dedicated Tutor'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $tutorCollection = collect($tutors);

    $totalRequests = $tutorCollection->count();
    $pendingRequests = $tutorCollection->where('status', 'pending')->count();
    $confirmedRequests = $tutorCollection->where('status', 'confirmed')->count();
    $rejectedRequests = $tutorCollection->where('status', 'rejected')->count();

    $todayRequests = $tutorCollection->filter(function ($item) {
        return $item->date && \Carbon\Carbon::parse($item->date)->isToday();
    })->count();

    // Ambil semua pengajar menggunakan role_id = 2 (sesuai standar aplikasi Anda)
    $allTeachers = \App\Models\User::where('role_id', 2)->orderBy('name')->get();
?>

<div class="dt-page">

    
    <section class="dt-header">
        <div class="dt-header-left">
            <span class="dt-breadcrumb-capsule">Manajemen Akademik</span>
            <h1>Permintaan Dedicated Tutor</h1>
            <p>Kelola permintaan tutor privat siswa, tetapkan pengajar yang sesuai, dan pantau status konfirmasi secara real-time.</p>
        </div>
    </section>

    
    <section class="dt-summary">
        <!-- Card: Total -->
        <div class="dt-stat-card card-gray">
            <div class="dt-icon-box gray"><i class="fa-solid fa-layer-group"></i></div>
            <div class="dt-stat-info">
                <p>Total Request</p>
                <strong><?php echo e($totalRequests); ?></strong>
            </div>
            <span class="dt-card-badge gray"><?php echo e($todayRequests); ?> Hari Ini</span>
        </div>

        <!-- Card: Pending -->
        <div class="dt-stat-card card-orange">
            <div class="dt-icon-box orange">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div class="dt-stat-info">
                <p>Pending</p>
                <strong><?php echo e($pendingRequests); ?></strong>
            </div>
            <?php if($pendingRequests > 0): ?>
                <span class="dt-pulse-dot"></span>
            <?php endif; ?>
        </div>

        <!-- Card: Confirmed -->
        <div class="dt-stat-card card-teal">
            <div class="dt-icon-box teal"><i class="fa-solid fa-circle-check"></i></div>
            <div class="dt-stat-info">
                <p>Confirmed</p>
                <strong><?php echo e($confirmedRequests); ?></strong>
            </div>
        </div>

        <!-- Card: Rejected -->
        <div class="dt-stat-card card-red">
            <div class="dt-icon-box red"><i class="fa-solid fa-circle-xmark"></i></div>
            <div class="dt-stat-info">
                <p>Rejected</p>
                <strong><?php echo e($rejectedRequests); ?></strong>
            </div>
        </div>
    </section>

    
    <section class="dt-main-grid">
        <div class="dt-request-list">
            <?php $__empty_1 = true; $__currentLoopData = $tutors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $studentName = $t->student->user->name ?? 'N/A';
                    $topicTitle = $t->material->title ?? 'General Topic';
                    $subjectName = $t->material->material_name ?? $topicTitle;
                    $materialClassId = $t->material->class_id ?? null;

                    // Cari pengajar ahli berdasarkan relasi subject (material_name)
                    $qualifiedTeachers = collect();
                    if ($t->status === 'pending' && $materialClassId && $subjectName) {
                        $qualifiedTeachers = \App\Models\TeacherAssignment::whereHas('subject', function($q) use ($subjectName) {
                                $q->where('material_name', $subjectName);
                            })
                            ->where('class_id', $materialClassId)
                            ->with('teacher')
                            ->get();
                    }

                    $initial = strtoupper(substr($studentName, 0, 1));
                    $avatarColors = ['#e53935','#2ea8ab','#c5352c','#9e9e9e','#1f2937'];
                    $avatarBg = $avatarColors[crc32($studentName) % count($avatarColors)];
                ?>

                <article class="dt-card <?php echo e($t->status); ?>">
                    
                    <div class="dt-card-info">
                        <div class="dt-avatar" style="background: <?php echo e($avatarBg); ?>"><?php echo e($initial); ?></div>
                        <div class="dt-details">
                            <div class="dt-head">
                                <h3><?php echo e($studentName); ?></h3>
                                <span class="dt-badge <?php echo e($t->status); ?>">
                                    <span class="dt-dot-wrapper">
                                        <i class="dt-dot"></i>
                                        <?php if($t->status === 'pending'): ?>
                                            <i class="dt-dot-pulse"></i>
                                        <?php endif; ?>
                                    </span>
                                    <?php echo e(strtoupper($t->status)); ?>

                                </span>
                            </div>

                            <div class="dt-meta-row">
                                <div class="meta-item">
                                    <small>Topik Pembelajaran</small>
                                    <strong><?php echo e($subjectName); ?></strong>
                                </div>
                                <div class="meta-item">
                                    <small>Jadwal Diajukan</small>
                                    <strong><i class="fa-regular fa-clock"></i> <?php echo e(\Carbon\Carbon::parse($t->date)->translatedFormat('d M Y')); ?> • <?php echo e($t->time); ?> WIB</strong>
                                </div>
                                <div class="meta-item">
                                    <small>Guru Utama</small>
                                    <strong style="color: var(--spekta-teal);"><?php echo e($t->teacher->name ?? 'Belum Ditugaskan'); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="dt-card-action">
                        <?php if($t->status === 'pending'): ?>
                            <form action="<?php echo e(route('admin.tutor.update', $t->dedicated_tutor_id)); ?>" method="POST" class="dt-assign-form">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="status" value="confirmed">

                                <label>Tetapkan Pengajar</label>
                                <div class="select-wrapper">
                                    <select name="teacher_id" required>
                                        <option value="">Pilih pengajar...</option>
                                        <?php if($qualifiedTeachers->isNotEmpty()): ?>
                                            <optgroup label="Pengajar Ahli Materi Ini">
                                                <?php $__currentLoopData = $qualifiedTeachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($assign->teacher->usersID); ?>"><?php echo e($assign->teacher->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </optgroup>
                                        <?php endif; ?>
                                        <optgroup label="Semua Pengajar">
                                            <?php $__currentLoopData = $allTeachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($teacher->usersID); ?>"><?php echo e($teacher->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </optgroup>
                                    </select>
                                </div>
                                <button type="submit" class="btn-confirm">Konfirmasi Jadwal</button>
                            </form>
                        <?php else: ?>
                            <div class="dt-resolved-state <?php echo e($t->status); ?>">
                                <div class="resolved-icon">
                                    <i class="fa-solid <?php echo e($t->status === 'confirmed' ? 'fa-check' : 'fa-xmark'); ?>"></i>
                                </div>
                                <div class="resolved-text">
                                    <strong>Request <?php echo e(ucfirst($t->status)); ?></strong>
                                    <span>Guru: <?php echo e($t->teacher->name ?? '-'); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <!-- Tampilan Empty State yang Menarik -->
                <div class="dt-empty">
                    <div class="dt-empty-icon">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <strong>Belum ada permintaan Dedicated Tutor</strong>
                    <span>Permintaan privat siswa yang masuk melalui aplikasi siswa akan muncul di sini.</span>
                </div>
            <?php endif; ?>
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

    .dt-page {
        padding: 10px;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── Header Minimalis Modern ── */
    .dt-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }

    .dt-breadcrumb-capsule {
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

    .dt-header h1 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
        color: var(--text-main);
    }

    .dt-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }

    /* ── Stats Summary Strip ── */
    .dt-summary {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .dt-stat-card { 
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
    .dt-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.03);
    }

    .dt-icon-box { width: 42px; height: 42px; border-radius: 10px; display: grid; place-items: center; font-size: 16px; }
    .dt-icon-box.gray { background: var(--spekta-gray-light); color: var(--text-muted); }
    .dt-icon-box.orange { background: rgba(217, 119, 6, 0.08); color: #d97706; }
    .dt-icon-box.teal { background: var(--spekta-teal-light); color: var(--spekta-teal); }
    .dt-icon-box.red { background: var(--spekta-red-light); color: var(--spekta-red); }

    .dt-stat-info p { margin: 0; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; }
    .dt-stat-info strong { font-size: 22px; font-weight: 900; color: var(--text-main); display: block; }

    .dt-card-badge {
        position: absolute;
        top: 12px; right: 12px;
        font-size: 9px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 4px;
    }
    .dt-card-badge.gray { background: var(--spekta-gray-light); color: var(--text-muted); }

    /* Indikator Denyut Pending */
    .dt-pulse-dot {
        position: absolute;
        top: 14px; right: 14px;
        width: 6px; height: 6px;
        background: #d97706;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7);
        animation: pulseOrange 1.5s infinite;
    }
    @keyframes pulseOrange {
        0% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(217, 119, 6, 0); }
        100% { box-shadow: 0 0 0 0 rgba(217, 119, 6, 0); }
    }

    /* ── Request List & Cards ── */
    .dt-request-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    
    .dt-card {
        background: var(--spekta-white);
        border-radius: 16px;
        border: 1px solid var(--border-soft);
        display: flex;
        justify-content: space-between;
        align-items: stretch;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.01);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .dt-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.04);
        border-color: var(--spekta-gray);
    }

    /* Kiri: Info Permintaan */
    .dt-card-info {
        padding: 20px;
        display: flex;
        gap: 16px;
        flex: 1;
    }
    .dt-avatar {
        width: 48px;
        height: 48px;
        border-radius: 99px;
        display: grid;
        place-items: center;
        font-size: 16px;
        font-weight: 900;
        color: var(--spekta-white);
        flex-shrink: 0;
        box-shadow: 0 3px 8px rgba(0,0,0,0.06);
    }
    .dt-details {
        flex: 1;
    }
    .dt-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }
    .dt-head h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
    }
    
    /* Neon Status Badges */
    .dt-badge {
        font-size: 9px;
        padding: 3px 8px;
        border-radius: 6px;
        font-weight: 800;
        letter-spacing: 0.04em;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .dt-dot-wrapper {
        position: relative;
        width: 5px; height: 5px;
        display: inline-block;
    }
    .dt-dot {
        width: 5px; height: 5px;
        border-radius: 99px;
        background: currentColor;
        display: block;
        position: absolute;
        left: 0; top: 0;
    }
    .dt-dot-pulse {
        width: 5px; height: 5px;
        border-radius: 99px;
        background: currentColor;
        display: block;
        position: absolute;
        left: 0; top: 0;
        opacity: 0.4;
        transform: scale(1);
        animation: dotGlow 1.8s infinite ease-in-out;
    }
    @keyframes dotGlow {
        0% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(3.2); opacity: 0; }
    }

    .dt-badge.pending { background: #fff7ed; color: #c2410c; border: 1px solid #fde68a;}
    .dt-badge.confirmed { background: #e6f7ed; color: #15803d; border: 1px solid #a7f3d0;}
    .dt-badge.rejected { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca;}

    .dt-meta-row {
        display: flex;
        gap: 32px;
        flex-wrap: wrap;
    }
    .meta-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .meta-item small {
        font-size: 10px;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 800;
        letter-spacing: 0.02em;
    }
    .meta-item strong {
        font-size: 12px;
        color: var(--text-main);
        font-weight: 700;
    }

    /* Kanan: Panel Aksi Penetapan Guru */
    .dt-card-action {
        width: 280px;
        background: #f9fafb;
        border-left: 1px solid var(--border-soft);
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Form Dropdown */
    .dt-assign-form label {
        display: block;
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .select-wrapper select {
        width: 100%;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid var(--border-soft);
        background: var(--spekta-white);
        font-size: 12px;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 10px;
        outline: none;
        transition: all 0.2s ease;
    }
    .select-wrapper select:focus {
        border-color: var(--spekta-teal);
        box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12);
    }
    .btn-confirm {
        width: 100%;
        background: var(--spekta-teal);
        color: var(--spekta-white);
        border: none;
        padding: 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(46, 168, 171, 0.15);
    }
    .btn-confirm:hover { 
        background: #1e878a; 
        transform: translateY(-1px);
    }

    /* Resolved State (Selesai diproses) */
    .dt-resolved-state {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .resolved-icon {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 12px;
    }
    .dt-resolved-state.confirmed .resolved-icon { background: #e6f7ed; color: #15803d; }
    .dt-resolved-state.rejected .resolved-icon { background: #fee2e2; color: #dc2626; }

    .resolved-text {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }
    .resolved-text strong { font-size: 13px; color: var(--text-main); font-weight: 800; }
    .resolved-text span { font-size: 11px; color: var(--text-muted); font-weight: 600;}

    /* Empty State */
    .dt-empty {
        text-align: center;
        padding: 48px;
        background: var(--spekta-white);
        border-radius: 16px;
        border: 1px dashed var(--border-soft);
    }
    .dt-empty-icon {
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        margin: 0 auto 12px;
        background: var(--spekta-gray-light);
        color: var(--spekta-gray);
        border-radius: 99px;
        font-size: 18px;
    }
    .dt-empty strong { display: block; color: var(--text-main); font-size: 14px; font-weight: 800; margin-bottom: 4px; }
    .dt-empty span { display: block; color: var(--text-muted); font-size: 12px; font-weight: 600; }

    /* RESPONSIVE LAYOUT */
    @media (max-width: 1024px) {
        .dt-summary { grid-template-columns: repeat(2, 1fr); }
        .dt-card { flex-direction: column; }
        .dt-card-action { width: 100%; border-left: none; border-top: 1px solid var(--border-soft); }
    }
    @media (max-width: 640px) {
        .dt-summary { grid-template-columns: 1fr; }
        .dt-meta-row { flex-direction: column; gap: 12px; }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\perkuliahan\PA 2 - code\PAAAAA2\BackEnd\resources\views/admin/dedicated_tutor/index.blade.php ENDPATH**/ ?>