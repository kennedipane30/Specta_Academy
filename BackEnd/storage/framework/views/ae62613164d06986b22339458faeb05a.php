<?php $__env->startSection('title', 'Matrix Penugasan'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $assignmentCollection = collect($assignments);
    // Ambil nama materi unik untuk header Matrix
    $uniqueSubjectNames = collect($subjects)->pluck('material_name')->unique()->sort();
    $classCollection = collect($classes);

    $totalAssignments = $assignmentCollection->count();
    $assignedTeachers = $assignmentCollection->pluck('user_id')->filter()->unique()->count();

    // Hitung total slot (Jumlah Kelas x Jumlah Nama Materi Unik)
    $totalSlots = max($classCollection->count() * $uniqueSubjectNames->count(), 1);
    $coveragePercent = min(round(($totalAssignments / $totalSlots) * 100), 100);

    // Hitung cakupan per nama mapel
    $subjectCoverage = $uniqueSubjectNames->map(function ($name) use ($assignmentCollection, $subjects) {
        // Cari ID material yang memiliki nama ini
        $ids = collect($subjects)->where('material_name', $name)->pluck('material_id');
        return [
            'name' => $name,
            'total' => $assignmentCollection->whereIn('subject_id', $ids)->count(),
        ];
    })->sortByDesc('total')->values();
?>

<div class="am-page">
    
    <section class="am-hero">
        <div class="am-hero-text">
            <div class="am-kicker">Assignment Control Matrix</div>
            <h1>Penugasan Kurikulum</h1>
            <p>Atur relasi pengajar untuk setiap mata pelajaran di database lokal.</p>
            <div class="am-tags">
                <span><i class="fa-solid fa-user-tie"></i> <?php echo e($assignedTeachers); ?> Pengajar</span>
                <span><i class="fa-solid fa-book"></i> <?php echo e($uniqueSubjectNames->count()); ?> Jenis Mapel</span>
                <span><i class="fa-solid fa-school"></i> <?php echo e($classCollection->count()); ?> Program</span>
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
        <div class="am-strip-card"><span>SLOT KOSONG</span><strong><?php echo e(max($totalSlots - $totalAssignments, 0)); ?></strong></div>
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
                    <select name="class_id" id="class-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($class->class_id); ?>"><?php echo e($class->program_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="am-field">
                    <label>Pilih Mata Pelajaran</label>
                    <select name="subject_id" id="subject-select" required disabled>
                        <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                    </select>
                </div>
                <button type="submit" class="am-btn-submit">Simpan Penugasan</button>
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
                <p>Data dikelola berdasarkan tabel <strong>Materials</strong></p>
            </div>
        </div>
        <div class="am-table-scroll">
            <table class="am-matrix-table">
                <thead>
                    <tr>
                        <th class="sticky-col">PROGRAM KELAS</th>
                        <?php $__currentLoopData = $uniqueSubjectNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th><?php echo e($name); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="sticky-col"><strong><?php echo e($class->program_name); ?></strong></td>
                            <?php $__currentLoopData = $uniqueSubjectNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    // Cari apakah kelas ini punya material dengan nama ini
                                    $targetMaterial = collect($subjects)->where('class_id', $class->class_id)->where('material_name', $name)->first();

                                    $assigned = null;
                                    if($targetMaterial) {
                                        $assigned = $assignmentCollection->where('class_id', $class->class_id)
                                                                         ->where('subject_id', $targetMaterial->material_id)
                                                                         ->first();
                                    }
                                ?>
                                <td>
                                    <?php if($targetMaterial): ?>
                                        <?php if($assigned): ?>
                                            <div class="am-slot filled">
                                                <i class="fa-solid fa-check-circle"></i>
                                                <span><?php echo e($assigned->teacher->name ?? 'Guru'); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <div class="am-slot empty">Kosong</div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="am-slot locked" title="Tidak ada di kurikulum"><i class="fa-solid fa-lock"></i></div>
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
                    <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="am-t-cell">
                                    <div class="am-avatar"><?php echo e(strtoupper(substr($assign->teacher->name ?? 'P', 0, 1))); ?></div>
                                    <strong><?php echo e($assign->teacher->name ?? 'N/A'); ?></strong>
                                </div>
                            </td>
                            <td><?php echo e($assign->classModel->program_name ?? 'N/A'); ?></td>
                            <td>
                                <?php
                                    $matName = collect($subjects)->where('material_id', $assign->subject_id)->first()->material_name ?? 'N/A';
                                ?>
                                <span class="badge-subject"><?php echo e($matName); ?></span>
                            </td>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#class-select').on('change', function() {
        var classId = $(this).val();
        var subjectSelect = $('#subject-select');

        if (classId) {
            subjectSelect.prop('disabled', true).html('<option>Memuat Mapel...</option>');
            $.ajax({
                url: "<?php echo e(url('/admin/get-subjects-by-class')); ?>/" + classId,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    subjectSelect.prop('disabled', false).empty();
                    subjectSelect.append('<option value="">-- Pilih Mapel --</option>');
                    if(data.length > 0) {
                        $.each(data, function(key, value) {
                            subjectSelect.append('<option value="' + value.material_id + '">' + value.material_name + '</option>');
                        });
                    } else {
                        subjectSelect.html('<option value="">Tidak ada mapel untuk kelas ini</option>');
                    }
                }
            });
        } else {
            subjectSelect.prop('disabled', true).html('<option value="">-- Pilih Kelas Terlebih Dahulu --</option>');
        }
    });
});
</script>

