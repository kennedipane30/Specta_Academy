@extends('layouts.spekta')

@section('title', 'Edit Penugasan')
@section('subtitle', 'Sistem Manajemen Terpadu Spekta Academy')

@section('content')
<div class="am-page">
    {{-- HEADER --}}
    <section class="am-header">
        <div class="am-header-left">
            <span class="am-breadcrumb-capsule">Manajemen Kurikulum</span>
            <h1>Edit Penugasan Pengajar</h1>
            <p>Perbarui relasi pengajar untuk mata pelajaran yang ditugaskan.</p>
        </div>
    </section>

    {{-- FORM EDIT --}}
    <section class="am-grid-top">
        <div class="am-card am-form-card">
            <div class="am-card-header">
                <h2>Form Edit Penugasan</h2>
                <a href="{{ route('admin.assignments.index') }}" class="am-btn-back">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>

            @if(session('error'))
                <div class="am-alert error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.assignments.update', $assignment->id) }}" method="POST" class="am-form">
                @csrf
                @method('PUT')

                <div class="am-field">
                    <label>Pilih Pengajar</label>
                    <select name="teacher_id" required>
                        <option value="">-- Pilih Guru --</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->usersID }}"
                                {{ $teacher->usersID == $assignment->user_id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="am-field">
                    <label>Pilih Program</label>
                    <select name="class_id" id="class-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->class_id }}"
                                {{ $class->class_id == $assignment->class_id ? 'selected' : '' }}>
                                {{ $class->program_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="am-field">
                    <label>Pilih Mata Pelajaran</label>
                    <select name="subject_id" id="subject-select" required>
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->material_id }}"
                                data-name="{{ $subject->material_name }}"
                                {{ $subject->material_id == $assignment->subject_id ? 'selected' : '' }}>
                                {{ $subject->material_name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="subject_name" id="subject-name-hidden" value="{{ $assignment->subject_name }}">
                </div>

                <div class="am-form-actions">
                    <a href="{{ route('admin.assignments.index') }}" class="am-btn-secondary">
                        <i class="fa-solid fa-times"></i> Batal
                    </a>
                    <button type="submit" class="am-btn-submit">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- INFO CARD --}}
        <div class="am-card">
            <div class="am-card-header">
                <h2>Informasi Penugasan</h2>
            </div>
            <div class="am-info-list">
                <div class="am-info-item">
                    <span class="am-info-label">Pengajar Saat Ini</span>
                    <strong class="am-info-value">{{ $assignment->teacher->name ?? 'N/A' }}</strong>
                </div>
                <div class="am-info-item">
                    <span class="am-info-label">Program</span>
                    <strong class="am-info-value">{{ $assignment->classModel->program_name ?? 'N/A' }}</strong>
                </div>
                <div class="am-info-item">
                    <span class="am-info-label">Mata Pelajaran</span>
                    <strong class="am-info-value">{{ $assignment->subject_name ?? 'N/A' }}</strong>
                </div>
                <div class="am-info-item">
                    <span class="am-info-label">ID Penugasan</span>
                    <strong class="am-info-value">#{{ $assignment->id }}</strong>
                </div>
            </div>
            <div class="am-info-note">
                <i class="fa-solid fa-info-circle"></i>
                <span>Perubahan akan langsung diterapkan setelah disimpan.</span>
            </div>
        </div>
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Simpan nilai awal subject untuk fallback
    var initialSubjectId = $('#subject-select').val();
    var initialSubjectName = $('#subject-select option:selected').data('name') || '';
    var initialClassId = $('#class-select').val();

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

                    var hasMatch = false;
                    if(data.length > 0) {
                        $.each(data, function(key, value) {
                            var selected = '';
                            // Jika class_id masih sama dengan awal dan subject_id cocok
                            if (classId == initialClassId && value.material_id == initialSubjectId) {
                                selected = 'selected';
                                hasMatch = true;
                            }
                            subjectSelect.append('<option value="' + value.material_id + '" data-name="' + value.material_name + '" ' + selected + '>' + value.material_name + '</option>');
                        });

                        // Jika tidak ada yang match, gunakan yang pertama
                        if (!hasMatch && data.length > 0) {
                            var firstOption = subjectSelect.find('option:eq(1)');
                            firstOption.prop('selected', true);
                            subjectNameHidden.val(firstOption.data('name'));
                        }
                    } else {
                        subjectSelect.html('<option value="">Tidak ada mapel untuk kelas ini</option>');
                        subjectNameHidden.val('');
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

    // Trigger change untuk load data awal jika class sudah terpilih
    if ($('#class-select').val()) {
        $('#class-select').trigger('change');
    }
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

    .am-grid-top {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .am-card {
        background: var(--spekta-white);
        border-radius: 16px;
        padding: 20px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    }

    .am-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }

    .am-card-header h2 {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
    }

    .am-btn-back {
        color: var(--text-muted);
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        background: var(--spekta-gray-light);
        transition: 0.2s;
    }

    .am-btn-back:hover {
        background: #e5e7eb;
        color: var(--text-main);
    }

    .am-alert {
        display: flex;
        gap: 10px;
        align-items: center;
        padding: 12px 18px;
        border-radius: 12px;
        margin-bottom: 18px;
        font-weight: 800;
        font-size: 13px;
    }

    .am-alert.error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .am-form {
        display: grid;
        gap: 15px;
    }

    .am-field label {
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 6px;
        display: block;
        letter-spacing: 0.02em;
    }

    .am-field select {
        width: 100%;
        padding: 11px;
        border-radius: 10px;
        border: 1px solid var(--border-soft);
        background: var(--spekta-gray-light);
        font-weight: 600;
        font-family: inherit;
        font-size: 12px;
        outline: none;
        transition: all 0.25s;
    }

    .am-field select:focus {
        border-color: var(--spekta-teal);
        background: var(--spekta-white);
        box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12);
    }

    .am-field select:disabled {
        background: #f9fafb !important;
        border-color: var(--border-soft) !important;
        color: var(--text-muted) !important;
        cursor: not-allowed;
    }

    .am-form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 10px;
    }

    .am-btn-secondary {
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 800;
        cursor: pointer;
        font-family: inherit;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid var(--border-soft);
        background: var(--spekta-gray-light);
        color: var(--text-main);
        transition: 0.2s;
    }

    .am-btn-secondary:hover {
        background: #e5e7eb;
    }

    .am-btn-submit {
        background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%);
        color: var(--spekta-white);
        border: none;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: 0.2s;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
    }

    .am-btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(229, 57, 53, 0.2);
    }

    /* Info List */
    .am-info-list {
        display: grid;
        gap: 14px;
    }

    .am-info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid var(--spekta-gray-light);
    }

    .am-info-item:last-child {
        border-bottom: none;
    }

    .am-info-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .am-info-value {
        font-size: 14px;
        font-weight: 800;
        color: var(--text-main);
    }

    .am-info-note {
        margin-top: 16px;
        padding: 12px 16px;
        background: var(--spekta-teal-light);
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 12px;
        font-weight: 600;
        color: var(--spekta-teal);
    }

    .am-info-note i {
        font-size: 16px;
    }

    @media (max-width: 768px) {
        .am-grid-top {
            grid-template-columns: 1fr;
        }
        .am-form-actions {
            flex-direction: column;
        }
        .am-form-actions .am-btn-secondary,
        .am-form-actions .am-btn-submit {
            justify-content: center;
        }
        .am-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection
