@extends('layouts.spekta')

@section('title', 'Input Absensi')

@section('content')
<div class="abs-page">

    {{-- ── 1. HEADER MINIMALIS ── --}}
    <section class="abs-header">
        <div class="abs-header-left">
            <a href="{{ route('pengajar.absensi.weeks', [$class->class_id, $subject]) }}" class="abs-back-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <span class="abs-breadcrumb-capsule">Attendance Input</span>
            <h1>{{ $subject }}</h1>
            <p>{{ $class->program_name }} • Minggu ke-{{ $week }}</p>
        </div>

        <div class="abs-date-info">
            <span>Waktu Input</span>
            <strong>{{ date('d M Y') }}</strong>
        </div>
    </section>

    {{-- ── 2. INPUT PANEL (RADIO BUTTON DENGAN CAPSULE INTERAKTIF) ── --}}
    <form action="{{ route('pengajar.absensi.store') }}" method="POST" class="abs-input-panel">
        @csrf

        <input type="hidden" name="class_id" value="{{ $class->class_id }}">
        <input type="hidden" name="subject_name" value="{{ $subject }}">
        <input type="hidden" name="week" value="{{ $week }}">

        <div class="abs-input-head">
            <div>
                <h2>Daftar Kehadiran Siswa</h2>
                <p>Pilih status kehadiran untuk setiap siswa di bawah ini secara teliti.</p>
            </div>

            <!-- Legenda Penanda Status -->
            <div class="abs-legend">
                <span><i class="green"></i> Hadir</span>
                <span><i class="yellow"></i> Izin</span>
                <span><i class="red"></i> Alpa</span>
            </div>
        </div>

        @if($siswas->count() > 0)
            <div class="abs-student-list">
                @foreach($siswas as $index => $s)
                    <div class="abs-student-row">
                        <!-- Nomor Absen Rapi -->
                        <div class="abs-student-number">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </div>

                        <div class="abs-student-info">
                            <strong>{{ $s->user->name }}</strong>
                            <span>Siswa Aktif</span>
                        </div>

                        <!-- Grup Radio Kapsul Interaktif (Style Gen Z) -->
                        <div class="abs-radio-group">
                            <label>
                                <input type="radio"
                                        name="status[{{ $s->user->usersID }}]"
                                        value="h"
                                        {{ ($existingAttendance[$s->user->usersID] ?? null) === 'h' ? 'checked' : '' }}
                                        required>
                                <span class="hadir">Hadir</span>
                            </label>

                            <label>
                                <input type="radio"
                                        name="status[{{ $s->user->usersID }}]"
                                        value="i"
                                        {{ ($existingAttendance[$s->user->usersID] ?? null) === 'i' ? 'checked' : '' }}>
                                <span class="izin">Izin</span>
                            </label>

                            <label>
                                <input type="radio"
                                        name="status[{{ $s->user->usersID }}]"
                                        value="a"
                                        {{ ($existingAttendance[$s->user->usersID] ?? null) === 'a' ? 'checked' : '' }}>
                                <span class="alpa">Alpa</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Submit Action Bar -->
            <div class="abs-submit-bar">
                <div class="submit-info-text">
                    <strong>{{ $siswas->count() }}</strong>
                    <span>Siswa diproses untuk minggu ke-{{ $week }}</span>
                </div>

                <button type="submit">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Absensi
                </button>
            </div>
        @else
            <div class="abs-empty">
                <div class="abs-empty-icon"><i class="fa-solid fa-user-slash"></i></div>
                <strong>Belum ada siswa aktif di kelas ini.</strong>
                <span>Absensi dapat dilakukan setelah siswa aktif tersedia pada program kelas ini.</span>
            </div>
        @endif
    </form>

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

    .abs-page {
        width: 100%;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Header Minimalis */
    .abs-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }
    
    .abs-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid var(--border-soft);
        background: var(--spekta-white);
        color: var(--text-muted);
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 11px;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.2s;
        margin-bottom: 12px;
    }
    .abs-back-btn:hover {
        background: var(--spekta-gray-light);
        color: var(--text-main);
        border-color: var(--spekta-gray);
    }

    .abs-breadcrumb-capsule {
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
    .abs-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .abs-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }

    .abs-date-info {
        flex-shrink: 0;
        min-width: 160px;
        padding: 14px;
        border-radius: 12px;
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        text-align: right;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
    }
    .abs-date-info span { display: block; color: var(--text-muted); font-size: 10px; font-weight: 800; text-transform: uppercase; margin-bottom: 4px; }
    .abs-date-info strong { display: block; color: var(--text-main); font-size: 16px; font-weight: 800; }

    /* Input Panel */
    .abs-input-panel { background: var(--spekta-white); border: 1px solid var(--border-soft); border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); }
    .abs-input-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 18px; padding-bottom: 14px; border-bottom: 1px solid var(--spekta-gray-light); margin-bottom: 12px; }
    .abs-input-head h2 { margin: 0; color: var(--text-main); font-size: 15px; font-weight: 800; }
    .abs-input-head p { margin: 4px 0 0; color: var(--text-muted); font-size: 11px; font-weight: 600; }
    
    .abs-legend { display: flex; gap: 8px; flex-wrap: wrap; }
    .abs-legend span { display: inline-flex; align-items: center; gap: 6px; height: 26px; padding: 0 10px; border-radius: 6px; background: var(--spekta-gray-light); color: var(--text-muted); font-size: 9px; font-weight: 800; text-transform: uppercase; }
    .abs-legend i { width: 6px; height: 6px; border-radius: 999px; }
    .abs-legend .green { background: #16a34a; }
    .abs-legend .yellow { background: #f59e0b; }
    .abs-legend .red { background: #dc2626; }

    /* List Siswa */
    .abs-student-list { display: grid; }
    .abs-student-row { display: grid; grid-template-columns: 44px minmax(0, 1fr) auto; gap: 14px; align-items: center; padding: 14px 4px; border-bottom: 1px solid var(--spekta-gray-light); }
    .abs-student-row:last-child { border-bottom: none; }
    
    .abs-student-number { width: 34px; height: 34px; display: grid; place-items: center; border-radius: 8px; background: var(--spekta-gray-light); color: var(--text-muted); font-size: 11px; font-weight: 800; }
    .abs-student-info strong { display: block; color: var(--text-main); font-size: 13px; font-weight: 800; text-transform: uppercase; }
    .abs-student-info span { display: block; margin-top: 4px; color: var(--text-muted); font-size: 10px; font-weight: 700; }
    
    /* Kapsul Radio Button Interaktif */
    .abs-radio-group { display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end; }
    .abs-radio-group label { cursor: pointer; }
    .abs-radio-group input { display: none; }
    .abs-radio-group span { min-width: 64px; height: 30px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; border: 1px solid var(--border-soft); background: var(--spekta-white); color: var(--text-muted); font-size: 10px; font-weight: 800; text-transform: uppercase; transition: all 0.2s ease; }
    
    .abs-radio-group input:checked + .hadir { background: #e6f7ed; border-color: #16a34a; color: #15803d; box-shadow: 0 2px 6px rgba(22, 163, 74, 0.1); }
    .abs-radio-group input:checked + .izin { background: #fff7ed; border-color: #f59e0b; color: #c2410c; }
    .abs-radio-group input:checked + .alpa { background: #fee2e2; border-color: #dc2626; color: #b91c1c; }

    /* Submit Bar */
    .abs-submit-bar { margin-top: 20px; border-radius: 12px; background: var(--spekta-gray-light); border: 1px solid var(--border-soft); padding: 12px; display: flex; align-items: center; justify-content: space-between; gap: 18px; }
    .submit-info-text { display: flex; align-items: baseline; gap: 4px; }
    .abs-submit-bar strong { color: var(--text-main); font-size: 20px; font-weight: 900; line-height: 1; }
    .abs-submit-bar span { color: var(--text-muted); font-size: 11px; font-weight: 700; }
    
    .abs-submit-bar button { border: none; height: 38px; border-radius: 10px; background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%); color: var(--spekta-white); padding: 0 16px; display: inline-flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 800; text-transform: uppercase; cursor: pointer; font-family: inherit; box-shadow: 0 4px 10px rgba(229, 57, 53, 0.15); transition: all 0.2s; }
    .abs-submit-bar button:hover { transform: translateY(-1px); box-shadow: 0 6px 15px rgba(229, 57, 53, 0.25); }

    .abs-empty { padding: 40px; text-align: center; color: var(--text-muted); font-size: 11px; font-weight: 700; display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .abs-empty-icon { width: 48px; height: 48px; margin: 0 auto 8px; display: grid; place-items: center; border-radius: 50%; background: var(--spekta-gray-light); color: var(--spekta-gray); font-size: 18px; }
    .abs-empty strong { display: block; color: var(--text-main); font-size: 14px; font-weight: 800; margin-bottom: 4px; }

    @media (max-width: 850px) { 
        .abs-input-header, .abs-input-head, .abs-submit-bar { flex-direction: column; align-items: flex-start; gap: 14px; } 
        .abs-date-info { width: 100%; text-align: left; } 
        .abs-student-row { grid-template-columns: 34px 1fr; } 
        .abs-radio-group { grid-column: 1 / -1; justify-content: flex-start; } 
    }
</style>
@endsection