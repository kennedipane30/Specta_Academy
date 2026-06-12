<?php $__env->startSection('title', 'Dashboard Portal Pengajar'); ?>
<?php $__env->startSection('subtitle', 'Workspace pengajar Spekta Academy'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $teacher = Auth::user();
    $jadwalUtama = $jadwalMendatang->first();
?>

<div class="td-page">

    
    <div class="td-dashboard-grid">

        
        <div class="td-main-col">
            
            
            <header class="td-welcome-header">
                <span class="td-breadcrumb-capsule">Spekta Teacher Workspace</span>
                <h1>Selamat Datang, <span><?php echo e($teacher->name); ?>!</span> 👋</h1>
                <p>Pantau agenda mengajar, kelola materi, dan tinjau aktivitas kelas Anda secara terpusat.</p>
            </header>

            
            <section class="td-stats-grid">
                <!-- Card 1: Kelas & Mapel (Info Utama) -->
                <div class="td-stat-card card-gray">
                    <div class="td-stat-icon gray">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div class="td-stat-info">
                        <p>Kelas Diampu</p>
                        <h2><?php echo e(number_format($totalKelas)); ?> <span>Kelas</span></h2>
                    </div>
                </div>

                <!-- Card 2: Upload Materi (Clickable) -->
                <a href="<?php echo e(route('pengajar.materi.index')); ?>" class="td-stat-card card-teal clickable-card" title="Klik untuk kelola materi belajar">
                    <div class="td-stat-icon teal">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div class="td-stat-info">
                        <p>Materi Diupload</p>
                        <h2><?php echo e(number_format($totalMateri)); ?> <span>Berkas</span></h2>
                    </div>
                    <div class="card-hover-arrow"><i class="fa-solid fa-arrow-right"></i></div>
                </a>

                <!-- Card 3: Latihan Soal (Clickable) -->
                <a href="<?php echo e(route('pengajar.latihan.index')); ?>" class="td-stat-card card-orange clickable-card" title="Klik untuk upload tugas harian">
                    <div class="td-stat-icon orange">
                        <i class="fa-solid fa-clipboard-question"></i>
                    </div>
                    <div class="td-stat-info">
                        <p>Latihan Soal</p>
                        <h2><?php echo e(number_format($totalLatihan)); ?> <span>Tugas</span></h2>
                    </div>
                    <div class="card-hover-arrow"><i class="fa-solid fa-arrow-right"></i></div>
                </a>

                <!-- Card 4: Setor Soal TO (Clickable) -->
                <a href="<?php echo e(route('pengajar.tryout.index')); ?>" class="td-stat-card card-red clickable-card" title="Klik untuk setor draf soal TO">
                    <div class="td-stat-icon red">
                        <i class="fa-solid fa-stopwatch"></i>
                    </div>
                    <div class="td-stat-info">
                        <p>Tryout Dibuat</p>
                        <h2><?php echo e(number_format($totalTryout)); ?> <span>Draf</span></h2>
                    </div>
                    <div class="card-hover-arrow"><i class="fa-solid fa-arrow-right"></i></div>
                </a>
            </section>

            
            <div class="td-panel">
                <div class="td-panel-heading">
                    <div class="heading-text">
                        <span class="panel-kicker">Teaching Schedule</span>
                        <h2>Jadwal Kelas Reguler</h2>
                        <p>Agenda kelas reguler yang sudah ditetapkan oleh admin akademik.</p>
                    </div>
                    <a href="<?php echo e(route('pengajar.absensi.index')); ?>" class="btn-outline-primary">
                        Cek Absensi <i class="fa-solid fa-arrow-right-long" style="margin-left: 4px;"></i>
                    </a>
                </div>

                <div class="td-schedule-list">
                    <?php $__empty_1 = true; $__currentLoopData = $jadwalMendatang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $isToday = \Carbon\Carbon::parse($item->date)->isToday();
                            $dateObj = \Carbon\Carbon::parse($item->date);
                        ?>

                        <article class="td-schedule-item <?php echo e($isToday ? 'is-today' : ''); ?>">
                            <div class="td-date-badge">
                                <strong><?php echo e($dateObj->format('d')); ?></strong>
                                <span><?php echo e($dateObj->translatedFormat('M')); ?></span>
                            </div>

                            <div class="td-schedule-details">
                                <div class="schedule-head">
                                    <h3><?php echo e($item->title); ?></h3>
                                    <?php if($isToday): ?>
                                        <span class="badge-live"><span class="live-dot-pulse"></span> Hari Ini</span>
                                    <?php else: ?>
                                        <span class="badge-scheduled">Terjadwal</span>
                                    <?php endif; ?>
                                </div>
                                <p class="program-name"><?php echo e($item->class->program_name ?? 'Program Kelas'); ?></p>

                                <div class="schedule-meta">
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
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="td-empty-state">
                            <div class="empty-icon"><i class="fa-regular fa-calendar-xmark"></i></div>
                            <strong>Belum ada jadwal mengajar.</strong>
                            <p>Jadwal akan muncul setelah admin akademik mempublikasikan jadwal kelas.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="td-panel" style="margin-top: 24px;">
                <div class="td-panel-heading">
                    <div class="heading-text">
                        <span class="panel-kicker" style="color: var(--spekta-teal);">Private Session</span>
                        <h2>Jadwal Dedicated Tutor</h2>
                        <p>Sesi privat yang diajukan oleh siswa dan telah disetujui/dikonfirmasi oleh admin untuk Anda.</p>
                    </div>
                </div>

                <div class="td-tutor-grid">
                    <?php $__empty_1 = true; $__currentLoopData = $jadwalTutor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tutor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $studentName = $tutor->student->user->name ?? 'Nama Siswa';
                            $subjectName = $tutor->material->title ?? $tutor->material->material_name ?? 'Materi Privat Umum';
                            $dateObj = \Carbon\Carbon::parse($tutor->date);
                            $isTodayTutor = $dateObj->isToday();
                        ?>

                        <article class="td-tutor-card <?php echo e($isTodayTutor ? 'is-today' : ''); ?>">
                            <div class="tutor-header">
                                <div class="student-avatar">
                                    <?php echo e(strtoupper(substr($studentName, 0, 1))); ?>

                                </div>
                                <div class="student-info">
                                    <h3><?php echo e($studentName); ?></h3>
                                    <?php if($isTodayTutor): ?>
                                        <span class="badge-tutor today"><span class="live-dot-pulse"></span> Hari Ini</span>
                                    <?php else: ?>
                                        <span class="badge-tutor">Terkonfirmasi</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="tutor-body">
                                <p class="subject-title"><?php echo e($subjectName); ?></p>

                                <div class="tutor-meta-box">
                                    <div>
                                        <i class="fa-regular fa-calendar"></i>
                                        <span><?php echo e($dateObj->translatedFormat('l, d M Y')); ?></span>
                                    </div>
                                    <div>
                                        <i class="fa-regular fa-clock"></i>
                                        <span><?php echo e(substr($tutor->time, 0, 5)); ?> WIB</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="td-empty-state">
                            <div class="empty-icon"><i class="fa-solid fa-headset"></i></div>
                            <strong>Belum ada jadwal Dedicated Tutor.</strong>
                            <p>Jika ada permintaan tutor dari siswa yang disetujui admin untuk Anda, jadwalnya akan muncul di sini.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        
        <aside class="td-sidebar-col">
            
            <!-- Widget Kalender Hari Ini -->
            <div class="td-side-widget date-widget">
                <div class="td-date-box">
                    <span><?php echo e(now()->translatedFormat('l')); ?></span>
                    <strong><?php echo e(now()->translatedFormat('d')); ?></strong>
                    <small><?php echo e(now()->translatedFormat('F Y')); ?></small>
                </div>
            </div>

            <!-- Widget Agenda Terdekat -->
            <div class="td-side-widget agenda-widget">
                <div class="widget-header">
                    <span class="material-symbols-outlined">event_upcoming</span>
                    <h3>Agenda Terdekat</h3>
                </div>
                
                <div class="widget-body">
                    <?php if($jadwalUtama): ?>
                        <div class="agenda-item-active">
                            <span class="agenda-class-badge"><?php echo e($jadwalUtama->class->program_name ?? 'Program Kelas'); ?></span>
                            <strong><?php echo e($jadwalUtama->title); ?></strong>
                            <small><i class="fa-regular fa-clock"></i> <?php echo e(substr($jadwalUtama->start_time, 0, 5)); ?> WIB</small>
                        </div>
                    <?php else: ?>
                        <div class="agenda-empty">
                            <i class="fa-solid fa-ghost"></i>
                            <strong>Tidak ada agenda</strong>
                            <span>Belum ada jadwal kelas terdekat untuk Anda.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
        </aside>

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

    .td-page {
        width: 100%;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── DASHBOARD GRID ASIMETRIS ── */
    .td-dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 24px;
        align-items: start;
    }

    /* Kolom Kiri */
    .td-main-col {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .td-welcome-header {
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }
    .td-breadcrumb-capsule {
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
    .td-welcome-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .td-welcome-header h1 span { color: var(--spekta-red); }
    .td-welcome-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }

    /* ── INTERACTIVE CLICKABLE STATS ── */
    .td-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
    .td-stat-card {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        position: relative;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .td-stat-card.clickable-card {
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }
    .td-stat-card.clickable-card:hover {
        transform: translateY(-2.5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.04);
    }
    .td-stat-card.card-teal:hover { border-color: var(--spekta-teal); }
    .td-stat-card.card-orange:hover { border-color: #d97706; }
    .td-stat-card.card-red:hover { border-color: var(--spekta-red); }

    .td-stat-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .td-stat-icon.gray { background: var(--spekta-gray-light); color: var(--text-muted); }
    .td-stat-icon.teal { background: var(--spekta-teal-light); color: var(--spekta-teal); }
    .td-stat-icon.orange { background: rgba(217, 119, 6, 0.08); color: #d97706; }
    .td-stat-icon.red { background: var(--spekta-red-light); color: var(--spekta-red); }

    .td-stat-info p { margin: 0 0 4px; font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; }
    .td-stat-info h2 { margin: 0; font-size: 22px; font-weight: 800; color: var(--text-main); line-height: 1; display: flex; align-items: baseline; gap: 3px; }
    .td-stat-info h2 span { font-size: 11px; font-weight: 600; color: var(--text-muted); }

    .card-hover-arrow {
        position: absolute;
        right: 14px; top: 14px;
        font-size: 10px;
        color: var(--spekta-gray);
        opacity: 0;
        transform: translateX(-4px);
        transition: all 0.2s ease;
    }
    .td-stat-card.clickable-card:hover .card-hover-arrow {
        opacity: 1;
        transform: translateX(0);
    }

    /* ── PANELS (REGULAR SCHEDULE) ── */
    .td-panel {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        padding: 20px;
    }
    .td-panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--spekta-gray-light);
    }
    .panel-kicker { display: block; font-size: 10px; font-weight: 800; color: var(--spekta-red); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;}
    .heading-text h2 { margin: 0 0 4px; font-size: 15px; font-weight: 800; color: var(--text-main); }
    .heading-text p { margin: 0; font-size: 11px; color: var(--text-muted); font-weight: 600; }

    .btn-outline-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border: 1px solid var(--border-soft);
        background: var(--spekta-white);
        color: var(--text-muted);
        border-radius: 8px;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-outline-primary:hover { background: var(--spekta-gray-light); color: var(--text-main); border-color: var(--spekta-gray); }

    /* SCHEDULE LIST (REGULAR) */
    .td-schedule-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 14px;
    }
    .td-schedule-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px;
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        background: var(--spekta-white);
        transition: all 0.2s;
    }
    .td-schedule-item:hover { border-color: var(--spekta-gray); transform: translateY(-2px);}
    .td-schedule-item.is-today { border-color: rgba(229, 57, 53, 0.15); background: var(--spekta-red-light); }

    .td-date-badge {
        width: 50px;
        height: 50px;
        background: var(--spekta-gray-light);
        border: 1px solid var(--border-soft);
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        flex-shrink: 0;
    }
    .td-schedule-item.is-today .td-date-badge { background: #fee2e2; border-color: #fecaca; }
    .td-date-badge strong { font-size: 18px; font-weight: 800; line-height: 1; color: var(--text-main);}
    .td-schedule-item.is-today .td-date-badge strong { color: var(--spekta-red-dark); }
    .td-date-badge span { font-size: 9px; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-top: 1px;}
    .td-schedule-item.is-today .td-date-badge span { color: var(--spekta-red); }

    .td-schedule-details { flex-grow: 1; min-width: 0; }
    .schedule-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2px;}
    .schedule-head h3 { margin: 0; font-size: 13px; font-weight: 800; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .badge-live { 
        font-size: 9px; font-weight: 800; background: #e6f7ed; color: #15803d; padding: 1px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px; 
    }
    
    .live-dot-pulse {
        width: 5px; height: 5px;
        background: currentColor;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(21, 128, 61, 0.7);
        animation: pulseGreen 1.5s infinite;
    }
    @keyframes pulseGreen {
        0% { box-shadow: 0 0 0 0 rgba(21, 128, 61, 0.7); }
        70% { box-shadow: 0 0 0 6px rgba(21, 128, 61, 0); }
        100% { box-shadow: 0 0 0 0 rgba(21, 128, 61, 0); }
    }

    .badge-scheduled { font-size: 9px; font-weight: 600; background: var(--spekta-gray-light); color: var(--text-muted); padding: 1px 6px; border-radius: 4px; }

    .program-name { margin: 0 0 8px; font-size: 11px; color: var(--spekta-red-dark); font-weight: 700;}
    .schedule-meta { display: flex; gap: 10px; font-size: 11px; color: var(--text-muted); font-weight: 600; }
    .schedule-meta span { display: flex; align-items: center; gap: 4px; }

    /* DEDICATED TUTOR GRID */
    .td-tutor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 14px;
    }
    .td-tutor-card {
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        background: var(--spekta-white);
        padding: 16px;
        display: flex;
        flex-direction: column;
        transition: all 0.2s;
    }
    .td-tutor-card:hover { border-color: var(--spekta-gray); transform: translateY(-2px);}
    .td-tutor-card.is-today { border-color: #a7f3d0; background: #f0fdf4; }

    .tutor-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--spekta-gray-light);
    }
    .td-tutor-card.is-today .tutor-header { border-color: #d1fae5; }

    .student-avatar {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #dbeafe;
        color: #1d4ed8;
        display: grid;
        place-items: center;
        font-size: 14px;
        font-weight: 900;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .student-info h3 { margin: 0 0 2px; font-size: 13px; font-weight: 800; color: var(--text-main);}
    .badge-tutor { font-size: 9px; font-weight: 600; color: var(--text-muted); background: var(--spekta-gray-light); padding: 1px 6px; border-radius: 4px; }
    .badge-tutor.today { background: #10b981; color: #fff; font-weight: 800; display: inline-flex; align-items: center; gap: 4px; }

    .tutor-body .subject-title {
        margin: 0 0 10px;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-main);
    }
    .tutor-meta-box {
        display: flex;
        gap: 12px;
        background: var(--spekta-gray-light);
        padding: 10px;
        border-radius: 8px;
        border: 1px solid var(--border-soft);
    }
    .td-tutor-card.is-today .tutor-meta-box { background: var(--spekta-white); border-color: #a7f3d0; }
    .tutor-meta-box div { display: flex; align-items: center; gap: 4px; font-size: 11px; color: var(--text-muted); font-weight: 600;}
    .tutor-meta-box div i { color: var(--spekta-gray); }
    .td-tutor-card.is-today .tutor-meta-box div i { color: #059669; }

    /* ── KOLOM KANAN (SIDEBAR KALENDER & AGENDA BENTO) ── */
    .td-sidebar-col {
        display: flex;
        flex-direction: column;
        gap: 20px;
        position: sticky;
        top: 20px;
    }

    .td-side-widget {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
    }

    .td-date-box {
        text-align: center;
    }
    .td-date-box span { display: block; font-size: 11px; font-weight: 800; color: var(--spekta-red); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 4px; }
    .td-date-box strong { display: block; font-size: 38px; font-weight: 900; line-height: 1; color: var(--text-main); }
    .td-date-box small { display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); margin-top: 4px;}

    .td-side-widget .widget-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--spekta-gray-light);
    }
    .td-side-widget .widget-header h3 { font-size: 12px; font-weight: 800; color: var(--text-main); text-transform: uppercase; margin: 0; letter-spacing: 0.02em; }
    .td-side-widget .widget-header span { color: var(--spekta-red); font-size: 16px; }

    .agenda-item-active {
        display: flex;
        flex-direction: column;
        gap: 4px;
        background: var(--spekta-red-light);
        padding: 12px;
        border-radius: 10px;
        border-left: 4px solid var(--spekta-red);
    }
    .agenda-class-badge { font-size: 9px; font-weight: 800; color: var(--spekta-red-dark); text-transform: uppercase; }
    .agenda-item-active strong { font-size: 13px; font-weight: 800; color: var(--text-main); line-height: 1.4; }
    .agenda-item-active small { font-size: 11px; color: var(--text-muted); font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }

    .agenda-empty {
        text-align: center;
        padding: 20px 10px;
        color: var(--text-muted);
    }
    .agenda-empty i { font-size: 20px; color: var(--spekta-gray); margin-bottom: 6px; display: block; }
    .agenda-empty strong { display: block; font-size: 12px; color: var(--text-main); margin-bottom: 2px; }
    .agenda-empty span { font-size: 11px; font-weight: 600; line-height: 1.4; }

    /* Empty States */
    .td-empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 30px 16px;
        background: var(--spekta-gray-light);
        border-radius: 12px;
        border: 1px dashed var(--border-soft);
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 600;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }
    .empty-icon {
        width: 44px;
        height: 44px;
        background: var(--spekta-white);
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 18px;
        color: var(--spekta-gray);
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .td-empty-state strong { display: block; font-size: 13px; color: var(--text-main); margin-bottom: 2px; font-weight: 800; }
    .td-empty-state p { margin: 0; line-height: 1.4; }

    /* RESPONSIVE LAYOUT */
    @media (max-width: 1100px) {
        .td-dashboard-grid { grid-template-columns: 1fr; }
        .td-sidebar-col { position: static; }
        .td-stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
        .td-stats-grid { grid-template-columns: 1fr; }
        .td-schedule-list, .td-tutor-grid { grid-template-columns: 1fr; }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.spekta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\perkuliahan\PA 2 - code\PAAAAA2\BackEnd\resources\views/pengajar/dashboard.blade.php ENDPATH**/ ?>