@extends('layouts.spekta')

@section('title', 'Kirim Soal Tryout')
@section('subtitle', 'Kelola paket soal tryout sesuai penugasan')

@section('content')
@php
    $assignmentCollection = collect($assignments);
    $totalAssignment = $assignmentCollection->count();
    $totalProgram = $assignmentCollection->pluck('class_id')->unique()->count();
    $totalSubject = $assignmentCollection->pluck('subject_name')->unique()->count();
@endphp

<div class="to-page">

    {{-- HERO --}}
    <section class="to-hero">
        <div>
            <span>Tryout Question Center</span>
            <h1>Kirim Soal Tryout</h1>
            <p>
                Pilih program kelas dan mata pelajaran yang Anda ampu untuk membuat atau mengelola paket soal tryout.
            </p>
        </div>

        <div class="to-hero-summary">
            <div>
                <strong>{{ $totalProgram }}</strong>
                <span>Program</span>
            </div>

            <div>
                <strong>{{ $totalSubject }}</strong>
                <span>Mapel</span>
            </div>

            <div>
                <strong>{{ $totalAssignment }}</strong>
                <span>Penugasan</span>
            </div>
        </div>
    </section>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="to-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="to-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="to-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Data belum valid.</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- MAIN PANEL --}}
    <section class="to-panel">
        <div class="to-panel-head">
            <div>
                <span>Teaching Assignment</span>
                <h2>Daftar Paket Tryout yang Dapat Dikelola</h2>
                <p>
                    Setiap baris menampilkan program dan bidang soal yang menjadi tanggung jawab Anda.
                </p>
            </div>
        </div>

        @if($assignments->isEmpty())
            <div class="to-empty">
                <i class="fa-solid fa-stopwatch"></i>
                <strong>Belum ada penugasan tryout.</strong>
                <span>Admin perlu menugaskan Anda pada program dan mata pelajaran tertentu terlebih dahulu.</span>
            </div>
        @else
            <div class="to-table-wrap">
                <table class="to-table">
                    <thead>
                        <tr>
                            <th>Program Kelas</th>
                            <th>Bidang Soal</th>
                            <th>Jenis Konten</th>
                            <th>Mode Input</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($assignments as $assign)
                            <tr>
                                <td>
                                    <div class="to-program">
                                        <div>
                                            {{ strtoupper(substr($assign->classModel->program_name ?? 'P', 0, 1)) }}
                                        </div>

                                        <section>
                                            <strong>{{ $assign->classModel->program_name ?? 'Program Kelas' }}</strong>
                                            <span>ID Kelas: {{ $assign->class_id }}</span>
                                        </section>
                                    </div>
                                </td>

                                <td>
                                    <span class="to-subject">
                                        {{ $assign->subject_name }}
                                    </span>
                                </td>

                                <td>
                                    <span class="to-type">
                                        <i class="fa-solid fa-stopwatch"></i>
                                        Tryout
                                    </span>
                                </td>

                                <td>
                                    <div class="to-mode">
                                        <strong>Input Paket Soal</strong>
                                        <span>Soal, opsi jawaban, kunci, dan pembahasan</span>
                                    </div>
                                </td>

                                <td>
                                    <span class="to-status">
                                        <i class="fa-solid fa-circle"></i>
                                        Aktif
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('pengajar.tryout.create', [$assign->class_id, $assign->subject_name]) }}" class="to-action">
                                        Buat Paket Soal
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

</div>

