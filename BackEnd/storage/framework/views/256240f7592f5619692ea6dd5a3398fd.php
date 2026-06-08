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

    // MODIFIKASI: Ambil semua pengajar menggunakan role_id = 2 (sesuai standar aplikasi Anda)
    $allTeachers = \App\Models\User::where('role_id', 2)->orderBy('name')->get();
?>

<div class="dt-page">

    
    <section class="dt-hero">
        <div class="dt-hero-content">
            <div class="dt-kicker">
                <i class="fa-solid fa-headset"></i>
                <span>Dedicated Tutor Center</span>
            </div>
            <h1>Tutor Request Management</h1>
            <p>Kelola permintaan tutor privat siswa, tetapkan pengajar yang sesuai, dan pantau status konfirmasi.</p>
            <div class="dt-hero-tags">
                <span class="tag-pending"><i class="fa-solid fa-clock"></i> <?php echo e($pendingRequests); ?> Pending</span>
                <span class="tag-confirmed"><i class="fa-solid fa-circle-check"></i> <?php echo e($confirmedRequests); ?> Confirmed</span>
                <span class="tag-today"><i class="fa-solid fa-calendar-day"></i> <?php echo e($todayRequests); ?> Hari Ini</span>
            </div>
        </div>
        <div class="dt-hero-panel">
            <div class="dt-ring">
                <strong><?php echo e($totalRequests); ?></strong>
                <span>Total Request</span>
            </div>
        </div>
    </section>

    
    <section class="dt-summary">
        <div class="summary-card">
            <span>Total</span>
            <strong class="text-dark"><?php echo e($totalRequests); ?></strong>
        </div>
        <div class="summary-card">
            <span>Pending</span>
            <strong class="text-warning"><?php echo e($pendingRequests); ?></strong>
        </div>
        <div class="summary-card">
            <span>Confirmed</span>
            <strong class="text-success"><?php echo e($confirmedRequests); ?></strong>
        </div>
        <div class="summary-card">
            <span>Rejected</span>
            <strong class="text-danger"><?php echo e($rejectedRequests); ?></strong>
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

                    // MODIFIKASI: Cari pengajar ahli berdasarkan relasi subject (material_name)
                    $qualifiedTeachers = collect();
                    if ($t->status === 'pending' && $materialClassId && $subjectName) {
                        $qualifiedTeachers = \App\Models\TeacherAssignment::whereHas('subject', function($q) use ($subjectName) {
                                $q->where('material_name', $subjectName);
                            })
                            ->where('class_id', $materialClassId)
                            ->with('teacher')
                            ->get();
                    }
                ?>

                <article class="dt-card <?php echo e($t->status); ?>">
                    
                    <div class="dt-card-info">
                        <div class="dt-avatar"><?php echo e(strtoupper(substr($studentName, 0, 1))); ?></div>
                        <div class="dt-details">
                            <div class="dt-head">
                                <h3><?php echo e($studentName); ?></h3>
                                <span class="dt-badge <?php echo e($t->status); ?>"><?php echo e(strtoupper($t->status)); ?></span>
                            </div>

                            <div class="dt-meta-row">
                                <div class="meta-item">
                                    <small>Topik Pembelajaran</small>
                                    <strong><?php echo e($subjectName); ?></strong>
                                </div>
                                <div class="meta-item">
                                    <small>Jadwal Diajukan</small>
                                    <strong><?php echo e(\Carbon\Carbon::parse($t->date)->translatedFormat('d M Y')); ?> • <?php echo e($t->time); ?> WIB</strong>
                                </div>
                                <div class="meta-item">
                                    <small>Guru (Saat ini)</small>
                                    <strong><?php echo e($t->teacher->name ?? 'Belum Ditugaskan'); ?></strong>
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
                <div class="dt-empty">
                    <i class="fa-solid fa-inbox"></i>
                    <p>Belum ada permintaan Dedicated Tutor saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<style>
    /* BASE SETUP */
    .dt-page {
        padding: 0;
        font-family: 'Inter', system-ui, sans-serif;
        color: #334155;
    }

    /* HERO SECTION */
    .dt-hero {
        background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
        border-radius: 24px;
        padding: 40px;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px -5px rgba(185, 28, 28, 0.3);
    }
    .dt-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,0.15);
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 16px;
    }
    .dt-hero h1 {
        font-size: 32px;
        margin: 0 0 8px 0;
        font-weight: 800;
        letter-spacing: -0.02em;
    }
    .dt-hero p {
        margin: 0 0 20px 0;
        font-size: 14px;
        opacity: 0.9;
        max-width: 600px;
        line-height: 1.5;
    }
    .dt-hero-tags {
        display: flex;
        gap: 12px;
    }
    .dt-hero-tags span {
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .tag-pending { background: rgba(245, 158, 11, 0.2); color: #fcd34d; }
    .tag-confirmed { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; }
    .tag-today { background: rgba(255, 255, 255, 0.15); color: #fff; }

    .dt-ring {
        background: #fff;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        box-shadow: 0 0 0 8px rgba(255,255,255,0.1);
    }
    .dt-ring strong { font-size: 36px; font-weight: 900; color: #b91c1c; line-height: 1; }
    .dt-ring span { font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-top: 4px;}

    /* SUMMARY STRIP */
    .dt-summary {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    .summary-card {
        background: #fff;
        padding: 24px;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        text-align: left;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .summary-card span { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;}
    .summary-card strong { font-size: 32px; font-weight: 800; line-height: 1;}
    .text-dark { color: #0f172a; }
    .text-warning { color: #d97706; }
    .text-success { color: #059669; }
    .text-danger { color: #dc2626; }

    /* REQUEST LIST & CARDS */
    .dt-request-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .dt-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: stretch;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .dt-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }

    /* Kiri: Info */
    .dt-card-info {
        padding: 24px;
        display: flex;
        gap: 20px;
        flex: 1;
    }
    .dt-avatar {
        width: 56px;
        height: 56px;
        background: #fef2f2;
        color: #b91c1c;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 20px;
        font-weight: 800;
        flex-shrink: 0;
    }
    .dt-details {
        flex: 1;
    }
    .dt-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    .dt-head h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
    }
    .dt-badge {
        font-size: 10px;
        padding: 4px 10px;
        border-radius: 12px;
        font-weight: 700;
        letter-spacing: 0.05em;
    }
    .dt-badge.pending { background: #fffbeb; color: #b45309; border: 1px solid #fde68a;}
    .dt-badge.confirmed { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;}
    .dt-badge.rejected { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca;}

    .dt-meta-row {
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
    }
    .meta-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .meta-item small {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.02em;
    }
    .meta-item strong {
        font-size: 14px;
        color: #334155;
        font-weight: 600;
    }

    /* Kanan: Action Area */
    .dt-card-action {
        width: 300px;
        background: #f8fafc;
        border-left: 1px solid #e2e8f0;
        padding: 24px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Form Mode (Pending) */
    .dt-assign-form label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 8px;
    }
    .select-wrapper select {
        width: 100%;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        background: #fff;
        font-size: 13px;
        font-weight: 500;
        color: #0f172a;
        margin-bottom: 12px;
        outline: none;
        transition: border-color 0.2s;
    }
    .select-wrapper select:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    .btn-confirm {
        width: 100%;
        background: #059669;
        color: #fff;
        border: none;
        padding: 12px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-confirm:hover { background: #047857; }

    /* Resolved State (Confirmed/Rejected) */
    .dt-resolved-state {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .resolved-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 16px;
    }
    .dt-resolved-state.confirmed .resolved-icon { background: #dcfce7; color: #059669; }
    .dt-resolved-state.rejected .resolved-icon { background: #fee2e2; color: #dc2626; }

    .resolved-text {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .resolved-text strong { font-size: 14px; color: #0f172a; }
    .resolved-text span { font-size: 12px; color: #64748b; font-weight: 500;}

    /* Empty State */
    .dt-empty {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 20px;
        border: 1px dashed #cbd5e1;
        color: #94a3b8;
    }
    .dt-empty i { font-size: 48px; margin-bottom: 16px; color: #e2e8f0; }
    .dt-empty p { font-size: 15px; font-weight: 500; margin: 0;}

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .dt-hero { flex-direction: column; align-items: flex-start; gap: 24px; }
        .dt-ring { display: none; } /* Hide ring on smaller screens */
        .dt-summary { grid-template-columns: repeat(2, 1fr); }
        .dt-card { flex-direction: column; }
        .dt-card-action { width: 100%; border-left: none; border-top: 1px solid #e2e8f0; }
    }
    @media (max-width: 640px) {
        .dt-summary { grid-template-columns: 1fr; }
        .dt-meta-row { flex-direction: column; gap: 16px; }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/dedicated_tutor/index.blade.php ENDPATH**/ ?>