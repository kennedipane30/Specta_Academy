@extends('layouts.spekta')

@section('title', 'Teacher Management')
@section('subtitle', 'Sistem Manajemen Data Pengajar Spekta Academy')

@section('content')
<div class="tp-page">

    <section class="tp-header">
        <div>
            <span>Manajemen Akademik</span>
            <h1>Manajemen Pengajar</h1>
            <p>Kelola data pengajar Spekta Academy secara efisien.</p>
        </div>

        <a href="{{ route('admin.manajemen-pengajar.create') }}" class="tp-primary-btn">
            <i class="fa-solid fa-plus"></i>
            Tambah Pengajar
        </a>
    </section>

    @if(session('success'))
        <div class="tp-alert success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <section class="tp-stats">
        <div class="tp-stat-card">
            <div class="tp-stat-icon">
                <i class="fa-solid fa-user-group"></i>
            </div>
            <p>Total Pengajar</p>
            <h2>{{ number_format($totalPengajar ?? 0) }}</h2>
            <div class="tp-stat-meta">
                <span class="success">Live</span>
                <small>data pengajar</small>
            </div>
        </div>

        <div class="tp-stat-card">
            <div class="tp-stat-icon">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <p>Pengajar Aktif</p>
            <h2>{{ number_format($pengajarAktif ?? 0) }}</h2>
            <div class="tp-stat-meta">
                <span class="success">Aktif</span>
                <small>akun verified</small>
            </div>
        </div>

        <div class="tp-stat-card">
            <div class="tp-stat-icon">
                <i class="fa-solid fa-calendar-plus"></i>
            </div>
            <p>Pengajar Baru Bulan Ini</p>
            <h2>{{ number_format($pengajarBaruBulanIni ?? 0) }}</h2>
            <div class="tp-stat-meta">
                <span class="{{ ($growthPengajar ?? 0) >= 0 ? 'success' : 'danger' }}">
                    {{ ($growthPengajar ?? 0) >= 0 ? '+' : '' }}{{ $growthPengajar ?? 0 }}%
                </span>
                <small>vs bulan lalu</small>
            </div>
        </div>

        <div class="tp-stat-card">
            <div class="tp-stat-icon">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <p>Kelas Diajar</p>
            <h2>{{ number_format($kelasDiajar ?? 0) }}</h2>
            <div class="tp-stat-meta">
                <span class="info">Aktif</span>
                <small>penugasan kelas</small>
            </div>
        </div>
    </section>

    <section class="tp-main-grid">
        <div class="tp-table-panel">
            <form method="GET" action="{{ route('admin.manajemen-pengajar.index') }}" class="tp-toolbar">
                <div class="tp-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama pengajar, email, atau bidang ajar..."
                    >
                </div>

                <select name="subject_name" onchange="this.form.submit()">
                    <option value="">Semua Bidang</option>
                    @foreach($subjects ?? [] as $subject)
                        <option value="{{ $subject }}" {{ request('subject_name') === $subject ? 'selected' : '' }}>
                            {{ $subject }}
                        </option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>

                <button type="button" onclick="window.print()" class="tp-export">
                    <i class="fa-solid fa-download"></i>
                    Export
                </button>
            </form>

            <div class="tp-table-wrap">
                <table class="tp-table">
                    <thead>
                        <tr>
                            <th>Nama Pengajar</th>
                            <th>Bidang Ajar</th>
                            <th>Status</th>
                            <th>Kelas Aktif</th>
                            <th>Rating</th>
                            <th>Tanggal Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($teachers as $teacher)
                            @php
                                $teacherAssignments = ($assignmentMap ?? collect())->get($teacher->usersID, collect());
                                $subjectList = $teacherAssignments->pluck('subject_name')->filter()->unique()->values();
                                $classCount = $teacherAssignments->pluck('class_id')->filter()->unique()->count();

                                if (($scheduleCountMap ?? collect())->has($teacher->usersID)) {
                                    $classCount = max($classCount, (int) $scheduleCountMap[$teacher->usersID]);
                                }

                                $mainSubject = $subjectList->take(2)->implode(', ');
                                $moreSubjectCount = max($subjectList->count() - 2, 0);
                            @endphp

                            <tr>
                                <td>
                                    <div class="tp-teacher">
                                        <div class="tp-avatar">
                                            {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $teacher->name }}</strong>
                                            <span>NIP: {{ str_pad($teacher->usersID, 6, '0', STR_PAD_LEFT) }}</span>
                                            <small>{{ $teacher->email }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    @if($subjectList->count() > 0)
                                        <span class="tp-subject">
                                            {{ $mainSubject }}
                                            @if($moreSubjectCount > 0)
                                                +{{ $moreSubjectCount }}
                                            @endif
                                        </span>
                                    @else
                                        <span class="tp-muted">Belum ditugaskan</span>
                                    @endif
                                </td>

                                <td>
                                    @if($teacher->is_verified)
                                        <span class="tp-status active">Aktif</span>
                                    @else
                                        <span class="tp-status inactive">Nonaktif</span>
                                    @endif
                                </td>

                                <td>{{ $classCount }}</td>

                                <td>
                                    <span class="tp-rating">
                                        -
                                        <i class="fa-solid fa-star"></i>
                                    </span>
                                </td>

                                <td>{{ $teacher->created_at?->translatedFormat('d M Y') }}</td>

                                <td>
                                    <div class="tp-actions">
                                        <a href="{{ route('admin.assignments.index') }}" class="assignment" title="Penugasan materi">
                                            <i class="fa-solid fa-book-open"></i>
                                        </a>

                                        <a href="{{ route('admin.manajemen-pengajar.edit', $teacher->usersID) }}" class="edit" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <form action="{{ route('admin.manajemen-pengajar.destroy', $teacher->usersID) }}" method="POST" onsubmit="return confirm('Hapus akun pengajar ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="tp-empty">
                                        <i class="fa-solid fa-user-slash"></i>
                                        <strong>Belum ada data pengajar.</strong>
                                        <span>Tambahkan akun pengajar melalui tombol Tambah Pengajar.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="tp-pagination">
                <p>
                    Menampilkan {{ $teachers->firstItem() ?? 0 }} - {{ $teachers->lastItem() ?? 0 }}
                    dari {{ number_format($teachers->total() ?? 0) }} pengajar
                </p>

                @if(method_exists($teachers, 'hasPages') && $teachers->hasPages())
                    <div class="tp-pages">
                        @if($teachers->onFirstPage())
                            <span class="disabled"><i class="fa-solid fa-chevron-left"></i></span>
                        @else
                            <a href="{{ $teachers->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
                        @endif

                        @foreach(range(1, $teachers->lastPage()) as $page)
                            @if($page == 1 || $page == $teachers->lastPage() || abs($page - $teachers->currentPage()) <= 1)
                                <a href="{{ $teachers->url($page) }}" class="{{ $page == $teachers->currentPage() ? 'active' : '' }}">
                                    {{ $page }}
                                </a>
                            @elseif(abs($page - $teachers->currentPage()) == 2)
                                <span>...</span>
                            @endif
                        @endforeach

                        @if($teachers->hasMorePages())
                            <a href="{{ $teachers->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
                        @else
                            <span class="disabled"><i class="fa-solid fa-chevron-right"></i></span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <aside class="tp-side">
            <div class="tp-side-card">
                <h3>Distribusi Bidang Ajar</h3>

                <div class="tp-program-list">
                    @forelse($distribusiBidang ?? [] as $index => $bidang)
                        @php
                            $percentage = ($totalDistribusiBidang ?? 0) > 0
                                ? round(($bidang->total / $totalDistribusiBidang) * 100)
                                : 0;
                        @endphp

                        <div class="tp-program-row">
                            <div>
                                <i class="dot dot-{{ $index % 4 }}"></i>
                                <span>{{ $bidang->subject_name }}</span>
                            </div>
                            <strong>{{ $percentage }}%</strong>
                        </div>
                    @empty
                        <div class="tp-side-empty">Belum ada distribusi bidang ajar.</div>
                    @endforelse
                </div>
            </div>

            <div class="tp-side-card">
                <h3>Aktivitas Terbaru</h3>

                <div class="tp-activity-list">
                    @forelse($aktivitasTerbaru ?? [] as $activity)
                        <div class="tp-activity">
                            <div class="tp-activity-icon">
                                <i class="fa-solid {{ $activity['icon'] }}"></i>
                            </div>
                            <div>
                                <strong>{{ $activity['title'] }}</strong>
                                <span>{{ $activity['description'] }}</span>
                            </div>
                            <small>{{ $activity['time']->diffForHumans() }}</small>
                        </div>
                    @empty
                        <div class="tp-side-empty">Belum ada aktivitas terbaru.</div>
                    @endforelse
                </div>
            </div>
        </aside>
    </section>

    <section class="tp-quick-actions">
        <a href="{{ route('admin.manajemen-pengajar.create') }}" class="tp-quick-card">
            <div class="tp-quick-icon">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div class="tp-quick-text">
                <strong>Tambah Pengajar</strong>
                <span>Tambah pengajar baru</span>
            </div>
            <i class="fa-solid fa-chevron-right tp-quick-arrow"></i>
        </a>

        <a href="{{ route('admin.assignments.index') }}" class="tp-quick-card">
            <div class="tp-quick-icon">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <div class="tp-quick-text">
                <strong>Penugasan Materi</strong>
                <span>Atur bidang ajar</span>
            </div>
            <i class="fa-solid fa-chevron-right tp-quick-arrow"></i>
        </a>

        <a href="{{ route('admin.jadwal.index') }}" class="tp-quick-card">
            <div class="tp-quick-icon">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="tp-quick-text">
                <strong>Jadwal Mengajar</strong>
                <span>Kelola jadwal pengajar</span>
            </div>
            <i class="fa-solid fa-chevron-right tp-quick-arrow"></i>
        </a>

        <a href="{{ route('admin.announcement.index') }}" class="tp-quick-card">
            <div class="tp-quick-icon">
                <i class="fa-solid fa-bullhorn"></i>
            </div>
            <div class="tp-quick-text">
                <strong>Kirim Pengumuman</strong>
                <span>Informasi ke pengajar</span>
            </div>
            <i class="fa-solid fa-chevron-right tp-quick-arrow"></i>
        </a>

        <button type="button" onclick="window.print()" class="tp-quick-card">
            <div class="tp-quick-icon">
                <i class="fa-solid fa-print"></i>
            </div>
            <div class="tp-quick-text">
                <strong>Cetak Laporan</strong>
                <span>Data laporan pengajar</span>
            </div>
            <i class="fa-solid fa-chevron-right tp-quick-arrow"></i>
        </button>
    </section>
</div>

<style>
    .tp-page { width: 100%; }

    .tp-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 22px;
        margin-bottom: 22px;
    }

    .tp-header span {
        display: block;
        color: #d90429;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .18em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .tp-header h1 {
        margin: 0 0 7px;
        color: #111827;
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.02em;
    }

    .tp-header p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
    }

    .tp-primary-btn {
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

    .tp-alert {
        border-radius: 16px;
        padding: 15px 17px;
        margin-bottom: 18px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 13px;
        font-weight: 800;
    }

    .tp-alert.success {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    .tp-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 22px;
    }

    .tp-stat-card,
    .tp-table-panel,
    .tp-side-card,
    .tp-quick-actions {
        background: #fff;
        border: 1px solid #edf0f4;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .05);
    }

    .tp-stat-card {
        border-radius: 20px;
        padding: 24px;
        min-height: 160px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .tp-stat-icon {
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

    .tp-stat-card p {
        margin: 0 0 8px;
        color: #6b7280;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .tp-stat-card h2 {
        margin: 0 0 14px;
        color: #0f172a;
        font-size: 31px;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    .tp-stat-meta {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tp-stat-meta span {
        height: 23px;
        display: inline-flex;
        align-items: center;
        border-radius: 8px;
        padding: 0 9px;
        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .tp-stat-meta .success {
        background: #dcfce7;
        color: #16a34a;
    }

    .tp-stat-meta .danger {
        background: #fee2e2;
        color: #dc2626;
    }

    .tp-stat-meta .info {
        background: #dbeafe;
        color: #2563eb;
    }

    .tp-stat-meta small {
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .tp-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 310px;
        gap: 22px;
        align-items: start;
        margin-bottom: 22px;
    }

    .tp-table-panel {
        border-radius: 22px;
        padding: 18px;
        overflow: hidden;
    }

    .tp-toolbar {
        display: grid;
        grid-template-columns: minmax(230px, 1fr) 170px 170px auto;
        gap: 12px;
        margin-bottom: 17px;
    }

    .tp-search { position: relative; }

    .tp-search i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 13px;
    }

    .tp-search input,
    .tp-toolbar select {
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

    .tp-search input { padding: 0 15px 0 42px; }
    .tp-toolbar select { padding: 0 13px; }

    .tp-export {
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

    .tp-table-wrap { overflow-x: auto; }

    .tp-table {
        width: 100%;
        border-collapse: collapse;
    }

    .tp-table th {
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

    .tp-table td {
        padding: 14px 12px;
        border-bottom: 1px solid #edf0f4;
        color: #374151;
        font-size: 12px;
        font-weight: 700;
        vertical-align: middle;
        white-space: nowrap;
    }

    .tp-table tbody tr:hover { background: #fff7f9; }

    .tp-teacher {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .tp-avatar {
        width: 39px;
        height: 39px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        background: #ffe8ee;
        color: #d90429;
        font-weight: 900;
    }

    .tp-teacher strong {
        display: block;
        color: #111827;
        font-size: 13px;
        font-weight: 900;
    }

    .tp-teacher span,
    .tp-teacher small {
        display: block;
        color: #6b7280;
        font-size: 10px;
        font-weight: 700;
        margin-top: 2px;
    }

    .tp-subject {
        display: inline-flex;
        border-radius: 999px;
        background: #fff1f2;
        color: #d90429;
        padding: 7px 11px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .tp-muted {
        color: #9ca3af;
        font-size: 11px;
        font-weight: 800;
    }

    .tp-status {
        display: inline-flex;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .tp-status.active {
        background: #dcfce7;
        color: #16a34a;
    }

    .tp-status.inactive {
        background: #ffedd5;
        color: #ea580c;
    }

    .tp-rating {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #6b7280;
        font-weight: 900;
    }

    .tp-rating i {
        color: #f59e0b;
        font-size: 11px;
    }

    .tp-actions {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .tp-actions form { margin: 0; }

    .tp-actions button,
    .tp-actions a {
        width: 33px;
        height: 33px;
        border: none;
        border-radius: 10px;
        display: inline-grid;
        place-items: center;
        cursor: pointer;
        font-size: 12px;
    }

    .tp-actions .assignment {
        background: #fff1f2;
        color: #d90429;
    }

    .tp-actions .edit {
        background: #ffedd5;
        color: #ea580c;
    }

    .tp-actions .delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .tp-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding-top: 16px;
    }

    .tp-pagination p {
        margin: 0;
        color: #6b7280;
        font-size: 11px;
        font-weight: 700;
    }

    .tp-pages {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .tp-pages a,
    .tp-pages span {
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

    .tp-pages a.active {
        background: #d90429;
        color: #fff;
        border-color: #d90429;
    }

    .tp-pages .disabled { opacity: .45; }

    .tp-side {
        display: grid;
        gap: 22px;
    }

    .tp-side-card {
        border-radius: 22px;
        padding: 20px;
    }

    .tp-side-card h3 {
        margin: 0 0 18px;
        color: #111827;
        font-size: 15px;
        font-weight: 900;
    }

    .tp-program-list,
    .tp-activity-list {
        display: grid;
        gap: 14px;
    }

    .tp-program-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        color: #4b5563;
        font-size: 12px;
        font-weight: 800;
    }

    .tp-program-row div {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .tp-program-row strong {
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

    .tp-activity {
        display: grid;
        grid-template-columns: 36px minmax(0, 1fr) auto;
        gap: 11px;
        align-items: center;
    }

    .tp-activity-icon {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        border-radius: 11px;
        background: #ffe8ee;
        color: #d90429;
    }

    .tp-activity strong {
        display: block;
        color: #111827;
        font-size: 12px;
        font-weight: 900;
    }

    .tp-activity span {
        display: block;
        color: #6b7280;
        font-size: 10px;
        font-weight: 700;
        margin-top: 3px;
    }

    .tp-activity small {
        color: #6b7280;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .tp-empty,
    .tp-side-empty {
        padding: 25px;
        text-align: center;
        background: #f8fafc;
        border-radius: 14px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .tp-empty i {
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

    .tp-empty strong {
        display: block;
        color: #111827;
        font-size: 14px;
        font-weight: 900;
        margin-bottom: 5px;
    }

    .tp-quick-actions {
        border-radius: 22px;
        padding: 18px;
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 16px;
    }

    .tp-quick-card {
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

    button.tp-quick-card { width: 100%; }

    .tp-quick-icon {
        width: 50px;
        height: 50px;
        display: grid;
        place-items: center;
        border-radius: 15px;
        background: #ffe8ee;
        color: #d90429;
        font-size: 18px;
    }

    .tp-quick-text { min-width: 0; }

    .tp-quick-text strong {
        display: block;
        color: #111827;
        font-size: 13px;
        line-height: 1.25;
        font-weight: 900;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .tp-quick-text span {
        display: block;
        color: #6b7280;
        font-size: 11px;
        line-height: 1.3;
        font-weight: 700;
        margin-top: 4px;
    }

    .tp-quick-arrow {
        justify-self: end;
        color: #64748b;
        font-size: 11px;
    }

    @media (max-width: 1500px) {
        .tp-quick-actions { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 1400px) {
        .tp-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .tp-main-grid { grid-template-columns: 1fr; }
        .tp-side { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 900px) {
        .tp-header { flex-direction: column; align-items: flex-start; }
        .tp-stats,
        .tp-side,
        .tp-quick-actions { grid-template-columns: 1fr; }
        .tp-toolbar { grid-template-columns: 1fr; }
        .tp-pagination { flex-direction: column; align-items: flex-start; }
    }
</style>
@endsection