<style>
    .to-page {
        width: 100%;
    }

    .to-hero {
        position: relative;
        overflow: hidden;
        background: linear-gradient(120deg, #cf002b 0%, #85001d 52%, #182033 100%);
        border-radius: 24px;
        padding: 30px 34px;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
        margin-bottom: 22px;
        box-shadow: 0 18px 38px rgba(134, 0, 24, .20);
    }

    .to-hero::after {
        content: "";
        width: 280px;
        height: 280px;
        border-radius: 999px;
        background: rgba(255,255,255,.09);
        position: absolute;
        right: -95px;
        top: -130px;
    }

    .to-hero > div {
        position: relative;
        z-index: 2;
    }

    .to-hero span,
    .to-panel-head span {
        display: block;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .to-hero > div:first-child > span {
        color: rgba(255,255,255,.78);
        margin-bottom: 10px;
    }

    .to-hero h1 {
        margin: 0 0 8px;
        color: #fff;
        font-size: 31px;
        font-weight: 900;
        letter-spacing: -0.04em;
        text-transform: uppercase;
    }

    .to-hero p {
        margin: 0;
        color: rgba(255,255,255,.86);
        font-size: 13px;
        font-weight: 600;
        line-height: 1.6;
        max-width: 760px;
    }

    .to-hero-summary {
        display: flex;
        gap: 12px;
        flex-shrink: 0;
    }

    .to-hero-summary div {
        min-width: 112px;
        padding: 16px;
        border-radius: 18px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.16);
        backdrop-filter: blur(12px);
        text-align: center;
    }

    .to-hero-summary strong {
        display: block;
        font-size: 28px;
        font-weight: 900;
        line-height: 1;
    }

    .to-hero-summary span {
        margin-top: 7px;
        color: rgba(255,255,255,.75);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0;
    }

    .to-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        border-radius: 15px;
        margin-bottom: 18px;
        font-size: 12px;
        font-weight: 800;
    }

    .to-alert.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .to-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .to-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .to-panel {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        padding: 22px;
        box-shadow: 0 14px 35px rgba(15,23,42,.05);
    }

    .to-panel-head {
        margin-bottom: 18px;
    }

    .to-panel-head span {
        color: #d90429;
        margin-bottom: 8px;
    }

    .to-panel-head h2 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .to-panel-head p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
    }

    .to-table-wrap {
        overflow-x: auto;
    }

    .to-table {
        width: 100%;
        border-collapse: collapse;
    }

    .to-table th {
        text-align: left;
        padding: 14px 12px;
        border-bottom: 1px solid #edf0f4;
        color: #6b7280;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
        white-space: nowrap;
    }

    .to-table td {
        padding: 16px 12px;
        border-bottom: 1px solid #edf0f4;
        vertical-align: middle;
    }

    .to-table tbody tr:hover {
        background: #fff7f9;
    }

    .to-program {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .to-program > div {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 14px;
        background: #ffe8ee;
        color: #d90429;
        font-size: 14px;
        font-weight: 900;
        flex-shrink: 0;
    }

    .to-program strong {
        display: block;
        color: #111827;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .to-program span {
        display: block;
        margin-top: 4px;
        color: #9ca3af;
        font-size: 10px;
        font-weight: 700;
    }

    .to-subject,
    .to-type,
    .to-status {
        display: inline-flex;
        align-items: center;
        height: 30px;
        padding: 0 11px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .to-subject {
        background: #fff1f2;
        color: #d90429;
    }

    .to-type {
        gap: 7px;
        background: #ffedd5;
        color: #ea580c;
    }

    .to-status {
        gap: 7px;
        background: #dcfce7;
        color: #16a34a;
    }

    .to-status i {
        font-size: 7px;
    }

    .to-mode strong {
        display: block;
        color: #111827;
        font-size: 12px;
        font-weight: 900;
    }

    .to-mode span {
        display: block;
        margin-top: 4px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .to-action {
        height: 38px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0 14px;
        border-radius: 12px;
        background: #d90429;
        color: #fff;
        font-size: 11px;
        font-weight: 900;
        white-space: nowrap;
    }

    .to-empty {
        padding: 42px;
        text-align: center;
        background: #f8fafc;
        border-radius: 18px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .to-empty i {
        width: 58px;
        height: 58px;
        margin: 0 auto 14px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #ffe8ee;
        color: #d90429;
        font-size: 22px;
    }

    .to-empty strong {
        display: block;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    @media (max-width: 900px) {
        .to-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .to-hero-summary {
            width: 100%;
        }

        .to-hero-summary div {
            flex: 1;
        }
    }
</style>
@endsection