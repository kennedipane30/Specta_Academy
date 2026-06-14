@extends('layouts.spekta')

@section('title', 'Manajemen Materi')

@section('content')
<div class="cp-page">

    {{-- ── 1. HEADER MINIMALIS DENGAN KAPSUL BREADCRUMB ── --}}
    <section class="cp-header">
        <div class="cp-header-left">
            <span class="cp-breadcrumb-capsule">Weekly Material Manager</span>
            <h1>Kelola Materi: <span style="color: var(--spekta-teal);">{{ $subject_name }}</span></h1>
            <p>Kelas: {{ $class->program_name ?? 'N/A' }} • Pilih minggu, isi judul, dan unggah modul pembelajaran.</p>
        </div>
        <div class="cp-header-actions">
            <a href="{{ route('pengajar.materi.index') }}" class="cp-secondary-btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke List
            </a>
        </div>
    </section>

    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
        <div class="cp-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="cp-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(isset($serviceError) && $serviceError)
    <div class="cp-alert warning">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span>⚠️ Server materi sedang bermasalah. Data mungkin tidak dapat dimuat. Silakan coba lagi nanti.</span>
    </div>
@endif


    {{-- ── 2. FORM SECTION (PENGATURAN INPUT MATERIAL YANG RAPI) ── --}}
    <section class="cp-card">
        <div class="cp-card-head">
            <h2 id="form-title">Tambah atau Perbarui Materi</h2>
            <p>Pilih minggu keberapa, isi judul materi secara singkat, dan upload file PDF modul Anda.</p>
        </div>

        <form action="{{ route('pengajar.materi.store', $class->class_id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- material_name dikirim agar subject_name di Go tidak kosong -->
            <input type="hidden" name="material_name" value="{{ $subject_name }}">

            <div class="cp-form-grid">
                <!-- Minggu Ke -->
                <div class="cp-input-group">
                    <label>Minggu Ke</label>
                    <div class="cp-input-wrap">
                        <select name="week" id="input-week" required>
                            @for($i=1; $i<=20; $i++)
                                <option value="{{ $i }}">Minggu {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Judul Materi -->
                <div class="cp-input-group">
                    <label>Judul Materi</label>
                    <div class="cp-input-wrap">
                        <input id="input-title" type="text" name="title" placeholder="Contoh: Pengenalan Umum Aljabar" required>
                    </div>
                </div>

                <!-- Upload PDF -->
                <div class="cp-input-group">
                    <label>Upload PDF (Opsional saat ubah judul)</label>
                    <div class="cp-input-wrap file-wrap">
                        <input type="file" name="file_pdf" accept=".pdf">
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="cp-action-wrap">
                    <button type="submit" class="cp-btn-submit">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan
                    </button>
                </div>
            </div>
        </form>
    </section>

    {{-- ── 3. TABEL SECTION (DAFTAR FILE PDF TERUNGGAH) ── --}}
    <section class="cp-card">
        <div class="cp-card-head">
            <h2>Daftar Materi Terunggah</h2>
            <p>Daftar seluruh materi pembelajaran yang telah diunggah untuk mata pelajaran ini.</p>
        </div>

        <div class="table-responsive">
            <table class="cp-table">
                <thead>
                    <tr>
                        <th>Minggu</th>
                        <th>Judul Materi</th>
                        <th>Status File</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($materis as $item)
                    <tr>
                        {{-- Minggu --}}
                        <td>
                            <span class="badge-week">MG-{{ $item['week'] ?? 'N/A' }}</span>
                        </td>

                        {{-- Judul Materi --}}
                        <td>
                            <div class="materi-info">
                                <strong>{{ $item['title'] ?? 'Untitled' }}</strong>
                                <small>{{ $item['subject_name'] ?? $subject_name }}</small>
                            </div>
                        </td>

                        {{-- Status File (Pill Glowing Green) --}}
                        <td>
                            @if(!empty($item['file_path']))
                                <a target="_blank" href="{{ $item['file_path'] }}" class="badge-file">
                                    <i class="fa-solid fa-file-pdf"></i> PDF Tersedia
                                </a>
                            @else
                                <span class="badge-empty">Tanpa File</span>
                            @endif
                        </td>

                        {{-- Kolom Aksi Sejajar Tengah --}}
                        <td>
                            <div class="action-group">
                                @if(!empty($item['file_path']))
                                    <a href="{{ $item['file_path'] }}" download class="btn-icon blue" title="Download PDF">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                @endif

                                <button type="button" onclick="fillEditForm('{{ addslashes($item['title'] ?? '') }}', '{{ $item['week'] ?? '' }}')" class="btn-icon dark" title="Edit Materi">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <form method="POST" action="{{ route('pengajar.materi.destroy', $item['material_id'] ?? 0) }}" onsubmit="return confirm('Hapus materi ini? Data di aplikasi peserta didik juga akan terhapus.')" style="margin: 0; display: inline-flex;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon red" title="Hapus Materi">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="cp-empty-state">
                                    <div class="cp-empty-icon"><i class="fa-regular fa-folder-open"></i></div>
                                    <strong>Belum ada materi pembelajaran</strong>
                                    <span>Silakan unggah draf file PDF materi pertama Anda melalui form di atas.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

{{-- SCRIPT LOGIKA FILL EDIT FORM --}}
<script>
    function fillEditForm(title, week) {
        document.getElementById('input-title').value = title;
        document.getElementById('input-week').value = week;
        document.getElementById('form-title').innerText = "Edit Materi Minggu " + week;

        // Berikan visual feedback bahwa user sedang mengedit (Smooth Scroll)
        window.scrollTo({ top: 120, behavior: 'smooth' });
        document.getElementById('input-title').focus();
    }
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

    /* BASE LAYOUT */
    .cp-page {
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
    .cp-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
        border-bottom: 1px solid var(--border-soft);
        padding-bottom: 20px;
    }




    .cp-alert.warning {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
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
    .cp-header p {
        margin: 0;
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 600;
    }
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

    /* ALERTS */
    .cp-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 24px;
        font-size: 13px;
        font-weight: 800;
    }
    .cp-alert.success { background: #e6f7ed; color: #15803d; border: 1px solid #bbf7d0; }
    .cp-alert.error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

    /* CARDS */
    .cp-card {
        background: var(--spekta-white);
        padding: 20px;
        border-radius: 16px;
        margin-bottom: 24px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    }
    .cp-card-head {
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--spekta-gray-light);
    }
    .cp-card-head h2 {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
        margin: 0 0 6px 0;
    }
    .cp-card-head p {
        margin: 0;
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* FORM STYLES */
    .cp-form-grid {
        display: grid;
        grid-template-columns: 1fr 2fr 2.5fr auto;
        gap: 15px;
        align-items: flex-end;
    }
    .cp-input-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .cp-input-wrap input,
    .cp-input-wrap select {
        width: 100%;
        height: 40px;
        padding: 0 14px;
        border-radius: 10px;
        border: 1px solid var(--border-soft);
        background: var(--spekta-gray-light);
        font-size: 12px;
        font-weight: 600;
        color: var(--text-main);
        font-family: inherit;
        outline: none;
        transition: all 0.25s;
    }
    .cp-input-wrap input[type="file"] {
        padding-top: 10px;
    }
    .cp-input-wrap input:focus,
    .cp-input-wrap select:focus {
        background: var(--spekta-white);
        border-color: var(--spekta-teal);
        box-shadow: 0 0 0 3px rgba(46, 168, 171, 0.12);
    }
    .cp-action-wrap {
        display: flex;
        align-items: center;
        height: 40px;
    }
    .cp-btn-submit {
        height: 100%;
        padding: 0 20px;
        border: none;
        background: linear-gradient(135deg, var(--spekta-red) 0%, var(--spekta-red-dark) 100%);
        color: var(--spekta-white);
        font-size: 12px;
        font-weight: 800;
        border-radius: 10px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(229, 57, 53, 0.15);
    }
    .cp-btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(229, 57, 53, 0.25);
    }

    /* TABLE STYLES */
    .table-responsive { overflow-x: auto; }
    .cp-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }
    .cp-table th {
        padding: 12px 14px;
        font-size: 10px;
        color: var(--text-muted);
        text-transform: uppercase;
        text-align: left;
        border-bottom: 2px solid var(--spekta-gray-light);
        font-weight: 800;
        letter-spacing: 0.05em;
    }
    .cp-table th.text-end { text-align: right; }
    .cp-table td {
        padding: 14px;
        border-bottom: 1px solid var(--spekta-gray-light);
        vertical-align: middle;
    }
    .cp-table tr:last-child td { border-bottom: none; }
    .cp-table tr:hover { background: #fafbfc; }

    /* BADGES & TYPOGRAPHY */
    .badge-week {
        padding: 4px 10px;
        background: var(--spekta-gray-light);
        color: #475569;
        border-radius: 6px;
        font-weight: 800;
        font-size: 11px;
    }
    .badge-file {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        background: #e6f7ed;
        color: #15803d;
        border-radius: 6px;
        text-decoration: none;
        font-size: 11px;
        font-weight: 800;
        transition: all 0.2s ease;
    }
    .badge-file:hover { background: #bbf7d0; }
    .badge-empty {
        display: inline-block;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
        padding: 4px 0;
    }

    .materi-info strong {
        display: block;
        font-size: 13px;
        color: var(--text-main);
        margin-bottom: 4px;
    }
    .materi-info small {
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 600;
    }

    /* ACTIONS (TABLE) */
    .action-group {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }
    .btn-icon {
        width: 30px;
        height: 30px;
        border: none;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 12px;
    }
    .blue { background: #e0f2fe; color: #0369a1; }
    .blue:hover { background: #bae6fd; }

    .dark { background: var(--spekta-gray-light); color: #334155; }
    .dark:hover { background: var(--border-soft); }

    .red { background: var(--spekta-red-light); color: var(--spekta-red); }
    .red:hover { background: #fecaca; }

    /* EMPTY STATE */
    .cp-empty-state {
        text-align: center;
        padding: 40px;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .cp-empty-state i {
        font-size: 20px;
        color: var(--spekta-gray);
        margin-bottom: 4px;
    }

    /* RESPONSIVE */
    @media(max-width: 1024px){
        .cp-form-grid {
            grid-template-columns: 1fr 1fr;
            align-items: start;
        }
        .cp-action-wrap {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }
    }
    @media(max-width: 640px){
        .cp-card { padding: 15px; }
        .cp-form-grid { grid-template-columns: 1fr; }
        .cp-btn-submit { width: 100%; justify-content: center;}
    }
</style>
@endsection
