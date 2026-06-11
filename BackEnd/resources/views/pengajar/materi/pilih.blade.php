@extends('layouts.spekta')

@section('title', 'Manajemen Materi')

@section('content')
<div class="cp-page">

    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
        <div class="cp-alert success">
            <i class="fa-solid fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="cp-alert error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- FORM SECTION --}}
    <section class="cp-card">
        <div class="cp-card-head">
            <h2 id="form-title">Tambah atau Perbarui Materi</h2>
            <p>Pilih minggu, isi judul, dan upload PDF. Jika minggu sudah ada, sistem akan otomatis memperbarui data lama.</p>
        </div>

        <form action="{{ route('pengajar.materi.store', $class->class_id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- 🔥 PENTING: material_name dikirim agar subject_name di Go tidak kosong -->
            <input type="hidden" name="material_name" value="{{ $subject_name }}">

            <div class="cp-form-grid">
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

                <div class="cp-input-group">
                    <label>Judul Materi</label>
                    <div class="cp-input-wrap">
                        <input id="input-title" type="text" name="title" placeholder="Contoh: Pengenalan Umum" required>
                    </div>
                </div>

                <div class="cp-input-group">
                    <label>Upload PDF (Opsional saat ubah judul)</label>
                    <div class="cp-input-wrap file-wrap">
                        <input type="file" name="file_pdf" accept=".pdf">
                    </div>
                </div>

                <div class="cp-action-wrap">
                    <button type="submit" class="cp-btn-submit">
                        <i class="fa-solid fa-save"></i> Simpan
                    </button>
                </div>
            </div>
        </form>
    </section>

    {{-- TABEL SECTION --}}
    <section class="cp-card">
        <div class="cp-card-head">
            <h2>Daftar Materi</h2>
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
                        <td>
                            <span class="badge-week">MG-{{ $item['week'] ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <div class="materi-info">
                                <strong>{{ $item['title'] ?? 'Untitled' }}</strong>
                                <small>{{ $item['subject_name'] ?? $subject_name }}</small>
                            </div>
                        </td>
                        <td>
                            @if(!empty($item['file_path']))
                                <a target="_blank" href="{{ $item['file_path'] }}" class="badge-file">
                                    <i class="fa-solid fa-file-pdf"></i> PDF Tersedia
                                </a>
                            @else
                                <span class="badge-empty">Tanpa File</span>
                            @endif
                        </td>
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

                                <form method="POST" action="{{ route('pengajar.materi.destroy', $item['material_id'] ?? 0) }}" onsubmit="return confirm('Hapus materi ini? Data di aplikasi peserta didik juga akan terhapus.')" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon red" title="Hapus Materi">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="cp-empty-state">
                                    <i class="fa-regular fa-folder-open"></i>
                                    <strong>Belum ada materi.</strong>
                                    <span>Silakan unggah materi pertama melalui form di atas.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<script>
    function fillEditForm(title, week) {
        document.getElementById('input-title').value = title;
        document.getElementById('input-week').value = week;
        document.getElementById('form-title').innerText = "Edit Materi Minggu " + week;

        // Berikan visual feedback bahwa user sedang mengedit
        window.scrollTo({ top: 0, behavior: 'smooth' });
        document.getElementById('input-title').focus();
    }
</script>

<style>
    /* BASE LAYOUT */
    .cp-page {
        padding: 24px 0;
        font-family: 'Inter', system-ui, sans-serif;
        color: #334155;
    }

    /* ALERTS */
    .cp-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        font-size: 14px;
        font-weight: 700;
    }
    .cp-alert.success {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .cp-alert.error {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* CARDS */
    .cp-card {
        background: #fff;
        padding: 32px;
        border-radius: 16px;
        margin-bottom: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
    }
    .cp-card-head {
        margin-bottom: 28px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .cp-card-head h2 {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px 0;
    }
    .cp-card-head p {
        margin: 0;
        font-size: 13px;
        color: #64748b;
    }

    /* FORM STYLES */
    .cp-form-grid {
        display: grid;
        grid-template-columns: 1fr 2fr 2fr auto;
        gap: 20px;
        align-items: flex-end;
    }
    .cp-input-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
    }
    .cp-input-wrap input,
    .cp-input-wrap select {
        width: 100%;
        height: 44px;
        padding: 0 16px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        font-size: 14px;
        color: #1e293b;
        font-family: inherit;
        outline: none;
        transition: all 0.2s;
    }
    .cp-input-wrap input[type="file"] {
        padding-top: 10px;
    }
    .cp-input-wrap input:focus,
    .cp-input-wrap select:focus {
        background: #fff;
        border-color: #d90429;
        box-shadow: 0 0 0 3px rgba(217, 4, 41, 0.1);
    }
    .cp-action-wrap {
        display: flex;
        align-items: center;
        height: 44px;
    }
    .cp-btn-submit {
        height: 100%;
        padding: 0 24px;
        border: none;
        background: #d90429;
        color: white;
        font-size: 13px;
        font-weight: 800;
        border-radius: 10px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
        box-shadow: 0 4px 12px rgba(217, 4, 41, 0.2);
    }
    .cp-btn-submit:hover {
        background: #b80222;
        transform: translateY(-1px);
    }

    /* TABLE STYLES */
    .table-responsive { overflow-x: auto; }
    .cp-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }
    .cp-table th {
        padding: 16px;
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        text-align: left;
        border-bottom: 2px solid #f1f5f9;
        background: #f8fafc;
        font-weight: 800;
    }
    .cp-table th.text-end { text-align: right; }
    .cp-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .cp-table tr:last-child td { border-bottom: none; }
    .cp-table tr:hover { background: #f8fafc; }

    /* BADGES & TYPOGRAPHY */
    .badge-week {
        padding: 6px 12px;
        background: #f1f5f9;
        color: #475569;
        border-radius: 8px;
        font-weight: 800;
        font-size: 11px;
    }
    .badge-file {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #ecfdf5;
        color: #059669;
        border-radius: 8px;
        text-decoration: none;
        font-size: 11px;
        font-weight: 800;
        transition: background 0.2s;
    }
    .badge-file:hover { background: #d1fae5; }
    .badge-empty {
        display: inline-block;
        color: #94a3b8;
        font-size: 12px;
        font-weight: 600;
        padding: 6px 0;
    }

    .materi-info strong {
        display: block;
        font-size: 14px;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .materi-info small {
        color: #64748b;
        font-size: 12px;
    }

    /* ACTIONS (TABLE) */
    .action-group {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }
    .btn-icon {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }
    .blue { background: #eff6ff; color: #2563eb; }
    .blue:hover { background: #dbeafe; }

    .dark { background: #f1f5f9; color: #334155; }
    .dark:hover { background: #e2e8f0; color: #0f172a; }

    .red { background: #fef2f2; color: #dc2626; }
    .red:hover { background: #fecaca; }

    /* EMPTY STATE */
    .cp-empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .cp-empty-state i {
        font-size: 40px;
        color: #cbd5e1;
        margin-bottom: 16px;
        display: block;
    }
    .cp-empty-state strong {
        display: block;
        font-size: 16px;
        color: #1e293b;
        margin-bottom: 4px;
    }
    .cp-empty-state span {
        font-size: 14px;
        color: #64748b;
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
        .cp-card { padding: 20px; }
        .cp-form-grid { grid-template-columns: 1fr; }
        .cp-btn-submit { width: 100%; justify-content: center;}
    }
</style>
@endsection
