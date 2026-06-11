<?php $__env->startSection('title', 'Dashboard Program - Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<?php
    // Hitung statistik global
    $totalClasses = \App\Models\ClassModel::count();
    $totalStudents = \App\Models\User::where('role_id', 3)->count();
    $totalTeachers = \App\Models\User::where('role_id', 2)->count();
    $totalEnrollments = \App\Models\Enrollment::where('status', 'active')->count();

    // Mapping nama program ke nama file gambar
    $programImages = [
        'CALON ABDI NEGARA' => 'abdi_negara.png',
        'PTN & UNHAN' => 'ptn_unhan.png',
        'SMA & SMP REGULER' => 'regular.png',
        'SMA FAVORIT' => 'favort.png',
    ];
?>

<div class="cp-page">

    
    <section class="cp-header">
        <div>
            <span class="cp-tagline">SPEKTA DASHBOARD</span>
            <h1 class="cp-title">Program Kelas</h1>
            <p class="cp-subtitle">Monitor statistik program, jumlah siswa, dan penugasan pengajar.</p>
        </div>
    </section>

    
    <section class="cp-stats">
        <div class="cp-stat-card border-red">
            <div class="cp-stat-content">
                <p>Total Program</p>
                <h2><?php echo e(number_format($totalClasses)); ?></h2>
            </div>
            <div class="cp-stat-icon"><i class="fa-solid fa-layer-group"></i></div>
        </div>
        <div class="cp-stat-card border-green">
            <div class="cp-stat-content">
                <p>Total Siswa</p>
                <h2><?php echo e(number_format($totalStudents)); ?></h2>
            </div>
            <div class="cp-stat-icon"><i class="fa-solid fa-users"></i></div>
        </div>
        <div class="cp-stat-card border-blue">
            <div class="cp-stat-content">
                <p>Total Pengajar</p>
                <h2><?php echo e(number_format($totalTeachers)); ?></h2>
            </div>
            <div class="cp-stat-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
        </div>
        <div class="cp-stat-card border-purple">
            <div class="cp-stat-content">
                <p>Pendaftaran Aktif</p>
                <h2><?php echo e(number_format($totalEnrollments)); ?></h2>
            </div>
            <div class="cp-stat-icon"><i class="fa-solid fa-user-graduate"></i></div>
        </div>
    </section>

    
    <section class="cp-main-panel">
        <div class="cp-panel-heading">
            <h2>Statistik Program Kelas</h2>
            <p>Detail lengkap per program: jumlah siswa dan pengajar.</p>
        </div>

        <div class="cp-program-list">
            <?php $__empty_1 = true; $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    // Hitung statistik per kelas
                    $studentCount = \App\Models\Enrollment::where('class_id', $class->class_id)
                        ->where('status', 'active')
                        ->count();

                    $teacherCount = \App\Models\TeacherAssignment::where('class_id', $class->class_id)
                        ->distinct('user_id')
                        ->count('user_id');

                    // ✅ Ambil gambar dari folder public berdasarkan nama program
                    $imageFile = $programImages[$class->program_name] ?? 'default.png';
                    $imageUrl = asset($imageFile);
                ?>

                <div class="cp-card">
                    <div class="cp-card-img">
                        <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($class->program_name); ?>">
                        <div class="cp-badge-price">Rp <?php echo e(number_format($class->price, 0, ',', '.')); ?></div>
                    </div>

                    <div class="cp-card-content">
                        <div class="cp-card-header">
                            <span class="cp-id">ID: #<?php echo e($class->class_id); ?></span>
                            <h3 class="cp-class-name"><?php echo e($class->program_name); ?></h3>
                            <p class="cp-class-desc"><?php echo e(\Illuminate\Support\Str::limit($class->description, 100)); ?></p>
                        </div>

                        
                        <div class="cp-stats-mini">
                            <div class="stat-item">
                                <i class="fa-solid fa-user-graduate"></i>
                                <div>
                                    <strong><?php echo e(number_format($studentCount)); ?></strong>
                                    <span>Siswa Aktif</span>
                                </div>
                            </div>
                            <div class="stat-item">
                                <i class="fa-solid fa-chalkboard-user"></i>
                                <div>
                                    <strong><?php echo e(number_format($teacherCount)); ?></strong>
                                    <span>Pengajar</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="cp-empty-state">
                    <i class="fa-solid fa-folder-open"></i>
                    <p>Belum ada program kelas yang dibuat.</p>
                </div>
            <?php endif; ?>
        </div>

        
        <?php if(method_exists($classes, 'links')): ?>
        <div class="cp-pagination">
            <?php echo e($classes->links()); ?>

        </div>
        <?php endif; ?>
    </section>
</div>

<style>
    .cp-page { padding: 10px; font-family: 'Inter', sans-serif; background: #f8fafc; min-height: 100vh; }
    .cp-header { margin-bottom: 30px; }
    .cp-tagline { color: #d90429; font-weight: 800; font-size: 11px; letter-spacing: 1.5px; text-transform: uppercase; }
    .cp-title { font-size: 28px; font-weight: 900; color: #1e293b; margin: 5px 0; }
    .cp-subtitle { color: #64748b; font-size: 14px; margin: 0; }

    .cp-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
    .cp-stat-card { background: white; padding: 20px 25px; border-radius: 20px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0; }
    .cp-stat-card.border-red { border-left: 4px solid #d90429; }
    .cp-stat-card.border-green { border-left: 4px solid #10b981; }
    .cp-stat-card.border-blue { border-left: 4px solid #3b82f6; }
    .cp-stat-card.border-purple { border-left: 4px solid #8b5cf6; }
    .cp-stat-content p { color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase; margin: 0 0 4px; }
    .cp-stat-content h2 { font-size: 28px; font-weight: 800; color: #1e293b; margin: 0; }
    .cp-stat-icon { font-size: 28px; color: #cbd5e1; }

    .cp-main-panel { background: white; border-radius: 24px; padding: 30px; border: 1px solid #e2e8f0; }
    .cp-panel-heading { margin-bottom: 25px; }
    .cp-panel-heading h2 { font-size: 18px; font-weight: 800; color: #1e293b; margin: 0; }
    .cp-panel-heading p { color: #64748b; font-size: 13px; margin: 5px 0 0; }

    .cp-card { display: flex; background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; padding: 20px; gap: 25px; margin-bottom: 20px; transition: 0.2s; }
    .cp-card:hover { border-color: #d9042940; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    .cp-card-img { width: 280px; height: 180px; border-radius: 16px; overflow: hidden; position: relative; flex-shrink: 0; background: #f1f5f9; display: flex; align-items: center; justify-content: center; }
    .cp-card-img img { width: 100%; height: 100%; object-fit: cover; }
    .cp-card-img img[src=""] { display: none; }
    .cp-badge-price { position: absolute; top: 12px; left: 12px; background: rgba(0,0,0,0.75); color: white; padding: 5px 12px; border-radius: 10px; font-weight: 700; font-size: 12px; backdrop-filter: blur(4px); }
    .cp-card-content { flex: 1; display: flex; flex-direction: column; gap: 12px; }
    .cp-card-header { margin-bottom: 5px; }
    .cp-id { font-size: 11px; color: #94a3b8; font-weight: 600; }
    .cp-class-name { font-size: 20px; font-weight: 800; color: #1e293b; margin: 4px 0 0; }
    .cp-class-desc { font-size: 13px; color: #64748b; line-height: 1.5; margin: 8px 0 0; }

    .cp-stats-mini { display: flex; gap: 20px; background: #f8fafc; padding: 12px 16px; border-radius: 14px; }
    .stat-item { display: flex; align-items: center; gap: 12px; }
    .stat-item i { font-size: 20px; color: #d90429; }
    .stat-item strong { font-size: 18px; font-weight: 800; color: #1e293b; display: block; line-height: 1.2; }
    .stat-item span { font-size: 10px; color: #64748b; font-weight: 600; }

    .cp-pagination { margin-top: 25px; display: flex; justify-content: center; }

    @media (max-width: 1024px) {
        .cp-stats { grid-template-columns: repeat(2, 1fr); }
        .cp-card { flex-direction: column; }
        .cp-card-img { width: 100%; height: 200px; }
        .cp-stats-mini { flex-wrap: wrap; }
    }
    @media (max-width: 640px) {
        .cp-stats { grid-template-columns: 1fr; }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\perkuliahan\PA 2 - code\PAAAAA2\BackEnd\resources\views/admin/classes/index.blade.php ENDPATH**/ ?>