<?php $__env->startSection('title', 'Buat Paket Tryout'); ?>

<?php $__env->startSection('content'); ?>
<div class="sc-page">

    
    <section class="sc-header">
        <div class="sc-header-title">
            <span class="sc-breadcrumb-capsule">Admin Tryout Center</span>
            <h1>Buat Paket Tryout</h1>
            <p>Unggah soal tryout per mata pelajaran menggunakan berkas Excel/CSV secara efisien.</p>
        </div>
        <div class="sc-header-actions">
            <a href="<?php echo e(route('admin.tryout.index')); ?>" class="sc-secondary-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke List
            </a>
        </div>
    </section>

    
    <?php if(session('success')): ?>
        <div class="sc-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="sc-alert error">
            <i class="fa-solid fa-circle-xmark"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    
    <section class="sc-top-grid">
        <div class="sc-panel sc-form-panel">
            <div class="sc-panel-heading">
                <div class="sc-heading-icon"><i class="fa-solid fa-calendar-plus"></i></div>
                <h2>Pengaturan Paket Tryout</h2>
            </div>

            <form action="<?php echo e(route('admin.tryout.store')); ?>" method="POST" enctype="multipart/form-data" class="sc-form">
                <?php echo csrf_field(); ?>

                <div class="sc-input-row">
                    
                    <div class="sc-input-group">
                        <label>Judul Tryout <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="<?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="Contoh: TO UTBK 2025 - Gelombang 1" value="<?php echo e(old('title')); ?>" required>
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="error-msg" style="color: var(--spekta-red); font-size: 10px; font-weight: 700;"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="sc-input-group">
                        <label>Kelas <span class="text-danger">*</span></label>
                        <select name="class_id" class="<?php $__errorArgs = ['class_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Pilih Kelas</option>
                            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($class->class_id); ?>" <?php echo e(old('class_id') == $class->class_id ? 'selected' : ''); ?>>
                                    <?php echo e($class->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['class_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="error-msg" style="color: var(--spekta-red); font-size: 10px; font-weight: 700;"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="sc-input-row">
                    
                    <div class="sc-input-group">
                        <label>Durasi (menit) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_minutes" class="<?php $__errorArgs = ['duration_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('duration_minutes', 120)); ?>" required min="30">
                        <?php $__errorArgs = ['duration_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="error-msg" style="color: var(--spekta-red); font-size: 10px; font-weight: 700;"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="sc-input-group">
                        <label>Deskripsi</label>
                        <textarea name="description" class="<?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  rows="2" placeholder="Deskripsi singkat tentang tryout ini"><?php echo e(old('description')); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="error-msg" style="color: var(--spekta-red); font-size: 10px; font-weight: 700;"><?php echo e($message); ?></small>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="sc-input-row">
                    
                    <div class="sc-input-group">
                        <label>Harga</label>
                        <select name="is_free" id="is_free">
                            <option value="1">Gratis</option>
                            <option value="0">Berbayar</option>
                        </select>
                    </div>

                    
                    <div class="sc-input-group" id="price_container" style="display: none;">
                        <label>Harga (Rp)</label>
                        <input type="number" name="price" value="<?php echo e(old('price', 0)); ?>" min="0">
                    </div>

                    
                    <div class="sc-input-group">
                        <label>Maksimal Percobaan</label>
                        <input type="number" name="max_attempts" value="<?php echo e(old('max_attempts', 1)); ?>" min="1">
                    </div>
                </div>

                <hr class="my-4" style="border-top: 1px solid var(--border-soft);">

                
                <div class="sc-panel-heading">
                    <div class="sc-heading-icon" style="background: var(--spekta-teal-light); color: var(--spekta-teal);"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                    <div>
                        <h2>Upload Soal per Mata Pelajaran</h2>
                        <p>Setiap mata pelajaran diunggah dalam file Excel/CSV terpisah.</p>
                    </div>
                </div>

                <div id="file-inputs-container" class="space-y-4">
                    <div class="file-input-group sc-bento-row">
                        <div class="sc-input-group">
                            <label>Mata Pelajaran</label>
                            <input type="text" name="subjects[]" placeholder="Contoh: Matematika" required>
                        </div>
                        <div class="sc-input-group">
                            <label>File Excel/CSV</label>
                            <input type="file" name="excel_files[]" accept=".xlsx,.csv" required>
                        </div>
                        <div class="sc-input-group">
                            <label>Jumlah Soal</label>
                            <input type="number" placeholder="Otomatis" readonly disabled class="sc-input-readonly">
                        </div>
                        <div class="sc-btn-align">
                            <button type="button" class="sc-add-btn" onclick="addFileInput()">
                                <i class="fa-solid fa-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>

                
                <div class="sc-info-bento">
                    <h6><i class="fa-solid fa-circle-info"></i> Format Tabel Excel yang diterima:</h6>
                    <div style="overflow-x: auto; margin-top: 10px; border-radius: 8px; border: 1px solid var(--border-soft);">
                        <table class="sc-bento-table">
                            <thead>
                                <tr><th>No</th><th>Pertanyaan</th><th>Gambar Pertanyaan</th><th>Opsi A</th><th>Opsi B</th><th>Opsi C</th><th>Opsi D</th><th>Kunci</th><th>Pembahasan</th><th>Poin</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>1</td><td>Nilai x dari...</td><td>url_gambar.jpg</td><td>2</td><td>4</td><td>6</td><td>8</td><td>A</td><td>Gunakan rumus...</td><td>1</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <a href="<?php echo e(Route::has('admin.tryout.download-template') ? route('admin.tryout.download-template') : '#'); ?>" class="sc-template-btn">
                        <i class="fa-solid fa-cloud-arrow-down"></i> Download Template Excel
                    </a>
                </div>

                <div class="sc-footer-actions">
                    <button type="submit" class="sc-submit">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Upload & Buat Paket Tryout
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
let fileCount = 1;

function addFileInput() {
    fileCount++;
    const container = document.getElementById('file-inputs-container');
    const newDiv = document.createElement('div');
    newDiv.className = 'file-input-group sc-bento-row';
    newDiv.innerHTML = `
        <div class="sc-input-group">
            <label>Mata Pelajaran</label>
            <input type="text" name="subjects[]" placeholder="Contoh: Matematika" required>
        </div>
        <div class="sc-input-group">
            <label>File Excel/CSV</label>
            <input type="file" name="excel_files[]" accept=".xlsx,.csv" required>
        </div>
        <div class="sc-input-group">
            <label>Jumlah Soal</label>
            <input type="number" placeholder="Otomatis" readonly disabled class="sc-input-readonly">
        </div>
        <div class="sc-btn-align">
            <button type="button" class="sc-del-btn" onclick="this.closest('.file-input-group').remove()">
                <i class="fa-solid fa-trash-can"></i> Hapus
            </button>
        </div>
    `;
    container.appendChild(newDiv);
}

// Tampilkan/sembunyikan field harga
document.getElementById('is_free').addEventListener('change', function() {
    const priceContainer = document.getElementById('price_container');
    if (this.value === '0') {
        priceContainer.style.display = 'block';
    } else {
        priceContainer.style.display = 'none';
    }
});
</script>

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

    .sc-page { 
        font-family: 'Montserrat', sans-serif; 
        padding: 10px; 
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* ── Header Minimalis ── */
    .sc-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: flex-end; 
        margin-bottom: 24px; 
        border-bottom: 1px solid var(--border-soft); 
        padding-bottom: 20px; 
    }
    .sc-breadcrumb-capsule {
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
    .sc-header h1 { font-size: 24px; font-weight: 900; color: var(--text-main); margin: 0; letter-spacing: -0.02em; }
    .sc-header p { margin: 0; color: var(--text-muted); font-size: 13px; font-weight: 600;}

    .sc-secondary-btn {
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
    .sc-secondary-btn:hover {
        background: var(--spekta-gray-light);
        color: var(--text-main);
        border-color: var(--spekta-gray);
    }

    /* Alerts */
    .sc-alert { display: flex; gap: 10px; align-items: center; padding: 12px 16px; border-radius: 12px; margin-bottom: 24px; font-weight: 800; font-size: 13px;}
    .sc-alert.success { background: #e6f7ed; color: #15803d; border: 1px solid #bbf7d0;}
    .sc-alert.error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

    /* Form Panel Card */
    .sc-top-grid { display: block; margin-bottom: 24px; }
    .sc-panel { background: var(--spekta-white); border-radius: 16px; padding: 20px; border: 1px solid var(--border-soft); box-shadow: 0 4px 15px rgba(0,0,0,0.01); }

    .sc-panel-heading { display: flex; gap: 12px; align-items: center; margin-bottom: 18px;}
    .sc-heading-icon { width: 38px; height: 38px; background: var(--spekta-red-light); color: var(--spekta-red); display: grid; place-items: center; border-radius: 10px; font-size: 16px;}
    .sc-panel-heading h2 { margin: 0; font-size: 15px; font-weight: 800; color: var(--text-main); }
    .sc-panel-heading p { margin: 4px 0 0; font-size: 11px; color: var(--text-muted); font-weight: 600; }

    .sc-form { display: grid; gap: 15px; }
    .sc-input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .sc-input-group { display: flex; flex-direction: column; gap: 8px; }
    .sc-input-group label { font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.02em; }
    .sc-input-group input, .sc-input-group select, .sc-input-group textarea { padding: 11px; border-radius: 10px; border: 1px solid var(--border-soft); background: var(--spekta-gray-light); font-weight: 600; outline: none; transition: all 0.25s; font-family: inherit; font-size: 12px; }
    .sc-input-group input:focus, .sc-input-group select:focus, .sc-input-group textarea:focus { border-color: var(--spekta-teal); background: var(--spekta-white); box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12); }
    
    .sc-input-readonly { background: #f9fafb !important; border-color: var(--border-soft) !important; color: var(--text-muted) !important; cursor: not-allowed; }

    /* Bento Row for Multiple File Uploader */
    .sc-bento-row {
        display: grid;
        grid-template-columns: 1.5fr 2fr 1fr 100px;
        gap: 15px;
        padding: 16px;
        background: var(--spekta-gray-light);
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        align-items: center;
    }
    .sc-btn-align { padding-top: 18px; }

    /* Buttons */
    .sc-add-btn, .sc-del-btn {
        width: 100%;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    .sc-add-btn { background: var(--spekta-teal-light); color: var(--spekta-teal); }
    .sc-add-btn:hover { background: var(--spekta-teal); color: var(--spekta-white); }
    .sc-del-btn { background: var(--spekta-red-light); color: var(--spekta-red); }
    .sc-del-btn:hover { background: #fecaca; color: #991b1b; }

    .sc-submit { background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%); color: var(--spekta-white); border: none; padding: 12px 24px; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.2s; font-size: 13px; display: inline-flex; gap: 8px; align-items: center; font-family: inherit; box-shadow: 0 4px 10px rgba(229, 57, 53, 0.15);}
    .sc-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 15px rgba(229, 57, 53, 0.25); }

    /* Bento Info Box */
    .sc-info-bento {
        padding: 16px;
        background: #fffbeb;
        border: 1px solid #fef3c7;
        border-radius: 12px;
    }
    .sc-info-bento h6 { font-size: 11px; font-weight: 800; color: #92400e; margin: 0; text-transform: uppercase; display: flex; align-items: center; gap: 6px; }
    .sc-bento-table { width: 100%; border-collapse: collapse; background: var(--spekta-white); font-size: 11px; }
    .sc-bento-table th { background: var(--spekta-gray-light); padding: 8px; font-weight: 800; color: var(--text-muted); border-bottom: 1px solid var(--border-soft); }
    .sc-bento-table td { padding: 8px; border-bottom: 1px solid var(--spekta-gray-light); color: var(--text-main); font-weight: 600; }
    
    .sc-template-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--spekta-teal-light);
        color: var(--spekta-teal);
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        margin-top: 14px;
        transition: all 0.2s;
    }
    .sc-template-btn:hover { background: var(--spekta-teal); color: var(--spekta-white); }

    .sc-footer-actions { display: flex; justify-content: flex-end; margin-top: 10px; }

    @media (max-width: 768px) {
        .sc-input-row { grid-template-columns: 1fr; }
        .sc-bento-row { grid-template-columns: 1fr; gap: 10px; }
        .sc-btn-align { padding-top: 0; }
        .sc-footer-actions .sc-submit { width: 100%; justify-content: center; }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\perkuliahan\PA 2 - code\PAAAAA2\BackEnd\resources\views/admin/tryout/create.blade.php ENDPATH**/ ?>