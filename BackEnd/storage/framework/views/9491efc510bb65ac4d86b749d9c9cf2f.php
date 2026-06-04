<?php $__env->startSection('content'); ?>
<div class="cp-page">

    
    <section class="cp-header">
        <div>
            <span class="cp-tagline">
                SPEKTA COURSE MANAGER
            </span>
            <h1 class="cp-title">
                Kelola Materi: <?php echo e($subject_name); ?>

            </h1>
            <p class="cp-subtitle">
                Program: <strong><?php echo e($class->program_name); ?></strong>
            </p>
        </div>

        <a href="<?php echo e(route('pengajar.materi.index')); ?>" class="cp-back-btn">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </section>

    
    <?php if(session('success')): ?>
        <div style="padding: 15px; background: #dcfce7; color: #15803d; border-radius: 12px; margin-bottom: 20px; font-weight: 700;">
            <i class="fa-solid fa-check-circle"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div style="padding: 15px; background: #fee2e2; color: #dc2626; border-radius: 12px; margin-bottom: 20px; font-weight: 700;">
            <i class="fa-solid fa-circle-exclamation"></i> <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    
    <section class="cp-card">
        <div class="cp-card-head">
            <h2 id="form-title">Tambah atau Perbarui Materi</h2>
            <p>Pilih minggu, isi judul, dan upload PDF. Jika minggu sudah ada, sistem akan otomatis memperbarui data lama.</p>
        </div>

        <form action="<?php echo e(route('pengajar.materi.store', $class->class_id)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            
            <!-- 🔥 PENTING: material_name dikirim agar subject_name di Go tidak kosong -->
            <input type="hidden" name="material_name" value="<?php echo e($subject_name); ?>">

            <div class="cp-form-grid">
                <div>
                    <label>Minggu Ke</label>
                    <select name="week" id="input-week" class="cp-input" required>
                        <?php for($i=1; $i<=20; $i++): ?>
                            <option value="<?php echo e($i); ?>">Minggu <?php echo e($i); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div>
                    <label>Judul Materi</label>
                    <input id="input-title" type="text" name="title" class="cp-input" placeholder="Contoh: Pengenalan Umum" required>
                </div>

                <div>
                    <label>Upload PDF (Kosongkan jika hanya ubah judul)</label>
                    <input type="file" name="file_pdf" class="cp-input" accept=".pdf">
                </div>

                <button type="submit" class="cp-btn">
                    <i class="fa-solid fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </section>

    
    <section class="cp-card">
        <div class="cp-card-head">
            <h2>Daftar Materi</h2>
        </div>

        <div class="table-responsive">
            <table class="cp-table">
                <thead>
                    <tr>
                        <th>Minggu</th>
                        <th>Judul Materi</th>
                        <th>File</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $materis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <span class="badge-week">MG-<?php echo e($item->week); ?></span>
                        </td>
                        <td>
                            <div class="materi-info">
                                <strong><?php echo e($item->title); ?></strong>
                                <small><?php echo e($item->material_name); ?></small>
                            </div>
                        </td>
                        <td>
                            <?php if($item->file_path): ?>
                            <a target="_blank" href="<?php echo e(asset('storage/'.$item->file_path)); ?>" class="badge-file">
                                <i class="fa-solid fa-file-pdf"></i> PDF Tersedia
                            </a>
                            <?php else: ?>
                            <span style="color: #94a3b8; font-size: 12px;">Tanpa File</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-group">
                                <?php if($item->file_path): ?>
                                <a href="<?php echo e(asset('storage/'.$item->file_path)); ?>" download class="btn-icon blue">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                                <?php endif; ?>

                                <button type="button" onclick="fillEditForm('<?php echo e($item->title); ?>', '<?php echo e($item->week); ?>')" class="btn-icon dark">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <form method="POST" action="<?php echo e(route('pengajar.materi.destroy', $item->material_id)); ?>" onsubmit="return confirm('Hapus materi ini? Data di aplikasi HP juga akan terhapus.')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn-icon red">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="empty-state" style="text-align: center; padding: 40px; color: #94a3b8;">
                            Belum ada materi untuk mata pelajaran ini.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
    :root {
        --red: #d90429;
        --dark: #0f172a;
        --gray: #64748b;
    }

    .cp-page { padding: 24px; }

    /* HEADER */
    .cp-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .cp-title { font-size: 42px; font-weight: 900; margin: 10px 0; }
    .cp-tagline { font-size: 13px; font-weight: 800; letter-spacing: 2px; color: #64748b; }
    .cp-subtitle { color: #475569; }

    .cp-back-btn {
        padding: 12px 18px;
        background: white;
        border-radius: 12px;
        text-decoration: none;
        color: #111827;
        font-weight: 700;
        border: 1px solid #e2e8f0;
        transition: .3s;
    }

    .cp-back-btn:hover { background: #111827; color: white; }

    /* CARD */
    .cp-card {
        background: white;
        padding: 30px;
        border-radius: 24px;
        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,.05);
    }

    .cp-card-head { margin-bottom: 25px; }
    .cp-card-head h2 { font-size: 30px; font-weight: 800; margin-bottom: 10px; }

    /* FORM */
    .cp-form-grid {
        display: grid;
        grid-template-columns: 1fr 2fr 2fr auto;
        gap: 18px;
        align-items: end;
    }

    .cp-input {
        width: 100%;
        padding: 14px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .cp-btn {
        height: 50px;
        padding: 0 25px;
        border: none;
        background: var(--red);
        color: white;
        font-weight: 800;
        border-radius: 12px;
        cursor: pointer;
    }

    .cp-btn:hover { background: #b00322; }

    /* TABLE */
    .cp-table { width: 100%; border-collapse: collapse; }
    .cp-table th { padding: 18px; font-size: 12px; color: #94a3b8; text-transform: uppercase; text-align: left; }
    .cp-table td { padding: 22px 18px; border-top: 1px solid #f1f5f9; }
    .cp-table tr:hover { background: #fafafa; }

    /* BADGE */
    .badge-week { padding: 8px 12px; background: #f8fafc; border-radius: 10px; font-weight: 800; font-size: 12px; }
    .badge-file { display: inline-flex; gap: 8px; padding: 8px 14px; background: #ecfdf5; color: #10b981; border-radius: 10px; text-decoration: none; font-weight: 800; }
    .badge-file:hover { background: #10b981; color: white; }

    .materi-info strong { display: block; font-size: 15px; }
    .materi-info small { color: #94a3b8; }

    /* ACTION */
    .action-group { display: flex; justify-content: flex-end; gap: 8px; }
    .btn-icon { width: 40px; height: 40px; border: none; border-radius: 10px; display: grid; place-items: center; cursor: pointer; }
    .blue { background: #e0f2fe; color: #0284c7; }
    .dark { background: #111827; color: white; }
    .red { background: #fee2e2; color: #dc2626; }
    .btn-icon:hover { transform: translateY(-2px); }

    /* RESPONSIVE */
    @media(max-width:900px){
        .cp-form-grid { grid-template-columns: 1fr; }
        .cp-table { min-width: 700px; }
        .table-responsive { overflow: auto; }
    }
</style>

<script>
    function fillEditForm(title, week) {
        document.getElementById('input-title').value = title;
        document.getElementById('input-week').value = week;
        document.getElementById('form-title').innerText = "Edit Materi Minggu " + week;

        // Berikan visual feedback bahwa user sedang mengedit
        window.scrollTo({ top: 0, behavior: 'smooth' });
        document.getElementById('input-title').focus();
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\Specta_Academy\BackEnd\resources\views/pengajar/materi/pilih.blade.php ENDPATH**/ ?>