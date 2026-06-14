@extends('layouts.spekta')

@section('title', 'Pilih Paket Tryout')

@section('content')
<div class="cp-page">

    {{-- ── 1. HEADER MINIMALIS MODERN ── --}}
    <section class="cp-header">
        <div class="cp-header-left">
            <span class="cp-breadcrumb-capsule">Student Score Center</span>
            <h1>Paket Tryout: <span style="color: var(--spekta-teal);">{{ $class->program_name }}</span></h1>
            <p>Pilih salah satu paket di bawah ini untuk melihat rekapitulasi daftar nilai siswa.</p>
        </div>
        <div class="cp-header-actions">
            <a href="{{ route('admin.scores.index') }}" class="cp-secondary-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </section>

    {{-- ── 2. TABLE CARD (DENGAN PENANGANAN AMAN NULL & ARRAY) ── --}}
    <div class="cp-main-card">
        <div class="cp-table-wrap">
            <table class="cp-table">
                <thead>
                    <tr>
                        <th>Nama Paket</th>
                        <th>Durasi</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tryouts as $to)
                        <!-- Pengaman 1: Pastikan elemen $to tidak bernilai NULL -->
                        @if($to)
                            @php
                                // Pengaman 2: Konversi paksa array ke Object agar bisa dibaca dengan tanda ->
                                $toObj = (object) $to;
                                $duration = $toObj->duration ?? ($toObj->duration_minutes ?? 0);
                                $tryoutId = $toObj->tryout_id ?? ($toObj->id ?? 0);
                            @endphp
                            <tr>
                                {{-- Nama Paket --}}
                                <td class="to-package-title">
                                    <div class="to-package-cell">
                                        <div class="to-package-icon">
                                            <i class="fa-solid fa-file-invoice"></i>
                                        </div>
                                        <strong>{{ $toObj->title ?? 'Untitled Package' }}</strong>
                                    </div>
                                </td>

                                {{-- Durasi --}}
                                <td class="to-duration-cell">
                                    <i class="fa-regular fa-clock"></i> {{ $duration }} Menit
                                </td>

                                {{-- Aksi Rekap Nilai --}}
                                <td class="text-right">
                                    <a href="{{ route('admin.scores.result', $tryoutId) }}" class="to-btn-rekap">
                                        <span>REKAP NILAI</span> <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                        @if(isset($serviceError) && $serviceError)
    <div class="alert alert-warning">
        <i class="fa-solid fa-triangle-exclamation"></i>
        Server tryout sedang bermasalah. Data mungkin tidak lengkap.
    </div>
@endif

                    @empty
                    <tr>
                        <td colspan="3">
                            <div class="cp-empty-state">
                                <i class="fa-solid fa-folder-open"></i>
                                <span>Belum ada paket tryout yang diterbitkan untuk kelas ini.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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

    .cp-page {
        font-family: 'Montserrat', sans-serif;
        padding: 10px;
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* ── Header ── */
    .cp-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }
    .cp-breadcrumb-capsule {
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
    .cp-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .cp-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }
    .cp-secondary-btn {
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
    .cp-secondary-btn:hover {
        background: var(--spekta-gray-light);
        color: var(--text-main);
        border-color: var(--spekta-gray);
    }

    .cp-header-actions { display: flex; align-items: center; gap: 12px; }

    /* Table Container Card */
    .cp-main-card {
        background: var(--spekta-white);
        border-radius: 16px;
        padding: 20px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    }

    .cp-table-wrap { overflow-x: auto; border-radius: 12px; }
    .cp-table { width: 100%; border-collapse: collapse; min-width: 600px; }
    .cp-table th { text-align: left; padding: 12px 14px; font-size: 10px; color: var(--text-muted); text-transform: uppercase; border-bottom: 2px solid var(--spekta-gray-light); font-weight: 800; letter-spacing: 0.05em; }
    .cp-table td { padding: 14px; border-bottom: 1px solid var(--spekta-gray-light); vertical-align: middle; }
    .cp-table tbody tr:last-child td { border-bottom: none; }
    .cp-table tbody tr:hover { background: #fafbfc; }

    /* Package Title Cell */
    .to-package-cell { display: flex; align-items: center; gap: 10px; }
    .to-package-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: grid;
        place-items: center;
        background: var(--spekta-teal-light);
        color: var(--spekta-teal);
        font-size: 14px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        border: 1px solid var(--border-soft);
    }
    .to-package-cell strong { font-size: 13px; font-weight: 800; color: var(--text-main); }

    .to-duration-cell { color: var(--text-muted); font-size: 12px; font-weight: 700; }
    .to-duration-cell i { color: var(--spekta-gray); margin-right: 3px; }

    /* Rekap Nilai Button (Capsule) */
    .to-btn-rekap {
        background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%);
        color: var(--spekta-white) !important;
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 11px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 10px rgba(229, 57, 53, 0.15);
        transition: all 0.2s ease;
    }
    .to-btn-rekap:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(229, 57, 53, 0.25);
    }

    .cp-empty-state {
        padding: 40px;
        text-align: center;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .cp-empty-state i { font-size: 20px; color: var(--spekta-gray); }
    .text-right { text-align: right; }

    @media (max-width: 768px) {
        .cp-header { flex-direction: column; align-items: flex-start; gap: 14px; }
    }
</style>
@endsection
