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
                <span><i class="fa-solid fa-clock"></i> <?php echo e($pendingRequests); ?> Pending</span>
                <span><i class="fa-solid fa-circle-check"></i> <?php echo e($confirmedRequests); ?> Confirmed</span>
                <span><i class="fa-solid fa-calendar-day"></i> <?php echo e($todayRequests); ?> Hari Ini</span>
            </div>
        </div>
        <div class="dt-hero-panel">
            <div class="dt-ring"><strong><?php echo e($totalRequests); ?></strong><span>Total Request</span></div>
        </div>
    </section>

    
    <section class="dt-summary">
        <div><span>Total</span><strong><?php echo e($totalRequests); ?></strong></div>
        <div><span>Pending</span><strong><?php echo e($pendingRequests); ?></strong></div>
        <div><span>Confirmed</span><strong><?php echo e($confirmedRequests); ?></strong></div>
        <div><span>Rejected</span><strong><?php echo e($rejectedRequests); ?></strong></div>
    </section>

    
    <section class="dt-main-grid">
        <div class="dt-request-panel">
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

                    <article class="dt-request-card <?php echo e($t->status); ?>">
                        <div class="dt-request-main">
                            <div class="dt-student-avatar"><?php echo e(strtoupper(substr($studentName, 0, 1))); ?></div>
                            <div class="dt-request-info">
                                <div class="dt-request-head">
                                    <h3><?php echo e($studentName); ?></h3>
                                    <span class="dt-status <?php echo e($t->status); ?>"><?php echo e(strtoupper($t->status)); ?></span>
                                </div>
                                <div class="dt-info-grid">
                                    <div><small>Topik</small><strong><?php echo e($subjectName); ?></strong></div>
                                    <div><small>Jadwal</small><strong><?php echo e($t->date); ?></strong><span><?php echo e($t->time); ?> WIB</span></div>
                                    <div><small>Guru</small><strong><?php echo e($t->teacher->name ?? 'Belum ada'); ?></strong></div>
                                </div>
                            </div>
                        </div>

                        <div class="dt-action-area">
                            <?php if($t->status === 'pending'): ?>
                                <form action="<?php echo e(route('admin.tutor.update', $t->dedicated_tutor_id)); ?>" method="POST" id="form-<?php echo e($t->dedicated_tutor_id); ?>">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="status" value="confirmed">
                                    <label>Assign Teacher</label>
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
                                    <div class="dt-action-buttons">
                                        <button type="submit" class="dt-confirm-btn">Confirm</button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <div class="dt-final-state <?php echo e($t->status); ?>">
                                    <strong>Request <?php echo e($t->status); ?></strong>
                                    <span>Guru: <?php echo e($t->teacher->name ?? '-'); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="dt-empty">Belum ada permintaan.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<style>
    /* CSS disederhanakan untuk memastikan tampilan tetap profesional */
    .dt-page { padding: 20px; font-family: 'Inter', sans-serif; }
    .dt-hero { background: linear-gradient(135deg, #d90429 0%, #2b2d42 100%); border-radius: 20px; padding: 30px; color: #fff; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .dt-hero h1 { font-size: 28px; margin-bottom: 10px; font-weight: 900; }
    .dt-hero-tags span { background: rgba(255,255,255,0.15); padding: 5px 12px; border-radius: 10px; font-size: 11px; margin-right: 10px; }
    .dt-ring { background: #fff; color: #111; width: 100px; height: 100px; border-radius: 50%; display: flex; flex-direction: column; justify-content: center; align-items: center; }
    .dt-summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px; }
    .dt-summary div { background: #fff; padding: 15px; border-radius: 15px; border: 1px solid #eee; text-align: center; }
    .dt-summary strong { font-size: 24px; display: block; color: #d90429; }
    .dt-request-card { background: #fff; border-radius: 15px; padding: 20px; margin-bottom: 15px; border: 1px solid #eee; display: grid; grid-template-columns: 1fr 250px; gap: 20px; }
    .dt-student-avatar { width: 50px; height: 50px; background: #fff1f2; color: #d90429; border-radius: 12px; display: grid; place-items: center; font-weight: 900; }
    .dt-info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 10px; }
    .dt-info-grid div { background: #f8fafc; padding: 10px; border-radius: 10px; }
    .dt-info-grid small { font-size: 9px; color: #94a3b8; text-transform: uppercase; }
    .dt-info-grid strong { display: block; font-size: 12px; }
    .dt-status { font-size: 10px; padding: 3px 8px; border-radius: 10px; }
    .dt-status.pending { background: #fef3c7; color: #d97706; }
    .dt-status.confirmed { background: #dcfce7; color: #16a34a; }
    .dt-confirm-btn { width: 100%; background: #16a34a; color: #fff; border: none; padding: 10px; border-radius: 10px; font-weight: 800; cursor: pointer; margin-top: 10px; }
    .dt-action-area select { width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #ddd; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/dedicated_tutor/index.blade.php ENDPATH**/ ?>