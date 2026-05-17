@extends('layouts.spekta')

@section('title', 'Schedule Management')
@section('subtitle', 'Sistem Manajemen Terpadu Spekta Academy')

@section('content')
<div class="sc-page">

    <section class="sc-header">
        <div>
            <span>Manajemen Akademik</span>
            <h1>Kelola Jadwal Pembelajaran</h1>
            <p>Buat dan kelola jadwal kelas dengan mudah dan terstruktur.</p>
        </div>
    </section>

    @if(session('success'))
        <div class="sc-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="sc-alert error">
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

    <section class="sc-top-grid">

        <div class="sc-panel sc-form-panel">
            <div class="sc-panel-heading">
                <div class="sc-heading-icon">
                    <i class="fa-solid fa-sliders"></i>
                </div>
                <h2>Buat Jadwal Baru</h2>
            </div>

            <form action="{{ route('admin.jadwal.store') }}" method="POST" class="sc-form">
                @csrf

                <div class="sc-input-group">
                    <label>Select Program</label>
                    <div>
                        <select name="class_id" id="classSelect" required>
                            <option value="">Pilih Program</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->class_id }}" {{ old('class_id') == $class->class_id ? 'selected' : '' }}>
                                    {{ $class->program_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="sc-input-group">
                    <label>Select Teacher</label>
                    <div>
                        <select name="teacher_id" required>
                            <option value="">Pilih Pengajar</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->usersID }}" {{ old('teacher_id') == $teacher->usersID ? 'selected' : '' }}>
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="sc-input-group">
                    <label>Material Topic</label>
                    <div>
                        <select name="title" id="materialSelect" data-old="{{ old('title') }}" required disabled>
                            <option value="">Pilih program terlebih dahulu</option>
                        </select>
                    </div>
                </div>

                <div class="sc-input-group">
                    <label>Date</label>
                    <div>
                        <input type="date" name="date" value="{{ old('date') }}" required>
                        <i class="fa-regular fa-calendar"></i>
                    </div>
                </div>

                <div class="sc-input-group">
                    <label>Start Time</label>
                    <div>
                        <input type="time" name="start_time" value="{{ old('start_time') }}" required>
                        <i class="fa-regular fa-clock"></i>
                    </div>
                </div>

                <div class="sc-input-group">
                    <label>End Time</label>
                    <div>
                        <input type="time" name="end_time" value="{{ old('end_time') }}" required>
                        <i class="fa-regular fa-clock"></i>
                    </div>
                </div>

                <button type="submit" class="sc-submit">
                    <i class="fa-solid fa-calendar-check"></i>
                    Publikasikan Jadwal
                </button>
            </form>
        </div>

        <div class="sc-panel sc-calendar-panel">
            <div class="sc-panel-heading">
                <div class="sc-heading-icon">
                    <i class="fa-regular fa-calendar"></i>
                </div>
                <h2>Ringkasan Jadwal</h2>
            </div>

            <div class="sc-calendar-box">
                <div class="sc-calendar-header">
                    <button type="button">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <strong>{{ $calendarMonth->translatedFormat('F Y') }}</strong>
                    <button type="button">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

                <div class="sc-calendar-days-name">
                    <span>Sen</span>
                    <span>Sel</span>
                    <span>Rab</span>
                    <span>Kam</span>
                    <span>Jum</span>
                    <span>Sab</span>
                    <span>Min</span>
                </div>

                <div class="sc-calendar-days">
                    @foreach($calendarDays as $day)
                        <div class="
                            sc-day
                            {{ !$day['is_current_month'] ? 'muted' : '' }}
                            {{ $day['is_today'] ? 'today' : '' }}
                            {{ $day['schedule_count'] > 0 ? 'has-schedule' : '' }}
                        ">
                            <span>{{ $day['day'] }}</span>
                            @if($day['schedule_count'] > 0)
                                <em>{{ $day['schedule_count'] }}</em>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="sc-summary-list">
                <div>
                    <span><i class="yellow"></i>Jadwal Hari Ini</span>
                    <strong>{{ number_format($jadwalHariIni ?? 0) }}</strong>
                </div>
                <div>
                    <span><i class="green"></i>Kelas Berlangsung</span>
                    <strong>{{ number_format($kelasBerlangsung ?? 0) }}</strong>
                </div>
                <div>
                    <span><i class="blue"></i>Jadwal Selesai</span>
                    <strong>{{ number_format($jadwalSelesaiHariIni ?? 0) }}</strong>
                </div>
                <div>
                    <span><i class="red"></i>Total Jadwal Bulan Ini</span>
                    <strong>{{ number_format($totalJadwalBulanIni ?? 0) }}</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="sc-stats">
        <div class="sc-stat-card">
            <div class="sc-stat-icon red">
                <i class="fa-regular fa-calendar-days"></i>
            </div>
            <div>
                <span>Total Jadwal</span>
                <strong>{{ number_format($totalJadwalBulanIni ?? 0) }}</strong>
                <small>Bulan ini</small>
            </div>
        </div>

        <div class="sc-stat-card">
            <div class="sc-stat-icon blue">
                <i class="fa-regular fa-calendar-check"></i>
            </div>
            <div>
                <span>Jadwal Hari Ini</span>
                <strong>{{ number_format($jadwalHariIni ?? 0) }}</strong>
                <small>{{ now()->translatedFormat('l, d M Y') }}</small>
            </div>
        </div>

        <div class="sc-stat-card">
            <div class="sc-stat-icon green">
                <i class="fa-regular fa-calendar"></i>
            </div>
            <div>
                <span>Kelas Berlangsung</span>
                <strong>{{ number_format($kelasBerlangsung ?? 0) }}</strong>
                <small>Sedang berjalan</small>
            </div>
        </div>

        <div class="sc-stat-card">
            <div class="sc-stat-icon purple">
                <i class="fa-regular fa-square-check"></i>
            </div>
            <div>
                <span>Jadwal Selesai</span>
                <strong>{{ number_format($jadwalSelesaiTotal ?? 0) }}</strong>
                <small>Total selesai</small>
            </div>
        </div>
    </section>

    <section class="sc-table-panel">
        <div class="sc-table-heading">
            <h2>Time & Date Program Subject & Teacher Action</h2>
        </div>

        <form method="GET" action="{{ route('admin.jadwal.index') }}" class="sc-toolbar">
            <div class="sc-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari jadwal, program, materi, atau pengajar..."
                >
            </div>

            <button type="submit" class="sc-filter-btn">
                <i class="fa-solid fa-filter"></i>
                Filter
            </button>

            <select name="status" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Berlangsung</option>
                <option value="finished" {{ request('status') === 'finished' ? 'selected' : '' }}>Selesai</option>
            </select>
        </form>

        <div class="sc-table-wrap">
            <table class="sc-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Program</th>
                        <th>Materi</th>
                        <th>Pengajar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($jadwal as $row)
                        @php
                            $startDateTime = \Carbon\Carbon::parse($row->date . ' ' . $row->start_time);
                            $endDateTime = \Carbon\Carbon::parse($row->date . ' ' . $row->end_time);
                            $now = now();

                            if ($now->between($startDateTime, $endDateTime)) {
                                $statusClass = 'ongoing';
                                $statusLabel = 'Berlangsung';
                            } elseif ($now->greaterThan($endDateTime)) {
                                $statusClass = 'finished';
                                $statusLabel = 'Selesai';
                            } else {
                                $statusClass = 'scheduled';
                                $statusLabel = 'Terjadwal';
                            }
                        @endphp

                        <tr>
                            <td>{{ \Carbon\Carbon::parse($row->date)->translatedFormat('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($row->end_time)->format('H:i') }}</td>
                            <td>{{ $row->class->program_name ?? '-' }}</td>
                            <td>{{ $row->title }}</td>
                            <td>{{ $row->teacher->name ?? '-' }}</td>
                            <td>
                                <span class="sc-status {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td>
                                <div class="sc-actions">
                                    <button type="button" class="view" title="Lihat detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <a href="{{ route('admin.jadwal.edit', $row->schedule_id) }}" class="edit" title="Edit jadwal">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form action="{{ route('admin.jadwal.destroy', $row->schedule_id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete" title="Hapus jadwal">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="sc-empty">
                                    <i class="fa-regular fa-calendar-xmark"></i>
                                    <strong>Belum ada jadwal.</strong>
                                    <span>Tambahkan jadwal pembelajaran melalui form di atas.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="sc-pagination">
            <p>
                Menampilkan {{ $jadwal->firstItem() ?? 0 }} - {{ $jadwal->lastItem() ?? 0 }}
                dari {{ number_format($jadwal->total() ?? 0) }} data
            </p>

            @if($jadwal->hasPages())
                <div class="sc-pages">
                    @if($jadwal->onFirstPage())
                        <span class="disabled"><i class="fa-solid fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $jadwal->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
                    @endif

                    @foreach(range(1, $jadwal->lastPage()) as $page)
                        @if($page == 1 || $page == $jadwal->lastPage() || abs($page - $jadwal->currentPage()) <= 1)
                            <a href="{{ $jadwal->url($page) }}" class="{{ $page == $jadwal->currentPage() ? 'active' : '' }}">
                                {{ $page }}
                            </a>
                        @elseif(abs($page - $jadwal->currentPage()) == 2)
                            <span>...</span>
                        @endif
                    @endforeach

                    @if($jadwal->hasMorePages())
                        <a href="{{ $jadwal->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
                    @else
                        <span class="disabled"><i class="fa-solid fa-chevron-right"></i></span>
                    @endif
                </div>
            @endif
        </div>
    </section>
</div>

<script>
    const classSelect = document.getElementById('classSelect');
    const materialSelect = document.getElementById('materialSelect');
    const oldMaterial = materialSelect ? materialSelect.dataset.old : '';
    const materiUrlTemplate = "{{ route('admin.jadwal.getMateri', ':id') }}";

    function loadMaterials(classId, selectedValue = '') {
        if (!materialSelect) return;

        if (!classId) {
            materialSelect.disabled = true;
            materialSelect.innerHTML = '<option value="">Pilih program terlebih dahulu</option>';
            return;
        }

        materialSelect.disabled = false;
        materialSelect.innerHTML = '<option value="">Loading...</option>';

        fetch(materiUrlTemplate.replace(':id', classId))
            .then(response => response.json())
            .then(data => {
                materialSelect.innerHTML = '<option value="">Pilih Materi</option>';

                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.title;
                    option.text = item.title;

                    if (selectedValue && selectedValue === item.title) {
                        option.selected = true;
                    }

                    materialSelect.appendChild(option);
                });
            })
            .catch(() => {
                materialSelect.innerHTML = '<option value="">Gagal memuat materi</option>';
            });
    }

    if (classSelect) {
        classSelect.addEventListener('change', function () {
            loadMaterials(this.value);
        });

        if (classSelect.value) {
            loadMaterials(classSelect.value, oldMaterial);
        }
    }
</script>

<style>
    .sc-page {
        width: 100%;
    }

    .sc-header {
        margin-bottom: 22px;
    }

    .sc-header span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .sc-header h1 {
        margin: 0 0 7px;
        color: #111827;
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .sc-header p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
    }

    .sc-alert {
        border-radius: 16px;
        padding: 15px 17px;
        margin-bottom: 18px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 800;
    }

    .sc-alert.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .sc-alert.error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .sc-alert ul {
        margin: 6px 0 0;
        padding-left: 18px;
    }

    .sc-top-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(360px, .95fr);
        gap: 22px;
        margin-bottom: 22px;
    }

    .sc-panel,
    .sc-stat-card,
    .sc-table-panel {
        background: #fff;
        border: 1px solid #edf0f4;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
    }

    .sc-panel {
        border-radius: 22px;
        padding: 22px;
    }

    .sc-panel-heading {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .sc-heading-icon {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: #ffe8ee;
        color: #d90429;
    }

    .sc-panel-heading h2 {
        margin: 0;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
    }

    .sc-form {
        display: grid;
        gap: 15px;
    }

    .sc-input-group {
        display: grid;
        grid-template-columns: 130px 1fr;
        align-items: center;
        gap: 16px;
    }

    .sc-input-group label {
        color: #374151;
        font-size: 11px;
        font-weight: 800;
    }

    .sc-input-group div {
        position: relative;
    }

    .sc-input-group select,
    .sc-input-group input {
        width: 100%;
        height: 43px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
        color: #111827;
        padding: 0 14px;
        outline: none;
        font-size: 12px;
        font-weight: 700;
        font-family: inherit;
    }

    .sc-input-group input[type="date"],
    .sc-input-group input[type="time"] {
        padding-right: 40px;
    }

    .sc-input-group div > i {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 12px;
        pointer-events: none;
    }

    .sc-input-group select:focus,
    .sc-input-group input:focus {
        background: #fff;
        border-color: #fecdd3;
        box-shadow: 0 0 0 4px rgba(217, 4, 41, .08);
    }

    .sc-submit {
        height: 48px;
        border: none;
        border-radius: 13px;
        background: linear-gradient(90deg, #d90429, #ef233c);
        color: #fff;
        font-size: 12px;
        font-weight: 900;
        cursor: pointer;
        font-family: inherit;
        box-shadow: 0 14px 28px rgba(217, 4, 41, .20);
        margin-top: 4px;
    }

    .sc-calendar-box {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .sc-calendar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .sc-calendar-header button {
        border: none;
        background: transparent;
        color: #64748b;
        cursor: pointer;
    }

    .sc-calendar-header strong {
        color: #111827;
        font-size: 13px;
        font-weight: 900;
    }

    .sc-calendar-days-name,
    .sc-calendar-days {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 7px;
    }

    .sc-calendar-days-name {
        margin-bottom: 8px;
    }

    .sc-calendar-days-name span {
        text-align: center;
        color: #6b7280;
        font-size: 10px;
        font-weight: 900;
    }

    .sc-day {
        height: 31px;
        border-radius: 999px;
        display: grid;
        place-items: center;
        position: relative;
        color: #111827;
        font-size: 11px;
        font-weight: 800;
    }

    .sc-day.muted {
        color: #cbd5e1;
    }

    .sc-day.today {
        background: #d90429;
        color: #fff;
    }

    .sc-day.has-schedule:not(.today) {
        background: #fff1f2;
        color: #d90429;
    }

    .sc-day em {
        position: absolute;
        width: 14px;
        height: 14px;
        right: -2px;
        top: -2px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #111827;
        color: #fff;
        font-size: 8px;
        font-style: normal;
    }

    .sc-summary-list {
        display: grid;
        gap: 12px;
    }

    .sc-summary-list div {
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
    }

    .sc-summary-list span {
        display: inline-flex;
        align-items: center;
        gap: 9px;
    }

    .sc-summary-list i {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        display: inline-block;
    }

    .sc-summary-list .yellow { background: #facc15; }
    .sc-summary-list .green { background: #22c55e; }
    .sc-summary-list .blue { background: #3b82f6; }
    .sc-summary-list .red { background: #f43f5e; }

    .sc-summary-list strong {
        color: #111827;
        font-weight: 900;
    }

    .sc-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    .sc-stat-card {
        border-radius: 18px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        min-height: 104px;
    }

    .sc-stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 15px;
        display: grid;
        place-items: center;
        font-size: 18px;
    }

    .sc-stat-icon.red {
        background: #ffe8ee;
        color: #d90429;
    }

    .sc-stat-icon.blue {
        background: #dbeafe;
        color: #2563eb;
    }

    .sc-stat-icon.green {
        background: #dcfce7;
        color: #16a34a;
    }

    .sc-stat-icon.purple {
        background: #ede9fe;
        color: #7c3aed;
    }

    .sc-stat-card span {
        display: block;
        color: #6b7280;
        font-size: 11px;
        font-weight: 900;
        margin-bottom: 4px;
    }

    .sc-stat-card strong {
        display: block;
        color: #111827;
        font-size: 28px;
        font-weight: 900;
        line-height: 1;
        margin-bottom: 4px;
    }

    .sc-stat-card small {
        color: #6b7280;
        font-size: 11px;
        font-weight: 600;
    }

    .sc-table-panel {
        border-radius: 22px;
        padding: 20px;
    }

    .sc-table-heading h2 {
        margin: 0 0 16px;
        color: #111827;
        font-size: 16px;
        font-weight: 900;
    }

    .sc-toolbar {
        display: grid;
        grid-template-columns: minmax(240px, 1fr) auto 180px;
        gap: 12px;
        margin-bottom: 16px;
    }

    .sc-search {
        position: relative;
    }

    .sc-search i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 13px;
    }

    .sc-search input,
    .sc-toolbar select {
        width: 100%;
        height: 44px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
        color: #111827;
        font-size: 12px;
        font-weight: 700;
        outline: none;
        font-family: inherit;
    }

    .sc-search input {
        padding: 0 15px 0 42px;
    }

    .sc-toolbar select {
        padding: 0 13px;
    }

    .sc-filter-btn {
        height: 44px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        border-radius: 12px;
        padding: 0 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 900;
        cursor: pointer;
        font-family: inherit;
    }

    .sc-table-wrap {
        overflow-x: auto;
    }

    .sc-table {
        width: 100%;
        border-collapse: collapse;
    }

    .sc-table th {
        padding: 14px 12px;
        border-bottom: 1px solid #edf0f4;
        color: #6b7280;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .06em;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .sc-table td {
        padding: 14px 12px;
        border-bottom: 1px solid #edf0f4;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        vertical-align: middle;
        white-space: nowrap;
    }

    .sc-table tbody tr:hover {
        background: #fff7f9;
    }

    .sc-status {
        display: inline-flex;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .sc-status.ongoing {
        background: #dcfce7;
        color: #16a34a;
    }

    .sc-status.scheduled {
        background: #dbeafe;
        color: #2563eb;
    }

    .sc-status.finished {
        background: #f3f4f6;
        color: #6b7280;
    }

    .sc-actions {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .sc-actions form {
        margin: 0;
    }

    .sc-actions button,
    .sc-actions a {
        width: 33px;
        height: 33px;
        border: none;
        border-radius: 10px;
        display: inline-grid;
        place-items: center;
        cursor: pointer;
        font-size: 12px;
    }

    .sc-actions .view {
        background: #dbeafe;
        color: #2563eb;
    }

    .sc-actions .edit {
        background: #f3f4f6;
        color: #374151;
    }

    .sc-actions .delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .sc-empty {
        padding: 35px;
        text-align: center;
        background: #f8fafc;
        border-radius: 14px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .sc-empty i {
        width: 54px;
        height: 54px;
        margin: 0 auto 14px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #ffe8ee;
        color: #d90429;
        font-size: 20px;
    }

    .sc-empty strong {
        display: block;
        color: #111827;
        font-size: 14px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    .sc-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding-top: 16px;
    }

    .sc-pagination p {
        margin: 0;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
    }

    .sc-pages {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .sc-pages a,
    .sc-pages span {
        min-width: 32px;
        height: 32px;
        display: grid;
        place-items: center;
        border: 1px solid #edf0f4;
        border-radius: 9px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 900;
    }

    .sc-pages a.active {
        background: #d90429;
        color: #fff;
        border-color: #d90429;
    }

    .sc-pages .disabled {
        opacity: .45;
    }

    @media (max-width: 1280px) {
        .sc-top-grid,
        .sc-stats {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 900px) {
        .sc-input-group {
            grid-template-columns: 1fr;
            gap: 7px;
        }

        .sc-toolbar {
            grid-template-columns: 1fr;
        }

        .sc-pagination {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection