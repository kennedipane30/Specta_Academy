@extends('layouts.spekta')

@section('title', 'Bank Soal Tryout - Spekta Academy')

@section('content')
@php
    $assignmentCollection = collect($assignments);
    $totalAssignment = $assignmentCollection->count();
    
    // Menghitung total seluruh soal yang sudah disetor guru ini ke Admin
    $totalSoalSelesai = \DB::table('tryout_drafts')
        ->whereIn('class_id', $assignmentCollection->pluck('class_id'))
        ->whereIn('subject_name', $assignmentCollection->pluck('subject_name'))
        ->count();
@endphp

<div class="cp-page">
    {{-- 1. HEADER HERO SECTION --}}
    <section class="tm-hero-header">
        <div class="tm-hero-content">
            <div class="tm-hero-text">
                <span class="tm-pre-title">TEACHER TRYOUT PORTAL</span>
                <h1 class="tm-main-title">Tryout Question Center</h1>
                <p class="tm-sub-title">Kontribusikan draf soal terbaik Anda. Admin akan mengkurasi dan menggabungkan draf tersebut menjadi satu paket Tryout resmi di aplikasi mobile.</p>
            </div>
        </div>
        
        <div class="tm-hero-summary">
            <div class="summary-card">
                <i class="fa-solid fa-briefcase"></i>
                <div class="summary-data">
                    <strong>{{ $totalAssignment }}</strong>
                    <span>Penugasan</span>
                </div>
            </div>
            <div class="summary-card highlight">
                <i class="fa-solid fa-file-export"></i>
                <div class="summary-data">
                    <strong>{{ $totalSoalSelesai }}</strong>
                    <span>Total Soal</span>
                </div>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="tm-alert-modern success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- 2. MAIN TABLE SECTION --}}
    <section class="cp-main-card">
        <div class="card-header-flex">
            <div>
                <h2>Daftar Penugasan Soal</h2>
                <p>Silakan klik tombol input pada bidang ajar Anda untuk menyetor soal baru.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="cp-table-modern">
                <thead>
                    <tr>
                        <th width="35%">PROGRAM KELAS</th>
                        <th width="20%">MATA PELAJARAN</th>
                        <th width="25%">PROGRESS ANDA</th>
                        <th width="20%" class="text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assign)
                        @php 
                            $count = \DB::table('tryout_drafts')
                                ->where('class_id', $assign->class_id)
                                ->where('subject_name', $assign->subject_name)
                                ->count();
                        @endphp
                        <tr>
                            <td>
                                <div class="program-info">
                                    <div class="program-icon-box">
                                        <i class="fa-solid fa-school-flag"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $assign->classModel->program_name ?? 'Program Kelas' }}</strong>
                                        <small>ID Kelas: #{{ $assign->class_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="subject-tag">{{ $assign->subject_name }}</span>
                            </td>
                            <td>
                                <div class="contribution-info {{ $count > 0 ? 'active' : '' }}">
                                    <div class="info-content">
                                        <strong>{{ $count }} Soal</strong>
                                        <span>Berhasil terkirim</span>
                                    </div>
                                    @if($count > 0)
                                        <i class="fa-solid fa-circle-check check-icon"></i>
                                    @endif
                                </div>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('pengajar.tryout.create', [$assign->class_id, $assign->subject_name]) }}" class="btn-input-modern">
                                    <span>Input Soal</span>
                                    <div class="icon-circle">
                                        <i class="fa-solid fa-pen-nib"></i>
                                    </div>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">
                                <div class="empty-wrap">
                                    <i class="fa-solid fa-stopwatch-20"></i>
                                    <p>Belum ada jadwal penugasan tryout untuk akun Anda.</p>
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
    /* Premium Page Styling */
    .cp-page { padding: 10px; font-family: 'Montserrat', sans-serif; animation: fadeIn 0.5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    /* Hero Section */
    .tm-hero-header {
        background: linear-gradient(135deg, #111827 0%, #1e293b 100%);
        border-radius: 28px;
        padding: 40px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
    }
    .tm-pre-title { font-size: 10px; font-weight: 800; letter-spacing: 2px; color: #d90429; text-transform: uppercase; }
    .tm-main-title { font-size: 34px; font-weight: 900; margin: 10px 0; letter-spacing: -1px; }
    .tm-sub-title { font-size: 14px; opacity: 0.7; max-width: 600px; line-height: 1.6; font-weight: 500; }

    /* Summary Cards */
    .tm-hero-summary { display: flex; gap: 15px; }
    .summary-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        padding: 18px 22px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        backdrop-filter: blur(10px);
    }
    .summary-card i { font-size: 24px; color: #fff; opacity: 0.5; }
    .summary-card.highlight { background: #d90429; border: none; }
    .summary-card.highlight i { opacity: 1; }
    .summary-card strong { display: block; font-size: 24px; font-weight: 900; }
    .summary-card span { font-size: 10px; font-weight: 700; text-transform: uppercase; opacity: 0.8; }

    /* Main Panel Card */
    .cp-main-card { background: white; border-radius: 30px; padding: 30px; border: 1px solid #f1f5f9; box-shadow: 0 10px 40px rgba(0,0,0,0.02); }
    .card-header-flex { margin-bottom: 30px; border-bottom: 2px solid #f8fafc; padding-bottom: 20px; }
    .card-header-flex h2 { font-size: 20px; font-weight: 900; color: #111827; }
    .card-header-flex p { font-size: 13px; color: #64748b; margin-top: 5px; font-weight: 600; }

    /* Table Styling */
    .cp-table-modern { width: 100%; border-collapse: collapse; }
    .cp-table-modern th { text-align: left; padding: 15px; font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
    .cp-table-modern td { padding: 22px 15px; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
    .cp-table-modern tr:hover { background: #fafbfc; }

    .program-info { display: flex; align-items: center; gap: 15px; }
    .program-icon-box { width: 44px; height: 44px; background: #f1f5f9; color: #111827; border-radius: 14px; display: grid; place-items: center; font-size: 18px; }
    .program-info strong { display: block; font-size: 14px; color: #111827; text-transform: uppercase; letter-spacing: -0.2px; }
    .program-info small { font-size: 11px; color: #94a3b8; font-weight: 700; }

    .subject-tag { background: #f1f5f9; color: #475569; padding: 8px 16px; border-radius: 12px; font-weight: 800; font-size: 11px; text-transform: uppercase; }

    /* Contribution Progress */
    .contribution-info { display: flex; align-items: center; justify-content: space-between; padding: 10px 15px; background: #f8fafc; border-radius: 14px; border: 1px solid #edf2f7; }
    .contribution-info.active { border-color: #dcfce7; background: #f0fdf4; }
    .info-content strong { display: block; font-size: 14px; color: #111827; }
    .info-content span { font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
    .check-icon { color: #10b981; font-size: 18px; }

    /* Modern Action Button */
    .btn-input-modern {
        display: inline-flex; align-items: center; gap: 12px;
        background: #111827; color: white; padding: 6px 6px 6px 20px;
        border-radius: 15px; text-decoration: none; transition: 0.3s;
    }
    .btn-input-modern span { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .btn-input-modern .icon-circle { width: 34px; height: 34px; background: rgba(255,255,255,0.1); border-radius: 11px; display: grid; place-items: center; transition: 0.3s; }
    
    .btn-input-modern:hover { background: #d90429; transform: translateX(-5px); box-shadow: 0 10px 20px rgba(217, 4, 41, 0.2); }
    .btn-input-modern:hover .icon-circle { background: white; color: #d90429; }

    .text-right { text-align: right; }
    .tm-alert-modern { padding: 15px 25px; border-radius: 16px; margin-bottom: 25px; background: #dcfce7; color: #15803d; font-weight: 800; display: flex; align-items: center; gap: 15px; border-left: 5px solid #22c55e; }

    .empty-state { text-align: center; padding: 80px !important; }
    .empty-wrap i { font-size: 50px; color: #e2e8f0; margin-bottom: 20px; display: block; }
    .empty-wrap p { color: #94a3b8; font-weight: 700; font-size: 14px; }
</style>
@endsection