@extends('layouts.spekta')

@section('title', 'Input Absensi')

@section('content')
<div class="abs-page">

    <section class="abs-input-header">
        <div>
            <a href="{{ route('pengajar.absensi.weeks', [$class->class_id, $subject]) }}" class="abs-back">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>

            <span>Attendance Input</span>
            <h1>{{ $subject }}</h1>
            <p>{{ $class->program_name }} • Minggu ke-{{ $week }}</p>
        </div>

        <div class="abs-date-info">
            <span>Waktu Input</span>
            <strong>{{ date('d M Y') }}</strong>
        </div>
    </section>

    <form action="{{ route('pengajar.absensi.store') }}" method="POST" class="abs-input-panel">
        @csrf

        <input type="hidden" name="class_id" value="{{ $class->class_id }}">
        <input type="hidden" name="subject_name" value="{{ $subject }}">
        <input type="hidden" name="week" value="{{ $week }}">

        <div class="abs-input-head">
            <div>
                <h2>Daftar Siswa</h2>
                <p>Pilih status kehadiran setiap siswa: Hadir, Izin, atau Alpa.</p>
            </div>

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
                        <div class="abs-student-number">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </div>

                        <div class="abs-student-info">
                            <strong>{{ $s->user->name }}</strong>
                            <span>Siswa Aktif</span>
                        </div>

                        <div class="abs-radio-group">
                            <label>
                                <input type="radio"
                                        name="status[{{ $s->user->usersID }}]"
                                        value="h"
                                        class="hidden peer"
                                        {{ ($existingAttendance[$s->user->usersID] ?? null) === 'h' ? 'checked' : '' }}
                                        required>
                                <span class="hadir">Hadir</span>
                            </label>

                            <label>
                                <input type="radio"
                                        name="status[{{ $s->user->usersID }}]"
                                        value="i"
                                        class="hidden peer"
                                        {{ ($existingAttendance[$s->user->usersID] ?? null) === 'i' ? 'checked' : '' }}>
                                <span class="izin">Izin</span>
                            </label>

                            <label>
                                <input type="radio"
                                        name="status[{{ $s->user->usersID }}]"
                                        value="a"
                                        class="hidden peer"
                                        {{ ($existingAttendance[$s->user->usersID] ?? null) === 'a' ? 'checked' : '' }}>
                                <span class="alpa">Alpa</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="abs-submit-bar">
                <div>
                    <strong>{{ $siswas->count() }}</strong>
                    <span>siswa akan diproses untuk absensi minggu {{ $week }}</span>
                </div>

                <button type="submit">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Absensi
                </button>
            </div>
        @else
            <div class="abs-empty">
                <i class="fa-solid fa-user-slash"></i>
                <strong>Belum ada siswa aktif di kelas ini.</strong>
                <span>Absensi dapat dilakukan setelah siswa aktif tersedia pada program kelas.</span>
            </div>
        @endif
    </form>

</div>

<style>
    .abs-page {
        width: 100%;
    }

    .abs-input-header {
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

    .abs-back {
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

    .abs-input-header span {
        display: block;
        color: rgba(255,255,255,.78);
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .16em;
        margin-bottom: 9px;
    }

    .abs-input-header h1 {
        margin: 0 0 8px;
        color: #fff;
        font-size: 30px;
        font-weight: 900;
        letter-spacing: -0.04em;
        text-transform: uppercase;
    }

    .abs-input-header p {
        margin: 0;
        color: rgba(255,255,255,.86);
        font-size: 13px;
        font-weight: 700;
    }

    .abs-date-info {
        flex-shrink: 0;
        min-width: 190px;
        padding: 18px;
        border-radius: 18px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.17);
        text-align: right;
    }

    .abs-date-info span {
        margin-bottom: 7px;
    }

    .abs-date-info strong {
        display: block;
        color: #fff;
        font-size: 18px;
        font-weight: 900;
    }

    .abs-input-panel {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 22px;
        padding: 22px;
        box-shadow: 0 14px 35px rgba(15,23,42,.05);
    }

    .abs-input-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        padding-bottom: 18px;
        border-bottom: 1px solid #edf0f4;
        margin-bottom: 12px;
    }

    .abs-input-head h2 {
        margin: 0;
        color: #111827;
        font-size: 18px;
        font-weight: 900;
    }

    .abs-input-head p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
    }

    .abs-legend {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .abs-legend span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 30px;
        padding: 0 10px;
        border-radius: 999px;
        background: #f8fafc;
        color: #6b7280;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .abs-legend i {
        width: 8px;
        height: 8px;
        border-radius: 999px;
    }

    .abs-legend .green { background: #16a34a; }
    .abs-legend .yellow { background: #f59e0b; }
    .abs-legend .red { background: #dc2626; }

    .abs-student-list {
        display: grid;
    }

    .abs-student-row {
        display: grid;
        grid-template-columns: 54px minmax(0, 1fr) auto;
        gap: 14px;
        align-items: center;
        padding: 16px 4px;
        border-bottom: 1px solid #edf0f4;
    }

    .abs-student-row:last-child {
        border-bottom: none;
    }

    .abs-student-number {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 13px;
        background: #f8fafc;
        color: #6b7280;
        font-size: 12px;
        font-weight: 900;
    }

    .abs-student-info strong {
        display: block;
        color: #111827;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .abs-student-info span {
        display: block;
        margin-top: 4px;
        color: #9ca3af;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .abs-radio-group {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .abs-radio-group label {
        cursor: pointer;
    }

    .abs-radio-group input {
        display: none;
    }

    .abs-radio-group span {
        min-width: 74px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #6b7280;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        transition: .18s ease;
    }

    .abs-radio-group input:checked + .hadir {
        background: #16a34a;
        border-color: #16a34a;
        color: #fff;
    }

    .abs-radio-group input:checked + .izin {
        background: #f59e0b;
        border-color: #f59e0b;
        color: #fff;
    }

    .abs-radio-group input:checked + .alpa {
        background: #dc2626;
        border-color: #dc2626;
        color: #fff;
    }

    .abs-submit-bar {
        margin-top: 20px;
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid #edf0f4;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
    }

    .abs-submit-bar strong {
        color: #111827;
        font-size: 24px;
        font-weight: 900;
        line-height: 1;
    }

    .abs-submit-bar span {
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        margin-left: 8px;
    }

    .abs-submit-bar button {
        border: none;
        height: 44px;
        border-radius: 13px;
        background: #d90429;
        color: #fff;
        padding: 0 18px;
        display: inline-flex;
        align-items: center;
        gap: 9px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        cursor: pointer;
        font-family: inherit;
    }

    .abs-empty {
        padding: 42px;
        text-align: center;
        background: #f8fafc;
        border-radius: 18px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .abs-empty i {
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

    .abs-empty strong {
        display: block;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    @media (max-width: 850px) {
        .abs-input-header,
        .abs-input-head,
        .abs-submit-bar {
            flex-direction: column;
            align-items: flex-start;
        }

        .abs-date-info {
            width: 100%;
            text-align: left;
        }

        .abs-student-row {
            grid-template-columns: 44px 1fr;
        }

        .abs-radio-group {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }
    }
</style>
@endsection