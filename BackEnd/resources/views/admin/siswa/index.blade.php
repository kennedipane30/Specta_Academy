@extends('layouts.spekta')

@section('title', 'Student Management')
@section('subtitle', 'Sistem Manajemen Data Siswa Spekta Academy')

@section('content')
<div class="ss-page">

    {{-- HEADER --}}
    <section class="ss-header">
        <div>
            <span>Manajemen Akademik</span>
            <h1>Manajemen Siswa</h1>
            <p>Kelola data siswa Spekta Academy secara efisien berdasarkan data pendaftaran dari aplikasi.</p>
        </div>

        <a href="{{ route('admin.siswa.pendaftaran') }}" class="ss-primary-btn">
            <i class="fa-solid fa-circle-check"></i>
            Konfirmasi Kelas
            @if(($pendingEnrollment ?? 0) > 0)
                <em>{{ $pendingEnrollment }}</em>
            @endif
        </a>
    </section>

    {{-- STATISTIC CARDS --}}
    <section class="ss-stats">
        <div class="ss-stat-card">
            <div class="ss-stat-icon">
                <i class="fa-solid fa-user-group"></i>
            </div>
            <p>Total Siswa</p>
            <h2>{{ number_format($totalSiswa ?? 0) }}</h2>
            <div class="ss-stat-meta">
                <span class="success">Live</span>
                <small>data siswa</small>
            </div>
        </div>

        <div class="ss-stat-card">
            <div class="ss-stat-icon">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <p>Siswa Aktif</p>
            <h2>{{ number_format($siswaAktif ?? 0) }}</h2>
            <div class="ss-stat-meta">
                <span class="success">Aktif</span>
                <small>enrollment</small>
            </div>
        </div>

        <div class="ss-stat-card">
            <div class="ss-stat-icon">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <p>Siswa Baru Bulan Ini</p>
            <h2>{{ number_format($siswaBaruBulanIni ?? 0) }}</h2>
            <div class="ss-stat-meta">
                <span class="{{ ($growthSiswa ?? 0) >= 0 ? 'success' : 'danger' }}">
                    {{ ($growthSiswa ?? 0) >= 0 ? '+' : '' }}{{ $growthSiswa ?? 0 }}%
                </span>
                <small>vs bulan lalu</small>
            </div>
        </div>

        <div class="ss-stat-card">
            <div class="ss-stat-icon">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <p>Kelas Aktif</p>
            <h2>{{ number_format($kelasAktif ?? 0) }}</h2>
            <div class="ss-stat-meta">
                <span class="info">Aktif</span>
                <small>program siswa</small>
            </div>
        </div>
    </section>

    {{-- MAIN CONTENT --}}
    <section class="ss-main-grid">

        {{-- TABLE --}}
        <div class="ss-table-panel">
            <form method="GET" action="{{ route('admin.siswa.index') }}" class="ss-toolbar">
                <div class="ss-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama siswa, NIS, atau email..."
                    >
                </div>

                <select name="class_id" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($classes ?? [] as $class)
                        <option value="{{ $class->class_id }}" {{ request('class_id') == $class->class_id ? 'selected' : '' }}>
                            {{ $class->program_name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>

                <button type="button" onclick="window.print()" class="ss-export">
                    <i class="fa-solid fa-download"></i>
                    Export
                </button>
            </form>

            <div class="ss-table-wrap">
                <table class="ss-table">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th>Tanggal Daftar</th>
                            <th>Nilai Rata-Rata</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($siswas as $s)
                            @php
                                $student = $s->student;
                                $class = $student?->class;
                                $latestEnrollment = ($latestEnrollmentMap ?? collect())->get($s->usersID);
                                $status = $latestEnrollment?->status ?? 'registered';
                                $avgScore = ($avgScoreMap ?? collect())[$s->usersID] ?? null;

                                $statusClass = match($status) {
                                    'active' => 'active',
                                    'pending' => 'pending',
                                    'expired' => 'expired',
                                    default => 'registered',
                                };

                                $statusLabel = match($status) {
                                    'active' => 'Aktif',
                                    'pending' => 'Pending',
                                    'expired' => 'Expired',
                                    default => 'Registered',
                                };
                            @endphp

                            <tr>
                                <td>
                                    <div class="ss-student">
                                        <div class="ss-avatar">
                                            {{ strtoupper(substr($s->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $s->name }}</strong>
                                            <span>NIS: {{ $student?->national_id_number ?? '-' }}</span>
                                            <small>{{ $s->email }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>{{ $class ? 'Kelas ' . $class->class_id : '-' }}</td>
                                <td>{{ $class?->program_name ?? '-' }}</td>

                                <td>
                                    <span class="ss-status {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td>{{ $s->created_at?->translatedFormat('d M Y') }}</td>
                                <td>{{ $avgScore !== null ? $avgScore : '-' }}</td>

                                <td>
                                    <div class="ss-actions">
                                        <button type="button" class="view" title="Lihat detail">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>

                                        @if($latestEnrollment && $latestEnrollment->status === 'pending')
                                            <a href="{{ route('admin.siswa.form_aktivasi', $latestEnrollment->enrollment_id) }}" class="approve" title="Aktivasi">
                                                <i class="fa-solid fa-circle-check"></i>
                                            </a>
                                        @endif

                                        <button type="button" class="more" title="Menu lainnya">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="ss-empty">
                                        <i class="fa-solid fa-user-slash"></i>
                                        <strong>Belum ada data siswa.</strong>
                                        <span>Data siswa akan muncul setelah siswa mendaftar melalui aplikasi.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="ss-pagination">
                <p>
                    Menampilkan {{ $siswas->firstItem() ?? 0 }} - {{ $siswas->lastItem() ?? 0 }}
                    dari {{ number_format($siswas->total() ?? 0) }} siswa
                </p>

                @if(method_exists($siswas, 'hasPages') && $siswas->hasPages())
                    <div class="ss-pages">
                        @if($siswas->onFirstPage())
                            <span class="disabled"><i class="fa-solid fa-chevron-left"></i></span>
                        @else
                            <a href="{{ $siswas->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
                        @endif

                        @foreach(range(1, $siswas->lastPage()) as $page)
                            @if($page == 1 || $page == $siswas->lastPage() || abs($page - $siswas->currentPage()) <= 1)
                                <a href="{{ $siswas->url($page) }}" class="{{ $page == $siswas->currentPage() ? 'active' : '' }}">
                                    {{ $page }}
                                </a>
                            @elseif(abs($page - $siswas->currentPage()) == 2)
                                <span>...</span>
                            @endif
                        @endforeach

                        @if($siswas->hasMorePages())
                            <a href="{{ $siswas->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
                        @else
                            <span class="disabled"><i class="fa-solid fa-chevron-right"></i></span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <aside class="ss-side">
            <div class="ss-side-card">
                <h3>Distribusi Program</h3>

                <div class="ss-program-list">
                    @forelse($distribusiProgram ?? [] as $index => $program)
                        @php
                            $percentage = ($totalDistribusi ?? 0) > 0
                                ? round(($program->total / $totalDistribusi) * 100)
                                : 0;
                        @endphp

                        <div class="ss-program-row">
                            <div>
                                <i class="dot dot-{{ $index % 4 }}"></i>
                                <span>{{ $program->program_name }}</span>
                            </div>
                            <strong>{{ $percentage }}%</strong>
                        </div>
                    @empty
                        <div class="ss-side-empty">Belum ada distribusi program.</div>
                    @endforelse
                </div>
            </div>

            <div class="ss-side-card">
                <h3>Aktivitas Terbaru</h3>

                <div class="ss-activity-list">
                    @forelse($aktivitasTerbaru ?? [] as $activity)
                        <div class="ss-activity">
                            <div class="ss-activity-icon">
                                <i class="fa-solid {{ $activity['icon'] }}"></i>
                            </div>
                            <div>
                                <strong>{{ $activity['title'] }}</strong>
                                <span>{{ $activity['description'] }}</span>
                            </div>
                            <small>{{ $activity['time']->diffForHumans() }}</small>
                        </div>
                    @empty
                        <div class="ss-side-empty">Belum ada aktivitas terbaru.</div>
                    @endforelse
                </div>
            </div>
        </aside>
    </section>

    {{-- QUICK ACTIONS --}}
    <section class="ss-quick-actions">
        <a href="{{ route('admin.siswa.pendaftaran') }}" class="ss-quick-card">
            <div class="ss-quick-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="ss-quick-text">
                <strong>Konfirmasi Kelas</strong>
                <span>Verifikasi pendaftaran</span>
            </div>
            <i class="fa-solid fa-chevron-right ss-quick-arrow"></i>
        </a>

        <a href="{{ route('admin.jadwal.index') }}" class="ss-quick-card">
            <div class="ss-quick-icon">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="ss-quick-text">
                <strong>Jadwal Kelas</strong>
                <span>Kelola jadwal siswa</span>
            </div>
            <i class="fa-solid fa-chevron-right ss-quick-arrow"></i>
        </a>

        <a href="{{ route('admin.announcement.index') }}" class="ss-quick-card">
            <div class="ss-quick-icon">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <div class="ss-quick-text">
                <strong>Kirim Pengumuman</strong>
                <span>Informasi ke siswa</span>
            </div>
            <i class="fa-solid fa-chevron-right ss-quick-arrow"></i>
        </a>

        <button type="button" onclick="window.print()" class="ss-quick-card">
            <div class="ss-quick-icon">
                <i class="fa-solid fa-print"></i>
            </div>
            <div class="ss-quick-text">
                <strong>Cetak Laporan</strong>
                <span>Data siswa aktif</span>
            </div>
            <i class="fa-solid fa-chevron-right ss-quick-arrow"></i>
        </button>
    </section>
</div>

<style>
    .ss-page {
        width: 100%;
    }

    .ss-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 22px;
        margin-bottom: 22px;
    }

    .ss-header span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .ss-header h1 {
        margin: 0 0 7px;
        color: #111827;
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .ss-header p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
    }

    .ss-primary-btn {
        min-height: 46px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #d90429;
        color: #fff;
        border-radius: 12px;
        padding: 0 18px;
        font-size: 12px;
        font-weight: 900;
        box-shadow: 0 14px 28px rgba(217, 4, 41, .22);
        white-space: nowrap;
    }

    .ss-primary-btn em {
        min-width: 23px;
        height: 23px;
        display: grid;
        place-items: center;
        background: #fff;
        color: #d90429;
        border-radius: 999px;
        font-size: 10px;
        font-style: normal;
    }

    .ss-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    .ss-stat-card {
        background: #fff;
        border: 1px solid #edf0f4;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
        border-radius: 20px;
        padding: 24px;
        min-height: 160px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .ss-stat-icon {
        width: 44px;
        height: 44px;
        display: grid;
        place-items: center;
        background: #ffe8ee;
        color: #d90429;
        border-radius: 15px;
        margin-bottom: 16px;
        font-size: 17px;
    }

    .ss-stat-card p {
        margin: 0 0 8px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .ss-stat-card h2 {
        margin: 0 0 14px;
        color: #0f172a;
        font-size: 31px;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .ss-stat-meta {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ss-stat-meta span {
        height: 23px;
        display: inline-flex;
        align-items: center;
        border-radius: 8px;
        padding: 0 9px;
        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .ss-stat-meta .success {
        background: #dcfce7;
        color: #16a34a;
    }

    .ss-stat-meta .danger {
        background: #fee2e2;
        color: #dc2626;
    }

    .ss-stat-meta .info {
        background: #dbeafe;
        color: #2563eb;
    }

    .ss-stat-meta small {
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .ss-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 310px;
        gap: 22px;
        align-items: start;
        margin-bottom: 22px;
    }

    .ss-table-panel,
    .ss-side-card,
    .ss-quick-actions {
        background: #fff;
        border: 1px solid #edf0f4;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
    }

    .ss-table-panel {
        border-radius: 22px;
        padding: 18px;
        overflow: hidden;
    }

    .ss-toolbar {
        display: grid;
        grid-template-columns: minmax(230px, 1fr) 170px 170px auto;
        gap: 12px;
        margin-bottom: 17px;
    }

    .ss-search {
        position: relative;
    }

    .ss-search i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 13px;
    }

    .ss-search input,
    .ss-toolbar select {
        width: 100%;
        height: 44px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #f8fafc;
        color: #111827;
        font-size: 12px;
        font-weight: 700;
        outline: none;
    }

    .ss-search input {
        padding: 0 15px 0 42px;
    }

    .ss-toolbar select {
        padding: 0 13px;
    }

    .ss-search input:focus,
    .ss-toolbar select:focus {
        background: #fff;
        border-color: #fecdd3;
        box-shadow: 0 0 0 4px rgba(217, 4, 41, .08);
    }

    .ss-export {
        height: 44px;
        border: 1px solid #d90429;
        background: #fff;
        color: #d90429;
        border-radius: 12px;
        padding: 0 16px;
        font-size: 12px;
        font-weight: 900;
        cursor: pointer;
        font-family: inherit;
        white-space: nowrap;
    }

    .ss-table-wrap {
        overflow-x: auto;
    }

    .ss-table {
        width: 100%;
        border-collapse: collapse;
    }

    .ss-table th {
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

    .ss-table td {
        padding: 14px 12px;
        border-bottom: 1px solid #edf0f4;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        vertical-align: middle;
        white-space: nowrap;
    }

    .ss-table tbody tr:hover {
        background: #fff7f9;
    }

    .ss-student {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ss-avatar {
        width: 39px;
        height: 39px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #ffe8ee;
        color: #d90429;
        font-weight: 900;
    }

    .ss-student strong {
        display: block;
        color: #111827;
        font-size: 13px;
        font-weight: 900;
    }

    .ss-student span,
    .ss-student small {
        display: block;
        color: #6b7280;
        font-size: 10px;
        font-weight: 700;
        margin-top: 2px;
    }

    .ss-status {
        display: inline-flex;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .ss-status.active {
        background: #dcfce7;
        color: #16a34a;
    }

    .ss-status.pending {
        background: #ffedd5;
        color: #ea580c;
    }

    .ss-status.expired {
        background: #fee2e2;
        color: #dc2626;
    }

    .ss-status.registered {
        background: #dbeafe;
        color: #2563eb;
    }

    .ss-actions {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .ss-actions button,
    .ss-actions a {
        width: 33px;
        height: 33px;
        border: none;
        border-radius: 10px;
        display: inline-grid;
        place-items: center;
        cursor: pointer;
        font-size: 12px;
    }

    .ss-actions .view {
        background: #dbeafe;
        color: #2563eb;
    }

    .ss-actions .approve {
        background: #dcfce7;
        color: #16a34a;
    }

    .ss-actions .more {
        background: #f3f4f6;
        color: #6b7280;
    }

    .ss-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding-top: 16px;
    }

    .ss-pagination p {
        margin: 0;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
    }

    .ss-pages {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .ss-pages a,
    .ss-pages span {
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

    .ss-pages a.active {
        background: #d90429;
        color: #fff;
        border-color: #d90429;
    }

    .ss-pages .disabled {
        opacity: .45;
    }

    .ss-side {
        display: grid;
        gap: 22px;
    }

    .ss-side-card {
        border-radius: 22px;
        padding: 20px;
    }

    .ss-side-card h3 {
        margin: 0 0 18px;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
    }

    .ss-program-list,
    .ss-activity-list {
        display: grid;
        gap: 14px;
    }

    .ss-program-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        color: #4b5563;
        font-size: 12px;
        font-weight: 800;
    }

    .ss-program-row div {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .ss-program-row strong {
        color: #111827;
        font-weight: 900;
    }

    .dot {
        width: 9px;
        height: 9px;
        border-radius: 999px;
        display: inline-block;
    }

    .dot-0 { background: #d90429; }
    .dot-1 { background: #111827; }
    .dot-2 { background: #ff8fab; }
    .dot-3 { background: #d1d5db; }

    .ss-activity {
        display: grid;
        grid-template-columns: 36px minmax(0, 1fr) auto;
        gap: 11px;
        align-items: center;
    }

    .ss-activity-icon {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        border-radius: 11px;
        background: #ffe8ee;
        color: #d90429;
    }

    .ss-activity strong {
        display: block;
        color: #111827;
        font-size: 12px;
        font-weight: 900;
    }

    .ss-activity span {
        display: block;
        color: #6b7280;
        font-size: 10px;
        font-weight: 700;
        margin-top: 3px;
    }

    .ss-activity small {
        color: #6b7280;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .ss-empty,
    .ss-side-empty {
        padding: 25px;
        text-align: center;
        background: #f8fafc;
        border-radius: 14px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .ss-empty i {
        display: grid;
        place-items: center;
        width: 52px;
        height: 52px;
        margin: 0 auto 13px;
        border-radius: 999px;
        background: #ffe8ee;
        color: #d90429;
        font-size: 19px;
    }

    .ss-empty strong {
        display: block;
        color: #111827;
        font-size: 14px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    .ss-empty span {
        display: block;
    }

    .ss-quick-actions {
        border-radius: 22px;
        padding: 18px;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .ss-quick-card {
        min-height: 96px;
        display: grid;
        grid-template-columns: 50px minmax(0, 1fr) 18px;
        align-items: center;
        gap: 14px;
        border: 1px solid #edf0f4;
        border-radius: 16px;
        background: #fff;
        padding: 17px 18px;
        text-align: left;
        cursor: pointer;
        color: inherit;
        font-family: inherit;
    }

    button.ss-quick-card {
        width: 100%;
    }

    .ss-quick-icon {
        width: 50px;
        height: 50px;
        display: grid;
        place-items: center;
        border-radius: 15px;
        background: #ffe8ee;
        color: #d90429;
        font-size: 18px;
    }

    .ss-quick-text {
        min-width: 0;
    }

    .ss-quick-text strong {
        display: block;
        color: #111827;
        font-size: 13px;
        line-height: 1.25;
        font-weight: 900;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ss-quick-text span {
        display: block;
        color: #6b7280;
        font-size: 11px;
        line-height: 1.3;
        font-weight: 700;
        margin-top: 4px;
    }

    .ss-quick-arrow {
        justify-self: end;
        color: #64748b;
        font-size: 11px;
    }

    @media (max-width: 1400px) {
        .ss-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ss-main-grid {
            grid-template-columns: 1fr;
        }

        .ss-side {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ss-quick-actions {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .ss-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .ss-stats,
        .ss-side,
        .ss-quick-actions {
            grid-template-columns: 1fr;
        }

        .ss-toolbar {
            grid-template-columns: 1fr;
        }

        .ss-pagination {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection