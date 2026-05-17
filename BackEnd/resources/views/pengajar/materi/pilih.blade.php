@extends('layouts.spekta')

@section('title', 'Upload ' . $subject_name)
@section('subtitle', 'Kelola materi pembelajaran mingguan')

@section('content')
@php
    $materiCollection = collect($materis);
    $materiByWeek = $materiCollection->keyBy('week');
    $filledWeeks = $materiCollection->pluck('week')->unique()->count();
    $progress = round(($filledWeeks / 20) * 100);
@endphp

<div class="tm-page">

    {{-- HEADER --}}
    <section class="tm-detail-header">
        <div>
            <a href="{{ route('pengajar.materi.index') }}" class="tm-back">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>

            <span>Material Weekly Manager</span>
            <h1>{{ $subject_name }}</h1>
            <p>{{ $class->program_name }} • Kelola materi pembelajaran untuk 20 minggu.</p>
        </div>

        <div class="tm-progress-box">
            <strong>{{ $filledWeeks }}/20</strong>
            <span>Minggu terisi</span>
            <div>
                <em style="width: {{ $progress }}%"></em>
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

    {{-- UPLOAD FORM --}}
    <section class="tm-upload-panel">
        <div class="tm-upload-head">
            <div>
                <span>Upload Module</span>
                <h2>Tambah atau Perbarui Materi</h2>
                <p>Pilih minggu, isi judul materi, lalu unggah file PDF modul pembelajaran.</p>
            </div>
        </div>

        <form action="{{ route('pengajar.materi.store', $class->class_id) }}" method="POST" enctype="multipart/form-data" class="tm-upload-form">
            @csrf

            <input type="hidden" name="material_name" value="{{ $subject_name }}">

            <div class="tm-field">
                <label>Minggu Ke-</label>
                <div>
                    <i class="fa-solid fa-calendar-week"></i>
                    <select name="week" required>
                        @for($i = 1; $i <= 20; $i++)
                            <option value="{{ $i }}" {{ old('week') == $i ? 'selected' : '' }}>
                                Minggu {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="tm-field title">
                <label>Judul Materi</label>
                <div>
                    <i class="fa-solid fa-heading"></i>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Persamaan Kuadrat" required>
                </div>
            </div>

            <div class="tm-field file">
                <label>File PDF</label>
                <div>
                    <i class="fa-solid fa-file-pdf"></i>
                    <input type="file" name="file_pdf" accept="application/pdf" required>
                </div>
            </div>

            <button type="submit" class="tm-submit">
                <i class="fa-solid fa-upload"></i>
                Simpan Materi
            </button>
        </form>
    </section>

    {{-- WEEK OVERVIEW --}}
    <section class="tm-week-panel">
        <div class="tm-panel-head">
            <div>
                <span>Weekly Content</span>
                <h2>Daftar Materi Mingguan</h2>
                <p>Pantau minggu mana yang sudah memiliki materi dan akses file PDF yang sudah diunggah.</p>
            </div>
        </div>

        <div class="tm-week-strip">
            @for($i = 1; $i <= 20; $i++)
                @php $hasMaterial = $materiByWeek->has($i); @endphp

                <div class="tm-week-dot {{ $hasMaterial ? 'filled' : '' }}">
                    <span>{{ $i }}</span>
                </div>
            @endfor
        </div>

        <div class="tm-table-wrap">
            <table class="tm-table">
                <thead>
                    <tr>
                        <th>Minggu</th>
                        <th>Judul Materi</th>
                        <th>File</th>
                        <th>Tanggal Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($materis as $m)
                        <tr>
                            <td>
                                <span class="tm-week-badge">
                                    MG-{{ $m->week }}
                                </span>
                            </td>

                            <td>
                                <div class="tm-material-title">
                                    <strong>{{ $m->title }}</strong>
                                    <span>{{ $m->material_name ?? $subject_name }}</span>
                                </div>
                            </td>

                            <td>
                                @if($m->file_path)
                                    <span class="tm-file-status active">
                                        <i class="fa-solid fa-file-pdf"></i>
                                        PDF tersedia
                                    </span>
                                @else
                                    <span class="tm-file-status empty">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        Belum ada file
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="tm-date-text">
                                    {{ $m->created_at ? $m->created_at->translatedFormat('d M Y') : '-' }}
                                </span>
                            </td>

                            <td>
                                @if($m->file_path)
                                    <a href="{{ asset('storage/' . $m->file_path) }}" target="_blank" class="tm-download">
                                        Download
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                @else
                                    <span class="tm-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="tm-empty">
                                    <i class="fa-solid fa-file-circle-plus"></i>
                                    <strong>Belum ada materi yang diunggah.</strong>
                                    <span>Gunakan form di atas untuk mengunggah materi pertama.</span>
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
    .tm-page {
        width: 100%;
    }

    .tm-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 24px;
        margin-bottom: 22px;
        padding: 28px 30px;
        border-radius: 24px;
        color: #fff;
        background: linear-gradient(120deg, #cf002b 0%, #85001d 52%, #182033 100%);
        box-shadow: 0 18px 38px rgba(134, 0, 24, .18);
    }

    .tm-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        background: rgba(255,255,255,.13);
        border: 1px solid rgba(255,255,255,.17);
        color: #fff;
        font-size: 11px;
        font-weight: 900;
        margin-bottom: 18px;
    }

    .tm-detail-header span {
        display: block;
        color: rgba(255,255,255,.78);
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .16em;
        margin-bottom: 9px;
    }

    .tm-detail-header h1 {
        margin: 0 0 8px;
        color: #fff;
        font-size: 30px;
        font-weight: 900;
        letter-spacing: -0.04em;
        text-transform: uppercase;
    }

    .tm-detail-header p {
        margin: 0;
        color: rgba(255,255,255,.86);
        font-size: 13px;
        font-weight: 700;
    }

    .tm-progress-box {
        width: 230px;
        flex-shrink: 0;
        padding: 18px;
        border-radius: 20px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.17);
        backdrop-filter: blur(12px);
    }

    .tm-progress-box strong {
        display: block;
        color: #fff;
        font-size: 28px;
        font-weight: 900;
        line-height: 1;
    }

    .tm-progress-box span {
        margin: 8px 0 14px;
        color: rgba(255,255,255,.75);
        letter-spacing: 0;
    }

    .tm-progress-box div {
        height: 8px;
        border-radius: 999px;
        background: rgba(255,255,255,.25);
        overflow: hidden;
    }

    .tm-progress-box em {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: #fff;
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

    .tm-upload-panel,
    .tm-week-panel {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        padding: 22px;
        box-shadow: 0 14px 35px rgba(15,23,42,.05);
        margin-bottom: 22px;
    }

    .tm-upload-head,
    .tm-panel-head {
        margin-bottom: 18px;
    }

    .tm-upload-head span,
    .tm-panel-head span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .16em;
        margin-bottom: 8px;
    }

    .tm-upload-head h2,
    .tm-panel-head h2 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .tm-upload-head p,
    .tm-panel-head p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
    }

    .tm-upload-form {
        display: grid;
        grid-template-columns: 170px minmax(0, 1fr) 260px 160px;
        gap: 14px;
        align-items: end;
    }

    .tm-field label {
        display: block;
        color: #374151;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: 8px;
    }

    .tm-field div {
        position: relative;
    }

    .tm-field i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 13px;
    }

    .tm-field select,
    .tm-field input {
        width: 100%;
        height: 48px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
        padding: 0 15px 0 42px;
        color: #111827;
        font-size: 12px;
        font-weight: 800;
        outline: none;
        font-family: inherit;
    }

    .tm-field input[type="file"] {
        padding-top: 13px;
    }

    .tm-field select:focus,
    .tm-field input:focus {
        background: #fff;
        border-color: #fecdd3;
        box-shadow: 0 0 0 4px rgba(217, 4, 41, .08);
    }

    .tm-submit {
        height: 48px;
        border: none;
        border-radius: 14px;
        background: #d90429;
        color: #fff;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 9px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        cursor: pointer;
        font-family: inherit;
        box-shadow: 0 14px 28px rgba(217, 4, 41, .20);
    }

    .tm-week-strip {
        display: grid;
        grid-template-columns: repeat(20, minmax(0, 1fr));
        gap: 6px;
        padding: 14px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #edf0f4;
        margin-bottom: 18px;
    }

    .tm-week-dot {
        min-height: 34px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: #fff;
        border: 1px solid #e5e7eb;
    }

    .tm-week-dot span {
        color: #9ca3af;
        font-size: 10px;
        font-weight: 900;
    }

    .tm-week-dot.filled {
        background: #dcfce7;
        border-color: #bbf7d0;
    }

    .tm-week-dot.filled span {
        color: #16a34a;
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

    .tm-week-badge {
        display: inline-flex;
        align-items: center;
        height: 30px;
        padding: 0 11px;
        border-radius: 999px;
        background: #fff1f2;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .tm-material-title strong {
        display: block;
        color: #111827;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .tm-material-title span {
        display: block;
        margin-top: 4px;
        color: #9ca3af;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .tm-file-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 30px;
        padding: 0 11px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .tm-file-status.active {
        background: #dcfce7;
        color: #16a34a;
    }

    .tm-file-status.empty {
        background: #fee2e2;
        color: #dc2626;
    }

    .tm-date-text {
        color: #6b7280;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .tm-download {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 36px;
        padding: 0 13px;
        border-radius: 12px;
        background: #dbeafe;
        color: #2563eb;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .tm-muted {
        color: #9ca3af;
        font-size: 12px;
        font-weight: 900;
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

    @media (max-width: 1200px) {
        .tm-upload-form {
            grid-template-columns: 1fr 1fr;
        }

        .tm-submit {
            grid-column: 1 / -1;
        }

        .tm-week-strip {
            grid-template-columns: repeat(10, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .tm-detail-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .tm-progress-box {
            width: 100%;
        }

        .tm-upload-form {
            grid-template-columns: 1fr;
        }

        .tm-week-strip {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }
    }
</style>
@endsection