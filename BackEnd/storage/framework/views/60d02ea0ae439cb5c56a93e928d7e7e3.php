<?php $__env->startSection('title', 'Edit Jadwal'); ?>
<?php $__env->startSection('subtitle', 'Sistem Manajemen Terpadu Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<div class="sc-page">
    <section class="sc-header">
        <div class="sc-header-title">
            <nav class="sc-breadcrumb">
                <a href="<?php echo e(route('admin.dashboard')); ?>">Dashboard</a>
                <i class="fa-solid fa-chevron-right"></i>
                <a href="<?php echo e(route('admin.jadwal.index')); ?>">Manajemen Akademik</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Edit Jadwal</span>
            </nav>
            <div class="sc-title-wrapper">
                <h1>Edit Jadwal Pembelajaran</h1>
                <span class="sc-badge-live">
                    <span class="dot-pulse"></span> Terkoneksi Matrix
                </span>
            </div>
            <p>Perbarui informasi jadwal yang telah dipublikasikan.</p>
        </div>
    </section>

    <?php if(session('error')): ?>
        <div class="sc-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span><?php echo e(session('error')); ?></span>
        </div>
    <?php endif; ?>

    <section class="sc-top-grid">
        <div class="sc-panel sc-form-panel">
            <div class="sc-panel-heading">
                <div class="sc-heading-icon"><i class="fa-solid fa-pen-to-square"></i></div>
                <h2>Edit Jadwal</h2>
            </div>

            <form action="<?php echo e(route('admin.jadwal.update', $schedule->schedule_id)); ?>" method="POST" class="sc-form" id="editScheduleForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <input type="hidden" name="teacher_id" id="teacherIdHidden" value="<?php echo e($schedule->teacher_id); ?>">
                <input type="hidden" name="title" id="autoTitle" value="<?php echo e($schedule->subject_name ?? $schedule->title); ?>">

                <div class="sc-input-row">
                    <div class="sc-input-group">
                        <label>Program</label>
                        <select name="class_id" id="classSelect" required>
                            <option value="">Pilih Program</option>
                            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($class->class_id); ?>"
                                    <?php echo e($class->class_id == $schedule->class_id ? 'selected' : ''); ?>>
                                    <?php echo e($class->program_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="sc-input-group">
                        <label>Mata Pelajaran</label>
                        <select name="subject_id" id="subjectSelect" required>
                            <option value="">Pilih Mata Pelajaran</option>
                            <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($subject->subject_id); ?>"
                                    <?php echo e($subject->subject_id == $schedule->subject_id ? 'selected' : ''); ?>>
                                    <?php echo e($subject->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="sc-input-group">
                    <label>Pengajar Terdaftar</label>
                    <input type="text" id="teacherNameDisplay" class="sc-input-readonly"
                           readonly value="<?php echo e($schedule->teacher->name ?? 'Akan terisi otomatis...'); ?>">
                </div>

                <div class="sc-input-row three-col">
                    <div class="sc-input-group">
                        <label>Hari / Tanggal</label>
                        <input type="date" name="date" id="scheduleDate"
                               value="<?php echo e($schedule->date); ?>" required min="<?php echo e(date('Y-m-d')); ?>">
                    </div>
                    <div class="sc-input-group">
                        <label>Jam Mulai</label>
                        <input type="time" name="start_time" id="startTime"
                               value="<?php echo e($schedule->start_time); ?>" required>
                    </div>
                    <div class="sc-input-group">
                        <label>Jam Selesai</label>
                        <input type="time" name="end_time" id="endTime"
                               value="<?php echo e($schedule->end_time); ?>" required>
                    </div>
                </div>

                <div class="sc-form-actions">
                    <a href="<?php echo e(route('admin.jadwal.index')); ?>" class="sc-btn sc-btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Batal
                    </a>
                    <button type="submit" class="sc-submit">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>

<style>
    .sc-page { font-family: 'Montserrat', sans-serif; padding: 10px; animation: fadeIn 0.4s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .sc-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; border-bottom: 1px solid #e5e7eb; padding-bottom: 20px; }
    .sc-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 700; color: #6b7280; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.02em; }
    .sc-breadcrumb a { color: #2ea8ab; text-decoration: none; }
    .sc-breadcrumb i { font-size: 8px; color: #9e9e9e; }
    .sc-title-wrapper { display: flex; align-items: center; gap: 15px; margin-bottom: 8px; }
    .sc-header h1 { font-size: 24px; font-weight: 900; color: #1f2937; margin: 0; letter-spacing: -0.02em; }
    .sc-badge-live { background: #f3f4f6; padding: 4px 12px; border-radius: 20px; font-size: 10px; font-weight: 800; color: #6b7280; display: flex; align-items: center; gap: 6px; border: 1px solid #e5e7eb; }
    .dot-pulse { width: 6px; height: 6px; background: #22c55e; border-radius: 50%; animation: pulse 1.5s infinite; }
    @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); } 70% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); } 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); } }
    .sc-header p { margin: 0; color: #6b7280; font-size: 13px; font-weight: 600; }

    .sc-alert { display: flex; gap: 10px; align-items: center; padding: 12px 18px; border-radius: 12px; margin-bottom: 24px; font-weight: 800; font-size: 13px; }
    .sc-alert.error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    .sc-top-grid { display: block; margin-bottom: 24px; }
    .sc-panel { background: #ffffff; border-radius: 16px; padding: 20px; border: 1px solid #e5e7eb; box-shadow: 0 4px 15px rgba(0,0,0,0.01); }
    .sc-panel-heading { display: flex; gap: 12px; align-items: center; margin-bottom: 18px; }
    .sc-heading-icon { width: 38px; height: 38px; background: rgba(229, 57, 53, 0.06); color: #e53935; display: grid; place-items: center; border-radius: 10px; font-size: 16px; }
    .sc-panel-heading h2 { margin: 0; font-size: 15px; font-weight: 800; }

    .sc-input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .sc-input-row.three-col { grid-template-columns: 1fr 1fr 1fr; }
    .sc-input-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 15px; }
    .sc-input-group label { font-size: 10px; font-weight: 800; color: #6b7280; text-transform: uppercase; letter-spacing: 0.02em; }
    .sc-input-group input, .sc-input-group select { padding: 11px; border-radius: 10px; border: 1px solid #e5e7eb; background: #f3f4f6; font-weight: 600; outline: none; transition: all 0.25s; font-family: inherit; font-size: 12px; }
    .sc-input-group input:focus, .sc-input-group select:focus { border-color: #2ea8ab; background: #ffffff; box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12); }
    .sc-input-readonly { background: #f9fafb !important; border-color: #e5e7eb !important; color: #6b7280 !important; cursor: not-allowed; box-shadow: none !important; }

    .sc-form-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 10px; }
    .sc-btn { padding: 12px 20px; border-radius: 12px; font-weight: 800; cursor: pointer; font-family: inherit; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border: none; transition: 0.2s; }
    .sc-btn-secondary { background: #f3f4f6; color: #1f2937; border: 1px solid #e5e7eb; }
    .sc-btn-secondary:hover { background: #e5e7eb; }
    .sc-submit { background: linear-gradient(135deg, #e53935 0%, #c5352c 100%); color: #ffffff; border: none; padding: 12px 20px; border-radius: 12px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 15px rgba(229, 57, 53, 0.2); transition: 0.2s; display: inline-flex; gap: 8px; align-items: center; font-family: inherit; font-size: 13px; }
    .sc-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(229, 57, 53, 0.3); }

    @media (max-width: 768px) {
        .sc-input-row, .sc-input-row.three-col { grid-template-columns: 1fr; }
        .sc-header { flex-direction: column; align-items: flex-start; gap: 15px; }
        .sc-form-actions { flex-direction: column; }
        .sc-form-actions .sc-btn, .sc-form-actions .sc-submit { justify-content: center; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const BASE_URL = "<?php echo e(url('/')); ?>";
        const classSelect = document.getElementById('classSelect');
        const subjectSelect = document.getElementById('subjectSelect');
        const teacherIdHidden = document.getElementById('teacherIdHidden');
        const teacherNameDisplay = document.getElementById('teacherNameDisplay');
        const autoTitle = document.getElementById('autoTitle');

        const initialSubjectId = subjectSelect.value;
        const initialClassId = classSelect.value;

        classSelect.addEventListener('change', function() {
            const classId = this.value;

            subjectSelect.disabled = true;
            subjectSelect.innerHTML = '<option value="">Memuat...</option>';
            teacherNameDisplay.value = '';
            teacherIdHidden.value = '';
            autoTitle.value = '';

            if (classId) {
                fetch(`${BASE_URL}/admin/jadwal/get-subjects/${classId}`)
                    .then(res => res.json())
                    .then(data => {
                        subjectSelect.disabled = false;
                        subjectSelect.innerHTML = '<option value="">Pilih Mata Pelajaran</option>';

                        if (data.length > 0) {
                            data.forEach(sub => {
                                const selected = (sub.subject_id == initialSubjectId && classId == initialClassId) ? 'selected' : '';
                                subjectSelect.innerHTML += `<option value="${sub.subject_id}" ${selected}>${sub.name}</option>`;
                            });

                            if (subjectSelect.value) {
                                subjectSelect.dispatchEvent(new Event('change'));
                            }
                        } else {
                            subjectSelect.innerHTML = '<option value="">Belum ada mapel</option>';
                        }
                    })
                    .catch(() => {
                        subjectSelect.disabled = false;
                        subjectSelect.innerHTML = '<option value="">Gagal memuat mapel</option>';
                    });
            } else {
                subjectSelect.innerHTML = '<option value="">Pilih program dahulu</option>';
            }
        });

        subjectSelect.addEventListener('change', function() {
            const classId = classSelect.value;
            const subjectId = this.value;

            const selectedText = subjectSelect.options[subjectSelect.selectedIndex]?.text || '';
            autoTitle.value = selectedText;

            if (classId && subjectId) {
                teacherNameDisplay.value = 'Mencari pengajar...';

                fetch(`${BASE_URL}/admin/jadwal/get-teacher/${classId}/${subjectId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.teacher_id) {
                            teacherNameDisplay.value = data.teacher_name;
                            teacherIdHidden.value = data.teacher_id;
                        } else {
                            teacherNameDisplay.value = 'Guru belum ditugaskan';
                            teacherIdHidden.value = '';
                        }
                    })
                    .catch(() => {
                        teacherNameDisplay.value = 'Error mengambil data guru';
                    });
            }
        });

        // Trigger untuk load data awal jika ada class yang dipilih
        if (classSelect.value) {
            classSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/jadwal/edit.blade.php ENDPATH**/ ?>