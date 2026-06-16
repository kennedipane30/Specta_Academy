<?php $__env->startSection('title', 'Rekap Nilai - Pilih Kelas'); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-page">
    
    
    <section class="cp-header">
        <div class="cp-header-left">
            <span class="cp-breadcrumb-capsule">Student Score Center</span>
            <h1>Rekap Nilai Tryout</h1>
            <p>Pilih program kelas di bawah ini untuk melihat daftar paket tryout dan hasil ujian siswa.</p>
        </div>
    </section>

    
    <div class="cp-grid">
        <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="cp-main-card class-card">
            <!-- Ikon Berpendar Teal -->
            <div class="class-icon-box">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            
            <h3><?php echo e($class->program_name); ?></h3>
            <p>ID Kelas: #<?php echo e($class->class_id); ?></p>
            
            <a href="<?php echo e(route('admin.scores.pilih_tryout', $class->class_id)); ?>" class="cp-primary-btn">
                LIHAT DAFTAR PAKET
            </a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
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

    .cp-page { 
        font-family: 'Montserrat', sans-serif; 
        padding: 10px; 
        animation: fadeIn 0.4s ease-out; 
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* ── Header ── */
    .cp-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }
    .cp-breadcrumb-capsule {
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
    .cp-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .cp-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }

    /* Selection Grid */
    .cp-grid {
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
        gap: 16px;
    }

    /* Kartu Pilihan Kelas */
    .cp-main-card.class-card {
        background: var(--spekta-white); 
        border-radius: 16px; 
        padding: 24px 20px; 
        border: 1px solid var(--border-soft); 
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .cp-main-card.class-card:hover {
        transform: translateY(-4px);
        border-color: var(--spekta-teal);
        box-shadow: 0 8px 24px rgba(46, 168, 171, 0.08);
    }

    .class-icon-box {
        width: 54px; 
        height: 54px; 
        background: var(--spekta-teal-light); 
        color: var(--spekta-teal); 
        border-radius: 12px; 
        display: grid; 
        place-items: center; 
        margin: 0 auto 16px; 
        font-size: 20px;
        transition: transform 0.2s ease;
    }
    .cp-main-card.class-card:hover .class-icon-box {
        transform: scale(1.08) rotate(3deg);
    }

    .cp-main-card h3 {
        font-size: 15px; 
        font-weight: 800; 
        color: var(--text-main); 
        margin: 0 0 4px 0;
        letter-spacing: -0.01em;
    }
    .cp-main-card p {
        font-size: 11px; 
        color: var(--text-muted); 
        margin: 0 0 20px 0;
        font-weight: 600;
    }

    /* Tombol Aksi */
    .cp-primary-btn {
        background: #1f2937; 
        display: block; 
        text-decoration: none; 
        color: var(--spekta-white) !important; 
        padding: 10px; 
        border-radius: 10px; 
        font-weight: 800; 
        font-size: 11px;
        letter-spacing: 0.04em;
        transition: all 0.2s ease;
    }
    .cp-primary-btn:hover {
        background: var(--spekta-red);
        box-shadow: 0 4px 12px rgba(229, 57, 53, 0.25);
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/tryout/pilih_kelas.blade.php ENDPATH**/ ?>