<?php $__env->startSection('title', 'Dashboard Portal Pengajar'); ?>
<?php $__env->startSection('subtitle', 'Workspace pengajar Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $teacher = Auth::user();
    $jadwalUtama = $jadwalMendatang->first();
    $tutorUtama = $jadwalTutor->first();
?>

<div class="td-page">

    
    <section class="td-hero">
        <div class="td-hero-content">
            <div class="td-kicker">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span>Spekta Teacher Workspace</span>
            </div>

            <h1>Selamat Datang, <?php echo e($teacher->name); ?>!</h1>

            <p>
                Pantau agenda mengajar, materi, latihan, tryout, dan sesi dedicated tutor dalam satu dashboard pengajar.
            </p>

            <div class="td-hero-tags">
                <span>
                    <i class="fa-solid fa-calendar-day"></i>
                    <?php echo e(number_format($jadwalHariIni)); ?> Jadwal Hari Ini
                </span>

                <span>
                    <i class="fa-solid fa-headset"></i>
                    <?php echo e(number_format($tutorHariIni)); ?> Tutor Hari Ini
                </span>

                <span>
                    <i class="fa-solid fa-layer-group"></i>
                    <?php echo e(number_format($totalKelas)); ?> Kelas Diampu
                </span>
            </div>
        </div>

        <div class="td-today-panel">
            <div class="td-date-box">
                <span><?php echo e(now()->translatedFormat('l')); ?></span>
                <strong><?php echo e(now()->translatedFormat('d')); ?></strong>
                <small><?php echo e(now()->translatedFormat('F Y')); ?></small>
            </div>

            <div class="td-next-info">
                <span>Agenda Terdekat</span>

                <?php if($jadwalUtama): ?>
                    <strong><?php echo e($jadwalUtama->title); ?></strong>
                    <small>
                        <?php echo e(\Carbon\Carbon::parse($jadwalUtama->date)->translatedFormat('d M Y')); ?>,
                        <?php echo e(substr($jadwalUtama->start_time, 0, 5)); ?> WIB
                    </small>
                <?php elseif($tutorUtama): ?>
                    <strong>Dedicated Tutor</strong>
                    <small>
                        <?php echo e(\Carbon\Carbon::parse($tutorUtama->date)->translatedFormat('d M Y')); ?>,
                        <?php echo e(substr($tutorUtama->time, 0, 5)); ?> WIB
                    </small>
                <?php else: ?>
                    <strong>Tidak ada agenda</strong>
                    <small>Belum ada jadwal terdekat</small>
                <?php endif; ?>
            </div>
        </div>
    </section>

    
    <section class="td-stats">
        <div class="td-stat-card">
            <div class="td-stat-icon">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <p>Kelas Diampu</p>
            <h2><?php echo e(number_format($totalKelas)); ?></h2>
            <div class="td-stat-meta">
                <span class="info">Program</span>
                <small>aktif</small>
            </div>
        </div>

        <div class="td-stat-card">
            <div class="td-stat-icon">
                <i class="fa-solid fa-book-open-reader"></i>
            </div>
            <p>Mata Pelajaran</p>
            <h2><?php echo e(number_format($totalMapel)); ?></h2>
            <div class="td-stat-meta">
                <span class="success">Mapel</span>
                <small>diampu</small>
            </div>
        </div>

        <div class="td-stat-card">
            <div class="td-stat-icon">
                <i class="fa-solid fa-file-lines"></i>
            </div>
            <p>Materi Diupload</p>
            <h2><?php echo e(number_format($totalMateri)); ?></h2>
            <div class="td-stat-meta">
                <span class="info">Materi</span>
                <small>tersimpan</small>
            </div>
        </div>

        <div class="td-stat-card">
            <div class="td-stat-icon">
                <i class="fa-solid fa-clipboard-question"></i>
            </div>
            <p>Latihan Soal</p>
            <h2><?php echo e(number_format($totalLatihan)); ?></h2>
            <div class="td-stat-meta">
                <span class="warning">Latihan</span>
                <small>dibuat</small>
            </div>
        </div>

        <div class="td-stat-card">
            <div class="td-stat-icon">
                <i class="fa-solid fa-stopwatch"></i>
            </div>
            <p>Tryout Dibuat</p>
            <h2><?php echo e(number_format($totalTryout)); ?></h2>
            <div class="td-stat-meta">
                <span class="info">Tryout</span>
                <small>soal</small>
            </div>
        </div>
    </section>

    
    <section class="td-action-strip">
        <a href="<?php echo e(route('pengajar.materi.index')); ?>">
            <div><i class="fa-solid fa-upload"></i></div>
            <section>
                <strong>Upload Materi</strong>
                <span>Tambah materi pembelajaran</span>
            </section>
            <i class="fa-solid fa-chevron-right"></i>
        </a>

        <a href="<?php echo e(route('pengajar.tryout.index')); ?>">
            <div><i class="fa-solid fa-stopwatch"></i></div>
            <section>
                <strong>Buat Tryout</strong>
                <span>Kelola soal tryout</span>
            </section>
            <i class="fa-solid fa-chevron-right"></i>
        </a>

        <a href="<?php echo e(route('pengajar.latihan.index')); ?>">
            <div><i class="fa-solid fa-clipboard-question"></i></div>
            <section>
                <strong>Latihan Soal</strong>
                <span>Upload latihan siswa</span>
            </section>
            <i class="fa-solid fa-chevron-right"></i>
        </a>

        <a href="<?php echo e(route('pengajar.absensi.index')); ?>">
            <div><i class="fa-solid fa-user-check"></i></div>
            <section>
                <strong>Absensi</strong>
                <span>Isi dan lihat rekap</span>
            </section>
            <i class="fa-solid fa-chevron-right"></i>
        </a>
    </section>

    
    <section class="td-main-grid">

        
        <div class="td-left-column">

            
            <section class="td-panel">
                <div class="td-panel-heading">
                    <div>
                        <span>Teaching Schedule</span>
                        <h2>Jadwal Mengajar Terdekat</h2>
                        <p>Agenda kelas reguler yang sudah ditetapkan oleh admin.</p>
                    </div>

                    <a href="<?php echo e(route('pengajar.absensi.index')); ?>">Absensi</a>
                </div>

                <div class="td-schedule-list">
                    <?php $__empty_1 = true; $__currentLoopData = $jadwalMendatang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $isToday = \Carbon\Carbon::parse($item->date)->isToday();
                            $dateObj = \Carbon\Carbon::parse($item->date);
                        ?>

                        <article class="td-schedule-item <?php echo e($isToday ? 'today' : ''); ?>">
                            <div class="td-date-mini">
                                <strong><?php echo e($dateObj->format('d')); ?></strong>
                                <span><?php echo e($dateObj->translatedFormat('M')); ?></span>
                            </div>

                            <div class="td-schedule-info">
                                <h3><?php echo e($item->title); ?></h3>
                                <p><?php echo e($item->class->program_name ?? 'Program Kelas'); ?></p>
                                <div>
                                    <span>
                                        <i class="fa-regular fa-clock"></i>
                                        <?php echo e(substr($item->start_time, 0, 5)); ?> - <?php echo e(substr($item->end_time, 0, 5)); ?> WIB
                                    </span>

                                    <span>
                                        <i class="fa-regular fa-calendar"></i>
                                        <?php echo e($dateObj->translatedFormat('l')); ?>

                                    </span>
                                </div>
                            </div>

                            <div class="td-schedule-status">
                                <?php if($isToday): ?>
                                    <span class="live">Hari Ini</span>
                                <?php else: ?>
                                    <span>Terjadwal</span>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="td-empty">
                            <i class="fa-regular fa-calendar-xmark"></i>
                            <strong>Belum ada jadwal kelas.</strong>
                            <span>Jadwal mengajar akan muncul setelah admin membuat jadwal kelas.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            
            <section class="td-panel">
                <div class="td-panel-heading">
                    <div>
                        <span>My Teaching Subjects</span>
                        <h2>Kelas & Mata Pelajaran Saya</h2>
                        <p>Daftar program dan mapel yang menjadi tanggung jawab Anda.</p>
                    </div>
                </div>

                <div class="td-assignment-grid">
                    <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <article class="td-assignment-card">
                            <div class="td-assignment-icon">
                                <i class="fa-solid fa-book-open-reader"></i>
                            </div>

                            <div>
                                <h3><?php echo e($assign->subject_name); ?></h3>
                                <p><?php echo e($assign->classModel->program_name ?? 'Program Kelas'); ?></p>
                            </div>

                            <div class="td-assignment-actions">
                            <a href="<?php echo e(route('pengajar.materi.pilih', ['class_id' => $assign->class_id, 'subject_id' => $assign->subject_id])); ?>">
                                Materi
                            </a>

                            <a href="<?php echo e(route('pengajar.tryout.create', ['class_id' => $assign->class_id, 'subject_id' => $assign->subject_id])); ?>">
                                Tryout
                            </a>

                            <a href="<?php echo e(route('pengajar.latihan.pilih', ['class_id' => $assign->class_id, 'subject_id' => $assign->subject_id])); ?>">
                                Latihan
                            </a>
                        </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="td-empty">
                            <i class="fa-solid fa-book-open"></i>
                            <strong>Belum ada penugasan materi.</strong>
                            <span>Admin perlu menugaskan pengajar pada menu Penugasan Materi.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        
        <aside class="td-right-column">

            
            <section class="td-side-panel">
                <div class="td-side-heading">
                    <span>Private Session</span>
                    <h2>Dedicated Tutor</h2>
                </div>

                <div class="td-tutor-list">
                    <?php $__empty_1 = true; $__currentLoopData = $jadwalTutor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tutor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $studentName = $tutor->student->user->name ?? 'Siswa';
                            $initial = strtoupper(substr($studentName, 0, 1));
                        ?>

                        <article class="td-tutor-item">
                            <div class="td-tutor-avatar">
                                <?php echo e($initial); ?>

                            </div>

                            <div>
                                <h3><?php echo e($studentName); ?></h3>
                                <p><?php echo e($tutor->material->title ?? $tutor->material->material_name ?? 'Materi Privat'); ?></p>
                                <span>
                                    <?php echo e(\Carbon\Carbon::parse($tutor->date)->translatedFormat('d M Y')); ?>,
                                    <?php echo e(substr($tutor->time, 0, 5)); ?> WIB
                                </span>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="td-empty small">
                            <i class="fa-solid fa-headset"></i>
                            <strong>Belum ada tutor.</strong>
                            <span>Sesi tutor yang sudah dikonfirmasi akan muncul di sini.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            
            <section class="td-side-panel">
                <div class="td-side-heading">
                    <span>Latest Material</span>
                    <h2>Materi Terbaru</h2>
                </div>

                <div class="td-material-list">
                    <?php $__empty_1 = true; $__currentLoopData = $materiTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $materi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <article>
                            <div>
                                <i class="fa-solid fa-file-lines"></i>
                            </div>

                            <section>
                                <h3><?php echo e($materi->title ?? $materi->material_name ?? 'Materi'); ?></h3>
                                <p><?php echo e($materi->class->program_name ?? 'Program Kelas'); ?></p>
                                <span><?php echo e($materi->created_at ? $materi->created_at->diffForHumans() : '-'); ?></span>
                            </section>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="td-empty small">
                            <i class="fa-solid fa-file-circle-plus"></i>
                            <strong>Belum ada materi.</strong>
                            <span>Materi yang Anda upload akan tampil di sini.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            
            <section class="td-side-panel">
                <div class="td-side-heading">
                    <span>Recent Activity</span>
                    <h2>Aktivitas Terbaru</h2>
                </div>

                <div class="td-activity-list">
                    <?php $__empty_1 = true; $__currentLoopData = $aktivitasTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <article>
                            <div>
                                <i class="fa-solid <?php echo e($activity['icon']); ?>"></i>
                            </div>

                            <section>
                                <h3><?php echo e($activity['title']); ?></h3>
                                <p><?php echo e($activity['subtitle']); ?></p>
                                <span><?php echo e($activity['time'] ? $activity['time']->diffForHumans() : '-'); ?></span>
                            </section>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="td-empty small">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <strong>Belum ada aktivitas.</strong>
                            <span>Aktivitas pengajar akan muncul otomatis.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

        </aside>

    </section>

</div>

<style>
    .td-page {
        width: 100%;
    }

    .td-hero {
        position: relative;
        overflow: hidden;
        background: linear-gradient(120deg, #cf002b 0%, #85001d 48%, #182033 100%);
        color: #fff;
        border-radius: 24px;
        padding: 34px 36px;
        margin-bottom: 22px;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 270px;
        align-items: center;
        gap: 28px;
        box-shadow: 0 18px 38px rgba(134, 0, 24, .22);
    }

    .td-hero::before {
        content: "";
        position: absolute;
        width: 360px;
        height: 360px;
        right: -150px;
        top: -170px;
        border-radius: 999px;
        background: rgba(255,255,255,.10);
    }

    .td-hero::after {
        content: "";
        position: absolute;
        width: 230px;
        height: 230px;
        right: 90px;
        bottom: -150px;
        border-radius: 999px;
        background: rgba(255,255,255,.07);
    }

    .td-hero-content,
    .td-today-panel {
        position: relative;
        z-index: 2;
    }

    .td-kicker {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        min-height: 28px;
        padding: 0 11px;
        border-radius: 999px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.16);
        margin-bottom: 16px;
    }

    .td-kicker span {
        color: rgba(255,255,255,.88);
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .td-kicker i {
        font-size: 11px;
    }

    .td-hero h1 {
        margin: 0 0 10px;
        color: #fff;
        font-size: 34px;
        font-weight: 900;
        line-height: 1.08;
        letter-spacing: -0.045em;
    }

    .td-hero p {
        margin: 0;
        max-width: 770px;
        color: rgba(255,255,255,.88);
        font-size: 14px;
        font-weight: 600;
        line-height: 1.65;
    }

    .td-hero-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 22px;
    }

    .td-hero-tags span {
        min-height: 34px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0 13px;
        border-radius: 999px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.14);
        color: #fff;
        font-size: 11px;
        font-weight: 900;
    }

    .td-today-panel {
        justify-self: end;
        width: 255px;
        border-radius: 24px;
        padding: 20px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.18);
        backdrop-filter: blur(14px);
    }

    .td-date-box {
        background: #fff;
        color: #111827;
        border-radius: 20px;
        padding: 18px;
        text-align: center;
        margin-bottom: 14px;
        box-shadow: 0 12px 28px rgba(15,23,42,.16);
    }

    .td-date-box span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .1em;
        margin-bottom: 8px;
    }

    .td-date-box strong {
        display: block;
        font-size: 44px;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.06em;
    }

    .td-date-box small {
        display: block;
        margin-top: 7px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 800;
    }

    .td-next-info span {
        display: block;
        color: rgba(255,255,255,.7);
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .td-next-info strong {
        display: block;
        color: #fff;
        font-size: 13px;
        font-weight: 900;
        line-height: 1.4;
    }

    .td-next-info small {
        display: block;
        color: rgba(255,255,255,.78);
        font-size: 11px;
        font-weight: 700;
        margin-top: 5px;
    }

    .td-stats {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    .td-stat-card,
    .td-action-strip,
    .td-panel,
    .td-side-panel {
        background: #fff;
        border: 1px solid #edf0f4;
        box-shadow: 0 14px 35px rgba(15,23,42,.05);
    }

    .td-stat-card {
        border-radius: 20px;
        padding: 20px;
        min-height: 145px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .td-stat-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        background: #ffe8ee;
        color: #d90429;
        border-radius: 15px;
        margin-bottom: 14px;
    }

    .td-stat-card p {
        margin: 0 0 8px;
        color: #6b7280;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .td-stat-card h2 {
        margin: 0 0 13px;
        color: #0f172a;
        font-size: 31px;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .td-stat-meta {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .td-stat-meta span {
        height: 23px;
        display: inline-flex;
        align-items: center;
        border-radius: 8px;
        padding: 0 9px;
        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .td-stat-meta .success {
        background: #dcfce7;
        color: #16a34a;
    }

    .td-stat-meta .warning {
        background: #ffedd5;
        color: #ea580c;
    }

    .td-stat-meta .info {
        background: #dbeafe;
        color: #2563eb;
    }

    .td-stat-meta small {
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
    }

    .td-action-strip {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1px;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 22px;
    }

    .td-action-strip a {
        min-height: 92px;
        display: grid;
        grid-template-columns: 44px 1fr 14px;
        align-items: center;
        gap: 14px;
        padding: 18px;
        color: inherit;
        background: #fff;
        transition: .2s ease;
    }

    .td-action-strip a:hover {
        background: #fff7f9;
    }

    .td-action-strip a > div {
        width: 44px;
        height: 44px;
        display: grid;
        place-items: center;
        border-radius: 14px;
        background: #ffe8ee;
        color: #d90429;
    }

    .td-action-strip strong {
        display: block;
        color: #111827;
        font-size: 13px;
        font-weight: 900;
    }

    .td-action-strip span {
        display: block;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        margin-top: 3px;
    }

    .td-action-strip a > i {
        color: #64748b;
        font-size: 11px;
    }

    .td-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 365px;
        gap: 22px;
        align-items: start;
    }

    .td-left-column,
    .td-right-column {
        display: grid;
        gap: 22px;
    }

    .td-panel,
    .td-side-panel {
        border-radius: 22px;
        padding: 22px;
    }

    .td-panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 20px;
    }

    .td-panel-heading span,
    .td-side-heading span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .td-panel-heading h2,
    .td-side-heading h2 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .td-panel-heading p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.5;
    }

    .td-panel-heading a {
        height: 36px;
        display: inline-flex;
        align-items: center;
        padding: 0 13px;
        border-radius: 11px;
        background: #fff1f2;
        color: #d90429;
        font-size: 11px;
        font-weight: 900;
        white-space: nowrap;
    }

    .td-schedule-list {
        display: grid;
        gap: 14px;
    }

    .td-schedule-item {
        display: grid;
        grid-template-columns: 70px minmax(0, 1fr) 105px;
        gap: 16px;
        align-items: center;
        padding: 15px;
        border: 1px solid #edf0f4;
        border-radius: 18px;
        background: #fff;
        transition: .2s ease;
    }

    .td-schedule-item:hover {
        border-color: #fecdd3;
        box-shadow: 0 12px 28px rgba(15,23,42,.06);
        transform: translateY(-2px);
    }

    .td-schedule-item.today {
        background: linear-gradient(90deg, #fff7f9, #fff);
        border-color: #fecdd3;
    }

    .td-date-mini {
        width: 64px;
        height: 64px;
        border-radius: 17px;
        background: #111827;
        color: #fff;
        display: grid;
        place-items: center;
        align-content: center;
    }

    .td-date-mini strong {
        display: block;
        font-size: 23px;
        font-weight: 900;
        line-height: 1;
    }

    .td-date-mini span {
        display: block;
        margin-top: 4px;
        color: #fda4af;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .td-schedule-info h3 {
        margin: 0;
        color: #111827;
        font-size: 14px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .td-schedule-info p {
        margin: 4px 0 10px;
        color: #d90429;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .td-schedule-info div {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .td-schedule-info span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
    }

    .td-schedule-status {
        justify-self: end;
    }

    .td-schedule-status span {
        height: 28px;
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 0 10px;
        background: #dbeafe;
        color: #2563eb;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .td-schedule-status .live {
        background: #dcfce7;
        color: #16a34a;
    }

    .td-assignment-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .td-assignment-card {
        border: 1px solid #edf0f4;
        border-radius: 18px;
        padding: 17px;
        background: #fff;
        display: grid;
        grid-template-columns: 48px 1fr;
        gap: 13px;
        align-items: start;
    }

    .td-assignment-icon {
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        border-radius: 15px;
        background: #ffe8ee;
        color: #d90429;
    }

    .td-assignment-card h3 {
        margin: 0;
        color: #111827;
        font-size: 14px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .td-assignment-card p {
        margin: 4px 0 12px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.45;
    }

    .td-assignment-actions {
        grid-column: 1 / -1;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .td-assignment-actions a {
        height: 30px;
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 0 10px;
        background: #f8fafc;
        color: #374151;
        font-size: 10px;
        font-weight: 900;
    }

    .td-side-heading {
        margin-bottom: 17px;
    }

    .td-tutor-list,
    .td-material-list,
    .td-activity-list {
        display: grid;
        gap: 12px;
    }

    .td-tutor-item {
        display: grid;
        grid-template-columns: 44px 1fr;
        gap: 12px;
        padding: 13px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #edf0f4;
    }

    .td-tutor-avatar {
        width: 44px;
        height: 44px;
        display: grid;
        place-items: center;
        border-radius: 15px;
        background: #dbeafe;
        color: #2563eb;
        font-weight: 900;
    }

    .td-tutor-item h3,
    .td-material-list h3,
    .td-activity-list h3 {
        margin: 0;
        color: #111827;
        font-size: 12px;
        font-weight: 900;
    }

    .td-tutor-item p,
    .td-material-list p,
    .td-activity-list p {
        margin: 4px 0 4px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.4;
    }

    .td-tutor-item span,
    .td-material-list span,
    .td-activity-list span {
        color: #9ca3af;
        font-size: 10px;
        font-weight: 800;
    }

    .td-material-list article,
    .td-activity-list article {
        display: grid;
        grid-template-columns: 42px 1fr;
        gap: 12px;
        padding: 13px;
        border-radius: 16px;
        border: 1px solid #edf0f4;
        background: #fff;
    }

    .td-material-list article > div,
    .td-activity-list article > div {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 13px;
        background: #ffe8ee;
        color: #d90429;
    }

    .td-empty {
        padding: 34px;
        text-align: center;
        background: #f8fafc;
        border-radius: 18px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .td-empty.small {
        padding: 25px;
    }

    .td-empty i {
        width: 56px;
        height: 56px;
        margin: 0 auto 14px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #ffe8ee;
        color: #d90429;
        font-size: 22px;
    }

    .td-empty strong {
        display: block;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    .td-empty span {
        display: block;
        line-height: 1.55;
    }

    @media (max-width: 1500px) {
        .td-stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .td-main-grid {
            grid-template-columns: 1fr;
        }

        .td-right-column {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 1100px) {
        .td-hero {
            grid-template-columns: 1fr;
        }

        .td-today-panel {
            justify-self: start;
            width: 100%;
            max-width: 270px;
        }

        .td-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .td-action-strip {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .td-right-column {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .td-hero {
            padding: 28px;
        }

        .td-hero h1 {
            font-size: 27px;
        }

        .td-stats,
        .td-action-strip,
        .td-assignment-grid {
            grid-template-columns: 1fr;
        }

        .td-schedule-item {
            grid-template-columns: 1fr;
        }

        .td-schedule-status {
            justify-self: start;
        }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Windows\Documents\GitHub\PAAAAA2\BackEnd\resources\views/pengajar/dashboard.blade.php ENDPATH**/ ?>