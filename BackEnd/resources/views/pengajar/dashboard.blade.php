@extends('layouts.spekta')

@section('title', 'Dashboard Portal Pengajar')
@section('subtitle', 'Workspace pengajar Spekta Academy')

@section('content')
@php
    $teacher = Auth::user();
    $jadwalUtama = $jadwalMendatang->first();
@endphp

<div class="td-page">

    {{-- HERO SECTION --}}
    <section class="td-hero">
        <div class="td-hero-content">
            <div class="td-kicker">
                <i class="fa-solid fa-chalkboard-user"></i>
                <span>Spekta Teacher Workspace</span>
            </div>

            <h1>Selamat Datang, {{ $teacher->name }}!</h1>

            <p>
                Pantau agenda mengajar, kelola materi, dan pantau aktivitas kelas Anda dalam satu dashboard terpusat.
            </p>

            <div class="td-hero-tags">
                <span class="tag-today">
                    <i class="fa-solid fa-calendar-day"></i>
                    {{ number_format($jadwalHariIni) }} Jadwal Hari Ini
                </span>

                <span class="tag-class">
                    <i class="fa-solid fa-layer-group"></i>
                    {{ number_format($totalKelas) }} Kelas Diampu
                </span>
            </div>
        </div>

        <div class="td-today-panel">
            <div class="td-date-box">
                <span>{{ now()->translatedFormat('l') }}</span>
                <strong>{{ now()->translatedFormat('d') }}</strong>
                <small>{{ now()->translatedFormat('F Y') }}</small>
            </div>

            <div class="td-next-info">
                <span>Agenda Terdekat</span>

                @if($jadwalUtama)
                    <strong>{{ $jadwalUtama->title }}</strong>
                    <small>
                        <i class="fa-regular fa-clock"></i>
                        {{ substr($jadwalUtama->start_time, 0, 5) }} WIB
                    </small>
                @else
                    <strong>Tidak ada agenda</strong>
                    <small>Belum ada jadwal kelas terdekat</small>
                @endif
            </div>
        </div>
    </section>

    {{-- STATS SECTION --}}
    <section class="td-stats">
        <div class="td-stat-card">
            <div class="td-stat-icon icon-class">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div class="td-stat-info">
                <p>Kelas Diampu</p>
                <h2>{{ number_format($totalKelas) }}</h2>
            </div>
        </div>

        <div class="td-stat-card">
            <div class="td-stat-icon icon-subject">
                <i class="fa-solid fa-book-open-reader"></i>
            </div>
            <div class="td-stat-info">
                <p>Mata Pelajaran</p>
                <h2>{{ number_format($totalMapel) }}</h2>
            </div>
        </div>

        <div class="td-stat-card">
            <div class="td-stat-icon icon-material">
                <i class="fa-solid fa-file-lines"></i>
            </div>
            <div class="td-stat-info">
                <p>Materi Diupload</p>
                <h2>{{ number_format($totalMateri) }}</h2>
            </div>
        </div>

        <div class="td-stat-card">
            <div class="td-stat-icon icon-exercise">
                <i class="fa-solid fa-clipboard-question"></i>
            </div>
            <div class="td-stat-info">
                <p>Latihan Soal</p>
                <h2>{{ number_format($totalLatihan) }}</h2>
            </div>
        </div>

        <div class="td-stat-card">
            <div class="td-stat-icon icon-tryout">
                <i class="fa-solid fa-stopwatch"></i>
            </div>
            <div class="td-stat-info">
                <p>Tryout Dibuat</p>
                <h2>{{ number_format($totalTryout) }}</h2>
            </div>
        </div>
    </section>

    {{-- QUICK ACTIONS --}}
    <section class="td-action-strip">
        <a href="{{ route('pengajar.materi.index') }}" class="action-card">
            <div class="action-icon"><i class="fa-solid fa-upload"></i></div>
            <div class="action-text">
                <strong>Upload Materi</strong>
                <span>Tambah materi pembelajaran</span>
            </div>
            <i class="fa-solid fa-chevron-right action-arrow"></i>
        </a>

        <a href="{{ route('pengajar.tryout.index') }}" class="action-card">
            <div class="action-icon"><i class="fa-solid fa-stopwatch"></i></div>
            <div class="action-text">
                <strong>Buat Tryout</strong>
                <span>Kelola soal evaluasi</span>
            </div>
            <i class="fa-solid fa-chevron-right action-arrow"></i>
        </a>

        <a href="{{ route('pengajar.latihan.index') }}" class="action-card">
            <div class="action-icon"><i class="fa-solid fa-clipboard-question"></i></div>
            <div class="action-text">
                <strong>Latihan Soal</strong>
                <span>Upload tugas harian</span>
            </div>
            <i class="fa-solid fa-chevron-right action-arrow"></i>
        </a>

        <a href="{{ route('pengajar.absensi.index') }}" class="action-card">
            <div class="action-icon"><i class="fa-solid fa-user-check"></i></div>
            <div class="action-text">
                <strong>Absensi Kelas</strong>
                <span>Isi & lihat kehadiran</span>
            </div>
            <i class="fa-solid fa-chevron-right action-arrow"></i>
        </a>
    </section>

    {{-- MAIN CONTENT (FULL WIDTH) --}}
    <section class="td-main-content">

        {{-- REGULAR SCHEDULE PANEL --}}
        <div class="td-panel">
            <div class="td-panel-heading">
                <div class="heading-text">
                    <span class="panel-kicker">Teaching Schedule</span>
                    <h2>Jadwal Kelas Reguler</h2>
                    <p>Agenda kelas reguler yang sudah ditetapkan oleh admin akademik.</p>
                </div>
                <a href="{{ route('pengajar.absensi.index') }}" class="btn-outline-primary">
                    Cek Absensi
                </a>
            </div>

            <div class="td-schedule-list">
                @forelse($jadwalMendatang as $item)
                    @php
                        $isToday = \Carbon\Carbon::parse($item->date)->isToday();
                        $dateObj = \Carbon\Carbon::parse($item->date);
                    @endphp

                    <article class="td-schedule-item {{ $isToday ? 'is-today' : '' }}">
                        <div class="td-date-badge">
                            <strong>{{ $dateObj->format('d') }}</strong>
                            <span>{{ $dateObj->translatedFormat('M') }}</span>
                        </div>

                        <div class="td-schedule-details">
                            <div class="schedule-head">
                                <h3>{{ $item->title }}</h3>
                                @if($isToday)
                                    <span class="badge-live">Hari Ini</span>
                                @else
                                    <span class="badge-scheduled">Terjadwal</span>
                                @endif
                            </div>
                            <p class="program-name">{{ $item->class->program_name ?? 'Program Kelas' }}</p>

                            <div class="schedule-meta">
                                <span>
                                    <i class="fa-regular fa-clock"></i>
                                    {{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }} WIB
                                </span>
                                <span>
                                    <i class="fa-regular fa-calendar"></i>
                                    {{ $dateObj->translatedFormat('l') }}
                                </span>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="td-empty-state">
                        <div class="empty-icon"><i class="fa-regular fa-calendar-xmark"></i></div>
                        <strong>Belum ada jadwal mengajar.</strong>
                        <p>Jadwal akan muncul setelah admin akademik mempublikasikan jadwal kelas.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- DEDICATED TUTOR PANEL (MENGGANTIKAN PANEL SEBELUMNYA) --}}
        <div class="td-panel">
            <div class="td-panel-heading">
                <div class="heading-text">
                    <span class="panel-kicker" style="color: #059669;">Private Session</span>
                    <h2>Jadwal Dedicated Tutor</h2>
                    <p>Sesi privat yang diajukan oleh siswa dan telah disetujui/dikonfirmasi oleh admin untuk Anda.</p>
                </div>
            </div>

            <div class="td-tutor-grid">
                @forelse($jadwalTutor as $tutor)
                    @php
                        $studentName = $tutor->student->user->name ?? 'Nama Siswa';
                        $subjectName = $tutor->material->title ?? $tutor->material->material_name ?? 'Materi Privat Umum';
                        $dateObj = \Carbon\Carbon::parse($tutor->date);
                        $isTodayTutor = $dateObj->isToday();
                    @endphp

                    <article class="td-tutor-card {{ $isTodayTutor ? 'is-today' : '' }}">
                        <div class="tutor-header">
                            <div class="student-avatar">
                                {{ strtoupper(substr($studentName, 0, 1)) }}
                            </div>
                            <div class="student-info">
                                <h3>{{ $studentName }}</h3>
                                @if($isTodayTutor)
                                    <span class="badge-tutor today">Hari Ini</span>
                                @else
                                    <span class="badge-tutor">Terkonfirmasi</span>
                                @endif
                            </div>
                        </div>

                        <div class="tutor-body">
                            <p class="subject-title">{{ $subjectName }}</p>

                            <div class="tutor-meta-box">
                                <div>
                                    <i class="fa-regular fa-calendar"></i>
                                    <span>{{ $dateObj->translatedFormat('l, d M Y') }}</span>
                                </div>
                                <div>
                                    <i class="fa-regular fa-clock"></i>
                                    <span>{{ substr($tutor->time, 0, 5) }} WIB</span>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="td-empty-state">
                        <div class="empty-icon"><i class="fa-solid fa-headset"></i></div>
                        <strong>Belum ada jadwal Dedicated Tutor.</strong>
                        <p>Jika ada permintaan tutor dari siswa yang disetujui admin untuk Anda, jadwalnya akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </section>

