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

    
    <section class="sc-stats">
        <!-- Total Jadwal -->
        <div class="sc-stat-card card-red">
            <div class="sc-icon-box red"><i class="fa-regular fa-calendar-days"></i></div>
            <div class="sc-stat-info">
                <p>Total Jadwal</p>
                <strong><?php echo e(number_format($totalJadwalBulanIni ?? 0)); ?></strong>
            </div>
        </div>
        <!-- Hari Ini -->
        <div class="sc-stat-card card-teal">
            <div class="sc-icon-box teal"><i class="fa-regular fa-clock"></i></div>
            <div class="sc-stat-info">
                <p>Hari Ini</p>
                <strong><?php echo e(number_format($jadwalHariIni ?? 0)); ?></strong>
            </div>
        </div>
        <!-- Selesai -->
        <div class="sc-stat-card card-gray">
            <div class="sc-icon-box gray"><i class="fa-solid fa-check-double"></i></div>
            <div class="sc-stat-info">
                <p>Selesai</p>
                <strong><?php echo e(number_format($jadwalSelesaiTotal ?? 0)); ?></strong>
            </div>
        </div>
    </section>

    
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
                        <small class="error-msg" id="dateError" style="color: #e53935; font-size: 10px; display: none; margin-top: 4px; font-weight: 700;">Tanggal tidak boleh kurang dari hari ini</small>
                    </div>
                    <div class="sc-input-group">
                        <label>Jam Mulai</label>
                        <input type="time" name="start_time" id="startTime" required>
                        <small class="error-msg" id="startTimeError" style="color: #e53935; font-size: 10px; display: none; margin-top: 4px; font-weight: 700;">Jam mulai tidak valid</small>
                    </div>
                    <div class="sc-input-group">
                        <label>Jam Selesai</label>
                        <input type="time" name="end_time" id="endTime" required>
                        <small class="error-msg" id="endTimeError" style="color: #e53935; font-size: 10px; display: none; margin-top: 4px; font-weight: 700;">Jam selesai harus setelah jam mulai</small>
                    </div>
                </div>

                <button type="submit" class="sc-submit">
                    <i class="fa-solid fa-paper-plane"></i> Publikasikan Jadwal
                </button>
            </form>
        </div>
    </section>
    <?php endif; ?>

    
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
                        <th class="text-center">Aksi</th>
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
                                <small style="color: #9e9e9e; font-weight: 600;"><?php echo e($start->format('H:i')); ?> - <?php echo e($end->format('H:i')); ?></small>
                            </td>
                            <td><?php echo e($row->class->program_name ?? '-'); ?></td>
                            <td><?php echo e($row->subject_name ?? $row->title); ?></td>
                            <td><?php echo e($row->teacher->name ?? '-'); ?></td>
                            <td><span class="sc-status-badge <?php echo e($status); ?>"><?php echo e(ucfirst($status)); ?></span></td>
                            <td>
                                <div class="sc-actions-wrap">
                                    <form action="<?php echo e(route('admin.jadwal.destroy', $row->schedule_id)); ?>" method="POST" style="display: inline-flex;">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn-delete" onclick="return confirm('Hapus jadwal ini?')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="sc-empty-state">
                                    <i class="fa-regular fa-calendar-times"></i>
                                    <span>Belum ada jadwal yang diatur.</span>
                                </div>
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

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── Header ── */
    .sc-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: flex-end; 
        margin-bottom: 24px; 
        border-bottom: 1px solid var(--border-soft); 
        padding-bottom: 20px; 
    }
    
    .sc-breadcrumb {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .sc-breadcrumb a { color: var(--spekta-teal); text-decoration: none; }
    .sc-breadcrumb i { font-size: 8px; color: var(--spekta-gray); }

    .sc-title-wrapper { display: flex; align-items: center; gap: 15px; margin-bottom: 8px;}
    .sc-header h1 { font-size: 24px; font-weight: 900; color: var(--text-main); margin: 0; letter-spacing: -0.02em; }
    
    .sc-badge-live { 
        background: var(--spekta-gray-light); 
        padding: 4px 12px; 
        border-radius: 20px; 
        font-size: 10px; 
        font-weight: 800; 
        color: var(--text-muted); 
        display: flex; 
        align-items: center; 
        gap: 6px; 
        border: 1px solid var(--border-soft); 
    }
    
    .dot-pulse { width: 6px; height: 6px; background: #22c55e; border-radius: 50%; animation: pulse 1.5s infinite; }
    @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); } 70% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); } 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); } }
    .sc-header p { margin: 0; color: var(--text-muted); font-size: 13px; font-weight: 600;}

    /* Alert */
    .sc-alert { display: flex; gap: 10px; align-items: center; padding: 12px 18px; border-radius: 12px; margin-bottom: 24px; font-weight: 800; font-size: 13px;}
    .sc-alert.success { background: #e6f7ed; color: #15803d; border: 1px solid #bbf7d0;}

    /* ── Stats Cards (Metrik yang Rapi di Atas) ── */
    .sc-stats { 
        display: grid; 
        grid-template-columns: repeat(3, 1fr); 
        gap: 16px; 
        margin-bottom: 24px; 
    }
    .sc-stat-card { 
        background: var(--spekta-white); 
        border-radius: 14px; 
        padding: 16px; 
        display: flex; 
        align-items: center; 
        gap: 14px; 
        border: 1px solid var(--border-soft); 
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: all 0.2s ease;
    }
    .sc-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.03);
    }
    .sc-stat-card.card-red:hover { border-color: var(--spekta-red); }
    .sc-stat-card.card-teal:hover { border-color: var(--spekta-teal); }
    .sc-stat-card.card-gray:hover { border-color: var(--spekta-gray); }

    .sc-icon-box { width: 42px; height: 42px; border-radius: 10px; display: grid; place-items: center; font-size: 16px; }
    .sc-icon-box.red { background: var(--spekta-red-light); color: var(--spekta-red); }
    .sc-icon-box.teal { background: var(--spekta-teal-light); color: var(--spekta-teal); }
    .sc-icon-box.gray { background: var(--spekta-gray-light); color: var(--text-muted); }
    .sc-stat-info p { margin: 0; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; }
    .sc-stat-info strong { font-size: 22px; font-weight: 900; color: var(--text-main); display: block; }

    /* Form Panel */
    .sc-top-grid { display: block; margin-bottom: 24px; }
    .sc-panel { background: var(--spekta-white); border-radius: 16px; padding: 20px; border: 1px solid var(--border-soft); box-shadow: 0 4px 15px rgba(0,0,0,0.01); }

    .sc-panel-heading { display: flex; gap: 12px; align-items: center; margin-bottom: 18px;}
    .sc-heading-icon { width: 38px; height: 38px; background: var(--spekta-red-light); color: var(--spekta-red); display: grid; place-items: center; border-radius: 10px; font-size: 16px;}
    .sc-panel-heading h2 { margin: 0; font-size: 15px; font-weight: 800;}

    .sc-input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
    .sc-input-row.three-col { grid-template-columns: 1fr 1fr 1fr; }
    .sc-input-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 15px; }
    .sc-input-group label { font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.02em; }
    .sc-input-group input, .sc-input-group select { padding: 11px; border-radius: 10px; border: 1px solid var(--border-soft); background: var(--spekta-gray-light); font-weight: 600; outline: none; transition: all 0.25s; font-family: inherit; font-size: 12px; }
    .sc-input-group input:focus, .sc-input-group select:focus { border-color: var(--spekta-teal); background: var(--spekta-white); box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12); }
    
    /* Perubahan Read-Only State yang Seimbang & Pasif */
    .sc-input-readonly { 
        background: #f9fafb !important; 
        border-color: var(--border-soft) !important; 
        color: var(--text-muted) !important; 
        cursor: not-allowed; 
        box-shadow: none !important;
    }

    .sc-submit { background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%); color: var(--spekta-white); border: none; padding: 12px 20px; border-radius: 12px; font-weight: 800; cursor: pointer; margin-top: 10px; box-shadow: 0 4px 15px rgba(229, 57, 53, 0.2); transition: 0.2s; display: inline-flex; gap: 8px; align-items: center; font-family: inherit; font-size: 13px;}
    .sc-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(229, 57, 53, 0.3); }

    /* Table Panel */
    .sc-table-panel { background: var(--spekta-white); border-radius: 16px; padding: 20px; border: 1px solid var(--border-soft); }
    .sc-table-wrap { overflow-x: auto;}
    .sc-table { width: 100%; border-collapse: collapse; min-width: 800px;}
    .sc-table th { text-align: left; padding: 12px 14px; font-size: 10px; color: var(--text-muted); text-transform: uppercase; border-bottom: 2px solid var(--spekta-gray-light); font-weight: 800; letter-spacing: 0.05em; }
    .sc-table td { padding: 14px; border-bottom: 1px solid var(--spekta-gray-light); font-size: 13px; font-weight: 600; color: var(--text-main); vertical-align: middle; }
    .sc-table tbody tr:last-child td { border-bottom: none; }
    .sc-table tbody tr:hover { background: #fafbfc; }
    
    /* Status Badge alignment */
    .sc-status-badge { 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        height: 22px; 
        padding: 0 10px; 
        border-radius: 6px; 
        font-size: 9px; 
        font-weight: 800; 
        text-transform: uppercase; 
        letter-spacing: 0.02em;
    }
    .sc-status-badge.ongoing { background: #e6f7ed; color: #15803d; }
    .sc-status-badge.scheduled { background: #e0f2fe; color: #0269a1; }
    .sc-status-badge.finished { background: var(--spekta-gray-light); color: var(--text-muted); }
    
    .sc-actions-wrap {
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .btn-delete { 
        color: var(--spekta-red); 
        border: none; 
        background: var(--spekta-red-light); 
        width: 30px; 
        height: 30px; 
        border-radius: 8px; 
        cursor: pointer; 
        font-size: 12px; 
        transition: 0.2s; 
        display: inline-flex; 
        align-items: center; 
        justify-content: center;
    }
    .btn-delete:hover { transform: scale(1.05); background: #fecaca; color: #991b1b; }
    
    .sc-empty-state {
        padding: 24px;
        text-align: center;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 700;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .sc-empty-state i {
        font-size: 20px;
        color: var(--spekta-gray);
    }

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
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\perkuliahan\PA 2 - code\PAAAAA2\BackEnd\resources\views/admin/jadwal/index.blade.php ENDPATH**/ ?>