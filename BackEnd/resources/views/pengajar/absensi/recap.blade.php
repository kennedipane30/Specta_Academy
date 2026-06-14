@extends('layouts.spekta')

@section('title', 'Rekap Absensi')

@section('content')
@php
    $totalData = $data->count();
    $hadir = $data->where('status', 'h')->count();
    $izin = $data->where('status', 'i')->count();
    $alpa = $data->where('status', 'a')->count();
@endphp

<div class="recap-container">

    {{-- ── 1. HEADER SECTION ── --}}
    <div class="recap-header">
        <div class="recap-header-left">
            <div class="breadcrumb">
                <a href="{{ route('pengajar.absensi.weeks', [$class->class_id, $subject]) }}" class="back-link">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
                <span class="badge-capsule">ATTENDANCE RECAP</span>
            </div>
            <h1 class="recap-title">Rekap Absensi</h1>
            <p class="recap-subtitle">{{ $class->program_name }} • {{ $subject }} • Minggu {{ $week }}</p>
        </div>
    </div>

    {{-- ── 2. STATISTIK CARD ── --}}
    <div class="stats-wrapper">
        <div class="stat-item stat-hadir">
            <div class="stat-icon">📊</div>
            <div class="stat-info">
                <span class="stat-label">HADIR</span>
                <strong class="stat-value">{{ $hadir }}</strong>
            </div>
        </div>
        <div class="stat-item stat-izin">
            <div class="stat-icon">📝</div>
            <div class="stat-info">
                <span class="stat-label">IZIN</span>
                <strong class="stat-value">{{ $izin }}</strong>
            </div>
        </div>
        <div class="stat-item stat-alpa">
            <div class="stat-icon">⚠️</div>
            <div class="stat-info">
                <span class="stat-label">ALPA</span>
                <strong class="stat-value">{{ $alpa }}</strong>
            </div>
        </div>
        <div class="stat-item stat-total">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <span class="stat-label">TOTAL</span>
                <strong class="stat-value">{{ $totalData }}</strong>
            </div>
        </div>
    </div>

    {{-- ── 3. TABLE CARD ── --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="card-title">
                <i class="fa-solid fa-clipboard-list"></i>
                <h3>Daftar Kehadiran Siswa</h3>
            </div>
            <div class="card-badge">
                <i class="fa-solid fa-database"></i> {{ $totalData }} Siswa
            </div>
        </div>

        <div class="table-responsive">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th class="col-no">NO</th>
                        <th class="col-student">NAMA SISWA</th>
                        <th class="col-status">STATUS KEHADIRAN</th>
                        <th class="col-action">TANGGAL INPUT & AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $row)
                    <tr class="student-row">
                        <td class="no-cell">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="student-cell">
                            <div class="student-avatar">
                                {{ strtoupper(substr($row->user->name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="student-detail">
                                <strong class="student-name">{{ $row->user->name ?? 'N/A' }}</strong>
                                <span class="student-role">Siswa Aktif</span>
                            </div>
                        </td>
                        <td class="status-cell">
                            @if($row->status == 'h')
                                <span class="status-badge hadir">
                                    <i class="fa-solid fa-check-circle"></i> HADIR
                                </span>
                            @elseif($row->status == 'i')
                                <span class="status-badge izin">
                                    <i class="fa-solid fa-clock"></i> IZIN
                                </span>
                            @else
                                <span class="status-badge alpa">
                                    <i class="fa-solid fa-times-circle"></i> ALPA
                                </span>
                            @endif
                        </td>
                        <td class="action-cell">
                            <div class="action-wrapper">
                                <span class="date-input">
                                    <i class="fa-regular fa-calendar"></i>
                                    {{ $row->date ? date('d M Y', strtotime($row->date)) : '-' }}
                                </span>
                                <div class="action-buttons-group">
                                    <a href="{{ route('pengajar.absensi.edit', [$class->class_id, $subject, $week]) }}"
                                       class="action-icon edit-icon" title="Edit Absensi">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <a href="{{ route('pengajar.absensi.export-pdf', [$class->class_id, $subject, $week]) }}"
                                       class="action-icon export-icon" title="Export PDF" target="_blank">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <button type="button" class="action-icon delete-icon" title="Hapus Absensi" onclick="confirmDelete({{ $week }})">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="empty-row">
                            <div class="empty-state">
                                <i class="fa-regular fa-folder-open"></i>
                                <strong>Belum Ada Data Absensi</strong>
                                <span>Silakan isi absensi terlebih dahulu</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Delete --}}
<div id="deleteModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fa-solid fa-trash-can modal-icon"></i>
                <h3>Hapus Absensi</h3>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menghapus seluruh data absensi untuk <strong>Minggu ke-<span id="weekNumber"></span></strong>?</p>
                <p class="warning-text">⚠️ Data yang dihapus tidak dapat dikembalikan!</p>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal()">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete-confirm">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let deleteWeek = null;

    function confirmDelete(week) {
        deleteWeek = week;
        const modal = document.getElementById('deleteModal');
        const weekSpan = document.getElementById('weekNumber');
        weekSpan.textContent = week;

        const form = document.getElementById('deleteForm');
        const url = "{{ route('pengajar.absensi.destroy', [$class->class_id, $subject, ':week']) }}";
        form.action = url.replace(':week', week);

        modal.style.display = 'flex';
    }

    function closeModal() {
        const modal = document.getElementById('deleteModal');
        modal.style.display = 'none';
        deleteWeek = null;
    }

    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }

    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
        showToast('{{ session('error') }}', 'error');
    @endif

    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'}"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.remove()"><i class="fa-solid fa-xmark"></i></button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }
</script>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .recap-container {
        padding: 24px 32px;
        background: #f5f7fa;
        min-height: 100vh;
        font-family: 'Inter', 'Montserrat', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Header Styles */
    .recap-header {
        margin-bottom: 32px;
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #64748b;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
    }

    .back-link:hover {
        background: #f1f5f9;
        color: #1e293b;
        border-color: #cbd5e1;
    }

    .badge-capsule {
        background: #fee2e2;
        color: #dc2626;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .recap-title {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
        letter-spacing: -0.3px;
    }

    .recap-subtitle {
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }

    /* Stats Wrapper */
    .stats-wrapper {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-item {
        background: white;
        padding: 16px 20px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        gap: 14px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }

    .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    .stat-icon {
        font-size: 28px;
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        display: block;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 800;
        color: #1e293b;
    }

    .stat-hadir .stat-value { color: #10b981; }
    .stat-izin .stat-value { color: #f59e0b; }
    .stat-alpa .stat-value { color: #ef4444; }
    .stat-total .stat-value { color: #3b82f6; }

    /* Table Card */
    .table-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .table-card-header {
        padding: 18px 24px;
        background: white;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .card-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title i {
        font-size: 18px;
        color: #2ea8ab;
    }

    .card-title h3 {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
    }

    .card-badge {
        padding: 4px 12px;
        background: #f8fafc;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
    }

    .card-badge i {
        margin-right: 4px;
    }

    /* Table Styles */
    .table-responsive {
        overflow-x: auto;
    }

    .attendance-table {
        width: 100%;
        border-collapse: collapse;
    }

    .attendance-table thead {
        background: #f8fafc;
    }

    .attendance-table th {
        padding: 14px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        border-bottom: 1px solid #e2e8f0;
    }

    .attendance-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .student-row:hover {
        background: #fafcff;
    }

    .col-no {
        width: 70px;
    }

    .col-student {
        width: 35%;
    }

    .col-status {
        width: 20%;
    }

    .col-action {
        width: 40%;
    }

    .no-cell {
        font-weight: 700;
        color: #94a3b8;
        font-size: 13px;
    }

    /* Student Cell */
    .student-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .student-avatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: linear-gradient(135deg, #e6f7f5 0%, #d1f0ee 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
        color: #2ea8ab;
    }

    .student-detail {
        flex: 1;
    }

    .student-name {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .student-role {
        font-size: 10px;
        font-weight: 600;
        color: #94a3b8;
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 10px;
        display: inline-block;
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    .status-badge.hadir {
        background: #d1fae5;
        color: #059669;
    }

    .status-badge.izin {
        background: #fed7aa;
        color: #ea580c;
    }

    .status-badge.alpa {
        background: #fee2e2;
        color: #dc2626;
    }

    /* Action Cell - Kunci utama: semua dalam satu baris */
    .action-cell {
        padding: 16px 20px !important;
    }

    .action-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .date-input {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        background: #f8fafc;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
        color: #475569;
    }

    .date-input i {
        color: #94a3b8;
        font-size: 11px;
    }

    .action-buttons-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .action-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        background: transparent;
        font-size: 14px;
    }

    .edit-icon {
        background: #dbeafe;
        color: #2563eb;
    }

    .edit-icon:hover {
        background: #3b82f6;
        color: white;
        transform: translateY(-2px);
    }

    .export-icon {
        background: #d1fae5;
        color: #059669;
    }

    .export-icon:hover {
        background: #10b981;
        color: white;
        transform: translateY(-2px);
    }

    .delete-icon {
        background: #fee2e2;
        color: #dc2626;
    }

    .delete-icon:hover {
        background: #ef4444;
        color: white;
        transform: translateY(-2px);
    }

    /* Empty State */
    .empty-row td {
        padding: 60px 20px !important;
        text-align: center;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }

    .empty-state i {
        font-size: 48px;
        color: #cbd5e1;
    }

    .empty-state strong {
        font-size: 15px;
        color: #475569;
    }

    .empty-state span {
        font-size: 12px;
        color: #94a3b8;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        align-items: center;
        justify-content: center;
        z-index: 1000;
        backdrop-filter: blur(4px);
    }

    .modal-dialog {
        max-width: 440px;
        width: 90%;
        animation: modalFadeIn 0.2s ease;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-content {
        background: white;
        border-radius: 20px;
        overflow: hidden;
    }

    .modal-header {
        padding: 20px 24px 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .modal-icon {
        font-size: 24px;
        color: #dc2626;
    }

    .modal-header h3 {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
    }

    .modal-body {
        padding: 20px 24px;
    }

    .modal-body p {
        color: #475569;
        line-height: 1.5;
        margin-bottom: 8px;
    }

    .warning-text {
        font-size: 12px;
        color: #dc2626 !important;
        font-weight: 600;
    }

    .modal-footer {
        padding: 16px 24px 24px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-cancel {
        padding: 8px 20px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cancel:hover {
        background: #f1f5f9;
    }

    .btn-delete-confirm {
        padding: 8px 20px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 600;
        border: none;
        background: #dc2626;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-delete-confirm:hover {
        background: #b91c1c;
    }

    /* Toast Notification */
    .toast {
        position: fixed;
        top: 24px;
        right: 24px;
        padding: 12px 20px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 1100;
        animation: slideIn 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .toast-success {
        background: #10b981;
        color: white;
    }

    .toast-error {
        background: #ef4444;
        color: white;
    }

    .toast button {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        font-size: 14px;
        margin-left: 8px;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .recap-container {
            padding: 16px;
        }

        .stats-wrapper {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .recap-title {
            font-size: 22px;
        }

        .action-wrapper {
            flex-direction: column;
            align-items: flex-start;
        }

        .action-buttons-group {
            margin-top: 8px;
        }
    }

    @media (max-width: 640px) {
        .table-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .col-status, .col-date {
            min-width: 120px;
        }
    }
</style>
@endsection
