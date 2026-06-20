@extends('layouts.spekta')

@section('title', 'Matrix Penugasan')

@section('content')
@php
    $assignmentCollection = collect($assignments);
    // Ambil nama materi unik untuk header Matrix
    $uniqueSubjectNames = collect($subjects)->pluck('material_name')->unique()->sort();
    $classCollection = collect($classes);

    $totalAssignments = $assignmentCollection->count();

    // Hitung total slot (Jumlah Kelas x Jumlah Nama Materi Unik)
    $totalSlots = max($classCollection->count() * $uniqueSubjectNames->count(), 1);

    // Hitung cakupan per nama mapel
    $subjectCoverage = $uniqueSubjectNames->map(function ($name) use ($assignmentCollection, $subjects) {
        // Cari ID material yang memiliki nama ini
        $ids = collect($subjects)->where('material_name', $name)->pluck('material_id');
        return [
            'name' => $name,
            'total' => $assignmentCollection->whereIn('subject_id', $ids)->count(),
        ];
    })->sortByDesc('total')->values();
@endphp

<div class="am-page">

    {{-- 1. HEADER (STYLISH CAPSULE & SHARP TITLE) --}}
    <section class="am-header">
        <div class="am-header-left">
            <span class="am-breadcrumb-capsule">Manajemen Kurikulum</span>
            <h1>Penugasan Pengajar</h1>
            <p>Atur relasi pengajar untuk setiap mata pelajaran di database lokal.</p>
        </div>
    </section>

    {{-- 2. ACTION STRIP (KARTU METRIK MODERN & SEIMBANG) --}}
    <section class="am-strip">
        <div class="am-strip-card card-teal">
            <span>TOTAL PENUGASAN</span>
            <strong>{{ $totalAssignments }}</strong>
        </div>
        <div class="am-strip-card card-blue">
            <span>SLOT TERSEDIA</span>
            <strong>{{ $totalSlots }}</strong>
        </div>
        <div class="am-strip-card card-red">
            <span>SLOT KOSONG</span>
            <strong>{{ max($totalSlots - $totalAssignments, 0) }}</strong>
        </div>
    </section>

    {{-- 3. MAIN GRID (FORM & METRIK CAKUPAN MAPEL) --}}
    <section class="am-grid-top">
        <div class="am-card am-form-card">
            <div class="am-card-header">
                <h2>Tambah Penugasan Baru</h2>
            </div>
            <form action="{{ route('admin.assignments.store') }}" method="POST" class="am-form">
                @csrf
                <div class="am-field">
                    <label>Pilih Pengajar</label>
                    <select name="teacher_id" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->usersID }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="am-field">
                    <label>Pilih Program</label>
                    <select name="class_id" id="class-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->class_id }}">{{ $class->program_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="am-field">
                    <label>Pilih Mata Pelajaran</label>
                    <select name="subject_id" id="subject-select" required disabled class="am-select-disabled">
                        <option value="">-- Pilih Kelas Terlebih Dahulu --</option>
                    </select>
                    {{-- hidden input untuk subject_name --}}
                    <input type="hidden" name="subject_name" id="subject-name-hidden">
                </div>
                <button type="submit" class="am-btn-submit">
                    <i class="fa-solid fa-cloud-arrow-up"></i> Simpan Penugasan
                </button>
            </form>
        </div>

        <!-- Panel Cakupan Mapel -->
        <div class="am-card">
            <div class="am-card-header">
                <h2>Cakupan Mapel</h2>
            </div>
            <div class="am-coverage-list">
                @forelse($subjectCoverage as $sc)
                    <div class="am-progress-row">
                        <div class="am-progress-label">
                            <span>{{ $sc['name'] }}</span>
                            <strong>{{ $sc['total'] }}</strong>
                        </div>
                        <div class="am-progress-bg">
                            <em style="width: {{ $totalAssignments > 0 ? ($sc['total'] / $totalAssignments) * 100 : 0 }}%"></em>
                        </div>
                    </div>
                @empty
                    <div class="am-empty-coverage">
                        <i class="fa-solid fa-book-bookmark"></i>
                        <span>Belum ada cakupan mapel terdaftar</span>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- 4. MATRIX TABLE VIEW --}}
    <section class="am-card am-matrix-card">
        <div class="am-card-header">
            <div>
                <h2>Peta Penugasan (Matrix View)</h2>
                <p class="am-matrix-subtitle">Data dikelola berdasarkan tabel <strong>Materials</strong></p>
            </div>
        </div>
        <div class="am-table-scroll">
            <table class="am-matrix-table">
                <thead>
                    <tr>
                        <th class="sticky-col">PROGRAM KELAS</th>
                        @foreach($uniqueSubjectNames as $name)
                            <th>{{ $name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $class)
                        <tr>
                            <td class="sticky-col"><strong>{{ $class->program_name }}</strong></td>
                            @foreach($uniqueSubjectNames as $name)
                                @php
                                    // Cari apakah kelas ini punya material dengan nama ini
                                    $targetMaterial = collect($subjects)->where('class_id', $class->class_id)->where('material_name', $name)->first();

                                    $assigned = null;
                                    if($targetMaterial) {
                                        $assigned = $assignmentCollection->where('class_id', $class->class_id)
                                                                         ->where('subject_id', $targetMaterial->material_id)
                                                                         ->first();
                                    }
                                @endphp
                                <td>
                                    @if($targetMaterial)
                                        @if($assigned)
                                            <div class="am-slot filled">
                                                <i class="fa-solid fa-circle-check"></i>
                                                <span>{{ $assigned->teacher->name ?? 'Guru' }}</span>
                                            </div>
                                        @else
                                            <div class="am-slot empty">Kosong</div>
                                        @endif
                                    @else
                                        <div class="am-slot locked" title="Tidak ada di kurikulum"><i class="fa-solid fa-lock"></i></div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    {{-- 5. DAFTAR PENUGASAN AKTIF (RECORD LIST) --}}
    <section class="am-card">
        <div class="am-card-header">
            <h2>Daftar Penugasan Aktif</h2>
        </div>
        <div class="am-table-list-wrap">
            <table class="am-list-table">
                <thead>
                    <tr>
                        <th>Pengajar</th>
                        <th>Program</th>
                        <th>Mata Pelajaran</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assign)
                        <tr>
                            <td>
                                <div class="am-t-cell">
                                    <div class="am-avatar">{{ strtoupper(substr($assign->teacher->name ?? 'P', 0, 1)) }}</div>
                                    <strong>{{ $assign->teacher->name ?? 'N/A' }}</strong>
                                </div>
                            </td>
                            <td>{{ $assign->classModel->program_name ?? 'N/A' }}</td>
                            <td>
                                {{-- LANGSUNG PAKAI subject_name dari database --}}
                                <span class="badge-subject">{{ $assign->subject_name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <td>
                                    <div class="am-actions-wrap">
                                        <a href="{{ route('admin.assignments.edit', $assign->id) }}" class="am-btn-edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('admin.assignments.destroy', $assign->id) }}" method="POST" style="display: inline-flex;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="am-btn-del" onclick="return confirm('Hapus penugasan ini?')">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="am-empty-state">
                                    <i class="fa-solid fa-user-slash"></i>
                                    <span>Belum ada data penugasan yang aktif.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#class-select').on('change', function() {
        var classId = $(this).val();
        var subjectSelect = $('#subject-select');
        var subjectNameHidden = $('#subject-name-hidden');

        if (classId) {
            subjectSelect.prop('disabled', true).html('<option>Memuat Mapel...</option>');
            $.ajax({
                url: "{{ url('/admin/get-subjects-by-class') }}/" + classId,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    subjectSelect.prop('disabled', false).empty();
                    subjectSelect.append('<option value="">-- Pilih Mapel --</option>');
                    if(data.length > 0) {
                        $.each(data, function(key, value) {
                            // Simpan juga nama mapel ke dalam data attribute
                            subjectSelect.append('<option value="' + value.material_id + '" data-name="' + value.material_name + '">' + value.material_name + '</option>');
                        });
                    } else {
                        subjectSelect.html('<option value="">Tidak ada mapel untuk kelas ini</option>');
                    }
                }
            });
        } else {
            subjectSelect.prop('disabled', true).html('<option value="">-- Pilih Kelas Terlebih Dahulu --</option>');
            subjectNameHidden.val('');
        }
    });

    // Ketika user memilih mata pelajaran, simpan nama mapel ke hidden input
    $(document).on('change', '#subject-select', function() {
        var selectedOption = $(this).find('option:selected');
        var subjectName = selectedOption.data('name');
        $('#subject-name-hidden').val(subjectName);
    });
});
</script>

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

        .am-actions-wrap {
        display: flex;
        align-items: center;
        gap: 6px;
        justify-content: center;
    }

    .am-btn-edit {
        background: rgba(46, 168, 171, 0.08);
        border: none;
        color: var(--spekta-teal);
        width: 30px;
        height: 30px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .am-btn-edit:hover {
        background: #a7f3d0;
        color: #065f46;
        transform: scale(1.05);
    }

    .am-page {
        font-family: 'Montserrat', sans-serif;
        padding: 10px;
        color: var(--text-main);
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── Header Minimalis Modern ── */
    .am-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }

    .am-breadcrumb-capsule {
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

    .am-header h1 {
        margin: 0 0 6px;
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
        color: var(--text-main);
    }

    .am-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }

    /* ── Stats Strip ── */
    .am-strip {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .am-strip-card {
        background: var(--spekta-white);
        padding: 16px 20px;
        border-radius: 14px;
        border: 1px solid var(--border-soft);
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.01);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .am-strip-card::before {
        content: "";
        position: absolute;
        left: 0; right: 0; top: 0;
        height: 4px;
        border-radius: 14px 14px 0 0;
    }
    .card-teal::before { background: var(--spekta-teal); }
    .card-blue::before { background: #3b82f6; }
    .card-red::before { background: var(--spekta-red); }

    .am-strip-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
    }
    .am-strip-card span { font-size: 9px; font-weight: 800; color: var(--text-muted); display: block; margin-bottom: 4px; letter-spacing: 0.04em; }
    .am-strip-card strong { font-size: 26px; font-weight: 900; color: var(--text-main); }

    /* Form & Coverage */
    .am-grid-top { display: grid; grid-template-columns: 1.5fr 1fr; gap: 24px; margin-bottom: 24px; }
    .am-card { background: var(--spekta-white); border-radius: 16px; padding: 20px; border: 1px solid var(--border-soft); box-shadow: 0 4px 15px rgba(0,0,0,0.01); }
    .am-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; }
    .am-card-header h2 { font-size: 15px; font-weight: 800; color: var(--text-main); margin: 0; }

    .am-form { display: grid; gap: 15px; }
    .am-field label { font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px; display: block; letter-spacing: 0.02em; }
    .am-field select { width: 100%; padding: 11px; border-radius: 10px; border: 1px solid var(--border-soft); background: var(--spekta-gray-light); font-weight: 600; font-family: inherit; font-size: 12px; outline: none; transition: all 0.25s; }
    .am-field select:focus { border-color: var(--spekta-teal); background: var(--spekta-white); box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12); }

    /* Disabled select styling */
    .am-select-disabled {
        background: #f9fafb !important;
        border-color: var(--border-soft) !important;
        color: var(--text-muted) !important;
        cursor: not-allowed;
    }

    .am-btn-submit { background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%); color: var(--spekta-white); border: none; padding: 12px 18px; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.2s; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; justify-content: center; }
    .am-btn-submit:hover { transform: translateY(-1px); box-shadow: 0 4px 15px rgba(229, 57, 53, 0.2); }

    /* Matrix View Table */
    .am-matrix-subtitle { font-size: 11px; color: var(--text-muted); margin: 4px 0 0 0; font-weight: 600; }
    .am-table-scroll { overflow-x: auto; border: 1px solid var(--border-soft); border-radius: 12px; }
    .am-matrix-table { width: 100%; border-collapse: collapse; min-width: 1000px; }
    .am-matrix-table th { background: #f9fafb; padding: 12px 14px; text-align: left; font-size: 10px; color: var(--text-muted); text-transform: uppercase; font-weight: 800; border-bottom: 1px solid var(--border-soft); }
    .am-matrix-table td { padding: 10px; border-bottom: 1px solid var(--spekta-gray-light); vertical-align: middle; }
    .sticky-col { position: sticky; left: 0; background: var(--spekta-white) !important; z-index: 10; border-right: 2px solid var(--spekta-gray-light); min-width: 180px; }

    /* Slot Badges */
    .am-slot { padding: 6px 10px; border-radius: 8px; font-size: 11px; font-weight: 800; display: flex; align-items: center; gap: 6px; justify-content: center; min-height: 36px; transition: all 0.2s; }
    .am-slot.filled { background: var(--spekta-teal-light); color: var(--spekta-teal); border: 1px solid rgba(46, 168, 171, 0.12); }
    .am-slot.filled i { font-size: 12px; }
    .am-slot.empty { background: var(--spekta-red-light); color: var(--spekta-red); border: 1px dashed rgba(229, 57, 53, 0.12); }
    .am-slot.locked { background: var(--spekta-gray-light); color: var(--spekta-gray); opacity: 0.6; }

    /* Record List Table */
    .am-table-list-wrap { overflow-x: auto; }
    .am-list-table { width: 100%; border-collapse: collapse; }
    .am-list-table th { text-align: left; padding: 12px; font-size: 10px; color: var(--text-muted); text-transform: uppercase; border-bottom: 2px solid var(--spekta-gray-light); font-weight: 800; }
    .am-list-table td { padding: 12px; border-bottom: 1px solid var(--spekta-gray-light); vertical-align: middle; }
    .am-list-table tbody tr:last-child td { border-bottom: none; }
    .am-list-table tbody tr:hover { background: #fafbfc; }

    .am-t-cell { display: flex; align-items: center; gap: 10px; }
    .am-avatar { width: 32px; height: 32px; background: var(--spekta-teal-light); color: var(--spekta-teal); border-radius: 50%; display: grid; place-items: center; font-weight: 900; font-size: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.03); }
    .badge-subject { background: #e0f2fe; color: #0269a1; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; }

    .am-actions-wrap {
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .am-btn-del {
        background: var(--spekta-red-light);
        border: none;
        color: var(--spekta-red);
        width: 30px;
        height: 30px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .am-btn-del:hover { background: #fecaca; color: #991b1b; transform: scale(1.05); }

    /* Progress bar */
    .am-progress-row { margin-bottom: 12px; }
    .am-progress-row:last-child { margin-bottom: 0; }
    .am-progress-label { display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 4px; }
    .am-progress-label span { font-weight: 800; color: var(--text-main); }
    .am-progress-label strong { font-weight: 800; color: var(--spekta-teal); }
    .am-progress-bg { height: 6px; background: var(--spekta-gray-light); border-radius: 10px; overflow: hidden; }
    .am-progress-bg em { display: block; height: 100%; background: var(--spekta-teal); border-radius: 10px; box-shadow: 0 0 6px rgba(46, 168, 171, 0.2); }

    .am-empty-state, .am-empty-coverage {
        padding: 20px;
        text-align: center;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }
    .am-empty-state i, .am-empty-coverage i {
        font-size: 18px;
        color: var(--spekta-gray);
    }
    .text-center { text-align: center; }
</style>
@endsection
