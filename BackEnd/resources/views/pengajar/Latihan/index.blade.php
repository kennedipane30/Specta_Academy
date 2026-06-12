@extends('layouts.spekta')

@section('title', 'Materi Saya - Spekta Academy')

@section('content')
@php
    $totalAssignment = count($assignmentsWithSubjects ?? []);
@endphp

<div class="tm-container">

    {{-- ── 1. HEADER MINIMALIS MODERN ── --}}
    <section class="tm-header">
        <div class="tm-header-text">
            <span class="tm-breadcrumb-capsule">Teacher Portal</span>
            <h1>Materi Pembelajaran</h1>
            <p>Pilih bidang ajar Anda untuk mengelola modul materi dan file PDF mingguan secara berkala.</p>
        </div>
    </section>

    {{-- ── 2. STATS SUMMARY CARD ── --}}
    <section class="tm-stats">
        <div class="tm-stat-card card-teal">
            <div class="tm-stat-icon teal"><i class="fa-solid fa-briefcase"></i></div>
            <div class="tm-stat-info">
                <p>Total Penugasan</p>
                <h2>{{ $totalAssignment }} <span>Kelas</span></h2>
            </div>
        </div>
    </section>

    {{-- ── 3. LIST PANEL (DAFTAR BIDANG AJAR) ── --}}
    <section class="tm-card">
        <div class="tm-card-head">
            <div>
                <h2>Daftar Bidang Ajar</h2>
                <small>Semua kombinasi kelas dan mata pelajaran yang Anda ampu</small>
            </div>
        </div>

        <div class="tm-table-responsive">
            <table class="tm-table">
                <thead>
                    <tr>
                        <th style="width: 30%">Program Kelas</th>
                        <th style="width: 30%">Mata Pelajaran</th>
                        <th style="width: 20%">Durasi</th>
                        <th class="text-end" style="width: 20%">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($assignmentsWithSubjects ?? [] as $assign)
                        <tr>
                            {{-- Program Kelas --}}
                            <td>
                                <div class="tm-class-info">
                                    <strong>{{ $assign->classModel->program_name ?? 'Kelas' }}</strong>
                                    <small>ID #{{ $assign->class_id }}</small>
                                </div>
                            </td>

                            {{-- Mata Pelajaran --}}
                            <td>
                                <span class="tm-subject-pill">
                                    <i class="fa-solid fa-book-bookmark"></i>
                                    {{ $assign->subject_name ?? 'Mata Pelajaran' }}
                                </span>
                            </td>

                            {{-- Durasi --}}
                            <td>
                                <span class="tm-muted">20 Minggu</span>
                            </td>

                            {{-- Tombol Aksi Buka --}}
                            <td class="text-end">
                                <a href="{{ route('pengajar.materi.pilih', ['class_id' => $assign->class_id, 'subject_name' => $assign->subject_name]) }}"
                                   class="tm-btn-manage">
                                    <span>Kelola Materi</span> <i class="fa-solid fa-arrow-right-long"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="tm-empty">
                                    <div class="tm-empty-icon"><i class="fa-solid fa-folder-open"></i></div>
                                    <strong>Belum ada penugasan mengajar</strong>
                                    <span>Admin akademik belum mendaftarkan kelas pengampu untuk Anda saat ini.</span>
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

    .tm-container {
        padding: 10px;
        font-family: 'Montserrat', sans-serif;
        color: var(--text-main);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Header Minimalis */
    .tm-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }
    .tm-breadcrumb-capsule {
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
    .tm-header h1 {
        margin: 0 0 6px;
        color: var(--text-main);
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }
    .tm-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }

    /* Stats summary */
    .tm-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .tm-stat-card {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.01);
        transition: all 0.2s ease;
    }
    .tm-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.03);
    }
    .tm-stat-card.card-teal:hover { border-color: var(--spekta-teal); }

    .tm-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-size: 16px;
    }
    .tm-stat-icon.teal { background: var(--spekta-teal-light); color: var(--spekta-teal); }

    .tm-stat-info p {
        margin: 0 0 4px;
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .tm-stat-info h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        color: var(--text-main);
        line-height: 1;
        display: flex;
        align-items: baseline;
        gap: 4px;
    }
    .tm-stat-info h2 span {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
    }

    /* Card panel */
    .tm-card {
        background: var(--spekta-white);
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, .01);
        border: 1px solid var(--border-soft);
    }

    .tm-card-head { margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--spekta-gray-light); }
    .tm-card-head h2 { margin: 0; font-size: 15px; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 10px; }
    .tm-card-head small { color: var(--text-muted); font-size: 11px; font-weight: 600; margin-left: 28px; }

    .tm-table { width: 100%; border-collapse: collapse; }
    .tm-table th {
        padding: 12px 14px;
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        border-bottom: 2px solid var(--spekta-gray-light);
        text-align: left;
        letter-spacing: 0.05em;
    }

    .tm-table th.text-end, .tm-table td.text-end { text-align: right; }
    .tm-table td { padding: 14px; border-bottom: 1px solid var(--spekta-gray-light); vertical-align: middle; }
    .tm-table tr:hover { background: #fafbfc; }

    .tm-class-info strong { display: block; font-size: 13px; font-weight: 800; color: var(--text-main); }
    .tm-class-info small { color: var(--text-muted); font-weight: 700; font-size: 10px; }

    .tm-subject-pill {
        background: var(--spekta-red-light);
        padding: 6px 12px;
        border-radius: 6px;
        color: var(--spekta-red-dark);
        font-size: 11px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid rgba(229, 57, 53, 0.1);
    }

    /* BUTTONS */
    .tm-btn-manage {
        background: #1f2937;
        padding: 8px 14px;
        border-radius: 8px;
        text-decoration: none;
        color: var(--spekta-white) !important;
        font-size: 11px;
        font-weight: 800;
        display: inline-flex;
        gap: 6px;
        align-items: center;
        transition: all 0.2s ease;
    }

    .tm-btn-manage:hover {
        background: var(--spekta-red);
        box-shadow: 0 4px 10px rgba(229, 57, 53, 0.25);
    }

    .tm-muted { color: var(--text-muted); font-weight: 700; font-size: 12px; }
    .tm-empty { padding: 40px; text-align: center; color: var(--text-muted); font-size: 11px; font-weight: 700; display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .tm-empty-icon { width: 48px; height: 48px; margin: 0 auto 8px; display: grid; place-items: center; border-radius: 50%; background: var(--spekta-gray-light); color: var(--spekta-gray); font-size: 18px; }

    @media(max-width:1200px) {
        .tm-stats { grid-template-columns: 1fr; }
    }
    @media(max-width:768px) {
        .tm-table-responsive { overflow-x: auto; }
        .tm-panel-heading { flex-direction: column; align-items: stretch;}
    }
</style>
@endsection