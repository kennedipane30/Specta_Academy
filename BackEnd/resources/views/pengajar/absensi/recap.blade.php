@extends('layouts.spekta')

@section('title', 'Rekap Absensi')

@section('content')
@php
    $totalData = $data->count();
    $hadir = $data->where('status', 'h')->count();
    $izin = $data->where('status', 'i')->count();
    $alpa = $data->where('status', 'a')->count();
@endphp

<div class="abs-page">

    {{-- ── 1. HEADER MINIMALIS DENGAN KARTU RINGKASAN MINI ── --}}
    <section class="abs-header">
        <div class="abs-header-left">
            <a href="{{ route('pengajar.absensi.weeks', [$class->class_id, $subject]) }}" class="abs-back-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <span class="abs-breadcrumb-capsule">Attendance Recap</span>
            <h1>Rekap Absensi</h1>
            <p>{{ $class->program_name }} • {{ $subject }} • Minggu {{ $week }}</p>
        </div>

        <!-- Ringkasan Kehadiran Mini yang Rapi -->
        <div class="abs-recap-summary">
            <div class="summary-box green-box">
                <strong>{{ $hadir }}</strong>
                <span>Hadir</span>
            </div>

            <div class="summary-box yellow-box">
                <strong>{{ $izin }}</strong>
                <span>Izin</span>
            </div>

            <div class="summary-box red-box">
                <strong>{{ $alpa }}</strong>
                <span>Alpa</span>
            </div>
        </div>
    </section>

    {{-- ── 2. DAFTAR KEHADIRAN SISWA (TABLE PANEL) ── --}}
    <section class="abs-panel">
        <div class="abs-panel-head">
            <div>
                <h2>Daftar Kehadiran Siswa</h2>
                <p>Total data absensi terverifikasi: {{ $totalData }} siswa.</p>
            </div>
        </div>

        <div class="abs-table-wrap">
            <table class="abs-table">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Status Kehadiran</th>
                        <th>Tanggal Input</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($data as $row)
                        <tr>
                            {{-- Profil Siswa --}}
                            <td>
                                <div class="abs-student-cell">
                                    <div class="abs-avatar">
                                        {{ strtoupper(substr($row->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="abs-student-name">
                                        <strong>{{ $row->user->name ?? 'N/A' }}</strong>
                                        <span>Siswa Aktif</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Badge Status --}}
                            <td>
                                @if($row->status == 'h')
                                    <span class="abs-badge hadir">Hadir</span>
                                @elseif($row->status == 'i')
                                    <span class="abs-badge izin">Izin</span>
                                @else
                                    <span class="abs-badge alpa">Alpa</span>
                                @endif
                            </td>

                            {{-- Tanggal Selesai --}}
                            <td>
                                <span class="abs-date-text">
                                    <i class="fa-regular fa-clock"></i> {{ $row->date ? date('d M Y', strtotime($row->date)) : '-' }} WIB
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <div class="abs-empty">
                                    <div class="abs-empty-icon"><i class="fa-solid fa-folder-open"></i></div>
                                    <strong>Data absensi tidak ditemukan.</strong>
                                    <span>Belum ada data absensi yang masuk untuk minggu ini.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
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

    /* Summary Mini Boxes */
    .abs-recap-summary { display: flex; gap: 8px; flex-shrink: 0; }
    .summary-box { min-width: 74px; padding: 10px; border-radius: 10px; border: 1px solid var(--border-soft); text-align: center; background: var(--spekta-white); }
    .summary-box strong { display: block; font-size: 18px; font-weight: 900; line-height: 1; }
    .summary-box span { display: block; font-size: 8px; font-weight: 800; text-transform: uppercase; margin-top: 4px; color: var(--text-muted); }
    
    .green-box strong { color: #15803d; }
    .yellow-box strong { color: #d97706; }
    .red-box strong { color: #dc2626; }

    /* Panel */
    .abs-panel { background: var(--spekta-white); border: 1px solid var(--border-soft); border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); }
    .abs-panel-head { margin-bottom: 20px; }
    .abs-panel-head h2 { margin: 0; color: var(--text-main); font-size: 15px; font-weight: 800; }
    .abs-panel-head p { margin: 4px 0 0; color: var(--text-muted); font-size: 11px; font-weight: 600; }
    
    .abs-table-wrap { overflow-x: auto; }
    .abs-table { width: 100%; border-collapse: collapse; min-width: 600px; }
    .abs-table th { text-align: left; padding: 12px 14px; font-size: 10px; color: var(--text-muted); text-transform: uppercase; border-bottom: 2px solid var(--spekta-gray-light); font-weight: 800; letter-spacing: 0.05em; }
    .abs-table td { padding: 14px; border-bottom: 1px solid var(--spekta-gray-light); vertical-align: middle; }
    .abs-table tbody tr:hover { background: #fafbfc; }
    
    /* Student Cell Profile */
    .abs-student-cell { display: flex; align-items: center; gap: 10px; }
    .abs-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        background: var(--spekta-teal-light);
        color: var(--spekta-teal);
        font-weight: 900;
        font-size: 13px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
    }
    .abs-student-name strong { display: block; color: var(--text-main); font-size: 13px; font-weight: 800; text-transform: uppercase; }
    .abs-student-name span { display: block; margin-top: 4px; color: var(--text-muted); font-size: 10px; font-weight: 700; }
    
    /* Badges */
    .abs-badge { display: inline-flex; align-items: center; height: 22px; padding: 0 10px; border-radius: 6px; font-size: 9px; font-weight: 800; text-transform: uppercase; }
    .abs-badge.hadir { background: #e6f7ed; color: #15803d; }
    .abs-badge.izin { background: #fff7ed; color: #c2410c; }
    .abs-badge.alpa { background: #fee2e2; color: #dc2626; }
    .abs-date-text { color: var(--text-muted); font-size: 11px; font-weight: 700; }
    .abs-date-text i { color: var(--spekta-gray); margin-right: 3px; }

    .abs-empty { padding: 40px; text-align: center; color: var(--text-muted); font-size: 11px; font-weight: 700; display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .abs-empty-icon { width: 48px; height: 48px; margin: 0 auto 8px; display: grid; place-items: center; border-radius: 50%; background: var(--spekta-gray-light); color: var(--spekta-gray); font-size: 18px; }

    @media (max-width: 850px) { .abs-header { flex-direction: column; align-items: flex-start; gap: 14px; } .abs-recap-summary { width: 100%; } .abs-recap-summary div { flex: 1; } }
</style>
@endsection