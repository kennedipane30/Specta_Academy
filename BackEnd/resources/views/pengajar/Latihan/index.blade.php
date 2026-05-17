@extends('layouts.spekta')

@section('title', 'Pilih Program Latihan')
@section('subtitle', 'Kelola bank latihan soal sesuai penugasan')

@section('content')
@php
    $assignmentCollection = collect($assignments);
    $totalAssignment = $assignmentCollection->count();
    $totalProgram = $assignmentCollection->pluck('class_id')->unique()->count();
    $totalSubject = $assignmentCollection->pluck('subject_name')->unique()->count();
@endphp

<div class="pq-page">

    {{-- HERO --}}
    <section class="pq-hero">
        <div>
            <span>Practice Question Center</span>
            <h1>Latihan Soal</h1>
            <p>
                Kelola bank soal latihan berbasis CSV sesuai program kelas dan bidang ajar yang Anda ampu.
            </p>
        </div>

        <div class="pq-hero-summary">
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

    @if(session('success'))
        <div class="pq-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="pq-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="pq-alert error">
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
    <section class="pq-panel">
        <div class="pq-panel-head">
            <div>
                <span>Teaching Assignment</span>
                <h2>Daftar Bank Soal yang Dapat Dikelola</h2>
                <p>Pilih salah satu program dan bidang soal untuk mengunggah latihan mingguan.</p>
            </div>
        </div>

        @if($assignments->isEmpty())
            <div class="pq-empty">
                <i class="fa-solid fa-clipboard-question"></i>
                <strong>Belum ada penugasan latihan soal.</strong>
                <span>Admin perlu menugaskan Anda pada program dan mata pelajaran tertentu terlebih dahulu.</span>
            </div>
        @else
            <div class="pq-table-wrap">
                <table class="pq-table">
                    <thead>
                        <tr>
                            <th>Program Kelas</th>
                            <th>Bidang Soal</th>
                            <th>Format Upload</th>
                            <th>Struktur</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($assignments as $assign)
                            <tr>
                                <td>
                                    <div class="pq-program">
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
                                    <span class="pq-subject">
                                        {{ $assign->subject_name }}
                                    </span>
                                </td>

                                <td>
                                    <span class="pq-format">
                                        <i class="fa-solid fa-file-csv"></i>
                                        CSV
                                    </span>
                                </td>

                                <td>
                                    <div class="pq-structure">
                                        <strong>20 Minggu</strong>
                                        <span>Bank soal latihan bertahap</span>
                                    </div>
                                </td>

                                <td>
                                    <span class="pq-status">
                                        <i class="fa-solid fa-circle"></i>
                                        Aktif
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('pengajar.latihan.pilih', [$assign->class_id, $assign->subject_name]) }}" class="pq-action">
                                        Kelola Bank Soal
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
    .pq-page {
        width: 100%;
    }

    .pq-hero {
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

    .pq-hero::after {
        content: "";
        width: 280px;
        height: 280px;
        border-radius: 999px;
        background: rgba(255,255,255,.09);
        position: absolute;
        right: -95px;
        top: -130px;
    }

    .pq-hero > div {
        position: relative;
        z-index: 2;
    }

    .pq-hero span,
    .pq-panel-head span {
        display: block;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .pq-hero > div:first-child > span {
        color: rgba(255,255,255,.78);
        margin-bottom: 10px;
    }

    .pq-hero h1 {
        margin: 0 0 8px;
        color: #fff;
        font-size: 31px;
        font-weight: 900;
        letter-spacing: -0.04em;
        text-transform: uppercase;
    }

    .pq-hero p {
        margin: 0;
        color: rgba(255,255,255,.86);
        font-size: 13px;
        font-weight: 600;
        line-height: 1.6;
        max-width: 760px;
    }

    .pq-hero-summary {
        display: flex;
        gap: 12px;
        flex-shrink: 0;
    }

    .pq-hero-summary div {
        min-width: 112px;
        padding: 16px;
        border-radius: 18px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.16);
        backdrop-filter: blur(12px);
        text-align: center;
    }

    .pq-hero-summary strong {
        display: block;
        font-size: 28px;
        font-weight: 900;
        line-height: 1;
    }

    .pq-hero-summary span {
        margin-top: 7px;
        color: rgba(255,255,255,.75);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0;
    }

    .pq-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        border-radius: 15px;
        margin-bottom: 18px;
        font-size: 12px;
        font-weight: 800;
    }

    .pq-alert.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .pq-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .pq-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .pq-panel {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        padding: 22px;
        box-shadow: 0 14px 35px rgba(15,23,42,.05);
    }

    .pq-panel-head {
        margin-bottom: 18px;
    }

    .pq-panel-head span {
        color: #d90429;
        margin-bottom: 8px;
    }

    .pq-panel-head h2 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .pq-panel-head p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
    }

    .pq-table-wrap {
        overflow-x: auto;
    }

    .pq-table {
        width: 100%;
        border-collapse: collapse;
    }

    .pq-table th {
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

    .pq-table td {
        padding: 16px 12px;
        border-bottom: 1px solid #edf0f4;
        vertical-align: middle;
    }

    .pq-table tbody tr:hover {
        background: #fff7f9;
    }

    .pq-program {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .pq-program > div {
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

    .pq-program strong {
        display: block;
        color: #111827;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .pq-program span {
        display: block;
        margin-top: 4px;
        color: #9ca3af;
        font-size: 10px;
        font-weight: 700;
    }

    .pq-subject,
    .pq-format,
    .pq-status {
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

    .pq-subject {
        background: #fff1f2;
        color: #d90429;
    }

    .pq-format {
        gap: 7px;
        background: #dbeafe;
        color: #2563eb;
    }

    .pq-status {
        gap: 7px;
        background: #dcfce7;
        color: #16a34a;
    }

    .pq-status i {
        font-size: 7px;
    }

    .pq-structure strong {
        display: block;
        color: #111827;
        font-size: 12px;
        font-weight: 900;
    }

    .pq-structure span {
        display: block;
        margin-top: 4px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .pq-action {
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

    .pq-empty {
        padding: 42px;
        text-align: center;
        background: #f8fafc;
        border-radius: 18px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .pq-empty i {
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

    .pq-empty strong {
        display: block;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    @media (max-width: 900px) {
        .pq-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .pq-hero-summary {
            width: 100%;
        }

        .pq-hero-summary div {
            flex: 1;
        }
    }
</style>
@endsection