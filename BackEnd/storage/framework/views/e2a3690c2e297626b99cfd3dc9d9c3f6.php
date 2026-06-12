<?php $__env->startSection('title', 'Kurasi Paket Tryout - Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-page">

    
    <?php if(session('error')): ?>
        <div class="sc-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    
    <section class="cp-header">
        <div class="cp-header-left">
            <span class="cp-breadcrumb-capsule">Reviewing Drafts</span>
            <h1>Kurasi Soal: <span style="color: var(--spekta-teal);"><?php echo e($class->program_name); ?></span></h1>
            <p>Satukan draf soal terbaik dari para pengajar, atur batas durasi, lalu terbitkan ke aplikasi mobile siswa.</p>
        </div>
        
        <div class="cp-header-actions">
            <!-- Hitungan Draf Rapi -->
            <div class="cp-draft-badge-card">
                <strong><?php echo e($drafts->count()); ?></strong>
                <span>Draf Soal</span>
            </div>
            
            <a href="<?php echo e(route('admin.tryout.index')); ?>" class="cp-secondary-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Master
            </a>
        </div>
    </section>

    
    <section class="cp-publish-panel">
        <div class="cp-panel-heading">
            <div class="cp-heading-icon"><i class="fa-solid fa-paper-plane"></i></div>
            <div>
                <h2>Pengaturan Publikasi Paket TO</h2>
                <p>Masukkan judul paket tryout dan alokasikan durasi waktu sebelum diterbitkan ke siswa.</p>
            </div>
        </div>

        <form action="<?php echo e(route('admin.tryout.publish')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="class_id" value="<?php echo e($class->class_id); ?>">
            
            <div class="cp-input-row">
                <div class="cp-input-group">
                    <label>Judul Paket Tryout Resmi</label>
                    <input type="text" name="title" placeholder="Contoh: Tryout Akbar Nasional 2024" required>
                </div>
                <div class="cp-input-group">
                    <label>Durasi (Menit)</label>
                    <input type="number" name="duration" value="90" required min="1">
                </div>
                <div class="cp-btn-align">
                    <button type="submit" class="cp-btn-publish">
                        <i class="fa-solid fa-paper-plane"></i> PUBLISH KE MOBILE
                    </button>
                </div>
            </div>
        </form>
    </section>

    
    <div class="cp-questions-wrapper">
        <h4 class="cp-section-title">Daftar Detail Soal (Tinjauan Admin)</h4>
        
        <?php $__currentLoopData = $drafts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="soal-card">
                <div class="soal-card-header">
                    <div class="header-tags">
                        <span class="soal-badge">SOAL #<?php echo e($index + 1); ?></span>
                        <span class="subject-badge"><?php echo e($d->subject_name); ?></span>
                    </div>
                    
                    <form action="<?php echo e(route('admin.tryout.draft.delete', $d->id)); ?>" method="POST" onsubmit="return confirm('Hapus soal ini dari draf?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn-delete-soal">
                            <i class="fa-solid fa-trash-can"></i> Hapus Soal
                        </button>
                    </form>
                </div>

                <div class="soal-body">
                    <div class="soal-text"><?php echo $d->question; ?></div>
                    
                    <div class="options-grid">
                        <?php $__currentLoopData = ['a','b','c','d','e']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php 
                                $isCorrect = (strtoupper($opt) == strtoupper($d->correct_answer)); 
                            ?>
                            <div class="option-item <?php echo e($isCorrect ? 'correct' : ''); ?>">
                                <span class="opt-label"><?php echo e(strtoupper($opt)); ?></span>
                                <span class="opt-text"><?php echo e($d->{'option_'.$opt}); ?></span>
                                <?php if($isCorrect): ?> 
                                    <i class="fa-solid fa-circle-check check-icon"></i> 
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <?php if($d->explanation): ?>
                        <div class="explanation-box">
                            <strong>PEMBAHASAN:</strong>
                            <p><?php echo e($d->explanation); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
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

    .cp-draft-badge-card {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        padding: 8px 14px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
    }
    .cp-draft-badge-card strong { font-size: 18px; font-weight: 900; color: var(--spekta-red); }
    .cp-draft-badge-card span { font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; }

    .cp-secondary-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid var(--border-soft);
        background: var(--spekta-white);
        color: var(--text-muted);
        border-radius: 12px;
        padding: 10px 16px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s;
    }
    .cp-secondary-btn:hover {
        background: var(--spekta-gray-light);
        color: var(--text-main);
        border-color: var(--spekta-gray);
    }

    .cp-header-actions { display: flex; align-items: center; gap: 12px; }

    /* Alerts */
    .sc-alert { display: flex; gap: 10px; align-items: center; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; font-weight: 800; font-size: 13px;}
    .sc-alert.error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

    /* Publish Panel */
    .cp-publish-panel { background: var(--spekta-white); border-radius: 16px; padding: 20px; border: 1px solid var(--border-soft); box-shadow: 0 4px 15px rgba(0,0,0,0.01); margin-bottom: 24px; }
    .cp-panel-heading { display: flex; gap: 12px; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--spekta-gray-light); }
    .cp-heading-icon { width: 38px; height: 38px; background: var(--spekta-red-light); color: var(--spekta-red); display: grid; place-items: center; border-radius: 10px; font-size: 16px;}
    .cp-panel-heading h2 { margin: 0; font-size: 15px; font-weight: 800; color: var(--text-main); }
    .cp-panel-heading p { margin: 4px 0 0; font-size: 11px; color: var(--text-muted); font-weight: 600; }

    .cp-input-row { display: grid; grid-template-columns: 2fr 1fr 1.5fr; gap: 15px; align-items: end; }
    .cp-input-group { display: flex; flex-direction: column; gap: 8px; }
    .cp-input-group label { font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.02em; }
    .cp-input-group input { padding: 11px; border-radius: 10px; border: 1px solid var(--border-soft); background: var(--spekta-gray-light); font-weight: 600; outline: none; transition: all 0.25s; font-family: inherit; font-size: 12px; }
    .cp-input-group input:focus { border-color: var(--spekta-teal); background: var(--spekta-white); box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12); }
    .cp-btn-align { padding-top: 18px; }

    .cp-btn-publish {
        width: 100%;
        height: 40px;
        background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%);
        color: var(--spekta-white);
        border: none;
        border-radius: 10px;
        font-weight: 800;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: 0.2s;
        box-shadow: 0 4px 10px rgba(229, 57, 53, 0.15);
        cursor: pointer;
    }
    .cp-btn-publish:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(229, 57, 53, 0.25);
    }

    /* ── Question Cards ── */
    .cp-questions-wrapper { display: flex; flex-direction: column; gap: 16px; }
    .cp-section-title { font-weight: 900; color: var(--text-main); font-size: 15px; margin: 0 0 4px 4px; }
    
    .soal-card {
        background: var(--spekta-white);
        border-radius: 16px;
        padding: 20px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
        transition: all 0.25s ease;
    }
    .soal-card:hover {
        border-color: var(--spekta-gray);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.03);
    }
    .soal-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--spekta-gray-light);
    }
    .header-tags { display: flex; align-items: center; gap: 10px; }
    .soal-badge {
        background: #1f2937;
        color: var(--spekta-white);
        font-size: 10px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 6px;
    }
    .subject-badge {
        background: var(--spekta-red-light);
        color: var(--spekta-red-dark);
        font-size: 10px;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .btn-delete-soal {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-delete-soal:hover { color: var(--spekta-red); }

    .soal-text {
        font-size: 14px;
        color: var(--text-main);
        line-height: 1.6;
        margin-bottom: 20px;
        font-weight: 700;
    }
    .options-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
    }
    .option-item {
        display: flex;
        align-items: center;
        padding: 10px 14px;
        background: var(--spekta-gray-light);
        border: 1.5px solid var(--border-soft);
        border-radius: 10px;
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 700;
        position: relative;
    }
    .option-item.correct {
        background: var(--spekta-teal-light);
        border-color: var(--spekta-teal);
        color: var(--spekta-teal);
        font-weight: 800;
        box-shadow: 0 2px 6px rgba(46, 168, 171, 0.1);
    }
    .opt-label {
        font-weight: 800;
        margin-right: 10px;
        opacity: 0.4;
    }
    .check-icon {
        position: absolute;
        right: 14px;
        color: var(--spekta-teal);
        font-size: 14px;
    }

    /* Explanation */
    .explanation-box {
        margin-top: 20px;
        padding: 14px;
        background: #fffbeb;
        border-radius: 12px;
        border: 1px solid #fef3c7;
    }
    .explanation-box strong {
        display: block;
        font-size: 9px;
        color: #92400e;
        margin-bottom: 6px;
        letter-spacing: 1px;
    }
    .explanation-box p {
        font-size: 12px;
        color: #78350f;
        margin: 0;
        line-height: 1.6;
        font-weight: 600;
    }

    @media (max-width: 1000px) {
        .cp-input-row { grid-template-columns: 1fr; gap: 10px; }
        .cp-btn-align { padding-top: 0; }
        .options-grid { grid-template-columns: 1fr; }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\perkuliahan\PA 2 - code\PAAAAA2\BackEnd\resources\views/admin/tryout/review_drafts.blade.php ENDPATH**/ ?>