</div>

<style>
    /* BASE SETUP */
    .td-page {
        width: 100%;
        font-family: 'Inter', system-ui, sans-serif;
        color: #1e293b;
    }

    /* HERO SECTION */
    .td-hero {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
        color: #fff;
        border-radius: 24px;
        padding: 40px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 30px;
        box-shadow: 0 10px 25px -5px rgba(185, 28, 28, 0.3);
    }
    .td-hero::before {
        content: "";
        position: absolute;
        width: 300px;
        height: 300px;
        right: -100px;
        top: -100px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
    }

    .td-hero-content {
        position: relative;
        z-index: 2;
        max-width: 600px;
    }
    .td-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 20px;
        background: rgba(255,255,255,0.15);
        margin-bottom: 16px;
    }
    .td-kicker span {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .td-hero h1 {
        margin: 0 0 12px;
        font-size: 32px;
        font-weight: 800;
        letter-spacing: -0.02em;
    }
    .td-hero p {
        margin: 0 0 24px;
        font-size: 14px;
        line-height: 1.6;
        opacity: 0.9;
    }

    .td-hero-tags {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .td-hero-tags span {
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .tag-today { background: rgba(245, 158, 11, 0.2); color: #fcd34d; }
    .tag-class { background: rgba(255, 255, 255, 0.15); color: #fff; }

    /* TODAY PANEL IN HERO */
    .td-today-panel {
        position: relative;
        z-index: 2;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        padding: 24px;
        border-radius: 20px;
        min-width: 250px;
    }
    .td-date-box {
        background: #fff;
        color: #0f172a;
        padding: 20px;
        border-radius: 16px;
        text-align: center;
        margin-bottom: 16px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .td-date-box span { display: block; font-size: 11px; font-weight: 800; color: #dc2626; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 4px; }
    .td-date-box strong { display: block; font-size: 42px; font-weight: 900; line-height: 1;}
    .td-date-box small { display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-top: 4px;}

    .td-next-info span { display: block; font-size: 10px; color: rgba(255,255,255,0.7); text-transform: uppercase; font-weight: 700; margin-bottom: 4px; }
    .td-next-info strong { display: block; font-size: 14px; font-weight: 700; line-height: 1.4; margin-bottom: 2px;}
    .td-next-info small { display: flex; align-items: center; gap: 4px; font-size: 12px; color: rgba(255,255,255,0.9); }

    /* STATS STRIP */
    .td-stats {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    .td-stat-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        transition: transform 0.2s;
    }
    .td-stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }

    .td-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .icon-class { background: #eff6ff; color: #2563eb; }
    .icon-subject { background: #fef2f2; color: #dc2626; }
    .icon-material { background: #f0fdf4; color: #16a34a; }
    .icon-exercise { background: #fff7ed; color: #ea580c; }
    .icon-tryout { background: #faf5ff; color: #9333ea; }

    .td-stat-info p { margin: 0 0 4px; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; }
    .td-stat-info h2 { margin: 0; font-size: 24px; font-weight: 800; color: #0f172a; line-height: 1; }

    /* QUICK ACTIONS */
    .td-action-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    .action-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        color: inherit;
        text-decoration: none;
        transition: all 0.2s;
    }
    .action-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        background: #f8fafc;
    }
    .action-icon {
        width: 44px;
        height: 44px;
        background: #fef2f2;
        color: #b91c1c;
        border-radius: 12px;
        display: grid;
        place-items: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .action-text { flex-grow: 1; }
    .action-text strong { display: block; font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 2px;}
    .action-text span { display: block; font-size: 12px; color: #64748b; }
    .action-arrow { color: #cbd5e1; font-size: 12px; transition: transform 0.2s;}
    .action-card:hover .action-arrow { transform: translateX(4px); color: #b91c1c;}

    /* MAIN PANELS */
    .td-main-content {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .td-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .td-panel-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .panel-kicker { display: block; font-size: 11px; font-weight: 700; color: #b91c1c; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;}
    .heading-text h2 { margin: 0 0 6px; font-size: 18px; font-weight: 800; color: #0f172a; }
    .heading-text p { margin: 0; font-size: 13px; color: #64748b; }

    .btn-outline-primary {
        padding: 8px 16px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #334155;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-outline-primary:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }

    /* SCHEDULE LIST (REGULAR) */
    .td-schedule-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
    }
    .td-schedule-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #fff;
        transition: all 0.2s;
    }
    .td-schedule-item:hover { border-color: #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transform: translateY(-2px);}
    .td-schedule-item.is-today { border-color: #fecaca; background: #fffcfc; }

    .td-date-badge {
        width: 60px;
        height: 60px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        flex-shrink: 0;
    }
    .td-schedule-item.is-today .td-date-badge { background: #fee2e2; border-color: #fecaca; color: #dc2626;}
    .td-date-badge strong { font-size: 20px; font-weight: 800; line-height: 1; color: #0f172a;}
    .td-schedule-item.is-today .td-date-badge strong { color: #b91c1c; }
    .td-date-badge span { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-top: 2px;}
    .td-schedule-item.is-today .td-date-badge span { color: #dc2626; }

    .td-schedule-details { flex-grow: 1; }
    .schedule-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;}
    .schedule-head h3 { margin: 0; font-size: 15px; font-weight: 700; color: #0f172a; }
    .badge-live { font-size: 10px; font-weight: 700; background: #dcfce7; color: #059669; padding: 2px 8px; border-radius: 6px; }
    .badge-scheduled { font-size: 10px; font-weight: 600; background: #f1f5f9; color: #64748b; padding: 2px 8px; border-radius: 6px; }

    .program-name { margin: 0 0 10px; font-size: 12px; color: #b91c1c; font-weight: 600;}
    .schedule-meta { display: flex; gap: 12px; font-size: 12px; color: #64748b; }
    .schedule-meta span { display: flex; align-items: center; gap: 4px; }

    /* DEDICATED TUTOR GRID */
    .td-tutor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 16px;
    }
    .td-tutor-card {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #fff;
        padding: 20px;
        display: flex;
        flex-direction: column;
        transition: all 0.2s;
    }
    .td-tutor-card:hover { border-color: #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); transform: translateY(-2px);}
    .td-tutor-card.is-today { border-color: #a7f3d0; background: #f0fdf4; }

    .tutor-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .td-tutor-card.is-today .tutor-header { border-color: #d1fae5; }

    .student-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #dbeafe;
        color: #1d4ed8;
        display: grid;
        place-items: center;
        font-size: 16px;
        font-weight: 800;
    }
    .student-info h3 { margin: 0 0 4px; font-size: 15px; font-weight: 700; color: #0f172a;}
    .badge-tutor { font-size: 10px; font-weight: 600; color: #64748b; background: #f1f5f9; padding: 2px 8px; border-radius: 6px; }
    .badge-tutor.today { background: #10b981; color: #fff; font-weight: 700;}

    .tutor-body .subject-title {
        margin: 0 0 12px;
        font-size: 14px;
        font-weight: 600;
        color: #334155;
    }
    .tutor-meta-box {
        display: flex;
        gap: 16px;
        background: #f8fafc;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    .td-tutor-card.is-today .tutor-meta-box { background: #fff; border-color: #a7f3d0; }
    .tutor-meta-box div { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #475569; font-weight: 500;}
    .tutor-meta-box div i { color: #94a3b8; }
    .td-tutor-card.is-today .tutor-meta-box div i { color: #059669; }

    /* EMPTY STATES */
    .td-empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px 20px;
        background: #f8fafc;
        border-radius: 16px;
        border: 1px dashed #cbd5e1;
    }
    .empty-icon {
        width: 56px;
        height: 56px;
        background: #fff;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 24px;
        color: #94a3b8;
        margin: 0 auto 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .td-empty-state strong { display: block; font-size: 15px; color: #1e293b; margin-bottom: 6px; }
    .td-empty-state p { margin: 0; font-size: 13px; color: #64748b; }

    /* RESPONSIVE */
    @media (max-width: 1200px) {
        .td-stats { grid-template-columns: repeat(3, 1fr); }
        .td-action-strip { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 900px) {
        .td-hero { flex-direction: column; align-items: flex-start; }
        .td-today-panel { width: 100%; max-width: 100%; }
        .td-stats { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
        .td-stats { grid-template-columns: 1fr; }
        .td-action-strip { grid-template-columns: 1fr; }
        .td-schedule-list, .td-tutor-grid { grid-template-columns: 1fr; }
        .td-panel-heading { flex-direction: column; gap: 16px; align-items: flex-start; }
    }
</style>
@endsection
