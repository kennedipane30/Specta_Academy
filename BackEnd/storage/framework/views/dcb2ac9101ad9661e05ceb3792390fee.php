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

    $latestRequests = $tutorCollection->sortByDesc('created_at')->take(5);

    // ✨ AMBIL SEMUA PENGAJAR SEBAGAI CADANGAN
    $allTeachers = \App\Models\User::whereHas('role', function($q) {
        $q->where('role_name', 'pengajar');
    })->get();
?>

<div class="dt-page">

    
    <section class="dt-hero">
        <div class="dt-hero-content">
            <div class="dt-kicker">
                <i class="fa-solid fa-headset"></i>
                <span>Dedicated Tutor Center</span>
            </div>

            <h1>Tutor Request Management</h1>

            <p>
                Kelola permintaan tutor privat siswa, tetapkan pengajar yang sesuai dengan materi,
                dan pantau status konfirmasi secara terstruktur.
            </p>

            <div class="dt-hero-tags">
                <span>
                    <i class="fa-solid fa-clock"></i>
                    <?php echo e(number_format($pendingRequests)); ?> Pending
                </span>

                <span>
                    <i class="fa-solid fa-circle-check"></i>
                    <?php echo e(number_format($confirmedRequests)); ?> Confirmed
                </span>

                <span>
                    <i class="fa-solid fa-calendar-day"></i>
                    <?php echo e(number_format($todayRequests)); ?> Hari Ini
                </span>
            </div>
        </div>

        <div class="dt-hero-panel">
            <div class="dt-ring">
                <strong><?php echo e(number_format($totalRequests)); ?></strong>
                <span>Total Request</span>
            </div>

            <div class="dt-ring-info">
                <strong><?php echo e(number_format($pendingRequests)); ?></strong>
                <span>menunggu tindakan admin</span>
            </div>
        </div>
    </section>

    
    <?php if(session('success')): ?>
        <div class="dt-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <div>
                <strong>Berhasil!</strong>
                <span><?php echo e(session('success')); ?></span>
            </div>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="dt-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Data belum valid.</strong>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    
    <section class="dt-summary">
        <div>
            <span>Total Request</span>
            <strong><?php echo e(number_format($totalRequests)); ?></strong>
            <small>semua permintaan</small>
        </div>

        <div>
            <span>Pending</span>
            <strong><?php echo e(number_format($pendingRequests)); ?></strong>
            <small>menunggu konfirmasi</small>
        </div>

        <div>
            <span>Confirmed</span>
            <strong><?php echo e(number_format($confirmedRequests)); ?></strong>
            <small>sudah ditetapkan</small>
        </div>

        <div>
            <span>Rejected</span>
            <strong><?php echo e(number_format($rejectedRequests)); ?></strong>
            <small>ditolak admin</small>
        </div>
    </section>

    
    <section class="dt-main-grid">

        
        <div class="dt-request-panel">
            <div class="dt-section-title">
                <div>
                    <span>Request Queue</span>
                    <h2>Dedicated Tutor Requests</h2>
                    <p>Daftar permintaan tutor privat dari siswa yang masuk melalui aplikasi mobile.</p>
                </div>
            </div>

            <div class="dt-request-list">
                <?php $__empty_1 = true; $__currentLoopData = $tutors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $studentName = $t->student->user->name ?? 'N/A';
                        $studentInitial = strtoupper(substr($studentName, 0, 1));
                        $nisn = $t->student->national_id_number ?? '-';

                        $topicTitle = $t->material->title ?? 'General Topic';
                        $subjectName = $t->material->material_name ?? $topicTitle;
                        $materialClassId = $t->material->class_id ?? null;

                        $formattedDate = $t->date ? \Carbon\Carbon::parse($t->date)->translatedFormat('d M Y') : '-';
                        $formattedDay = $t->date ? \Carbon\Carbon::parse($t->date)->translatedFormat('l') : '-';
                        $formattedTime = $t->time ? substr($t->time, 0, 5) : '-';

                        $statusClass = $t->status;
                        $statusLabel = strtoupper($t->status);

                        // Cari pengajar yang ditugaskan khusus untuk materi ini
                        $qualifiedTeachers = collect();
                        if ($t->status === 'pending' && $materialClassId && $subjectName) {
                            $qualifiedTeachers = \App\Models\TeacherAssignment::where('subject_name', $subjectName)
                                ->where('class_id', $materialClassId)
                                ->with('user')
                                ->get();
                        }
                    ?>

                    <article class="dt-request-card <?php echo e($statusClass); ?>">
                        <div class="dt-request-main">
                            <div class="dt-student-avatar">
                                <?php echo e($studentInitial); ?>

                            </div>

                            <div class="dt-request-info">
                                <div class="dt-request-head">
                                    <div>
                                        <h3><?php echo e($studentName); ?></h3>
                                        <span>NISN: <?php echo e($nisn); ?></span>
                                    </div>

                                    <span class="dt-status <?php echo e($statusClass); ?>">
                                        <?php echo e($statusLabel); ?>

                                    </span>
                                </div>

                                <div class="dt-info-grid">
                                    <div>
                                        <small>Topik</small>
                                        <strong><?php echo e($topicTitle); ?></strong>
                                        <span><?php echo e($subjectName); ?></span>
                                    </div>

                                    <div>
                                        <small>Jadwal</small>
                                        <strong><?php echo e($formattedDate); ?></strong>
                                        <span><?php echo e($formattedDay); ?>, <?php echo e($formattedTime); ?> WIB</span>
                                    </div>

                                    <div>
                                        <small>Pengajar</small>
                                        <strong><?php echo e($t->teacher->name ?? 'Belum ditetapkan'); ?></strong>
                                        <span><?php echo e($t->status === 'pending' ? 'Menunggu penugasan' : 'Penugasan selesai'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dt-action-area">
                            <?php if($t->status === 'pending'): ?>
                                <form action="<?php echo e(route('admin.tutor.update', $t->dedicated_tutor_id)); ?>" method="POST" class="dt-approve-form" id="form-approve-<?php echo e($t->dedicated_tutor_id); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="status" value="confirmed">

                                    <label>Assign Teacher</label>
                                    <select name="teacher_id" required>
                                        <option value="">Pilih pengajar...</option>

                                        <?php if($qualifiedTeachers->isNotEmpty()): ?>
                                            <optgroup label="Pengajar Ahli Materi Ini">
                                                <?php $__currentLoopData = $qualifiedTeachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($assign->user): ?>
                                                        <option value="<?php echo e($assign->user->usersID); ?>">
                                                            <?php echo e($assign->user->name); ?>

                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </optgroup>
                                        <?php endif; ?>

                                        <optgroup label="Semua Pengajar (Cadangan)">
                                            <?php $__currentLoopData = $allTeachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($teacher->usersID); ?>"><?php echo e($teacher->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </optgroup>
                                    </select>
                                </form>

                                <div class="dt-action-buttons">
                                    
                                    <button type="submit" form="form-approve-<?php echo e($t->dedicated_tutor_id); ?>" class="dt-confirm-btn">
                                        <i class="fa-solid fa-check"></i>
                                        Confirm
                                    </button>

                                    <form action="<?php echo e(route('admin.tutor.update', $t->dedicated_tutor_id)); ?>" method="POST" onsubmit="return confirm('Tolak permintaan tutor ini?')">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="dt-reject-btn">
                                            <i class="fa-solid fa-xmark"></i>
                                            Reject
                                        </button>
                                    </form>
                                </div>

                                <?php if($qualifiedTeachers->isEmpty()): ?>
                                    <div class="dt-warning">
                                        <i class="fa-solid fa-info-circle"></i>
                                        Belum ada pengajar khusus untuk materi ini. Silakan pilih dari daftar cadangan.
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="dt-final-state <?php echo e($t->status); ?>">
                                    <?php if($t->status === 'confirmed'): ?>
                                        <i class="fa-solid fa-circle-check"></i>
                                        <div>
                                            <strong>Request confirmed</strong>
                                            <span>Pengajar: <?php echo e($t->teacher->name ?? '-'); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <i class="fa-solid fa-circle-xmark"></i>
                                        <div>
                                            <strong>Request rejected</strong>
                                            <span>Permintaan telah ditolak.</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="dt-empty">
                        <i class="fa-solid fa-headset"></i>
                        <strong>Belum ada permintaan dedicated tutor.</strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        
        <aside class="dt-side-panel">
             
        </aside>

    </section>
</div>

<style>
    .dt-page {
        width: 100%;
    }

    .dt-hero {
        position: relative;
        overflow: hidden;
        background: linear-gradient(120deg, #cf002b 0%, #87001e 50%, #182033 100%);
        color: #fff;
        border-radius: 24px;
        padding: 34px 36px;
        margin-bottom: 22px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 250px;
        align-items: center;
        gap: 28px;
        box-shadow: 0 18px 38px rgba(134, 0, 24, .22);
    }

    .dt-hero::before {
        content: "";
        position: absolute;
        width: 340px;
        height: 340px;
        right: -140px;
        top: -155px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .10);
    }

    .dt-hero::after {
        content: "";
        position: absolute;
        width: 230px;
        height: 230px;
        right: 74px;
        bottom: -150px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .07);
    }

    .dt-hero-content,
    .dt-hero-panel {
        position: relative;
        z-index: 2;
    }

    .dt-kicker {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        min-height: 28px;
        padding: 0 11px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .16);
        margin-bottom: 16px;
    }

    .dt-kicker i {
        font-size: 11px;
    }

    .dt-kicker span {
        color: rgba(255, 255, 255, .88);
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .dt-hero h1 {
        margin: 0 0 10px;
        color: #fff;
        font-size: 34px;
        font-weight: 900;
        line-height: 1.05;
        text-transform: uppercase;
        letter-spacing: -0.045em;
    }

    .dt-hero p {
        margin: 0;
        max-width: 780px;
        color: rgba(255, 255, 255, .88);
        font-size: 14px;
        font-weight: 600;
        line-height: 1.65;
    }

    .dt-hero-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 22px;
    }

    .dt-hero-tags span {
        min-height: 34px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0 13px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .14);
        color: #fff;
        font-size: 11px;
        font-weight: 900;
    }

    .dt-hero-panel {
        justify-self: end;
        width: 230px;
        min-height: 180px;
        border-radius: 24px;
        padding: 22px;
        background: rgba(255, 255, 255, .14);
        border: 1px solid rgba(255, 255, 255, .18);
        backdrop-filter: blur(14px);
        display: grid;
        place-items: center;
        text-align: center;
    }

    .dt-ring {
        width: 112px;
        height: 112px;
        border-radius: 999px;
        background: #fff;
        color: #111827;
        display: grid;
        place-items: center;
        align-content: center;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .18);
    }

    .dt-ring strong {
        display: block;
        font-size: 28px;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .dt-ring span {
        display: block;
        color: #6b7280;
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
        margin-top: 6px;
    }

    .dt-ring-info {
        margin-top: 13px;
    }

    .dt-ring-info strong {
        color: #fff;
        font-size: 17px;
        font-weight: 900;
    }

    .dt-ring-info span {
        display: block;
        margin-top: 4px;
        color: rgba(255, 255, 255, .78);
        font-size: 11px;
        font-weight: 700;
    }

    .dt-alert {
        border-radius: 16px;
        padding: 15px 17px;
        margin-bottom: 18px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 800;
    }

    .dt-alert.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .dt-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .dt-alert strong {
        display: block;
        margin-bottom: 3px;
    }

    .dt-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .dt-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        background: #fff;
        border: 1px solid #edf0f4;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 22px;
    }

    .dt-summary div {
        padding: 20px 22px;
        border-right: 1px solid #edf0f4;
    }

    .dt-summary div:last-child {
        border-right: none;
    }

    .dt-summary span {
        display: block;
        color: #6b7280;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 8px;
    }

    .dt-summary strong {
        display: block;
        color: #111827;
        font-size: 28px;
        font-weight: 900;
        letter-spacing: -0.04em;
        line-height: 1;
    }

    .dt-summary small {
        display: block;
        color: #9ca3af;
        font-size: 11px;
        font-weight: 700;
        margin-top: 7px;
    }

    .dt-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 350px;
        gap: 22px;
        align-items: start;
    }

    .dt-request-panel,
    .dt-side-card {
        background: #fff;
        border: 1px solid #edf0f4;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
        border-radius: 22px;
        padding: 22px;
    }

    .dt-section-title {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 20px;
    }

    .dt-section-title span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .dt-section-title h2,
    .dt-side-card h3 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .dt-section-title p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
    }

    .dt-request-list {
        display: grid;
        gap: 16px;
    }

    .dt-request-card {
        border: 1px solid #edf0f4;
        border-radius: 20px;
        padding: 18px;
        background: #fff;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 18px;
        transition: .2s ease;
    }

    .dt-request-card:hover {
        border-color: #fecdd3;
        box-shadow: 0 16px 35px rgba(15, 23, 42, .07);
        transform: translateY(-2px);
    }

    .dt-request-card.pending {
        border-left: 5px solid #f59e0b;
    }

    .dt-request-card.confirmed {
        border-left: 5px solid #16a34a;
    }

    .dt-request-card.rejected {
        border-left: 5px solid #dc2626;
    }

    .dt-request-main {
        display: flex;
        gap: 14px;
        min-width: 0;
    }

    .dt-student-avatar {
        width: 48px;
        height: 48px;
        flex-shrink: 0;
        border-radius: 16px;
        background: #ffe8ee;
        color: #d90429;
        display: grid;
        place-items: center;
        font-weight: 900;
        font-size: 16px;
    }

    .dt-request-info {
        min-width: 0;
        flex: 1;
    }

    .dt-request-head {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 14px;
    }

    .dt-request-head h3 {
        margin: 0;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
    }

    .dt-request-head span {
        display: block;
        margin-top: 4px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
    }

    .dt-status {
        height: 28px;
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 0 10px;
        font-size: 10px !important;
        font-weight: 900 !important;
        text-transform: uppercase;
        margin-top: 0 !important;
        flex-shrink: 0;
    }

    .dt-status.pending {
        background: #fef3c7;
        color: #d97706;
    }

    .dt-status.confirmed {
        background: #dcfce7;
        color: #16a34a;
    }

    .dt-status.rejected {
        background: #fee2e2;
        color: #dc2626;
    }

    .dt-info-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .dt-info-grid div {
        background: #f8fafc;
        border: 1px solid #edf0f4;
        border-radius: 15px;
        padding: 13px;
        min-width: 0;
    }

    .dt-info-grid small {
        display: block;
        color: #9ca3af;
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .07em;
        margin-bottom: 5px;
    }

    .dt-info-grid strong {
        display: block;
        color: #111827;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dt-info-grid span {
        display: block;
        color: #6b7280;
        font-size: 10px;
        font-weight: 700;
        margin-top: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dt-action-area {
        border-left: 1px solid #edf0f4;
        padding-left: 18px;
        display: grid;
        align-content: center;
        gap: 12px;
    }

    .dt-approve-form label {
        display: block;
        color: #374151;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 8px;
    }

    .dt-approve-form select {
        width: 100%;
        height: 44px;
        border: 1px solid #e5e7eb;
        border-radius: 13px;
        background: #f8fafc;
        padding: 0 13px;
        color: #111827;
        font-size: 12px;
        font-weight: 800;
        outline: none;
        font-family: inherit;
    }

    .dt-approve-form select:focus {
        background: #fff;
        border-color: #fecdd3;
        box-shadow: 0 0 0 4px rgba(217, 4, 41, .08);
    }

    .dt-action-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 9px;
    }

    .dt-action-buttons form {
        margin: 0;
    }

    .dt-confirm-btn,
    .dt-reject-btn {
        width: 100%;
        height: 40px;
        border: none;
        border-radius: 12px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-weight: 900;
        cursor: pointer;
        font-family: inherit;
        text-transform: uppercase;
    }

    .dt-confirm-btn {
        background: #16a34a;
        color: #fff;
    }

    .dt-confirm-btn:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    .dt-reject-btn {
        background: #fee2e2;
        color: #dc2626;
    }

    .dt-warning {
        border-radius: 13px;
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
        padding: 11px 12px;
        font-size: 10px;
        font-weight: 800;
        line-height: 1.45;
        display: flex;
        gap: 8px;
    }

    .dt-final-state {
        min-height: 90px;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px;
        border-radius: 16px;
    }

    .dt-final-state.confirmed {
        background: #f0fdf4;
        color: #166534;
    }

    .dt-final-state.rejected {
        background: #fef2f2;
        color: #b91c1c;
    }

    .dt-final-state i {
        font-size: 20px;
    }

    .dt-final-state strong {
        display: block;
        font-size: 13px;
        font-weight: 900;
    }

    .dt-final-state span {
        display: block;
        font-size: 11px;
        font-weight: 700;
        margin-top: 4px;
    }

    .dt-side-panel {
        display: grid;
        gap: 22px;
    }

    .dt-side-card h3 {
        margin-bottom: 18px;
    }

    .dt-status-list {
        display: grid;
        gap: 14px;
    }

    .dt-status-list div {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        color: #374151;
        font-size: 12px;
        font-weight: 800;
    }

    .dt-status-list span {
        display: inline-flex;
        align-items: center;
        gap: 9px;
    }

    .dt-status-list strong {
        color: #111827;
        font-weight: 900;
    }

    .dot {
        width: 9px;
        height: 9px;
        border-radius: 999px;
        display: inline-block;
    }

    .dot.yellow { background: #f59e0b; }
    .dot.green { background: #16a34a; }
    .dot.red { background: #dc2626; }
    .dot.blue { background: #2563eb; }

    .dt-timeline {
        position: relative;
    }

    .dt-timeline::before {
        content: "";
        position: absolute;
        left: 8px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: #fee2e2;
    }

    .dt-timeline-item {
        position: relative;
        display: grid;
        grid-template-columns: 20px 1fr;
        gap: 12px;
        padding-bottom: 18px;
    }

    .dt-timeline-item > i {
        position: relative;
        z-index: 1;
        width: 18px;
        height: 18px;
        border-radius: 999px;
        background: #d90429;
        border: 4px solid #ffe8ee;
        margin-top: 2px;
    }

    .dt-timeline-item strong {
        display: block;
        color: #111827;
        font-size: 12px;
        font-weight: 900;
    }

    .dt-timeline-item span {
        display: block;
        color: #6b7280;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.45;
        margin-top: 4px;
    }

    .dt-timeline-item small {
        display: block;
        color: #9ca3af;
        font-size: 10px;
        font-weight: 800;
        margin-top: 7px;
    }

    .dt-quick-list {
        display: grid;
        gap: 12px;
    }

    .dt-quick-list a {
        display: grid;
        grid-template-columns: 42px 1fr 12px;
        gap: 12px;
        align-items: center;
        padding: 12px;
        border: 1px solid #edf0f4;
        border-radius: 15px;
        color: inherit;
        transition: .2s ease;
    }

    .dt-quick-list a:hover {
        background: #fff7f9;
        border-color: #fecdd3;
    }

    .dt-quick-list div {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 13px;
        background: #ffe8ee;
        color: #d90429;
    }

    .dt-quick-list span {
        color: #111827;
        font-size: 12px;
        font-weight: 900;
    }

    .dt-quick-list a > i {
        color: #64748b;
        font-size: 11px;
    }

    .dt-empty,
    .dt-empty-small {
        padding: 38px;
        text-align: center;
        background: #f8fafc;
        border-radius: 18px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .dt-empty i {
        width: 62px;
        height: 62px;
        margin: 0 auto 14px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #ffe8ee;
        color: #d90429;
        font-size: 24px;
    }

    .dt-empty strong {
        display: block;
        color: #111827;
        font-size: 16px;
        font-weight: 900;
        margin-bottom: 6px;
    }

    .dt-empty span {
        display: block;
        max-width: 480px;
        margin: 0 auto;
        line-height: 1.6;
    }

    .dt-empty-small {
        padding: 24px;
    }

    @media (max-width: 1500px) {
        .dt-request-card {
            grid-template-columns: 1fr;
        }

        .dt-action-area {
            border-left: none;
            border-top: 1px solid #edf0f4;
            padding-left: 0;
            padding-top: 16px;
        }
    }

    @media (max-width: 1300px) {
        .dt-main-grid {
            grid-template-columns: 1fr;
        }

        .dt-side-panel {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 1000px) {
        .dt-hero {
            grid-template-columns: 1fr;
        }

        .dt-hero-panel {
            justify-self: start;
            width: 100%;
            max-width: 250px;
        }

        .dt-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dt-side-panel {
            grid-template-columns: 1fr;
        }

        .dt-info-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        .dt-hero {
            padding: 28px;
        }

        .dt-hero h1 {
            font-size: 27px;
        }

        .dt-summary {
            grid-template-columns: 1fr;
        }

        .dt-summary div {
            border-right: none;
            border-bottom: 1px solid #edf0f4;
        }

        .dt-summary div:last-child {
            border-bottom: none;
        }

        .dt-request-main {
            flex-direction: column;
        }

        .dt-request-head {
            flex-direction: column;
        }

        .dt-action-buttons {
            grid-template-columns: 1fr;
        }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Perkuliahan\SEMESTER 4\PA ll\Specta_Academy\BackEnd\resources\views/admin/dedicated_tutor/index.blade.php ENDPATH**/ ?>