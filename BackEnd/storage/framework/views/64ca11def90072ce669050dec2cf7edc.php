<?php $__env->startSection('title', 'Schedule Management'); ?>
<?php $__env->startSection('subtitle', 'Sistem Manajemen Terpadu Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $userRole = auth()->user()->role->name ?? 'admin';
    $isAdmin = $userRole === 'admin';
    $isTeacher = $userRole === 'teacher';
    $isStudent = $userRole === 'student';
?>

<div class="sc-page">

    
    <section class="sc-header">
        <div class="sc-header-title">
            <nav class="sc-breadcrumb">
                <a href="#">Dashboard</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span>Manajemen Akademik</span>
            </nav>
            <div class="sc-title-wrapper">
                <h1>
                    <?php if($isAdmin): ?> Atur Waktu Pembelajaran
                    <?php elseif($isTeacher): ?> Jadwal Mengajar Saya
                    <?php else: ?> Jadwal Kelas Saya
                    <?php endif; ?>
                </h1>
                <span class="sc-badge-live">
                    <span class="dot-pulse"></span> Terkoneksi Matrix
                </span>
            </div>
            <p>Tentukan hari dan jam belajar berdasarkan penugasan pengajar yang sudah diatur sebelumnya.</p>
        </div>
    </section>

    <?php if(session('success')): ?>
        <div class="sc-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    
    <?php if($isAdmin): ?>
    <section class="sc-top-grid">
        <div class="sc-panel sc-form-panel">
            <div class="sc-panel-heading">
                <div class="sc-heading-icon"><i class="fa-solid fa-calendar-plus"></i></div>
                <h2>Buat Jadwal Baru</h2>
            </div>

            <form action="<?php echo e(route('admin.jadwal.store')); ?>" method="POST" class="sc-form" id="scheduleForm">
                <?php echo csrf_field(); ?>

                
                <input type="hidden" name="teacher_id" id="teacherIdHidden">
                <input type="hidden" name="title" id="autoTitle">

                <div class="sc-input-row">
                    
                    <div class="sc-input-group">
                        <label>Program</label>
                        <select name="class_id" id="classSelect" required>
                            <option value="">Pilih Program</option>
                            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($class->class_id); ?>"><?php echo e($class->program_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="sc-input-group">
                        <label>Mata Pelajaran</label>
                        <select name="subject_id" id="subjectSelect" required disabled>
                            <option value="">Pilih program dahulu</option>
                        </select>
                    </div>
                </div>

                
                <div class="sc-input-group">
                    <label>Pengajar Terdaftar</label>
                    <input type="text" id="teacherNameDisplay" class="sc-input-readonly" readonly placeholder="Akan terisi otomatis berdasarkan mata pelajaran...">
                </div>

                
                <div class="sc-input-row three-col">
                    <div class="sc-input-group">
                        <label>Hari / Tanggal</label>
                        <input type="date" name="date" id="scheduleDate" required min="<?php echo e(date('Y-m-d')); ?>">
                        <small class="error-msg" id="dateError" style="color: #b91c1c; font-size: 10px; display: none; margin-top: 4px;">Tanggal tidak boleh kurang dari hari ini</small>
                    </div>
                    <div class="sc-input-group">
                        <label>Jam Mulai</label>
                        <input type="time" name="start_time" id="startTime" required>
                        <small class="error-msg" id="startTimeError" style="color: #b91c1c; font-size: 10px; display: none; margin-top: 4px;">Jam mulai tidak valid</small>
                    </div>
                    <div class="sc-input-group">
                        <label>Jam Selesai</label>
                        <input type="time" name="end_time" id="endTime" required>
                        <small class="error-msg" id="endTimeError" style="color: #b91c1c; font-size: 10px; display: none; margin-top: 4px;">Jam selesai harus setelah jam mulai</small>
                    </div>
                </div>

                <button type="submit" class="sc-submit">
                    <i class="fa-solid fa-paper-plane"></i> Publikasikan Jadwal
                </button>
            </form>
        </div>
    </section>
    <?php endif; ?>

    
    <section class="sc-stats">
        <div class="sc-stat-card">
            <div class="sc-icon-box red"><i class="fa-regular fa-calendar-days"></i></div>
            <div class="sc-stat-info">
                <p>Total Jadwal</p>
                <strong><?php echo e(number_format($totalJadwalBulanIni ?? 0)); ?></strong>
            </div>
        </div>
        <div class="sc-stat-card">
            <div class="sc-icon-box blue"><i class="fa-regular fa-clock"></i></div>
            <div class="sc-stat-info">
                <p>Hari Ini</p>
                <strong><?php echo e(number_format($jadwalHariIni ?? 0)); ?></strong>
            </div>
        </div>
        <div class="sc-stat-card">
            <div class="sc-icon-box purple"><i class="fa-solid fa-check-double"></i></div>
            <div class="sc-stat-info">
                <p>Selesai</p>
                <strong><?php echo e(number_format($jadwalSelesaiTotal ?? 0)); ?></strong>
            </div>
        </div>
    </section>

    
    <section class="sc-table-panel">
        <div class="sc-table-wrap">
            <table class="sc-table">
                <thead>
                    <tr>
                        <th>Waktu & Tanggal</th>
                        <th>Program</th>
                        <th>Mata Pelajaran</th>
                        <th>Pengajar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $jadwal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $start = \Carbon\Carbon::parse($row->date . ' ' . $row->start_time);
                            $end = \Carbon\Carbon::parse($row->date . ' ' . $row->end_time);
                            $now = now();
                            $status = $now->between($start, $end) ? 'ongoing' : ($now->greaterThan($end) ? 'finished' : 'scheduled');
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo e($start->translatedFormat('d M Y')); ?></strong><br>
                                <small><?php echo e($start->format('H:i')); ?> - <?php echo e($end->format('H:i')); ?></small>
                            </td>
                            <td><?php echo e($row->class->program_name ?? '-'); ?></td
                            
                            <td><?php echo e($row->subject_name ?? $row->title); ?></td
                            <td><?php echo e($row->teacher->name ?? '-'); ?></td
                            <td><span class="sc-status-badge <?php echo e($status); ?>"><?php echo e(ucfirst($status)); ?></span></td
                            <td>
                                <form action="<?php echo e(route('admin.jadwal.destroy', $row->schedule_id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn-delete" onclick="return confirm('Hapus jadwal ini?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="text-center">Belum ada jadwal yang diatur.</td</td
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
    .sc-page { font-family: 'Inter', sans-serif; padding: 10px; }
    .sc-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; }
    .sc-title-wrapper { display: flex; align-items: center; gap: 15px; margin-bottom: 8px;}
    .sc-header h1 { font-size: 28px; font-weight: 800; color: #0f172a; margin: 0; }
    .sc-badge-live { background: #f1f5f9; padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 800; color: #64748b; display: flex; align-items: center; gap: 6px; border: 1px solid #e2e8f0; }
    .dot-pulse { width: 6px; height: 6px; background: #22c55e; border-radius: 50%; animation: pulse 1.5s infinite; }
    @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); } 70% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); } 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); } }
    .sc-header p {margin: 0; color: #64748b; font-size: 14px;}

    .sc-alert { display: flex; gap: 10px; align-items: center; padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 14px;}
    .sc-alert.success { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0;}

    .sc-top-grid { display: block; margin-bottom: 30px; }
    .sc-panel { background: #fff; border-radius: 22px; padding: 25px; border: 1px solid #f1f5f9; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }

    .sc-panel-heading {display: flex; gap: 15px; align-items: center; margin-bottom: 20px;}
    .sc-heading-icon { width: 45px; height: 45px; background: #fff1f2; color: #d90429; display: grid; place-items: center; border-radius: 12px; font-size: 20px;}
    .sc-panel-heading h2 { margin: 0; font-size: 18px; font-weight: 800;}

    .sc-input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .sc-input-row.three-col { grid-template-columns: 1fr 1fr 1fr; }
    .sc-input-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 15px; }
    .sc-input-group label { font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; }
    .sc-input-group input, .sc-input-group select { padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; font-weight: 600; outline: none; transition: all 0.3s; font-family: inherit;}
    .sc-input-group input:focus, .sc-input-group select:focus { border-color: #d90429; background: #fff;}
    .sc-input-readonly { background: #eff6ff !important; border-color: #bfdbfe !important; color: #1e40af; cursor: not-allowed; }

    .sc-submit { background: linear-gradient(135deg, #d90429 0%, #ef233c 100%); color: #fff; border: none; padding: 14px 24px; border-radius: 14px; font-weight: 800; cursor: pointer; margin-top: 10px; box-shadow: 0 10px 20px rgba(217, 4, 41, 0.2); transition: 0.3s; display: inline-flex; gap: 10px; align-items: center;}
    .sc-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(217, 4, 41, 0.3); }

    .sc-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
    .sc-stat-card { background: #fff; border-radius: 20px; padding: 20px; display: flex; align-items: center; gap: 15px; border: 1px solid #f1f5f9; }
    .sc-icon-box { width: 48px; height: 48px; border-radius: 14px; display: grid; place-items: center; font-size: 18px; }
    .sc-icon-box.red { background: #fff1f2; color: #d90429; }
    .sc-icon-box.blue { background: #eff6ff; color: #2563eb; }
    .sc-icon-box.purple { background: #faf5ff; color: #7c3aed; }
    .sc-stat-info p { margin: 0; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; }
    .sc-stat-info strong { font-size: 24px; font-weight: 900; color: #0f172a; display: block; }

    .sc-table-panel { background: #fff; border-radius: 22px; padding: 25px; border: 1px solid #f1f5f9; }
    .sc-table-wrap { overflow-x: auto;}
    .sc-table { width: 100%; border-collapse: collapse; min-width: 800px;}
    .sc-table th { text-align: left; padding: 15px; font-size: 11px; color: #94a3b8; text-transform: uppercase; border-bottom: 2px solid #f8fafc; }
    .sc-table td { padding: 15px; border-bottom: 1px solid #f8fafc; font-size: 13px; font-weight: 600; color: #334155;}
    .sc-table tbody tr:last-child td {border-bottom: none;}
    .sc-status-badge { padding: 4px 12px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
    .sc-status-badge.ongoing { background: #dcfce7; color: #15803d; }
    .sc-status-badge.scheduled { background: #e0f2fe; color: #0369a1; }
    .sc-status-badge.finished { background: #f1f5f9; color: #64748b; }
    .btn-delete { color: #d90429; border: none; background: #fff1f2; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: 0.3s; display: grid; place-items: center;}
    .btn-delete:hover { transform: scale(1.05); background: #fecdd3;}
    .text-center { text-align: center; }
    .error-msg { display: block; }

    @media (max-width: 768px) {
        .sc-input-row, .sc-input-row.three-col { grid-template-columns: 1fr; }
        .sc-stats { grid-template-columns: 1fr; }
        .sc-header { flex-direction: column; align-items: flex-start; gap: 15px;}
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

        // Elemen validasi waktu
        const dateInput = document.getElementById('scheduleDate');
        const startTimeInput = document.getElementById('startTime');
        const endTimeInput = document.getElementById('endTime');
        const dateError = document.getElementById('dateError');
        const startTimeError = document.getElementById('startTimeError');
        const endTimeError = document.getElementById('endTimeError');
        const form = document.getElementById('scheduleForm');

        // Set min date untuk input date
        const today = new Date().toISOString().split('T')[0];
        if (dateInput) {
            dateInput.setAttribute('min', today);
        }

        function validateDate() {
            const selectedDate = dateInput.value;
            if (!selectedDate) return true;

            const selected = new Date(selectedDate);
            const todayDate = new Date();
            todayDate.setHours(0, 0, 0, 0);

            if (selected < todayDate) {
                dateError.style.display = 'block';
                return false;
            } else {
                dateError.style.display = 'none';
                return true;
            }
        }

        function validateStartTime() {
            const selectedDate = dateInput.value;
            const startTime = startTimeInput.value;

            if (!selectedDate || !startTime) return true;

            const selectedDateTime = new Date(`${selectedDate}T${startTime}`);
            const now = new Date();

            const isToday = selectedDate === today;

            if (isToday && selectedDateTime < now) {
                startTimeError.textContent = 'Jam mulai tidak boleh kurang dari jam sekarang';
                startTimeError.style.display = 'block';
                return false;
            } else {
                startTimeError.style.display = 'none';
                return true;
            }
        }

        function validateEndTime() {
            const startTime = startTimeInput.value;
            const endTime = endTimeInput.value;

            if (!startTime || !endTime) return true;

            if (endTime <= startTime) {
                endTimeError.textContent = 'Jam selesai harus setelah jam mulai';
                endTimeError.style.display = 'block';
                return false;
            } else {
                endTimeError.style.display = 'none';
                return true;
            }
        }

        if (dateInput) {
            dateInput.addEventListener('change', function() {
                validateDate();
                if (dateInput.value === today) {
                    validateStartTime();
                } else {
                    startTimeError.style.display = 'none';
                }
            });
        }

        if (startTimeInput) {
            startTimeInput.addEventListener('change', validateStartTime);
        }

        if (endTimeInput) {
            endTimeInput.addEventListener('change', validateEndTime);
        }

        if (form) {
            form.addEventListener('submit', function(e) {
                const isDateValid = validateDate();
                const isStartTimeValid = validateStartTime();
                const isEndTimeValid = validateEndTime();

                if (!isDateValid || !isStartTimeValid || !isEndTimeValid) {
                    e.preventDefault();
                    let errorMsg = 'Jadwal tidak valid:\n';
                    if (!isDateValid) errorMsg += '- Tanggal tidak boleh kurang dari hari ini\n';
                    if (!isStartTimeValid) errorMsg += '- Jam mulai tidak valid (jika hari ini, tidak boleh kurang dari jam sekarang)\n';
                    if (!isEndTimeValid) errorMsg += '- Jam selesai harus setelah jam mulai\n';
                    alert(errorMsg);
                }
            });
        }

        if (classSelect) {
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
                                    subjectSelect.innerHTML += `<option value="${sub.subject_id}">${sub.name}</option>`;
                                });
                            } else {
                                subjectSelect.innerHTML = '<option value="">Belum ada mapel di Matrix</option>';
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

                const selectedText = subjectSelect.options[subjectSelect.selectedIndex].text;
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
                                teacherNameDisplay.value = 'Guru belum ditugaskan di Matrix';
                                teacherIdHidden.value = '';
                            }
                        })
                        .catch(() => {
                            teacherNameDisplay.value = 'Error mengambil data guru';
                        });
                }
            });
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/admin/jadwal/index.blade.php ENDPATH**/ ?>