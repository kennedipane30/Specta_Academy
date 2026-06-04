<?php $__env->startSection('title', 'Matrix Penugasan'); ?>
<?php $__env->startSection('subtitle', 'Atur relasi pengajar dan mata pelajaran per program'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $assignmentCollection = collect($assignments);
    $subjectCollection = collect($subjects);
    $classCollection = collect($classes);

    $totalAssignments = $assignmentCollection->count();
    $assignedTeachers = $assignmentCollection->pluck('user_id')->filter()->unique()->count();
    $assignedClasses = $assignmentCollection->pluck('class_id')->filter()->unique()->count();
    $coveredSubjects = $assignmentCollection->pluck('subject_id')->filter()->unique()->count();

    $totalSlots = max($classCollection->count() * $subjectCollection->count(), 1);
    $coveragePercent = min(round(($totalAssignments / $totalSlots) * 100), 100);

    $subjectCoverage = $subjectCollection->map(function ($subject) use ($assignmentCollection) {
        return [
            'name' => $subject->name,
            'total' => $assignmentCollection->where('subject_id', $subject->subject_id)->count(),
        ];
    })->sortByDesc('total')->values();
?>

<div class="am-page">

    
    <section class="am-hero">
        <div class="am-hero-text">
            <div class="am-kicker">Assignment Control Matrix</div>
            <h1>Penugasan Kurikulum</h1>
            <p>Atur siapa pengajar untuk setiap mata pelajaran di setiap program kelas secara sistematis.</p>
            
            <div class="am-tags">
                <span><i class="fa-solid fa-user-tie"></i> <?php echo e($assignedTeachers); ?> Pengajar</span>
                <span><i class="fa-solid fa-book"></i> <?php echo e($coveredSubjects); ?> Mapel Terisi</span>
                <span><i class="fa-solid fa-school"></i> <?php echo e($assignedClasses); ?> Program</span>
            </div>
        </div>
        <div class="am-hero-chart">
            <div class="am-circle" style="--progress: <?php echo e($coveragePercent); ?>%;">
                <div class="am-inner-circle">
                    <strong><?php echo e($coveragePercent); ?>%</strong>
                    <small>Coverage</small>
                </div>
            </div>
        </div>
    </section>

    
    <section class="am-strip">
        <div class="am-strip-card"><span>TOTAL PENUGASAN</span><strong><?php echo e($totalAssignments); ?></strong></div>
        <div class="am-strip-card"><span>SLOT TERSEDIA</span><strong><?php echo e($totalSlots); ?></strong></div>
        <div class="am-strip-card"><span>SLOT KOSONG</span><strong><?php echo e($totalSlots - $totalAssignments); ?></strong></div>
    </section>

    
    <section class="am-grid-top">
        
        <div class="am-card am-form-card">
            <div class="am-card-header">
                <h2>Tambah Penugasan Baru</h2>
                <i class="fa-solid fa-circle-plus"></i>
            </div>
            <form action="<?php echo e(route('admin.assignments.store')); ?>" method="POST" class="am-form">
                <?php echo csrf_field(); ?>
                <div class="am-field">
                    <label>Pilih Pengajar</label>
                    <select name="teacher_id" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($teacher->usersID); ?>"><?php echo e($teacher->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="am-field">
                    <label>Pilih Program</label>
                    <select name="class_id" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($class->class_id); ?>"><?php echo e($class->program_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="am-field">
                    <label>Pilih Mata Pelajaran</label>
                    <select name="subject_id" required>
                        <option value="">-- Pilih Mapel --</option>
                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($subject->subject_id); ?>"><?php echo e($subject->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <button type="submit" class="am-btn-submit">Simpan  </button>
            </form>
        </div>

        
        <div class="am-card">
            <div class="am-card-header"><h2>Cakupan Mapel</h2></div>
            <div class="am-coverage-list">
                <?php $__currentLoopData = $subjectCoverage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="am-progress-row">
                        <div class="am-progress-label"><span><?php echo e($sc['name']); ?></span><strong><?php echo e($sc['total']); ?></strong></div>
                        <div class="am-progress-bg"><em style="width: <?php echo e($totalAssignments > 0 ? ($sc['total'] / $totalAssignments) * 100 : 0); ?>%"></em></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    
    <section class="am-card am-matrix-card">
        <div class="am-card-header">
            <div>
                <h2>Peta Penugasan (Matrix View)</h2>
                <p>Scroll ke kanan untuk melihat mapel lainnya</p>
            </div>
        </div>
        <div class="am-table-scroll">
            <table class="am-matrix-table">
                <thead>
                    <tr>
                        <th class="sticky-col">PROGRAM KELAS</th>
                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th><?php echo e($subject->name); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="sticky-col"><strong><?php echo e($class->program_name); ?></strong></td>
                            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $slot = $assignmentCollection
                                        ->where('class_id', $class->class_id)
                                        ->firstWhere('subject_id', $subject->subject_id);
                                ?>
                                <td>
                                    <?php if($slot): ?>
                                        <div class="am-slot filled">
                                            <i class="fa-solid fa-check-circle"></i>
                                            <span><?php echo e($slot->teacher->name ?? 'Guru'); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <div class="am-slot empty">Kosong</div>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </section>

    
    <section class="am-card">
        <div class="am-card-header"><h2>Daftar Penugasan Aktif</h2></div>
        <div class="am-table-list-wrap">
            <table class="am-list-table">
                <thead>
                    <tr>
                        <th>Pengajar</th>
                        <th>Program</th>
                        <th>Mata Pelajaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $assignmentCollection; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="am-t-cell">
                                    <div class="am-avatar"><?php echo e(strtoupper(substr($assign->teacher->name ?? 'P', 0, 1))); ?></div>
                                    <strong><?php echo e($assign->teacher->name ?? 'N/A'); ?></strong>
                                </div>
                            </td>
                            <td><?php echo e($assign->classModel->program_name ?? 'N/A'); ?></td>
                            <td><span class="badge-subject"><?php echo e($assign->subject->name ?? 'N/A'); ?></span></td>
                            <td>
                                <form action="<?php echo e(route('admin.assignments.destroy', $assign->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="am-btn-del" onclick="return confirm('Hapus?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-center">Belum ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
    .am-page { font-family: 'Inter', sans-serif; }
    
    /* Hero */
    .am-hero { background: linear-gradient(135deg, #d90429 0%, #2b2d42 100%); border-radius: 24px; padding: 40px; color: #fff; display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; box-shadow: 0 20px 40px rgba(217, 4, 41, 0.15); }
    .am-kicker { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px; margin-bottom: 15px; display: inline-block; }
    .am-hero h1 { font-size: 36px; font-weight: 900; margin: 0 0 10px; }
    .am-tags { display: flex; gap: 15px; margin-top: 20px; }
    .am-tags span { background: rgba(255,255,255,0.1); padding: 7px 15px; border-radius: 12px; font-size: 12px; font-weight: 700; }
    
    /* Circle Chart */
    .am-circle { width: 120px; height: 120px; border-radius: 50%; background: conic-gradient(#fff var(--progress), rgba(255,255,255,0.1) 0); display: grid; place-items: center; padding: 10px; }
    .am-inner-circle { background: #fff; width: 100%; height: 100%; border-radius: 50%; display: flex; flex-direction: column; justify-content: center; align-items: center; color: #111; }
    .am-inner-circle strong { font-size: 24px; font-weight: 900; }
    .am-inner-circle small { font-size: 9px; font-weight: 800; text-transform: uppercase; color: #64748b; }

    /* Strip */
    .am-strip { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
    .am-strip-card { background: #fff; padding: 20px; border-radius: 18px; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); text-align: center; }
    .am-strip-card span { font-size: 10px; font-weight: 800; color: #94a3b8; display: block; margin-bottom: 5px; }
    .am-strip-card strong { font-size: 28px; font-weight: 900; color: #1e293b; }

    /* Grid & Cards */
    .am-grid-top { display: grid; grid-template-columns: 1.6fr 1fr; gap: 25px; margin-bottom: 25px; }
    .am-card { background: #fff; border-radius: 22px; padding: 25px; border: 1px solid #f1f5f9; margin-bottom: 25px; }
    .am-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .am-card-header h2 { font-size: 18px; font-weight: 800; margin: 0; color: #0f172a; }
    
    /* Form */
    .am-form { display: grid; gap: 15px; }
    .am-field label { font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; display: block; }
    .am-field select { width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; font-weight: 600; outline: none; }
    .am-btn-submit { background: #d90429; color: #fff; border: none; padding: 15px; border-radius: 14px; font-weight: 800; cursor: pointer; box-shadow: 0 10px 20px rgba(217, 4, 41, 0.2); }

    /* Matrix Table */
    .am-table-scroll { overflow-x: auto; border-radius: 15px; border: 1px solid #f1f5f9; }
    .am-matrix-table { width: 100%; border-collapse: collapse; min-width: 1200px; }
    .am-matrix-table th { background: #f8fafc; padding: 15px; text-align: left; font-size: 11px; color: #64748b; border-bottom: 2px solid #f1f5f9; }
    .am-matrix-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; }
    
    .sticky-col { position: sticky; left: 0; background: #fff !important; z-index: 10; border-right: 2px solid #f1f5f9; width: 220px; }
    
    .am-slot { padding: 10px; border-radius: 12px; font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .am-slot.filled { background: #f0fdf4; color: #15803d; }
    .am-slot.empty { background: #f8fafc; color: #94a3b8; border: 1px dashed #e2e8f0; }

    /* List Table */
    .am-list-table { width: 100%; border-collapse: collapse; }
    .am-list-table th { text-align: left; padding: 12px; font-size: 11px; color: #94a3b8; border-bottom: 2px solid #f1f5f9; }
    .am-list-table td { padding: 15px 12px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
    .am-t-cell { display: flex; align-items: center; gap: 10px; }
    .am-avatar { width: 35px; height: 35px; background: #fff1f2; color: #d90429; border-radius: 50%; display: grid; place-items: center; font-weight: 800; }
    .badge-subject { background: #e0f2fe; color: #0369a1; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .am-btn-del { background: none; border: none; color: #ef4444; cursor: pointer; font-size: 16px; }

    /* Progress */
    .am-progress-row { margin-bottom: 15px; }
    .am-progress-label { display: flex; justify-content: space-between; font-size: 13px; font-weight: 700; margin-bottom: 5px; }
    .am-progress-bg { height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
    .am-progress-bg em { display: block; height: 100%; background: #d90429; border-radius: 10px; }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Perkuliahan\SEMESTER 4\PA ll\Specta_Academy\BackEnd\resources\views/admin/assignments/index.blade.php ENDPATH**/ ?>