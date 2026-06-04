<?php $__env->startSection('title', 'Bank Soal Tryout - Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<?php
    // Menghitung total seluruh soal yang sudah dibuat oleh guru ini di semua kelas/mapel
    $totalSoalSelesai = \DB::table('tryout_drafts')
        ->where('user_id', Auth::user()->usersID)
        ->count();
    
    $totalAssignment = count($assignments);
?>

<div class="cp-page">
    
    <section class="tm-hero-header">
        <div class="tm-hero-content">
            <div class="tm-hero-text">
                <span class="tm-pre-title">TEACHER TRYOUT PORTAL</span>
                <h1 class="tm-main-title">Tryout Question Center</h1>
                <p class="tm-sub-title">Kontribusikan draf soal terbaik Anda. Admin akan mengkurasi draf tersebut menjadi satu paket Tryout resmi.</p>
            </div>
        </div>
        
        <div class="tm-hero-summary">
            <div class="summary-card">
                <i class="fa-solid fa-briefcase"></i>
                <div class="summary-data">
                    <strong><?php echo e($totalAssignment); ?></strong>
                    <span>Penugasan</span>
                </div>
            </div>
            <div class="summary-card highlight">
                <i class="fa-solid fa-file-circle-check"></i>
                <div class="summary-data">
                    <strong><?php echo e($totalSoalSelesai); ?></strong>
                    <span>Total Soal</span>
                </div>
            </div>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="tm-alert-modern success">
            <i class="fa-solid fa-circle-check"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="tm-alert-modern error" style="background: #fee2e2; color: #b91c1c; border-left: 5px solid #ef4444;">
            <i class="fa-solid fa-circle-xmark"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    
    <section class="cp-main-card">
        <div class="card-header-flex">
            <div>
                <h2>Daftar Penugasan Soal</h2>
                <p>Klik tombol input untuk mengelola soal di setiap mata pelajaran.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="cp-table-modern">
                <thead>
                    <tr>
                        <th width="25%">PROGRAM KELAS</th>
                        <th width="20%" class="text-center">MATA PELAJARAN</th>
                        <th width="35%" class="text-center">PROGRESS ANDA</th>
                        <th width="20%" class="text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php 
                            // Ambil nama mapel resmi dari database
                            $subjectName = $assign->subject->name ?? 'Umum';
                            
                            // HITUNG SOAL: Pastikan filter ini SAMA dengan saat simpan/import di Controller
                            $count = \DB::table('tryout_drafts')
                                ->where('user_id', Auth::user()->usersID)
                                ->where('class_id', $assign->class_id)
                                ->where('subject_name', trim($subjectName))
                                ->count();
                        ?>
                        <tr>
                            <td class="align-middle">
                                <div class="program-info">
                                    <div class="program-icon-box">
                                        <i class="fa-solid fa-school-flag"></i>
                                    </div>
                                    <div>
                                        <strong><?php echo e($assign->classModel->program_name ?? 'Program'); ?></strong>
                                        <small>ID Kelas: #<?php echo e($assign->class_id); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <span class="subject-tag">
                                    <i class="fa-solid fa-book-bookmark mr-1"></i>
                                    <?php echo e($subjectName); ?>

                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="progress-container-flex">
                                    
                                    <div class="contribution-info <?php echo e($count > 0 ? 'active' : ''); ?>">
                                        <div class="info-content">
                                            <strong><?php echo e($count); ?> Soal</strong>
                                            <span><?php echo e($count > 0 ? 'TERUPLOAD' : 'BELUM ADA'); ?></span>
                                        </div>
                                        <?php if($count > 0): ?>
                                            <i class="fa-solid fa-circle-check check-icon" style="color: #10b981;"></i>
                                        <?php else: ?>
                                            <i class="fa-solid fa-circle-minus" style="color: #cbd5e1;"></i>
                                        <?php endif; ?>
                                    </div>
                                    
                                    
                                    <?php if($count > 0): ?>
                                        <form action="<?php echo e(route('pengajar.tryout.deleteAll')); ?>" method="POST" onsubmit="return confirm('Tarik kembali semua soal <?php echo e($subjectName); ?>?')">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="class_id" value="<?php echo e($assign->class_id); ?>">
                                            <input type="hidden" name="subject_name" value="<?php echo e($subjectName); ?>">
                                            <button type="submit" class="btn-action-delete" title="Tarik/Hapus Semua Draf Mapel Ini">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-right align-middle">
                                <a href="<?php echo e(route('pengajar.tryout.create', [$assign->class_id, $subjectName])); ?>" 
                                   class="btn-input-modern <?php echo e($count > 0 ? 'btn-has-content' : ''); ?>">
                                    <span><?php echo e($count > 0 ? 'EDIT / TAMBAH' : 'INPUT SOAL'); ?></span>
                                    <div class="icon-circle">
                                        <i class="fa-solid fa-<?php echo e($count > 0 ? 'pen-to-square' : 'pen-nib'); ?>"></i>
                                    </div>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 50px;">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" style="opacity: 0.2; margin-bottom: 15px;">
                                <p style="color: #94a3b8; font-weight: 700;">Belum ada penugasan soal untuk Anda.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
    .cp-page { padding: 10px; font-family: 'Montserrat', sans-serif; }
    
    /* Hero Section */
    .tm-hero-header {
        background: linear-gradient(135deg, #111827 0%, #1e293b 100%);
        border-radius: 28px; padding: 40px; color: white;
        display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;
    }
    .tm-main-title { font-size: 34px; font-weight: 900; }
    .tm-hero-summary { display: flex; gap: 15px; }
    .summary-card { background: rgba(255,255,255,0.04); padding: 18px 22px; border-radius: 20px; display: flex; align-items: center; gap: 15px; }
    .summary-card.highlight { background: #d90429; box-shadow: 0 10px 20px rgba(217, 4, 41, 0.3); }

    /* Table Design */
    .cp-main-card { background: white; border-radius: 30px; padding: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.02); }
    .cp-table-modern { width: 100%; border-collapse: separate; border-spacing: 0 15px; }
    .cp-table-modern th { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; padding: 0 20px; }
    .cp-table-modern td { padding: 20px; background: white; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; vertical-align: middle !important; }
    .cp-table-modern td:first-child { border-left: 1px solid #f1f5f9; border-radius: 20px 0 0 20px; }
    .cp-table-modern td:last-child { border-right: 1px solid #f1f5f9; border-radius: 0 20px 20px 0; }

    /* Content Styling */
    .program-info { display: flex; align-items: center; gap: 15px; }
    .program-icon-box { width: 44px; height: 44px; background: #f1f5f9; border-radius: 12px; display: grid; place-items: center; font-size: 18px; color: #475569; }
    .subject-tag { background: #fdf2f2; color: #d90429; padding: 8px 16px; border-radius: 12px; font-weight: 800; font-size: 11px; text-transform: uppercase; border: 1px solid #fee2e2; display: inline-flex; align-items: center; }

    /* Progress Box */
    .progress-container-flex { display: inline-flex; align-items: center; gap: 12px; }
    .contribution-info { 
        display: flex; align-items: center; gap: 15px; 
        padding: 10px 20px; background: #f8fafc; 
        border-radius: 15px; border: 1px solid #edf2f7;
        min-width: 170px; text-align: left;
    }
    .contribution-info.active { background: #f0fdf4; border-color: #dcfce7; }
    .info-content strong { display: block; font-size: 14px; color: #111827; line-height: 1.2; font-weight: 900; }
    .info-content span { font-size: 9px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }

    /* Action Buttons */
    .btn-action-delete { 
        background: #fee2e2; color: #ef4444; border: none; 
        width: 38px; height: 38px; border-radius: 10px; 
        cursor: pointer; transition: 0.3s; display: grid; place-items: center;
    }
    .btn-action-delete:hover { background: #ef4444; color: white; transform: scale(1.1); }

    .btn-input-modern {
        display: inline-flex; align-items: center; gap: 12px;
        background: #111827; color: white; padding: 6px 6px 6px 20px;
        border-radius: 15px; text-decoration: none; transition: 0.3s;
        white-space: nowrap; font-weight: 800; font-size: 12px;
    }
    .btn-input-modern.btn-has-content { background: #059669; }
    .btn-input-modern:hover { transform: translateX(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
    .btn-input-modern.btn-has-content:hover { background: #047857; }
    
    .icon-circle { width: 34px; height: 34px; background: rgba(255,255,255,0.15); border-radius: 10px; display: grid; place-items: center; font-size: 14px; }

    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .tm-alert-modern { padding: 15px 25px; border-radius: 16px; margin-bottom: 25px; background: #dcfce7; color: #15803d; font-weight: 800; border-left: 5px solid #22c55e; }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\Specta_Academy\BackEnd\resources\views/pengajar/tryout/index.blade.php ENDPATH**/ ?>