<style>
    /* DESAIN STYLE SPEKTA ACADEMY */
    .am-page { font-family: 'Inter', sans-serif; padding: 20px; color: #1e293b; }
    .am-hero { background: linear-gradient(135deg, #d90429 0%, #2b2d42 100%); border-radius: 24px; padding: 40px; color: #fff; display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .am-kicker { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px; margin-bottom: 15px; display: inline-block; }
    .am-hero h1 { font-size: 36px; font-weight: 900; margin: 0 0 10px; }
    .am-tags { display: flex; gap: 15px; margin-top: 20px; }
    .am-tags span { background: rgba(255,255,255,0.1); padding: 7px 15px; border-radius: 12px; font-size: 12px; font-weight: 700; }
    .am-circle { width: 120px; height: 120px; border-radius: 50%; background: conic-gradient(#fff var(--progress), rgba(255,255,255,0.1) 0); display: grid; place-items: center; }
    .am-inner-circle { background: #fff; width: 85%; height: 85%; border-radius: 50%; display: flex; flex-direction: column; justify-content: center; align-items: center; color: #111; }
    .am-inner-circle strong { font-size: 24px; font-weight: 900; }
    .am-inner-circle small { font-size: 9px; font-weight: 800; text-transform: uppercase; color: #64748b; }

    .am-strip { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
    .am-strip-card { background: #fff; padding: 20px; border-radius: 18px; border: 1px solid #f1f5f9; text-align: center; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); }
    .am-strip-card span { font-size: 10px; font-weight: 800; color: #94a3b8; display: block; margin-bottom: 5px; }
    .am-strip-card strong { font-size: 28px; font-weight: 900; color: #1e293b; }

    .am-grid-top { display: grid; grid-template-columns: 1.6fr 1fr; gap: 25px; margin-bottom: 25px; }
    .am-card { background: #fff; border-radius: 22px; padding: 25px; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); }
    .am-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .am-card-header h2 { font-size: 18px; font-weight: 800; color: #0f172a; margin: 0; }

    .am-form { display: grid; gap: 15px; }
    .am-field label { font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; display: block; }
    .am-field select { width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; font-weight: 600; font-family: inherit; }
    .am-btn-submit { background: #d90429; color: #fff; border: none; padding: 15px; border-radius: 14px; font-weight: 800; cursor: pointer; transition: 0.3s; }
    .am-btn-submit:hover { background: #ef233c; transform: translateY(-2px); }

    .am-table-scroll { overflow-x: auto; border: 1px solid #f1f5f9; border-radius: 15px; }
    .am-matrix-table { width: 100%; border-collapse: collapse; min-width: 1000px; }
    .am-matrix-table th { background: #f8fafc; padding: 15px; text-align: left; font-size: 11px; color: #64748b; text-transform: uppercase; }
    .am-matrix-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; }
    .sticky-col { position: sticky; left: 0; background: #fff !important; z-index: 10; border-right: 2px solid #f1f5f9; min-width: 200px; }

    .am-slot { padding: 8px 12px; border-radius: 10px; font-size: 11px; font-weight: 700; display: flex; align-items: center; gap: 8px; justify-content: center; min-height: 40px; }
    .am-slot.filled { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .am-slot.empty { background: #fff1f2; color: #be123c; border: 1px dashed #fecdd3; }
    .am-slot.locked { background: #f1f5f9; color: #94a3b8; opacity: 0.5; }

    .am-list-table { width: 100%; border-collapse: collapse; }
    .am-list-table th { text-align: left; padding: 12px; font-size: 11px; color: #94a3b8; text-transform: uppercase; }
    .am-list-table td { padding: 15px 12px; border-bottom: 1px solid #f1f5f9; }
    .am-t-cell { display: flex; align-items: center; gap: 12px; }
    .am-avatar { width: 32px; height: 32px; background: #fff1f2; color: #d90429; border-radius: 50%; display: grid; place-items: center; font-weight: 800; font-size: 12px; }
    .badge-subject { background: #e0f2fe; color: #0369a1; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .am-btn-del { background: none; border: none; color: #ef4444; cursor: pointer; font-size: 16px; transition: 0.2s; }
    .am-btn-del:hover { color: #b91c1c; transform: scale(1.1); }

    .am-progress-row { margin-bottom: 15px; }
    .am-progress-label { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px; }
    .am-progress-label span { font-weight: 700; color: #475569; }
    .am-progress-bg { height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
    .am-progress-bg em { display: block; height: 100%; background: #d90429; border-radius: 10px; }
</style>
<?php $__env->stopSection(); ?>@extends('layouts.spekta')

<?php $__env->startSection('title', 'Matrix Penugasan'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $assignmentCollection = collect($assignments);
    // Ambil nama materi unik untuk header Matrix
    $uniqueSubjectNames = collect($subjects)->pluck('material_name')->unique()->sort();
    $classCollection = collect($classes);

    $totalAssignments = $assignmentCollection->count();
    $assignedTeachers = $assignmentCollection->pluck('user_id')->filter()->unique()->count();

    // Hitung total slot (Jumlah Kelas x Jumlah Nama Materi Unik)
    $totalSlots = max($classCollection->count() * $uniqueSubjectNames->count(), 1);
    $coveragePercent = min(round(($totalAssignments / $totalSlots) * 100), 100);

    // Hitung cakupan per nama mapel
    $subjectCoverage = $uniqueSubjectNames->map(function ($name) use ($assignmentCollection, $subjects) {
        // Cari ID material yang memiliki nama ini
        $ids = collect($subjects)->where('material_name', $name)->pluck('material_id');
        return [
            'name' => $name,
            'total' => $assignmentCollection->whereIn('subject_id', $ids)->count(),
        ];
    })->sortByDesc('total')->values();
?>

<div class="am-page">
    
    <section class="am-hero">
        <div class="am-hero-text">
            <div class="am-kicker">Assignment Control Matrix</div>
            <h1>Penugasan Kurikulum</h1>
            <p>Atur relasi pengajar untuk setiap mata pelajaran di database lokal.</p>
            <div class="am-tags">
                <span><i class="fa-solid fa-user-tie"></i> <?php echo e($assignedTeachers); ?> Pengajar</span>
                <span><i class="fa-solid fa-book"></i> <?php echo e($uniqueSubjectNames->count()); ?> Jenis Mapel</span>
                <span><i class="fa-solid fa-school"></i> <?php echo e($classCollection->count()); ?> Program</span>
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
        <div class="am-strip-card"><span>SLOT KOSONG</span><strong><?php echo e(max($totalSlots - $totalAssignments, 0)); ?></strong></div>
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
                    <select name="class_id" id="class-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($class->class_id); ?>"><?php echo e($class->program_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="am-field">
                    <label>Pilih Mata Pelajaran</label>
                    <select name="subject_id" id="subject-select" required disabled>
                        <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                    </select>
                </div>
                <button type="submit" class="am-btn-submit">Simpan Penugasan</button>
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
                <p>Data dikelola berdasarkan tabel <strong>Materials</strong></p>
            </div>
        </div>
        <div class="am-table-scroll">
            <table class="am-matrix-table">
                <thead>
                    <tr>
                        <th class="sticky-col">PROGRAM KELAS</th>
                        <?php $__currentLoopData = $uniqueSubjectNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th><?php echo e($name); ?></th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="sticky-col"><strong><?php echo e($class->program_name); ?></strong></td>
                            <?php $__currentLoopData = $uniqueSubjectNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    // Cari apakah kelas ini punya material dengan nama ini
                                    $targetMaterial = collect($subjects)->where('class_id', $class->class_id)->where('material_name', $name)->first();

                                    $assigned = null;
                                    if($targetMaterial) {
                                        $assigned = $assignmentCollection->where('class_id', $class->class_id)
                                                                         ->where('subject_id', $targetMaterial->material_id)
                                                                         ->first();
                                    }
                                ?>
                                <td>
                                    <?php if($targetMaterial): ?>
                                        <?php if($assigned): ?>
                                            <div class="am-slot filled">
                                                <i class="fa-solid fa-check-circle"></i>
                                                <span><?php echo e($assigned->teacher->name ?? 'Guru'); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <div class="am-slot empty">Kosong</div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="am-slot locked" title="Tidak ada di kurikulum"><i class="fa-solid fa-lock"></i></div>
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
                    <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="am-t-cell">
                                    <div class="am-avatar"><?php echo e(strtoupper(substr($assign->teacher->name ?? 'P', 0, 1))); ?></div>
                                    <strong><?php echo e($assign->teacher->name ?? 'N/A'); ?></strong>
                                </div>
                            </td>
                            <td><?php echo e($assign->classModel->program_name ?? 'N/A'); ?></td>
                            <td>
                                <?php
                                    $matName = collect($subjects)->where('material_id', $assign->subject_id)->first()->material_name ?? 'N/A';
                                ?>
                                <span class="badge-subject"><?php echo e($matName); ?></span>
                            </td>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#class-select').on('change', function() {
        var classId = $(this).val();
        var subjectSelect = $('#subject-select');

        if (classId) {
            subjectSelect.prop('disabled', true).html('<option>Memuat Mapel...</option>');
            $.ajax({
                url: "<?php echo e(url('/admin/get-subjects-by-class')); ?>/" + classId,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    subjectSelect.prop('disabled', false).empty();
                    subjectSelect.append('<option value="">-- Pilih Mapel --</option>');
                    if(data.length > 0) {
                        $.each(data, function(key, value) {
                            subjectSelect.append('<option value="' + value.material_id + '">' + value.material_name + '</option>');
                        });
                    } else {
                        subjectSelect.html('<option value="">Tidak ada mapel untuk kelas ini</option>');
                    }
                }
            });
        } else {
            subjectSelect.prop('disabled', true).html('<option value="">-- Pilih Kelas Terlebih Dahulu --</option>');
        }
    });
});
</script>

<style>
    /* DESAIN STYLE SPEKTA ACADEMY */
    .am-page { font-family: 'Inter', sans-serif; padding: 20px; color: #1e293b; }
    .am-hero { background: linear-gradient(135deg, #d90429 0%, #2b2d42 100%); border-radius: 24px; padding: 40px; color: #fff; display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .am-kicker { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px; margin-bottom: 15px; display: inline-block; }
    .am-hero h1 { font-size: 36px; font-weight: 900; margin: 0 0 10px; }
    .am-tags { display: flex; gap: 15px; margin-top: 20px; }
    .am-tags span { background: rgba(255,255,255,0.1); padding: 7px 15px; border-radius: 12px; font-size: 12px; font-weight: 700; }
    .am-circle { width: 120px; height: 120px; border-radius: 50%; background: conic-gradient(#fff var(--progress), rgba(255,255,255,0.1) 0); display: grid; place-items: center; }
    .am-inner-circle { background: #fff; width: 85%; height: 85%; border-radius: 50%; display: flex; flex-direction: column; justify-content: center; align-items: center; color: #111; }
    .am-inner-circle strong { font-size: 24px; font-weight: 900; }
    .am-inner-circle small { font-size: 9px; font-weight: 800; text-transform: uppercase; color: #64748b; }

    .am-strip { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
    .am-strip-card { background: #fff; padding: 20px; border-radius: 18px; border: 1px solid #f1f5f9; text-align: center; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); }
    .am-strip-card span { font-size: 10px; font-weight: 800; color: #94a3b8; display: block; margin-bottom: 5px; }
    .am-strip-card strong { font-size: 28px; font-weight: 900; color: #1e293b; }

    .am-grid-top { display: grid; grid-template-columns: 1.6fr 1fr; gap: 25px; margin-bottom: 25px; }
    .am-card { background: #fff; border-radius: 22px; padding: 25px; border: 1px solid #f1f5f9; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); }
    .am-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .am-card-header h2 { font-size: 18px; font-weight: 800; color: #0f172a; margin: 0; }

    .am-form { display: grid; gap: 15px; }
    .am-field label { font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 8px; display: block; }
    .am-field select { width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; font-weight: 600; font-family: inherit; }
    .am-btn-submit { background: #d90429; color: #fff; border: none; padding: 15px; border-radius: 14px; font-weight: 800; cursor: pointer; transition: 0.3s; }
    .am-btn-submit:hover { background: #ef233c; transform: translateY(-2px); }

    .am-table-scroll { overflow-x: auto; border: 1px solid #f1f5f9; border-radius: 15px; }
    .am-matrix-table { width: 100%; border-collapse: collapse; min-width: 1000px; }
    .am-matrix-table th { background: #f8fafc; padding: 15px; text-align: left; font-size: 11px; color: #64748b; text-transform: uppercase; }
    .am-matrix-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; }
    .sticky-col { position: sticky; left: 0; background: #fff !important; z-index: 10; border-right: 2px solid #f1f5f9; min-width: 200px; }

    .am-slot { padding: 8px 12px; border-radius: 10px; font-size: 11px; font-weight: 700; display: flex; align-items: center; gap: 8px; justify-content: center; min-height: 40px; }
    .am-slot.filled { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .am-slot.empty { background: #fff1f2; color: #be123c; border: 1px dashed #fecdd3; }
    .am-slot.locked { background: #f1f5f9; color: #94a3b8; opacity: 0.5; }

    .am-list-table { width: 100%; border-collapse: collapse; }
    .am-list-table th { text-align: left; padding: 12px; font-size: 11px; color: #94a3b8; text-transform: uppercase; }
    .am-list-table td { padding: 15px 12px; border-bottom: 1px solid #f1f5f9; }
    .am-t-cell { display: flex; align-items: center; gap: 12px; }
    .am-avatar { width: 32px; height: 32px; background: #fff1f2; color: #d90429; border-radius: 50%; display: grid; place-items: center; font-weight: 800; font-size: 12px; }
    .badge-subject { background: #e0f2fe; color: #0369a1; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .am-btn-del { background: none; border: none; color: #ef4444; cursor: pointer; font-size: 16px; transition: 0.2s; }
    .am-btn-del:hover { color: #b91c1c; transform: scale(1.1); }

    .am-progress-row { margin-bottom: 15px; }
    .am-progress-label { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px; }
    .am-progress-label span { font-weight: 700; color: #475569; }
    .am-progress-bg { height: 8px; background: #f1f5f9; border-radius: 10px; overflow: hidden; }
    .am-progress-bg em { display: block; height: 100%; background: #d90429; border-radius: 10px; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\Specta_Academy\BackEnd\resources\views/admin/assignments/index.blade.php ENDPATH**/ ?>