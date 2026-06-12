@extends('layouts.spekta')

@section('title', 'Detail Absensi')

@section('content')
<div class="cp-page">

    {{-- ── 1. HEADER MINIMALIS ── --}}
    <section class="cp-header">
        <div class="cp-header-left">
            <span class="cp-breadcrumb-capsule">Attendance Detail</span>
            <h1>Recap {{ $subject }} - Week {{ $week }}</h1>
        </div>
        <div class="cp-header-actions">
            <a href="{{ route('pengajar.absensi.weeks', [$class->class_id, $subject]) }}" class="cp-secondary-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </section>

    {{-- ── 2. DETAIL TABLE CARD ── --}}
    <div class="cp-main-card">
        <div class="cp-table-wrap">
            <table class="cp-table">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th class="text-center">Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                    <tr>
                        {{-- Nama Siswa --}}
                        <td>
                            <div class="cp-student-cell">
                                <div class="cp-student-avatar">
                                    {{ strtoupper(substr($row->student->name ?? 'S', 0, 1)) }}
                                </div>
                                <div class="cp-student-info">
                                    <strong>{{ $row->student->name }}</strong>
                                    <span>Siswa Aktif</span>
                                </div>
                            </div>
                        </td>

                        {{-- Status Kehadiran Kapsul --}}
                        <td class="text-center">
                            <span class="cp-status-badge
                                {{ $row->status == 'h' ? 'hadir' : ($row->status == 'i' ? 'izin' : 'alpa') }}">
                                {{ $row->status == 'h' ? 'Hadir' : ($row->status == 'i' ? 'Izin' : 'Alpa') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
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

    /* Header Minimalis */
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
    .cp-header h1 span { color: var(--spekta-teal); }
    
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

    /* Table Container Card */
    .cp-main-card {
        background: var(--spekta-white);
        border: 1px solid var(--border-soft);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    }

    .cp-table-wrap { overflow-x: auto; border-radius: 12px; }
    .cp-table { width: 100%; border-collapse: collapse; min-width: 600px; }
    .cp-table th { text-align: left; padding: 12px 14px; font-size: 10px; color: var(--text-muted); text-transform: uppercase; border-bottom: 2px solid var(--spekta-gray-light); font-weight: 800; letter-spacing: 0.05em; }
    .cp-table td { padding: 14px; border-bottom: 1px solid var(--spekta-gray-light); vertical-align: middle; }
    .cp-table tbody tr:hover { background: #fafbfc; }

    /* Student Cell Profile */
    .cp-student-cell { display: flex; align-items: center; gap: 10px; }
    .cp-student-avatar {
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
    .cp-student-info strong { display: block; color: var(--text-main); font-size: 13px; font-weight: 800; text-transform: uppercase; }
    .cp-student-info span { display: block; margin-top: 4px; color: var(--text-muted); font-size: 10px; font-weight: 700; }

    /* Capsule Badge */
    .cp-status-badge {
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
    .cp-status-badge.hadir { background: #e6f7ed; color: #15803d; }
    .cp-status-badge.izin { background: #fff7ed; color: #c2410c; }
    .cp-status-badge.alpa { background: #fee2e2; color: #dc2626; }
    .text-center { text-align: center; }

    @media (max-width: 768px) {
        .cp-header { flex-direction: column; align-items: flex-start; gap: 14px; }
    }
</style>
@endsection