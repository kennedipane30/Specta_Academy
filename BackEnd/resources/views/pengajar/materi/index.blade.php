@extends('layouts.spekta')

@section('title', 'Materi Saya')
@section('subtitle', 'Kelola materi pembelajaran Spekta Academy')

@section('content')
@php
    $assignmentCollection = collect($assignments);
    $totalAssignment = $assignmentCollection->count();
    $totalProgram = $assignmentCollection->pluck('class_id')->unique()->count();
    $totalSubject = $assignmentCollection->pluck('subject_name')->unique()->count();
@endphp

<div class="tm-page">

    {{-- HERO --}}
    <section class="tm-hero">
        <div>
            <span>Teacher Material Center</span>
            <h1>Materi Pembelajaran</h1>
            <p>Pilih program dan mata pelajaran yang Anda ampu untuk mengelola modul materi selama 20 minggu pembelajaran.</p>
        </div>

        <div class="tm-hero-summary">
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
        <div class="tm-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="tm-alert error">
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
    <section class="tm-panel">
        <div class="tm-panel-head">
            <div>
                <span>Teaching Assignment</span>
                <h2>Daftar Materi yang Dapat Dikelola</h2>
                <p>Setiap baris mewakili program kelas dan bidang ajar yang menjadi tanggung jawab Anda.</p>
            </div>
        </div>

        @if($assignments->isEmpty())
            <div class="tm-empty">
                <i class="fa-solid fa-book-open"></i>
                <strong>Belum ada penugasan materi.</strong>
                <span>Admin perlu menugaskan Anda pada program dan mata pelajaran tertentu terlebih dahulu.</span>
            </div>
        @else
            <div class="tm-table-wrap">
                <table class="tm-table">
                    <thead>
                        <tr>
                            <th>Program Kelas</th>
                            <th>Bidang Ajar</th>
                            <th>Struktur Materi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($assignments as $assign)
                            <tr>
                                <td>
                                    <div class="tm-program">
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
                                    <span class="tm-subject">
                                        {{ $assign->subject_name }}
                                    </span>
                                </td>

                                <td>
                                    <div class="tm-structure">
                                        <strong>20 Minggu</strong>
                                        <span>Modul pembelajaran bertahap</span>
                                    </div>
                                </td>

                                <td>
                                    <span class="tm-status">
                                        <i class="fa-solid fa-circle"></i>
                                        Aktif
                                    </span>
                                </td>

                                <td>
                                    <a href="{{ route('pengajar.materi.pilih', [$assign->class_id, $assign->subject_name]) }}" class="tm-action">
                                        Kelola Materi
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
    .tm-page {
        width: 100%;
    }

    .tm-hero {
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

    .tm-hero::after {
        content: "";
        width: 280px;
        height: 280px;
        border-radius: 999px;
        background: rgba(255,255,255,.09);
        position: absolute;
        right: -95px;
        top: -130px;
    }

    .tm-hero > div {
        position: relative;
        z-index: 2;
    }

    .tm-hero span,
    .tm-panel-head span {
        display: block;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .tm-hero > div:first-child > span {
        color: rgba(255,255,255,.78);
        margin-bottom: 10px;
    }

    .tm-hero h1 {
        margin: 0 0 8px;
        color: #fff;
        font-size: 31px;
        font-weight: 900;
        letter-spacing: -0.04em;
        text-transform: uppercase;
    }

    .tm-hero p {
        margin: 0;
        color: rgba(255,255,255,.86);
        font-size: 13px;
        font-weight: 600;
        line-height: 1.6;
        max-width: 760px;
    }

    .tm-hero-summary {
        display: flex;
        gap: 12px;
        flex-shrink: 0;
    }

    .tm-hero-summary div {
        min-width: 112px;
        padding: 16px;
        border-radius: 18px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.16);
        backdrop-filter: blur(12px);
        text-align: center;
    }

    .tm-hero-summary strong {
        display: block;
        font-size: 28px;
        font-weight: 900;
        line-height: 1;
    }

    .tm-hero-summary span {
        margin-top: 7px;
        color: rgba(255,255,255,.75);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0;
    }

    .tm-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        border-radius: 15px;
        margin-bottom: 18px;
        font-size: 12px;
        font-weight: 800;
    }

    .tm-alert.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .tm-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .tm-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .tm-panel {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        padding: 22px;
        box-shadow: 0 14px 35px rgba(15,23,42,.05);
    }

    .tm-panel-head {
        margin-bottom: 18px;
    }

    .tm-panel-head span {
        color: #d90429;
        margin-bottom: 8px;
    }

    .tm-panel-head h2 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .tm-panel-head p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
    }

    .tm-table-wrap {
        overflow-x: auto;
    }

    .tm-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tm-table th {
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

    .tm-table td {
        padding: 16px 12px;
        border-bottom: 1px solid #edf0f4;
        vertical-align: middle;
    }

    .tm-table tbody tr:hover {
        background: #fff7f9;
    }

    .tm-program {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .tm-program > div {
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

    .tm-program strong {
        display: block;
        color: #111827;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .tm-program span {
        display: block;
        margin-top: 4px;
        color: #9ca3af;
        font-size: 10px;
        font-weight: 700;
    }

    .tm-subject {
        display: inline-flex;
        align-items: center;
        height: 30px;
        padding: 0 11px;
        border-radius: 999px;
        background: #fff1f2;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .tm-structure strong {
        display: block;
        color: #111827;
        font-size: 12px;
        font-weight: 900;
    }

    .tm-structure span {
        display: block;
        margin-top: 4px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .tm-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 30px;
        padding: 0 11px;
        border-radius: 999px;
        background: #dcfce7;
        color: #16a34a;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .tm-status i {
        font-size: 7px;
    }

    .tm-action {
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

    .tm-empty {
        padding: 42px;
        text-align: center;
        background: #f8fafc;
        border-radius: 18px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .tm-empty i {
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

    .tm-empty strong {
        display: block;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    @media (max-width: 900px) {
        .tm-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .tm-hero-summary {
            width: 100%;
        }

        .tm-hero-summary div {
            flex: 1;
        }
    }
</style>
@endsection